<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/3/14
 * Time: 10:11 PM
 */

namespace Giftcards\FixedWidth\Spec\Loader;


use Giftcards\FixedWidth\Spec\FileSpec;
use Giftcards\FixedWidth\Spec\RecordSpec;
use Giftcards\FixedWidth\Spec\FieldSpec;
use Giftcards\FixedWidth\Spec\SpecNotFoundException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArraySpecLoader implements SpecLoaderInterface
{
    protected $arraySpecs = [];
    protected $initializedSpecs = [];

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
            ->setRequired(['width'])
            ->setDefaults(['field_types' => [], 'record_types' => []])
            ->setAllowedTypes(['field_types' => 'array', 'record_types' => 'array'])
        ;
        $fieldTypeOptionsResolver = new OptionsResolver();
        $fieldTypeOptionsResolver
            ->setDefaults([
                    'padding_direction' => FieldSpec::PADDING_DIRECTION_LEFT,
                    'padding_char' => '',
                    'format_specifier' => 's',
                ])
            ->setAllowedTypes([
                    'padding_char' => 'scalar',
                    'format_specifier' => 'string'
                ])
            ->setAllowedValues([
                    'padding_direction' => [
                        FieldSpec::PADDING_DIRECTION_LEFT,
                        FieldSpec::PADDING_DIRECTION_RIGHT
                    ]
                ])
        ;


        $spec = $fileOptionsResolver->resolve($spec);

        $spec['field_types'] = array_map(function($fieldType) use ($fieldTypeOptionsResolver)
        {
            return $fieldTypeOptionsResolver->resolve($fieldType);
        }, $spec['field_types']);

        $fieldOptionsResolver = clone $fieldTypeOptionsResolver;
        $fieldOptionsResolver
            ->setRequired(['type', 'slice'])
            ->setDefaults(['default' => null])
            ->setAllowedValues(['type' => array_keys($spec['field_types'])])
        ;

        $lineSpecs = [];

        foreach ($spec['record_types'] as $name => $lineType) {

            $fieldSpecs = [];

            foreach ($lineType as $fieldName => $options) {

                $options = $fieldOptionsResolver->resolve(array_merge(
                    isset($spec['field_types'][$options['type']]) ? $spec['field_types'][$options['type']] : array(),
                    $options
                ));
                $fieldSpecs[$fieldName] = new FieldSpec(
                    $options['default'],
                    $options['format_specifier'],
                    $fieldName,
                    $options['slice'],
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