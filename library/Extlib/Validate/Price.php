<?php

namespace Extlib\Validate;

/**
 * Price validate class
 *
 * @category    Extlib
 * @package     Extlib\Validate
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2011 Lukasz Ciolecki (mart)
 */
class Price extends \Zend_Validate_Abstract
{
    /**
     * Error message keys
     */
    const PRICE_NEGATIVE = 'priceNegative';
    const INVALID_PRICE = 'notPrice';

    /**
     * Array of error messages 
     * 
     * @var array
     */
    protected $_messageTemplates = array(
        self::PRICE_NEGATIVE => "Price can not be negative",
        self::INVALID_PRICE => "Value '%value%' is not a valid format of price'",
    );

    /**
     * Array of options 
     * 
     * @var array 
     */
    protected $_options = array(
        'length' => 8,
        'precision' => 2,
        'negative' => false
    );

    /**
     * $_pattern - pattern for validation
     * 
     * @var string 
     */
    protected $_pattern = '/^([0-9]{1,:length:})(|\.([0-9]{1,:precision:}))$/';

    /**
     * Class constructor
     *
     * @param array|Zend_Config
     */
    public function __construct($options = null)
    {
        if ($options instanceof \Zend_Config) {
            $options = $options->toArray();
        }

        if (null !== $options) {
            $this->setOptions($options);
        }

        $this->_pattern = str_replace(':length:', $this->_options['length'], $this->_pattern);
        $this->_pattern = str_replace(':precision:', $this->_options['precision'], $this->_pattern);
    }

    /**
     * Returns the set options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Sets options to use
     *
     * @param  array $options (Optional) Options to use
     * @return \Extlib\Validate\Price
     */
    public function setOptions(array $options = null)
    {
        $this->_options = $options + $this->_options;
        return $this;
    }

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

        if (!$value || !is_string($value) && !is_int($value) && !is_float($value)) {
            $this->_error(self::INVALID_PRICE);
            return false;
        }

        if (false === $this->_options['negative'] && '-' === $value[0]) {
            $this->_error(self::PRICE_NEGATIVE, $value);
            return false;
        }

        $status = @preg_match($this->_pattern, $value);
        if (false === $status) {
            $this->_error(self::INVALID_PRICE);
            return false;
        }

        if (!$status) {
            $this->_error(self::INVALID_PRICE);
            return false;
        }

        return true;
    }
}
