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

    /**
     * Name of events
     */
    const EVENT_PRE = 'cache.pre';
    const EVENT_POST = 'cache.post';
    const EVENT_CLEAN = 'cache.clean';

    /**
     * Instance of Zend_Cache_Core
     *
     * @var \Zend_Cache_Core
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
        $this->listeners[] = $events->attach(self::EVENT_CLEAN, array($this, 'clean'), 50);
    }

    /**
     * Implementation Zend_EventManager_ListenerAggregate
     * 
     * @param \Zend_EventManager_EventCollection $events
     */
    public function detach(\Zend_EventManager_EventCollection $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Pre event - try load content from cache
     * 
     * @param \Zend_EventManager_EventDescription $event
     * @return mixed|false
     */
    public function load(\Zend_EventManager_EventDescription $event)
    {
        $params = $event->getParams();
        if (!isset($params['id'])) {
            throw new \Zend_EventManager_Exception_InvalidArgumentException('Missing param id');
        }

        $class = $this->_getClassName($event->getTarget());
        $id = md5($class . '-' . $params['id']);

        if (false !== ($content = $this->cache->load($id))) {
            $event->stopPropagation(true);
            return $content;
        }

        return false;
    }

    /**
     * Post event - save content in cache
     * 
     * @param \Zend_EventManager_EventDescription $event
     */
    public function save(\Zend_EventManager_EventDescription $event)
    {
        $params = $event->getParams();
        if (!isset($params['id'])) {
            throw new \Zend_EventManager_Exception_InvalidArgumentException('Missing param id.');
        }

        if (!isset($params['data'])) {
            throw new \Zend_EventManager_Exception_InvalidArgumentException('Missing param data.');
        }

        $tags = $this->_getTags($event);
        $class = $this->_getClassName($event->getTarget());
        $id = md5($class . '-' . $params['id']);

        $this->cache->save($params['data'], $id, $tags);
    }

    /**
     * Clena event - clear all cache by tags (class name)
     * 
     * @param \Zend_EventManager_EventDescription $event
     */
    public function clean(\Zend_EventManager_EventDescription $event)
    {
        $this->cache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, $this->_getTags($event));
    }

    /**
     * Method return target class name
     * 
     * @param mixed $target
     * @return string
     */
    protected function _getClassName($target)
    {
        if (is_object($target)) {
            return get_class($target);
        }

        return $target;
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

        if (isset($params['tags'])) {
            if (is_array($params['tags'])) {
                $tags = array_merge($tags, $params['tags']);
            } else {
                $tags[] = $params['tags'];
            }
        }

        return $tags;
    }

}
