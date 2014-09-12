<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/4/14
 * Time: 10:10 PM
 */

namespace Giftcards\FixedWidth;


use Giftcards\FixedWidth\Spec\Loader\SpecLoaderInterface;
use Giftcards\FixedWidth\Spec\Recognizer\RecordSpecRecognizerInterface;

class FileFactory
{
    protected $specLoader;
    protected $recordSpecRecognizers = array();

    public function __construct(SpecLoaderInterface $specLoader)
    {
        $this->specLoader = $specLoader;
    }

    public function addRecordSpecRecognizer(
        $specName,
        RecordSpecRecognizerInterface $recognizer
    ) {
        $this->recordSpecRecognizers[$specName] = $recognizer;
        return $this;
    }

    /**
     * @return RecordSpecRecognizerInterface[]
     */
    public function getRecordSpecRecognizers()
    {
        return $this->recordSpecRecognizers;
    }

    public function create($name, $width)
    {
        return new File($name, $width);
    }

    public function createBuilder($name, $specName)
    {
        return new FileBuilder($name, $this->specLoader->loadSpec($specName));
    }

    public function createFromFile(\SplFileInfo $file)
    {
        $lines = explode("\r\n", file_get_contents($file->getRealPath()));

        if (!($width = strlen($lines[0]))) {

            throw new \InvalidArgumentException('The file you\'ve passed is empty and therefore the width cannot be inferred.');
        }

        return new File(
            $file->getFilename(),
            $width,
            $lines
        );
    }

    public function createReader(File $file, $specName)
    {
        return new FileReader(
            $file,
            $this->specLoader->loadSpec($specName),
            isset($this->recordSpecRecognizers[$specName]) ? $this->recordSpecRecognizers[$specName] : null
        );
    }
} 