<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/3/14
 * Time: 9:09 PM
 */

namespace Giftcards\FixedWidth;


class LazyLine extends AbstractLine
{
    protected $file;
    protected $startPosition;
    protected $length;

    public function __construct(\SplFileObject $fileObject, $startPosition, $length)
    {
        $this->fileObject = $fileObject;
        $this->startPosition = $startPosition;
        $this->length = $length;
    }

    public function getLength()
    {
        return $this->length;
    }

    protected function loadSlice(Slice $slice)
    {
        $this->checkSlice($slice);
        $this->fileObject->fseek($this->startPosition + $slice->getStart());
        return $this->readFromFile($slice->getWidth());
    }

    protected function setSlice(Slice $slice, $value)
    {
        $this->checkSlice($slice);
        $this->fileObject->fseek($this->startPosition + $slice->getStart());
        $value = str_pad(substr($value, 0, $slice->getWidth()), $slice->getWidth(), ' ', STR_PAD_RIGHT);
        $this->fileObject->fwrite($value);
        return $this;
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
}