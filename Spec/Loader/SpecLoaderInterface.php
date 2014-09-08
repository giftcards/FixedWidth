<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/3/14
 * Time: 10:10 PM
 */

namespace Giftcards\FixedWidth\Spec\Loader;


use Giftcards\FixedWidth\Spec\FileSpec;

interface SpecLoaderInterface
{
    /**
     * @param $value
     * @return FileSpec
     * @throw SpecNotFoundException
     */
    public function loadSpec($value);
} 