<?php
/**
 * Extlib_View_Helper_FlashMessage - Flash message view helper, render message
 * 
 * @category   Extlib
 * @package    Extlib_View
 * @subpackage Helper
 * @author Łukasz Ciołecki (Mart) 
 */
class Extlib_View_Helper_FlashMessage extends  Zend_View_Helper_HtmlElement
{
    const BAR_CLASS = 'bar';
    const BAR_CONTAINER = 'div';
    
    /**
     * Image link
     * 
     * @var string
     */
    static public $imgSrc = '/images/admin/empty-img.png';
    
    /**
     * $_flashMessage - instance of Extlib_Controller_Action_Helper_FlashMessage
     * 
     * @var Extlib_Controller_Action_Helper_FlashMessage 
     */
    protected $_flashMessage = null;
    
    /**
     *  __construct() - instance of construct
     */
    public function __construct() 
    {
        if (null === $this->_flashMessage) {
            $this->_flashMessage = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessage');
        }
    }
    
    /**
     * flashMessage() - execute method
     * 
     * @param array $attribs
     * @param boolean $escape
     * @return xhtml|string 
     */
    public function flashMessage(array $attribs = array(), $escape = true)
    {               
        if (!$this->_flashMessage->hasMessage()) {
             return null;
         }
        
        $class = self::BAR_CLASS . ' ' . self::BAR_CLASS . '_' . $this->_flashMessage->getTypeMessage();

        if (key_exists('class', $attribs)) {
            $class = $class . ' ' . trim($this->view->escape($attribs['class']), ' ');
        }
        
        $attribs['class'] = $class;
     
        $xhtml = '<' . self::BAR_CONTAINER . $this->_htmlAttribs($attribs) . '>';
        $xhtml .= '<img class="flashmeessage-icon" src="' . self::$imgSrc .'" alt=""/>';
        $xhtml .= '<span class="flashmeessage-text">';
        
        if ($escape) {
            $xhtml .= $this->view->translate($this->view->escape($this->_flashMessage->getMessage()));
        } else{
            $xhtml .= $this->view->translate($this->_flashMessage->getMessage());
        }
        
        $xhtml .= '</span>';
        $xhtml .= '</' . self::BAR_CONTAINER . '>';
   
        return $xhtml;
    }    
}