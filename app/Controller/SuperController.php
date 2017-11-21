<?php

App::uses('SupersAppController', 'Controller');

class SuperController extends SupersAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Paginator', 'Common', 'Dateform');
    public $helper = array('Encryption', 'Paginator', 'Form', 'DateformHelper', 'Common');
    public $uses = array('MerchantPayment', 'MainSiteSetting', 'Plan', 'User', 'StoreGallery', 'Store', 'MerchantStoreRequest', 'Category', 'Tab', 'Permission', 'Merchant', 'StoreAvailability', 'Module', 'OrderPreference', 'SpecialDay', 'DefaultSpecialDay');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('forgetPassword');
    }

    public function index() {
        $this->autoRender = false;
        $this->redirect(array('controller' => 'super', 'action' => 'login'));
    }

    public function login() {
        $this->layout = "super_login";
        $this->set('title', 'Sign in');
        if ($this->request->is('post')) {
            $this->User->set($this->request->data);
            if ($this->User->validates()) {
                if ($this->data['User']['remember'] == 1) {
                    // Cookie is valid for 7 days
                    $this->Cookie->write('Auth.superemail', $this->data['User']['email'], false, 604800);
                    $this->Cookie->write('Auth.superpassword', $this->data['User']['password'], false, 604800);
                    $this->set('cookies', '1');
                    unset($this->request->data['User']['remember_me']);
                } else {
                    $this->Cookie->delete('Auth');
                    $this->Cookie->delete('Auth');
                }

                if ($this->Auth->login()) {
                    $superUserId = $this->Session->read('Auth.Super.id');
                    $superAdminData = $this->User->currentUserInfo($superUserId);
                    $roleId = $this->Session->read('Auth.Super.role_id'); // ROLE OF THE USER [2=>Merchant]
                    $this->Session->write('login_date_time', date('Y-m-d H:i:s'));
                    //$this->Session->setFlash("<div class='alert_success'>".LOGINSUCCESSFULL."</div>");
                    if ($roleId == 1) {  // Store admin will redirect to his related dashboard
                        $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
                    } else {
                        $this->redirect(array('controller' => 'super', 'action' => 'logout'));
                    }
                } else {
                    $this->Session->setFlash(__("Invalid email or password, try again"), 'alert_failed');
                }
            }
        } elseif ($this->Auth->login()) {
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        } else {
            $UserId = $this->Session->read('Auth.Super.id');
            if ($UserId) {
                $this->redirect(array('controller' => 'super', 'action' => 'logout'));
            }
            $this->set('rem', $this->Cookie->read('Auth.email'));
            if ($this->Cookie->read('Auth.email')) {
                $this->request->data['User']['email'] = $this->Cookie->read('Auth.superemail');
                $this->request->data['User']['password'] = $this->Cookie->read('Auth.superpassword');
            }
        }
    }

    /* ------------------------------------------------
      7Function name:dashboard()
      Description:Dash Board of Store Admin
      created:27/7/2015
      ----------------------------------------------------- */

    public function dashboard() {
        $this->layout = "super_dashboard";
        $roleId = $this->Session->read('Auth.Super.role_id'); // ROLE OF THE USER [2=>Merchant]
        if ($roleId != 1) {  // Store admin will redirect to his related dashboard
            $this->redirect(array('controller' => 'super', 'action' => 'logout'));
        }
    }

    /* ------------------------------------------------
      Function name:logout()
      Description:For logout of the user
      created:27/7/2015
      ----------------------------------------------------- */

    public function logout() {

        $this->Session->delete('Auth.Super');
        $this->redirect(array('controller' => 'super', 'action' => 'login'));
        //return $this->redirect($this->Auth->logout());
    }

    /* ------------------------------------------------
      Function name:myProfile()
      Description:This section will manage the profile of the user for Store Admin
      created:22/7/2015
      ----------------------------------------------------- */

    public function myProfile() {
        $this->layout = "super_dashboard";
        $userResult = $this->User->currentUserInfo($this->Session->read('Auth.Super.id'));
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
                        $this->redirect(array('controller' => 'super', 'action' => 'myProfile'));
                    }
                }
                $this->User->id = $this->Session->read('Auth.Super.id');
                if ($this->User->saveUserInfo($this->request->data['User'])) {
                    $this->Session->setFlash(__("Profile has been updated successfully"), 'alert_success');
                    $this->redirect(array('controller' => 'super', 'action' => 'myProfile'));
                } else {
                    $this->Session->setFlash(__("Profile not updated successfully"), 'alert_failed');
                    $this->redirect(array('controller' => 'super', 'action' => 'myProfile'));
                }
            }
        }
        $this->set(compact('roleId'));
        $this->request->data['User'] = $userResult['User'];
        //$this->request->data['User']['dateOfBirth']=$this->Dateform->us_format($userResult['User']['dateOfBirth']);
    }

    /* ------------------------------------------------
      Function name:dashboard()
      Description:Dash Board of Store Admin
      created:27/7/2015
      ----------------------------------------------------- */

    public function manageStaff($EncrypteduserID = null) {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'manageStaff', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";
        $userResult = $this->User->currentUserInfo($this->Session->read('Auth.Super.id'));
        $loginuserid = $this->Session->read('Auth.Super.id');
        $this->set('loginuserid', $loginuserid);
        $roleId = $userResult['User']['role_id'];
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
                    $userdata['User'] = $this->request->data['User'];
                    if ($this->User->saveUserInfo($userdata)) {
                        $this->permission($this->request->data['User']['id'], $this->request->data['Permission']);
                        $this->Session->setFlash(__("Staff member details has been updated successfully"), 'alert_success');
                        $this->redirect(array('controller' => 'super', 'action' => 'manageStaff'));
                    } else {
                        $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                        $this->redirect(array('controller' => 'super', 'action' => 'manageStaff'));
                    }
                } elseif ($this->User->superemailExists($this->request->data['User']['email'], $roleId) && $this->request->data['User']['id'] == '') {
                    $userdata['User'] = $this->request->data['User'];
                    if ($this->User->saveUserInfo($userdata)) {
                        $userid = $this->User->getLastInsertId();
                        $this->permission($userid, $this->request->data['Permission']);
                        $this->Session->setFlash(__("Staff member has been added successfully"), 'alert_success');
                        $this->redirect(array('controller' => 'super', 'action' => 'manageStaff'));
                    } else {
                        $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                        $this->redirect(array('controller' => 'super', 'action' => 'manageStaff'));
                    }
                } else {
                    $this->Session->setFlash(__("Email already exists"), 'alert_failed');
                    //$this->redirect(array('controller' => 'Stores', 'action' => 'manageStaff'));
                }
            } elseif ($EncrypteduserID) {
                $userID = $this->Encryption->decode($EncrypteduserID);
                $this->request->data = $this->User->currentUserInfo($userID);
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
//        $loginuserid = $this->Session->read('Auth.Super.id');
//        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'manageStaff', $loginuserid)) {
//            $this->Session->setFlash(__("Permission Denied"));
//            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
//        }
        $value = "";
        $criteria = " User.is_deleted=0 AND User.role_id=1";
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
      Function name:activateStaff
      Description:Delete users
      created:27/7/2015
      ----------------------------------------------------- */

    public function activateStaff($EncrypteduserID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "super_dashboard";
        $data['User']['id'] = $this->Encryption->decode($EncrypteduserID);
        $data['User']['is_active'] = $status;
        if ($this->User->saveUserInfo($data)) {
            if ($status) {
                $SuccessMsg = "Staff Activated";
            } else {
                $SuccessMsg = "Staff Deactivated and member will not able to log in to system";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'super', 'action' => 'manageStaff'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'super', 'action' => 'manageStaff'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteStaff()
      Description:Delete users
      created:27/7/2015
      ----------------------------------------------------- */

    public function deleteStaff($EncrypteduserID = null) {
        $this->autoRender = false;
        $this->layout = "super_dashboard";
        $data['User']['id'] = $this->Encryption->decode($EncrypteduserID);
        $data['User']['is_deleted'] = 1;
        if ($this->User->saveUserInfo($data)) {
            $this->Session->setFlash(__("User deleted"), 'alert_success');
            $this->redirect(array('controller' => 'super', 'action' => 'manageStaff'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'super', 'action' => 'manageStaff'));
        }
    }

    /* ------------------------------------------------
      Function name:permission()
      Description:Update User permissions
      created:01/8/2015
      ----------------------------------------------------- */

    public function permission($userid = null, $permission = null) {
        $this->autoRender = false;
        if ($permission) {
            $this->Permission->DeleteAllPermission($userid);
            $permissiondata = array_filter($permission['tab_id']);
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
      Function name:storeRequest()
      Description:Request New store
      created:01/9/2015
      ----------------------------------------------------- */

    public function storeRequestList() {
        $this->layout = "super_dashboard";
        $value = "";
        $criteria = "MerchantStoreRequest.is_deleted=0";
        if (!empty($this->params)) {
            if (!empty($this->params->query['keyword'])) {
                $value = trim($this->params->query['keyword']);
            }
            if ($value != "") {
                $criteria .= " AND (MerchantStoreRequest.store_name LIKE '%" . $value . "%')";
            }
        }

        $this->paginate = array('conditions' => array($criteria), 'order' => array('MerchantStoreRequest.created' => 'DESC'));
        $storedetail = $this->paginate('MerchantStoreRequest');
        $this->set('list', $storedetail);
        $this->set('keyword', $value);
    }

    /* ------------------------------------------------
      Function name:deleteRequestedStore()
      Description:Dete Requested Store
      created:01/9/2015
      ----------------------------------------------------- */

    public function deleteRequestedStore($EncryptedstoreID = null) {
        $this->autoRender = false;
        $this->layout = "super_dashboard";
        $data['MerchantStoreRequest']['id'] = $this->Encryption->decode($EncryptedstoreID);
        $data['MerchantStoreRequest']['is_deleted'] = 1;
        if ($this->MerchantStoreRequest->saveStoreRequest($data)) {
            $this->Session->setFlash(__("Store Request deleted"), 'alert_success');
            $this->redirect(array('controller' => 'super', 'action' => 'storeRequestList'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'super', 'action' => 'storeRequestList'));
        }
    }

    public function checkSuperEmail($roleId = null) {
        $this->autoRender = false;
        if ($_GET) {
            $emailEntered = $_GET['data']['User']['email'];
            $merchantId = "";
//            $emailStatus = $this->User->superemailExists($emailEntered, $roleId);
            $emailStatus = $this->User->emailExistsSuper($emailEntered, $roleId);
            echo json_encode($emailStatus);
        }
    }

    /* ------------------------------------------------
      Function name:forgetPassword()
      Description:For forget password
      created:04/9/2015
      ----------------------------------------------------- */

    public function forgetPassword() {
        $this->layout = "super_login";
        $this->autorender = false;

        if ($this->request->is('post') && !empty($this->data)) {
            $roleId = "";
            $email = $this->request->data['User']['email'];
            $roleId = 1;
            $merchantId = $this->Session->read('admin_merchant_id');
            if (!$merchantId) {
                $merchantId = "";
            }

            $userEmail = $this->User->checkSuperForgetEmail($roleId, $email); //Calling function on model for checking the email
            $this->loadModel('Merchant');
            $this->loadModel('MainSiteSetting');

            $superEmail = $this->MainSiteSetting->getSiteSettings();
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
                    $url = HTTP_ROOT . 'users/resetPassword/' . $token . '/1';
                    $activationLink = '<a style="color:#fff;background-color: #10c4f7; text-decoration:none; padding: 5px 10px 7px;font-weight: bold; display:inline-block;" href="' . $url . '">Click here to reset your password</a>';
                    $emailData = str_replace('{ACTIVE_LINK}', $activationLink, $emailData);
                    $subject = ucwords(str_replace('_', ' ', $emailTemplate['DefaultTemplate']['template_subject']));
                    $this->Email->to = $email;
                    $this->Email->subject = $subject;
                    $this->Email->from = $superEmail['MainSiteSetting']['super_email'];
                    $this->set('data', $emailData);
                    $this->Email->template = 'template';
                    //echo $this->smtp_port;die;
                    $this->Email->smtpOptions = array(
                        'port' => "$this->smtp_port",
                        'timeout' => '100',
                        'host' => "$this->smtp_host",
                        'username' => "$this->smtp_username",
                        'password' => "$this->smtp_password"
                    );
                    //$this->Email->delivery = "smtp";
                    $this->Email->sendAs = 'html'; // because we like to send pretty mail
                    try {
                        if ($this->Email->send()) {
                            $this->request->data['User']['id'] = $userEmail['User']['id'];

                            $this->request->data['User']['forgot_token'] = $token;
                            $this->User->saveUserInfo($this->data['User']);
                            $this->Session->setFlash(__("Please check your email for reset new password"), 'alert_success');
                            $this->redirect(array('controller' => 'super', 'action' => 'login'));
                        }
                    } catch (Exception $e) {
                        $this->Session->setFlash("Please try after some time", 'alert_failed');
                        $this->redirect(array('controller' => 'super', 'action' => 'forgetPassword'));
                    }
                }
                ////////////Dynamic SMTP//////////
            } else {
                $this->Session->setFlash("Please enter correct email.", 'alert_failed');
                $this->redirect(array('controller' => 'super', 'action' => 'forgetPassword'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:customerList()
      Description:Display the list of customer
      created:14/09/2015
      ----------------------------------------------------- */

    public function customerList($clearAction = null) {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'customerList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";
        $value = "";
        $criteria = "User.role_id=4 AND User.is_deleted=0";

        //if(isset($this->params['named']['sort']) || isset($this->params['named']['page'])){
        if ($this->Session->read('CustomerSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('CustomerSearchData'), true);
        } else {
            $this->Session->delete('CustomerSearchData');
        }

        if (!empty($this->request->data)) {
            $this->Session->write('CustomerSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['User']['store_id'])) {
                $active = trim($this->request->data['User']['store_id']);
                $criteria .= " AND (User.store_id ='" . $active . "')";
            }
            if (!empty($this->request->data['User']['name'])) {
                $value = trim($this->request->data['User']['name']);
                $criteria .= " AND (User.fname LIKE '%" . $value . "%' OR User.lname LIKE '%" . $value . "%' OR User.email LIKE '%" . $value . "%')";
            }
            if ($this->request->data['User']['from'] != '' && $this->request->data['User']['to'] != '') {
                $stratdate = $this->Dateform->formatDate($this->request->data['User']['from']);
                $enddate = $this->Dateform->formatDate($this->request->data['User']['to']);
                // echo $stratdate;echo "<br>";echo $enddate;die;
                // $criteria .= " AND (User.created BETWEEN ? AND ?) =" array($stratdate,$enddate);

                $criteria.= " AND (User.created BETWEEN '" . $stratdate . "' AND '" . $enddate . "')";
            }
        }

        $this->User->bindModel(array('belongsTo' => array('Store' => array('fields' => array('Store.store_name', 'Store.id'), 'className' => 'Store', 'foreignKey' => 'store_id'))), false);
        $this->paginate = array('limit' => 30, 'conditions' => array($criteria), 'fields' => array('User.fname', 'User.lname', 'User.email', 'User.id', 'User.store_id', 'User.merchant_id', 'User.is_active', 'User.created', 'User.phone'), 'order' => array('User.created' => 'DESC'), 'recursive' => 2);
        $customerdetail = $this->paginate('User');
        $this->set('list', $customerdetail);
        $this->set('keyword', $value);
    }

    /* ------------------------------------------------
      Function name:activateCustomer()
      Description:Active/deactive Customer
      created:10/8/2015
      ----------------------------------------------------- */

    public function activateCustomer($EncryptCustomerID = null, $status = 0) {

        $this->autoRender = false;
        $this->layout = "super_dashboard";
        $data['User']['id'] = $this->Encryption->decode($EncryptCustomerID);
        $data['User']['is_active'] = $status;
        if ($this->User->saveUserInfo($data)) {
            if ($status) {
                $SuccessMsg = "User Activated";
            } else {
                $SuccessMsg = "User Deactivated and User will not get Display in the List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'super', 'action' => 'customerList'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'super', 'action' => 'customerList'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteCustomer()
      Description:Delete Customer
      created:13/09/2015
      ----------------------------------------------------- */

    public function deleteCustomer($EncryptCustomerID = null) {
        $this->autoRender = false;
        $this->layout = "super_dashboard";
        $data['User']['id'] = $this->Encryption->decode($EncryptCustomerID);
        $data['User']['is_deleted'] = 1;
        if ($this->User->saveUserInfo($data)) {
            $this->Session->setFlash(__("Customer deleted"), 'alert_success');
            $this->redirect(array('controller' => 'super', 'action' => 'customerList'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'super', 'action' => 'customerList'));
        }
    }

    /* ------------------------------------------------
      Function name:editCustomer()
      Description:Edit customer
      created:13/09/2015
      ----------------------------------------------------- */

    public function editCustomer($EncryptCustomerID = null) {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'customerList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }

        $this->layout = "super_dashboard";
        $data['User']['id'] = $this->Encryption->decode($EncryptCustomerID);
        $this->loadModel('User');
        $customerDetail = $this->User->getUser($data['User']['id']);
        if ($this->request->is('post')) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $email = trim($this->data['User']['email']);
            $isUniqueEmail = $this->User->checkUserUniqueEmail($email, $storeId = NULL, $data['User']['id']);
            if ($isUniqueEmail) {
                $storeId = "";
                $merchantId = "";
                $storeId = $this->Session->read('admin_store_id'); // It will read from session when a customer will try to register on store
                $merchantId = $this->Session->read('admin_merchant_id');
                $email = trim($this->request->data['User']['email']); //Here username is email
                $this->request->data['User']['store_id'] = $this->request->data['User']['store_id']; // Store Id
                $this->request->data['User']['merchant_id'] = $this->request->data['User']['merchant_id']; // Merchant Id
                $roleId = $this->request->data['User']['role_id']; // Role Id of the user
                $userName = trim($this->request->data['User']['email']); //Here username is email
                $this->request->data['User']['username'] = trim($userName);
                //echo $actualDbDate=date("Y-m-d",strtotime($this->request->data['User']['dateOfBirth']));die;  //Not working
                $actualDbDate = $this->Dateform->formatDate($this->request->data['User']['dateOfBirth']); // calling formatDate function in Appcontroller to format the date (Y-m-d) format
                $this->request->data['User']['dateOfBirth'] = $actualDbDate;
                $result = $this->User->saveUserInfo($this->request->data);   // We are calling function written on Model to save data

                $this->Session->setFlash(__('Customer details updated successfully'), 'alert_success');

                $this->redirect(array('controller' => 'super', 'action' => 'customerList'));
            } else {
                $this->Session->setFlash(__("Email  Already exists"), 'alert_failed');
            }
        }
        $this->request->data = $customerDetail;
    }

    /* ------------------------------------------------
      Function name:merchantPaymentList()
      Description:Display merchant payment list
      created:15/09/2015
      ----------------------------------------------------- */

    public function merchantPaymentList($clearAction = null) {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'merchantPaymentList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";
        $this->loadModel('MerchantPayment');
        $criteria = "MerchantPayment.is_deleted=0";
        if ($this->Session->read('MerchantPaymentListSearch') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('MerchantPaymentListSearch'), true);
        } else {
            $this->Session->delete('MerchantPaymentListSearch');
        }

        if (!empty($this->request->data)) {
            $this->Session->write('MerchantPaymentListSearch', json_encode($this->request->data));
            if (!empty($this->request->data['MerchantPayment']['merchant_id'])) {
                $active = trim($this->request->data['MerchantPayment']['merchant_id']);
                $criteria .= " AND (MerchantPayment.merchant_id ='" . $active . "')";
            }
            if (!empty($this->request->data['MerchantPayment']['payment_status'])) {
                $payment_status = trim($this->request->data['MerchantPayment']['payment_status']);
                $criteria .= " AND (MerchantPayment.payment_status ='" . $payment_status . "')";
            }
            if (!empty($this->request->data['MerchantPayment']['keyword'])) {
                $value = trim($this->request->data['MerchantPayment']['keyword']);
                $criteria .= " AND (Merchant.name LIKE '%" . $value . "%' OR Plan.name LIKE '%" . $value . "%' OR Merchant.company_name LIKE '%" . $value . "%' OR Merchant.address LIKE '%" . $value . "%' OR Merchant.phone LIKE '%" . $value . "%' OR Merchant.email LIKE '%" . $value . "%' OR Merchant.domain_name LIKE '%" . $value . "%')";
            }
            if (!empty($this->request->data['MerchantPayment']['from']) && !empty($this->request->data['MerchantPayment']['to'])) {
                $stratdate = $this->Dateform->formatDate($this->request->data['MerchantPayment']['from']);
                $enddate = $this->Dateform->formatDate($this->request->data['MerchantPayment']['to']);
                $criteria.= " AND (Date(MerchantPayment.payment_date) >= '" . $stratdate . "' AND Date(MerchantPayment.payment_date) <='" . $enddate . "')";
            }
        }

        $this->MerchantPayment->bindModel(array('belongsTo' => array(
                'Plan' => array('fields' => array('name'), 'className' => 'Plan', 'foreignKey' => 'plan_id'), 'Merchant' => array('fields' => array('name'), 'className' => 'Merchant', 'foreignKey' => 'merchant_id'))), false);
        $this->paginate = array('conditions' => array($criteria), 'order' => array('MerchantPayment.created' => 'DESC'));
        $transactionDetail = $this->paginate('MerchantPayment');
        $this->set('list', $transactionDetail);
    }
    
    
    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('MerchantPayment');
            $criteria = "MerchantPayment.is_deleted=0";
            if (!empty($_GET['term'])) {
                $value = trim($_GET['term']);
                $criteria .= " AND (Merchant.name LIKE '%" . $value . "%' OR Plan.name LIKE '%" . $value . "%' OR Merchant.company_name LIKE '%" . $value . "%' OR Merchant.address LIKE '%" . $value . "%' OR Merchant.phone LIKE '%" . $value . "%' OR Merchant.email LIKE '%" . $value . "%' OR Merchant.domain_name LIKE '%" . $value . "%')";
            }
            $this->MerchantPayment->bindModel(array('belongsTo' => array(
                'Plan' => array('fields' => array('name'), 'className' => 'Plan', 'foreignKey' => 'plan_id'), 'Merchant' => array('fields' => array('name', 'address', 'phone', 'email'), 'className' => 'Merchant', 'foreignKey' => 'merchant_id'))), false);
            
            $searchData = $this->MerchantPayment->find('all', array('conditions' => array($criteria), 'order' => array('MerchantPayment.created' => 'DESC')));
            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    $new_array[] = array('label' => $val['Merchant']['name'], 'value' => $val['Merchant']['name'], 'desc' => $val['Merchant']['name'] . '-' . $val['Plan']['name'] . '-' . $val['Merchant']['address'] . '-' . $val['Merchant']['phone'] . '-' . $val['Merchant']['email']);
                };
            }
            echo json_encode($new_array);/**/
        } else {
            exit;
        }
    }

    /* ------------------------------------------------
      Function name:addMerchantPayment()
      Description:Add merchant payments
      created:15/09/2015
      ----------------------------------------------------- */

    public function addMerchantPayment() {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'merchantPaymentList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";
        if (!empty($this->request->data)) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $invoice_number = $this->Common->RandomString();
            $this->request->data['MerchantPayment']['payment_date'] = $this->Dateform->formatDate($this->request->data['MerchantPayment']['payment_date']);
            $this->request->data['MerchantPayment']['invoice_number'] = $invoice_number;
            if ($this->MerchantPayment->savePayment($this->request->data)) {
                $this->loadModel('DefaultTemplate');
                $emailSuccess = $this->DefaultTemplate->adminTemplates('invoice');
                if ($emailSuccess) {
                    $merchant = $this->Merchant->getMerchantDetail($this->request->data['MerchantPayment']['merchant_id']);
                    $superEmail = $this->MainSiteSetting->getSiteSettings();
                    $plan = $this->Plan->find('first', array('conditions' => array('Plan.id' => $this->request->data['MerchantPayment']['plan_id'])));
                    $project = 'Merchant Subscription Charges <br/> <b>P.O. </b>';
                    $description = $plan['Plan']['description'];
                    $address = $merchant['Merchant']['owner_name'] . '<br/>' . $merchant['Merchant']['company_name']
                            . '<br/>' . $merchant['Merchant']['address'] . '<br/>' . $merchant['Merchant']['city'] . ', '
                            . $merchant['Merchant']['state'] . ' ' . $merchant['Merchant']['zipcode'];
                    $logo = '<img src="' . HTTP_ROOT . '/img/logo.jpg" width="200"/>';
                    $emailData = $emailSuccess['DefaultTemplate']['template_message'];
                    $emailData = str_replace('{LOGO}', $logo, $emailData);
                    if ($this->request->data['MerchantPayment']['payment_status'] == 'Paid') {
                        $emailData = str_replace('{STATUS}', 'PAID', $emailData);
                    } else {
                        $emailData = str_replace('{STATUS}', '', $emailData);
                    }
                    $emailData = str_replace('{QUANTITY}', 1, $emailData);
                    $emailData = str_replace('{AMOUNT}', $this->request->data['MerchantPayment']['amount'], $emailData);
                    $emailData = str_replace('{PROJECT}', $project, $emailData);
                    $emailData = str_replace('{DATE}', $this->request->data['MerchantPayment']['payment_date'], $emailData);
                    $emailData = str_replace('{INVOICE_NUMBER}', $invoice_number, $emailData);
                    $emailData = str_replace('{ADDRESS}', $address, $emailData);
                    $emailData = str_replace('{DESCRIPTION}', $description, $emailData);
                    $subject = ucwords(str_replace('_', ' ', $emailSuccess['DefaultTemplate']['template_subject']));
                    $this->Email->to = $merchant['Merchant']['email'];
                    $this->Email->subject = $subject;
                    $this->Email->from = $superEmail['MainSiteSetting']['super_email'];
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
                $this->Session->setFlash(__("Merchant payment added successfully."), 'alert_success');
                $this->redirect(array('controller' => 'super', 'action' => 'merchantPaymentList'));
            } else {
                $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                $this->redirect(array('controller' => 'super', 'action' => 'merchantPaymentList'));
            }
        }
        $plan = $this->Plan->find('list', array('fields' => array('id', 'name')));
        $this->set('plan', $plan);
    }


    public function getMerchantUrl()
    {
        $this->layout = false;
        $this->autoRender = false;
        $merchant = array();
        if ($this->request->is(array('ajax'))) 
        {
            if(isset($this->request->data))
            {
                if(isset($this->request->data['merchantId']) && $this->request->data['merchantId'] != '')
                {
                    $merchantId = $this->request->data['merchantId'];
                    $this->loadModel('Merchant');
                    $merchant = $this->Merchant->find('first', array('fields' => array('id', 'domain_name'), 'conditions' => array('Merchant.id' => $merchantId)));
                }
            }
        }
        $merchant = json_encode($merchant);
        return $merchant;
    }


    public function updateMerchantPayment($merchantPaymentId = null) {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'merchantPaymentList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";
        $merchantDecodePaymentId = $this->Encryption->decode($merchantPaymentId);
        if (!empty($this->request->data)) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $this->request->data['MerchantPayment']['payment_date'] = $this->Dateform->formatDate($this->request->data['MerchantPayment']['payment_date']);
            $this->loadModel('MerchantPayment');
            if ($this->MerchantPayment->savePayment($this->request->data)) {
                $this->loadModel('DefaultTemplate');
                $emailSuccess = $this->DefaultTemplate->adminTemplates('invoice');
                if ($emailSuccess) {
                    $merchant = $this->Merchant->getMerchantDetail($this->request->data['MerchantPayment']['merchant_id']);
                    $superEmail = $this->MainSiteSetting->getSiteSettings();
                    $plan = $this->Plan->find('first', array('conditions' => array('Plan.id' => $this->request->data['MerchantPayment']['plan_id'])));
                    $project = 'Merchant Subscription Charges <br/> <b>P.O. </b>';
                    $description = $plan['Plan']['description'];
                    $address = $merchant['Merchant']['owner_name'] . '<br/>' . $merchant['Merchant']['company_name']
                            . '<br/>' . $merchant['Merchant']['address'] . '<br/>' . $merchant['Merchant']['city'] . ', '
                            . $merchant['Merchant']['state'] . ' ' . $merchant['Merchant']['zipcode'];
                    $logo = '<img src="' . HTTP_ROOT . '/img/logo.jpg" width="200"/>';
                    $emailData = $emailSuccess['DefaultTemplate']['template_message'];
                    $emailData = str_replace('{LOGO}', $logo, $emailData);
                    if ($this->request->data['MerchantPayment']['payment_status'] == 'Paid') {
                        $emailData = str_replace('{STATUS}', 'PAID', $emailData);
                    } else {
                        $emailData = str_replace('{STATUS}', '', $emailData);
                    }
                    $emailData = str_replace('{QUANTITY}', 1, $emailData);
                    $emailData = str_replace('{AMOUNT}', $this->request->data['MerchantPayment']['amount'], $emailData);
                    $emailData = str_replace('{PROJECT}', $project, $emailData);
                    $emailData = str_replace('{DATE}', $this->request->data['MerchantPayment']['payment_date'], $emailData);
                    $emailData = str_replace('{INVOICE_NUMBER}', $this->request->data['MerchantPayment']['invoice_number'], $emailData);
                    $emailData = str_replace('{ADDRESS}', $address, $emailData);
                    $emailData = str_replace('{DESCRIPTION}', $description, $emailData);
                    $subject = ucwords(str_replace('_', ' ', $emailSuccess['DefaultTemplate']['template_subject']));
                    $this->Email->to = $merchant['Merchant']['email'];
                    $this->Email->subject = $subject;
                    $this->Email->from = $superEmail['MainSiteSetting']['super_email'];
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
                $this->Session->setFlash(__("Merchant payment updated successfully."), 'alert_success');
                $this->redirect(array('controller' => 'super', 'action' => 'merchantPaymentList'));
            } else {
                $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                $this->redirect(array('controller' => 'super', 'action' => 'merchantPaymentList'));
            }
        }
        $merchantPayment = $this->MerchantPayment->fetchMerchantPayment($merchantDecodePaymentId);
        $merchantPayment['MerchantPayment']['payment_date'] = date('m-d-Y', strtotime($merchantPayment['MerchantPayment']['payment_date']));
        $this->request->data = $merchantPayment;
        $plan = $this->Plan->find('list', array('fields' => array('id', 'name')));
        $this->set(compact('plan'));
    }

    /* ------------------------------------------------
      Function name:addMerchant()
      Description:add Merchant Details
      created:15/9/2015
      ----------------------------------------------------- */

    public function addMerchant() {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addMerchant', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";

        if (!empty($this->request->data)) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $this->request->data['Merchant']['domain_name'] = trim($this->request->data['Merchant']['domain_name']);
            if ($this->Merchant->checkMerchantUniqueName($this->request->data['Merchant']['name'])) {
                if ($this->Merchant->checkMerchantUniqueEmail($this->request->data['Merchant']['email'])) {
                    $roleId = 2; //Merchant User Role Id
                    if ($this->User->superemailExists($this->request->data['User']['email'], $roleId)) {
                        if ($this->Merchant->saveMerchant($this->request->data['Merchant'])) {
                            $merchantID = $this->Merchant->getInsertID();
                            // Hq merchant Email Template
                            $this->loadModel('EmailTemplate');
                            $this->loadModel('DefaultTemplate');
                            $emailData = $this->DefaultTemplate->getAllDefaultTemplate();
                            foreach ($emailData as $eData) {
                                unset($eData['DefaultTemplate']['id'], $eData['DefaultTemplate']['is_active'], $eData['DefaultTemplate']['is_deleted'], $eData['DefaultTemplate']['created'], $eData['DefaultTemplate']['modified'], $eData['DefaultTemplate']['store_id']);
                                $eData['DefaultTemplate']['merchant_id'] = $merchantID;
                                $emailTemp['EmailTemplate'] = $eData['DefaultTemplate'];
                                $this->EmailTemplate->create();
                                $this->EmailTemplate->saveTemplate($emailTemp);
                            }
                            $this->request->data['User']['merchant_id'] = $merchantID;
                            $this->request->data['User']['role_id'] = $roleId;
                            if ($this->User->saveUserInfo($this->request->data['User'])) {
                                $userID = $this->User->getInsertID();
                                $data['id'] = $merchantID;
                                $data['owner_name'] = $this->request->data['User']['fname'] . ' ' . $this->request->data['User']['lname'];
                                $data['user_id'] = $userID;
                                $this->Merchant->saveMerchant($data);
                                // Permission for admin user
                                $tabid = $this->Tab->getTabData('Hq Staff Management', null, null, $roleId);
                                $data['tab_id'] = $tabid;
                                $data['user_id'] = $userID;
                                $data['is_deleted'] = 0;
                                $this->Permission->savePermission($data);
                                // Permission for admin user
				//TermsAndPolicy for merchant start
                                $this->loadModel('TermsAndPolicy');
                                $termAndPolicy['TermsAndPolicy']['merchant_id'] = $merchantID;
                                $this->TermsAndPolicy->create();
                                $this->TermsAndPolicy->save($termAndPolicy);
                                //TermsAndPolicy for merchant end
                                // Entry in Module table for Routes
                                /*  HQ FrontPage    */
                                $routedata['id'] = "";
                                $routedata['merchant_id'] = $merchantID;
                                $routedata['type'] = 5;
                                $routedata['subdomain'] = $this->request->data['Merchant']['domain_name'] . "/";
                                $routedata['displayController'] = "hqusers";
                                $routedata['displayAction'] = "merchant";
                                $routedata['is_deleted'] = 0;
                                $this->Module->saveRouteData($routedata);
                                /*  HQ FrontPage    */
                                /*  HQ Login    */
                                $routedata['type'] = 2;
                                $routedata['subdomain'] = $this->request->data['Merchant']['domain_name'] . "/hq/";
                                $routedata['displayController'] = "hq";
                                $routedata['displayAction'] = "login";
                                $this->Module->saveRouteData($routedata);
                                /*  HQ Login    */
                                // Entry in Module table for Routes
                                // Entry in Routes File
                                $hqUrl = trim($this->request->data['Merchant']['domain_name']);
                                $routesStr = "##" . $hqUrl . "##start
                                    Router::connect('/hq/$hqUrl', array('controller' => 'hq', 'action' => 'merchant'));
                                    ##" . $hqUrl . "##end";
                                $filepath = $_SERVER['DOCUMENT_ROOT'] . DS . APP_DIR . DS . 'Config' . DS . 'custom_routes.php';
                                file_put_contents($filepath, $routesStr, FILE_APPEND);
                                // Entry in Routes File
                                // Permission for admin user
                                /**                                 * ******Start mail send********* */
                                if (!empty($this->request->data['User']['email'])) {
                                    $this->loadModel('MainSiteSetting');
                                    $superEmail = $this->MainSiteSetting->getSiteSettings();
                                    $this->loadModel('DefaultTemplate');
                                    $template_type = 'new_merchant';
                                    $emailTemplate = $this->DefaultTemplate->adminTemplates($template_type);
                                    $randomCode = $this->request->data['User']['password'];

                                    if ($emailTemplate) {
                                        if ($this->request->data['User']['fname']) {
                                            $fullName1 = $this->request->data['User']['fname'];
                                            $fullName = ucfirst($fullName1);
                                        }
                                        $userName = $this->request->data['User']['email'];
                                        $emailData = $emailTemplate['DefaultTemplate']['template_message'];
                                        $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                                        $emailData = str_replace('{USERNAME}', $userName, $emailData);
                                        $emailData = str_replace('{PASSWORD}', $randomCode, $emailData);
                                        $activationLink = $hqUrl . '/hq/login';
                                        $emailData = str_replace('{ACTIVE_LINK}', $activationLink, $emailData);
                                        $subject = ucwords(str_replace('_', ' ', $emailTemplate['DefaultTemplate']['template_subject']));
                                        $this->Email->to = $this->request->data['User']['email'];
                                        $this->Email->subject = $subject;
                                        $this->Email->from = $superEmail['MainSiteSetting']['super_email'];
                                        $this->set('data', $emailData);
                                        $this->Email->template = 'template';
                                        $this->Email->smtpOptions = array(
                                            'port' => "$this->smtp_port",
                                            'timeout' => '30',
                                            'host' => "$this->smtp_host",
                                            'username' => "$this->smtp_username",
                                            'password' => "$this->smtp_password"
                                        );
                                        //$this->Email->delivery = "smtp";
                                        $this->Email->sendAs = 'html'; // because we like to send pretty mail
                                        try {
                                            $this->Email->send();
                                        } catch (Exception $e) {
                                            
                                        }
                                    }
                                }
                                // Send Login Credentials to Admin User
                                $this->Session->setFlash(__("Merchant Details successfully added"), 'alert_success');
                                $this->redirect(array('controller' => 'super', 'action' => 'viewMerchantDetails'));
                            } else {
                                $this->Session->setFlash(__("Unable to save Merchant admin user details, Please try again"), 'alert_failed');
                            }
                        } else {
                            $this->Session->setFlash(__("Unable to save Merchant data, Please try again"), 'alert_failed');
                        }
                    } else {
                        $this->Session->setFlash(__("Merchant Admin user Email already exists"), 'alert_failed');
                    }
                } else {
                    $this->Session->setFlash(__("Merchant Email already exists"), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Merchant name already exists"), 'alert_failed');
            }
        }
    }




    /* ------------------------------------------------
      Function name:merchantStoreList()
      Description:Display merchants store list
      created:25/09/2017
      ----------------------------------------------------- */

    public function merchantStoreList($merchantId = null, $clearAction = null) 
    {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'merchantPaymentList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";
        $storeIds = '';
        $merchantDecodeId = $this->Encryption->decode($merchantId);
        if($merchantDecodeId != null && !empty($merchantDecodeId)) 
        {
            $this->loadModel('Store');
            $this->loadModel('Merchant');
            $merchant = $this->Merchant->find('first', array('fields' => array('Merchant.id', 'Merchant.name'), 'conditions' => array('Merchant.id' => $merchantDecodeId)));
            
            $this->loadModel('Store');
            $criteria = "Store.is_deleted=0 AND Store.merchant_id = " . $merchantDecodeId;

            if ($this->Session->read('MerchantStoreListSearch') && $clearAction != 'clear' && !$this->request->is('post')) {
                $this->request->data = json_decode($this->Session->read('MerchantStoreListSearch'), true);
            } else {
                $this->Session->delete('MerchantStoreListSearch');
            }

            if (!empty($this->request->data)) {
                $this->Session->write('MerchantStoreListSearch', json_encode($this->request->data));
                if (!empty($this->request->data['Store']['keyword'])) {
                    $value = trim($this->request->data['Store']['keyword']);
                    $criteria .= " AND (Store.store_name LIKE '%" . $value . "%' OR Store.address LIKE '%" . $value . "%' OR Store.phone LIKE '%" . $value . "%' OR Store.email_id LIKE '%" . $value . "%' OR Store.store_url LIKE '%" . $value . "%')";
                }
            }
            $this->paginate = array('conditions' => array($criteria), 'order' => array('Store.created' => 'DESC'));
            $transactionDetail = $this->paginate('Store');
            $this->set('list', $transactionDetail);
            
            $this->set('merchantDetail', $merchant);
            $this->set('merchantId', $merchantId);
        } else {
            $this->redirect(array('controller' => 'super', 'action' => 'merchantPaymentList'));
        }
    }
    
    
    /* ------------------------------------------------
      Function name:merchantStorePaymentList()
      Description:Display merchants store payment list
      created:25/09/2017
      ----------------------------------------------------- */

    public function merchantStorePaymentList($storeId = null, $clearAction = null) 
    {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'merchantPaymentList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";
        $storeIds = '';
        $storeDecodeId = $this->Encryption->decode($storeId);
        if($storeDecodeId != null && !empty($storeDecodeId)) {
            $this->loadModel('Store');
            $storeDetail = $this->Store->find('first', array('fields' => array('Store.id', 'Store.merchant_id', 'Store.store_name'), 'conditions' => array('Store.id' => $storeDecodeId)));
            $this->loadModel('Merchant');
            $merchant = $this->Merchant->find('first', array('fields' => array('Merchant.id', 'Merchant.name'), 'conditions' => array('Merchant.id' => $storeDetail['Store']['merchant_id'])));
            
            $this->loadModel('StorePayment');
            $criteria = "StorePayment.is_deleted=0 AND StorePayment.store_id = " . $storeDecodeId;
            if ($this->Session->read('MerchantStorePaymentListSearch') && $clearAction != 'clear' && !$this->request->is('post')) {
                $this->request->data = json_decode($this->Session->read('MerchantStorePaymentListSearch'), true);
            } else {
                $this->Session->delete('MerchantStorePaymentListSearch');
            }

            if (!empty($this->request->data)) {
                $this->Session->write('MerchantStorePaymentListSearch', json_encode($this->request->data));
                if (!empty($this->request->data['StorePayment']['payment_status'])) {
                    $payment_status = trim($this->request->data['StorePayment']['payment_status']);
                    $criteria .= " AND (StorePayment.payment_status ='" . $payment_status . "')";
                }
                if (!empty($this->request->data['StorePayment']['keyword'])) {
                    $value = trim($this->request->data['StorePayment']['keyword']);
                    $criteria .= " AND (Store.store_name LIKE '%" . $value . "%' OR Plan.name LIKE '%" . $value . "%' OR Store.address LIKE '%" . $value . "%' OR Store.phone LIKE '%" . $value . "%' OR Store.email_id LIKE '%" . $value . "%' OR Store.store_url LIKE '%" . $value . "%')";
                }
                if (!empty($this->request->data['StorePayment']['from']) && !empty($this->request->data['StorePayment']['to'])) {
                    $stratdate = $this->Dateform->formatDate($this->request->data['StorePayment']['from']);
                    $enddate = $this->Dateform->formatDate($this->request->data['StorePayment']['to']);
                    $criteria.= " AND (Date(StorePayment.payment_date) >= '" . $stratdate . "' AND Date(StorePayment.payment_date) <='" . $enddate . "')";
                }
            }

            $this->StorePayment->bindModel(array('belongsTo' => array(
                    'Plan' => array('fields' => array('name'), 'className' => 'Plan', 'foreignKey' => 'plan_id'), 'Store' => array('fields' => array('store_name', 'store_url'), 'className' => 'Store', 'foreignKey' => 'store_id'))), false);
            $this->paginate = array('conditions' => array($criteria), 'order' => array('StorePayment.created' => 'DESC'));
            $transactionDetail = $this->paginate('StorePayment');
            $this->set('list', $transactionDetail);
            
            $this->set('merchantDetail', $merchant);
            $this->set('storeDetail', $storeDetail);
            $this->set('storeId', $storeId);
        } else {
            $this->redirect(array('controller' => 'super', 'action' => 'merchantPaymentList'));
        }
    }



    /* ------------------------------------------------
      Function name:addStore()
      Description:add Store Details
      created:15/9/2015
      ----------------------------------------------------- */

    public function addStore($id = null) {

        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addStore', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";
        if (!empty($id)) {
            $key = $this->Encryption->decode($id);
            $data = $this->MerchantStoreRequest->find('first', array('conditions' => array('id' => $key)));
            $store = '';
            foreach ($data as $k => $value) {
                $store['Store']['store_name'] = $value['store_name'];
                $store['Store']['email_id'] = $value['email'];
                $store['Store']['phone'] = $value['phone'];
                $store['Store']['merchant_id'] = $value['merchant_id'];
            }
        }
        if (!empty($this->request->data)) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $this->request->data['Store']['store_url'] = trim($this->request->data['Store']['store_url']);
            if ($this->Store->checkStoreUniqueName($this->request->data['Store']['store_name'], $this->request->data['Merchant']['id'])) {
                if ($this->Store->checkStoreUniqueEmail($this->request->data['Store']['email_id'])) {
                    $roleId = 3;
                    // Store User role ID
                    $latitude = "";
                    $longitude = "";
                    if (trim($this->request->data['Store']['address']) && trim($this->request->data['Store']['city']) && trim($this->request->data['Store']['state']) && trim($this->request->data['Store']['zipcode'])) {
                        $dlocation = trim($this->request->data['Store']['address']) . " " . trim($this->request->data['Store']['city']) . " " . trim($this->request->data['Store']['state']) . " " . trim($this->request->data['Store']['zipcode']);
                        $address2 = str_replace(' ', '+', $dlocation);
                        $geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address=' . urlencode($address2) . '&sensor=false');
                        $output = json_decode($geocode);
                        if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {

                        } else {
                            $latitude = @$output->results[0]->geometry->location->lat;
                            $longitude = @$output->results[0]->geometry->location->lng;
                        }
                    }
                    if ($latitude && $longitude) {
                        $this->request->data['Store']['latitude'] = $latitude;
                        $this->request->data['Store']['logitude'] = $longitude;
                    }
                    if ($this->Store->saveStoreInfo($this->request->data['Store'])) {
                        if (!empty($id)) {
                            $status = 1;
                            $this->MerchantStoreRequest->id = $key;
                            $this->MerchantStoreRequest->saveField("request_status", $status);
                        }
                        $storeId = $this->Store->getInsertID();
                        $this->request->data['User']['store_id'] = $storeId;
                        $this->request->data['User']['merchant_id'] = $this->request->data['Merchant']['id'];
                        $this->request->data['User']['role_id'] = $roleId;
                        $defaultSpecialDay = $this->DefaultSpecialDay->find('all', array("conditions" => array("DefaultSpecialDay.is_active" => 1, "DefaultSpecialDay.is_deleted" => 0, "DefaultSpecialDay.is_default" => array(1, 3))));
                        if (!empty($defaultSpecialDay)) {
                            foreach ($defaultSpecialDay as $specialDay) {
                                $specialDays['SpecialDay']['store_id'] = $storeId;
                                $specialDays['SpecialDay']['merchant_id'] = $this->request->data['Merchant']['id'];
                                $specialDays['SpecialDay']['default_special_day_id'] = $specialDay['DefaultSpecialDay']['id'];
                                $specialDays['SpecialDay']['name'] = $specialDay['DefaultSpecialDay']['name'];
                                $specialDays['SpecialDay']['template_message'] = $specialDay['DefaultSpecialDay']['template_message'];
                                $specialDays['SpecialDay']['sms_template'] = $specialDay['DefaultSpecialDay']['sms_template'];
                                $specialDays['SpecialDay']['special_day_time_id'] = 1;
                                $this->SpecialDay->create();
                                $this->SpecialDay->save($specialDays);
                            }
                        }

                        if ($this->User->saveUserInfo($this->request->data['User'])) {
                            $userID = $this->User->getInsertID();
                            $data['merchant_id'] = $this->request->data['Merchant']['id'];
                            $data['user_id'] = $userID;
                            $data['time_zone_id'] = 7;
                            $this->Store->saveStoreInfo($data);


                            // Store Featured Section
                            $this->loadModel('DefaultFeaturedSection');
                            $this->loadModel('StoreFeaturedSection');
                            //$sectionData = $this->DefaultFeaturedSection->getAllDetail();
                            $sectionData = $this->DefaultFeaturedSection->find('all', array('conditions' => array('is_active' => 1, 'is_deleted' => 0)));
                            foreach ($sectionData as $sData) {
                                unset($sData['DefaultFeaturedSection']['id'], $sData['DefaultFeaturedSection']['is_active'], $sData['DefaultFeaturedSection']['is_deleted'], $sData['DefaultFeaturedSection']['created'], $sData['DefaultFeaturedSection']['modified']);
                                $sData['StoreFeaturedSection'] = $sData['DefaultFeaturedSection'];
                                $sData['StoreFeaturedSection']['store_id'] = $storeId;
                                $sData['StoreFeaturedSection']['merchant_id'] = $this->request->data['Merchant']['id'];
                                $this->StoreFeaturedSection->create();
                                $this->StoreFeaturedSection->saveSection($sData);
                            }
                            //Default menu entry
                            $this->_addDefaultStoreMenu($this->request->data['Merchant']['id'], $storeId);
                            // Store Featured Section
                            // Store Email Template
                            $this->loadModel('EmailTemplate');
                            $this->loadModel('DefaultTemplate');
                            $emailData = $this->DefaultTemplate->getAllDetail();
                            foreach ($emailData as $eData) {
                                unset($eData['DefaultTemplate']['id'], $eData['DefaultTemplate']['is_active'], $eData['DefaultTemplate']['is_deleted'], $eData['DefaultTemplate']['created'], $eData['DefaultTemplate']['modified']);
                                $eData['DefaultTemplate']['store_id'] = $storeId;
                                $eData['DefaultTemplate']['merchant_id'] = $this->request->data['Merchant']['id'];
                                $emailTemp['EmailTemplate'] = $eData['DefaultTemplate'];
                                $this->EmailTemplate->create();
                                $this->EmailTemplate->saveTemplate($emailTemp);
                            }

                            //Store Availbility
                            $daysarr = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
                            $starttime = "06:30";
                            $endtime = "23:30";
                            $StoreAvailabilityData = array();
                            foreach ($daysarr as $day) {
                                $StoreAvailabilityData['store_id'] = $storeId;
                                $StoreAvailabilityData['day_name'] = $day;
                                $StoreAvailabilityData['start_time'] = $starttime;
                                $StoreAvailabilityData['end_time'] = $endtime;
                                $StoreAvailabilityData['id'] = '';
                                $this->StoreAvailability->saveStoreAvailabilityInfo($StoreAvailabilityData);
                            }
                            //Store Availbility
                            // Permission for admin user
                            $tabid = $this->Tab->getTabData('Staff Management', null, null, $roleId);
                            $data['tab_id'] = $tabid;
                            $data['user_id'] = $userID;
                            $data['is_deleted'] = 0;
                            $this->Permission->savePermission($data);
                            // Permission for admin user
                            // Entry in Module table for Routes
                            /*  Store FrontPage    */
                            $routedata['id'] = "";
                            $routedata['merchant_id'] = $this->request->data['Merchant']['id'];
                            $routedata['store_id'] = $storeId;
                            $routedata['type'] = 4;
                            $routedata['subdomain'] = $this->request->data['Store']['store_url'] . "/";
                            $routedata['displayController'] = "users";
                            $routedata['displayAction'] = "store";
                            $routedata['is_deleted'] = 0;
                            $this->Module->saveRouteData($routedata);
                            /*  Store FrontPage    */
                            /*  Store Admin Login    */
                            $routedata['type'] = 3;
                            $routedata['subdomain'] = $this->request->data['Store']['store_url'] . "/admin";
                            $routedata['displayController'] = "stores";
                            $routedata['displayAction'] = "store";
                            $this->Module->saveRouteData($routedata);
                            /*  Store Admin Login    */
                            // Entry in Module table for Routes
                            // Entry in Routes File
                            $storeUrl = trim($this->request->data['Store']['store_url']);
                            $routesStr = "##" . $storeUrl . "##start
                            Router::connect('/$storeUrl', array('controller' => 'users', 'action' => 'store'));
                            Router::connect('/$storeUrl/admin', array('controller' => 'stores', 'action' => 'store'));
                            ##" . $storeUrl . "##end";
                            $filepath = $_SERVER['DOCUMENT_ROOT'] . DS . APP_DIR . DS . 'Config' . DS . 'custom_routes.php';
                            file_put_contents($filepath, $routesStr, FILE_APPEND);
                            // Entry in Routes File

                            /*                             * *******Start mail send********* */
                            if (!empty($this->request->data['User']['email'])) {
                                $this->loadModel('MainSiteSetting');
                                $superEmail = $this->MainSiteSetting->getSiteSettings();
                                $this->loadModel('DefaultTemplate');
                                $template_type = 'new_store';
                                $emailTemplate = $this->DefaultTemplate->adminTemplates($template_type);
                                $randomCode = $this->request->data['User']['password'];
                                if ($emailTemplate) {
                                    if ($this->request->data['User']['fname']) {
                                        $fullName1 = $this->request->data['User']['fname'];
                                        $fullName = ucfirst($fullName1);
                                    }
                                    $userName = $this->request->data['User']['email'];
                                    $emailData = $emailTemplate['DefaultTemplate']['template_message'];
                                    $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                                    $emailData = str_replace('{USERNAME}', $userName, $emailData);
                                    $emailData = str_replace('{PASSWORD}', $randomCode, $emailData);
                                    //  $activationLink = HTTP_ROOT . $this->request->data['Store']['store_url'] . '/admin';
                                    $activationLink = $storeUrl . '/admin';
                                    $emailData = str_replace('{ACTIVE_LINK}', $activationLink, $emailData);
                                    $subject = ucwords(str_replace('_', ' ', $emailTemplate['DefaultTemplate']['template_subject']));
                                    $this->Email->to = $this->request->data['User']['email'];
                                    $this->Email->subject = $subject;
                                    $this->Email->from = $superEmail['MainSiteSetting']['super_email'];
                                    $this->set('data', $emailData);
                                    $this->Email->template = 'template';
                                    //echo $this->smtp_port;die;
                                    $this->Email->smtpOptions = array(
                                        'port' => "$this->smtp_port",
                                        'timeout' => '30',
                                        'host' => "$this->smtp_host",
                                        'username' => "$this->smtp_username",
                                        'password' => "$this->smtp_password"
                                    );
                                    //$this->Email->delivery = "smtp";
                                    $this->Email->sendAs = 'html'; // because we like to send pretty mail
                                    try {
                                        $this->Email->send();
                                    } catch (Exception $e) {

                                    }
                                }
                            }

                            /*                             * *******End mail send********* */

                            // Send Login Credentials to Admin User

                            $this->Session->setFlash(__("Store Details successfully added"), 'alert_success');
                            $this->redirect(array('controller' => 'super', 'action' => 'addStore'));
                        } else {
                            $this->Session->setFlash(__("Unable to save Store admin user details, Please try again"), 'alert_failed');
                        }
                    } else {
                        $this->Session->setFlash(__("Unable to save Store data, Please try again"), 'alert_failed');
                    }
                } else {
                    $this->Session->setFlash(__("Store Email already exists"), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Store name already exists"), 'alert_failed');
            }
        }

        if (!empty($id)) {
            $this->request->data = $store;
        }
        $merchantList = $this->Merchant->getListTotalMerchant();
        $this->set('merchantList', $merchantList);
    }

    /* ------------------------------------------------
      Function name:addDefaultStoreMenu()
      Description:Default Menu add when store Create
      created:29/05/2017
      ----------------------------------------------------- */

    private function _addDefaultStoreMenu($merchant_id = null, $store_id = null) {
        if (!empty($store_id) && !empty($merchant_id)) {
            $this->loadModel('StoreContent');
            $menus = array('Home', 'Place Order', 'Reservations', 'Store Info', 'Photo', 'Reviews', 'Menu', 'Deals', 'Gallery');
            foreach ($menus as $key => $menu) {
                $key = $key + 1;
                $pagedata['name'] = strtoupper($menu);
                $pagedata['content_key'] = 'default_' . strtolower(str_replace(' ', '', $menu));
                $pagedata['page_position'] = 1;
                $pagedata['position'] = $key;
                $pagedata['is_active'] = 1;
                $pagedata['store_id'] = $store_id;
                $pagedata['merchant_id'] = $merchant_id;
                $this->StoreContent->create();
                $this->StoreContent->savePage($pagedata);
            }
        }
    }

    /* ------------------------------------------------
      Function name:storeCreateList()
      Description:List requested store list
      created:15/09/2015
      ----------------------------------------------------- */

    public function storeCreateList($clearAction = null) {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'storeCreateList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";
        $value = "";
        $criteria = "MerchantStoreRequest.is_deleted=0";
        if ($this->Session->read('storeSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('storeSearchData'), true);
        } else {
            $this->Session->delete('storeSearchData');
        }
        if (!empty($this->request->data)) {

            $this->Session->write('storeSearchData', json_encode($this->request->data));

            if (!empty($this->request->data['MerchantStoreRequest']['keyword'])) {
                $value = explode(" ", trim($this->request->data['MerchantStoreRequest']['keyword']));
                // $tags_trimmed = preg_replace('/\s+/', '', $this->request->data['Order']['keyword']);
                $criteria .= " AND (MerchantStoreRequest.store_name LIKE '%" . $value[0] . "%')";
            }

            if (!empty($this->request->data['Status']['id'])) {

                $statusID = trim($this->request->data['Status']['id']);
                $criteria .= " AND (MerchantStoreRequest.request_status =$statusID)";
            }
        }

        $this->MerchantStoreRequest->bindModel(array('belongsTo' => array('Merchant' => array('fields' => array('name'), 'className' => 'Merchant', 'foreignKey' => 'merchant_id'))), false);
        $this->paginate = array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('MerchantStoreRequest.created' => 'DESC'));
        $data = $this->paginate('MerchantStoreRequest');
        $this->set('list', $data);
        $this->set('keyword', $value);
    }

    /* ------------------------------------------------
      Function name: approvedRequest()
      Description: Review approve and disapproved
      created:15/09/2015
      ----------------------------------------------------- */

    public function approvedRequest($EncryptRequestID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "super_dashboard";
        $id = $this->Encryption->decode($EncryptRequestID);
        $this->MerchantStoreRequest->id = $id;
        $this->MerchantStoreRequest->saveField("request_status", $status);
        $this->Session->setFlash(__("Request status updated successfully."), 'alert_success');
        $this->redirect(array('controller' => 'super', 'action' => 'storeCreateList'));
    }

    /* ------------------------------------------------
      Function name:viewMerchantDetails()
      Description:Display merchant list
      created:15/09/2015
      ----------------------------------------------------- */

    public function viewMerchantDetails($clearAction = null) {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addMerchant', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";
        $this->loadModel('Merchant');
        $criteria = "Merchant.is_deleted=0";
        if ($this->request->data) {
            if ($this->request->data['Merchant']['is_active'] != '') {
                $active = trim($this->request->data['Merchant']['is_active']);
                $criteria .= " AND (Merchant.is_active =$active)";
            }
            if (!empty($this->request->data['Merchant']['keyword'])) {
                $value = trim($this->request->data['Merchant']['keyword']);
                $criteria .= " AND (Merchant.name LIKE '%" . $value . "%' OR Merchant.email LIKE '%" . $value . "%' OR Merchant.phone LIKE '%" . $value . "%')";
            }
        }
        $this->Merchant->bindModel(array('hasMany' => array('Store' => array('className' => 'Store', 'foreignKey' => 'merchant_id', 'fields' => array('id')))), false);
        $this->paginate = array('conditions' => array($criteria), 'order' => array('Merchant.created' => 'DESC'));
        $transactionDetail = $this->paginate('Merchant');
        $this->set('list', $transactionDetail);
    }

    public function getMerchantSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('Merchant');
            $superAdminID = $this->Session->read('Auth.Super.id');
            $criteria = "";
            if (!empty($_GET['term'])) {
                $criteria = "Merchant.is_deleted=0";
                $value = trim($_GET['term']);
                $criteria .= " AND (Merchant.name LIKE '%" . $value . "%' OR Merchant.email LIKE '%" . $value . "%' OR Merchant.phone LIKE '%" . $value . "%')";
            }
            $searchData = $this->Merchant->find('all', array('fields' => array('Merchant.name', 'Merchant.email', 'Merchant.phone'), 'conditions' => array($criteria), 'order' => array('Merchant.created' => 'DESC')));
//            prx($searchData);
            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    $new_array[] = array('label' => $val['Merchant']['name'], 'value' => $val['Merchant']['name'], 'desc' => $val['Merchant']['name'] . ' - ' . $val['Merchant']['email'] . ' - ' . $val['Merchant']['phone']);
                };
            }
            echo json_encode($new_array);
        } else {
            exit;
        }
    }

    /* ------------------------------------------------
      Function name:checkMerchantEmail()
      Description:check merchant email
      created:15/09/2015
      ----------------------------------------------------- */

    public function checkMerchantEmail() {
        $this->autoRender = false;
        if ($_GET) {
            $emailEntered = $_GET['data']['User']['email'];
            $emailStatus = $this->User->supermerchantemailExists($emailEntered);
            echo json_encode($emailStatus);
        }
    }

    public function checkMerchantNotificationEmail() {
        $this->autoRender = false;
        if ($_GET) {
            $emailEntered = $_GET['data']['Merchant']['email'];
            $emailStatus = $this->Merchant->merchantemailExists($emailEntered);
            echo json_encode($emailStatus);
        }
    }

    public function checkStoreNotificationEmail() {
        $this->autoRender = false;

        if ($_GET) {
            $emailEntered = $_GET['data']['Store']['email_id'];
            $merchantId = $_GET['merchantId'];
            $emailStatus = $this->Store->storeemailExists($emailEntered, $merchantId);
            echo json_encode($emailStatus);
        }
    }

    public function checkStoreEmail() {
        $this->autoRender = false;
        if ($this->request->query) {
            $emailEntered = $this->request->query['storeemail'];
            $merchantId = $this->request->query['merchantId'];
            $emailStatus = $this->Store->checkStoreEmailExists($emailEntered, $merchantId);
            echo $emailStatus;
            die;
        }
    }

    public function checkAllDomainsMerchant() {
        $this->autoRender = false;
        if ($_GET) {
            $domain = $_GET['data']['Merchant']['domain_name'];
            if (!empty($domain)) {
                $isValid = true;
                $conditions = array('LOWER(Merchant.domain_name)' => strtolower($domain), 'Merchant.is_deleted' => 0, 'Merchant.is_active' => 1);
                if (!empty($_GET['MerchantDomainId'])) {
                    $conditions['Merchant.id !='] = $_GET['MerchantDomainId'];
                }
                $result = $this->Merchant->find('first', array('conditions' => $conditions, 'fields' => array('id')));
                if (empty($result)) {
                    $results = $this->Store->find('first', array('conditions' => array('LOWER(Store.store_url)' => strtolower($domain), 'Store.is_deleted' => 0, 'Store.is_active' => 1), 'fields' => array('id')));
                    if (!empty($results)) {
                        $isValid = false;
                    }
                } else {
                    $isValid = false;
                }
                echo json_encode($isValid);
            }
        }
    }

    public function checkAllDomainsStore() {
        $this->autoRender = false;
        if ($_GET) {
            $domain = $_GET['data']['Store']['store_url'];
            if (!empty($domain)) {
                $isValid = true;
                $conditions = array('LOWER(Store.store_url)' => strtolower($domain), 'Store.is_deleted' => 0, 'Store.is_active' => 1);
                if (!empty($_GET['StoreDomainId'])) {
                    $conditions['Store.id !='] = $_GET['StoreDomainId'];
                }
                $result = $this->Store->find('first', array('conditions' => $conditions, 'fields' => array('id')));
                if (empty($result)) {
                    $results = $this->Merchant->find('first', array('conditions' => array('LOWER(Merchant.domain_name)' => strtolower($domain), 'Merchant.is_deleted' => 0, 'Merchant.is_active' => 1), 'fields' => array('id')));
                    if (!empty($results)) {
                        $isValid = false;
                    }
                } else {
                    $isValid = false;
                }
                echo json_encode($isValid);
            }
        }
    }

    /* ------------------------------------------------
      Function name:viewStoreDetails()
      Description:Display store list
      created:16/09/2015
      ----------------------------------------------------- */

    public function viewStoreDetails($clearAction = null) {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addStore', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";
        $this->loadModel('Store');
        $criteria = "Store.is_deleted=0";
        if (!empty($this->request->data)) {
            if ($this->request->data['Store']['is_active'] != '') {
                $active = trim($this->request->data['Store']['is_active']);
                $criteria .= " AND (Store.is_active =$active)";
            }
            if (!empty($this->request->data['Store']['keyword'])) {
                $value = trim($this->request->data['Store']['keyword']);
                $criteria .= " AND (Store.store_name LIKE '%" . $value . "%' OR Store.store_url LIKE '%" . $value . "%' OR Merchant.name LIKE '%" . $value . "%' OR Store.email_id LIKE '%" . $value . "%' OR Store.phone LIKE '%" . $value . "%')";
            }
        }

        $this->Store->bindModel(
                array(
            'belongsTo' => array(
                'Merchant' => array(
                    'className' => 'Merchant',
                    'foreignKey' => 'merchant_id',
                    'conditions' => array('Merchant.is_deleted' => 0, 'Merchant.is_active' => 1),
                    'fields' => array('id', 'name'),
                    'type' => 'INNER'
                )
            )
                ), false
        );





        $this->paginate = array('conditions' => array($criteria), 'order' => array('Store.created' => 'DESC'));
        $transactionDetail = $this->paginate('Store');
        $this->set('list', $transactionDetail);
    }

    public function getStoreSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('Store');
            $this->Store->bindModel(
                    array(
                'belongsTo' => array(
                    'Merchant' => array(
                        'className' => 'Merchant',
                        'foreignKey' => 'merchant_id',
                        'conditions' => array('Merchant.is_deleted' => 0, 'Merchant.is_active' => 1),
                        'fields' => array('id', 'name'),
                        'type' => 'INNER'
                    )
                )
                    ), false
            );
            $criteria = "";
            if (!empty($_GET['term'])) {
                $criteria = "Store.is_deleted=0";
                $value = trim($_GET['term']);
                $criteria .= " AND (Store.store_name LIKE '%" . $value . "%' OR Store.store_url LIKE '%" . $value . "%' OR Merchant.name LIKE '%" . $value . "%' OR Store.email_id LIKE '%" . $value . "%' OR Store.phone LIKE '%" . $value . "%')";
            }
            $searchData = $this->Store->find('all', array('fields' => array('Store.store_name', 'Store.store_url', 'Merchant.name', 'Store.email_id', 'Store.phone'), 'conditions' => array($criteria), 'order' => array('Store.created' => 'DESC')));
            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    $new_array[] = array('label' => $val['Store']['store_name'], 'value' => $val['Store']['store_name'], 'desc' => $val['Store']['store_name'] . ' - ' . $val['Store']['store_url'] . ' - ' . $val['Merchant']['name'] . ' - ' . $val['Store']['email_id'] . ' - ' . $val['Store']['phone']);
                };
            }
            echo json_encode($new_array);
        } else {
            exit;
        }
    }

    /* ------------------------------------------------
      Function name:activateStore()
      Description:Active/deactive Store
      created:16/09/2015
      ----------------------------------------------------- */

    public function activateStore($EncryptStoreID = null, $status = 0) {

        $this->autoRender = false;
        //$this->layout="super_dashboard";
        $data['Store']['id'] = $this->Encryption->decode($EncryptStoreID);
        $data['Store']['is_active'] = $status;
        if ($this->Store->saveStoreInfo($data)) {
            if ($status) {
                $SuccessMsg = "Store Activated";
            } else {
                $SuccessMsg = "Store Deactivated and User will not get Display in the List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'super', 'action' => 'viewStoreDetails'));

            //$this->redirect($this->request->referer());
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'super', 'action' => 'viewStoreDetails'));

            //$this->redirect($this->request->referer());
        }
    }

    /* ------------------------------------------------
      Function name:activateMerchant()
      Description:Active/deactive Merchant
      created:16/09/2015
      ----------------------------------------------------- */

    public function activateMerchant($EncryptCustomerID = null, $status = 0) {

        $this->layout = false;
        $this->autoRender = false;
        $data['Merchant']['id'] = $this->Encryption->decode($EncryptCustomerID);
        $data['Merchant']['is_active'] = $status;
        if ($this->Merchant->saveMerchant($data)) {
            if ($status) {
                $SuccessMsg = "Merchant Activated";
            } else {
                $SuccessMsg = "Merchant Deactivated and User will not get Display in the List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'super', 'action' => 'viewMerchantDetails'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'super', 'action' => 'viewMerchantDetails'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteStore()
      Description:Delete store
      created:16/09/2015
      ----------------------------------------------------- */

    public function deleteStore($EncryptedStoreID = null) {
        $this->autoRender = false;
        $this->layout = "super_dashboard";
        $data['Store']['id'] = $this->Encryption->decode($EncryptedStoreID);
        $data['Store']['is_deleted'] = 1;
        if ($this->Store->saveStoreInfo($data)) {
            $this->Module->updateAll(array('Module.is_delete' => 1), array('Module.store_id' => $data['Store']['id']));
            $this->User->updateAll(array('User.is_deleted' => 1), array('User.store_id' => $data['Store']['id']));

            $this->Session->setFlash(__("Store deleted"), 'alert_success');
            $this->redirect(array('controller' => 'super', 'action' => 'viewStoreDetails'));
            //$this->redirect($this->request->referer());
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'super', 'action' => 'viewStoreDetails'));
            //$this->redirect($this->request->referer());
        }
    }

    /* ------------------------------------------------
      Function name:deleteMerchant()
      Description:Delete merchant
      created:16/09/2015
      ----------------------------------------------------- */

    public function deleteMerchant($EncryptedMerchantID = null) {
        $this->autoRender = false;
        $this->layout = "super_dashboard";
        $data['Merchant']['id'] = $this->Encryption->decode($EncryptedMerchantID);
        $data['Merchant']['is_deleted'] = 1;
        if ($this->Merchant->saveMerchant($data)) {
            $this->Module->updateAll(array('Module.is_delete' => 1), array('Module.merchant_id' => $data['Merchant']['id']));
            $this->Store->updateAll(array('Store.is_deleted' => 1), array('Store.merchant_id' => $data['Merchant']['id']));
            $this->User->updateAll(array('User.is_deleted' => 1), array('User.merchant_id' => $data['Merchant']['id']));
            $this->Session->setFlash(__("Merchant deleted"), 'alert_success');
            $this->redirect(array('controller' => 'super', 'action' => 'viewMerchantDetails'));
            //$this->redirect($this->request->referer());
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'super', 'action' => 'viewMerchantDetails'));
            //$this->redirect($this->request->referer());
        }
    }

    /* ------------------------------------------------
      Function name:editMerchant()
      Description:edit Merchant Details
      created:15/9/2015
      ----------------------------------------------------- */

    public function editMerchant($EncryptMerchantID = null) {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addMerchant', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";
        if ($EncryptMerchantID) {
            $data['Merchant']['id'] = $this->Encryption->decode($EncryptMerchantID);
        } else {
            $data['Merchant']['id'] = $this->request->data['Merchant']['id'];
        }

        $this->Merchant->bindModel(array('belongsTo' => array('User' => array('className' => 'User', 'foreignKey' => 'user_id'))), false);
        $this->Merchant->bindModel(array('hasOne' => array('Module' => array('className' => 'Module', 'conditions' =>
                    array('Module.merchant_id' => $data['Merchant']['id'], 'Module.type' => 2, 'Module.is_active' => 1, 'Module.is_delete' => 0)))), false);

        $this->loadModel('Merchant');

        $merchantDetail = $this->Merchant->getMerchantDetail($data['Merchant']['id']);
        if (!empty($this->request->data)) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $this->request->data['Merchant']['domain_name'] = trim($this->request->data['Merchant']['domain_name']);
            if ($this->Merchant->checkMerchantUniqueName($this->request->data['Merchant']['name'], $this->request->data['Merchant']['id'])) {
                if ($this->Merchant->checkMerchantUniqueEmail($this->request->data['Merchant']['email'], $this->request->data['Merchant']['id'])) {

                    $roleId = 2; //Merchant User Role Id
                    if ($this->User->supermailExists($this->request->data['User']['email'], $roleId, $this->request->data['User']['id'], $this->request->data['Merchant']['id'])) {
                        if ($this->Merchant->saveMerchant($this->request->data['Merchant'])) {
                            $this->request->data['User']['role_id'] = $roleId;
                            if ($this->User->saveUserInfo($this->request->data['User'])) {
                                if ($merchantDetail['Merchant']['domain_name']) {
                                    $moduleType5 = $this->Module->find('first', array('fields' => array('id'), 'conditions' => array('Module.merchant_id' => $this->request->data['Merchant']['id'], 'Module.type' => 5, 'Module.is_active' => 1, 'Module.is_delete' => 0)));
                                    $moduleType2 = $this->Module->find('first', array('fields' => array('id'), 'conditions' => array('Module.merchant_id' => $this->request->data['Merchant']['id'], 'Module.type' => 2, 'Module.is_active' => 1, 'Module.is_delete' => 0)));
                                    // Entry in Module table for Routes
                                    /*  HQ FrontPage    */
                                    if (!empty($moduleType5)) {
                                        $routedata['id'] = $moduleType5['Module']['id'];
                                    } else {
                                        $routedata['id'] = "";
                                    }
                                    $routedata['merchant_id'] = $this->request->data['Merchant']['id'];
                                    $routedata['type'] = 5;
                                    $routedata['subdomain'] = $this->request->data['Merchant']['domain_name'] . "/";
                                    $routedata['is_ssl'] = @$this->request->data['Module']['is_ssl'];
                                    $routedata['is_www'] = @$this->request->data['Module']['is_www'];
                                    $routedata['displayController'] = "hqusers";
                                    $routedata['displayAction'] = "merchant";
                                    $routedata['is_deleted'] = 0;
                                    $this->Module->saveRouteData($routedata);
                                    /*  HQ FrontPage    */
                                    /*  HQ Login    */
                                    if (!empty($moduleType2)) {
                                        $routedata['id'] = $moduleType2['Module']['id'];
                                    } else {
                                        $routedata['id'] = "";
                                    }
                                    $routedata['type'] = 2;
                                    $routedata['subdomain'] = $this->request->data['Merchant']['domain_name'] . "/hq/";
                                    $routedata['displayController'] = "hq";
                                    $routedata['displayAction'] = "login";
                                    $this->Module->saveRouteData($routedata);
                                    /*  HQ Login    */
                                    // Entry in Module table for Routes
                                    // Entry in Routes File
                                    $hqUrl = trim($this->request->data['Merchant']['domain_name']);
                                    $filepath = $_SERVER['DOCUMENT_ROOT'] . DS . APP_DIR . DS . 'Config' . DS . 'custom_routes.php';
                                    $stringToReplace1 = "/hq/" . $merchantDetail['Merchant']['domain_name'];
                                    $stringToReplace2 = "##" . $merchantDetail['Merchant']['domain_name'] . "##";
                                    $stringfromReplace1 = "/hq/" . $hqUrl;
                                    $stringfromReplace2 = "##" . $hqUrl . "##";
                                    $str = file_get_contents($filepath);
                                    $str = str_replace($stringToReplace1, $stringfromReplace1, $str);
                                    $str = str_replace($stringToReplace2, $stringfromReplace2, $str);
                                    file_put_contents($filepath, $str);
                                    // Entry in Routes File
                                }
                                $this->Session->setFlash(__("Merchant Details successfully updated"), 'alert_success');
                                $this->redirect(array('controller' => 'super', 'action' => 'viewMerchantDetails'));
                            } else {
                                $this->Session->setFlash(__("Unable to save Merchant admin user details, Please try again"), 'alert_failed');
                            }
                        } else {
                            $this->Session->setFlash(__("Unable to save Merchant data, Please try again"), 'alert_failed');
                        }
                    } else {
                        $this->Session->setFlash(__("Merchant Admin user Email already exists"), 'alert_failed');
                    }
                } else {
                    $this->Session->setFlash(__("Merchant Email already exists"), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Merchant name already exists"), 'alert_failed');
            }
        }

        $this->request->data = $merchantDetail;
    }

    /* ------------------------------------------------
      Function name:editStore()
      Description:add Store Details
      created:15/9/2015
      ----------------------------------------------------- */

    public function editStore($EncryptStoreID = null) {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addStore', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";
        $data['Store']['id'] = $this->Encryption->decode($EncryptStoreID);

        $this->Store->bindModel(array('belongsTo' => array('User' => array('className' => 'User', 'foreignKey' => 'user_id'))), false);
        $this->Store->bindModel(array('hasOne' => array('Module' => array('className' => 'Module', 'conditions' =>
                    array('Module.store_id' => $data['Store']['id'], 'Module.type' => 4, 'Module.is_active' => 1, 'Module.is_delete' => 0)))), false);

        $this->loadModel('Store');
        $storeDetail = $this->Store->getStoreDetail($data['Store']['id']);

        if (!empty($this->request->data)) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $this->request->data['Store']['store_url'] = trim($this->request->data['Store']['store_url']);
            if ($this->Store->checkSuperStoreUniqueName($this->request->data['Store']['store_name'], $this->request->data['Store']['merchant_id'], $this->request->data['Store']['id'])) {
                if ($this->Store->checkSuperStoreUniqueEmail($this->request->data['Store']['email_id'], $this->request->data['Store']['id'])) {

                    $roleId = 3; //Merchant User Role Id
                    if ($this->User->supermailExists($this->request->data['User']['email'], $roleId, $this->request->data['User']['id'], $this->request->data['Store']['id'])) {
                        $latitude = "";
                        $longitude = "";
                        if (trim($this->request->data['Store']['address']) && trim($this->request->data['Store']['city']) && trim($this->request->data['Store']['state']) && trim($this->request->data['Store']['zipcode'])) {
                            $dlocation = trim($this->request->data['Store']['address']) . " " . trim($this->request->data['Store']['city']) . " " . trim($this->request->data['Store']['state']) . " " . trim($this->request->data['Store']['zipcode']);
                            $address2 = str_replace(' ', '+', $dlocation);
                            $geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?key='.GOOGLE_MAP_API_KEY.'&address=' . urlencode($address2) . '&sensor=false');
                            $output = json_decode($geocode);
                            if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {

                            } else {
                                $latitude = @$output->results[0]->geometry->location->lat;
                                $longitude = @$output->results[0]->geometry->location->lng;
                            }
                        }
                        if ($latitude && $longitude) {
                            $this->request->data['Store']['latitude'] = $latitude;
                            $this->request->data['Store']['logitude'] = $longitude;
                        }
                        if ($this->Store->saveStoreInfo($this->request->data['Store'])) {
                            $this->request->data['User']['role_id'] = $roleId;
                            if ($this->User->saveUserInfo($this->request->data['User'])) {
                                $moduleType4 = $this->Module->find('first', array('fields' => array('id'), 'conditions' => array('Module.merchant_id' => $this->request->data['Store']['merchant_id'], 'Module.store_id' => $this->request->data['Store']['id'], 'Module.type' => 4, 'Module.is_active' => 1, 'Module.is_delete' => 0)));
                                $moduleType3 = $this->Module->find('first', array('fields' => array('id'), 'conditions' => array('Module.merchant_id' => $this->request->data['Store']['merchant_id'], 'Module.store_id' => $this->request->data['Store']['id'], 'Module.type' => 3, 'Module.is_active' => 1, 'Module.is_delete' => 0)));
                                // Entry in Module table for Routes
                                /*  Store FrontPage    */
                                if (!empty($moduleType4)) {
                                    $routedata['id'] = $moduleType4['Module']['id'];
                                } else {
                                    $routedata['id'] = "";
                                }
                                $routedata['merchant_id'] = $this->request->data['Store']['merchant_id'];
                                $routedata['store_id'] = $this->request->data['Store']['id'];
                                $routedata['type'] = 4;
                                $routedata['is_ssl'] = $this->request->data['Module']['is_ssl'];
                                $routedata['is_www'] = $this->request->data['Module']['is_www'];
                                $routedata['subdomain'] = $this->request->data['Store']['store_url'] . "/";
                                $routedata['displayController'] = "users";
                                $routedata['displayAction'] = "store";
                                $routedata['is_deleted'] = 0;

                                $this->Module->saveRouteData($routedata);
                                /*  Store FrontPage    */
                                /*  Store Admin Login    */
                                if (!empty($moduleType3)) {
                                    $routedata['id'] = $moduleType3['Module']['id'];
                                } else {
                                    $routedata['id'] = "";
                                }
                                $routedata['type'] = 3;
                                $routedata['subdomain'] = $this->request->data['Store']['store_url'] . "/admin";
                                $routedata['displayController'] = "stores";
                                $routedata['displayAction'] = "store";
                                $this->Module->saveRouteData($routedata);
                                /*  Store Admin Login    */

                                // Entry in Routes File
//                                $storeUrl = trim($this->request->data['Store']['store_url']);
//
//                                if ($storeUrl) {
//                                    // Entry in Routes File
//                                    $filepath = $_SERVER['DOCUMENT_ROOT'] . DS . APP_DIR . DS . 'Config' . DS . 'custom_routes.php';
//                                    $stringToReplace1 = "/" . $storeDetail['Store']['store_url'];
//                                    $stringToReplace2 = "/" . $storeDetail['Store']['store_url'] . "/admin";
//                                    $stringToReplace3 = "##" . $storeDetail['Store']['store_url'] . "##start";
//                                    $stringToReplace4 = "##" . $storeDetail['Store']['store_url'] . "##end";
//                                    $stringfromReplace1 = "/" . $storeUrl;
//                                    $stringfromReplace2 = "/" . $storeUrl . "/admin";
//                                    $stringfromReplace3 = "##" . $storeUrl . "##start";
//                                    $stringfromReplace4 = "##" . $storeUrl . "##end";
//                                    $str = file_get_contents($filepath);
//                                    $str = str_replace($stringToReplace1, $stringfromReplace1, $str);
//                                    $str = str_replace($stringToReplace2, $stringfromReplace2, $str);
//                                    $str = str_replace($stringToReplace3, $stringfromReplace3, $str);
//                                    $str = str_replace($stringToReplace4, $stringfromReplace4, $str);
//                                    file_put_contents($filepath, $str);
//                                    // Entry in Routes File
//                                }
                                $this->Session->setFlash(__("Store Details successfully updated"), 'alert_success');
                                $this->redirect(array('controller' => 'super', 'action' => 'editStore/' . $EncryptStoreID));
                            } else {
                                $this->Session->setFlash(__("Unable to save Store admin user details, Please try again"), 'alert_failed');
                            }
                        } else {
                            $this->Session->setFlash(__("Unable to save Store data, Please try again"), 'alert_failed');
                        }
                    } else {
                        $this->Session->setFlash(__("Store Admin user Email already exists"), 'alert_failed');
                    }
                } else {
                    $this->Session->setFlash(__("Store Email already exists"), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Store name already exists"), 'alert_failed');
            }
        }
        $merchantList = $this->Merchant->getListTotalMerchant();
        $this->set('merchantList', $merchantList);
        $this->request->data = $storeDetail;
    }

    /* ------------------------------------------------
      Function name:configuration()
      Description:Add paypal configuration settings
      created:17/09/2015
      ----------------------------------------------------- */

    public function configuration() {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'configuration', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";
        $this->loadModel('MainSiteSetting');
        $data = '';
        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $this->MainSiteSetting->saveConfiguration($this->request->data);
            $this->Session->setFlash(__("Smtp configuration successfully updated"), 'alert_success');
        }
        $configInfo = $this->MainSiteSetting->getSiteSettings();
        $this->request->data = $configInfo;
    }

    /* ------------------------------------------------
      Function name:orderHistory()
      Description:Display the customer all orders
      created:18/8/2015
      ----------------------------------------------------- */

    public function orderHistory($EncryptCustomerID = null) {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'customerList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";
        $userId = $this->Encryption->decode($EncryptCustomerID);
        $userDetail = $this->User->findById($userId, array('fname', 'lname', 'email', 'phone'));
        $this->set(compact('userDetail', 'EncryptCustomerID'));
    }

    public function ajaxRequest($id = '') {
        $this->autoRender = false;
        $this->loadModel('OrderStatus');
        $this->layout = "super_dashboard";
        if (!empty($this->request->params['requested'])) {
            $data = $this->OrderStatus->find('first', array('conditions' => array('OrderStatus.id' => $id)));
            echo $data['OrderStatus']['name'];
        }
    }

    public function orderDetail() {
        $this->layout = "super_dashboard";
        $EncryptCustomerID = $_GET['cId'];
        $userId = $this->Encryption->decode($EncryptCustomerID);
        $this->loadModel('Order');
        $this->Order->bindModel(
                array(
            'belongsTo' => array(
                //'User' => array('className' => 'User', 'foreignKey' => 'user_id'),
                'Segment' => array('className' => 'Segment', 'foreignKey' => 'seqment_id'),
                'OrderStatus' => array('fields' => array('name')),
                'OrderPayment' => array(
                    'className' => 'OrderPayment',
                    'foreignKey' => 'payment_id',
                    'fields' => array('id', 'transection_id', 'amount', 'payment_gateway'),
                ))), false);
        $fields = array('Order.store_id', 'Order.created', 'Order.id', 'Order.order_number', 'Order.amount', 'Order.coupon_discount', 'Order.user_id', 'OrderStatus.name', 'Segment.name');
        $this->paginate = array('fields' => @$fields, 'conditions' => array('Order.user_id' => $userId, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0), 'order' => array('Order.created' => 'DESC'));
        $orderDetails = $this->paginate('Order');
        $this->set(compact('orderDetails', 'EncryptCustomerID'));
    }

    public function reviewDetail() {
        $this->layout = "super_dashboard";
        $EncryptCustomerID = $_GET['cId'];
        $userId = $this->Encryption->decode($EncryptCustomerID);
        $this->loadModel('StoreReview');
        $this->loadModel('OrderItem');
        $this->OrderItem->bindModel(array('belongsTo' => array('Item' => array('className' => 'Item', 'fields' => array('name')))), false);
        $this->StoreReview->bindModel(array('belongsTo' => array('OrderItem' => array('className' => 'OrderItem', 'fields' => array('id', 'order_id', 'item_id')))), false);
        $this->paginate = array('fields' => array('StoreReview.created', 'StoreReview.store_id', 'StoreReview.review_comment', 'StoreReview.review_rating', 'StoreReview.order_id', 'StoreReview.id', 'StoreReview.order_item_id'), 'recursive' => 2, 'conditions' => array('StoreReview.is_deleted' => 0, 'StoreReview.user_id' => $userId));
        $myReviews = $this->paginate('StoreReview');
        $this->set(compact('orderDetails', 'EncryptCustomerID', 'myReviews'));
    }

    public function reservationDetail() {
        $this->layout = "super_dashboard";
        $EncryptCustomerID = $_GET['cId'];
        $userId = $this->Encryption->decode($EncryptCustomerID);
        $this->loadModel('Booking');
        $this->Booking->bindModel(array('belongsTo' => array('BookingStatus')), false);
        $this->paginate = array('fields' => array('Booking.store_id', 'BookingStatus.name', 'Booking.special_request', 'Booking.id', 'Booking.number_person', 'Booking.reservation_date'), 'conditions' => array('Booking.user_id' => $userId, 'Booking.is_active' => 1, 'Booking.is_deleted' => 0));
        $myBookings = $this->paginate('Booking');
        $this->set(compact('orderDetails', 'EncryptCustomerID', 'myBookings'));
    }

    public function customerOrderDetail($order_id = null) {
        $this->layout = "super_dashboard";
        $orderId = $this->Encryption->decode($order_id);
        $this->loadModel('OrderItemFree');
        $this->loadModel('OrderPreference');
        $this->loadModel('OrderOffer');
        $this->loadModel('OrderTopping');
        $this->loadModel('OrderItem');
        $this->loadModel('Order');
        $this->OrderItemFree->bindModel(array('belongsTo' => array('Item' => array('fields' =>
                    array('id', 'name')))), false);
        $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('className' => 'Item', 'foreignKey' => 'offered_item_id', 'fields' => array('id', 'name')), 'Size' => array('className' => 'Size', 'foreignKey' => 'offered_size_id', 'fields' => array('id', 'size')))), false);
        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('className' => 'Topping', 'foreignKey' => 'topping_id', 'fields' => array('id', 'name')))), false);
        $this->OrderItem->bindModel(array('hasMany' => array('OrderTopping' => array('fields' => array('id', 'topping_id', 'addon_size_id'), 'order' => array('OrderTopping.id')), 'OrderOffer' => array('fields' => array('id', 'offered_item_id', 'offered_size_id', 'quantity')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id', 'size'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('id', 'name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('id', 'size')))), false);
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
                'User' => array('className' => 'User', 'foreignKey' => 'user_id'),
                'Segment' => array('className' => 'Segment', 'foreignKey' => 'seqment_id'),
                'DeliveryAddress' => array('className' => 'DeliveryAddress', 'foreignKey' => 'delivery_address_id'),
                'OrderStatus' => array('fields' => array('id', 'name')),
                'OrderPayment' => array(
                    'className' => 'OrderPayment',
                    'foreignKey' => 'payment_id',
                    'fields' => array('id', 'transection_id', 'amount', 'payment_gateway', 'payment_status', 'last_digit'),
                ))), false);
        $orderDetails = $this->Order->getsuperSingleOrderDetail(null, null, $orderId);
        $this->set('orderDetail', $orderDetails);
        $this->loadModel('OrderStatus');
        $statusList = $this->OrderStatus->OrderStatusList(null);
        $this->set('statusList', $statusList);
        $referer = $this->referer();
        if ($this->referer() != "/") {
            $this->Session->write("ref", $referer);
        }
    }

    public function paymentDownload($merchantId = null) {
        $criteria = "MerchantPayment.is_deleted=0";
        if (!empty($merchantId)) {
            $criteria .= " AND (MerchantPayment.merchant_id ='" . $merchantId . "')";
        }
        $this->MerchantPayment->bindModel(array('belongsTo' => array('Plan' => array('fields' => array('name'), 'className' => 'Plan', 'foreignKey' => 'plan_id'), 'Merchant' => array('fields' => array('name'), 'className' => 'Merchant', 'foreignKey' => 'merchant_id'))), false);
        $list = $this->MerchantPayment->find('all', array('conditions' => array($criteria), 'order' => array('MerchantPayment.created' => 'DESC')));

        Configure::write('debug', 0);
        App::import('Vendor', 'PHPExcel');
        $objPHPExcel = new PHPExcel;
        ;
        $styleArray2 = array(
            'font' => array('name' => 'Arial', 'size' => '10', 'color' => array('rgb' => '444555'), 'bold' => true),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'D6D6D6'))
        );
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
        $filename = 'Payment_Download' . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('Payment Report');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Subscription Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Merchant Name');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Subscription Type');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Payment Date');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Amount($)');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Status');

        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);

        $i = 2;
        foreach ($list as $data) {
            $data = $this->Common->trimValue($data);
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", $data['MerchantPayment']['id']);
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", $data['Merchant']['name']);
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", $data['Plan']['name']);
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", date('m-d-Y', strtotime($data['MerchantPayment']['payment_date'])));
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", $data['MerchantPayment']['amount']);
            $objPHPExcel->getActiveSheet()->setCellValue("F$i", $data['MerchantPayment']['payment_status']);

            $i++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    function storeList($EncryptMerchantID = null) {
        $this->loadModel('Store');
        $this->layout = "super_dashboard";
        $MerchantID = $this->Encryption->decode($EncryptMerchantID);
        $storeData = $this->Store->getAllMerchantStores($MerchantID);
        $this->set(compact('storeData'));
    }

    /* ------------------------------------------------
      Function name:transaction_permission()
      Description:show Active/deactive store transaction permission
      created:05/09/2016
      ----------------------------------------------------- */

    public function transaction_permission() {
        $this->layout = "super_dashboard";
        $loginuserid = $this->Session->read('Auth.Super.id');
        $this->loadModel('Store');
        $criteria = "Store.is_deleted=0";
        if (!empty($this->request->data['Merchant']['id'])) {
            $criteria .= " AND Store.merchant_id=" . $this->request->data['Merchant']['id'];
        }
        if (isset($this->request->data['Store']['is_allow_transaction']) && $this->request->data['Store']['is_allow_transaction'] != '') {
            $criteria .= " AND Store.is_allow_transaction=" . $this->request->data['Store']['is_allow_transaction'];
        }
        $this->Store->bindModel(array('belongsTo' => array('Merchant' => array('className' => 'Merchant', 'foreignKey' => 'merchant_id', 'fields' => array('Merchant.name')))), false);
        $this->Store->unbindModel(array('belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent'), 'hasOne' => array('SocialMedia')), true);
        $this->paginate = array('fields' => array('Store.id', 'Store.store_name', 'Merchant.name', 'Store.is_active', 'Store.created', 'Store.is_allow_transaction', 'Store.merchant_id', 'Merchant.id'), 'conditions' => array($criteria), 'order' => array('Store.created' => 'DESC',));
        $transactionDetail = $this->paginate('Store');
        //pr($transactionDetail);die;
        $this->set('list', $transactionDetail);
        $merchantList = $this->Merchant->getListTotalMerchant();
        $this->set('merchantList', $merchantList);
    }

    /* ------------------------------------------------
      Function name:activateStoreTransactionPermisssion()
      Description:Active/deactive store transaction permission
      created:16/09/2015
      ----------------------------------------------------- */

    public function activateStoreTransactionPermisssion($EncryptStoreID = null, $status = 0) {
        $this->layout = false;
        $this->autoRender = false;
        $data['Store']['id'] = $this->Encryption->decode($EncryptStoreID);
        $data['Store']['is_allow_transaction'] = $status;
        $this->loadModel('Store');
        if ($this->Store->saveStoreInfo($data)) {
            if ($status) {
                $SuccessMsg = "Store transaction permission activated";
            } else {
                $SuccessMsg = "Store transaction permission deactive";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'super', 'action' => 'transaction_permission'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'super', 'action' => 'transaction_permission'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteMerchant()
      Description:Delete merchant
      created:22/09/2016
      ----------------------------------------------------- */

    public function deleteMerchantPayment($EncryptedMerchantPaymentID = null) {
        $this->autoRender = false;
        $this->layout = "super_dashboard";
        $data['MerchantPayment']['id'] = $this->Encryption->decode($EncryptedMerchantPaymentID);
        $data['MerchantPayment']['is_deleted'] = 1;
        if ($this->MerchantPayment->savePayment($data)) {
            $this->Session->setFlash(__("Merchant payment deleted"), 'alert_success');
            $this->redirect(array('controller' => 'super', 'action' => 'merchantPaymentList'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'super', 'action' => 'merchantPaymentList'));
        }
    }

    public function checkMerchantNumber($merchantno = null) {
        $this->autoRender = false;
        if ($_GET) {
            $numberEntered = $_GET['data']['Store']['merchant_number'];
            //$storeID = @$_GET['data']['Store']['StoreDbId'];
            if (isset($_GET['StoreDbId'])) {
                $storeID = @$_GET['StoreDbId'];
            } else {
                $storeID = null;
            }
            $numberStatus = $this->Store->checkmercNumber($numberEntered, $storeID);
            echo json_encode($numberStatus);
        }
    }

    public function storeConfiguration($storeId = null) {
        $this->layout = "super_dashboard";
        $storeId = $this->Encryption->decode($storeId);
        $count = $this->Store->find('count', array('conditions' => array('Store.id' => $storeId)));
        if ($count) {
            $this->loadModel('StoreSetting');
            $this->loadModel('ModulePermission');
            if ($this->request->is(array('post', 'put'))) {
                $this->StoreSetting->save($this->request->data['StoreSetting']);
                $this->ModulePermission->save($this->request->data['ModulePermission']);
                $this->Session->setFlash(__("Update Successfully."), 'alert_success');
                //update store table
                $this->loadModel('Store');
                if (empty($this->request->data['StoreSetting']['delivery_allow'])) {
                    $this->Store->updateAll(array('is_delivery' => 0, 'is_delivery_beftax' => 0), array('id' => $storeId));
                }
                if (empty($this->request->data['StoreSetting']['before_tax_delivery'])) {
                    $this->Store->updateAll(array('is_delivery_beftax' => 0), array('id' => $storeId));
                }
                if (empty($this->request->data['StoreSetting']['pickup_allow'])) {
                    $this->Store->updateAll(array('is_take_away' => 0, 'is_pick_beftax' => 0), array('id' => $storeId));
                }
                if (empty($this->request->data['StoreSetting']['before_tax_pickup'])) {
                    $this->Store->updateAll(array('is_pick_beftax' => 0), array('id' => $storeId));
                }
                if (empty($this->request->data['StoreSetting']['reservations_allow'])) {
                    $this->Store->updateAll(array('is_booking_open' => 0), array('id' => $storeId));
                }
                $this->redirect($this->referer());
            }
            $settingData = $this->StoreSetting->findByStoreId($storeId);
            if (empty($settingData)) {
                $data['store_id'] = $storeId;
                $this->StoreSetting->save($data);
                $this->ModulePermission->save($data);
                $ssData = $this->StoreSetting->findByStoreId($storeId);
                $mpData = $this->ModulePermission->findByStoreId($storeId);
            } else {
                $ssData = $settingData;
                $mpData = $this->ModulePermission->findByStoreId($storeId);
            }

            $this->set(compact('ssData', 'mpData'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect($this->referer());
        }
    }

    public function defaultTemplate($clearAction = null) {
        $this->layout = "super_dashboard";
        $this->loadModel('DefaultTemplate');
        $criteria = "DefaultTemplate.is_deleted=0";
        if ($this->Session->read('DefaultTemplate') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('TemplateSearchData'), true);
        } else {
            $this->Session->delete('DefaultTemplate');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('DefaultTemplate', json_encode($this->request->data));
            if (isset($this->request->data['DefaultTemplate']['is_active']) && $this->request->data['DefaultTemplate']['is_active'] != '') {
                $active = trim($this->request->data['DefaultTemplate']['is_active']);
                $criteria .= " AND (DefaultTemplate.is_active` ='" . $active . "')";
            }
            if (!empty($this->request->data['DefaultTemplate']['search'])) {
                $search = trim($this->request->data['DefaultTemplate']['search']);
                $criteria .= " AND (DefaultTemplate.template_subject LIKE '%" . $search . "%')";
            }
        }
        $this->paginate = array('conditions' => array($criteria));
        $templateDetail = $this->paginate('DefaultTemplate');
//        pr($templateDetail);die;
        $this->set('list', $templateDetail);
    }

    /* ------------------------------------------------
      Function name:editTemplate()
      Description:Edit Template contents
      created:24/8/2015
      ----------------------------------------------------- */

    public function editTemplate($EncryptTemplateID = null) {
        $this->layout = "super_dashboard";
        $data['DefaultTemplate']['id'] = $this->Encryption->decode($EncryptTemplateID);
        $this->loadModel('DefaultTemplate');
        $templateDetail = $this->DefaultTemplate->findById($data['DefaultTemplate']['id']);
        if ($this->request->data) {
            $templateTitle = trim($this->data['DefaultTemplate']['template_subject']);
            $templatedata = array();
            $templatedata['template_subject'] = trim($this->data['DefaultTemplate']['template_subject']);
            $templatedata['id'] = trim($this->data['DefaultTemplate']['id']);
            $templatedata['template_message'] = trim($this->data['DefaultTemplate']['template_message']);
            if (empty($this->data['DefaultTemplate']['sms_template'])) {
                $this->request->data['DefaultTemplate']['sms_template'] = '';
            }
            $templatedata['sms_template'] = trim($this->data['DefaultTemplate']['sms_template']);
            $templatedata['is_active'] = trim($this->data['DefaultTemplate']['is_active']);
            $this->loadModel('DefaultTemplate');
            $this->DefaultTemplate->save($templatedata);

            $this->Session->setFlash(__("Template Successfully Updated."), 'alert_success');
            $this->redirect(array('controller' => 'super', 'action' => 'DefaultTemplate'));
        }

        $this->request->data = $templateDetail;
    }

    public function viewStorePrinter($clearAction = null) {
        $loginuserid = $this->Session->read('Auth.Super.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addStore', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'super', 'action' => 'dashboard'));
        }
        $this->layout = "super_dashboard";
        $this->loadModel('StorePrinterStatus');
        $this->StorePrinterStatus->bindModel(array('belongsTo' =>
            array('Merchant' => array('className' => 'Merchant', 'foreignKey' => 'merchant_id', 'fields' => array('Merchant.name')),
            'Store' => array('className' => 'Store', 'foreignKey' => 'store_id', 'fields' => array('Store.store_name','Store.id')))
        ), false);
        $result = $this->paginate('StorePrinterStatus');
        $date1 = new DateTime(date('Y-m-d H:i:s'));
        for($i=0; $i<count($result); $i++) {
            $update_date = $result[$i]['StorePrinterStatus']['modified'];
            $date = date('Y-m-d H:i:s', strtotime($update_date));
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

}
