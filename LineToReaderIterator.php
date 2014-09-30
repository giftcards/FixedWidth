<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/22/14
 * Time: 9:57 PM
 */

namespace Giftcards\FixedWidth;

use Giftcards\FixedWidth\Spec\FileSpec;
use Giftcards\FixedWidth\Spec\Recognizer\RecordSpecRecognizerInterface;
use Giftcards\FixedWidth\Spec\ValueFormatter\ValueFormatterInterface;

class LineToReaderIterator extends \IteratorIterator
{
    protected $spec;
    protected $recognizer;
    protected $formatter;

    public function __construct(
        FileInterface $file,
        FileSpec $spec,
        RecordSpecRecognizerInterface $recognizer,
        ValueFormatterInterface $formatter
    ) {
        parent::__construct($file);
        $this->spec = $spec;
        $this->recognizer = $recognizer;
        $this->formatter = $formatter;
    }

    public function current()
    {
        $line = parent::current();
        return new LineReader(
            $line,
            $this->spec->getRecordSpec($this->recognizer->recognize($line, $this->spec)),
            $this->formatter
        );
    }
}