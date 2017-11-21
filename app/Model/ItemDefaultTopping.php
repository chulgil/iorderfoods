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
class ItemDefaultTopping extends AppModel {
     var $name = 'ItemDefaultTopping';
     
     
     
      /*------------------------------------------------
     Function name:deleteallDefaultTopping()
     Description:To delete all default item by Item id
     created:04/8/2015
     -----------------------------------------------------*/	
    public function deleteallDefaultTopping($ItemId=null,$defaultToppingid=null){
          if($ItemId){
            if($defaultToppingid){
                $condition['ItemDefaultTopping.id']=$defaultToppingid;
            }
            $condition['ItemDefaultTopping.item_id']=$ItemId;
            if($this->updateAll(array('ItemDefaultTopping.is_deleted'=>1),$condition)){		    
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
    public function defaultToppingExits($toppingId=null){
          if($toppingId){              
              $deafulttoppingId=$this->find('first',array('fields'=>'ItemDefaultTopping.id','conditions'=>array('ItemDefaultTopping.topping_Id'=>$toppingId,'ItemDefaultTopping.is_active'=>1)));
              if($deafulttoppingId){                 
                  return $deafulttoppingId;
              }else{
                  return false;
              }
          }         
    }
     
    /*------------------------------------------------
     Function name:saveDefaultTopping()
     Description:To Save default Topping
     created:04/8/2015
     -----------------------------------------------------*/	
    public function saveDefaultTopping($defaultData=null){
          if($defaultData){
            if($this->save($defaultData)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
    
    
    public function getDefaultItems($subaddonId=null){
          if($subaddonId){              
              $deafultitemId=$this->find('all',array('fields'=>'ItemDefaultTopping.item_id','conditions'=>array('ItemDefaultTopping.topping_id'=>$subaddonId,'ItemDefaultTopping.is_active'=>1,'ItemDefaultTopping.is_deleted'=>0)));
              if($deafultitemId){                 
                  return $deafultitemId;
              }else{
                  return false;
              }
          }         
    }
     
}