<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/15/14
 * Time: 1:58 PM
 */

namespace Giftcards\FixedWidth\Spec\ValueFormatter;


use Giftcards\FixedWidth\Spec\FieldSpec;

interface ValueFormatterInterface
{
    public function formatToFile(FieldSpec $spec, $value);
    public function formatFromFile(FieldSpec $spec, $value);
} 