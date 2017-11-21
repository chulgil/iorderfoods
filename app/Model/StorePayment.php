<?php App::uses('AppModel','Model');  
    class StorePayment extends AppModel {
      
                
        
         
    /*------------------------------------------------
   Function name:categoryListing()
   Description:To find list of the categories from category table 
   created:3/8/2015
  -----------------------------------------------------*/
    public function findCategotyList($storeId=null,$merchantId=null){
       
       //echo $storeId." ".$merchantId;
        $categoryList =$this->find('all',array('fields'=>array('id','name','store_id','start_time','end_time','imgcat','is_meal','has_topping','is_sizeonly'),'conditions'=>array('Category.store_id'=>$storeId,'Category.is_active'=>1,'Category.is_deleted'=>0),'order' => array('name' => 'asc')));
        //echo "<pre>";print_r($categoryList);die;
        if($categoryList){
            return $categoryList;
         
        }
     }
     

     
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
    
     public function fetchStorePayment($storePaymentId=null){
        $storeResult=$this->find('first',array('conditions'=>array('StorePayment.id'=>$storePaymentId)));
        if($storeResult){
            return $storeResult;
        }else{
            return false;
        }
        
    }
}