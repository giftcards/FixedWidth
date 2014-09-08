<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/4/14
 * Time: 4:01 PM
 */

namespace Giftcards\FixedWidth;


class FieldNotFoundException extends \OutOfBoundsException
{
    public function __construct($name)
    {
        parent::__construct(sprintf('Field %s was not found.', $name));
    }

} 