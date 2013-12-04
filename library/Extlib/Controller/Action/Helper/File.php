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
    /* Default time cache */

    const CACHE_EXPIRE = 2678400;
    
    /**
     * Array of available options
     * 
     * @var array
     */
    protected $options = array(
        'fileName' => null,
        'download' => false,
        'mimeType' => null,
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
        $response = $this->getResponse();

        $respondFileSize = filesize($filePath);
        $respondFileContent = file_get_contents($filePath);

        $respondFileName = isset($params['fileName']) ? $params['fileName'] : null;
        if (null === $respondFileName) {
            $respondFileName = pathinfo($filePath, PATHINFO_BASENAME);
        }

        $respondMimeType = isset($params['mimeType']) ? $params['mimeType'] : null;
        if (null === $respondMimeType) {
            $respondMimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filePath);
        }

        $response->clearBody();
        $response->clearAllHeaders();
        $response->setHeader('Content-Type', $respondMimeType);
        $response->setHeader('Content-Length', $respondFileSize);

        if (isset($params['headers']) && is_array($params['headers'])) {
            $this->buildHeaders($response, $params['headers']);
        } else {
            $response->setHeader('Cache-Control', sprintf('max-age=%s, public', self::CACHE_EXPIRE));
        }

        $response->setHeader('Content-Disposition', sprintf('attachment; filename="%s"', $respondFileName));
        $response->setBody($respondFileContent, $respondFileName);
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
        foreach ($headers as $header) {
            if (is_array($header) && isset($header['name']) && isset($header['value'])) {
                $response->setHeader($header['name'], $header['value'], isset($header['replace']) ? $header['replace'] : false);
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
