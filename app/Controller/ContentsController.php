<?php

App::uses('StoreAppController', 'Controller');

class ContentsController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'Dateform', 'Common');
    public $uses = array('Order');

    public function beforeFilter() {
        parent::beforeFilter();
        $adminfunctions = array('index', 'pageList', 'pageLocation', 'activatePage', 'deletePage', 'editPage');
        if (in_array($this->params['action'], $adminfunctions) && !$this->Common->checkPermissionByaction($this->params['controller'])) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
    }

    /* ------------------------------------------------
      Function name:index()
      Description:Add the newsletter in table
      created:20/8/2015
      ----------------------------------------------------- */

    public function index() {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $this->loadModel('StoreContent');
        $merchantId = $this->Session->read('admin_merchant_id');
        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $pagedata['name'] = trim($this->request->data['StoreContent']['name']);
            $pagedata['content_key'] = trim($this->request->data['StoreContent']['content_key']);
            $isUniqueName = $this->StoreContent->checkPageUniqueName($pagedata['name'], $storeID);
            $isUniqueCode = $this->StoreContent->checkPageUniqueCode($pagedata['content_key'], $storeID);
            if ($isUniqueName) {
                if ($isUniqueCode) {
                    $pagedata = array();
                    $pagedata['name'] = trim($this->request->data['StoreContent']['name']);
                    $pagedata['content_key'] = trim($this->request->data['StoreContent']['content_key']);
                    $pagedata['content'] = trim($this->request->data['StoreContent']['content']);
                    $pagedata['page_position'] = trim($this->request->data['StoreContent']['page_position']);
                    $pagedata['is_active'] = trim($this->request->data['StoreContent']['is_active']);
                    $pagedata['store_id'] = $storeID;
                    $pagedata['merchant_id'] = $merchantId;
                    $this->StoreContent->create();
                    $this->StoreContent->savePage($pagedata);
                    $this->Session->setFlash(__("Page Successfully Created"), 'alert_success');
                    $this->redirect(array('controller' => 'contents', 'action' => 'pageList'));
                } else {
                    $this->Session->setFlash(__("Page code Already exists"), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Page name Already exists"), 'alert_failed');
            }
        }
    }

    /* ------------------------------------------------
      Function name:pageList()
      Description:Display the list of created pages
      created:20/8/2015
      ----------------------------------------------------- */

    public function pageList() {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $this->loadModel('StoreContent');
        $this->loadModel('TermsAndPolicy');
        $this->loadModel('Store');
        $criteria = "StoreContent.store_id =$storeID AND StoreContent.is_deleted=0 AND StoreContent.merchant_id=$merchantId";
        $pageDetail = $this->StoreContent->find('all', array('conditions' => array($criteria), 'order' => array('StoreContent.position' => 'ASC')));
//        $this->paginate = array('conditions' => array($criteria), 'order' => array('StoreContent.created' => 'DESC'));
//        $pageDetail = $this->paginate('StoreContent');
        //$pagePostion = $this->Store->find('first', array('conditions' => array('Store.id' => $storeID)));
        //$this->request->data = $pagePostion;
        $this->set('list', $pageDetail);
        $termsAndPolicy = $this->TermsAndPolicy->findByStoreId($storeID);
        $this->set('termsAndPolicy', $termsAndPolicy);
    }

    /* ------------------------------------------------
      Function name:pageLocation()
      Description:Fixed the page position
      created:20/8/2015
      ----------------------------------------------------- */

    public function pageLocation() {
        $this->layout = "admin_dashboard";
        $this->autoRender = false;
        $this->loadModel('Store');
        $storeID = $this->Session->read('admin_store_id');
        if ($this->request->data) {
            $this->Store->id = $storeID;
            $this->Store->saveField("navigation", $this->request->data['Store']['navigation']);
            $this->Session->setFlash(__("Navigation Position Updated Successfully."), 'alert_success');
            $this->redirect($this->referer());
        }
    }

    /* ------------------------------------------------
      Function name:activatePage()
      Description:Active/Deactive pages
      created:20/8/2015
      ----------------------------------------------------- */

    public function activatePage($EncryptPageID = null, $status = 0) {
        $this->layout = "admin_dashboard";
        $this->loadModel('StoreContent');
        $data['StoreContent']['store_id'] = $this->Session->read('admin_store_id');
        $data['StoreContent']['id'] = $this->Encryption->decode($EncryptPageID);
        $data['StoreContent']['is_active'] = $status;
        if ($this->StoreContent->savePage($data)) {
            if ($status) {
                $SuccessMsg = "Page Activated";
            } else {
                $SuccessMsg = "Page Inactive and Page will not get Display in Menu List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'contents', 'action' => 'pageList'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'contents', 'action' => 'pageList'));
        }
    }

    /* ------------------------------------------------
      Function name:activateTermsAndPolicy()
      Description:Active/Deactive Terms And Policy pages
      created:03/01/2017
      ----------------------------------------------------- */

    public function activateTermsAndPolicy($EncryptTermsAndPolicyID = null, $status = 0) {
        $this->layout = "admin_dashboard";
        $this->loadModel('TermsAndPolicy');
        $data['TermsAndPolicy']['id'] = $this->Encryption->decode($EncryptTermsAndPolicyID);
        $data['TermsAndPolicy']['is_active'] = $status;
        if ($this->TermsAndPolicy->save($data)) {
            if ($status) {
                $SuccessMsg = "Page Activated";
            } else {
                $SuccessMsg = "Page Inactive and Page will not get Display in Menu List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'contents', 'action' => 'pageList'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'contents', 'action' => 'pageList'));
        }
    }

    /* ------------------------------------------------
      Function name:deletePage()
      Description:Delete page from list
      created:20/8/2015
      ----------------------------------------------------- */

    public function deletePage($EncryptPageID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $this->loadModel('StoreContent');
        $data['StoreContent']['store_id'] = $this->Session->read('admin_store_id');
        $data['StoreContent']['id'] = $this->Encryption->decode($EncryptPageID);
        $data['StoreContent']['is_deleted'] = 1;
        if ($this->StoreContent->savePage($data)) {
            $this->Session->setFlash(__("Page deleted"), 'alert_success');
            $this->redirect(array('controller' => 'contents', 'action' => 'pageList'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'contents', 'action' => 'pageList'));
        }
    }

    /* ------------------------------------------------
      Function name:editPage()
      Description:Edit Page contents
      created:20/8/2015
      ----------------------------------------------------- */

    public function editPage($EncryptPageID = null) {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $data['StoreContent']['id'] = $this->Encryption->decode($EncryptPageID);
        $this->loadModel('StoreContent');
        $pageDetail = $this->StoreContent->getPageDetail($data['StoreContent']['id'], $storeId);
        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $pageTitle = trim($this->request->data['StoreContent']['name']);
            $pageCode = trim($this->request->data['StoreContent']['content_key']);
            $isUniqueName = $this->StoreContent->checkPageUniqueName($pageTitle, $storeId, $data['StoreContent']['id']);
            $isUniqueCode = $this->StoreContent->checkPageUniqueCode($pageCode, $storeId, $data['StoreContent']['id']);
            if ($isUniqueName) {
                if ($isUniqueCode) {
                    $pagedata = array();
                    $pagedata['name'] = trim($this->request->data['StoreContent']['name']);
                    $pagedata['content_key'] = trim($this->request->data['StoreContent']['content_key']);
                    $pagedata['id'] = trim($this->request->data['StoreContent']['id']);
                    $pagedata['content'] = trim($this->request->data['StoreContent']['content']);
                    $pagedata['page_position'] = trim($this->request->data['StoreContent']['page_position']);
                    $pagedata['position'] = trim($this->request->data['StoreContent']['position']);
                    $pagedata['is_active'] = trim($this->request->data['StoreContent']['is_active']);
                    $pagedata['store_id'] = $storeId;
                    $pagedata['merchant_id'] = $merchantId;
                    $this->loadModel('StoreContent');
                    $this->StoreContent->create();
                    $this->StoreContent->savePage($pagedata);
                    $this->Session->setFlash(__("Page Successfully Updated."), 'alert_success');
                    $this->redirect(array('controller' => 'contents', 'action' => 'pageList'));
                } else {
                    $this->Session->setFlash(__("Page Code Already exists"), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Page Name Already exists"), 'alert_failed');
            }
        }
        $this->request->data = $pageDetail;
    }

    public function updatePageListingPosition() {
        $this->autoRender = false;
        if (isset($_GET) && !empty($_GET)) {
            $this->loadModel('StoreContent');
            foreach ($_GET as $key => $val) {
                $this->StoreContent->updateAll(array('position' => $val), array('id' => $this->Encryption->decode($key)));
            }
        }
    }

}
