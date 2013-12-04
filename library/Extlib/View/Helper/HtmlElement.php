<?php
/**
 * Extlib_View_Helper_HtmlElement - Base view class Extlib Html element.
 * 
 * @category   Extlib
 * @package    Extlib_View
 * @subpackage Helper
 * @author Łukasz Ciołecki (mart)
 */
class Extlib_View_Helper_HtmlElement extends Zend_View_Helper_HtmlElement
{
    /**
     * $_charset - view encoding
     * 
     * @var string
     */
    protected $_charset = 'UTF-8';
    
    /**
     * __construct() - instance of constructors
     */
    public function __construct() 
    {
        if (isset($this->view) && $this->view->getEncoding()) {
            $this->_charset = $this->view->getEncoding();
        }
    }
}