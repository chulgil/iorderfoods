<?php

App::uses('AppController', 'Controller');

class HqAppController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        //$this->_loginToBoth();
        if ($this->params['controller'] == 'hq' || $this->Session->read('Auth.Admin.role_id') == 2) {
            if ($this->params['action'] == 'merchant') {
                $this->setmerchant($this->params->url);
            } else {
                $this->setHqadmin($this->params->url);
            }
        } elseif ($this->params['controller'] == 'hqusers' || $this->Session->read('Auth.Admin.role_id') == 5) {
            if ($this->params['action'] == 'merchant') {
                $this->setmerchant($this->params->url);
            }
            $this->Auth_hqusers();
            //$this->_loginToBoth();
        } elseif (!$this->Session->check('Auth.hq.id') && !$this->Session->check('Auth.hqusers.id')) {
            if (!$this->Session->check('hq_id') && !$this->Session->check('merchantId')) {
                //header('Location:' . 'http://' . env('SERVER_NAME'));
                header('Location:' . Router::fullbaseUrl());
                exit;
            } else {
                $this->setDefaultPage();
            }
        } else {
            $this->setDefaultPage();
        }

        if ($this->params['controller'] == 'hq') {
            $this->assignHQBackAuth();
        } elseif (($this->Session->check('hq_id')) && ($this->params['controller'] == 'hqusers' || $this->params['controller'] == 'hq')) {
            $this->assignHQFrontAuth();
        }
        $this->loadModel('MerchantDesign');
        $layoutCss = $this->MerchantDesign->find('first', array('conditions' => array('merchant_id' => $this->Session->read('hq_id')), 'fields' => array('merchant_css')));
        $this->set(compact('layoutCss'));
    }

    /* Assign Login auth to HQ Back Panel */

    function assignHQBackAuth() {
        AuthComponent::$sessionKey = 'Auth.hq';
        $scopearray = array('User.is_active' => 1, 'User.role_id' => 2, 'User.is_deleted' => 0);
        if ($this->Session->check('merchantId')) {
            $scopearray['User.merchant_id'] = $this->Session->read('merchantId');
        }
        $this->Auth->authenticate = array(
            'Form' => array(
                'userModel' => 'User',
                'fields' => array('username' => 'email', 'password' => 'password', 'merchant_id'),
                'scope' => $scopearray
            )
        );
    }

    /* Assign Login auth to HQ Front Panel */

    function assignHQFrontAuth() {
        AuthComponent::$sessionKey = 'Auth.hqusers';
        $this->Auth->authenticate = array(
            'Form' => array(
                'userModel' => 'User',
                'fields' => array('username' => 'email', 'password' => 'password', 'store_id'),
                'scope' => array('User.merchant_id' => $this->Session->read('hq_id'), 'User.role_id' => array('4', '5'), 'User.is_active' => 1, 'User.is_deleted' => 0)
            )
        );
    }

    /* Identify Merchant & Set session */

    function setHqadmin($params) {
       // $requestParam = explode('/', $this->params->url);
       // $merchant_name = trim($requestParam[0]); // Name of the store which we will change later with Saas
	$subdomain = $_SERVER['SERVER_NAME'];
        if ($subdomain) {
            $merchant_name = $subdomain;
        } else {
            $requestParam = explode('/', $this->params->url);
            $merchant_name = trim($requestParam[0]); // Name of the store which we will change later with Saas
        }
        if ($merchant_name) {
            $this->loadModel('Merchant');
            $merchant = $this->Merchant->merchant_info($merchant_name);
            if ($merchant) {
                $this->merchantId = $merchant['Merchant']['id'];
                $this->merchantName = $merchant['Merchant']['name'];
                $this->merchantImage = $merchant['Merchant']['background_image'];
                $this->merchantLogo = $merchant['Merchant']['logo'];
                $this->Session->write('merchantId', $merchant['Merchant']['id']);
                $this->assignHQBackAuth();
            }
        } else {
            $this->redirect(array('controller' => 'users', 'action' => 'selectStore'));
        }
    }

    /* Identify Merchant & Set session */

    function setmerchant($params) {
        $subdomain = $_SERVER['SERVER_NAME'];
        if ($subdomain) {
            $merchant_name = $subdomain;
        } else {
            $requestParam = explode('/', $this->params->url);
            $merchant_name = trim($requestParam[0]); // Name of the store which we will change later with Saas
        }
        if ($merchant_name) {
            $this->loadModel('Merchant');
            $merchant = $this->Merchant->merchant_info($merchant_name);
            if ($merchant) {
                $this->merchantId = $merchant['Merchant']['id'];
                $this->merchantName = $merchant['Merchant']['name'];
                $this->merchantImage = $merchant['Merchant']['background_image'];
                $this->merchantLogo = $merchant['Merchant']['logo'];
                $this->Session->write('hq_id', $merchant['Merchant']['id']);
                $this->assignHQFrontAuth();
                $this->loadModel('MerchantDesign');
                $layoutCss = $this->MerchantDesign->find('first', array('conditions' => array('merchant_id' => $this->Session->read('hq_id')), 'fields' => array('merchant_css')));
                $this->set(compact('layoutCss'));
            }
        }
    }

}
