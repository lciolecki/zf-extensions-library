<?php

namespace Extlib\Session\SaveHandler;

/**
 * Session save handler adapter for Doctrine 1.2
 *
 * @category        Extlib
 * @package         Extlib\Session
 * @subpackage      Extlib\Session\SaveHandler
 * @author          Lukasz Ciolecki <ciolecki.lukasz@gmail.com> 
 * @copyright       Copyright (c) Lukasz Ciolecki (mart)
 */
class Doctrine implements \Zend_Session_SaveHandler_Interface
{
    /**
     * Definition of options namespace
     */
    const PRIMARY_KEY_COLUMN = 'primaryKeyColumn';
    const DATA_COLUMN = 'dataColumn';
    const LIFETIME_COLUMN = 'lifetimeColumn';
    const MODIFIED_COLUMN = 'modifiedColumn';
    const TABLE_NAME = 'tableName';
    const LIFETIME = 'lifetime';
    const OVERRIDE_LIFETIME = 'overrideLifetime';

    /**
     * Primary key column name
     * 
     * @var string
     */
    protected $_primaryKeyColumn = null;

    /**
     * Session content data column name
     * 
     * @var string
     */
    protected $_dataColumn = null;

    /**
     * Session lifetime column name
     * 
     * @var string
     */
    protected $_lifetimeColumn = null;

    /**
     * Session modify date column name
     * 
     * @var string
     */
    protected $_modifiedColumn = null;

    /**
     * Default lifetime session
     * 
     * @var int
     */
    protected $_lifetime = false;

    /**
     * Is session life time override
     * 
     * @var boolean
     */
    protected $_overrideLifetime = false;

    /**
     * Session table name
     * 
     * @var string
     */
    protected $_tableName = null;

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
                    $this->_dataColumn = (string) $value;
                    break;
                case self::LIFETIME_COLUMN:
                    $this->_lifetimeColumn = (string) $value;
                    break;
                case self::MODIFIED_COLUMN:
                    $this->_modifiedColumn = (string) $value;
                    break;
                case self::PRIMARY_KEY_COLUMN:
                    $this->_primaryKeyColumn = (string) $value;
                    break;
                case self::TABLE_NAME:
                    $this->_tableName = (string) $value;
                    break;
                case self::LIFETIME:
                    $this->_lifetime = $this->setLifetime($value);
                    break;
                case self::OVERRIDE_LIFETIME:
                    $this->_overrideLifetime = $this->setOverrideLifetime($value);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Destructor
     *
     * @return void
     */
    public function __destruct()
    {
        \Zend_Session::writeClose();
    }

    /**
     * Set session lifetime and optional whether or not the lifetime of an 
     * existing session should be overridden
     *
     * $lifetime === false resets lifetime to session.gc_maxlifetime
     * 
     * @param int $lifetime
     * @param boolean $overrideLifetime
     * @return \Extlib\Session\SaveHandler\Doctrine
     * @throws \Zend_Session_SaveHandler_Exception
     */
    public function setLifetime($lifetime, $overrideLifetime = null)
    {
        if ($lifetime < 0) {
            throw new \Zend_Session_SaveHandler_Exception('$lifetime must be greater than 0.');
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
     * Set whether or not the lifetime of an existing session should be 
     * overridden
     * 
     * @param boolean $overrideLifetime
     * @return \Extlib\Session\SaveHandler\Doctrine
     */
    public function setOverrideLifetime($overrideLifetime)
    {
        $this->_overrideLifetime = (boolean) $overrideLifetime;
        return $this;
    }

    /**
     * Open Session - implementation of interface
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
     * Close session - implementation of interface
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
        $session = \Doctrine::getTable($this->_tableName)->find($id);
        if (false !== $session) {
            if ($this->_getExpirationTime($session) > time()) {
                return $session->{$this->_dataColumn};
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
        $session = \Doctrine::getTable($this->_tableName)->find($id);
        if (false === $session) {
            $session = new $this->_tableName();
            $session->{$this->_primaryKeyColumn} = $id;
        }

        $session->{$this->_dataColumn} = $data;
        $session->{$this->_lifetimeColumn} = $this->_lifetime;
        $session->{$this->_modifiedColumn} = time();

        if ($session->save()) {
            return true;
        }

        return false;
    }

    /**
     * Destroy session
     *
     * @param string $id
     * @return boolean
     */
    public function destroy($id)
    {
        $return = false;

        $record = \Doctrine::getTable($this->_tableName)->find($id);
        if (false !== $record) {
            if ($record->delete()) {
                $return = true;
            }
        }

        return $return;
    }

    /**
     * Garbage Collection
     *
     * @param int $maxlifetime
     * @return true
     */
    public function gc($maxlifetime)
    {
        $sessions = \Doctrine::getTable($this->_tableName)->findAll();
        foreach ($sessions as $session) {
            if ($this->_getExpirationTime($session) < time()) {
                $session->delete();
            }
        }

        return true;
    }

    /**
     * Retrieve session lifetime
     *
     * @param \Doctrine_Record $record
     * @return int
     */
    public function _getLifetime(\Doctrine_Record $record)
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
     * @param \Doctrine_Record $record
     * @return int
     */
    protected function _getExpirationTime(\Doctrine_Record $record)
    {
        return (int) $record->{$this->_modifiedColumn} + $this->_getLifetime($record);
    }
}
