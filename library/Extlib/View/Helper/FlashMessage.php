<?php

namespace Extlib\View\Helper;

/**
 * Flash message view helper, render messag
 * 
 * @category    Extlib
 * @package     Extlib\View
 * @subpackage  Extlib\View\Helper
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2010 Lukasz Ciolecki (mart)
 */
class FlashMessage extends \Zend_View_Helper_HtmlElement
{
    /**
     * FlashMessage bar class
     */
    const BAR_CLASS = 'bar';

    /**
     * Image link
     * 
     * @var string
     */
    static public $imgSrc = '/images/admin/empty-img.png';

    /**
     * Instance of FlashMessage
     * 
     * @var \Extlib\Controller\Action\Helper\FlashMessage 
     */
    protected $flashMessage = null;

    /**
     *  Instance of construct
     */
    public function __construct()
    {
        if (null === $this->flashMessage) {
            $this->flashMessage = \Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessage');
        }
    }

    /**
     * Execute method
     * 
     * @param array $attribs
     * @param boolean $escape
     * @return xhtml|string 
     */
    public function flashMessage(array $attribs = array(), $escape = true)
    {
        if (!$this->flashMessage->hasMessage()) {
            return '';
        }

        $class = self::BAR_CLASS . ' ' . self::BAR_CLASS . '_' . $this->flashMessage->getTypeMessage();
        if (isset($attribs['class'])) {
            $class = $class . ' ' . trim($attribs['class'], ' ');
        }

        $attribs['class'] = $class;

        $xhtml = '<div ' . $this->_htmlAttribs($attribs) . '>';
        $xhtml .= '<img class="flashmeessage-icon" src="' . self::$imgSrc . '" alt=""/>';
        $xhtml .= '<span class="flashmeessage-text">';

        if ($escape) {
            $xhtml .= $this->view->translate($this->view->escape($this->flashMessage->getMessage()));
        } else {
            $xhtml .= $this->view->translate($this->flashMessage->getMessage());
        }

        $xhtml .= '</span>';
        $xhtml .= '</div>';

        return $xhtml;
    }
}
