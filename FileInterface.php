<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/2/14
 * Time: 1:00 PM
 */

namespace Giftcards\FixedWidth;


interface FileInterface extends \Traversable, \Countable
{
    /**
     * @return string
     */
    public function __toString();
    /**
     * @return LineInterface[]
     */
    public function getLines();
    /**
     * @param $index
     * @return LineInterface
     */
    public function getLine($index);
    /**
     * @return string
     */
    public function getName();
    /**
     * @param LineInterface|string $line
     * @return $this
     */
    public function addLine($line);
    /**
     * @param int $index
     * @param LineInterface|string $line
     * @return $this
     */
    public function setLine($index, $line);
    /**
     * @param int $index
     * @return $this
     */
    public function removeLine($index);
    /**
     * @return int
     */
    public function getWidth();
    /**
     * @return string
     */
    public function getLineSeparator();
}