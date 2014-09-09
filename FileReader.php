<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/4/14
 * Time: 3:36 PM
 */

namespace Giftcards\FixedWidth;


use Giftcards\FixedWidth\Spec\FieldSpec;
use Giftcards\FixedWidth\Spec\FileSpec;
use Giftcards\FixedWidth\Spec\Recognizer\FailedRecognizer;
use Giftcards\FixedWidth\Spec\Recognizer\RecordSpecRecognizerInterface;

class FileReader
{
    protected $file;
    protected $spec;
    protected $recognizer;

    public function __construct(
        File $file,
        FileSpec $spec,
        RecordSpecRecognizerInterface $recognizer = null
    ) {
        $this->spec = $spec;
        $this->file = $file;
        $this->recognizer = $recognizer ?: new FailedRecognizer();
    }

    public function getFile()
    {
        return $this->file;
    }

    public function parseField($lineNumber, $fieldName, $recordSpecName = null)
    {
        $line = $this->file[$lineNumber];

        $fieldSpec = $this->spec
            ->getRecordSpec($recordSpecName ?: $this->recognizer->recognize($line, $this->spec))
            ->getFieldSpec($fieldName)
        ;

        return $line[$fieldSpec->getSlice()];
    }

    public function parseLine($lineNumber, $recordSpecName = null)
    {
        $line = $this->file[$lineNumber];

        $fieldSpecs = $this->spec
            ->getRecordSpec($recordSpecName ?: $this->recognizer->recognize($line, $this->spec))
            ->getFieldSpecs()
        ;

        return array_map(function(FieldSpec $fieldSpec) use ($line)
        {
            return $line[$fieldSpec->getSlice()];
        }, $fieldSpecs);
    }

    /**
     * @param $lineNumber
     * @return string
     */
    public function getRecordSpecName($lineNumber)
    {
        return $this->recognizer->recognize($this->file[$lineNumber], $this->spec);
    }
}