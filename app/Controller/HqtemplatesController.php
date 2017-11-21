<?php

App::uses('HqAppController', 'Controller');

class HqtemplatesController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'Dateform', 'Common');
    public $uses = array('EmailTemplate', 'MerchantDesign');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    /* ------------------------------------------------
      Function name:index()
      Description:Display the list of created email templates
      created:28/7/2016
      ----------------------------------------------------- */

    public function index($clearAction = null) {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $this->loadModel('EmailTemplate');
        $criteria = "EmailTemplate.store_id IS NULL AND EmailTemplate.merchant_id=$merchantId AND EmailTemplate.is_deleted=0";
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
        $this->paginate = array('conditions' => array($criteria), 'order' => array('EmailTemplate.id' => 'ASC'));
        $templateDetail = $this->paginate('EmailTemplate');
        $this->set('list', $templateDetail);
    }

    /* ------------------------------------------------
      Function name:editTemplate()
      Description:Edit Template contents
      created:28/7/2016
      ----------------------------------------------------- */

    public function editTemplate($EncryptTemplateID = null) {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $data['EmailTemplate']['id'] = $this->Encryption->decode($EncryptTemplateID);
        $this->loadModel('EmailTemplate');
        $templateDetail = $this->EmailTemplate->getTemplateDetail($data['EmailTemplate']['id']);
        if ($this->request->data) {
            $templateTitle = trim($this->data['EmailTemplate']['template_subject']);
            $isUniqueName = $this->EmailTemplate->checkTemplateUniqueName($templateTitle, null, $data['EmailTemplate']['id'], $merchantId);
            if ($isUniqueName) {
                $templatedata = array();
                $templatedata['template_subject'] = trim($this->data['EmailTemplate']['template_subject']);
                $templatedata['id'] = trim($this->data['EmailTemplate']['id']);
                $templatedata['template_message'] = trim($this->data['EmailTemplate']['template_message']);
                $templatedata['is_active'] = trim($this->data['EmailTemplate']['is_active']);
                $templatedata['merchant_id'] = $merchantId;
                $this->loadModel('EmailTemplate');
                $this->EmailTemplate->create();
                $this->EmailTemplate->saveTemplate($templatedata);
                $this->Session->setFlash(__("Template Successfully Updated."), 'alert_success');
                $this->redirect(array('controller' => 'hqtemplates', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("Template Name Already exists"), 'alert_failed');
            }
        }

        $this->request->data = $templateDetail;
    }

    /* ------------------------------------------------
      Function name:deleteTemplate()
      Description:Delete template from list
      created:28/7/2017
      ----------------------------------------------------- */

    public function deleteTemplate($EncryptTemplateID = null) {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $this->loadModel('EmailTemplate');
        $data['EmailTemplate']['store_id'] = $this->Session->read('merchantId');
        $data['EmailTemplate']['id'] = $this->Encryption->decode($EncryptTemplateID);
        $data['EmailTemplate']['is_deleted'] = 1;
        if ($this->EmailTemplate->saveTemplate($data)) {
            $this->Session->setFlash(__("Template deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqtemplates', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqtemplates', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:activateTemplates()
      Description:Active/Deactive template
      created:28/7/2016
      ----------------------------------------------------- */

    public function activateTemplate($EncryptTemplateID = null, $status = 0) {
        $this->layout = "hq_dashboard";
        $this->loadModel('EmailTemplate');
        $data['EmailTemplate']['id'] = $this->Encryption->decode($EncryptTemplateID);
        $data['EmailTemplate']['is_active'] = $status;
        if ($this->EmailTemplate->saveTemplate($data)) {
            if ($status) {
                $SuccessMsg = "Template Activated";
            } else {
                $SuccessMsg = "Template Inactived";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hqtemplates', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqtemplates', 'action' => 'index'));
        }
    }

    public function enquiryMessages($clearAction=null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $this->layout = "hq_dashboard";
        $this->loadModel('ContactUs');
        $merchant_id = $this->Session->read('merchantId');
        $criteria = "ContactUs.is_deleted=0 AND ContactUs.merchant_id=$merchant_id";
        
        if ($this->Session->read('hqEnquirySearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('hqEnquirySearchData'), true);
        } else {
            $this->Session->delete('hqEnquirySearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        $value='';
        if (!empty($this->request->data)) {
            $this->Session->write('hqEnquirySearchData', json_encode($this->request->data));
           if (!empty($this->request->data['ContactUs']['search'])) {
               $value = trim($this->request->data['ContactUs']['search']);
               $criteria .= " AND (ContactUs.name LIKE '%" . $value . "%' OR ContactUs.email LIKE '%" . $value . "%')";
               
           }
            
        }
        $this->paginate = array('conditions' => array($criteria), 'order' => array('ContactUs.created' => 'DESC'));
        $data = $this->paginate('ContactUs');
        $this->set('enquiryMessages', $data);
        $this->set('keyword', $value);
    }

    public function replyMessage($EncryptContactUsID = null) {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $this->loadModel('ContactUs');
        $DecryptContactUsID = $this->Encryption->decode($EncryptContactUsID);
        $contactUsDetail = $this->ContactUs->getDetailById($DecryptContactUsID);
        $this->set('contactUsDetail', $contactUsDetail);
        $this->loadModel('EmailTemplate');
        if ($this->request->is('post')) {
            $template_type = "merchant_enquiry_reply";
            $this->loadModel('Merchant');
            $storeEmail = $this->Merchant->getMerchantDetail($merchantId);
            $emailTemplate = $this->EmailTemplate->storeTemplates(null, $merchantId, $template_type);
            if (!empty($emailTemplate)) {
                $emailData = $emailTemplate['EmailTemplate']['template_message'];
                $emailData = str_replace('{FULL_NAME}', $contactUsDetail['ContactUs']['name'], $emailData);
                $emailData = str_replace('{MESSAGE}', $this->request->data['ContactUs']['template_message'], $emailData);
                $url = "http://" . $storeEmail['Merchant']['domain_name'];
                $merchantUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Merchant']['domain_name'] . "</a>";
                $emailData = str_replace('{MERCHANT_URL}', $merchantUrl, $emailData);
                $emailData = str_replace('{MERCHANT_COMPANY_NAME}', $storeEmail['Merchant']['company_name'], $emailData);
                $emailData = str_replace('{MERCHANT_ADDRESS}', $storeEmail['Merchant']['address'], $emailData);
                $emailData = str_replace('{MERCHANT_PHONE}', $storeEmail['Merchant']['phone'], $emailData);
                $this->Email->to = $contactUsDetail['ContactUs']['email'];
                $this->Email->subject = $contactUsDetail['ContactUs']['subject'];
                $this->Email->from = $storeEmail['Merchant']['email'];
                $this->set('data', $emailData);
                $this->Email->template = 'template';
                $this->Email->smtpOptions = array(
                    'port' => "$this->smtp_port",
                    'timeout' => '30',
                    'host' => "$this->smtp_host",
                    'username' => "$this->smtp_username",
                    'password' => "$this->smtp_password"
                );
                $this->Email->sendAs = 'html'; // because we like to send pretty mail
                try {
                    if ($this->Email->send()) {
                        $contactUpdate['ContactUs']['id'] = $contactUsDetail['ContactUs']['id'];
                        $contactUpdate['ContactUs']['flag'] = 1;
                        $this->ContactUs->save($contactUpdate);
                        $this->Session->setFlash(__("Message is send successfuly."), 'alert_success');
                        $this->redirect(array('controller' => 'hqtemplates', 'action' => 'enquiryMessages'));
                    }
                } catch (Exception $e) {
                    $this->Session->setFlash("Something went wrong!", 'alert_failed');
                }
            }
        }
    }

    public function merchant_design($clearAction = null) {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $this->loadModel('MerchantDesign');
        if ($this->request->is(array('post', 'put'))) {
            $this->request->data['MerchantDesign']['merchant_id'] = $merchantId;
            if (!empty($this->request->data['MerchantDesign']['id'])) {
                $this->MerchantDesign->create();
            }
            if ($this->MerchantDesign->save($this->request->data)) {
                $this->Session->setFlash(__("Css Successfully Updated."), 'alert_success');
            } else {
                $this->Session->setFlash(__("Something went wrong."), 'alert_failed');
            }
        }
        $mDesignDetail = $this->MerchantDesign->find('first', array('fields' => array('id', 'merchant_css'), 'conditions' => array('merchant_id' => $merchantId, 'is_active' => 1)));
        $this->request->data = $mDesignDetail;
    }

    public function editMerchantCss($EncryptMerchantCssID = null) {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $data['MerchantDesign']['id'] = $this->Encryption->decode($EncryptMerchantCssID);
        $this->loadModel('MerchantDesign');
        $MerchantCssDetail = $this->MerchantDesign->find('first', array('conditions' => array('MerchantDesign.id' => $data['MerchantDesign']['id'])));
        if ($this->request->data) {
            $templateTitle = trim($this->data['MerchantDesign']['template_subject']);
            $isUniqueName = $this->MerchantDesign->checkTemplateUniqueName($templateTitle, null, $data['EmailTemplate']['id']);
            if ($isUniqueName) {
                $templatedata = array();
                $templatedata['template_subject'] = trim($this->data['MerchantDesign']['template_subject']);
                $templatedata['id'] = trim($this->data['MerchantDesign']['id']);
                $templatedata['template_message'] = trim($this->data['MerchantDesign']['template_message']);
                $templatedata['is_active'] = trim($this->data['MerchantDesign']['is_active']);
                $templatedata['merchant_id'] = $merchantId;
                $this->loadModel('MerchantDesign');
                $this->MerchantDesign->create();
                $this->MerchantDesign->saveTemplate($templatedata);

                $this->Session->setFlash(__("Merchant Design Successfully Updated."), 'alert_success');
                $this->redirect(array('controller' => 'hqtemplates', 'action' => 'merchant_design'));
            } else {
                $this->Session->setFlash(__("Merchant Design Name Already exists"), 'alert_failed');
            }
        }

        $this->request->data = $MerchantCssDetail;
    }

    /* ------------------------------------------------
      Function name:deleteTemplate()
      Description:Delete template from list
      created:28/7/2017
      ----------------------------------------------------- */

    public function deleteMerchantCss($EncryptMerchantCssID = null) {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $this->loadModel('MerchantDesign');
        $data['MerchantDesign']['id'] = $this->Encryption->decode($EncryptMerchantCssID);
        $data['MerchantDesign']['is_deleted'] = 1;
        if ($this->MerchantDesign->save($data)) {
            $this->Session->setFlash(__("Merchant Design deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqtemplates', 'action' => 'merchant_design'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqtemplates', 'action' => 'merchant_design'));
        }
    }

    /* ------------------------------------------------
      Function name:activateTemplates()
      Description:Active/Deactive template
      created:28/7/2016
      ----------------------------------------------------- */

    public function activateMerchantCss($EncryptMerchantCssID = null, $status = 0) {
        $this->layout = "hq_dashboard";
        $this->loadModel('MerchantDesign');
        $data['MerchantDesign']['id'] = $this->Encryption->decode($EncryptMerchantCssID);
        $data['MerchantDesign']['is_active'] = $status;
        if ($this->MerchantDesign->save($data)) {
            if ($status) {
                $SuccessMsg = "Merchant Design Activated";
            } else {
                $SuccessMsg = "Merchant Design Inactived";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hqtemplates', 'action' => 'merchant_design'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqtemplates', 'action' => 'merchant_design'));
        }
    }

    public function deleteMessage($EncryptMessageID = null) {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $this->loadModel('ContactUs');
        $data['ContactUs']['id'] = $this->Encryption->decode($EncryptMessageID);
        $data['ContactUs']['is_deleted'] = 1;
        if ($this->ContactUs->saveMessage($data)) {
            $this->Session->setFlash(__("Message deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqtemplates', 'action' => 'enquiryMessages'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqtemplates', 'action' => 'enquiryMessages'));
        }
    }
    
    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('EmailTemplate');
            $merchant_id = $this->Session->read('merchantId');
            $criteria = "EmailTemplate.store_id IS NULL AND EmailTemplate.merchant_id=$merchant_id AND EmailTemplate.is_deleted=0";
            $searchData = $this->EmailTemplate->find('list', array('fields' => array('EmailTemplate.template_subject', 'EmailTemplate.template_subject'), 'conditions' => array('OR' => array('EmailTemplate.template_subject LIKE' => '%' . $_GET['term'] . '%'), $criteria)));
            echo json_encode($searchData);
        } else {
            exit;
        }
    }
    
    public function getEnquiryMessages() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('ContactUs');
            $merchant_id = $this->Session->read('merchantId');
            $searchData = $this->ContactUs->find('all', array('fields' => array('ContactUs.id', 'ContactUs.name', 'ContactUs.email'), 'conditions' => array('OR' => array('ContactUs.name LIKE' => '%' . $_GET['term'] . '%', 'ContactUs.email LIKE' => '%' . $_GET['term'] . '%'), 'ContactUs.merchant_id' => $merchant_id, 'ContactUs.is_deleted' => 0), 'order' => array('ContactUs.created' => 'DESC')));
            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    $new_array[] = array('label' => $val['ContactUs']['name'], 'value' => $val['ContactUs']['name'], 'desc' => $val['ContactUs']['name'] . ", " . $val['ContactUs']['email']);
                };
            }
            echo json_encode($new_array);
        } else {
            exit;
        }
    }

}
