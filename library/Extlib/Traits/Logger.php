<?php

namespace Extlib\Traits;

/**
 * Trait logger
 *
 * @category    Extlib
 * @package     Extlib\Traits
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2011 Lukasz Ciolecki (mart)
 */
trait Logger
{
    /**
     * Zend_Log registry namespace
     * 
     * @var string
     */
    static protected $namespace = 'logger';
    
    /**
     * Instance of logger
     * 
     * @var \Zend_Log
     */
    protected $logger = null;
  
    /**
     * Get instance of logger
     * 
     * @return \Zend_Log
     */
    public function getLogger()
    {
        if (null === $this->logger && \Zend_Registry::isRegistered(self::getNamespace())) {
            $this->setLogger(\Zend_Registry::get(self::getNamespace()));
        }
        
        return $this->logger;
    }

    /**
     * Set logger
     * 
     * @return \Extlib\Traits\Logger
     */
    public function setLogger(\Zend_Log $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Log method
     * 
     * @param string $message
     * @param int $priority
     * @param string $extras
     * @return \Extlib\Traits\Logger
     */
    public function log($message, $priority = \Zend_Log::INFO, $extras = null)
    {
        if (null !== $this->getLogger()) {
            $this->getLogger()->log($message, $priority, $extras);
        }
        
        return $this;
    }
    
    /**
     * Get static namespace
     */
    static public function getNamespace()
    {
        return self::$namespace;
    }
    
    /**
     * Set static namespace
     */
    static public function setNamespace($namespace)
    {
        self::$namespace = $namespace;
    }
}
