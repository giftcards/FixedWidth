<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/10/14
 * Time: 6:35 PM
 */

namespace Giftcards\FixedWidth\Spec\Recognizer;


use Giftcards\FixedWidth\Line;
use Giftcards\FixedWidth\Spec\FileSpec;
use Giftcards\FixedWidth\Spec\SpecNotFoundException;

class SpecFieldRecognizer implements RecordSpecRecognizerInterface
{
    protected $field;

    public function __construct($field = '$id')
    {
        $this->field = $field;
    }

    public function recognize(Line $line, FileSpec $spec)
    {
        $specsMissingField = array();

        foreach ($spec->getRecordSpecs() as $name => $recordSpec) {


            try {

                $fieldSpec = $recordSpec->getFieldSpec($this->field);
            } catch(SpecNotFoundException $e) {

                $specsMissingField[] = $name;
                continue;
            }

            try {

                if ($fieldSpec->getDefault() == $line[$fieldSpec->getSlice()]) {

                    return $name;
                }
            } catch (\OutOfBoundsException $e) {
            }
        }

        $extraHelp = '';

        if (count($specsMissingField)) {

            $extraHelp = sprintf(
                'record specs named %s are missing the %s field.',
                implode(', ', $specsMissingField),
                $this->field
            );
        }

        throw new CouldNotRecognizeException($extraHelp);
    }
}