<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/14
 * Time: 5:34 PM
 */

namespace Giftcards\FixedWidth\Tests\Spec\Loader;


use Giftcards\FixedWidth\Slice;
use Giftcards\FixedWidth\Spec\FieldSpec;
use Giftcards\FixedWidth\Spec\FileSpec;
use Giftcards\FixedWidth\Spec\Loader\ArraySpecLoader;
use Giftcards\FixedWidth\Spec\RecordSpec;
use Giftcards\FixedWidth\Tests\TestCase;

class ArraySpecLoaderTest extends TestCase
{
    /** @var  ArraySpecLoader */
    protected $loader;

    public function setUp()
    {
        $this->loader = new ArraySpecLoader(array(
            'spec1' => array(
                'width' => 78,
                'field_types' => array(
                    'string' => array(
                        'format_specifier' => 's',
                        'padding_char' => 'w',
                        'padding_direction' => FieldSpec::PADDING_DIRECTION_RIGHT
                    ),
                    'integer' => array()
                ),
                'record_types' => array(
                    'record1' => array(
                        'field1' => array(
                            'type' => 'string',
                            'default' => 23.34,
                            'slice' => '34:39',
                            'format_specifier' => '.2f',
                            'padding_char' => 'x',
                            'padding_direction' => FieldSpec::PADDING_DIRECTION_LEFT
                        ),
                        'field2' => array(
                            'type' => 'string',
                            'slice' => '40:42',
                        ),
                    ),
                    'record2' => array(
                        'field3' => array(
                            'type' => 'integer',
                            'slice' => '34:56',
                        ),
                    ),
                )
            ),
            'bad_field_type_spec1' => array(
                'width' => 78,
                'field_types' => array(
                    'integer' => array()
                ),
                'record_types' => array(
                    'record1' => array(
                        'field1' => array(
                            'type' => 'string',
                            'slice' => '34:36',
                        ),
                    ),
                )
            )
        ));
    }

    public function testLoadWhereFound()
    {
        $field1Spec = new FieldSpec('field1', Slice::createFromString('34:39'), 23.34, '.2f', 'x', FieldSpec::PADDING_DIRECTION_LEFT, 'string');
        $field2Spec = new FieldSpec('field2', Slice::createFromString('40:42'), null, 's', 'w', FieldSpec::PADDING_DIRECTION_RIGHT, 'string');
        $field3Spec = new FieldSpec('field3', Slice::createFromString('34:56'), null, 's', '', FieldSpec::PADDING_DIRECTION_LEFT, 'integer');
        $spec = new FileSpec(
            'spec1',
            array(
                'record1' => new RecordSpec('record1', array(
                        'field1' => $field1Spec,
                        'field2' => $field2Spec
                    )),
                'record2' => new RecordSpec('record2', array('field3' => $field3Spec))
            ),
            78,
            "\r\n"
        );

        $this->assertEquals($spec, $this->loader->loadSpec('spec1'));
        $this->assertEquals($spec, $this->loader->loadSpec('spec1'));
    }

    /**
     * @expectedException \Giftcards\FixedWidth\Spec\SpecNotFoundException
     */
    public function testLoadWhereSpecNotFound()
    {
        $this->loader->loadSpec('spec2');
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testLoadWhereFieldTypeIsNotDefined()
    {
        $this->loader->loadSpec('bad_field_type_spec1');
    }
}
 