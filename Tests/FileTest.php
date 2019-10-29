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

class FileTest extends InMemoryFileTest
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
        parent::setUp();
        $this->file = new File(
            $this->name,
            $this->width,
            array($this->line1, $this->line2)
        );
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}
