<?php

App::uses('AppModel','Model');  
    class MerchantStoreRequest extends AppModel {
        var $name = 'MerchantStoreRequest';
        
        
        /*------------------------------------------------
        Function name:getTabs()
        Description:To find Permission Tabs
        created:3/8/2015
       -----------------------------------------------------*/
        public function getRequestedStoreList(){
            $merchantID=$this->Session->read('Auth.Admin.merchant_id');
            $Tabs =$this->find('all',array('conditions'=>array('MerchantStoreRequest.is_deleted'=>0,'MerchantStoreRequest.merchant_id'=>$merchantID)));
            if($Tabs){
                return $Tabs;         
            }
        }
        
        /*------------------------------------------------
        Function name:saveItem()
        Description:To Save Item Information
        created:04/8/2015
        -----------------------------------------------------*/	
       public function saveStoreRequest($storeData=null){
             if($storeData){
               if($this->save($storeData)){		    
                       return true; //Success
                  }else{			
                       return false;// Failure 
                  }	       
             }         
       }
        
       
       public function checkAlreadyRequested($storeName=null,$merchantId=null){
            $conditions = array('LOWER(MerchantStoreRequest.store_name)'=>strtolower($storeName),'MerchantStoreRequest.merchant_id'=>$merchantId,'MerchantStoreRequest.is_deleted'=>0);        
            $item =$this->find('first',array('fields'=>array('id'),'conditions'=>$conditions));            
            if($item){
                return 0;
            }else{
                return 1;
            }
        }
       
}