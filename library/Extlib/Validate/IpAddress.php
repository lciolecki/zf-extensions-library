<?php

namespace Extlib\Validate;

/**
 * Ip address validate class
 *
 * @category    Extlib
 * @package     Extlib\Validate
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2011 Lukasz Ciolecki (mart)
 */
class IpAddress extends FilterVar
{
    /**
     * Error message keys
     */
    const INVALID_IP_ADDRESS = 'invalidIp';
    const INVALID_IPV4_ADDRESS = 'invalidIpV4';
    const INVALID_IPV6_ADDRESS = 'invalidIpV6';
     
    /**
     * Array of error messages
     * 
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID_IP_ADDRESS => "'%value%' is not a valid ip address.",
        self::INVALID_IPV4_ADDRESS => "'%value%' is not a valid ip address version 4.",
        self::INVALID_IPV6_ADDRESS => "'%value%' is not a valid ip address version 6."
    );
    
    /**
     * Array of allowed options
     * 
     * @var array
     */
    protected $allowedOptions = array(
        FILTER_FLAG_NONE => self::INVALID_IP_ADDRESS,
        FILTER_FLAG_IPV4 => self::INVALID_IPV4_ADDRESS,
        FILTER_FLAG_IPV6 => self::INVALID_IPV6_ADDRESS
    );
    
    /**
     * Instance of construct
     * 
     * @param mixed $options
     */
    public function __construct($options = array())
    {
        $options = $this->getOptions($options);
        $options['filter'] = FILTER_VALIDATE_IP;
        parent::__construct($options);
    }
}
