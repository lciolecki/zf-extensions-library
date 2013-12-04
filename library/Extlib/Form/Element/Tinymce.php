<?php

namespace Extlib\Form\Element;

/**
 * Form element tinymce
 *
 * @category    Extlib
 * @package     Extlib\Form
 * @subpackage  Extlib\Form\Element
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2011 Lukasz Ciolecki (mart)
 */
class Tinymce extends \Zend_Form_Element_Textarea
{

    /**
     * View helper for tinymce element
     * 
     * @var string
     */
    public $helper = 'formTinymce';

    /**
     * Path to TinyMce java script library
     * 
     * @var string 
     */
    public $pathScript = '/js/tiny_mce/tiny_mce.js';

    /**
     * Get path script
     * 
     * @return string
     */
    public function getPathScript()
    {
        return $this->pathScript;
    }

    /**
     * Set path script
     * 
     * @param string $pathScript
     * @return \Extlib\Form\Element\Tinymce
     */
    public function setPathScript($pathScript)
    {
        $this->pathScript = $pathScript;
        return $this;
    }

}
