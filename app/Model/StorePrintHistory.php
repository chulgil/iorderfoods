<?php App::uses('AppModel','Model');
class StorePrintHistory extends AppModel {

    var $name = 'store_print_histories';

    /*------------------------------------------------
    Function name:savePayment()
    Description:To Save store payment
    created:02/9/2015
    -----------------------------------------------------*/
    public function saveStorePrintHistory($data=null){
        if($data){
            if($this->save($data)){
                return true; //Success
            }else{
                return false;// Failure
            }
        }
    }

    public function updateOrderStatus($id){
        $data = array('id' => $id, 'is_active' => '1');
        if($id){
            if($this->save($data)){
                return true;
            }else{
                return false;
            }
        }
    }

    public function getPrintNumber($id) {
        $result=$this->find('list',array('fields'=>array('order_number'),'conditions'=>array(
            'StorePrintHistory.id'=>$id
        )));
        if($result){
            return $result[$id];
        }else{
            return false;
        }
    }



    public function fetchPrintList($merchantId, $storeId, $print_type){

        $date=date('Y-m-d');

        $result=$this->find('list',array('fields'=>array('id'),'conditions'=>array(
            'StorePrintHistory.merchant_id'=>$merchantId,
            'StorePrintHistory.store_id'=>$storeId,
            'StorePrintHistory.type'=>$print_type,
            'StorePrintHistory.is_active'=>1
//            'StorePrintHistory.created >='=>$date
        )));

        if($result){
            return $result;
        }else{
            return false;
        }

    }


}