<?php

App::uses('StoreAppController', 'Controller');

class ItemOffersController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'Common');
    public $uses = array('Item', 'ItemPrice', 'Store', 'ItemOffer', 'Category');

    public function beforeFilter() {
        parent::beforeFilter();
        $adminfunctions = array('List', 'activate', 'delete', 'add', 'edit', 'deleteMultiple');
//        if(in_array($this->params['action'],$adminfunctions)){
//           if(!$this->Common->checkPermissionByaction($this->params['controller'])){
//             $this->Session->setFlash(__("Permission Denied"));
//             $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
//           }
//        }
    }

    /* ------------------------------------------------
      Function name:addCoupon()
      Description:Add New Coupon
      created:8/8/2015
      ----------------------------------------------------- */

    public function add() {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        if (!empty($this->request->data)) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            if (!empty($this->request->data['ItemOffer']['category_id'])) {
                $isUniqueOffer = $this->ItemOffer->checkUniqueOffer($this->request->data['ItemOffer']['item_id'], $storeId);
                if ($isUniqueOffer) {
                    $offerData = array();
                    $offerData['ItemOffer']['id'] = "";
                    $offerData['ItemOffer']['item_id'] = $this->request->data['ItemOffer']['item_id'];
                    $offerData['ItemOffer']['is_active'] = $this->request->data['ItemOffer']['is_active'];
                    $offerData['ItemOffer']['category_id'] = $this->request->data['ItemOffer']['category_id'];
                    $offerData['ItemOffer']['unit_counter'] = $this->request->data['ItemOffer']['unit_counter'];
                    $offerData['ItemOffer']['start_date'] = $this->Dateform->formatDate($this->request->data['ItemOffer']['start_date']);
                    $offerData['ItemOffer']['end_date'] = $this->Dateform->formatDate($this->request->data['ItemOffer']['end_date']);
                    $offerData['ItemOffer']['store_id'] = $storeId;
                    $offerData['ItemOffer']['merchant_id'] = $merchantId;
                    $this->ItemOffer->saveItemOffer($offerData);
                    $this->request->data = '';
                    $this->Session->setFlash(__("Offer Successfully Added"), 'alert_success');
                } else {
                    $this->Session->setFlash(__("Offer Already exists"), 'alert_failed');
                }
            }
        }
        $categoryList = $this->Category->getCategoryList($storeId);
        $itemList = $this->Item->getallItemsByStore($storeId);
        $this->set(compact('categoryList', 'itemList'));
        $this->_itemOfferList();
        $this->loadModel('StoreDeals');
        $storeDealData = $this->StoreDeals->findByStoreId($storeId);
        $this->set('storeDealData', $storeDealData);
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
      Function name:index()
      Description:Display the list of Item Offers
      created:8/8/2015
      ----------------------------------------------------- */

    private function _itemOfferList($clearAction = null) {
        if (!empty($this->params->pass[0])) {
            $clearAction = $this->params->pass[0];
        }
        $storeID = $this->Session->read('admin_store_id');
        $criteria = "ItemOffer.store_id =$storeID AND ItemOffer.is_deleted=0";
        if ($this->Session->read('ItemOfferSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('ItemOfferSearchData'), true);
        } else {
            $this->Session->delete('ItemOfferSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('ItemOfferSearchData', json_encode($this->request->data));
            if (isset($this->request->data['ItemOffer']['isActive']) && $this->request->data['ItemOffer']['isActive'] != '') {
                $active = trim($this->request->data['ItemOffer']['isActive']);
                $criteria .= " AND (ItemOffer.is_active =$active)";
            }
            if (!empty($this->request->data['ItemOffer']['search'])) {
                $search = trim($this->request->data['ItemOffer']['search']);
                $criteria .= " AND (Item.name LIKE '%" . $search . "%')";
            }
        }

        $this->ItemOffer->bindModel(
                array(
            'belongsTo' => array(
                'Item' => array(
                    'className' => 'Item',
                    'foreignKey' => 'item_id',
                    'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                    'fields' => array('id', 'name'),
                    'type' => "INNER"
                )
            )
                ), false
        );
//        $this->Item->bindModel(
//                array(
//            'hasMany' => array(
//                'OrderItemFree' => array(
//                    'className' => 'OrderItemFree',
//                    'foreignKey' => 'item_id',
//                    'conditions' => array('OrderItemFree.is_deleted' => 0, 'OrderItemFree.is_active' => 1),
//                )
//            )
//                ), false
//        );
        $this->paginate = array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('ItemOffer.created' => 'DESC'));
        $ItemOfferdetail = $this->paginate('ItemOffer');
        if (!empty($ItemOfferdetail)) {
            foreach ($ItemOfferdetail as $key => $offerData) {
                $this->loadModel('OrderItemFree');
                $totalFreeUnits = $this->OrderItemFree->find('all', array('fields' => array('sum(OrderItemFree.free_quantity) as total_sum'), 'conditions' => array('OrderItemFree.item_id' => $offerData['Item']['id'], 'OrderItemFree.is_active' => 1, 'OrderItemFree.is_deleted' => 0)));
                $ItemOfferdetail[$key]['ItemOffer']['item_used_count'] = $totalFreeUnits[0][0]['total_sum'];
            }
        }
        //prx($ItemOfferdetail);
        $this->set('list', $ItemOfferdetail);
    }

    public function testData($a = null, $b = null) {
        echo $a . '-' . $b;
        pr($this->params);
        die;
    }

    public function itemOfferUsedList($EncryptItemID = null, $page = null) {
        $this->layout = "admin_dashboard";
        $item_id = $this->Encryption->decode($EncryptItemID);
        $storeId = $this->Session->read('admin_store_id');
        $this->loadModel('OrderItemFree');
        $this->loadModel('Order');
        $this->OrderItemFree->bindModel(
                array('belongsTo' => array(
                        'Item' => array(
                            'className' => 'Item',
                            'foreignKey' => 'item_id',
                            'fields' => array('name'),
                        )
        )));
        $this->OrderItemFree->bindModel(
                array('belongsTo' => array(
                        'Order' => array(
                            'className' => 'Order',
                            'foreignKey' => 'order_id',
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
        //$totalFreeUnitsData = $this->OrderItemFree->find('all', array('conditions' => array('OrderItemFree.item_id' => $item_id, 'OrderItemFree.store_id' => $storeId, 'OrderItemFree.is_active' => 1, 'OrderItemFree.is_deleted' => 0), 'group' => array('OrderItemFree.user_id')));
        //$this->paginate = array('fields' => array('sum(OrderItemFree.free_quantity) as total_sum', 'User.email', 'User.fname', 'User.lname', 'Item.name'), 'conditions' => array('OrderItemFree.store_id' => $storeId, 'OrderItemFree.item_id' => $item_id, 'OrderItemFree.is_active' => 1, 'OrderItemFree.is_deleted' => 0), 'group' => array('OrderItemFree.user_id'));
        //$totalFreeUnitsDataList = $this->Paginator->paginate('OrderItemFree');
        $totalFreeUnitsDataList = $this->OrderItemFree->find('all', array('recursive' => 2, 'fields' => array('OrderItemFree.order_id', 'Order.id', 'OrderItemFree.free_quantity', 'OrderItemFree.user_id', 'Item.name', 'Order.delivery_address_id', 'Order.user_id'), 'conditions' => array('OrderItemFree.store_id' => $storeId, 'OrderItemFree.item_id' => $item_id, 'OrderItemFree.is_active' => 1, 'OrderItemFree.is_deleted' => 0), 'order' => array('OrderItemFree.created' => 'DESC')));
        //prx($totalFreeUnitsDataList);
        $guestEmail = $totalFreeUnitsData = array();
        if (!empty($totalFreeUnitsDataList)) {
            foreach ($totalFreeUnitsDataList as $key => $list) {
                if (!empty($list)) {
                    if ($list['OrderItemFree']['user_id'] == 0) {
                        $index = $list['Order']['DeliveryAddress']['email'];
                        if (in_array($index, $guestEmail)) {
                            $totalFreeUnitsData[$index]['count'] = $totalFreeUnitsData[$index]['count'] + $list['OrderItemFree']['free_quantity'];
                        } else {
                            $totalFreeUnitsData[$index]['count'] = $list['OrderItemFree']['free_quantity'];
                            $guestEmail[] = $index;
                        }
                        $totalFreeUnitsData[$index]['item_name'] = $list['Item']['name'];
                        $totalFreeUnitsData[$index]['name'] = $list['Order']['DeliveryAddress']['name_on_bell'];
                        $totalFreeUnitsData[$index]['email'] = $list['Order']['DeliveryAddress']['email'];
                    } else {
                        if (!empty($list['Order']['User']['email'])) {
                            $index = $list['Order']['User']['email'];
                            if (in_array($index, $guestEmail)) {
                                $totalFreeUnitsData[$index]['count'] = $totalFreeUnitsData[$index]['count'] + $list['OrderItemFree']['free_quantity'];
                            } else {
                                $totalFreeUnitsData[$index]['count'] = $list['OrderItemFree']['free_quantity'];
                                $guestEmail[] = $index;
                            }
                            $totalFreeUnitsData[$index]['item_name'] = $list['Item']['name'];
                            $totalFreeUnitsData[$index]['name'] = $list['Order']['User']['userName'];
                            $totalFreeUnitsData[$index]['email'] = $list['Order']['User']['email'];
                        }
                    }
                }
            }
        }
        $this->set('list', $totalFreeUnitsData);
    }

    /* ------------------------------------------------
      Function name:activateCoupon()
      Description:Active/deactive Coupon
      created:08/8/2015
      ----------------------------------------------------- */

    public function activateOffer($EncryptOfferID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['ItemOffer']['store_id'] = $this->Session->read('admin_store_id');
        $data['ItemOffer']['id'] = $this->Encryption->decode($EncryptOfferID);
        $data['ItemOffer']['is_active'] = $status;
        if ($this->ItemOffer->saveItemOffer($data)) {
            if ($status) {
                $SuccessMsg = "Offer Activated";
                $this->Session->setFlash(__($SuccessMsg), 'alert_success');
                $this->redirect(array('controller' => 'itemOffers', 'action' => 'edit/' . $EncryptOfferID . '#ItemOfferStartDate'));
            } else {
                $SuccessMsg = "Offer Deactivated";
                $this->Session->setFlash(__($SuccessMsg), 'alert_success');
                $this->redirect(array('controller' => 'itemOffers', 'action' => 'add'));
            }
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'itemOffers', 'action' => 'add'));
        }
    }

    /* ------------------------------------------------
      Function name:deleteCoupon()
      Description:Delete Coupon
      created:08/8/2015
      ----------------------------------------------------- */

    public function deleteOffer($EncryptOfferID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['ItemOffer']['store_id'] = $this->Session->read('admin_store_id');
        $data['ItemOffer']['id'] = $this->Encryption->decode($EncryptOfferID);
        $data['ItemOffer']['is_deleted'] = 1;
        if ($this->ItemOffer->saveItemOffer($data)) {
            $this->Session->setFlash(__("Offer deleted"), 'alert_success');
            $this->redirect(array('controller' => 'itemOffers', 'action' => 'add'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'itemOffers', 'action' => 'add'));
        }
    }

    /* ------------------------------------------------
      Function name:editCoupon()
      Description:Edit Coupon
      created:08/8/2015
      ----------------------------------------------------- */

    public function edit($EncryptOfferID = null) {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $OfferID = $this->Encryption->decode($EncryptOfferID);
        $offerDetail = $this->ItemOffer->getOfferDetail($OfferID, $storeId);
        if ($this->request->data) {
            //pr($this->request->data);die;
            $this->request->data = $this->Common->trimValue($this->request->data);
            if (!empty($this->request->data['ItemOffer']['item_id'])) {
                $isUniqueOffer = $this->ItemOffer->checkUniqueOffer($this->request->data['ItemOffer']['item_id'], $storeId, $this->request->data['ItemOffer']['id']);
                if ($isUniqueOffer) {
                    $offerData = array();
                    $offerData['ItemOffer']['id'] = $this->request->data['ItemOffer']['id'];
                    $offerData['ItemOffer']['item_id'] = $this->request->data['ItemOffer']['item_id'];
                    $offerData['ItemOffer']['is_active'] = $this->request->data['ItemOffer']['is_active'];
                    $offerData['ItemOffer']['category_id'] = $this->request->data['ItemOffer']['category_id'];
                    $offerData['ItemOffer']['unit_counter'] = $this->request->data['ItemOffer']['unit_counter'];
                    $offerData['ItemOffer']['start_date'] = $this->Dateform->formatDate($this->request->data['ItemOffer']['start_date']);
                    $offerData['ItemOffer']['end_date'] = $this->Dateform->formatDate($this->request->data['ItemOffer']['end_date']);
                    $offerData['ItemOffer']['store_id'] = $storeId;
                    $this->ItemOffer->saveItemOffer($offerData);

                    $this->Session->setFlash(__("Offer Successfully updated"), 'alert_success');
                    $this->redirect(array('controller' => 'itemOffers', 'action' => 'add'));
                } else {
                    $this->Session->setFlash(__("Offer Already exists"), 'alert_failed');
                }
            } else {
                $this->Session->setFlash(__("Please select item"), 'alert_failed');
            }
        }
        $this->request->data = $offerDetail;
        $categoryList = $this->Category->getCategoryList($storeId);
        //$itemList = $this->Item->getallItemsByStore($storeId);
        $this->Item->bindModel(array(
            'belongsTo' => array('Category')
        ));
        $itemList = $this->Item->find('list', array(
            'fields' => array('Item.id', 'Item.name'),
            'conditions' => array('Item.store_id' => $storeId, 'Item.is_deleted' => 0, 'Item.is_active' => 1, 'Category.is_deleted' => 0, 'Category.is_active' => 1),
            'recursive' => 1,
            'order' => array('Item.name ASC')
        ));
        $this->set(compact('categoryList', 'itemList'));
    }

    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('Item');
            $storeID = $this->Session->read('admin_store_id');
            $searchData = $this->Item->find('list', array('fields' => array('Item.name', 'Item.name'), 'conditions' => array('OR' => array('Item.name LIKE' => '%' . $_GET['term'] . '%'), 'Item.is_deleted' => 0, 'Item.store_id' => $storeID)));
            echo json_encode($searchData);
        } else {
            exit;
        }
    }

    /* ------------------------------------------------
      Function name:shareOffer()
      Description:Share the coupon to customers
      created:13/06/2017
      ----------------------------------------------------- */

    public function shareExtendedOffer($EncryptExtendedOfferId = null) {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        if (!empty($_GET['extendedOfferId'])) {
            $EncryptExtendedOfferId = $_GET['extendedOfferId'];
        }
        if ($EncryptExtendedOfferId) {
            $extendedOfferId = $this->Encryption->decode($EncryptExtendedOfferId);
        } else {
            $extendedOfferId = $this->request->data['User']['extended_offer_id'];
        }
        if ($this->request->is(array('post', 'put'))) {
            $this->request->data['User']['id'] = array_filter($this->request->data['User']['id']);
            $this->loadModel('Store');
            $storeEmail = $this->Store->fetchStoreDetail($storeId);
            foreach ($this->request->data['User']['id'] as $data) {
                $this->loadModel('User');
                $this->User->bindModel(array('belongsTo' => array('CountryCode')));
                $shareuserdetail = $this->User->find('first', array('fields' => array('User.id', 'User.fname', 'User.lname', 'User.email', 'User.phone', 'User.is_emailnotification', 'User.is_smsnotification', 'User.country_code_id', 'CountryCode.code'), 'conditions' => array('User.id' => $data)));
                if (!empty($shareuserdetail)) {
                    $this->ItemOffer->bindModel(
                            array(
                        'belongsTo' => array(
                            'Item' => array(
                                'className' => 'Item',
                                'foreignKey' => 'item_id',
                                'conditions' => array('Item.is_deleted' => 0, 'Item.is_active' => 1),
                                'fields' => array('id', 'name'),
                                'type' => "INNER"
                            )
                        )
                            ), false
                    );

                    $itemOfferDetail = $this->ItemOffer->getOfferDetail($extendedOfferId, $storeId);
                    if (!empty($itemOfferDetail)) {
                        if (($itemOfferDetail['ItemOffer']['unit_counter'] - 1) > 1) {//units
                            $detail = 'Buy ' . (@$itemOfferDetail['ItemOffer']['unit_counter'] - 1) . ' Units of ' . @$itemOfferDetail['Item']['name'] . ' starting ' . date("m-d-Y", strtotime($itemOfferDetail['ItemOffer']['start_date'])) . ' to ' . date("m-d-Y", strtotime($itemOfferDetail['ItemOffer']['end_date'])) . ' and the next one is on us!';
                        } else {//unit
                            $detail = 'Buy ' . (@$itemOfferDetail['ItemOffer']['unit_counter'] - 1) . ' Unit of ' . @$itemOfferDetail['Item']['name'] . ' starting ' . date("m-d-Y", strtotime($itemOfferDetail['ItemOffer']['start_date'])) . ' to ' . date("m-d-Y", strtotime($itemOfferDetail['ItemOffer']['end_date'])) . ' and the next one is on us!';
                        }
                        $template_type = 'extended_offer';
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
                }
            }
            $message = "Extended offer send successfully";
            $this->Session->setFlash(__($message), 'alert_success');
            $this->redirect($this->referer());
        }
        $this->loadModel('User');
        $criteria = array('User.merchant_id' => $merchantId, 'User.role_id' => array(4, 5), 'User.is_deleted' => 0, 'User.is_active' => 1);
        $this->paginate = array('fields' => array('User.fname', 'User.lname', 'User.email', 'User.id', 'User.created'), 'conditions' => array($criteria), 'order' => array('User.created' => 'DESC'));
        $list = $this->paginate('User');
        $this->set(compact('list', 'extendedOfferId'));
    }

    /* ------------------------------------------------
      Function name:deleteMultipleItemOffer()
      Description:Delete multiple item offers
      created:02/08/2017
      ----------------------------------------------------- */

    public function deleteMultipleItemOffer() {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        if ($this->request->is(array('post')) && !empty($this->request->data['ItemOffer']['id'])) {
            $filter_array = array_filter($this->request->data['ItemOffer']['id']);
            if ($this->Common->deleteMultipleRecords($filter_array, 'ItemOffer')) {
                $msg = "Extended offers deleted successfully.";
                $msgType = "alert_success";
            } else {
                $msg = "Some problem occured.";
                $msgType = "alert_failed";
            }
            $this->Session->setFlash(__($msg), $msgType);
            $this->redirect($this->referer());
        }
    }

    public function orderItemOfferUsedDetail($EncryptOrderID = null) {
        $this->layout = "admin_dashboard";
        $order_id = $this->Encryption->decode($EncryptOrderID);
        $storeId = $this->Session->read('admin_store_id');
        $this->loadModel('OrderItemFree');
        $this->loadModel('Order');
        $this->OrderItemFree->bindModel(
                array('belongsTo' => array(
                        'Item' => array(
                            'className' => 'Item',
                            'foreignKey' => 'item_id',
                            'fields' => array('name'),
                        )
        )));
        $this->OrderItemFree->bindModel(
                array('belongsTo' => array(
                        'Order' => array(
                            'className' => 'Order',
                            'foreignKey' => 'order_id',
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
        
        $totalFreeUnitsDataList = $this->OrderItemFree->find('all', array('recursive' => 2, 'fields' => array('OrderItemFree.order_id', 'Order.id', 'OrderItemFree.free_quantity', 'OrderItemFree.user_id', 'Item.name', 'Order.delivery_address_id', 'Order.user_id'), 'conditions' => array('OrderItemFree.store_id' => $storeId, 'OrderItemFree.order_id' => $order_id, 'OrderItemFree.is_active' => 1, 'OrderItemFree.is_deleted' => 0), 'order' => array('OrderItemFree.created' => 'DESC')));
        //prx($totalFreeUnitsDataList);
        $guestEmail = $totalFreeUnitsData = array();
        if (!empty($totalFreeUnitsDataList)) {
            foreach ($totalFreeUnitsDataList as $key => $list) {
                if (!empty($list)) {
                    if ($list['OrderItemFree']['user_id'] == 0) {
                        $index = $list['Order']['DeliveryAddress']['email'];
                        if (in_array($index, $guestEmail)) {
                            $totalFreeUnitsData[$index]['count'] = $totalFreeUnitsData[$index]['count'] + $list['OrderItemFree']['free_quantity'];
                        } else {
                            $totalFreeUnitsData[$index]['count'] = $list['OrderItemFree']['free_quantity'];
                            $guestEmail[] = $index;
                        }
                        $totalFreeUnitsData[$index]['item_name'] = $list['Item']['name'];
                        $totalFreeUnitsData[$index]['name'] = $list['Order']['DeliveryAddress']['name_on_bell'];
                        $totalFreeUnitsData[$index]['email'] = $list['Order']['DeliveryAddress']['email'];
                    } else {
                        if (!empty($list['Order']['User']['email'])) {
                            $index = $list['Order']['User']['email'];
                            if (in_array($index, $guestEmail)) {
                                $totalFreeUnitsData[$index]['count'] = $totalFreeUnitsData[$index]['count'] + $list['OrderItemFree']['free_quantity'];
                            } else {
                                $totalFreeUnitsData[$index]['count'] = $list['OrderItemFree']['free_quantity'];
                                $guestEmail[] = $index;
                            }
                            $totalFreeUnitsData[$index]['item_name'] = $list['Item']['name'];
                            $totalFreeUnitsData[$index]['name'] = $list['Order']['User']['userName'];
                            $totalFreeUnitsData[$index]['email'] = $list['Order']['User']['email'];
                        }
                    }
                }
            }
        }
        $this->set('list', $totalFreeUnitsData);
    }

}
