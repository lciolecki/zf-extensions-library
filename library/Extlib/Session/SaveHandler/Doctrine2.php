<?php

namespace Extlib\Session\SaveHandler;

/**
 * Session save handler adapter for Doctrine 2. 
 * Note: your entity class must have public access to fields.
 *
 * @category        Extlib
 * @package         Extlib\Session
 * @subpackage      Extlib\Session\SaveHandler
 * @author          Lukasz Ciolecki <ciolecki.lukasz@gmail.com> 
 * @copyright       Copyright (c) Lukasz Ciolecki (mart)
 */
class Doctrine2 implements \Zend_Session_SaveHandler_Interface
{
    /**
     * Definition of options namespace
     */
    const PRIMARY_KEY_COLUMN = 'primaryKeyColumn';
    const DATA_COLUMN = 'dataColumn';
    const LIFETIME_COLUMN = 'lifetimeColumn';
    const MODIFIED_COLUMN = 'modifiedColumn';
    const ENITY_MANAGER = 'enityManager';
    const ENITY_NAME = 'enityName';
    const LIFETIME = 'lifetime';
    const OVERRIDE_LIFETIME = 'overrideLifetime';

    /**
     * Primary key column name
     * 
     * @var string
     */
    protected $primaryKeyColumn = null;

    /**
     * Session content data column name
     * 
     * @var string
     */
    protected $dataColumn = null;

    /**
     * Session lifetime column name
     * 
     * @var string
     */
    protected $lifetimeColumn = null;

    /**
     * Session modify date column name
     * 
     * @var string
     */
    protected $modifiedColumn = null;

    /**
     * Default lifetime session
     * 
     * @var int
     */
    protected $lifetime = false;

    /**
     * Is session life time override
     * 
     * @var boolean
     */
    protected $overrideLifetime = false;

    /**
     * Session entity name
     * 
     * @var string
     */
    protected $entityName = null;

    /**
     * Entity manager
     * 
     * @var \Doctrine\ORM\EntityManager  
     */
    protected $entityManager = null;

    /**
     * Constructor
     *
     * @param  \Zend_Config|array $config
     * @return void
     * @throws \Zend_Session_SaveHandler_Exception
     */
    public function __construct($config)
    {
        if ($config instanceof \Zend_Config) {
            $config = $config->toArray();
        } elseif (!is_array($config)) {
            throw new \Zend_Session_SaveHandler_Exception('$config must be an instance of Zend_Config or array.');
        }

        foreach ($config as $key => $value) {
            switch ($key) {
                case self::DATA_COLUMN:
                    $this->dataColumn = (string) $value;
                    break;
                case self::LIFETIME_COLUMN:
                    $this->lifetimeColumn = (string) $value;
                    break;
                case self::MODIFIED_COLUMN:
                    $this->modifiedColumn = (string) $value;
                    break;
                case self::PRIMARY_KEY_COLUMN:
                    $this->primaryKeyColumn = (string) $value;
                    break;
                case self::LIFETIME:
                    $this->setLifetime($value);
                    break;
                case self::OVERRIDE_LIFETIME:
                    $this->setOverrideLifetime($value);
                    break;
                case self::ENITY_NAME:
                    $this->entityName = (string) $value;
                    break;
                case self::ENITY_MANAGER:
                    $this->setEnityManager($value);
                    break;
                default:
                    break;
            }
        }

        if (!$this->getEnityManager() instanceof \Doctrine\ORM\EntityManager) {
            throw new \Zend_Session_SaveHandler_Exception(sprintf('EnityManager must be an instance of Doctrine\ORM\EntityManager, %s given.', $this->getEnityManager()));
        }
    }

    /**
     * Destructor
     *
     * @return void
     */
    public function __destruct()
    {
        Zend_Session::writeClose();
    }

