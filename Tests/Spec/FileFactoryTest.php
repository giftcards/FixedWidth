<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/10/14
 * Time: 6:49 PM
 */

namespace Giftcards\FixedWidth\Tests\Spec;


use Giftcards\FixedWidth\File;
use Giftcards\FixedWidth\FileBuilder;
use Giftcards\FixedWidth\Spec\FileFactory;
use Giftcards\FixedWidth\FileReader;
use Giftcards\FixedWidth\Spec\FileSpec;
use Giftcards\FixedWidth\Spec\ValueFormatter\SprintfValueFormatter;
use Giftcards\FixedWidth\Tests\FileFactoryTest as BaseFileFactoryTest;
use Mockery\MockInterface;

class FileFactoryTest extends BaseFileFactoryTest
{
    /** @var  FileFactory */
    protected $factory;
    /** @var  MockInterface */
    protected $specLoader;

    public function setUp()
    {
        $this->factory = new FileFactory(
            $this->specLoader = \Mockery::mock('Giftcards\FixedWidth\Spec\Loader\SpecLoaderInterface')
        );
    }

    public function testRecordSpecRecognizerManagement()
    {
        $recognizer = \Mockery::mock('Giftcards\FixedWidth\Spec\Recognizer\RecordSpecRecognizerInterface');
        $specName = $this->getFaker()->word;
        $this->assertSame($this->factory, $this->factory->addRecordSpecRecognizer(
            $specName,
            $recognizer
        ));
        $this->assertEquals(array($specName => $recognizer), $this->factory->getRecordSpecRecognizers());
    }

    public function testCreateBuilder()
    {
        $specName = $this->getFaker()->word;
        $name = $this->getFaker()->word;
        $spec = new FileSpec('', array(), 0, "\r\n");
        $this->specLoader
            ->shouldReceive('loadSpec')
            ->once()
            ->with($specName)
            ->andReturn($spec)
        ;
        $this->assertEquals(new FileBuilder($name, $spec, new SprintfValueFormatter()), $this->factory->createBuilder($name, $specName));
    }

    public function testCreateReader()
    {
        $specName = $this->getFaker()->word;
        $spec = new FileSpec('', array(), 0, "\r\n");
        $file = new File('', 0);
        $this->specLoader
            ->shouldReceive('loadSpec')
            ->twice()
            ->with($specName)
            ->andReturn($spec)
        ;
        $this->assertEquals(new FileReader($file, $spec, new SprintfValueFormatter()), $this->factory->createReader($file, $specName));
        $recognizer = \Mockery::mock('Giftcards\FixedWidth\Spec\Recognizer\RecordSpecRecognizerInterface');
        $this->factory->addRecordSpecRecognizer(
            $specName,
            $recognizer
        );
        $this->assertEquals(new FileReader($file, $spec, new SprintfValueFormatter(), $recognizer), $this->factory->createReader($file, $specName));
    }
}
 