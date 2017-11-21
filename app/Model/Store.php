<?php

App::uses('AppModel', 'Model');

class Store extends AppModel {
    /* ----------------------------------------
      Funtion name:store_info
      Desc:To find the store id and merchant id
      created:22-07-2015
     * ---------------------------------------- */

    public function store_info($storeName = null) {

        $storeResult = $this->find('first', array('fields' => array('id', 'merchant_id', 'store_name', 'store_url', 'email_id', 'service_fee', 'delivery_fee', 'minimum_order_price', 'phone', 'time_zone_id'), 'conditions' => array('Store.store_url' => $storeName, 'Store.is_deleted' => 0, 'Store.is_active' => 1)));
        if ($storeResult) {
            return $storeResult;
        } else {
            return false;
        }
    }

    /* ----------------------------------------
      Funtion name:fetchStoreDetail
      Desc:To find the store detail
      created:22-07-2015
     * ---------------------------------------- */

    public function fetchStoreDetail($storeId = null) {
        $storeResult = $this->find('first', array('conditions' => array('Store.id' => $storeId)));
        if ($storeResult) {
            return $storeResult;
        } else {
            return false;
        }
    }

    public function fetchStoreUrl($storeId = null) {

        $storeResult = $this->find('first', array('fields' => array('id', 'store_url'), 'conditions' => array('Store.id' => $storeId)));
        if ($storeResult) {
            return $storeResult;
        } else {
            return false;
        }
    }

    public function fetchStorePrinterIP($storeId = null) {

        $storeResult = $this->find('first', array('fields' => array('printer_location'), 'conditions' => array('Store.id' => $storeId)));
        if ($storeResult) {
            return $storeResult;
        } else {
            return false;
        }
    }

    /* ------------------------------------------------
      Function name:saveStoreInfo()
      Description:To Save Store Information
      created:22/7/2015
      ----------------------------------------------------- */

    public function saveStoreInfo($storeData = null) {
        if ($storeData) {
            if ($this->save($storeData)) {
                return true; //Success
            } else {
                return false; // Failure
            }
        }
    }

    /* ----------------------------------------
      Funtion name:getMerchantStores
      Desc:To find the List of Merchant
      created:24-07-2015
     * ---------------------------------------- */

    public function getMerchantStores($merchantId = null) {

        $storeResult = $this->find('list', array('fields' => array('id', 'store_name'), 'conditions' => array('Store.merchant_id' => $merchantId, 'Store.is_active' => 1, 'Store.is_deleted' => 0)));
        if ($storeResult) {
            return $storeResult;
        } else {
            return false;
        }
    }

    public function getMerchantStoresDet($merchantId = null) {

        $storeResult = $this->find('list', array('fields' => array('id', 'store_name'), 'conditions' => array('Store.merchant_id' => $merchantId, 'Store.is_active' => 1, 'Store.is_deleted' => 0)));
        if ($storeResult) {
            return $storeResult;
        } else {
            return false;
        }
    }

    /* ----------------------------------------
      Funtion name:getMerchantStores
      Desc:To find the List of Merchant
      created:24-07-2015
     * ---------------------------------------- */

    public function getHQStorestransList($merchantId = null) {

        $storeResult = $this->find('list', array('fields' => array('id', 'store_name'), 'conditions' => array('Store.merchant_id' => $merchantId, 'Store.is_deleted' => 0, 'Store.is_allow_transaction' => 1)));
        if ($storeResult) {
            return $storeResult;
        } else {
            return false;
        }
    }

    public function fetchStoreEmail($storeId = null) {

        $storeResult = $this->find('first', array('fields' => array('id', 'email_id'), 'conditions' => array('Store.id' => $storeId)));
        return $storeResult;
    }

    public function fetchStoreBreak($storeId = null) {

        $storeResult = $this->find('first', array('fields' => array('id', 'is_break_time', 'is_break1', 'is_break2'), 'conditions' => array('Store.id' => $storeId)));
        return $storeResult;
    }

    public function fetchStoreImage($storeName = null) {
        $storeResult = $this->find('first', array('fields' => array('id', 'store_name', 'store_logo', 'merchant_id', 'is_store_logo', 'background_image', 'SocialMedia.*', 'navigation', 'StoreTheme.name', 'StoreTheme.body_horizontal', 'StoreTheme.main_horizontal', 'StoreTheme.body_vertical', 'StoreTheme.main_vertical', 'StoreTheme.keyword', 'StoreTheme.layout', 'StoreFont.name', 'StoreFont.class', 'is_not_photo', 'is_booking_open', 'is_delivery', 'is_take_away', 'store_hours', 'logotype', 'deal_page', 'phone', 'address', 'city', 'state', 'zipcode', 'review_page', 'theme_color_id', 'store_theme_id', 'display_email'), 'conditions' => array('Store.store_url' => $storeName, 'Store.is_deleted' => 0)));
        return $storeResult;
    }

