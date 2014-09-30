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
use Traversable;

class FileReader implements \IteratorAggregate, \ArrayAccess, \Countable
{
    protected $file;
    protected $spec;
    protected $formatter;
    protected $recognizer;

    public function __construct(
        FileInterface $file,
        FileSpec $spec,
        ValueFormatterInterface $formatter,
        RecordSpecRecognizerInterface $recognizer = null
    ) {
        $this->spec = $spec;
        $this->file = $file;
        $this->formatter = $formatter;
        $this->recognizer = $recognizer ?: new FailedRecognizer();
    }

    /**
     * @return FileInterface
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param $lineIndex
     * @param $fieldName
     * @param null $recordSpecName
     * @return mixed
     */
    public function parseField($lineIndex, $fieldName, $recordSpecName = null)
    {
        return $this->getLineReader($lineIndex, $recordSpecName)->getField($fieldName);
    }

    /**
     * @param $lineIndex
     * @param null $recordSpecName
     * @return array
     */
    public function parseLine($lineIndex, $recordSpecName = null)
    {
        return $this->getLineReader($lineIndex, $recordSpecName)->getFields();
    }

    public function getLineReader($lineIndex, $recordSpecName = null)
    {
        $line = $this->file->getLine($lineIndex);

        $recordSpec = $this->spec
            ->getRecordSpec($recordSpecName ?: $this->recognizer->recognize($line, $this->spec))
        ;

        return new LineReader($line, $recordSpec, $this->formatter);
    }

    /**
     * @param $lineIndex
     * @return string
     */
    public function getRecordSpecName($lineIndex)
    {
        return $this->recognizer->recognize($this->file->getLine($lineIndex), $this->spec);
    }

    /**
     * @return LineToReaderIterator
     */
    public function getIterator()
    {
        return new LineToReaderIterator(
            $this->file,
            $this->spec,
            $this->recognizer,
            $this->formatter
        );
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->file[$offset]);
    }

    /**
     * @param mixed $offset
     * @return LineReader
     */
    public function offsetGet($offset)
    {
        return $this->getLineReader($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('a file reader is read only.');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('a file reader is read only.');
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->file);
    }
}