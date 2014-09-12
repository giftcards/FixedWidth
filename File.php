<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/2/14
 * Time: 1:00 PM
 */

namespace Giftcards\FixedWidth;


class File implements \ArrayAccess, \Countable, \IteratorAggregate
{
    protected $name;
    protected $lines;
    protected $width;

    public function __construct($name, $width, array $lines = array())
    {
        $this->name = $name;
        $this->width = $width;
        array_walk($lines, array($this, 'addLine'));
    }

    public function __toString()
    {
        return implode("\r\n", $this->lines);
    }

    /**
     * @return string
     */
    public function getLines()
    {
        return $this->lines;
    }

    public function offsetExists($offset)
    {
        return isset($this->lines[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->lines[$offset];
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

    public function count()
    {
        return count($this->lines);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->getLines());
    }

    public function addLine($line)
    {
        $this->lines[] = $this->validateLine($line);
        return $this;
    }

    public function setLine($index, $line)
    {
        $this->lines[$index] = $this->validateLine($line);
        return $this;
    }

    public function removeLine($index)
    {
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines);
        return $this;
    }

    protected function validateLine($line)
    {
        if (!$line instanceof Line) {

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
}