    public function checkStoreUniqueName($storeName = null, $merchantId = null) {
        $conditions = array('LOWER(Store.store_name)' => strtolower($storeName), 'Store.merchant_id' => $merchantId, 'Store.is_deleted' => 0);
        $item = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($item) {
            return 0;
        } else {
            return 1;
        }
    }

    /* ----------------------------------------
      Funtion name:getHQStores
      Desc:To find the List of HQ store
      created:1-09-2015
     * ---------------------------------------- */

    public function getTotalMerchantStores($merchantId = null) {
        $storecount = $this->find('count', array('fields' => array('id'), 'conditions' => array('Store.merchant_id' => $merchantId, 'Store.is_active' => 1)));
        if ($storecount) {
            return $storecount;
        } else {
            return false;
        }
    }

    /* ----------------------------------------
      Funtion name:getTotalStores
      Desc:To find the List of total store
      created:14-09-2015
     * ---------------------------------------- */

    public function getTotalStores() {
        $storecount = $this->find('count', array('fields' => array('id'), 'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1)));
        if ($storecount) {
            return $storecount;
        } else {
            return false;
        }
    }

    /* ----------------------------------------
      Funtion name:getStores
      Desc:To find the List of store
      created:14-09-2015
     * ---------------------------------------- */

    public function getStores() {

        $storeResult = $this->find('list', array('fields' => array('id', 'store_name')));
        if ($storeResult) {
            return $storeResult;
        } else {
            return false;
        }
    }

    /* ----------------------------------------
      Funtion name:checkMerchantUniqueEmail
      Desc:To check Store unique email id
      created:15-09-2015
     * ---------------------------------------- */

    public function checkStoreUniqueEmail($storeemail = null, $storeId = null) {

        $conditions = array('LOWER(Store.email_id)' => strtolower($storeemail), 'Store.is_deleted' => 0, 'Store.is_active' => 1);
        if ($storeId) {
            $conditions['Store.id !='] = $storeId;
        }
        $item = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($item) {
            return 0;
        } else {
            return 1;
        }
    }

    /* ----------------------------------------
      Funtion name:getStoreDetail
      Desc:To find the store detail with it's user
      created:16-09-2015
     * ---------------------------------------- */

    public function getStoreDetail($storeId = null) {
        $storeResult = $this->find('first', array('conditions' => array('Store.id' => $storeId)));
        if ($storeResult) {
            return $storeResult;
        } else {
            return false;
        }
    }

    public function checkSuperStoreUniqueName($storeName = null, $merchantId = null, $storeId = null) {

        $conditions = array('LOWER(Store.store_name)' => strtolower($storeName), 'Store.is_deleted' => 0, 'Store.merchant_id' => $merchantId, 'Store.is_active' => 1);
        if ($storeId) {
            $conditions['Store.id !='] = $storeId;
        }
        $item = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($item) {
            return 0;
        } else {
            return 1;
        }
    }

    public function checkSuperStoreUniqueEmail($storeemail = null, $storeId = null) {

        $conditions = array('LOWER(Store.email_id)' => strtolower($storeemail), 'Store.is_deleted' => 0, 'Store.is_active' => 1);
        if ($storeId) {
            $conditions['Store.id !='] = $storeId;
        }
        $item = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($item) {
            return 0;
        } else {
            return 1;
        }
    }

    public function getStoreIdByName($storeName = null) {
        $conditions = array('LOWER(Store.store_name)' => strtolower($storeName), 'Store.is_active' => 1, 'Store.is_deleted' => 0);
        $item = $this->find('first', array('fields' => array('id', 'merchant_id'), 'conditions' => $conditions));
        return $item;
    }

    public function getkitchentype($storeId = null) {
        $kitchenDisplay = $this->find('first', array('fields' => array('id', 'kitchen_dashboard_type'), 'conditions' => array('id' => $storeId)));
        return $kitchenDisplay;
    }

    public function getAllMerchantStores($merchantId = null) {

        $storeResult = $this->find('all', array('conditions' => array('Store.merchant_id' => $merchantId)));
        if ($storeResult) {
            return $storeResult;
        } else {
            return false;
        }
    }

    public function storeemailExists($email = null, $merchantId = null) {
        if (!empty($email) && !empty($merchantId)) {
            $isValid = true;
            $conditions = array('Store.email_id' => trim($email), 'Store.merchant_id' => $merchantId, 'Store.is_deleted' => 0, 'Store.is_active' => 1);
            //$conditions = array('Store.email_id' => trim($email), 'Store.is_deleted' => 0, 'Store.is_active' => 1);
            $result = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));

            if ($result) {
                $isValid = false;
            }
            return $isValid;
        }
        $isValid = false;
        return $isValid;
    }

