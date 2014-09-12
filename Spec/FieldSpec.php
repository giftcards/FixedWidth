<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/3/14
 * Time: 9:59 PM
 */

namespace Giftcards\FixedWidth\Spec;


class FieldSpec
{
    const PADDING_DIRECTION_LEFT = 'left';
    const PADDING_DIRECTION_RIGHT = 'right';

    protected $name;
    protected $type;
    protected $slice;
    protected $paddingDirection;
    protected $paddingChar;
    protected $formatSpecifier;
    protected $default;

    public function __construct(
        $default,
        $formatSpecifier,
        $name,
        $slice,
        $paddingChar,
        $paddingDirection,
        $type
    ) {
        $this->default = $default;
        $this->formatSpecifier = $formatSpecifier;
        $this->name = $name;
        $this->paddingChar = $paddingChar;
        $this->paddingDirection = $paddingDirection;
        $this->type = $type;
        $this->slice = $slice;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return string
     */
    public function getFormatSpecifier()
    {
        return $this->formatSpecifier;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPaddingChar()
    {
        return $this->paddingChar;
    }

    /**
     * @return string
     */
    public function getPaddingDirection()
    {
        return $this->paddingDirection;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getSlice()
    {
        return $this->slice;
    }
}