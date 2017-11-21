<?php

App::uses('AppModel','Model');  
    class Permission extends AppModel {
        var $name = 'Permission';
        
        
        /*------------------------------------------------
        Function name:DeleteAllPermission()
        Description:To Delete permission
        created:3/8/2015
       -----------------------------------------------------*/
     
    public function DeleteAllPermission($userid=null){
          if($userid){            
            $condition['Permission.user_id']=$userid;                      
            if($this->updateAll(array('Permission.is_deleted'=>1),$condition)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
    
    
     /*------------------------------------------------
        Function name:checkPermissionExists()
        Description:To check permission
        created:17/8/2015
       -----------------------------------------------------*/
     
    public function checkPermissionExists($tabId=null,$userid=null){
        $conditions = array('Permission.tab_id'=>$tabId,'Permission.user_id'=>$userid);        
        $permission =$this->find('first',array('fields'=>array('id'),'conditions'=>$conditions));            
        if($permission){
            return $permission;
        }else{
            return 0;
        }
    }
    
    /*------------------------------------------------
        Function name:savePermission()
        Description:To Save Permission
        created:17/8/2015
       -----------------------------------------------------*/
    public function savePermission($permissionData=null){
          if($permissionData){
            if($this->save($permissionData)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
    
    
    /*------------------------------------------------
   Function name:getUserPermission()
   Description:To Get User Permission
   created:17/8/2015
  -----------------------------------------------------*/
    //public function getUserPermission($userid=null){ 
    //    $userPermissions =$this->find('all',array('fields'=>array('id'),'conditions'=>array('Permission.user_id'=>$userid,'Permission.is_active'=>1,'Permission.is_deleted'=>0)));        
    //    return $userPermissions;
    // }
     
     
     
     function getPermissionData($user_id=null,$tab = null) {
        $data = $this->find('first', array('conditions' => array('Permission.user_id' => $user_id, 'Permission.tab_id'=>$tab,'Permission.is_active'=>1,'Permission.is_deleted'=>0)));
        return $data;
    } 
    
}