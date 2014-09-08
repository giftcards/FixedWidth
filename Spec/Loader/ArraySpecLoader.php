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
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArraySpecLoader implements SpecLoaderInterface
{
    protected $specs = [];

    public function __construct(array $specs)
    {
        $this->specs = $specs;
    }

    public function loadSpec($name)
    {
        if (!isset($this->specs[$name])) {

            throw new SpecNotFoundException($name, 'file');
        }

        $spec = $this->specs[$name];

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
                'format_type' => 's',
            ])
            ->setAllowedTypes([
                'padding_char' => 'scalar',
                'format_type' => 'string'
            ])
            ->setAllowedValues([
                'padding_direction' => [
                    FieldSpec::PADDING_DIRECTION_LEFT,
                    FieldSpec::PADDING_DIRECTION_RIGHT
                ]
            ])
        ;

        $fieldOptionsResolver = clone $fieldTypeOptionsResolver;
        $fieldOptionsResolver
            ->setRequired(['type', 'slice'])
            ->setDefaults(['default' => null])
        ;

        $spec = $fileOptionsResolver->resolve($spec);

        $spec['field_types'] = array_map(function($fieldType) use ($fieldTypeOptionsResolver)
        {
            return $fieldTypeOptionsResolver->resolve($fieldType);
        }, $spec['field_types']);

        $fieldOptionsResolver->setAllowedValues(['type' => array_keys($spec['field_types'])]);

        $lineSpecs = [];

        foreach ($spec['record_types'] as $name => $lineType) {

            $fieldSpecs = [];

            foreach ($lineType as $fieldName => $options) {

                $options = $fieldOptionsResolver->resolve(array_merge(
                    $spec['field_types'][$options['type']],
                    $options
                ));
                $fieldSpecs[$fieldName] = new FieldSpec(
                    $options['default'],
                    $options['format_type'],
                    $fieldName,
                    $options['slice'],
                    $options['padding_char'],
                    $options['padding_direction'],
                    $options['type']
                );
            }

            $lineSpecs[$name] = new RecordSpec($name, $fieldSpecs);
        }

        $fileSpec = new FileSpec($lineSpecs, $name, $spec['width']);
        return $fileSpec;
    }
}