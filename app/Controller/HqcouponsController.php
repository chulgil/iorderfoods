<?php

App::uses('HqAppController', 'Controller');

class HqcouponsController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'Common');
    public $uses = array('Coupon', 'UserCoupon', 'Store');
    public $layout = 'hq_dashboard';

    public function beforeFilter() {
        //Check permission for Admin User
        parent::beforeFilter();
    }

    private function _saveCoupons($merchant_id = null, $postData = null) {
        $this->request->data = $postData;
        $couponTitle = trim($this->request->data['Coupon']['name']);
        $couponCode = trim($this->request->data['Coupon']['coupon_code']);
        $storeId = trim($this->request->data['Coupon']['store_id']);
        $isUniqueName = $this->Coupon->checkCouponUniqueName($couponTitle, $storeId);
        $isUniqueCode = $this->Coupon->checkCouponUniqueCode($couponCode, $storeId);
        if ($isUniqueName) {
            if ($isUniqueCode) {
                if (!empty($this->request->data['Coupon']['days'])) {
                    $couponDays = implode(",", array_keys($this->request->data['Coupon']['days']));
                } else {
                    $couponDays = '';
                }
                $coupondata['image'] = $this->request->data['Coupon']['image'];
                $coupondata['store_id'] = $storeId;
                $coupondata['merchant_id'] = $merchant_id;
                $coupondata['name'] = trim($this->request->data['Coupon']['name']);
                $coupondata['start_date'] = $this->Dateform->formatDate($this->request->data['Coupon']['start_date']);
                $coupondata['end_date'] = $this->Dateform->formatDate($this->request->data['Coupon']['end_date']);
                $coupondata['allow_time'] = $this->request->data['Coupon']['allow_time'];
                $coupondata['start_time'] = $this->request->data['Coupon']['start_time'];
                $coupondata['end_time'] = $this->request->data['Coupon']['end_time'];
                $coupondata['days'] = $couponDays;
                $coupondata['coupon_code'] = trim($this->request->data['Coupon']['coupon_code']);
                $coupondata['number_can_use'] = $this->request->data['Coupon']['number_can_use'];
                $coupondata['discount_type'] = $this->request->data['Coupon']['discount_type'];
                $coupondata['discount'] = $this->request->data['Coupon']['discount'];
                $coupondata['is_active'] = $this->request->data['Coupon']['is_active'];
                if (isset($this->request->data['Coupon']['promotional_message']) && $this->request->data['Coupon']['promotional_message']) {
                    $coupondata['promotional_message'] = trim($this->request->data['Coupon']['promotional_message']);
                }
                $this->Coupon->create();
                $this->Coupon->saveCoupon($coupondata);
                $this->Session->setFlash(__("Coupon Successfully Added"), 'alert_success');
            } else {
                $this->Session->setFlash(__("Coupon Code Already exists"), 'alert_failed');
            }
        } else {
            $this->Session->setFlash(__("Coupon Title Already exists"), 'alert_failed');
        }
    }

    /* ------------------------------------------------
      Function name:index()
      Description:Add New Coupon and show list of added coupons
      created:22/8/2016
      ----------------------------------------------------- */

    public function index() {
        if ($this->request->is('post') && !empty($this->request->data['Coupon']['coupon_code'])) {
            $merchant_id = $this->Session->read('merchantId');
            $this->request->data = $this->Common->trimValue($this->request->data);
            $response = $this->Common->uploadMenuItemImages($this->request->data['Coupon']['image'], '/Coupon-Image/', $merchant_id, 480, 320);
            if (!$response['status']) {
                $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
            } else {
                $this->request->data['Coupon']['image'] = $response['imagename'];
                if ($this->request->data['Coupon']['store_id'] == 'All') {
                    $storeData = $this->Store->getAllStoreByMerchantId($merchant_id);
                    if (!empty($storeData)) {
                        foreach ($storeData as $store) {
                            $this->request->data['Coupon']['store_id'] = $store['Store']['id'];
                            $this->_saveCoupons($merchant_id, $this->request->data);
                        }
                    }
                } else {
                    $this->_saveCoupons($merchant_id, $this->request->data);
                }
                $this->request->data = '';
            }
        }
        $start = "00:30";
        $end = "24:00";
        $timeRange = $this->Common->getStoreTimeForHq($start, $end);
        $this->set('timeOptions', $timeRange);
        $this->_listCoupons();
    }

    private function _listCoupons($clearAction = null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $storeID = @$this->request->data['Coupon']['storeId'];
        $merchant_id = $this->Session->read('merchantId');
        $criteria = "Coupon.merchant_id =$merchant_id AND Coupon.is_deleted=0";
        if (!empty($storeID)) {
            $criteria .= " AND Coupon.store_id =$storeID";
        }
        if ($this->Session->read('HqCouponSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqCouponSearchData'), true);
        } else {
            $this->Session->delete('HqCouponSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('HqCouponSearchData', json_encode($this->request->data));
            if (isset($this->request->data['Coupon']['isActive']) && $this->request->data['Coupon']['isActive'] != "") {
                $active = trim($this->request->data['Coupon']['isActive']);
                $criteria .= " AND (Coupon.is_active =$active)";
            }
            if (!empty($this->request->data['Coupon']['search'])) {
                $search = trim($this->request->data['Coupon']['search']);
                $criteria .= " AND (Coupon.coupon_code LIKE '%" . $search . "%')";
            }
        }
        $this->Coupon->bindModel(
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
        $this->paginate = array('conditions' => array($criteria), 'order' => array('Coupon.created' => 'DESC'));
        $coupondetail = $this->paginate('Coupon');
        $this->set('list', $coupondetail);
    }

    /* ------------------------------------------------
      Function name:activateCoupon()
      Description:Active/deactive Coupon
      created:22/8/2016
      ----------------------------------------------------- */

    public function activateCoupon($EncryptCouponID = null, $status = 0) {
        $this->autoRender = false;
        $data['Coupon']['merchant_id'] = $this->Session->read('merchantId');
        $data['Coupon']['id'] = $this->Encryption->decode($EncryptCouponID);
        $data['Coupon']['is_active'] = $status;
        if ($this->Coupon->saveCoupon($data)) {
            if ($status) {
                $SuccessMsg = "Coupon Activated";
                $this->Session->setFlash(__($SuccessMsg), 'alert_success');
                $this->redirect(array('controller' => 'hqcoupons', 'action' => 'editCoupon/' . $EncryptCouponID . '#CouponStartDate'));
            } else {
                $SuccessMsg = "Coupon Deactivated and Coupon will not get Display in Menu List";
                $this->Session->setFlash(__($SuccessMsg), 'alert_success');
                $this->redirect(array('controller' => 'hqcoupons', 'action' => 'index'));
            }
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqcoupons', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteCoupon()
      Description:Delete Coupon
      created:22/8/2016
      ----------------------------------------------------- */

    public function deleteCoupon($EncryptCouponID = null) {
        $this->autoRender = false;
        $data['Coupon']['merchant_id'] = $this->Session->read('merchantId');
        $data['Coupon']['id'] = $this->Encryption->decode($EncryptCouponID);
        $data['Coupon']['is_deleted'] = 1;
        if ($this->Coupon->saveCoupon($data)) {
            $this->Session->setFlash(__("Coupon deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqcoupons', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqcoupons', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:editCoupon()
      Description:Edit Coupon
      created:22/8/2016
      ----------------------------------------------------- */

    public function editCoupon($EncryptCouponID = null) {
        $data['Coupon']['id'] = $this->Encryption->decode($EncryptCouponID);
        $this->loadModel('Coupon');
        $couponDetail = $this->Coupon->findById($data['Coupon']['id']);
        if ($this->request->data && !empty($couponDetail['Coupon']['store_id'])) {
            $merchantId = $this->Session->read('merchantId');
            $storeId = $couponDetail['Coupon']['store_id'];
            $this->request->data = $this->Common->trimValue($this->request->data);
            $couponTitle = trim($this->request->data['Coupon']['name']);
            //$couponCode = trim($this->request->data['Coupon']['coupon_code']);
            $isUniqueName = $this->Coupon->checkCouponUniqueName($couponTitle, $storeId, $data['Coupon']['id']);
            //$isUniqueCode = $this->Coupon->checkCouponUniqueCode($couponCode, $storeId, $data['Coupon']['id']);
            if ($isUniqueName) {
                //if ($isUniqueCode) {
                if ($this->request->data['Coupon']['image']['error'] == 0) {
                    $response = $this->Common->uploadMenuItemImages($this->request->data['Coupon']['image'], '/Coupon-Image/', $storeId, 480, 320);
                } elseif ($this->request->data['Coupon']['image']['error'] == 4) {
                    $response['status'] = true;
                    $response['imagename'] = '';
                }
                if (!$response['status']) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                } else {
                    if ($response['imagename']) {
                        $coupondata['image'] = $response['imagename'];
                    }
                    if (!empty($this->request->data['Coupon']['days'])) {
                        $this->request->data['Coupon']['days'] = implode(",", array_keys($this->request->data['Coupon']['days']));
                    } else {
                        $this->request->data['Coupon']['days'] = '';
                    }
                    $coupondata['store_id'] = $storeId;
                    $coupondata['merchant_id'] = $merchantId;
                    $coupondata['id'] = $data['Coupon']['id'];
                    $coupondata['used_count'] = $this->request->data['Coupon']['used_count'];
                    $coupondata['name'] = trim($this->request->data['Coupon']['name']);
                    $coupondata['start_date'] = $this->Dateform->formatDate($this->request->data['Coupon']['start_date']);
                    $coupondata['end_date'] = $this->Dateform->formatDate($this->request->data['Coupon']['end_date']);
                    $coupondata['allow_time'] = $this->request->data['Coupon']['allow_time'];
                    $coupondata['start_time'] = $this->request->data['Coupon']['start_time'];
                    $coupondata['end_time'] = $this->request->data['Coupon']['end_time'];
                    $coupondata['days'] = $this->request->data['Coupon']['days'];
                    //$coupondata['coupon_code'] = trim($this->request->data['Coupon']['coupon_code']);
                    $coupondata['number_can_use'] = $this->request->data['Coupon']['number_can_use'];
                    $coupondata['discount_type'] = $this->request->data['Coupon']['discount_type'];
                    $coupondata['discount'] = $this->request->data['Coupon']['discount'];
                    $coupondata['is_active'] = $this->request->data['Coupon']['is_active'];
                    if (isset($this->request->data['Coupon']['promotional_message']) && $this->request->data['Coupon']['promotional_message']) {
                        $coupondata['promotional_message'] = trim($this->request->data['Coupon']['promotional_message']);
                    }
                    $this->Coupon->saveCoupon($coupondata);
                    $this->Session->setFlash(__("Coupon Successfully Updated"), 'alert_success');
                    $this->redirect(array('controller' => 'hqcoupons', 'action' => 'index'));
                }
//                } else {
//                    $this->Session->setFlash(__("Coupon Code Already exists"), 'alert_failed');
//                }
            } else {
                $this->Session->setFlash(__("Coupon Title Already exists"), 'alert_failed');
            }
        }
        $start = "00:30";
        $end = "24:00";
        $timeRange = $this->Common->getStoreTimeForHq($start, $end);
        $this->set('timeOptions', $timeRange);
        $this->request->data = $couponDetail;
    }

    /* ------------------------------------------------
      Function name:deleteCouponPhoto()
      Description:Delete coupon Photo
      created:8/11/2016
      ----------------------------------------------------- */

    public function deleteCouponPhoto($EncryptCouponID = null) {
        $this->autoRender = false;
        $data['Coupon']['id'] = $this->Encryption->decode($EncryptCouponID);
        $data['Coupon']['image'] = '';
        if ($this->Coupon->saveCoupon($data)) {
            $this->Session->setFlash(__("Coupon Image deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqcoupons', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqcoupons', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:shareCoupon()
      Description:Share the coupon to customers
      created:2/8/2016
      ----------------------------------------------------- */

    public function shareCoupon($EncryptCouponID = null) {
        if (!empty($_GET['couponId'])) {
            $EncryptCouponID = $_GET['couponId'];
        }
        if ($EncryptCouponID) {
            $data['Coupon']['id'] = $this->Encryption->decode($EncryptCouponID);
        } else {
            $data['Coupon']['id'] = $this->request->data['User']['coupon_id'];
        }
        $this->Coupon->bindModel(
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
        $couponId = $this->Coupon->findById($data['Coupon']['id']);
        if (empty($couponId)) {
            $this->Session->setFlash(__("Something went wrong!."), 'alert_failed');
            $this->redirect($this->referer());
        }
        $storeId = $couponId['Coupon']['store_id'];
        if (!empty($this->request->data)) {
            $merchantId = $this->Session->read('merchantId');
            $this->request->data['User']['id'] = array_filter($this->request->data['User']['id']);
            $this->loadModel('Store');
            $storeEmail = $this->Store->fetchStoreDetail($storeId);
            $alreadyShared = 0;
            $newshared = 0;
            foreach ($this->request->data['User']['id'] as $data) {
                $this->loadModel('User');
                $this->User->bindModel(array('belongsTo' => array('CountryCode')));
                $shareuserdetail = $this->User->find('first', array('fields' => array('User.id', 'User.fname', 'User.lname', 'User.email', 'User.phone', 'User.is_emailnotification', 'User.is_smsnotification', 'User.country_code_id', 'CountryCode.code'), 'conditions' => array('User.id' => $data)));
                if (!empty($shareuserdetail)) {
                    $userCoupon['UserCoupon']['user_id'] = $shareuserdetail['User']['id'];
                    $userCoupon['UserCoupon']['store_id'] = $storeId;
                    $userCoupon['UserCoupon']['coupon_id'] = $this->request->data['User']['coupon_id'];
                    $userCoupon['UserCoupon']['coupon_code'] = $this->request->data['User']['coupon_code'];
                    $userCoupon['UserCoupon']['merchant_id'] = $merchantId;

                    $isUniqueUserShare = $this->UserCoupon->checkUserCouponData($userCoupon['UserCoupon']['user_id'], $userCoupon['UserCoupon']['coupon_code'], $userCoupon['UserCoupon']['store_id'], $userCoupon['UserCoupon']['coupon_id']);
                    if ($isUniqueUserShare) {
                        $this->UserCoupon->create();
                        $this->UserCoupon->saveUserCoupon($userCoupon);
                        $newshared++;
                        if ($shareuserdetail['User']['lname']) {
                            $fullName = $shareuserdetail['User']['fname'] . " " . $shareuserdetail['User']['lname'];
                        } else {
                            $fullName = $shareuserdetail['User']['fname'];
                        }

                        $template_type = 'coupon_offer';
                        $this->loadModel('EmailTemplate');
                        $emailSuccess = $this->EmailTemplate->storeTemplates($storeId, $merchantId, $template_type);
                        $this->loadModel('Coupon');
                        $couponDetail = $this->Coupon->getCouponDetail($this->request->data['User']['coupon_id'], $storeId);
                        if ($emailSuccess) {
                            if ($couponDetail['Coupon']['promotional_message']) {
                                $smsData = nl2br($couponDetail['Coupon']['promotional_message']);
                            } else {
                                $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                            }
                            $emailData = $emailSuccess['EmailTemplate']['template_message'];
                            $subject = $emailSuccess['EmailTemplate']['template_subject'];
                            $couponcode = $this->request->data['User']['coupon_code'];
                            if ($shareuserdetail['User']['is_emailnotification'] == 1) {
                                $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                                $emailData = str_replace('{COUPON}', $couponcode, $emailData);

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
                                $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                                $smsData = str_replace('{COUPON}', $couponcode, $smsData);
                                $smsData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $smsData);
                                $smsData = str_replace('{STORE_PHONE}', $storePhone, $smsData);
                                $message = $smsData;
                                $mob = $shareuserdetail['CountryCode']['code'] . "" . str_replace(array('(', ')', ' ', '-'), '', $shareuserdetail['User']['phone']);
                                $this->Common->sendSmsNotification($mob, $message);
                            }
                        }
                    } else {
                        $alreadyShared++;
                    }
                }
            }
            $message = "";
            if ($newshared) {
                $message.="Coupon has been shared to " . $newshared . " Users <br>";
            }
            if ($alreadyShared) {
                $message.="Coupon has already shared to " . $alreadyShared . " Users";
            }
            $this->Session->setFlash(__($message), 'alert_success');
            $this->redirect($this->referer());
        }
        $this->loadModel('User');
        $criteria = array('User.store_id' => $storeId, 'User.role_id' => 4, 'User.is_deleted' => 0, 'User.is_active' => 1);
        $this->paginate = array('fields' => array('User.fname', 'User.lname', 'User.email', 'User.id', 'User.created', 'User.store_id'), 'conditions' => array($criteria), 'order' => array('User.created' => 'DESC'), 'limit' => 10);
        $list = $this->paginate('User');
        $this->set(compact('list', 'couponId'));
    }

    public function uploadfile() {
        $this->layout = 'hq_dashboard';
        $this->loadModel('Store');

        if (!empty($this->request->data)) {
            $tmp = $this->request->data;
            if ($tmp['Coupon']['file']['error'] == 4) {
                $this->Session->setFlash(__('Your file contains error. Please retry uploading.'), 'alert_failed');
                $this->redirect($this->here);
            }
            $valid = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            if (!in_array($tmp['Coupon']['file']['type'], $valid)) {
                $this->Session->setFlash(__('You can only upload Excel file.'), 'alert_failed');
            } else if ($tmp['Coupon']['file']['error'] != 0) {
                $this->Session->setFlash(__('The file you uploaded contains errors.'), 'alert_failed');
            } else if ($tmp['Coupon']['file']['size'] > 20000000) {
                $this->Session->setFlash(__('The file size must be Max 20MB'), 'alert_failed');
            } else {
                ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large
                App::import('Vendor', 'PHPExcel');
                $objPHPExcel = new PHPExcel;
                $objPHPExcel = PHPExcel_IOFactory::load($tmp['Coupon']['file']['tmp_name']);
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                $real_data = array_values($sheetData);
                $merchantId = $this->Session->read('merchantId');
                $storeId = $this->request->data['Coupon']['store_id'];
                if ($storeId == "All") {
                    $storeId = $this->Store->find('list', array('fields' => array('id'), 'conditions' => array('Store.merchant_id' => $merchantId)));
                    $i = $this->couponForMultipleStore($storeId, $real_data, $merchantId);
                } else {
                    $i = $this->saveCoupon($real_data, $storeId, $merchantId);
                }
                $this->Session->setFlash(__($i . ' ' . 'Hq Coupon has been saved'), 'alert_success');
                $this->redirect(array("controller" => "hqcoupons", "action" => "index"));
            }
        }
    }

    public function couponForMultipleStore($storeIds = array(), $real_data = array(), $merchantId = null) {
        $i = 0;
        if (!empty($storeIds)) {
            foreach ($storeIds as $storeId) {
                $k = $this->saveCoupon($real_data, $storeId, $merchantId);
                if (is_numeric($k)) {
                    $i = $i + $k;
                }
            }
        }
        return $i;
    }

    public function saveCoupon($real_data = null, $storeId, $merchantId) {
        $i = 0;
        foreach ($real_data as $key => $row) {

            $row['A'] = trim($row['A']);
            if (!empty($row['A'])) {
                $isUniqueId = $this->Coupon->checkCouponWithId($row['A']);
                if (!empty($isUniqueId) && $isUniqueId['Coupon']['store_id'] != $storeId) {
                    continue;
                }
            }
            $row = $this->Common->trimValue($row);
            if ($key > 0) {
                if (!empty($row['B']) && !empty($row['C']) && !empty($row['D']) && !empty($row['E']) && !empty($row['F'])) {
                    $row['B'] = trim($row['B']);
                    $row['C'] = trim($row['C']);
                    if (!empty($row['A'])) {
                        $isUniqueName = $this->Coupon->checkCouponUniqueName($row['B'], $storeId, $row['A']);
                    } else {
                        $isUniqueName = $this->Coupon->checkCouponUniqueName($row['B'], $storeId);
                    }
                    if ($isUniqueName) {
                        if (!empty($row['A'])) {
                            $isUniqueCode = $this->Coupon->checkCouponUniqueCode($row['C'], $storeId, $row['A']);
                        } else {
                            $isUniqueCode = $this->Coupon->checkCouponUniqueCode($row['C'], $storeId);
                        }
                        if ($isUniqueCode) {
                            $coupondata['store_id'] = $storeId;
                            $coupondata['merchant_id'] = $merchantId;
                            $coupondata['name'] = $row['B'];
                            $coupondata['coupon_code'] = $row['C'];
                            if (!empty($row['D'])) {
                                $coupondata['discount_type'] = $row['D'];
                            } else {
                                $coupondata['discount_type'] = 1;
                            }

                            $coupondata['discount'] = $row['E'];

                            if (!empty($row['F'])) {
                                $coupondata['number_can_use'] = $row['F'];
                            } else {
                                $coupondata['number_can_use'] = 1;
                            }
                            if (!empty($row['G'])) {
                                $coupondata['promotional_message'] = $row['G'];
                            }
                            if (!empty($row['H'])) {
                                $coupondata['is_active'] = $row['H'];
                            } else {
                                $coupondata['is_active'] = 0;
                            }

                            if (!empty($row['A'])) {
                                $coupondata['id'] = $row['A'];
                            } else {
                                $coupondata['id'] = "";
                                $this->Coupon->create();
                            }
                            $this->Coupon->saveCoupon($coupondata);
                            $i++;
                        }
                    }
                }
            }
        }

        return $i;
    }

    public function download($store_id = null) {
        if (!empty($store_id)) {
            $this->Coupon->bindModel(array(
                'belongsTo' => array(
                    'Store' => array(
                        'className' => 'Store',
                        'foreignKey' => 'store_id',
                        'fields' => array(
                            'id', 'store_name'
                        )
                    ))), false);

            if ($store_id == "All") {
                $merchantId = $this->Session->read('merchantId');
                $result = $this->Coupon->fetchCouponListByMerchantId($merchantId);
            } else {
                $storeId = $store_id;
                $result = $this->Coupon->fetchCouponList($storeId);
            }
        }

        Configure::write('debug', 0);
        App::import('Vendor', 'PHPExcel');
        $objPHPExcel = new PHPExcel;
//        $styleArray2 = array(
//            'font' => array('name' => 'Arial', 'size' => '10', 'color' => array('rgb' => '444555'), 'bold' => true),
//            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'D6D6D6'))
//        );
        $styleArray = array(
            'font' => array(
                'name' => 'Arial',
                'size' => '10',
                'color' => array('rgb' => 'ffffff'),
                'bold' => true,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '0295C9'),
            ),
        );
        ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large
        $filename = 'Hq_Coupon_' . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('Coupon');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Title');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Coupon Code');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Discount Type');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Discount');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Number Can Use');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Promotional Message');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Active');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Store Name');

        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleArray);

        $i = 2;
        foreach ($result as $data) {
            $data = $this->Common->trimValue($data);
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", $data['Coupon']['id']);
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", $data['Coupon']['name']);
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", $data['Coupon']['coupon_code']);
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", $data['Coupon']['discount_type']);
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", $data['Coupon']['discount']);
            $objPHPExcel->getActiveSheet()->setCellValue("F$i", $data['Coupon']['number_can_use']);
            $objPHPExcel->getActiveSheet()->setCellValue("G$i", $data['Coupon']['promotional_message']);
            $objPHPExcel->getActiveSheet()->setCellValue("H$i", $data['Coupon']['is_active']);
            $objPHPExcel->getActiveSheet()->setCellValue("I$i", $data['Store']['store_name']);
            $i++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('Coupon');
            if (!empty($_GET['storeId'])) {
                $storeID = $_GET['storeId'];
            } else {
                $merchant_id = $this->Session->read('merchantId');
                $storeID = $this->Store->getAllStoresByMerchantId($merchant_id);
            }

            $searchData = $this->Coupon->find('all', array('fields' => array('Coupon.name', 'Coupon.coupon_code'), 'conditions' => array('OR' => array('Coupon.name LIKE' => '%' . $_GET['term'] . '%', 'Coupon.coupon_code LIKE' => '%' . $_GET['term'] . '%'), 'Coupon.is_deleted' => 0, 'Coupon.store_id' => $storeID)));
            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    $new_array[] = array('label' => $val['Coupon']['name'], 'value' => $val['Coupon']['coupon_code'], 'desc' => $val['Coupon']['name'] . " - " . $val['Coupon']['coupon_code']);
                };
            }

            echo json_encode($new_array);
        } else {
            exit;
        }
    }

    /* ------------------------------------------------
      Function name:deleteMultipleCoupons()
      Description:Delete multiple coupons
      created:02/08/2017
      ----------------------------------------------------- */

    public function deleteMultipleCoupons() {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        if ($this->request->is(array('post')) && !empty($this->request->data['Coupon']['id'])) {
            $filter_array = array_filter($this->request->data['Coupon']['id']);
            if ($this->Common->deleteMultipleRecords($filter_array, 'Coupon')) {
                $msg = "Coupons deleted successfully.";
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
