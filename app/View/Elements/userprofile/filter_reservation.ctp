<?php $merchantId = $this->Session->read('merchant_id');?>
<div class="col-lg-4 col-sm-4 col-xs-12">
    <?php
    $locked_storeId = "";
    $merchantDet = array();
    $merchantList = $this->Common->getStores($merchantId);
    foreach ($merchantList as $key => $value) {
        $J = $this->Encryption->encode($key);
        $merchantDet[$J] = $value;
    }
    if (isset($encryptedlock_storeId) && !empty($encryptedlock_storeId)) {
        $locked_storeId = $encryptedlock_storeId;
    }

    echo $this->Form->input('Merchant.lock', array('options' => $merchantDet, 'class' => 'form-control', 'div' => false, 'empty' => 'Please Select Store', 'value' => $locked_storeId, 'label' => FALSE));
    ?>
</div>
<div class="col-lg-2 col-sm-2 col-xs-6 mtb-10">
    <?php echo $this->Form->input('MyBooking.from_date', array('type' => 'text', 'class' => 'user-detail date-select', 'placeholder' => 'From Date', 'label' => false, 'div' => false, 'readOnly' => true, 'value' => $fromdate)); ?>
<!--    <span class="blue">( From Date )</span>-->
</div>
<div class="col-lg-2 col-sm-2 col-xs-6 mtb-10">
    <?php echo $this->Form->input('MyBooking.to_date', array('type' => 'text', 'class' => 'user-detail date-select', 'placeholder' => 'To Date', 'label' => false, 'div' => false, 'readOnly' => true, 'value' => $endDate)); ?>
<!--    <span class="blue">( To Date )</span>-->
</div>

