<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body

    }

    public function loginAction()
    {
        $flashMessenger = $this->_helper->getHelper('FlashMessenger');

        if ($this->getRequest()->isPost()) {
            $adapter = new Zend_Auth_Adapter_DbTable(
                Zend_Db_Table_Abstract::getDefaultAdapter(), 'users', 'username', 'password', self::getHashPasswordQuery()
            );

            $adapter->setIdentity(strtolower($this->_getParam('username')));
            $adapter->setCredential($this->_getParam('password'));

            $auth = Zend_Auth::getInstance();

            $result = $auth->authenticate($adapter);

            if (!$result->isValid()) {
                $flashMessenger->addMessage(array('error' => 'Bad credentials.'));
                $this->_helper->redirector('login', 'index');
            } else {
                $this->_helper->redirector('profile', 'index');
            }
        }


        $this->view->flashmessages = $flashMessenger->getMessages();
    }

    public function logoutAction()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            Zend_Auth::getInstance()->clearIdentity();
        }

        Zend_Session::destroy();

        $this->_helper->redirector('index', 'index');
    }

    public function profileAction()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $flashMessenger = $this->_helper->getHelper('FlashMessenger');
            $flashMessenger->addMessage(array('error' => 'This page is secured. Please authenticate.'));

            $this->_helper->redirector ( 'login', 'index' );
        }
    }

    public function getHashPasswordQuery() {
        return "SHA1(CONCAT('" . Zend_Registry::get('static_salt') . ":', ?))";
    }
}

