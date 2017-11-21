<?php

App::uses("Sanitize", "Utility");
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('ServicesAppController', 'Controller');

class AppServicesController
        extends ServicesAppController {

  public $name = 'AppServices';
  public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Paginator', 'Common', 'Dateform', 'Webservice', 'Webservicetest');
  public $helper = array('Encryption', 'Paginator', 'Form', 'DateformHelper', 'Common');
  public $uses = array('User', 'StoreGallery', 'Store', 'StoreBreak', 'StoreAvailability', 'StoreHoliday', 'Category', 'Tab', 'Permission', 'StoreTheme', 'Merchant', 'StoreTax', 'StoreFont', 'Booking', 'CountryCode', 'Type', 'ItemType', 'ItemPrice', 'Topping', 'Item', 'SubPreference', 'Size', 'StoreTax', 'Category', 'AddonSize', 'Offer', 'OfferDetail', 'SubPreferencePrice', 'ToppingPrice', 'DeliveryAddress', 'IntervalPrice', 'Interval', 'UserCoupon', 'Coupon', 'ItemDefaultTopping', 'ItemOffer', 'OrderItem', 'OrderPreference', 'orderSave', 'OrderTopping', 'OrderOffer', 'OrderItem', 'Favorite', 'Order', 'UserCoupon', 'Coupon', 'StoreReview', 'OrderPayment', 'OrderTopping', 'OrderPreference', 'MobileOrder', 'StoreReviewImage', 'TimeZone', 'OrderItemFree', 'UserToken');

  public function beforeFilter() {
    parent::beforeFilter();
    $this->layout = null;
    $this->autoRender = false;
    $this->Auth->allow('locStoreList', 'login', 'forgotPassword',
            'reservationPostService', 'merchantTemplates', 'countryCodeList',
            'registration', 'menuItems', 'getStoreMenu', 'guestLogin',
            'coupons', 'applyCoupon', 'deals', 'myDeliveryAddress',
            'addAddress', 'updateAddress', 'deleteAddress', 'allReviews',
            'myProfile', 'checkItemOffer', 'checkOut', 'checkOutDet',
            'checkExtendedOffer', 'myFavoritesList', 'RemoveFavorites',
            'proceedOrder', 'myOrdersList', 'myCouponsList', 'addToFavorite',
            'addRating', 'RemoveCoupon', 'myBooking', 'RemoveBooking',
            'mySavedOrders', 'removeMySavedOrder', 'storeTime', 'reorder',
            'myReviews', 'RemoveReview', 'store', 'getProfileInfo',
            'verifyAddress', 'searchLocation');
    $target_dir = WWW_ROOT . "/webserviceLog";
    if (!file_exists($target_dir)) {
      (new Folder($target_dir, true, 0777));
    }
  }

  /*   * *
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

    return json_encode($ResponseArr,
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }
  
    /*   * ******************************************************************************************
    @Method         : GET
    @Function Name  : GET header value
    @Description    : this function is used get header value
    @Author         : SmartData
    @created        : 10/09/2017
   * ****************************************************************************************** */
  
  public function getheader(){
    $headers=array();
    $headers=$this->getheaderLocal();   // This funtion is activated when we work on local Machine or with Apache server
    //$headers=$this->getheaderServer(); // This funtion is activated when we work on Test Server or with Nginx server
      return $headers;
  }
  

  /*   * ******************************************************************************************
    @Function Name : locStoreList
    @Description   : this function is used for get location Store List based on merchant ID
    @Author        : SmartData
    created:23/08/2016
   * ****************************************************************************************** */

  public function locStoreList() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "store_loc.txt", $headers);
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        /* Authenticating the login time If the last request is more than 30 minutes*/
    if(empty($headers['authtoken'])){
      $responsedata['message'] = "Please login again.";
      $responsedata['response'] = 401;
      return json_encode($responsedata);
    }else{
      $loginCheck=$this->checkLoggedIn($headers['authtoken']);
        if($loginCheck['response']=='401'){                    
                return json_encode($loginCheck);
        }
    }
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        $this->Store->unBindModel(array('belongsTo' => array('StoreTheme')),
                false);
        $this->Store->unBindModel(array('belongsTo' => array('StoreFont')),
                false);
        $this->Store->unBindModel(array('hasOne' => array('SocialMedia')), false);
        $this->Store->unBindModel(array('hasMany' => array('StoreContent')),
                false);
        $this->Store->unBindModel(array('hasMany' => array('StoreGallery')),
                false);

        $this->Store->bindModel(array(
            'hasMany' => array(
                'StoreAvailability' => array('fields' => array('id', 'day_name', 'store_id', 'start_time', 'end_time', 'is_closed'), 'conditions' => array('StoreAvailability.is_active' => 1, 'StoreAvailability.is_deleted' => 0)),
            )), false);
        $locStoreList = $this->Store->find('all',
                array('conditions' => array('Store.is_active' => 1, 'Store.is_deleted' => 0, 'Store.merchant_id' => $merchant_id), 'fields' => array('id', 'store_name', 'address', 'city', 'state', 'store_logo', 'phone', 'zipcode', 'merchant_id', 'delivery_fee', 'service_fee', 'minimum_order_price', 'minimum_takeaway_price', 'is_booking_open', 'is_delivery', 'cash_on_delivery', 'is_pay_by_credit_card', 'is_express_check_out', 'deliverycalendar_limit', 'pickcalendar_limit', 'deliveryblackout_limit', 'pickblackout_limit', 'dineinblackout_limit', 'pre_order_allowed', 'calendar_limit', 'dineinblackout_limit', 'is_take_away', 'is_booking_open')));

        $listing = array();

        foreach ($locStoreList as $k => $locStoreDet) {
          if ($locStoreDet['Store']['is_booking_open'] == 1) {
            $locStoreDet['Store']['is_booking_open'] = true;
          } else {
            $locStoreDet['Store']['is_booking_open'] = false;
          }

          if ($locStoreDet['Store']['is_delivery'] == 1) {
            $locStoreDet['Store']['is_delivery'] = true;
          } else {
            $locStoreDet['Store']['is_delivery'] = false;
          }

          if ($locStoreDet['Store']['cash_on_delivery'] == 1) {
            $locStoreDet['Store']['cash_on_delivery'] = true;
          } else {
            $locStoreDet['Store']['cash_on_delivery'] = false;
          }

          if ($locStoreDet['Store']['is_pay_by_credit_card'] == 1) {
            $locStoreDet['Store']['is_pay_by_credit_card'] = true;
          } else {
            $locStoreDet['Store']['is_pay_by_credit_card'] = false;
          }

          if ($locStoreDet['Store']['is_express_check_out'] == 1) {
            $locStoreDet['Store']['is_express_check_out'] = true;
          } else {
            $locStoreDet['Store']['is_express_check_out'] = false;
          }

          if ($locStoreDet['Store']['is_take_away'] == 1) {
            $locStoreDet['Store']['is_take_away'] = true;
          } else {
            $locStoreDet['Store']['is_take_away'] = false;
          }

          if ($locStoreDet['Store']['pre_order_allowed'] == 1) {
            $locStoreDet['Store']['pre_order_allowed'] = true;
          } else {
            $locStoreDet['Store']['pre_order_allowed'] = false;
          }
          //Current Date for all three delivery, Reception and Pickup
          $this->Session->write('store_id', $locStoreDet['Store']['id']);
          $this->Session->write('merchant_id', $merchant_id);
          $merchant['store_id'] = $locStoreDet['Store']['id'];
          $merchant['merchant_id'] = $merchant_id;
          $current_date = $this->Webservice->getcurrentTime($locStoreDet['Store']['id'],
                  2);
          $today = 1;
          $finaldata = array();
          // for delivery
          $orderType = 3;
          $finaldata = $this->Common->getNextDayTimeRange($current_date, $today,
                  $orderType, $merchant);
          $Del_date_current = $finaldata['currentdate'];
          $current_Del_Date_Var = $Del_date_current;
          if ($locStoreDet['Store']['deliveryblackout_limit'] > 0) {
            $current_Del_Date_Var = date('Y-m-d',
                    strtotime($Del_date_current . ' +' . $locStoreDet['Store']['deliveryblackout_limit'] . ' day'));
          }
          $locStoreDet['Store']['delivery_current_date'] = $current_Del_Date_Var;

          //For Pick Up

          $orderType = 2;
          $finaldata = $this->Common->getNextDayTimeRange($current_date, $today,
                  $orderType, $merchant);
          $pickup_date_current = $finaldata['currentdate'];
          $current_pickup_Date_Var = $pickup_date_current;
          if ($locStoreDet['Store']['pickblackout_limit'] > 0) {
            $current_pickup_Date_Var = date('Y-m-d',
                    strtotime($pickup_date_current . ' +' . $locStoreDet['Store']['pickblackout_limit'] . ' day'));
          }
          $locStoreDet['Store']['pickup_current_date'] = $current_pickup_Date_Var;

          //For Reservation Up
          $orderType = 1;
          $finaldata = $this->Common->getNextDayTimeRange($current_date, $today,
                  $orderType, $merchant);
          $reser_date_current = $finaldata['currentdate'];
          $current_Reser_Date_Var = $reser_date_current;
          if ($locStoreDet['Store']['dineinblackout_limit'] > 0) {
            $current_Reser_Date_Var = date('Y-m-d',
                    strtotime($reser_date_current . ' +' . $locStoreDet['Store']['dineinblackout_limit'] . ' day'));
          }
          $locStoreDet['Store']['reservation_current_date'] = $current_Reser_Date_Var;

          foreach ($locStoreDet['StoreAvailability'] as $storeAvailability) {
            $today = $this->Webservice->getcurrentTime($storeAvailability['store_id'],
                    1);
            // $today;
            $dateTime = date("l", strtotime($today)) . "\n";
            if ($storeAvailability["day_name"] == trim($dateTime)) {
              $locStoreDet['Store']['start_time'] = $storeAvailability['start_time'];
              $locStoreDet['Store']['end_time'] = $storeAvailability['end_time'];
              if ($storeAvailability['is_closed'] == 1) {
                $locStoreDet['Store']['is_closed'] = true;
              } else {
                $locStoreDet['Store']['is_closed'] = false;
              }
            }
            $locStoreList[$k] = $locStoreDet;
          }
          unset($locStoreList[$k]['StoreAvailability']);

          if (!empty($locStoreDet['Store']['store_logo'])) {
            $protocol = 'http://';
            if (isset($_SERVER['HTTPS'])) {
              if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                $protocol = 'https://';
              }
            }
            $locStoreList[$k]['Store']['store_logo'] = $protocol . $_SERVER['HTTP_HOST'] . "/storeLogo/" . $locStoreDet['Store']['store_logo'];
          } else {
            $locStoreList[$k]['Store']['store_logo'] = " ";
          }
          $this->Session->destroy();
        }

        foreach ($locStoreList as $listdata) {
          $listing[] = $listdata['Store'];
        }
        $countryCode = $this->CountryCode->find('list',
                array('fields' => array('id', 'code'), 'order' => array('CountryCode.id ASC')));
        if (!empty($countryCode)) {
          foreach ($countryCode as $k => $countrylist) {
            $responsedata['countyCode'][$k] = $countrylist;
          }
        } else {
          $listing['countyCode'][$k] = array();
        }
        $responsedata['countyCode'] = array_values($responsedata['countyCode']);
        header('merchant_id:' . $merchant_id);
        if (!empty($listing)) {
          $responsedata['distance'] = array('10', '20', '50', '100', '500');
          $responsedata['aboutUs'] = $protocol . $_SERVER['HTTP_HOST'];
          $responsedata['message'] = "Success";
          $responsedata['response'] = 1;
          $responsedata['data'] = $listing;
          $responsedata['countyCode'] = array_values($responsedata['countyCode']);
          //pr($responsedata);
          //    die;
          return json_encode($responsedata);
          //return $this->json_message(1, 'Success', $listing);
        } else {
          return $this->json_message(404, 'Store not found.');
        }
      } else {
        return $this->json_message(404, 'Merchant not found.');
      }
    } else {
      return $this->json_message(404, 'Please select a merchant.');
    }
  }

  /*   * ******************************************************************************************
    @Function Name : reservationPostService
    @Description   : this function is used for Reservation based on merchant ID
    @Author        : SmartData
    created:23/08/2016
   * ****************************************************************************************** */

  public function reservationPostService() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    //$requestBody = '{"number_person": 2,"store_id":108,"reservation_date":"2017-01-04 21:35:00","special_request":"Table of 8 people"}';
    $this->Webservice->webserviceLog($requestBody, "dine_request.txt", $headers);
    $responsedata = array();
    $requestBody = json_decode($requestBody, true);
    //$headers['user_id'] = "NTc2";
    //$headers['merchant_id'] = 85;
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('POST')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        /* Authenticating the login time If the last request is more than 30 minutes*/
    if(empty($headers['authtoken'])){
      $responsedata['message'] = "Please login again.";
      $responsedata['response'] = 401;
      return json_encode($responsedata);
    }else{
      $loginCheck=$this->checkLoggedIn($headers['authtoken']);
        if($loginCheck['response']=='401'){                    
                return json_encode($loginCheck);
        }
    }
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {
        $requestBody['merchant_id'] = $merchant_id;
        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
          $store_id = $requestBody['store_id'];
          // Checking Store id parameter is Integer
                $argStore = $this->checkParameter($store_id, 'integer', 'Store'); 
                    if ($argStore['response'] == '403') {
                      return json_encode($argStore);
                    }
          if (isset($headers['user_id']) && !empty($headers['user_id'])) {
           
//                        $store = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0)));
            $store = $this->Store->find('first',
                    array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.notification_type', 'Store.notification_email', 'Store.email_id', 'Store.store_name', 'Store.address', 'Store.city', 'Store.state', 'Store.zipcode', 'Store.phone', 'Store.notification_number', 'is_booking_open', 'calendar_limit', 'dineinblackout_limit', 'twilio_api_key', 'twilio_api_token', 'twilio_number', 'store_url')));

            if (!empty($store)) {
              if ($store['Store']['is_booking_open'] == 0) {
                $responsedata['message'] = "Store doesn't provide reservation.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
              }
              header('merchant_id:' . $merchant_id);
              header('store_id:' . $requestBody['store_id']);
              header('token:' . $headers['user_id']);
              $requestBody['user_id'] = $this->Encryption->decode($headers['user_id']);
              $roleid = array(4, 5);
              $userDet = $this->User->find('first',
                      array('conditions' => array('User.id' => $requestBody['user_id'], 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted', 'User.fname', 'User.lname', 'User.email', 'User.phone')));
              if (!empty($userDet)) {
                if (!empty($requestBody)) {
                  if (!empty($requestBody['reservation_date'])) {
                    // Checking Date parameter is Date
                        $argDate = $this->checkParameter($requestBody['reservation_date'], 'dateTime','Reservation date and time'); 
                         if ($argDate['response'] == '403') {
                           return json_encode($argDate);
                         }
                    $dateTime = explode(' ', $requestBody['reservation_date']);
                    $unixTime = strtotime($dateTime[1]);
                    $unixDayTime = strtotime("24:00");
                    if ($unixTime > $unixDayTime) {
                      $responsedata['message'] = "Please select time again.";
                      $responsedata['response'] = 0;
                      return json_encode($responsedata);
                    }
                    $current_date = $this->Webservice->getcurrentTime($store['Store']['id'],
                            1);
                    if (!empty($requestBody['number_person'])) {
                      // Checking Store id parameter is Integer
                      $argNumberPerson = $this->checkParameter($requestBody['number_person'], 'integer', 'Number of Person'); 
                          if ($argNumberPerson['response'] == '403') {
                            return json_encode($argNumberPerson);
                          }
                      $requestBody['number_person'] = $requestBody['number_person'];
                    } else {
                      $requestBody['number_person'] = 1;
                    }
                    if (!empty($requestBody['special_request'])) {
                      // Checking Store id parameter is Integer
                      $argSpecialRequest = $this->checkParameter($requestBody['special_request'], 'string', 'Special Request'); 
                          if ($argSpecialRequest['response'] == '403') {
                            return json_encode($argSpecialRequest);
                          }
                      $requestBody['number_person'] = $requestBody['number_person'];
                    }
                    
                    $save_result = $this->Booking->save($requestBody);
                    if ($save_result) {
                      $template_type = 'customer_dine_in_request';
                      $this->loadModel('DefaultTemplate');
                      $fullName = "Admin";
                      $number_person = $requestBody['number_person']; //no of person
                      $start_date = $dateTime[0];
                      $start_time = date('H:i a', strtotime($dateTime[1]));
                      $customer_name = $userDet['User']['fname'] . " " . $userDet['User']['lname'];
                      if ($requestBody['special_request']) {
                        $special_request = $requestBody['special_request'];
                      } else {
                        $special_request = "N/A";
                      }

                      $emailSuccess = $this->DefaultTemplate->find('first',
                              array('conditions' => array('DefaultTemplate.template_code' => $template_type, 'DefaultTemplate.is_default' => 1), 'fields' => array('DefaultTemplate.id', 'DefaultTemplate.template_message', 'DefaultTemplate.template_subject', 'sms_template')));

                      if ($emailSuccess) {
                        if (($store['Store']['notification_type'] == 1 || $store['Store']['notification_type'] == 3) && (!empty($store['Store']['notification_email']))) {
                          $storeEmail = $store['Store']['notification_email'];
                        } else {
                          $storeEmail = $store['Store']['email_id'];
                        }
                        $contactPerson = $userDet['User']['fname'] . " " . $userDet['User']['lname'] . " " . $userDet['User']['phone'];
                        $customerEmail = $userDet['User']['email'];
                        $emailData = $emailSuccess['DefaultTemplate']['template_message'];
                        $emailData = str_replace('{FULL_NAME}', $fullName,
                                $emailData);
                        $emailData = str_replace('{BOOKING_DATE}', $start_date,
                                $emailData);
                        $emailData = str_replace('{BOOKING_TIME}', $start_time,
                                $emailData);
                        $emailData = str_replace('{NO_PERSON}', $number_person,
                                $emailData);
                        $emailData = str_replace('{SPECIAL_REQUEST}',
                                $special_request, $emailData);
                        $emailData = str_replace('{CUSTOMER_NAME}',
                                $customer_name, $emailData);
                        $emailData = str_replace('{CONTACT_PERSON}',
                                $contactPerson, $emailData);
                        $subject = ucwords(str_replace('_', ' ',
                                        $emailSuccess['DefaultTemplate']['template_subject']));

                        $emailData = str_replace('{STORE_NAME}',
                                $store['Store']['store_name'], $emailData);
                        $storeAddress = $store['Store']['address'] . "<br>" . $store['Store']['city'] . ", " . $store['Store']['state'] . " " . $store['Store']['zipcode'];

                        $storePhone = $store['Store']['phone'];
                        $url = "http://" . $store['Store']['store_url'];
                        $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $store['Store']['store_url'] . "</a>";
                        $emailData = str_replace('{STORE_URL}', $storeUrl,
                                $emailData);
                        $emailData = str_replace('{STORE_ADDRESS}',
                                $storeAddress, $emailData);
                        $emailData = str_replace('{STORE_PHONE}', $storePhone,
                                $emailData);

                        $this->Email->to = $storeEmail;
                        $this->Email->subject = $subject;
                        //$this->Email->from = $customerEmail;
                        $this->Email->from = $this->front_email;
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

                        if (!empty($store['Store']['twilio_api_key']) && !empty($store['Store']['twilio_api_token']) && !empty($store['Store']['twilio_number'])) {
                          if (!empty($emailSuccess['DefaultTemplate']['sms_template'])) {
                            if (($store['Store']['notification_type'] == 2 || $store['Store']['notification_type'] == 3) && (!empty($store['Store']['notification_number']))) {
                              $mobnumber = '+1' . str_replace(array('(', ')', ' ', '-'),
                                              '',
                                              $store['Store']['notification_number']);
                            } else {
                              $mobnumber = '+1' . str_replace(array('(', ')', ' ', '-'),
                                              '', $store['Store']['phone']);
                            }
                            $smsData = $emailSuccess['DefaultTemplate']['sms_template'];
                            $smsData = str_replace('{FULL_NAME}', $fullName,
                                    $smsData);
                            $smsData = str_replace('{BOOKING_DATE}',
                                    $start_date, $smsData);
                            $smsData = str_replace('{BOOKING_TIME}',
                                    $start_time, $smsData);
                            $smsData = str_replace('{NO_PERSON}',
                                    $number_person, $smsData);
                            $smsData = str_replace('{SPECIAL_REQUEST}',
                                    $special_request, $smsData);
                            $smsData = str_replace('{CONTACT_PERSON}',
                                    $contactPerson, $smsData);
                            $smsData = str_replace('{STORE_NAME}',
                                    $store['Store']['store_name'], $smsData);
                            $smsData = str_replace('{STORE_PHONE}', $mobnumber,
                                    $smsData);
                            $message = $smsData;
                            $this->Webservice->sendSmsNotificationFront($mobnumber,
                                    $message, $requestBody['store_id']);
                          }
                        }
                      }

                      $responsedata['message'] = "Your request has been submitted, You will receive a confirmation email shortly. Thank you!";
                      $responsedata['response'] = 1;
                      return json_encode($responsedata);
                    } else {
                      $responsedata['message'] = "Reservation request could not be submitted, Please try again.";
                      $responsedata['response'] = 0;
                      return json_encode($responsedata);
                    }
                  } else {
                    $responsedata['message'] = "Please select date and time.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                  }
                } else {
                  $responsedata['message'] = "Please select number of person and reservation date.";
                  $responsedata['response'] = 0;
                  return json_encode($responsedata);
                  return $this->json_message(401, '');
                }
              } else {
                $responsedata['message'] = "You are not registered under this merchant.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
              }
            } else {
              $responsedata['message'] = "Store not found.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            }
          } else {
            $responsedata['message'] = "Please login first to access this feature.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
            return $this->json_message(401, '');
          }
        } else {
          $responsedata['message'] = "Please select a store.";
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

  /*   * ******************************************************************************************
    @Function Name : login
    @Description   : this function is used for login based on merchant ID
    @Author        : SmartData
    created:23/08/2016
   * ****************************************************************************************** */

  public function login() {

    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "login_request.txt", $headers);
    //$requestBody = '{"email":"rjsaini@mailinator.com","password": "Smartdata123","device_token":"12365478"}';
    //$requestBody = '{"email":"maheshku@mailinator.com","password":"Smartdata123","device_token":"12365479"}';
    $responsedata = array();
    $requestBody = json_decode($requestBody, true);
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('POST')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {
        if (isset($requestBody['email']) && !empty($requestBody['email']) && isset($requestBody['password']) && !empty($requestBody['password'])) {
          // Checking Email parameter is String
	         $argEmail=$this->checkParameter($requestBody['email'],'string','Email'); 
                  if($argEmail['response']=='403'){                    
                    return json_encode($argEmail);                    
                  }
          // Checking Password parameter is String
	         $argPassword=$this->checkParameter($requestBody['password'],'string','Password'); 
                  if($argPassword['response']=='403'){                    
                    return json_encode($argPassword);                    
                  }
                  
          $password = AuthComponent::password($requestBody['password']);
          $user = $this->User->find("first", array("conditions" => array("User.email" => $requestBody['email'], "User.password" => $password, "User.role_id" => array(4, 5), "User.merchant_id" => $merchant_id), 'fields' => array('email', 'fname', 'id', 'lname', 'phone', 'store_id', 'dateOfBirth', 'country_code_id', 'is_deleted', 'is_active', 'is_newsletter', 'is_smsnotification', 'is_emailnotification')));
          if (!empty($user)) {
            if ($user['User']['is_active'] == 1) {
              if ($user['User']['is_deleted'] == 0) {
                if ($this->Auth->login($user['User'])) {
                  $responsedata['message'] = "Success";
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
                    $responsedata['dateOfBirth'] = " ";
                  }
                  if (!empty($user['User']['country_code_id'])) {
                    $responsedata['country_code_id'] = $user['User']['country_code_id'];
                  } else {
                    $responsedata['country_code_id'] = " ";
                  }

                  if ($user['User']['is_newsletter']) {
                    $responsedata['is_newsletter'] = true;
                  } else {
                    $responsedata['is_newsletter'] = false;
                  }

                  if ($user['User']['is_smsnotification']) {
                    $responsedata['is_smsnotification'] = true;
                  } else {
                    $responsedata['is_smsnotification'] = false;
                  }

                  if ($user['User']['is_emailnotification']) {
                    $responsedata['is_emailnotification'] = true;
                  } else {
                    $responsedata['is_emailnotification'] = false;
                  }
                  
                  $authToken = md5(microtime().rand());
                  $authTime=time();
                  $UserDeviceDet['auth_token'] = $authToken;
                  $UserDeviceDet['auth_time'] = $authTime;
                  if (isset($requestBody['device_token']) && !empty($requestBody['device_token'])) {
                    $UserDeviceDet['device_token'] = $requestBody['device_token'];
                  } else {
                    $UserDeviceDet['device_token'] = "";
                  }
                   $userTokenDet = $this->UserToken->find('first', array('conditions' => array("UserToken.device_token" => $UserDeviceDet['device_token'], "UserToken.is_active" => 1, "UserToken.is_deleted" => 0)));
                  if(!empty($userTokenDet)){
                    $UserDeviceDet['id']=$userTokenDet['UserToken']['id'];
                    $result = $this->UserToken->save($UserDeviceDet);
                  }

                  if (empty($userTokenDet)) {
                    $result = $this->UserToken->save($UserDeviceDet);
                  }  

                  $EncryptUserID = $this->Encryption->encode($responsedata['id']);
                  $EncryptmerchantID = $this->Encryption->encode($merchant_id);
                  $this->tokenGenerate($EncryptUserID, $EncryptmerchantID);
                  $responsedata['token'] = $EncryptUserID;
                  $responsedata['AuthToken'] = $authToken;
                  $countryCode = $this->CountryCode->find('first',
                          array('conditions' => array('CountryCode.id' => $user['User']['country_code_id'])));
                  if (!empty($countryCode)) {
                    $responsedata['country_code_id'] = $countryCode['CountryCode']['code'];
                  } else {
                    $responsedata['country_code_id'] = "+1";
                  }
                  return json_encode($responsedata);
                } else {
                  $responsedata['message'] = "Invalid email or password.";
                  $responsedata['response'] = 0;
                  return json_encode($responsedata);
                }
              } else {
                $responsedata['message'] = "Your account is disabled, kindly contact store admin.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
              }
            } else {
              $responsedata['message'] = "No active user found with this email/password.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            }
          } else {
            $responsedata['message'] = "Incorrect email or password";
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

  /*   * ******************************************************************************************
    @Function Name : forgotPassword
    @Description   : this function is used for forgot Password based on merchant ID
    @Author        : SmartData
    created:23/08/2016
   * ****************************************************************************************** */

  public function forgotPassword() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "forgot_pasword.txt",
            $headers);
    //$requestBody = '{"email": "rjsaini@mailinator.com"}';
    $responsedata = array();
    $requestBody = json_decode($requestBody, true);
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('POST')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
    
        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantResult = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id', 'domain_name', 'name', 'address', 'city', 'state', 'zipcode', 'phone', 'email')));
      if (!empty($merchantResult)) {
        if ($requestBody['email'] != '') {
          // Checking Password parameter is String
	         $argEmail=$this->checkParameter($requestBody['email'],'string','Email'); 
                  if($argEmail['response']=='403'){                    
                    return json_encode($argEmail);                    
                  }
          $userEmail = $this->User->find('first',
                  array('conditions' => array('User.email' => $requestBody['email'], 'User.role_id' => array(4, 5), 'User.merchant_id' => $merchant_id), 'fields' => array('id', 'fname', 'lname', 'email')));
          if (!empty($userEmail)) {
            $this->loadModel('EmailTemplate');
            $template_type = 'merchant_customer_forget_password';
            $domain = $merchantResult['Merchant']['domain_name'];
            $WerserviceTemplate = $this->EmailTemplate->find('first',
                    array('conditions' => array('EmailTemplate.merchant_id' => $merchant_id, 'EmailTemplate.is_deleted' => 0, 'EmailTemplate.template_code' => $template_type)));
            //== new email code starts here ===========================
            if ($WerserviceTemplate) {
              if ($userEmail['User']['lname']) {
                $fullName = $userEmail['User']['fname'] . " " . $userEmail['User']['lname'];
              } else {
                $fullName = $userEmail['User']['fname'];
              }
              $token = time() . rand();
              $userName = $userEmail['User']['email'];
              $emailData = $WerserviceTemplate['EmailTemplate']['template_message'];
              $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
              $url = $domain . '/hqusers/resetPassword/' . $token;
              $activationLink = '<a href="' . $url . '">' . 'Click here to reset your password' . '</a>';
              $emailData = str_replace('{ACTIVE_LINK}', $activationLink,
                      $emailData);
              $subject = ucwords(str_replace('_', ' ',
                              $WerserviceTemplate['EmailTemplate']['template_subject']));
              $url = "http://" . $merchantResult['Merchant']['domain_name'];
              $merchantUrl = "<a href=" . $url . " target='_blank'>" . "www." . $merchantResult['Merchant']['domain_name'] . "</a>";
              $emailData = str_replace('{MERCHANT_URL}', $merchantUrl,
                      $emailData);
              $emailData = str_replace('{MERCHANT_COMPANY_NAME}',
                      $merchantResult['Merchant']['name'], $emailData);
              $merchantAddress = $merchantResult['Merchant']['address'] . "<br>" . $merchantResult['Merchant']['city'] . ", " . $merchantResult['Merchant']['state'] . " " . $merchantResult['Merchant']['zipcode'];
              $merchantPhone = $merchantResult['Merchant']['phone'];
              $emailData = str_replace('{MERCHANT_ADDRESS}', $merchantAddress,
                      $emailData);
              $emailData = str_replace('{MERCHANT_PHONE}', $merchantPhone,
                      $emailData);

              $this->Email->to = $requestBody['email'];
              $this->Email->subject = $subject;
              $this->Email->from = $merchantResult['Merchant']['email'];
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
                  $userDetail['User']['id'] = $userEmail['User']['id'];
                  $userDetail['User']['forgot_token'] = $token;
                  $this->User->save($userDetail['User']);
                  $responsedata['response'] = 1;
                  $responsedata['message'] = "Email sent, please check your email account.";
                  return json_encode($responsedata);
                }
              } catch (Exception $e) {
                $responsedata['response'] = 0;
                $responsedata['message'] = "Unable to process your request, Please try after some time.";
                return json_encode($responsedata);
              }
            } else {
              $responsedata['response'] = 0;
              $responsedata['message'] = "Template not found";
              return json_encode($responsedata);
            }
          } else {
            $responsedata['response'] = 0;
            $responsedata['message'] = "No active user found with this email.";
            return json_encode($responsedata);
          }
        } else {
          $responsedata['response'] = 0;
          $responsedata['message'] = "Please enter email.";
          return json_encode($responsedata);
        }
      } else {
        $responsedata['message'] = "Merchant not found.";
        $responsedata['response'] = 0;
        return json_encode($responsedata);
      }
    } else {
      $responsedata['response'] = 0;
      $responsedata['message'] = "Please select a merchant.";
      return json_encode($responsedata);
    }
  }

  /*   * ******************************************************************************************
    @Function Name : countryCodeList
    @Description   : this function is used for Country Code List based on merchant ID
    @Author        : SmartData
    created:23/08/2016
   * ****************************************************************************************** */

  public function countryCodeList() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $responsedata = array();
    $requestBody = json_decode($requestBody, true);
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {
        $countryCode = $this->CountryCode->find('list',
                array('fields' => array('id', 'code'), 'order' => array('CountryCode.id ASC')));
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
          $responsedata['message'] = "County not found.";
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

  /*   * ******************************************************************************************
    @Function Name : registration
    @Description   : this function is used for Registration based on merchant ID
    @Author        : SmartData
    created:23/08/2016
   * ****************************************************************************************** */

  public function registration() {

    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "sign_up.txt", $headers);
    //$requestBody = '{"fname": "vikasg","lname": "gupta","email": "vikasgupta@mailinator.com","password": "Smartdata123","phone": "(232) 131-2312","is_privacypolicy": "1","country_code_id":"+1","dateOfBirth":"1988-08-15"}';
    $responsedata = array();
    //$headers['merchant_id']=85;
    $requestBody = json_decode($requestBody, true);
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('POST')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantResult = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id', 'domain_name', 'name', 'address', 'city', 'state', 'zipcode', 'phone', 'email', 'company_name')));
      if (!empty($merchantResult)) {
        if (empty($requestBody['email'])) {
          $responsedata['message'] = "Please enter an email.";
          $responsedata['response'] = 0;
          return json_encode($responsedata);
        }else{
          // Checking Email parameter is String
	         $argEmail=$this->checkParameter($requestBody['email'],'string','Email'); 
                  if($argEmail['response']=='403'){                    
                    return json_encode($argEmail);
                  }
        }
        if (empty($requestBody['fname'])) {
          $responsedata['message'] = "Please enter your first name.";
          $responsedata['response'] = 0;
          return json_encode($responsedata);
        }else{
          // Checking First Name parameter is String
	         $argFname=$this->checkParameter($requestBody['fname'],'string','First name'); 
                  if($argFname['response']=='403'){                    
                    return json_encode($argFname);
                  } 
        }
        if (empty($requestBody['lname'])) {
          $responsedata['message'] = "Please enter your last name.";
          $responsedata['response'] = 0;
          return json_encode($responsedata);
        }else{
          // Checking Last name parameter is String
	         $argLname=$this->checkParameter($requestBody['lname'],'string','Last name'); 
                  if($argLname['response']=='403'){                    
                    return json_encode($argLname);
                  }
        }
        if (empty($requestBody['phone'])) {
          $responsedata['message'] = "Please enter your phone.";
          $responsedata['response'] = 0;
          return json_encode($responsedata);
        }else{
          // Checking Phone parameter is String
	         $argPhone=$this->checkParameter($requestBody['phone'],'string','Phone number'); 
                  if($argPhone['response']=='403'){                    
                    return json_encode($argPhone);
                  }
        }
        if(!empty($requestBody['country_code_id'])){
          // Checking Country Code parameter is String
	         $argCountry=$this->checkParameter($requestBody['country_code_id'],'string','Country Code'); 
                  if($argCountry['response']=='403'){                    
                    return json_encode($argCountry);
                  }
        }
         
        if (strlen($requestBody['phone']) > 15) {
          $responsedata['message'] = "Phone number should not be greater then 15 digits.";
          $responsedata['response'] = 0;
          return json_encode($responsedata);
        }

        if ($requestBody['is_privacypolicy'] == 1) {
          $email = strtolower(trim($requestBody['email'])); //Here username is email
          $roleIds = array('4', '5');
          $emailExist = $this->User->find('first',
                  array('conditions' => array('User.email' => $email, 'User.role_id' => $roleIds, 'User.merchant_id' => $merchant_id), 'fields' => array('id')));
          if (empty($emailExist)) {
            $requestBody['merchant_id'] = $merchant_id; // Merchant Id
            $roleId = 5; // Role Id of the user
            $userName = trim($requestBody['email']); //Here username is email
            $requestBody['username'] = trim($userName);
            $current_time = $date = date("Y-m-d H:i:s");
            $requestBody['dateOfjoin'] = $current_time;
            $countryId = $this->CountryCode->find('first',
                    array('conditions' => array('CountryCode.code' => $requestBody['country_code_id']), 'fields' => array('id')));
            $requestBody['country_code_id'] = $countryId['CountryCode']['id'];
            $token = time() . rand();
            $requestBody['email'] = $email;
            $requestBody['role_id'] = $roleId;
            $requestBody['is_active'] = 0;
            $requestBody['activation_token'] = $token;
            $requestBody['is_privacypolicy'] = 1;
            $requestBody['state_id'] = 0;
            $requestBody['city_id'] = 0;
            $requestBody['zip_id'] = 0;
            $number = "";

            if (!empty($requestBody['phone'])) {
              $phone = preg_replace("/[^0-9]/", "", $requestBody['phone']);
              $number = "(" . substr($phone, 0, 3) . ') ' .
                      substr($phone, 3, 3) . '-' .
                      substr($phone, 6);
            }
            $requestBody['phone'] = $number;
            if ($this->User->save($requestBody)) {
//                                $merchantResult = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchant_id)));
              $domain = $merchantResult['Merchant']['domain_name'];
              $this->loadModel('EmailTemplate');
              $template_type = 'merchant_customer_registration';
              $emailSuccess = $this->EmailTemplate->find('first',
                      array('conditions' => array('EmailTemplate.merchant_id' => $merchant_id, 'EmailTemplate.is_deleted' => 0, 'EmailTemplate.template_code' => $template_type)));

              if ($emailSuccess) {
                if ($requestBody['lname']) {
                  $fullName = $requestBody['fname'] . " " . $requestBody['lname'];
                } else {
                  $fullName = $requestBody['fname'];
                }
                $emailData = $emailSuccess['EmailTemplate']['template_message'];
                $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                $emailData = str_replace('{USERNAME}', $userName, $emailData);
                $emailData = str_replace('{PASSWORD}',
                        trim($requestBody['password']), $emailData);
                $url = $domain . '/hqusers/accountActivation/' . $token;
                $activationLink = '<a href="' . $url . '">Click Here</a>';
                $emailData = str_replace('{ACTIVE_LINK}', $activationLink,
                        $emailData);
                $url = "http://" . $merchantResult['Merchant']['domain_name'];
                $merchantUrl = "<a href=" . $url . " target='_blank'>" . "www." . $merchantResult['Merchant']['domain_name'] . "</a>";
                $emailData = str_replace('{MERCHANT_URL}', $merchantUrl,
                        $emailData);
                $emailData = str_replace('{MERCHANT_COMPANY_NAME}',
                        $merchantResult['Merchant']['name'], $emailData);
                $merchantAddress = $merchantResult['Merchant']['address'] . "<br>" . $merchantResult['Merchant']['city'] . ", " . $merchantResult['Merchant']['state'] . " " . $merchantResult['Merchant']['zipcode'];
                $merchantPhone = $merchantResult['Merchant']['phone'];
                $emailData = str_replace('{MERCHANT_ADDRESS}', $merchantAddress,
                        $emailData);
                $emailData = str_replace('{MERCHANT_PHONE}', $merchantPhone,
                        $emailData);
                $subject = ucwords(str_replace('_', ' ',
                                $emailSuccess['EmailTemplate']['template_subject']));
                $this->Email->to = $email;
                $this->Email->subject = $merchantResult['Merchant']['company_name'] . " " . $subject;
                $this->Email->from = $merchantResult['Merchant']['email'];
                $this->set('data', $emailData);
                $this->Email->template = 'template';
                $this->Email->smtpOptions = array(
                    'port' => "$this->smtp_port",
                    'timeout' => '100',
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
              $responsedata['message'] = "Thank you for signing up for " . $merchantResult['Merchant']['name'] . ". Activation link has been sent to your email address.";
              $responsedata['response'] = 1;
              return json_encode($responsedata);
            } else {
              $responsedata['message'] = "Record could not be saved, please try again.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            }
          } else {
            $responsedata['message'] = "Email already exists.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
          }
        } else {
          $responsedata['message'] = "Please accept term and conditions.";
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

  public function merchantTemplates() {
    $this->autoRender = false;
    $this->layout = false;
    $this->loadModel('Merchant');
    $merchantList = $this->Merchant->getMerchantList();
    $this->loadModel('EmailTemplate');
    $this->loadModel('DefaultTemplate');
    $emailData = $this->DefaultTemplate->getAllDefaultTemplate();

    foreach ($merchantList as $merchantID) {//echo "<br>===========<br>";
      foreach ($emailData as $eData) {
        unset($eData['DefaultTemplate']['id'],
                $eData['DefaultTemplate']['is_active'],
                $eData['DefaultTemplate']['is_deleted'],
                $eData['DefaultTemplate']['created'],
                $eData['DefaultTemplate']['modified'],
                $eData['DefaultTemplate']['store_id']);
        $eData['DefaultTemplate']['merchant_id'] = $merchantID;
        $emailTemp['EmailTemplate'] = $eData['DefaultTemplate'];
        $templateNotExists = $this->EmailTemplate->checkTemplate($eData['DefaultTemplate']['template_code'],
                $merchantID);
        if ($templateNotExists) {
          //pr($merchantID);
          $this->EmailTemplate->create();
          $this->EmailTemplate->saveTemplate($emailTemp);
        }
      }
    }
  }

  /*   * ******************************************************************************************
    @Function Name : menuItems
    @Description   : this function is used for Getting list of All Items with Size of Item based on merchant ID
    @Author        : SmartData
    created:13/09/2016
   * ****************************************************************************************** */

  public function menuItems() {

    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');    
   // $requestBody = '{"store_id": 2}'; //local server
    $requestBody['store_id']=$_GET['store_id'];
    $this->Webservice->webserviceLog($requestBody, "menu_item.txt", $headers);
    $responsedata = array();
//        $headers['merchant_id'] = 85;
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id', 'domain_name')));
      if (!empty($merchantCheck)) {
        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
          $store_id = $requestBody['store_id'];
          $storeResult = $this->Store->find('first',
                  array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id')));
          if (!empty($storeResult)) {
            $domain = $merchantCheck['Merchant']['domain_name'];

            $this->loadModel('Category');

            $this->Category->bindModel(
                    array('hasMany' => array(
                            'Item' => array(
                                'className' => 'Item',
                                'foreignKey' => 'category_id',
                                'conditions' => array('Item.is_active' => 1, 'Item.is_deleted' => 0),
                                'order' => array('position' => 'asc')
                            )
                        )
            ));
            $this->Category->bindModel(
                    array('belongsTo' => array(
                            'Store' => array(
                                'className' => 'Store',
                                'foreignKey' => 'store_id',
                                'fields' => array('id'),
                                'conditions' => array('Store.is_active' => 1, 'Store.is_deleted' => 0
            )))));
            $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));

            $categoryList = $this->Category->find('all',
                    array('conditions' => array('Category.store_id' => $store_id, 'Category.is_active' => 1, 'Category.is_deleted' => 0), 'order' => array('position' => 'asc'), 'recursive' => 2));
            $respon = array();
            if ($categoryList) {

              $protocol = 'http://';
              if (isset($_SERVER['HTTPS'])) {
                if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                  $protocol = 'https://';
                }
              }

              $currentdateTime = $this->Webservice->getcurrentTime($store_id, 1);
              $currentarr = explode(' ', $currentdateTime);
              $selectedTime = $currentarr[1];
              $currentTime = strtotime($currentarr[1]);
              $today = $currentarr[0];
              foreach ($categoryList as $k => $catList) {
                $is_Meal = 0;
                if ($catList['Category']['is_meal'] == 1) {
                  $starTime = strtotime($catList['Category']['start_time']);
                  $endTime = strtotime($catList['Category']['end_time']);
                  if ($currentTime >= $starTime && $currentTime <= $endTime) {
                    $is_Meal = 0;
                  } else {
                    $is_Meal = 1;
                  }
                }
                if ($is_Meal == 0) {
                  $respon[$k]['id'] = $catList['Category']['id'];
                  $respon[$k]['name'] = $catList['Category']['name'];
                  $respon[$k]['store_id'] = $catList['Category']['store_id'];
                  if (!empty($catList['Category']['start_time'])) {
                    $respon[$k]['start_time'] = $catList['Category']['start_time'];
                  } else {
                    $respon[$k]['start_time'] = " ";
                  }
                  if (!empty($catList['Category']['end_time'])) {
                    $respon[$k]['end_time'] = $catList['Category']['end_time'];
                  } else {
                    $respon[$k]['end_time'] = " ";
                  }
                  if (!empty($catList['Category']['imgcat'])) {
                    $respon[$k]['imgcat'] = $catList['Category']['imgcat'];
                  } else {
                    $respon[$k]['imgcat'] = " ";
                  }

                  if (!empty($catList['Category']['is_mandatory'])) {
                    $respon[$k]['is_mandatory'] = "1";
                    $respon[$k]['min_value'] = (!empty($catList['Category']['min_value'])) ? $catList['Category']['min_value'] : '0';
                    $respon[$k]['max_value'] = (!empty($catList['Category']['max_value'])) ? $catList['Category']['max_value'] : '0';
                  } else {
                    $respon[$k]['is_mandatory'] = "0";
                  }


                  $respon[$k]['is_meal'] = $catList['Category']['is_meal'];
                  $respon[$k]['has_topping'] = $catList['Category']['has_topping'];
                  if (!empty($catList['Category']['is_sizeonly'])) {
                    $respon[$k]['is_sizeonly'] = $catList['Category']['is_sizeonly'];
                  } else {
                    $respon[$k]['is_sizeonly'] = "";
                  }

                  if (!empty($catList['Item'])) {
                    foreach ($catList['Item'] as $key => $item) {
                      $itemId = $item['id'];
                      $categoryId = $item['category_id'];
                      $storeId = $store_id;
                      $respon[$k]['Item'][$key]['id'] = $item['id'];
                      $respon[$k]['Item'][$key]['name'] = $item['name'];
                      $respon[$k]['Item'][$key]['category_id'] = $item['category_id'];
                      if (!empty($catList['Category']['is_mandatory'])) {
                        $respon[$k]['Item'][$key]['mandatory_unit'] = $item['mandatory_item_units'];
                      } else {
                        $respon[$k]['Item'][$key]['mandatory_unit'] = "0";
                      }
                      if (!empty($item['start_date'])) {
                        $respon[$k]['Item'][$key]['start_date'] = $item['start_date'];
                      } else {
                        $respon[$k]['Item'][$key]['start_date'] = " ";
                      }
                      if (!empty($item['end_date'])) {
                        $respon[$k]['Item'][$key]['end_date'] = $item['end_date'];
                      } else {
                        $respon[$k]['Item'][$key]['end_date'] = " ";
                      }
                      if (!empty($item['image'])) {
                        $respon[$k]['Item'][$key]['image'] = $protocol . $domain . "/MenuItem-Image/" . $item['image'];
                      } else {

                        $respon[$k]['Item'][$key]['image'] = $protocol . $domain . "/MenuItem-Image/" . 'default_menu.png';
                      }
                      $respon[$k]['Item'][$key]['description'] = $item['description'];
                      $respon[$k]['Item'][$key]['is_seasonal_item'] = $item['is_seasonal_item'];
                      $respon[$k]['Item'][$key]['position'] = $item['position'];
                      $respon[$k]['Item'][$key]['Currency'] = "USD";
                      if ($item['preference_mandatory'] == 0) {
                        $respon[$k]['Item'][$key]['preference_mandatory'] = FALSE;
                      } else {
                        $respon[$k]['Item'][$key]['preference_mandatory'] = TRUE;
                      }


                      if ($item['is_deliverable'] == 0) {
                        $respon[$k]['Item'][$key]['is_deliverable'] = "0";
                      } else {
                        $respon[$k]['Item'][$key]['is_deliverable'] = "1";
                      }

                      $res = $this->getPreference($store_id, $item['id']);
                      if ($res == 1) {
                        $respon[$k]['Item'][$key]['isPrefenceAllowed'] = TRUE;
                      } else {
                        $respon[$k]['Item'][$key]['isPrefenceAllowed'] = FALSE;
                      }

                      $resadd = $this->getAddons($store_id, $item['id']);
                      if ($resadd == 1) {
                        $respon[$k]['Item'][$key]['isAddonsAllowed'] = TRUE;
                      } else {
                        $respon[$k]['Item'][$key]['isAddonsAllowed'] = FALSE;
                      }

                      $this->ItemPrice->bindModel(
                              array('belongsTo' => array(
                                      'Size' => array(
                                          'className' => 'Size',
                                          'foreignKey' => 'size_id',
                                          'fields' => array('id', 'size'),
                                          'conditions' => array('Size.is_active' => 1, 'Size.is_deleted' => 0, 'Size.store_id' => $storeId),
                                          'order' => array('Size.id ASC')
                                      ),
                                      'StoreTax' => array(
                                          'className' => 'StoreTax',
                                          'foreignKey' => 'store_tax_id',
                                          'fields' => array('id', 'tax_name', 'tax_value'),
                                          'conditions' => array('StoreTax.is_active' => 1, 'StoreTax.is_deleted' => 0, 'StoreTax.store_id' => $storeId)
                                      )
                      )));

//                                            $this->OfferDetail->bindModel(array(
//                                                'belongsTo' => array(
//                                                    'Item' => array(
//                                                        'className' => 'Item',
//                                                        'foreignKey' => 'offerItemID',
//                                                        'conditions' => array('Item.is_active' => 1, 'Item.is_deleted' => 0, 'Item.store_id' => $storeId),
//                                                        'type' => 'INNER',
//                                                    ),
//                                                )
//                                            ));

                      $this->Item->bindModel(
                              array('hasMany' => array(
                                      'ItemPrice' => array(
                                          'className' => 'ItemPrice',
                                          'foreignKey' => 'item_id',
                                          'type' => 'INNER',
                                          'fields' => array('id', 'item_id', 'price', 'size_id', 'store_tax_id'),
                                          'conditions' => array('ItemPrice.is_active' => 1, 'ItemPrice.is_deleted' => 0, 'ItemPrice.store_id' => $storeId),
                                          'order' => array('ItemPrice.position ASC')
                                      ),
                                  )
                      ));

                      $this->Item->bindModel(array(
                          'hasMany' => array(
                              'Offer' => array(
                                  'className' => 'Offer',
                                  'foreignKey' => 'item_id',
                                  'type' => 'INNER',
//                                            'order' => array('Offer.position ASC'),
                                  'conditions' => array('Offer.is_active' => 1, 'Offer.is_deleted' => 0, 'Offer.store_id' => $store_id, "OR" => array("AND" => array('Offer.offer_end_date >=' => $today, 'Offer.offer_start_date <=' => $today), 'Offer.offer_end_date IS NULL', 'Offer.offer_start_date IS NULL')), 'fields' => array('id', 'description', 'offer_end_date', 'offer_start_date', 'is_time', 'offer_start_time', 'offer_end_time')
                              ),
                              'ItemOffer' => array(
                                  'className' => 'ItemOffer',
                                  'foreignKey' => 'item_id',
                                  'type' => 'INNER',
                                  //'conditions' => array('ItemOffer.is_active' => 1, 'ItemOffer.is_deleted' => 0, 'ItemOffer.store_id' => $store_id, 'OR' => array('ItemOffer.end_date >=' => $today)),
                                  'conditions' => array('ItemOffer.is_active' => 1, 'ItemOffer.is_deleted' => 0, 'ItemOffer.store_id' => $store_id, 'ItemOffer.start_date <= ' => $today, 'ItemOffer.end_date >= ' => $today),
                                  'fields' => array('id', 'unit_counter', 'start_date', 'end_date')
                              )
                      )));
                      $positionvalue = array();
                      $itemTypearray = array();

                      $itemConditions = array('Item.store_id' => $storeId, 'Item.is_active' => 1, 'Item.is_deleted' => 0, 'Item.id' => $itemId);
                      $productInfo = $this->Item->find('first',
                              array('conditions' => $itemConditions, 'recursive' => 3, 'fields' => array('id')));
                      //pr($productInfo);

                      unset($productInfo['Item']);
                      if (!empty($productInfo['ItemPrice'])) {
                        foreach ($productInfo['ItemPrice'] as $s => $ItemPrice) {
                          $ItemPrice['Size'] = array_filter($ItemPrice['Size']);
                          $default_price = $ItemPrice['price'];
                          $intervalPrice = 0;
                          $intervalPrice = $this->getTimeIntervalPrice($ItemPrice['item_id'],
                                  $ItemPrice['size_id'], $storeId);
                          if (!empty($intervalPrice['Interval']['IntervalDay']) && !empty($intervalPrice['IntervalPrice'])) {
                            $default_price = $intervalPrice['IntervalPrice']['price'];
                            $interval_id = $intervalPrice['IntervalPrice']['interval_id'];
                          }
                          if (!empty($ItemPrice['size_id'])) {
                            $respon[$k]['Item'][$key]['Size'][$s]['size_id'] = $ItemPrice['Size']['id'];
                            $respon[$k]['Item'][$key]['Size'][$s]['size_name'] = $ItemPrice['Size']['size'];
                            if (!empty($default_price)) {
                              $respon[$k]['Item'][$key]['Size'][$s]['size_price'] = $default_price;
                            } else {
                              $respon[$k]['Item'][$key]['Size'][$s]['size_price'] = $ItemPrice['price'];
                            }
                            $respon[$k]['Item'][$key]['default_price'] = $respon[$k]['Item'][$key]['Size'][0]['size_price'];
                          } else {
                            $respon[$k]['Item'][$key]['default_price'] = $ItemPrice['price'];
                            $respon[$k]['Item'][$key]['Size'] = array();
                          }
                          if (!empty($ItemPrice['StoreTax'])) {
                            $respon[$k]['Item'][$key]['isStoreTax'] = TRUE;
                            $respon[$k]['Item'][$key]['StoreTax']['tax_name'] = $ItemPrice['StoreTax']['tax_name'];
                            $respon[$k]['Item'][$key]['StoreTax']['tax_value'] = $ItemPrice['StoreTax']['tax_value'];
                          } else {
                            $respon[$k]['Item'][$key]['isStoreTax'] = FALSE;
                            $respon[$k]['Item'][$key]['StoreTax']['tax_name'] = "";
                            $respon[$k]['Item'][$key]['StoreTax']['tax_value'] = "";
                          }
                        }
                        unset($productInfo['ItemPrice']);
                      } else {
                        $respon[$k]['Item'][$key]['default_price'] = 0;
                        $respon[$k]['Item'][$key]['Size'] = array();
                        $respon[$k]['Item'][$key]['isStoreTax'] = FALSE;
                        $respon[$k]['Item'][$key]['StoreTax']['tax_name'] = "";
                        $respon[$k]['Item'][$key]['StoreTax']['tax_value'] = "";
                      }
                      if ($item['preference_mandatory'] == 0) {
                        $respon[$k]['Item'][$key]['preference_mandatory'] = FALSE;
                      } else {
                        $respon[$k]['Item'][$key]['preference_mandatory'] = TRUE;
                      }

                      if (!empty($productInfo['Offer'])) {
                        $op = 0;
                        $Offer_detail = array();
                        foreach ($productInfo['Offer'] as $o => $promotionsList) {
                          if ($promotionsList['is_time'] == 1) {
                            if (($promotionsList['offer_start_time'] <= $selectedTime) && ($promotionsList['offer_end_time'] >= $selectedTime)) {
                              $Offer_detail[$op] = $promotionsList['description'];
                              if (!empty($promotionsList['offer_end_date'])) {
                                $promoDateArr = explode('-',
                                        $promotionsList['offer_end_date']);
                                $promoDate = $promoDateArr[1] . "/" . $promoDateArr[2] . "/" . $promoDateArr[0];
                                $Offer_detail[$op] = $promotionsList['description'] . ". [Valid until " . $promoDate . "].";
                              }
                              $op++;
                            }
                          } else {
                            $Offer_detail[$op] = $promotionsList['description'];
                            if (!empty($promotionsList['offer_end_date'])) {
                              $promoDateArr = explode('-',
                                      $promotionsList['offer_end_date']);
                              $promoDate = $promoDateArr[1] . "/" . $promoDateArr[2] . "/" . $promoDateArr[0];
                              $Offer_detail[$op] = $promotionsList['description'] . ". [Valid until " . $promoDate . "].";
                            }
                            $op++;
                          }
                          if (!empty($Offer_detail)) {
                            $respon[$k]['Item'][$key]['isOfferAvailabe'] = TRUE;
                          } else {
                            $respon[$k]['Item'][$key]['isOfferAvailabe'] = FALSE;
                            $Offer_detail = array();
                          }
                        }
                      } else {
                        $respon[$k]['Item'][$key]['isOfferAvailabe'] = FALSE;
                        $Offer_detail = array();
                      }

                      if (!empty($productInfo['ItemOffer'])) {
                        $respon[$k]['Item'][$key]['isExtendOfferAvailabe'] = TRUE;
                        $oE = 0;
                        $extended = array();
                        foreach ($productInfo['ItemOffer'] as $o => $extendedOfferList) {
                          $numSurfix = $this->Webservice->addOrdinalNumberSuffix($extendedOfferList['unit_counter']);
                          if (!empty($numSurfix)) {
                            $extended[$oE] = "Buy " . ($extendedOfferList['unit_counter'] - 1) . " unit and get the " . $numSurfix . " Item free on " . $item['name'];
                          } else {
                            $extended[$oE] = "Buy " . ($extendedOfferList['unit_counter'] - 1) . " unit and get 1 free on Item " . $item['name'];
                          }
                          if (!empty($extendedOfferList['end_date'])) {
                            $extendedDateArr = explode('-',
                                    $extendedOfferList['end_date']);
                            $extendedDate = $extendedDateArr[1] . "/" . $extendedDateArr[2] . "/" . $extendedDateArr[0];
                            $extended[$oE] = $extended[$oE] . ". [Valid until " . $extendedDate . "].";
                          }
                          $oE++;
                        }
                      } else {
                        $respon[$k]['Item'][$key]['isExtendOfferAvailabe'] = FALSE;
                        $extended = array();
                      }
                      $OfferDetailArr = array_merge($Offer_detail, $extended);
                      if (!empty($OfferDetailArr)) {
                        $respon[$k]['Item'][$key]['Offer_detail'] = $OfferDetailArr;
                      } else {
                        $respon[$k]['Item'][$key]['Offer_detail'] = array();
                      }
//                                
                    }
                  } else {
                    unset($respon[$k]);
                  }
                }
              }

              header('merchant_id:' . $merchant_id);
              header('store_id:' . $store_id);
              $responsedata['response'] = 1;
              $responsedata['message'] = "Success";
              $responsedata['data'] = array_values($respon);
              //pr($responsedata);
              return json_encode($responsedata);
            } else {
              header('merchant_id:' . $merchant_id);
              header('store_id:' . $store_id);
              $responsedata['message'] = "No Item found";
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

  public function getPreference($storeId = null, $itemId = null) {

    $this->Type->bindModel(
            array('hasMany' => array(
                    'SubPreference' => array(
                        'className' => 'SubPreference',
                        'foreignKey' => 'type_id',
                        'order' => array('SubPreference.position ASC'),
                        'conditions' => array('SubPreference.is_active' => 1, 'SubPreference.is_deleted' => 0, 'SubPreference.store_id' => $storeId),
                        'fields' => array('SubPreference.id', 'SubPreference.name', 'SubPreference.price', 'SubPreference.position')
                    )
                )
    ));

    $this->Type->unBindModel(array('hasMany' => array('ItemType')));
    $this->ItemType->bindModel(
            array('belongsTo' => array(
                    'Type' => array(
                        'className' => 'Type',
                        'foreignKey' => 'type_id',
                        'conditions' => array('Type.is_active' => 1, 'Type.is_deleted' => 0, 'Type.store_id' => $storeId),
                        'fields' => array('Type.id', 'Type.name', 'Type.price', 'Type.position')
                    )
    )));

    $preference = $this->ItemType->find('all',
            array('conditions' => array('ItemType.is_active' => 1, 'ItemType.is_deleted' => 0, 'ItemType.store_id' => $storeId, 'ItemType.item_id' => $itemId), 'fields' => array('id'), 'recursive' => 2));
    $isPrefenceAllowed = FALSE;
    if (!empty($preference)) {

      if (!empty($preference['ItemType'])) {

        if (!empty($preference['Type']['SubPreference'])) {
          $isPrefenceAllowed = TRUE;
        } else {
          $isPrefenceAllowed = FALSE;
        }
      } else {
        $isPrefenceAllowed = TRUE;
      }
    } else {
      $isPrefenceAllowed = FALSE;
    }

    return $isPrefenceAllowed;
  }

  public function getAddons($storeId = null, $itemId = null) {

    $isAddonsAllowed = FALSE;
    $topping = $this->Topping->find('all',
            array('conditions' => array('Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.store_id' => $storeId, 'Topping.item_id' => $itemId, 'Topping.addon_id !=' => 0), 'fields' => array('id')));
    if (!empty($topping)) {
      $isAddonsAllowed = TRUE;
    } else {
      $isAddonsAllowed = FALSE;
    }
    return $isAddonsAllowed;
  }

  public function getTimeIntervalPrice($itemId = null, $sizeId = null,
          $storeId = null) {

    //$this->layout = null;
    //$this->autoRender = false;
    $this->loadModel('Store');
    $this->loadModel('Interval');
    $this->loadModel('IntervalPrice');
    $this->loadModel('IntervalDay');

    $currentDateTime = $this->Webservice->getcurrentTime($storeId, 1);
    $currentTime = date("H:i:s", strtotime($currentDateTime));
    $currentDay = date("N", strtotime($currentDateTime));
    $this->Interval->unbindModel(
            array('hasMany' => array('IntervalDay'))
    );

    $this->Interval->bindModel(
            array(
                'hasOne' => array(
                    'IntervalDay' => array(
                        'className' => 'IntervalDay',
                        'foreignKey' => 'interval_id',
                        'conditions' => array('IntervalDay.week_day_id' => $currentDay, 'IntervalDay.day_status' => 1, 'IntervalDay.store_id' => $storeId),
                        'fields' => array('IntervalDay.id', 'IntervalDay.week_day_id', 'IntervalDay.interval_id'),
                        'type' => 'INNER',
                    )
                )
            )
    );

    $this->IntervalPrice->bindModel(
            array(
                'belongsTo' => array(
                    'Interval' => array(
                        'className' => 'Interval',
                        'foreignKey' => 'interval_id',
                        'conditions' => array('Interval.is_active' => 1, 'Interval.is_deleted' => 0, 'Interval.store_id' => $storeId, 'Interval.start <=' => $currentTime, 'Interval.end >=' => $currentTime),
                        'fields' => array('Interval.id', 'Interval.name'),
                        'type' => 'INNER'
                    )
                )
            )
    );
    $intervalPriceDetail = array();
    $intervalPriceDetail = $this->IntervalPrice->find('all',
            array('recursive' => 2, 'conditions' => array('IntervalPrice.item_id' => $itemId, 'IntervalPrice.size_id' => $sizeId, 'IntervalPrice.store_id' => $storeId, 'IntervalPrice.is_active' => 1, 'IntervalPrice.is_deleted' => 0, 'IntervalPrice.size_active' => 1), 'fields' => array('IntervalPrice.id', 'IntervalPrice.interval_id', 'IntervalPrice.price')));

    foreach ($intervalPriceDetail as $key => $value) {
      if (!empty($value['IntervalPrice']) && !empty($value['Interval']) && !empty($value['Interval']['IntervalDay'])) {
        return $intervalPriceDetail[$key];
        break;
      }
    }
  }

  /*   * ******************************************************************************************
    @Function Name : getStoreMenu
    @Description   : this function is used for Getting list of All Preference,Addon, Subpreference,sub addon and Default Addon of Item based on merchant ID
    @Author        : SmartData
    created:14/09/2016
   * ****************************************************************************************** */

  public function getStoreMenu() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
//        ini_set('memory_limit', '-1');
    $requestBody = file_get_contents('php://input');
    //$requestBody = '{"store_id": 2,"item_id":240,"size_id":0}';
    //$requestBody = '{"store_id": "108","item_id": "4974","size_id":"336"}';
    $requestBody['store_id']=$_GET['store_id'];
    $requestBody['item_id']=$_GET['item_id'];
    if(isset($_GET['size_id']) && !empty($_GET['size_id'])){
     $requestBody['size_id']=$_GET['size_id'];
    }else{
      $requestBody['size_id']='';
    }
    $this->Webservice->webserviceLog($requestBody, "get_store_menu.txt", $headers);
    $responsedata = array();
    //$headers['merchant_id'] = 85;
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {
        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
          $store_id = $requestBody['store_id'];
          $storeResult = $this->Store->find('first',
                  array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id')));
          if (!empty($storeResult)) {

           // if (isset($requestBody['size_id']) && !empty($requestBody['size_id'])) {
              if (isset($requestBody['size_id'])){
              if (isset($requestBody['item_id']) && !empty($requestBody['item_id'])) {
                $store_id = $requestBody['store_id'];
                $itemId = $requestBody['item_id'];
                $size_Id = $requestBody['size_id'];
                if ($size_Id > 0) {
                  $size_Id = $requestBody['size_id'];
                } else {
                  $size_Id = "";
                }

                $this->Type->bindModel(
                        array('hasMany' => array(
                                'SubPreference' => array(
                                    'className' => 'SubPreference',
                                    'foreignKey' => 'type_id',
                                    'order' => array('SubPreference.position ASC'),
                                    'conditions' => array('SubPreference.is_active' => 1, 'SubPreference.is_deleted' => 0, 'SubPreference.store_id' => $store_id),
                                    'fields' => array('SubPreference.id', 'SubPreference.name', 'SubPreference.price', 'SubPreference.position')
                                )
                            )
                ));
                $this->ItemType->bindModel(
                        array('belongsTo' => array(
                                'Type' => array(
                                    'className' => 'Type',
                                    'foreignKey' => 'type_id',
                                    'conditions' => array('Type.is_active' => 1, 'Type.is_deleted' => 0, 'Type.store_id' => $store_id),
                                    'fields' => array('Type.id', 'Type.name', 'Type.price', 'Type.position', 'Type.min_value', 'Type.max_value')
                                )
                )));
                $this->ItemPrice->bindModel(
                        array('belongsTo' => array(
                                'Size' => array(
                                    'className' => 'Size',
                                    'foreignKey' => 'size_id',
                                    'conditions' => array('Size.is_active' => 1, 'Size.is_deleted' => 0, 'Size.store_id' => $store_id),
                                    'order' => array('Size.id ASC'),
                                    'fields' => array('id', 'size', 'category_id')
                                )
                )));


                $this->Topping->bindModel(
                        array('hasMany' => array(
                                'SubTopping' => array(
                                    'className' => 'Topping',
                                    'foreignKey' => 'addon_id',
                                    'order' => array('SubTopping.position ASC'),
                                    'conditions' => array('SubTopping.is_addon_category' => 0, 'SubTopping.is_active' => 1, 'SubTopping.is_deleted' => 0, 'SubTopping.store_id' => $store_id),
                                    'fields' => array('SubTopping.id', 'SubTopping.name', 'SubTopping.is_addon_category', 'SubTopping.addon_id', 'SubTopping.size_id', 'SubTopping.price', 'SubTopping.item_id', 'SubTopping.no_size', 'SubTopping.position')
                                )
                            ),
                            'hasOne' => array(
                                'SubToppingPrice' => array(
                                    'className' => 'ToppingPrice',
                                    'foreignKey' => 'topping_id',
                                    'conditions' => array('SubToppingPrice.is_active' => 1, 'SubToppingPrice.is_deleted' => 0, 'SubToppingPrice.store_id' => $store_id),
                                    'fields' => array('SubToppingPrice.id', 'SubToppingPrice.store_id', 'SubToppingPrice.item_id', 'SubToppingPrice.size_id', 'SubToppingPrice.topping_id', 'SubToppingPrice.price')
                                )
                            )
                        )
                );

                $this->Item->bindModel(
                        array(
                    'hasMany' => array(
                        'ItemType' => array(
                            'className' => 'ItemType',
                            'foreignKey' => 'item_id',
                            'order' => array('ItemType.position ASC'),
                            'conditions' => array('ItemType.is_active' => 1, 'ItemType.is_deleted' => 0, 'ItemType.store_id' => $store_id),
                            'fields' => array('id', 'item_id', 'type_id', 'position', 'position')
                        ),
                        'ItemPrice' => array(
                            'className' => 'ItemPrice',
                            'foreignKey' => 'item_id',
                            'conditions' => array('ItemPrice.is_active' => 1, 'ItemPrice.is_deleted' => 0, 'ItemPrice.store_id' => $store_id, 'ItemPrice.size_id' => $size_Id),
                            'order' => array('ItemPrice.position ASC'),
                            'fields' => array('id', 'item_id', 'price', 'store_tax_id', 'size_id', 'position')
                        ), 'Topping' => array(
                            'className' => 'Topping',
                            'foreignKey' => 'item_id',
                            'order' => array('Topping.position ASC'),
                            'conditions' => array('Topping.is_addon_category' => 1, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.store_id' => $store_id),
                            'fields' => array('id', 'price', 'item_id', 'category_id', 'name', 'is_addon_category', 'addon_id', 'no_size', 'size_id', 'price', 'position', 'min_value', 'max_value')
                        ), 'ItemDefaultTopping' => array(
                            'className' => 'ItemDefaultTopping',
                            'foreignKey' => 'item_id',
                            'conditions' => array('ItemDefaultTopping.store_id' => $store_id, 'ItemDefaultTopping.is_active' => 1, 'ItemDefaultTopping.is_deleted' => 0),
                            'fields' => array('id', 'topping_id', 'item_id')
                        )
                    )
                        ), false);
                $this->Type->unBindModel(array('hasMany' => array('ItemType')));



                $categoryList = $this->Item->find('first',
                        array('recursive' => 4, 'fields' => array('id', 'name', 'category_id', 'start_date', 'end_date', 'image', 'description', 'is_seasonal_item', 'position', 'is_deliverable', 'default_subs_price', 'preference_mandatory'), 'conditions' => array('Item.id' => $itemId, 'Item.store_id' => $store_id, 'Item.is_active' => 1, 'Item.is_deleted' => 0)));
                $toppingSizes = $this->AddonSize->find('all',
                        array('fields' => array('id', 'size', 'price_percentage'), 'conditions' => array('AddonSize.store_id' => $store_id, 'AddonSize.is_active' => 1, 'AddonSize.is_deleted' => 0, 'AddonSize.merchant_id' => $merchant_id)));
//                                pr($categoryList);
//                                 pr($toppingSizes);
                $responseData = array();
                foreach ($categoryList as $key => $catarr) {
                  $categoryList['Item']['ItemPrice'] = $categoryList['ItemPrice'];
                  $categoryList['Item']['Topping'] = $categoryList['Topping'];
                  $categoryList['Item']['Preference'] = $categoryList['ItemType'];

                  foreach ($categoryList['Item']['ItemPrice'] as $iPkey => $itemPricearr) {

                    if (!empty($itemPricearr['Size'])) {
                      $preferenceData = $this->getSubPrePrice($itemPricearr['size_id'],
                              $itemPricearr['item_id'], $store_id);
                      if (!empty($preferenceData)) {
                        foreach ($preferenceData as $preKey => $dataPreference) {
                          if (!empty($dataPreference['ItemType'])) {
                            $categoryList['Item']['ItemPrice'][$iPkey]['Size']['subPre'] = $preferenceData;
                          } else {
                            
                          }
                        }
                      }
                      $ToppingData = $this->getToppingPrice($itemPricearr['size_id'],
                              $itemPricearr['item_id'], $store_id);
                      if (!empty($ToppingData)) {
                        $categoryList['Item']['ItemPrice'][$iPkey]['Size']['Top'] = $ToppingData;
                      }
                    } else {
                      unset($categoryList['ItemPrice'][$iPkey]);
                    }
                  }
                }

                unset($categoryList['ItemPrice']);
                unset($categoryList['Topping']);
                unset($categoryList['ItemType']);

                //pr($categoryList);
                if (!empty($size_Id)) {
                  if (!empty($categoryList['Item']['ItemPrice'])) {
                    foreach ($categoryList['Item']['ItemPrice'] as $k => $itemList) {
                      if (!empty($itemList['Size']['subPre'])) {
                        foreach ($itemList['Size']['subPre'] as $sub => $subData) {
                          if (!empty($subData['ItemType']) && !empty($subData['SubPreference'])) {
                            $responseData['preferences'][$sub]['id'] = $subData['Type']['id'];
                            $responseData['preferences'][$sub]['name'] = $subData['Type']['name'];
                            $responseData['preferences'][$sub]['minvalue'] = (!empty($subData['Type']['min_value'])) ? $subData['Type']['min_value'] : '0';
                            $responseData['preferences'][$sub]['maxvalue'] = (!empty($subData['Type']['max_value'])) ? $subData['Type']['max_value'] : '0';
                            foreach ($subData['SubPreference'] as $subpre => $subPreData) {
                              $responseData['preferences'][$sub]['subprefernce'][$subpre]['id'] = $subPreData['id'];
                              $responseData['preferences'][$sub]['subprefernce'][$subpre]['name'] = $subPreData['name'];
                              if ($categoryList['Item']['default_subs_price'] == 0) {
                                if (!empty($subPreData['SubPreferencePrice'])) {
                                  $responseData['preferences'][$sub]['subprefernce'][$subpre]['price'] = $subPreData['SubPreferencePrice']['price'];
                                } else {
                                  $responseData['preferences'][$sub]['subprefernce'][$subpre]['price'] = '0';
                                }
                              } else {
                                $responseData['preferences'][$sub]['subprefernce'][$subpre]['price'] = $subPreData['price'];
                              }
                            }
                          }
                        }
                      }
//pr($responseData);
                      if ($categoryList['Item']['default_subs_price'] == 1) {
                        if (!empty($categoryList['Item']['Topping'])) {
                          foreach ($categoryList['Item']['Topping'] as $t => $topData) {
                            if (!empty($topData['SubTopping'])) {
                              $responseData['adons'][$t]['id'] = $topData['id'];
                              $responseData['adons'][$t]['name'] = $topData['name'];

                              $responseData['adons'][$t]['minvalue'] = (!empty($topData['min_value'])) ? $topData['min_value'] : '0';
                              $responseData['adons'][$t]['maxvalue'] = (!empty($topData['max_value'])) ? $topData['max_value'] : '0';



                              foreach ($topData['SubTopping'] as $subAdd => $subAddonData) {

                                $responseData['adons'][$t]['subaddons'][$subAdd]['id'] = $subAddonData['id'];
                                $responseData['adons'][$t]['subaddons'][$subAdd]['name'] = $subAddonData['name'];
                                $ogetsubaddon = $this->getsubaddon($store_id,
                                        $subAddonData['item_id'],
                                        $subAddonData['id']);
                                if (!empty($ogetsubaddon)) {
                                  $responseData['adons'][$t]['subaddons'][$subAdd]['is_defualt'] = TRUE;
                                  $responseData['adons'][$t]['subaddons'][$subAdd]['price'] = '0';
                                  $subAddonData['price'] = '0';
                                } else {
                                  $responseData['adons'][$t]['subaddons'][$subAdd]['is_defualt'] = FALSE;
                                  $responseData['adons'][$t]['subaddons'][$subAdd]['price'] = $subAddonData['price'];
                                }
                                if ($subAddonData['no_size'] == 1) {
                                  $responseData['adons'][$t]['subaddons'][$subAdd]['no_size'] = TRUE;
                                } else {
                                  $responseData['adons'][$t]['subaddons'][$subAdd]['no_size'] = FALSE;
                                }

                                $i = 0;
                                $responseData['adons'][$t]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['size_id'] = '0';
                                $responseData['adons'][$t]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['size_name'] = '1';
                                $responseData['adons'][$t]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['price'] = $subAddonData['price'];
                                if (!empty($toppingSizes)) {
                                  foreach ($toppingSizes as $st => $sizeTopping) {
                                    $i++;
                                    $price = round($subAddonData['price'] * ($sizeTopping['AddonSize']['price_percentage'] / 100),
                                            2);
                                    $responseData['adons'][$t]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['size_id'] = $sizeTopping['AddonSize']['id'];
                                    $responseData['adons'][$t]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['size_name'] = $sizeTopping['AddonSize']['size'];
                                    $responseData['adons'][$t]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['price'] = (string) $price;
                                  }
                                }
                              }
                            }
                          }
                        }
                      } else {
                        if (!empty($itemList['Size']['Top'])) {
                          foreach ($itemList['Size']['Top'] as $top => $topData) {
                            if (!empty($topData['subTopping'])) {
                              $responseData['adons'][$top]['id'] = $topData['Topping']['id'];
                              $responseData['adons'][$top]['name'] = $topData['Topping']['name'];
                              $responseData['adons'][$top]['minvalue'] = (!empty($topData['Topping']['min_value'])) ? $topData['Topping']['min_value'] : '0';
                              $responseData['adons'][$top]['maxvalue'] = (!empty($topData['Topping']['max_value'])) ? $topData['Topping']['max_value'] : '0';


                              foreach ($topData['subTopping'] as $subAdd => $subAddonData) {
                                $responseData['adons'][$top]['subaddons'][$subAdd]['id'] = $subAddonData['id'];
                                $responseData['adons'][$top]['subaddons'][$subAdd]['name'] = $subAddonData['name'];

                                $ogetsubaddon = $this->getsubaddon($store_id,
                                        $subAddonData['item_id'],
                                        $subAddonData['id']);
                                if (!empty($ogetsubaddon)) {
                                  $responseData['adons'][$top]['subaddons'][$subAdd]['is_defualt'] = TRUE;
                                  $responseData['adons'][$top]['subaddons'][$subAdd]['price'] = '0';
                                  if (!empty($subAddonData['subToppingPrice'])) {
                                    $responseData['adons'][$top]['subaddons'][$subAdd]['price'] = $subAddonData['subToppingPrice']['price'];
                                    $subAddonData['price'] = $subAddonData['subToppingPrice']['price'];
                                  } else {
                                    $responseData['adons'][$top]['subaddons'][$subAdd]['price'] = '0';
                                    $subAddonData['price'] = 0;
                                  }
                                } else {
                                  $responseData['adons'][$top]['subaddons'][$subAdd]['is_defualt'] = FALSE;
                                  if (!empty($subAddonData['subToppingPrice'])) {
                                    $responseData['adons'][$top]['subaddons'][$subAdd]['price'] = $subAddonData['subToppingPrice']['price'];
                                    $subAddonData['price'] = $subAddonData['subToppingPrice']['price'];
                                  } else {
                                    $responseData['adons'][$top]['subaddons'][$subAdd]['price'] = '0';
                                    $subAddonData['price'] = 0;
                                  }
                                }
                                if ($subAddonData['no_size'] == 1) {
                                  $responseData['adons'][$top]['subaddons'][$subAdd]['no_size'] = TRUE;
                                } else {
                                  $responseData['adons'][$top]['subaddons'][$subAdd]['no_size'] = FALSE;
                                }
                                $i = 0;
                                $responseData['adons'][$top]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['size_id'] = '0';
                                $responseData['adons'][$top]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['size_name'] = '1';
                                if (($responseData['adons'][$top]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['size_name'] == '1') && !empty($ogetsubaddon)) {

                                  $responseData['adons'][$top]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['price'] = '0';
                                } else {
                                  $responseData['adons'][$top]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['price'] = $subAddonData['price'];
                                }
                                if (!empty($toppingSizes)) {
                                  foreach ($toppingSizes as $st => $sizeTopping) {
                                    $i++;
                                    $price = round($subAddonData['price'] * ($sizeTopping['AddonSize']['price_percentage'] / 100),
                                            2);
                                    $responseData['adons'][$top]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['size_id'] = $sizeTopping['AddonSize']['id'];
                                    $responseData['adons'][$top]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['size_name'] = $sizeTopping['AddonSize']['size'];
                                    $responseData['adons'][$top]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['price'] = (string) $price;
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  } else {
                    header('merchant_id:' . $merchant_id);
                    header('store_id:' . $store_id);
                    $responsedata['message'] = "Preference and addons not found";
                    $responsedata['response'] = 0;
                  }
                } else {

                  if (!empty($categoryList['Item']['Preference'])) {
                    foreach ($categoryList['Item']['Preference'] as $t => $PreferenceData) {
                      if (!empty($PreferenceData['Type']['SubPreference'])) {
                        $responseData['preferences'][$t]['id'] = $PreferenceData['Type']['id'];
                        $responseData['preferences'][$t]['name'] = $PreferenceData['Type']['name'];
                        $responseData['preferences'][$t]['minvalue'] = (!empty($PreferenceData['Type']['min_value'])) ? $PreferenceData['Type']['min_value'] : '0';
                        $responseData['preferences'][$t]['maxvalue'] = (!empty($PreferenceData['Type']['max_value'])) ? $PreferenceData['Type']['max_value'] : '0';

                        foreach ($PreferenceData['Type']['SubPreference'] as $subPreference => $subPreferenceDataData) {
                          $responseData['preferences'][$t]['subprefernce'][$subPreference]['id'] = $subPreferenceDataData['id'];
                          $responseData['preferences'][$t]['subprefernce'][$subPreference]['name'] = $subPreferenceDataData['name'];
                          $responseData['preferences'][$t]['subprefernce'][$subPreference]['price'] = $subPreferenceDataData['price'];
                        }
                      }
                    }
                  } else {
                    
                  }

                  if (!empty($categoryList['Item']['Topping'])) {

                    foreach ($categoryList['Item']['Topping'] as $t => $topData) {
                      if (!empty($topData['SubTopping'])) {
                        $responseData['adons'][$t]['id'] = $topData['id'];
                        $responseData['adons'][$t]['name'] = $topData['name'];
                        $responseData['adons'][$t]['minvalue'] = (!empty($topData['min_value'])) ? $topData['min_value'] : '0';
                        $responseData['adons'][$t]['maxvalue'] = (!empty($topData['max_value'])) ? $topData['max_value'] : '0';


                        foreach ($topData['SubTopping'] as $subAdd => $subAddonData) {
                          $responseData['adons'][$t]['subaddons'][$subAdd]['id'] = $subAddonData['id'];
                          $responseData['adons'][$t]['subaddons'][$subAdd]['name'] = $subAddonData['name'];


                          $ogetsubaddon = $this->getsubaddon($store_id,
                                  $subAddonData['item_id'], $subAddonData['id']);
                          if (!empty($ogetsubaddon)) {
                            $responseData['adons'][$t]['subaddons'][$subAdd]['is_defualt'] = TRUE;
                            $responseData['adons'][$t]['subaddons'][$subAdd]['price'] = '0';
                            if ($categoryList['Item']['default_subs_price'] == 1) {
                              $subAddonData['price'] = $subAddonData['price'];
                            } else {
                              if (!empty($subAddonData['subToppingPrice'])) {
                                $subAddonData['price'] = $subAddonData['subToppingPrice']['price'];
                              } else {
                                $subAddonData['price'] = 0;
                              }
                            }
                          } else {
                            $responseData['adons'][$t]['subaddons'][$subAdd]['is_defualt'] = FALSE;
                            if ($categoryList['Item']['default_subs_price'] == 1) {
                              $responseData['adons'][$t]['subaddons'][$subAdd]['price'] = $subAddonData['price'];
                            } else {
                              if (!empty($subAddonData['subToppingPrice'])) {
                                $responseData['adons'][$t]['subaddons'][$subAdd]['price'] = $subAddonData['subToppingPrice']['price'];
                                $subAddonData['price'] = $subAddonData['subToppingPrice']['price'];
                              } else {
                                if (!empty($subAddonData['price'])) {
                                  $subAddonData['price'] = $subAddonData['price'];
                                } else {
                                  $subAddonData['price'] = '0';
                                }
                                $responseData['adons'][$t]['subaddons'][$subAdd]['price'] = $subAddonData['price'];
                                $subAddonData['price'] = (float) $subAddonData['price'];
                              }
                            }
                          }
                          if ($subAddonData['no_size'] == 1) {
                            $responseData['adons'][$t]['subaddons'][$subAdd]['no_size'] = TRUE;
                          } else {
                            $responseData['adons'][$t]['subaddons'][$subAdd]['no_size'] = FALSE;
                          }
                          $i = 0;
                          $responseData['adons'][$t]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['size_id'] = '0';
                          $responseData['adons'][$t]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['size_name'] = '1';
                          $responseData['adons'][$t]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['price'] = (string) $subAddonData['price'];
                          if (!empty($toppingSizes)) {
                            foreach ($toppingSizes as $st => $sizeTopping) {
                              $i++;
                              $price = round($subAddonData['price'] * ($sizeTopping['AddonSize']['price_percentage'] / 100),
                                      2);
                              $responseData['adons'][$t]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['size_id'] = $sizeTopping['AddonSize']['id'];
                              $responseData['adons'][$t]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['size_name'] = $sizeTopping['AddonSize']['size'];
                              $responseData['adons'][$t]['subaddons'][$subAdd]['sub_add_on_sizes'][$i]['price'] = (string) $price;
                            }
                          }
                        }
                      }
                    }
                  } else {
                    $responseData['Item']['AddOn'] = array();
                  }
                }

                header('merchant_id:' . $merchant_id);
                header('store_id:' . $store_id);
                $responsedata['response'] = 1;
                $responsedata['message'] = "Success";
                if (!empty($responseData['preferences'])) {
                  $responsedata['preferences'] = array_values($responseData['preferences']);
                } else {
                  $responsedata['preferences'] = array();
                }
                if (!empty($responseData['adons'])) {
                  $responsedata['adons'] = array_values($responseData['adons']);
                } else {
                  $responsedata['adons'] = array();
                }
//                                pr($responsedata);
                return json_encode($responsedata, JSON_PRETTY_PRINT);
              } else {
                $responsedata['message'] = "Please select a item.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
              }
            } else {
              $responsedata['message'] = "Please select a size.";
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

  public function getSubPrePrice($sizeID = null, $ItemId = null, $storeId = null) {
    $this->SubPreference->bindModel(
            array('hasOne' => array(
                    'SubPreferencePrice' => array(
                        'className' => 'SubPreferencePrice',
                        'foreignKey' => 'sub_preference_id',
                        'conditions' => array('SubPreferencePrice.is_active' => 1, 'SubPreferencePrice.is_deleted' => 0, 'SubPreferencePrice.store_id' => $storeId, 'SubPreferencePrice.size_id' => $sizeID, 'SubPreferencePrice.item_id' => $ItemId),
                        'fields' => array('SubPreferencePrice.id', 'SubPreferencePrice.item_id', 'SubPreferencePrice.size_id', 'SubPreferencePrice.price', 'SubPreferencePrice.sub_preference_id'),
                        'type' => 'INNER'
                    )
                )
    ));

    $this->Type->bindModel(
            array('hasMany' => array(
                    'SubPreference' => array(
                        'className' => 'SubPreference',
                        'foreignKey' => 'type_id',
                        'order' => array('SubPreference.position ASC'),
                        'conditions' => array('SubPreference.is_active' => 1, 'SubPreference.is_deleted' => 0, 'SubPreference.store_id' => $storeId),
                        'fields' => array('SubPreference.id', 'SubPreference.name', 'SubPreference.price', 'SubPreference.position'),
                        'type' => 'INNER'
                    ),
                    'ItemType' => array(
                        'className' => 'ItemType',
                        'foreignKey' => 'type_id',
                        'conditions' => array('ItemType.is_active' => 1, 'ItemType.is_deleted' => 0, 'ItemType.item_id' => $ItemId, 'ItemType.store_id' => $storeId),
                        'fields' => array('ItemType.id', 'ItemType.item_id', 'ItemType.type_id'),
                        'type' => 'INNER'
                    )
    )));
    $preferenceData = $this->Type->getTypedetails($storeId);
    return $preferenceData;
  }

  public function getToppingPrice($sizeID = null, $ItemId = null,
          $storeId = null) {

    $this->Topping->unbindModel(array('hasMany' => array('Topping'), 'hasOne' => array('ItemDefaultTopping', 'ToppingPrice')));

    $this->Topping->bindModel(
            array('hasMany' => array(
                    'subTopping' => array(
                        'className' => 'Topping',
                        'foreignKey' => 'addon_id',
                        'type' => 'inner',
                        'conditions' => array('subTopping.is_deleted' => 0, 'subTopping.is_active' => 1, 'subTopping.is_addon_category' => 0),
                        'fields' => array('subTopping.id', 'subTopping.item_id', 'subTopping.price', 'subTopping.name', 'subTopping.is_addon_category', 'subTopping.addon_id', 'subTopping.no_size', 'subTopping.size_id')
                    )
                )
            )
    );
    $this->Topping->subTopping->bindModel(array('hasOne' => array(
            'subToppingPrice' => array(
                'className' => 'ToppingPrice',
                'foreignKey' => 'topping_id',
                'conditions' => array('subToppingPrice.is_active' => 1, 'subToppingPrice.is_deleted' => 0, 'subToppingPrice.store_id' => $storeId, 'subToppingPrice.size_id' => $sizeID, 'subToppingPrice.item_id' => $ItemId),
                'fields' => array('subToppingPrice.id', 'subToppingPrice.store_id', 'subToppingPrice.item_id', 'subToppingPrice.size_id', 'subToppingPrice.topping_id', 'subToppingPrice.price')
            )
        )
    ));
    $Toppingprice = $this->Topping->find('all',
            array('recursive' => 3, 'fields' => array('id', 'price', 'item_id', 'name', 'category_id', 'is_addon_category', 'addon_id', 'no_size', 'size_id', 'min_value', 'max_value'), 'conditions' => array('Topping.item_id' => $ItemId, 'Topping.store_id' => $storeId, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.is_addon_category' => 1)));
    return $Toppingprice;
  }

  /*   * ******************************************************************************************
    @Function Name : guestLogin
    @Description   : this function is used for Guest Login based on merchant ID
    @Author        : SmartData
    created:14/09/2016
   * ****************************************************************************************** */

  public function guestLogin() {

    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "guest_login.txt", $headers);
    //$requestBody = '{"fname":"ranjeet.com","lname": "saini","email":"rjsaini@mailinator.com","phone": "(222) 222-2222","country_code_id": "+91"}';
    $responsedata = array();
    $requestBody = json_decode($requestBody, true);
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('POST')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {
        if (isset($requestBody['email']) && !empty($requestBody['email']) && isset($requestBody['fname']) && !empty($requestBody['fname']) && isset($requestBody['lname']) && !empty($requestBody['lname']) && isset($requestBody['phone']) && !empty($requestBody['phone']) && isset($requestBody['country_code_id']) && !empty($requestBody['country_code_id'])) {

          // Checking Email parameter is String
	         $argEmail=$this->checkParameter($requestBody['email'],'string','Email'); 
                  if($argEmail['response']=='403'){                    
                    return json_encode($argEmail);
                  }
            // Checking First Name parameter is String
	         $argFname=$this->checkParameter($requestBody['fname'],'string','First name'); 
                  if($argFname['response']=='403'){                    
                    return json_encode($argFname);
                  }      
          // Checking Last name parameter is String
	         $argLname=$this->checkParameter($requestBody['lname'],'string','Last name'); 
                  if($argLname['response']=='403'){                    
                    return json_encode($argLname);
                  }
            // Checking Phone parameter is String
	         $argPhone=$this->checkParameter($requestBody['phone'],'string','Phone'); 
                  if($argPhone['response']=='403'){                    
                    return json_encode($argPhone);
                  }
              // Checking Country Code parameter is String
	         $argCountry=$this->checkParameter($requestBody['country_code_id'],'string','Country Code'); 
                  if($argCountry['response']=='403'){                    
                    return json_encode($argCountry);
                  }     
          
          $countryId = $this->CountryCode->find('first',
                    array('conditions' => array('CountryCode.code' => $requestBody['country_code_id']), 'fields' => array('id')));
            $requestBody['country_code_id'] = $countryId['CountryCode']['id'];
          
          $data['DeliveryAddress']['name_on_bell'] = $requestBody['fname'] . " " . $requestBody['lname'];
          $data['DeliveryAddress']['user_id'] = 0;
          $data['DeliveryAddress']['merchant_id'] = $merchant_id;
          $number = "";
          if (!empty($requestBody['phone'])) {
            $phone = preg_replace("/[^0-9]/", "", $requestBody['phone']);
            $number = "(" . substr($phone, 0, 3) . ') ' .
                    substr($phone, 3, 3) . '-' .
                    substr($phone, 6);
          }
          $data['DeliveryAddress']['phone'] = $number;
          $data['DeliveryAddress']['email'] = $requestBody['email'];
          $data['DeliveryAddress']['country_code_id'] = $requestBody['country_code_id'];
          if ($this->DeliveryAddress->save($data)) {
            $address_id = $this->DeliveryAddress->getLastInsertId();
            $guestID = $this->Encryption->encode($address_id);
            $EncryptmerchantID = $this->Encryption->encode($merchant_id);
            $authToken = md5(microtime().rand());
                  $authTime=time();
                  $UserDeviceDet['auth_token'] = $authToken;
                  $UserDeviceDet['auth_time'] = $authTime;
                  if (isset($requestBody['device_token']) && !empty($requestBody['device_token'])) {
                    $UserDeviceDet['device_token'] = $requestBody['device_token'];
                  } else {
                    $UserDeviceDet['device_token'] = "";
                  }
                  $userTokenDet = $this->UserToken->find('first', array('conditions' => array("UserToken.device_token" => $UserDeviceDet['device_token'], "UserToken.is_active" => 1, "UserToken.is_deleted" => 0)));
                  if(!empty($userTokenDet)){
                    $UserDeviceDet['id']=$userTokenDet['UserToken']['id'];
                    $result = $this->UserToken->save($UserDeviceDet);
                  }

                  if (empty($userDeviceDet)) {
                    $result = $this->UserToken->save($UserDeviceDet);
                  } 
            //$this->guesttokenGenerate($guestID, $merchant_id);
            $responsedata['message'] = "Success";
            $responsedata['response'] = 1;
            //$responsedata['token'] = $guestID;
            $responsedata['token'] = "0";
            return json_encode($responsedata);
          } else {
            $responsedata['message'] = "Record could not be saved, please try again.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
          }
        } else {
          $responsedata['message'] = "Please fill all required fields.";
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

  public function guesttokenGenerate($headerVal = null, $merchant_id = null) {
    $iPod = stripos($_SERVER['HTTP_USER_AGENT'], "iPod");
    $iPhone = stripos($_SERVER['HTTP_USER_AGENT'], "iPhone");
    $iPad = stripos($_SERVER['HTTP_USER_AGENT'], "iPad");
    $Android = stripos($_SERVER['HTTP_USER_AGENT'], "Android");
    $webOS = stripos($_SERVER['HTTP_USER_AGENT'], "webOS");
    header('guest_token:' . $headerVal);
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

  /* ------------- Users Coupon Code End--------------------- */

  /* -------------  Code Start for Apply Coupon Code --------------------- */

  /*   * ******************************************************************************************
    @Function Name : applyCoupon
    @Description   : this function is used for Apply Coupon on merchant ID
    @Author        : SmartData
    created:14/09/2016
   * ****************************************************************************************** */

  public function applyCoupon() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    //$requestBody = '{"store_id":108,"coupon_code":"NewYear2017"}';
    $responsedata = array();
    $requestBody['store_id']=$_GET['store_id'];
    $requestBody['coupon_code']=$_GET['coupon_code'];
    $this->Webservice->webserviceLog($requestBody, "apply_coupon.txt", $headers);
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {
        if (isset($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          if (empty($user_id)) {
            $user_id = 0;
          }
          $roleid = array(4, 5);
          if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
            $store_id = $requestBody['store_id'];
            if (isset($requestBody['coupon_code']) && !empty($requestBody['coupon_code'])) {
              $storeResult = $this->Store->find('first',
                      array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id')));
              if (!empty($storeResult)) {
                $couponCode = strtolower($requestBody['coupon_code']);
                //$couponCode = $requestBody['coupon_code'];
                $coupon = $this->Coupon->find('first',
                        array('conditions' => array('Coupon.store_id' => $store_id, 'Coupon.coupon_code' => $couponCode, 'Coupon.is_deleted' => 0)));
                //print_r($coupon);
                if (!empty($coupon)) {
                  if ($coupon['Coupon']['is_active'] == 1) {

                    $currentdatetemp = $this->Webservice->getcurrentTime($store_id,
                            1);
                    $currentarr = explode(' ', $currentdatetemp);
                    $currentdate = $currentarr[0];
                    $currentTimestr = strtotime($currentarr[1]);
                    if ($coupon['Coupon']['start_date'] > $currentdate) {
                      $responsedata['message'] = "Coupon is not active.";
                      $responsedata['response'] = 0;
                      return json_encode($responsedata);
                    }
                    if ($coupon['Coupon']['end_date'] < $currentdate) {
                      $responsedata['message'] = "Coupon is not active.";
                      $responsedata['response'] = 0;
                      return json_encode($responsedata);
                    }
                    $Istime = 1;

                    if (!empty($coupon['Coupon']['allow_time'])) {
                      $startTime = strtotime($coupon['Coupon']['start_time']);
                      $endTime = strtotime($coupon['Coupon']['end_time']);
                      if ($startTime <= $currentTimestr && $endTime >= $currentTimestr) {
                        $Istime = 1;
                      } else {
                        $Istime = 0;
                      }
                      if (empty($Istime)) {
                        $responsedata['message'] = "Coupon is not valid at this time.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                      }
                    }


                    if (!empty($coupon['Coupon']['days']) && $Istime) {
                      $days = explode(',', $coupon['Coupon']['days']);
                      $day_number = date('N', strtotime($currentdate));
                      if (!in_array($day_number, $days)) {
                        $responsedata['message'] = "Coupon is not valid on this day.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                      }
                    }





                    if ($coupon['Coupon']['number_can_use'] > $coupon['Coupon']['used_count']) {
                      $couponDet = array();
                      $couponDet['id'] = $coupon['Coupon']['id'];
                      $couponDet['discount_type'] = $coupon['Coupon']['discount_type'];
                      if ($coupon['Coupon']['discount_type'] == 1) {
                        $couponDet['discount_in'] = "USD";
                        $couponDet['coupon_detail'] = 'You got $' . $coupon['Coupon']['discount'] . " Off.";
                      } else {
                        $couponDet['coupon_detail'] = 'You got ' . $coupon['Coupon']['discount'] . "% Off.";
                      }
                      $couponDet['discount'] = $coupon['Coupon']['discount'];
                      $responsedata['response'] = 1;
                      if (!empty($couponDet['coupon_detail'])) {
                        $responsedata['message'] = $couponDet['coupon_detail'];
                      } else {
                        $responsedata['message'] = "Success";
                      }
                      $responsedata['data'] = $couponDet;
                      return json_encode($responsedata);
                    } else {
                      $responsedata['message'] = "This coupon code is invalid or has expired.";
                      $responsedata['response'] = 0;
                      return json_encode($responsedata);
                    }
                  } else {
                    $responsedata['message'] = "Coupon is not active.";
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
              $responsedata['message'] = "Please select a coupon.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            }
          } else {
            $responsedata['message'] = "Please select a store.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
          }
        } else {
          $responsedata['message'] = "Please login or continue as a guest.";
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

  public function deals() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    //$requestBody = '{"store_id": 2}';
    $requestBody['store_id']=$_GET['store_id'];
    $this->Webservice->webserviceLog($requestBody, "deals.txt", $headers);
    $responsedata = array();
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        if (isset($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          if (empty($user_id)) {
            $user_id = 0;
          }
          $roleid = array(4, 5);
//                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
          //if (!empty($userDet)) {
          if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
            $store_id = $requestBody['store_id'];
            $storeResult = $this->Store->find('first',
                    array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
            if (!empty($storeResult)) {
              $merchant_id = $headers['merchant_id'];
              $DateTime = $this->Webservice->getcurrentTime($store_id, 1);
              $currentarr = explode(' ', $DateTime);
              $currentTime = $currentarr[1];
              $today = $currentarr[0];
              $this->UserCoupon->bindModel(array('belongsTo' => array('Coupon')),
                      false);
              $UserCoupon = $this->UserCoupon->find('all',
                      array('conditions' => array('UserCoupon.user_id' => $user_id, 'Coupon.store_id' => $store_id, 'UserCoupon.is_active' => 1, 'UserCoupon.is_deleted' => 0, 'Coupon.start_date <= ' => $today, 'Coupon.end_date >= ' => $today, 'Coupon.is_active' => 1, 'Coupon.is_deleted' => 0)));
              $merchantResult = $this->Merchant->find('first',
                      array('conditions' => array('Merchant.id' => $merchant_id)));
              $domain = $merchantResult['Merchant']['domain_name'];
              //print_r($UserCoupon);
              $protocol = 'http://';
              if (isset($_SERVER['HTTPS'])) {
                if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                  $protocol = 'https://';
                }
              }
              $coupons = array();
              if (!empty($UserCoupon)) {
                foreach ($UserCoupon as $c => $couponsDet) {
//                        pr($couponsDet);
                  $coupons[$c]['id'] = $couponsDet['Coupon']['id'];
                  $coupons[$c]['name'] = $couponsDet['Coupon']['name'];
                  $coupons[$c]['coupon_code'] = $couponsDet['Coupon']['coupon_code'];
                  $coupons[$c]['number_can_use'] = $couponsDet['Coupon']['number_can_use'];
                  $coupons[$c]['discount_type'] = $couponsDet['Coupon']['discount_type'];
                  $coupons[$c]['discount'] = $couponsDet['Coupon']['discount'];
                  if ($couponsDet['Coupon']['discount_type'] == 1) {
                    $coupons[$c]['coupon_detail'] = 'Use ' . $couponsDet['Coupon']['coupon_code'] . " get $" . $couponsDet['Coupon']['discount'] . " Off.";
                  } else if ($couponsDet['Coupon']['discount_type'] == 2) {
                    $coupons[$c]['coupon_detail'] = 'Use ' . $couponsDet['Coupon']['coupon_code'] . " get " . $couponsDet['Coupon']['discount'] . "% Off.";
                  }
                  $coupons[$c]['store_url'] = $protocol . $storeResult['Store']['store_url'];
                }
              }

              $promotions = array();
              $this->ItemOffer->bindModel(array(
                  'belongsTo' => array(
                      'Item' => array(
                          'className' => 'Item',
                          'foreignKey' => 'item_id',
                          'type' => 'INNER',
                          'conditions' => array('Item.is_active' => 1, 'Item.is_deleted' => 0, 'Item.store_id' => $store_id)
                      ),
                  )
              ));

              $this->ItemOffer->bindModel(
                      array('belongsTo' => array(
                              'Category' => array(
                                  'className' => 'Category',
                                  'foreignKey' => 'category_id',
                                  'fields' => array('id', 'name'),
                                  'conditions' => array('Category.is_active' => 1, 'Category.is_deleted' => 0)
              ))));
              //$promotionList = $this->ItemOffer->find('all', array('recursive' => 2, 'conditions' => array('ItemOffer.is_active' => 1, 'ItemOffer.is_deleted' => 0, 'ItemOffer.store_id' => $store_id, 'OR' => array('ItemOffer.end_date >=' => $today))));
              $promotionList = $this->ItemOffer->find('all',
                      array('recursive' => 2, 'conditions' => array('ItemOffer.is_active' => 1, 'ItemOffer.is_deleted' => 0, 'ItemOffer.store_id' => $store_id, 'ItemOffer.start_date <= ' => $today, 'ItemOffer.end_date >= ' => $today)));


              if (!empty($promotionList)) {
                foreach ($promotionList as $p => $ListPromo) {
                  if (!empty($ListPromo['Category']['id'])) {
                    $promotions[$p]['promo_id'] = $ListPromo['ItemOffer']['id'];
                    $promotions[$p]['item_id'] = $ListPromo['Item']['id'];
                    $promotions[$p]['item_name'] = $ListPromo['Item']['name'];
                    $promotions[$p]['description'] = $ListPromo['Item']['description'];
                    $numSurfix = $this->Webservice->addOrdinalNumberSuffix($ListPromo['ItemOffer']['unit_counter']);

                    if (!empty($numSurfix)) {
                      $promotions[$p]['extended_detail'] = "Buy " . ($ListPromo['ItemOffer']['unit_counter'] - 1) . " unit and get the $numSurfix" . " Item free on " . $ListPromo['Item']['name'] . '.';
                    } else {
                      $promotions[$p]['extended_detail'] = "Buy " . ($ListPromo['ItemOffer']['unit_counter'] - 1) . " unit and get 1 free on Item " . $ListPromo['Item']['name'] . '.';
                    }
                    if (!empty($ListPromo['Item']['image'])) {
                      $promotions[$p]['image'] = $protocol . $domain . "/MenuItem-Image/" . $ListPromo['Item']['image'];
                    } else {
                      $promotions[$p]['image'] = $protocol . $domain . "/Offer-Image/default_offer.png";
                    }

                    $promotions[$p]['unit'] = $ListPromo['ItemOffer']['unit_counter'];
                    if (!empty($ListPromo['ItemOffer']['start_date'])) {
                      $promotions[$p]['start_date'] = $ListPromo['ItemOffer']['start_date'];
                    } else {
                      $promotions[$p]['start_date'] = "";
                    }
                    if (!empty($ListPromo['ItemOffer']['end_date'])) {
                      $promotions[$p]['end_date'] = $ListPromo['ItemOffer']['end_date'];
                    } else {
                      $promotions[$p]['end_date'] = "";
                    }
                    $promotions[$p]['store_url'] = $protocol . $storeResult['Store']['store_url'];
                  }
                }
              }
              $this->Offer->bindModel(array(
                  'hasMany' => array(
                      'OfferDetail' => array(
                          'className' => 'OfferDetail',
                          'foreignKey' => 'offer_id',
                          'type' => 'INNER',
                          'conditions' => array('OfferDetail.is_active' => 1, 'OfferDetail.is_deleted' => 0, 'OfferDetail.store_id' => $store_id),
                          'fields' => array('OfferDetail.id', 'OfferDetail.offer_id', 'OfferDetail.offerItemID', 'OfferDetail.offerSize', 'OfferDetail.offerItemType', 'OfferDetail.quantity', 'OfferDetail.discountAmt')
                      ),
                  )
              ));

              $conditions = array(
                  'Offer.is_active' => '1',
                  'Offer.is_deleted' => '0',
                  'Offer.store_id' => $store_id,
                  'OR' => array(
                      array(
                          'Offer.is_time' => '0',
                          array(
                              'OR' => array(
                                  array(
                                      'Offer.offer_start_date <=' => $today,
                                      'Offer.offer_end_date >=' => $today
                                  ),
                                  array(
                                      'Offer.offer_start_date is NULL',
                                      'Offer.offer_end_date is NULL'
                                  )
                              )
                          )
                      ),
                      array(
                          'Offer.is_time' => '1',
                          array(
                              'OR' => array(
                                  array(
                                      'Offer.offer_start_date <=' => $today,
                                      'Offer.offer_end_date >=' => $today
                                  ),
                                  array(
                                      'Offer.offer_start_date is NULL',
                                      'Offer.offer_end_date is NULL'
                                  )
                              )
                          ),
                          array(
                              'OR' => array(
                                  array(
                                      'Offer.offer_start_time <=' => $currentTime,
                                      'Offer.offer_end_time >=' => $currentTime
                                  ),
                                  array(
                                      'Offer.offer_start_time is NULL',
                                      'Offer.offer_end_time is NULL'
                                  )
                              )
                          )
                      )
                  )
              );


              $offerList = $this->Offer->find('all',
                      array(
                  'recursive' => 2,
                  'conditions' => $conditions, 'fields' => array('Offer.id', 'Offer.store_id', 'Offer.item_id', 'Offer.size_id', 'Offer.description', 'Offer.unit', 'Offer.is_fixed_price', 'Offer.offerprice', 'Offer.is_time', 'Offer.offer_start_date', 'Offer.offer_end_date', 'Offer.offer_start_time', 'Offer.offer_end_time', 'Offer.is_time', 'Offer.offerImage')));
              //pr($offerList);die;
              $Offerpromotion = array();
              $of = 0;
              if (!empty($offerList)) {
                foreach ($offerList as $o => $promotionsList) {
                  if (!empty($promotionsList['OfferDetail'])) {
                    $itemDet = $this->getItemDetails($store_id,
                            $promotionsList['Offer']['item_id']);
                    //pr($itemDet);
                    if (!empty($itemDet['Category']['id'])) {
                      $Offerpromotion['Offer'][$of]['id'] = $promotionsList['Offer']['id'];
                      $Offerpromotion['Offer'][$of]['Item_name'] = $itemDet['Item']['name'];
                      $Offerpromotion['Offer'][$of]['item_id'] = $promotionsList['Offer']['item_id'];
                      $Offerpromotion['Offer'][$of]['category_id'] = $itemDet['Category']['id'];
                      $Offerpromotion['Offer'][$of]['category_name'] = $itemDet['Category']['name'];
                      $Offerpromotion['Offer'][$of]['store_url'] = $protocol . $storeResult['Store']['store_url'];
                      if ($promotionsList['Offer']['size_id'] == 0) {
                        $Offerpromotion['Offer'][$of]['isSizeApplicable'] = FALSE;
                      } else {
                        $Offerpromotion['Offer'][$of]['isSizeApplicable'] = TRUE;
                      }
                      if ($promotionsList['Offer']['unit'] >= 1) {
                        $Offerpromotion['Offer'][$of]['isUnitApplicable'] = TRUE;
                      } else {
                        $Offerpromotion['Offer'][$of]['isUnitApplicable'] = FALSE;
                      }
                      $Offerpromotion['Offer'][$of]['size_id'] = $promotionsList['Offer']['size_id'];
                      $sizeDet = $this->getsizeNames($store_id,
                              $promotionsList['Offer']['size_id']);
                      if (!empty($sizeDet)) {
                        $Offerpromotion['Offer'][$of]['size_name'] = $sizeDet['Size']['size'];
                      } else {
                        $Offerpromotion['Offer'][$of]['size_name'] = "";
                      }
                      $ItemPriceDet = $this->getItemPrices($store_id,
                              $promotionsList['Offer']['item_id'],
                              $promotionsList['Offer']['size_id']);
                      if (!empty($ItemPriceDet)) {
                        $Offerpromotion['Offer'][$of]['Item_price'] = $ItemPriceDet['ItemPrice']['price'];
                      } else {
                        $Offerpromotion['Offer'][$of]['Item_price'] = 0;
                      }
                      if (!empty($promotionsList['Offer']['description'])) {
                        $Offerpromotion['Offer'][$of]['offer_description'] = $promotionsList['Offer']['description'];
                      } else {
                        $Offerpromotion['Offer'][$of]['offer_description'] = "";
                      }


                      if (!empty($promotionsList['Offer']['offer_start_date'])) {
                        $Offerpromotion['Offer'][$of]['offer_start_date'] = $promotionsList['Offer']['offer_start_date'];
                      } else {
                        $Offerpromotion['Offer'][$of]['offer_start_date'] = "";
                      }
                      if (!empty($promotionsList['Offer']['offer_end_date'])) {
                        $Offerpromotion['Offer'][$of]['offer_end_date'] = $promotionsList['Offer']['offer_end_date'];
                      } else {
                        $Offerpromotion['Offer'][$of]['offer_end_date'] = "";
                      }
                      if ($promotionsList['Offer']['is_time'] == 1) {
                        $Offerpromotion['Offer'][$of]['is_time'] = TRUE;
                        $Offerpromotion['Offer'][$of]['offer_start_time'] = $promotionsList['Offer']['offer_start_time'];
                        $Offerpromotion['Offer'][$of]['offer_end_time'] = $promotionsList['Offer']['offer_end_time'];

                        if ($promotionsList['Offer']['offer_start_time'] == "24:00:00") {
                          $Offerpromotion['Offer'][$of]['offer_start_time'] = "00:00:00";
                        }
                        if ($promotionsList['Offer']['offer_end_time'] == "24:00:00") {
                          $Offerpromotion['Offer'][$of]['offer_end_time'] = "00:00:00";
                        }
                      } else {
                        $Offerpromotion['Offer'][$of]['is_time'] = FALSE;
                        $Offerpromotion['Offer'][$of]['offer_start_time'] = "";
                        $Offerpromotion['Offer'][$of]['offer_end_time'] = "";
                      }
                      $protocol = 'http://';
                      if (isset($_SERVER['HTTPS'])) {
                        if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                          $protocol = 'https://';
                        }
                      }
                      if (!empty($promotionsList['Offer']['offerImage'])) {
                        $Offerpromotion['Offer'][$of]['offerImage'] = $protocol . $domain . "/Offer-Image/" . $promotionsList['Offer']['offerImage'];
                      } else if (!empty($itemDet['Item']['image'])) {
                        $Offerpromotion['Offer'][$of]['offerImage'] = $protocol . $domain . "/MenuItem-Image/" . $itemDet['Item']['image'];
                      } else {
                        $Offerpromotion['Offer'][$of]['offerImage'] = $protocol . $domain . "/Offer-Image/default_offer.png";
                      }
                      $Offerpromotion['Offer'][$of]['unit'] = $promotionsList['Offer']['unit'];

                      if ($promotionsList['Offer']['is_fixed_price'] == 0) {
                        $Offerpromotion['Offer'][$of]['is_fixed_price'] = FALSE;
                        $Offerpromotion['Offer'][$of]['offerprice'] = "";
                        $Offerpromotion['Offer'][$of]['offerDetail'] = array();
                      }

                      if ($promotionsList['Offer']['is_fixed_price'] == 1) {
                        $Offerpromotion['Offer'][$of]['is_fixed_price'] = TRUE;
                        $Offerpromotion['Offer'][$of]['offerprice'] = $promotionsList['Offer']['offerprice'];
                        $Offerpromotion['Offer'][$of]['offerDetail'] = array();
                      }

                      if (!empty($promotionsList['OfferDetail'])) {
                        $j = 0;
                        foreach ($promotionsList['OfferDetail'] as $oD => $promotionsItemList) {
                          $offerItemitemDet = $this->getItemDetails($store_id,
                                  $promotionsItemList['offerItemID']);
                          if (!empty($offerItemitemDet['Category']['id'])) {
                            $itemSize = $this->getsizeNames($store_id,
                                    $promotionsItemList['offerSize']);
                            $Offerpromotion['Offer'][$of]['offerDetail'][$j]['Offered_id'] = $promotionsItemList['id'];
                            $Offerpromotion['Offer'][$of]['offerDetail'][$j]['offered_item_id'] = $promotionsItemList['offerItemID'];
                            $Offerpromotion['Offer'][$of]['offerDetail'][$j]['name'] = $offerItemitemDet['Item']['name'];
                            $Offerpromotion['Offer'][$of]['offerDetail'][$j]['price'] = $promotionsItemList['discountAmt'];
                            $Offerpromotion['Offer'][$of]['offerDetail'][$j]['category_id'] = $offerItemitemDet['Category']['id'];
                            $Offerpromotion['Offer'][$of]['offerDetail'][$j]['category_name'] = $offerItemitemDet['Category']['name'];
                            if (!empty($promotionsList['offerImage'])) {
                              $Offerpromotion['Offer'][$of]['offerDetail'][$j]['image'] = $protocol . $domain . "/Offer-Image/" . $promotionsList['offerImage'];
                            } else {
                              $Offerpromotion['Offer'][$of]['offerDetail'][$j]['image'] = $protocol . $domain . "/Offer-Image/default_offer.png";
                            }
                            $Offerpromotion['Offer'][$of]['offerDetail'][$j]['item_description'] = $offerItemitemDet['Item']['description'];
                            if (!empty($itemSize)) {
                              $Offerpromotion['Offer'][$of]['offerDetail'][$j]['size_name'] = $itemSize['Size']['size'];
                              $Offerpromotion['Offer'][$of]['offerDetail'][$j]['size_id'] = $itemSize['Size']['id'];
                            } else {
                              $Offerpromotion['Offer'][$of]['offerDetail'][$j]['size_name'] = "";
                              $Offerpromotion['Offer'][$of]['offerDetail'][$j]['size_id'] = $promotionsItemList['offerSize'];
                            }
                            $j++;
                          }
                        }
                        if (!empty($Offerpromotion['Offer'][$of]['offerDetail'])) {
                          $Offerpromotion['Offer'][$of]['offerDetail'] = array_values($Offerpromotion['Offer'][$of]['offerDetail']);
                        }
                      }
                      $of++;
                    }
                  }
                }
              }
              header('merchant_id:' . $merchant_id);
              $responsedata['response'] = 1;
              $responsedata['message'] = "Success";
              $responsedata['coupons'] = array_values($coupons);
              $responsedata['ExtendedPromo'] = array_values($promotions);
              if (!empty($Offerpromotion['Offer'])) {
                $responsedata['Offers'] = array_values($Offerpromotion['Offer']);
              } else {
                $responsedata['Offers'] = array();
              }
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
          //} else {
          //    $responsedata['message'] = "You are not register under this merchant";
          //    $responsedata['response'] = 0;
          //    return json_encode($responsedata);
          //}
        } else {
          $responsedata['message'] = "Please login or continue as a guest.";
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

  public function getsizeNames($storeId = null, $sizeId = null) {

    $getsize = $this->Size->find('first',
            array('conditions' => array('Size.is_active' => 1, 'Size.is_deleted' => 0, 'Size.store_id' => $storeId, 'Size.id' => $sizeId), 'fields' => array('Size.id', 'Size.size')));
    return $getsize;
    //pr($getsize);
  }

  public function getItemPrices($storeId = null, $itemId = null, $sizeId = null) {
    $this->ItemPrice->unBindModel(array('belongsTo' => array('Size', 'StoreTax')));
    $getItemPrice = $this->ItemPrice->find('first',
            array('conditions' => array('ItemPrice.is_active' => 1, 'ItemPrice.is_deleted' => 0, 'ItemPrice.store_id' => $storeId, 'ItemPrice.item_id' => $itemId, 'ItemPrice.size_id' => $sizeId), 'fields' => array('id', 'item_id', 'price', 'size_id'), 'recursive' => 2));
    return $getItemPrice;
  }

  public function getItemDetails($storeId = null, $itemId = null) {
    $this->Item->bindModel(
            array('belongsTo' => array(
                    'Category' => array(
                        'className' => 'Category',
                        'foreignKey' => 'category_id',
                        'fields' => array('id', 'name'),
                        'conditions' => array('Category.is_active' => 1, 'Category.is_deleted' => 0)
    ))));
    $getItemPrice = $this->Item->find('first',
            array('conditions' => array('Item.is_active' => 1, 'Item.is_deleted' => 0, 'Item.store_id' => $storeId, 'Item.id' => $itemId)));
    return $getItemPrice;
  }

  public function getsubaddon($storeId = null, $itemId = null, $toppingId = null) {
    $this->ItemDefaultTopping->bindModel(
            array('belongsTo' => array(
                    'Topping' => array(
                        'className' => 'Topping',
                        'foreignKey' => 'topping_id',
                        'fields' => array('id', 'name', 'name', 'price', 'name'),
                        'conditions' => array('Topping.is_active' => 1, 'Topping.is_deleted' => 0)
    ))));
    $itemDefaultTopping = $this->ItemDefaultTopping->find('all',
            array('conditions' => array('ItemDefaultTopping.is_active' => 1, 'ItemDefaultTopping.is_deleted' => 0, 'ItemDefaultTopping.store_id' => $storeId, 'ItemDefaultTopping.item_id' => $itemId, 'ItemDefaultTopping.topping_id' => $toppingId), 'fields' => array('id', 'topping_id', 'item_id')));
    return $itemDefaultTopping;
  }

  /* ------------------------------------------------
    Function name:getProfileInfo()
    Description:This section will manage the profile of the user for Customer
    created:23/11/2016
    ----------------------------------------------------- */

  public function getProfileInfo() {

    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "myprofile.txt", $headers);
    //$headers['user_id']='NTc2';
    //$headers['merchant_id'] = 85;
    $responsedata = array();
    $requestBody = json_decode($requestBody, true);

        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        if (isset($headers['user_id']) && !empty($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          $roleid = array(4, 5);
          $user = $this->User->find("first",
                  array("conditions" => array("User.id" => $user_id, "User.merchant_id" => $merchant_id), 'fields' => array('email', 'password', 'fname', 'id', 'lname', 'phone', 'dateOfBirth', 'country_code_id', 'is_deleted', 'is_active', 'is_newsletter', 'is_emailnotification', 'is_smsnotification')));
          if (!empty($user)) {
            $responsedata['message'] = "Success";
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
              $responsedata['dateOfBirth'] = " ";
            }
            if (!empty($user['User']['country_code_id'])) {
              $responsedata['country_code_id'] = $user['User']['country_code_id'];
            } else {
              $responsedata['country_code_id'] = " ";
            }

            if ($user['User']['is_newsletter']) {
              $responsedata['is_newsletter'] = true;
            } else {
              $responsedata['is_newsletter'] = false;
            }

            if ($user['User']['is_smsnotification']) {
              $responsedata['is_smsnotification'] = true;
            } else {
              $responsedata['is_smsnotification'] = false;
            }

            if ($user['User']['is_emailnotification']) {
              $responsedata['is_emailnotification'] = true;
            } else {
              $responsedata['is_emailnotification'] = false;
            }

            $EncryptUserID = $this->Encryption->encode($responsedata['id']);
            $EncryptmerchantID = $this->Encryption->encode($merchant_id);
            $this->tokenGenerate($EncryptUserID, $EncryptmerchantID);
            $responsedata['token'] = $EncryptUserID;
            $countryCode = $this->CountryCode->find('first',
                    array('conditions' => array('CountryCode.id' => $user['User']['country_code_id'])));
            if (!empty($countryCode)) {
              $responsedata['country_code_id'] = $countryCode['CountryCode']['code'];
            } else {
              $responsedata['country_code_id'] = "+1";
            }
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

  /* ------------------------------------------------
    Function name:myProfile()
    Description:This section will manage the profile of the user for Customer
    created:30/09/2016
    ----------------------------------------------------- */

  public function myProfile() {

    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "myprofile.txt", $headers);
    //$requestBody = '{"fname":"Rishabh","lname":"Bhardwaj","phone":"(953) 077-6612","country_code_id":"1","changepassword":false,"oldpassword":"Smartdata123","password":"Smartdata123","dateOfBirth":"1988-08-15","is_newsletter":false,"is_emailnotification":false,"is_smsnotification":false}';
    //$headers['user_id']='Mjg2';
    //$headers['merchant_id'] = 1;
    $responsedata = array();
    $requestBody = json_decode($requestBody, true);

        /*Check HTTP METHOD Request*/
        if(!$this->request->is('PUT')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        if (isset($headers['user_id']) && !empty($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          $roleid = array(4, 5);
          $userResult = $this->User->find("first",
                  array("conditions" => array("User.id" => $user_id, "User.merchant_id" => $merchant_id), 'fields' => array('email', 'password', 'fname', 'id', 'lname', 'phone', 'dateOfBirth', 'country_code_id', 'is_deleted', 'is_active', 'is_newsletter', 'is_emailnotification', 'is_smsnotification')));
          if (!empty($userResult)) {

            if (isset($requestBody['fname']) && !empty($requestBody['fname']) && isset($requestBody['phone']) && !empty($requestBody['phone']) && isset($requestBody['country_code_id']) && !empty($requestBody['country_code_id'])) {
              
              // Checking Email parameter is String
	         $argPassword=$this->checkParameter($requestBody['password'],'string','Password'); 
                  if($argPassword['response']=='403'){                    
                    return json_encode($argPassword);
                  }
            // Checking First Name parameter is String
	         $argFname=$this->checkParameter($requestBody['fname'],'string','First name'); 
                  if($argFname['response']=='403'){                    
                    return json_encode($argFname);
                  }      
          // Checking Last name parameter is String
	         $argLname=$this->checkParameter($requestBody['lname'],'string','Last name'); 
                  if($argLname['response']=='403'){                    
                    return json_encode($argLname);
                  }
            // Checking Phone parameter is String
	         $argPhone=$this->checkParameter($requestBody['phone'],'string','Phone'); 
                  if($argPhone['response']=='403'){                    
                    return json_encode($argPhone);
                  }
              // Checking Country Code parameter is String
	         $argCountry=$this->checkParameter($requestBody['country_code_id'],'string','Country Code'); 
                  if($argCountry['response']=='403'){                    
                    return json_encode($argCountry);
                  }
              // Checking status id parameter is boolean
                $argNewsletter = $this->checkParameter($requestBody['is_newsletter'], 'boolean','Newsletter'); 
                if ($argNewsletter['response'] == '403') {
                  return json_encode($argNewsletter);
                }
              // Checking status id parameter is boolean
               $argEmailNotification = $this->checkParameter($requestBody['is_emailnotification'],'boolean','Email notification');               if ($argEmailNotification['response'] == '403') {
                  return json_encode($argEmailNotification);
                }
              // Checking status id parameter is boolean
                $argSmsNotification = $this->checkParameter($requestBody['is_smsnotification'], 'boolean','Sms notification'); 
                if ($argSmsNotification['response'] == '403') {
                  return json_encode($argSmsNotification);
                }  

              $data['User']['id'] = $userResult['User']['id'];
              $data['User']['fname'] = $requestBody['fname'];
              $data['User']['lname'] = $requestBody['lname'];
              $number = "";
              if (!empty($requestBody['phone'])) {
                $phone = preg_replace("/[^0-9]/", "", $requestBody['phone']);
                $number = "(" . substr($phone, 0, 3) . ') ' .
                        substr($phone, 3, 3) . '-' .
                        substr($phone, 6);
              }
              $data['User']['phone'] = $number;

              if ($requestBody['is_newsletter']) {
                $data['User']['is_newsletter'] = 1;
                $requestBody['is_newsletter'] = 1;
              } else {
                $data['User']['is_newsletter'] = 0;
                $requestBody['is_newsletter'] = 0;
              }
              if ($requestBody['is_smsnotification']) {
                $data['User']['is_smsnotification'] = 1;
                $requestBody['is_smsnotification'] = 1;
              } else {
                $data['User']['is_smsnotification'] = 0;
                $requestBody['is_smsnotification'] = 0;
              }
              if ($requestBody['is_emailnotification']) {
                $data['User']['is_emailnotification'] = 1;
                $requestBody['is_emailnotification'] = 1;
              } else {
                $data['User']['is_emailnotification'] = 0;
                $requestBody['is_emailnotification'] = 0;
              }
              if ($requestBody['changepassword']) {
                $data['User']['changepassword'] = 1;
              } else {
                $data['User']['changepassword'] = 0;
              }

              $countryId = $this->CountryCode->find('first',
                      array('conditions' => array('CountryCode.code' => $requestBody['country_code_id']), 'fields' => array('id')));
              $data['User']['country_code_id'] = $countryId['CountryCode']['id'];
              //$data['User']['country_code_id'] = $requestBody['country_code_id'];

              $dbformatDate = $this->Dateform->formatDate($requestBody['dateOfBirth']);
              $requestBody['dateOfBirth'] = $dbformatDate;
              $data['User']['dateOfBirth'] = $requestBody['dateOfBirth'];

              if ($requestBody['changepassword'] == 1) {
                $oldPassword = AuthComponent::password($requestBody['oldpassword']);
                if ($oldPassword != $userResult['User']['password']) {
                  $responsedata['message'] = "Please enter correct old password";
                  $responsedata['response'] = 0;
                  return json_encode($responsedata);
                } else {
                  $data['User']['password'] = $requestBody['password'];
                }
              }
              $data['User']['state_id'] = 0;
              $data['User']['city_id'] = 0;
              $data['User']['zip_id'] = 0;
              $merchantResult = $this->Merchant->find('first',
                      array('conditions' => array('Merchant.id' => $merchant_id), 'fields' => array('name', 'email', 'address', 'state', 'city', 'zipcode', 'phone', 'domain_name')));
              if ($this->User->save($data)) {
                $user_email = $userResult['User']['email'];
                $fullName = $requestBody['fname'] . ' ' . $userResult['User']['lname'];

                if ($userResult['User']['is_newsletter'] != $requestBody['is_newsletter']) {
                  if ($requestBody['is_newsletter'] == 1) {
                    $template_type = 'merchant_subscribe_newsletter';
                  } else {
                    $template_type = 'merchant_unsubscribe_newsletter';
                  }
                  $this->loadModel('EmailTemplate');
                  $emailSuccess = $this->EmailTemplate->find('first',
                          array('conditions' => array('EmailTemplate.merchant_id' => $merchant_id, 'EmailTemplate.is_deleted' => 0, 'EmailTemplate.template_code' => $template_type)));
                  if ($emailSuccess) {
                    $emailData = $emailSuccess['EmailTemplate']['template_message'];
                    $emailData = str_replace('{FULL_NAME}', $fullName,
                            $emailData);
                    $subject = ucwords(str_replace('_', ' ',
                                    $emailSuccess['EmailTemplate']['template_subject']));
                    $url = "http://" . $merchantResult['Merchant']['domain_name'];
                    $merchantUrl = "<a href=" . $url . " target='_blank'>" . "www." . $merchantResult['Merchant']['domain_name'] . "</a>";
                    $emailData = str_replace('{MERCHANT_URL}', $merchantUrl,
                            $emailData);
                    $emailData = str_replace('{MERCHANT_COMPANY_NAME}',
                            $merchantResult['Merchant']['name'], $emailData);
                    $merchantAddress = $merchantResult['Merchant']['address'] . "<br>" . $merchantResult['Merchant']['city'] . ", " . $merchantResult['Merchant']['state'] . " " . $merchantResult['Merchant']['zipcode'];
                    $merchantPhone = $merchantResult['Merchant']['phone'];
                    $emailData = str_replace('{MERCHANT_ADDRESS}',
                            $merchantAddress, $emailData);
                    $emailData = str_replace('{MERCHANT_PHONE}', $merchantPhone,
                            $emailData);
                    $this->Email->to = $user_email;
                    $this->Email->subject = $subject;
                    $this->Email->from = $merchantResult['Merchant']['email'];
                    $this->set('data', $emailData);
                    $this->Email->template = 'template';
                    $this->Email->smtpOptions = array(
                        'port' => "$this->smtp_port",
                        'timeout' => '100',
                        'host' => "$this->smtp_host",
                        'username' => "$this->smtp_username",
                        'password' => "$this->smtp_password"
                    );
                    $this->Email->sendAs = 'html'; // because we like to send pretty mail
                    // $this->Email->delivery ='smtp';
                    try {
                      $this->Email->send();
                    } catch (Exception $e) {
                      
                    }
                  }
                }
                if ($userResult['User']['is_emailnotification'] != $requestBody['is_emailnotification']) {
//                            if ($requestBody['is_email_check'] == 1) {
                  if ($requestBody['is_emailnotification'] == 1) {
                    $template_type = 'merchant_subscribe_email_notification';
                  } else {
                    $template_type = 'merchant_unsubscribe_email_notification';
                  }
                  $this->loadModel('EmailTemplate');
                  $emailSuccess = $this->EmailTemplate->find('first',
                          array('conditions' => array('EmailTemplate.merchant_id' => $merchant_id, 'EmailTemplate.is_deleted' => 0, 'EmailTemplate.template_code' => $template_type)));
                  if ($emailSuccess) {
                    $emailData = $emailSuccess['EmailTemplate']['template_message'];
                    $emailData = str_replace('{FULL_NAME}', $fullName,
                            $emailData);
                    $subject = ucwords(str_replace('_', ' ',
                                    $emailSuccess['EmailTemplate']['template_subject']));
                    $emailData = str_replace('{MERCHANT_COMPANY_NAME}',
                            $merchantResult['Merchant']['name'], $emailData);
                    $url = "http://" . $merchantResult['Merchant']['domain_name'];
                    $merchantUrl = "<a href=" . $url . " target='_blank'>" . "www." . $merchantResult['Merchant']['domain_name'] . "</a>";
                    $emailData = str_replace('{MERCHANT_URL}', $merchantUrl,
                            $emailData);
                    $merchantAddress = $merchantResult['Merchant']['address'] . "<br>" . $merchantResult['Merchant']['city'] . ", " . $merchantResult['Merchant']['state'] . " " . $merchantResult['Merchant']['zipcode'];
                    $merchantPhone = $merchantResult['Merchant']['phone'];
                    $emailData = str_replace('{MERCHANT_ADDRESS}',
                            $merchantAddress, $emailData);
                    $emailData = str_replace('{MERCHANT_PHONE}', $merchantPhone,
                            $emailData);
                    $this->Email->to = $user_email;
                    $this->Email->subject = $subject;
                    $this->Email->from = $merchantResult['Merchant']['email'];
                    $this->set('data', $emailData);
                    $this->Email->template = 'template';
                    $this->Email->smtpOptions = array(
                        'port' => "$this->smtp_port",
                        'timeout' => '100',
                        'host' => "$this->smtp_host",
                        'username' => "$this->smtp_username",
                        'password' => "$this->smtp_password"
                    );
                    $this->Email->sendAs = 'html'; // because we like to send pretty mail
                    // $this->Email->delivery ='smtp';
                    try {
                      $this->Email->send();
                    } catch (Exception $e) {
                      
                    }
                  }
                }
                if ($userResult['User']['is_smsnotification'] != $requestBody['is_smsnotification']) {
//                            if ($requestBody['is_sms_check'] == 1) {
                  if ($requestBody['is_smsnotification'] == 1) {
                    $template_type = 'merchant_subscribe_sms_notification';
                  } else {
                    $template_type = 'merchant_unsubscribe_sms_notification';
                  }
                  $this->loadModel('EmailTemplate');
                  $emailSuccess = $this->EmailTemplate->find('first',
                          array('conditions' => array('EmailTemplate.merchant_id' => $merchant_id, 'EmailTemplate.is_deleted' => 0, 'EmailTemplate.template_code' => $template_type)));
                  if ($emailSuccess) {
                    $emailData = $emailSuccess['EmailTemplate']['template_message'];
                    $emailData = str_replace('{FULL_NAME}', $fullName,
                            $emailData);
                    $subject = ucwords(str_replace('_', ' ',
                                    $emailSuccess['EmailTemplate']['template_subject']));
                    $url = "http://" . $merchantResult['Merchant']['domain_name'];
                    $merchantUrl = "<a href=" . $url . " target='_blank'>" . "www." . $merchantResult['Merchant']['domain_name'] . "</a>";
                    $emailData = str_replace('{MERCHANT_URL}', $merchantUrl,
                            $emailData);
                    $emailData = str_replace('{MERCHANT_COMPANY_NAME}',
                            $merchantResult['Merchant']['name'], $emailData);
                    $merchantAddress = $merchantResult['Merchant']['address'] . "<br>" . $merchantResult['Merchant']['city'] . ", " . $merchantResult['Merchant']['state'] . " " . $merchantResult['Merchant']['zipcode'];
                    $merchantPhone = $merchantResult['Merchant']['phone'];
                    $emailData = str_replace('{MERCHANT_ADDRESS}',
                            $merchantAddress, $emailData);
                    $emailData = str_replace('{MERCHANT_PHONE}', $merchantPhone,
                            $emailData);
                    $this->Email->to = $user_email;
                    $this->Email->subject = $subject;
                    $this->Email->from = $merchantResult['Merchant']['email'];
                    $this->set('data', $emailData);
                    $this->Email->template = 'template';
                    $this->Email->smtpOptions = array(
                        'port' => "$this->smtp_port",
                        'timeout' => '100',
                        'host' => "$this->smtp_host",
                        'username' => "$this->smtp_username",
                        'password' => "$this->smtp_password"
                    );
                    $this->Email->sendAs = 'html'; // because we like to send pretty mail
                    // $this->Email->delivery ='smtp';
                    try {
                      $this->Email->send();
                    } catch (Exception $e) {
                      
                    }
                  }
                }
                $userResultDet = $this->User->find("first",
                        array("conditions" => array("User.id" => $user_id, "User.merchant_id" => $merchant_id), 'fields' => array('email', 'password', 'fname', 'id', 'lname', 'phone', 'dateOfBirth', 'country_code_id', 'is_deleted', 'is_active', 'is_newsletter', 'is_emailnotification', 'is_smsnotification')));
                $responsedata['message'] = "Profile has been updated successfully.";
                $responsedata['response'] = 1;
                $responsedata['id'] = $userResultDet['User']['id'];
                if ($userResultDet['User']['fname'] != "") {
                  $responsedata['name'] = $userResultDet['User']['fname'];
                  $responsedata['lname'] = $userResultDet['User']['lname'];
                }
                $responsedata['phone'] = $userResultDet['User']['phone'];
                $responsedata['email'] = $userResultDet['User']['email'];
                if (!empty($userResultDet['User']['dateOfBirth'])) {
                  $responsedata['dateOfBirth'] = $userResultDet['User']['dateOfBirth'];
                } else {
                  $responsedata['dateOfBirth'] = " ";
                }
                if (!empty($userResultDet['User']['country_code_id'])) {
                  $countryCode = $this->CountryCode->find('first',
                          array('conditions' => array('CountryCode.id' => $userResultDet['User']['country_code_id']), 'fields' => array('id', 'code')));

                  $responsedata['country_code_id'] = $countryCode['CountryCode']['code'];
                  //$responsedata['country_code_id'] = $userResultDet['User']['country_code_id'];
                } else {
                  $responsedata['country_code_id'] = " ";
                }

                if ($userResultDet['User']['is_newsletter']) {
                  $responsedata['is_newsletter'] = true;
                } else {
                  $responsedata['is_newsletter'] = false;
                }

                if ($userResultDet['User']['is_smsnotification']) {
                  $responsedata['is_smsnotification'] = true;
                } else {
                  $responsedata['is_smsnotification'] = false;
                }

                if ($userResultDet['User']['is_emailnotification']) {
                  $responsedata['is_emailnotification'] = true;
                } else {
                  $responsedata['is_emailnotification'] = false;
                }


                $EncryptUserID = $this->Encryption->encode($responsedata['id']);
                $EncryptmerchantID = $this->Encryption->encode($merchant_id);
                $this->tokenGenerate($EncryptUserID, $EncryptmerchantID);
                $responsedata['token'] = $EncryptUserID;
                return json_encode($responsedata);
              } else {
                $responsedata['message'] = "Profile could not be updated, please try again.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
              }
            } else {
              $responsedata['message'] = "Please fill all required fields.";
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

  /* ------------------------------------------------
    Function name:myDeliveryAddress()
    Description:This section will Show the Delivery Address of the user for Customer
    created:3/10/2016
    ----------------------------------------------------- */

  public function myDeliveryAddress() {

    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "myDelivery_add.txt",$headers);
    $responsedata = array();
    $requestBody = json_decode($requestBody, true);

        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {
        $addressArr = array();
        if (isset($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          if (empty($user_id)) {
            $user_id = 0;
            $responsedata['message'] = "Success";
            $responsedata['response'] = 1;
            $responsedata['DeliveryAddress'] = array_values($addressArr);
            return json_encode($responsedata);
          }
          $userResult = $this->User->find("first",
                  array("conditions" => array("User.id" => $user_id, "User.merchant_id" => $merchant_id), 'fields' => array('email', 'password', 'fname', 'id', 'lname', 'phone', 'dateOfBirth', 'country_code_id', 'is_deleted', 'is_active', 'is_newsletter', 'is_emailnotification', 'is_smsnotification')));
          //pr($userResult);
          //if (!empty($userResult)) {
          $roleId = array('4', '5');
          $checkaddress = $this->DeliveryAddress->find('all',
                  array('conditions' => array('DeliveryAddress.user_id' => $user_id, 'DeliveryAddress.merchant_id' => $merchant_id, 'DeliveryAddress.is_deleted' => 0, 'DeliveryAddress.is_active' => 1), 'fields' => array('DeliveryAddress.id', 'DeliveryAddress.label', 'DeliveryAddress.address', 'DeliveryAddress.city', 'DeliveryAddress.state', 'DeliveryAddress.zipcode', 'DeliveryAddress.country_code_id', 'DeliveryAddress.phone', 'DeliveryAddress.name_on_bell', 'DeliveryAddress.latitude', 'DeliveryAddress.longitude', 'DeliveryAddress.default')));

          $i = 0;
          foreach ($checkaddress as $address) {
            $addressArr[$i] = $address['DeliveryAddress'];
            if ($address['DeliveryAddress']['default'] == 1) {
              $addressArr[$i]['default'] = true;
            } else {
              $addressArr[$i]['default'] = false;
            }
            $countryCode = $this->CountryCode->find('first',
                    array('conditions' => array('CountryCode.id' => $address['DeliveryAddress']['country_code_id'])));
            if (!empty($countryCode)) {
              $addressArr[$i]['country_code_id'] = $countryCode['CountryCode']['code'];
            } else {
              $addressArr[$i]['country_code_id'] = "+1";
            }

            if ($address['DeliveryAddress']['label'] == 1) {
              $addressArr[$i]['address_type'] = 'Home Address';
            } elseif ($address['DeliveryAddress']['label'] == 2) {
              $addressArr[$i]['address_type'] = 'Work Address';
            } elseif ($address['DeliveryAddress']['label'] == 3) {
              $addressArr[$i]['address_type'] = 'Other Address';
            } elseif ($address['DeliveryAddress']['label'] == 4) {
              $addressArr[$i]['address_type'] = 'Address4';
            } elseif ($address['DeliveryAddress']['label'] == 5) {
              $addressArr[$i]['address_type'] = 'Address5';
            }
            $i++;
          }

          $responsedata['message'] = "Success";
          $responsedata['response'] = 1;
          $responsedata['DeliveryAddress'] = array_values($addressArr);
          return json_encode($responsedata);
          //} else {
          //    $responsedata['message'] = "You are not register under this merchant";
          //    $responsedata['response'] = 0;
          //    return json_encode($responsedata);
          //}
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

  public function addAddress() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "add_Address.txt", $headers);
    //$requestBody = '{"name_on_bell":"Ranjeet","label":"1","address":"Torrance","city":"CA","state":"CA","zipcode":"90501","country_code":"+91","phone":"233235688888888","default":false}';
    //$headers['user_id'] = 'NTI2';
    //$headers['merchant_id'] = 85;
    $responsedata = array();
    $requestBody = json_decode($requestBody, true);

        /*Check HTTP METHOD Request*/
        if(!$this->request->is('POST')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        if (isset($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          $roleid = array(4, 5);
          $userDet = $this->User->find('first',
                  array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));

          if (empty($user_id)) {
            if (isset($requestBody['email']) && !empty($requestBody['email'])) {
               // Checking Email parameter is String
	         $argEmail=$this->checkParameter($requestBody['email'],'string','Email'); 
                  if($argEmail['response']=='403'){                    
                    return json_encode($argEmail);
                  }
              $user_id = 0;
            } else {
              $responsedata['message'] = "Please enter an email.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            }
          } else {
            $requestBody['email'] = null;
            if (empty($requestBody['label'])) {
              $responsedata['message'] = "Please select address type.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            }else{
              // Checking Address type parameter is integer
              $argAddressLabel = $this->checkParameter($requestBody['label'], 'integer','Address type'); 
                if ($argAddressLabel['response'] == '403') {
                  return json_encode($argAddressLabel);
                }
            }
          }

          if (empty($requestBody['name_on_bell'])) {
            $responsedata['message'] = "Please enter name.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
          }else{
            // Checking Name parameter is String
	         $argName=$this->checkParameter($requestBody['name_on_bell'],'string','Name'); 
                  if($argName['response']=='403'){                    
                    return json_encode($argName);
                  } 
          }
          if (empty($requestBody['address'])) {
            $responsedata['message'] = "Please enter address.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
          }else{
            // Checking First Name parameter is String
	         $argAddress=$this->checkParameter($requestBody['address'],'string','Address'); 
                  if($argAddress['response']=='403'){                    
                    return json_encode($argAddress);
                  } 
          }
          if (empty($requestBody['city'])) {
            $responsedata['message'] = "Please enter city.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
          }else{
            // Checking City parameter is String
	         $argCity=$this->checkParameter($requestBody['city'],'string','City'); 
                  if($argCity['response']=='403'){                    
                    return json_encode($argCity);
                  } 
          }
          if (empty($requestBody['state'])) {
            $responsedata['message'] = "Please enter state.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
          }else{
            // Checking State parameter is String
	         $argState=$this->checkParameter($requestBody['state'],'string','State'); 
                  if($argState['response']=='403'){                    
                    return json_encode($argState);
                  } 
          }
          if (empty($requestBody['zipcode'])) {
            $responsedata['message'] = "Please enter zipcode.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
          }else{
            // Checking Zipcode parameter is String
	         $argZipcode=$this->checkParameter($requestBody['zipcode'],'string','Zipcode'); 
                  if($argZipcode['response']=='403'){                    
                    return json_encode($argZipcode);
                  } 
          }
          if (empty($requestBody['phone'])) {
            $responsedata['message'] = "Please enter phone number.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
          }else{
            // Checking Phone parameter is String
	         $argPhone=$this->checkParameter($requestBody['phone'],'string','Phone'); 
                  if($argPhone['response']=='403'){                    
                    return json_encode($argPhone);
                  }
          }

          if (strlen($requestBody['phone']) > 15) {
            $responsedata['message'] = "Phone number should not be greater then 15 digits.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
          }
          if (strlen($requestBody['zipcode']) > 10) {
            $responsedata['message'] = "Zipcode should not be greater then 10 characters.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
          }
          $number = "";
          if (!empty($requestBody['phone'])) {
            $phone = preg_replace("/[^0-9]/", "", $requestBody['phone']);
            $number = "(" . substr($phone, 0, 3) . ') ' .
                    substr($phone, 3, 3) . '-' .
                    substr($phone, 6);
          }
          $requestBody['phone'] = $number;
          $zipCode = trim($requestBody['zipcode'], " ");
          $stateName = trim($requestBody['state'], " ");
          $cityName = strtolower($requestBody['city']);
          $cityName = trim(ucwords($cityName));
          $address = trim(ucwords($requestBody['address']));
          $dlocation = $address . " " . $cityName . " " . $stateName . " " . $zipCode;
          $adjuster_address2 = str_replace(' ', '+', $dlocation);
          $geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address=' . $adjuster_address2 . '&sensor=false');
          $output = json_decode($geocode);
          $requestBody['user_id'] = $user_id;
          $requestBody['merchant_id'] = $merchant_id;
          $latitude = "";
          $longitude = "";
          if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
            
          } else {
            $latitude = @$output->results[0]->geometry->location->lat;
            $longitude = @$output->results[0]->geometry->location->lng;
            $requestBody['latitude'] = $latitude;
            $requestBody['longitude'] = $longitude;
          }

          ///Check Zone
          $DelAddress['DeliveryAddress']['longitude'] = $longitude;
          $DelAddress['DeliveryAddress']['latitude'] = $latitude;
          $zoneData = $this->Common->addressInZone($DelAddress,
                  $requestBody['store_id'], "mob");
          if (empty($zoneData)) {
            $response['message'] = "Order can't be delivered at this address.";
            $response['response'] = 0;
            return json_encode($response);
          }
          ///Check Zone


          $countryID = $requestBody['country_code'];
          if(!empty($countryID)){
            // Checking Country Code parameter is String
	         $argCountry=$this->checkParameter($countryID,'string','Country Code'); 
                  if($argCountry['response']=='403'){                    
                    return json_encode($argCountry);
                  }     
          }
           
          
          $countryCodeID = $this->CountryCode->find('first',
                  array('fields' => array('id', 'code'), 'conditions' => array('CountryCode.code' => $countryID)));
          if (!empty($countryCodeID)) {
            $requestBody['country_code_id'] = $countryCodeID['CountryCode']['id'];
          } else {
            $requestBody['country_code_id'] = 1;
          }

          unset($requestBody['country_code']);
          // Checking status id parameter is boolean
          if (!empty($requestBody['default'])) {
                $argDefaultAddress = $this->checkParameter($requestBody['default'], 'boolean','Default address'); 
                if ($argDefaultAddress['response'] == '403') {
                  return json_encode($argDefaultAddress);
                }
          }

          if ($requestBody['default'] == 1) {
            $this->DeliveryAddress->updateAll(array('DeliveryAddress.default' => 0),
                    array('DeliveryAddress.user_id' => $user_id));
          } else {
            $requestBody['default'] = 0;
          }
          
          if (!empty($user_id)) {
            $address = $this->DeliveryAddress->find('first',
                    array('conditions' => array('DeliveryAddress.user_id' => $user_id, 'DeliveryAddress.label' => $requestBody['label'])));
            if (!empty($address)) {
              $requestBody['id'] = $address['DeliveryAddress']['id'];
            }
          }

          $this->DeliveryAddress->create();
          $result = $this->DeliveryAddress->save($requestBody);
          $address_id = 0;
          if (!empty($address['DeliveryAddress']['id'])) {
            $address_id = $address['DeliveryAddress']['id'];
            $msg = "Address has been added successfully.";
          } else {
            $address_id = $this->DeliveryAddress->getLastInsertID();
            $msg = "Address has been added successfully.";
          }

          if ($result) {
            $responsedata['data'] = $zoneData['Zone'];
            $responsedata['message'] = $msg;
            $responsedata['response'] = 1;
            $responsedata['address_id'] = $address_id;
            return json_encode($responsedata);
          } else {
            $responsedata['message'] = "Delivery address could not be added, please try again.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
          }
        } else {
          $responsedata['message'] = "Please login or continue as a guest.";
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

  public function updateAddress() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "update_address.txt",
            $headers);
//        $requestBody = '{"address_id":"248","name_on_bell":"Ranjeet","label":"1","address":"Hno 54","city":"Chandigarh","state":"Chandigarh","zipcode":"160036","country_code_id":"1","phone":"879562356","default":"1"}';
//        $headers['user_id'] = 'Mjg2';
//        $headers['merchant_id'] = 1;
    $responsedata = array();
    $requestBody = json_decode($requestBody, true);

        /*Check HTTP METHOD Request*/
        if(!$this->request->is('PUT')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        if (isset($headers['user_id']) && !empty($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          $roleid = array(4, 5);
          $userDet = $this->User->find('first',
                  array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
          if (!empty($userDet)) {
            if (empty($requestBody['address_id'])) {
              $responsedata['message'] = "Please select address.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            } else {
              // Checking Address type parameter is integer
              $argAddress = $this->checkParameter($requestBody['address_id'], 'integer','Address'); 
                if ($argAddress['response'] == '403') {
                  return json_encode($argAddress);
                }
              }
            
            if (empty($requestBody['label'])) {
              $responsedata['message'] = "Please select address type.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            }else{
              // Checking Address type parameter is integer
              $argAddressLabel = $this->checkParameter($requestBody['label'], 'integer','Address type'); 
                if ($argAddressLabel['response'] == '403') {
                  return json_encode($argAddressLabel);
                }
            }
            if (empty($requestBody['name_on_bell'])) {
              $responsedata['message'] = "Please enter name.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            }else{
            // Checking Name parameter is String
	         $argName=$this->checkParameter($requestBody['name_on_bell'],'string','Name'); 
                  if($argName['response']=='403'){                    
                    return json_encode($argName);
                  } 
            }
            if (empty($requestBody['address'])) {
              $responsedata['message'] = "Please enter address.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            }else{
            // Checking First Name parameter is String
	         $argAddress=$this->checkParameter($requestBody['address'],'string','Address'); 
                  if($argAddress['response']=='403'){                    
                    return json_encode($argAddress);
                  } 
          }
            if (empty($requestBody['city'])) {
              $responsedata['message'] = "Please enter city.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            }else{
            // Checking City parameter is String
	         $argCity=$this->checkParameter($requestBody['city'],'string','City'); 
                  if($argCity['response']=='403'){                    
                    return json_encode($argCity);
                  } 
          }
            if (empty($requestBody['state'])) {
              $responsedata['message'] = "Please enter state.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            }else{
            // Checking State parameter is String
	         $argState=$this->checkParameter($requestBody['state'],'string','State'); 
                  if($argState['response']=='403'){                    
                    return json_encode($argState);
                  } 
          }
            if (empty($requestBody['zipcode'])) {
              $responsedata['message'] = "Please enter zipcode.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            }else{
            // Checking Zipcode parameter is String
	         $argZipcode=$this->checkParameter($requestBody['zipcode'],'string','Zipcode'); 
                  if($argZipcode['response']=='403'){                    
                    return json_encode($argZipcode);
                  } 
          }
            if (empty($requestBody['phone'])) {
              $responsedata['message'] = "Please enter phone number.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            }else{
            // Checking Phone parameter is String
	         $argPhone=$this->checkParameter($requestBody['phone'],'string','Phone'); 
                  if($argPhone['response']=='403'){                    
                    return json_encode($argPhone);
                  }
          }
            if (strlen($requestBody['phone']) > 15) {
              $responsedata['message'] = "Phone number should not be greater then 15 digits.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            }
            if (strlen($requestBody['zipcode']) > 10) {
              $responsedata['message'] = "Zipcode should not be less greater 10 characters.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            }

            $resultAddress = $this->DeliveryAddress->find('first',
                    array('conditions' => array('DeliveryAddress.id' => $requestBody['address_id'], 'DeliveryAddress.is_active' => 1, 'DeliveryAddress.is_deleted' => 0)));

            $number = "";
            if (!empty($requestBody['phone'])) {
              $phone = preg_replace("/[^0-9]/", "", $requestBody['phone']);
              $number = "(" . substr($phone, 0, 3) . ') ' .
                      substr($phone, 3, 3) . '-' .
                      substr($phone, 6);
            }
            $requestBody['phone'] = $number;
            $zipCode = trim($requestBody['zipcode'], " ");
            $stateName = trim($requestBody['state'], " ");
            $cityName = strtolower($requestBody['city']);
            $cityName = trim(ucwords($cityName));
            $address = trim(ucwords($requestBody['address']));
            $dlocation = $address . " " . $cityName . " " . $stateName . " " . $zipCode;
            $adjuster_address2 = str_replace(' ', '+', $dlocation);
            $geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address=' . $adjuster_address2 . '&sensor=false');
            $output = json_decode($geocode);
            $requestBody['user_id'] = $user_id;
            $requestBody['id'] = $resultAddress['DeliveryAddress']['id'];
            $requestBody['merchant_id'] = $merchant_id;

            $latitude = "";
            $longitude = "";

            if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
              
            } else {
              $latitude = @$output->results[0]->geometry->location->lat;
              $longitude = @$output->results[0]->geometry->location->lng;
              $requestBody['latitude'] = $latitude;
              $requestBody['longitude'] = $longitude;
            }

            ///Check Zone
            $DelAddress['DeliveryAddress']['longitude'] = $longitude;
            $DelAddress['DeliveryAddress']['latitude'] = $latitude;

            $zoneData = $this->Common->addressInZone($DelAddress,
                    $requestBody['store_id'], "mob");
            if (empty($zoneData)) {
              $response['message'] = "Order can't be delivered at this address.";
              $response['response'] = 0;
              return json_encode($response);
            }
            ///Check Zone

            if ($requestBody['default'] == 1) {
              $this->DeliveryAddress->updateAll(array('DeliveryAddress.default' => 0),
                      array('DeliveryAddress.user_id' => $user_id));
            } else {
              $requestBody['default'] = 0;
            }
            $countryCode = $this->CountryCode->find('first',
                    array('conditions' => array('CountryCode.code' => $requestBody['country_code_id'])));
            if (!empty($countryCode)) {
              $requestBody['country_code_id'] = $countryCode['CountryCode']['id'];
            } else {
              $requestBody['country_code_id'] = "1";
            }
            $result_sucess = $this->DeliveryAddress->save($requestBody);

            if ($result_sucess) {
              $responsedata['data'] = $zoneData['Zone'];
              $responsedata['message'] = "Delivery address has been updated successfully.";
              $responsedata['response'] = 1;
              return json_encode($responsedata);
            } else {
              $responsedata['message'] = "Delivery address could not be updated, please try again.";
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

  public function deleteAddress() {

    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "delete_address.txt",
            $headers);
    //$requestBody = '{"address_id":247}';
    // $headers['user_id'] = 'Mjg2';
    //$headers['merchant_id'] = 1;
    $responsedata = array();
    $requestBody = json_decode($requestBody, true);

        /*Check HTTP METHOD Request*/
        if(!$this->request->is('DELETE')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        if (isset($headers['user_id']) && !empty($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          $roleid = array(4, 5);
          $userDet = $this->User->find('first',
                  array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
          if (!empty($userDet)) {

            if (isset($requestBody['address_id']) && !empty($requestBody['address_id'])) {
              // Checking Address type parameter is integer
              $argAddress = $this->checkParameter($requestBody['address_id'], 'integer','Address'); 
                if ($argAddress['response'] == '403') {
                  return json_encode($argAddress);
                }
              $requestBody['id'] = $requestBody['address_id'];
              $requestBody['is_deleted'] = 1;
              if ($this->DeliveryAddress->save($requestBody)) {
                $responsedata['message'] = "Delivery address has been deleted successfully.";
                $responsedata['response'] = 1;
                return json_encode($responsedata);
              } else {
                $responsedata['message'] = "Delivery address could not be deleted, please try again.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
              }
            } else {
              $responsedata['message'] = "Please select an address to delete.";
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

  public function allReviews() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    //$requestBody = '{"store_id":2}';
    $responsedata = array();
    $requestBody['store_id']=$_GET['store_id'];
    $this->Webservice->webserviceLog($requestBody, "all_review.txt", $headers);
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {
        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
          $storeId = $requestBody['store_id'];
          $storeResult = $this->Store->find('first',
                  array('conditions' => array('Store.id' => $storeId, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
          if (!empty($storeResult)) {
            $this->OrderItem->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name', 'image')))),
                    false);
            $this->StoreReview->bindModel(array(
                'hasMany' => array(
                    'StoreReviewImage' => array(
                        'className' => 'StoreReviewImage',
                        'foreignKey' => 'store_review_id',
                        'fields' => array('id', 'image'),
                        'type' => 'INNER',
                        'conditions' => array('StoreReviewImage.is_deleted' => 0, 'StoreReviewImage.is_active' => 1, 'StoreReviewImage.store_id' => $storeId)
                    ))
                    ), false);
            $this->StoreReview->bindModel(array('belongsTo' => array('User' => array('fields' => array('fname', 'lname')), 'OrderItem' => array('foreignKey' => 'order_item_id', 'fields' => array('item_id')))),
                    false);
            $allReviews = $this->StoreReview->find('all',
                    array('recursive' => 2, 'order' => array('StoreReview.review_rating' => 'DESC', 'StoreReview.created' => 'DESC'), 'conditions' => array('StoreReview.store_id' => $storeId, 'StoreReview.is_active' => 1, 'StoreReview.is_deleted' => 0, 'StoreReview.is_approved' => 1)));
            $store_url = $storeResult['Store']['store_url'];
            $protocol = 'http://';
            if (isset($_SERVER['HTTPS'])) {
              if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                $protocol = 'https://';
              }
            }
            $reviewArr = array();
            //pr($allReviews);
            if (!empty($allReviews)) {
              $i = 0;
              foreach ($allReviews as $reviewsAll) {
                $reviewArr[$i]['review_id'] = $reviewsAll['StoreReview']['id'];
                if (!empty($reviewsAll['StoreReview']['review_rating'])) {
                  $reviewArr[$i]['review_rating'] = $reviewsAll['StoreReview']['review_rating'];
                } else {
                  $reviewArr[$i]['review_rating'] = 0;
                }
                if (!empty($reviewsAll['StoreReview']['review_rating'])) {
                  $reviewArr[$i]['review_comment'] = $reviewsAll['StoreReview']['review_comment'];
                } else {
                  $reviewArr[$i]['review_comment'] = "";
                }


                if (!empty($reviewsAll['User']['fname'])) {
                  $reviewArr[$i]['user_name'] = $reviewsAll['User']['fname'] . " " . $reviewsAll['User']['lname'];
                } else {
                  $reviewArr[$i]['user_name'] = "Anonymous User";
                }

                if (!empty($reviewsAll['OrderItem']['item_id'])) {
                  $reviewArr[$i]['item_name'] = $reviewsAll['OrderItem']['Item']['name'];

                  if (!empty($reviewsAll['OrderItem']['Item']['image'])) {
                    $reviewArr[$i]['img_url'] = $protocol . $store_url . "/MenuItem-Image/" . $reviewsAll['OrderItem']['Item']['image'];
                  } else {
                    $reviewArr[$i]['img_url'] = $protocol . $store_url . "/storeReviewImage/" . 'no_image.jpeg';
                  }
                } else {
                  $reviewArr[$i]['item_name'] = "";
                  $reviewArr[$i]['img_url'] = "";
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

                $dateTime = $this->Webservice->storeTimezone($storeId,
                        $reviewsAll['StoreReview']['created'], true);
                $reviewArr[$i]['created'] = $dateTime;
                $i++;
              }
            } else {
              $reviewArr = array();
            }
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
    Function name:checkOut()
    Description:This section is Used to Validate the orederdetails, Carts and Category Items
    created:17/10/2016
    ----------------------------------------------------- */

  public function checkOut() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "check_out.txt", $headers);
    //$requestBody = '{"is_future_order":"2","total_amount":159.18,"sub_total_price":154.0,"payment_method":{"expiry":"","address":"","payment_type":"Cash","city":"","cc_number":"","cvv":"","fname":"","lastname":"","zip_code":"","card_type":"","state":""},"extended_discount":0.0,"delivery_fee":0.00,"store_id":108,"order_details":{"phone":"2545875566","country_code_id":"+1","order_type":"Delivery","time":"10:30","address_id":"592","date":"27-12-2016","email":"maheshku@mailinator.com","name_on_bell":"mahesh Smartdata"},"service_fee":0.00,"total_tax":5.18,"coupon_code":"","coupon_discount":0.0,"cart_item_list":[{"quantity":"2","selectedSubPreferencesModels":[],"total_price":"2.0","tax":".04","category_id":"828","item_id":"5119","comment":"","selectedSubAddonsModels":[],"Offers":[],"item_tax_value":"2","free_units":"0","size_id":"","ExtendedPromo":[]},{"quantity":"3","selectedSubPreferencesModels":[],"total_price":"60.0","tax":"5.10","category_id":"836","item_id":"5102","comment":"","selectedSubAddonsModels":[],"Offers":[],"item_tax_value":"8.5","free_units":"0","size_id":"334","ExtendedPromo":[]},{"quantity":"2","selectedSubPreferencesModels":[],"total_price":"90.0","tax":".00","category_id":"824","item_id":"4972","comment":"","selectedSubAddonsModels":[],"Offers":[],"item_tax_value":"0","free_units":"0","size_id":"","ExtendedPromo":[]},{"quantity":"2","selectedSubPreferencesModels":[],"total_price":"2.0","tax":".04","category_id":"836","item_id":"5118","comment":"","selectedSubAddonsModels":[],"Offers":[],"item_tax_value":"2","free_units":"0","size_id":"334","ExtendedPromo":[]}]}';
    //$headers['user_id'] = 'NTI2';
    //$headers['merchant_id'] = 85;
    $responsedata = array();
    $requestBody = json_decode($requestBody, true);
    //pr($requestBody);
    //die;
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('POST')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        if (isset($headers['user_id'])) {
          $roleid = array(4, 5);
          $user_id = $this->Encryption->decode($headers['user_id']);
          if (empty($user_id)) {
            $user_id = 0;
          }
          $userDet = $this->User->find('first',
                  array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
          //if (!empty($userDet)) {

          if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
            $storeId = $requestBody['store_id'];
             // Checking Store id parameter is Integer
                 $argStore = $this->checkParameter($storeId, 'integer', 'Store'); 
                     if ($argStore['response'] == '403') {
                       return json_encode($argStore);
             }

             if (isset($requestBody['total_amount']) && !empty($requestBody['total_amount'])) {    
             // Checking Store id parameter is Integer
                 $argTotalAmount = $this->checkParameter($requestBody['total_amount'], 'double', 'Total Amount'); 
                     if ($argTotalAmount['response'] == '403') {
                       return json_encode($argTotalAmount);
               }
             }    
             if (isset($requestBody['sub_total_price']) && !empty($requestBody['sub_total_price'])) {
               // Checking Store id parameter is Integer
               $argSubTotalAmount = $this->checkParameter($requestBody['sub_total_price'],'double', 'SubTotal Amount');
               if ($argSubTotalAmount['response'] == '403') {
                 return json_encode($argSubTotalAmount);
               }
             }
             
             if (isset($requestBody['extended_discount']) && !empty($requestBody['extended_discount'])) {
               // Checking Store id parameter is Integer
               $argExtendedOffer = $this->checkParameter($requestBody['extended_discount'],'double', 'Extended offer amount');
               if ($argExtendedOffer['response'] == '403') {
                 return json_encode($argExtendedOffer);
               }
             }
             if (isset($requestBody['delivery_fee']) && !empty($requestBody['delivery_fee'])) {
               // Checking Store id parameter is Integer
               $argDeliveryFee = $this->checkParameter($requestBody['delivery_fee'],'double', 'Delivery fee amount');
               if ($argDeliveryFee['response'] == '403') {
                 return json_encode($argDeliveryFee);
               }
             }
             if (isset($requestBody['service_fee']) && !empty($requestBody['service_fee'])) {
               // Checking Store id parameter is Integer
               $argServiceFee = $this->checkParameter($requestBody['service_fee'],'double', 'Service fee amount');
               if ($argServiceFee['response'] == '403') {
                 return json_encode($argServiceFee);
               }
             }
             if (isset($requestBody['sub_total_price']) && !empty($requestBody['sub_total_price'])) {
               // Checking Store id parameter is Integer
               $argSubTotalAmount = $this->checkParameter($requestBody['sub_total_price'],'double', 'SubTotal Amount');
               if ($argSubTotalAmount['response'] == '403') {
                 return json_encode($argSubTotalAmount);
               }
             }
             if (isset($requestBody['total_tax']) && !empty($requestBody['total_tax'])) {
               // Checking Store id parameter is Integer
               $argTaxAmount = $this->checkParameter($requestBody['total_tax'],'double', 'Tax amount');
               if ($argTaxAmount['response'] == '403') {
                 return json_encode($argTaxAmount);
               }
             }
             if (isset($requestBody['coupon_discount']) && !empty($requestBody['coupon_discount'])) {
               // Checking Store id parameter is Integer
               $argCouponDiscount = $this->checkParameter($requestBody['sub_total_price'],'double', 'Coupon discount Amount');
               if ($argCouponDiscount['response'] == '403') {
                 return json_encode($argCouponDiscount);
               }
             }
             
            $storeResult = $this->Store->find('first',
                    array('conditions' => array('Store.id' => $storeId, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url', 'Store.nzgateway_apikey', 'Store.minimum_order_price', 'Store.minimum_takeaway_price')));
            //pr($storeResult);
            if (!empty($storeResult)) {
              /* --------------------------- Order Detail check function------------ */
              if (isset($requestBody['is_future_order'])) {
                if (empty($requestBody['is_future_order'])) {
                  $requestBody['is_future_order'] = 2;
                }else{
                  // Checking Store id parameter is Integer
                $argFutureOrder = $this->checkParameter($requestBody['is_future_order'], 'integer', 'Future Order'); 
                    if ($argFutureOrder['response'] == '403') {
                      return json_encode($argFutureOrder);
                    }
                }
              } else {
                $requestBody['is_future_order'] = 2;
              }

              if ($requestBody['is_future_order'] == 1) {
                $paymentType = 1;
              } else {
                if (isset($requestBody['payment_method']) && !empty($requestBody['payment_method'])) {
                  $paymentType = $requestBody['payment_method']['payment_type'];
                  // Checking Email parameter is String
                    $argPaymentType=$this->checkParameter($requestBody['payment_type'],'string','Payment type'); 
                         if($argPaymentType['response']=='403'){                    
                           return json_encode($argPaymentType);
                         }
                  //Checking is store is has Holiday or not
                  $storePaymentCheck = $this->Webservice->storePaymentCheck($storeId,
                          $paymentType);
                  if (!empty($storePaymentCheck)) {
                    return json_encode($storePaymentCheck);
                  }
                } else {
                  $responsedata['message'] = "Please select a payment type.";
                  $responsedata['response'] = 0;
                  return json_encode($responsedata);
                }
              }
              if (isset($requestBody['order_details']) && !empty($requestBody['order_details'])) {
                $time = $requestBody['order_details']['time'];
                $date = $requestBody['order_details']['date'];
                // Checking Date parameter is Date
                $argDate = $this->checkParameter($date, 'date','Date','d-m-Y'); 
                 if ($argDate['response'] == '403') {
                   return json_encode($argDate);
                 }
                $address_id = $requestBody['order_details']['address_id'];
                $order_type = $requestBody['order_details']['order_type'];
                if(!empty($order_type)){
                  // Checking Order type parameter is String
                  $argOrderType=$this->checkParameter($order_type,'string','Order type'); 
                       if($argOrderType['response']=='403'){                    
                         return json_encode($argOrderType);
                       }
                }
                
                //This function is used to check is Wheater store accepting order on that day and Time.
                $storeOrderCheck = $this->Webservice->storeOrderCheck($storeId,
                        $order_type, $time, $date);
                if (!empty($storeOrderCheck)) {
                  if ($storeOrderCheck['response'] == 0)
                    return json_encode($storeOrderCheck);
                }
                //Checking is store is has Holiday or not
                $storeCheck = $this->Webservice->storeCheck($storeId,
                        $order_type);
                if (!empty($storeCheck)) {
                  return json_encode($storeCheck);
                }

                //Checking is store is has Holiday or not
                $storeHolidayCheck = $this->Webservice->storeHolidayCheck($storeId,
                        $time, $date);

                if (!$storeHolidayCheck) {
//                                        echo "storeHolidayCheck API" . "<br>";
                  $responsedata['message'] = "The Store is closed, due to holiday.";
                  $responsedata['response'] = 0;
                  return json_encode($responsedata);
                }

//                                    //Checking is store has Blackout days
                $storeStoreBlackOutCheck = $this->Webservice->storeBlackOutCheck($storeId,
                        $time, $date,
                        $requestBody['order_details']['order_type']);
                if (!empty($storeStoreBlackOutCheck)) {

                  if ($storeStoreBlackOutCheck['response'] == 0)
                    return json_encode($storeStoreBlackOutCheck);
                }

                //Checking is store is opened or closed at that Time
                $storeStoreAvailabilityCheck = $this->Webservice->storeAvailabilityCheck($storeId,
                        $time, $date);
//                                    echo $storeStoreAvailabilityCheck."<br>";
                if (!$storeStoreAvailabilityCheck) {
//                                        echo "storeStoreAvailabilityCheck API" . "<br>";
                  $responsedata['message'] = "Store is not accepting order this Time";
                  $responsedata['response'] = 0;
                  return json_encode($responsedata);
                }
                //Checking is store is opened or closed at that Time
                if ($order_type == "Delivery") {
                  $deliveryAddressCheck = $this->Webservice->deliveryAddressCheck($storeId,
                          $merchant_id, $address_id);
                  if (!$deliveryAddressCheck) {
//                                        echo "deliveryAddressCheck API" . "<br>";
                    $responsedata['message'] = "Delivery address is not valid.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                  }
                } else {
                  if (empty($user_id)) {
                    $data['DeliveryAddress']['name_on_bell'] = trim($requestBody['order_details']['name_on_bell']);
                    $data['DeliveryAddress']['user_id'] = 0;
                    $data['DeliveryAddress']['merchant_id'] = $merchant_id;
                    $data['DeliveryAddress']['phone'] = $requestBody['order_details']['phone'];
                    $data['DeliveryAddress']['email'] = strtolower(trim($requestBody['order_details']['email']));
                    $countryId = $this->CountryCode->find('first',
                            array('conditions' => array('CountryCode.code' => $requestBody['order_details']['country_code_id']), 'fields' => array('id')));
                    if (!empty($countryId)) {
                      $data['DeliveryAddress']['country_code_id'] = $countryId['CountryCode']['id'];
                    } else {
                      $data['DeliveryAddress']['country_code_id'] = "1";
                    }
                    if ($this->DeliveryAddress->save($data)) {
                      $address_id = $this->DeliveryAddress->getLastInsertId();
                    } else {
                      $address_id = 0;
                    }
                    $requestBody['order_details']['address_id'] = $address_id;
                  }
                }
              } else {
                $responsedata['message'] = "Please select order details.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
              }
              /* ------------------- Order Detail check function End-------------------------------- */
              /*-------------------- Checking is Category is active or Not------------------- */
              if (isset($requestBody['coupon_code']) && !empty($requestBody['coupon_code'])) {
                // Checking Email parameter is String
                  $argCouponCode=$this->checkParameter($requestBody['coupon_code'],'string','Coupon code'); 
                       if($argCouponCode['response']=='403'){                    
                         return json_encode($argCouponCode);
                       }
                $coupon_code = $requestBody['coupon_code'];
                $checkCouponCode = $this->Webservice->checkCouponCode($storeId,
                        $merchant_id, $coupon_code, $user_id);
                if (empty($checkCouponCode['discount'])) {
                  return json_encode($checkCouponCode);
                }
              }
              /*------------------------ Checking is Item is active or Not------------------------ */


              /*--------------------- Category, Item check function Start---------------------------- */

              if (isset($requestBody['cart_item_list']) && !empty($requestBody['cart_item_list'])) {
                $itemPriceArr = array();
                foreach ($requestBody['cart_item_list'] as $key => $itemDetails) {
                  if (empty($itemDetails['quantity'])) {
                    $itemDetails['quantity'] = 1;
                  }
                  // Checking Store id parameter is Integer
                  $argQuantity = $this->checkParameter($itemDetails['quantity'], 'integer', 'Quantity'); 
                    if ($argQuantity['response'] == '403') {
                      return json_encode($argQuantity);
                    }
                    
                  //pr($itemDetails);
                  $catId = $itemDetails['category_id'];
                  // Checking Category id parameter is Integer
                  $argCategory = $this->checkParameter($itemDetails['category_id'], 'integer', 'Category'); 
                    if ($argCategory['response'] == '403') {
                      return json_encode($argCategory);
                    }
                    
                  $itemId = $itemDetails['item_id'];
                  // Checking Item id parameter is Integer
                  $argItem = $this->checkParameter($itemDetails['item_id'], 'integer', 'Item'); 
                    if ($argItem['response'] == '403') {
                      return json_encode($argItem);
                    }
                    
                  $itemQuantity = $itemDetails['quantity'];
                  
                  if (isset($itemDetails['size_id']) && !empty($itemDetails['size_id'])) {
                    $sizeId = $itemDetails['size_id'];
                    // Checking Item id parameter is Integer
                    $argSize = $this->checkParameter($sizeId, 'integer', 'Size'); 
                      if ($argSize['response'] == '403') {
                        return json_encode($argSize);
                      }
                  } else {
                    $sizeId = 0;
                  }
                  /*---------------- Checking is Category is active or Not------------------- */
                  if (!empty($catId)) {
                    $checkCategory = $this->Webservice->checkCategory($storeId,
                            $merchant_id, $catId);
                    if (!$checkCategory) {
//                                            echo "checkCategory API" . "<br>";
                      $responsedata['message'] = "No active category found.";
                      $responsedata['response'] = 0;
                      return json_encode($responsedata);
                    }

                    /*----------------- Checking is Item is active or Not------------------------ */
                    $checkItem = $this->Webservice->checkItem($storeId,
                            $merchant_id, $catId, $itemId);
                    if (!$checkItem) {
//                                            echo "checkItem API" . "<br>";
                      $responsedata['message'] = "No active item found.";
                      $responsedata['response'] = 0;
                      return json_encode($responsedata);
                    }

                    if ($order_type == "Delivery") {
                      if ($checkItem['Item']['is_deliverable'] == 0) {
                        $responsedata['message'] = $checkItem['Item']['name'] . " is not deliverable.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                      }
                    }
                  }
                  /*------------------------- Checking is Item Price with Size ID------------------------- */
                  if (!empty($itemId)) {
                    $checkItemPrice = $this->Webservice->checkItemPrice($storeId,
                            $merchant_id, $itemId, $sizeId);
                    if (!$checkItemPrice) {
//                                            echo "checkItemPrice API" . "<br>";
                      $responsedata['message'] = "No active item found.";
                      $responsedata['response'] = 0;
                      return json_encode($responsedata);
                    }
                  }
                  /*---------------------- Check Addons is active or not function Start------------------ */
                  if (!empty($itemDetails['selectedSubAddonsModels'])) {
                    foreach ($itemDetails['selectedSubAddonsModels'] as $p => $subAddon) {
                      $sub_addonId = $subAddon['sub_addon_id'];
                      $addonsize_id = $subAddon['sub_addons_size_id'];
                      //Checking is Item Price with Size ID
                      $checkSubAddon = $this->Webservice->checkSubAddon($storeId,
                              $merchant_id, $itemId, $sub_addonId, $addonsize_id);
                      //if (!$checkSubAddon) {
                      //echo "checkSubAddon API" . "<br>";
                      //    $responsedata['message'] = "No active sub-addon found.";
                      //    $responsedata['response'] = 0;
                      //    return json_encode($responsedata);
                      //}
                    }
                  }
                  /* -------------------- check Addons is active or not function End-------------------- */
                  /* -------------------- check subpreference is active or not function Start----------- */
                  if (!empty($itemDetails['selectedSubPreferencesModels'])) {
                    foreach ($itemDetails['selectedSubPreferencesModels'] as $p => $subpreference) {
                      $subprefernce_id = $subpreference['subprefernce_id'];
                      //Checking is Item Price with Size ID
                      $checkSubpreference = $this->Webservice->checkSubpreference($storeId,
                              $merchant_id, $subprefernce_id);
                      if (!$checkSubpreference) {
                        $responsedata['message'] = "No active sub-preference found.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                      }
                    }
                  }
                  /*----------------------- check subpreference is active or not function End--------------- */
                  /*----------------------- Offer check function Start--------------------------------------- */
                  if (!empty($itemDetails['Offers'])) {
                    foreach ($itemDetails['Offers'] as $o => $offer) {
                      $offerItem_id = $offer['item_id'];
                      $offer_id = $offer['offer_id'];
                      if (!empty($offer['size_id'])) {
                        $offerSize_id = $offer['size_id'];
                      } else {
                        $offerSize_id = 0;
                      }
                      $itemQuantity = $itemDetails['quantity'];
                      $checkOffer = $this->Webservice->checkOffer($storeId,
                              $merchant_id, $offerItem_id, $offer_id,
                              $offerSize_id, $date, $time);
                      if (!$checkOffer) {
                        $responsedata['message'] = "No active offer found.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                      }
                      foreach ($offer['offered_items'] as $offered_items) {
                        $offered_id = $offered_items['Offered_id'];
                        $offeredItem_id = $offered_items['offered_item_id'];
                        $offeredSize_id = $offered_items['size_id'];
                        $checkOfferDetail = $this->Webservice->checkOfferDetail($storeId,
                                $merchant_id, $offeredItem_id, $offered_id,
                                $offer_id, $offeredSize_id);
                        if (!$checkOfferDetail) {
//                                                    echo "checkOfferDetail API" . "<br>";
                          $responsedata['message'] = "No active offer found.";
                          $responsedata['response'] = 0;
                          return json_encode($responsedata);
                        }
                      }
                    }
                  }

                  /* --------------- Offer check function End---------------------- */
                  /* --------------- Extended Offer check function Start--------- */
                  if (!empty($itemDetails['ExtendedPromo'])) {
                    foreach ($itemDetails['ExtendedPromo'] as $o => $extendedOffer) {
                      $extendedOfferItem_id = $extendedOffer['item_id'];
                      $extendedOffer_id = $extendedOffer['promo_id'];
                      $extendedOfferunit_id = $extendedOffer['unit'];
                      $extendedOfferCheck = $this->Webservice->extendedOfferCheck($storeId,
                              $merchant_id, $extendedOfferItem_id,
                              $extendedOffer_id, $extendedOfferunit_id, $date);
                      if (!$extendedOfferCheck) {
//                                                    echo "extendedOfferCheck API" . "<br>";
                        $responsedata['message'] = "No active extended offer found.";
                        $responsedata['response'] = 0;
                        return json_encode($responsedata);
                      }
                    }
                  }

                  /*----------------------------Extended Offer check function End------------------------------------ */

                  /* ------------------------Price Check function Start------------------------------------ */

                  if (!empty($catId) && !empty($itemId)) {
                    $itemPrice = $this->Webservice->getItemPrice($storeId,
                            $merchant_id, $catId, $itemId, $sizeId);
                    $itemPriceArr[$key]['item_price'] = $itemPrice * $itemDetails['quantity'];


                    /*---------------------- Find Preference price function end Start------------------ */
                    if (!empty($itemDetails['selectedSubPreferencesModels'])) {
                      foreach ($itemDetails['selectedSubPreferencesModels'] as $p => $subPreference) {
//                                                    pr($subPreference);
                        $sub_preferenceId = $subPreference['subprefernce_id'];
                        //Checking is Item Price with Size ID
                        $itemPriceArr[$key]['SubPreference'][$p] = $this->Webservice->getSubPrePrice($storeId,
                                $merchant_id, $itemId, $sub_preferenceId,
                                $sizeId);
                        //if (empty($itemPriceArr[$key]['SubPreference'][$p]['price'])) {
                        //    $responsedata['message'] = $itemPriceArr[$key]['SubPreference'][$p]['message'];
                        //    $responsedata['response'] = 0;
                        //    $responsedata['data'] = $subPreference;
                        //    return json_encode($responsedata);
                        //}
                        //if (($itemPriceArr[$key]['SubPreference'][$p]['price'] != $subPreference['price'])) {
                        //    $responsedata['message'] = $itemPriceArr[$key]['SubPreference'][$p]['message'];
                        //    $responsedata['response'] = 0;
                        //    $responsedata['data'] = $subPreference;
                        //    return json_encode($responsedata);
                        //}
//                                           
                      }
                    }
                    /* ---------------------- Find Preference price function end------------------ */


                    /* ---------------------- Find Extended Offer price function end Start------------------ */

                    if (!empty($itemDetails['ExtendedPromo'])) {
//                                                $getExtendedOffersPrice = array();
                      foreach ($itemDetails['ExtendedPromo'] as $eo => $extendedOffersPrice) {
                        $itemPriceArr[$key]['ExtendedPromo'][$eo] = $this->Webservice->getExtendedOffersPrice($storeId,
                                $merchant_id, $extendedOffersPrice, $user_id,
                                $itemQuantity, $itemPrice);
                      }
                    }
                    /* --------------------------- Find Offer price function end------------------ */
                    if ((!empty($storeId)) && (!empty($itemId)) && (!empty($sizeId))) {
                      $itemPriceArr[$key]['tax'] = $this->Webservice->getStoreTx($storeId,
                              $merchant_id, $itemId, $sizeId);
                    }
                  }
                }
                /* -------------------Price Check function End------------------------- */

                /* ------------------- cart item list function End------------------------------ */
//                                    pr($itemPriceArr);
                $arrTotalItemPrice = array();
                $total = array();
                if (!empty($itemPriceArr)) {
                  foreach ($itemPriceArr as $ip => $priceItemArr) {
//                                            pr($priceItemArr);
                    if (!empty($priceItemArr)) {
                      if ($priceItemArr['item_price']) {
                        $arrTotalItemPrice[$ip]['Total_price_Item'] = $priceItemArr['item_price'];
                      }
                      if (!empty($priceItemArr['SubPreference'])) {
                        foreach ($priceItemArr['SubPreference'] as $sa => $SubPreference) {
                          if (!empty($SubPreference['price'])) {
                            $arrTotalItemPrice[$ip]['SubPreference_price'][$sa] = $SubPreference['price'];
                          }
                        }
                      }
                      if (!empty($priceItemArr['ExtendedPromo'])) {
                        foreach ($priceItemArr['ExtendedPromo'] as $sa => $extendedOffersDet) {
                          if ((!empty($extendedOffersDet['price']) && ($extendedOffersDet['message'] == 'Item free'))) {
                            $arrTotalItemPrice[$ip]['extended_Offers_price'][$sa] = $extendedOffersDet['price'];
                          }
                        }
                      }
                      if (!empty($priceItemArr['tax'])) {
                        $arrTotalItemPrice[$ip]['tax'] = $priceItemArr['tax'];
                      } else {
                        $arrTotalItemPrice[$ip]['tax'] = 0;
                      }
                    }
                  }
                }
                //pr($arrTotalItemPrice);
                if (!empty($arrTotalItemPrice)) {
                  $ItemTax = array();
                  $taxes = 0;
                  foreach ($arrTotalItemPrice as $t => $arrTotalPrice) {
                    if (!empty($arrTotalPrice['Total_price_Item'])) {
                      $total['price'][$t] = $arrTotalPrice['Total_price_Item'];
                      $taxes = $arrTotalPrice['Total_price_Item'];
                    }
                    if (!empty($arrTotalPrice['SubPreference_price'])) {
                      $total['SubPreference_price'][$t] = array_sum($arrTotalPrice['SubPreference_price']);
                      $taxes = $taxes + $total['SubPreference_price'][$t];
                    }
                    if (!empty($arrTotalPrice['tax'])) {
                      $tax_on_Item = number_format($taxes * ( $arrTotalPrice['tax'] / 100),
                              2);
                      $total['storetax'][$t] = $tax_on_Item;
                    }
                  }
                }



                $sub_total_price = 0;
                $total_price = 0;
                $total_storetax = 0;
                if (!empty($total)) {
                  if (!empty($total['price'])) {
                    $total_item_price = array_sum($total['price']);
                  }
                  if (!empty($total['SubPreference_price'])) {
                    $total_SubPreference_price = array_sum($total['SubPreference_price']);
                  }
                  if (!empty($total['storetax'])) {
                    $total_storetax = array_sum($total['storetax']);
                  }
                }
                if (!empty($total_item_price)) {
                  $total_price = $total_price + $total_item_price;
                }
                if (!empty($total_subadd_price)) {
                  $total_price = $total_price + $total_subadd_price;
                }
                if (!empty($total_SubPreference_price)) {
                  $total_price = $total_price + $total_SubPreference_price;
                }

                if (!empty($checkCouponCode)) {
//                                        pr($checkCouponCode);
                  if ($checkCouponCode['discount_type'] == 1) {     // Discount is USD if discount_type is1 else in %
                    $discountValue = $checkCouponCode['discount'];
                  } else {
                    $discountValue = number_format($total_price * $checkCouponCode['discount'] / 100,
                            2);
                  }
                }

                if (!empty($discountValue)) {
                  $total_Price_items = $total_price - $discountValue;
                } else {
                  $total_Price_items = $total_price;
                }
                if (!empty($total_storetax)) {
                  $total_Price_items = $total_price + $total_storetax;
                }
                $orderTypeRequest = strtolower($requestBody['order_details']['order_type']);
                //pr($storeResult);
                if ($orderTypeRequest == 'delivery') {
                  $minAmount = $storeResult['Store']['minimum_order_price'];
                }
                if ($orderTypeRequest == 'carry-out') {
                  $minAmount = $storeResult['Store']['minimum_takeaway_price'];
                }

                if ($requestBody['total_amount'] >= $minAmount) {

                  if (isset($requestBody['is_future_order'])) {
                    if (empty($requestBody['is_future_order'])) {
                      $requestBody['is_future_order'] = 2;
                    }
                  } else {
                    $requestBody['is_future_order'] = 2;
                  }
                  //$requestBody['is_future_order']=2;
                  if ($requestBody['is_future_order'] == 1) {
                    $requestBody['store_id'] = $storeId;
                    $requestBody['merchant_id'] = $merchant_id;
                    $requestBody['user_id'] = $user_id;
                    $orderInfo = $this->convertOrderArr($requestBody, $storeId,
                            $merchant_id, $user_id);
                    unset($requestBody['cart_item_list']);
                    $requestBody['Items'] = $orderInfo;
                    $orderType = strtolower($requestBody['order_details']['order_type']);
                    if ($orderType == 'delivery') {
                      $requestBody['order_type'] = 3;
                    } else {
                      $requestBody['order_type'] = 2;
                    }
                    $orderData = $this->_getSessionOrder($requestBody,
                            $paymentType);
                    if ($this->orderTranSave($orderData, $paymentType)) {
                      $responsedata['message'] = "Order has been successfully saved in your saved order list.";
                      $responsedata['response'] = 1;
                    } else {
                      $responsedata['message'] = "Can't save order, Please try again.";
                      $responsedata['response'] = 0;
                    }
                  } else {

                    $result = $this->orderSave($requestBody, $storeId,
                            $merchant_id, $user_id);
                    $responsedata['response'] = 0;
                    $responsedata['message'] = "Can't save order, Please try again.";

                    if (!empty($result[1])) {
                      if (($result[0] == 'paypal') || ($result[0] == 'creditcard')) {
                        $responsedata['message'] = "Your temporary Order id";
                        $responsedata['temp_order_id'] = $result[1];
                        if ($result[0] == 'creditcard') {
                          $protocol = 'http://';
                          if (isset($_SERVER['HTTPS'])) {
                            if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                              $protocol = 'https://';
                            }
                          }
                          $store_id = $this->Encryption->encode($storeResult['Store']['id']);
                          if (!empty($storeResult['Store']['nzgateway_apikey'])) {
                            $key = $this->Encryption->encode($storeResult['Store']['nzgateway_apikey']);
                            $order_id = $this->Encryption->encode($result[1]);
                            $responsedata['key'] = $key;
                            $responsedata['url'] = $protocol . $storeResult['Store']['store_url'] . '/MBServices/payment/' . $key . '/' . $order_id . '/' . $store_id;
                            $responsedata['store_id'] = $store_id;
                          } else {
                            $responsedata['key'] = "";
                            $responsedata['url'] = $protocol . $storeResult['Store']['store_url'];
                            $responsedata['store_id'] = $store_id;
                          }
                        }

                        $this->Session->write('user_id', $user_id);
                        $this->Session->write('store_id',
                                $storeResult['Store']['id']);
                        $this->Session->write('merchant_id', $merchant_id);
                      } else {
                        $responsedata['message'] = "Order has been placed successfully.";
                        $responsedata['orderid'] = $result[1];
                        $this->Webservice->orderPushNotification($result[1]);
                        $this->notification($responsedata['orderid'], $storeId,
                                $merchant_id, $user_id);
                      }
                      $responsedata['response'] = 1;
                      $responsedata['time'] = @$requestBody['order_details']['time'];
                      $responsedata['payment_type'] = @$requestBody['payment_method']['payment_type'];
                      $responsedata['total_amount'] = @$requestBody['total_amount'];
                    } else {
                      $this->notificationFail($responsedata['message'],
                              $storeId, $merchant_id, $user_id, $address_id);
                    }
                  }

                  return json_encode($responsedata);
                } else {
                  $responsedata['message'] = "Item price should be greater than " . $minAmount;
                  $responsedata['response'] = 0;
                  return json_encode($responsedata);
                }
              } else {
                $responsedata['message'] = "Cart is empty.";
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
          //} else {
          //    $responsedata['message'] = "You are not register under this merchant";
          //    $responsedata['response'] = 0;
          //    return json_encode($responsedata);
          //}
        } else {
          $responsedata['message'] = "Please login or continue as a guest.";
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

//    public function checkExtendedOffer() {
//        configure::Write('debug', 0);
//        $headers = $this->getheader();
//        $requestBody = file_get_contents('php://input');
//        $this->Webservice->webserviceLog($requestBody, "extended_offer.txt", $headers);
//        //$requestBody =  '{"store_id":"108","items": [{"item_id": "5102","quantity": "2","size_id":"334"},{"item_id": "5102","quantity": "2","size_id":"334"}]}';
//        //$headers['user_id'] = 'NTI3'; // user_id=286
//        //$headers['merchant_id'] = 85;
//        $responsedata = array();
//        $requestBody = json_decode($requestBody, true);
//        if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
//            $merchant_id = $headers['merchant_id'];
//            $merchantCheck = $this->Merchant->find('first', array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
//            if (!empty($merchantCheck)) {
//
//                if (isset($headers['user_id']) && !empty($headers['user_id'])) {
//                    $roleid = array(4, 5);
//                    $user_id = $this->Encryption->decode($headers['user_id']);
//                    $userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
//                    if (!empty($userDet)) {
//                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
//
//                            $storeId = $requestBody['store_id'];
//                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $storeId, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id')));
//                            if (!empty($storeResult)) {
//                                $i = 0;
//                                foreach ($requestBody['items'] as $i => $items) {
//                                    $itemId = $items['item_id'];
//                                    if(!empty($items['size_id'])){
//                                        $item_Size_Id = $items['size_id'];    
//                                    }else{
//                                        $item_Size_Id=0;
//                                    }
//                                    
//                                    $ItemQuantity = $items['quantity'];
//                                    $this->Item->bindModel(
//                                            array(
//                                        'hasOne' => array(
//                                            'ItemPrice' => array(
//                                                'className' => 'ItemPrice',
//                                                'foreignKey' => 'item_id',
//                                                'conditions' => array('ItemPrice.is_active' => 1, 'ItemPrice.is_deleted' => 0, 'ItemPrice.store_id' => $storeId, 'ItemPrice.size_id' => $item_Size_Id),
//                                                'order' => array('ItemPrice.position ASC'),
//                                                'fields' => array('id', 'item_id', 'price')
//                                            )
//                                        )
//                                            ), false);
//
//                                    $this->ItemPrice->unBindModel(array('hasMany' => array('Size')));
//                                    $checkItem = $this->Item->find('first', array('conditions' => array('Item.store_id' => $storeId, 'Item.merchant_id' => $merchant_id, 'Item.is_deleted' => 0, 'Item.id' => $itemId, 'Item.is_active' => 1), 'fields' => array('id', 'name'), 'recursive' => 3));
//                                    //pr($requestBody);
//                                    $freeQuantity = $this->Webservice->checkItemOffer($itemId, $user_id, $ItemQuantity, $storeId);
//                                    $price[$i]['item_id'] = $itemId;
//                                    $price[$i]['quantity'] = $ItemQuantity;
//                                    if ($freeQuantity > 0) {
//                                        $price[$i]['freeUnit'] = $freeQuantity;
//                                        if (!empty($checkItem['ItemPrice'])) {
//                                            $price[$i]['discount'] = number_format($checkItem['ItemPrice']['price'] * $freeQuantity,2);
//                                        } else {
//                                            $price[$i]['discount'] = '0';
//                                        }
//                                    } else {
//                                        $price[$i]['freeUnit'] = '0';
//                                        $price[$i]['discount'] = '0';
//                                    }
//                                    $i++;
//                                    //echo "Free Quantity - ".$freeQuantity."<br>";
//                                }
//                                $total_Price = 0;
//                                foreach ($price as $totalPrice) {
//                                    $total_Price = number_format($total_Price + $totalPrice['discount'],2);
//                                }
//                                $responsedata['message'] = "Success";
//                                $responsedata['response'] = 1;
//                                $responsedata['total_discount'] = $total_Price;
//                                $responsedata['items'] = array_values($price);
//                                return json_encode($responsedata);
//                                //pr($price);
//                            } else {
//                                $responsedata['message'] = "Store not found.";
//                                $responsedata['response'] = 0;
//                                return json_encode($responsedata);
//                            }
//                        } else {
//                            $responsedata['message'] = "Please select a store.";
//                            $responsedata['response'] = 0;
//                            return json_encode($responsedata);
//                        }
//                    } else {
//                        $responsedata['message'] = "You are not registered under this merchant.";
//                        $responsedata['response'] = 0;
//                        return json_encode($responsedata);
//                    }
//                } else {
//                    $responsedata['message'] = "Please login.";
//                    $responsedata['response'] = 0;
//                    return json_encode($responsedata);
//                }
//            } else {
//                $responsedata['message'] = "Merchant not found.";
//                $responsedata['response'] = 0;
//                return json_encode($responsedata);
//            }
//        } else {
//            $responsedata['message'] = "Please select a merchant.";
//            $responsedata['response'] = 0;
//            return json_encode($responsedata);
//        }
//    }


  public function checkExtendedOffer() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "extended_offer.txt",
            $headers);
    //$requestBody =  '{"store_id":"108","items": [{"item_id": "5102","quantity": "2","size_id":"334"},{"item_id": "5102","quantity": "2","size_id":"334"}]}';
    //$headers['user_id'] = 'NTI3'; // user_id=286
    //$headers['merchant_id'] = 85;
    $responsedata = array();
    $requestBody = json_decode($requestBody, true);
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('POST')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        //if (isset($headers['user_id']) && !empty($headers['user_id'])) {
        $roleid = array(4, 5);
        $user_id = 0;
        if (isset($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
        }
        //$userDet = $this->User->find('first', array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
        //if (!empty($userDet)) {
        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
          $storeId = $requestBody['store_id'];
          // Checking Store id parameter is Integer
                $argStore = $this->checkParameter($storeId, 'integer', 'Store'); 
                    if ($argStore['response'] == '403') {
                      return json_encode($argStore);
                    }
          $storeResult = $this->Store->find('first',
                  array('conditions' => array('Store.id' => $storeId, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id')));
          if (!empty($storeResult)) {
            $i = 0;
            foreach ($requestBody['items'] as $i => $items) {
              $itemId = $items['item_id'];
                 if (!empty($itemId)) {
                  // Checking Store id parameter is Integer
                    $argItem = $this->checkParameter($itemId, 'integer', 'Item'); 
                        if ($argItem['response'] == '403') {
                          return json_encode($argItem);
                        }
                 }
              
              if (!empty($items['size_id'])) {
                // Checking Store id parameter is Integer
                $argSize = $this->checkParameter($items['size_id'], 'integer', 'Size'); 
                    if ($argSize['response'] == '403') {
                      return json_encode($argSize);
                    }
                $item_Size_Id = $items['size_id'];
              } else {
                $item_Size_Id = 0;
              }

              $ItemQuantity = $items['quantity'];
              if (!empty($ItemQuantity)) {
                  // Checking Store id parameter is Integer
                    $argItemQuantity = $this->checkParameter($ItemQuantity, 'integer', 'Item quantity'); 
                        if ($argItemQuantity['response'] == '403') {
                          return json_encode($argItemQuantity);
                        }
                 }
              $this->Item->bindModel(
                      array(
                  'hasOne' => array(
                      'ItemPrice' => array(
                          'className' => 'ItemPrice',
                          'foreignKey' => 'item_id',
                          'conditions' => array('ItemPrice.is_active' => 1, 'ItemPrice.is_deleted' => 0, 'ItemPrice.store_id' => $storeId, 'ItemPrice.size_id' => $item_Size_Id),
                          'order' => array('ItemPrice.position ASC'),
                          'fields' => array('id', 'item_id', 'price')
                      )
                  )
                      ), false);

              $this->ItemPrice->unBindModel(array('hasMany' => array('Size')));
              $checkItem = $this->Item->find('first',
                      array('conditions' => array('Item.store_id' => $storeId, 'Item.merchant_id' => $merchant_id, 'Item.is_deleted' => 0, 'Item.id' => $itemId, 'Item.is_active' => 1), 'fields' => array('id', 'name'), 'recursive' => 3));
              //pr($requestBody);
              $freeQuantity = $this->Webservice->checkItemOffer($itemId,
                      $user_id, $ItemQuantity, $storeId);
              $price[$i]['item_id'] = $itemId;
              $price[$i]['quantity'] = $ItemQuantity;
              if ($freeQuantity > 0) {
                $price[$i]['freeUnit'] = $freeQuantity;
                if (!empty($checkItem['ItemPrice'])) {
                  $price[$i]['discount'] = number_format($checkItem['ItemPrice']['price'] * $freeQuantity,
                          2);
                } else {
                  $price[$i]['discount'] = '0';
                }
              } else {
                $price[$i]['freeUnit'] = '0';
                $price[$i]['discount'] = '0';
              }
              $i++;
              //echo "Free Quantity - ".$freeQuantity."<br>";
            }
            $total_Price = 0;
            foreach ($price as $totalPrice) {
              $total_Price = number_format($total_Price + $totalPrice['discount'],
                      2);
            }
            $responsedata['message'] = "Success";
            $responsedata['response'] = 1;
            $responsedata['total_discount'] = $total_Price;
            $responsedata['items'] = array_values($price);
            return json_encode($responsedata);
            //pr($price);
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
//                    } else {
//                        $responsedata['message'] = "You are not registered under this merchant.";
//                        $responsedata['response'] = 0;
//                        return json_encode($responsedata);
//                    }
//                } else {
//                    $responsedata['message'] = "Please login.";
//                    $responsedata['response'] = 0;
//                    return json_encode($responsedata);
//                }
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

  public function myFavoritesList() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "my_favorites_list.txt",
            $headers);
    $responsedata = array();
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id', 'domain_name')));
      if (!empty($merchantCheck)) {
        if (isset($headers['user_id']) && !empty($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          $roleid = array(4, 5);
          $userDet = $this->User->find('first',
                  array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
          if (!empty($userDet)) {
            $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('id', 'name')))),
                    false);
            $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('id', 'name')))),
                    false);
            $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('fields' => array('id', 'name')))),
                    false);
            $this->OrderItem->bindModel(array('hasOne' => array('StoreReview' => array('fields' => array('review_rating', 'is_approved'))), 'hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity')), 'OrderTopping' => array('fields' => array('id', 'topping_id')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('id', 'name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('id', 'size')))),
                    false);
            $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id', 'interval_id'))), 'belongsTo' => array('DeliveryAddress' => array('fields' => array('id', 'name_on_bell', 'city', 'address')), 'OrderStatus' => array('fields' => array('id', 'name')))),
                    false);
            $this->Favorite->bindModel(array('belongsTo' => array('Order' => array('fields' => array('id', 'user_id', 'order_number', 'amount', 'seqment_id', 'delivery_address_id', 'order_status_id', 'coupon_discount', 'created')))),
                    false);

            $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
            $this->Favorite->bindModel(array(
                'belongsTo' => array(
                    'Store' => array(
                        'className' => 'Store',
                        'foreignKey' => 'store_id',
                        'fields' => array('id', 'store_name', 'store_url'),
                        'type' => 'INNER',
                        'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1)
                    ))
                    ), false);

            $myFav = $this->Favorite->find('all',
                    array('order' => 'Favorite.created DESC', 'recursive' => 4, 'conditions' => array('Favorite.merchant_id' => $merchant_id, 'Favorite.user_id' => $user_id, 'Favorite.is_active' => 1, 'Favorite.is_deleted' => 0)));
            $encrypted_merchantId = $this->Encryption->encode($merchant_id);

            if (!empty($myFav)) {
              foreach ($myFav as $key => $mf) {
                if (!empty($mf['Order']['order_number'])) {
                  $my_favorites[$key]['favorite_id'] = $mf['Favorite']['id'];
                  $my_favorites[$key]['order_id'] = $mf['Favorite']['order_id'];
                  $my_favorites[$key]['order_number'] = $mf['Order']['order_number'];
                  $my_favorites[$key]['total_amount'] = '$' . $mf['Order']['amount'];
                  $my_favorites[$key]['store_id'] = $mf['Store']['id'];
                  $my_favorites[$key]['store_name'] = $mf['Store']['store_name'];
                  $encrypted_storeId = $this->Encryption->encode($mf['Store']['id']);
                  if (!empty($mf['Store']['store_url'])) {
                    $domain = $mf['Store']['store_url'];
                    $my_favorites[$key]['link'] = 'http://' . $domain . "/orders/myFavorites/" . $encrypted_storeId . "/" . $encrypted_merchantId;
                  } else {
                    $my_favorites[$key]['link'] = 'http://' . $domain;
                  }
                  if (!empty($mf['Order']['seqment_id']) && $mf['Order']['seqment_id'] == 2) {
                    $my_favorites[$key]['order_type'] = 'Carry-out';
                  } elseif (!empty($mf['Order']['seqment_id']) && $mf['Order']['seqment_id'] == 3) {
                    $my_favorites[$key]['order_type'] = 'Delivery';
                  }
                  $my_favorites[$key]['placed_date'] = date('m-d-Y h:m a',
                          strtotime($mf['Order']['created']));
                  if (!empty($mf['Order']['DeliveryAddress'])) {
                    $my_favorites[$key]['delivery_address_id'] = $mf['Order']['delivery_address_id'];
                    $my_favorites[$key]['name_on_bell'] = $mf['Order']['DeliveryAddress']['name_on_bell'];
                    $my_favorites[$key]['city'] = $mf['Order']['DeliveryAddress']['city'];
                    $my_favorites[$key]['address'] = $mf['Order']['DeliveryAddress']['address'];
                  } else {
                    $my_favorites[$key]['delivery_address_id'] = "";
                    $my_favorites[$key]['name_on_bell'] = "";
                    $my_favorites[$key]['city'] = "";
                    $my_favorites[$key]['address'] = "";
                  }
                  if (!empty($mf['Order']['OrderStatus'])) {
                    $my_favorites[$key]['OrderStatus'] = $mf['Order']['OrderStatus']['name'];
                  }
                  if (!empty($mf['Order']['OrderItem'])) {
                    foreach ($mf['Order']['OrderItem'] as $key1 => $mfo) {
                      //pr($mfo);
                      $my_favorites[$key]['items'][$key1]['item_id'] = $mfo['item_id'];
                      $my_favorites[$key]['items'][$key1]['item_name'] = @$mfo['Item']['name'];
                      $my_favorites[$key]['items'][$key1]['quantity'] = @$mfo['quantity'];
                      if (!empty($mfo['Size'])) {
                        $my_favorites[$key]['items'][$key1]['size_id'] = @$mfo['Size']['id'];
                        $my_favorites[$key]['items'][$key1]['size_name'] = @$mfo['Size']['size'];
                      } else {
                        $my_favorites[$key]['items'][$key1]['size_id'] = "";
                        $my_favorites[$key]['items'][$key1]['size_name'] = "";
                      }
                      if (!empty($mfo['OrderTopping'])) {
                        foreach ($mfo['OrderTopping'] as $key2 => $mfot) {
                          $my_favorites[$key]['items'][$key1]['subAddons'][$key2]['id'] = @$mfot['Topping']['id'];
                          $my_favorites[$key]['items'][$key1]['subAddons'][$key2]['name'] = @$mfot['Topping']['name'];
                        }
                      } else {
                        $my_favorites[$key]['items'][$key1]['subAddons'] = array();
                      }
                      if (!empty($mfo['OrderPreference'])) {
                        foreach ($mfo['OrderPreference'] as $key3 => $mfop) {
                          if (!empty($mfop['SubPreference'])) {
                            $my_favorites[$key]['items'][$key1]['subpreferences'][$key3]['id'] = @$mfop['SubPreference']['id'];
                            $my_favorites[$key]['items'][$key1]['subpreferences'][$key3]['subpreference_name'] = @$mfop['SubPreference']['name'];
                          } else {
                            $my_favorites[$key]['items'][$key1]['subpreferences'] = array();
                          }
                        }
                      } else {
                        $my_favorites[$key]['items'][$key1]['subpreferences'] = array();
                      }
                      if (!empty($mfo['OrderOffer'])) {
                        foreach ($mfo['OrderOffer'] as $key4 => $mfOffer) {
                          $my_favorites[$key]['items'][$key1]['OfferedItem'][$key4]['offered_item_id'] = @$mfOffer['Item']['id'];
                          $my_favorites[$key]['items'][$key1]['OfferedItem'][$key4]['name'] = @$mfOffer['Item']['name'];
                          $my_favorites[$key]['items'][$key1]['OfferedItem'][$key4]['quantity'] = @$mfOffer['quantity'];
                        }
                      } else {
                        $my_favorites[$key]['items'][$key1]['OfferedItem'] = array();
                      }

                      $FreeItem = $this->OrderItemFree->find('first',
                              array('fields' => array('id', 'item_id', 'free_quantity'), 'conditions' => array('OrderItemFree.item_id' => $mfo['item_id'], 'OrderItemFree.order_id' => $mf['Favorite']['order_id'], 'OrderItemFree.is_active' => 1, 'OrderItemFree.is_deleted' => 0)));


                      if (!empty($FreeItem)) {
                        $my_favorites[$key]['items'][$key1]['FreeItem'] = $FreeItem['OrderItemFree']['free_quantity'];
                      } else {
                        $my_favorites[$key]['items'][$key1]['FreeItem'] = "0";
                      }
                    }
                  }
                }
              }
              //pr($my_favorites);
              if (!empty($my_favorites)) {
                $responsedata['message'] = "Success";
                $responsedata['response'] = 1;
                $responsedata['my_favorites'] = array_values($my_favorites);
                //pr($responsedata);
                return json_encode($responsedata);
              } else {
                $responsedata['message'] = "No record found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
              }
            } else {
              $responsedata['message'] = "No record found.";
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

  public function RemoveFavorites() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "my_favorites_list.txt",
            $headers);
    //$requestBody =  '{"order_id":939,"fav_id":30}';
    $requestBody = json_decode($requestBody, true);
    $responsedata = array();
    //$headers['user_id'] = 'MQ';
    //$headers['merchant_id'] = 1;
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('DELETE')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        if (isset($headers['user_id']) && !empty($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          $roleid = array(4, 5);
          $userDet = $this->User->find('first',
                  array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
          if (!empty($userDet)) {
            if (isset($requestBody['order_id']) && !empty($requestBody['order_id'])) {
              $order_id = $requestBody['order_id'];
                // Checking Order id parameter is Integer
               $argOrder = $this->checkParameter($order_id, 'integer','Order'); 
                if ($argOrder['response'] == '403') {
                  return json_encode($argOrder);
                }
              $resultFavorite = $this->Favorite->updateAll(array('Favorite.is_deleted' => 1),
                      array('Favorite.order_id' => $order_id, 'Favorite.user_id' => $user_id));
              if ($resultFavorite) {
                $responsedata['message'] = "Success";
                $responsedata['response'] = 1;
                return json_encode($responsedata);
              } else {
                $responsedata['message'] = "Record can not be deleted, please try again.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
              }
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

  function convertOrderArr($requestBody = array(), $storeId, $merchant_id,
          $user_id) {
    $orderInfo = array();
    $j = 0;
    $tax_on_Item = 0;
    $itemPricefree = 0;
    foreach ($requestBody['cart_item_list'] as $i => $item_Detail) {
      //pr($item_Detail);
      if (empty($item_Detail['quantity'])) {
        $item_Detail['quantity'] = 1;
      }
      if (!empty($item_Detail['size_id'])) {
        $sizeId = $item_Detail['size_id'];
      } else {
        $sizeId = 0;
      }
      $orderInfo[$j]['Item']['id'] = $item_Detail['item_id'];
      $orderInfo[$j]['Item']['categoryid'] = $item_Detail['category_id'];
      $orderInfo[$j]['Item']['quantity'] = $item_Detail['quantity'];
      $orderInfo[$j]['Item']['price'] = $item_Detail['total_price'];
      $orderInfo[$j]['Item']['size_id'] = $item_Detail['size_id'];
      $freeQuantity = $this->Webservice->checkItemOffer($item_Detail['item_id'],
              $user_id, $item_Detail['quantity'], $storeId);
      if (!empty($freeQuantity)) {
        $itemPricefree = $this->Webservice->getItemPrice($storeId, $merchant_id,
                $item_Detail['category_id'], $item_Detail['item_id'], $sizeId);
        $orderInfo[$j]['Item']['freeQuantity'] = $freeQuantity;
        $orderInfo[$j]['Item']['unitPrice'] = $freeQuantity;
        $orderInfo[$j]['Item']['item_price'] = $itemPricefree * $freeQuantity;
      } else {
        $orderInfo[$j]['Item']['freeQuantity'] = 0;
        $orderInfo[$j]['Item']['item_price'] = $itemPricefree * $freeQuantity;
      }
      $orderInfo[$j]['Item']['taxvalue'] = $item_Detail['item_tax_value'];
      $orderInfo[$j]['Item']['taxamount'] = $item_Detail['tax'];

      $intervalPrice = $this->Webservice->getIntervalId($storeId, $merchant_id,
              $item_Detail['category_id'], $item_Detail['item_id'],
              $item_Detail['size_id']);
      $interval_id = 0;
      if (!empty($intervalPrice['IntervalPrice'])) {
        $interval_id = $intervalPrice['IntervalPrice']['interval_id'];
      }
      $orderInfo[$j]['Item']['interval_id'] = $interval_id;
      if (!empty($item_Detail['selectedSubAddonsModels'])) {
        $d = 0;
        foreach ($item_Detail['selectedSubAddonsModels'] as $s => $subAddon_Detail) {
          if ($subAddon_Detail['is_defualt'] == 1) {
            $orderInfo[$j]['Item']['default_topping'][$d]['id'] = $subAddon_Detail['sub_addon_id'];
            $orderInfo[$j]['Item']['default_topping'][$d]['size'] = $subAddon_Detail['sub_addons_size_id'];
          } else {
            $orderInfo[$j]['Item']['paid_topping'][$d]['id'] = $subAddon_Detail['sub_addon_id'];
            $orderInfo[$j]['Item']['paid_topping'][$d]['size'] = $subAddon_Detail['sub_addons_size_id'];
          }
          $d++;
        }
      }
      if (!empty($item_Detail['selectedSubPreferencesModels'])) {
        foreach ($item_Detail['selectedSubPreferencesModels'] as $p => $subPre_Detail) {
          $orderInfo[$j]['Item']['subpreference'][$subPre_Detail['subprefernce_id']] = $subPre_Detail['price'];
        }
      }
      if (!empty($item_Detail['Offers'])) {
        foreach ($item_Detail['Offers'] as $o => $Offers) {
          $orderInfo[$j]['Item']['OfferItemUnit'] = $Offers['unit'];
          $offeredItemprice = array();
          foreach ($Offers['offered_items'] as $of => $offeredItem) {
            $offeredItemprice[$of] = $offeredItem['price'];
            $orderInfo[$j]['Item']['StoreOffer'][$of]['offer_id'] = $Offers['offer_id'];
            $orderInfo[$j]['Item']['StoreOffer'][$of]['Offered_id'] = $offeredItem['Offered_id'];
            $orderInfo[$j]['Item']['StoreOffer'][$of]['offered_item_id'] = $offeredItem['offered_item_id'];
            $orderInfo[$j]['Item']['StoreOffer'][$of]['offered_size_id'] = $offeredItem['size_id'];
            $orderInfo[$j]['Item']['StoreOffer'][$of]['quantity'] = floor($item_Detail['quantity'] / $Offers['unit']);
            $orderInfo[$j]['Item']['StoreOffer'][$of]['offer_price'] = $offeredItem['price'];
          }
          if ($Offers['is_fixed_price'] == 1) {
            $orderInfo[$j]['Item']['OfferItemPrice'] = $Offers['offerprice'];
            $orderInfo[$j]['Item']['OfferType'] = 1;
          } else {
            $orderInfo[$j]['Item']['OfferType'] = 0;
            $orderInfo[$j]['Item']['OfferItemPrice'] = array_sum($offeredItemprice);
          }
//                    if()
        }
      }
      $j++;
    }
    //pr($orderInfo);
    //die;
    return $orderInfo;
  }

  function orderSave($requestBody = array(), $storeId, $merchant_id, $user_id) {
    $requestBody['store_id'] = $storeId;
    $requestBody['merchant_id'] = $merchant_id;
    $requestBody['user_id'] = $user_id;
    $result = array();
    $orderInfo = $this->convertOrderArr($requestBody, $storeId, $merchant_id,
            $user_id);
    unset($requestBody['cart_item_list']);
    $requestBody['Items'] = $orderInfo;

    $orderType = strtolower($requestBody['order_details']['order_type']);
    if ($orderType == 'delivery') {
      $requestBody['order_type'] = 3;
    } else {
      $requestBody['order_type'] = 2;
    }
    //4-ordersave,1-creditcard,2-paypal,3-COD        
    $requestBody['payment_method']['payment_option'] = strtolower($requestBody['payment_method']['payment_type']);
    $paymentoption = strtolower($requestBody['payment_method']['payment_option']);
    $result[0] = $paymentoption;
    if ($paymentoption == 'paypal') {
      $paymentType = 2;
      $requestBody['payment_type'] = $paymentType;
      $requestDataPaypal = json_encode($requestBody);
      $data['order_details'] = $requestDataPaypal;
      $this->MobileOrder->create();
      $this->MobileOrder->save($data);
      $temp_orderId = $this->MobileOrder->getLastInsertID();
      $result[1] = $temp_orderId;
      return $result;
    } elseif ($paymentoption == 'creditcard' || $paymentoption == 'cash') {
      if ($paymentoption == 'cash') {
        $paymentType = 3;
      } else {
        $paymentType = 1;
      }

      $requestBody['payment_type'] = $paymentType;
      $orderid = $this->completeOrder($requestBody, $paymentType);
      $result[1] = $orderid;
      return $result;
      //$this->savePayment($this->_getPayItem('OrderPayment', $response, "NZGateway", "PAID by credit card"));
    }
  }

  function completeOrder($requestBody = null, $paymentType = null,
          $response = null) {
    $ds = $this->Order->getdatasource();
    $ds->begin($this);
    try {
      $orderData = $this->_getSessionOrder($requestBody, $paymentType);
      if ($this->orderTranSave($orderData, $paymentType)) {
        $ds->commit($this);
        if ($paymentType == 3) {
          $requestBody['orderPayment'] = $this->_getPayItem('OrderPayment', "",
                  "COD", "Cash on Delivery", $requestBody);
          $this->OrderPayment->savePayment($requestBody['orderPayment']);
          $ds->commit($this);
        }
        if ($paymentType == 2) {
          $requestBody['orderPayment'] = $this->_getPayItem('PaypalPayment', "",
                  "PayPal", "Paid", $response);
          $this->OrderPayment->savePayment($requestBody['orderPayment']);
          $ds->commit($this);
        }
        //elseif ($paymentType == 1) {
        //    
        //    $response=$this->CCPayment($requestBody);
        //    if($response['response']['ACK']=='Success'){
        //        $requestBody['orderPayment']=$this->_getPayItem('OrderPayment', $response, "NZGateway", "PAID by credit card",$requestBody);
        //        $this->OrderPayment->savePayment($requestBody['orderPayment']);
        //        $ds->commit($this);
        //    }else{
        //         $ds->rollback($this);
        //         return false;
        //        //throw new Exception("NZ Gateway Sale Error : ");    
        //    }
        //}
        $orderid = $this->Order->getLastInsertId();
        $orderPaymentID = $this->OrderPayment->getLastInsertId();
        $ds->commit($this);

        $this->Order->updateAll(array('payment_id' => $orderPaymentID),
                array('Order.id' => $orderid));
        $ds->commit($this);
        return $orderid;
      } else {
        throw new Exception("NZ Gateway Sale Error : ");
      }
    } catch (Exception $e) {
      $ds->rollback($this);
    }
  }

  public function proceedOrder() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "my_paypalOrder.txt",
            $headers);
    //$requestBody = '{"temp_order_id":"5","create_time":"2016-10-25T05:45:58Z","id":"PAY-2N117911TM925335ULAHPDDY","state":"approved"}';
    $requestBody = json_decode($requestBody, true);
    $responsedata = array();
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('POST')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($requestBody['temp_order_id']) && !empty($requestBody['temp_order_id'])) {
      $jsondata = $this->MobileOrder->getTempOrder($requestBody['temp_order_id']);
      $jSonRequestBody = json_decode($jsondata['MobileOrder']['order_details'],
              true);
      $response['transactionid'] = $requestBody['id'];
      $response['responsetext'] = $requestBody['state'];
      $response['response_code'] = 100;
      $response['total_amount'] = $jSonRequestBody['total_amount'];
      $response['user_id'] = $jSonRequestBody['user_id'];
      $response['store_id'] = $jSonRequestBody['store_id'];
      $response['merchant_id'] = $jSonRequestBody['merchant_id'];
      $storeId = $jSonRequestBody['store_id'];
      $merchant_id = $jSonRequestBody['merchant_id'];
      $user_id = $jSonRequestBody['user_id'];
      $address_id = $jSonRequestBody['order_details']['address_id'];

      $result = $this->completeOrder($jSonRequestBody, '2', $response);
      if ($result) {
        $responsedata['message'] = "Order has been placed successfully.";
        $responsedata['orderid'] = $result;
        $responsedata['response'] = 1;
        $responsedata['payment_type'] = @$jSonRequestBody['payment_method']['payment_type'];
        $responsedata['total_amount'] = @$jSonRequestBody['total_amount'];
        try {
          $this->Webservice->orderPushNotification($result);
        } catch (Exception $e) {
          
        }

        $this->notification($responsedata['orderid'], $storeId, $merchant_id,
                $user_id);
        return json_encode($responsedata);
      }
    } else {
      $responsedata['message'] = "Can't save order, please try again.";
      $responsedata['response'] = 0;
      $this->notificationFail($responsedata['message'], $storeId, $merchant_id,
              $user_id, $address_id);
      return json_encode($responsedata);
    }
  }

  public function orderTranSave($orderInfo, $paymemt_type = null) {
    $this->loadModel('Order');
    $this->loadModel('OrderItem');
    $this->loadModel('OrderItemFree');
    $this->loadModel('OrderOffer');
    $this->loadModel('OrderTopping');
    $this->loadModel('OrderPreference');
    $this->loadModel('StorePrintHistory');
    $this->loadModel('OrderPayment');
    $detail['user_id'] = $orderInfo['user_id'];
    $detail['store_id'] = $orderInfo['store_id'];
    $detail['merchant_id'] = $orderInfo['merchant_id'];
    //----------------------------------------------------------------------//
    // 1: Save OrderInfo
    //----------------------------------------------------------------------//
    $orderInfo = json_decode(json_encode($orderInfo), true);
    //pr($orderInfo);
    //die;
    if ($this->Order->saveOrder($orderInfo['orderdata'])) {

      //----------------------------------------------------------------------//
      // 2: Save Print History
      //----------------------------------------------------------------------//
      if ($paymemt_type != 4) {
        $this->StorePrintHistory->create();
        $store_info = $this->Store->fetchStoreDetail($orderInfo['store_id']);
        if ($store_info['Store']['is_kitchen_printer'] == 1) {
          $this->StorePrintHistory->saveStorePrintHistory($this->_getOrderItem('KitchenPrinter',
                          null, $detail));
        }
        if ($store_info['Store']['is_receipt_printer'] == 1) {
          $this->StorePrintHistory->saveStorePrintHistory($this->_getOrderItem('ReceiptPrinter',
                          null, $detail));
        }
      }
    } else {
      return false;
    }

    foreach ($orderInfo['Items'] as $result) {

      // TODO : throw new Exception('Item key is null');
      if (!array_key_exists('Item', $result))
        return false;

      //----------------------------------------------------------------------//
      // 3: Save OrderItem
      //----------------------------------------------------------------------//
      $this->OrderItem->create();
      $this->OrderItem->save($this->_getOrderItem("OrderItem", $result['Item'],
                      $detail));

      if (!empty($result['Item']['freeQuantity'])) {
        $this->OrderItemFree->create();
        $this->OrderItemFree->saveItemFree($this->_getOrderItem("OrderItemFree",
                        $result['Item'], $detail));
      }

      //----------------------------------------------------------------------//
      // 4: Save OrderOffer
      //----------------------------------------------------------------------//
      if (!empty($result['Item']['StoreOffer'])) {
        foreach ($result['Item']['StoreOffer'] as $key => $item) {
          $this->OrderOffer->create();
          $this->OrderOffer->saveOfferOrder($this->_getOrderItem('OrderOffer',
                          $item, $detail));
        }
      }

      //----------------------------------------------------------------------//
      // 5: Save DefaultTopping
      //----------------------------------------------------------------------//
      if (!empty($result['Item']['default_topping'])) {
        foreach ($result['Item']['default_topping'] as $item) {
          $this->OrderTopping->create();
          $this->OrderTopping->saveTopping($this->_getOrderItem('OrderTopping',
                          $item, $detail));
        }
      }

      //----------------------------------------------------------------------//
      // 6: Save PaidTopping
      //----------------------------------------------------------------------//
      if (!empty($result['Item']['paid_topping'])) {
        foreach ($result['Item']['paid_topping'] as $item) {
          $this->OrderTopping->create();
          $this->OrderTopping->saveTopping($this->_getOrderItem('OrderTopping',
                          $item, $detail));
        }
      }

      //----------------------------------------------------------------------//
      // 7: Save SubPreference
      //----------------------------------------------------------------------//
      if (!empty($result['Item']['subpreference'])) {
        foreach ($result['Item']['subpreference'] as $subPreId => $item) {
          $this->OrderPreference->create();
          $this->OrderPreference->saveSubpreference($this->_getOrderItem('OrderPreference',
                          $subPreId, $detail));
        }
      }
    }

    return true;
  }

  private function _getSessionOrder($requestData, $paymemt_type) {
    $userId = $requestData['user_id'];
    $segment_id = (strtolower($requestData['order_details']['order_type']) == 'delivery') ? 3 : 2;
    //$order_comments=($requestData['extended_discount'])?$requestData['extended_discount']:'';
    $order_comments = "";
    $aResult = array();

    $aResult['order_status_id'] = 1;
    // 0:NoneMember(???)
    $aResult['user_id'] = $userId;
    // Read the type of Delivery
    $aResult['seqment_id'] = $segment_id;
    $aResult['order_comments'] = $order_comments;
    $aResult['mobile_flag'] = 1;
    // Read the PreOrder Type
    $aResult['merchant_id'] = $requestData['merchant_id'];
    $aResult['store_id'] = $requestData['store_id'];
    $aResult['is_pre_order'] = 1;
    $aResult['tax_price'] = (!empty($requestData['total_tax'])) ? $requestData['total_tax'] : 0.00;
    $aResult['service_amount'] = (!empty($requestData['service_fee'])) ? $requestData['service_fee'] : 0.00;
    if ($aResult['seqment_id'] == 3) {
      $aResult['delivery_amount'] = (!empty($requestData['delivery_fee'])) ? $requestData['delivery_fee'] : 0.00;
    }
    $aResult['amount'] = (!empty($requestData['total_amount'])) ? $requestData['total_amount'] : 0.00;
    $aResult['delivery_address_id'] = ($requestData['order_details']['address_id']) ? $requestData['order_details']['address_id'] : '';
    $aResult['payment_id'] = (!empty($requestData['payment_id'])) ? $requestData['payment_id'] : '';
    $aResult['coupon_code'] = (!empty($requestData['coupon_code'])) ? $requestData['coupon_code'] : '';
    $aResult['coupon_discount'] = (!empty($requestData['coupon_discount'])) ? $requestData['coupon_discount'] : 0.00;

    // ????? ????
    if ($paymemt_type == 4) {
      $aResult['payment_id'] = 0;
      $aResult['is_future_order'] = 1;
    }
    if ($paymemt_type == 1) {
      $aResult['is_active'] = 0;
//            $aResult['is_future_order'] = 1;
    }
    if ($requestData['is_future_order'] == 1) {
      $aResult['is_future_order'] = 1;
      $aResult['is_active'] = 1;
    } else {
      $aResult['is_future_order'] = 0;
    }

    $aResult['tip'] = (isset($requestData['tip_amount'])) ? $requestData['tip_amount'] : 0.00;
    $orderDate = $this->Webservice->reformatDate($requestData['order_details']['date'],
            'd-m-Y');
    $orderTime = $orderDate . ' ' . $requestData['order_details']['time'];
    $aResult['pickup_time'] = $orderTime;
    //$aResult['order_number'] = $this->Order->getOrderNumber($aResult['store_id']);



    $timeZoneInfo = $this->Store->find('first',
            array('conditions' => array("Store.id" => $aResult['store_id']), 'fields' => array('Store.time_zone_id'), 'recursive' => -1));
    $Store_Gmt_diff = "-8:00";
    if (!empty($timeZoneInfo['Store']['time_zone_id'])) {
      $storeadmintimezone = $this->TimeZone->find('first',
              array('conditions' => array("TimeZone.id" => $timeZoneInfo['Store']['time_zone_id']), 'fields' => array('TimeZone.difference_in_seconds', 'TimeZone.code', 'TimeZone.gmt'), 'recursive' => -1));

      if (!empty($storeadmintimezone)) {
        $storeGmt = explode(" ", $storeadmintimezone['TimeZone']['gmt']);
        $Store_Gmt_diff = $storeGmt[1];
      }
    }
    $aResult['order_number'] = $this->Common->RandomStringMobile($aResult['store_id'],
            $Store_Gmt_diff, 'M');
    $requestData['orderdata'] = $aResult;
    return $requestData;
  }

  function _getOrderItem($type, $item = array(), $detail) {
    $result =array();
    $temp = array();
    $lastOrderID = $this->Order->getLastInsertId();
    $lastOrderItemID = $this->OrderItem->getLastInsertId();
    $orderNumber = '';
    $userId = $detail['user_id'];
    $store_id = $detail['store_id'];
    $merchant_id = $detail['merchant_id'];
    switch ($type) {
      case 'OrderItem' :
        $temp['order_id'] = $lastOrderID;
        $temp['quantity'] = array_key_exists('quantity', $item) ? $item['quantity'] : 0;
        $temp['tax_price'] = array_key_exists('taxamount', $item) ? $item['taxamount'] : 0;
        $temp['item_id'] = array_key_exists('id', $item) ? $item['id'] : 0;
        $temp['size_id'] = array_key_exists('size_id', $item) ? $item['size_id'] : 0;
        $temp['type_id'] = array_key_exists('type_id', $item) ? $item['type_id'] : 0;
        $temp['interval_id'] = array_key_exists('interval_id', $item) ? $item['interval_id'] : 0;
        $temp['total_item_price'] = array_key_exists('price', $item) ? $item['price'] : 0;
        $temp['discount'] = 0; // Flow is not known for now for this particual field
        $temp['user_id'] = $userId;
        $temp['store_id'] = $store_id;
        $temp['merchant_id'] = $merchant_id;
        $temp['size_id'] = trim($temp['size_id']);
        if (empty($temp['size_id'])) {
          $temp['size_id'] = 0;
        }

        $result['OrderItem'] = $temp;
        break;

      case 'OrderItemFree' :
        $temp['item_id'] = $item['id'];
        $temp['free_quantity'] = $item['freeQuantity'];
        $temp['price'] = $item['item_price'];
        $temp['order_id'] = $lastOrderID;
        $temp['store_id'] = $store_id;
        $temp['user_id'] = $userId;
        $result['OrderItemFree'] = $temp;
        break;

      case 'OrderOffer' :
        $temp['order_id'] = $lastOrderID;
        $temp['order_item_id'] = $lastOrderItemID;
        $temp['offer_id'] = $item['offer_id'];
        $temp['offered_item_id'] = $item['offered_item_id'];
        $temp['offered_size_id'] = $item['offered_size_id'];
        $temp['quantity'] = $item['quantity'];
        $temp['store_id'] = $store_id;
        $temp['merchant_id'] = $merchant_id;
        $result['OrderOffer'] = $temp;
        break;

      case 'OrderTopping' :
        $temp['order_id'] = $lastOrderID;
        $temp['order_item_id'] = $lastOrderItemID;
        $temp['topping_id'] = $item['id'];
        $temp['addon_size_id'] = $item['size'];
        $temp['topType'] = "defaultTop";
        $temp['store_id'] = $store_id;
        $temp['merchant_id'] = $merchant_id;
        $result['OrderTopping'] = $temp;
        break;

      case 'PaidTopping' :
        $temp['order_id'] = $lastOrderID;
        $temp['order_item_id'] = $lastOrderItemID;
        $temp['topping_id'] = $item['id'];
        $temp['addon_size_id'] = $item['size'];
        $temp['topType'] = "defaultTop";
        $temp['store_id'] = $store_id;
        $temp['merchant_id'] = $merchant_id;
        $result['OrderTopping'] = $temp;
        break;

      case 'OrderPreference' :
        $temp['order_id'] = $lastOrderID;
        $temp['order_item_id'] = $lastOrderItemID;
        $temp['sub_preference_id'] = $item;
        $temp['store_id'] = $store_id;
        $temp['merchant_id'] = $merchant_id;
        $result['OrderPreference'] = $temp;
        break;

      case 'KitchenPrinter' :
        $temp['id'] = '';
        $temp['merchant_id'] = $merchant_id;
        $temp['store_id'] = $store_id;
        $temp['order_id'] = $lastOrderID;
        $temp['order_number'] = $orderNumber;
        $temp['type'] = '1'; //Kitchen Printer
        $result = $temp;
        break;

      case 'ReceiptPrinter' :
        $temp['id'] = '';
        $temp['merchant_id'] = $merchant_id;
        $temp['store_id'] = $store_id;
        $temp['order_id'] = $lastOrderID;
        $temp['order_number'] = $orderNumber;
        $temp['type'] = '2'; //Receipt Printer
        $result = $temp;
        break;
    }
    return $result;
  }

  private function _getPayItem($type, $response = '', $gateway = '',
          $status = '', $data) {

    $userId = $data['user_id'];
    $store_id = $data['store_id'];
    $merchant_id = $data['merchant_id'];
    $amount = $data['total_amount'];

    switch ($type) {
      case 'VaultId' : // NZ Gateway vault id
        $nzsafe_info = $this->NzsafeUser->getUser($userId);
        $this->request->data['NzsafeUser']['id'] = $nzsafe_info['NzsafeUser']['id'];
        $this->request->data['NzsafeUser']['customer_vault_id'] = $nzsafe_info['NzsafeUser']['customer_vault_id'];
        return $nzsafe_info['NzsafeUser']['customer_vault_id'];

      case 'NzsafeUser' : // Credit Card
        $card_num = $this->request->data['Payment']['cardnumber'];
        $credit_type = $this->request->data['Payment']['creditype'];
        $credit_temp = substr(strrev($card_num), 0, 4);
        $credit_mask = strrev($credit_temp);
        $customer_email = AuthComponent::User('email');
        $this->request->data['NzsafeUser']['email'] = $customer_email;
        $this->request->data['NzsafeUser']['user_id'] = $userId;
        $this->request->data['NzsafeUser']['store_id'] = $store_id;
        $this->request->data['NzsafeUser']['merchant_id'] = $merchant_id;
        $this->request->data['NzsafeUser']['credit_type'] = $credit_type;
        $this->request->data['NzsafeUser']['credit_mask'] = $credit_mask;
        $this->request->data['NzsafeUser']['customer_vault_id'] = $this->_getPayItem('VaultId');
        return $this->request->data['NzsafeUser'];

      case 'OrderPayment' :
        $this->request->data['OrderPayment']['order_id'] = $this->Order->getLastInsertId();
        $this->request->data['OrderPayment']['user_id'] = $userId;
        $this->request->data['OrderPayment']['store_id'] = $store_id;
        $this->request->data['OrderPayment']['merchant_id'] = $merchant_id;
        $this->request->data['OrderPayment']['transection_id'] = isset($response['transactionid']) ? $response['transactionid'] : '';
        $this->request->data['OrderPayment']['amount'] = $amount;
        $this->request->data['OrderPayment']['response'] = isset($response['responsetext']) ? $response['responsetext'] : '';
        $this->request->data['OrderPayment']['response_code'] = isset($response['response_code']) ? $response['response_code'] : '';
        $this->request->data['OrderPayment']['payment_gateway'] = isset($this->request->data['OrderPayment']['payment_gateway']) ?
                $this->request->data['OrderPayment']['payment_gateway'] : $gateway;
        if ($gateway == "COD") {
          $order_type = $data['order_type'] ? $data['order_type'] : "";
          if ($order_type == "2")
            $status = "Cash on Pickup - UNPAID"; // Pickup
          if ($order_type == "3")
            $status = "Cash on Delivery - UNPAID"; // Pickup
        }
        $this->request->data['OrderPayment']['payment_status'] = $status;
        return $this->request->data['OrderPayment'];

      case 'PaypalPayment' :
        $this->request->data['OrderPayment']['order_id'] = $this->Order->getLastInsertId();
        $this->request->data['OrderPayment']['user_id'] = $userId;
        $this->request->data['OrderPayment']['store_id'] = $store_id;
        $this->request->data['OrderPayment']['merchant_id'] = $merchant_id;
        $this->request->data['OrderPayment']['transection_id'] = isset($data['transactionid']) ? $data['transactionid'] : 0;
        $this->request->data['OrderPayment']['amount'] = isset($data['AMT']) ? $data['AMT'] : $amount;
        $this->request->data['OrderPayment']['payment_status'] = $status;
        $this->request->data['OrderPayment']['payment_gateway'] = $gateway;
        $this->request->data['OrderPayment']['response'] = $data['responsetext'];
        $this->request->data['OrderPayment']['response_code'] = $data['response_code'];
        if ($status != 'Paid')
          $this->request->data['OrderPayment']['response'] = 'Please enter proper details';
        else
          $this->request->data['OrderPayment']['response'] = 'Payment has been approved';
        return $this->request->data['OrderPayment'];
    }
  }

  function CCPayment($Details) {
    $expiryDate = explode('/', $Details['payment_method']['expiry']);
    $env = "sandbox";
    $store_id = $Details['store_id'];
    $paymentType = "";
    $amount = $Details['total_amount'];
    $creditCardType = $Details['payment_method']['card_type'];
    $creditCardNumber = $Details['payment_method']['cc_number'];
    $padDateMonth = $expiryDate[0];
    $expDateYear = $expiryDate[1];
    $cvv2Number = $Details['payment_method']['cvv'];
    $firstName = $Details['payment_method']['fname'];
    $lastName = $Details['payment_method']['lastname'];
    $address1 = $Details['payment_method']['address'];
    $city = $Details['payment_method']['city'];
    $state = $Details['payment_method']['state'];
    $zip = $Details['payment_method']['zip_code'];
    $nvpStr = "&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber" .
            "&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName" .
            "&STREET=$address1&CITY=$city&STATE=$state&ZIP=$zip&CURRENCYCODE=USD";
    $storeInfo = $this->Store->fetchStorePaypalDetail($store_id);
    $password = trim($storeInfo['Store']['paypal_password']);
    $email = trim($storeInfo['Store']['paypal_email']);
    $signature = trim($storeInfo['Store']['paypal_signature']);
    if (!empty($storeInfo['Store']['paypal_mode'])) {
      $env = 'live';
    }
    $response = $this->Paypal->PPHttpPost($env, 'DoDirectPayment', $nvpStr,
            $email, $password, $signature);

    return $response;
  }

  /*   * ******************************************************************************************
    @Function Name : myOrdersList
    @Description   : this function is used to show list of Users Orders on merchant ID
    @Author        : SmartData
    created:26/10/2016
   * ****************************************************************************************** */

  public function myOrdersList() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "myOrdersList.txt", $headers);
    $requestBody = json_decode($requestBody, true);
    $responsedata = array();
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {
        if (isset($headers['user_id']) && !empty($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          $roleid = array(4, 5);
          $userDet = $this->User->find('first',
                  array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
          if (!empty($userDet)) {

            $myFav = $this->Favorite->find('all',
                    array('order' => 'Favorite.created DESC', 'recursive' => 4, 'conditions' => array('Favorite.merchant_id' => $headers['merchant_id'], 'Favorite.user_id' => $user_id, 'Favorite.is_active' => 1, 'Favorite.is_deleted' => 0)));
            $compare = array();
            if (!empty($myFav)) {
              foreach ($myFav as $fav) {
                $compare[] = $fav['Favorite']['order_id'];
              }
            }
            $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))),
                    false);
            $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('fields' => array('name')))),
                    false);
            $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')))),
                    false);
            $this->OrderItem->bindModel(array('hasOne' => array('StoreReview' => array('fields' => array('review_rating', 'is_approved'))), 'hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity')), 'OrderTopping' => array('fields' => array('id', 'topping_id')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('id', 'name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('id', 'size')))),
                    false);
            $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id', 'interval_id'))), 'belongsTo' => array('DeliveryAddress' => array('fields' => array('name_on_bell', 'city', 'address')), 'User' => array('fields' => array('id', 'fname', 'lname', 'address', 'city', 'state')), 'OrderStatus' => array('fields' => array('name')))),
                    false);

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


            $myOrders = $this->Order->find('all',
                    array('order' => 'Order.created DESC', 'recursive' => 3, 'conditions' => array('Order.merchant_id' => $merchant_id, 'Order.user_id' => $user_id, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0), 'fields' => array('Order.id', 'Order.order_number', 'Order.seqment_id', 'Order.order_status_id', 'Order.user_id', 'Order.amount', 'Order.created', 'Order.store_id', 'Order.delivery_address_id')));
            $encrypted_merchantId = $this->Encryption->encode($merchant_id);
            $myOrdersList = array();
            if (!empty($myOrders)) {
              foreach ($myOrders as $o => $listOrder) {
                $myOrdersList[$o]['favorite_bool'] = false;
                if (in_array($listOrder['Order']['id'], $compare)) {
                  $myOrdersList[$o]['favorite_bool'] = true;
                }
                $myOrdersList[$o]['order_id'] = $listOrder['Order']['id'];
                $myOrdersList[$o]['order_number'] = $listOrder['Order']['order_number'];
                $myOrdersList[$o]['total_amount'] = '$' . $listOrder['Order']['amount'];
                $myOrdersList[$o]['placed_date'] = date('m-d-Y h:m a',
                        strtotime($listOrder['Order']['created']));
                if (!empty($listOrder['Order']['seqment_id']) && $listOrder['Order']['seqment_id'] == 2) {
                  $myOrdersList[$o]['order_type'] = 'Carry-out';
                } elseif (!empty($listOrder['Order']['seqment_id']) && $listOrder['Order']['seqment_id'] == 3) {
                  $myOrdersList[$o]['order_type'] = 'Delivery';
                }
                if (!empty($listOrder['DeliveryAddress']['name_on_bell'])) {
                  $myOrdersList[$o]['name_on_bell'] = $listOrder['DeliveryAddress']['name_on_bell'];
                } elseif (!empty($listOrder['User']['fname'])) {
                  $myOrdersList[$o]['name_on_bell'] = $listOrder['User']['fname'] . ' ' . $listOrder['User']['lname'];
                } else {
                  $myOrdersList[$o]['name_on_bell'] = "";
                }
                if (!empty($listOrder['DeliveryAddress']['city'])) {
                  $myOrdersList[$o]['city'] = $listOrder['DeliveryAddress']['city'];
                } elseif (!empty($listOrder['User']['city'])) {
                  $myOrdersList[$o]['city'] = $listOrder['User']['city'];
                } else {
                  $myOrdersList[$o]['city'] = "";
                }
                if (!empty($listOrder['DeliveryAddress']['address'])) {
                  $myOrdersList[$o]['address'] = $listOrder['DeliveryAddress']['address'];
                } elseif (!empty($listOrder['User']['address'])) {
                  $myOrdersList[$o]['address'] = $listOrder['User']['address'];
                } else {
                  $myOrdersList[$o]['address'] = "";
                }

                $myOrdersList[$o]['OrderStatus'] = $listOrder['OrderStatus']['name'];
                $myOrdersList[$o]['store_id'] = $listOrder['Store']['id'];
                $myOrdersList[$o]['store_name'] = $listOrder['Store']['store_name'];
                $encrypted_storeId = $this->Encryption->encode($listOrder['Store']['id']);
                if (!empty($listOrder['Store']['store_url'])) {
                  $domain = $listOrder['Store']['store_url'];
                  $myOrdersList[$o]['link'] = 'http://' . $domain . "/orders/myOrders/" . $encrypted_storeId . "/" . $encrypted_merchantId;
                } else {
                  $myOrdersList[$o]['link'] = 'http://' . $domain;
                }

                if (!empty($listOrder['OrderItem'])) {
                  foreach ($listOrder['OrderItem'] as $oI => $listOrderItem) {
                    $myOrdersList[$o]['items'][$oI]['item_id'] = $listOrderItem['Item']['id'];
                    $myOrdersList[$o]['items'][$oI]['order_item_id'] = $listOrderItem['id'];
                    if (!empty($listOrderItem['Item'])) {
                      $myOrdersList[$o]['items'][$oI]['item_name'] = $listOrderItem['Item']['name'];
                    } else {
                      $myOrdersList[$o]['items'][$oI]['item_name'] = "";
                    }

                    if (!empty($listOrderItem['quantity'])) {
                      $myOrdersList[$o]['items'][$oI]['quantity'] = $listOrderItem['quantity'];
                    } else {
                      $myOrdersList[$o]['items'][$oI]['quantity'] = 1;
                    }

                    $myReview = $this->StoreReview->find('first',
                            array('conditions' => array('StoreReview.order_item_id' => $listOrderItem['id'], 'StoreReview.order_id' => $listOrderItem['order_id'], 'StoreReview.is_active' => 1, 'StoreReview.is_deleted' => 0)));
                    if (!empty($myReview)) {
                      $myOrdersList[$o]['items'][$oI]['already_review'] = true;
                    } else {
                      $myOrdersList[$o]['items'][$oI]['already_review'] = false;
                    }
                    if (!empty($listOrderItem['Size'])) {
                      $myOrdersList[$o]['items'][$oI]['size_id'] = $listOrderItem['Size']['id'];
                      $myOrdersList[$o]['items'][$oI]['size_name'] = $listOrderItem['Size']['size'];
                    } else {
                      $myOrdersList[$o]['items'][$oI]['size_id'] = "";
                      $myOrdersList[$o]['items'][$oI]['size_name'] = "";
                    }

                    if (!empty($listOrderItem['StoreReview']['review_rating'])) {
                      $myOrdersList[$o]['items'][$oI]['review_rating'] = $listOrderItem['StoreReview']['review_rating'];
                    } else {
                      $myOrdersList[$o]['items'][$oI]['review_rating'] = 0;
                    }
                    if (!empty($listOrderItem['OrderTopping'])) {
                      foreach ($listOrderItem['OrderTopping'] as $key2 => $mfot) {
                        if (!empty($mfot['Topping'])) {
                          $myOrdersList[$o]['items'][$oI]['subAddons'][$key2]['id'] = @$mfot['topping_id'];
                          $myOrdersList[$o]['items'][$oI]['subAddons'][$key2]['name'] = @$mfot['Topping']['name'];
                        } else {
                          $myOrdersList[$o]['items'][$oI]['subAddons'] = array();
                        }
                      }
                    } else {
                      $myOrdersList[$o]['items'][$oI]['subAddons'] = array();
                    }
                    if (!empty($listOrderItem['OrderPreference'])) {
                      foreach ($listOrderItem['OrderPreference'] as $key3 => $mfop) {
                        if (!empty($mfop['SubPreference'])) {
                          $myOrdersList[$o]['items'][$oI]['subpreferences'][$key3]['id'] = @$mfop['sub_preference_id'];
                          $myOrdersList[$o]['items'][$oI]['subpreferences'][$key3]['subpreference_name'] = @$mfop['SubPreference']['name'];
                        } else {
                          $myOrdersList[$o]['items'][$oI]['subpreferences'] = array();
                        }
                      }
                    } else {
                      $myOrdersList[$o]['items'][$oI]['subpreferences'] = array();
                    }

                    if (!empty($listOrderItem['OrderOffer'])) {
                      foreach ($listOrderItem['OrderOffer'] as $key4 => $mfOffer) {
                        $myOrdersList[$o]['items'][$oI]['OfferedItem'][$key4]['offered_item_id'] = @$mfOffer['offered_item_id'];
                        $myOrdersList[$o]['items'][$oI]['OfferedItem'][$key4]['name'] = @$mfOffer['Item']['name'];
                        $myOrdersList[$o]['items'][$oI]['OfferedItem'][$key4]['quantity'] = @$mfOffer['quantity'];
                      }
                    } else {
                      $myOrdersList[$o]['items'][$oI]['OfferedItem'] = array();
                    }


                    $FreeItem = $this->OrderItemFree->find('first',
                            array('fields' => array('id', 'item_id', 'free_quantity'), 'conditions' => array('OrderItemFree.item_id' => $listOrderItem['Item']['id'], 'OrderItemFree.order_id' => $listOrderItem['order_id'], 'OrderItemFree.is_active' => 1, 'OrderItemFree.is_deleted' => 0)));


                    if (!empty($FreeItem)) {
                      $myOrdersList[$o]['items'][$oI]['FreeItem'] = $FreeItem['OrderItemFree']['free_quantity'];
                    } else {
                      $myOrdersList[$o]['items'][$oI]['FreeItem'] = "0";
                    }
                  }
                }
              }
              //die;
              //pr($myOrdersList);
              if (!empty($myOrdersList)) {
                $responsedata['message'] = "Success";
                $responsedata['response'] = 1;
                $responsedata['myOrder'] = array_values($myOrdersList);
                return json_encode($responsedata);
              } else {
                $responsedata['message'] = "No record found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
              }
            } else {
              $responsedata['message'] = "No record found.";
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

  /*   * ******************************************************************************************
    @Function Name : myCoupons
    @Description   : this function is used for list of Users Coupon on merchant ID
    @Author        : SmartData
    created:26/10/2016
   * ****************************************************************************************** */

  public function myCouponsList() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "my_Coupons_list.txt",
            $headers);
    $requestBody = json_decode($requestBody, true);
    $responsedata = array();
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        if (isset($headers['user_id']) && !empty($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          $roleid = array(4, 5);
          $userDet = $this->User->find('first',
                  array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
          if (!empty($userDet)) {
            $this->UserCoupon->bindModel(
                    array('belongsTo' => array(
                            'Coupon' => array(
                                'className' => 'Coupon',
                                'foreignKey' => 'coupon_id',
                                'conditions' => array('Coupon.is_active' => 1, 'Coupon.is_deleted' => 0),
                                'fields' => array('Coupon.id', 'Coupon.coupon_code', 'Coupon.name', 'Coupon.number_can_use', 'Coupon.discount_type', 'Coupon.discount')
                            )
            )));
            $this->UserCoupon->bindModel(
                    array('belongsTo' => array(
                            'Store' => array(
                                'className' => 'Store',
                                'foreignKey' => 'store_id',
                                'conditions' => array('Store.is_active' => 1, 'Store.is_deleted' => 0),
                                'fields' => array('Store.id', 'Store.store_name', 'Store.store_url')
                            )
            )));
            //$this->UserCoupon->bindModel(array('belongsTo' => array('Coupon')), false);
            $UserCoupon = $this->UserCoupon->find('all',
                    array('conditions' => array('UserCoupon.user_id' => $user_id, 'UserCoupon.is_deleted' => 0, 'Coupon.is_active' => 1, 'Coupon.is_deleted' => 0)));
            $encrypted_merchantId = $this->Encryption->encode($merchant_id);
            $coupons = array();
            if (!empty($UserCoupon)) {
              foreach ($UserCoupon as $c => $couponsDet) {
                $coupons[$c]['id'] = $couponsDet['UserCoupon']['id'];
                $coupons[$c]['name'] = $couponsDet['Coupon']['name'];
                $coupons[$c]['coupon_code'] = $couponsDet['Coupon']['coupon_code'];
                $coupons[$c]['number_can_use'] = $couponsDet['Coupon']['number_can_use'];
                $coupons[$c]['discount_type'] = $couponsDet['Coupon']['discount_type'];
                $coupons[$c]['discount'] = $couponsDet['Coupon']['discount'];
                if ($couponsDet['Coupon']['discount_type'] == 1) {
                  $coupons[$c]['coupon_detail'] = 'Use ' . $couponsDet['Coupon']['coupon_code'] . " get $" . $couponsDet['Coupon']['discount'] . " Off.";
                } else if ($couponsDet['Coupon']['discount_type'] == 2) {
                  $coupons[$c]['coupon_detail'] = 'Use ' . $couponsDet['Coupon']['coupon_code'] . " get " . $couponsDet['Coupon']['discount'] . "% Off.";
                }
                $coupons[$c]['store_id'] = $couponsDet['Store']['id'];
                $coupons[$c]['store_name'] = $couponsDet['Store']['store_name'];
                $encrypted_storeId = $this->Encryption->encode($couponsDet['Store']['id']);
                if (!empty($couponsDet['Store']['store_url'])) {
                  $domain = $couponsDet['Store']['store_url'];
                  $coupons[$c]['link'] = 'http://' . $domain . "/orders/myCoupons/" . $encrypted_storeId . "/" . $encrypted_merchantId;
                } else {
                  $coupons[$c]['link'] = 'http://' . $domain;
                }

                if ($couponsDet['UserCoupon']['is_active'] == 1) {
                  $coupons[$c]['status'] = true;
                } else {
                  $coupons[$c]['status'] = false;
                }
              }
              if (!empty($coupons)) {
                $responsedata['response'] = 1;
                $responsedata['message'] = "Success";
                $responsedata['coupons'] = array_values($coupons);
                return json_encode($responsedata);
              } else {
                $responsedata['message'] = "No record found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
              }
            } else {
              $responsedata['message'] = "No record found.";
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

  /* ------------- Users Coupon Code End--------------------- */

  /*   * ******************************************************************************************
    @Function Name : Favorite
    @Description   : this function is used To add order to Favorite on merchant ID
    @Author        : SmartData
    created:14/09/2016
   * ****************************************************************************************** */

  public function addToFavorite() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "add_to_favorites.txt",
            $headers);
    //$requestBody =  '{"store_id":2,"order_id":1049}';
    $requestBody = json_decode($requestBody, true);
    $responsedata = array();
    //$headers['user_id'] = 'MQ';
    //$headers['merchant_id'] = 1;
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('POST')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        if (isset($headers['user_id']) && !empty($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          $roleid = array(4, 5);
          $userDet = $this->User->find('first',
                  array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
          if (!empty($userDet)) {
            if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
              $store_id = $requestBody['store_id'];
              // Checking Store id parameter is Integer
                $argStore = $this->checkParameter($store_id, 'integer', 'Store'); 
                    if ($argStore['response'] == '403') {
                      return json_encode($argStore);
                    }
              $storeResult = $this->Store->find('first',
                      array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id')));
              if (!empty($storeResult)) {
                if (isset($requestBody['order_id']) && !empty($requestBody['order_id'])) {
                  // Checking Store id parameter is Integer
                    $argOrder = $this->checkParameter($requestBody['order_id'], 'integer', 'Order'); 
                        if ($argOrder['response'] == '403') {
                          return json_encode($argOrder);
                        }
                  $resultFavorite = $this->Favorite->find("all",
                          array('conditions' => array('Favorite.order_id' => $requestBody['order_id'])));
                  if (!empty($resultFavorite)) {
                    $result = $this->Favorite->updateAll(array('Favorite.is_deleted' => 0),
                            array('Favorite.order_id' => $requestBody['order_id']));
                  } else {
                    $data['Favorite']['order_id'] = $requestBody['order_id'];
                    $data['Favorite']['merchant_id'] = $merchant_id;
                    $data['Favorite']['user_id'] = $user_id;
                    $data['Favorite']['store_id'] = $requestBody['store_id'];
                    $result = $this->Favorite->save($data);
                  }
                  if ($result) {
                    $responsedata['message'] = "Order has been added to your favorite list successfully.";
                    $responsedata['response'] = 1;
                    return json_encode($responsedata);
                  } else {
                    $responsedata['message'] = "Order could not be added to your favorite list, please try again.";
                    $responsedata['response'] = 0;
                    return json_encode($responsedata);
                  }
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

  /* ------------- Add to Favorite Code End--------------------- */

  /* ------------- Users Coupon Code Start--------------------- */
  /*   * ******************************************************************************************
    @Function Name : myCoupons
    @Description   : this function is used for list of Users Coupon on merchant ID
    @Author        : SmartData
    created:14/09/2016
   * ****************************************************************************************** */

  public function addRating() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "add_rating.txt", $headers);
    //$requestBody = '{"image":[{"image":"data:image/gif;base64,R0lGODlhPQBEAPeoAJosM//AwO/AwHVYZ/z595kzAP/s7P+goOXMv8+fhw/v739/f+8PD98fH/8mJl+fn/9ZWb8/PzWlwv///6wWGbImAPgTEMImIN9gUFCEm/gDALULDN8PAD6atYdCTX9gUNKlj8wZAKUsAOzZz+UMAOsJAP/Z2ccMDA8PD/95eX5NWvsJCOVNQPtfX/8zM8+QePLl38MGBr8JCP+zs9myn/8GBqwpAP/GxgwJCPny78lzYLgjAJ8vAP9fX/+MjMUcAN8zM/9wcM8ZGcATEL+QePdZWf/29uc/P9cmJu9MTDImIN+/r7+/vz8/P8VNQGNugV8AAF9fX8swMNgTAFlDOICAgPNSUnNWSMQ5MBAQEJE3QPIGAM9AQMqGcG9vb6MhJsEdGM8vLx8fH98AANIWAMuQeL8fABkTEPPQ0OM5OSYdGFl5jo+Pj/+pqcsTE78wMFNGQLYmID4dGPvd3UBAQJmTkP+8vH9QUK+vr8ZWSHpzcJMmILdwcLOGcHRQUHxwcK9PT9DQ0O/v70w5MLypoG8wKOuwsP/g4P/Q0IcwKEswKMl8aJ9fX2xjdOtGRs/Pz+Dg4GImIP8gIH0sKEAwKKmTiKZ8aB/f39Wsl+LFt8dgUE9PT5x5aHBwcP+AgP+WltdgYMyZfyywz78AAAAAAAD///8AAP9mZv///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAKgALAAAAAA9AEQAAAj/AFEJHEiwoMGDCBMqXMiwocAbBww4nEhxoYkUpzJGrMixogkfGUNqlNixJEIDB0SqHGmyJSojM1bKZOmyop0gM3Oe2liTISKMOoPy7GnwY9CjIYcSRYm0aVKSLmE6nfq05QycVLPuhDrxBlCtYJUqNAq2bNWEBj6ZXRuyxZyDRtqwnXvkhACDV+euTeJm1Ki7A73qNWtFiF+/gA95Gly2CJLDhwEHMOUAAuOpLYDEgBxZ4GRTlC1fDnpkM+fOqD6DDj1aZpITp0dtGCDhr+fVuCu3zlg49ijaokTZTo27uG7Gjn2P+hI8+PDPERoUB318bWbfAJ5sUNFcuGRTYUqV/3ogfXp1rWlMc6awJjiAAd2fm4ogXjz56aypOoIde4OE5u/F9x199dlXnnGiHZWEYbGpsAEA3QXYnHwEFliKAgswgJ8LPeiUXGwedCAKABACCN+EA1pYIIYaFlcDhytd51sGAJbo3onOpajiihlO92KHGaUXGwWjUBChjSPiWJuOO/LYIm4v1tXfE6J4gCSJEZ7YgRYUNrkji9P55sF/ogxw5ZkSqIDaZBV6aSGYq/lGZplndkckZ98xoICbTcIJGQAZcNmdmUc210hs35nCyJ58fgmIKX5RQGOZowxaZwYA+JaoKQwswGijBV4C6SiTUmpphMspJx9unX4KaimjDv9aaXOEBteBqmuuxgEHoLX6Kqx+yXqqBANsgCtit4FWQAEkrNbpq7HSOmtwag5w57GrmlJBASEU18ADjUYb3ADTinIttsgSB1oJFfA63bduimuqKB1keqwUhoCSK374wbujvOSu4QG6UvxBRydcpKsav++Ca6G8A6Pr1x2kVMyHwsVxUALDq/krnrhPSOzXG1lUTIoffqGR7Goi2MAxbv6O2kEG56I7CSlRsEFKFVyovDJoIRTg7sugNRDGqCJzJgcKE0ywc0ELm6KBCCJo8DIPFeCWNGcyqNFE06ToAfV0HBRgxsvLThHn1oddQMrXj5DyAQgjEHSAJMWZwS3HPxT/QMbabI/iBCliMLEJKX2EEkomBAUCxRi42VDADxyTYDVogV+wSChqmKxEKCDAYFDFj4OmwbY7bDGdBhtrnTQYOigeChUmc1K3QTnAUfEgGFgAWt88hKA6aCRIXhxnQ1yg3BCayK44EWdkUQcBByEQChFXfCB776aQsG0BIlQgQgE8qO26X1h8cEUep8ngRBnOy74E9QgRgEAC8SvOfQkh7FDBDmS43PmGoIiKUUEGkMEC/PJHgxw0xH74yx/3XnaYRJgMB8obxQW6kL9QYEJ0FIFgByfIL7/IQAlvQwEpnAC7DtLNJCKUoO/w45c44GwCXiAFB/OXAATQryUxdN4LfFiwgjCNYg+kYMIEFkCKDs6PKAIJouyGWMS1FSKJOMRB/BoIxYJIUXFUxNwoIkEKPAgCBZSQHQ1A2EWDfDEUVLyADj5AChSIQW6gu10bE/JG2VnCZGfo4R4d0sdQoBAHhPjhIB94v/wRoRKQWGRHgrhGSQJxCS+0pCZbEhAAOw=="}, {"image":"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAI4AAAB2CAIAAACRXjwrAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDE0IDc5LjE1Njc5NywgMjAxNC8wOC8yMC0wOTo1MzowMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjU1NDYwNzkxMkEyMzExRTY5MjZGREM2OUREMTE0MENGIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjU1NDYwNzkyMkEyMzExRTY5MjZGREM2OUREMTE0MENGIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NTU0NjA3OEYyQTIzMTFFNjkyNkZEQzY5REQxMTQwQ0YiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NTU0NjA3OTAyQTIzMTFFNjkyNkZEQzY5REQxMTQwQ0YiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz6k/GCsAABEoklEQVR42rx9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"}]"StoreReview":{"store_id":108,"order_id":1112,"order_item_id":1854,"item_id":5102,"review_rating":3,"review_comment":"testing 1234","item_name":"Hamburger"}}'


    $requestBody = json_decode($requestBody, true);
    $responsedata = array();
    //pr($requestBody);
    //die;
    //$headers['user_id'] = 'MQ';
    //$headers['merchant_id'] = 1;
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('POST')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        if (isset($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          if (empty($user_id)) {
            $user_id = null;
          }
          $roleid = array(4, 5);
          $userDet = $this->User->find('first',
                  array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
          //if (!empty($userDet)) {
//                        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
//                            $store_id = $requestBody['store_id'];
//                            $storeResult = $this->Store->find('first', array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id')));
//                            if (!empty($storeResult)) {

          if (!empty($requestBody['image'])) {
            $i = 0;
            foreach ($requestBody['image'] as $img) {
              $requestBody['image'][$i] = $requestBody['image'][$i]['image'];
              $i++;
            }
          } else {
            $requestBody['image'] = array();
          }
          $data = $requestBody;
          if(!empty($requestBody['StoreReview']['store_id'])){
            // Checking Store id parameter is Integer
                $argStore = $this->checkParameter($requestBody['StoreReview']['store_id'], 'integer', 'Store'); 
                    if ($argStore['response'] == '403') {
                      return json_encode($argStore);
                    }
          }
          if(!empty($requestBody['StoreReview']['order_id'])){
            // Checking Order id parameter is Integer
                $argOrder = $this->checkParameter($requestBody['StoreReview']['order_id'], 'integer', 'Order'); 
                    if ($argOrder['response'] == '403') {
                      return json_encode($argOrder);
                    }
          }
          if(!empty($requestBody['StoreReview']['order_item_id'])){
            // Checking Order item id parameter is Integer
                $argOrderItem= $this->checkParameter($requestBody['StoreReview']['order_item_id'], 'integer', 'Order item'); 
                    if ($argOrderItem['response'] == '403') {
                      return json_encode($argOrderItem);
                    }
          }
          if(!empty($requestBody['StoreReview']['item_id'])){
            // Checking Item id parameter is Integer
                $argItem = $this->checkParameter($requestBody['StoreReview']['item_id'], 'integer', 'Item'); 
                    if ($argItem['response'] == '403') {
                      return json_encode($argItem);
                    }
          }
          if(!empty($requestBody['StoreReview']['review_rating'])){
            // Checking Review rating parameter is Integer
                $argReviewRating = $this->checkParameter($requestBody['StoreReview']['review_rating'],'integer','Review rating'); 
                    if ($argReviewRating['response'] == '403') {
                      return json_encode($argReviewRating);
                    }
          }
          if(!empty($requestBody['StoreReview']['review_comment'])){
            // Checking Review Comment parameter is string
              $argReviewComment = $this->checkParameter($requestBody['StoreReview']['review_comment'],'string','Review Comment');                if ($argReviewComment['response'] == '403') {
                      return json_encode($argReviewComment);
                    }
          }
          if(!empty($requestBody['StoreReview']['item_name'])){
            // Checking Item name parameter is string
                $argItemName = $this->checkParameter($requestBody['StoreReview']['item_name'], 'string', 'Item name'); 
                    if ($argItemName['response'] == '403') {
                      return json_encode($argItemName);
                    }
          }
          
          
          $store_id = $requestBody['StoreReview']['store_id'];
          $data['StoreReview']['store_id'] = $store_id;
          $data['StoreReview']['merchant_id'] = $merchant_id;
          $data['StoreReview']['user_id'] = $user_id;
          $data['StoreReview']['is_active'] = 1;
          $data['StoreReview']['is_approved'] = 1;
          $data['StoreReview']['is_deleted'] = 0;
          $data['StoreReview']['review_rating'] = $requestBody['StoreReview']['review_rating'];
          $data['StoreReview']['review_comment'] = $requestBody['StoreReview']['review_comment'];
          $data['StoreReview']['item_id'] = $requestBody['StoreReview']['item_id'];
          $data['StoreReview']['order_id'] = $requestBody['StoreReview']['order_id'];
          $data['StoreReview']['order_item_id'] = $requestBody['StoreReview']['order_item_id'];
          $data['StoreReview']['item_name'] = $requestBody['StoreReview']['item_name'];
          $storeId = $requestBody['StoreReview']['store_id'];
          $this->StoreReview->create();
          if ($this->StoreReview->save($data)) {
            $storeReviewId = $this->StoreReview->getLastInsertID();
            $data['image'] = array_filter($data['image']);
            if (!empty($storeReviewId) && !empty($data['image'])) {
              if (count($data['image']) <= 5) {
                foreach ($data['image'] as $key => $val) {

                  $dat = explode(';', $val);
                  $type = $dat[0];
                  $data2 = $dat[1];
                  $dat2 = explode(',', $data2);
                  //list(, $data1) = explode(',', $data1);
                  $data3 = base64_decode($dat2[1]);
                  $jpgMimes = array('image/jpeg', 'image/pjpeg');
                  $pngMimes = array('image/png');
                  $gifMimes = array('image/gif');

                  $imgdata = base64_decode($dat2[1]);
                  $f = finfo_open();
                  $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
                  if (in_array($mime_type, $jpgMimes)) {
                    $imageType = "jpg";
                    $imagename = uniqid() . "_" . date('Y-m-d-H-s') . '_' . $store_id . '_image.jpg';
                  } else if (in_array($mime_type, $pngMimes)) {
                    $imageType = "png";
                    $imagename = uniqid() . "_" . date('Y-m-d-H-s') . '_' . $store_id . '_image.png';
                  } else if (in_array($mime_type, $gifMimes)) {
                    $imageType = "gif";
                    $imagename = uniqid() . "_" . date('Y-m-d-H-s') . '_' . $store_id . '_image.gif';
                  } else {
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
                    $ImageStatus = $this->Webservice->cropImage($path,
                            $folderName, $imagename, $newWidth, $newHeight,
                            $imageType);
                    if ($ImageStatus) {
                      $imageData['image'] = $imagename;
                    }
                    $imageData['store_id'] = $requestBody['StoreReview']['store_id'];
                    $imageData['created'] = date("Y-m-d H:i:s");
                    $imageData['is_active'] = 1;
                    $imageData['store_review_id'] = $storeReviewId;
                    $this->StoreReviewImage->create();
                    $this->StoreReviewImage->save($imageData);
                  }
                }
              } else {
                $responsedata['message'] = "You can upload upto 5 images.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
              }
            }
            $template_type = 'review_rating';
            $this->loadModel('EmailTemplate');
            $fullName = "Admin";
            $item_name = $data['StoreReview']['item_name'];
            $review = $data['StoreReview']['review_comment']; //no of person
            $rating = $data['StoreReview']['review_rating'];
            $userDetail = $this->User->find("first",
                    array('conditions' => array('User.id' => $user_id, 'User.is_active' => 1, 'User.is_deleted' => 0), 'fields' => array('User.id', 'User.email', 'User.fname', 'User.lname', 'User.phone', 'User.country_code_id')));
            if (!empty($userDetail)) {
              $user_email = $userDetail['User']['email'];
              $customer_name = $userDetail['User']['fname'] . " " . $userDetail['User']['lname'];
              $emailSuccess = $this->EmailTemplate->storeTemplates($data['StoreReview']['store_id'],
                      $data['StoreReview']['merchant_id'], $template_type);
              $store = $this->Store->fetchStoreDetail($data['StoreReview']['store_id'],
                      $data['StoreReview']['merchant_id']);
              if ($emailSuccess) {
                if (($store['Store']['notification_type'] == 1 || $store['Store']['notification_type'] == 3) && (!empty($store['Store']['notification_email']))) {
                  $storeEmail = trim($store['Store']['notification_email']);
                } else {
                  $storeEmail = trim($store['Store']['email_id']);
                }

                $customerEmail = trim($user_email);
                $emailData = $emailSuccess['EmailTemplate']['template_message'];
                $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                $emailData = str_replace('{REVIEW}', $review, $emailData);
                $emailData = str_replace('{RATING}', $rating, $emailData);
                $emailData = str_replace('{ITEM_NAME}', $item_name, $emailData);
                $emailData = str_replace('{CUSTOMER_NAME}', $customer_name,
                        $emailData);
                $storeAddress = $store['Store']['address'] . "<br>" . $store['Store']['city'] . ", " . $store['Store']['state'] . " " . $store['Store']['zipcode'];
                $storePhone = $store['Store']['phone'];
                $url = "http://" . $store['Store']['store_url'];
                $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $store['Store']['store_url'] . "</a>";
                $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                $emailData = str_replace('{STORE_ADDRESS}', $storeAddress,
                        $emailData);
                $emailData = str_replace('{STORE_PHONE}', $storePhone,
                        $emailData);
                $subject = ucwords(str_replace('_', ' ',
                                $emailSuccess['EmailTemplate']['template_subject']));
                $this->Email->to = $storeEmail;
                $this->Email->subject = $subject;
                //$this->Email->from = $customerEmail;
                $this->Email->from = $this->front_email;
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

                if (($store['Store']['notification_type'] == 2 || $store['Store']['notification_type'] == 3) && (!empty($store['Store']['notification_number']))) {
                  $mobnumber = '+1' . str_replace(array('(', ')', ' ', '-'), '',
                                  $store['Store']['notification_number']);
                } else {
                  $mobnumber = '+1' . str_replace(array('(', ')', ' ', '-'), '',
                                  $store['Store']['phone']);
                }
                $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                $smsData = str_replace('{REVIEW}', $review, $smsData);
                $smsData = str_replace('{RATING}', $rating, $smsData);
                $smsData = str_replace('{ITEM_NAME}', $item_name, $smsData);
                $smsData = str_replace('{CUSTOMER_NAME}', $customer_name,
                        $smsData);
                $smsData = str_replace('{STORE_NAME}',
                        $store['Store']['store_name'], $smsData);
                $smsData = str_replace('{STORE_PHONE}', $mobnumber, $smsData);
                $message = $smsData;
                $this->Webservice->sendSmsNotificationFront($mobnumber,
                        $message, $store['Store']['id']);
              }
            }
            $responsedata['message'] = "Rating & Review has been saved successfully.";
            $responsedata['response'] = 1;
            return json_encode($responsedata);
          } else {
            $responsedata['message'] = "Rating & Review could not be saved, please try again.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
          }
          //} else {
          //    $responsedata['message'] = "You are not register under this merchant";
          //    $responsedata['response'] = 0;
          //    return json_encode($responsedata);
          //}
        } else {
          $responsedata['message'] = "Please login or continue as a guest.";
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

  /* ------------- Users Coupon Code End--------------------- */

  //function notification($orderId,$store_id,$merchant_id,$segment_type)
  function notification($orderId, $store_id, $merchant_id, $user_id) {
    $this->loadModel('Item');
    $this->loadModel('OrderOffer');
    $this->loadModel('OrderItem');
    $this->loadModel('Order');
    $this->loadModel('EmailTemplate');
    $this->loadModel('CountryCode');

//        $orderId=1070;
//        $store_id = 2;
//        $merchant_id = 1;
    // ????? ???? ??? ???? ???. if $paymemt_type == 4 return;

    $printdata = $this->Webservice->getOrderFaxFormat($orderId, $store_id,
            $merchant_id);
    $this->Item->bindModel(array('belongsTo' => array('Category' => array('foreignKey' => 'category_id', 'fields' => array('name')))));
    $this->Order->bindModel(array('belongsTo' => array('Segment' => array('foreignKey' => 'seqment_id', 'fields' => array('name')))));
    $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')))));
    $this->OrderItem->bindModel(array('hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name', 'category_id')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')))));
    $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id'))), 'belongsTo' => array('OrderPayment' => array('className' => 'OrderPayment', 'foreignKey' => 'payment_id', 'fields' => array('id', 'transection_id', 'amount')))));
    $result_order = $this->Order->getfirstOrder($merchant_id, $store_id,
            $orderId);
    $segment_type = $result_order['Order']['seqment_id'];



    if ($result_order) {
      $this->loadModel('Store');
      $storeEmail = $this->Store->fetchStoreDetail($store_id);
      $order_type = "";
      if (!empty($result_order['Segment']['name'])) {
        $order_type = $result_order['Segment']['name'];
      }
      if ($result_order['Order']['is_pre_order'] == 1) {
        $template_type = 'pre_order_receipt';
      } else {
        if ($result_order['Order']['seqment_id'] == 3) {
          //$template_type = 'order_receipt';
          $template_type = 'pre_order_receipt';
        } else {
          //$template_type = 'pickup_order_receipt';
          $template_type = 'pre_order_receipt';
        }
      }
      if ($user_id) {
        $userDetail = $this->User->find("first",
                array('conditions' => array('User.id' => $user_id, 'User.is_active' => 1, 'User.is_deleted' => 0), 'fields' => array('User.id', 'User.email', 'User.fname', 'User.phone', 'User.country_code_id')));
        $user_email = $userDetail['User']['email'];
        $fullName = $userDetail['User']['fname'];
        $phone = $userDetail['User']['phone'];
        $country_code = $this->CountryCode->fetchCountryCodeId($userDetail['User']['country_code_id']);
      } else {
        $userid = '';
        $this->loadModel('DeliveryAddress');
        $delivery_address_id = $result_order['Order']['delivery_address_id'];
        $delivery_address = $this->DeliveryAddress->fetchAddress($delivery_address_id,
                $userid, $store_id);
        $country_code = $this->CountryCode->fetchCountryCodeId($delivery_address['DeliveryAddress']['country_code_id']);
        $user_email = $delivery_address['DeliveryAddress']['email'];
        $phone = $delivery_address['DeliveryAddress']['phone'];
        $fullName = $delivery_address['DeliveryAddress']['name_on_bell'];
      }

      $emailSuccess = $this->EmailTemplate->storeTemplates($store_id,
              $merchant_id, $template_type);
      if ($emailSuccess) {
        $emailData = $emailSuccess['EmailTemplate']['template_message'];
        $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
        $preorderDateTime = $this->Webservice->storeTimeFormateUser($result_order['Order']['pickup_time'],
                true, $store_id);
        if (isset($preorderDateTime) && !empty($preorderDateTime)) {
          $orderDateTime = explode(" ", $preorderDateTime);
          $date = $orderDateTime[0];
          $time = $orderDateTime[1];
          if (isset($orderDateTime[2]) && !empty($orderDateTime[2])) {
            $storeTimeAm = trim($orderDateTime[2]);
            $time = $time . $storeTimeAm;
          }
        }
        //echo $result_order['Order']['pickup_time']."<br>";
        //echo $preorderDateTime;die;
        $emailData = str_replace('{PRE_ORDER_DATE_TIME}', $preorderDateTime,
                $emailData);
        $emailData = str_replace('{ORDER_DETAIL}', $printdata, $emailData);
        $emailData = str_replace('Order Id:', '', $emailData);
        $emailData = str_replace('{ORDER_ID}', '', $emailData);
        $emailData = str_replace('Total Amount:', '', $emailData);
        $emailData = str_replace('{TOTAL}', '', $emailData);
        $emailData = str_replace('Transaction Id :', '', $emailData);
        $emailData = str_replace('{TRANSACTION_ID}', '', $emailData);
        $emailData = str_replace('{STORE_NAME}',
                $storeEmail['Store']['store_name'], $emailData);
        $url = "http://" . $storeEmail['Store']['store_url'];
        $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
        $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
        $storeAddress = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
        $storePhone = $storeEmail['Store']['phone'];
        $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
        $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);

        // $subject = ucwords(str_replace('_', ' ', $emailSuccess['EmailTemplate']['template_subject']));
        $orderType = ($segment_type == 2) ? "Pick-up" : "Delivery";
        $newSubject = "Your " . $storeEmail['Store']['store_name'] . " Online Order Confirmation #" . $result_order['Order']['order_number'] . "/" . $orderType;
        $this->Email->to = $user_email;
        $this->Email->subject = $newSubject;
        $this->Email->from = $storeEmail['Store']['email_id'];
        $this->set('data', $emailData);
        $this->Email->template = 'template';
        $this->Email->smtpOptions = array(
            'port' => "$this->smtp_port",
            'timeout' => '100',
            'host' => "$this->smtp_host",
            'username' => "$this->smtp_username",
            'password' => "$this->smtp_password"
        );
        $this->Email->sendAs = 'html'; // because we like to send pretty mail
        // $this->Email->delivery ='smtp';
        try {
          $this->Email->send();
          $this->loadModel('DefaultTemplate');
          $template_type = 'order_notification';
          $emailTemplate = $this->DefaultTemplate->adminTemplates($template_type);
          $storeEmailData = $emailTemplate['DefaultTemplate']['template_message'];
          $storesmsData = $emailTemplate['DefaultTemplate']['sms_template'];

          //Store ORder Email Notification
          if (($storeEmail['Store']['notification_type'] == 1 || $storeEmail['Store']['notification_type'] == 3) && (!empty($storeEmail['Store']['notification_email']))) {
            $EncorderID = $this->Encryption->encode($orderId);
            $surl = HTTP_ROOT . 'orders/confirmOrder/' . $EncorderID;
            $orderconHtml = '<table style="width:100%;height:100px;" border="0" cellpadding="10" cellspacing="0"><tbody><tr><td style="text-align:center;">';
            $orderconHtml .= '<a href="' . $surl . '" style="padding:15px 15px;background-color:#F1592A;color:#FFFFFF;font-weight:bold;text-decoration: none;border:1px solid #000000;">CONFIRM ORDER</a></td></tr></tbody></table> ';

            $storeEmailData = $orderconHtml . $printdata;
            $subject = ucwords(str_replace('_', ' ',
                            $emailTemplate['DefaultTemplate']['template_subject']));

            $this->Email->to = $storeEmail['Store']['notification_email'];
            $this->Email->subject = $subject;
            $this->Email->from = $storeEmail['Store']['email_id'];
            $this->set('data', $storeEmailData);
            $this->Email->template = 'template';
            $this->Email->smtpOptions = array(
                'port' => "$this->smtp_port",
                'timeout' => '100',
                'host' => "$this->smtp_host",
                'username' => "$this->smtp_username",
                'password' => "$this->smtp_password"
            );
            $this->Email->sendAs = 'html';
            $this->Email->send();
          }
          // Store ORder Email Notification
          // STore Order Notification via SMS

          if (!empty($storeEmail['Store']['twilio_api_key']) && !empty($storeEmail['Store']['twilio_api_token']) && !empty($storeEmail['Store']['twilio_number'])) {
            if (($storeEmail['Store']['notification_type'] == 2 || $storeEmail['Store']['notification_type'] == 3) && (!empty($storeEmail['Store']['notification_number']))) {
              $storemobnumber = $country_code['CountryCode']['code'] . str_replace(array('(', ')', ' ', '-'),
                              '', $storeEmail['Store']['notification_number']);
              if ($storesmsData) {
                $storesmsData = str_replace('{ORDER_NUMBER}',
                        $result_order['Order']['order_number'], $storesmsData);
                $storesmsData = str_replace('{STORE_NAME}',
                        $storeEmail['Store']['store_name'], $storesmsData);
                $storesmsData = str_replace('{ORDER_DATE}', $date, $storesmsData);
                $storesmsData = str_replace('{ORDER_TIME}', $time, $storesmsData);
                $storesmsData = str_replace('{ORDER_TYPE}', $order_type,
                        $storesmsData);
                $storesmsData = str_replace('{STORE_PHONE}',
                        $storeEmail['Store']['notification_number'],
                        $storesmsData);
                $this->Webservice->sendSmsNotificationFront($storemobnumber,
                        $storesmsData, $store_id);
              }
            }
            $mobnumber = $country_code['CountryCode']['code'] . str_replace(array('(', ')', ' ', '-'),
                            '', $phone);
            $smsData = $emailSuccess['EmailTemplate']['sms_template'];
            $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
            $smsData = str_replace('{ORDER_STATUS}', 'Pending', $smsData);
            $smsData = str_replace('{ORDER_NUMBER}',
                    $result_order['Order']['order_number'], $smsData);
            $smsData = str_replace('{ORDER_DATE}', $date, $smsData);
            $smsData = str_replace('{ORDER_TIME}', $time, $smsData);
            $smsData = str_replace('{ORDER_TYPE}', $order_type, $smsData);
            $smsData = str_replace('{STORE_NAME}',
                    $storeEmail['Store']['store_name'], $smsData);
            $smsData = str_replace('{PRE_ORDER_DATE_TIME}', $preorderDateTime,
                    $smsData);
            $smsData = str_replace('{STORE_PHONE}',
                    $storeEmail['Store']['notification_number'], $smsData);
            $message = $smsData;
            $this->Webservice->sendSmsNotificationFront($mobnumber, $message,
                    $store_id);
          }
          //STore Order Notification via SMS
        } catch (Exception $e) {
          
        }
      }
    }
    try {
      $this->orderFaxrelay($orderId, $store_id, $merchant_id, $printdata);
    } catch (Exception $e) {
      
    }
  }

  public function orderFaxrelay($orderId = null, $storeID = null,
          $merchant_id = null, $printdata = null) {
    if (isset($printdata) && !empty($printdata)) {
      $printdata = $printdata;
    } else {
      $printdata = $this->Webservice->getOrderFaxFormat($orderId, $storeID,
              $merchant_id);
    }

    //$username = 'ecomm2015'; // Enter your Interfax username here
    //$password = 'ecomm2015'; // Enter your Interfax password here
    $this->loadModel('Store');
    //$storeID = $this->Session->read('admin_store_id');
    $storeInfo = $this->Store->fetchStoreDetail($storeID);
    $faxnumber = $storeInfo['Store']['fax_number']; // Enter your designated fax number here in the format +[country code][area code][fax number], for example: +12125554874
    $username = $storeInfo['Store']['fax_username'];
    $password = $storeInfo['Store']['fax_password'];

    if (!empty($faxnumber) && !empty($username) && !empty($password)) {
      $filetype = 'HTML';
      try {
        $params = (object) [];
        $client = new SoapClient("http://ws.interfax.net/dfs.asmx?wsdl");
        $params->Username = $username;
        $params->Password = $password;
        $params->FaxNumber = $faxnumber;
        $params->Data = $printdata;
        $params->FileType = $filetype;
        $faxResult = $client->SendCharFax($params);
      } catch (Exception $e) {
        
      }
    }
  }

  function notificationFail($reason, $store_id, $merchant_id, $user_id,
          $delivery_address_id) {

    $this->loadModel('EmailTemplate');
    $this->loadModel('CountryCode');
//        $store_id=2;
//        $merchant_id=1;
//        $user_id=1;
//        $delivery_address_id=1;
    if ($user_id) {
      $userDetail = $this->User->find("first",
              array('conditions' => array('User.id' => $user_id, 'User.is_active' => 1, 'User.is_deleted' => 0), 'fields' => array('User.id', 'User.email', 'User.fname', 'User.phone', 'User.country_code_id')));
      $user_email = $userDetail['User']['email'];
      $fullName = $userDetail['User']['fname'];
      $phone = $userDetail['User']['phone'];
      $country_code = $this->CountryCode->fetchCountryCodeId($userDetail['User']['country_code_id']);
    } else {
      $userid = '';
      $this->loadModel('DeliveryAddress');
      $delivery_address_id = $delivery_address_id;
      $delivery_address = $this->DeliveryAddress->fetchAddress($delivery_address_id,
              $userid, $store_id);
      $country_code = $this->CountryCode->fetchCountryCodeId($delivery_address['DeliveryAddress']['country_code_id']);
      $user_email = $delivery_address['DeliveryAddress']['email'];
      $phone = $delivery_address['DeliveryAddress']['phone'];
      $fullName = $delivery_address['DeliveryAddress']['name_on_bell'];
    }

    $emailSuccess = $this->EmailTemplate->storeTemplates($store_id,
            $merchant_id, 'payment_error');
    $storeEmail = $this->Store->fetchStoreDetail($store_id);
    $storeAddressemail = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
    $storePhoneemail = $storeEmail['Store']['phone'];
    if ($emailSuccess) {
      $emailData = $emailSuccess['EmailTemplate']['template_message'];
      $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
      $emailData = str_replace('{STORE_NAME}',
              $storeEmail['Store']['store_name'], $emailData);
      $emailData = str_replace('{REASON}', $reason, $emailData);
      $url = "http://" . $storeEmail['Store']['store_url'];
      $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
      $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
      $emailData = str_replace('{STORE_ADDRESS}', $storeAddressemail, $emailData);
      $emailData = str_replace('{STORE_PHONE}', $storePhoneemail, $emailData);
      $subject = ucwords(str_replace('_', ' ',
                      $emailSuccess['EmailTemplate']['template_subject']));
      $this->Email->to = $user_email;
      $this->Email->subject = $subject;
      $this->Email->from = $storeEmail['Store']['email_id'];
      $this->set('data', $emailData);
      $this->Email->template = 'template';
      $this->Email->smtpOptions = array(
          'port' => "$this->smtp_port",
          'timeout' => '100',
          'host' => "$this->smtp_host",
          'username' => "$this->smtp_username",
          'password' => "$this->smtp_password"
      );
      $this->Email->sendAs = 'html'; // because we like to send pretty mail
      // $this->Email->delivery ='smtp';
      try {
        $this->Email->send();
      } catch (Exception $e) {
        
      }
      if (!empty($storeEmail['Store']['twilio_api_key']) && !empty($storeEmail['Store']['twilio_api_token']) && !empty($storeEmail['Store']['twilio_number'])) {
        $mobnumber = $country_code['CountryCode']['code'] . str_replace(array('(', ')', ' ', '-'),
                        '', $phone);
        $smsData = $emailSuccess['EmailTemplate']['sms_template'];
        $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
        $smsData = str_replace('{STORE_NAME}',
                $storeEmail['Store']['store_name'], $smsData);
        $smsData = str_replace('{REASON}', $reason, $smsData);
        $smsData = str_replace('{STORE_NAME}',
                $storeEmail['Store']['store_name'], $smsData);
        $smsData = str_replace('{STORE_PHONE}',
                $storeEmail['Store']['notification_number'], $smsData);
        $message = $smsData;
        $this->Webservice->sendSmsNotificationFront($mobnumber, $message,
                $store_id);
      }
    }
  }

  /*   * ******************************************************************************************
    @Function Name : RemoveCoupons
    @Description   : this function is used to Delete the coupons on merchant ID
    @Author        : SmartData
    created:1/11/2016
   * ****************************************************************************************** */

  public function RemoveCoupon() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "delete_Coupon.txt", $headers);
    //$requestBody =  '{"userCoupon_id":939}';
    $requestBody = json_decode($requestBody, true);
    $responsedata = array();
    //$headers['user_id'] = 'MQ';
    //$headers['merchant_id'] = 1;
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('DELETE')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        if (isset($headers['user_id']) && !empty($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          $roleid = array(4, 5);
          $userDet = $this->User->find('first',
                  array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
          if (!empty($userDet)) {
            if (isset($requestBody['userCoupon_id']) && !empty($requestBody['userCoupon_id'])) {
              // Checking Store id parameter is Integer
                $argCoupon = $this->checkParameter($requestBody['userCoupon_id'], 'integer', 'Coupon'); 
                    if ($argCoupon['response'] == '403') {
                      return json_encode($argCoupon);
                    }
              $UserCouponResult = $this->UserCoupon->find('first',
                      array('conditions' => array('UserCoupon.id' => $requestBody['userCoupon_id'], 'UserCoupon.user_id' => $user_id)));
              if (!empty($UserCouponResult)) {
                $data['UserCoupon']['is_deleted'] = 1;
                $data['UserCoupon']['id'] = $requestBody['userCoupon_id'];
                if ($this->UserCoupon->saveUserCoupon($data)) {
                  $responsedata['message'] = "Coupon has been deleted successfully.";
                  $responsedata['response'] = 1;
                  return json_encode($responsedata);
                } else {
                  $responsedata['message'] = "Coupon could not be deleted, please try again.";
                  $responsedata['response'] = 0;
                  return json_encode($responsedata);
                }
              } else {
                $responsedata['message'] = "Coupon could not be deleted, please try again.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
              }
            } else {
              $responsedata['message'] = "Please select a coupon.";
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

  /*   * ******************************************************************************************
    @Function Name : myBooking
    @Description   : this function is used to show list of Booking booked by user on merchant ID
    @Author        : SmartData
    created:26/10/2016
   * ****************************************************************************************** */

  public function myBooking() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "myBooking.txt", $headers);
    $requestBody = json_decode($requestBody, true);
    $responsedata = array();
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {
        if (isset($headers['user_id']) && !empty($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          $roleid = array(4, 5);
          $userDet = $this->User->find('first',
                  array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
          if (!empty($userDet)) {
            $this->Booking->bindModel(array('belongsTo' => array('BookingStatus')),
                    false);
            $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
            $this->Booking->bindModel(array(
                'belongsTo' => array(
                    'Store' => array(
                        'className' => 'Store',
                        'foreignKey' => 'store_id',
                        'fields' => array('id', 'store_name', 'store_url'),
                        'type' => 'INNER',
                        'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1)
                    ))
                    ), false);
            $myBookings = $this->Booking->find('all',
                    array('order' => 'Booking.created DESC', 'recursive' => 3, 'conditions' => array('Booking.user_id' => $user_id, 'Booking.is_active' => 1, 'Booking.is_deleted' => 0)));
            $encrypted_merchantId = $this->Encryption->encode($merchant_id);
            if (!empty($myBookings)) {
              $i = 0;
              foreach ($myBookings as $bookingList) {
                $bookingListing[$i]['id'] = $bookingList['Booking']['id'];
                $bookingListing[$i]['number_person'] = $bookingList['Booking']['number_person'];
                $bookingListing[$i]['reservation_date'] = $bookingList['Booking']['reservation_date'];
                $bookingListing[$i]['status'] = $bookingList['BookingStatus']['name'];
                $bookingListing[$i]['store_id'] = $bookingList['Store']['id'];
                $bookingListing[$i]['store_name'] = $bookingList['Store']['store_name'];
                $encrypted_storeId = $this->Encryption->encode($bookingList['Store']['id']);
                if (!empty($bookingList['Store']['store_url'])) {
                  $domain = $bookingList['Store']['store_url'];
                  $bookingListing[$i]['link'] = 'http://' . $domain . "/orders/myFavorites/" . $encrypted_storeId . "/" . $encrypted_merchantId;
                } else {
                  $bookingListing[$i]['link'] = 'http://' . $domain;
                }
                $i++;
              }
            } else {
              $responsedata['message'] = "No record found.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            }
            if (!empty($bookingListing)) {
              $responsedata['message'] = "Success";
              $responsedata['response'] = 1;
              $responsedata['myBooking'] = array_values($bookingListing);
              return json_encode($responsedata);
            } else {
              $responsedata['message'] = "Record not found.";
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

  /*   * ******************************************************************************************
    @Function Name : RemoveBookings
    @Description   : this function is used to Delete the Bookings on merchant ID
    @Author        : SmartData
    created:1/11/2016
   * ****************************************************************************************** */

  public function RemoveBooking() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "delete_Coupon.txt", $headers);
    //$requestBody =  '{"booking_id": 939}';
    $requestBody = json_decode($requestBody, true);
    $responsedata = array();
    //$headers['user_id'] = 'MQ';
    //$headers['merchant_id'] = 1;
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('DELETE')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        if (isset($headers['user_id']) && !empty($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          $roleid = array(4, 5);
          $userDet = $this->User->find('first',
                  array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
          if (!empty($userDet)) {
            if (isset($requestBody['booking_id']) && !empty($requestBody['booking_id'])) {
              // Checking Store id parameter is Integer
                $argBooking = $this->checkParameter($requestBody['booking_id'], 'integer', 'Booking'); 
                    if ($argBooking['response'] == '403') {
                      return json_encode($argBooking);
                    }
              $bookResult = $this->Booking->find('first',
                      array('conditions' => array('Booking.id' => $requestBody['booking_id'], 'Booking.user_id' => $user_id)));
              if (!empty($bookResult)) {
                $data['Booking']['id'] = $requestBody['booking_id'];
                $data['Booking']['is_deleted'] = 1;
                if ($this->Booking->saveBookingDetails($data)) {
                  $responsedata['message'] = "Reservation has been deleted successfully.";
                  $responsedata['response'] = 1;
                  return json_encode($responsedata);
                } else {
                  $responsedata['message'] = "Reservation could not be deleted, please try again.";
                  $responsedata['response'] = 0;
                  return json_encode($responsedata);
                }
              } else {
                $responsedata['message'] = "Reservation could not be deleted, please try again.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
              }
            } else {
              $responsedata['message'] = "Please select a booking.";
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

  /*   * ******************************************************************************************
    @Function Name : mySavedOrders
    @Description   : this function is used to show list of Users Saved Orders on merchant ID
    @Author        : SmartData
    created:26/10/2016
   * ****************************************************************************************** */

  public function mySavedOrders() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "myBooking.txt", $headers);
    $requestBody = json_decode($requestBody, true);
    $responsedata = array();
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {
        if (isset($headers['user_id']) && !empty($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          $roleid = array(4, 5);
          $userDet = $this->User->find('first',
                  array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
          if (!empty($userDet)) {
            $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))),
                    false);
            $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('fields' => array('name')))),
                    false);
            $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')))),
                    false);
            $this->OrderItem->bindModel(array('hasOne' => array('StoreReview' => array('fields' => array('review_rating', 'is_approved'))), 'hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity')), 'OrderTopping' => array('fields' => array('id', 'topping_id')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')))),
                    false);
            $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id', 'interval_id'))), 'belongsTo' => array('DeliveryAddress' => array('fields' => array('name_on_bell', 'city', 'address')), 'User' => array('fields' => array('id', 'fname', 'lname', 'address', 'city', 'state')), 'OrderStatus' => array('fields' => array('name')))),
                    false);
            $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
            $this->Order->bindModel(array(
                'belongsTo' => array(
                    'Store' => array(
                        'className' => 'Store',
                        'foreignKey' => 'store_id',
                        'fields' => array('id', 'store_name', 'store_url'),
                        'type' => 'INNER',
                        'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1)
                    ))
                    ), false);

            $mySavedOrders = $this->Order->find('all',
                    array('order' => 'Order.created DESC', 'recursive' => 3, 'conditions' => array('Order.merchant_id' => $merchant_id, 'Order.user_id' => $user_id, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 1)));
            $encrypted_merchantId = $this->Encryption->encode($merchant_id);
            $myOrdersList = array();
            if (!empty($mySavedOrders)) {
              foreach ($mySavedOrders as $o => $listOrder) {
                $myOrdersList[$o]['order_id'] = $listOrder['Order']['id'];
                $myOrdersList[$o]['order_number'] = $listOrder['Order']['order_number'];
                $myOrdersList[$o]['total_amount'] = '$' . $listOrder['Order']['amount'];
                $myOrdersList[$o]['placed_date'] = date('m-d-Y h:m a',
                        strtotime($listOrder['Order']['created']));
                if (!empty($listOrder['Order']['seqment_id']) && $listOrder['Order']['seqment_id'] == 2) {
                  $myOrdersList[$o]['order_type'] = 'Carry-out';
                } elseif (!empty($listOrder['Order']['seqment_id']) && $listOrder['Order']['seqment_id'] == 3) {
                  $myOrdersList[$o]['order_type'] = 'Delivery';
                }
                if (!empty($listOrder['DeliveryAddress']['name_on_bell'])) {
                  $myOrdersList[$o]['name_on_bell'] = $listOrder['DeliveryAddress']['name_on_bell'];
                } elseif (!empty($listOrder['User']['fname'])) {
                  $myOrdersList[$o]['name_on_bell'] = $listOrder['User']['fname'] . ' ' . $listOrder['User']['lname'];
                } else {
                  $myOrdersList[$o]['name_on_bell'] = "";
                }
                if (!empty($listOrder['DeliveryAddress']['city'])) {
                  $myOrdersList[$o]['city'] = $listOrder['DeliveryAddress']['city'];
                } elseif (!empty($listOrder['User']['city'])) {
                  $myOrdersList[$o]['city'] = $listOrder['User']['city'];
                } else {
                  $myOrdersList[$o]['city'] = "";
                }
                if (!empty($listOrder['DeliveryAddress']['address'])) {
                  $myOrdersList[$o]['address'] = $listOrder['DeliveryAddress']['address'];
                } elseif (!empty($listOrder['User']['address'])) {
                  $myOrdersList[$o]['address'] = $listOrder['User']['address'];
                } else {
                  $myOrdersList[$o]['address'] = "";
                }

                //$myOrdersList[$o]['OrderStatus'] = $listOrder['OrderStatus']['name'];
                $myOrdersList[$o]['store_id'] = $listOrder['Store']['id'];
                $myOrdersList[$o]['store_name'] = $listOrder['Store']['store_name'];
                $encrypted_storeId = $this->Encryption->encode($listOrder['Store']['id']);
                if (!empty($listOrder['Store']['store_url'])) {
                  $domain = $listOrder['Store']['store_url'];
                  $myOrdersList[$o]['link'] = 'http://' . $domain . "/orders/mySavedOrders/" . $encrypted_storeId . "/" . $encrypted_merchantId;
                } else {
                  $myOrdersList[$o]['link'] = 'http://' . $domain;
                }

                if (!empty($listOrder['OrderItem'])) {
                  foreach ($listOrder['OrderItem'] as $oI => $listOrderItem) {
                    $myOrdersList[$o]['items'][$oI]['item_id'] = $listOrderItem['item_id'];
                    $myOrdersList[$o]['items'][$oI]['order_item_id'] = $listOrderItem['id'];
                    $myOrdersList[$o]['items'][$oI]['item_name'] = $listOrderItem['Item']['name'];
                    $myOrdersList[$o]['items'][$oI]['quantity'] = $listOrderItem['quantity'];
                    if (!empty($listOrderItem['Size'])) {
                      $myOrdersList[$o]['items'][$oI]['size_id'] = $listOrderItem['size_id'];
                      $myOrdersList[$o]['items'][$oI]['size_name'] = $listOrderItem['Size']['size'];
                    } else {
                      $myOrdersList[$o]['items'][$oI]['size_id'] = "";
                      $myOrdersList[$o]['items'][$oI]['size_name'] = "";
                    }

                    if (!empty($listOrderItem['OrderTopping'])) {
                      foreach ($listOrderItem['OrderTopping'] as $key2 => $mfot) {
                        if (!empty($mfot['Topping'])) {
                          $myOrdersList[$o]['items'][$oI]['subAddons'][$key2]['id'] = @$mfot['topping_id'];
                          $myOrdersList[$o]['items'][$oI]['subAddons'][$key2]['name'] = @$mfot['Topping']['name'];
                        } else {
                          $myOrdersList[$o]['items'][$oI]['subAddons'] = array();
                        }
                      }
                    } else {
                      $myOrdersList[$o]['items'][$oI]['subAddons'] = array();
                    }
                    if (!empty($listOrderItem['OrderPreference'])) {
                      foreach ($listOrderItem['OrderPreference'] as $key3 => $mfop) {
                        if (!empty($mfop['SubPreference'])) {
                          $myOrdersList[$o]['items'][$oI]['subpreferences'][$key3]['id'] = @$mfop['sub_preference_id'];
                          $myOrdersList[$o]['items'][$oI]['subpreferences'][$key3]['subpreference_name'] = @$mfop['SubPreference']['name'];
                        } else {
                          $myOrdersList[$o]['items'][$oI]['subpreferences'] = array();
                        }
                      }
                    } else {
                      $myOrdersList[$o]['items'][$oI]['subpreferences'] = array();
                    }

                    if (!empty($listOrderItem['OrderOffer'])) {
                      foreach ($listOrderItem['OrderOffer'] as $key4 => $mfOffer) {
                        $myOrdersList[$o]['items'][$oI]['OfferedItem'][$key4]['offered_item_id'] = @$mfOffer['offered_item_id'];
                        $myOrdersList[$o]['items'][$oI]['OfferedItem'][$key4]['name'] = @$mfOffer['Item']['name'];
                        $myOrdersList[$o]['items'][$oI]['OfferedItem'][$key4]['quantity'] = @$mfOffer['quantity'];
                      }
                    } else {
                      $myOrdersList[$o]['items'][$oI]['OfferedItem'] = array();
                    }


                    $FreeItem = $this->OrderItemFree->find('first',
                            array('fields' => array('id', 'item_id', 'free_quantity'), 'conditions' => array('OrderItemFree.item_id' => $listOrderItem['item_id'], 'OrderItemFree.order_id' => $listOrder['Order']['id'], 'OrderItemFree.is_active' => 1, 'OrderItemFree.is_deleted' => 0)));


                    if (!empty($FreeItem)) {
                      $myOrdersList[$o]['items'][$oI]['FreeItem'] = $FreeItem['OrderItemFree']['free_quantity'];
                    } else {
                      $myOrdersList[$o]['items'][$oI]['FreeItem'] = "0";
                    }
                  }
                }
              }

              if (!empty($myOrdersList)) {
                $responsedata['message'] = "Success";
                $responsedata['response'] = 1;
                $responsedata['mySavedOrders'] = array_values($myOrdersList);
                //pr($responsedata);
                return json_encode($responsedata);
              } else {
                $responsedata['message'] = "No record found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
              }
            } else {
              $responsedata['message'] = "No record found.";
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

  public function removeMySavedOrder() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "delete_SavedOrder.txt",
            $headers);
    //$requestBody =  '{"order_id": 939}';
    $requestBody = json_decode($requestBody, true);
    $responsedata = array();
    //$headers['user_id'] = 'MQ';
    //$headers['merchant_id'] = 1;
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('DELETE')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        if (isset($headers['user_id']) && !empty($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          $roleid = array(4, 5);
          $userDet = $this->User->find('first',
                  array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
          if (!empty($userDet)) {
            if (isset($requestBody['order_id']) && !empty($requestBody['order_id'])) {
              // Checking Order id parameter is Integer
                $argOrder = $this->checkParameter($requestBody['order_id'], 'integer','Order'); 
                 if ($argOrder['response'] == '403') {
                   return json_encode($argOrder);
                 }
              $savedOrderResult = $this->Order->find('first',
                      array('conditions' => array('Order.id' => $requestBody['order_id'], 'Order.user_id' => $user_id, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 1)));
              if (!empty($savedOrderResult)) {
                $futureOrderId = $requestBody['order_id'];
                $this->loadModel('Order');
                if ($this->Order->delete($futureOrderId)) {
                  $this->OrderOffer->deleteAll(array('OrderOffer.order_id' => $futureOrderId),
                          false);
                  $this->OrderItem->deleteAll(array('OrderItem.order_id' => $futureOrderId),
                          false);
                  $this->OrderTopping->deleteAll(array('OrderTopping.order_id' => $futureOrderId),
                          false);
                  $responsedata['message'] = "Saved order has been deleted.";
                  $responsedata['response'] = 1;
                  return json_encode($responsedata);
                } else {
                  $responsedata['message'] = "Saved Order could not be deleted, please try again.";
                  $responsedata['response'] = 0;
                  return json_encode($responsedata);
                }
              } else {
                $responsedata['message'] = "Saved Order could not be deleted, please try again.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
              }
            } else {
              $responsedata['message'] = "Please select an order.";
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

  /*   * ******************************************************************************************
    @Function Name : storeTime
    @Description   : this function is used to show list of Users Saved Orders on merchant ID
    @Author        : SmartData
    created:1/11/2016
   * ****************************************************************************************** */

  public function storeTime() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    //$requestBody = '{"store_id":2,"orderType":"pickup","date":"12-07-2016"}';// and for pickup orderType":"pickup" m-d-Y
    $responsedata = array();
    $requestBody['store_id']=$_GET['store_id'];
    $requestBody['orderType']=$_GET['orderType'];
    $requestBody['date']=$_GET['date'];
    $this->Webservice->webserviceLog($requestBody, "store_Time.txt", $headers);
    //$headers['merchant_id'] = 85;
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {
        if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
          $storeId = $requestBody['store_id'];
          $storeResult = $this->Store->find('first',
                  array('conditions' => array('Store.id' => $storeId, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id')));
          if (!empty($storeResult)) {
            if (isset($requestBody['orderType']) && !empty($requestBody['orderType'])) {
              if (strtolower($requestBody['orderType']) == 'delivery') {
                $orderType = 3;
              } elseif (strtolower($requestBody['orderType']) == 'pickup') {
                $orderType = 2;
              } else {
                $orderType = 1;
              }
              $merchantId = $merchant_id; //$this->Encryption->decode($_POST['merchantId']);
              $this->loadModel('StoreAvailability');
              $this->loadModel('Store');
              $this->loadModel('StoreHoliday');
              if (isset($requestBody['date']) && !empty($requestBody['date'])) {
              // Checking Date parameter is Date
                $argDate = $this->checkParameter($requestBody['date'], 'date','Date'); 
                if ($argDate['response'] == '403') {
                  return json_encode($argDate);
                }
              }
              $date_shuffle = explode("-", $requestBody['date']);
              $new_date = $date_shuffle[2] . '-' . $date_shuffle[0] . '-' . $date_shuffle[1];
              $selected_day = date('l', strtotime($new_date));
              $store_data = $this->Store->fetchStoreBreak($storeId);
              $store_availability = $this->StoreAvailability->getStoreInfoForDay($selected_day,
                      $storeId); // get store detail
              $holidayList = $this->StoreHoliday->getStoreHolidaylistDate($storeId,
                      $new_date);
              $current_array = array();
              $time_break = array();
              $storeBreak = array();
              //$todayDate = date('m-d-Y');
              $todayDate = date("m-d-Y",
                      (strtotime($this->Webservice->storeTimeZoneUser('',
                                      date("Y-m-d H:i:s"), $storeId))));
              if (empty($holidayList)) {
                if (!empty($store_availability)) {
                  $start = $store_availability['StoreAvailability']['start_time'];
                  $end = $store_availability['StoreAvailability']['end_time'];
                  $StoreCutOff = $this->Store->fetchStoreCutOff($storeId);
                  $cutTime = '-' . $StoreCutOff['Store']['cutoff_time'] . ' minutes';
                  $end = date("H:i:s", strtotime("$cutTime", strtotime($end)));
                  $preOrder = 0;

                  if (strtotime(str_replace('-', '/', $requestBody['date'])) == strtotime(str_replace('-',
                                          '/', $todayDate))) {
                    $start = $this->Webservice->getStartTime($start, true,
                            $orderType, $preOrder, $end, $storeId);
                  } else {
                    $start = $this->Webservice->getStartTime($start, false,
                            $orderType, $preOrder, $end, $storeId);
                  }
                  $time_ranges = $this->Webservice->getStoreTime($start, $end,
                          $orderType, $storeId); // calling Common Component
                  $current_array = $time_ranges;


                  if ($store_data['Store']['is_break_time'] == 1) {
                    $this->loadModel('StoreBreak');
                    $store_break = $this->StoreBreak->fetchStoreBreak($store_data['Store']['id'],
                            $store_availability['StoreAvailability']['id']);
                    $time_break1 = array();
                    $time_break2 = array();
                    if ($store_data['Store']['is_break1'] == 1) {
                      $break_start_time = $store_break['StoreBreak']['break1_start_time'];
                      $break_end_time = $store_break['StoreBreak']['break1_end_time'];
                      $storeBreak[0]['start'] = $store_break['StoreBreak']['break1_start_time'];
                      $storeBreak[0]['end'] = $store_break['StoreBreak']['break1_end_time'];
                      $time_break1 = $this->Webservice->getStoreTime($break_start_time,
                              $break_end_time, true, $storeId);
                    }
                    if ($store_data['Store']['is_break2'] == 1) {
                      $break_start_time = $store_break['StoreBreak']['break2_start_time'];
                      $break_end_time = $store_break['StoreBreak']['break2_end_time'];
                      $storeBreak[1]['start'] = $store_break['StoreBreak']['break2_start_time'];
                      $storeBreak[1]['end'] = $store_break['StoreBreak']['break2_end_time'];
                      $time_break2 = $this->Webservice->getStoreTime($break_start_time,
                              $break_end_time, true, $storeId);
                    }
                    $time_break = array_unique(array_merge($time_break1,
                                    $time_break2), SORT_REGULAR);
                  }
                }
              }
              $time_range = $current_array;
              $TimeArr = array();
              $todayDate = date("m-d-Y");
              if (strtotime($requestBody['date']) == strtotime($todayDate)) {
                foreach ($time_range as $rangeKey => $rangeValue) {
                  $flag = true;

                  $hr24 = explode(':', $rangeKey);
                  foreach ($storeBreak as $breakKey => $breakVlue) {
                    if (strtotime($storeBreak[$breakKey]['start']) <= strtotime($rangeKey) && strtotime($storeBreak[$breakKey]['end']) >= strtotime($rangeKey)) {
                      $flag = false;
                    }
                  }
                  if ($flag) {
                    $HrMin = explode(':', $rangeValue);
                    $AmPm = explode(' ', $HrMin[1]);
                    if (count($AmPm) > 1) {
                      $TimeArr[$hr24[0]][] = $AmPm[0];
                    } else {
                      $TimeArr[$HrMin[0]][] = $HrMin[1];
                    }
                  }
                }
              } else {
                foreach ($time_range as $key => $value) {
                  $hr24 = explode(':', $key);
                  if (in_array($value, $time_break)) {
                    
                  } else {
                    $HrMin = explode(':', $value);
                    $AmPm = explode(' ', $HrMin[1]);
                    if (count($AmPm) > 1) {
                      $TimeArr[$hr24[0]][] = $AmPm[0];
                    } else {
                      $TimeArr[$HrMin[0]][] = $HrMin[1];
                    }
                  }
                }
              }
              //pr($TimeArr);
              $responsedata['message'] = "Success";
              $responsedata['response'] = 1;
              $responsedata['DateTime'] = $TimeArr;
              return json_encode($responsedata);
            } else {
              $responsedata['message'] = "Please select an order type.";
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
    Function name:myReviews()
    Description: Listing of user Reviews
    created:8/11/2016
    ----------------------------------------------------- */

  public function myReviews() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "my_review.txt", $headers);
    $responsedata = array();
    $requestBody = json_decode($requestBody, true);

        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        if (isset($headers['user_id']) && !empty($headers['user_id'])) {
          $roleid = array(4, 5);
          $user_id = $this->Encryption->decode($headers['user_id']);
          $userDet = $this->User->find('first',
                  array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
          if (!empty($userDet)) {
            $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
            $this->StoreReview->bindModel(array(
                'belongsTo' => array(
                    'Store' => array(
                        'className' => 'Store',
                        'foreignKey' => 'store_id',
                        'fields' => array('id', 'store_name', 'store_url'),
                        'type' => 'INNER',
                        'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1)
                    ))
                    ), false);

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

            $this->OrderItem->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name', 'image')))),
                    false);
            $this->StoreReview->bindModel(array('belongsTo' => array('User' => array('fields' => array('fname', 'lname')), 'OrderItem' => array('foreignKey' => 'order_item_id', 'fields' => array('item_id')))),
                    false);
            $allReviews = $this->StoreReview->find('all',
                    array('recursive' => 2, 'order' => array('StoreReview.created DESC'), 'conditions' => array('StoreReview.merchant_id' => $merchant_id, 'StoreReview.user_id' => $user_id, 'StoreReview.is_active' => 1, 'StoreReview.is_deleted' => 0)));
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
                if (!empty($reviewsAll['Store'])) {
                  $reviewArr[$i]['store_id'] = $reviewsAll['Store']['id'];
                  $reviewArr[$i]['store_name'] = $reviewsAll['Store']['store_name'];
                } else {
                  
                }
                if (!empty($reviewsAll['OrderItem']['item_id'])) {
                  $reviewArr[$i]['item_name'] = $reviewsAll['OrderItem']['Item']['name'];
                  $store_url = $reviewsAll['Store']['store_url'];
                  if (!empty($reviewsAll['OrderItem']['Item']['image'])) {
                    $reviewArr[$i]['img_url'] = $protocol . $store_url . "/MenuItem-Image/" . $reviewsAll['OrderItem']['Item']['image'];
                  } else {
                    $reviewArr[$i]['img_url'] = $protocol . $store_url . "/storeReviewImage/" . 'no_image.jpeg';
                  }
                } else {
                  $reviewArr[$i]['item_name'] = "";
                  $reviewArr[$i]['img_url'] = "";
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

                if (!empty($reviewsAll['Store']['id']))
                  $dateTime = $this->Webservice->storeTimezone($reviewsAll['Store']['id'],
                          $reviewsAll['StoreReview']['created'], true);
                $reviewArr[$i]['created'] = $dateTime;
                $i++;
              }
            } else {
              $reviewArr = array();
            }
//                                pr($reviewArr);
            $responsedata['message'] = "Success";
            $responsedata['response'] = 1;
            $responsedata['review'] = array_values($reviewArr);
            //pr($responsedata);
            return json_encode($responsedata);
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

  public function RemoveReview() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    $this->Webservice->webserviceLog($requestBody, "remove_review.txt", $headers);
    //$requestBody =  '{"review_id": 939}';
    $requestBody = json_decode($requestBody, true);
    $responsedata = array();
    //$headers['user_id'] = 'MQ';
    //$headers['merchant_id'] = 1;
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('DELETE')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        if (isset($headers['user_id']) && !empty($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          $roleid = array(4, 5);
          $userDet = $this->User->find('first',
                  array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
          if (!empty($userDet)) {
            if (isset($requestBody['review_id']) && !empty($requestBody['review_id'])) {
              $review_id = $requestBody['review_id'];
              // Checking Store id parameter is Integer
                $argReview = $this->checkParameter($review_id, 'integer', 'Review'); 
                    if ($argReview['response'] == '403') {
                      return json_encode($argReview);
                    }
              $resultReview = $this->StoreReview->updateAll(array('StoreReview.is_deleted' => 1),
                      array('StoreReview.id' => $review_id, 'StoreReview.user_id' => $user_id));
              if ($resultReview) {
                $responsedata['message'] = "Review has been deleted successfully.";
                $responsedata['response'] = 1;
                return json_encode($responsedata);
              } else {
                $responsedata['message'] = "Review could not be deleted, please try again.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
              }
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

  /*   * ******************************************************************************************
    @Function Name : store
    @Description   : this function is used for get selected Store details on merchant ID
    @Author        : SmartData
    created:10/11/2016
   * ****************************************************************************************** */

  public function store() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    //$requestBody = '{"store_id":2,"order_type":"reservation"}';
    $responsedata = array();
    $requestBody['store_id']=$_GET['store_id'];
    $requestBody['order_type']=$_GET['order_type'];
    $this->Webservice->webserviceLog($requestBody, "store_Det.txt", $headers);
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        $this->Store->unBindModel(array('belongsTo' => array('StoreTheme')),
                false);
        $this->Store->unBindModel(array('belongsTo' => array('StoreFont')),
                false);
        $this->Store->unBindModel(array('hasOne' => array('SocialMedia')), false);
        $this->Store->unBindModel(array('hasMany' => array('StoreContent')),
                false);
        $this->Store->unBindModel(array('hasMany' => array('StoreGallery')),
                false);

        $this->Store->bindModel(array(
            'hasMany' => array(
                'StoreAvailability' => array('fields' => array('id', 'day_name', 'store_id', 'start_time', 'end_time', 'is_closed'), 'conditions' => array('StoreAvailability.is_active' => 1, 'StoreAvailability.is_deleted' => 0)),
            )), false);

        if (isset($headers['user_id'])) {
          if (empty($user_id)) {
            $user_id = 0;
          }
          if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
            if (isset($requestBody['order_type']) && !empty($requestBody['order_type'])) {
              $orderType = $requestBody['order_type'];
              $store_id = $requestBody['store_id'];
              $locStoreList = $this->Store->find('first',
                      array('conditions' => array('Store.is_active' => 1, 'Store.is_deleted' => 0, 'Store.merchant_id' => $merchant_id, 'Store.id' => $store_id), 'fields' => array('id', 'store_name', 'address', 'city', 'state', 'store_logo', 'phone', 'zipcode', 'merchant_id', 'delivery_fee', 'service_fee', 'minimum_order_price', 'minimum_takeaway_price', 'is_booking_open', 'is_delivery', 'cash_on_delivery', 'is_pay_by_credit_card', 'is_express_check_out', 'deliverycalendar_limit', 'pickcalendar_limit', 'deliveryblackout_limit', 'pickblackout_limit', 'dineinblackout_limit', 'pre_order_allowed', 'calendar_limit', 'dineinblackout_limit', 'is_take_away', 'is_booking_open')));
//                        pr($locStoreList);
//                        die;
              if (!empty($locStoreList)) {
                $this->Session->write('store_id', $locStoreList['Store']['id']);
                $this->Session->write('merchant_id', $merchant_id);
                $merchant['store_id'] = $locStoreList['Store']['id'];
                $merchant['merchant_id'] = $merchant_id;
                $current_date = $this->Webservice->getcurrentTime($locStoreList['Store']['id'],
                        2);
//                            echo "Current Date:- ".$current_date."<br>";
                //$current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
                $order_type = strtolower($orderType);
                if ($order_type == 'delivery') {
                  $orderType = 3;
                } elseif ($order_type == 'carryout') {
                  $orderType = 2;
                } elseif ($order_type == 'reservation') {
                  $orderType = 1;
                }
                $finaldata = array();
                $today = 1;
                $finaldata = $this->Common->getNextDayTimeRange($current_date,
                        $today, $orderType, $merchant);
                //pr($finaldata);
                $date_current = $finaldata['currentdate'];
                $currentDateVar = $date_current;
                //pr($locStoreList);
                if ($order_type == 'delivery') {
                  if ($locStoreList['Store']['deliveryblackout_limit'] > 0) {
                    $currentDateVar = date('Y-m-d',
                            strtotime($date_current . ' +' . $locStoreList['Store']['deliveryblackout_limit'] . ' day'));
                  }
                  $locStoreList['Store']['delivery_current_date'] = $currentDateVar;
                  $locStoreList['Store']['pickup_current_date'] = "";
                  $locStoreList['Store']['reservation_current_date'] = "";
                } elseif ($order_type == 'carryout') {
                  if ($locStoreList['Store']['pickblackout_limit'] > 0) {
                    $currentDateVar = date('Y-m-d',
                            strtotime($date_current . ' +' . $locStoreList['Store']['pickblackout_limit'] . ' day'));
                  }
                  $locStoreList['Store']['delivery_current_date'] = "";
                  $locStoreList['Store']['pickup_current_date'] = $currentDateVar;
                  $locStoreList['Store']['reservation_current_date'] = "";
                } elseif ($order_type == 'reservation') {
                  if ($locStoreList['Store']['dineinblackout_limit'] > 0) {
                    $currentDateVar = date('Y-m-d',
                            strtotime($date_current . ' +' . $locStoreList['Store']['dineinblackout_limit'] . ' day'));
                  }
                  $locStoreList['Store']['delivery_current_date'] = "";
                  $locStoreList['Store']['pickup_current_date'] = "";
                  $locStoreList['Store']['reservation_current_date'] = $currentDateVar;
                } else {
                  $responsedata['message'] = "Please select valid order Type";
                  $responsedata['response'] = 0;
                  return json_encode($responsedata);
                }
                //pr($locStoreList);
//                            echo "Final Date:- ".$date_current."<br>";
//                            echo "Curremt Date with Blackout:- ".$currentDateVar."<br>";

                if ($locStoreList['Store']['is_booking_open'] == 1) {
                  $locStoreList['Store']['is_booking_open'] = true;
                } else {
                  $locStoreList['Store']['is_booking_open'] = false;
                }

                if ($locStoreList['Store']['is_delivery'] == 1) {
                  $locStoreList['Store']['is_delivery'] = true;
                } else {
                  $locStoreList['Store']['is_delivery'] = false;
                }

                if ($locStoreList['Store']['cash_on_delivery'] == 1) {
                  $locStoreList['Store']['cash_on_delivery'] = true;
                } else {
                  $locStoreList['Store']['cash_on_delivery'] = false;
                }

                if ($locStoreList['Store']['is_pay_by_credit_card'] == 1) {
                  $locStoreList['Store']['is_pay_by_credit_card'] = true;
                } else {
                  $locStoreList['Store']['is_pay_by_credit_card'] = false;
                }

                if ($locStoreList['Store']['is_express_check_out'] == 1) {
                  $locStoreList['Store']['is_express_check_out'] = true;
                } else {
                  $locStoreList['Store']['is_express_check_out'] = false;
                }

                if ($locStoreList['Store']['is_take_away'] == 1) {
                  $locStoreList['Store']['is_take_away'] = true;
                } else {
                  $locStoreList['Store']['is_take_away'] = false;
                }

                if ($locStoreList['Store']['pre_order_allowed'] == 1) {
                  $locStoreList['Store']['pre_order_allowed'] = true;
                } else {
                  $locStoreList['Store']['pre_order_allowed'] = false;
                }
                foreach ($locStoreList['StoreAvailability'] as $storeAvailability) {
                  $dateTime = date("l", strtotime($currentDateVar)) . "\n";
                  if ($storeAvailability["day_name"] == trim($dateTime)) {
                    $locStoreList['Store']['start_time'] = $storeAvailability['start_time'];
                    $locStoreList['Store']['end_time'] = $storeAvailability['end_time'];
                    if ($storeAvailability['is_closed'] == 1) {
                      $locStoreList['Store']['is_closed'] = true;
                    } else {
                      $locStoreList['Store']['is_closed'] = false;
                    }
                  }
                }
                if (!empty($locStoreList['Store']['store_logo'])) {
                  $protocol = 'http://';
                  if (isset($_SERVER['HTTPS'])) {
                    if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                      $protocol = 'https://';
                    }
                  }
                  $locStoreList['Store']['store_logo'] = $protocol . $_SERVER['HTTP_HOST'] . "/storeLogo/" . $locStoreList['Store']['store_logo'];
                } else {
                  $locStoreList['Store']['store_logo'] = " ";
                }
                unset($locStoreList['StoreAvailability']);


                if (!empty($locStoreList)) {
                  $responsedata['message'] = "Success";
                  $responsedata['response'] = 1;
                  $responsedata['data'] = $locStoreList['Store'];

                  //pr($responsedata);
                  return json_encode($responsedata);
                } else {
                  $responsedata['message'] = "Store not found.";
                  $responsedata['response'] = 0;
                  return json_encode($responsedata);
                }
              } else {
                $responsedata['message'] = "Store not found.";
                $responsedata['response'] = 0;
                return json_encode($responsedata);
              }
            } else {
              $responsedata['message'] = "Please select a valid order type.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            }
          } else {
            $responsedata['message'] = "Please select a store.";
            $responsedata['response'] = 0;
            return json_encode($responsedata);
          }
        } else {
          $responsedata['message'] = "Please login or continue as a guest.";
          $responsedata['response'] = 0;
          return json_encode($responsedata);
          return $this->json_message(401, '');
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
    Function name:reorder()
    Description: Used for re-order cycle
    created:17/8/2015
    ----------------------------------------------------- */

  public function reorder() {

    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    //$requestBody =  '{"order_id":1409,"store_id":108}';
    //$headers['user_id'] = 'NTc2';
    //$headers['merchant_id'] = 85;
    $this->Webservice->webserviceLog($requestBody, "reorder.txt", $headers);
    $requestBody = json_decode($requestBody, true);
    $responsedata = array();

        /*Check HTTP METHOD Request*/
        if(!$this->request->is('POST')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantResult = $this->Merchant->find('first',
              array('conditions' => array('Merchant.id' => $merchant_id)));
      if (!empty($merchantResult)) {
        $domain = $merchantResult['Merchant']['domain_name'];
        if (isset($headers['user_id']) && !empty($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          $roleid = array(4, 5);
          $userDet = $this->User->find('first',
                  array('conditions' => array('User.id' => $user_id, 'User.merchant_id' => $merchant_id, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => $roleid), 'fields' => array('User.id', 'User.role_id', 'User.store_id', 'User.merchant_id', 'User.email', 'User.is_active', 'User.is_deleted')));
          if (!empty($userDet)) {
            if (isset($requestBody['store_id']) && !empty($requestBody['store_id'])) {
              $store_id = $requestBody['store_id'];
              // Checking Store id parameter is Integer
                $argStore = $this->checkParameter($store_id, 'integer', 'Store'); 
                    if ($argStore['response'] == '403') {
                      return json_encode($argStore);
                    }
              $storeResult = $this->Store->find('first',
                      array('conditions' => array('Store.id' => $store_id, 'Store.merchant_id' => $merchant_id, 'Store.is_active' => 1, 'Store.is_deleted' => 0), 'fields' => array('Store.id', 'Store.store_url')));
              if (!empty($storeResult)) {
                $store_url = $storeResult['Store']['store_url'];
                if (isset($requestBody['order_id']) && !empty($requestBody['order_id'])) {

                  $protocol = 'http://';
                  if (isset($_SERVER['HTTPS'])) {
                    if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                      $protocol = 'https://';
                    }
                  }
                  if(!empty($requestBody['order_id'])){
                    // Checking Store id parameter is Integer
                    $argOrder = $this->checkParameter($requestBody['order_id'], 'integer', 'Order'); 
                        if ($argOrder['response'] == '403') {
                          return json_encode($argOrder);
                        }
                  }
                  $decrypted_orderId = $requestBody['order_id'];
                  $this->loadModel('Order');
                  $this->loadModel('OrderItem');
                  $this->loadModel('Item');
                  $this->loadModel('ItemPrice');
                  $this->loadModel('ItemType');
                  $this->loadModel('OrderOffer');
                  $this->loadModel('Topping');
                  $this->loadModel('OrderItemFree');
                  $this->loadModel('StoreTax');
                  $this->loadModel('Type');
                  $this->loadModel('SubPreference');
                  $this->loadModel('OrderPreference');



                  $this->OrderItem->bindModel(
                          array(
                      'hasMany' => array(
                          'OrderOffer' => array('fields' => array('id', 'offer_id', 'offered_size_id', 'offered_item_id', 'quantity'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0)),
                          'OrderTopping' => array('fields' => array('id', 'topping_id', 'topType', 'addon_size_id'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0)),
                          'OrderPreference' => array('fields' => array('id', 'order_item_id', 'sub_preference_id', 'order_id'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0))
                      ),
                      'belongsTo' => array(
                          'Item' => array('foreignKey' => 'item_id', 'fields' => array('id'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0)),
                          'Type' => array('foreignKey' => 'type_id', 'fields' => array('id', 'name', 'price'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0)),
                          'Size' => array('foreignKey' => 'size_id', 'fields' => array('id', 'size'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0)))
                          ), false);

                  $this->Order->bindModel(array('hasMany' => array(
                          'OrderItem' => array(
                              'fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id'),
                              'conditions' => array('is_active' => 1, 'is_deleted' => 0)),
                      )), false);
                  $myOrders = $this->Order->find('first',
                          array('recursive' => 4, 'conditions' => array('Order.id' => $decrypted_orderId, 'Order.user_id' => $user_id, 'Order.store_id' => $store_id, 'Order.merchant_id' => $merchant_id)));
                  $count = 0;
                  $activeItem = 0;
                  $taxprice = 0;
//                        echo "<pre>"."<br>";
//                        print_r($myOrders);
//                        die;

                  $SessionItem = array();
                  if (!empty($myOrders)) {
                    foreach ($myOrders['OrderItem'] as $order) {
                      //pr($order);

                      $this->ItemPrice->bindModel(
                              array('belongsTo' => array(
                              'StoreTax' => array(
                                  'className' => 'StoreTax',
                                  'foreignKey' => 'store_tax_id',
                                  'conditions' => array('StoreTax.is_active' => 1, 'StoreTax.is_deleted' => 0, 'StoreTax.store_id' => $store_id)
                              )
                          )), false);


                      $this->Item->bindModel(
                              array('hasMany' => array(
                              'ItemPrice' => array(
                                  'className' => 'ItemPrice',
                                  'foreignKey' => 'item_id',
                                  'conditions' => array('ItemPrice.is_active' => 1, 'ItemPrice.is_deleted' => 0, 'ItemPrice.store_id' => $store_id, 'ItemPrice.size_id' => $order['size_id'])
                              ),
                              'ItemType' => array(
                                  'className' => 'ItemType',
                                  'foreignKey' => 'item_id',
                                  'conditions' => array('ItemType.is_active' => 1, 'ItemType.is_deleted' => 0, 'ItemType.store_id' => $store_id)
                              ),
                              'ItemDefaultTopping' => array(
                                  'className' => 'ItemDefaultTopping',
                                  'foreignKey' => 'item_id',
                                  'conditions' => array('ItemDefaultTopping.store_id' => $store_id, 'ItemDefaultTopping.is_active' => 1, 'ItemDefaultTopping.is_deleted' => 0)
                              ),
                              'OrderItemFree' => array(
                                  'className' => 'OrderItemFree',
                                  'foreignKey' => 'item_id',
                                  'fields' => array('id', 'item_id', 'free_quantity', 'order_id', 'price'),
                                  'conditions' => array('OrderItemFree.is_active' => 1, 'OrderItemFree.is_deleted' => 0, 'OrderItemFree.item_id' => $order['item_id'], 'OrderItemFree.order_id' => $order['order_id']))
                          )
                              ), false);
                      $today = $this->Webservice->getcurrentTime($store_id, 2);
                      $this->Item->bindModel(array(
                          'hasMany' => array(
                              'Offer' => array(
                                  'className' => 'Offer',
                                  'foreignKey' => 'item_id',
                                  'type' => 'INNER',
                                  'conditions' => array('Offer.is_active' => 1, 'Offer.is_deleted' => 0, 'Offer.store_id' => $store_id, 'OR' => array('Offer.offer_end_date >=' => $today, 'Offer.offer_start_date =' => NULL)),
                                  'fields' => array('id', 'description')
                              ),
                              'ItemOffer' => array(
                                  'className' => 'ItemOffer',
                                  'foreignKey' => 'item_id',
                                  'type' => 'INNER',
                                  'conditions' => array('ItemOffer.is_active' => 1, 'ItemOffer.is_deleted' => 0, 'ItemOffer.store_id' => $store_id, 'OR' => array('ItemOffer.end_date >=' => $today, 'ItemOffer.start_date =' => NULL)),
                                  'fields' => array('id', 'unit_counter')
                              )
                      )));
                      if (!empty($order['Item']['id'])) {
                        $ordItem = $this->Item->getItemById($order['Item']['id']);
                      } else {
                        $ordItem = array();
                      }
                      //pr($ordItem);

                      if (empty($ordItem)) {
                        $activeItem = 1;
                      } else {
                        if ($ordItem['Item']['is_seasonal_item'] == 1) {
                          //$date = date('Y-m-d');
                          $date = $this->Common->gettodayDate();
                          if (($ordItem['Item']['start_date'] <= $date) && ($ordItem['Item']['end_date'] >= $date)) {
                            $activeItem = 1;
                          }
                        } else {
                          //pr($ordItem);
                          $SessionItem[$count]['category_id'] = $ordItem['Item']['category_id'];
                          $SessionItem[$count]['item_id'] = $ordItem['Item']['id'];
                          $SessionItem[$count]['item_name'] = $ordItem['Item']['name'];
                          $SessionItem[$count]['description'] = $ordItem['Item']['description'];
                          if (!empty($ordItem['ItemPrice'][0]['StoreTax'])) {
                            $SessionItem[$count]['isStoreTax'] = TRUE;
                            $SessionItem[$count]['StoreTax']['tax_name'] = $ordItem['ItemPrice'][0]['StoreTax']['tax_name'];
                            $SessionItem[$count]['StoreTax']['tax_value'] = $ordItem['ItemPrice'][0]['StoreTax']['tax_value'];
                          } else {
                            $SessionItem[$count]['isStoreTax'] = FALSE;
                            $SessionItem[$count]['StoreTax']['tax_name'] = "";
                            $SessionItem[$count]['StoreTax']['tax_value'] = "";
                          }

                          $SessionItem[$count]['quantity'] = $order['quantity'];

                          if (!empty($myOrders['Order']['order_comments'])) {
                            $SessionItem[$count]['order_comments'] = $myOrders['Order']['order_comments'];
                          } else {
                            $SessionItem[$count]['order_comments'] = $myOrders['Order']['order_comments'];
                          }


                          if (!empty($order['Size'])) {
                            $SessionItem[$count]['size_id'] = $order['Size']['id'];
                            $SessionItem[$count]['size_name'] = $order['Size']['size'];
                          } else {
                            $SessionItem[$count]['size_id'] = "";
                            $SessionItem[$count]['size_name'] = "";
                          }
                          if (!empty($ordItem['Item']['start_date'])) {
                            $SessionItem[$count]['start_date'] = $ordItem['Item']['start_date'];
                          } else {
                            $SessionItem[$count]['start_date'] = "";
                          }
                          if (!empty($item['end_date'])) {
                            $SessionItem[$count]['end_date'] = $ordItem['Item']['end_date'];
                          } else {
                            $SessionItem[$count]['end_date'] = "";
                          }

                          if (!empty($ordItem['Item']['image'])) {

                            $SessionItem[$count]['image'] = $protocol . $store_url . "/MenuItem-Image/" . $ordItem['Item']['image'];
                          } else {
                            $SessionItem[$count]['image'] = $protocol . $store_url . "/MenuItem-Image/" . 'default_menu.png';
                          }
                          $SessionItem[$count]['Currency'] = "USD";
                          if ($ordItem['Item']['preference_mandatory'] == 0) {
                            $SessionItem[$count]['preference_mandatory'] = FALSE;
                          } else {
                            $SessionItem[$count]['preference_mandatory'] = TRUE;
                          }


                          if (!empty($ordItem['Offer'])) {
                            $SessionItem[$count]['isOfferAvailabe'] = TRUE;
                            $op = 0;
                            $Offer_detail = array();
                            foreach ($ordItem['Offer'] as $o => $promotionsList) {
                              $Offer_detail[$op] = $promotionsList['description'];
                              $op++;
                            }
                          } else {
                            $SessionItem[$count]['isOfferAvailabe'] = FALSE;
                            $Offer_detail = array();
                          }

                          if (!empty($ordItem['ItemOffer'])) {
                            $SessionItem[$count]['isExtendOfferAvailabe'] = TRUE;
                            $oE = 0;
                            $extended = array();
                            foreach ($ordItem['ItemOffer'] as $o => $extendedOfferList) {
                              $extended[$oE] = "Buy " . ($extendedOfferList['unit_counter'] - 1) . " unit and get 1 free on Item " . $ordItem['Item']['name'];
                              $oE++;
                            }
                          } else {
                            $SessionItem[$count]['isExtendOfferAvailabe'] = FALSE;
                            $extended = array();
                          }
                          $OfferDetailArr = array_merge($Offer_detail, $extended);
                          if (!empty($OfferDetailArr)) {
                            $SessionItem[$count]['Offer_detail'] = $OfferDetailArr;
                          } else {
                            $SessionItem[$count]['Offer_detail'] = array();
                          }


                          $ordSize = $this->ItemPrice->getSizeById($order['size_id'],
                                  $order['item_id']);
                          //pr($ordSize);
                          if (empty($ordSize)) {
                            $activeItem = 1;
                            $SessionItem[$count]['size_id'] = "";
                            $SessionItem[$count]['item_price'] = "";
                            $SessionItem[$count]['size_price'] = "";
                          } else {
                            $SessionItem[$count]['ItemPrice_id'] = $ordSize['ItemPrice']['id'];
                            $intervalPrice = 0;
                            $intervalPrice = $this->getTimeIntervalPrice($order['item_id'],
                                    $order['size_id'], $store_id);
                            if (!empty($intervalPrice['Interval']['IntervalDay']) && !empty($intervalPrice['IntervalPrice'])) {
                              $SessionItem[$count]['item_price'] = $intervalPrice['IntervalPrice']['price'];
                              $SessionItem[$count]['size_price'] = $intervalPrice['IntervalPrice']['price'];
                              //$SessionItem[$count]['interval_id'] = $intervalPrice['IntervalPrice']['interval_id'];
                            } else {
                              $SessionItem[$count]['item_price'] = $ordSize['ItemPrice']['price'];
                              $SessionItem[$count]['size_price'] = $ordSize['ItemPrice']['price'];
                              //$SessionItem[$count]['interval_id'] = $intervalPrice['IntervalPrice']['interval_id'];
                            }
                          }



                          // Extended Promo Code
                          if (!empty($ordItem['OrderItemFree'])) {
                            //pr($ordItem);
                            foreach ($ordItem['OrderItemFree'] as $fI => $OrderItemFree) {
                              // pr($OrderItemFree);
                              $this->ItemOffer->bindModel(
                                      array('belongsTo' => array(
                                              'Category' => array(
                                                  'className' => 'Category',
                                                  'foreignKey' => 'category_id',
                                                  'fields' => array('id', 'name'),
                                                  'conditions' => array('Category.is_active' => 1, 'Category.is_deleted' => 0)
                              ))));
                              $ItemOffer = $this->ItemOffer->find('first',
                                      array('recursive' => 2, 'conditions' => array('ItemOffer.item_id' => $OrderItemFree['item_id'], 'ItemOffer.is_active' => 1, 'ItemOffer.is_deleted' => 0, 'ItemOffer.store_id' => $store_id, 'OR' => array('ItemOffer.end_date >=' => $today, 'ItemOffer.start_date =' => NULL))));
//                                                     //pr($ItemOffer);
                              if (!empty($ItemOffer)) {

                                if (!empty($ItemOffer['Category']['id'])) {
                                  $SessionItem[$count]['ExtendedPromo'][$fI]['promo_id'] = $ItemOffer['ItemOffer']['id'];
                                  $SessionItem[$count]['ExtendedPromo'][$fI]['item_id'] = $ordItem['Item']['id'];
                                  $SessionItem[$count]['ExtendedPromo'][$fI]['item_name'] = $ordItem['Item']['name'];
                                  $SessionItem[$count]['ExtendedPromo'][$fI]['description'] = $ordItem['Item']['description'];
                                  $SessionItem[$count]['ExtendedPromo'][$fI]['extended_detail'] = "Buy " . ($ItemOffer['ItemOffer']['unit_counter'] - 1) . " unit and get 1 free on Item " . $ordItem['Item']['name'];
                                  if (!empty($ItemOffer['Item']['image'])) {
                                    $SessionItem[$count]['ExtendedPromo'][$fI]['image'] = $protocol . $store_url . "/MenuItem-Image/" . $ordItem['Item']['image'];
                                  } else {
                                    $SessionItem[$count]['ExtendedPromo'][$fI]['image'] = $protocol . $store_url . "/Offer-Image/default_offer.png";
                                  }

                                  $SessionItem[$count]['ExtendedPromo'][$fI]['unit'] = $ItemOffer['ItemOffer']['unit_counter'];
                                  if (!empty($ItemOffer['ItemOffer']['start_date'])) {
                                    $SessionItem[$count]['ExtendedPromo'][$fI]['start_date'] = $ItemOffer['ItemOffer']['start_date'];
                                  } else {
                                    $SessionItem[$count]['ExtendedPromo'][$fI]['start_date'] = "";
                                  }
                                  if (!empty($ItemOffer['ItemOffer']['end_date'])) {
                                    $SessionItem[$count]['ExtendedPromo'][$fI]['end_date'] = $ItemOffer['ItemOffer']['end_date'];
                                  } else {
                                    $SessionItem[$count]['ExtendedPromo'][$fI]['end_date'] = "";
                                  }
                                  $SessionItem[$count]['discountedPrice'] = $OrderItemFree['price'];
                                }
                              } else {
                                $SessionItem[$count]['ExtendedPromo'] = array();
                                $SessionItem[$count]['discountedPrice'] = "0";
                              }
                            }
                          } else {
                            $SessionItem[$count]['ExtendedPromo'] = array();
                            $SessionItem[$count]['discountedPrice'] = "0";
                          }


                          /* ------------------------------------------------Sub Addons Code Start here------------------------------------------------------------------- */
                          $top_count = 0;
                          if (!empty($order['OrderTopping'])) {
                            foreach ($order['OrderTopping'] as $topping) {
                              //pr($topping);
                              $this->Topping->bindModel(
                                      array('hasMany' => array(
                                              'ItemDefaultTopping' => array(
                                                  'className' => 'ItemDefaultTopping',
                                                  'foreignKey' => 'topping_id',
                                                  'conditions' => array('ItemDefaultTopping.is_active' => 1, 'ItemDefaultTopping.is_deleted' => 0, 'ItemDefaultTopping.topping_id' => $topping['topping_id'], 'ItemDefaultTopping.item_id' => $order['Item']['id'], 'ItemDefaultTopping.store_id' => $store_id)
                              ))));
                              $this->Topping->bindModel(
                                      array('hasMany' => array(
                                              'ToppingPrice' => array(
                                                  'className' => 'ToppingPrice',
                                                  'foreignKey' => 'topping_id',
                                                  'conditions' => array('ToppingPrice.is_active' => 1, 'ToppingPrice.is_deleted' => 0, 'ToppingPrice.topping_id' => $topping['topping_id'], 'ToppingPrice.item_id' => $order['Item']['id'], 'ToppingPrice.size_id' => $order['size_id'], 'ToppingPrice.store_id' => $store_id)
                              ))));
                              $topType = $this->Topping->getToppingById($topping['topping_id'],
                                      $order['Item']['id']);
                              //pr($topType);
                              if (empty($topType)) {
                                $activeItem = 1;
                                //$SessionItem[$count]['selectedSubAddonsModels']=array();
                              } else {
                                if (!empty($ordItem)) {
                                  if ($ordItem['Item']['default_subs_price'] == 1) {
                                    $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['id'] = $topType['Topping']['id'];
                                    $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['price'] = $topType['Topping']['price'];
                                    $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['name'] = $topType['Topping']['name'];
                                    $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['addon_size_id'] = $topping['addon_size_id'];

                                    // Price based On if Item is default
                                    if (!empty($topType['ItemDefaultTopping'])) {
                                      $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['is_defualt'] = TRUE;
                                      $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['price'] = "0";
                                    } else {
                                      $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['is_defualt'] = FALSE;
                                    }
                                    if ($topType['Topping']['no_size'] == 1) {
                                      $topping['addon_size_id'] = "";
                                    }


                                    // Price based on Addon Size ID

                                    if (!empty($topping['addon_size_id'])) {
                                      $addonsize_name = $this->AddonSize->find('first',
                                              array('conditions' => array('AddonSize.id' => $topping['addon_size_id'])));
                                      if (!empty($addonsize_name)) {
                                        $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['addonsize_name'] = $addonsize_name['AddonSize']['size'];
                                        $price = round($topType['Topping']['price'] * ($addonsize_name['AddonSize']['price_percentage'] / 100),
                                                2);
                                        $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['price'] = (string) $price;
                                      }
                                    } else {
                                      $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['addon_size_id'] = "0";
                                      $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['addonsize_name'] = "0";
                                    }
                                  } else {

                                    $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['id'] = $topType['Topping']['id'];
                                    if (!empty($topType['ToppingPrice'])) {
                                      $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['price'] = $topType['ToppingPrice'][0]['price'];
                                      $priceTop = $topType['ToppingPrice'][0]['price'];
                                    } else {
                                      $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['price'] = $topType['Topping']['price'];
                                      $priceTop = $topType['Topping']['price'];
                                    }

                                    $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['name'] = $topType['Topping']['name'];
                                    $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['addon_size_id'] = $topping['addon_size_id'];

                                    // Price based On if Item is default
                                    if (!empty($topType['ItemDefaultTopping'])) {
                                      $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['is_defualt'] = TRUE;
                                      $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['price'] = "0";
                                    } else {
                                      $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['is_defualt'] = FALSE;
                                    }

                                    if ($topType['Topping']['no_size'] == 1) {
                                      $topping['addon_size_id'] = "";
                                    }
                                    // Price based on Addon Size ID

                                    if (!empty($topping['addon_size_id'])) {
                                      $addonsize_name = $this->AddonSize->find('first',
                                              array('conditions' => array('AddonSize.id' => $topping['addon_size_id'])));
                                      if (!empty($addonsize_name)) {
                                        $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['addonsize_name'] = $addonsize_name['AddonSize']['size'];
                                        $price = round($priceTop * ($addonsize_name['AddonSize']['price_percentage'] / 100),
                                                2);
                                        $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['price'] = (string) $price;
                                      }
                                    } else {
                                      $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['addon_size_id'] = "0";
                                      $SessionItem[$count]['selectedSubAddonsModels'][$top_count]['addonsize_name'] = "0";
                                    }
                                  }
                                }
                              }
                              $top_count++;
                            }
                          } else {
                            $SessionItem[$count]['selectedSubAddonsModels'] = array();
                          }
                          /* ------------------------------------------------Sub Addons Code End here-------------------------------------------------------------------------- */

                          /* ------------------------------------------------Sub Preference Code Start here------------------------------------------------------------------- */
                          $pre_count = 0;
                          if (!empty($order['OrderPreference'])) {
                            foreach ($order['OrderPreference'] as $preference) {
                              //pr($preference);
                              $this->SubPreference->bindModel(
                                      array('hasMany' => array(
                                              'SubPreferencePrice' => array(
                                                  'className' => 'SubPreferencePrice',
                                                  'foreignKey' => 'sub_preference_id',
                                                  'conditions' => array('SubPreferencePrice.is_active' => 1, 'SubPreferencePrice.is_deleted' => 0, 'SubPreferencePrice.item_id' => $order['Item']['id'], 'SubPreferencePrice.size_id' => $order['size_id'], 'SubPreferencePrice.store_id' => $store_id)
                              ))));
                              $preData = $this->SubPreference->getSubPreferenceDetail($preference['sub_preference_id'],
                                      $store_id);
                              $this->Type->unBindModel(array('hasMany' => array('ItemType')));
                              $preType = $this->Type->find('first',
                                      array('conditions' => array('Type.id' => $preData['SubPreference']['type_id'], 'Type.store_id' => $store_id, 'Type.is_active' => 1, 'Type.is_deleted' => 0), 'fields' => array('Type.id')));
                              //pr($preData);
                              if (!empty($preData) && !empty($preType)) {
                                $SessionItem[$count]['selectedSubPreferencesModels'][$pre_count]['id'] = $preData['SubPreference']['id'];
                                $SessionItem[$count]['selectedSubPreferencesModels'][$pre_count]['name'] = $preData['SubPreference']['name'];
                                if (empty($order['size_id'])) {
                                  $SessionItem[$count]['selectedSubPreferencesModels'][$pre_count]['price'] = $preData['SubPreference']['price'];
                                } else {

                                  if ($ordItem['Item']['default_subs_price'] == 1) {
                                    $SessionItem[$count]['selectedSubPreferencesModels'][$pre_count]['price'] = $preData['SubPreference']['price'];
                                  } else {

                                    if (!empty($preData['SubPreferencePrice'])) {
                                      $SessionItem[$count]['selectedSubPreferencesModels'][$pre_count]['price'] = $preData['SubPreferencePrice'][0]['price'];
                                    } else {
                                      $SessionItem[$count]['selectedSubPreferencesModels'][$pre_count]['price'] = "0";
                                    }
                                  }
                                }
                              } else {
                                $SessionItem[$count]['selectedSubPreferencesModels'] = array();
                              }
                              $pre_count++;
                            }
                          } else {
                            $SessionItem[$count]['selectedSubPreferencesModels'] = array();
                          }
                          /* ------------------------------------------------Sub Preference Code Start here------------------------------------------------------------------- */

                          /* ------------------------------------------------------Offer Code Start here--------------------------------------------------------------------------------------- */

                          $offer_count = 0;
                          $offerIds = array();
                          if (!empty($order['OrderOffer'])) {
                            $Offerpromotion = array();
                            $of = 0;
                            foreach ($order['OrderOffer'] as $o => $OrderOffer) {
                              $this->Offer->bindModel(array(
                                  'hasMany' => array(
                                      'OfferDetail' => array(
                                          'className' => 'OfferDetail',
                                          'foreignKey' => 'offer_id',
                                          'type' => 'INNER',
                                          'conditions' => array('OfferDetail.is_active' => 1, 'OfferDetail.is_deleted' => 0, 'OfferDetail.store_id' => $store_id),
                                          'fields' => array('OfferDetail.id', 'OfferDetail.offer_id', 'OfferDetail.offerItemID', 'OfferDetail.offerSize', 'OfferDetail.offerItemType', 'OfferDetail.quantity', 'OfferDetail.discountAmt')
                                      ),
                                  )
                              ));
                              $today = $this->Webservice->getcurrentTime($store_id,
                                      2);
                              $promotionsList = $this->Offer->find('first',
                                      array('recursive' => 2, 'conditions' => array('Offer.id' => $OrderOffer['offer_id'], 'Offer.is_active' => 1, 'Offer.is_deleted' => 0, 'Offer.store_id' => $store_id, 'OR' => array('Offer.offer_end_date >=' => $today, 'Offer.offer_start_date =' => NULL)), 'fields' => array('Offer.id', 'Offer.store_id', 'Offer.item_id', 'Offer.size_id', 'Offer.description', 'Offer.unit', 'Offer.is_fixed_price', 'Offer.offerprice', 'Offer.is_time', 'Offer.offer_start_date', 'Offer.offer_end_date', 'Offer.offer_start_time', 'Offer.offer_end_time', 'Offer.is_time', 'Offer.offerImage')));
                              //pr($promotionsList);

                              if (!empty($promotionsList)) {
                                if (!empty($promotionsList['OfferDetail'])) {
                                  $itemDet = $this->getItemDetails($store_id,
                                          $promotionsList['Offer']['item_id']);
                                  //pr($itemDet);
                                  if (!empty($itemDet['Category']['id'])) {
                                    $Offerpromotion['Offer'][$of]['id'] = $promotionsList['Offer']['id'];
                                    $Offerpromotion['Offer'][$of]['Item_name'] = $itemDet['Item']['name'];
                                    $Offerpromotion['Offer'][$of]['item_id'] = $promotionsList['Offer']['item_id'];
                                    $Offerpromotion['Offer'][$of]['category_id'] = $itemDet['Category']['id'];
                                    $Offerpromotion['Offer'][$of]['category_name'] = $itemDet['Category']['name'];
                                    if ($promotionsList['Offer']['size_id'] == 0) {
                                      $Offerpromotion['Offer'][$of]['isSizeApplicable'] = FALSE;
                                    } else {
                                      $Offerpromotion['Offer'][$of]['isSizeApplicable'] = TRUE;
                                    }
                                    if ($promotionsList['Offer']['unit'] >= 1) {
                                      $Offerpromotion['Offer'][$of]['isUnitApplicable'] = TRUE;
                                    } else {
                                      $Offerpromotion['Offer'][$of]['isUnitApplicable'] = FALSE;
                                    }
                                    $Offerpromotion['Offer'][$of]['size_id'] = $promotionsList['Offer']['size_id'];
                                    $sizeDet = $this->getsizeNames($store_id,
                                            $promotionsList['Offer']['size_id']);
                                    if (!empty($sizeDet)) {
                                      $Offerpromotion['Offer'][$of]['size_name'] = $sizeDet['Size']['size'];
                                    } else {
                                      $Offerpromotion['Offer'][$of]['size_name'] = "";
                                    }
                                    $ItemPriceDet = $this->getItemPrices($store_id,
                                            $promotionsList['Offer']['item_id'],
                                            $promotionsList['Offer']['size_id']);
                                    if (!empty($ItemPriceDet)) {
                                      $Offerpromotion['Offer'][$of]['Item_price'] = $ItemPriceDet['ItemPrice']['price'];
                                    } else {
                                      $Offerpromotion['Offer'][$of]['Item_price'] = "0";
                                    }
                                    if (!empty($promotionsList['Offer']['description'])) {
                                      $Offerpromotion['Offer'][$of]['offer_description'] = $promotionsList['Offer']['description'];
                                    } else {
                                      $Offerpromotion['Offer'][$of]['offer_description'] = "";
                                    }


                                    if (!empty($promotionsList['Offer']['offer_start_date'])) {
                                      $Offerpromotion['Offer'][$of]['offer_start_date'] = $promotionsList['Offer']['offer_start_date'];
                                    } else {
                                      $Offerpromotion['Offer'][$of]['offer_start_date'] = "";
                                    }
                                    if (!empty($promotionsList['Offer']['offer_end_date'])) {
                                      $Offerpromotion['Offer'][$of]['offer_end_date'] = $promotionsList['Offer']['offer_end_date'];
                                    } else {
                                      $Offerpromotion['Offer'][$of]['offer_end_date'] = "";
                                    }
                                    if ($promotionsList['Offer']['is_time'] == 1) {
                                      $Offerpromotion['Offer'][$of]['is_time'] = TRUE;
                                      $Offerpromotion['Offer'][$of]['offer_start_time'] = $promotionsList['Offer']['offer_start_time'];
                                      $Offerpromotion['Offer'][$of]['offer_end_time'] = $promotionsList['Offer']['offer_end_time'];
                                      if ($promotionsList['Offer']['offer_start_time'] == "24:00:00") {
                                        $Offerpromotion['Offer'][$of]['offer_start_time'] = "00:00:00";
                                      }
                                      if ($promotionsList['Offer']['offer_start_time'] == "24:00:00") {
                                        $Offerpromotion['Offer'][$of]['offer_end_time'] = "00:00:00";
                                      }
                                    } else {
                                      $Offerpromotion['Offer'][$of]['is_time'] = FALSE;
                                      $Offerpromotion['Offer'][$of]['offer_start_time'] = "";
                                      $Offerpromotion['Offer'][$of]['offer_end_time'] = "";
                                    }
                                    $protocol = 'http://';
                                    if (isset($_SERVER['HTTPS'])) {
                                      if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                                        $protocol = 'https://';
                                      }
                                    }
                                    if (!empty($promotionsList['Offer']['offerImage'])) {
                                      $Offerpromotion['Offer'][$of]['offerImage'] = $protocol . $domain . "/Offer-Image/" . $promotionsList['Offer']['offerImage'];
                                    } else if (!empty($itemDet['Item']['image'])) {
                                      $Offerpromotion['Offer'][$of]['offerImage'] = $protocol . $domain . "/MenuItem-Image/" . $itemDet['Item']['image'];
                                    } else {
                                      $Offerpromotion['Offer'][$of]['offerImage'] = $protocol . $domain . "/Offer-Image/default_offer.png";
                                    }
                                    $Offerpromotion['Offer'][$of]['unit'] = $promotionsList['Offer']['unit'];

                                    if ($promotionsList['Offer']['is_fixed_price'] == 0) {
                                      $Offerpromotion['Offer'][$of]['is_fixed_price'] = FALSE;
                                      $Offerpromotion['Offer'][$of]['offerprice'] = "";
                                      $Offerpromotion['Offer'][$of]['offerDetail'] = array();
                                    }

                                    if ($promotionsList['Offer']['is_fixed_price'] == 1) {
                                      $Offerpromotion['Offer'][$of]['is_fixed_price'] = TRUE;
                                      $Offerpromotion['Offer'][$of]['offerprice'] = $promotionsList['Offer']['offerprice'];
                                      $Offerpromotion['Offer'][$of]['offerDetail'] = array();
                                    }

                                    if (!empty($promotionsList['OfferDetail'])) {
                                      $j = 0;
                                      foreach ($promotionsList['OfferDetail'] as $oD => $promotionsItemList) {
                                        $offerItemitemDet = $this->getItemDetails($store_id,
                                                $promotionsItemList['offerItemID']);
                                        if (!empty($offerItemitemDet['Category']['id'])) {
                                          $itemSize = $this->getsizeNames($store_id,
                                                  $promotionsItemList['offerSize']);
                                          $Offerpromotion['Offer'][$of]['offerDetail'][$j]['Offered_id'] = $promotionsItemList['id'];
                                          $Offerpromotion['Offer'][$of]['offerDetail'][$j]['offered_item_id'] = $promotionsItemList['offerItemID'];
                                          $Offerpromotion['Offer'][$of]['offerDetail'][$j]['name'] = $offerItemitemDet['Item']['name'];
                                          $Offerpromotion['Offer'][$of]['offerDetail'][$j]['price'] = $promotionsItemList['discountAmt'];
                                          $Offerpromotion['Offer'][$of]['offerDetail'][$j]['category_id'] = $offerItemitemDet['Category']['id'];
                                          $Offerpromotion['Offer'][$of]['offerDetail'][$j]['category_name'] = $offerItemitemDet['Category']['name'];
                                          if (!empty($promotionsList['offerImage'])) {
                                            $Offerpromotion['Offer'][$of]['offerDetail'][$j]['image'] = $protocol . $domain . "/Offer-Image/" . $promotionsList['offerImage'];
                                          } else {
                                            $Offerpromotion['Offer'][$of]['offerDetail'][$j]['image'] = $protocol . $domain . "/Offer-Image/default_offer.png";
                                          }
                                          $Offerpromotion['Offer'][$of]['offerDetail'][$j]['item_description'] = $offerItemitemDet['Item']['description'];
                                          if (!empty($itemSize)) {
                                            $Offerpromotion['Offer'][$of]['offerDetail'][$j]['size_name'] = $itemSize['Size']['size'];
                                            $Offerpromotion['Offer'][$of]['offerDetail'][$j]['size_id'] = $itemSize['Size']['id'];
                                          } else {
                                            $Offerpromotion['Offer'][$of]['offerDetail'][$j]['size_name'] = "";
                                            $Offerpromotion['Offer'][$of]['offerDetail'][$j]['size_id'] = $promotionsItemList['offerSize'];
                                          }
                                          $j++;
                                        }
                                      }
                                      if (!empty($Offerpromotion['Offer'][$of]['offerDetail'])) {
                                        $Offerpromotion['Offer'][$of]['offerDetail'] = array_values($Offerpromotion['Offer'][$of]['offerDetail']);
                                      }
                                    }
                                    $of++;
                                  }
                                }
                              }
                            }
                            if (!empty($Offerpromotion['Offer'])) {
                              $result = array();
                              foreach ($Offerpromotion['Offer'] as $arr) {
                                if (!isset($result[$arr["id"]])) {
                                  $result[$arr["id"]] = $arr;
                                }
                              }
                              //pr($result);
                              $SessionItem[$count]['Offers'] = array_values($result);
                            } else {
                              $SessionItem[$count]['Offers'] = array();
                            }
                          } else {
                            $SessionItem[$count]['Offers'] = array();
                          }
                          /* --------------------------------Offer Code End here------------------------------------------------ */

                          $count++;
                        }
                      }
                    }

                    /* --------------------------------Price Calculation Code start------------------------------------------------ */

                    if (!empty($SessionItem)) {
                      foreach ($SessionItem as $key => $itemCal) {
                        //pr($itemCal);
                        $preferencePrice = 0;
                        $addonPrice = 0;
                        $offersPrice = 0;
                        $totalPrice = $itemCal['quantity'] * $itemCal['item_price'];

                        if (!empty($itemCal['selectedSubAddonsModels'])) {
                          $subprice = array();

                          foreach ($itemCal['selectedSubAddonsModels'] as $subadd => $subAddOn) {

                            $subprice[] = $subAddOn['price'] * $itemCal['quantity'];
                            $SessionItem[$key]['selectedSubAddonsModels'][$subadd]['size_id'] = $subAddOn['addon_size_id'];
                            $SessionItem[$key]['selectedSubAddonsModels'][$subadd]['size'] = $subAddOn['addonsize_name'];
                            $SessionItem[$key]['selectedSubAddonsModels'][$subadd]['defaults'] = $subAddOn['is_defualt'];
                            unset($SessionItem[$key]['selectedSubAddonsModels'][$subadd]['addon_size_id']);
                            unset($SessionItem[$key]['selectedSubAddonsModels'][$subadd]['addonsize_name']);
                            unset($SessionItem[$key]['selectedSubAddonsModels'][$subadd]['is_defualt']);
                          }
                          if (!empty($subprice)) {
                            $addonPrice = array_sum($subprice);
                          }
                        }
                        if (!empty($itemCal['selectedSubPreferencesModels'])) {
                          $subPreprice = array();
                          foreach ($itemCal['selectedSubPreferencesModels'] as $subpre => $subPreference) {
                            $subPreprice[] = $subPreference['price'] * $itemCal['quantity'];
                          }
                          if (!empty($subPreprice)) {
                            $preferencePrice = array_sum($subPreprice);
                          }
                        }

                        if (!empty($itemCal['Offers'])) {
                          $pendingPrice = 0;
                          $quotient = 1;
                          $offersArr = array();
                          foreach ($itemCal['Offers'] as $offer => $offerArray) {
                            if (!empty($itemCal['quantity']) && !empty($offerArray['unit'])) {
                              if ($itemCal['quantity'] > $offerArray['unit']) {
                                $quotient = (int) ($itemCal['quantity'] / $offerArray['unit']);
                                $mod = $itemCal['quantity'] % $offerArray['unit'];
//                                                echo "Item quantity:- ".$itemCal['quantity']."<br>";
//                                                echo "Offer Unit:- ".$offerArray['unit']."<br>";
//                                                echo "quotient:- ".$quotient."<br>";
//                                                echo "Modeulus:- ".$mod."<br>";
//                                                echo "Item price:- ".$itemCal['item_price']."<br>";
                                if ($mod > 0) {

                                  $pendingPrice = $mod * $itemCal['item_price'];
                                }
                              }
                            }


                            if ($offerArray['is_fixed_price'] == 1) {
                              $totalPrice = $offerArray['offerprice'] * $quotient + $pendingPrice;
//                                            $addonPrice = 0;
//                                            $preferencePrice = 0;
                            } else {
                              if (!empty($offerArray['offerDetail'])) {
                                foreach ($offerArray['offerDetail'] as $offerDet => $offerDetArray) {
                                  $offersArr[] = $offerDetArray['price'];
                                }
                              } else {
                                continue;
                              }
                            }
                          }
                          if (!empty($offersArr)) {
                            $offersPrice = array_sum($offersArr);
                            //echo "pendingPrice:".$pendingPrice."<br>";
                            //echo "offer Price:".$offersPrice."<br>";
                            //echo "Total Price:".$totalPrice."<br>";
                            $offersPrice = $offersPrice * $quotient;
                            $totalPrice = $totalPrice + $offersPrice;
                          }
                        }

                        //$SessionItem[$key]['Items_price'] = $totalPrice;
                        $SessionItem[$key]['Total_subAddon_price'] = (string) $addonPrice;
                        $SessionItem[$key]['Total_subPre_price'] = (string) $preferencePrice;
                        $SessionItem[$key]['total_price'] = (string) $totalPrice;
                        if (!empty($addonPrice)) {
                          $SessionItem[$key]['total_price'] = (string) ($SessionItem[$key]['total_price'] + $addonPrice);
                        }
                        if (!empty($preferencePrice)) {
                          $SessionItem[$key]['total_price'] = (string) ($SessionItem[$key]['total_price'] + $preferencePrice);
                        }

                        if (!empty($itemCal['StoreTax']['tax_value'])) {
                          $SessionItem[$key]['totalTaxAmount'] = (string) ($SessionItem[$key]['total_price'] * $itemCal['StoreTax']['tax_value'] / 100);
                        } else {
                          $SessionItem[$key]['totalTaxAmount'] = "0";
                        }
                      }
                    }
                    //echo "<pre>" . "<br>";
                    //print_r($SessionItem);
                    /* --------------------------------Price Calculation Code End------------------------------------------------ */

                    //$this->Session->write('reOrder', $SessionItem);
                    $responsedata['message'] = "Succes";
                    $responsedata['response'] = "1";
                    $responsedata['item'] = (string) $activeItem;
                    $responsedata['count'] = (string) $count;
                    $responsedata['cart_items'] = array_values($SessionItem);
                    //pr($responsedata);
                    return json_encode($responsedata);
                  } else {
                    $responsedata['message'] = "No active order found for this user under this store.";
                    $responsedata['response'] = "0";
                    return json_encode($responsedata);
                  }
                } else {
                  $responsedata['message'] = "Please select an order.";
                  $responsedata['response'] = "0";
                  return json_encode($responsedata);
                }
              } else {
                $responsedata['message'] = "Store not found.";
                $responsedata['response'] = "0";
                return json_encode($responsedata);
              }
            } else {
              $responsedata['message'] = "Please select a store.";
              $responsedata['response'] = "0";
              return json_encode($responsedata);
            }
          } else {
            $responsedata['message'] = "You are not registered under this merchant.";
            $responsedata['response'] = "0";
            return json_encode($responsedata);
          }
        } else {
          $responsedata['message'] = "Please login.";
          $responsedata['response'] = "0";
          return json_encode($responsedata);
        }
      } else {
        $responsedata['message'] = "Merchant not found.";
        $responsedata['response'] = "0";
        return json_encode($responsedata);
      }
    } else {
      $responsedata['message'] = "Please select a merchant.";
      $responsedata['response'] = "0";
      return json_encode($responsedata);
    }
  }

  public function verifyAddress() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    //$requestBody = '{"delivery_address_id":"939","store_id":"149"}';
    $responsedata = array();
    $requestBody['store_id']=$_GET['store_id'];
    $requestBody['delivery_address_id']=$_GET['delivery_address_id'];
    $this->Webservice->webserviceLog($requestBody, "verify_Address.txt",
            $headers);
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        if (isset($headers['user_id'])) {
          $user_id = $this->Encryption->decode($headers['user_id']);
          $roleid = array(4, 5);


          if (!empty($requestBody['delivery_address_id'])) {
            $DelAddress = $this->DeliveryAddress->fetchAddress($requestBody['delivery_address_id']);
          } else {
            $response['message'] = "Delivery address not found.";
            $response['response'] = 0;
            return json_encode($response);
          }
          $zoneData = $this->Common->setZonefee($DelAddress,
                  $requestBody['store_id'], "mob");

          if (empty($zoneData)) {
            $response['message'] = "Order can't be delivered at this address.";
            $response['response'] = 0;
            return json_encode($response);
          } else {
            $responsedata['message'] = "Success";
            $responsedata['response'] = 1;
            $responsedata['data'] = $zoneData['Zone'];
            return json_encode($responsedata);
          }
          ///Check Zone                    
        } else {
          $responsedata['message'] = "Please login or continue as a guest.";
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

  public function searchLocation() {
    configure::Write('debug', 0);
    $headers = $this->getheader();
    $requestBody = file_get_contents('php://input');
    // $requestBody ='{"distance":"10","keyword":"torrance"}';
    $responsedata = array();
    $requestBody['distance']=$_GET['distance'];
    $requestBody['keyword']=$_GET['keyword'];
    $this->Webservice->webserviceLog($requestBody, "search_loc.txt", $headers);
    //$headers['merchant_id']=1;
    
        /*Check HTTP METHOD Request*/
        if(!$this->request->is('GET')){
            $methodresponse=$this->methodResponse();
            return $methodresponse;
        }
        
        /* Authenticating the login time If the last request is more than 30 minutes*/
          if(empty($headers['authtoken'])){
            $responsedata['message'] = "Please login again.";
            $responsedata['response'] = 401;
            return json_encode($responsedata);
          }else{
            $loginCheck=$this->checkLoggedIn($headers['authtoken']);
              if($loginCheck['response']=='401'){                    
                      return json_encode($loginCheck);
              }
          }
    
        
    if (isset($headers['merchant_id']) && !empty($headers['merchant_id'])) {
      $merchant_id = $headers['merchant_id'];
      $merchantCheck = $this->Merchant->find('first',
              array('conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0, 'Merchant.id' => $merchant_id), 'fields' => array('id')));
      if (!empty($merchantCheck)) {

        $this->Store->unBindModel(array('belongsTo' => array('StoreTheme')),
                false);
        $this->Store->unBindModel(array('belongsTo' => array('StoreFont')),
                false);
        $this->Store->unBindModel(array('hasOne' => array('SocialMedia')), false);
        $this->Store->unBindModel(array('hasMany' => array('StoreContent')),
                false);
        $this->Store->unBindModel(array('hasMany' => array('StoreGallery')),
                false);

        $this->Store->bindModel(array(
            'hasMany' => array(
                'StoreAvailability' => array('fields' => array('id', 'day_name', 'store_id', 'start_time', 'end_time', 'is_closed'), 'conditions' => array('StoreAvailability.is_active' => 1, 'StoreAvailability.is_deleted' => 0)),
            )), false);

        if (!empty($requestBody['keyword'])) {
          if (empty($requestBody['distance'])) {
            $requestBody['distance'] = 10;
          }
          $addresskey = str_replace(' ', '+', $requestBody['keyword']);
          $geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address=' . $addresskey . '&sensor=false');
          $output = json_decode($geocode);
          if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
            
          } else {
            $latitude = @$output->results[0]->geometry->location->lat;
            $longitude = @$output->results[0]->geometry->location->lng;
          }
          if (empty($latitude) && empty($longitude)) {
            $responsedata['data'] = array();
            $responsedata['message'] = "Store No found.";
            $responsedata['response'] = 1;
            return json_encode($responsedata);
          }


          $this->Store->virtualFields['distance'] = '( 3959 * acos( cos( radians(' . $latitude . ') ) * cos( radians( latitude ) ) * cos( radians( logitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin(radians(latitude))))';
          $locStoreList = $this->Store->find('all',
                  array('conditions' => array('Store.is_active' => 1, 'Store.is_deleted' => 0, 'Store.merchant_id' => $merchant_id), 'fields' => array('distance', 'id', 'store_name', 'address', 'city', 'state', 'store_logo', 'phone', 'zipcode', 'merchant_id', 'delivery_fee', 'service_fee', 'minimum_order_price', 'minimum_takeaway_price', 'is_booking_open', 'is_delivery', 'cash_on_delivery', 'is_pay_by_credit_card', 'is_express_check_out', 'deliverycalendar_limit', 'pickcalendar_limit', 'deliveryblackout_limit', 'pickblackout_limit', 'dineinblackout_limit', 'pre_order_allowed', 'calendar_limit', 'dineinblackout_limit', 'is_take_away', 'is_booking_open'), 'group' => array('Store__distance HAVING Store__distance < ' . $requestBody['distance'])));
        } else {
          $responsedata['data'] = array();
          $responsedata['response'] = 1;
          $responsedata['message'] = "No keyword found.";
          return json_encode($responsedata);
        }

        ///SELECT id, ( 3959 * acos( cos( radians(33.8483436) ) * cos( radians( latitude ) ) * cos( radians( logitude ) - radians(-118.3541056) ) + sin( radians(33.8483436) ) * sin(radians(latitude)) ) ) AS distance FROM stores HAVING distance < 2 ORDER BY distance ASC



        $listing = array();

        foreach ($locStoreList as $k => $locStoreDet) {
          if ($locStoreDet['Store']['is_booking_open'] == 1) {
            $locStoreDet['Store']['is_booking_open'] = true;
          } else {
            $locStoreDet['Store']['is_booking_open'] = false;
          }

          if ($locStoreDet['Store']['is_delivery'] == 1) {
            $locStoreDet['Store']['is_delivery'] = true;
          } else {
            $locStoreDet['Store']['is_delivery'] = false;
          }

          if ($locStoreDet['Store']['cash_on_delivery'] == 1) {
            $locStoreDet['Store']['cash_on_delivery'] = true;
          } else {
            $locStoreDet['Store']['cash_on_delivery'] = false;
          }

          if ($locStoreDet['Store']['is_pay_by_credit_card'] == 1) {
            $locStoreDet['Store']['is_pay_by_credit_card'] = true;
          } else {
            $locStoreDet['Store']['is_pay_by_credit_card'] = false;
          }

          if ($locStoreDet['Store']['is_express_check_out'] == 1) {
            $locStoreDet['Store']['is_express_check_out'] = true;
          } else {
            $locStoreDet['Store']['is_express_check_out'] = false;
          }

          if ($locStoreDet['Store']['is_take_away'] == 1) {
            $locStoreDet['Store']['is_take_away'] = true;
          } else {
            $locStoreDet['Store']['is_take_away'] = false;
          }

          if ($locStoreDet['Store']['pre_order_allowed'] == 1) {
            $locStoreDet['Store']['pre_order_allowed'] = true;
          } else {
            $locStoreDet['Store']['pre_order_allowed'] = false;
          }
          //Current Date for all three delivery, Reception and Pickup
          $this->Session->write('store_id', $locStoreDet['Store']['id']);
          $this->Session->write('merchant_id', $merchant_id);
          $merchant['store_id'] = $locStoreDet['Store']['id'];
          $merchant['merchant_id'] = $merchant_id;
          $current_date = $this->Webservice->getcurrentTime($locStoreDet['Store']['id'],
                  2);
          $today = 1;
          $finaldata = array();
          // for delivery
          $orderType = 3;
          $finaldata = $this->Common->getNextDayTimeRange($current_date, $today,
                  $orderType, $merchant);
          $Del_date_current = $finaldata['currentdate'];
          $current_Del_Date_Var = $Del_date_current;
          if ($locStoreDet['Store']['deliveryblackout_limit'] > 0) {
            $current_Del_Date_Var = date('Y-m-d',
                    strtotime($Del_date_current . ' +' . $locStoreDet['Store']['deliveryblackout_limit'] . ' day'));
          }
          $locStoreDet['Store']['delivery_current_date'] = $current_Del_Date_Var;

          //For Pick Up

          $orderType = 2;
          $finaldata = $this->Common->getNextDayTimeRange($current_date, $today,
                  $orderType, $merchant);
          $pickup_date_current = $finaldata['currentdate'];
          $current_pickup_Date_Var = $pickup_date_current;
          if ($locStoreDet['Store']['pickblackout_limit'] > 0) {
            $current_pickup_Date_Var = date('Y-m-d',
                    strtotime($pickup_date_current . ' +' . $locStoreDet['Store']['pickblackout_limit'] . ' day'));
          }
          $locStoreDet['Store']['pickup_current_date'] = $current_pickup_Date_Var;

          //For Reservation Up
          $orderType = 1;
          $finaldata = $this->Common->getNextDayTimeRange($current_date, $today,
                  $orderType, $merchant);
          $reser_date_current = $finaldata['currentdate'];
          $current_Reser_Date_Var = $reser_date_current;
          if ($locStoreDet['Store']['dineinblackout_limit'] > 0) {
            $current_Reser_Date_Var = date('Y-m-d',
                    strtotime($reser_date_current . ' +' . $locStoreDet['Store']['dineinblackout_limit'] . ' day'));
          }
          $locStoreDet['Store']['reservation_current_date'] = $current_Reser_Date_Var;

          foreach ($locStoreDet['StoreAvailability'] as $storeAvailability) {
            $today = $this->Webservice->getcurrentTime($storeAvailability['store_id'],
                    1);
            // $today;
            $dateTime = date("l", strtotime($today)) . "\n";
            if ($storeAvailability["day_name"] == trim($dateTime)) {
              $locStoreDet['Store']['start_time'] = $storeAvailability['start_time'];
              $locStoreDet['Store']['end_time'] = $storeAvailability['end_time'];
              if ($storeAvailability['is_closed'] == 1) {
                $locStoreDet['Store']['is_closed'] = true;
              } else {
                $locStoreDet['Store']['is_closed'] = false;
              }
            }
            $locStoreList[$k] = $locStoreDet;
          }
          unset($locStoreList[$k]['StoreAvailability']);

          if (!empty($locStoreDet['Store']['store_logo'])) {
            $protocol = 'http://';
            if (isset($_SERVER['HTTPS'])) {
              if (strtoupper($_SERVER['HTTPS']) == 'ON') {
                $protocol = 'https://';
              }
            }
            $locStoreList[$k]['Store']['store_logo'] = $protocol . $_SERVER['HTTP_HOST'] . "/storeLogo/" . $locStoreDet['Store']['store_logo'];
          } else {
            $locStoreList[$k]['Store']['store_logo'] = " ";
          }
          $this->Session->destroy();
        }

        foreach ($locStoreList as $listdata) {
          $listing[] = $listdata['Store'];
        }

        header('merchant_id:' . $merchant_id);
        if (!empty($listing)) {
          $responsedata['data'] = $listing;
          $responsedata['response'] = 1;
          $responsedata['message'] = "Success";
          return json_encode($responsedata);
          //return $this->json_message(1, 'Success', $listing);
        } else {
          $responsedata['data'] = array();
          $responsedata['response'] = 1;
          $responsedata['message'] = "Store not found.";
          return json_encode($responsedata);
        }
      } else {
        $responsedata['data'] = array();
        $responsedata['response'] = 1;
        $responsedata['message'] = "Merchant not found.";
        return json_encode($responsedata);
      }
    } else {
      $responsedata['data'] = array();
      $responsedata['response'] = 1;
      $responsedata['message'] = "Please select a merchant.";
      return json_encode($responsedata);
    }
  }

  Public function methodResponse() {
    $responsedata['message'] = "Bad Request.";
    $responsedata['response'] = 400;
    return json_encode($responsedata);
  }
   Public function checkParameter($arg = null, $typeOf = null, $argName = null,
          $dateFormat = null) {
    $argtype = gettype($arg);
    $responsedata['response'] = 1;
    switch ($typeOf) {
      case "string":
        if ($argtype != $typeOf) {
          $responsedata['message'] = "Invalid argument. " . $argName . " should be string.";
          $responsedata['response'] = 403;
        }
        break;
      case "integer":
        if ($argtype != $typeOf) {
          $responsedata['message'] = "Invalid argument. " . $argName . " should be integer.";
          $responsedata['response'] = 403;
        }
        break;
        
      case "double":
        if ($argtype != $typeOf) {
          $responsedata['message'] = "Invalid argument. " . $argName . " should be float.";
          $responsedata['response'] = 403;
        }
        break;  
      case "date":
        $formatDate='m-d-Y';
        if(!empty($dateFormat)){
          $formatDate=$dateFormat;
        }
        if (DateTime::createFromFormat($formatDate, $arg) == FALSE) {
          $responsedata['message'] = "Invalid argument. " . $argName . " should be in format ".$formatDate.".";
          $responsedata['response'] = 403;
        }
        break;
      case "dateTime":
        if (DateTime::createFromFormat('Y-m-d H:i:s', $arg) == FALSE) {
          $responsedata['message'] = "Invalid argument. " . $argName . " should be in format Y-m-d H:i:s.";
          $responsedata['response'] = 403;
        }
        break;
      case "boolean":
        if ($argtype != $typeOf) {
          $responsedata['message'] = "Invalid argument. " . $argName . " should be boolean.";
          $responsedata['response'] = 403;
        }
        break;
    }
    return $responsedata;
  }
  
  public function checkLoggedIn($token){
    $user = $this->UserToken->find("first",array("conditions" => array("UserToken.auth_token" => $token), 'fields' => array('auth_token','auth_time','id')));
    if(empty($user)){
      $responsedata['message'] = "Please login again.";
      $responsedata['response'] = 401;
      return $responsedata;
      }
    $loginTime= $user['UserToken']['auth_time'];
    $currentTime=time();
    $timeComare = strtotime('+30 minutes',$loginTime);     
    if($currentTime > $timeComare){
      $responsedata['message'] = "Please login again.";
      $responsedata['response'] = 401;
      return $responsedata;
    }
    
    $UpdateTime = $this->UserToken->updateAll(array('UserToken.auth_time' => $currentTime), array('UserToken.id' => $user['UserToken']['id']));
    
  }
  
  public function getheaderServer(){
    $headers=array();
     if (isset($_SERVER['HTTP_MERCHANTID']) && !empty($_SERVER['HTTP_MERCHANTID'])) {
        $headers['merchant_id']=$_SERVER['HTTP_MERCHANTID'];
        }
    
    if (isset($_SERVER['HTTP_USERID']) && !empty($_SERVER['HTTP_USERID'])) {
      $headers['user_id']=$_SERVER['HTTP_USERID'];
      }
    if (isset($_SERVER['HTTP_AUTHTOKEN']) && !empty($_SERVER['HTTP_AUTHTOKEN'])) {
      $headers['authtoken']=$_SERVER['HTTP_AUTHTOKEN'];
      }
      return $headers;
  }
  
  public function headerGetApache(){
    $headers = apache_request_headers();
    return $headers;
  }
  public function getheaderLocal(){
    $headers=array();
     if (isset($_SERVER['HTTP_MERCHANTID']) && !empty($_SERVER['HTTP_MERCHANTID'])) {
        $headers['merchant_id']=$_SERVER['HTTP_MERCHANTID'];
        }elseif(!function_exists('headerGetApache')){
            $headers = apache_request_headers();
            if(empty($headers)){
              $responsedata['message'] = "Merchant not set in header request.";
              $responsedata['response'] = 0;
              return json_encode($responsedata);
            }
        }else{
          $responsedata['message'] = "Merchant not set in header request.";
          $responsedata['response'] = 0;
          return json_encode($responsedata);
    }
    
    if (isset($_SERVER['HTTP_USERID']) && !empty($_SERVER['HTTP_USERID'])) {
      $headers['user_id']=$_SERVER['HTTP_USERID'];
      }elseif(!function_exists('headerGetApache')){
          $headers = apache_request_headers();
      }
    if (isset($_SERVER['HTTP_AUTHTOKEN']) && !empty($_SERVER['HTTP_AUTHTOKEN'])) {
      $headers['authtoken']=$_SERVER['HTTP_AUTHTOKEN'];
      }elseif(!function_exists('headerGetApache')){
          $headers = apache_request_headers();
      }  
      
      return $headers;
  }

}
