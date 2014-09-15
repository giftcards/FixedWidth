<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/15/14
 * Time: 1:59 PM
 */

namespace Giftcards\FixedWidth\Spec\ValueFormatter;


use Giftcards\FixedWidth\Spec\FieldSpec;

class SprintfValueFormatter implements ValueFormatterInterface
{
    protected $typeSpecifierMap = array(
        'b' => 'integer',
        'd' => 'integer',
        'f' => 'float',
        'F' => 'float',
        'o' => 'integer',
        'u' => 'integer'
    );

    public function formatToFile(FieldSpec $spec, $value)
    {
        $slice = $spec->getSlice();

        $paddingChar = $spec->getPaddingChar();

        if (!in_array($paddingChar, array(' ', 0, '0', ''), true)) {

            $paddingChar = "'".$paddingChar;
        }

        return sprintf(
            sprintf(
                '%%%s%s%s%s',
                $paddingChar,
                $spec->getPaddingDirection() == FieldSpec::PADDING_DIRECTION_LEFT ? '' : '-',
                $slice->getWidth(),
                $spec->getFormatSpecifier()
            ),
            $value
        );
    }

    public function formatFromFile(FieldSpec $spec, $value)
    {
        $type = 'string';

        if (isset($this->typeSpecifierMap[substr($spec->getFormatSpecifier(), -1)])) {

            $type = $this->typeSpecifierMap[substr($spec->getFormatSpecifier(), -1)];
        }

        settype($value, $type);
        return $value;
    }
}