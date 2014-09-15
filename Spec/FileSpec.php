<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/3/14
 * Time: 9:58 PM
 */

namespace Giftcards\FixedWidth\Spec;


class FileSpec
{
    /** @var  RecordSpec[] */
    protected $recordSpecs;
    protected $name;
    protected $width;

    public function __construct($name, array $recordSpecs, $width)
    {
        $this->recordSpecs = $recordSpecs;
        $this->name = $name;
        $this->width = (int)$width;
    }

    /**
     * @param string $name
     * @throws SpecNotFoundException
     * @return RecordSpec
     */
    public function getRecordSpec($name)
    {
        if (!isset($this->recordSpecs[$name])) {

            throw new SpecNotFoundException($name, 'record');
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