<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/12/14
 * Time: 5:24 PM
 */

namespace Giftcards\FixedWidth;


class Slice
{
    protected $start;
    protected $finish;
    protected $width;

    public static function createFromString($string)
    {
        $range = explode(':', $string);

        if (!isset($range[1])) {

            $range[1] = $range[0] + 1;
        }

        return new Slice($range[0], $range[1]);
    }

    public function __construct($start, $finish)
    {
        if ($finish < $start) {

            throw new \RangeException(sprintf('The range %s:%s has a negative width.', $start, $finish));
        }

        $this->finish = $finish;
        $this->start = $start;
        $this->width = $finish - $start;
    }

    public function __toString()
    {
        return $this->start.':'.$this->finish;
    }

    /**
     * @return integer
     */
    public function getFinish()
    {
        return $this->finish;
    }

    /**
     * @return integer
     */
    public function getStart()
    {
        return $this->start;
    }

    public function getWidth()
    {
        return $this->width;
    }
} 