<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/4/14
 * Time: 3:36 PM
 */

namespace Giftcards\FixedWidth;


use Giftcards\FixedWidth\Spec\FileSpec;
use Giftcards\FixedWidth\Spec\ValueFormatter\ValueFormatterInterface;

class FileBuilder
{
    protected $file;
    protected $spec;
    protected $formatter;

    public function __construct($name, FileSpec $spec, ValueFormatterInterface $formatter)
    {
        $this->spec = $spec;
        $this->file = new File($name, $spec->getWidth());
        $this->formatter = $formatter;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function addRecord($recordSpecName, array $data)
    {
        $recordSpec = $this->spec->getRecordSpec($recordSpecName);
        $this->file[] = $line = new Line($this->spec->getWidth());

        foreach ($recordSpec->getFieldSpecs() as $name => $fieldSpec) {

            $value = isset($data[$name]) ? $data[$name] : $fieldSpec->getDefault();

            if (is_null($value)) {

                throw new FieldRequiredException($recordSpecName, $fieldSpec);
            }

            $line[$fieldSpec->getSlice()] = $this->formatter->formatToFile(
                $fieldSpec,
                $value
            );
        }

        return $this;
    }
}