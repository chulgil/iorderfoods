<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
App::uses('HqAppController', 'Controller');

class HqcustomersController extends HqAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'Common', 'Dateform');
    public $uses = 'User';
    public $layout = 'hq_dashboard';

    public function beforeFilter() {
        parent::beforeFilter();
    }

    /* ------------------------------------------------
      Function name:index()
      Description:Display the list of Customer
      created:21/09/2016
      ----------------------------------------------------- */

    public function index($clearAction = null) {
        $merchantId = $this->Session->read('merchantId');
        $value = "";
        $criteria = "User.merchant_id =$merchantId AND User.role_id IN (4,5) AND User.is_deleted=0";
        if (!empty($this->request->data['User']['store_id']) && $this->request->data['User']['store_id'] != 'All') {
            $storeId = $this->request->data['User']['store_id'];
            $criteria .= " AND User.store_id =$storeId";
        }
        if ($this->Session->read('HqCustomerSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('HqCustomerSearchData'), true);
        } else {
            $this->Session->delete('HqCustomerSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }

        if (!empty($this->request->data)) {
            $this->Session->write('HqCustomerSearchData', json_encode($this->request->data));
            if (!empty($this->request->data['User']['keyword'])) {
                $value = trim($this->request->data['User']['keyword']);
                $criteria .= " AND (User.fname LIKE '%" . $value . "%' OR User.email LIKE '%" . $value . "%' OR User.lname LIKE '%" . $value . "%' OR User.phone LIKE '%" . $value . "%')";
            }
            if (isset($this->request->data['User']['is_active']) && $this->request->data['User']['is_active'] != '') {
                $active = trim($this->request->data['User']['is_active']);
                $criteria .= " AND (User.is_active =$active)";
            }
            if ($this->request->data['User']['from'] != '' && $this->request->data['User']['to'] != '') {
                $stratdate = $this->Dateform->formatDate($this->request->data['User']['from']);
                $enddate = $this->Dateform->formatDate($this->request->data['User']['to']);
                $criteria.= " AND (Date(User.created) >= '" . $stratdate . "' AND Date(User.created) <='" . $enddate . "')";
            }
        }
        $this->paginate = array('conditions' => array($criteria), 'order' => array('User.created' => 'DESC'), 'recursive' => -1);
        $customerdetail = $this->paginate('User');
        $this->set('list', $customerdetail);
        $this->set('keyword', $value);
    }

    /* ------------------------------------------------
      Function name:editCustomer()
      Description:Registration  Form for the  End customer
      created:21/09/2016
      ----------------------------------------------------- */

    public function editCustomer($EncryptCustomerID = null) {
        $merchantId = $this->Session->read('merchantId');
        $data['User']['id'] = $this->Encryption->decode($EncryptCustomerID);
        $this->loadModel('User');
        $this->loadModel('DeliveryAddress');
        $customerDetail = $this->User->currentUserInfo($data['User']['id']);
        $addressDetail = $this->DeliveryAddress->fetchAllAddress($data['User']['id']);
        $defaultAddress = $this->DeliveryAddress->fetchDefaultAddress($data['User']['id']);
        $this->set(compact('addressDetail', 'defaultAddress'));
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $email = trim($this->data['User']['email']);
            $isUniqueEmail = $this->User->checkUserUniqueEmailHq($email, $merchantId, $data['User']['id'], 5);
            if ($isUniqueEmail) {
                $email = trim($this->request->data['User']['email']); //Here username is email
                $this->request->data['User']['merchant_id'] = $merchantId; // Merchant Id
                $userName = trim($this->request->data['User']['email']); //Here username is email
                $this->request->data['User']['username'] = trim($userName);
                $actualDbDate = $this->Dateform->formatDate($this->request->data['User']['dateOfBirth']); // calling formatDate function in Appcontroller to format the date (Y-m-d) format
                $this->request->data['User']['dateOfBirth'] = $actualDbDate;
                $this->User->saveUserInfo($this->Common->trimValue($this->request->data));   // We are calling function written on Model to save data
                $this->Session->setFlash(__('Customer details updated successfully'), 'alert_success');
                $this->redirect(array('controller' => 'hqcustomers', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("Email  Already exists"), 'alert_failed');
            }
        }$this->set(compact('addressDetail'));
        $this->request->data = $customerDetail;
    }

    /* ------------------------------------------------
      Function name:deleteCustomer()
      Description:Delete Customer
      created:21/09/2016
      ----------------------------------------------------- */

    public function deleteCustomer($EncryptCustomerID = null) {
        $this->autoRender = false;
        $data['User']['merchant_id'] = $this->Session->read('merchantId');
        $data['User']['id'] = $this->Encryption->decode($EncryptCustomerID);
        $data['User']['is_deleted'] = 1;
        if ($this->User->saveUserInfo($data)) {
            $this->Session->setFlash(__("User deleted"), 'alert_success');
            $this->redirect(array('controller' => 'hqcustomers', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqcustomers', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:activateCustomer()
      Description:Active/deactive Customer
      created:21/09/2016
      ----------------------------------------------------- */

    public function activateCustomer($EncryptCustomerID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['User']['merchant_id'] = $this->Session->read('merchantId');
        $data['User']['id'] = $this->Encryption->decode($EncryptCustomerID);
        $data['User']['is_active'] = $status;
        if ($this->User->saveUserInfo($data)) {
            if ($status) {
                $SuccessMsg = "User Activated";
            } else {
                $SuccessMsg = "User Deactivated and User will not get Display in the List";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect(array('controller' => 'hqcustomers', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'hqcustomers', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:orderHistory()
      Description:Display the customer all orders
      created:21/09/2016
      ----------------------------------------------------- */

    public function orderHistory($EncryptCustomerID = null) {
        $userId = $this->Encryption->decode($EncryptCustomerID);
        $userDetail = $this->User->findById($userId, array('fname', 'lname', 'email', 'phone'));
        $this->set(compact('userDetail', 'EncryptCustomerID'));
    }

    public function ajaxRequest($id = null) {
        $this->autoRender = false;
        $this->loadModel('OrderStatus');
        $this->layout = "admin_dashboard";
        if (!empty($this->request->params['requested'])) {
            $data = $this->OrderStatus->find('first', array('conditions' => array('OrderStatus.id' => $id)));
            echo $data['OrderStatus']['name'];
        }
    }

    public function orderDetail() {
        $merchantId = $this->Session->read('merchantId');
        $EncryptCustomerID = $_GET['cId'];
        $userId = $this->Encryption->decode($EncryptCustomerID);
        $this->loadModel('Order');
        $this->Order->bindModel(
                array(
            'belongsTo' => array(
                //'User' => array('className' => 'User', 'foreignKey' => 'user_id'),
                'Segment' => array('className' => 'Segment', 'foreignKey' => 'seqment_id'),
                'OrderStatus' => array('fields' => array('name')),
                'OrderPayment' => array(
                    'className' => 'OrderPayment',
                    'foreignKey' => 'payment_id',
                    'fields' => array('id', 'transection_id', 'amount', 'payment_gateway'),
                ))), false);
        $fields = array('Order.store_id', 'Order.created', 'Order.id', 'Order.order_number', '(Order.amount-Order.coupon_discount) as amt', 'Order.amount','Order.user_id','Order.coupon_discount', 'OrderStatus.name', 'Segment.name');
        $this->paginate = array('fields' => @$fields, 'conditions' => array('Order.merchant_id' => $merchantId, 'Order.user_id' => $userId, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0), 'order' => array('Order.created' => 'DESC'));
        $orderDetails = $this->paginate('Order');
        $this->set(compact('orderDetails', 'EncryptCustomerID'));
    }

    public function reviewDetail() {
        $EncryptCustomerID = $_GET['cId'];
        $userId = $this->Encryption->decode($EncryptCustomerID);
        $this->loadModel('StoreReview');
        $this->loadModel('OrderItem');
        $this->OrderItem->bindModel(array('belongsTo' => array('Item' => array('className' => 'Item', 'fields' => array('name')))), false);
        $this->StoreReview->bindModel(array('belongsTo' => array('OrderItem' => array('className' => 'OrderItem', 'fields' => array('id', 'order_id', 'item_id')))), false);
        $this->paginate = array('fields' => array('StoreReview.created', 'StoreReview.store_id', 'StoreReview.review_comment', 'StoreReview.review_rating', 'StoreReview.order_id', 'StoreReview.id', 'StoreReview.order_item_id'), 'recursive' => 2, 'conditions' => array('StoreReview.is_deleted' => 0, 'StoreReview.user_id' => $userId));
        $myReviews = $this->paginate('StoreReview');
        $this->set(compact('orderDetails', 'EncryptCustomerID', 'myReviews'));
    }

    public function reservationDetail() {
        $EncryptCustomerID = $_GET['cId'];
        $userId = $this->Encryption->decode($EncryptCustomerID);
        $this->loadModel('Booking');
        $this->Booking->bindModel(array('belongsTo' => array('BookingStatus')), false);
        $this->paginate = array('fields' => array('Booking.store_id', 'BookingStatus.name', 'Booking.special_request', 'Booking.id', 'Booking.number_person', 'Booking.reservation_date'), 'conditions' => array('Booking.user_id' => $userId, 'Booking.is_active' => 1, 'Booking.is_deleted' => 0));
        $myBookings = $this->paginate('Booking');
        $this->set(compact('orderDetails', 'EncryptCustomerID', 'myBookings'));
    }

    public function customerOrderDetail($order_id = null) {
        $orderId = $this->Encryption->decode($order_id);
        $this->loadModel('OrderItemFree');
        $this->loadModel('OrderPreference');
        $this->loadModel('OrderOffer');
        $this->loadModel('OrderTopping');
        $this->loadModel('OrderItem');
        $this->loadModel('Order');
        $this->OrderItemFree->bindModel(array('belongsTo' => array('Item' => array('fields' =>
                    array('id', 'name')))), false);
        $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('className' => 'Item', 'foreignKey' => 'offered_item_id', 'fields' => array('id', 'name')), 'Size' => array('className' => 'Size', 'foreignKey' => 'offered_size_id', 'fields' => array('id', 'size')))), false);
        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('className' => 'Topping', 'foreignKey' => 'topping_id', 'fields' => array('id', 'name')))), false);
        $this->OrderItem->bindModel(array('hasMany' => array('OrderTopping' => array('fields' => array('id', 'topping_id', 'addon_size_id'), 'order' => array('OrderTopping.id')), 'OrderOffer' => array('fields' => array('id', 'offered_item_id', 'offered_size_id', 'quantity')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id','size'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('id', 'name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('id', 'size')))), false);
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
        $orderDetails = $this->Order->getsuperSingleOrderDetail(null, null, $orderId);
        $this->set('orderDetail', $orderDetails);
        $this->loadModel('OrderStatus');
        $statusList = $this->OrderStatus->OrderStatusList(null);
        $this->set('statusList', $statusList);
        $referer = $this->referer();
        if ($this->referer() != "/") {
            $this->Session->write("ref", $referer);
        }
    }

    public function getSearchValues() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is(array('get'))) {
            $this->loadModel('User');
            if (!empty($_GET['storeID'])) {
                $storeID = $_GET['storeID'];
            } else {
                $merchant_id = $this->Session->read('merchantId');
                $storeID = $this->Store->getAllStoresByMerchantId($merchant_id);
            }
            $searchData = $this->User->find('all', array('fields' => array('User.fname', 'User.lname', 'User.email', 'User.phone'), 'conditions' => array('User.role_id IN' => array(4, 5), 'OR' => array('User.fname LIKE' => '%' . $_GET['term'] . '%', 'User.lname LIKE' => '%' . $_GET['term'] . '%', 'User.email LIKE' => '%' . $_GET['term'] . '%', 'User.phone LIKE' => '%' . $_GET['term'] . '%'), 'User.is_deleted' => 0, 'User.store_id' => $storeID), 'group' => array('User.email')));
            $new_array = array();
            if (!empty($searchData)) {
                foreach ($searchData as $key => $val) {
                    $new_array[] = array('label' => $val['User']['fname'], 'value' => $val['User']['email'], 'desc' => $val['User']['fname'] . " " . $val['User']['lname'] . ',' . $val['User']['email'] . ',' . $val['User']['phone']);
                };
            }
            echo json_encode($new_array);
        } else {
            exit;
        }
    }

}
