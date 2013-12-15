<?php

namespace Extlib\View\Helper;


/**
 * 
 */
class Media extends \Zend_View_Helper_Abstract
{
    /* Add param name */
    const QUERY_PARAM_NAME = 'ts';
    
    /**
     * Media directory
     * 
     * @var string
     */
    static public $dir = null;
    
    /**
     * Media domain name
     * 
     * @var string
     */
    static public $domain = null;

    /**
     * Instance of construct
     */
    public function __construct()
    {
        if (null === self::$dir) {
            self::$dir = APPLICATION_PATH . '/../public';
        }
    
        if (null === self::$domain) {
            $domain = new \Extlib\System\Domain();
            self::$domain = $domain->getAddress();
        }
    }
    
    /**
     * Helper method
     * 
     * @param string $file
     * @param boolean $queryParam
     * @return string
     */
    public function media($file, $queryParam = true)
    {
        if ($queryParam) {
            $mediaDir = rtrim(self::$dir, DIRECTORY_SEPARATOR);
            $filePath = $mediaDir . DIRECTORY_SEPARATOR . trim($file, DIRECTORY_SEPARATOR);

            if (file_exists($filePath)) {
                $file .= sprintf('?%s=%s', self::QUERY_PARAM_NAME,  filemtime($filePath));
            }
        }

        return trim(self::$domain, '/') .  '/' . trim($file, '/');
    }
}