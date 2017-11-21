<?php

App::uses('StoreAppController', 'Controller');

class KitchensController extends StoreAppController {

    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Encryption', 'Dateform', 'Common');
    public $helper = array('Encryption', 'Dateform', 'Common');
    public $uses = array('OrderOffer', 'Order', 'Item', 'ItemPrice', 'ItemType', 'Size', 'OrderItem', 'StoreReview', 'Favorite', 'Topping', 'OrderTopping', 'OrderPreference');

    public function beforeFilter() {
        // echo Router::url( $this->here, true );die;
        parent::beforeFilter();
        $adminfunctions = array('getOrderListData', 'index', 'UpdateOrderStatus', 'orderDetail', 'listView', 'getOrderData');
        if (in_array($this->params['action'], $adminfunctions)) {
            if (!$this->Common->checkPermissionByaction($this->params['controller'])) {
                $this->Session->setFlash(__("Permission Denied"));
                $this->redirect(array('controller' => 'Stores', 'action' => 'dashboard'));
            }
        }
    }

    /* ------------------------------------------------
      Function name:index()
      Description:List order item that is not served to customer
      created:17/8/2015
      ----------------------------------------------------- */

    public function index() {
        $this->layout = "admin_dashboard";
        Configure::write('debug', 0);
    }

