<?php

namespace Extlib\Validate\Doctrine2;

/**
 * Doctrine v2 no record exists validate class
 *
 * @category    Extlib
 * @package     Extlib\Validate
 * @subpackage  Extlib\Validate\Doctrine2
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2013 Lukasz Ciolecki (mart)
 */
class NoRecordExists extends Doctrine2Abstract
{

    /**
     * (non-PHPdoc)
     * @see Zend_Validate_Interface::isValid()
     */
    public function isValid($value)
    {
        if ($this->query($value)) {
            $this->_error(self::ERROR_RECORD_FOUND, $value);
            return false;
        }

        return true;
    }
}
