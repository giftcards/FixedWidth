<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/3/14
 * Time: 9:09 PM
 */

namespace Giftcards\FixedWidth;


interface LineInterface
{
    /**
     * @return mixed
     */
    public function __toString();

    /**
     * @param $slice
     * @return mixed
     */
    public function get($slice);

    /**
     * @param $slice
     * @param $value
     * @return mixed
     */
    public function set($slice, $value);

    /**
     * @param $slice
     * @return mixed
     */
    public function has($slice);

    /**
     * @param $slice
     * @return mixed
     */
    public function remove($slice);

    /**
     * @return mixed
     */
    public function getLength();
}