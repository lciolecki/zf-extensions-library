<?php

namespace Extlib;

/**
 * EventManager cache listener
 * 
 * @category    Extlib
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2012 Lukasz Ciolecki (mart)
 */
class CacheListener implements \Zend_EventManager_ListenerAggregate
{
    /* Name of events */
    const EVENT_PRE = 'cache.pre';
    const EVENT_POST = 'cache.post';
    const EVENT_CLEAN = 'cache.clean';
    const EVENT_REMOVE = 'cache.remove';

    /* Valida name for id and tag format */
    const VALID_NAME_FORMAT = '/[^a-zA-Z0-9_]/';
    const REPLACE_FORMAT_CHAR = '';
    
    /**
     * Instance of Zend_Cache_Core
     *
     * @var Zend_Cache_Core
     */
    protected $cache = null;

    /**
     * Array of listeners 
     *
     * @var array 
     */
    protected $listeners = array();

    /**
     * Instance of construct
     * 
     * @param \Zend_Cache_Core $cache
     */
    public function __construct(\Zend_Cache_Core $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Implementation Zend_EventManager_ListenerAggregate
     * 
     * @param \Zend_EventManager_EventCollection $events
     */
    public function attach(\Zend_EventManager_EventCollection $events)
    {
        $this->listeners[] = $events->attach(self::EVENT_PRE, array($this, 'load'), 100);
        $this->listeners[] = $events->attach(self::EVENT_POST, array($this, 'save'), -100);
        $this->listeners[] = $events->attach(self::EVENT_REMOVE, array($this, 'remove'), 50);
        $this->listeners[] = $events->attach(self::EVENT_CLEAN, array($this, 'clean'), 0);
    }

    /**
     * Implementation Zend_EventManager_ListenerAggregate
     * 
     * @param \Zend_EventManager_EventCollection $events
     */
    public function detach(\Zend_EventManager_EventCollection $events)
    {
        foreach($this->listeners as $index => $listener) {
            if($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Pre event - try load content from cache
     * 
     * @param \Zend_EventManager_EventDescription $event
     * @return mixed
     */
    public function load(\Zend_EventManager_EventDescription $event)
    {
        if(false !== ($content = $this->cache->load($this->_getIdentifier($event)))) {
            $event->stopPropagation(true);
            return $content;
        }
    }

    /**
     * Post event - save content in cache
     * 
     * @param \Zend_EventManager_EventDescription $event
     */
    public function save(\Zend_EventManager_EventDescription $event)
    {
        $params = $event->getParams();
        if(!isset($params['data'])) {
            throw new \Zend_EventManager_Exception_InvalidArgumentException('Missing param data.');
        }

        $this->cache->save($params['data'], $this->_getIdentifier($event), $this->_getTags($event));
    }

    /**
     * Clear event - clear all cache by tags
     * 
     * @param Zend_EventManager_EventDescription $event
     */
    public function clean(\Zend_EventManager_EventDescription $event)
    {      
        $params = $event->getParams();
        $mode = \Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG;

        if (isset($params['mode'])) {
            $mode = $params['mode'];   
        }

        return $this->cache->clean($mode, $this->_getTags($event));
    }
    
    /**
     * Remove event - remove cache by id
     * 
     * @param \Zend_EventManager_EventDescription $event
     * @return boolean
     */
    public function remove(\Zend_EventManager_EventDescription $event)
    {
        return $this->cache->remove($this->_getIdentifier($event));
    }
    
    /**
     * Method return identifier name from class and id
     * 
     * @param \Zend_EventManager_EventDescription $event
     * @return string
     * @throws \Zend_EventManager_Exception_InvalidArgumentException
     */
    protected function _getIdentifier(\Zend_EventManager_EventDescription $event)
    {
        $params = $event->getParams();
        if(!isset($params['id'])) {
            throw new \Zend_EventManager_Exception_InvalidArgumentException('Missing param id.');
        }

        return md5($this->_getClassName($event->getTarget()) . '-' . $params['id']);
    }

    /**
     * Method return target class name
     * 
     * @param mixed $target
     * @return string
     */
    protected function _getClassName($target)
    {
        $className = $target;
        if (is_object($target)) {
            $className = get_class($target);
        }

        return $this->_normalizeName($className);
    }

    /**
     * Method return array of tags from event
     * 
     * @param \Zend_EventManager_EventDescription $event
     * @return array
     */
    protected function _getTags(\Zend_EventManager_EventDescription $event)
    {
        $params = $event->getParams();

        $tags = array($this->_getClassName($event->getTarget()));

        if(isset($params['tags'])) {
            if(is_array($params['tags'])) {
                $tags = array_merge($tags, $params['tags']);
            } else {
                $tags[] = $params['tags'];
            }
        }
        
        foreach ($tags as &$tag) {
            $tag = $this->_normalizeName($tag);
        }

        return $tags;
    }
    
    /**
     * Method normalize tag or id name
     * 
     * @param string $name
     * @return string
     */
    protected function _normalizeName($name)
    {
        return preg_replace(self::VALID_NAME_FORMAT, self::REPLACE_FORMAT_CHAR, $name);
    }
}