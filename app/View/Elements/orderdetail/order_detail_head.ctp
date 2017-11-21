<?php

echo $this->Session->flash();
//pr($orderDetail);
echo $this->Form->create('Order', array('action' => 'UpdateOrderStatus', 'controller' => 'orders',
    'inputDefaults' => array('label' => false,
        'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' =>
        'off'),'enctype' => 'multipart/form-data'));
echo $this->Form->input('Orders.id', array('type' => 'hidden', 'value' => $orderDetail[0]['Order']['id']));
?>