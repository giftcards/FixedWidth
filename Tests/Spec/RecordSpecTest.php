<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/12/14
 * Time: 5:39 PM
 */

namespace Giftcards\FixedWidth\Tests\Spec;


use Giftcards\FixedWidth\Spec\RecordSpec;
use Giftcards\FixedWidth\Tests\TestCase;

class RecordSpecTest extends TestCase
{

    public function tearDown()
    {
        \Mockery::close();
    }

    public function testGetters()
    {
        $name = $this->getFaker()->word;
        $fieldSpec1 = \Mockery::mock('Giftcards\FixedWidth\Spec\FieldSpec');
        $fieldSpec2 = \Mockery::mock('Giftcards\FixedWidth\Spec\FieldSpec');
        $fieldSpecs = array(
            'field1' => $fieldSpec1,
            'field2' => $fieldSpec2,
        );
        $spec = new RecordSpec($name, $fieldSpecs);
        $this->assertEquals($name, $spec->getName());
        $this->assertSame($fieldSpecs, $spec->getFieldSpecs());
        $this->assertSame($fieldSpec1, $spec->getFieldSpec('field1'));
        $this->assertSame($fieldSpec2, $spec->getFieldSpec('field2'));
    }

    /**
     * @expectedException \Giftcards\FixedWidth\Spec\SpecNotFoundException
     */
    public function testGetFieldSpecWhereNotThere()
    {
        $spec = new RecordSpec($this->getFaker()->word, array());
        $spec->getFieldSpec('field1');

    }
}
