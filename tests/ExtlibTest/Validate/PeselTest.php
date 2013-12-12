<?php

namespace ExtlibTest\Validate;

/**
 * Tests for Extlib\Validate\Pesel
 * 
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2011 Lukasz Ciolecki (mart)
 */
class PeselTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Return array of tests data
     * 
     * @return array
     */
    public function pesels()
    {
        return array(
            [null, false],
            ["", false],
            ["46042919991", false],
            ["46042919999", true],
            [46042919999, true],
            [46042919991, false]
        );
    }

    /**
     * Testing method
     * 
     * @dataProvider pesels
     * @test
     */
    public function tests($pesel, $expected)
    {
        $validator = new \Extlib\Validate\Pesel();
        $result = $validator->isValid($pesel);
        $this->assertEquals($expected, $result);
    }
}
