<div class="row">
    <div class="col-lg-13">
        <div>
            &nbsp;&nbsp;&nbsp;&nbsp; Set order status
        </div>
        <div class="col-lg-11">
            <?php
            $savedButton = false;
            foreach ($arr as $k => $data) {
                if (in_array($k, $savedStatus)) {
                    $savedButton = true;
                    ?>
                    <div class="new-chkbx-wrap">
                        <?php
                        echo $this->Form->input('Order.order_status_id', array(
                            'type' => 'radio',
                            'options' => array($k => $data),
                            'default' => $orderDetail[0]['Order']['order_status_id']
                        ));
                        ?>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</div>
<table>
    <tr>
        <td colspan="6">
            <?php
            if ($savedButton) {
                echo $this->Form->button('Update Status', array('type' => 'submit', 'class' => 'btn btn-default'));
            }
            echo $this->Html->link('Cancel', "/orders/index/", array("class" => "btn btn-default", 'escape' => false));
            if (!empty($printerIP)) {
                //echo $this->Html->link('Print',array('controller'=>'orders','action'=>'PrintReceipt',$this->Encryption->encode($orderDetail[0]['Order']['id']),1),array("class" => "btn btn-default",'escape' => false));
            }
            ?>
        </td>
    </tr>
</table>
<?php echo $this->Form->end(); ?>