<?php

App::uses('StoreAppController', 'Controller');

class StoresController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Paginator', 'Common', 'Dateform');
    public $helper = array('Encryption', 'Paginator', 'Form', 'DateformHelper', 'Common');
    public $uses = array('User', 'StoreGallery', 'Store', 'StoreBreak', 'StoreAvailability', 'StoreHoliday', 'Category', 'Tab', 'Permission', 'StoreTheme', 'Merchant', 'StoreTax', 'StoreFont', 'StoreTip');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow("getTermsAndPolicyData");
    }

    public function store() {
        $this->autoRender = false;
        $this->redirect(array('controller' => 'stores', 'action' => 'login'));
    }

    /* ------------------------------------------------
      Function name:index()
      Description:redirect user to Admin dasboard
      created:22/7/2015
      ----------------------------------------------------- */

    public function index() {
        $this->autoRender = false;
        $this->redirect(array('controller' => 'stores', 'action' => 'dashboard'));
    }

    /* ------------------------------------------------
      Function name:login()
      Description:Registration  Form for the  End customer
      created:22/7/2015
      ----------------------------------------------------- */

    public function login($layout_type = null) {
        $this->layout = "store_login";
        $this->set('title', 'Sign in');
        if ($this->request->is('post')) {
            $storeId = $this->Session->read('admin_store_id'); // It will read from session when a customer will try to register on store
            $merchantId = $this->Session->read('admin_merchant_id');
            if ($storeId) {
                $this->request->data['User']['store_id'] = $storeId;
            }
            $this->User->set($this->request->data);
            if ($this->User->validates()) {
                if ($this->data['User']['remember'] == 1) {
                    // Cookie is valid for 7 days
                    $this->Cookie->write('Auth.store_admin_email', $this->data['User']['email'], false, 604800);
                    $this->Cookie->write('Auth.store_admin_password', $this->data['User']['password'], false, 604800);
                    $this->set('cookies', '1');
                    unset($this->request->data['User']['remember_me']);
                } else {
                    $this->Cookie->delete('Auth');
                    $this->Cookie->delete('Auth');
                }

                if ($this->Auth->login()) {

                    $roleId = $this->Session->read('Auth.Admin.role_id'); // ROLE OF THE USER [4=>Customer]
                    $this->Session->write('login_date_time', $this->Common->sa_gettodayDate(3));
                    //$this->Session->setFlash("<div class='alert_success'>".LOGINSUCCESSFULL."</div>");
                    if ($roleId == 3) {  // Store admin will redirect to his related dashboard
                        $this->redirect(array('controller' => 'stores', 'action' => 'dashboard'));
                    } else {
                        $this->redirect(array('controller' => 'stores', 'action' => 'logout'));
                    }
                } else {
                    $this->Session->setFlash(__("Invalid email or password, try again"), 'alert_failed');
                }
            }
        } elseif ($this->Auth->login()) {
            $this->redirect(array('controller' => 'stores', 'action' => 'dashboard'));
        } else {
            $UserId = $this->Session->read('Auth.Admin.id');
            if ($UserId) {
                $this->redirect(array('controller' => 'Stores', 'action' => 'logout'));
            }
            $this->set('rem', $this->Cookie->read('Auth.store_admin_email'));
            if ($this->Cookie->read('Auth.store_admin_email')) {
                $this->request->data['User']['email'] = $this->Cookie->read('Auth.store_admin_email');
                $this->request->data['User']['password'] = $this->Cookie->read('Auth.store_admin_password');
            }
        }
    }

    /* ------------------------------------------------
      7Function name:dashboard()
      Description:Dash Board of Store Admin
      created:27/7/2015
      ----------------------------------------------------- */

    public function dashboard() {
        $this->layout = "admin_dashboard";
        $roleId = $this->Session->read('Auth.Admin.role_id'); // ROLE OF THE USER [4=>Customer]
        if ($roleId != 3) {  // Store admin will redirect to his related dashboard
            //$this->redirect(array('controller'=>'stores','action'=>'logout'));
            $this->InvalidLogin($roleId);
        }
        $storeId = $this->Session->read('admin_store_id'); // It will read from session when a customer will try to register on store
        $this->loadModel('StorePrinterStatus');
        $this->StorePrinterStatus->bindModel(array(
            'belongsTo' => array('Merchant' => array('className' => 'Merchant', 'foreignKey' => 'merchant_id', 'fields' => array('Merchant.name')),
            'Store' => array('className' => 'Store', 'foreignKey' => 'store_id', 'fields' => array('Store.store_name','Store.id')))
        ), false);

        $this->paginate = array('recursive' => 2, 'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1, 'StorePrinterStatus.store_id'=>$storeId));
        $result = $this->paginate('StorePrinterStatus');
        $date1 = new DateTime(date('Y-m-d H:i:s'));
        for($i=0; $i<count($result); $i++) {
            $update_date = $result[$i]['StorePrinterStatus']['modified'];
            $date2 = new DateTime(date('Y-m-d H:i:s', strtotime($update_date)));
            $interval = $date1->diff($date2);
            if($interval->s <= PRINTER_CHECK_INTERVAL && $interval->i == 0) {
                $result[$i]['StorePrinterStatus']['is_active'] = 1;
            } else {
                $result[$i]['StorePrinterStatus']['is_active'] = 0;
            }
        }
        $this->set('list', $result);
    }

    /* ------------------------------------------------
      Function name:logout()
      Description:For logout of the user
      created:27/7/2015
      ----------------------------------------------------- */

    public function logout() {
        $admin_domainName = $this->Session->read('admin_domainname');
        $this->Session->delete('admin_storeName');
        $this->Session->delete('admin_domainname');
        $this->Session->delete('admin_store_id');
        $this->Session->delete('admin_merchant_id');
        $this->Session->delete('Auth.Admin');
        $this->redirect('/admin');
    }

    /* ------------------------------------------------
      Function name:dashboard()
      Description:Dash Board of Store Admin
      created:27/7/2015
      ----------------------------------------------------- */

    public function manageStaff($EncrypteduserID = null) {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], $this->params['action'])) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->layout = "admin_dashboard";
        $userResult = $this->User->currentUserInfo($this->Session->read('Auth.Admin.id'));
        $loginuserid = $this->Session->read('Auth.Admin.id');
        $this->set('loginuserid', $loginuserid);
        $roleId = $userResult['User']['role_id'];
        $merchantId = $this->Session->read('admin_merchant_id');
        $storeId = $this->Session->read('admin_store_id');
        $this->set(compact('roleId'));
        $this->User->set($this->request->data);
        if ($EncrypteduserID) {
            $userID = $this->Encryption->decode($EncrypteduserID);
            $this->Tab->bindModel(
                    array(
                'hasMany' => array(
                    'Permission' => array(
                        'className' => 'Permission',
                        'foreignKey' => 'tab_id',
                        'conditions' => array('Permission.is_deleted' => 0, 'Permission.is_active' => 1, 'Permission.user_id' => $userID),
                        'fields' => array('id', 'tab_id')
                    )
                )
                    ), false
            );
        }
        $this->loadModel('Tab');
        $Tabs = $this->Tab->getTabs($roleId);
        $this->set(compact('Tabs'));
        if ($this->User->validates()) {
            if ($this->request->is(array('post', 'put')) && !empty($this->request->data['User']['phone'])) { //pr($this->request->data);die;
                $this->request->data = $this->Common->trimValue($this->request->data);
                if ($this->request->data['User']['id']) {
                    $this->request->data['User']['fname'] = trim($this->request->data['User']['fname']);
                    $this->request->data['User']['lname'] = trim($this->request->data['User']['lname']);
                    $this->request->data['User']['email'] = trim($this->request->data['User']['email']);
                    $userdata['User'] = $this->request->data['User'];
                    if ($this->User->saveUserInfo($userdata)) {
                        $this->permission($this->request->data['User']['id'], $this->request->data['Permission']);
                        $this->Session->setFlash(__("Staff member details has been updated successfully"), 'alert_success');
                        $this->redirect(array('controller' => 'stores', 'action' => 'manageStaff'));
                    } else {
                        $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                        $this->redirect(array('controller' => 'stores', 'action' => 'manageStaff'));
                    }
                } elseif ($this->User->storeemailExists($this->request->data['User']['email'], $roleId, $storeId) && $this->request->data['User']['id'] == '') {
                    $this->request->data['User']['store_id'] = $storeId;
                    $this->request->data['User']['merchant_id'] = $merchantId;
                    $this->request->data['User']['fname'] = trim($this->request->data['User']['fname']);
                    $this->request->data['User']['lname'] = trim($this->request->data['User']['lname']);
                    $this->request->data['User']['email'] = trim($this->request->data['User']['email']);
                    $this->request->data['User']['password'] = trim($this->request->data['User']['password']);
                    $userdata['User'] = $this->request->data['User'];
                    if ($this->User->saveUserInfo($userdata)) {
                        $userid = $this->User->getLastInsertId();
                        $this->permission($userid, $this->request->data['Permission']);
                        $this->Session->setFlash(__("Staff member has been added successfully"), 'alert_success');
                        $this->redirect(array('controller' => 'stores', 'action' => 'manageStaff'));
                    } else {
                        $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                        $this->redirect(array('controller' => 'stores', 'action' => 'manageStaff'));
                    }
                } else {
                    $this->Session->setFlash(__("Email already exists"), 'alert_failed');
                    //$this->redirect(array('controller' => 'Stores', 'action' => 'manageStaff'));
                }
            } elseif ($EncrypteduserID) {
                $userID = $this->Encryption->decode($EncrypteduserID);
                $this->request->data = $this->User->currentUserInfo($userID);
                //pr($this->request->data);die;
            }
        }
        if (empty($EncrypteduserID)) {
            $this->_staffList();
        }
    }

    /* ------------------------------------------------
      Function name:staffList()
      Description:Display Staff List of Particular store
      created:27/7/2015
      ----------------------------------------------------- */

    private function _staffList() {
//        if (!$this->Common->checkPermissionByaction($this->params['controller'], "manageStaff")) {
//            $this->Session->setFlash(__("Permission Denied"));
//            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
//        }
        $storeID = $this->Session->read('admin_store_id');
        $value = "";
        $criteria = "User.store_id =$storeID AND User.is_deleted=0 AND User.role_id=3";
        if (!empty($this->params)) {
            if (!empty($this->params->query['keyword'])) {
                $value = trim($this->params->query['keyword']);
            }
            if ($value != "") {
                $criteria .= " AND (User.fname LIKE '%" . $value . "%' OR User.lname LIKE '%" . $value . "%' OR User.email LIKE '%" . $value . "%')";
            }
        }
        $this->paginate = array('conditions' => array($criteria), 'order' => array('User.created' => 'DESC'));
        $userdetail = $this->paginate('User');
        $this->set('list', $userdetail);
        $this->set('keyword', $value);
    }

    /* ------------------------------------------------
      Function name:deleteStaff()
      Description:Delete users
      created:27/7/2015
      ----------------------------------------------------- */

    public function deleteStaff($EncrypteduserID = null) {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], "manageStaff")) {
            $this->Session->setFlash(__("Permission Denied"), 'alert_failed');
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['User']['store_id'] = $this->Session->read('admin_store_id');
        $data['User']['id'] = $this->Encryption->decode($EncrypteduserID);
        $data['User']['is_deleted'] = 1;
        if ($this->User->saveUserInfo($data)) {
            $this->Session->setFlash(__("User deleted"), 'alert_success');
            $this->redirect(array('controller' => 'Stores', 'action' => 'manageStaff'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'Stores', 'action' => 'manageStaff'));
        }
    }

    /* ------------------------------------------------
      Function name:manageStoreSliderImages()
      Description:Manage Images for Somepage slider
      created:27/7/2015
      ----------------------------------------------------- */

    public function manageSliderPhotos() {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], $this->params['action'])) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->layout = "admin_dashboard";
        $errormsg = "";
        $merchantId = $this->Session->read('admin_merchant_id');
        $storeId = $this->Session->read('admin_store_id');
        $this->set('merchantId', $merchantId);
        $this->set('storeId', $storeId);
        if ($this->request->is(array('post', 'put'))) {
            if ($this->data['StoreGallery']['image']['error'] == 0) {
                $response = $this->Common->uploadMenuItemImages($this->data['StoreGallery']['image'], '/sliderImages/', $storeId, 1900, 1026);
            } elseif ($this->data['StoreGallery']['image']['error'] == 4) {
                $response['status'] = true;
                $response['imagename'] = '';
            }
            if (!$response['status']) {
                $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
            } else {
                if ($response['imagename']) {
                    $data['image'] = $response['imagename'];
                }
                //$data['image']=$uniqueImageName;
                $data['store_id'] = $storeId;
                $data['merchant_id'] = $merchantId;
                $data['description'] = $this->request->data["StoreGallery"]["description"];
                $count = $this->StoreGallery->find('count',array('conditions'=>array('store_id'=>$storeId,'merchant_id'=>$merchantId)));
                if(!empty($count)){
                    $data['position'] = $count+1;
                }
                if ($this->StoreGallery->saveStoreSliderImage($data)) {
                    $this->Session->setFlash(__("File successfully uploaded"), 'alert_success');
                    $this->redirect(array('controller' => 'Stores', 'action' => 'manageSliderPhotos'));
                } else {
                    $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                    $this->redirect(array('controller' => 'Stores', 'action' => 'manageSliderPhotos'));
                }
            }
        }
        $this->set('sliderImages', $this->StoreGallery->getStoreSliderImages($storeId, $merchantId));
    }

    /* ------------------------------------------------
      Function name:manageStoreSliderImages()
      Description:Manage Images for Somepage slider
      created:27/7/2015
      ----------------------------------------------------- */

    public function deleteSliderPhoto($EncryptedImageID = null) {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], "manageSliderPhotos")) {
            $this->Session->setFlash(__("Permission denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->layout = "admin_dashboard";
        $imageID = $this->Encryption->decode($EncryptedImageID);
        if ($imageID) {
            $merchantId = $this->Session->read('admin_merchant_id');
            $storeId = $this->Session->read('admin_store_id');
            $this->set('merchantId', $merchantId);
            $this->set('storeId', $storeId);
            $data['id'] = $imageID;
            $data['is_deleted'] = 1;
            if ($this->StoreGallery->saveStoreSliderImage($data)) {
                $this->Session->setFlash(__("Slider photo has been deleted"), 'alert_success');
                $this->redirect($this->referer());
            } else {
                $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                $this->redirect($this->referer());
            }
        }
    }

    /* ------------------------------------------------
      Function name:configuration()
      Description:Manage Images for Somepage slider
      created:27/7/2015
      ----------------------------------------------------- */

    public function configuration() {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], $this->params['action'])) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }

        $this->layout = "admin_dashboard";
        $merchantId = $this->Session->read('admin_merchant_id');
        $storeId = $this->Session->read('admin_store_id');
        $this->set('userid', $this->Session->read('Auth.Admin.id'));
        $this->set('roleid', $this->Session->read('Auth.Admin.role_id'));
        $this->set('storeId', $storeId);
        $this->loadModel("TimeZone");
        $this->set('timeZoneList', $this->TimeZone->find('list', array('fields' => 'timezone_location')));
        $this->loadModel("ThemeColor");
        $this->set('themeColors', $this->ThemeColor->find('list'));

        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $deliverystatus = '';
            $pickstatus = '';
            if (!empty($this->request->data['deliverystatus'])) {
                $deliverystatus = implode(',', array_keys(array_filter($this->request->data['deliverystatus'])));
            }
            if (!empty($this->request->data['pickupstatus'])) {
                $pickstatus = implode(',', array_keys(array_filter($this->request->data['pickupstatus'])));
            }
            if (!empty($this->request->data['Store']['credit_card_type'])) {
                $this->request->data['Store']['credit_card_type'] = implode(",", $this->request->data['Store']['credit_card_type']);
            } else {
                $this->request->data['Store']['credit_card_type'] = '';
            }
            if (!isset($this->request->data['Store']['is_booking_open'])) {
                $this->request->data['Store']['is_booking_open'] = 0;
            }
            if (!isset($this->request->data['Store']['is_take_away'])) {
                $this->request->data['Store']['is_take_away'] = 0;
            }
            if (!isset($this->request->data['Store']['is_delivery'])) {
                $this->request->data['Store']['is_delivery'] = 0;
            }
            if (!isset($this->request->data['Store']['review_page'])) {
                $this->request->data['Store']['review_page'] = 1;
            }
            if (empty($this->request->data['Store']['minimum_order_price'])) {
                $this->request->data['Store']['minimum_order_price'] = 0.00;
            }
            if (empty($this->request->data['Store']['minimum_takeaway_price'])) {
                $this->request->data['Store']['minimum_takeaway_price'] = 0.00;
            }
            if (!empty($this->request->data['Store']['store_theme_id']) && $this->request->data['Store']['store_theme_id'] < 11) {
                $this->request->data['Store']['theme_color_id'] = '';
            }
            $latitude = "";
            $longitude = "";
            if (trim($this->request->data['Store']['address']) && trim($this->request->data['Store']['city']) && trim($this->request->data['Store']['state']) && trim($this->request->data['Store']['zipcode'])) {

                $dlocation = trim($this->request->data['Store']['address']) . " " . trim($this->request->data['Store']['city']) . " " . trim($this->request->data['Store']['state']) . " " . trim($this->request->data['Store']['zipcode']);
                $address2 = str_replace(' ', '+', $dlocation);
                $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . urlencode($address2) . '&sensor=false');
                $output = json_decode($geocode);


                if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                    
                } else {
                    $latitude = @$output->results[0]->geometry->location->lat;
                    $longitude = @$output->results[0]->geometry->location->lng;
                }
            }

            //Background Image Upload
            if (isset($this->data['Store']['back_image'])) {
                if ($this->data['Store']['back_image']['error'] == 0) {
                    $response = $this->Common->uploadMenuItemImages($this->data['Store']['back_image'], '/storeBackground-Image/', $storeId);
                } elseif ($this->data['Store']['back_image']['error'] == 4) {
                    $response['status'] = true;
                    $response['imagename'] = '';
                }

                if (!$response['status']) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    $this->redirect(array('controller' => 'Stores', 'action' => 'configuration'));
                } else {
                    //Item Data
                    if ($response['imagename']) {
                        $this->request->data['Store']['background_image'] = $response['imagename'];
                    }
                }
            }


            //Store Logo Upload
            if (isset($this->data['Store']['store_logophoto'])) {
                if ($this->data['Store']['store_logophoto']['error'] == 0) {
                    $response = $this->Common->uploadMenuItemImages($this->data['Store']['store_logophoto'], '/storeLogo/', $storeId);
                } elseif ($this->data['Store']['store_logophoto']['error'] == 4) {
                    $response['status'] = true;
                    $response['imagename'] = '';
                }

                if (!$response['status']) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    $this->redirect(array('controller' => 'Stores', 'action' => 'configuration'));
                } else {
                    //Item Data
                    if ($response['imagename']) {
                        $this->request->data['Store']['store_logo'] = $response['imagename'];
                    }
                }
            }

            //Store Info Background Image Upload
            if (isset($this->data['Store']['store_info_bgimage'])) {
                if ($this->data['Store']['store_info_bgimage']['error'] == 0) {
                    $response = $this->Common->uploadMenuItemImages($this->data['Store']['store_info_bgimage'], '/storeBackground-Image/', $storeId);
                } elseif ($this->data['Store']['store_info_bgimage']['error'] == 4) {
                    $response['status'] = true;
                    $response['imagename'] = '';
                }

                if (!$response['status']) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    $this->redirect(array('controller' => 'Stores', 'action' => 'configuration'));
                } else {
                    if ($response['imagename']) {
                        $this->request->data['Store']['store_info_bg_image'] = $response['imagename'];
                    }
                }
            }

            if (isset($this->data['Store']['is_store_logo']) && $this->data['Store']['is_store_logo']) {
                $this->request->data['Store']['is_store_logo'] = 2;
            } else {
                $this->request->data['Store']['is_store_logo'] = 1;
            }

            if ($latitude && $longitude) {
                $this->request->data['Store']['latitude'] = $latitude;
                $this->request->data['Store']['logitude'] = $longitude;
            }

            $this->request->data['Store'] = $this->Common->trimValue($this->request->data['Store']);
            $this->loadModel('StoreSetting');
            //prx($this->request->data['StoreSetting']);
            $this->request->data['StoreSetting']['delivery_status'] = $deliverystatus;
            $this->request->data['StoreSetting']['pickup_status'] = $pickstatus;
            $this->StoreSetting->save($this->request->data['StoreSetting']);
            if ($this->Store->saveStoreInfo($this->request->data['Store'])) {
                if (!empty($this->request->data['StoreTax'])) {
                    foreach ($this->request->data['StoreTax'] as $key => $taxvalue) {
                        $taxdata['id'] = $taxvalue['id'];
                        $taxdata['tax_value'] = trim($taxvalue['tax_value']);
                        $this->StoreTax->saveStoreTax($taxdata);
                    }
                }
                
                // Store Tip Data Save
                if (!empty($this->request->data['StoreTip'])) {
                    foreach ($this->request->data['StoreTip'] as $key => $tipvalue) {
                        $tipdata['id'] = $tipvalue['id'];
                        $tipdata['tip_value'] = trim($tipvalue['tip_value']);
                        $tipdata['is_checked'] = trim($tipvalue['is_checked']);
                        $this->StoreTip->saveStoreTip($tipdata);
                    }
                }
                
                $this->Session->write('storeName', $this->request->data['Store']['store_name']);
                $this->Session->write('admin_time_zone_id', $this->request->data['Store']['time_zone_id']);
                $this->Session->setFlash(__("Store Configuration details successfully Updated"), 'alert_success');
                $this->redirect(array('controller' => 'Stores', 'action' => 'configuration'));
            } else {
                $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                $this->redirect(array('controller' => 'Stores', 'action' => 'configuration'));
            }
        }
        $fontOptions = $this->StoreFont->getFonts();
        $this->set('fontOptions', $fontOptions);

        $themeOptions = $this->StoreTheme->getThemes();
        $this->set('themeOptions', $themeOptions);
        $this->loadModel('StoreSetting');
        $storeSetting = $this->StoreSetting->find('first', array('conditions' => array('store_id' => $storeId)));
        if (!empty($storeSetting)) {
            $this->request->data['StoreSetting'] = $storeSetting['StoreSetting'];
            $this->request->data['StoreSetting']['pickup_status'] = array();
            $this->request->data['StoreSetting']['delivery_status'] = array();
            if (!empty($storeSetting['StoreSetting']['delivery_status'])) {
                $this->request->data['StoreSetting']['delivery_status'] = explode(',', $storeSetting['StoreSetting']['delivery_status']);
            }
            if (!empty($storeSetting['StoreSetting']['pickup_status'])) {
                $this->request->data['StoreSetting']['pickup_status'] = explode(',', $storeSetting['StoreSetting']['pickup_status']);
            }
        }
        $storeInfo = $this->Store->fetchStoreDetail($storeId, $merchantId);
        if (!empty($storeInfo)) {
            $this->request->data['Store'] = $storeInfo['Store'];
        }
        $storeTax = $this->StoreTax->storeTaxInfo($storeId);
        if (!empty($storeTax)) {
            $this->request->data['StoreTax'] = $storeTax;
        } else {
            $createStoretax = array();
            for ($i = 1; $i <= 4; $i++) {
                $createStoretax['store_id'] = $storeId;
                $createStoretax['tax_name'] = "Tax" . $i;
                $createStoretax['tax_value'] = '';
                $createStoretax['id'] = '';
                $this->StoreTax->saveStoreTax($createStoretax);
            }
            $storeTax = $this->StoreTax->storeTaxInfo($storeId);
            $this->request->data['StoreTax'] = $storeTax;
        }
        
        // For Store Tip
        $storeTax = $this->StoreTip->storeTipInfo($storeId);
        if (!empty($storeTax)) {
            $this->request->data['StoreTip'] = $storeTax;
        } else {
            $createStoretax = array();
            for ($i = 1; $i <= 4; $i++) {
                $createStoretax['store_id']     = $storeId;
                $createStoretax['tip_name']     = "Tip" . $i;
                $createStoretax['tip_value']    = '';
                $createStoretax['is_checked']   = 0;
                $createStoretax['id']           = '';
                $this->StoreTip->saveStoreTip($createStoretax);
            }
            $storeTax = $this->StoreTip->storeTipInfo($storeId);
            $this->request->data['StoreTip'] = $storeTax;
        }
        
        
        $this->loadModel('StoreSetting');
        $storeSetting = $this->StoreSetting->findByStoreId($storeId);
        $this->loadModel('OrderStatus');
        $deliveryType = array('1', '3');
        $pickType = array('1', '2');
        $pickupStatus = $this->OrderStatus->getStatus($pickType);
        $deliveryStatus = $this->OrderStatus->getStatus($deliveryType);
        //$this->set('storeSetting', $storeSetting);
        $this->set(compact('pickupStatus', 'storeSetting', 'deliveryStatus'));
    }

    /* ------------------------------------------------
      Function name:manageTimings()
      Description:Manage Store Open and close Timings
      created:27/7/2015
      ----------------------------------------------------- */

    public function manageTimings() {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], $this->params['action'])) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }

        $this->layout = "admin_dashboard";
        $merchantId = $this->Session->read('admin_merchant_id');
        $storeId = $this->Session->read('admin_store_id');
        $this->set('userid', $this->Session->read('Auth.Admin.id'));
        $this->set('roleid', $this->Session->read('Auth.Admin.role_id'));
        $this->set('storeId', $storeId);
        if ($this->request->data) { //pr($this->request->data); //die;
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
        $end = "23:59";
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
        // pr($availabilityInfo);die;
        //pr($availabilityInfo);die;
        $daysarr = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $this->set('daysarr', $daysarr);
    }

    /* ------------------------------------------------
      Function name:addClosedDate()
      Description:delete closed date from list
      created:29/7/2015
      ----------------------------------------------------- */

    public function addClosedDate() {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], "manageTimings")) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->autoRender = false;
        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $data['store_id'] = $this->Session->read('admin_store_id');
            $holidayDate = $this->Dateform->formatDate($this->request->data['StoreHoliday']['holiday_date']);
            $data['holiday_date'] = trim($holidayDate);
            $data['description'] = trim($this->request->data['StoreHoliday']['description']);
            if ($this->StoreHoliday->storeHolidayNotExists($holidayDate, $data['store_id'])) {
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
      Function name:deleteHoliday()
      Description:delete closed date from list
      created:29/7/2015
      ----------------------------------------------------- */

    public function deleteHoliday($EncryptedHolidayID = null) {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], "manageTimings")) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->autoRender = false;
        $HolidayID = $this->Encryption->decode($EncryptedHolidayID);
        if ($HolidayID) {
            $data['id'] = $HolidayID;
            $data['is_deleted'] = $HolidayID;
            if ($this->StoreHoliday->saveStoreHolidayInfo($data)) {
                $this->Session->setFlash(__("Closed Holiday Successfully deleted"), 'alert_success');
                $this->redirect(array('controller' => 'Stores', 'action' => 'manageTimings'));
            } else {
                $this->Session->setFlash(__("Some Problem occured"), 'alert_failed');
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
        if (!$this->Common->checkPermissionByaction($this->params['controller'], "manageTimings")) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
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

    /* ------------------------------------------------
      Function name:forgetPassword()
      Description:For forget password
      created:22/7/2015
      ----------------------------------------------------- */

    public function forgetPassword() {
        $this->layout = "store_login";
        $this->autorender = false;
        if (!empty($this->data)) {
            $roleId = "";
            $email = $this->request->data['User']['email'];
            $roleId = $this->request->data['User']['role_id'];
            $merchantId = $this->Session->read('merchant_id');
            if (!$merchantId) {
                $merchantId = "";
            }
            $storeId = $this->Session->read('store_id');
            if (!$storeId) {
                $storeId = '';
            }
            $this->loadModel('Store');
            $storeEmail = $this->Store->fetchStoreDetail($storeId);
            $userEmail = $this->User->checkForgetEmail($roleId, $storeId, $merchantId, $email); //Calling function on model for checking the email
            if (!empty($userEmail)) {
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
                    $url = HTTP_ROOT . 'users/resetPassword/' . $token . '/3';
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
                            $this->request->data['User']['id'] = $userEmail['User']['id'];

                            $this->request->data['User']['forgot_token'] = $token;
                            $this->User->saveUserInfo($this->data['User']);
                            $this->Session->setFlash(__("Please check your email for reset new password"), 'alert_success');
                            $this->redirect(array('controller' => 'stores', 'action' => 'forgetPassword'));
                        }
                    } catch (Exception $e) {
                        $this->Session->setFlash("Please try after some time", $e->getMessage());
                        $this->redirect(array('controller' => 'Stores', 'action' => 'forgetPassword'));
                    }
                }

                ////////////Dynamic SMTP//////////
            } else {
                $this->Session->setFlash("Please enter correct email.", 'alert_failed');
                $this->redirect(array('controller' => 'Stores', 'action' => 'forgetPassword'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:myProfile()
      Description:This section will manage the profile of the user for Store Admin
      created:22/7/2015
      ----------------------------------------------------- */

    public function myProfile($encrypted_storeId = null, $encrypted_merchantId = null) {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], null)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->layout = "admin_dashboard";
        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId'));
        $userResult = $this->User->currentUserInfo($this->Session->read('Auth.Admin.id'));
        $roleId = $userResult['User']['role_id'];
        $this->User->set($this->request->data);
        if (isset($this->request->data['User']['changepassword'])) {
            if (!($this->request->data['User']['changepassword'])) {
                $this->User->validator()->remove('password');
                $this->User->validator()->remove('password_match');
            }
        }
        if ($this->User->validates()) {
            if ($this->request->is('post')) {

                //$dbformatDate=$this->Dateform->formatDate($this->data['User']['dateOfBirth']);
                //$this->request->data['User']['dateOfBirth']=$dbformatDate;
                if ($this->request->data['User']['changepassword'] == 1) {
                    $oldPassword = AuthComponent::password($this->data['User']['oldpassword']);
                    if ($oldPassword != $userResult['User']['password']) {
                        $this->Session->setFlash(__("Please Enter correct old password"), 'alert_failed');
                        $this->redirect(array('controller' => 'Stores', 'action' => 'myProfile', $encrypted_storeId, $encrypted_merchantId));
                    }
                }
                $this->User->id = $this->Session->read('Auth.Admin.id');
                if ($this->User->saveUserInfo($this->request->data['User'])) {
                    $this->Session->setFlash(__("Profile has been updated successfully"), 'alert_success');
                    $this->redirect(array('controller' => 'Stores', 'action' => 'myProfile', $encrypted_storeId, $encrypted_merchantId));
                } else {
                    $this->Session->setFlash(__("Profile not updated successfully"), 'alert_failed');
                    $this->redirect(array('controller' => 'Stores', 'action' => 'myProfile', $encrypted_storeId, $encrypted_merchantId));
                }
            }
        }
        $this->set(compact('roleId'));
        $this->request->data['User'] = $userResult['User'];
        //$this->request->data['User']['dateOfBirth']=$this->Dateform->us_format($userResult['User']['dateOfBirth']);
    }

    /* ------------------------------------------------
      Function name:activateStaff()savePermission
      Description:Delete users
      created:27/7/2015
      ----------------------------------------------------- */

    public function activateStaff($EncrypteduserID = null, $status = 0) {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], "manageStaff")) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['User']['store_id'] = $this->Session->read('admin_store_id');
        $data['User']['id'] = $this->Encryption->decode($EncrypteduserID);
        $data['User']['is_active'] = $status;
        if ($this->User->saveUserInfo($data)) {
            if ($status) {
                $SuccessMsg = "Staff Activated";
            } else {
                $SuccessMsg = "Staff Deactivated and member will not able to log in to system";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'stores', 'action' => 'manageStaff'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'stores', 'action' => 'manageStaff'));
        }
    }

    /* ------------------------------------------------
      Function name:checkStoreEmail()
      Description:For logout of the user
      created:22/7/2015
      ----------------------------------------------------- */

    public function checkStoreEmail($roleId = null) {
        $this->autoRender = false;
        if ($_GET) {
            $emailEntered = $_GET['data']['User']['email'];
            $storeId = "";
            $merchantId = "";
            $storeId = $this->Session->read('admin_store_id');
            $merchantId = $this->Session->read('admin_merchant_id');
//           $emailStatus = $this->User->storeemailExists($emailEntered, $roleId, $storeId);
            $emailStatus = $this->User->emailExistsStore($emailEntered, $roleId, $merchantId);
            echo json_encode($emailStatus);
        }
    }

    public function permission($userid = null, $permission = null) {
        $this->autoRender = false;
        //pr($permission);die;
        //$userid=52;
        if ($permission) {
            $this->Permission->DeleteAllPermission($userid);
            $permissiondata = array_filter($permission['tab_id']);
            //pr($permissiondata);die;
            foreach ($permissiondata as $pkey => $tab_id) {
                $permissionid = $this->Permission->checkPermissionExists($tab_id, $userid);
                if ($permissionid) {
                    $data['id'] = $permissionid['Permission']['id'];
                } else {
                    $data['id'] = '';
                }
                $data['tab_id'] = $tab_id;
                $data['user_id'] = $userid;
                $data['is_deleted'] = 0;
                $this->Permission->savePermission($data);
            }
        }
    }

    /* ------------------------------------------------
      Function name:deleteStoreBackgroundPhoto()
      Description:delete Images store background
      created:27/7/2015
      ----------------------------------------------------- */

    public function deleteStoreBackgroundPhoto($EncryptedStoreID = null) {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], "configuration")) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->layout = "admin_dashboard";
        $storeId = $this->Encryption->decode($EncryptedStoreID);
        if ($storeId) {
            $merchantId = $this->Session->read('admin_merchant_id');
            $storeId = $this->Session->read('admin_store_id');
            $data['id'] = $storeId;
            $data['background_image'] = '';
            if ($this->Store->saveStoreInfo($data)) {
                $this->Session->setFlash(__("Background Photo successfully Deleted"), 'alert_success');
                $this->redirect(array('controller' => 'Stores', 'action' => 'configuration'));
            } else {
                $this->Session->setFlash(__("Some Problem occured"), 'alert_failed');
                $this->redirect(array('controller' => 'Stores', 'action' => 'configuration'));
            }
        }
    }

    public function deleteStoreLogo($EncryptedStoreID = null) {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], "configuration")) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->layout = "admin_dashboard";
        $storeId = $this->Encryption->decode($EncryptedStoreID);
        if ($storeId) {
            $merchantId = $this->Session->read('admin_merchant_id');
            $storeId = $this->Session->read('admin_store_id');
            $data['id'] = $storeId;
            $data['store_logo'] = '';
            $data['is_store_logo'] = 0;
            if ($this->Store->saveStoreInfo($data)) {
                $this->Session->setFlash(__("Store Logo successfully Deleted"), 'alert_success');
                $this->redirect(array('controller' => 'Stores', 'action' => 'configuration'));
            } else {
                $this->Session->setFlash(__("Some Problem occured"), 'alert_failed');
                $this->redirect(array('controller' => 'Stores', 'action' => 'configuration'));
            }
        }
    }

    public function deleteStoreInfoBgImage($EncryptedStoreID = null) {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], "configuration")) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->layout = "admin_dashboard";
        $storeId = $this->Encryption->decode($EncryptedStoreID);
        if ($storeId) {
            $merchantId = $this->Session->read('admin_merchant_id');
            $storeId = $this->Session->read('admin_store_id');
            $data['id'] = $storeId;
            $data['store_info_bg_image'] = '';
            if ($this->Store->saveStoreInfo($data)) {
                $this->Session->setFlash(__("Store info background image successfully Deleted"), 'alert_success');
                $this->redirect(array('controller' => 'Stores', 'action' => 'configuration'));
            } else {
                $this->Session->setFlash(__("Some Problem occured"), 'alert_failed');
                $this->redirect(array('controller' => 'Stores', 'action' => 'configuration'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:socialMedia()
      Description:social media configuratuion
      ----------------------------------------------------- */

    public function socialMedia() {
        $this->layout = "admin_dashboard";
        $this->loadModel('SocialMedia');
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $this->request->data['SocialMedia']['store_id'] = $storeId; // Store Id
            $this->request->data['SocialMedia']['merchant_id'] = $merchantId;
            // pr($this->request->data);die;
            if ($this->SocialMedia->saveSocialMedia($this->request->data)) {
                $this->Session->setFlash(__("Social media url updated successfully."), 'alert_success');
                $this->redirect(array('controller' => 'Stores', 'action' => 'socialMedia'));
            } else {
                $this->Session->setFlash(__("Some Problem occured"), 'alert_failed');
                $this->redirect(array('controller' => 'Stores', 'action' => 'socialMedia'));
            }
        }
        $socialInfo = $this->SocialMedia->fetchSocialMediaDetail($storeId);
        if (!empty($socialInfo)) {
            $this->request->data = $socialInfo;
        }
    }

    /* ------------------------------------------------
      Function name:activateSliderImage()
      Description:Active/deactive slider images
      created:4/10/2016
      ----------------------------------------------------- */

    public function activateSliderImage($EncryptedSliderID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $this->loadModel('StoreGallery');
        $data['StoreGallery']['store_id'] = $this->Session->read('admin_store_id');
        $data['StoreGallery']['id'] = $this->Encryption->decode($EncryptedSliderID);
        $data['StoreGallery']['is_active'] = $status;
        if ($this->StoreGallery->save($data)) {
            if ($status) {
                $SuccessMsg = "Image Activated";
            } else {
                $SuccessMsg = "Image Deactivated";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'stores', 'action' => 'manageSliderPhotos'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'stores', 'action' => 'manageSliderPhotos'));
        }
    }

    /* ------------------------------------------------
      Function name:editSliderImage()
      Description:Edit slider details
      created:26/10/2016
      ----------------------------------------------------- */

    public function editSliderImage($EncryptStoreGalleryID) {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $StoreGalleryId = $this->Encryption->decode($EncryptStoreGalleryID);
        $this->loadModel('StoreGallery');
        $StoreGalleryDetail = $this->StoreGallery->findById($StoreGalleryId);
        if ($this->request->is(array('post', 'put')) && !empty($StoreGalleryDetail)) {
            if ($this->data['StoreGallery']['image']['error'] == 0) {
                $response = $this->Common->uploadMenuItemImages($this->data['StoreGallery']['image'], '/sliderImages/', $storeId, 1900, 1026);
                if ($response['imagename']) {
                    $data['image'] = $response['imagename'];
                }
            } elseif ($this->data['StoreGallery']['image']['error'] == 4) {
                $response['status'] = true;
            }
            if (!$response['status']) {
                $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
            } else {
                $data['id'] = $StoreGalleryId;
                $data['store_id'] = $storeId;
                $data['merchant_id'] = $merchantId;
                $data['description'] = $this->request->data["StoreGallery"]["description"];
                if ($this->StoreGallery->saveStoreSliderImage($data)) {
                    $this->Session->setFlash(__("Update Successfully."), 'alert_success');
                    $this->redirect(array('controller' => 'Stores', 'action' => 'manageSliderPhotos'));
                } else {
                    $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                    $this->redirect(array('controller' => 'Stores', 'action' => 'manageSliderPhotos'));
                }
            }
        }
        $this->request->data = $StoreGalleryDetail;
    }

    public function deleteSliderPhotoName($EncryptedImageID) {
        $this->layout = "admin_dashboard";
        $imageID = $this->Encryption->decode($EncryptedImageID);
        if ($imageID) {
            $merchantId = $this->Session->read('admin_merchant_id');
            $storeId = $this->Session->read('admin_store_id');
            $this->set('merchantId', $merchantId);
            $this->set('storeId', $storeId);
            $data['id'] = $imageID;
            $data['image'] = '';
            $data['is_active'] = 0;
            if ($this->StoreGallery->saveStoreSliderImage($data)) {
                $this->Session->setFlash(__("Slider image has been deleted"), 'alert_success');
                $this->redirect($this->referer());
            } else {
                $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                $this->redirect($this->referer());
            }
        }
    }

    public function saveTermsAndPolicies($EncryptTermsAndPolicyID = null) {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $this->loadModel('TermsAndPolicy');
        if ($this->request->is(array('post', 'put')) && !empty($storeId)) {
            $StoreDetail = $this->Store->find('first', array('conditions' => array('Store.id' => $storeId), 'fields' => array('Store.id', 'Store.merchant_id')));
            $this->request->data['TermsAndPolicy']['store_id'] = $storeId;
            $this->request->data['TermsAndPolicy']['merchant_id'] = $StoreDetail['Store']['merchant_id'];
            if (empty($this->request->data['TermsAndPolicy']['id'])) {
                $this->TermsAndPolicy->create();
            }
            if ($this->TermsAndPolicy->save($this->request->data)) {
                $this->Session->setFlash(__("Update Successfully."), 'alert_success');
            } else {
                $this->Session->setFlash(__("Something went wrong."), 'alert_failed');
            }
            if (!empty($EncryptTermsAndPolicyID)) {
                $this->redirect(array('controller' => 'contents', 'action' => 'pageList'));
            }
        }
        $this->request->data = $this->TermsAndPolicy->findByStoreId($storeId);
    }

    public function getTermsAndPolicyData() {
        $this->layout = false;
        $this->autoRender = false;
        $this->loadModel('TermsAndPolicy');
        if ($this->request->is('ajax') && !empty($this->request->data['type'])) {
            $storeId = $this->Session->read('store_id');
            $type = $this->request->data['type'];
            if ($type == 'Term') {
                $getContent = 'terms_and_conditions';
                $heading = 'Terms & Conditions';
            } else {
                $getContent = 'privacy_policy';
                $heading = 'Privacy Policy';
            }
            $tandcData = $this->TermsAndPolicy->findByStoreId($storeId, array($getContent));
            if (!empty($tandcData)) {
                if ($type == 'Term') {
                    $tandcData = $tandcData['TermsAndPolicy']['terms_and_conditions'];
                } else {
                    $tandcData = $tandcData['TermsAndPolicy']['privacy_policy'];
                }
            } else {
                $tandcData = "";
            }
            $this->set(compact('tandcData', 'heading'));
            $this->render('/Elements/hquser/term_and_policy');
        }
    }

    /* ------------------------------------------------
      Function name:manageStoreSliderImages()
      Description:Manage Images for Somepage slider
      created:27/7/2015
      ----------------------------------------------------- */

    public function manageThemeImages() {
        $this->layout = "admin_dashboard";
        $this->loadModel('HomeImage');
        $errormsg = "";
        $storeId = $this->Session->read('admin_store_id');
        $this->set('storeId', $storeId);
        if ($this->request->is(array('post', 'put'))) {
            $tData = $this->HomeImage->getStoreThemeImages($storeId);
            //prx($this->data);
            if ($this->data['HomeImage']['contact_left']['error'] == 0) {
                $response = $this->Common->uploadMenuItemImages($this->data['HomeImage']['contact_left'], '/sliderImages/', $storeId, 600, 534);
                if (!$response['status']) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    $this->redirect($this->referer());
                }
                if ($response['imagename']) {
                    $data['contact_left'] = $response['imagename'];
                }
            } else {
                if (!empty($tData['HomeImage']['contact_left'])) {
                    $data['contact_left'] = $tData['HomeImage']['contact_left'];
                }
            }
            if ($this->data['HomeImage']['contact_right']['error'] == 0) {
                $response = $this->Common->uploadMenuItemImages($this->data['HomeImage']['contact_right'], '/sliderImages/', $storeId, 400, 284);
                if (!$response['status']) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    $this->redirect($this->referer());
                }
                if ($response['imagename']) {
                    $data['contact_right'] = $response['imagename'];
                }
            } else {
                if (!empty($tData['HomeImage']['contact_right'])) {
                    $data['contact_right'] = $tData['HomeImage']['contact_right'];
                }
            }
            if ($this->data['HomeImage']['opening_left']['error'] == 0) {
                $response = $this->Common->uploadMenuItemImages($this->data['HomeImage']['opening_left'], '/sliderImages/', $storeId, 400, 284);
                if (!$response['status']) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    $this->redirect($this->referer());
                }
                if ($response['imagename']) {
                    $data['opening_left'] = $response['imagename'];
                }
            } else {
                if (!empty($tData['HomeImage']['opening_left'])) {
                    $data['opening_left'] = $tData['HomeImage']['opening_left'];
                }
            }
            if ($this->data['HomeImage']['opening_right']['error'] == 0) {
                $response = $this->Common->uploadMenuItemImages($this->data['HomeImage']['opening_right'], '/sliderImages/', $storeId, 600, 534);
                if (!$response['status']) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    $this->redirect($this->referer());
                }
                if ($response['imagename']) {
                    $data['opening_right'] = $response['imagename'];
                }
            } else {
                if (!empty($tData['HomeImage']['opening_right'])) {
                    $data['opening_right'] = $tData['HomeImage']['opening_right'];
                }
            }
            $data['id'] = $tData['HomeImage']['id'];
            if (!empty($tData['HomeImage']['id'])) {
                $this->HomeImage->saveStoreThemeImage($data);
                $this->redirect($this->referer());
            }
        }
        $tData = $this->HomeImage->getStoreThemeImages($storeId);
        $this->set('themeImages', $tData);
        if (empty($tData)) {
            $data['store_id'] = $storeId;
            $this->HomeImage->saveStoreThemeImage($data);
        }
    }

    public function deleteThemeImage($EncryptedImageID, $column) {
        $this->layout = "admin_dashboard";
        $imageID = $this->Encryption->decode($EncryptedImageID);
        if ($imageID) {
            $this->loadModel('HomeImage');
            $data['id'] = $imageID;
            $data[$column] = '';
            if ($this->HomeImage->saveStoreThemeImage($data)) {
                $this->Session->setFlash(__("Image has been deleted"), 'alert_success');
                $this->redirect($this->referer());
            } else {
                $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                $this->redirect($this->referer());
            }
        }
    }

    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('User');
            $storeID = $this->Session->read('admin_store_id');
            $criteria = "User.store_id =$storeID AND User.is_deleted=0 AND User.role_id=3";
            $searchData = $this->User->find('all', array('fields' => array('User.id', 'User.fname', 'User.lname', 'User.email'), 'conditions' => array('OR' => array('User.fname LIKE' => '%' . $_GET['term'] . '%', 'User.lname LIKE' => '%' . $_GET['term'] . '%', 'User.email LIKE' => '%' . $_GET['term'] . '%'), $criteria), 'group' => 'User.fname'));
            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    $new_array[] = array('label' => $val['User']['fname'], 'value' => $val['User']['fname'], 'desc' => $val['User']['fname'] . " " . $val['User']['lname'] . '-' . $val['User']['email']);
                };
            }
            echo json_encode($new_array);
        } else {
            exit;
        }
    }

    public function manageTemplateCss() {
        $this->layout = "admin_dashboard";
        $this->loadModel('StoreStyle');
        $storeId = $this->Session->read('admin_store_id');
        if ($this->request->is(array('post', 'put'))) {
            $styleExist = $this->StoreStyle->find('first', array('conditions' => array('store_id' => $storeId, 'navigation' => $this->request->data['StoreStyle']['navigation'], 'store_theme_id' => $this->request->data['StoreStyle']['store_theme_id'])));
            if (!empty($styleExist)) {
                $this->request->data['StoreStyle']['id'] = $styleExist['StoreStyle']['id'];
            }
            if (!empty($this->request->data['StoreStyle']['id'])) {
                $this->StoreStyle->create();
            }
            $this->request->data['StoreStyle']['store_id'] = $storeId;
            $this->request->data['StoreStyle']['merchant_id'] = $this->Session->read('admin_merchant_id');
            if ($this->StoreStyle->save($this->request->data)) {
                if ($this->StoreStyle->getLastInsertId()) {
                    $condition[] = array('id' => $this->StoreStyle->getLastInsertId());
                } else {
                    $condition[] = array('id' => $this->request->data['StoreStyle']['id']);
                }
                $this->Session->setFlash(__("Css update successfully."), 'alert_success');
            } else {
                $condition[] = array('id' => (!empty($this->request->data['StoreStyle']['id'])) ? $this->request->data['StoreStyle']['id'] : '');
                $this->Session->setFlash(__("Please try again."), 'alert_failed');
            }
        }
        $this->loadModel("ThemeColor");
        $this->set('themeColors', $this->ThemeColor->find('list'));
        $themeOptions = $this->StoreTheme->getThemes();
        $this->set('themeOptions', $themeOptions);
        $condition[] = array('store_id' => $storeId);
        $data = $this->StoreStyle->find('first', array('conditions' => $condition));
        $this->request->data = $data;
    }

    public function getStoreAdminStyle() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is('ajax') && !empty($this->request->data['storeStyleThemeId']) && !empty($this->request->data['storeStyleNavigation'])) {
            $this->loadModel('StoreStyle');
            $storeId = $this->Session->read('admin_store_id');
            $data = $this->StoreStyle->find('first', array('conditions' => array('store_id' => $storeId, 'store_theme_id' => $this->request->data['storeStyleThemeId'], 'navigation' => $this->request->data['storeStyleNavigation'])));
            if (!empty($data)) {
                $html = '';
                $viewObject = new View($this, false);
                $html .= $viewObject->Form->input('StoreStyle.css', array(
                    'type' => 'textarea',
                    'value' => $data['StoreStyle']['css'],
                    'label' => false,
                    'div' => false,
                    'rows' => 20,
                    'class' => 'form-control'
                ));
                $html .= $viewObject->Form->input('StoreStyle.id', array(
                    'type' => 'hidden',
                    'value' => $data['StoreStyle']['id'],
                ));
                echo $html;
            }
        }
    }

    public function homePageModal() {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $this->loadModel('HomeModal');
        if ($this->request->is(array('post', 'put'))) {
            $this->request->data['HomeModal']['added_from'] = 1;
            $this->request->data['HomeModal']['store_id'] = $storeId;
            if ($this->HomeModal->save($this->request->data)) {
                $this->Session->setFlash(__("Info update successfly."), 'alert_success');
            } else {
                $this->Session->setFlash(__("Unable to save details, Please try again."), 'alert_failed');
            }
            $this->redirect($this->referer());
        }
        $this->request->data = $this->HomeModal->findByStoreId($storeId);
    }
    
    /* ------------------------------------------------
      Function name:updateStoreImageOrder()
      Description: Update the display order for Image in slider
      created Date:26/09/2017
      created By:
      ----------------------------------------------------- */

    public function updateStoreImageOrder() {
        $this->autoRender = false;
        if (isset($_GET) && !empty($_GET)) {
            foreach (array_filter($_GET) as $key => $val) {
                if (!empty($val) && !empty($key)) {
                    $this->StoreGallery->updateAll(array('position' => $val), array('id' => $this->Encryption->decode($key)));
                }
            }
        }
    }

}
