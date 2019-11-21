<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/12/14
 * Time: 4:06 PM
 */

namespace Giftcards\FixedWidth\Tests\Spec\Recognizer;


use Giftcards\FixedWidth\Line;
use Giftcards\FixedWidth\Spec\Loader\YamlSpecLoader;
use Giftcards\FixedWidth\Spec\Recognizer\SpecFieldRecognizer;
use Giftcards\FixedWidth\Tests\TestCase;
use Mockery;
use Symfony\Component\Config\FileLocator;

class SpecFieldRecognizerTest extends TestCase
{
    /** @var SpecFieldRecognizer  */
    protected $recognizer;

    public function setUp() : void
    {
        $this->recognizer = new SpecFieldRecognizer('field1');
    }

    public function tearDown() : void
    {
        Mockery::close();
    }

    public function testSuccessfulRecognize()
    {
        $loader = new YamlSpecLoader(new FileLocator(__DIR__.'/../../Fixtures/'));
        $spec = $loader->loadSpec('field_recognizer');
        $line = new Line(60);
        $line['34:39'] = 'hello';
        $this->assertEquals('record1', $this->recognizer->recognize($line, $spec));
        $line = new Line(60);
        $line['34:41'] = 'goodbye';
        $this->assertEquals('record2', $this->recognizer->recognize($line, $spec));
    }

    public function testFailedRecognize()
    {
        $this->expectException('\Giftcards\FixedWidth\Spec\Recognizer\CouldNotRecognizeException');
        $loader = new YamlSpecLoader(new FileLocator(__DIR__.'/../../Fixtures/'));
        $spec = $loader->loadSpec('field_recognizer');
        $line = new Line(60);
        $line['34:39'] = 'goo';
        $this->assertEquals('record1', $this->recognizer->recognize($line, $spec));
        $line = new Line(60);
        $line['34:41'] = 'bye';
        $this->assertEquals('record2', $this->recognizer->recognize($line, $spec));
    }
}
