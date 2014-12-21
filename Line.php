<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/3/14
 * Time: 9:09 PM
 */

namespace Giftcards\FixedWidth;


class Line extends AbstractLine
{
    protected $data;

    public function __construct($dataOrLength)
    {
        $this->data = is_int($dataOrLength) ? str_repeat(' ', $dataOrLength) : $dataOrLength;
    }

    public function getLength()
    {
        return strlen($this->data);
    }

    protected function loadSlice(Slice $slice)
    {
        $this->checkSlice($slice);
        return substr($this->data, $slice->getStart(), $slice->getWidth());
    }

    protected function setSlice(Slice $slice, $value)
    {
        $this->checkSlice($slice);
        $value = str_pad(substr($value, 0, $slice->getWidth()), $slice->getWidth(), ' ', STR_PAD_RIGHT);
        $this->data = substr_replace($this->data, $value, $slice->getStart(), $slice->getWidth());
        return $this;
    }
}