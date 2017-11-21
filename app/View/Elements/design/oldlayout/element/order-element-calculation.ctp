<?php
//pr($_SESSION);
$url = HTTP_ROOT;
$encrypted_storeId = $this->Encryption->encode($_SESSION['store_id']); // Encrypted Store Id
$encrypted_merchantId = $this->Encryption->encode($_SESSION['merchant_id']);
$finalItem = $this->Common->taxCalculation($finalItem);

$total_sum = 0;
$total_of_items = 0;
$ordertype = "";
$total_of_extra = 0;
$totaltaxPrice = 0;
$ItemOfferArray = $itemDisplayArray = array();
if ($finalItem) {
    //prx($finalItem);
    foreach ($finalItem as $session_key => $item) {
        $itemDisplayArray['item'][$session_key] = array();
        $storetaxInfo = array();
        $CatName = '';
        $CategoryName = $this->Common->getCategoryName($item['Item']['categoryid']);
        if ($CategoryName) {
            $CatName = $CategoryName['Category']['name'];
        }
        $taxlabel = '';
        if ($item['Item']['taxamount'] > 0) {
            $taxlabel = "T";
            $totaltaxPrice = $totaltaxPrice + $item['taxCalculated'];
        }

        $total_sum = $total_sum + $item['Item']['final_price'];
        $total_of_items = $total_of_items + $item['Item']['final_price'];
        $itemDisplayArray['item'][$session_key]['category_id'] = $item['Item']['categoryid'];
        $itemDisplayArray['item'][$session_key]['session_key'] = $session_key;
        $itemDisplayArray['item'][$session_key]['category_name'] = $CatName;
        $itemDisplayArray['item'][$session_key]['tax_label'] = $taxlabel;
        $Interval = "";
        if (isset($item['Item']['interval_id'])) {
            $intervalId = $item['Item']['interval_id'];
            $Interval = $this->Common->getIntervalName($intervalId);
        }
        $itemDisplayArray['item'][$session_key]['interval'] = $Interval;
        $itemDisplayArray['item'][$session_key]['item_quantity'] = $item['Item']['quantity'];
        $itemDisplayArray['item'][$session_key]['item_size'] = @$item['Item']['size'];
        $itemDisplayArray['item'][$session_key]['item_type'] = @$item['Item']['item_type'];
        $itemDisplayArray['item'][$session_key]['item_name'] = @$item['Item']['name'];
        $itemDisplayArray['item'][$session_key]['item_price'] = $this->Common->amount_format($item['Item']['final_price']);
        $itemDisplayArray['item'][$session_key]['item_actual_price'] = $this->Common->amount_format($item['Item']['actual_price']);
        $item_total_price_with_quantity = $item['Item']['actual_price'] * $item['Item']['quantity'];
        $itemDisplayArray['item'][$session_key]['item_total_price_with_quantity'] = $this->Common->amount_format($item_total_price_with_quantity);
        if (!empty($item['Item']['OfferType'])) {//fixed price promotion
            $itemDisplayArray['item'][$session_key]['item_actual_price'] = (!empty($item['Item']['OfferItemPrice'])) ? $this->Common->amount_format($item['Item']['OfferItemPrice']) : '';
            $itemDisplayArray['item'][$session_key]['item_total_price_with_quantity'] = (!empty($item['Item']['OfferItemPrice'])) ? $this->Common->amount_format($item['Item']['OfferItemPrice'] * $item['Item']['quantity']) : '';
        }
        $itemDisplayArray['item'][$session_key]['offer_item_name'] = @$item['Item']['OfferItemName'];
        if (!empty($item['Item']['subPreferenceOld'])) {
            $itemDisplayArray['item'][$session_key]['subpreference_array'] = $item['Item']['subPreferenceOld'];
        }
        if (!empty($item['Item']['default_topping'])) {
            $itemDisplayArray['item'][$session_key]['default_topping_array'] = $item['Item']['default_topping'];
        }
        if (!empty($item['Item']['paid_topping'])) {
            $itemDisplayArray['item'][$session_key]['paid_topping_array'] = $item['Item']['paid_topping'];
        }
        if (!empty($item['Item']['freeQuantity'])) {
            $ItemOfferArray['item'][$session_key]['itemName'] = @$item['Item']['size'] . ' ' . @$item['Item']['type'] . ' ' . $item['Item']['name'];
            $ItemOfferArray['item'][$session_key]['freeQuantity'] = @$item['Item']['freeQuantity'];
            $ItemOfferArray['item'][$session_key]['price'] = $this->Common->amount_format($item['Item']['SizePrice']);
        }
    }
}

