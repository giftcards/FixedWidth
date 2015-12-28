<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/10/14
 * Time: 5:57 PM
 */

namespace Giftcards\FixedWidth\Tests;


use Giftcards\FixedWidth\FileIterator;
use Giftcards\FixedWidth\FileSystemFile;
use Giftcards\FixedWidth\FileSystemLine;
use Giftcards\FixedWidth\Line;

class FileSystemFileTest extends TestCase
{
    /** @var  FileSystemFile */
    protected $file;
    protected $width;
    /** @var  FileSystemLine */
    protected $line1;
    /** @var  FileSystemLine */
    protected $line2;
    protected $name;

    public function setUp()
    {
        $this->width = $this->getFaker()->numberBetween(10, 15);
        $this->line1 = str_repeat('w', $this->width);
        $this->line2 = new Line($this->width);
        $this->name = 'lazy_file_'.$this->getFaker()->word.'.txt';
        $this->file = new FileSystemFile(
            $this->width,
            new \SplFileObject(__DIR__.'/Fixtures/'.$this->name, 'w+')
        );
        $this->file->addLine($this->line1)->addLine($this->line2);
    }

    public function testGettersSetters()
    {
        $this->assertEquals($this->width, $this->file->getWidth());
        $this->assertEquals("\r\n", $this->file->getLineSeparator());
        $this->assertEquals(
            array(
                new FileSystemLine($this->file->getFileObject(), 0, $this->width),
                new FileSystemLine($this->file->getFileObject(), $this->width + 2, $this->width),
            ),
            $this->file->getLines()
        );

        $this->assertEquals($this->line1."\r\n".$this->line2."\r\n", (string)$this->file);
        $this->assertTrue(isset($this->file[0]));
        $this->assertTrue(isset($this->file[1]));
        $this->assertFalse(isset($this->file[2]));
        $this->assertEquals($this->line1, $this->file[0]);
        $this->assertEquals((string)$this->line2, $this->file[1]);
        $this->assertCount(2, $this->file);
        $line3 = new Line($this->width);
        $line4 = str_repeat('x', $this->width);
        $line5 = new Line($this->width);
        $this->file[] = $line3;
        $this->file[] = $line4;
        $this->file[1] = $line5;
        $this->assertEquals(array(
            (string)$this->line1,
            (string)$line5,
            (string)$line3,
            (string)$line4
        ), $this->file->getLines());
        $this->assertSame($this->file, $this->file->addLine($this->line2));
        $this->assertEquals(array(
            (string)$this->line1,
            (string)$line5,
            (string)$line3,
            (string)$line4,
            (string)$this->line2
        ), $this->file->getLines());
        $this->assertSame($this->file, $this->file->setLine(0, $line4));
        $this->assertEquals(array(
            (string)$line4,
            (string)$line5,
            (string)$line3,
            (string)$line4,
            (string)$this->line2
        ), $this->file->getLines());
        $this->assertEquals($this->name, $this->file->getName());
        $this->assertEquals(new FileIterator($this->file), $this->file->getIterator());
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructionWithUnevenFile()
    {
        new FileSystemFile(10, new \SplFileObject(__DIR__.'/Fixtures/fixed_width_uneven.txt'), "\n");
    }

    public function testFileWithoutTrailingLineEnding()
    {
        $fileObject = new \SplFileObject(__DIR__.'/Fixtures/'.$this->getFaker()->word.'.txt', 'w+');
        $fileObject->fwrite('line');
        $file = new FileSystemFile(4, $fileObject, "\n");
        $file->addLine('line');
        $this->assertEquals("line\nline", $file);
        $this->assertEquals(file_get_contents($fileObject->getRealPath()), $file);
        unlink($fileObject->getRealPath());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testRemoveLine()
    {
        $this->file->removeLine(0);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testUnsetLine()
    {
        unset($this->file[0]);
    }

    public function tearDown()
    {
        unlink(__DIR__.'/Fixtures/'.$this->name);
    }
}