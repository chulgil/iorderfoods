<?php App::uses('AppModel','Model');  
    class MerchantPayment extends AppModel {
      
                
   
     
     /*------------------------------------------------
     Function name:savePayment()
     Description:To Save store payment
     created:02/9/2015
     -----------------------------------------------------*/	
    public function savePayment($paymentData=null){
          if($paymentData){
            if($this->save($paymentData)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
    
    public function fetchMerchantPayment($merchantPaymentId=null){
        $storeResult=$this->find('first',array('conditions'=>array('MerchantPayment.id'=>$merchantPaymentId)));
        if($storeResult){
            return $storeResult;
        }else{
            return false;
        }
        
    }
    
}