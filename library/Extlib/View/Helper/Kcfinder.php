<?php

namespace Extlib\View\Helper;

/**
 * Kcfinder view helper class
 * 
 * @category    Extlib
 * @package     Extlib\View
 * @subpackage  Extlib\View\Helper
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2010 Lukasz Ciolecki (mart)
 */
class Kcfinder extends \Zend_View_Helper_HtmlElement
{
    /**
     * Path to TinyMce java script library
     * 
     * @var string 
     */
    static public $kcfinderPath = '/js/kcfinder/browse.php';

    /**
     * Instance of Zend_Locale
     * 
     * @var \Zend_Locale 
     */
    protected $locale = null;

    /**
     * Instance of construct
     */
    public function __construct()
    {
        $this->locale = new \Zend_Locale();
    }

    /**
     * Execute method
     * 
     * @param string $name
     * @param string $type
     * @param array $attribs
     * @param string $kcfinderPath
     * @return string 
     */
    public function kcfinder($name, $type = 'file', array $attribs = array(), $kcfinderPath = null)
    {
        if (null !== $kcfinderPath) {
            self::$kcfinderPath = $kcfinderPath;
        }

        $src = self::$kcfinderPath . '?type=' . $type . '&amp;lang=' . $this->locale->getLanguage();

        if (isset($attribs['src'])) {
            unset($attribs['src']);
        }

        $xhtml  = '<div ' . $this->_htmlAttribs($attribs) . '>';
        $xhtml .= '<iframe id="' . $this->view->escape($name) . '" src="' . $src . '"></iframe>';
        $xhtml .= '</div>';

        return $xhtml;
    }
}
