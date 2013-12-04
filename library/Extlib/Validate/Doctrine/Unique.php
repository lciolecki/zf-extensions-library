<?php
/**
 * Extlib_Validate_Doctrine_Unique - validator class for check is field unique.
 * This class can check index unique for many fieldys.
 * 
 * @category   Extlib
 * @package    Extlib_Validate
 * @uses       Extlib_Validate_Doctrine_Abstract
 * @copyright  Copyright (c) 2012 Łukasz Ciołecki (Mart)
 */
class Extlib_Validate_Doctrine_Unique extends Extlib_Validate_Doctrine_Abstract
{
    const NOT_UNIQUE = 'notUniqueField';
    
    /**
     * $_additionals - array of Zend_Form_Element
     *
     * @var array
     */
    protected $_additionals = array();    
    
    /**
     * $_messageTemplates - array of error template messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_UNIQUE  => "Value '%value%' is not unique",
    );

    /**
     * __construct() - instance of constructor
     *
     * @param mixed $options
     */
    public function __construct($options)
    {
        parent::__construct($options);
        
        if (array_key_exists('additionals', $options)) {
            $this->setAdditionals($options['additionals']);
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend_Validate_Interface::isValid()
     */
    public function isValid($value)
    {
        $result = $this->_createQuery($value);
             
        if (!empty($result)) {
            $this->_error(self::NOT_UNIQUE, $value);
            return false;
        }
        
        return true;      
    }
    
    /**
     * setAdditionals() - method set addionals fields to check 
     * (when index unique is for many fields)
     *
     * @param array $additionals
     * @return Extlib_Validate_Doctrine_Unique
     */
    public function setAdditionals(array $additionals)
    {
        if (!empty($additionals)) {
            $this->_additionals = $additionals;
        }
        
        return $this;
    }
    
    /**
     * getAdditionals() - method return array of addionals fields to check
     *
     * @return array
     */
    public function getAdditionals()
    {
        return $this->_additionals;    
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
                               ->where($this->_field . '=?', $value);

        $additionals = $this->getAdditionals();
  
        foreach ($additionals as $key => $element) {
            //When name Zend_Form_Element is other than field name
            if (!is_int($key) && $element instanceof Zend_Form_Element) {
                $query->andWhere("$key =?", $element->getValue());
            } elseif(is_int($key) && $element instanceof Zend_Form_Element) {
                $query->andWhere($element->getName() . " =?", $element->getValue());
            }
        }
   
        $query = $this->_addExcludeQuery($query);
        $result = $query->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

        return $result;
    }
}