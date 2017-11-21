<?php

App::uses('StoreAppController', 'Controller');

class TemplatesController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'Dateform', 'Common');
    public $uses = array('Order');

    public function beforeFilter() {
        // echo Router::url( $this->here, true );die;
        parent::beforeFilter();
        $adminfunctions = array('index', 'activateTemplate', 'deleteTemplate', '');
        if (in_array($this->params['action'], $adminfunctions)) {
            if (!$this->Common->checkPermissionByaction($this->params['controller'])) {
                $this->Session->setFlash(__("Permission Denied"));
                $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:index()
      Description:Display the list of created templates
      created:24/8/2015
      ----------------------------------------------------- */

    public function index($clearAction = null) {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $this->loadModel('EmailTemplate');
        $criteria = "EmailTemplate.store_id =$storeID AND EmailTemplate.is_deleted=0";
        if ($this->Session->read('TemplateSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('TemplateSearchData'), true);
        } else {
            $this->Session->delete('TemplateSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('TemplateSearchData', json_encode($this->request->data));
            if ($this->request->data['EmailTemplate']['is_active'] != '') {
                $active = trim($this->request->data['EmailTemplate']['is_active']);
                $criteria .= " AND (EmailTemplate.is_active` ='" . $active . "')";
            }
            if (!empty($this->request->data['EmailTemplate']['search'])) {
                $search = trim($this->request->data['EmailTemplate']['search']);
                $criteria .= " AND (EmailTemplate.template_subject LIKE '%" . $search . "%')";
            }
        }
        $this->paginate = array('conditions' => array($criteria), 'order' => array('EmailTemplate.created' => 'DESC'));
        $templateDetail = $this->paginate('EmailTemplate');
        //pr($templateDetail);die;
        $this->set('list', $templateDetail);
    }

    /* ------------------------------------------------
      Function name:activateTemplates()
      Description:Active/Deactive template
      created:24/8/2015
      ----------------------------------------------------- */

    public function activateTemplate($EncryptTemplateID = null, $status = 0) {
        $this->layout = "admin_dashboard";
        $this->loadModel('EmailTemplate');
        $data['EmailTemplate']['store_id'] = $this->Session->read('admin_store_id');
        $data['EmailTemplate']['id'] = $this->Encryption->decode($EncryptTemplateID);
        $data['EmailTemplate']['is_active'] = $status;
        if ($this->EmailTemplate->saveTemplate($data)) {
            if ($status) {
                $SuccessMsg = "Template Activated";
            } else {
                $SuccessMsg = "Template Inactive and Template will not get Display in Menu List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'templates', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'templates', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteTemplate()
      Description:Delete template from list
      created:24/8/2015
      ----------------------------------------------------- */

    public function deleteTemplate($EncryptTemplateID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $this->loadModel('EmailTemplate');
        $data['EmailTemplate']['store_id'] = $this->Session->read('admin_store_id');
        $data['EmailTemplate']['id'] = $this->Encryption->decode($EncryptTemplateID);
        $data['EmailTemplate']['is_deleted'] = 1;
        if ($this->EmailTemplate->saveTemplate($data)) {
            $this->Session->setFlash(__("Template deleted"), 'alert_success');
            $this->redirect(array('controller' => 'templates', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'templates', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:editTemplate()
      Description:Edit Template contents
      created:24/8/2015
      ----------------------------------------------------- */

    public function editTemplate($EncryptTemplateID = null) {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $data['EmailTemplate']['id'] = $this->Encryption->decode($EncryptTemplateID);
        $this->loadModel('EmailTemplate');
        $templateDetail = $this->EmailTemplate->getTemplateDetail($data['EmailTemplate']['id'], $storeId);
        if ($this->request->data) {
            $templateTitle = trim($this->data['EmailTemplate']['template_subject']);
            $isUniqueName = $this->EmailTemplate->checkTemplateUniqueName($templateTitle, $storeId, $data['EmailTemplate']['id']);
            if ($isUniqueName) {
                $templatedata = array();
                $templatedata['template_subject'] = trim($this->data['EmailTemplate']['template_subject']);
                $templatedata['id'] = trim($this->data['EmailTemplate']['id']);
                $templatedata['template_message'] = trim($this->data['EmailTemplate']['template_message']);
                if (empty($this->data['EmailTemplate']['sms_template'])) {
                    $this->request->data['EmailTemplate']['sms_template'] = '';
                }
                $templatedata['sms_template'] = trim($this->data['EmailTemplate']['sms_template']);
                $templatedata['is_active'] = trim($this->data['EmailTemplate']['is_active']);
                $templatedata['store_id'] = $storeId;
                $templatedata['merchant_id'] = $merchantId;
                $this->loadModel('EmailTemplate');
                $this->EmailTemplate->create();
                $this->EmailTemplate->saveTemplate($templatedata);

                $this->Session->setFlash(__("Template Successfully Updated."), 'alert_success');
                $this->redirect(array('controller' => 'templates', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("Template Name Already exists"), 'alert_failed');
            }
        }

        $this->request->data = $templateDetail;
    }

    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('EmailTemplate');
            $storeID = $this->Session->read('admin_store_id');
            $criteria = "EmailTemplate.store_id =$storeID AND EmailTemplate.is_deleted=0";
            $searchData = $this->EmailTemplate->find('list', array('fields' => array('EmailTemplate.template_subject', 'EmailTemplate.template_subject'), 'conditions' => array('OR' => array('EmailTemplate.template_subject LIKE' => '%' . $_GET['term'] . '%'), $criteria)));
            echo json_encode($searchData);
        } else {
            exit;
        }
    }

}
