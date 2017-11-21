<?php

App::uses('StoreAppController', 'Controller');

class ShareSocialsController extends StoreAppController {

    var $helpers = array('Html', 'Form', 'Cache');
    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler');

    public function index() {
        $this->redirect(array('controller' => 'users', 'action' => 'login'));
    }

    public function itemShare($itemID = null) {
        $this->layout = "";
        //$this->autoRender=false;
        $this->loadModel('Store');
        $this->loadModel('User');
        $this->loadModel('Item');

        if ($itemID) {

            $this->Item->bindModel(
                    array(
                        'belongsTo' => array(
                            'Store' => array(
                                'className' => 'Store',
                                'foreignKey' => 'store_id',
                                'type' => 'INNER',
                                'fields' => array('id', 'store_name', 'store_url'),)
                        )
                    )
            );
            $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme', 'StoreFont'), 'hasMany' => array('StoreGallery', 'StoreContent')));
            $itemDetails = $this->Item->getItemById($itemID);
            $this->set(compact('itemDetails'));
        }
    }

    public function orderShare($orderID = null) {
        $this->layout = "";
        $this->autoRender = false;
        $this->loadModel('Store');
        $this->loadModel('User');
        $this->loadModel('Item');
        $this->loadModel('Order');
        $this->loadModel('OrderItem');

        if ($orderID) {

            $this->OrderItem->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name')))), false);

            $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'order_id', 'user_id', 'type_id', 'item_id', 'size_id')))), false);


            $orderDetails = $this->Order->getOrderById($orderID);
            pr($orderDetails);
            die;
            //$this->set(compact('itemDetails'));
        }
    }

}
