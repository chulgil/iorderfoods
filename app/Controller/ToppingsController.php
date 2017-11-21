<?php

App::uses('StoreAppController', 'Controller');

class ToppingsController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'Form', 'Common');
    public $uses = array('Topping', 'Item', 'ItemPrice', 'ItemType', 'Size', 'Category', 'ItemDefaultTopping');

    public function beforeFilter() {
        parent::beforeFilter();
        // pr($this->params);
        //die('test');
    }

    /* ------------------------------------------------
      Function name:index()
      Description:List Menu Items
      created:5/8/2015
      ----------------------------------------------------- */

    public function index($clearAction = null) {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');
        $value = "";
        $criteria = "Topping.store_id =$storeID AND Topping.is_deleted=0 AND Topping.is_addon_category =1";
        $order = '';
        $pagingFlag = true;

        //if(isset($this->params['named']['sort']) || isset($this->params['named']['page'])){
        if ($this->Session->read('ToppingSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('ToppingSearchData'), true);
        } else {
            $this->Session->delete('ToppingSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }

        if (!empty($this->request->data)) {
            if (isset($this->request->data['Topping']['no']) && $this->request->data['Topping']['no']) {

                if (array_filter($this->request->data['Topping']['no'])) {

                    // pr($this->request->data['Topping']['no']);die;
                    $result = $this->Topping->deleteMultipleToppings($this->request->data['Topping']['no'], $storeID);
                    $del = $result . "  " . "topping deleted successfully.";

                    $this->Session->setFlash(__($del), 'alert_success');
                }
            }
            $this->Session->write('ToppingSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['Topping']['keyword'])) {
                $value = trim($this->request->data['Topping']['keyword']);
                $criteria .= " AND (Topping.name LIKE '%" . $value . "%' OR Item.name LIKE '%" . $value . "%')";
            }
            if (!empty($this->request->data['Topping']['item_id'])) {
                $ItemID = trim($this->request->data['Topping']['item_id']);
                $criteria .= " AND (Topping.item_id =$ItemID)";
                $order = 'Topping.position ASC';
                $pagingFlag = false;
            }
            if (isset($this->request->data['Topping']['is_active']) && $this->request->data['Topping']['is_active'] != '') {
                $active = trim($this->request->data['Topping']['is_active']);
                $criteria .= " AND (Topping.is_active =$active)";
            }

            //Check if set or unset topping ids are in request
            if (isset($this->request->data['Topping']['id']) && $this->request->data['Topping']['id'] && $this->request->data['Topping']['item_id'] && isset($this->request->data['Topping']['item_id'])) {
                $ToppingId = $this->request->data['Topping']['id'];

                if ($ItemID) {
                    if (isset($this->request->data['set'])) {            // Set Default Toppings
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
                        foreach ($ToppingId as $key => $topid) {
                            if ($topid) {
                                //$this->ItemDefaultTopping->deleteallDefaultTopping($ItemID,$topid);
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
                    $this->Session->setFlash(__("Please select Item"), 'alert_failed');
                }
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
                    'fields' => array('id', 'name','category_id')
                ), 'Category' => array(
                    'className' => 'Category',
                    'foreignKey' => 'category_id',
                    'type' => 'inner',
                    'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                    'fields' => array('id', 'name')
                )
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
        //pr($this->request->data);
        if ($order == '') {
            $order = 'Topping.created DESC';
        }

        $toppingdetail = '';
        if ($pagingFlag) {
            $this->paginate = array('conditions' => array($criteria), 'order' => $order);
            $toppingdetail = $this->paginate('Topping');
        } else {
            //$toppingdetail=$this->Topping->find('all',array('conditions'=>array($criteria),'order'=>$order));
            $this->paginate = array('conditions' => array($criteria), 'order' => $order);
            $toppingdetail = $this->paginate('Topping');
        }
        $this->set('list', $toppingdetail);
        $this->set('pagingFlag', $pagingFlag);

        $newToppingList = $this->Topping->find('all', array('conditions' => array('Topping.store_id' => $storeID, 'Topping.is_deleted' => 0, 'Topping.is_addon_category' => 1), 'group' => array('Topping.item_id')));
        $nList = array();
        if (!empty($newToppingList)) {
            foreach ($newToppingList as $iList) {
                if (!empty($iList['Item']) && !empty($iList['Category'])) {
                    $nList[$iList['Item']['id']] = $iList['Item']['name'];
                }
            }
        }
        $this->set('customTopping', $nList);
        $this->loadModel('Category');
        $this->Item->bindModel(array(
            'belongsTo' => array('Category')
        ));
        //$itemList=$this->Item->getallItemsByStore($storeID);
        $itemList = $this->Item->find('list', array(
            'fields' => array('Item.id', 'Item.name'),
            'conditions' => array('Item.store_id' => $storeID, 'Item.is_deleted' => 0, 'Item.is_active' => 1, 'Category.is_deleted' => 0, 'Category.is_active' => 1),
            'recursive' => 1
        ));
        $this->set('itemList', $itemList);
        //$categoryList=$this->Category->getCategoryList($storeID);
        //$this->set('categoryList',$categoryList);
        $this->set('keyword', $value);
        $categoryList = $this->Category->getCategoryListHasTopping($storeID);
        $this->set('categoryList', $categoryList);
        //$this->set('itempost',$itempost);
    }

    /* ------------------------------------------------
      Function name:addTopping()
      Description:Add Item Toppings
      created:5/8/2015
      ----------------------------------------------------- */

    public function addTopping() {

        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addTopping')) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');

        if ($this->request->is('post') && $this->request->data && !empty($this->request->data['Topping']['name1'])) {
//          die;
            $this->request->data = $this->Common->trimValue($this->request->data);
            $itemIds = $this->request->data['Topping']['item_id'];
            $itemCount = count($itemIds);
            if ($itemCount) {
                $toppingdata = array();
                // $priceArray=explode(',',$this->request->data['Topping']['price']);
                // if(!$priceArray[0]){
                //    $priceArray[0]=0;
                // }
                $toppingName = trim($this->request->data['Topping']['name1']);
                $topping = 0;
                $successToppingName = '';
                $failedToppingName = '';
                $categoryID = $this->request->data['Category']['id1'];
                foreach ($itemIds as $key => $itemId) {
                    if ($this->Topping->checkToppingUniqueName($toppingName, $storeID, $itemId)) {
                        //   if(!isset($priceArray[$key])){
                        //      $priceArray[$key]=$priceArray[0];
                        //   }
                        $toppingdata['name'] = trim($toppingName);
                        $toppingdata['item_id'] = $itemId;
                        $toppingdata['is_active'] = $this->request->data['Topping']['is_active1'];
                        $toppingdata['min_value'] = $this->request->data['Topping']['min_value'];
                        $toppingdata['max_value'] = $this->request->data['Topping']['max_value'];
                        //  $toppingdata['price']=$priceArray[$key];
                        $toppingdata['price'] = 0;
                        $toppingdata['store_id'] = $storeID;
                        $toppingdata['merchant_id'] = $merchant_id;
                        $toppingdata['category_id'] = $categoryID;
                        $this->Topping->create();
                        $topping = $this->Topping->saveTopping($toppingdata);
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
                    $this->request->data = '';
                    $this->Session->setFlash(__($message), 'alert_success');
                    $this->redirect(array('controller' => 'toppings', 'action' => 'index'));
                } else {
                    $this->Session->setFlash(__("Some Problem occured"), 'alert_failed');
                    $this->redirect(array('controller' => 'toppings', 'action' => 'index'));
                }
            }
        }

        $itempost = 0;
        $itemList = '';
        if (isset($this->request->data['Topping']['item_id1'])) {
            $itempost = 1;
        }
        if (isset($this->request->data['Topping']['item_id1'])) {
            $itemList = $this->Item->getItemsByCategory($this->request->data['Category']['id1'], $storeID);
        }
        $this->set('itemList', $itemList);
        $categoryList = $this->Category->getCategoryListHasTopping($storeID);
        $this->set('categoryList', $categoryList);
        $this->set('itempost', $itempost);
    }

    /* ------------------------------------------------
      Function name:editTopping()
      Description:Edit Item Toppings
      created:5/8/2015
      ----------------------------------------------------- */

    public function editTopping($EncryptedToppingID = null) {
        //pr($this->params);
        //die('test');
        $this->layout = "admin_dashboard";
        $merchant_id = $this->Session->read('admin_merchant_id');
        $storeId = $this->Session->read('admin_store_id');
        $toppingId = $this->Encryption->decode($EncryptedToppingID);

        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $toppingName = trim($this->request->data['Topping']['name']);
            $itemId = trim($this->request->data['Topping']['item_id']);
            $toppingId = trim($this->request->data['Topping']['id']);
            $categoryID = $this->request->data['Category']['id'];
            if ($this->Topping->checkToppingUniqueName($toppingName, $storeId, $itemId, $toppingId)) {
                $toppingdata['name'] = trim($toppingName);
                $toppingdata['item_id'] = $itemId;
                //$toppingdata['is_active']=$this->request->data['Topping']['is_active'];
                if ($this->request->data['Topping']['is_active']) {
                    $toppingdata['is_active'] = 1;
                } else {
                    $toppingdata['is_active'] = 0;
                }
                //$priceArray=explode(',',trim($this->request->data['Topping']['price']));
                //if(!$priceArray[0]){
                //   $priceArray[0]=0;
                //}
                //$toppingdata['price']=$priceArray[0];
                $toppingdata['price'] = 0;
                $toppingdata['store_id'] = $storeId;
                $toppingdata['merchant_id'] = $merchant_id;
                $toppingdata['category_id'] = $categoryID;
                $toppingdata['id'] = $toppingId;
                $toppingdata['min_value'] = $this->request->data['Topping']['min_value'];
                $toppingdata['max_value'] = $this->request->data['Topping']['max_value'];
                $topping = $this->Topping->saveTopping($toppingdata);
                $this->Session->setFlash(__("Add-on Details Updated"), 'alert_success');
                $this->redirect(array('controller' => 'Toppings', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("Add-on Name Already Exists for Item"), 'alert_failed');
            }
        }
        //echo $toppingId.','.$storeId;
        $toppingsDetails = $this->Topping->fetchToppingDetails($toppingId, $storeId); //pr($toppingsDetails);die;
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
        //pr($this->request->data);//die;
    }

    /* ------------------------------------------------
      Function name:activateTopping()
      Description:Active/deactive Topping
      created:5/8/2015
      ----------------------------------------------------- */

    public function activateTopping($EncryptedtoppingID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $toppingid = $this->Encryption->decode($EncryptedtoppingID);
        $data['Topping']['store_id'] = $this->Session->read('admin_store_id');
        $data['Topping']['id'] = $toppingid;
        $data['Topping']['is_active'] = $status;
        if ($this->Topping->saveTopping($data)) {
            if ($status) {
                $SuccessMsg = "Add-on Activated";
            } else {
                $SuccessMsg = "Add-on Deactivated and Add-on will not available at Add-on List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'Toppings', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'Toppings', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteTopping()
      Description:Delete Topping
      created:5/8/2015
      ----------------------------------------------------- */

    public function deleteTopping($EncryptedtoppingID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $toppingid = $this->Encryption->decode($EncryptedtoppingID);
        //pr($toppingid);die;
        $data['Topping']['store_id'] = $this->Session->read('admin_store_id');
        $data['Topping']['id'] = $toppingid;
        $data['Topping']['is_deleted'] = 1;
        if ($this->Topping->saveTopping($data)) {
            $this->Session->setFlash(__("Add-on deleted"), 'alert_success');
            $this->redirect(array('controller' => 'Toppings', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'Toppings', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:addSubTopping()
      Description:Add sub toppings
      created:5/8/2015
      ----------------------------------------------------- */

    public function addSubTopping() {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addSubTopping')) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $storeId = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');
        $successToppingName = '';
        $failedToppingName = '';
        $this->layout = "admin_dashboard";
        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            //  if($this->Topping->checkSubaddonmUniqueName($this->request->data['Topping']['name'],$this->request->data['Topping']['id'],$storeId)){
            if (!empty($this->request->data['Topping']['item_id'])) {
                $toppingData = $this->Topping->getItemsBytoppingID($this->request->data['Topping']['id'], $storeId, $this->request->data['Category']['id']);
                foreach ($toppingData as $key => $value) {
                    if (in_array($value['Topping']['item_id'], $this->request->data['Topping']['item_id'])) {
                        if ($this->Topping->checkToppingexistsOnItem(trim($this->request->data['Topping']['name']), $storeId, $value['Topping']['item_id'], $value['Topping']['id'])) {
                            $data['Topping']['item_id'] = $value['Topping']['item_id'];
                            $data['Topping']['store_id'] = $storeId;
                            $data['Topping']['merchant_id'] = $merchant_id;
                            $data['Topping']['is_active'] = $this->request->data['Topping']['is_active1'];
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
                            $itemNamesuccess = $this->Item->getItemName($value['Topping']['item_id'], $storeId);
                            if ($successToppingName == '') {
                                $successToppingName.=$itemNamesuccess['Item']['name'];
                            } else {
                                $successToppingName.=',' . $itemNamesuccess['Item']['name'];
                            }
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
                        } else {
                            $itemNamefailed = $this->Item->getItemName($value['Topping']['item_id'], $storeId);
                            if ($failedToppingName == '') {
                                $failedToppingName.=$itemNamefailed['Item']['name'];
                            } else {
                                $failedToppingName.=',' . $itemNamefailed['Item']['name'];
                            }
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
                    $this->redirect(array('controller' => 'toppings', 'action' => 'listSubTopping', 'clear'));
                } else {
                    $this->Session->setFlash(__("Some Problem occured"), 'alert_failed');
                    $this->redirect(array('controller' => 'toppings', 'action' => 'listSubTopping', 'clear'));
                }
            } else {
                $this->Session->setFlash(__("Please select item"), 'alert_failed');
                $this->redirect(array('controller' => 'toppings', 'action' => 'listSubTopping', 'clear'));
            }

            //$this->Session->setFlash(__("Sub Add-ons created successfully."),'alert_success');
            //$this->redirect(array('controller' => 'toppings', 'action' => 'listSubTopping','clear'));
            //}else{
            //$this->Session->setFlash(__("Sub Add-on Name Already Exists for Add-ons"),'alert_failed');
            // }
        }
        $this->loadModel('Topping');
        $addonList = $this->Topping->getAddons($storeId);
        $this->set('addonList', $addonList);
        $categoryList = $this->Category->getCategoryListHasTopping($storeId);
        $this->set('categoryList', $categoryList);
        $addonpost = 0;
        $this->set('addonpost', $addonpost);
    }

    /* ------------------------------------------------
      Function name:listSubTopping()
      Description:List of sub Add-ons
      created:5/8/2015
      ----------------------------------------------------- */

    public function listSubTopping($clearAction = null) {
        //pr($this->request->data);die;
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addSubTopping')) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');

        /*         * ****start******* */
        $value = "";
        $criteria = "Topping.store_id =$storeID AND Topping.is_deleted=0 AND Topping.is_addon_category=0";
        $order = '';
        $pagingFlag = true;
        $addOnsCriteria = "Topping.store_id =$storeID AND Topping.is_deleted=0 AND Topping.is_addon_category=1";
        $addOnsCriteriaAdditional = '';

        if ($this->Session->read('ToppingSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('ToppingSearchData'), true);
        } else {
            $this->Session->delete('ToppingSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }

        if (!empty($this->request->data)) {
            $this->Session->write('ToppingSearchData', json_encode($this->request->data));
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
            if (!empty($this->request->data['Topping']['category_id'])) {
                $categoryid = $this->request->data['Topping']['category_id'];
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
            if (isset($this->request->data['Topping']['is_active']) && $this->request->data['Topping']['is_active'] != '') {
                $active = trim($this->request->data['Topping']['is_active']);
                $criteria .= " AND (Topping.is_active =$active)";
            }
            $ItemID = "";
            if (!empty($this->request->data['Topping']['item_id'])) {
                $ItemID = trim($this->request->data['Topping']['item_id']);
                $criteria .= " AND (Topping.item_id =$ItemID)";
                $addOnsCriteriaAdditional = " AND (Topping.item_id =$ItemID)";
                $pagingFlag = false;
            }

            if (isset($this->request->data['Topping']['no']) && $this->request->data['Topping']['no'] && isset($this->request->data['subaddondelete'])) {
                if (array_filter($this->request->data['Topping']['no'])) {
                    // pr($this->request->data['Topping']['no']);die;
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
                                //$this->ItemDefaultTopping->deleteallDefaultTopping($ItemID,$topid);
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

                    if (isset($this->request->data['set'])) {            // Set Default Toppings
                        if ($this->request->data['Topping']['id']) {
                            $ToppingId = $this->request->data['Topping']['id'];
                            foreach ($ToppingId as $key => $topid) {
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
                                    //pr($defaulttoppingdata);die;
                                    $this->ItemDefaultTopping->saveDefaultTopping($defaulttoppingdata);
                                }
                            }
                            $this->Session->setFlash(__("Add-ons are successfully assigned as default to Item"), 'alert_success');
                        }
                    }
                    if (isset($this->request->data['unset'])) {         // unset Toppings
                        if ($this->request->data['Topping']['id']) {
                            $ToppingId = $this->request->data['Topping']['id'];
                            foreach ($ToppingId as $key => $topid) {
                                if ($topid) {
                                    //$this->ItemDefaultTopping->deleteallDefaultTopping($ItemID,$topid);
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
                }
                unset($this->request->data['set']);
                unset($this->request->data['unset']);
                unset($this->request->data['Topping']['no']);
                unset($this->request->data['Topping']['id']);
            }
//            $this->loadModel('Topping');
//            $addonList = $this->Topping->getAddons($storeID);
//            $this->set('addonList', $addonList);
//            $categoryList = $this->Category->getCategoryListHasTopping($storeID);
//            $this->set('categoryList', $categoryList);
//            $addonpost = 0;
//            $this->set('addonpost', $addonpost);
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
                )
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
            //$itemdetail=$this->Topping->find('all',array('conditions'=>array($criteria),'order'=>$order));
            $this->paginate = array('conditions' => array($criteria), 'order' => $order);
            $itemdetail = $this->paginate('Topping');
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
//        $this->Item->bindModel(array(
//            'belongsTo' => array('Category')
//        ));
//        //$itemList=$this->Item->getallItemsByStore($storeID);
//        $itemList = $this->Item->find('list', array(
//            'fields' => array('Item.id', 'Item.name'),
//            'conditions' => array('Item.store_id' => $storeID, 'Item.is_deleted' => 0, 'Item.is_active' => 1, 'Category.is_deleted' => 0, 'Category.is_active' => 1),
//            'recursive' => 1
//        ));
//        $this->set('itemList', $itemList);
        //$categoryList = $this->Category->getCategoryListHasTopping($storeID);
        $this->set('categoryList', $categoryList);
        $this->set('categoryListHasSubAddons', $categoryListHasSubAddons);
    }

    /* ------------------------------------------------
      Function name:editSubTopping()
      Description:Edit sub toppings
      created:22/09/2015
      ----------------------------------------------------- */

    public function editSubTopping($EncryptedSubToppingID = null) {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addSubTopping')) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $toppingId = $this->Encryption->decode($EncryptedSubToppingID);

        $storeId = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');

        $this->layout = "admin_dashboard";
        if ($this->request->data) {
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

                    $this->Topping->create();
                    $topping = $this->Topping->saveTopping($data);
                    if ($this->request->data['ItemDefaultTopping']['id']) {
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
                    $this->redirect(array('controller' => 'toppings', 'action' => 'listSubTopping', 'clear'));
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
            ),
            'belongsTo' => array(
                'Category' => array(
                    'className' => 'Category',
                    'foreignKey' => 'category_id',
                    'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                    'fields' => array('id', 'name'),
                    'type' => 'inner'
                )
            )
                ), false
        );

        $toppingsDetails = $this->Topping->fetchToppingDetails($toppingId, $storeId);
        $this->request->data = $toppingsDetails;
        $this->request->data['Category']['id'] = $toppingsDetails['Topping']['category_id'];
        $this->loadModel('Topping');
        $addonList = $this->Topping->getAddons($storeId);
        $addonListt = $this->Topping->getAddonsForEdit($storeId, $toppingsDetails['Topping']['addon_id'], $this->request->data['Category']['id']);

        $addonList = $this->Topping->getAddonsByCategory($storeId, $this->request->data['Category']['id']);
        $arrayaddon = array();
        foreach ($addonList as $key => $value) {
            if ($addonListt['Topping']['name'] == $value) {
                $arrayaddon[$addonListt['Topping']['id']] = $value;
            } else {
                $arrayaddon[$key] = $value;
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

        //pr($this->request->data);die;
//	 $defaultarray=array();
//      if($this->request->data['Topping']['id']){
//	 $defaultItems=$this->ItemDefaultTopping->getDefaultItems($this->request->data['Topping']['id']);
//	 if($defaultItems){
//	    foreach($defaultItems as $key =>$defaultvalue){
//	       //pr($defaultvalue);die;
//	       if($this->request->data['Topping']['item_id']!=$defaultvalue['ItemDefaultTopping']['item_id']){
//		  $defaultarray[]=$defaultvalue['ItemDefaultTopping']['item_id'];
//	       }
//	    }
//	 }
//      }
//      if($Items){
//	 foreach($Items as $key => $value){
//	    if(!empty($value['Item'])){
//	       if(!in_array($value['Item']['id'],$defaultarray)){
//		  $Itemslist[$value['Item']['id']]=$value['Item']['name'];
//	       }
//	    }
//	 }
//	 $itempost=1;
//      }


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

    /* ------------------------------------------------
      Function name:activateSubTopping()
      Description:Active/deactive Sub Topping
      created:22/09/2015
      ----------------------------------------------------- */

    public function activateSubTopping($EncryptedtoppingID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $toppingid = $this->Encryption->decode($EncryptedtoppingID);
        $data['Topping']['store_id'] = $this->Session->read('admin_store_id');
        $data['Topping']['id'] = $toppingid;
        $data['Topping']['is_active'] = $status;
        if ($this->Topping->saveTopping($data)) {
            if ($status) {
                $SuccessMsg = "Sub Add-on Activated";
            } else {
                $SuccessMsg = "Sub Add-on Deactivated and Add-on will not available at Add-on List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'Toppings', 'action' => 'listSubTopping'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'Toppings', 'action' => 'listSubTopping'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteSubTopping()
      Description:Delete Sub Topping
      created:22/09/2015
      ----------------------------------------------------- */

    public function deleteSubTopping($EncryptedtoppingID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $toppingid = $this->Encryption->decode($EncryptedtoppingID);
        //pr($toppingid);die;
        $data['Topping']['store_id'] = $this->Session->read('admin_store_id');
        $data['Topping']['id'] = $toppingid;
        $data['Topping']['is_deleted'] = 1;
        if ($this->Topping->saveTopping($data)) {
            $this->Session->setFlash(__("Sub Add-on deleted"), 'alert_success');
            $this->redirect(array('controller' => 'Toppings', 'action' => 'listSubTopping'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'Toppings', 'action' => 'listSubTopping'));
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
        $data['Topping']['store_id'] = $this->Session->read('admin_store_id');
        $data['Topping']['is_deleted'] = 1;
        if (!empty($this->request->data['Topping']['id'])) {
            $filter_array = array_filter($this->request->data['Topping']['id']);
            $i = 0;
            foreach ($filter_array as $k => $toppingId) {
                $data['Topping']['id'] = $toppingId;
                $this->Topping->saveTopping($data);
                $i++;
            }
            $del = $i . "  " . "Sub Add-ons deleted successfully.";
            $this->Session->setFlash(__($del), 'alert_success');
            $this->redirect(array('controller' => 'Toppings', 'action' => 'listSubTopping'));
        }
    }

    public function uploadfile() {
        $this->layout = 'admin_dashboard';
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
                $i = 0;
                foreach ($real_data as $key => $row) {
                    $row = $this->Common->trimValue($row);
                    if ($key > 0) {
                        if (!empty($row['B']) && !empty($row['C'])) {
                            $storeId = $this->Session->read('admin_store_id');
                            $merchantId = $this->Session->read('admin_merchant_id');
                            if (!empty($storeId)) {
                                $categoryId = $this->Category->getCategoryByName($storeId, trim($row['C']));
                                if (!empty($categoryId)) {
                                    if ($row['D']) {
                                        //$itemName = trim($row['D']);
                                        //$itemName = explode(',',$itemName);
                                        //foreach($itemName as  $key => $value){
                                        //$itemId = $this->Item->getItemByName($value,$storeId,$categoryId['Category']['id']);
                                        $itemId = $this->Item->getItemByName(trim($row['D']), $storeId, $categoryId['Category']['id']);
                                        if (!empty($itemId)) {
                                            if (!empty($row['A'])) {
                                                $isUniqueName = $this->Topping->checkToppingUniqueName($row['B'], $storeId, $itemId['Item']['id'], $row['A']);
                                            } else {
                                                $isUniqueName = $this->Topping->checkToppingUniqueName($row['B'], $storeId, $itemId['Item']['id']);
                                            }
                                            if ($isUniqueName) {
                                                $toppingdata['name'] = $row['B'];
                                                $toppingdata['category_id'] = $categoryId['Category']['id'];
                                                $toppingdata['item_id'] = $itemId['Item']['id'];
                                                $toppingdata['price'] = 0;
                                                $toppingdata['store_id'] = $storeId;
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
                                                if (!empty($row['H']) && ($row['H'] <= 10)) {
                                                    $toppingdata['max_value'] = trim($row['H']);
                                                    if (!empty($row['G']) && ($row['G'] <= $row['H'])) {
                                                        $toppingdata['min_value'] = $row['G'];
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
                                                    $toppingdata['id'] = "";
                                                    $this->Topping->create();
                                                }
                                                $topping = $this->Topping->saveTopping($toppingdata);
                                                $i++;
                                            }
                                        }
                                        //}
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
                                                    $toppingdata['category_id'] = $categoryId['Category']['id'];
                                                    $toppingdata['item_id'] = $item['Item']['id'];
                                                    $toppingdata['price'] = 0;
                                                    $toppingdata['store_id'] = $storeId;
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

                                                    if (!empty($row['H']) && ($row['H'] <= 10)) {
                                                        $typedata['max_value'] = trim($row['H']);
                                                        if (!empty($row['G']) && ($row['G'] <= $row['H'])) {
                                                            $typedata['min_value'] = $row['G'];
                                                        } else {
                                                            $typedata['min_value'] = 0;
                                                        }
                                                    } else {
                                                        $typedata['min_value'] = 0;
                                                        $typedata['max_value'] = 0;
                                                    }

                                                    if (!empty($row['A'])) {
                                                        $toppingdata['id'] = $row['A'];
                                                    } else {
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
                }

                $this->Session->setFlash(__($i . ' ' . 'Add-ons has been saved'), 'alert_success');
                $this->redirect(array("controller" => "toppings", "action" => "index"));
            }
        }
    }

    public function uploadsubfile() {
        $this->layout = 'admin_dashboard';
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
                $i = 0;
                foreach ($real_data as $key => $row) {
                    $row = $this->Common->trimValue($row);
                    if ($key > 0) {
                        if (!empty($row['B']) && !empty($row['C']) && !empty($row['D'])) {
                            $row['E'] = trim($row['E']);
                            $storeId = $this->Session->read('admin_store_id');
                            $merchantId = $this->Session->read('admin_merchant_id');
                            if (!empty($storeId)) {
                                $row['C'] = trim($row['C']);
                                $categoryId = $this->Category->getCategoryByName($storeId, $row['D']);
                                if (!empty($categoryId)) {
                                    //   if(!empty($toppingId)){
                                    //$categoryTopCheck = $this->Topping->checkAddonByCategory($categoryId['Category']['id'],$storeId,$toppingId['Topping']['id']);
                                    //   if(!empty($categoryTopCheck)){
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
                                                //$isUniqueName = $this->Topping->checkSubaddonmUniqueName($row['B'],$toppingId['Topping']['id'],$storeId);
                                                //if($isUniqueName){
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
                }
                $this->Session->setFlash(__($i . ' ' . 'Sub Add-ons has been saved'), 'alert_success');
                $this->redirect(array("controller" => "toppings", "action" => "listSubTopping"));
            }
        }
    }

    public function download() {
        $storeId = $this->Session->read('admin_store_id');

        $this->Topping->bindModel(array('belongsTo' => array('Item' => array('fields' => array('name', 'id', 'category_id'), 'conditions' => array('Item.store_id' => $storeId, 'Item.is_deleted' => 0), 'type' => 'inner'))), false);
        $this->Topping->bindModel(array('belongsTo' => array('Category' => array('fields' => array('id', 'name'), 'conditions' => array('Category.store_id' => $storeId, 'Category.is_deleted' => 0), 'type' => 'inner'))), false);
        //$result = $this->Topping->findAddonList($storeId);
        $result = $this->Topping->find('all', array('conditions' => array('Topping.store_id' => $storeId, 'Topping.is_deleted' => 0, 'Topping.is_addon_category' => 1), 'order' => array('Category.name' => "ASC", 'Item.name' => "ASC", 'Topping.position' => "ASC"), 'type' => 'inner'));
        Configure::write('debug', 0);
        App::import('Vendor', 'PHPExcel');
        $objPHPExcel = new PHPExcel;
        ;
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
        $filename = 'Add-ons_' . date("Y-m-d") . ".xls"; //create a file
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
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Min Sub-Add-on');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Max Sub-Add-on');

        //$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);

        $i = 2;
        foreach ($result as $data) {
            if (!empty($data['Category']['name']) && $data['Item']['name']) {
                $objPHPExcel->getActiveSheet()->setCellValue("A$i", trim($data['Topping']['id']));
                $objPHPExcel->getActiveSheet()->setCellValue("B$i", trim($data['Topping']['name']));
                $objPHPExcel->getActiveSheet()->setCellValue("C$i", trim($data['Category']['name']));
                $objPHPExcel->getActiveSheet()->setCellValue("D$i", trim($data['Item']['name']));
                $objPHPExcel->getActiveSheet()->setCellValue("E$i", trim($data['Topping']['is_active']));
                $objPHPExcel->getActiveSheet()->setCellValue("F$i", trim($data['Topping']['position']));
                $objPHPExcel->getActiveSheet()->setCellValue("G$i", trim($data['Topping']['min_value']));
                $objPHPExcel->getActiveSheet()->setCellValue("H$i", trim($data['Topping']['max_value']));
                $i++;
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function subdownload() {
        $storeId = $this->Session->read('admin_store_id');
        $this->Topping->bindModel(array('belongsTo' => array('Category' => array('fields' => array('name')), 'Item' => array('fields' => array('name'), 'className' => 'Item', 'foreignKey' => 'item_id', 'type' => 'inner', 'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1)), 'ParentTopping' => array('className' => 'Topping', 'foreignKey' => 'addon_id', 'fields' => 'name', 'type' => 'inner', 'conditions' => array('ParentTopping.is_deleted' => 0, 'ParentTopping.is_active' => 1)))));
        //$result = $this->Topping->findsubAddonList($storeId);
        $result = $this->Topping->find('all', array('recursive' => 2, 'conditions' => array('Topping.store_id' => $storeId, 'Topping.is_deleted' => 0, 'Topping.is_addon_category' => 0), 'order' => array('Category.name' => "ASC", 'Item.name' => "ASC", 'ParentTopping.name' => "ASC", 'Topping.position' => "ASC")));

        Configure::write('debug', 0);
        App::import('Vendor', 'PHPExcel');
        $objPHPExcel = new PHPExcel;
        ;
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
        $filename = 'Sub-Add-ons_' . date("Y-m-d") . ".xls"; //create a file
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

        // $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
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
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", trim($data['Topping']['id']));
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", trim($data['Topping']['name']));
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", trim($data['ParentTopping']['name']));
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", trim($data['Category']['name']));
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", trim($data['Topping']['price']));
            $objPHPExcel->getActiveSheet()->setCellValue("F$i", trim($data['Topping']['is_active']));
            $objPHPExcel->getActiveSheet()->setCellValue("G$i", trim($data['Item']['name']));
            $objPHPExcel->getActiveSheet()->setCellValue("H$i", trim($data['Topping']['no_size']));
            $objPHPExcel->getActiveSheet()->setCellValue("I$i", trim($data['Topping']['position']));
            $i++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function getItemsByAddonCategoryId($toppingCategoryID = null, $subaddonID = null, $itemID = null, $Categoryid = null) {
        $storeID = $this->Session->read('admin_store_id');
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
//      $defaultarray=array();
//      if($subaddonID){
//	 $defaultItems=$this->ItemDefaultTopping->getDefaultItems($subaddonID);
//	 if($defaultItems){
//	    foreach($defaultItems as $key =>$defaultvalue){
//	       //pr($defaultvalue);die;
//	       if($itemID!=$defaultvalue['ItemDefaultTopping']['item_id']){
//		  $defaultarray[]=$defaultvalue['ItemDefaultTopping']['item_id'];
//	       }
//	    }
//	 }
//      }
        //pr($defaultarray);die;
        if ($Items) {
            foreach ($Items as $key => $value) {
                if (!empty($value['Item'])) {
                    //if(!in_array($value['Item']['id'],$defaultarray)){
                    $Itemslist[$value['Item']['id']] = $value['Item']['name'];
                    //}
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

    public function addonByCategory($categoryId = null) {
        $addonList = '';
        $storeID = $this->Session->read('admin_store_id');
        if ($categoryId) {
            $addonList = $this->Topping->getAddonsByCategory($storeID, $categoryId);
        }
        $this->set('addonList', $addonList);
    }

    /* ------------------------------------------------
      Function name:itemsBycategory()
      Description:get items by category
      created:6/8/2015
      ----------------------------------------------------- */

    //public function addonByCategoryEdit($categoryId=null){
    //   $addonList='';
    //   $storeID=$this->Session->read('store_id');
    //   if($categoryId){
    //      $addonList=$this->Topping->getAddonsByCategory($storeID,$categoryId);
    //   }
    //   $this->set('addonList',$addonList);
    //}

    public function addonByCategoryEdit($categoryId = null, $addonID = null) {
        $storeId = $this->Session->read('admin_store_id');
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

    public function getItemsByAddonCategoryIdMultiple($toppingCategoryID = null) {
        $storeID = $this->Session->read('admin_store_id');
        $Items = array();
        $Itemslist = array();
        if ($toppingCategoryID) {
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
            $Items = $this->Topping->getItemsbyAddoncategory($toppingCategoryID, $storeID);
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
      Function name:updateAddOnsOrder()
      Description: Update the display order for add-ons according to items
      created Date:16/12/2015
      created By:Praveen Soni
      ----------------------------------------------------- */

    public function updateAddOnsOrder() {
        $this->autoRender = false;
        if (isset($_GET) && !empty($_GET)) {
            foreach ($_GET as $key => $val) {
                $this->Topping->updateAll(array('position' => $val), array('id' => $this->Encryption->decode($key)));
            }
        }
    }

    /* ------------------------------------------------
      Function name:updateSubAddOnsOrder()
      Description: Update the display order for sub-add-ons according to add-ons
      created Date:16/12/2015
      created By:Praveen Soni
      ----------------------------------------------------- */

    public function updateSubAddOnsOrder() {
        $this->autoRender = false;
        if (isset($_GET) && !empty($_GET)) {
            foreach ($_GET as $key => $val) {
                $this->Topping->updateAll(array('position' => $val), array('id' => $this->Encryption->decode($key)));
            }
        }
    }

//    public function getSearchValues() {
//        $this->layout = false;
//        $this->autoRender = false;
//        if ($this->request->is(array('get'))) {
//            $this->loadModel('Topping');
//            $storeID = $this->Session->read('admin_store_id');
//            $searchData = $this->Topping->find('list', array('fields' => array('Topping.name', 'Topping.name'), 'conditions' => array('OR' => array('Topping.name LIKE' => '%' . $_GET['term'] . '%'), 'Topping.is_deleted' => 0, 'Topping.store_id' => $storeID)));
//            echo json_encode($searchData);
//        } else {
//            exit;
//        }
//    }

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
                        'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                        'type' => 'inner',
                        'fields' => array('id', 'name')
                    ), 'Category' => array(
                        'className' => 'Category',
                        'foreignKey' => 'category_id',
                        'type' => 'inner',
                        'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                        'fields' => array('id', 'name')
                    )
                )
                    ), false
            );
            $storeID = $this->Session->read('admin_store_id');
            if (!empty($_GET['sub']) && $_GET['sub'] == 'sub') {
                $cat = 0;
            } else {
                $cat = 1;
            }
            $searchData = $this->Topping->find('all', array('fields' => array('Topping.name', 'Topping.id', 'Item.name', 'Item.id', 'Category.id', 'Category.name'), 'conditions' => array('OR' => array('Topping.name LIKE' => '%' . $_GET['term'] . '%', 'Item.name LIKE' => '%' . $_GET['term'] . '%', 'Category.name LIKE' => '%' . $_GET['term'] . '%'), 'Topping.is_deleted' => 0, 'Topping.store_id' => $storeID, 'Topping.is_addon_category' => $cat)));
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
