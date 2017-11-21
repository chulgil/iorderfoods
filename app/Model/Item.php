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
class Item extends AppModel {

    var $name = 'Item';

    /* ------------------------------------------------
      Function name:checkItemUniqueName()
      Description:to check item name is unique
      created:3/8/2015
      ----------------------------------------------------- */

    public function checkItemUniqueName($itemName = null, $storeId = null, $Itemid = null, $categoryID = null) {

        $conditions = array('LOWER(Item.name)' => strtolower($itemName), 'Item.store_id' => $storeId, 'Item.is_deleted' => 0);
        if ($Itemid) {
            $conditions['Item.id !='] = $Itemid;
        }
        if ($categoryID) {
            $conditions['Item.category_id'] = $categoryID;
        }
        $item = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($item) {
            return 0;
        } else {
            return 1;
        }
    }

    /* ------------------------------------------------
      Function name:checkItemUniqueName()
      Description:to check item name is unique
      created:3/8/2015
      ----------------------------------------------------- */

    public function fetchItemDetail($itemId = null, $storeId = null, $allItems = null) {

        $conditions = array('Item.store_id' => $storeId, 'Item.is_deleted' => 0, 'Item.id' => $itemId);

        if (!$allItems) {
            $conditions['Item.is_active'] = 1;
        }
        $item = $this->find('first', array('conditions' => $conditions, 'recursive' => 4));
        if ($item) {

            return $item;
        } else {
            return false;
        }
    }

    /* ------------------------------------------------
      Function name:saveItem()
      Description:To Save Item Information
      created:04/8/2015
      ----------------------------------------------------- */

    public function saveItem($itemData = null) {
        if ($itemData) {
            if ($this->save($itemData)) {
                return true; //Success
            } else {
                return false; // Failure 
            }
        }
    }

    /* ------------------------------------------------
      Function name:getItemsByCategory()
      Description:To get all items of category
      created:06/8/2015
      ----------------------------------------------------- */

    public function getItemsByCategory($categoryID = null, $storeId = null) {
        $itemList = '';
        if ($categoryID) {
            $itemList = $this->find('list', array('conditions' => array('Item.store_id' => $storeId, 'Item.is_active' => 1, 'Item.is_deleted' => 0, 'Item.category_id' => $categoryID)));
        }
        if ($itemList) {
            return $itemList;
        } else {
            return false;
        }
    }

    /* ------------------------------------------------
      Function name:getallItemsByStore()
      Description:To get all items of category
      created:06/8/2015
      ----------------------------------------------------- */

    public function getallItemsByStore($storeId = null) {
        $itemList = '';
        if ($storeId) {

            $itemList = $this->find('list', array('conditions' => array('Item.store_id' => $storeId, 'Item.is_deleted' => 0,'Item.is_active'=>1), 'order' => array('Item.name ASC')));
        }
        if ($itemList) {
            return $itemList;
        } else {
            return false;
        }
    }

    /* ------------------------------------------------
      Function name:getcategoryByitemID()
      Description:to check item name is unique
      created:3/8/2015
      ----------------------------------------------------- */

    public function getcategoryByitemID($itemId = null, $storeId = null) {
        // echo $itemId;die;
        $categoryid = $this->find('first', array('fields' => array('id', 'category_id','name'), 'conditions' => array('Item.store_id' => $storeId, 'Item.is_active' => 1, 'Item.is_deleted' => 0, 'Item.id' => $itemId), 'recursive' => 2));
        // print_r($item);die;
        if ($categoryid) {
            return $categoryid;
        } else {
            return false;
        }
    }

    /* ------------------------------------------------
      Function name:getItemName()
      Description:to check item name is unique
      created:3/8/2015
      ----------------------------------------------------- */

    public function getItemName($itemId = null, $storeId = null) {
        // echo $itemId;die;
        $itemName = $this->find('first', array('fields' => array('id', 'name'), 'conditions' => array('Item.store_id' => $storeId, 'Item.is_active' => 1, 'Item.is_deleted' => 0, 'Item.id' => $itemId)));
        // print_r($item);die;
        if ($itemName) {
            return $itemName;
        } else {
            return false;
        }
    }

    /* ------------------------------------------------
      Function name:getAllItems()
      Description:to get all Items
      created:3/8/2015
      ----------------------------------------------------- */

