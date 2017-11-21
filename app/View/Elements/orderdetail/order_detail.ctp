<table class="table tablesorter">
    <thead>
        <tr>
            <th class="th_checkbox" colspan="5" style="text-align:left;">
                Order Id : <?php echo $orderDetail[0]['Order']['order_number']; ?>
                | Cost : <?php echo $this->Common->amount_format($total_amount);?>
                <?php
                if ($orderDetail[0]['Order']['tax_price']) {
                    echo "| Tax :" . $this->Common->amount_format($orderDetail[0]['Order']['tax_price']);
                }
                ?>
                | Status : <?php echo $data = $this->requestAction('/orders/ajaxRequest/' . $orderDetail[0]['Order']['order_status_id'] . '') ?>
                </br> Name : <?php echo $orderAttr['enduser_name']; ?>
                </br> Contact # <?php echo $orderAttr['enduser_phone']; ?>
                <?php echo $orderAttr['address']; ?>
                <br/>
                Order Type : <?php echo $orderAttr['orderType'] . '-' . $orderAttr['paymentStatus']; ?>
                <?php if (!empty($orderDetail[0]['OrderPayment']['last_digit'])) { ?>
                    &nbsp;| Card Number : <?php echo 'XXXXXXXXXXXX'.$orderDetail[0]['OrderPayment']['last_digit']; ?>
                <?php } ?>
                &nbsp;| Created :&nbsp;&nbsp;<?php echo $orderAttr['created_time']; ?>
                <?php echo $orderAttr['pickup_time'] ?>
            </th>
        </tr>
    </thead>
</table>

