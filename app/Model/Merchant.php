<?php

App::uses('AppModel', 'Model');

class Merchant extends AppModel {

    var $name = 'Merchant';

    /* ------------------------------------------------
      Function name:saveItem()
      Description:To Save Item Information
      created:04/8/2015
      ----------------------------------------------------- */

    public function saveMerchant($merchantData = null) {
        if ($merchantData) {
            if ($this->save($merchantData)) {
                return true; //Success
            } else {
                return false; // Failure 
            }
        }
    }

    public function currentUserInfo($userId) {
        if ($userId) {
            $userData = $this->find('first', array('conditions' => array('Merchant.user_id' => $userId)));
            if ($userData) {
                return $userData;
            } else {
                return false;
            }
        }
    }

    /* ----------------------------------------
      Funtion name:fetchMerchantDetail
      Desc:To find the store detail
      created:22-07-2015
     * ---------------------------------------- */

    public function fetchMerchantDetail($merchantId = null) {
        $storeResult = $this->find('first', array('fields' => array('email', 'id', 'background_image', 'logo','banner_image','contact_us_bg_image','logotype'), 'conditions' => array('Merchant.id' => $merchantId)));
        if ($storeResult) {
            return $storeResult;
        } else {
            return false;
        }
    }

    /* ----------------------------------------
      Funtion name:getTotalMerchant
      Desc:To find the List of Merchant's
      created:14-09-2015
     * ---------------------------------------- */

    public function getTotalMerchant() {
        $merchantcount = $this->find('count', array('fields' => array('id'), 'conditions' => array('Merchant.is_deleted' => 0)));
        if ($merchantcount) {
            return $merchantcount;
        } else {
            return false;
        }
    }

    /* ----------------------------------------
      Funtion name:getListTotalMerchant
      Desc:To find the List of Merchant's
      created:15-09-2015
     * ---------------------------------------- */

    public function getListTotalMerchant() {
        $merchant = $this->find('list', array('fields' => array('id', 'name'), 'conditions' => array('Merchant.is_active' => 1, 'Merchant.is_deleted' => 0)));
        if ($merchant) {
            return $merchant;
        } else {
            return false;
        }
    }

    public function checkMerchantUniqueName($merchantName = null, $merchantId = null) {

        $conditions = array('LOWER(Merchant.name)' => strtolower($merchantName), 'Merchant.is_deleted' => 0, 'Merchant.is_active' => 1);
        if ($merchantId) {
            $conditions['Merchant.id !='] = $merchantId;
        }
        $item = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($item) {
            return 0;
        } else {
            return 1;
        }
    }

    public function checkMerchantUniqueEmail($merchantemail = null, $merchantId = null) {

        $conditions = array('LOWER(Merchant.email)' => strtolower($merchantemail), 'Merchant.is_deleted' => 0, 'Merchant.is_active' => 1);
        if ($merchantId) {
            $conditions['Merchant.id !='] = $merchantId;
        }
        $item = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($item) {
            return 0;
        } else {
            return 1;
        }
    }

    /* ----------------------------------------
      Funtion name:getMerchantDetail
      Desc:To find the merchant detail with it's user
      created:16-09-2015
     * ---------------------------------------- */

    public function getMerchantDetail($merchantId = null) {
        $merchantResult = $this->find('first', array('conditions' => array('Merchant.id' => $merchantId)));
        if ($merchantResult) {
            return $merchantResult;
        } else {
            return false;
        }
    }

    public function merchant_info($merchantName = null) {

        $merchantResult = $this->find('first', array('fields' => array('id', 'name', 'background_image', 'logo'), 'conditions' => array('Merchant.domain_name' => $merchantName, 'Merchant.is_deleted' => 0, 'Merchant.is_active' => 1)));
        if ($merchantResult) {
            return $merchantResult;
        } else {
            return false;
        }
    }

    public function merchantemailExists($email = null) {
        if ($email) {
            $isValid = true;
            $result = $this->find('first', array('conditions' => array('Merchant.email' => trim($email), 'Merchant.is_deleted' => 0, 'Merchant.is_active' => 1), 'fields' => array('id')));

            if ($result) {
                $isValid = false;
            }
            return $isValid;
        }
    }

    public function getTransactionAllowData($merchantID) {
        if ($merchantID) {
            $result = $this->find('first', array('conditions' => array('Merchant.id' => $merchantID), 'fields' => array('is_allow_transaction')));
            if (!empty($result)) {
                return $result['Merchant']['is_allow_transaction'];
            } else {
                return false;
            }
        }
    }
    
    public function getMerchantList() {
        $merchant = $this->find('list', array('fields' => array('id')));
        if ($merchant) {
            return $merchant;
        } else {
            return false;
        }
    }

}
