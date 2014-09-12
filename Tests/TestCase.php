<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/10/14
 * Time: 6:05 PM
 */

namespace Giftcards\FixedWidth\Tests;


use Faker\Factory;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $faker;

    public function getFaker()
    {
        if (!$this->faker) {

            $this->faker = Factory::create();
        }

        return $this->faker;
    }
} 