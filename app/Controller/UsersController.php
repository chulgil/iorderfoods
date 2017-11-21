<?php
App::uses('StoreAppController', 'Controller');

//App::import('Sanitize');

class UsersController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common', 'NZGateway');
    public $helper = array('Encryption', 'Common');
    public $uses = array('User', 'NzsafeUser', 'StoreHoliday');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('guestUserSignUp', 'popuplogin', 'user_guest', 'setDefaultStoreTime', 'clearsession', 'checkStoreEndUserEmail', 'city', 'zip', 'logout');
        $roleId = AuthComponent::User('role_id');
        if ($roleId) {
            $roles = array('4', '5');
            if (!in_array($roleId, $roles)) {
                $this->InvalidLogin($roleId);
            }
        }

        $storeId = $this->Session->read('store_id');
        $merchantId = $this->Session->read('merchant_id');

        $this->loadModel('StoreAvailability');
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
        $this->loadModel('Store');
        $store_break_data = $this->Store->fetchStoreDetail($storeId, $merchantId);
        $this->set('store_break_data', $store_break_data);

        $closedDay = array();
        $storeavaibilityInfo = $this->StoreAvailability->getclosedDay($storeId);
        $daysarray = array('sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6);
        //pr($storeavaibilityInfo);die;
        $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        $end_date = date('Y-m-d', date(strtotime("+7 day", strtotime($current_date))));
        $holidayDates = $this->StoreHoliday->getholidaydate($storeId, $current_date, $end_date);
        $HolidayDay = array();
        if (!empty($holidayDates)) {
            foreach ($holidayDates as $key => $date) {
                if (!empty($date)) {
                    $datetime = DateTime::createFromFormat('Y-m-d', $date);
                    $day = strtolower($datetime->format('l'));
                    if (array_key_exists($day, $daysarray)) {
                        $HolidayDay[$key] = $daysarray[$day];
                    }
                }
            }
        }

        if (!empty($storeavaibilityInfo)) {
            foreach ($storeavaibilityInfo as $key => $value) {

                if (!empty($value)) {
                    $day = strtolower($value['StoreAvailability']['day_name']);
                    if (array_key_exists($day, $daysarray)) {
                        $closedDay[$key] = $daysarray[$day];
                    }
                }
            }
        }
        $closedDay = array_unique(array_merge($HolidayDay, $closedDay));
        $this->set('closedDay', $closedDay);


        $decrypt_storeId = $this->Session->read('store_id');
        $decrypt_merchantId = $this->Session->read('merchant_id');
        $encrypted_storeId = $this->Encryption->encode($decrypt_storeId);
        $encrypted_merchantId = $this->Encryption->encode($decrypt_merchantId);
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId'));
    }

    public function store() {
        $this->autoRender = false;
        $this->redirect(array('controller' => 'users', 'action' => 'login'));
    }

    /* ------------------------------------------------
      Function name:registration()
      Description:Registration  Form for the  End customer
      created:22/7/2015
      ----------------------------------------------------- */

    public function registration() {
        $this->layout = $this->store_layout;
        $this->loadModel('CountryCode');
        $this->Session->delete('Order');
        $this->Session->delete('Cart');
        $this->Session->delete('cart');
        $this->Session->delete('FetchProductData');
        $this->Session->delete('Coupon');
        $this->Session->delete('Discount');
        $countryCode = $this->CountryCode->fetchAllCountryCode();
        $this->set(compact('countryCode'));
        if ($this->request->is('post')) {

            $this->User->set($this->request->data);
            if ($this->User->validates()) {
                $storeId = "";
                $merchantId = "";
                $storeId = $this->Session->read('store_id'); // It will read from session when a customer will try to register on store
                $merchantId = $this->Session->read('merchant_id');
                $email = strtolower(trim($this->request->data['User']['email'])); //Here username is email
                $this->request->data['User']['store_id'] = $storeId; // Store Id
                $this->request->data['User']['merchant_id'] = $merchantId; // Merchant Id
                $this->request->data['User']['role_id'] = 4; // Role Id of the user
                $userName = trim($this->request->data['User']['email']); //Here username is email
                $this->request->data['User']['username'] = trim($userName);
                //$current_time = date("Y-m-d H:i:s");
                $current_time = $date = $this->Common->gettodayDate(3);
                $this->request->data['User']['dateOfjoin'] = $current_time;
                $actualDbDate = $this->Dateform->formatDate($this->request->data['User']['dateOfBirth']); // calling formatDate function in Appcontroller to format the date (Y-m-d) format
                $this->request->data['User']['dateOfBirth'] = $actualDbDate;
                $token = time() . rand();
                $this->request->data['User']['is_active'] = 0;
                $this->request->data['User']['activation_token'] = $token;
                $this->request->data['User']['is_privacypolicy'] = 1;
                $this->request->data['User']['city'] = trim($this->request->data['User']['city_id']);
                $this->request->data['User']['state'] = trim($this->request->data['User']['state_id']);
                $this->request->data['User']['zip'] = trim($this->request->data['User']['zip_id']);
                $this->request->data['User']['state_id'] = 0;
                $this->request->data['User']['city_id'] = 0;
                $this->request->data['User']['zip_id'] = 0;
                
                // Activate User
                $this->request->data['User']['is_active'] = 1;
                $this->request->data['User']['activation_token'] = 1;
                $result = $this->User->saveUserInfo($this->request->data);   // We are calling function written on Model to save data
                $this->loadModel('Store');
                $storeEmail = $this->Store->fetchStoreDetail($storeId);
                if ($result == 1) {
                    $this->Session->setFlash(__('Registration successfully done.'), 'flash_success');
                    if ($this->Auth->login()) {
                        $roleId = AuthComponent::User('role_id');
                        $this->Session->write('login_date_time', $this->Common->gettodayDate(3));
                        $encrypted_storeId = $this->Encryption->encode($this->Session->read('store_id'));
                        $encypted_merchantId = $this->Encryption->encode(AuthComponent::User('merchant_id'));
                        $this->redirect(array('controller' => 'users', 'action' => 'customerDashboard', $encrypted_storeId, $encypted_merchantId));
                    }
                    else{
                        $this->redirect(array('controller' => 'users', 'action' => 'registration'));
                    }
                } else {
                    $this->Session->setFlash(__('Some problem has been occured in your registration process, please try again later'), 'flash_error');
                }
            } else {
                $errors = $this->User->validationErrors;
            }
        }
        $states = $this->states();
        $this->set('states', $states);
        $this->set(compact('googleSiteKey'));
    }

    /* ------------------------------------------------
      Function name:accountActivation()
      Description:Activating user account
      created:21/8/2015
      ----------------------------------------------------- */

    public function accountActivation($token = null) {
        $this->layout = false;
        $this->autoRender = false;
        $user = $this->User->find('first', array('conditions' => array('User.activation_token' => $token)));
        if (!empty($user)) {
            $this->User->updateAll(
                    array('User.is_active' => 1, 'User.activation_token' => 1), array('User.id' => $user['User']['id'])
            );
            $this->loadModel('Store');
            $storeEmail = $this->Store->fetchStoreUrl($user['User']['store_id']);
            $this->Session->setFlash(__('Your account has been activated successfully, you can login now'), 'flash_success');
            $this->redirect(HTTP_ROOT);
        } else {
            $this->Session->setFlash(__('This link has been used before'), 'flash_error');
            $this->redirect(HTTP_ROOT);
        }
    }

    /* ------------------------------------------------
      Function name:login()
      Description:Registration  Form for the  End customer
      created:22/7/2015
      ----------------------------------------------------- */

    public function login($layout_type = null) {
        $this->layout = $this->store_layout;
        $this->set('title', 'Store Login');

        /*         * *****************Guest Ordering************ */
//        $this->Session->delete('Order');
//        $this->Session->delete('Cart');
//        $this->Session->delete('cart');
//        $this->Session->delete('FetchProductData');
//        $this->Session->delete('Coupon');
//        $this->Session->delete('Discount');
        $decrypt_storeId = $this->Session->read('store_id');
        $decrypt_merchantId = $this->Session->read('merchant_id');
        $encrypted_storeId = $this->Encryption->encode($decrypt_storeId);
        $encrypted_merchantId = $this->Encryption->encode($decrypt_merchantId);
        $avalibilty_status = $this->Common->checkStoreAvalibility($decrypt_storeId);
        if ($avalibilty_status == 1) {
            
        } else {
            $this->set(compact('avalibilty_status'));
        }
        $this->loadModel('Store');
        //$current_date = date('Y-m-d');
        $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));


        $finaldata = array();
        $today = 1;
        $orderType = 2;
        $finaldata = $this->Common->getNextDayTimeRange($current_date, $today, $orderType);

        $pickcurrent_date = $finaldata['currentdate'];
        $explodeVal = explode("-", $pickcurrent_date);
        $pickcurrentDateVar = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];
        $this->set(compact('pickcurrentDateVar'));


        $today = 1;
        $orderType = 3;
        $finaldata = $this->Common->getNextDayTimeRange($current_date, $today, $orderType);

        $delcurrent_date = $finaldata['currentdate'];
        $explodeVal = explode("-", $delcurrent_date);
        $delcurrentDateVar = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];
        $this->set(compact('delcurrentDateVar'));


        $this->loadModel('CountryCode');
        $countryCode = $this->CountryCode->fetchAllCountryCode();
        //$this->set(compact('countryCode'));
        //$this->set(compact('countryCode', 'encrypted_storeId', 'encrypted_merchantId'));

        $time_break = $finaldata['time_break'];
        $store_data = $finaldata['store_data'];
        $storeBreak = $finaldata['storeBreak'];
        $time_range = $finaldata['time_range'];
        $setPre = $finaldata['setPre'];

        $this->set(compact('storeBreak', 'countryCode', 'time_break', 'time_range', 'store_data', 'encrypted_storeId', 'encrypted_merchantId', 'setPre'));

        $this->loadModel('StoreAvailability');
        $this->StoreAvailability->bindModel(
                array(
            'hasOne' => array(
                'StoreBreak' => array(
                    'className' => 'StoreBreak',
                    'foreignKey' => 'store_availablity_id',
                    'conditions' => array('StoreBreak.is_deleted' => 0, 'StoreBreak.is_active' => 1, 'StoreBreak.store_id' => $decrypt_storeId),
                )
            )
                ), false
        );
        $availabilityInfo = $this->StoreAvailability->getStoreAvailabilityDetails($decrypt_storeId);
        $this->set('availabilityInfo', $availabilityInfo);

        /*         * **************************************************************** */
        if ($this->request->is('post')) {
            $storeId = $this->Session->read('store_id');
            $merchantId = $this->Session->read('merchant_id');
            if ($storeId) {
                $this->request->data['User']['store_id'] = $storeId;
            }

            $this->User->set($this->request->data);
            if ($this->User->validates()) {
                if (isset($this->data['User']['remember'])) {
                    $this->request->data['User']['remember'] = 1;
                } else {
                    $this->request->data['User']['remember'] = 0;
                }
                if ($this->data['User']['remember'] == 1) {
                    $this->Cookie->write('Auth.email', $this->data['User']['email'], false, 604800);
                    $this->Cookie->write('Auth.password', $this->data['User']['password'], false, 604800);
                    $this->set('cookies', '1');
                    unset($this->request->data['User']['remember']);
                } else {
                    $this->Cookie->delete('Auth');
                }
                $password = AuthComponent::password($this->request->data['User']['password']);
                //$user = $this->User->find("first", array("conditions" => array("User.email" => $this->data['User']['email'], "User.password" => $password,"User.role_id" => 4,"User.store_id" => $storeId,'User.is_active'=>1,'User.is_deleted'=>0)));
                //pr($this->data);die;
                $user = $this->User->find("first", array("conditions" => array("User.email" => $this->data['User']['email'], "User.password" => $password, "User.role_id" => array('4', '5'), "User.merchant_id" => $merchantId, 'User.is_active' => 1, 'User.is_deleted' => 0)));
                //pr($user);die;
                if (!empty($user)) {
                    if ($user['User']['is_deleted'] == 0) {
                        if ($user['User']['is_active'] == 1) {
                            if ($this->Auth->login()) {
                                $roleId = AuthComponent::User('role_id');
                                $this->Session->write('login_date_time', $this->Common->gettodayDate(3));
                                $encrypted_storeId = $this->Encryption->encode($this->Session->read('store_id'));
                                $encypted_merchantId = $this->Encryption->encode(AuthComponent::User('merchant_id'));
                                $this->redirect(array('controller' => 'users', 'action' => 'customerDashboard', $encrypted_storeId, $encypted_merchantId));
                            } else {
                                $this->Session->setFlash(__('Invalid email or password, please try again'), 'flash_error');
                            }
                        } else {
                            $this->Session->setFlash(__('Your account is not activated, please activate your account by click on the activation link provided in registration email'), 'flash_error');
                        }
                    } else {
                        $this->Session->setFlash(__('Account no longer exists'), 'flash_error');
                    }
                } else {
                    $this->Session->setFlash(__('Invalid email or password, please try again'), 'flash_error');
                }
            }
        } else {
            $this->set('rem', $this->Cookie->read('Auth.email'));
            if ($this->Cookie->read('Auth.email')) {
                $this->request->data['User']['email'] = $this->Cookie->read('Auth.email');
                $this->request->data['User']['password'] = $this->Cookie->read('Auth.password');
            }
            $this->_featuredItemData($decrypt_storeId, $decrypt_merchantId);
            $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme'), 'hasMany' => array('StoreGallery', 'StoreContent')));
            $sData = $this->Store->findById($decrypt_storeId, array('deal_page'));
            if (!empty($sData) && $sData['Store']['deal_page'] == 1) {
                $this->_deal_page($decrypt_storeId);
            }
            if (in_array(KEYWORD, array('IOF-C2-H', 'IOF-C2-V', 'IOF-C3-H', 'IOF-C3-V'))) {
                $this->loadModel('HomeImage');
                $this->set('themeImage', $this->HomeImage->getStoreThemeImages($decrypt_storeId));
            }
            //get home page modal poup data
            $this->loadModel('HomeModal');
            $modalPopupData = $this->HomeModal->find('first', array('conditions' => array('is_active' => 1, 'is_deleted' => 0, 'store_id' => $decrypt_storeId, 'added_from' => 1)));
            $this->set('modalPopupData', $modalPopupData);
        }
    }

    /* ------------------------------------------------
      Function name:login()
      Description:Registration  Form for the  End customer
      created:22/7/2015
      ----------------------------------------------------- */

    public function popuplogin($layout_type = null) {
        $this->layout = 'ajax';
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $storeId = $this->Session->read('store_id');
            $merchantId = $this->Session->read('merchant_id');
            if ($storeId) {
                $this->request->data['User']['store_id'] = $storeId;
            }

            $this->User->set($this->request->data);

            if ($this->User->validates()) {

                if (DESIGN != 4) {
                    $this->request->data['User']['password'] = $this->request->data['password'];
                    $this->request->data['User']['email'] = $this->request->data['email'];
                    $this->request->data['User']['remember'] = $this->request->data['remember'];
                } else {
                    $this->request->data['User']['password'] = $this->request->data['password'];
                    $this->request->data['User']['email'] = $this->request->data['email'];
                }
                $password = AuthComponent::password($this->request->data['User']['password']);
                $user = $this->User->find("first", array("conditions" => array("User.email" => $this->data['User']['email'], "User.password" => $password, "User.role_id" => array('4', '5'), "User.merchant_id" => $merchantId, 'User.is_active' => 1, 'User.is_deleted' => 0)));
                if (!empty($user)) {
                    if (!empty($this->data['User']['remember'])) {
                        $this->Cookie->write('Auth.email', $this->data['User']['email'], false, 604800);
                        $this->Cookie->write('Auth.password', $this->data['User']['password'], false, 604800);
                        $this->set('cookies', '1');
                        unset($this->request->data['User']['remember']);
                    } else {
                        $this->Cookie->delete('Auth');
                    }
                    if ($user['User']['is_deleted'] == 0) {
                        if ($user['User']['is_active'] == 1) {
                            if ($this->Auth->login()) {

                                $roleId = AuthComponent::User('role_id');
                                $this->Session->write('login_date_time', $this->Common->gettodayDate(3));
                                $encrypted_storeId = $this->Encryption->encode($this->Session->read('store_id'));
                                $encypted_merchantId = $this->Encryption->encode(AuthComponent::User('merchant_id'));
                                $response['status'] = 1;
                                $response['msg'] = 'Login successful';
                            } else {
                                $response['status'] = 0;
                                $response['msg'] = 'Invalid email or password, please try again';
                            }
                        } else {
                            $response['status'] = 0;
                            $response['msg'] = 'Your account is not activated, please activate your account by click on the activation link provided in registration email';
                        }
                    } else {
                        $response['status'] = 0;
                        $response['msg'] = 'Account no longer exists';
                    }
                } else {
                    $response['status'] = 0;
                    $response['msg'] = 'Invalid email or password, please try again';
                }
            }
        } else {
            $response['status'] = 0;
            $response['msg'] = 'Invalid request';
        }
        return json_encode($response);
    }

    public function signIn($layout_type = null) {
        $this->layout = $this->store_layout;
        $this->set('title', 'Store Login');        
        if ($this->request->is('post')) {
            $storeId = $this->Session->read('store_id');
            $merchantId = $this->Session->read('merchant_id');
            if ($storeId) {
                $this->request->data['User']['store_id'] = $storeId;
            }

            $this->User->set($this->request->data);
            if ($this->User->validates()) {
                if (isset($this->data['User']['remember'])) {
                    $this->request->data['User']['remember'] = 1;
                } else {
                    $this->request->data['User']['remember'] = 0;
                }
                if ($this->data['User']['remember'] == 1) {
                    $this->Cookie->write('Auth.email', $this->data['User']['email'], false, 604800);
                    $this->Cookie->write('Auth.password', $this->data['User']['password'], false, 604800);
                    $this->set('cookies', '1');
                    unset($this->request->data['User']['remember']);
                } else {
                    $this->Cookie->delete('Auth');
                }
                $password = AuthComponent::password($this->request->data['User']['password']);
                //$user = $this->User->find("first", array("conditions" => array("User.email" => $this->data['User']['email'], "User.password" => $password, "User.role_id" => array('4', '5'), "User.store_id" => $storeId, 'User.is_active' => 1, 'User.is_deleted' => 0)));
                $user = $this->User->find("first", array("conditions" => array("User.email" => $this->data['User']['email'], "User.password" => $password, "User.role_id" => array('4', '5'), "User.merchant_id" => $merchantId, 'User.is_active' => 1, 'User.is_deleted' => 0)));
                if (!empty($user)) {
                    if ($user['User']['is_deleted'] == 0) {
                        if ($user['User']['is_active'] == 1) {
                            if ($this->Auth->login()) {
                                $roleId = AuthComponent::User('role_id');
                                $this->Session->write('login_date_time', $this->Common->gettodayDate(3));
                                $encrypted_storeId = $this->Encryption->encode($this->Session->read('store_id'));
                                $encypted_merchantId = $this->Encryption->encode(AuthComponent::User('merchant_id'));
                                $this->redirect(array('controller' => 'users', 'action' => 'customerDashboard', $encrypted_storeId, $encypted_merchantId));
                            } else {
                                $this->Session->setFlash(__('Invalid email or password, please try again'), 'flash_error');
                            }
                        } else {
                            $this->Session->setFlash(__('Your account is not activated, please activate your account by click on the activation link provided in registration email'), 'flash_error');
                        }
                    } else {
                        $this->Session->setFlash(__('Account no longer exists'), 'flash_error');
                    }
                } else {
                    $this->Session->setFlash(__('Invalid email or password, please try again'), 'flash_error');
                }
            }
        } else {
            $this->set('rem', $this->Cookie->read('Auth.email'));
            if ($this->Cookie->read('Auth.email')) {
                $this->request->data['User']['email'] = $this->Cookie->read('Auth.email');
                $this->request->data['User']['password'] = $this->Cookie->read('Auth.password');
            }
        }
    }

    /* ------------------------------------------------
      Function name:dashboard()
      Description:Registration  Form for the  End customer
      created:22/7/2015
      ----------------------------------------------------- */

    public function customerDashboard($encrypted_storeId, $encrypted_merchantId, $orderId = null) {

        $this->layout = $this->store_inner_pages;
        $this->loadModel('Store');
//        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
//        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $decrypt_storeId = (empty($encrypted_storeId)) ? $this->Session->read('store_id') : $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = (empty($decrypt_merchantId)) ? $this->Session->read('merchant_id') : $this->Encryption->decode($encrypted_merchantId);
        $avalibilty_status = $this->Common->checkStoreAvalibility($decrypt_storeId); // I will check the time avalibility of the store
        $store_data = $this->Store->fetchStoreDetail($decrypt_storeId, $decrypt_merchantId);
//        $this->Session->delete('Order');
//        $this->Session->delete('Cart');
//        $this->Session->delete('cart');
//        $this->Session->delete('FetchProductData');
//        $this->Session->delete('Coupon');
//        $this->Session->delete('Discount');
        if ($avalibilty_status != 1) {
            $this->set(compact('avalibilty_status'));
        }
        $this->loadModel('DeliveryAddress');
        $userID = AuthComponent::User('id');
        $defaultAddress = $this->DeliveryAddress->fetchDefaultAddress($userID);
        $this->set(compact('defaultAddress', 'store_data', 'encrypted_storeId', 'encrypted_merchantId', 'orderId'));
    }

    /* ------------------------------------------------
      Function name:logout()
      Description:For logout of the user
      created:22/7/2015
      ----------------------------------------------------- */

    public function logout() {
        $this->Session->delete('orderOverview');
        $this->Session->delete('Order');
        $this->Session->delete('Cart');
        $this->Session->delete('cart');
        $this->Session->delete('FetchProductData');
        $this->Session->delete('Coupon');
        $this->Session->delete('Discount');
        $this->Session->delete('Auth.User');
        $this->Session->delete('GuestUser');
        $this->Session->delete('ordersummary');
        $this->Session->delete('delivery_fee');
        $this->Session->delete('checkForZone');
        $this->Session->delete('Zone');
        /* Hq user logout start */
        $this->Session->delete('Auth.hqusers');
        /* Hq user logout end */

        $this->redirect(array('controller' => 'users', 'action' => 'login'));
        // return $this->redirect($this->Auth->logout());
    }

    /* ------------------------------------------------
      Function name:checkEmail()
      Description:For logout of the user
      created:22/7/2015
      ----------------------------------------------------- */

    public function checkEmail($roleId = null) {
        $this->autoRender = false;
        if ($_GET) {
            $emailEntered = $_GET['data']['User']['email'];
            $storeId = "";
            $merchantId = "";
            if ($roleId == 4) {
                $storeId = $this->Session->read('store_id');
                $merchantId = $this->Session->read('merchant_id');
                $emailStatus = $this->User->emailCheck($roleId, $storeId, $merchantId, $emailEntered);
            }
            echo json_encode($emailStatus);
        }
    }

    /* ------------------------------------------------
      Function name:checkStoreEndUserEmail()
      Description:Check end user email
      created:22/7/2015
      ----------------------------------------------------- */

    public function checkStoreEndUserEmail() {
        $this->autoRender = false;
        if ($_GET) {
            $emailEntered = $_GET['data']['User']['email'];
            $storeId = "";
            $merchantId = "";
            $roleId = array('4', '5');
            $storeId = $this->Session->read('store_id');
            $merchantId = $this->Session->read('merchant_id');
            $emailStatus = $this->User->emailCheck($roleId, $storeId, $merchantId, $emailEntered);
            echo json_encode($emailStatus);
        }
    }

    /* ------------------------------------------------
      Function name:forgetPassword()
      Description:For forget password
      created:22/7/2015
      ----------------------------------------------------- */

    public function forgetPassword() {
        $this->layout = $this->store_layout;
        $this->autorender = false;
        $this->Session->delete('Order');
        $this->Session->delete('Cart');
        $this->Session->delete('cart');
        $this->Session->delete('FetchProductData');
        $this->Session->delete('Coupon');
        $this->Session->delete('Discount');
        if (!empty($this->data)) {
            $email = $this->request->data['User']['email'];
            $roleId = array(4, 5);
            $merchantId = $this->Session->read('merchant_id');
            if (!$merchantId) {
                $merchantId = "";
            }
            $storeId = $this->Session->read('store_id');
            if (!$storeId) {
                $storeId = '';
            }
            $userEmail = $this->User->checkForgetEmail($roleId, $storeId, $merchantId, $email); //Calling function on model for checking the email
            if (!empty($userEmail)) {
                $this->loadModel('EmailTemplate');
                $template_type = 'customer_forget_password';
                $this->loadModel('Store');
                $storeEmail = $this->Store->fetchStoreDetail($storeId);
                $emailTemplate = $this->EmailTemplate->storeTemplates($storeId, $merchantId, $template_type);
                if ($emailTemplate) {
                    if ($userEmail['User']['lname']) {
                        $fullName = $userEmail['User']['fname'] . " " . $userEmail['User']['lname'];
                    } else {
                        $fullName = $this->request->data['User']['fname'];
                    }
                    $token = time() . rand();
                    $userName = $userEmail['User']['email'];
                    $emailData = $emailTemplate['EmailTemplate']['template_message'];
                    $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                    $url = HTTP_ROOT . 'users/resetPassword/' . $token;
                    $activationLink = '<a href="' . $url . '">' . $url . '</a>';
                    $emailData = str_replace('{ACTIVE_LINK}', $activationLink, $emailData);
                    $subject = ucwords(str_replace('_', ' ', $emailTemplate['EmailTemplate']['template_subject']));
                    $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);
                    $storeAddress = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
                    $storePhone = $storeEmail['Store']['phone'];
                    $url = "http://" . $storeEmail['Store']['store_url'];
                    $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
                    $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
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
                            $this->request->data['User']['id'] = $userEmail['User']['id'];

                            $this->request->data['User']['forgot_token'] = $token;
                            $this->User->saveUserInfo($this->data['User']);
                            $this->Session->setFlash(__("A link to reset the password has been sent on your email"), 'flash_success');
                            $this->redirect(array('controller' => 'users', 'action' => 'forgetPassword'));
                        }
                    } catch (Exception $e) {
                        $this->Session->setFlash("Please try after some time", 'alert_failed');
                        $this->redirect(array('controller' => 'Stores', 'action' => 'forgetPassword'));
                    }
                }
            } else {
                $this->Session->setFlash(__('Email is not registered'), 'flash_error');
            }
        }
    }

    /* ------------------------------------------------
      Function name:resetPassword()
      Description:reset user password
      created:29/09/2015
      ----------------------------------------------------- */

    public function resetPassword($token = null, $adminType = null) {
        if (isset($adminType)) {
            if ($adminType == 1) {
                $this->layout = "super_login";
            } elseif ($adminType == 2) {
                $this->layout = "hq_login";
            } elseif ($adminType == 3) {
                $this->layout = "store_login";
            }
            $this->set(compact('adminType'));
        } else {
            $this->layout = $this->store_layout;
        }

        if ($this->data) {

            $records = $this->User->find('first', array('fields' => array('User.forgot_token', 'User.store_id'), 'conditions' => array('User.id' => $this->data['User']['id'])));
            if (empty($records['User']['forgot_token'])) {
                $this->Session->setFlash("Token has been expired.Please request another one", 'flash_error');
                //  $this->redirect(HTTP_ROOT . $this->Session->read('store_url'));
                $this->redirect(HTTP_ROOT);
            } else {
                $this->loadModel('Store');
                $storeEmail = $this->Store->fetchStoreUrl($records['User']['store_id']);
                $this->request->data['User']['forgot_token'] = "";
            }
            $this->request->data['User']['password'] = $this->data['User']['newpassword'];
            if ($this->User->save($this->data)) {
                $this->Session->setFlash(__('Password has been reset successfully, you can login now'), 'flash_success');
                if ($this->data['User']['check'] == 3) {
                    // $this->redirect(HTTP_ROOT . $storeEmail['Store']['store_url'].'/admin');
                    $this->redirect(HTTP_ROOT . 'admin');
                }
                if ($this->data['User']['check'] == 2) {
                    $this->redirect(HTTP_ROOT . 'hq/login');
                }
                if ($this->data['User']['check'] == 1) {
                    $this->redirect(HTTP_ROOT . 'super/login');
                }

                if ($this->data['User']['check'] == 4) {
                    $this->redirect(HTTP_ROOT);
                }
            } else {
                $this->Session->setFlash('Unable to save password', 'flash_error');
            }
        } else if (!empty($token)) {
            $record = $this->User->find('first', array('conditions' => array('User.forgot_token' => $token)));
            if ($record) {
                if ($adminType == 3) {
                    $this->set('adminrecord', $record);
                } elseif ($adminType == 2) {

                    $this->set('hqadminrecord', $record);
                } elseif ($adminType == 1) {

                    $this->set('superadminrecord', $record);
                } else {
                    $this->set('record', $record);
                }
            } else {
                $this->Session->setFlash("Token has been expired.Please request another one", 'flash_error');
                $this->redirect(HTTP_ROOT);
            }
        } else {
            $this->Session->setFlash("Cannot access this page directly", 'flash_error');
            $this->redirect(HTTP_ROOT);
        }
    }

    /* ------------------------------------------------
      Function name:myProfile()
      Description:This section will manage the profile of the user for Customer
      created:22/7/2015
      ----------------------------------------------------- */

    public function myProfile($encrypted_storeId, $encrypted_merchantId) {
        $this->layout = $this->store_inner_pages;
//        $this->Session->delete('Order');
//        $this->Session->delete('Cart');
//        $this->Session->delete('cart');
//        $this->Session->delete('FetchProductData');
//        $this->Session->delete('Coupon');
//        $this->Session->delete('Discount');
//        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
//        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $decrypt_storeId = (empty($encrypted_storeId)) ? $this->Session->read('store_id') : $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = (empty($decrypt_merchantId)) ? $this->Session->read('merchant_id') : $this->Encryption->decode($encrypted_merchantId);
        $this->loadModel('CountryCode');
        $countryCode = $this->CountryCode->fetchAllCountryCode();
        $this->set(compact('countryCode'));
        $this->User->bindModel(array('belongsTo' => array('CountryCode')), false);
        $userId = AuthComponent::User('id');
        $userResult = $this->User->find('first', array('conditions' => array('User.id' => $userId), 'recursive' => 2));
        $roleId = $userResult['User']['role_id'];
        $encryped_userId = $this->Encryption->encode($userResult['User']['id']);
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId', 'encryped_userId'));
        //$this->User->set($this->request->data);
        if (isset($this->request->data['User']['changepassword'])) {
            $this->request->data['User']['changepassword'] = 1;
        } else {
            $this->request->data['User']['changepassword'] = 0;
            $this->User->validator()->remove('password');
            $this->User->validator()->remove('password_match');
        }

        if (isset($this->request->data['User']['is_smsnotification'])) {
            $this->request->data['User']['is_smsnotification'] = 1;
        } else {
            $this->request->data['User']['is_smsnotification'] = 0;
        }

        if (isset($this->request->data['User']['is_newsletter'])) {
            $this->request->data['User']['is_newsletter'] = 1;
        } else {
            $this->request->data['User']['is_newsletter'] = 0;
        }

        if (isset($this->request->data['User']['is_emailnotification'])) {
            $this->request->data['User']['is_emailnotification'] = 1;
        } else {
            $this->request->data['User']['is_emailnotification'] = 0;
        }

        if ($this->request->is('post')) {
            if (empty($this->request->data['User']['password'])) {
                unset($this->request->data['User']['password'], $this->request->data['User']['oldpassword']);
            }
            $dbformatDate = $this->Dateform->formatDate($this->data['User']['dateOfBirth']);
            $this->request->data['User']['dateOfBirth'] = $dbformatDate;

            if ($this->request->data['User']['changepassword'] == 1) {
                $oldPassword = AuthComponent::password($this->data['User']['oldpassword']);
                if ($oldPassword != $userResult['User']['password']) {
                    $this->Session->setFlash(__("Please enter correct old password"), 'flash_error');
                    $this->redirect(array('controller' => 'Users', 'action' => 'myProfile', $encrypted_storeId, $encrypted_merchantId));
                }
            }
            $data['User'] = $this->request->data['User'];
            $data['User']['id'] = AuthComponent::User('id');
            $data['User']['state_id'] = 0;
            $data['User']['city_id'] = 0;
            $data['User']['zip_id'] = 0;
            $data['User']['city'] = trim($this->request->data['User']['city_id']);
            $data['User']['state'] = trim($this->request->data['User']['state_id']);
            $data['User']['zip'] = trim($this->request->data['User']['zip_id']);
            //$this->User->create();
            if ($this->User->save($data)) {
                $store_id = $this->Session->read('store_id');
                $merchant_id = $this->Session->read('merchant_id');
                $storeEmail = $this->Store->fetchStoreDetail($store_id);
                $user_email = AuthComponent::User('email');
                $fullName = AuthComponent::User('fname');
                if ($data['User']['is_news_check'] == 1) {
                    if ($data['User']['is_newsletter'] == 1) {
                        $template_type = 'subscribe_newsletter';
                    } else {
                        $template_type = 'unsubscribe_newsletter';
                    }
                    $this->loadModel('EmailTemplate');
                    $emailSuccess = $this->EmailTemplate->storeTemplates($store_id, $merchant_id, $template_type);
                    if ($emailSuccess) {
                        $emailData = $emailSuccess['EmailTemplate']['template_message'];
                        $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                        $subject = ucwords(str_replace('_', ' ', $emailSuccess['EmailTemplate']['template_subject']));
                        $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);
                        $storeAddress = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
                        $storePhone = $storeEmail['Store']['phone'];
                        $url = "http://" . $storeEmail['Store']['store_url'];
                        $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
                        $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                        $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                        $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);
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
                    }
                }
                if ($data['User']['is_email_check'] == 1) {
                    if ($data['User']['is_emailnotification'] == 1) {
                        $template_type = 'subscribe_email_notification';
                    } else {
                        $template_type = 'unsubscribe_email_notification';
                    }
                    $this->loadModel('EmailTemplate');
                    $emailSuccess = $this->EmailTemplate->storeTemplates($store_id, $merchant_id, $template_type);
                    if ($emailSuccess) {
                        $emailData = $emailSuccess['EmailTemplate']['template_message'];
                        $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                        $subject = ucwords(str_replace('_', ' ', $emailSuccess['EmailTemplate']['template_subject']));
                        $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);
                        $storeAddress = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
                        $storePhone = $storeEmail['Store']['phone'];
                        $url = "http://" . $storeEmail['Store']['store_url'];
                        $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
                        $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                        $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                        $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);
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
                    }
                }
                if ($data['User']['is_sms_check'] == 1) {
                    if ($data['User']['is_smsnotification'] == 1) {
                        $template_type = 'subscribe_sms_notification';
                    } else {
                        $template_type = 'unsubscribe_sms_notification';
                    }
                    $this->loadModel('EmailTemplate');
                    $emailSuccess = $this->EmailTemplate->storeTemplates($store_id, $merchant_id, $template_type);
                    if ($emailSuccess) {
                        $emailData = $emailSuccess['EmailTemplate']['template_message'];
                        $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                        $subject = ucwords(str_replace('_', ' ', $emailSuccess['EmailTemplate']['template_subject']));
                        $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);
                        $storeAddress = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
                        $storePhone = $storeEmail['Store']['phone'];
                        $url = "http://" . $storeEmail['Store']['store_url'];
                        $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
                        $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                        $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                        $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);
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
                    }
                }
                $this->Session->setFlash(__('Profile has been updated successfully.'), 'flash_success');
                $this->redirect(array('controller' => 'Users', 'action' => 'myProfile', $encrypted_storeId, $encrypted_merchantId));
            } else {
                $this->Session->setFlash(__('Profile could not be updated, please try again'), 'flash_error');
            }
        }


        $this->set(compact('roleId'));
        $this->request->data['User'] = $userResult['User'];
        $this->request->data['CountryCode'] = $userResult['CountryCode'];
        $this->request->data['User']['dateOfBirth'] = $this->Dateform->us_format($userResult['User']['dateOfBirth']);
        if (!empty($userResult['User']['state'])) {
            $this->request->data['User']['state_id'] = $userResult['User']['state'];
        } elseif (isset($userResult['State']['name']) && !empty($userResult['State']['name'])) {
            $this->request->data['User']['state_id'] = $userResult['State']['name'];
        } else {
            $this->request->data['User']['state_id'] = "";
        }

        if (!empty($userResult['User']['city'])) {
            $this->request->data['User']['city_id'] = $userResult['User']['city'];
        } elseif (isset($userResult['City']['name']) && !empty($userResult['City']['name'])) {
            $this->request->data['User']['city_id'] = $userResult['City']['name'];
        } else {
            $this->request->data['User']['city_id'] = "";
        }
        if (!empty($userResult['User']['zip'])) {
            $this->request->data['User']['zip_id'] = $userResult['User']['zip'];
        } elseif (isset($userResult['Zip']['zipcode']) && !empty($userResult['Zip']['zipcode'])) {
            $this->request->data['User']['zip_id'] = $userResult['Zip']['zipcode'];
        } else {
            $this->request->data['User']['zip_id'] = "";
        }
    }

    /* ------------------------------------------------
      Function name:deliveryAddress()
      Description:This section will manage the delivery address portion
      created:27/7/2015
      ----------------------------------------------------- */

    public function deliveryAddress($encrypted_storeId = null, $encrypted_merchantId = null, $orderId = null) {
        $this->layout = $this->store_inner_pages;
//        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
//        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $decrypt_storeId = (empty($encrypted_storeId)) ? $this->Session->read('store_id') : $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = (empty($decrypt_merchantId)) ? $this->Session->read('merchant_id') : $this->Encryption->decode($encrypted_merchantId);


        $avalibilty_status = $this->Common->checkStoreAvalibility($decrypt_storeId); // I will check the time avalibility of the store
        if ($avalibilty_status != 1) {
            $setPre = 1;
        } else {
            $setPre = 0;
        }
        $this->loadModel('Store');
        $this->loadModel('StoreHoliday');

        //$current_date = date('Y-m-d');
        $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        $today = 1;
        $orderType = 3;
        $finaldata = $this->Common->getNextDayTimeRange($current_date, $today, $orderType);
        $time_break = $finaldata['time_break'];
        $store_data = $finaldata['store_data'];
        $storeBreak = $finaldata['storeBreak'];
        $time_range = $finaldata['time_range'];
        $current_date = $finaldata['currentdate'];
        $setPre = $finaldata['setPre'];

        $this->loadModel('DeliveryAddress');
        $userId = AuthComponent::User('id'); // Customer Id
        $roleId = AuthComponent::User('role_id');
        $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
        $checkaddress = $this->DeliveryAddress->checkAllAddress($userId, $roleId, $decrypt_storeId, $decrypt_merchantId); // It will call the function in the model to check the address either exist or not
        if (!$checkaddress) {
            $checkaddress = array();
        }
        $explodeVal = explode("-", $current_date);
        $currentDateVar = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];

        $this->set(compact('storeBreak', 'setPre', 'time_break', 'orderId', 'checkaddress', 'encrypted_storeId', 'encrypted_merchantId', 'time_range', 'currentDateVar', 'store_data'));
    }

    public function myDeliveryAddress($encrypted_storeId = null, $encrypted_merchantId = null) {
        $this->layout = $this->store_inner_pages;
        $decrypt_storeId = (empty($encrypted_storeId)) ? $this->Session->read('store_id') : $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = (empty($decrypt_merchantId)) ? $this->Session->read('merchant_id') : $this->Encryption->decode($encrypted_merchantId);
        $this->loadModel('Store');
        //$current_date = date('Y-m-d');
        $current_date = $this->Common->gettodayDate(1);
        $date = new DateTime($current_date);
        $current_day = $date->format('l');
        $this->Store->bindModel(
                array(
                    'hasMany' => array(
                        'StoreAvailability' => array(
                            'className' => 'StoreAvailability',
                            'foreignKey' => 'store_id',
                            'conditions' => array('StoreAvailability.day_name' => $current_day, 'StoreAvailability.is_deleted' => 0, 'StoreAvailability.is_active' => 1, 'is_closed' => 0),
                            'fields' => array('id', 'start_time', 'end_time')
                        )
                    )
                )
        );
        $store_data = $this->Store->fetchStoreDetail($decrypt_storeId, $decrypt_merchantId); // call model function to fetch store details
        $current_array = array();
        $time_break = array();
        if ($store_data) {
            if (!empty($store_data['StoreAvailability'])) {
                $start = $store_data['StoreAvailability'][0]['start_time'];
                $end = $store_data['StoreAvailability'][0]['end_time'];
                $StoreCutOff = $this->Store->fetchStoreCutOff($decrypt_storeId);
                $cutTime = '-' . $StoreCutOff['Store']['cutoff_time'] . ' minutes';
                $end = date("H:i:s", strtotime("$cutTime", strtotime($end)));
                $time_ranges = $this->Common->getStoreTime($start, $end); // calling Common Component


                foreach ($time_ranges as $time_key => $time_val) {
                    $current_time = strtotime($this->Common->gettodayDate(2));
                    $time_key_str = strtotime($time_key);
                    if ($time_key_str > $current_time) {
                        $current_array[$time_key] = $time_val;
                    }
                }
                if ($store_data['Store']['is_break_time'] == 1) {
                    $this->loadModel('StoreBreak');
                    $store_break = $this->StoreBreak->fetchStoreBreak($store_data['Store']['id'], $store_data['StoreAvailability'][0]['id']);
                    $time_break1 = array();
                    $time_break2 = array();
                    if ($store_data['Store']['is_break1'] == 1) {
                        $break_start_time = $store_break['StoreBreak']['break1_start_time'];
                        $break_end_time = $store_break['StoreBreak']['break1_end_time'];
                        $time_break1 = $this->Common->getStoreTime($break_start_time, $break_end_time);
                    }
                    if ($store_data['Store']['is_break2'] == 1) {
                        $break_start_time = $store_break['StoreBreak']['break2_start_time'];
                        $break_end_time = $store_break['StoreBreak']['break2_end_time'];
                        $time_break2 = $this->Common->getStoreTime($break_start_time, $break_end_time);
                    }
                    $time_break = array_unique(array_merge($time_break1, $time_break2), SORT_REGULAR);
                }
            }
        }
        $time_range = $current_array;
        $this->loadModel('DeliveryAddress');
        $userId = AuthComponent::User('id'); // Customer Id
        $roleId = AuthComponent::User('role_id');
        $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
        $checkaddress = $this->DeliveryAddress->checkAllAddress($userId, $roleId, $decrypt_storeId, $decrypt_merchantId); // It will call the function in the model to check the address either exist or not
        if (!$checkaddress) {
            $checkaddress = array();
        }

        $this->set(compact('time_break', 'checkaddress', 'encrypted_storeId', 'encrypted_merchantId', 'time_range'));
    }

    /* ------------------------------------------------
      Function name:addAddress()
      Description:This section will add the delivery address portion
      created:27/7/2015
      ----------------------------------------------------- */

    public function addAddress($encrypted_storeId = null, $encrypted_merchantId = null) {
        $this->layout = $this->store_inner_pages;
//        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
//        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $decrypt_storeId = (empty($encrypted_storeId)) ? $this->Session->read('store_id') : $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = (empty($decrypt_merchantId)) ? $this->Session->read('merchant_id') : $this->Encryption->decode($encrypted_merchantId);


        $this->loadModel('DeliveryAddress');
        $userId = AuthComponent::User('id'); // Customer Id
        $roleId = AuthComponent::User('role_id');
        $checkaddress = $this->DeliveryAddress->checkAllAddress($userId, $roleId, $decrypt_storeId, $decrypt_merchantId); // It will call the function in the model to check the address either exist or not
        $label1 = 0;
        $label2 = 0;
        $label3 = 0;
        $label4 = 0;
        $label5 = 0;
        $zoneError = '';
        if (!empty($checkaddress)) {
            foreach ($checkaddress as $address) {
                if ($address['DeliveryAddress']['label'] == 1) {
                    $label1 = 1;
                } elseif ($address['DeliveryAddress']['label'] == 2) {
                    $label2 = 1;
                } elseif ($address['DeliveryAddress']['label'] == 3) {
                    $label3 = 1;
                } elseif ($address['DeliveryAddress']['label'] == 4) {
                    $label4 = 1;
                } elseif ($address['DeliveryAddress']['label'] == 5) {
                    $label5 = 1;
                }
            }
        }
        if ($this->request->is('post')) {
            $tmp = $this->request->data;
            if (empty($tmp)) {
                $this->Session->setFlash(__('Please select atleast one address type'), 'flash_error');
                $this->redirect(array('controller' => 'users', 'action' => 'addAddress', $encrypted_storeId, $encrypted_merchantId));
            }
            if (isset($tmp['DeliveryAddress'])) {
                $zipCode = trim($tmp['DeliveryAddress']['zipcode'], " ");
                $stateName = trim($tmp['DeliveryAddress']['state'], " ");
                $cityName = strtolower($tmp['DeliveryAddress']['city']);
                $cityName = trim(ucwords($cityName));
                $address = trim(ucwords($tmp['DeliveryAddress']['address']));
                $dlocation = $address . " " . $cityName . " " . $stateName . " " . $zipCode;
                $adjuster_address2 = str_replace(' ', '+', $dlocation);
                $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $adjuster_address2 . '&sensor=false');
                $output = json_decode($geocode);
                $tmp['DeliveryAddress']['user_id'] = AuthComponent::User('id');
                $tmp['DeliveryAddress']['store_id'] = $decrypt_storeId;
                $tmp['DeliveryAddress']['merchant_id'] = $decrypt_merchantId;
                if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                    
                } else {
                    $latitude = @$output->results[0]->geometry->location->lat;
                    $longitude = @$output->results[0]->geometry->location->lng;
                    $tmp['DeliveryAddress']['latitude'] = $latitude;
                    $tmp['DeliveryAddress']['longitude'] = $longitude;
                }
                $tmp['DeliveryAddress']['label'] = 1;
                $data['DeliveryAddress'] = $tmp['DeliveryAddress'];
                $zoneData = $this->Common->addressInZone($data);
                if (empty($zoneData)) {
                    $zoneError.= "Home address is out of delivery area.<br />";
                } else {
                    $this->DeliveryAddress->create();
                    $this->DeliveryAddress->saveAddress($data);
                }
            }
            if (isset($tmp['DeliveryAddress1'])) {
                $zipCode = trim($tmp['DeliveryAddress1']['zipcode'], " ");
                $stateName = trim($tmp['DeliveryAddress1']['state'], " ");
                $cityName = strtolower($tmp['DeliveryAddress1']['city']);
                $cityName = trim(ucwords($cityName));
                $address = trim(ucwords($tmp['DeliveryAddress1']['address']));
                $dlocation = $address . " " . $cityName . " " . $stateName . " " . $zipCode;
                $adjuster_address2 = str_replace(' ', '+', $dlocation);
                $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $adjuster_address2 . '&sensor=false');
                $output = json_decode($geocode);
                $tmp['DeliveryAddress1']['user_id'] = AuthComponent::User('id');
                $tmp['DeliveryAddress1']['store_id'] = $decrypt_storeId;
                $tmp['DeliveryAddress1']['merchant_id'] = $decrypt_merchantId;
                if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                    
                } else {
                    $latitude = @$output->results[0]->geometry->location->lat;
                    $longitude = @$output->results[0]->geometry->location->lng;
                    $tmp['DeliveryAddress1']['latitude'] = $latitude;
                    $tmp['DeliveryAddress1']['longitude'] = $longitude;
                }
                $tmp['DeliveryAddress1']['label'] = 2;
                $data1['DeliveryAddress'] = $tmp['DeliveryAddress1'];
                $zoneData2 = $this->Common->addressInZone($data1);
                if (empty($zoneData2)) {
                    $zoneError.= "Work address is out of delivery area.<br />";
                } else {
                    $this->DeliveryAddress->create();
                    $this->DeliveryAddress->saveAddress($data1);
                }
            }
            if (isset($tmp['DeliveryAddress2'])) {
                $zipCode = trim($tmp['DeliveryAddress2']['zipcode'], " ");
                $stateName = trim($tmp['DeliveryAddress2']['state'], " ");
                $cityName = strtolower($tmp['DeliveryAddress2']['city']);
                $cityName = trim(ucwords($cityName));
                $address = trim(ucwords($tmp['DeliveryAddress2']['address']));
                $dlocation = $address . " " . $cityName . " " . $stateName . " " . $zipCode;
                $adjuster_address2 = str_replace(' ', '+', $dlocation);
                $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $adjuster_address2 . '&sensor=false');
                $output = json_decode($geocode);
                $tmp['DeliveryAddress2']['user_id'] = AuthComponent::User('id');
                $tmp['DeliveryAddress2']['store_id'] = $decrypt_storeId;
                $tmp['DeliveryAddress2']['merchant_id'] = $decrypt_merchantId;
                if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                    
                } else {
                    $latitude = @$output->results[0]->geometry->location->lat;
                    $longitude = @$output->results[0]->geometry->location->lng;
                    $tmp['DeliveryAddress2']['latitude'] = $latitude;
                    $tmp['DeliveryAddress2']['longitude'] = $longitude;
                }
                $tmp['DeliveryAddress2']['label'] = 3;
                $data2['DeliveryAddress'] = $tmp['DeliveryAddress2'];
                $zoneData3 = $this->Common->addressInZone($data2);
                if (empty($zoneData3)) {
                    $zoneError.= "Other address is out of delivery area.<br />";
                } else {
                    $this->DeliveryAddress->create();
                    $this->DeliveryAddress->saveAddress($data2);
                }
            }
            if (isset($tmp['DeliveryAddress3'])) {
                $zipCode = trim($tmp['DeliveryAddress3']['zipcode'], " ");
                $stateName = trim($tmp['DeliveryAddress3']['state'], " ");
                $cityName = strtolower($tmp['DeliveryAddress3']['city']);
                $cityName = trim(ucwords($cityName));
                $address = trim(ucwords($tmp['DeliveryAddress3']['address']));
                $dlocation = $address . " " . $cityName . " " . $stateName . " " . $zipCode;
                $adjuster_address3 = str_replace(' ', '+', $dlocation);
                $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $adjuster_address3 . '&sensor=false');
                $output = json_decode($geocode);
                $tmp['DeliveryAddress3']['user_id'] = AuthComponent::User('id');
                $tmp['DeliveryAddress3']['store_id'] = $decrypt_storeId;
                $tmp['DeliveryAddress3']['merchant_id'] = $decrypt_merchantId;
                if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                    
                } else {
                    $latitude = @$output->results[0]->geometry->location->lat;
                    $longitude = @$output->results[0]->geometry->location->lng;
                    $tmp['DeliveryAddress3']['latitude'] = $latitude;
                    $tmp['DeliveryAddress3']['longitude'] = $longitude;
                }
                $tmp['DeliveryAddress3']['label'] = 4;
                $data3['DeliveryAddress'] = $tmp['DeliveryAddress3'];
                $zoneData4 = $this->Common->addressInZone($data3);
                if (empty($zoneData4)) {
                    $zoneError.= "Address 4 is out of delivery area.<br />";
                } else {
                    $this->DeliveryAddress->create();
                    $this->DeliveryAddress->saveAddress($data3);
                }
            }

            if (isset($tmp['DeliveryAddress4'])) {
                $zipCode = trim($tmp['DeliveryAddress4']['zipcode'], " ");
                $stateName = trim($tmp['DeliveryAddress4']['state'], " ");
                $cityName = strtolower($tmp['DeliveryAddress4']['city']);
                $cityName = trim(ucwords($cityName));
                $address = trim(ucwords($tmp['DeliveryAddress4']['address']));
                $dlocation = $address . " " . $cityName . " " . $stateName . " " . $zipCode;
                $adjuster_address4 = str_replace(' ', '+', $dlocation);
                $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $adjuster_address4 . '&sensor=false');
                $output = json_decode($geocode);
                $tmp['DeliveryAddress4']['user_id'] = AuthComponent::User('id');
                $tmp['DeliveryAddress4']['store_id'] = $decrypt_storeId;
                $tmp['DeliveryAddress4']['merchant_id'] = $decrypt_merchantId;
                if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                    
                } else {
                    $latitude = @$output->results[0]->geometry->location->lat;
                    $longitude = @$output->results[0]->geometry->location->lng;
                    $tmp['DeliveryAddress4']['latitude'] = $latitude;
                    $tmp['DeliveryAddress4']['longitude'] = $longitude;
                }
                $tmp['DeliveryAddress4']['label'] = 5;
                $data4['DeliveryAddress'] = $tmp['DeliveryAddress4'];
                $zoneData5 = $this->Common->addressInZone($data4);
                if (empty($zoneData5)) {
                    $zoneError.= "Address 5 is out of delivery area.<br />";
                } else {
                    $this->DeliveryAddress->create();
                    $this->DeliveryAddress->saveAddress($data4);
                }
            }

            if (!empty($zoneError)) {
                $this->Session->setFlash(__($zoneError), 'flash_error');
            } else {
                $this->Session->setFlash(__('Delivery Address has been saved successfully'), 'flash_success');
            }
            $this->redirect(array('controller' => 'users', 'action' => 'deliveryAddress', $encrypted_storeId, $encrypted_merchantId));
        }
        $this->loadModel('CountryCode');
        $countryCode = $this->CountryCode->fetchAllCountryCode();
        $this->set(compact('label1', 'label2', 'label3', 'label4', 'label5', 'countryCode', 'encrypted_storeId', 'encrypted_merchantId'));
    }

    /* ------------------------------------------------
      Function name:checkusersadddress()
      Description:This section will verify the address
      created:27/7/2015
      ----------------------------------------------------- */

    public function checkusersadddress() {
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            $result_address = $this->checkaddress($_POST['address'], $_POST['city'], $_POST['state'], $_POST['zip']);
        }
    }

    /* ------------------------------------------------
      Function name:updateAddress()
      Description:This section will manage the delivery address portion
      created:27/7/2015
      ----------------------------------------------------- */

    public function updateAddress($encrypted_storeId = null, $encrypted_merchantId = null, $encrypt_deliveryAddressId = null) {
        $this->layout = $this->store_inner_pages;
//        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
//        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $decrypt_storeId = (empty($encrypted_storeId)) ? $this->Session->read('store_id') : $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = (empty($decrypt_merchantId)) ? $this->Session->read('merchant_id') : $this->Encryption->decode($encrypted_merchantId);
        $decrypt_deliveryAddressId = $this->Encryption->decode($encrypt_deliveryAddressId);
        $this->loadModel('CountryCode');
        $countryCode = $this->CountryCode->fetchAllCountryCode();
        $this->set(compact('countryCode'));
        $this->loadModel('DeliveryAddress');
        $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
        $resultAddress = $this->DeliveryAddress->fetchAddress($decrypt_deliveryAddressId);
        if ($this->request->data) {
            $decrypt_storeId = $this->Encryption->decode($this->request->data['DeliveryAddress']['store_id']);
            $decrypt_merchantId = $this->Encryption->decode($this->request->data['DeliveryAddress']['merchant_id']);
            $encypted_storeId = $this->request->data['DeliveryAddress']['store_id'];
            $encypted_merchantId = $this->request->data['DeliveryAddress']['merchant_id'];
            $zipCode = trim($this->request->data['DeliveryAddress']['zipcode'], " ");
            $stateName = trim($this->data['DeliveryAddress']['state'], " ");
            $cityName = strtolower($this->request->data['DeliveryAddress']['city']);
            $cityName = trim(ucwords($cityName));
            $address = trim(ucwords($this->request->data['DeliveryAddress']['address']));
            $dlocation = $address . " " . $cityName . " " . $stateName . " " . $zipCode;
            $adjuster_address2 = str_replace(' ', '+', $dlocation);
            $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $adjuster_address2 . '&sensor=false');
            $output = json_decode($geocode);
            $this->request->data['DeliveryAddress']['user_id'] = AuthComponent::User('id');
            $this->request->data['DeliveryAddress']['store_id'] = $decrypt_storeId;
            $this->request->data['DeliveryAddress']['merchant_id'] = $decrypt_merchantId;
            if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                
            } else {
                $latitude = @$output->results[0]->geometry->location->lat;
                $longitude = @$output->results[0]->geometry->location->lng;
                $this->request->data['DeliveryAddress']['latitude'] = $latitude;
                $this->request->data['DeliveryAddress']['longitude'] = $longitude;
            }
            $zoneData = $this->Common->addressInZone($this->request->data);
            $zoneError = '';
            if (empty($zoneData)) {
                $zoneError = "Order cannot be delivered to this address.";
            } else {
                if ($this->request->data['DeliveryAddress']['default'] == 1) {
                    $this->DeliveryAddress->updateAll(array('DeliveryAddress.default' => 0), array('DeliveryAddress.user_id' => $this->request->data['DeliveryAddress']['user_id']));
                }
                $result_sucess = $this->DeliveryAddress->saveAddress($this->request->data);
            }
            if (!empty($result_sucess)) {
                $this->Session->setFlash(__('Delivery Address has been updated successfully'), 'flash_success');
                $this->redirect(array('controller' => 'users', 'action' => 'deliveryAddress', $encrypted_storeId, $encrypted_merchantId));
            } else {
                if (!empty($zoneError)) {
                    $this->Session->setFlash(__($zoneError), 'flash_error');
                } else {
                    $this->Session->setFlash(__('Delivery Address could not be updated, please try again'), 'flash_error');
                }
            }
        }
        if ($resultAddress) {
            $this->request->data = $resultAddress;
        }

        $addressId = $resultAddress['DeliveryAddress']['id'];
        $this->set(compact('addressId', 'encrypted_storeId', 'encrypted_merchantId'));
    }

    /* ------------------------------------------------
      Function name:checkusersadddress()
      Description:This section will verify the address
      created:27/7/2015
      ----------------------------------------------------- */

    public function getDeliveryAddress() {
        $this->layout = false;
        $this->loadModel('DeliveryAddress');
        $encrypted_storeId = $_POST['storeId'];
        $encrypted_merchantId = $_POST['merchantId'];
        $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
        if (empty($_POST['deliveryId'])) {
            $delivery = $this->DeliveryAddress->fetchfirstAddress(AuthComponent::User('id'));
            $deliveryID = $delivery['DeliveryAddress']['id'];
        } else {
            $deliveryID = $_POST['deliveryId'];
        }
        $resultAddress = $this->DeliveryAddress->fetchAddress($deliveryID);
        $this->set(compact('resultAddress', 'encrypted_storeId', 'encrypted_merchantId'));
    }

    /* ------------------------------------------------
      Function name:deleteDeliveryAddress()
      Description: Delete delivery address of user
      created:28/09/2015
      ----------------------------------------------------- */

    public function deleteDeliveryAddress($encrypted_storeId = null, $encrypted_merchantId = null, $encrypted_deliveryaddressId = null) {
        $this->autoRender = false;
        $this->loadModel('DeliveryAddress');
        $data['DeliveryAddress']['id'] = $this->Encryption->decode($encrypted_deliveryaddressId);
        $data['DeliveryAddress']['is_deleted'] = 1;
        if ($this->DeliveryAddress->saveAddress($data)) {
            $this->Session->setFlash(__('Delivery Address has been deleted'), 'flash_success');
            $this->redirect($this->referer());
        } else {
            $this->Session->setFlash(__('Delivery Address could not be deleted, please try again'), 'flash_error');
            $this->redirect($this->referer());
        }
    }

    /* ------------------------------------------------
      Function name:orderType()
      Description:This section will look the order type
      created:27/7/2015
      ----------------------------------------------------- */

    public function orderType($orderId = null) {
        if ($this->Session->check('Auth.User.Order')) {
            $this->Session->delete('Auth.User.Order');
        }
        $encrypted_storeId = $this->Encryption->encode($this->Session->read('store_id'));
        $encrypted_merchantId = $this->Encryption->encode(AuthComponent::User('merchant_id'));
        if ($this->data['Order']['type'] == 3) { // Home Delivery
            $order_type = $this->data['Order']['type'];
            $this->Session->write('Order.order_type', $order_type); // Type of Delivery
            $this->redirect(array('controller' => 'users', 'action' => 'deliveryAddress', $encrypted_storeId, $encrypted_merchantId, $orderId));
        } elseif ($this->data['Order']['type'] == 2) {// Pick Up
            $order_type = $this->data['Order']['type'];
            $this->Session->write('Order.order_type', $order_type); //Type of Delivery
            $this->redirect(array('controller' => 'users', 'action' => 'pickUp', $encrypted_storeId, $encrypted_merchantId, $orderId));
        } elseif ($this->data['Order']['type'] == 1) {// Dinein
            $order_type = $this->data['Order']['type'];
            $this->Session->write('Order.order_type', $order_type); //Type of Delivery
            $this->redirect(array('controller' => 'users', 'action' => 'dineIn', $encrypted_storeId, $encrypted_merchantId));
        } else {// Nothing Selcteed
            $this->Session->setFlash(__('Please select order type'), 'flash_error');
            $this->redirect(array('controller' => 'users', 'action' => 'customerDashboard', $encrypted_storeId, $encrypted_merchantId, $orderId)); //
        }
    }

    /* ------------------------------------------------
      Function name:ordercatCheck()
      Description: // It will check either the order is pre-order  or Now and write the value ito the session
      created:27/7/2015
      ----------------------------------------------------- */

    public function ordercatCheck($orderId = null) {
        $this->autoRender = false; // It will check either the order is pre-order  or Now
        // pr(AuthComponent::User()); die;
        $encrypted_storeId = $this->Encryption->encode($this->Session->read('store_id'));
        $encrypted_merchantId = $this->Encryption->encode(AuthComponent::User('merchant_id'));
        if ($this->request->is('post')) {
            if (!empty($this->request->data['DeliveryAddress']['id'])) {
                $this->Session->write('selectedAddress', $this->request->data['DeliveryAddress']['id']);
            } else {
                $this->Session->delete('selectedAddress');
            }
            $type = $this->Session->read('Order.order_type');

            $nowData = $this->_checkNowTime();
            if (!empty($nowData['pickup_date_time'])) {
                if (!empty($this->request->data['Store']['pickup_date'])) {
                    $this->request->data['DeliveryAddress']['type'] = 1;
                    $this->request->data['Store']['pickup_time'] = $this->request->data['Store']['pickup_hour'] . ':' . $this->request->data['Store']['pickup_minute'] . ':00';
                } else {
                    $this->request->data['DeliveryAddress']['type'] = 0;
                    $this->request->data['Store']['pickup_time'] = $nowData['pickup_time'];
                    $this->request->data['Store']['pickup_date'] = $nowData['pickup_date'];
                }
            } elseif (!empty($this->request->data['Store']['pickup_date'])) {
                $this->request->data['DeliveryAddress']['type'] = 1;
                $this->request->data['Store']['pickup_time'] = $this->request->data['Store']['pickup_hour'] . ':' . $this->request->data['Store']['pickup_minute'] . ':00';
            }
            $pickupTime = $this->Common->storeTimeFormateUser($this->request->data['Store']['pickup_time']);
            if ($type == 2) {
                $order_cattype = $this->request->data['DeliveryAddress']['type'];
                $this->Session->write('Order.is_preorder', $order_cattype);
                if ($this->data['DeliveryAddress']['type'] == 0) { //Now
                    if ($this->Session->check('Auth.User.Order.delivery_address_id')) {
                        $this->Session->delete('Order.delivery_address_id');
                    }
                    if (AuthComponent::User('Order.store_pickup_time')) {
                        $this->Session->delete('Order.store_pickup_time');
                    }
                    if (AuthComponent::User('Order.store_pickup_date')) {
                        $this->Session->delete('Order.store_pickup_date');
                    }
                    $this->Session->write('Order.store_pickup_date', $this->request->data['Store']['pickup_date']);
                    $this->Session->write('Order.store_pickup_time', $pickupTime);
                    $this->Session->write('Order.pickup_store_id', $this->data['Store']['id']); //Store Id to fin details of store
                    $this->redirect(array('controller' => 'products', 'action' => 'items', $encrypted_storeId, $encrypted_merchantId, $orderId));
                } elseif ($this->data['DeliveryAddress']['type'] == 1) { // PreOrder
                    $order_cattype = $this->data['DeliveryAddress']['type'];
                    $this->Session->write('Order.is_preorder', $order_cattype);
                    if ($this->request->is('post')) {
                        $this->Session->write('Order.store_pickup_time', $pickupTime); // Pick up time of Store
                        $this->Session->write('Order.store_pickup_date', $this->request->data['Store']['pickup_date']); // Pick up date of
                    }
                    $this->redirect(array('controller' => 'products', 'action' => 'items', $encrypted_storeId, $encrypted_merchantId, $orderId));
                }
            } elseif ($type == 3) {
                if ($this->request->is('post')) {
                    $this->loadModel('DeliveryAddress');
                    $DelAddress = $this->DeliveryAddress->fetchAddress($this->request->data['DeliveryAddress']['id']);
                    $this->Common->setZonefee($DelAddress);
                    $zoneData = $this->Session->read('Zone.id');
                    if (empty($zoneData)) {
                        $this->Session->setFlash(__("Order cannot be delivered to this address."), 'flash_error');
                        $this->redirect($this->referer()); //
                    }
                    $this->Session->write('Order.store_pickup_time', $pickupTime); // Pick up time of Store
                    $this->Session->write('Order.store_pickup_date', $this->request->data['Store']['pickup_date']); // Pick up date of
                }
                $order_cattype = $this->data['DeliveryAddress']['type'];
                $this->Session->write('Order.is_preorder', $order_cattype);
                $this->Session->write('Order.delivery_address_id', $this->data['DeliveryAddress']['id']);

                $this->redirect(array('controller' => 'products', 'action' => 'items', $encrypted_storeId, $encrypted_merchantId, $orderId));
            }
        }
    }

    /* ------------------------------------------------
      Function name:pickUp()
      Description: // For Pick Up
      created:27/7/2015
      ----------------------------------------------------- */

    public function pickUp($encrypted_storeId = null, $encrypted_merchantId = null, $orderId = null) {   // It will check either the order is pre-order  or Now
        $this->layout = $this->store_inner_pages;
        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $avalibilty_status = $this->Common->checkStoreAvalibility($decrypt_storeId); // I will check the time avalibility of the store
        if ($avalibilty_status != 1) {
            $setPre = 1;
        } else {
            $setPre = 0;
        }
        $this->loadModel('Store');
        $this->loadModel('StoreHoliday');
        //$current_date = date('Y-m-d');
        $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        $today = 1;
        $orderType = 2;
        $finaldata = $this->Common->getNextDayTimeRange($current_date, $today, $orderType);
        $time_break = $finaldata['time_break'];
        $store_data = $finaldata['store_data'];
        $storeBreak = $finaldata['storeBreak'];
        $time_range = $finaldata['time_range'];
        $current_date = $finaldata['currentdate'];
        $setPre = $finaldata['setPre'];

        $explodeVal = explode("-", $current_date);
        $currentDateVar = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];
        $this->set(compact('storeBreak', 'setPre', 'orderId', 'time_break', 'time_range', 'store_data', 'encrypted_storeId', 'encrypted_merchantId', 'currentDateVar'));
    }

    /* ------------------------------------------------
      Function name:dineIn()
      Description: // For dineIn Booking
      created:27/7/2015
      ----------------------------------------------------- */

    public function dineIn($encrypted_storeId = null, $encrypted_merchantId = null) {   // It will check either the order is pre-order  or Now
        $this->layout = $this->store_inner_pages;
        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $avalibilty_status = $this->Common->checkStoreAvalibility($decrypt_storeId); // I will check the time avalibility of the store
        if ($avalibilty_status != 1) {
            $setPre = 1;
        } else {
            $setPre = 0;
        }
        $this->loadModel('Store');
        $this->loadModel('StoreAvailability');
        $this->loadModel('StoreHoliday');
        $this->loadModel('Booking');
        $this->loadModel('StorePrintHistory');


        $store = $this->Store->fetchStoreDetail($decrypt_storeId, $decrypt_merchantId);
        $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        $today = 1;
        $orderType = 1;
        $finaldata = $this->Common->getNextDayTimeRange($current_date, $today, $orderType);
        $time_break = $finaldata['time_break'];
        $store_data = $finaldata['store_data'];
        $storeBreak = $finaldata['storeBreak'];
        $time_range = $finaldata['time_range'];
        $current_date = $finaldata['currentdate'];
        $setPre = $finaldata['setPre'];

        $explodeVal = explode("-", $current_date);
        $currentDateVar = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];

        $i = 1;
        $number_person = array();
        for ($i; $i < 30; $i++) {
            $number_person[$i] = $i;
        }
        $this->set(compact('storeBreak', 'setPre', 'number_person', 'time_break', 'time_range', 'store_data', 'store', 'encrypted_storeId', 'encrypted_merchantId', 'currentDateVar'));



        if ($this->request->is('post')) {
            $this->request->data['Booking']['store_id'] = $decrypt_storeId;
            $this->request->data['Booking']['user_id'] = AuthComponent::User('id');
            $reservationDate = $this->Dateform->formatDate($this->request->data['Booking']['start_date']);
            $ResTime = $this->request->data['Store']['pickup_hour'] . ':' . $this->request->data['Store']['pickup_minute'] . ':00';
            $reservationDateTime = $reservationDate . " " . $ResTime;
            $this->request->data['Booking']['reservation_date'] = $reservationDateTime;
            $save_result = $this->Booking->saveBookingDetails($this->data); // call on model to save data
            if ($save_result) {

                if ($store['Store']['is_dinein_printer'] == 1) {
                    $last_id = $this->Booking->getLastInsertId();
                    $aPrintData = array();
                    $aPrintData['id'] = '';
                    $aPrintData['merchant_id'] = $this->Session->read('merchant_id');
                    $aPrintData['store_id'] = $this->Session->read('store_id');
                    $aPrintData['order_id'] = $last_id;
                    $aPrintData['order_number'] = $last_id;
                    $aPrintData['type'] = '3'; //DineIn Printer
                    $this->StorePrintHistory->saveStorePrintHistory($aPrintData);
                }

                $template_type = 'customer_dine_in_request';
                $this->loadModel('DefaultTemplate');
                $roleId = AuthComponent::User('role_id');
                //$emailSuccess = $this->EmailTemplate->storeTemplates($decrypt_storeId, $decrypt_merchantId, $template_type);
                $emailSuccess = $this->DefaultTemplate->find('first', array('conditions' => array('DefaultTemplate.template_code' => $template_type, 'DefaultTemplate.is_default' => 1)));
                $fullName = "Admin";
                $number_person = $this->data['Booking']['number_person']; //no of person
                $start_date = $this->data['Booking']['start_date'];
                
                $start_date = $this->Common->storeTimeFormateUser($start_date, true, $this->Session->read('store_id'));
                $start_date = explode(' ', $start_date);
                $start_date = $start_date[0];
                $start_time = $start_date[1];
                
                //$start_time = date('H:i a', strtotime($ResTime));
                $customer_name = AuthComponent::User('fname') . " " . AuthComponent::User('lname');
                if ($this->data['Booking']['special_request']) {
                    $special_request = $this->data['Booking']['special_request'];
                } else {
                    $special_request = "N/A";
                }
                if ($emailSuccess) {
                    $storeEmail = $this->Store->fetchStoreDetail($decrypt_storeId);
                    if (($store['Store']['notification_type'] == 1 || $store['Store']['notification_type'] == 3) && (!empty($store['Store']['notification_email']))) {
                        $storeEmailid = trim($store['Store']['notification_email']);
                    } else {
                        $storeEmailid = trim($store['Store']['email_id']);
                    }
                    $customerEmail = trim(AuthComponent::User('email'));
                    $contactPerson = AuthComponent::User('fname') . " " . AuthComponent::User('lname') . " " . AuthComponent::User('phone');
                    $emailData = $emailSuccess['DefaultTemplate']['template_message'];
                    $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                    $emailData = str_replace('{BOOKING_DATE}', $start_date, $emailData);
                    $emailData = str_replace('{BOOKING_TIME}', $start_time, $emailData);
                    $emailData = str_replace('{NO_PERSON}', $number_person, $emailData);
                    $emailData = str_replace('{SPECIAL_REQUEST}', $special_request, $emailData);
                    $emailData = str_replace('{CUSTOMER_NAME}', $customer_name, $emailData);
                    $emailData = str_replace('{CONTACT_PERSON}', $contactPerson, $emailData);
                    $subject = ucwords(str_replace('_', ' ', $emailSuccess['DefaultTemplate']['template_subject']));
                    $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);
                    $storeAddress = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
                    $storePhone = $storeEmail['Store']['phone'];
                    $url = "http://" . $storeEmail['Store']['store_url'];
                    $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
                    $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                    $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                    $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);
                    $this->Email->to = $storeEmailid;
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
                    // $this->Email->delivery = 'smtp';
                    try {
                        $this->Email->send();
                    } catch (Exception $e) {
                        
                    }

                    if (($store['Store']['notification_type'] == 2 || $store['Store']['notification_type'] == 3) && (!empty($store['Store']['notification_number']))) {
                        $mobnumber = '+1' . str_replace(array('(', ')', ' ', '-'), '', $store['Store']['notification_number']);
                    } else {
                        $mobnumber = '+1' . str_replace(array('(', ')', ' ', '-'), '', $store['Store']['phone']);
                    }
                    $contactPerson = AuthComponent::User('fname') . " " . AuthComponent::User('lname') . " " . AuthComponent::User('phone');
                    $smsData = $emailSuccess['DefaultTemplate']['sms_template'];
                    $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                    $smsData = str_replace('{BOOKING_DATE}', $start_date, $smsData);
                    $smsData = str_replace('{BOOKING_TIME}', $start_time, $smsData);
                    $smsData = str_replace('{NO_PERSON}', $number_person, $smsData);
                    $smsData = str_replace('{SPECIAL_REQUEST}', $special_request, $smsData);
                    $smsData = str_replace('{CONTACT_PERSON}', $contactPerson, $smsData);
                    $smsData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $smsData);
                    $smsData = str_replace('{STORE_PHONE}', $mobnumber, $smsData);
                    $message = $smsData;
                    $this->Common->sendSmsNotificationFront($mobnumber, $message);
                }
                $this->Session->setFlash(__('Your request has been submitted, you will receive a confirmation email shortly. Thank you!'), 'flash_success');
                $this->redirect(array('controller' => 'users', 'action' => 'dineIn', $encrypted_storeId, $encrypted_merchantId)); //
            } else {
                $this->Session->setFlash(__('Reservation Request could not be submitted, please try again'), 'flash_error');
                $this->redirect(array('controller' => 'users', 'action' => 'dineIn', $encrypted_storeId, $encrypted_merchantId)); //
            }
        }
    }

    /* ------------------------------------------------
      Function name: getStoreTime()
      Description: User for getting time for different date selected
      created: 12/8/2015
      ----------------------------------------------------- */

    public function getStoreTime() {   // It will check either the order is pre-order  or Now
        $this->layout = 'ajax';
        if ($this->request->is('ajax')) {
            $type1 = $_POST['type1'];
            $type2 = $_POST['type2'];
            $type3 = @$_POST['type3'];
            $orderType = $_POST['orderType'];
            $storeId = $this->Session->read('store_id'); //$this->Encryption->decode($_POST['storeId']);
            $merchantId = $this->Session->read('merchant_id'); //$this->Encryption->decode($_POST['merchantId']);
            $this->loadModel('StoreAvailability');
            $this->loadModel('Store');
            $this->loadModel('StoreHoliday');
            $date_shuffle = explode("-", $_POST['date']);
            $new_date = $date_shuffle[2] . '-' . $date_shuffle[0] . '-' . $date_shuffle[1];
            $selected_day = date('l', strtotime($new_date));
            $store_data = $this->Store->fetchStoreBreak($storeId);
            $store_availability = $this->StoreAvailability->getStoreInfoForDay($selected_day, $storeId); // get store detail
            $holidayList = $this->StoreHoliday->getStoreHolidaylistDate($storeId, $new_date);
            $current_array = array();
            $time_break = array();
            $storeBreak = array();
            //$todayDate = date('m-d-Y');
            $todayDate = date("m-d-Y", (strtotime($this->Common->storeTimeZoneUser('', date("Y-m-d H:i:s")))));

            if (empty($holidayList)) {
                if (!empty($store_availability)) {
                    $start = $store_availability['StoreAvailability']['start_time'];
                    $end = $store_availability['StoreAvailability']['end_time'];
                    $StoreCutOff = $this->Store->fetchStoreCutOff($storeId);
                    $cutTime = '-' . $StoreCutOff['Store']['cutoff_time'] . ' minutes';
                    $end = date("H:i:s", strtotime("$cutTime", strtotime($end)));
                    $orderType = $this->request->data['orderType'];
                    $preOrder = null; //$this->request->data['preOrder'];

                    if (strtotime(str_replace('-', '/', $_POST['date'])) == strtotime(str_replace('-', '/', $todayDate))) {
                        $start = $this->Common->getStartTime($start, true, $orderType, $preOrder, $end);
                    } else {
                        $start = $this->Common->getStartTime($start, false, $orderType, $preOrder, $end);
                    }

                    $time_ranges = $this->Common->getStoreTime($start, $end, $orderType); // calling Common Component
                    $current_array = $time_ranges;


                    if ($store_data['Store']['is_break_time'] == 1) {
                        $this->loadModel('StoreBreak');
                        $store_break = $this->StoreBreak->fetchStoreBreak($store_data['Store']['id'], $store_availability['StoreAvailability']['id']);
                        $time_break1 = array();
                        $time_break2 = array();
                        if ($store_data['Store']['is_break1'] == 1) {
                            $break_start_time = $store_break['StoreBreak']['break1_start_time'];
                            $break_end_time = $store_break['StoreBreak']['break1_end_time'];
                            $storeBreak[0]['start'] = $store_break['StoreBreak']['break1_start_time'];
                            $storeBreak[0]['end'] = $store_break['StoreBreak']['break1_end_time'];
                            $time_break1 = $this->Common->getStoreTime($break_start_time, $break_end_time);
                        }
                        if ($store_data['Store']['is_break2'] == 1) {
                            $break_start_time = $store_break['StoreBreak']['break2_start_time'];
                            $break_end_time = $store_break['StoreBreak']['break2_end_time'];
                            $storeBreak[1]['start'] = $store_break['StoreBreak']['break2_start_time'];
                            $storeBreak[1]['end'] = $store_break['StoreBreak']['break2_end_time'];
                            $time_break2 = $this->Common->getStoreTime($break_start_time, $break_end_time);
                        }
                        $time_break = array_unique(array_merge($time_break1, $time_break2), SORT_REGULAR);
                    }
                }
            }
            $time_range = $current_array;
            $this->set("selectedDate", $_POST['date']);
            //$this->set("selectedDate",$current_date);
            $this->set(compact('time_break', 'time_range', 'type1', 'type2', 'type3', 'storeBreak', 'orderType'));
        }
    }

    public function selectStore() {
        $this->layout = false;
    }

    /* ------------------------------------------------
      Function name: guestOrdering()
      Description: User for guest order
      created: 19/8/2015
      ----------------------------------------------------- */

    public function guestOrdering() {
        if ($this->request->is(array('post', 'put')) && (!empty($this->request->data['DeliveryAddress']['email']) || !empty($this->request->data['PickUpAddress']['email']))) {
            //prx($this->request->data);
            $this->loadModel('DeliveryAddress');
            $encrypted_storeId = $this->Encryption->encode($this->Session->read('store_id'));
            $encrypted_merchantId = $this->Encryption->encode($this->Session->read('merchant_id'));
            $order_type = $this->data['Order']['type'];
            $this->Session->write('Order.order_type', $order_type);
	    $this->Session->write('Cart.segment_type', $order_type);
            $preOrderallowed = $this->Store->checkPreorder($this->Session->read('store_id'), $this->Session->read('merchant_id'));
            if (empty($preOrderallowed)) {
                //$pickupTime=$this->Common->getNowDelayTime($type);
                //$pickupDate= date("m-d-Y", strtotime($this->Common->storeTimeZoneUser('',(date("Y-m-d H:i:s")))));
                $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
                $orderType = $order_type;
                $today = 1;
                $finaldata = $this->Common->getNextDayTimeRange($current_date, $today, $orderType);
                $timearray = array_diff($finaldata['time_range'], $finaldata['time_break']);
                $pickupTime = reset($timearray);
                $explodeVal = explode("-", $finaldata['currentdate']);
                $finaldata['currentdate'] = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];
                $pickupDate = $finaldata['currentdate'];
            } else {
                $this->request->data['Store']['pickup_time'] = $this->request->data['Store']['pickup_hour'] . ':' . $this->request->data['Store']['pickup_minute'] . ':00';
                $pickupTime = $this->Common->storeTimeFormateUser($this->data['Store']['pickup_time']);
                if ($order_type == 2) {
                    $pickupDate = $this->data['PickUp']['pickup_date'];
                } else {
                    $pickupDate = $this->data['Delivery']['pickup_date'];
                }
            }
            if ($order_type == 2) {
                $data['DeliveryAddress']['user_id'] = 0;
                $data['DeliveryAddress']['store_id'] = $this->Session->read('store_id');
                $data['DeliveryAddress']['merchant_id'] = $this->Session->read('merchant_id');
                $data['DeliveryAddress']['name_on_bell'] = $this->request->data['PickUpAddress']['name_on_bell'];
                $data['DeliveryAddress']['phone'] = $this->request->data['PickUpAddress']['phone'];
                $data['DeliveryAddress']['email'] = $this->request->data['PickUpAddress']['email'];
                $data['DeliveryAddress']['country_code_id'] = $this->request->data['PickUpAddress']['country_code_id'];
                $this->DeliveryAddress->saveAddress($data);
                $address_id = $this->DeliveryAddress->getLastInsertId();
                $order_cattype = $this->data['PickUp']['type'];
                $this->Session->write('Order.is_preorder', $order_cattype);
                $this->Session->delete('Order.store_pickup_date');
                $this->Session->write('Order.delivery_address_id', $address_id);
                $this->Session->write('Order.store_pickup_time', $pickupTime);
                $this->Session->write('Order.store_pickup_date', $pickupDate);
                $this->Session->write('Order.pickup_store_id', $this->Session->read('store_id'));
            } elseif ($order_type == 3) {
                $data['DeliveryAddress']['user_id'] = 0;
                $data['DeliveryAddress']['store_id'] = $this->Session->read('store_id');
                $data['DeliveryAddress']['merchant_id'] = $this->Session->read('merchant_id');
                $data['DeliveryAddress']['name_on_bell'] = $this->request->data['DeliveryAddress']['name_on_bell'];
                $data['DeliveryAddress']['phone'] = $this->request->data['DeliveryAddress']['phone'];
                $data['DeliveryAddress']['email'] = $this->request->data['DeliveryAddress']['email'];
                $data['DeliveryAddress']['address'] = $this->request->data['DeliveryAddress']['address'];
                $data['DeliveryAddress']['city'] = $this->request->data['DeliveryAddress']['city'];
                $data['DeliveryAddress']['state'] = $this->request->data['DeliveryAddress']['state'];
                $data['DeliveryAddress']['zipcode'] = $this->request->data['DeliveryAddress']['zipcode'];
                $data['DeliveryAddress']['country_code_id'] = $this->request->data['DeliveryAddress']['country_code_id'];

                $dlocation = $data['DeliveryAddress']['address'] . " " . $data['DeliveryAddress']['city'] . " " . $data['DeliveryAddress']['state'] . " " . $data['DeliveryAddress']['zipcode'];
                $adjuster_address2 = str_replace(' ', '+', $dlocation);
                $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $adjuster_address2 . '&sensor=false');
                $output = json_decode($geocode);
                if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                    
                } else {
                    $latitude = @$output->results[0]->geometry->location->lat;
                    $longitude = @$output->results[0]->geometry->location->lng;
                    $data['DeliveryAddress']['latitude'] = $latitude;
                    $data['DeliveryAddress']['longitude'] = $longitude;
                }
                $this->Common->setZonefee($data);
                $zoneData = $this->Session->read('Zone.id');
                if (empty($zoneData)) {
                    $this->Session->setFlash(__('Order cannot be delivered to this address.'), 'flash_error');
                    $this->redirect($this->referer());
                } else {
                    $this->loadModel('DeliveryAddress');
                    if ($this->DeliveryAddress->saveAddress($data)) {
                        $address_id = $this->DeliveryAddress->getLastInsertId();
                        $this->Session->write('Order.store_pickup_time', $pickupTime);
                        $this->Session->write('Order.store_pickup_date', $pickupDate);
                        $order_cattype = $this->data['Delivery']['type'];
                        $this->Session->write('Order.is_preorder', $order_cattype);
                        $this->Session->write('Order.delivery_address_id', $address_id);
                    } else {
                        $this->Session->setFlash(__('Somthing went wrong please try after some time.'), 'flash_error');
                        $this->redirect($this->referer());
                    }
                }
            }
            $this->redirect(array('controller' => 'products', 'action' => 'items', $encrypted_storeId, $encrypted_merchantId));
        }
    }

    /* ------------------------------------------------
      Function name: storeLocation()
      Description: Fetch store location
      created: 19/8/2015
      ----------------------------------------------------- */

    public function storeLocation($encrypted_storeId = null, $encrypted_merchantId = null) {
        $this->layout = $this->store_layout;
        $this->loadModel('Store');
        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $store_data = $this->Store->fetchStoreDetail($decrypt_storeId, $decrypt_merchantId);
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId', 'store_data'));

        $this->loadModel('StoreAvailability');
        $this->StoreAvailability->bindModel(
                array(
            'hasOne' => array(
                'StoreBreak' => array(
                    'className' => 'StoreBreak',
                    'foreignKey' => 'store_availablity_id',
                    'conditions' => array('StoreBreak.is_deleted' => 0, 'StoreBreak.is_active' => 1, 'StoreBreak.store_id' => $decrypt_storeId),
                )
            )
                ), false
        );
        $displayContactUsForm = $this->StoreSetting->findByStoreId($decrypt_storeId, array('display_contact_us_form'));
        $availabilityInfo = $this->StoreAvailability->getStoreAvailabilityDetails($decrypt_storeId);
        $this->set(compact('availabilityInfo', 'displayContactUsForm'));
    }

    /* ------------------------------------------------
      Function name: storePhoto()
      Description: Fetch store photo
      created: 19/8/2015
      ----------------------------------------------------- */

    public function storePhoto($encrypted_storeId = null, $encrypted_merchantId = null) {
        $this->layout = $this->store_layout;
        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId'));
    }

    public function myBillingInfo($encrypted_storeId = null, $encrypted_merchantId = null) {
//        $this->autoRender = false;
        if (AuthComponent::User()) {
            $userId = AuthComponent::User('id');
        } else {
            $userId = 0;
        }
        $this->loadModel('Store');
        $this->layout = $this->store_inner_pages;
        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);

        $this->loadModel('Store');
        $this->loadModel('NzsafeUser');
        $store_info = $this->Store->fetchStoreDetail($decrypt_storeId, $decrypt_merchantId);
        $LoginId = $store_info['Store']['api_username'];
        $TransactionKey = $store_info['Store']['api_password'];
        $this->NZGateway->setLogin($LoginId, $TransactionKey);
        $nzsafe_temp = $this->NzsafeUser->getUser($userId);
        $nzsafe_temp = $nzsafe_temp['NzsafeUser'];
        $nzsafe_info = array();
        $nzsafe_info['first_name'] = '';
        $nzsafe_info['last_name'] = '';
        $nzsafe_info['address_1'] = '';
        $nzsafe_info['city'] = '';
        $nzsafe_info['state'] = '';
        $nzsafe_info['postal_code'] = '';
        $nzsafe_info['country'] = '';
        $nzsafe_info['cc_number'] = '';
        $nzsafe_info['cc_exp'] = '';
        $nzsafe_info['customer_vault_id'] = '';
        if ($nzsafe_temp) {
            $response = $this->NZGateway->getVault($nzsafe_temp['customer_vault_id']);
            if (count($response) > 0) {
                $nzsafe_info['first_name'] = $this->Common->spaceToHtml($response['first_name']);
                $nzsafe_info['last_name'] = $this->Common->spaceToHtml($response['last_name']);
                $nzsafe_info['address_1'] = $this->Common->spaceToHtml($response['address_1']);
                $nzsafe_info['city'] = $this->Common->spaceToHtml($response['city']);
                $nzsafe_info['state'] = $this->Common->spaceToHtml($response['state']);
                $nzsafe_info['postal_code'] = $this->Common->spaceToHtml($response['postal_code']);
                $nzsafe_info['country'] = $this->Common->spaceToHtml($response['country']);
                $nzsafe_info['cc_number'] = $response['cc_number'];
                $nzsafe_info['customer_vault_id'] = $response['customer_vault_id'];
                $ccexp = $response['cc_exp'];
                if ($ccexp)
                    $ccexp = substr($ccexp, 0, 2) . '/20' . substr($ccexp, 2, 4);
                $nzsafe_info['cc_exp'] = $ccexp;
            }
        }
        $this->set(compact('nzsafe_info', 'encrypted_storeId', 'encrypted_merchantId'));
    }

    public function deleteBillingInfo($encrypted_storeId, $encrypted_merchantId, $id) {

        if (AuthComponent::User()) {
            $userId = AuthComponent::User('id');
        } else {
            $userId = 0;
        }

        $this->autoRender = false;
        $this->loadModel('Store');
        $this->loadModel('NzsafeUser');
        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $store_info = $this->Store->fetchStoreDetail($decrypt_storeId, $decrypt_merchantId);
        $LoginId = $store_info['Store']['api_username'];
        $TransactionKey = $store_info['Store']['api_password'];
        $this->NZGateway->setLogin($LoginId, $TransactionKey);
        $res = $this->NZGateway->delVault($id);
        $nzsafe_info = $this->NzsafeUser->getUser($userId);

        if ($res['response_code'] == '100') {
            $nzsafe_info['NzsafeUser']['is_deleted'] = true;
            $nzsafe_info = $this->NzsafeUser->save($nzsafe_info);
            $this->Session->setFlash(__('Billing Information has been deleted'), 'flash_success');
            $this->redirect($this->redirect(array('controller' => 'users', 'action' => 'login')));
        } else {
            $this->Session->setFlash(__('Billing Information could not be deleted, please try again : </br>' . $res['responsetext']), 'flash_error');
            $this->redirect($this->referer());
        }
    }

    public function dologin() {
        $this->autoRender = false;
        $this->Session->delete('orderOverview');
        $this->Session->delete('Order');
        $this->Session->delete('Cart');
        $this->Session->delete('cart');
        $this->Session->delete('FetchProductData');
        $this->Session->delete('Coupon');
        $this->Session->delete('Discount');
        $this->Session->delete('Auth.User');
        $this->redirect(array('controller' => 'users', 'action' => 'login'));
    }

    public function clearsession() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $this->Session->delete('orderOverview');
        $this->Session->delete('Order');
        $this->Session->delete('Cart');
        $this->Session->delete('cart');
        $this->Session->delete('FetchProductData');
        $this->Session->delete('Coupon');
        $this->Session->delete('Discount');
        $this->Session->delete('Auth.User');
        $this->Session->delete('ordersummary');
        $this->Session->delete('GuestUser');
        return 1;
    }

    public function guestUserSignUp() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            $this->loadModel('CountryCode');
            $country_code = $this->CountryCode->fetchCountryCodeId($this->request->data['countryCode']);
            $this->Session->write('GuestUser.country_code_id', $this->request->data['countryCode']);
            $this->Session->write('GuestUser.countryCode', $country_code['CountryCode']['code']);
            $this->Session->write('GuestUser.name', $this->request->data['name']);
            $this->Session->write('GuestUser.userPhone', trim($this->request->data['userPhone']));
            $this->Session->write('GuestUser.email', trim($this->request->data['email']));
            $response['status'] = 1;
            $response['msg'] = 'Login successful';
        } else {
            $response['status'] = 0;
            $response['msg'] = 'Someting went wrong!';
        }
        return json_encode($response);
    }

    public function city() {
        $this->layout = false;
        $this->autoRender = false;
        $this->loadModel('City');
        if ($this->request->is('ajax') && !empty($this->request->data['state_id'])) {
            $result = $this->City->find('list', array('conditions' => array('state_id' => $this->request->data['state_id'])));
            $viewObject = new View($this, false);
            echo $viewObject->Form->input('User.city_id', array('type' => 'select', 'options' => @$result, 'class' => 'user-detail', 'label' => false, 'div' => false, 'empty' => 'Select City'));
        }
    }

    public function zip() {
        $this->layout = false;
        $this->autoRender = false;
        $this->loadModel('Zip');
        if ($this->request->is('ajax') && !empty($this->request->data['state_id']) && !empty($this->request->data['city_id'])) {
            $result = $this->Zip->find('list', array('fields' => array('id', 'zipcode'), 'conditions' => array('state_id' => $this->request->data['state_id'], 'city_id' => $this->request->data['city_id'])));
            $viewObject = new View($this, false);
            echo $viewObject->Form->input('User.zip_id', array('type' => 'select', 'options' => @$result, 'class' => 'user-detail', 'label' => false, 'div' => false, 'empty' => 'Select Zip'));
        }
    }

    private function _featuredItemData($store_id = null, $merchant_id = null) {
        $this->loadModel('StoreFeaturedSection');
        $this->loadModel('FeaturedItem');
        $this->StoreFeaturedSection->bindModel(
                array(
            'hasMany' => array(
                'FeaturedItem' => array(
                    'className' => 'FeaturedItem',
                    'foreignKey' => 'store_featured_section_id',
                    'conditions' => array('FeaturedItem.is_deleted' => 0, 'FeaturedItem.is_active' => 1, 'FeaturedItem.store_id' => $store_id, 'FeaturedItem.merchant_id' => $merchant_id),
                    'fields' => array('FeaturedItem.id', 'FeaturedItem.item_id'),
                    'limit' => 4,
                    'order' => array('FeaturedItem.position' => 'asc')
                )
            )
                ), false
        );
        $this->FeaturedItem->bindModel(
                array(
            'belongsTo' => array(
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id',
                    'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1, 'Item.store_id' => $store_id),
                    'fields' => array('Item.name', 'Item.image', 'Item.is_seasonal_item', 'Item.start_date', 'Item.end_date')
                )
            )
                ), false
        );
        $fields = array('StoreFeaturedSection.id', 'StoreFeaturedSection.featured_name', 'StoreFeaturedSection.image', 'StoreFeaturedSection.background_image');
        $sfData = $this->StoreFeaturedSection->find('all', array('fields' => $fields, 'recursive' => 2, 'conditions' => array('StoreFeaturedSection.store_id' => $store_id, 'StoreFeaturedSection.merchant_id' => $merchant_id, 'StoreFeaturedSection.is_active' => 1, 'StoreFeaturedSection.is_deleted' => 0), 'order' => array('StoreFeaturedSection.position' => 'asc')));
        $currentDate = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        $this->set('currentDate', $currentDate);
        $this->set('feturedData', $sfData);
    }

    private function _deal_page($store_id = null) {
        $this->loadModel('Coupon');
        $this->loadModel('ItemOffer');
        $this->loadModel('Offer');
        $this->loadModel('OfferDetail');
        $date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        $couponsData = $this->Coupon->find('all', array('conditions' => array('Coupon.store_id' => $store_id, 'Coupon.is_active' => 1, 'Coupon.is_deleted' => 0, 'Coupon.number_can_use > Coupon.used_count', 'Coupon.start_date <= ' => $date, 'Coupon.end_date >= ' => $date), 'fields' => array('id', 'name', 'coupon_code', 'discount', 'discount_type', 'image')));
        $this->ItemOffer->bindModel(
                array(
            'belongsTo' => array(
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id',
                    'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                    'fields' => array('id', 'name', 'image'),
                    'type' => "INNER"
                )
            )
                ), false
        );
        $itemOfferData = $this->ItemOffer->find('all', array('conditions' => array('ItemOffer.store_id' => $store_id, 'ItemOffer.is_active' => 1, 'ItemOffer.is_deleted' => 0, 'ItemOffer.start_date <= ' => $date, 'ItemOffer.end_date >= ' => $date), 'fields' => array()));
        $this->loadModel('Item');
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
        $this->Offer->bindModel(
                array(
                    'belongsTo' => array(
                        'Item' => array(
                            'className' => 'Item',
                            'foreignKey' => 'item_id',
                            'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                            'fields' => array('name', 'image', 'category_id'),
                            'type' => 'INNER'
                        ),
                    )
        ));
        $promotionalOfferData = $this->Offer->find('all', array('recursive' => 2, 'conditions' => array('Offer.store_id' => $store_id, 'Offer.is_active' => 1, 'Offer.is_deleted' => 0, 'OR' => array(array('Offer.offer_start_date <= ' => $date, 'Offer.offer_end_date >= ' => $date), array('Offer.offer_start_date' => NULL, 'Offer.offer_end_date' => NULL))), 'fields' => array('item_id', 'description', 'offerImage')));
        $deals = 0;
        if (!empty($couponsData) || !empty($itemOfferData) || !empty($promotionalOfferData)) {
            $deals = 1;
        }
        $this->loadModel('StoreDeals');
        $storeDealData = $this->StoreDeals->findByStoreId($store_id);
        $this->set(compact('storeDealData', 'couponsData', 'itemOfferData', 'promotionalOfferData', 'deals'));
    }

}
