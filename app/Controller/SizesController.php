<?php

App::uses('StoreAppController', 'Controller');

class SizesController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'Common');
    public $uses = array('Size', 'Item', 'ItemPrice', 'OfferDetail', 'AddonSize');

    public function beforeFilter() {
        // echo Router::url( $this->here, true );die;
        parent::beforeFilter();

        // $adminfunctions=array('addSize','index','deleteSize','activateSize','editSize','createAddonSize','addOnSizeList');
        //if(in_array($this->params['action'],$adminfunctions)){
        //   if(!$this->Common->checkPermissionByaction($this->params['controller'])){
        //     $this->Session->setFlash(__("Permission Denied"));
        //     $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        //   }
        //}
    }

    /* ------------------------------------------------
      Function name:getCategorySizes()
      Description:To find list of the categories from category table
      created:3/8/2015
      ----------------------------------------------------- */

    public function getCategorySizes($categoryId = null) {
        $storeId = $this->Session->read('admin_store_id');
        $this->loadModel('Size');
        $this->loadModel('Category');
        if ($categoryId) {
            $sizeList = '';
            if ($this->Category->checkCategorySizeExists($categoryId, $storeId)) {
                $sizeList = $this->Size->getCategorySizes($categoryId, $storeId);
            }
            $sizeInfo = $this->Category->getCategorySizeType($categoryId, $storeId);
            $this->set('sizeList', $sizeList);
            $this->set('sizeInfo', $sizeInfo);
        } else {
            exit;
        }
    }

    /* ------------------------------------------------
      Function name:getItemSizes()
      Description:To find list of the Sizes
      created:3/8/2015
      ----------------------------------------------------- */

    public function getItemSizes($itemId = null) {
        $storeId = $this->Session->read('admin_store_id');
        $this->loadModel('Size');
        $this->loadModel('Category');
        if ($itemId) {
            $sizeList = '';
            $category = $this->Item->getcategoryByitemID($itemId, $storeId);
            if ($category) {
                $categoryId = $category['Item']['category_id'];
                if ($this->Category->checkCategorySizeExists($categoryId, $storeId)) {
                    $sizeList = $this->Size->getCategorySizes($categoryId, $storeId);
                }
            }
            $this->set('sizeList', $sizeList);
        } else {
            exit;
        }
    }

    /* ------------------------------------------------
      Function name:getItemSizes()
      Description:To find list of the Sizes
      created:3/8/2015
      ----------------------------------------------------- */

    public function getItemSize($itemId = null) {
        $storeId = $this->Session->read('admin_store_id');
        $this->loadModel('Size');
        $this->loadModel('Category');
        if ($itemId) {
            $sizeList = '';
            $category = $this->Item->getcategoryByitemID($itemId, $storeId);
            if ($category) {
                $categoryId = $category['Item']['category_id'];
                if ($this->Category->checkCategorySizeExists($categoryId, $storeId)) {
                    $this->ItemPrice->bindModel(
                            array(
                        'belongsTo' => array(
                            'Size' => array(
                                'className' => 'Size',
                                'foreignKey' => 'size_id',
                                'conditions' => array('Size.is_deleted' => 0, 'Size.is_active' => 1),
                                'fields' => array('id', 'size')
                            )
                        )
                            ), false
                    );
                    $sizeList = array();
                    $sizeListarray = $this->ItemPrice->getItemSizes($itemId, $storeId);
                    //pr($sizeListarray);
                    if ($sizeListarray) {
                        foreach ($sizeListarray as $key => $value) {
                            if ($value['Size']) {
                                $sizeList[$value['ItemPrice']['size_id']] = $value['Size']['size'];
                            }
                        }
                    }

                    // $sizeList=$this->Size->getCategorySizes($categoryId,$storeId);
                }
            }
            $this->set('sizeList', $sizeList);
        } else {
            exit;
        }
    }

    /* ------------------------------------------------
      Function name:getItemSizes()
      Description:To find list of the Sizes
      created:3/8/2015
      ----------------------------------------------------- */

    public function getMultipleItemSizes() {    //pr($this->data);
        $storeId = $this->Session->read('admin_store_id');
        $this->loadModel('Size');
        $this->loadModel('Category');
        if ($this->data) {
            $sizeList = array();
            foreach ($this->data['Offered']['id'] as $key => $itemId) {

                $category = $this->Item->getcategoryByitemID($itemId, $storeId);
                if ($category) {
                    $categoryId = $category['Item']['category_id'];
                    if ($this->Category->checkCategorySizeExists($categoryId, $storeId)) {

                        $this->ItemPrice->bindModel(
                                array(
                            'belongsTo' => array(
                                'Size' => array(
                                    'className' => 'Size',
                                    'foreignKey' => 'size_id',
                                    'conditions' => array('Size.is_deleted' => 0, 'Size.is_active' => 1),
                                    'fields' => array('id', 'size')
                                )
                            )
                                ), false
                        );
                        $sizeListarray = $this->ItemPrice->getItemSizes($itemId, $storeId);
                        if ($sizeListarray) {
                            foreach ($sizeListarray as $key => $value) {
                                if ($value['Size']) {
                                    $sizeList[$itemId][$value['ItemPrice']['size_id']] = $value['Size']['size'];
                                } else {
                                    $sizeList[$itemId] = '';
                                }
                            }
                        } else {
                            $sizeList[$itemId] = '';
                        }


                        //$sizeList1[$itemId]=$this->Size->getCategorySizes($categoryId,$storeId);
                    } else {
                        $sizeList[$itemId] = '';
                    }
                }
            }
            //pr($sizeList1);
            // pr($sizeList);
            $this->set('sizeList', $sizeList);
        } else {
            exit;
        }
    }

    /* ------------------------------------------------
      Function name:addSize()
      Description:To add thesize in size table
      created:7/8/2015
      ----------------------------------------------------- */

    public function addSize() {

        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addSize')) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');
        if ($this->request->is('post') && !empty($this->request->data['Size']['size'])) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $size = trim($this->data['Size']['size']);
            $categoryId = trim($this->data['Size']['category_ids']);
            $size = explode(',', $this->data['Size']['size']);
            //$sizeuniquecheckArray=array();
            $sucess = 0;
            foreach ($size as $key => $Data) {
                $Data = trim($Data);
                if (!empty($Data)) {
                    $isUniqueName = $this->Size->checkSizeUniqueName($Data, $storeID, $categoryId);
                    if ($isUniqueName) {
                        // $sizeuniquecheckArray[]=$Data;
                        $sizedata['store_id'] = $storeID;
                        $sizedata['merchant_id'] = $merchant_id;
                        $sizedata['size'] = $Data;
                        $sizedata['category_id'] = $this->data['Size']['category_ids'];
                        $sizedata['is_active'] = $this->data['Size']['is_active1'];
                        $this->Size->create();
                        $this->Size->saveSize($sizedata);
                        $sucess++;
                    }
                }
            }
            $message = '';
            if ($sucess) {
                $this->request->data = '';
                $message.="No of size " . $sucess . " created Successfully<br>";
                $this->redirect(array('controller' => 'sizes', 'action' => 'index'));
            } else {
                $message.="Size not created.<br>";
                $this->redirect(array('controller' => 'sizes', 'action' => 'index'));
            }
            $this->Session->setFlash(__($message), 'alert_success');
        }
        $this->loadModel('Category');
        $categoryList = $this->Category->getCategoryListIsSize($storeID);
        $this->set('categoryList', $categoryList);
        $this->index();
    }

    /* ------------------------------------------------
      Function name:index()
      Description:To display the list of category size
      created:7/8/2015
      ----------------------------------------------------- */

    public function index($clearAction = null) {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addSize')) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');

        /*         * ****start******* */
        $value = "";
        $criteria = "Size.store_id =$storeID AND Size.is_deleted=0";

        //if(isset($this->params['named']['sort']) || isset($this->params['named']['page'])){


        if ($this->Session->read('SizeSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('SizeSearchData'), true);
        } else {
            $this->Session->delete('SizeSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }

        if (!empty($this->request->data)) {
            $this->Session->write('SizeSearchData', json_encode($this->request->data));

            if (!empty($this->request->data['Size']['category_id'])) {
                $categoryID = trim($this->request->data['Size']['category_id']);
                $criteria .= " AND (Category.id =$categoryID)";
            }
            if (isset($this->request->data['Size']['is_active']) && $this->request->data['Size']['is_active'] != '') {
                $active = trim($this->request->data['Size']['is_active']);
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
                    'fields' => array('id', 'name'),
                    'type' => 'inner'
                )
            )
                ), false
        );
        $this->paginate = array('conditions' => array($criteria), 'order' => array('Size.created' => 'DESC'));
        $itemdetail = $this->paginate('Size');
        //pr($itemdetail);die;
        $this->set('list', $itemdetail);
        $this->loadModel('Category');
        $categoryList = $this->Category->getCategoryList($storeID);
        $this->set('categoryList', $categoryList);
        $this->set('keyword', $value);
        /*         * ****end******** */
        $this->loadModel('Category');
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
        $this->layout = "admin_dashboard";
        $data['Size']['store_id'] = $this->Session->read('admin_store_id');
        $data['Size']['id'] = $this->Encryption->decode($EncryptSizeID);
        $data['Size']['is_deleted'] = 1;
        if ($this->Size->saveSize($data)) {
            //$this->ItemPrice->updateAll(
            //   array('ItemPrice.is_deleted' => 1),
            //   array('ItemPrice.size_id' => $data['Size']['id'])
            //);
            //$this->OfferDetail->updateAll(
            //   array('OfferDetail.is_deleted' => 1),
            //   array('OfferDetail.offerSize' => $data['Size']['id'])
            //);
            $this->Session->setFlash(__("Size deleted"), 'alert_success');
            $this->redirect(array('controller' => 'sizes', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'sizes', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:activateSize()
      Description:Active/deactive category sizes
      created:7/8/2015
      ----------------------------------------------------- */

    public function activateSize($EncryptedSizeID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Size']['store_id'] = $this->Session->read('admin_store_id');
        $data['Size']['id'] = $this->Encryption->decode($EncryptedSizeID);
        $data['Size']['is_active'] = $status;
        if ($this->Size->saveSize($data)) {
            if ($status) {
                $SuccessMsg = "Size Activated";
            } else {
                $SuccessMsg = "Size Deactivated and Size will not get Display in Menu List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'sizes', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'sizes', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:editSize()
      Description:Edit Category Size
      created:6/8/2015
      ----------------------------------------------------- */

    public function editSize($EncryptSizeID = null) {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'addSize')) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $data['Size']['id'] = $this->Encryption->decode($EncryptSizeID);
        $this->loadModel('Size');
        $sizeDetail = $this->Size->getSizeDetail($data['Size']['id'], $storeId);

        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $sizedata = array();
            $size = trim($this->data['Size']['size']);
            $categoryId = trim($this->data['Size']['category_id']);
            $isUniqueName = $this->Size->checkSizeUniqueName($size, $storeId, $categoryId, $data['Size']['id']);
            if ($isUniqueName) {
                $sizedata['id'] = $data['Size']['id'];
                $sizedata['size'] = trim($this->data['Size']['size']);
                $sizedata['category_id'] = $this->data['Size']['category_id'];
                $sizedata['is_active'] = $this->data['Size']['is_active'];
                $sizedata['store_id'] = $storeId;
                $sizedata['merchant_id'] = $merchantId;
                $this->Size->create();
                $this->Size->saveSize($sizedata);
                $this->Session->setFlash(__("Category Size Updated Successfully ."), 'alert_success');
                $this->redirect(array('controller' => 'sizes', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("Size Already exists"), 'alert_failed');
            }
        }
        $this->loadModel('Category');
        $categoryList = $this->Category->getCategoryListIsSize($storeId);
        $this->set('categoryList', $categoryList);
        $this->request->data = $sizeDetail;
    }

    /* ------------------------------------------------
      Function name:deleteMultipleSize()
      Description:Delete multiple size
      created:03/9/2015
      ----------------------------------------------------- */

    public function deleteMultipleSize() {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Size']['store_id'] = $this->Session->read('admin_store_id');
        $data['Size']['is_deleted'] = 1;
        if (!empty($this->request->data['Size']['id'])) {
            $filter_array = array_filter($this->request->data['Size']['id']);
            $i = 0;
            foreach ($filter_array as $k => $orderId) {
                $data['Size']['id'] = $orderId;
                $this->Size->saveSize($data);
                $i++;
            }
            $del = $i . "  " . "size deleted successfully.";
            $this->Session->setFlash(__($del), 'alert_success');
            $this->redirect(array('controller' => 'sizes', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:createAddonSize()
      Description:Add add-ons  size
      created:08/9/2015
      ----------------------------------------------------- */

    public function createAddonSize() {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'createAddonSize')) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        //$this->autoRender=false;
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');

        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $sizedata = array();
            $size = trim($this->data['AddonSize']['size']);
            $isUniqueName = $this->AddonSize->checkAddonSizeUniqueName($size, $storeId, $merchantId);
            if ($isUniqueName) {
                $sizedata['size'] = trim($this->data['AddonSize']['size']);
                $sizedata['price_percentage'] = $this->data['AddonSize']['price_percentage'];
                $sizedata['is_active'] = $this->data['AddonSize']['is_active1'];
                $sizedata['store_id'] = $storeId;
                $sizedata['merchant_id'] = $merchantId;
                $this->AddonSize->create();
                $this->AddonSize->saveAddonSize($sizedata);
                $this->Session->setFlash(__("Add-ons size created Successfully ."), 'alert_success');
                $this->redirect(array('controller' => 'sizes', 'action' => 'addOnSizeList'));
            } else {
                $this->Session->setFlash(__("Add-ons size Already exists"), 'alert_failed');
                $this->redirect(array('controller' => 'sizes', 'action' => 'addOnSizeList'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:addOnSizeList()
      Description:To display the list of add-ons size
      created:7/8/2015
      ----------------------------------------------------- */

    public function addOnSizeList($clearAction = null) {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'createAddonSize')) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');

        /*         * ****start******* */
        $value = "";
        $criteria = "AddonSize.store_id =$storeID AND AddonSize.is_deleted=0";
        if ($this->Session->read('SizeSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('SizeSearchData'), true);
        } else {
            $this->Session->delete('SizeSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('SizeSearchData', json_encode($this->request->data));
            if (isset($this->request->data['AddonSize']['is_active']) && $this->request->data['AddonSize']['is_active'] != '') {
                $active = trim($this->request->data['AddonSize']['is_active']);
                $criteria .= " AND (AddonSize.is_active =$active)";
            }
            if (!empty($this->request->data['AddonSize']['search'])) {
                $search = trim($this->request->data['AddonSize']['search']);
                $criteria .= " AND (AddonSize.size LIKE '%" . $search . "%')";
            }
        }
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
        $this->layout = "admin_dashboard";
        $data['AddonSize']['store_id'] = $this->Session->read('admin_store_id');
        $data['AddonSize']['id'] = $this->Encryption->decode($EncryptedSizeID);
        $data['AddonSize']['is_active'] = $status;
        if ($this->AddonSize->saveAddonSize($data)) {
            if ($status) {
                $SuccessMsg = "Add-ons size Activated";
            } else {
                $SuccessMsg = "Add-ons size Deactivated and Size will not get Display in Menu List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'sizes', 'action' => 'addOnSizeList'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'sizes', 'action' => 'addOnSizeList'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteAddonSize()
      Description:Delete add-ons size
      created:7/8/2015
      ----------------------------------------------------- */

    public function deleteAddonSize($EncryptSizeID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['AddonSize']['store_id'] = $this->Session->read('admin_store_id');
        $data['AddonSize']['id'] = $this->Encryption->decode($EncryptSizeID);
        $data['AddonSize']['is_deleted'] = 1;
        if ($this->AddonSize->saveAddonSize($data)) {
            $this->Session->setFlash(__("Add-ons size deleted"), 'alert_success');
            $this->redirect(array('controller' => 'sizes', 'action' => 'addOnSizeList'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'sizes', 'action' => 'addOnSizeList'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteMultipleAddonSize()
      Description:Delete multiple add-ons size
      created:03/9/2015
      ----------------------------------------------------- */

    public function deleteMultipleAddonSize() {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['AddonSize']['store_id'] = $this->Session->read('admin_store_id');
        $data['AddonSize']['is_deleted'] = 1;
        if (!empty($this->request->data['AddonSize']['id'])) {
            $filter_array = array_filter($this->request->data['AddonSize']['id']);
            $i = 0;
            foreach ($filter_array as $k => $orderId) {
                $data['AddonSize']['id'] = $orderId;
                $this->AddonSize->saveAddonSize($data);
                $i++;
            }
            $del = $i . "  " . "Add-ons size deleted successfully.";
            $this->Session->setFlash(__($del), 'alert_success');
            $this->redirect(array('controller' => 'sizes', 'action' => 'addOnSizeList'));
        }
    }

    /* ------------------------------------------------
      Function name:editAddonSize()
      Description:Add add-ons  size
      created:08/9/2015
      ----------------------------------------------------- */

    public function editAddonSize($EncryptSizeID = null) {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], 'createAddonSize')) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $data['AddonSize']['id'] = $this->Encryption->decode($EncryptSizeID);
        $this->loadModel('AddonSize');
        $sizeDetail = $this->AddonSize->getAddonSizeDetail($data['AddonSize']['id'], $storeId);

        if ($this->request->data) {
            $this->request->data = $this->Common->trimValue($this->request->data);
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
                $this->AddonSize->create();
                $this->AddonSize->saveAddonSize($sizedata);
                $this->Session->setFlash(__("Add-ons size created Successfully ."), 'alert_success');
                $this->redirect(array('controller' => 'sizes', 'action' => 'addOnSizeList'));
            } else {
                $this->Session->setFlash(__("Add-ons size Already exists"), 'alert_failed');
            }
        }

        $this->request->data = $sizeDetail;
    }

    public function uploadfile() {
        $this->layout = 'admin_dashboard';
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
                $i = 0;
                foreach ($real_data as $key => $row) {
                    $row = $this->Common->trimValue($row);
                    if ($key > 0) {
                        if (!empty($row['B']) && !empty($row['C'])) {
                            $storeId = $this->Session->read('admin_store_id');
                            $merchantId = $this->Session->read('admin_merchant_id');
                            if (!empty($storeId)) {
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
                                            $$isUniqueName = $this->Size->checkSizeUniqueName($Data, $storeId, $categoryId['Category']['id']);
                                        }
                                        if ($isUniqueName) {
                                            $sizedata['store_id'] = $storeId;
                                            $sizedata['merchant_id'] = $merchantId;
                                            $sizedata['size'] = $Data;
                                            $sizedata['category_id'] = $categoryId['Category']['id'];
                                            if (!empty($row['D'])) {
                                                $sizedata['is_active'] = $row['D'];
                                            } else {
                                                $sizedata['is_active'] = 0;
                                            }

                                            if (!empty($row['A'])) {
                                                $sizedata['id'] = $row['A'];
                                            } else {
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
                }
            }
            $this->Session->setFlash(__($i . ' ' . 'Size has been saved'), 'alert_success');
            $this->redirect(array("controller" => "sizes", "action" => "index"));
        }
    }

    public function download() {
        $storeId = $this->Session->read('admin_store_id');
        $this->Size->bindModel(array('belongsTo' => array('Category' => array('fields' => array('id', 'name')))), false);
        $result = $this->Size->findSizeList($storeId);
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
        $filename = 'Size_' . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('Size');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Category Name');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Size');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Active');

        // $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);

        $i = 2;
        foreach ($result as $data) {
            $data = $this->Common->trimValue($data);
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", $data['Size']['id']);
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", $data['Category']['name']);
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", $data['Size']['size']);
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", $data['Size']['is_active']);
            $i++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function uploadaddonsfile() {
        $this->layout = 'admin_dashboard';
//        $this->loadModel('AddOnSize');
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
                $i = 0;

                foreach ($real_data as $key => $row) {
                    $row = $this->Common->trimValue($row);
                    if ($key > 0) {
                        if (!empty($row['B']) && !empty($row['C'])) {
                            $storeId = $this->Session->read('admin_store_id');
                            $merchantId = $this->Session->read('admin_merchant_id');
                            if (!empty($storeId)) {
                                $sizeName = trim($row['B']);
                                if ($sizeName == 1) {
                                    continue;
                                }
                                if (!empty($row['A'])) {
                                    $isUniqueName = $this->AddonSize->checkAddOnSize($sizeName, $storeId, $row['A']);
                                } else {
                                    $isUniqueName = $this->AddonSize->checkAddOnSize($sizeName, $storeId);
                                }
                                if ($isUniqueName) {
                                    $sizedata['store_id'] = $storeId;
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
                                        $sizedata['id'] = $row['A'];
                                    } else {
                                        $sizedata['id'] = "";
                                        $this->AddonSize->create();
                                    }
                                    $this->AddonSize->saveAddonSize($sizedata);
                                    $i++;
                                }
                            }
                        }
                    }
                }
            }
            $this->Session->setFlash(__($i . ' ' . 'Addon Size has been saved'), 'alert_success');
            $this->redirect(array("controller" => "sizes", "action" => "addOnSizeList"));
        }
    }

    public function downloadaddonsize() {
        $storeId = $this->Session->read('admin_store_id');
//        $this->Size->bindModel(array('belongsTo'=>array('Category' =>array('fields'=>array('id','name')))),false);
        $result = $this->AddonSize->getAddonSizeDetailByStoreId($storeId);
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
        $filename = 'Add_on_Size_' . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('Add On Size');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'size');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'price_percentage');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Active');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Deleted');

        // $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);

        $i = 2;
        foreach ($result as $data) {
            $data = $this->Common->trimValue($data);
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", $data['AddonSize']['id']);
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", $data['AddonSize']['size']);
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", $data['AddonSize']['price_percentage']);
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", $data['AddonSize']['is_active']);
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", $data['AddonSize']['is_deleted']);
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
      Function name:index()
      Description:To display the list of type
      created:7/8/2015
      ----------------------------------------------------- */

    public function sizelisting($clearAction = null) {
        $this->layout = "admin_dashboard";
        $this->loadmodel("Item");
        $this->loadmodel("ItemPrice");
        $storeID = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');
        /*         * ****start******* */
        $value = "";
        $criteria = "ItemPrice.store_id =$storeID AND ItemPrice.is_deleted=0";
        $order = '';
        $pagingFlag = true;
        if ($this->Session->read('ItemSizeSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('ItemSizeSearchData'), true);
        } else {
            $this->Session->delete('ItemSizeSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }

        $flag = 0;
        if (!empty($this->request->data)) {
            $this->Session->write('ItemSizeSearchData', json_encode($this->request->data));
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
                } else {
                    $flag = 1;
                }
            }
        }
        if ($order == '') {
            $order = 'ItemPrice.position ASC';
        }
        if ($flag) {
            //$criteria .= " AND (ItemType.is_active =1)";
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
                )
            )
                ), false
        );

        $sizedetail = '';
        if ($pagingFlag) {
            $this->paginate = array('conditions' => array($criteria), 'order' => $order);
            $sizedetail = $this->paginate('ItemPrice');
        } else {
            //$sizedetail=$this->ItemPrice->find('all',array('conditions'=>array($criteria),'order'=>$order));
            $this->paginate = array('conditions' => array($criteria), 'order' => $order);
            $sizedetail = $this->paginate('ItemPrice');
        }
        //pr($typedetail);die;
        //pr($sizedetail);die;
        $this->set('list', $sizedetail);
        $this->set('pagingFlag', $pagingFlag);


//        $this->loadModel('Category');
//        $this->Item->bindModel(array(
//            'belongsTo' => array('Category')
//        ));
//        $itemList = $this->Item->find('list', array(
//            'fields' => array('Item.id', 'Item.name'),
//            'conditions' => array('Item.store_id' => $storeID, 'Item.is_deleted' => 0, 'Item.is_active' => 1, 'Category.is_deleted' => 0, 'Category.is_active' => 1),
//            'recursive' => 1
//        ));
        $itemList = $this->ItemPrice->find('all', array('conditions' => array('ItemPrice.store_id' => $storeID, 'ItemPrice.is_deleted' => 0),'group'=>array('ItemPrice.item_id')));
        //$itemList=$this->Item->getallItemsByStore($storeID);
        $nList = array();
        if (!empty($itemList)) {
            foreach ($itemList as $iList) {
                if (!empty($iList['Item']) && !empty($iList['Size'])) {
                    $nList[$iList['Item']['id']] = $iList['Item']['name'];
                }
            }
        }
        $this->set('itemList', $nList);
    }

    /* ------------------------------------------------
      Function name:updateitempreOrder()
      Description: Update the display order
      created Date:16/12/2015
      created By:Praveen Soni
      ----------------------------------------------------- */

    public function updateitempreOrder() {
        $this->autoRender = false;
        if (isset($_GET) && !empty($_GET)) {
            $this->loadmodel("ItemPrice");
            foreach ($_GET as $key => $val) {
                $this->ItemPrice->updateAll(array('position' => $val), array('ItemPrice.id' => $this->Encryption->decode($key)));
            }
        }
    }

    /* ------------------------------------------------
      Function name:activateType()
      Description:Active/deactive type
      created:7/8/2015
      ----------------------------------------------------- */

    public function activateItemSize($EncryptedTypeID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['ItemPrice']['store_id'] = $this->Session->read('admin_store_id');
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
            $this->redirect(array('controller' => 'sizes', 'action' => 'sizelisting'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'sizes', 'action' => 'sizelisting'));
        }
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
            $storeID = $this->Session->read('admin_store_id');
            $searchData = $this->Size->find('all', array('fields' => array('Size.size', 'Category.name'), 'conditions' => array('OR' => array('Size.size LIKE' => '%' . $_GET['term'] . '%', 'Category.name LIKE' => '%' . $_GET['term'] . '%'), 'Size.is_deleted' => 0, 'Size.store_id' => $storeID)));
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

    public function getAddonsSizeName() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('AddonSize');
            $storeID = $this->Session->read('admin_store_id');
            $searchData = $this->AddonSize->find('list', array('fields' => array('AddonSize.size', 'AddonSize.size'), 'conditions' => array('OR' => array('AddonSize.size LIKE' => '%' . $_GET['term'] . '%'), 'AddonSize.is_deleted' => 0, 'AddonSize.store_id' => $storeID)));
            echo json_encode($searchData);
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
            $storeID = $this->Session->read('admin_store_id');
            $searchData = $this->ItemPrice->find('all', array('fields' => array('Size.size', 'Item.name'), 'conditions' => array('OR' => array('Size.size LIKE' => '%' . $_GET['term'] . '%', 'Item.name LIKE' => '%' . $_GET['term'] . '%'), 'ItemPrice.is_deleted' => 0, 'ItemPrice.store_id' => $storeID)));
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

}
