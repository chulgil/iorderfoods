<?php App::uses('AppModel','Model');  
    class SpecialDay extends AppModel {
      
                

   
     /*------------------------------------------------
     Function name:saveSpecialDay()
     Description:Save SpecialDay
     created:14/02/2017
     -----------------------------------------------------*/	
    public function saveSpecialDay($specialDayData=null){
          if($specialDayData){
            if($this->save($specialDayData)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
    
         /*------------------------------------------------
   Function name:getSpecialDayDetail()
   Description:To find Detail of the SpecialDay 
   created:14/02/2017
  -----------------------------------------------------*/
    public function getSpecialDayDetail($specialDayId=null,$storeId=null){      
        $specialDayDetail =$this->find('first',array('conditions'=>array('SpecialDay.store_id'=>$storeId,'SpecialDay.id'=>$specialDayId),'recursive'=>2));     
        if($specialDayDetail){
            return $specialDayDetail;
         
        }
     }
     
         
      /*------------------------------------------------
        Function name:checkSpecialDayUniqueName()
        Description:to check SpecialDay name is unique
        created:14/02/2017
        -----------------------------------------------------*/
     public function checkSpecialDayUniqueName($SpecialDayName=null,$storeId=null,$SpecialDayId=null){
        $conditions = array('LOWER(SpecialDay.name)'=>strtolower($SpecialDayName),'SpecialDay.store_id'=>$storeId,'SpecialDay.is_deleted'=>0);
            if($SpecialDayId){
                $conditions['SpecialDay.id !=']=$SpecialDayId;
            }
            $specialDay =$this->find('first',array('fields'=>array('id'),'conditions'=>$conditions));            
            if($specialDay){
                return 0;
            }else{
                return 1;
            }
        
           
     }
}