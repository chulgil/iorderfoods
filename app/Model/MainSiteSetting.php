<?php 
App::uses('AppModel', 'Model');
class MainSiteSetting extends AppModel {
    ##function to getting site's common settings
    function getSiteSettings(){
            $result = $this->find('first');
            return $result;
    }
    
     /*------------------------------------------------
     Function name:saveConfiguration()
     Description:To save configuration
     created:17/09/2015
     -----------------------------------------------------*/
    
    public function saveConfiguration($data=null){
        if($data){
            $res=$this->save($data);
            if($res){
                return true ;
            }else{
                return false;
            }
        }
        
    }

}