if (isset($total_of_items)) {
    $itemDisplayArray['sub_total'] = $total_of_items;
}
if ($ItemOfferArray) {
    $itemDisplayArray['free_item_array'] = $ItemOfferArray;
}
if ($totaltaxPrice) {
    if ($totaltaxPrice >= 0) {
        $totaltaxPrice = $totaltaxPrice;
    } else {
        $totaltaxPrice = $totaltaxPrice = 0.00;
    }
    $_SESSION['taxPrice'] = $totaltaxPrice;
} else {
    $_SESSION['taxPrice'] = 0.00;
}
$itemDisplayArray['tax'] = $this->Common->amount_format($_SESSION['taxPrice']);
$ordertype = $this->Session->read('Order.order_type');
$ordertype = ($ordertype) ? $ordertype : null;
if (empty($ordertype)) {
    $ordertype = $this->Session->read('ordersummary.order_type');
}
if ($this->Session->check('Zone.fee') && $ordertype == 3) {
    if ($this->Session->check('Zone.fee')) {
        $_SESSION['delivery_fee'] = $this->Session->read('Zone.fee');
        $total_of_extra = $total_of_extra + $this->Session->read('Zone.fee');
        $itemDisplayArray['zone_fee'] = $this->Session->read('Zone.fee');
    }
}

if (isset($_SESSION['final_service_fee']) && ($_SESSION['final_service_fee'] > 0)) {
    $serviceFee = $this->Session->read('final_service_fee');
    $total_of_extra = $total_of_extra + $serviceFee;
    $itemDisplayArray['service_fee'] = isset($serviceFee) ? $this->Common->amount_format($serviceFee) : $this->Common->amount_format(0);
}

