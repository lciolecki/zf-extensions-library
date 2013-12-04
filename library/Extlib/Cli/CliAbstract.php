<?php

namespace Extlib\Cli;

/**
 * Abstract, base class of Console line interface
 * 
 * @category    Extlib
 * @package     Extlib\Cli
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2013 Lukasz Ciolecki (mart)
 */
abstract class CliAbstract
{

    /**
     * Instance of application
     *  
     * @var mixed 
     */
    protected $application;

    /**
     * Instance of parameters
     *
     * @var \Zend_Console_Getopt
     */
    protected $opts;

    /**
     * Information about initialize cli script
     * 
     * @var boolean 
     */
    protected $initialized = false;

    /**
     * Array of errors
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Instance of logger
     * 
     * @var \Zend_Log
     */
    protected $logger = null;

    /**
     * Instance of construct
     * 
     * @param \Zend_Console_Getopt $opts
     * @param mixed $application
     * @param \Zend_Log $logger
     */
    public function __construct(\Zend_Console_Getopt $opts, $application, \Zend_Log $logger = null)
    {
        $this->application = $application;
        $this->logger = $logger;
        $this->opts = $opts;
        $this->init();
    }

    /**
     * Instance of desctruct
     */
    public function __destruct()
    {
        $this->buildErrors();
    }

    /**
     * Get errors
     * 
     * @return array
     */
    protected function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set errors
     * 
     * @param array $errors
     * @return \Extlib\Cli\CliAbstract
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Add error
     * 
     * @param string $message
     * @return \Extlib\Cli\CliAbstract
     */
    public function addError($message)
    {
        $this->errors[] = $message;
        return $this;
    }

    /**
     * Check if are any errors
     * 
     * @return boolean
     */
    protected function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Method build and show error message
     * 
     * @return \Extlib\Cli\CliAbstract
     */
    protected function buildErrors()
    {
        if ($this->hasErrors()) {
            echo implode(PHP_EOL, $this->getErrors());
            echo PHP_EOL, $this->opts->getUsageMessage();
        }

        return $this;
    }

    /**
     * Set application
     * 
     * @param mixed $application
     * @return \Extlib\Cli\CliAbstract
     */
    public function setApplication($application)
    {
        $this->application = $application;
        return $this;
    }

    /**
     * Get application
     * 
     * @return mixed
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set options
     * 
     * @param \Zend_Console_Getopt $opts
     * @return \Extlib\Cli\CliAbstract
     */
    public function setOpts(\Zend_Console_Getopt $opts)
    {
        $this->opts = $opts;
        return $this;
    }

    /**
     * Get options
     * 
     * @return \Zend_Console_Getopt
     */
    public function getOpts()
    {
        return $this->opts;
    }

    /**
     * Set initialized 
     * 
     * @param boolean $initialized
     * @return \Extlib\Cli\CliAbstract
     */
    public function setInitialized($initialized)
    {
        $this->initialized = $initialized;
        return $this;
    }

    /**
     * Get initialized
     * 
     * @return boolean
     */
    public function getInitialized()
    {
        return $this->initialized;
    }

    /**
     * Set logger
     * 
     * @param \Zend_Log $logger
     * @return \Extlib\Cli\CliAbstract
     */
    public function setLogger(\Zend_Log $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Get logger
     * 
     * @return \Zend_Log
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Alias for log on logger
     * 
     * @param string $message
     * @param int $priority
     * @param mixed $extras
     * @return \Extlib\Cli\CliAbstract
     */
    public function log($message, $priority, $extras = null)
    {
        if (null !== $this->getLogger()) {
            $this->getLogger()->log($message, $priority, $extras);
        }

        return $this;
    }

}
