<?php

namespace Extlib\Filter\Filter;
use Extlib\Filter\Filter\PolishChar;

/**
 * Simple class filter for slug
 *
 * @category    Extlib
 * @package     Filter
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2012 Lukasz Ciolecki (mart)
 */
class Slug extends PolishChar
{
    const SLUG_PATTERN = '/[a-zA-Z0-9]+/';
    
    /**
     * $_wordSeparator - slug word separator
     * 
     * @var string 
     */
    protected $_wordSeparator = '-';

    /**
     * __construct() - instance of construct
     * 
     * @param mixed $options 
     */
    public function __construct($options = null)
    {
        if ($options instanceof \Zend_Config) {
            $options = $options->toArray();
        } else if (!is_array($options)) {
            $options = func_get_args();
            $temp    = array();
            if (!empty($options)) {
                $temp['wordSeparator'] = array_shift($options);
            }
            $options = $temp;
        }
   
        if (array_key_exists('wordSeparator', $options)) {
            $this->setWordSeparator($options['wordSeparator']);
        }
        
        parent::__construct($options);
    }

   /**
    * Defined by Zend_Filter_Interface
    *
    * Encrypts the content $value with the defined settings
    *
    * @param  string $value Content to encrypt
    * @return string The encrypted content
    */
    public function filter($value)
    {
        $lowerFilter = new \Zend_Filter_StringToLower();
        $value = $lowerFilter->filter(parent::filter($value));
        preg_match_all(self::SLUG_PATTERN, $value, $return);
        
        return implode($this->getWordSeparator(), $return[0]);
    }
    
    /**
     * getWordSeparator() - method return word separator for tag
     * 
     * @return string 
     */
    public function getWordSeparator()
    {
        return $this->_wordSeparator;
    }

    /**
     * setWordSeparator() - set word separator
     * 
     * @param string $wordSeparator
     * @return \Extlib\Filter\Slug 
     */
    public function setWordSeparator($wordSeparator)
    {
        $this->_wordSeparator = $wordSeparator;
        return $this;
    }
}