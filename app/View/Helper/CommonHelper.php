<?php

/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
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
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('AppHelper', 'View/Helper');
App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class CommonHelper extends Helper {

    public $helpers = array('Session');

    // get countries List as a dropdown
    function getitemdetals($itemid = null) {
        // import the country DB
        $itemdetails = array();
        App::import("Model", "Item");
        $this->Item = new Item();

        $itemName = $this->Item->find('first', array('fields' => array('id', 'name'), 'conditions' => array('Item.is_active' => 1, 'Item.is_deleted' => 0, 'Item.id' => $itemid)));

        if ($itemName) {
            return $itemName;
        } else {
            return false;
        }
    }

    /* ------------------------------------------------
      Function name:getItemSizes()
      Description:To find list of the Sizes
      created:3/8/2015
      ----------------------------------------------------- */

    public function getItemSize($itemId = null, $storeId = null) {
        if (empty($storeId)) {
            $storeId = $_SESSION['admin_store_id'];
        }
        App::import("Model", "Item");
        $this->Item = new Item();
        App::import("Model", "Size");
        $this->Size = new Size();
        App::import("Model", "Category");
        $this->Category = new Category();
        if ($itemId) {
            $sizeList = '';
            $category = $this->Item->getcategoryByitemID($itemId, $storeId);
            if ($category) {
                $categoryId = $category['Item']['category_id'];
                if ($this->Category->checkCategorySizeExists($categoryId, $storeId)) {
                    $sizeList = $this->Size->getCategorySizes($categoryId, $storeId);
                }
            }
            if ($sizeList) {
                return $sizeList;
            } else {
                return false;
            }
        } else {
            exit;
        }
    }

    public function getStoreDetail($storeId = null) { /////front
        // echo "hiiii";die;
        $storeId = $_SESSION['store_id'];
        App::import("Model", "Store");
        $this->Store = new Store();
        if ($storeId) {
            $sizeList = '';
            $store = $this->Store->fetchStoreDetail($storeId);
            if ($store) {
                return $store;
            }
        }
    }

    public function getStoreDet($storeId = null) { /////front
        // echo "hiiii";die;
        App::import("Model", "Store");
        $this->Store = new Store();
        if ($storeId) {
            $sizeList = '';
            $store = $this->Store->fetchStoreDetail($storeId);
            if ($store) {
                return $store;
            }
        }
    }

    // For permissions of navigation Panel

    function checkPermissionByTabName($tabname = null, $userId = null, $roleId = null) {
        //pr($_SESSION);die;
        if (!empty($tabname)) {
            App::import('Model', 'Tab');
            $this->Tab = new Tab();
            $tabid = $this->Tab->getTabData($tabname, null, null, $roleId);
            App::import('Model', 'Permission');
            $this->Permission = new Permission();
            $permissiondata = $this->Permission->getPermissionData($userId, $tabid);
            if (!empty($permissiondata)) {
                $permission = 1;
            } else {
                $permission = 0;
            }
            return $permission;
        }
    }

    function isDispalyMenuAllow() {
        $storeID = $_SESSION['admin_store_id'];
        App::import('Model', 'StoreSetting');
        $this->StoreSetting = new StoreSetting();
        $result = $this->StoreSetting->find('first', array('fields' => array('id', 'pos_menu_allow'), 'conditions' => array('StoreSetting.store_id' => $storeID)));
        if (isset($result['StoreSetting'])) {
            return !$result['StoreSetting']['pos_menu_allow'];
        }
        return true;
    }

    function getKitchendisplayType() {
        $storeID = $_SESSION['admin_store_id'];
        if (!empty($storeID)) {
            App::import('Model', 'Store');
            $this->Store = new Store();
            $kitchenType = $this->Store->getkitchentype($storeID);
            return $kitchenType;
        }
    }

    //Get Todays Order
    function getTodaysOrder($storeId = null) {
        if (!$storeId) {
            $storeId = $_SESSION['admin_store_id'];
        }
        if ($storeId) {
            App::import('Model', 'Order');
            $this->Order = new Order();
            $current_date = date("Y-m-d", (strtotime($this->storeTimeZone('', date('Y-m-d H:i:s')))));
            $totalorders = $this->Order->getTodaysOrder($storeId, $current_date);
            return $totalorders;
        }
    }

    //Get pre-Order
    function getPreOrder($storeId = null) {
        if (!$storeId) {
            $storeId = $_SESSION['admin_store_id'];
        }
        if ($storeId) {
            App::import('Model', 'Order');
            $this->Order = new Order();
            $current_date = date("Y-m-d", (strtotime($this->storeTimeZone('', date('Y-m-d H:i:s')))));
            $totalPreOrders = $this->Order->getPreOrder($storeId, $current_date);
            return $totalPreOrders;
        }
    }

    //get todays Pending Order
    function getTodaysPendingOrder($storeId = null) {
        if (!$storeId) {
            $storeId = $_SESSION['admin_store_id'];
        }
        if ($storeId) {
            App::import('Model', 'Order');
            $this->Order = new Order();
            $current_date = date("Y-m-d", (strtotime($this->storeTimeZone('', date('Y-m-d H:i:s')))));
            $totalorders = $this->Order->getTodaysPendingOrder($storeId, $current_date);
            return $totalorders;
        }
    }

    //get todays Bookings Request
    function getTodaysBookingRequest($storeId = null) {
        if (!$storeId) {
            $storeId = $_SESSION['admin_store_id'];
        }
        if ($storeId) {
            App::import('Model', 'Booking');
            $this->Booking = new Booking();
            $current_date = date("Y-m-d", (strtotime($this->storeTimeZone('', date('Y-m-d H:i:s')))));
            $totalorders = $this->Booking->getTodaysBookingRequest($storeId, $current_date);
            return $totalorders;
        }
    }

    //get todays pending Bookings Request
    function getTodaysPendingBookings($storeId = null) {
        if (!$storeId) {
            $storeId = $_SESSION['admin_store_id'];
        }
        if ($storeId) {
            App::import('Model', 'Booking');
            $this->Booking = new Booking();
            $current_date = date("Y-m-d", (strtotime($this->storeTimeZone('', date('Y-m-d H:i:s')))));
            $pendingbookings = $this->Booking->getTodaysPendingBookings($storeId, $current_date);
            return $pendingbookings;
        }
    }

    //get HQ stores

    function getHQStores($merchantId = null) {
        if ($merchantId) {
            App::import('Model', 'Store');
            $this->Store = new Store();
            $merchantList = $this->Store->getMerchantStores($merchantId);
            return $merchantList;
        }
    }

    //get HQ stores

    function getStores($merchantId = null) {
        if ($merchantId) {
            App::import('Model', 'Store');
            $this->Store = new Store();
            $merchantList = $this->Store->getMerchantStoresDet($merchantId);
            return $merchantList;
        }
    }

    //get HQ stores Transaction list

    function getHQtransaction($merchantId = null) {
        if ($merchantId) {
            App::import('Model', 'Store');
            $this->Store = new Store();
            $merchantList = $this->Store->getHQStorestransList($merchantId);
            return $merchantList;
        }
    }

    // Get the list of HQ stores

    function getTotalHQStores($merchantId = null) {
        if (!$merchantId) {
            $merchantId = $_SESSION['merchantId'];
        }
        if ($merchantId) {
            App::import('Model', 'Store');
            $this->Store = new Store();
            $merchantList = $this->Store->getTotalMerchantStores($merchantId);
            return $merchantList;
        }
    }

    // Get the list of HQ Customers

    function getTotalHQCustomers($merchantId = null) {
        if (!$merchantId) {
            $merchantId = $_SESSION['merchantId'];
        }
        if ($merchantId) {
            App::import('Model', 'User');
            $this->User = new User();
            $customerList = $this->User->getTotalMerchantCustomer($merchantId);
            return $customerList;
        }
    }

    // Get the list of HQ Online Collections

    function getTotalHQOnlineCollection($merchantId = null) {
        if (!$merchantId) {
            $merchantId = $_SESSION['merchantId'];
        }
        if ($merchantId) {
            App::import('Model', 'OrderPayment');
            $this->OrderPayment = new OrderPayment();
            $collectionList = $this->OrderPayment->MerchantOnlineCollection($merchantId);
            return $collectionList;
        }
    }

    // Get the list of Store total Customers

    function getTotalStoreCustomers($storeId = null) {
        $merchantId = $_SESSION['merchantId'];

        if ($storeId) {
            App::import('Model', 'User');
            $this->User = new User();
            $customerList = $this->User->getTotalStoreCustomer($storeId, $merchantId);
            return $customerList;
        }
    }

    // Get the list of Store Online Collections

    function getTotalStoreOnlineCollection($storeId = null) {
        $merchantId = $_SESSION['merchantId'];

        if ($storeId) {
            App::import('Model', 'OrderPayment');
            $this->OrderPayment = new OrderPayment();
            $collectionList = $this->OrderPayment->StoreOnlineCollection($storeId, $merchantId);
            return $collectionList;
        }
    }

    /* ------------------------------------------------
      Function name:getStore()
      Description:To find total store's
      created:14/09/2015
      ----------------------------------------------------- */

    function getStore() {
        App::import('Model', 'Store');
        $this->Store = new Store();
        $storeCount = $this->Store->getTotalStores();
        return $storeCount;
    }

    /* ------------------------------------------------
      Function name:getCustomer()
      Description:To find total customers
      created:14/09/2015
      ----------------------------------------------------- */

    function getCustomer() {
        App::import('Model', 'User');
        $this->User = new User();
        $customerCount = $this->User->getTotalCustomer();
        return $customerCount;
    }

    /* ------------------------------------------------
      Function name:getMerchant()
      Description:To find total merchant's
      created:14/09/2015
      ----------------------------------------------------- */

    function getMerchant() {
        App::import('Model', 'Merchant');
        $this->Merchant = new Merchant();
        $merchantCount = $this->Merchant->getTotalMerchant();
        return $merchantCount;
    }

    /* ------------------------------------------------
      Function name:getStoreList()
      Description:To find store list
      created:14/09/2015
      ----------------------------------------------------- */

    function getStoreList() {
        App::import('Model', 'Store');
        $this->Store = new Store();
        $storeList = $this->Store->getStores();
        return $storeList;
    }

    /* ------------------------------------------------
      Function name:getListMerchant()
      Description:To find total merchant list
      created:15/09/2015
      ----------------------------------------------------- */

    function getListMerchant() {
        App::import('Model', 'Merchant');
        $this->Merchant = new Merchant();
        $merchantCount = $this->Merchant->getListTotalMerchant();
        return $merchantCount;
    }

    /* ------------------------------------------------
      Function name:getCategoryName()
      Description:To find Category name
      created:24/11/2015
      ----------------------------------------------------- */

    function getCategoryName($categoryID = null) {
        App::import('Model', 'Category');
        $this->Category = new Category();
        $categoryName = $this->Category->getCategoryName($categoryID);
        return $categoryName;
    }

    /* ------------------------------------------------
      Function name:getCategoryName()
      Description:To find Category name
      created:24/11/2015
      ----------------------------------------------------- */

    function getCategoryID($itemID = null) {
        App::import('Model', 'Item');
        $this->Item = new Item();
        $categoryID = $this->Item->find('first', array('conditions' => array('Item.id' => $itemID), 'fields' => array('Item.id', 'Item.category_id')));
        return $categoryID['Item']['category_id'];
    }

    /* ------------------------------------------------
      Function name:getItemTax()
      Description:To Item tax
      created:24/11/2015
      ----------------------------------------------------- */

    function getItemTax($ItemId = null, $sizeid = null) {
        App::import('Model', 'ItemPrice');
        $this->ItemPrice = new ItemPrice();
        $itemIfo = $this->ItemPrice->getItemTax($ItemId, $sizeid);
        return $itemIfo;
    }

    /* ------------------------------------------------
      Function name:getItemTax()
      Description:To Item tax
      created:24/11/2015
      ----------------------------------------------------- */

    function getStoreTaxByID($taxId = null) {
        App::import('Model', 'StoreTax');
        $this->StoreTax = new StoreTax();
        $taxInfo = $this->StoreTax->storeTaxesBytaxId($taxId);
        return $taxInfo;
    }

    /* ------------------------------------------------
      Function name:getSubpreferenceDetail()
      Description:To get sub preference details
      created:01/12/2015
      ----------------------------------------------------- */

    function getSubpreferenceDetail($subpreID = null) {
        App::import('Model', 'SubPreference');
        $this->SubPreference = new SubPreference();
        $SubPreference = $this->SubPreference->getSubPreferenceDetail($subpreID);
        return $SubPreference;
    }

    function storeTimezone($timezoneId = null, $dateToconvert = null, $type = null) {

        App::import('Model', 'TimeZone');
        App::import('Model', 'Store');
        $this->TimeZone = new TimeZone();
        $this->Store = new Store();
        $storeId = $this->Session->read('admin_store_id');
        $timezone = date_default_timezone_get(); //get server time zone

        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.dst', 'Store.time_zone_id', 'Store.time_formate')));

        if ($this->Session->check('admin_time_zone_id')) {

            $storeadmintimezone = $this->TimeZone->find('first', array('conditions' => array("TimeZone.id" => $this->Session->read('admin_time_zone_id')), 'fields' => array('TimeZone.difference_in_seconds', 'TimeZone.code'), 'recursive' => -1));

            $servertime = strtotime(date("d-m-Y h:i:s A"));
            date_default_timezone_set($storeadmintimezone['TimeZone']['code']);
            $requiredTime = strtotime(date("d-m-Y h:i:s A"));
            if ($servertime != $requiredTime) {
                date_default_timezone_set("GMT");
                $gmtTime = strtotime(date("d-m-Y h:i:s A"));
                $diff1 = $gmtTime - $servertime;
                $diff2 = $requiredTime - $gmtTime;
                date_default_timezone_set($timezone);
                $dateToconvert = str_replace('/', '-', $dateToconvert);
                $dateToconvert = date_format(new DateTime($dateToconvert), "Y-m-d h:i:s A");
                $add = ($diff1) + ($diff2);
                $var = strtotime($dateToconvert) + $add;
            } else {
                $var = strtotime($dateToconvert);
            }

            if (!empty($type)) {
                $dateToconvert = date("H:i:s", $var);
            } else {
                $dateToconvert = date("Y-m-d H:i:s", $var);
            }

            if ($storeInfo['Store']['time_formate'] == 1 && $type != '') {
                $dateToconvert = date("h:i:s a", $var);
            } else if ($storeInfo['Store']['time_formate'] == 0 && $type != '') {
                $dateToconvert = date("H:i:s", $var);
            } else if ($storeInfo['Store']['time_formate'] == 1) {
                $dateToconvert = date("Y-m-d h:i:s a", $var);
            }
        }
        return $dateToconvert;
    }

    function storeTimeZoneUser($timezoneId = null, $dateToconvert = null) {
        App::import('Model', 'TimeZone');
        App::import('Model', 'Store');
        $this->TimeZone = new TimeZone();
        $this->Store = new Store();
        $storeId = $this->Session->read('store_id');
        $timezone = date_default_timezone_get(); //get server time zone
        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.dst', 'Store.time_zone_id', 'Store.time_formate')));
        if ($this->Session->check('front_time_zone_id')) {
            $storefronttimezone = $this->TimeZone->find('first', array('conditions' => array("TimeZone.id" => $this->Session->read('front_time_zone_id')), 'fields' => array('TimeZone.difference_in_seconds', 'TimeZone.code'), 'recursive' => -1));

            $servertime = strtotime(date("d-m-Y h:i:s A"));
            date_default_timezone_set($storefronttimezone['TimeZone']['code']);
            $requiredTime = strtotime(date("d-m-Y h:i:s A"));
            if ($servertime != $requiredTime) {
                date_default_timezone_set("GMT");
                $gmtTime = strtotime(date("d-m-Y h:i:s A"));
                $diff1 = $gmtTime - $servertime;
                $diff2 = $requiredTime - $gmtTime;
                date_default_timezone_set($timezone);
                $dateToconvert = str_replace('/', '-', $dateToconvert);
                $dateToconvert = date_format(new DateTime($dateToconvert), "Y-m-d h:i:s A");
                $add = ($diff1) + ($diff2);
                $var = strtotime($dateToconvert) + $add;
            } else {
                $var = strtotime($dateToconvert);
            }

            if ($storeInfo['Store']['time_formate'] == 1) {
                $dateToconvert = date("Y-m-d h:i:s a", $var);
            } else {
                $dateToconvert = date("Y-m-d H:i:s", $var);
            }
        }
        return $dateToconvert;
    }

    function storeTimeFormate($timeToconvert = null, $withDate = null) {
        if (!$this->Session->check('admin_store_id')) {
            return $timeToconvert;
        }
        App::import('Model', 'Store');
        $this->Store = new Store();
        $storeId = $this->Session->read('admin_store_id');
        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.time_formate')));
        if ($withDate) {
            if ($storeInfo['Store']['time_formate'] == 1) {
                $timeToconvert = date("m-d-Y h:i a", (strtotime($timeToconvert)));
            } else {
                $timeToconvert = date("m-d-Y H:i", (strtotime($timeToconvert)));
            }
        } else {
            if ($storeInfo['Store']['time_formate'] == 1) {
                $timeToconvert = date("h:i a", (strtotime($timeToconvert)));
            } else {
                $timeToconvert = date("H:i", (strtotime($timeToconvert)));
            }
        }
        return $timeToconvert;
    }

    function storeTimeFormateUser($timeToconvert = null, $withDate = null) {
        if (!$this->Session->check('store_id')) {
            return $timeToconvert;
        }
        App::import('Model', 'Store');
        $this->Store = new Store();
        $storeId = $this->Session->read('store_id');
        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.time_formate')));
//        if ($withDate) {
//            if ($storeInfo['Store']['time_formate'] == 1) {
//                $timeToconvert = date("m-d-Y h:i a", (strtotime($timeToconvert)));
//            } else {
//                $timeToconvert = date("m-d-Y H:i", (strtotime($timeToconvert)));
//            }
//        } else {
//            if ($storeInfo['Store']['time_formate'] == 1) {
//                $timeToconvert = date("h:i a", (strtotime($timeToconvert)));
//            } else {
//                $timeToconvert = date("H:i", (strtotime($timeToconvert)));
//            }
//        }
        if ($withDate) {
            if ($storeInfo['Store']['time_formate'] == 1) {
                $timeToconvert = date("n/j/Y g:i a", (strtotime($timeToconvert)));
            } else {
                $timeToconvert = date("n/j/Y G:i", (strtotime($timeToconvert)));
            }
        } else {
            if ($storeInfo['Store']['time_formate'] == 1) {
                $timeToconvert = date("g:i a", (strtotime($timeToconvert)));
            } else {
                $timeToconvert = date("G:i", (strtotime($timeToconvert)));
            }
        }

        return $timeToconvert;
    }

    /* ------------------------------------------------
      Function name:ajaxOrderOverview()
      Description: write order item listing into session
      created Date:11/02/2016
      created By:Praveen Soni
      ----------------------------------------------------- */

    public function getTimeIntervalPrice($itemId = null, $sizeId = null) {
        $this->layout = false;
        $this->autoRender = false;

        App::import("Model", "Store");
        $this->Store = new Store();
        App::import("Model", "Interval");
        $this->Interval = new Interval();
        App::import("Model", "IntervalPrice");
        $this->IntervalPrice = new IntervalPrice();
        App::import("Model", "IntervalDay");
        $this->IntervalDay = new IntervalDay();
        //$this->loadModel('Store');
        //$this->loadModel('Interval');
        //$this->loadModel('IntervalPrice');
        //$this->loadModel('IntervalDay');

        $storeId = $this->Session->read('store_id');
        $currentDateTime = date("Y-m-d H:i:s", (strtotime($this->Common->storeTimeZoneUser('', date("Y-m-d H:i:s")))));
        $currentTime = date("H:i:s", strtotime($currentDateTime));
        $currentDay = date("N", strtotime($currentDateTime));
        $this->Interval->unbindModel(
                array('hasMany' => array('IntervalDay'))
        );

        $currentDay = 2;
        $this->Interval->bindModel(
                array(
                    'hasMany' => array(
                        'IntervalPrice' => array(
                            'className' => 'IntervalPrice',
                            'foreignKey' => 'interval_id',
                            'conditions' => array('IntervalPrice.price >' => 0, 'IntervalPrice.item_id' => $itemId, 'IntervalPrice.store_id' => $storeId, 'IntervalPrice.is_active' => 1),
                            'type' => 'INNER',
                        ),
                        'IntervalDay' => array(
                            'className' => 'IntervalDay',
                            'foreignKey' => 'interval_id',
                            'conditions' => array('IntervalDay.week_day_id' => $currentDay, 'IntervalDay.day_status' => 1, 'IntervalDay.store_id' => $storeId),
                            'type' => 'INNER'
                        )
                    )
                )
        );

        $currentTime = '10:20:00';
        $intervalDetail = $this->Interval->find('all', array('conditions' => array('Interval.start <=' => $currentTime, 'Interval.end >=' => $currentTime, 'Interval.store_id' => $storeId, 'Interval.is_active' => 1, 'Interval.is_deleted' => 0)));
        $price = 0.00;
        foreach ($intervalDetail as $interval) {
            if ((isset($interval['IntervalDay']) && !empty($interval['IntervalDay'])) && (isset($interval['IntervalPrice']) && !empty($interval['IntervalPrice']))) {
                foreach ($interval['IntervalPrice'] as $intervalPrice) {
                    $price = $price + $intervalPrice['price'];
                }
            }
        }
        return $price;
    }

    function getNextDayTimeRange($currentdate = null, $today = 0, $orderType = 1) {
        App::import('Model', 'Store');
        $this->Store = new Store();

        $decrypt_storeId = $this->Session->read('store_id');
        $decrypt_merchantId = $this->Session->read('merchant_id');

        $date = new DateTime($currentdate);
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
        $store_data = $this->Store->fetchStoreDetail($decrypt_storeId, $decrypt_merchantId);
        if (empty($store_data['StoreAvailability'])) {
            $nextDate = date('Y-m-d', strtotime('+1 day', strtotime($currentdate)));
            $today = 0;
            $finaldata = $this->getNextDayTimeRange($nextDate, $today, $orderType);
            $finaldata['currentdate'] = $nextDate;
            return $finaldata;
        } else {

            $current_array = array();
            $time_break = array();
            $storeBreak = array();
            $time_range = array();
            $finaldata = array();

            $start = $store_data['StoreAvailability'][0]['start_time'];
            $end = $store_data['StoreAvailability'][0]['end_time'];
            $start = $this->getStartTime($start, $today, $orderType, 1, $end);
            $time_ranges = $this->getStoreTime($start, $end, $orderType); // calling Common Component
            $current_array = $time_ranges;  //pr($current_array);
            if (empty($current_array) || !$start) {
                $currentdate = date('Y-m-d', strtotime('+1 day', strtotime($currentdate)));
                $today = 0;
                $finaldata = $this->getNextDayTimeRange($currentdate, $today, $orderType);
                $finaldata['currentdate'] = $currentdate;
                return $finaldata;
            }
            $finaldata['currentdate'] = $currentdate;
            if ($store_data['Store']['is_break_time'] == 1) {
                //$this->loadModel('StoreBreak');
                App::import('Model', 'StoreBreak');
                $this->StoreBreak = new StoreBreak();
                $store_break = $this->StoreBreak->fetchStoreBreak($store_data['Store']['id'], $store_data['StoreAvailability'][0]['id']);
                $time_break1 = array();
                $time_break2 = array();
                if ($store_data['Store']['is_break1'] == 1) {
                    $break_start_time = $store_break['StoreBreak']['break1_start_time'];
                    $break_end_time = $store_break['StoreBreak']['break1_end_time'];
                    $storeBreak[0]['start'] = $store_break['StoreBreak']['break1_start_time'];
                    $storeBreak[0]['end'] = $store_break['StoreBreak']['break1_end_time'];
                    $time_break1 = $this->getStoreTime($break_start_time, $break_end_time);
                }
                if ($store_data['Store']['is_break2'] == 1) {
                    $break_start_time = $store_break['StoreBreak']['break2_start_time'];
                    $break_end_time = $store_break['StoreBreak']['break2_end_time'];
                    $storeBreak[1]['start'] = $store_break['StoreBreak']['break2_start_time'];
                    $storeBreak[1]['end'] = $store_break['StoreBreak']['break2_end_time'];
                    $time_break2 = $this->getStoreTime($break_start_time, $break_end_time);
                }
                $time_break = array_unique(array_merge($time_break1, $time_break2), SORT_REGULAR);
            }
            $time_range = array_diff($current_array, $time_break);
            $current_date = date("Y-m-d H:i:s", (strtotime($this->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
            $CurrentDateTocheck = explode(" ", $current_date);
            $avalibilty_status = $this->checkStoreAvalibility($decrypt_storeId, $CurrentDateTocheck[0], $CurrentDateTocheck[1]);
            if ($avalibilty_status != 1) {
                $setPre = 1;
            } else {
                $setPre = 0;
            }
            $finaldata['setPre'] = $setPre;
            $finaldata['time_break'] = $time_break;
            $finaldata['store_data'] = $store_data;
            $finaldata['storeBreak'] = $storeBreak;
            $finaldata['time_range'] = $time_range;
            return $finaldata;
        }
    }

    function getIntervalName($intervalid = null) {
        App::import('Model', 'Interval');
        $this->Interval = new Interval();
        $intervalName = $this->Interval->getIntervalName($intervalid);
        return $intervalName;
    }

    function getFirstDeliveryAddress($userId = null) {
        App::import('Model', 'DeliveryAddress');
        $this->DeliveryAddress = new DeliveryAddress();
        $deliveryAddress = $this->DeliveryAddress->fetchfirstAddress($userId);
        return $deliveryAddress;
    }

    /**
     * @name getAllCountryCode
     * @description return list of country code list
     * @return type
     */
    function getAllCountryCode() {
        $countryCodeList = array();
        App::import('Model', 'CountryCode');
        $this->CountryCode = new CountryCode();
        $countryCodeList = $this->CountryCode->fetchAllCountryCode();
        return $countryCodeList;
    }

    public function getCurrentPickUpStoreTime() {
        $current_date = date("Y-m-d", (strtotime($this->storeTimeZoneUser('', date('Y-m-d H:i:s')))));

        $finaldata = array();
        $today = 1;
        $orderType = 2;
        $finaldata = $this->getNextDayTimeRange($current_date, $today, $orderType);
        $pickcurrent_date = $finaldata['currentdate'];
        $explodeVal = explode("-", $pickcurrent_date);
        $pickcurrentDateVar = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];
        $pickupadvanceDay = $finaldata['store_data']['Store']['pickcalendar_limit'] - 1;
        $datetoConvert = explode('-', $pickcurrentDateVar);
        $datetoConvert = $datetoConvert[2] . '-' . $datetoConvert[0] . '-' . $datetoConvert[1];
        $pickupmaxdate = date('m-d-Y', strtotime($datetoConvert . ' +' . $pickupadvanceDay . ' day'));

        $finaldata['pickcurrentDateVar'] = $pickcurrentDateVar;
        $finaldata['pickupmaxdate'] = $pickupmaxdate;
        return $finaldata;
    }

    public function getCurrentDeliveryStoreTime() {
        $current_date = date("Y-m-d", (strtotime($this->storeTimeZoneUser('', date('Y-m-d H:i:s')))));

        $finaldata = array();
        $today = 1;
        $orderType = 3;
        $finaldata = $this->getNextDayTimeRange($current_date, $today, $orderType);

        $delcurrent_date = $finaldata['currentdate'];
        $explodeVal = explode("-", $delcurrent_date);
        $delcurrentDateVar = $explodeVal[1] . "-" . $explodeVal[2] . "-" . $explodeVal[0];
        $deliveryadvanceDay = $finaldata['store_data']['Store']['deliverycalendar_limit'] - 1;
        $datetoConvert = explode('-', $delcurrentDateVar);
        $datetoConvert = $datetoConvert[2] . '-' . $datetoConvert[0] . '-' . $datetoConvert[1];
        $deliverymaxdate = date('m-d-Y', strtotime($datetoConvert . ' +' . $deliveryadvanceDay . ' day'));

        $finaldata['delcurrentDateVar'] = $delcurrentDateVar;
        $finaldata['deliverymaxdate'] = $deliverymaxdate;
        return $finaldata;
    }

    public function logindeliveryaddress($storeId = null, $merchantID = null) {
        App::import('Model', 'DeliveryAddress');
        $this->DeliveryAddress = new DeliveryAddress();
        $userId = $this->Session->read('Auth.User.id');
        $roleId = $this->Session->read('Auth.User.role_id');
        $storeId = $this->Session->read('store_id');
        $merchantID = $this->Session->read('merchant_id');
        //$userId = AuthComponent::User('id'); // Customer Id
        //$roleId = AuthComponent::User('role_id');
        $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
        $checkaddress = $this->DeliveryAddress->checkAllAddress($userId, $roleId, $storeId, $merchantID); // It will call the function in the model to check the address either exist or not
        if (!$checkaddress) {
            $checkaddress = array();
        }
        return $checkaddress;
    }

    public function getOrderType() {
        echo $_SESSION['Order']['order_type'];
        die;
    }

    public function getStoreDetails() {
        App::import('Model', 'Store');
        $this->Store = new Store();
        $storeId = $this->Session->read('store_id');
        $merchantID = $this->Session->read('merchant_id');
        $store_data = $this->Store->fetchStoreDetail($storeId, $merchantID);
        return $store_data;
    }

    public function checkPreOrder() {
        App::import('Model', 'Store');
        $this->Store = new Store();
        $storeId = $this->Session->read('store_id');
        $merchantID = $this->Session->read('merchant_id');
        $store_data = $this->Store->checkPreorder($storeId, $merchantID);
        return $store_data;
    }

    public function popupallowed() {
        App::import('Model', 'Store');
        $this->Store = new Store();
        $storeId = $this->Session->read('store_id');
        $merchantID = $this->Session->read('merchant_id');
        $popupstatus = $this->Store->checkPopup($storeId, $merchantID);
        return $popupstatus;
    }

    public function getaddonSize($sizeid = null) {
        App::import('Model', 'AddonSize');
        $this->AddonSize = new AddonSize();
        $storeId = $this->Session->read('store_id');
        $Sizedetail = $this->AddonSize->getAddonSizeDetail($sizeid, $storeId);
        return $Sizedetail;
    }

    public function checkTransactionAllowPermission($merchantID) {
        if (!empty($merchantID)) {
            App::import('Model', 'Merchant');
            $this->Merchant = new Merchant();
            $transactionPermission = $this->Merchant->getTransactionAllowData($merchantID);
            return $transactionPermission;
        }
    }

    /* ------------------------------------------------
      Function name:getItemSizes()
      Description:To find list of the Sizes
      created:3/8/2015
      ----------------------------------------------------- */

    public function getItemSizeHqEditOffer($itemId = null, $storeId = null) {
        App::import("Model", "ItemPrice");
        $this->ItemPrice = new ItemPrice();
        App::import("Model", "Size");
        $this->Size = new Size();
        $sizeList = $this->ItemPrice->find('list', array('fields' => array('id'), 'conditions' => array('item_id' => $itemId, 'store_id' => $storeId, 'is_active' => 1, 'is_deleted' => 0)));
    }

    public function gettodayDate() {
        $current_date = date("Y-m-d", (strtotime($this->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        return $current_date;
    }

    public function getOrderDeliveryAddressUsingId() {
        App::import('Model', 'DeliveryAddress');
        $this->DeliveryAddress = new DeliveryAddress();
        $delivery_address_id = $this->Session->read('ordersummary.delivery_address_id');
        $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode')), false);
        $checkaddress = $this->DeliveryAddress->fetchAddress($delivery_address_id); // It will call the function in the model to check the address either exist or not
        if (!$checkaddress) {
            $checkaddress = array();
        }
        return $checkaddress;
    }

    public function getFeaturedItemStatus($featuredID, $itemID) {
        App::import('Model', 'FeaturedItem');
        $this->FeaturedItem = new FeaturedItem();
        $data = $this->FeaturedItem->find('first', array('conditions' => array('store_featured_section_id' => $featuredID, 'item_id' => $itemID), 'fields' => array('is_active')));
        return $data;
    }

    public function taxCalculation($finalCart = null) {
        //pr($finalCart);
        if (!empty($finalCart)) {
            $gross_amount = 0.00;
            foreach ($finalCart as $key => $itemData) {
                if (!empty($itemData['Item']['taxvalue'])) {
                    $gross_amount = $gross_amount + $itemData['Item']['final_price'];
                }
            }
            $storeID = $_SESSION['store_id'];
            App::import('Model', 'StoreSetting');
            $this->StoreSetting = new StoreSetting();
            $result = $this->StoreSetting->find('first', array('fields' => array('id', 'tax_on_item_price'), 'conditions' => array('StoreSetting.store_id' => $storeID)));

            $totalServiceFee = 0;

            foreach ($finalCart as $key => $itemData) {
                $finalCart[$key]['taxCalculated'] = 0.00;
                if (empty($itemData['Item']['taxvalue'])) {
                    $finalCart[$key]['propotion'] = 0.00;

                    // Calculating Service Fee
                    $serviceFee = $this->serviceFeeByItems($itemData['Item']['final_price'], $gross_amount);
                    $finalCart[$key]['FinalItemPrice'] = $itemData['Item']['final_price'] + $serviceFee;
                    $finalCart[$key]['ServiceAmount'] = $serviceFee;
                    $finalCart[$key]['DiscountAmount'] = 0.00;
                    $finalCart[$key]['taxCalculated'] = 0.00;
                    $finalCart[$key]['finalAmount'] = $finalCart[$key]['FinalItemPrice'];
                    continue;
                }
                if (!empty($_SESSION['Coupon'])) {
                    if ($result['StoreSetting']['tax_on_item_price']) {
                        $finalCart[$key]['propotion'] = 0.00;

                        // Calculating Service Fee
                        $serviceFee = $this->serviceFeeByItems($itemData['Item']['final_price'], $gross_amount);
                        $finalCart[$key]['FinalItemPrice'] = $itemData['Item']['final_price'] + $serviceFee;
                        $finalCart[$key]['ServiceAmount'] = $serviceFee;
                        //$finalCart[$key]['FinalItemPrice'] = $itemData['Item']['final_price'];

                        $finalCart[$key]['DiscountAmount'] = 0.00;
                        if (!empty($itemData['Item']['taxvalue'])) {
                            $finalCart[$key]['taxCalculated'] = (($itemData['Item']['taxvalue'] / 100) * $finalCart[$key]['FinalItemPrice']);
                        }
                        $finalCart[$key]['finalAmount'] = $finalCart[$key]['taxCalculated'] + $finalCart[$key]['FinalItemPrice'];
                    } else {
                        if ($_SESSION['Coupon']['Coupon']['discount_type'] == 1) { // Price     
                            $finalCart[$key]['propotion'] = number_format((($itemData['Item']['final_price'] * 100) / $gross_amount), 2);
                            $discount_amount = $_SESSION['Coupon']['Coupon']['discount'];
                            $finalItemDiscount = round((($finalCart[$key]['propotion'] * $discount_amount) / 100), 2);
                            // Calculating Service Fee
                            $serviceFee = $this->serviceFeeByItems($itemData['Item']['final_price'], $gross_amount);
                            $finalCart[$key]['ServiceAmount'] = $serviceFee;
                            $finalCart[$key]['FinalItemPrice'] = $itemData['Item']['final_price'] + $serviceFee - $finalItemDiscount;

                            $finalCart[$key]['DiscountAmount'] = $finalItemDiscount;
                            if (!empty($itemData['Item']['taxvalue'])) {
                                $finalCart[$key]['taxCalculated'] = (($itemData['Item']['taxvalue'] / 100) * $finalCart[$key]['FinalItemPrice']);
                            }
                            $finalCart[$key]['finalAmount'] = $finalCart[$key]['taxCalculated'] + $finalCart[$key]['FinalItemPrice'];
                        } else { // Percentage
                            $finalCart[$key]['propotion'] = number_format((($itemData['Item']['final_price'] * 100) / $gross_amount), 2);
                            $finalItemDiscount = round((($_SESSION['Coupon']['Coupon']['discount'] * $itemData['Item']['final_price']) / 100), 2);

                            // Calculating Service Fee
                            $serviceFee = $this->serviceFeeByItems($itemData['Item']['final_price'], $gross_amount);
                            $finalCart[$key]['ServiceAmount'] = $serviceFee;
                            $finalCart[$key]['FinalItemPrice'] = $itemData['Item']['final_price'] + $serviceFee - $finalItemDiscount;

                            $finalCart[$key]['DiscountAmount'] = $finalItemDiscount;

                            if (!empty($itemData['Item']['taxvalue'])) {
                                $finalCart[$key]['taxCalculated'] = (($itemData['Item']['taxvalue'] / 100) * $finalCart[$key]['FinalItemPrice']);
                            }

                            $finalCart[$key]['finalAmount'] = $finalCart[$key]['taxCalculated'] + $finalCart[$key]['FinalItemPrice'];
                        }
                    }
                } else {
                    $finalCart[$key]['propotion'] = 0.00;

                    // Calculating Service Fee
                    $serviceFee = $this->serviceFeeByItems($itemData['Item']['final_price'], $gross_amount);
                    $finalCart[$key]['ServiceAmount'] = $serviceFee;
                    $finalCart[$key]['FinalItemPrice'] = $itemData['Item']['final_price'] + $serviceFee;
                    $finalCart[$key]['DiscountAmount'] = 0.00;
                    if (!empty($itemData['Item']['taxvalue'])) {
                        $finalCart[$key]['taxCalculated'] = (($itemData['Item']['taxvalue'] / 100) * $finalCart[$key]['FinalItemPrice']);
                    }
                    $finalCart[$key]['finalAmount'] = $finalCart[$key]['taxCalculated'] + $finalCart[$key]['FinalItemPrice'];
                }
                //$finalCart[$key]['Item']['revised_taxamount']=$finalCart[$key]['taxCalculated'];
                //$finalCart[$key]['Item']['revised_price']=$finalCart[$key]['FinalItemPrice'];
                $finalCart[$key]['Item']['taxamount'] = round($finalCart[$key]['taxCalculated'], 2);
                $finalCart[$key]['Item']['final_revised_price'] = $finalCart[$key]['FinalItemPrice'];
                $finalCart[$key]['Item']['serviceamount'] = round($finalCart[$key]['ServiceAmount'], 2);
                $totalServiceFee += round($finalCart[$key]['ServiceAmount'], 2);
            }
            $_SESSION['final_service_fee'] = $totalServiceFee;
            $_SESSION['cart'] = $finalCart;
            return $finalCart;

            //die;
        }
    }

    function addOrdinalNumberSuffix($num) {
        if (!in_array(($num % 100), array(11, 12, 13))) {
            switch ($num % 10) {
                // Handle 1st, 2nd, 3rd
                case 1: return $num . 'st';
                case 2: return $num . 'nd';
                case 3: return $num . 'rd';
            }
        }
        return $num . 'th';
    }

    function getCategory($itemId) {
        App::import("Model", "Item");
        $this->Item = new Item();
        if ($itemId) {
            $storeId = $_SESSION['admin_store_id'];
            $category = $this->Item->getcategoryByitemID($itemId, $storeId);
            return $category;
        }
    }

    function formatDate($start_date = null, $format = null) {
        if (!empty($start_date)) {
            if (empty($format)) {
                $format = 'n/j/Y';
            }
            $date = new DateTime($start_date);
            $start_date = $date->format($format);
            return $start_date;
        }
    }

    /*     * ***********************
     * Function name:promoList()
      Description:promo list
      created:05/09/2017
     *
     * ********************* */

    public function promoList($storeId = null) {
        $offer = array();
        //$merchantId = (isset($_SESSION['merchantId']) ? $_SESSION['merchantId']);
        if (isset($storeId) && !empty($storeId)) {
            App::import("Model", "OrderOffer");
            $this->OrderOffer = new OrderOffer();
            $this->OrderOffer->bindModel(array('belongsTo' => array('Offer')));
            $this->OrderOffer->bindModel(array('belongsTo' => array('Order')));
            $offer = $this->OrderOffer->find('all', array('fields' => array('Offer.id', 'Offer.description'), 'group' => array('OrderOffer.offer_id'), 'conditions' => array('Order.store_id' => $storeId), 'order' => array('Order.created' => 'DESC')));
        }
        return $offer;
    }

    /*     * ***********************
     * Function name:couponList()
      Description:coupon list
      created:05/09/2017
     *
     * ********************* */

    public function couponList($storeId = null) {
        $coupon = array();
        if (isset($storeId) && !empty($storeId)) {
            App::import("Model", "Order");
            $this->Order = new Order();
            $coupon = $this->Order->find('all', array('fields' => array('Order.coupon_code as coupon'), 'group' => array('Order.coupon_code'), 'conditions' => array('Order.store_id' => $storeId, 'Order.coupon_code != ' => ''), 'order' => array('Order.created' => 'DESC')));
        }
        return $coupon;
    }

    /*     * ***********************
     * Function name:extendedOfferList()
      Description:extended offer list
      created:05/09/2017
     *
     * ********************* */

    public function extendedOfferList($storeId = null) {
        $extended_offer = array();
        if (isset($storeId) && !empty($storeId)) {
            App::import("Model", "OrderItemFree");
            $this->OrderItemFree = new OrderItemFree();
            $this->OrderItemFree->bindModel(array('belongsTo' => array('Order', 'Item')));
            $extended_offer = $this->OrderItemFree->find('all', array('fields' => array('Item.id', 'Item.name'), 'group' => array('OrderItemFree.item_id'), 'conditions' => array('Order.store_id' => $storeId), 'order' => array('Order.created' => 'DESC')));
        }
        return $extended_offer;
    }

    /*     * ***********************
     * Function name:storeTimeFormateValue()
      Description: store time format value
      created:04/10/2017
     *
     * ********************* */

    public function storeTimeFormateValue($storeId = null) {
        $timeFormat = 0;
        if (isset($storeId) && !empty($storeId)) {
            App::import("Model", "Store");
            $this->Store = new Store();
            $storeData = $this->Store->find('first', array('fields' => array('Store.id', 'Store.time_formate'), 'conditions' => array('Store.id' => $storeId)));
            $timeFormat = $storeData['Store']['time_formate'];
        }
        return $timeFormat;
    }

    /* ------------------------------------------------
      Function name:getStoreTipFront()
      Description:Get Store Tip Data For Front
      created:24/11/2015
      ----------------------------------------------------- */

    function getStoreTipFront($storeId = null) {
        App::import('Model', 'StoreTip');
        $this->StoreTip = new StoreTip();
        $tipInfo = $this->StoreTip->storeTipFront($storeId);
        $tipData = array();
        foreach ($tipInfo as $tipKey => $tipValue) {
            $tipData[$tipValue['StoreTip']['tip_value']] = $tipValue['StoreTip']['tip_value'];
        }
        return $tipData;
    }

    //change amount in 4 digits i.e. .9 = $00.90
    function amount_format($amount = null, $symbol = null) {
        if(strstr($amount,',')) {
            $amount = str_replace(',','', $amount);
        }
        $amount = number_format($amount, 2, '.', '');
        if ($amount > 0 && $amount < 1) {
            $amount = explode('.', $amount);
            $amount = '0.' . $amount[1];
        }
        if ($amount >= 1 && $amount <= 9) {
            $amount = $amount;
        }
        if (empty($symbol)) {
            $amount = '$' . $amount;
        }
        return $amount;
    }

    function orderItemDetail($orderId = null) {
        if ($orderId) {
            App::import('Model', 'OrderItem');
            $this->OrderItem = new OrderItem();
            $orderDetail = $this->OrderItem->find('all', array('conditions' => array('OrderItem.order_id' => $orderId)));
            return $orderDetail;
        }
    }

    function usedOfferDetailCount($orderId = null) {
        if ($orderId) {
            App::import('Model', 'OrderOffer');
            $this->OrderOffer = new OrderOffer();
            $orderOfferDetail = $this->OrderOffer->find('count', array('conditions' => array('OrderOffer.order_id' => $orderId)));
            return $orderOfferDetail;
        }
    }

    function usedItemOfferDetailCount($orderId = null) {
        if ($orderId) {
            App::import('Model', 'OrderItemFree');
            $this->OrderItemFree = new OrderItemFree();
            $orderItemFreeDetail = $this->OrderItemFree->find('count', array('conditions' => array('OrderItemFree.order_id' => $orderId)));
            return $orderItemFreeDetail;
        }
    }

    function deleteSession() {
        unset($_SESSION['Order']['Item'], $_SESSION['Order']['item'], $_SESSION['Order']['subPreference'], $_SESSION['final_service_fee'], $_SESSION['taxPrice'], $_SESSION['Offer'], $_SESSION['orderOverview'], $_SESSION['cart'], $_SESSION['Cart'], $_SESSION['Coupon'], $_SESSION['reOrder'], $_SESSION['Discount']);
    }

    function serviceFeeByItems($itemPrice = null, $grossAmount = null) {
        $serviceFee = 0;
        if (isset($_SESSION['service_fee']) && ($_SESSION['service_fee'] > 0)) {
            $serviceFeeType = ($_SESSION['service_fee_type'] ? $_SESSION['service_fee_type'] : 1);
            if ($serviceFeeType != 1) {
                $serviceFee = ($_SESSION['service_fee'] / 100) * $itemPrice;
            } else {
                $proportion = number_format((($itemPrice * 100) / $grossAmount), 2);
                $serviceFee = round((($proportion * $_SESSION['service_fee']) / 100), 2);
            }
        }
        //echo $serviceFee . '<br/>';
        return $serviceFee;
    }

    function reOrgranizeCart($cartdeatils = null) {
        $catarr = array();
        $newarr = array();
        $finalarr = array();
        foreach ($cartdeatils['item'] as $key => $details) {
            if (in_array($details['category_id'], $catarr)) {
                $newarr[$details['category_id']][] = $details;
            } else {
                $catarr = $details['category_id'];
                $newarr[$details['category_id']][] = $details;
            }
        }
        foreach ($newarr as $key => $value) {
            $finalarr = array_merge($finalarr, $value);
        }
        return $finalarr;
    }

    function dineInTitleString($startmonth = null, $startyear = null, $endmonth = null, $endyear = null, $type = null) {

        if ($type == 'life_time' || $type == 'yearly') {
            $titleText = 'Reservations in ' . $startyear . ' to ' . $endyear;
        } else {
            if ($startmonth == $endmonth && $startyear == $endyear) {
                $dateObj = DateTime::createFromFormat('!m', $startmonth);
                $monthName = $dateObj->format('F');
                $titleText = 'Reservations in ' . $monthName . ' ' . $startyear;
            } else if ($startmonth == $endmonth && $startyear != $endyear) {
                $dateObj = DateTime::createFromFormat('!m', $startmonth);
                $monthName = $dateObj->format('F');
                $titleText = 'Reservations in ' . $monthName . ' ' . $startyear . ' to ' . $monthName . ' ' . $endyear;
            } else if ($startmonth != $endmonth && $startyear == $endyear) {
                $dateObjStart = DateTime::createFromFormat('!m', $startmonth);
                $monthNameStart = $dateObjStart->format('F');

                $dateObjEnd = DateTime::createFromFormat('!m', $endmonth);
                $monthNameEnd = $dateObjEnd->format('F');

                $titleText = 'Reservations in ' . $monthNameStart . ' ' . $startyear . ' to ' . $monthNameEnd . ' ' . $endyear;
            } else {
                $titleText = '';
            }
        }

        return $titleText;
    }

    function dineInPieDataArrange($dineInPieData = null) {
        $totalPieStatus = 0;
        $finalPie = $returnArray = array();

        if ($dineInPieData) {
            foreach ($dineInPieData as $pieKey => $pieData) {
                if ($pieData['Booking']['booking_status_id'] == 1 || $pieData['Booking']['booking_status_id'] == 4 || $pieData['Booking']['booking_status_id'] == 5) {
                    $finalPie[$pieKey]['status_id'] = $pieData['Booking']['booking_status_id'];
                    $finalPie[$pieKey]['status_count'] = $pieData[0]['booking_count'];
                    if ($pieData['Booking']['booking_status_id'] == 1) {
                        $finalPie[$pieKey]['status_name'] = 'Pending';
                        $finalPie[$pieKey]['color'] = '#EEE090';
                    } else if ($pieData['Booking']['booking_status_id'] == 5) {
                        $finalPie[$pieKey]['status_name'] = 'Booked';
                        $finalPie[$pieKey]['color'] = '#FDAE61';
                    } else if ($pieData['Booking']['booking_status_id'] == 4) {
                        $finalPie[$pieKey]['status_name'] = 'Canceled';
                        $finalPie[$pieKey]['color'] = '#D73027';
                    } else if ($pieData['Booking']['booking_status_id'] == 6) {
                        $finalPie[$pieKey]['status_name'] = 'No Show';
                        $finalPie[$pieKey]['color'] = '#D0D07D';
                    }
                    $totalPieStatus += $pieData[0]['booking_count'];
                }
            }
            $returnArray['data'] = $finalPie;
            $returnArray['total'] = $totalPieStatus;
        }
        return $returnArray;
    }

}
