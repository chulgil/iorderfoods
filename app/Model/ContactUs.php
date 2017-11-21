<?php

App::uses('AppModel', 'Model');

class ContactUs extends AppModel {

    public $useTable = 'contact_us';

    public function getAllMessages($merchant_id) {
        if(!empty($merchant_id)) {
            $result = $this->find('all',array('conditions'=>array('merchant_id'=>$merchant_id)));
            return $result;
        } else {
            return false;
        }
    }
    public function getDetailById($id=null) {
        if(!empty($id)) {
            $result = $this->findById($id);
            return $result;
        } else {
            return false;
        }
    }
    
    public function saveMessage($data=null){      
       if($data){
         if($this->save($data)){
            return true;
         }else{
            return false;
         }
        
       }
    }

}
