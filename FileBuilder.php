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
use Giftcards\FixedWidth\Spec\Loader\SpecLoaderInterface;
use Symfony\Component\Finder\SplFileInfo;

class FileBuilder
{
    protected $file;
    protected $spec;

    public function __construct($name, FileSpec $spec)
    {
        $this->spec = $spec;
        $this->file = new File($name, $spec->getWidth());
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

            $slice = $fieldSpec->getSlice();
            $range = explode(':', $slice);

            $line[$slice] = sprintf(
                sprintf(
                    '%%%s%s%s%s',
                    $fieldSpec->getPaddingChar(),
                    $fieldSpec->getPaddingDirection() == FieldSpec::PADDING_DIRECTION_LEFT ? '' : '-',
                    $range[1] - $range[0],
                    $fieldSpec->getFormatType()
                ),
                $value
            );
        }

        return $this;
    }
}