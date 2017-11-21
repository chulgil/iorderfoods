<?php

App::uses('AppController', 'Controller');
App::uses('AuthComponent', 'Controller/Component');

class SupersAppController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth_super();
        $this->assignSuperAuth();
    }

    /* Assign Login auth to Super Panel */

    function assignSuperAuth() {
        AuthComponent::$sessionKey = 'Auth.Super';
        $this->Auth->authenticate = array(
            'Form' => array(
                'userModel' => 'User',
                'fields' => array('username' => 'email', 'password' => 'password'),
                'scope' => array('User.is_active' => 1, 'User.role_id' => 1, 'User.is_deleted' => 0)
            )
        );
    }

}
