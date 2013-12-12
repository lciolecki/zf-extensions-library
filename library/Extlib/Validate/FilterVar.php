<?php

namespace Extlib\Validate;

/**
 * FilterVar validate class use filter_var function
 *
 * @category    Extlib
 * @package     Extlib\Validate
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2011 Lukasz Ciolecki (mart)
 */
class FilterVar extends \Zend_Validate_Abstract
{
    /**
     * Filter param for filter_var
     * 
     * @var int
     */
    protected $filter = null;

    /**
     * Array of allowed filters for filter_var
     *
     * @var array
     */
    protected $allowedFilters = array(
        FILTER_VALIDATE_BOOLEAN,
        FILTER_VALIDATE_EMAIL,
        FILTER_VALIDATE_FLOAT,
        FILTER_VALIDATE_INT,
        FILTER_VALIDATE_URL,
        FILTER_VALIDATE_IP
    );

    /**
     * Option param for filter_var
     * 
     * @var int
     */
    protected $option = FILTER_FLAG_NONE;

    /**
     * Array of allowed options for filter_var
     * 
     * @var array
     */
    protected $allowedOptions = array();

    /**
     * Instance of construct
     * 
     * @param mixed $options
     */
    public function __construct($options = array())
    {
        $options = $this->getOptions($options);
        
        if (!isset($options['filter'])) {
            throw new \Zend_Validate_Exception("Option 'filter' is required.");
        } else {
            $this->setFilter($options['filter']);
        }

        if (isset($options['option'])) {
            $this->setOption($options['option']);
        }
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!filter_var($value, $this->getFilter(), $this->getOption())) {
            $this->_error($this->getMessageKey(), $value);
            return false;
        }

        return true;
    }

    /**
     * Return message key
     * 
     * @return string
     */
    public function getMessageKey()
    {
        return $this->allowedOptions[$this->option];
    }

    /**
     * Set option
     * 
     * @param int $option
     * @return \Extlib\Validate\IpAddress
     * @throws \Zend_Validate_Exception
     */
    public function setOption($option)
    {
        if (!array_key_exists($option, $this->allowedOptions)) {
            throw new \Zend_Validate_Exception('Invalid param option.');
        }

        $this->option = (int) $option;
        return $this;
    }

    /**
     * Get option
     * 
     * @return int
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Set filter
     * 
     * @param string $filter
     * @return \Extlib\Validate\FilterVar
     * @throws \Zend_Validate_Exception
     */
    public function setFilter($filter)
    {
        if (!in_array($filter, $this->allowedFilters)) {
            throw new \Zend_Validate_Exception('Invalid param filter.');
        }

        $this->filter = (int) $filter;
        return $this;
    }

    /**
     * Get filter
     * 
     * @return int
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Normalize options
     * 
     * @param miexed $options
     * @return array
     * @throws \Zend_Validate_Exception
     */
    public function getOptions($options = array())
    {
        if ($options instanceof \Zend_Config) {
            return $options->toArray();
        } elseif (!is_array($options)) {
            throw new \Zend_Validate_Exception('$config must be an instance of Zend_Config or array.');
        }
        
        return $options;
    }
}
