<?php

/**
 * Doctrine v1.2 log writer (old namespace for zend support resource log)
 *
 * @category        Extlib
 * @package         Extlib\Log
 * @subpackage      Extlib\Log\Writer
 * @author          Matthew Lurz (http://framework.zend.com/wiki/display/ZFPROP/Zend_Log_Writer_Doctrine+-+Matthew+Lurz)
 * @modification    Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright       Copyright (c) Matthew Lurz
 */
class Extlib_Log_Writer_Doctrine extends \Zend_Log_Writer_Abstract
{

    /**
     * Class model name
     * 
     * @var string
     */
    protected $modelClass = null;

    /**
     * Array of model columns
     * 
     * @var array
     */
    protected $columnMap = array();

    /**
     * Instance of constructor
     *
     * @param   string $modelClass
     * @param   array $columnMap
     * @throws  \Zend_Log_Exception
     */
    public function __construct($modelClass, array $columnMap = array())
    {
        if (!class_exists($modelClass)) {
            throw new \Zend_Log_Exception('Invalid model class.');
        }

        $this->setColumnMap($columnMap);
        $this->setModelClass($modelClass);
    }

    /**
     * Write a message to the log
     *
     * @param array $event
     * @return \Extlib_Log_Writer_Doctrine
     */
    protected function _write($event)
    {
        $data = array();
        if (empty($this->getColumnMap())) {
            $data = $event;
        } else {
            foreach ($this->getColumnMap() as $name => $value) {
                if (isset($event[$name])) {
                    $data[$name] = $event[$name];
                }
            }
        }

        $entry = new $this->modelClass();
        $entry->fromArray($data);
        $entry->save();

        return $this;
    }

    /**
     * Create a new instance of 
     *
     * @param  array|\Zend_Config $config
     * @return \Extlib_Log_Writer_Doctrine
     */
    static public function factory($configs)
    {
        $config = self::_parseConfig($config);
        $config = array_merge(array('modelClass' => null, 'columnMap' => null), $config);
        return new self($config['modelClass'], $config['columnMap']);
    }

    /**
     * Get model class name
     * 
     * @return string
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * Set model class name
     * 
     * @param string $modelClass
     * @return \Extlib_Log_Writer_Doctrine
     */
    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
        return $this;
    }

    /**
     * Get model columns
     * 
     * @return array
     */
    public function getColumnMap()
    {
        return $this->columnMap;
    }

    /**
     * Set model columns
     * 
     * @param array $columnMap
     * @return \Extlib_Log_Writer_Doctrine
     */
    public function setColumnMap(array $columnMap)
    {
        $this->columnMap = $columnMap;
        return $this;
    }

    /**
     * Disable formatting
     *
     * @param   mixed $formatter
     * @return  void
     * @throws  \Zend_Log_Exception
     */
    public function setFormatter($formatter)
    {
        throw new \Zend_Log_Exception('Formatting is not supported.');
    }

}
