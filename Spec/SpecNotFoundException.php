<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/4/14
 * Time: 3:03 PM
 */

namespace Giftcards\FixedWidth\Spec;


use OutOfBoundsException;

class SpecNotFoundException extends \OutOfBoundsException
{
    public function __construct($name, $type, \Exception $previous = null)
    {
        parent::__construct(
            sprintf(
                'The %s spec named %s was not found.',
                $type,
                $name
            ),
            0,
            $previous
        );
    }
}