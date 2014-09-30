<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/4/14
 * Time: 10:40 PM
 */

namespace Giftcards\FixedWidth\Spec\Recognizer;


use Giftcards\FixedWidth\LineInterface;
use Giftcards\FixedWidth\Spec\FileSpec;

interface RecordSpecRecognizerInterface
{
    /**
     * @param LineInterface $line
     * @param FileSpec $spec
     * @return string
     */
    public function recognize(LineInterface $line, FileSpec $spec);
} 