    public function getOrderData() {
        $this->layout = false;
        Configure::write('debug', 0);
        if ($_POST['limit'] <= 8) {
            $limit = $_POST['limit'];
            //$limit = 6;
        } else {
            $limit = 5;
            //$limit = 6;
        }
        $current_date = date("Y-m-d", (strtotime($this->Common->storeTimezone('', date('Y-m-d H:i:s')))));

        $decrypt_storeId = $this->Session->read('admin_store_id');
        $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);
        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('fields' => array('name')), 'AddonSize' => array('fields' => array('size')))), false);
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'offered_size_id', 'fields' => array('size')))), false);
        $this->OrderItem->bindModel(array('hasMany' => array('OrderOffer' => array('fields' => array('offered_item_id', 'quantity')), 'OrderTopping' => array('fields' => array('id', 'topping_id', 'addon_size_id')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name', 'id')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name', 'id')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size', 'id')))), false);
        $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'type_id', 'item_id', 'size_id'))), 'belongsTo' => array('Segment' => array('className' => 'Segment', 'foreignKey' => 'seqment_id'), 'OrderPayment' => array('className' => 'OrderPayment', 'foreignKey' => 'payment_id', 'fields' => array('id', 'transection_id', 'amount', 'payment_gateway')))), false);
        $myOrders = $this->Order->find('all', array('recursive' => 3, 'conditions' => array('NOT' => array("Order.order_status_id" => array(5, 7, 9)), 'Order.store_id' => $decrypt_storeId, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0, 'Date(Order.pickup_time)' => $current_date), 'limit' => $limit, 'order' => array('Order.pickup_time' => 'ASC')));
        $this->set(compact('myOrders'));
    }

    public function listView($clearAction = null) {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $current_date = date("Y-m-d", (strtotime($this->Common->storeTimezone('', date('Y-m-d H:i:s')))));
        $criteria = "Order.store_id = $storeID AND Order.is_active=1 AND Order.is_deleted=0 AND Order.is_future_order=0 AND Order.order_status_id=2";
        $this->OrderItem->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name')))), false);
        $this->Order->bindModel(array('belongsTo' => array('Segment' => array('className' => 'Segment', 'foreignKey' => 'seqment_id'), 'OrderPayment' => array('className' => 'OrderPayment', 'foreignKey' => 'payment_id', 'fields' => array('id', 'transection_id', 'amount', 'payment_gateway'))), 'hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'item_id')))), false);
        $orderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array('NOT' => array("Order.order_status_id" => array(5, 7, 9)), 'Order.store_id' => $storeID, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0, 'Date(Order.pickup_time)' => $current_date), 'limit' => 10, 'order' => array('Order.pickup_time' => 'ASC')));
        $this->set('list', $orderdetail);
    }

    public function getOrderListData() {

        $this->layout = false;
        $storeID = $this->Session->read('admin_store_id');
        $current_date = date("Y-m-d", (strtotime($this->Common->storeTimezone('', date('Y-m-d H:i:s')))));
        $this->OrderItem->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('name')))), false);
        $this->Order->bindModel(array('belongsTo' => array('Segment' => array('className' => 'Segment', 'foreignKey' => 'seqment_id'), 'OrderPayment' => array('className' => 'OrderPayment', 'foreignKey' => 'payment_id', 'fields' => array('id', 'transection_id', 'amount', 'payment_gateway'))), 'hasMany' => array('OrderItem' => array('fields' => array('id1', 'quantity', 'order_id', 'item_id')))), false);
        $orderdetail = $this->Order->find('all', array('recursive' => 2, 'conditions' => array('NOT' => array("Order.order_status_id" => array(5, 7, 9)), 'Order.store_id' => $storeID, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0, 'Date(Order.pickup_time)' => $current_date), 'limit' => 10, 'order' => array('Order.pickup_time' => 'ASC')));
        $this->set('list', $orderdetail);
    }

    /* ------------------------------------------------
      Function name: UpdateOrderStatus()
      Description: Update the order status
      created:17/8/2015
      ----------------------------------------------------- */

    public function UpdateOrderStatus() {
        $this->autoRender = false;
        $this->layout = "admin_dashboard";
        if (!empty($this->request->data['Order']['id'])) {
            // pr($this->request->data);die;
            $filter_array = array_filter($this->request->data['Order']['id']);
            foreach ($filter_array as $k => $orderId) {
                $this->Order->id = $orderId;
                $this->Order->saveField("order_status_id", $this->request->data['Order']['order_status_id']);
            }
        }
        if (!empty($this->request->data['Orders']['id'])) {
            $this->Order->id = $this->request->data['Orders']['id'];
            $this->Order->saveField("order_status_id", $this->request->data['Order']['order_status_id']);
        }
        $this->Session->setFlash(__("Order status updated successfully."), 'alert_success');
        $this->redirect(array('action' => 'index', 'controller' => 'kitchens'));
    }

    /* ------------------------------------------------
      Function name: orderDetail()
      Description: Dispaly the detail of perticular order
      created:12/8/2015
      ----------------------------------------------------- */

    public function orderDetail($order_id = null) {
        $this->layout = "admin_dashboard";
        $storeID = $this->Session->read('admin_store_id');
        $merchantId = $this->Session->read('admin_merchant_id');
        $orderId = $this->Encryption->decode($order_id);
        $this->loadModel('OrderItemFree');
        $this->loadModel('Item');
        // $this->OrderItem->bindModel(array('belongsTo'=>array('Item'=>array('className' => 'Item','foreignKey'=>'item_id','fields'=>array('id','name')))), false);
        $this->OrderItemFree->bindModel(array('belongsTo' => array('Item' => array('fields' =>
                    array('id', 'name')))), false);
        $this->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name')))), false);
        $this->Item->bindModel(array('belongsTo' => array('category' => array('fields' =>
                    array('id', 'name')))), false);
        $this->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('className' => 'Item', 'foreignKey' => 'offered_item_id', 'fields' => array('id', 'name')), 'Size' => array('className' => 'Size', 'foreignKey' => 'offered_size_id', 'fields' => array('id', 'size')))), false);
        $this->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('className' => 'Topping', 'foreignKey' => 'topping_id', 'fields' => array('id', 'name')))), false);
        $this->OrderItem->bindModel(array('hasMany' => array('OrderTopping' => array('fields' => array('id', 'topping_id', 'addon_size_id')), 'OrderOffer' => array('fields' => array('id', 'offered_item_id', 'offered_size_id', 'quantity')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id', 'size'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name','category_id')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('id', 'name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('id', 'size')))), false);
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
                    'fields' => array('id', 'transection_id', 'amount', 'payment_gateway', 'last_digit'),
                ))), false);
        $orderDetails = $this->Order->getSingleOrderDetail($merchantId, $storeID, $orderId);
        $this->set('orderDetail', $orderDetails);
        $this->loadModel('OrderStatus');
        $statusList = $this->OrderStatus->OrderStatusList($storeID);
        $this->set('statusList', $statusList);
        $printerIP = $this->Store->fetchStorePrinterIP($storeID);
        $this->set('printerIP', $printerIP['Store']['printer_location']);
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

}
