<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/4/14
 * Time: 2:59 PM
 */

namespace Giftcards\FixedWidth\Spec\Loader;


use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

class YamlSpecLoader extends AbstractSpecFileLoader
{
    protected $parser;

    protected function getFileName($name)
    {
        return sprintf('%s.yml', $name);
    }

    protected function loadSpecFile($path, $name)
    {
        if (!$this->parser) {

            $this->parser = new Parser();
        }

        return $this->parser->parse(file_get_contents($path));
    }
}