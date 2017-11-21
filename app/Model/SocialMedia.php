<?php App::uses('AppModel','Model');  
    class SocialMedia extends AppModel {
      
                

   
     /*------------------------------------------------
     Function name:saveSocialMedia()
     Description:Save social media
     created:25/8/2015
     -----------------------------------------------------*/	
    public function saveSocialMedia($socialData=null){
          if($socialData){
            if($this->save($socialData)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
    
    /*----------------------------------------
     Funtion name:fetchSocialMediaDetail
     Desc:To find the social media detail 
     created:31-08-2015
    *----------------------------------------*/
    public function fetchSocialMediaDetail($storeId=null,$merchantId=null){
        if(!empty($storeId)){
            $condition = array('SocialMedia.store_id'=>$storeId);
        }
        if(!empty($merchantId)){
            $condition = array('SocialMedia.store_id IS NULL','SocialMedia.merchant_id'=>$merchantId);
        }
        $socialResult=$this->find('first',array('conditions'=>$condition));
        if($socialResult){
            return $socialResult;
        }else{
            return false;
        }
        
    }
    
}