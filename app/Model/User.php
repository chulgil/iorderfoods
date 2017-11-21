<?php

/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('AppModel', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class User extends AppModel {
    var $virtualFields = array(
        'userName' => "CONCAT(User.fname, ' ', User.lname)"
    );

//***************Model Relation**************************//
//     public $hasOne = array(
//	    'DeliveryAddress' => array(
//		'className' => 'DeliveryAddress',
//		'conditions' => array('DeliveryAddress.is_deleted' => '0','DeliveryAddress.is_active'=>1),
//		'order' => 'DeliveryAddress.created DESC',
//		'dependent' => true,
//		'foreignKey'=>'user_id'
//	    )
//     );
    //encrypt password before saving
    public function beforeSave($options = array()) {
        //echo "<pre>"; print_r($this->data);die;
        if (!empty($this->data[$this->alias]['password'])) {
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
        }
        return true;
    }

    /* --------------------------------------------
      Function Name:validate_passwords
      Desc:Custom validation for confirm poassword
      created:22-7-2015
      ---------------------------------------------- */

    public function validate_passwords() {
        return $this->data[$this->alias]['password'] === $this->data[$this->alias]['password_match'];
    }

    /* --------------------------------------------
      Function Name:saveUserInfo()
      Desc:To save data on User table
      created:22-7-2015
      ---------------------------------------------- */

    public function saveUserInfo($userData = null) {
        if ($userData) {
            if ($this->save($userData)) {
                return true; //Success
            } else {
                return false; // Failure 
            }
        }
    }

    /* ------------------------------------------------
      Function name:emailCheck()
      Description:Check for email already exist or not
      created:22/7/2015
      ----------------------------------------------------- */

    public function emailCheck($roleId = null, $storeId = null, $merchantId = null, $emailEntered = null) {
        if ($emailEntered) {
            $isValid = true;
            //if ($roleId == 4) {
            //$result = $this->find('first', array('conditions' => array('User.email' => trim($emailEntered), 'User.role_id' => $roleId, 'User.is_deleted' => 0, 'User.store_id' => $storeId), 'fields' => array('id')));
            $result = $this->find('first', array('conditions' => array('User.email' => trim($emailEntered), 'User.role_id' => $roleId, 'User.is_deleted' => 0, 'User.merchant_id' => $merchantId), 'fields' => array('id')));
            //} else {
            //    return false;
            //}
            if ($result) {
                $isValid = false;
            }
            return $isValid;
        }
    }

    /* ------------------------------------------------
      Function name:getRandomCode()
      Description:To generate random password
      created:22/7/2015
      ----------------------------------------------------- */

    public function getRandomCode($length = 8) {
        $this->layout = '';
        $this->autoRender = false;
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890"; //length:36
        $final_rand = '';
        for ($i = 0; $i < $length; $i++) {
            $final_rand .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $final_rand;
    }

    /* ------------------------------------------------
      Function name:checkForgetEmail()
      Description:To fetch the out the email when user will forget the password.
      created:22/7/2015
      ----------------------------------------------------- */

    public function checkForgetEmail($roleId = null, $storeId = null, $merchantId = null, $email = null) {
        if ($email) {

            if (in_array(4, $roleId)) { // Customer Role
                $userEmail = $this->find('first', array('conditions' => array('User.email' => $email, 'User.merchant_id' => $merchantId, 'User.is_deleted' => 0, 'User.is_active' => 1, 'User.role_id' => $roleId), 'fields' => array('User.id', 'User.email', 'User.fname', 'User.lname')));
            } elseif ($roleId == 3) { // Customer Role
                $userEmail = $this->find('first', array('conditions' => array('User.email' => $email, 'User.store_id' => $storeId, 'User.is_deleted' => 0, 'User.is_active' => 1, 'User.role_id' => $roleId), 'fields' => array('User.id', 'User.email', 'User.fname', 'User.lname')));
            } elseif (in_array(5, $roleId)) { // Customer Role
                $userEmail = $this->find('first', array('conditions' => array('User.email' => $email, 'User.merchant_id' => $merchantId, 'User.is_deleted' => 0, 'User.is_active' => 1, 'User.role_id' => $roleId), 'fields' => array('User.id', 'User.email', 'User.fname', 'User.lname')));
            } else {
                return false;
            }

//            if ($roleId == 4) { // Customer Role
//                $userEmail = $this->find('first', array('conditions' => array('User.email' => $email, 'User.store_id' => $storeId, 'User.is_deleted' => 0, 'User.role_id' => $roleId), 'fields' => array('User.id', 'User.email', 'User.fname', 'User.lname')));
//            } elseif ($roleId == 3) { // Customer Role
//                $userEmail = $this->find('first', array('conditions' => array('User.email' => $email, 'User.store_id' => $storeId, 'User.is_deleted' => 0, 'User.role_id' => $roleId), 'fields' => array('User.id', 'User.email', 'User.fname', 'User.lname')));
//            } elseif ($roleId == 5) { // Customer Role
//                $userEmail = $this->find('first', array('conditions' => array('User.email' => $email, 'User.merchant_id' => $merchantId, 'User.is_deleted' => 0, 'User.role_id' => $roleId), 'fields' => array('User.id', 'User.email', 'User.fname', 'User.lname')));
//            } else {
//                return false;
//            }
            if ($userEmail) {
                return $userEmail;
            } else {
                return false;
            }
        }
    }

    /* ------------------------------------------------
      Function name:checkMerchantForgetEmail()
      Description:To fetch the out the email when user will forget the password.
      created:04/9/2015
      ----------------------------------------------------- */

    public function checkMerchantForgetEmail($roleId = null, $email = null) {
        if ($email) {
            if ($roleId == 2) { // Customer Role
                $userEmail = $this->find('first', array('conditions' => array('User.email' => $email, 'User.is_deleted' => 0, 'User.role_id' => $roleId), 'fields' => array('User.id', 'User.email', 'User.fname', 'User.lname', 'User.merchant_id')));
            } else {
                return false;
            }
            if ($userEmail) {
                return $userEmail;
            } else {
                return false;
            }
        }
    }

    /* ------------------------------------------------
      Function name:checkSuperForgetEmail()
      Description:To fetch the out the email when user will forget the password.
      created:04/9/2015
      ----------------------------------------------------- */

    public function checkSuperForgetEmail($roleId = null, $email = null) {
        if ($email) {

            if ($roleId == 1) { // Customer Role
                $userEmail = $this->find('first', array('conditions' => array('User.email' => $email, 'User.is_deleted' => 0, 'User.role_id' => $roleId)));
            } else {
                return false;
            }
            if ($userEmail) {
                return $userEmail;
            } else {
                return false;
            }
        }
    }

    /* ------------------------------------------------
      Function name:currentUserInfo()
      Description:To fetch the out the current info  when user will edit some thing.
      created:22/7/2015
      ----------------------------------------------------- */

    public function currentUserInfo($userId) {
        if ($userId) {
            $userData = $this->find('first', array('conditions' => array('User.id' => $userId)));
            if ($userData) {
                return $userData;
            } else {
                return false;
            }
        }
    }

    /* ------------------------------------------------
      Function name:getUserDetail()
      Description:To find Detail of the Perticular user from user table
      created:10/8/2015
      ----------------------------------------------------- */

    public function getUserDetail($userId = null, $storeId = null) {
        if ($userId) {
            $userData = $this->find('first', array('conditions' => array('User.id' => $userId, 'User.store_id' => $storeId), 'recursive' => -1));
            if ($userData) {
                return $userData;
            } else {
                return false;
            }
        }
    }

    /* ------------------------------------------------
      Function name:storeemailExists()
      Description:To check if email already exists for store Users.
      created:27/7/2015
      ----------------------------------------------------- */

    public function storeemailExists($email = null, $roleId = null, $storeId = null) {
        if ($email) {
            $isValid = true;
            $result = $this->find('first', array('conditions' => array('User.email' => trim($email), 'User.role_id' => $roleId, 'User.is_deleted' => 0, 'User.is_active' => 1, 'User.store_id' => $storeId), 'fields' => array('id')));

            if ($result) {
                $isValid = false;
            }
            return $isValid;
        }
    }

    /* ------------------------------------------------
      Function name:storeemailExists()
      Description:To check if email already exists for store Users.
      created:27/7/2015
      ----------------------------------------------------- */

    public function emailExistsStore($email = null, $roleId = null, $merchantId = null) {
        if ($email) {
            $isValid = true;
            $result = $this->find('first', array('conditions' => array('User.email' => trim($email), 'User.role_id' => $roleId, 'User.is_deleted' => 0, 'User.merchant_id' => $merchantId), 'fields' => array('id')));
            if ($result) {
                $isValid = false;
            }
            return $isValid;
        }
    }

    /* ------------------------------------------------
      Function name:merchantemailExists()
      Description:To check if email already exists for HQ Users.
      created:27/7/2015
      ----------------------------------------------------- */

    public function merchantemailExists($email = null, $roleId = null, $merchantid = null) {
        if ($email) {
            $isValid = true;
            //$result = $this->find('first', array('conditions' => array('User.email' => trim($email), 'User.role_id' => $roleId, 'User.is_deleted' => 0, 'User.is_active' => 1, 'User.merchant_id' => $merchantid), 'fields' => array('id')));
            $result = $this->find('first', array('conditions' => array('User.email' => trim($email), 'User.role_id' => $roleId, 'User.is_deleted' => 0, 'User.merchant_id' => $merchantid), 'fields' => array('id')));


            if ($result) {
                $isValid = false;
            }
            return $isValid;
        }
    }

    /* ------------------------------------------------
      Function name:superemailExists()
      Description:To check if email already exists for HQ Users.
      created:27/7/2015
      ----------------------------------------------------- */

    public function superemailExists($email = null, $roleId = null) {
        if ($email) {
            $isValid = true;
            $result = $this->find('first', array('conditions' => array('User.email' => trim($email), 'User.role_id' => $roleId, 'User.is_deleted' => 0, 'User.is_active' => 1), 'fields' => array('id')));

            if ($result) {
                $isValid = false;
            }
            return $isValid;
        }
    }

    /* ------------------------------------------------
      Function name:emailExistsSuper()
      Description:To check if email already exists for HQ Users.
      created:27/7/2015
      ----------------------------------------------------- */

    public function emailExistsSuper($email = null, $roleId = null) {
        if ($email) {
            $isValid = true;
            $result = $this->find('first', array('conditions' => array('User.email' => trim($email), 'User.role_id' => $roleId, 'User.is_deleted' => 0), 'fields' => array('id')));

            if ($result) {
                $isValid = false;
            }
            return $isValid;
        }
    }

    /* ------------------------------------------------
      Function name:checkUserUniqueEmail()
      Description:to check user email for edit  is unique
      created:10/8/2015
      ----------------------------------------------------- */

    public function checkUserUniqueEmail($email = null, $storeId = null, $userId = null, $roleID = null) {

        $conditions = array('LOWER(User.email)' => strtolower($email), 'User.store_id' => $storeId, 'User.is_deleted' => 0);
        if ($userId) {
            $conditions['User.id !='] = $userId;
        }
        if ($roleID) {
            $conditions['User.role_id'] = $roleID;
        }
        $data = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($data) {
            return 0;
        } else {
            return 1;
        }
    }

    /* ----------------------------------------
      Funtion name:getTotalMerchantCustomer
      Desc:To find the List of HQ customer
      created:1-09-2015
     * ---------------------------------------- */

    public function getTotalMerchantCustomer($merchantId = null) {
        $usercount = $this->find('count', array('fields' => array('id'), 'conditions' => array('User.merchant_id' => $merchantId, 'User.store_id !=' => NULL, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => 5)));
        if ($usercount) {
            return $usercount;
        } else {
            return false;
        }
    }

    /* ----------------------------------------
      Funtion name:getTotalStoreCustomer
      Desc:To find the List of Store customer
      created:2-09-2015
     * ---------------------------------------- */

    public function getTotalStoreCustomer($storeId = null, $merchantId = null) {
        $usercount = $this->find('count', array('fields' => array('id'), 'conditions' => array('User.store_id' => $storeId, 'User.merchant_id' => $merchantId, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => 4)));
        if ($usercount) {
            return $usercount;
        } else {
            return false;
        }
    }

    /* ----------------------------------------
      Funtion name:fetchUserToday
      Desc:To find the List of User
      created:9-09-2015
     * ---------------------------------------- */

    public function fetchUserToday($storeId = null, $start = null, $end = null, $customerType = null) 
    {
        $conditions = array('User.is_active' => 1, 'User.is_deleted' => 0);
        if ($start && $end) 
        {
            $conditions['User.created >='] = $start;
            $conditions['User.created <='] = $end;
        }
        
        if(isset($customerType))
        {
            $conditions['User.role_id'] = $customerType;
            if($customerType == 5)
            {
                $conditions['User.merchant_id'] = $storeId;
            }
            else {
                $conditions['User.store_id'] = $storeId;
            }
        }
        else
        {
            $conditions['User.role_id IN '] = array(4,5);
            $conditions['User.store_id'] = $storeId;
        }
        $result = $this->find('all', array('fields' => array("COUNT('User.id') as per_day", 'created'), 'group' => array("DATE_FORMAT(User.created, '%Y-%m-%d')"), 'conditions' => $conditions));
        /*if (!empty($storeId)) 
        {
            if ($start && $end) 
            {
                $result = $this->find('all', array('fields' => array("COUNT('User.id') as per_day", 'created'), 'group' => array("DATE_FORMAT(User.created, '%Y-%m-%d')"), 'conditions' => array('User.store_id' => $storeId, 'User.created >=' => $start, 'User.created <=' => $end, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => array(4,5))));
            }
            else
            {
                $result = $this->find('all', array('fields' => array("COUNT('User.id') as per_day", 'created'), 'group' => array("DATE_FORMAT(User.created, '%Y-%m-%d')"), 'conditions' => array('User.store_id' => $storeId, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => array(4,5))));
            }
        }
        else
        {
            if ($start && $end) 
            {
                $result = $this->find('all', array('fields' => array("COUNT('User.id') as per_day", 'created'), 'group' => array("DATE_FORMAT(User.created, '%Y-%m-%d')"), 'conditions' => array('User.created >=' => $start, 'User.created <=' => $end, 'User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => array(4,5))));
                //print_r($result);die;
            }
            else
            {
                $result = $this->find('all', array('fields' => array("COUNT('User.id') as per_day", 'created'), 'group' => array("DATE_FORMAT(User.created, '%Y-%m-%d')"), 'conditions' => array('User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => array(4,5))));
            }
            //echo '<pre>';print_r($result);die;	
        }*/
        return $result;
    }

    /* ----------------------------------------
      Funtion name:fetchUser
      Desc:To find the List of User
      created:9-09-2015
     * ---------------------------------------- */

    public function fetchUser($storeId = null) {
        $result = $this->find('all', array('fields' => array("COUNT('User.created') as per_day", 'created'), 'group' => array("DATE_FORMAT(User.created, '%Y-%m-%d')"), 'conditions' => array('User.store_id' => $storeId, 'User.is_active' => 1, 'User.is_deleted' => 0)));
        return $result;
    }

    /* ----------------------------------------
      Funtion name:getTotalCustomer
      Desc:To find the List of User
      created:14-09-2015
     * ---------------------------------------- */

    public function getTotalCustomer() {
        $usercount = $this->find('count', array('fields' => array('id'), 'conditions' => array('User.is_active' => 1, 'User.is_deleted' => 0, 'User.role_id' => 4)));
        if ($usercount) {
            return $usercount;
        } else {
            return false;
        }
    }

    /* ------------------------------------------------
      Function name:getUser()
      Description:To find Detail of the Perticular user from user table
      created:13/09/2015
      ----------------------------------------------------- */

    public function getUser($userId = null) {
        if ($userId) {
            $userData = $this->find('first', array('conditions' => array('User.id' => $userId), 'recursive' => -1));
            if ($userData) {
                return $userData;
            } else {
                return false;
            }
        }
    }

    /* ------------------------------------------------
      Function name:supermerchantemailExists()
      Description:To check if email already exists for store Users.
      created:27/7/2015
      ----------------------------------------------------- */

    public function supermerchantemailExists($email = null) {
        if ($email) {
            $isValid = true;
            $result = $this->find('first', array('conditions' => array('User.email' => trim($email), 'User.is_deleted' => 0, 'User.is_active' => 1, 'User.role_id' => 2), 'fields' => array('id')));

            if ($result) {
                $isValid = false;
            }
            return $isValid;
        }
    }

    /* ------------------------------------------------
      Function name:supermerchantemailExists()
      Description:To check if email already exists for store Users.
      created:27/7/2015
      ----------------------------------------------------- */

    public function superstoreemailExists($email = null) {
        if ($email) {
            $isValid = true;
            $result = $this->find('first', array('conditions' => array('User.email' => trim($email), 'User.is_deleted' => 0, 'User.is_active' => 1, 'User.role_id' => 3), 'fields' => array('id')));

            if ($result) {
                $isValid = false;
            }
            return $isValid;
        }
    }

    /* ------------------------------------------------
      Function name:superemailExists()
      Description:To check if email already exists for HQ Users.
      created:27/7/2015
      ----------------------------------------------------- */

    public function supermailExists($email = null, $roleId = null, $userId = null, $store_mercID) {


        $conditions = array('LOWER(User.email)' => strtolower($email), 'User.is_deleted' => 0, 'User.is_active' => 1, 'User.role_id' => $roleId);
        if ($roleId == 2) {
            $conditions['User.merchant_id'] = $store_mercID;
        } elseif ($roleId == 3) {
            $conditions['User.store_id'] = $store_mercID;
        }
        if ($userId) {
            $conditions['User.id !='] = $userId;
        }
        $item = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($item) {
            return 0;
        } else {
            return 1;
        }
    }

    public function findUserRole($userId = null) {
        $userRole = $this->find('first', array('fields' => array('User.role_id'), 'conditions' => array('User.id' => $userId)));
        return $userRole;
    }

    /* ------------------------------------------------
      Function name:checkUserUniqueEmail()
      Description:to check user email for edit  is unique
      created:10/8/2015
      ----------------------------------------------------- */

    public function checkUserUniqueEmailHq($email = null, $merchantId = null, $userId = null, $roleID = null) {

        $conditions = array('LOWER(User.email)' => strtolower($email), 'User.merchant_id' => $merchantId, 'User.is_deleted' => 0);
        if ($userId) {
            $conditions['User.id !='] = $userId;
        }
        if ($roleID) {
            $conditions['User.role_id'] = $roleID;
        }
        $data = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($data) {
            return 0;
        } else {
            return 1;
        }
    }

}