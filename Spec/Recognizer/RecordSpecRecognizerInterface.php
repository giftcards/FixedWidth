<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/4/14
 * Time: 10:40 PM
 */

namespace Giftcards\FixedWidth\Spec\Recognizer;


use Giftcards\FixedWidth\Line;
use Giftcards\FixedWidth\Spec\FileSpec;

interface RecordSpecRecognizerInterface
{
    /**
     * @param Line $line
     * @param FileSpec $spec
     * @return string
     */
    public function recognize(Line $line, FileSpec $spec);
} 