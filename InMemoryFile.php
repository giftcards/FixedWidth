<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/2/14
 * Time: 1:00 PM
 */

namespace Giftcards\FixedWidth;


class InMemoryFile extends AbstractFile
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

    /**
     * @return string
     */
    public function getLines()
    {
        return $this->lines;
    }

    public function getLine($index)
    {
        if ($index >= $this->count()) {

            throw new \OutOfBoundsException('The index is outside of the available indexes of lines.');
        }

        return $this->lines[$index];
    }

    public function offsetExists($offset)
    {
        return isset($this->lines[$offset]);
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

    public function addLine($line)
    {
        $this->lines[] = $this->validateLine($line);
        return $this;
    }

    public function setLine($index, $line)
    {
        if ($index >= $this->count()) {

            throw new \OutOfBoundsException('setLine can only be used to update lines. To add a new line use addLine.');
        }

        $this->lines[$index] = $this->validateLine($line);
        return $this;
    }

    public function removeLine($index)
    {
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines);
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    public function getLineSeparator()
    {
        return $this->lineSeparator;
    }
}