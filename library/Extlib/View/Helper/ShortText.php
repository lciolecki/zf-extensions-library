<?php

namespace Extlib\View\Helper;

/**
 * View helper class for cut long text
 * 
 * @category    Extlib
 * @package     Extlib\View
 * @subpackage  Extlib\View\Helper
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2010 Lukasz Ciolecki (mart)
 */
class ShortText extends \Zend_View_Helper_Abstract
{
    /**
     * End of longest text
     */
    const END_TEXT = '...';
    
    /**
     * View encoding
     * 
     * @var string
     */
    protected $charset = 'UTF-8';

    /**
     * Instance of constructors
     */
    public function __construct()
    {
        if ($this->view && $this->view->getEncoding()) {
            $this->charset = $this->view->getEncoding();
        }
    }

    /**
     * Execute method
     *
     * @param string $text
     * @param int $length
     * @param boolean $escape
     * @return string
     */
    public function shortText($text, $length, $escape = true)
    {
        if (!$text) {
            return '';
        }

        if ($length <= 0) {
            throw new \Zend_View_Exception('Max length text must be great then 0.');
        }

        if ($this->view && (bool) $escape) {
            $text = $this->view->escape($text);
        }
        
        if (mb_strlen($text, $this->charset) > $length) {
            return mb_substr($text, 0, $length, $this->charset) . self::END_TEXT;
        } 

        return $text;
    }
}
