<?php
/**
 * Extlib_Validate_Doctrine_Abstract - Class for Doctrine record validation
 *
 * @category   Extlib
 * @package    Extlib_Validate
 * @uses       Extlib_Validate_Abstract
 * @copyright  Copyright (c) 2012 Łukasz Ciołecki (Mart)
 */
abstract class Extlib_Validate_Doctrine_Abstract extends Zend_Validate_Abstract
{
    /**
     * Error constants
     */
    const ERROR_NO_RECORD_FOUND = 'noDoctrineRecordFound';
    const ERROR_RECORD_FOUND    = 'doctrineRecordFound';

    /**
     * $_messageTemplates - message templates
     * 
     * @var array
     */
    protected $_messageTemplates = array(
        self::ERROR_NO_RECORD_FOUND => "No record matching '%value%' was found",
        self::ERROR_RECORD_FOUND    => "A record matching '%value%' was found",
    );

    /**
     * $_table - table name
     * 
     * @var string
     */
    protected $_table = '';

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
    protected $_exclude = array();

    /**
     * Provides basic configuration for use with Zend_Validate_Doctrine Validators
     * Setting $exclude allows a single record to be excluded from matching.
     * Exclude can either be a String containing a where clause, or an array with `field` and `value` keys
     * to define the where clause added to the sql.
     *
     * The following option keys are supported:
     * 'table'   => The database table to validate against
     * 'field'   => The field to check for a match
     * 'exclude' => An optional where clause or field/value pair to exclude from the query
     * 'adapter' => An optional database adapter to use
     *
     * @param array|Zend_Config $options Options to use for this validator
     */
    public function __construct($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (func_num_args() > 1) {
            $options       = func_get_args();
            $temp['table'] = array_shift($options);
            $temp['field'] = array_shift($options);
            if (!empty($options)) {
                $temp['exclude'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['adapter'] = array_shift($options);
            }

            $options = $temp;
        }         
        
        if (!array_key_exists('table', $options)) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception('Table option missing!');
        } else {
            $this->setTable($options['table']);    
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
     * @return array
     */
    public function getExclude()
    {
        return $this->_exclude;
    }

    /**
     * Sets a new exclude clause
     *
     * @param array $exclude
     * @return Extlib_Validate_Doctrine_Abstract
     */
    public function setExclude(array $exclude)
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
     * Returns the set table
     *
     * @return string
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * Sets a new table
     *
     * @param string $table
     * @return Extlib_Validate_Doctrine_Abstract
     */
    public function setTable($table)
    {
        $this->_table = (string) $table;
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

        $query = Doctrine_Query::create()
                               ->select($this->getField())
                               ->from($this->getTable())
                               ->where($this->getField() . '=?', $value);
        
        $query = $this->_addExcludeQuery($query);
        $result = $query->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
        return $result;
    }
    
    /**
     * Add exclude subquery
     * 
     * @param Doctrine_Query $query
     * @return \Doctrine_Query
     */
    protected function _addExcludeQuery(Doctrine_Query $query)
    {
        $exclude = $this->getExclude();                       
                
        if (isset($exclude['value']) && null !== $exclude['value'] && isset($exclude['field'])) {
            $query->andWhere($exclude['field'] . ' != ?', $exclude['value']);         
        } else {
            foreach($exclude as $condition) {
                if (is_array($condition) && isset($condition['value']) && null !== $condition['value'] && isset($condition['field'])) {
                    $query->andWhere($condition['field'] . ' != ?', $condition['value']);       
                }  
            }
        }
        
        return $query;
    }
}
