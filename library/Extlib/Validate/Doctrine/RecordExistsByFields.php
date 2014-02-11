<?php

namespace Extlib\Validate\Doctrine;

/**
 * Doctrine v1.2 check record exists by fields
 *
 * @category    Extlib
 * @package     Extlib\Validate
 * @subpackage  Extlib\Validate\Doctrine
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2012 Łukasz Ciołecki (mart)
 */
class RecordExistsByFields extends \Extlib\Validate\Doctrine\DoctrineAbstract
{
    /**
     * Array of search by fields
     *
     * @var array
     */
    protected $searchFields = array();    
    
    /**
     * __construct() - instance of constructor
     *
     * @param mixed $options
     */
    public function __construct($options)
    {
        parent::__construct($options);
        
        if (array_key_exists('searchFields', $options)) {
            $this->setSearchFields($options['searchFields']);
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend_Validate_Interface::isValid()
     */
    public function isValid($value)
    {      
        if (empty($this->query($value))) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Search fileds
     * 
     * @return array
     */
    public function getSearchFields()
    {
        return $this->searchFields;
    }

    /**
     * Set sarch fields
     * 
     * @param array $searchFields
     * @return \Simplepanel_Validate_Doctrine_RecordExistsByFields
     */
    public function setSearchFields(array $searchFields)
    {
        $this->searchFields = $searchFields;
        return $this;
    }

    /**
     * Prepare query
     * 
     * @param array $value
     * @return \Doctrine_Query
     */
    public function prepeareQuery($value)
    {
        $query = \Doctrine_Query::create($this->getConnection())
                ->select($this->getField())
                ->from($this->getTable());

        foreach ($this->getExclude() as $exclude => $value) {
            $query->andWhere(sprintf('%s != ?', $exclude), $value);
        }

        foreach ($this->getInclude() as $include => $value) {
            $query->andWhere(sprintf('%s = ?', $include), $value);
        }

        foreach ($this->getSearchFields() as $field) {
            if (isset($value[$field])) {
                $query->andWhere("$field = ?", $value[$field]);
            }
        }

        return $query;
    }
}