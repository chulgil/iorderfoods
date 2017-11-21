<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');

//ob_start();
/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    public $components = array('Session', 'Auth', 'Paginator', 'Common', 'Security', 'Paypal', 'Cookie');
    public $helpers = array('Common');
    public $store_layout;
    public $store_inner_pages;

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Security->validatePost = false;
        $this->Security->enabled = false;
        $this->Security->csrfCheck = false;

        $this->Superadmin = 1;
        $this->Hqadmin = 2;
        $this->Storeadmin = 3;
        $this->Frontsite = 4;
        $this->Hqfront = 5;



        $this->Auth->allow('ajaxStaticContent', 'test', 'selectMerchant', 'merchant', 'sendNewLetter', 'checkCombination', 'resetPassword', 'accountActivation', 'staticContent', 'removeOfferItem', 'storePhoto', 'storeLocation', 'signIn', 'accountActivation', 'fetchCoupon', 'items', 'fetchProduct', 'sizePrice', 'fetchToppingPrice', 'fetchCategoryInfo', 'cart', 'removeItem', 'addQuantity', 'orderDetails', 'cancelOffer', 'orderSave', 'success', 'status', 'guestOrdering', 'login', 'registration', 'store', 'checkEmail', 'forgetPassword', 'selectStore', 'paymentSection', 'typePrice', 'fetchToppingSizePrice', 'removeOrderItem', 'removeOrderOfferItem', 'menuItems', 'menuFetchProduct', 'menuFetchCategoryInfo', 'getlatesttotalamont', 'getTimeIntervalPrice', 'itemShare', 'getStoreTime', 'ajaxChangeOrderType', 'confirmOrder', 'checkPreference', 'popuplogin', 'dologin', 'menuItems', 'customerOrderDetail');
        $siteSettingData = ClassRegistry::init('MainSiteSetting')->getSiteSettings(); // global values
        $this->Auth->fields = array('username' => 'email', 'password' => 'password');
        //$this->_loginToBoth();
        if ($siteSettingData) {
            $this->smtp_host = "tls://" . $siteSettingData['MainSiteSetting']['smtp_host']; //SITE VARIABLES
            $this->smtp_port = $siteSettingData['MainSiteSetting']['smtp_port'];
            $this->smtp_username = $siteSettingData['MainSiteSetting']['smtp_username'];
            $this->smtp_password = $siteSettingData['MainSiteSetting']['smtp_password'];
            $this->google_site_key = $siteSettingData['MainSiteSetting']['google_site_key'];
            $this->google_secret_key = $siteSettingData['MainSiteSetting']['google_secret_key'];
            $this->front_email = "info@iorderfoods.com";
        }

        if (!empty($this->data)) {
            $controllerArray = array('hq', 'contents', 'templates', 'newsletters', 'hqnewsletters', 'hqtemplates', 'hq', 'hqconfigurations', 'stores', 'Stores', 'super');
            $actionArray = array('addPage', 'editPage', 'editTemplate', 'index', 'editNewsletter', 'newsLetterAdd', 'newsLetterEdit', 'addNewsletter', 'saveTermsAndPolicies', 'editSpecialDay', 'saveLayoutContent', 'merchantAddPage', 'merchantEditPage', 'configuration', 'manageSliderPhotos', 'editSliderImage', 'homePageModal');
            if (in_array($this->params['controller'], $controllerArray) && in_array($this->params['action'], $actionArray)) {
                // Do nothing as these action includeds HTML editor
            } else {
                $this->request->data = $this->_sanitizeData($this->request->data);
            }
        }

        /*
          if ($this->Session->check('store_id')) {
          $this->loadModel('Store');
          $storeInfo = $this->Store->fetchStorePaypalDetail($this->Session->read('store_id'));
          if (!empty($storeInfo)) {
          $this->Paypal->sandboxMode = false;
          $this->Paypal->config = array(
          'webscr' => 'https://www.paypal.com/webscr/',
          'endpoint' => 'https://api-3t.paypal.com/nvp/',
          'password' => trim($storeInfo['Store']['paypal_password']),
          'email' => trim($storeInfo['Store']['paypal_email']),
          'signature' => trim($storeInfo['Store']['paypal_signature'])
          );
          }
          }
         */
    }

    public function _loginToBoth() {//function is used to login if user go to merchant to store or vise-versa
        $storeUserMerchantId = $this->Session->read('Auth.User.merchant_id');
        //pr($storeUserMerchantId.'in Store');
        pr($_SESSION);
        $hqUserMerchantId = $this->Session->read('Auth.hqusers.merchant_id');
        //pr($hqUserMerchantId.'in Hq');
        $hq_id = $this->Session->read('hq_id');
        pr($hq_id.'hq_id');
        $merchant_id = $this->Session->read('merchant_id');
        pr($merchant_id.'merchant_id');
        if (empty($storeUserMerchantId) && !empty($hqUserMerchantId) && !empty($hq_id)) {//hq to store
            if ($hqUserMerchantId == $hq_id) {
                $this->Session->write('Auth.User', $this->Session->read('Auth.hqusers'));
            }
        } elseif (empty($hqUserMerchantId) && !empty($storeUserMerchantId) && !empty($merchant_id)) {//store to hq
            if ($storeUserMerchantId == $merchant_id) {
                $this->Session->write('Auth.hqusers', $this->Session->read('Auth.User'));
            }
        }
    }

    function clrSession() {
        if ($this->Session->check('store_id')) {
            $currentTime = $this->Common->gettodayDate(3);
            if ($this->Session->check('ordersummary')) {
                $date_aux = date_create_from_format('m-d-Y', $this->Session->read('ordersummary.pickup_date'));
                $sessiondate = date_format($date_aux, 'Y-m-d');
                $SessionOrderTime = $sessiondate . ' ' . $this->Session->read('ordersummary.pickup_hour') . ':' . $this->Session->read('ordersummary.pickup_minute') . ":00";
                if (strtotime($SessionOrderTime) < strtotime($currentTime)) {
                    $this->Session->delete('ordersummary');
                    $this->Session->delete('GuestUser');
                    $this->Session->delete('CartOfferM');
                }
            }
        }
    }

    function gmtDiff() {
        $this->loadModel('TimeZone');
        $timezone = date_default_timezone_get(); //get server time zone
        $dtz = new DateTimeZone($timezone);
        $time = new DateTime('now', $dtz);
        $diffInSeconds = $dtz->getOffset($time);

        $timezonedetail = $this->TimeZone->getTimezoneId($diffInSeconds);
        $Server_Gmt_diff = "-8:00";
        if (!empty($timezonedetail['TimeZone']['offset'])) {
            $serverGmt = explode(" ", $timezonedetail['TimeZone']['gmt']);
            $Server_Gmt_diff = $serverGmt[1];
        }
        $this->server_offset = $Server_Gmt_diff;
        Configure::write('server_offset', $this->server_offset);

        $Store_Gmt_diff = "-8:00";
        $storeadmintimezone = $this->TimeZone->find('first', array('conditions' => array("TimeZone.id" => $this->Session->read('admin_time_zone_id')), 'fields' => array('TimeZone.difference_in_seconds', 'TimeZone.code', 'TimeZone.gmt'), 'recursive' => -1));
        if (!empty($storeadmintimezone)) {
            $storeGmt = explode(" ", $storeadmintimezone['TimeZone']['gmt']);
            $Store_Gmt_diff = $storeGmt[1];
        }
        $this->store_offset = $Store_Gmt_diff;
        Configure::write('store_offset', $this->store_offset);
    }

    /* Redirect to default page  */

    function setDefaultPage() {
        $checkCookie = $this->Cookie->read('storecookiename');
        $controllerarray = array('hqreports', 'superreports', 'hqusers', 'cronJobs', 'shareSocials', 'hqtemplates', 'hqsettings', 'hqnewsletters', 'hqcategories', 'hqsizes', 'hqtypes', 'hqorders', 'hqsubpreferences', 'hqintervals', 'hqtoppings', 'hqitems', 'hqcoupons', 'hqoffers', 'WebServices', 'WebTests', 'hqitemoffers', 'hqstores', 'hqmenus', 'orderOverviews', 'coupons', 'hqcustomers', 'hqfeatures', 'hqdeals', 'datasyncs', 'adminservices', 'hqconfigurations', 'MBServices', 'AdminTests','AppServices','AppAdminServices', 'CronJobs', 'hqsalesreports');
        if (!in_array($this->params['controller'], $controllerarray)) {
            if (!$this->Session->check('admin_store_id') && !$this->Session->check('store_id') && $this->params['action'] != 'selectStore') {
                $string = BASE_URL;
                $parts = parse_url($string);
                $isIP = (bool) ip2long($parts['path']);
                if (!$isIP) {
                    $this->redirect('/');
                } elseif (!empty($checkCookie)) {
                    $this->redirect(array('controller' => $this->Cookie->read('storecookiename'), 'action' => ''));
                } else {
                    $this->redirect(array('controller' => 'users', 'action' => 'selectStore'));
                }
            }
        } else {
            $this->autoParam();
        }
    }

    /* Set NZ safe globals */

    function NZSafe() {
        if ($this->Session->check('store_id')) {
            $this->loadModel("Store");
            $this->loadModel("NzsafeUser");
            $timeZoneInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $this->Session->read('store_id')), 'fields' => array('Store.time_zone_id'), 'recursive' => -1));
            $this->Session->write('front_time_zone_id', $timeZoneInfo['Store']['time_zone_id']);
            $this->Session->read('Auth.User.id');
            $nzsafeInfo = $this->NzsafeUser->getUser($this->Session->read('Auth.User.id'));
            $nzsafe_data_app = $nzsafeInfo['NzsafeUser'];
            return $nzsafe_data_app;
        }
    }

    /* Identify Panel & set Auth varibles */

    function assignAuth() {
        $panelarr = array('users', 'super', 'stores', 'hq', 'hqusers');
        if (in_array($this->params['controller'], $panelarr)) {
            $func = 'Auth_' . $this->params['controller'];
            $this->$func();
        } else {
            $this->autoParam();
        }
    }

    function autoParam() {
        if ($this->params['pass']) {
            $params = $this->params['pass'];
        } elseif ($this->params['named']) {
            $params = $this->params['named'];
        } else {
            $params = array();
        }
        $url = array('controller' => $this->params['controller'], 'action' => $this->params['action']);
        if ($params) {
            $this->Auth->loginAction = array_merge($url, $params);
            $this->Auth->logoutRedirect = array_merge($url, $params);
            $this->Auth->loginRedirect = array_merge($url, $params);
        } else {
            $this->Auth->loginAction = array('controller' => $this->params['controller'], 'action' => $this->params['action']);
            $this->Auth->logoutRedirect = array('controller' => 'store', 'action' => 'login');
            $this->Auth->loginRedirect = array('controller' => 'store', 'action' => 'dashboard');
        }
    }

    function Auth_users() {
        $this->Auth->loginAction = array('controller' => 'users', 'action' => 'merchant');
        $this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'merchant');
        $this->Auth->loginRedirect = array('controller' => 'users', 'action' => 'merchant');
    }

    function Auth_super() {
        $this->Auth->loginAction = array('controller' => 'super', 'action' => 'login');
        $this->Auth->logoutRedirect = array('controller' => 'super', 'action' => 'login');
        $this->Auth->loginRedirect = array('controller' => 'super', 'action' => 'dashboard');
    }

    function Auth_hq() {
        $this->Auth->loginAction = array('controller' => 'hq', 'action' => 'login');
        $this->Auth->logoutRedirect = array('controller' => 'hq', 'action' => 'login');
        $this->Auth->loginRedirect = array('controller' => 'hq', 'action' => 'dashboard');
    }

    function Auth_hqusers() {
        $this->Auth->loginAction = array('controller' => 'hqusers', 'action' => 'merchant');
        $this->Auth->logoutRedirect = array('controller' => 'hqusers', 'action' => 'merchant');
        $this->Auth->loginRedirect = array('controller' => 'hqusers', 'action' => 'merchant');
    }

    function Auth_stores() {
        $this->loadModel("Store");
        if ($this->Session->check('admin_store_id')) {
            $timeZoneInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $this->Session->read('admin_store_id')), 'fields' => array('Store.time_zone_id'), 'recursive' => -1));
            $this->Session->write('admin_time_zone_id', $timeZoneInfo['Store']['time_zone_id']);
            $this->Auth->loginAction = array('controller' => 'store', 'action' => 'login');
            $this->Auth->logoutRedirect = array('controller' => 'store', 'action' => 'login');
            $this->Auth->loginRedirect = array('controller' => 'store', 'action' => 'dashboard');
        }
    }

    function InvalidLogin($roleId = null) {
        if ($roleId) {
            if ($roleId == 4) {
                $encrypted_storeId = $this->Encryption->encode($this->Session->read('store_id'));
                $encypted_merchantId = $this->Encryption->encode(AuthComponent::User('merchant_id'));
                $this->redirect(array('controller' => 'users', 'action' => 'customerDashboard', $encrypted_storeId, $encypted_merchantId));
            } elseif ($roleId == 3) {
                $this->redirect(array('controller' => 'stores', 'action' => 'dashboard'));
            } elseif ($roleId == 2) {
                $this->redirect(array('controller' => 'hq', 'action' => 'dashboard'));
            }
        } else {
            $this->redirect(array('controller' => 'users', 'action' => 'login'));
        }
    }

    function _setupSecurity() {
        $this->Security->blackHoleCallback = '_badRequest';
        if (Configure::read('forceSSL')) {
            $this->Security->requireSecure('*');
        }
    }

    /**
     * The main SecurityComponent callback.
     * Handles both missing SSL problems and general bad requests.
     */
    function _badRequest() {
        if (Configure::read('forceSSL') && !$this->RequestHandler->isSSL()) {
            $this->_forceSSL();
        } else {
            $this->redirect(array('controller' => 'error', 'action' => 'error'));
        }
        exit;
    }

    /**
     * Redirect to the same page, but with the https protocol and exit.
     */
    function _forceSSL() {
        $this->redirect('https://' . BASE_URL . $this->here);
        exit;
    }

    function _checkMerchant() {
        if ($this->params['prefix'] == 'merchant') {
            AuthComponent::$sessionKey = 'Auth.hqUsers';
            $this->Auth->loginAction = array(
                'controller' => 'hqUsers',
                'action' => 'index'
            );

            $this->Auth->loginRedirect = array(
                'controller' => 'hqUsers',
                'action' => 'index'
            );

            $this->Auth->logoutRedirect = array(
                'controller' => 'hqUsers',
                'action' => 'index'
            );
        }
    }

    public function _sanitizeData($data) {
        if (empty($data)) {
            return $data;
        }
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $data[$key] = $this->_sanitizeData($val);
            }
            return $data;
        } else {
            $data = trim(strip_tags($data));
            return $data;
        }
    }

    public function states() {
        $this->loadModel('States');
        return $this->States->find('list');
    }

    function userLoginCheck() {
        $roleId = AuthComponent::User('role_id');
        $roles = array('4', '5');
        if (!in_array($roleId, $roles)) {
            $this->InvalidLogin();
        }
    }

    protected function _addDefaultFeaturedList($storeId = null) {
        $this->autoRender = false;
        $this->layout = false;
        if (!empty($storeId)) {
            $this->loadModel('Store');
            $merchantData = $this->Store->findById($storeId, array('merchant_id'));
            if (!empty($storeId) && !empty($merchantData)) {
                $this->loadModel('DefaultFeaturedSection');
                $this->loadModel('StoreFeaturedSection');
                $sectionData = $this->DefaultFeaturedSection->find('all', array('conditions' => array('is_active' => 1, 'is_deleted' => 0)));
                foreach ($sectionData as $sData) {
                    unset($sData['DefaultFeaturedSection']['id'], $sData['DefaultFeaturedSection']['is_active'], $sData['DefaultFeaturedSection']['is_deleted'], $sData['DefaultFeaturedSection']['created'], $sData['DefaultFeaturedSection']['modified']);
                    $sData['StoreFeaturedSection'] = $sData['DefaultFeaturedSection'];
                    $sData['StoreFeaturedSection']['store_id'] = $storeId;
                    $sData['StoreFeaturedSection']['merchant_id'] = $merchantData['Store']['merchant_id'];
                    $this->StoreFeaturedSection->create();
                    $this->StoreFeaturedSection->saveSection($sData);
                }
                $this->Session->setFlash(__("List added successfully"), 'alert_success');
                $this->redirect($this->referer());
            } else {
                $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                $this->redirect($this->referer());
            }
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect($this->referer());
        }
    }

    /* ------------------------------------------------
      Function name: orderFaxrelay()
      Description: Get details of order placed
      created:14/12/2015
      ----------------------------------------------------- */

    public function orderFaxrelay($orderId = null, $storeID = null, $merchant_id = null) {
        $this->autoRender = false;
        $printdata = $this->Common->getOrderFaxFormat($orderId, $storeID, $merchant_id);
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

    public function _deleteCategoryRelatedData($categoryId = null) {
        if (!empty($categoryId)) {
            $this->loadModel('Item');
            $itemIds = $this->Item->find('list', array('fields' => array('id'), 'conditions' => array('category_id' => $categoryId)));
            $this->Item->updateAll(array('Item.is_deleted' => 1), array('Item.category_id' => $categoryId));
            $this->loadModel('ItemOffer');
            $this->ItemOffer->updateAll(array('ItemOffer.is_deleted' => 1), array('ItemOffer.category_id' => $categoryId));
            $this->loadModel('Topping');
            $this->Topping->updateAll(array('Topping.is_deleted' => 1), array('Topping.category_id' => $categoryId));
            $this->loadModel('Size');
            $this->Size->updateAll(array('Size.is_deleted' => 1), array('Size.category_id' => $categoryId));
            if (!empty($itemIds)) {
                $this->loadModel('ItemType');
                $this->ItemType->updateAll(array('ItemType.is_deleted' => 1), array('ItemType.item_id' => $itemIds));

                $this->loadModel('ItemPrice');
                $this->ItemPrice->updateAll(array('ItemPrice.is_deleted' => 1), array('ItemPrice.item_id' => $itemIds));

                $this->loadModel('IntervalPrice');
                $this->IntervalPrice->updateAll(array('IntervalPrice.is_deleted' => 1), array('IntervalPrice.item_id' => $itemIds));
                $this->loadModel('FeaturedItem');
                $this->FeaturedItem->updateAll(array('FeaturedItem.is_deleted' => 1), array('FeaturedItem.item_id' => $itemIds));
                $this->loadModel('ItemDefaultTopping');
                $this->ItemDefaultTopping->updateAll(array('ItemDefaultTopping.is_deleted' => 1), array('ItemDefaultTopping.item_id' => $itemIds));
                $this->loadModel('Offer');
                $this->Offer->updateAll(array('Offer.is_deleted' => 1), array('Offer.item_id' => $itemIds));
                $this->loadModel('ToppingPrice');
                $this->ToppingPrice->updateAll(array('ToppingPrice.is_deleted' => 1), array('ToppingPrice.item_id' => $itemIds));
            }
        }
    }

    public function _checkNowTime($orderType = null) {
        $nowTime = array();
        $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        $today = 1;
        $orderType = ($orderType) ? $orderType : $this->Session->read('Order.order_type');
        $finaldata = $this->Common->getNextDayTimeRange($current_date, $today, $orderType);
        $timearray = array_diff($finaldata['time_range'], $finaldata['time_break']);
        $nowTime['pickup_time'] = reset($timearray);
        $explodeVal = explode("-", $finaldata['currentdate']);
        $finaldata['currentdate'] = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];
        $nowTime['pickup_date'] = $finaldata['currentdate'];
        $nowTime['pickup_date_time'] = $finaldata['currentdate'] . ' ' . $nowTime['pickup_time'];
        $nowTime['setPre'] = $finaldata['setPre'];
        return $nowTime;
    }

    public function _checkStoreStatus($orderType = null) {
        //date time div start
        $this->loadModel('Store');
        $storeId = $this->Session->read('store_id');
        $merchantID = $this->Session->read('merchant_id');
        $PreorderAllowed = $this->Store->checkPreorder($storeId, $merchantID);
        if (!empty($orderType)) {
            $nowAvail = $this->Store->getNowAvailability($orderType, $storeId);
        }
        $current_date = date("Y-m-d", (strtotime($this->Common->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        $today = 1;
        $finaldata = $this->Common->getNextDayTimeRange($current_date, $today, $orderType);
        $setPre = $finaldata['setPre'];
        /*
          PreorderAllowed - 0 (Preorder not allow), 1 (Allowed) - Flag Based
          nowAvail - 0 (current date Black out), 1 (Current day available)- Date based
          setPre - 0 (Now is avalable), 1 (Preorder is available) - Time based
          close day  - array based on holidays dates
         */
        if (!empty($PreorderAllowed) && !empty($nowAvail)) {
            //echo "Both are avalibale Show calendar";
        } elseif (!empty($PreorderAllowed) && empty($nowAvail)) {
            //echo "Only Preorder allowed Show calendar";
        } elseif (empty($PreorderAllowed) && !empty($nowAvail) && empty($setPre)) {
            //echo "Only Now allowed not to Show calendar";
        } else {
            //echo "None is available Store is closed";
            return false;
        }
        return true;
        //date time div end
    }

}
