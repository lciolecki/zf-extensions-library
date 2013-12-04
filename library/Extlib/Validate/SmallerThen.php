<?php
/**
 * Extlib_Validate_SmallerThen - Smaller data field validator. 
 * 
 * @category   Extlib
 * @package    Extlib_Validate
 * @uses       Zend_Validate_Abstract
 * @copyright  Copyright (c) 2012 Łukasz Ciołecki (Mart)
 */
class Extlib_Validate_SmallerThen extends Zend_Validate_Abstract
{
    const SMALLER_NEGATIVE = 'smallerNegative';
    
    /**
     * $_messageTemplates - array of error messages 
     * 
     * @var array
     */
    protected $_messageTemplates = array(
        self::SMALLER_NEGATIVE   => "Value '%value%' must be less than base value",
    );

    /**
     * $_baseValueToken - base value field name
     *
     * @var string 
     */
    protected $_baseValueToken;
        
    /**
     * Class constructor
     *
     * @param array|Zend_Config
     */
    public function __construct($baseValueToken = null)
    {
        if ($baseValueToken instanceof Zend_Config) {
            $baseValueToken = $baseValueToken->toArray();
        }

        if (is_array($baseValueToken)) {
            if (array_key_exists('baseValueToken', $baseValueToken)) {
                $baseValueToken = $baseValueToken['baseValueToken'];
            } else {
                require_once 'Zend/Validate/Exception.php';
                throw new Zend_Validate_Exception("Missing option 'baseValueToken'");
            }
        }
        
        $this->_baseValueToken = $baseValueToken;
    }

   /**
    * Defined by Zend_Validate_Interface
    *
    * Returns true if and only if $value is less than max option
    *
    * @param  mixed $value
    * @param mixed $context
    * @return boolean
    */
    public function isValid($value, $context = null)
    {
        if (!array_key_exists($this->_baseValueToken, $context)) {
            throw new Zend_Validate_Exception('Base value was not found');
        }
        
        if ($value >= $context[$this->_baseValueToken]) {
            $this->_error(self::SMALLER_NEGATIVE, $value);
            return false;
        }
        
        return true;
    }
}