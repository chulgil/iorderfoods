<?php

App::uses('StoreAppController', 'Controller');

class NewslettersController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'Dateform', 'Common');
    public $uses = array('Order', 'TimeZone');

    public function beforeFilter() {
        // echo Router::url( $this->here, true );die;
        parent::beforeFilter();
        $adminfunctions = array('index', 'newsletterList', 'activateNewsletter', 'deleteNewsletter', 'editNewsletter', 'customerList', 'specialDay', 'editSpecialDay', 'activateSpecialDay', 'deleteSpecialDay');
        if (in_array($this->params['action'], $adminfunctions)) {
            if (!$this->Common->checkPermissionByaction($this->params['controller'])) {
                $this->Session->setFlash(__("Permission Denied"));
                $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:index()
      Description:Add the newsletter in table
      created:21/8/2015
      ----------------------------------------------------- */

    public function index() {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $this->loadModel('Newsletter');
        $merchantId = $this->Session->read('admin_merchant_id');
        if ($this->request->is('post') && !empty($this->request->data['Newsletter']['name'])) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $newsletterdata['name'] = trim($this->request->data['Newsletter']['name']);
            $newsletterdata['content_key'] = trim($this->request->data['Newsletter']['content_key']);
            $isUniqueName = $this->Newsletter->checkNewsletterUniqueName($newsletterdata['name'], $storeID);
            $isUniqueCode = $this->Newsletter->checkNewsletterUniqueCode($newsletterdata['content_key'], $storeID);
            if ($isUniqueName) {
                if ($isUniqueCode) {
                    $newsletterdata = array();
                    $newsletterdata['name'] = trim($this->request->data['Newsletter']['name']);
                    $newsletterdata['content_key'] = trim($this->request->data['Newsletter']['content_key']);
                    $newsletterdata['content'] = trim($this->request->data['Newsletter']['content']);
                    $newsletterdata['is_active'] = trim($this->request->data['Newsletter']['is_active']);
                    $newsletterdata['store_id'] = $storeID;
                    $newsletterdata['merchant_id'] = $merchantId;
                    $this->Newsletter->create();
                    $this->Newsletter->saveNewsletter($this->Common->trimValue($newsletterdata));
                    $last_insert_id = $this->Newsletter->getLastInsertID();
                    if (!empty($last_insert_id)) {
//                        if ($newsletterdata['is_active'] == 1) {
//                            $this->Newsletter->updateAll(array('is_active' => 0), array('id !=' => $last_insert_id));
//                        }
                        $this->loadModel('TimeZone');
                        $this->loadModel("NewsletterManagement");
                        $this->loadModel('Store');
                        $sendtime = "";
                        $store = $this->Store->find('first', array('fields' => array('Store.time_zone_id', 'Store.id'), 'conditions' => array('Store.id' => $storeID, 'Store.is_active' => 1, 'Store.is_deleted' => 0)));
                        if (!empty($this->request->data['NewsletterManagement'])) {
                            $timezone = date_default_timezone_get(); //get server time zone
                            $dtz = new DateTimeZone($timezone);
                            $time = new DateTime('now', $dtz);
                            $diffInSeconds = $dtz->getOffset($time);
                            $timezoneId = $this->TimeZone->getTimezoneId($diffInSeconds); // get server time zone id

                            if (!empty($store['Store']['time_zone_id']) && !empty($this->request->data['NewsletterManagement']['timezone_send_time']) && $this->request->data['NewsletterManagement']['send_type'] == 1) { //Monthly
                                $dateTime = date('Y-m-') . $this->request->data['NewsletterManagement']['send_date'] . " " . $this->request->data['NewsletterManagement']['timezone_send_time'];  // create date time from post data
                                if (!empty($timezoneId)) {
                                    $sendtime = $this->Common->storeToServerTimeZone($timezoneId['TimeZone']['id'], $dateTime, false); // convert to store time zone
                                } else {
                                    $sendtime = $dateTime;
                                }
                                $newdate = strtotime($sendtime);
                                $crondate = date('d', $newdate);
                                $crontime = date('H:i:s', $newdate);
                                $datatoSave['send_date'] = $this->request->data['NewsletterManagement']['send_date'];
                                $datatoSave['send_time'] = $crontime;
                            } else if (isset($this->request->data['NewsletterManagement']['send_type']) && $this->request->data['NewsletterManagement']['send_type'] == 2) { //Weekly
                                $dateTime = date('Y-m-') . "02" . $this->request->data['NewsletterManagement']['timezone_send_time'];  // create date time from post data
                                $sendtime = $this->Common->storeToServerTimeZone($timezoneId['TimeZone']['id'], $dateTime, false);
                                $newdate = strtotime($sendtime);
                                $crondate = date('d', $newdate);
                                $crontime = date('H:i:s', $newdate);

                                if ($crondate < 2) {
                                    if ($this->request->data['NewsletterManagement']['send_day'] == 1) {
                                        $cronday = 7;
                                    } else {
                                        $cronday = intval($this->request->data['NewsletterManagement']['send_day'] - 1);
                                    }
                                } else {
                                    $cronday = $this->request->data['NewsletterManagement']['send_day'];
                                }
                                $datatoSave['send_day'] = $cronday;
                                $datatoSave['send_time'] = $crontime;
                            } else if (isset($this->request->data['NewsletterManagement']['send_type']) && $this->request->data['NewsletterManagement']['send_type'] == 3) { //Daily
                                $dateTime = $this->request->data['NewsletterManagement']['timezone_send_time']; //create datetime from post data
                                $sendtime = $this->Common->storeToServerTimeZone($timezoneId['TimeZone']['id'], $dateTime, true);

                                $newdate = strtotime($sendtime);
                                $crontime = date('H:i:s', $newdate);
                                $datatoSave['send_time'] = $crontime;
                            }
                            $datatoSave['store_id'] = $storeID;
                            $datatoSave['newsletter_id'] = $last_insert_id;
                            $datatoSave['timezone_send_time'] = $this->request->data['NewsletterManagement']['timezone_send_time'];
                            $datatoSave['send_type'] = $this->request->data['NewsletterManagement']['send_type'];
                            $this->NewsletterManagement->deleteAll(array('NewsletterManagement.store_id' => $storeID, 'NewsletterManagement.newsletter_id' => $last_insert_id), false);
                            $this->NewsletterManagement->save($datatoSave);
                        }
                    }
                    $this->Session->setFlash(__("Newsletter Successfully Created"), 'alert_success');
                    $this->redirect(array('controller' => 'newsletters', 'action' => 'index'));
                } else {
                    $this->Session->setFlash(__("Newsletter code Already exists"), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Newsletter name Already exists"), 'alert_failed');
            }
        }
        $start = "00:00";
        $end = "23:59";
        $timeRange = $this->Common->getStoreTimeAdmin($start, $end);
        $this->set('timeOptions', $timeRange);
        $this->_newsletterList();
    }

    /* ------------------------------------------------
      Function name:newsletterList()
      Description:Display the list of created newsletters
      created:21/8/2015
      ----------------------------------------------------- */

    private function _newsletterList($clearAction = null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $storeID = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $this->loadModel('Newsletter');
        $criteria = "Newsletter.store_id =$storeID AND Newsletter.is_deleted=0 AND Newsletter.merchant_id=$merchantId";
        if ($this->Session->read('NewsletterSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('NewsletterSearchData'), true);
        } else {
            $this->Session->delete('NewsletterSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('NewsletterSearchData', json_encode($this->request->data));
            if ($this->request->data['Newsletter']['isActive'] != '') {
                $active = trim($this->request->data['Newsletter']['isActive']);
                $criteria .= " AND (Newsletter.is_active` ='" . $active . "')";
            }
            if (!empty($this->request->data['Newsletter']['search'])) {
                $search = trim($this->request->data['Newsletter']['search']);
                $criteria .= " AND (Newsletter.name LIKE '%" . $search . "%')";
            }
        }
        $this->paginate = array('conditions' => array($criteria), 'order' => array('Newsletter.created' => 'DESC'));
        $newsletterDetail = $this->paginate('Newsletter');
        $this->set('list', $newsletterDetail);
    }

    /* ------------------------------------------------
      Function name:activateNewsletter()
      Description:Active/Deactive newsletter
      created:21/8/2015
      ----------------------------------------------------- */

    public function activateNewsletter($EncryptNewsletterID = null, $status = 0) {
        $this->layout = "admin_dashboard";
        $this->loadModel('Newsletter');
        $data['Newsletter']['store_id'] = $this->Session->read('admin_store_id');
        $data['Newsletter']['merchant_id'] = $this->Session->read('admin_merchant_id');
        ;
        $data['Newsletter']['id'] = $this->Encryption->decode($EncryptNewsletterID);
        $data['Newsletter']['is_active'] = $status;
        if ($this->Newsletter->saveNewsletter($data)) {
            if ($status) {
                $SuccessMsg = "Newsletter Activated Successfully";
            } else {
                $SuccessMsg = "Newsletter Deactivated Successfully";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'newsletters', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'newsletters', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteNewsletter()
      Description:Delete newsletter from list
      created:21/8/2015
      ----------------------------------------------------- */

    public function deleteNewsletter($EncryptNewsletterID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $this->loadModel('Newsletter');
        $data['Newsletter']['store_id'] = $this->Session->read('admin_store_id');
        $data['Newsletter']['id'] = $this->Encryption->decode($EncryptNewsletterID);
        $data['Newsletter']['is_deleted'] = 1;
        if ($this->Newsletter->saveNewsletter($data)) {
            $this->Session->setFlash(__("Newsletter deleted"), 'alert_success');
            $this->redirect(array('controller' => 'newsletters', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'newsletters', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:editNewsletter()
      Description:Edit Newsletter contents
      created:21/8/2015
      ----------------------------------------------------- */

    public function editNewsletter($EncryptNewsletterID = null) {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $data['Newsletter']['id'] = $this->Encryption->decode($EncryptNewsletterID);
        $this->loadModel('Newsletter');
        $this->Newsletter->bindModel(
                array(
            'hasOne' => array(
                'NewsletterManagement' => array(
                    'className' => 'NewsletterManagement',
                    'foreignKey' => 'newsletter_id'
                )
            )
                ), false
        );
        $newsletterDetail = $this->Newsletter->getNewsletterDetail($data['Newsletter']['id'], $storeID);
        if ($this->request->data && $this->request->is(array('post', 'put'))) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $newsletterTitle = trim($this->request->data['Newsletter']['name']);
            $newsletterCode = trim($this->request->data['Newsletter']['content_key']);
            $isUniqueName = $this->Newsletter->checkNewsletterUniqueName($newsletterTitle, $storeID, $data['Newsletter']['id']);
            $isUniqueCode = $this->Newsletter->checkNewsletterUniqueCode($newsletterCode, $storeID, $data['Newsletter']['id']);
            if ($isUniqueName) {
                if ($isUniqueCode) {
                    $newsletterdata = array();
                    $newsletterdata['name'] = trim($this->request->data['Newsletter']['name']);
                    $newsletterdata['content_key'] = trim($this->request->data['Newsletter']['content_key']);
                    $newsletterdata['id'] = trim($this->request->data['Newsletter']['id']);
                    $newsletterdata['content'] = trim($this->request->data['Newsletter']['content']);
                    $newsletterdata['is_active'] = trim($this->request->data['Newsletter']['is_active']);
                    $newsletterdata['store_id'] = $storeID;
                    $newsletterdata['merchant_id'] = $merchantId;
                    $this->loadModel('Newsletter');
                    if ($this->Newsletter->saveNewsletter($newsletterdata)) {
                        $this->loadModel('TimeZone');
                        $this->loadModel("NewsletterManagement");
                        $this->loadModel('Store');
                        $sendtime = "";
                        $store = $this->Store->find('first', array('fields' => array('Store.time_zone_id', 'Store.id'), 'conditions' => array('Store.id' => $storeID, 'Store.is_active' => 1, 'Store.is_deleted' => 0)));
                        if (!empty($this->request->data['NewsletterManagement'])) {
                            $timezone = date_default_timezone_get(); //get server time zone
                            $dtz = new DateTimeZone($timezone);
                            $time = new DateTime('now', $dtz);
                            $diffInSeconds = $dtz->getOffset($time);
                            $timezoneId = $this->TimeZone->getTimezoneId($diffInSeconds); // get server time zone id

                            if (!empty($store['Store']['time_zone_id']) && !empty($this->request->data['NewsletterManagement']['timezone_send_time']) && $this->request->data['NewsletterManagement']['send_type'] == 1) { //Monthly
                                $dateTime = date('Y-m-') . $this->request->data['NewsletterManagement']['send_date'] . " " . $this->request->data['NewsletterManagement']['timezone_send_time'];  // create date time from post data
                                if (!empty($timezoneId)) {
                                    $sendtime = $this->Common->storeToServerTimeZone($timezoneId['TimeZone']['id'], $dateTime, false); // convert to store time zone
                                } else {
                                    $sendtime = $dateTime;
                                }
                                $newdate = strtotime($sendtime);
                                $crondate = date('d', $newdate);
                                $crontime = date('H:i:s', $newdate);
                                $datatoSave['send_date'] = $this->request->data['NewsletterManagement']['send_date'];
                                $datatoSave['send_time'] = $crontime;
                            } else if (isset($this->request->data['NewsletterManagement']['send_type']) && $this->request->data['NewsletterManagement']['send_type'] == 2) { //Weekly
                                $dateTime = date('Y-m-') . "02" . $this->request->data['NewsletterManagement']['timezone_send_time'];  // create date time from post data
                                $sendtime = $this->Common->storeToServerTimeZone($timezoneId['TimeZone']['id'], $dateTime, false);
                                $newdate = strtotime($sendtime);
                                $crondate = date('d', $newdate);
                                $crontime = date('H:i:s', $newdate);

                                if ($crondate < 2) {
                                    if ($this->request->data['NewsletterManagement']['send_day'] == 1) {
                                        $cronday = 7;
                                    } else {
                                        $cronday = intval($this->request->data['NewsletterManagement']['send_day'] - 1);
                                    }
                                } else {
                                    $cronday = $this->request->data['NewsletterManagement']['send_day'];
                                }
                                $datatoSave['send_day'] = $cronday;
                                $datatoSave['send_time'] = $crontime;
                            } else if (isset($this->request->data['NewsletterManagement']['send_type']) && $this->request->data['NewsletterManagement']['send_type'] == 3) { //Daily
                                $dateTime = $this->request->data['NewsletterManagement']['timezone_send_time']; //create datetime from post data
                                $sendtime = $this->Common->storeToServerTimeZone($timezoneId['TimeZone']['id'], $dateTime, true);

                                $newdate = strtotime($sendtime);
                                $crontime = date('H:i:s', $newdate);
                                $datatoSave['send_time'] = $crontime;
                            }
                            $datatoSave['store_id'] = $storeID;
                            $datatoSave['newsletter_id'] = $this->request->data['Newsletter']['id'];
                            $datatoSave['timezone_send_time'] = $this->request->data['NewsletterManagement']['timezone_send_time'];
                            $datatoSave['send_type'] = $this->request->data['NewsletterManagement']['send_type'];
                            $this->NewsletterManagement->deleteAll(array('NewsletterManagement.store_id' => $storeID, 'NewsletterManagement.newsletter_id' => $this->request->data['Newsletter']['id']), false);
                            //prx($datatoSave);
                            $this->NewsletterManagement->save($datatoSave);
                        }
                    }
                    $this->Session->setFlash(__("Newsletter Successfully Updated."), 'alert_success');
                    $this->redirect(array('controller' => 'newsletters', 'action' => 'index'));
                } else {
                    $this->Session->setFlash(__("Newsletter Code Already exists"), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Newsletter Name Already exists"), 'alert_failed');
            }
        }
        $start = "00:00";
        $end = "23:59";
        $timeRange = $this->Common->getStoreTimeAdmin($start, $end);
        $this->set('timeOptions', $timeRange);
        $this->request->data = $newsletterDetail;
    }

    public function newsletterManagement($EncryptNewsletterID = null) {
        $this->layout = "admin_dashboard";
        $newsletterID = $this->Encryption->decode($EncryptNewsletterID);
        $start = "00:30";
        $end = "23:59";
        $timeRange = $this->Common->getStoreTimeAdmin($start, $end);
        $this->set('timeOptions', $timeRange);
        $storeId = $this->Session->read('admin_store_id');
        $this->set('storeId', $storeId);
        $this->loadModel('TimeZone');
        $this->loadModel("NewsletterManagement");
        $this->loadModel('Store');
        $sendtime = "";
        $store = $this->Store->find('first', array('fields' => array('Store.time_zone_id', 'Store.id'), 'conditions' => array('Store.id' => $storeId, 'Store.is_active' => 1, 'Store.is_deleted' => 0)));

        $datatoSave = array();
        if (!empty($this->request->data)) {
            $timezone = date_default_timezone_get(); //get server time zone
            $dtz = new DateTimeZone($timezone);
            $time = new DateTime('now', $dtz);
            $diffInSeconds = $dtz->getOffset($time);
            $timezoneId = $this->TimeZone->getTimezoneId($diffInSeconds); // get server time zone id

            if (!empty($store['Store']['time_zone_id']) && !empty($this->request->data['NewsletterManagement']['timezone_send_time']) && $this->request->data['NewsletterManagement']['send_type'] == 1) { //Monthly
                $dateTime = date('Y-m-') . $this->request->data['NewsletterManagement']['send_date'] . " " . $this->request->data['NewsletterManagement']['timezone_send_time'];  // create date time from post data
                if (!empty($timezoneId)) {
                    $sendtime = $this->Common->storeToServerTimeZone($timezoneId['TimeZone']['id'], $dateTime, false); // convert to store time zone
                } else {
                    $sendtime = $dateTime;
                }
                $newdate = strtotime($sendtime);
                $crondate = date('d', $newdate);
                $crontime = date('H:i:s', $newdate);
                $datatoSave['send_date'] = $this->request->data['NewsletterManagement']['send_date'];
                $datatoSave['send_time'] = $crontime;
            } else if (isset($this->request->data['NewsletterManagement']['send_type']) && $this->request->data['NewsletterManagement']['send_type'] == 2) { //Weekly
                $dateTime = date('Y-m-') . "02" . $this->request->data['NewsletterManagement']['timezone_send_time'];  // create date time from post data
                $sendtime = $this->Common->storeToServerTimeZone($timezoneId['TimeZone']['id'], $dateTime, false);
                $newdate = strtotime($sendtime);
                $crondate = date('d', $newdate);
                $crontime = date('H:i:s', $newdate);

                if ($crondate < 2) {
                    if ($this->request->data['NewsletterManagement']['send_day'] == 1) {
                        $cronday = 7;
                    } else {
                        $cronday = intval($this->request->data['NewsletterManagement']['send_day'] - 1);
                    }
                } else {
                    $cronday = $this->request->data['NewsletterManagement']['send_day'];
                }
                $datatoSave['send_day'] = $cronday;
                $datatoSave['send_time'] = $crontime;
            } else if (isset($this->request->data['NewsletterManagement']['send_type']) && $this->request->data['NewsletterManagement']['send_type'] == 3) { //Daily
                $dateTime = $this->request->data['NewsletterManagement']['timezone_send_time']; //create datetime from post data
                $sendtime = $this->Common->storeToServerTimeZone($timezoneId['TimeZone']['id'], $dateTime, true);

                $newdate = strtotime($sendtime);
                $crontime = date('H:i:s', $newdate);
                $datatoSave['send_time'] = $crontime;
            }
            $datatoSave['store_id'] = $storeId;
            $datatoSave['newsletter_id'] = $newsletterID;
            $datatoSave['timezone_send_time'] = $this->request->data['NewsletterManagement']['timezone_send_time'];
            $datatoSave['send_type'] = $this->request->data['NewsletterManagement']['send_type'];
            $this->NewsletterManagement->deleteAll(array('NewsletterManagement.store_id' => $storeId, 'NewsletterManagement.newsletter_id' => $newsletterID), false);
            $this->NewsletterManagement->save($datatoSave);
            $this->Session->setFlash(__("Newsletter setting successfully saved."), 'alert_success');
            $this->redirect('/newsletters/index');
        }
        $newsDetail = array();
        //If No entry of Newsletter is present in the table
        $newsDetail = $this->NewsletterManagement->find("first", array("conditions" => array("store_id" => $storeId, "newsletter_id" => $newsletterID)));
        $this->request->data = $newsDetail;
    }

    /* ------------------------------------------------
      Function name: customerList()
      Description: Display the list of customers
      created: 18/07/2016
      ----------------------------------------------------- */

    public function customerList($clearAction = null) {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchantID = $this->Session->read('admin_merchant_id');
        $value = "";
        $storeIds = array();
        $storeIds[0] = 0;
        $storeIds[1] = $storeID;
        //$criteria = "User.store_id =$storeID AND User.role_id=4 AND User.is_deleted=0";
        //$criteria = "User.merchant_id =$merchantID AND User.store_id IN (0,$storeID) AND User.role_id IN (4,5) AND User.is_deleted=0 AND User.is_active=1";
        $criteria = "User.merchant_id =$merchantID AND User.store_id =$storeID AND User.role_id IN (4,5) AND User.is_deleted=0";
        $this->loadModel('User');
        //if(isset($this->params['named']['sort']) || isset($this->params['named']['page'])){
        if ($this->Session->read('CustomerSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('CustomerSearchData'), true);
        } else {
            $this->Session->delete('CustomerSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }

        if (!empty($this->request->data)) {
//            pr($this->request->data);
            $this->Session->write('CustomerSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['User']['keyword'])) {
                $value = trim($this->request->data['User']['keyword']);
                $explode = explode(' ', $value);
                //$criteria .= " AND (User.fname LIKE '%" . $kvalue . "%' OR User.lname LIKE '%" . $kvalue . "%' OR User.phone LIKE '%" . $kvalue . "%' OR User.email LIKE '%" . $kvalue . "%' )";
                if (!empty($explode[0]) && !empty($explode[1])) {
                    $criteria .= " AND (User.fname LIKE '%" . @$explode[0] . "%' AND User.lname LIKE '%" . @$explode[1] . "%')";
                } elseif (empty($explode[1])) {
                    $criteria .= " AND (User.fname LIKE '%" . @$explode[0] . "%' OR User.lname LIKE '%" . @$explode[0] . "%')";
                }
            }

            if (isset($this->request->data['User']['is_active']) && $this->request->data['User']['is_active'] != '') {
                $active = trim($this->request->data['User']['is_active']);
                $criteria .= " AND (User.is_active =$active)";
            }

            if ($this->request->data['User']['from'] != '' && $this->request->data['User']['to'] != '') {
                $stratdate = $this->Dateform->formatDate($this->request->data['User']['from']);
                $enddate = $this->Dateform->formatDate($this->request->data['User']['to']);
                // $criteria .= " AND (User.created BETWEEN ? AND ?) =" array($stratdate,$enddate);
                //$criteria.= " AND (User.created BETWEEN '".$stratdate."' AND '".$enddate."')";
                $criteria.= " AND (Date(User.created) >= '" . $stratdate . "' AND Date(User.created) <='" . $enddate . "')";
                //$criteria.= " AND WEEK(Order.created) >=WEEK('".$startDate."') AND WEEK(Order.created) <=WEEK('".$endDate."')";
            }
            if (!empty($this->request->data['User']['dateOfBirth'])) {
                $criteria .= ' AND MONTH(User.dateOfBirth) =' . $this->request->data['User']['dateOfBirth'];
            }
            $this->User->bindModel(
                    array('belongsTo' => array(
                            'State' => array(
                                'className' => 'State',
                                'foreignKey' => 'state_id',
                                'fields' => array('id', 'name')),
                            'City' => array(
                                'className' => 'City',
                                'foreignKey' => 'city_id',
                                'fields' => array('id', 'name')),
                            'Zip' => array(
                                'className' => 'Zip',
                                'foreignKey' => 'zip_id',
                                'fields' => array('id', 'zipcode')),
            )));
            if (!empty($this->request->data['User']['state_id'])) {

                $criteria .= " AND (User.state LIKE '%" . $this->request->data['User']['state_id'] . "%' OR State.name LIKE '%" . $this->request->data['User']['state_id'] . "%')";

                //$criteria .= " AND (User.state LIKE %'" . $this->request->data['User']['state_id'] . "%' OR State.name LIKE %'" . $this->request->data['User']['state_id'] . "%')";
            }
            if (!empty($this->request->data['User']['city'])) {

                $criteria .= " AND (User.city LIKE '%" . $this->request->data['User']['city'] . "%')";

                //$criteria .= " AND (User.city LIKE %'" . $this->request->data['User']['city_id'] . "%' OR City.name LIKE %'" . $this->request->data['User']['city_id'] . "%')";
            }
            if (!empty($this->request->data['User']['zip'])) {

                $criteria .= " AND (User.zip LIKE '%" . $this->request->data['User']['zip'] . "%')";

                //$criteria .= " AND (User.zip LIKE %'" . $this->request->data['User']['zip_id'] . "%' OR Zip.zipcode LIKE %'" . $this->request->data['User']['zip_id'] . "%')";
            }
        }

        $this->paginate = array('conditions' => array($criteria), 'order' => array('User.created' => 'DESC'), 'recursive' => 2);
        $customerdetail = $this->paginate('User');
        $this->set('list', $customerdetail);
        $this->set('keyword', $value);
//        pr($customerdetail);
//        die;
//        $this->loadModel('State');        
//        $states=$this->Common->getCustomerbyStateId($merchantID,$storeIds);
//        $this->set('states', $states);
//        if (!empty($this->request->data['User']['state_id'])) {
//            $stateId=$this->request->data['User']['state_id'];
//            $city=$this->Common->getCustomerbyCityId($merchantID,$storeIds,$stateId);
//            $this->set('cities', $city);
//        }
//        if (!empty($this->request->data['User']['state_id']) && !empty($this->request->data['User']['city_id'])) {
//            $stateId=$this->request->data['User']['state_id'];
//            $cityId=$this->request->data['User']['city_id'];
//            $zip=$this->Common->getCustomerbyZipId($merchantID,$storeIds,$stateId,$cityId);
//            $this->set('zips', $zip);
//            
//        }
    }

    /* Ajax function to get promotions, coupons, newsletters, offers names */

    function getPromotionValue() {
        $this->autoRender = false;
        $storeID = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $html = '';
        if (isset($this->request->data['promotionTypeId']) && !empty($this->request->data['promotionTypeId'])) {
            $promotionTypeId = $this->request->data['promotionTypeId'];
            switch ($promotionTypeId) {
                case "1": //Coupons
                    $options['conditions'] = array('Coupon.store_id' => $storeID, 'Coupon.is_deleted' => 0, 'Coupon.is_active' => 1);
                    $options['fields'] = array('Coupon.id', 'Coupon.name');
                    $options['order'] = array('Coupon.created' => 'DESC');
                    $this->loadModel('Coupon');
                    $getData = $this->Coupon->find('list', $options);
                    break;
                case "2": //Promotions
                    $options['conditions'] = array('Offer.store_id' => $storeID, 'Offer.is_deleted' => 0, 'Offer.is_active' => 1);
                    $options['fields'] = array('Offer.id', 'Offer.description');
                    $options['order'] = array('Offer.created' => 'DESC');
                    $this->loadModel('Offer');
                    $getData = $this->Offer->find('list', $options);
                    break;
                case "3": //Newsletters
                    $options['conditions'] = array('Newsletter.store_id' => $storeID, 'Newsletter.merchant_id' => $merchantId, 'Newsletter.is_deleted' => 0, 'Newsletter.is_active' => 1);
                    $options['fields'] = array('Newsletter.id', 'Newsletter.name');
                    $options['order'] = array('Newsletter.created' => 'DESC');
                    $this->loadModel('Newsletter');
                    $getData = $this->Newsletter->find('list', $options);
                    break;
                case "4": //Offers
                    $options['conditions'] = array('ItemOffer.store_id' => $storeID, 'ItemOffer.is_deleted' => 0, 'ItemOffer.is_active' => 1);
                    $options['fields'] = array('ItemOffer.id', 'Item.name');
                    $options['order'] = array('ItemOffer.created' => 'DESC');
                    $this->loadModel('ItemOffer');
                    $this->ItemOffer->bindModel(
                            array(
                        'belongsTo' => array(
                            'Item' => array(
                                'className' => 'Item',
                                'foreignKey' => 'item_id',
                                'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                                'fields' => array('id', 'name')
                            )
                        )
                            ), false
                    );
                    $itemOffer = $this->ItemOffer->find('all', $options);
                    foreach ($itemOffer as $val) {
                        $getData[$val['ItemOffer']['id']] = $val['Item']['name'];
                    }
                    break;
            }
        }
        if (isset($getData) && !empty($getData)) {
            foreach ($getData as $key => $val) {
                $html .= "<option value=" . $key . ">" . $val . "</option>";
            }
        }
        return $html;
        die;
    }

    /* Share promotions, coupons, newsletters, offers with users */

    function share() {
        $this->autoRender = false;
        $storeID = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $this->loadModel('Store');
        $this->loadModel('User');
        $storeEmail = $this->Store->fetchStoreDetail($storeID);
        $userData = array();
        $options['conditions'][] = array('User.store_id' => $storeID);
        $options['conditions'][] = array('User.role_id' => 4);
        $options['conditions'][] = array('User.is_deleted' => 0);
        $options['fields'] = array('User.id', 'User.email', 'User.fname', 'User.lname', 'User.is_newsletter', 'User.store_id');
        $options['recursive'] = -1;
        if (isset($this->request->data) && !empty($this->request->data)) {
            $promotionTypeId = $this->request->data['Customer']['selectPromotionType'];
            $promotionValue = $this->request->data['Customer']['selectPromotionValue'];
            if (isset($this->request->data['selectedUser']) && !empty($this->request->data['checkboxes'])) {
                $checkedList = $this->request->data['checkboxes'];
                for ($i = 0; $i < count($checkedList); $i++) {
                    $userId[$i] = base64_decode($checkedList[$i]);
                }
                $options['conditions'][] = array('User.id' => $userId);
                $userData = $this->User->find('all', $options);
            } elseif (isset($this->request->data['allUser'])) {
                if (!empty($this->request->data['checkboxes'])) {
                    $checkedList = $this->request->data['checkboxes'];
                    for ($i = 0; $i < count($checkedList); $i++) {
                        $userId[$i] = base64_decode($checkedList[$i]);
                    }
                    $options['conditions'][] = array('User.id' => $userId);
                }
                $userData = $this->User->find('all', $options);
            } elseif (isset($this->request->data['allActiveUser'])) {
                if (!empty($this->request->data['checkboxes'])) {
                    $checkedList = $this->request->data['checkboxes'];
                    for ($i = 0; $i < count($checkedList); $i++) {
                        $userId[$i] = base64_decode($checkedList[$i]);
                    }
                    $options['conditions'][] = array('User.id' => $userId);
                }
                $options['conditions'][] = array('User.is_active' => 1);
                $userData = $this->User->find('all', $options);
            }

            if (!empty($userData)) {
                $c = 0;
                switch ($promotionTypeId) {
                    case "1": //Coupons
                        $this->loadModel('EmailTemplate');
                        $template_type = 'coupon_offer';
                        $emailSuccess = $this->EmailTemplate->storeTemplates($storeID, $merchantId, $template_type);
                        $this->loadModel('Coupon');
                        $couponDetail = $this->Coupon->getCouponDetail($promotionValue, $storeID);
                        if ($emailSuccess) {
                            foreach ($userData as $usr) {
                                if ($usr['User']['is_newsletter'] == 1) {
                                    $emailData = $emailSuccess['EmailTemplate']['template_message'];
                                    $subject = $emailSuccess['EmailTemplate']['template_subject'];
                                    $couponcode = $couponDetail['Coupon']['coupon_code'];
                                    $fullName = $usr['User']['fname'] . ' ' . $usr['User']['lname'];
                                    $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                                    $emailData = str_replace('{STORE_PHONE}', $storeEmail['Store']['phone'], $emailData);
                                    $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);
                                    $storeAddress = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
                                    $url = "http://" . $storeEmail['Store']['store_url'];
                                    $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
                                    $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                                    $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                                    $emailData = str_replace('{COUPON}', $couponcode, $emailData);
                                    $subject = ucwords(str_replace('_', ' ', $subject));
                                    $this->sendEmail($usr['User']['email'], $subject, $emailData, $storeEmail['Store']['email_id']);
                                    $c++;
                                }
                            }
                        }
                        break;
                    case "2": //Promotions
                        $this->loadModel('EmailTemplate');
                        $template_type = 'promotion';
                        $emailSuccess = $this->EmailTemplate->storeTemplates($storeID, $merchantId, $template_type);
                        if ($emailSuccess) {
                            foreach ($userData as $usr) {
                                if ($usr['User']['is_newsletter'] == 1) {
                                    $emailData = $emailSuccess['EmailTemplate']['template_message'];
                                    $subject = $emailSuccess['EmailTemplate']['template_subject'];
                                    $fullName = $usr['User']['fname'] . ' ' . $usr['User']['lname'];
                                    $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                                    $emailData = str_replace('{STORE_PHONE}', $storeEmail['Store']['phone'], $emailData);
                                    $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);
                                    $storeAddress = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
                                    $url = "http://" . $storeEmail['Store']['store_url'];
                                    $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
                                    $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                                    $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                                    $subject = ucwords(str_replace('_', ' ', $subject));
                                    $this->sendEmail($usr['User']['email'], $subject, $emailData, $storeEmail['Store']['email_id']);
                                    $c++;
                                }
                            }
                        }
                        break;
                    case "3": //Newsletters
                        $this->loadModel('Newsletter');
                        $newsLetter = $this->Newsletter->find('first', array('conditions' => array('Newsletter.id' => $promotionValue)));
                        foreach ($userData as $usr) {
                            if ($usr['User']['is_newsletter'] == 1) {
                                $emailData = $newsLetter['Newsletter']['content'];
                                $subject = $newsLetter['Newsletter']['name'];
                                $fullName = $usr['User']['fname'] . ' ' . $usr['User']['lname'];
                                $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                                $emailData = str_replace('{STORE_PHONE}', $storeEmail['Store']['phone'], $emailData);
                                $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);
                                $storeAddress = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
                                $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                                $subject = ucwords(str_replace('_', ' ', $subject));
                                $this->sendEmail($usr['User']['email'], $subject, $emailData, $storeEmail['Store']['email_id']);
                                $c++;
                            }
                        }
                        break;
                    case "4": //Offers
                        $this->loadModel('EmailTemplate');
                        $template_type = 'item_offer';
                        $emailSuccess = $this->EmailTemplate->storeTemplates($storeID, $merchantId, $template_type);
                        if ($emailSuccess) {
                            foreach ($userData as $usr) {
                                if ($usr['User']['is_newsletter'] == 1) {
                                    $emailData = $emailSuccess['EmailTemplate']['template_message'];
                                    $subject = $emailSuccess['EmailTemplate']['template_subject'];
                                    $fullName = $usr['User']['fname'] . ' ' . $usr['User']['lname'];
                                    $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                                    $emailData = str_replace('{STORE_PHONE}', $storeEmail['Store']['phone'], $emailData);
                                    $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);
                                    $storeAddress = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
                                    $url = "http://" . $storeEmail['Store']['store_url'];
                                    $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
                                    $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                                    $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                                    $subject = ucwords(str_replace('_', ' ', $subject));
                                    $this->sendEmail($usr['User']['email'], $subject, $emailData, $storeEmail['Store']['email_id']);
                                    $c++;
                                }
                            }
                        }
                        break;
                }
                if ($c > 0) {
                    $this->Session->setFlash(__("Newsletter sent successfully."), 'alert_success');
                }
                $this->redirect('/newsletters/customerList');
            }
        }
    }

    /*
     * This function is used to send email with template
     * @author        smartData
     * @copyright     smartData Enterprise Inc.
     * @method        sendEmail
     * @param         $to, $subject, $template
     */

    public function sendEmail($to = null, $subject = null, $emailData = null, $from = null) {
//        echo $to.'<br>';
//        echo $subject.'<br>';
//        echo $emailData.'<br>';
//        echo $from.'<br>-------------------<br>';
        $this->Email->to = $to;
        $this->Email->subject = $subject;
        $this->Email->from = $from;
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
            $this->Email->send();
        } catch (Exception $e) {
            
        }
        return;
    }

    public function getUserDetails() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('User');
            $storeID = $this->Session->read('admin_store_id');
            $merchantID = $this->Session->read('admin_merchant_id');
            $storeids = array();
            $storeids[0] = 0;
            $storeids[1] = $storeID;
            $searchData = $this->User->find('all', array('fields' => array('User.userName', 'User.email', 'User.phone'), 'conditions' => array('OR' => array('User.userName LIKE' => '%' . $_GET['term'] . '%', 'User.email LIKE' => '%' . $_GET['term'] . '%', 'User.phone LIKE' => '%' . $_GET['term'] . '%'), 'User.is_deleted' => 0, 'User.merchant_id' => $merchantID, 'User.store_id' => $storeids, 'User.role_id' => array(4, 5))));
            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    $new_array[] = array('label' => $val['User']['userName'], 'value' => $val['User']['userName'], 'desc' => $val['User']['userName'] . '-' . $val['User']['email'] . '-' . $val['User']['phone']);
                };
            }
            echo json_encode($new_array);
        } else {
            exit;
        }
    }

    /* ----------------------- Special Day's Functionality Start-------------------------------------- */

    /* ------------------------------------------------
      Function name:special_day()
      Description:Display the list of Anniversary and Birthday newsletters
      created:21/8/2015
      ----------------------------------------------------- */

    public function special_day() {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $this->loadModel('SpecialDay');
        $this->loadModel('DefaultSpecialDay');
        $criteria = "SpecialDay.store_id =$storeID AND SpecialDay.is_deleted=0 AND SpecialDay.merchant_id=$merchantId";
        if ($this->Session->read('SpecialDaySearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('SpecialDaySearchData'), true);
        } else {
            $this->Session->delete('SpecialDaySearchData');
        }
        if (!empty($this->request->data)) {
            $this->Session->write('SpecialDaySearchData', json_encode($this->request->data));
            if ($this->request->data['SpecialDay']['isActive'] != '') {
                $active = trim($this->request->data['SpecialDay']['isActive']);
                $criteria .= " AND (SpecialDay.is_active` ='" . $active . "')";
            }
        }
        $this->SpecialDay->bindModel(
                array(
            'belongsTo' => array(
                'DefaultSpecialDay' => array(
                    'className' => 'DefaultSpecialDay',
                    'foreignKey' => 'default_special_day_id',
                    'conditions' => array('DefaultSpecialDay.is_deleted' => 0, 'DefaultSpecialDay.is_active' => 1),
                    'fields' => array('id', 'name')
                )
            )
                ), false
        );
        $this->paginate = array('recursive' => 2, 'conditions' => array($criteria), 'fields' => array('id', 'default_special_day_id', 'is_active'), 'order' => array('SpecialDay.created' => 'DESC'));
        $specialDetail = $this->paginate('SpecialDay');
        $this->set('list', $specialDetail);
    }

    /* ------------------------------------------------
      Function name:activateNewsletter()
      Description:Active/Deactive newsletter
      created:21/8/2015
      ----------------------------------------------------- */

    public function activateSpecialDay($EncryptSpecialDayID = null, $status = 0) {
        $this->layout = "admin_dashboard";
        $this->loadModel('SpecialDay');
        $data['SpecialDay']['store_id'] = $this->Session->read('admin_store_id');
        $data['SpecialDay']['id'] = $this->Encryption->decode($EncryptSpecialDayID);
        $data['SpecialDay']['is_active'] = $status;
        if ($this->SpecialDay->save($data)) {
            if ($status) {
                $SuccessMsg = "SpecialDay Activated Successfully";
            } else {
                $SuccessMsg = "SpecialDay Deactivated Successfully";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'newsletters', 'action' => 'special_day'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'newsletters', 'action' => 'special_day'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteNewsletter()
      Description:Delete newsletter from list
      created:21/8/2015
      ----------------------------------------------------- */

    public function deleteSpecialDay($EncryptSpecialDayID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $this->loadModel('SpecialDay');
        $data['SpecialDay']['store_id'] = $this->Session->read('admin_store_id');
        $data['SpecialDay']['id'] = $this->Encryption->decode($EncryptSpecialDayID);
        $data['SpecialDay']['is_deleted'] = 1;
        if ($this->SpecialDay->save($data)) {
            $this->Session->setFlash(__("SpecialDay deleted"), 'alert_success');
            $this->redirect(array('controller' => 'newsletters', 'action' => 'special_day'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'newsletters', 'action' => 'special_day'));
        }
    }

    /* ------------------------------------------------
      Function name:editNewsletter()
      Description:Edit Newsletter contents
      created:21/8/2015
      ----------------------------------------------------- */

    public function editSpecialDay($EncryptNewsletterID = null) {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $data['SpecialDay']['id'] = $this->Encryption->decode($EncryptNewsletterID);
        $this->loadModel('SpecialDay');
        $this->loadModel('DefaultSpecialDay');
        $this->SpecialDay->bindModel(
                array(
            'belongsTo' => array(
                'DefaultSpecialDay' => array(
                    'className' => 'DefaultSpecialDay',
                    'foreignKey' => 'default_special_day_id',
                    'conditions' => array('DefaultSpecialDay.is_deleted' => 0, 'DefaultSpecialDay.is_active' => 1),
                    'fields' => array('id', 'name')
                )
            )
                ), false
        );
        $specialDayDetail = $this->SpecialDay->getSpecialDayDetail($data['SpecialDay']['id'], $storeId);
        if ($this->request->data) {
            $specialDaydata = array();
            $specialDaydata['id'] = trim($this->request->data['SpecialDay']['id']);
            $specialDaydata['template_message'] = trim($this->request->data['SpecialDay']['template_message']);
            $specialDaydata['is_active'] = trim($this->request->data['SpecialDay']['is_active']);
            $specialDaydata['store_id'] = $storeId;
            $specialDaydata['merchant_id'] = $merchantId;
            $this->loadModel('SpecialDay');
            $this->SpecialDay->saveSpecialDay($specialDaydata);
            $this->Session->setFlash(__("SpecialDay Successfully Updated."), 'alert_success');
            $this->redirect(array('controller' => 'newsletters', 'action' => 'special_day'));
        }
        $this->request->data = $specialDayDetail;
    }

    public function specialDayManagement($EncryptSpecialID = null) {
        $this->layout = "admin_dashboard";
        $specialDayID = $this->Encryption->decode($EncryptSpecialID);
        $storeId = $this->Session->read('admin_store_id');
        $this->set('storeId', $storeId);
        $this->loadModel("SpecialDay");
        if (!empty($this->request->data)) {
            $datatoSave['id'] = $this->request->data['SpecialDay']['id'];
            $datatoSave['special_day_time_id'] = $this->request->data['SpecialDay']['cron_time'];
            $this->SpecialDay->save($datatoSave);
            $this->Session->setFlash(__("SpecialDay setting successfully saved."), 'alert_success');
            $this->redirect('/newsletters/special_day');
        }
        $specialDayDetail = array();
        //If No entry of Newsletter is present in the table
        $specialDayDetail = $this->SpecialDay->find("first", array("conditions" => array("SpecialDay.store_id" => $storeId, "SpecialDay.id" => $specialDayID), 'fields' => array('id', 'store_id', 'merchant_id', 'special_day_time_id')));
        $this->request->data = $specialDayDetail;
        $this->loadModel('SpecialDayTime');
        $specialDayTimes = $this->SpecialDayTime->find('list', array('conditions' => array('SpecialDayTime.is_active' => 1, 'SpecialDayTime.is_deleted' => 0), 'fields' => array('id', 'special_day_time')));
        $this->set('specialDayTimes', $specialDayTimes);
//        pr($specialDayTimes);
//        die;
    }

    /* ----------------------- Special Day's Functionality End-------------------------------------- */

    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $storeID = $this->Session->read('admin_store_id');
            $merchantId = $this->Session->read('admin_merchant_id');
            $this->loadModel('Newsletter');
            $criteria = "Newsletter.store_id =$storeID AND Newsletter.is_deleted=0 AND Newsletter.merchant_id=$merchantId";
            $searchData = $this->Newsletter->find('list', array('fields' => array('Newsletter.name', 'Newsletter.name'), 'conditions' => array('OR' => array('Newsletter.name LIKE' => '%' . $_GET['term'] . '%'), 'Newsletter.is_deleted' => 0, $criteria)));
            echo json_encode($searchData);
        } else {
            exit;
        }
    }

    /* ----------------------- Get state details of users-------------------------------------- */

    public function getStateDetails() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $storeID = $this->Session->read('admin_store_id');
            $merchantId = $this->Session->read('admin_merchant_id');
            $this->loadModel('State');
            $this->loadModel('User');
            $this->User->bindModel(
                    array('belongsTo' => array(
                            'State' => array(
                                'className' => 'State',
                                'foreignKey' => 'state_id',
                                'fields' => array('id', 'name'))
            )));
            $criteria = "User.store_id =$storeID AND User.is_deleted=0 AND User.merchant_id=$merchantId";
            $searchData = $this->User->find('all', array('conditions' => array('OR' => array('User.state LIKE' => '%' . $_GET['term'] . '%', 'State.name LIKE' => '%' . $_GET['term'] . '%'), $criteria)));
            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    if (!empty($val['User']['state'])) {
                        $new_array[] = array('label' => $val['User']['state'], 'value' => $val['User']['state']);
                    } else {
                        $new_array[] = array('label' => $val['State']['name'], 'value' => $val['State']['name']);
                    }
                };
            }
            $result = array_map('unserialize', array_unique(array_map('serialize', $new_array)));
            echo json_encode($result);
        } else {
            exit;
        }
    }

    public function citySearch() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $storeID = $this->Session->read('admin_store_id');
            $merchantId = $this->Session->read('admin_merchant_id');
            $city = $_GET['term'];
            $this->loadModel('User');
            $cityList = $this->User->find('list', array('fields' => array('User.city', 'User.city'), 'conditions' => array('User.city LIKE' => '%' . $city . '%', 'User.store_id' => $storeID, 'User.merchant_id' => $merchantId, 'User.is_deleted' => 0, 'User.is_active' => 1), 'group' => array('User.city')));
            echo json_encode($cityList);
        } else {
            exit;
        }
    }

    public function zipSearch() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $storeID = $this->Session->read('admin_store_id');
            $merchantId = $this->Session->read('admin_merchant_id');
            $city = $_GET['term'];
            $this->loadModel('User');
            $zipList = $this->User->find('list', array('fields' => array('User.zip', 'User.zip'), 'conditions' => array('User.role_id' => array(4, 5), 'User.zip LIKE' => '%' . $city . '%', 'User.store_id' => $storeID, 'User.merchant_id' => $merchantId, 'User.is_deleted' => 0, 'User.is_active' => 1), 'group' => array('User.zip')));
            echo json_encode($zipList);
        } else {
            exit;
        }
    }

    /* ------------------------------------------------
      Function name:deleteMultipleNewsletters()
      Description:Delete multiple Newsletters
      created:02/08/2017
      ----------------------------------------------------- */

    public function deleteMultipleNewsletters() {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        if ($this->request->is(array('post'))) {
            $data['Newsletter']['store_id'] = $this->Session->read('admin_store_id');
            $data['Newsletter']['is_deleted'] = 1;
            if (!empty($this->request->data['Newsletter']['id'])) {
                $filter_array = array_filter($this->request->data['Newsletter']['id']);
                $i = 0;
                foreach ($filter_array as $orderId) {
                    $data['Newsletter']['id'] = $orderId;
                    $this->loadModel('Newsletter');
                    $this->Newsletter->saveNewsletter($data);
                    $i++;
                }
                $del = $i . "  " . "newsletter deleted successfully.";
                $this->Session->setFlash(__($del), 'alert_success');
                $this->redirect($this->referer());
            }
        }
    }

}
