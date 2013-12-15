<?php

namespace ExtlibTest\View\Helper;

use Extlib\View\Helper\Media;

/**
 * Tests for Extlib\View\Helper\Media
 * 
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2013 Lukasz Ciolecki (mart)
 */
class MediaTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test method
     */
    public function test()
    {
        $domain = 'http://test-domain.com';

        $file = 'style.css';
        $resources = APPLICATION_PATH . '/../resources/';
        $time = filemtime($resources . $file);

        Media::$dir = $resources;
        Media::$domain = $domain;
        $helper = new Media();

        $withTime = sprintf('%s/%s?ts=%s', $domain, $file, $time);
        $withoutTime = sprintf('%s/%s', $domain, $file);

        $this->assertEquals(true, $helper->media($file) === $withTime);
        $this->assertEquals(true, $helper->media($file, false) === $withoutTime);
    }
}
