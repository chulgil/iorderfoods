
<?php
//$Ordertype=$this->Common->getOrderType();echo "Ordertype".$Ordertype;
$Ordertype=$this->Session->read('Order.order_type');
if($Ordertype==2){
    echo $this->element('orderLogin/pickup_address');    
}elseif($Ordertype==3){
    if ($this->Session->check('Auth.User.id')) {        
        echo $this->element('orderLogin/login_delivery_address'); 
    } else {
        $userId = 0;
        echo $this->element('orderLogin/guest_delivery_address'); 
    }
}

?>