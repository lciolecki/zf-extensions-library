<?php
/**
 * Extlib_Validate_Url - Adress url valiatior class
 *
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2010 Łukasz Ciołecki (mart)
 */
class Extlib_Validate_Url extends Zend_Validate_Abstract
{
    const INVALID_URL = 'invalidUrl';
 
    protected $_messageTemplates = array(
        self::INVALID_URL   => "'%value%' is not a valid URL.",
    );
 
    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is less than max option
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $valueString = (string) $value;
        $this->_setValue($valueString);
 
        if (!Zend_Uri::check($value)) {
            $this->_error(self::INVALID_URL);
            return false;
        }
        
        return true;
    }
}