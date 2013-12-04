<?php

/**
 * Extlib_Session_Validator_UserIpAdress - Session ip adress validator for session
 *
 * @category   Extlib
 * @package    Extlib_Session
 * @subpackage Validator
 * @copyright  Copyright (c) 2010 Łukasz Ciołecki (mart)
 */
class Extlib_Session_Validator_UserIpAdress extends Zend_Session_Validator_Abstract
{

    /**
     * Setup() - this method will get the client's remote address and store
     * it in the session as 'valid data'
     *
     * @return void
     */
    public function setup()
    {
        $this->setValidData((isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null));
    }

    /**
     * Validate() - this method will determine if the client's remote addr
     * matches the remote address we stored when we initialized this variable.
     *
     * @return bool
     */
    public function validate()
    {
        $currentIpAdress = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null);
        return $currentIpAdress === $this->getValidData();
    }

}
