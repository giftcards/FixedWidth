<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/12/14
 * Time: 3:34 PM
 */

namespace Giftcards\FixedWidth\Tests\Spec\Loader;


use Mockery\MockInterface;

abstract class AbstractSpecFileLoaderTest extends ArraySpecLoaderTest
{
    /** @var  MockInterface */
    protected $locator;

    public function setUp()
    {
        $this->locator = \Mockery::mock('Symfony\Component\Config\FileLocatorInterface');
        parent::setUp();
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}
