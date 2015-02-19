<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/3/14
 * Time: 9:09 PM
 */

namespace Giftcards\FixedWidth;


class FileSystemLine extends AbstractLine
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

    public function __toString()
    {
        try {

            return parent::__toString();
        } catch (\OverflowException $e) {

            return '';
        }
    }

    public function getLength()
    {
        return $this->length;
    }

    protected function loadSlice(Slice $slice)
    {
        $this->checkSlice($slice);
        $this->seekToStart($slice);
        return $this->readFromFile($slice->getWidth());
    }

    protected function setSlice(Slice $slice, $value)
    {
        $this->checkSlice($slice);
        $this->seekToStart($slice);
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

    protected function seekToStart(Slice $slice)
    {
        $position = $this->fileObject->ftell();
        $seekTo = ($this->startPosition + $slice->getStart()) - $position;
        
        if ($seekTo == 0) {
            
            return;
        }
        
        $this->fileObject->fseek($seekTo, SEEK_CUR);
    }
}