<?php
namespace Giftcards\FixedWidth\Spec\ValueFormatter;

use Giftcards\FixedWidth\Spec\ValueFormatter\SprintfValueFormatter;
use Giftcards\FixedWidth\Spec\FieldSpec;

/**
 * @author Cody Phillips
 */
class SprintfValueFormatterTrim extends SprintfValueFormatter {
    
    public function formatFromFile(FieldSpec $spec, $value)
    {
        $type = 'string';

        if (isset($this->typeSpecifierMap[substr($spec->getFormatSpecifier(), -1)])) {

            $type = $this->typeSpecifierMap[substr($spec->getFormatSpecifier(), -1)];
        }

        settype($value, $type);
        
        if ($type == 'string' && ($paddingChar = $spec->getPaddingChar()) !== null) {
            if ($spec->getPaddingDirection() == FieldSpec::PADDING_DIRECTION_LEFT) {
                $value = ltrim($value, $paddingChar);
            } else {
                $value = rtrim($value, $paddingChar);
            }
        }
        
        return $value;
    }
}
