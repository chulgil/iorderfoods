<?php

App::uses('AppModel','Model');  
    class Tab extends AppModel {
        var $name = 'Tab';
        
        
        /*------------------------------------------------
   Function name:getTabs()
   Description:To find Permission Tabs
   created:3/8/2015
  -----------------------------------------------------*/
    public function getTabs($roleId=null){
       // $roleId=$_SESSION['Auth']['Admin']['role_id'];
        $Tabs =$this->find('all',array('fields'=>array('id','tab_name'),'conditions'=>array('Tab.is_active'=>1,'Tab.is_deleted'=>0,'Tab.section'=>$roleId)));
        if($Tabs){
            return $Tabs;         
        }
     }
        
    
    
    function getTabData($tab_name=null,$controller=null,$action=null,$roleId=null) {       
        if(!$roleId){
            if(!empty($_SESSION['Auth']['Admin'])){
                $roleId=$_SESSION['Auth']['Admin']['role_id'];
            }
        }
        $conditions=array('Tab.is_active' => '1', 'Tab.is_deleted' => '0','Tab.section' => $roleId);
        if (!empty($tab_name)) {
            $conditions['Tab.tab_name']=$tab_name;
        }else{
            $conditions['Tab.tab_controller']=$controller;
            if($action){
                $conditions['Tab.tab_action']=$action;
            }
        }
        $data = $this->find('first', array('conditions' => $conditions));
        if (!empty($data)) {
            return $data['Tab']['id'];
        }
    }

}