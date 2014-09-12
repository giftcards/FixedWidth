<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/12/14
 * Time: 3:38 PM
 */

namespace Giftcards\FixedWidth\Tests\Spec\Loader;


use Giftcards\FixedWidth\Spec\Loader\YamlSpecLoader;
use Symfony\Component\Config\FileLocator;

class YamlSpecLoaderTest extends AbstractSpecFileLoaderTest
{
    public function setUp()
    {
        parent::setUp();
        $this->loader = new YamlSpecLoader(new FileLocator(__DIR__.'/../../Fixtures/'));
    }
}
 