    public function getAllItems($storeId = null) {
        // echo $itemId;die;
        $itemNameList = $this->find('list', array('fields' => array('id', 'name'), 'conditions' => array('Item.store_id' => $storeId, 'Item.is_active' => 1, 'Item.is_deleted' => 0), 'order' => array('Item.name ASC')));
        // print_r($item);die;
        if ($itemNameList) {
            return $itemNameList;
        } else {
            return false;
        }
    }

    public function getItemById($itemId = null) {
        $itemDetail = $this->find('first', array('recursive' => 2, 'conditions' => array('Item.id' => $itemId, 'Item.is_active' => 1, 'Item.is_deleted' => 0)));
        return $itemDetail;
    }

    public function getItemByName($itemName = null, $storeId = null, $categoryId = null) {
        $conditions = array('LOWER(Item.name)' => strtolower($itemName), 'Item.store_id' => $storeId, 'Item.category_id' => $categoryId, 'Item.is_active' => 1, 'Item.is_deleted' => 0);
        $categoryDetail = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        return $categoryDetail;
    }

    public function getItemByNameTopping($itemName = null, $storeId = null, $categoryId = null) {
        $conditions = array('LOWER(Item.name)' => strtolower($itemName), 'Item.store_id' => $storeId, 'Item.category_id' => $categoryId, 'Item.is_active' => 1, 'Item.is_deleted' => 0);
        $categoryDetail = $this->find('first', array('fields' => array('id', 'store_id', 'category_id'), 'conditions' => $conditions));
        return $categoryDetail;
    }

    public function getItemIdByName($storeId = null, $itemName = null) {
        $conditions = array('LOWER(Item.name)' => strtolower($itemName), 'Item.store_id' => $storeId, 'Item.is_active' => 1, 'Item.is_deleted' => 0);
        $categoryDetail = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        return $categoryDetail;
    }

    /* ------------------------------------------------
      Function name:getitemIDBycategory()
      Description:to check item name is unique
      created:3/8/2015
      ----------------------------------------------------- */

    public function getitemIDBycategory($category = null, $storeId = null) {
        // echo $itemId;die;
        $itemid = $this->find('all', array('fields' => array('id'), 'conditions' => array('Item.store_id' => $storeId, 'Item.is_active' => 1, 'Item.is_deleted' => 0, 'category_id' => $category), 'recursive' => 2));
        // print_r($item);die;
        if ($itemid) {
            return $itemid;
        } else {
            return false;
        }
    }

    public function fetchItemList($storeId = null) {
        $itemid = $this->find('all', array('recursive' => 2, 'conditions' => array('Item.store_id' => $storeId, 'Item.is_deleted' => 0)));
        if ($itemid) {
            return $itemid;
        } else {
            return false;
        }
    }

    public function checkPreIsMandatory($itemID = null) {
        $item = $this->find('first', array('fields' => array('preference_mandatory'), 'conditions' => array('Item.id' => $itemID)));
        if ($item) {
            return $item['Item']['preference_mandatory'];
        }
        return false;
    }

    public function getItemPreferences($storeId = null, $itemId = null) {
        $typeDetail = $this->find('first', array('fields' => array('id', 'name'), 'recursive' => 3, 'conditions' => array('Item.store_id' => $storeId, 'Item.id' => $itemId, 'Item.is_active' => 1, 'Item.is_deleted' => 0)));
        return $typeDetail;
    }

    public function getItemListWithDuplicateName($category_ids = null, $merchant_id = null) {
        $iteamList = $this->find('list', array('fields' => array('name', 'name'), 'conditions' => array('category_id' => $category_ids, 'merchant_id' => $merchant_id, 'is_active' => 1, 'is_deleted' => 0), 'group' => 'name'));
        return $iteamList;
    }

    public function getItemListIds($store_id = null, $itemName = null) {
        $itemList = $this->find('first', array('fields' => array('id'), 'conditions' => array('LOWER(name)' => strtolower($itemName), 'store_id' => $store_id, 'is_active' => 1, 'is_deleted' => 0)));
        return $itemList;
    }

    public function checkItemWithId($itemId = null) {
        $conditions = array('Item.id' => $itemId);
        $itemIdDet = $this->find('first', array('fields' => array('id', 'store_id'), 'conditions' => $conditions));
        return $itemIdDet;
    }

}
