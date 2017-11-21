<?php

App::uses('AppModel', 'Model');

class StoreTip extends AppModel { 
     var $name = 'StoreTip';     
     
     /*------------------------------------------------
     Function name:saveStoreTip()
     Description:Save Store tip Information
     created:09/10/2017
     -----------------------------------------------------*/
	
     public function saveStoreTip($tipData=null){
        if($tipData){
            if($this->save($tipData)){		    
                return true; //Success
            }else{			
                return false;// Failure 
            }	       
        }
     }
     
     /*------------------------------------------------
     Function name:storeTipInfo()
     Description:To get all tip data by store id
     created:09/10/2017
     -----------------------------------------------------*/
	
     public function storeTipInfo($storeId = null){
        if($storeId){
            $tipInfo = $this->find('all', array('conditions' => array('StoreTip.store_id' => $storeId, 'StoreTip.is_deleted' => 0, 'StoreTip.is_active' => 1)));		
                return $tipInfo;
        }     
     }
     
     /*------------------------------------------------
     Function name:storeTipFront()
     Description:To get tip data for front order details by store id
     created:09/10/2017
     -----------------------------------------------------*/
	
     public function storeTipFront($storeId = null){
        if($storeId){
            $tipInfo = $this->find('all', array('fields' => array('id', 'tip_name', 'tip_value'), 'conditions' => array('StoreTip.store_id' => $storeId, 'StoreTip.is_checked' => 1, 'StoreTip.is_deleted' => 0, 'StoreTip.is_active' => 1)));		
                return $tipInfo;
        }     
     }
     
      /*------------------------------------------------
     Function name:storeTipsBytipId()
     Description:To get tip data
     created:09/10/2017
     -----------------------------------------------------*/
	
     public function storeTipsBytipId($tipId=null){
        if($tipId){
                $tipInfo = $this->find('first',array('fields'=>array('id','tip_name','tip_value'),'conditions'=>array('StoreTip.id'=>$tipId,'StoreTip.is_deleted'=>0,'StoreTip.is_active'=>1)));		
                return $tipInfo;
        }     
     }
     
     
      /*------------------------------------------------
     Function name:storeTipsBytipvalue()
     Description:To get tip ID
     created:09/10/2017
     -----------------------------------------------------*/
	
     public function storeTipsBytipvalue($tipvalue=null,$storeId=null){
        if($tipvalue){
                $tipInfo = $this->find('first',array('fields'=>array('id'),'conditions'=>array('LOWER(StoreTip.tip_name)'=>strtolower($tipvalue),'StoreTip.is_deleted'=>0,'StoreTip.is_active'=>1,'StoreTip.store_id'=>$storeId)));		
                return $tipInfo;
        }     
     } 
    
}