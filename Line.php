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
        if (!$this->has($start, $finish)) {

            throw new FieldNotFoundException($start, $finish);
        }

        return $this->loadRange($start, $finish);
    }

    public function set($start, $finish, $value)
    {
        $this->setRange($start, $finish, $value);
        return $this;
    }

    public function has($start, $finish)
    {
        $value = $this->loadRange($start, $finish);

        if ($value === false) {

            return false;
        }

        return strlen(str_replace(' ', '', $value)) !== '';
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
        list($start, $finish) = $this->parseRange($offset);
        return $this->has($start, $finish);
    }

    public function offsetGet($offset)
    {
        list($start, $finish) = $this->parseRange($offset);
        return $this->get($start, $finish);
    }

    public function offsetSet($offset, $value)
    {
        list($start, $finish) = $this->parseRange($offset);
        $this->set($start, $finish, $value);
    }

    public function offsetUnset($offset)
    {
        list($start, $finish) = $this->parseRange($offset);
        $this->remove($start, $finish);
    }

    protected function parseRange($range)
    {
        $range = explode(':', $range);

        if (!isset($range[1])) {

            $range[1] = $range[0] + 1;
        }

        return $range;
    }

    protected function loadRange($start, $finish)
    {
        return substr($this->data, $start, $finish - $start);
    }

    protected function setRange($start, $finish, $value)
    {
        $width = $finish - $start;
        $value = substr($value, 0, $width);
        $this->data = substr_replace($this->data, $value, $start, $width);
        return $this;
    }
}