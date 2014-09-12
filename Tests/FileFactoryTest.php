<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/10/14
 * Time: 6:49 PM
 */

namespace Giftcards\FixedWidth\Tests;


use Giftcards\FixedWidth\File;
use Giftcards\FixedWidth\FileBuilder;
use Giftcards\FixedWidth\FileFactory;
use Giftcards\FixedWidth\FileReader;
use Giftcards\FixedWidth\Spec\FileSpec;
use Mockery\MockInterface;

class FileFactoryTest extends TestCase
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

    public function testCreate()
    {
        $width = $this->getFaker()->numberBetween(5, 15);
        $name = $this->getFaker()->word;
        $this->assertEquals(new File($name, $width), $this->factory->create(
            $name,
            $width
        ));
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
        $spec = new FileSpec(array(), '', 0);
        $this->specLoader
            ->shouldReceive('loadSpec')
            ->once()
            ->with($specName)
            ->andReturn($spec)
        ;
        $this->assertEquals(new FileBuilder($name, $spec), $this->factory->createBuilder($name, $specName));
    }

    public function testCreateFromFile()
    {
        $file = new \SplFileInfo(__DIR__.'/Fixtures/fixed_width.txt');
        $lines = explode("\r\n", file_get_contents($file->getRealPath()));

        $this->assertEquals(
            new File($file->getFilename(), strlen($lines[0]), $lines),
            $this->factory->createFromFile($file)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateFromFileWhereFileIsEmpty()
    {
        $this->factory->createFromFile(new \SplFileInfo(__DIR__.'/Fixtures/empty_fixed_width.txt'));
    }

    public function testCreateReader()
    {
        $specName = $this->getFaker()->word;
        $spec = new FileSpec(array(), '', 0);
        $file = new File('', 0);
        $this->specLoader
            ->shouldReceive('loadSpec')
            ->twice()
            ->with($specName)
            ->andReturn($spec)
        ;
        $this->assertEquals(new FileReader($file, $spec), $this->factory->createReader($file, $specName));
        $recognizer = \Mockery::mock('Giftcards\FixedWidth\Spec\Recognizer\RecordSpecRecognizerInterface');
        $this->factory->addRecordSpecRecognizer(
            $specName,
            $recognizer
        );
        $this->assertEquals(new FileReader($file, $spec, $recognizer), $this->factory->createReader($file, $specName));
    }
}
 