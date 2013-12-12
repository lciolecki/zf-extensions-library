<?php

namespace Extlib\Validate;

/**
 * Url validate class
 *
 * @category    Extlib
 * @package     Extlib\Validate
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2013 Lukasz Ciolecki (mart)
 */
class Url extends FilterVar
{
    /**
     * Message keys
     */
    const INVALID_URL = 'invalidUrl';
    const REQUIRED_PATH = 'requiredPathUrl';
    const REQUIRED_QUERY = 'requiredQueryUrl';
    
    /**
     * Array of error messages
     * 
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID_URL => "'%value%' is not a valid URL.",
        self::REQUIRED_PATH => 'Path url is required.',
        self::REQUIRED_QUERY => 'Query url is required.'
    );
    
    /**
     * Array of allowed options
     * 
     * @var array
     */
    protected $allowedOptions = array(
        FILTER_FLAG_NONE => self::INVALID_URL,
        FILTER_FLAG_PATH_REQUIRED => self::REQUIRED_PATH,
        FILTER_FLAG_QUERY_REQUIRED => self::REQUIRED_QUERY
    );

    /**
     * Instance of construct
     * 
     * @param mixed $options
     */
    public function __construct($options = array())
    {
        $options = $this->getOptions($options);
        $options['filter'] = FILTER_VALIDATE_URL;
        parent::__construct($options);
    }
}
