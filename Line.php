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

    public function get($start, $finish)
    {
        return $this->loadSlice(new Slice($start, $finish));
    }

    public function set($start, $finish, $value)
    {
        $this->setSlice(new Slice($start, $finish), $value);
        return $this;
    }

    public function has($start, $finish)
    {
        try {

            $this->loadSlice(new Slice($start, $finish));
            return true;
        } catch (\OutOfBoundsException $e) {

            return false;
        }
    }

    public function remove($start, $finish)
    {
        $value = str_repeat(
            ' ',
            $start,
            $finish - $start
        );

        return $this->set($start, $start, $value);
    }

    public function getLength()
    {
        return strlen($this->data);
    }

    public function offsetExists($offset)
    {
        $slice = $this->parseSlice($offset);
        return $this->has($slice->getStart(), $slice->getFinish());
    }

    public function offsetGet($offset)
    {
        $slice = $this->parseSlice($offset);
        return $this->get($slice->getStart(), $slice->getFinish());
    }

    public function offsetSet($offset, $value)
    {
        $slice = $this->parseSlice($offset);
        $this->set($slice->getStart(), $slice->getFinish(), $value);
    }

    public function offsetUnset($offset)
    {
        $slice = $this->parseSlice($offset);
        $this->remove($slice->getStart(), $slice->getFinish());
    }

    protected function parseSlice($range)
    {
        if ($range instanceof Slice) {

            return $range;
        }

        return Slice::createFromString($range);
    }

    protected function loadSlice(Slice $slice)
    {
        $this->checkSlice($slice);
        return substr($this->data, $slice->getStart(), $slice->getWidth());
    }

    protected function setSlice(Slice $slice, $value)
    {
        $this->checkSlice($slice);
        $value = substr($value, 0, $slice->getWidth());
        $this->data = substr_replace($this->data, $value, $slice->getStart(), $slice->getWidth());
        return $this;
    }

    protected function checkSlice(Slice $slice)
    {
        if ($slice->getFinish() >= strlen($this->data) || $slice->getStart() < 0) {

            throw new \OutOfBoundsException(sprintf('the slice %s is outside the length %d of this line.', $slice, $this->getLength()));
        }
    }
}