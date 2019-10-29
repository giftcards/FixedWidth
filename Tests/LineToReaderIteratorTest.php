<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/30/14
 * Time: 2:56 PM
 */

namespace Giftcards\FixedWidth\Tests;


use Giftcards\FixedWidth\InMemoryFile;
use Giftcards\FixedWidth\Line;
use Giftcards\FixedWidth\LineReader;
use Giftcards\FixedWidth\LineToReaderIterator;
use Giftcards\FixedWidth\Spec\FileSpec;
use Giftcards\FixedWidth\Spec\Loader\YamlSpecLoader;
use Mockery\MockInterface;
use Symfony\Component\Config\FileLocator;

class LineToReaderIteratorTest extends TestCase
{
    /** @var  InMemoryFile */
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
        $this->formatter = \Mockery::mock('Giftcards\FixedWidth\Spec\ValueFormatter\ValueFormatterInterface');
        $this->recognizer = \Mockery::mock('Giftcards\FixedWidth\Spec\Recognizer\RecordSpecRecognizerInterface');
        $this->file = new InMemoryFile($this->getFaker()->word, $this->spec->getWidth());

    }

    public function tearDown()
    {
        \Mockery::close();
    }

    public function testIteration()
    {
        $this->file[] = new Line($this->spec->getWidth());
        $this->file[] = new Line($this->spec->getWidth());
        $this->recognizer
            ->shouldReceive('recognize')
            ->once()
            ->with($this->file[0], $this->spec)
            ->andReturn('record1')
            ->shouldReceive('recognize')
            ->once()
            ->with($this->file[1], $this->spec)
            ->andReturn('record2')
        ;

        $readers = iterator_to_array(new LineToReaderIterator(
            $this->file,
            $this->spec,
            $this->recognizer,
            $this->formatter
        ));

        $this->assertEquals(
            new LineReader($this->file[0], $this->spec->getRecordSpec('record1'), $this->formatter),
            $readers[0]
        );

        $this->assertEquals(
            new LineReader($this->file[1], $this->spec->getRecordSpec('record2'), $this->formatter),
            $readers[1]
        );
    }
}
