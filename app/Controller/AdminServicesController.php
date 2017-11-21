<?php

App::uses("Sanitize", "Utility");
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('ServicesAppController', 'Controller');

class AdminServicesController extends ServicesAppController {

    //public $name = 'AdminServices';
    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Paginator', 'Common', 'Dateform', 'Webservice','Webservicetest');
    public $helper = array('Encryption', 'Paginator', 'Form', 'DateformHelper', 'Common');
   public $uses = array('User', 'StoreGallery', 'Store', 'StoreBreak', 'StoreAvailability', 'StoreHoliday', 'Category', 'Tab', 'Permission', 'StoreTheme', 'Merchant', 'StoreTax', 'StoreFont', 'Booking', 'CountryCode', 'Type', 'ItemType', 'ItemPrice', 'Topping', 'Item', 'SubPreference', 'Size', 'StoreTax', 'Category', 'AddonSize', 'Offer', 'OfferDetail', 'SubPreferencePrice', 'ToppingPrice', 'DeliveryAddress', 'IntervalPrice', 'Interval', 'UserCoupon', 'Coupon', 'ItemDefaultTopping', 'ItemOffer','OrderItem','OrderPreference','orderSave','OrderTopping','OrderOffer','OrderItem','Favorite','Order','UserCoupon','Coupon','StoreReview','OrderPayment','OrderTopping','OrderPreference','MobileOrder','StoreReviewImage','TimeZone','OrderItemFree','UserDevice','AlarmTime','NotificationConfiguration');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->layout = null;
        $this->autoRender = false;
        $this->Auth->allow('getStoresList', 'login','getStoresNames', 'forgotPassword', 'storeDashboard', 'orderCounts','orderList','orderDetail','updateOrderStatus','getStoresInfo','updateStoresInfo','reservationList','reservationCount','updateReservation','imageGallery','uploadGalleryImages','removeGalleryImage','updateGalleryStatus','storeReviewListing','searchReview','searchOrder','searchReservation','reservationByMonthList','removeStoreReview','couponsList','addCoupon','editCoupon','removeCoupon','updateCouponStatus','extendedOffersList','categoryList','addExtendedOffer','updateExtendedOfferStatus','removeExtendedOffer','editExtendedOffer','promosList','itemsList','addPromo','editPromo','usersList','updatePromoStatus','removePromo','searchPromo','usersList','couponShare','getAlarmTime','getNotificationDetail','updateNotificationDetail','orderNotification','hitSocket','ios_push_notification','bookingNotification','logOut');
        $target_dir = WWW_ROOT . "/webserviceAdminLog";
        if (!file_exists($target_dir)) {
            (new Folder($target_dir, true, 0777));
        }
	ini_set("memory_limit","256M");
    }

    /*     * *
     *
     * @ Description this function is used for sending msg back to user
     * @ Params void.
     * @ Return void.
     * @ Created Date 23-08-2016
     * @ Updated Date 
     * @ Created By Smartdata.
     * @ Updated By Smartdata.
     *
     * * */

    private function json_message($Response = 0, $Message = null, $Data = array()) {
        $ResponseArr = array(
            'message' => $Message,
            'response' => $Response,
            'data' => $Data
        );
        //pr($ResponseArr);die;

        return json_encode($ResponseArr, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    
    /*     * ******************************************************************************************
      @Function Name : login
      @Description   : this function is used for login based on store ID
      @Author        : SmartData
      created:23/11/2016
     * ****************************************************************************************** */

     public function login() {
        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        $this->Webservice->webserviceAdminLog($requestBody, "login_request.txt", $headers);
        //$requestBody = '{"email":"ekansh.working@gmail.com","password": "Smartdata123","device_type":"","device_token":"","device_id":""}';
        //$requestBody = '{"device_token":"98275827352758","email":"ekansh.working@gmail.com","device_type":"iOS","password":"Smartdata321"}';
        //$requestBody = '{"email":"rjsaini90@yopmail.com","password":"Smartdata123"}';
        //$headers['merchant_id'] = 85;
        $responsedata = array();
        $requestBody = json_decode($requestBody, true);
        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantCheck = $this->Merchant->find('first', array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantCheck)) {
                if (isset($requestBody['email']) && !empty($requestBody['email']) && isset($requestBody['password']) && !empty($requestBody['password'])) {
                    $password = AuthComponent::password($requestBody['password']);

                    $this->Store->unBindModel(array('belongsTo' => array('StoreTheme')), false);
                    $this->Store->unBindModel(array('belongsTo' => array('StoreFont')), false);
                    $this->Store->unBindModel(array('hasOne' => array('SocialMedia')), false);
                    $this->Store->unBindModel(array('hasMany' => array('StoreContent')), false);
                    $this->Store->unBindModel(array('hasMany' => array('StoreGallery')), false);

                    $this->User->bindModel(array(
                        'hasMany' => array(
                            'Permission' => array(
                                'className' => 'Permission',
                                'foreignKey' => 'user_id',
                                'type' => 'INNER',
                                'conditions' => array('Permission.is_active' => 1, 'Permission.is_deleted' => 0, 'Permission.tab_id' => array(10, 12, 13, 14, 16, 47, 3)),
                                'fields' => array('id', 'tab_id')
                            ),
                        )
                    ));
                    $this->User->bindModel(array(
                        'belongsTo' => array(
                            'Store' => array(
                                'className' => 'Store',
                                'foreignKey' => 'store_id',
                                'type' => 'INNER',
                                'conditions' => array('Store.is_active' => 1, 'Store.is_deleted' => 0),
                                'fields' => array('id', 'store_name', 'address', 'city', 'state', 'store_logo', 'phone', 'zipcode', 'merchant_id', 'notification_number', 'notification_email', 'notification_email','display_email','display_fax')
                            ),
                        )
                    ));
                    $user = $this->User->find("first", array('recursive' => 2, "conditions" => array("User.email" => $requestBody['email'], "User.password" => $password, "User.role_id" => 3, "User.merchant_id" => $merchant_id, "User.is_deleted" => 0), 'fields' => array('store_id', 'email', 'fname', 'id', 'lname', 'phone', 'dateOfBirth', 'country_code_id', 'is_deleted', 'is_active', 'is_newsletter', 'is_smsnotification', 'is_emailnotification')));
                    //pr($user);
                    if (!empty($user)) {
                        if ($user['User']['is_active'] == 1) {
                                if ($this->Auth->login($user['User'])) {
                                     $protocol = 'http://';
                                    if (isset($_SERVER['HTTPS'])) {
                                        if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                                            $protocol = 'https://';
                                        }
                                    }
                                    $store_id=0;
                                       if(!empty($user['Store'])){
                                           $store_id=$user['Store']['id'];
                                       }
                                    $userNotificationDet=$this->NotificationConfiguration->find('first', array('conditions'=>array('NotificationConfiguration.user_id'=>$user['User']['id'], "NotificationConfiguration.store_id" => $store_id, "NotificationConfiguration.merchant_id" => $merchant_id, "NotificationConfiguration.is_active" => 1, "NotificationConfiguration.is_deleted" => 0)));
                                    if (!empty($userNotificationDet)) {
                                        $UserDeviceDet['notification_configuration_id'] = $userNotificationDet['NotificationConfiguration']['id'];
                                    } else {
                                        $getNotification['store_id'] = $store_id;
                                        $getNotification['user_id'] = $user['User']['id'];
                                        $getNotification['merchant_id'] = $merchant_id;
                                        $getNotification['order_notification'] = 0;
                                        $getNotification['show_in_notification'] = 0;
                                        $getNotification['sound'] = 0;
                                        $getNotification['badge_app'] = 0;
                                        $getNotification['add_alarm'] = 0;
                                        $getNotification['alarm_time_id'] = 1;
                                        $this->NotificationConfiguration->save($getNotification);
                                        $notification_configuration_id=$this->NotificationConfiguration->getLastInsertID();
                                        $UserDeviceDet['notification_configuration_id']=$notification_configuration_id;

                                    }

                                    $UserDeviceDet['user_id']=$user['User']['id'];
                                    $UserDeviceDet['store_id']=$user['User']['store_id'];
                                    $UserDeviceDet['merchant_id']=$merchant_id;
                                    if (isset($requestBody['device_token']) && !empty($requestBody['device_token'])) {
                                           $UserDeviceDet['device_token']=$requestBody['device_token'];
                                    }else{
                                          $UserDeviceDet['device_token']="";
                                    }
                                    if (isset($requestBody['device_type']) && !empty($requestBody['device_type'])) {
                                       $UserDeviceDet['device_type']=  strtolower(trim($requestBody['device_type']));
                                    }else{
                                          $UserDeviceDet['device_type']="";
                                    }

                                    if (isset($requestBody['device_id']) && !empty($requestBody['device_id'])) {
                                         $UserDeviceDet['device_id']=$requestBody['device_id'];
                                    }else{
                                          $UserDeviceDet['device_id']="";
                                    }

                                    $userDeviceDet=$this->UserDevice->find('all', array('conditions'=>array('UserDevice.user_id'=>$user['User']['id'], "UserDevice.store_id" => $store_id, "UserDevice.merchant_id" => $merchant_id, "UserDevice.device_type" => $UserDeviceDet['device_type'], "UserDevice.device_token" => $UserDeviceDet['device_token'], "UserDevice.is_active" => 1, "UserDevice.is_deleted" => 0)));


                                    if(empty($userDeviceDet)){
                                        $result=$this->UserDevice->save($UserDeviceDet);
                                    }

                                    $responsedata['message'] = "Success.";
                                    $responsedata['response'] = 1;
                                    $responsedata['id'] = $user['User']['id'];
                                    if ($user['User']['fname'] != "") {
                                        $responsedata['name'] = $user['User']['fname'];
                                        $responsedata['lname'] = $user['User']['lname'];
                                    }
                                    $responsedata['phone'] = $user['User']['phone'];
                                    $responsedata['email'] = $user['User']['email'];
                                    if (!empty($user['User']['dateOfBirth'])) {
                                        $responsedata['dateOfBirth'] = $user['User']['dateOfBirth'];
                                    } else {
                                        $responsedata['dateOfBirth'] = "";
                                    }
                                    if (!empty($user['User']['country_code_id'])) {
                                        $responsedata['country_code_id'] = $user['User']['country_code_id'];
                                    } else {
                                        $responsedata['country_code_id'] = " ";
                                    }

                                    $EncryptUserID = $this->Encryption->encode($responsedata['id']);
                                    $EncryptmerchantID = $this->Encryption->encode($merchant_id);
                                    $this->tokenGenerate($EncryptUserID, $EncryptmerchantID);
                                    $responsedata['token'] = $EncryptUserID;
                                    $countryCode = $this->CountryCode->find('first', array('conditions' => array('CountryCode.id' => $user['User']['country_code_id'])));
                                    if (!empty($countryCode)) {
                                        $responsedata['country_code_id'] = $countryCode['CountryCode']['code'];
                                    } else {
                                        $responsedata['country_code_id'] = "+1";
                                    }
                                    $locStoreList=array();
                                    //$locStoreList['Store']= $user['Store'];
                                    if(!empty($user['Store'])){
                                    if (!empty($user['Store']['id'])) {
                                        $locStoreList['Store']['id'] = $user['Store']['id'];
                                    } else {
                                        $locStoreList['Store']['id'] = "";
                                    }
                                    if (!empty($user['Store']['store_name'])) {
                                        $locStoreList['Store']['store_name'] = $user['Store']['store_name'];
                                    } else {
                                        $locStoreList['Store']['store_name'] = "";
                                    }
                                    if (!empty($user['Store']['address'])) {
                                        $locStoreList['Store']['address'] = $user['Store']['address'];
                                    } else {
                                        $locStoreList['Store']['address'] = "";
                                    }
                                    if (!empty($user['Store']['city'])) {
                                        $locStoreList['Store']['city'] = $user['Store']['city'];
                                    } else {
                                        $locStoreList['Store']['city'] = "";
                                    }
                                    if (!empty($user['Store']['state'])) {
                                        $locStoreList['Store']['state'] = $user['Store']['state'];
                                    } else {
                                        $locStoreList['Store']['state'] = "";
                                    }
                                     if (!empty($user['Store']['zipcode'])) {
                                        $locStoreList['Store']['zipcode'] = $user['Store']['zipcode'];
                                    } else {
                                        $locStoreList['Store']['zipcode'] = "";
                                    }
                                    if (!empty($user['Store']['merchant_id'])) {
                                        $locStoreList['Store']['merchant_id'] = $user['Store']['merchant_id'];
                                    } else {
                                        $locStoreList['Store']['merchant_id'] = "";
                                    }

                                    if (!empty($user['Store']['store_logo'])) {
                                        $locStoreList['Store']['store_logo'] = $protocol . $user['Store']['store_url'] . "/storeLogo/" . $user['Store']['store_logo'];
                                    } else {
                                        $locStoreList['Store']['store_logo'] = "";
                                    }
                                    if (!empty($user['Store']['display_email'])) {
                                        $locStoreList['Store']['display_email'] = $user['Store']['display_email'];
                                    } else {
                                       $locStoreList['Store']['display_email'] = "";
                                    }
                                    if (!empty($user['Store']['display_fax'])) {
                                       $locStoreList['Store']['display_fax'] = $user['Store']['display_fax'];
                                    } else {
                                        $locStoreList['Store']['display_fax'] = "";
                                    }


                                    $currentDateStore = $this->Webservice->getcurrentTime($user['Store']['id'], 1);
                                    if (!empty($currentDateStore)) {
                                        $currentDate = $currentDateStore;
                                    } else {
                                        $currentDate = date("Y-m-d H:i:s");
                                    }
                                    $dateTime = explode(" ", $currentDate);
                                    $current_date = $dateTime[0];
                                    $current_time = $dateTime[1];
                                    if (!empty($current_date)) {
                                        $locStoreList['Store']['current_date'] = $current_date;
                                        $locStoreList['Store']['current_time'] = $current_time;
                                    }
                                    if (!empty($user['Store']['phone'])) {
                                        $locStoreList['Store']['phone'] = $user['Store']['phone'];
                                    } else {
                                        $locStoreList['Store']['phone'] = "";
                                    }



                                    if (!empty($user['Store']['notification_number'])) {
                                        $locStoreList['Store']['notification_number'] = $user['Store']['notification_number'];
                                    } else {
                                        $locStoreList['Store']['notification_number'] = "";
                                    }
                                    if (!empty($user['Store']['notification_email'])) {
                                        $locStoreList['Store']['notification_email'] = $user['Store']['notification_email'];
                                    } else {
                                        $locStoreList['Store']['notification_email'] = "";
                                    }

                                    $locStoreList['Store']['isCouponAllow'] = FALSE;
                                    $locStoreList['Store']['isPromotionAllow'] = FALSE;
                                    $locStoreList['Store']['isOrderAllow'] = FALSE;
                                    $locStoreList['Store']['isReviewAllow'] = FALSE;
                                    $locStoreList['Store']['isBookingAllow'] = FALSE;
                                    $locStoreList['Store']['isItemOffersAllow'] = FALSE;
                                    $locStoreList['Store']['isConfigurationAllow'] = FALSE;

                                    if (!empty($user['Permission'])) {
                                        foreach ($user['Permission'] as $p => $permission) {
                                            if ($permission['tab_id'] == 10) {
                                                $locStoreList['Store']['isCouponAllow'] = TRUE;
                                            } elseif ($permission['tab_id'] == 12) {
                                                $locStoreList['Store']['isPromotionAllow'] = TRUE;
                                            } elseif ($permission['tab_id'] == 13) {
                                                $locStoreList['Store']['isOrderAllow'] = TRUE;
                                            } elseif ($permission['tab_id'] == 14) {
                                                $locStoreList['Store']['isReviewAllow'] = TRUE;
                                            } elseif ($permission['tab_id'] == 16) {
                                                $locStoreList['Store']['isBookingAllow'] = TRUE;
                                            } elseif ($permission['tab_id'] == 47) {
                                                $locStoreList['Store']['isItemOffersAllow'] = TRUE;
                                            } elseif ($permission['tab_id'] == 3) {
                                                $locStoreList['Store']['isConfigurationAllow'] = TRUE;
                                            }
                                        }
                                    }
                                }
                                        $responsedata['store']=$locStoreList['Store'];
                                    //pr($responsedata);
                                    return json_encode($responsedata);
                                } else {
                                    $responsedata['message'] = "Incorrect email or password.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }

                        } else {
                            $responsedata['message'] = "No active user found with this email/password.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "Incorrect email or password.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please enter email/password.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

    /*     * ******************************************************************************************
      @Function Name : getStoresNames
      @Description   : this function is used for get Stores Names based on user email
      @Author        : SmartData
      created:29/11/2016
     * ****************************************************************************************** */

    public function getStoresNames() {
        configure::Write('debug', 0);
        $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody = '{"email":"rjsaini90@yopmail.com"}';
        $this->Webservice->webserviceAdminLog($requestBody,"admin_store_loc.txt",$headers);
        //$headers['user_id']="NzU";// Ekansh test server
        //$headers['user_id']="MzEw";
        //$headers['merchant_id']=1;
        $responsedata = array();
        $requestBody = json_decode($requestBody, true);
        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantCheck = $this->Merchant->find('first', array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantCheck)) {
                   if (isset($requestBody['email']) && !empty($requestBody['email'])) {
                       $email=  strtolower($requestBody['email']);
                       $roleID=3;
                        $this->Store->unBindModel(array('belongsTo' => array('StoreTheme')), false);
                        $this->Store->unBindModel(array('belongsTo' => array('StoreFont')), false);
                        $this->Store->unBindModel(array('hasOne' => array('SocialMedia')), false);
                        $this->Store->unBindModel(array('hasMany' => array('StoreContent')), false);
                        $this->Store->unBindModel(array('hasMany' => array('StoreGallery')), false);

                    $this->User->bindModel(array(
                                    'belongsTo' => array(
                                        'Store' => array(
                                            'className' => 'Store',
                                            'foreignKey' => 'store_id',
                                            'type' => 'INNER',
                                            'conditions' => array('Store.is_active' => 1, 'Store.is_deleted' => 0),
                                            'fields' => array('id', 'store_name')
                                        ),
                                    )
                                ));
                    $locStoreList = $this->User->find("all", array('recursive' => 3,"conditions" => array("User.email" => $email,"User.role_id"=> $roleID, "User.is_active" => 1,"User.is_deleted" => 0), 'fields' => array('email', 'password', 'fname', 'id',  'store_id',  'merchant_id','lname', 'phone', 'dateOfBirth', 'country_code_id', 'is_deleted', 'is_active', 'is_newsletter', 'is_emailnotification', 'is_smsnotification')));
                $listing =array();
                if (!empty($locStoreList)) {
                foreach ($locStoreList as $k => $locStoreDet) {
                    $listing['Store'][ $k]['id']=$locStoreDet['Store']['id'];
                    $listing['Store'][ $k]['store_name']=$locStoreDet['Store']['store_name'];
                    unset($locStoreList[$k]['User']);
                }
                //pr($listing);

                    $responsedata['message'] = "Success";
                    $responsedata['response'] = 1;
                    $responsedata['Store'] = array_values($listing['Store']);
                    //pr($responsedata);
                     return json_encode($responsedata);
                    //return $this->json_message(1, 'Success', $listing);
                } else {
                    $responsedata['message'] = "No active user found with this email.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
                } else {
                        $responsedata['message'] = "Please enter email.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
            } else {
                $responsedata['message'] = "Merchant not found.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

    /*     * ******************************************************************************************
      @Function Name : forgotPassword
      @Description   : this function is used for forgot Password based on Store ID
      @Author        : SmartData
      created:29/11/2016
     * ****************************************************************************************** */

    public function forgotPassword() {
        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$headers['merchant_id']=1;
        $this->Webservice->webserviceAdminLog($requestBody, "forgot_pasword.txt", $headers);
        //$requestBody = '{"email": "rjsaini90@yopmail.com","store_id":"108"}';
        $responsedata = array();
        $requestBody = json_decode($requestBody, true);
        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantCheck = $this->Merchant->find('first', array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantCheck)) {
                $roleId = 3;
                if ($requestBody['email'] != '') {
                    $email=$requestBody['email'];
                    //if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                    $userEmail = $this->User->find('first', array('conditions' => array('User.email' => $email, 'User.merchant_id' => $merchant_id, 'User.is_deleted' => 0,'User.is_active' => 1, 'User.role_id' => $roleId), 'fields' => array('User.id', 'User.email', 'User.fname', 'User.lname', 'User.store_id')));
                    if (!empty($userEmail)) {
                        $store_id = $userEmail['User']['store_id'];
                        $storeEmail = $this->Store->fetchStoreDetail($store_id);
                        $store_url=$storeEmail['Store']['store_url'];
                        //Calling function on model for checking the email
                        $protocol = 'http://';
                                    if (isset($_SERVER['HTTPS'])) {
                                        if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                                            $protocol = 'https://';
                                        }
                                    }

                            $this->loadModel('DefaultTemplate');
                            $template_type = 'forget_password';
                            $emailTemplate = $this->DefaultTemplate->adminTemplates($template_type);
                            if ($emailTemplate) {
                                if ($userEmail['User']['lname']) {
                                    $fullName = $userEmail['User']['fname'] . " " . $userEmail['User']['lname'];
                                } else {
                                    $fullName = $this->request->data['User']['fname'];
                                }
                                $token = Security::hash($email, 'md5', true) . time() . rand();
                                $emailData = $emailTemplate['DefaultTemplate']['template_message'];
                                $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                                //$url = HTTP_ROOT . 'users/resetPassword/' . $token . '/3';
                                $url = $protocol.$store_url . '/users/resetPassword/' . $token . '/3';

                                $activationLink = '<a style="color:#fff;background-color: #10c4f7; text-decoration:none; padding: 5px 10px 7px;font-weight: bold; display:inline-block;" href="' . $url . '">Click here to reset your password</a>';
                                $emailData = str_replace('{ACTIVE_LINK}', $activationLink, $emailData);

                                $subject = ucwords(str_replace('_', ' ', $emailTemplate['DefaultTemplate']['template_subject']));

                                $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);
                                $storeAddress = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
                                $storePhone = $storeEmail['Store']['phone'];
                                $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                                $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);

                                $this->Email->to = $email;
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
                                $this->Email->sendAs = 'html'; // because we like to send pretty mail

                                try {
                                    if ($this->Email->send()) {
                                       $data['User']['id'] = $userEmail['User']['id'];
                                        $data['User']['forgot_token'] = $token;
                                        $this->User->saveUserInfo($data['User']);
                                        $responsedata['message'] = "Email sent, please check your email account.";
                                        $responsedata['response'] = 1;
                                        return json_encode($responsedata);
                                    }
                                } catch (Exception $e) {
                                        $responsedata['message'] = "Unable to process your request, Please try after some time.";
                                        $responsedata['response'] = 0;
                                        return json_encode($responsedata);
                                }
                            }
                        } else {
                            $responsedata['message'] = "No active user found with this email.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    //} else {
                    //    $responsedata['message'] = "Please select a store";
                    //    $responsedata['response'] = 0;
                    //    return json_encode($responsedata);
                    //}
                } else {
                    $responsedata['message'] = "Please enter email.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

    /*     * ******************************************************************************************
      @Function Name : getStoresList
      @Description   : this function is used for get location Store List based on merchant id and User id
      @Author        : SmartData
      created:24/11/2016
     * ****************************************************************************************** */

    public function getStoresList() {
        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        $this->Webservice->webserviceAdminLog($requestBody,"admin_store_loc.txt",$headers);
        //$headers['user_id']="NzU";// Ekansh test server
        //$headers['user_id']="MzEw";
        //$headers['merchant_id']=1;
        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantCheck = $this->Merchant->find('first', array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantCheck)) {
                   if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                       $roleID=3;
                            $user_id = $this->Encryption->decode($headers['user_id']);
                            $userResult = $this->User->find("first", array("conditions" => array("User.id" => $user_id,"User.role_id" => $roleID, "User.merchant_id" => $merchant_id, "User.is_active" => 1,"User.is_deleted" => 0), 'fields' => array('email', 'password', 'fname', 'id', 'lname', 'phone', 'dateOfBirth', 'country_code_id', 'is_deleted', 'is_active', 'is_newsletter', 'is_emailnotification', 'is_smsnotification')));
                   if (!empty($userResult)) {
                    $email=$userResult['User']['email'];
                    $password=$userResult['User']['password'];
                //pr($userResult);
                    $this->Store->unBindModel(array('belongsTo' => array('StoreTheme')), false);
                    $this->Store->unBindModel(array('belongsTo' => array('StoreFont')), false);
                    $this->Store->unBindModel(array('hasOne' => array('SocialMedia')), false);
                    $this->Store->unBindModel(array('hasMany' => array('StoreContent')), false);
                    $this->Store->unBindModel(array('hasMany' => array('StoreGallery')), false);

                    $this->User->bindModel(array(
                                    'hasMany' => array(
                                        'Permission' => array(
                                            'className' => 'Permission',
                                            'foreignKey' => 'user_id',
                                            'type' => 'INNER',
                                            'conditions' => array('Permission.is_active' => 1, 'Permission.is_deleted' => 0,'Permission.tab_id' => array(10,12,13,14,16,47,3)),
                                            'fields' => array('id', 'tab_id')
                                        ),
                                    )
                                ));
                    $this->User->bindModel(array(
                                    'belongsTo' => array(
                                        'Store' => array(
                                            'className' => 'Store',
                                            'foreignKey' => 'store_id',
                                            'type' => 'INNER',
                                            'conditions' => array('Store.is_active' => 1, 'Store.is_deleted' => 0),
                                            'fields' => array('id', 'store_name', 'address', 'city', 'state', 'store_logo', 'phone', 'zipcode', 'merchant_id','notification_number','notification_email')
                                        ),
                                    )
                                ));
                    $locStoreList = $this->User->find("all", array('recursive' => 3,"conditions" => array("User.email" => $email,"User.role_id"=> $roleID, "User.password" => $password, "User.is_active" => 1,"User.is_deleted" => 0), 'fields' => array('email', 'password', 'fname', 'id',  'store_id',  'merchant_id','lname', 'phone', 'dateOfBirth', 'country_code_id', 'is_deleted', 'is_active', 'is_newsletter', 'is_emailnotification', 'is_smsnotification')));

                foreach ($locStoreList as $k => $locStoreDet) {
                    if (!empty($locStoreDet['Store']['store_logo'])) {
                        $protocol = 'http://';
                        if (isset($_SERVER['HTTPS'])) {
                            if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                                $protocol = 'https://';
                            }
                        }
                        $locStoreList[$k]['Store']['store_logo'] = $protocol . $_SERVER['HTTP_HOST'] . "/storeLogo/" . $locStoreDet['Store']['store_logo'];
                    } else {
                        $locStoreList[$k]['Store']['store_logo'] = "";
                    }
                    $currentDateStore=$this->Webservice->getcurrentTime($locStoreDet['Store']['id'],1);
                    if(!empty($currentDateStore)){
                       $currentDate= $currentDateStore;
                    }else{
                       $currentDate= date("Y-m-d H:i:s");
                    }
                    $dateTime=  explode(" ", $currentDate);
                    $current_date = $dateTime[0];
                    $current_time = $dateTime[1];
                    if(!empty($current_date)){
                        $locStoreList[$k]['Store']['current_date']=$current_date;
                        $locStoreList[$k]['Store']['current_time']=$current_time;
                    }
                    if(!empty($locStoreDet['Store']['notification_number'])){
                        $locStoreList[$k]['Store']['notification_number']=$locStoreDet['Store']['notification_number'];
                    }else{
                        $locStoreList[$k]['Store']['notification_number']="";
                    }
                    if(!empty($locStoreDet['Store']['notification_email'])){
                        $locStoreList[$k]['Store']['notification_email']=$locStoreDet['Store']['notification_email'];
                    }else{
                        $locStoreList[$k]['Store']['notification_email']="";
                    }
                    $locStoreList[$k]['Store']['isCouponAllow']=FALSE;
                    $locStoreList[$k]['Store']['isPromotionAllow']=FALSE;
                    $locStoreList[$k]['Store']['isOrderAllow']=FALSE;
                    $locStoreList[$k]['Store']['isReviewAllow']=FALSE;
                    $locStoreList[$k]['Store']['isBookingAllow']=FALSE;
                    $locStoreList[$k]['Store']['isItemOffersAllow']=FALSE;
                    $locStoreList[$k]['Store']['isConfigurationAllow']=FALSE;

                    if(!empty($locStoreDet['Permission'])){
                        foreach($locStoreDet['Permission'] as $p=>$permission){
                            if($permission['tab_id']==10){
                                $locStoreList[$k]['Store']['isCouponAllow']=TRUE;
                            }elseif($permission['tab_id']==12){
                                 $locStoreList[$k]['Store']['isPromotionAllow']=TRUE;
                            }elseif($permission['tab_id']==13){
                                 $locStoreList[$k]['Store']['isOrderAllow']=TRUE;
                            }elseif($permission['tab_id']==14){
                                 $locStoreList[$k]['Store']['isReviewAllow']=TRUE;
                            }elseif($permission['tab_id']==16){
                                 $locStoreList[$k]['Store']['isBookingAllow']=TRUE;
                            }elseif($permission['tab_id']==47){;
                                 $locStoreList[$k]['Store']['isItemOffersAllow']=TRUE;
                            }elseif($permission['tab_id']==3){;
                                 $locStoreList[$k]['Store']['isConfigurationAllow']=TRUE;
                            }
                        }
                    }
                    unset($locStoreList[$k]['User']);
                    unset($locStoreList[$k]['Permission']);
                }


                header('merchant_id:' . $merchant_id);
                $listing=array();
                if (!empty($locStoreList)) {
                    $s=0;
                    foreach($locStoreList as $storeData){
                        $listing[$s]=$storeData['Store'];
                        $s++;
                    }
                //    pr($listing);
                //die;
                    $responsedata['message'] = "Success";
                    $responsedata['response'] = 1;
                    $responsedata['stores'] = array_values($listing);
                    //pr($responsedata);
                     return json_encode($responsedata);
                    //return $this->json_message(1, 'Success', $listing);
                } else {
                    $responsedata['message'] = "Store not found.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
                } else {
                        $responsedata['message'] = "No active user found.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                        $responsedata['message'] = "Please login.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
            } else {
                $responsedata['message'] = "Merchant not found.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }



    /*     * ******************************************************************************************
      @Function Name : countryCodeList
      @Description   : this function is used for Country Code List based on merchant ID
      @Author        : SmartData
      created:24/11/2016
     * ****************************************************************************************** */

    public function countryCodeList() {
        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        $responsedata = array();
        $requestBody = json_decode($requestBody, true);
        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantCheck = $this->Merchant->find('first', array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantCheck)) {
                $countryCode = $this->CountryCode->find('list', array('fields' => array('id', 'code'), 'order' => array('CountryCode.id ASC')));
                if (!empty($countryCode)) {
//            header('merchant_id:'.$merchant_id);
                    $responsedata['message'] = "Success";
                    $responsedata['response'] = 1;

                    foreach ($countryCode as $k => $countrylist) {
                        $responsedata['countyCode'][$k] = $countrylist;
                    }
                    header('merchant_id:' . $merchant_id);
                    $responsedata['countyCode'] = array_values($responsedata['countyCode']);
                    return json_encode($responsedata);
                } else {
                    $responsedata['message'] = "Country information not found.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

    public function tokenGenerate($headerVal = null, $merchant_id = null) {
        $iPod = stripos($_SERVER['HTTP_USER_AGENT'], "iPod");
        $iPhone = stripos($_SERVER['HTTP_USER_AGENT'], "iPhone");
        $iPad = stripos($_SERVER['HTTP_USER_AGENT'], "iPad");
        $Android = stripos($_SERVER['HTTP_USER_AGENT'], "Android");
        $webOS = stripos($_SERVER['HTTP_USER_AGENT'], "webOS");
        header('user_token:' . $headerVal);
        header('merchant_id:' . $merchant_id);
        //do something with this information
        if ($iPod) {
            header('device_type:' . $iPod);
        } else if ($iPhone) {
            header('device_type:' . $iPhone);
        } else if ($Android) {
            header('device_type:' . $iPad);
        } else if ($Android) {
            header('device_type:' . $Android);
        } else if ($webOS) {
            header('device_type:' . $webOS);
        } else {
            header('device_type:' . 'web');
        }
    }
    /* ------------------------------------------------
      Function name:storeDashboard()
      Description: Used showing dashbord like today order booking
      created:25/11/2016
      ----------------------------------------------------- */

      public function storeDashboard() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody =  '{"store_id": "2","type":"daily"}';//  type="monthly/daily" like
        //$headers['user_id'] = 'MzEx';
        //$headers['merchant_id'] = 1;
        $this->Webservice->webserviceAdminLog($requestBody, "storeDashboard.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id)));
            if (!empty($merchantResult)) {
                $domain = $merchantResult['Merchant']['domain_name'];
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $this->User->bindModel(array(
                        'hasMany' => array(
                            'Permission' => array(
                                'className' => 'Permission',
                                'foreignKey' => 'user_id',
                                'type' => 'INNER',
                                'conditions' => array('Permission.is_active' => 1, 'Permission.is_deleted' => 0, 'Permission.tab_id' => array(10, 12, 13, 14, 16, 47, 3)),
                                'fields' => array('id', 'tab_id')
                            ),
                        )
                    ));
                     $this->User->bindModel(array(
                        'hasMany' => array(
                            'NotificationConfiguration' => array(
                                'className' => 'NotificationConfiguration',
                                'foreignKey' => 'user_id',
                                'type' => 'INNER',
                                'conditions' => array('NotificationConfiguration.is_active' => 1, 'NotificationConfiguration.is_deleted' => 0),
                                'fields' => array('id', 'order_notification', 'show_in_notification', 'sound', 'badge_app', 'add_alarm','alarm_time_id')
                            ),
                        )
                    ));
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                $date=$this->Webservice->getcurrentTime($store_id,2);
                                $month=  explode('-', $date);
                                $current_month=$month[1];
                                $dateformate= explode('-', $date);
                                $dateConverted=$dateformate[2].'/'.$dateformate[1].'/'.$dateformate[0];
                                $responsedata['current_date']=$dateConverted;
                                $filterType =  strtolower($requestBody['type']);
                                // Find count of order monthly basis
                            if ($filterType == 'monthly')  {

                               $todaysOrder = $this->Order->find('count',array('fields'=>array('id'),'conditions'=>array('Order.is_future_order'=>0,'Order.store_id'=>$store_id,'Order.is_active'=>1,'MONTH(Order.pickup_time)'=>$current_month)));
                                if(!empty($todaysOrder)){
                                    $responsedata['todayOrders']=$todaysOrder;
                                }else{
                                    $responsedata['todayOrders']=0;
                                }

                                //Pending Orders
                                $todaysPendingOrder = $this->Order->find('count',array('fields'=>array('id'),'conditions'=>array('Order.is_future_order'=>0,'Order.store_id'=>$store_id,'Order.is_active'=>1,'MONTH(Order.pickup_time)'=>$current_month ,'Order.order_status_id'=>1)));
                                  if(!empty($todaysPendingOrder)){
                                   $responsedata['pendingOrder']=$todaysPendingOrder;
                                }else{
                                    $responsedata['pendingOrder']=0;
                                }

                                //Pre-orders
                                $preOrder = $this->Order->find('count',array('fields'=>array('id'),'conditions'=>array('Order.is_future_order'=>0,'Order.store_id'=>$store_id,'Order.is_active'=>1,'Order.is_deleted'=>0,'MONTH(Order.pickup_time) '=>$current_month)));;
                                 if(!empty($preOrder)){
                                    $responsedata['preOrder']=$preOrder;
                                }else{
                                    $responsedata['preOrder']=0;
                                }

                                //Today's Booking Requests
                                $todaysBookingRequest = $this->Booking->find('count',array('fields'=>array('id'),'conditions'=>array('Booking.store_id'=>$store_id,'Booking.is_active'=>1,'Booking.is_deleted'=>0,'MONTH(Booking.reservation_date)'=>$current_month)));
                                 if(!empty($todaysBookingRequest)){
                                    $responsedata['todaybookingRequest']=$todaysBookingRequest;
                                }else{
                                    $responsedata['todaybookingRequest']=0;
                                }

                                //Pending Booking Requests
                                $todaysPendingBookings =$this->Booking->find('count',array('fields'=>array('id'),'conditions'=>array('Booking.store_id'=>$store_id,'Booking.is_active'=>1,'MONTH(Booking.reservation_date)'=>$current_month,'Booking.booking_status_id'=>1,'Booking.is_deleted'=>0)));;
                                 if(!empty($todaysPendingBookings)){
                                    $responsedata['pendingBookings']=$todaysPendingBookings;
                                }else{
                                    $responsedata['pendingBookings']=0;
                                }


                            } else{

                                // Find count of order Daily basis
                                //Today's Orders
                                //$todaysOrder = $this->Webservice->getTodaysOrder($store_id);
                                $todaysOrder=$this->Order->find('count',array('fields'=>array('id'),'conditions'=>array('Order.is_future_order'=>0,'Order.store_id'=>$store_id,'Order.is_active'=>1,'DATE(Order.pickup_time)'=>$date)));
                                if(!empty($todaysOrder)){
                                    $responsedata['todayOrders']=$todaysOrder;
                                }else{
                                    $responsedata['todayOrders']=0;
                                }

                                //Pending Orders
                                //$todaysPendingOrder = $this->Webservice->getTodaysPendingOrder($store_id);
                                $todaysPendingOrder= $this->Order->find('count',array('fields'=>array('id'),'conditions'=>array('Order.is_future_order'=>0,'Order.store_id'=>$store_id,'Order.is_active'=>1,'DATE(Order.pickup_time)'=>$date ,'Order.order_status_id'=>1)));
                                  if(!empty($todaysPendingOrder)){
                                   $responsedata['pendingOrder']=$todaysPendingOrder;
                                }else{
                                    $responsedata['pendingOrder']=0;
                                }

                                //Pre-orders
                                $preOrder = $this->Webservice->getPreOrder($store_id);
                                 if(!empty($preOrder)){
                                    $responsedata['preOrder']=$preOrder;
                                }else{
                                    $responsedata['preOrder']=0;
                                }

                                //Today's Booking Requests
                                //$todaysBookingRequest = $this->Webservice->getTodaysBookingRequest($store_id);
                                $todaysBookingRequest=$this->Booking->find('count',array('fields'=>array('id'),'conditions'=>array('Booking.store_id'=>$store_id,'Booking.is_active'=>1,'Booking.is_deleted'=>0,'DATE(Booking.reservation_date)'=>$date)));
                                 if(!empty($todaysBookingRequest)){
                                    $responsedata['todaybookingRequest']=$todaysBookingRequest;
                                }else{
                                    $responsedata['todaybookingRequest']=0;
                                }

                                //Pending Booking Requests
                                //$todaysPendingBookings = $this->Webservice->getTodaysPendingBookings($store_id);
                                $todaysPendingBookings = $this->Booking->find('count',array('fields'=>array('id'),'conditions'=>array('Booking.store_id'=>$store_id,'Booking.is_active'=>1,'DATE(Booking.reservation_date)'=>$date ,'Booking.booking_status_id'=>1,'Booking.is_deleted'=>0)));
                                 if(!empty($todaysPendingBookings)){
                                    $responsedata['pendingBookings']=$todaysPendingBookings;
                                }else{
                                    $responsedata['pendingBookings']=0;
                                }

                            }
                            $responsedata['currency'] = '$' ;
                            $responsedata['isCouponAllow'] = FALSE;
                            $responsedata['isPromotionAllow'] = FALSE;
                            $responsedata['isOrderAllow'] = FALSE;
                            $responsedata['isReviewAllow'] = FALSE;
                            $responsedata['isBookingAllow'] = FALSE;
                            $responsedata['isItemOffersAllow'] = FALSE;
                            $responsedata['isConfigurationAllow'] = FALSE;

                             if (!empty($userDet['Permission'])) {
                                foreach ($userDet['Permission'] as $p => $permission) {
                                    if ($permission['tab_id'] == 10) {
                                        $responsedata['isCouponAllow'] = TRUE;
                                    } elseif ($permission['tab_id'] == 12) {
                                        $responsedata['isPromotionAllow'] = TRUE;
                                    } elseif ($permission['tab_id'] == 13) {
                                        $responsedata['isOrderAllow'] = TRUE;
                                    } elseif ($permission['tab_id'] == 14) {
                                        $responsedata['isReviewAllow'] = TRUE;
                                    } elseif ($permission['tab_id'] == 16) {
                                        $responsedata['isBookingAllow'] = TRUE;
                                    } elseif ($permission['tab_id'] == 47) {
;
                                        $responsedata['isItemOffersAllow'] = TRUE;
                                    } elseif ($permission['tab_id'] == 3) {
                                        $responsedata['isConfigurationAllow'] = TRUE;
                                    }
                                }
                            }

                                    $responsedata['Notifications']['order_notification']=FALSE;
                                    $responsedata['Notifications']['show_in_notification']=FALSE;
                                    $responsedata['Notifications']['sound']=FALSE;
                                    $responsedata['Notifications']['badge_app']=FALSE;
                                    $responsedata['Notifications']['add_alarm']=FALSE;
                                    $responsedata['Notifications']['alarm_time_id']="";
                            if(!empty($userDet['NotificationConfiguration'])){
                                    if($userDet['NotificationConfiguration'][0]['order_notification']==1){
                                      $responsedata['Notifications']['order_notification']=TRUE;
                                    }
                                    if($userDet['NotificationConfiguration'][0]['show_in_notification']==1){
                                      $responsedata['Notifications']['show_in_notification']=TRUE;
                                    }
                                    if($userDet['NotificationConfiguration'][0]['sound']==1){
                                      $responsedata['Notifications']['sound']=TRUE;
                                    }
                                    if($userDet['NotificationConfiguration'][0]['badge_app']==1){
                                      $responsedata['Notifications']['badge_app']=TRUE;
                                    }
                                    if($userDet['NotificationConfiguration'][0]['add_alarm']==1){
                                      $responsedata['Notifications']['add_alarm']=TRUE;
                                    }
                                     if(!empty($userDet['NotificationConfiguration'][0]['alarm_time_id'])){
                                      $responsedata['Notifications']['alarm_time_id']=$userDet['NotificationConfiguration'][0]['alarm_time_id'];
                                    }else{
                                       $responsedata['Notifications']['alarm_time_id']="";
                                    }
                                }


                                $responsedata['message'] = "Success";
                                $responsedata['response'] = 1;
                                //pr($responsedata);
                                return json_encode($responsedata);

                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not registered under this store.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     /* ------------------------------------------------
      Function name:orderCounts()
      Description: Used today order ,booking
      created:25/11/2016
      ----------------------------------------------------- */

      public function orderCounts() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody =  '{"store_id": "108","date":"25/11/2016"}';//  date formate=dd/mm/yyyy
        //$requestBody =  '{"store_id": "108"}';
        //$headers['user_id'] = 'NzU';
        //$headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "orderCounts.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();
    //pr($requestBody);
    //die;
        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id)));
            if (!empty($merchantResult)) {
                $domain = $merchantResult['Merchant']['domain_name'];
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if(!empty($requestBody['date'])){
                                $date=$requestBody['date'];
                                }else{
                                    $date=date("d/m/Y");
                                }
                                $month=  explode('/', $date);
                                $current_month=$month[1];
                                $dateYMD=$month[2].'-'.$month[1].'-'.$month[0];

                                $totalOrders = $this->Order->find('count',array('fields'=>array('id'),'conditions'=>array('Order.is_future_order'=>0,'Order.store_id'=>$store_id,'Order.is_active'=>1)));
                                if(!empty($totalOrders)){
                                     $responsedata['totalOrders']=$totalOrders;
                                }else{
                                      $responsedata['totalOrders']=0;
                                }
                                // Find count of order monthly basis
                               $todaysOrder = $this->Order->find('count',array('fields'=>array('id'),'conditions'=>array('Order.is_future_order'=>0,'Order.store_id'=>$store_id,'Order.is_active'=>1,'DATE(Order.pickup_time)'=>$dateYMD)));
                                if(!empty($todaysOrder)){
                                     $responsedata['todayOrders']=$todaysOrder;
                                }else{
                                      $responsedata['todayOrders']=0;
                                }

                                //Today's Booking Requests
                                $totalBookings = $this->Booking->find('count',array('fields'=>array('id'),'conditions'=>array('Booking.store_id'=>$store_id,'Booking.is_active'=>1,'Booking.is_deleted'=>0)));
                                if(!empty($totalBookings)){
                                     $responsedata['totalbookingRequest']=$totalBookings;
                                }else{
                                      $responsedata['totalbookingRequest']=0;
                                }
                                $todaysBookingRequest = $this->Booking->find('count',array('fields'=>array('id'),'conditions'=>array('Booking.store_id'=>$store_id,'Booking.is_active'=>1,'Booking.is_deleted'=>0,'DATE(Booking.created)'=>$dateYMD)));
                               if(!empty($todaysBookingRequest)){
                                     $responsedata['todaybookingRequest']=$todaysBookingRequest;
                                }else{
                                      $responsedata['todaybookingRequest']=0;
                                }
                                $responsedata['preOrder']=0;
                                $responsedata['pendingBookings']=0;
                                $responsedata['message'] = "Success";
                                $responsedata['response'] = 1;
                                return json_encode($responsedata);

                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not registered under this store.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

    /*     * ******************************************************************************************
      @Function Name : orderList
      @Description   : this function is used for Order Lisitng
      @Author        : SmartData
      created:28/11/2016
     * ****************************************************************************************** */

    public function orderList() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody =  '{"date":"30/11/2016","order_type":"2","page_number":"1","store_id":"108"}'; //  date formate=dd/mm/yyyy "todayOrders": 1,  "pendingOrder": 2,  "preOrder": 3,
        //$headers['user_id'] = 'NzU';
        //$headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "order_list.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id)));
            if (!empty($merchantResult)) {
                $domain = $merchantResult['Merchant']['domain_name'];
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if(!empty($requestBody['date'])){
                                $date=$requestBody['date'];
                                }else{
                                    $date=date("d/m/Y");
                                }
                                $month=  explode('/', $date);
                                $current_month=$month[1];
                                $dateYMD=$month[2].'-'.$month[1].'-'.$month[0];
                                //echo $dateYMD."<br>";
                                $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);

                        $this->Order->bindModel(array('belongsTo' => array('DeliveryAddress' => array('fields' => array('name_on_bell', 'city', 'address')), 'OrderStatus' => array('fields' => array('name')))), false);
                        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
                        $this->Order->bindModel(array(
                            'belongsTo' => array(
                                'Store' => array(
                                    'className' => 'Store',
                                    'foreignKey' => 'store_id',
                                    'fields' => array('id', 'store_name','store_url'),
                                    'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1)
                                ))
                                ), false);
                        $this->Order->bindModel(array(
                            'belongsTo' => array(
                                'User' => array(
                                    'className' => 'User',
                                    'foreignKey' => 'user_id',
                                    'fields' => array('id','address','fname','lname','city'),
                                    'conditions' => array('User.is_deleted' => 0, 'User.is_active' => 1)
                                ))
                                ), false);
                           if (empty($requestBody['page_number'])) {
                            $requestBody['page_number']=1;
                           }
                           $limit = 9;
                           $offset = $requestBody['page_number'] * 9 - 9;

                                if($requestBody['order_type']==1){//todayOrders": 1,
                                     $OrderCount = $this->Order->find('count',array('order' => 'Order.created DESC', 'recursive' => 3, 'conditions'=>array('Order.is_future_order'=>0,'Order.store_id'=>$store_id,'Order.is_active'=>1,'Order.is_deleted'=>0,'DATE(Order.pickup_time)'=>$dateYMD),'fields'=>array('Order.id','Order.order_number','Order.amount','Order.pickup_time','Order.seqment_id','Order.store_id','Order.delivery_address_id','Order.order_status_id','Order.created','Order.user_id')));
                                     $Order = $this->Order->find('all',array('order' => 'Order.created DESC', 'recursive' => 3, 'offset' => $offset, 'limit' => $limit, 'conditions'=>array('Order.is_future_order'=>0,'Order.store_id'=>$store_id,'Order.is_active'=>1,'Order.is_deleted'=>0,'DATE(Order.pickup_time)'=>$dateYMD),'fields'=>array('Order.id','Order.user_id','Order.order_number','Order.amount','Order.pickup_time','Order.seqment_id','Order.store_id','Order.delivery_address_id','Order.order_status_id','Order.created','Order.user_id')));

                                }elseif ($requestBody['order_type']==2) { // "pendingOrder": 2,
                                    $OrderCount = $this->Order->find('count',array('order' => 'Order.created DESC', 'recursive' => 3, 'conditions'=>array('Order.is_future_order'=>0,'Order.store_id'=>$store_id,'Order.is_active'=>1,'Order.is_deleted'=>0,'DATE(Order.pickup_time)'=>$dateYMD ,'Order.order_status_id'=>1),'fields'=>array('Order.id','Order.order_number','Order.amount','Order.pickup_time','Order.seqment_id','Order.store_id','Order.delivery_address_id','Order.order_status_id','Order.created','Order.user_id')));

                                       $Order = $this->Order->find('all',array('order' => 'Order.created DESC', 'recursive' => 3, 'offset' => $offset, 'limit' => $limit, 'conditions'=>array('Order.is_future_order'=>0,'Order.is_deleted'=>0,'Order.store_id'=>$store_id,'Order.is_active'=>1,'DATE(Order.pickup_time)'=>$dateYMD ,'Order.order_status_id'=>1),'fields'=>array('Order.id','Order.user_id','Order.order_number','Order.amount','Order.pickup_time','Order.seqment_id','Order.store_id','Order.delivery_address_id','Order.order_status_id','Order.created','Order.user_id')));

                                }elseif ($requestBody['order_type']==3) { // "preOrder": 3,
                                    $OrderCount = $this->Order->find('count',array('order' => 'Order.created DESC', 'recursive' => 3,'conditions'=>array('Order.is_future_order'=>0,'Order.store_id'=>$store_id,'Order.is_active'=>1,'Order.is_deleted'=>0,'Order.is_pre_order'=>1,'Order.is_deleted'=>0,'DATE(Order.pickup_time) >='=>$dateYMD),'fields'=>array('Order.id','Order.order_number','Order.amount','Order.pickup_time','Order.seqment_id','Order.store_id','Order.delivery_address_id','Order.order_status_id','Order.created','Order.user_id')));
                                     $Order = $this->Order->find('all',array('order' => 'Order.created DESC', 'recursive' => 3, 'offset' => $offset, 'limit' => $limit, 'conditions'=>array('Order.is_future_order'=>0,'Order.store_id'=>$store_id,'Order.is_active'=>1,'Order.is_pre_order'=>1,'Order.is_deleted'=>0,'DATE(Order.pickup_time) >='=>$dateYMD),'fields'=>array('Order.id','Order.user_id','Order.order_number','Order.amount','Order.pickup_time','Order.seqment_id','Order.store_id','Order.delivery_address_id','Order.order_status_id','Order.created','Order.user_id')));
                                }else{
                                    $OrderCount = $this->Order->find('count',array('order' => 'Order.created DESC', 'recursive' => 3,'conditions'=>array('Order.is_future_order'=>0,'Order.store_id'=>$store_id,'Order.is_active'=>1,'Order.is_deleted'=>0),'fields'=>array('Order.id','Order.order_number','Order.amount','Order.pickup_time','Order.seqment_id','Order.store_id','Order.delivery_address_id','Order.order_status_id','Order.created','Order.user_id')));
                                    $Order = $this->Order->find('all',array('order' => 'Order.created DESC', 'recursive' => 3, 'offset' => $offset, 'limit' => $limit, 'conditions'=>array('Order.is_future_order'=>0,'Order.store_id'=>$store_id,'Order.is_active'=>1,'Order.is_deleted'=>0),'fields'=>array('Order.id','Order.user_id','Order.order_number','Order.amount','Order.pickup_time','Order.seqment_id','Order.store_id','Order.delivery_address_id','Order.order_status_id','Order.created')));
                                    //$Order = $this->Order->find('all',array('order' => 'Order.pickup_time DESC', 'recursive' => 3, 'conditions'=>array('Order.is_future_order'=>0,'Order.store_id'=>$store_id,'Order.is_active'=>1,'Order.is_deleted'=>0),'fields'=>array('Order.id','Order.order_number','Order.amount','Order.pickup_time','Order.seqment_id','Order.store_id','Order.delivery_address_id','Order.order_status_id','Order.created','Order.user_id')));
                                }
                              $myOrdersList=array();
                              if(!empty($Order)){
                                $o=0;
                                foreach($Order as $listOrder){
                                $myOrdersList[$o]['order_id'] = $listOrder['Order']['id'];
                                $myOrdersList[$o]['order_number'] = $listOrder['Order']['order_number'];
                                $myOrdersList[$o]['total_amount'] = '$'.$listOrder['Order']['amount'];
                                    $dateTime =explode(" ",$listOrder['Order']['pickup_time']);
                                    $myOrdersList[$o]['date'] = $dateTime[0];
                                    $myOrdersList[$o]['time'] = $dateTime[1];
                                    $placedDateTime =explode(" ",$listOrder['Order']['created']);
                                    $myOrdersList[$o]['placed_date'] = $placedDateTime[0];
                                    $myOrdersList[$o]['placed_time'] = $placedDateTime[1];
                                //$myOrdersList[$o]['pickup_date'] = $listOrder['Order']['pickup_time'];
                                //$myOrdersList[$o]['order_placed'] = $listOrder['Order']['created'];
                                if (!empty($listOrder['Order']['seqment_id']) && $listOrder['Order']['seqment_id'] == 2) {
                                    $myOrdersList[$o]['order_type'] = 'Take Away';
                                } elseif (!empty($listOrder['Order']['seqment_id']) && $listOrder['Order']['seqment_id'] == 3) {
                                    $myOrdersList[$o]['order_type'] = 'Home Delivery';
                                }
                                if(!empty($listOrder['DeliveryAddress']['name_on_bell'])){
                                    $myOrdersList[$o]['name_on_bell'] = $listOrder['DeliveryAddress']['name_on_bell'];
                                } elseif(!empty($listOrder['User']['fname'])) {
                                    $myOrdersList[$o]['name_on_bell'] = $listOrder['User']['fname']." ".$listOrder['User']['lname'];
                                }else{
                                    $myOrdersList[$o]['name_on_bell'] = "";
                                }
                                if(!empty($listOrder['DeliveryAddress']['city'])){
                                    $myOrdersList[$o]['city'] = $listOrder['DeliveryAddress']['city'];
                                }elseif(!empty($listOrder['User']['city'])) {
                                    $myOrdersList[$o]['city']  = $listOrder['User']['city'];
                                }else{
                                    $myOrdersList[$o]['city'] = "";
                                }
                                if(!empty($listOrder['DeliveryAddress']['address'])){
                                    $myOrdersList[$o]['address'] = $listOrder['DeliveryAddress']['address'];
                                }elseif(!empty($listOrder['User']['address'])) {
                                    $myOrdersList[$o]['address']  = $listOrder['User']['address'];
                                }else{
                                    $myOrdersList[$o]['address'] = "";
                                }

                                $myOrdersList[$o]['OrderStatus'] = $listOrder['OrderStatus']['name'];
                                $myOrdersList[$o]['store_id'] = $listOrder['Store']['id'];
                                $myOrdersList[$o]['store_name'] = $listOrder['Store']['store_name'];
                                $o++;
                                }

                              }

                                $responsedata['message'] = "Success";
                                $responsedata['response'] = 1;
                                if(!empty($OrderCount)){
                                    if($OrderCount>10){
                                        $responsedata['count'] = (string)ceil($OrderCount/10);
                                    }else{
                                        $responsedata['count'] = "1";
                                    }

                                }else{
                                   $responsedata['count'] = "1";
                                }

                                $responsedata['Order'] = array_values($myOrdersList);
                                //pr($responsedata);
                                return json_encode($responsedata);

                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not registered under this store.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

    /*     * ******************************************************************************************
      @Function Name : orderDetail
      @Description   : this function is used for show order deatial for particular Order.
      @Author        : SmartData
      created:28/11/2016
     * ****************************************************************************************** */

     public function orderDetail() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody = '{"store_id": "108","order_id":"1037"}';
        //$headers['user_id'] = 'NzU';
        //$headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "order_Detail.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id)));
            if (!empty($merchantResult)) {
                $domain = $merchantResult['Merchant']['domain_name'];
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if (isset($requestBody['order_id']) && !empty($requestBody['order_id'])) {
                                    $order_id = $requestBody['order_id'];
                                    $this->OrderItemFree->bindModel(array('belongsTo' => array('Item' => array('fields' =>array('id', 'name')))), false);
                                    $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);
                                    $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('className' => 'Item', 'foreignKey' => 'offered_item_id', 'fields' => array('id', 'name')), 'Size' => array('className' => 'Size', 'foreignKey' => 'offered_size_id', 'fields' => array('id', 'size')))), false);
                                    $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('className' => 'Topping', 'foreignKey' => 'topping_id', 'fields' => array('id', 'name')))), false);
                                    $this->OrderItem->bindModel(array('hasMany' => array('OrderTopping' => array('fields' => array('id', 'topping_id', 'addon_size_id')), 'OrderOffer' => array('fields' => array('id', 'offered_item_id', 'offered_size_id', 'quantity')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('id', 'name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('id', 'size')))), false);
                                    $this->Order->bindModel(
                                            array(
                                        'hasMany' => array(
                                            'OrderItem' => array(
                                                'fields' => array('id',
                                                    'quantity', 'order_id', 'user_id', 'type_id',
                                                    'item_id', 'size_id', 'total_item_price', 'tax_price', 'interval_id')),
                                            'OrderItemFree' => array('foreignKey' => 'order_id', 'fields' => array('id', 'item_id', 'order_id', 'free_quantity', 'price'))
                                        ),
                                        'belongsTo' => array(
                                            'User' => array('className' => 'User', 'foreignKey' => 'user_id', 'fields' => array('id', 'fname', 'lname', 'country_code_id', 'phone', 'email','city','state')),
                                            'Segment' => array('className' => 'Segment', 'foreignKey' => 'seqment_id', 'fields' => array('id', 'name')),
                                            'DeliveryAddress' => array('className' => 'DeliveryAddress', 'foreignKey' => 'delivery_address_id', 'fields' => array('id', 'address', 'city', 'state', 'zipcode', 'country_code_id', 'phone', 'email','name_on_bell')),
                                            'OrderStatus' => array('fields' => array('id', 'name')),
                                            'OrderPayment' => array(
                                                'className' => 'OrderPayment',
                                                'foreignKey' => 'payment_id',
                                                'fields' => array('id', 'transection_id', 'amount', 'payment_gateway', 'payment_status'),
                                            ))), false);
                                    $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
                                    $this->Order->bindModel(array(
                                        'belongsTo' => array(
                                            'Store' => array(
                                                'className' => 'Store',
                                                'foreignKey' => 'store_id',
                                                'fields' => array('id', 'store_name', 'store_url'),
                                                'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1)
                                            ))
                                            ), false);
                                    $listOrder = $this->Order->find('first', array('recursive' => 3, 'conditions' => array('Order.merchant_id' => $merchant_id, 'Order.id' => $order_id, 'Order.store_id' => $store_id, 'Order.is_active' => 1, 'Order.is_deleted' => 0)));

                                    $myOrdersList = array();
                                     $price=0;
                                      if (empty($listOrder)) {
                                        $responsedata['message'] = "Order not found.";
                                        $responsedata['response'] = 0;
                                        $responsedata['Order'] = $myOrdersList;
                                    //pr($responsedata);
                                    return json_encode($responsedata);
                                      }
                                    if (!empty($listOrder)) {

                                        $myOrdersList['order_id'] = $listOrder['Order']['id'];
                                        $myOrdersList['order_number'] = $listOrder['Order']['order_number'];
                                        $myOrdersList['total_amount'] = number_format($listOrder['Order']['amount'],2);
                                        $myOrdersList['currency'] = '$' ;
                                        $price=$listOrder['Order']['amount'];
                                        $dateTime =explode(" ",$listOrder['Order']['pickup_time']);
                                        $myOrdersList['date'] = $dateTime[0];
                                        $myOrdersList['time'] = $dateTime[1];
                                        $placedDateTime =explode(" ",$listOrder['Order']['created']);
                                        $myOrdersList['placed_date'] = $placedDateTime[0];
                                        $myOrdersList['placed_time'] = $placedDateTime[1];
                                        //$myOrdersList['pickup_date'] = $listOrder['Order']['pickup_time'];
                                        //$myOrdersList['placed_date'] = $listOrder['Order']['created'];
                                         if (!empty($listOrder['Order']['coupon_discount'])) {
                                                    $myOrdersList['coupon_code'] = $listOrder['Order']['coupon_code'];
                                                    $myOrdersList['coupon_discount'] = number_format($listOrder['Order']['coupon_discount'],2);
                                                } else {
                                                    $myOrdersList['coupon_discount'] = "";
                                                     $myOrdersList['coupon_code'] = "";
                                                }
                                         if (!empty($listOrder['Order']['tax_price'])) {
                                                    $myOrdersList['tax_price'] = number_format($listOrder['Order']['tax_price'],2);
                                                } else {
                                                    $myOrdersList['tax_price'] = "";
                                                }
                                         if (!empty($listOrder['Order']['service_amount'])) {
                                                     $myOrdersList['service_amount'] = number_format($listOrder['Order']['service_amount'],2);
                                                } else {
                                                    $myOrdersList['service_amount'] = "";
                                                }
                                         if (!empty($listOrder['Order']['delivery_amount'])) {
                                                     $myOrdersList['delivery_amount'] = number_format($listOrder['Order']['delivery_amount'],2);
                                                } else {
                                                    $myOrdersList['delivery_amount'] = "";
                                                }
                                        if (!empty($listOrder['Order']['tip'])) {
                                                     $myOrdersList['tip'] = number_format($listOrder['Order']['tip'],2);
                                                } else {
                                                    $myOrdersList['tip'] = "";
                                                }
                                                if (!empty($listOrder['OrderItemFree'])) {
		                                    $f = 0;
		                                    foreach ($listOrder['OrderItemFree'] as $f => $freeItem) {
		                                        if (!empty($freeItem['price'])) {
		                                            $freeItemPrice[$f] = $freeItem['price'];
		                                        } else {
		                                            $freeItemPrice[$f] = 0;
		                                        }
							$myOrdersList['freeItem'][$f]['itemName']=$freeItem['Item']['name'];
							$myOrdersList['freeItem'][$f]['free']=$freeItem['free_quantity'];
		                                    }
		                                }else{
						    $myOrdersList['freeItem']=array();
		                                    $freeItem=array();
		                                }


                                        if (!empty($listOrder['Order']['seqment_id']) && $listOrder['Order']['seqment_id'] == 2) {
                                            $myOrdersList['order_type'] = 'Take Away';
                                        } elseif (!empty($listOrder['Order']['seqment_id']) && $listOrder['Order']['seqment_id'] == 3) {
                                            $myOrdersList['order_type'] = 'Home Delivery';
                                        }
                                        if (!empty($listOrder['DeliveryAddress']['name_on_bell'])) {
                                            $myOrdersList['name_on_bell'] = $listOrder['DeliveryAddress']['name_on_bell'];
                                        }  elseif(!empty($listOrder['User']['fname'])) {
                                            $myOrdersList['name_on_bell'] = $listOrder['User']['fname']." ".$listOrder['User']['lname'];
                                        }else {
                                            $myOrdersList['name_on_bell'] = "";
                                        }
                                        if (!empty($listOrder['DeliveryAddress']['address'])) {
                                            $myOrdersList['address'] = $listOrder['DeliveryAddress']['address'];
                                        }  elseif(!empty($listOrder['User']['address'])) {
                                            $myOrdersList['address'] = $listOrder['User']['address'];
                                        }else {
                                            $myOrdersList['address'] = "";
                                        }
                                        if (!empty($listOrder['DeliveryAddress']['city'])) {
                                            $myOrdersList['city'] = $listOrder['DeliveryAddress']['city'];
                                        } elseif(!empty($listOrder['User']['city'])) {
                                            $myOrdersList['city'] = $listOrder['User']['city'];
                                        } else {
                                            $myOrdersList['city'] = "";
                                        }
                                         if (!empty($listOrder['DeliveryAddress']['state'])) {
                                            $myOrdersList['state'] = $listOrder['DeliveryAddress']['state'];
                                        }  elseif(!empty($listOrder['User']['state'])) {
                                            $myOrdersList['state'] = $listOrder['User']['state'];
                                        } else {
                                            $myOrdersList['state'] = "";
                                        }

                                        if (!empty($listOrder['DeliveryAddress']['email'])) {
                                            $myOrdersList['user_email'] = $listOrder['DeliveryAddress']['email'];
                                        } elseif(!empty($listOrder['User']['email'])) {
                                            $myOrdersList['user_email'] = $listOrder['User']['email'];
                                        }else{
                                              $myOrdersList['user_email'] = "";
                                        }

                                        if (!empty($listOrder['DeliveryAddress']['phone'])) {
                                            $countryCode = $this->CountryCode->find('first', array('conditions'=>array('CountryCode.id'=>$listOrder['User']['country_code_id'])));
                                            if(!empty($countryCode)){
                                            $country_code_id=$countryCode['CountryCode']['code'];
                                            }else{
                                            $country_code_id="+1";
                                        }
                                            $myOrdersList['user_phone'] = $listOrder['DeliveryAddress']['phone'];
                                        } elseif(!empty($listOrder['User']['phone'])) {
                                            $countryCode = $this->CountryCode->find('first', array('conditions'=>array('CountryCode.id'=>$listOrder['User']['country_code_id'])));
                                            if(!empty($countryCode)){
                                            $country_code_id=$countryCode['CountryCode']['code'];
                                            }else{
                                            $country_code_id="+1";
                                        }
                                           $myOrdersList['user_phone'] = $listOrder['User']['phone'];
                                        }else{
                                              $myOrdersList['user_phone'] = "";
                                              $country_code_id="+1";
                                        }
                                        $number=$this->phoneNumberChange($myOrdersList['user_phone']);
                                       $myOrdersList['user_phone']=$country_code_id.$number;
                                        $myOrdersList['OrderStatus'] = $listOrder['OrderStatus']['name'];
                                        $myOrdersList['store_id'] = $listOrder['Store']['id'];
                                        $myOrdersList['store_name'] = $listOrder['Store']['store_name'];


                                        if (!empty($listOrder['OrderItem'])) {
                                            foreach ($listOrder['OrderItem'] as $oI => $listOrderItem) {
                                                $myOrdersList['items'][$oI]['item_id'] = $listOrderItem['Item']['id'];
                                                $myOrdersList['items'][$oI]['order_item_id'] = $listOrderItem['id'];
                                                if (!empty($listOrderItem['Item'])) {
                                                    $myOrdersList['items'][$oI]['item_name'] = $listOrderItem['Item']['name'];
                                                } else {
                                                    $myOrdersList['items'][$oI]['item_name'] = "";
                                                }

                                                if (!empty($listOrderItem['quantity'])) {
                                                    $myOrdersList['items'][$oI]['quantity'] = $listOrderItem['quantity'];
                                                } else {
                                                    $myOrdersList['items'][$oI]['quantity'] = 1;
                                                }
                                                if (!empty($listOrderItem['total_item_price'])) {
                                                    $myOrdersList['items'][$oI]['total_item_price'] = $listOrderItem['total_item_price'];
                                                } else {
                                                    $myOrdersList['items'][$oI]['total_item_price'] ="0.00";
                                                }

                                                if (!empty($listOrderItem['Size'])) {
                                                    $myOrdersList['items'][$oI]['size_id'] = $listOrderItem['Size']['id'];
                                                    $myOrdersList['items'][$oI]['size_name'] = $listOrderItem['Size']['size'];
                                                } else {
                                                    $myOrdersList['items'][$oI]['size_id'] = "";
                                                    $myOrdersList['items'][$oI]['size_name'] = "";
                                                }

                                                if (!empty($listOrderItem['OrderTopping'])) {
                                                    foreach ($listOrderItem['OrderTopping'] as $key2 => $mfot) {
                                                        if (!empty($mfot['Topping'])) {
                                                            $myOrdersList['items'][$oI]['subAddons'][$key2]['id'] = @$mfot['topping_id'];
                                                            $myOrdersList['items'][$oI]['subAddons'][$key2]['name'] = @$mfot['Topping']['name'];
                                                        } else {
                                                            $myOrdersList['items'][$oI]['subAddons'] = array();
                                                        }
                                                    }
                                                } else {
                                                    $myOrdersList['items'][$oI]['subAddons'] = array();
                                                }
                                                if (!empty($listOrderItem['OrderPreference'])) {
                                                    foreach ($listOrderItem['OrderPreference'] as $key3 => $mfop) {
                                                        if (!empty($mfop['SubPreference'])) {
                                                            $myOrdersList['items'][$oI]['subpreferences'][$key3]['id'] = @$mfop['sub_preference_id'];
                                                            $myOrdersList['items'][$oI]['subpreferences'][$key3]['subpreference_name'] = @$mfop['SubPreference']['name'];
                                                        } else {
                                                            $myOrdersList['items'][$oI]['subpreferences'] = array();
                                                        }
                                                    }
                                                } else {
                                                    $myOrdersList['items'][$oI]['subpreferences'] = array();
                                                }

                                                if (!empty($listOrderItem['OrderOffer'])) {
                                                    foreach ($listOrderItem['OrderOffer'] as $key4 => $mfOffer) {
                                                        $myOrdersList['items'][$oI]['OfferedItem'][$key4]['offered_item_id'] = @$mfOffer['offered_item_id'];
                                                        $myOrdersList['items'][$oI]['OfferedItem'][$key4]['name'] = @$mfOffer['Item']['name'];
                                                        $myOrdersList['items'][$oI]['OfferedItem'][$key4]['quantity'] = @$mfOffer['quantity'];
                                                    }
                                                } else {
                                                    $myOrdersList['items'][$oI]['OfferedItem'] = array();
                                                }
                                            }
                                        }


                                    }
                                    $discount=0;
                                   if(!empty($freeItemPrice)){
                                       //$discount=  array_sum($freeItemPrice);
                                   }
                                    $myOrdersList['discount']=number_format($discount,2);

                                   if(!empty($myOrdersList["coupon_discount"])){
                                       $price=$price-$myOrdersList["coupon_discount"];
                                   }
                                    if(!empty($myOrdersList["discount"])){
                                       $price=$price-$myOrdersList["discount"];
                                   }
                                   if(!empty($myOrdersList["service_amount"])){
                                       $price=$price-$myOrdersList["service_amount"];
                                   }
                                   if(!empty($myOrdersList["delivery_amount"])){
                                       $price=$price-$myOrdersList["delivery_amount"];
                                   }
                                   if(!empty($myOrdersList["tip"])){
                                       $price=$price-$myOrdersList["tip"];
                                   }
                                   if(!empty($myOrdersList["tax_price"])){
                                       $price=$price-$myOrdersList["tax_price"];
                                   }
                                   $subprice=number_format($price,2);
                                    $myOrdersList['subtotal']=(string)$subprice;

                                    //pr($myOrdersList);
                                    //die;
                                    $responsedata['message'] = "Success";
                                    $responsedata['response'] = 1;
                                    $responsedata['Order'] = $myOrdersList;
                                    //pr($responsedata);
                                    return json_encode($responsedata);
                                } else {
                                    $responsedata['message'] = "Please select an order.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not registered under this store.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     /*     * ******************************************************************************************
      @Function Name : updateOrderStatus
      @Description   : this function is used to change the status of particular order.
      @Author        : SmartData
      created:28/11/2016
     * ****************************************************************************************** */
    /* "Pending":"1", "In - Preparation":"2", "Ready For Delivery":"3", "On the way":"4", "Delivered":"5", "Ready for Pick up":"6", "Picked Up":"7", "Confirmed":"8", "Order not processed":"9"
Delivery :"Pending":"1", "In - Preparation":"2", "Ready For Delivery":"3", "Delivered":"5",  "Order not processed":"9"
Pick Up: "Pending":"1", "In - Preparation":"2", "Ready for Pick up":"6",, "Picked Up":"7",  "Order not processed":"9" */

    public function updateOrderStatus() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody = '{"store_id": "108","order_id":"1037","order_status_id":"3"}';
        //$headers['user_id'] = 'NzU';
        //$headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "order_update_status.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id)));
            if (!empty($merchantResult)) {
                $domain = $merchantResult['Merchant']['domain_name'];
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $this->User->bindModel(array(
                                    'hasMany' => array(
                                        'Permission' => array(
                                            'className' => 'Permission',
                                            'foreignKey' => 'user_id',
                                            'type' => 'INNER',
                                            'conditions' => array('Permission.is_active' => 1, 'Permission.is_deleted' => 0,'Permission.tab_id' => 12),
                                            'fields' => array('id', 'tab_id')
                                        ),
                                    )
                                ));
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
                    if(empty($userDet['Permission'])){
                            $responsedata['message'] = "You are not authorized to change this information.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url','address','city','state','zipcode','phone','store_name','email_id','store_url')));
                             $storeAddress=$storeResult['Store']['address']."<br>".$storeResult['Store']['city'].", ".$storeResult['Store']['state']." ".$storeResult['Store']['zipcode'];
                             $storePhone=$storeResult['Store']['phone'];
                            if (!empty($storeResult)) {
                                /*                                 * *******send mail only one user********* */

                                if (isset($requestBody['order_id']) && !empty($requestBody['order_id'])) {
                                    if (empty($requestBody['order_status_id'])) {
                                        $requestBody['order_status_id'] = 2;
                                    }
                                    $order_id = $requestBody['order_id'];
                                    $this->Order->id = $order_id;
                                    $this->Order->saveField("order_status_id", $requestBody['order_status_id']);
                                    if ($this->Order->id && $requestBody['order_status_id'] == 2) {    // Only for In-Preparation status
                                        //$storeData=$this->Store->fetchStorePrinterIP($storeID);
                                        //if(!empty($storeData['Store']['printer_location'])){
                                        //    $encryorderId = $this->Encryption->encode($this->Order->id);
                                        //    $this->PrintReceipt($encryorderId);
                                        //}
                                    }
                                    $this->loadModel('DeliveryAddress');
                                    $this->loadModel('OrderOffer');
                                    $this->loadModel('OrderItem');
                                    $this->loadModel('User');
                                    $this->User->bindModel(array('belongsTo' => array('CountryCode' => array('className' => 'CountryCode', 'foreignKey' => 'country_code_id', 'fields' => array('code')))), false);
                                    $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode' => array('className' => 'CountryCode', 'foreignKey' => 'country_code_id', 'fields' => array('code')))), false);
                                    $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')))), false);
                                    $this->OrderItem->bindModel(array('hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')))), false);
                                    $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id'))), 'belongsTo' => array('OrderPayment' => array('className' => 'OrderPayment', 'foreignKey' => 'payment_id', 'fields' => array('id', 'transection_id', 'amount')), 'DeliveryAddress' => array('className' => 'DeliveryAddress', 'foreignKey' => 'delivery_address_id'), 'User' => array('fields' => array('fname', 'lname', 'email', 'phone', 'is_smsnotification', 'is_emailnotification', 'country_code_id')), 'OrderStatus' => array('fields' => array('name')))), false);
                                    $orderDetails = $this->Order->find('first',array('recursive'=>3,'conditions'=>array('Order.merchant_id'=>$merchant_id,'Order.id'=>$order_id,'Order.store_id'=>$store_id,'Order.is_active'=>1,'Order.is_deleted'=>0)));

                                    //pr($orderDetails);
                                    $this->loadModel('EmailTemplate');
                                    if ($orderDetails['Order']['order_status_id'] == 2) {
                                        $template_type = 'order_receipt';
                                    } elseif ($orderDetails['Order']['order_status_id'] == 3) {
                                        $template_type = 'ready_for_delivery';
                                    } elseif ($orderDetails['Order']['order_status_id'] == 5) {
                                        $template_type = 'delivered';
                                    } elseif ($orderDetails['Order']['order_status_id'] == 6) {
                                        $template_type = 'ready_for_pickup';
                                    } elseif ($orderDetails['Order']['order_status_id'] == 9) {
                                        $template_type = 'order_not_processed';
                                    } else {
                                        $template_type = 'order_status';
                                    }
                                    $emailSuccess = $this->EmailTemplate->storeTemplates($store_id, $merchant_id, $template_type);

                                    if ($emailSuccess) {
                                        $emailData = $emailSuccess['EmailTemplate']['template_message'];
                                        $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                                        //$subject = $emailSuccess['EmailTemplate']['template_subject'];

                                        $fullName = $orderDetails['User']['fname'] . " " . $orderDetails['User']['lname'];
                                        $orderNumber = $orderDetails['Order']['order_number'];
                                        $status = $orderDetails['OrderStatus']['name'];

                                        if ($orderDetails['User']['is_emailnotification'] == 1) {
                                            $desc = '';
                                            $offers = '';
                                            $result = '';
                                            foreach ($orderDetails['OrderItem'] as $order) {
                                                $desc = $order['quantity'] . ' ' . @$order['Size']['size'] . ' ' . @$order['Type']['name'] . ' ' . $order['Item']['name'];
                                                if (!empty($order['OrderOffer'])) {
                                                    foreach ($order['OrderOffer'] as $offer) {
                                                        if(!empty($offer['Item']['name'])){
                                                            $offers .= $offer['quantity'] . 'X' . $offer['Item']['name'] . '&nbsp;';
                                                        }

                                                    }
                                                }
                                                if (!empty($offers)) {
                                                    $result .= $desc . ' ( Offer : ' . $offers . '), ';
                                                } else {
                                                    $result .= $desc . ', ';
                                                }
                                                $offers = '';
                                                $desc = '';
                                            }
                                            $emailData = str_replace('{ORDER_DETAIL}', $result, $emailData);
                                            $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                                            $emailData = str_replace('{ORDER_ID}', $orderNumber, $emailData);
                                            $emailData = str_replace('{ORDER_STATUS}', $status, $emailData);
                                            $emailData = str_replace('{TOTAL}', "$" . $orderDetails['OrderPayment']['amount'], $emailData);
                                            $emailData = str_replace('{TRANSACTION_ID}', $orderDetails['OrderPayment']['transection_id'], $emailData);
                                            $url = "http://" . $storeResult['Store']['store_url'];
                                            $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeResult['Store']['store_url'] . "</a>";
                                            $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);

                                            $emailData = str_replace('{STORE_NAME}', $storeResult['Store']['store_name'], $emailData);
                                            $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                                            $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);

                                            //$subject = ucwords(str_replace('_', ' ', $subject));

                                            $orderType = ($orderDetails['Order']['seqment_id'] == 2) ? "Pick-up" : "Delivery";
                                            $newSubject = "Your " . $storeResult['Store']['store_name'] . " Online Order Status #" . $orderDetails['Order']['order_number'] . "/" . $orderType;
//pr($emailData);
//die;
                                            $this->Email->to = $orderDetails['User']['email'];
                                            $this->Email->subject = $newSubject;
                                            $this->Email->from = $storeResult['Store']['email_id'];
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
                                        }

                                        if ($orderDetails['User']['is_smsnotification'] == 1) {
                                            $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                                            $smsData = str_replace('{ORDER_NUMBER}', $orderNumber, $smsData);
                                            $smsData = str_replace('{ORDER_STATUS}', $status, $smsData);
                                            $smsData = str_replace('{STORE_NAME}', $storeResult['Store']['store_name'], $smsData);
                                            $smsData = str_replace('{STORE_PHONE}', $storePhone, $smsData);
                                            $message = $smsData;
                                            if (!empty($orderDetails['DeliveryAddress']['phone'])) {
                                                $tonumber = $orderDetails['DeliveryAddress']['phone'];
                                            } else {
                                                $tonumber = $orderDetails['User']['phone'];
                                            }
                                            if (!empty($orderDetails['DeliveryAddress']['CountryCode']['code'])) {
                                                $mobnumber = $orderDetails['DeliveryAddress']['CountryCode']['code'] . "" . $tonumber;
                                            } else {
                                                $mobnumber = $orderDetails['User']['CountryCode']['code'] . "" . $tonumber;
                                            }
                                            $this->Webservice->sendSmsNotificationFront($mobnumber, $message,$store_id);
                                        }
                                    }

                                    $responsedata['message'] = "Order status updated successfully.";
                                    $responsedata['response'] = 1;
                                    //pr($responsedata);
                                    return json_encode($responsedata);
                                } else {
                                    $responsedata['message'] = "Please select an order.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not registered under this store.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

    /*     * ******************************************************************************************
      @Function Name : getStoresList
      @Description   : this function is used for get location Store List based on merchant id and User id
      @Author        : SmartData
      created:29/11/2016
     * ****************************************************************************************** */

    public function getStoresInfo() {
        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody = '{"store_id": "108"}';
        $this->Webservice->webserviceAdminLog($requestBody, "admin_store_info.txt", $headers);
        //$headers['user_id']="NzU";// Ekansh test server
        //$headers['user_id']="MzEw";
        //$headers['merchant_id']=1;
         $responsedata = array();
        $requestBody = json_decode($requestBody, true);
        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantCheck = $this->Merchant->find('first', array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantCheck)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $roleID = 3;
                    $user_id = $this->Encryption->decode($headers['user_id']);
if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
     $store_id = $requestBody['store_id'];
                    $this->Store->unBindModel(array('belongsTo' => array('StoreTheme')), false);
                    $this->Store->unBindModel(array('belongsTo' => array('StoreFont')), false);
                    $this->Store->unBindModel(array('hasOne' => array('SocialMedia')), false);
                    $this->Store->unBindModel(array('hasMany' => array('StoreContent')), false);
                    $this->Store->unBindModel(array('hasMany' => array('StoreGallery')), false);

                    $this->User->bindModel(array(
                        'hasMany' => array(
                            'Permission' => array(
                                'className' => 'Permission',
                                'foreignKey' => 'user_id',
                                'type' => 'INNER',
                                'conditions' => array('Permission.is_active' => 1, 'Permission.is_deleted' => 0, 'Permission.tab_id' => array(10, 12, 13, 14, 16, 47, 3)),
                                'fields' => array('id', 'tab_id')
                            ),
                        )
                    ));
                    $this->User->bindModel(array(
                        'belongsTo' => array(
                            'Store' => array(
                                'className' => 'Store',
                                'foreignKey' => 'store_id',
                                'type' => 'INNER',
                                'conditions' => array('Store.is_active' => 1, 'Store.is_deleted' => 0,'Store.id' => $store_id),
                                'fields' => array('id', 'store_name', 'address', 'city', 'state', 'store_logo', 'phone', 'zipcode', 'merchant_id', 'notification_number', 'notification_email','store_url','display_email','display_fax')
                            ),
                        )
                    ));
                    $locStoreList = $this->User->find("first", array('recursive' => 3, "conditions" => array("User.id" => $user_id, "User.role_id" => $roleID,"User.is_active" => 1, "User.is_deleted" => 0), 'fields' => array('email', 'password', 'fname', 'id', 'store_id', 'merchant_id', 'lname', 'phone', 'dateOfBirth', 'country_code_id', 'is_deleted', 'is_active', 'is_newsletter', 'is_emailnotification', 'is_smsnotification')));

                     $protocol = 'http://';
                                if (isset($_SERVER['HTTPS'])) {
                                    if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                                        $protocol = 'https://';
                                    }
                                }
                    if (!empty($locStoreList)) {
                            if (!empty($locStoreList['Store']['store_logo'])) {
                               $locStoreList['Store']['store_logo'] = $protocol . $locStoreList['Store']['store_url'] . "/storeLogo/" . $locStoreList['Store']['store_logo'];
                            } else {
                                $locStoreList['Store']['store_logo'] = "";
                            }

                            $currentDateStore=$this->Webservice->getcurrentTime($locStoreList['Store']['id'],1);
                            if(!empty($currentDateStore)){
                               $currentDate= $currentDateStore;
                            }else{
                               $currentDate= date("Y-m-d H:i:s");
                            }
                            $dateTime=  explode(" ", $currentDate);
                            $current_date = $dateTime[0];
                            $current_time = $dateTime[1];
                            if(!empty($current_date)){
                                $locStoreList['Store']['current_date']=$current_date;
                                $locStoreList['Store']['current_time']=$current_time;
                            }
                            if (!empty($locStoreList['Store']['phone'])) {
                                $storephone=$this->phoneNumberChange($locStoreList['Store']['phone']);
                                $locStoreList['Store']['phone'] = $storephone;
                            } else {
                                $locStoreList['Store']['phone'] = "";
                            }
                            if (!empty($locStoreList['Store']['notification_number'])) {
                                $notificationphone=$this->phoneNumberChange($locStoreList['Store']['notification_number']);
                                $locStoreList['Store']['notification_number'] = $notificationphone;
                            } else {
                                $locStoreList['Store']['notification_number'] = "";
                            }
                            if (!empty($locStoreList['Store']['notification_email'])) {
                                $locStoreList['Store']['notification_email'] = $locStoreList['Store']['notification_email'];
                            } else {
                                $locStoreList['Store']['notification_email'] = "";
                            }
                            if (!empty($locStoreList['Store']['display_email'])) {
                                $locStoreList['Store']['display_email'] = $locStoreList['Store']['display_email'];
                            } else {
                                $locStoreList['Store']['display_email'] = "";
                            }
                            if (!empty($locStoreList['Store']['display_fax'])) {
                                    $faxPhone=$this->phoneNumberChange($locStoreList['Store']['display_fax']);
                                    $locStoreList['Store']['display_fax'] = $faxPhone;
                            } else {
                                $locStoreList['Store']['display_fax'] = "";
                            }

                            $locStoreList['Store']['isCouponAllow'] = FALSE;
                            $locStoreList['Store']['isPromotionAllow'] = FALSE;
                            $locStoreList['Store']['isOrderAllow'] = FALSE;
                            $locStoreList['Store']['isReviewAllow'] = FALSE;
                            $locStoreList['Store']['isBookingAllow'] = FALSE;
                            $locStoreList['Store']['isItemOffersAllow'] = FALSE;
                            $locStoreList['Store']['isConfigurationAllow'] = FALSE;

                            if (!empty($locStoreList['Permission'])) {
                                foreach ($locStoreList['Permission'] as $p => $permission) {
                                    if ($permission['tab_id'] == 10) {
                                        $locStoreList['Store']['isCouponAllow'] = TRUE;
                                    } elseif ($permission['tab_id'] == 12) {
                                        $locStoreList['Store']['isPromotionAllow'] = TRUE;
                                    } elseif ($permission['tab_id'] == 13) {
                                        $locStoreList['Store']['isOrderAllow'] = TRUE;
                                    } elseif ($permission['tab_id'] == 14) {
                                        $locStoreList['Store']['isReviewAllow'] = TRUE;
                                    } elseif ($permission['tab_id'] == 16) {
                                        $locStoreList['Store']['isBookingAllow'] = TRUE;
                                    } elseif ($permission['tab_id'] == 47) {
;
                                        $locStoreList['Store']['isItemOffersAllow'] = TRUE;
                                    } elseif ($permission['tab_id'] == 3) {
;
                                        $locStoreList['Store']['isConfigurationAllow'] = TRUE;
                                    }
                                }
                            }
                            unset($locStoreList['User']);
                            unset($locStoreList['Permission']);
                        if (!empty($locStoreList)) {
                            $responsedata['message'] = "Success";
                            $responsedata['response'] = 1;
                            $responsedata['store'] =$locStoreList['Store'];
                            //pr($responsedata);
                            return json_encode($responsedata);
                            //return $this->json_message(1, 'Success', $listing);
                        } else {
                            $responsedata['message'] = "Store not found.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "No active user found.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                    } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }

                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     /*     * ******************************************************************************************
      @Function Name : updateStoresInfo
      @Description   : this function is used to Update Store information
      @Author        : SmartData
      created:29/11/2016
     * ****************************************************************************************** */

    public function updateStoresInfo() {
        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody = '{"store_id":"108","store_name":"Curry House Sandy","address":"21281 S Western Ave","notification_email":"ecomm@ebankcardservice.com","phone":"123456789","phone":"123456789","display_fax":"123456789"}';
        $this->Webservice->webserviceAdminLog($requestBody, "update_store_info.txt", $headers);
       // $headers['user_id']="NzU";// Ekansh test server
        //$headers['user_id']="MzEw";
        //$headers['merchant_id']=85;
        $responsedata = array();
        $requestBody = json_decode($requestBody, true);
        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantCheck = $this->Merchant->find('first', array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantCheck)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $roleID = 3;
                    $user_id = $this->Encryption->decode($headers['user_id']);

                    if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                        $store_id = $requestBody['store_id'];
                        $this->User->bindModel(array(
                                    'hasMany' => array(
                                        'Permission' => array(
                                            'className' => 'Permission',
                                            'foreignKey' => 'user_id',
                                            'type' => 'INNER',
                                            'conditions' => array('Permission.is_active' => 1, 'Permission.is_deleted' => 0,'Permission.tab_id' => 3),
                                            'fields' => array('id', 'tab_id')
                                        ),
                                    )
                                ));
                        $userDetail = $this->User->find("first", array('recursive' => 3, "conditions" => array("User.id" => $user_id, "User.role_id" => $roleID, "User.is_active" => 1, "User.is_deleted" => 0, "User.merchant_id" => $merchant_id, "User.store_id" => $store_id), 'fields' => array('id', 'store_id')));
                        if(empty($userDetail['Permission'])){
                            $responsedata['message'] = "You are not authorized to change this information.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }

                        if ($userDetail) {
                            if(empty($requestBody['store_name'])){
                                 $responsedata['message'] = "Please enter a store name.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                            if(empty($requestBody['address'])){
                                 $responsedata['message'] = "Please enter a store address.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                            if(empty($requestBody['notification_email'])){
                                 $responsedata['message'] = "Please enter a notification email.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                            if(empty($requestBody['phone'])){
                                $responsedata['message'] = "Please enter a phone number.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }

                            $this->Store->unBindModel(array('belongsTo' => array('StoreTheme')), false);
                            $this->Store->unBindModel(array('belongsTo' => array('StoreFont')), false);
                            $this->Store->unBindModel(array('hasOne' => array('SocialMedia')), false);
                            $this->Store->unBindModel(array('hasMany' => array('StoreContent')), false);
                            $this->Store->unBindModel(array('hasMany' => array('StoreGallery')), false);

                            $locStoreList = $this->Store->find("first", array('recursive' => 3, "conditions" => array("Store.id" => $store_id, "Store.is_active" => 1, "Store.is_deleted" => 0), 'fields' => array('id','phone')));

                            if (!empty($locStoreList)) {
                                $data['id']=$locStoreList['Store']['id'];
                                $data['store_name']=  trim($requestBody['store_name']);
                                $data['address']=trim($requestBody['address']);
                                $data['notification_email']=  strtolower(trim($requestBody['notification_email']));
                                $number=$locStoreList['Store']['phone'];
                                // Validate email
                                if (!filter_var($data['notification_email'], FILTER_VALIDATE_EMAIL) === false) {

                                } else {
                                    $responsedata['message'] = "Please enter a valid email.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                if (strlen($requestBody['phone']) > 20) {
                                    $responsedata['message'] = "Phone number should not be greater then 20 digits.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }

                                if(!empty($requestBody['phone'])){
                                    $phone =preg_replace("/[^0-9]/", "", $requestBody['phone']);
                                    $number = "(".substr($phone, 0, 3) .') '.
                                                    substr($phone, 3, 3) .'-'.
                                                    substr($phone, 6);
                                }
                                $data['phone']=$number;

                                $fax=$requestBody['display_fax'];
                                 if(!empty($requestBody['display_fax'])){
                                    $faxNumber =preg_replace("/[^0-9]/", "", $requestBody['display_fax']);
                                    $fax = "(".substr($faxNumber, 0, 3) .') '.
                                                    substr($faxNumber, 3, 3) .'-'.
                                                    substr($faxNumber, 6);
                                }
                                $data['display_fax']=$fax;
                                if($this->Store->save($data)){
                                    $responsedata['message'] = "Store configuration details successfully updated.";
                                    $responsedata['response'] = 1;
                                    $responsedata['store'] = $data;
                                    return json_encode($responsedata);
                                }else{
                                    $responsedata['message'] = "Store configuration details could not be updated, please try again.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "You are not registered under this merchant or store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "Please select a store.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "No active merchant found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

    /*     * ******************************************************************************************
      @Function Name : reservationList
      @Description   : this function is used for reservation Lisitng
      @Author        : SmartData
      created:01/12/2016
     * ****************************************************************************************** */

    public function reservationList() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody =  '{"date":"1/12/2016","reservation_type":"3","page_number":"1","store_id":"2"}'; //  date formate=dd/mm/yyyy "todayOrders": 1,  "pendingOrder": 2,  "preOrder": 3,
//        $headers['user_id'] = 'MzEw';
//        $headers['merchant_id'] = 1;
        $this->Webservice->webserviceAdminLog($requestBody, "reservation_list.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id)));
            if (!empty($merchantResult)) {
                $domain = $merchantResult['Merchant']['domain_name'];
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if(!empty($requestBody['date'])){
                                $date=$requestBody['date'];
                                }else{
                                    $date=date("d/m/Y");
                                }

                                $month=  explode('/', $date);
                                $current_month=$month[1];
                                $dateYMD=$month[2].'-'.$month[1].'-'.$month[0];
                                //echo $dateYMD."<br>";
                           if (empty($requestBody['page_number'])) {
                            $requestBody['page_number']=1;
                           }
                           $limit = 9;
                           $offset = $requestBody['page_number'] * 9 - 9;
                           $bookingsCount=0;
                                    $this->Booking->bindModel(array(
                                    'belongsTo' => array(
                                        'User' => array(
                                            'className' => 'User',
                                            'foreignKey' => 'user_id',
                                            'type' => 'INNER',
                                            'conditions' => array('User.is_active' => 1, 'User.is_deleted' => 0),
                                            'fields' => array('id', 'fname', 'lname', 'email')
                                        ),
                                    )
                                ),FALSE);

                                if($requestBody['reservation_type']==1){//todaybooking ": 1,
                                      $bookingsCount = $this->Booking->find('count',array('conditions'=>array('Booking.store_id'=>$store_id,'Booking.is_active'=>1,'Booking.is_deleted'=>0,'Date(Booking.reservation_date)'=>$dateYMD)));
                                       $bookings = $this->Booking->find('all',array('recursive'=>1,'order'=>array('Booking.created DESC'), 'offset' => $offset, 'limit' => $limit,'conditions'=>array('Booking.store_id'=>$store_id,'Booking.is_active'=>1,'Booking.is_deleted'=>0,'Date(Booking.reservation_date)'=>$dateYMD)));

                                }elseif ($requestBody['reservation_type']==2) { // "pendingbooking": 2,
                                    $bookingsCount = $this->Booking->find('count',array('fields'=>array('id'),'conditions'=>array('Booking.store_id'=>$store_id,'Booking.is_active'=>1,'Date(Booking.reservation_date)'=>$dateYMD,'Booking.booking_status_id'=>1,'Booking.is_deleted'=>0)));

                                      $bookings =$this->Booking->find('all',array('recursive'=>1,'order'=>array('Booking.created DESC'),'offset' => $offset, 'limit' => $limit,'conditions'=>array('Booking.store_id'=>$store_id,'Booking.is_active'=>1,'Date(Booking.reservation_date)'=>$dateYMD,'Booking.booking_status_id'=>1,'Booking.is_deleted'=>0)));

                                }else{
                                    $bookingsCount = $this->Booking->find('count',array('conditions'=>array('Booking.store_id'=>$store_id,'Booking.is_active'=>1,'Booking.booking_status_id'=>1,'Booking.is_deleted'=>0)));
                                    $bookings =$this->Booking->find('all',array('recursive'=>1,'order'=>array('Booking.created DESC'),'offset' => $offset, 'limit' => $limit,'conditions'=>array('Booking.store_id'=>$store_id,'Booking.is_active'=>1,'Booking.is_deleted'=>0)));
                                }
                            // pr($bookings);
                              $myBookingsList=array();
                              if (!empty($bookings)) {
                                    $o = 0;
                                    foreach ($bookings as $listOrder) {
                                        $myBookingsList[$o]['booking_id'] = $listOrder['Booking']['id'];
                                        if (!empty($listOrder['Booking']['number_person'])) {
                                            $myBookingsList[$o]['number_person'] = $listOrder['Booking']['number_person'];
                                        } else {
                                            $myBookingsList[$o]['number_person'] = "";
                                        }
                                        if (!empty($listOrder['Booking']['special_request'])) {
                                            $myBookingsList[$o]['special_request'] = $listOrder['Booking']['special_request'];
                                        } else {
                                            $myBookingsList[$o]['special_request'] = "";
                                        }

                                         if (!empty($listOrder['User']['fname'])) {
                                            $myBookingsList[$o]['name'] = $listOrder['User']['fname']." ".$listOrder['User']['lname'] ;
                                        } else {
                                            $myBookingsList[$o]['name'] = "";
                                        }


                                        if (!empty($listOrder['User']['email'])) {
                                            $myBookingsList[$o]['email'] = $listOrder['User']['email'];
                                        } else {
                                            $myBookingsList[$o]['email'] = "";
                                        }

                                        if ($listOrder['Booking']['is_replied']==1) {
                                            $myBookingsList[$o]['is_replied'] = TRUE;
                                        } else {
                                            $myBookingsList[$o]['is_replied'] = FALSE;
                                        }
                                         if (!empty($listOrder['Booking']['admin_comment'])) {
                                            $myBookingsList[$o]['admin_comment'] = $listOrder['Booking']['admin_comment'];
                                        } else {
                                            $myBookingsList[$o]['admin_comment'] = "";
                                        }

                                        if (!empty($listOrder['Booking']['reservation_date'])) {
                                            $dateTime = explode(" ", $listOrder['Booking']['reservation_date']);
                                            $dateResBooking=explode("-", $dateTime[0]);
                                            $finalResdate=$dateResBooking[2].'/'.$dateResBooking[1].'/'.$dateResBooking[0];
                                            $myBookingsList[$o]['date'] = $finalResdate;
                                            $myBookingsList[$o]['time'] = $dateTime[1];
                                        } else {
                                            $myBookingsList[$o]['date'] = "";
                                            $myBookingsList[$o]['time'] = "";
                                        }

                                        if (!empty($listOrder['Booking']['created'])) {
                                            $placedDateTime = explode(" ", $listOrder['Booking']['created']);
                                            $dateBooking=explode("-", $placedDateTime[0]);
                                            $finaldate=$dateBooking[2].'/'.$dateBooking[1].'/'.$dateBooking[0];
                                            $myBookingsList[$o]['placed_date'] = $finaldate;
                                            $myBookingsList[$o]['placed_time'] = $placedDateTime[1];
                                        } else {
                                            $myBookingsList[$o]['placed_date'] = "";
                                            $myBookingsList[$o]['placed_date'] = "";
                                        }

                                        if (!empty($listOrder['Booking']['booking_status_id'])) {
                                            if($listOrder['Booking']['booking_status_id']==1){
                                                $myBookingsList[$o]['booking_status']="Pending";
                                            }elseif($listOrder['Booking']['booking_status_id']==4){
                                                $myBookingsList[$o]['booking_status']="Cancel";
                                            }elseif($listOrder['Booking']['booking_status_id']==5){
                                                $myBookingsList[$o]['booking_status']="Booked";
                                            }else{
                                            $myBookingsList[$o]['booking_status']="";
                                        }

                                        }else{
                                            $myBookingsList[$o]['booking_status']="";
                                        }
                                        $o++;
                                    }
                                }

                                $responsedata['message'] = "Success";
                                $responsedata['response'] = 1;
                                if(!empty($bookingsCount)){
                                    if($bookingsCount>10){
                                        $responsedata['count'] = (string)ceil($bookingsCount/10);
                                    }else{
                                        $responsedata['count'] = "1";
                                    }

                                }else{
                                   $responsedata['count'] = "0";
                                }

                                $responsedata['Bookings'] = array_values($myBookingsList);
                                //pr($responsedata);
                                return json_encode($responsedata);

                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not registered under this store.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     /*     * ******************************************************************************************
      @Function Name : reservationList
      @Description   : this function is used for reservation Lisitng
      @Author        : SmartData
      created:01/12/2016
     * ****************************************************************************************** */

    public function reservationCount() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody =  '{"month":"12/11/2016","page_number":"1","store_id":"2"}'; //  date formate=dd/mm/yyyy "todayOrders": 1,  "pendingOrder": 2,  "preOrder": 3,
//        $headers['user_id'] = 'MzEw';
//        $headers['merchant_id'] = 1;
        $this->Webservice->webserviceAdminLog($requestBody, "reservation_list.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id)));
            if (!empty($merchantResult)) {
                $domain = $merchantResult['Merchant']['domain_name'];
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if(!empty($requestBody['month'])){
                                $date=$requestBody['month'];
                                }else{
                                    $date=date("d/m/Y");
                                }

                                $month=  explode('/', $date);
                                $current_month=$month[1];
                                $current_year=$month[2];
                                $dateYMD=$month[2].'-'.$month[1].'-'.$month[0];
                            $conditions=array('Booking.store_id'=>$store_id,'Booking.is_active'=>1,'Booking.is_deleted'=>0,'Month(Booking.reservation_date)'=>$current_month,'Year(Booking.reservation_date)'=>$current_year);
                             $options = array(
                                    'conditions' => $conditions,
                                    'fields'=>array('COUNT(`id`) as `count`','date(`reservation_date`) as `reservationDate`'),
                                    'group' => 'date(`reservation_date`)',
                                );

                               $bookings=  $this->Booking->find('all', $options);
                              $myBookingsList=array();
//                              {"12/12/2016":"5","13/12/2016":"10"}
                              if (!empty($bookings)) {
                                    $o = 0;
                                    foreach ($bookings as $listOrder) {
                                        $date=  explode("-", $listOrder[0]['reservationDate']);
                                        $reservationDate=$date[2].'/'.$date[1].'/'.$date[0];
                                        $myBookingsList[$reservationDate]=$listOrder[0]['count'];
                                    }
                                }
                                $responsedata['message'] = "Success";
                                $responsedata['response'] = 1;
                                $responsedata['Bookings'] = $myBookingsList;
                                //pr($responsedata);
                                return json_encode($responsedata);

                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

    /*     * ******************************************************************************************
      @Function Name : updateReservation
      @Description   : this function is used for update the status of reservation.
      @Author        : SmartData
      created:05/12/2016
     * ****************************************************************************************** */

     public function updateReservation() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody = '{"booking_id":"149","store_id":"2","booking_status_id":"5","special_request":"ABC"}'; //"Pending": 1,  "Cancel": 4,  "Booked": 5,
//        $headers['user_id'] = 'MzEw';
//        $headers['merchant_id'] = 1;
        $this->Webservice->webserviceAdminLog($requestBody, "update_reservation.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $this->User->bindModel(array(
                                    'hasMany' => array(
                                        'Permission' => array(
                                            'className' => 'Permission',
                                            'foreignKey' => 'user_id',
                                            'type' => 'INNER',
                                            'conditions' => array('Permission.is_active' => 1, 'Permission.is_deleted' => 0,'Permission.tab_id' => 16),
                                            'fields' => array('id', 'tab_id')
                                        ),
                                    )
                                ));
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if(empty($userDet['Permission'])){
                            $responsedata['message'] = "You are not authorized to change this information.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url', 'Store.phone', 'Store.store_name', 'Store.address', 'Store.city', 'Store.state', 'Store.zipcode', 'Store.email_id','Store.store_url')));
                            if (!empty($storeResult)) {
                                if (isset($requestBody['booking_id']) && !empty($requestBody['booking_id'])) {
                                    $this->Booking->bindModel(
                                            array(
                                        'belongsTo' => array(
                                            'User' => array(
                                                'className' => 'User',
                                                'foreignKey' => 'user_id',
                                                'fields' => array('id', 'email', 'fname', 'lname', 'country_code_id', 'phone', 'is_emailnotification', 'is_smsnotification'),
                                            ), 'BookingStatus' => array(
                                                'className' => 'BookingStatus',
                                                'foreignKey' => 'booking_status_id',
                                                'fields' => array('id', 'name', 'is_active')
                                            )
                                        ),
                                            )
                                            , false
                                    );
                                    $booking = $this->Booking->find('first', array('recursive' => 3, 'conditions' => array('Booking.id' => $requestBody['booking_id']), 'fields' => array('id', 'number_person', 'special_request', 'user_id', 'is_replied', 'booking_status_id', 'reservation_date')));
                                    $bookingStatusArr=array(1,4,5);
                                    if(!in_array($requestBody['booking_status_id'], $bookingStatusArr)){
                                        $responsedata['message'] = "Please select a status.";
                                        $responsedata['response'] = 0;
                                        return json_encode($responsedata);
                                    }
                                    $data['id'] = $requestBody['booking_id'];
                                    $template_type = 'booking_status';
                                    if (empty($requestBody['booking_status_id'])) {
                                        $requestBody['booking_status_id'] = 1;
                                    }
                                    $template_type = 'booking_status';
                                    $comment="";
                                    switch ($requestBody['booking_status_id']) {
                                        case "1":
                                            $status = 'Pending';
                                            if(!empty($requestBody['special_request'])){
                                                $comment = trim($requestBody['special_request']);
                                                }
                                            break;
                                        case "2":
                                            $status = 'Available';
                                            break;
                                        case "3":
                                            $status = 'Not Available';
                                            break;
                                        case "4":
                                            $status = 'Cancel';
                                            $template_type = 'cancel_booking';
                                            if(!empty($requestBody['special_request'])){
                                                $comment ="Admin comment: ";
                                                $comment = trim($requestBody['special_request']);
                                                }
                                            break;
                                        default:
                                            $status = 'Booked';
                                            $template_type = 'confirm_booking';
                                            if(!empty($requestBody['special_request'])){
                                                $comment ="Admin comment: ";
                                                $comment = trim($requestBody['special_request']);
                                                }
                                    }
                                    $data['booking_status_id'] = $requestBody['booking_status_id'];
                                    $data['is_replied'] = 1;
                                    $data['admin_comment'] = trim($requestBody['special_request']);
                                    if ($this->Booking->save($data)) {
                                        $fullName = trim($booking['User']['fname'] . ' ' . $booking['User']['lname']);
                                        $order = $requestBody['booking_id'];
                                        $number = $booking['Booking']['number_person'];
                                        $datetime = $booking['Booking']['reservation_date'];
                                        $st = $booking['BookingStatus']['name'];
                                        $this->loadModel('EmailTemplate');

                                        $userCountryCode = $this->CountryCode->find('first', array('fields' => array('id', 'code'), 'conditions' => array('CountryCode.id' => $booking['User']['country_code_id'])));
                                        $emailSuccess = $this->EmailTemplate->storeTemplates($store_id, $merchant_id, $template_type);
                                        if ($emailSuccess) {
                                            $emailData = $emailSuccess['EmailTemplate']['template_message'];
                                            $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                                            $subject = $emailSuccess['EmailTemplate']['template_subject'];
                                        }
                                        //pr($emailData);
                                        //die;
                                        if ($booking['User']['is_emailnotification'] == 1) {



                                            $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                                            $emailData = str_replace('{STATUS}', $status, $emailData);
                                            $emailData = str_replace('{BOOKING_PEOPLE}', $number, $emailData);
                                            $emailData = str_replace('{BOOKING_DATE_TIME}', date('m-d-Y H:i a', strtotime($datetime)), $emailData);
                                            $url = "http://" . $storeResult['Store']['store_url'];
                                            $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeResult['Store']['store_url'] . "</a>";
                                            $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);

                                            $emailData = str_replace('{STORE_PHONE}', $storeResult['Store']['phone'], $emailData);
                                            $emailData = str_replace('{STORE_NAME}', $storeResult['Store']['store_name'], $emailData);
                                            $storeAddress = $storeResult['Store']['address'] . "<br>" . $storeResult['Store']['city'] . ", " . $storeResult['Store']['state'] . " " . $storeResult['Store']['zipcode'];
                                            $storePhone = $storeResult['Store']['phone'];
                                            $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                                            $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);
                                            $emailData = str_replace('{COMMENT}', $comment, $emailData);
                                            $subject = ucwords(str_replace('_', ' ', $subject));
                                            $this->Email->to = $booking['User']['email'];
                                            $this->Email->subject = $subject;
                                            $this->Email->from = $storeResult['Store']['email_id'];
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
                                        }
                                        if ($booking['User']['is_smsnotification'] == 1) {
                                            /*                                             * ************sms gateway data************* */
                                            $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                                            $smsData = str_replace('{REQUEST_ID}', $order, $smsData);
                                            $smsData = str_replace('{BOOKING_STATUS}', $status, $smsData);
                                            $smsData = str_replace('{STORE_NAME}', $storeResult['Store']['store_name'], $smsData);
                                            $smsData = str_replace('{STORE_PHONE}', $storePhone, $smsData);
                                            /*                                             * ***********end sms gateway data********** */
                                            $message = $smsData;
                                            $mob = $userCountryCode['CountryCode']['code'] . "" . str_replace(array('(', ')', ' ', '-'), '', $booking['User']['phone']);
                                            $this->Common->sendSmsNotification($mob, $message);
                                        }
                                        $responsedata['message'] = "Reservation has been updated successfully.";
                                        $responsedata['response'] = 1;
                                        return json_encode($responsedata);
                                    } else {
                                        $responsedata['message'] = "Reservation request could not be changed, Please try again.";
                                        $responsedata['response'] = 0;
                                        return json_encode($responsedata);
                                    }
                                } else {
                                    $responsedata['message'] = "Please select a booking.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     /*     * ******************************************************************************************
      @Function Name : imageGallary
      @Description   : this function is used for listing Store image Gallary
      @Author        : SmartData
      created:05/12/2016
     * ****************************************************************************************** */

    public function imageGallery() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody =  '{"page_number":"1","store_id":"108"}'; //  date formate=dd/mm/yyyy "todayOrders": 1,  "pendingOrder": 2,  "preOrder": 3,
        //$headers['user_id'] = 'MzEw';
        //$headers['merchant_id'] = 1;
        $this->Webservice->webserviceAdminLog($requestBody, "image_gallery.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id),'fields'=>array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {

                            if (empty($requestBody['page_number'])) {
                                $requestBody['page_number']=1;
                            }
                           $limit = 9;
                           $offset = $requestBody['page_number'] * 9 - 9;
                                $storeCountImages = $this->StoreReviewImage->find('count',array('conditions'=>array('StoreReviewImage.store_id'=>$store_id,'StoreReviewImage.store_review_id'=>0,'StoreReviewImage.is_deleted'=>0),'fields'=>array('id')));
                                $storeReviewImages = $this->StoreReviewImage->find('all',array('order' => 'StoreReviewImage.created DESC','offset' => $offset, 'limit' => $limit,'conditions'=>array('StoreReviewImage.store_id'=>$store_id,'StoreReviewImage.store_review_id'=>0,'StoreReviewImage.is_deleted'=>0),'fields'=>array('id','image','is_active')));
                                $imageGallary=array();
                                if (!empty($storeCountImages)) {
                                    if ($storeCountImages > 10) {
                                        $responsedata['count'] = (string)ceil($storeCountImages / 10);
                                    } else {
                                        $responsedata['count'] = "1";
                                    }
                                } else {
                                    $responsedata['count'] = "0";
                                }
                                if (!empty($storeReviewImages)) {
                                     $protocol = 'http://';
                                    if (isset($_SERVER['HTTPS'])) {
                                        if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                                            $protocol = 'https://';
                                        }
                                    }
                                    //pr($storeReviewImages);
                                    $i = 0;
                                    foreach ($storeReviewImages as $reviewImages) {
                                        $imageGallary[$i]['id'] = $reviewImages['StoreReviewImage']['id'];
                                        if (!empty( $reviewImages['StoreReviewImage']['image'])) {
                                             $imageGallary[$i]['image'] = $protocol . $storeResult['Store']['store_url'] . "/storeReviewImage/thumb/" . $reviewImages['StoreReviewImage']['image'];
                                        } else {
                                             $imageGallary[$i]['image'] = "";
                                        }
                                        if ($reviewImages['StoreReviewImage']['is_active'] == 1) {
                                            $imageGallary[$i]['status'] = 'active';
                                             $imageGallary[$i]['activeStatus'] = true;
                                        } else {
                                            $imageGallary[$i]['status'] = 'deactive';
                                            $imageGallary[$i]['activeStatus'] = false;
                                        }
                                        $i++;
                                    }
                                }

                                $responsedata['message'] = "Success.";
                                $responsedata['response'] = 1;
                                $responsedata['galleryImages'] = array_values($imageGallary);
                                //pr($responsedata);
                                return json_encode($responsedata);

                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }
     /*     * ******************************************************************************************
      @Function Name : uploadGallaryImages
      @Description   : this function is used for upload Store Gallary images.
      @Author        : SmartData
      created:05/12/2016
     * ****************************************************************************************** */

   public function uploadGalleryImages() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        $this->Webservice->webserviceAdminLog($requestBody, "upload_images.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if(empty($requestBody['images'])){
                                    $responsedata['message'] = "Please select images.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                $data['image'] = array_filter($requestBody['images']);
                                if (!empty($data['image'])) {
                                    $storeReviewImages=array();
                                    if (count($data['image']) <= 5) {
                                        foreach ($data['image'] as $key => $val) {

										$dat = explode(';', $val);
										$type = $dat[0];
										$data2 = $dat[1];
										$dat2 = explode(',', $data2);
										//list(, $data1) = explode(',', $data1);
										$data3 = base64_decode($dat2[1]);
										$jpgMimes=array('image/jpeg','image/pjpeg');
										$pngMimes=array('image/png');
										$gifMimes=array('image/gif');

										$imgdata = base64_decode($dat2[1]);
										$f = finfo_open();
										$mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
										if(in_array($mime_type,$jpgMimes)){
											$imageType="jpg";
											$imagename = uniqid() . "_" . date('Y-m-d-H-s') . '_' . $store_id . '_image.jpg';
										}else if(in_array($mime_type,$pngMimes)){
											$imageType="png";
											$imagename = uniqid() . "_" . date('Y-m-d-H-s') . '_' . $store_id . '_image.png';
										}else if(in_array($mime_type,$gifMimes)){
											$imageType="gif";
											$imagename = uniqid() . "_" . date('Y-m-d-H-s') . '_' . $store_id . '_image.gif';
										}else{
											$responsedata['message'] = "Only jpg, gif, png type images are allowed.";
											$responsedata['response'] = 0;
											return json_encode($responsedata);
										}
										$path = WWW_ROOT . "/storeReviewImage/" . $imagename;
										$folderName = "/storeReviewImage";
										$newWidth = 300;
										$newHeight = 190;

                                            if ($imagename) {
                                                file_put_contents($path, $data3);
                                                $this->Webservice->cropImage($path, $folderName,$imagename,$newWidth,$newHeight,$imageType);
                                                $imageData['image'] = $imagename;
                                                $imageData['store_id'] = $store_id;
                                                $imageData['created'] = date("Y-m-d H:i:s");
                                                $imageData['is_active'] = 1;
                                                $imageData['store_review_id'] = 0;
                                                $this->StoreReviewImage->create();
                                                $this->StoreReviewImage->save($imageData);
                                                $storeReviewImages[$key] = $this->StoreReviewImage->getLastInsertId();
                                            }


                                        }
                                    } else {
                                        $responsedata['message'] = "You can upload upto 5 images.";
                                        $responsedata['response'] = 0;
                                        return json_encode($responsedata);
                                    }
                                }

                                $imageGallary=array();
                                if(!empty($storeReviewImages)){
                                     $storeReviewList = $this->StoreReviewImage->find('all',array('order' => 'StoreReviewImage.created DESC','conditions'=>array('StoreReviewImage.store_id'=>$store_id,'StoreReviewImage.store_review_id'=>0,'StoreReviewImage.is_deleted'=>0,'StoreReviewImage.id'=>$storeReviewImages),'fields'=>array('id','image','is_active')));
                                      if (!empty($storeReviewList)) {
                                     $protocol = 'http://';
                                    if (isset($_SERVER['HTTPS'])) {
                                        if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                                            $protocol = 'https://';
                                        }
                                    }
                                    //pr($storeReviewImages);
                                    $i = 0;
                                    foreach ($storeReviewList as $reviewImages) {
                                        $imageGallary[$i]['id'] = $reviewImages['StoreReviewImage']['id'];
                                        if (!empty( $reviewImages['StoreReviewImage']['image'])) {
                                             $imageGallary[$i]['image'] = $protocol . $storeResult['Store']['store_url'] . "/storeReviewImage/" . $reviewImages['StoreReviewImage']['image'];
                                        } else {
                                             $imageGallary[$i]['image'] = "";
                                        }
                                        if ($reviewImages['StoreReviewImage']['is_active'] == 1) {
                                            $imageGallary[$i]['status'] = 'active';
                                             $imageGallary[$i]['activeStatus'] = true;
                                        } else {
                                            $imageGallary[$i]['status'] = 'deactive';
                                            $imageGallary[$i]['activeStatus'] = false;
                                        }
                                        $i++;
                                    }
                                }

                                }
                                $responsedata['message'] = "Success.";
                                $responsedata['response'] = 1;
                                $responsedata['galleryImages'] = array_values($imageGallary);
                                return json_encode($responsedata);
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

    /*     * ******************************************************************************************
      @Function Name : removeGalleryImage
      @Description   : this function is used to remove slected Gallery images.
      @Author        : SmartData
      created:05/12/2016
     * ****************************************************************************************** */

    public function removeGalleryImage() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody = '{"store_id":"108", "image_id":"130"}';
//        $headers['user_id'] = 'NzU';
//        $headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "remove_images.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if (empty($requestBody['image_id'])) {
                                    $responsedata['message'] = "Please select a image.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                $review_id = $requestBody['image_id'];
                                $resultReview = $this->StoreReviewImage->updateAll(array('StoreReviewImage.is_deleted' => 1), array('StoreReviewImage.id' => $review_id,'StoreReviewImage.store_id' => $store_id));
                                if ($resultReview) {
                                    $responsedata['message'] = "Gallery image has been deleted successfully.";
                                    $responsedata['response'] = 1;
                                    return json_encode($responsedata);
                                } else {
                                    $responsedata['message'] = "Gallery image could not be deleted, please try again.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     /*     * ******************************************************************************************
      @Function Name : updateGalleryStatus
      @Description   : this function is used to update  status Gallery images active/deactive.//true=>1 false=>0;
      @Author        : SmartData
      created        : 05/12/2016
     * ****************************************************************************************** */

    public function updateGalleryStatus() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody = '{"store_id":"108", "image_id":"130", "status":false}'; //true=>1 false=>0;
//        $headers['user_id'] = 'NzU';
//        $headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "update_gallery.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if (empty($requestBody['image_id'])) {
                                    $responsedata['message'] = "Please select a image.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                $review_id = $requestBody['image_id'];
                                $resultReview = $this->StoreReviewImage->find('first', array('conditions' => array('StoreReviewImage.id' => $review_id, 'StoreReviewImage.store_id' => $store_id)));
                                if (empty($resultReview)) {
                                    $responsedata['message'] = "Image not found.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }

                                if ($requestBody['status']) {
                                    $data['is_active'] = 1;
                                } else {
                                    $data['is_active'] = 0;
                                }
                                $data['id'] = $resultReview['StoreReviewImage']['id'];
                                $result = $this->StoreReviewImage->save($data);
                                if ($result) {
                                    $responsedata['message'] = "Gallery image has been updated successfully.";
                                    $responsedata['response'] = 1;
                                    return json_encode($responsedata);
                                } else {
                                    $responsedata['message'] = "Gallery image could not be updated, please try again.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     /*     * ******************************************************************************************
      @Function Name : storeReviewListing
      @Description   : this function is used for listing Store Reviews
      @Author        : SmartData
      created:05/12/2016
     * ****************************************************************************************** */

    public function storeReviewListing() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody = '{"page_number":"1","store_id":"108"}';
//        $headers['user_id'] = 'NzU';
//        $headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "store_review_listing.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();
        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantCheck = $this->Merchant->find('first', array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantCheck)) {

                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $roleid = 3;
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                $store_url=$storeResult['Store']['store_url'];
                                $this->StoreReview->bindModel(array(
                                    'hasMany' => array(
                                        'StoreReviewImage' => array(
                                            'className' => 'StoreReviewImage',
                                            'foreignKey' => 'store_review_id',
                                            'fields' => array('id', 'image'),
                                            'type' => 'INNER',
                                            'conditions' => array('StoreReviewImage.is_deleted' => 0, 'StoreReviewImage.is_active' => 1)
                                        ))
                                        ), false);
                                $this->OrderItem->bindModel(array(
                                    'belongsTo' => array(
                                        'Order' => array(
                                            'className' => 'Order',
                                            'foreignKey' => 'order_id',
                                            'fields' => array('id', 'order_number'),
                                            'type' => 'INNER',
                                            'conditions' => array('Order.is_deleted' => 0, 'Order.is_active' => 1)
                                        ))
                                        ), false);

                                $this->OrderItem->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name', 'image')))), false);
                                $this->StoreReview->bindModel(array('belongsTo' => array('User' => array('fields' => array('fname', 'lname')), 'OrderItem' => array('foreignKey' => 'order_item_id', 'fields' => array('item_id','order_id')))), false);
                                if (empty($requestBody['page_number'])) {
                                $requestBody['page_number']=1;
                            }
                            $limit = 9;
                            $offset = $requestBody['page_number'] * 9 - 9;
                            $allReviewsCount = $this->StoreReview->find('count', array('recursive' => 2, 'order' => array('StoreReview.created DESC'), 'conditions' => array('StoreReview.merchant_id' => $merchant_id,'StoreReview.store_id' => $store_id, 'StoreReview.is_active' => 1, 'StoreReview.is_deleted' => 0)));
                                $allReviews = $this->StoreReview->find('all', array('offset' => $offset, 'limit' => $limit,'recursive' => 2, 'order' => array('StoreReview.created DESC'), 'conditions' => array('StoreReview.merchant_id' => $merchant_id,'StoreReview.store_id' => $store_id, 'StoreReview.is_active' => 1, 'StoreReview.is_deleted' => 0)));
                                // pr($allReviews);
                                $protocol = 'http://';
                                if (isset($_SERVER['HTTPS'])) {
                                    if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                                        $protocol = 'https://';
                                    }
                                }
                                $reviewArr = array();
                                if (!empty($allReviews)) {
                                    $i = 0;
                                    foreach ($allReviews as $reviewsAll) {
                                        $reviewArr[$i]['review_id'] = $reviewsAll['StoreReview']['id'];
                                        if (!empty($reviewsAll['StoreReview']['review_rating'])) {
                                            $reviewArr[$i]['review_rating'] = $reviewsAll['StoreReview']['review_rating'];
                                        } else {
                                            $reviewArr[$i]['review_rating'] = 0;
                                        }

                                        if (!empty($reviewsAll['StoreReview']['review_comment'])) {
                                            $reviewArr[$i]['review_comment'] = $reviewsAll['StoreReview']['review_comment'];
                                        } else {
                                            $reviewArr[$i]['review_comment'] = "";
                                        }


                                        $reviewArr[$i]['user_name'] = $reviewsAll['User']['fname'] . " " . $reviewsAll['User']['lname'];
                                        if (!empty($reviewsAll['OrderItem']['order_id'])) {
                                             $reviewArr[$i]['order_id'] = $reviewsAll['OrderItem']['Order']['id'];
                                            $reviewArr[$i]['order_number'] = $reviewsAll['OrderItem']['Order']['order_number'];
                                        } else {
                                            $reviewArr[$i]['order_id'] = "";
                                            $reviewArr[$i]['order_number'] = "";
                                        }
                                        if (!empty($reviewsAll['OrderItem']['item_id'])) {
                                            $reviewArr[$i]['item_name'] = $reviewsAll['OrderItem']['Item']['name'];
                                        } else {
                                            $reviewArr[$i]['item_name'] = "";
                                        }
                                        if (!empty($reviewsAll['StoreReviewImage'])) {
                                            $img = 0;
                                            foreach ($reviewsAll['StoreReviewImage'] as $StoreReviewImage) {
                                                if (!empty($StoreReviewImage['image'])) {
                                                    $reviewArr[$i]['image'][$img] = $protocol . $store_url . "/storeReviewImage/thumb/" . $StoreReviewImage['image'];
                                                } else {
                                                    $reviewArr[$i]['image'][$img] = $protocol . $store_url . "/storeReviewImage/" . 'no_image.jpeg';
                                                }
                                                $img++;
                                            }
                                        } else {
                                            $reviewArr[$i]['image'] = array();
                                        }
                                         if(!empty($storeResult['Store']['id']))
                                        $dateTime = $this->Webservice->storeTimezone($storeResult['Store']['id'], $reviewsAll['StoreReview']['created'], true);
                                        $reviewArr[$i]['created'] = $dateTime;
                                        $i++;
                                    }
                                } else {
                                    $reviewArr = array();
                                }

                                if (!empty($allReviewsCount)) {
                                    if ($allReviewsCount > 10) {
                                        $responsedata['count'] = (string)ceil($allReviewsCount / 10);
                                    } else {
                                        $responsedata['count'] = "1";
                                    }
                                } else {
                                    $responsedata['count'] = "0";
                                }
                                //pr($reviewArr);
                                $responsedata['message'] = "Success";
                                $responsedata['response'] = 1;
                                $responsedata['review'] = array_values($reviewArr);
                                //pr($responsedata);
                                return json_encode($responsedata);
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not registered under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }
       /*     * ******************************************************************************************
      @Function Name : searchReview
      @Description   : this function is used for Searching with rating and order number
      @Author        : SmartData
      created:07/12/2016
     * ****************************************************************************************** */

     public function searchReview() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody = '{"review_rating":"5","keyword":"","store_id":"108"}'; Keyword could be Order_number or Review
        //$headers['user_id'] = 'NzU';
        //$headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "search_review.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();
        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantCheck = $this->Merchant->find('first', array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantCheck)) {

                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $roleid = 3;
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                $store_url = $storeResult['Store']['store_url'];
                                $criteria = "StoreReview.store_id =$store_id AND StoreReview.is_deleted=0";

                                if (!empty($requestBody['keyword'])) {
                                    $value = trim($requestBody['keyword']);
                                    $criteria .= " AND (StoreReview.review_comment LIKE '%" . $value . "%' OR Order.order_number LIKE '%" . $value . "%' OR Order.id LIKE '%" . $value . "%')";
                                }


                                if ($requestBody['review_rating'] != '') {
                                    $rating = trim($requestBody['review_rating']);
                                    $criteria .= " AND (StoreReview.review_rating =$rating)";
                                }

                                 $this->OrderItem->bindModel(array('belongsTo' => array('Item' => array('className' => 'Item', 'foreignKey' => 'item_id', 'fields' => 'name'))), false);
                                $this->loadModel('StoreReview');
                                $this->StoreReview->bindModel(array('belongsTo' => array('Order' => array('className' => 'Order', 'foreignKey' => 'order_id', 'fields' => array('id','order_number','order_number')), 'OrderItem' => array('className' => 'OrderItem', 'foreignKey' => 'order_item_id', 'fields' => array('id','order_id','item_id'))), 'hasMany' => array('StoreReviewImage' => array('className' => 'StoreReviewImage', 'foreignKey' => 'store_review_id', 'fields' => array('id')))), false);
                                $this->StoreReview->bindModel(array('hasMany' => array('StoreReviewImage' => array('className' => 'StoreReviewImage', 'foreignKey' => 'store_review_id', 'fields' => array('id','image')))), false);
                                $this->StoreReview->bindModel(array('belongsTo' => array('User' => array('className' => 'User', 'foreignKey' => 'user_id','fields' => array('fname', 'lname')))), false);


                                $allReviewsCount = $this->StoreReview->find('count', array('conditions' => array($criteria),'fields'=>array('id')));
                                $allReviews = $this->StoreReview->find('all', array('order' => array('StoreReview.created' => 'DESC'), 'recursive' => 4, 'conditions' => array($criteria)));
                                $protocol = 'http://';
                                if (isset($_SERVER['HTTPS'])) {
                                    if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                                        $protocol = 'https://';
                                    }
                                }
                                $reviewArr = array();
                                if (!empty($allReviews)) {
                                    $i = 0;
                                    foreach ($allReviews as $reviewsAll) {
                                        $reviewArr[$i]['review_id'] = $reviewsAll['StoreReview']['id'];
                                        if (!empty($reviewsAll['StoreReview']['review_rating'])) {
                                            $reviewArr[$i]['review_rating'] = $reviewsAll['StoreReview']['review_rating'];
                                        } else {
                                            $reviewArr[$i]['review_rating'] = 0;
                                        }

                                        if (!empty($reviewsAll['StoreReview']['review_comment'])) {
                                            $reviewArr[$i]['review_comment'] = $reviewsAll['StoreReview']['review_comment'];
                                        } else {
                                            $reviewArr[$i]['review_comment'] = "";
                                        }


                                        $reviewArr[$i]['user_name'] = $reviewsAll['User']['fname'] . " " . $reviewsAll['User']['lname'];

                                        if (!empty($reviewsAll['Order']['id'])) {
                                            $reviewArr[$i]['order_id'] = $reviewsAll['Order']['id'];
                                            $reviewArr[$i]['order_number'] = $reviewsAll['Order']['order_number'];
                                        } else {
                                            $reviewArr[$i]['order_id'] = "";
                                            $reviewArr[$i]['order_number'] = "";
                                        }
                                        if (!empty($reviewsAll['OrderItem']['item_id'])) {
                                            $reviewArr[$i]['item_name'] = $reviewsAll['OrderItem']['Item']['name'];
                                        } else {
                                            $reviewArr[$i]['item_name'] = "";
                                        }
                                        if (!empty($reviewsAll['StoreReviewImage'])) {
                                            $img = 0;
                                            foreach ($reviewsAll['StoreReviewImage'] as $StoreReviewImage) {
                                                if (!empty($StoreReviewImage['image'])) {
                                                    $reviewArr[$i]['image'][$img] = $protocol . $store_url . "/storeReviewImage/" . $StoreReviewImage['image'];
                                                } else {
                                                    $reviewArr[$i]['image'][$img] = $protocol . $store_url . "/storeReviewImage/" . 'no_image.jpeg';
                                                }
                                                $img++;
                                            }
                                        } else {
                                            $reviewArr[$i]['image'] = array();
                                        }
                                        if (!empty($storeResult['Store']['id']))
                                            $dateTime = $this->Webservice->storeTimezone($storeResult['Store']['id'], $reviewsAll['StoreReview']['created'], true);
                                        $reviewArr[$i]['created'] = $dateTime;
                                        $i++;
                                    }
                                } else {
                                    $reviewArr = array();
                                }

                                if (!empty($allReviewsCount)) {
                                    if ($allReviewsCount > 10) {
                                        $responsedata['count'] = (string)ceil($allReviewsCount / 10);
                                    } else {
                                        $responsedata['count'] = "1";
                                    }
                                } else {
                                    $responsedata['count'] = "0";
                                }
                                //pr($reviewArr);
                                $responsedata['message'] = "Success";
                                $responsedata['response'] = 1;
                                $responsedata['review'] = array_values($reviewArr);
                                //pr($responsedata);
                                return json_encode($responsedata);
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not registered under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     /*     * ******************************************************************************************
      @Function Name : orderList
      @Description   : this function is used for Order Lisitng
      @Author        : SmartData
      created:06/12/2016
     * ****************************************************************************************** */

     public function searchOrder() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//$requestBody = '{"order_statusid":"","order_type":"Home Delivery","store_id":"108","keyword":"M123456-120516002","date":"06/12/2016","preorder":true}'; // keyword could be any order number or user name or email or phone" order_number":"M123456-120516016","user_name":"Ranjeet","email":"rjsaini@mailinator.com","phone":"2132","page_number":"1"}'; order_statusid => Pending =>1, In - Preparation=>2, Ready For Delivery=>3, On the way=>4, Delivered=>5, Ready for Pick up=>6, Picked Up=>7, Confirmed=>8, Order not processed=>9
        //$headers['user_id'] = 'NzU';
        //$headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "search_order.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id)));
            if (!empty($merchantResult)) {
                $domain = $merchantResult['Merchant']['domain_name'];
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                $value = "";
                                $criteria = "Order.store_id =$store_id AND Order.is_active=1 AND Order.is_deleted=0 AND Order.is_future_order=0";

                                if (!empty($requestBody['keyword'])) {
                                    $value = trim($requestBody['keyword']);
                                   $criteria .= " AND (Order.order_number LIKE '%" . $value . "%' OR Order.id LIKE '%" . $value . "%' OR User.fname LIKE '%" . $value . "%' OR User.lname LIKE '%" . $value . "%' OR User.email LIKE '%" . $value . "%' OR DeliveryAddress.phone LIKE '%" . $value . "%' OR DeliveryAddress.name_on_bell LIKE '%" . $value . "%')";
                                }
                                if (!empty($requestBody['order_statusid'])) {
                                    $orderStatusID = trim($requestBody['order_statusid']);
                                    $criteria .= " AND (Order.order_status_id =$orderStatusID)";
                                }

                                 if (!empty($requestBody['date'])) {
                                        $date = $requestBody['date'];
                                        $month = explode('/', $date);
                                        $current_month = $month[1];
                                        $dateYMD = $month[2] . '-' . $month[1] . '-' . $month[0];
                                 }
                                 if ($requestBody['preorder']) {

                                    $criteria .= " AND (Order.is_pre_order =1)";
                                    if (!empty($dateYMD)) {
                                        $criteria .= " AND DATE(Order.pickup_time) >='" . $dateYMD . "'";
                                    } else {
                                        $date = $this->Webservice->getcurrentTime($store_id, 2);
                                        $month = explode('-', $date);
                                        $dateformate = explode('-', $date);
                                        $dateConverted = $dateformate[2] . '-' . $dateformate[1] . '-' . $dateformate[0];
                                        $criteria .= " AND DATE(Order.pickup_time) >='" . $date . "'";
                                    }
                                } else {
                                    if (!empty($dateYMD)) {
                                        $criteria .= " AND Date(Order.pickup_time)='" . $dateYMD . "'";
                                    }
                                }

                                if (!empty($requestBody['order_type'])) {
                                    $orderType=  strtolower(trim($requestBody['order_type']));
                                            if($orderType =='home delivery'){
                                                $segment_id=3;
                                            }else{
                                                $segment_id=2;
                                            }
                                    $criteria .= " AND (Order.seqment_id =$segment_id)";
                                }

                                $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);

                                $this->Order->bindModel(array(
                                    'belongsTo' => array(
                                        'DeliveryAddress' => array(
                                            'fields' => array('name_on_bell', 'city', 'address', 'email', 'phone')),
                                        'OrderStatus' => array('fields' => array('name')),
                                        'User' => array('fields' => array('fname', 'lname', 'email', 'phone','city','address'))
                                    )), false);
                                $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
                                $this->Order->bindModel(array(
                                    'belongsTo' => array(
                                        'Store' => array(
                                            'className' => 'Store',
                                            'foreignKey' => 'store_id',
                                            'fields' => array('id', 'store_name', 'store_url'),
                                            'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1)
                                        ))
                                        ), false);
                                //if (empty($requestBody['page_number'])) {
                                //    $requestBody['page_number'] = 1;
                                //}
                                //$limit = 9;
                                //$offset = $requestBody['page_number'] * 9 - 9;
                                //
                                //echo $criteria;

                                $OrderCount = $this->Order->find('count', array('order' => 'Order.created DESC', 'recursive' => 3, 'conditions' => $criteria, 'fields' => array('Order.id', 'Order.order_number', 'Order.amount', 'Order.pickup_time', 'Order.seqment_id', 'Order.store_id', 'Order.delivery_address_id', 'Order.order_status_id', 'Order.created','Order.user_id')));
                                $Order = $this->Order->find('all', array('order' => 'Order.created DESC', 'recursive' => 3, 'conditions' => $criteria, 'fields' => array('Order.id', 'Order.order_number', 'Order.amount', 'Order.pickup_time', 'Order.seqment_id', 'Order.store_id', 'Order.delivery_address_id', 'Order.order_status_id', 'Order.created','Order.user_id')));
                                //pr($Order);
                                $myOrdersList = array();
                                if (!empty($Order)) {
                                    $o = 0;
                                    foreach ($Order as $listOrder) {
                                        $myOrdersList[$o]['order_id'] = $listOrder['Order']['id'];
                                        $myOrdersList[$o]['order_number'] = $listOrder['Order']['order_number'];
                                        $myOrdersList[$o]['total_amount'] = '$' . $listOrder['Order']['amount'];
                                        $dateTime = explode(" ", $listOrder['Order']['pickup_time']);
                                        $myOrdersList[$o]['date'] = $dateTime[0];
                                        $myOrdersList[$o]['time'] = $dateTime[1];
                                        $placedDateTime = explode(" ", $listOrder['Order']['created']);
                                        $myOrdersList[$o]['placed_date'] = $placedDateTime[0];
                                        $myOrdersList[$o]['placed_time'] = $placedDateTime[1];
                                        //$myOrdersList[$o]['pickup_date'] = $listOrder['Order']['pickup_time'];
                                        //$myOrdersList[$o]['order_placed'] = $listOrder['Order']['created'];
                                        if (!empty($listOrder['Order']['seqment_id']) && $listOrder['Order']['seqment_id'] == 2) {
                                            $myOrdersList[$o]['order_type'] = 'Take Away';
                                        } elseif (!empty($listOrder['Order']['seqment_id']) && $listOrder['Order']['seqment_id'] == 3) {
                                            $myOrdersList[$o]['order_type'] = 'Home Delivery';
                                        }
                                        if (!empty($listOrder['DeliveryAddress']['name_on_bell'])) {
                                            $myOrdersList[$o]['name_on_bell'] = $listOrder['DeliveryAddress']['name_on_bell'];
                                        } elseif(!empty($listOrder['User']['fname'])) {
                                            $myOrdersList[$o]['name_on_bell'] = $listOrder['User']['fname']." ".$listOrder['User']['lname'];
                                        }else{
                                            $myOrdersList[$o]['name_on_bell'] = "";
                                        }
                                        if (!empty($listOrder['DeliveryAddress']['city'])) {
                                            $myOrdersList[$o]['city'] = $listOrder['DeliveryAddress']['city'];
                                        }elseif(!empty($listOrder['User']['city'])) {
                                            $myOrdersList[$o]['city']  = $listOrder['User']['city'];
                                        } else {
                                            $myOrdersList[$o]['city'] = "";
                                        }
                                        if (!empty($listOrder['DeliveryAddress']['address'])) {
                                            $myOrdersList[$o]['address'] = $listOrder['DeliveryAddress']['address'];
                                        }elseif(!empty($listOrder['User']['address'])) {
                                            $myOrdersList[$o]['address']  = $listOrder['User']['address'];
                                        } else {
                                            $myOrdersList[$o]['address'] = "";
                                        }



                                        $myOrdersList[$o]['OrderStatus'] = $listOrder['OrderStatus']['name'];
                                        $myOrdersList[$o]['store_id'] = $listOrder['Store']['id'];
                                        $myOrdersList[$o]['store_name'] = $listOrder['Store']['store_name'];
                                        $o++;
                                    }
                                }

                                $responsedata['message'] = "Success";
                                $responsedata['response'] = 1;
                                if (!empty($OrderCount)) {
                                    if ($OrderCount > 10) {
                                        $responsedata['count'] = (string)ceil($OrderCount / 10);
                                    } else {
                                        $responsedata['count'] = "1";
                                    }
                                } else {
                                    $responsedata['count'] = "0";
                                }

                                $responsedata['Order'] = array_values($myOrdersList);
                                //pr($responsedata);
                                return json_encode($responsedata);
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not registered under this store.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }
    /*     * ******************************************************************************************
      @Function Name : reservationList
      @Description   : this function is used for reservation Lisitng
      @Author        : SmartData
      created:06/12/2016
     * ****************************************************************************************** */

    public function searchReservation() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody = '{"booking_statusid":"1","booking_type":"","store_id":"108","keyword":"","date":"06/12/2016","isMonth":true}'; // keyword could be any booking id":"328","user_name":"Ranjeet"  booking_statusid => Pending =>1, Available=>2, Cancel=>3, Booked=>5 . booking_type replied=>1 Not Replied=>2
        //$headers['user_id'] = 'NzU';
        //$headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "search_reservation.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id)));
            if (!empty($merchantResult)) {
                $domain = $merchantResult['Merchant']['domain_name'];
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                $value = "";
                                $criteria = "Booking.store_id =$store_id AND Booking.is_active=1 AND Booking.is_deleted=0 AND User.role_id IN ('4','5')";

                                if (!empty($requestBody['keyword'])) {
                                    $value = trim($requestBody['keyword']);
                                    //$criteria .= " AND (User.fname LIKE '%" . $value . "%' OR User.lname LIKE '%" . $value . "%' OR Booking.id LIKE '%" . $value . "%' )";
					$criteria .= " AND (User.fname LIKE '%" . $value . "%' OR User.lname LIKE '%" . $value . "%' OR Booking.id LIKE '%" . $value . "%' OR CONCAT(User.fname,' ' ,User.lname) LIKE '%" . $value . "%')";
                                }
                                if (!empty($requestBody['booking_type'])) {
                                    if ($requestBody['booking_type'] == 1) {
                                        $repliedID = trim($requestBody['booking_type']);
                                    }
                                    if ($requestBody['booking_type'] == 2) {
                                        $repliedID = 0;
                                    }
                                    $criteria .= " AND (Booking.is_replied =$repliedID)";
                                }

                                if (!empty($requestBody['booking_statusid'])) {
                                    $status = trim($requestBody['booking_statusid']);
                                    $criteria .= " AND (Booking.booking_status_id =$status)";
                                }

                                 if (!empty($requestBody['date'])) {
                                    $date = $requestBody['date'];
                                    $month = explode('/', $date);
                                    $current_month = $month[1];
                                    $dateYMD = $month[2] . '-' . $month[1] . '-' . $month[0];
                                    if ($requestBody['isMonth']) {
                                        $criteria .= " AND Month(Booking.reservation_date)='" . $month[1] . "'";
                                    } else {
                                        $criteria .= " AND Date(Booking.reservation_date)='" . $dateYMD . "'";
                                    }
                                }

                                $bookingsCount = 0;
                                $this->Booking->bindModel(array(
                                    'belongsTo' => array(
                                        'User' => array(
                                            'className' => 'User',
                                            'foreignKey' => 'user_id',
                                            'type' => 'INNER',
                                            'conditions' => array('User.is_active' => 1, 'User.is_deleted' => 0),
                                            'fields' => array('id', 'fname', 'lname', 'email')
                                        ),
                                    )
                                        ), FALSE);
                                //echo $criteria;
                                $bookingsCount = $this->Booking->find('count', array('conditions' => $criteria));
                                $bookings = $this->Booking->find('all', array('recursive' => 1, 'order' => array('Booking.reservation_date DESC'), 'conditions' => $criteria));

                                $myBookingsList = array();
                                if (!empty($bookings)) {
                                    $o = 0;
                                    foreach ($bookings as $listOrder) {
                                        $myBookingsList[$o]['booking_id'] = $listOrder['Booking']['id'];
                                        if (!empty($listOrder['Booking']['number_person'])) {
                                            $myBookingsList[$o]['number_person'] = $listOrder['Booking']['number_person'];
                                        } else {
                                            $myBookingsList[$o]['number_person'] = "";
                                        }
                                        if (!empty($listOrder['Booking']['special_request'])) {
                                            $myBookingsList[$o]['special_request'] = $listOrder['Booking']['special_request'];
                                        } else {
                                            $myBookingsList[$o]['special_request'] = "";
                                        }

                                        if (!empty($listOrder['User']['fname'])) {
                                            $myBookingsList[$o]['name'] = $listOrder['User']['fname'] . " " . $listOrder['User']['lname'];
                                        } else {
                                            $myBookingsList[$o]['name'] = "";
                                        }

                                        if (!empty($listOrder['User']['email'])) {
                                            $myBookingsList[$o]['email'] = $listOrder['User']['email'];
                                        } else {
                                            $myBookingsList[$o]['email'] = "";
                                        }

                                        if ($listOrder['Booking']['is_replied'] == 1) {
                                            $myBookingsList[$o]['is_replied'] = TRUE;
                                        } else {
                                            $myBookingsList[$o]['is_replied'] = FALSE;
                                        }
                                        if (!empty($listOrder['Booking']['admin_comment'])) {
                                            $myBookingsList[$o]['admin_comment'] = $listOrder['Booking']['admin_comment'];
                                        } else {
                                            $myBookingsList[$o]['admin_comment'] = "";
                                        }

                                        if (!empty($listOrder['Booking']['reservation_date'])) {
                                            $dateTime = explode(" ", $listOrder['Booking']['reservation_date']);
                                            $dateResBooking = explode("-", $dateTime[0]);
                                            $finalResdate = $dateResBooking[2] . '/' . $dateResBooking[1] . '/' . $dateResBooking[0];
                                            $myBookingsList[$o]['date'] = $finalResdate;
                                            $myBookingsList[$o]['time'] = $dateTime[1];
                                        } else {
                                            $myBookingsList[$o]['date'] = "";
                                            $myBookingsList[$o]['time'] = "";
                                        }

                                        if (!empty($listOrder['Booking']['created'])) {
                                            $placedDateTime = explode(" ", $listOrder['Booking']['created']);
                                            $dateBooking = explode("-", $placedDateTime[0]);
                                            $finaldate = $dateBooking[2] . '/' . $dateBooking[1] . '/' . $dateBooking[0];
                                            $myBookingsList[$o]['placed_date'] = $finaldate;
                                            $myBookingsList[$o]['placed_time'] = $placedDateTime[1];
                                        } else {
                                            $myBookingsList[$o]['placed_date'] = "";
                                            $myBookingsList[$o]['placed_date'] = "";
                                        }

                                        if (!empty($listOrder['Booking']['booking_status_id'])) {
                                            if ($listOrder['Booking']['booking_status_id'] == 1) {
                                                $myBookingsList[$o]['booking_status'] = "Pending";
                                            } elseif ($listOrder['Booking']['booking_status_id'] == 4) {
                                                $myBookingsList[$o]['booking_status'] = "Cancel";
                                            } elseif ($listOrder['Booking']['booking_status_id'] == 5) {
                                                $myBookingsList[$o]['booking_status'] = "Booked";
                                            } else {
                                                $myBookingsList[$o]['booking_status'] = "";
                                            }
                                        } else {
                                            $myBookingsList[$o]['booking_status'] = "";
                                        }
                                        $o++;
                                    }
                                }

                                $responsedata['message'] = "Success";
                                $responsedata['response'] = 1;
                                if (!empty($bookingsCount)) {
                                    if ($bookingsCount > 10) {
                                        $responsedata['count'] = number_format($bookingsCount / 10);
                                    } else {
                                        $responsedata['count'] = "1";
                                    }
                                } else {
                                    $responsedata['count'] = "0";
                                }

                                $responsedata['Bookings'] = array_values($myBookingsList);
                                //pr($responsedata);
                                return json_encode($responsedata);
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not registered under this store.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     //Montly Reservation List

    /*     * ******************************************************************************************
      @Function Name : reservationMontlyList
      @Description   : this function is used for reservation Lisitng by Month
      @Author        : SmartData
      created:06/12/2016
     * ****************************************************************************************** */

    public function reservationByMonthList() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody =  '{"date":"1/12/2016","page_number":"1","store_id":"108"}'; //  date formate=dd/mm/yyyy "todayOrders": 1,  "pendingOrder": 2,  "preOrder": 3,
        //$headers['user_id'] = 'NzU';
        //$headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "reservation_list_month.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id)));
            if (!empty($merchantResult)) {
                $domain = $merchantResult['Merchant']['domain_name'];
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if(!empty($requestBody['date'])){
                                $date=$requestBody['date'];
                                }else{
                                    $date=date("d/m/Y");
                                }
                                $month=  explode('/', $date);
                                $current_month=$month[1];
                                $current_year=$month[2];
                                //echo $dateYMD."<br>";
                           if (empty($requestBody['page_number'])) {
                            $requestBody['page_number']=1;
                           }
                           $limit = 9;
                           $offset = $requestBody['page_number'] * 9 - 9;
                           $bookingsCount=0;
                                    $this->Booking->bindModel(array(
                                    'belongsTo' => array(
                                        'User' => array(
                                            'className' => 'User',
                                            'foreignKey' => 'user_id',
                                            'type' => 'INNER',
                                            'conditions' => array('User.is_active' => 1, 'User.is_deleted' => 0),
                                            'fields' => array('id', 'fname', 'lname', 'email')
                                        ),
                                    )
                                ),FALSE);

                                $bookingsCount = $this->Booking->find('count',array('conditions'=>array('Booking.store_id'=>$store_id,'Booking.is_active'=>1,'Booking.is_deleted'=>0,'Month(Booking.reservation_date)'=>$current_month,'Year(Booking.reservation_date)'=>$current_year)));
                                $bookings = $this->Booking->find('all',array('recursive'=>1,'order'=>array('Booking.created DESC'), 'offset' => $offset, 'limit' => $limit,'conditions'=>array('Booking.store_id'=>$store_id,'Booking.is_active'=>1,'Booking.is_deleted'=>0,'Month(Booking.reservation_date)'=>$current_month,'Year(Booking.reservation_date)'=>$current_year)));

                              $myBookingsList=array();
                              if (!empty($bookings)) {
                                    $o = 0;
                                    foreach ($bookings as $listOrder) {
                                        $myBookingsList[$o]['booking_id'] = $listOrder['Booking']['id'];
                                        if (!empty($listOrder['Booking']['number_person'])) {
                                            $myBookingsList[$o]['number_person'] = $listOrder['Booking']['number_person'];
                                        } else {
                                            $myBookingsList[$o]['number_person'] = "";
                                        }
                                        if (!empty($listOrder['Booking']['special_request'])) {
                                            $myBookingsList[$o]['special_request'] = $listOrder['Booking']['special_request'];
                                        } else {
                                            $myBookingsList[$o]['special_request'] = "";
                                        }

                                         if (!empty($listOrder['User']['fname'])) {
                                            $myBookingsList[$o]['name'] = $listOrder['User']['fname']." ".$listOrder['User']['lname'] ;
                                        } else {
                                            $myBookingsList[$o]['name'] = "";
                                        }

                                        if (!empty($listOrder['User']['email'])) {
                                            $myBookingsList[$o]['email'] = $listOrder['User']['email'];
                                        } else {
                                            $myBookingsList[$o]['email'] = "";
                                        }

                                        if ($listOrder['Booking']['is_replied']==1) {
                                            $myBookingsList[$o]['is_replied'] = TRUE;
                                        } else {
                                            $myBookingsList[$o]['is_replied'] = FALSE;
                                        }
                                         if (!empty($listOrder['Booking']['admin_comment'])) {
                                             $myBookingsList[$o]['admin_comment'] = $listOrder['Booking']['admin_comment'];
                                        } else {
                                             $myBookingsList[$o]['admin_comment'] = "";
                                        }

                                        if (!empty($listOrder['Booking']['reservation_date'])) {
                                            $dateTime = explode(" ", $listOrder['Booking']['reservation_date']);
                                            $dateResBooking=explode("-", $dateTime[0]);
                                            $finalResdate=$dateResBooking[2].'/'.$dateResBooking[1].'/'.$dateResBooking[0];
                                            $myBookingsList[$o]['date'] = $finalResdate;
                                            $myBookingsList[$o]['time'] = $dateTime[1];
                                        } else {
                                            $myBookingsList[$o]['date'] = "";
                                            $myBookingsList[$o]['time'] = "";
                                        }

                                        if (!empty($listOrder['Booking']['created'])) {
                                            $placedDateTime = explode(" ", $listOrder['Booking']['created']);
                                            $dateBooking=explode("-", $placedDateTime[0]);
                                            $finaldate=$dateBooking[2].'/'.$dateBooking[1].'/'.$dateBooking[0];
                                            $myBookingsList[$o]['placed_date'] = $finaldate;
                                            $myBookingsList[$o]['placed_time'] = $placedDateTime[1];
                                        } else {
                                            $myBookingsList[$o]['placed_date'] = "";
                                            $myBookingsList[$o]['placed_date'] = "";
                                        }

                                        if (!empty($listOrder['Booking']['booking_status_id'])) {
                                            if($listOrder['Booking']['booking_status_id']==1){
                                                $myBookingsList[$o]['booking_status']="pending";
                                            }elseif($listOrder['Booking']['booking_status_id']==4){
                                                $myBookingsList[$o]['booking_status']="cancel";
                                            }elseif($listOrder['Booking']['booking_status_id']==5){
                                                $myBookingsList[$o]['booking_status']="booked";
                                            }else{
                                            $myBookingsList[$o]['booking_status']="";
                                        }

                                        }else{
                                            $myBookingsList[$o]['booking_status']="";
                                        }
                                        $o++;
                                    }
                                }

                                $responsedata['message'] = "Success";
                                $responsedata['response'] = 1;
                                if(!empty($bookingsCount)){
                                    if($bookingsCount>10){
                                        $responsedata['count'] = number_format($bookingsCount/10);
                                    }else{
                                        $responsedata['count'] = "1";
                                    }

                                }else{
                                   $responsedata['count'] = "0";
                                }

                                $responsedata['Bookings'] = array_values($myBookingsList);
                                //pr($responsedata);
                                return json_encode($responsedata);

                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not registered under this store.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

    /*     * ******************************************************************************************
      @Function Name : removeStoreReview
      @Description   : this function is used to remove selected Review images.
      @Author        : SmartData
      created:05/12/2016
     * ****************************************************************************************** */

    public function removeStoreReview() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody = '{"store_id":"108", "review_id":"130"}';
//        $headers['user_id'] = 'NzU';
//        $headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "remove_images.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if (empty($requestBody['review_id'])) {
                                    $responsedata['message'] = "Please select a review.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                $review_id = $requestBody['review_id'];
                                $resultReview = $this->StoreReview->updateAll(array('StoreReview.is_deleted' => 1), array('StoreReview.id' => $review_id,'StoreReview.store_id' => $store_id));
                                if ($resultReview) {
                                    $storeReviewImages = $this->StoreReviewImage->find('all', array('conditions' => array('StoreReviewImage.store_review_id' => $review_id)));

                                    if (!empty($storeReviewImages)) {
                                        foreach ($storeReviewImages as $reviewImagesStore) {
                                            $data['StoreReviewImage']['id'] = $reviewImagesStore['StoreReviewImage']['id'];
                                            $data['StoreReviewImage']['is_deleted'] = 1;
                                            if ($this->StoreReviewImage->save($data)) {

                                            }
                                        }
                                    }
                                    $responsedata['message'] = "Review has been deleted successfully.";
                                    $responsedata['response'] = 1;
                                    return json_encode($responsedata);
                                } else {
                                    $responsedata['message'] = "Review could not be deleted, please try again.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     /*     * ******************************************************************************************
      @Function Name : couponsList
      @Description   : this function is used to List Coupons.
      @Author        : SmartData
      created:06/12/2016
     * ****************************************************************************************** */

    public function couponsList() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody = '{"store_id":"108", "isActive":"","page_number":"1"}'; //isActive=>1 show active isActive=>2 show deactive and if isActive=>0 all
//        $headers['user_id'] = 'NzU';
//        $headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "coupons_list.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                $domain=$storeResult['Store']['store_url'];
                                $criteria = "Coupon.store_id =$store_id AND Coupon.is_deleted=0";

                                if ($requestBody['isActive'] == 1) {
                                    $active = 1;
                                    $criteria .= " AND (Coupon.is_active =$active)";
                                } elseif ($requestBody['isActive'] == 2) {
                                    $active = 0;
                                    $criteria .= " AND (Coupon.is_active =$active)";
                                }

                                if (empty($requestBody['page_number'])) {
                                    $requestBody['page_number'] = 1;
                                }
                                $limit = 9;
                                $offset = $requestBody['page_number'] * 9 - 9;
                                $couponCount = $this->Coupon->find('count', array('conditions' => array($criteria), 'order' => array('Coupon.created' => 'DESC')));
                                $coupon = $this->Coupon->find('all', array('offset' => $offset, 'limit' => $limit,'conditions' => array($criteria), 'order' => array('Coupon.created' => 'DESC')));
//                                pr($couponCount);
                                //pr($coupon);
                                $copounArr=array();
                                if(!empty($coupon)){
                                    $protocol = 'http://';
                            if (isset($_SERVER['HTTPS'])) {
                                if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                                    $protocol = 'https://';
                                }
                            }
                                    $i=0;
                                    foreach($coupon as $couponsList){
                                       $copounArr[$i]['id']=$couponsList['Coupon']['id'];
                                       if(!empty($couponsList['Coupon']['name'])){
                                            $copounArr[$i]['name']=$couponsList['Coupon']['name'];
                                       }else{
                                            $copounArr[$i]['name']="";
                                       }
                                       if(!empty($couponsList['Coupon']['coupon_code'])){
                                            $copounArr[$i]['coupon_code']=$couponsList['Coupon']['coupon_code'];
                                       }else{
                                            $copounArr[$i]['coupon_code']="";
                                       }
                                       if(!empty($couponsList['Coupon']['number_can_use'])){
                                            $copounArr[$i]['number_can_use']=$couponsList['Coupon']['number_can_use'];
                                       }else{
                                            $copounArr[$i]['number_can_use']="0";
                                       }
                                        $copounArr[$i]['used_count']=$couponsList['Coupon']['used_count'];
                                       if(!empty($couponsList['Coupon']['used_count'])){
                                            $copounArr[$i]['used_count']=$couponsList['Coupon']['used_count'];
                                       }else{
                                           $copounArr[$i]['used_count']="0";
                                       }
                                       if(!empty($couponsList['Coupon']['start_date'])){
                                           $date=  explode("-", $couponsList['Coupon']['start_date']);
                                           $startDate=$date[2]."/".$date[1]."/".$date[0];
                                            $copounArr[$i]['start_date']=$startDate;
                                       }else{
                                           $copounArr[$i]['start_date']="";
                                       }
                                       if(!empty($couponsList['Coupon']['end_date'])){
                                           $dateEnd=  explode("-", $couponsList['Coupon']['end_date']);
                                           $endDate=$dateEnd[2]."/".$dateEnd[1]."/".$dateEnd[0];
                                            $copounArr[$i]['end_date']=$endDate;
                                       }else{
                                           $copounArr[$i]['end_date']="";
                                       }
                                       if(!empty($couponsList['Coupon']['is_active'])){
                                            $copounArr[$i]['is_active']=TRUE;
                                       }else{
                                           $copounArr[$i]['is_active']=FALSE;
                                       }
                                       if(!empty($couponsList['Coupon']['discount_type'])){
                                            $copounArr[$i]['discount_type']=$couponsList['Coupon']['discount_type'];
                                       }else{
                                           $copounArr[$i]['discount_type']="";
                                       }
                                       if(!empty($couponsList['Coupon']['discount'])){
                                            $copounArr[$i]['discount']=$couponsList['Coupon']['discount'];
                                       }else{
                                           $copounArr[$i]['discount']="";
                                       }


                                       if(!empty($couponsList['Coupon']['start_time'])){
                                            $copounArr[$i]['start_time']=$couponsList['Coupon']['start_time'];
                                       }else{
                                           $copounArr[$i]['start_time']="";
                                       }

                                       if(!empty($couponsList['Coupon']['end_time'])){
                                            $copounArr[$i]['end_time']=$couponsList['Coupon']['end_time'];
                                       }else{
                                           $copounArr[$i]['end_time']="";
                                       }

                                       if(!empty($couponsList['Coupon']['allow_time'])){
                                            $copounArr[$i]['allow_time']=$couponsList['Coupon']['allow_time'];
                                       }else{
                                           $copounArr[$i]['allow_time']="0";
                                       }

                                       if(!empty($couponsList['Coupon']['days'])){
                                            $copounArr[$i]['days']=$couponsList['Coupon']['days'];
                                       }else{
                                           $copounArr[$i]['days']="";
                                       }


                                      if ($couponsList['Coupon']['discount_type'] == 1) {
                                            $copounArr[$i]['coupon_detail'] = 'Use coupon code ' . $couponsList['Coupon']['coupon_code'] . ' get $' . $couponsList['Coupon']['discount'] . " Off.";
                                        } else if ($couponsList['Coupon']['discount_type'] == 2) {
                                            $copounArr[$i]['coupon_detail'] = 'Use coupon code ' . $couponsList['Coupon']['coupon_code'] . ' get ' . $couponsList['Coupon']['discount'] . "% Off.";
                                        }

                                        if (!empty($couponsList['Coupon']['image'])) {
                                                $copounArr[$i]['image'] = $protocol . $domain . "/Coupon-Image/" . $couponsList['Coupon']['image'];
                                        } else {

                                            $copounArr[$i]['image'] = "";
                                        }

                                        $userlist=array();
                                        $orderData = array();
                                        if (!empty($copounArr[$i]['used_count'])) {
                                            $couponName = $copounArr[$i]['coupon_code'];
                                            $this->loadModel('Order');
                                            $this->Order->bindModel(
                                                array(
                                                    'belongsTo' => array(
                                                        'User' => array(
                                                            'className' => 'User',
                                                            'foreignKey' => 'user_id',
                                                            'fields' => array('id','email','userName'),
                                                        ),
                                                        'DeliveryAddress' => array(
                                                            'className' => 'DeliveryAddress',
                                                            'foreignKey' => 'delivery_address_id',
                                                        )
                                                    )
                                                ), false
                                            );

                                            $criteria= " (Order.created BETWEEN '" . $couponsList['Coupon']['start_date'] . "' AND '" . $couponsList['Coupon']['end_date'] . "')";
                                            $criteria.= " AND LOWER(Order.coupon_code) ='" .strtolower($couponName)."'";
                                            $orderData = $this->Order->find('all',array('recursive'=>2,'fields' => array('Order.coupon_code', 'Order.order_number', 'Order.delivery_address_id', 'Order.user_id', 'DeliveryAddress.*','Order.created'), 'conditions' => $criteria));

                                    if(!empty($orderData)){
                                        foreach($orderData as $okey =>$ovalue){
                                            if(!empty($ovalue)){
                                                if (empty($ovalue['Order']['user_id'])) {
                                                    $index = $ovalue['DeliveryAddress']['email'];
                                                    if(in_array($ovalue['DeliveryAddress']['email'],$guestEmail)){
                                                        $userlist[$index]['usedCount']=$userlist[$index]['usedCount'] + 1;
                                                    }else{
                                                        $userlist[$index]['usedCount'] = 1;
                                                        $guestEmail[$okey]=$ovalue['DeliveryAddress']['email'];
                                                    }
                                                    $userlist[$index]['userID']="0";
                                                    $userlist[$index]['couponCode']=(isset($copounArr[$i]['coupon_code']))?$copounArr[$i]['coupon_code']:"";
                                                    $userlist[$index]['userName']=(isset($ovalue['DeliveryAddress']['name_on_bell']))?$ovalue['DeliveryAddress']['name_on_bell']:"";
                                                    $userlist[$index]['email']=(isset($ovalue['DeliveryAddress']['email']))?$ovalue['DeliveryAddress']['email']:"";
                                                    $userlist[$index]['date']=(isset($ovalue['Order']['created']))?date('m-d-Y', strtotime($ovalue['Order']['created'])):"";
                                                } else {
                                                    $index = $ovalue['User']['email'];
                                                    if (in_array($ovaluey['Order']['user_id'], $userIds)) {
                                                        $userlist[$index]['usedCount']=$userlist[$index]['usedCount']+1;
                                                    }else{
                                                        $userlist[$index]['usedCount']= 1;
                                                    }
                                                    $userlist[$index]['userID']=(isset($ovalue['Order']['user_id']))?$ovalue['Order']['user_id']:"";
                                                    $userlist[$index]['couponCode']=(isset($copounArr[$i]['coupon_code']))?$copounArr[$i]['coupon_code']:"";
                                                    $userlist[$index]['userName']=(isset($ovalue['User']['userName']))?$ovalue['User']['userName']:"";
                                                    $userlist[$index]['email']=(isset($ovalue['User']['email']))?$ovalue['User']['email']:"";
                                                    $userlist[$index]['date']=(isset($ovalue['Order']['created']))?date('m-d-Y', strtotime($ovalue['Order']['created'])):"";
                                                }
                                                $userIds[$index] = $orderDataAs['Order']['user_id'];
                                            }

                                        }
                                    }

                                        }
                                        $copounArr[$i]['userlist']=array_values($userlist);

                                    $i++;
                                    }

                                }

                                $responsedata['message'] = "Success.";
                                $responsedata['response'] = 1;
                                 if(!empty($couponCount)){
                                    if($couponCount>10){
                                        $responsedata['count'] = (string)ceil($couponCount/10);
                                    }else{
                                        $responsedata['count'] = "1";
                                    }

                                }else{
                                   $responsedata['count'] = "0";
                                }

                                $responsedata['Coupons'] = $copounArr;
                                //pr($responsedata);
                                return json_encode($responsedata);
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

    /*     * ******************************************************************************************
      @Function Name : addCoupons
      @Description   : this function is used to Add Coupons.
      @Author        : SmartData
      created:06/12/2016
     * ****************************************************************************************** */

    public function addCoupon() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        $this->Webservice->webserviceAdminLog($requestBody, "add_coupon.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if (empty($requestBody['name'])) {
                                    $responsedata['message'] = "Please enter a coupon title.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                if (empty($requestBody['coupon_code'])) {
                                    $responsedata['message'] = "Please enter a coupon code.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                if (empty($requestBody['discount_type'])) {
                                    $responsedata['discount_type'] = 1;
                                    return json_encode($responsedata);
                                }
                                if (empty($requestBody['discount'])) {
                                    $responsedata['message'] = "Please enter a discount.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                if (empty($requestBody['number_can_use'])) {
                                    $responsedata['number_can_use'] = 1;
                                }
                                if (empty($requestBody['start_date'])) {
                                    $responsedata['message'] = "Please enter a start date.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                if (empty($requestBody['end_date'])) {
                                    $responsedata['message'] = "Please enter a end date.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }

                                if ($requestBody['is_active']) {
                                    $requestBody['is_active'] = 1;
                                } else {
                                    $requestBody['is_active'] = 0;
                                }
                                $couponTitle = trim($requestBody['name']);
                                $couponCode = trim($requestBody['coupon_code']);
                                $isUniqueName = $this->Coupon->checkCouponUniqueName($couponTitle, $store_id);
                                $isUniqueCode = $this->Coupon->checkCouponUniqueCode($couponCode, $store_id);
                                if ($isUniqueName) {
                                    if ($isUniqueCode) {
                                        $imagename="";
                                          if (!empty($requestBody['image'])) {
                                            $dat = explode(';', $requestBody['image']);
                                            $type = $dat[0];
                                            $data2 = $dat[1];
                                            $dat2 = explode(',', $data2);
                                            //list(, $data1) = explode(',', $data1);
                                            $data3 = base64_decode($dat2[1]);
                                            $jpgMimes=array('image/jpeg','image/pjpeg');
                                            $pngMimes=array('image/png');
                                            $gifMimes=array('image/gif');

                                            $imgdata = base64_decode($dat2[1]);
                                            $f = finfo_open();
                                            $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
                                            if(in_array($mime_type,$jpgMimes)){
                                                    $imageType="jpg";
                                                    $imagename = uniqid() . "_" . date('Y-m-d-H-s') . '_' . $store_id . '_image.jpg';
                                            }else if(in_array($mime_type,$pngMimes)){
                                                    $imageType="png";
                                                    $imagename = uniqid() . "_" . date('Y-m-d-H-s') . '_' . $store_id . '_image.png';
                                            }else if(in_array($mime_type,$gifMimes)){
                                                    $imageType="gif";
                                                    $imagename = uniqid() . "_" . date('Y-m-d-H-s') . '_' . $store_id . '_image.gif';
                                            }else{
                                                    $responsedata['message'] = "Only jpg, gif, png type images are allowed.";
                                                    $responsedata['response'] = 0;
                                                    return json_encode($responsedata);
                                            }
                                            $path = WWW_ROOT . "/Coupon-Image/" . $imagename;
                                            $folderName = "/Coupon-Image";
                                            $newWidth = 480;
                                            $newHeight = 320;


                                            if ($imagename) {
                                                file_put_contents($path, $data3);
                                                $ImageStatus=$this->Webservice->cropImage($path, $folderName,$imagename,$newWidth,$newHeight,$imageType);
                                            }
                                          }
											if(!empty($imagename) && $ImageStatus==TRUE){
												$coupondata['image'] = $imagename;
											}
                                            $coupondata['store_id'] = $store_id;
                                            $coupondata['merchant_id'] = $merchant_id;
                                            $coupondata['name'] = trim($requestBody['name']);
                                            $startDate="";
                                            $endDate="";


                                            if(!empty($requestBody['start_date'])){
                                                $dateStart=  explode("/", $requestBody['start_date']);
                                                $startDate=$dateStart[2]."-".$dateStart[1]."-".$dateStart[0];
                                            }
                                            if(!empty($requestBody['end_date'])){
                                                $dateEnd=  explode("/", $requestBody['end_date']);
                                                $endDate=$dateEnd[2]."-".$dateEnd[1]."-".$dateEnd[0];
                                            }
                                            $coupondata['start_date'] = $startDate;
                                            $coupondata['end_date'] = $endDate;
                                            $coupondata['coupon_code'] = trim($requestBody['coupon_code']);
                                            $coupondata['number_can_use'] = $requestBody['number_can_use'];
                                            $coupondata['discount_type'] = $requestBody['discount_type'];
                                            $coupondata['discount'] = $requestBody['discount'];
                                            $coupondata['is_active'] = $requestBody['is_active'];

                                            if (isset($requestBody['promotional_message']) && $requestBody['promotional_message']) {
                                                $coupondata['promotional_message'] = trim($requestBody['promotional_message']);
                                            }

                                            $coupondata['allow_time'] = (isset($requestBody['allow_time']))?$requestBody['allow_time']:'0';
                                            $coupondata['start_time'] = (isset($requestBody['start_time']))?$requestBody['start_time']:'';
                                            $coupondata['end_time'] = (isset($requestBody['end_time']))?$requestBody['end_time']:'';
                                            $coupondata['days'] = (isset($requestBody['days']))?$requestBody['days']:'';




                                            $this->Coupon->create();
                                            $this->Coupon->saveCoupon($coupondata);
                                            $responsedata['message'] = "Coupon has been added successfully.";
                                            $responsedata['response'] = 1;
                                            return json_encode($responsedata);
                                    } else {
                                        $responsedata['message'] = "Coupon code already exists.";
                                        $responsedata['response'] = 0;
                                        return json_encode($responsedata);
                                    }
                                } else {
                                    $responsedata['message'] = "Coupon title already exists.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     /*     * ******************************************************************************************
      @Function Name : editCoupon
      @Description   : this function is used to Edit Coupon Information.
      @Author        : SmartData
      created:07/12/2016
     * ****************************************************************************************** */

    public function editCoupon() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        $this->Webservice->webserviceAdminLog($requestBody, "edit_coupons.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if (empty($requestBody['coupon_id'])) {
                                    $responsedata['message'] = "Please select a coupon.";
                                    $responsedata['response'] = 0;
                                     return json_encode($responsedata);
                                }
                                $couponsDet=$this->Coupon->find('first',array('conditions'=>array('Coupon.id'=>$requestBody['coupon_id'],'Coupon.is_deleted'=>0)));

                                if(empty($couponsDet)){
                                    $responsedata['message'] = "Coupon is not active.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }

                                if (empty($requestBody['name'])) {
                                    $responsedata['message'] = "Please enter a coupon title.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                if (empty($requestBody['coupon_code'])) {
                                    $responsedata['message'] = "Please enter a coupon code.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                if (empty($requestBody['discount_type'])) {
                                    $responsedata['discount_type'] = 1;
                                    return json_encode($responsedata);
                                }
                                if (empty($requestBody['discount'])) {
                                    $responsedata['message'] = "Please enter a discount.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                if (empty($requestBody['number_can_use'])) {
                                    $responsedata['number_can_use'] = 1;
                                    return json_encode($responsedata);
                                }
                                if (empty($requestBody['start_date'])) {
                                    $responsedata['message'] = "Please enter a start date.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                if (empty($requestBody['end_date'])) {
                                    $responsedata['message'] = "Please enter a end date.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }

                                if ($requestBody['is_active']) {
                                    $requestBody['is_active'] = 1;
                                } else {
                                    $requestBody['is_active'] = 0;
                                }
                                $couponTitle = trim($requestBody['name']);
                                $couponCode = trim($requestBody['coupon_code']);
                                $isUniqueName = $this->Coupon->checkCouponUniqueName($couponTitle, $store_id, $requestBody['coupon_id']);
                                $isUniqueCode = $this->Coupon->checkCouponUniqueCode($couponCode, $store_id, $requestBody['coupon_id']);
                                if ($isUniqueName) {
                                                if ($isUniqueCode) {
                                        $imagename="";
                                          if (!empty($requestBody['image'])) {
                                            //$pathLoadedImg = WWW_ROOT . "/Coupon-Image/" . $couponsDet['Coupon']['image'];
                                            //$path2LoadedImg = WWW_ROOT . "/Coupon-Image/thumb/" . $couponsDet['Coupon']['image'];
                                            //unlink($pathLoadedImg);
                                            //unlink($path2LoadedImg);
                                            $dat = explode(';', $requestBody['image']);
                                                    $type = $dat[0];
                                                    $data2 = $dat[1];
                                                    $dat2 = explode(',', $data2);
                                                    //list(, $data1) = explode(',', $data1);
                                                    $data3 = base64_decode($dat2[1]);
                                                    $jpgMimes=array('image/jpeg','image/pjpeg');
                                                    $pngMimes=array('image/png');
                                                    $gifMimes=array('image/gif');

                                                    $imgdata = base64_decode($dat2[1]);
                                                    $f = finfo_open();
                                                    $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
                                                    if(in_array($mime_type,$jpgMimes)){
                                                            $imageType="jpg";
                                                            $imagename = uniqid() . "_" . date('Y-m-d-H-s') . '_' . $store_id . '_image.jpg';
                                                    }else if(in_array($mime_type,$pngMimes)){
                                                            $imageType="png";
                                                            $imagename = uniqid() . "_" . date('Y-m-d-H-s') . '_' . $store_id . '_image.png';
                                                    }else if(in_array($mime_type,$gifMimes)){
                                                            $imageType="gif";
                                                            $imagename = uniqid() . "_" . date('Y-m-d-H-s') . '_' . $store_id . '_image.gif';
                                                    }else{
                                                            $responsedata['message'] = "Only jpg, gif, png type images are allowed.";
                                                            $responsedata['response'] = 0;
                                                            return json_encode($responsedata);
                                                    }
                                                    $path = WWW_ROOT . "/Coupon-Image/" . $imagename;
                                                    $folderName = "/Coupon-Image";
                                                    $newWidth = 480;
                                                    $newHeight = 320;
                                            if ($imagename) {
                                                file_put_contents($path, $data3);
                                                $ImageStatus= $this->Webservice->cropImage($path, $folderName,$imagename,$newWidth,$newHeight,$imageType);
                                            }
                                          }else{
                                            $coupondata['image'] = $requestBody['image'];
                                          }
                                            $coupondata['id'] = $couponsDet['Coupon']['id'];
                                                if(!empty($imagename) && $ImageStatus==TRUE){
                                                        $coupondata['image'] = $imagename;
                                                }
                                            $coupondata['store_id'] = $store_id;
                                            $coupondata['merchant_id'] = $merchant_id;
                                            $coupondata['name'] = trim($requestBody['name']);
                                            $startDate="";
                                            $endDate="";
                                            if(!empty($requestBody['start_date'])){
                                                $dateStart=  explode("/", $requestBody['start_date']);
                                                $startDate=$dateStart[2]."-".$dateStart[1]."-".$dateStart[0];
                                            }
                                            if(!empty($requestBody['end_date'])){
                                                $dateEnd=  explode("/", $requestBody['end_date']);
                                                $endDate=$dateEnd[2]."-".$dateEnd[1]."-".$dateEnd[0];
                                            }
                                            $coupondata['start_date'] = $startDate;
                                            $coupondata['end_date'] = $endDate;
                                            $coupondata['coupon_code'] = trim($requestBody['coupon_code']);
                                            $coupondata['number_can_use'] = $requestBody['number_can_use'];
                                            $coupondata['discount_type'] = $requestBody['discount_type'];
                                            $coupondata['discount'] = $requestBody['discount'];
                                            $coupondata['is_active'] = $requestBody['is_active'];
                                            if (isset($requestBody['promotional_message']) && $requestBody['promotional_message']) {
                                                $coupondata['promotional_message'] = trim($requestBody['promotional_message']);
                                            }

                                            $coupondata['allow_time'] = (isset($requestBody['allow_time']))?$requestBody['allow_time']:'0';
                                            $coupondata['start_time'] = (isset($requestBody['start_time']))?$requestBody['start_time']:'';
                                            $coupondata['end_time'] = (isset($requestBody['end_time']))?$requestBody['end_time']:'';
                                            $coupondata['days'] = (isset($requestBody['days']))?$requestBody['days']:'';



                                            $this->Coupon->save($coupondata);
                                            $responsedata['message'] = "Coupon has been updated successfully.";
                                            $responsedata['response'] = 1;
                                            return json_encode($responsedata);
                                        } else {
                                $responsedata['message'] = "Coupon code already exists.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                            } else {
                                $responsedata['message'] = "Coupon title already exists.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }



                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     /*     * ******************************************************************************************
      @Function Name : removeCoupon
      @Description   : this function is used to remove selected Coupon.
      @Author        : SmartData
      created:07/12/2016
     * ****************************************************************************************** */

    public function removeCoupon() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody = '{"store_id":"108", "coupon_id":"130"}';
//        $headers['user_id'] = 'NzU';
//        $headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "remove_coupon.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if (empty($requestBody['coupon_id'])) {
                                    $responsedata['message'] = "Please select a coupon.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                $coupon_id = $requestBody['coupon_id'];
                                $couponsDet = $this->Coupon->find('first', array('conditions' => array('Coupon.id' => $coupon_id)));
                                if ($couponsDet) {
                                    $data['id'] = $couponsDet['Coupon']['id'];
                                    $data['is_deleted'] = 1;
                                    if ($this->Coupon->save($data)) {
                                        $responsedata['message'] = "Coupon has been deleted successfully.";
                                        $responsedata['response'] = 1;
                                        return json_encode($responsedata);
                                    } else {
                                        $responsedata['message'] = "Coupon could not be deleted, please try again.";
                                        $responsedata['response'] = 0;
                                        return json_encode($responsedata);
                                    }
                                } else {
                                    $responsedata['message'] = "Coupon not found.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

      /*     * ******************************************************************************************
      @Function Name : updateCouponStatus
      @Description   : this function is used to update selected Coupon status .
      @Author        : SmartData
      created:07/12/2016
     * ****************************************************************************************** */

    public function updateCouponStatus() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody = '{"store_id":"108", "coupon_id":"130", "status":true}';
//        $headers['user_id'] = 'NzU';
//        $headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "remove_coupon.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if (empty($requestBody['coupon_id'])) {
                                    $responsedata['message'] = "Please select a coupon.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                $coupon_id = $requestBody['coupon_id'];
                                $couponsDet = $this->Coupon->find('first', array('conditions' => array('Coupon.id' => $coupon_id)));
                                if ($couponsDet) {
                                    $data['id'] = $couponsDet['Coupon']['id'];
                                    if ($requestBody['status']) {
                                        $data['is_active'] = 1;
                                    } else {
                                        $data['is_active'] = 0;
                                    }

                                    if ($this->Coupon->save($data)) {
                                        $responsedata['message'] = "Coupon has been updated successfully.";
                                        $responsedata['response'] = 1;
                                        return json_encode($responsedata);
                                    } else {
                                        $responsedata['message'] = "Coupon could not be updated, please try again.";
                                        $responsedata['response'] = 0;
                                        return json_encode($responsedata);
                                    }
                                } else {
                                    $responsedata['message'] = "Coupon not found.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     /*     * ******************************************************************************************
      @Function Name : itemOffersList
      @Description   : this function is used to List offer on Item.
      @Author        : SmartData
      created:07/12/2016
     * ****************************************************************************************** */

    public function extendedOffersList() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody = '{"store_id":"108", "isActive":"","page_number":"1"}';  //isActive=>1 show active isActive=>2 show deactive and if isActive=>0 all
//        $headers['user_id'] = 'NzU';
//        $headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "item_offers_list.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                $criteria = "ItemOffer.store_id =$store_id AND ItemOffer.is_deleted=0";

                                if ($requestBody['isActive'] == 1) {
                                    $active = 1;
                                    $criteria .= " AND (ItemOffer.is_active =$active)";
                                } elseif ($requestBody['isActive'] == 2) {
                                    $active = 0;
                                    $criteria .= " AND (ItemOffer.is_active =$active)";
                                }

                                if (empty($requestBody['page_number'])) {
                                    $requestBody['page_number'] = 1;
                                }
                                $limit = 9;
                                $offset = $requestBody['page_number'] * 9 - 9;
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
                                  $this->ItemOffer->bindModel(
                                        array(
                                    'belongsTo' => array(
                                        'Category' => array(
                                            'className' => 'Category',
                                            'foreignKey' => 'category_id',
                                            'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                                            'fields' => array('id', 'name'),
                                            'type' => "INNER"
                                        )
                                    )
                                        ), false
                                );
                                $itemOfferCount = $this->ItemOffer->find('count', array('conditions' => array($criteria), 'order' => array('ItemOffer.created' => 'DESC')));
                                $itemOffer = $this->ItemOffer->find('all', array('offset' => $offset, 'limit' => $limit,'conditions' => array($criteria), 'order' => array('ItemOffer.created' => 'DESC')));
//                                pr($itemOfferCount);
//                                pr($itemOffer);
//                                die;
                                $itemOfferArr=array();
                                if(!empty($itemOffer)){
                                    $i=0;
                                    foreach($itemOffer as $itemOfferList){
                                         if (!empty($itemOfferList['Category'])) {
                                            $itemOfferArr[$i]['category_id'] = $itemOfferList['Category']['id'];
                                            $itemOfferArr[$i]['category_name'] = $itemOfferList['Category']['name'];
                                       $itemOfferArr[$i]['id']=$itemOfferList['ItemOffer']['id'];

                                       if(!empty($itemOfferList['Item']['id'])){
                                            $itemOfferArr[$i]['item_id']=$itemOfferList['Item']['id'];
                                            $itemOfferArr[$i]['item_name']=$itemOfferList['Item']['name'];
                                       }else{
                                            $itemOfferArr[$i]['item_id']="";
                                            $itemOfferArr[$i]['item_name']="";
                                       }
                                       if(!empty($itemOfferList['ItemOffer']['unit_counter'])){
                                            $itemOfferArr[$i]['unit_counter']=$itemOfferList['ItemOffer']['unit_counter'];
                                       }else{
                                            $itemOfferArr[$i]['unit_counter']="";
                                       }

                                       if(!empty($itemOfferList['ItemOffer']['start_date'])){
                                           $date=  explode("-", $itemOfferList['ItemOffer']['start_date']);
                                           $startDate=$date[2]."/".$date[1]."/".$date[0];
                                            $itemOfferArr[$i]['start_date']=$startDate;
                                       }else{
                                           $itemOfferArr[$i]['start_date']="";
                                       }
                                       if(!empty($itemOfferList['ItemOffer']['end_date'])){
                                           $dateEnd=  explode("-", $itemOfferList['ItemOffer']['end_date']);
                                           $endDate=$dateEnd[2]."/".$dateEnd[1]."/".$dateEnd[0];
                                            $itemOfferArr[$i]['end_date']=$endDate;
                                       }else{
                                           $itemOfferArr[$i]['end_date']="";
                                       }
                                       if(!empty($itemOfferList['ItemOffer']['is_active'])){
                                            $itemOfferArr[$i]['is_active']=TRUE;
                                       }else{
                                           $itemOfferArr[$i]['is_active']=FALSE;
                                       }
                                       $itemOfferArr[$i]['item_used_count']="";
                                       if(!empty($itemOfferArr[$i]['item_id'])){
                                            $totalFreeUnits = $this->OrderItemFree->find('all', array('fields' => array('sum(OrderItemFree.free_quantity) as total_sum'), 'conditions' => array('OrderItemFree.item_id' => $itemOfferArr[$i]['item_id'], 'OrderItemFree.is_active' => 1, 'OrderItemFree.is_deleted' => 0)));
                                            //$itemOfferArr[$i]['item_used_count'] = $totalFreeUnits[0][0]['total_sum'];
                                            if(!empty($totalFreeUnits[0][0]['total_sum'])){
                                                $itemOfferArr[$i]['item_used_count'] = $totalFreeUnits[0][0]['total_sum'];
                                           }else{
                                                $itemOfferArr[$i]['item_used_count'] = "";
                                           }
                                       }



                                       //if($itemOfferList['ItemOffer']['unit_counter']>1){
                                       // $itemOfferList['ItemOffer']['unit_counter']=$itemOfferList['ItemOffer']['unit_counter']-1;
                                       //}
                                       $numSurfix = $this->Webservice->addOrdinalNumberSuffix($itemOfferList['ItemOffer']['unit_counter']);
                                       if(!empty($numSurfix)){
                                            $itemOfferArr[$i]['extended_detail'] = "Buy " . ($itemOfferList['ItemOffer']['unit_counter']-1) . " unit and get the ". $numSurfix." Item free on " . $itemOfferList['Item']['name'].'.';
                                        }else{
                                           $itemOfferArr[$i]['extended_detail'] = "Buy " . ($itemOfferList['ItemOffer']['unit_counter']-1) . " get 1 free.";
                                        }

                                        $this->loadModel('OrderItemFree');
                                        $this->OrderItemFree->bindModel(
                                                array('belongsTo' => array(
                                                        'User' => array(
                                                            'className' => 'User',
                                                            'foreignKey' => 'user_id',
                                                            //'fields' => array('userName', 'email', 'COUNT(User.id) as count'),
                                                        ),
                                                        'Item' => array(
                                                            'className' => 'Item',
                                                            'foreignKey' => 'item_id',
                                                            'fields' => array('name'),
                                                        )
                                        )),false);
                                        $extendedarr=$this->OrderItemFree->find('all',array('fields' => array('sum(OrderItemFree.free_quantity) as total_sum','OrderItemFree.user_id','User.email','User.fname','User.lname','Item.name'), 'conditions' => array('OrderItemFree.store_id' => $store_id, 'OrderItemFree.item_id' => $itemOfferArr[$i]['item_id'], 'OrderItemFree.is_active' => 1, 'OrderItemFree.is_deleted' => 0), 'group' => array('OrderItemFree.user_id')));

                                       // echo "<pre>";
                                        //print_r($extendedarr);
                                        $userlist=array();
                                        if(!empty($extendedarr)){
                                            foreach($extendedarr as $okey =>$ovalue){
                                                if(!empty($ovalue)){
                                                    $userlist[$okey]['extendedCount']=(isset($ovalue[0]['total_sum']))?$ovalue[0]['total_sum']:"";
                                                    $userlist[$okey]['userID']=(isset($ovalue['OrderItemFree']['user_id']))?$ovalue['OrderItemFree']['user_id']:"";
                                                    $userlist[$okey]['userName']=(isset($ovalue['User']['fname']))?$ovalue['User']['fname']." ".$ovalue['User']['lname']:'Guest User';
                                                    $userlist[$okey]['email']=(isset($ovalue['User']['email']))?$ovalue['User']['email']:'-';
                                                }

                                            }
                                        }
                                        $itemOfferArr[$i]['userlist']=$userlist;



                                    $i++;
                                    }
                                    }
                                }

                                $responsedata['message'] = "Success.";
                                $responsedata['response'] = 1;
                                 if(!empty($itemOfferCount)){
                                    if($itemOfferCount>10){
                                        $responsedata['count'] = (string)ceil($itemOfferCount/10);
                                    }else{
                                        $responsedata['count'] = "1";
                                    }

                                }else{
                                   $responsedata['count'] = "1";
                                }

                                $responsedata['itemOffers'] = $itemOfferArr;
                                //pr($responsedata);
                                return json_encode($responsedata);
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     /*     * ******************************************************************************************
      @Function Name : itemOffersList
      @Description   : this function is used to List offer on Item.
      @Author        : SmartData
      created:07/12/2016
     * ****************************************************************************************** */

    public function categoryList() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody = '{"store_id":"2"}';
//        $headers['user_id'] = 'MzEw';
//        $headers['merchant_id'] = 1;
        $this->Webservice->webserviceAdminLog($requestBody, "category_list.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                $this->Category->bindModel(
                                        array('hasMany' => array(
                                                'Item' => array(
                                                    'className' => 'Item',
                                                    'foreignKey' => 'category_id',
                                                    'fields' => array('id', 'name'),
                                                    'conditions' => array('Item.is_active' => 1, 'Item.is_deleted' => 0),
                                                    'order' => array('position' => 'asc')
                                                )
                                            )
                                ));
                                $catLising = $this->Category->find('all', array('conditions' => array('Category.store_id' => $store_id, 'Category.merchant_id' => $merchant_id, 'Category.is_active' => 1, 'Category.is_deleted' => 0), 'fields' => array('id', 'name')));

                                $CatListArr = array();
                                $i = 0;
                                if (!empty($catLising)) {
                                    foreach ($catLising as $catList) {
                                        if (!empty($catList['Item'])) {
                                            $CatListArr[$i]['catgory_id'] = $catList['Category']['id'];
                                            $CatListArr[$i]['catgory_name'] = $catList['Category']['name'];
                                            $j = 0;
                                            foreach ($catList['Item'] as $items) {
                                                $CatListArr[$i]['item'][$j]['item_id'] = $items['id'];
                                                $CatListArr[$i]['item'][$j]['item_name'] = $items['name'];
                                                $j++;
                                            }
                                        }

                                        $i++;
                                    }
                                }


                                $responsedata['message'] = "Success.";
                                $responsedata['response'] = 1;
                                $responsedata['Category'] = array_values($CatListArr);
                                //pr($responsedata);
                                return json_encode($responsedata);
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     /*     * ******************************************************************************************
      @Function Name : addCoupons
      @Description   : this function is used to Add Coupons.
      @Author        : SmartData
      created:06/12/2016
     * ****************************************************************************************** */

    public function addExtendedOffer() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody = '{"store_id":"2","category_id":"276","item_id":"220","unit_counter":"2","start_date":"31/12/2016","end_date":"31/12/2016","is_active":true}';
//        //$headers['user_id'] = 'MzEw';
//        $headers['user_id'] = 'NzU';
//        $headers['merchant_id'] = 1;
        $this->Webservice->webserviceAdminLog($requestBody, "add_item_offer.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if (empty($requestBody['category_id'])) {
                                    $responsedata['message'] = "Please select a category.";
                                    $responsedata['response'] = 0;
                                     return json_encode($responsedata);
                                }
                                if (empty($requestBody['item_id'])) {
                                    $responsedata['message'] = "Please select an item.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }

                                if (empty($requestBody['unit_counter'])) {
                                    $responsedata['unit_counter'] = 1;
                                }
                                if (empty($requestBody['start_date'])) {
                                    $responsedata['message'] = "Please enter a start date.";
                                    $responsedata['response'] = 0;
                                     return json_encode($responsedata);
                                }
                                if (empty($requestBody['end_date'])) {
                                    $responsedata['message'] = "Please enter a end date.";
                                    $responsedata['response'] = 0;
                                     return json_encode($responsedata);
                                }
                                if ($requestBody['is_active']) {
                                    $requestBody['is_active'] = 1;
                                } else {
                                    $requestBody['is_active'] = 0;
                                }

                                $isUniqueOffer = $this->ItemOffer->checkUniqueOffer($requestBody['item_id'], $store_id);

                                $offerData = array();
                                if ($isUniqueOffer) {
                                    $offerData['store_id'] = $store_id;
                                    $offerData['merchant_id'] = $merchant_id;
                                    $offerData['item_id'] = $requestBody['item_id'];
                                    $offerData['category_id'] = $requestBody['category_id'];
                                    $offerData['unit_counter'] = $requestBody['unit_counter'];
                                    $startDate = "";
                                    $endDate = "";
                                    if (!empty($requestBody['start_date'])) {
                                        $dateStart = explode("/", $requestBody['start_date']);
                                        $startDate = $dateStart[2] . "-" . $dateStart[1] . "-" . $dateStart[0];
                                    }
                                    if (!empty($requestBody['end_date'])) {
                                        $dateEnd = explode("/", $requestBody['end_date']);
                                        $endDate = $dateEnd[2] . "-" . $dateEnd[1] . "-" . $dateEnd[0];
                                    }
                                    $offerData['start_date'] = $startDate;
                                    $offerData['end_date'] = $endDate;
                                    $offerData['is_active'] = $requestBody['is_active'];
                                    $this->ItemOffer->create();
                                    if ($this->ItemOffer->save($offerData)) {
                                        $responsedata['message'] = "Offer has been added successfully.";
                                        $responsedata['response'] = 1;
                                        return json_encode($responsedata);
                                    } else {
                                        $responsedata['message'] = "Offer could not be saved, please try again.";
                                        $responsedata['response'] = 1;
                                        return json_encode($responsedata);
                                    }
                                } else {
                                    $responsedata['message'] = "Offer already exists.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

    /*     * ******************************************************************************************
      @Function Name : updateCouponStatus
      @Description   : this function is used to update selected Coupon status .
      @Author        : SmartData
      created:07/12/2016
     * ****************************************************************************************** */

    public function updateExtendedOfferStatus() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody = '{"store_id":"108", "itemoffer_id":"130", "status":true}';
//        $headers['user_id'] = 'NzU';
//        $headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "update_item_offer_status.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if (empty($requestBody['itemoffer_id'])) {
                                    $responsedata['message'] = "Please select a offer.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                $itemoffer_id = $requestBody['itemoffer_id'];
                                $itemofferDet = $this->ItemOffer->find('first', array('conditions' => array('ItemOffer.id' => $itemoffer_id,'ItemOffer.is_deleted' =>0)));
                                if ($itemofferDet) {
                                    $data['id'] = $itemofferDet['ItemOffer']['id'];
                                    if ($requestBody['status']) {
                                        $data['is_active'] = 1;
                                    } else {
                                        $data['is_active'] = 0;
                                    }

                                    if ($this->ItemOffer->save($data)) {
                                        $responsedata['message'] = "Offer has been updated successfully.";
                                        $responsedata['response'] = 1;
                                        return json_encode($responsedata);
                                    } else {
                                        $responsedata['message'] = "Offer could not be updated, please try again.";
                                        $responsedata['response'] = 0;
                                        return json_encode($responsedata);
                                    }
                                } else {
                                    $responsedata['message'] = "Offer not found.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }
     /*     * ******************************************************************************************
      @Function Name : removeCoupon
      @Description   : this function is used to remove selected Coupon.
      @Author        : SmartData
      created:07/12/2016
     * ****************************************************************************************** */

    public function removeExtendedOffer() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody = '{"store_id":"108", "itemoffer_id":"44"}';
//        $headers['user_id'] = 'NzU';
//        $headers['merchant_id'] = 85;

        $this->Webservice->webserviceAdminLog($requestBody, "remove_item_offer.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if (empty($requestBody['itemoffer_id'])) {
                                    $responsedata['message'] = "Please select a offer.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                $itemoffer_id = $requestBody['itemoffer_id'];
                                $itemofferDet = $this->ItemOffer->find('first', array('conditions' => array('ItemOffer.id' => $itemoffer_id)));
                                if ($itemofferDet) {
                                    $data['id'] = $itemofferDet['ItemOffer']['id'];
                                    $data['is_deleted'] = 1;
                                    if ($this->ItemOffer->save($data)) {
                                        $responsedata['message'] = "Offer has been deleted successfully.";
                                        $responsedata['response'] = 1;
                                        return json_encode($responsedata);
                                    } else {
                                        $responsedata['message'] = "Offer could not be deleted, please try again.";
                                        $responsedata['response'] = 0;
                                        return json_encode($responsedata);
                                    }
                                } else {
                                    $responsedata['message'] = "Offer not found.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }
       /*     * ******************************************************************************************
      @Function Name : editItemOffer
      @Description   :  this function is used to Edit Item offer Information.
      @Author        : SmartData
      created:06/12/2016
     * ****************************************************************************************** */

    public function editExtendedOffer() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody = '{"itemoffer_id":"2","store_id":"2","category_id":"276","item_id":"220","unit_counter":"2","start_date":"31/12/2016","end_date":"31/12/2016","is_active":true}';
//        //$headers['user_id'] = 'MzEw';
//        $headers['user_id'] = 'NzU';
//        $headers['merchant_id'] = 1;
        $this->Webservice->webserviceAdminLog($requestBody, "edit_item_offer.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if (empty($requestBody['itemoffer_id'])) {
                                    $responsedata['message'] = "Please enter a offer.";
                                    $responsedata['response'] = 0;
                                }

                                $itemofferDet = $this->ItemOffer->find('first', array('conditions' => array('ItemOffer.id' => $requestBody['itemoffer_id'])));

                                if (empty($itemofferDet)) {
                                    $responsedata['message'] = "Offer is not active.";
                                    $responsedata['response'] = 0;
                                     return json_encode($responsedata);
                                }

                                if (empty($requestBody['category_id'])) {
                                    $responsedata['message'] = "Please select a category.";
                                    $responsedata['response'] = 0;
                                     return json_encode($responsedata);
                                }
                                if (empty($requestBody['item_id'])) {
                                    $responsedata['message'] = "Please select an item.";
                                    $responsedata['response'] = 0;
                                     return json_encode($responsedata);
                                }

                                if (empty($requestBody['unit_counter'])) {
                                    $responsedata['unit_counter'] = 1;
                                }
                                if (empty($requestBody['start_date'])) {
                                    $responsedata['message'] = "Please enter a start date.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                if (empty($requestBody['end_date'])) {
                                    $responsedata['message'] = "Please enter a end date.";
                                    $responsedata['response'] = 0;
                                     return json_encode($responsedata);
                                }
                                if ($requestBody['is_active']) {
                                    $requestBody['is_active'] = 1;
                                } else {
                                    $requestBody['is_active'] = 0;
                                }

                                //  $isUniqueOffer = $this->ItemOffer->checkUniqueOffer($requestBody['item_id'], $store_id);

                                $offerData = array();
                                $offerData['id'] = $itemofferDet['ItemOffer']['id'];
                                $offerData['store_id'] = $store_id;
                                $offerData['merchant_id'] = $merchant_id;
                                $offerData['item_id'] = $requestBody['item_id'];
                                $offerData['category_id'] = $requestBody['category_id'];
                                $offerData['unit_counter'] = $requestBody['unit_counter'];
                                $startDate = "";
                                $endDate = "";
                                if (!empty($requestBody['start_date'])) {
                                    $dateStart = explode("/", $requestBody['start_date']);
                                    $startDate = $dateStart[2] . "-" . $dateStart[1] . "-" . $dateStart[0];
                                }
                                if (!empty($requestBody['end_date'])) {
                                    $dateEnd = explode("/", $requestBody['end_date']);
                                    $endDate = $dateEnd[2] . "-" . $dateEnd[1] . "-" . $dateEnd[0];
                                }
                                $offerData['start_date'] = $startDate;
                                $offerData['end_date'] = $endDate;
                                $offerData['is_active'] = $requestBody['is_active'];
                                $this->ItemOffer->create();
                                if ($this->ItemOffer->save($offerData)) {
                                    $responsedata['message'] = "Offer has been updated successfully.";
                                    $responsedata['response'] = 1;
                                    return json_encode($responsedata);
                                } else {
                                    $responsedata['message'] = "Offer could not be updated, please try again.";
                                    $responsedata['response'] = 1;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     /*     * ******************************************************************************************
      @Function Name : offersList
      @Description   : this function is used to List offer / Promotions .
      @Author        : SmartData
      created:08/12/2016
     * ****************************************************************************************** */

    public function promosList() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody = '{"store_id":"108","page_number":"1"}';  //isActive=>1 show active isActive=>2 show deactive and if isActive=>0 all
        //$headers['user_id'] = 'NzU';
        //$headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "promos_list.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {

                                $value = "";
                                $criteria = "Offer.store_id =$store_id AND Offer.is_deleted=0";

                                $this->loadModel('Category');
                                $this->Item->bindModel(array(
                                    'belongsTo' => array('Category' => array(
                                            'className' => 'Category',
                                            'foreignKey' => 'category_id',
                                            'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                                            'fields' => array('id', 'name')
                                        )
                                    )
                                        ), false);
                                $this->Offer->bindModel(
                                        array(
                                    'belongsTo' => array(
                                        'Item' => array(
                                            'className' => 'Item',
                                            'foreignKey' => 'item_id',
                                            'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                                            'fields' => array('id', 'name', 'category_id')
                                        ),
                                        'Size' => array(
                                            'className' => 'Size',
                                            'foreignKey' => 'size_id',
                                            'conditions' => array('Size.is_deleted' => 0, 'Size.is_active' => 1),
                                            'fields' => array('id', 'size')
                                        )
                                    )
                                        ), false
                                );
                                $this->OfferDetail->bindModel(
                                        array(
                                    'belongsTo' => array(
                                        'Item' => array(
                                            'className' => 'Item',
                                            'foreignKey' => 'offerItemID',
                                            'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                                            'fields' => array('id', 'name', 'category_id')
                                        ),
                                        'Size' => array(
                                            'className' => 'Size',
                                            'foreignKey' => 'offerSize',
                                            'conditions' => array('Size.is_deleted' => 0, 'Size.is_active' => 1),
                                            'fields' => array('id', 'size')
                                        )
                                    )
                                        ), false
                                );
                                $this->Offer->bindModel(
                                        array(
                                            'hasMany' => array(
                                                'OfferDetail' => array(
                                                    'className' => 'OfferDetail',
                                                    'foreignKey' => 'offer_id',
                                                    'conditions' => array('OfferDetail.is_deleted' => 0, 'OfferDetail.is_active' => 1),
                                                    'fields' => array('id', 'offerItemID', 'offerSize', 'discountAmt')
                                                )
                                            )
                                        ), false
                                );
                                 if (empty($requestBody['page_number'])) {
                            $requestBody['page_number']=1;
                           }
                           $limit = 9;
                           $offset = $requestBody['page_number'] * 9 - 9;
                                //$offerCount = $this->Offer->find('count', array('conditions' => array($criteria)));
                                 $offerDet = $this->Offer->find('all', array('recursive' => 3, 'offset' => $offset, 'limit' => $limit, 'conditions' => array($criteria), 'order' => array('Offer.created' => 'DESC'), 'fields' => array('id', 'item_id', 'description', 'offer_start_date', 'offer_end_date', 'offer_start_time', 'offer_end_time', 'is_active', 'size_id', 'offerImage', 'is_time','is_fixed_price','offerprice','unit')));
//                                pr($offerCount);
                                //pr($offerDet);
                                //die;
                                $offerArr = array();
                                if (!empty($offerDet)) {
                                     $protocol = 'http://';
                                    if (isset($_SERVER['HTTPS'])) {
                                        if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                                            $protocol = 'https://';
                                        }
                                    }
                                    $i = 0;
                                    foreach ($offerDet as $offerList) {
                                        if (!empty($offerList['Item']['Category'])) {
                                            $offerArr[$i]['id'] = $offerList['Offer']['id'];
                                            if (!empty($offerList['Offer']['description'])) {
                                                $offerArr[$i]['description'] = $offerList['Offer']['description'];
                                            } else {
                                                $offerArr[$i]['description'] = "";
                                            }

                                            if (!empty($offerList['Offer']['item_id'])) {
                                                $offerArr[$i]['item_id'] = $offerList['Offer']['item_id'];
                                                if (!empty($offerList['Item']['id'])) {
                                                    $offerArr[$i]['item_name'] = $offerList['Item']['name'];
                                                } else {
                                                    $offerArr[$i]['item_name'] = "";
                                                }
                                            } else {
                                                $offerArr[$i]['item_id'] = "";
                                                $offerArr[$i]['item_name'] = "";
                                            }

                                            if (!empty($offerList['Size'])) {
                                                $offerArr[$i]['size_id'] = $offerList['Size']['id'];
                                                $offerArr[$i]['size_name'] = $offerList['Size']['size'];
                                            } else {
                                                $offerArr[$i]['size_id'] = "";
                                                $offerArr[$i]['size_name'] = "";
                                            }



                                            if (!empty($offerList['Offer']['offer_start_date'])) {
                                                $date = explode("-", $offerList['Offer']['offer_start_date']);
                                                $startDate = $date[2] . "/" . $date[1] . "/" . $date[0];
                                                $offerArr[$i]['offer_start_date'] = $startDate;
                                            } else {
                                                $offerArr[$i]['offer_start_date'] = "";
                                            }

                                            if (!empty($offerList['Offer']['offer_end_date'])) {
                                                $dateEnd = explode("-", $offerList['Offer']['offer_end_date']);
                                                $endDate = $dateEnd[2] . "/" . $dateEnd[1] . "/" . $dateEnd[0];
                                                $offerArr[$i]['offer_end_date'] = $endDate;
                                            } else {
                                                $offerArr[$i]['offer_end_date'] = "";
                                            }

                                            if (!empty($offerList['Offer']['offer_start_time'])) {
                                                $offerArr[$i]['offer_start_time'] = $offerList['Offer']['offer_start_time'];
                                                if($offerList['Offer']['offer_start_time']=="24:00:00"){
                                                    $offerArr[$i]['offer_start_time'] = "00:00:00";
                                                }
                                            } else {
                                                $offerArr[$i]['offer_start_time'] = "";
                                            }
                                            if (!empty($offerList['Offer']['offer_end_time'])) {
                                                $offerArr[$i]['offer_end_time'] = $offerList['Offer']['offer_end_time'];
                                                 if($offerList['Offer']['offer_end_time']=="24:00:00"){
                                                    $offerArr[$i]['offer_end_time'] = "00:00:00";
                                                }
                                            } else {
                                                $offerArr[$i]['offer_end_time'] = "";
                                            }

                                            if (!empty($offerList['Offer']['is_active'])) {
                                                $offerArr[$i]['is_active'] = TRUE;
                                            } else {
                                                $offerArr[$i]['is_active'] = FALSE;
                                            }
                                             if (!empty($offerList['Offer']['is_time'])) {
                                                $offerArr[$i]['is_time'] = TRUE;

                                            } else {
                                                $offerArr[$i]['is_time'] = FALSE;
;                                            }
                                            if (!empty($offerList['Offer']['is_fixed_price'])) {
                                                $offerArr[$i]['is_fixed_price'] = TRUE;
                                            } else {
                                                $offerArr[$i]['is_fixed_price'] = FALSE;
                                            }

                                            if($offerList['Offer']['unit']){
                                                  $offerArr[$i]['unit'] = $offerList['Offer']['unit'];
                                            }else{
                                                $offerArr[$i]['unit']="";
                                            }
                                             if($offerList['Offer']['offerprice']){
                                                  $offerArr[$i]['offerprice'] = $offerList['Offer']['offerprice'];
                                            }else{
                                                $offerArr[$i]['offerprice']="";
                                            }
                                             if (!empty($offerList['Offer']['offerImage'])) {
                                       $offerArr[$i]['offerImage'] = $protocol . $storeResult['Store']['store_url'] . "/Offer-Image/" . $offerList['Offer']['offerImage'];
                                    } else {
                                       $offerArr[$i]['offerImage'] = "";
                                    }

                                            if (!empty($offerList['OfferDetail'])) {
                                                $o = 0;
                                                foreach ($offerList['OfferDetail'] as $offerDetail) {
                                                    if (!empty($offerDetail['Item']['Category'])) {
                                                    $offerArr[$i]['OfferDetail'][$o]['offered_id'] = $offerDetail['id'];

                                                    if (!empty($offerDetail['offerItemID'])) {
                                                       $offerArr[$i]['OfferDetail'][$o]['item_id'] = $offerDetail['offerItemID'];
                                                        if (!empty($offerDetail['Item']['id'])) {
                                                           $offerArr[$i]['OfferDetail'][$o]['item_name'] = $offerDetail['Item']['name'];
                                                        } else {
                                                            $offerArr[$i]['OfferDetail'][$o]['item_name'] = "";
                                                        }
                                                    } else {
                                                        $offerArr[$i]['OfferDetail'][$o]['item_id'] = "";
                                                        $offerArr[$i]['OfferDetail'][$o]['item_name'] = "";
                                                    }

                                                    if (!empty($offerDetail['Size'])) {
                                                       $offerArr[$i]['OfferDetail'][$o]['size_id'] = $offerDetail['Size']['id'];
                                                       $offerArr[$i]['OfferDetail'][$o]['size_name'] = $offerDetail['Size']['size'];
                                                    } else {
                                                        $offerArr[$i]['OfferDetail'][$o]['size_id'] = "";
                                                       $offerArr[$i]['OfferDetail'][$o]['size_name'] = "";
                                                    }
                                                    if (!empty($offerDetail['discountAmt'])) {
                                                       $offerArr[$i]['OfferDetail'][$o]['discountAmt'] = $offerDetail['discountAmt'];
                                                    } else {
                                                       $offerArr[$i]['OfferDetail'][$o]['discountAmt'] = "";
                                                    }
                                                    $o++;
                                                }
                                            }

                                            }else{
                                                 $offerArr[$i]['OfferDetail']=array();
                                            }


                                            $i++;
                                        }
                                    }
                                }

                                //// Get Offer used Count & List of users
                                $this->loadModel('OrderOffer');
                                if (!empty($offerArr)) {

                                    $this->OrderOffer->bindModel(
                                        array('belongsTo' => array(
                                                'Offer' => array(
                                                    'className' => 'Offer',
                                                    'foreignKey' => 'offer_id',
                                                    'fields' => array('description'),
                                                ),
                                                'Order' => array(
                                                    'className' => 'Order',
                                                    'foreignKey' => 'order_id',
                                                )
                                    )),false);

                                    $this->Offer->bindModel(
                                            array('belongsTo' => array(
                                                    'Item' => array(
                                                        'className' => 'Item',
                                                        'foreignKey' => 'item_id',
                                                        'fields' => array('Item.name'),
                                                    )
                                    )));
                                    $this->loadModel('Order');
                                    $this->loadModel('User');
                                    $this->Order->bindModel(
                                        array('belongsTo' => array(
                                            'User' => array(
                                                'className' => 'User',
                                                'foreignKey' => 'user_id',
                                                'fields' => array('id','email','userName'),
                                            )
                                    )),false);

                                    foreach ($offerArr as $key => $offerData) {

                                        $totalFreeUnits = $this->OrderOffer->find('all', array('fields' => array('sum(OrderOffer.quantity) as total_sum'), 'conditions' => array('OrderOffer.offer_id' => $offerData['id'], 'OrderOffer.is_active' => 1, 'OrderOffer.is_deleted' => 0)));
                                        $offerArr[$key]['offer_used_count']='';
                                        if(!empty($totalFreeUnits[0][0]['total_sum'])){
                                            $offerArr[$key]['offer_used_count'] = $totalFreeUnits[0][0]['total_sum'];
                                        }

                                        $totalOfferUsedList=0;
                                        $userlist=array();
                                        if(!empty($offerArr[$key]['offer_used_count'])){



                                                $totalOfferUsedList = $this->OrderOffer->find('all', array( 'recursive' =>3,'fields' => array('sum(OrderOffer.quantity) as total_sum', 'Order.user_id', 'Offer.description','OrderOffer.created','Offer.item_id'), 'conditions' => array('OrderOffer.store_id' => $store_id, 'OrderOffer.offer_id' => $offerData['id'], 'OrderOffer.is_active' => 1, 'OrderOffer.is_deleted' => 0), 'group' => array('Order.user_id')));

                                                if(!empty($totalOfferUsedList)){
                                                    foreach($totalOfferUsedList as $okey =>$ovalue){
                                                        if(!empty($ovalue)){
                                                            $userlist[$okey]['offerCount']=(isset($ovalue[0]['total_sum']))?$ovalue[0]['total_sum']:NULL;
                                                            $userlist[$okey]['itemName']=(isset($offerData['item_name']))?$offerData['item_name']:NULL;
                                                            $userlist[$okey]['userID']=(isset($ovalue['Order']['user_id']))?$ovalue['Order']['user_id']:NULL;
                                                            $userlist[$okey]['decription']=(isset($ovalue['Offer']['description']))?$ovalue['Offer']['description']:NULL;
                                                            $userlist[$okey]['userName']=(isset($ovalue['Order']['User']['userName']))?$ovalue['Order']['User']['userName']:NULL;
                                                            $userlist[$okey]['email']=(isset($ovalue['Order']['User']['email']))?$ovalue['Order']['User']['email']:NULL;
                                                            $userlist[$okey]['date']=(isset($ovalue['OrderOffer']['created']))?date('m-d-Y', strtotime($ovalue['OrderOffer']['created'])):NULL;
                                                        }

                                                    }
                                                }
                                        }

                                        $offerArr[$key]['userlist']=$userlist;
                                    }
                                }
                                //// Get Offer used Count & List of users


                                //// Get List of the Items that has offer applicable
                                $this->loadModel('Offer');
                                $this->loadModel('Item');
                                $this->Offer->bindModel(
                                    array(
                                        'belongsTo' => array(
                                            'Item' => array(
                                                'className' => 'Item',
                                                'foreignKey' => 'item_id',
                                                'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                                                'fields' => array('id', 'name', 'category_id'),
                                                'type' => 'INNER'
                                            )
                                        )
                                    ), false
                                );
                                $this->loadModel('Category');
                                $this->Item->bindModel(array(
                                    'belongsTo' => array('Category' => array(
                                            'className' => 'Category',
                                            'foreignKey' => 'category_id',
                                            'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                                            'fields' => array('id', 'name'),
                                            'type' => 'INNER'
                                        )
                                    )
                                ));
                                $itmList = $this->Offer->find('all', array(
                                    'fields' => array('Offer.id', 'Offer.item_id', 'Item.id', 'Item.name', 'Item.category_id'),
                                    'conditions' => array('Item.store_id' => $store_id, 'Item.is_deleted' => 0, 'Item.is_active' => 1, 'Offer.is_deleted' => 0),
                                    'recursive' => 2
                                ));
                                //prx($itemList);
                                $nList = array();
                                $itemIDarr=array();
                                if (!empty($itmList)) {
                                    $i=0;
                                    foreach ($itmList as $ikey =>$iList) {
                                        if (!empty($iList['Item']) && !empty($iList['Item']['Category'])) {
                                            //$nList[$iList['Item']['id']] = $iList['Item']['name'];
                                            if(!in_array($iList['Item']['id'], $itemIDarr)){
                                                $itemIDarr[]=$iList['Item']['id'];
                                                $nList[$i]['itemname'] = $iList['Item']['name'];
                                                $nList[$i]['id'] = $iList['Item']['id'];
                                                $i++;
                                            }
                                        }

                                    }
                                }


                                if(!empty($nList)){
                                    $responsedata['itemsdropdown'] = $nList;
                                }
                                //// Get List of the Items that has offer applicable

                                $responsedata['message'] = "Success.";
                                $responsedata['response'] = 1;
                                if (!empty($offerCount)) {
                                    if ($offerCount > 10) {
                                        $responsedata['count'] = (string) ceil($offerCount / 10);
                                    } else {
                                        $responsedata['count'] = "1";
                                    }
                                } else {
                                    $responsedata['count'] = "1";
                                }

                                $responsedata['promoOffers'] = $offerArr;
                                //pr($responsedata);
                                return json_encode($responsedata);
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }
     /*     * ******************************************************************************************
      @Function Name : itemsList
      @Description   : this function is used to List items.
      @Author        : SmartData
      created:12/12/2016
     * ****************************************************************************************** */

    public function itemsList() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody = '{"store_id":"108"}';
        //$headers['user_id'] = 'NzU';
        //$headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "category_list.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                $this->ItemPrice->bindModel(
                                        array('belongsTo' => array(
                                                'Size' => array(
                                                    'className' => 'Size',
                                                    'foreignKey' => 'size_id',
                                                    'conditions' => array('Size.is_active' => 1, 'Size.is_deleted' => 0, 'Size.store_id' => $store_id),
                                                    'fields' => array('id', 'size'),
                                                    'order' => array('Size.id ASC')
                                                ),
                                )));
                                $this->Item->bindModel(
                                        array('hasMany' => array(
                                                'ItemPrice' => array(
                                                    'className' => 'ItemPrice',
                                                    'foreignKey' => 'item_id',
                                                    'type' => 'INNER',
                                                    'conditions' => array('ItemPrice.is_active' => 1, 'ItemPrice.is_deleted' => 0, 'ItemPrice.store_id' => $store_id),
                                                    'fields' => array('id', 'size_id'),
                                                    'order' => array('ItemPrice.position ASC')
                                                ),
                                            )
                                ));
                                $this->Item->bindModel(
                                        array('belongsTo' => array(
                                                'Category' => array(
                                                    'className' => 'Category',
                                                    'foreignKey' => 'category_id',
                                                    'conditions' => array('Category.is_active' => 1, 'Category.is_deleted' => 0, 'Category.store_id' => $store_id),
                                                    'fields' => array('id', 'name'),
                                                    'order' => array('Category.id ASC')
                                                ),
                                )));
                                $itemConditions = array('Item.store_id' => $store_id, 'Item.is_active' => 1, 'Item.is_deleted' => 0);
                                $itemListing = $this->Item->find('all', array('conditions' => $itemConditions, 'fields' => array('id', 'name', 'category_id'), 'recursive' => 2));
                                $itemListArr = array();
                                $i = 0;
                                if (!empty($itemListing)) {
                                    foreach ($itemListing as $itemList) {
                                        if (!empty($itemList['Category'])) {
                                            $itemListArr[$i]['item_id'] = $itemList['Item']['id'];
                                            $itemListArr[$i]['item_name'] = $itemList['Item']['name'];
                                            $j = 0;
                                            if (!empty($itemList['ItemPrice'])) {
                                            foreach ($itemList['ItemPrice'] as $itemSize) {
                                                if(!empty($itemSize['Size'])){
                                                    $itemListArr[$i]['Size'][$j]['size_id'] = $itemSize['Size']['id'];
                                                    $itemListArr[$i]['Size'][$j]['size_name'] = $itemSize['Size']['size'];
                                                }else{
                                                     $itemListArr[$i]['Size']=array();
                                                }
                                                $j++;
                                            }
                                            }else{
                                               $itemListArr[$i]['Size']=array();
                                            }
                                        }

                                        $i++;
                                    }
                                }
//                                pr($itemListArr);
//                                die;

                                $responsedata['message'] = "Success.";
                                $responsedata['response'] = 1;
                                $responsedata['Items'] = array_values($itemListArr);
                                //pr($responsedata);
                                return json_encode($responsedata);
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }
     /*     * ******************************************************************************************
      @Function Name : addOffer
      @Description   : this function is used to Add Offer.
      @Author        : SmartData
      created:06/12/2016
     * ****************************************************************************************** */

    public function addPromo() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        $this->Webservice->webserviceAdminLog($requestBody, "add_promo.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $this->User->bindModel(array(
                        'hasMany' => array(
                            'Permission' => array(
                                'className' => 'Permission',
                                'foreignKey' => 'user_id',
                                'type' => 'INNER',
                                'conditions' => array('Permission.is_active' => 1, 'Permission.is_deleted' => 0, 'Permission.tab_id' => 12),
                                'fields' => array('id', 'tab_id')
                            ),
                        )
                    ));
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (empty($userDet['Permission'])) {
                        $responsedata['message'] = "You are not authorized to add an offer.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if (empty($requestBody['item_id'])) {
                                    $responsedata['message'] = "Please select a item.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                if (empty($requestBody['description'])) {
                                    $responsedata['message'] = "Please enter a description.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                if (empty($requestBody['OfferDetail'])) {
                                    $responsedata['message'] = "Please select offered item.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }

                                if (empty($requestBody['unit'])) {
                                    $responsedata['unit'] = 1;
                                }

                                if ($requestBody['is_active']) {
                                    $requestBody['is_active'] = 1;
                                } else {
                                    $requestBody['is_active'] = 0;
                                }
                                if ($requestBody['is_fixed_price']) {
                                    $requestBody['is_fixed_price'] = 1;
                                } else {
                                    $requestBody['is_fixed_price'] = 0;
                                }
                                if ($requestBody['is_time']) {
                                    $requestBody['is_time'] = 1;
                                } else {
                                    $requestBody['is_time'] = 0;
                                }
                                $sizeId = 0;
                                if (empty($requestBody['size_id'])) {
                                    $requestBody['size_id'] = 0;
                                }

                                if (!empty($requestBody['size_id'])) {
                                    $sizeId = $requestBody['size_id'];
                                }

                                if (!$this->Offer->offerExistsOnItem($requestBody['item_id'], $requestBody['offer_start_date'], $requestBody['offer_end_date'], $sizeId, $requestBody['unit'])) {
                                    $imagename = "";
                                    if (!empty($requestBody['image'])) {
                                        $dat = explode(';', $requestBody['image']);
                                        $type = $dat[0];
                                        $data2 = $dat[1];
										$dat2 = explode(',', $data2);
                                        //list(, $data1) = explode(',', $data1);
                                        $data3 = base64_decode($dat2[1]);
										$jpgMimes=array('image/jpeg','image/pjpeg');
										$pngMimes=array('image/png');
										$gifMimes=array('image/gif');

										$imgdata = base64_decode($dat2[1]);
										$f = finfo_open();
										$mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
										if(in_array($mime_type,$jpgMimes)){
											$imageType="jpg";
											$imagename = uniqid() . "_" . date('Y-m-d-H-s') . '_' . $store_id . '_image.jpg';
										}else if(in_array($mime_type,$pngMimes)){
											$imageType="png";
											$imagename = uniqid() . "_" . date('Y-m-d-H-s') . '_' . $store_id . '_image.png';
										}else if(in_array($mime_type,$gifMimes)){
											$imageType="gif";
											$imagename = uniqid() . "_" . date('Y-m-d-H-s') . '_' . $store_id . '_image.gif';
										}else{
											$responsedata['message'] = "Only jpg, gif, png type images are allowed.";
											$responsedata['response'] = 0;
											return json_encode($responsedata);
										}
										$path = WWW_ROOT . "/Offer-Image/" . $imagename;
										$folderName = "/Offer-Image";
                                        $newWidth = 480;
                                        $newHeight = 320;
                                        if ($imagename) {
                                            file_put_contents($path, $data3);
                                            $ImageStatus=$this->Webservice->cropImage($path,$folderName,$imagename,$newWidth,$newHeight,$imageType);

                                        }
                                    }
                                    $offerData['offerImage']="";
                                    if(!empty($imagename) && $ImageStatus==TRUE){
                                        $offerData['offerImage'] = $imagename;
                                    }
                                    $offerData['store_id'] = $store_id;
                                    $offerData['merchant_id'] = $merchant_id;
                                    $offerData['item_id'] = trim($requestBody['item_id']);
                                    $offerData['unit'] = trim($requestBody['unit']);
                                    $offerData['is_time'] = $requestBody['is_time'];
                                    if($offerData['is_time']==1){
                                        $offerData['offer_end_time'] = trim($requestBody['offer_end_time']);
                                        $offerData['offer_start_time'] = trim($requestBody['offer_start_time']);
                                    }

                                    $offerData['description'] = trim($requestBody['description']);
                                    if (!empty($requestBody['size_id']) && isset($requestBody['size_id'])) {
                                        $offerData['size_id'] = $requestBody['size_id'];
                                    }
                                    $offerData['is_fixed_price'] = $requestBody['is_fixed_price'];

                                    $offerData['offerprice'] = ($requestBody['offerprice']) ? $requestBody['offerprice'] : 0;
                                    $offerData['is_active'] = $requestBody['is_active'];

                                    $startDate = "";
                                    $endDate = "";
                                    if (!empty($requestBody['offer_start_date'])) {
                                        $dateStart = explode("/", $requestBody['offer_start_date']);
                                        $startDate = $dateStart[2] . "-" . $dateStart[1] . "-" . $dateStart[0];
                                    }
                                    if (!empty($requestBody['offer_end_date'])) {
                                        $dateEnd = explode("/", $requestBody['offer_end_date']);
                                        $endDate = $dateEnd[2] . "-" . $dateEnd[1] . "-" . $dateEnd[0];
                                    }
                                    $offerData['offer_start_date'] = $startDate;
                                    $offerData['offer_end_date'] = $endDate;

                                    $this->Offer->saveOffer($offerData);
                                    $offerID = $this->Offer->getLastInsertId();
                                    if ($offerID) {
                                        if (isset($requestBody['OfferDetail']) && $requestBody['OfferDetail']) {
                                            foreach ($requestBody['OfferDetail'] as $key => $offerdetails) {
                                                $offerdetailsData['offerItemID'] = trim($offerdetails['item_id']);
                                                $offerdetailsData['offer_id'] = $offerID;
                                                $offerdetailsData['store_id'] = $store_id;
                                                $offerdetailsData['merchant_id'] = $merchant_id;
                                                 $offerdetailsData['discountAmt']=trim($offerdetails['discountAmt']);
                                                if (isset($offerdetails['size_id']) && $offerdetails['size_id']) {
                                                    $offerdetailsData['offerSize'] = trim($offerdetails['size_id']);
                                                } else {
                                                    $offerdetailsData['offerSize'] = 0;
                                                }
                                                $this->OfferDetail->create();
                                                $this->OfferDetail->saveOfferDetail($offerdetailsData);
                                            }
                                        }
                                        $responsedata['message'] = "Offer has been added successfully.";
                                        $responsedata['response'] = 1;
                                        return json_encode($responsedata);
                                    } else {
                                        $responsedata['message'] = "Offer could not be added.Please try again.";
                                        $responsedata['response'] = 1;
                                        return json_encode($responsedata);
                                    }
                                } else {
                                    $responsedata['message'] = "Offer on item already exists.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     /*     * ******************************************************************************************
      @Function Name : editOffer
      @Description   : this function is used to Edit Offer.
      @Author        : SmartData
      created:06/12/2016
     * ****************************************************************************************** */

    public function editPromo() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody = '{"offer_id":"141","item_id":"5017","store_id":"108","size_id":"352","unit":"3","description":"TEsting to Upload","is_fixed_price":true,"offerprice":"50","offer_start_date":"13/12/2016","offer_end_date":"15/12/2016","is_time":true,"offer_start_time":"00:30:00","offer_end_time":"00:30:00","is_active":true,"image":"","OfferDetail":[{"offered_id":"195","item_id":"4973","size_id":"","discountAmt":"10"},{"offered_id":"196","item_id":"5020","size_id":"","discountAmt":"20"},{"item_id":"5017","size_id":"352","discountAmt":"5"}]}';
        //$headers['user_id'] = 'NzU';
        //$headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "edit_promo.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $this->User->bindModel(array(
                        'hasMany' => array(
                            'Permission' => array(
                                'className' => 'Permission',
                                'foreignKey' => 'user_id',
                                'type' => 'INNER',
                                'conditions' => array('Permission.is_active' => 1, 'Permission.is_deleted' => 0, 'Permission.tab_id' => 12),
                                'fields' => array('id', 'tab_id')
                            ),
                        )
                    ));
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (empty($userDet['Permission'])) {
                        $responsedata['message'] = "You are not authorized to add an offer.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                 if (empty($requestBody['offer_id'])) {
                                    $responsedata['message'] = "Please select an Offer.";
                                    $responsedata['response'] = 0;
                                     return json_encode($responsedata);
                                }
                                if (empty($requestBody['item_id'])) {
                                    $responsedata['message'] = "Please select a item.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                if (empty($requestBody['description'])) {
                                    $responsedata['message'] = "Please enter a description.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                if (empty($requestBody['OfferDetail'])) {
                                    $responsedata['message'] = "Please select offered item.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }

                                if (empty($requestBody['unit'])) {
                                    $responsedata['unit'] = 1;
                                }

                                if ($requestBody['is_active']) {
                                    $requestBody['is_active'] = 1;
                                } else {
                                    $requestBody['is_active'] = 0;
                                }
                                if ($requestBody['is_fixed_price']) {
                                    $requestBody['is_fixed_price'] = 1;
                                } else {
                                    $requestBody['is_fixed_price'] = 0;
                                }
                                if ($requestBody['is_time']) {
                                    $requestBody['is_time'] = 1;
                                } else {
                                    $requestBody['is_time'] = 0;
                                }
                                $sizeId = 0;
                                if (empty($requestBody['size_id'])) {
                                    $requestBody['size_id'] = 0;
                                }

                                if (!empty($requestBody['size_id'])) {
                                    $sizeId = $requestBody['size_id'];
                                }
//                                pr($requestBody);

                                $conditions = array('Offer.is_deleted' => 0,'Offer.id' => $requestBody['offer_id']);
                                $offer = $this->Offer->find('first', array('conditions' => $conditions,'fields'=>array('id','offerImage')));
//                                pr($offer);
//                                die;
                                if(empty($offer)){
                                    $responsedata['message'] = "Offer is not active.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                if (!$this->Offer->offerExistsOnItem($requestBody['item_id'], $requestBody['offer_start_date'], $requestBody['offer_end_date'], $sizeId, $requestBody['unit'], $requestBody['offer_id'])) {
                                    $imagename = "";
                                    if (!empty($requestBody['image'])) {
                                        //if(!empty($offer['Offer']['offerImage'])){
                                        //$pathLoadedImg = WWW_ROOT . "/Offer-Image/" . $offer['Offer']['offerImage'];
                                        //$path2LoadedImg = WWW_ROOT . "/Offer-Image/thumb/" . $offer['Offer']['offerImage'];
                                        //unlink($pathLoadedImg);
                                        //unlink($path2LoadedImg);
                                        //}

										$dat = explode(';', $requestBody['image']);
                                        $type = $dat[0];
                                        $data2 = $dat[1];
										$dat2 = explode(',', $data2);
                                        //list(, $data1) = explode(',', $data1);
                                        $data3 = base64_decode($dat2[1]);
										$jpgMimes=array('image/jpeg','image/pjpeg');
										$pngMimes=array('image/png');
										$gifMimes=array('image/gif');

										$imgdata = base64_decode($dat2[1]);
										$f = finfo_open();
										$mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
										if(in_array($mime_type,$jpgMimes)){
											$imageType="jpg";
											$imagename = uniqid() . "_" . date('Y-m-d-H-s') . '_' . $store_id . '_image.jpg';
										}else if(in_array($mime_type,$pngMimes)){
											$imageType="png";
											$imagename = uniqid() . "_" . date('Y-m-d-H-s') . '_' . $store_id . '_image.png';
										}else if(in_array($mime_type,$gifMimes)){
											$imageType="gif";
											$imagename = uniqid() . "_" . date('Y-m-d-H-s') . '_' . $store_id . '_image.gif';
										}else{
											$responsedata['message'] = "Only jpg, gif, png type images are allowed.";
											$responsedata['response'] = 0;
											return json_encode($responsedata);
										}
										$path = WWW_ROOT . "/Offer-Image/" . $imagename;
										$folderName = "/Offer-Image";
                                        $newWidth = 480;
                                        $newHeight = 320;

                                        if ($imagename) {
                                            file_put_contents($path, $data3);
                                            $ImageStatus=$this->Webservice->cropImage($path, $folderName,$imagename,$newWidth,$newHeight,$imageType);
                                        }
                                    }else{
                                        $offerData['offerImage']=$requestBody['image'];
                                    }
                                    //$offerData['offerImage']=$offer['Offer']['offerImage'];
                                    if(!empty($imagename) && $ImageStatus==TRUE){
                                        $offerData['offerImage'] = $imagename;
                                    }
                                    $offerData['id'] = $offer['Offer']['id'];
                                    $offerData['store_id'] = $store_id;
                                    $offerData['merchant_id'] = $merchant_id;
                                    $offerData['item_id'] = trim($requestBody['item_id']);
                                    $offerData['unit'] = trim($requestBody['unit']);
                                    $offerData['is_time'] = $requestBody['is_time'];
                                    if($offerData['is_time']==1){
                                        $offerData['offer_end_time'] = trim($requestBody['offer_end_time']);
                                        $offerData['offer_start_time'] = trim($requestBody['offer_start_time']);
                                    }

                                    $offerData['description'] = trim($requestBody['description']);
                                    if (!empty($requestBody['size_id']) && isset($requestBody['size_id'])) {
                                        $offerData['size_id'] = $requestBody['size_id'];
                                    }
                                    $offerData['is_fixed_price'] = $requestBody['is_fixed_price'];
                                    if($requestBody['is_fixed_price']==0){
                                      $requestBody['offerprice']=0;
                                    }
                                    $offerData['offerprice'] = ($requestBody['offerprice']) ? $requestBody['offerprice'] : 0;
                                    $offerData['is_active'] = $requestBody['is_active'];

                                    $startDate = "";
                                    $endDate = "";
                                    if (!empty($requestBody['offer_start_date'])) {
                                        $dateStart = explode("/", $requestBody['offer_start_date']);
                                        $startDate = $dateStart[2] . "-" . $dateStart[1] . "-" . $dateStart[0];
                                    }
                                    if (!empty($requestBody['offer_end_date'])) {
                                        $dateEnd = explode("/", $requestBody['offer_end_date']);
                                        $endDate = $dateEnd[2] . "-" . $dateEnd[1] . "-" . $dateEnd[0];
                                    }
                                    $offerData['offer_start_date'] = $startDate;
                                    $offerData['offer_end_date'] = $endDate;

                                    $this->Offer->saveOffer($offerData);
                                    $offerID = $this->Offer->getLastInsertId();
                                    if ($this->OfferDetail->deleteallOfferItems($offer['Offer']['id'])) {
                                        if (isset($requestBody['OfferDetail']) && $requestBody['OfferDetail']) {
                                            foreach ($requestBody['OfferDetail'] as $key => $offerdetails) {
                                                if (isset($offerdetails['offered_id']) && !empty($offerdetails['offered_id'])) {
                                                    $offerdetailsData['id'] = $offerdetails['offered_id'];
                                                } else {
                                                    $offerdetailsData['id'] = '';
                                                }
                                                $offerdetailsData['offerItemID'] = trim($offerdetails['item_id']);
                                                $offerdetailsData['offer_id'] = $offer['Offer']['id'];
                                                $offerdetailsData['store_id'] = $store_id;
                                                $offerdetailsData['merchant_id'] = $merchant_id;
                                                if (isset($offerdetails['size_id']) && $offerdetails['size_id']) {
                                                    $offerdetailsData['offerSize'] = trim($offerdetails['size_id']);
                                                } else {
                                                    $offerdetailsData['offerSize'] = 0;
                                                }
                                                if ($offerdetails['discountAmt']) {
                                                     if($requestBody['is_fixed_price']==0){
                                                        $offerdetailsData['discountAmt'] = trim($offerdetails['discountAmt']);
                                                     }else{
                                                        $offerdetailsData['discountAmt'] = 0;
                                                     }
                                                    } else {
                                                        $offerdetailsData['discountAmt'] = 0;
                                                    }

                                                $this->OfferDetail->saveOfferDetail($offerdetailsData);
                                            }
                                        }
                                        $responsedata['message'] = "Offer has been updated successfully.";
                                        $responsedata['response'] = 1;
                                        return json_encode($responsedata);
                                    } else {
                                        $responsedata['message'] = "Offer could not be updated.Please try again.";
                                        $responsedata['response'] = 1;
                                        return json_encode($responsedata);
                                    }
                                } else {
                                    $responsedata['message'] = "Offer on item already exists.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }
     /*     * ******************************************************************************************
      @Function Name : updateOfferStatus
      @Description   : this function is used to update selected Offer status .
      @Author        : SmartData
      created:07/12/2016
     * ****************************************************************************************** */

    public function updatePromoStatus() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody = '{"store_id":"108", "offer_id":"136", "status":false}';
        //$headers['user_id'] = 'NzU';
        //$headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "update_promo_status.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if (empty($requestBody['offer_id'])) {
                                    $responsedata['message'] = "Please select an offer.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                $offer_id = $requestBody['offer_id'];
                                $offerDet = $this->Offer->find('first', array('conditions' => array('Offer.id' => $offer_id,'Offer.is_deleted' =>0)));
                                if ($offerDet) {
                                    $data['id'] = $offerDet['Offer']['id'];
                                    if ($requestBody['status']) {
                                        $data['is_active'] = 1;
                                    } else {
                                        $data['is_active'] = 0;
                                    }

                                    if ($this->Offer->save($data)) {
                                        $responsedata['message'] = "Offer has been updated successfully.";
                                        $responsedata['response'] = 1;
                                        return json_encode($responsedata);
                                    } else {
                                        $responsedata['message'] = "Offer could not be updated, please try again.";
                                        $responsedata['response'] = 0;
                                        return json_encode($responsedata);
                                    }
                                } else {
                                    $responsedata['message'] = "Offer not found.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

    /*     * ******************************************************************************************
      @Function Name : removeOffer
      @Description   : this function is used to remove selected Offer.
      @Author        : SmartData
      created:07/12/2016
     * ****************************************************************************************** */

    public function removePromo() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody = '{"store_id":"108", "offer_id":"136"}';
        //$headers['user_id'] = 'NzU';
        //$headers['merchant_id'] = 85;

        $this->Webservice->webserviceAdminLog($requestBody, "remove_promo.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if (empty($requestBody['offer_id'])) {
                                    $responsedata['message'] = "Please select a offer.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                                $offer_id = $requestBody['offer_id'];
                                $offerDet = $this->Offer->find('first', array('conditions' => array('Offer.id' => $offer_id)));
                                if ($offerDet) {
                                    $data['id'] = $offerDet['Offer']['id'];
                                    $data['is_deleted'] = 1;
                                    if ($this->Offer->save($data)) {
                                        $responsedata['message'] = "Offer has been deleted successfully.";
                                        $responsedata['response'] = 1;
                                        return json_encode($responsedata);
                                    } else {
                                        $responsedata['message'] = "Offer could not be deleted, please try again.";
                                        $responsedata['response'] = 0;
                                        return json_encode($responsedata);
                                    }
                                } else {
                                    $responsedata['message'] = "Offer not found.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     /*     * ******************************************************************************************
      @Function Name : searchOffers
      @Description   : this function is used to List search offer / Promotions by status, Item and Keywords:Item name,Offer Description.
      @Author        : SmartData
      created:13/12/2016
     * ****************************************************************************************** */

    public function searchPromo() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody = '{"store_id":"2","keyword":"","isActive":"","item_id":""}';  //isActive=>1 show active isActive=>2 show deactive and if isActive=>"" all
        //$headers['user_id'] = 'MzEw';
        //$headers['merchant_id'] = 1;
        $this->Webservice->webserviceAdminLog($requestBody, "search_promo.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {

                                $value = "";
                                $criteria = "Offer.store_id =$store_id AND Offer.is_deleted=0";

                                $this->loadModel('Category');
                                $this->Item->bindModel(array(
                                    'belongsTo' => array('Category' => array(
                                            'className' => 'Category',
                                            'foreignKey' => 'category_id',
                                            'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                                            'fields' => array('id', 'name'),
                                            'type' => 'INNER'
                                        )
                                    )
                                        ), false);
                                $this->Offer->bindModel(
                                        array(
                                    'belongsTo' => array(
                                        'Item' => array(
                                            'className' => 'Item',
                                            'foreignKey' => 'item_id',
                                            'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                                            'fields' => array('id', 'name', 'category_id'),
                                            'type' => 'INNER'
                                        ),
                                        'Size' => array(
                                            'className' => 'Size',
                                            'foreignKey' => 'size_id',
                                            'conditions' => array('Size.is_deleted' => 0, 'Size.is_active' => 1),
                                            'fields' => array('id', 'size'),
                                            'type' => 'INNER'
                                        )
                                    )
                                        ), false
                                );
                                $this->OfferDetail->bindModel(
                                        array(
                                    'belongsTo' => array(
                                        'Item' => array(
                                            'className' => 'Item',
                                            'foreignKey' => 'offerItemID',
                                            'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                                            'fields' => array('id', 'name', 'category_id'),
                                            'type' => 'INNER'
                                        ),
                                        'Size' => array(
                                            'className' => 'Size',
                                            'foreignKey' => 'offerSize',
                                            'conditions' => array('Size.is_deleted' => 0, 'Size.is_active' => 1),
                                            'fields' => array('id', 'size'),
                                            'type' => 'INNER'
                                        )
                                    )
                                        ), false
                                );
                                $this->Offer->bindModel(
                                        array(
                                    'hasMany' => array(
                                        'OfferDetail' => array(
                                            'className' => 'OfferDetail',
                                            'foreignKey' => 'offer_id',
                                            'conditions' => array('OfferDetail.is_deleted' => 0, 'OfferDetail.is_active' => 1),
                                            'fields' => array('id', 'offerItemID', 'offerSize', 'discountAmt'),
                                            'type' => 'INNER'
                                        )
                                    )
                                        ), false
                                );
                                if (!empty($requestBody['keyword'])) {
                                    $value = trim($requestBody['keyword']);
                                    $criteria .= " AND (Offer.description LIKE '%" . $value . "%' OR Item.name LIKE '%" . $value . "%')";
                                }

                                 if ($requestBody['isActive'] == 1) {
                                    $active = 1;
                                    $criteria .= " AND (Offer.is_active =$active)";
                                } elseif ($requestBody['isActive'] == 2) {
                                    $active = 0;
                                    $criteria .= " AND (Offer.is_active =$active)";
                                }
                                if (isset($requestBody['item_id']) && $requestBody['item_id'] != '') {
                                    $item = trim($requestBody['item_id']);
                                    $criteria .= " AND (Offer.item_id =$item)";
                                }


                                //$offerCount = $this->Offer->find('count', array('conditions' => array($criteria)));

                                 $offerDet = $this->Offer->find('all', array('recursive' => 3, 'conditions' => array($criteria), 'order' => array('Offer.created' => 'DESC'), 'fields' => array('id', 'item_id', 'description', 'offer_start_date', 'offer_end_date', 'offer_start_time', 'offer_end_time', 'is_active', 'size_id', 'offerImage', 'is_time','is_fixed_price','offerprice','unit')));
//                                pr($offerCount);
//                                pr($offerDet);
//                                die;
                                $offerArr = array();
                                if (!empty($offerDet)) {
                                     $protocol = 'http://';
                                    if (isset($_SERVER['HTTPS'])) {
                                        if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                                            $protocol = 'https://';
                                        }
                                    }
                                    $i = 0;
                                    foreach ($offerDet as $offerList) {
                                        if (!empty($offerList['Item']['Category'])) {
                                            $offerArr[$i]['id'] = $offerList['Offer']['id'];
                                            if (!empty($offerList['Offer']['description'])) {
                                                $offerArr[$i]['description'] = $offerList['Offer']['description'];
                                            } else {
                                                $offerArr[$i]['description'] = "";
                                            }

                                            if (!empty($offerList['Offer']['item_id'])) {
                                                $offerArr[$i]['item_id'] = $offerList['Offer']['item_id'];
                                                if (!empty($offerList['Item']['id'])) {
                                                    $offerArr[$i]['item_name'] = $offerList['Item']['name'];
                                                } else {
                                                    $offerArr[$i]['item_name'] = "";
                                                }
                                            } else {
                                                $offerArr[$i]['item_id'] = "";
                                                $offerArr[$i]['item_name'] = "";
                                            }

                                            if (!empty($offerList['Size'])) {
                                                $offerArr[$i]['size_id'] = $offerList['Size']['id'];
                                                $offerArr[$i]['size_name'] = $offerList['Size']['size'];
                                            } else {
                                                $offerArr[$i]['size_id'] = "";
                                                $offerArr[$i]['size_name'] = "";
                                            }



                                            if (!empty($offerList['Offer']['offer_start_date'])) {
                                                $date = explode("-", $offerList['Offer']['offer_start_date']);
                                                $startDate = $date[2] . "/" . $date[1] . "/" . $date[0];
                                                $offerArr[$i]['offer_start_date'] = $startDate;
                                            } else {
                                                $offerArr[$i]['offer_start_date'] = "";
                                            }

                                            if (!empty($offerList['Offer']['offer_end_date'])) {
                                                $dateEnd = explode("-", $offerList['Offer']['offer_end_date']);
                                                $endDate = $dateEnd[2] . "/" . $dateEnd[1] . "/" . $dateEnd[0];
                                                $offerArr[$i]['offer_end_date'] = $endDate;
                                            } else {
                                                $offerArr[$i]['offer_end_date'] = "";
                                            }

                                            if (!empty($offerList['Offer']['offer_start_time'])) {
                                                $offerArr[$i]['offer_start_time'] = $offerList['Offer']['offer_start_time'];
                                                if($offerList['Offer']['offer_start_time']=="24:00:00"){
                                                    $offerArr[$i]['offer_start_time'] = "00:00:00";
                                                }
                                            } else {
                                                $offerArr[$i]['offer_start_time'] = "";
                                            }
                                            if (!empty($offerList['Offer']['offer_end_time'])) {
                                                $offerArr[$i]['offer_end_time'] = $offerList['Offer']['offer_end_time'];
                                                if($offerList['Offer']['offer_end_time']=="24:00:00"){
                                                    $offerArr[$i]['offer_end_time'] = "00:00:00";
                                                }
                                            } else {
                                                $offerArr[$i]['offer_end_time'] = "";
                                            }

                                            if (!empty($offerList['Offer']['is_active'])) {
                                                $offerArr[$i]['is_active'] = TRUE;
                                            } else {
                                                $offerArr[$i]['is_active'] = FALSE;
                                            }
                                             if (!empty($offerList['Offer']['is_time'])) {
                                                $offerArr[$i]['is_time'] = TRUE;

                                            } else {
                                                $offerArr[$i]['is_time'] = FALSE;
;                                            }
                                            if (!empty($offerList['Offer']['is_fixed_price'])) {
                                                $offerArr[$i]['is_fixed_price'] = TRUE;
                                            } else {
                                                $offerArr[$i]['is_fixed_price'] = FALSE;
                                            }

                                            if($offerList['Offer']['unit']){
                                                  $offerArr[$i]['unit'] = $offerList['Offer']['unit'];
                                            }else{
                                                $offerArr[$i]['unit']="";
                                            }
                                             if($offerList['Offer']['offerprice']){
                                                  $offerArr[$i]['offerprice'] = $offerList['Offer']['offerprice'];
                                            }else{
                                                $offerArr[$i]['offerprice']="";
                                            }
                                             if (!empty($offerList['Offer']['offerImage'])) {
                                       $offerArr[$i]['offerImage'] = $protocol . $storeResult['Store']['store_url'] . "/Offer-Image/" . $offerList['Offer']['offerImage'];
                                    } else {
                                       $offerArr[$i]['offerImage'] = "";
                                    }

                                            if (!empty($offerList['OfferDetail'])) {
                                                $o = 0;
                                                foreach ($offerList['OfferDetail'] as $offerDetail) {
                                                    if (!empty($offerDetail['Item']['Category'])) {
                                                    $offerArr[$i]['OfferDetail'][$o]['offered_id'] = $offerDetail['id'];

                                                    if (!empty($offerDetail['offerItemID'])) {
                                                       $offerArr[$i]['OfferDetail'][$o]['item_id'] = $offerDetail['offerItemID'];
                                                        if (!empty($offerDetail['Item']['id'])) {
                                                           $offerArr[$i]['OfferDetail'][$o]['item_name'] = $offerDetail['Item']['name'];
                                                        } else {
                                                            $offerArr[$i]['OfferDetail'][$o]['item_name'] = "";
                                                        }
                                                    } else {
                                                        $offerArr[$i]['OfferDetail'][$o]['item_id'] = "";
                                                        $offerArr[$i]['OfferDetail'][$o]['item_name'] = "";
                                                    }

                                                    if (!empty($offerDetail['Size'])) {
                                                       $offerArr[$i]['OfferDetail'][$o]['size_id'] = $offerDetail['Size']['id'];
                                                       $offerArr[$i]['OfferDetail'][$o]['size_name'] = $offerDetail['Size']['size'];
                                                    } else {
                                                        $offerArr[$i]['OfferDetail'][$o]['size_id'] = "";
                                                       $offerArr[$i]['OfferDetail'][$o]['size_name'] = "";
                                                    }
                                                    if (!empty($offerDetail['discountAmt'])) {
                                                       $offerArr[$i]['OfferDetail'][$o]['discountAmt'] = $offerDetail['discountAmt'];
                                                    } else {
                                                       $offerArr[$i]['OfferDetail'][$o]['discountAmt'] = "";
                                                    }
                                                    $o++;
                                                }
                                            }

                                            }else{
                                                 $offerArr[$i]['OfferDetail']=array();
                                            }


                                            $i++;
                                        }
                                    }
                                }
                                $responsedata['message'] = "Success.";
                                $responsedata['response'] = 1;
                                //if (!empty($offerCount)) {
                                //    if ($offerCount > 10) {
                                //        $responsedata['count'] = (string) ceil($offerCount / 10);
                                //    } else {
                                //        $responsedata['count'] = "1";
                                //    }
                                //} else {
                                //    $responsedata['count'] = "1";
                                //}

                                $responsedata['promoOffers'] = $offerArr;
                                //pr($responsedata);
                                return json_encode($responsedata);
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     /*     * ******************************************************************************************
      @Function Name : usersList
      @Description   : this function is used to show list of user for Coupons sharing.
      @Author        : SmartData
      created:13/12/2016
     * ****************************************************************************************** */

    public function usersList() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody = '{"store_id":"108","page_number":"4"}'; //isActive=>1 show active isActive=>2 show deactive and if isActive=>0 all
//        $headers['user_id'] = 'NzU';
//        $headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "users_list.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                               $criteria = array('User.merchant_id' => $merchant_id, 'User.role_id' => array(4, 5), 'User.is_deleted' => 0, 'User.is_active' => 1);

                               if (empty($requestBody['page_number'])) {
                                $requestBody['page_number']=1;
                               }
                               $limit = 9;
                                $offset = $requestBody['page_number'] * 9 - 9;

                                $usersCount = $this->User->find('count', array('conditions' => array($criteria)));
                                $userList = $this->User->find('all', array('offset' => $offset, 'limit' => $limit,'conditions' => array($criteria),'fields' => array('User.fname', 'User.lname', 'User.email', 'User.id', 'User.created'), 'order' => array('User.created' => 'DESC')));
//                                pr($usersCount);
//                                pr($userList);
//                               die;
                               $usersArr=array();
                               if(!empty($userList)){
                                   $i=0;
                                   foreach($userList as $userDetail){
                                       $usersArr[$i]['id']=$userDetail['User']['id'];
                                       if(!empty($userDetail['User']['fname'])){
                                           $usersArr[$i]['name']=$userDetail['User']['fname']." ".$userDetail['User']['lname'];
                                       }else{
                                           $usersArr[$i]['name']="";
                                       }
                                       if(!empty($userDetail['User']['email'])){
                                           $usersArr[$i]['email']=$userDetail['User']['email'];
                                       }else{
                                           $usersArr[$i]['email']="";
                                       }
                                       if(!empty($userDetail['User']['created'])){
                                            $dateTime=  explode(" ", $userDetail['User']['created']);
                                            $date=explode("-", $dateTime[0]);
                                            $finalDate=$date[2]."/".$date[1]."/".$date[0];
                                            $usersArr[$i]['date']=$finalDate;
                                          $usersArr[$i]['time']=$dateTime[1];
                                       }else{
                                           $usersArr[$i]['date']="";
                                           $usersArr[$i]['time']="";
                                       }
                                        $i++;
                                   }
                               }

                                $responsedata['message'] = "Success.";
                                $responsedata['response'] = 1;
                                 if(!empty($usersCount)){
                                    if($usersCount>10){
                                        $responsedata['count'] = (string)ceil($usersCount/10);
                                    }else{
                                        $responsedata['count'] = "1";
                                    }

                                }else{
                                   $responsedata['count'] = "0";
                                }

                                $responsedata['Users'] = $usersArr;
                                //pr($responsedata);
                                return json_encode($responsedata);
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

    /*     * ******************************************************************************************
      @Function Name : couponShare
      @Description   : this function is used to share Coupon with users.
      @Author        : SmartData
      created:13/12/2016
     * ****************************************************************************************** */

    public function couponShare() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody = '{"store_id":"108","coupon_id":"65","user_id":["588","593","589"]}'; //isActive=>1 show active isActive=>2 show deactive and if isActive=>0 all
//        $headers['user_id'] = 'NzU';
//        $headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "coupon_share.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url', 'Store.address', 'Store.city', 'Store.state', 'Store.zipcode', 'Store.store_name', 'Store.phone','Store.email_id','Store.store_url')));
                            if (!empty($storeResult)) {
                                $alreadyShared = 0;
                                $newshared = 0;

                                $couponDetail = $this->Coupon->find('first', array('conditions' => array("Coupon.id"=>$requestBody['coupon_id'],"Coupon.store_id"=>$store_id)));
                                foreach ($requestBody['user_id'] as $data) {
                                    $this->loadModel('User');
                                    $this->User->bindModel(array('belongsTo' => array('CountryCode')));
                                    $shareuserdetail = $this->User->find('first', array('fields' => array('User.id', 'User.fname', 'User.lname', 'User.email', 'User.phone', 'User.is_emailnotification', 'User.is_smsnotification', 'User.country_code_id', 'CountryCode.code'), 'conditions' => array('User.id' => $data)));
                                    if (!empty($shareuserdetail)) {
                                        $userCoupon['UserCoupon']['user_id'] = $shareuserdetail['User']['id'];
                                        $userCoupon['UserCoupon']['store_id'] = $store_id;
                                        $userCoupon['UserCoupon']['coupon_id'] = $requestBody['coupon_id'];
                                        $userCoupon['UserCoupon']['coupon_code'] = $couponDetail['Coupon']['coupon_code'];
                                        $userCoupon['UserCoupon']['merchant_id'] = $merchant_id;
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
                                            $emailSuccess = $this->EmailTemplate->storeTemplates($store_id, $merchant_id, $template_type);
                                            $this->loadModel('Coupon');
                                            if ($emailSuccess) {
                                                if ($couponDetail['Coupon']['promotional_message']) {
                                                    $smsData = nl2br($couponDetail['Coupon']['promotional_message']);
                                                } else {
                                                    $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                                                }
                                                $emailData = $emailSuccess['EmailTemplate']['template_message'];
                                                $subject = $emailSuccess['EmailTemplate']['template_subject'];
                                                $couponcode = $couponDetail['Coupon']['coupon_code'];
                                                if ($shareuserdetail['User']['is_emailnotification'] == 1) {
                                                    $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                                                    $emailData = str_replace('{COUPON}', $couponcode, $emailData);
                                                    $emailData = str_replace('{STORE_NAME}', $storeResult['Store']['store_name'], $emailData);
                                                    $storeAddress = $storeResult['Store']['address'] . "<br>" . $storeResult['Store']['city'] . ", " . $storeResult['Store']['state'] . " " . $storeResult['Store']['zipcode'];
                                                    $storePhone = $storeResult['Store']['phone'];
                                                    $url = "http://" . $storeResult['Store']['store_url'];
                                                    $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeResult['Store']['store_url'] . "</a>";
                                                    $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                                                    $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                                                    $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);
                                                    $subject = ucwords(str_replace('_', ' ', $subject));
                                                    $this->Email->to = $shareuserdetail['User']['email'];
                                                    $this->Email->subject = $subject;
                                                    $this->Email->from = $storeResult['Store']['email_id'];
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
                                                    $smsData = str_replace('{STORE_NAME}', $storeResult['Store']['store_name'], $smsData);
						    $smsData = str_replace('{STORE_PHONE}', $storePhone, $smsData);
                                                    $message = $smsData;
                                                    $mob = $shareuserdetail['CountryCode']['code'] . "" . str_replace(array('(', ')', ' ', '-'), '', $shareuserdetail['User']['phone']);
                                                    $this->Webservice->sendSmsNotification($mob, $message,$store_id);
                                                }
                                            }
                                        } else {
                                            $alreadyShared++;
                                        }
                                    }
                                }
                                $message = "coupons has been shared successfuly.";
                                $responsedata['message'] = $message;
                                $responsedata['response'] = 1;
                                return json_encode($responsedata);
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

     public function phoneNumberChange($number = null) {

        $phone = preg_replace("/[^0-9]/", "", $number);
        $phoneNumber = "(" . substr($phone, 0, 3) . ') ' .
                substr($phone, 3, 3) . '-' .
                substr($phone, 6);
        return $phoneNumber;
    }

     /*     * ******************************************************************************************
      @Function Name : getAlarmTime
      @Description   : this function is used for get Alarm Time List
      @Author        : SmartData
      created:19/12/2016
     * ****************************************************************************************** */

     public function getAlarmTime() {
        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
       // $headers['user_id']="NzU";// Ekansh test server
//        $headers['user_id']="MzEw";
//        $headers['merchant_id']=1;

        $this->Webservice->webserviceAdminLog($requestBody,"get_alarm_time.txt",$headers);
        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantCheck = $this->Merchant->find('first', array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantCheck)) {
                   if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                       $roleID=3;
                            $user_id = $this->Encryption->decode($headers['user_id']);
                            $userResult = $this->User->find("first", array("conditions" => array("User.id" => $user_id,"User.role_id" => $roleID, "User.merchant_id" => $merchant_id, "User.is_active" => 1,"User.is_deleted" => 0), 'fields' => array('id')));
                   if (!empty($userResult)) {

                             $getAlarmTime = $this->AlarmTime->find("all", array("conditions" => array("AlarmTime.is_active" => 1,"AlarmTime.is_deleted" => 0), 'fields' => array('id', 'alarm_time')));
                             $getAlarmTimeArr=array();
                             if(!empty($getAlarmTime)){
                                 $i=0;
                                 foreach ($getAlarmTime as $getAlarmList){
                                     $getAlarmTimeArr[$i]['id']=$getAlarmList['AlarmTime']['id'];
                                     if(!empty($getAlarmList['AlarmTime']['alarm_time'])){
                                         $getAlarmTimeArr[$i]['alarm_time']=$getAlarmList['AlarmTime']['alarm_time'];
                                     }else{
                                        $getAlarmTimeArr[$i]['alarm_time']="";
                                     }

                                     $i++;
                                 }
                             }
                        $responsedata['message'] = "Success.";
                        $responsedata['response'] = 1;
                        $responsedata['Alarm']=$getAlarmTimeArr;
                        return json_encode($responsedata);


                   } else {
                        $responsedata['message'] = "No active user found.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                        $responsedata['message'] = "Please login.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
            } else {
                $responsedata['message'] = "Merchant not found.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

    /*     * ******************************************************************************************
      @Function Name : getNotificationDetail
      @Description   : this function is used for get Notifications Details.
      @Author        : SmartData
      created:19/12/2016
     * ****************************************************************************************** */

    public function getNotificationDetail() {
        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody = '{"store_id": "2"}'; //  type="monthly/daily" like
        //$headers['user_id'] = "MzEw";
        //$headers['merchant_id'] = 1;
        $responsedata = array();
        $requestBody = json_decode($requestBody, true);
        $this->Webservice->webserviceAdminLog($requestBody, "get_notification_detail.txt", $headers);
        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantCheck = $this->Merchant->find('first', array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantCheck)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $roleID = 3;
                    $user_id = $this->Encryption->decode($headers['user_id']);

                    $userResult = $this->User->find("first", array("conditions" => array("User.id" => $user_id, "User.role_id" => $roleID, "User.merchant_id" => $merchant_id, "User.is_active" => 1, "User.is_deleted" => 0), 'fields' => array('id')));
                    if (!empty($userResult)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                $getNotification = $this->NotificationConfiguration->find("first", array("conditions" => array("NotificationConfiguration.is_active" => 1,"NotificationConfiguration.is_deleted" => 0,"NotificationConfiguration.user_id" => $user_id,"NotificationConfiguration.store_id" => $store_id,"NotificationConfiguration.merchant_id" => $merchant_id), 'fields' => array('id', 'user_id', 'store_id', 'merchant_id', 'order_notification', 'show_in_notification', 'sound', 'badge_app', 'add_alarm','alarm_time_id')));
                                $getNotificationArr=array();
                                if(!empty($getNotification)){
                                    $getNotificationArr['order_notification']=FALSE;
                                    $getNotificationArr['show_in_notification']=FALSE;
                                    $getNotificationArr['sound']=FALSE;
                                    $getNotificationArr['badge_app']=FALSE;
                                    $getNotificationArr['add_alarm']=FALSE;
                                    if($getNotification['NotificationConfiguration']['order_notification']==1){
                                      $getNotificationArr['order_notification']=TRUE;
                                    }
                                    if($getNotification['NotificationConfiguration']['show_in_notification']==1){
                                      $getNotificationArr['show_in_notification']=TRUE;
                                    }
                                    if($getNotification['NotificationConfiguration']['sound']==1){
                                      $getNotificationArr['sound']=TRUE;
                                    }
                                    if($getNotification['NotificationConfiguration']['badge_app']==1){
                                      $getNotificationArr['badge_app']=TRUE;
                                    }
                                    if($getNotification['NotificationConfiguration']['add_alarm']==1){
                                      $getNotificationArr['add_alarm']=TRUE;
                                    }
                                     if(!empty($getNotification['NotificationConfiguration']['alarm_time_id'])){
                                      $getNotificationArr['alarm_time_id']=$getNotification['NotificationConfiguration']['alarm_time_id'];
                                    }else{
                                       $getNotificationArr['alarm_time_id']="";
                                    }
                                }else{
                                    $getNotification=array();
                                }

                                $responsedata['message'] = "Success.";
                                $responsedata['response'] = 1;
                                $responsedata['Notifications'] = $getNotificationArr;
                                return json_encode($responsedata);
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "No active user found.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }
    /*     * ******************************************************************************************
      @Function Name : getNotificationDetail
      @Description   : this function is used for get Notifications Details.
      @Author        : SmartData
      created:19/12/2016
     * ****************************************************************************************** */

    public function updateNotificationDetail() {
        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
//        $requestBody = '{"store_id": "2","alarm_time_id":"3","order_notification":true,"show_in_notification":false,"sound":true,"badge_app":false,"add_alarm":true}'; //  type="monthly/daily" like
//        $headers['user_id'] = "MzEw";
//        $headers['merchant_id'] = 1;
        $responsedata = array();
        $requestBody = json_decode($requestBody, true);
        $this->Webservice->webserviceAdminLog($requestBody, "get_notification_detail.txt", $headers);
        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantCheck = $this->Merchant->find('first', array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantCheck)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $roleID = 3;
                    $user_id = $this->Encryption->decode($headers['user_id']);

                    $userResult = $this->User->find("first", array("conditions" => array("User.id" => $user_id, "User.role_id" => $roleID, "User.merchant_id" => $merchant_id, "User.is_active" => 1, "User.is_deleted" => 0), 'fields' => array('id')));
                    if (!empty($userResult)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                if ($requestBody['add_alarm']) {
                                    if (empty($requestBody['alarm_time_id'])) {
                                        $responsedata['message'] = "Please select time.";
                                        $responsedata['response'] = 0;
                                        return json_encode($responsedata);
                                    }
                                }

                                $getNotificationDetail = $this->NotificationConfiguration->find("first", array("conditions" => array("NotificationConfiguration.is_active" => 1,"NotificationConfiguration.is_deleted" => 0,"NotificationConfiguration.user_id" => $user_id,"NotificationConfiguration.store_id" => $store_id,"NotificationConfiguration.merchant_id" => $merchant_id), 'fields' => array('id', 'user_id', 'store_id', 'merchant_id', 'order_notification', 'show_in_notification', 'sound', 'badge_app', 'add_alarm','alarm_time_id')));
                                if(!empty($getNotificationDetail)){
                                    $getNotification['id']=$getNotificationDetail['NotificationConfiguration']['id'];
                                    $getNotification['store_id'] =$getNotificationDetail['NotificationConfiguration']['store_id'];
                                    $getNotification['user_id'] = $getNotificationDetail['NotificationConfiguration']['user_id'];
                                    $getNotification['merchant_id'] = $getNotificationDetail['NotificationConfiguration']['merchant_id'];
                                    $getNotification['order_notification'] = 0;
                                    $getNotification['show_in_notification'] = 0;
                                    $getNotification['sound'] = 0;
                                    $getNotification['badge_app'] = 0;
                                    $getNotification['add_alarm'] = 0;
                                    if ($requestBody['order_notification']) {
                                        $getNotification['order_notification'] = 1;
                                    }
                                    if ($requestBody['show_in_notification']) {
                                        $getNotification['show_in_notification'] = 1;
                                    }
                                    if ($requestBody['sound']) {
                                        $getNotification['sound'] = 1;
                                    }
                                    if ($requestBody['badge_app']) {
                                        $getNotification['badge_app'] = 1;
                                    }
                                    if ($requestBody['add_alarm']) {
                                        $getNotification['add_alarm'] = 1;
                                    }
                                    if (!empty($requestBody['alarm_time_id'])) {
                                        $getNotification['alarm_time_id'] = $requestBody['alarm_time_id'];
                                    } else {
                                        $getNotification['alarm_time_id'] = 1;
                                    }
                                }

                                if($this->NotificationConfiguration->save($getNotification)){
                                $responsedata['message'] = "Notification settings has been updated succesfully.";
                                $responsedata['response'] = 1;
                                return json_encode($responsedata);
                                }else{
                                $responsedata['message'] = "Record could not be updated, please try again.";
                                $responsedata['response'] = 1;
                                return json_encode($responsedata);
                                }

                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "No active user found.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }

    public function orderNotification($orderId = null) {
        $this->Order->bindModel(array('belongsTo' => array('DeliveryAddress' => array('foreignKey' => 'delivery_address_id','fields' => array('id','name_on_bell')))), false);
        $this->Order->bindModel(array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id',
                    'fields' => array('id', 'fname', 'lname'),
                    'conditions' => array('User.is_deleted' => 0, 'User.is_active' => 1)
                ))
                ), false);
        $this->OrderItem->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name')))), false);
        $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'item_id')))), false);

        $orderDetail = $this->Order->find('first', array('recursive' => 2, 'conditions' => array('Order.id' => $orderId, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'is_future_order' => 0), 'fields' => array('id', 'order_number', 'seqment_id', 'order_status_id', 'pickup_time', 'is_pre_order', 'user_id', 'store_id', 'merchant_id','delivery_address_id')));
       // {"order_id":"4447","oder_message":"Veg Pizza with topping","customer_name":"Rishabh","Order timing":"MM-dd-yyyy hh:mm a","orer_type":"Pickup/Delivery"}

        if(!empty($orderDetail)){
            $orderArr = array();
        $orderArr['order_id'] = $orderDetail['Order']['id'];
        $store_id=$orderDetail['Order']['store_id'];
        if ($orderDetail['Order']['seqment_id'] == 3) {
            $orderArr['order_type'] = "Home Delivery";
        } elseif ($orderDetail['Order']['seqment_id'] == 2) {
            $orderArr['order_type'] = "Take Away";
        }
        if (!empty($orderDetail['Order']['pickup_time'])) {
            $orderArr['Order_timing'] = $orderDetail['Order']['pickup_time'];
        } else{
            $orderArr['Order_timing'] = "";
        }

        if (!empty($orderDetail['DeliveryAddress']['name_on_bell'])) {
            $orderArr['customer_name'] = $orderDetail['DeliveryAddress']['name_on_bell'];
        } elseif (!empty($orderDetail['User']['fname'])) {
            $orderArr['customer_name'] = $orderDetail['User']['fname'] . " " . $orderDetail['User']['lname'];
        } else {
            $orderArr['customer_name'] = "";
        }
        $orderArr['store_id'] = $orderDetail['Order']['store_id'];
        $orderArr['notification_type'] = "1";
        $orderNotification_type = "1";
        $item_message='';
        if(!empty($orderDetail['OrderItem'])){
            $j=2;
            foreach($orderDetail['OrderItem'] as $items){
                if(!empty($items['Item']))
                $item_message.=$items['Item']['name']." ".$j.". ";
                $j++;
            }
        }

        $orderArr['order_message']=$item_message;
        $message="New Order";
        if(!empty($orderArr)){
           $message=json_encode($orderArr);
        }
        $this->sendNotification($message,$store_id,$orderNotification_type);

        }

    }

    public function bookingNotification($orderId = null) {
        $this->Booking->bindModel(array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id',
                    'fields' => array('id', 'fname', 'lname','email'),
                    'conditions' => array('User.is_deleted' => 0, 'User.is_active' => 1)
                ))
        ), false);
        $bookingDetail = $this->Booking->find('first', array('recursive' => 1, 'conditions' => array('Booking.id' => $orderId, 'Booking.is_active' => 1, 'Booking.is_deleted' => 0)));
      //{"booking_id":"335","number_person":"1","special_request":"Please make my table clean.","customer_name":"Rishabh","booking_timing":"MM-dd-yyyy hh:mm a","notification_type":"2"}

        $orderArr = array();
        if(!empty($bookingDetail)){
            $orderArr['booking_id'] = $bookingDetail['Booking']['id'];
            $store_id=$bookingDetail['Booking']['store_id'];

            $orderArr['number_person'] = "";
            if (!empty($bookingDetail['Booking']['number_person'])) {
                $orderArr['number_person'] = $bookingDetail['Booking']['number_person'];
            }

            $orderArr['special_request'] = "";
            if (!empty($bookingDetail['Booking']['special_request'])) {
                $orderArr['special_request'] = $bookingDetail['Booking']['special_request'];
            }


            $orderArr['name'] = "";
            if (!empty($bookingDetail['User']['fname'])) {
                $orderArr['name'] = $bookingDetail['User']['fname']." ".$bookingDetail['User']['lname'] ;
            }

            $orderArr['email'] = "";
            if (!empty($bookingDetail['User']['email'])) {
               $orderArr['email'] = $bookingDetail['User']['email'];
            }

            $orderArr['is_replied'] = FALSE;
            if ($bookingDetail['Booking']['is_replied']==1) {
                $orderArr['is_replied'] = TRUE;
            }


            $orderArr['admin_comment'] = "";
            if (!empty($bookingDetail['Booking']['admin_comment'])) {
                $orderArr['admin_comment'] = $bookingDetail['Booking']['admin_comment'];
            }


            $orderArr['date'] = "";
            $orderArr['time'] = "";
            if (!empty($bookingDetail['Booking']['reservation_date'])) {
                $dateTime = explode(" ", $bookingDetail['Booking']['reservation_date']);
                $dateResBooking=explode("-", $dateTime[0]);
                $finalResdate=$dateResBooking[2].'/'.$dateResBooking[1].'/'.$dateResBooking[0];
                $orderArr['date'] = $finalResdate;
                $orderArr['time'] = $dateTime[1];
            }


            $orderArr['placed_date'] = "";
            $orderArr['placed_date'] = "";
            if (!empty($bookingDetail['Booking']['created'])) {
                $placedDateTime = explode(" ", $bookingDetail['Booking']['created']);
                $dateBooking=explode("-", $placedDateTime[0]);
                $finaldate=$dateBooking[2].'/'.$dateBooking[1].'/'.$dateBooking[0];
                $orderArr['placed_date'] = $finaldate;
                $orderArr['placed_time'] = $placedDateTime[1];
            }

            $orderArr['booking_status']="";
            if (!empty($bookingDetail['Booking']['booking_status_id'])) {
                if($bookingDetail['Booking']['booking_status_id']==1){
                    $orderArr['booking_status']="Pending";
                }elseif($bookingDetail['Booking']['booking_status_id']==4){
                    $orderArr['booking_status']="Cancel";
                }elseif($bookingDetail['Booking']['booking_status_id']==5){
                    $orderArr['booking_status']="Booked";
                }
            }

            $orderArr['store_id'] = $bookingDetail['Booking']['store_id'];
            $orderArr['notification_type'] = "2";

            $orderNotification_type = "2";
            $message="New Order";
            if(!empty($orderArr)){
               $message=json_encode($orderArr);
            }
            $this->sendNotification($message,$store_id,$orderNotification_type);
        }
    }

    //Send Notification Code

    public function sendNotification($message= null,$store_id=null,$orderNotification_type){
        $this->UserDevice->bindModel(array(
            'belongsTo' => array(
                'NotificationConfiguration' => array(
                    'className' => 'NotificationConfiguration',
                    'foreignKey' => 'notification_configuration_id',
                    'type' => 'LEFT',
                    'conditions' => array('NotificationConfiguration.is_active' => 1, 'NotificationConfiguration.is_deleted' => 0),
                    'fields' => array('id', 'order_notification', 'show_in_notification', 'sound', 'badge_app', 'add_alarm', 'alarm_time_id')
                ),
            )
                ), FALSE);
        $this->UserDevice->bindModel(array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id',
                    'conditions' => array('User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => 3),
                    'fields' => array('id')
                ),
            )
                ), FALSE);
        $userDet = $this->UserDevice->find('all', array('recursive' => 1, 'conditions' => array('UserDevice.is_active' => 1, 'UserDevice.is_deleted' => 0, 'UserDevice.store_id' => $store_id)));

        $deviceTokenAndroidIds = array();
        $deviceTokenIosIds = array();
        $a = 0;
        $i = 0;
        $deviceNotificationArr=array();
        foreach ($userDet as $deviceNotification) {
            if (!empty($deviceNotification['User'])) {
                if (!empty($deviceNotification['NotificationConfiguration'])) {
                    if($orderNotification_type==1){
                        if ($deviceNotification['NotificationConfiguration']['order_notification'] == 1) {
                            if ($deviceNotification['UserDevice']['device_type'] == 'android') {
                                $deviceTokenAndroidIds[$a] = $deviceNotification['UserDevice']['device_token'];
                                $a++;
                            }
                            if ($deviceNotification['UserDevice']['device_type'] == 'ios') {
                                $deviceTokenIosIds[$i]['token'] = $deviceNotification['UserDevice']['device_token'];
                                $deviceTokenIosIds[$i]['notification']=$deviceNotification['NotificationConfiguration'];
                                $i++;
                            }
                        }
                    }
                    if($orderNotification_type==2){
                        if ($deviceNotification['NotificationConfiguration']['show_in_notification'] == 1) {
                            if ($deviceNotification['UserDevice']['device_type'] == 'android') {
                                $deviceTokenAndroidIds[$a] = $deviceNotification['UserDevice']['device_token'];
                                $a++;
                            }
                            if ($deviceNotification['UserDevice']['device_type'] == 'ios') {
                                $deviceTokenIosIds[$i]['token'] = $deviceNotification['UserDevice']['device_token'];
                                $deviceTokenIosIds[$i]['notification']=$deviceNotification['NotificationConfiguration'];
                                $i++;
                            }
                        }
                    }

                }
            }
        }


        $this->Webservice = $this->Components->load('Webservice');
        $date = $this->Webservice->getcurrentTime($store_id,2);
        $todaysPendingBookings = $this->Booking->find('count',array('fields'=>array('id'),'conditions'=>array('Booking.store_id'=>$store_id,'Booking.is_active'=>1,'DATE(Booking.reservation_date)'=>$date ,'Booking.booking_status_id'=>1,'Booking.is_deleted'=>0)));
        $todaysPendingOrder= $this->Order->find('count',array('fields'=>array('id'),'conditions'=>array('Order.is_future_order'=>0,'Order.store_id'=>$store_id,'Order.is_active'=>1,'DATE(Order.pickup_time)'=>$date ,'Order.order_status_id'=>1)));
        $totalBookingOrder=(int)$todaysPendingBookings+$todaysPendingOrder;

        if(!empty($deviceTokenAndroidIds)){
            $resultA=$this->hitCurl($deviceTokenAndroidIds,$message,$orderNotification_type); //Android
        }


        if(!empty($deviceTokenIosIds)){
            $resultI=$this->hitSocket($deviceTokenIosIds,$message,$orderNotification_type,$totalBookingOrder); //IOS
        }


    }

    // Anroid Notifications send by this Method

     function hitCurl($gcmDeciceTokenId,$message,$orderNotification_type){
        $tAlert =  (array)json_decode($message);
        if($orderNotification_type==2){
               $message = array('Reservation' =>$tAlert,'status'=>1,'message'=>'New booking has been placed.','notification_type'=>$orderNotification_type);
                $fields = array(
                    'registration_ids' => $gcmDeciceTokenId,
                    'data' => $message,
                );
            }else{
                $message = array('Order' =>$tAlert,'status'=>1,'message'=>'New order has been placed.','notification_type'=>$orderNotification_type);
                $fields = array(
                'registration_ids' => $gcmDeciceTokenId,
                'data' => $message,
                );
        }
        //$json=json_encode($fields);
        //print_r($json);
        //die;

        $headers = array(
            'Authorization:key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_ENCODING, '');
        //curl_setopt($ch, CURLOPT_NOBODY, true);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_URL, GCM_URL_ANDROID);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        //curl_setopt($ch, CURLOPT_HEADER, true); // header will be at output
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD'); // HTTP request is 'HEAD'
        //curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); // ADD THIS
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    // IOS Notifications send by this Method
    function hitSocket($tToken = null,$message=null,$deviceNotificationArr=array(),$totalBookingOrder=null){

        $tResult=array();
        $i=0;
        $tAlert=array();
        if(!empty($message)){
            $tAlert =  (array)json_decode($message);
        }else{
            $tAlert['notification_type']='';
        }
        $tHost = IOS_PUSH_HOST;
        $tPort = IOS_PUSH_PORT;

        //$tCert = WWW_ROOT.IOS_PUSH_CERTIFICATE;
        $tCert = IOS_PUSH_CERTIFICATE;
        //$tPassphrase = 'BcSdEveloPer2016';
        $tPassphrase = IOS_PASSPHASE;
        if(!empty($tToken)){
        foreach ($tToken as $deviceToken){
        // Create the message content that is to be sent to the device.
       // $tBadge = 8;
        $tSound = "default";
        $tBadge="0";
        $tBody = array();
        if(!empty($tAlert)){
            if($tAlert['notification_type']==2){
                 $tBody['Reservation']=$tAlert;
                 $msg='booking';
            }else{
                $tBody['Order']=$tAlert;
                $msg='order';
            }
        }
        if(($deviceToken['notification']['sound']==0) && ($deviceToken['notification']['badge_app']==0) ){
            $tBadge=0;
            $tBody['aps'] = array ('alert'=>"New ".$msg." has been placed.",'badge' => $tBadge);
        }elseif(($deviceToken['notification']['sound']==0) && ($deviceToken['notification']['badge_app']==1) ){
            if(!empty($totalBookingOrder)){
                $tBadge=$totalBookingOrder;
            }else{
               $tBadge=1;
            }

            $tBody['aps'] = array ('alert'=>"New ".$msg." has been placed.",'badge' => $tBadge);
        }elseif(($deviceToken['notification']['sound']==1) && ($deviceToken['notification']['badge_app']==0) ){
            $tBadge=0;
            $tBody['aps'] = array ('alert'=>"New ".$msg." has been placed.",'badge' => $tBadge,'sound' => $tSound);
        }elseif(($deviceToken['notification']['sound']==1) && ($deviceToken['notification']['badge_app']==1) ){
            if(!empty($totalBookingOrder)){
                $tBadge=$totalBookingOrder;
            }else{
               $tBadge=1;
            }
            $tBody['aps'] = array ('alert'=>"New ".$msg." has been placed.",'badge' => $tBadge,'sound' => $tSound);
        }
        $tBody = json_encode ($tBody);
        $tContext = stream_context_create ();
        stream_context_set_option ($tContext, 'ssl', 'local_cert', $tCert);
        // Remove this line if you would like to enter the Private Key Passphrase manually.
        stream_context_set_option ($tContext, 'ssl', 'passphrase', $tPassphrase);

        // Open the Connection to the APNS Server.
       // $tSocket = stream_socket_client ('ssl://'.$tHost.':'.$tPort, $error, $errstr, 30, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $tContext);
         $tSocket = stream_socket_client('ssl://' . $tHost . ':' . $tPort, $error, $errorString, 30, STREAM_CLIENT_CONNECT, $tContext);

        // Check if we were able to open a socket.
        if (!$tSocket){
            continue;
        }
            //exit ("APNS Connection Failed: $error $errorString" . PHP_EOL);
            // Build the Binary Notification.
            //$tMsg = chr (0) . chr (0) . chr (32) . pack ('H*', trim($deviceToken['token'])) . pack ('n', strlen ($tBody)) . $tBody;
            if(empty($deviceToken['token'])){
                continue;
            }
            if($deviceToken['token']=='SimulatorToken-668678778'){
                continue;
            }

            $tMsg = chr (0) . chr (0) . chr (32) . pack ('H*', trim($deviceToken['token'])) . pack ('n', strlen ($tBody)) . $tBody;
            $tResult[$i]=fwrite ($tSocket, $tMsg, strlen ($tMsg));
            $i++;
           }
            // Send the Notification to the Server.
            fclose ($tSocket);
            return $tResult;
        }

    }

      /*     * ******************************************************************************************
      @Function Name : logOut
      @Description   : this function is used to remove the device from UserDevice table.
      @Author        : SmartData
      created:23/12/2016
     * ****************************************************************************************** */

    public function logOut() {

        configure::Write('debug', 0);
        $headers = $headers = array();
         foreach ($_SERVER as $name => $value) {
             if (substr($name, 0, 5) == 'HTTP_') {
                 $headers[strtolower(str_replace('-', '_', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr(($name), 5)))))))] = $value;
             }
         }
        $requestBody = file_get_contents('php://input');
        //$requestBody = '{"device_token":"e7897498238jhdkajkjkdas","store_id":"108"}';
        //$headers['user_id'] = 'NzU';
        //$headers['merchant_id'] = 85;
        $this->Webservice->webserviceAdminLog($requestBody, "logout.txt", $headers);
        $requestBody = json_decode($requestBody, true);
        $responsedata = array();

        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
            $merchant_id = $headers['merchant_id'];
            $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('id')));
            if (!empty($merchantResult)) {
                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
                    $user_id = $this->Encryption->decode($headers['user_id']);
                    $roleid = 3;
                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.role_id' => $roleid), 'fields' => array('User.id')));
                    if (!empty($userDet)) {
                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
                            $store_id = $requestBody['store_id'];
                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
                            if (!empty($storeResult)) {
                                $device_token = trim($requestBody['device_token']);
                                
                                $result = $this->UserDevice->updateAll(array('UserDevice.is_deleted' => 1), array('UserDevice.device_token' => $device_token,'UserDevice.user_id' => $user_id,'UserDevice.merchant_id' => $merchant_id,'UserDevice.store_id' => $store_id));
                                if ($result) {
                                    $responsedata['message'] = "You have been successfully logged out!.";
                                    $responsedata['response'] = 1;
                                    return json_encode($responsedata);
                                } else {
                                    $responsedata['message'] = "You could not be logged out, please try again.";
                                    $responsedata['response'] = 0;
                                    return json_encode($responsedata);
                                }
                            } else {
                                $responsedata['message'] = "Store not found.";
                                $responsedata['response'] = 0;
                                return json_encode($responsedata);
                            }
                        } else {
                            $responsedata['message'] = "Please select a store.";
                            $responsedata['response'] = 0;
                            return json_encode($responsedata);
                        }
                    } else {
                        $responsedata['message'] = "You are not register under this merchant.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                    }
                } else {
                    $responsedata['message'] = "Please login.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                }
            } else {
                $responsedata['message'] = "Merchant not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
            }
        } else {
            $responsedata['message'] = "Please select a merchant.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
        }
    }
}

