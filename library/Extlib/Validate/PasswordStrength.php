<?php

namespace Extlib\Validate;

/**
 * Password strength validate class
 *
 * @category    Extlib
 * @package     Extlib\Validate
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2011 Lukasz Ciolecki (mart)
 */
class PasswordStrength extends \Zend_Validate_Abstract
{
    /**
     * Error message keys
     */
    const PASSWORD_NO_NUMBER = 'passwordNoNumber';
    const PASSWORD_NO_LOWER_CASE_LETTER = 'passwordNoLowerCaseLetter';
    const PASSWORD_NO_UPPER_CASE_LETTER = 'passwordNoUpperCaseLetter';

    /* Number of letters count */
    const LETTERS_COUNT = 35;

    /**
     * Array of error messages
     * 
     * @var array
     */
    protected $_messageTemplates = array(
        self::PASSWORD_NO_NUMBER => "Password must contain at least one number",
        self::PASSWORD_NO_LOWER_CASE_LETTER => "Password must contain at least one lower case letter",
        self::PASSWORD_NO_UPPER_CASE_LETTER => "Password must contain at least one upper case letter"
    );

    /**
     * Array of possible lower case letters
     * 
     * @var array 
     */
    protected $_loweCaseLetters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
        'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
        'w', 'x', 'y', 'z', 'ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż'
    );

    /**
     * Array of possible upper case letters
     * 
     * @var array 
     */
    protected $_upperCaseLetters = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
        'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
        'W', 'X', 'Y', 'Z', 'Ą', 'Ć', 'Ę', 'Ł', 'Ń', 'Ó', 'Ś', 'Ź', 'Ż'
    );

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
        $this->_setValue($value);
        $arr = str_split($value);

        if (preg_match('/[0-9]/', $value) !== 1) {
            $this->_error(self::PASSWORD_NO_NUMBER);
            return false;
        }

        if (count(array_diff($this->_loweCaseLetters, $arr)) === self::LETTERS_COUNT) {
            $this->_error(self::PASSWORD_NO_LOWER_CASE_LETTER);
            return false;
        }

        if (count(array_diff($this->_upperCaseLetters, $arr)) === self::LETTERS_COUNT) {
            $this->_error(self::PASSWORD_NO_UPPER_CASE_LETTER);
            return false;
        }

        return true;
    }
}
