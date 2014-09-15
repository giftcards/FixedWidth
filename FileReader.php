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
use Giftcards\FixedWidth\Spec\ValueFormatter\ValueFormatterInterface;

class FileReader
{
    protected $file;
    protected $spec;
    protected $formatter;
    protected $recognizer;

    public function __construct(
        File $file,
        FileSpec $spec,
        ValueFormatterInterface $formatter,
        RecordSpecRecognizerInterface $recognizer = null
    ) {
        $this->spec = $spec;
        $this->file = $file;
        $this->formatter = $formatter;
        $this->recognizer = $recognizer ?: new FailedRecognizer();
    }

    public function getFile()
    {
        return $this->file;
    }

    public function parseField($lineIndex, $fieldName, $recordSpecName = null)
    {
        $line = $this->file[$lineIndex];

        $fieldSpec = $this->spec
            ->getRecordSpec($recordSpecName ?: $this->recognizer->recognize($line, $this->spec))
            ->getFieldSpec($fieldName)
        ;

        return $this->formatter->formatFromFile($fieldSpec ,$line[$fieldSpec->getSlice()]);
    }

    public function parseLine($lineIndex, $recordSpecName = null)
    {
        $line = $this->file[$lineIndex];

        $fieldSpecs = $this->spec
            ->getRecordSpec($recordSpecName ?: $this->recognizer->recognize($line, $this->spec))
            ->getFieldSpecs()
        ;

        $formatter = $this->formatter;

        return array_map(function(FieldSpec $fieldSpec) use ($line, $formatter)
        {
            return $formatter->formatFromFile($fieldSpec ,$line[$fieldSpec->getSlice()]);
        }, $fieldSpecs);
    }

    /**
     * @param $lineIndex
     * @return string
     */
    public function getRecordSpecName($lineIndex)
    {
        return $this->recognizer->recognize($this->file[$lineIndex], $this->spec);
    }
}