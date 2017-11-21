<?php App::uses('AppModel','Model');  
class OrderItemFree extends AppModel {
    var $name = 'OrderItemFree';    
    
    
    /*------------------------------------------------
     Function name:save()
     Description:To Save Free Item Information
     created:09/02/2016
     -----------------------------------------------------*/	
    public function saveItemFree($ItemFreeData=null){
          if($ItemFreeData){
            if($this->save($ItemFreeData)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
        
        
        
        
}