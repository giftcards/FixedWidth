<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/2/14
 * Time: 1:00 PM
 */

namespace Giftcards\FixedWidth;


abstract class AbstractFile implements \IteratorAggregate, \ArrayAccess, FileInterface
{
    public function __toString()
    {
        return implode($this->getLineSeparator(), $this->getLines());
    }

    public function offsetGet($offset)
    {
        return $this->getLine($offset);
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

    public function getIterator()
    {
        return new FileIterator($this);
    }

    public function newLine()
    {
        $this->addLine(str_repeat(' ', $this->getWidth()));
        return $this->getLine($this->count() - 1);
    }
    
    protected function validateLine($line)
    {
        if (!$line instanceof Line) {

            $line = new Line((string)$line);
        }

        if ($line->getLength() != $this->getWidth()) {

            throw new \InvalidArgumentException(sprintf(
                'All lines in a batch file must be %d chars wide this line is %d chars wide.',
                $this->getWidth(),
                strlen($line)
            ));
        }

        return $line;
    }
}