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
class ItemPrice extends AppModel {
    
     
     
      public $belongsTo=array(
        'Size'=>array(
              'className' => 'Size',
              'foreignKey' => 'size_id',
        )
   );
      
      
  /*------------------------------------------------
     Function name:saveItemPrice()
     Description:To Save Item Price Information
     created:04/8/2015
     -----------------------------------------------------*/	
    public function saveItemPrice($priceData=null){
          if($priceData){
            if($this->save($priceData)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
    
    
     /*------------------------------------------------
     Function name:fetchItemPrice()
     Description:To fetch the price based on Size
     created:05/8/2015
     -----------------------------------------------------*/	
    public function fetchItemPrice($itemId=null,$sizeId=null,$storeId=null){
          if($itemId){
              
              $price=$this->find('first',array('fields'=>'price','conditions'=>array('ItemPrice.item_id'=>$itemId,'ItemPrice.size_id'=>$sizeId,'ItemPrice.store_id'=>$storeId,'ItemPrice.is_active'=>1,'ItemPrice.is_deleted'=>0)));
              if($price){
                 
                  return $price;
              }else{
                  return false;
              }
          }         
    }
    
    
    /*------------------------------------------------
     Function name:ItemPriceExits()
     Description:To fetch the price based on Size
     created:05/8/2015
     -----------------------------------------------------*/	
    public function ItemPriceExits($itemId=null,$sizeId=null){
          if($itemId){
              $condition=array('ItemPrice.item_id'=>$itemId,'ItemPrice.is_active'=>1);
              if($sizeId){
                  $condition['ItemPrice.size_id']=$sizeId;
              }
              $itemPriceID=$this->find('first',array('fields'=>'id','conditions'=>$condition));
              if($itemPriceID){                 
                  return $itemPriceID;
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
    public function deleteallItemPrice($ItemId=null){
          if($ItemId){
            if($this->updateAll(array('ItemPrice.is_deleted'=>1),array('ItemPrice.item_id'=>$ItemId))){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    } 
    
      public function getSizeById($sizeId=null,$itemId=null){
        $sizeDetail = $this->find('first',array('conditions'=>array('ItemPrice.size_id'=>$sizeId,'ItemPrice.item_id'=>$itemId,'ItemPrice.is_active'=>1,'ItemPrice.is_deleted'=>0)));
        return $sizeDetail;
    }
    
    
    public function getItemSizes($itemID=null,$storeId=null){      
            $sizeList =$this->find('all',array('fields'=>array('ItemPrice.size_id'),'conditions'=>array('ItemPrice.item_id'=>$itemID,'ItemPrice.store_id'=>$storeId,'ItemPrice.is_active'=>1,'ItemPrice.is_deleted'=>0), 'recursive'=>2));            
            if($sizeList){
                return $sizeList;             
            }
        }
        
    public function getItemPriceByName($itemId=null,$sizeId=null,$storeId=null){
        $conditions = array('ItemPrice.store_id'=>$storeId,'ItemPrice.item_id'=>$itemId,'ItemPrice.size_id'=>$sizeId,'ItemPrice.is_active'=>1,'ItemPrice.is_deleted'=>0);        
        $categoryDetail = $this->find('first',array('fields'=>array('id'),'conditions'=>$conditions));            
        return $categoryDetail; 
    }
    
    public function getItemTax($itemId=null,$sizeId=null){            
        $conditions = array('ItemPrice.item_id'=>$itemId,'ItemPrice.is_active'=>1,'ItemPrice.is_deleted'=>0); 
	if($sizeId){
		$conditions['ItemPrice.size_id']=$sizeId;
	}       
        $itemTax = $this->find('first',array('fields'=>array('id','store_tax_id'),'conditions'=>$conditions));            
        return $itemTax; 
    }
     
}
