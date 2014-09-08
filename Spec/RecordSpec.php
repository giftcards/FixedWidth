<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/3/14
 * Time: 9:58 PM
 */

namespace Giftcards\FixedWidth\Spec;


use Giftcards\FixedWidth\Spec\SpecNotFoundException;

class RecordSpec
{
    protected $name;
    /** @var FieldSpec[] */
    protected $fieldSpecs;

    public function __construct($name, array $fieldSpecs)
    {
        $this->name = $name;
        $this->fieldSpecs = $fieldSpecs;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @throws SpecNotFoundException
     * @return FieldSpec
     */
    public function getFieldSpec($name)
    {
        if (!isset($this->fieldSpecs[$name])) {

            throw new SpecNotFoundException($name, 'field');
        }

        return $this->fieldSpecs[$name];
    }

    public function getFieldSpecs()
    {
        return $this->fieldSpecs;
    }
}