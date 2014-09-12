<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/4/14
 * Time: 10:45 PM
 */

namespace Giftcards\FixedWidth\Spec\Recognizer;


class CouldNotRecognizeException extends \Exception
{
    public function __construct($extraHelp = '')
    {
        parent::__construct(sprintf(
            'Could not recognize the spec for the given line. %s',
            $extraHelp ? 'this may help: '.$extraHelp : ''
        ));
    }
} 