<?php
/**
 * @see Extlib_Validate_Db_Abstract
 */
require_once 'Extlib/Validate/Doctrine/Abstract.php';

/**
 * Extlib_Validate_Doctrine_RecordExists - Confirms a record exists in a table.
 *
 * @category   Extlib
 * @package    Extlib_Validate
 * @uses       Extlib_Validate_Doctrine_Abstract
 * @copyright  Copyright (c) 2012 Łukasz Ciołecki (Mart)
 */
class Extlib_Validate_Doctrine_RecordExists extends Extlib_Validate_Doctrine_Abstract
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
        
        if (!$result) {
            $valid = false;
            $this->_error(self::ERROR_NO_RECORD_FOUND);
        }

        return $valid;
    }
}