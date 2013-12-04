<?php

namespace Extlib\Traits;

/**
 * Trati getter and setter - 
 *
 * @category    Extlib
 * @package     Extlib\Traits
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2011 Lukasz Ciolecki (mart)
 */
trait GetterAndSetter
{
    /**
     * Magic __call
     * 
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __call($method, $args)
    {
        $property = lcfirst(substr($method, 3));

        if (property_exists(\get_class($this), $property)) {
            switch (substr($method, 0, 3)) {
                case 'set':
                    $arg = null;
                    if (isset($args[0])) {
                        $arg = $args[0];
                    }

                    $this->$property = $arg;
                    return $this;
                case 'get':
                    return $this->$property;
            }
        }

        throw new \InvalidArgumentException(sprintf("Call to undefined method '%s' in class '%s'.", $method, get_class($this)));
    }

    /**
     * Magic __set 
     * 
     * @param string $property
     * @param miexed $value
     * @throws \InvalidArgumentException
     */
    public function __set($property, $value)
    {
        $method = 'set' . ucfirst($property);

        if (method_exists($this, $method)) {
            return $this->$method($value);
        } else if (property_exists($this, $property)) {
            $this->$property = $value;
        } else {
            throw new \InvalidArgumentException(sprintf("Call to undefined property '%s' in class '%s'.", $property, get_class($this)));
        }
    }

    /**
     * Magic __get
     * 
     * @param string $property
     * @throws \InvalidArgumentException
     */
    public function __get($property)
    {
        $method = 'get' . ucfirst($property);

        if (method_exists($this, $method)) {
            return $this->$method();
        } else if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new \InvalidArgumentException(sprintf("Call to undefined property '%s' in class '%s'.", $property, get_class($this)));
        }
    }

}
