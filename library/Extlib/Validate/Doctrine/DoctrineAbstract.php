<?php

namespace Extlib\Validate\Doctrine;

/**
 * Doctrine v1.2 abstract class validate
 *
 * @category    Extlib
 * @package     Extlib\Validate
 * @subpackage  Extlib\Validate\Doctrine
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2012 Łukasz Ciołecki (mart)
 */
abstract class DoctrineAbstract extends \Zend_Validate_Abstract
{
    /**
     * Error message keys
     */
    const ERROR_NO_RECORD_FOUND = 'noDoctrineRecordFound';
    const ERROR_RECORD_FOUND = 'doctrineRecordFound';

    /**
     * Array of error messages
     * 
     * @var array Message templates
     */
    protected $_messageTemplates = array(
        self::ERROR_NO_RECORD_FOUND => "No record matching '%value%' was found",
        self::ERROR_RECORD_FOUND => "A record matching '%value%' was found",
    );

    /**
     * Instance of connection
     * 
     * @var \Doctrine_Connection
     */
    protected $connection = null;

    /**
     * Table model class name
     * 
     * @var string
     */
    protected $table = '';

    /**
     * Field name
     * 
     * @var string
     */
    protected $field = '';

    /**
     * Eexlude fields
     * 
     * field => value
     * 
     * @var array
     */
    protected $exclude = array();

    /**
     * Include fields
     * 
     * field => value
     * 
     * @var array
     */
    protected $include = array();

    /**
     * Instance of construct
     * 
     * @param mixed $options
     * @throws \Zend_Validate_Exception
     */
    public function __construct($options)
    {
        if ($options instanceof \Zend_Config) {
            $options = $options->toArray();
        } elseif (!is_array($options)) {
            throw new \Zend_Validate_Exception('$config must be an instance of Zend_Config or array.');
        }

        if (isset($options['connection'])) {
            $this->setConnection($options['connection']);
        }

        if (isset($options['table'])) {
            $this->setTable($options['table']);
        }

        if (isset($options['field'])) {
            $this->setField($options['field']);
        }

        if (isset($options['exclude'])) {
            $this->setExclude($options['exclude']);
        }

        if (isset($options['include'])) {
            $this->setInclude($options['include']);
        }
    }

    /**
     * Get doctrine connection
     * 
     * @return \Doctrine_Connection
     */
    public function getConnection()
    {
        if (null === $this->connection) {
            $this->setConnection(\Doctrine_Manager::getInstance()->getCurrentConnection());
        }

        return $this->connection;
    }

    /**
     * Set doctrine connection
     * 
     * @param \Doctrine_Connection $connection
     * @return \Extlib\Validate\Doctrine\DoctrineAbstract
     */
    public function setConnection(\Doctrine_Connection $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * Get table model name
     * 
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Set table model name
     * 
     * @param string $table
     * @return \Extlib\Validate\Doctrine\DoctrineAbstract
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Get checking field
     * 
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set checking field
     * 
     * @param string $field
     * @return \Extlib\Validate\Doctrine\DoctrineAbstract
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * Get exclude fields
     * 
     * @return array
     */
    public function getExclude()
    {
        return $this->exclude;
    }

    /**
     * Set exclude fields
     * 
     * @param array $exclude
     * @return \Extlib\Validate\Doctrine\DoctrineAbstract
     */
    public function setExclude(array $exclude = array())
    {
        $this->exclude = $exclude;
        return $this;
    }

    /**
     * Add exclude fields
     * 
     * @param array $exclude
     * @return \Extlib\Validate\Doctrine\DoctrineAbstract
     */
    public function addExclude(array $exclude)
    {
        $this->exclude = array_merge($this->exclude, $exclude);
        return $this;
    }

    /**
     * Get include fields
     * 
     * @return array
     */
    public function getInclude()
    {
        return $this->include;
    }

    /**
     * Set include fields
     * 
     * @param array $include
     * @return \Extlib\Validate\Doctrine\DoctrineAbstract
     */
    public function setInclude(array $include = array())
    {
        $this->include = $include;
        return $this;
    }

    /**
     * Add include fields
     * 
     * @param array $include
     * @return \Extlib\Validate\Doctrine\DoctrineAbstract
     */
    public function addInclude(array $include)
    {
        $this->include = array_merge($this->include, $include);
        return $this;
    }

    /**
     * Run query and returns matches, or null if no matches are found.
     *
     * @param  string $value
     * @return array
     */
    protected function query($value)
    {
        $query = \Doctrine_Query::create($this->getConnection())
                ->select($this->getField())
                ->from($this->getTable())
                ->where(sprintf('%s = ?', $this->getField()), $value);

        foreach ($this->getExclude() as $exclude => $value) {
            $query->andWhere(sprintf('%s != ?', $exclude), $value);
        }

        foreach ($this->getInclude() as $include => $value) {
            $query->andWhere(sprintf('%s = ?', $include), $value);
        }

        return $query->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
    }
}
