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
    protected $width;
    protected $lines = array();
    protected $lineSeparator;

    public function __construct(
        $name,
        $width,
        array $lines = array(),
        $lineSeparator = "\r\n"
    ) {
        $this->name = $name;
        $this->width = (int)$width;
        array_walk($lines, array($this, 'addLine'));
        $this->lineSeparator = $lineSeparator;
    }

    public function __toString()
    {
        return implode($this->lineSeparator, $this->lines);
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

    public function newLine()
    {
        $this->lines[] = $line = new Line($this->width);
        return $line;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
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