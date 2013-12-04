<?php
/**
 * Extlib_Validate_Doctrine2_NoRecordExists - Confirms a record does not exist in a table.
 *
 * @category   Extlib
 * @package    Extlib_Validate
 * @uses       Extlib_Validate_Doctrine_Abstract
 * @copyright  Copyright (c) 2012 Łukasz Ciołecki (Mart)
 */
class Extlib_Validate_Doctrine2_NoRecordExists extends App_Validate_Doctrine2_Abstract
{
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
        $valid = true;
        $this->_setValue($value);

        $result = $this->_query($value);

        if ($result) {
            $valid = false;
            $this->_error(self::ERROR_RECORD_FOUND);
        }

        return $valid;
    }
}
