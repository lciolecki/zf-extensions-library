<?php

namespace Extlib\Controller\Action\Helper;

/**
 * File controller action helper - return file to download from path
 *
 * @category    Extlib
 * @package     Extlib\Controller
 * @subpackage  Extlib\Controller\Action\Helper
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2013 Lukasz Ciolecki (mart)
 */
class File extends \Zend_Controller_Action_Helper_Abstract
{
    /**
     * Default uses headers
     */
    const HEADER_MIME_TYPE  = 'Content-Type';
    const HEADER_DOWNLOAD   = 'Content-Disposition';
    const HEADER_SIZE       = 'Content-Length';
    
    /**
     * Array of default headers
     *
     * @var array
     */
    protected $defaultHeaders = array (
        self::HEADER_MIME_TYPE,
        self::HEADER_DOWNLOAD,
        self::HEADER_SIZE
    );
    
    /**
     * Array of available options
     * 
     * @var array
     */
    protected $options = array(
        'fileName' => null,
        'size' => null,
        'mimeType' => null,
        'download' => false,
        'headers' => array(
            'Cache-Control' => 'max-age=2678400, public'
        )
    );

    /**
     * Proxy: file()
     * 
     * @param string $filePath
     * @param array $params
     * @return mixed
     */
    public function direct($filePath, array $params = array())
    {
        return $this->file($filePath, $params);
    }

    /**
     * Execute method 
     * 
     * @param string $filePath
     * @param array $params
     * @throws Zend_Controller_Action_Exception
     */
    public function file($filePath, array $params = array())
    {
        if (!file_exists($filePath)) {
            throw new Zend_Controller_Action_Exception(null, 404);
        }

        $this->disableViewAndLayout();
        $this->options = array_merge_recursive($this->options, $params);
        
        if (null === $this->options['size']) {
            $this->options['size'] = filesize($filePath);
        }
        
        if (null === $this->options['fileName']) {
            $this->options['fileName'] = pathinfo($filePath, PATHINFO_BASENAME);
        }

        if (null === $this->options['mimeType']) {
            $this->options['mimeType'] = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filePath);
        }

        $response = $this->getResponse();
        $response->clearAllHeaders();
        $response->clearBody();

        $response->setHeader(self::HEADER_MIME_TYPE, $this->options['mimeType']);
        $response->setHeader(self::HEADER_SIZE, $this->options['size']);
        
        if ($this->options['download']) {
            $response->setHeader(self::HEADER_DOWNLOAD, sprintf('attachment; filename="%s"', $this->options['fileName']));            
        }

        $this->buildHeaders($response, $this->options['headers']);
        
        $response->setBody(file_get_contents($filePath), $this->options['fileName']);
        $response->sendResponse();
        exit;
    }

    /**
     * Method build response headers
     * 
     * @param Zend_Controller_Response_Abstract $response
     * @param array $headers
     * @return \Extlib\Controller\Action\Helper\File
     */
    protected function buildHeaders(Zend_Controller_Response_Abstract $response, array $headers)
    {
        foreach ($headers as $header => $value) {
            if (!in_array($header, $this->defaultHeaders)) {
                $response->setHeader($header, $value);
            }
        }

        return $this;
    }

    /**
     * Method disable default html view and layout
     * 
     * @return \Extlib\Controller\Action\Helper\File
     */
    protected function disableViewAndLayout()
    {
        $layoout = Zend_Layout::getMvcInstance();
        if (null !== $layoout) {
            $layoout->disableLayout();
        }

        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        if (null !== $view) {
            $view->setNoRender(true);
        }

        return $this;
    }

}
