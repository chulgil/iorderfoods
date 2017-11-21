<?php

App::uses('AppModel', 'Model');


class MobileOrder extends AppModel {
    
    var $name = 'MobileOrder';
     
    public function getTempOrder($tempOrderId=null){
        $orderDetail = $this->find('first',array('fields'=>array('order_details'),'conditions'=>array('MobileOrder.id'=>$tempOrderId)));
        return $orderDetail;
    }
    
    
     
}