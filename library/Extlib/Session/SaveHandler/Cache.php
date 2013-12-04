<?php

/**
 * Zend_Session_SaveHandler_Cache
 *
 * @category   Extlib
 * @package    Extlib_Session
 * @subpackage SaveHandler
 * @author Łukasz Ciołecki (mart)
 * @copyright  Copyright (c) 2012 Łukasz Ciołecki (mart)
 */
class Extlib_Session_SaveHandler_Cache implements Zend_Session_SaveHandler_Interface
{
    /* Option name list */

    const OPTION_PREFIX = 'prefix';
    const OPTION_CACHE = 'cache';

    /* Session ID separaotr */
    const ID_SEPARATOR = '_';

    /**
     * Zend Cache
     * 
     * @var Zend_Cache_Core
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
                    case self::OPTION_PREFIX:
                        $this->setSessionPrefix($value);
                        break;
                    case self::OPTION_CACHE:
                        $this->setCache($value);
                        break;
                    default:
                        break 2;
                }
                unset($config[$key]);
            } while (false);
        }

        if (null === $this->getCache()) {
            throw new Zend_Session_SaveHandler_Exception('$cache must be an instanc of Zend_Cache_Core.');
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
     * Set Cache
     *
     * @param mixed $cache
     * @return Extlib_Session_SaveHandler_Cache
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
     * @return Zend_Cache_Core
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
     * Set prefix for cache session
     * 
     * @param string $sessionPrefix
     * @return \Extlib_Session_SaveHandler_Cache
     */
    public function setSessionPrefix($sessionPrefix = null)
    {
        $this->sessionPrefix = $sessionPrefix;
        return $this;
    }

    /**
     * Open Session
     *
     * @param string $save_path
     * @param string $name
     * @return boolean
     */
    public function open($save_path, $name)
    {
        $this->_sessionSavePath = $save_path;
        $this->_sessionName = $name;

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
        if (!$data = $this->cache->load($this->normalizeId($id))) {
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
        return $this->cache->save(
                        $data, $this->normalizeId($id), array(), Zend_Session::getOptions('gc_maxlifetime')
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
        return $this->cache->remove($this->normalizeId($id));
    }

    /**
     * Garbage Collection
     *
     * @param int $maxlifetime
     * @return true
     */
    public function gc($maxlifetime)
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
