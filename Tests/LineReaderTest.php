<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/30/14
 * Time: 2:40 PM
 */

namespace Giftcards\FixedWidth\Tests;


use Giftcards\FixedWidth\Line;
use Giftcards\FixedWidth\LineReader;
use Giftcards\FixedWidth\Spec\Loader\YamlSpecLoader;
use Giftcards\FixedWidth\Spec\RecordSpec;
use Mockery\MockInterface;
use Symfony\Component\Config\FileLocator;

class LineReaderTest extends TestCase
{
    /** @var  LineReader */
    protected $reader;
    /** @var  Line */
    protected $line;
    /** @var  RecordSpec */
    protected $spec;
    /** @var  MockInterface */
    protected $formatter;

    public function setUp()
    {
        $loader = new YamlSpecLoader(new FileLocator(__DIR__.'/Fixtures/'));
        $spec = $loader->loadSpec('spec1');
        $this->reader = new LineReader(
            $this->line = new Line($spec->getWidth()),
            $this->spec = $spec->getRecordSpec('record1'),
            $this->formatter = \Mockery::mock('Giftcards\FixedWidth\Spec\ValueFormatter\ValueFormatterInterface')
        );
    }

    public function testGetField()
    {
        $this->line['40:42'] = 'ho';
        $this->formatter
            ->shouldReceive('formatFromFile')
            ->twice()
            ->with($this->spec->getFieldSpec('field2'), 'ho')
            ->andReturn('ha')
        ;
        $this->assertEquals('ha', $this->reader->getField('field2'));
        $this->assertEquals('ha', $this->reader['field2']);
    }

    public function testParseLine()
    {
        $this->line['34:39'] = 'seeya';
        $this->line['40:42'] = 'ho';
        $this->formatter
            ->shouldReceive('formatFromFile')
            ->once()
            ->with($this->spec->getFieldSpec('field2'), 'ho')
            ->andReturn('ha')
            ->getMock()
            ->shouldReceive('formatFromFile')
            ->once()
            ->with($this->spec->getFieldSpec('field1'), 'seeya')
            ->andReturn('booya')
            ->getMock()
        ;
        $this->assertEquals(array(
            'field1' => 'booya',
            'field2' => 'ha',
        ), $this->reader->getFields());
    }

    public function testIsset()
    {
        $this->assertTrue(isset($this->reader['field2']));
        $this->assertFalse(isset($this->reader['field3']));
    }

    public function testGetLine()
    {
        $this->assertSame($this->line, $this->reader->getLine());
    }

    public function testSpec()
    {
        $this->assertSame($this->spec, $this->reader->getSpec());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testOffsetSet()
    {
        $this->reader['field2'] = 'hello';
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testOffsetUnset()
    {
        unset($this->reader['field2']);
    }
}
 