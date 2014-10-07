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
use Giftcards\FixedWidth\Spec\ValueFormatter\SprintfValueFormatter;
use Giftcards\FixedWidth\Spec\ValueFormatter\ValueFormatterInterface;

class FileFactory
{
    public function create($name, $width, $lineSeparator = "\r\n")
    {
        return new File($name, $width, array(), $lineSeparator);
    }

    public function createFromFile(\SplFileInfo $file, $lineSeparator = "\r\n")
    {
        return $this->createFromData(
            file_get_contents($file->getRealPath()),
            $file->getFilename(),
            $lineSeparator
        );
    }

    public function createFromData($data, $name, $lineSeparator = "\r\n")
    {
        $lines = explode($lineSeparator, $data);

        if (!($width = strlen($lines[0]))) {

            throw new \InvalidArgumentException(
                'The data you\'ve passed is empty and therefore the width cannot be inferred.'
            );
        }

        //if a file had a trailing line ending remove the last line since it will make the file instance throw an exception
        if (strlen(end($lines)) == 0) {

            array_pop($lines);
        }

        return new File(
            $name,
            $width,
            $lines
        );
    }
} 