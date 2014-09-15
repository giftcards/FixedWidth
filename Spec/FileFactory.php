<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/15/14
 * Time: 4:56 PM
 */

namespace Giftcards\FixedWidth\Spec;


use Giftcards\FixedWidth\File;
use Giftcards\FixedWidth\FileBuilder;
use Giftcards\FixedWidth\FileFactory as BaseFileFactory;
use Giftcards\FixedWidth\FileReader;
use Giftcards\FixedWidth\Spec\Loader\SpecLoaderInterface;
use Giftcards\FixedWidth\Spec\Recognizer\RecordSpecRecognizerInterface;
use Giftcards\FixedWidth\Spec\ValueFormatter\SprintfValueFormatter;
use Giftcards\FixedWidth\Spec\ValueFormatter\ValueFormatterInterface;

class FileFactory extends BaseFileFactory
{
    protected $specLoader;
    protected $formatter;
    protected $recordSpecRecognizers = array();

    public function __construct(SpecLoaderInterface $specLoader, ValueFormatterInterface $formatter = null)
    {
        $this->specLoader = $specLoader;
        $this->formatter = $formatter ?: new SprintfValueFormatter();
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

    public function createBuilder($name, $specName)
    {
        return new FileBuilder($name, $this->specLoader->loadSpec($specName), $this->formatter);
    }

    public function createReader(File $file, $specName)
    {
        return new FileReader(
            $file,
            $this->specLoader->loadSpec($specName),
            $this->formatter,
            isset($this->recordSpecRecognizers[$specName]) ? $this->recordSpecRecognizers[$specName] : null
        );
    }

} 