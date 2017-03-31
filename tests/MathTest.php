<?php
/**
 * Created by PhpStorm.
 * User: liumapp.com
 * contact: liumapp.com@gmail.com
 * Date: 3/31/17
 * Time: 10:43 AM
 */
namespace liumapp\payment\tests;

use liumapp\payment\Math;

class MathTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeMultiplication ()
    {
        $a = new Math(1);

        $b = $a->multiplication();

        $this->assertEquals(1.5 , $b->getNumber());

    }
}