<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/4/14
 * Time: 2:55 PM
 */

namespace Giftcards\FixedWidth\Spec\Loader;


use Giftcards\FixedWidth\Spec\Loader\ArraySpecLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\FileLocatorInterface;

abstract class SpecFileLoader extends ArraySpecLoader
{
    protected $fileLocator;

    public function __construct(FileLocatorInterface $fileLocator)
    {
        $this->fileLocator = $fileLocator;
        parent::__construct([]);
    }

    public function loadSpec($name)
    {
        if ($path = $this->fileLocator->locate($this->getFileName($name))) {

            $this->specs[$name] = $this->loadSpecFile($path, $name);
        }

        return parent::loadSpec($name);
    }

    abstract protected function getFileName($name);
    abstract protected function loadSpecFile($path, $name);
} 