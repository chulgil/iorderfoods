<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppModel', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class ItemType extends AppModel {
     var $name = 'ItemType';
     
     
   //public $belongsTo=array(
   //     'Type'=>array(
   //           'className' => 'Type',
   //           'foreignKey' => 'type_id'
   //           
   //     )
   //);
   
   
    /*------------------------------------------------
     Function name:saveItemType()
     Description:To Save Item Type Information
     created:04/8/2015
     -----------------------------------------------------*/	
    public function saveItemType($typeData=null){
          if($typeData){
            if($this->save($typeData)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
    
    
     /*------------------------------------------------
     Function name:ItemTypeExits()
     Description:To fetch the type based on typeid
     created:05/8/2015
     -----------------------------------------------------*/	
    public function ItemTypeExits($itemId=null,$typeId=null){
          if($itemId){              
              $itemtypeID=$this->find('first',array('fields'=>'id','conditions'=>array('ItemType.item_id'=>$itemId,'ItemType.type_id'=>$typeId,'ItemType.is_active'=>1)));
              if($itemtypeID){                 
                  return $itemtypeID;
              }else{
                  return false;
              }
          }         
    }
    
     /*------------------------------------------------
     Function name:deleteallItemPrice()
     Description:To Save Item Price Information
     created:04/8/2015
     -----------------------------------------------------*/	
    public function deleteallItemType($ItemId=null){
          if($ItemId){
            if($this->updateAll(array('ItemType.is_deleted'=>1),array('ItemType.item_id'=>$ItemId))){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
         
          
    public function getTypeById($typeId=null,$itemId=null){
        $typeDetail = $this->find('first',array('conditions'=>array('ItemType.type_id'=>$typeId,'ItemType.item_id'=>$itemId,'ItemType.is_active'=>1,'ItemType.is_deleted'=>0)));
        return $typeDetail;
    }
    
     /*------------------------------------------------
     Function name:fetchItemType()
     Description:To fetch the price based on Type
     created:07/9/2015
     -----------------------------------------------------*/	
    public function fetchItemType($itemId=null,$typeId=null,$storeId=null){
          if($itemId){
              
              $price=$this->find('first',array('conditions'=>array('ItemType.item_id'=>$itemId,'ItemType.type_id'=>$typeId,'ItemType.store_id'=>$storeId,'ItemType.is_active'=>1,'ItemType.is_deleted'=>0)));
              if($price){
                 
                  return $price;
              }else{
                  return false;
              }
          }         
    }
    
    
    
    
}