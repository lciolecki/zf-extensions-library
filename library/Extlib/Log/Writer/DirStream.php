<?php

/**
 * Log writer, create dir from current date and priority (old namespace for support zend resource log)
 *
 * @category    Extlib
 * @package     Extlib\Log
 * @subpackage  Extlib\Log\Writer
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2011 Lukasz Ciolecki (mart)
 */
class Extlib_Log_Writer_DirStream extends \Zend_Log_Writer_Abstract
{

    /**
     * Default priority name
     */
    const DEFAULT_PRIORITY_NAME = 'WARN';

    /**
     * Array of sreams log
     * 
     * @var array
     */
    protected $streams = array();

    /**
     * Base dir for log
     * 
     * @var string
     */
    protected $baseDir = null;

    /**
     * Open file mode
     * 
     * @var string
     */
    protected $mode = 'a';

    /**
     * Current date 
     * 
     * @var \DateTime 
     */
    protected $date = null;

    /**
     * Instance of construct
     * 
     * @param string $streamDir
     * @param string $mode
     * @throws \Zend_Log_Exception 
     */
    public function __construct($streamDir, $mode = null)
    {
        if (null !== $mode) {
            $this->mode = $mode;
        }

        if (is_resource($streamDir)) {
            throw new \Zend_Log_Exception('Argument is not a directory path');
        } else {
            $this->baseDir = rtrim($streamDir, DIRECTORY_SEPARATOR);

            if (is_array($streamDir) && isset($streamDir['baseDir'])) {
                $this->baseDir = rtrim($streamDir['baseDir'], DIRECTORY_SEPARATOR);
            }
        }

        $this->_formatter = new \Zend_Log_Formatter_Simple();
        $this->date = new \DateTime('now');
    }

    /**
     * Create a new instance of 
     *
     * @param  array|\Zend_Config $config
     * @return Extlib_Log_Writer_DirStream
     */
    static public function factory($config)
    {
        $config = self::_parseConfig($config);
        $config = array_merge(array('baseDir' => null, 'mode' => null), $config);

        return new self($config['baseDir'], $config['mode']);
    }

    /**
     * Close the stream resource.
     * 
     * @param int $priorityName
     * @return Extlib_Log_Writer_DirStream
     */
    public function shutdown($priorityName = null)
    {
        if ($priorityName === null) {
            foreach ($this->streams as $stream) {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
        } elseif (isset($this->streams[$priorityName]) && is_resource($this->streams[$priorityName])) {
            fclose($this->streams[$priorityName]);
        }

        return $this;
    }

    /**
     * Write a message to the log.
     * 
     * @param array $event
     * @return Extlib_Log_Writer_DirStream
     * @throws \Zend_Log_Exception
     */
    protected function _write($event)
    {
        $priorityName = self::DEFAULT_PRIORITY_NAME;

        if (is_array($event) && isset($event['priorityName'])) {
            $priorityName = $event['priorityName'];
        }

        $line = $this->_formatter->format($event);

        $subPriority = null;

        isset($event['subPriority']) ? $subPriority = $event['subPriority'] : null;

        $stream = $this->_createCurrentStream($priorityName, $subPriority);

        if (false === @fwrite($stream, $line)) {
            throw new \Zend_Log_Exception("Unable to write to stream");
        }

        return $this;
    }

    /**
     * Create current stream for log priority.
     * 
     * @param string $priorityName
     * @param string $subPriority
     * @return resource a file pointer resource
     * @throws Zend_Log_Exception
     */
    private function _createCurrentStream($priorityName, $subPriority = null)
    {
        $priorityName = strtolower($priorityName);

        if ($subPriority !== null) {
            $priorityName = strtolower($subPriority);
        }

        $streamDir = $this->baseDir . DIRECTORY_SEPARATOR . str_ireplace('-', DIRECTORY_SEPARATOR, $this->date->format('Y-m-d'));

        if (!is_dir($streamDir)) {
            umask(0000);
            if (!mkdir($streamDir, 0777, true)) {
                $msg = "Dir '$streamDir' cannot be created";
                throw new \Zend_Log_Exception($msg);
            }

            $this->shutdown($priorityName);
        }

        $filepath = $streamDir . DIRECTORY_SEPARATOR . $priorityName . '.log';
        if (!$this->streams[$priorityName] = @fopen($filepath, $this->mode, false)) {
            $msg = "File '$filepath' cannot be opened with mode '$this->mode'";
            throw new \Zend_Log_Exception($msg);
        }

        return $this->streams[$priorityName];
    }

}
