<?php
/**
 * Extlib_View_Helper_LowerText - View helper class create lower text
 * 
 * @category   Extlib
 * @package    Extlib_View
 * @subpackage Helper
 * @author Łukasz Ciołecki (Mart) 
 */
class Extlib_View_Helper_LowerText extends Zend_View_Helper_Abstract
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
        $this->setView(Zend_Layout::getMvcInstance()->getView());
        
        if ($this->view->getEncoding()) {
            $this->_charset = $this->view->getEncoding();
        }
    }
    
    /**
     * lowerNapis() - execute method
     *
     * @param string $text
     * @param boolean $escape
     * @return string
     */
    public function lowerText($text, $escape = true)
    {
        if ($escape) {
            return mb_strtolower($this->view->translate($this->view->escape($text)), $this->_charset);
        }
        
        return mb_strtolower($this->view->translate($text), $this->_charset);
    }
}