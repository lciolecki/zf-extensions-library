<?php

namespace Extlib\Validate\Doctrine2;

/**
 * Doctrine v2 abstract class validate
 *
 * @category    Extlib
 * @package     Extlib\Validate
 * @subpackage  Extlib\Validate\Doctrine2
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2013 Lukasz Ciolecki (mart)
 */
abstract class Doctrine2Abstract extends \Zend_Validate_Abstract
{
    /**
     * EntityManager Zend_Registry namespace
     */
    const REGISTY_NAMESPACE = 'em';

    /**
     * Error message keys
     */
    const ERROR_NO_RECORD_FOUND = 'noRecordFound';
    const ERROR_RECORD_FOUND = 'recordFound';

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
     * Enity name
     * 
     * @var string
     */
    protected $entity = '';

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
     * Instance of EnityManager
     * 
     * @var \Doctrine\ORM\EntityManager  
     */
    protected $entityManager = null;

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

        if (isset($options['em'])) {
            $this->setEntity($options['em']);
        } elseif (Zend_Registry::isRegistered(self::REGISTY_NAMESPACE)) {
            $this->setEntityManager(Zend_Registry::get(self::REGISTY_NAMESPACE));
        }

        if (isset($options['entity'])) {
            $this->setEntity($options['entity']);
        }

        if (isset($options['field'])) {
            $this->setField($options['field']);
        }

        if (isset($options['exclude'])) {
            $this->setExclude($options['exclude']);
        }
    }

    /**
     * Get entity name
     * 
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Get check field name
     * 
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Get exclue fields
     * 
     * @return array
     */
    public function getExclude()
    {
        return $this->exclude;
    }

    /**
     * Get EntityManager
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Set entity name
     * 
     * @param string $entity
     * @return \Extlib\Validate\Doctrine2\Doctrine2Abstract
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Set checking filed name
     * 
     * @param string $field
     * @return \Extlib\Validate\Doctrine2\Doctrine2Abstract
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * Set exclude fields
     * 
     * @param array $exclude
     * @return \Extlib\Validate\Doctrine2\Doctrine2Abstract
     */
    public function setExclude(array $exclude)
    {
        $this->exclude = $exclude;
        return $this;
    }
 
    /**
     * Add exclude fields
     * 
     * @param array $exclude
     * @return \Extlib\Validate\Doctrine2\Doctrine2Abstract
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
     * @return \Extlib\Validate\Doctrine2\Doctrine2Abstract
     */
    public function setInclude(array $include)
    {
        $this->include = $include;
        return $this;
    }

    /**
     * Add include fields
     * 
     * @param array $include
     * @return \Extlib\Validate\Doctrine2\Doctrine2Abstract
     */
    public function addInclude(array $include)
    {
        $this->include = array_merge($this->include, $include);
        return $this;
    }

    /**
     * Set EntityManager
     * 
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @return \Extlib\Validate\Doctrine2\Doctrine2Abstract
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * Run query and returns matches, or null if no matches are found.
     *
     * @param  String $value
     * @return Array when matches are found.
     */
    protected function query($value)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select(sprintf('entity.%s', $this->getField()))
                ->from($this->getEntity(), 'entity')
                ->where(sprintf('entity.%s = :field', $this->getField()))
                ->setParameter(':field', $value);

        foreach ($this->getExclude() as $exclude => $value) {
            $query->andWhere(sprintf('entity.%s != :%s', $exclude, $exclude))
                    ->setParameter($exclude, $value);
        }

        foreach ($this->getInclude() as $include => $value) {
            $query->andWhere(sprintf('entity.%s = :%s', $include, $include))
                    ->setParameter($include, $value);
        }

        return $query->getQuery()->execute();
    }
}
