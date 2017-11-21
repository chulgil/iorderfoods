<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
App::uses('HqAppController', 'Controller');

class HqconfigurationsController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption');
    public $uses = array();
    public $layout = 'hq_dashboard';

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
    }

    public function logoPosition() {
        $this->loadModel('MerchantConfiguration');
        $merchantId = $this->Session->read('merchantId');
        if ($this->request->is(array('post', 'put')) && !empty($merchantId)) {
            $this->request->data['MerchantConfiguration']['merchant_id'] = $merchantId;
            if (empty($this->request->data['MerchantConfiguration']['id'])) {
                $this->MerchantConfiguration->create();
            }
            if ($this->MerchantConfiguration->save($this->request->data)) {
                $this->Session->setFlash(__("Update Successfully."), 'alert_success');
            } else {
                $this->Session->setFlash(__("Something went wrong."), 'alert_failed');
            }
        }
        $this->request->data = $this->MerchantConfiguration->findByMerchantId($merchantId);
    }

    public function htmlModule($merchant_content_id = null) {
        if (!empty($merchant_content_id)) {
            $merchant_content_id = $this->Encryption->decode($merchant_content_id);
        } else {
            $this->Session->setFlash(__("Permission Denied"), 'alert_failed');
            $this->redirect($this->referer());
        }
        $this->loadModel('MasterContent');
        $merchantId = $this->Session->read('merchantId');
        $this->loadModel('MerchantContent');
        $pageDetail = $this->MerchantContent->getPageDetail($merchant_content_id, $merchantId);
        $mContent = $this->MasterContent->find('all', array('conditions' => array('merchant_id' => $merchantId, 'is_active' => 1, 'is_deleted' => 0, 'merchant_content_id' => $merchant_content_id)));
        $this->set('mContent', $mContent);
        $this->set('pageDetail', $pageDetail);
    }

    public function htmlLayout() {
        $masterContentId = $this->params->query['content'];
        $merchant_id = $this->Session->read('merchantId');
        if (!empty($masterContentId) && !empty($merchant_id)) {
            $this->loadModel('MasterContent');
            $masterContent = $this->MasterContent->find('first', array('conditions' => array('id' => $masterContentId, 'merchant_id' => $merchant_id, 'is_active' => 1, 'is_deleted' => 0)));
            if (empty($masterContent)) {
                $this->Session->setFlash(__("Please create content module."), 'alert_failed');
                $this->redirect(array('controller' => 'hqconfigurations', 'action' => 'htmlModule'));
            }
            $this->loadModel('ContentLayout');
            $this->ContentLayout->bindModel(
                    array(
                'hasMany' => array(
                    'LayoutBox' => array(
                        'className' => 'LayoutBox',
                        'foreignKey' => 'content_layout_id',
                        'conditions' => array('LayoutBox.is_deleted' => 0, 'LayoutBox.is_active' => 1),
                    )
                )
                    ), false
            );
            $this->loadModel('HomeContent');
            $activeData = $this->HomeContent->find('list', array('fields' => array('layout_box_id'), 'conditions' => array('master_content_id' => $masterContentId, 'merchant_id' => $merchant_id, 'is_active' => 1, 'is_deleted' => 0)));
            $processData = $this->HomeContent->find('list', array('fields' => array('layout_box_id'), 'conditions' => array('master_content_id' => $masterContentId, 'merchant_id' => $merchant_id, 'is_active' => 0, 'is_deleted' => 0)));
            $cLayout = $this->ContentLayout->find('all', array('conditions' => array('is_active' => 1, 'is_deleted' => 0)));
            $this->set(compact('activeData', 'processData', 'cLayout', 'masterContent'));
        } else {
            $this->Session->setFlash(__("No layout."), 'alert_failed');
            $this->redirect($this->referer());
        }
    }

    public function getContentModal() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('ajax')) && !empty($this->request->data['layoutBoxId']) && !empty($this->request->data['contentLayoutId']) && !empty($this->request->data['masterContentId']) && !empty($this->request->data['merchantContentId'])) {
            $merchant_id = $this->Session->read('merchantId');
            if (!empty($merchant_id)) {
                $this->loadModel('HomeContent');
                $this->request->data['HomeContent']['layout_box_id'] = $this->request->data['layoutBoxId'];
                $this->request->data['HomeContent']['content_layout_id'] = $this->request->data['contentLayoutId'];
                $this->request->data['HomeContent']['master_content_id'] = $this->request->data['masterContentId'];
                $this->request->data['HomeContent']['merchant_content_id'] = $this->request->data['merchantContentId'];
//                $homeContentStatus = $this->HomeContent->find('first', array('conditions' => array('content_layout_id !=' => $this->request->data['HomeContent']['content_layout_id'], 'master_content_id' => $this->request->data['HomeContent']['master_content_id'], 'merchant_id' => $merchant_id, 'is_deleted' => 0, 'layout_status' => 0)));
//                if (empty($homeContentStatus)) {
                $homeContentData = $this->HomeContent->find('first', array('conditions' => array('content_layout_id' => $this->request->data['HomeContent']['content_layout_id'], 'layout_box_id' => $this->request->data['HomeContent']['layout_box_id'], 'master_content_id' => $this->request->data['HomeContent']['master_content_id'], 'merchant_id' => $merchant_id, 'is_deleted' => 0)));
                if (!empty($homeContentData)) {
                    $this->request->data = $homeContentData;
                }
//                } else {
//                    $this->set('homeContentStatus', $homeContentStatus);
//                }
                $this->set($this->request->data);
                $this->render('/Elements/htmlModule/layout_modal');
            }
        }
    }

    public function saveLayoutContent() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('ajax'))) {
            parse_str($this->request->data['formData'], $data);
            $merchant_id = $this->Session->read('merchantId');
            if (!empty($data['data']) && !empty($merchant_id)) {
                $data['data']['HomeContent']['merchant_id'] = $merchant_id;
                //prx($data['data']['HomeContent']);
                $this->loadModel('HomeContent');
                if (empty($data['data']['HomeContent']['id'])) {
                    $this->HomeContent->create();
                }
                if ($this->HomeContent->save($data['data']['HomeContent'])) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    /* ------------------------------------------------
      Function name:finalSubmit()
      Description:activate layout
      created:23/11/2016
      ----------------------------------------------------- */

    public function finalSubmit($master_content_id = null, $content_layout_id = null, $status = null) {
        $this->autoRender = false;
        $merchant_id = $this->Session->read('merchantId');
        $this->loadModel('HomeContent');
        if ($status) {
            $msg = "Activate Successfully.";
            $this->HomeContent->updateAll(array('is_active' => 0), array('merchant_id' => $merchant_id, 'master_content_id' => $master_content_id));
            $flag = $this->HomeContent->updateAll(array('is_active' => 1), array('merchant_id' => $merchant_id, 'master_content_id' => $master_content_id, 'content_layout_id' => $content_layout_id));
        } else {
            $msg = "Deactivated Successfully.";
            $flag = $this->HomeContent->updateAll(array('is_active' => 0), array('merchant_id' => $merchant_id, 'master_content_id' => $master_content_id));
        }
        if ($flag) {
            $this->Session->setFlash(__($msg), 'alert_success');
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
        }
        $this->redirect($this->referer());
    }

    public function discardLayout($master_content_id = null, $content_layout_id = null) {
        $this->autoRender = false;
        $merchant_id = $this->Session->read('merchantId');
        $this->loadModel('HomeContent');
        if ($this->HomeContent->updateAll(array('layout_status' => 0, 'is_active' => 0, 'is_deleted' => 1), array('merchant_id' => $merchant_id, 'master_content_id' => $master_content_id, 'content_layout_id' => $content_layout_id))) {
            $this->Session->setFlash(__("Discard Successfully."), 'alert_success');
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
        }
        $this->redirect($this->referer());
    }

    public function contentModule($merchant_content_id = null) {//no use of this function
        $this->loadModel('MasterContent');
        $merchant_id = $this->Session->read('merchantId');
        if ($this->request->is(array('post', 'put'))) {
            if (empty($this->request->data['MasterContent']['id'])) {
                $this->MasterContent->create();
                $conditions = array('LOWER(name)' => strtolower($this->request->data['MasterContent']['name']), 'merchant_id' => $merchant_id);
            } else {
                $conditions = array('LOWER(name)' => strtolower($this->request->data['MasterContent']['name']), 'merchant_id' => $merchant_id, 'id !=' => $this->request->data['MasterContent']['id']);
            }
            $count = $this->MasterContent->find('count', array('conditions' => $conditions));
            if ($count > 0) {
                $this->Session->setFlash(__("Name already exist."), 'alert_failed');
                $this->redirect($this->referer());
            }
            $this->request->data['MasterContent']['merchant_id'] = $merchant_id;
            $this->request->data['MasterContent']['merchant_content_id'] = $this->Encryption->decode($merchant_content_id);
            $this->MasterContent->save($this->request->data);
            $this->Session->setFlash(__("Save Successfully."), 'alert_success');
            $this->redirect(array('controller' => 'hqconfigurations', 'action' => 'htmlModule', $merchant_content_id));
        }
        if (!empty($this->params->query['content'])) {
            $this->request->data = $this->MasterContent->findById($this->params->query['content']);
        }
    }

    public function previewLayout($master_content_id = null, $content_layout_id = null) {
        $this->layout = false;
        $merchant_id = $this->Session->read('merchantId');
        $this->loadModel("Merchant");
        $merchantDetail = $this->Merchant->getMerchantDetail($merchant_id);
        $id = $merchantDetail['Merchant']['id'];
        $name = $merchantDetail['Merchant']['name'];
        $image = $merchantDetail['Merchant']['background_image'];
        $logo = $merchantDetail['Merchant']['logo'];
        $logoType = $merchantDetail['Merchant']['logotype'];
        $bannerImage = $merchantDetail['Merchant']['banner_image'];
        $contactUsBgImage = $merchantDetail['Merchant']['contact_us_bg_image'];
        $phone = $merchantDetail['Merchant']['phone'];
        $m_email = $merchantDetail['Merchant']['email'];
        $this->set(compact('name', 'image', 'logo', 'logoType', 'bannerImage', 'id', 'phone', 'm_email', 'contactUsBgImage'));

        $this->loadModel("MerchantContent");
        $merchantList = $this->MerchantContent->find('all', array('conditions' => array('MerchantContent.merchant_id' => $merchant_id, 'MerchantContent.is_active' => 1, 'MerchantContent.is_deleted' => 0), 'order' => array('MerchantContent.position' => 'ASC')));
        $this->loadModel("SocialMedia");
        $socialLinks = $this->SocialMedia->find('first', array('conditions' => array('merchant_id' => $merchant_id, 'store_id' => NULL, 'is_active' => 1, 'is_deleted' => 0)));
        $this->loadModel("Store");
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $store = $this->Store->find('all', array('fields' => array('id', 'merchant_id', 'store_name', 'store_url', 'phone', 'address', 'city', 'state', 'latitude', 'logitude', 'zipcode'), 'conditions' => array('Store.merchant_id' => $merchant_id, 'Store.is_deleted' => 0, 'Store.is_active' => 1)));
        $this->loadModel('MerchantConfiguration');
        $logoPosition = $this->MerchantConfiguration->find('first', array('conditions' => array('merchant_id' => $merchant_id), 'fields' => array('logo_position', 'contact_active', 'map_zoom_level')));
        $this->set(compact('store', 'merchantList', 'socialLinks', 'logoPosition'));

        $this->loadModel('MerchantGallery');
        $photo = $this->MerchantGallery->getSlidersImages($merchant_id);
        $this->set(compact('photo'));

        $this->loadModel('HomeContent');
        $this->HomeContent->bindModel(
                array(
            'belongsTo' => array(
                'LayoutBox' => array(
                    'className' => 'LayoutBox',
                    'foreignKey' => 'layout_box_id',
                    'conditions' => array('LayoutBox.is_deleted' => 0, 'LayoutBox.is_active' => 1),
                )
            )
                ), false
        );
        $homeContentData = $this->HomeContent->find('all', array('recursive' => 2, 'conditions' => array('HomeContent.content_layout_id' => $content_layout_id, 'HomeContent.master_content_id' => $master_content_id, 'HomeContent.merchant_id' => $merchant_id, 'HomeContent.is_deleted' => 0)));
        $this->set(compact('homeContentData'));
    }

    public function saveTermsAndPolicies($EncryptTermsAndPolicyID = null) {
        $merchantId = $this->Session->read('merchantId');
        $this->loadModel('TermsAndPolicy');
        if ($this->request->is(array('post', 'put')) && !empty($merchantId)) {
            if (!empty($EncryptTermsAndPolicyID)) {
                $decryptTermsAndPolicyID = $this->Encryption->decode($EncryptTermsAndPolicyID);
                $this->request->data['TermsAndPolicy']['id'] = $decryptTermsAndPolicyID;
            }
            $this->request->data['TermsAndPolicy']['merchant_id'] = $merchantId;
            if (empty($this->request->data['TermsAndPolicy']['id'])) {
                $this->TermsAndPolicy->create();
            }
            if ($this->TermsAndPolicy->save($this->request->data)) {
                $this->Session->setFlash(__("Update Successfully."), 'alert_success');
                $this->redirect(array('controller' => 'hq', 'action' => 'merchantPageList'));
            } else {
                $this->Session->setFlash(__("Something went wrong."), 'alert_failed');
                $this->redirect(array('controller' => 'hq', 'action' => 'merchantPageList'));
            }
        }
        $this->request->data = $this->TermsAndPolicy->find('first', array('conditions' => array('TermsAndPolicy.merchant_id' => $merchantId, 'is_deleted' => 0, 'TermsAndPolicy.store_id' => NULL)));
    }

    public function openContentModal() {
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            if (!empty($this->request->data['masterContentId'])) {
                $this->loadModel("MasterContent");
                $data = $this->MasterContent->findById($this->request->data['masterContentId']);
                $response['status'] = 'Success';
                $response['name'] = $data['MasterContent']['name'];
                return json_encode($response);
            }
        }
    }

    public function saveContentModal() {
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            $this->loadModel("MasterContent");
            parse_str($this->request->data['formData'], $data);
            $this->request->data = $data['data'];
            $merchant_id = $this->Session->read('merchantId');
            if (empty($this->request->data['MasterContent']['id'])) {
                $this->MasterContent->create();
                $conditions = array('LOWER(name)' => strtolower($this->request->data['MasterContent']['name']), 'merchant_id' => $merchant_id);
            } else {
                $conditions = array('LOWER(name)' => strtolower($this->request->data['MasterContent']['name']), 'merchant_id' => $merchant_id, 'id !=' => $this->request->data['MasterContent']['id']);
            }

            $count = $this->MasterContent->find('count', array('conditions' => $conditions));
            if ($count > 0) {
                $response['status'] = 'Error';
                $response['msg'] = 'Name already exist.';
            } else {
                $this->request->data['MasterContent']['merchant_id'] = $merchant_id;
                $this->request->data['MasterContent']['merchant_content_id'] = $this->Encryption->decode($this->request->data['MasterContent']['merchant_content_id']);
                $this->MasterContent->save($this->request->data);
                $response['status'] = 'Success';
                $response['msg'] = 'Save successfully.';
            }
            return json_encode($response);
        }
    }

    public function checkUniqueName() {
        $this->autoRender = false;
        if ($this->request->is('ajax') && !empty($_POST['data']['MasterContent']['name'])) {
            $merchant_id = $this->Session->read('merchantId');
            if (empty($_POST['MasterContentId'])) {
                $conditions = array('LOWER(name)' => strtolower($this->request->data['MasterContent']['name']), 'merchant_id' => $merchant_id);
            } else {
                $conditions = array('LOWER(name)' => strtolower($this->request->data['MasterContent']['name']), 'merchant_id' => $merchant_id, 'id !=' => $_POST['MasterContentId']);
            }
            $this->loadModel("MasterContent");
            $count = $this->MasterContent->find('count', array('conditions' => $conditions));
            if ($count > 0) {
                $valid = false;
            } else {
                $valid = true;
            }
            echo json_encode($valid);
        }
    }

    public function merchantEditPage() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('ajax', 'put', 'post'))) {
            $data = $this->request->data;
            //parse_str($this->request->data['formData'], $data);
            //$data = $data['data'];
            if (!empty($data['MerchantContent']['id'])) {
                $merchantId = $this->Session->read('merchantId');
                $this->loadModel('MerchantContent');
                $pageTitle = trim($data['MerchantContent']['name']);
                $isUniqueName = $this->MerchantContent->checkPageUniqueName($pageTitle, $merchantId, $data['MerchantContent']['id']);
                if ($isUniqueName) {
                    $pagedata = array();
                    if (!empty($data['MerchantContent']['page_type']) && $data['MerchantContent']['page_type'] == 'reserved') {
                        $pagedata['content_key'] = trim($data['MerchantContent']['name']);
                    } else {
                        $pagedata['name'] = trim($data['MerchantContent']['name']);
                    }
                    $pagedata['id'] = trim($data['MerchantContent']['id']);
                    $pagedata['is_active'] = trim($data['MerchantContent']['is_active']);
                    $pagedata['page_position'] = (!empty($data['MerchantContent']['page_position'])) ? $data['MerchantContent']['page_position'] : 1;
                    //$pagedata['page_position'] = 1;
                    $this->MerchantContent->savePage($pagedata);
                    $this->Session->setFlash(__("Detail Successfully Updated."), 'alert_success');
                } else {
                    $this->Session->setFlash(__("Page name already exists."), 'alert_failed');
                }
                $this->redirect($this->referer());
            }
        }
    }

    public function deleteMasterContent($merchantContentId = null, $masterContentId = null) {
        $this->autoRender = false;
        $this->layout = false;
        if (!empty($merchantContentId) && !empty($masterContentId)) {
            $merchantContentId = $this->Encryption->decode($merchantContentId);
            $this->loadModel("MasterContent");
            $this->loadModel("HomeContent");
            $merchantId = $this->Session->read('merchantId');
            $this->MasterContent->updateAll(array('MasterContent.is_deleted' => 1), array('MasterContent.id' => $masterContentId, 'MasterContent.merchant_id' => $merchantId));
            $this->HomeContent->updateAll(array('HomeContent.is_deleted' => 1), array('HomeContent.merchant_content_id' => $merchantContentId, 'HomeContent.master_content_id' => $masterContentId));
            $this->Session->setFlash(__("Deleted Successfuly"), 'alert_success');
            $this->redirect($this->referer());
        }
    }

}
