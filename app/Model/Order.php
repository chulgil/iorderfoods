<?php

App::uses('AppModel', 'Model');

class Order extends AppModel {

    var $name = 'Order';
    
    /* ------------------------------------------------
      Function name:saveOrder()
      Description:To save order
      created:11/8/2015
      ----------------------------------------------------- */

    public function saveOrder($data = null) {
        if ($data) {
            $res = $this->save($data);
            if ($res) {
                return true;
            } else {
                return false;
            }
        }
    }

    /* ------------------------------------------------
      Function name:getSingleOrderDetail()
      Description:To get the orders details
      created:12/8/2015
      ----------------------------------------------------- */

    public function getSingleOrderDetail($merchantId = null, $storeId = null, $orderId = null) {
        $orderDetail = $this->find('all', array('recursive' => 3, 'conditions' => array('Order.merchant_id' => $merchantId, 'Order.id' => $orderId, 'Order.store_id' => $storeId, 'Order.is_active' => 1, 'Order.is_deleted' => 0)));
        return $orderDetail;
    }

    /* ------------------------------------------------
      Function name:getsuperSingleOrderDetail()
      Description:To get the orders details
      created:12/8/2015
      ----------------------------------------------------- */

    public function getsuperSingleOrderDetail($merchantId = null, $storeId = null, $orderId = null) {
        $orderDetail = $this->find('all', array('recursive' => 3, 'conditions' => array('Order.id' => $orderId, 'Order.is_active' => 1, 'Order.is_deleted' => 0)));
        return $orderDetail;
    }

    /* ------------------------------------------------
      Function name:getOrderDetails()
      Description:To get list of orders
      created:11/8/2015
      ----------------------------------------------------- */

    public function getOrderDetails($decrypt_merchantId = null, $decrypt_storeId = null, $decrypt_userId = null) {
        //$myOrders = $this->find('all',array('order'=>'Order.created DESC','recursive'=>3,'conditions'=>array('Order.merchant_id'=>$decrypt_merchantId,'Order.user_id'=>$decrypt_userId,'Order.store_id'=>$decrypt_storeId,'Order.is_active'=>1,'Order.is_deleted'=>0,'Order.is_future_order'=>0)));

        $myOrders = $this->find('all', array('order' => 'Order.created DESC', 'recursive' => 3, 'conditions' => array('Order.merchant_id' => $decrypt_merchantId, 'Order.user_id' => $decrypt_userId, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0)));
        return $myOrders;
    }

    public function getKitchenOrderDetails($decrypt_storeId = null) {
        $myOrders = $this->find('all', array('limit' => 4, 'order' => 'Order.created ASC', 'recursive' => 3, 'conditions' => array('Order.order_status_id' => 2, 'Order.store_id' => $decrypt_storeId, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0)));
        return $myOrders;
    }

