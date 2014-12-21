<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 12/15/14
 * Time: 8:49 PM
 */
namespace Giftcards\FixedWidth;


class LazyFile implements \IteratorAggregate, \ArrayAccess, FileInterface
{
    protected $fileObject;
    protected $lineSeparatorLength;
    protected $realLineWidth;
    protected $fileData;
    protected $lineSeparator;
    protected $width;

    public function __construct(
        $width,
        \SplFileObject $fileObject,
        $lineSeparator = "\r\n"
    ) {
        $this->width = $width;
        $this->fileObject = $fileObject;
        $this->lineSeparator = $lineSeparator;
        $this->lineSeparatorLength = strlen($this->lineSeparator);
        $this->realLineWidth = $this->width + $this->lineSeparatorLength;

        $lineRemainder = $this->getSize() % $this->realLineWidth;
        
        if ($lineRemainder != 0 && $lineRemainder != $this->width) {
            
            throw new \InvalidArgumentException(sprintf(
                'It looks like the file supplied does not have all lines of the width %d. 
                make sure you have the correct line ending being passed since it is used in the comparison to figure this out.',
                $this->width
            ));
        }
        
        $this->hasTrailingLineSeparator = $lineRemainder == 0;
    }

    public function __toString()
    {
        return file_get_contents($this->fileObject->getRealPath());
    }

    public function getLines()
    {
        return array_map(
            function($index){return $this->getLine($index);}, 
            //array_filter is to take out the empty trailing line left behind by the trailing line separator
            range(0, $this->count() - 1)
        );
    }

    public function getLine($index)
    {
        if ($index >= $this->count()) {

            throw new \OutOfBoundsException('The index is outside of the available indexes of lines.');
        }
        
        $linePosition = $this->getLinePosition($index);

        $this->fileObject->fseek(
            $linePosition,
            SEEK_SET
        );
        
        if ($this->fileObject->ftell() == 0) {
            
            $preLineEnding = $this->lineSeparator;
        } else {

            $this->fileObject->fseek(-$this->lineSeparatorLength, SEEK_CUR);
            $preLineEnding = $this->readFromFile($this->lineSeparatorLength);
        }

        $this->fileObject->fseek($linePosition + $this->width);

        try {
            
            $postLineEnding = $this->readFromFile($this->lineSeparatorLength);
        } catch (\OverflowException $e) {

            $postLineEnding = $this->lineSeparator;
        }

        if (($preLineEnding != $this->lineSeparator) || $postLineEnding != $this->lineSeparator) {
            
            throw new \RuntimeException(sprintf('the line is not bound by line endings.'));
        }
        
        return new LazyLine($this->fileObject, $linePosition, $this->width);
    }

    public function count()
    {
        return ceil($this->getSize()/$this->realLineWidth);
    }

    public function getName()
    {
        return $this->fileObject->getFilename();
    }

    public function getIterator()
    {
        return new FileIterator($this);
    }

    public function addLine($line)
    {
        $line = $this->validateLine($line);
        $this->fileObject->fseek(0, SEEK_END);
        if (!$this->hasTrailingLineSeparator) {
            
            $this->fileObject->fwrite($this->lineSeparator);
        }
        
        $this->fileObject->fwrite((string)$line);
        
        if ($this->hasTrailingLineSeparator) {

            $this->fileObject->fwrite($this->lineSeparator);
        }
        
        return $this;
    }

    public function setLine($index, $line)
    {
        $line = $this->validateLine($line);
        $this->fileObject->fseek($this->getLinePosition($index), SEEK_SET);
        $this->fileObject->fwrite((string)$line);
        $this->fileObject->fwrite($this->lineSeparator);
        return $this;
    }

    public function removeLine($index)
    {
        throw new \BadMethodCallException('This method is not yet implemented.');
    }

    protected function getLinePosition($lineIndex)
    {
        return $this->realLineWidth * $lineIndex;
    }

    protected function readFromFile($length)
    {
        $data = '';
        
        for ($i = 0; $i < $length; $i++) {

            $char = $this->fileObject->fgetc();
            
            if ($this->fileObject->eof()) {

                throw new \OverflowException('overflowed the file');
            }

            $data .= $char;
        }
        
        return $data;
    }

    public function offsetExists($offset)
    {
        $fileData = $this->fileObject->fstat();
        return $this->getLinePosition($offset) > $fileData['size'];
    }

    public function offsetGet($offset)
    {
        return $this->getLine($offset);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {

            $this->addLine($value);
            return;
        }

        $this->setLine($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->removeLine($offset);
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return string
     */
    public function getLineSeparator()
    {
        return $this->lineSeparator;
    }

    public function newLine()
    {
        $this->fileObject->fseek(0, SEEK_END);
        $this->addLine(str_repeat(' ', $this->width));
        return $this->getLine($this->count() - 1);
    }

    protected function validateLine($line)
    {
        if (!$line instanceof LineInterface) {

            $line = new Line((string)$line);
        }

        if ($line->getLength() != $this->width) {

            throw new \InvalidArgumentException(sprintf(
                'All lines in a batch file must be %d chars wide this line is %d chars wide.',
                $this->width,
                strlen($line)
            ));
        }

        return $line;
    }

    protected function getSize()
    {
        $fileData = $this->fileObject->fstat();
        return $fileData['size'];
    }
}