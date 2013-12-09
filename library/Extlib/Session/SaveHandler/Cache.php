<?php

namespace Extlib\Session\SaveHandler;

/**
 * Session seave handler adapter for \Zend_Cache
 *
 * @category        Extlib
 * @package         Extlib\Session
 * @subpackage      Extlib\Session\SaveHandler
 * @author          Lukasz Ciolecki <ciolecki.lukasz@gmail.com> 
 * @copyright       Copyright (c) Lukasz Ciolecki (mart)
 */
class Cache implements \Zend_Session_SaveHandler_Interface
{
    /**
     * Definition of options namespace
     */
    const OPTION_PREFIX = 'prefix';
    const OPTION_CACHE = 'cache';

    /**
     * Session separator
     */
    const ID_SEPARATOR = '_';

    /**
     * \Zend Cache
     * 
     * @var \Zend_Cache_Core
     */
    protected $cache = null;

    /**
     * Prefix for cache session
     * 
     * @var string
     */
    protected $sessionPrefix = null;

    /**
     * Constructor
     *
     * @param  \Zend_Config|array $config
     * @return void
     * @throws \Zend_Session_SaveHandler_Exception
     */
    public function __construct($config)
    {
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        } elseif (!is_array($config)) {
            throw new \Zend_Session_SaveHandler_Exception('$config must be an instance of \Zend_Config or array.');
        }

        foreach ($config as $key => $value) {
            switch ($key) {
                case self::OPTION_PREFIX:
                    $this->setSessionPrefix($value);
                    break;
                case self::OPTION_CACHE:
                    $this->setCache($value);
                    break;
                default:
                    break;
            }
        }

        if (null === $this->getCache()) {
            throw new \Zend_Session_SaveHandler_Exception('$cache must be an instanc of Zend_Cache_Core.');
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
     * Set cache
     * 
     * @param mixed $cache
     * @return \Extlib\Session\SaveHandler\Cache
     * @throws \Zend_Session_SaveHandler_Exception
     */
    public function setCache($cache)
    {
        if ($cache instanceof \Zend_Cache_Core) {
            $this->cache = $cache;
        } elseif ($cache instanceof \Zend_Config) {
            $this->cache = \Zend_Cache::factory($cache->toArray());
        } elseif (is_array($cache)) {
            $this->cache = \Zend_Cache::factory($cache);
        } else {
            throw new \Zend_Session_SaveHandler_Exception('Cache options must be in an associative array or instance of Zend_Config or Zend_Cache_Core.');
        }

        return $this;
    }

    /**
     * Get Cache
     * 
     * @return \Zend_Cache_Core
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Get prefix for cache session
     * 
     * @return string
     */
    public function getSessionPrefix()
    {
        return $this->sessionPrefix;
    }

    /**
     * Set session prefix
     * 
     * @param string $sessionPrefix
     * @return \Extlib\Session\SaveHandler\Cache
     */
    public function setSessionPrefix($sessionPrefix = null)
    {
        $this->sessionPrefix = $sessionPrefix;
        return $this;
    }

    /**
     * Read session data
     *
     * @param string $id
     * @return string
     */
    public function read($id)
    {
        if (!$data = $this->getCache()->load($this->normalizeId($id))) {
            return null;
        }

        return $data;
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
        return $this->getCache()->save(
            $data, 
            $this->normalizeId($id), 
            array(), 
            \Zend_Session::getOptions('gc_maxlifetime')
        );
    }

    /**
     * Destroy session
     *
     * @param string $id
     * @return boolean
     */
    public function destroy($id)
    {
        return $this->getCache()->remove($this->normalizeId($id));
    }

    /**
     * Garbage Collection - implementation of interface
     *
     * @param int $maxlifetime
     * @return true
     */
    public function gc($maxlifetime)
    {
        return true;
    }

    /**
     * Open Session - implementation of interface
     *
     * @param string $save_path
     * @param string $name
     * @return boolean
     */
    public function open($save_path, $name)
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
     * Method prepare id
     * 
     * @param string $id
     * @return string
     */
    protected function normalizeId($id)
    {
        if (null !== $this->getSessionPrefix()) {
            return $this->getSessionPrefix() . self::ID_SEPARATOR . $id;
        }

        return $id;
    }
}
