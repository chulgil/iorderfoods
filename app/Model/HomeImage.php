<?php

App::uses('AppModel', 'Model');


class HomeImage extends AppModel { 
     var $name = 'HomeImage';   
    
    
     /*------------------------------------------------
     Function name:getStoreSliderImages()
     Description:To get images of store slider
     created:22/7/2015
     -----------------------------------------------------*/
	
	 public function getStoreThemeImages($storeID=null){
                    $storeSliderImages = $this->find('first',array('conditions'=>array('store_id'=>$storeID)));
                    return $storeSliderImages;          
         }
         
     /*------------------------------------------------
     Function name:getStoreSliderImages()
     Description:To get images of store slider
     created:22/7/2015
     -----------------------------------------------------*/
	
	 public function saveStoreThemeImage($imageData=null){
               if($imageData){
	         if($this->save($imageData)){		    
			 return true; //Success
		    }else{			
			 return false;// Failure 
		    }	       
               }         
         }
    
}