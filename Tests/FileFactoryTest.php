<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/10/14
 * Time: 6:49 PM
 */

namespace Giftcards\FixedWidth\Tests;


use Giftcards\FixedWidth\InMemoryFile;
use Giftcards\FixedWidth\FileFactory;
use Mockery\MockInterface;

class FileFactoryTest extends TestCase
{
    /** @var  FileFactory */
    protected $factory;
    /** @var  MockInterface */
    protected $specLoader;

    public function setUp()
    {
        $this->factory = new FileFactory();
    }

    public function tearDown()
    {
        \Mockery::close();
    }

    public function testCreate()
    {
        $width = $this->getFaker()->numberBetween(5, 15);
        $name = $this->getFaker()->word;
        $this->assertEquals(new InMemoryFile($name, $width), $this->factory->create(
            $name,
            $width
        ));
    }

    public function testCreateFromFile()
    {
        $file = new \SplFileInfo(__DIR__.'/Fixtures/fixed_width.txt');
        $lines = explode("\n", file_get_contents($file->getRealPath()));

        $this->assertEquals(
            new InMemoryFile($file->getFilename(), strlen($lines[0]), $lines, "\n"),
            $this->factory->createFromFile($file, "\n")
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateFromFileWhereFileIsEmpty()
    {
        $this->factory->createFromFile(new \SplFileInfo(__DIR__.'/Fixtures/empty_fixed_width.txt'));
    }

    public function testCreateFromFileWhereFileHasTrailingEndline()
    {
        $file = new \SplFileInfo(__DIR__.'/Fixtures/fixed_width_trailing_newline.txt');
        $lines = explode("\n", file_get_contents($file->getRealPath()));

        array_pop($lines);

        $this->assertEquals(
            new InMemoryFile($file->getFilename(), strlen($lines[0]), $lines, "\n"),
            $this->factory->createFromFile($file, "\n")
        );
    }

    public function testCreateFromData()
    {
        $file = new \SplFileInfo(__DIR__.'/Fixtures/fixed_width.txt');
        $lines = explode("\n", file_get_contents($file->getRealPath()));

        $this->assertEquals(
            new InMemoryFile($file->getFilename(), strlen($lines[0]), $lines, "\n"),
            $this->factory->createFromFile($file, "\n")
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateFromDataWhereDataIsEmpty()
    {
        $this->factory->createFromData(
            file_get_contents(__DIR__.'/Fixtures/empty_fixed_width.txt'),
            'name'
        );
    }

    public function testCreateFromDataWhereDataHasTrailingEndline()
    {
        $data = file_get_contents(__DIR__.'/Fixtures/fixed_width_trailing_newline.txt');
        $lines = explode("\n", $data);

        array_pop($lines);

        $this->assertEquals(
            new InMemoryFile(
                'fixed_width_trailing_newline.txt',
                strlen($lines[0]),
                $lines,
                "\n"
            ),
            $this->factory->createFromData(
                $data,
                'fixed_width_trailing_newline.txt',
                "\n"
            )
        );
    }
}
