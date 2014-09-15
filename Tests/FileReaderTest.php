<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/15/14
 * Time: 3:05 PM
 */

namespace Giftcards\FixedWidth\Tests;


use Giftcards\FixedWidth\File;
use Giftcards\FixedWidth\FileReader;
use Giftcards\FixedWidth\Line;
use Giftcards\FixedWidth\Spec\FileSpec;
use Giftcards\FixedWidth\Spec\Loader\YamlSpecLoader;
use Mockery\MockInterface;
use Symfony\Component\Config\FileLocator;

class FileReaderTest extends TestCase
{
    /** @var FileReader */
    protected $reader;
    protected $file;
    /** @var  FileSpec */
    protected $spec;
    /** @var  MockInterface */
    protected $formatter;
    /** @var  MockInterface */
    protected $recognizer;

    public function setUp()
    {
        $loader = new YamlSpecLoader(new FileLocator(__DIR__.'/Fixtures/'));
        $this->spec = $loader->loadSpec('spec1');
        $this->reader = new FileReader(
            $this->file = new File($this->getFaker()->word, $this->spec->getWidth()),
            $this->spec,
            $this->formatter = \Mockery::mock('Giftcards\FixedWidth\Spec\ValueFormatter\ValueFormatterInterface'),
            $this->recognizer = \Mockery::mock('Giftcards\FixedWidth\Spec\Recognizer\RecordSpecRecognizerInterface')
        );
    }

    public function testGetFile()
    {
        $this->assertSame($this->file, $this->reader->getFile());
    }

    public function testParseField()
    {
        $this->file[] = new Line($this->spec->getWidth());
        $this->file[] = new Line($this->spec->getWidth());
        $this->file[1]['40:42'] = 'ho';
        $this->recognizer
            ->shouldReceive('recognize')
            ->once()
            ->with($this->file[1], $this->spec)
            ->andReturn('record1')
        ;
        $this->formatter
            ->shouldReceive('formatFromFile')
            ->twice()
            ->with($this->spec->getRecordSpec('record1')->getFieldSpec('field2'), 'ho')
            ->andReturn('ha')
        ;
        $this->assertEquals('ha', $this->reader->parseField(1, 'field2', 'record1'));
        $this->assertEquals('ha', $this->reader->parseField(1, 'field2'));
    }

    public function testParseLine()
    {
        $this->file[] = new Line($this->spec->getWidth());
        $this->file[] = new Line($this->spec->getWidth());
        $this->file[1]['34:39'] = 'seeya';
        $this->file[1]['40:42'] = 'ho';
        $this->recognizer
            ->shouldReceive('recognize')
            ->once()
            ->with($this->file[1], $this->spec)
            ->andReturn('record1')
        ;
        $this->formatter
            ->shouldReceive('formatFromFile')
            ->twice()
            ->with($this->spec->getRecordSpec('record1')->getFieldSpec('field2'), 'ho')
            ->andReturn('ha')
            ->getMock()
            ->shouldReceive('formatFromFile')
            ->twice()
            ->with($this->spec->getRecordSpec('record1')->getFieldSpec('field1'), 'seeya')
            ->andReturn('booya')
            ->getMock()
        ;
        $this->assertEquals(array(
            'field1' => 'booya',
            'field2' => 'ha',
        ), $this->reader->parseLine(1, 'record1'));
        $this->assertEquals(array(
            'field1' => 'booya',
            'field2' => 'ha',
        ), $this->reader->parseLine(1));
    }

    public function testGetRecordSpecName()
    {
        $this->file[] = $line = new Line($this->spec->getWidth());
        $this->recognizer
            ->shouldReceive('recognize')
            ->once()
            ->with($line, $this->spec)
            ->andReturn('record1')
        ;
        $this->assertEquals('record1', $this->reader->getRecordSpecName(0));
    }
}
 