<?php

namespace ExtlibTest\Validate;

/**
 * Tests for Extlib\Validate\Url
 * 
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2011 Lukasz Ciolecki (mart)
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Return array of tests data
     * 
     * @return array
     */
    public function urls()
    {
        return array(
            [null, false],
            ["", false],
            ["http://onet.pl/ad/asd?123", true],
            ["https://onet.pl/asdasd/asda", true],
            ["onet.pl", false],
            ["asdad", false]
        );
    }

    /**
     * Testing method
     * 
     * @dataProvider urls
     * @test
     */
    public function tests($url, $expected)
    {
        $validator = new \Extlib\Validate\Url();
        $result = $validator->isValid($url);
        $this->assertEquals($expected, $result);
    }
}
