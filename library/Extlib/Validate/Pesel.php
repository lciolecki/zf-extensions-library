<?php

namespace Extlib\Validate;

/**
 * Polish universal electronic system for registration of the population number validate class
 *
 * @category    Extlib
 * @package     Extlib\Validate
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2011 Lukasz Ciolecki (mart)
 */
class Pesel extends \Zend_Validate_Abstract
{
    /**
     * Error message key
     */
    const INVALID_PESEL = 'invalidPesel';

    /**
     * Array of error messages
     * 
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID_PESEL => "Number '%value%' is not a valid PESEL.",
    );

    /**
     * Array of weights
     *
     * @var array 
     */
    protected $_weights = array(1, 3, 7, 9, 1, 3, 7, 9, 1, 3);

    /**
     * Defined by Zend_Validate_Interface
     * 
     * @see Zend_Validate_Interface::isValid()
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

        $int = 10 - $intSum % 10;
        $intControlNr = ($int == 10) ? 0 : $int;

        if ($intControlNr != $value[10]) {
            $this->_error(self::INVALID_PESEL, $value);
            return false;
        }

        return true;
    }
}
