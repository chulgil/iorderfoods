<?php
$merchantId = $this->Session->read('merchant_id');
$decrypt_storeId = $this->Encryption->decode($encrypted_storeId);
?>
<div class="col-lg-4 col-sm-4 col-xs-6 mtb-10">
    <?php
    $merchantList = $this->Common->getStores($merchantId);
    echo $this->Form->input('Merchant.store_id', array('options' => $merchantList, 'class' => 'form-control', 'div' => false, 'empty' => 'Please Select Store', 'value' => $decrypt_storeId, 'label' => FALSE));
    ?>
</div>
<div class="col-lg-4 col-sm-4 col-xs-6 mtb-10">
    <?php
    $val = '';
    if (isset($keyword) && !empty($keyword)) {
        $val = $keyword;
    }
    ?>
    <?php echo $this->Form->input('User.keyword', array('value' => $val, 'label' => false, 'div' => false, 'placeholder' => 'Keyword Search', 'class' => 'form-control')); ?>
    <?php if ($this->params['action'] == 'myOrders' || $this->params['action'] == 'mySavedOrders') { ?>
        <span class="blue">(<b>Search by:</b>Order no.,address,city,name)</span>
    <?php } elseif ($this->params['action'] == 'myFavorites') { ?>
        <span class="blue">(<b>Search by:</b>Order no.)</span>
    <?php } ?>
</div>
