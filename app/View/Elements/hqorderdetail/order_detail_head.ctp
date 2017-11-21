<?php

echo $this->Session->flash();
            echo $this->Form->create('Hqorder', array('action'=>'UpdateOrderStatus','controller'=>'hqorders',
                'inputDefaults' => array('label' => false,
                'div'=>false, 'required' => false, 'error' => false, 'legend' => false,'autocomplete' =>
                'off'),'enctype'=>'multipart/form-data'));
            echo $this->Form->input('Orders.id',array('type'=>'hidden','value'=>$orderDetail[0]['Order']['id']));
            echo $this->Form->input('Orders.store_id',array('type'=>'hidden','value'=>$orderDetail[0]['Order']['store_id']));

?>