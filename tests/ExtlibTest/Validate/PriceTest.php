<?php

namespace ExtlibTest\Validate;

/**
 * Tests for Extlib\Validate\Price
 * 
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2013 Lukasz Ciolecki (mart)
 */
class PriceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Return array of tests data
     * 
     * @return array
     */
    public function prices()
    {
        return array(
            [null, false],
            ["", false],
            [-2.52, false],
            [2.56, true]
        );
    }

    /**
     * Testing method
     * 
     * @dataProvider prices
     * @test
     */
    public function tests($price, $expected)
    {
        $validator = new \Extlib\Validate\Price();
        $result = $validator->isValid($price);
        $this->assertEquals($expected, $result);
    }
}
