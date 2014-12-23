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
    /** @var FileInterface  */
    protected $file;
    protected $spec;
    protected $formatter;

    /**
     * @param FileInterface|string $nameOrFile
     * @param FileSpec $spec
     * @param ValueFormatterInterface $formatter
     */
    public function __construct($nameOrFile, FileSpec $spec, ValueFormatterInterface $formatter)
    {
        $this->spec = $spec;
        
        if (!$nameOrFile instanceof FileInterface) {
            
            $nameOrFile = new File(
                $nameOrFile,
                $spec->getWidth(),
                array(),
                $this->spec->getLineSeparator()
            );
        }
        
        $this->file = $nameOrFile;
        $this->formatter = $formatter;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function addRecord($recordSpecName, array $data)
    {
        $recordSpec = $this->spec->getRecordSpec($recordSpecName);
        $line = new Line($this->file->getWidth());

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
        
        $this->file->addLine($line);

        return $this;
    }
}