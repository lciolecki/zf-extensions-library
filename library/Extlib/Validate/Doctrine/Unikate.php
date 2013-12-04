<?php

/**
 * Extlib_Validate_Doctrine_Unikate - Validator class for join form elemenet. Check is exist joined field in DB.
 * 
 * @category   Extlib
 * @package    Extlib_Validate
 * @uses       Extlib_Validate_Doctrine_Abstract
 * @copyright  Copyright (c) 2012 Łukasz Ciołecki (Mart)
 */
class Extlib_Validate_Doctrine_Unikate extends Extlib_Validate_Doctrine_NoRecordExists
{
    const NOT_UNIKATE = 'notUnikateField';
    const FIELDS_SEPARATOR = '_';

    /**
     * $_fields - array of joined fields
     *  
     * @var array 
     */
    protected $_fields = array();

    /**
     * $_fieldSeparator - fields separator
     * 
     * @var string 
     */
    protected $_fieldsSeparator = self::FIELDS_SEPARATOR;

    /**
     * $_messageTemplates - array of error template messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_UNIKATE => "Value '%value%' is not unikate",
    );

    /**
     * __construct() - instance of constructor
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (!array_key_exists('fields', $options)) {
            throw new Zend_Validate_Exception('Fields option missing!');
        }

        if (array_key_exists('fieldsSeparator', $options)) {
            $this->_fieldsSeparator = $options['fieldsSeparator'];
        }

        $this->_fields = $options['fields'];
    
        parent::__construct($options);
    }

    /**
     * (non-PHPdoc)
     * @see Zend_Validate_Interface::isValid()
     */
    public function isValid($value)
    {
        $string = '';

        foreach ($this->_fields as $field) {
            $string .= $field->getValue() . $this->_fieldsSeparator;
        }

        $string .= $value;

        if (!parent::isValid($string)) {
            $this->_error(self::NOT_UNIKATE, $value);

            foreach ($this->_fields as $field) {
                $field->addError(null);
            }

            return false;
        }

        return true;
    }
}