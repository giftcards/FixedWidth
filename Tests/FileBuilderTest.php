<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/11/14
 * Time: 12:57 PM
 */

namespace Giftcards\FixedWidth\Tests;


use Giftcards\FixedWidth\File;
use Giftcards\FixedWidth\FileBuilder;
use Giftcards\FixedWidth\Line;
use Giftcards\FixedWidth\Spec\FileSpec;
use Giftcards\FixedWidth\Spec\Loader\YamlSpecLoader;
use Mockery\MockInterface;
use Symfony\Component\Config\FileLocator;

class FileBuilderTest extends TestCase
{
    /** @var  FileBuilder */
    protected $builder;
    protected $fileName;
    /** @var  FileSpec */
    protected $spec;
    /** @var  MockInterface */
    protected $formatter;

    public function setUp()
    {
        $loader = new YamlSpecLoader(new FileLocator(__DIR__.'/Fixtures/'));
        $this->spec = $loader->loadSpec('spec1');
        $this->builder = new FileBuilder(
            $this->fileName = $this->getFaker()->word,
            $this->spec,
            $this->formatter = \Mockery::mock('Giftcards\FixedWidth\Spec\ValueFormatter\ValueFormatterInterface')
        );
    }

    public function testAddRecord()
    {
        $this->formatter
            ->shouldReceive('formatToFile')
            ->once()
            ->with(
                $this->spec->getRecordSpec('record1')->getFieldSpec('field1'),
                'hello'
            )
            ->andReturn('x0.00')
            ->getMock()
            ->shouldReceive('formatToFile')
            ->once()
            ->with(
                $this->spec->getRecordSpec('record1')->getFieldSpec('field2'),
                'go'
            )
            ->andReturn('go')
            ->getMock()
            ->shouldReceive('formatToFile')
            ->once()
            ->with(
                $this->spec->getRecordSpec('record1')->getFieldSpec('field1'),
                3
            )
            ->andReturn('x3.00')
            ->getMock()
            ->shouldReceive('formatToFile')
            ->once()
            ->with(
                $this->spec->getRecordSpec('record1')->getFieldSpec('field2'),
                'h'
            )
            ->andReturn('hw')
            ->getMock()
            ->shouldReceive('formatToFile')
            ->once()
            ->with(
                $this->spec->getRecordSpec('record2')->getFieldSpec('field3'),
                12345
            )
            ->andReturn('                 12345')
            ->getMock()
        ;

        $this->builder
            ->addRecord('record1', array(
                'field2' => 'go'
            ))
            ->addRecord('record1', array(
                'field1' => 3,
                'field2' => 'h'
            ))
            ->addRecord('record2', array(
                'field3' => 12345
            ))
        ;
        $line1 = new Line($this->spec->getWidth());
        $line2 = new Line($this->spec->getWidth());
        $line3 = new Line($this->spec->getWidth());
        $line1['34:39'] = 'x0.00';
        $line1['40:42'] = 'go';
        $line2['34:39'] = 'x3.00';
        $line2['40:42'] = 'hw';
        $line3['34:56'] = '                 12345';
        $file = new File($this->fileName, $this->spec->getWidth());
        $file[] = $line1;
        $file[] = $line2;
        $file[] = $line3;
        $this->assertEquals($file, $this->builder->getFile());
    }

    /**
     * @expectedException \Giftcards\FixedWidth\FieldRequiredException
     */
    public function testAddRecordWhereRequiredFieldIsMissing()
    {
        $this->formatter->shouldIgnoreMissing();
        $this->builder
            ->addRecord('record1', array(
                'field1' => 3
            ))
        ;
    }
}
 