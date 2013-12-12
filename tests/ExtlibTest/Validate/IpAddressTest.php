<?php

namespace ExtlibTest\Validate;

/**
 * Tests for Extlib\Validate\IpAddress
 * 
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2013 Lukasz Ciolecki (mart)
 */
class IpAddressTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Return array of tests data
     * 
     * @return array
     */
    public function addresses()
    {
        return array(
            [null, false],
            ["", false],
            ["127.0.0.1", true],
            ["127.0.0", false],
            ["192.168.0.1", true]
        );
    }

    /**
     * Testing method
     * 
     * @dataProvider addresses
     * @test
     */
    public function tests($address, $expected)
    {
        $validator = new \Extlib\Validate\IpAddress();
        $result = $validator->isValid($address);
        $this->assertEquals($expected, $result);
    }
}
