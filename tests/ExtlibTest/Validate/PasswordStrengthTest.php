<?php

namespace ExtlibTest\Validate;

/**
 * Tests for Extlib\Validate\PasswordStrength
 * 
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2013 Lukasz Ciolecki (mart)
 */
class PasswordStrengthTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Return array of tests data
     * 
     * @return array
     */
    public function passwords()
    {
        return array(
            [null, false],
            ["", false],
            ["a", false],
            ["A", false],
            ["abc", false],
            ["ABC", false],
            ["abcABC", false],
            ["1abc", false],
            ["1ABC", false],
            ["1abcABC", true]
        );
    }

    /**
     * Testing method
     * 
     * @dataProvider passwords
     * @test
     */
    public function assert($password, $expected)
    {
        $validator = new \Extlib\Validate\PasswordStrength();
        $result = $validator->isValid($password);
        $this->assertEquals($expected, $result);
    }
}
