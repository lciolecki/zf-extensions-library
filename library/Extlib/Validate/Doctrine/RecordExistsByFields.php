<?php
/**
 * Extlib_Validate_Doctrine_RecordExistsByFields - 
 * 
 * @category   Extlib
 * @package    Extlib_Validate
 * @uses       Extlib_Validate_Doctrine_Abstract
 * @copyright  Copyright (c) 2012 Łukasz Ciołecki (Mart)
 */
class Extlib_Validate_Doctrine_RecordExistsByFields extends Extlib_Validate_Doctrine_Abstract
{
    /**
     * $_additionalFields - array of additionals fields
     *
     * @var array
     */
    protected $_additionalFields = array();    
    
    /**
     * __construct() - instance of constructor
     *
     * @param mixed $options
     */
    public function __construct($options)
    {
        parent::__construct($options);
        
        if (array_key_exists('additionalFields', $options)) {
            $this->setAdditionalFields($options['additionalFields']);
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend_Validate_Interface::isValid()
     */
    public function isValid($value)
    {
        if (is_array(!$value)) {
            throw new Zend_Validate_Exception("Valid value must be an array of fields and value!");      
        } 
                 
        if (!array_key_exists($this->_field, $value)) {
            throw new Zend_Validate_Exception("Value for check field - " . $this->_field . " doesn't set!");    
        }
        
        /* Check if all additional fields is set */
        foreach ($this->_additionalFields as $fieldName) {
            if (!array_key_exists($fieldName, $value)) {
                throw new Zend_Validate_Exception("Value for check field - $fieldName doesn't set!");      
            }
        }
      
        $result = $this->_createQuery($value);

        if (empty($result)) {
            return false;
        }
        
        return true;      
    }
    
    /**
     * setAdditionalFields() - method set addionals fields to check 
     *
     * @param array $additionals
     * @return Extlib_Validate_Doctrine_RecordExistsByFields
     */
    public function setAdditionalFields(array $additionals)
    {
        if (!empty($additionals)) {
            $this->_additionalFields = $additionals;
        }
        
        return $this;
    }
    
    /**
     * getAdditionalFields() - method return array of addionals fields to check
     *
     * @return array
     */
    public function getAdditionalFields()
    {
        return $this->_additionalFields;    
    }
    
    /**
     * _createQuery() - protected mehtod, create a Zend_Db_Select query
     * 
     * @return Doctrine_Query
     */
    protected function _createQuery($value)
    {    
        $query = Doctrine_Query::create()
                               ->select($this->_field)
                               ->from($this->_table)
                               ->where($this->_field . '=?', $value[$this->_field]);

        $additionalFields = $this->getAdditionalFields();

        if (!empty($additionalFields)) {
            foreach ($additionalFields as $field) {
                $query->andWhere("$field =?", $value[$field]);
            }
        }

        if (!empty($this->_exclude) & null != $this->_exclude['value']){
            $query->andWhere($this->_exclude['field'] . '!=?', $this->_exclude['value']);
        }
             
        $result = $query->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
 
        return $result;
    }
}