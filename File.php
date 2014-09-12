<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/2/14
 * Time: 1:00 PM
 */

namespace Giftcards\FixedWidth;


use Traversable;


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

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->lines[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->lines[$offset];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @throws \InvalidArgumentException
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {

            $this->addLine($value);
            return;
        }

        $this->setLine($offset, $value);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->removeLine($offset);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->lines);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
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