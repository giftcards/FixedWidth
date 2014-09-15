<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/3/14
 * Time: 10:11 PM
 */

namespace Giftcards\FixedWidth\Spec\Loader;


use Giftcards\FixedWidth\Slice;
use Giftcards\FixedWidth\Spec\FileSpec;
use Giftcards\FixedWidth\Spec\RecordSpec;
use Giftcards\FixedWidth\Spec\FieldSpec;
use Giftcards\FixedWidth\Spec\SpecNotFoundException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArraySpecLoader implements SpecLoaderInterface
{
    protected $arraySpecs = array();
    protected $initializedSpecs = array();

    public function __construct(array $specs)
    {
        $this->arraySpecs = $specs;
    }

    public function loadSpec($specName)
    {
        if (!isset($this->initializedSpecs[$specName])) {

            $this->initializeSpec($specName);
        }

        return $this->initializedSpecs[$specName];
    }

    protected function initializeSpec($specName)
    {
        if (!isset($this->arraySpecs[$specName])) {

            throw new SpecNotFoundException($specName, 'file');
        }

        $spec = $this->arraySpecs[$specName];

        $fileOptionsResolver = new OptionsResolver();
        $fileOptionsResolver
            ->setRequired(array('width'))
            ->setDefaults(array('field_types' => array(), 'record_types' => array()))
            ->setAllowedTypes(array('field_types' => 'array', 'record_types' => 'array'))
        ;
        $fieldTypeOptionsResolver = new OptionsResolver();
        $fieldTypeOptionsResolver
            ->setDefaults(array(
                'padding_direction' => FieldSpec::PADDING_DIRECTION_LEFT,
                'padding_char' => '',
                'format_specifier' => 's',
            ))
            ->setAllowedTypes(array(
                'padding_char' => 'scalar',
                'format_specifier' => 'string'
            ))
            ->setAllowedValues(array(
                'padding_direction' => array(
                    FieldSpec::PADDING_DIRECTION_LEFT,
                    FieldSpec::PADDING_DIRECTION_RIGHT
                )
            ))
        ;


        $spec = $fileOptionsResolver->resolve($spec);

        $spec['field_types'] = array_map(function($fieldType) use ($fieldTypeOptionsResolver)
        {
            return $fieldTypeOptionsResolver->resolve($fieldType);
        }, $spec['field_types']);

        $fieldOptionsResolver = clone $fieldTypeOptionsResolver;
        $fieldOptionsResolver
            ->setRequired(array('type', 'slice'))
            ->setDefaults(array('default' => null))
            ->setAllowedValues(array('type' => array_keys($spec['field_types'])))
        ;

        $lineSpecs = array();

        foreach ($spec['record_types'] as $name => $lineType) {

            $fieldSpecs = array();

            foreach ($lineType as $fieldName => $options) {

                $options = $fieldOptionsResolver->resolve(array_merge(
                    isset($spec['field_types'][$options['type']]) ? $spec['field_types'][$options['type']] : array(),
                    $options
                ));
                $fieldSpecs[$fieldName] = new FieldSpec(
                    $fieldName,
                    Slice::createFromString($options['slice']),
                    $options['default'],
                    $options['format_specifier'],
                    $options['padding_char'],
                    $options['padding_direction'],
                    $options['type']
                );
            }

            $lineSpecs[$name] = new RecordSpec($name, $fieldSpecs);
        }

        $this->initializedSpecs[$specName] = new FileSpec(
            $specName,
            $lineSpecs,
            $spec['width']
        );
    }
}