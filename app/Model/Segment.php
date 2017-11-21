<?php App::uses('AppModel','Model');  
    class Segment extends AppModel {
      
                
        
         
    
     /*------------------------------------------------
   Function name:OrderTypeList()
   Description:To find list of the order type from segments table 
   created:11/8/2015
  -----------------------------------------------------*/
    public function OrderTypeList($storeId=null){
        $typeList =$this->find('list',array('fields'=>array('id','name'),'conditions'=>array('Segment.is_active'=>1,'Segment.is_deleted'=>0,'Segment.id !=1')));     
        if($typeList){
            return $typeList;
         
        }
     }
}