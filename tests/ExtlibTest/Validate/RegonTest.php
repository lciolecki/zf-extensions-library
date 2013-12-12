<?php

namespace ExtlibTest\Validate;

/**
 * Tests for Extlib\Validate\Regon
 * 
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2013 Lukasz Ciolecki (mart)
 */
class RegonTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Return array of tests data
     * 
     * @return array
     */
    public function regons()
    {
        return array(
            [null, false],
            ["", false],
            ["750100311", false],
            ["750100318", true],
            [750100318, true],
            [750100311, false],
            ["63417801701566", true],
            ["63417801701561", false],
            [63417801701566, true],
            ["63417801701561", false]
        );
    }

    /**
     * Testing method
     * 
     * @dataProvider regons
     * @test
     */
    public function tests($regon, $expected)
    {
        $validator = new \Extlib\Validate\Regon();
        $result = $validator->isValid($regon);
        $this->assertEquals($expected, $result);
    }
}
