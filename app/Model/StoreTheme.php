<?php App::uses('AppModel','Model');  
    class StoreTheme extends AppModel {
         var $name = 'StoreTheme';
         
         
        public function getThemes(){
            $themeList =$this->find('list',array('fields'=>array('id','name'),'conditions'=>array('StoreTheme.is_active'=>1,'StoreTheme.is_deleted'=>0)));     
            if($themeList){
                return $themeList;             
            }
        }
}