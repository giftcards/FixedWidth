<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/12/14
 * Time: 5:57 PM
 */

namespace Giftcards\FixedWidth\Tests;


use Giftcards\FixedWidth\Line;
use Giftcards\FixedWidth\Slice;

class LineTest extends TestCase
{

    public function testGettersSetters()
    {
        $line = new Line(10);

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
    }
}
 