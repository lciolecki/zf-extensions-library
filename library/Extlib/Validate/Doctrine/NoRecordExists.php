<?php
/**
 * Extlib_Validate_Doctrine_NoRecordExists - Confirms a record does not exist in a table.
 *
 * @category   Extlib
 * @package    Extlib_Validate
 * @uses       Extlib_Validate_Doctrine_Abstract
 * @copyright  Copyright (c) 2012 Łukasz Ciołecki (Mart)
 */
class Extlib_Validate_Doctrine_NoRecordExists extends Extlib_Validate_Doctrine_Abstract
{
    /**
     * (non-PHPdoc)
     * @see Zend_Validate_Interface::isValid()
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
