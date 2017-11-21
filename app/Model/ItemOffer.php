<?php App::uses('AppModel','Model');  
class ItemOffer extends AppModel {
    
    var $name = 'ItemOffer';
    
   
    
    /*------------------------------------------------
     Function name:saveItemOffer()
     Description:To Save Item Offer Information
     created:29/02/2016
     -----------------------------------------------------*/	
    public function saveItemOffer($ItemOfferData=null){
          if($ItemOfferData){
            if($this->save($ItemOfferData)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    } 
    
    
    
    /*------------------------------------------------
     Function name:checkUniqueOffer()
     Description:To Check UNique Offer on Item
     created:29/02/2016
     -----------------------------------------------------*/	
    public function checkUniqueOffer($Itemid=null,$storeId=null,$itemOfferID=null){
            $conditions = array('ItemOffer.item_id'=>$Itemid,'ItemOffer.store_id'=>$storeId,'ItemOffer.is_deleted'=>0,'ItemOffer.is_active'=>1);
            if($itemOfferID){
                $conditions['ItemOffer.id !=']=$itemOfferID;
            }
            $itemOffer =$this->find('first',array('fields'=>array('id'),'conditions'=>$conditions));            
            if($itemOffer){
                return 0;
            }else{
                return 1;
            }         
    } 
    
    /*------------------------------------------------
    Function name:getCouponDetail()
    Description:To find Detail of coupon from coupon table 
    created:08/8/2015
   -----------------------------------------------------*/
    public function getOfferDetail($offerId=null,$storeId=null){      
        $offerDetail =$this->find('first',array('conditions'=>array('ItemOffer.store_id'=>$storeId,'ItemOffer.id'=>$offerId)));     
        if($offerDetail){
            return $offerDetail;
         
        }
     }
     
     /*------------------------------------------------
    Function name:OfferExists()
    Description:To find Detail of coupon from coupon table 
    created:08/8/2015
   -----------------------------------------------------*/
    public function OfferExists($itemID=null,$todayDate=null){    
        
        if($todayDate){            
            $condition['start_date <=']= $todayDate; 
            $condition['end_date >=']=$todayDate;
        }
        $condition['ItemOffer.item_id']=$itemID;
        $condition['ItemOffer.is_deleted']=0;
        $condition['ItemOffer.is_active']=1;
        $offerDetail =$this->find('first',array('conditions'=>$condition));     
        if($offerDetail){
            return $offerDetail;         
        }else{
            return 0;    
        }
     }
     
     public function allItemOfferOnItem($itemId = null, $current_date) {
        //echo $current_date;die;           
        $conditions = array('ItemOffer.is_active' => 1, 'ItemOffer.is_deleted' => 0, 'ItemOffer.item_id' => $itemId, 'ItemOffer.start_date <=' => $current_date, 'ItemOffer.end_date >=' => $current_date);
        $offer = $this->find('all', array('conditions' => $conditions));
        $displayItemOffer = array();
        if (!empty($offer)) {
            $i = 0;
            foreach ($offer as $k=>$itemOffer){
               $displayItemOffer[$i] = 'Buy '.($itemOffer['ItemOffer']['unit_counter']-1).' and get 1 free';
               $i++;
            }
            
        }
        return $displayItemOffer;
    }

}