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
    public function __toString();
    public function get($slice);
    public function set($slice, $value);
    public function has($slice);
    public function remove($slice);
    public function getLength();
}