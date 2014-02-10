<?php

namespace Extlib\Mail\Message;

/**
 * Mail message html type
 * 
 * @category    Extlib
 * @package     Extlib\Mail
 * @subpackage  Extlib\Mail\Message
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2011 Lukasz Ciolecki (mart)
 */
class Html implements MessageInterface
{

    /**
     * Emails path
     */
    const EMAIL_PATH = '/views/emails';

    /**
     * Instance of Zend_Layout
     * 
     * @var \Zend_Layout
     */
    protected $layout = null;

    /**
     * View script name
     * 
     * @var string 
     */
    protected $scriptName = null;

    /**
     * Instance of Zend_Controller_Front
     * 
     * @var \Zend_Controller_Front 
     */
    protected $frontController = null;

    /**
     * Instance of construct
     * 
     * @param string $layout
     * @param array $options
     */
    public function __construct($layout = 'email', array $options = array())
    {
        $this->frontController = \Zend_Controller_Front::getInstance();
        $resources = $this->frontController->getParam('bootstrap')->getOption('resources');

        if (isset($options['layout'])) {
            $this->layout = new \Zend_Layout($options['layout']);
        } elseif (isset($resources['layout'])) {
            $this->layout = new \Zend_Layout($resources['layout']);
        } else {
            $this->layout = new \Zend_Layout();
        }

        if (isset($options['view'])) {
            $this->layout->setView(new \Zend_View($options['view']));
        } elseif (isset($resources['view'])) {
            $this->layout->setView(new \Zend_View($resources['view']));
        } else {
            $this->layout->setView(new \Zend_View());
        }

        $scriptPath = APPLICATION_PATH . self::EMAIL_PATH;
        if (null !== $this->frontController->getModuleDirectory()) {
            $scriptPath = $this->frontController->getModuleDirectory() . self::EMAIL_PATH;
        }

        $this->layout->setLayout($layout);
        $this->setScriptPath($scriptPath);
    }

    /**
     * Implementation \Extlib\Mail\Message\MessageInterface::create()
     * 
     * @param string $template
     * @return string|html 
     */
    public function create($template = null)
    {
        if (null !== $template) {
            $this->setScriptName($template);
        }

        $this->layout->assign('content', $this->layout->getView()->render($this->getScript()));
        return $this->layout->render();
    }

    /**
     * Get full name of script to render
     * 
     * @return string 
     */
    protected function getScript()
    {
        if (null === $this->getScriptName()) {
            $request = $this->frontController->getRequest();
            $this->setScriptName($request->getControllerName() . DIRECTORY_SEPARATOR . $request->getActionName());
        }

        return $this->getScriptName() . '.' . $this->getViewSuffix();
    }

    /**
     * Set a script name
     * 
     * @param string $name
     * @return \Extlib\Mail\Message\Html 
     */
    public function setScriptName($name)
    {
        $this->scriptName = $name;
        return $this;
    }

    /**
     * Return a script name
     * 
     * @return string 
     */
    public function getScriptName()
    {
        return $this->scriptName;
    }

    /**
     * Set script path - alias for \Zend_View::setScriptPath()
     * 
     * @param string $scriptPath
     * @return \Extlib\Mail\Message\Html
     */
    public function setScriptPath($scriptPath)
    {
        $this->layout->getView()->setScriptPath($scriptPath);
        return $this;
    }

    /**
     * Return script path - alias for \Zend_View::getScriptPath()
     * 
     * @return string
     */
    public function getScriptPath()
    {
        return $this->layout->getView()->getScriptPath();
    }

    /**
     * Set view suffix - alias for \Zend_Layout::setViewSuffix
     * 
     * @param string $suffix
     * @return \Extlib\Mail\Message\Html 
     */
    public function setViewSuffix($suffix)
    {
        $this->layout->setViewSuffix($suffix);
        return $this;
    }

    /**
     * Return view suffix - alias for \Zend_Layout::getViewSuffix
     *
     * @return string 
     */
    public function getViewSuffix()
    {
        return $this->layout->getViewSuffix();
    }

    /**
     * Set data collections in View
     *  
     * @param array $data 
     * @return \Extlib\Mail\Message\Html 
     */
    public function setData(array $data)
    {
        foreach ($data as $name => $value) {
            if (is_string($name)) {
                $this->__set($name, $value);
            }
        }

        return $this;
    }

    /**
     * Alias for setData
     *  
     * @param array $data 
     * @return \Extlib\Mail\Message\Html 
     */
    public function addData(array $data)
    {
        return $this->setData($data); 
    }


    /**
     * Magic set property - alis for __set in Zend_View
     * 
     * @param string $name
     * @param mixed $value
     * @return \Extlib\Mail\Message\Html 
     */
    public function __set($name, $value)
    {
        $this->layout->getView()->assign($name, $value);
        return $this;
    }

    /**
     * Magic reuturn property - alias for __get in Zend_View
     * 
     * @param string $name
     * @return mixed|null 
     */
    public function __get($name)
    {
        if (isset($this->layout->getView()->{$name})) {
            return $this->layout->getView()->{$name};
        }

        return null;
    }

}