    /**
     * Set session lifetime and optional whether or not the lifetime of an 
     * existing session should be overridden
     *
     * $lifetime === false resets lifetime to session.gc_maxlifetime
     *
     * @param int $lifetime
     * @param boolean $overrideLifetime (optional)
     * @return \Extlib\Session\SaveHandler\Doctrine2
     * @throws \Zend_Session_SaveHandler_Exception
     */
    public function setLifetime($lifetime, $overrideLifetime = null)
    {
        if ($lifetime < 0) {
            throw new \Zend_Session_SaveHandler_Exception('$lifetime must be greater than 0.');
        } else if (empty($lifetime)) {
            $this->lifetime = (int) ini_get('session.gc_maxlifetime');
        } else {
            $this->lifetime = (int) $lifetime;
        }

        if ($overrideLifetime != null) {
            $this->setOverrideLifetime($overrideLifetime);
        }

        return $this;
    }

    /**
     * Set whether or not the lifetime of an existing session should be 
     * overridden
     *
     * @param boolean $overrideLifetime
     * @return \Extlib\Session\SaveHandler\Doctrine2
     */
    public function setOverrideLifetime($overrideLifetime)
    {
        $this->overrideLifetime = (boolean) $overrideLifetime;
        return $this;
    }

    /**
     * Set entity manager
     * 
     * @param \Doctrine\ORM\EntityManager $enityManager
     * @return \Extlib\Session\SaveHandler\Doctrine2
     */
    public function setEnityManager(\Doctrine\ORM\EntityManager $enityManager)
    {
        $this->entityManager = $enityManager;
        return $this;
    }

    /**
     * Get Enity Manager
     * 
     * @return \Doctrine\ORM\EntityManager   
     */
    public function getEnityManager()
    {
        return $this->entityManager;
    }

    /**
     * Open Session
     *
     * @param string $savePath
     * @param string $name
     * @return boolean
     */
    public function open($savePath, $name)
    {
        return true;
    }

    /**
     * Close session
     *
     * @return boolean
     */
    public function close()
    {
        return true;
    }

    /**
     * Read session data
     *
     * @param string $id
     * @return string
     */
    public function read($id)
    {
        $session = $this->entityManager->find($this->entityManager, $id);
        if (null !== $session) {
            if ($this->_getExpirationTime($session) > time()) {
                return $session->{$this->dataColumn};
            } else {
                $this->destroy($id);
            }
        }

        return '';
    }

    /**
     * Write session data
     *
     * @param string $id
     * @param string $data
     * @return boolean
     */
    public function write($id, $data)
    {
        $session = $this->entityManager->find($this->entityManager, $id);
        if (null === $session) {
            $session = new $this->entityName();
            $session->{$this->primaryKeyColumn} = $id;
        }

        $session->{$this->dataColumn} = $data;
        $session->{$this->lifetimeColumn} = $this->lifetime;
        $session->{$this->modifiedColumn} = time();
        $this->entityManager->persist($session);

        try {
            $this->entityManager->flush();
            return true;
        } catch (\Doctrine\ORM\OptimisticLockException $exc) {
            return false;
        }
    }

    /**
     * Destroy session
     *
     * @param string $id
     * @return boolean
     */
    public function destroy($id)
    {
        $session = $this->entityManager->find($this->entityManager, $id);
        if (false !== $session) {
            try {
                $this->entityManager->remove($session);
                $this->entityManager->flush();
                return true;
            } catch (\Doctrine\ORM\OptimisticLockException $exc) {
                return false;
            }
        }

        return true;
    }

    /**
     * Garbage Collection
     *
     * @param int $maxlifetime
     * @return true
     */
    public function gc($maxlifetime)
    {
        $sessions = $this->entityManager->getRepository($this->entityName)->findAll();
        foreach ($sessions as $session) {
            if ($this->_getExpirationTime($session) < time()) {
                $this->entityManager->remove($session);
            }
        }

        try {
            $this->entityManager->flush();
            return true;
        } catch (\Doctrine\ORM\OptimisticLockException $exc) {
            return false;
        }
    }

    /**
     * Retrieve session lifetime
     *
     * @param mixed $record
     * @return int
     */
    protected function _getLifetime($record)
    {
        if (!$this->overrideLifetime) {
            return $record->{$this->lifetimeColumn};
        }

        return $this->lifetime;
    }

    /**
     * Retrieve session expiration time
     *
     * @param mixed $record
     * @return int
     */
    protected function _getExpirationTime($record)
    {
        return (int) $record->{$this->modifiedColumn} + $this->_getLifetime($record);
    }
}
