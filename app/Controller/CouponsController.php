<?php

App::uses('StoreAppController', 'Controller');

class CouponsController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common', 'Paginator', 'Webservice');
    public $helper = array('Encryption', 'Common', 'Paginator');
    public $uses = array('Coupon', 'UserCoupon');

    public function beforeFilter() {
        //Check permission for Admin User 
        parent::beforeFilter();
        $adminfunctions = array('download', 'addCoupon', 'index', 'activateCoupon', 'deleteCoupon', 'editCoupon', 'shareCoupon', 'uploadfile');
        if (in_array($this->params['action'], $adminfunctions) && !$this->Common->checkPermissionByaction($this->params['controller'])) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        //Restrict admin user to access Frontend Methods.
        $roleId = $this->Session->read('Auth.Admin.role_id');
        if ($roleId && $roleId != 3 && in_array($this->params['action'], $adminfunctions)) {
            $this->InvalidLogin($roleId);
        }
    }

    /* ------------------------------------------------
      Function name:addCoupon()
      Description:Add New Coupon
      created:8/8/2015
      ----------------------------------------------------- */

    public function addCoupon() {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        if ($this->request->is(array('post', 'put')) && !empty($this->request->data['Coupon']['coupon_code'])) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $couponTitle = trim($this->request->data['Coupon']['name']);
            $couponCode = trim($this->request->data['Coupon']['coupon_code']);
            $isUniqueName = $this->Coupon->checkCouponUniqueName($couponTitle, $storeId);
            $isUniqueCode = $this->Coupon->checkCouponUniqueCode($couponCode, $storeId);
            if ($isUniqueName) {
                if ($isUniqueCode) {
                    $response = $this->Common->uploadMenuItemImages($this->request->data['Coupon']['image'], '/Coupon-Image/', $storeId, 480, 320);
                    if (!$response['status']) {
                        $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    } else {
                        if (!empty($this->request->data['Coupon']['days'])) {
                            $this->request->data['Coupon']['days'] = implode(",", array_keys($this->request->data['Coupon']['days']));
                        } else {
                            $this->request->data['Coupon']['days'] = '';
                        }
                        $coupondata['image'] = $response['imagename'];
                        $coupondata['store_id'] = $storeId;
                        $coupondata['merchant_id'] = $merchantId;
                        $coupondata['name'] = trim($this->request->data['Coupon']['name']);
                        $coupondata['start_date'] = $this->Dateform->formatDate($this->request->data['Coupon']['start_date']);
                        $coupondata['end_date'] = $this->Dateform->formatDate($this->request->data['Coupon']['end_date']);
                        $coupondata['coupon_code'] = trim($this->request->data['Coupon']['coupon_code']);
                        $coupondata['number_can_use'] = $this->request->data['Coupon']['number_can_use'];
                        $coupondata['discount_type'] = $this->request->data['Coupon']['discount_type'];
                        $coupondata['discount'] = $this->request->data['Coupon']['discount'];
                        $coupondata['is_active'] = $this->request->data['Coupon']['is_active'];
                        $coupondata['allow_time'] = $this->request->data['Coupon']['allow_time'];
                        $coupondata['start_time'] = $this->request->data['Coupon']['start_time'];
                        $coupondata['end_time'] = $this->request->data['Coupon']['end_time'];
                        $coupondata['days'] = $this->request->data['Coupon']['days'];
                        if (isset($this->request->data['Coupon']['promotional_message']) && $this->request->data['Coupon']['promotional_message']) {
                            $coupondata['promotional_message'] = trim($this->request->data['Coupon']['promotional_message']);
                        }
                        $this->Coupon->create();
                        $this->Coupon->saveCoupon($coupondata);
                        $this->request->data = '';
                        $this->Session->setFlash(__("Coupon Successfully Added"), 'alert_success');
                    }
                } else {
                    $this->Session->setFlash(__("Coupon Code Already exists"), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Coupon Title Already exists"), 'alert_failed');
            }
        }
        $start = "00:00";
        $end = "24:00";
        $timeRange = $this->Common->getStoreTimeAdmin($start, $end);
        $this->set('timeOptions', $timeRange);
        $this->_coupanList();
        //For Dealdata
        $this->loadModel('StoreDeals');
        $storeDealData = $this->StoreDeals->findByStoreId($storeId);
        $this->set('storeDealData', $storeDealData);
        $this->request->data = "";
    }

    /* ------------------------------------------------
      Function name:index()
      Description:Display the list of coupon
      created:8/8/2015
      ----------------------------------------------------- */

    private function _coupanList($clearAction = null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $storeID = $this->Session->read('admin_store_id');
        $criteria = "Coupon.store_id =$storeID AND Coupon.is_deleted=0";
        if ($this->Session->read('CouponSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('CouponSearchData'), true);
        } else {
            $this->Session->delete('CouponSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('CouponSearchData', json_encode($this->request->data));
            if (isset($this->request->data['Coupon']['isActive']) && $this->request->data['Coupon']['isActive'] != "") {
                $active = trim($this->request->data['Coupon']['isActive']);
                $criteria .= " AND (Coupon.is_active =$active)";
            }
            if (!empty($this->request->data['Coupon']['search'])) {
                $search = trim($this->request->data['Coupon']['search']);
                $criteria .= " AND (Coupon.coupon_code LIKE '%" . $search . "%')";
            }
        }
        $this->paginate = array('conditions' => array($criteria), 'order' => array('Coupon.created' => 'DESC'));
        $coupondetail = $this->paginate('Coupon');
        $this->set('list', $coupondetail);
    }

    /* ------------------------------------------------
      Function name:activateCoupon()
      Description:Active/deactive Coupon
      created:08/8/2015
      ----------------------------------------------------- */

    public function activateCoupon($EncryptCouponID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Coupon']['store_id'] = $this->Session->read('admin_store_id');
        $data['Coupon']['id'] = $this->Encryption->decode($EncryptCouponID);
        $data['Coupon']['is_active'] = $status;
        if ($this->Coupon->saveCoupon($data)) {
            if ($status) {
                $SuccessMsg = "Coupon Activated";
                $this->Session->setFlash(__($SuccessMsg), 'alert_success');
                $this->redirect(array('controller' => 'coupons', 'action' => 'editCoupon/' . $EncryptCouponID . '#CouponStartDate'));
            } else {
                $SuccessMsg = "Coupon Deactivated and Coupon will not get Display in Menu List";
                $this->Session->setFlash(__($SuccessMsg), 'alert_success');
                $this->redirect(array('controller' => 'coupons', 'action' => 'addCoupon'));
            }
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'coupons', 'action' => 'addCoupon'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteCoupon()
      Description:Delete Coupon
      created:08/8/2015
      ----------------------------------------------------- */

    public function deleteCoupon($EncryptCouponID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Coupon']['store_id'] = $this->Session->read('admin_store_id');
        $data['Coupon']['id'] = $this->Encryption->decode($EncryptCouponID);
        $data['Coupon']['is_deleted'] = 1;
        if ($this->Coupon->saveCoupon($data)) {
            $this->Session->setFlash(__("Coupon deleted"), 'alert_success');
            $this->redirect(array('controller' => 'coupons', 'action' => 'addCoupon'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'coupons', 'action' => 'addCoupon'));
        }
    }

    /* ------------------------------------------------
      Function name:editCoupon()
      Description:Edit Coupon
      created:08/8/2015
      ----------------------------------------------------- */

    public function editCoupon($EncryptCouponID = null) {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $data['Coupon']['id'] = $this->Encryption->decode($EncryptCouponID);
        $this->loadModel('Coupon');
        $couponDetail = $this->Coupon->getCouponDetail($data['Coupon']['id'], $storeId);
        if ($this->request->is(array('post', 'put')) && $this->request->data) {
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
                    //Item Data
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
                    $coupondata['start_date'] = $this->Dateform->formatDate($this->request->data['Coupon']['start_date']);
                    $coupondata['end_date'] = $this->Dateform->formatDate($this->request->data['Coupon']['end_date']);

                    $coupondata['name'] = trim($this->request->data['Coupon']['name']);
                    //$coupondata['coupon_code'] = trim($this->request->data['Coupon']['coupon_code']);
                    $coupondata['number_can_use'] = $this->request->data['Coupon']['number_can_use'];
                    $coupondata['discount_type'] = $this->request->data['Coupon']['discount_type'];
                    $coupondata['discount'] = $this->request->data['Coupon']['discount'];
                    $coupondata['is_active'] = $this->request->data['Coupon']['is_active'];
                    $coupondata['allow_time'] = $this->request->data['Coupon']['allow_time'];
                    $coupondata['start_time'] = $this->request->data['Coupon']['start_time'];
                    $coupondata['end_time'] = $this->request->data['Coupon']['end_time'];
                    $coupondata['days'] = $this->request->data['Coupon']['days'];
                    if (isset($this->request->data['Coupon']['promotional_message']) && $this->request->data['Coupon']['promotional_message']) {
                        $coupondata['promotional_message'] = trim($this->request->data['Coupon']['promotional_message']);
                    }
                    $this->Coupon->create();
                    $this->Coupon->saveCoupon($coupondata);
                    $this->Session->setFlash(__("Coupon Successfully Updated"), 'alert_success');
                    $this->redirect(array('controller' => 'coupons', 'action' => 'addCoupon'));
                }
//                } else {
//                    $this->Session->setFlash(__("Coupon Code Already exists"), 'alert_failed');
//                }
            } else {
                $this->Session->setFlash(__("Coupon Title Already exists"), 'alert_failed');
            }
        }
        $start = "00:00";
        $end = "24:00";
        $timeRange = $this->Common->getStoreTimeAdmin($start, $end);
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
        $this->layout = "admin_dashboard";
        $data['Coupon']['store_id'] = $this->Session->read('admin_store_id');
        $data['Coupon']['id'] = $this->Encryption->decode($EncryptCouponID);
        $data['Coupon']['image'] = '';
        if ($this->Coupon->saveCoupon($data)) {
            $this->Session->setFlash(__("Coupon Image deleted"), 'alert_success');
            $this->redirect(array('controller' => 'coupons', 'action' => 'editCoupon', $EncryptCouponID));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'coupons', 'action' => 'editCoupon', $EncryptCouponID));
        }
    }

    /* ------------------------------------------------
      Function name:shareCoupon()
      Description:Share the coupon to customers
      created:08/8/2015
      ----------------------------------------------------- */

    public function shareCoupon($EncryptCouponID = null) {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        if (!empty($_GET['couponId'])) {
            $EncryptCouponID = $_GET['couponId'];
        }
        if ($EncryptCouponID) {
            $data['Coupon']['id'] = $this->Encryption->decode($EncryptCouponID);
        } else {
            $data['Coupon']['id'] = $this->request->data['User']['coupon_id'];
        }
        if ($this->request->is(array('post', 'put'))) {
            $this->request->data['User']['id'] = array_filter($this->request->data['User']['id']);
            $this->loadModel('Store');
            $storeEmail = $this->Store->fetchStoreDetail($storeId);
            $alreadyShared = 0;
            $newshared = 0;
            //prx($this->request->data['User']['id']);
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
//                            if ($couponDetail['Coupon']['promotional_message']) {
//                                $smsData = nl2br($couponDetail['Coupon']['promotional_message']);
//                            } else {
                            $smsData = $emailSuccess['EmailTemplate']['sms_template'];
//                            }
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

        $couponId = $this->Coupon->getCouponDetail($data['Coupon']['id'], $storeId);
        $this->loadModel('User');
        $criteria = array('User.merchant_id' => $merchantId, 'User.role_id' => array(4, 5), 'User.is_deleted' => 0, 'User.is_active' => 1);
        $this->paginate = array('fields' => array('User.fname', 'User.lname', 'User.email', 'User.id', 'User.created'), 'conditions' => array($criteria), 'order' => array('User.created' => 'DESC'));
        $list = $this->paginate('User');
        $this->set(compact('list', 'couponId'));
    }

    /* ------------------------------------------------
      Function name:myCoupons()
      Description:List of User Coupons
      created:12/8/2015
      ----------------------------------------------------- */

    public function myCoupons($encrypted_storeId = null, $encrypted_merchantId = null) {
        $this->layout = $this->store_inner_pages;
        $decrypt_userId = AuthComponent::User('id');
        $this->UserCoupon->bindModel(array('belongsTo' => array('Coupon')), false);
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->UserCoupon->bindModel(array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('id', 'store_name'),
                    'type' => 'INNER',
                    'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1)
                ))
                ), false);
        $value = "";
        if (isset($this->params->pass[0]) && !empty($this->params->pass[0]) && $this->params->pass[0] == 'clear') {
            $this->Session->delete('MycouponsSearchData');
        }

        if ($this->Session->read('MycouponsSearchData') && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('MycouponsSearchData'), true);
            $value = $this->request->data['User']['keyword'];
            $encrypted_storeId = $this->request->data['Merchant']['store_id'];
            $encrypted_storeId = $this->Encryption->encode($encrypted_storeId);
        } else {
            $this->Session->delete('MycouponsSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }

        if (!empty($this->request->data)) {
            $this->Session->write('MycouponsSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['User']['keyword'])) {
                $value = trim($this->request->data['User']['keyword']);
                $conditions1 = array("UserCoupon.coupon_code LIKE '%" . $value . "%'");
            } else {
                $conditions1 = array();
            }
            $merchantId = $this->Session->read('merchant_id');
            $decrypt_storeId = $this->request->data['Merchant']['store_id'];
            $today = $this->Webservice->getcurrentTime($decrypt_storeId, 2);
            $decrypt_merchantId = $merchantId;
            $encrypted_storeId = $this->Encryption->encode($this->request->data['Merchant']['store_id']);
            if (!empty($encrypted_storeId)) {
                $conditions = array('UserCoupon.merchant_id' => $decrypt_merchantId, 'UserCoupon.store_id' => $decrypt_storeId, 'UserCoupon.user_id' => $decrypt_userId, 'UserCoupon.is_active' => 1, 'UserCoupon.is_deleted' => 0, 'Coupon.is_active' => 1, 'Coupon.is_deleted' => 0);
                $conditions = array_merge($conditions1, $conditions);
                $this->paginate = array(
                    'conditions' => $conditions,
                    'limit' => 9
                );
            } else {
                $conditions = array('UserCoupon.merchant_id' => $decrypt_merchantId, 'UserCoupon.user_id' => $decrypt_userId, 'UserCoupon.is_active' => 1, 'UserCoupon.is_deleted' => 0, 'Coupon.is_active' => 1, 'Coupon.is_deleted' => 0);
                $conditions = array_merge($conditions1, $conditions);
                $this->paginate = array('conditions' => $conditions, 'limit' => 9);
            }
        } else {
            if (!empty($encrypted_merchantId)) {
                $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
            } else {
                $decrypt_merchantId = $this->Session->read('merchant_id');
            }
            $this->paginate = array(
                'conditions' => array('UserCoupon.merchant_id' => $decrypt_merchantId, 'UserCoupon.user_id' => $decrypt_userId, 'UserCoupon.is_active' => 1, 'UserCoupon.is_deleted' => 0, 'Coupon.is_active' => 1, 'Coupon.is_deleted' => 0),
                'limit' => 10
            );
        }
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId', 'encrypted_userId'));
        $myCoupons = $this->paginate('UserCoupon');
        $this->set(compact('myCoupons'));
        $this->set("keyword", $value);
    }

    /* ------------------------------------------------
      Function name:deleteUserCoupon()
      Description:Delete User Coupon
      created:12/8/2015
      ----------------------------------------------------- */

    public function deleteUserCoupon($encrypted_storeId = null, $encrypted_merchantId = null, /* $encrypted_userId=null, */ $encrypted_userCouponId = null) {
        $this->autoRender = false;
        $data['UserCoupon']['id'] = $this->Encryption->decode($encrypted_userCouponId);
        $data['UserCoupon']['is_deleted'] = 1;
        if ($this->UserCoupon->saveUserCoupon($data)) {
            $this->Session->setFlash(__("Coupon has been deleted"), 'flash_success');
            $this->redirect(array('controller' => 'Coupons', 'action' => 'myCoupons', $encrypted_storeId, $encrypted_merchantId/* ,$encrypted_userId */));
        } else {
            $this->Session->setFlash(__("Some problem has been occured"), 'flash_error');
            $this->redirect(array('controller' => 'Coupons', 'action' => 'myCoupons', $encrypted_storeId, $encrypted_merchantId/* ,$encrypted_userId */));
        }
    }

    public function uploadfile() {
        $this->layout = 'admin_dashboard';
        $this->loadModel('Store');
        if (!empty($this->request->data)) {
            $tmp = $this->request->data;
            if ($tmp['Coupon']['file']['error'] == 4) {
                $this->Session->setFlash(__('Your file contains error. Please retry uploading.'), 'alert_failed');
                $this->redirect($this->referer());
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
                $i = 0;
                foreach ($real_data as $key => $row) {
                    $row = $this->Common->trimValue($row);
                    if ($key > 0) {
                        if (!empty($row['B']) && !empty($row['C']) && !empty($row['D']) && !empty($row['E']) && !empty($row['F'])) {
                            $storeId = $this->Session->read('admin_store_id');
                            $merchantId = $this->Session->read('admin_merchant_id');
                            if (!empty($storeId)) {
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
                }
                $this->Session->setFlash(__($i . ' ' . 'Coupon has been saved'), 'alert_success');
                $this->redirect(array('controller' => 'coupons', 'action' => 'addCoupon'));
            }
        }
    }

    public function download() {
        $storeId = $this->Session->read('admin_store_id');
        $result = $this->Coupon->fetchCouponList($storeId);
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
        $filename = 'Coupon_' . date("Y-m-d") . ".xls"; //create a file
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

        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);

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
            $storeID = $this->Session->read('admin_store_id');
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

    public function couponUsedList($encryptedCouponId = null) {
        $this->layout = "admin_dashboard";
        $this->loadModel('Coupon');
        $coupon_id = $this->Encryption->decode($encryptedCouponId);
        $merchantId = $this->Session->read('admin_merchant_id');
        $data = $this->Coupon->find('first', array('conditions' => array('Coupon.id' => $coupon_id, 'Coupon.merchant_id' => $merchantId), 'fields' => array('coupon_code', 'start_date', 'end_date')));
        $orderData = $userIds = $orderDataAs = array();
        if (!empty($data)) {
            $couponName = $data['Coupon']['coupon_code'];
            $this->loadModel('Order');
            $this->Order->bindModel(
                    array(
                'belongsTo' => array(
                    'User' => array(
                        'className' => 'User',
                        'foreignKey' => 'user_id',
                        'fields' => array('userName', 'email')
                    ),
                    'DeliveryAddress' => array(
                        'className' => 'DeliveryAddress',
                        'foreignKey' => 'delivery_address_id',
                    ),
                )
                    ), false
            );

            $criteria = " (Order.created BETWEEN '" . $data['Coupon']['start_date'] . "' AND '" . $data['Coupon']['end_date'] . "')";
            $criteria.= " AND LOWER(Order.coupon_code) ='" . strtolower($couponName) . "'";
            //$this->paginate = array('fields' => array('count(Order.user_id) as coupon_count', 'Order.coupon_code', 'Order.order_number', 'User.fname', 'User.lname', 'User.email', 'Order.user_id'), 'conditions' => $criteria, 'group' => array('Order.user_id'));
            $orderDatas = $this->Order->find('all', array('fields' => array('Order.coupon_code', 'Order.order_number', 'Order.delivery_address_id', 'User.fname', 'User.lname', 'User.email', 'Order.user_id', 'DeliveryAddress.*'), 'conditions' => $criteria));
            //pr($orderDatas);
            if (!empty($orderDatas)) {
                $guestEmail = array();
                foreach ($orderDatas as $key => $orderDataAs) {
                    if (!empty($orderDataAs)) {
                        if ($orderDataAs['Order']['user_id'] == 0) {
                            $index = $orderDataAs['DeliveryAddress']['email'];               
                            if (in_array($orderDataAs['DeliveryAddress']['email'], $guestEmail)) {
                                $orderData[$index]['count'] = $orderData[$index]['count'] + 1;
                            } else {
                                $orderData[$index]['count'] = 1;
                                $guestEmail[$key] = $orderDataAs['DeliveryAddress']['email'];
                            }
                            $orderData[$index]['user_id'] = 0;
                            $orderData[$index]['coupon_code'] = $orderDataAs['Order']['coupon_code'];                            

                            $orderData[$index]['name'] = $orderDataAs['DeliveryAddress']['name_on_bell'];
                            $orderData[$index]['email'] = $orderDataAs['DeliveryAddress']['email'];
                        } else {
                            $index = $orderDataAs['User']['email'];
                            if (in_array($orderDataAs['Order']['user_id'], $userIds)) {
                                $orderData[$index]['count'] = $orderData[$index]['count'] + 1;
                            } else {
                                $orderData[$index]['count'] = 1;
                            }
                            $orderData[$index]['user_id'] = $orderDataAs['Order']['user_id'];
                            $orderData[$index]['coupon_code'] = $orderDataAs['Order']['coupon_code'];
                            $orderData[$index]['name'] = $orderDataAs['User']['fname'] . ' ' . $orderDataAs['User']['lname'];
                            $orderData[$index]['email'] = $orderDataAs['User']['email'];
                        }
                        $userIds[$index] = $orderDataAs['Order']['user_id'];
                    }
                }
            }
        }
        $this->set('list', $orderData);
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
