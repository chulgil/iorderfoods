<?php

App::uses('HqAppController', 'Controller');

class HqController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Paginator', 'Common', 'Dateform');
    public $helper = array('Encryption', 'Paginator', 'Form', 'DateformHelper', 'Common');
    public $uses = array('MerchantGallery', 'MerchantContent', 'User', 'StoreGallery', 'Store', 'MerchantStoreRequest', 'Category', 'Tab', 'Permission', 'Merchant', 'StoreReview', 'Plan', 'Merchant', 'StorePayment', 'SocialMedia');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->autoRender = false;
        $this->redirect(array('controller' => 'hq', 'action' => 'login'));
    }

    public function merchant() {
        $this->layout = "merchant";
        $name = $this->merchantName;
        $image = $this->merchantImage;
        $logo = $this->merchantLogo;
        $id = $this->merchantId;
        $store = $this->Store->find('all', array('fields' => array('id', 'store_name', 'store_url', 'merchant_id', 'phone', 'address', 'city', 'state', 'latitude', 'logitude', 'zipcode'), 'conditions' => array('Store.merchant_id' => $id, 'Store.is_deleted' => 0, 'Store.is_active' => 1)));
        $merchantList = $this->MerchantContent->find('all', array('conditions' => array('MerchantContent.merchant_id' => $id, 'MerchantContent.is_active' => 1, 'MerchantContent.is_deleted' => 0), 'order' => array('MerchantContent.position' => 'ASC')));
        $socialLinks = $this->SocialMedia->find('first', array('conditions' => array('merchant_id' => $this->Session->read('hq_id'), 'store_id' => NULL, 'is_active' => 1, 'is_deleted' => 0)));
        $this->set(compact('store', 'name', 'image', 'logo', 'merchantList', 'id', 'socialLinks'));
    }

    public function ajaxStaticContent() {
        $this->layout = false;
        $content = array();
        $store = array();
        $photo = array();
        if ($_POST['typeId'] == 1) {
            $store = $this->Store->find('all', array('fields' => array('store_name', 'store_url', 'phone', 'address', 'city', 'state', 'latitude', 'logitude', 'zipcode'), 'conditions' => array('Store.merchant_id' => $_POST['merchantId'], 'Store.is_deleted' => 0, 'Store.is_active' => 1)));
        } else if ($_POST['typeId'] == 2) {
            $content = $this->MerchantContent->getPageDetail($_POST['contentId'], $_POST['merchantId']);
        } else if ($_POST['typeId'] == 3) {
            $photo = $this->MerchantGallery->getSliderImages($_POST['merchantId']);
        }
        $type = $_POST['typeId'];
        $this->set(compact('type', 'content', 'store', 'photo'));
    }

    public function selectMerchant() {
        $this->layout = false;
    }

    public function login() {
        $this->layout = "hq_login";
        $this->set('title', 'Sign in');
        if ($this->request->is('post')) {
            $this->User->set($this->request->data);
            if ($this->User->validates()) {
                if ($this->data['User']['remember'] == 1) {
                    // Cookie is valid for 7 days
                    $this->Cookie->write('Auth.email', $this->data['User']['email'], false, 604800);
                    $this->Cookie->write('Auth.password', $this->data['User']['password'], false, 604800);
                    $this->set('cookies', '1');
                    unset($this->request->data['User']['remember_me']);
                } else {
                    $this->Cookie->delete('Auth');
                    $this->Cookie->delete('Auth');
                }
                if ($this->Auth->login()) {
                    $merchant_user_id = $this->Session->read('Auth.hq.id');
                    if (!empty($merchant_user_id)) {
                        $this->_checkMerchatIsNotDisabled($merchant_user_id);
                    }
                    $MerchantData = $this->User->currentUserInfo($merchant_user_id);
                    $this->Session->write('merchantId', $MerchantData['User']['merchant_id']);
                    $roleId = $this->Session->read('Auth.hq.role_id'); // ROLE OF THE USER [2=>Merchant]
                    $this->Session->write('login_date_time', date('Y-m-d H:i:s'));
                    if ($roleId == 2) {  // Store admin will redirect to his related dashboard
                        $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
                    } else {
                        $this->redirect(array('controller' => 'hq', 'action' => 'logout'));
                    }
                } else {
                    $this->Session->setFlash(__("Invalid email or password, try again"), 'alert_failed');
                }
            }
        } elseif ($this->Auth->login()) {
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        } else {
            $UserId = $this->Session->read('Auth.hq.id');
            if ($UserId) {
                $this->redirect(array('controller' => 'hq', 'action' => 'logout'));
            }
            $this->set('rem', $this->Cookie->read('Auth.email'));
            if ($this->Cookie->read('Auth.email')) {
                $this->request->data['User']['email'] = $this->Cookie->read('Auth.email');
                $this->request->data['User']['password'] = $this->Cookie->read('Auth.password');
            }
        }
    }

    public function _checkMerchatIsNotDisabled($merchant_user_id) {
        $count = $this->Merchant->find('count', array('conditions' => array('Merchant.user_id' => $merchant_user_id, 'Merchant.is_active' => 0)));
        if ($count == 1) {
            $this->Session->setFlash(__("Merchant is deactivated."), 'alert_failed');
            $this->logout();
        }
    }

    /* ------------------------------------------------
      7Function name:dashboard()
      Description:Dash Board of Store Admin
      created:27/7/2015
      ----------------------------------------------------- */

    public function dashboard() {
        $storeId = "";
        if ($this->request->data) {
            $storeId = $this->request->data['Merchant']['store_id'];
        } else {
            $this->Session->write('selectedStoreId', "");
        }

        $this->set('storeId', $storeId);
        $this->layout = "hq_dashboard";
        $roleId = $this->Session->read('Auth.hq.role_id'); // ROLE OF THE USER [2=>Merchant]
        $merchantId = $this->Session->read('merchantId');
        $this->set('merchantId', $merchantId);
        if ($roleId != 2) {  // Store admin will redirect to his related dashboard
            $this->redirect(array('controller' => 'hq', 'action' => 'logout'));
        }
    }

    /* ------------------------------------------------
      Function name:logout()
      Description:For logout of the user
      created:27/7/2015
      ----------------------------------------------------- */

    public function logout() {
        $this->Session->delete('Auth.hq');
        $this->redirect(array('controller' => 'hq', 'action' => 'login'));
    }

    /* ------------------------------------------------
      Function name:myProfile()
      Description:This section will manage the profile of the user for Store Admin
      created:22/7/2015
      ----------------------------------------------------- */

    public function myProfile($encrypted_storeId = null, $encrypted_merchantId = null) {
        $this->layout = "hq_dashboard";
        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId'));
        $userResult = $this->User->currentUserInfo($this->Session->read('Auth.hq.id'));
        $roleId = $userResult['User']['role_id'];
        $this->User->set($this->request->data);
        if (isset($this->request->data['User']['changepassword']) && !($this->request->data['User']['changepassword'])) {
            $this->User->validator()->remove('password');
            $this->User->validator()->remove('password_match');
        }
        if ($this->User->validates() && $this->request->is('post')) {
            if ($this->request->data['User']['changepassword'] == 1) {
                $oldPassword = AuthComponent::password($this->data['User']['oldpassword']);
                if ($oldPassword != $userResult['User']['password']) {
                    $this->Session->setFlash(__("Please Enter correct old password"), 'alert_failed');
                    $this->redirect(array('controller' => 'hq', 'action' => 'myProfile', $encrypted_storeId, $encrypted_merchantId));
                }
            }
            $this->User->id = $this->Session->read('Auth.hq.id');
            if ($this->User->saveUserInfo($this->request->data['User'])) {
                $this->Session->setFlash(__("Profile has been updated successfully"), 'alert_success');
                $this->redirect(array('controller' => 'hq', 'action' => 'myProfile', $encrypted_storeId, $encrypted_merchantId));
            } else {
                $this->Session->setFlash(__("Profile not updated successfully"), 'alert_failed');
                $this->redirect(array('controller' => 'hq', 'action' => 'myProfile', $encrypted_storeId, $encrypted_merchantId));
            }
        }
        $this->set(compact('roleId'));
        $this->request->data['User'] = $userResult['User'];
    }

    /* ------------------------------------------------
      Function name:dashboard()
      Description:Dash Board of Store Admin
      created:27/7/2015
      ----------------------------------------------------- */

    public function manageStaff($EncrypteduserID = null) {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'manageStaff', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        $userResult = $this->User->currentUserInfo($this->Session->read('Auth.hq.id'));
        $this->set('loginuserid', $loginuserid);
        $roleId = $userResult['User']['role_id'];
        $merchantId = $this->Session->read('merchantId');
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
        $Tabs = $this->Tab->getTabs($this->Session->read('Auth.hq.role_id'));
        $this->set(compact('Tabs'));
        if ($this->User->validates()) {
            if ($this->request->is(array('post', 'put')) && !empty($this->request->data['User']['phone'])) { //pr($this->request->data);die;
                $this->request->data = $this->Common->trimValue($this->request->data);
                if ($this->request->data['User']['id']) {
                    $userdata['User'] = $this->request->data['User'];
                    if ($this->User->saveUserInfo($userdata)) {
                        $this->permission($this->request->data['User']['id'], $this->request->data['Permission']);
                        $this->Session->setFlash(__("Staff member details has been updated successfully"), 'alert_success');
                        $this->redirect(array('controller' => 'hq', 'action' => 'manageStaff'));
                    } else {
                        $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                        $this->redirect(array('controller' => 'hq', 'action' => 'manageStaff'));
                    }
                } elseif ($this->User->merchantemailExists($this->request->data['User']['email'], $roleId, $merchantId) && $this->request->data['User']['id'] == '') {
                    $this->request->data['User']['merchant_id'] = $merchantId;
                    $userdata['User'] = $this->request->data['User'];
                    if ($this->User->saveUserInfo($userdata)) {
                        $userid = $this->User->getLastInsertId();
                        $this->permission($userid, $this->request->data['Permission']);
                        $this->Session->setFlash(__("Staff member has been added successfully"), 'alert_success');
                        $this->redirect(array('controller' => 'hq', 'action' => 'manageStaff'));
                    } else {
                        $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                        $this->redirect(array('controller' => 'hq', 'action' => 'manageStaff'));
                    }
                } else {
                    $this->Session->setFlash(__("Email already exists"), 'alert_failed');
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

        $merchantId = $this->Session->read('merchantId');
        $value = "";
        $criteria = "User.merchant_id =$merchantId AND User.is_deleted=0 AND User.role_id=2";
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
        $this->layout = "hq_dashboard";
        $data['User']['merchant_id'] = $this->Session->read('merchantId');
        $data['User']['id'] = $this->Encryption->decode($EncrypteduserID);
        $data['User']['is_active'] = $status;
        if ($this->User->saveUserInfo($data)) {
            if ($status) {
                $SuccessMsg = "Staff Activated";
            } else {
                $SuccessMsg = "Staff Deactivated and member will not able to log in to system";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hq', 'action' => 'manageStaff'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hq', 'action' => 'manageStaff'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteStaff()
      Description:Delete users
      created:27/7/2015
      ----------------------------------------------------- */

    public function deleteStaff($EncrypteduserID = null) {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $data['User']['merchant_id'] = $this->Session->read('merchantId');
        $data['User']['id'] = $this->Encryption->decode($EncrypteduserID);
        $data['User']['is_deleted'] = 1;
        if ($this->User->saveUserInfo($data)) {
            $this->Session->setFlash(__("User deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hq', 'action' => 'manageStaff'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hq', 'action' => 'manageStaff'));
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
            foreach ($permissiondata as $tab_id) {
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
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'storeRequestList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $value = "";
        $criteria = "MerchantStoreRequest.merchant_id =$merchantId AND MerchantStoreRequest.is_deleted=0";
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
      Function name:storeRequest()
      Description:Request New store
      created:01/9/2015
      ----------------------------------------------------- */

    public function requestNewStore() {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'storeRequestList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $isUniqueName = $this->Store->checkStoreUniqueName($this->request->data['MerchantStoreRequest']['store_name'], $merchantId);
            $isUniqueName = $this->MerchantStoreRequest->checkAlreadyRequested($this->request->data['MerchantStoreRequest']['store_name'], $merchantId);
            if ($isUniqueName) {
                $this->request->data['MerchantStoreRequest']['merchant_id'] = $merchantId;
                if ($this->MerchantStoreRequest->saveStoreRequest($this->request->data)) {
                    $this->Session->setFlash(__("Store request successfully send"), 'alert_success');
                    $this->redirect(array('controller' => 'hq', 'action' => 'storeRequestList'));
                } else {
                    $this->Session->setFlash(__("Some problem occurred"), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Store Name Already Exists"), 'alert_failed');
            }
        }
    }

    /* ------------------------------------------------
      Function name:deleteRequestedStore()
      Description:Dete Requested Store
      created:01/9/2015
      ----------------------------------------------------- */

    public function deleteRequestedStore($EncrypteduserID = null) {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $data['MerchantStoreRequest']['merchant_id'] = $this->Session->read('merchantId');
        $data['MerchantStoreRequest']['id'] = $this->Encryption->decode($EncrypteduserID);
        $data['MerchantStoreRequest']['is_deleted'] = 1;
        if ($this->MerchantStoreRequest->saveStoreRequest($data)) {
            $this->Session->setFlash(__("Store Request deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hq', 'action' => 'storeRequestList'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hq', 'action' => 'storeRequestList'));
        }
    }

    /* ------------------------------------------------
      Function name:manageStoreSliderImages()
      Description:Manage Images for Somepage slider
      created:27/7/2015
      ----------------------------------------------------- */

    public function manageSliderPhotos() {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'manageSliderPhotos', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $storeId = $this->Session->read('selectedStoreId');
        $this->set('merchantId', $merchantId);
        $this->set('storeId', $storeId);
        if ($this->request->data) {
            $storeId = $this->request->data['Store']['store_id'];
            $this->data = $this->Common->trimValue($this->data);
            if ($this->data['StoreGallery']['image']['error'] == 0) {
                $response = $this->Common->uploadMenuItemImages($this->data['StoreGallery']['image'], '/sliderImages/', $storeId, 300, 190);
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
                $data['store_id'] = $storeId;
                $data['merchant_id'] = $merchantId;
                $data['description'] = trim($this->request->data["StoreGallery"]["description"]);
                if ($this->StoreGallery->saveStoreSliderImage($data)) {
                    $this->Session->setFlash(__("File successfully uploaded"), 'alert_success');
                    $this->redirect(array('controller' => 'hq', 'action' => 'manageSliderPhotos'));
                } else {
                    $this->Session->setFlash(__("Some problem occurred"), 'alert_success');
                    $this->redirect(array('controller' => 'hq', 'action' => 'manageSliderPhotos'));
                }
            }
        }
        $sliderImages = $this->StoreGallery->getStoreSliderImages($storeId, $merchantId);
        $this->set('sliderImages', $sliderImages);
    }

    public function getStoreImages() {
        $this->layout = false;
        if ($this->request->is('ajax') && $this->request->data['storeId']) {
            $merchantId = $this->Session->read('merchantId');
            $sliderImages = $this->StoreGallery->getStoreSliderImages($this->request->data['storeId'], $merchantId);
            $this->set('sliderImages', $sliderImages);
        }
    }

    /* ------------------------------------------------
      Function name:manageStoreSliderImages()
      Description:Manage Images for Somepage slider
      created:27/7/2015
      ----------------------------------------------------- */

    public function deleteSliderPhoto($EncryptedImageID = null) {
        $this->layout = "hq_dashboard";
        $imageID = $this->Encryption->decode($EncryptedImageID);
        if ($imageID) {
            $merchantId = $this->Session->read('merchantId');
            $this->set('merchantId', $merchantId);
            $data['id'] = $imageID;
            $data['is_deleted'] = 1;
            if ($this->StoreGallery->saveStoreSliderImage($data)) {
                $this->Session->setFlash(__("Slider photo has been deleted"), 'alert_success');
                $this->redirect(array('controller' => 'hq', 'action' => 'manageSliderPhotos'));
            } else {
                $this->Session->setFlash(__("Some Problem occured"), 'alert_failed');
                $this->redirect(array('controller' => 'hq', 'action' => 'manageSliderPhotos'));
            }
        }
    }

    public function merchantManageSliderPhotos() {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'merchantManageSliderPhotos', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $this->set('merchantId', $merchantId);
        if ($this->request->data) {
            $this->data = $this->Common->trimValue($this->data);
            if ($this->data['MerchantGallery']['image']['error'] == 0) {
                $response = $this->Common->uploadMenuItemImages($this->data['MerchantGallery']['image'], '/merchantSliderImages/', $merchantId, 1300, 600);
            } elseif ($this->data['MerchantGallery']['image']['error'] == 4) {
                $response['status'] = true;
                $response['imagename'] = '';
            }
            if (!$response['status']) {
                $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
            } else {
                if ($response['imagename']) {
                    $data['image'] = $response['imagename'];
                }
                $data['merchant_id'] = $merchantId;
                $data['description'] = trim($this->request->data["MerchantGallery"]["description"]);
		$count = $this->MerchantGallery->find('count',array('conditions'=>array('merchant_id'=>$merchantId)));
                if(!empty($count)){
                    $data['position'] = $count+1;
                }
                if ($this->MerchantGallery->saveSliderImage($data)) {
                    $this->Session->setFlash(__("File successfully uploaded"), 'alert_success');
                    $this->redirect(array('controller' => 'hq', 'action' => 'merchantManageSliderPhotos'));
                } else {
                    $this->Session->setFlash(__("Some problem occurred"), 'alert_success');
                    $this->redirect(array('controller' => 'hq', 'action' => 'merchantManageSliderPhotos'));
                }
            }
        }
        $sliderImages = $this->MerchantGallery->getSliderImages($merchantId);
        $this->set('sliderImages', $sliderImages);
    }

    /* ------------------------------------------------
      Function name:activateSliderImage()
      Description:Active/deactive slider images
      created:2/12/2016
      ----------------------------------------------------- */

    public function activateSliderImage($EncryptedSliderID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = false;
        $this->loadModel('MerchantGallery');
        $data['MerchantGallery']['merchant_id'] = $this->Session->read('merchantId');
        $data['MerchantGallery']['id'] = $this->Encryption->decode($EncryptedSliderID);
        $data['MerchantGallery']['is_active'] = $status;
        if ($this->MerchantGallery->save($data)) {
            if ($status) {
                $SuccessMsg = "Image Activated";
            } else {
                $SuccessMsg = "Image Deactivated";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hq', 'action' => 'merchantManageSliderPhotos'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hq', 'action' => 'merchantManageSliderPhotos'));
        }
    }

    /* ------------------------------------------------
      Function name:editSliderImage()
      Description:Edit slider details
      created:2/12/2016
      ----------------------------------------------------- */

    public function editSliderImage($EncryptMerchantGalleryID) {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $MerchantGalleryId = $this->Encryption->decode($EncryptMerchantGalleryID);
        $this->loadModel('MerchantGallery');
        $MerchantGalleryDetail = $this->MerchantGallery->findById($MerchantGalleryId);
        if ($this->request->is(array('post', 'put')) && !empty($MerchantGalleryDetail)) {
            if ($this->data['MerchantGallery']['image']['error'] == 0) {
                $response = $this->Common->uploadMenuItemImages($this->data['MerchantGallery']['image'], '/merchantSliderImages/', $merchantId, 1300, 600);
                if ($response['imagename']) {
                    $data['image'] = $response['imagename'];
                }
            } elseif ($this->data['MerchantGallery']['image']['error'] == 4) {
                $response['status'] = true;
            }
            if (!$response['status']) {
                $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
            } else {
                $data['id'] = $MerchantGalleryId;
                $data['merchant_id'] = $merchantId;
                $data['description'] = $this->request->data["MerchantGallery"]["description"];
                if ($this->MerchantGallery->save($data)) {
                    $this->Session->setFlash(__("Update Successfully."), 'alert_success');
                    $this->redirect(array('controller' => 'hq', 'action' => 'merchantManageSliderPhotos'));
                } else {
                    $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                    $this->redirect(array('controller' => 'hq', 'action' => 'merchantManageSliderPhotos'));
                }
            }
        }
        $this->request->data = $MerchantGalleryDetail;
    }

    /* ------------------------------------------------
      Function name:manageStoreSliderImages()
      Description:Manage Images for Somepage slider
      created:27/7/2015
      ----------------------------------------------------- */

    public function merchantDeleteSliderPhoto($EncryptedImageID = null) {
        $this->layout = "hq_dashboard";
        $imageID = $this->Encryption->decode($EncryptedImageID);
        if ($imageID) {
            $merchantId = $this->Session->read('merchantId');
            $this->set('merchantId', $merchantId);
            $data['id'] = $imageID;
            $data['is_deleted'] = 1;
            $this->loadModel('MerchantGallery');
            if ($this->MerchantGallery->saveSliderImage($data)) {
                $this->Session->setFlash(__("Slider photo has been deleted"), 'alert_success');
                $this->redirect(array('controller' => 'hq', 'action' => 'merchantManageSliderPhotos'));
            } else {
                $this->Session->setFlash(__("Some Problem occured"), 'alert_failed');
                $this->redirect(array('controller' => 'hq', 'action' => 'merchantManageSliderPhotos'));
            }
        }
    }

    public function deleteSliderPhotoName($EncryptedImageID) {
        $this->layout = "hq_dashboard";
        $imageID = $this->Encryption->decode($EncryptedImageID);
        if ($imageID) {
            $merchantId = $this->Session->read('merchantId');
            $data['id'] = $imageID;
            $data['image'] = '';
            $data['is_active'] = 0;
            $this->loadModel('MerchantGallery');
            if ($this->MerchantGallery->save($data)) {
                $this->Session->setFlash(__("Slider image has been deleted"), 'alert_success');
                $this->redirect($this->referer());
            } else {
                $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                $this->redirect($this->referer());
            }
        }
    }

    /* ------------------------------------------------
      Function name:transactionList()
      Description:Display the list of transaction
      created:2/09/2015
      ----------------------------------------------------- */

    public function transactionList($clearAction = null) {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'transactionList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $this->set('merchantId', $merchantId);
        $this->loadModel('OrderPayment');
        $criteria = "OrderPayment.is_deleted=0 AND Store.is_allow_transaction=1 AND Store.is_deleted=0 AND OrderPayment.merchant_id=" . $merchantId;
        $storeId = "";
        $value = '';
        if (isset($this->request->data['Merchant']['store_id']) && !empty($this->request->data['Merchant']['store_id'])) {
            $storeId = $this->request->data['Merchant']['store_id'];
            $criteria .= " AND OrderPayment.store_id=$storeId";
        }
        $this->set('storeId', $storeId);
        if ($this->Session->read('TransactionSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('TransactionSearchData'), true);
        } else {
            $this->Session->delete('TransactionSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('TransactionSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['Payment']['is_active'])) {
                $active = trim($this->request->data['Payment']['is_active']);
                $criteria .= " AND (OrderPayment.payment_status ='" . $active . "')";
            }
            if ($this->request->data['User']['from'] != '' && $this->request->data['User']['to'] != '') {
                $stratdate = $this->Dateform->formatDate($this->request->data['User']['from']);
                $enddate = $this->Dateform->formatDate($this->request->data['User']['to']);
                $criteria.= " AND (OrderPayment.created BETWEEN '" . $stratdate . "' AND '" . $enddate . "')";
            }
            if (!empty($this->request->data['Segment']['id'])) {
                $type = trim($this->request->data['Segment']['id']);
                $criteria .= " AND (Order.seqment_id =$type)";
            }
            if (!empty($this->request->data['User']['search'])) {
                $value = trim($this->request->data['User']['search']);
                $criteria .= " AND (OrderPayment.transection_id LIKE '%" . $value . "%' OR Order.order_number LIKE '%" . $value . "%')";
            }
        }
        $this->Store->unbindModel(array('belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent'), 'hasOne' => array('SocialMedia')), true);
        $this->OrderPayment->bindModel(array('belongsTo' => array(
                'Order' => array('className' => 'Order',
                    'foreignKey' => 'order_id'
                ),
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('store_name', 'is_allow_transaction'),
                    'conditions' => array('Store.is_allow_transaction' => 1),
                    'type' => 'inner'
                ),
            )
                ), false);
        $this->loadModel('Order');
        $this->Order->bindModel(array('belongsTo' => array(
                'Segment' => array(
                    'className' => 'Segment',
                    'foreignKey' => 'seqment_id',
                    'fields' => 'name'
                ),
            )
                ), false);

        $this->paginate = array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('OrderPayment.created' => 'DESC'));
        $transactionDetail = $this->paginate('OrderPayment');
        $this->set('list', $transactionDetail);
        $this->loadModel('Segment');
        $typeList = $this->Segment->OrderTypeList();
        $this->set('typeList', $typeList);
        $this->set('keyword', $value);
    }

    /* ------------------------------------------------
      Function name:addStorePayment()
      Description:Add store payments
      created:2/09/2015
      ----------------------------------------------------- */

    public function addStorePayment() {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addStorePayment', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        if (!empty($this->request->data)) {
            $this->request->data = $this->Common->trimValue($this->request->data);

            $invoice_number = $this->Common->RandomString($this->request->data['StorePayment']['store_id'], $this->front_store_offset, 'W');
            $this->request->data['StorePayment']['invoice_number'] = $invoice_number;
            if ($this->StorePayment->savePayment($this->request->data)) {
                $this->loadModel('DefaultTemplate');
                $emailSuccess = $this->DefaultTemplate->adminTemplates('invoice');
                if ($emailSuccess) {
                    $this->Store->bindModel(array('belongsTo' => array('User' => array('fields' => array('User.fname', 'User.lname')))));
                    $store = $this->Store->getStoreDetail($this->request->data['StorePayment']['store_id']);
                    if (($store['Store']['notification_type'] == 1 || $store['Store']['notification_type'] == 3) && (!empty($store['Store']['notification_email']))) {
                        $storeEmail = $store['Store']['notification_email'];
                    } else {
                        $storeEmail = $store['Store']['email_id'];
                    }
                    $merchantEmail = $this->Merchant->fetchMerchantDetail($merchantId);
                    $plan = $this->Plan->find('first', array('conditions' => array('Plan.id' => $this->request->data['StorePayment']['plan_id'])));
                    $project = 'Store Subscription Charges <br/> <b>P.O. </b>';
                    $description = $plan['Plan']['description'];
                    $address = $store['User']['fname'] . ' ' . $store['User']['lname'] . '<br/>' . $store['Store']['store_name']
                            . '<br/>' . $store['Store']['address'] . '<br/>' . $store['Store']['city'] . ', '
                            . $store['Store']['state'] . ' ' . $store['Store']['zipcode'];
                    $logo = '<img src="' . HTTP_ROOT . '/img/logo.jpg" width="200"/>';
                    $emailData = $emailSuccess['DefaultTemplate']['template_message'];
                    $emailData = str_replace('{LOGO}', $logo, $emailData);
                    if ($this->request->data['StorePayment']['payment_status'] == 'Paid') {
                        $emailData = str_replace('{STATUS}', 'PAID', $emailData);
                    } else {
                        $emailData = str_replace('{STATUS}', '', $emailData);
                    }
                    $emailData = str_replace('{QUANTITY}', 1, $emailData);
                    $emailData = str_replace('{AMOUNT}', $this->request->data['StorePayment']['amount'], $emailData);
                    $emailData = str_replace('{PROJECT}', $project, $emailData);
                    $emailData = str_replace('{DATE}', $this->request->data['StorePayment']['payment_date'], $emailData);
                    $emailData = str_replace('{INVOICE_NUMBER}', $invoice_number, $emailData);
                    $emailData = str_replace('{ADDRESS}', $address, $emailData);
                    $emailData = str_replace('{DESCRIPTION}', $description, $emailData);
                    $subject = ucwords(str_replace('_', ' ', $emailSuccess['DefaultTemplate']['template_subject']));
                    $this->Email->to = $storeEmail;
                    $this->Email->subject = $subject;
                    $this->Email->from = $merchantEmail['Merchant']['email'];
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
                $this->Session->setFlash(__("Store payment added successfully."), 'alert_success');
                $this->redirect(array('controller' => 'hq', 'action' => 'storePaymentList'));
            } else {
                $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                $this->redirect(array('controller' => 'hq', 'action' => 'storePaymentList'));
            }
        }
        $plan = $this->Plan->find('list', array('fields' => array('id', 'name')));
        $this->set('plan', $plan);
        $this->set('merchantId', $merchantId);
    }

    public function updateStorePayment($storePaymentId = null) {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addStorePayment', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        $storeDecodePaymentId = $this->Encryption->decode($storePaymentId);
        $merchantId = $this->Session->read('merchantId');
        if (!empty($this->request->data)) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            if ($this->StorePayment->savePayment($this->request->data)) {
                $this->loadModel('DefaultTemplate');
                $emailSuccess = $this->DefaultTemplate->adminTemplates('invoice');
                if ($emailSuccess) {
                    $this->Store->bindModel(array('belongsTo' => array('User' => array('fields' => array('User.fname', 'User.lname')))));
                    $store = $this->Store->getStoreDetail($this->request->data['StorePayment']['store_id']);
                    if (($store['Store']['notification_type'] == 1 || $store['Store']['notification_type'] == 3) && (!empty($store['Store']['notification_email']))) {
                        $storeEmail = $store['Store']['notification_email'];
                    } else {
                        $storeEmail = $store['Store']['email_id'];
                    }
                    $merchantEmail = $this->Merchant->fetchMerchantDetail($merchantId);
                    $plan = $this->Plan->find('first', array('conditions' => array('Plan.id' => $this->request->data['StorePayment']['plan_id'])));
                    $project = 'Store Subscription Charges <br/> <b>P.O. </b>';
                    $description = $plan['Plan']['description'];
                    $address = $store['User']['fname'] . ' ' . $store['User']['lname'] . '<br/>' . $store['Store']['store_name']
                            . '<br/>' . $store['Store']['address'] . '<br/>' . $store['Store']['city'] . ', '
                            . $store['Store']['state'] . ' ' . $store['Store']['zipcode'];
                    $logo = '<img src="' . HTTP_ROOT . '/img/logo.jpg" width="200"/>';
                    $emailData = $emailSuccess['DefaultTemplate']['template_message'];
                    $emailData = str_replace('{LOGO}', $logo, $emailData);
                    if ($this->request->data['StorePayment']['payment_status'] == 'Paid') {
                        $emailData = str_replace('{STATUS}', 'PAID', $emailData);
                    } else {
                        $emailData = str_replace('{STATUS}', '', $emailData);
                    }
                    $emailData = str_replace('{QUANTITY}', 1, $emailData);
                    $emailData = str_replace('{AMOUNT}', $this->request->data['StorePayment']['amount'], $emailData);
                    $emailData = str_replace('{PROJECT}', $project, $emailData);
                    $emailData = str_replace('{DATE}', $this->request->data['StorePayment']['payment_date'], $emailData);
                    $emailData = str_replace('{INVOICE_NUMBER}', $this->request->data['StorePayment']['invoice_number'], $emailData);
                    $emailData = str_replace('{ADDRESS}', $address, $emailData);
                    $emailData = str_replace('{DESCRIPTION}', $description, $emailData);
                    $subject = ucwords(str_replace('_', ' ', $emailSuccess['DefaultTemplate']['template_subject']));
                    $this->Email->to = $storeEmail;
                    $this->Email->subject = $subject;
                    $this->Email->from = $merchantEmail['Merchant']['email'];
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
                $this->Session->setFlash(__("Store payment updated successfully."), 'alert_success');
                $this->redirect(array('controller' => 'hq', 'action' => 'storePaymentList'));
            } else {
                $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                $this->redirect(array('controller' => 'hq', 'action' => 'storePaymentList'));
            }
        }
        $storePayment = $this->StorePayment->fetchStorePayment($storeDecodePaymentId);
        $this->request->data = $storePayment;
        $plan = $this->Plan->find('list', array('fields' => array('id', 'name')));
        $this->set(compact('plan', 'merchantId'));
    }

    /* ------------------------------------------------
      Function name:storeList()
      Description:Display store list
      created:26/09/2017
      ----------------------------------------------------- */

    public function storeList($clearAction = null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addStorePayment', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $this->set('merchantId', $merchantId);
        $this->loadModel('Store');
        $criteria = "Store.is_active = 1 AND Store.is_deleted=0 AND Store.merchant_id=" . $merchantId;
        if ($this->Session->read('StoreSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('StoreSearchData'), true);
        } else {
            $this->Session->delete('StoreSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('StoreSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['Merchant']['store_id'])) {
                $active = trim($this->request->data['Merchant']['store_id']);
                $criteria .= " AND (Store.id = '" . $active . "' ) ";
            }
            if (!empty($this->request->data['Merchant']['search'])) {
                $value = trim($this->request->data['Merchant']['search']);
                if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $value)) {
                    $value = explode("'", $value);
                    $value = $value[0];
                    // one or more of the 'special characters' found in $string
                }
                $criteria .= " AND (Store.store_name LIKE '%" . $value . "%' OR Store.address LIKE '%" . $value . "%' OR Store.phone LIKE '%" . $value . "%' OR Store.email_id LIKE '%" . $value . "%' OR Store.store_url LIKE '%" . $value . "%')";
            }
        }
        $this->paginate = array('conditions' => array($criteria), 'order' => array('Store.created' => 'DESC'));
        $storeDetails = $this->paginate('Store');
        $this->set('list', $storeDetails);
    }

    /* ------------------------------------------------
      Function name:storePaymentList()
      Description:Display store payment list
      created:2/09/2015
      ----------------------------------------------------- */

    public function storePaymentList($storeId = null, $clearAction = null) {

        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addStorePayment', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }


        $storeDecodeId = $this->Encryption->decode($storeId);
        if ($storeDecodeId == null && empty($storeDecodeId)) {
            $this->redirect(array('controller' => 'hq', 'action' => 'storeList'));
        }

        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $this->set('merchantId', $merchantId);


        $this->loadModel('Store');
        $storeDetail = $this->Store->find('first', array('fields' => array('Store.id', 'Store.merchant_id', 'Store.store_name'), 'conditions' => array('Store.id' => $storeDecodeId)));

        $this->loadModel('StorePayment');
        $criteria = "StorePayment.is_deleted=0 AND Store.merchant_id = " . $merchantId . " AND StorePayment.store_id = " . $storeDecodeId;
        if ($this->Session->read('StorePaymentSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('StorePaymentSearchData'), true);
        } else {

            $this->Session->delete('StorePaymentSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('StorePaymentSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['StorePayment']['payment_status'])) {
                $payment_status = trim($this->request->data['StorePayment']['payment_status']);
                $criteria .= " AND (StorePayment.payment_status ='" . $payment_status . "')";
            }

            if (!empty($this->request->data['StorePayment']['from']) && !empty($this->request->data['StorePayment']['to'])) {
                $stratdate = $this->Dateform->formatDate($this->request->data['StorePayment']['from']);
                $enddate = $this->Dateform->formatDate($this->request->data['StorePayment']['to']);
                $criteria.= " AND (Date(StorePayment.payment_date) >= '" . $stratdate . "' AND Date(StorePayment.payment_date) <='" . $enddate . "')";
            }
        }
        $this->StorePayment->bindModel(array(
            'belongsTo' => array(
                'Plan' => array(
                    'fields' => array('name'),
                    'className' => 'Plan',
                    'foreignKey' => 'plan_id'),
                'Store' => array(
                    'fields' => array('store_name', 'merchant_id'),
                    'className' => 'Store',
                    'foreignKey' => 'store_id')
            )
                ), false);
        $this->paginate = array('conditions' => array($criteria), 'order' => array('StorePayment.created' => 'DESC'));
        $transactionDetail = $this->paginate('StorePayment');
        $this->set('list', $transactionDetail);
        $this->set('storeDetail', $storeDetail);
        $this->set('storeId', $storeId);
    }

    /* ------------------------------------------------
      Function name:paymentList()
      Description:Display subscription payment list
      created:3/09/2015
      ----------------------------------------------------- */

    public function paymentList() {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addStorePayment', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $this->set('merchantId', $merchantId);
        $this->loadModel('MerchantPayment');
        $criteria = "MerchantPayment.is_deleted = 0 AND MerchantPayment.merchant_id = $merchantId";
        $this->MerchantPayment->bindModel(array('belongsTo' => array('Plan' => array('fields' => array('name'), 'className' => 'Plan', 'foreignKey' => 'plan_id'))), false);

        $this->paginate = array('conditions' => array($criteria), 'order' => array('MerchantPayment.created' => 'DESC'));
        $transactionDetail = $this->paginate('MerchantPayment');
        $this->set('list', $transactionDetail);
    }

    public function checkMerchantEmail($roleId = null) {
        $this->autoRender = false;
        if ($_GET) {
            $emailEntered = $_GET['data']['User']['email'];
            $merchantId = "";
            $merchantId = $this->Session->read('merchantId');
            $emailStatus = $this->User->merchantemailExists($emailEntered, $roleId, $merchantId);
            echo json_encode($emailStatus);
        }
    }

    /* ------------------------------------------------
      Function name:forgetPassword()
      Description:For forget password
      created:04/9/2015
      ----------------------------------------------------- */

    public function forgetPassword() {
        $this->layout = "hq_login";
        $this->autorender = false;
        if (!empty($this->data)) {
            $roleId = "";
            $email = $this->request->data['User']['email'];
            $roleId = $this->request->data['User']['role_id'];
            $merchantId = $this->Session->read('merchantId');
            if (!$merchantId) {
                $merchantId = "";
            }
            $userEmail = $this->User->checkMerchantForgetEmail($roleId, $email);
            $this->loadModel('Merchant');
            $merchantEmail = $this->Merchant->fetchMerchantDetail($userEmail['User']['merchant_id']);
            if (!empty($userEmail)) {
                $this->loadModel('DefaultTemplate');
                $template_type = 'forget_password';
                $emailTemplate = $this->DefaultTemplate->adminTemplates($template_type);
                if ($emailTemplate) {
                    if ($userEmail['User']['lname']) {
                        $fullName = $userEmail['User']['fname'] . " " . $userEmail['User']['lname'];
                    } else {
                        $fullName = $userEmail['User']['fname'];
                    }
                    $token = Security::hash($email, 'md5', true) . time() . rand();
                    $emailData = $emailTemplate['DefaultTemplate']['template_message'];
                    $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                    $url = HTTP_ROOT . 'users/resetPassword/' . $token . '/2';
                    $activationLink = '<a style="color:#fff;background-color: #10c4f7; text-decoration:none; padding: 5px 10px 7px;font-weight: bold; display:inline-block;" href="' . $url . '">Click here to reset your password</a>';
                    $emailData = str_replace('{ACTIVE_LINK}', $activationLink, $emailData);
                    $subject = ucwords(str_replace('_', ' ', $emailTemplate['DefaultTemplate'] ['template_subject']));
                    $this->Email->to = $email;
                    $this->Email->subject = $subject;
                    $this->Email->from = $merchantEmail['Merchant']['email'];
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
                        if ($this->Email->send()) {
                            $this->request->data['User']['id'] = $userEmail['User']['id'];
                            $this->request->data['User']['forgot_token'] = $token;
                            $this->User->saveUserInfo($this->data['User']);
                            $this->Session->setFlash(__("Please check your email for reset new password"), 'alert_success');
                            $this->redirect(array('controller' => 'hq', 'action' => 'forgetPassword'));
                        }
                    } catch (Exception $e) {
                        $this->Session->setFlash("Please enter correct email.", 'alert_failed');
                        $this->redirect(array('controller' => 'hq', 'action' => 'forgetPassword'));
                    }
                }
            } else {
                $this->Session->setFlash("Please enter correct email.", 'alert_failed');
                $this->redirect(array('controller' => 'hq', 'action' => 'forgetPassword'));
            }
        }
    }

    /* ------------------------------------------------
      Function name: reviewRating()
      Description: Display the list of Reviews and Ratings in admin panel
      created:29/09/2015
      ----------------------------------------------------- */

    public function reviewRating($clearAction = null) {
        $loginuserid = $this->Session->read('Auth.hq.id');
        $this->layout = "hq_dashboard";
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'reviewRating', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $merchantId = $this->Session->read('merchantId');
        $this->set('merchantId', $merchantId);
        $value = "";
        $criteria = "StoreReview.is_deleted=0 AND StoreReview.merchant_id=" . $merchantId;
        $storeId = "";
        if ($this->Session->read('selectedStoreId')) {
            $storeId = $this->Session->read('selectedStoreId');
            $criteria .= " AND StoreReview.store_id =$storeId";
        } elseif (isset($this->request->data['Merchant']['store_id']) && !empty($this->request->data['Merchant']['store_id'])) {
            $storeId = $this->request->data['Merchant']['store_id'];
            $criteria .= " AND StoreReview.store_id=$storeId";
        }
        $this->set('storeId', $storeId);
        if ($this->Session->read('hqRatingSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('hqRatingSearchData'), true);
        } else {
            $this->Session->delete('hqRatingSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('hqRatingSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['User']['keyword'])) {
                $value = trim($this->request->data['User']['keyword']);
                $criteria .= " AND (StoreReview.review_comment LIKE '%" . $value . "%' OR Order.order_number LIKE '%" . $value . "%')";
            }
            if ($this->request->data['StoreReview']['review_rating'] != '') {
                $rating = trim($this->request->data['StoreReview']['review_rating']);
                $criteria .= " AND (StoreReview.review_rating =$rating)";
            }
        }
        $this->loadModel('Order');
        $this->loadModel('OrderItem');
        $this->loadModel('Item');
//        $this->OrderItem->bindModel(array(
//            'belongsTo' => array(
//                'Item' => array(
//                    'className' => 'Item', 
//                    'foreignKey' => 'item_id', 
//                    'fields' => 'name'))));
        $this->loadModel('StoreReview');
        $this->StoreReview->bindModel(array(
            'belongsTo' => array(
                'Order' => array(
                    'className' => 'Order',
                    'foreignKey' => 'order_id'),
                'OrderItem' => array(
                    'className' => 'OrderItem',
                    'foreignKey' => 'order_item_id'),
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id',
                    'fields' => 'name')
        )));
        $this->paginate = array('conditions' => array($criteria), 'order' => array('StoreReview.created' => 'DESC'), 'recursive' => 2);
        $reviewdetail = $this->paginate('StoreReview');
        $this->set('keyword', $value);
        $this->set('list', $reviewdetail);
    }

    /* ------------------------------------------------
      Function name: ApprovedReview()
      Description: Review approve and disapproved
      created:29/09/2015
      ----------------------------------------------------- */

    public function approvedReview($EncryptReviewID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $id = $this->Encryption->decode($EncryptReviewID);
        $this->StoreReview->id = $id;
        $this->StoreReview->saveField("is_approved", $status);
        $this->Session->setFlash(__("Review status updated successfully."), 'alert_success');
        $this->redirect($this->referer());
    }

    /* ------------------------------------------------
      Function name:pageList()
      Description:Display the list of created pages
      created:29/09/2015
      ----------------------------------------------------- */

    public function pageList($clearAction = null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'pageList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        $storeID = "";
        $value = '';
        $active = '';
        $merchantId = $this->Session->read('merchantId');
        $this->set('merchantId', $merchantId);
        $this->loadModel('StoreContent');
        $this->loadModel('TermsAndPolicy');
        $this->loadModel('Store');
        $criteria = "StoreContent.is_deleted=0 AND StoreContent.merchant_id=$merchantId";
        $criteria1 = "TermsAndPolicy.is_deleted=0 AND TermsAndPolicy.merchant_id=$merchantId";

        if ($this->Session->read('hqStoreContentSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('hqStoreContentSearchData'), true);
        } else {
            $this->Session->delete('hqStoreContentSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }

        if (!empty($this->request->data)) {
            if (isset($this->request->data['Merchant']['store_id']) && !empty($this->request->data['Merchant']['store_id'])) {
                $this->Session->write('pageListStoreId', $this->request->data['Merchant']['store_id']);
                $storeID = $this->request->data['Merchant']['store_id'];
                $criteria .= " AND StoreContent.store_id=$storeID";
                $criteria1 .= " AND TermsAndPolicy.store_id=$storeID";
            } else {
                $this->Session->delete('pageListStoreId');
            }
            $this->Session->write('hqStoreContentSearchData', json_encode($this->request->data));
            if (isset($this->request->data['PageList']['isActive']) && $this->request->data['PageList']['isActive'] != '') {
                $active = trim($this->request->data['PageList']['isActive']);
                $criteria .= " AND (StoreContent.is_active ='" . $active . "')";
                $criteria1 .= " AND (TermsAndPolicy.is_active ='" . $active . "')";
            }

            if (!empty($this->request->data['PageList']['search'])) {
                $value = trim($this->request->data['PageList']['search']);
                $criteria .= " AND (StoreContent.name LIKE '%" . $value . "%')";
                $criteria1 .= " AND (TermsAndPolicy.terms_and_conditions LIKE '%" . $value . "%')";
            }
//            echo $criteria;
//         die;
        }
        if (empty($storeID)) {
            $criteria1 .= " AND TermsAndPolicy.store_id!=NULL";
        }

        $this->set('storeID', $storeID);
        $this->set('active', $active);
        //$this->paginate = array('conditions' => array($criteria), 'order' => array('StoreContent.created' => 'DESC'));
        $pageDetail = $this->StoreContent->find('all', array('conditions' => array($criteria), 'order' => array('StoreContent.created' => 'DESC')));
        //$pageDetail = $this->paginate('StoreContent');
        $pagePostion = $this->Store->find('first', array('conditions' => array('Store.id' => $storeID)));
        $this->request->data = $pagePostion;
        $this->set('list', $pageDetail);
        $termsAndPolicy = $this->TermsAndPolicy->find('all', array('conditions' => array($criteria1), 'order' => array('TermsAndPolicy.created' => 'DESC')));
        $this->set('termsAndPolicy', $termsAndPolicy);
        $this->set('keyword', $value);
    }

    public function merchantPageList() {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'merchantPageList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $value = '';
        $active = '';
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $this->loadModel('MerchantContent');
        $criteria = "MerchantContent.merchant_id = $merchantId AND MerchantContent.is_deleted=0";
        if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
            if ($this->params->pass[0] == 'clear') {
                $this->redirect($this->referer());
            }
        }
        if (!empty($this->request->data)) {
            if (isset($this->request->data['MerchantContent']['is_active']) && $this->request->data['MerchantContent']['is_active'] != '') {
                $active = trim($this->request->data['MerchantContent']['is_active']);
                $criteria .= " AND (MerchantContent.is_active ='" . $active . "')";
            }

            if (!empty($this->request->data['MerchantContent']['search'])) {
                $value = trim($this->request->data['MerchantContent']['search']);
                $criteria .= " AND (MerchantContent.name LIKE '%" . $value . "%')";
            }
        }
        $this->paginate = array('conditions' => array($criteria), 'order' => array('MerchantContent.position' => 'ASC'));
        $pageDetail = $this->paginate('MerchantContent');
        $homepageExist = $this->MerchantContent->find('count', array('conditions' => array('OR' => array('LOWER(MerchantContent.name)' => strtolower('Home'), 'LOWER(MerchantContent.content_key)' => strtolower('Home')), 'MerchantContent.merchant_id' => $merchantId)));
        if ($homepageExist < 1) {//execute only once for home page dynamic content
            $pagedata['name'] = trim('HOME');
            $pagedata['content_key'] = trim('HOME');
            $pagedata['page_position'] = 1;
            $pagedata['is_active'] = 1;
            $pagedata['merchant_id'] = $merchantId;
            $this->MerchantContent->create();
            $this->MerchantContent->savePage($pagedata);
            $this->redirect(array('controller' => 'hq', 'action' => 'merchantPageList'));
        }
        $locationExist = $this->MerchantContent->find('count', array('conditions' => array('OR' => array('LOWER(MerchantContent.name)' => strtolower('LOCATIONS'), 'LOWER(MerchantContent.content_key)' => strtolower('LOCATIONS')), 'MerchantContent.merchant_id' => $merchantId)));
        if ($locationExist < 1) {//execute only once for home page dynamic content
            $pagedata['name'] = trim('LOCATIONS');
            $pagedata['content_key'] = trim('LOCATIONS');
            $pagedata['page_position'] = 1;
            $pagedata['is_active'] = 1;
            $pagedata['merchant_id'] = $merchantId;
            $this->MerchantContent->create();
            $this->MerchantContent->savePage($pagedata);
            $this->redirect(array('controller' => 'hq', 'action' => 'merchantPageList'));
        }
        $galleryExist = $this->MerchantContent->find('count', array('conditions' => array('OR' => array('LOWER(MerchantContent.name)' => strtolower('GALLERY'), 'LOWER(MerchantContent.content_key)' => strtolower('GALLERY')), 'MerchantContent.merchant_id' => $merchantId)));
        if ($galleryExist < 1) {//execute only once for home page dynamic content
            $pagedata['name'] = trim('GALLERY');
            $pagedata['content_key'] = trim('GALLERY');
            $pagedata['page_position'] = 1;
            $pagedata['is_active'] = 1;
            $pagedata['merchant_id'] = $merchantId;
            $this->MerchantContent->create();
            $this->MerchantContent->savePage($pagedata);
            $this->redirect(array('controller' => 'hq', 'action' => 'merchantPageList'));
        }
        $newletterExist = $this->MerchantContent->find('count', array('conditions' => array('OR' => array('LOWER(MerchantContent.name)' => strtolower('NEWSLETTER'), 'LOWER(MerchantContent.content_key)' => strtolower('NEWSLETTER')), 'MerchantContent.merchant_id' => $merchantId)));
        if ($newletterExist < 1) {//execute only once for home page dynamic content
            $pagedata['name'] = trim('NEWSLETTER');
            $pagedata['content_key'] = trim('NEWSLETTER');
            $pagedata['page_position'] = 1;
            $pagedata['is_active'] = 1;
            $pagedata['merchant_id'] = $merchantId;
            $this->MerchantContent->create();
            $this->MerchantContent->savePage($pagedata);
            $this->redirect(array('controller' => 'hq', 'action' => 'merchantPageList'));
        }
        $promotionExist = $this->MerchantContent->find('count', array('conditions' => array('OR' => array('LOWER(MerchantContent.name)' => strtolower('PROMOTIONS'), 'LOWER(MerchantContent.content_key)' => strtolower('PROMOTIONS')), 'MerchantContent.merchant_id' => $merchantId)));
        if ($promotionExist < 1) {//execute only once for home page dynamic content
            $pagedata['name'] = trim('PROMOTIONS');
            $pagedata['content_key'] = trim('PROMOTIONS');
            $pagedata['page_position'] = 1;
            $pagedata['is_active'] = 1;
            $pagedata['merchant_id'] = $merchantId;
            $this->MerchantContent->create();
            $this->MerchantContent->savePage($pagedata);
            $this->redirect(array('controller' => 'hq', 'action' => 'merchantPageList'));
        }
	$this->loadModel('TermsAndPolicy');
        $termAndPolicyCount = $this->TermsAndPolicy->find('count', array('conditions' => array('merchant_id' => $merchantId, 'is_deleted' => 0,'store_id'=>null)));
        if ($termAndPolicyCount == 0) {
            $termAndPolicy['merchant_id'] = $merchantId;
            $this->TermsAndPolicy->create();
            $this->TermsAndPolicy->save($termAndPolicy);
        }
        $this->set('list', $pageDetail);
        $this->set('keyword', $value);
        $this->set('active', $active);
        $this->loadModel("TermsAndPolicy");
        $termAndPolicy = $this->TermsAndPolicy->find('first', array('conditions' => array('TermsAndPolicy.merchant_id' => $merchantId, 'is_deleted' => 0, 'TermsAndPolicy.store_id' => NULL)));

        $this->set('termsAndPolicy', $termAndPolicy);
    }

    /* ------------------------------------------------
      Function name:addPage()
      Description:Add the newsletter in table
      created:29/09/2015
      ----------------------------------------------------- */

    public function addPage() {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'pageList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        $this->loadModel('StoreContent');
        $merchantId = $this->Session->read('merchantId');
        $this->set('merchantId', $merchantId);
        if ($this->request->data) {
            $storeID = $this->request->data['StoreContent']['store_id'];
            $this->data = $this->Common->trimValue($this->data);
            $pagedata['name'] = trim($this->data['StoreContent']['name']);
            $pagedata['content_key'] = trim($this->data['StoreContent']['content_key']);
            $isUniqueName = $this->StoreContent->checkPageUniqueName($pagedata['name'], $storeID);
            $isUniqueCode = $this->StoreContent->checkPageUniqueCode($pagedata['content_key'], $storeID);
            if ($isUniqueName) {
                if ($isUniqueCode) {
                    $pagedata = array();
                    $pagedata['name'] = trim($this->data['StoreContent']['name']);
                    $pagedata['content_key'] = trim($this->data['StoreContent']['content_key']);
                    $pagedata['content'] = trim($this->data['StoreContent']['content']);
                    $pagedata['page_position'] = trim($this->data['StoreContent']['page_position']);
                    $pagedata['is_active'] = trim($this->data['StoreContent']['is_active']);
                    $pagedata['store_id'] = $storeID;
                    $pagedata['merchant_id'] = $merchantId;

                    $this->StoreContent->create();
                    $this->StoreContent->savePage($pagedata);
                    $this->Session->setFlash(__("Page Successfully Created"), 'alert_success');
                    $this->redirect(array('controller' => 'hq', 'action' => 'pageList'));
                } else {
                    $this->Session->setFlash(__("Page code Already exists"), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Page name Already exists"), 'alert_failed');
            }
        }
    }

    public function merchantAddPage() {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'merchantPageList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        $this->loadModel('MerchantContent');
        $merchantId = $this->Session->read('merchantId');
        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $pagedata['name'] = trim($this->data['MerchantContent']['name']);
            //$pagedata['content_key'] = trim($this->data['MerchantContent']['content_key']);
            $isUniqueName = $this->MerchantContent->checkPageUniqueName($pagedata['name'], $merchantId);
            //$isUniqueCode = $this->MerchantContent->checkPageUniqueCode($pagedata['content_key'], $merchantId);
            if ($isUniqueName) {
                //if ($isUniqueCode) {
                $pagedata = array();
                $pagedata['name'] = trim($this->data['MerchantContent']['name']);
                //$pagedata['content_key'] = trim($this->data['MerchantContent']['content_key']);
                $pagedata['page_position'] = trim($this->data['MerchantContent']['page_position']);
                //$pagedata['content'] = trim($this->data['MerchantContent']['content']);
                $pagedata['is_active'] = trim($this->data['MerchantContent']['is_active1']);
                $pagedata['merchant_id'] = $merchantId;
                //$pagedata['page_position'] = 1;

                $this->MerchantContent->create();
                $this->MerchantContent->savePage($pagedata);
                $this->Session->setFlash(__("Page Successfully Created"), 'alert_success');
//                } else {
//                    $this->Session->setFlash(__("Page code Already exists"), 'alert_failed');
//                }
            } else {
                $this->Session->setFlash(__("Page name Already exists"), 'alert_failed');
            }
            $this->redirect(array('controller' => 'hq', 'action' => 'merchantPageList'));
        }
    }

    /* ------------------------------------------------
      Function name:pageLocation()
      Description:Fixed the page position
      created:29/09/2015
      ----------------------------------------------------- */

    public function pageLocation() {
        $this->layout = "hq_dashboard";
        $this->autoRender = false;
        $this->loadModel('Store');
        if ($this->Session->read('pageListStoreId')) {
            $storeID = $this->Session->read('pageListStoreId');
        } else {
            $this->Session->setFlash(__("Please select store."), 'alert_failed');
            $this->redirect($this->referer());
        }
        if ($this->request->data) {
            $this->Store->id = $storeID;
            $this->Store->saveField("navigation", $this->request->data['Store']['navigation']);
            $this->Session->setFlash(__("Navigation Position Updated Successfully."), 'alert_success');
            $this->redirect($this->referer());
        }
    }

    /* ------------------------------------------------
      Function name:activatePage()
      Description:Active/Deactive pages
      created:29/09/2015
      ----------------------------------------------------- */

    public function activatePage($EncryptPageID = null, $status = 0) {
        $this->layout = "hq_dashboard";
        $this->loadModel('StoreContent');
        $data['StoreContent']['id'] = $this->Encryption->decode($EncryptPageID);
        $data['StoreContent']['is_active'] = $status;
        if ($this->StoreContent->savePage($data)) {
            if ($status) {
                $SuccessMsg = "Page Activated";
            } else {
                $SuccessMsg = "Page Inactive and Page will not get Display in Menu List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hq', 'action' => 'pageList'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hq', 'action' => 'pageList'));
        }
    }

    /* ------------------------------------------------
      Function name:activateTermsAndPolicy()
      Description:Active/Deactive Terms And Policy pages
      created:03/01/2017
      ----------------------------------------------------- */

    public function activateTermsAndPolicy($EncryptTermsAndPolicyID = null, $status = 0, $EncryptMerchantID = null) {
        $this->layout = "hq_dashboard";
        $this->loadModel('TermsAndPolicy');
        $data['TermsAndPolicy']['id'] = $this->Encryption->decode($EncryptTermsAndPolicyID);
        $data['TermsAndPolicy']['is_active'] = $status;
        if ($this->TermsAndPolicy->save($data)) {
            if ($status) {
                $SuccessMsg = "Page Activated";
            } else {
                $SuccessMsg = "Page Inactive and Page will not get Display in Menu List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            if ($EncryptMerchantID) {
                $this->redirect(array('controller' => 'hq', 'action' => 'merchantPageList'));
            } else {
                $this->redirect(array('controller' => 'hq', 'action' => 'pageList'));
            }
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hq', 'action' => 'pageList'));
        }
    }

    public function saveTermsAndPolicies($EncryptTermsAndPolicyID = null, $store_id = null) {
        $this->layout = "hq_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $this->loadModel('TermsAndPolicy');
        $decryptTermsAndPolicyID = $this->Encryption->decode($EncryptTermsAndPolicyID);
        $decryptStoreID = $this->Encryption->decode($store_id);
        if ($this->request->is(array('post', 'put')) && !empty($decryptTermsAndPolicyID)) {
            $this->request->data['TermsAndPolicy']['store_id'] = $decryptStoreID;
            $this->request->data['TermsAndPolicy']['id'] = $decryptTermsAndPolicyID;
            if ($this->TermsAndPolicy->save($this->request->data)) {
                $this->Session->setFlash(__("Update Successfully."), 'alert_success');
                $this->redirect(array('controller' => 'hq', 'action' => 'pageList'));
            } else {
                $this->Session->setFlash(__("Something went wrong."), 'alert_failed');
                $this->redirect(array('controller' => 'hq', 'action' => 'pageList'));
            }
        }
        $this->request->data = $this->TermsAndPolicy->findByStoreId($decryptStoreID);
    }

    /* ------------------------------------------------
      Function name:activatePage()
      Description:Active/Deactive pages
      created:29/09/2015
      ----------------------------------------------------- */

    public function activateReview($EncryptReviewID = null, $status = 0) {
        $this->layout = "hq_dashboard";
        $this->loadModel('StoreReview');
        $data['StoreReview']['id'] = $this->Encryption->decode($EncryptReviewID);
        $data['StoreReview']['is_active'] = $status;
        if ($this->StoreReview->save($data)) {
            if ($status) {
                $SuccessMsg = "Review & Ratings Activated";
            } else {
                $SuccessMsg = "Review & Ratings Deactivate";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hq', 'action' => 'reviewRating'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hq', 'action' => 'reviewRating'));
        }
    }

    public function merchantActivatePage($EncryptPageID = null, $status = 0) {
        $this->layout = "hq_dashboard";
        $this->loadModel('MerchantContent');
        $data['MerchantContent']['merchant_id'] = $this->Session->read('merchantId');
        $data['MerchantContent']['id'] = $this->Encryption->decode($EncryptPageID);
        $data['MerchantContent']['is_active'] = $status;
        if ($this->MerchantContent->savePage($data)) {
            if ($status) {
                $SuccessMsg = "Page Activated";
            } else {
                $SuccessMsg = "Page Inactive and Page will not get Display in Menu List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hq', 'action' => 'merchantPageList'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hq', 'action' => 'merchantPageList'));
        }
    }

    /* ------------------------------------------------
      Function name:deletePage()
      Description:Delete page from list
      created:29/09/2015
      ----------------------------------------------------- */

    public function deletePage($EncryptPageID = null) {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $this->loadModel('StoreContent');
        $data['StoreContent']['id'] = $this->Encryption->decode($EncryptPageID);
        $data['StoreContent']['is_deleted'] = 1;
        if ($this->StoreContent->savePage($data)) {
            $this->Session->setFlash(__("Page deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hq', 'action' => 'pageList'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hq', 'action' => 'pageList'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteReviewRating()
      Description:Delete page from list
      created:29/09/2015
      ----------------------------------------------------- */

    public function deleteReviewRating($EncryptID = null) {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $this->loadModel('StoreReview');
        $this->loadModel('StoreReviewImage');
        $data['StoreReview']['id'] = $this->Encryption->decode($EncryptID);
        $data['StoreReview']['is_deleted'] = 1;
        if ($this->StoreReview->save($data)) {
            $this->StoreReviewImage->updateAll(array('is_deleted' => 1), array('store_review_id' => $data['StoreReview']['id']));
            $this->Session->setFlash(__("Review & Rating deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hq', 'action' => 'reviewRating'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hq', 'action' => 'reviewRating'));
        }
    }

    public function merchantDeletePage($EncryptPageID = null) {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $this->loadModel('MerchantContent');
        $data['MerchantContent']['merchant_id'] = $this->Session->read('merchantId');
        $data['MerchantContent']['id'] = $this->Encryption->decode($EncryptPageID);
        $data['MerchantContent']['is_deleted'] = 1;
        if ($this->MerchantContent->savePage($data)) {
            $this->Session->setFlash(__("Page deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hq', 'action' => 'merchantPageList'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hq', 'action' => 'merchantPageList'));
        }
    }

    /* ------------------------------------------------
      Function name:editPage()
      Description:Edit Page contents
      created:29/09/2015
      ----------------------------------------------------- */

    public function editPage($EncryptPageID = null) {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'pageList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $storeID = '';
        $this->layout = "hq_dashboard";
        $data['StoreContent']['id'] = $this->Encryption->decode($EncryptPageID);
        $this->loadModel('StoreContent');
        $pageDetail = $this->StoreContent->findById($data['StoreContent']['id']);

        if ($this->request->data) {
            $this->data = $this->Common->trimValue($this->data);
            $storeId = trim($this->data['StoreContent']['store_id']);
            $pageTitle = trim($this->data['StoreContent']['name']);
            $pageCode = trim($this->data['StoreContent']['content_key']);
            $isUniqueName = $this->StoreContent->checkPageUniqueName($pageTitle, $storeId, $this->data['StoreContent']['id']);
            $isUniqueCode = $this->StoreContent->checkPageUniqueCode($pageCode, $storeId, $this->data['StoreContent']['id']);
            if ($isUniqueName) {
                if ($isUniqueCode) {
                    $pagedata = array();
                    $pagedata['name'] = trim($this->data['StoreContent']['name']);
                    $pagedata['content_key'] = trim($this->data['StoreContent']['content_key']);
                    $pagedata['id'] = trim($this->data['StoreContent']['id']);
                    $pagedata['content'] = trim($this->data['StoreContent']['content']);
                    $pagedata['page_position'] = trim($this->data['StoreContent']['page_position']);
                    $pagedata['is_active'] = trim($this->data['StoreContent']['is_active']);
                    $this->loadModel('StoreContent');
                    $this->StoreContent->savePage($pagedata);
                    $this->Session->setFlash(__("Page Successfully Updated."), 'alert_success');
                    $this->redirect(array('controller' => 'hq', 'action' => 'pageList'));
                } else {
                    $this->Session->setFlash(__("Page Code Already exists"), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Page Name Already exists"), 'alert_failed');
            }
        }
        $this->request->data = $pageDetail;
    }

    public function merchantEditPage($EncryptPageID = null) {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'merchantPageList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        $this->loadModel('MerchantContent');
        $merchantId = $this->Session->read('merchantId');
        $data['MerchantContent']['id'] = $this->Encryption->decode($EncryptPageID);
        $pageDetail = $this->MerchantContent->getPageDetail($data['MerchantContent']['id'], $merchantId);
        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $pageTitle = trim($this->data['MerchantContent']['name']);
            //$pageCode = trim($this->data['MerchantContent']['content_key']);
            $isUniqueName = $this->MerchantContent->checkPageUniqueName($pageTitle, $merchantId, $this->data['MerchantContent']['id']);
            //$isUniqueCode = $this->MerchantContent->checkPageUniqueCode($pageCode, $merchantId, $this->data['MerchantContent']['id']);
            if ($isUniqueName) {
                //if ($isUniqueCode) {
                $pagedata = array();
                $pagedata['name'] = trim($this->data['MerchantContent']['name']);
                //$pagedata['content_key'] = trim($this->data['MerchantContent']['content_key']);
                $pagedata['id'] = trim($this->data['MerchantContent']['id']);
                $pagedata['page_position'] = trim($this->data['MerchantContent']['page_position']);
                //$pagedata['content'] = trim($this->data['MerchantContent']['content']);
                $pagedata['is_active'] = trim($this->data['MerchantContent']['is_active']);
                $pagedata['merchant_id'] = $merchantId;
                //$pagedata['page_position'] = 1;
                $this->MerchantContent->savePage($pagedata);
                $this->Session->setFlash(__("Page Successfully Updated."), 'alert_success');
                $this->redirect(array('controller' => 'hq', 'action' => 'merchantPageList'));
//                } else {
//                    $this->Session->setFlash(__("Page Code Already exists"), 'alert_failed');
//                }
            } else {
                $this->Session->setFlash(__("Page Name Already exists"), 'alert_failed');
            }
        }
        $this->request->data = $pageDetail;
    }

    public function paymentDownload($storeId = null) {
        $criteria = "StorePayment.is_deleted=0";
        //if (!empty($storeId)) {
        $criteria .= " AND (StorePayment.store_id ='" . $storeId . "')";
        //}
        $this->StorePayment->bindModel(array('belongsTo' => array('Plan' => array('fields' => array('name'), 'className' => 'Plan', 'foreignKey' => 'plan_id'), 'Store' => array('fields' => array('store_name'), 'className' => 'Store', 'foreignKey' => 'store_id'))), false);
        $list = $this->StorePayment->find('all', array('conditions' => array($criteria), 'order' => array('StorePayment.created' => 'DESC')));
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
        $filename = 'Payment_Download' . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('Payment Report');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Subscription Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Store Name');
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
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", $data['StorePayment']['id']);
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", $data['Store']['store_name']);
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", $data['Plan']['name']);
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", date('m-d-Y', strtotime($data['StorePayment']['payment_date'])));
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", $data['StorePayment']['amount']);
            $objPHPExcel->getActiveSheet()->setCellValue("F$i", $data['StorePayment']['payment_status']);

            $i++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function paymentListDownload() {
        $merchantId = $this->Session->read('merchantId');
        $this->loadModel('MerchantPayment');
        $criteria = "MerchantPayment.is_deleted=0 AND MerchantPayment.merchant_id = $merchantId";
        $this->MerchantPayment->bindModel(array('belongsTo' => array('Plan' => array('fields' => array('name'), 'className' => 'Plan', 'foreignKey' => 'plan_id'))), false);
        $list = $this->MerchantPayment->find('all', array('conditions' => array($criteria), 'order' => array('MerchantPayment.created' => 'DESC')));
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
        $filename = 'Payment_Download' . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('Payment Report');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Subscription Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Subscription Type');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Payment Date');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Amount($)');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Status');

        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);

        $i = 2;
        foreach ($list as $data) {
            $data = $this->Common->trimValue($data);
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", $data['MerchantPayment']['id']);
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", $data['Plan']['name']);
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", date('m-d-Y', strtotime($data['MerchantPayment']['payment_date'])));
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", $data['MerchantPayment']['amount']);
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", $data['MerchantPayment']['payment_status']);

            $i++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function backgroundImage() {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $this->set('merchantId', $merchantId);
        $this->loadModel('MerchantConfiguration');
        if ($this->request->is(array('post', 'put'))) {
//Background Image Upload
            if (!empty($this->data['Merchant']['back_image']) && !empty($this->data['Merchant']['back_image']['name'])) {
                $this->data = $this->Common->trimValue($this->data);
                if ($this->data['Merchant']['back_image']['error'] == 0) {
                    $response = $this->Common->uploadMenuItemImages($this->data['Merchant']['back_image'], '/merchantBackground-Image/', $merchantId);
                } elseif ($this->data['Merchant']['back_image']['error'] == 4) {
                    $response['status'] = true;
                    $response['imagename'] = '';
                }
                if (!$response['status']) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    $this->redirect(array('controller' => 'hq', 'action' => 'backgroundImage'));
                } else {
                    //Item Data
                    if ($response['imagename']) {
                        $this->request->data['Merchant']['background_image'] = $response['imagename'];
                    }
                }
            }
            if (!empty($this->data['Merchant']['logo']) && !empty($this->data['Merchant']['logo']['name'])) {
                $this->data = $this->Common->trimValue($this->data);
                if ($this->data['Merchant']['logo']['error'] == 0) {
                    $response = $this->Common->uploadMenuItemImages($this->data['Merchant']['logo'], '/merchantLogo/', $merchantId);
                } elseif ($this->data['Merchant']['logo']['error'] == 4) {
                    $response['status'] = true;
                    $response['imagename'] = '';
                }
                if (!$response['status']) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    $this->redirect(array('controller' => 'hq', 'action' => 'backgroundImage'));
                } else {
                    //Item Data
                    if ($response['imagename']) {
                        $this->request->data['Merchant']['logo'] = $response['imagename'];
                    }
                }
            } else {
                unset($this->request->data['Merchant']['logo']);
            }
            if (!empty($this->data['Merchant']['banner_image']) && !empty($this->data['Merchant']['banner_image']['name'])) {
                $this->data = $this->Common->trimValue($this->data);
                if ($this->data['Merchant']['banner_image']['error'] == 0) {
                    $response = $this->Common->uploadMenuItemImages($this->data['Merchant']['banner_image'], '/merchantBackground-Image/', $merchantId);
                } elseif ($this->data['Merchant']['banner_image']['error'] == 4) {
                    $response['status'] = true;
                    $response['imagename'] = '';
                }
                if (!$response['status']) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    $this->redirect(array('controller' => 'hq', 'action' => 'backgroundImage'));
                } else {
                    //Item Data
                    if ($response['imagename']) {
                        $this->request->data['Merchant']['banner_image'] = $response['imagename'];
                    }
                }
            } else {
                unset($this->request->data['Merchant']['banner_image']);
            }

//contact us background image 
            if (isset($this->data['Merchant']['contact_us_bgimage'])) {
                if ($this->data['Merchant']['contact_us_bgimage']['error'] == 0) {
                    $response = $this->Common->uploadMenuItemImages($this->data['Merchant']['contact_us_bgimage'], '/merchantBackground-Image/', $merchantId);
                } elseif ($this->data['Merchant']['contact_us_bgimage']['error'] == 4) {
                    $response['status'] = true;
                    $response['imagename'] = '';
                }

                if (!$response['status']) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    $this->redirect(array('controller' => 'hq', 'action' => 'backgroundImage'));
                } else {
                    //Item Data
                    if ($response['imagename']) {
                        $this->request->data['Merchant']['contact_us_bg_image'] = $response['imagename'];
                    }
                }
            }
            if (!empty($this->data['Merchant']['banner_image']) || !empty($this->data['Merchant']['contact_us_bg_image'])) {
                if ($this->Merchant->saveMerchant($this->request->data['Merchant'])) {
                    $this->Session->setFlash(__("Successfully Saved"), 'alert_success');
                } else {
                    $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                }
            }
            if (!empty($this->data['Merchant']['background_image'])) {
                if ($this->Merchant->saveMerchant($this->request->data['Merchant'])) {
                    $this->Session->setFlash(__("Successfully Saved"), 'alert_success');
                } else {
                    $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                }
            }
            if (!empty($this->data['Merchant']['logo'])) {
                if ($this->Merchant->saveMerchant($this->request->data['Merchant'])) {
                    $this->Session->setFlash(__("Successfully Saved"), 'alert_success');
                } else {
                    $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                }
            }
            if (!empty($this->data['Merchant'])) {
                if ($this->Merchant->saveMerchant($this->request->data['Merchant'])) {
                    $this->Session->setFlash(__("Successfully Saved"), 'alert_success');
                } else {
                    $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                }
            }
            $this->request->data['MerchantConfiguration']['merchant_id'] = $merchantId;
            if (empty($this->request->data['MerchantConfiguration']['id'])) {
                $this->MerchantConfiguration->create();
            }
            if ($this->MerchantConfiguration->save($this->request->data)) {
                $this->Session->setFlash(__("Update Successfully."), 'alert_success');
            } else {
                $this->Session->setFlash(__("Something went wrong."), 'alert_failed');
            }
            $this->redirect(array('controller' => 'hq', 'action' => 'backgroundImage'));
        }
        $merchantConfiguration = $this->MerchantConfiguration->findByMerchantId($merchantId);
        if (!empty($merchantConfiguration)) {
            $this->request->data = $merchantConfiguration;
        }
        $merchantInfo = $this->Merchant->fetchMerchantDetail($merchantId);
        if (!empty($merchantInfo)) {
            $this->request->data['Merchant']['id'] = $merchantInfo['Merchant']['id'];
            $this->request->data['Merchant']['logotype'] = $merchantInfo['Merchant']['logotype'];
            $this->request->data['Merchant']['background_image'] = $merchantInfo['Merchant']['background_image'];
            $this->request->data['Merchant']['logo'] = $merchantInfo['Merchant']['logo'];
            $this->request->data['Merchant']['banner_image'] = $merchantInfo['Merchant']['banner_image'];
            $this->request->data['Merchant']['contact_us_bg_image'] = $merchantInfo['Merchant']['contact_us_bg_image'];
        }
    }

    public function logoImage() {
        $this->layout = false;
        $merchantId = $this->Session->read('merchantId');
        if ($this->request->data) {
//Logo Image Upload
            if (isset($this->data['Merchant']['logo'])) {
                $this->data = $this->Common->trimValue($this->data);
                if ($this->data['Merchant']['logo']['error'] == 0) {
                    $response = $this->Common->uploadMenuItemImages($this->data['Merchant']['logo'], '/merchantLogo/', $merchantId);
                } elseif ($this->data['Merchant']['logo']['error'] == 4) {
                    $response['status'] = true;
                    $response['imagename'] = '';
                }
                if (!$response['status']) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    $this->redirect(array('controller' => 'hq', 'action' => 'backgroundImage'));
                } else {
                    //Item Data
                    if ($response['imagename']) {
                        $this->request->data['Merchant']['logo'] = $response['imagename'];
                    }
                }
            }
            if ($this->Merchant->saveMerchant($this->request->data['Merchant'])) {
                $this->Session->setFlash(__("Logo Image successfully saved"), 'alert_success');
                $this->redirect(array('controller' => 'hq', 'action' => 'backgroundImage'));
            } else {
                $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                $this->redirect(array('controller' => 'hq', 'action' => 'backgroundImage'));
            }
        }
    }

    public function deleteMerchantBackgroundPhoto($EncryptedMerchantID = null, $removeImg = null) {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Encryption->decode($EncryptedMerchantID);
        if ($merchantId && $removeImg) {
            $data['id'] = $merchantId;
            if ($removeImg == "LI") {
                $data['logo'] = '';
            } else if ($removeImg == "BI") {
                $data['background_image'] = '';
            } else if ($removeImg == "BANNERI") {
                $data['banner_image'] = '';
            } else if ($removeImg == "CONTACTUS") {
                $data['contact_us_bg_image'] = '';
            }
            if ($this->Merchant->saveMerchant($data)) {
                $this->Session->setFlash(__("Image successfully Deleted"), 'alert_success');
                $this->redirect(array('controller' => 'hq', 'action' => 'backgroundImage'));
            } else {
                $this->Session->setFlash(__("Some Problem occured"), 'alert_failed');
                $this->redirect(array('controller' => 'hq', 'action' => 'backgroundImage'));
            }
        }
    }

    public function viewStoreDetails($clearAction = null) {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'viewStoreDetails', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        $this->loadModel('Store');
        $merchantId = $this->Session->read('merchantId');
        $criteria = "Store.is_deleted=0 AND Store.merchant_id=$merchantId";

        if ($this->Session->read('hqStoreSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('hqStoreSearchData'), true);
        } else {
            $this->Session->delete('hqStoreSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }

        if (!empty($this->request->data)) {
            $this->Session->write('hqStoreSearchData', json_encode($this->request->data));
            if (isset($this->request->data['Store']['is_active']) && $this->request->data['Store']['is_active'] != '') {
                $active = trim($this->request->data['Store']['is_active']);
                $criteria .= " AND (Store.is_active =$active)";
            }
            if (!empty($this->request->data['Store']['search'])) {
                $value = trim($this->request->data['Store']['search']);
                if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $value)) {
                    $value = explode("'", $value);
                    $value = $value[0];
                    // one or more of the 'special characters' found in $string
                }
                $criteria .= " AND (Store.store_name LIKE '%" . $value . "%')";
            }
        }

        $this->paginate = array('conditions' => array($criteria), 'order' => array('Store.created' => 'DESC'));
        $transactionDetail = $this->paginate('Store');
        $this->set('list', $transactionDetail);
    }

    public function editStore($EncryptStoreID = null) {
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'viewStoreDetails', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        if (!empty($this->request->data)) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            if ($this->Store->checkSuperStoreUniqueName($this->request->data['Store']['store_name'], $this->request->data['Store']['merchant_id'], $this->request->data['Store']['id'])) {
                if ($this->Store->saveStoreInfo($this->request->data['Store'])) {
                    $this->Session->setFlash(__("Store details has been updated successfully."), 'alert_success');
                    $this->redirect(array('controller' => 'hq', 'action' => 'viewStoreDetails'));
                } else {
                    $this->Session->setFlash(__("Unable to save store details, Please try again."), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Store name already exists."), 'alert_failed');
            }
        }
        $this->loadModel('Store');
        $data['Store']['id'] = $this->Encryption->decode($EncryptStoreID);
        $storeDetail = $this->Store->getStoreDetail($data['Store']['id']);
        $this->request->data = $storeDetail;
    }

    public function activateStore($EncryptStoreID = null, $status = 0) {
        $this->autoRender = false;
        $data['Store']['id'] = $this->Encryption->decode($EncryptStoreID);
        $data['Store']['is_active'] = $status;
        if ($this->Store->saveStoreInfo($data)) {
            if ($status) {
                $SuccessMsg = "Store has been activated successully.";
            } else {
                $SuccessMsg = "Store has been de-activated successully.";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hq', 'action' => 'viewStoreDetails'));
        } else {
            $this->Session->setFlash(__("Some problem has been occured."), 'alert_failed');
            $this->redirect(array('controller' => 'hq', 'action' => 'viewStoreDetails'));
        }
    }

    public function reviewRatingSearch() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('StoreReview');
            $this->StoreReview->bindModel(array(
                'belongsTo' => array(
                    'Order' => array(
                        'className' => 'Order',
                        'foreignKey' => 'order_id'),
                )
                    )
            );
            if (!empty($_GET['storeID'])) {
                $storeID = $_GET['storeID'];
            } else {
                $merchant_id = $this->Session->read('merchantId');
                $storeID = $this->Store->getAllStoresByMerchantId($merchant_id);
            }
            $criteria = "";
            if (!empty($_GET['term'])) {
                $value = trim($_GET['term']);
                $criteria = "(StoreReview.review_comment LIKE '%" . $value . "%' OR Order.order_number LIKE '%" . $value . "%')";
            }
            $searchData = $this->StoreReview->find('all', array('fields' => array('Order.order_number', 'StoreReview.review_comment'), 'conditions' => array('StoreReview.store_id' => $storeID, 'StoreReview.is_deleted' => 0, $criteria)));
            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    $new_array[] = array('label' => $val['Order']['order_number'], 'value' => $val['Order']['order_number'], 'desc' => $val['Order']['order_number'] . '-' . $val['StoreReview']['review_comment']);
                };
            }
            echo json_encode($new_array);
        } else {
            exit;
        }
    }

    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('User');
            $merchant_id = $this->Session->read('merchantId');
            $criteria = "User.merchant_id =$merchant_id AND User.is_deleted=0 AND User.role_id=2";
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

    public function getStoreNames() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('User');
            $merchant_id = $this->Session->read('merchantId');
            $criteria = "Store.merchant_id =$merchant_id AND Store.is_deleted=0";
            $searchData = $this->Store->find('list', array('fields' => array('Store.store_name', 'Store.store_name'), 'conditions' => array('OR' => array('Store.store_name LIKE' => '%' . $_GET['term'] . '%'), $criteria)));
            echo json_encode($searchData);
        } else {
            exit;
        }
    }

    public function getMerchantStoreNames() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('MerchantStoreRequest');
            $merchant_id = $this->Session->read('merchantId');
            $criteria = "MerchantStoreRequest.merchant_id =$merchant_id AND MerchantStoreRequest.is_deleted=0";
            $searchData = $this->MerchantStoreRequest->find('list', array('fields' => array('MerchantStoreRequest.store_name', 'MerchantStoreRequest.store_name'), 'conditions' => array('OR' => array('MerchantStoreRequest.store_name LIKE' => '%' . $_GET['term'] . '%'), $criteria)));
            echo json_encode($searchData);
        } else {
            exit;
        }
    }

    public function getMerchantStorePaymentNames() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('StorePayment');
            $merchant_id = $this->Session->read('merchantId');
            $criteria = "StorePayment.is_deleted=0 AND Store.merchant_id=" . $merchant_id;
            $this->StorePayment->bindModel(array(
                'belongsTo' => array(
                    'Plan' => array(
                        'fields' => array('name'),
                        'className' => 'Plan',
                        'foreignKey' => 'plan_id'),
                    'Store' => array(
                        'fields' => array('store_name', 'merchant_id'),
                        'className' => 'Store',
                        'foreignKey' => 'store_id')
                )
                    ), false);
            $searchData = $this->StorePayment->find('list', array('fields' => array('Store.store_name', 'Store.store_name'), 'conditions' => array('OR' => array('Store.store_name LIKE' => '%' . $_GET['term'] . '%', 'Store.address LIKE' => '%' . $_GET['term'] . '%', 'Store.phone LIKE' => '%' . $_GET['term'] . '%', 'Store.email_id LIKE' => '%' . $_GET['term'] . '%', 'Store.store_url LIKE' => '%' . $_GET['term'] . '%'), $criteria), 'recursive' => 2));
            echo json_encode($searchData);
        } else {
            exit;
        }
    }

    public function getTransectionSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $merchant_id = $this->Session->read('merchantId');
            if (!empty($_GET['storeID'])) {
                $storeID = $_GET['storeID'];
            } else {

                $storeID = $this->Store->getAllStoresByMerchantId($merchant_id);
            }
            $this->loadModel('OrderPayment');
            $this->OrderPayment->bindModel(array(
                'belongsTo' => array(
                    'Order' => array(
                        'className' => 'Order',
                        'foreignKey' => 'order_id'
                    )
                )
                    ), false);
            $searchData = $this->OrderPayment->find('all', array('fields' => array('OrderPayment.id', 'OrderPayment.transection_id', 'Order.order_number'), 'conditions' => array('OR' => array('OrderPayment.transection_id LIKE' => '%' . $_GET['term'] . '%', 'Order.order_number LIKE' => '%' . $_GET['term'] . '%'), 'OrderPayment.store_id' => $storeID, 'OrderPayment.is_deleted' => 0, 'OrderPayment.merchant_id' => $merchant_id), 'order' => array('OrderPayment.created' => 'DESC')));
            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    $new_array[] = array('label' => $val['Order']['order_number'], 'value' => $val['Order']['order_number'], 'desc' => $val['Order']['order_number'] . ", " . $val['OrderPayment']['transection_id']);
                };
            }
            echo json_encode($new_array);
        } else {
            exit;
        }
    }

    public function getStoreContents() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('StoreContent');
            $merchant_id = $this->Session->read('merchantId');
            if (!empty($_GET['storeID'])) {
                $storeID = $_GET['storeID'];
            } else {
                $storeID = $this->Store->getAllStoresByMerchantId($merchant_id);
            }
            $criteria = "StoreContent.merchant_id =$merchant_id AND StoreContent.is_deleted=0";
            $searchData = $this->StoreContent->find('list', array('fields' => array('StoreContent.name', 'StoreContent.name'), 'conditions' => array('OR' => array('StoreContent.name LIKE' => '%' . $_GET['term'] . '%'), 'StoreContent.store_id' => $storeID, $criteria)));
            echo json_encode($searchData);
        } else {
            exit;
        }
    }

    public function getMerchantContents() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('StoreContent');
            $merchant_id = $this->Session->read('merchantId');
            $criteria = "MerchantContent.merchant_id =$merchant_id AND MerchantContent.is_deleted=0";
            $searchData = $this->MerchantContent->find('list', array('fields' => array('MerchantContent.name', 'MerchantContent.name'), 'conditions' => array('OR' => array('MerchantContent.name LIKE' => '%' . $_GET['term'] . '%'), $criteria)));
            echo json_encode($searchData);
        } else {
            exit;
        }
    }

    public function updateContentListing() {
        $this->autoRender = false;
        if (isset($_GET) && !empty($_GET)) {
            foreach ($_GET as $key => $val) {
                $this->MerchantContent->updateAll(array('position' => $val), array('id' => $this->Encryption->decode($key)));
            }
        }
    }
    public function homePageModal() {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $this->loadModel('HomeModal');
        if ($this->request->is(array('post', 'put'))) {
            $this->request->data['HomeModal']['added_from'] = 2;
            $this->request->data['HomeModal']['merchant_id'] = $merchantId;
            if ($this->HomeModal->save($this->request->data)) {
                $this->Session->setFlash(__("Info update successfly."), 'alert_success');
            } else {
                $this->Session->setFlash(__("Unable to save details, Please try again."), 'alert_failed');
            }
            $this->redirect($this->referer());
        }
        $this->request->data = $this->HomeModal->findByMerchantId($merchantId);
    }
	
    /* ------------------------------------------------
      Function name:updateHqImageOrder()
      Description: Update the display order for Image in slider
      created Date:26/09/2017
      created By:
      ----------------------------------------------------- */

    public function updateHqImageOrder() {
        $this->autoRender = false;
        if (isset($_GET) && !empty($_GET)) {
            foreach (array_filter($_GET) as $key => $val) {
                if (!empty($val) && !empty($key)) {
                    $this->MerchantGallery->updateAll(array('position' => $val), array('id' => $this->Encryption->decode($key)));
                }
            }
        }
    }

    public function orderItemOfferUsedDetail($EncryptOrderID = null) {
        $this->layout = "hq_dashboard";
        $order_id = $this->Encryption->decode($EncryptOrderID);
        $this->loadModel('OrderItemFree');
        $this->loadModel('Order');
        $this->OrderItemFree->bindModel(
                array('belongsTo' => array(
                        'Item' => array(
                            'className' => 'Item',
                            'foreignKey' => 'item_id',
                            'fields' => array('name'),
                        )
        )));
        $this->OrderItemFree->bindModel(
                array('belongsTo' => array(
                        'Order' => array(
                            'className' => 'Order',
                            'foreignKey' => 'order_id',
                        )
        )));
        $this->Order->bindModel(
                array('belongsTo' => array(
                        'User' => array(
                            'className' => 'User',
                            'foreignKey' => 'user_id',
                            'fields' => array('userName', 'email'),
                        ),
                        'DeliveryAddress' => array(
                            'className' => 'DeliveryAddress',
                            'foreignKey' => 'delivery_address_id',
                        )
        )));
        
        $totalFreeUnitsDataList = $this->OrderItemFree->find('all', array('recursive' => 2, 'fields' => array('OrderItemFree.order_id', 'Order.id', 'OrderItemFree.free_quantity', 'OrderItemFree.user_id', 'Item.name', 'Order.delivery_address_id', 'Order.user_id'), 'conditions' => array('OrderItemFree.order_id' => $order_id, 'OrderItemFree.is_active' => 1, 'OrderItemFree.is_deleted' => 0), 'order' => array('OrderItemFree.created' => 'DESC')));
        $guestEmail = $totalFreeUnitsData = array();
        if (!empty($totalFreeUnitsDataList)) {
            foreach ($totalFreeUnitsDataList as $key => $list) {
                if (!empty($list)) {
                    if ($list['OrderItemFree']['user_id'] == 0) {
                        $index = $list['Order']['DeliveryAddress']['email'];
                        if (in_array($index, $guestEmail)) {
                            $totalFreeUnitsData[$index]['count'] = $totalFreeUnitsData[$index]['count'] + $list['OrderItemFree']['free_quantity'];
                        } else {
                            $totalFreeUnitsData[$index]['count'] = $list['OrderItemFree']['free_quantity'];
                            $guestEmail[] = $index;
                        }
                        $totalFreeUnitsData[$index]['item_name'] = $list['Item']['name'];
                        $totalFreeUnitsData[$index]['name'] = $list['Order']['DeliveryAddress']['name_on_bell'];
                        $totalFreeUnitsData[$index]['email'] = $list['Order']['DeliveryAddress']['email'];
                    } else {
                        if (!empty($list['Order']['User']['email'])) {
                            $index = $list['Order']['User']['email'];
                            if (in_array($index, $guestEmail)) {
                                $totalFreeUnitsData[$index]['count'] = $totalFreeUnitsData[$index]['count'] + $list['OrderItemFree']['free_quantity'];
                            } else {
                                $totalFreeUnitsData[$index]['count'] = $list['OrderItemFree']['free_quantity'];
                                $guestEmail[] = $index;
                            }
                            $totalFreeUnitsData[$index]['item_name'] = $list['Item']['name'];
                            $totalFreeUnitsData[$index]['name'] = $list['Order']['User']['userName'];
                            $totalFreeUnitsData[$index]['email'] = $list['Order']['User']['email'];
                        }
                    }
                }
            }
        }
        $this->set('list', $totalFreeUnitsData);
    }

    public function offerUsedDetail($EncryptOrderId = null) {
        $this->layout = "hq_dashboard";
        $order_id = $this->Encryption->decode($EncryptOrderId);
        $this->loadModel('OrderOffer');
        $this->loadModel('Order');
        $this->loadModel('Offer');
        
        $this->OrderOffer->bindModel(
                array('belongsTo' => array(
                        'Offer' => array(
                            'className' => 'Offer',
                            'foreignKey' => 'offer_id',
                            'fields' => array('id', 'description', 'item_id', 'is_fixed_price'),
                        ),
                        'Order' => array(
                            'className' => 'Order',
                            'foreignKey' => 'order_id',
                        ),
                        'OrderOfferedItem' => array(
                            'className' => 'Item',
                            'foreignKey' => 'offered_item_id',
                        )
        )));
        $this->Order->bindModel(
                array('belongsTo' => array(
                        'User' => array(
                            'className' => 'User',
                            'foreignKey' => 'user_id',
                            'fields' => array('userName', 'email'),
                        ),
                        'DeliveryAddress' => array(
                            'className' => 'DeliveryAddress',
                            'foreignKey' => 'delivery_address_id',
                        )
        )));
        $this->Offer->bindModel(
                array('belongsTo' => array(
                        'Item' => array(
                            'className' => 'Item',
                            'foreignKey' => 'item_id',
                            'fields' => array('Item.id', 'Item.name'),
                        )
        ), 'hasMany' => array(
            'OfferDetail' => array(
                            'fields' => array('OfferDetail.discountAmt', 'OfferDetail.offerItemID'),
                            'className' => 'OfferDetail',
                            'foreignKey' => 'offer_id',
                            'bindingKey' => 'id'
                        )
        )));
        $totalOfferUsedLists = $this->OrderOffer->find('all', array('recursive' => 3, 'fields' => array('OrderOffer.quantity', 'Order.user_id', 'Order.delivery_address_id', 'Offer.id', 'Offer.description', 'Offer.is_fixed_price', 'Offer.offerprice', 'Offer.item_id', 'OrderOffer.offered_item_id'), 'conditions' => array('OrderOffer.order_id' => $order_id, 'OrderOffer.is_active' => 1, 'OrderOffer.is_deleted' => 0), 'order' => array('Order.created' => 'DESC')));
        $guestEmail = $totalOfferUsedList = $offerNewArray = array();
        if (!empty($totalOfferUsedLists)) {
            $index = 0;
            foreach ($totalOfferUsedLists as $key => $list) {
                if (!empty($list)) {
                    if(!in_array($list['Offer']['id'], $offerNewArray))
                    {
                        $offeredItemArray = $this->orderOfferItemNames($order_id, $list['Offer']['id']);
                        $offeredItemNames = '';
                        foreach ($offeredItemArray as $offeredItem)
                        {
                            $offeredItemNames .= $offeredItem['Item']['name'] . ', ';
                        }
                        $offeredItemNames = trim($offeredItemNames, ', ');
                        
                        $offerNewArray[] = $list['Offer']['id'];
                        $totalOfferUsedList[$index]['offer_id'] = $list['Offer']['id'];
                        $totalOfferUsedList[$index]['order_offer_item_id'] = $list['OrderOffer']['offered_item_id'];          
                        $totalOfferUsedList[$index]['is_fixed_price'] = $list['Offer']['is_fixed_price'];
                        $totalOfferUsedList[$index]['offerprice'] = $list['Offer']['offerprice'];
                        $totalOfferUsedList[$index]['offered_item_name'] = $offeredItemNames;
                        if ($list['Order']['user_id'] == 0) {
                            $totalOfferUsedList[$index]['description'] = $list['Offer']['description'];
                            $totalOfferUsedList[$index]['item_name'] = $list['Offer']['Item']['name'];
                            $totalOfferUsedList[$index]['offer_item'] = $list['Offer']['OfferDetail'];
                            $totalOfferUsedList[$index]['name'] = $list['Order']['DeliveryAddress']['name_on_bell'];
                            $totalOfferUsedList[$index]['email'] = $list['Order']['DeliveryAddress']['email'];

                        } else {
                            $totalOfferUsedList[$index]['description'] = $list['Offer']['description'];
                            $totalOfferUsedList[$index]['item_name'] = $list['Offer']['Item']['name'];
                            $totalOfferUsedList[$index]['offer_item'] = $list['Offer']['OfferDetail'];
                            $totalOfferUsedList[$index]['name'] = $list['Order']['User']['userName'];
                            $totalOfferUsedList[$index]['email'] = $list['Order']['User']['email'];
                        }
                    }
                }
                $index++;
            }
        }
        
        $this->set('list', $totalOfferUsedList);
    }

    function orderOfferItemNames($orderId = null, $offerId = null)
    {
        $this->loadModel('OrderOffer');
        $this->OrderOffer->bindModel(
                array('belongsTo' => array(
                        'Item' => array(
                            'className' => 'Item',
                            'foreignKey' => 'offered_item_id',
                            //'fields' => array('id', 'name'),
                        )
                    )
                )
            );
        $list = $this->OrderOffer->find('all', array('fields' => array('OrderOffer.id', 'OrderOffer.offered_item_id', 'Item.name'), 'conditions' => array('OrderOffer.order_id' => $orderId, 'OrderOffer.offer_id' => $offerId)));
        return $list;
    }
    
    /* ------------------------------------------------
      Function name:exportTransactionList()
      Description:Export excel list of transaction
      created:10/26/2015
      ----------------------------------------------------- */
    public function exportTransactionList() {
        $this->layout = false;
        $this->autoRender = false;
        
        $loginuserid = $this->Session->read('Auth.hq.id');
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'transactionList', $loginuserid)) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
        }
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $this->set('merchantId', $merchantId);
        $this->loadModel('OrderPayment');
        $criteria = "OrderPayment.is_deleted=0 AND Store.is_allow_transaction=1 AND Store.is_deleted=0 AND OrderPayment.merchant_id=" . $merchantId;
        $storeId = "";
        $value = '';
        
        if ($this->Session->read('TransactionSearchData')) {
            $this->request->data = json_decode($this->Session->read('TransactionSearchData'), true);
        }
        if (!empty($this->request->data)) {
            if (isset($this->request->data['Merchant']['store_id']) && !empty($this->request->data['Merchant']['store_id'])) {
                $storeId = $this->request->data['Merchant']['store_id'];
                $criteria .= " AND OrderPayment.store_id=$storeId";
            }
            if (!empty($this->request->data['Payment']['is_active'])) {
                $active = trim($this->request->data['Payment']['is_active']);
                $criteria .= " AND (OrderPayment.payment_status ='" . $active . "')";
            }
            if ($this->request->data['User']['from'] != '' && $this->request->data['User']['to'] != '') {
                $stratdate = $this->Dateform->formatDate($this->request->data['User']['from']);
                $enddate = $this->Dateform->formatDate($this->request->data['User']['to']);
                $criteria.= " AND (OrderPayment.created BETWEEN '" . $stratdate . "' AND '" . $enddate . "')";
            }
            if (!empty($this->request->data['Segment']['id'])) {
                $type = trim($this->request->data['Segment']['id']);
                $criteria .= " AND (Order.seqment_id =$type)";
            }
            if (!empty($this->request->data['User']['search'])) {
                $value = trim($this->request->data['User']['search']);
                $criteria .= " AND (OrderPayment.transection_id LIKE '%" . $value . "%' OR Order.order_number LIKE '%" . $value . "%')";
            }
        }
        $this->Store->unbindModel(array('belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent'), 'hasOne' => array('SocialMedia')), true);
        $this->OrderPayment->bindModel(array('belongsTo' => array(
                'Order' => array('className' => 'Order',
                    'foreignKey' => 'order_id'
                ),
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('store_name', 'is_allow_transaction'),
                    'conditions' => array('Store.is_allow_transaction' => 1),
                    'type' => 'inner'
                ),
            )
                ), false);
        $this->loadModel('Order');
        $this->Order->bindModel(array('belongsTo' => array(
                'Segment' => array(
                    'className' => 'Segment',
                    'foreignKey' => 'seqment_id',
                    'fields' => 'name'
                ),
            )
                ), false);
        
        $transactions = $this->OrderPayment->find('all', array('conditions' => array($criteria), 'order' => array('OrderPayment.created' => 'DESC')));
        if(!empty($transactions))
        {
            //Configure::write('debug', 2);
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
            $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
            $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->setTitle('HQ - Transactions' . date("Y-m-d"));

            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Order Id');
            $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Store Name');
            $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Transaction Id');
            $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Sub Total');
            $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Tax($)');
            $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Tip');
            $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Discount');
            $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Total Sales Amount ($)');
            $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Date');
            $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Payment Type');
            $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Payment Status');
            $objPHPExcel->getActiveSheet()->setCellValue('L1', 'Reason');
            $objPHPExcel->getActiveSheet()->setCellValue('M1', 'Response Code');
            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('J1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('K1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('L1')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('M1')->applyFromArray($styleArray);
            $i = 2;
            foreach ($transactions as $key => $data) {
                // Order No 
                $objPHPExcel->getActiveSheet()->setCellValue("A$i", $data['Order']['order_number']);
                
                // Store Name
                $objPHPExcel->getActiveSheet()->setCellValue("B$i", $data['Store']['store_name']);
                
                // Transaction No 
                $objPHPExcel->getActiveSheet()->setCellValue("C$i", (($data['OrderPayment']['transection_id'] != 0) ? $data['OrderPayment']['transection_id'] : ''));
                
                // Product Price
                $orderDetail = $this->Common->orderItemDetail($data['OrderPayment']['order_id']);
                $totalItemPrice = 0;
                if($orderDetail)
                {
                    foreach ($orderDetail as $itemKey => $itemVal)
                    {
                        if ($itemVal['OrderItem']['total_item_price']) {
                            $totalItemPrice += $itemVal['OrderItem']['total_item_price'];
                        }
                    }
                }
                $objPHPExcel->getActiveSheet()->setCellValue("D$i", $this->Common->amount_format($totalItemPrice));
                
                // Tax
                $objPHPExcel->getActiveSheet()->setCellValue("E$i", $this->Common->amount_format($data['Order']['tax_price']));
                
                // Tip
                $tipValue = (($data['Order']['tip'] && $data['Order']['tip'] > 0) ? $this->Common->amount_format($data['Order']['tip']) : '-');
                $objPHPExcel->getActiveSheet()->setCellValue("F$i", $tipValue);
                
                // Discount
                $discountData = '';
                $showcount = 0;
                if($data['Order']['coupon_code'] != null)
                {
                    $coupon_amount = $this->Common->amount_format($data['Order']['coupon_discount']);
                    $discountData .= $coupon_amount . "\n\r";
                }
                
                $promotionCount = $this->Common->usedOfferDetailCount($data['OrderPayment']['order_id']);       
                if($promotionCount > 0)
                {
                    $discountData .= "Promotions\n\r";
                }
                
                $extendedOffersCount = $this->Common->usedItemOfferDetailCount($data['OrderPayment']['order_id']);
                if($extendedOffersCount > 0)
                {
                    $discountData .= "Extended Offers\n\r";
                }
                $discountData = trim($discountData, "\n\r");
                if($discountData == '')
                {
                    $discountData = '-';
                }
                $objPHPExcel->getActiveSheet()->setCellValue("G$i", $discountData);
                $objPHPExcel->getActiveSheet()->getStyle("G$i")->getAlignment()->setWrapText(true);
                
                // Total Sales Amount ($)
                $totalPrice = $this->Common->amount_format(($data['OrderPayment']['amount'] - $data['Order']['coupon_discount']));
                $objPHPExcel->getActiveSheet()->setCellValue("H$i", $totalPrice);
                
                // Date
                $objPHPExcel->getActiveSheet()->setCellValue("I$i", $this->Dateform->us_format($this->Common->storeTimezone('', $data['OrderPayment']['created'])));
                
                // Payment Type
                $objPHPExcel->getActiveSheet()->setCellValue("J$i", $data['OrderPayment']['payment_gateway']);
                
                // Payment Status
                $objPHPExcel->getActiveSheet()->setCellValue("K$i", $data['OrderPayment']['payment_status']);
                
                // Reason
                $sReason = $data['OrderPayment']['response'];
                if ($sReason) {
                    if ($data['OrderPayment']['user_id'] == 0) {
                        $sReason .= '\nNon-members Payment';
                    }
                } else {
                    $sReason = "-";
                }
                $objPHPExcel->getActiveSheet()->setCellValue("L$i", $sReason);
                
                // Response Code
                $response = (($data['OrderPayment']['response_code']) ? $data['OrderPayment']['response_code'] : '-');
                $objPHPExcel->getActiveSheet()->setCellValue("M$i", $response);
                
                $i++;
            }
            $filename = 'HQ - Transactions' . date("Y-m-d") . ".xls"; //create a file
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename=' . $filename);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
        }
        else {
            $this->Session->setFlash(__('Record not Found.'), 'alert_failed');
            $this->redirect('/payments/paymentList/');
        }
        exit;
    }

}
