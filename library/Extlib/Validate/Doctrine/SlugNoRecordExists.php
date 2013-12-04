<?php
/**
 * Extlib_Validate_Doctrine_PermalinkNoRecordExists - 
 * 
 * @category   Extlib
 * @package    Extlib_Validate
 * @uses       Extlib_Validate_Doctrine_NoRecordExists
 * @copyright  Copyright (c) 2012 Łukasz Ciołecki (Mart)
 */
class Extlib_Validate_Doctrine_SlugNoRecordExists extends Extlib_Validate_Doctrine_NoRecordExists
{
    /**
     * Error constants
     */
    const ERROR_PERMALINK_FOUND = 'doctrinePermalinkFound';

    /**
     * $_messageTemplates - message templates
     * 
     * @var array
     */
    protected $_messageTemplates = array(
        self::ERROR_PERMALINK_FOUND    => "A permalink for '%value%' was found",
    );

    /**
     * (non-PHPdoc)
     * @see Zend_Validate_Interface::isValid()
     */
    public function isValid($value)
    {
        $permalinkFilter = new Extlib_Filter_Slug();
        
        if (!parent::isValid($permalinkFilter->filter($value))) {
            $this->_error(self::ERROR_PERMALINK_FOUND, $value);
            return false;
        }
        
        return true;
    }
}