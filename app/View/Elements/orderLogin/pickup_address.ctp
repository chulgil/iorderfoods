<?php
$store_data = $this->Common->getStoreDetail();
?>

<h2>Pickup</h2>
<div class="address">
    <address class="inbox">
        <h3><?php echo $store_data['Store']['store_name']; ?></h3>
        <p> <?php echo $store_data['Store']['address']; ?> <br> <?php echo $store_data['Store']['city'] . ' ' . $store_data['Store']['state'] . ' ' . $store_data['Store']['zipcode']; ?> <br> <?php echo $store_data['Store']['phone']; ?></p>
    </address>
    <span><b><?php echo $this->Html->link('Change Order Type', 'javascript:void(0)', array('class' => 'button-link', 'escape' => false, 'id' => 'changeorderType')); ?></b></span>
    <div class="button-frame"><button type="button" id="pickupButton" class="btn btn-primary theme-bg-1">Continue</button></div>
</div>

<script>
    $('#pickupButton').click(function () {
        $("#btnOrderType").trigger("click");
        window.location = window.location;
    });

    $('#changeorderType').click(function () {
        changeTabPan('chkOrderType', 'chkDeliveryAddress');
        setDefaultStoreTime();
    });
</script>