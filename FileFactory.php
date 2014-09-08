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
use Symfony\Component\Finder\SplFileInfo;

class FileFactory
{
    protected $specLoader;
    protected $recordSpecRecognizers = [];

    public function __construct(SpecLoaderInterface $specLoader)
    {
        $this->specLoader = $specLoader;
    }

    public function addRecordSpecRecognizer($specName, RecordSpecRecognizerInterface $recognizer)
    {
        $this->recordSpecRecognizers[$specName] = $recognizer;
        return $this;
    }

    public function create($name, $width)
    {
        return new File($name, $width);
    }

    public function createFileBuilder($name, $specName)
    {
        return new FileBuilder($name, $this->specLoader->loadSpec($specName));
    }

    public function createFromFile(SplFileInfo $file, $specName)
    {
        return new File(
            $file->getFilename(),
            $this->specLoader->loadSpec($specName)->getWidth(),
            explode("\r\n", $file->getContents())
        );
    }

    public function createFileReader(File $file, $specName)
    {
        return new FileReader(
            $file,
            $this->specLoader->loadSpec($specName),
            isset($this->recordSpecRecognizers[$specName]) ? $this->recordSpecRecognizers[$specName] : null
        );
    }
} 