if (!empty($storeSetting['StoreSetting']['discount_on_extra_fee'])) {
    $total_sum = $total_of_items + $total_of_extra;
} else {
    $total_sum = $total_of_items;
}
if (isset($_SESSION['Coupon'])) {
    $Couponcode = $_SESSION['Coupon']['Coupon']['coupon_code'];
    $itemDisplayArray['coupon_code'] = ($Couponcode) ? $Couponcode : '';
    $discount_amount = 0;
    if (isset($_SESSION['Coupon'])) {
        if ($_SESSION['Coupon']['Coupon']['discount_type'] == 1) { // Price
            $discount_amount = $_SESSION['Coupon']['Coupon']['discount'];
        } else { // Percentage
            $discount_amount = $total_sum * $_SESSION['Coupon']['Coupon']['discount'] / 100;
        }
    }

    if ($total_sum < $discount_amount) {
        $discount_amount = $total_sum;
    }
    $itemDisplayArray['coupon_discount_amount'] = $this->Common->amount_format($discount_amount);
    $total_sum = $total_sum - $discount_amount;
    $_SESSION['Discount'] = $discount_amount;
}
if (isset($_SESSION['tip']) && ($_SESSION['tip'] > 0)) {
    $default_tip_option = $this->Session->read('Cart.tip_option');
    $default_tip_value = $this->Session->read('Cart.tip_value');
    $default_tip_select = $this->Session->read('Cart.tip_select');
    $default_tip = $this->Session->read('Cart.tip');

    if ($default_tip_option != '' && $default_tip_option != 0 && $default_tip_option == 3) {
        $default_tip = ($default_tip_select / 100) * $total_of_items;
        $_SESSION['Cart']['tip'] = $default_tip;
    }

    $tipValueDisplay = '';
    $tipSelectDisplay = '';
    if ($default_tip_option == 2) {
        $tipValueDisplay = '';
        $tipSelectDisplay = 'hidden';
    } else if ($default_tip_option == 3) {
        $tipValueDisplay = 'hidden';
        $tipSelectDisplay = '';
    } else {
        $tipValueDisplay = 'hidden';
        $tipSelectDisplay = 'hidden';
    }


    $tipTextDisabled = true;
    if ($default_tip_option == 2) {
        $tipTextDisabled = false;
    } else {
        $tipTextDisabled = true;
    }


    if ($default_tip_option == 0 || $default_tip_option == 1) {
        $tipValueDisplay = 'hidden';
    } else {
        $tipValueDisplay = '';
    }


    $tipOptions = array(0 => 'No Tip', 1 => 'Tip With Cash', 2 => 'Tip With Card', 3 => 'Tip %');
    $itemDisplayArray['tip_amount'] = $this->Common->amount_format($default_tip);
}
if (empty($storeSetting['StoreSetting']['discount_on_extra_fee'])) {
    $total_sum = $total_sum + $total_of_extra;
}
if ($totaltaxPrice) {
    $total_sum = $total_sum + $totaltaxPrice;
}
if (!empty($ItemDiscount)) {
    $total_sum = $total_sum - $ItemDiscount;
}
if (isset($_SESSION['tip']) && ($_SESSION['tip'] > 0)) {
    $tipamount = @$_SESSION['Cart']['tip'];
    if ($tipamount > 0) {
        $total_sum = $total_sum + $tipamount;
    }
}
$itemDisplayArray['total'] = $this->Common->amount_format($total_sum);
$_SESSION['Cart']['grand_total_final'] = $this->Common->amount_format($total_sum, true);
$itemDisplayArray['item'] = $this->Common->reOrgranizeCart($itemDisplayArray);
?>
<style>
    body { font-family:Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;font-size:12px;font-weight:300;}
    .iodr-table { width:100%;border:3px solid #000 !important;}
    .iodr-table input[type="text"],
    .iodr-table select { width:120px !important;border:1px solid rgba(155, 155, 155, 0.4);float:left;font-size:14px;padding:8px;margin-left:5px;}
    .iodr-table td { padding:1px 4px;font-size:14px;}
    .iodr-table tr.small-items td { font-size:13px;}
    .iodr-table .seperator-box td { border-top:1px dashed #000000 !important; -webkit-border-top:1px dashed #000000 !important; padding:4px;}
    .iodr-table .tip-amnt { padding:8px 0;display:inline-block;}
    .editable-form { margin-bottom:15px;}
    .iodr-table .common-bold { font-size:14px;}
    .iodr-table .common-bold-cat { font-weight:bold !important;}
    .iodr-table .singleItemRemove { color:#381f02 !important;}
    .iodr-table .offerItems,
    .iodr-table .offerItems a { display:block;cursor:auto;position:relative;}
    .iodr-table .offerItems a b { color:#886c58;font-size:14px;cursor:pointer;position:absolute;top:0;right:0;}
    .iodr-table .offerItems br { display:none;}
</style>
<table class="iodr-table">
    <?php if (DESIGN == 4) { ?>
        <tr>
            <td>
                <table style="width:100%;border-bottom:1px dashed #000000;">
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><strong class="common-bold"><?php echo ($ordertype == 3) ? 'Delivery' : 'Pick Up'; ?> Date: <?php echo $this->Session->read('Order.store_pickup_date'); ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong class="common-bold"><?php echo ($ordertype == 3) ? 'Delivery' : 'Pick Up'; ?> Time: <?php echo $this->Common->storeTimeFormate($this->Session->read('Order.store_pickup_time')); ?></strong></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
    <?php } ?>
    <tr>
        <td>
            <table style="width:100%;" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="3"><strong>Order Detail</strong></td>
                </tr>
                <tr>
                    <td style="width: 15%"><strong class="common-bold">Qty</strong></td>
                    <td style="width: 60%"><strong class="common-bold">Item</strong></td>
                    <td><strong class="common-bold">Price</strong></td>
                </tr>
                <?php
                if (!empty($itemDisplayArray['item'])) {
                    $catArray = array();
                    foreach ($itemDisplayArray['item'] as $keyIndex => $orderDetail) {
                        if (!in_array($orderDetail['category_id'], $catArray)) {
                            echo '<tr><td>&nbsp;</td><td colspan="2"><strong class="common-bold common-bold-cat">' . $orderDetail['category_name'] . '</strong></td></tr>';
                        }
                        $catArray[] = $orderDetail['category_id'];
                        $session_key = $orderDetail['session_key'];
                        echo '<tr><td>' . $orderDetail['item_quantity'] . '</td><td>' . $orderDetail['item_size'] . ' ' . $orderDetail['item_name'] . ' x ' . $orderDetail['item_actual_price'] . '</td><td>' . ($orderDetail['item_total_price_with_quantity']) . '<strong>' . $orderDetail['tax_label'] . '</strong>' . '<item class="itemRemove" id=' . "$session_key" . ' style="float:right;"><a href="javascript:void(0)" title="Remove Item"><i class="fa fa-times"></i></a></item></td></tr>';
                        if (!empty($orderDetail['subpreference_array'])) {//subpreference
                            foreach ($orderDetail['subpreference_array'] as $subPreference) {
                                $unitPrice = $subPreference['price'] / $subPreference['size'];
                                $price = $unitPrice * $subPreference['size'];
                                $price = ($price > 0) ? $this->Common->amount_format($price * $orderDetail['item_quantity']) : '';
                                $showIndividualPrice = ($unitPrice > 0) ? ' x ' . $this->Common->amount_format($unitPrice) : '';
                                echo '<tr class="small-items"><td>&nbsp;</td><td>+' . $subPreference['size'] . ' ' . $subPreference['name'] . $showIndividualPrice . '</td><td>' . $price . '</td></tr>';
                            }
                        }
                        if (!empty($orderDetail['default_topping_array'])) {//default toppings
                            foreach ($orderDetail['default_topping_array'] as $defaultTopping) {
                                if ($defaultTopping['size'] == 1) {//unit 1 is default
                                    $defaultTopping['price'] = 0.00;
                                } else {
                                    $defaultTopping['price'] = $defaultTopping['price'] * $defaultTopping['size'];
                                }
                                $showIndividualPrice = ($defaultTopping['price'] > 0) ? ' x ' . $this->Common->amount_format($defaultTopping['price']) : '';
                                $defaultToppingPrice = ($defaultTopping['price'] > 0) ? $this->Common->amount_format($defaultTopping['price'] * $orderDetail['item_quantity']) : '';
                                echo '<tr class="small-items"><td>&nbsp;</td><td>+' . $defaultTopping['size'] . ' ' . $defaultTopping['name'] . $showIndividualPrice . '</td><td>' . $defaultToppingPrice . '</td></tr>';
                            }
                        }
                        if (!empty($orderDetail['paid_topping_array'])) {//paid toppings
                            foreach ($orderDetail['paid_topping_array'] as $paidTopping) {
                                $paidToppingPrice = $paidTopping['price'] * $paidTopping['size'];
                                $paidToppingPrice = ($paidToppingPrice > 0) ? $this->Common->amount_format($paidToppingPrice * $orderDetail['item_quantity']) : '';
                                $showIndividualPrice = ($paidTopping['price'] > 0) ? ' x ' . $this->Common->amount_format($paidTopping['price']) : '';
                                echo '<tr class="small-items"><td>&nbsp;</td><td>+' . $paidTopping['size'] . ' ' . $paidTopping['name'] . $showIndividualPrice . '</td><td>' . $paidToppingPrice . '</td></tr>';
                            }
                        }
                        if (!empty($orderDetail['offer_item_name'])) {
                            echo '<tr><td>&nbsp;</td><td colspan="2"><item id=' . "$session_key" . ' style="font-size: 12px;" class="offerItems">Promotional Offer:' . $orderDetail['offer_item_name'] . '</item></td></tr>';
                        }
                        echo '<tr><td colspan="2"></td><td style="font-weight:initial"><strong>' . $orderDetail['item_price'] . '</strong></td></td></tr>';
                    }
                }
                if (!empty($itemDisplayArray['free_item_array']['item'])) {
                    echo '<tr class="seperator-box"><td colspan="3"><strong class="common-bold">Free Item</strong></td></tr>';
                    foreach ($itemDisplayArray['free_item_array']['item'] as $freeItem) {
                        if ($freeItem['freeQuantity'] > 0) {
                            echo '<tr><td>' . $freeItem['freeQuantity'] . '</td><td colspan="2">' . $freeItem['itemName'] . '</td></tr>';
                        }
                    }
                }
                ?>
                <tr class="seperator-box">
                    <td colspan="2"><strong>Sub-Total</strong></td>
                    <td class="sub-total" core-sub-total="<?php echo $itemDisplayArray['sub_total']; ?>"><strong><?php echo $this->Common->amount_format($itemDisplayArray['sub_total']); ?></strong></td>
                </tr>
                <?php
                if (!empty($itemDisplayArray['zone_fee']) && $itemDisplayArray['zone_fee'] > 0) {
                    echo '<tr class="seperator-box"><td colspan="2">Delivery Fee</td>';
                    echo '<td>' . $this->Common->amount_format($itemDisplayArray['zone_fee']) . '</td></tr>';
                }
                if (isset($_SESSION['final_service_fee']) && ($_SESSION['final_service_fee'] > 0)) {
                    echo '<tr class="seperator-box"><td colspan="2">Service Fee</td>';
                    echo '<td>' . $itemDisplayArray['service_fee'] . '</td></tr>';
                }
                if (!empty($itemDisplayArray['coupon_code'])) {
                    echo '<tr class="seperator-box"><td colspan="2">Coupon Code (' . $itemDisplayArray['coupon_code'] . ')</td><td><div style="min-width:100px;">' . $itemDisplayArray['coupon_discount_amount'] . '<item class="pull-right">' . $this->Html->link('<i class="fa fa-times"></i>', array('controller' => 'products', 'action' => 'removeCoupon'), array('escape' => false, 'confirm' => 'Are you sure to delete coupon?')) . '</item></div></td></tr>';
                } else {
                    echo $this->element('orderoverview/coupon');
                }
                if (isset($_SESSION['tip']) && ($_SESSION['tip'] > 0)) {
                    ?>
                    <tr class="seperator-box">
                        <td>Add Tip</td>
                        <td colspan="2" style="text-align:right;">
                            <?php
                            echo $this->Form->input('Order.tip', array('type' => 'select', 'class' => 'tip-select inbox ', 'label' => false, 'style' => 'display: inline-block; float: none; font-size: 12px; margin-left: 0;', 'div' => false, 'options' => $tipOptions, 'default' => ($default_tip_option != '') ? $default_tip_option : ''));
                            $storeID = $_SESSION['store_id'];
                            $tipData = $this->Common->getStoreTipFront($storeID);
                            echo $this->Form->input('Order.tip_select', array('type' => 'select', 'class' => 'tip inbox ' . $tipSelectDisplay, 'options' => $tipData, 'label' => false, 'div' => false, 'style' => 'display: inline-block; float: none; font-size: 12px; width: 60px !important; ', 'default' => ($default_tip_select != '') ? $default_tip_select : ''));
                            ?>
                            <?php
                            echo $this->Form->input('Order.tip_value', array('type' => 'text', 'class' => 'tip inbox ' . $tipValueDisplay, 'Placeholder' => '', 'label' => false, 'style' => 'display: inline-block; float: none; font-size: 12px;', 'div' => false, 'maxlength' => '10', 'disabled' => $tipTextDisabled, 'value' => ($itemDisplayArray['tip_amount'] != '') ? $itemDisplayArray['tip_amount'] : ''));
                            ?>
                        </td>
                    </tr>
                <?php } if (!empty($_SESSION['taxPrice'])) { ?>
                    <tr class="seperator-box">
                        <td colspan="2">Tax</td>
                        <td><?php echo $itemDisplayArray['tax']; ?></td>
                    </tr>
                <?php } ?>
                <tr class="seperator-box">
                    <td colspan="2"><strong style="font-size:18px;">Total</strong></td>
                    <td><strong style="font-size:18px;"><?php echo $itemDisplayArray['total']; ?></strong></td>
                </tr>
                <?php if (DESIGN == 4) { ?>
                    <tr class="seperator-box">
                        <?php
                        if (AuthComponent::User()) {
                            if (!empty($storeSetting['StoreSetting']['save_to_order_btn'])) {
                                ?>
                                <td id="desktop_save" colspan="3"><?php echo $this->Form->button('SAVE TO ORDERS', array('type' => 'button', 'onclick' => "checkMandatoryItem()", 'class' => 'btn green-btn', 'style' => 'float:right;')); ?>
                                </td>
                                <?php
                            }
                        }
                        ?>
                    </tr>
                <?php } ?>
            </table>
        </td>
    </tr>
</table>
