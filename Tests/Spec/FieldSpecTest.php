<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/12/14
 * Time: 5:22 PM
 */

namespace Giftcards\FixedWidth\Tests\Spec;


use Giftcards\FixedWidth\Slice;
use Giftcards\FixedWidth\Spec\FieldSpec;
use Giftcards\FixedWidth\Tests\TestCase;

class FieldSpecTest extends TestCase
{
    public function testGetters()
    {
        $default = $this->getFaker()->word;
        $formatSpecifier = $this->getFaker()->word;
        $name = $this->getFaker()->word;
        $slice = Slice::createFromString($this->getFaker()->numberBetween(0, 10).':'.$this->getFaker()->numberBetween(10, 20));
        $paddingChar = $this->getFaker()->word;
        $paddingDirection = $this->getFaker()->word;
        $type = $this->getFaker()->word;
        $spec = new FieldSpec(
            $name,
            $slice,
            $default,
            $formatSpecifier,
            $paddingChar,
            $paddingDirection,
            $type
        );
        $this->assertEquals($default, $spec->getDefault());
        $this->assertEquals($formatSpecifier, $spec->getFormatSpecifier());
        $this->assertEquals($name, $spec->getName());
        $this->assertEquals($slice, $spec->getSlice());
        $this->assertEquals($paddingChar, $spec->getPaddingChar());
        $this->assertEquals($paddingDirection, $spec->getPaddingDirection());
        $this->assertEquals($type, $spec->getType());
    }
}
