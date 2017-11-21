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
class Topping extends AppModel {
    /* public $hasMany=array(
      'ItemDefaultTopping'=>array(
      'className'=>'ItemDefaultTopping',
      'foreignKey'=>'topping_id',
      'conditions'=>array('ItemDefaultTopping.is_deleted'=>0)
      )

      ); */


    /* ------------------------------------------------
      Function name:fetchItemPrice()
      Description:To fetch the price based on Size
      created:05/8/2015
      ----------------------------------------------------- */

    public function fetchToppingPrice($itemId = null, $toppingId = null, $storeId = null) {
        if ($itemId) {
            // echo $itemId;die;
            $price = $this->find('first', array('fields' => array('price', 'name'), 'conditions' => array('Topping.item_id' => $itemId, 'Topping.id' => $toppingId, 'Topping.store_id' => $storeId, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0)));
            if ($price) {

                return $price;
            } else {
                return false;
            }
        }
    }

    /* ------------------------------------------------
      Function name:saveTopping()
      Description:To Save Topping Information
      created:04/8/2015
      ----------------------------------------------------- */

    public function saveTopping($itemData = null) {
        if ($itemData) {
            if ($this->save($itemData)) {
                return true; //Success
            } else {
                return false; // Failure 
            }
        }
    }

    /* ------------------------------------------------
      Function name:fetchToppingDetails()
      Description:To fetch the price based on Size
      created:05/8/2015
      ----------------------------------------------------- */

    public function fetchToppingDetails($toppingId = null, $storeId = null) {
        if ($toppingId) {
            // echo $itemId;die;
            $price = $this->find('first', array('conditions' => array('Topping.id' => $toppingId, 'Topping.store_id' => $storeId, 'Topping.is_deleted' => 0)));
            if ($price) {

                return $price;
            } else {
                return false;
            }
        }
    }

    /* ------------------------------------------------
      Function name:checkItemUniqueName()
      Description:to check item name is unique
      created:3/8/2015
      ----------------------------------------------------- */

    public function checkToppingUniqueName($toppingName = null, $storeId = null, $Itemid = null, $toppingid = null) {

        $conditions = array('LOWER(Topping.name)' => strtolower($toppingName), 'Topping.store_id' => $storeId, 'Topping.item_id' => $Itemid, 'Topping.is_deleted' => 0);
        if ($Itemid) {
            $conditions['Topping.id !='] = $toppingid;
        }

        $item = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($item) {
            return 0;
        } else {
            return 1;
        }
    }

    public function checkToppingexistsOnItem($toppingName = null, $storeId = null, $Itemid = null, $addonId = null) {

        $conditions = array('LOWER(Topping.name)' => strtolower($toppingName), 'Topping.store_id' => $storeId, 'Topping.item_id' => $Itemid, 'Topping.is_deleted' => 0);
        if ($addonId) {
            $conditions['Topping.addon_id'] = $addonId;
        }
        $item = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($item) {
            return 0;
        } else {
            return 1;
        }
    }

