<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HqitemoffersController
 *
 * @author vikassingh
 */
App::uses('HqAppController', 'Controller');

class HqitemoffersController extends HqAppController {

    //put your code here
    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'Common');
    public $uses = array('Item', 'ItemPrice', 'Store', 'ItemOffer', 'Category');
    public $layout = "hq_dashboard";

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function getCategory() {
        if ($this->request->is('ajax') && $this->request->data['storeId']) {
            $this->loadModel('Category');
            if ($this->request->data['storeId'] == 'All') {
                $merchant_id = $this->Session->read('merchantId');
                $categoryList = $this->Category->getCategoryListWithDuplicateName($merchant_id);
            } else {
                $categoryList = $this->Category->getCategoryList($this->request->data['storeId']);
            }
            $this->set('categoryList', $categoryList);
        } else {
            exit;
        }
    }

    /* ------------------------------------------------
      Function name:itemsBycategory()
      Description:get items by category
      created:25/8/2016
      ----------------------------------------------------- */

    public function itemsByCategory($categoryId = null, $storeID = null) {
        $itemList = '';
        if ($this->request->is('ajax') && $categoryId && $storeID) {
            if ($storeID == 'All') {
                $merchant_id = $this->Session->read('merchantId');
                $cData = $this->Category->find('list', array('conditions' => array('merchant_id' => $merchant_id, 'name' => strtolower($categoryId), 'is_active' => 1, 'is_deleted' => 0), 'fields' => array('id')));
                if (!empty($cData)) {
                    $itemList = $this->Item->find('list', array('fields' => array('Item.name', 'Item.name'), 'conditions' => array('Item.merchant_id' => $merchant_id, 'Item.is_active' => 1, 'Item.is_deleted' => 0, 'Item.category_id' => $cData), 'group' => 'name'));
                }
            } else {
                $itemList = $this->Item->getItemsByCategory($categoryId, $storeID);
            }
            $this->set('itemList', $itemList);
        } else {
            exit;
        }
    }

    private function _saveItemOffer($merchantId = null, $postData = null) {
        $this->request->data = $postData;
        $isUniqueOffer = $this->ItemOffer->checkUniqueOffer($this->request->data['ItemOffer']['item_id'], $this->request->data['ItemOffer']['store_id']);
        if ($isUniqueOffer) {
            $offerData = array();
            $offerData['ItemOffer']['item_id'] = $this->request->data['ItemOffer']['item_id'];
            $offerData['ItemOffer']['is_active'] = $this->request->data['ItemOffer']['is_active'];
            $offerData['ItemOffer']['category_id'] = $this->request->data['ItemOffer']['category_id'];
            $offerData['ItemOffer']['unit_counter'] = $this->request->data['ItemOffer']['unit_counter'];
            $offerData['ItemOffer']['start_date'] = $this->Dateform->formatDate($this->request->data['ItemOffer']['start_date']);
            $offerData['ItemOffer']['end_date'] = $this->Dateform->formatDate($this->request->data['ItemOffer']['end_date']);
            $offerData['ItemOffer']['store_id'] = $this->request->data['ItemOffer']['store_id'];
            $offerData['ItemOffer']['merchant_id'] = $merchantId;
            $this->ItemOffer->create();
            $this->ItemOffer->saveItemOffer($offerData);
            $this->Session->setFlash(__("Offer Successfully Added"), 'alert_success');
        } else {
            $this->Session->setFlash(__("Offer Already exists"), 'alert_failed');
        }
    }

    /* ------------------------------------------------
      Function name:index()
      Description:Add Iteam offers and show list
      created:25/8/2016
      ----------------------------------------------------- */

    public function index() {
        $storeId = @$this->request->data['ItemOffer']['store_id'];
        if ($this->request->is('post') && !empty($this->request->data['ItemOffer']['unit_counter'])) {
            $merchantId = $this->Session->read('merchantId');
            $this->request->data = $this->Common->trimValue($this->request->data);
            if (!empty($this->request->data['ItemOffer']['item_id'])) {
                if ($this->request->data['ItemOffer']['store_id'] == 'All') {
                    $categoryName = $this->data['ItemOffer']['category_id'];
                    $itemName = $this->request->data['ItemOffer']['item_id'];
                    $storeData = $this->Store->getAllStoreByMerchantId($merchantId);
                    if (!empty($storeData)) {
                        foreach ($storeData as $store) {
                            $this->request->data['ItemOffer']['store_id'] = $store['Store']['id'];
                            $categoryData = $this->Category->find('first', array('fields' => 'id', 'conditions' => array('name' => strtolower($categoryName), 'store_id' => $store['Store']['id'], 'is_deleted' => 0, 'is_active' => 1), 'recursive' => -1));
                            if (!empty($categoryData)) {
                                $this->request->data['ItemOffer']['category_id'] = $categoryData['Category']['id'];
                                $itemData = $this->Item->getItemListIds($this->request->data['ItemOffer']['store_id'], $itemName);
                                if (!empty($itemData)) {
                                    $this->request->data['ItemOffer']['item_id'] = $itemData['Item']['id'];
                                    $this->_saveItemOffer($merchantId, $this->request->data);
                                }
                            }
                        }
                    }
                    $this->request->data = '';
                } else {
                    $this->_saveItemOffer($merchantId, $this->request->data);
                    $this->request->data = '';
                }
            } else {
                $this->Session->setFlash(__("Please select item"), 'alert_failed');
            }
        }
        $categoryList = $this->Category->getCategoryList($storeId);
        $itemList = $this->Item->getallItemsByStore($storeId);
        $this->set(compact('categoryList', 'itemList'));
        $this->_itemOfferList();
    }

    private function _itemOfferList($clearAction = null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $storeID = @$this->request->data['ItemOffer']['storeId'];
        $merchant_id = $this->Session->read('merchantId');
        $criteria = "ItemOffer.merchant_id =$merchant_id AND ItemOffer.is_deleted=0";
        if (!empty($storeID)) {
            $criteria .= " AND ItemOffer.store_id =$storeID";
        }
        if ($this->Session->read('HqItemOfferSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqItemOfferSearchData'), true);
        } else {
            $this->Session->delete('HqItemOfferSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('HqItemOfferSearchData', json_encode($this->request->data));
            if (isset($this->request->data['ItemOffer']['isActive']) && $this->request->data['ItemOffer']['isActive'] != '') {
                $active = trim($this->request->data['ItemOffer']['isActive']);
                $criteria .= " AND (ItemOffer.is_active =$active)";
            }
            if (!empty($this->request->data['ItemOffer']['search'])) {
                $search = trim($this->request->data['ItemOffer']['search']);
                $criteria .= " AND (Item.name LIKE '%" . $search . "%')";
            }
        }

        $this->ItemOffer->bindModel(
                array(
            'belongsTo' => array(
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id',
                    'type' => 'inner',
                    'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                    'fields' => array('id', 'name')
                ), "Store" => array('className' => 'Store',
                    'foreignKey' => 'store_id',
                    'type' => 'inner',
                    'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1),
                    'fields' => array('store_name'))
            )
                ), false
        );

        $this->paginate = array('conditions' => array($criteria), 'order' => array('ItemOffer.created' => 'DESC'));
        $ItemOfferdetail = $this->paginate('ItemOffer');
        $this->set('list', $ItemOfferdetail);
    }

    /* ------------------------------------------------
      Function name:activateCoupon()
      Description:Active/deactive Coupon
      created:08/8/2015
      ----------------------------------------------------- */

    public function activateOffer($EncryptOfferID = null, $status = 0) {
        $this->autoRender = false;
        $data['ItemOffer']['merchant_id'] = $this->Session->read('merchantId');
        $data['ItemOffer']['id'] = $this->Encryption->decode($EncryptOfferID);
        $data['ItemOffer']['is_active'] = $status;
        if ($this->ItemOffer->saveItemOffer($data)) {
            if ($status) {
                $SuccessMsg = "Offer Activated";
                $this->Session->setFlash(__($SuccessMsg), 'alert_success');
                $this->redirect(array('controller' => 'hqitemoffers', 'action' => 'edit/' . $EncryptOfferID . "#ItemOfferStartDate"));
            } else {
                $SuccessMsg = "Offer Deactivated";
                $this->Session->setFlash(__($SuccessMsg), 'alert_success');
                $this->redirect(array('controller' => 'hqitemoffers', 'action' => 'index'));
            }
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqitemoffers', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteCoupon()
      Description:Delete Coupon
      created:08/8/2015
      ----------------------------------------------------- */

    public function deleteOffer($EncryptOfferID = null) {
        $this->autoRender = false;
        $data['ItemOffer']['merchant_id'] = $this->Session->read('merchantId');
        $data['ItemOffer']['id'] = $this->Encryption->decode($EncryptOfferID);
        $data['ItemOffer']['is_deleted'] = 1;
        if ($this->ItemOffer->saveItemOffer($data)) {
            $this->Session->setFlash(__("Offer deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqitemoffers', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqitemoffers', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:editCoupon()
      Description:Edit Coupon
      created:08/8/2015
      ----------------------------------------------------- */

    public function edit($EncryptOfferID = null) {
        $merchantId = $this->Session->read('merchantId');
        $OfferID = $this->Encryption->decode($EncryptOfferID);
        $offerDetail = $this->ItemOffer->findById($OfferID);
        $storeId = $offerDetail['ItemOffer']['store_id'];
        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            if (!empty($this->request->data['ItemOffer']['item_id'])) {
                $isUniqueOffer = $this->ItemOffer->checkUniqueOffer($this->request->data['ItemOffer']['item_id'], $storeId, $offerDetail['ItemOffer']['id']);
                if ($isUniqueOffer) {
                    $offerData = array();
                    $offerData['ItemOffer']['id'] = $offerDetail['ItemOffer']['id'];
                    $offerData['ItemOffer']['item_id'] = $this->request->data['ItemOffer']['item_id'];
                    $offerData['ItemOffer']['is_active'] = $this->request->data['ItemOffer']['is_active'];
                    $offerData['ItemOffer']['category_id'] = $this->request->data['ItemOffer']['category_id'];
                    $offerData['ItemOffer']['unit_counter'] = $this->request->data['ItemOffer']['unit_counter'];
                    $offerData['ItemOffer']['start_date'] = $this->Dateform->formatDate($this->request->data['ItemOffer']['start_date']);
                    $offerData['ItemOffer']['end_date'] = $this->Dateform->formatDate($this->request->data['ItemOffer']['end_date']);
                    $offerData['ItemOffer']['store_id'] = $storeId;
                    $offerData['ItemOffer']['merchant_id'] = $merchantId;
                    $this->ItemOffer->saveItemOffer($offerData);

                    $this->Session->setFlash(__("Offer Successfully updated"), 'alert_success');
                    $this->redirect(array('controller' => 'hqitemoffers', 'action' => 'index'));
                } else {
                    $this->Session->setFlash(__("Offer Already exists"), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Please select item"), 'alert_failed');
            }
        }
        $this->request->data = $offerDetail;
        $categoryList = $this->Category->getCategoryList($storeId);
        //$itemList = $this->Item->getallItemsByStore($storeId);
        $this->Item->bindModel(array(
            'belongsTo' => array('Category')
        ));
        $itemList = $this->Item->find('list', array(
            'fields' => array('Item.id', 'Item.name'),
            'conditions' => array('Item.store_id' => $storeId, 'Item.is_deleted' => 0, 'Item.is_active' => 1, 'Category.is_deleted' => 0, 'Category.is_active' => 1),
            'recursive' => 1,
            'order' => array('Item.name ASC')
        ));
        $this->set(compact('categoryList', 'itemList'));
    }

    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('Item');
            if (!empty($_GET['storeId'])) {
                $storeID = $_GET['storeId'];
            } else {
                $merchant_id = $this->Session->read('merchantId');
                $storeID = $this->Store->getAllStoresByMerchantId($merchant_id);
            }
            $searchData = $this->Item->find('list', array('fields' => array('Item.name', 'Item.name'), 'conditions' => array('OR' => array('Item.name LIKE' => '%' . $_GET['term'] . '%'), 'Item.is_deleted' => 0, 'Item.store_id' => $storeID)));
            echo json_encode($searchData);
        } else {
            exit;
        }
    }

    /* ------------------------------------------------
      Function name:shareOffer()
      Description:Share the coupon to customers
      created:13/06/2017
      ----------------------------------------------------- */

    public function shareExtendedOffer($EncryptExtendedOfferId = null) {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        if (!empty($_GET['extendedOfferId'])) {
            $EncryptExtendedOfferId = $_GET['extendedOfferId'];
        }
        if ($EncryptExtendedOfferId) {
            $extendedOfferId = $this->Encryption->decode($EncryptExtendedOfferId);
        } else {
            $extendedOfferId = $this->request->data['User']['extended_offer_id'];
        }
        $this->ItemOffer->bindModel(
                array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1),
                    'fields' => array('store_name')
                )
            )), false
        );
        $itemOfferData = $this->ItemOffer->findById($extendedOfferId);
        if (empty($itemOfferData)) {
            $this->Session->setFlash(__("Something went wrong!."), 'alert_failed');
            $this->redirect($this->referer());
        }
        $storeId = $itemOfferData['ItemOffer']['store_id'];
        if ($this->request->is(array('post', 'put'))) {
            $this->request->data['User']['id'] = array_filter($this->request->data['User']['id']);
            $this->loadModel('Store');
            $storeEmail = $this->Store->fetchStoreDetail($storeId);
            foreach ($this->request->data['User']['id'] as $data) {
                $this->loadModel('User');
                $this->User->bindModel(array('belongsTo' => array('CountryCode')));
                $shareuserdetail = $this->User->find('first', array('fields' => array('User.id', 'User.fname', 'User.lname', 'User.email', 'User.phone', 'User.is_emailnotification', 'User.is_smsnotification', 'User.country_code_id', 'CountryCode.code'), 'conditions' => array('User.id' => $data)));
                if (!empty($shareuserdetail)) {
                    $this->ItemOffer->bindModel(
                            array(
                        'belongsTo' => array(
                            'Item' => array(
                                'className' => 'Item',
                                'foreignKey' => 'item_id',
                                'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                                'fields' => array('id', 'name'),
                                'type' => "INNER"
                            )
                        )
                            ), false
                    );
                    $itemOfferDetail = $this->ItemOffer->getOfferDetail($extendedOfferId, $storeId);
                    if (!empty($itemOfferDetail)) {
                        if (($itemOfferDetail['ItemOffer']['unit_counter'] - 1) > 1) {//units
                            $detail = 'Buy ' . (@$itemOfferDetail['ItemOffer']['unit_counter'] - 1) . ' Units of ' . @$itemOfferDetail['Item']['name'] . ' starting ' . date("m-d-Y", strtotime($itemOfferDetail['ItemOffer']['start_date'])) . ' to ' . date("m-d-Y", strtotime($itemOfferDetail['ItemOffer']['end_date'])) . ' and the next one is on us!';
                        } else {//unit
                            $detail = 'Buy ' . (@$itemOfferDetail['ItemOffer']['unit_counter'] - 1) . ' Unit of ' . @$itemOfferDetail['Item']['name'] . ' starting ' . date("m-d-Y", strtotime($itemOfferDetail['ItemOffer']['start_date'])) . ' to ' . date("m-d-Y", strtotime($itemOfferDetail['ItemOffer']['end_date'])) . ' and the next one is on us!';
                        }
                        $template_type = 'extended_offer';
                        $this->loadModel('EmailTemplate');
                        $emailSuccess = $this->EmailTemplate->storeTemplates($storeId, $merchantId, $template_type);
                        if ($emailSuccess) {
                            if ($shareuserdetail['User']['lname']) {
                                $fullName = $shareuserdetail['User']['fname'] . " " . $shareuserdetail['User']['lname'];
                            } else {
                                $fullName = $shareuserdetail['User']['fname'];
                            }
                            $emailData = $emailSuccess['EmailTemplate']['template_message'];
                            $subject = $emailSuccess['EmailTemplate']['template_subject'];
                            if ($shareuserdetail['User']['is_emailnotification'] == 1) {
                                $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                                $emailData = str_replace('{DETAIL_TEXT}', $detail, $emailData);
                                $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);
                                $storeAddress = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
                                $storePhone = $storeEmail['Store']['phone'];
                                $url = "http://" . $storeEmail['Store']['store_url'];
                                $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
                                $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                                $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                                $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);
                                $subject = ucwords(str_replace('_', ' ', $subject));
                                $this->Email->to = $shareuserdetail['User']['email'];
                                $this->Email->subject = $subject;
                                $this->Email->from = $storeEmail['Store']['email_id'];
                                $this->set('data', $emailData);
                                $this->Email->template = 'template';
                                $this->Email->smtpOptions = array(
                                    'port' => "$this->smtp_port",
                                    'timeout' => '30',
                                    'host' => "$this->smtp_host",
                                    'username' => "$this->smtp_username",
                                    'password' => "$this->smtp_password"
                                );
                                $this->Email->sendAs = 'html';
                                try {
                                    $this->Email->send();
                                } catch (Exception $e) {
                                    
                                }
                            }
                            if ($shareuserdetail['User']['is_smsnotification'] == 1) {
                                $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                                $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                                $smsData = str_replace('{DETAIL_TEXT}', $detail, $smsData);
                                $smsData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $smsData);
                                $smsData = str_replace('{STORE_PHONE}', $storePhone, $smsData);
                                $message = $smsData;
                                $mob = $shareuserdetail['CountryCode']['code'] . "" . str_replace(array('(', ')', ' ', '-'), '', $shareuserdetail['User']['phone']);
                                $this->Common->sendSmsNotification($mob, $message);
                            }
                        }
                    }
                }
            }
            $message = "Extended offer send successfully";
            $this->Session->setFlash(__($message), 'alert_success');
            $this->redirect($this->referer());
        }
        $this->loadModel('User');
        $criteria = array('User.store_id' => $storeId, 'User.role_id' => 4, 'User.is_deleted' => 0, 'User.is_active' => 1);
        $this->paginate = array('fields' => array('User.fname', 'User.lname', 'User.email', 'User.id', 'User.created'), 'conditions' => array($criteria), 'order' => array('User.created' => 'DESC'));
        $list = $this->paginate('User');
        $this->set(compact('list', 'itemOfferData'));
    }

    /* ------------------------------------------------
      Function name:deleteMultipleItemOffer()
      Description:Delete multiple item offers
      created:02/08/2017
      ----------------------------------------------------- */

    public function deleteMultipleItemOffer() {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        if ($this->request->is(array('post')) && !empty($this->request->data['ItemOffer']['id'])) {
            $filter_array = array_filter($this->request->data['ItemOffer']['id']);
            if ($this->Common->deleteMultipleRecords($filter_array, 'ItemOffer')) {
                $msg = "Extended offers deleted successfully.";
                $msgType = "alert_success";
            } else {
                $msg = "Some problem occured.";
                $msgType = "alert_failed";
            }
            $this->Session->setFlash(__($msg), $msgType);
            $this->redirect($this->referer());
        }
    }

}
