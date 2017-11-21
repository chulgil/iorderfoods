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
class DeliveryAddress extends AppModel {
     
        //***************Model Relation**************************//
        
        //     public $hasOne = array(
        //	    'DeliveryAddress' => array(
        //		'className' => 'DeliveryAddress',
        //		'conditions' => array('DeliveryAddress.is_deleted' => '0','DeliveryAddress.is_active'=>1),
        //		'order' => 'DeliveryAddress.created DESC'
        //	    )
        //     );

    /*------------------------------------------------
     Function name:checkAddress()
     Description:To fetch the out the delivery address based on User Id
     created:22/7/2015
    -----------------------------------------------------*/
	
    public function checkAddress($userId=null,$roleId=null,$decrypt_storeId=null,$decrypt_merchantId=null){
                    if($decrypt_storeId){
                          
                            $result = $this->find('first',array('conditions'=>array('DeliveryAddress.user_id'=>$userId,'DeliveryAddress.store_id'=>$decrypt_storeId,'DeliveryAddress.merchant_id'=>$decrypt_merchantId,'DeliveryAddress.is_deleted'=>0,'DeliveryAddress.is_active'=>1)));		
                            
			    if($result){
                                return $result;
                            }else{
                                return false;
                            }
                    }
    
    }
    
    public function checkAllAddress($userId=null,$roleId=null,$decrypt_storeId=null,$decrypt_merchantId=null){
                    if($decrypt_storeId){                          
                            //$result = $this->find('all',array('conditions'=>array('DeliveryAddress.user_id'=>$userId,'DeliveryAddress.store_id'=>$decrypt_storeId,'DeliveryAddress.merchant_id'=>$decrypt_merchantId,'DeliveryAddress.is_deleted'=>0,'DeliveryAddress.is_active'=>1)));
                        $result = $this->find('all',array('conditions'=>array('DeliveryAddress.user_id'=>$userId,'DeliveryAddress.merchant_id'=>$decrypt_merchantId,'DeliveryAddress.is_deleted'=>0,'DeliveryAddress.is_active'=>1)));           
			    if($result){
                                return $result;
                            }else{
                                return false;
                            }
                    }
    
    }
    
    
    
      /*------------------------------------------------
     Function name:saveAddress()
     Description:To save delivery address
     created:28/7/2015
    -----------------------------------------------------*/
	
    public function saveAddress($data=null){
                    if($data){
                         if($this->save($data)){
				return true;	  
		          }else{
				return false;
			  }
                    }
    
    }
    
       /*------------------------------------------------
     Function name:fetchAddress()
     Description:To save delivery address
     created:28/7/2015
    -----------------------------------------------------*/
	
    public function fetchAddress($id=null,$user_id=null,$store_id=null){
        if($id){
           $result= $this->find('first',array('conditions'=>array('DeliveryAddress.id'=>$id,'DeliveryAddress.is_active'=>1,'DeliveryAddress.is_deleted'=>0)));
           return $result;
        }
    
    }
    
    
      /*------------------------------------------------
     Function name:fetchAllAddress()
     Description:To save delivery address
     created:28/7/2015
    -----------------------------------------------------*/
	
    public function fetchAllAddress($user_id=null){
        if($user_id){
           $result= $this->find('all',array('fields'=>array('id','user_id','label','default','address','city','state','zipcode','phone','name_on_bell'),'conditions'=>array('DeliveryAddress.user_id'=>$user_id,'DeliveryAddress.is_active'=>1,'DeliveryAddress.is_deleted'=>0)));
           return $result;
        }
    
    }
    
    
       /*------------------------------------------------
     Function name:fetchAddress()
     Description:To save delivery address
     created:28/7/2015
    -----------------------------------------------------*/
	
    public function fetchDefaultAddress($user_id=null){
        if($user_id){
           $result= $this->find('first',array('conditions'=>array('DeliveryAddress.user_id'=>$user_id,'DeliveryAddress.is_active'=>1,'DeliveryAddress.is_deleted'=>0,'DeliveryAddress.default'=>1)));
           return $result;
        }
    
    }
    
    
       /*------------------------------India Red vs India Green------------------
     Function name:fetchfirstAddress()
     Description:To get delivery address
     created:28/7/2015
    -----------------------------------------------------*/
	
    public function fetchfirstAddress($user_id=null){
        if($user_id){
           $result= $this->find('first',array('conditions'=>array('DeliveryAddress.user_id'=>$user_id,'DeliveryAddress.is_active'=>1,'DeliveryAddress.is_deleted'=>0)));
           return $result;
        }
    
    }
   
}