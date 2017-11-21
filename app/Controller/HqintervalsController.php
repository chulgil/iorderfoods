<?php

App::uses('HqAppController', 'Controller');

class HqintervalsController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'DateformHelper', 'Common');
    public $uses = array('Store', 'Interval', 'IntervalDay', 'WeekDay', 'Item', 'ItemPrice', 'ItemType', 'Size', 'Category', 'IntervalDetail');
    public $layout = "hq_dashboard";

    public function beforeFilter() {
        parent::beforeFilter();
    }

    private function _saveTimeInterval($merchantId = null) {
        $this->request->data = $this->Common->trimValue($this->request->data);
        $intervalData = $this->request->data['Interval'];
        $storeID = $this->request->data['Interval']['store_id'];
        $intervalData['store_id'] = $storeID;
        $intervalData['merchant_id'] = $merchantId;
        $this->Interval->create();
        $flag = $this->Interval->saveInterval($intervalData);
        if ($flag) {
            $intervalId = $this->Interval->getLastInsertId();
            foreach ($this->request->data['IntervalDay'] as $key => $value) {
                $intervalDayData['store_id'] = $storeID;
                $intervalDayData['interval_id'] = $intervalId;
                $intervalDayData['week_day_id'] = $key;
                $intervalDayData['day_status'] = $value;
                $this->IntervalDay->create();
                $this->IntervalDay->saveIntervalDay($intervalDayData);
            }
            $this->Session->setFlash(__("Time-Interval Successfully Created"), 'alert_success');
        } else {
            $this->Session->setFlash(__("Time-Interval Not Created"), 'alert_failed');
        }
    }

    public function index() {
        $start = "00:30";
        $end = "24:00";
        $timeRange = $this->Common->getStoreTimeForHq($start, $end);
        $this->set('timeRange', $timeRange);
        $daysArray = $this->WeekDay->getWeekDaysList();
        $this->set('daysArray', $daysArray);
        if ($this->request->is('post') && !empty($this->request->data['Interval']['name'])) {
            $merchant_id = $this->Session->read('merchantId');
            if ($this->request->data['Interval']['store_id'] == 'All') {
                $storeData = $this->Store->getAllStoreByMerchantId($merchant_id);
                if (!empty($storeData)) {
                    foreach ($storeData as $store) {
                        $this->request->data['Interval']['store_id'] = $store['Store']['id'];
                        $this->_saveTimeInterval($merchant_id);
                    }
                }
            } else {
                $this->_saveTimeInterval($merchant_id);
            }
            $this->request->data = '';
        }
        $this->_timeIntervalList();
    }

    /* ------------------------------------------------
      Function name:index()
      Description:List Menu Items
      created:5/8/2015
      ----------------------------------------------------- */

    private function _timeIntervalList($clearAction = null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $storeID = @$this->request->data['Interval']['storeId'];
        $merchant_id = $this->Session->read('merchantId');
        $criteria = "Interval.merchant_id =$merchant_id AND Interval.is_deleted=0";
        if ($this->Session->read('hqIntervalSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('hqIntervalSearchData'), true);
        } else {
            $this->Session->delete('hqIntervalSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
       if (!empty($this->request->data)) {
            $this->Session->write('hqIntervalSearchData', json_encode($this->request->data));
            if ($this->request->data['Interval']['is_Active'] != '') {
                $active = trim($this->request->data['Interval']['is_Active']);
                $criteria .= " AND (Interval.is_active =$active)";
            }
            if (!empty($this->request->data['Interval']['search'])) {
                $search = trim($this->request->data['Interval']['search']);
                $criteria .= " AND (Interval.name LIKE '%" . $search . "%')";
            }
        }
        if (!empty($storeID)) {
            $criteria .= " AND Interval.store_id =$storeID";
        }
        $this->Interval->bindModel(
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
        $this->paginate = array('conditions' => array($criteria), 'order' => array('Interval.created' => 'DESC'));
        $intervalList = $this->paginate('Interval');
        $this->set('intervalList', $intervalList);
    }

    /* ------------------------------------------------
      Function name:editMenuItem()
      Description:Update Menu Interval
      created:5/8/2015
      ----------------------------------------------------- */

    public function editInterval($EncryptedIntervalID = null) {
        $this->layout = "hq_dashboard";
        $data['Interval']['id'] = $this->Encryption->decode($EncryptedIntervalID);
        $intervalDetail = $this->Interval->getIntervalDetail($data['Interval']['id']);

        $this->set('intervalDetail', $intervalDetail);

        $start = "00:30";
        $end = "24:00";
        $timeRange = $this->Common->getStoreTimeForHq($start, $end);
        $this->set('timeRange', $timeRange);

        $daysArray = $this->WeekDay->getWeekDaysList();
        $this->set('daysArray', $daysArray);

        if ($this->request->data) {
            $storeId = $intervalDetail['Interval']['store_id'];
            $this->request->data = $this->Common->trimValue($this->request->data);
            $data['Interval'] = $this->request->data['Interval'];
            $data['Interval']['store_id'] = $storeId;
            $flag = $this->Interval->saveInterval($data);
            if ($flag) {
                foreach ($this->request->data['IntervalDay'] as $key => $value) {
                    $intervalDayData['id'] = $key;
                    $intervalDayData['store_id'] = $storeId;
                    $intervalDayData['interval_id'] = $data['Interval']['id'];
                    $intervalDayData['day_status'] = $value;
                    $this->IntervalDay->saveIntervalDay($intervalDayData);
                    $intervalDayData = array();
                }
                $this->Session->setFlash(__("Time-Interval Successfully Updated"), 'alert_success');
                $this->redirect(array('controller' => 'hqintervals', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("Time-Interval is not updated"), 'alert_failed');
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $intervalDetail;
        }
    }

    /* ------------------------------------------------
      Function name:activateInterval()
      Description:Active/deactive Interval
      created:5/8/2015
      ----------------------------------------------------- */

    public function activateInterval($EncryptIntervalID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $data['Interval']['merchant_id'] = $this->Session->read('merchantId');
        $data['Interval']['id'] = $this->Encryption->decode($EncryptIntervalID);
        $data['Interval']['is_active'] = $status;
        if ($this->Interval->saveInterval($data)) {
            if ($status) {
                $SuccessMsg = "Interval Activated";
            } else {
                $SuccessMsg = "Interval Deactivated and Interval will not get Display";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
        }
        $this->redirect(array('controller' => 'hqintervals', 'action' => 'index'));
    }

    /* ------------------------------------------------
      Function name:deleteInterval()
      Description:Delete Interval
      created:9/2/2016
      ----------------------------------------------------- */

    public function deleteInterval($EncryptIntervalID = null) {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $data['Interval']['merchant_id'] = $this->Session->read('merchantId');
        $data['Interval']['id'] = $this->Encryption->decode($EncryptIntervalID);
        $data['Interval']['is_deleted'] = 1;
        if ($this->Interval->saveInterval($data)) {
            $this->Session->setFlash(__("Interval deleted"), 'alert_success');
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
        }
        $this->redirect(array('controller' => 'hqintervals', 'action' => 'index'));
    }

    /* ------------------------------------------------
      Function name:deleteMultipleInterval()
      Description:Delete multiple Interval
      created:09/2/2016
      ----------------------------------------------------- */

    public function deleteMultipleInterval() {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $data['Interval']['merchant_id'] = $this->Session->read('merchantId');
        $data['Interval']['is_deleted'] = 1;
        if (!empty($this->request->data['Interval']['id'])) {
            $filter_array = array_filter($this->request->data['Interval']['id']);
            $i = 0;
            foreach ($filter_array as $intervalId) {
                $data['Interval']['id'] = $intervalId;
                $this->Interval->saveInterval($data);
                $i++;
            }
            $del = $i . "  " . "Time-Intervals deleted successfully.";
            $this->Session->setFlash(__($del), 'alert_success');
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
        }
        $this->redirect(array('controller' => 'hqintervals', 'action' => 'index'));
    }
    
    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('Interval');
           if(!empty($_GET['storeID'])){
                $storeID = $_GET['storeID'];   
                 
            }else{
                    $merchant_id = $this->Session->read('merchantId');
                $storeID = $this->Store->getAllStoresByMerchantId($merchant_id);
            }
            $searchData = $this->Interval->find('list', array('fields' => array('Interval.name', 'Interval.name'), 'conditions' => array('OR' => array('Interval.name LIKE' => '%' . $_GET['term'] . '%'), 'Interval.is_deleted' => 0, 'Interval.store_id' => $storeID)));
            echo json_encode($searchData);
        } else {
            exit;
        }
    }

}
