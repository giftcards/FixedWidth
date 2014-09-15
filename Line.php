<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/3/14
 * Time: 9:09 PM
 */

namespace Giftcards\FixedWidth;


class Line implements \ArrayAccess
{
    protected $data;

    public function __construct($dataOrLength)
    {
        $this->data = is_int($dataOrLength) ? str_repeat(' ', $dataOrLength) : $dataOrLength;
    }

    public function __toString()
    {
        return (string)$this->data;
    }

    public function get($slice)
    {
        return $this->loadSlice($this->normalizeSlice($slice));
    }

    public function set($slice, $value)
    {
        $this->setSlice($this->normalizeSlice($slice), $value);
        return $this;
    }

    public function has($slice)
    {
        try {

            $this->loadSlice($this->normalizeSlice($slice));
            return true;
        } catch (\OutOfBoundsException $e) {
        }

        return false;
    }

    public function remove($slice)
    {
        $slice = $this->normalizeSlice($slice);
        $value = str_repeat(
            ' ',
            $slice->getWidth()
        );

        $this->setSlice($slice, $value);
        return $this;
    }

    public function getLength()
    {
        return strlen($this->data);
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    protected function normalizeSlice($slice)
    {
        if ($slice instanceof Slice) {

            return $slice;
        }

        return Slice::createFromString($slice);
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

    protected function checkSlice(Slice $slice)
    {
        if ($slice->getFinish() > strlen($this->data) || $slice->getStart() < 0) {

            throw new \OutOfBoundsException(sprintf('the slice %s is outside the length %d of this line.', $slice, $this->getLength()));
        }
    }
}