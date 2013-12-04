<?php
/**
 * Extlib_View_Helper_Kcfinder - Kcfinder view helper class
 * 
 * @category   Extlib
 * @package    Extlib_View
 * @subpackage Helper
 * @author Łukasz Ciołecki (Mart)
 */
class Extlib_View_Helper_Kcfinder extends Zend_View_Helper_HtmlElement
{
    const CONTENER = 'div';
    
    /**
     * $_kcfinderPath - path to tinyMce java script library
     * 
     * @var string 
     */
    static public $_kcfinderPath = '/js/kcfinder/browse.php';
    	
    /**
     * $_locale - instance of Zend_Locale
     * 
     * @var Zend_Locale 
     */
    protected $_locale = null;
    
    /**
     * __construct() - instance of construct
     */
    public function __construct()
    {
        $this->_locale = new Zend_Locale();
    }
    
    /**
     * kcfinder() - exequte method
     * 
     * @param string $name
     * @param string $type
     * @param array $attribs
     * @param string $kcfinderPath
     * @return mixed 
     */
    public function kcfinder($name, $type = 'file', array $attribs = array(), $kcfinderPath = null)
    {
        if (null !== $kcfinderPath) {
            self::$_kcfinderPath = $kcfinderPath;
        }

        $src = self::$_kcfinderPath . '?type=' . $type . '&amp;lang=' . $this->_locale->getLanguage();
        
        if (array_key_exists('src', $attribs)) {
            unset($attribs['src']);
        }

        $xhtml = '<' . self::CONTENER . ' ' . $this->_htmlAttribs($attribs) . '>';
        $xhtml .= '<iframe id="'. $this->view->escape($name) .'" src="'. $src .'"></iframe>';
        $xhtml .= '</' . self::CONTENER . '>';

        return $xhtml;
    }							
}