    public function getToppingById($topId = null, $itemId = null) {
        $topDetail = $this->find('first', array('conditions' => array('Topping.id' => $topId, 'Topping.item_id' => $itemId, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.is_addon_category' => 0)));
        return $topDetail;
    }

    /* ------------------------------------------------
      Function name:deleteMultipleToppings()
      Description:Delete multiple topping
      created:03/9/2015
      ----------------------------------------------------- */

    public function deleteMultipleToppings($id = null, $storeId = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Topping']['store_id'] = $storeId;
        $data['Topping']['is_deleted'] = 1;
        if (!empty($id)) {
            $filter_array = array_filter($id);
            $i = 0;
            foreach ($filter_array as $k => $orderId) {
                $data['Topping']['id'] = $orderId;
                $this->saveTopping($data);
                $i++;
            }
        }
        return $i;
    }

    /* ------------------------------------------------
      Function name:getAddons()
      Description:To fetch the Add-ons of store
      created:05/8/2015
      ----------------------------------------------------- */

    public function getAddons($storeId = null) {
        if ($storeId) {
            // echo $itemId;die;
            $list = $this->find('list', array('fields' => array('id', 'name'), 'conditions' => array('Topping.store_id' => $storeId, 'Topping.is_deleted' => 0, 'Topping.is_addon_category' => 1), 'group' => array('Topping.name')));
            if ($list) {

                return $list;
            } else {
                return false;
            }
        }
    }

    /* ------------------------------------------------
      Function name:getAddons()
      Description:To fetch the Add-ons of store
      created:05/8/2015
      ----------------------------------------------------- */

    public function getAddonsForEdit($storeId = null, $addonID = null, $categoryID = null) {
        if ($storeId && $addonID) {
            // echo $itemId;die;
            $list = $this->find('first', array('fields' => array('id', 'name'), 'conditions' => array('Topping.store_id' => $storeId, 'Topping.is_deleted' => 0, 'Topping.id' => $addonID, 'Topping.is_addon_category' => 1, 'Topping.category_id' => $categoryID)));
            if ($list) {

                return $list;
            } else {
                return false;
            }
        }
    }

    /* ------------------------------------------------
      Function name:getAddonsForListing()
      Description:To fetch the Add-ons of store
      created:05/8/2015
      ----------------------------------------------------- */

    public function getAddonsForListing($storeId = null, $addonID = null) {
        if ($storeId && $addonID) {
            $list = array();
            $addonname = $this->find('first', array('fields' => array('id', 'name'), 'conditions' => array('Topping.store_id' => $storeId, 'Topping.is_deleted' => 0, 'Topping.id' => $addonID, 'Topping.is_addon_category' => 1)));
            if ($addonname) {
                $list = $this->find('all', array('fields' => array('id'), 'conditions' => array('Topping.store_id' => $storeId, 'Topping.is_deleted' => 0, 'Topping.name' => $addonname['Topping']['name'], 'Topping.is_addon_category' => 1)));
            }
            if ($list) {
                return $list;
            } else {
                return false;
            }
        }
    }

    /* ------------------------------------------------
      Function name:getAddons()
      Description:To fetch the Add-ons of store
      created:05/8/2015
      ----------------------------------------------------- */

    public function getAddonsByCategory($storeId = null, $categoryid = null) {
        if ($storeId && $categoryid) {
            // echo $itemId;die;
            $list = $this->find('list', array('fields' => array('id', 'name'), 'conditions' => array('Topping.store_id' => $storeId, 'Topping.is_deleted' => 0, 'Topping.category_id' => $categoryid, 'Topping.is_addon_category' => 1), 'group' => array('Topping.name')));
            if ($list) {

                return $list;
            } else {
                return false;
            }
        }
    }

    /* ------------------------------------------------
      Function name:checkSubaddonmUniqueName()
      Description:to check sub add-ons unique name
      created:22/09/2015
      ----------------------------------------------------- */

    public function checkSubaddonmUniqueName($subtoppingName = null, $toppingName = null, $storeId = null, $subtoppingId = null, $itemId = null) {

        $conditions = array('LOWER(Topping.name)' => strtolower($subtoppingName), 'Topping.store_id' => $storeId, 'Topping.addon_id' => $toppingName, 'Topping.is_deleted' => 0);
        if ($subtoppingId) {
            $conditions['Topping.id !='] = $subtoppingId;
        }
        if ($itemId) {
            $conditions['Topping.item_id'] = $itemId;
        }
        $item = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($item) {
            return 0;
        } else {
            return 1;
        }
    }

    public function getToppingByName($storeId = null, $topName = null) {
        $conditions = array('LOWER(Topping.name)' => strtolower($topName), 'Topping.store_id' => $storeId, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0);
        $categoryDetail = $this->find('first', array('fields' => array('id', 'item_id'), 'conditions' => $conditions));
        return $categoryDetail;
    }

    public function getToppingByNameCategory($storeId = null, $topName = null, $categoryId = null, $itemID = null) {
        $conditions = array('LOWER(Topping.name)' => strtolower($topName), 'Topping.store_id' => $storeId, 'Topping.category_id' => $categoryId, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0);
        if ($itemID) {
            $conditions['Topping.item_id'] = $itemID;
        }
        $categoryDetail = $this->find('first', array('fields' => array('id', 'item_id'), 'conditions' => $conditions));
        return $categoryDetail;
    }

    /* ------------------------------------------------
      Function name:getitemIDBycategory()
      Description:to check item name is unique
      created:3/8/2015
      ----------------------------------------------------- */

    public function getItemsBytoppingID($toppingID = null, $storeId = null, $categoryId = null) {
        // echo $itemId;die;                       
        $ToppingData = $this->find('first', array('fields' => array('Topping.id', 'Topping.name'), 'conditions' => array('Topping.id' => $toppingID)));
        $itemid = array();
        if ($ToppingData) {
            $itemid = $this->find('all', array('fields' => array('item_id', 'id'), 'conditions' => array('Topping.store_id' => $storeId, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.name' => $ToppingData['Topping']['name'], 'Topping.category_id' => $categoryId), 'recursive' => 2));
        }
        // print_r($item);die;
        if ($itemid) {
            return $itemid;
        } else {
            return false;
        }
    }

    /* ------------------------------------------------
      Function name:getAddOnID()
      Description:to get addOnid
      created:3/8/2015
      ----------------------------------------------------- */

    public function getAddOnID($itemID = null, $toppingID = null, $storeId = null) {
        // echo $itemId;die;                       
        $ToppingData = $this->find('first', array('fields' => array('Topping.id', 'Topping.name'), 'conditions' => array('Topping.id' => $toppingID)));
        $itemid = array();
        if ($ToppingData) {
            $itemid = $this->find('first', array('fields' => array('item_id', 'id'), 'conditions' => array('Topping.store_id' => $storeId, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.name' => $ToppingData['Topping']['name'], 'Topping.item_id' => $itemID), 'recursive' => 2));
        }
        // print_r($item);die;
        if ($itemid) {
            return $itemid;
        } else {
            return false;
        }
    }

    public function findAddonList($storeId = null) {
        $categoryList = $this->find('all', array('recursive' => 2, 'conditions' => array('Topping.store_id' => $storeId, 'Topping.is_deleted' => 0, 'Topping.is_addon_category' => 1)));
        if ($categoryList) {
            return $categoryList;
        }
    }

    public function findsubAddonList($storeId = null) {
        $categoryList = $this->find('all', array('recursive' => 2, 'conditions' => array('Topping.store_id' => $storeId, 'Topping.is_deleted' => 0, 'Topping.is_addon_category' => 0)));
        if ($categoryList) {
            return $categoryList;
        }
    }

    /* ------------------------------------------------
      Function name:getItemsbyAddoncategory()
      Description:get Items by Addon category
      created:3/8/2015
      ----------------------------------------------------- */

    public function getItemsbyAddoncategory($toppingCategoryID = null, $storeId = null, $catgeoryId = null) {
        // echo $itemId;die;                       
        $ToppingData = $this->find('first', array('fields' => array('Topping.id', 'Topping.name'), 'conditions' => array('Topping.id' => $toppingCategoryID)));
        $this->bindModel(
                array(
            'belongsTo' => array(
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id',
                    'fields' => array('Item.id', 'Item.name'),
                    'type' => 'inner',
                    'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                ),
            )
                ), false
        );

        if ($catgeoryId) {
            $this->bindModel(
                    array(
                'belongsTo' => array(
                    'Category' => array(
                        'className' => 'Category',
                        'foreignKey' => 'category_id',
                        'fields' => array('Category.id', 'Category.name'),
                        'type' => 'inner',
                        'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1, 'Category.id' => $catgeoryId),
                    ),
                )
                    ), false
            );
        }



        $itemid = array();
        if ($ToppingData) {
            $itemid = $this->find('all', array('fields' => array('Topping.item_id', 'Topping.id', 'Topping.name'), 'conditions' => array('Topping.store_id' => $storeId, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.name' => $ToppingData['Topping']['name']), 'recursive' => 2));
        }
        // print_r($item);die;
        if ($itemid) {
            return $itemid;
        } else {
            return false;
        }
    }

    public function checkAddonByCategory($categoryId = null, $storeId = null, $addonId = null) {
        $result = $this->find('first', array('conditions' => array('Topping.id' => $addonId, 'Topping.category_id' => $categoryId, 'Topping.store_id' => $storeId)));
        return $result;
    }

    public function ChecksubtoppingonItem($toppingid = null, $itemid = null, $addonid = null, $subToppingname = null) {
        $flag = 0;
        $addonname = $this->find('first', array('fields' => array('Topping.name'), 'conditions' => array('Topping.id' => $addonid, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0)));
        //pr($addonname);
        if ($addonname) {
            $addonTopping = $this->find('first', array('fields' => array('Topping.id'), 'conditions' => array('LOWER(Topping.name)' => strtolower($addonname['Topping']['name']), 'Topping.item_id' => $itemid, 'Topping.is_addon_category' => 1, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0)));
            //pr($addonTopping);
            if ($addonTopping) {
                $subaddonTopping = $this->find('first', array('fields' => array('Topping.id'), 'conditions' => array('Topping.addon_id' => $addonTopping['Topping']['id'], 'Topping.item_id' => $itemid, 'LOWER(Topping.name)' => strtolower($subToppingname), 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.id !=' => $toppingid)));            
                
                if ($subaddonTopping) {
                    $flag = 1;
                }
            }
        }
        return $flag;
        //pr($subaddonTopping);
        //die;
    }

    /* ------------------------------------------------
      Function name:fetchToppingDetails()
      Description:To fetch the price based on Size
      created:05/8/2015
      ----------------------------------------------------- */

    public function getToppingitemID($toppingId = null) {
        if ($toppingId) {
            $toppingDetails = $this->find('first', array('conditions' => array('Topping.id' => $toppingId, 'Topping.is_deleted' => 0)));
            if ($toppingDetails) {
                return $toppingDetails;
            } else {
                return false;
            }
        }
    }

    public function checkToppingWithId($toppingId = null) {
        $conditions = array('Topping.id' => $toppingId);
        $toppingIdDet = $this->find('first', array('fields' => array('id', 'store_id'), 'conditions' => $conditions));
        return $toppingIdDet;
    }

    public function getToppingListWithDuplicateName($category_ids = null, $merchant_id = null) {
        $iteamList = $this->find('list', array('fields' => array('name', 'name'), 'conditions' => array('category_id' => $category_ids, 'merchant_id' => $merchant_id, 'is_active' => 1, 'is_deleted' => 0), 'group' => 'name'));
        return $iteamList;
    }
    
    
    public function fetchToppingservice($itemId = null,$storeId = null) {
        if ($itemId) {
            // echo $itemId;die;
            $price = $this->find('all', array('recursive'=>3,'fields' => array('id','price','item_id','name','category_id','is_addon_category','addon_id','no_size','size_id'), 'conditions' => array('Topping.item_id' => $itemId,'Topping.store_id' => $storeId, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.is_addon_category' => 1)));
            if ($price) {
                return $price;
            } else {
                return false;
            }
        }
    }

}
