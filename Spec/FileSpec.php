<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/3/14
 * Time: 9:58 PM
 */

namespace Giftcards\FixedWidth\Spec;


use Giftcards\FixedWidth\Spec\RecordSpec;
use Giftcards\FixedWidth\Spec\SpecNotFoundException;

class FileSpec
{
    /** @var  RecordSpec[] */
    protected $recordSpecs;
    protected $name;
    protected $width;

    public function __construct($lineSpecs, $name, $width)
    {
        $this->recordSpecs = $lineSpecs;
        $this->name = $name;
        $this->width = $width;
    }

    /**
     * @param string $name
     * @throws SpecNotFoundException
     * @return RecordSpec
     */
    public function getRecordSpec($name)
    {
        if (!isset($this->recordSpecs[$name])) {

            throw new SpecNotFoundException($name, 'line');
        }

        return $this->recordSpecs[$name];
    }

    public function getRecordSpecs()
    {
        return $this->recordSpecs;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getWidth()
    {
        return $this->width;
    }
} 