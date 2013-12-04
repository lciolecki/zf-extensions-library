<?php
/**
 * Extlib_View_Helper_ShortText - View helper class for cut long text
 * 
 * @category   Extlib
 * @package    Extlib_View
 * @subpackage Helper
 * @author Łukasz Ciołecki (Mart)
 */
class Extlib_View_Helper_ShortText extends Zend_View_Helper_Abstract 
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
     * shortText() - execute method
     *
     * @param string $text
     * @param int $maxLength
     * @param boolean $escape
     * @return string
     */
    public function shortText($text, $maxLength, $escape = true)
    {
        if (empty($text)) {
            return null;
        }
        
        if ((int) $maxLength <= 0) {
            throw new Zend_View_Exception('Max length text must be great then 0.');
        }
        
        $text = $this->view->translate($text);

        if (mb_strlen($text,  $this->_charset) > $maxLength) {
            $text = mb_substr($text, 0, $maxLength, $this->_charset) . '...' ;  
        } else {
            $text = $text;
        }
        
        if ((bool)$escape) {
            return $this->view->escape($text);
        }
        
        return $text;
    }
}