    public function checkStoreEmailExists($email = null, $merchantId = null) {
        if (!empty($email) && !empty($merchantId)) {
            $isValid = 1;
            $conditions = array('Store.email_id' => trim($email), 'Store.merchant_id' => $merchantId, 'Store.is_deleted' => 0, 'Store.is_active' => 1);
            //$conditions = array('Store.email_id' => trim($email), 'Store.is_deleted' => 0, 'Store.is_active' => 1);
            $result = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
            if ($result) {
                $isValid = 0;
            }
            return $isValid;
        }
        $isValid = 0;
        return $isValid;
    }

    public function checkPreorder($storeId, $merchantID) {
        $result = $this->find('first', array('fields' => array('id', 'pre_order_allowed'), 'conditions' => array('Store.merchant_id' => $merchantID, 'Store.is_deleted' => 0, 'Store.is_active' => 1, 'Store.id' => $storeId)));
        if (!empty($result)) {
            return $result['Store']['pre_order_allowed'];
        }
        return 0;
    }

    public function checkPopup($storeId, $merchantID) {
        $result = $this->find('first', array('fields' => array('allow_pop_up'), 'conditions' => array('Store.merchant_id' => $merchantID, 'Store.is_deleted' => 0, 'Store.is_active' => 1, 'Store.id' => $storeId)));
        if (!empty($result)) {
            return $result['Store']['allow_pop_up'];
        }
        return 0;
    }

    public function fetchStoreCutOff($storeId = null) {

        $storeResult = $this->find('first', array('fields' => array('id', 'cutoff_time'), 'conditions' => array('Store.id' => $storeId)));
        return $storeResult;
    }

    public function getlatlong($storeId = null) {
        $storeResult = $this->find('first', array('fields' => array('id', 'latitude', 'logitude', 'store_name'), 'conditions' => array('Store.id' => $storeId)));
        return $storeResult;
    }

    public function getDelayTime($storeId = null) {

        $storeResult = $this->find('first', array('fields' => array('id', 'delivery_delay', 'pick_up_delay'), 'conditions' => array('Store.id' => $storeId, 'Store.is_deleted' => 0, 'Store.is_active' => 1)));
        if ($storeResult) {
            return $storeResult;
        } else {
            return false;
        }
    }

    public function getAllStoreByMerchantId($merchant_id = null) {
        if ($merchant_id) {
            $storeData = $this->find('all', array('fields' => array('id'), 'conditions' => array('merchant_id' => $merchant_id, 'is_active' => 1, 'is_deleted' => 0), 'recursive' => -1));
            return $storeData;
        } else {
            return false;
        }
    }

    public function getAllStoresByMerchantId($merchant_id = null) {
        $storeResult = $this->find('list', array('fields' => array('id'), 'conditions' => array('merchant_id' => $merchant_id, 'is_active' => 1, 'is_deleted' => 0)));
        if ($storeResult) {
            return $storeResult;
        } else {
            return false;
        }
    }

    /* ----------------------------------------
      Funtion name:fetchStorePaypalDetail
      Desc:To find the store detail
      created:31-08-2016
     * ---------------------------------------- */

    public function fetchStorePaypalDetail($storeId = null) {
        $storeResult = $this->find('first', array('fields' => array('paypal_mode', 'paypal_email', 'paypal_password', 'paypal_signature'), 'conditions' => array('Store.id' => $storeId)));
        if ($storeResult) {
            return $storeResult;
        } else {
            return false;
        }
    }

    public function getNowAvailability($ordeType = null, $storeId = null) {
        $conditions = array('Store.id' => $storeId);
        if ($ordeType == 2) {
            $conditions['Store.pickblackout_limit >'] = 0;
        } elseif ($ordeType == 3) {
            $conditions['Store.deliveryblackout_limit >'] = 0;
        }
        $storeResult = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($storeResult) {
            return 0;
        } else {
            return 1;
        }
    }

    public function checkmercNumber($number = null, $storeId = null) {
        if (!empty($storeId)) {
            $condition['Store.id !='] = $storeId;
        }
        $condition['Store.is_active'] = 1;
        $condition['Store.is_deleted'] = 0;
        $condition['Store.merchant_number'] = $number;
        $status = $this->find('count', array('fields' => array('id'), 'conditions' => $condition));

        if ($status) {
            return false;
        } else {
            return true;
        }
    }

    public function getMerchantNumber($storeID = null) {
        $condition['Store.id'] = $storeID;
        $condition['Store.is_active'] = 1;
        $condition['Store.is_deleted'] = 0;
        $storedata = $this->find('first', array('fields' => array('merchant_number'), 'conditions' => $condition));
        return $storedata;
    }

    public function getStoreList() {
        $store = $this->find('all', array('fields' => array('id', 'merchant_id')));
        if ($store) {
            return $store;
        } else {
            return false;
        }
    }

}
