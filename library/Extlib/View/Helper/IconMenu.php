<?php

namespace Extlib\View\Helper;

/**
 * View helper class menu with icons
 * 
 * @category    Extlib
 * @package     Extlib\View
 * @subpackage  Extlib\View\Helper
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2010 Lukasz Ciolecki (mart)
 */
class IconMenu extends \Zend_View_Helper_Navigation_Menu
{
    /**
     * Const settings
     */
    const ICON_CLASS = 'menu-icon';
    const LABEL_CLASS = 'menu-label';

    /**
     * View helper execute method
     * 
     * @param \Zend_Navigation_Container $container
     * @return \Zend_View_Helper_IconMenu
     */
    public function iconMenu(\Zend_Navigation_Container $container = null)
    {
        if (null !== $container) {
            $this->setContainer($container);
        }

        return $this;
    }

    /**
     * Returns an HTML string containing an 'a' element for the given page if
     * the page's href is not empty, and a 'span' element if it is empty
     *
     * Overrides {@link Zend_View_Helper_Navigation_Menu::htmlify()}.
     * 
     * @param \Zend_Navigation_Page $page
     * @return string
     */
    public function htmlify(\Zend_Navigation_Page $page)
    {
        // get label and title for translating
        $label = $page->getLabel();
        $title = $page->getTitle();

        // translate label and title?
        if ($this->getUseTranslator() && $t = $this->getTranslator()) {
            if (is_string($label) && !empty($label)) {
                $label = $t->translate($label);
            }
            if (is_string($title) && !empty($title)) {
                $title = $t->translate($title);
            }
        }

        // get attribs for element
        $attribs = array(
            'id' => $page->getId(),
            'title' => $title,
            'class' => $page->getClass()
        );

        // does page have a href?
        $href = $page->getHref();
        if ($href) {
            $element = 'a';
            $attribs['href'] = $href;
            $attribs['target'] = $page->getTarget();
        } else {
            $element = 'div';
        }

        // does page have icon
        if (null !== $page->icon) {
            $icon = '<img src="' . $page->icon . '" class="' . self::ICON_CLASS . '" alt="" /> ';
        } else {
            $icon = '';
        }

        return '<' . $element . $this->_htmlAttribs($attribs) . '>'
                . $icon
                . '<span class="' . self::LABEL_CLASS . '">' . $this->view->escape($label) . '</span>'
                . '</' . $element . '>';
    }
}
