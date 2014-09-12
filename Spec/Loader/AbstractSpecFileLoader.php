<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/4/14
 * Time: 2:55 PM
 */

namespace Giftcards\FixedWidth\Spec\Loader;


use Giftcards\FixedWidth\Spec\SpecNotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\FileLocatorInterface;

abstract class AbstractSpecFileLoader extends ArraySpecLoader
{
    protected $fileLocator;

    public function __construct(FileLocatorInterface $fileLocator)
    {
        $this->fileLocator = $fileLocator;
        parent::__construct([]);
    }

    public function initializeSpec($name)
    {
        try {

            if ($path = $this->fileLocator->locate($this->getFileName($name))) {

                $this->arraySpecs[$name] = $this->loadSpecFile($path, $name);
            }
        } catch (\Exception $e) {

            throw new SpecNotFoundException($name, 'file', $e);
        }

        parent::initializeSpec($name);
    }

    abstract protected function getFileName($name);
    abstract protected function loadSpecFile($path, $name);
} 