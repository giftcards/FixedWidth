<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 12/15/14
 * Time: 8:49 PM
 */
namespace Giftcards\FixedWidth;


class LazyFile extends AbstractFile
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

    public function getLines()
    {
        return array_map(
            function($index)
            {
                return $this->getLine($index);
            }, 
            range(0, $this->count() - 1)
        );
    }

    public function getLine($index)
    {
        if ($index >= $this->count()) {

            throw new \OutOfBoundsException('The index is outside of the available indexes of lines.');
        }
        
        return new LazyLine($this->fileObject, $this->getLinePosition($index), $this->width);
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
        if ($index >= $this->count()) {
            
            throw new \OutOfBoundsException('setLine can only be used to update lines. To add a new line use addLine.');
        }
        
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

    public function offsetExists($offset)
    {
        return $this->getLinePosition($offset) > $this->getSize();
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