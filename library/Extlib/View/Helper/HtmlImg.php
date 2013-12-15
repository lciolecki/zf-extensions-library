<?php

namespace Extlib\View\Helper;

/**
 * Class View to create a html img tag
 * 
 * @category    Extlib
 * @package     Extlib\View
 * @subpackage  Extlib\View\Helper
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2010 Lukasz Ciolecki (mart)
 */
class HtmlImg extends \Zend_View_Helper_HtmlElement
{
    /**
     * Execute method
     * 
     * @param string $src
     * @param array $attribs 
     * @param boolean $internal
     * @return string|xhtml 
     */
    public function htmlimg($src, array $attribs = array(), $internal = true)
    {
        if (isset($attribs['src'])) {
            unset($attribs['src']);
        }

        if ($internal) {
            $src = $this->view->media($src);
        }

        $xhtml = '<img ';
        $xhtml .= 'src="' . $src . '" ';
        $xhtml .= $this->_htmlAttribs($attribs);
        $xhtml .= $this->getClosingBracket();

        return $xhtml;
    }
}
