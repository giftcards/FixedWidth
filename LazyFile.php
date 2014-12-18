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
    protected $fileSize;

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
        $this->updateFileSize();

        $lineRemainder = $this->fileSize % $this->realLineWidth;
        
        if ($lineRemainder != 0 && $lineRemainder != $this->width) {
            
            throw new \InvalidArgumentException(sprintf(
                'It looks like the file supplied does not have all lines of the width %d. 
                make sure you have the correct line ending being passed since it is used in the comparison to figure this out.',
                $this->width
            ));
        }
    }

    public function __toString()
    {
        return file_get_contents($this->fileObject->getRealPath());
    }

    public function getLines()
    {
        return array_map(
            function($lineData){return new Line($lineData);}, 
            //array_filter is to take out the empty trailing line left behind by the trailing line separator
            array_filter(explode($this->__toString(), $this->lineSeparator))
        );
    }

    public function getLine($index)
    {
        if ($index >= $this->count()) {

            throw new \OutOfBoundsException('The index is outside of the available indexes of lines.');
        }
        
        $linePosition = $this->getLinePosition($index);
        
        $this->fileObject->fseek(
            $linePosition - $this->lineSeparatorLength,
            SEEK_SET
        );

        if ($this->fileObject->ftell() == 0) {
            
            $preLineEnding = $this->lineSeparator;
        } else {

            $preLineEnding = $this->readFromFile($this->lineSeparatorLength);
        }

        $lineData = $this->readFromFile($this->width);

        if ($this->fileObject->ftell() == $this->fileSize) {

            $postLineEnding = $this->lineSeparator;
        } else {

            $postLineEnding = $this->readFromFile($this->lineSeparatorLength);
        }

        if (($preLineEnding != $this->lineSeparator) || $postLineEnding != $this->lineSeparator) {
            
            throw new \RuntimeException(sprintf('the line is not bound by line endings.'));
        }
        
        return new Line($lineData);
    }

    public function count()
    {
        return ceil($this->fileSize/$this->realLineWidth);
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
        $this->fileObject->fseek(0, SEEK_END);
        $this->fileObject->fwrite((string)$line);
        $this->fileObject->fflush();
        $this->updateFileSize();
    }

    public function setLine($index, $line)
    {
        $line = $this->validateLine($line);
        $this->fileObject->fseek($this->getLinePosition($index), SEEK_SET);
        $this->fileObject->fwrite((string)$line);
        $this->fileObject->fwrite($this->lineSeparator);
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
        if (is_callable(array($this->fileObject, 'fread'))) {
            
            return $this->fileObject->fread($length);
        }

        $data = '';
        
        for ($i = 0; $i < $length; $i++) {

            if ($this->fileObject->ftell() == $this->fileSize) {
                
                throw new \OverflowException('overflowed the file');
            }
            
            $data .= $this->fileObject->fgetc();
        }
        
        return $data;
    }

    public function offsetExists($offset)
    {
        return $this->getLinePosition($offset) > $this->fileSize;
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

    protected function validateLine($line)
    {
        if (!$line instanceof Line) {

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

    protected function updateFileSize()
    {
        $fileData = $this->fileObject->fstat();
        $this->fileSize = $fileData['size'];
    }
}