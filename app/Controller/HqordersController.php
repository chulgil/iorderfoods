<?php

App::uses('HqAppController', 'Controller');

class HqordersController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common', 'Item', 'Paginator');
    public $helper = array('Encryption', 'Paginator', 'Dateform', 'Common');
    public $uses = array('Store', 'OrderOffer', 'Order', 'Item', 'ItemPrice', 'ItemType', 'Size', 'OrderItem', 'StoreReview', 'Favorite', 'Topping', 'OrderTopping', 'OrderPreference', 'StorePrintHistory', 'StoreReviewImage');

    public function beforeFilter() {
        parent::beforeFilter();

        $roleId = $this->Session->read('Auth.hq.role_id');
        if (!$roleId) {
            $this->InvalidLogin(2);
        }
    }

    /* ------------------------------------------------
      Function name:index()
      Description:List Menu Items
      created:5/8/2015
      ----------------------------------------------------- */

    public function index($clearAction = null) {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $storeInfo = array();
        $storeInfo = $this->Store->find('first', array('conditions' => array('Store.merchant_id' => $merchantId)));
        $value = "";
        $storeID = "";
        $criteria = "Order.merchant_id = $merchantId AND Order.is_active=1 AND Order.is_deleted=0 AND Order.is_future_order=0";
        if ($this->Session->read('hqOrderSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('hqOrderSearchData'), true);
        } else {
            $this->Session->delete('hqOrderSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('hqOrderSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['Order']['today'])) {
                $todaydate = $this->Common->gettodayDate();
                $criteria .= ' AND DATE(Order.pickup_time)="' . $todaydate . '"';
            }
            if (!empty($this->request->data['Order']['todayPendingOrder'])) {
                $todaydate = $this->Common->gettodayDate();
                $criteria .= ' AND Order.order_status_id= 1 AND DATE(Order.pickup_time)="' . $todaydate . '"';
            }
            if (isset($this->request->data['Order']['preOrder']) && !empty($this->request->data['Order']['preOrder'])) {
                $todaydate = $this->Common->gettodayDate();
                $criteria .= ' AND Order.is_pre_order= 1 AND DATE(Order.pickup_time) >="' . $todaydate . '"';
            }
            if (!empty($this->request->data['Order']['store_id'])) {
                $storeID = $this->request->data['Order']['store_id'];
                if ($this->request->data['Order']['store_id'] == 'All') {
                    $storeInfo = $this->Store->find('first', array('conditions' => array('Store.merchant_id' => $merchantId)));
                } else {
                    $storeInfo = $this->Store->fetchStoreDetail($storeID, $merchantId);
                    $criteria .= " AND Order.store_id = $storeID";
                }
            }
            if (!empty($this->request->data['Order']['keyword'])) {
                $value = trim($this->request->data['Order']['keyword']);
                $criteria .= " AND (Order.order_number LIKE '%" . $value . "%' OR User.fname LIKE '%" . $value . "%' OR User.lname LIKE '%" . $value . "%' OR User.email LIKE '%" . $value . "%' OR DeliveryAddress.phone LIKE '%" . $value . "%')";
            }
            if (!empty($this->request->data['OrderStatus']['id'])) {
                $orderStatusID = trim($this->request->data['OrderStatus']['id']);
                $criteria .= " AND (Order.order_status_id =$orderStatusID)";
            }
            if (!empty($this->request->data['Segment']['id'])) {
                $type = trim($this->request->data['Segment']['id']);
                $criteria .= " AND (Order.seqment_id =$type)";
            }
        }
        $this->OrderItem->bindModel(array('belongsTo' => array(
                'Item' => array('className' => 'Item', 'foreignKey' => 'item_id'),
                'Type' => array('className' => 'Type', 'foreignKey' => 'type_id'),
                'Size' => array('className' => 'Size', 'foreignKey' => 'size_id'))), false);
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->Order->bindModel(
                array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                ), 'Segment' => array(
                    'className' => 'Segment',
                    'foreignKey' => 'seqment_id'
                ),
                'OrderStatus' => array(
                    'className' => 'OrderStatus',
                    'foreignKey' => 'order_status_id'
                ),
                'DeliveryAddress' => array(
                    'className' => 'DeliveryAddress',
                    'foreignKey' => 'delivery_address_id'
                ),
                'OrderPayment' => array(
                    'className' => 'OrderPayment',
                    'foreignKey' => 'payment_id',
                    'fields' => array('id', 'transection_id', 'amount', 'payment_gateway'),
                ),
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('id', 'store_name')
                )
            ),
            'hasMany' => array(
                'OrderItem' => array(
                    'className' => 'OrderItem',
                    'foreignKey' => 'order_id'
                ),
                'StorePrintHistory' => array(
                    'className' => 'StorePrintHistory',
                    'foreignKey' => 'order_id'
                )
            ),
                ), false
        );

        $this->paginate = array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC'));
        $orderdetail = $this->paginate('Order');
        $this->set('list', $orderdetail);
        $this->loadModel('OrderStatus');
        $this->loadModel('Segment');
        if (!empty($storeID)) {
            $statusList = $this->OrderStatus->OrderStatusList($storeID);
            $typeList = $this->Segment->OrderTypeList($storeID);
        } else {
            $statusList = $this->OrderStatus->OrderStatusList($merchantId);
            $typeList = $this->Segment->OrderTypeList($merchantId);
        }
        $this->set('statusList', $statusList);
        $this->set('typeList', $typeList);
        $this->set('keyword', $value);
        if (!empty($storeInfo)) {
            $this->set('store', $storeInfo['Store']);
        }
    }

    /* ------------------------------------------------
      Function name:myOrders()
      Description:List Orders and Favourite Orders
      created:11/8/2015
      ----------------------------------------------------- */

    public function myOrders($encrypted_storeId = null, $encrypted_merchantId = null) {
        $this->layout = $this->store_inner_pages;
        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $decrypt_userId = AuthComponent::User('id');
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId'));
        $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);
        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('fields' => array('name')))), false);
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')))), false);
        $this->OrderItem->bindModel(array('hasOne' => array('StoreReview' => array('fields' => array('review_rating', 'is_approved'))), 'hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity')), 'OrderTopping' => array('fields' => array('id', 'topping_id'), 'order' => array('OrderTopping.id')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('id', 'name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('id', 'size')))), false);
        $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id', 'interval_id'))), 'belongsTo' => array('DeliveryAddress' => array('fields' => array('name_on_bell', 'city', 'address')), 'OrderStatus' => array('fields' => array('name')))), false);
        $this->Favorite->bindModel(array('belongsTo' => array('Order' => array('fields' => array('id', 'user_id', 'order_number', 'amount', 'seqment_id', 'delivery_address_id')))), false);
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->loadModel('OrderItemFree');
        $this->OrderItemFree->bindModel(array('belongsTo' => array('Item' => array('fields' =>
                    array('id', 'name')))), false);
        $this->Order->bindModel(array(
            'hasMany' => array(
                'OrderItem' => array(
                    'fields' => array('id',
                        'quantity', 'order_id', 'user_id', 'type_id',
                        'item_id', 'size_id', 'total_item_price', 'tax_price', 'interval_id')),
                'OrderItemFree' => array('foreignKey' => 'order_id', 'fields' => array('id', 'item_id', 'order_id', 'free_quantity', 'price'))
            ),
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array(
                        'id', 'store_name'
                    )))
                ), false);
        $this->paginate = array(
            'conditions' => array('Order.merchant_id' => $decrypt_merchantId, 'Order.user_id' => $decrypt_userId, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0),
            'order' => 'Order.created DESC',
            'recursive' => 2,
            'limit' => 9
        );
        $myOrders = $this->paginate('Order');
        $myFav = $this->Favorite->getFavoriteDetails($decrypt_merchantId, $decrypt_storeId, $decrypt_userId);
        $compare = array();
        foreach ($myFav as $fav) {
            $compare[] = $fav['Favorite']['order_id'];
        }
        $this->set(compact('myOrders', 'compare', 'encrypted_storeId', 'encrypted_merchantId'));
    }

    /* ------------------------------------------------
      Function name:myOrders()
      Description:List Orders and Favourite Orders
      created:11/8/2015
      ----------------------------------------------------- */

    public function mySavedOrders($encrypted_storeId = null, $encrypted_merchantId = null) {
        $this->layout = $this->store_inner_pages;
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $decrypt_userId = AuthComponent::User('id');
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId'));
        $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);
        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('fields' => array('name')))), false);
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')))), false);
        $this->OrderItem->bindModel(array('hasOne' => array('StoreReview' => array('fields' => array('review_rating', 'is_approved'))), 'hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity')), 'OrderTopping' => array('fields' => array('id', 'topping_id'), 'order' => array('OrderTopping.id')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')))), false);
        $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id', 'interval_id'))), 'belongsTo' => array('DeliveryAddress' => array('fields' => array('name_on_bell', 'city', 'address')), 'OrderStatus' => array('fields' => array('name')))), false);
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->Order->bindModel(array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array(
                        'id', 'store_name'
                    )))
                ), false);
        $this->paginate = array('conditions' => array('Order.merchant_id' => $decrypt_merchantId, 'Order.user_id' => $decrypt_userId, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 1),
            'order' => 'Order.created DESC',
            'recursive' => 3,
            'limit' => 9
        );
        $myOrders = $this->paginate('Order');
        $this->set(compact('myOrders', 'compare', 'encrypted_storeId', 'encrypted_merchantId'));
    }

    /* ------------------------------------------------
      Function name:myFavorites()
      Description:List Orders and Favourite Orders
      created:11/8/2015
      ----------------------------------------------------- */

    public function myFavorites($encrypted_storeId = null, $encrypted_merchantId = null) {
        $this->layout = $this->store_inner_pages;
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $decrypt_userId = AuthComponent::User('id');
        $this->set(compact('encrypted_storeId', 'encrypted_merchantId'));
        $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('id', 'name')))), false);
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('id', 'name')))), false);
        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('fields' => array('id', 'name')))), false);
        $this->OrderItem->bindModel(array('hasOne' => array('StoreReview' => array('fields' => array('review_rating', 'is_approved'))), 'hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity')), 'OrderTopping' => array('fields' => array('id', 'topping_id'), 'order' => array('OrderTopping.id')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('id', 'name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('id', 'size')))), false);
        $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id', 'interval_id'))), 'belongsTo' => array('DeliveryAddress' => array('fields' => array('id', 'name_on_bell', 'city', 'address')), 'OrderStatus' => array('fields' => array('id', 'name')))), false);
        $this->Favorite->bindModel(array('belongsTo' => array('Order' => array('fields' => array('id', 'user_id', 'order_number', 'amount', 'seqment_id', 'delivery_address_id', 'order_status_id', 'coupon_discount', 'created')))), false);

        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->Favorite->bindModel(array(
            'belongsTo' => array(
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array(
                        'id', 'store_name'
                    )))
                ), false);

        $this->paginate = array(
            'conditions' => array('Favorite.merchant_id' => $decrypt_merchantId, 'Favorite.user_id' => $decrypt_userId, 'Favorite.is_active' => 1, 'Favorite.is_deleted' => 0),
            'order' => 'Favorite.created DESC',
            'recursive' => 4,
            'limit' => 9
        );
        $myFav = $this->paginate('Favorite');
        $this->set(compact('myFav', 'encrypted_storeId', 'encrypted_merchantId'));
    }

    /* ------------------------------------------------
      Function name: rating()
      Description: Review and Rating for orders
      created:11/8/2015
      ----------------------------------------------------- */

    public function rating($encrypted_storeId = null, $encrypted_merchantId = null, $order_item_id = null, $order_id = null, $status = null, $orderName = null, $orderRating = null, $itemId = null) {
        $this->layout = $this->store_inner_pages;
        $decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
        $decrypt_merchantId = $this->Encryption->decode($encrypted_merchantId);
        $order_item_id = $this->Encryption->decode($order_item_id);
        $order_id = $this->Encryption->decode($order_id);
        $item_id = $this->Encryption->decode($itemId);
        $user_id = AuthComponent::User('id');
        $status = $this->Encryption->decode($status);
        $orderName = $this->Encryption->decode($orderName);
        $store = $this->Store->fetchStoreDetail($decrypt_storeId, $decrypt_merchantId);
        $this->StoreReview->bindModel(array('belongsTo' => array('User' => array('fields' => array('salutation', 'fname', 'lname')))), false);
        $allReviews = $this->StoreReview->getReviewDetails($decrypt_storeId, $item_id);
        $this->set(compact('item_id', 'orderRating', 'orderName', 'allReviews', 'status', 'allReviwes', 'encrypted_storeId', 'encrypted_merchantId', 'decrypt_storeId', 'decrypt_merchantId', 'order_item_id', 'order_id', 'user_id'));
        if ($this->data) {
            $data = $this->data;
            if (!empty($data['StoreReviewImage'])) {
                $response = $this->Common->checkImageExtensionAndSize($data['StoreReviewImage']);
                if (empty($response['status'])) {
                    $this->Session->setFlash(__($response['errmsg']), 'alert_failed');
                    $this->redirect($this->referer());
                }
            }
            $encrypted_storeId = $this->Encryption->encode($data['StoreReview']['store_id']);
            $encrypted_merchantId = $this->Encryption->encode($data['StoreReview']['merchant_id']);
            $this->StoreReview->create();
            if ($this->StoreReview->saveReview($data)) {
                $storeReviewId = $this->StoreReview->getLastInsertId();
                if (!empty($storeReviewId)) {
                    $this->_uploadStoreReviewImages($data, $storeReviewId);
                }
                $template_type = 'review_rating';
                $this->loadModel('EmailTemplate');
                $fullName = "Admin";
                $item_name = $data['StoreReview']['item_name'];
                $review = $data['StoreReview']['review_comment']; //no of person
                $rating = $data['StoreReview']['review_rating'];
                $customer_name = AuthComponent::User('fname') . " " . AuthComponent::User('lname');
                $emailSuccess = $this->EmailTemplate->storeTemplates($data['StoreReview']['store_id'], $data['StoreReview']['merchant_id'], $template_type);
                $store = $this->Store->fetchStoreDetail($data['StoreReview']['store_id'], $data['StoreReview']['merchant_id']);
                if ($emailSuccess) {
                    if (($store['Store']['notification_type'] == 1 || $store['Store']['notification_type'] == 3) && (!empty($store['Store']['notification_email']))) {
                        $storeEmail = trim($store['Store']['notification_email']);
                    } else {
                        $storeEmail = trim($store['Store']['email_id']);
                    }

                    $emailData = $emailSuccess['EmailTemplate']['template_message'];
                    $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                    $emailData = str_replace('{REVIEW}', $review, $emailData);
                    $emailData = str_replace('{RATING}', $rating, $emailData);
                    $emailData = str_replace('{ITEM_NAME}', $item_name, $emailData);
                    $emailData = str_replace('{CUSTOMER_NAME}', $customer_name, $emailData);
                    $storeAddress = $store['Store']['address'] . "<br>" . $store['Store']['city'] . ", " . $store['Store']['state'] . " " . $store['Store']['zipcode'];
                    $storePhone = $store['Store']['phone'];
                    $url = "http://" . $store['Store']['store_url'];
                    $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $store['Store']['store_url'] . "</a>";
                    $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                    $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                    $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);
                    $subject = ucwords(str_replace('_', ' ', $emailSuccess['EmailTemplate']['template_subject']));
                    $this->Email->to = $storeEmail;
                    $this->Email->subject = $subject;
                    $this->Email->from = $this->front_email;
                    $this->set('data', $emailData);
                    $this->Email->template = 'template';
                    $this->Email->smtpOptions = array(
                        'port' => "$this->smtp_port",
                        'timeout' => '30',
                        'host' => "$this->smtp_host",
                        'username' => "$this->smtp_username",
                        'password' => "$this->smtp_password"
                    );
                    $this->Email->sendAs = 'html'; // because we like to send pretty mail
                    try {
                        $this->Email->send();
                    } catch (Exception $e) {
                        
                    }

                    if (($store['Store']['notification_type'] == 2 || $store['Store']['notification_type'] == 3) && (!empty($store['Store']['notification_number']))) {
                        $mobnumber = '+1' . str_replace(array('(', ')', ' ', '-'), '', $store['Store']['notification_number']);
                    } else {
                        $mobnumber = '+1' . str_replace(array('(', ')', ' ', '-'), '', $store['Store']['phone']);
                    }
                    $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                    $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                    $smsData = str_replace('{REVIEW}', $review, $smsData);
                    $smsData = str_replace('{RATING}', $rating, $smsData);
                    $smsData = str_replace('{ITEM_NAME}', $item_name, $smsData);
                    $smsData = str_replace('{CUSTOMER_NAME}', $customer_name, $smsData);
                    $smsData = str_replace('{STORE_NAME}', $store['Store']['store_name'], $smsData);
                    $smsData = str_replace('{STORE_PHONE}', $mobnumber, $smsData);
                    $message = $smsData;
                    $this->Common->sendSmsNotificationFront($mobnumber, $message);
                }
                $this->Session->setFlash(__("Rating & Review has been saved successfully"), 'flash_success');
            } else {
                $this->Session->setFlash(__("Some problem has been occured"), 'flash_error');
            }
            $this->redirect(array('controller' => 'orders', 'action' => 'myOrders', $encrypted_storeId, $encrypted_merchantId));
        }
    }

    private function _uploadStoreReviewImages($data = null, $store_review_id = null) {
        if (!empty($data) && !empty($store_review_id)) {
            foreach ($data['StoreReviewImage']['image'] as $image) {
                if ($image['error'] == 0) {
                    $response = $this->Common->uploadMenuItemImages($image, '/storeReviewImage/', $data['StoreReview']['store_id']);
                } elseif ($image['error'] == 4) {
                    $response['status'] = true;
                    $response['imagename'] = '';
                }
                if ($response['imagename']) {
                    $imageData['image'] = $response['imagename'];
                    $imageData['store_id'] = $data['StoreReview']['store_id'];
                    $imageData['created'] = date("Y-m-d H:i:s");
                    $imageData['store_review_id'] = $store_review_id;
                    $this->StoreReviewImage->saveStoreReviewImage($imageData);
                }
            }
        }
    }

    /* ------------------------------------------------
      Function name: myFavorite()
      Description: Add/Remove favorite
      created:11/8/2015
      ----------------------------------------------------- */

    public function myFavorite($encrypted_storeId = null, $encrypted_merchantId = null, $order_id = null, $fav_id = null) {
        $this->autoRender = false;
        if (!empty($fav_id)) {
            $data['Favorite']['id'] = $this->Encryption->decode($fav_id);
        }
        $data['Favorite']['store_id'] = $this->Encryption->decode($encrypted_storeId);
        $data['Favorite']['user_id'] = AuthComponent::User('id');
        $data['Favorite']['merchant_id'] = $this->Encryption->decode($encrypted_merchantId);
        $data['Favorite']['order_id'] = $this->Encryption->decode($order_id);

        if ($this->Favorite->saveFavorite($data)) {
            $this->Session->setFlash(__("Your favorite list has been updated"), 'flash_success');
            $this->redirect(array('controller' => 'orders', 'action' => 'myFavorites', $encrypted_storeId, $encrypted_merchantId));
        } else {
            $this->Session->setFlash(__("Some problem has been occured"), 'flash_error');
            $this->redirect(array('controller' => 'orders', 'action' => 'myFavorites', $encrypted_storeId, $encrypted_merchantId));
        }
    }

    /* ------------------------------------------------
      Function name: orderDetail()
      Description: Dispaly the detail of perticular order
      created:12/8/2015
      ----------------------------------------------------- */

    public function orderDetail($order_id = null, $store_Id = null) {
        $this->layout = "hq_dashboard";
        $merchantId = $this->Session->read('merchantId');
        $storeID = $this->Encryption->decode($store_Id);
        $orderId = $this->Encryption->decode($order_id);
        $this->loadModel('OrderItemFree');
        $this->loadModel('Item');
        $this->OrderItemFree->bindModel(array('belongsTo' => array('Item' => array('fields' =>array('id', 'name','category_id')))), false);
        $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);
        $this->Item->bindModel(array('belongsTo' => array('category' => array('fields' =>
                    array('id', 'name')))), false);
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('className' => 'Item', 'foreignKey' => 'offered_item_id', 'fields' => array('id', 'name')), 'Size' => array('className' => 'Size', 'foreignKey' => 'offered_size_id', 'fields' => array('id', 'size')))), false);
        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('className' => 'Topping', 'foreignKey' => 'topping_id', 'fields' => array('id', 'name')))), false);
        $this->OrderItem->bindModel(array('hasMany' => array('OrderTopping' => array('fields' => array('id', 'topping_id', 'addon_size_id'), 'order' => array('OrderTopping.id')), 'OrderOffer' => array('fields' => array('id', 'offered_item_id', 'offered_size_id', 'quantity')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id', 'size'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name','category_id')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('id', 'name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('id', 'size')))), false);
        $this->Order->bindModel(
                array(
            'hasMany' => array(
                'OrderItem' => array(
                    'fields' => array('id',
                        'quantity', 'order_id', 'user_id', 'type_id',
                        'item_id', 'size_id', 'total_item_price', 'tax_price', 'interval_id')),
                'OrderItemFree' => array('foreignKey' => 'order_id', 'fields' => array('id', 'item_id', 'order_id', 'free_quantity', 'price'))
            ),
            'belongsTo' => array(
                'User' => array('className' => 'User', 'foreignKey' => 'user_id'),
                'Segment' => array('className' => 'Segment', 'foreignKey' => 'seqment_id'),
                'DeliveryAddress' => array('className' => 'DeliveryAddress', 'foreignKey' => 'delivery_address_id'),
                'OrderStatus' => array('fields' => array('id', 'name')),
                'OrderPayment' => array(
                    'className' => 'OrderPayment',
                    'foreignKey' => 'payment_id',
                    'fields' => array('id', 'transection_id', 'amount', 'payment_gateway', 'payment_status', 'last_digit'),
                ))), false);
        $orderDetails = $this->Order->getSingleOrderDetail($merchantId, $storeID, $orderId);
        $this->set('orderDetail', $orderDetails);
        $this->loadModel('OrderStatus');
        $statusList = $this->OrderStatus->OrderStatusList($storeID);
        $this->set('statusList', $statusList);
        
        $this->loadModel('StoreSetting');
        $storeSetting = $this->StoreSetting->find('first', array('conditions' => array('store_id' => $storeID), 'fields' => array('id','delivery_status','pickup_status','pos_menu_allow')));
        
        $savedStatus=array();
        if($orderDetails[0]['Order']['seqment_id'] == 2){
            if(!empty($storeSetting['StoreSetting']['pickup_status'])){
                $savedStatus=explode(',',$storeSetting['StoreSetting']['pickup_status']);
            }
        }
        
        if($orderDetails[0]['Order']['seqment_id'] == 3){
            if(!empty($storeSetting['StoreSetting']['delivery_status'])){
                $savedStatus=explode(',',$storeSetting['StoreSetting']['delivery_status']);
            }
        }
        
        
        
        $is_pos_menu = $storeSetting['StoreSetting']['pos_menu_allow'];
        $this->set(compact('savedStatus','is_pos_menu'));
        
        
        
        
        $printerIP = $this->Store->fetchStorePrinterIP($storeID);
        $this->set('printerIP', $printerIP['Store']['printer_location']);
    }

    /* ------------------------------------------------
      Function name: UpdateOrderStatus()
      Description: Update the order status
      created:12/8/2015
      ----------------------------------------------------- */

    public function UpdateOrderStatus() {
        $this->autoRender = false;
        $this->layout = "hq_dashboard";
        $this->loadModel('Store');
        if (!empty($this->request->data['Orders']['store_id'])) {
            $storeID = $this->request->data['Orders']['store_id'];
            $storeEmail = $this->Store->fetchStoreDetail($storeID);
            $storeAddress = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
            $storePhone = $storeEmail['Store']['phone'];
        }
        $merchantId = $this->Session->read('merchantId');
        if (!empty($this->request->data['Order']['id'])) {
            $filter_array = array_filter($this->request->data['Order']['id']);
            foreach ($filter_array as $k => $orderId) {
                $this->Order->id = $orderId;
                $this->Order->saveField("order_status_id", $this->request->data['Order']['order_status_id']);
                $orderIdn = $orderId;
                $storeID = $this->request->data['Order']['store_id'][$k];
                $storeEmail = $this->Store->fetchStoreDetail($storeID);
                $storeAddress = $storeEmail['Store']['address'] . "<br>" . $storeEmail['Store']['city'] . ", " . $storeEmail['Store']['state'] . " " . $storeEmail['Store']['zipcode'];
                $storePhone = $storeEmail['Store']['phone'];
                $this->loadModel('OrderOffer');
                $this->loadModel('OrderItem');
                $this->loadModel('DeliveryAddress');
                $this->loadModel('User');
                $this->User->bindModel(array('belongsTo' => array('CountryCode' => array('className' => 'CountryCode', 'foreignKey' => 'country_code_id', 'fields' => array('code')))), false);
                $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode' => array('className' => 'CountryCode', 'foreignKey' => 'country_code_id', 'fields' => array('code')))), false);
                $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')))), false);
                $this->OrderItem->bindModel(array('hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name', 'id')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name', 'id')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size', 'id')))), false);
                $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id'))), 'belongsTo' => array('OrderPayment' => array('className' => 'OrderPayment', 'foreignKey' => 'payment_id', 'fields' => array('id', 'transection_id', 'amount')), 'DeliveryAddress' => array('className' => 'DeliveryAddress', 'foreignKey' => 'delivery_address_id'), 'User' => array('fields' => array('fname', 'lname', 'email', 'phone', 'is_smsnotification', 'is_emailnotification', 'country_code_id')), 'OrderStatus' => array('fields' => array('name')))), false);
                $orderDetails = $this->Order->getfirstOrder($merchantId, $storeID, $orderIdn);
                $this->loadModel('EmailTemplate');
                if ($orderDetails['Order']['order_status_id'] == 2) {
                    if($orderDetails['Order']['seqment_id'] == 2){
                        $template_type = 'pickup_order_receipt';
                    }else{
                        $template_type = 'order_receipt';
                    }
                } elseif ($orderDetails['Order']['order_status_id'] == 3) {
                    $template_type = 'ready_for_delivery';
                } elseif ($orderDetails['Order']['order_status_id'] == 5) {
                    $template_type = 'delivered';
                } elseif ($orderDetails['Order']['order_status_id'] == 6) {
                    $template_type = 'ready_for_pickup';
                } elseif ($orderDetails['Order']['order_status_id'] == 9) {
                    $template_type = 'order_not_processed';
                } else {
                    $template_type = 'order_status';
                }
                $emailSuccess = $this->EmailTemplate->storeTemplates($storeID, $merchantId, $template_type);
                if ($emailSuccess) {
                    $emailData = $emailSuccess['EmailTemplate']['template_message'];
                    $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                    $fullName = $orderDetails['User']['fname'] . " " . $orderDetails['User']['lname'];
                    $orderNumber = $orderDetails['Order']['order_number'];
                    $status = $orderDetails['OrderStatus']['name'];
                    if ($orderDetails['User']['is_emailnotification'] == 1) {
                        $desc = '';
                        $offers = '';
                        $result = '';
                        foreach ($orderDetails['OrderItem'] as $order) {
                            $desc = $order['quantity'] . ' ' . @$order['Size']['size'] . ' ' . @$order['Type']['name'] . ' ' . $order['Item']['name'];
                            if (!empty($order['OrderOffer'])) {
                                foreach ($order['OrderOffer'] as $offer) {
                                    $offers .= $offer['quantity'] . 'X' . $offer['Item']['name'] . '&nbsp;';
                                }
                            }
                            if (!empty($offers)) {
                                $result .= $desc . ' ( Offer : ' . $offers . '), ';
                            } else {
                                $result .= $desc . ', ';
                            }
                            $offers = '';
                            $desc = '';
                        }
                        $emailData = str_replace('{ORDER_DETAIL}', $result, $emailData);
                        $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                        $emailData = str_replace('{ORDER_ID}', $orderNumber, $emailData);
                        $emailData = str_replace('{ORDER_STATUS}', $status, $emailData);
                        $emailData = str_replace('{TOTAL}', "$" . $orderDetails['OrderPayment']['amount'], $emailData);
                        $emailData = str_replace('{TRANSACTION_ID}', $orderDetails['OrderPayment']['transection_id'], $emailData);
                        $url = "http://" . $storeEmail['Store']['store_url'];
                        $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
                        $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                        $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);
                        $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                        $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);


                        $orderType = ($orderDetails['Order']['seqment_id'] == 2) ? "Pick-up" : "Delivery";
                        $newSubject = "Your " . $storeEmail['Store']['store_name'] . " Online Order Status# " . $orderDetails['Order']['order_number'] . "/" . $orderType;
                        $this->Email->to = $orderDetails['User']['email'];
                        $this->Email->subject = $newSubject;
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

                        $this->Email->sendAs = 'html'; // because we like to send pretty mail
                        try {
                            $this->Email->send();
                        } catch (Exception $e) {
                            
                        }
                    }

                    if ($orderDetails['User']['is_smsnotification'] == 1) {
                        $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                        $smsData = str_replace('{ORDER_NUMBER}', $orderNumber, $smsData);
                        $smsData = str_replace('{ORDER_STATUS}', $status, $smsData);
                        $smsData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $smsData);
                        $smsData = str_replace('{STORE_PHONE}', $storePhone, $smsData);
                        $message = $smsData;
                        if (!empty($orderDetails['DeliveryAddress']['phone'])) {
                            $tonumber = str_replace(array('(', ')', ' ', '-'), '', $orderDetails['DeliveryAddress']['phone']);
                        } else {
                            $tonumber = str_replace(array('(', ')', ' ', '-'), '', $orderDetails['User']['phone']);
                        }
                        if (!empty($orderDetails['DeliveryAddress']['CountryCode']['code'])) {
                            $mobnumber = $orderDetails['DeliveryAddress']['CountryCode']['code'] . "" . $tonumber;
                        } else {
                            $mobnumber = $orderDetails['User']['CountryCode']['code'] . "" . $tonumber;
                        }
                        $this->Common->sendSmsNotification($mobnumber, $message);
                    }
                }
            }
        }
        /*         * *******send mail only one user********* */
        if (!empty($this->request->data['Orders']['id'])) {
            $this->Order->id = $this->request->data['Orders']['id'];
            $this->Order->saveField("order_status_id", $this->request->data['Order']['order_status_id']);
            $this->loadModel('DeliveryAddress');
            $this->loadModel('OrderOffer');
            $this->loadModel('OrderItem');
            $this->loadModel('User');
            $orderIdn = $this->request->data['Orders']['id'];
            $this->User->bindModel(array('belongsTo' => array('CountryCode' => array('className' => 'CountryCode', 'foreignKey' => 'country_code_id', 'fields' => array('code')))), false);
            $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode' => array('className' => 'CountryCode', 'foreignKey' => 'country_code_id', 'fields' => array('code')))), false);
            $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')))), false);
            $this->OrderItem->bindModel(array('hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')))), false);
            $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id'))), 'belongsTo' => array('OrderPayment' => array('className' => 'OrderPayment', 'foreignKey' => 'payment_id', 'fields' => array('id', 'transection_id', 'amount')), 'DeliveryAddress' => array('className' => 'DeliveryAddress', 'foreignKey' => 'delivery_address_id'), 'User' => array('fields' => array('fname', 'lname', 'email', 'phone', 'is_smsnotification', 'is_emailnotification', 'country_code_id')), 'OrderStatus' => array('fields' => array('name')))), false);
            $orderDetails = $this->Order->getfirstOrder($merchantId, $storeID, $orderIdn);
            $this->loadModel('EmailTemplate');
            if ($orderDetails['Order']['order_status_id'] == 2) {
                if($orderDetails['Order']['seqment_id'] == 2){
                    $template_type = 'pickup_order_receipt';
                }else{
                    $template_type = 'order_receipt';
                }
            } elseif ($orderDetails['Order']['order_status_id'] == 3) {
                $template_type = 'ready_for_delivery';
            } elseif ($orderDetails['Order']['order_status_id'] == 5) {
                $template_type = 'delivered';
            } elseif ($orderDetails['Order']['order_status_id'] == 6) {
                $template_type = 'ready_for_pickup';
            } elseif ($orderDetails['Order']['order_status_id'] == 9) {
                $template_type = 'order_not_processed';
            } else {
                $template_type = 'order_status';
            }
            $emailSuccess = $this->EmailTemplate->storeTemplates($storeID, $merchantId, $template_type);
            if ($emailSuccess) {
                $emailData = $emailSuccess['EmailTemplate']['template_message'];
                $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                $fullName = $orderDetails['User']['fname'] . " " . $orderDetails['User']['lname'];
                $orderNumber = $orderDetails['Order']['order_number'];
                $status = $orderDetails['OrderStatus']['name'];
                if ($orderDetails['User']['is_emailnotification'] == 1) {
                    $desc = '';
                    $offers = '';
                    $result = '';
                    foreach ($orderDetails['OrderItem'] as $order) {
                        $desc = $order['quantity'] . ' ' . @$order['Size']['size'] . ' ' . @$order['Type']['name'] . ' ' . $order['Item']['name'];
                        if (!empty($order['OrderOffer'])) {
                            foreach ($order['OrderOffer'] as $offer) {
                                $offers .= $offer['quantity'] . 'X' . $offer['Item']['name'] . '&nbsp;';
                            }
                        }
                        if (!empty($offers)) {
                            $result .= $desc . ' ( Offer : ' . $offers . '), ';
                        } else {
                            $result .= $desc . ', ';
                        }
                        $offers = '';
                        $desc = '';
                    }
                    $emailData = str_replace('{ORDER_DETAIL}', $result, $emailData);
                    $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                    $emailData = str_replace('{ORDER_ID}', $orderNumber, $emailData);
                    $emailData = str_replace('{ORDER_STATUS}', $status, $emailData);
                    $emailData = str_replace('{TOTAL}', "$" . $orderDetails['OrderPayment']['amount'], $emailData);
                    $emailData = str_replace('{TRANSACTION_ID}', $orderDetails['OrderPayment']['transection_id'], $emailData);
                    $url = "http://" . $storeEmail['Store']['store_url'];
                    $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $storeEmail['Store']['store_url'] . "</a>";
                    $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                    $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);
                    $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                    $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);
                    $orderType = ($orderDetails['Order']['seqment_id'] == 2) ? "Pick-up" : "Delivery";
                    $newSubject = "Your " . $storeEmail['Store']['store_name'] . " Online Order Status #" . $orderDetails['Order']['order_number'] . "/" . $orderType;

                    $this->Email->to = $orderDetails['User']['email'];
                    $this->Email->subject = $newSubject;
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

                    $this->Email->sendAs = 'html'; // because we like to send pretty mail
                    try {
                        $this->Email->send();
                    } catch (Exception $e) {
                        
                    }
                }
                if ($orderDetails['User']['is_smsnotification'] == 1) {
                    $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                    $smsData = str_replace('{ORDER_NUMBER}', $orderNumber, $smsData);
                    $smsData = str_replace('{ORDER_STATUS}', $status, $smsData);
                    $smsData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $smsData);
                    $smsData = str_replace('{STORE_PHONE}', $storePhone, $smsData);
                    $message = $smsData;
                    if (!empty($orderDetails['DeliveryAddress']['phone'])) {
                        $tonumber = $orderDetails['DeliveryAddress']['phone'];
                    } else {
                        $tonumber = $orderDetails['User']['phone'];
                    }
                    if (!empty($orderDetails['DeliveryAddress']['CountryCode']['code'])) {
                        $mobnumber = $orderDetails['DeliveryAddress']['CountryCode']['code'] . "" . $tonumber;
                    } else {
                        $mobnumber = $orderDetails['User']['CountryCode']['code'] . "" . $tonumber;
                    }
                    $this->Common->sendSmsNotification($mobnumber, $message);
                }
            }
        }
        $this->Session->setFlash(__("Order status updated successfully."), 'alert_success');
        $this->redirect(array('action' => 'index', 'controller' => 'hqorders'));
    }

    /* ------------------------------------------------
      Function name: reviewRating()
      Description: Display the list of Reviews and Ratings in admin panel
      created:13/8/2015
      ----------------------------------------------------- */

    public function reviewRating($clearAction = null) {
        if (!$this->Common->checkPermissionByaction($this->params['controller'], $this->params['action'])) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $value = "";
        $criteria = "";
        $criteria = "StoreReview.store_id =$storeID AND StoreReview.is_deleted=0";
        if ($this->Session->read('RatingSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('RatingSearchData'), true);
        } else {
            $this->Session->delete('RatingSearchData');
        }
        if (!empty($this->request->data)) {
            $this->Session->write('RatingSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['User']['keyword'])) {
                $value = trim($this->request->data['User']['keyword']);
                $criteria .= " AND (StoreReview.review_comment LIKE '%" . $value . "%' OR Order.order_number LIKE '%" . $value . "%')";
            }
            if ($this->request->data['StoreReview']['review_rating'] != '') {
                $rating = trim($this->request->data['StoreReview']['review_rating']);
                $criteria .= " AND (StoreReview.review_rating =$rating)";
            }
        }
        $this->loadModel('Order');
        $this->loadModel('OrderItem');
        $this->OrderItem->bindModel(array('belongsTo' => array('Item' => array('className' => 'Item', 'foreignKey' => 'item_id', 'fields' => 'name'))));
        $this->loadModel('StoreReview');
        $this->StoreReview->bindModel(array('belongsTo' => array('Order' => array('className' => 'Order', 'foreignKey' => 'order_id'), 'OrderItem' => array('className' => 'OrderItem', 'foreignKey' => 'order_item_id')), 'hasMany' => array('StoreReviewImage' => array('className' => 'StoreReviewImage', 'foreignKey' => 'store_review_id', 'fields' => array('id')))));
        $this->paginate = array('conditions' => array($criteria), 'order' => array('StoreReview.created' => 'DESC'), 'recursive' => 2);
        $reviewdetail = $this->paginate('StoreReview');
        $this->set('keyword', $value);
        $this->set('list', $reviewdetail);
    }

    /* ------------------------------------------------
      Function name: reviewImages()
      Description: Display the list of store reviews images in admin panel
      created:26/07/2016
      ----------------------------------------------------- */

    public function reviewImages($EncryptStoreReviewID = null) {
        $this->layout = "admin_dashboard";
        $this->set('store_review_id', $EncryptStoreReviewID);
        $id = $this->Encryption->decode($EncryptStoreReviewID);
        if ($this->request->is('post') && count($this->request->data['StoreReviewImage']) >= 1) {
            if ($this->request->data['StoreReviewImage']['status'] == 1) {
                $updateField = "StoreReviewImage.is_active";
                $status = 1;
            } elseif ($this->request->data['StoreReviewImage']['status'] == 2) {
                $updateField = "StoreReviewImage.is_active";
                $status = 0;
            } elseif ($this->request->data['StoreReviewImage']['status'] == 3) {
                $updateField = "StoreReviewImage.is_deleted";
                $status = 1;
            }
            foreach ($this->request->data['StoreReviewImage'] as $imageData) {
                $this->StoreReviewImage->updateAll(array($updateField => $status), array('StoreReviewImage.id' => $this->request->data['StoreReviewImage']['id']));
            }
            $this->Session->setFlash(__("Status updated successfully."), 'alert_success');
        }
        $result = $this->StoreReviewImage->getAllReviewImages($id);
        $this->set('storeReviewImages', $result);
    }

    /* ------------------------------------------------
      Function name: ApprovedReview()
      Description: Review approve and disapproved
      created:14/8/2015
      ----------------------------------------------------- */

    public function approvedReview($EncryptReviewID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $id = $this->Encryption->decode($EncryptReviewID);
        $this->StoreReview->id = $id;
        $this->StoreReview->saveField("is_approved", $status);
        $this->Session->setFlash(__("Review status updated successfully."), 'alert_success');
        $this->redirect($this->referer());
    }

    public function ajaxRequest($id = '') {
        $this->autoRender = false;
        $this->loadModel('OrderStatus');
        $this->layout = "admin_dashboard";
        if (!empty($this->request->params['requested'])) {
            $data = $this->OrderStatus->find('first', array('conditions' => array('OrderStatus.id' => $id)));
            echo $data['OrderStatus']['name'];
        }
    }

    public function dashboardData() {
        $this->autoRender = false;
        $storeId = $this->Session->read('admin_store_id');
        $todaydate = $this->Common->gettodayDate();
        $ordercount = $this->Order->find('all', array('conditions' => array('Order.store_id' => $storeId, 'Order.is_active' => 1, 'DATE(Order.created)' => $todaydate)));
        return $ordercount;
    }

    function PrintReceipt($encryorderId = null, $fromView = null) {
        $this->autoRender = false;
        $orderId = $this->Encryption->decode($encryorderId);
        $storeID = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('merchantId');
        $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme'), 'hasMany' => array('StoreGallery', 'StoreContent')));
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')), 'Size' => array('className' => 'Size', 'foreignKey' => 'offered_size_id', 'fields' => array('id', 'size')))), false);
        $this->OrderItem->bindModel(array('hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity', 'offered_size_id'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('id', 'name')))), false);
        $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'item_id', 'size_id', 'type_id', 'total_item_price', 'discount'))), 'belongsTo' => array('Store' => array('fields' => array('id', 'service_fee', 'delivery_fee', 'store_name', 'store_url', 'address')), 'Segment' => array('className' => 'Segment', 'foreignKey' => 'seqment_id', 'fields' => array('name')), 'DeliveryAddress' => array('fields' => array('name_on_bell', 'city', 'address')), 'OrderStatus' => array('fields' => array('name')))), false);
        $orderDetails = $this->Order->getfirstOrder($merchantId, $storeID, $orderId);
        $amount = 0;
        App::import('Vendor', 'escpos', array('file' => 'escpos' . DS . 'Escpos.php'));
        try {
            $storeData = $this->Store->fetchStorePrinterIP($storeID);
            if ($storeData) {
                $connector = new NetworkPrintConnector($storeData['Store']['printer_location'], 9100);
                if (is_object($connector)) {
                    $printer = new Escpos($connector);
                    $itemss = array();
                    foreach ($orderDetails['OrderItem'] as $order) {
                        if (empty($order['OrderOffer'])) {
                            $itemss[] = $order['quantity'] . ' ' . @$order['Size']['name'] . ' ' . $order['Item']['name'] . ' $' . number_format($order['total_item_price'], 2);
                        } else {
                            $prefix = '';
                            $offerItemName = '';
                            foreach ($order['OrderOffer'] as $off) {
                                $offerItemName .= $prefix . '' . $off['quantity'] . ' ' . @$off['Size']['size'] . ' ' . $off['Item']['name'];
                                $prefix = "\n";
                            }
                            $itemss[] = $order['quantity'] . ' ' . @$order['Type']['name'] . ' ' . @$order['Size']['name'] . ' ' . $order['Item']['name'] . "\n" . $offerItemName . ' $' . number_format($order['total_item_price'], 2);
                        }
                        $amount = $amount + $order['total_item_price'];
                    }
                    $printdata = "";
                    if ($orderDetails['Order']['seqment_id'] == 3) {
                        $printdata .= 'Subtotal : $' . number_format($amount, 2) . "\n\n Discount: $" . number_format($orderDetails['Order']['coupon_discount'], 2) . "\n Delivery Charge : $" . number_format($orderDetails['Store']['delivery_fee'], 2) . "\n Service Fee : $" . number_format($orderDetails['Store']['service_fee'], 2) . "\n Total : $" . number_format($orderDetails['Order']['amount'], 2);
                    } else {
                        $printdata .= 'Subtotal : $' . number_format($amount, 2) . "\n\n Discount: $" . number_format($orderDetails['Order']['coupon_discount'], 2) . "\n Service Fee : $" . number_format($orderDetails['Store']['service_fee'], 2) . "\n Total : $" . number_format($orderDetails['Order']['amount'], 2);
                    }
                    $printer->text($orderDetails['Store']['store_url'] . "\n");
                    $printer->text($orderDetails['Store']['store_name'] . "\n\n");
                    $printer->text("Order Detail\n\n");
                    foreach ($itemss as $dataItem) {
                        $printer->text($dataItem . "\n");
                    }
                    $printer->text($printdata . "\n\n\n");
                    $printer->text('Thank you for ordering at ' . $orderDetails['Store']['store_name'] . "\n");
                    $printer->text('For more information, please visit' . $orderDetails['Store']['store_url'] . "\n\n\n");
                    $printer->text(date('l jS \of F Y h:i:s A') . "\n");
                    $printer->cut();
                    $printer->close();
                    return 1;
                }
            } else {
                throw new Exception("Printer Details Not Found");
            }
        } catch (Exception $e) {
            $error = "Printer IP not found or unable to connect to printer";
            $this->Session->setFlash(__($error), 'alert_failed');
        }
        if ($fromView) {
            $this->Session->setFlash(__($error), 'alert_failed');
            $this->redirect(array('controller' => 'orders', 'action' => 'orderDetail', $encryorderId));
        }
    }

    public function deleteSaveOrder($encrypted_storeId = null, $encrypted_merchantId = null, $encrypted_orderId = null) {
        $this->autoRender = false;
        $futureOrderId = $this->Encryption->decode($encrypted_orderId);
        $this->loadModel('OrderOffer');
        $this->loadModel('OrderTopping');
        $this->loadModel('OrderItem');
        $this->loadModel('Order');
        if ($this->Order->delete($futureOrderId)) {
            $this->OrderOffer->deleteAll(array('OrderOffer.order_id' => $futureOrderId), false);
            $this->OrderItem->deleteAll(array('OrderItem.order_id' => $futureOrderId), false);
            $this->OrderTopping->deleteAll(array('OrderTopping.order_id' => $futureOrderId), false);
            $this->Session->setFlash(__('Saved Order has been deleted'), 'flash_success');
            $this->redirect(array('controller' => 'orders', 'action' => 'mySavedOrders', $encrypted_storeId, $encrypted_merchantId)); //
        } else {
            $this->Session->setFlash(__('Saved Order could not be deleted, please try again'), 'flash_error');
            $this->redirect(array('controller' => 'orders', 'action' => 'mySavedOrders', $encrypted_storeId, $encrypted_merchantId)); //
        }
    }

    public function getOrderListData($clearAction = null) {
        $this->layout = false;
        $merchantId = $this->Session->read('merchantId');
        $storeInfo = $this->Store->find('first', array('conditions' => array('Store.merchant_id' => $merchantId)));
        $value = "";
        $storeID = "";
        $criteria = "Order.merchant_id = $merchantId AND Order.is_active=1 AND Order.is_deleted=0 AND Order.is_future_order=0";
        if ($this->Session->read('hqOrderSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('hqOrderSearchData'), true);
        } else {
            $this->Session->delete('hqOrderSearchData');
        }
        if (!empty($this->request->data)) {
            $this->Session->write('hqOrderSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['Order']['today'])) {
                $todaydate = $this->Common->gettodayDate();
                $criteria .= ' AND DATE(Order.created)="' . $todaydate . '"';
            }
            if (!empty($this->request->data['Order']['todayPendingOrder'])) {
                $todaydate = $this->Common->gettodayDate();
                $criteria .= ' AND Order.order_status_id= 1 AND DATE(Order.created)="' . $todaydate . '"';
            }
            if (isset($this->request->data['Order']['preOrder']) && !empty($this->request->data['Order']['preOrder'])) {
                $todaydate = $this->Common->gettodayDate();
                $criteria .= ' AND Order.is_pre_order= 1 AND DATE(Order.pickup_time) >= "' . $todaydate . '"';
            }
            if (!empty($this->request->data['Order']['store_id'])) {
                $storeID = $this->request->data['Order']['store_id'];
                if ($this->request->data['Order']['store_id'] == 'All') {
                    $storeInfo = $this->Store->find('first', array('conditions' => array('Store.merchant_id' => $merchantId)));
                } else {
                    $storeInfo = $this->Store->fetchStoreDetail($storeID, $merchantId);
                    $criteria .= " AND Order.store_id = $storeID";
                }
            }
            if (!empty($this->request->data['Order']['keyword'])) {
                $value = trim($this->request->data['Order']['keyword']);
                $criteria .= " AND (Order.order_number LIKE '%" . $value . "%' OR User.fname LIKE '%" . $value . "%' OR User.lname LIKE '%" . $value . "%' OR User.email LIKE '%" . $value . "%' OR DeliveryAddress.phone LIKE '%" . $value . "%')";
            }
            if (!empty($this->request->data['OrderStatus']['id'])) {
                $orderStatusID = trim($this->request->data['OrderStatus']['id']);
                $criteria .= " AND (Order.order_status_id =$orderStatusID)";
            }
            if (!empty($this->request->data['Segment']['id'])) {
                $type = trim($this->request->data['Segment']['id']);
                $criteria .= " AND (Order.seqment_id =$type)";
            }
        }
        $this->OrderItem->bindModel(array('belongsTo' => array(
                'Item' => array('className' => 'Item', 'foreignKey' => 'item_id'),
                'Type' => array('className' => 'Type', 'foreignKey' => 'type_id'),
                'Size' => array('className' => 'Size', 'foreignKey' => 'size_id'))), false);
        $this->Order->bindModel(
                array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id'
                ), 'Segment' => array(
                    'className' => 'Segment',
                    'foreignKey' => 'seqment_id'
                ),
                'OrderStatus' => array(
                    'className' => 'OrderStatus',
                    'foreignKey' => 'order_status_id'
                ),
                'DeliveryAddress' => array(
                    'className' => 'DeliveryAddress',
                    'foreignKey' => 'delivery_address_id'
                ),
                'OrderPayment' => array(
                    'className' => 'OrderPayment',
                    'foreignKey' => 'payment_id',
                    'fields' => array('id', 'transection_id', 'amount', 'payment_gateway'),
                ),
                'Store' => array(
                    'className' => 'Store',
                    'foreignKey' => 'store_id',
                    'fields' => array('id', 'store_name')
                )
            ),
            'hasMany' => array(
                'OrderItem' => array(
                    'className' => 'OrderItem',
                    'foreignKey' => 'order_id'
                ),
                'StorePrintHistory' => array(
                    'className' => 'StorePrintHistory',
                    'foreignKey' => 'order_id'
                )
            ),
                ), false
        );
        $this->paginate = array('recursive' => 2, 'conditions' => array($criteria), 'order' => array('Order.created' => 'DESC'));
        $orderdetail = $this->paginate('Order');
        $this->set('list', $orderdetail);
        $this->loadModel('OrderStatus');
        $this->loadModel('Segment');
        if (!empty($storeID)) {
            $statusList = $this->OrderStatus->OrderStatusList($storeID);
            $typeList = $this->Segment->OrderTypeList($storeID);
        } else {
            $statusList = $this->OrderStatus->OrderStatusList($merchantId);
            $typeList = $this->Segment->OrderTypeList($merchantId);
        }
        $this->set('statusList', $statusList);
        $this->set('typeList', $typeList);
        $this->set('keyword', $value);
        $this->set('store', $storeInfo['Store']);
    }

    /* ------------------------------------------------
      Function name:confirmOrder()
      Description:Activating user account
      created:21/8/2015
      ----------------------------------------------------- */

    public function confirmOrder($encorderId = null) {
        $this->layout = false;
        $this->autoRender = false;
        $orderId = $this->Encryption->decode($encorderId);
        if ($orderId) {
            $confirmOrder = $this->Order->getconfirmorder($orderId);
            if (empty($confirmOrder)) {
                $orderdetails['id'] = $orderId;
                $orderdetails['order_status_id'] = 8;
                $this->Order->saveOrder($orderdetails);             // 8 is Confirmed
                $storeurl = $this->notifyCustomer($orderId);
                $this->Session->setFlash(__('Order has been confirmed'), 'flash_success', array('class' => 'order_confirm'), 'order_confirm');
                $string = BASE_URL;
                $parts = parse_url($string);
                $isIP = (bool) ip2long($parts['path']);
                if (!$isIP) {
                    $this->redirect('/admin');
                } else {
                    $this->redirect(HTTP_ROOT . $storeurl . '/admin');
                }
            } else {
                $this->Order->bindModel(array('belongsTo' => array('Store' => array('foreignKey' => 'store_id', 'fields' => array('id', 'store_name', 'merchant_id', 'store_url')))), false);
                $orderDetails = $this->Order->getOrderInfo($orderId);
                $this->Session->setFlash(__('This order is already confirmed'), 'flash_error', array('class' => 'link_used'), 'link_used');
                $this->redirect(HTTP_ROOT . $orderDetails['Store']['store_url'] . '/admin');
            }
        }
    }

    public function notifyCustomer($orderId) {
        if (!empty($orderId)) {
            $this->loadModel('DeliveryAddress');
            $this->loadModel('OrderOffer');
            $this->loadModel('OrderItem');
            $this->loadModel('User');
            $this->User->bindModel(array('belongsTo' => array('CountryCode' => array('className' => 'CountryCode', 'foreignKey' => 'country_code_id', 'fields' => array('code')))), false);
            $this->DeliveryAddress->bindModel(array('belongsTo' => array('CountryCode' => array('className' => 'CountryCode', 'foreignKey' => 'country_code_id', 'fields' => array('code')))), false);
            $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')))), false);
            $this->OrderItem->bindModel(array('hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')))), false);
            $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id'))), 'belongsTo' => array('OrderPayment' => array('className' => 'OrderPayment', 'foreignKey' => 'payment_id', 'fields' => array('id', 'transection_id', 'amount')), 'DeliveryAddress' => array('className' => 'DeliveryAddress', 'foreignKey' => 'delivery_address_id'), 'User' => array('fields' => array('fname', 'lname', 'email', 'phone', 'is_smsnotification', 'is_emailnotification', 'country_code_id')), 'OrderStatus' => array('fields' => array('name')))), false);
            $this->Order->bindModel(array('belongsTo' => array('Store' => array('foreignKey' => 'store_id', 'fields' => array('id', 'store_name', 'merchant_id', 'store_url')))), false);
            $orderDetails = $this->Order->getOrderInfo($orderId);
            $this->loadModel('EmailTemplate');
            $template_type = 'order_status';
            $emailSuccess = $this->EmailTemplate->storeTemplates($orderDetails['Store']['id'], $orderDetails['Store']['merchant_id'], $template_type);
            $storeEmail = $this->Store->fetchStoreDetail($orderDetails['Store']['id']);
            if ($emailSuccess) {
                $emailData = $emailSuccess['EmailTemplate']['template_message'];
                $smsData = $emailSuccess['EmailTemplate']['sms_template'];
                $fullName = $orderDetails['User']['fname'] . " " . $orderDetails['User']['lname'];
                $orderNumber = $orderDetails['Order']['order_number'];
                $status = $orderDetails['OrderStatus']['name'];
                if ($orderDetails['User']['is_emailnotification'] == 1) {
                    $desc = '';
                    $offers = '';
                    $result = '';
                    foreach ($orderDetails['OrderItem'] as $order) {
                        $desc = $order['quantity'] . ' ' . @$order['Size']['size'] . ' ' . @$order['Type']['name'] . ' ' . $order['Item']['name'];
                        if (!empty($order['OrderOffer'])) {
                            foreach ($order['OrderOffer'] as $offer) {
                                $offers .= $offer['quantity'] . 'X' . $offer['Item']['name'] . '&nbsp;';
                            }
                        }
                        if (!empty($offers)) {
                            $result .= $desc . ' ( Offer : ' . $offers . '), ';
                        } else {
                            $result .= $desc . ', ';
                        }
                        $offers = '';
                        $desc = '';
                    }
                    $emailData = str_replace('{ORDER_DETAIL}', $result, $emailData);
                    $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                    $emailData = str_replace('{ORDER_ID}', $orderNumber, $emailData);
                    $emailData = str_replace('{ORDER_STATUS}', $status, $emailData);
                    $emailData = str_replace('{TOTAL}', "$" . $orderDetails['OrderPayment']['amount'], $emailData);
                    $emailData = str_replace('{TRANSACTION_ID}', $orderDetails['OrderPayment']['transection_id'], $emailData);
                    $emailData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $emailData);
                    $storeAddress = $stor['Store']['address'] . "<br>" . $stor['Store']['city'] . ", " . $stor['Store']['state'] . " " . $stor['Store']['zipcode'];
                    $storePhone = $stor['Store']['phone'];
                    $url = "http://" . $stor['Store']['store_url'];
                    $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $stor['Store']['store_url'] . "</a>";
                    $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                    $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                    $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);
                    $orderType = ($orderDetails['Order']['seqment_id'] == 2) ? "Pick-up" : "Delivery";
                    $newSubject = "Your " . $storeEmail['Store']['store_name'] . " Online Order Status #" . $orderDetails['Order']['order_number'] . "/" . $orderType;
                    $this->Email->to = $orderDetails['User']['email'];
                    $this->Email->subject = $newSubject;
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
                    $this->Email->sendAs = 'html'; // because we like to send pretty mail
                    try {
                        $this->Email->send();
                    } catch (Exception $e) {
                        
                    }
                }
                if ($orderDetails['User']['is_smsnotification'] == 1) {
                    $smsData = str_replace('{FULL_NAME}', $fullName, $smsData);
                    $smsData = str_replace('{ORDER_NUMBER}', $orderNumber, $smsData);
                    $smsData = str_replace('{ORDER_STATUS}', $status, $smsData);
                    $smsData = str_replace('{STORE_NAME}', $storeEmail['Store']['store_name'], $smsData);
                    $smsData = str_replace('{STORE_PHONE}', $storePhone, $smsData);
                    $message = $smsData;
                    if (!empty($orderDetails['DeliveryAddress']['phone'])) {
                        $tonumber = $orderDetails['DeliveryAddress']['phone'];
                    } else {
                        $tonumber = $orderDetails['User']['phone'];
                    }
                    if (!empty($orderDetails['DeliveryAddress']['CountryCode']['code'])) {
                        $mobnumber = $orderDetails['DeliveryAddress']['CountryCode']['code'] . "" . $tonumber;
                    } else {
                        $mobnumber = $orderDetails['User']['CountryCode']['code'] . "" . $tonumber;
                    }
                    $this->Common->sendSmsNotification($mobnumber, $message);
                }
            }
            return $orderDetails['Store']['store_url'];
        }
    }

    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('Order');
            $this->Order->bindModel(
                    array(
                'belongsTo' => array(
                    'User' => array(
                        'className' => 'User',
                        'foreignKey' => 'user_id',
                        'conditions' => array('User.is_active' => 1, 'User.is_deleted' => 0)
                    ),
                    'DeliveryAddress' => array(
                        'className' => 'DeliveryAddress',
                        'foreignKey' => 'delivery_address_id',
                        'conditions' => array('DeliveryAddress.is_active' => 1, 'DeliveryAddress.is_deleted' => 0)
                    )
                )
                    ), false
            );
            if (!empty($_GET['storeID']) && $_GET['storeID'] != 'All') {
                $storeID = $_GET['storeID'];
            } else {
                $merchant_id = $this->Session->read('merchantId');
                $storeID = $this->Store->getAllStoresByMerchantId($merchant_id);
            }
            $criteria = "";
            if (!empty($_GET['term'])) {
                $criteria = " Order.is_deleted=0 AND Order.is_future_order=0";
                $value = trim($_GET['term']);
                $criteria .= " AND (Order.order_number LIKE '%" . $value . "%' OR User.fname LIKE '%" . $value . "%' OR User.lname LIKE '%" . $value . "%' OR User.email LIKE '%" . $value . "%' OR DeliveryAddress.phone LIKE '%" . $value . "%')";
            }
            $searchData = $this->Order->find('all', array('fields' => array('Order.order_number', 'User.fname', 'User.lname', 'User.email', 'DeliveryAddress.phone'), 'conditions' => array('Order.store_id' => $storeID, $criteria), 'order' => array('Order.created' => 'DESC')));
            //prx($searchData);
            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    $new_array[] = array('label' => $val['Order']['order_number'], 'value' => $val['Order']['order_number'], 'desc' => $val['Order']['order_number'] . '-' . $val['User']['fname'] . " " . $val['User']['lname'] . '-' . $val['User']['email'] . '-' . $val['DeliveryAddress']['phone']);
                };
            }
            echo json_encode($new_array);
        } else {
            exit;
        }
    }

}
