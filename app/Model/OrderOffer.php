<?php
App::uses('AppModel', 'Model');

class OrderOffer extends AppModel {
    
    /*------------------------------------------------
        Function name:saveOfferOrder()
        Description:To Save  Order Offer Information
        created:18/8/2015
    -----------------------------------------------------*/	
    public function saveOfferOrder($itemData=null){
        if($itemData){
            if($this->save($itemData)){		    
                return true; //Success
            }else{			
                 return false;// Failure 
            }	       
        }         
    }    
}
