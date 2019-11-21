<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 12/23/14
 * Time: 4:07 PM
 */

namespace Giftcards\FixedWidth\Tests;


use Giftcards\FixedWidth\FileSystemLine;
use Giftcards\FixedWidth\Slice;
use Mockery;
use SplFileObject;

class FileSystemLineTest extends TestCase
{
    public function tearDown() : void
    {
        Mockery::close();
    }

    public function testGettersSetters()
    {
        $fileObject = new SplFileObject(__DIR__.'/Fixtures/lazy_line_'.$this->getFaker()->word.'.txt', 'w+');
        $fileObject->fwrite(str_repeat(' ', 10));
        $fileObject->fwrite("\n");
        $fileObject->fwrite(str_repeat(' ', 10));
        $line = new FileSystemLine($fileObject, 11, 10);

        $this->assertSame($line, $line->set('0:5', 'hello'));
        $this->assertEquals('hello', $line->get('0:5'));
        $this->assertEquals('hello     ', (string)$line);
        $this->assertSame($line, $line->set(0, 'g'));
        $this->assertEquals('g', $line->get(0));
        $this->assertEquals('gello', $line->get('0:5'));
        $this->assertEquals(10, $line->getLength());
        $line[0] = 'h';
        $this->assertEquals('hello', $line->get('0:5'));
        $this->assertEquals('hello', $line['0:5']);
        $this->assertEquals('hello', $line[Slice::createFromString('0:5')]);
        $line['6:9'] = 'how';
        $this->assertEquals('hello how ', (string)$line);
        $this->assertEquals('how', $line['6:9']);
        $this->assertTrue(isset($line['6:9']));
        $this->assertFalse(isset($line['9:11']));
        $line['9:10'] = 'e';
        $this->assertEquals('hello howe', (string)$line);
        $this->assertSame($line, $line->remove('0:5'));
        $this->assertEquals('      howe', (string)$line);
        unset($line[9]);
        $this->assertEquals('      how ', (string)$line);
        unset($line['6:9']);
        $this->assertEquals('          ', (string)$line);
        unlink($fileObject->getRealPath());
    }

    public function testSetWhereValueSetIsntAsLongAsSlice()
    {
        $fileObject = new SplFileObject(__DIR__.'/Fixtures/lazy_line_'.$this->getFaker()->word.'.txt', 'w+');
        $fileObject->fwrite(str_repeat(' ', 10));
        $fileObject->fwrite("\n");
        $fileObject->fwrite(str_repeat(' ', 10));
        $line = new FileSystemLine($fileObject, 11, 10);

        $line['0:5'] = 'we';
        $this->assertEquals('we        ', (string)$line);
        unlink($fileObject->getRealPath());
    }

    public function testFileOverflow()
    {
        $this->expectException('\OverflowException');
        $fileObject = new SplFileObject(__DIR__.'/Fixtures/fixed_width_uneven.txt', 'r');
        $line = new FileSystemLine($fileObject, 11, 10);
        $line->get('0:10');
    }

    public function test__toStringFileOverflow()
    {
        $fileObject = new SplFileObject(__DIR__.'/Fixtures/fixed_width_uneven.txt', 'r');
        $line = new FileSystemLine($fileObject, 11, 10);
        $this->assertIsString((string)$line);
    }
}