    public function getSavedOrderDetails($decrypt_merchantId = null, $decrypt_storeId = null, $decrypt_userId = null) {
//        $myOrders = $this->find('all',array('order'=>'Order.created DESC','recursive'=>3,'conditions'=>array('Order.merchant_id'=>$decrypt_merchantId,'Order.user_id'=>$decrypt_userId,'Order.store_id'=>$decrypt_storeId,'Order.is_active'=>1,'Order.is_deleted'=>0,'Order.is_future_order'=>1)));
        $myOrders = $this->find('all', array('order' => 'Order.created DESC', 'recursive' => 3, 'conditions' => array('Order.merchant_id' => $decrypt_merchantId, 'Order.user_id' => $decrypt_userId, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.is_future_order' => 1)));
        return $myOrders;
    }

    public function getfirstOrder($merchantId = null, $storeId = null, $orderId = null) {
        $orderDetail = $this->find('first', array('recursive' => 3, 'conditions' => array('Order.merchant_id' => $merchantId, 'Order.id' => $orderId, 'Order.store_id' => $storeId, 'Order.is_active' => 1, 'Order.is_deleted' => 0)));
        return $orderDetail;
    }

    public function getOrderById($orderId = null) {
        $orderDetail = $this->find('first', array('recursive' => 3, 'conditions' => array('Order.id' => $orderId)));
        return $orderDetail;
    }

    /* ------------------------------------------------
      Function name:getUserOrderDetail()
      Description:To get the user all orders details
      created:18/8/2015
      ----------------------------------------------------- */

    public function getUserOrderDetail($merchantId = null, $storeId = null, $userId = null) {
        if ($storeId) {
            $orderDetail = $this->find('all', array('order' => 'Order.created DESC', 'recursive' => 3, 'conditions' => array('Order.merchant_id' => $merchantId, 'Order.user_id' => $userId, 'Order.store_id' => $storeId, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0)));
        } else {
            $orderDetail = $this->find('all', array('order' => 'Order.created DESC', 'recursive' => 3, 'conditions' => array('Order.merchant_id' => $merchantId, 'Order.user_id' => $userId, 'Order.is_deleted' => 0, 'Order.is_future_order' => 0)));
        }

        return $orderDetail;
    }

    public function getTodaysOrder($storeId = null, $todaydate = null) {
        //$todaydate=date('Y-m-d');
        //$ordercount = $this->find('count', array('fields' => array('id'), 'conditions' => array('Order.is_future_order' => 0, 'Order.store_id' => $storeId, 'Order.is_active' => 1, 'DATE(convert_tz(Order.pickup_time,"' . Configure::read('server_offset') . '","' . Configure::read('store_offset') . '"))' => $todaydate)));
        //$ordercount = $this->find('count',array('fields'=>array('id'),'conditions'=>array('Order.is_future_order'=>0,'Order.store_id'=>$storeId,'Order.is_active'=>1,'DATE(Order.created)'=>$todaydate)));
        $ordercount = $this->find('count', array('fields' => array('id'), 'conditions' => array('Order.is_future_order' => 0, 'Order.store_id' => $storeId, 'Order.is_active' => 1, 'DATE(Order.pickup_time)' => $todaydate)));
        return $ordercount;
    }

    //Get pre-Order
    public function getPreOrder($storeId = null, $todaydate = null) {
        //$todaydate=date('Y-m-d');
        //$ordercount = $this->find('count', array('fields' => array('id'), 'conditions' => array('Order.is_future_order' => 0, 'Order.store_id' => $storeId, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'DATE(Order.pickup_time) >' => $todaydate)));
        $ordercount = $this->find('count', array('fields' => array('id'), 'conditions' => array('Order.is_future_order' => 0, 'Order.store_id' => $storeId, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'DATE(Order.pickup_time) >=' => $todaydate, 'Order.is_pre_order' => 1)));
        return $ordercount;
    }

    public function getTodaysPendingOrder($storeId = null, $todaydate = null) {
        //$todaydate=date('Y-m-d');
        //$ordercount = $this->find('count',array('fields'=>array('id'),'conditions'=>array('Order.is_future_order'=>0,'Order.store_id'=>$storeId,'Order.is_active'=>1,'DATE(Order.created)'=>$todaydate ,'Order.order_status_id'=>1)));
        //$ordercount = $this->find('count', array('fields' => array('id'), 'conditions' => array('Order.is_future_order' => 0, 'Order.store_id' => $storeId, 'Order.is_active' => 1, 'DATE(convert_tz(Order.pickup_time,"' . Configure::read('server_offset') . '","' . Configure::read('store_offset') . '"))' => $todaydate, 'Order.order_status_id' => 1)));
        $ordercount = $this->find('count', array('fields' => array('id'), 'conditions' => array('Order.is_future_order' => 0, 'Order.store_id' => $storeId, 'Order.is_active' => 1, 'DATE(Order.pickup_time)' => $todaydate, 'Order.order_status_id' => 1)));
        return $ordercount;
    }

    public function fetchOrderToday($storeId = null, $start = null, $end = null) {
        $result = $this->find('all', array('fields' => array('order_number', 'amount', 'created'), 'conditions' => array('Order.store_id' => $storeId, 'Order.created >=' => $start, 'Order.created <=' => $end, 'Order.is_active' => 1, 'Order.is_deleted' => 0)));
        return $result;
    }

    public function fetchOrder($storeId = null) {
        $result = $this->find('all', array('fields' => array('amount', 'created'), 'conditions' => array('Order.store_id' => $storeId, 'Order.is_active' => 1, 'Order.is_deleted' => 0)));
        return $result;
    }

    public function getconfirmorder($orderID = null) {
        $result = $this->find('first', array('fields' => array('id', 'order_status_id'), 'conditions' => array('Order.id' => $orderID, 'Order.is_active' => 1, 'Order.is_deleted' => 0, 'Order.order_status_id' => 8)));
        return $result;
    }

    public function getOrderInfo($orderId = null) {
        $orderDetail = $this->find('first', array('recursive' => 3, 'conditions' => array('Order.id' => $orderId, 'Order.is_active' => 1, 'Order.is_deleted' => 0)));
        return $orderDetail;
    }

    public function getOrderNumber($storeId) {

        set_time_limit(5);

        do {
            $order_number = sprintf("%06d", mt_rand(1, 999999));
            $ordercount = $this->find('count', array('fields' => array('id'),
                'conditions' => array(
                    'Order.store_id' => $storeId, 'Order.order_number' => $order_number, 'Order.is_active' => 1, 'Order.is_deleted' => 0)));
            pr($ordercount);
        } while ($ordercount = 0);

        return $order_number;
    }

    public function getPrepayOrderDetail($orderSeq = null) {
        $orderDetail = $this->find('all', array('recursive' => 3, 'conditions' => array('Order.id' => $orderSeq, 'Order.is_active' => 1, 'Order.is_deleted' => 0)));
        return $orderDetail;
    }

    public function getStoreOrdernumber($server_offset, $todaydate) {
        $criteria = ' Order.order_number!="" AND Order.is_active= 1 AND Order.is_deleted= 0 AND DATE(convert_tz(Order.created,"' . $server_offset . '","' . $server_offset . '"))="' . $todaydate . '"';
        $count = $this->find('count', array('fields' => array('id'), 'conditions' => array($criteria)));
        return $count;
    }

    public function checkorderNumber($ordernumber) {
        $ordernumber = $this->find('first', array('fields' => array('id'), 'conditions' => array('Order.order_number' => $ordernumber, 'Order.is_active' => 1, 'Order.is_deleted' => 0)));
        return $ordernumber;
    }

}
