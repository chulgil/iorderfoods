<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('HqAppController', 'Controller');

class HqusersController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Paginator', 'Common', 'Dateform', 'HqCommon');
    public $helper = array('Encryption', 'Paginator', 'Form', 'DateformHelper', 'Common', 'Hq');
    public $uses = array('MerchantGallery', 'MerchantContent', 'User', 'StoreGallery', 'Store', 'MerchantStoreRequest', 'Category', 'Tab', 'Permission', 'Merchant', 'StoreReview', 'Plan', 'Merchant', 'StorePayment', 'HqUser', 'SocialMedia', 'StoreReviewImage');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('getMerchantNewsLetterContent', 'logout', 'merchant', 'location', 'gallery', 'staticContent', 'checkMerchantEmail', 'forgetPassword', 'resetPassword', 'accountActivation', 'contact_us', 'newsletter', 'checkHqEndUserEmail', 'city', 'zip', 'getTermsAndPolicyData', 'getStateByCity', 'getState', 'storeRedirect');
        $merchantDetail = $this->Merchant->getMerchantDetail($this->Session->read('hq_id'));
        $id = $merchantDetail['Merchant']['id'];
        $name = $merchantDetail['Merchant']['name'];
        $image = $merchantDetail['Merchant']['background_image'];
        $logo = $merchantDetail['Merchant']['logo'];
        $logoType = $merchantDetail['Merchant']['logotype'];
        $bannerImage = $merchantDetail['Merchant']['banner_image'];
        $contactUsBgImage = $merchantDetail['Merchant']['contact_us_bg_image'];
        $phone = $merchantDetail['Merchant']['phone'];
        $m_email = $merchantDetail['Merchant']['email'];
        $hqroleId = $this->Session->read('Auth.hqusers.role_id');
        $this->set(compact('name', 'image', 'logo', 'logoType', 'bannerImage', 'id', 'hqroleId', 'phone', 'm_email', 'contactUsBgImage'));
        $this->_hqCommonData();
    }

    public function merchant() {
        $this->layout = 'merchant_front';
        $merchant_id = $this->Session->read('hq_id');
        $photo = $this->MerchantGallery->getSlidersImages($merchant_id);
        $this->set(compact('photo'));
        $this->loadModel('HomeContent');
        $merchantContentID = $this->MerchantContent->find('first', array('conditions' => array('MerchantContent.name' => 'Home', 'MerchantContent.name' => 'HOME', 'MerchantContent.merchant_id' => $merchant_id), 'fields' => array('MerchantContent.id')));
        if (!empty($merchantContentID)) {

//            $this->HomeContent->bindModel(
//                    array(
//                'belongsTo' => array(
//                    'LayoutBox' => array(
//                        'className' => 'LayoutBox',
//                        'foreignKey' => 'layout_box_id',
//                        'conditions' => array('LayoutBox.is_deleted' => 0, 'LayoutBox.is_active' => 1),
//                    )
//                )
//                    ), false
//            );
            $homeContentData1 = $this->HomeContent->find('all', array('recursive' => 2, 'fields' => array('HomeContent.id', 'HomeContent.content_layout_id', 'HomeContent.master_content_id'), 'conditions' => array('HomeContent.merchant_id' => $merchant_id, 'HomeContent.merchant_content_id' => $merchantContentID['MerchantContent']['id'], 'HomeContent.is_deleted' => 0, 'HomeContent.is_active' => 1), 'group' => array('HomeContent.master_content_id'), 'order' => array('HomeContent.master_content_id')));
            if (!empty($homeContentData1)) {
                foreach ($homeContentData1 as $key => $layoutData) {
                    $this->HomeContent->bindModel(
                            array(
                        'belongsTo' => array(
                            'LayoutBox' => array(
                                'className' => 'LayoutBox',
                                'foreignKey' => 'layout_box_id',
                                'conditions' => array('LayoutBox.is_deleted' => 0, 'LayoutBox.is_active' => 1),
                            )
                        )
                            ), false
                    );
                    $data = $this->HomeContent->find('all', array('conditions' => array('HomeContent.merchant_id' => $merchant_id, 'HomeContent.master_content_id' => $layoutData['HomeContent']['master_content_id'], 'HomeContent.is_deleted' => 0, 'HomeContent.is_active' => 1, 'HomeContent.merchant_content_id' => $merchantContentID['MerchantContent']['id']), 'order' => array('HomeContent.layout_box_id')));
                    $homeContentData[] = $data;
                }
            }
            //prx($homeContentData);
            //get home page modal poup data
            $this->loadModel('HomeModal');
            $modalPopupData = $this->HomeModal->find('first', array('conditions' => array('is_active' => 1, 'is_deleted' => 0, 'merchant_id' => $merchant_id, 'added_from' => 2)));
            $this->set(compact('homeContentData', 'modalPopupData'));
        }
    }

    private function _hqCommonData() {
        $storeCity = $this->Store->find('all', array('fields' => array('city'), 'conditions' => array('Store.merchant_id' => $this->Session->read('hq_id'), 'Store.is_deleted' => 0, 'Store.is_active' => 1), 'group' => array('Store.city')));
        $merchantList = $this->MerchantContent->find('all', array('conditions' => array('MerchantContent.merchant_id' => $this->Session->read('hq_id'), 'MerchantContent.is_active' => 1, 'MerchantContent.is_deleted' => 0), 'order' => array('MerchantContent.position' => 'ASC')));
        $socialLinks = $this->SocialMedia->find('first', array('conditions' => array('merchant_id' => $this->Session->read('hq_id'), 'store_id' => NULL, 'is_active' => 1, 'is_deleted' => 0)));
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->Store->bindModel(
                array(
            'hasOne' => array(
                'StoreSetting' => array(
                    'className' => 'StoreSetting',
                    'foreignKey' => 'store_id'
                )
            )
                ), false
        );
        $store = $this->Store->find('all', array('fields' => array('id', 'merchant_id', 'store_name', 'store_url', 'phone', 'address', 'city', 'state', 'latitude', 'logitude', 'zipcode', 'StoreSetting.merchant_online_order_btn'), 'conditions' => array('Store.merchant_id' => $this->Session->read('hq_id'), 'Store.is_deleted' => 0, 'Store.is_active' => 1)));
        $this->loadModel('MerchantConfiguration');
        $logoPosition = $this->MerchantConfiguration->find('first', array('conditions' => array('merchant_id' => $this->Session->read('hq_id')), 'fields' => array('logo_position', 'contact_active', 'map_zoom_level')));
        $this->set(compact('store', 'merchantList', 'storeCity', 'socialLinks', 'logoPosition'));
        $this->set('rem', $this->Cookie->read('Auth.email'));
        if ($this->Cookie->read('Auth.email')) {
            $this->request->data['User']['email-m'] = $this->Cookie->read('Auth.email');
            $this->request->data['User']['password-m'] = $this->Cookie->read('Auth.password');
        }
    }

    /* ------------------------------------------------
      Function name:login()
      Description:For merchant login
      created:27/7/2016
      ----------------------------------------------------- */

    public function login($layout_type = null) {
        $this->layout = false;
        $this->autoRender = false;
        $merchantId = $this->Session->read('hq_id');
        /*         * **************************************************************** */
        if ($this->request->is('ajax') && !empty($this->request->data['formData'])) {
            parse_str($this->request->data['formData'], $data);
            $this->request->data['User']['email'] = $data['data']['User']['email-m'];
            $this->request->data['User']['password'] = $data['data']['User']['password-m'];
            $this->request->data['User']['remember'] = (!empty($data['data']['User']['remember'])) ? 'no' : '';
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
                $user = $this->User->find("first", array("conditions" => array("User.email" => $this->data['User']['email'], "User.password" => $password, "User.role_id" => array('5', '4'), "User.merchant_id" => $merchantId, 'User.is_active' => 1, 'User.is_deleted' => 0), 'fields' => array('User.id', 'User.is_deleted', 'User.is_active')));
                if (!empty($user)) {
                    if ($user['User']['is_deleted'] == 0) {
                        if ($user['User']['is_active'] == 1) {
                            if ($this->Auth->login()) {
                                $this->Session->write('login_date_time', date('Y-m-d H:i:s'));
                                $response['status'] = 'Success';
                            } else {
                                $response['status'] = 'Error';
                                $response['msg'] = 'Invalid email or password, please try again';
                            }
                        } else {
                            $response['status'] = 'Error';
                            $response['msg'] = 'Your account is not activated, please activate your account by click on the activation link provided in registration email';
                        }
                    } else {
                        $response['status'] = 'Error';
                        $response['msg'] = 'Account no longer exists';
                    }
                } else {
                    $response['status'] = 'Error';
                    $response['msg'] = 'Invalid email or password, please try again';
                }
            } else {
                $response['status'] = 'Error';
                $response['msg'] = 'Validation error.';
            }
            return json_encode($response);
        } else {
            $this->set('rem', $this->Cookie->read('Auth.email'));
            if ($this->Cookie->read('Auth.email')) {
                $this->request->data['User']['email-m'] = $this->Cookie->read('Auth.email');
                $this->request->data['User']['password-m'] = $this->Cookie->read('Auth.password');
            }
        }
    }

    public function dashboard() {
        $this->layout = "merchant";
    }

    /* ------------------------------------------------
      Function name:logout()
      Description:For logout of the merchant
      created:27/7/2016
      ----------------------------------------------------- */

    public function logout() {

        /* store front user logout start */
        $this->Session->delete('Auth.User');
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
        $this->Session->delete('Zone');
        /* store front user logout end */
        $this->Session->delete('Auth.hqusers');
        $this->redirect($this->referer());
        die;
//        $merchantId = $this->Session->read('hq_id');
//        $merchantDomain = $this->Merchant->find('first', array('conditions' => array('Merchant.id' => $merchantId, 'Merchant.is_active' => 1, 'Merchant.is_deleted' => 0), 'fields' => array('domain_name')));
//        if (!empty($merchantDomain)) {
//            $this->redirect("/" . $merchantDomain['Merchant']['domain_name']);
//        } else {
//            $this->redirect($this->referer());
//        }
    }

    /* ------------------------------------------------
      Function name:registration()
      Description:Registration  Form for Merchant
      created:27/7/2016
      ----------------------------------------------------- */

    public function registration() {
        $this->layout = "merchant_front";
        $this->Session->delete('Auth.hqusers');
        $this->loadModel("CountryCode");
        $countryCode = $this->CountryCode->fetchAllCountryCode();
        $this->set(compact('countryCode'));
        if ($this->request->is('post')) {
            $this->User->set($this->request->data);
            if ($this->User->validates()) {

                $merchantId = $this->Session->read('hq_id');
                $email = strtolower(trim($this->request->data['User']['email'])); //Here username is email
                $this->request->data['User']['merchant_id'] = $merchantId; // Merchant Id
                $this->request->data['User']['role_id'] = 5; // Role Id of the user
                $userName = trim($this->request->data['User']['email']); //Here username is email
                $this->request->data['User']['username'] = trim($userName);
                $current_time = date("Y-m-d H:i:s");
                $this->request->data['User']['dateOfjoin'] = $current_time;
                //$this->request->data['User']['dateOfBirth'] = $this->request->data['User']['month'] . "-" . $this->request->data['User']['day'] . "-" . $this->request->data['User']['year'];
                $actualDbDate = $this->Dateform->formatDate($this->request->data['User']['dateOfBirth']); // calling formatDate function in Appcontroller to format the date (Y-m-d) format
                $this->request->data['User']['dateOfBirth'] = $actualDbDate;
                $token = time() . rand();
                $this->request->data['User']['is_active'] = 0;
                $this->request->data['User']['activation_token'] = $token;
                $this->request->data['User']['is_privacypolicy'] = 1;
                $userName = trim($this->request->data['User']['email']); //Here username is email
                $this->request->data['User']['city'] = trim($this->request->data['User']['city_id']);
                $this->request->data['User']['state'] = trim($this->request->data['User']['state_id']);
                $this->request->data['User']['zip'] = trim($this->request->data['User']['zip_id']);
                $this->request->data['User']['state_id'] = 0;
                $this->request->data['User']['city_id'] = 0;
                $this->request->data['User']['zip_id'] = 0;
                
                // Activate User
                $this->request->data['User']['is_active'] = 1;

                $result = $this->User->saveUserInfo($this->request->data);   // We are calling function written on Model to save data
                $this->loadModel('Store');
                $merchantId = $this->Session->read('hq_id');
                $storeEmail = $this->Merchant->getMerchantDetail($merchantId);
                if ($result == 1 && !empty($merchantId)) {
                    $this->Session->setFlash(__('Registration successfully done'), 'flash_success');
                    if ($this->Auth->login()) {
                        $this->Session->write('login_date_time', date('Y-m-d H:i:s'));            
                        $this->redirect(array('controller' => 'hqusers', 'action' => 'myProfile'));
                    } else {
                        $this->logout();
                    }
                } else {
                    $this->Session->setFlash(__('Some problem has been occured in your registration process, please try again later'), 'flash_error');
                }
            } else {
                $errors = $this->User->validationErrors;
            }
        }
        $states = $this->states();
        $this->set(compact('googleSiteKey', 'states'));
    }

    /* ------------------------------------------------
      Function name:checkMerchantEmail()
      Description:check merchant exist or not
      created:27/7/2016
      ----------------------------------------------------- */

    public function checkMerchantEmail($roleId = null) {
        $this->autoRender = false;
        if ($_GET) {
            $emailEntered = $_GET['data']['User']['email'];
            if ($roleId == 5) {
                $merchantId = $this->Session->read('hq_id');
                $emailStatus = $this->User->merchantemailExists($emailEntered, $roleId, $merchantId);
            }
            echo json_encode($emailStatus);
        }
    }

    /* ------------------------------------------------
      Function name:checkHqEndUserEmail()
      Description:check merchant exist or not
      created:27/7/2016
      ----------------------------------------------------- */

    public function checkHqEndUserEmail() {
        $this->autoRender = false;
        if ($_GET) {
            $emailEntered = $_GET['data']['User']['email'];
            $roleId = array('4', '5');
            $merchantId = $this->Session->read('hq_id');
            $emailStatus = $this->User->merchantemailExists($emailEntered, $roleId, $merchantId);

            echo json_encode($emailStatus);
        }
    }

    /* ------------------------------------------------
      Function name:accountActivation()
      Description:Activating user account
      created:27/7/2016
      ----------------------------------------------------- */

    public function accountActivation($token = null) {
        $this->layout = false;
        $this->autoRender = false;
        $user = $this->User->find('first', array('conditions' => array('User.activation_token' => $token)));
        if (!empty($user)) {
            $this->User->updateAll(
                    array('User.is_active' => 1, 'User.activation_token' => 1), array('User.id' => $user['User']['id'])
            );
            $this->Session->setFlash(__('Your account has been activated successfully, you can login now'), 'flash_success');
            $this->logout();
        } else {
            $this->Session->setFlash(__('This link has been used before'), 'flash_error');
            $this->logout();
        }
    }

    /* ------------------------------------------------
      Function name:forgetPassword()
      Description:For forget password
      created:27/7/2016
      ----------------------------------------------------- */

    public function forgetPassword() {
        $this->layout = "merchant_front";
        $this->Session->delete('Auth.hqusers');
        if ($this->request->is(array('post', 'put')) && !empty($this->data)) {
            $email = $this->request->data['User']['email'];
            $roleId = array(4, 5);
            $merchantId = $this->Session->read('hq_id');
            if (!$merchantId) {
                $merchantId = "";
            }
            $userEmail = $this->User->checkForgetEmail($roleId, null, $merchantId, $email); //Calling function on model for checking the email
            if (!empty($userEmail)) {
                $this->loadModel('EmailTemplate');
                $template_type = "merchant_customer_forget_password";
                $storeEmail = $this->Merchant->getMerchantDetail($merchantId);
                $emailTemplate = $this->EmailTemplate->storeTemplates(null, $merchantId, $template_type);
                if ($emailTemplate) {
                    if ($userEmail['User']['lname']) {
                        $fullName = $userEmail['User']['fname'] . " " . $userEmail['User']['lname'];
                    } else {
                        $fullName = $this->request->data['User']['fname'];
                    }
                    $token = time() . rand();
                    $emailData = $emailTemplate['EmailTemplate']['template_message'];
                    $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                    $url = HTTP_ROOT . 'hqusers/resetPassword/' . $token;
                    $activationLink = '<a href="' . $url . '">' . $url . '</a>';
                    $emailData = str_replace('{ACTIVE_LINK}', $activationLink, $emailData);
                    $subject = ucwords(str_replace('_', ' ', $emailTemplate['EmailTemplate']['template_subject']));
                    $url = "http://" . $storeEmail['Merchant']['domain_name'];
                    $merchantUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Merchant']['domain_name'] . "</a>";
                    $emailData = str_replace('{MERCHANT_URL}', $merchantUrl, $emailData);
                    $emailData = str_replace('{MERCHANT_COMPANY_NAME}', $storeEmail['Merchant']['name'], $emailData);
                    $storeAddress = $storeEmail['Merchant']['address'] . "<br>" . $storeEmail['Merchant']['city'] . ", " . $storeEmail['Merchant']['state'] . " " . $storeEmail['Merchant']['zipcode'];
                    $storePhone = $storeEmail['Merchant']['phone'];
                    $emailData = str_replace('{MERCHANT_ADDRESS}', $storeAddress, $emailData);
                    $emailData = str_replace('{MERCHANT_PHONE}', $storePhone, $emailData);
                    $this->Email->to = $email;
                    $this->Email->subject = $subject;
                    $this->Email->from = $storeEmail['Merchant']['email'];
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
                            $this->logout();
                        }
                    } catch (Exception $e) {
                        $this->Session->setFlash("Please try after some time", 'alert_failed');
                        $this->logout();
                    }
                }
            } else {
                $this->Session->setFlash(__('Email is not registered or Active'), 'flash_error');
            }
        }
    }

    /* ------------------------------------------------
      Function name:resetPassword()
      Description:reset user password
      created:29/09/2015
      ----------------------------------------------------- */

    public function resetPassword($token = null) {
        $this->layout = "merchant_front";
        if ($this->request->is(array('post', 'put'))) {
            $records = $this->User->find('first', array('fields' => array('User.forgot_token', 'User.merchant_id'), 'conditions' => array('User.id' => $this->data['User']['id'])));
            if (empty($records['User']['forgot_token'])) {
                $this->Session->setFlash("Token has been expired.Please request another one", 'flash_error');
                $this->redirect($this->referer());
            } else {
                $this->request->data['User']['forgot_token'] = "";
            }
            $this->request->data['User']['password'] = $this->data['User']['newpassword'];
            if ($this->User->save($this->data)) {
                $this->Session->setFlash(__('Password has been reset successfully, you can login now'), 'flash_success');
                $this->redirect(array('controller' => 'hqusers', 'action' => 'registration'));
            } else {
                $this->Session->setFlash('Unable to save password', 'flash_error');
            }
        } else if (!empty($token)) {
            $record = $this->User->find('first', array('conditions' => array('User.forgot_token' => $token)));
            if (!empty($record)) {
                $this->set('userData', $record);
            } else {
                $this->Session->setFlash("Token has been expired.Please request another one", 'flash_error');
                $this->redirect(array('controller' => 'hqusers', 'action' => 'forgetPassword'));
            }
        } else {
            $this->Session->setFlash("Cannot access this page directly", 'flash_error');
            $this->logout();
        }
    }

    /* ------------------------------------------------
      Function name:location()
      Description: Used for show merchant stores
      created:28/7/2016
      ----------------------------------------------------- */

    public function location($cityName = null) {
        $this->layout = 'merchant_front';
        $search = $condition = '';
        $merchantId = $this->Session->read('hq_id');
        if (empty($merchantId)) {
            $merchantId = 0;
        }
        if (!empty($this->request->data['Store']['keyword'])) {
            $search = $this->request->data['Store']['keyword'];
            $addresskey = str_replace(' ', '+', $search);
            $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $addresskey . '&sensor=false');
            $output = json_decode($geocode);
            if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                
            } else {
                $latitude = @$output->results[0]->geometry->location->lat;
                $longitude = @$output->results[0]->geometry->location->lng;
            }
        }
        if (!empty($cityName)) {
            $condition = 'Store.city=' . "'$cityName'";
        }
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->Store->bindModel(
                array(
            'hasOne' => array(
                'StoreSetting' => array(
                    'className' => 'StoreSetting',
                    'foreignKey' => 'store_id'
                )
            )
                ), false
        );
        if (!empty($latitude) && !empty($longitude)) {
            if (empty($this->request->data['Store']['miles'])) {
                $distance = 10;
            } else {
                $distance = $this->request->data['Store']['miles'];
            }
            $this->Store->virtualFields['distance'] = '( 3959 * acos( cos( radians(' . $latitude . ') ) * cos( radians( latitude ) ) * cos( radians( logitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin(radians(latitude))))';
            $store = $this->Store->find('all', array('conditions' => array('Store.is_active' => 1, 'Store.is_deleted' => 0, 'Store.merchant_id' => $merchantId), 'fields' => array('distance', 'id', 'merchant_id', 'store_name', 'store_url', 'phone', 'address', 'city', 'state', 'latitude', 'logitude', 'zipcode', 'StoreSetting.merchant_online_order_btn'), 'group' => array('Store__distance HAVING Store__distance < ' . $distance)));
        } else {
            $store = $this->Store->find('all', array('fields' => array('id', 'merchant_id', 'store_name', 'store_url', 'phone', 'address', 'city', 'state', 'latitude', 'logitude', 'zipcode', 'StoreSetting.merchant_online_order_btn'), 'conditions' => array($condition, 'Store.merchant_id' => $merchantId, 'Store.is_deleted' => 0, 'Store.is_active' => 1, 'OR' => array('Store.address LIKE' => "%$search%", 'Store.zipcode LIKE' => "%$search%", 'Store.city LIKE' => "%$search%", 'Store.state LIKE' => "%$search%"))));
        }
        $this->set(compact('store'));
    }

    /* ------------------------------------------------
      Function name:gallery()
      Description: Used for merchant gallery images
      created:28/7/2016
      ----------------------------------------------------- */

    public function gallery() {
        $this->layout = 'merchant_front';
        $merchantId = $this->Session->read('hq_id');
        if (!empty($merchantId)) {
            $this->set('title', 'Merchant Review Images Gallery');
            $this->loadModel('MerchantImage');
            $this->paginate = array('order' => array('MerchantImage.created' => 'DESC'), 'conditions' => array('MerchantImage.merchant_id' => $merchantId, 'MerchantImage.is_active' => 1, 'MerchantImage.is_deleted' => 0), 'fields' => array('MerchantImage.image'));
            $allReviewImages = $this->paginate('MerchantImage');
            $this->set('allReviewImages', $allReviewImages);
        } else {
            $this->merchant();
        }
    }

    /* ------------------------------------------------
      Function name:staticContent()
      Description: Used for static content in merchant front end
      created:28/7/2016
      ----------------------------------------------------- */

    public function staticContent($encrypted_contentId = null) {
        $this->layout = 'merchant_front';
        $merchantId = $this->Session->read('hq_id');
        if (!empty($encrypted_contentId) && !empty($merchantId)) {
            $contentId = $this->Encryption->decode($encrypted_contentId);
            $content = $this->MerchantContent->getPageDetail($contentId, $merchantId);
            $this->loadModel('HomeContent');
//            $this->HomeContent->bindModel(
//                    array(
//                'belongsTo' => array(
//                    'LayoutBox' => array(
//                        'className' => 'LayoutBox',
//                        'foreignKey' => 'layout_box_id',
//                        'conditions' => array('LayoutBox.is_deleted' => 0, 'LayoutBox.is_active' => 1),
//                    )
//                )
//                    ), false
//            );
            $homeContentData1 = $this->HomeContent->find('all', array('fields' => array('HomeContent.id', 'HomeContent.content_layout_id', 'HomeContent.master_content_id'), 'conditions' => array('HomeContent.merchant_id' => $merchantId, 'HomeContent.merchant_content_id' => $contentId, 'HomeContent.is_deleted' => 0, 'HomeContent.is_active' => 1), 'group' => array('HomeContent.master_content_id'), 'order' => array('HomeContent.master_content_id')));
            if (!empty($homeContentData1)) {
                foreach ($homeContentData1 as $key => $layoutData) {
                    $this->HomeContent->bindModel(
                            array(
                        'belongsTo' => array(
                            'LayoutBox' => array(
                                'className' => 'LayoutBox',
                                'foreignKey' => 'layout_box_id',
                                'conditions' => array('LayoutBox.is_deleted' => 0, 'LayoutBox.is_active' => 1),
                            )
                        )
                            ), false
                    );
                    $data = $this->HomeContent->find('all', array('conditions' => array('HomeContent.master_content_id' => $layoutData['HomeContent']['master_content_id'], 'HomeContent.is_deleted' => 0, 'HomeContent.is_active' => 1), 'order' => array('HomeContent.layout_box_id')));
                    $homeContentData[] = $data;
                }
            }
            $this->set(compact('homeContentData', 'content'));
        } else {
            $this->merchant();
        }
    }

    public function contact_us() {
        $this->layout = false;
        $this->autoRender = false;
        $merchantId = $this->Session->read('hq_id');
        if ($this->request->is(array('ajax', 'post', 'put')) && !empty($merchantId)) {
            parse_str($this->request->data['formData'], $data);
            $this->loadModel('ContactUs');
            $data['data']['ContactUs']['merchant_id'] = $merchantId;
            $this->request->data = $data['data'];
            if ($this->ContactUs->save($this->request->data, false)) {
                $this->loadModel('EmailTemplate');
                $template_type = "merchant_enquiry";
                $reply_template_type = "enquiry_merchant_reply";
                $storeEmail = $this->Merchant->getMerchantDetail($merchantId);
                $merchant_Address = $storeEmail['Merchant']['address'] . ' ' . $storeEmail['Merchant']['city'] . ' ' . $storeEmail['Merchant']['state'];
                $emailTemplate = $this->EmailTemplate->storeTemplates(null, $merchantId, $template_type);
                $replyEmailTemplate = $this->EmailTemplate->storeTemplates(null, $merchantId, $reply_template_type);
                if (!empty($emailTemplate)) {
                    $emailData = $emailTemplate['EmailTemplate']['template_message'];
                    $emailData = str_replace('{FULL_NAME}', $storeEmail['Merchant']['name'], $emailData);
                    $emailData = str_replace('{CUSTOMER_NAME}', $this->request->data['ContactUs']['name'], $emailData);
                    $emailData = str_replace('{CUSTOMER_EMAIL}', $this->request->data['ContactUs']['email'], $emailData);
                    $emailData = str_replace('{CUSTOMER_NUMBER}', $this->request->data['ContactUs']['phone'], $emailData);
                    $emailData = str_replace('{MESSAGE}', $this->request->data['ContactUs']['message'], $emailData);
                    $url = "http://" . $storeEmail['Merchant']['domain_name'];
                    $merchantUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Merchant']['domain_name'] . "</a>";
                    $emailData = str_replace('{MERCHANT_URL}', $merchantUrl, $emailData);
                    $emailData = str_replace('{MERCHANT_COMPANY_NAME}', $storeEmail['Merchant']['name'], $emailData);
                    $emailData = str_replace('{MERCHANT_ADDRESS}', $merchant_Address, $emailData);
                    $emailData = str_replace('{MERCHANT_PHONE}', $storeEmail['Merchant']['phone'], $emailData);
                    $this->Email->to = $storeEmail['Merchant']['email'];
                    $this->Email->subject = $this->request->data['ContactUs']['subject'];
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
                        if ($this->Email->send() && !empty($replyEmailTemplate)) {
                            $repEmailData = $replyEmailTemplate['EmailTemplate']['template_message'];
                            $repEmailData = str_replace('{FULL_NAME}', $this->request->data['ContactUs']['name'], $repEmailData);
                            $repEmailData = str_replace('{EMAIL}', $this->request->data['ContactUs']['email'], $repEmailData);
                            $repEmailData = str_replace('{MESSAGE}', $this->request->data['ContactUs']['message'], $repEmailData);
                            $url = "http://" . $storeEmail['Merchant']['domain_name'];
                            $merchantUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Merchant']['domain_name'] . "</a>";
                            $emailData = str_replace('{MERCHANT_URL}', $merchantUrl, $emailData);
                            $repEmailData = str_replace('{MERCHANT_COMPANY_NAME}', $storeEmail['Merchant']['name'], $repEmailData);
                            $repEmailData = str_replace('{MERCHANT_ADDRESS}', $merchant_Address, $repEmailData);
                            $repEmailData = str_replace('{MERCHANT_PHONE}', $storeEmail['Merchant']['phone'], $repEmailData);
                            $this->Email->to = $this->request->data['ContactUs']['email'];
                            $this->Email->subject = $this->request->data['ContactUs']['subject'];
                            $this->Email->from = $this->front_email;
                            $this->set('data', $repEmailData);
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
                                    $response['status'] = 'Success';
                                    $response['msg'] = 'Message send successfully.';
                                }
                            } catch (Exception $e) {
                                $response['status'] = 'Error';
                                $response['msg'] = 'Something went wrong!';
                            }
                        }
                    } catch (Exception $e) {
                        $response['status'] = 'Error';
                        $response['msg'] = 'Something went wrong!';
                    }
                }
            } else {
                $response['status'] = 'Error';
                $response['msg'] = 'Email is not registered in our system, please check again.';
            }
        } else {
            $response['status'] = 'Error';
            $response['msg'] = 'Something went wrong!';
        }
        return json_encode($response);
    }

    public function newsletter($month = null, $year = null) {
        $this->layout = "merchant_front";
        $merchantId = $this->Session->read('hq_id');
        if (empty($merchantId)) {
            $this->merchant();
        }
        $this->loadModel('Newsletter');
        $condition = 'is_active=1 AND is_deleted=0 AND merchant_id=' . $merchantId;
        if (!empty($_GET['val'])) {
            $this->Session->delete('HqFrontNewsletterSearchData');
            $newsLetterId = $this->Encryption->decode($_GET['val']);
            $condition .= " AND Newsletter.id=$newsLetterId";
        }
        if (@$this->params['pass'][0] == 'clear') {
            $this->request->data = '';
            $this->Session->delete('HqFrontNewsletterSearchData');
        }
        if ($this->Session->read('HqFrontNewsletterSearchData.Search') && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqFrontNewsletterSearchData.Search'), true);
        } else {
            $this->Session->delete('HqFrontNewsletterSearchData.Search');
        }
        if (!empty($this->request->data['Newsletter']) && empty($newsLetterId)) {
            $this->Session->write('HqFrontNewsletterSearchData.Search', json_encode($this->request->data));
            $this->Newsletter->set($this->request->data);
            if ($this->request->data['Newsletter']['content_key']) {
                $key = $this->request->data['Newsletter']['content_key'];
                $condition .= " AND name LIKE '%$key%'";
            }
            if ($this->request->data['Newsletter']['start_date'] && $this->request->data['Newsletter']['end_date']) {
                $startDate = $this->request->data['Newsletter']['start_date'];
                $endDate = $this->request->data['Newsletter']['end_date'];
                $condition .= " AND (DATE(created) BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
            }
        }
        $data = array();
        if ($this->Session->read('HqFrontNewsletterSearchData.Archive')) {
            $data = json_decode($this->Session->read('HqFrontNewsletterSearchData.Archive'), true);
        }
        if (!empty($month) && !empty($year)) {
            $this->Session->delete('HqFrontNewsletterSearchData.Search');
            $data['Newsletter']['month'] = $month;
            $data['Newsletter']['year'] = $year;
            $this->Session->write('HqFrontNewsletterSearchData.Archive', json_encode($data));
        }
        if (!empty($data['Newsletter']['month']) && !empty($data['Newsletter']['year'])) {
            $condition .= " AND MONTH(created)='" . $data['Newsletter']['month'] . "' AND YEAR(created)='" . $data['Newsletter']['year'] . "'";
        }
        $this->set('archiveSelect', $data);
        //Recent Post
        $mnLatestData = $this->Newsletter->find('all', array('conditions' => array('is_active' => 1, 'is_deleted' => 0, 'Newsletter.merchant_id' => $merchantId, 'OR' => array('type' => array(2, 3), 'show_to_hq_front' => 1)), 'fields' => array('name', 'id', 'created'), 'order' => array('created' => 'DESC'), 'limit' => 3));
        $this->set('merchantNewsletterRecentPost', $mnLatestData);
        //Archive
        $mnData = $this->Newsletter->find('all', array('order' => array('created' => 'DESC'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0, 'Newsletter.merchant_id' => $merchantId, 'OR' => array('type' => array(2, 3), 'show_to_hq_front' => 1)), 'fields' => array('count(*) as count', 'created', 'monthname(created) as monthname', 'month(created) as month', 'year(created) as year'), 'group' => array('year(created)', 'month(created)')));
        $this->set('merchantNewsletterArchive', $mnData);
        //List
        $this->paginate = array('limit' => 10, 'conditions' => array($condition, 'OR' => array('type' => array(2, 3), 'show_to_hq_front' => 1)), 'order' => array('created' => 'DESC'));
        $merchantNewsletterList = $this->paginate('Newsletter');
        $this->set('merchantNewsletterList', $merchantNewsletterList);
    }

    public function getMerchantNewsLetterContent() {
        $merchantId = $this->Session->read('hq_id');
        if ($this->request->is('ajax') && !empty($this->request->data['merchantNewsLetterId']) && $merchantId) {
            $this->loadModel('Newsletter');
            $merchantNewsLetterId = $this->Encryption->decode($this->request->data['merchantNewsLetterId']);
            $content = $this->Newsletter->findById($merchantNewsLetterId, array('content', 'name'));
            $this->set('content', $content);
        }
    }

    public function storeLocation($storeId = null) {
        $this->layout = "merchant";
        $merchantId = $this->Session->read('hq_id');
        $storeId = $this->Encryption->decode($storeId);
        if (!empty($merchantId) && !empty($storeId)) {
            $this->Store->recursive = -1;
            $storeData = $this->Store->find('first', array('fields' => array('id', 'store_name', 'store_url', 'phone', 'address', 'city', 'state', 'latitude', 'logitude', 'zipcode'), 'conditions' => array('Store.id' => $storeId, 'Store.is_deleted' => 0, 'Store.is_active' => 1)));
        }
        $this->set('store_data', @$storeData);
    }

    /* ------------------------------------------------
      Function name:myProfile()
      Description:This section will manage the profile of the merchant
      created:13/08/2016
      ----------------------------------------------------- */

    public function myProfile() {
        $this->layout = "merchant_front";
        $merchantId = $this->Session->read('hq_id');
        $user_id = AuthComponent::User('id');
        if (empty($merchantId) && empty($user_id)) {
            $this->merchant();
        }
        $this->loadModel('CountryCode');
        $countryCode = $this->CountryCode->fetchAllCountryCode();
        $this->set(compact('countryCode'));
        $this->User->bindModel(array('belongsTo' => array('CountryCode')), false);
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
        $userId = AuthComponent::User('id');
        $userResult = $this->User->find('first', array('conditions' => array('User.id' => $userId), 'recursive' => 2));
        $this->User->set($this->request->data);
        if (isset($this->request->data['User']['changepassword'])) {
            $this->request->data['User']['changepassword'] = 1;
        } else {
            $this->request->data['User']['changepassword'] = 0;
            $this->User->validator()->remove('password');
            $this->User->validator()->remove('password_match');
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $dbformatDate = $this->Dateform->formatDate($this->data['User']['dateOfBirth']);
            $this->request->data['User']['dateOfBirth'] = $dbformatDate;
            if ($this->request->data['User']['changepassword'] == 1) {
                $oldPassword = AuthComponent::password($this->data['User']['oldpassword']);
                if ($oldPassword != $userResult['User']['password']) {
                    $this->Session->setFlash(__("Please enter correct old password"), 'flash_error');
                    $this->redirect(array('controller' => 'hqusers', 'action' => 'myProfile'));
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

            if ($this->User->saveUserInfo($data)) {
                $this->Session->setFlash(__('Profile has been updated successfully.'), 'flash_success');
                $this->redirect(array('controller' => 'hqusers', 'action' => 'myProfile'));
            } else {
                $this->Session->setFlash(__('Profile could not be updated, please try again'), 'flash_error');
            }
        }
        $roleId = $userResult['User']['role_id'];
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

//        $states = $this->states();
//        $this->set('states', $states);
//        if (!empty($this->request->data['User']['state_id'])) {
//            $this->loadModel('City');
//            $city = $this->City->find('list', array('conditions' => array('state_id' => $this->request->data['User']['state_id'])));
//            $this->set('cities', $city);
//        }
//        if (!empty($this->request->data['User']['state_id']) && !empty($this->request->data['User']['city_id'])) {
//            $this->loadModel('Zip');
//            $zip = $this->Zip->find('list', array('fields' => array('id', 'zipcode'), 'conditions' => array('state_id' => $this->request->data['User']['state_id'], 'city_id' => $this->request->data['User']['city_id'])));
//            $this->set('zips', $zip);
//        }
    }

    /* ------------------------------------------------
      Function name:myOrders()
      Description:List Orders and Favourite Orders
      created:13/09/2016
      ----------------------------------------------------- */

    public function myOrders() {
        $this->layout = "merchant_front";
        $merchantId = $this->Session->read('hq_id');
        $user_id = AuthComponent::User('id');
        if (empty($merchantId) && empty($user_id)) {
            $this->merchant();
        }
        $this->loadModel('OrderPreference');
        $this->loadModel('OrderTopping');
        $this->loadModel('OrderOffer');
        $this->loadModel('OrderItem');
        $this->loadModel('Order');
        $this->loadModel('Favorite');
        $this->loadModel('Store');
        $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);
        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('fields' => array('name')))), false);
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')))), false);
        $this->OrderItem->bindModel(array('hasOne' => array('StoreReview' => array('conditions' => array('StoreReview.is_active' => 1, 'StoreReview.is_deleted' => 0), 'fields' => array('review_rating', 'is_approved'))), 'hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity')), 'OrderTopping' => array('fields' => array('id', 'topping_id')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('id', 'name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('id', 'size')))), false);
        $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id', 'interval_id'))), 'belongsTo' => array('DeliveryAddress' => array('fields' => array('name_on_bell', 'city', 'address')), 'OrderStatus' => array('fields' => array('name')))), false);
        $this->Favorite->bindModel(array('belongsTo' => array('Order' => array('fields' => array('id', 'user_id', 'order_number', 'amount', 'seqment_id', 'delivery_address_id')))), false);
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->Order->bindModel(array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('id', 'store_name'),
                    'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1)
                ))
                ), false);

        $value = "";
        if (isset($this->params->pass[0]) && !empty($this->params->pass[0]) && $this->params->pass[0] == 'clear') {
            $this->Session->delete('HqMyOrderSearchData');
        }
        if ($this->Session->read('HqMyOrderSearchData') && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqMyOrderSearchData'), true);
        } else {
            $this->Session->delete('HqMyOrderSearchData');
        }
        $conditions = array('Order.merchant_id' => $merchantId, 'Order.user_id' => $user_id, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0);
        if (!empty($this->request->data)) {
            $this->Session->write('HqMyOrderSearchData', json_encode($this->request->data));
            $conditions1 = array();
            $conditions2 = array();
            if (!empty($this->request->data['User']['keyword'])) {
                $value = trim($this->request->data['User']['keyword']);
                $conditions1 = array("OR" => array("Order.order_number LIKE '%" . $value . "%'", "DeliveryAddress.name_on_bell LIKE '%" . $value . "%'", "DeliveryAddress.address LIKE '%" . $value . "%'", "DeliveryAddress.city LIKE '%" . $value . "%'"));
                $this->set("keyword", $value);
            }
            if (!empty($this->request->data['Merchant']['store_id'])) {
                $storeId = trim($this->request->data['Merchant']['store_id']);
                $conditions2 = array('Order.store_id' => $storeId);
            }
            $conditions = array_merge($conditions1, $conditions2, $conditions);
        }
        $this->paginate = array(
            'conditions' => $conditions,
            'order' => 'Order.created DESC',
            'recursive' => 3,
            'limit' => 9
        );
        $myOrders = $this->paginate('Order');
        $myFav = $this->Favorite->getFavoriteDetails($merchantId, null, $user_id);
        $compare = array();
        foreach ($myFav as $fav) {
            $compare[] = $fav['Favorite']['order_id'];
        }
        $this->set(compact('myOrders', 'compare'));
    }

    /* ------------------------------------------------
      Function name:myFavorites()
      Description:List Orders and Favourite Orders
      created:13/09/2016
      ----------------------------------------------------- */

    public function myFavorites() {
        $this->layout = "merchant_front";
        $merchantId = $this->Session->read('hq_id');
        $user_id = AuthComponent::User('id');
        if (empty($merchantId) && empty($user_id)) {
            $this->merchant();
        }
        $this->loadModel('OrderPreference');
        $this->loadModel('OrderTopping');
        $this->loadModel('OrderOffer');
        $this->loadModel('OrderItem');
        $this->loadModel('Order');
        $this->loadModel('Favorite');
        $this->loadModel('Store');
        $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('id', 'name')))), false);
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('id', 'name')))), false);
        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('fields' => array('id', 'name')))), false);
        $this->OrderItem->bindModel(array('hasOne' => array('StoreReview' => array('fields' => array('review_rating', 'is_approved'))), 'hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity')), 'OrderTopping' => array('fields' => array('id', 'topping_id')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('id', 'name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('id', 'size')))), false);
        $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id', 'interval_id'))), 'belongsTo' => array('DeliveryAddress' => array('fields' => array('id', 'name_on_bell', 'city', 'address')), 'OrderStatus' => array('fields' => array('id', 'name')))), false);
        $this->Favorite->bindModel(array('belongsTo' => array('Order' => array('fields' => array('id', 'user_id', 'order_number', 'amount', 'seqment_id', 'delivery_address_id', 'order_status_id', 'coupon_discount', 'created', 'pickup_time', 'order_comments', 'store_id')))), false);
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->Favorite->bindModel(array(
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
            $this->Session->delete('HqMyfavorederSearchData');
        }

        if ($this->Session->read('HqMyfavorederSearchData') && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqMyfavorederSearchData'), true);
            $value = $this->request->data['User']['keyword'];
        } else {
            $this->Session->delete('HqMyfavorederSearchData');
        }
        $conditions = array('Favorite.merchant_id' => $merchantId, 'Favorite.user_id' => $user_id, 'Favorite.is_active' => 1, 'Favorite.is_deleted' => 0);

        if ($this->Session->read('HqMyfavorederSearchData') && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqMyfavorederSearchData'), true);
        } else {
            $this->Session->delete('HqMyfavorederSearchData');
        }

        if (!empty($this->request->data)) {
            $this->Session->write('MyfavorederSearchData', json_encode($this->request->data));
            $conditions1 = array();
            $conditions2 = array();
            if (!empty($this->request->data['User']['keyword'])) {
                $value = trim($this->request->data['User']['keyword']);
                $conditions1 = array("OR" => array("Order.order_number LIKE '%" . $value . "%'"));
            }
            if (!empty($this->request->data['Merchant']['store_id'])) {
                $storeId = trim($this->request->data['Merchant']['store_id']);
                $conditions2 = array('Favorite.store_id' => $storeId);
            }
            $conditions = array_merge($conditions1, $conditions2, $conditions);
        }
        $this->paginate = array(
            'conditions' => $conditions,
            'order' => 'Favorite.created DESC',
            'recursive' => 4,
            'limit' => 9
        );
        $myFav = $this->paginate('Favorite');
        $this->set(compact('myFav'));
        $this->set("keyword", $value);
    }

    /* ------------------------------------------------
      Function name:mySavedOrders()
      Description:List Orders and Favourite Orders
      created:13/09/2016
      ----------------------------------------------------- */

    public function mySavedOrders() {
        $this->layout = "merchant_front";
        $merchantId = $this->Session->read('hq_id');
        $user_id = AuthComponent::User('id');
        if (empty($merchantId) && empty($user_id)) {
            $this->merchant();
        }
        $this->loadModel('OrderPreference');
        $this->loadModel('OrderTopping');
        $this->loadModel('OrderOffer');
        $this->loadModel('OrderItem');
        $this->loadModel('Order');
        $this->loadModel('Favorite');
        $this->loadModel('Store');
        $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);
        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('fields' => array('name')))), false);
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')))), false);
        $this->OrderItem->bindModel(array('hasOne' => array('StoreReview' => array('fields' => array('review_rating', 'is_approved'))), 'hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity')), 'OrderTopping' => array('fields' => array('id', 'topping_id')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')))), false);
        $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id', 'interval_id'))), 'belongsTo' => array('DeliveryAddress' => array('fields' => array('name_on_bell', 'city', 'address')), 'OrderStatus' => array('fields' => array('name')))), false);
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->Order->bindModel(array(
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

        if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
            if ($this->params->pass[0] == 'clear') {
                $this->Session->delete('HqMySavedOrderSearchData');
            }
        }

        if ($this->Session->read('HqMySavedOrderSearchData') && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqMySavedOrderSearchData'), true);
            $value = @$this->request->data['User']['keyword'];
        } else {
            $this->Session->delete('HqMySavedOrderSearchData');
        }

        if (!empty($this->request->data)) {
            $this->Session->write('HqMySavedOrderSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['User']['keyword'])) {
                $value = trim($this->request->data['User']['keyword']);
                $conditions1 = array("OR" => array("Order.order_number LIKE '%" . $value . "%'", "DeliveryAddress.name_on_bell LIKE '%" . $value . "%'", "DeliveryAddress.address LIKE '%" . $value . "%'", "DeliveryAddress.city LIKE '%" . $value . "%'"));
                $this->set("keyword", $value);
            } else {
                $conditions1 = array();
            }
            if (!empty($this->request->data['Merchant']['store_id'])) {
                $conditions = array('Order.merchant_id' => $merchantId, 'Order.store_id' => $this->request->data['Merchant']['store_id'], 'Order.user_id' => $user_id, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 1);
                $conditions = array_merge($conditions1, $conditions);
                $this->paginate = array('conditions' => $conditions, 'order' => 'Order.created DESC', 'recursive' => 3, 'limit' => 9);
            } else {
                $conditions = array('Order.merchant_id' => $merchantId, 'Order.user_id' => $user_id, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 1);
                $conditions = array_merge($conditions1, $conditions);
                $this->paginate = array('conditions' => $conditions, 'order' => 'Order.created DESC', 'recursive' => 3, 'limit' => 9);
            }
        } else {
            $this->paginate = array('conditions' => array('Order.merchant_id' => $merchantId, 'Order.user_id' => $user_id, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 1),
                'order' => 'Order.created DESC',
                'recursive' => 3,
                'limit' => 9
            );
        }
        $myOrders = $this->paginate('Order');
        $this->set(compact('myOrders', 'compare'));
    }

    /* ------------------------------------------------
      Function name:deleteSaveOrder()
      Description:Delete Saved orders form saved list
      created:13/09/2016
      ----------------------------------------------------- */

    public function deleteSaveOrder($encrypted_orderId = null) {
        $this->autoRender = false;
        $futureOrderId = $this->Encryption->decode($encrypted_orderId);
        $merchantId = $this->Session->read('hq_id');
        $user_id = AuthComponent::User('id');
        if (empty($merchantId) && empty($user_id) && empty($futureOrderId)) {
            $this->merchant();
        }
        $this->loadModel('OrderOffer');
        $this->loadModel('OrderTopping');
        $this->loadModel('OrderItem');
        $this->loadModel('Order');
        if ($this->Order->delete($futureOrderId)) {
            $this->OrderOffer->deleteAll(array('OrderOffer.order_id' => $futureOrderId), false);
            $this->OrderItem->deleteAll(array('OrderItem.order_id' => $futureOrderId), false);
            $this->OrderTopping->deleteAll(array('OrderTopping.order_id' => $futureOrderId), false);
            $this->Session->setFlash(__('Saved Order has been deleted'), 'flash_success');
            $this->redirect(array('controller' => 'hqusers', 'action' => 'mySavedOrders')); //
        } else {
            $this->Session->setFlash(__('Saved Order could not be deleted, please try again'), 'flash_error');
            $this->redirect(array('controller' => 'hqusers', 'action' => 'mySavedOrders')); //
        }
    }

    /* ------------------------------------------------
      Function name:myCoupons()
      Description:List of User Coupons
      created:13/09/2016
      ----------------------------------------------------- */

    public function myCoupons() {
        $this->layout = "merchant_front";
        $merchantId = $this->Session->read('hq_id');
        $user_id = AuthComponent::User('id');
        if (empty($merchantId) && empty($user_id)) {
            $this->merchant();
        }
        $this->loadModel('UserCoupon');
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
        if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
            if ($this->params->pass[0] == 'clear') {
                $this->Session->delete('HqMycouponsSearchData');
            }
        }
        if ($this->Session->read('HqMycouponsSearchData') && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqMycouponsSearchData'), true);
            $value = @$this->request->data['User']['keyword'];
        } else {
            $this->Session->delete('HqMycouponsSearchData');
        }

        if (!empty($this->request->data)) {
            $this->Session->write('HqMycouponsSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['User']['keyword'])) {
                $value = trim($this->request->data['User']['keyword']);
                $conditions1 = array("UserCoupon.coupon_code LIKE '%" . $value . "%'");
            } else {
                $conditions1 = array();
            }
            if (!empty($this->request->data['Merchant']['store_id'])) {
                $conditions = array('UserCoupon.merchant_id' => $merchantId, 'UserCoupon.store_id' => $this->request->data['Merchant']['store_id'], 'UserCoupon.user_id' => $user_id, 'UserCoupon.is_active' => 1, 'UserCoupon.is_deleted' => 0, 'Coupon.is_active' => 1, 'Coupon.is_deleted' => 0);
                $conditions = array_merge($conditions1, $conditions);
                $this->paginate = array(
                    'conditions' => $conditions,
                    'limit' => 9
                );
            } else {
                $conditions = array('UserCoupon.merchant_id' => $merchantId, 'UserCoupon.user_id' => $user_id, 'UserCoupon.is_active' => 1, 'UserCoupon.is_deleted' => 0, 'Coupon.is_active' => 1, 'Coupon.is_deleted' => 0);
                $conditions = array_merge($conditions1, $conditions);
                $this->paginate = array('conditions' => $conditions, 'limit' => 9);
            }
        } else {
            $this->paginate = array(
                'conditions' => array('UserCoupon.merchant_id' => $merchantId, 'UserCoupon.user_id' => $user_id, 'UserCoupon.is_active' => 1, 'UserCoupon.is_deleted' => 0, 'Coupon.is_active' => 1, 'Coupon.is_deleted' => 0),
                'limit' => 9
            );
        }
        $myCoupons = $this->paginate('UserCoupon');
        $this->set(compact('myCoupons'));
        $this->set("keyword", $value);
    }

    /* ------------------------------------------------
      Function name:deleteUserCoupon()
      Description:Delete User Coupon
      created:13/09/2016
      ----------------------------------------------------- */

    public function deleteUserCoupon($encrypted_userCouponId = null) {
        $this->autoRender = false;
        $merchantId = $this->Session->read('hq_id');
        $user_id = AuthComponent::User('id');
        if (empty($merchantId) && empty($user_id) && empty($encrypted_userCouponId)) {
            $this->merchant();
        }
        $data['UserCoupon']['id'] = $this->Encryption->decode($encrypted_userCouponId);
        $data['UserCoupon']['is_deleted'] = 1;
        $this->loadModel('UserCoupon');
        if ($this->UserCoupon->saveUserCoupon($data)) {
            $this->Session->setFlash(__("Coupon has been deleted"), 'flash_success');
            $this->redirect(array('controller' => 'hqusers', 'action' => 'myCoupons'));
        } else {
            $this->Session->setFlash(__("Some problem has been occured"), 'flash_error');
            $this->redirect(array('controller' => 'hqusers', 'action' => 'myCoupons'));
        }
    }

    /* ------------------------------------------------
      Function name:myReviews()
      Description:List of User Reviews
      created:13/09/2016
      ----------------------------------------------------- */

    public function myReviews() {
        $this->layout = "merchant_front";
        $merchantId = $this->Session->read('hq_id');
        $user_id = AuthComponent::User('id');
        if (empty($merchantId) && empty($user_id)) {
            $this->merchant();
        }
        $this->loadModel('OrderItem');
        $this->loadModel('StoreReview');
        $this->loadModel('Store');
        $this->OrderItem->bindModel(array('belongsTo' => array('Item' => array('fields' => array('name')))), false);
        $this->StoreReview->bindModel(array('belongsTo' => array('OrderItem' => array('fields' => array('item_id')))), false);
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->StoreReview->bindModel(array(
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
        if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
            if ($this->params->pass[0] == 'clear') {
                $this->Session->delete('HqMyReviewSearchData');
            }
        }
        if ($this->Session->read('HqMyReviewSearchData') && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqMyReviewSearchData'), true);
            $value = @$this->request->data['User']['keyword'];
        } else {
            $this->Session->delete('HqMyReviewSearchData');
        }

        if (!empty($this->request->data)) {
            $this->Session->write('HqMyReviewSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['User']['keyword'])) {
                $value = trim($this->request->data['User']['keyword']);
                $conditions1 = array('OR' => array("StoreReview.review_comment LIKE '%" . $value . "%'", "StoreReview.review_rating LIKE '%" . $value . "%'"));
            } else {
                $conditions1 = array();
            }

            if (!empty($this->request->data['Merchant']['store_id'])) {
                $conditions = array('StoreReview.is_deleted' => 0, 'StoreReview.user_id' => $user_id, 'StoreReview.store_id' => $this->request->data['Merchant']['store_id'], 'StoreReview.merchant_id' => $merchantId);
                $conditions = array_merge($conditions1, $conditions);
                $this->paginate = array(
                    'conditions' => $conditions,
                    'order' => 'StoreReview.created DESC',
                    'recursive' => 2,
                    'limit' => 9
                );
            } else {
                $conditions = array('StoreReview.is_deleted' => 0, 'StoreReview.user_id' => $user_id, 'StoreReview.merchant_id' => $merchantId);
                $conditions = array_merge($conditions1, $conditions);
                $this->paginate = array('conditions' => $conditions, 'order' => 'StoreReview.created DESC', 'recursive' => 2, 'limit' => 9);
            }
        } else {
            $this->paginate = array(
                'conditions' => array('StoreReview.is_deleted' => 0, 'StoreReview.user_id' => $user_id, 'StoreReview.merchant_id' => $merchantId),
                'order' => 'StoreReview.created DESC',
                'recursive' => 2,
                'limit' => 9
            );
        }
        $myReviews = $this->paginate('StoreReview');
        $this->set(compact('myReviews'));
        $this->set("keyword", $value);
    }

    /* ------------------------------------------------
      Function name:deleteReview()
      Description:Delete User Review
      created:13/09/2016
      ----------------------------------------------------- */

    public function deleteReview($encrypted_reviewId = null) {
        $this->autoRender = false;
        $merchantId = $this->Session->read('hq_id');
        $user_id = AuthComponent::User('id');
        if (empty($merchantId) && empty($user_id) && empty($encrypted_reviewId)) {
            $this->merchant();
        }
        $data['StoreReview']['id'] = $this->Encryption->decode($encrypted_reviewId);
        $data['StoreReview']['is_deleted'] = 1;
        $this->loadModel('StoreReview');
        if ($this->StoreReview->saveReview($data)) {
            $this->StoreReviewImage->updateAll(array('is_deleted' => 1), array('store_review_id' => $data['StoreReview']['id']));
            $this->Session->setFlash(__('Review has been deleted'), 'flash_success');
            $this->redirect(array('controller' => 'hqusers', 'action' => 'myReviews')); //
        } else {
            $this->Session->setFlash(__('Review could not be deleted, please try again'), 'flash_error');
            $this->redirect(array('controller' => 'hqusers', 'action' => 'myReviews')); //
        }
    }

    /* ------------------------------------------------
      Function name: myFavorite()
      Description: Add/Remove favorite
      created:13/09/2015
      ----------------------------------------------------- */

    public function myFavorite($order_id = null, $fav_id = null) {
        $this->autoRender = false;
        $merchantId = $this->Session->read('hq_id');
        $user_id = AuthComponent::User('id');
        if (empty($merchantId) && empty($user_id) && empty($order_id)) {
            $this->merchant();
        }
        if (!empty($fav_id)) {
            $data['Favorite']['id'] = $this->Encryption->decode($fav_id);
        } else {
            $this->loadModel('Order');
            $storeData = $this->Order->findById($this->Encryption->decode($order_id), array('store_id'));
            if (!empty($storeData)) {
                $data['Favorite']['store_id'] = $storeData['Order']['store_id'];
            }
        }

        $data['Favorite']['user_id'] = $user_id;
        $data['Favorite']['merchant_id'] = $merchantId;
        $data['Favorite']['order_id'] = $this->Encryption->decode($order_id);
        $this->loadModel('Favorite');
        if ($this->Favorite->saveFavorite($data)) {
            $this->Session->setFlash(__("Your favorite list has been updated"), 'flash_success');
            $this->redirect(array('controller' => 'hqusers', 'action' => 'myFavorites'));
        } else {
            $this->Session->setFlash(__("Some problem has been occured"), 'flash_error');
            $this->redirect(array('controller' => 'hqusers', 'action' => 'myFavorites'));
        }
    }

    /* ------------------------------------------------
      Function name:myBookings()
      Description:List of User Bookings
      created:13/09/2016
      ----------------------------------------------------- */

    public function myBookings() {
        $this->layout = "merchant_front";
        $merchantId = $this->Session->read('hq_id');
        $user_id = AuthComponent::User('id');
        if (empty($merchantId) && empty($user_id)) {
            $this->merchant();
        }
        $this->loadModel('Booking');
        $this->Booking->bindModel(array('belongsTo' => array('BookingStatus' => array('className' => 'BookingStatus',
                    'fields' => array('id', 'name')))), false);
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->Booking->bindModel(array(
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
        $fromdate = "";
        $enddate = "";
        $encryptedlock_storeId = "";
        if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
            if ($this->params->pass[0] == 'clear') {
                $this->Session->delete('HqMyreservationSearchData');
            }
        }
        if ($this->Session->read('HqMyreservationSearchData') && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqMyreservationSearchData'), true);
        } else {
            $this->Session->delete('HqMyreservationSearchData');
        }

        if ($this->Session->read('HqMyreservationSearchData') && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqMyreservationSearchData'), true);
        } else {
            $this->Session->delete('HqMyreservationSearchData');
        }

        if (!empty($this->request->data)) {
            $conditions1 = array();
            $this->Session->write('HqMyreservationSearchData', json_encode($this->params->query));
            if (!empty($this->request->data['MyBooking']['from_date'])) {
                $fromdate = $this->Dateform->formatDate(trim($this->request->data['MyBooking']['from_date']));
                $conditions1['Date(Booking.reservation_date) >='] = $fromdate;
            }

            if (!empty($this->request->data['MyBooking']['to_date'])) {
                $enddate = $this->Dateform->formatDate(trim($this->request->data['MyBooking']['to_date']));
                $conditions1['Date(Booking.reservation_date) <='] = $enddate;
            }

            if (!empty($this->request->data['Merchant']['store_id'])) {
                $conditions = array('Booking.is_deleted' => 0, 'Booking.is_active' => 1, 'Booking.user_id' => $user_id, 'Booking.store_id' => $this->Encryption->decode($this->request->data['Merchant']['store_id']));
                $conditions = array_merge($conditions, $conditions1);
                $this->paginate = array(
                    'conditions' => $conditions,
                    'order' => 'Booking.created DESC',
                    'limit' => 10
                );
            } else {
                $conditions = array('Booking.is_deleted' => 0, 'Booking.is_active' => 1, 'Booking.user_id' => $user_id);
                $conditions = array_merge($conditions1, $conditions);
                $this->paginate = array(
                    'conditions' => $conditions,
                    'order' => 'Booking.created DESC',
                    'limit' => 10
                );
            }
        } else {
            $this->paginate = array(
                'conditions' => array('Booking.user_id' => $user_id, 'Booking.is_active' => 1, 'Booking.is_deleted' => 0),
                'order' => 'Booking.created DESC',
                'limit' => 10
            );
        }
        $myBookings = $this->paginate('Booking');
        $this->set(compact('myBookings'));
    }

    /* ------------------------------------------------
      Function name:cancelBooking()
      Description:Cancel User bookings
      created:14/09/2016
      ----------------------------------------------------- */

    public function cancelBooking($encrypted_storeId = null, $encrypted_bookingId = null) {
        $this->autoRender = false;
        $merchantId = $this->Session->read('hq_id');
        $user_id = AuthComponent::User('id');
        if (empty($merchantId) && empty($user_id)) {
            $this->merchant();
        }
        $data['Booking']['id'] = $this->Encryption->decode($encrypted_bookingId);
        $data['Booking']['is_deleted'] = 1;
        $data['Booking']['booking_status_id'] = 4;
        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = $merchantId;
        $this->loadModel('Booking');
        $save_result = $this->Booking->saveBookingDetails($data); // call on model to save data
        if ($save_result) {
            $store = $this->Store->fetchStoreDetail($decrypt_storeId, $decrypt_merchantId);
            $myBookings = $this->Booking->getBookingDetailsById($data['Booking']['id']);
            $template_type = "customer_dine_in_cancel_request";
            $this->loadModel('EmailTemplate');
            $fullName = "Admin";
            $number_person = $myBookings['Booking']['number_person']; //no of person
            $start_time = date('d M Y -  H:i a', strtotime($myBookings['Booking']['reservation_date']));
            $customer_name = AuthComponent::User('fname') . " " . AuthComponent::User('lname');
            $this->loadModel('EmailTemplate');
            $emailSuccess = $this->EmailTemplate->storeTemplates($decrypt_storeId, $decrypt_merchantId, $template_type);
            if ($emailSuccess) {
                if (($store['Store']['notification_type'] == 1 || $store['Store']['notification_type'] == 3) && (!empty($store['Store']['notification_email']))) {
                    $storeEmail = trim($store['Store']['notification_email']);
                } else {
                    $storeEmail = trim($store['Store']['email_id']);
                }
                $customerEmail = trim(AuthComponent::User('email'));
                $emailData = $emailSuccess['EmailTemplate']['template_message'];
                $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                $emailData = str_replace('{BOOKING_DATE}', $start_time, $emailData);
                $emailData = str_replace('{NO_PERSON}', $number_person, $emailData);
                $emailData = str_replace('{CUSTOMER_NAME}', $customer_name, $emailData);
                $subject = ucwords(str_replace('_', ' ', $emailSuccess['EmailTemplate']['template_subject']));

                $emailData = str_replace('{STORE_NAME}', $store['Store']['store_name'], $emailData);
                $storeAddress = $store['Store']['address'] . "<br>" . $store['Store']['city'] . ", " . $store['Store']['state'] . " " . $store['Store']['zipcode'];
                $storePhone = $store['Store']['phone'];
                $url = "http://" . $store['Store']['store_url'];
                $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $store['Store']['store_url'] . "</a>";
                $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);

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
                    $mobnumber = '+1' . str_replace(array('(', ')', ' ', '-'), '', $store['Store']['notification_number']);
                } else {
                    $mobnumber = '+1' . str_replace(array('(', ')', ' ', '-'), '', $store['Store']['phone']);
                }
                $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                $smsData = str_replace('{BOOKING_DATE}', $start_time, $smsData);
                $smsData = str_replace('{NO_PERSON}', $number_person, $smsData);
                $smsData = str_replace('{CUSTOMER_NAME}', $customer_name, $smsData);
                $smsData = str_replace('{STORE_NAME}', $store['Store']['store_name'], $smsData);
                $smsData = str_replace('{STORE_PHONE}', $mobnumber, $smsData);
                $message = $smsData;
                $this->HqCommon->sendSmsNotificationFront($mobnumber, $message, $decrypt_storeId);
            }
            $this->Session->setFlash(__('Reservation has been cancelled'), 'flash_success');
            $this->redirect(array('controller' => 'hqusers', 'action' => 'myBookings')); //
        } else {
            $this->Session->setFlash(__('Reservation could not be cancelled, please try again'), 'flash_error');
            $this->redirect(array('controller' => 'hqusers', 'action' => 'myBookings')); //
        }
    }

    /* ------------------------------------------------
      Function name:deleteBooking()
      Description:Delete User Booking
      created:13/09/2016
      ----------------------------------------------------- */

    public function deleteBooking($encrypted_bookingId = null) {
        $this->autoRender = false;
        $merchantId = $this->Session->read('hq_id');
        $user_id = AuthComponent::User('id');
        if (empty($merchantId) && empty($user_id) && empty($encrypted_bookingId)) {
            $this->merchant();
        }
        $data['Booking']['id'] = $this->Encryption->decode($encrypted_bookingId);
        $data['Booking']['is_deleted'] = 1;
        $this->loadModel('Booking');
        if ($this->Booking->saveBookingDetails($data)) {
            $this->Session->setFlash(__('Reservation has been deleted.'), 'flash_success');
            $this->redirect(array('controller' => 'hqusers', 'action' => 'myBookings')); //
        } else {
            $this->Session->setFlash(__('Reservation could not be deleted, please try again'), 'flash_error');
            $this->redirect(array('controller' => 'hqusers', 'action' => 'myBookings')); //
        }
    }

    /* ------------------------------------------------
      Function name: rating()
      Description: Review and Rating for orders
      created:15/09/2016
      ----------------------------------------------------- */

    public function rating($encrypted_storeId = null, $order_item_id = null, $order_id = null, $status = null, $orderName = null, $orderRating = null, $itemId = null) {
        //$this->layout = "merchant";
        $this->layout = "merchant_front";
        $merchantId = $this->Session->read('hq_id');
        $user_id = AuthComponent::User('id');
        if (empty($merchantId) && empty($user_id)) {
            $this->merchant();
        }
        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
        $order_item_id = $this->Encryption->decode($order_item_id);
        $order_id = $this->Encryption->decode($order_id);
        $item_id = $this->Encryption->decode($itemId);
        $status = $this->Encryption->decode($status);
        $orderName = $this->Encryption->decode($orderName);
        $store = $this->Store->fetchStoreDetail($decrypt_storeId, $merchantId);
        $this->loadModel('StoreReview');
        $this->StoreReview->bindModel(array('belongsTo' => array('User' => array('fields' => array('salutation', 'fname', 'lname')))), false);
        $allReviews = $this->StoreReview->getReviewDetails($decrypt_storeId, $item_id);
        $this->set(compact('item_id', 'orderRating', 'orderName', 'allReviews', 'status', 'allReviwes', 'encrypted_storeId', 'encrypted_merchantId', 'decrypt_storeId', 'decrypt_merchantId', 'order_item_id', 'order_id', 'user_id'));
        if ($this->request->is(array('post', 'put'))) {//pr($this->data);die;
            $this->request->data['StoreReview']['merchant_id'] = $merchantId;
            $data = $this->data;
            if (!empty($data['StoreReviewImage']) && $data['StoreReviewImage']['image'][0]['error'] == 0) {
                $response = $this->Common->checkImageExtensionAndSize($data['StoreReviewImage']);
                if (empty($response['status']) && !empty($response['errmsg'])) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    $this->redirect($this->referer());
                }
            }
            $encrypted_storeId = $this->Encryption->encode($data['StoreReview']['store_id']);
            $data['StoreReview']['is_active'] = 1;
            $data['StoreReview']['is_approved'] = 1;
            $data['StoreReview']['is_deleted'] = 0;
            $this->StoreReview->create();

            if ($this->StoreReview->saveReview($data)) {
                $storeReviewId = $this->StoreReview->getLastInsertId();
                if (!empty($storeReviewId) && !empty($data['StoreReviewImage']) && $data['StoreReviewImage']['image'][0]['error'] == 0) {
                    $this->_uploadStoreReviewImages($data, $storeReviewId);
                }
                $template_type = 'review_rating';
                $this->loadModel('EmailTemplate');
                $fullName = "Admin";
                $item_name = $data['StoreReview']['item_name'];
                $review = $data['StoreReview']['review_comment']; //no of person
                $rating = $data['StoreReview']['review_rating'];
                $customer_name = AuthComponent::User('fname') . " " . AuthComponent::User('lname');
                $emailSuccess = $this->EmailTemplate->storeTemplates($data['StoreReview']['store_id'], $merchantId, $template_type);
                $store = $this->Store->fetchStoreDetail($data['StoreReview']['store_id'], $merchantId);
                if ($emailSuccess) {
                    if (($store['Store']['notification_type'] == 1 || $store['Store']['notification_type'] == 3) && (!empty($store['Store']['notification_email']))) {
                        $storeEmail = trim($store['Store']['notification_email']);
                    } else {
                        $storeEmail = trim($store['Store']['email_id']);
                    }

                    $customerEmail = trim(AuthComponent::User('email'));
                    $emailData = $emailSuccess['EmailTemplate']['template_message'];
                    $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                    $emailData = str_replace('{REVIEW}', $review, $emailData);
                    $emailData = str_replace('{RATING}', $rating, $emailData);
                    $emailData = str_replace('{ITEM_NAME}', $item_name, $emailData);
                    $emailData = str_replace('{CUSTOMER_NAME}', $customer_name, $emailData);
                    $storeAddress = $store['Store']['address'] . "<br>" . $store['Store']['city'] . ", " . $store['Store']['state'] . " " . $store['Store']['zipcode'];
                    $storePhone = $store['Store']['phone'];
                    $url = "http://" . $store['Store']['store_url'];
                    $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $store['Store']['store_url'] . "</a>";
                    $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                    $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                    $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);
                    $subject = ucwords(str_replace('_', ' ', $emailSuccess['EmailTemplate']['template_subject']));
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
                        $mobnumber = '+1' . str_replace(array('(', ')', ' ', '-'), '', $store['Store']['notification_number']);
                    } else {
                        $mobnumber = '+1' . str_replace(array('(', ')', ' ', '-'), '', $store['Store']['phone']);
                    }
                    $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                    $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                    $smsData = str_replace('{REVIEW}', $review, $smsData);
                    $smsData = str_replace('{RATING}', $rating, $smsData);
                    $smsData = str_replace('{ITEM_NAME}', $item_name, $smsData);
                    $smsData = str_replace('{CUSTOMER_NAME}', $customer_name, $smsData);
                    $smsData = str_replace('{STORE_NAME}', $store['Store']['store_name'], $smsData);
                    $smsData = str_replace('{STORE_PHONE}', $mobnumber, $smsData);
                    $message = $smsData;
                    $this->HqCommon->sendSmsNotificationFront($mobnumber, $message, $decrypt_storeId);
                }
                $this->Session->setFlash(__("Rating & Review has been saved successfully"), 'flash_success');
            } else {
                $this->Session->setFlash(__("Some problem has been occured"), 'flash_error');
            }
            $this->redirect(array('controller' => 'hqusers', 'action' => 'myOrders'));
        }
    }

    private function _uploadStoreReviewImages($data = null, $store_review_id = null) {
        if (!empty($data) && !empty($store_review_id)) {
            foreach ($data['StoreReviewImage']['image'] as $image) {
                if ($image['error'] == 0) {
                    //$response = $this->Common->uploadMenuItemImages($image, '/storeReviewImage/', $data['StoreReview']['store_id']);
                    $response = $this->Common->uploadMenuItemImages($image, '/storeReviewImage/', $data['StoreReview']['store_id'], 300, 190);
                } elseif ($image['error'] == 4) {
                    $response['status'] = true;
                    $response['imagename'] = '';
                }
                if ($response['imagename']) {
                    $imageData['image'] = $response['imagename'];
                    $imageData['store_id'] = $data['StoreReview']['store_id'];
                    $imageData['created'] = date("Y-m-d H:i:s");
                    $imageData['store_review_id'] = $store_review_id;
                    $imageData['is_active'] = 1;
                    $imageData['is_deleted'] = 0;
                    $this->loadModel('StoreReviewImage');
                    $this->StoreReviewImage->create();
                    $this->StoreReviewImage->saveStoreReviewImage($imageData);
                }
            }
        }
    }

    /* ------------------------------------------------
      Function name:myBookings()
      Description:List of User Bookings
      created:31/8/2015
      ----------------------------------------------------- */

    public function myReservation($encrypted_storeId = null) {
        $this->layout = "merchant";
        $merchantId = $this->Session->read('hq_id');
        $user_id = AuthComponent::User('id');
        if (empty($merchantId) && empty($user_id)) {
            $this->merchant();
        }
        $decrypt_merchantId = $merchantId;
        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
        $avalibilty_status = $this->HqCommon->checkStoreAvalibility($decrypt_storeId); // I will check the time avalibility of the store
        if ($avalibilty_status != 1) {
            $setPre = 1;
        } else {
            $setPre = 0;
        }
        $store = $this->Store->fetchStoreDetail(2);
        $current_date = date("Y-m-d", (strtotime($this->HqCommon->storeTimeZoneUser(null, date('Y-m-d H:i:s'), 2))));

        if ($store['Store']['dineinblackout_limit']) {
            $current_date = date("Y-m-d", strtotime($current_date . ' +' . $store['Store']['dineinblackout_limit'] . ' day'));
        }

        $today = 1;
        $orderType = 1;
        $finaldata = $this->HqCommon->getNextDayTimeRange($current_date, $today, $orderType, $decrypt_storeId, $decrypt_merchantId);
        $time_break = $finaldata['time_break'];
        $store_data = $finaldata['store_data'];
        $storeBreak = $finaldata['storeBreak'];
        $time_range = $finaldata['time_range'];
        $current_date = $finaldata['currentdate'];
        $i = 1;
        $number_person = array();
        for ($i; $i < 30; $i++) {
            $number_person[$i] = $i;
        }

        $explodeVal = explode("-", $current_date);
        $currentDateVar = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];
        $closedDay = array();
        $this->loadModel('StoreAvailability');
        $storeavaibilityInfo = $this->StoreAvailability->getclosedDay($decrypt_storeId);
        $daysarray = array('sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6);
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
        $this->set('closedDay', $closedDay);

        $this->set(compact('storeBreak', 'myBookings', 'setPre', 'number_person', 'time_break', 'time_range', 'store_data', 'stores', 'encrypted_storeId', 'encrypted_merchantId', 'encrypted_userId', 'currentDateVar'));

        if ($this->request->is('post')) {
            $this->request->data['Booking']['store_id'] = $decrypt_storeId;
            $this->request->data['Booking']['user_id'] = AuthComponent::User('id');
            $reservationDate = $this->Dateform->formatDate($this->request->data['Booking']['start_date']);
            $ResTime = $this->request->data['Store']['pickup_hour'] . ':' . $this->request->data['Store']['pickup_minute'] . ':00';
            $reservationDateTime = $reservationDate . " " . $ResTime;

            $this->request->data['Booking']['reservation_date'] = $reservationDateTime;
            $save_result = $this->Booking->saveBookingDetails($this->data); // call on model to save data
            if ($store['Store']['is_dinein_printer'] == 1) {
                $last_id = $this->Booking->getLastInsertId();
                $aPrintData = array();
                $aPrintData['id'] = '';
                $aPrintData['merchant_id'] = $this->Session->read('hq_id');
                $aPrintData['store_id'] = $decrypt_storeId;
                $aPrintData['order_id'] = $last_id;
                $aPrintData['order_number'] = $last_id;
                $aPrintData['type'] = '3'; //DineIn Printer
                $this->StorePrintHistory->saveStorePrintHistory($aPrintData);
            }

            if ($save_result) {
                $template_type = 'customer_dine_in_request';
                $this->loadModel('DefaultTemplate');
                $fullName = "Admin";
                $number_person = $this->data['Booking']['number_person']; //no of person
                $start_date = $this->data['Booking']['start_date'];
                $start_time = date('H:i a', strtotime($ResTime));
                $customer_name = AuthComponent::User('fname') . " " . AuthComponent::User('lname');
                if ($this->data['Booking']['special_request']) {
                    $special_request = $this->data['Booking']['special_request'];
                } else {
                    $special_request = "N/A";
                }
                //$emailSuccess = $this->EmailTemplate->storeTemplates($decrypt_storeId, $decrypt_merchantId, $template_type);
                $emailSuccess = $this->DefaultTemplate->find('first', array('conditions' => array('DefaultTemplate.template_code' => $template_type, 'DefaultTemplate.is_default' => 1)));
                if ($emailSuccess) {
                    if (($store['Store']['notification_type'] == 1 || $store['Store']['notification_type'] == 3) && (!empty($store['Store']['notification_email']))) {
                        $storeEmail = $store['Store']['notification_email'];
                    } else {
                        $storeEmail = $store['Store']['email_id'];
                    }
                    $contactPerson = AuthComponent::User('fname') . " " . AuthComponent::User('lname') . " " . AuthComponent::User('phone');
                    $customerEmail = trim(AuthComponent::User('email'));
                    $emailData = $emailSuccess['DefaultTemplate']['template_message'];
                    $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                    $emailData = str_replace('{BOOKING_DATE}', $start_date, $emailData);
                    $emailData = str_replace('{BOOKING_TIME}', $start_time, $emailData);
                    $emailData = str_replace('{NO_PERSON}', $number_person, $emailData);
                    $emailData = str_replace('{SPECIAL_REQUEST}', $special_request, $emailData);
                    $emailData = str_replace('{CUSTOMER_NAME}', $customer_name, $emailData);
                    $emailData = str_replace('{CONTACT_PERSON}', $contactPerson, $emailData);
                    $subject = ucwords(str_replace('_', ' ', $emailSuccess['DefaultTemplate']['template_subject']));

                    $emailData = str_replace('{STORE_NAME}', $store['Store']['store_name'], $emailData);
                    $storeAddress = $store['Store']['address'] . "<br>" . $store['Store']['city'] . ", " . $store['Store']['state'] . " " . $store['Store']['zipcode'];

                    $storePhone = $store['Store']['phone'];
                    $url = "http://" . $store['Store']['store_url'];
                    $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $store['Store']['store_url'] . "</a>";
                    $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                    $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                    $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);

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
                    $smsData = $emailSuccess['DefaultTemplate']['sms_template'];
                    $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                    $smsData = str_replace('{BOOKING_DATE}', $start_date, $smsData);
                    $smsData = str_replace('{BOOKING_TIME}', $start_time, $smsData);
                    $smsData = str_replace('{NO_PERSON}', $number_person, $smsData);
                    $smsData = str_replace('{SPECIAL_REQUEST}', $special_request, $smsData);
                    $smsData = str_replace('{CONTACT_PERSON}', $contactPerson, $smsData);
                    $smsData = str_replace('{STORE_NAME}', $store['Store']['store_name'], $smsData);
                    $smsData = str_replace('{STORE_PHONE}', $mobnumber, $smsData);
                    $message = $smsData;
                    $this->Common->sendSmsNotificationFront($mobnumber, $message);
                }

                $this->Session->setFlash(__('Your request has been submitted, you will receive a confirmation email shortly. Thank you!'), 'flash_success');
                $this->redirect(array('controller' => 'pannels', 'action' => 'myBookings', $encrypted_storeId, $encrypted_merchantId)); //
            } else {
                $this->Session->setFlash(__('Reservation Request could not be submitted, please try again'), 'flash_error');
                $this->redirect(array('controller' => 'pannels', 'action' => 'myBookings', $encrypted_storeId, $encrypted_merchantId)); //
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
            $storeId = $this->Encryption->decode($_POST['storeId']);
            $merchantId = $this->Encryption->decode($_POST['merchantId']);
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
            $todayDate = date("m-d-Y", (strtotime($this->HqCommon->storeTimeZoneUser('', date("Y-m-d H:i:s"), $storeId))));



            if (empty($holidayList)) {
                if (!empty($store_availability)) {
                    $start = $store_availability['StoreAvailability']['start_time'];
                    $end = $store_availability['StoreAvailability']['end_time'];
                    $StoreCutOff = $this->Store->fetchStoreCutOff($storeId);
                    $cutTime = '-' . $StoreCutOff['Store']['cutoff_time'] . ' minutes';
                    $end = date("H:i:s", strtotime("$cutTime", strtotime($end)));
                    $orderType = $this->request->data['orderType'];
                    $preOrder = $this->request->data['preOrder'];

                    if (strtotime(str_replace('-', '/', $_POST['date'])) == strtotime(str_replace('-', '/', $todayDate))) {
                        $start = $this->HqCommon->getStartTime($start, true, $orderType, $preOrder, $end, $storeId);
                    } else {
                        $start = $this->HqCommon->getStartTime($start, false, $orderType, $preOrder, $end, $storeId);
                    }

                    $time_ranges = $this->HqCommon->getStoreTime($start, $end, $orderType, null, null, $storeId); // calling Common Component
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
                            $time_break1 = $this->HqCommon->getStoreTime($break_start_time, $break_end_time, null, null, null, $storeId);
                        }
                        if ($store_data['Store']['is_break2'] == 1) {
                            $break_start_time = $store_break['StoreBreak']['break2_start_time'];
                            $break_end_time = $store_break['StoreBreak']['break2_end_time'];
                            $storeBreak[1]['start'] = $store_break['StoreBreak']['break2_start_time'];
                            $storeBreak[1]['end'] = $store_break['StoreBreak']['break2_end_time'];
                            $time_break2 = $this->HqCommon->getStoreTime($break_start_time, $break_end_time, null, null, null, $storeId);
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

    public function myDeliveryAddress() {
        $this->layout = "merchant_front";
        $decrypt_merchantId = $this->Session->read('hq_id');
        $this->loadModel('DeliveryAddress');
        $userId = AuthComponent::User('id'); // Customer Id
        $roleId = AuthComponent::User('role_id');
        $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
        $checkaddress = $this->DeliveryAddress->find('all', array('conditions' => array('DeliveryAddress.user_id' => $userId, 'DeliveryAddress.merchant_id' => $decrypt_merchantId, 'DeliveryAddress.is_deleted' => 0, 'DeliveryAddress.is_active' => 1)));
        // It will call the function in the model to check the address either exist or not
        if (!$checkaddress) {
            $checkaddress = array();
        }

        $this->set(compact('time_break', 'checkaddress', 'time_range'));
    }

    /* ------------------------------------------------
      Function name:addAddress()
      Description:This section will add the delivery address portion
      created:27/7/2015
      ----------------------------------------------------- */

    public function addAddress() {
        $this->layout = "merchant_front";
        $decrypt_merchantId = $this->Session->read('hq_id');
        $this->loadModel('DeliveryAddress');
        $userId = AuthComponent::User('id'); // Customer Id
        $roleId = AuthComponent::User('role_id');
        $checkaddress = $this->DeliveryAddress->find('all', array('conditions' => array('DeliveryAddress.user_id' => $userId, 'DeliveryAddress.merchant_id' => $decrypt_merchantId, 'DeliveryAddress.is_deleted' => 0, 'DeliveryAddress.is_active' => 1)));
        // It will call the function in the model to check the address either exist or not
        $label1 = 0;
        $label2 = 0;
        $label3 = 0;
        $label4 = 0;
        $label5 = 0;
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
                $this->redirect(array('controller' => 'hqusers', 'action' => 'addAddress'));
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
                $this->DeliveryAddress->create();
                $this->DeliveryAddress->saveAddress($data);
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
                $this->DeliveryAddress->create();
                $this->DeliveryAddress->saveAddress($data1);
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
                $this->DeliveryAddress->create();
                $this->DeliveryAddress->saveAddress($data2);
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
                $this->DeliveryAddress->create();
                $this->DeliveryAddress->saveAddress($data3);
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
                $this->DeliveryAddress->create();
                $this->DeliveryAddress->saveAddress($data4);
            }

            $this->Session->setFlash(__('Delivery Address has been saved successfully'), 'flash_success');
            $this->redirect(array('controller' => 'hqusers', 'action' => 'myDeliveryAddress'));
        }
        $this->loadModel('CountryCode');
        $countryCode = $this->CountryCode->fetchAllCountryCode();
        $this->set(compact('label1', 'label2', 'label3', 'label4', 'label5', 'countryCode'));
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

    public function updateAddress($encrypt_deliveryAddressId) {
        $this->layout = "merchant_front";
        $decrypt_merchantId = $this->Session->read('hq_id');
        $decrypt_deliveryAddressId = $this->Encryption->decode($encrypt_deliveryAddressId);
        $this->loadModel('CountryCode');
        $countryCode = $this->CountryCode->fetchAllCountryCode();
        $this->set(compact('countryCode'));
        $this->loadModel('DeliveryAddress');
        $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
        $resultAddress = $this->DeliveryAddress->fetchAddress($decrypt_deliveryAddressId);
        if ($this->request->is(array('post', 'put')) && !empty($resultAddress)) {
            $zipCode = trim($this->request->data['DeliveryAddress']['zipcode'], " ");
            $stateName = trim($this->data['DeliveryAddress']['state'], " ");
            $cityName = strtolower($this->request->data['DeliveryAddress']['city']);
            $cityName = trim(ucwords($cityName));
            $address = trim(ucwords($this->request->data['DeliveryAddress']['address']));
            $dlocation = $address . " " . $cityName . " " . $stateName . " " . $zipCode;
            $adjuster_address2 = str_replace(' ', '+', $dlocation);
            $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $adjuster_address2 . '&sensor=false');
            $output = json_decode($geocode);
            $this->request->data['DeliveryAddress']['id'] = $decrypt_deliveryAddressId;
            $this->request->data['DeliveryAddress']['user_id'] = AuthComponent::User('id');
            $this->request->data['DeliveryAddress']['merchant_id'] = $decrypt_merchantId;
            if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                
            } else {
                $latitude = @$output->results[0]->geometry->location->lat;
                $longitude = @$output->results[0]->geometry->location->lng;
                $this->request->data['DeliveryAddress']['latitude'] = $latitude;
                $this->request->data['DeliveryAddress']['longitude'] = $longitude;
            }
            if ($this->request->data['DeliveryAddress']['default'] == 1) {
                $this->DeliveryAddress->updateAll(array('DeliveryAddress.default' => 0), array('DeliveryAddress.user_id' => $this->request->data['DeliveryAddress']['user_id']));
            }
            $result_sucess = $this->DeliveryAddress->saveAddress($this->request->data);
            if ($result_sucess) {
                $this->Session->setFlash(__('Delivery Address has been updated successfully'), 'flash_success');
                $this->redirect(array('controller' => 'hqusers', 'action' => 'myDeliveryAddress'));
            } else {
                $this->Session->setFlash(__('Delivery Address could not be updated, please try again'), 'flash_error');
            }
        }
        if ($resultAddress) {
            $this->request->data = $resultAddress;
        }
    }

    /* ------------------------------------------------
      Function name:checkusersadddress()
      Description:This section will verify the address
      created:27/7/2015
      ----------------------------------------------------- */

    public function getDeliveryAddress() {
        $this->layout = false;
        $this->loadModel('DeliveryAddress');
        $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
        if (empty($_POST['deliveryId'])) {
            $delivery = $this->DeliveryAddress->fetchfirstAddress(AuthComponent::User('id'));
            $deliveryID = $delivery['DeliveryAddress']['id'];
        } else {
            $deliveryID = $_POST['deliveryId'];
        }
        $resultAddress = $this->DeliveryAddress->fetchAddress($deliveryID);
        $this->set(compact('resultAddress'));
    }

    /* ------------------------------------------------
      Function name:deleteDeliveryAddress()
      Description: Delete delivery address of user
      created:28/09/2015
      ----------------------------------------------------- */

    public function deleteDeliveryAddress($encrypted_deliveryaddressId = null) {
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

    public function city() {
        $this->layout = false;
        $this->autoRender = false;
        $this->loadModel('City');
        if ($this->request->is('ajax') && !empty($this->request->data['state_id'])) {
            $result = $this->City->find('list', array('conditions' => array('state_id' => $this->request->data['state_id'])));
            $viewObject = new View($this, false);
            echo $viewObject->Form->input('User.city_id', array('type' => 'select', 'options' => @$result, 'class' => 'form-control custom-text', 'label' => false, 'div' => false, 'empty' => 'Select City'));
        }
    }

    public function zip() {
        $this->layout = false;
        $this->autoRender = false;
        $this->loadModel('Zip');
        if ($this->request->is('ajax') && !empty($this->request->data['state_id']) && !empty($this->request->data['city_id'])) {
            $result = $this->Zip->find('list', array('fields' => array('id', 'zipcode'), 'conditions' => array('state_id' => $this->request->data['state_id'], 'city_id' => $this->request->data['city_id'])));
            $viewObject = new View($this, false);
            echo $viewObject->Form->input('User.zip_id', array('type' => 'select', 'options' => @$result, 'class' => 'form-control custom-text', 'label' => false, 'div' => false, 'empty' => 'Select Zip'));
        }
    }

    public function getTermsAndPolicyData() {
        $this->layout = false;
        $this->autoRender = false;
        $this->loadModel('TermsAndPolicy');
        if ($this->request->is('ajax') && !empty($this->request->data['type'])) {
            $merchantId = $this->Session->read('hq_id');
            $type = $this->request->data['type'];
            if ($type == 'Term') {
                $getContent = 'terms_and_conditions';
                $heading = 'Terms & Conditions';
            } else {
                $getContent = 'privacy_policy';
                $heading = 'Privacy Policy';
            }
            $tandcData = $this->TermsAndPolicy->findByMerchantId($merchantId, array($getContent));
            if (!empty($tandcData)) {
                if ($type == 'Term') {
                    $tandcData = $tandcData['TermsAndPolicy']['terms_and_conditions'];
                } else {
                    $tandcData = $tandcData['TermsAndPolicy']['privacy_policy'];
                }
                $this->set(compact('tandcData', 'heading'));
                $this->render('/Elements/hquser/term_and_policy');
            }
        }
    }

    public function getState() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('State');
            $states = $this->State->find('list', array('conditions' => array('OR' => array('State.name LIKE' => '%' . $_GET['term'] . '%'))));
            echo json_encode($states);
        } else {
            exit;
        }
    }

    public function storeRedirect($encrypted_storeId = null) {
        $this->layout = false;
        $storeId = $this->Encryption->decode($encrypted_storeId);
        $merchantId = $this->Session->read('hq_id');
        $this->Store->bindModel(
                array(
            'hasOne' => array(
                'StoreSetting' => array(
                    'className' => 'StoreSetting',
                    'foreignKey' => 'store_id'
                )
            )
                ), false
        );
        $storeDetail = $this->Store->find('first', array('conditions' => array('Store.id' => $storeId, 'merchant_id' => $merchantId, 'Store.is_deleted' => 0, 'Store.is_active' => 1), 'fields' => array('service_fee', 'delivery_fee', 'minimum_order_price', 'store_name', 'store_url', 'phone', 'time_zone_id', 'StoreSetting.merchant_btn_redirect')));
        if (!empty($storeDetail)) {
            $this->Session->write('service_fee', $storeDetail['Store']['service_fee']);
            //$this->Session->write('delivery_fee', $storeDetail['Store']['delivery_fee']);
            $this->Session->write('minprice', $storeDetail['Store']['minimum_order_price']);
            $this->Session->write('storeName', $storeDetail['Store']['store_name']);
            $this->Session->write('store_url', $storeDetail['Store']['store_url']);
            $this->Session->write('store_phone', $storeDetail['Store']['phone']);
            $this->Session->write('store_id', $storeId);
            $this->Session->write('merchant_id', $merchantId);
            $this->Cookie->write('storecookiename', $storeDetail['Store']['store_url']);
            $this->Session->write('front_time_zone_id', $storeDetail['Store']['time_zone_id']);
            $encrypted_merchantId = $this->Encryption->encode($merchantId);
            if (!empty($storeDetail['StoreSetting']['merchant_btn_redirect'])) {
                header('Location:' . 'http://' . $storeDetail['Store']['store_url'] . '/products/items/' . $encrypted_storeId . '/' . $encrypted_merchantId);
                //$this->redirect(array('controller' => 'products', 'action' => 'items/' . $encrypted_storeId . '/' . $encrypted_merchantId));
            } else {
                header('Location:' . 'http://' . $storeDetail['Store']['store_url']);
                //$this->redirect(array('controller' => 'users', 'action' => 'login'));
            }
            exit;
        } else {
            $this->redirect($this->referer());
        }
    }

//   public function getStateByCity() {
//        $this->layout = false;
//        $this->autoRender = false;
//          if ($this->request->is('ajax') && !empty($this->request->data['city_id'])) {
//            $this->loadModel('City');
//            $this->loadModel('User');
//            $this->loadModel('State');
//            $this->City->bindModel(
//                    array('belongsTo' => array(
//                            'State' => array(
//                                'className' => 'State',
//                                'foreignKey' => 'state_id'
//                            )
//            )));
//            $states = $this->City->find('all', array('conditions' => array('City.name LIKE' => '%' .$this->request->data['city_id'] . '%')));
//
//            $stateListArr = array();
//            if (!empty($states)) {
//                foreach ($states as $s => $stateList) {
//                    if (!empty($stateList['State']['id'])) {
//                        $stateListArr[$stateList['State']['id']] = $stateList['State']['name'];
//                    }
//                }
//            }
//            //return $stateListArr; 
//            $viewObject = new View($this, false);
//            echo $this->Form->input('User.state_id', array('type' => 'select', 'options' => @$stateListArr, 'class' => 'form-control custom-text', 'label' => false, 'div' => false, 'empty' => "Select State"));
//        } else {
//            exit;
//        }
//    }
}
