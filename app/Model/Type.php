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
class Type extends AppModel {

    var $name = 'Type';
    public $hasMany = array(
        'ItemType' => array(
            'className' => 'ItemType',
            'foreginKey' => 'type_id',
        )
    );

    public function getTypes($storeId = null) {
        if ($storeId) {
            $typeList = $this->find('list', array('fields' => array('id', 'name'), 'conditions' => array('Type.store_id' => $storeId, 'Type.is_active' => 1, 'Type.is_deleted' => 0)));
            if ($typeList) {
                return $typeList;
            }
        }
    }

    public function findTypeName($typeId = null, $storeId = null) {
        if ($typeId) {
            $typeList = $this->find('first', array('fields' => array('name'), 'conditions' => array('Type.id' => $typeId, 'Type.store_id' => $storeId, 'Type.is_active' => 1, 'Type.is_deleted' => 0)));
            if ($typeList) {
                return $typeList;
            } else {
                return false;
            }
        }
    }

    /* ------------------------------------------------
      Function name:checkTypeUniqueName()
      Description:to check Type name is unique
      created:7/8/2015
      ----------------------------------------------------- */

    public function checkTypeUniqueName($typeName = null, $storeId = null, $TypeId = null) {
        $conditions = array('LOWER(Type.name)' => strtolower($typeName), 'Type.is_deleted' => 0);
        if ($TypeId) {
            $conditions['Type.id !='] = $TypeId;
        }
        if ($storeId) {
            $conditions['Type.store_id'] = $storeId;
        }
        $type = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($type) {
            return 0;
        } else {
            return 1;
        }
    }

    /* ------------------------------------------------
      Function name:saveType()
      Description:To Save Type Information
      created:07/8/2015
      ----------------------------------------------------- */

    public function saveType($typeData = null) {
        if ($typeData) {
            if ($this->save($typeData)) {
                return true; //Success
            } else {
                return false; // Failure 
            }
        }
    }

    /* ------------------------------------------------
      Function name:getTypeDetail()
      Description:To find Detail of Type from type table
      created:7/8/2015
      ----------------------------------------------------- */

    public function getTypeDetail($typeId = null, $storeId = null) {
        $typeDetail = $this->find('first', array('conditions' => array('Type.store_id' => $storeId, 'Type.id' => $typeId)));
        if ($typeDetail) {
            return $typeDetail;
        }
    }

    public function getTypeIdByName($typeName = null, $storeId = null) {
        $conditions = array('LOWER(Type.name)' => strtolower($typeName), 'Type.store_id' => $storeId, 'Type.is_active' => 1, 'Type.is_deleted' => 0);
        $categoryDetail = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        return $categoryDetail;
    }

    public function findTypeList($storeId = null) {
        $categoryList = $this->find('all', array('conditions' => array('Type.store_id' => $storeId, 'Type.is_deleted' => 0)));
        if ($categoryList) {
            return $categoryList;
        }
    }
     public function findTypeListByStoreId($storeId = null) {
        $categoryList = $this->find('all', array('conditions' => array('Type.store_id' => $storeId, 'Type.is_deleted' => 0)));
        if ($categoryList) {
            return $categoryList;
        }
    }
     public function findTypeListByMerchantId($merchantId = null) {
        $categoryList = $this->find('all', array('conditions' => array('Type.merchant_id' => $merchantId, 'Type.is_deleted' => 0)));
        if ($categoryList) {
            return $categoryList;
        }
    }

    public function getStoreType($storeId = null) {
        $typeList = $this->find('list', array('fields' => array('id', 'name'), 'conditions' => array('Type.store_id' => $storeId, 'Type.is_active' => 1, 'Type.is_deleted' => 0)));
        if ($typeList) {
            return $typeList;
        }
    }

    public function getTypeDetailById($typeId = null) {
        $typeDetail = $this->findById($typeId);
        if ($typeDetail) {
            return $typeDetail;
        }
    }

    public function getTypeIdByNameAndStoreId($store_id = null, $name = null) {
        $typeData = $this->find('first', array('fields' => array('id'), 'conditions' => array('store_id' => $store_id, 'LOWER(name)' => strtolower($name), 'is_active' => 1, 'is_deleted' => 0)));
        return $typeData;
    }

    public function getTypeListWithDuplicateName($merchant_id = null) {
        $typeList = $this->find('list', array('fields' => array('name', 'name'), 'conditions' => array('merchant_id' => $merchant_id, 'is_active' => 1, 'is_deleted' => 0), 'group' => 'name'));
        return $typeList;
    }
    public function checkTypeWithId($typeId=null){
        $conditions = array('Type.id'=>$typeId);
            $typeDet =$this->find('first',array('fields'=>array('id','store_id'),'conditions'=>$conditions));  
            return $typeDet;

    }
    
    public function getTypedetails($storeId = null) {
        $typedet = $this->find('all', array('recursive'=>3,'fields' => array('id','name','min_value','max_value'),'conditions' => array('Type.store_id' => $storeId, 'Type.is_deleted' => 0,'Type.is_active' => 1)));
        if ($typedet) {
            return $typedet;
        }
    }

}
