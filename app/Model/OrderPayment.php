<?php

App::uses('AppModel', 'Model');


class OrderPayment extends AppModel {
    
    
    public function savePayment($data=null){
        if(!empty($data)){
            $res=$this->save($data);
            if($res){
                return true ;
            }else{
                return false;
            }
        }
        
    }
    
     /*------------------------------------------------
   Function name:transactionDetail()
   Description:To find list of the transaction
   created:20/8/2015
  -----------------------------------------------------*/
    public function transactionDetail($storeId=null,$merchantId=null){
       
       //echo $storeId." ".$merchantId;
        $transactionList =$this->find('all',array('conditions'=>array('OrderPayment.store_id'=>$storeId,'OrderPayment.is_active'=>1,'OrderPayment.is_deleted'=>0),'order' => array('created' => 'desc')));
        //echo "<pre>";print_r($categoryList);die;
        if($transactionList){
            return $transactionList;
         
        }
     }
     /*----------------------------------------
     Funtion name:MerchantOnlineCollection
     Desc:To find the List of HQ online collections
     created:1-09-2015
    *----------------------------------------*/
    public function MerchantOnlineCollection($merchantId=null){
      $onlineCollection = $this->find('all',array('fields' => array(
    'SUM(amount) AS total'
  ),'conditions'=>array('OrderPayment.merchant_id'=>$merchantId,'OrderPayment.is_active'=>1)));
      $total = $onlineCollection[0][0]['total'];

        if($total){
            return $total;
        }else{
            return false;
        }
        
    }
    
     /*----------------------------------------
     Funtion name:StoreOnlineCollection
     Desc:To find the List of Store online collections
     created:2-09-2015
    *----------------------------------------*/
    public function StoreOnlineCollection($storetId=null,$merchantId=null){
     $onlineCollection = $this->find('all',array('fields' => array(
    'SUM(amount) AS total'
  ),'conditions'=>array('OrderPayment.merchant_id'=>$merchantId,'OrderPayment.store_id'=>$storetId,'OrderPayment.is_active'=>1)));
      $total = $onlineCollection[0][0]['total'];

        if($total){
            return $total;
        }else{
            return false;
        }
        
    }
    
    
    public function fetchPaymentToday($storeId=null,$start=null,$end=null,$ordertype=null){
        
        $condition=array('OrderPayment.store_id'=>$storeId,'OrderPayment.created >='=>$start,'OrderPayment.created <='=>$end,'OrderPayment.is_active'=>1,'OrderPayment.is_deleted'=>0);
        
        
        $result = $this->find('all',array('fields' => array('id1','order_id','amount','created'),'conditions'=>$condition,'recursive'=>2));
        return $result;
    }
    
     
    public function fetchPayment($storeId=null){
        $result = $this->find('all',array('fields' => array('amount','created'),'conditions'=>array('OrderPayment.store_id'=>$storeId,'OrderPayment.is_active'=>1,'OrderPayment.is_deleted'=>0)));
        return $result;
    }
    
    
}