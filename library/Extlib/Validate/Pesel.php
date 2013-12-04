<?php 
/**
 * Extlib_Validate_Pesel - Pesel class validator
 *
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2012 Łukasz Ciołecki (mart)
 */
class Extlib_Validate_Pesel extends Zend_Validate_Abstract
{
    const INVALID_PESEL = 'invalidPesel';
    
    /**
     * $_messageTemplates - array of error messages
     * 
     * @var array
     */
    protected $_messageTemplates = array (
        self::INVALID_PESEL => "Number '%value%' is not a valid PESEL", 
    );
    
    /**
     * $_weights - Array of weights
     *
     * @var array 
     */
    protected $_weights = array(1, 3, 7, 9, 1, 3, 7, 9, 1, 3);
    
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
        if (strlen($value) != 11) {
            $this->_error(self::INVALID_PESEL, $value);
            return false;    
        }
        
        $intSum = 0;

        for ($i = 0; $i < 10; $i++) {
            $intSum += $this->_weights[$i] * $value[$i];     
        }
        
        $int = 10 - ($intSum % 10);
        $intControlNr = ($int == 10) ? 0 : $int; 
        
        if ($intControlNr != $value[10]) {
            $this->_error(self::INVALID_PESEL, $value);
            return false;      
        }

        return true;
    }
}