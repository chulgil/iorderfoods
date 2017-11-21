<?php
$arr[] = '';
if ($orderDetail[0]['Order']['seqment_id'] == 2) {
    $arr = array_diff($statusList, array('Ready For Delivery', 'On the way', 'Delivered', 'Confirmed'));
}
if ($orderDetail[0]['Order']['seqment_id'] == 3) {
    $arr = array_diff($statusList, array('Ready for Pick up', 'On the way', 'Confirmed', 'Picked Up'));
}
if ($orderDetail[0]['Order']['seqment_id'] == 1) {
    $arr = $statusList;
}

$total_amount = number_format(($orderDetail[0]['Order']['amount'] - $orderDetail[0]['Order']['coupon_discount']), 2);
$devlInfo = $orderDetail[0]['DeliveryAddress'];
$orderInfo = $orderDetail[0]['Order'];
$userInfo = $orderDetail[0]['User'];

if ($orderInfo['user_id'] == 0) {
    $enduser_name = $devlInfo['name_on_bell'];
    $enduser_phone = $devlInfo['phone'];
} else {
    $enduser_name = $userInfo['fname'] . ' ' . $userInfo['lname'];
    $enduser_phone = $userInfo['phone'];
}
$address = $devlInfo['address'] . ' ' . $devlInfo['city'];
$pickup_time = '';
$pickup_time = $this->Common->storeTimeFormate($orderInfo['pickup_time'], true);
if ($orderInfo['seqment_id'] == 2) {
    $address = "Pickup";
    $address = "</br>Address : " . $address;
    $pickup_time = "| Pickup Time:&nbsp;&nbsp;" . $pickup_time;
    $orderType = "Pickup";
} else {
    $pickup_time = "| Delivery Time:&nbsp;&nbsp;" . $pickup_time;
    $address = "</br>Address : " . $address;
    $orderType = "Delivery";
}
if ($orderDetail[0]['OrderPayment']['payment_gateway'] == 'COD') {
    if ($orderDetail[0]['Order']['seqment_id'] == 3) {
        $paymentStatus = "UNPAID";
    } else {
        $paymentStatus = "UNPAID";
    }
} else {
    $paymentStatus = "PAID";
}
$created_time = $this->Common->storeTimeFormate($this->Common->storeTimezone('', $orderInfo['created']), true);

$orderAttr = array('enduser_name' => $enduser_name, 'enduser_phone' => $enduser_phone, 'address' => $address, 'orderType' => $orderType, 'paymentStatus' => $paymentStatus, 'created_time' => $created_time, 'pickup_time' => $pickup_time);
?>

<style>
    .new-chkbx-wrap {
        float: left;
        padding: 5px;
        width: 30%;
        margin-bottom: 10px;
    }

    .new-chkbx-wrap > input {
        float: left;
        margin-right: 5px;
        position: relative;
        top: -3px;
    }
</style>
<div class="row">
    <div class="col-lg-11">
        <h3>Order Details</h3>
        <br>


        <?php
//echo $this->element('orderdetail/order_detail_head');

        echo $this->element('orderdetail/order_detail', array('total_amount' => $total_amount, 'arr' => $arr, 'orderDetail' => $orderDetail, 'orderAttr' => $orderAttr));

//echo $this->element('orderdetail/order_detail_bottom', array('total_amount' => $total_amount,'arr' => $arr,'orderDetail' => $orderDetail));
        ?> 

    </div>
</div>
<?php echo $this->Html->css('pagination'); ?>