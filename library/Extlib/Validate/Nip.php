<?php

namespace Extlib\Validate;

/**
 * Polish tax identyfication number validate class
 *
 * @category    Extlib
 * @package     Extlib\Validate
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2011 Lukasz Ciolecki (mart)
 */
class Nip extends \Zend_Validate_Abstract
{
    /**
     * Error message keys
     */
    const INVALID_NIP = 'invalidNip';

    /**
     * Array of error messages
     * 
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID_NIP => "Number '%value%' is not a valid NIP",
    );

    /**
     * Array of weights
     *
     * @var array 
     */
    protected $_weights = array(6, 5, 7, 2, 3, 4, 5, 6, 7);

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
        $value = (string) $value;
        if (strlen($value) !== 10) {
            $this->_error(self::INVALID_NIP, $value);
            return false;
        }

        $intSum = 0;

        for ($i = 0; $i < 9; $i++) {
            $intSum += $this->_weights[$i] * $value[$i];
        }

        $int = $intSum % 11;
        $intControlNr = ($int === 10) ? 0 : $int;

        if ($intControlNr !== (int) $value[9]) {
            $this->_error(self::INVALID_NIP, $value);
            return false;
        }

        return true;
    }
}
