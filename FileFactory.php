<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/4/14
 * Time: 10:10 PM
 */

namespace Giftcards\FixedWidth;


class FileFactory
{
    public function create($name, $width, $lineSeparator = "\r\n")
    {
        return new InMemoryFile($name, $width, array(), $lineSeparator);
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

        return new InMemoryFile(
            $name,
            $width,
            $lines,
            $lineSeparator
        );
    }
} 