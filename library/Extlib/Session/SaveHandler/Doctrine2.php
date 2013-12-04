<?php
/**
 * App_Session_SaveHandler_Doctrine - Session SaveHandler standard adapter for Doctrine
 *   
 * @category   Extlib
 * @package    Extlib_Session
 * @subpackage SaveHandler
 * @author Copyright (c) 2012 Łukasz Ciołecki (mart)
 */
class App_Session_SaveHandler_Doctrine2 implements Zend_Session_SaveHandler_Interface
{
    const PRIMARY_KEY_COLUMN    = 'primaryKeyColumn';
    const DATA_COLUMN           = 'dataColumn';
    const LIFETIME_COLUMN       = 'lifetimeColumn';
    const MODIFIED_COLUMN       = 'modifiedColumn';

    const ENITY_MANAGER         = 'enityManager';
    const ENITY_NAME            = 'enityName';
    const LIFETIME              = 'lifetime';
    const OVERRIDE_LIFETIME     = 'overrideLifetime';

    /**
     * $_primaryKeyColumn - enity field name of primary key table
     * 
     * @var string
     */
    protected $_primaryKeyColumn = null;
    
    /**
     * $_dataColumn - enity field name of serialize data
     * 
     * @var string
     */
    protected $_dataColumn = null;

    /**
     * $_lifetimeColumn - enity field column name of life time
     * 
     * @var string
     */
    protected $_lifetimeColumn = null;

    /**
     * $_modifiedColumn - enity field column name of last time visit
     * 
     * @var string
     */
    protected $_modifiedColumn = null;

    /**
     * $_lifetime - session lifetime
     * 
     * @var int
     */
    protected $_lifetime = false;

    /**
     * $_overrideLifetime - information about override session life time
     * 
     * @var boolean
     */
    protected $_overrideLifetime = false;

    /**
     * $_enityName - name of enity 
     * 
     * @var string
     */
    protected $_enityName = null;

    /**
     * $_enityManager - instance of EnityManager
     * 
     * @var \Doctrine\ORM\EntityManager  
     */
    protected $_enityManager = null;
    
    /**
     * Constructor
     *
     * @param  Zend_Config|array $config
     * @return void
     * @throws Zend_Session_SaveHandler_Exception
     */
    public function __construct($config)
    {
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        } elseif (!is_array($config)) {
            require_once 'Zend/Session/SaveHandler/Exception.php';
            throw new Zend_Session_SaveHandler_Exception('$config must be an instance of Zend_Config or array.');
        }

        foreach ($config as $key => $value) {
            do {
                switch ($key) {
                    case self::DATA_COLUMN:
                        $this->_dataColumn = (string) $value;
                        break;
                    case self::LIFETIME_COLUMN:
                        $this->_lifetimeColumn = (string) $value;
                        break;
                    case self::MODIFIED_COLUMN:
                        $this->_modifiedColumn = (string) $value;
                        break;
                    case self::PRIMARY_KEY_COLUMN:
                        $this->setPrimaryKeyColumn($value);
                        break;
                    case self::LIFETIME:
                        $this->setLifetime($value);
                        break;
                    case self::OVERRIDE_LIFETIME:
                        $this->setOverrideLifetime($value);
                        break;
                    case self::ENITY_NAME:
                        $this->setEnityName($value);
                        break;
                    case self::ENITY_MANAGER:
                        $this->setEnityManager($value);
                        break;
                    default:
                        break 2;
                }
                unset($config[$key]);
            } while (false);
        }
        
