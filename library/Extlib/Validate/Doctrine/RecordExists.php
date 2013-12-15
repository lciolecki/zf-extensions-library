<?php

namespace Extlib\Validate\Doctrine;

/**
 * Doctrine v1.2 record exists validate class
 *
 * @category    Extlib
 * @package     Extlib\Validate
 * @subpackage  Extlib\Validate\Doctrine
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2012 Łukasz Ciołecki (Mart)
 */
class RecordExists extends DoctrineAbstract
{

    /**
     * (non-PHPdoc)
     * @see Zend_Validate_Interface::isValid()
     */
    public function isValid($value)
    {
        if (!$this->query($value)) {
            $this->_error(self::ERROR_NO_RECORD_FOUND, $value);
            return false;
        }

        return true;
    }
}
