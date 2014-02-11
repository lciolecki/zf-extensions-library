<?php

namespace Extlib\Filter;

/**
 * Filter class for remove polish char.
 *
 * @category    Extlib
 * @package     Filter
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2012 Lukasz Ciolecki (mart)
 */
class PolishChar implements \Zend_Filter_Interface
{
    /**
     * $_replacementChar - array of characters to replace
     *
     * @var array
     */
    protected $_replacementChar = array(
    	'ą' => 'a',
        'ć' => 'c',
        'ę' => 'e',
        'ł' => 'l',
        'ń' => 'n',
        'ó' => 'o',
        'ś' => 's',
        'ź' => 'z',
        'ż' => 'z',
    	'Ą' => 'A',
        'Ć' => 'C',
        'Ę' => 'E',
        'Ł' => 'L',
        'Ń' => 'N',
        'Ó' => 'O',
        'Ś' => 'S',
        'Ź' => 'Z',
        'Ż' => 'Z'
    ); 

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
                $temp['replacementChar'] = array_shift($options);
            }
            $options = $temp;
        }

        if (array_key_exists('replacementChar', $options)) {
            $this->setReplacementChar($options['replacementChar']);
        }
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
        return str_replace(array_keys($this->_replacementChar), $this->_replacementChar, $value);
    }

    /**
     * setReplacementChar() - set new array of characters 
     * 
     * @param array $replacementChar 
     */
    public function setReplacementChar(array $replacementChar)
    {
        $this->_replacementChar = $replacementChar;
    }
}