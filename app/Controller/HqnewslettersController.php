<?php

App::uses('HqAppController', 'Controller');

class HqnewslettersController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common', 'HqCommon');
    public $helper = array('Encryption', 'Dateform', 'Common');
    public $uses = array('MerchantNewsletter', 'Order', 'TimeZone', 'Newsletter', 'Store');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('newsletter');
    }

    public function addNewsletter() {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        if ($this->request->is('post') && !empty($this->request->data['Newsletter']['name'])) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            if ($this->request->data['Newsletter']['type'] == 2) {
                unset($this->request->data['NewsletterManagement']);
            }
            $newsletterdata['name'] = trim($this->request->data['Newsletter']['name']);
            $newsletterdata['content_key'] = trim($this->request->data['Newsletter']['content_key']);
            $isUniqueName = $this->Newsletter->checkNewsletterUniqueNameByMerchantId($newsletterdata['name'], $merchantId);
            $isUniqueCode = $this->Newsletter->checkNewsletterUniqueCodeByMerchantId($newsletterdata['content_key'], $merchantId);
            if ($isUniqueName) {
                if ($isUniqueCode) {
                    if ($this->request->data['Newsletter']['store_id'] == 'HQ') {
                        $newsletterdata = array();
                        $newsletterdata['name']         = trim(strip_tags($this->request->data['Newsletter']['name']));
                        $newsletterdata['content_key']  = trim(strip_tags($this->request->data['Newsletter']['content_key']));
                        $newsletterdata['content']      = trim($this->request->data['Newsletter']['content']);
                        $newsletterdata['is_active']    = trim($this->request->data['Newsletter']['is_active']);
                        $newsletterdata['merchant_id']  = $merchantId;
                        $newsletterdata['type']         = $this->request->data['Newsletter']['type'];
                        $newsletterdata['added_from'] = 2;
                        
                        $this->Newsletter->create();
                        $this->Newsletter->saveNewsletter($this->Common->trimValue($newsletterdata));
                        $last_insert_id = $this->Newsletter->getLastInsertID();
                        if (!empty($last_insert_id)) 
                        {
                            $this->loadModel("NewsletterManagement");
                            $sendtime = "";
                            if (!empty($this->request->data['NewsletterManagement'])) 
                            {
                                if (!empty($this->request->data['NewsletterManagement']['timezone_send_time']) && $this->request->data['NewsletterManagement']['send_type'] == 1) { //Monthly
                                    $datatoSave['send_date'] = $this->request->data['NewsletterManagement']['send_date'];
                                    $datatoSave['send_time'] = $this->request->data['NewsletterManagement']['timezone_send_time'];
                                } else if (isset($this->request->data['NewsletterManagement']['send_type']) && $this->request->data['NewsletterManagement']['send_type'] == 2) { //Weekly
                                    $datatoSave['send_day'] = $this->request->data['NewsletterManagement']['send_day'];
                                    $datatoSave['send_time'] = $this->request->data['NewsletterManagement']['timezone_send_time'];
                                } else if (isset($this->request->data['NewsletterManagement']['send_type']) && $this->request->data['NewsletterManagement']['send_type'] == 3) { //Daily
                                    $datatoSave['send_time'] = $this->request->data['NewsletterManagement']['timezone_send_time'];
                                } else if (isset($this->request->data['NewsletterManagement']['send_type']) && $this->request->data['NewsletterManagement']['send_type'] == 4 && !empty($this->request->data['NewsletterManagement']['specific_date'])) {
                                    $sDate = $this->Dateform->formatDate($this->request->data['NewsletterManagement']['specific_date']);
                                    $datatoSave['specific_date'] = $sDate;
                                    $datatoSave['send_time'] = $this->request->data['NewsletterManagement']['timezone_send_time'];
                                }
                                $datatoSave['merchant_id'] = $merchantId;
                                $datatoSave['newsletter_id'] = $last_insert_id;
                                $datatoSave['timezone_send_time'] = $this->request->data['NewsletterManagement']['timezone_send_time'];
                                $datatoSave['send_type'] = $this->request->data['NewsletterManagement']['send_type'];
                                $this->NewsletterManagement->deleteAll(array('NewsletterManagement.newsletter_id' => $last_insert_id), false);
                                $this->NewsletterManagement->create();
                                if ($this->NewsletterManagement->save($datatoSave)) {
                                    $this->request->data = "";
                                }
                            }
                        }
                    }
                    else if ($this->request->data['Newsletter']['store_id'] == 'All') 
                    {
                        $storeData = $this->Store->getAllStoreByMerchantId($merchantId);
                        if (!empty($storeData)) {
                            foreach ($storeData as $key => $store) {
                                $this->request->data['Newsletter']['store_id'] = $store['Store']['id'];
                                $this->request->data['Newsletter']['added_from']= 2;
                                $this->_saveNewsLetterAdd($merchantId, $this->request->data, 'All');
                            }
                        }
                    }
                    else
                    {
                        $this->request->data['Newsletter']['added_from']= 2;
                        $this->_saveNewsLetterAdd($merchantId, $this->request->data);
                    }

                    $this->Session->setFlash(__("Newsletter Successfully Created"), 'alert_success');
                    $this->redirect(array('controller' => 'hqnewsletters', 'action' => 'addNewsletter'));
                } else {
                    $this->Session->setFlash(__("Newsletter content key Already exists"), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Newsletter subject Already exists"), 'alert_failed');
            }
        }
        $timeRange = "";
        $timeRange = $this->HqCommon->getStoreTimeAdmin("00:30", "23:59", null);
        $this->set('timeOptions', $timeRange);
        $this->_newsletterMerchantList();
    }

    private function _newsletterMerchantList($clearAction = null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $merchantId = $this->Session->read('merchantId');
        $criteria = "Newsletter.is_deleted=0 AND Newsletter.merchant_id=$merchantId";
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
            if (isset($this->request->data['Newsletter']['isActive']) && $this->request->data['Newsletter']['isActive'] != '') {
                $active = trim($this->request->data['Newsletter']['isActive']);
                $criteria .= " AND (Newsletter.is_active ='" . $active . "')";
            }
            if (!empty($this->request->data['Newsletter']['search'])) {
                $search = trim($this->request->data['Newsletter']['search']);
                $criteria .= " AND (Newsletter.name LIKE '%" . $search . "%')";
            }
            if (!empty($this->request->data['Newsletter']['storeId'])) {
                $storeId = $this->request->data['Newsletter']['storeId'];
                if ($storeId == 'HQ') {
                    $criteria.=" AND Newsletter.added_from=2";
                } elseif ($storeId != 'HQ' && $storeId != 'All') {
                    $criteria.=" AND Newsletter.store_id= $storeId";
                } elseif ($storeId == 'All') {
                    $criteria.=" AND Newsletter.added_from=1";
                }
            }
        } else {
            $criteria.=" AND Newsletter.added_from=1";
        }
        $this->Newsletter->bindModel(
                array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('store_name')
                )
            )
                ), false
        );
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
        $this->layout = "hq_dashboard";
        $data['Newsletter']['id'] = $this->Encryption->decode($EncryptNewsletterID);
        $data['Newsletter']['is_active'] = $status;
        if ($this->Newsletter->saveNewsletter($data)) {
            if ($status) {
                $SuccessMsg = "Newsletter Activated Successfully";
            } else {
                $SuccessMsg = "Newsletter Deactivated Successfully";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect($this->referer());
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect($this->referer());
        }
    }

    /* ------------------------------------------------
      Function name:deleteNewsletter()
      Description:Delete newsletter from list
      created:21/8/2015
      ----------------------------------------------------- */

    public function deleteNewsletter($EncryptNewsletterID = null) {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $data['Newsletter']['id'] = $this->Encryption->decode($EncryptNewsletterID);
        $data['Newsletter']['is_deleted'] = 1;
        if ($this->Newsletter->saveNewsletter($data)) {
            $this->Session->setFlash(__("Newsletter deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqnewsletters', 'action' => 'addNewsletter'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqnewsletters', 'action' => 'addNewsletter'));
        }
    }

    /* ------------------------------------------------
      Function name:editNewsletter()
      Description:Edit Newsletter contents
      created:21/8/2015
      ----------------------------------------------------- */

    public function editNewsletter($EncryptNewsletterID = null) {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $data['Newsletter']['id'] = $this->Encryption->decode($EncryptNewsletterID);
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
        $newsletterDetail = $this->Newsletter->findById($data['Newsletter']['id']);
        if ($this->request->is(array('post', 'put')) && $this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $newsletterTitle = trim($this->request->data['Newsletter']['name']);
            $newsletterCode = trim($this->request->data['Newsletter']['content_key']);
            $isUniqueName = $this->Newsletter->checkNewsletterUniqueNameByMerchantId($newsletterTitle, $merchantId, $data['Newsletter']['id']);
            $isUniqueCode = $this->Newsletter->checkNewsletterUniqueCodeByMerchantId($newsletterCode, $merchantId, $data['Newsletter']['id']);
            if ($isUniqueName) {
                if ($isUniqueCode) {
                    $newsletterdata = array();
                    $newsletterdata['name']         = trim(strip_tags($this->request->data['Newsletter']['name']));
                    $newsletterdata['content_key']  = trim(strip_tags($this->request->data['Newsletter']['content_key']));    
                    $newsletterdata['id']           = trim($this->request->data['Newsletter']['id']);
                    $newsletterdata['content']      = trim($this->request->data['Newsletter']['content']);
                    $newsletterdata['is_active']    = trim($this->request->data['Newsletter']['is_active']);
                    $newsletterdata['merchant_id']  = $merchantId;
                    $newsletterdata['type'] = $this->request->data['Newsletter']['type'];
                    if ($this->Newsletter->saveNewsletter($newsletterdata)) {
                        $this->loadModel("NewsletterManagement");
                        $sendtime = "";
                        if (!empty($this->request->data['NewsletterManagement'])) {
                            if (!empty($this->request->data['NewsletterManagement']['timezone_send_time']) && $this->request->data['NewsletterManagement']['send_type'] == 1) { //Monthly
                                $datatoSave['send_date'] = $this->request->data['NewsletterManagement']['send_date'];
                                $datatoSave['send_time'] = $this->request->data['NewsletterManagement']['timezone_send_time'];
                            } else if (isset($this->request->data['NewsletterManagement']['send_type']) && $this->request->data['NewsletterManagement']['send_type'] == 2) { //Weekly
                                $datatoSave['send_day'] = $this->request->data['NewsletterManagement']['send_day'];
                                $datatoSave['send_time'] = $this->request->data['NewsletterManagement']['timezone_send_time'];
                            } else if (isset($this->request->data['NewsletterManagement']['send_type']) && $this->request->data['NewsletterManagement']['send_type'] == 3) { //Daily
                                $datatoSave['send_time'] = $this->request->data['NewsletterManagement']['timezone_send_time'];
                            } else if (isset($this->request->data['NewsletterManagement']['send_type']) && $this->request->data['NewsletterManagement']['send_type'] == 4 && !empty($this->request->data['NewsletterManagement']['specific_date'])) {
                                $sDate = $this->Dateform->formatDate($this->request->data['NewsletterManagement']['specific_date']);
                                $datatoSave['specific_date'] = $sDate;
                                $datatoSave['send_time'] = $this->request->data['NewsletterManagement']['timezone_send_time'];
                            }
                            $datatoSave['merchant_id'] = $merchantId;
                            $datatoSave['newsletter_id'] = $this->request->data['Newsletter']['id'];
                            $datatoSave['timezone_send_time'] = $this->request->data['NewsletterManagement']['timezone_send_time'];
                            $datatoSave['send_type'] = $this->request->data['NewsletterManagement']['send_type'];
                            $this->NewsletterManagement->deleteAll(array('NewsletterManagement.newsletter_id' => $datatoSave['newsletter_id']), false);
                            $this->NewsletterManagement->save($datatoSave);
                        }
                    }
                    $this->Session->setFlash(__("Newsletter Successfully Updated."), 'alert_success');
                    $this->redirect(array('controller' => 'hqnewsletters', 'action' => 'addNewsletter'));
                } else {
                    $this->Session->setFlash(__("Newsletter Code Already exists"), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Newsletter Name Already exists"), 'alert_failed');
            }
        }
        $this->request->data = $newsletterDetail;
        $timeRange = "";
        $timeRange = $this->HqCommon->getStoreTimeAdmin("00:30", "23:59", null);
        $this->set('timeOptions', $timeRange);
    }

    public function newsLetterAdd() {
        $this->layout = "hq_dashboard";
        if ($this->request->is('post') && !empty($this->request->data['Newsletter']['name'])) {
            $merchantId = $this->Session->read('merchantId');
            $this->request->data = $this->Common->trimValue($this->request->data);
            if ($this->request->data['Newsletter']['store_id'] == 'All') {
                $storeData = $this->Store->getAllStoreByMerchantId($merchantId);
                if (!empty($storeData)) {
                    foreach ($storeData as $key => $store) {
                        $this->request->data['Newsletter']['store_id'] = $store['Store']['id'];
                        $this->_saveNewsLetterAdd($merchantId, $this->request->data, 'All');
                    }
                }
            } else {
                $this->_saveNewsLetterAdd($merchantId, $this->request->data);
            }
            $this->request->data = '';
        }
        $timeRange = "";
        $timeRange = $this->HqCommon->getStoreTimeAdmin("00:30", "23:59", null);
        $this->set('timeOptions', $timeRange);
        $this->_newsLetterList();
    }

    private function _saveNewsLetterAdd($merchantId = null, $postData = null, $type = null) {
        $newsletterdata['name'] = trim($this->request->data['Newsletter']['name']);
        $storeID = trim($this->request->data['Newsletter']['store_id']);
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
                $newsletterdata['type']         = $this->request->data['Newsletter']['type'];
                
                $this->Newsletter->create();
                $this->Newsletter->saveNewsletter($this->Common->trimValue($newsletterdata));
                $last_insert_id = $this->Newsletter->getLastInsertID();
                if (!empty($last_insert_id)) {
                    $this->loadModel('TimeZone');
                    $this->loadModel("NewsletterManagement");
                    $this->loadModel('Store');
                    $sendtime = "";
                    if (!empty($this->request->data['NewsletterManagement'])) {

                        if (!empty($this->request->data['NewsletterManagement']['timezone_send_time']) && $this->request->data['NewsletterManagement']['send_type'] == 1) { //Monthly
                            $dateTime = date('Y-m-') . $this->request->data['NewsletterManagement']['send_date'] . " " . $this->request->data['NewsletterManagement']['timezone_send_time'];  // create date time from post data
                            $sendtime = $this->HqCommon->storeToServerTimeZoneHq($dateTime, true, $storeID);
                            $newdate = strtotime($sendtime);
                            $crondate = date('d', $newdate);
                            $crontime = date('H:i:s', $newdate);
                            $datatoSave['send_date'] = $this->request->data['NewsletterManagement']['send_date'];
                            $datatoSave['send_time'] = $crontime;
                        } else if (isset($this->request->data['NewsletterManagement']['send_type']) && $this->request->data['NewsletterManagement']['send_type'] == 2) { //Weekly
                            $dateTime = date('Y-m-') . "02" . $this->request->data['NewsletterManagement']['timezone_send_time'];  // create date time from post data
                            $sendtime = $this->HqCommon->storeToServerTimeZoneHq($dateTime, true, $storeID); // convert to store time zone
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
                            $sendtime = $this->HqCommon->storeToServerTimeZoneHq($dateTime, true, $storeID);
                            $newdate = strtotime($sendtime);
                            $crontime = date('H:i:s', $newdate);
                            $datatoSave['send_time'] = $crontime;
                        } else if (isset($this->request->data['NewsletterManagement']['send_type']) && $this->request->data['NewsletterManagement']['send_type'] == 4 && !empty($this->request->data['NewsletterManagement']['specific_date'])) {
                            $dateTime = $this->request->data['NewsletterManagement']['timezone_send_time']; //create datetime from post data
                            $sendtime = $this->HqCommon->storeToServerTimeZoneHq($dateTime, true, $storeID);
                            $newdate = strtotime($sendtime);
                            $crontime = date('H:i:s', $newdate);
                            $datatoSave['send_time'] = $crontime;
                            $sDate = $this->Dateform->formatDate($this->request->data['NewsletterManagement']['specific_date']);
                            $datatoSave['specific_date'] = $sDate;
                        }
                        $datatoSave['store_id'] = $storeID;
                        $datatoSave['newsletter_id'] = $last_insert_id;
                        $datatoSave['timezone_send_time'] = $this->request->data['NewsletterManagement']['timezone_send_time'];
                        $datatoSave['send_type'] = $this->request->data['NewsletterManagement']['send_type'];
                        $datatoSave['type'] = 1;
                        $this->NewsletterManagement->deleteAll(array('NewsletterManagement.store_id' => $storeID, 'NewsletterManagement.newsletter_id' => $last_insert_id), false);
                        $this->NewsletterManagement->create();
                        $this->NewsletterManagement->save($datatoSave);
                    }
                }
                $this->Session->setFlash(__("Newsletter Successfully Created"), 'alert_success');
            } else {
                $this->Session->setFlash(__("Newsletter code Already exists"), 'alert_failed');
            }
        } else {
            $this->Session->setFlash(__("Newsletter name Already exists"), 'alert_failed');
        }
    }

    private function _newsLetterList($clearAction = null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $storeID = @$this->request->data['Newsletter']['storeId'];
        $merchantId = $this->Session->read('merchantId');
        $criteria = "Newsletter.is_deleted=0 AND Newsletter.merchant_id=" . $merchantId;
        if (!empty($clearAction) && $clearAction == 1) {
            $criteria .= " AND Newsletter.is_active =1";
        }
        if (!empty($storeID)) {
            $criteria .= " AND Newsletter.store_id =$storeID";
        }
        if ($this->Session->read('HqNewsletterSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqNewsletterSearchData'), true);
        } else {
            $this->Session->delete('HqNewsletterSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('HqNewsletterSearchData', json_encode($this->request->data));
            if (isset($this->request->data['Newsletter']['isActive']) && $this->request->data['Newsletter']['isActive'] != '') {
                $active = trim($this->request->data['Newsletter']['isActive']);
                $criteria .= " AND (Newsletter.is_active` ='" . $active . "')";
            }
            if (!empty($this->request->data['Newsletter']['search'])) {
                $search = trim($this->request->data['Newsletter']['search']);
                $criteria .= " AND (Newsletter.name LIKE '%" . $search . "%')";
            }
        }
        $this->Newsletter->bindModel(
                array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'type' => 'inner',
                    'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1),
                    'fields' => array('store_name')
                )
            )
                ), false
        );
        $this->paginate = array('conditions' => array($criteria), 'order' => array('Newsletter.created' => 'DESC'));
        $newsletterDetail = $this->paginate('Newsletter');
        $this->set('list', $newsletterDetail);
    }

    /* ------------------------------------------------
      Function name:activateNewsletter()
      Description:Active/Deactive newsletter
      created:21/8/2015
      ----------------------------------------------------- */

    public function activateNewsletterHq($EncryptNewsletterID = null, $status = 0) {
        $data['Newsletter']['merchant_id'] = $this->Session->read('merchantId');
        $data['Newsletter']['id'] = $this->Encryption->decode($EncryptNewsletterID);
        $data['Newsletter']['is_active'] = $status;
        if ($this->Newsletter->saveNewsletter($data)) {
            if ($status) {
                $SuccessMsg = "Newsletter Activated Successfully";
            } else {
                $SuccessMsg = "Newsletter Deactivated Successfully";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect($this->referer());
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect($this->referer());
        }
    }

    /* ------------------------------------------------
      Function name:deleteNewsletter()
      Description:Delete newsletter from list
      created:21/8/2015
      ----------------------------------------------------- */

    public function deleteNewsletterHq($EncryptNewsletterID = null) {
        $data['Newsletter']['merchant_id'] = $this->Session->read('merchantId');
        $data['Newsletter']['id'] = $this->Encryption->decode($EncryptNewsletterID);
        $data['Newsletter']['is_deleted'] = 1;
        if ($this->Newsletter->saveNewsletter($data)) {
            $this->Session->setFlash(__("Newsletter deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqnewsletters', 'action' => 'newsLetterAdd'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqnewsletters', 'action' => 'newsLetterAdd'));
        }
    }

    /* ------------------------------------------------
      Function name:editNewsletter()
      Description:Edit Newsletter contents
      created:21/8/2015
      ----------------------------------------------------- */

    public function newsLetterEdit($EncryptNewsletterID = null) {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $data['Newsletter']['id'] = $this->Encryption->decode($EncryptNewsletterID);
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
        $newsletterDetail = $this->Newsletter->findById($data['Newsletter']['id']);
        $storeId = $newsletterDetail['Newsletter']['store_id'];
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $newsletterTitle = trim($this->request->data['Newsletter']['name']);
            $newsletterCode = trim($this->request->data['Newsletter']['content_key']);
            $isUniqueName = $this->Newsletter->checkNewsletterUniqueName($newsletterTitle, $storeId, $data['Newsletter']['id']);
            $isUniqueCode = $this->Newsletter->checkNewsletterUniqueCode($newsletterCode, $storeId, $data['Newsletter']['id']);
            if ($isUniqueName) {
                if ($isUniqueCode) {
                    $newsletterdata = array();
                    $newsletterdata['name'] = trim($this->request->data['Newsletter']['name']);
                    $newsletterdata['content_key'] = trim($this->request->data['Newsletter']['content_key']);
                    $newsletterdata['id'] = trim($this->request->data['Newsletter']['id']);
                    $newsletterdata['content'] = trim($this->request->data['Newsletter']['content']);
                    $newsletterdata['is_active'] = trim($this->request->data['Newsletter']['is_active']);
                    $newsletterdata['store_id'] = $storeId;
                    $newsletterdata['merchant_id'] = $merchantId;
                    $this->loadModel('Newsletter');
                    if ($this->Newsletter->saveNewsletter($newsletterdata)) {
                        $this->loadModel('TimeZone');
                        $this->loadModel("NewsletterManagement");
                        $this->loadModel('Store');
                        $sendtime = "";
                        //$store = $this->Store->find('first', array('fields' => array('Store.time_zone_id', 'Store.id'), 'conditions' => array('Store.id' => $storeId, 'Store.is_active' => 1, 'Store.is_deleted' => 0)));
                        if (!empty($this->request->data['NewsletterManagement'])) {
                            if (!empty($this->request->data['NewsletterManagement']['timezone_send_time']) && $this->request->data['NewsletterManagement']['send_type'] == 1) { //Monthly
                                $dateTime = date('Y-m-') . $this->request->data['NewsletterManagement']['send_date'] . " " . $this->request->data['NewsletterManagement']['timezone_send_time'];  // create date time from post data
                                $sendtime = $this->HqCommon->storeToServerTimeZoneHq($dateTime, true, $storeId); // convert to store time zone
                                $newdate = strtotime($sendtime);
                                $crondate = date('d', $newdate);
                                $crontime = date('H:i:s', $newdate);
                                $datatoSave['send_date'] = $this->request->data['NewsletterManagement']['send_date'];
                                $datatoSave['send_time'] = $crontime;
                            } else if (isset($this->request->data['NewsletterManagement']['send_type']) && $this->request->data['NewsletterManagement']['send_type'] == 2) { //Weekly
                                $dateTime = date('Y-m-') . "02" . $this->request->data['NewsletterManagement']['timezone_send_time'];  // create date time from post data
                                $sendtime = $this->HqCommon->storeToServerTimeZoneHq($dateTime, true, $storeId);
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
                                $sendtime = $this->HqCommon->storeToServerTimeZoneHq($dateTime, true, $storeId);
                                $newdate = strtotime($sendtime);
                                $crontime = date('H:i:s', $newdate);
                                $datatoSave['send_time'] = $crontime;
                            } else if (isset($this->request->data['NewsletterManagement']['send_type']) && $this->request->data['NewsletterManagement']['send_type'] == 4 && !empty($this->request->data['NewsletterManagement']['specific_date'])) {
                                $dateTime = $this->request->data['NewsletterManagement']['timezone_send_time']; //create datetime from post data
                                $sendtime = $this->HqCommon->storeToServerTimeZoneHq($dateTime, true, $storeId);
                                $newdate = strtotime($sendtime);
                                $crontime = date('H:i:s', $newdate);
                                $datatoSave['send_time'] = $crontime;
                                $sDate = $this->Dateform->formatDate($this->request->data['NewsletterManagement']['specific_date']);
                                $datatoSave['specific_date'] = $sDate;
                            }
                            $datatoSave['store_id'] = $storeId;
                            $datatoSave['newsletter_id'] = $this->request->data['Newsletter']['id'];
                            $datatoSave['timezone_send_time'] = $this->request->data['NewsletterManagement']['timezone_send_time'];
                            $datatoSave['send_type'] = $this->request->data['NewsletterManagement']['send_type'];
                            $this->NewsletterManagement->deleteAll(array('NewsletterManagement.store_id' => $storeId, 'NewsletterManagement.newsletter_id' => $this->request->data['Newsletter']['id']), false);
                            //prx($datatoSave);
                            $this->NewsletterManagement->save($datatoSave);
                        }
                    }
                    $this->Session->setFlash(__("Newsletter Successfully Updated."), 'alert_success');
                    $this->redirect(array('controller' => 'hqnewsletters', 'action' => 'newsLetterAdd'));
                } else {
                    $this->Session->setFlash(__("Newsletter Code Already exists"), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Newsletter Name Already exists"), 'alert_failed');
            }
        }
        $timeRange = $this->HqCommon->getStoreTimeAdmin("00:30", "23:59", $storeId);
        $this->set('timeOptions', $timeRange);
        $this->request->data = $newsletterDetail;
    }

    public function newsletterManagement($EncryptNewsletterID = null) {
        $this->layout = "hq_dashboard";
        $newsletterID = $this->Encryption->decode($EncryptNewsletterID);
        $newsLetterData = $this->Newsletter->findById($newsletterID, array('store_id'));
        $storeId = $newsLetterData['Newsletter']['store_id'];
        $timeRange = $this->HqCommon->getStoreTimeAdmin("00:30", "23:59", $storeId);
        $this->set('timeOptions', $timeRange);
        $this->set('storeId', $storeId);
        $this->loadModel('TimeZone');
        $this->loadModel("NewsletterManagement");
        $sendtime = "";
        //$store = $this->Store->find('first', array('fields' => array('Store.time_zone_id', 'Store.id'), 'conditions' => array('Store.id' => $storeId, 'Store.is_active' => 1, 'Store.is_deleted' => 0)));
        $datatoSave = array();
        if (!empty($this->request->data)) {
            if (!empty($this->request->data['NewsletterManagement']['timezone_send_time']) && $this->request->data['NewsletterManagement']['send_type'] == 1) { //Monthly
                $dateTime = date('Y-m-') . $this->request->data['NewsletterManagement']['send_date'] . " " . $this->request->data['NewsletterManagement']['timezone_send_time'];  // create date time from post data

                $sendtime = $this->HqCommon->storeToServerTimeZoneHq($dateTime, false, $storeId); // convert to store time zone
                $newdate = strtotime($sendtime);
                $crondate = date('d', $newdate);
                $crontime = date('H:i:s', $newdate);
                $datatoSave['send_date'] = $this->request->data['NewsletterManagement']['send_date'];
                $datatoSave['send_time'] = $crontime;
            } else if (isset($this->request->data['NewsletterManagement']['send_type']) && $this->request->data['NewsletterManagement']['send_type'] == 2) { //Weekly
                $dateTime = date('Y-m-') . "02" . $this->request->data['NewsletterManagement']['timezone_send_time'];  // create date time from post data
                $sendtime = $this->HqCommon->storeToServerTimeZoneHq($dateTime, false, $storeId);
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
                $sendtime = $this->HqCommon->storeToServerTimeZoneHq($dateTime, true, $storeId);

                $newdate = strtotime($sendtime);
                $crontime = date('H:i:s', $newdate);
                $datatoSave['send_time'] = $crontime;
            } else if (isset($this->request->data['NewsletterManagement']['send_type']) && $this->request->data['NewsletterManagement']['send_type'] == 4 && !empty($this->request->data['NewsletterManagement']['specific_date'])) {
                $dateTime = $this->request->data['NewsletterManagement']['timezone_send_time']; //create datetime from post data
                $sendtime = $this->HqCommon->storeToServerTimeZoneHq($dateTime, true, $storeId);
                $newdate = strtotime($sendtime);
                $crontime = date('H:i:s', $newdate);
                $datatoSave['send_time'] = $crontime;
                $sDate = $this->Dateform->formatDate($this->request->data['NewsletterManagement']['specific_date']);
                $datatoSave['specific_date'] = $sDate;
            }
            $datatoSave['store_id'] = $storeId;
            $datatoSave['newsletter_id'] = $newsletterID;
            $datatoSave['timezone_send_time'] = $this->request->data['NewsletterManagement']['timezone_send_time'];
            $datatoSave['send_type'] = $this->request->data['NewsletterManagement']['send_type'];
            $this->NewsletterManagement->deleteAll(array('NewsletterManagement.store_id' => $storeId, 'NewsletterManagement.newsletter_id' => $newsletterID), false);
            $this->NewsletterManagement->save($datatoSave);
            $this->Session->setFlash(__("Newsletter setting successfully saved."), 'alert_success');
            $this->redirect('/hqnewsletters/newsLetterAdd');
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
        $this->layout = "hq_dashboard";
        $storeID = @$this->request->data['User']['store_id'];
        $value = "";

        $this->loadModel('User');
        if ($this->Session->read('HqCustomerSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqCustomerSearchData'), true);
        } else {
            $this->Session->delete('HqCustomerSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if ($this->Session->read('HqCustomerSearchData')) {
            $this->request->data = json_decode($this->Session->read('HqCustomerSearchData'), true);
            if (!empty($this->request->data['User']['store_id'])) {
                $storeID = $this->request->data['User']['store_id'];
            }
        }
        $merchant_id = $this->Session->read('merchantId');
        $criteria = "User.merchant_id =$merchant_id AND User.role_id IN (4,5) AND User.is_deleted=0 AND User.is_active=1";
        if (!empty($storeID)) {
            $criteria .= " AND User.store_id =$storeID";
        }
        if (!empty($this->request->data)) {
            $this->Session->write('HqCustomerSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['User']['keyword'])) {
                $value = trim($this->request->data['User']['keyword']);
                $explode = explode(' ', $value);
                //$criteria .= " AND (User.fname LIKE '%" . $value . "%' OR User.email LIKE '%" . $value . "%' OR User.lname LIKE '%" . $value . "%' OR User.phone LIKE '%" . $value . "%')";
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
                $criteria.= " AND (Date(User.created) >= '" . $stratdate . "' AND Date(User.created) <='" . $enddate . "')";
            }
        }
        $this->User->bindModel(
                array(
            'belongsTo' => array(
                "Store" => array('className' => 'Store',
                    'foreignKey' => 'store_id',
                    'type' => 'left',
                    'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1),
                    'fields' => array('store_name'))
            )
                ), false
        );
        $this->paginate = array('conditions' => array($criteria), 'order' => array('User.created' => 'DESC'));
        $customerdetail = $this->paginate('User');
        $this->set('list', $customerdetail);
        $this->set('keyword', $value);
    }

    /* Ajax function to get promotions, coupons, newsletters, offers names */

    function getPromotionValue() {
        $this->autoRender = false;
        $merchantId = $this->Session->read('merchantId');
        $html = '';
        if (isset($this->request->data['promotionTypeId']) && !empty($this->request->data['promotionTypeId']) && !empty($this->request->data['storeId'])) {
            $storeID = $this->request->data['storeId'];
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
                default :
                    $getData = '';
                    break;
            }
        }
        if (isset($getData) && !empty($getData)) {
            $html .= "<option>-Promotion Record-</option>";
            foreach ($getData as $key => $val) {
                $html .= "<option value=" . $key . ">" . $val . "</option>";
            }
        } else {
            $html .= "<option>-No Record-</option>";
        }
        return $html;
    }

    /* Share promotions, coupons, newsletters, offers with users */

    function share() {
        $this->autoRender = false;
        if (!empty($this->request->data['Customer']['storeId'])) {
            $storeID = $this->request->data['Customer']['storeId'];
        } else {
            $this->Session->setFlash(__("Store Not Selected."), 'alert_failed');
            $this->redirect($this->referer());
        }
        $merchantId = $this->Session->read('merchantId');
        $this->loadModel('Store');
        $this->loadModel('User');
        $storeEmail = $this->Store->fetchStoreDetail($storeID);
        $userData = array();
        //$options['conditions'][] = array('User.store_id' => $storeID);
        //$options['conditions'][] = array('User.role_id' => 4);
        $options['conditions'][] = array('User.is_deleted' => 0);
        $options['fields'] = array('User.id', 'User.email', 'User.fname', 'User.lname', 'User.is_newsletter');
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
                    default :
                        break;
                }

                if ($c > 0) {
                    $this->Session->setFlash(__("Newsletter sent successfully."), 'alert_success');
                }

                $this->redirect('/hqnewsletters/customerList');
            }
        }
    }

    /* ------------------------------------------------
      Function name:activateCustomer()
      Description:Active/deactive Customer
      created:25/8/2016
      ----------------------------------------------------- */

    public function activateCustomerHq($EncryptCustomerID = null, $status = 0) {
        $this->autoRender = false;
        $data['User']['merchant_id'] = $this->Session->read('merchantId');
        $data['User']['id'] = $this->Encryption->decode($EncryptCustomerID);
        $data['User']['is_active'] = $status;
        $this->loadModel('User');
        if ($this->User->saveUserInfo($data)) {
            if ($status) {
                $SuccessMsg = "User Activated";
            } else {
                $SuccessMsg = "User Deactivated and User will not get Display in the List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hqnewsletters', 'action' => 'customerList'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqnewsletters', 'action' => 'customerList'));
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
            if (!empty($_GET['storeId'])) {
                $storeID = $_GET['storeId'];
                $searchData = $this->User->find('all', array('fields' => array('User.userName', 'User.email', 'User.phone'), 'conditions' => array('OR' => array('User.userName LIKE' => '%' . $_GET['term'] . '%', 'User.email LIKE' => '%' . $_GET['term'] . '%', 'User.phone LIKE' => '%' . $_GET['term'] . '%'), 'User.is_deleted' => 0, 'User.store_id' => $storeID, 'User.role_id' => array(4, 5))));
            } else {
                $merchant_id = $this->Session->read('merchantId');
                $searchData = $this->User->find('all', array('fields' => array('User.userName', 'User.email', 'User.phone'), 'conditions' => array('OR' => array('User.userName LIKE' => '%' . $_GET['term'] . '%', 'User.email LIKE' => '%' . $_GET['term'] . '%', 'User.phone LIKE' => '%' . $_GET['term'] . '%'), 'User.is_deleted' => 0, 'User.merchant_id' => $merchant_id, 'User.role_id' => array(4, 5))));
            }
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

    /* ----------------------- Special Day's Functionality End-------------------------------------- */

    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {

            if (!empty($_GET['storeId'])) {
                $storeID = $_GET['storeId'];
            } else {
                $merchant_id = $this->Session->read('merchantId');
                $storeID = $this->Store->getAllStoresByMerchantId($merchant_id);
            }
            $this->loadModel('Newsletter');
            //$criteria = "Newsletter.store_id = $storeID AND Newsletter.is_deleted=0";
            $searchData = $this->Newsletter->find('list', array('fields' => array('Newsletter.name', 'Newsletter.name'), 'conditions' => array('OR' => array('Newsletter.name LIKE' => '%' . $_GET['term'] . '%'), 'Newsletter.store_id' => $storeID, 'Newsletter.is_deleted' => 0)));
            echo json_encode($searchData);
        } else {
            exit;
        }
    }

    public function getMerchantNewsLetters() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $merchant_id = $this->Session->read('merchantId');
            $criteria = "Newsletter.is_deleted=0 AND Newsletter.added_from=2 AND Newsletter.merchant_id=$merchant_id";
            $this->loadModel('Newsletter');
            //$criteria = "Newsletter.store_id = $storeID AND Newsletter.is_deleted=0";
            $searchData = $this->Newsletter->find('list', array('fields' => array('Newsletter.name', 'Newsletter.name'), 'conditions' => array('OR' => array('Newsletter.name LIKE' => '%' . $_GET['term'] . '%'), $criteria)));
            echo json_encode($searchData);
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
        $this->layout = "hq_dashboard";
        if ($this->request->is(array('post'))) {
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

    public function storeNewsletterList() {
        $this->layout = "hq_dashboard";
        $this->_newsLetterList(1);
    }

    /* ------------------------------------------------
      Function name:showStoreNewsletterToHqFront ()
      Description:Active/Deactive newsletter
      created:08/08/2017
      ----------------------------------------------------- */

    public function showStoreNewsletterToHqFront($EncryptNewsletterID = null, $status = 0, $hqStatus = null) {
        $data['Newsletter']['merchant_id'] = $this->Session->read('merchantId');
        $data['Newsletter']['id'] = $this->Encryption->decode($EncryptNewsletterID);
        if (empty($hqStatus)) {
            $data['Newsletter']['show_to_hq_front'] = $status;
        } else {
            $status = ($status == 0) ? 1 : 2;
            $data['Newsletter']['type'] = $status;
        }
        if ($this->Newsletter->saveNewsletter($data)) {
            if ($status) {
                $SuccessMsg = "Newsletter posted on front site successfully.";
            } else {
                $SuccessMsg = "Newsletter removed from front site successfully.";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
        } else {
            $this->Session->setFlash(__("Some problem occured."), 'alert_failed');
        }
        $this->redirect($this->referer());
    }

}
