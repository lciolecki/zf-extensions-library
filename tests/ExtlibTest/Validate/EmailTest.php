<?php

namespace ExtlibTest\Validate;

/**
 * Tests for Extlib\Validate\Email
 * 
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2013 Lukasz Ciolecki (mart)
 */
class EmailTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Return array of tests data
     * 
     * @return array
     */
    public function emails()
    {
        return array(
            [null, false],
            ["", false],
            ["ciolecki.lukasz@gmail.com", true],
            ["ciolecki.lukasz.gmail.com", false],
            ["ciolecki.lukasz@gmail", false],
            ["blabla", false]
        );
    }

    /**
     * Testing method
     * 
     * @dataProvider emails
     * @test
     */
    public function tests($email, $expected)
    {
        $validator = new \Extlib\Validate\Email();
        $result = $validator->isValid($email);
        $this->assertEquals($expected, $result);
    }
}
