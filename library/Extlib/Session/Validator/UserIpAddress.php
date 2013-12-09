<?php

namespace Extlib\Session\Validator;

/**
 * Session user ip address valitator
 *
 * @category        Extlib
 * @package         Extlib\Session
 * @subpackage      Extlib\Session\Validator
 * @author          Lukasz Ciolecki <ciolecki.lukasz@gmail.com> 
 * @copyright       Copyright (c) Lukasz Ciolecki (mart)
 */
class UserIpAddress extends \Zend_Session_Validator_Abstract
{
    /**
     * Instance of IpAddess
     * 
     * @var \Extlib\System\IpAddress
     */
    protected $ipAddress = null;
    
    /**
     * Instance of constructor  
     */
    public function __construct()
    {
        $this->setIpAddress(new \Extlib\System\IpAddress());
    }

    /**
     * Method get set user ip address in session store
     *
     * @return void
     */
    public function setup()
    {
        $this->setValidData($this->getIpAddress());
    }

    /**
     * Method validate user ip address
     *
     * @return bool
     */
    public function validate()
    {
        return $this->getIpAddress()->equals($this->getValidData());
    }
    
    /**
     * Get ip address
     * 
     * @return \Extlib\System\IpAddress
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set ip address
     * 
     * @param \Extlib\System\IpAddress $ipAddress
     * @return \Extlib\Session\Validator\UserIpAddress
     */
    public function setIpAddress(\Extlib\System\IpAddress $ipAddress)
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }
}
