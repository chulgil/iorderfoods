<?php App::uses('AppModel','Model');  
    class BookingStatus extends AppModel {
  
  
   /*------------------------------------------------
   Function name:BookingStatusList()
   Description:To find list of the booking status from bookingstatus table 
   created:17/08/2015
  -----------------------------------------------------*/
    public function statusList($storeId=null){
        $statusList =$this->find('list',array('fields'=>array('id','name'),'conditions'=>array('BookingStatus.is_active'=>1,'BookingStatus.is_deleted'=>0)));     
        if($statusList){
            return $statusList;
         
        }
     }
     
   
}