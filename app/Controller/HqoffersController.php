<?php

App::uses('HqAppController', 'Controller');

class HqoffersController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'Common');
    public $uses = array('OfferDetail', 'Offer', 'Store', 'Item', 'Size');
    public $layout = 'hq_dashboard';

    public function beforeFilter() {
        //Check permission for Admin User
        parent::beforeFilter();
    }

    public function getItemByStoreId() {
        if ($this->request->is('ajax') && $this->request->data['storeId']) {
            $this->Item->bindModel(array(
                'belongsTo' => array('Category')
            ));
            if ($this->request->data['storeId'] == 'All') {
                $merchant_id = $this->Session->read('merchantId');
                $itemlist = $this->Item->find('list', array('recursive' => 1, 'fields' => array('Item.name', 'Item.name'), 'conditions' => array('Item.merchant_id' => $merchant_id, 'Item.is_active' => 1, 'Item.is_deleted' => 0, 'Category.is_deleted' => 0, 'Category.is_active' => 1), 'group' => array('Item.name')));
            } else {
                $itemlist = $this->Item->getAllItems($this->request->data['storeId']);
            }
            $this->set('itemList', $itemlist);
        } else {
            exit;
        }
    }

    /* ------------------------------------------------
      Function name:getItemSize()
      Description:To find list of the Sizes
      created:3/8/2015
      ----------------------------------------------------- */

    public function getItemSize($itemId = null, $storeId = null) {
        $this->loadModel('Size');
        $this->loadModel('Category');
        if ($itemId && $storeId) {
            $sizeList = '';
            if ($storeId == 'All') {
                $merchant_id = $this->Session->read('merchantId');
                $this->loadModel('ItemPrice');
                $category = $this->Item->find('list', array('fields' => array('Item.id'), 'conditions' => array('Item.merchant_id' => $merchant_id, 'LOWER(Item.name)' => strtolower($itemId), 'Item.is_active' => 1, 'Item.is_deleted' => 0)));
                if (!empty($category)) {
                    $sizeList = $this->ItemPrice->find('list', array('fields' => array('Size.size', 'Size.size'), 'conditions' => array('ItemPrice.item_id' => $category, 'ItemPrice.merchant_id' => $merchant_id, 'ItemPrice.is_active' => 1, 'ItemPrice.is_deleted' => 0, 'Size.is_active' => 1, 'Size.is_deleted' => 0), 'group' => array('size'), 'recursive' => 2));
                }
            } else {
                $category = $this->Item->getcategoryByitemID($itemId, $storeId);
                if ($category) {
                    $categoryId = $category['Item']['category_id'];
                    if ($this->Category->checkCategorySizeExists($categoryId, $storeId)) {
                        $this->loadModel('ItemPrice');
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
                        if ($sizeListarray) {
                            foreach ($sizeListarray as $value) {
                                if ($value['Size']) {
                                    $sizeList[$value['ItemPrice']['size_id']] = $value['Size']['size'];
                                }
                            }
                        }
                    }
                }
            }
            $this->set('sizeList', $sizeList);
        } else {
            exit;
        }
    }

    /* ------------------------------------------------
      Function name:getMultipleItemSizes()
      Description:To find list of the Sizes
      created:23/8/2016
      ----------------------------------------------------- */

    public function getMultipleItemSizes($storeId = null) {
        $this->loadModel('Size');
        $this->loadModel('Category');
        if ($this->request->data && $storeId) {
            $sizeList = array();
            if ($storeId == 'All') {
                $merchant_id = $this->Session->read('merchantId');
                foreach ($this->request->data['Offered']['id'] as $key => $itemId) {
                    $item = $this->Item->find('list', array('fields' => 'id', 'conditions' => array('merchant_id' => $merchant_id, 'LOWER(name)' => strtolower($itemId), 'is_active' => 1, 'is_deleted' => 0), 'recursive' => -1));
                    if (!empty($item)) {
                        $this->loadModel('ItemPrice');
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
                        $sizeListarray = $this->ItemPrice->find('all', array('fields' => array('ItemPrice.size_id', 'ItemPrice.item_id'), 'conditions' => array('ItemPrice.item_id' => $item, 'ItemPrice.merchant_id' => $merchant_id, 'ItemPrice.is_active' => 1, 'ItemPrice.is_deleted' => 0), 'recursive' => 2, 'group' => 'Size.size'));
                        if ($sizeListarray) {
                            foreach ($sizeListarray as $key => $value) {
                                if ($value['Size']) {
                                    $sizeList[$value['ItemPrice']['item_id']][$value['Size']['size']] = $value['Size']['size'];
                                } else {
                                    $sizeList[$value['ItemPrice']['item_id']] = '';
                                }
                            }
                        } else {
                            $item = $this->Item->find('first', array('fields' => 'id', 'conditions' => array('merchant_id' => $merchant_id, 'LOWER(name)' => strtolower($itemId), 'is_active' => 1, 'is_deleted' => 0), 'recursive' => -1));
                            $sizeList[$item['Item']['id']] = '';
                        }
                    }
                }
            } else {
                foreach ($this->request->data['Offered']['id'] as $key => $itemId) {
                    $category = $this->Item->getcategoryByitemID($itemId, $storeId);
                    if ($category) {
                        $categoryId = $category['Item']['category_id'];
                        if ($this->Category->checkCategorySizeExists($categoryId, $storeId)) {
                            $this->loadModel('ItemPrice');
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
                        } else {
                            $sizeList[$itemId] = '';
                        }
                    }
                }
            }
            $this->set('sizeList', $sizeList);
        } else {
            exit;
        }
    }

    public function getItemByStoreIdMultiselect() {
        if ($this->request->is('ajax') && $this->request->data['storeId']) {
            if ($this->request->data['storeId'] == 'All') {
                $merchant_id = $this->Session->read('merchantId');
                $itemlist = $this->Item->find('list', array('fields' => array('name', 'name'), 'conditions' => array('merchant_id' => $merchant_id, 'is_active' => 1, 'is_deleted' => 0), 'group' => 'name'));
            } else {
                $itemlist = $this->Item->getAllItems($this->request->data['storeId']);
            }
            $this->set('itemList', $itemlist);
        } else {
            exit;
        }
    }

    private function _saveOffers($merchant_id = null, $postData = null, $temp = null) {
        $this->request->data = $postData;
        $storeId = $this->request->data['Offer']['store_id'];
        if (!empty($this->request->data['Item']['id']) && $temp == 'All') {
            $iteamData = $this->Item->find('first', array('fields' => 'id', 'conditions' => array('store_id' => $storeId, 'LOWER(name)' => strtolower($this->request->data['Item']['id']), 'is_deleted' => 0, 'is_active' => 1), 'recursive' => -1));
            if (!empty($iteamData)) {
                $offerData['item_id'] = $iteamData['Item']['id'];
            }
        } else {
            $offerData['item_id'] = $this->request->data['Item']['id'];
        }
        if (!empty($offerData['item_id'])) {
            if ($this->request->data['Offer']['is_time'] == 0) {
                $this->request->data['Offer']['offer_start_time'] = "";
                $this->request->data['Offer']['offer_end_time'] = "";
            }
            $offerData['offer_start_date'] = '';
            $offerData['offer_end_date'] = '';
            if ($this->request->data['Offer']['offer_start_date'] && $this->request->data['Offer']['offer_end_date']) {
                $offerData['offer_start_date'] = $this->Dateform->formatDate($this->request->data['Offer']['offer_start_date']);
                $offerData['offer_end_date'] = $this->Dateform->formatDate($this->request->data['Offer']['offer_end_date']);
            }
            //Offer Data

            $offerData['offerImage'] = $this->request->data['Offer']['imgcat'];
            $offerData['store_id'] = $storeId;
            $offerData['merchant_id'] = $merchant_id;
            $offerData['unit'] = $this->request->data['Offer']['unit'];
            $offerData['offer_end_time'] = $this->request->data['Offer']['offer_end_time'];
            $offerData['offer_start_time'] = $this->request->data['Offer']['offer_start_time'];
            $offerData['is_time'] = $this->request->data['Offer']['is_time'];
            $offerData['description'] = trim($this->request->data['Offer']['description']);
            if (isset($this->request->data['Size']['id'])) {
                if ($temp == 'All') {
                    $sizeData = $this->Size->find('first', array('fields' => 'id', 'conditions' => array('store_id' => $storeId, 'LOWER(size)' => strtolower($this->request->data['Size']['id']), 'is_deleted' => 0, 'is_active' => 1), 'recursive' => -1));
                    if (!empty($sizeData)) {
                        $offerData['size_id'] = $sizeData['Size']['id'];
                    }
                } else {
                    $offerData['size_id'] = $this->request->data['Size']['id'];
                }
            }
            $offerData['is_fixed_price'] = $this->request->data['Offer']['is_fixed_price'];
            $offerData['offerprice'] = ($this->request->data['Offer']['offerprice']) ? $this->request->data['Offer']['offerprice'] : 0;
            $offerData['is_active'] = ($this->request->data['Offer']['is_active'] == 1) ? 1 : 0;
            $this->Offer->create();
            $this->Offer->saveOffer($offerData);
            $offerID = $this->Offer->getLastInsertId();
            if ($offerID) {
                if (isset($this->request->data['OfferDetails']) && $this->request->data['OfferDetails']) {
                    $i = 0;
                    foreach ($this->request->data['OfferDetails'] as $offerdetails) {
                        if ($this->request->data['Offered']['id'][$i] && $temp == 'All') {
                            $IteamData = $this->Item->find('first', array('fields' => 'id', 'conditions' => array('store_id' => $storeId, 'merchant_id' => $merchant_id, 'LOWER(name)' => strtolower($this->request->data['Offered']['id'][$i]), 'is_deleted' => 0, 'is_active' => 1), 'recursive' => -1));
                            if (!empty($IteamData['Item']['id'])) {
                                $offerdetailsData['offerItemID'] = $IteamData['Item']['id'];
                            } else {
                                $offerdetailsData['offerItemID'] = 0;
                            }
                        } else {
                            $offerdetailsData['offerItemID'] = $this->request->data['Offered']['id'][$i];
                        }
                        $offerdetailsData['offer_id'] = $offerID;
                        $offerdetailsData['store_id'] = $storeId;
                        $offerdetailsData['merchant_id'] = $merchant_id;
                        $priceArray = explode(',', $offerdetails['discountAmt']);
                        if (!$priceArray[0]) {
                            $priceArray[0] = 0;
                        }
                        if (isset($offerdetails['offerSize']) && $offerdetails['offerSize']) {
                            if ($temp == 'All') {
                                $sizeDataId = $this->Size->find('first', array('fields' => 'id', 'conditions' => array('store_id' => $storeId, 'LOWER(size)' => strtolower($offerdetails['offerSize']), 'is_deleted' => 0, 'is_active' => 1), 'recursive' => -1));
                                if (!empty($sizeDataId)) {
                                    $offerdetailsData['offerSize'] = $sizeDataId['Size']['id'];
                                }
                            } else {
                                $offerdetailsData['offerSize'] = $offerdetails['offerSize'];
                            }
                            $offerdetailsData['discountAmt'] = $priceArray[0];
                            $this->OfferDetail->create();
                            $this->OfferDetail->saveOfferDetail($offerdetailsData);
                        } else {
                            $offerdetailsData['offerSize'] = 0;
                            $offerdetailsData['discountAmt'] = $priceArray[0];
                            $this->OfferDetail->create();
                            $this->OfferDetail->saveOfferDetail($offerdetailsData);
                        }
                        $i++;
                    }
                }
            } else {
                $this->Session->setFlash(__("Offer Not Created"), 'alert_failed');
            }
        }
    }

    /* ------------------------------------------------
      Function name:deleteOfferPhoto()
      Description:Delete Offer Photo
      created:8/11/2016
      ----------------------------------------------------- */

    public function deleteOfferPhoto($EncryptOfferID = null) {
        $this->autoRender = false;
        $data['Offer']['id'] = $this->Encryption->decode($EncryptOfferID);
        $data['Offer']['offerImage'] = '';
        if ($this->Offer->saveOffer($data)) {
            $this->Session->setFlash(__("Offer Photo deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqoffers', 'action' => 'editOffer', $EncryptOfferID));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqoffers', 'action' => 'editOffer', $EncryptOfferID));
        }
    }

    public function index() {
        $merchant_id = $this->Session->read('merchantId');
        $start = "00:30";
        $end = "24:00";
        $timeRange = $this->Common->getStoreTimeForHq($start, $end);
        $this->set('timeOptions', $timeRange);
        if ($this->request->is('post') && !empty($this->request->data['Offer']['store_id'])) {
            $merchant_id = $this->Session->read('merchantId');
            $this->request->data = $this->Common->trimValue($this->request->data);
            $response = $this->Common->uploadMenuItemImages($this->request->data['Offer']['imgcat'], '/Offer-Image/', $merchant_id, 480, 320);
            if (!$response['status']) {
                $this->Session->setFlash(__($response['errmsg'], 'alert_failed'));
            } else {
                $this->request->data['Offer']['imgcat'] = $response['imagename'];
                if ($this->request->data['Offer']['store_id'] == 'All') {
                    $storeData = $this->Store->getAllStoreByMerchantId($merchant_id);
                    if (!empty($storeData)) {
                        foreach ($storeData as $store) {
                            $this->request->data['Offer']['store_id'] = $store['Store']['id'];
                            $this->_saveOffers($merchant_id, $this->request->data, 'All');
                        }
                    }
                } else {
                    $this->_saveOffers($merchant_id, $this->request->data);
                }
                $this->request->data = '';
                $this->Session->setFlash(__("Offer Successfully Created"), 'alert_success');
            }
        }
        $this->_offerListing();
    }

    private function _offerListing($clearAction = null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $storeID = @$this->request->data['Offer']['storeId'];
        if (empty($storeID) && $this->Session->read('HqOfferSearchData')) {
            $data = json_decode($this->Session->read('HqOfferSearchData'), true);
            if (!empty($data['Offer']['storeId'])) {
                $storeID = $data['Offer']['storeId'];
            }
        }
        $merchant_id = $this->Session->read('merchantId');

        $value = "";
        $criteria = "Offer.merchant_id =$merchant_id AND Offer.is_deleted=0";
        if (!empty($storeID)) {
            $criteria .= " AND Offer.store_id =$storeID";
        }
        if ($this->Session->read('HqOfferSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqOfferSearchData'), true);
        } else {
            $this->Session->delete('HqOfferSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('HqOfferSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['Offer']['keyword'])) {
                $value = trim($this->request->data['Offer']['keyword']);
                $criteria .= " AND (Offer.description LIKE '%" . $value . "%' OR Item.name LIKE '%" . $value . "%')";
            }
            if (isset($this->request->data['Offer']['isActive']) && $this->request->data['Offer']['isActive'] != '') {
                $active = trim($this->request->data['Offer']['isActive']);
                $criteria .= " AND (Offer.is_active =$active)";
            }
            if (isset($this->request->data['Item']['id']) && $this->request->data['Item']['id'] != '') {
                $item = trim($this->request->data['Item']['id']);
                $criteria .= " AND (Offer.item_id =$item)";
            }
        }
        $this->Offer->bindModel(
                array(
            'belongsTo' => array(
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id',
                    'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                    'fields' => array('id', 'name'),
                    'type' => 'INNER'
                ),
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1),
                    'fields' => array('store_name'),
                    'type' => 'INNER'
                )
            )
                ), false
        );
        $this->paginate = array('conditions' => array($criteria), 'order' => array('Offer.created' => 'DESC'));
        $itemdetail = $this->paginate('Offer');
        $this->set('list', $itemdetail);
        $this->set('keyword', $value);
        //$itemlist = $this->Item->getAllItems($storeID);
        $nList = array();
        if (!empty($storeID)) {
            $this->loadModel('Category');
            $this->Item->bindModel(array(
                'belongsTo' => array('Category' => array(
                        'className' => 'Category',
                        'foreignKey' => 'category_id',
                        'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                        'fields' => array('id', 'name'),
                        'type' => 'INNER'
                    )
                )
            ));
            $itemList = $this->Offer->find('all', array(
                'fields' => array('Offer.id', 'Offer.item_id', 'Item.id', 'Item.name', 'Item.category_id'),
                'conditions' => array('Item.store_id' => $storeID, 'Item.is_deleted' => 0, 'Item.is_active' => 1, 'Offer.is_deleted' => 0),
                'recursive' => 2
            ));
            $nList = array();
            if (!empty($itemList)) {
                foreach ($itemList as $iList) {
                    if (!empty($iList['Item']) && !empty($iList['Item']['Category'])) {
                        $nList[$iList['Item']['id']] = $iList['Item']['name'];
                    }
                }
            }
        }
        $this->set('itemLists', $nList);
    }

    /* ------------------------------------------------
      Function name:activateOffer()
      Description:Active/deactive Offer
      created:23/8/2016
      ----------------------------------------------------- */

    public function activateOffer($EncryptOfferID = null, $status = 0) {
        $this->autoRender = false;
        $data['Offer']['merchant_id'] = $this->Session->read('merchantId');
        $data['Offer']['id'] = $this->Encryption->decode($EncryptOfferID);
        $data['Offer']['is_active'] = $status;
        if ($this->Offer->saveOffer($data)) {
            if ($status) {
                $SuccessMsg = "Offer Activated";
                $this->Session->setFlash(__($SuccessMsg), 'alert_success');
                $this->redirect(array('controller' => 'hqoffers', 'action' => 'editOffer/' . $EncryptOfferID . "#OfferOfferStartDate"));
            } else {
                $SuccessMsg = "Offer Deactivated and Offer will not get Display";
                $this->Session->setFlash(__($SuccessMsg), 'alert_success');
                $this->redirect(array('controller' => 'hqoffers', 'action' => 'index'));
            }
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqoffers', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteOffer()
      Description:Delete Offer
      created:23/8/2015
      ----------------------------------------------------- */

    public function deleteOffer($EncryptOfferID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Offer']['merchant_id'] = $this->Session->read('merchantId');
        $data['Offer']['id'] = $this->Encryption->decode($EncryptOfferID);
        $data['Offer']['is_deleted'] = 1;
        if ($this->Offer->saveOffer($data)) {
            $this->Session->setFlash(__("Offer deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqoffers', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqoffers', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:editMenuItem()
      Description:Update Menu Item created:5/8/2015
      ----------------------------------------------------- */

    public function editOffer($EncryptedofferID = null) {
        $merchant_id = $this->Session->read('merchantId');
        $start = "00:30";
        $end = "24:00";
        $timeRange = $this->Common->getStoreTimeForHq($start, $end);
        $this->set('timeOptions', $timeRange);
        $data['Offer']['id'] = $this->Encryption->decode($EncryptedofferID);
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $storeId = $this->request->data['Offer']['store_id'];
            $offerData['offer_start_date'] = '';
            $offerData['offer_end_date'] = '';
            if ($this->request->data['Offer']['offer_start_date'] && $this->request->data['Offer']['offer_end_date']) {
                $offerData['offer_start_date'] = $this->Dateform->formatDate($this->request->data['Offer']['offer_start_date']);
                $offerData['offer_end_date'] = $this->Dateform->formatDate($this->request->data['Offer']['offer_end_date']);
            }
            if ($this->request->data['Offer']['is_time'] == 0) {
                $this->request->data['Offer']['offer_start_time'] = "";
                $this->request->data['Offer']['offer_end_time'] = "";
            }
            $offerData['id'] = $this->request->data['Offer']['id'];
            $offerData['store_id'] = $storeId;
            $offerData['offer_start_time'] = $this->request->data['Offer']['offer_start_time'];
            $offerData['offer_end_time'] = $this->request->data['Offer']['offer_end_time'];
            $offerData['is_time'] = $this->request->data['Offer']['is_time'];
            $offerData['merchant_id'] = $merchant_id;
            $offerData['item_id'] = $this->request->data['Item']['id'];
            $offerData['description'] = trim($this->request->data['Offer']['description']);
            if (isset($this->request->data['Size']['id'])) {
                $offerData['size_id'] = $this->request->data['Size']['id'];
            }
            $offerData['unit'] = $this->request->data['Offer']['unit'];
            $offerData['is_fixed_price'] = $this->request->data['Offer']['is_fixed_price'];
            $offerData['offerprice'] = ($this->request->data['Offer']['offerprice']) ? $this->request->data['Offer']['offerprice'] : 0;
            $offerData['is_active'] = ($this->request->data['Offer']['is_active'] == 1) ? 1 : 0;
            if ($this->request->data['Offer']['imgcat']['error'] == 0) {
                $response = $this->Common->uploadMenuItemImages($this->request->data['Offer']['imgcat'], '/Offer-Image/', $storeId, 480, 320);
            } elseif ($this->request->data['Offer']['imgcat']['error'] == 4) {
                $response['status'] = true;
                $response['imagename'] = '';
            }
            if (!$response['status']) {
                $this->Session->setFlash(__($response['errmsg']));
            } else {
                //Item Data
                if ($response['imagename']) {
                    $offerData['offerImage'] = $response['imagename'];
                }
                $this->Offer->saveOffer($offerData);
                $this->Session->setFlash(__("Offer Updated"));
                if ($this->OfferDetail->deleteallOfferItems($this->request->data['Offer']['id'])) {
                    if (isset($this->request->data['OfferDetails']) && $this->request->data['OfferDetails']) {
                        foreach ($this->request->data['OfferDetails'] as $details) {
                            if (isset($details['id'])) {
                                $offerdetails['id'] = $details['id'];
                            } else {
                                $offerdetails['id'] = '';
                            }
                            $offerdetails['offer_id'] = $this->request->data['Offer']['id'];
                            $offerdetails['offerItemID'] = $details['item_id'];
                            $offerdetails['is_deleted'] = 0;
                            if (isset($details['offerSize']) && $details['offerSize']) {
                                $offerdetails['offerSize'] = $details['offerSize'];
                            } else {
                                $offerdetails['offerSize'] = 0;
                            }
                            if ($details['discountAmt']) {
                                $offerdetails['discountAmt'] = $details['discountAmt'];
                            } else {
                                $offerdetails['discountAmt'] = 0;
                            }
                            $this->OfferDetail->saveOfferDetail($offerdetails);
                        }
                    }
                    $this->Session->setFlash(__("Offer Updated"), 'alert_success');
                    $this->redirect(array('controller' => 'hqoffers', 'action' => 'index'));
                } else {
                    $this->Session->setFlash(__("Some Problem occured"), 'alert_failed');
                }
            }
        }


        $this->Offer->bindModel(
                array(
            'hasMany' => array(
                'OfferDetail' => array(
                    'className' => 'OfferDetail',
                    'foreignKey' => 'offer_id',
                    'conditions' => array('OfferDetail.is_deleted' => 0),
                    'fields' => array('OfferDetail.id', 'OfferDetail.offer_id', 'OfferDetail.offerItemID', 'OfferDetail.offerSize', 'OfferDetail.discountAmt')
                )
            )
                ), false
        );
        $offerDetails = $this->Offer->getOfferDetails($data['Offer']['id']);

        $storeId = $offerDetails['Offer']['store_id'];
        $FinalOfferDetails['Offered'] = array();
        foreach ($offerDetails as $key => $Offer) {
            if ($key == "Offer") {
                $FinalOfferDetails['Item']['id'] = $Offer['item_id'];
                $FinalOfferDetails['Size']['id'] = $Offer['size_id'];
                $FinalOfferDetails['Offer']['store_id'] = $Offer['store_id'];
                $FinalOfferDetails['Offer']['description'] = $Offer['description'];
                $FinalOfferDetails['Offer']['is_fixed_price'] = $Offer['is_fixed_price'];
                $FinalOfferDetails['Offer']['offerprice'] = $Offer['offerprice'];
                $FinalOfferDetails['Offer']['offer_start_date'] = $Offer['offer_start_date'];
                $FinalOfferDetails['Offer']['offer_end_date'] = $Offer['offer_end_date'];
                $FinalOfferDetails['Offer']['is_active'] = $Offer['is_active'];
                $FinalOfferDetails['Offer']['offer_start_time'] = $Offer['offer_start_time'];
                $FinalOfferDetails['Offer']['offer_end_time'] = $Offer['offer_end_time'];
                $FinalOfferDetails['Offer']['is_time'] = $Offer['is_time'];
                $FinalOfferDetails['Offer']['imgcat'] = $Offer['offerImage'];
                $FinalOfferDetails['Offer']['id'] = $Offer['id'];
                $FinalOfferDetails['Offer']['unit'] = $Offer['unit'];
            } elseif ($key == "OfferDetail") {
                if ($Offer) {
                    //$keyforprevious=0;
                    foreach ($Offer as $vkey => $offerdetails) {
                        $FinalOfferDetails['Offered']['id'][$vkey] = $offerdetails['offerItemID'];
                        $FinalOfferDetails['OfferDetails'][$vkey]['id'] = $offerdetails['id'];
                        $FinalOfferDetails['OfferDetails'][$vkey]['item_id'] = $offerdetails['offerItemID'];
                        $FinalOfferDetails['OfferDetails'][$vkey]['offer_id'] = $offerdetails['offer_id'];
                        $FinalOfferDetails['OfferDetails'][$vkey]['offerSize'] = $offerdetails['offerSize'];
                        $FinalOfferDetails['OfferDetails'][$vkey]['discountAmt'] = $offerdetails['discountAmt'];
                    }
                }
            }
        }
        $this->request->data = $FinalOfferDetails;
        $sizepost = 0;
        $sizeList = "";
        $isfixed = 0;
        $istimeRestriction = 0;
        if (isset($this->request->data['Item']['id'])) {
            $this->Item->getcategoryByitemID($this->request->data['Item']['id'], $storeId);
            $this->loadModel('ItemPrice');
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

            $sizeListarray = $this->ItemPrice->getItemSizes($this->request->data['Item']['id'], $storeId);
            foreach ($sizeListarray as $key => $value) {
                if (isset($value['Size']['size'])) {
                    $sizeList[$value['ItemPrice']['size_id']] = $value['Size']['size'];
                }
            }
            $sizepost = 1;
        }
        if (isset($this->request->data['Offer']['is_fixed_price']) && $this->request->data['Offer']['is_fixed_price']) {
            $isfixed = 1;
        }
        if (isset($this->request->data['Offer']['is_time']) && $this->request->data['Offer']['is_time']) {
            $istimeRestriction = 1;
        }
        $itemlist = $this->Item->getAllItems($storeId);
        $this->set('istimeRestriction', $istimeRestriction);
        $this->set('itemList', $itemlist);
        $this->set('isfixed', $isfixed);
        $this->set('sizepost', $sizepost);
        $this->set('sizeList', $sizeList);
    }

    public function uploadfile() {
        $this->layout = 'hq_dashboard';
        if (!empty($this->request->data)) {
            $tmp = $this->request->data;
            $this->loadModel('Store');
            $this->loadModel('Size');
            $this->loadModel('Item');
            $this->loadModel('ItemPrice');
            if ($tmp[
                    'Offer']['file']['error'] == 4) {
                $this->Session->setFlash(__('Your file contains error. Please retry uploading.'), 'alert _failed');
                $this->redirect($this->here);
            }
            $valid = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            if (!in_array($tmp['Offer']['file']['type'], $valid)) {
                $this->Session->setFlash(__('You can only upload Excel file.'), 'alert_failed');
            } else if ($tmp['Offer']['file']['error'] != 0) {
                $this->Session->setFlash(__('The file you uploaded contains errors.'), 'alert_failed');
            } else if ($tmp['Offer']['file']['size'] > 20000000) {
                $this->Session->setFlash(__('The file size must be Max 20MB'), 'alert_failed');
            } else {
                ini_set('max_execution_time', 600); //increase max_execution_time to 10 min if data set is very large
                App::import('Vendor', 'PHPExcel');
                $objPHPExcel = new PHPExcel;
                $objPHPExcel = PHPExcel_IOFactory::load($tmp['Offer']['file']['tmp_name']);
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                $real_data = array_values($sheetData);
                $merchantId = $this->Session->read('merchantId');
                $storeId = $this->request->data['Offer']['store_id'];
                if ($storeId == "All") {
                    $storeId = $this->Store->find('list', array('fields' => array('id'), 'conditions' => array('Store.merchant_id' => $merchantId)));
                    $i = $this->offerForMultipleStore($storeId, $real_data, $merchantId);
                } else {
                    $i = $this->saveOffer($real_data, $storeId, $merchantId);
                }
                $this->Session->setFlash(__($i . ' ' . 'Hq Promotions has been saved'), 'alert_success');
                $this->redirect(array("controller" => "hqoffers", "action" => "index"));
            }
        }
    }

    public function offerForMultipleStore($storeIds = array(), $real_data = array(), $merchantId = null) {
        $i = 0;
        if (!empty($storeIds)) {
            foreach ($storeIds as $storeId) {
                $k = $this->saveOffer($real_data, $storeId, $merchantId);
                if (is_numeric($k)) {
                    $i = $i + $k;
                }
            }
        }
        return $i;
    }

    public function saveOffer($real_data = null, $storeId, $merchantId) {
        $i = 0;
        foreach ($real_data as $key => $row) {
            $row['A'] = trim($row['A']);
            if (!empty($row['A'])) {
                $isUniqueId = $this->Offer->checkOfferWithId($row['A']);
                if (!empty($isUniqueId) && $isUniqueId['Offer']['store_id'] != $storeId) {
                    continue;
                }
            }
            $row = $this->Common->trimValue($row);
            if ($key > 0) {
                if (!empty($row['B']) && !empty($row['C']) && !empty($row['E'])) {
                    $itemId = $this->Item->getItemIdByName($storeId, trim($row['B']));
                    if (!empty($itemId)) {
                        if (!empty($row['D'])) {
                            $sizeId = $this->Size->getSizeIdByNameOnly(trim($row['D']), $storeId);
                            if ($sizeId) {
                                $itemsizeId = $this->ItemPrice->getItemPriceByName($itemId ['Item']['id'], $sizeId['Size']['id'], $storeId);
                                $itemsizeId['Size']['id'] = $sizeId['Size']['id'];
                            } else {
                                $itemsizeId['Size']['id'] = 0;
                            }
                        } else {
                            $itemsizeId['Size']['id'] = 0;
                        }

                        if ($itemsizeId) {
                            $offerData['merchant_id'] = $merchantId;
                            $offerData['item_id'] = $itemId['Item']['id'];
                            $offerData['unit'] = $row['C'];
                            $offerData['description'] = $row['E'];
                            $offerData['size_id'] = $itemsizeId['Size']['id'];

                            if (!empty($row['F'])) {
                                $offerData['is_fixed_price'] = $row['F'];
                            } else {
                                $offerData['is_fixed_price'] = 0;
                            }
                            if (!empty($row['G'])) {
                                $offerData['offerprice'] = $row['G'];
                            } else {
                                $offerData['offerprice'] = 0;
                            }

                            if (!empty($row['H'])) {
                                $offerData['offer_start_date'] = $this->Dateform->formatDate($row['H']);
                            } else {
                                $offerData['offer_start_date'] = '';
                            }

                            if (!empty($row['I'])) {
                                $offerData['offer_end_date'] = $this->Dateform->formatDate($row['I']);
                            } else {
                                $offerData['offer_end_date'] = '';
                            }

                            if (!empty($row['J'])) {
                                if ($row['J'] == 1) {
                                    $itemdata['is_time'] = 1;
                                    if (!empty($row['K']) && !empty($row['L'])) {
                                        $itemdata['offer_start_time'] = $row['K'];
                                        $itemdata['offer_end_time'] = $row['L'];
                                    } else {
                                        $itemdata['is_time'] = 0;
                                        $itemdata['offer_start_time'] = '00:30:00';
                                        $itemdata['offer_end_time'] = '00:30:00';
                                    }
                                } else {
                                    $itemdata['is_time'] = 0;
                                    $itemdata['offer_start_time'] = '00:30:00';
                                    $itemdata['offer_end_time'] = '00:30:00';
                                }
                            } else {
                                $itemdata['is_time'] = 0;
                                $itemdata['offer_start_time'] = '00:30:00';
                                $itemdata['offer_end_time'] = '00:30:00';
                            }

                            if (!empty($row['M'])) {
                                $offerData['is_active'] = $row['M'];
                            } else {
                                $offerData['is_active'] = 0;
                            }

                            if (!empty($row['A'])) {
                                $offerData['id'] = $row['A'];
                            } else {
                                $offerData['store_id'] = $storeId;
                                $offerData['id'] = "";
                                $this->Offer->create();
                            }

                            $this->Offer->saveOffer($offerData);
                            if (!empty($row['A'])) {
                                $offerID = $row['A'];
                            } else {
                                $offerID = $this->Offer->getLastInsertId();
                            }
                            if ($offerID) {
                                if (!empty($row['A'])) {
                                    $this->OfferDetail->deleteallOfferItems($offerID);
                                }
                                $da = 'N';
                                while ($da) {
                                    if (empty($row[$da])) {
                                        break;
                                    }
                                    $detailArray = array();
                                    $detailArray = explode(',', $row[$da]);
                                    if (isset($detailArray[1]) && !empty($detailArray[1])) {
                                        $detailSizeId = $this->Size->getSizeIdByNameOnly(trim($detailArray[1]), $storeId);
                                    } else {
                                        $detailSizeId['Size']['id'] = 0;
                                    }

                                    if (!empty($detailSizeId)) {
                                        if (isset($detailArray[0]) && !empty($detailArray[0])) {
                                            $detailItemId = $this->Item->getItemIdByName($storeId, trim($detailArray[0]));
                                        } else {
                                            $detailItemId = array();
                                        }

                                        if (!empty($detailItemId)) {
                                            $detailItemSizeId = $this->ItemPrice->getItemPriceByName($detailItemId['Item']['id'], $detailSizeId['Size']['id'], $storeId);
                                            if (!empty($detailItemSizeId)) {
                                                $offerdetailsData['offerItemID'] = $detailItemId['Item']['id'];
                                                $offerdetailsData['offer_id'] = $offerID;
                                                $offerdetailsData['store_id'] = $storeId;
                                                $offerdetailsData['merchant_id'] = $merchantId;
                                                $offerdetailsData['offerSize'] = $detailSizeId['Size']['id'];
                                                if (isset($detailArray[2]) && !empty($detailArray[2])) {
                                                    $offerdetailsData['discountAmt'] = $detailArray[2];
                                                } else {
                                                    $offerdetailsData['discountAmt'] = 0;
                                                }

                                                $this->OfferDetail->create();
                                                $this->OfferDetail->saveOfferDetail($offerdetailsData);
                                            }
                                        }
                                    }
                                    $da++;
                                }
                            }
                            $i++;
                        }
                    }
                }
            }
        }
        return $i;
    }

    public function download($store_id = null) {
        if (!empty($store_id)) {
            $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
            $this->OfferDetail->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offerItemID', 'fields' => array('name')), 'Size' => array('foreignKey' => 'offerSize', 'fields' => array('size')))));
            $this->Offer->bindModel(array(
                'hasMany' => array(
                    'OfferDetail' => array(
                        'conditions' => array(
                            'OfferDetail.is_deleted' => 0),
                        'fields' => array('offerItemID', 'offerSize', 'discountAmt')
                    )
                ),
                'belongsTo' => array(
                    'Size' => array('fields' => array('size')),
                    'Item' => array('fields' => array('name')), 'Store' => array('className' => 'Store', 'foreignKey' => 'store_id', 'fields' => array('id', 'store_name')),
                )
                    )
            );
            if ($store_id == "All") {
                $merchantId = $this->Session->read('merchantId');
                $result = $this->Offer->fetchOfferListByMerchantId($merchantId);
            } else {
                $storeId = $store_id;
                $result = $this->Offer->fetchOfferList($storeId);
            }
        }
        Configure::write('debug', 2);
        App:: import('Vendor', 'PHPExcel');
        $objPHPExcel = new PHPExcel;
//        $styleArray2 = array(
//            'font' => array('name' => 'Arial', 'size' => '10', 'color' => array('rgb' => '444555'), 'bold' => true),
//            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'D6D6D6'))
//        );
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
        $filename = 'Hq_Promotions_' . date("Y-m-d") . ".xls"; //create a file
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setTitle('Promotions');

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Item Name');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Number of Units');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Size Name');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Description');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Is Fixed Price');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Offer Price');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Offer Start Date');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Offer End Date');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Is Time');
        $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Offer Start Time');
        $objPHPExcel->getActiveSheet()->setCellValue('L1', 'Offer End Time');
        $objPHPExcel->getActiveSheet()->setCellValue('M1', 'Is Active');

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

        $i = 2;
        $k = 1;
        foreach ($result as $data) {

            if (!empty($data['OfferDetail'])) {
                $index = 'N';
                foreach ($data['OfferDetail'] as $detail) {
                    $objPHPExcel->getActiveSheet()->setCellValue("$index$k", 'Offered Item');
                    $objPHPExcel->getActiveSheet()->getStyle("$index$k")->applyFromArray($styleArray);
                    if (!empty($detail['Item']['name'])) {
                        $objPHPExcel->getActiveSheet()->setCellValue("$index$i", $detail['Item']['name'] . ',' . @$detail['Size']['size'] . ',' . $detail['discountAmt']);
                    } else {
                        $objPHPExcel->getActiveSheet()->setCellValue("$index$i", ' ' . ',' . @$detail['Size']['size'] . ',' . $detail['discountAmt']);
                    }


                    $index++;
                }
            }
            $objPHPExcel->getActiveSheet()->setCellValue("A$i", trim($data['Offer']['id']));
            $objPHPExcel->getActiveSheet()->setCellValue("B$i", trim($data['Item']['name']));
            $objPHPExcel->getActiveSheet()->setCellValue("C$i", trim($data['Offer']['unit']));
            $objPHPExcel->getActiveSheet()->setCellValue("D$i", trim($data['Size']['size']));
            $objPHPExcel->getActiveSheet()->setCellValue("E$i", trim($data['Offer']['description']));
            $objPHPExcel->getActiveSheet()->setCellValue("F$i", trim($data['Offer']['is_fixed_price']));
            $objPHPExcel->getActiveSheet()->setCellValue("G$i", trim($data['Offer']['offerprice']));
            if (!empty($data['Offer']['offer_start_date'])) {
                $startDate = date('m-d-Y', strtotime($data['Offer']['offer_start_date']));
            } else {
                $startDate = '';
            }
            $objPHPExcel->getActiveSheet()->setCellValue("H$i", trim($startDate));
            if (!empty($data['Offer']['offer_end_date'])) {
                $endDate = date('m-d-Y', strtotime($data['Offer']['offer_end_date']));
            } else {
                $endDate = '';
            }
            $objPHPExcel->getActiveSheet()->setCellValue("I$i", trim($endDate));
            $objPHPExcel->getActiveSheet()->setCellValue("J$i", trim($data['Offer']['is_time']));
            $objPHPExcel->getActiveSheet()->setCellValue("K$i", trim($data['Offer']['offer_start_time']));
            $objPHPExcel->getActiveSheet()->setCellValue("L$i", trim($data['Offer']['offer_end_time']));
            $objPHPExcel->getActiveSheet()->setCellValue("M$i", trim($data['Offer']['is_active']));

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
            $this->loadModel('Offer');
            $this->Offer->bindModel(
                    array(
                'belongsTo' => array(
                    'Item' => array(
                        'className' => 'Item',
                        'foreignKey' => 'item_id',
                        'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                        'fields' => array('id', 'name'),
                        'type' => 'INNER'
                    )
                )
                    ), false
            );
            if (!empty($_GET['storeId'])) {
                $storeID = $_GET['storeId'];
            } else {
                $merchant_id = $this->Session->read('merchantId');
                $storeID = $this->Store->getAllStoresByMerchantId($merchant_id);
            }
            $searchData = $this->Offer->find('all', array('fields' => array('Item.name', 'Offer.description'), 'conditions' => array('OR' => array('Offer.description LIKE' => '%' . $_GET['term'] . '%', 'Item.name LIKE' => '%' . $_GET['term'] . '%'), 'Offer.is_deleted' => 0, 'Offer.store_id' => $storeID)));
            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    $new_array[] = array('label' => $val['Item']['name'], 'value' => $val['Item']['name'], 'desc' => $val['Item']['name'] . "-" . $val['Offer']['description']);
                };
            }
            echo json_encode($new_array);
        } else {
            exit;
        }
    }

    /* ------------------------------------------------
      Function name:shareOffer()
      Description:Share the coupon to customers
      created:13/06/2017
      ----------------------------------------------------- */

    public function shareOffer($EncryptOfferID = null) {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        if (!empty($_GET['offerId'])) {
            $EncryptOfferID = $_GET['offerId'];
        }
        if ($EncryptOfferID) {
            $offerId = $offer['Offer']['id'] = $this->Encryption->decode($EncryptOfferID);
        } else {
            $offer['Offer']['id'] = $this->request->data['User']['offer_id'];
        }
        $this->Offer->bindModel(
                array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'conditions' => array('Store.is_deleted' => 0, 'Store.is_active' => 1),
                    'fields' => array('store_name')
                )
            )), false
        );
        $offerData = $this->Offer->findById($offer['Offer']['id']);
        if (empty($offerData)) {
            $this->Session->setFlash(__("Something went wrong!."), 'alert_failed');
            $this->redirect($this->referer());
        }
        $storeId = $offerData['Offer']['store_id'];
        if ($this->request->is(array('post', 'put'))) {

            //$this->request->data['User']['id'] = array(333, 332);
            $this->request->data['User']['id'] = array_filter($this->request->data['User']['id']);
            $this->loadModel('Store');
            $storeEmail = $this->Store->fetchStoreDetail($storeId);
            $alreadyShared = 0;
            $newshared = 0;
            foreach ($this->request->data['User']['id'] as $data) {
                $this->loadModel('User');
                $this->User->bindModel(array('belongsTo' => array('CountryCode')));
                $shareuserdetail = $this->User->find('first', array('fields' => array('User.id', 'User.fname', 'User.lname', 'User.email', 'User.phone', 'User.is_emailnotification', 'User.is_smsnotification', 'User.country_code_id', 'CountryCode.code'), 'conditions' => array('User.id' => $data)));
                if (!empty($shareuserdetail)) {
                    $this->Offer->bindModel(
                            array(
                                'belongsTo' => array(
                                    'Size' => array(
                                        'fields' => array('size')),
                                    'Item' => array(
                                        'fields' => array('name'))),
                    ));
                    $this->Offer->bindModel(
                            array(
                        'hasMany' => array(
                            'OfferDetail' => array(
                                'className' => 'OfferDetail',
                                'foreignKey' => 'offer_id',
                                'conditions' => array('OfferDetail.is_deleted' => 0),
                                'fields' => array('OfferDetail.id', 'OfferDetail.offer_id', 'OfferDetail.offerItemID', 'OfferDetail.offerSize', 'OfferDetail.discountAmt')
                            )
                        )
                            ), false
                    );
                    $this->loadModel('Offer');
                    $offerDetails = $this->Offer->getOfferDetails($offer['Offer']['id']);
                    //pr($offerDetails);
                    if (!empty($offerDetails['Offer'])) {
                        if ($offerDetails['Offer']['unit'] > 1) {//units
                            $detail = "Buy " . @$offerDetails['Offer']['unit'] . ' units of ' . @$offerDetails['Size']['size'] . ' ' . @$offerDetails['Item']['name'] . ' and get ';
                        } else {//unit
                            $detail = "Buy " . @$offerDetails['Offer']['unit'] . ' unit of ' . @$offerDetails['Size']['size'] . ' ' . @$offerDetails['Item']['name'] . ' and get ';
                        }
                    }
                    if (!empty($offerDetails['OfferDetail'])) {

                        foreach ($offerDetails['OfferDetail'] as $key => $offerDetail) {
                            $itemDetail = $this->Item->findById($offerDetail['offerItemID'], array('name'));
                            $this->loadModel('Size');
                            $sizeDetail = $this->Size->findById($offerDetail['offerSize'], array('size'));
                            if (!empty($itemDetail)) {
                                $detail.=@$itemDetail['Item']['name'];
                            }
                            if ($offerDetails['Offer']['is_fixed_price'] == 0) {
                                if (!empty($sizeDetail)) {
                                    $detail.='(' . @$sizeDetail['Size']['size'] . ') for ';
                                } else {
                                    $detail.=' for ';
                                }
                                if (!empty($offerDetail['discountAmt']) && $offerDetail['discountAmt'] > 0) {
                                    $detail.='$' . @$offerDetail['discountAmt'] . ' and ';
                                } else {
                                    $detail.='free and ';
                                }
                            } else {
                                if (!empty($sizeDetail)) {
                                    $detail.='(' . @$sizeDetail['Size']['size'] . '), ';
                                } else {
                                    $detail.=', ';
                                }
                            }
                            //pr($itemDetail);
                            //pr($sizeDetail);
                        }
                    }

                    if ($offerDetails['Offer']['is_fixed_price'] == 1) {
                        $detail = rtrim($detail, ', ');
                        $detail.=' for $' . $offerDetails['Offer']['offerprice'] . ' ';
                    }
                    if (!empty($offerDetails['Offer']['offer_start_date']) && !empty($offerDetails['Offer']['offer_end_date'])) {
                        $detail = rtrim($detail, ' and ');
                        $detail.=' starting ' . date("m-d-Y", strtotime($offerDetails['Offer']['offer_start_date'])) . ' to ' . date("m-d-Y", strtotime($offerDetails['Offer']['offer_end_date']));
                    }
                    if (!empty($offerDetails['Offer']) && $offerDetails['Offer']['is_time'] == 1) {
                        $detail = rtrim($detail, ' and ');
                        $detail.=' during the hours of ' . date('h:i a', strtotime($offerDetails['Offer']['offer_start_time'])) . ' to ' . date('h:i a', strtotime($offerDetails['Offer']['offer_end_time']));
                    }
                    $detail.='!';
                    //prx($detail);
                }

                $template_type = 'promotional_offer';
                $this->loadModel('EmailTemplate');
                $emailSuccess = $this->EmailTemplate->storeTemplates($storeId, $merchantId, $template_type);
                if ($emailSuccess) {
                    if ($shareuserdetail['User']['lname']) {
                        $fullName = $shareuserdetail['User']['fname'] . " " . $shareuserdetail['User']['lname'];
                    } else {
                        $fullName = $shareuserdetail['User']['fname'];
                    }
                    $emailData = $emailSuccess['EmailTemplate']['template_message'];
                    $subject = $emailSuccess['EmailTemplate']['template_subject'];
                    if ($shareuserdetail['User']['is_emailnotification'] == 1) {
                        $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                        $emailData = str_replace('{DETAIL_TEXT}', $detail, $emailData);
                        $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);
                        $storeAddress = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
                        $storePhone = $storeEmail['Store']['phone'];
                        $url = "http://" . $storeEmail['Store']['store_url'];
                        $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
                        $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                        $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                        $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);
                        $subject = ucwords(str_replace('_', ' ', $subject));
                        $this->Email->to = $shareuserdetail['User']['email'];
                        $this->Email->subject = $subject;
                        $this->Email->from = $storeEmail['Store']['email_id'];
                        $this->set('data', $emailData);
                        $this->Email->template = 'template';
                        $this->Email->smtpOptions = array(
                            'port' => "$this->smtp_port",
                            'timeout' => '30',
                            'host' => "$this->smtp_host",
                            'username' => "$this->smtp_username",
                            'password' => "$this->smtp_password"
                        );
                        $this->Email->sendAs = 'html';
                        try {
                            $this->Email->send();
                        } catch (Exception $e) {
                            
                        }
                    }
                    if ($shareuserdetail['User']['is_smsnotification'] == 1) {
                        $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                        $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                        $smsData = str_replace('{DETAIL_TEXT}', $detail, $smsData);
                        $smsData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $smsData);
                        $smsData = str_replace('{STORE_PHONE}', $storePhone, $smsData);
                        $message = $smsData;
                        $mob = $shareuserdetail['CountryCode']['code'] . "" . str_replace(array('(', ')', ' ', '-'), '', $shareuserdetail['User']['phone']);
                        $this->Common->sendSmsNotification($mob, $message);
                    }
                }
            }
            $message = "Promotional offer send successfully";
            $this->Session->setFlash(__($message), 'alert_success');
            $this->redirect($this->referer());
        }
        $this->loadModel('User');
        $criteria = array('User.store_id' => $storeId, 'User.role_id' => 4, 'User.is_deleted' => 0, 'User.is_active' => 1);
        $this->paginate = array('fields' => array('User.fname', 'User.lname', 'User.email', 'User.id', 'User.created'), 'conditions' => array($criteria), 'order' => array('User.created' => 'DESC'));
        $list = $this->paginate('User');
        $this->set(compact('list', 'offerData'));
    }

    /* ------------------------------------------------
      Function name:deleteMultipleOffers()
      Description:Delete multiple offers
      created:02/08/2017
      ----------------------------------------------------- */

    public function deleteMultipleOffers() {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        if ($this->request->is(array('post')) && !empty($this->request->data['Offer']['id'])) {
            $filter_array = array_filter($this->request->data['Offer']['id']);
            if ($this->Common->deleteMultipleRecords($filter_array, 'Offer')) {
                $msg = "Offers deleted successfully.";
                $msgType = "alert_success";
            } else {
                $msg = "Some problem occured.";
                $msgType = "alert_failed";
            }
            $this->Session->setFlash(__($msg), $msgType);
            $this->redirect($this->referer());
        }
    }

}
