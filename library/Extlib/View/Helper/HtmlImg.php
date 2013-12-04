<?php
/**
 * Extlib_View_Helper_HtmlImg - Class View to create a html img tag
 * 
 * @category   Extlib
 * @package    Extlib_View
 * @subpackage Helper
 * @author Łukasz Ciołecki (mart)
 */
class Extlib_View_Helper_HtmlImg extends Extlib_View_Helper_HtmlElement 
{
    /* Static png image - NO IMG */
    const IMG_NOT_FOUND = 'data:image/png;base64,R0lGODlhFAAUAIAAAAAAAP///yH5BAAAAAAALAAAAAAUABQAAAI5jI+pywv4DJiMyovTi1srHnTQd1BRSaKh6rHT2cTyHJqnVcPcDWZgJ0oBV7sb5jc6KldHUytHi0oLADs=';
        
    /**
     * htmlimage() - execute method 
     * 
     * @param string $src
     * @param string $alt
     * @param array $attribs 
     * @param boolean $internal
     * @parma boolean $base64
     * @return string|xhtml 
     */
    public function htmlimg($src, $alt, array $attribs = array(), $internal = true, $base64 = false)
    {        
        if (array_key_exists('alt', $attribs)) {
            unset($attribs['alt']);
        }
 
        if (array_key_exists('src', $attribs)) {
            unset($attribs['src']);
        }
        
        if ($internal) {
            $src = $this->view->baseUrl($src);
        } 
        
//        if (!file_exists($src)) {
//            $src = self::IMG_NOT_FOUND;
//        } elseif (file_exists($src) && $base64) {
//            $src = $this->_base64EncodeImage($src);
//        }
        
        $xhtml  = '<img ';
        $xhtml .= 'src="' . $src . '" ';
        $xhtml .= 'alt="' . $this->view->translate(ucfirst(mb_strtolower($this->view->escape($alt), $this->_charset))) . '"';
        $xhtml .= $this->_htmlAttribs($attribs);
        $xhtml .= $this->getClosingBracket();

        return $xhtml;
    } 
    
    /**
     * _base64EncodeImage() - method encoding image file to base64
     * 
     * @param string $file 
     * @return string 
     */
    protected function _base64EncodeImage ($file) 
    {
        $imgbinary = fread(fopen($file, "r"), filesize($file));
        return 'data:image/' . pathinfo($file, PATHINFO_EXTENSION) . ';base64,' . base64_encode($file);
    }
}