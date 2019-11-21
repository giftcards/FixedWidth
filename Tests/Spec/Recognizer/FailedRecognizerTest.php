<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/12/14
 * Time: 4:01 PM
 */

namespace Giftcards\FixedWidth\Tests\Spec\Recognizer;


use Giftcards\FixedWidth\Line;
use Giftcards\FixedWidth\Spec\FileSpec;
use Giftcards\FixedWidth\Spec\Recognizer\FailedRecognizer;
use Giftcards\FixedWidth\Tests\TestCase;
use Mockery;

class FailedRecognizerTest extends TestCase
{
    /** @var  FailedRecognizer */
    protected $recognizer;

    public function setUp() : void
    {
        $this->recognizer = new FailedRecognizer();
    }

    public function tearDown() : void
    {
        Mockery::close();
    }

    public function testRecognize()
    {
        $this->expectException('\Giftcards\FixedWidth\Spec\Recognizer\CouldNotRecognizeException');
        $this->recognizer->recognize(new Line(''), new FileSpec('', array(), 1, "\r\n"));
    }
}
