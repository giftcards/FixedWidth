<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/12/14
 * Time: 5:34 PM
 */

namespace Giftcards\FixedWidth\Tests;

use Giftcards\FixedWidth\Slice;
use Mockery;

class SliceTest extends TestCase
{

    public function tearDown() : void
    {
        Mockery::close();
    }

    public function testGettersSetters()
    {
        $start = $this->getFaker()->numberBetween(0, 20);
        $finish = $this->getFaker()->numberBetween(20, 40);
        $slice = new Slice($start, $finish);
        $this->assertEquals($start, $slice->getStart());
        $this->assertEquals($finish, $slice->getFinish());
        $this->assertEquals($finish - $start, $slice->getWidth());
        $this->assertEquals($slice, Slice::createFromString($start.':'.$finish));
        $this->assertEquals($start.':'.$finish, $slice);
        $this->assertEquals(new Slice(1, 2), Slice::createFromString(1));
    }

    public function testInvalidSliceRange()
    {
        $this->expectException('\RangeException');
        new Slice(23, 12);
    }
}
