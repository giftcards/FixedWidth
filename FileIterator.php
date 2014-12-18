<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 12/18/14
 * Time: 12:03 AM
 */

namespace Giftcards\FixedWidth;


class FileIterator implements \Iterator
{
    protected $index;
    protected $current;
    protected $file;

    public function __construct(FileInterface $file)
    {
        $this->file = $file;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->index++;
        $this->loadCurrent();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return (bool)$this->current;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->index = 0;
        $this->loadCurrent();
    }

    protected function loadCurrent()
    {
        try {

            $this->current = $this->file->getLine($this->index);
        } catch (\OutOfBoundsException $e) {
            $this->current = null;
        }
    }
}