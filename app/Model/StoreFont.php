<?php App::uses('AppModel','Model');  
    class StoreFont extends AppModel {
         var $name = 'StoreFont';
         
         
        public function getFonts(){
            $fontList =$this->find('list',array('fields'=>array('id','name'),'conditions'=>array('StoreFont.is_active'=>1,'StoreFont.is_deleted'=>0)));     
            if($fontList){
                return $fontList;             
            }
        }
}