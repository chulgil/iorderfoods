<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HqstoresController
 *
 * @author vikassingh
 */
App::uses('HqAppController', 'Controller');

class HqstoresController extends HqAppController {

    //put your code here
    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Paginator', 'Common', 'Dateform', 'HqCommon');
    public $helper = array('Encryption', 'Paginator', 'Form', 'DateformHelper', 'Common');
    public $uses = array('User', 'StoreGallery', 'Store', 'StoreBreak', 'StoreAvailability', 'StoreHoliday', 'Category', 'Tab', 'Permission', 'StoreTheme', 'Merchant', 'StoreTax', 'StoreFont');
    public $layout = 'hq_dashboard';

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $merchant_id = $this->Session->read('merchantId');
        $criteria = "Store.merchant_id =$merchant_id AND Store.is_deleted=0";
        $value='';
        if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        if (!empty($this->request->data)) {
            if (!empty($this->request->data['StoreHour']['search'])) {
                $value = trim($this->request->data['StoreHour']['search']);
                if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $value))
                        {
                            $value=explode("'",$value);
                            $value=$value[0];
                            // one or more of the 'special characters' found in $string
                        }
                $criteria .= " AND (Store.store_name LIKE '%" . $value . "%')";
            }
        }
        $this->paginate = array('fields' => array('id', 'store_name', 'is_active'), 'conditions' => array($criteria), 'order' => array('Store.created DESC'), 'recursive' => -1);
        $storedetail = $this->paginate('Store');
        $this->set('list', $storedetail);
        $this->set('keyword', $value);
    }

    /* ------------------------------------------------
      Function name:manageTimings()
      Description:Manage Store Open and close Timings
      created:27/7/2015
      ----------------------------------------------------- */

    public function manageTimings($EncryptedStoreID) {
        $storeId = $this->Encryption->decode($EncryptedStoreID);
        $this->Session->write('StoreIDMT', $storeId);
        $merchantId = $this->Session->read('merchantId');
        $this->set('userid', $this->Session->read('Auth.Admin.id'));
        $this->set('roleid', $this->Session->read('Auth.Admin.role_id'));
        $this->set('storeId', $storeId);
        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $this->request->data['Store']['id'] = $storeId;
            if ($this->Store->saveStoreInfo($this->Common->trimValue($this->request->data['Store']))) {
                $this->Session->setFlash(__("Store Timings successfully Updated"), 'alert_success');
                $this->redirect(array('controller' => 'Stores', 'action' => 'manageTimings/' . $EncryptedStoreID));
            } else {
                $this->Session->setFlash(__("Some problem occurred"), 'alert_failed');
                $this->redirect(array('controller' => 'Stores', 'action' => 'manageTimings/' . $EncryptedStoreID));
            }
        }
        $storeInfo = $this->Store->fetchStoreDetail($storeId, $merchantId);
        $this->set('StoreBreak', $storeInfo['Store']['is_break_time']);
        $this->set('StoreBreak1', $storeInfo['Store']['is_break1']);
        $this->set('StoreBreak2', $storeInfo['Store']['is_break2']);
        $start = "00:00";
        $end = "23:59";
        $timeRange = $this->HqCommon->getStoreTimeAdmin($start, $end, $storeId);
        $this->set('timeOptions', $timeRange);
        $this->request->data['Store'] = $storeInfo['Store'];
        $holidayInfo = $this->StoreHoliday->getStoreHolidayInfo($storeId);
        $this->set('holidayInfo', $holidayInfo);
        $this->StoreAvailability->bindModel(
                array(
            'hasOne' => array(
                'StoreBreak' => array(
                    'className' => 'StoreBreak',
                    'foreignKey' => 'store_availablity_id',
                    'conditions' => array('StoreBreak.is_deleted' => 0, 'StoreBreak.is_active' => 1, 'StoreBreak.store_id' => $storeId),
                )
            )
                ), false
        );
        $availabilityInfo = $this->StoreAvailability->getStoreAvailabilityDetails($storeId);
        $this->set('availabilityInfo', $availabilityInfo);
        $daysarr = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $this->set('daysarr', $daysarr);
    }

    /* ------------------------------------------------
      Function name:activateStore()
      Description:Active/deactive store
      created:29/8/2016
      ----------------------------------------------------- */

    public function activateStore($EncryptedStoreID = null, $status = 0) {
        $this->autoRender = false;
        $data['Store']['merchant_id'] = $this->Session->read('merchantId');
        $data['Store']['id'] = $this->Encryption->decode($EncryptedStoreID);
        $data['Store']['is_active'] = $status;
        if ($this->Store->saveStoreInfo($data)) {
            if ($status) {
                $SuccessMsg = "Store Activated";
            } else {
                $SuccessMsg = "Store Deactivated";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hqstores', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqstores', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteStore()
      Description:Delete Type
      created:29/8/2016
      ----------------------------------------------------- */

    public function deleteStore($EncryptedStoreID = null) {
        $this->autoRender = false;
        $data['Store']['merchant_id'] = $this->Session->read('merchantId');
        $data['Store']['id'] = $this->Encryption->decode($EncryptedStoreID);
        $data['Store']['is_deleted'] = 1;
        if ($this->Type->saveType($data)) {
            $this->Session->setFlash(__("Store deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqstores', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqstores', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:addClosedDate()
      Description:delete closed date from list
      created:29/8/2016
      ----------------------------------------------------- */

    public function addClosedDate() {
        $this->autoRender = false;
        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $data['store_id'] = $this->Session->read('StoreIDMT');
            $holidayDate = $this->Dateform->formatDate($this->request->data['StoreHoliday']['holiday_date']);
            $data['holiday_date'] = trim($holidayDate);
            $data['description'] = trim($this->request->data['StoreHoliday']['description']);
            if ($this->StoreHoliday->storeHolidayNotExists($holidayDate)) {
                if ($this->StoreHoliday->saveStoreHolidayInfo($data)) {
                    $this->Session->setFlash(__("Closed Holiday Successfully added"), 'alert_success');
                    $this->redirect(array('controller' => 'hqstores', 'action' => 'manageTimings/' . $this->Encryption->encode($data['store_id'])));
                } else {
                    $this->Session->setFlash(__("Some Problem occured"), 'alert_failed');
                    $this->redirect(array('controller' => 'hqstores', 'action' => 'manageTimings/' . $this->Encryption->encode($data['store_id'])));
                }
            } else {
                $this->Session->setFlash(__("Closed Date already exists"), 'alert_failed');
                $this->redirect(array('controller' => 'hqstores', 'action' => 'manageTimings/' . $this->Encryption->encode($data['store_id'])));
            }
        }
    }

    /* ------------------------------------------------
      Function name:deleteHoliday()
      Description:delete closed date from list
      created:29/7/2015
      ----------------------------------------------------- */

    public function deleteHoliday($EncryptedHolidayID = null) {

        $this->autoRender = false;
        $HolidayID = $this->Encryption->decode($EncryptedHolidayID);
        if ($HolidayID) {
            $storeId = $this->Session->read('StoreIDMT');
            $data['id'] = $HolidayID;
            $data['is_deleted'] = $HolidayID;
            if ($this->StoreHoliday->saveStoreHolidayInfo($data)) {
                $this->Session->setFlash(__("Closed Holiday Successfully deleted"), 'alert_success');
                $this->redirect(array('controller' => 'hqstores', 'action' => 'manageTimings/' . $this->Encryption->encode($storeId)));
            } else {
                $this->Session->setFlash(__("Some Problem occured"), 'alert_failed');
                $this->redirect(array('controller' => 'hqstores', 'action' => 'manageTimings/' . $this->Encryption->encode($storeId)));
            }
        }
    }

    /* ------------------------------------------------
      Function name:updatestoreAvailability()
      Description:Update store timing basis of days
      created:29/7/2015
      ----------------------------------------------------- */

    public function updatestoreAvailability() {
        $this->autoRender = false;
        $storeId = $this->Session->read('StoreIDMT');
        if ($this->request->data) {
            $storeclosedata['Store']['close_details'] = $this->Common->trimValue($this->request->data['Store']['close_details']);
            $storeclosedata['Store']['id'] = $storeId;
            $this->Store->saveStoreInfo($storeclosedata);
            $this->request->data = $this->Common->trimValue($this->request->data);
            foreach ($this->request->data['StoreAvailability'] as $key => $value) {
                $value['store_id'] = $storeId;
                if (!isset($value['id'])) {
                    $this->StoreAvailability->create();
                }
                $this->StoreAvailability->saveStoreAvailabilityInfo($value);
            }
            $storedata['id'] = $storeId;
            if (isset($this->request->data['Store']['is_break_time'])) {
                $storedata['is_break_time'] = $this->request->data['Store']['is_break_time'];
            }
            if (isset($this->request->data['Store']['is_break_time'])) {
                $storedata['is_break1'] = $this->request->data['Store']['is_break1'];
            }
            if (isset($this->request->data['Store']['is_break_time'])) {
                $storedata['is_break2'] = $this->request->data['Store']['is_break2'];
            }

            $this->Store->saveStoreInfo($storedata);
            foreach ($this->request->data['StoreBreak'] as $key => $value) {
                $value['store_id'] = $storeId;
                if (!isset($value['id'])) {
                    $this->StoreBreak->create();
                }
                $this->StoreBreak->saveStoreBreak($value);
            }
        }
        $this->Session->setFlash(__("Weekly Timings Successfully Updated"), 'alert_success');
        $this->redirect(array('controller' => 'hqstores', 'action' => 'manageTimings/' . $this->Encryption->encode($storeId)));
    }
    
    public function getMerchantStoreNames() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('Store');
             $merchant_id = $this->Session->read('merchantId');
            $criteria = "Store.merchant_id =$merchant_id AND Store.is_deleted=0";
            $searchData = $this->Store->find('list', array('fields' => array('Store.store_name', 'Store.store_name'), 'conditions' => array('OR' => array('Store.store_name LIKE' => '%' . $_GET['term'] . '%'), $criteria)));
            echo json_encode($searchData);
        } else {
            exit;
        }
    }

}
