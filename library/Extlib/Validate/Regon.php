<?php

namespace Extlib\Validate;

/**
 * Polish journal of the national register of economic entities number validate class
 * 
 * @category    Extlib
 * @package     Extlib\Validate
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2011 Lukasz Ciolecki (mart)
 */
class Regon extends \Zend_Validate_Abstract
{
    /**
     * Error message keys
     */
    const INVALID_REGON = 'invalidRegon';

    /**
     * Array of error messages
     * 
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID_REGON => "Number '%value%' is not a valid REGON.",
    );

    /**
     * Array of weight for len = 9
     *
     * @var array 
     */
    protected $_weights9 = array(8, 9, 2, 3, 4, 5, 6, 7);

    /**
     * Array of weight for len = 14
     *
     * @var array 
     */
    protected $_weights14 = array(2, 4, 8, 5, 0, 9, 7, 3, 6, 1, 2, 4, 8);

    /**
     * Defined by Zend_Validate_Interface
     * 
     * @see Zend_Validate_Interface::isValid()
     */
    public function isValid($value)
    {
        if (strlen($value) == 9) {
            $weights = $this->_weights9;
        } elseif (strlen($value) == 14) {
            $weights = $this->_weights14;
        } else {
            $this->_error(self::INVALID_REGON, $value);
            return false;
        }

        $sum = 0;

        for ($i = 0; $i < count($weights); $i++) {
            $sum += $weights[$i] * $value[$i];
        }

        $int = $sum % 11;
        $checksum = ($int == 10) ? 0 : $int;

        if ($checksum != $value[count($weights)]) {
            $this->_error(self::INVALID_REGON, $value);
            return false;
        }

        return true;
    }
}
