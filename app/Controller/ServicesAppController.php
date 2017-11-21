<?php

App::uses('AppController', 'Controller');
App::uses('AuthComponent', 'Controller/Component');

class ServicesAppController extends AppController {

    public $components = array('Session', 'Auth', 'Paginator', 'Common', 'Security', 'Paypal', 'Cookie');
    public $helpers = array('Common');

    public function beforeFilter() {
        parent::beforeFilter();

//       if ($this->params['controller'] == 'WebServices' || $this->params['controller'] == 'WebTests' || $this->params['controller'] == 'AdminServices' || $this->params['controller'] == 'MBServices'  || $this->params['controller'] == 'AdminTests' || $this->params['controller'] == 'CronJobs') {
//          $webFlag = true;
//        }else{
//            $this->setDefaultPage();
//        }
//         if(!$webFlag){
//
//            }
        //$this->setDefaultPage();
        //$this->assignAuth();
    }

}
