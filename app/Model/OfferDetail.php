<?php App::uses('AppModel','Model');  
    class OfferDetail extends AppModel {
        var $name = 'OfferDetail';
        
        
        /*------------------------------------------------
     Function name:saveItem()
     Description:To Save Item Information
     created:04/8/2015
     -----------------------------------------------------*/	
    public function saveOfferDetail($offerData=null){
          if($offerData){
            if($this->save($offerData)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
        
     /*------------------------------------------------
     Function name:deleteallOfferItems()
     Description:To delete all default item by Item id
     created:04/8/2015
     -----------------------------------------------------*/	
    public function deleteallOfferItems($Offerid=null){
          if($Offerid){            
            $condition['OfferDetail.offer_id']=$Offerid; 
            if($this->updateAll(array('OfferDetail.is_deleted'=>1),$condition)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
    
      
}