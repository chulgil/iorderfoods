<?php App::uses('AppModel','Model');  
    class OrderStatus extends AppModel {
      
                
        
         
    
     /*------------------------------------------------
   Function name:OrderStatusList()
   Description:To find list of the order status from orderstatus table 
   created:11/8/2015
  -----------------------------------------------------*/
    public function OrderStatusList($storeId=null){
        $statusList =$this->find('list',array('fields'=>array('id','name'),'conditions'=>array('OrderStatus.is_active'=>1,'OrderStatus.is_deleted'=>0)));     
        if($statusList){
            return $statusList;
         
        }
     }
     
     
     public function getStatus($ordertype=null){
        $statusList =$this->find('all',array('fields'=>array('id','name','order_type'),'conditions'=>array('OrderStatus.is_active'=>1,'OrderStatus.is_deleted'=>0,'OrderStatus.order_type'=>$ordertype)));     
        if($statusList){
            return $statusList;
         
        }
     }
}