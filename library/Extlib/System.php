<?php
namespace Extlib;

/**
 * System class contained all information about user, server and actual data
 * 
 * @category   Extlib
 * @copyright  Copyright (c) 2013 Łukasz Ciołecki (Mart)
 */
final class System
{   
    const DEFAULT_IP = '127.0.0.1';
    
    /**
     * Instanc of singleton
     *
     * @var \Extlib\System 
     */
    static private $instance = null;

    /**
     * Actual time
     *
     * @var \DateTime 
     */
    protected $date = null;
    
    /**
     * Ip address
     *
     * @var string 
     */
    protected $ipAddress = null;

    /**
     * Instance of \Extlib\Browser
     *
     * @var \Extlib\Browser 
     */
    protected $browser = null;

    /**
     * Domain name
     * 
     * @var string
     */
    protected $domain = null;
    
    protected $phpVersion = null;
    
    protected $apacheVersion = null;
    
    protected $mysqlVersion = null;
    
    protected $postgreVersion = null;

    /**
     * Instance of construct
     */
    private function __construct()
    {
        $this->browser = new Browser();
        $this->date = new \DateTime('now');
        $this->phpVersion = phpversion();

        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = trim(end(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));
        } elseif(isset($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipAddress = self::DEFAULT_IP;
        }

        $ipValidatior = new \Zend_Validate_Ip(array('allowipv6' => true, 'allowipv4' => true));
        if (!$ipValidatior->isValid($ipAddress)) {
            throw new \InvalidArgumentException('Illegal value for IP remote host! Possible attempt to attack Man In The Middle.');
        }

        $this->ipAddress = $ipAddress;
        
        if (isset($_SERVER['HTTP_HOST'])) {
            $this->domain = isset($_SERVER['HTTPS']) ? 'https:' : 'http:' . '//' . $_SERVER['HTTP_HOST'];
        }
    }
    
    /**
     *  Method return instance of System
     * 
     * @return \Extlib\System
     */
    static public function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new System();
        }

        return self::$instance;
    }

    /**
     * Method return actual DateTime object
     * 
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Method return instance of \Extlib\Browser
     * 
     * @return \Extlib\Browser
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * Method return ip address
     * 
     * @param boolean $ip2long
     * @return mixed
     */
    public function getIpAddress($ip2long = false)
    {
        if ($ip2long) {
            return ip2long($this->ipAddress);
        }
        
        return $this->ipAddress;
    }

    /**
     * Method return domain name
     * 
     * @param boolean $nameOnly
     * @return string
     */
    public function getDomain($nameOnly = false)
    {
        if ($nameOnly && isset($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }
        
        return $this->domain;
    }
}