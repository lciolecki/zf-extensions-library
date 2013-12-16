<?php

namespace ExtlibTest\View\Helper;

use Extlib\View\Helper\ShortText;

/**
 * Tests for Extlib\View\Helper\ShortText
 * 
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2013 Lukasz Ciolecki (mart)
 */
class ShortTextTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test method
     */
    public function test()
    {
        $text = 'abcdfgh';
        $after = 'abc' . ShortText::END_TEXT;
        $len = 3;
        
        $helper = new ShortText();

        $this->assertEquals(true, $helper->shortText($text, $len) === $after);
        $this->assertEquals(true, $helper->shortText($text, strlen($text)) === $text);
        $this->assertEquals(true, $helper->shortText($text, strlen($text) + 5) === $text);
    }
}