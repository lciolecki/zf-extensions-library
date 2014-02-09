<?php

namespace Extlib\Controller\Action\Helper;

/**
 * Flash Message - action controller helper, which allows for one-time (within a single request)
 * in session, setting message in one of three types - information, success, error.
 * 
 * @category    Extlib
 * @package     Extlib\Controller
 * @subpackage  Extlib\Controller\Action\Helper
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2010 Lukasz Ciolecki (mart)
 */
class FlashMessage extends \Zend_Controller_Action_Helper_Abstract
{

    const MESSAGE_SUCCESS = 'success';
    const MESSAGE_INFROMATION = 'information';
    const MESSAGE_ERROR = 'error';
    const VALUE_PATTERN = '%value%';

    /**
     * $_message - messages from previous request,
     * array('type' => 'message')
     *
     * @var array
     */
    static protected $_message = array();

    /**
     * $_session - Zend_Session storage object
     *
     * @var Zend_Session
     */
    static protected $_session = null;

    /**
     * $_messageAdded - wether a message has been previously added
     *
     * @var boolean
     */
    static protected $_messageAdded = false;

    /**
     * $_attribute - array of attribute
     * 
     * @var array
     */
    protected $_attribute = array();

    /**
     * $_namespace - Instance namespace, where has been save message,
     *  default is 'flashmessage'
     *
     * @var string
     */
    protected $_namespace = 'flashmessage';

    /**
     * $_allowedTypes - array of allowed types messages
     * 
     * @var array
     */
    static protected $_allowedTypes = array(
        self::MESSAGE_INFROMATION,
        self::MESSAGE_SUCCESS,
        self::MESSAGE_ERROR,
    );

    /**
     * $_translator - instance of Zend_Translate
     * 
     * @var Zend_Translate_Adapter 
     */
    protected $_translator = null;

    /**
     * __construct() - instance of constructor 
     *
     * @return void
     */
    public function __construct()
    {
        if (!self::$_session instanceof \Zend_Session_Namespace) {

            self::$_session = new \Zend_Session_Namespace($this->getName());

            if (self::$_session->{$this->_namespace} && is_array(self::$_session->{$this->_namespace}) &&
                    in_array(key(self::$_session->{$this->_namespace}), self::$_allowedTypes)) {
                self::$_message = self::$_session->{$this->_namespace};
            }

            unset(self::$_session->{$this->_namespace});
        }

        if (\Zend_Registry::isRegistered('Zend_Translate')) {
            $translator = \Zend_Registry::get('Zend_Translate');
            if ($translator instanceof \Zend_Translate_Adapter) {
                $this->_translator = $translator;
            } elseif ($translator instanceof \Zend_Translate) {
                $this->_translator = $translator->getAdapter();
            }
        }
    }

    /**
     * @see Zend_Controller_Action_Helper_Abstract::postDispatch()
     */
    public function postDispatch()
    {
        $this->resetNamespace();
        return $this;
    }

    /**
     * setNamespace() - method set namespace session
     * default - 'flashmessage'
     *
     * @param  string $namespace
     * @return \Extlib\Controller\Action\Helper\FlashMessage
     */
    public function setNamespace($namespace = 'flashmessage')
    {
        $this->_namespace = $namespace;
        return $this;
    }

    /**
     * resetNamespace() - method reset namespace, set default namespace session
     * 
     * @return \Extlib\Controller\Action\Helper\FlashMessage
     */
    public function resetNamespace()
    {
        $this->setNamespace();
        return $this;
    }

    /**
     * setMessage() - method set message and type
     *
     * @param string $type
     * @param string $message
     * @param mixed $value
     * @param int $len
     * @return \Extlib\Controller\Action\Helper\FlashMessage
     */
    public function setMessage($type, $message, $value = null, $len = 25)
    {
        if (!in_array(strtolower($type), self::$_allowedTypes)) {
            throw new \Zend_Controller_Action_Exception('Incorrect type of message!');
        }

        if (!Zend_Validate::is($len, 'Int')) {
            throw new \Zend_Controller_Action_Exception("Param 'len' must be a int value!");
        }

        if (null !== $this->_translator) {
            $message = $this->_translator->translate($message);
        }

        if (null !== $value) {
            if (strlen($value) > $len) {
                $value = mb_substr($value, 0, $len, 'UTF-8') . '..';
            }

            $message = str_replace(self::VALUE_PATTERN, $value, $message);
        }

        self::$_session->setExpirationHops(1, null, true);
        self::$_session->{$this->_namespace} = array(strtolower($type) => $message);
        self::$_messageAdded = true;

        return $this;
    }

    /**
     * getMessage() - method return message
     *
     * @return string|null
     */
    public function getMessage()
    {
        if ($this->hasMessage()) {
            return current(self::$_message);
        }

        return null;
    }

    /**
     *  getTypeMessage() - method return type message
     *
     * @return string|null
     */
    public function getTypeMessage()
    {
        if ($this->hasMessage()) {
            return key(self::$_message);
        }

        return null;
    }

    /**
     * hasMessage() - method check any message has been set
     *
     * @return boolean
     */
    public function hasMessage()
    {
        return !empty(self::$_message);
    }

}
