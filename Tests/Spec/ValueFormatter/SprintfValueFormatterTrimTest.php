<?php

namespace Giftcards\FixedWidth\Tests\Spec\ValueFormatter;


use Giftcards\FixedWidth\Slice;
use Giftcards\FixedWidth\Spec\FieldSpec;
use Giftcards\FixedWidth\Spec\ValueFormatter\SprintfValueFormatterTrim;
use Giftcards\FixedWidth\Tests\TestCase;

class SprintfValueFormatterTrimTest extends TestCase
{
    /** @var SprintfValueFormatter */
    protected $formatter;

    public function setUp()
    {
        $this->formatter = new SprintfValueFormatterTrim();
    }

    public function testFormatFromFile()
    {
        $spec = new FieldSpec(
            $this->getFaker()->word,
            new Slice(0, 10),
            null,
            's',
            ' ',
            FieldSpec::PADDING_DIRECTION_RIGHT,
            'string'
        );
        $this->assertSame('hello', $this->formatter->formatFromFile($spec, 'hello     '));
    }
}
 