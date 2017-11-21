<?php App::uses('AppModel','Model');  
    class Favorite extends AppModel {
        var $name = 'Favorite';
        
        
    /*------------------------------------------------
     Function name:saveFavorite()
     Description:To Save favorite Information
     created:11/8/2015
     -----------------------------------------------------*/	
    public function saveFavorite($data=null){
       if($data){
           if(!empty($data['Favorite']['id'])){
                $data['Favorite']['is_deleted'] = 1;
           } else {
                $myFav = $this->find('first',array('conditions'=>array('Favorite.merchant_id'=>$data['Favorite']['merchant_id'],'Favorite.user_id'=>$data['Favorite']['user_id'],'Favorite.store_id'=>$data['Favorite']['store_id'],'Favorite.order_id'=>$data['Favorite']['order_id'],'Favorite.is_active'=>1,'Favorite.is_deleted'=>1)));   
                if($myFav){
                   $data['Favorite']['id'] = $myFav['Favorite']['id'];
                   $data['Favorite']['is_deleted'] = 0;
                } 
           }
           
            if($this->save($data)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
    
    /*------------------------------------------------
     Function name:getFavoriteDetails()
     Description:To get list of favourites
     created:11/8/2015
     -----------------------------------------------------*/	
    
    public function getFavoriteDetails($decrypt_merchantId=null,$decrypt_storeId=null,$decrypt_userId=null){
        //$myFav = $this->find('all',array('order'=>'Favorite.created DESC','recursive'=>4,'conditions'=>array('Favorite.merchant_id'=>$decrypt_merchantId,'Favorite.user_id'=>$decrypt_userId,'Favorite.store_id'=>$decrypt_storeId,'Favorite.is_active'=>1,'Favorite.is_deleted'=>0)));
        $myFav = $this->find('all',array('order'=>'Favorite.created DESC','recursive'=>4,'conditions'=>array('Favorite.merchant_id'=>$decrypt_merchantId,'Favorite.user_id'=>$decrypt_userId,'Favorite.is_active'=>1,'Favorite.is_deleted'=>0)));
        return $myFav;
        
    }       
}