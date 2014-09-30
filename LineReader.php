<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/22/14
 * Time: 9:48 PM
 */

namespace Giftcards\FixedWidth;

use Giftcards\FixedWidth\Spec\FieldSpec;
use Giftcards\FixedWidth\Spec\RecordSpec;
use Giftcards\FixedWidth\Spec\SpecNotFoundException;
use Giftcards\FixedWidth\Spec\ValueFormatter\ValueFormatterInterface;

class LineReader implements \ArrayAccess
{
    protected $spec;
    protected $line;
    protected $formatter;

    public function __construct(
        LineInterface $line,
        RecordSpec $spec,
        ValueFormatterInterface $formatter
    ) {
        $this->line = $line;
        $this->spec = $spec;
        $this->formatter = $formatter;
    }

    /**
     * @param $fieldName
     * @return mixed
     */
    public function getField($fieldName)
    {
        $fieldSpec = $this->spec->getFieldSpec($fieldName);
        return $this->formatter->formatFromFile($fieldSpec, $this->line->get($fieldSpec->getSlice()));
    }

    /**
     * @return array
     */
    public function getFields()
    {
        $fieldSpecs = $this->spec->getFieldSpecs();
        $formatter = $this->formatter;
        $line = $this->line;

        return array_map(function(FieldSpec $fieldSpec) use ($line, $formatter)
        {
            return $formatter->formatFromFile($fieldSpec, $line->get($fieldSpec->getSlice()));
        }, $fieldSpecs);
    }

    /**
     * @return RecordSpec
     */
    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * @return Line
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        try {
            $this->getField($offset);
            return true;
        } catch(SpecNotFoundException $e) {
            return false;
        }
    }

    /**
     * @param mixed $offset
     * @return array|mixed
     */
    public function offsetGet($offset)
    {
        return $this->getFields($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('a line reader is read only.');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('a line reader is read only.');
    }
}