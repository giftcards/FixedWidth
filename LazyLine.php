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

    public function __construct(\SplFileObject $file, $startPosition, $length)
    {
        $this->file = $file;
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
        $this->file->fseek($this->startPosition + $slice->getStart());

        $width = $slice->getWidth();
        
        $data = '';
        
        for ($i = 0; $i < $width; $i++) {
            
            $data .= $this->file->fgetc();
        }
        
        return $data;
    }

    protected function setSlice(Slice $slice, $value)
    {
        $this->checkSlice($slice);
        $this->file->fseek($this->startPosition + $slice->getStart());
        $value = str_pad(substr($value, 0, $slice->getWidth()), $slice->getWidth(), ' ', STR_PAD_RIGHT);
        $this->file->fwrite($value);
        return $this;
    }
}