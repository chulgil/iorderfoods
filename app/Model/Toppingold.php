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
class Topping extends AppModel {
    
     
    public $hasMany=array(
         'ItemDefaultTopping'=>array(
            'className'=>'ItemDefaultTopping',
            'foreignKey'=>'topping_id',
            'conditions'=>array('ItemDefaultTopping.is_deleted'=>0)
         )
      
     );
   
          
     /*------------------------------------------------
     Function name:fetchItemPrice()
     Description:To fetch the price based on Size
     created:05/8/2015
     -----------------------------------------------------*/	
    public function fetchToppingPrice($itemId=null,$toppingId=null,$storeId=null){
          if($itemId){
             // echo $itemId;die;
               $price=$this->find('first',array('fields'=>array('price','name'),'conditions'=>array('Topping.item_id'=>$itemId,'Topping.id'=>$toppingId,'Topping.store_id'=>$storeId,'Topping.is_active'=>1,'Topping.is_deleted'=>0)));
              if($price){
                 
                  return $price;
              }else{
                  return false;
              }
          }         
    }   
    
     /*------------------------------------------------
     Function name:saveTopping()
     Description:To Save Topping Information
     created:04/8/2015
     -----------------------------------------------------*/	
    public function saveTopping($itemData=null){
          if($itemData){
            if($this->save($itemData)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
    
    
    /*------------------------------------------------
     Function name:fetchToppingDetails()
     Description:To fetch the price based on Size
     created:05/8/2015
     -----------------------------------------------------*/	
    public function fetchToppingDetails($toppingId=null,$storeId=null){
          if($toppingId){
             // echo $itemId;die;
               $price=$this->find('first',array('conditions'=>array('Topping.id'=>$toppingId,'Topping.store_id'=>$storeId,'Topping.is_deleted'=>0)));
              if($price){
                 
                  return $price;
              }else{
                  return false;
              }
          }         
    }
    
    
    /*------------------------------------------------
        Function name:checkItemUniqueName()
        Description:to check item name is unique
        created:3/8/2015
        -----------------------------------------------------*/
     public function checkToppingUniqueName($toppingName=null,$storeId=null,$Itemid=null,$toppingid=null){
        
            $conditions = array('LOWER(Topping.name)'=>strtolower($toppingName),'Topping.store_id'=>$storeId,'Topping.item_id'=>$Itemid,'Topping.is_deleted'=>0);
            if($Itemid){
                $conditions['Topping.id !=']=$toppingid;
            }
            $item =$this->find('first',array('fields'=>array('id'),'conditions'=>$conditions));            
            if($item){
                return 0;
            }else{
                return 1;
            }
     }
     
       public function getToppingById($topId=null,$itemId=null){
        $topDetail = $this->find('first',array('conditions'=>array('Topping.id'=>$topId,'Topping.item_id'=>$itemId,'Topping.is_active'=>1,'Topping.is_deleted'=>0)));
        return $topDetail;
    }
     
     /*------------------------------------------------
      Function name:deleteMultipleToppings()
      Description:Delete multiple topping
      created:03/9/2015
     -----------------------------------------------------*/  
      public function deleteMultipleToppings($id=null,$storeId=null){
            $this->autoRender=false;
            $this->layout="admin_dashboard";       
	    $data['Topping']['store_id']=$storeId;
            $data['Topping']['is_deleted']=1;
           if(!empty($id)){
	    $filter_array = array_filter($id);
	    $i=0;
             foreach($filter_array as $k=>$orderId){
	         $data['Topping']['id']=$orderId;
		 $this->saveTopping($data);
		 $i++;
	     }
	                
	   }
           return $i;
      }
      
       /*------------------------------------------------
     Function name:getAddons()
     Description:To fetch the Add-ons of store
     created:05/8/2015
     -----------------------------------------------------*/	
    public function getAddons($storeId=null){
          if($storeId){
             // echo $itemId;die;
               $list=$this->find('list',array('conditions'=>array('Topping.store_id'=>$storeId,'Topping.is_deleted'=>0,'Topping.is_addon_category'=>1)));
              if($list){
                 
                  return $list;
              }else{
                  return false;
              }
          }         
    }
    
      
}
