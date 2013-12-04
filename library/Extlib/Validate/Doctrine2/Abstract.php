<?php
/**
 * Extlib_Validate_Doctrine2_Abstract - Class for Doctrine2 record validation.
 *
 * @category   Extlib
 * @package    Extlib_Validate
 * @uses       Extlib_Validate_Abstract
 * @copyright  Copyright (c) 2012 Łukasz Ciołecki (Mart)
 */
abstract class Extlib_Validate_Doctrine2_Abstract extends Zend_Validate_Abstract
{
    /**
     * Error constants
     */
    const ERROR_NO_RECORD_FOUND = 'noRecordFound';
    const ERROR_RECORD_FOUND    = 'recordFound';

    /**
     * $_messageTemplates - array of error messages
     * 
     * @var array Message templates
     */
    protected $_messageTemplates = array(
        self::ERROR_NO_RECORD_FOUND => "No record matching '%value%' was found",
        self::ERROR_RECORD_FOUND    => "A record matching '%value%' was found",
    );

    /**
     * $_entity - enity name
     * 
     * @var string
     */
    protected $_entity = '';

    /**
     * $_field - field name
     * 
     * @var string
     */
    protected $_field = '';

    /**
     * $_exclude - exlude fields
     * 
     * @var mixed
     */
    protected $_exclude = null;
    
    /**
     * $_em - instance of Enity Manager
     * 
     * @var \Doctrine\ORM\EntityManager  
     */
    protected $_em = null;

    /**
     * Provides basic configuration for use with Zend_Validate_Doctrine Validators
     * Setting $exclude allows a single record to be excluded from matching.
     * Exclude can either be a String containing a where clause, or an array with `field` and `value` keys
     * to define the where clause added to the sql.
     *
     * The following option keys are supported:
     * 'enity'   => The database enity to validate against
     * 'field'   => The field to check for a match
     * 'exclude' => An optional where clause or field/value pair to exclude from the query
     * 'em' => An optional instance of EnityManager to use
     *
     * @param array|Zend_Config $options Options to use for this validator
     */
    public function __construct($options)
    {        
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (func_num_args() > 1) {
            $options       = func_get_args();
            $temp['entity'] = array_shift($options);
            $temp['field'] = array_shift($options);
            if (!empty($options)) {
                $temp['exclude'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['em'] = array_shift($options);
            }

            $options = $temp;
        }     
        
        if (array_key_exists('em', $options)) {
            $this->_em = $options['em'];
        } elseif (Zend_Registry::isRegistered('em')) {
            $this->_em = Zend_Registry::get('em');
        } else {
            throw new Exception("Entity Manager dosen't found!");
        }
        
        if (!array_key_exists('entity', $options)) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception('Entity option missing!');
        } else {
            $this->setenity($options['entity']);    
        }

        if (!array_key_exists('field', $options)) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception('Field option missing!');
        } else {
            $this->setField($options['field']);    
        }

        if (array_key_exists('exclude', $options)) {
            $this->setExclude($options['exclude']);
        }
    }

    /**
     * Returns the set exclude clause
     *
     * @return string|array
     */
    public function getExclude()
    {
        return $this->_exclude;
    }

    /**
     * Sets a new exclude clause
     *
     * @param string|array $exclude
     * @return Extlib_Validate_Doctrine_Abstract
     */
    public function setExclude($exclude)
    {
        $this->_exclude = $exclude;
        return $this;
    }

    /**
     * Returns the set field
     *
     * @return string|array
     */
    public function getField()
    {
        return $this->_field;
    }

    /**
     * Sets a new field
     *
     * @param string $field
     * @return Extlib_Validate_Doctrine_Abstract
     */
    public function setField($field)
    {
        $this->_field = (string) $field;
        return $this;
    }

    /**
     * Returns the set enity
     *
     * @return string
     */
    public function getEnity()
    {
        return $this->_entity;
    }

    /**
     * Sets a new enity
     *
     * @param string $enity
     * @return Extlib_Validate_Doctrine_Abstract
     */
    public function setEnity($enity)
    {
        $this->_entity = (string) $enity;
        return $this;
    }

    /**
     * Run query and returns matches, or null if no matches are found.
     *
     * @param  String $value
     * @return Array when matches are found.
     */
    protected function _query($value)
    {
        $query = $this->_em->createQueryBuilder();
        $query->add('select', "enity.$this->_field")
              ->add('from', "$this->_entity enity")
              ->add('where', "enity.$this->_field = :identifier")
              ->setParameter('identifier', $value);
                
        $exclude = $this->getExclude();                       
                
        if ($exclude && isset($exclude['value'])) {
     
            if (null !== $exclude['value']) {
                $query->andWhere("enity." . $exclude['field'] . " != :" . $exclude['field'])
                      ->setParameter($exclude['field'], $exclude['value']);
             }     
        } elseif (is_array($exclude)) {
            foreach($exclude as $condition) {
                if (is_array($condition) && null !== $condition['value']) {
                    $query->andWhere("enity." . $condition['field'] . " != :" . $condition['field'])
                          ->setParameter($condition['field'], $condition['value']);
                }  
            }
        }

        return $query->getQuery()->execute();
    }
}
