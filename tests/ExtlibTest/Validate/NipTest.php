<?php

namespace ExtlibTest\Validate;

/**
 * Tests for Extlib\Validate\Nip
 * 
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2013 Lukasz Ciolecki (mart)
 */
class NipTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Return array of tests data
     * 
     * @return array
     */
    public function nips()
    {
        return array(
            [null, false],
            ["", false],
            ["1744722061", false],
            ["1744722064", true],
            [1744722064, true],
            [1744722061, false]
        );
    }

    /**
     * Testing method
     * 
     * @dataProvider nips
     * @test
     */
    public function tests($nip, $expected)
    {
        $validator = new \Extlib\Validate\Nip();
        $result = $validator->isValid($nip);
        $this->assertEquals($expected, $result);
    }
}
