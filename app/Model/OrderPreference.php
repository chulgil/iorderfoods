<?php
App::uses('AppModel', 'Model');

class OrderPreference extends AppModel {   
    
    public function saveSubpreference($data=null){       
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