<table class="table table-bordered table-hover table-striped tablesorter table-net-record-modify">


    <thead>
        <tr>
            <th class="th_checkbox">Item</th>
            <th class="th_checkbox">Size</th>
            <th class="th_checkbox" colspan="2">Preference</th>
            <th class="th_checkbox">Add-ons</th>
            <th class="th_checkbox">Price($)</th>
        </tr>
    </thead>

    <tbody class="dyntable">
        <?php
        $i = 0;
        $totalItemPrice = 0.00;
        foreach ($orderDetail[0]['OrderItem'] as $key => $item) {

            $class = ($i % 2 == 0) ? ' class="active"' : '';
            ?>
            <tr>
                <td>
                    <span style="line-height: 30px;">
                        <?php
                        $Interval = "";
                        if (isset($item['interval_id'])) {
                            $intervalId = $item['interval_id'];
                            $Interval = $this->Common->getIntervalName($intervalId);
                        }


                        echo $item['quantity'];
                        ?> x <?php
                        echo $item['Item']['category']['name'] . "-" . $item['Item']['name'];
                        echo ($Interval) ? "(" . $Interval . ")" : "";
                        ?>
                        <?php
                        if (isset($item['OrderOffer'])) {
                            echo "<br>";
                            foreach ($item['OrderOffer'] as $j => $offer) {
                                $offeroitem = "&nbsp;&nbsp;";
                                if (isset($offer['quantity'])) {
                                    $offeroitem.=$offer['quantity'];
                                }
                                if (isset($offer['Size']['size'])) {
                                    $offeroitem.="x " . $offer['Size']['size'];
                                }
                                if ($offer['Item']['name']) {
                                    $offeroitem.=" " . $offer['Item']['name'] . "<br>";
                                }

                                echo $offeroitem;
                            }
                        }
                        ?>
                    </span>
                </td>

                <td>
                    <?php echo ($item['Size']) ? $item['Size']['size'] : "-"; ?>
                </td>
                <td colspan="2">
                    <?php
                    if (!empty($item['OrderPreference'])) {
                        foreach ($item['OrderPreference'] as $key => $opre) {
                            $preSize = $opre['size'];
                            echo $preSize . ' ' . $opre['SubPreference']['name'] . "<br>";
                        }
                    } else {
                        echo ' - ';
                    }
                    ?>
                </td>
                <td style="width: 300px; word-wrap: break-word; word-break: break-all;">
                    <?php
                    $Toppings = '';
                    if ($item['OrderTopping']) {
                        $Toppings = array();
                        foreach ($item['OrderTopping'] as $vkey => $toppingdetails) {
                            if (isset($toppingdetails['Topping']['name'])) {
                                $addonsize = $toppingdetails['addon_size_id'];
                                echo $addonsize . ' ' . $toppingdetails['Topping']['name'] . "<br>";
                            }
                        }
                    }
                    ?>
                </td>
                <td>
                    <?php
                    echo ($item['total_item_price']) ? $this->Common->amount_format($item['total_item_price']) : "-";
                    if ($item['total_item_price']) {
                        $totalItemPrice = $totalItemPrice + $item['total_item_price'];
                    }
                    ?>
                </td>
            </tr>
            <?php
            $i++;
        }
        ?>
        <?php
        if (!empty($orderDetail[0]['OrderItemFree'])) {
            ?>
            <tr>
                <th>Free Item</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>

            <?php
            foreach ($orderDetail[0]['OrderItemFree'] as $fkey => $itemfree) {
                ?>
                <tr>
                    <td><?php echo $itemfree['free_quantity'] . ' ' . $itemfree['Item']['name']; ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <?php
            }
        }
        ?>

        <tr>
            <td colspan="4" rowspan="7" style="vertical-align: top !important;"><b>Special Comment: </b><?php
                $orderComment = '';
                if (!empty($orderDetail['Order']['order_comments'])) {
                    $orderComment = $orderDetail['Order']['order_comments'];
                }
                echo $orderComment;
                ?></td>
            <td class="net-record-font-size">Sub-Total:</td>
            <td class="net-record-font-size"><?php echo $this->Common->amount_format($totalItemPrice + $data['Order']['tax_price']); ?></td>

        </tr>
        <tr>
            <td class="net-record-font-size">Tax:</td>
            <td class="net-record-font-size"><?php echo ($orderDetail[0]['Order']['tax_price']) ? $this->Common->amount_format($orderDetail[0]['Order']['tax_price']) : '-'; ?></td>
        </tr>

        <tr>
            <td class="net-record-font-size">Service Fee:</td>
            <td class="net-record-font-size"><?php echo ($orderDetail[0]['Order']['service_amount']) ? $this->Common->amount_format($orderDetail[0]['Order']['service_amount']) : '-'; ?></td>
        </tr>
        <?php if ($data['Order']['seqment_id'] != 2) { ?>
            <tr>
                <td class="net-record-font-size">Delivery Fee:</td>
                <td class="net-record-font-size"><?php echo ($orderDetail[0]['Order']['delivery_amount']) ? $this->Common->amount_format($orderDetail[0]['Order']['delivery_amount']) : '-'; ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td class="net-record-font-size">Coupon Discount:<br><?php echo ($orderDetail[0]['Order']['coupon_code']) ? "(" . $orderDetail[0]['Order']['coupon_code'] . ")" : ''; ?></td>
            <td class="net-record-font-size"
                style="color:#ff1a1a;"><?php echo ($orderDetail[0]['Order']['coupon_discount']) ? "-" . $this->Common->amount_format($orderDetail[0]['Order']['coupon_discount']) : '-'; ?></td>
        </tr>
        <?php
        $tipLabel = '';
        $tipAmount = '';
        if($orderDetail[0]['Order']['tip'] > 0 && $orderDetail[0]['Order']['tip_option'] == 0 && $orderDetail[0]['Order']['tip_percent'] == 0)
        {
            $tipLabel = 'Tip: ';
            $tipAmount = $this->Common->amount_format($orderDetail[0]['Order']['tip']);
        } else {
            if($orderDetail[0]['Order']['tip_option'] == 0) {
                $tipLabel = 'No Tip';
                $tipAmount = '';
            } else if($orderDetail[0]['Order']['tip_option'] == 1) {
                $tipLabel = 'Tip With Cash';
                $tipAmount = '';
            } else if($orderDetail[0]['Order']['tip_option'] == 2) {
                $tipLabel = 'Tip With Card: ';
                $tipAmount = $this->Common->amount_format($orderDetail[0]['Order']['tip']);
            } else {
                $tipLabel = 'Tip % (' . $orderDetail[0]['Order']['tip_percent'] . '%): ';
                $tipAmount = $this->Common->amount_format($orderDetail[0]['Order']['tip']);
            }
        }
        ?>
        <tr>
            <td class="net-record-font-size"><?php echo $tipLabel;?><br></td>
            <td class="net-record-font-size"><?php echo $tipAmount;?></td>
        </tr>
        <tr>
            <td class="net-record-font-size">Total:</td>
            <td class="net-record-font-size"><?php echo $this->Common->amount_format($total_amount); ?></td>
        </tr>        


    </tbody>
</table>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;


