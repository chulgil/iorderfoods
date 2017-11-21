<?php

App::uses('HqAppController', 'Controller');

class HqsettingsController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption');
    public $uses = array('SocialMedia', 'Store', 'StoreHoliday', 'StoreAvailability');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    /* ------------------------------------------------
      Function name:socialMedia()
      Description:social media configuratuion
      ----------------------------------------------------- */

    public function socialMedia() {
        $this->layout = "hq_dashboard";
        $this->loadModel('SocialMedia');
        $merchantId = $this->Session->read('merchantId');
        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $this->request->data['SocialMedia']['merchant_id'] = $merchantId;
            if ($this->SocialMedia->saveSocialMedia($this->request->data)) {
                $this->Session->setFlash(__("Social media url updated successfully."), 'alert_success');
                $this->redirect(array('controller' => 'hqsettings', 'action' => 'socialMedia'));
            } else {
                $this->Session->setFlash(__("Some Problem occured"), 'alert_failed');
                $this->redirect(array('controller' => 'hqsettings', 'action' => 'socialMedia'));
            }
        }
        $socialInfo = $this->SocialMedia->fetchSocialMediaDetail(null, $merchantId);
        if (!empty($socialInfo)) {
            $this->request->data = $socialInfo;
        }
    }

    /* ------------------------------------------------
      Function name:manageTimings()
      Description:Manage Store Open and close Timings
      created:27/7/2015
      ----------------------------------------------------- */

    public function manageTimings() {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $storeId = $this->Session->read('admin_store_id');
        $this->set('userid', $this->Session->read('Auth.Admin.id'));
        $this->set('roleid', $this->Session->read('Auth.Admin.role_id'));
        $this->set('storeId', $storeId);
        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $this->request->data['Store']['id'] = $storeId;
            if ($this->Store->saveStoreInfo($this->Common->trimValue($this->request->data['Store']))) {
                $this->Session->setFlash(__("Store Timings successfully Updated"), 'alert_success');
                $this->redirect(array('controller' => 'Stores', 'action' => 'manageTimings'));
            } else {
                $this->Session->setFlash(__("Some problem occurred"), 'alert_failed');
                $this->redirect(array('controller' => 'Stores', 'action' => 'manageTimings'));
            }
        }
        $storeInfo = $this->Store->fetchStoreDetail($storeId, $merchantId);
        $this->set('StoreBreak', $storeInfo['Store']['is_break_time']);
        $this->set('StoreBreak1', $storeInfo['Store']['is_break1']);
        $this->set('StoreBreak2', $storeInfo['Store']['is_break2']);
        $start = "00:00";
        $end = "24:00";
        $timeRange = $this->Common->getStoreTimeAdmin($start, $end);
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
      Function name:addClosedDate()
      Description:delete closed date from list
      created:29/7/2015
      ----------------------------------------------------- */

    public function addClosedDate() {
        $this->autoRender = false;
        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $data['store_id'] = $this->Session->read('admin_store_id');
            $holidayDate = $this->Dateform->formatDate($this->request->data['StoreHoliday']['holiday_date']);
            $data['holiday_date'] = trim($holidayDate);
            $data['description'] = trim($this->request->data['StoreHoliday']['description']);
            if ($this->StoreHoliday->storeHolidayNotExists($holidayDate)) {
                if ($this->StoreHoliday->saveStoreHolidayInfo($data)) {
                    $this->Session->setFlash(__("Closed Holiday Successfully added"), 'alert_success');
                    $this->redirect(array('controller' => 'Stores', 'action' => 'manageTimings'));
                } else {
                    $this->Session->setFlash(__("Some Problem occured"), 'alert_failed');
                    $this->redirect(array('controller' => 'Stores', 'action' => 'manageTimings'));
                }
            } else {
                $this->Session->setFlash(__("Closed Date already exists"), 'alert_failed');
                $this->redirect(array('controller' => 'Stores', 'action' => 'manageTimings'));
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
        $storeId = $this->Session->read('admin_store_id');
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
        $this->redirect(array('controller' => 'Stores', 'action' => 'manageTimings'));
    }

}
