<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/4/14
 * Time: 5:24 PM
 */

namespace Giftcards\FixedWidth;


use Giftcards\FixedWidth\Spec\FieldSpec;

class FieldRequiredException extends \Exception
{
    public function __construct($recordSpecName, FieldSpec $fieldSpec)
    {
        parent::__construct(sprintf(
            'A value for %s in record %s is required.',
            $fieldSpec->getName(),
            $recordSpecName
        ));
    }
} 