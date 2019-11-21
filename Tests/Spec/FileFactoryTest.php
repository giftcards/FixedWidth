<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/10/14
 * Time: 6:49 PM
 */

namespace Giftcards\FixedWidth\Tests\Spec;


use Giftcards\FixedWidth\InMemoryFile;
use Giftcards\FixedWidth\FileBuilder;
use Giftcards\FixedWidth\Spec\FileFactory;
use Giftcards\FixedWidth\FileReader;
use Giftcards\FixedWidth\Spec\FileSpec;
use Giftcards\FixedWidth\Spec\ValueFormatter\SprintfValueFormatter;
use Giftcards\FixedWidth\Tests\FileFactoryTest as BaseFileFactoryTest;
use Mockery;
use Mockery\MockInterface;
use SplFileInfo;

class FileFactoryTest extends BaseFileFactoryTest
{
    /** @var  FileFactory */
    protected $factory;
    /** @var  MockInterface */
    protected $specLoader;

    public function setUp() : void
    {
        $this->factory = new FileFactory(
            $this->specLoader = Mockery::mock('Giftcards\FixedWidth\Spec\Loader\SpecLoaderInterface')
        );
    }

    public function tearDown() : void
    {
        Mockery::close();
    }

    public function testRecordSpecRecognizerManagement()
    {
        $recognizer = Mockery::mock('Giftcards\FixedWidth\Spec\Recognizer\RecordSpecRecognizerInterface');
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
        $spec = new FileSpec('', array(), 0, "\n");
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
        $spec = new FileSpec('', array(), 0, "\n");
        $file = new InMemoryFile('', 0);
        $this->specLoader
            ->shouldReceive('loadSpec')
            ->twice()
            ->with($specName)
            ->andReturn($spec)
        ;
        $this->assertEquals(new FileReader($file, $spec, new SprintfValueFormatter()), $this->factory->createReader($file, $specName));
        $recognizer = Mockery::mock('Giftcards\FixedWidth\Spec\Recognizer\RecordSpecRecognizerInterface');
        $this->factory->addRecordSpecRecognizer(
            $specName,
            $recognizer
        );
        $this->assertEquals(new FileReader($file, $spec, new SprintfValueFormatter(), $recognizer), $this->factory->createReader($file, $specName));
    }

    public function testCreateFromFileAndSpecWhereFileIsEmpty()
    {
        $this->expectException('\InvalidArgumentException');
        $specName = $this->getFaker()->word;
        $spec = new FileSpec('', array(), 0, "\n");
        $this->specLoader
            ->shouldReceive('loadSpec')
            ->once()
            ->with($specName)
            ->andReturn($spec)
        ;
        $this->factory->createFromFileAndSpec(
            new SplFileInfo(__DIR__.'/../Fixtures/empty_fixed_width.txt'),
            $specName
        );
    }

    public function testCreateFromFileAndSpecWhereFileHasTrailingEndline()
    {
        $specName = $this->getFaker()->word;
        $spec = new FileSpec('', array(), 0, "\n");
        $this->specLoader
            ->shouldReceive('loadSpec')
            ->once()
            ->with($specName)
            ->andReturn($spec)
        ;
        $file = new SplFileInfo(__DIR__.'/../Fixtures/fixed_width_trailing_newline.txt');
        $lines = explode("\n", file_get_contents($file->getRealPath()));

        array_pop($lines);

        $this->assertEquals(
            new InMemoryFile($file->getFilename(), strlen($lines[0]), $lines, "\n"),
            $this->factory->createFromFileAndSpec($file, $specName)
        );
    }

    public function testCreateFromDataAndSpec()
    {
        $specName = $this->getFaker()->word;
        $spec = new FileSpec('', array(), 0, "\n");
        $this->specLoader
            ->shouldReceive('loadSpec')
            ->once()
            ->with($specName)
            ->andReturn($spec)
        ;
        $file = new SplFileInfo(__DIR__.'/../Fixtures/fixed_width.txt');
        $lines = explode("\n", file_get_contents($file->getRealPath()));

        $this->assertEquals(
            new InMemoryFile($file->getFilename(), strlen($lines[0]), $lines, "\n"),
            $this->factory->createFromFileAndSpec($file, $specName)
        );
    }

    public function testCreateFromDataAndSpecWhereDataIsEmpty()
    {
        $this->expectException('\InvalidArgumentException');
        $specName = $this->getFaker()->word;
        $name = $this->getFaker()->word;
        $spec = new FileSpec('', array(), 0, "\n");
        $this->specLoader
            ->shouldReceive('loadSpec')
            ->once()
            ->with($specName)
            ->andReturn($spec)
        ;
        $this->factory->createFromDataAndSpec(
            file_get_contents(__DIR__.'/../Fixtures/empty_fixed_width.txt'),
            $name,
            $specName
        );
    }

    public function testCreateFromDataAndSpecWhereDataHasTrailingEndline()
    {
        $specName = $this->getFaker()->word;
        $name = $this->getFaker()->word;
        $spec = new FileSpec('', array(), 0, "\n");
        $this->specLoader
            ->shouldReceive('loadSpec')
            ->once()
            ->with($specName)
            ->andReturn($spec)
        ;
        $data = file_get_contents(__DIR__.'/../Fixtures/fixed_width_trailing_newline.txt');
        $lines = explode("\n", $data);

        array_pop($lines);

        $this->assertEquals(
            new InMemoryFile(
                $name,
                strlen($lines[0]),
                $lines,
                "\n"
            ),
            $this->factory->createFromDataAndSpec(
                $data,
                $name,
                $specName
            )
        );
    }
}
