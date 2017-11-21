<?php App::uses('AppModel','Model');  
    class ZoneCoordinate extends AppModel {
        var $name = 'ZoneCoordinate';
        
    /*------------------------------------------------
     Function name:saveZonecord()
     Description:To Save the Zone info
     created:15/7/2016
     -----------------------------------------------------*/	
    public function saveZonecord($Data=null){
          if($Data){
            if($this->save($Data)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
}