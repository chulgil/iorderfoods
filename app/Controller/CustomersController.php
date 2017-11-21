<?php

App::uses('StoreAppController', 'Controller');

class CustomersController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'Common', 'Dateform');
    public $uses = 'User';

    public function beforeFilter() {
        parent::beforeFilter();
        $adminfunctions = array('index', 'activateCustomer', 'deleteCustomer', 'editCustomer', 'orderHistory', 'ajaxRequest');
        if (in_array($this->params['action'], $adminfunctions) && !$this->Common->checkPermissionByaction($this->params['controller'])) {
            $this->Session->setFlash(__("Permission Denied"));
            $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
        }
    }

    /* ------------------------------------------------
      Function name:index()
      Description:Display the list of Customer
      created:10/8/2015
      ----------------------------------------------------- */

    public function index($clearAction = null) {
        $this->layout = "admin_dashboard";
        $merchantId = $this->Session->read('admin_merchant_id');
        $value = "";
        $criteria = "User.merchant_id =$merchantId AND User.role_id IN (4,5) AND User.is_deleted=0";
        if ($this->Session->read('CustomerSearchData') && $clearAction != 'clear' && !$this->request->is('post')) {
            $this->request->data = json_decode($this->Session->read('CustomerSearchData'), true);
        } else {
            $this->Session->delete('CustomerSearchData');
            if (isset($this->params->pass[0]) && !empty($this->params->pass[0])) {
                if ($this->params->pass[0] == 'clear') {
                    $this->redirect($this->referer());
                }
            }
        }
        if (!empty($this->request->data)) {
            $this->Session->write('CustomerSearchData', json_encode($this->request->data));
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
      Function name:activateCustomer()
      Description:Active/deactive Customer
      created:10/8/2015
      ----------------------------------------------------- */

    public function activateCustomer($EncryptCustomerID = null, $status = 0) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['User']['store_id'] = $this->Session->read('admin_store_id');
        $data['User']['id'] = $this->Encryption->decode($EncryptCustomerID);
        $data['User']['is_active'] = $status;
        if ($this->User->saveUserInfo($data)) {
            if ($status) {
                $SuccessMsg = "User Activated";
            } else {
                $SuccessMsg = "User Deactivated";
            }
            $this->Session->setFlash(__($SuccessMsg), 'alert_success');
            $this->redirect($this->referer());
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect($this->referer());
        }
    }

    /* ------------------------------------------------
      Function name:deleteCustomer()
      Description:Delete Customer
      created:10/8/2015
      ----------------------------------------------------- */

    public function deleteCustomer($EncryptCustomerID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['User']['store_id'] = $this->Session->read('admin_store_id');
        $data['User']['id'] = $this->Encryption->decode($EncryptCustomerID);
        $data['User']['is_deleted'] = 1;
        if ($this->User->saveUserInfo($data)) {
            $this->Session->setFlash(__("User deleted"), 'alert_success');
            $this->redirect(array('controller' => 'customers', 'action' => 'index'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'customers', 'action' => 'index'));
        }
    }

    /* ------------------------------------------------
      Function name:editCustomer()
      Description:Registration  Form for the  End customer
      created:10/8/2015
      ----------------------------------------------------- */

    public function editCustomer($EncryptCustomerID = null) {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $data['User']['id'] = $this->Encryption->decode($EncryptCustomerID);
        $this->loadModel('User');
        $this->loadModel('DeliveryAddress');
        $customerDetail = $this->User->currentUserInfo($data['User']['id']);
        $addressDetail = $this->DeliveryAddress->fetchAllAddress($data['User']['id']);
        $defaultAddress = $this->DeliveryAddress->fetchDefaultAddress($data['User']['id']);
        $this->set(compact('addressDetail', 'defaultAddress'));
        if ($this->request->is('post')) {
            $this->request->data = $this->Common->trimValue($this->request->data);
            $email = trim($this->data['User']['email']);
            $isUniqueEmail = $this->User->checkUserUniqueEmail($email, $storeId, $data['User']['id'], 4);
            if ($isUniqueEmail) {
                $storeId = "";
                $merchantId = "";
                $storeId = $this->Session->read('admin_store_id'); // It will read from session when a customer will try to register on store
                $merchantId = $this->Session->read('admin_merchant_id');
                $email = trim($this->request->data['User']['email']); //Here username is email
                $this->request->data['User']['store_id'] = $storeId; // Store Id
                $this->request->data['User']['merchant_id'] = $merchantId; // Merchant Id
                $userName = trim($this->request->data['User']['email']); //Here username is email
                $this->request->data['User']['username'] = trim($userName);
                $actualDbDate = $this->Dateform->formatDate($this->request->data['User']['dateOfBirth']); // calling formatDate function in Appcontroller to format the date (Y-m-d) format
                $this->request->data['User']['dateOfBirth'] = $actualDbDate;
                //prx($this->request->data);
                $this->User->saveUserInfo($this->Common->trimValue($this->request->data));   // We are calling function written on Model to save data
                $this->Session->setFlash(__('Customer details updated successfully'), 'alert_success');
                $this->redirect(array('controller' => 'customers', 'action' => 'index'));
            } else {
                $this->Session->setFlash(__("Email  Already exists"), 'alert_failed');
            }
        }
        $this->set(compact('addressDetail'));
        $this->request->data = $customerDetail;
    }

    /* ------------------------------------------------
      Function name:orderHistory()
      Description:Display the customer all orders
      created:18/8/2015
      ----------------------------------------------------- */

    public function orderHistory($EncryptCustomerID = null) {
        $this->layout = "admin_dashboard";
        $userId = $this->Encryption->decode($EncryptCustomerID);
        $userDetail = $this->User->findById($userId, array('fname', 'lname', 'email', 'phone'));
        $this->set(compact('userDetail', 'EncryptCustomerID'));
    }

    /* ------------------------------------------------
      Function name:ajaxRequest()
      Description:Get Order Status
      created:18/8/2015
      ----------------------------------------------------- */

    public function ajaxRequest($id = '') {
        $this->autoRender = false;
        $this->loadModel('OrderStatus');
        $this->layout = "admin_dashboard";
        if (!empty($this->request->params['requested'])) {
            $data = $this->OrderStatus->find('first', array('conditions' => array('OrderStatus.id' => $id)));
            echo $data['OrderStatus']['name'];
        }
    }

    /* ------------------------------------------------
      Function name:orderDetail()
      Description:Get Order Detail
      created:18/8/2015
      ----------------------------------------------------- */

    public function orderDetail() {
        $this->layout = "admin_dashboard";
        $merchantId = $this->Session->read('admin_merchant_id');
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
        $fields = array('Order.store_id', 'Order.created', 'Order.id', 'Order.order_number', 'Order.amount', 'Order.user_id', 'OrderStatus.name', 'Segment.name','Order.coupon_discount');
        $this->paginate = array('order' => array('Order.created'=>'DESC'), 'fields' => @$fields, 'conditions' => array('Order.merchant_id' => $merchantId, 'Order.user_id' => $userId, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0));
        $orderDetails = $this->paginate('Order');
        $this->set(compact('orderDetails', 'EncryptCustomerID'));
    }

    /* ------------------------------------------------
      Function name:reviewDetail()
      Description:Get Review Detail
      created:18/8/2015
      ----------------------------------------------------- */

    public function reviewDetail() {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $EncryptCustomerID = $_GET['cId'];
        $userId = $this->Encryption->decode($EncryptCustomerID);
        $this->loadModel('StoreReview');
        $this->loadModel('OrderItem');
        $this->OrderItem->bindModel(array('belongsTo' => array('Item' => array('className' => 'Item', 'fields' => array('name')))), false);
        $this->StoreReview->bindModel(array('belongsTo' => array('OrderItem' => array('className' => 'OrderItem', 'fields' => array('id', 'order_id', 'item_id')))), false);
        $this->paginate = array('fields' => array('StoreReview.created', 'StoreReview.store_id', 'StoreReview.review_comment', 'StoreReview.review_rating', 'StoreReview.order_id', 'StoreReview.id', 'StoreReview.order_item_id'), 'recursive' => 2, 'conditions' => array('StoreReview.is_deleted' => 0, 'StoreReview.user_id' => $userId, 'StoreReview.store_id' => $storeId, 'StoreReview.merchant_id' => $merchantId));
        $myReviews = $this->paginate('StoreReview');
        $this->set(compact('orderDetails', 'EncryptCustomerID', 'myReviews'));
    }

    /* ------------------------------------------------
      Function name:reservationDetail()
      Description:Get reservationDetail Detail
      created:18/8/2015
      ----------------------------------------------------- */

    public function reservationDetail() {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $EncryptCustomerID = $_GET['cId'];
        $userId = $this->Encryption->decode($EncryptCustomerID);
        $this->loadModel('Booking');
        $this->Booking->bindModel(array('belongsTo' => array('BookingStatus')), false);
        $this->paginate = array('fields' => array('Booking.store_id', 'BookingStatus.name', 'Booking.special_request', 'Booking.id', 'Booking.number_person', 'Booking.reservation_date'), 'conditions' => array('Booking.user_id' => $userId, 'Booking.is_active' => 1, 'Booking.is_deleted' => 0, 'Booking.store_id' => $storeId));
        $myBookings = $this->paginate('Booking');
        $this->set(compact('orderDetails', 'EncryptCustomerID', 'myBookings'));
    }

    /* ------------------------------------------------
      Function name:customerOrderDetail()
      Description:Get customer Detail
      created:18/8/2015
      ----------------------------------------------------- */

    public function customerOrderDetail($order_id = null) {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $orderId = $this->Encryption->decode($order_id);
        $this->loadModel('OrderItemFree');
        $this->loadModel('OrderPreference');
        $this->loadModel('OrderOffer');
        $this->loadModel('OrderTopping');
        $this->loadModel('OrderItem');
        $this->loadModel('Order');
        $this->loadModel('Item');
        $this->OrderItemFree->bindModel(array('belongsTo' => array('Item' => array('fields' =>
                    array('id', 'name', 'category_id')))), false);
        $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);
        $this->Item->bindModel(array('belongsTo' => array('category' => array('fields' =>
                    array('id', 'name')))), false);


        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('className' => 'Item', 'foreignKey' => 'offered_item_id', 'fields' => array('id', 'name')), 'Size' => array('className' => 'Size', 'foreignKey' => 'offered_size_id', 'fields' => array('id', 'size')))), false);
        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('className' => 'Topping', 'foreignKey' => 'topping_id', 'fields' => array('id', 'name')))), false);
        $this->OrderItem->bindModel(array('hasMany' => array('OrderTopping' => array('fields' => array('id', 'topping_id', 'addon_size_id'), 'order' => array('OrderTopping.id')), 'OrderOffer' => array('fields' => array('id', 'offered_item_id', 'offered_size_id', 'quantity')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id', 'size'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name', 'category_id')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('id', 'name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('id', 'size')))), false);
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
        $orderDetails = $this->Order->getSingleOrderDetail($merchantId, $storeId, $orderId);
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
            $storeID = $this->Session->read('admin_store_id');
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

    public function contact_us() {
        $this->layout = false;
        $this->autoRender = false;
        if ($this->request->is('post') && !empty($_POST['data']['_Token']['key'])) {
            //if (1) {
            $storeId = $this->Session->read('store_id');
            $merchantId = $this->Session->read('merchant_id');
//            $this->request->data['StoreInquiries']['name'] = 'Vikas1';
//            $this->request->data['StoreInquiries']['email'] = 'hello1@yopmail.com';
//            $this->request->data['StoreInquiries']['message'] = 'This is test1.';
//            //$this->request->data['StoreInquiries']['phone'] = '9808117322';
            $this->request->data['StoreInquiries']['store_id'] = $storeId;
            $this->request->data['StoreInquiries']['merchant_id'] = $merchantId;
            $this->loadModel('StoreInquiries');
            if ($this->StoreInquiries->save($this->request->data)) {
                $template_type = 'enquiry_to_store_admin';
                $this->loadModel('DefaultTemplate');
                $emailSuccess = $this->DefaultTemplate->adminTemplates($template_type);
                if ($emailSuccess) {
                    $this->Store->recursive = -1;
                    $store = $this->Store->fetchStoreDetail($storeId);
                    if (($store ['Store'] ['notification_type'] == 1 || $store['Store']['notification_type'] == 3) && (!empty($store['Store']['notification_email']))) {
                        $emailData = $emailSuccess['DefaultTemplate']['template_message'];
                        $emailData = str_replace('{STORE_NAME}', $store['Store']['store_name'], $emailData);
                        $subject = ucwords(str_replace('_', ' ', $emailSuccess['DefaultTemplate']['template_subject']));
                        $this->Email->to = $store['Store']['notification_email'];
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
                        $this->Email->sendAs = 'html';
                        try {
                            if ($this->Email->send()) {
                                $this->Session->setFlash(__('Your request has been submitted. Thank you!'));
                            }
                        } catch (Exception $e) {
                            $this->Session->setFlash(__('Email not send.'));
                        }
                    }
                }
            }
        }
        //prx($this->Session->read('Message'));
        $this->redirect($this->referer());
    }

    public function customerInquiries() {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $criteria = "StoreInquiries.store_id =$storeID AND StoreInquiries.is_deleted=0";
        $this->loadModel('StoreInquiries');
        $this->paginate = array('conditions' => array($criteria), 'order' => array('StoreInquiries.created' => 'DESC'));
        $storeInquiryDetail = $this->paginate('StoreInquiries');
        $this->set('list', $storeInquiryDetail);
    }

    public function replyCustomerInquiry($EncryptInquiryID = null) {
        $this->layout = "admin_dashboard";
        $storeId = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $storeInquiryId = $this->Encryption->decode($EncryptInquiryID);
        $this->loadModel('StoreInquiries');
        $storeInquiryDetail = $this->StoreInquiries->findById($storeInquiryId);
        if ($this->request->is(array('post', 'put'))) {
            $template_type = 'reply_admin_to_customer';
            $this->loadModel('EmailTemplate');
            $emailSuccess = $this->EmailTemplate->storeTemplates($storeId, $merchantId, $template_type);
            if ($emailSuccess) {
                $this->Store->recursive = -1;
                $store = $this->Store->fetchStoreDetail($storeId);
                if (($store ['Store'] ['notification_type'] == 1 || $store['Store']['notification_type'] == 3) && (!empty($store['Store']['notification_email']))) {
                    $emailData = $emailSuccess['EmailTemplate']['template_message'];
                    $emailData = str_replace('{FULL_NAME}', $storeInquiryDetail['StoreInquiries']['name'], $emailData);
                    $emailData = str_replace('{MESSAGE}', $this->request->data['StoreInquiries']['admin_message'], $emailData);
                    $url = "http://" . $store['Store']['store_url'];
                    $storeUrl = "<a href=" . $url . " target='_blank'>" . "www." . $store['Store']['store_url'] . "</a>";
                    $emailData = str_replace('{STORE_URL}', $storeUrl, $emailData);
                    $emailData = str_replace('{STORE_PHONE}', $store['Store']['phone'], $emailData);
                    $emailData = str_replace('{STORE_NAME}', $store['Store']['store_name'], $emailData);
                    $storeAddress = $store['Store']['address'] . "<br>" . $store['Store']['city'] . ", " . $store['Store']['state'] . " " . $store['Store']['zipcode'];
                    $storePhone = $store['Store']['phone'];
                    $emailData = str_replace('{STORE_ADDRESS}', $storeAddress, $emailData);
                    $emailData = str_replace('{STORE_PHONE}', $storePhone, $emailData);
                    $subject = ucwords(str_replace('_', ' ', $emailSuccess['EmailTemplate']['template_subject']));
                    $this->Email->to = $storeInquiryDetail['StoreInquiries']['email'];
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
                    $this->Email->sendAs = 'html';
                    try {
                        if ($this->Email->send()) {
                            $this->StoreInquiries->updateAll(array('reply_flag' => 1), array('id' => $storeInquiryId));
                            $this->Session->setFlash(__('Message send successfully.'), 'flash_success');
                        }
                    } catch (Exception $e) {
                        $this->Session->setFlash(__('Email not send.'), 'flash_error');
                    }
                    $this->redirect(array('controllers'=>'customers','action'=>'customerInquiries'));
                }
            }
        }
        $this->request->data = $storeInquiryDetail;
    }

    public function deleteCustomerInquiry($EncryptInquiryID = null) {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        $data['StoreInquiries']['store_id'] = $this->Session->read('admin_store_id');
        $data['StoreInquiries']['id'] = $this->Encryption->decode($EncryptInquiryID);
        $data['StoreInquiries']['is_deleted'] = 1;
        $this->loadModel('StoreInquiries');
        if ($this->StoreInquiries->save($data)) {
            $this->Session->setFlash(__("Enquiry deleted"), 'alert_success');
            $this->redirect(array('controller' => 'customers', 'action' => 'customerInquiries'));
        } else {
            $this->Session->setFlash(__("Some problem occured"), 'alert_failed');
            $this->redirect(array('controller' => 'customers', 'action' => 'customerInquiries'));
        }
    }

}
