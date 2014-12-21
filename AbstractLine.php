<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/3/14
 * Time: 9:09 PM
 */

namespace Giftcards\FixedWidth;


abstract class AbstractLine implements LineInterface, \ArrayAccess
{
    public function __toString()
    {
        return $this->loadSlice(Slice::createFromString('0:'.$this->getLength()));
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

    protected function checkSlice(Slice $slice)
    {
        if ($slice->getFinish() > $this->getLength() || $slice->getStart() < 0) {

            throw new \OutOfBoundsException(sprintf('the slice %s is outside the length %d of this line.', $slice, $this->getLength()));
        }
    }

    abstract protected function loadSlice(Slice $slice);
    abstract protected function setSlice(Slice $slice, $value);
}