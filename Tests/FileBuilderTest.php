<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/11/14
 * Time: 12:57 PM
 */

namespace Giftcards\FixedWidth\Tests;


use Giftcards\FixedWidth\FileBuilder;
use Giftcards\FixedWidth\Spec\FieldSpec;
use Giftcards\FixedWidth\Spec\FileSpec;
use Giftcards\FixedWidth\Spec\RecordSpec;

class FileBuilderTest extends TestCase
{
    /** @var  FileBuilder */
    protected $builder;
    protected $fileName;
    protected $spec;
    protected $recordSpec1;
    protected $recordSpec1Name;
    protected $recordSpec2;
    protected $recordSpec2Name;

    public function setUp()
    {
        $field1Name = $this->getFaker()->word;
        $field2Name = $this->getFaker()->word;
        $this->recordSpec1 = new RecordSpec(
            $this->recordSpec1Name = $this->getFaker()->word,
            array(
                $field1Name => new FieldSpec($this->getFaker()->word, $this->getFaker()->word, $field1Name, '12:15', '', 'right', 'string'),
                $field2Name => new FieldSpec(null, $this->getFaker()->word, $field2Name, '17:25', '0', 'left', 'integer')
            )
        );
        $field1Name = $this->getFaker()->word;
        $field2Name = $this->getFaker()->word;
        $this->recordSpec2 = new RecordSpec(
            $this->recordSpec2Name = $this->getFaker()->word,
            array(
                $field1Name => new FieldSpec(null, $this->getFaker()->word, $field1Name, '15:19', '', 'right', 'string'),
                $field2Name => new FieldSpec($this->getFaker()->word, $this->getFaker()->word, $field2Name, '21:26', '0', 'left', 'integer')
            )
        );
        $this->builder = new FileBuilder(
            $this->fileName = $this->getFaker()->word,
            $this->spec = new FileSpec(
                array(new RecordSpec($this->getFaker()->word, array())),
                $this->getFaker()->word,
                30
            )
        );
    }

    public function testAddRecord()
    {
        $this->builder->addRecord($this->recordSpec1Name, array(
            ''
        ));
    }
}
 