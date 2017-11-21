<?php App::uses('AppModel','Model');
class StorePrinterStatus extends AppModel {

    var $name = 'StorePrinterStatus';
    public $useTable = 'store_printer_status';

    /*------------------------------------------------
    Function name:savePayment()
    Description:To Save store payment
    created:02/9/2015
    -----------------------------------------------------*/
    public function saveStorePrinterStatus($data=null){
        if($data){
            if($this->save($data)){
                return true; //Success
            }else{
                return false;// Failure
            }
        }
    }

}