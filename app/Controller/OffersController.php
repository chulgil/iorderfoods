<?php

App::uses('StoreAppController', 'Controller');

class OffersController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'DateformHelper', 'Common');
    public $uses = array('Offer', 'Item', 'ItemPrice', 'ItemType', 'Size', 'Category', 'OfferDetail');

    public function beforeFilter() {
        parent::beforeFilter();
        //Check permission for Admin User
        $adminfunctions = array('index', 'addOffer', 'editOffer', 'activateOffer', 'deleteOffer', 'deleteOfferPhoto');
        if (in_array($this->params['action'], $adminfunctions)) {
            if (!$this->Common->checkPermissionByaction($this->params['controller'])) {
                $this->Session->setFlash(__("Permission Denied"));
                $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:index()
      Description:List Menu Items
      created:5/8/2015
      ----------------------------------------------------- */

    private function _offerListing($clearAction = null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $storeID = $this->Session->read('admin_store_id');
//        $itemlist = $this->Item->getAllItems($storeID);
//        $this->set('itemList', $itemlist);
        $value = "";
        $criteria = "Offer.store_id =$storeID AND Offer.is_deleted=0";
        //if(isset($this->params['named']['sort']) || isset($this->params['named']['page'])){
        if ($this->Session->read('OfferSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('OfferSearchData'), true);
        } else {
            $this->Session->delete('OfferSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('OfferSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['Offer']['keyword'])) {
                $value = trim($this->request->data['Offer']['keyword']);
                $criteria .= " AND (Offer.description LIKE '%" . $value . "%' OR Item.name LIKE '%" . $value . "%')";
            }
            //if(!empty($this->request->data['Item']['category_id'])){
            //    $categoryID = trim($this->request->data['Item']['category_id']);
            //    $criteria .= " AND (Category.id =$categoryID)";
            //}
            if (isset($this->request->data['Offer']['isActive']) && $this->request->data['Offer']['isActive'] != '') {
                $active = trim($this->request->data['Offer']['isActive']);
                $criteria .= " AND (Offer.is_active =$active)";
            }
            if (isset($this->request->data['Items']['ids']) && $this->request->data['Items']['ids'] != '') {
                $item = trim($this->request->data['Items']['ids']);
                $criteria .= " AND (Offer.item_id =$item)";
            }
        }



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
        $this->Offer->bindModel(
                array(
            'belongsTo' => array(
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id',
                    'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                    'fields' => array('id', 'name', 'category_id'),
                    'type' => 'INNER'
                )
            )
                ), false
        );
        $this->paginate = array('recursive' => 3, 'conditions' => array($criteria), 'order' => array('Offer.created' => 'DESC'));
        $itemdetail = $this->paginate('Offer');
        if (!empty($itemdetail)) {
            foreach ($itemdetail as $key => $offerData) {
                $this->loadModel('OrderOffer');
                $totalFreeUnits = $this->OrderOffer->find('all', array('fields' => array('sum(OrderOffer.quantity) as total_sum'), 'conditions' => array('OrderOffer.offer_id' => $offerData['Offer']['id'], 'OrderOffer.is_active' => 1, 'OrderOffer.is_deleted' => 0)));
                $itemdetail[$key]['Offer']['offer_used_count'] = $totalFreeUnits[0][0]['total_sum'];
            }
        }
        $this->set('list', $itemdetail);
//        $this->loadModel('Category');
//        $categoryList = $this->Category->getCategoryList($storeID);
//        $this->set('categoryList', $categoryList);
        $this->set('keyword', $value);
    }

    public function offerUsedList($EncryptOfferID = null) {
        $this->layout = "admin_dashboard";
        $offer_id = $this->Encryption->decode($EncryptOfferID);
        $storeId = $this->Session->read('admin_store_id');
        $this->loadModel('OrderOffer');
        $this->OrderOffer->bindModel(
                array('belongsTo' => array(
                        'Offer' => array(
                            'className' => 'Offer',
                            'foreignKey' => 'offer_id',
                            'fields' => array('description', 'item_id'),
                        ),
                        'Order' => array(
                            'className' => 'Order',
                            'foreignKey' => 'order_id',
                        )
        )));
        $this->loadModel('Order');
        $this->Order->bindModel(
                array('belongsTo' => array(
                        'User' => array(
                            'className' => 'User',
                            'foreignKey' => 'user_id',
                            'fields' => array('userName', 'email'),
                        ),
                        'DeliveryAddress' => array(
                            'className' => 'DeliveryAddress',
                            'foreignKey' => 'delivery_address_id',
                        )
        )));
        $this->Offer->bindModel(
                array('belongsTo' => array(
                        'Item' => array(
                            'className' => 'Item',
                            'foreignKey' => 'item_id',
                            'fields' => array('Item.name'),
                        )
        )));
        //latest//$this->paginate = array('recursive' => 2, 'fields' => array('sum(OrderOffer.quantity) as total_sum', 'Order.user_id', 'Offer.description', 'Offer.item_id'), 'conditions' => array('OrderOffer.store_id' => $storeId, 'OrderOffer.offer_id' => $offer_id, 'OrderOffer.is_active' => 1, 'OrderOffer.is_deleted' => 0), 'group' => array('Order.user_id'));
        //$this->paginate = array('recursive' => 2, 'fields' => array('Item.name', 'Order.user_id', 'Offer.description'), 'conditions' => array('OrderOffer.store_id' => $storeId, 'OrderOffer.offer_id' => $offer_id, 'OrderOffer.is_active' => 1, 'OrderOffer.is_deleted' => 0));

        $totalOfferUsedLists = $this->OrderOffer->find('all', array('recursive' => 2, 'fields' => array('OrderOffer.quantity', 'Order.user_id', 'Order.delivery_address_id', 'Offer.description', 'Offer.item_id'), 'conditions' => array('OrderOffer.store_id' => $storeId, 'OrderOffer.offer_id' => $offer_id, 'OrderOffer.is_active' => 1, 'OrderOffer.is_deleted' => 0), 'order' => array('Order.created' => 'DESC')));
        //$totalOfferUsedList = $this->paginate('OrderOffer');
        //prx($totalOfferUsedLists);
        $guestEmail = $totalOfferUsedList = array();
        if (!empty($totalOfferUsedLists)) {
            foreach ($totalOfferUsedLists as $key => $list) {
                if (!empty($list)) {
                    if ($list['Order']['user_id'] == 0) {
                        $index = $list['Order']['DeliveryAddress']['email'];
                        if (in_array($index, $guestEmail)) {
                            $totalOfferUsedList[$index]['count'] = $totalOfferUsedList[$index]['count'] + $list['OrderOffer']['quantity'];
                        } else {
                            $totalOfferUsedList[$index]['count'] = $list['OrderOffer']['quantity'];
                            $guestEmail[] = $index;
                        }
                        $totalOfferUsedList[$index]['description'] = $list['Offer']['description'];
                        $totalOfferUsedList[$index]['item_name'] = $list['Offer']['Item']['name'];
                        $totalOfferUsedList[$index]['name'] = $list['Order']['DeliveryAddress']['name_on_bell'];
                        $totalOfferUsedList[$index]['email'] = $list['Order']['DeliveryAddress']['email'];
                    } else {
                        $index = $list['Order']['User']['email'];
                        if (in_array($index, $guestEmail)) {
                            $totalOfferUsedList[$index]['count'] = $totalOfferUsedList[$index]['count'] + $list['OrderOffer']['quantity'];
                        } else {
                            $totalOfferUsedList[$index]['count'] = $list['OrderOffer']['quantity'];
                            $guestEmail[] = $index;
                        }
                        $totalOfferUsedList[$index]['description'] = $list['Offer']['description'];
                        $totalOfferUsedList[$index]['item_name'] = $list['Offer']['Item']['name'];
                        $totalOfferUsedList[$index]['name'] = $list['Order']['User']['userName'];
                        $totalOfferUsedList[$index]['email'] = $list['Order']['User']['email'];
                    }
                }
            }
        }
        $this->set('list', $totalOfferUsedList);
    }

    /* ------------------------------------------------
      Function name:addOffer()
      Description:add Offer
      created:5/8/2015
      ----------------------------------------------------- */

    public function addOffer() {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');
        $start = "00:00";
        $end = "24:00";
        $timeRange = $this->Common->getStoreTimeAdmin($start, $end);
        $this->set('timeOptions', $timeRange);
        if ($this->request->is('post') && !empty($this->data['Item']['id'])) {
            $this->data = $this->Common->trimValue($this->data);
            $response = $this->Common->uploadMenuItemImages($this->data['Offer']['imgcat'], '/Offer-Image/', $storeId, 480, 320);
            if (!$response['status']) {
                $this->Session->setFlash(__($response['errmsg']));
            } else {

                $offerData['offer_start_date'] = '';
                $offerData['offer_end_date'] = '';
                if ($this->data['Offer']['offer_start_date'] && $this->data['Offer']['offer_end_date']) {
                    $offerData['offer_start_date'] = $this->Dateform->formatDate($this->data['Offer']['offer_start_date']);
                    $offerData['offer_end_date'] = $this->Dateform->formatDate($this->data['Offer']['offer_end_date']);
                }
                $sizeId = 0;
                if (!empty($this->data['Size']['id'])) {
                    $sizeId = $this->data['Size']['id'];
                }
                if (!$this->Offer->offerExistsOnItem($this->data['Item']['id'], $offerData['offer_start_date'], $offerData['offer_end_date'], $sizeId, $this->data['Offer']['unit'])) {
                    //Offer Data
                    if ($this->data['Offer']['is_time'] == 0) {
                        $this->request->data['Offer']['offer_start_time'] = "";
                        $this->request->data['Offer']['offer_end_time'] = "";
                    }
                    $offerData['offerImage'] = $response['imagename'];
                    $offerData['store_id'] = $storeId;
                    $offerData['merchant_id'] = $merchant_id;
                    $offerData['item_id'] = $this->data['Item']['id'];
                    $offerData['unit'] = $this->data['Offer']['unit'];
                    $offerData['offer_end_time'] = $this->data['Offer']['offer_end_time'];
                    $offerData['offer_start_time'] = $this->data['Offer']['offer_start_time'];
                    $offerData['is_time'] = $this->data['Offer']['is_time'];
                    $offerData['description'] = trim($this->data['Offer']['description']);
                    if (!empty($this->data['Size']['id']) && isset($this->data['Size']['id'])) {
                        $offerData['size_id'] = $this->data['Size']['id'];
                    }
                    $offerData['is_fixed_price'] = $this->data['Offer']['is_fixed_price'];

                    $offerData['offerprice'] = ($this->data['Offer']['offerprice']) ? $this->data['Offer']['offerprice'] : 0;
                    $offerData['is_active'] = ($this->data['Offer']['is_active'] == 1) ? 1 : 0;
                    $this->Offer->saveOffer($offerData);
                    $offerID = $this->Offer->getLastInsertId();
                    if ($offerID) {
                        if (isset($this->data['OfferDetails']) && $this->data['OfferDetails']) {
                            foreach ($this->data['OfferDetails'] as $key => $offerdetails) {
                                $offerdetailsData['offerItemID'] = $offerdetails['item_id'];
                                $offerdetailsData['offer_id'] = $offerID;
                                $offerdetailsData['store_id'] = $storeId;
                                $offerdetailsData['merchant_id'] = $merchant_id;
                                $priceArray = explode(',', $offerdetails['discountAmt']);
                                if (!$priceArray[0]) {
                                    $priceArray[0] = 0;
                                }

                                if (isset($offerdetails['offerSize']) && $offerdetails['offerSize']) {
                                    //$i=0;
                                    $offerdetailsData['offerSize'] = $offerdetails['offerSize'];
                                    $offerdetailsData['discountAmt'] = $priceArray[0];
                                    $this->OfferDetail->create();
                                    $this->OfferDetail->saveOfferDetail($offerdetailsData);
                                } else {
                                    $offerdetailsData['offerSize'] = 0;
                                    $offerdetailsData['discountAmt'] = $priceArray[0];
                                    $this->OfferDetail->create();
                                    $this->OfferDetail->saveOfferDetail($offerdetailsData);
                                }
                            }
                        }
                        $this->request->data = '';
                        $this->Session->setFlash(__("Offer Successfully Created"), 'alert_success');
                        //$this->redirect(array('controller' => 'Offers', 'action' => 'index'));
                    } else {
                        $this->Session->setFlash(__("Offer Not Created"), 'alert_failed');
                    }
                } else {
                    $this->Session->setFlash(__("Offer on Item already exists"), 'alert_failed');
                }
            }
        }
        $sizepost = 0;
        $sizeList = "";
        $isfixed = 0;
        if (isset($this->data['Item']['id'])) {
            $category = $this->Item->getcategoryByitemID($this->data['Item']['id'], $storeId);
            //$sizeList=$this->Size->getCategorySizes($category['Item']['category_id'],$storeId);
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
                $sizeList[$value['ItemPrice']['size_id']] = $value['Size']['size'];
            }
            $sizepost = 1;
        }
        if (isset($this->data['Offer']['is_fixed_price']) && $this->data['Offer']['is_fixed_price']) {
            $isfixed = 1;
        }
//        $this->loadModel('Category');
//        $this->Item->bindModel(array(
//            'belongsTo' => array('Category' => array(
//                    'className' => 'Category',
//                    'foreignKey' => 'category_id',
//                    'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
//                    'type' => "INNER"
//                ))
//        ));
//        $itemList = $this->Item->find('list', array(
//            'fields' => array('Item.id', 'Item.name'),
//            'conditions' => array('Item.store_id' => $storeId, 'Item.is_deleted' => 0, 'Item.is_active' => 1),
//            'recursive' => 1
//        ));

        $this->Offer->bindModel(
                array(
            'belongsTo' => array(
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id',
                    'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                    'fields' => array('id', 'name', 'category_id'),
                    'type' => 'INNER'
                )
            )
                ), false
        );
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
        $itmList = $this->Offer->find('all', array(
            'fields' => array('Offer.id', 'Offer.item_id', 'Item.id', 'Item.name', 'Item.category_id'),
            'conditions' => array('Item.store_id' => $storeId, 'Item.is_deleted' => 0, 'Item.is_active' => 1, 'Offer.is_deleted' => 0),
            'recursive' => 2
        ));
        //prx($itemList);
        $nList = array();
        if (!empty($itmList)) {
            foreach ($itmList as $iList) {
                if (!empty($iList['Item']) && !empty($iList['Item']['Category'])) {
                    $nList[$iList['Item']['id']] = $iList['Item']['name'];
                }
            }
        }
        $this->set('nList', $nList);
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
        $itemList = $this->Item->find('list', array(
            'fields' => array('Item.id', 'Item.name'),
            'conditions' => array('Item.store_id' => $storeId, 'Item.is_deleted' => 0, 'Item.is_active' => 1, 'Category.is_deleted' => 0, 'Category.is_active' => 1),
            'recursive' => 1
        ));
        $this->set('itemList', $itemList);
        $this->set('isfixed', $isfixed);
        $this->set('sizepost', $sizepost);
        $this->set('sizeList', $sizeList);
        $this->_offerListing();
        $this->loadModel('StoreDeals');
        $storeDealData = $this->StoreDeals->findByStoreId($storeId);
        $this->set('storeDealData', $storeDealData);
    }

    /* ------------------------------------------------
      Function name:editMenuItem()
      Description:Update Menu Item created:5/8/2015
      ----------------------------------------------------- */

    public function editOffer($EncryptedofferID = null) {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchant_id = $this->Session->read('admin_merchant_id');
        $start = "00:00";
        $end = "24:00";
        $timeRange = $this->Common->getStoreTimeAdmin($start, $end);
        $this->set('timeOptions', $timeRange);
        $data['Offer']['id'] = $this->Encryption->decode($EncryptedofferID);
        if ($this->data) {
            $this->data = $this->Common->trimValue($this->data);
            $offerData['offer_start_date'] = '';
            $offerData['offer_end_date'] = '';
            if ($this->data['Offer']['offer_start_date'] && $this->data['Offer']['offer_end_date']) {
                $offerData['offer_start_date'] = $this->Dateform->formatDate($this->data['Offer']['offer_start_date']);
                $offerData['offer_end_date'] = $this->Dateform->formatDate($this->data['Offer']['offer_end_date']);
            }
            $sizeId = 0;
            if (!empty($this->data['Size']['id'])) {
                $sizeId = $this->data['Size']['id'];
            }
            if (!$this->Offer->offerExistsOnItem($this->data['Item']['id'], $offerData['offer_start_date'], $offerData['offer_end_date'], $sizeId, $this->data['Offer']['unit'], $this->data['Offer']['id'])) {
                if ($this->data['Offer']['is_time'] == 0) {
                    $this->request->data['Offer']['offer_start_time'] = "";
                    $this->request->data['Offer']['offer_end_time'] = "";
                }
                $offerData['id'] = $this->data['Offer']['id'];
                $offerData['store_id'] = $storeId;
                $offerData['offer_start_time'] = $this->data['Offer']['offer_start_time'];
                $offerData['offer_end_time'] = $this->data['Offer']['offer_end_time'];
                $offerData['is_time'] = $this->data['Offer']['is_time'];
                $offerData['merchant_id'] = $merchant_id;
                $offerData['item_id'] = $this->data['Item']['id'];
                $offerData['description'] = trim($this->data['Offer']['description']);
                if (isset($this->data['Size']['id'])) {
                    $offerData['size_id'] = $this->data['Size']['id'];
                }
                $offerData['unit'] = $this->data['Offer']['unit'];
                $offerData['is_fixed_price'] = $this->data['Offer']['is_fixed_price'];
                $offerData['offerprice'] = ($this->data['Offer']['offerprice']) ? $this->data['Offer']['offerprice'] : 0;
                $offerData['is_active'] = ($this->data['Offer']['is_active'] == 1) ? 1 : 0;
                if ($this->data['Offer']['imgcat']['error'] == 0) {
                    $response = $this->Common->uploadMenuItemImages($this->data['Offer']['imgcat'], '/Offer-Image/', $storeId, 480, 320);
                } elseif ($this->data['Offer']['imgcat']['error'] == 4) {
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
                    if ($this->OfferDetail->deleteallOfferItems($this->data['Offer']['id'])) {
                        if (isset($this->data['OfferDetails']) && $this->data['OfferDetails']) {
                            foreach ($this->data['OfferDetails'] as $key => $details) {
                                if (isset($details['id'])) {
                                    $offerdetails['id'] = $details['id'];
                                } else {
                                    $offerdetails['id'] = '';
                                }
                                $offerdetails['offer_id'] = $this->data['Offer']['id'];
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
                                $offerdetails['store_id'] = $storeId;
                                $offerdetails['merchant_id'] = $merchant_id;
                                $this->OfferDetail->saveOfferDetail($offerdetails);
                            }
                        }
                        $this->Session->setFlash(__("Offer Updated"), 'alert_success');
                        $this->redirect(array('controller' => 'offers', 'action' => 'addOffer'));
                    } else {
                        $this->Session->setFlash(__("Some Problem occured"), 'alert_failed');
                    }
                }
            } else {
                $this->Session->setFlash(__("Offer on Item already exists"), 'alert_failed');
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
        //pr($offerDetails);
        $FinalOfferDetails['Offered'] = array();
        foreach ($offerDetails as $key => $Offer) {
            if ($key == "Offer") {
                $FinalOfferDetails['Item']['id'] = $Offer['item_id'];
                $FinalOfferDetails['Size']['id'] = $Offer['size_id'];
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
                    $i = 0;
                    $price = 0;
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
        $this->request->data = $FinalOfferDetails; //pr($this->request->data);die;
        $sizepost = 0;
        $sizeList = "";
        $isfixed = 0;
        $istimeRestriction = 0;
        if (isset($this->request->data['Item']['id'])) {
            $category = $this->Item->getcategoryByitemID($this->request->data['Item']['id'], $storeId);

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
            // pr($sizeListarray);die;
            foreach ($sizeListarray as $key => $value) {
                if (isset($value['Size']['size'])) {
                    $sizeList[$value['ItemPrice']['size_id']] = $value['Size']['size'];
                }
            }
            // $sizeList=$this->Size->getCategorySizes($category['Item']['category_id'],$storeId);
            $sizepost = 1;
        }
        if (isset($this->request->data['Offer']['is_fixed_price']) && $this->request->data['Offer']['is_fixed_price']) {
            $isfixed = 1;
        }
        if (isset($this->request->data['Offer']['is_time']) && $this->request->data['Offer']['is_time']) {
            $istimeRestriction = 1;
        }
        $this->loadModel('Category');
        $this->Item->bindModel(array(
            'belongsTo' => array('Category' => array(
                    'className' => 'Category',
                    'foreignKey' => 'category_id',
                    'conditions' => array('Category.is_deleted' => 0, 'Category.is_active' => 1),
                    'type' => "INNER"
                ))
        ));
        $itemList = $this->Item->find('list', array(
            'fields' => array('Item.id', 'Item.name'),
            'conditions' => array('Item.store_id' => $storeId, 'Item.is_deleted' => 0, 'Item.is_active' => 1, 'Category.is_deleted' => 0, 'Category.is_active' => 1),
            'recursive' => 1
        ));
        $this->set('istimeRestriction', $istimeRestriction);
        $this->set('itemList', $itemList);
        $this->set('isfixed', $isfixed);
        $this->set('sizepost', $sizepost);
        $this->set('sizeList', $sizeList);
    }

    /* ------------------------------------------------
      Function name:activateOffer()
      Description:Active/deactive Offer
      created:5/8/2015
      ----------------------------------------------------- */

    public function activateOffer($EncryptOfferID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Offer']['store_id'] = $this->Session->read('admin_store_id');
        $data['Offer']['id'] = $this->Encryption->decode($EncryptOfferID);
        $data['Offer']['is_active'] = $status;
        if ($this->Offer->saveOffer($data)) {
            if ($status) {
                $SuccessMsg = "Offer Activated";
                $this->Session->setFlash(__($SuccessMsg), 'alert_success');
                $this->redirect(array('controller' => 'Offers', 'action' => 'editOffer/' . $EncryptOfferID . '#OfferOfferStartDate'));
            } else {
                $SuccessMsg = "Offer Deactivated and Offer will not get Display";
                $this->Session->setFlash(__($SuccessMsg), 'alert_success');
                $this->redirect(array('controller' => 'Offers', 'action' => 'addOffer'));
            }
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'Offers', 'action' => 'addOffer'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteOffer()
      Description:Delete Offer
      created:5/8/2015
      ----------------------------------------------------- */

    public function deleteOffer($EncryptOfferID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Offer']['store_id'] = $this->Session->read('admin_store_id');
        $data['Offer']['id'] = $this->Encryption->decode($EncryptOfferID);
        $data['Offer']['is_deleted'] = 1;
        if ($this->Offer->saveOffer($data)) {
            $this->Session->setFlash(__("Offer deleted"), 'alert_success');
            $this->redirect(array('controller' => 'Offers', 'action' => 'addOffer'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'Offers', 'action' => 'addOffer'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteOfferPhoto()
      Description:Delete Offer Photo
      created:5/8/2015
      ----------------------------------------------------- */

    public function deleteOfferPhoto($EncryptOfferID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['Offer']['store_id'] = $this->Session->read('admin_store_id');
        $data['Offer']['id'] = $this->Encryption->decode($EncryptOfferID);
        $data['Offer']['offerImage'] = '';
        if ($this->Offer->saveOffer($data)) {
            $this->Session->setFlash(__("Offer Photo deleted"), 'alert_success');
            $this->redirect(array('controller' => 'Offers', 'action' => 'editOffer', $EncryptOfferID));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'Offers', 'action' => 'editOffer', $EncryptOfferID));
        }
    }

    public function uploadfile() {
        $this->layout = 'admin_dashboard';
        if (!empty($this->request->data)) {
            $tmp = $this->request->data;
            $this->loadModel('Store');
            $this->loadModel('Size');
            $this->loadModel('Item');
            $this->loadModel('ItemPrice');
            if ($tmp['Offer']['file']['error'] == 4) {
                $this->Session->setFlash(__('Your file contains error. Please retry uploading.'), 'alert_failed');
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
                $i = 0;

                foreach ($real_data as $key => $row) {
                    $row = $this->Common->trimValue($row);
                    if ($key > 0) {
                        if (!empty($row['B']) && !empty($row['C']) && !empty($row['E'])) {
                            $storeId = $this->Session->read('admin_store_id');
                            $merchantId = $this->Session->read('admin_merchant_id');
                            if (!empty($storeId)) {
                                $itemId = $this->Item->getItemIdByName($storeId, trim($row['B']));
                                if (!empty($itemId)) {
                                    if (!empty($row['D'])) {
                                        $sizeId = $this->Size->getSizeIdByNameOnly(trim($row['D']), $storeId);
                                        if ($sizeId) {
                                            $itemsizeId = $this->ItemPrice->getItemPriceByName($itemId['Item']['id'], $sizeId['Size']['id'], $storeId);
                                            $itemsizeId['Size']['id'] = $sizeId['Size']['id'];
                                        } else {
                                            $itemsizeId['Size']['id'] = 0;
                                        }
                                    } else {
                                        $itemsizeId['Size']['id'] = 0;
                                    }

                                    if ($itemsizeId) {

                                        $offerData['store_id'] = $storeId;
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
                }
                $this->Session->setFlash(__($i . ' ' . 'Promotions has been saved'), 'alert_success');
                $this->redirect(array("controller" => "offers", "action" => "index"));
            }
        }
    }

    public function download() {
        $storeId = $this->Session->read('admin_store_id');
        $this->OfferDetail->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offerItemID', 'fields' => array('name')), 'Size' => array('foreignKey' => 'offerSize', 'fields' => array('size')))));
        $this->Offer->bindModel(array('belongsTo' => array('Size' => array('fields' => array('size')), 'Item' => array('fields' => array('name'))), 'hasMany' => array('OfferDetail' => array('conditions' => array('OfferDetail.is_deleted' => 0), 'fields' => array('offerItemID', 'offerSize', 'discountAmt')))));
        $result = $this->Offer->fetchOfferList($storeId);
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
        $filename = 'Promotions_' . date("Y-m-d") . ".xls"; //create a file
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

        // $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
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
                    $objPHPExcel->getActiveSheet()->setCellValue("$index$i", $detail['Item']['name'] . ',' . @$detail['Size']['size'] . ',' . $detail['discountAmt']);
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
            $storeID = $this->Session->read('admin_store_id');
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
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        if (!empty($_GET['offerId'])) {
            $EncryptOfferID = $_GET['offerId'];
        }
        if ($EncryptOfferID) {
            $offerId = $offer['Offer']['id'] = $this->Encryption->decode($EncryptOfferID);
        } else {
            $offer['Offer']['id'] = $this->request->data['User']['offer_id'];
        }
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
                            $maindetail = "Buy " . @$offerDetails['Offer']['unit'] . ' units of ' . @$offerDetails['Size']['size'] . ' ' . @$offerDetails['Item']['name'];
                        } else {//unit
                            $maindetail = "Buy " . @$offerDetails['Offer']['unit'] . ' unit of ' . @$offerDetails['Size']['size'] . ' ' . @$offerDetails['Item']['name'];
                        }
                    }
                    $freeItem = '';
                    $finaldetail = '';
                    $offerItemsFlag = false;
                    if (!empty($offerDetails['OfferDetail'])) {
                        foreach ($offerDetails['OfferDetail'] as $key => $offerDetail) {
                            $itemDetail = $this->Item->findById($offerDetail['offerItemID'], array('name'));
                            $this->loadModel('Size');
                            $sizeDetail = $this->Size->findById($offerDetail['offerSize'], array('size'));
                            $freeflag = false;
                            $detail = '';
                            if (!empty($itemDetail)) {
                                $detail.=@$itemDetail['Item']['name'];
                            }
                            if ($offerDetails['Offer']['is_fixed_price'] == 0) {
                                if (!empty($sizeDetail)) {
                                    $detail.='(' . @$sizeDetail['Size']['size'] . ') ';
                                } else {
                                    //$detail.=' for ';
                                    $detail.=' ';
                                }
                                if (!empty($offerDetail['discountAmt']) && $offerDetail['discountAmt'] > 0) {
                                    $detail.='for $' . @$offerDetail['discountAmt'] . ' and ';
                                } else {
                                    $freeflag = true;

                                    $detail.='and ';
                                }
                            } else {
                                if (!empty($sizeDetail)) {
                                    $detail.='(' . @$sizeDetail['Size']['size'] . '), ';
                                } else {
                                    $detail.=', ';
                                }
                            }
                            if ($freeflag) {
                                $freeItem.=$detail;
                            } else {
                                $finaldetail.=$detail;
                            }
                        }
                        $finaldetail = rtrim($finaldetail, ' and ');
                        $freeItem = rtrim($freeItem, ' and ');
                        $offerItemsFlag = true;
                    }

                    $offerdet = '';
                    if ($offerItemsFlag) {
                        $offerdet = " and get ";
                    }

                    if ($freeItem) {
                        $finaldetail = $maindetail . $offerdet . $finaldetail . ' and ' . $freeItem . ' for free, ';
                    } else {
                        $finaldetail = $maindetail . $offerdet . $finaldetail;
                    }

                    if ($offerDetails['Offer']['is_fixed_price'] == 1) {
                        $finaldetail = rtrim($finaldetail, ', ');
                        $finaldetail.=' for $' . $offerDetails['Offer']['offerprice'] . ' ';
                    }
                    if (!empty($offerDetails['Offer']['offer_start_date']) && !empty($offerDetails['Offer']['offer_end_date'])) {
                        $finaldetail = rtrim($finaldetail, ' and ');
                        $finaldetail.=' starting ' . date("m-d-Y", strtotime($offerDetails['Offer']['offer_start_date'])) . ' to ' . date("m-d-Y", strtotime($offerDetails['Offer']['offer_end_date']));
                    }
                    if (!empty($offerDetails['Offer']) && $offerDetails['Offer']['is_time'] == 1) {
                        $finaldetail = rtrim($finaldetail, ' and ');
                        $finaldetail.=' during the hours of ' . date('h:i a', strtotime($offerDetails['Offer']['offer_start_time'])) . ' to ' . date('h:i a', strtotime($offerDetails['Offer']['offer_end_time']));
                    }
                    $finaldetail.=' !';
                }
                //prx($finaldetail);
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
                        $emailData = str_replace('{DETAIL_TEXT}', $finaldetail, $emailData);
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
                        $smsData = str_replace('{DETAIL_TEXT}', $finaldetail, $smsData);
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
        $criteria = array('User.merchant_id' => $merchantId, 'User.role_id' => array(4, 5), 'User.is_deleted' => 0, 'User.is_active' => 1);
        $this->paginate = array('fields' => array('User.fname', 'User.lname', 'User.email', 'User.id', 'User.created'), 'conditions' => array($criteria), 'order' => array('User.created' => 'DESC'));
        $list = $this->paginate('User');
        $this->set(compact('list', 'offerId'));
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
    
    public function offerUsedDetail($EncryptOrderId = null) {
        $this->layout = "admin_dashboard";
        $order_id = $this->Encryption->decode($EncryptOrderId);
        $storeId = $this->Session->read('admin_store_id');
        $this->loadModel('OrderOffer');
        $this->loadModel('Order');
        $this->loadModel('Offer');
        $this->OrderOffer->bindModel(
                array('belongsTo' => array(
                        'Offer' => array(
                            'className' => 'Offer',
                            'foreignKey' => 'offer_id',
                            'fields' => array('id', 'description', 'item_id', 'is_fixed_price'),
                        ),
                        'Order' => array(
                            'className' => 'Order',
                            'foreignKey' => 'order_id',
                        ),
                        'OrderOfferedItem' => array(
                            'className' => 'Item',
                            'foreignKey' => 'offered_item_id',
                            //'fields' => array('id', 'name'),
                        )
        )));
        $this->Order->bindModel(
                array('belongsTo' => array(
                        'User' => array(
                            'className' => 'User',
                            'foreignKey' => 'user_id',
                            'fields' => array('userName', 'email'),
                        ),
                        'DeliveryAddress' => array(
                            'className' => 'DeliveryAddress',
                            'foreignKey' => 'delivery_address_id',
                        )
        )));
        $this->Offer->bindModel(
                array('belongsTo' => array(
                        'Item' => array(
                            'className' => 'Item',
                            'foreignKey' => 'item_id',
                            'fields' => array('Item.id', 'Item.name'),
                        )
        ), 'hasMany' => array(
            'OfferDetail' => array(
                            'fields' => array('OfferDetail.discountAmt', 'OfferDetail.offerItemID'),
                            'className' => 'OfferDetail',
                            'foreignKey' => 'offer_id',
                            'bindingKey' => 'id'
                        )
        )));
        //echo $order_id;
        $totalOfferUsedLists = $this->OrderOffer->find('all', array('recursive' => 3, 'fields' => array('OrderOffer.quantity', 'Order.user_id', 'Order.delivery_address_id', 'Offer.id', 'Offer.description', 'Offer.is_fixed_price', 'Offer.offerprice', 'Offer.item_id', 'OrderOffer.offered_item_id'), 'conditions' => array('OrderOffer.store_id' => $storeId, 'OrderOffer.order_id' => $order_id, 'OrderOffer.is_active' => 1, 'OrderOffer.is_deleted' => 0), 'order' => array('Order.created' => 'DESC')));
        //pr($totalOfferUsedLists);
        $guestEmail = $totalOfferUsedList = $offerNewArray = array();
        if (!empty($totalOfferUsedLists)) {
            $index = 0;
            foreach ($totalOfferUsedLists as $key => $list) {
                if (!empty($list)) {
                    if(!in_array($list['Offer']['id'], $offerNewArray))
                    {
                        $offeredItemArray = $this->orderOfferItemNames($order_id, $list['Offer']['id']);
                        $offeredItemNames = '';
                        foreach ($offeredItemArray as $offeredItem)
                        {
                            $offeredItemNames .= $offeredItem['Item']['name'] . ', ';
                        }
                        $offeredItemNames = trim($offeredItemNames, ', ');
                        
                        $offerNewArray[] = $list['Offer']['id'];
                        $totalOfferUsedList[$index]['offer_id'] = $list['Offer']['id'];
                        $totalOfferUsedList[$index]['order_offer_item_id'] = $list['OrderOffer']['offered_item_id'];          
                        $totalOfferUsedList[$index]['is_fixed_price'] = $list['Offer']['is_fixed_price'];
                        $totalOfferUsedList[$index]['offerprice'] = $list['Offer']['offerprice'];
                        $totalOfferUsedList[$index]['offered_item_name'] = $offeredItemNames;
                        if ($list['Order']['user_id'] == 0) {
                            $totalOfferUsedList[$index]['description'] = $list['Offer']['description'];
                            $totalOfferUsedList[$index]['item_name'] = $list['Offer']['Item']['name'];
                            $totalOfferUsedList[$index]['offer_item'] = $list['Offer']['OfferDetail'];
                            $totalOfferUsedList[$index]['name'] = $list['Order']['DeliveryAddress']['name_on_bell'];
                            $totalOfferUsedList[$index]['email'] = $list['Order']['DeliveryAddress']['email'];

                        } else {
                            $totalOfferUsedList[$index]['description'] = $list['Offer']['description'];
                            $totalOfferUsedList[$index]['item_name'] = $list['Offer']['Item']['name'];
                            $totalOfferUsedList[$index]['offer_item'] = $list['Offer']['OfferDetail'];
                            $totalOfferUsedList[$index]['name'] = $list['Order']['User']['userName'];
                            $totalOfferUsedList[$index]['email'] = $list['Order']['User']['email'];
                        }
                    }
                }
                $index++;
            }
        }
        
        $this->set('list', $totalOfferUsedList);
    }
    
    function orderOfferItemNames($orderId = null, $offerId = null)
    {
        $this->OrderOffer->bindModel(
                array('belongsTo' => array(
                        'Item' => array(
                            'className' => 'Item',
                            'foreignKey' => 'offered_item_id',
                            //'fields' => array('id', 'name'),
                        )
                    )
                )
            );
        $list = $this->OrderOffer->find('all', array('fields' => array('OrderOffer.id', 'OrderOffer.offered_item_id', 'Item.name'), 'conditions' => array('OrderOffer.order_id' => $orderId, 'OrderOffer.offer_id' => $offerId)));
        return $list;
    }
}