        if (!$this->getEnityManager() instanceof \Doctrine\ORM\EntityManager) {
            throw new Zend_Session_SaveHandler_Exception("EnityManager must be an instance of \Doctrine\ORM\EntityManager");
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
     * @return \App_Session_SaveHandler_Doctrine2
     * @throws Zend_Session_SaveHandler_Exception
     */
    public function setLifetime($lifetime, $overrideLifetime = null)
    {
        if ($lifetime < 0) {
            require_once 'Zend/Session/SaveHandler/Exception.php';
            throw new Zend_Session_SaveHandler_Exception();
        } else if (empty($lifetime)) {
            $this->_lifetime = (int) ini_get('session.gc_maxlifetime');
        } else {
            $this->_lifetime = (int) $lifetime;
        }

        if ($overrideLifetime != null) {
            $this->setOverrideLifetime($overrideLifetime);
        }

        return $this;
    }

    /**
     * Retrieve session lifetime
     *
     * @return int
     */
    public function getLifetime()
    {
        return $this->_lifetime;
    }

    /**
     * Set whether or not the lifetime of an existing session should be 
     * overridden
     *
     * @param boolean $overrideLifetime
     * @return \App_Session_SaveHandler_Doctrine2
     */
    public function setOverrideLifetime($overrideLifetime)
    {
        $this->_overrideLifetime = (boolean) $overrideLifetime;
        return $this;
    }

    /**
     * Retrieve whether or not the lifetime of an existing session should be 
     * overridden
     *
     * @return boolean
     */
    public function getOverrideLifetime()
    {
        return $this->_overrideLifetime;
    }

    /**
     * Set primary key column
     *
     * @param string|array $key
     * @return \App_Session_SaveHandler_Doctrine2
     * @throws Zend_Session_SaveHandler_Exception
     */
    public function setPrimaryKeyColumn($key = 'id')
    {
        if (is_string($key)) {
            $this->_primaryKeyColumn = $key;
        } else {
            require_once 'Zend/Session/SaveHandler/Exception.php';
            throw new Zend_Session_SaveHandler_Exception('Unable to set primary key column(s).');
        }

        return $this;
    }

    /**
     * Retrieve primary key column
     *
     * @return array
     */
    public function getPrimaryKeyColumn()
    {
        return $this->_primaryKeyColumn;
    }
    
    /**
     * Set EnityManager
     * 
     * @param Doctrine\ORM\EntityManager $enityManager 
     * @return \App_Session_SaveHandler_Doctrine2
     */
    public function setEnityManager($enityManager)
    {
        if (is_string($enityManager) && Zend_Registry::isRegistered($enityManager)) {
            $this->_enityManager = Zend_Registry::get($enityManager);
        } elseif (is_object($enityManager)) {
            $this->_enityManager = $enityManager;   
        }

        return $this;
    }
    
    /**
     * Retrieve Enity Manager
     * 
     * @return Doctrine\ORM\EntityManager   
     */
    public function getEnityManager()
    {
        return $this->_enityManager;
    }    
    /**
     * Set session enity name
     *
     * @param string $name
     * @return \App_Session_SaveHandler_Doctrine2
     */
    public function setEnityName($name = 'Session')
    {
        $this->_enityName = $name;
        return $this;
    }

    /**
     * Retrieve session enity name
     *
     * @return string
     */
    public function getEnityName()
    {
        return $this->_tableName;
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
     * @param string $id Session identifier
     * @return string Session data
     */
    public function read($id)
    {
        $return = '';

        $record = $this->_enityManager->getRepository($this->_enityName)->findOneBy(array($this->_primaryKeyColumn => $id));

        if (null !== $record) {
            if ($this->_getExpirationTime($record) > time()) {
                $return = $record->{$this->_dataColumn};
            } else {
                $this->destroy($id);
            }
        }

        return $return;
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
        $session = $this->_enityManager->getRepository($this->_enityName)->findOneBy(array($this->_primaryKeyColumn => $id));
        if (null === $session) {
            $session = new $this->_enityName();
            $session->{$this->_primaryKeyColumn} = $id;
        }

        $session->{$this->_dataColumn} = $data;
        $session->{$this->_lifetimeColumn} = $this->_lifetime;
        $session->{$this->_modifiedColumn} = time();
        $this->_enityManager->persist($session);
        
        try {
            $this->_enityManager->flush();
            return true;
        } catch (Doctrine\ORM\OptimisticLockException $exc) {
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
        $record = $this->_enityManager->getRepository($this->_enityName)->findOneBy(array($this->_primaryKeyColumn => $id));
        if (false !== $record) {
            try {
                $this->_enityManager->remove($record);
                $this->_enityManager->flush();
                return true;
            } catch (Doctrine\ORM\OptimisticLockException $exc) {
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
        $sessions = $this->_enityManager->getRepository($this->_enityName)->findAll();
        foreach ($sessions as $session) {
            if ($this->_getExpirationTime($session) < time()) {
                $this->_enityManager->remove($session);
            }
        }

        try {
            $this->_enityManager->flush();
            return true;
        } catch (Doctrine\ORM\OptimisticLockException $exc) {
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
        $return = $this->_lifetime;

        if (!$this->_overrideLifetime) {
            $return = (int) $record->{$this->_lifetimeColumn};
        }

        return $return;
    }

    /**
     * Retrieve session expiration time
     *
     * @param mixed $record
     * @return int
     */
    protected function _getExpirationTime($record)
    {
        return (int) $record->{$this->_modifiedColumn} + $this->_getLifetime($record);
    }
}
