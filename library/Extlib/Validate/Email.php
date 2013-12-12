<?php

namespace Extlib\Validate;

/**
 * Email validate class
 *
 * @category    Extlib
 * @package     Extlib\Validate
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2013 Lukasz Ciolecki (mart)
 */
class Email extends FilterVar
{
    /**
     * Error message keys
     */
    const INVALID_EMAIL = 'invalidEmail';
    
    /**
     * Array of error messagess
     * 
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID_EMAIL => "'%value%' is not a valid email address."
    );

    
    /**
     * Array of allowed options
     * 
     * @var array
     */
    protected $allowedOptions = array(
        FILTER_FLAG_NONE => self::INVALID_EMAIL
    );

    /**
     * Instance of construct
     * 
     * @param mixed $options
     */
    public function __construct($options = array())
    {
        $options = $this->getOptions($options);
        $options['filter'] = FILTER_VALIDATE_EMAIL;
        parent::__construct($options);
    }
}
