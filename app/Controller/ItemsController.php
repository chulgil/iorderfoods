<?php

App::uses('StoreAppController', 'Controller');

class ItemsController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption');
    public $uses = array('Item', 'ItemPrice', 'ItemType', 'Size', 'StoreTax');

    public function beforeFilter() {
        // echo Router::url( $this->here, true );die;
        parent::beforeFilter();
        $adminfunctions = array('addMenuItem', 'editMenuItem', 'index', 'activateItem', 'deleteItemPhoto', 'itemsBycategorysingle', 'updateOrder');
        if (in_array($this->params['action'], $adminfunctions)) {
            if (!$this->Common->checkPermissionByaction($this->params['controller'])) {
                $this->Session->setFlash(__("Permission Denied"));
                $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:addMenuItem()
      Description:Add Menu Item
      created:22/7/2015
      ----------------------------------------------------- */

    public function addMenuItem() {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $roleId = $this->Session->read('Auth.Admin.role_id');
        //$merchant_id=AuthComponent::User('merchant_id');
        $merchant_id = $this->Session->read('admin_merchant_id');
        $this->set(compact('roleId'));
        $this->set(compact('storeId'));

        $this->loadModel('Interval');
        $intervalList = $this->Interval->getIntervalList($storeId);
        $this->set(compact('intervalList'));

        if ($this->request->data && $this->request->is(array('post', 'put'))) {

            $this->request->data = $this->Common->trimValue($this->request->data);
            $itemName = trim($this->request->data['Item']['name']);
            $isUniqueName = $this->Item->checkItemUniqueName($itemName, $storeId, null, $this->request->data['Item']['category_id']);
            if ($isUniqueName) {
                $itemPrice = array();
                $itemType = array();
                $itemdata = array();
                $itemdata['name'] = trim($this->request->data['Item']['name']);
                $itemdata['category_id'] = $this->request->data['Item']['category_id'];
                $itemdata['store_id'] = $storeId;
                $itemdata['merchant_id'] = $merchant_id;
                if (!empty($this->request->data['Item']['mandatory_item_units'])) {
                    $itemdata['mandatory_item_units'] = $this->request->data['Item']['mandatory_item_units'];
                } else {
                    $itemdata['mandatory_item_units'] = 0;
                }
                $this->Item->bindModel(
                        array(
                    'belongsTo' => array(
                        'Category' => array(
                            'className' => 'Category',
                            'foreignKey' => 'category_id',
                            'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                            'fields' => array('id', 'name')
                        )
                    )
                        ), false
                );
                if ($this->request->data['Item']['is_deliverable']) {
                    $itemdata['is_deliverable'] = 1;
                } else {
                    $itemdata['is_deliverable'] = 0;
                }
//                if ($this->request->data['Item']['preference_mandatory']) {
//                    $itemdata['preference_mandatory'] = 1;
//                } else {
//                    $itemdata['preference_mandatory'] = 0;
//                }
                if ($this->request->data['Item']['default_subs_price']) {
                    $itemdata['default_subs_price'] = 1;
                } else {
                    $itemdata['default_subs_price'] = 0;
                }
                $itemdata['description'] = trim($this->request->data['Item']['description']);
                if ($this->request->data['Item']['is_seasonal_item']) {
                    $itemdata['is_seasonal_item'] = $this->request->data['Item']['is_seasonal_item'];
                    $startDate = $this->Dateform->formatDate($this->request->data['Item']['start_date']);
                    $endDate = $this->Dateform->formatDate($this->request->data['Item']['end_date']);
                    $itemdata['start_date'] = $startDate;
                    $itemdata['end_date'] = $endDate;
                }
                $response = $this->Common->uploadMenuItemImages($this->request->data['Item']['imgcat'], '/MenuItem-Image/', $storeId, 216, 160, 'deals-images', 480, 320);
                //pr($response);die;
                if (!$response['status']) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                } else {
                    //Item Data
                    $itemdata['image'] = $response['imagename'];
                    $this->Item->create();
                    $this->Item->saveItem($itemdata);
                    $itemID = $this->Item->getLastInsertId();
                    if ($itemID) {
                        //PriceData
                        if ($this->request->data['Size']['id']) {
                            $priceArray = explode(',', $this->request->data['ItemPrice']['price']);
                            if (!$priceArray[0]) {
                                $priceArray[0] = 0;
                            }
                            foreach ($this->request->data['Size']['id'] as $key => $sizeid) {
                                if (!isset($priceArray[$key])) {
                                    $priceArray[$key] = $priceArray[0];
                                }
                                $itemPrice['store_id'] = $storeId;
                                $itemPrice['size_id'] = $sizeid;
                                $itemPrice['item_id'] = $itemID;
                                $itemPrice['price'] = $priceArray[$key];
                                $itemPrice['merchant_id'] = $merchant_id;
                                if (!empty($this->request->data['StoreTax']['id'])) {
                                    $itemPrice['store_tax_id'] = $this->request->data['StoreTax']['id'];
                                }
                                $this->ItemPrice->create();
                                $this->ItemPrice->saveItemPrice($itemPrice);
                            }

                            //  Save Multiple Size intervalPrice
                            if (isset($this->request->data['Interval']) && !empty($this->request->data['Interval'])) {
                                $this->loadModel('IntervalPrice');
                                $this->IntervalPrice->saveMultipleSizeIntervalPrice($this->request->data['Interval'], $this->request->data['Size'], $itemID, $storeId);
                            }
                        } else {
                            $priceArray = explode(',', $this->request->data['ItemPrice']['price']);
                            if (!$priceArray[0]) {
                                $priceArray[0] = 0;
                            }
                            $itemPrice['store_id'] = $storeId;
                            $itemPrice['item_id'] = $itemID;
                            $itemPrice['merchant_id'] = $merchant_id;
                            $itemPrice['price'] = $priceArray[0];
                            if (!empty($this->request->data['StoreTax']['id'])) {
                                $itemPrice['store_tax_id'] = $this->request->data['StoreTax']['id'];
                            }

                            $this->ItemPrice->create();
                            $this->ItemPrice->saveItemPrice($itemPrice);

                            //  Save Multiple Size intervalPrice
                            if (isset($this->request->data['Interval']) && !empty($this->request->data['Interval'])) {
                                $this->loadModel('IntervalPrice');
                                $this->IntervalPrice->saveSingleSizeIntervalPrice($this->request->data['Interval'], $sizeId = 0, $itemID, $storeId);
                            }
                        }

                        //TypeData
                        if ($this->request->data['Type']['id']) {
                            foreach ($this->request->data['Type']['id'] as $key => $typeID) {
                                $itemType['item_id'] = $itemID;
                                $itemType['store_id'] = $storeId;
                                $itemType['type_id'] = $typeID;
                                $itemType['merchant_id'] = $merchant_id;
                                $this->ItemType->create();
                                $this->ItemType->saveItemType($itemType);
                            }
                        }
                        $this->request->data = '';
                        $this->Session->setFlash(__("Item Successfully Created"), 'alert_success');
                        //$this->redirect(array('controller' => 'Items', 'action' => 'index'));
                    } else {
                        $this->Session->setFlash(__("Item Not created"), 'alert_failed');
                        //$this->redirect(array('controller' => 'Items', 'action' => 'addMenuItem'));
                    }
                }
            } else {
                $this->Session->setFlash(__("Item name Already exists"), 'alert_failed');
                //$this->redirect(array('controller' => 'Items', 'action' => 'addMenuItem'));
            }
        }
        $sizeList = '';
        $sizepost = 0;
        $typepost = 0;
        $seasonalpost = 0;
        $this->loadModel('Category');
        if (isset($this->request->data['Item']['category_id'])) {
            $sizeList = $this->Size->getCategorySizes($this->request->data['Item']['category_id'], $storeId);
            $sizepost = 1;
        }
        $this->set('sizeList', $sizeList);
        if (isset($this->request->data['Type']['id'])) {
            $typeInfo = $this->Category->getCategorySizeType($this->request->data['Item']['category_id'], $storeId);
            if ($typeInfo['Category']['is_sizeonly'] == 2 || $typeInfo['Category']['is_sizeonly'] == 3) {
                $typepost = 1;
            }
        }
        if (isset($this->request->data['Item']['is_seasonal_item']) && $this->request->data['Item']['is_seasonal_item']) {
            $seasonalpost = 1;
        }
        $this->set('typepost', $typepost);
        $this->set('sizepost', $sizepost);
        $this->set('seasonalpost', $seasonalpost);


        $categoryList = $this->Category->getCategoryList($storeId);
        $this->set('categoryList', $categoryList);
        $this->loadModel('Type');
        $typeList = $this->Type->getTypes($storeId);
        $this->set('typeList', $typeList);
        $this->loadModel('StoreTax');
        $storeTax = $this->StoreTax->storeTaxes($storeId);
        $storeTaxlist = array();
        if (!empty($storeTax)) {
            foreach ($storeTax as $key => $value) {
                $storeTaxlist[$value['StoreTax']['id']] = ucwords($value['StoreTax']['tax_name']) . ' - ' . $value['StoreTax']['tax_value'] . "%";
            }
        }
        $this->set('storeTaxlist', $storeTaxlist);
    }

    /* ------------------------------------------------
      Function name:editMenuItem()
      Description:Update Menu Item
      created:5/8/2015
      ----------------------------------------------------- */

    public function editMenuItem($EncrypteditemID = null) {
        $this->layout = "admin_dashboard";
        $merchant_id = $this->Session->read('admin_merchant_id');
        $storeId = $this->Session->read('admin_store_id');
        $itemId = $this->Encryption->decode($EncrypteditemID);

        $this->loadModel('Interval');
        $this->loadModel('IntervalPrice');

        $this->Interval->unbindModel(
                array('hasMany' => array('IntervalDay'))
        );

        $this->Interval->bindModel(
                array(
                    'hasMany' => array(
                        'IntervalPrice' => array(
                            'className' => 'IntervalPrice',
                            'foreignKey' => 'interval_id',
                            'conditions' => array('IntervalPrice.item_id' => $itemId, 'IntervalPrice.store_id' => $storeId),
                            'type' => 'INNER',
        ))));


        $intervalDetail = $this->Interval->getAllActiveInvervals($storeId);
        $this->set(compact('intervalDetail'));
        $intervalList = $this->Interval->getIntervalList($storeId);
        $this->set(compact('intervalList'));


        if ($this->request->data) {
            //pr($this->request->data); exit;
            $this->request->data = $this->Common->trimValue($this->request->data);
            $itemName = trim($this->request->data['Item']['name']);
            $isUniqueName = $this->Item->checkItemUniqueName($itemName, $storeId, $itemId, $this->request->data['Item']['category_id']);

            if ($isUniqueName) {
                $itemPrice = array();
                $itemType = array();
                $itemdata = array();
                $itemdata['id'] = $this->request->data['Item']['id'];
                $itemdata['name'] = trim($this->request->data['Item']['name']);
                $itemdata['category_id'] = $this->request->data['Item']['category_id'];
                $itemdata['store_id'] = $storeId;
                $itemdata['merchant_id'] = $merchant_id;
//                if (!empty($this->request->data['Item']['mandatory_item_units'])) {
//                    $itemdata['mandatory_item_units'] = $this->request->data['Item']['mandatory_item_units'];
//                } else {
//                    $itemdata['mandatory_item_units'] = 0;
//                }
                if ($this->request->data['Item']['is_deliverable']) {
                    $itemdata['is_deliverable'] = 1;
                } else {
                    $itemdata['is_deliverable'] = 0;
                }

//                if ($this->request->data['Item']['preference_mandatory']) {
//                    $itemdata['preference_mandatory'] = 1;
//                } else {
//                    $itemdata['preference_mandatory'] = 0;
//                }

                if ($this->request->data['Item']['default_subs_price']) {
                    $itemdata['default_subs_price'] = 1;
                } else {
                    $itemdata['default_subs_price'] = 0;
                }


                $itemdata['description'] = trim($this->request->data['Item']['description']);
                if ($this->request->data['Item']['is_seasonal_item']) {
                    $itemdata['is_seasonal_item'] = $this->request->data['Item']['is_seasonal_item'];
                    $startDate = $this->Dateform->formatDate($this->request->data['Item']['start_date']);
                    $endDate = $this->Dateform->formatDate($this->request->data['Item']['end_date']);
                    $itemdata['start_date'] = $startDate;
                    $itemdata['end_date'] = $endDate;
                } else {
                    $itemdata['is_seasonal_item'] = 0;
                    $itemdata['start_date'] = '';
                    $itemdata['end_date'] = '';
                }
                if ($this->request->data['Item']['imgcat']['error'] == 0) {
                    $response = $this->Common->uploadMenuItemImages($this->request->data['Item']['imgcat'], '/MenuItem-Image/', $storeId, 216, 160, 'deals-images', 480, 320);
                } elseif ($this->request->data['Item']['imgcat']['error'] == 4) {
                    $response['status'] = true;
                    $response['imagename'] = '';
                }
                if (!$response['status']) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                } else {
                    //Item Data
                    if ($response['imagename']) {
                        $itemdata['image'] = $response['imagename'];
                    }
                    $this->Item->saveItem($itemdata);
                    //Delete all Item id Realed rows
                    $this->ItemType->deleteallItemType($itemdata['id']);
                    $this->ItemPrice->deleteallItemPrice($itemdata['id']);
                    //pr($this->request->data);die;
                    $priceArray = explode(',', $this->request->data['ItemPrice']['price']);
                    if (isset($this->request->data['Size']['id']) && $this->request->data['Size']['id']) {
                        foreach ($this->request->data['Size']['id'] as $key => $sizeid) {
                            $itemPriceID = $this->ItemPrice->ItemPriceExits($itemdata['id'], $sizeid, $storeId);
                            if (!$priceArray[0]) {
                                $priceArray[0] = 0;
                            }
                            if (!isset($priceArray[$key])) {
                                $priceArray[$key] = $priceArray[0];
                            }
                            if ($itemPriceID) {
                                $itemPrice['id'] = $itemPriceID['ItemPrice']['id'];
                            } else {
                                $itemPrice['id'] = '';
                            }
                            $itemPrice['store_id'] = $storeId;
                            $itemPrice['size_id'] = $sizeid;
                            $itemPrice['item_id'] = $itemdata['id'];
                            $itemPrice['price'] = $priceArray[$key];
                            $itemPrice['is_deleted'] = 0;
                            $itemPrice['merchant_id'] = $merchant_id;

                            if (!empty($this->request->data['ItemPrice']['store_tax_id'])) {
                                $itemPrice['store_tax_id'] = $this->request->data['ItemPrice']['store_tax_id'];
                            } else {
                                $itemPrice['store_tax_id'] = '';
                            }

                            $this->ItemPrice->saveItemPrice($itemPrice);
                        }
                        //  Save multiple intervalPrice
                        $this->loadModel('IntervalPrice');
                        if (isset($this->request->data['Interval']['Add']) && !empty($this->request->data['Interval']['Add'])) {
                            $this->IntervalPrice->saveMultipleSizeIntervalPrice($this->request->data['Interval']['Add'], $this->request->data['Size'], $itemdata['id'], $storeId);
                        } else if (isset($this->request->data['Interval']['Edit']) && !empty($this->request->data['Interval']['Edit'])) {
                            $this->IntervalPrice->updateMultipleSizeIntervalPrice($this->request->data['Interval']['Edit'], $this->request->data['Size'], $itemdata['id'], $storeId);
                        }
                    } else {
                        $itemPriceID = $this->ItemPrice->ItemPriceExits($itemdata['id'], '', $storeId);
                        if (!$priceArray[0]) {
                            $priceArray[0] = 0;
                        }
                        if ($itemPriceID) {
                            $itemPrice['id'] = $itemPriceID['ItemPrice']['id'];
                        } else {
                            $itemPrice['id'] = '';
                        }
                        $itemPrice['store_id'] = $storeId;
                        $itemPrice['size_id'] = 0;
                        $itemPrice['item_id'] = $itemdata['id'];
                        $itemPrice['is_deleted'] = 0;
                        $itemPrice['price'] = $priceArray[0];
                        $itemPrice['merchant_id'] = $merchant_id;

                        if (!empty($this->request->data['ItemPrice']['store_tax_id'])) {
                            $itemPrice['store_tax_id'] = $this->request->data['ItemPrice']['store_tax_id'];
                        } else {
                            $itemPrice['store_tax_id'] = '';
                        }
                        $this->ItemPrice->saveItemPrice($itemPrice);

                        //  Save multiple intervalPrice
                        $this->loadModel('IntervalPrice');
                        if (isset($this->request->data['Interval']['Add']) && !empty($this->request->data['Interval']['Add'])) {
                            $this->IntervalPrice->saveSingleSizeIntervalPrice($this->request->data['Interval']['Add'], $sizeId = 0, $itemdata['id'], $storeId);
                        } else if (isset($this->request->data['Interval']['Edit']) && !empty($this->request->data['Interval']['Edit'])) {
                            $this->IntervalPrice->updateSingleSizeIntervalPrice($this->request->data['Interval']['Edit'], $sizeId = 0, $itemdata['id'], $storeId);
                        }
                    }
                    //TYpe
                    if (isset($this->request->data['Type']['id']) && $this->request->data['Type']['id']) {
                        foreach ($this->request->data['Type']['id'] as $key => $typeid) {
                            $itemtypeID = $this->ItemType->ItemTypeExits($itemdata['id'], $typeid, $storeId);
                            if ($itemtypeID) {
                                $itemType['id'] = $itemtypeID['ItemType']['id'];
                            } else {
                                $itemType['id'] = '';
                            }
                            $itemType['item_id'] = $itemdata['id'];
                            $itemType['store_id'] = $storeId;
                            $itemType['type_id'] = $typeid;
                            $itemType['is_deleted'] = 0;
                            $itemType['merchant_id'] = $merchant_id;
                            $this->ItemType->saveItemType($itemType);
                        }
                    }
                    $this->Session->setFlash(__('Item details updated successfully'), 'alert_success');
                    $this->redirect(array('controller' => 'items', 'action' => 'index'));
                }
            } else {
                $this->Session->setFlash(__("Item name Already exists"), 'alert_failed');
            }
        }



        $this->Item->bindModel(
                array(
            'hasMany' => array(
                'ItemPrice' => array(
                    'className' => 'ItemPrice',
                    'foreignKey' => 'item_id',
                    'conditions' => array('ItemPrice.is_deleted' => 0, 'ItemPrice.is_active' => 1),
                    'fields' => array('id', 'price', 'size_id', 'store_tax_id'),
                    'order' => array('id ASC')
                ),
                'ItemType' => array(
                    'className' => 'ItemType',
                    'foreignKey' => 'item_id',
                    'conditions' => array('ItemType.is_deleted' => 0, 'ItemType.is_active' => 1),
                    'fields' => array('id', 'type_id')
                )
            ),
            'belongsTo' => array(
                'Category' => array(
                    'className' => 'Category',
                    'foreignKey' => 'category_id',
                    'type' => 'INNER',
                    'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                    'fields' => array('id', 'name', 'is_sizeonly', 'is_mandatory')
                )
            )
                ), false
        );
        $editItemArray = array();
        $itemDetails = $this->Item->fetchItemDetail($itemId, $storeId, 1); //pr($itemDetails);die;
        foreach ($itemDetails as $key => $Data) {
            if ($key == 'Item') {
                $editItemArray['Item']['id'] = $Data['id'];
                $editItemArray['Item']['name'] = $Data['name'];
                $editItemArray['Item']['description'] = $Data['description'];
                $editItemArray['Item']['category_id'] = $Data['category_id'];
                $editItemArray['Item']['is_seasonal_item'] = $Data['is_seasonal_item'];
                $editItemArray['Item']['start_date'] = $Data['start_date'];
                $editItemArray['Item']['end_date'] = $Data['end_date'];
                $editItemArray['Item']['image'] = $Data['image'];
                $editItemArray['Item']['is_deliverable'] = $Data['is_deliverable'];
                //$editItemArray['Item']['preference_mandatory'] = $Data['preference_mandatory'];
                $editItemArray['Item']['default_subs_price'] = $Data['default_subs_price'];
                $editItemArray['Item']['mandatory_item_units'] = $Data['mandatory_item_units'];
            }
            if ($key == 'ItemPrice') {
                $priceString = 0;
                $i = 1; //echo $key;pr($Data);die;
                foreach ($Data as $vkey => $Pricearray) {
                    if ($i == 1) {
                        $priceString = $Pricearray['price'];
                    } else {
                        $priceString.=',' . $Pricearray['price'];
                    }
                    if (isset($Pricearray['Size']['id'])) {
                        $editItemArray['Size']['id'][] = $Pricearray['Size']['id'];
                    }
                    //$editItemArray['ItemPrice']['id'][]=$Pricearray['id'];
                    $i++;
                    $editItemArray['ItemPrice']['store_tax_id'] = $Pricearray['store_tax_id'];
                }
                $editItemArray['ItemPrice']['price'] = $priceString;
            }

            if ($key == 'Category') {     //echo $key;pr($Data);//die;
                $editItemArray['Size']['issizeonly'] = $Data['is_sizeonly'];
                $editItemArray['Category']['is_mandatory'] = $Data['is_mandatory'];
            }

            if ($key == 'ItemType') { //echo $key;pr($Data);die;
                foreach ($Data as $vkey => $typeArray) {
                    $editItemArray['Type']['id'][] = $typeArray['type_id'];
                }
            }
        }
        $sizepost = 0;
        $typepost = 0;
        $seasonalpost = 0;
        if (isset($editItemArray['Item']['category_id'])) {
            $sizeList = $this->Size->getCategorySizes($editItemArray['Item']['category_id'], $storeId);
            if ($editItemArray['Size']['issizeonly'] == 1 || $editItemArray['Size']['issizeonly'] == 3) { // [1,3 Size applicable]
                $sizepost = 1;
            }
        }
        $this->set('sizeList', $sizeList);
        if ($editItemArray['Size']['issizeonly'] == 2 || $editItemArray['Size']['issizeonly'] == 3) {  // [2,3 Size applicable]
            $typepost = 1;
        }
        if ($editItemArray['Item']['is_seasonal_item'] > 0) {
            $seasonalpost = 1;
        }
        $this->set('typepost', $typepost);
        $this->set('sizepost', $sizepost);
        $this->set('seasonalpost', $seasonalpost);
        $this->loadModel('Category');
        $categoryList = $this->Category->getCategoryList($storeId);
        $this->set('categoryList', $categoryList);
        $this->loadModel('Type');
        $typeList = $this->Type->getTypes($storeId);
        $this->set('typeList', $typeList);
        $this->request->data = $editItemArray;
        //prx($this->request->data);

        $this->loadModel('StoreTax');
        $storeTax = $this->StoreTax->storeTaxes($storeId);
        $storeTaxlist = array();
        if (!empty($storeTax)) {
            foreach ($storeTax as $key => $value) {
                $storeTaxlist[$value['StoreTax']['id']] = ucwords($value['StoreTax']['tax_name']) . ' - ' . $value['StoreTax']['tax_value'] . "%";
            }
        }
        $this->set('storeTaxlist', $storeTaxlist);
        //pr($this->request->data);die;
    }

    /* ------------------------------------------------
      Function name:index()
      Description:List Menu Items
      created:5/8/2015
      ----------------------------------------------------- */

    public function index($clearAction = null) {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $value = "";
        $criteria = "Item.store_id =$storeID AND Item.is_deleted=0";
        $order = '';
        $pagingFlag = true;
        //if(isset($this->params['named']['sort']) || isset($this->params['named']['page'])){
        if ($this->Session->read('ItemSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('ItemSearchData'), true);
        } else {
            $this->Session->delete('ItemSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        $limit = 20;
        if (!empty($this->request->data)) {
            $this->Session->write('ItemSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['Item']['keyword'])) {
                $value = trim($this->request->data['Item']['keyword']);
                $criteria .= " AND (Item.name LIKE '%" . $value . "%' OR Item.description LIKE '%" . $value . "%' OR Category.name LIKE '%" . $value . "%')";
            }
            if (!empty($this->request->data['Item']['category_id'])) {
                $categoryID = trim($this->request->data['Item']['category_id']);
                $criteria .= " AND (Category.id =$categoryID)";
                $order = 'Item.position ASC';
                $limit = 50;
            }
            if ($this->request->data['Item']['is_active'] != '') {
                $active = trim($this->request->data['Item']['is_active']);
                $criteria .= " AND (Item.is_active =$active)";
            }
        }


        $this->Item->bindModel(
                array(
            'belongsTo' => array(
                'Category' => array(
                    'className' => 'Category',
                    'foreignKey' => 'category_id',
                    'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                    'fields' => array('id', 'name', 'is_mandatory'),
                    'type' => 'INNER'
                )
            )
                ), false
        );

        $this->Item->bindModel(
                array(
            'hasMany' => array(
                'ItemPrice' => array(
                    'className' => 'ItemPrice',
                    'foreignKey' => 'item_id',
                    'conditions' => array('ItemPrice.is_deleted' => 0, 'ItemPrice.is_active' => 1, 'ItemPrice.store_id' => $storeID),
                    'fields' => array('ItemPrice.id', 'ItemPrice.size_id'),
                    'type' => 'INNER',
                )
            ),
                ), false
        );


        if ($order == '') {
            $order = 'Item.created DESC';
        }

        $itemdetail = '';

        //$itemdetail=$this->Item->find('all',array('conditions'=>array($criteria),'order'=>$order));
        $this->paginate = array('conditions' => array($criteria), 'order' => $order, 'limit' => $limit);
        $itemdetail = $this->paginate('Item');
        $this->set('list', $itemdetail);

        $this->loadModel('Category');
        $categoryList = $this->Category->getCategoryList($storeID);
        $this->set('categoryList', $categoryList);
        $this->set('keyword', $value);
    }

    /* ------------------------------------------------
      Function name:activateItem()
      Description:Active/deactive items
      created:5/8/2015
      ----------------------------------------------------- */

    public function activateItem($EncrypteditemID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Item']['store_id'] = $this->Session->read('admin_store_id');
        $data['Item']['id'] = $this->Encryption->decode($EncrypteditemID);
        $data['Item']['is_active'] = $status;
        if ($this->Item->saveItem($data)) {
            if ($status) {
                $SuccessMsg = "Item Activated";
            } else {
                $SuccessMsg = "Item Deactivated and Item will not get Display in Menu List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'Items', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'Items', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteItem()
      Description:Delete item
      created:5/8/2015
      ----------------------------------------------------- */

    public function deleteItem($EncrypteditemID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Item']['store_id'] = $this->Session->read('admin_store_id');
        $data['Item']['id'] = $this->Encryption->decode($EncrypteditemID);
        $data['Item']['is_deleted'] = 1;
        if ($this->Item->saveItem($data)) {
            $this->Session->setFlash(__("Item deleted"), 'alert_success');
            $this->redirect(array('controller' => 'Items', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'Items', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteItemPhoto()
      Description:Delete item Photo
      created:5/8/2015
      ----------------------------------------------------- */

    public function deleteItemPhoto($EncryptItemID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Item']['store_id'] = $this->Session->read('admin_store_id');
        $data['Item']['id'] = $this->Encryption->decode($EncryptItemID);
        $data['Item']['image'] = '';
        if ($this->Item->saveItem($data)) {
            $this->Session->setFlash(__("Item Photo deleted"), 'alert_success');
            $this->redirect(array('controller' => 'Items', 'action' => 'editMenuItem', $EncryptItemID));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'Items', 'action' => 'editMenuItem', $EncryptItemID));
        }
    }

    /* ------------------------------------------------
      Function name:itemsBycategory()
      Description:get items by category
      created:6/8/2015
      ----------------------------------------------------- */

    public function itemsBycategory($categoryId = null) {
        $itemList = '';
        $storeID = $this->Session->read('admin_store_id');
        if ($categoryId) {
            $itemList = $this->Item->getItemsByCategory($categoryId, $storeID);
        }
        $this->set('itemList', $itemList);
    }

    /* ------------------------------------------------
      Function name:categoryItems()
      Description:get items by category
      created:6/8/2015
      ----------------------------------------------------- */

    public function categoryItems($categoryId = null) {
        $itemList = '';
        $storeID = $this->Session->read('admin_store_id');
        if ($categoryId) {
            $itemList = $this->Item->getItemsByCategory($categoryId, $storeID);
            //pr($itemList);die;
        }
        $this->set('itemList', $itemList);
    }

    /* ------------------------------------------------
      Function name:deleteMultipleItem()
      Description:Delete multiple item
      created:03/9/2015
      ----------------------------------------------------- */

    public function deleteMultipleItem() {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Item']['store_id'] = $this->Session->read('admin_store_id');
        $data['Item']['is_deleted'] = 1;
        if (!empty($this->request->data['Item']['id'])) {
            $filter_array = array_filter($this->request->data['Item']['id']);
            $i = 0;
            foreach ($filter_array as $k => $orderId) {
                $data['Item']['id'] = $orderId;
                $this->Item->saveItem($data);
                $i++;
            }
            $del = $i . "  " . "item deleted successfully.";
            $this->Session->setFlash(__($del), 'alert_success');
            $this->redirect(array('controller' => 'items', 'action' => 'index'));
        }
    }

    public function uploadfile() {
        $this->layout = 'admin_dashboard';
        if (!empty($this->request->data)) {
            $tmp = $this->request->data;
            $this->loadModel('Store');
            $this->loadModel('Category');
            $this->loadModel('Size');
            $this->loadModel('Type');
            $this->loadModel('StoreTax');
            if ($tmp['Item']['file']['error'] == 4) {
                $this->Session->setFlash(__('Your file contains error. Please retry uploading.'), 'alert_failed');
                $this->redirect($this->here);
            }
            $valid = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            if (!in_array($tmp['Item']['file']['type'], $valid)) {
                $this->Session->setFlash(__('You can only upload Excel file.'), 'alert_failed');
            } else if ($tmp['Item']['file']['error'] != 0) {
                $this->Session->setFlash(__('The file you uploaded contains errors.'), 'alert_failed');
            } else if ($tmp['Item']['file']['size'] > 20000000) {
                $this->Session->setFlash(__('The file size must be Max 20MB'), 'alert_failed');
            } else {
                ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large
                App::import('Vendor', 'PHPExcel');
                $objPHPExcel = new PHPExcel;
                $objPHPExcel = PHPExcel_IOFactory::load($tmp['Item']['file']['tmp_name']);
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                $real_data = array_values($sheetData);
                $i = 0;
                foreach ($real_data as $key => $row) {
                    $row = $this->Common->trimValue($row);
                    if ($key > 0) {
                        if (!empty($row['B']) && !empty($row['D'])) {
                            $storeId = $this->Session->read('admin_store_id');
                            $merchantId = $this->Session->read('admin_merchant_id');
                            if (!empty($storeId)) {
                                $categoryId = $this->Category->getCategoryByName($storeId, trim($row['D']));
                                if (!empty($categoryId)) {
                                    $itemName = trim($row['B']);
                                    if (!empty($row['A'])) {
                                        $isUniqueName = $this->Item->checkItemUniqueName($itemName, $storeId, $row['A'], $categoryId['Category']['id']);
                                    } else {
                                        $isUniqueName = $this->Item->checkItemUniqueName($itemName, $storeId, null, $categoryId['Category']['id']);
                                    }
                                    if ($isUniqueName) {
                                        $itemdata['name'] = $row['B'];
                                        $itemdata['category_id'] = $categoryId['Category']['id'];
                                        $itemdata['store_id'] = $storeId;
                                        $itemdata['merchant_id'] = $merchantId;
                                        $itemdata['description'] = $row['C'];
                                        
                                        
                                        if (!empty($row['E'])) {
                                            $itemdata['is_deliverable'] = $row['E'];
                                        } else {
                                            $itemdata['is_deliverable'] = 0;
                                        }

                                        if (!empty($row['F'])) {
                                            if ($row['F'] == 1) {
                                                $itemdata['is_seasonal_item'] = 1;
                                                if (!empty($row['H']) && !empty($row['I'])) {
                                                    $startDate = $this->Dateform->formatDate($row['H']);
                                                    $endDate = $this->Dateform->formatDate($row['I']);
                                                    $itemdata['start_date'] = $startDate;
                                                    $itemdata['end_date'] = $endDate;
                                                } else {
                                                    $itemdata['is_seasonal_item'] = 0;
                                                }
                                            } else {
                                                $itemdata['is_seasonal_item'] = 0;
                                            }
                                        } else {
                                            $itemdata['is_seasonal_item'] = 0;
                                        }
                                        
                                        // For Subs Default Price Applicable
                                        if (!empty($row['G'])) {
                                            $itemdata['default_subs_price'] = $row['G'];
                                        } else {
                                            $itemdata['default_subs_price'] = 0;
                                        }
                                        
                                        if (!empty($row['A'])) {
                                            $itemdata['id'] = $row['A'];
                                        } else {
                                            $itemdata['id'] = "";
                                            $this->Item->create();
                                        }

                                        if (!empty($row['N'])) {
                                            $itemdata['position'] = $row['N'];
                                        } else {
                                            $itemdata['position'] = 0;
                                        }
                                        if (!empty($row['O'])) {
                                            $itemdata['mandatory_item_units'] = $row['O'];
                                        } else {
                                            $itemdata['mandatory_item_units'] = 0;
                                        }
                                        
                                        $this->Item->saveItem($itemdata);
                                        if (!empty($row['A'])) {
                                            $itemID = $row['A'];
                                        } else {
                                            $itemID = $this->Item->getLastInsertId();
                                        }
                                        if ($itemID) {
                                            if (!empty($row['A'])) {
                                                $this->ItemType->deleteallItemType($itemID);
                                                $this->ItemPrice->deleteallItemPrice($itemID);
                                            }
                                            $priceArray = explode(',', $row['K']);
                                            if (!$priceArray[0]) {
                                                $priceArray[0] = 0;
                                            }
                                            $sizeflag = 0;
                                            $ItemTaxinfo = "";
                                            $itemTax = "";
                                            $taxvalue = trim($row['M']);
                                            if ($taxvalue) {
                                                $ItemTaxinfo = $this->StoreTax->storeTaxesBytaxvalue($taxvalue, $storeId);
                                                if ($ItemTaxinfo) {
                                                    $itemTax = $ItemTaxinfo['StoreTax']['id'];
                                                }
                                            }
                                            $itemPrice = array();
                                            if (!empty($row['J'])) {
                                                $sizeName = explode(',', $row['J']);
                                                foreach ($sizeName as $key => $sizename) {
                                                    $sizeId = $this->Size->getSizeIdByName($categoryId['Category']['id'], $storeId, trim($sizename));
                                                    if (!empty($sizeId)) {
                                                        if (!isset($priceArray[$key])) {
                                                            $priceArray[$key] = $priceArray[0];
                                                        }
                                                        $itemPrice['store_id'] = $storeId;
                                                        $itemPrice['size_id'] = $sizeId['Size']['id'];
                                                        $itemPrice['item_id'] = $itemID;
                                                        $itemPrice['price'] = trim($priceArray[$key]);
                                                        $itemPrice['merchant_id'] = $merchantId;
                                                        if (!empty($itemTax)) {
                                                            $itemPrice['store_tax_id'] = $itemTax;
                                                        } else {
                                                            $itemPrice['store_tax_id'] = '';
                                                        }
                                                        $this->ItemPrice->create();
                                                        $this->ItemPrice->saveItemPrice($itemPrice);
                                                        $sizeflag = 1;
                                                    }
                                                    /* else {

                                                      }
                                                     */
                                                }
                                                $itemPrice = array();
                                                if ($sizeflag == 0) {
                                                    $itemPrice['store_id'] = $storeId;
                                                    $itemPrice['item_id'] = $itemID;
                                                    $itemPrice['merchant_id'] = $merchantId;
                                                    $itemPrice['price'] = trim($priceArray[0]);
                                                    if (!empty($itemTax)) {
                                                        $itemPrice['store_tax_id'] = $itemTax;
                                                    } else {
                                                        $itemPrice['store_tax_id'] = '';
                                                    }
                                                    $this->ItemPrice->create();
                                                    $this->ItemPrice->saveItemPrice($itemPrice);
                                                }
                                            } else {
                                                $itemPrice['store_id'] = $storeId;
                                                $itemPrice['item_id'] = $itemID;
                                                $itemPrice['merchant_id'] = $merchantId;
                                                $itemPrice['price'] = trim($priceArray[0]);
                                                if (!empty($itemTax)) {
                                                    $itemPrice['store_tax_id'] = $itemTax;
                                                } else {
                                                    $itemPrice['store_tax_id'] = '';
                                                }
                                                $this->ItemPrice->create();
                                                $this->ItemPrice->saveItemPrice($itemPrice);
                                            }

                                            if (!empty($row['L'])) {
                                                $typeName = explode(',', $row['L']);
                                                foreach ($typeName as $key => $typeID) {
                                                    $typeId = $this->Type->getTypeIdByName($typeID, $storeId);
                                                    if (!empty($typeId)) {
                                                        $itemType['item_id'] = $itemID;
                                                        $itemType['store_id'] = $storeId;
                                                        $itemType['type_id'] = $typeId['Type']['id'];
                                                        $itemType['merchant_id'] = $merchantId;
                                                        $this->ItemType->create();
                                                        $this->ItemType->saveItemType($itemType);
                                                    }
                                                }
                                            }
                                            $i++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $this->Session->setFlash(__($i . ' ' . 'Item has been saved'), 'alert_success');
                $this->redirect(array("controller" => "items", "action" => "index"));
            }
        }
    }

    public function download() {
        $storeId = $this->Session->read('admin_store_id');
        $this->ItemPrice->bindModel(array('belongsTo' => array('Size' => array('fields' => 'size'), 'StoreTax' => array('fields' => 'tax_name'))));
        $this->ItemType->bindModel(array('belongsTo' => array('Type' => array('fields' => 'name'))));
        $this->Item->bindModel(array('hasMany' => array('ItemType' => array('conditions' => array('ItemType.is_deleted' => 0), 'fields' => array('type_id')), 'ItemPrice' => array('conditions' => array('ItemPrice.is_deleted' => 0), 'fields' => array('size_id', 'price', 'store_tax_id'))), 'belongsTo' => array('Category' => array('fields' => 'name', 'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1), 'type' => 'INNER'))));
        //$result = $this->Item->fetchItemList($storeId);
        $result = $this->Item->find('all', array('recursive' => 2, 'conditions' => array('Item.store_id' => $storeId, 'Item.is_deleted' => 0), 'order' => array('Category.name' => 'ASC', 'Item.position' => 'ASC')));


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
        $filename = 'Menu-Builder_' . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('Menu-Builder');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Item Name');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Description');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Category Name');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Is Deliverable');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Is Seasonal Item');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Subs Default Price Applicable');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Start Date');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'End Date');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Size Name');
        $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Price');
        $objPHPExcel->getActiveSheet()->setCellValue('L1', 'Preference Name');
        $objPHPExcel->getActiveSheet()->setCellValue('M1', 'Tax');
        $objPHPExcel->getActiveSheet()->setCellValue('N1', 'Position');
        $objPHPExcel->getActiveSheet()->setCellValue('O1', 'Item unit mandatory');

        //$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('J1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('K1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('L1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('M1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('N1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('O1')->applyFromArray($styleArray);

        $i = 2;

        foreach ($result as $data) {
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", trim($data['Item']['id']));
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", trim($data['Item']['name']));
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", trim($data['Item']['description']));
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", trim($data['Category']['name']));
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", trim($data['Item']['is_deliverable']));
            $objPHPExcel->getActiveSheet()->setCellValue("F$i", trim($data['Item']['is_seasonal_item']));
            $objPHPExcel->getActiveSheet()->setCellValue("G$i", trim($data['Item']['default_subs_price']));
            
            if (!empty($data['Item']['start_date']) && $data['Item']['start_date'] != 0000 - 00 - 00) {
                $startDate = date('m-d-Y', strtotime($data['Item']['start_date']));
            } else {
                $startDate = '';
            }
            $objPHPExcel->getActiveSheet()->setCellValue("H$i", trim($startDate));
            
            if (!empty($data['Item']['end_date']) && $data['Item']['end_date'] != 0000 - 00 - 00) {
                $endDate = date('m-d-Y', strtotime($data['Item']['end_date']));
            } else {
                $endDate = '';
            }
            $objPHPExcel->getActiveSheet()->setCellValue("I$i", trim($endDate));
            
            $size = '';
            $price = '';
            $itemTax = '';
            if (!empty($data['ItemPrice'])) {
                $itemSize = array();
                $itemPrice = array();
                foreach ($data['ItemPrice'] as $ItPrice) {
                    $itemSize[] = $ItPrice['Size']['size'];
                    $itemPrice[] = $ItPrice['price'];
                    if (!empty($ItPrice['StoreTax']['tax_name'])) {
                        $itemTax = $ItPrice['StoreTax']['tax_name'];
                    } else {
                        $itemTax = "";
                    }
                }
                $size = '';
                $price = '';
                $size = implode(',', $itemSize);
                $price = implode(',', $itemPrice);
            }
            $objPHPExcel->getActiveSheet()->setCellValue("J$i", $size);
            $objPHPExcel->getActiveSheet()->setCellValue("K$i", $price);
            
            $type = '';
            if (!empty($data['ItemType'])) {
                $itemType = array();
                foreach ($data['ItemType'] as $ItType) {
                    $itemType[] = $ItType['Type']['name'];
                }
                $type = '';
                $type = implode(',', $itemType);
            }
            $objPHPExcel->getActiveSheet()->setCellValue("L$i", $type);
            if (!empty($itemTax)) {
                $objPHPExcel->getActiveSheet()->setCellValue("M$i", $itemTax);
            }

            $objPHPExcel->getActiveSheet()->setCellValue("N$i", $data['Item']['position']);
            $objPHPExcel->getActiveSheet()->setCellValue("O$i", $data['Item']['mandatory_item_units']);
            $i++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    /* ------------------------------------------------
      Function name:updateAddOnsOrder()
      Description: Update the display order for add-ons according to items
      created Date:23/12/2015
      created By:Praveen Soni
      ----------------------------------------------------- */

    public function updateOrder() {
        $this->autoRender = false;
        if (isset($_GET) && !empty($_GET)) {
            foreach ($_GET as $key => $val) {
                $this->Item->updateAll(array('position' => $val), array('id' => $this->Encryption->decode($key)));
            }
        }
    }

    /* ------------------------------------------------
      Function name:setPrice()
      Description: set item price for prefrence, sub-preference, add-ons and sub-add-ons
      created Date:05/01/2016
      created By:Praveen Soni
      ----------------------------------------------------- */

    public function setPrice($itemId = null) {

        $this->layout = "admin_dashboard";
        $this->loadModel("ItemPrice");
        $this->loadModel("ItemType");
        $this->loadModel("Type");
        $this->loadModel("SubPreference");
        $this->loadModel("SubPreferencePrice");
        $this->loadModel("Topping");
        $this->loadModel("ToppingPrice");

        $merchant_id = $this->Session->read('admin_merchant_id');
        $storeId = $this->Session->read('admin_store_id');

        if (empty($itemId)) {
            $itemId = $this->params['pass'][0];
        }
        $itemId = $this->Encryption->decode($itemId);

        $this->ItemPrice->bindModel(
                array(
            'belongsTo' => array(
                'Size' => array(
                    'className' => 'Size',
                    'foreignKey' => 'size_id',
                    'conditions' => array('Size.is_deleted' => 0, 'Size.is_active' => 1, 'Size.store_id' => $storeId),
                    'fields' => array('Size.id', 'Size.size', 'Size.store_id'),
                    'type' => 'INNER',
                ),
            ),
                ), false
        );

        $this->Item->bindModel(
                array(
            'hasMany' => array(
                'ItemPrice' => array(
                    'className' => 'ItemPrice',
                    'foreignKey' => 'item_id',
                    'conditions' => array('ItemPrice.is_deleted' => 0, 'ItemPrice.is_active' => 1, 'ItemPrice.item_id' => $itemId, 'ItemPrice.store_id' => $storeId),
                    'fields' => array('ItemPrice.id', 'ItemPrice.size_id', 'ItemPrice.store_id'),
                    'type' => 'INNER',
                )
            ),
                ), false
        );

        $itemDetail = $this->Item->find('first', array('fields' => array('Item.id', 'Item.name', 'Item.store_id'), 'conditions' => array('Item.id' => $itemId, 'Item.store_id' => $storeId, 'Item.is_deleted' => 0), 'recursive' => 2));
        $this->set("itemDetail", $itemDetail);
        //pr($itemDetail); exit;

        if (!empty($this->request->data)) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            if ($this->request->data['Item']['size_id'] != "") {

                $itemData = $this->request->data['Item'];

                # Update Sub-Preference Price
                if (isset($this->request->data['SubPreferencePrice']['EditPrice'])) {
                    $subPreferenceEditPrice = $this->request->data['SubPreferencePrice']['EditPrice'];
                    foreach ($subPreferenceEditPrice as $key => $value) {
                        $value = !empty($value) ? $value : 0;
                        $this->SubPreferencePrice->updateAll(array('price' => $value), array('id' => $key));
                    }

                    $this->SubPreferencePrice->id = '';
                }

                # Add Sub-Preference Price
                if (isset($this->request->data['SubPreferencePrice']['AddPrice'])) {
                    $subPreferenceAddPrice = $this->request->data['SubPreferencePrice']['AddPrice'];
                    ;
                    foreach ($subPreferenceAddPrice as $key => $value) {
                        if ($value != '') {
                            $subPreferenceData['store_id'] = $storeId;
                            $subPreferenceData['item_id'] = $itemData['id'];
                            $subPreferenceData['size_id'] = $itemData['size_id'];
                            $subPreferenceData['sub_preference_id'] = $key;
                            $subPreferenceData['price'] = $value;
                            $this->SubPreferencePrice->validate = false;
                            $this->SubPreferencePrice->create();
                            $this->SubPreferencePrice->save($subPreferenceData);
                        }
                    }
                }

                # Edit Sub-Add-Ons Price
                if (isset($this->request->data['SubAddOnsPrice']['EditPrice'])) {
                    $subAddOnsEditPrice = $this->request->data['SubAddOnsPrice']['EditPrice'];
                    foreach ($subAddOnsEditPrice as $key => $value) {
                        $value = !empty($value) ? $value : 0;
                        $this->ToppingPrice->updateAll(array('price' => $value), array('id' => $key));
                    }

                    $this->ToppingPrice->id = '';
                }

                # Add Sub-Add-Ons Price
                if (isset($this->request->data['SubAddOnsPrice']['AddPrice'])) {
                    $subAddOnsAddPrice = $this->request->data['SubAddOnsPrice']['AddPrice'];
                    ;
                    foreach ($subAddOnsAddPrice as $key => $value) {
                        if ($value != '') {
                            $subAddOnsAddPriceData['store_id'] = $storeId;
                            $subAddOnsAddPriceData['item_id'] = $itemData['id'];
                            $subAddOnsAddPriceData['size_id'] = $itemData['size_id'];
                            $subAddOnsAddPriceData['topping_id'] = $key;
                            $subAddOnsAddPriceData['price'] = $value;
                            $this->ToppingPrice->validate = false;
                            $this->ToppingPrice->create();
                            $this->ToppingPrice->save($subAddOnsAddPriceData);
                        }
                    }
                }
                $subPreferencePriceDetail = $this->SubPreferencePrice->find('all', array('conditions' => array('SubPreferencePrice.item_id' => $itemId, 'SubPreferencePrice.size_id' => $itemData['size_id'], 'SubPreferencePrice.store_id' => $storeId, 'SubPreferencePrice.is_active' => 1, 'SubPreferencePrice.is_deleted' => 0)));
                $this->set("subPreferencePriceDetail", $subPreferencePriceDetail);
                //pr($subPreferencePriceDetail);

                $subAddOnsPriceDetail = $this->ToppingPrice->find('all', array('conditions' => array('ToppingPrice.item_id' => $itemId, 'ToppingPrice.size_id' => $itemData['size_id'], 'ToppingPrice.store_id' => $storeId, 'ToppingPrice.is_active' => 1, 'ToppingPrice.is_deleted' => 0)));
                $this->set("subAddOnsPriceDetail", $subAddOnsPriceDetail);
                //pr($subAddOnsPriceDetail);
                $this->Session->setFlash(__('Price has been successfully updated.'), 'alert_success');
            } else {
                $this->Session->setFlash(__('Please select item size.'), 'alert_failed');
            }
        }



        $this->Type->bindModel(
                array(
            'hasMany' => array(
                'SubPreference' => array(
                    'className' => 'SubPreference',
                    'foreignKey' => 'type_id',
                    'conditions' => array('SubPreference.is_deleted' => 0, 'SubPreference.is_active' => 1, 'SubPreference.store_id' => $storeId),
                    'fields' => array('SubPreference.id', 'SubPreference.name')
                )
            ),
                ), false
        );

        $this->ItemType->bindModel(
                array(
            'belongsTo' => array(
                'Type' => array(
                    'className' => 'Type',
                    'foreignKey' => 'type_id',
                    'conditions' => array('Type.is_deleted' => 0, 'Type.is_active' => 1),
                    'fields' => array('Type.id', 'Type.name')
                )
            ),
                ), false
        );

        $itemPreference = $this->ItemType->find('all', array('recursive' => 2, 'fields' => array('ItemType.id', 'ItemType.type_id'), 'conditions' => array('ItemType.item_id' => $itemId, 'ItemType.store_id' => $storeId, 'ItemType.is_active' => 1, 'ItemType.is_deleted' => 0)));
        $this->set("itemPreference", $itemPreference);

        $this->Topping->bindModel(
                array('hasMany' => array(
                        'SubAddOns' => array(
                            'className' => 'Topping',
                            'foreignKey' => 'addon_id',
                            'type' => 'inner',
                            'conditions' => array('SubAddOns.item_id' => $itemId, 'SubAddOns.is_addon_category' => 0, 'SubAddOns.is_active' => 1, 'SubAddOns.is_deleted' => 0, 'SubAddOns.store_id' => $storeId),
                            'fields' => array('SubAddOns.id', 'SubAddOns.name',),
                        )
                    ))
        );
        $itemAddOns = $this->Topping->find('all', array('recursive' => 2, 'fields' => array('Topping.id', 'Topping.name'), 'conditions' => array('Topping.item_id' => $itemId, 'Topping.store_id' => $storeId, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.is_addon_category' => 1)));
        $this->set("itemAddOns", $itemAddOns);
    }

    /* ------------------------------------------------
      Function name:setPrice()
      Description: set item price for prefrence, sub-preference, add-ons and sub-add-ons
      created Date:05/01/2016
      created By:Praveen Soni
      ----------------------------------------------------- */

    public function ajaxSetPrice($itemId = null, $sizeId = null, $storeId = null) {

        //$this->layout="ajax";
        if ($this->request->is('ajax')) {
            $itemId = $this->request->data['itemId'];
            $sizeId = $this->request->data['sizeId'];
        }


        $this->layout = null;
        $this->loadModel("ItemPrice");
        $this->loadModel("ItemType");
        $this->loadModel("Type");
        $this->loadModel("SubPreference");
        $this->loadModel("SubPreferencePrice");
        $this->loadModel("Topping");
        $this->loadModel("ToppingPrice");

        $storeId = $this->Session->read('admin_store_id');


        $subPreferencePriceDetail = $this->SubPreferencePrice->find('all', array('conditions' => array('SubPreferencePrice.item_id' => $itemId, 'SubPreferencePrice.size_id' => $sizeId, 'SubPreferencePrice.store_id' => $storeId, 'SubPreferencePrice.is_active' => 1, 'SubPreferencePrice.is_deleted' => 0)));
        $this->set("subPreferencePriceDetail", $subPreferencePriceDetail);
        //pr($subPreferencePriceDetail);

        $subAddOnsPriceDetail = $this->ToppingPrice->find('all', array('conditions' => array('ToppingPrice.item_id' => $itemId, 'ToppingPrice.size_id' => $sizeId, 'ToppingPrice.store_id' => $storeId, 'ToppingPrice.is_active' => 1, 'ToppingPrice.is_deleted' => 0)));
        $this->set("subAddOnsPriceDetail", $subAddOnsPriceDetail);
        //pr($subAddOnsPriceDetail);



        $this->Type->bindModel(
                array(
            'hasMany' => array(
                'SubPreference' => array(
                    'className' => 'SubPreference',
                    'foreignKey' => 'type_id',
                    'conditions' => array('SubPreference.is_deleted' => 0, 'SubPreference.is_active' => 1, 'SubPreference.store_id' => $storeId),
                    'fields' => array('SubPreference.id', 'SubPreference.name')
                )
            ),
                ), false
        );

        $this->ItemType->bindModel(
                array(
            'belongsTo' => array(
                'Type' => array(
                    'className' => 'Type',
                    'foreignKey' => 'type_id',
                    'conditions' => array('Type.is_deleted' => 0, 'Type.is_active' => 1),
                    'fields' => array('Type.id', 'Type.name')
                )
            ),
                ), false
        );

        $itemPreference = $this->ItemType->find('all', array('recursive' => 2, 'fields' => array('ItemType.id', 'ItemType.type_id'), 'conditions' => array('ItemType.item_id' => $itemId, 'ItemType.store_id' => $storeId, 'ItemType.is_active' => 1, 'ItemType.is_deleted' => 0)));
        $this->set("itemPreference", $itemPreference);

        $this->Topping->bindModel(
                array('hasMany' => array(
                        'SubAddOns' => array(
                            'className' => 'Topping',
                            'foreignKey' => 'addon_id',
                            'type' => 'inner',
                            'conditions' => array('SubAddOns.item_id' => $itemId, 'SubAddOns.is_addon_category' => 0, 'SubAddOns.is_active' => 1, 'SubAddOns.is_deleted' => 0, 'SubAddOns.store_id' => $storeId),
                            'fields' => array('SubAddOns.id', 'SubAddOns.name',),
                        )
                    ))
        );
        $itemAddOns = $this->Topping->find('all', array('recursive' => 2, 'fields' => array('Topping.id', 'Topping.name'), 'conditions' => array('Topping.item_id' => $itemId, 'Topping.store_id' => $storeId, 'Topping.is_active' => 1, 'Topping.is_deleted' => 0, 'Topping.is_addon_category' => 1)));
        $this->set("itemAddOns", $itemAddOns);
    }

    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('Item');
            $this->Item->bindModel(
                    array(
                'belongsTo' => array(
                    'Category' => array(
                        'className' => 'Category',
                        'foreignKey' => 'category_id',
                        'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                        'fields' => array('id', 'name'),
                        'type' => 'INNER'
                    )
                )
                    ), false
            );
            $storeID = $this->Session->read('admin_store_id');
            $searchData = $this->Item->find('all', array('fields' => array('Item.name', 'Item.description', 'Category.name'), 'conditions' => array('OR' => array('Item.description LIKE' => '%' . $_GET['term'] . '%', 'Item.name LIKE' => '%' . $_GET['term'] . '%', 'Category.name LIKE' => '%' . $_GET['term'] . '%'), 'Item.is_deleted' => 0, 'Item.store_id' => $storeID)));
            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    $new_array[] = array('label' => $val['Item']['name'], 'value' => $val['Item']['name'], 'desc' => $val['Item']['name'] . "-" . $val['Category']['name'] . '-' . $val['Item']['description']);
                };
            }
            echo json_encode($new_array);
        } else {
            exit;
        }
    }

}
