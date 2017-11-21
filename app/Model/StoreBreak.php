<?php

App::uses('AppModel', 'Model');

class StoreBreak extends AppModel { 
    var $name = 'StoreBreak';
     
     
     /*------------------------------------------------
     Function name:saveStoreBreak()
     Description:save Store Break Information
     created:02/9/2015
     -----------------------------------------------------*/
	
    public function saveStoreBreak($BreakData=null){
        if($BreakData){
          if($this->save($BreakData)){		    
                  return true; //Success
             }else{			
                  return false;// Failure 
             }	       
        }         
    }   
     
     
     
        /*------------------------------------------------
     Function name:getStoreNotAvailableInfo()
     Description:To check if email already exists.
     created:27/7/2015
     -----------------------------------------------------*/
	
    public function fetchStoreBreak($storeId=null,$storeAvailabilityId=null){
        $store_break = $this->find('first',array('conditions'=>array('StoreBreak.store_id'=>$storeId,'StoreBreak.store_availablity_id'=>$storeAvailabilityId,'StoreBreak.is_active'=>1,'StoreBreak.is_deleted'=>0)));		
        return $store_break;
    }
     
     
}