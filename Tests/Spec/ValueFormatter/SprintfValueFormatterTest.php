<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/15/14
 * Time: 3:39 PM
 */

namespace Giftcards\FixedWidth\Tests\Spec\ValueFormatter;


use Giftcards\FixedWidth\Slice;
use Giftcards\FixedWidth\Spec\FieldSpec;
use Giftcards\FixedWidth\Spec\ValueFormatter\SprintfValueFormatter;
use Giftcards\FixedWidth\Tests\TestCase;

class SprintfValueFormatterTest extends TestCase
{
    /** @var SprintfValueFormatter */
    protected $formatter;

    public function setUp()
    {
        $this->formatter = new SprintfValueFormatter();
    }

    public function tearDown()
    {
        \Mockery::close();
    }

    public function testFormatToFile()
    {
        $spec = new FieldSpec(
            $this->getFaker()->word,
            new Slice(0, 4),
            null,
            '.1f',
            '0',
            FieldSpec::PADDING_DIRECTION_LEFT,
            'float'
        );
        $this->assertSame('02.3', $this->formatter->formatToFile($spec, 2.3));
    }

    public function testFormatToFileWithSpecialPaddingChar()
    {
        $spec = new FieldSpec(
            $this->getFaker()->word,
            new Slice(0, 4),
            null,
            '.1f',
            't',
            FieldSpec::PADDING_DIRECTION_LEFT,
            'float'
        );
        $this->assertSame('t2.3', $this->formatter->formatToFile($spec, 2.3));
    }

    public function testFormatFromFile()
    {
        $spec = new FieldSpec(
            $this->getFaker()->word,
            new Slice(0, 4),
            null,
            '.1f',
            '0',
            FieldSpec::PADDING_DIRECTION_LEFT,
            'float'
        );
        $this->assertSame(2.3, $this->formatter->formatFromFile($spec, '02.3'));
    }

    public function testFormatFromFileTrims()
    {
        $spec = new FieldSpec(
            $this->getFaker()->word,
            new Slice(0, 4),
            null,
            's',
            ' ',
            FieldSpec::PADDING_DIRECTION_LEFT,
            'string'
        );
        $this->assertSame('2.3', $this->formatter->formatFromFile($spec, ' 2.3'));
        $spec = new FieldSpec(
            $this->getFaker()->word,
            new Slice(0, 4),
            null,
            's',
            ' ',
            FieldSpec::PADDING_DIRECTION_RIGHT,
            'string'
        );
        $this->assertSame('2.3', $this->formatter->formatFromFile($spec, '2.3 '));
    }
}
