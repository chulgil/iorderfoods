<?php

App::uses('HqAppController', 'Controller');

class HqtoppingsController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'Form', 'Common');
    public $uses = array('Store', 'Topping', 'Item', 'ItemPrice', 'ItemType', 'Size', 'Category', 'ItemDefaultTopping');
    public $layout = 'hq_dashboard';

    public function beforeFilter() {
        parent::beforeFilter();
    }

    private function _saveToppings($merchant_id = null, $temp = null, $postData = null) {
        $storeID = $postData['Topping']['store_id'];
        $toppingdata = array();
        $toppingName = trim($postData['Topping']['name']);
        $topping = 0;
        $successToppingName = '';
        $failedToppingName = '';
        $categoryID = $postData['Category']['id'];
        $itemIds = $postData['Topping']['item_id'];
        foreach ($itemIds as $itemId) {
            if ($temp == 'All') {
                $itemList = $this->Item->getItemListIds($storeID, $itemId);
                $itemId = @$itemList["Item"]["id"];
            }
            if (!empty($itemId) && $this->Topping->checkToppingUniqueName($toppingName, $storeID, $itemId)) {
                $toppingdata['name'] = trim($toppingName);
                $toppingdata['item_id'] = $itemId;
                $toppingdata['is_active'] = $postData['Topping']['is_active'];
                $toppingdata['price'] = 0;
                $toppingdata['store_id'] = $storeID;
                $toppingdata['merchant_id'] = $merchant_id;
                $toppingdata['category_id'] = $categoryID;
                $toppingdata['min_value'] = $postData['Topping']['min_value'];
                $toppingdata['max_value'] = $postData['Topping']['max_value'];
                $this->Topping->create();
                $topping = $this->Topping->saveTopping($toppingdata);
                $this->Item->getItemName($itemId, $storeID);
                $itemNamesuccess = $this->Item->getItemName($itemId, $storeID);
                if ($successToppingName == '') {
                    $successToppingName.=$itemNamesuccess['Item']['name'];
                } else {
                    $successToppingName.=',' . $itemNamesuccess['Item']['name'];
                }
            } else {
                $itemNamefailed = $this->Item->getItemName($itemId, $storeID);
                if ($failedToppingName == '') {
                    $failedToppingName.=$itemNamefailed['Item']['name'];
                } else {
                    $failedToppingName.=',' . $itemNamefailed['Item']['name'];
                }
            }
        }
        $message = '';
        if ($successToppingName) {
            $message.="Add-on for Item " . $successToppingName . " has been successfully created";
        }

        if ($failedToppingName) {
            $message.="<br> Add-on for Item " . $failedToppingName . " already exists";
        }

        if ($message) {
            $postData = '';
            $this->Session->setFlash(__($message), 'alert_success');
        } else {
            $this->Session->setFlash(__("Some Problem occured"), 'alert_failed');
        }
    }

    public function index() {
        $merchant_id = $this->Session->read('merchantId');
        if ($this->request->is('post') && $this->request->data && !empty($this->request->data['Topping']['name'])) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $itemIds = $this->request->data['Topping']['item_id'];
            if (!empty($itemIds)) {
                $itemCount = count($itemIds);
            } else {
                $itemCount = 0;
            }
            if ($itemCount > 0) {
                if ($this->request->data['Topping']['store_id'] == 'All') {
                    $storeData = $this->Store->getAllStoreByMerchantId($merchant_id);
                    $categoryName = $this->request->data['Category']['id'];
                    foreach ($storeData as $store) {
                        $this->request->data['Topping']['store_id'] = $store['Store']['id'];
                        $categoryData = $this->Category->find('first', array('fields' => 'id', 'conditions' => array('name' => strtolower($categoryName), 'store_id' => $store['Store']['id'], 'is_deleted' => 0, 'is_active' => 1), 'recursive' => -1));
                        if (!empty($categoryData)) {
                            $this->request->data['Category']['id'] = $categoryData['Category']['id'];
                            $this->_saveToppings($merchant_id, 'All', $this->request->data);
                        }
                    }
                    $this->request->data = '';
                } else {
                    $this->_saveToppings($merchant_id, null, $this->request->data);
                }
            } else {
                $this->Session->setFlash(__("Please select item."), 'alert_failed');
            }
        }

        $itempost = 0;
        $itemList = '';
        if (isset($this->request->data['Topping']['item_id'])) {
            $itempost = 1;
        }
        if (isset($this->request->data['Topping']['item_id'])) {
            $storeID = $this->request->data['Topping']['store_id'];
            $itemList = $this->Item->getItemsByCategory($this->request->data['Category']['id'], $storeID);
        }
        $this->set('itemList', $itemList);
        if (isset($this->request->data['Category']['id'])) {
            $storeID = $this->request->data['Topping']['store_id'];
            $categoryList = $this->Category->getCategoryListHasTopping($storeID);
            $this->set('categoryList', $categoryList);
        }
        $this->set('itempost', $itempost);
        $this->_getAddonsList();
    }

    /* ------------------------------------------------
      Function name:itemsBycategory()
      Description:get items by category
      created:6/8/2015
      ----------------------------------------------------- */

    public function itemsByCategory($categoryId = null, $storeID = null) {
        $itemList = '';
        if ($categoryId && $storeID) {
            if ($storeID == 'All') {
                $merchant_id = $this->Session->read('merchantId');
                $cData = $this->Category->find('list', array('conditions' => array('merchant_id' => $merchant_id, 'name' => strtolower($categoryId), 'is_active' => 1, 'is_deleted' => 0), 'fields' => array('id')));
                $itemList = $this->Item->getItemListWithDuplicateName($cData, $merchant_id);
            } else {
                $itemList = $this->Item->getItemsByCategory($categoryId, $storeID);
            }
        }
        $this->set('itemList', $itemList);
    }

    public function getCategory() {
        if ($this->request->is('ajax') && $this->request->data['storeId']) {
            $this->loadModel('Category');
            if ($this->request->data['storeId'] == 'All') {
                $merchant_id = $this->Session->read('merchantId');
                $categoryList = $this->Category->getCategoryListWithDuplicateNameHasToppings($merchant_id);
            } else {
                $categoryList = $this->Category->getCategoryListHasTopping($this->request->data['storeId']);
            }
            $this->set('categoryList', $categoryList);
        } else {
            exit;
        }
    }

    /* ------------------------------------------------
      Function name:index()
      Description:List Menu Items
      created:5/8/2015
      ----------------------------------------------------- */

    private function _getAddonsList($clearAction = null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $storeID = @$this->request->data['Topping']['storeId'];
        if (empty($storeID) && $this->Session->read('HqToppingSearchData')) {
            $data = json_decode($this->Session->read('HqToppingSearchData'), true);
            if (!empty($data['Topping']['storeId'])) {
                $storeID = $data['Topping']['storeId'];
            }
        }
        $merchant_id = $this->Session->read('merchantId');
        $value = "";
        $criteria = "Topping.merchant_id =$merchant_id AND Topping.is_deleted=0 AND Topping.is_addon_category =1";
        if (!empty($storeID)) {
            $criteria .= " AND Topping.store_id =$storeID";
        }
        $order = '';
        $pagingFlag = true;

        if ($this->Session->read('HqToppingSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqToppingSearchData'), true);
        } else {
            $this->Session->delete('HqToppingSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }

        if (!empty($this->request->data)) {
            if (isset($this->request->data['Topping']['no']) && !empty($this->request->data['Topping']['no']) && array_filter($this->request->data['Topping']['no'])) {
                $result = $this->Topping->deleteMultipleToppings($this->request->data['Topping']['no'], $storeID);
                $del = $result . "  " . "topping deleted successfully.";
                $this->Session->setFlash(__($del), 'alert_success');
            }
            $this->Session->write('HqToppingSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['Topping']['keyword'])) {
                $value = trim($this->request->data['Topping']['keyword']);
                $criteria .= " AND (Topping.name LIKE '%" . $value . "%' OR Item.name LIKE '%" . $value . "%')";
            }
            if (!empty($this->request->data['Topping']['itemId'])) {
                $ItemID = trim($this->request->data['Topping']['itemId']);
                $criteria .= " AND (Topping.item_id =$ItemID)";
                $order = 'Topping.position ASC';
                $pagingFlag = false;
            }
            if (!empty($this->request->data['Topping']['isActive'])) {
                $active = trim($this->request->data['Topping']['isActive']);
                $criteria .= " AND (Topping.is_active =$active)";
            }
        }

        $this->Topping->bindModel(
                array(
            'belongsTo' => array(
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id',
                    'type' => 'inner',
                    'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                    'fields' => array('id', 'name')
                ), 'Category' => array(
                    'className' => 'Category',
                    'foreignKey' => 'category_id',
                    'type' => 'inner',
                    'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                    'fields' => array('id', 'name')
                ), "Store" => array('className' => 'Store',
                    'foreignKey' => 'store_id',
                    'type' => 'inner',
                    'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1),
                    'fields' => array('store_name'))
            ),
            'hasMany' => array(
                'ItemDefaultTopping' => array(
                    'className' => 'ItemDefaultTopping',
                    'foreignKey' => 'topping_id',
                    'conditions' => array('ItemDefaultTopping.is_deleted' => 0, 'ItemDefaultTopping.is_active' => 1),
                    'fields' => array('id', 'topping_id', 'item_id')
                )
            )
                ), false
        );
        if ($order == '') {
            $order = 'Topping.created DESC';
        }

        $toppingdetail = '';
        if ($pagingFlag) {
            $this->paginate = array('conditions' => array($criteria), 'order' => $order);
            $toppingdetail = $this->paginate('Topping');
        } else {
            $toppingdetail = $this->Topping->find('all', array('conditions' => array($criteria), 'order' => $order));
        }
        $this->set('list', $toppingdetail);
        $this->set('pagingFlag', $pagingFlag);
        $nList = array();
        if (!empty($storeID)) {
//        $this->loadModel('Category');
//        $itemList = $this->Item->getallItemsByStore($storeID);
            $itemList = $this->Topping->find('all', array('conditions' => array('Topping.store_id' => $storeID, 'Topping.is_deleted' => 0, 'Topping.is_addon_category' => 1), 'group' => array('Topping.item_id')));
            if (!empty($itemList)) {
                foreach ($itemList as $iList) {
                    if (!empty($iList['Item']) && !empty($iList['Category'])) {
                        $nList[$iList['Item']['id']] = $iList['Item']['name'];
                    }
                }
            }
        }
        $this->set('itemList', $nList);
        $this->set('keyword', $value);
    }

    /* ------------------------------------------------
      Function name:activateTopping()
      Description:Active/deactive Topping
      created:5/8/2015
      ----------------------------------------------------- */

    public function activateTopping($EncryptedtoppingID = null, $status = 0) {
        $this->autoRender = false;
        $toppingid = $this->Encryption->decode($EncryptedtoppingID);
        $data['Topping']['merchant_id'] = $this->Session->read('merchantId');
        $data['Topping']['id'] = $toppingid;
        $data['Topping']['is_active'] = $status;
        if ($this->Topping->saveTopping($data)) {
            if ($status) {
                $SuccessMsg = "Add-on Activated";
            } else {
                $SuccessMsg = "Add-on Deactivated and Add-on will not available at Add-on List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hqtoppings', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqtoppings', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteTopping()
      Description:Delete Topping
      created:5/8/2015
      ----------------------------------------------------- */

    public function deleteTopping($EncryptedtoppingID = null) {
        $this->autoRender = false;
        $toppingid = $this->Encryption->decode($EncryptedtoppingID);
        $data['Topping']['merchant_id'] = $this->Session->read('merchantId');
        $data['Topping']['id'] = $toppingid;
        $data['Topping']['is_deleted'] = 1;
        if ($this->Topping->saveTopping($data)) {
            $this->Session->setFlash(__("Add-on deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqtoppings', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqtoppings', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:editTopping()
      Description:Edit Item Toppings
      created:5/8/2015
      ----------------------------------------------------- */

    public function editTopping($EncryptedToppingID = null) {
        $merchant_id = $this->Session->read('merchantId');
        $toppingId = $this->Encryption->decode($EncryptedToppingID);
        $toppingsDetails = $this->Topping->getToppingitemID($toppingId);
        if (empty($toppingsDetails)) {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect($this->referer());
        }
        $storeId = $toppingsDetails['Topping']['store_id'];
        if ($this->request->is('post') || $this->request->is('put')) {
            if (empty($this->request->data['Topping']['item_id'])) {
                $this->Session->setFlash(__("Please select item."), 'alert_failed');
                $this->redirect($this->referer());
            }
            $this->request->data = $this->Common->trimValue($this->request->data);
            $toppingName = trim($this->request->data['Topping']['name']);
            $itemId = trim($this->request->data['Topping']['item_id']);
            $toppingId = trim($this->request->data['Topping']['id']);
            $categoryID = $this->request->data['Category']['id'];
            if ($this->Topping->checkToppingUniqueName($toppingName, $storeId, $itemId, $toppingId)) {
                $toppingdata['name'] = trim($toppingName);
                $toppingdata['item_id'] = $itemId;
                if ($this->request->data['Topping']['is_active']) {
                    $toppingdata['is_active'] = 1;
                } else {
                    $toppingdata['is_active'] = 0;
                }
                $toppingdata['price'] = 0;
                $toppingdata['store_id'] = $storeId;
                $toppingdata['merchant_id'] = $merchant_id;
                $toppingdata['category_id'] = $categoryID;
                $toppingdata['id'] = $toppingId;
                $toppingdata['min_value'] = $this->request->data['Topping']['min_value'];
                $toppingdata['max_value'] = $this->request->data['Topping']['max_value'];
                $this->Topping->saveTopping($toppingdata);
                $this->Session->setFlash(__("Add-on Details Updated"), 'alert_success');
                $this->redirect(array('controller' => 'hqtoppings', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("Add-on Name Already Exists for Item"), 'alert_failed');
            }
        }
        $categoryDetails = $this->Item->getcategoryByitemID($toppingsDetails['Topping']['item_id'], $storeId);
        $this->request->data['Category']['id'] = $categoryDetails['Item']['category_id'];
        if (isset($this->request->data['Topping']['name']) && $this->request->data['Topping']['name']) {
            $toppingsDetails['Topping']['name'] = $this->request->data['Topping']['name'];
        }
        $this->request->data['Topping'] = $toppingsDetails['Topping'];
        $itempost = 0;
        if (isset($this->request->data['Topping']['item_id'])) {
            $itempost = 1;
        }
        if (isset($this->request->data['Topping']['item_id'])) {
            $itemList = $this->Item->getItemsByCategory($this->request->data['Category']['id'], $storeId);
        }
        $categoryList = $this->Category->getCategoryListHasTopping($storeId);
        $this->set('categoryList', $categoryList);
        $this->set('itemList', $itemList);
        $this->set('itempost', $itempost);
    }

    public function itemsByCategoryId($categoryId = null, $storeID = null) {
        $itemList = '';
        if ($categoryId && $storeID) {
            $itemList = $this->Item->getItemsByCategory($categoryId, $storeID);
        }
        $this->set('itemList', $itemList);
    }

    private function _saveSubToppings($merchant_id = null, $temp = null, $postdata = null) {
        $this->request->data = $postdata;
        $storeId = $this->request->data['Store']['store_id'];
        if ($temp == 'All') {
            $addonData = $this->Topping->find('first', array('fields' => array('Topping.id'), 'conditions' => array('Topping.store_id' => $storeId, 'LOWER(Topping.name)' => strtolower($this->request->data['Topping']['id']), 'Topping.category_id' => $this->request->data['Category']['id'], 'Topping.is_addon_category' => 1)));
            if (!empty($addonData)) {
                $this->Topping->bindModel(
                        array(
                    'belongsTo' => array(
                        'Item' => array(
                            'className' => 'Item',
                            'foreignKey' => 'item_id',
                            'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                            'type' => 'inner',
                            'fields' => array('id', 'name')
                        ),
                    )
                        ), false
                );
                $toppingData = $this->Topping->getItemsBytoppingID($addonData['Topping']['id'], $storeId, $this->request->data['Category']['id']);
                foreach ($toppingData as $key => $value) {
                    if (in_array($value['Item']['name'], $this->request->data['Topping']['item_id']) && $this->Topping->checkToppingexistsOnItem(trim($this->request->data['Topping']['name']), $storeId, $value['Topping']['item_id'], $value['Topping']['id'])) {
                        $data['Topping']['item_id'] = $value['Topping']['item_id'];
                        $data['Topping']['store_id'] = $storeId;
                        $data['Topping']['merchant_id'] = $merchant_id;
                        $data['Topping']['is_active'] = $this->request->data['Topping']['is_active'];
                        $data['Topping']['name'] = trim($this->request->data['Topping']['name']);
                        $data['Topping']['price'] = $this->request->data['Topping']['price'];
                        $data['Topping']['addon_id'] = $value['Topping']['id'];
                        $data['Topping']['category_id'] = $this->request->data['Category']['id'];
                        $data['Topping']['is_addon_category'] = 0;
                        if (!empty($this->request->data['Topping']['no_size'])) {
                            $data['Topping']['no_size'] = $this->request->data['Topping']['no_size'];
                        } else {
                            $data['Topping']['no_size'] = 0;
                        }
                        $this->Topping->create();
                        $topping = $this->Topping->saveTopping($data);
                        $subtoppingID = $this->Topping->getInsertID();
                        if ($subtoppingID && $this->request->data['Topping']['defaultcheck']) {
                            $defaulttoppingdata['topping_id'] = $subtoppingID;
                            $defaulttoppingdata['store_id'] = $storeId;
                            $defaulttoppingdata['merchant_id'] = $merchant_id;
                            $defaulttoppingdata['item_id'] = $value['Topping']['item_id'];
                            $defaulttoppingdata['is_deleted'] = 0;
                            $defaulttoppingdata['id'] = '';
                            $this->ItemDefaultTopping->saveDefaultTopping($defaulttoppingdata);
                        }
                    }
                }
            }
        } else {
            $toppingData = $this->Topping->getItemsBytoppingID($this->request->data['Topping']['id'], $storeId, $this->request->data['Category']['id']);
            foreach ($toppingData as $key => $value) {
                if (in_array($value['Topping']['item_id'], $this->request->data['Topping']['item_id']) && $this->Topping->checkToppingexistsOnItem(trim($this->request->data['Topping']['name']), $storeId, $value['Topping']['item_id'], $value['Topping']['id'])) {
                    $data['Topping']['item_id'] = $value['Topping']['item_id'];
                    $data['Topping']['store_id'] = $storeId;
                    $data['Topping']['merchant_id'] = $merchant_id;
                    $data['Topping']['is_active'] = $this->request->data['Topping']['is_active'];
                    $data['Topping']['name'] = trim($this->request->data['Topping']['name']);
                    $data['Topping']['price'] = $this->request->data['Topping']['price'];
                    $data['Topping']['addon_id'] = $value['Topping']['id'];
                    $data['Topping']['category_id'] = $this->request->data['Category']['id'];
                    $data['Topping']['is_addon_category'] = 0;
                    if (!empty($this->request->data['Topping']['no_size'])) {
                        $data['Topping']['no_size'] = $this->request->data['Topping']['no_size'];
                    } else {
                        $data['Topping']['no_size'] = 0;
                    }
                    $this->Topping->create();
                    $topping = $this->Topping->saveTopping($data);
                    $subtoppingID = $this->Topping->getInsertID();
                    if ($subtoppingID && $this->request->data['Topping']['defaultcheck']) {
                        $defaulttoppingdata['topping_id'] = $subtoppingID;
                        $defaulttoppingdata['store_id'] = $storeId;
                        $defaulttoppingdata['merchant_id'] = $merchant_id;
                        $defaulttoppingdata['item_id'] = $value['Topping']['item_id'];
                        $defaulttoppingdata['is_deleted'] = 0;
                        $defaulttoppingdata['id'] = '';
                        $this->ItemDefaultTopping->saveDefaultTopping($defaulttoppingdata);
                    }
                }
            }
            $message = '';
            if ($successToppingName) {
                $message.="Add-on for Item " . $successToppingName . " has been successfully created";
            }

            if ($failedToppingName) {
                $message.="<br> Add-on for Item " . $failedToppingName . " already exists";
            }

            if ($message) {
                $this->request->data = '';
                $this->Session->setFlash(__($message), 'alert_success');
            } else {
                $this->Session->setFlash(__("Some Problem occured"), 'alert_failed');
            }
        }
    }

    /* ------------------------------------------------
      Function name:addSubTopping()
      Description:Add sub toppings
      created:5/8/2015
      ----------------------------------------------------- */

    public function subTopping() {
        $storeId = @$this->request->data['Topping']['store_id'];
        $merchant_id = $this->Session->read('merchantId');
        if ($this->request->is('post') && !empty($this->request->data['Topping']['name'])) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            if (!empty($this->request->data['Topping']['item_id'])) {
                if ($this->request->data['Topping']['store_id'] == 'All') {
                    $storeData = $this->Store->getAllStoreByMerchantId($merchant_id);
                    $categoryName = $this->request->data['Category']['id'];
                    if (!empty($storeData)) {
                        foreach ($storeData as $store) {
                            $this->request->data['Store']['store_id'] = $store['Store']['id'];
                            $categoryData = $this->Category->find('first', array('fields' => 'id', 'conditions' => array('name' => strtolower($categoryName), 'store_id' => $store['Store']['id'], 'is_deleted' => 0, 'is_active' => 1), 'recursive' => -1));
                            if (!empty($categoryData)) {
                                $this->request->data['Category']['id'] = $categoryData['Category']['id'];
                                $this->_saveSubToppings($merchant_id, 'All', $this->request->data);
                            }
                        }
                        $this->request->data = '';
                        $this->Session->setFlash(__("Item Successfully Created"), 'alert_success');
                    }
                } else {
                    $this->_saveSubToppings($merchant_id, null, $this->request->data);
                }
            } else {
                $this->Session->setFlash(__("Please select item"), 'alert_failed');
            }
        }
        $this->loadModel('Topping');
        $addonList = $this->Topping->getAddons($storeId);
        $this->set('addonList', $addonList);
        $categoryList = $this->Category->getCategoryListHasTopping($storeId);
        $this->set('categoryList', $categoryList);
        $addonpost = 0;
        $this->set('addonpost', $addonpost);
        $this->_listSubTopping();
    }

    /* ------------------------------------------------
      Function name:itemsBycategory()
      Description:get items by category
      created:6/8/2015
      ----------------------------------------------------- */

    public function addonByCategory($categoryId = null, $storeID = null) {
        $addonList = '';
        if ($categoryId && $storeID) {
            if ($storeID == 'All') {
                $merchant_id = $this->Session->read('merchantId');
                $cData = $this->Category->find('list', array('conditions' => array('merchant_id' => $merchant_id, 'name' => strtolower($categoryId), 'is_active' => 1, 'is_deleted' => 0), 'fields' => array('id')));
                $addonList = $this->Topping->getToppingListWithDuplicateName($cData, $merchant_id);
            } else {
                $addonList = $this->Topping->getAddonsByCategory($storeID, $categoryId);
            }
        }
        $this->set('addonList', $addonList);
    }

    public function getItemsByAddonCategoryIdMultiple($toppingID = null, $categoryID = null, $storeID = null) {
        if ($storeID == 'All') {
            $merchant_id = $this->Session->read('merchantId');
            $catData = $this->Category->find('list', array('fields' => array('id'), 'conditions' => array('merchant_id' => $merchant_id, 'LOWER(name)' => strtolower($categoryID), 'is_active' => 1, 'is_deleted' => 0)));
            //$topData = $this->Topping->find('list', array('fields' => array('item_id'), 'conditions' => array('merchant_id' => $merchant_id, 'LOWER(name)' => strtolower($toppingID), 'is_active' => 1, 'is_deleted' => 0)));
            $Itemslist = $this->Item->find('list', array('fields' => array('name', 'name'), 'conditions' => array('merchant_id' => $merchant_id, 'category_id' => $catData, 'is_active' => 1, 'is_deleted' => 0)));
            $this->set('Itemslist', $Itemslist);
        } else {
            $Items = array();
            $Itemslist = array();
            if ($toppingID) {
                $this->Topping->bindModel(
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
                $Items = $this->Topping->getItemsbyAddoncategory($toppingID, $storeID);
            }
            if ($Items) {
                foreach ($Items as $value) {
                    if (!empty($value['Item'])) {
                        $Itemslist[$value['Item']['id']] = $value['Item']['name'];
                    }
                }
            }
            $this->set('Itemslist', $Itemslist);
        }
    }

    /* ------------------------------------------------
      Function name:listSubTopping()
      Description:List of sub Add-ons
      created:5/8/2015
      ----------------------------------------------------- */

    public function _listSubTopping($clearAction = null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $storeID = @$this->request->data['Topping']['storeId'];
        if (empty($storeID) && $this->Session->read('HqToppingSearchData')) {
            $data = json_decode($this->Session->read('HqToppingSearchData'), true);
            if (!empty($data['Topping']['storeId'])) {
                $storeID = $data['Topping']['storeId'];
            }
        }
        $merchant_id = $this->Session->read('merchantId');
        /*         * ****start******* */
        $value = "";
        $criteria = "Topping.merchant_id =$merchant_id AND Topping.is_deleted=0 AND Topping.is_addon_category=0";
        $order = '';
        $pagingFlag = true;
        $addOnsCriteria = "Topping.merchant_id =$merchant_id AND Topping.is_deleted=0 AND Topping.is_addon_category=1";
        $addOnsCriteriaAdditional = '';
        if (!empty($storeID)) {
            $criteria .= " AND Topping.store_id =$storeID";
            $addOnsCriteria .= " AND Topping.store_id =$storeID";
        }
        if ($this->Session->read('HqToppingSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqToppingSearchData'), true);
        } else {
            $this->Session->delete('HqToppingSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }

        if (!empty($this->request->data)) {
            $this->Session->write('HqToppingSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['Topping']['addonid'])) {
                $toppingID = $this->request->data['Topping']['addonid'];
                if ($toppingID) {
                    $Toppingname = $this->Topping->getAddonsForListing($storeID, $toppingID);
                    if ($Toppingname) {
                        foreach ($Toppingname as $key => $value) {
                            $ids[] = $value['Topping']['id'];
                        }
                        $toppingIDs = implode(',', $ids);
                        $criteria .= " AND (Topping.addon_id IN (" . $toppingIDs . "))";
                    }
                }
            }
            if (!empty($this->request->data['Topping']['search'])) {
                $search = trim($this->request->data['Topping']['search']);
                $criteria .= " AND (Topping.name LIKE '%" . $search . "%')";
            }
            if (!empty($this->request->data['Topping']['categoryId'])) {
                $categoryid = $this->request->data['Topping']['categoryId'];
                if ($categoryid) {
                    $criteria .= " AND (Topping.category_id = $categoryid)";
                    $addOnsCriteriaAdditional = " AND (Topping.category_id = $categoryid)";
                }
            }
            if (!empty($this->request->data['Topping']['add_ons_id'])) {
                $addOnsId = $this->request->data['Topping']['add_ons_id'];
                if ($addOnsId) {
                    $criteria .= " AND (Topping.addon_id = $addOnsId)";
                    $order = 'Topping.position ASC';
                    $pagingFlag = false;
                }
            }
            if (isset($this->request->data['Topping']['isActive']) && $this->request->data['Topping']['isActive'] != '') {
                $active = trim($this->request->data['Topping']['isActive']);
                $criteria .= " AND (Topping.is_active =$active)";
            }
            $ItemID = "";
            if (!empty($this->request->data['Topping']['itemId'])) {
                $ItemID = trim($this->request->data['Topping']['itemId']);
                $criteria .= " AND (Topping.item_id =$ItemID)";
                $addOnsCriteriaAdditional = " AND (Topping.item_id =$ItemID)";
                $pagingFlag = false;
            }

            if (isset($this->request->data['Topping']['no']) && $this->request->data['Topping']['no'] && isset($this->request->data['subaddondelete'])) {
                if (array_filter($this->request->data['Topping']['no'])) {
                    $result = $this->Topping->deleteMultipleToppings($this->request->data['Topping']['no'], $storeID);
                    $del = $result . "  " . "topping deleted successfully.";
                    $this->Session->setFlash(__($del), 'alert_success');
                }
            }
            if ($this->request->data) {
                if ($ItemID) {
                    if (isset($this->request->data['set'])) {            // Set Default Toppings
                        $ToppingId = $this->request->data['Topping']['id'];
                        if ($this->ItemDefaultTopping->deleteallDefaultTopping($ItemID, null)) {
                            foreach ($ToppingId as $key => $topid) {
                                if ($topid) {
                                    $deafulttoppingId = $this->ItemDefaultTopping->defaultToppingExits($topid);
                                    if ($deafulttoppingId) {
                                        $defaulttoppingdata['id'] = $deafulttoppingId['ItemDefaultTopping']['id'];
                                    } else {
                                        $defaulttoppingdata['id'] = '';
                                    }
                                    $defaulttoppingdata['topping_id'] = $topid;
                                    $defaulttoppingdata['store_id'] = $storeID;
                                    $defaulttoppingdata['merchant_id'] = $merchant_id;
                                    $defaulttoppingdata['item_id'] = $ItemID;
                                    $defaulttoppingdata['is_deleted'] = 0;
                                    $this->ItemDefaultTopping->saveDefaultTopping($defaulttoppingdata);
                                }
                            }
                            $this->Session->setFlash(__("Add-ons are successfully assigned as default to Item"), 'alert_success');
                        }
                    }
                    if (isset($this->request->data['unset'])) {         // unset Toppings
                        $ToppingId = $this->request->data['Topping']['id'];
                        foreach ($ToppingId as $key => $topid) {
                            if ($topid) {
                                $deafulttoppingId = $this->ItemDefaultTopping->defaultToppingExits($topid);
                                if ($deafulttoppingId) {
                                    $defaulttoppingdata['id'] = $deafulttoppingId['ItemDefaultTopping']['id'];
                                    $defaulttoppingdata['topping_id'] = $topid;
                                    $defaulttoppingdata['store_id'] = $storeID;
                                    $defaulttoppingdata['merchant_id'] = $merchant_id;
                                    $defaulttoppingdata['item_id'] = $ItemID;
                                    $defaulttoppingdata['is_deleted'] = 1;
                                    $this->ItemDefaultTopping->saveDefaultTopping($defaulttoppingdata);
                                }
                            }
                        }
                        $this->Session->setFlash(__("Default Add-ons has been removed from Item"), 'alert_success');
                    }
                } else {
                    if (isset($this->request->data['set']) && $this->request->data['Topping']['id']) {            // Set Default Toppings
                        $ToppingId = $this->request->data['Topping']['id'];
                        foreach ($ToppingId as $topid) {
                            if ($topid) {
                                $deafulttoppingId = $this->ItemDefaultTopping->defaultToppingExits($topid);
                                if ($deafulttoppingId) {
                                    $defaulttoppingdata['id'] = $deafulttoppingId['ItemDefaultTopping']['id'];
                                } else {
                                    $defaulttoppingdata['id'] = '';
                                }
                                $ItemDetails = $this->Topping->getToppingitemID($topid);
                                $defaulttoppingdata['topping_id'] = $topid;
                                $defaulttoppingdata['store_id'] = $storeID;
                                $defaulttoppingdata['merchant_id'] = $merchant_id;
                                $defaulttoppingdata['item_id'] = $ItemDetails['Topping']['item_id'];
                                $defaulttoppingdata['is_deleted'] = 0;
                                $this->ItemDefaultTopping->saveDefaultTopping($defaulttoppingdata);
                            }
                        }
                        $this->Session->setFlash(__("Add-ons are successfully assigned as default to Item"), 'alert_success');
                    }
                    if (isset($this->request->data['unset']) && $this->request->data['Topping']['id']) {         // unset Toppings
                        $ToppingId = $this->request->data['Topping']['id'];
                        foreach ($ToppingId as $topid) {
                            if ($topid) {
                                $deafulttoppingId = $this->ItemDefaultTopping->defaultToppingExits($topid);
                                if ($deafulttoppingId) {
                                    $ItemDetails = $this->Topping->getToppingitemID($topid);
                                    $defaulttoppingdata['id'] = $deafulttoppingId['ItemDefaultTopping']['id'];
                                    $defaulttoppingdata['topping_id'] = $topid;
                                    $defaulttoppingdata['store_id'] = $storeID;
                                    $defaulttoppingdata['merchant_id'] = $merchant_id;
                                    $defaulttoppingdata['item_id'] = $ItemDetails['Topping']['item_id'];
                                    $defaulttoppingdata['is_deleted'] = 1;
                                    $this->ItemDefaultTopping->saveDefaultTopping($defaulttoppingdata);
                                }
                            }
                        }
                        $this->Session->setFlash(__("Default Add-ons has been removed from Item"), 'alert_success');
                    }
                }
                unset($this->request->data['set']);
                unset($this->request->data['unset']);
                unset($this->request->data['Topping']['no']);
                unset($this->request->data['Topping']['id']);
            }
        }
        $this->Topping->bindModel(
                array(
            'belongsTo' => array(
                'ParentGroup' => array(
                    'className' => 'Topping',
                    'foreignKey' => 'addon_id',
                    //'type' => 'inner',
                    'conditions' => array('ParentGroup.is_deleted' => 0, 'ParentGroup.is_active' => 1),
                    'fields' => array('id', 'name')
                ),
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id',
                    'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                    'type' => 'inner',
                    'fields' => array('id', 'name')
                ), 'Category' => array(
                    'className' => 'Category',
                    'foreignKey' => 'category_id',
                    'type' => 'inner',
                    'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                    'fields' => array('id', 'name')
                ), "Store" => array('className' => 'Store',
                    'foreignKey' => 'store_id',
                    'type' => 'inner',
                    'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1),
                    'fields' => array('store_name'))
            ),
            'hasMany' => array(
                'ItemDefaultTopping' => array(
                    'className' => 'ItemDefaultTopping',
                    'foreignKey' => 'topping_id',
                    'conditions' => array('ItemDefaultTopping.is_deleted' => 0, 'ItemDefaultTopping.is_active' => 1),
                    'fields' => array('id', 'topping_id', 'item_id')
                )
            )
                ), false
        );
        if ($order == '') {
            $order = 'Topping.created DESC';
        }
        $itemdetail = '';
        $this->loadModel('Topping');
        if ($pagingFlag) {
            $this->paginate = array('conditions' => array($criteria), 'order' => $order);
            $itemdetail = $this->paginate('Topping');
        } else {
            $itemdetail = $this->Topping->find('all', array('conditions' => array($criteria), 'order' => $order));
        }
        $this->set('list', $itemdetail);
        $this->set('pagingFlag', $pagingFlag);
        $addonList = array();
        if ($addOnsCriteriaAdditional != '') {
            $addonList = $this->Topping->find('list', array('fields' => array('id', 'name'), 'conditions' => array($addOnsCriteria . $addOnsCriteriaAdditional), 'group' => array('Topping.name'), 'order' => array('Topping.name' => "ASC")));
        }
        $this->set('addonList', $addonList);
        $itemList = $this->Topping->find('all', array('conditions' => array('Topping.store_id' => $storeID, 'Topping.is_deleted' => 0)));
        //prx($itemList);
        $nList = $categoryList = $categoryListHasSubAddons = array();
        if (!empty($itemList)) {
            foreach ($itemList as $iList) {
                if (!empty($iList['Item']) && !empty($iList['Category'])) {
                    if ($iList['Topping']['is_addon_category']==1) {
                        $categoryList[$iList['Category']['id']] = $iList['Category']['name'];
                    } else {
                        $nList[$iList['Item']['id']] = $iList['Item']['name'];
                        $categoryListHasSubAddons[$iList['Category']['id']] = $iList['Category']['name'];
                    }
                }
            }
        }
        $this->set('itemList', $nList);
        //$categoryList = $this->Category->getCategoryListHasTopping($storeID);
        $this->set('categoryList', $categoryList);
        $this->set('categoryListHasSubAddons', $categoryListHasSubAddons);
    }

    /* ------------------------------------------------
      Function name:activateSubTopping()
      Description:Active/deactive Sub Topping
      created:22/09/2015
      ----------------------------------------------------- */

    public function activateSubTopping($EncryptedtoppingID = null, $status = 0) {
        $this->autoRender = false;
        $toppingid = $this->Encryption->decode($EncryptedtoppingID);
        $data['Topping']['merchant_id'] = $this->Session->read('merchantId');
        $data['Topping']['id'] = $toppingid;
        $data['Topping']['is_active'] = $status;
        if ($this->Topping->saveTopping($data)) {
            if ($status) {
                $SuccessMsg = "Sub Add-on Activated";
            } else {
                $SuccessMsg = "Sub Add-on Deactivated and Add-on will not available at Add-on List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hqtoppings', 'action' => 'subTopping'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqtoppings', 'action' => 'subTopping'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteSubTopping()
      Description:Delete Sub Topping
      created:22/09/2015
      ----------------------------------------------------- */

    public function deleteSubTopping($EncryptedtoppingID = null) {
        $this->autoRender = false;
        $toppingid = $this->Encryption->decode($EncryptedtoppingID);
        $data['Topping']['merchant_id'] = $this->Session->read('merchantId');
        $data['Topping']['id'] = $toppingid;
        $data['Topping']['is_deleted'] = 1;
        if ($this->Topping->saveTopping($data)) {
            $this->Session->setFlash(__("Sub Add-on deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqtoppings', 'action' => 'subTopping'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqtoppings', 'action' => 'subTopping'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteMultipleSubAddon()
      Description:Delete multiple Sub Add-ons
      created:22/09/2015
      ----------------------------------------------------- */

    public function deleteMultipleSubAddon() {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Topping']['merchant_id'] = $this->Session->read('merchantId');
        $data['Topping']['is_deleted'] = 1;
        if (!empty($this->request->data['Topping']['id'])) {
            $filter_array = array_filter($this->request->data['Topping']['id']);
            $i = 0;
            foreach ($filter_array as $toppingId) {
                $data['Topping']['id'] = $toppingId;
                $this->Topping->saveTopping($data);
                $i++;
            }
            $del = $i . "  " . "Sub Add-ons deleted successfully.";
            $this->Session->setFlash(__($del), 'alert_success');
            $this->redirect(array('controller' => 'hqtoppings', 'action' => 'listSubTopping'));
        }
    }

    /* ------------------------------------------------
      Function name:editSubTopping()
      Description:Edit sub toppings
      created:22/09/2015
      ----------------------------------------------------- */

    public function editSubTopping($EncryptedSubToppingID = null) {
        $toppingId = $this->Encryption->decode($EncryptedSubToppingID);
        $merchant_id = $this->Session->read('merchantId');
        $toppingsDetails = $this->Topping->getToppingitemID($toppingId);
        if (empty($toppingsDetails)) {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect($this->referer());
        }
        $storeId = $toppingsDetails['Topping']['store_id'];
        if ($this->request->is('post') || $this->request->is('put')) {
            if (empty($this->request->data['Topping']['item_id']) || empty($this->request->data['Topping']['addon_id'])) {
                $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
                $this->redirect($this->referer());
            }
            $this->request->data = $this->Common->trimValue($this->request->data);
            $id = $this->request->data['Topping']['id'];
            $itemid = $this->request->data['Topping']['item_id'];
            $categoryid = $this->request->data['Category']['id'];
            if ($this->Topping->checkSubaddonmUniqueName($this->request->data['Topping']['name'], $this->request->data['Topping']['addon_id'], $storeId, $id, $itemid)) {
                $checksubtopping = $this->Topping->ChecksubtoppingonItem($id, $itemid, $this->request->data['Topping']['addon_id'], $this->request->data['Topping']['name']);
                if (!$checksubtopping) {
                    $data['Topping']['item_id'] = $itemid;
                    $data['Topping']['store_id'] = $storeId;
                    $data['Topping']['merchant_id'] = $merchant_id;
                    $data['Topping']['is_active'] = $this->request->data['Topping']['is_active'];
                    $data['Topping']['name'] = trim($this->request->data['Topping']['name']);
                    $data['Topping']['price'] = $this->request->data['Topping']['price'];
                    $data['Topping']['addon_id'] = $this->request->data['Topping']['addon_id'];
                    $data['Topping']['id'] = $id;
                    $data['Topping']['category_id'] = $categoryid;
                    $data['Topping']['is_addon_category'] = 0;
                    if (!empty($this->request->data['Topping']['no_size'])) {
                        $data['Topping']['no_size'] = $this->request->data['Topping']['no_size'];
                    } else {
                        $data['Topping']['no_size'] = 0;
                    }
                    $this->Topping->saveTopping($data);
                    if (!empty($this->request->data['ItemDefaultTopping']['id'])) {
                        if ($this->request->data['Topping']['defaultcheck']) {
                            $defaulttoppingdata['is_deleted'] = 0;
                        } else {
                            $defaulttoppingdata['is_deleted'] = 1;
                        }
                        $defaulttoppingdata['topping_id'] = $id;
                        $defaulttoppingdata['item_id'] = $itemid;
                        $defaulttoppingdata['id'] = $this->request->data['ItemDefaultTopping']['id'];
                        $this->ItemDefaultTopping->saveDefaultTopping($defaulttoppingdata);
                    } else {
                        if ($this->request->data['Topping']['defaultcheck']) {
                            $defaulttoppingdata['is_deleted'] = 0;
                            $defaulttoppingdata['topping_id'] = $id;
                            $defaulttoppingdata['item_id'] = $itemid;
                            $defaulttoppingdata['id'] = '';
                            $defaulttoppingdata['store_id'] = $storeId;
                            $defaulttoppingdata['merchant_id'] = $merchant_id;
                            $this->ItemDefaultTopping->saveDefaultTopping($defaulttoppingdata);
                        }
                    }
                    $this->Session->setFlash(__("Sub Add-ons updated successfully."), 'alert_success');
                    $this->redirect(array('controller' => 'hqtoppings', 'action' => 'subTopping', 'clear'));
                } else {
                    $this->Session->setFlash(__("Sub Add-on Name Already assigned to the item"), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Sub Add-on Name Already Exists for Add-ons"), 'alert_failed');
            }
        }
        $this->Topping->bindModel(
                array(
            'hasOne' => array(
                'ItemDefaultTopping' => array(
                    'className' => 'ItemDefaultTopping',
                    'foreignKey' => 'topping_id',
                    'conditions' => array('ItemDefaultTopping.is_deleted' => 0),
                    'fields' => array('ItemDefaultTopping.id')
                ),
            )
                ), false
        );
        $toppingsDetails = $this->Topping->getToppingitemID($toppingId);
        $this->request->data = $toppingsDetails;
        $this->request->data['Category']['id'] = $toppingsDetails['Topping']['category_id'];
        $this->loadModel('Topping');
        $addonList = $this->Topping->getAddons($storeId);
        $addonListt = $this->Topping->getAddonsForEdit($storeId, $toppingsDetails['Topping']['addon_id'], $this->request->data['Category']['id']);
        $addonList = $this->Topping->getAddonsByCategory($storeId, $this->request->data['Category']['id']);
        $arrayaddon = array();
        if (!empty($addonList)) {
            foreach ($addonList as $key => $value) {
                if ($addonListt['Topping']['name'] == $value) {
                    $arrayaddon[$addonListt['Topping']['id']] = $value;
                } else {
                    $arrayaddon[$key] = $value;
                }
            }
        }
        $addonpost = 0;
        if ($arrayaddon) {
            $addonpost = 1;
        }
        $this->set('addonList', $arrayaddon);
        $Items = array();
        $Itemslist = array();
        $itempost = 0;
        if ($this->request->data['Topping']['addon_id']) {
            $this->Topping->bindModel(
                    array(
                'belongsTo' => array(
                    'Item' => array(
                        'className' => 'Item',
                        'foreignKey' => 'item_id',
                        'fields' => array('Item.id', 'Item.name')
                    ),
                )
                    ), false
            );
            $Items = $this->Topping->getItemsbyAddoncategory($this->request->data['Topping']['addon_id'], $storeId);
        }
        if ($Items) {
            foreach ($Items as $key => $value) {
                if (!empty($value['Item'])) {
                    $Itemslist[$value['Item']['id']] = $value['Item']['name'];
                }
            }
            $itempost = 1;
        }
        $this->set('itempost', $itempost);
        $this->set('Itemslist', $Itemslist);
        $categoryList = $this->Category->getCategoryListHasTopping($storeId);
        $this->set('categoryList', $categoryList);
        $this->set('addonpost', $addonpost);
    }

    public function getItemsByAddonCategoryId($toppingCategoryID = null, $subaddonID = null, $itemID = null, $Categoryid = null, $storeID = null) {
        $Items = array();
        $Itemslist = array();
        if ($toppingCategoryID) {
            $this->Topping->bindModel(
                    array(
                'belongsTo' => array(
                    'Item' => array(
                        'className' => 'Item',
                        'foreignKey' => 'item_id',
                        'fields' => array('Item.id', 'Item.name')
                    ),
                )
                    ), false
            );
            $Items = $this->Topping->getItemsbyAddoncategory($toppingCategoryID, $storeID, $Categoryid);
        }
        if ($Items) {
            foreach ($Items as $key => $value) {
                if (!empty($value['Item'])) {
                    $Itemslist[$value['Item']['id']] = $value['Item']['name'];
                }
            }
        }
        $this->set('Itemslist', $Itemslist);
    }

    /* ------------------------------------------------
      Function name:itemsBycategory()
      Description:get items by category
      created:6/8/2015
      ----------------------------------------------------- */

    public function addonByCategoryEdit($categoryId = null, $addonID = null, $storeId = null) {
        $addonList = $this->Topping->getAddons($storeId);
        $addonListt = $this->Topping->getAddonsForEdit($storeId, $addonID, $categoryId);
        $addonList = $this->Topping->getAddonsByCategory($storeId, $categoryId);
        $arrayaddon = array();
        if (!empty($addonList)) {
            foreach ($addonList as $key => $value) {
                if ($addonListt['Topping']['name'] == $value) {
                    $arrayaddon[$addonListt['Topping']['id']] = $value;
                } else {
                    $arrayaddon[$key] = $value;
                }
            }
        }
        $addonpost = 0;
        if ($arrayaddon) {
            $addonpost = 1;
        }
        $this->set('addonList', $arrayaddon);
    }

    public function uploadfile() {
        $this->layout = "hq_dashboard";
        $this->loadModel('Category');
        $this->loadModel('Store');
        $this->loadModel('Item');
        if (!empty($this->request->data)) {
            $tmp = $this->request->data;
            if ($tmp['Topping']['file']['error'] == 4) {
                $this->Session->setFlash(__('Your file contains error. Please retry uploading.'), 'alert_failed');
                $this->redirect($this->here);
            }
            $valid = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            if (!in_array($tmp['Topping']['file']['type'], $valid)) {
                $this->Session->setFlash(__('You can only upload Excel file.'), 'alert_failed');
            } else if ($tmp['Topping']['file']['error'] != 0) {
                $this->Session->setFlash(__('The file you uploaded contains errors.'), 'alert_failed');
            } else if ($tmp['Topping']['file']['size'] > 20000000) {
                $this->Session->setFlash(__('The file size must be Max 20MB'), 'alert_failed');
            } else {
                ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large
                App::import('Vendor', 'PHPExcel');
                $objPHPExcel = new PHPExcel;
                $objPHPExcel = PHPExcel_IOFactory::load($tmp['Topping']['file']['tmp_name']);
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                $real_data = array_values($sheetData);
                $merchantId = $this->Session->read('merchantId');
                $storeId = $this->request->data['Topping']['store_id'];
                if ($storeId == "All") {
                    $storeId = $this->Store->find('list', array('fields' => array('id'), 'conditions' => array('Store.merchant_id' => $merchantId)));
                    $i = $this->toppingForMultipleStore($storeId, $real_data, $merchantId);
                } else {
                    $i = $this->saveFileTopping($real_data, $storeId, $merchantId);
                }
                $this->Session->setFlash(__($i . ' ' . 'Add-ons has been saved'), 'alert_success');
                $this->redirect(array("controller" => "hqtoppings", "action" => "index"));
            }
        }
    }

    public function toppingForMultipleStore($storeIds = array(), $real_data = array(), $merchantId = null) {
        $i = 0;
        if (!empty($storeIds)) {
            foreach ($storeIds as $storeId) {
                $k = $this->saveFileTopping($real_data, $storeId, $merchantId);
                if (is_numeric($k)) {
                    $i = $i + $k;
                }
            }
        }
        return $i;
    }

    public function saveFileTopping($real_data = null, $storeId, $merchantId) {
        $i = 0;
        foreach ($real_data as $key => $row) {
            $row['A'] = trim($row['A']);
            if (!empty($row['A'])) {
                $isUniqueId = $this->Topping->checkToppingWithId($row['A']);
                if (!empty($isUniqueId) && $isUniqueId['Topping']['store_id'] != $storeId) {
                    continue;
                }
            }
            $row = $this->Common->trimValue($row);
            if ($key > 0) {
                if (!empty($row['B']) && !empty($row['C'])) {
                    $categoryId = $this->Category->getCategoryByNameTopping($storeId, trim($row['C']));
                    if (!empty($categoryId)) {
                        if ($row['D']) {
                            $itemId = $this->Item->getItemByNameTopping(trim($row['D']), $storeId, $categoryId['Category']['id']);
                            if (!empty($itemId)) {
                                if (!empty($row['A'])) {
                                    $isUniqueName = $this->Topping->checkToppingUniqueName($row['B'], $storeId, $itemId['Item']['id'], $row['A']);
                                } else {
                                    $isUniqueName = $this->Topping->checkToppingUniqueName($row['B'], $storeId, $itemId['Item']['id']);
                                }
                                if ($isUniqueName) {
                                    $toppingdata['name'] = $row['B'];
                                    $toppingdata['price'] = 0;
                                    $toppingdata['item_id'] = $itemId['Item']['id'];
                                    $toppingdata['category_id'] = $categoryId['Category']['id'];
                                    $toppingdata['merchant_id'] = $merchantId;

                                    if (!empty($row['E'])) {
                                        $toppingdata['is_active'] = $row['E'];
                                    } else {
                                        $toppingdata['is_active'] = 0;
                                    }

                                    if (!empty($row['F'])) {
                                        $toppingdata['position'] = $row['F'];
                                    } else {
                                        $toppingdata['position'] = 0;
                                    }
                                    if (!empty($row['I']) && ($row['I'] <= 10)) {
                                        $toppingdata['max_value'] = trim($row['I']);
                                        if (!empty($row['H']) && ($row['H'] <= $row['I'])) {
                                            $toppingdata['min_value'] = $row['H'];
                                        } else {
                                            $toppingdata['min_value'] = 0;
                                        }
                                    } else {
                                        $toppingdata['min_value'] = 0;
                                        $toppingdata['max_value'] = 0;
                                    }

                                    if (!empty($row['A'])) {
                                        $toppingdata['id'] = $row['A'];
                                    } else {
                                        $toppingdata['store_id'] = $storeId;
                                        $toppingdata['id'] = "";
                                        $this->Topping->create();
                                    }
                                    $topping = $this->Topping->saveTopping($toppingdata);
                                    $i++;
                                }
                            }
                        } else {
                            $itemId = $this->Item->getitemIDBycategory($categoryId['Category']['id'], $storeId);
                            if ($itemId) {
                                foreach ($itemId as $key => $item) {
                                    if (!empty($row['A'])) {
                                        $isUniqueName = $this->Topping->checkToppingUniqueName($row['B'], $storeId, $item['Item']['id'], $row['A']);
                                    } else {
                                        $isUniqueName = $this->Topping->checkToppingUniqueName($row['B'], $storeId, $item['Item']['id']);
                                    }
                                    if ($isUniqueName) {
                                        $toppingdata['name'] = $row['B'];
                                        $toppingdata['price'] = 0;
                                        $toppingdata['item_id'] = $itemId['Item']['id'];
                                        $toppingdata['category_id'] = $categoryId['Category']['id'];
                                        $toppingdata['merchant_id'] = $merchantId;

                                        if (!empty($row['E'])) {
                                            $toppingdata['is_active'] = $row['E'];
                                        } else {
                                            $toppingdata['is_active'] = 0;
                                        }

                                        if (!empty($row['F'])) {
                                            $toppingdata['position'] = $row['F'];
                                        } else {
                                            $toppingdata['position'] = 0;
                                        }

                                        if (!empty($row['I']) && ($row['I'] <= 10)) {
                                            $toppingdata['max_value'] = trim($row['I']);
                                            if (!empty($row['H']) && ($row['H'] <= $row['I'])) {
                                                $toppingdata['min_value'] = $row['H'];
                                            } else {
                                                $toppingdata['min_value'] = 0;
                                            }
                                        } else {
                                            $toppingdata['min_value'] = 0;
                                            $toppingdata['max_value'] = 0;
                                        }
                                        if (!empty($row['A'])) {
                                            $toppingdata['id'] = $row['A'];
                                        } else {
                                            $toppingdata['store_id'] = $storeId;
                                            $toppingdata['id'] = "";
                                            $this->Topping->create();
                                        }
                                        $topping = $this->Topping->saveTopping($toppingdata);
                                        $i++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $i;
    }

    public function downloadhqtoppings($store_id = null) {
        if (!empty($store_id)) {
            $this->Topping->bindModel(array(
                'belongsTo' => array(
                    'Store' => array(
                        'className' => 'Store',
                        'foreignKey' => 'store_id',
                        'fields' => array(
                            'id', 'store_name'
                        )
                    ))), false);
            if ($store_id == "All") {
                $merchantId = $this->Session->read('merchantId');
                $this->Topping->bindModel(array('belongsTo' => array('Item' => array('fields' => array('name', 'id', 'category_id'), 'conditions' => array('Item.merchant_id' => $merchantId, 'Item.is_deleted' => 0), 'type' => 'inner'))), false);
                $this->Topping->bindModel(array('belongsTo' => array('Category' => array('fields' => array('id', 'name'), 'conditions' => array('Category.merchant_id' => $merchantId, 'Category.is_deleted' => 0), 'type' => 'inner'))), false);
                $result = $this->Topping->find('all', array('conditions' => array('Topping.merchant_id' => $merchantId, 'Topping.is_deleted' => 0, 'Topping.is_addon_category' => 1), 'order' => array('Category.name' => "ASC", 'Item.name' => "ASC", 'Topping.position' => "ASC", 'Store.store_name' => "ASC"), 'type' => 'inner'));
            } else {
                $storeId = $store_id;
                $this->Topping->bindModel(array('belongsTo' => array('Item' => array('fields' => array('name', 'id', 'category_id'), 'conditions' => array('Item.store_id' => $storeId, 'Item.is_deleted' => 0), 'type' => 'inner'))), false);
                $this->Topping->bindModel(array('belongsTo' => array('Category' => array('fields' => array('id', 'name'), 'conditions' => array('Category.store_id' => $storeId, 'Category.is_deleted' => 0), 'type' => 'inner'))), false);
                $result = $this->Topping->find('all', array('conditions' => array('Topping.store_id' => $storeId, 'Topping.is_deleted' => 0, 'Topping.is_addon_category' => 1), 'order' => array('Category.name' => "ASC", 'Item.name' => "ASC", 'Topping.position' => "ASC", 'Store.store_name' => "ASC"), 'type' => 'inner'));
            }
        }
        Configure::write('debug', 0);
        App::import('Vendor', 'PHPExcel');
        $objPHPExcel = new PHPExcel;
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
        $filename = 'HqAdd-ons_' . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('Add-ons');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Add-on Name');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Category Name');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Items');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Active');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Position');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Store Name');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Min Sub-Add-on');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Max Sub-Add-on');

        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleArray);

        $i = 2;
        foreach ($result as $data) {
            if (!empty($data['Category']['name']) && $data['Item']['name']) {
                $objPHPExcel->getActiveSheet()->setCellValue("A$i", trim($data['Topping']['id']));
                $objPHPExcel->getActiveSheet()->setCellValue("B$i", trim($data['Topping']['name']));
                $objPHPExcel->getActiveSheet()->setCellValue("C$i", trim($data['Category']['name']));
                $objPHPExcel->getActiveSheet()->setCellValue("D$i", trim($data['Item']['name']));
                $objPHPExcel->getActiveSheet()->setCellValue("E$i", trim($data['Topping']['is_active']));
                $objPHPExcel->getActiveSheet()->setCellValue("F$i", trim($data['Topping']['position']));
                $objPHPExcel->getActiveSheet()->setCellValue("G$i", trim($data['Store']['store_name']));
                $objPHPExcel->getActiveSheet()->setCellValue("H$i", trim($data['Topping']['min_value']));
                $objPHPExcel->getActiveSheet()->setCellValue("I$i", trim($data['Topping']['max_value']));
                $i++;
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function upload_sub_topping_file() {
        $this->layout = "hq_dashboard";
        $this->loadModel('Store');
        if (!empty($this->request->data)) {
            $tmp = $this->request->data;
            if ($tmp['Topping']['file']['error'] == 4) {
                $this->Session->setFlash(__('Your file contains error. Please retry uploading.'), 'alert_failed');
                $this->redirect($this->here);
            }
            $valid = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            if (!in_array($tmp['Topping']['file']['type'], $valid)) {
                $this->Session->setFlash(__('You can only upload Excel file.'), 'alert_failed');
            } else if ($tmp['Topping']['file']['error'] != 0) {
                $this->Session->setFlash(__('The file you uploaded contains errors.'), 'alert_failed');
            } else if ($tmp['Topping']['file']['size'] > 20000000) {
                $this->Session->setFlash(__('The file size must be Max 20MB'), 'alert_failed');
            } else {
                ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large
                App::import('Vendor', 'PHPExcel');
                $objPHPExcel = new PHPExcel;
                $objPHPExcel = PHPExcel_IOFactory::load($tmp['Topping']['file']['tmp_name']);
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                $real_data = array_values($sheetData);
                $merchantId = $this->Session->read('merchantId');
                $storeId = $this->request->data['Topping']['store_id'];
                if ($storeId == "All") {
                    $storeId = $this->Store->find('list', array('fields' => array('id'), 'conditions' => array('Store.merchant_id' => $merchantId)));
                    $i = $this->subToppingForMultipleStore($storeId, $real_data, $merchantId);
                } else {
                    $i = $this->saveFileSubTopping($real_data, $storeId, $merchantId);
                }
                $this->Session->setFlash(__($i . ' ' . 'Sub Add-ons has been saved'), 'alert_success');
                $this->redirect(array("controller" => "hqtoppings", "action" => "SubTopping"));
            }
        }
    }

    public function subToppingForMultipleStore($storeIds = array(), $real_data = array(), $merchantId = null) {
        $i = 0;
        if (!empty($storeIds)) {
            foreach ($storeIds as $storeId) {
                $k = $this->saveFileSubTopping($real_data, $storeId, $merchantId);
                if (is_numeric($k)) {
                    $i = $i + $k;
                }
            }
        }
        return $i;
    }

    public function saveFileSubTopping($real_data = null, $storeId, $merchantId) {
        $i = 0;
        foreach ($real_data as $key => $row) {
            $row['A'] = trim($row['A']);

            if (!empty($row['A'])) {
                $isUniqueId = $this->Topping->checkToppingWithId($row['A']);
                if (!empty($isUniqueId)) {
                    if ($isUniqueId['Topping']['store_id'] != $storeId) {
                        continue;
                    }
                }
            }
            $row = $this->Common->trimValue($row);
            if ($key > 0) {
                if (!empty($row['B']) && !empty($row['C']) && !empty($row['D'])) {
                    $row['E'] = trim($row['E']);
                    $row['C'] = trim($row['C']);
                    $categoryId = $this->Category->getCategoryByName($storeId, $row['D']);
                    if (!empty($categoryId)) {
                        if (empty($row['E'])) {
                            $row['E'] = 0;
                        }
                        if (!empty($row['A'])) {
                            if (!empty($row['G'])) {
                                $getItem = $this->Item->getItemByName($row['G'], $storeId, $categoryId['Category']['id']);
                                if (!empty($getItem)) {
                                    $toppingId = $this->Topping->getToppingByNameCategory($storeId, $row['C'], $categoryId['Category']['id'], $getItem['Item']['id']);
                                    if ($toppingId) {
                                        $categoryTopCheck = $this->Topping->checkAddonByCategory($categoryId['Category']['id'], $storeId, $toppingId['Topping']['id']);
                                        if (!empty($categoryTopCheck)) {
                                            $isUniqueName = $this->Topping->checkSubaddonmUniqueName($row['B'], $toppingId['Topping']['id'], $storeId, $row['A'], $getItem['Item']['id']);
                                            if ($isUniqueName) {
                                                $data['Topping']['item_id'] = $getItem['Item']['id'];
                                                $data['Topping']['store_id'] = $storeId;
                                                $data['Topping']['merchant_id'] = $merchantId;
                                                $data['Topping']['name'] = $row['B'];
                                                $data['Topping']['price'] = $row['E'];
                                                $data['Topping']['is_addon_category'] = 0;
                                                $data['Topping']['addon_id'] = $toppingId['Topping']['id'];
                                                $data['Topping']['category_id'] = $categoryId['Category']['id'];
                                                $data['Topping']['is_addon_category'] = 0;
                                                if (!empty($row['F'])) {
                                                    $data['Topping']['is_active'] = $row['F'];
                                                } else {
                                                    $data['Topping']['is_active'] = 0;
                                                }
                                                if (!empty($row['H'])) {
                                                    $data['Topping']['no_size'] = $row['H'];
                                                } else {
                                                    $data['Topping']['no_size'] = 0;
                                                }
                                                if (!empty($row['I'])) {
                                                    $data['Topping']['position'] = $row['I'];
                                                } else {
                                                    $data['Topping']['position'] = 0;
                                                }
                                                $data['Topping']['id'] = $row['A'];
                                                $this->Topping->saveTopping($data);
                                                $i++;
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $row['B'] = trim($row['B']);
                            $toppingId = $this->Topping->getToppingByNameCategory($storeId, $row['C'], $categoryId['Category']['id']);
                            if ($toppingId) {
                                $categoryTopCheck = $this->Topping->checkAddonByCategory($categoryId['Category']['id'], $storeId, $toppingId['Topping']['id']);
                                if (!empty($categoryTopCheck)) {
                                    $toppingData = $this->Topping->getItemsBytoppingID($toppingId['Topping']['id'], $storeId, $categoryId['Category']['id']);
                                    foreach ($toppingData as $key => $value) {
                                        if ($this->Topping->checkToppingexistsOnItem($row['B'], $storeId, $value['Topping']['item_id'], $value['Topping']['id'])) {
                                            $data['Topping']['item_id'] = $value['Topping']['item_id'];
                                            $data['Topping']['store_id'] = $storeId;
                                            $data['Topping']['merchant_id'] = $merchantId;
                                            $data['Topping']['name'] = $row['B'];
                                            $data['Topping']['price'] = $row['E'];
                                            $data['Topping']['is_addon_category'] = 0;
                                            $data['Topping']['addon_id'] = $value['Topping']['id'];
                                            $data['Topping']['category_id'] = $categoryId['Category']['id'];
                                            $data['Topping']['is_addon_category'] = 0;
                                            if (!empty($row['F'])) {
                                                $data['Topping']['is_active'] = $row['F'];
                                            } else {
                                                $data['Topping']['is_active'] = 0;
                                            }
                                            if (!empty($row['H'])) {
                                                $data['Topping']['no_size'] = $row['H'];
                                            } else {
                                                $data['Topping']['no_size'] = 0;
                                            }
                                            if (!empty($row['I'])) {
                                                $data['Topping']['position'] = $row['I'];
                                            } else {
                                                $data['Topping']['position'] = 0;
                                            }
                                            $data['Topping']['id'] = "";
                                            $this->Topping->create();
                                            $this->Topping->saveTopping($data);
                                            $i++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $i;
    }

    public function downloadsubtoppingfile($store_id = null) {
        if (!empty($store_id)) {
            $this->Topping->bindModel(array(
                'belongsTo' => array(
                    'Store' => array(
                        'className' => 'Store',
                        'foreignKey' => 'store_id',
                        'fields' => array(
                            'id', 'store_name'
                        )
                    ))), false);
            $this->Topping->bindModel(array('belongsTo' => array('Category' => array('fields' => array('name')), 'Item' => array('fields' => array('name'), 'className' => 'Item', 'foreignKey' => 'item_id', 'type' => 'inner', 'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1)), 'ParentTopping' => array('className' => 'Topping', 'foreignKey' => 'addon_id', 'fields' => 'name', 'type' => 'inner', 'conditions' => array('ParentTopping.is_deleted' => 0, 'ParentTopping.is_active' => 1)))));
            if ($store_id == "All") {
                $merchantId = $this->Session->read('merchantId');
                $result = $this->Topping->find('all', array('recursive' => 2, 'conditions' => array('Topping.merchant_id' => $merchantId, 'Topping.is_deleted' => 0, 'Topping.is_addon_category' => 0), 'order' => array('Category.name' => "ASC", 'Item.name' => "ASC", 'ParentTopping.name' => "ASC", 'Topping.position' => "ASC", 'Store.store_name' => "ASC")));
            } else {
                $storeId = $store_id;
                $result = $this->Topping->find('all', array('recursive' => 2, 'conditions' => array('Topping.store_id' => $storeId, 'Topping.is_deleted' => 0, 'Topping.is_addon_category' => 0), 'order' => array('Category.name' => "ASC", 'Item.name' => "ASC", 'ParentTopping.name' => "ASC", 'Topping.position' => "ASC")));
            }
        }
        Configure::write('debug', 0);
        App::import('Vendor', 'PHPExcel');
        $objPHPExcel = new PHPExcel;
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
        $filename = 'Hq-Sub-Add-ons_' . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('Sub-Add-ons');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Sub Add-on Name');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Add-on Name');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Category Name');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Price ($)');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Active');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Item Name');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'No Size');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Position');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Store Name');

        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('J1')->applyFromArray($styleArray);

        $i = 2;
        foreach ($result as $data) {
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", trim($data['Topping']['id']));
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", trim($data['Topping']['name']));
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", trim($data['ParentTopping']['name']));
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", trim($data['Category']['name']));
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", trim($data['Topping']['price']));
            $objPHPExcel->getActiveSheet()->setCellValue("F$i", trim($data['Topping']['is_active']));
            $objPHPExcel->getActiveSheet()->setCellValue("G$i", trim($data['Item']['name']));
            $objPHPExcel->getActiveSheet()->setCellValue("H$i", trim($data['Topping']['no_size']));
            $objPHPExcel->getActiveSheet()->setCellValue("I$i", trim($data['Topping']['position']));
            $objPHPExcel->getActiveSheet()->setCellValue("J$i", trim($data['Store']['store_name']));
            $i++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('Topping');
            $this->Topping->bindModel(
                    array(
                'belongsTo' => array(
                    'Item' => array(
                        'className' => 'Item',
                        'foreignKey' => 'item_id',
                        'type' => 'inner',
                        'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                        'fields' => array('id', 'name')
                    ), 'Category' => array(
                        'className' => 'Category',
                        'foreignKey' => 'category_id',
                        'type' => 'inner',
                        'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                        'fields' => array('id', 'name')
                    ), "Store" => array('className' => 'Store',
                        'foreignKey' => 'store_id',
                        'type' => 'inner',
                        'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1),
                        'fields' => array('store_name'))
                ),
                'hasMany' => array(
                    'ItemDefaultTopping' => array(
                        'className' => 'ItemDefaultTopping',
                        'foreignKey' => 'topping_id',
                        'conditions' => array('ItemDefaultTopping.is_deleted' => 0, 'ItemDefaultTopping.is_active' => 1),
                        'fields' => array('id', 'topping_id', 'item_id')
                    )
                )
                    ), false
            );
            if (!empty($_GET['storeID'])) {
                $storeID = $_GET['storeID'];
            } else {
                $merchant_id = $this->Session->read('merchantId');
                $storeID = $this->Store->getAllStoresByMerchantId($merchant_id);
            }
            if (!empty($_GET['sub']) && $_GET['sub'] == 'sub') {
                $cat = 0;
            } else {
                $cat = 1;
            }
            $searchData = $this->Topping->find('all', array('fields' => array('Topping.name', 'Topping.store_id', 'Topping.id', 'Item.name', 'Item.id', 'Category.id', 'Category.name'), 'conditions' => array('OR' => array('Topping.name LIKE' => '%' . $_GET['term'] . '%', 'Item.name LIKE' => '%' . $_GET['term'] . '%', 'Category.name LIKE' => '%' . $_GET['term'] . '%'), 'Topping.is_deleted' => 0, 'Topping.store_id' => $storeID, 'Topping.is_addon_category' => $cat), 'recursive' => 2));
            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    $new_array[] = array('label' => $val['Topping']['name'], 'value' => $val['Topping']['name'], 'desc' => $val['Topping']['name'] . " - " . $val['Item']['name'] . ' - ' . $val['Category']['name']);
                };
            }
            echo json_encode($new_array);
        } else {
            exit;
        }
    }

}
