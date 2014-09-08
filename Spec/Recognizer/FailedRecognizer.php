<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/5/14
 * Time: 11:19 AM
 */

namespace Giftcards\FixedWidth\Spec\Recognizer;


use Giftcards\FixedWidth\Line;
use Giftcards\FixedWidth\Spec\FileSpec;

class FailedRecognizer implements RecordSpecRecognizerInterface
{
    /**
     * @param Line $line
     * @param FileSpec $spec
     * @return string
     * @throws CouldNotRecognizeException
     */
    public function recognize(Line $line, FileSpec $spec)
    {
        throw new CouldNotRecognizeException();
    }
}