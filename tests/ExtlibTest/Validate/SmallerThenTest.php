<?php

namespace ExtlibTest\Validate;

/**
 * Tests for Extlib\Validate\SmallerThen
 * 
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2012 Lukasz Ciolecki (mart)
 */
class SmallerThenTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Return array of tests data
     * 
     * @return array
     */
    public function data()
    {
        return array(
            [null, true],
            ["", true],
            [1, true],
            ["2", true],
            ["14", true],
            [14, true],
            [16, false]
        );
    }

    /**
     * Testing method
     * 
     * @dataProvider data
     * @test
     */
    public function tests($data, $expected)
    {
        $validator = new \Extlib\Validate\SmallerThen('formElement');
        $result = $validator->isValid($data, array('formElement' => 15));
        $this->assertEquals($expected, $result);
    }
}
