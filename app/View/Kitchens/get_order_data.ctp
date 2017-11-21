<style>
    .row {
        margin-left: -48px !important;
    }
    .noeffect{
        list-style-type: none;
        padding:0px;
        font-size: 12px;
    }
    .item{
        color:#404049;
    }
    .offers{
        list-style-type: none;
        font-size: 12px;
    }
    .offeritem{
        font-weight: bold;
        font-size: 12px;
    }
    .addonlist{
        list-style-type: none;
        font-size: 12px;
        margin-left:15px;
        padding:0px;
    }
    .container{
        float: left;
        font-family: Tahoma;
        /*min-width: 240px;*/
        margin:2px;
        padding:1px;
        -webkit-box-shadow: 0 0 5px 2px rgba(0, 0, 0, .5);
        box-shadow: 0 0 5px 2px rgba(0, 0, 0, .5);
        border-radius:5px;
        background-color:#fff;
        font-size:12px;
        min-height: 90vh;

    }
    .head{
        min-height:70px;
        border: 1px solid #F3F2F1;
        font-weight: bold;
        font-size: 12px;
        background-color: #F2F2F2;
    }
    .newitem{
        padding:3px;
    }

    .item{
        font-size: 14px;
        font-style: italic;
    }
    .noeffect.item{
        margin-bottom: 1%;
    }
</style>
<?php if (!empty($myOrders)) {
    foreach ($myOrders as $orders) {
        ?>
        <div class="container" style="width:200px;">
            <div class="head">
                <ul class="noeffect">
                    <li>
                        Order#:<?php echo $orders['Order']['order_number']; ?>
                    </li>
                    <li>
                        <?php
                        //if($orders['Order']['is_pre_order'] == 1) {
                        echo 'Date: ' . date('m/d/Y h:i a', strtotime($orders['Order']['pickup_time']));
                        //} else {
                        //    echo 'Date: '.date('m/d/Y h:i a',strtotime($this->Common->storeTimezone('',$orders['Order']['created'])));
                        //}
                        ?>
                    </li>
                    <li>
                        <?php
                        $paymentStatus = "";
                        if ($orders['OrderPayment']['payment_gateway'] == 'COD') {
                            if ($orders['Order']['seqment_id'] == 3) {
                                $paymentStatus = "UNPAID";
                            } else {
                                $paymentStatus = "UNPAID";
                            }
                        } else {
                            $paymentStatus = "PAID";
                        }


                        if ($orders['Order']['seqment_id'] == 1) {
                            echo 'OrderType: Dine-In';
                        } elseif ($orders['Order']['seqment_id'] == 2) {
                            echo 'OrderType: Pickup' . '-' . $paymentStatus;
                        } elseif ($orders['Order']['seqment_id'] == 3) {
                            echo 'OrderType: Delivery' . '-' . $paymentStatus;
                        }
                        ?>
                    </li>
                </ul>
            </div>
            <div class="orderItemDetails" style="border: 1px solid #F3F2F1;">
                <ul class="noeffect">
                        <?php foreach ($orders['OrderItem'] as $order) { ?>
                        <li class="newitem">
                                <?php echo "<span class='item'>" . $order['quantity'] . ' ' . @$order['Size']['size'] . ' ' . $order['Item']['name'] . "</span>"; ?>

                                <?php if (!empty($order['OrderPreference'])) { ?>
                                <ul class="addonlist">
                                    <li class='offeritem'>Preferences</li>
                                        <?php
                                        $preference = array();
                                        foreach ($order['OrderPreference'] as $Preferences) {
                                            ?>
                                        <li>
                                        <?php
                                        if (!empty($Preferences['SubPreference']['name'])) {
                                            echo $Preferences['SubPreference']['name'];
                                        }
                                        ?>
                                        </li>
                                <?php } ?>
                                </ul>
            <?php } ?>



                                    <?php if (!empty($order['OrderTopping'])) { ?>
                                <ul class="addonlist">
                                    <li class='offeritem'>Add-ons</li>
                                        <?php foreach ($order['OrderTopping'] as $topping) { ?>
                                        <li>
                                        <?php
                                        if ($topping['addon_size_id'] == 0) {
                                            echo '1 ' . $topping['Topping']['name'];
                                        } else {
                                            echo $topping['AddonSize']['size'] . ' ' . $topping['Topping']['name'];
                                        }
                                        ?>
                                        </li>
                                <?php } ?>
                                </ul>
                            <?php } ?>
            <?php
            if (!empty($order['OrderOffer'])) {
                echo "<ul class='offers'><li class='offeritem'>Offer Items </li>";
                foreach ($order['OrderOffer'] as $offer) {
                    echo '<li>' . $offer['quantity'] . ' ' . @$offer['Size']['size'] . ' ' . $offer['Item']['name'] . '</li>';
                }
                echo '</ul>';
            }
            ?>


                        </li>
        <?php } ?>
                </ul>
            </div>
        </div>
    <?php
    }
} else {
    echo 'No Orders Found';
}
?>
