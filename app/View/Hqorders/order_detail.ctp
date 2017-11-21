<?php
$arr[] = '';
if ($orderDetail[0]['Order']['seqment_id'] == 2) {
    $arr = array_diff($statusList, array('Ready For Delivery', 'Delivered'));
}
if ($orderDetail[0]['Order']['seqment_id'] == 3) {
    $arr = array_diff($statusList, array('Ready for Pick up', 'Picked Up'));
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
$pickup_time = $this->Hq->storeTimeFormate($this->Hq->storeTimezone(null, $orderInfo['pickup_time'], null, $orderInfo['store_id']), true, $orderInfo['store_id']);
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
$paymentStatus = $paymentStatus . '-' . $orderDetail[0]['OrderPayment']['payment_gateway'];
$created_time = $this->Hq->storeTimeFormate($this->Hq->storeTimezone(null, $orderInfo['created'], null, $orderInfo['store_id']), true, $orderInfo['store_id']);
//$created_time = $this->Common->storeTimeFormate($this->Hq->storeTimezone(null, $orderInfo['created'], null, $orderInfo['store_id']),true); 
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
        echo $this->element('hqorderdetail/order_detail_head');

        echo $this->element('hqorderdetail/order_detail', array('total_amount' => $total_amount, 'arr' => $arr, 'orderDetail' => $orderDetail, 'orderAttr' => $orderAttr));
        if (!in_array($orderDetail[0]['Order']['order_status_id'], array('5', '7', '9'))) {
            echo $this->element('hqorderdetail/order_detail_bottom', array('total_amount' => $total_amount, 'arr' => $arr, 'orderDetail' => $orderDetail));
        }
        ?> 

    </div>
</div>
<?php echo $this->Html->css('pagination'); ?>
<script type="text/javascript">
    $("#HqorderUpdateOrderStatusForm").closest('form').on('submit', function (e) {
        e.preventDefault();
        var radioValue = $('input[name="data[Order][order_status_id]"]:checked', '#HqorderUpdateOrderStatusForm').val();
        if (radioValue == 7 || radioValue == 5 || radioValue == 9) {
            if (confirm("Are you sure? No changes will be made after this.") == true) {
                this.submit();
            }
        } else {
            this.submit();
        }
    });
</script>