<?php

App::uses('AppModel', 'Model');

class StoreTax extends AppModel { 
     var $name = 'StoreTax';     
     
     /*------------------------------------------------
     Function name:saveStoreHolidayInfo()
     Description:save Store holiday Information
     created:29/7/2015
     -----------------------------------------------------*/
	
     public function saveStoreTax($taxData=null){
           if($taxData){
             if($this->save($taxData)){		    
                     return true; //Success
                }else{			
                     return false;// Failure 
                }	       
           }         
     }
     
     /*------------------------------------------------
     Function name:emailExists()
     Description:To check if email already exists.
     created:27/7/2015
     -----------------------------------------------------*/
	
     public function storeTaxInfo($storeId=null){
        if($storeId){
                $taxInfo = $this->find('all',array('conditions'=>array('StoreTax.store_id'=>$storeId,'StoreTax.is_deleted'=>0,'StoreTax.is_active'=>1)));		
                return $taxInfo;
        }     
     }
     
     /*------------------------------------------------
     Function name:emailExists()
     Description:To check if email already exists.
     created:27/7/2015
     -----------------------------------------------------*/
	
     public function storeTaxes($storeId=null){
        if($storeId){
                $taxInfo = $this->find('all',array('fields'=>array('id','tax_name','tax_value'),'conditions'=>array('StoreTax.store_id'=>$storeId,'StoreTax.is_deleted'=>0,'StoreTax.is_active'=>1)));		
                return $taxInfo;
        }     
     }
     
      /*------------------------------------------------
     Function name:emailExists()
     Description:To check if email already exists.
     created:27/7/2015
     -----------------------------------------------------*/
	
     public function storeTaxesBytaxId($taxId=null){
        if($taxId){
                $taxInfo = $this->find('first',array('fields'=>array('id','tax_name','tax_value'),'conditions'=>array('StoreTax.id'=>$taxId,'StoreTax.is_deleted'=>0,'StoreTax.is_active'=>1)));		
                return $taxInfo;
        }     
     }
     
     
      /*------------------------------------------------
     Function name:storeTaxesBytaxvalue()
     Description:To get tax ID
     created:08/12/2015
     -----------------------------------------------------*/
	
     public function storeTaxesBytaxvalue($taxvalue=null,$storeId=null){
        if($taxvalue){
                $taxInfo = $this->find('first',array('fields'=>array('id'),'conditions'=>array('LOWER(StoreTax.tax_name)'=>strtolower($taxvalue),'StoreTax.is_deleted'=>0,'StoreTax.is_active'=>1,'StoreTax.store_id'=>$storeId)));		
                return $taxInfo;
        }     
     } 
    
}