<?php

App::uses('AppModel', 'Model');


class OrderItem extends AppModel {
    var $name = 'OrderItem';
    public function saveOrderItem($data=null){
        if($data){
            $res=$this->save($data);
            if($res){
                return true ;
            }else{
                return false;
            }
        } 
        
    }
    
    public function getItemInfo($itemId=null,$userid=null,$startDate=null,$endDate=null){
        $date[]=$startDate;
        $date[]=$endDate;
        $offer = $this->find('all',array('fields'=>array('SUM(quantity) as total'),'conditions'=>array('OrderItem.is_deleted'=>0,'OrderItem.is_future'=>0,'OrderItem.item_id'=>$itemId,'OrderItem.user_id'=>$userid,'OrderItem.created BETWEEN ? and ? '=>$date)));
        return $offer;
        
    }
    
}