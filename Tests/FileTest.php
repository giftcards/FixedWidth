<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/10/14
 * Time: 5:57 PM
 */

namespace Giftcards\FixedWidth\Tests;


use Giftcards\FixedWidth\File;
use Giftcards\FixedWidth\Line;

class FileTest extends TestCase
{
    /** @var  File */
    protected $file;
    protected $width;
    /** @var  Line */
    protected $line1;
    /** @var  Line */
    protected $line2;
    protected $name;

    public function setUp()
    {
        $this->width = $this->getFaker()->numberBetween(10, 15);
        $this->line1 = str_repeat('w', $this->width);
        $this->line2 = new Line($this->width);
        $this->name = $this->getFaker()->word;
        $this->file = new File(
            $this->name,
            $this->width,
            array($this->line1, $this->line2)
        );
    }

    public function testGettersSetters()
    {
        $this->assertEquals($this->width, $this->file->getWidth());
        $this->assertEquals("\r\n", $this->file->getLineSeparator());
        $this->assertEquals(
            array(new Line($this->line1), $this->line2),
            $this->file->getLines()
        );
        $this->assertSame($this->line2, $this->file->getLine(1));
        $this->assertEquals($this->line1."\r\n".$this->line2, $this->file);
        $this->assertTrue(isset($this->file[0]));
        $this->assertTrue(isset($this->file[1]));
        $this->assertFalse(isset($this->file[2]));
        $this->assertEquals($this->line1, $this->file[0]);
        $this->assertSame($this->line2, $this->file[1]);
        $this->assertCount(2, $this->file);
        $line3 = new Line($this->width);
        $line4 = str_repeat('x', $this->width);
        $line5 = new Line($this->width);
        $this->file[] = $line3;
        $this->file[] = $line4;
        $this->file[1] = $line5;
        $this->assertEquals(array(
            $this->line1,
            $line5,
            $line3,
            $line4
        ), $this->file->getLines());
        $this->assertSame($this->file, $this->file->removeLine(2));
        $this->assertEquals(array(
            new Line($this->line1),
            $line5,
            new Line($line4)
        ), $this->file->getLines());
        unset($this->file[2]);
        $this->assertEquals(array(
            new Line($this->line1),
            $line5
        ), $this->file->getLines());
        $this->assertSame($this->file, $this->file->addLine($this->line2));
        $this->assertEquals(array(
            new Line($this->line1),
            $line5,
            $this->line2
        ), $this->file->getLines());
        $this->assertSame($this->file, $this->file->setLine(0, $line4));
        $this->assertEquals(array(
            $line4,
            $line5,
            $this->line2
        ), $this->file->getLines());
        $this->assertEquals($this->name, $this->file->getName());
        $this->assertEquals(new \ArrayIterator(array(
            $line4,
            $line5,
            $this->line2
        )), $this->file->getIterator());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddLineWhereLengthIsWrong()
    {
        $this->file->addLine(new Line($this->width - 1));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetLineWhereLengthIsWrong()
    {
        $this->file->setLine(1, new Line($this->width - 1));
    }


    /**
     * @expectedException \OutOfBoundsException
     */
    public function testSetLineWhereOutOfBounds()
    {
        $this->file->setLine(4, new Line($this->width));
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetLineWhereOutOfBounds()
    {
        $this->file->getLine(5);
    }
}
 