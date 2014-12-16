<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 12/15/14
 * Time: 8:49 PM
 */
namespace Giftcards\FixedWidth;


class LazyFile extends File
{
    protected $fileObject;
    protected $lineSeparatorLength;
    protected $realLineWidth;

    public function __construct(
        $name,
        $width,
        \SplFileObject $fileObject,
        $lineSeparator = "\r\n"
    ) {
        $this->fileObject = $fileObject;
        $this->lineSeparatorLength = strlen($this->lineSeparator);
        $this->realLineWidth = $this->width + $this->lineSeparatorLength;
        
        parent::__construct(
            $name,
            $width,
            array(),
            $lineSeparator
        );
    }

    public function __toString()
    {
        return parent::__toString();
    }

    public function getLines()
    {
        return parent::getLines();
    }

    public function getLine($index)
    {
        $linePosition = $this->getLinePosition($index);
        
        $this->fileObject->fseek(
            $linePosition - $this->lineSeparatorLength,
            SEEK_SET
        );
        
        $lineData = '';

        $totalWidth = $this->realLineWidth + $this->lineSeparatorLength;
        
        for ($i = 0; $i < $totalWidth; $i++) {

            $lineData .= $this->fileObject->fgetc();
        }
        
        $preLineEnding = substr($lineData, 0, $this->lineSeparatorLength);
        $postLineEnding = substr($lineData, -$this->lineSeparatorLength);
        
        if ($preLineEnding != $this->lineSeparator || $postLineEnding != $this->lineSeparator) {
            
            throw new \RuntimeException(sprintf('the line is not bound by line endings.'));
        }
        
        return new Line(substr($lineData, $this->lineSeparatorLength, strlen($lineData) - 2 * $this->lineSeparatorLength));
    }

    public function count()
    {
        return parent::count();
    }

    public function getName()
    {
        return parent::getName();
    }

    public function getIterator()
    {
        return parent::getIterator();
    }

    public function addLine($line)
    {
        $this->fileObject->fseek(0, SEEK_END);
        $this->fileObject->fwrite((string)$line);
    }

    public function setLine($index, $line)
    {
        return parent::setLine(
            $index,
            $line
        );
    }

    public function removeLine($index)
    {
        return parent::removeLine(
            $index
        );
    }

    public function newLine()
    {
        return parent::newLine();
    }

    public function getWidth()
    {
        return parent::getWidth();
    }

    public function getLineSeparator()
    {
        return parent::getLineSeparator(
        );
    }

    protected function getLinePosition($lineNumber)
    {
        return $this->realLineWidth * ($lineNumber - 1);
    }
}