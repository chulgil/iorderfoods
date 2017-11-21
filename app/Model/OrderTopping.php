<?php

App::uses('AppModel', 'Model');


class OrderTopping extends AppModel {
   
    
    
    public function saveTopping($data=null){
       // echo "hi";die;
        if($data){
           // print_r($data);die;
            $res=$this->save($data);
            if($res){
                return true ;
            }else{
                return false;
            }
        }
        
    }
    
}