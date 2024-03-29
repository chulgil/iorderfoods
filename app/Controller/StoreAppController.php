<?php

App::uses('AppController', 'Controller');

class StoreAppController extends AppController {

    public $components = array('Session', 'Auth', 'Paginator', 'Common', 'Security', 'Paypal', 'Cookie');
    public $helpers = array('Common', 'Session');
    public $store_layout;
    public $store_inner_pages;

    public function beforeFilter() {
        parent::beforeFilter();
        //echo "Start of Store App Controller<br>";
        $nzsafe_data_app = $this->NZSafe();
        $this->set(compact('nzsafe_data_app'));

        $cartcount = $this->cartCount();
        $this->set(compact('cartcount'));

        $nowAvail = $this->blackOutDays();
        $this->set(compact('nowAvail'));

        $this->_countStoreReviewImages();
        if ($this->Session->check('admin_store_id')) {
            $this->_modulePermissions();
        }

        if ($this->Session->check('admin_time_zone_id')) {
            $this->gmtdiff();
        }
        //for country code on guest user login popup
        $this->loadModel('CountryCode');
        $countryCode = $this->CountryCode->fetchAllCountryCode();
        $this->set(compact('countryCode'));

        if ($this->params['controller'] == 'users' && $this->params['action'] == 'store') {

            $this->setFrontStore($this->params->url);
        } elseif ($this->params['controller'] == 'stores' && $this->params['action'] == 'store') {
            $this->setBackStore($this->params->url);
        } elseif (!$this->Session->check('Auth.Admin.id') && !$this->Session->check('Auth.User.id')) {
            if (!$this->Session->check('store_id') && !$this->Session->check('admin_store_id')) {
                if($this->params['controller']=='orders' && $this->params['action'] == 'confirmOrder' && !empty($this->params['pass'][0])) {
                    
                } else {
                    header('Location:' . Router::fullbaseUrl());
                    exit;
                }
            }
        }

        //Store Static Pages
        if ($this->Session->check('store_url')) {
            $this->Store->bindModel(array(
                'hasMany' => array(
                    'StoreAvailability' => array('fields' => array('id', 'day_name', 'store_id', 'start_time', 'end_time', 'is_closed'), 'conditions' => array('StoreAvailability.is_active' => 1, 'StoreAvailability.is_deleted' => 0)),
                )), false);
            $store_data_app = $this->getStoreData($this->Session->read('store_url'));
            $current_date = $this->Common->getcurrentTime($store_data_app['Store']['id'], 1);
            $dateTime = date("l", strtotime($current_date)) . "\n";
            $store_data_app['Store']['is_store_open'] = 0; //Store 0=closed,1=opened 
            foreach ($store_data_app['StoreAvailability'] as $storeAvailability) {
                $dateTime = date("l", strtotime($current_date)) . "\n";
                if ($storeAvailability["day_name"] == trim($dateTime)) {
                    if ($storeAvailability['is_closed'] == 1) {
                        $store_data_app['Store']['is_store_open'] = 0;
                    } else {
                        $store_data_app['Store']['is_store_open'] = 1;
                    }
                }
            }
            $dateArr = explode(" ", $current_date);
            $storeHolidayCheck = $this->Common->storeHolidayCheck($store_data_app['Store']['id'], null, $dateArr[0]);
            if (!empty($storeHolidayCheck)) {
                $store_data_app['Store']['is_store_open'] = 0;
            }
            $this->loadModel('StoreSetting');
            $storeSetting = $this->StoreSetting->find('first', array('conditions' => array('store_id' => $store_data_app['Store']['id']), 'fields' => array('store_closed', 'order_allow')));
            $store_data_app = array_merge($store_data_app, $storeSetting);
            if (!empty($storeSetting['StoreSetting']['store_closed'])) {
                $store_data_app['Store']['is_store_open'] = 1;
            } else {
                $store_data_app['Store']['is_store_open'] = 0;
            }
            $Design = $store_data_app['StoreTheme']['layout'];
            $navigation = $store_data_app['Store']['navigation'];
            $color = $store_data_app['Store']['theme_color_id'];
            $theme = $store_data_app['Store']['store_theme_id'];
            if (!defined('THEMENAME'))
                define('THEMENAME', 'IOF-' . $theme);
            if (!defined('NAVIGATION'))
                define('NAVIGATION', $navigation);  // 1- Verticle , 2- Horizontal
            if ($navigation == 1) {
                if (!defined('KEYWORD'))
                    define('KEYWORD', $store_data_app['StoreTheme']['keyword'] . "V");
                if (!defined('THEME-NAME'))
                    define('THEME-NAME', 'IOF' . $theme);
                if (!defined('SELECTEDTHEME'))
                    define('SELECTEDTHEME', " IOF-THEME-" . $theme . " IOF-V" . " IOF-COLOR-" . $color);
            } else {
                if (!defined('KEYWORD'))
                    define('KEYWORD', $store_data_app['StoreTheme']['keyword'] . "H");
                if (!defined('SELECTEDTHEME'))
                    define('SELECTEDTHEME', " IOF-THEME-" . $theme . " IOF-H" . " IOF-COLOR-" . $color);
            }


            if (!defined('DESIGN'))
                define('DESIGN', $Design);
            if (DESIGN == 4) {
                if ($store_data_app['Store']['navigation'] == 1) { //vertical
                    $store_layout = 'vertical';
                    $store_inner_pages = 'vertical_inner';
                } else { //horizontal
                    $store_layout = 'horizontal';
                    $store_inner_pages = 'horizontal_inner';
                }
            } else if (DESIGN == 1) {
                $store_layout = 'aaronlayout';
                $store_inner_pages = 'aaronlayout';
            } else if (DESIGN == 2) {
                $store_layout = 'chloelayout';
                $store_inner_pages = 'chloelayout';
            } else if (DESIGN == 3) {
                $store_layout = 'dasollayout';
                $store_inner_pages = 'dasollayout';
            }
            $this->store_layout = $store_layout;
            $this->store_inner_pages = $store_inner_pages;
            //store style
            if (!empty($store_data_app['Store']['id']) && !empty($store_data_app['Store']['navigation']) && !empty($store_data_app['Store']['store_theme_id'])) {
                $this->loadModel('StoreStyle');
                $storeStyle = $this->StoreStyle->find('first', array('conditions' => array('store_id' => $store_data_app['Store']['id'], 'navigation' => $store_data_app['Store']['navigation'], 'store_theme_id' => $store_data_app['Store']['store_theme_id'])));
                if (!empty($storeStyle)) {
                    $this->set('storeStyle', $storeStyle);
                }
            }
            $this->set(compact('store_data_app'));
            $timeZoneInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $this->Session->read('store_id')), 'fields' => array('Store.time_zone_id', 'Store.service_fee', 'Store.service_fee_type'), 'recursive' => -1));
            $this->Session->write('front_time_zone_id', $timeZoneInfo['Store']['time_zone_id']);
            if (isset($timeZoneInfo['Store']['service_fee'])) {
                $this->Session->write('service_fee', $timeZoneInfo['Store']['service_fee']);
            }
            
            if (isset($timeZoneInfo['Store']['service_fee_type'])) {
                $this->Session->write('service_fee_type', $timeZoneInfo['Store']['service_fee_type']);
            }
        }


        if (($this->Session->check('admin_store_id')) && ($this->params['controller'] == 'stores')) {
            $this->assignBackAuth();
        } elseif (($this->Session->check('store_id')) && ($this->params['controller'] == 'users')) {
            $this->assignFrontAuth();
        }


        if ($this->Session->read('Auth.User.role_id') == 4) {
            $this->loadModel("Store");
            $timeZoneInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $this->Session->read('store_id')), 'fields' => array('Store.time_zone_id'), 'recursive' => -1));
            $this->Session->write('front_time_zone_id', $timeZoneInfo['Store']['time_zone_id']);
        }

        if ($this->Session->read('Auth.Admin.role_id') == 3) {
            $this->loadModel("Store");
            $timeZoneInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $this->Session->read('admin_store_id')), 'fields' => array('Store.time_zone_id'), 'recursive' => -1));
            $this->Session->write('admin_time_zone_id', $timeZoneInfo['Store']['time_zone_id']);
        }

        $this->assignAuth();
        //echo "End of Store App Controller<br>";
    }

    function cartCount() {
        $cartcount = 0;
        if ($this->Session->check('store_id') && $this->Session->check('cart')) {
            $cart = $this->Session->read('cart');
            foreach ($cart as $key => $itemarr) {
                if (!empty($itemarr['Item']['quantity']))
                    $cartcount+=$itemarr['Item']['quantity'];
            }
        }
        return $cartcount;
    }

    function _countStoreReviewImages() {
        $store_id = $this->Session->read('store_id');
        if (!empty($store_id)) {
            $this->loadModel('StoreReviewImage');
            $count = $this->StoreReviewImage->countStoreReviewImages($store_id);
            $this->set('imageCount', $count);
        } else {
            $this->set('imageCount', 0);
        }
    }

    /* BlackOut Days */

    function blackOutDays() {
        if ($this->Session->check('Order.order_type')) {
            $this->loadModel('Store');
            $ordeType = $this->Session->read('Order.order_type');
            $NowAvail = $this->Store->getNowAvailability($ordeType, $this->Session->read('store_id'));
            return $NowAvail;
        }
    }

    /* ---------------------------------------------
      Function name:checkAddress
      Description:To verify the address
      ----------------------------------------------- */

    function checkAddress($address = null, $state = null, $city = null, $zip = null) {
        $this->autoRender = false;
        if (isset($_POST)) {
            $zipCode = ltrim($zip, " ");
            $stateName = $state;
            $cityName = strtolower($city);
            $cityName = ucwords($cityName);
            $dlocation = $address . " " . $cityName . " " . $stateName . " " . $zipCode;
            $adjuster_address2 = str_replace(' ', '+', $dlocation);
            $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $adjuster_address2 . '&sensor=false');
            $output = json_decode($geocode);
            if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
                echo 2;
                die; // Bad Address
            } else {
                $latitude = @$output->results[0]->geometry->location->lat;
                $longitude = @$output->results[0]->geometry->location->lng;
                $formated_address = @$output->results[0]->formatted_address;
                if ($latitude) {
                    echo 1;
                    die; // Good Address
                }
            }
        }
    }

    public function getStoreData($storeUrl) { 
        $this->loadModel('Store');
        $this->Store->bindModel(array('belongsTo' => array('StoreTheme')), false);
        $this->Store->bindModel(array('belongsTo' => array('StoreFont')), false);
        $this->Store->bindModel(array('hasOne' => array('SocialMedia')), false);
        $this->Store->bindModel(array('hasMany' => array('StoreGallery' => array('conditions' => array('is_active' => 1, 'is_deleted' => 0), 'order' => array('StoreGallery.position' => 'ASC')), 'StoreContent' => array('fields' => array('name', 'id', 'page_position', 'position'), 'conditions' => array('is_active' => 1, 'is_deleted' => 0), 'order' => array('StoreContent.position' => 'ASC')))), false);
        $store_result = $this->Store->fetchStoreImage($storeUrl);
        return $store_result;
    }

    /* Assign Login auth to Store Admin Panel */

    function assignBackAuth() {
        AuthComponent::$sessionKey = 'Auth.Admin';
        $this->Auth->authenticate = array(
            'Form' => array(
                'userModel' => 'User',
                'fields' => array('username' => 'email', 'password' => 'password', 'store_id'),
                'scope' => array('User.store_id' => $this->Session->read('admin_store_id'), 'User.role_id' => 3, 'User.is_active' => 1, 'User.is_deleted' => 0)
            )
        );
    }

    /* Assign Login auth to Store Front */

    function assignFrontAuth() {
        AuthComponent::$sessionKey = 'Auth.User';
        $this->Auth->authenticate = array(
            'Form' => array(
                'userModel' => 'User',
                'fields' => array('username' => 'email', 'password' => 'password', 'store_id'),
                'scope' => array('User.merchant_id' => $this->Session->read('merchant_id'), 'User.role_id' => array('4', '5'), 'User.is_active' => 1, 'User.is_deleted' => 0)
            )
        );
    }

    /* Identify & Set session for Store panel panel  */

    function setBackStore() {
        $subdomain = $_SERVER['SERVER_NAME'];
	    if ($subdomain) {
		$store_name = $subdomain;
	    } else {
		$requestParam = explode('/', $this->params->url);
		$store_name = trim($requestParam[0]); // Name of the store which we will change later with Saas
	    }
	    $StoreAdminid=$this->Session->read('Auth.Admin.id');
        if ($store_name) {
            $this->loadModel('Store');
            $store_result = $this->Store->store_info($store_name);
            if ($store_result) {
                $storeName = $store_result['Store']['store_name'];
                $this->Session->write('admin_domainname', $store_name);
                $this->Session->write('admin_storeName', $storeName);
                $this->Session->write('admin_store_id', $store_result['Store']['id']);
                $this->Session->write('admin_merchant_id', $store_result['Store']['merchant_id']);
            } else {
                $this->redirect(array('controller' => 'users', 'action' => 'selectStore'));
            }
        }
    }

    /* Identify & Set session for Store Front panel  */

    function setFrontStore($params) {


        $subdomain = $_SERVER['SERVER_NAME'];
        if ($subdomain) {
            $store_name = $subdomain;
        } else {
            $requestParam = explode('/', $params);
            $store_name = trim($requestParam[0]); // Name of the store which we will change later with Saas
        }	
        $sid=$this->Session->read('store_id');
        $urlParts = parse_url($store_name);
        if(!empty($urlParts['host'])){
             $store_name = preg_replace('/^www\./', '', $urlParts['host']);
        }
        $Storeuserid = $this->Session->read('Auth.User.id');
        if ($store_name) {
            $this->loadModel('Store');
            $store_result = $this->Store->store_info($store_name);
            if ($store_result) {
                $storeName = $store_result['Store']['store_name'];
                $store_url = $store_result['Store']['store_url'];
                $store_phone = $store_result['Store']['phone'];
                if (isset($store_result['Store']['service_fee'])) {
                    $this->Session->write('service_fee', $store_result['Store']['service_fee']);
                }
//                if (isset($store_result['Store']['delivery_fee'])) {
//                    $this->Session->write('delivery_fee', $store_result['Store']['delivery_fee']);
//                }
                $this->Session->write('minprice', $store_result['Store']['minimum_order_price']);
                $this->Session->write('storeName', $storeName);
                $this->Session->write('store_url', $store_url);
                $this->Session->write('store_phone', $store_phone);
                $this->Session->write('store_id', $store_result['Store']['id']);
                $this->Session->write('merchant_id', $store_result['Store']['merchant_id']);
                $this->Cookie->write('storecookiename', $store_url);
                $this->Session->write('front_time_zone_id', $store_result['Store']['time_zone_id']);
            } else {
                $this->redirect(array('controller' => 'users', 'action' => 'selectStore'));
            }
        }
    }

    private function _modulePermissions() {
        $this->loadModel('ModulePermission');
        $storeId = $this->Session->read('admin_store_id');
        $modulePermission = $this->ModulePermission->findByStoreId($storeId);
        if (empty($modulePermission)) {
            $this->loadModel('StoreSetting');
            $settingData = $this->StoreSetting->findByStoreId($storeId);
            if (empty($settingData)) {
                $data['store_id'] = $storeId;
                $this->StoreSetting->save($data);
                $this->ModulePermission->save($data);
                $modulePermission = $this->ModulePermission->findByStoreId($storeId);
            }
        }
        $this->set('modulePermission', $modulePermission);
    }

    public function orderAllowedCheck() {
        $orderType = '';
        $this->loadModel('StoreSetting');
        $storeId = $this->Session->read('store_id');
        $storeSetting = $this->StoreSetting->findByStoreId($storeId, array('order_allow'));
        if (!empty($storeSetting) && empty($storeSetting['StoreSetting']['order_allow'])) {
            $response['status'] = 'Error';
            $response['msg'] = 'Store is currently not taking orders.';
            return json_encode($response);
        }
        $deliveryAddessId = $this->Session->read('Order.delivery_address_id');
        if (empty($deliveryAddessId)) {
            $deliveryAddessId = $this->Session->read('ordersummary.delivery_address_id');
        }
        $orderType = $this->Session->read('Order.order_type');
        if (empty($orderType)) {
            $orderType = $this->Session->read('ordersummary.order_type');
        }
        if (!empty($deliveryAddessId) && $orderType == 3) {
            $this->loadModel('DeliveryAddress');
            $DelAddress = $this->DeliveryAddress->fetchAddress($deliveryAddessId);
            $this->Common->setZonefee($DelAddress);
            $zoneData = $this->Session->read('Zone.id');
            if (empty($zoneData)) {
                unset($_SESSION['Zone']);
                $this->Session->delete('Zone');
                $this->Session->delete("ordersummary.delivery_address_id");
                $response['status'] = 'Error';
                $response['msg'] = "Order cannot be delivered to this address, Please update address or choose another address.";
                return json_encode($response);
            }
        }
    }

}
