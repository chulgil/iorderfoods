<?php

App::uses('HqAppController', 'Controller');

class HqsizesController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'Common');
    public $uses = array('Store', 'Size', 'Item', 'ItemPrice', 'OfferDetail', 'AddonSize', 'Category');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    private function _saveSize($merchant_id = null) {
        $storeID = trim($this->data['Size']['store_id']);
        $categoryId = trim($this->data['Size']['category_id']);
        $size = explode(',', trim($this->data['Size']['size']));
        $sucess = 0;
        foreach ($size as $Data) {
            $Data = trim($Data);
            if (!empty($Data)) {
                $isUniqueName = $this->Size->checkSizeUniqueName($Data, $storeID, $categoryId);
                if ($isUniqueName) {
                    $sizedata['store_id'] = $storeID;
                    $sizedata['merchant_id'] = $merchant_id;
                    $sizedata['size'] = $Data;
                    $sizedata['category_id'] = $categoryId;
                    $sizedata['is_active'] = $this->data['Size']['is_active'];
                    $this->Size->create();
                    $this->Size->saveSize($sizedata);
                    $sucess++;
                }
            }
        }
        $message = '';
        if ($sucess) {
            $message.="No of size " . $sucess . " created Successfully<br>";
        } else {
            $message.="Size not created.<br>";
        }
        $this->Session->setFlash(__($message), 'alert_success');
    }

    public function index() {
        $this->layout = "hq_dashboard";
        $merchant_id = $this->Session->read('merchantId');
        if ($this->request->is('post') && !empty($this->request->data['Size']['size'])) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            if ($this->request->data['Size']['store_id'] == 'All') {
                $storeData = $this->Store->getAllStoreByMerchantId($merchant_id);
                if (!empty($storeData)) {
                    $categoryName = $this->request->data['Size']['category_id'];
                    foreach ($storeData as $store) {
                        $catData = $this->Category->getCategoryIdByNameAndStoreId($store['Store']['id'], $categoryName);
                        if (!empty($catData)) {
                            $this->request->data['Size']['store_id'] = $store['Store']['id'];
                            $this->request->data['Size']['category_id'] = $catData['Category']['id'];
                            $this->_saveSize($merchant_id);
                        }
                    }
                }
            } else {
                $this->_saveSize($merchant_id);
            }
            $this->request->data = '';
        }
        $this->_sizeList();
    }

    public function getCategory() {
        if ($this->request->is('ajax') && $this->request->data['storeId']) {
            if ($this->request->data['storeId'] == 'All') {
                $merchant_id = $this->Session->read('merchantId');
                $categoryList = $this->Category->getCategoryListWithDuplicateNameIsSize($merchant_id);
            } else {
                $categoryList = $this->Category->getCategoryListIsSize($this->request->data['storeId']);
            }
            $this->set('categoryList', $categoryList);
        }
    }

    private function _sizeList($clearAction = null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $storeID = @$this->request->data['Size']['storeId'];
        $merchant_id = $this->Session->read('merchantId');
        /*         * ****start******* */
        $criteria = "Size.is_deleted=0 AND Size.merchant_id=$merchant_id";
        if (!empty($storeID)) {
            $criteria .= " AND Size.store_id =$storeID";
        }

        if ($this->Session->read('HqSizeSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqSizeSearchData'), true);
        } else {
            $this->Session->delete('HqSizeSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }

        if (!empty($this->request->data)) {
            $this->Session->write('HqSizeSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['Size']['categoryId'])) {
                $categoryID = trim($this->request->data['Size']['categoryId']);
                $criteria .= " AND (Category.id =$categoryID)";
            }
            if (isset($this->request->data['Size']['isActive']) && $this->request->data['Size']['isActive'] != '') {
                $active = trim($this->request->data['Size']['isActive']);
                $criteria .= " AND (Size.is_active =$active)";
            }
            if (!empty($this->request->data['Size']['search'])) {
                $search = trim($this->request->data['Size']['search']);
                $criteria .= " AND (Size.size LIKE '%" . $search . "%')";
            }
        }
        $this->Size->bindModel(
                array(
            'belongsTo' => array(
                'Category' => array(
                    'className' => 'Category',
                    'foreignKey' => 'category_id',
                    'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                    'fields' => array('id', 'name')
                ), 'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('store_name')
                )
            )), false
        );
        $this->paginate = array('conditions' => array($criteria), 'order' => array('Size.created' => 'DESC'));
        $itemdetail = $this->paginate('Size');
        $this->set('list', $itemdetail);
        /*         * ****end******** */
        $categoryList = $this->Category->getCategoryListIsSize($storeID);
        $this->set('categoryList', $categoryList);
    }

    /* ------------------------------------------------
      Function name:deleteSize()
      Description:Delete Size
      created:7/8/2015
      ----------------------------------------------------- */

    public function deleteSize($EncryptSizeID = null) {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $data['Size']['id'] = $this->Encryption->decode($EncryptSizeID);
        $data['Size']['is_deleted'] = 1;
        if ($this->
                Size->saveSize($data)) {
            $this->Session->setFlash(__("Size deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqsizes', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqsizes', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:activateSize()
      Description:Active/deactive category sizes
      created:7/8/2015
      ----------------------------------------------------- */

    public function activateSize($EncryptedSizeID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $data['Size']['id'] = $this->Encryption->decode($EncryptedSizeID);
        $data['Size']['is_active'] = $status;
        if ($this->Size->saveSize($data)) {
            if ($status) {
                $SuccessMsg = "Size Activated";
            } else {
                $SuccessMsg = "Size Deactivated and Size will not get Display in Menu List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hqsizes', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqsizes', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteMultipleSize()
      Description:Delete multiple size
      created:03/9/2015
      ----------------------------------------------------- */

    public function deleteMultipleSize() {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $data['Size']['is_deleted'] = 1;
        if (!empty($this->request->data['Size']['id'])) {
            $filter_array = array_filter($this->
                    request->data['Size']['id']);
            $i = 0;
            foreach ($filter_array as $orderId) {
                $data['Size']['id'] = $orderId;
                $this->Size->saveSize($data);
                $i++;
            }
            $del = $i . "  " . "size deleted successfully.";
            $this->Session->setFlash(__($del), 'aler t_succ ess');
            $this->redirect(array('controller' => 'hqsizes', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:editSize()
      Description:Edit Category Size
      created:6/8/2015
      ----------------------------------------------------- */

    public function editSize($EncryptSizeID = null) {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $data['Size']['id'] = $this->Encryption->decode($EncryptSizeID);
        $this->loadModel('Size');
        $sizeDetail = $this->Size->getSizeDetailById($data['Size']['id']);
        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $sizedata = array();
            $size = trim($this->data['Size']['size']);
            $categoryId = trim($this->data['Size']['category_id']);
            $isUniqueName = $this->Size->checkSizeUniqueName($size, $sizeDetail['Size']['store_id'], $categoryId, $data['Size']['id']);
            if ($isUniqueName) {
                $sizedata['id'] = $data['Size']['id'];
                $sizedata['size'] = trim($this->data['Size']['size']);
                $sizedata['category_id'] = $this->data['Size']['category_id'];
                $sizedata['is_active'] = $this->data['Size']['is_active'];
                $sizedata['store_id'] = $sizeDetail['Size']['store_id'];
                $sizedata['merchant_id'] = $merchantId;
                $this->Size->create();
                $this->Size->saveSize($sizedata);
                $this->Session->setFlash(__("Category Size Updated Successfully ."), 'alert_success');
                $this->redirect(array('controller' => 'hqsizes', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("Size Already exists"), 'alert_failed');
            }
        }
        $this->loadModel('Category');
        $categoryList = $this->Category->getCategoryListIsSize($sizeDetail['Size']['store_id']);
        $this->set('categoryList', $categoryList);
        $this->request->data = $sizeDetail;
    }

    /* ------------------------------------------------
      Function name:index()
      Description:To display the list of type
      created:7/8/2015
      ----------------------------------------------------- */

    public function sizelisting($clearAction = null) {
        $this->layout = "hq_dashboard";
        $storeID = @$this->request->data['ItemPrice']['store_id'];
        if (empty($storeID) && $this->Session->read('HqItemSizeSearchData')) {
            $data = json_decode($this->Session->read('HqItemSizeSearchData'), true);
            if (!empty($data['ItemPrice']['store_id'])) {
                $storeID = $data['ItemPrice']['store_id'];
            }
        }
        $merchant_id = $this->Session->read('merchantId');
        /*         * ****start******* */
        $criteria = "ItemPrice.is_deleted=0 AND ItemPrice.merchant_id=$merchant_id";
        if (!empty($storeID)) {
            $criteria .= " AND ItemPrice.store_id =$storeID";
        }
        $order = '';
        $pagingFlag = true;
        if ($this->Session->read('HqItemSizeSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqItemSizeSearchData'), true);
        } else {
            $this->Session->delete('HqItemSizeSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }

        $flag = 0;
        if (!empty($this->request->data)) {
            $this->Session->write('HqItemSizeSearchData', json_encode($this->request->data));
            if ($this->request->data['ItemPrice']['is_active'] != '') {
                $active = trim($this->request->data['ItemPrice']['is_active']);
                $criteria .= " AND (ItemPrice.is_active =$active)";
            }
            if (!empty($this->request->data['Size']['search'])) {
                $search = trim($this->request->data['Size']['search']);
                $criteria .= " AND (Size.size LIKE '%" . $search . "%')";
            }

            if ($this->request->data['ItemPrice']['item_id'] != '') {
                $itemId = trim($this->request->data['ItemPrice']['item_id']);
                $criteria .= " AND (ItemPrice.item_id =$itemId)";
                $typeId = $this->ItemPrice->find('list', array('fields' => array('ItemPrice.size_id'), 'conditions' => array('ItemPrice.item_id' => $itemId, 'ItemPrice.store_id' => $storeID, 'ItemPrice.is_deleted' => 0)));
                $pagingFlag = false;
                $typeId = array_values(array_filter($typeId));
                if (count($typeId) > 0) {
                    $criteria .= " AND (ItemPrice.size_id IN (" . implode(',', array_unique($typeId)) . "))";
                    $order = 'ItemPrice.position ASC';
                }
            }
        }
        if ($order == '') {
            $order = 'ItemPrice.position ASC';
        }
        $this->ItemPrice->bindModel(
                array(
            'belongsTo' => array(
                'Size' => array(
                    'className' => 'Size',
                    'foreignKey' => 'size_id',
                    'conditions' => array('Size.is_deleted' => 0, 'Size.is_active' => 1),
                    'fields' => array('id', 'size', 'is_active'),
                    'type' => 'INNER'
                ),
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id',
                    'type' => 'INNER',
                    'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                    'fields' => array('id', 'name')
                ), 'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('store_name')
                )
            )
                ), false
        );
        $sizedetail = '';
        if ($pagingFlag) {
            $this->paginate = array('conditions' => array($criteria), 'order' => $order);
            $sizedetail = $this->paginate('ItemPrice');
        } else {
            $this->paginate = array('conditions' => array($criteria), 'order' => $order);
            $sizedetail = $this->paginate('ItemPrice');
        }
        $this->set('list', $sizedetail);
        $this->set('pagingFlag', $pagingFlag);
        //$itemList = $this->Item->getallItemsByStore($storeID);
        $nList = array();
        if (!empty($storeID)) {
            $itemList = $this->ItemPrice->find('all', array('conditions' => array('ItemPrice.store_id' => $storeID, 'ItemPrice.is_deleted' => 0), 'group' => array('ItemPrice.item_id')));
            //$itemList=$this->Item->getallItemsByStore($storeID);
            if (!empty($itemList)) {
                foreach ($itemList as $iList) {
                    if (!empty($iList['Item']) && !empty($iList['Size'])) {
                        $nList[$iList['Item']['id']] = $iList['Item']['name'];
                    }
                }
            }
        }
        $this->set('itemList', $nList);
    }

    /* ------------------------------------------------
      Function name:activateType()
      Description:Active/deactive type
      created:7/8/2015
      ----------------------------------------------------- */

    public function activateItemSize($EncryptedTypeID = null, $status = 0) {
        $this->autoRender = false;
        $data['ItemPrice']['merchant_id'] = $this->Session->read('merchantId');
        $data['ItemPrice']['id'] = $this->Encryption->decode($EncryptedTypeID);
        $data['ItemPrice']['is_active'] = $status;
        $this->loadmodel("ItemPrice");
        if ($this->ItemPrice->saveItemPrice($data)) {
            if ($status) {
                $SuccessMsg = "Item Size Activated";
            } else {
                $SuccessMsg = "Item Size Deactivated and Preference will not get Display in Menu List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hqsizes', 'action' => 'sizelisting'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqsizes', 'action' => 'sizelisting'));
        }
    }

    private function _saveAddOnSize($merchantId = null) {
        $storeId = $this->request->data['AddonSize']['store_id'];
        $sizedata = array();
        $size = trim($this->data['AddonSize']['size']);
        $isUniqueName = $this->AddonSize->checkAddonSizeUniqueName($size, $storeId, $merchantId);
        if ($isUniqueName) {
            $sizedata['size'] = trim($this->data['AddonSize']['size']);
            $sizedata['price_percentage'] = $this->data['AddonSize']['price_percentage'];
            $sizedata['is_active'] = $this->data['AddonSize']['is_active'];
            $sizedata['store_id'] = $storeId;
            $sizedata['merchant_id'] = $merchantId;
            $this->AddonSize->create();
            $this->AddonSize->saveAddonSize($sizedata);
            $this->Session->setFlash(__("Add-ons size created Successfully ."), 'alert_success');
        } else {
            $this->Session->setFlash(__("Add-ons size Already exists"), 'alert_failed');
        }
    }

    /* ------------------------------------------------
      Function name:createAddonSize()
      Description:Add add-ons  size
      created:08/9/2015
      ----------------------------------------------------- */

    public function addOnSize() {
        $this->layout = "hq_dashboard";
        $merchant_id = $this->Session->read('merchantId');
        if ($this->request->is('post') && !empty($this->request->data['AddonSize']['size'])) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            if ($this->request->data['AddonSize']['store_id'] == 'All') {
                $storeData = $this->Store->getAllStoreByMerchantId($merchant_id);
                if (!empty($storeData)) {
                    foreach ($storeData as $store) {
                        $this->request->data['AddonSize']['store_id'] = $store['Store']['id'];
                        $this->_saveAddOnSize($merchant_id);
                    }
                }
            } else {
                $this->_saveAddOnSize($merchant_id);
            }
            $this->request->data = '';
        }
        $this->_addOnSizeList();
    }

    /* ------------------------------------------------
      Function name:addOnSizeList()
      Description:To display the list of add-ons size
      created:7/8/2015
      ----------------------------------------------------- */

    public function _addOnSizeList($clearAction = null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $storeId = @$this->request->data['AddonSize']['storeId'];
        $merchant_id = $this->Session->read('merchantId');
        /*         * ****start******* */
        $value = "";
        $criteria = "AddonSize.merchant_id =$merchant_id AND AddonSize.is_deleted=0";
        if (!empty($storeId)) {
            $criteria .= " AND AddonSize.store_id =$storeId";
        }
        if ($this->Session->read('HqSizeSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqSizeSearchData'), true);
        } else {
            $this->Session->delete('HqSizeSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('HqSizeSearchData', json_encode($this->request->data));
            if (isset($this->request->data['AddonSize']['isActive']) && $this->request->data['AddonSize']['isActive'] != '') {
                $active = trim($this->request->data['AddonSize']['isActive']);
                $criteria .= " AND (AddonSize.is_active =$active)";
            }

            if (!empty($this->request->data['AddonSize']['search'])) {
                $search = trim($this->request->data['AddonSize']['search']);
                $criteria .= " AND (AddonSize.size LIKE '%" . $search . "%')";
            }
        }
        $this->AddonSize->bindModel(array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('store_name')
                ))), false);
        $this->paginate = array('conditions' => array($criteria), 'order' => array('AddonSize.created' => 'DESC'));
        $itemdetail = $this->paginate('AddonSize');
        $this->set('list', $itemdetail);
        $this->set('keyword', $value);
    }

    /* ------------------------------------------------
      Function name:activateAddonSize()
      Description:Active/deactive add-ons sizes
      created:7/8/2015
      ----------------------------------------------------- */

    public function activateAddonSize($EncryptedSizeID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $data['AddonSize']['merchant_id'] = $this->Session->read('merchantId');
        $data['AddonSize']['id'] = $this->Encryption->decode($EncryptedSizeID);
        $data['AddonSize']['is_active'] = $status;
        if ($this->AddonSize->saveAddonSize($data)) {
            if ($status) {
                $SuccessMsg = "Add-ons size Activated";
            } else {
                $SuccessMsg = "Add-ons size Deactivated and Size will not get Display in Menu List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hqsizes', 'action' => 'addOnSize'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqsizes', 'action' => 'addOnSize'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteAddonSize()
      Description:Delete add-ons size
      created:7/8/2015
      ----------------------------------------------------- */

    public function deleteAddonSize($EncryptSizeID = null) {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $data['AddonSize']['merchant_id'] = $this->Session->read('merchantId');
        $data['AddonSize']['id'] = $this->Encryption->decode($EncryptSizeID);
        $data['AddonSize']['is_deleted'] = 1;
        if ($this->AddonSize->saveAddonSize($data)) {
            $this->Session->setFlash(__("Add-ons size deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqsizes', 'action' => 'addOnSize'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqsizes', 'action' => 'addOnSize'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteMultipleAddonSize()
      Description:Delete multiple add-ons size
      created:03/9/2015
      ----------------------------------------------------- */

    public function deleteMultipleAddonSize() {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $data['AddonSize']['merchant_id'] = $this->Session->read('merchantId');
        $data['AddonSize']['is_deleted'] = 1;
        if (!empty($this->request->data['AddonSize']['id'])) {
            $filter_array = array_filter($this->request->data['AddonSize']['id']);
            $i = 0;
            foreach ($filter_array as $orderId) {
                $data['AddonSize']['id'] = $orderId;
                $this->AddonSize->saveAddonSize($data);
                $i++;
            }
            $del = $i . "  " . "Add-ons size deleted successfully.";
            $this->Session->setFlash(__($del), 'alert_success');
            $this->redirect(array('controller' => 'hqsizes', 'action' => 'addOnSize'));
        }
    }

    /* ------------------------------------------------
      Function name:editAddonSize()
      Description:Add add-ons  size
      created:08/9/2015
      ----------------------------------------------------- */

    public function editAddonSize($EncryptSizeID = null) {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $data['AddonSize']['id'] = $this->Encryption->decode($EncryptSizeID);
        $this->loadModel('AddonSize');
        $sizeDetail = $this->AddonSize->getAddonSizeDetailById($data['AddonSize']['id']);
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $storeId = $sizeDetail['AddonSize']['store_id'];
            $sizedata = array();
            $size = trim($this->data['AddonSize']['size']);
            $isUniqueName = $this->AddonSize->checkAddonSizeUniqueName($size, $storeId, $merchantId, $this->data['AddonSize']['id']);
            if ($isUniqueName) {
                $sizedata['size'] = trim($this->data['AddonSize']['size']);
                $sizedata['price_percentage'] = $this->data['AddonSize']['price_percentage'];
                $sizedata['is_active'] = $this->data['AddonSize']['is_active'];
                $sizedata['id'] = $this->data['AddonSize']['id'];
                $sizedata['store_id'] = $storeId;
                $sizedata['merchant_id'] = $merchantId;
                $this->AddonSize->saveAddonSize($sizedata);
                $this->Session->setFlash(__("Add-ons size update Successfully ."), 'alert_success');
                $this->redirect(array('controller' => 'hqsizes', 'action' => 'addOnSize'));
            } else {
                $this->Session->setFlash(__("Add-ons size Already exists"), 'alert_failed');
            }
        }
        $this->request->data = $sizeDetail;
    }

    /* ------------------------------------------------
      Function name:hquploadfile() For Size Upload
      Description:Delete Size
      created:12/8/2016
      ----------------------------------------------------- */

    public function uploadfile() {
        $this->layout = "hq_dashboard";
        $this->loadModel('Category');
        $this->loadModel('Store');
        if (!empty($this->request->data)) {
            $tmp = $this->request->data;
            if ($tmp['Size']['file']['error'] == 4) {
                $this->Session->setFlash(__('Your file contains error. Please retry uploading.'), 'alert_failed');
                $this->redirect($this->here);
            }
            $valid = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            if (!in_array($tmp['Size']['file']['type'], $valid)) {
                $this->Session->setFlash(__('You can only upload Excel file.'), 'alert_failed');
            } else if ($tmp['Size']['file']['error'] != 0) {
                $this->Session->setFlash(__('The file you uploaded contains errors.'), 'alert_failed');
            } else if ($tmp['Size']['file']['size'] > 20000000) {
                $this->Session->setFlash(__('The file size must be Max 20MB'), 'alert_failed');
            } else {
                ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large
                App::import('Vendor', 'PHPExcel');
                $objPHPExcel = new PHPExcel;
                $objPHPExcel = PHPExcel_IOFactory::load($tmp['Size']['file']['tmp_name']);
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                $real_data = array_values($sheetData);
                $merchantId = $this->Session->read('merchantId');
                $storeId = $this->request->data['Size']['store_id'];
                if ($storeId == "All") {
                    $storeId = $this->Store->find('list', array('fields' => array('id'), 'conditions' => array('Store.merchant_id' => $merchantId)));
                    $i = $this->sizeForMultipleStore($storeId, $real_data, $merchantId);
                } else {
                    $i = $this->saveFileSize($real_data, $storeId, $merchantId);
                }
                $this->Session->setFlash(__($i . ' ' . 'Size has been saved'), 'alert_success');
                $this->redirect(array("controller" => "hqsizes", "action" => "index"));
            }
        }
    }

    /* ------------------------------------------------
      Function name:sizeForMultipleStore() For multiple Store Size Upload
      Description:Delete Size
      created:12/8/2016
      ----------------------------------------------------- */

    public function sizeForMultipleStore($storeIds = array(), $real_data = array(), $merchantId = null) {

        $i = 0;
        if (!empty($storeIds)) {
            foreach ($storeIds as $storeId) {
                $k = $this->saveFileSize($real_data, $storeId, $merchantId);
                if (is_numeric($k)) {
                    $i = $i + $k;
                }
            }
        }
        return $i;
    }

    public function saveFileSize($real_data = null, $storeId, $merchantId) {
        $i = 0;
        foreach ($real_data as $key => $row) {

            $row['A'] = trim($row['A']);
            if (!empty($row['A'])) {
                $isUniqueId = $this->Size->checkSizeWithId($row['A']);
                if (!empty($isUniqueId) && $isUniqueId['Size']['store_id'] != $storeId) {
                    continue;
                }
            }
            $row = $this->Common->trimValue($row);
            if ($key > 0) {
                if (!empty($row['B']) && !empty($row['C'])) {
                    $row['B'] = trim($row['B']);
                    $categoryId = $this->Category->getCategoryByName($storeId, $row['B']);
                    if (!empty($categoryId)) {
                        $size = trim($row['C']);
                        $size = explode(',', $size);
                        foreach ($size as $key => $Data) {
                            $Data = trim($Data);
                            if (!empty($row['A'])) {
                                $isUniqueName = $this->Size->checkSizeUniqueName($Data, $storeId, $categoryId['Category']['id'], $row['A']);
                            } else {
                                $isUniqueName = $this->Size->checkSizeUniqueName($Data, $storeId, $categoryId['Category']['id']);
                            }
                            if ($isUniqueName) {
                                $sizedata['merchant_id'] = $merchantId;
                                $sizedata['size'] = $Data;
                                $sizedata['category_id'] = $categoryId['Category']['id'];
                                if (!empty($row['D'])) {
                                    $sizedata['is_active'] = $row['D'];
                                } else {
                                    $sizedata['is_active'] = 0;
                                }

                                if (!empty($row['A'])) {

                                    if (!empty($isUniqueId)) {
                                        $sizedata['id'] = $row['A'];
                                    }
                                } else {
                                    $sizedata['store_id'] = $storeId;
                                    $sizedata['id'] = "";
                                    $this->Size->create();
                                }
                                $this->Size->saveSize($sizedata);
                                $i++;
                            }
                        }
                    }
                }
            }
        }
        return $i;
    }

    /* ------------------------------------------------
      Function name:downloadsize()
      Description:Download multiple Store
      created:12/8/2016
      ----------------------------------------------------- */

    public function downloadsize($store_id = null) {
        if (!empty($store_id)) {
            $this->Size->bindModel(array(
                'belongsTo' => array(
                    'Category' => array(
                        'fields' => array('id', 'name')
                    ),
                    'Store' => array(
                        'className' => 'Store',
                        'foreignKey' => 'store_id',
                        'fields' => array(
                            'id', 'store_name'
                        )
                    ))), false);

            if ($store_id == "All") {
                $merchantId = $this->Session->read('merchantId');
                $result = $this->Size->findSizeListByMerchantId($merchantId);
            } else {
                $storeId = $store_id;
                $result = $this->Size->findSizeList($storeId);
            }
        }

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
        $filename = 'HqSize_' . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('Size');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Category Name');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Size');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Active');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Store Name');

        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);

        $i = 2;
        foreach ($result as $data) {
            $data = $this->Common->trimValue($data);
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", $data['Size']['id']);
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", $data['Category']['name']);
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", $data['Size']['size']);
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", $data['Size']['is_active']);
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", $data['Store']['store_name']);
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
      Function name:hquploadaddonsfile()
      Description:Upload Excle file for Add on Size multiple Store
      created:12/8/2016
      ----------------------------------------------------- */

    public function uploadaddonsfile() {
        $this->layout = "hq_dashboard";
        $this->loadModel("Store");
        if (!empty($this->request->data)) {
            $tmp = $this->request->data;

            if ($tmp['AddonSize']['file']['error'] == 4) {
                $this->Session->setFlash(__('Your file contains error. Please retry uploading.'), 'alert_failed');
                $this->redirect($this->here);
            }
            $valid = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            if (!in_array($tmp['AddonSize']['file']['type'], $valid)) {
                $this->Session->setFlash(__('You can only upload Excel file.'), 'alert_failed');
            } else if ($tmp['AddonSize']['file']['error'] != 0) {
                $this->Session->setFlash(__('The file you uploaded contains errors.'), 'alert_failed');
            } else if ($tmp['AddonSize']['file']['size'] > 20000000) {
                $this->Session->setFlash(__('The file size must be Max 20MB'), 'alert_failed');
            } else {
                ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large
                App::import('Vendor', 'PHPExcel');
                $objPHPExcel = new PHPExcel;
                $objPHPExcel = PHPExcel_IOFactory::load($tmp['AddonSize']['file']['tmp_name']);
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                $real_data = array_values($sheetData);
                $merchantId = $this->Session->read('merchantId');
                $storeId = $this->request->data['AddonSize']['store_id'];
                if ($storeId == "All") {
                    $storeId = $this->Store->find('list', array('fields' => array('id'), 'conditions' => array('Store.merchant_id' => $merchantId)));
                    $i = $this->AddOnSizeForMultipleStore($storeId, $real_data, $merchantId);
                } else {
                    $i = $this->saveFileAddOnSize($real_data, $storeId, $merchantId);
                }
                $this->Session->setFlash(__($i . ' ' . 'Addon Size has been saved'), 'alert_success');
                $this->redirect(array("controller" => "hqsizes", "action" => "addOnSize"));
            }
        }
    }

    /* ------------------------------------------------
      Function name:hquploadaddonsfile()
      Description:Upload Excle file for Add on Size multiple Store
      created:12/8/2016
      ----------------------------------------------------- */

    public function AddOnSizeForMultipleStore($storeIds = array(), $real_data = array(), $merchantId = null) {
        $i = 0;
        if (!empty($storeIds)) {
            foreach ($storeIds as $storeId) {
                $k = $this->saveFileAddOnSize($real_data, $storeId, $merchantId);
                if (is_numeric($k)) {
                    $i = $i + $k;
                }
            }
        }
        return $i;
    }

    public function saveFileAddOnSize($real_data = null, $storeId, $merchantId) {
        $i = 0;
        foreach ($real_data as $key => $row) {
            $row['A'] = trim($row['A']);
            if (!empty($row['A'])) {
                $isUniqueId = $this->AddonSize->checkAddOnSizeWithId($row['A']);
                if (!empty($isUniqueId) && $isUniqueId['AddonSize']['store_id'] != $storeId) {
                    continue;
                }
            }
            $row = $this->Common->trimValue($row);
            if ($key > 0) {
                if (!empty($row['B']) && !empty($row['C'])) {
                    $sizeName = trim($row['B']);
                    if ($sizeName == '1') {
                        continue;
                    }
                    if (!empty($row['A'])) {
                        $isUniqueName = $this->AddonSize->checkAddOnSize($sizeName, $storeId, $row['A']);
                    } else {
                        $isUniqueName = $this->AddonSize->checkAddOnSize($sizeName, $storeId);
                    }
                    if ($isUniqueName) {
                        $sizedata['merchant_id'] = $merchantId;
                        $sizedata['size'] = trim($row['B']);
                        $sizedata['price_percentage'] = trim($row['C']);
                        if (!empty($row['D'])) {
                            $sizedata['is_active'] = $row['D'];
                        } else {
                            $sizedata['is_active'] = 0;
                        }
                        if (!empty($row['E'])) {
                            $sizedata['is_deleted'] = $row['E'];
                        } else {
                            $sizedata['is_deleted'] = 0;
                        }

                        if (!empty($row['A'])) {

                            if (!empty($isUniqueId)) {
                                $sizedata['id'] = $row['A'];
                            }
                        } else {
                            $sizedata['store_id'] = $storeId;
                            $sizedata['id'] = "";
                            $this->AddonSize->create();
                        }
                        $this->AddonSize->saveAddonSize($sizedata);
                        $i++;
                    }
                }
            }
        }
        return $i;
    }

    /* ------------------------------------------------
      Function name:hquploadaddonsfile()
      Description:Download Excle file for Add on Size multiple Store
      created:12/8/2016
      ----------------------------------------------------- */

    public function downloadaddonsize($store_id = null) {
        if (!empty($store_id)) {
            $this->AddonSize->bindModel(array(
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
                $result = $this->AddonSize->getAddonSizeDetailByMerchantId($merchantId);
            } else {
                $storeId = $store_id;
                $result = $this->AddonSize->getAddonSizeDetailByStoreId($storeId);
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
        $filename = 'HqAdd_on_Size_' . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('Add On Size');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'size');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'price_percentage');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Active');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Deleted');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Store Name');

        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);

        $i = 2;
        foreach ($result as $data) {
            $data = $this->Common->trimValue($data);
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", $data['AddonSize']['id']);
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", $data['AddonSize']['size']);
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", $data['AddonSize']['price_percentage']);
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", $data['AddonSize']['is_active']);
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", $data['AddonSize']['is_deleted']);
            $objPHPExcel->getActiveSheet()->setCellValue("F$i", $data['Store']['store_name']);
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
            $this->loadModel('Size');
            $this->Size->bindModel(
                    array(
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
            if (!empty($_GET['storeID'])) {
                $storeID = $_GET['storeID'];
                $searchData = $this->Size->find('all', array('fields' => array('Size.size', 'Category.name'), 'conditions' => array('OR' => array('Size.size LIKE' => '%' . $_GET['term'] . '%', 'Category.name LIKE' => '%' . $_GET['term'] . '%'), 'Size.is_deleted' => 0, 'Size.store_id' => $storeID)));
            } else {
                $merchant_id = $this->Session->read('merchantId');
                $searchData = $this->Size->find('all', array('fields' => array('Size.size', 'Category.name'), 'conditions' => array('OR' => array('Size.size LIKE' => '%' . $_GET['term'] . '%', 'Category.name LIKE' => '%' . $_GET['term'] . '%'), 'Size.is_deleted' => 0, 'Size.merchant_id' => $merchant_id)));
            }
            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    $new_array[] = array('label' => $val['Size']['size'], 'value' => $val['Size']['size'], 'desc' => $val['Size']['size'] . '-' . $val['Category']['name']);
                };
            }
            echo json_encode($new_array);
        } else {
            exit;
        }
    }

    public function getItemSizeList() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('ItemPrice');
            $this->ItemPrice->bindModel(
                    array(
                'belongsTo' => array(
                    'Size' => array(
                        'className' => 'Size',
                        'foreignKey' => 'size_id',
                        'conditions' => array('Size.is_deleted' => 0, 'Size.is_active' => 1),
                        'fields' => array('id', 'size', 'is_active'),
                        'type' => 'INNER'
                    ),
                    'Item' => array(
                        'className' => 'Item',
                        'foreignKey' => 'item_id',
                        'type' => 'INNER',
                        'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                        'fields' => array('id', 'name')
                    )
                )
                    ), false
            );
            if (!empty($_GET['storeID'])) {
                $storeID = $_GET['storeID'];
                $searchData = $this->ItemPrice->find('all', array('fields' => array('Size.size', 'Item.name'), 'conditions' => array('OR' => array('Size.size LIKE' => '%' . $_GET['term'] . '%', 'Item.name LIKE' => '%' . $_GET['term'] . '%'), 'ItemPrice.is_deleted' => 0, 'ItemPrice.store_id' => $storeID)));
            } else {
                $merchant_id = $this->Session->read('merchantId');
                $searchData = $this->ItemPrice->find('all', array('fields' => array('Size.size', 'Item.name'), 'conditions' => array('OR' => array('Size.size LIKE' => '%' . $_GET['term'] . '%', 'Item.name LIKE' => '%' . $_GET['term'] . '%'), 'ItemPrice.is_deleted' => 0, 'ItemPrice.merchant_id' => $merchant_id)));
            }

            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    $new_array[] = array('label' => $val['Size']['size'], 'value' => $val['Size']['size'], 'desc' => $val['Size']['size'] . '-' . $val['Item']['name']);
                };
            }
            echo json_encode($new_array);
        } else {
            exit;
        }
    }

    public function getAddonsSizeName() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('AddonSize');
            if (!empty($_GET['storeID'])) {
                $storeID = $_GET['storeID'];
            } else {
                $merchant_id = $this->Session->read('merchantId');
                $storeID = $this->Store->getAllStoresByMerchantId($merchant_id);
            }
            $searchData = $this->AddonSize->find('list', array('fields' => array('AddonSize.size', 'AddonSize.size'), 'conditions' => array('OR' => array('AddonSize.size LIKE' => '%' . $_GET['term'] . '%'), 'AddonSize.is_deleted' => 0, 'AddonSize.store_id' => $storeID)));
            echo json_encode($searchData);
        } else {
            exit;
        }
    }

}
