<style>
    .title-box,.share-button.clearfix{
        float: right;
    }
</style>
<?php
$url = HTTP_ROOT;
$imageurl = HTTP_ROOT . 'storeLogo/' . $store_data_app['Store']['store_logo'];
$encrypted_storeId = $this->Encryption->encode($_SESSION['store_id']); // Encrypted Store Id
$encrypted_merchantId = $this->Encryption->encode($_SESSION['merchant_id']);
$finalItem = $this->Common->taxCalculation($finalItem);
$desc = '';
$total_sum = 0;
$total_of_items = 0;
$ordertype = "";
$total_of_extra = 0;
$totaltaxPrice = 0;
$ItemOfferArray = $itemDisplayArray = array();
foreach ($finalItem as $session_key => $item) {
    //for share button start
    if (isset($item['Item']['OfferItemName'])) {
        $data = strip_tags($item['Item']['OfferItemName']);
        $offerItemName = explode('x', $data);
        unset($offerItemName[0]);
        $offerName = implode("<br/>", $offerItemName);
    }
    if (isset($offerName)) {
        $desc .= $item['Item']['quantity'] . 'X' . $item['Item']['name'] . ' ( Offer Items: ' . $offerName . ' ) @ ' . $this->Common->amount_format($item['Item']['final_price']) . ', ';
    } else {
        $desc .= $item['Item']['quantity'] . 'X' . $item['Item']['name'] . ' @ ' . $this->Common->amount_format($item['Item']['final_price']) . ', ';
    }
    //for share button end
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
if (isset($total_of_items)) {
    $itemDisplayArray['sub_total'] = $this->Common->amount_format($total_of_items);
}
if ($ItemOfferArray) {
    $itemDisplayArray['free_item_array'] = $ItemOfferArray;
}
if ($totaltaxPrice) {
    if ($totaltaxPrice >= 0) {
        $totaltaxPrice = $totaltaxPrice;
    } else {
        $totaltaxPrice = $totaltaxPrice = '0.00';
    }
    $_SESSION['taxPrice'] = $totaltaxPrice;
} else {
    $_SESSION['taxPrice'] = '0.00';
}
$itemDisplayArray['tax'] = $_SESSION['taxPrice'];
$ordertype = $this->Session->read('Order.order_type');
$ordertype = ($ordertype) ? $ordertype : null;
if (empty($ordertype)) {
    $ordertype = $this->Session->read('ordersummary.order_type');
}
if ($this->Session->check('Zone.fee') && $ordertype == 3) {
    if ($this->Session->check('Zone.fee')) {
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
if (isset($_SESSION['orderOverview']['Coupon'])) {
    $Couponcode = $_SESSION['orderOverview']['Coupon']['Coupon']['coupon_code'];
    $itemDisplayArray['coupon_code'] = ($Couponcode) ? $Couponcode : '';
    $discount_amount = 0;
    if (isset($_SESSION['orderOverview']['Coupon']['Coupon'])) {
        if ($_SESSION['orderOverview']['Coupon']['Coupon']['discount_type'] == 1) { // Price
            $discount_amount = $_SESSION['orderOverview']['Coupon']['Coupon']['discount'];
        } else { // Percentage
            $discount_amount = $total_sum * $_SESSION['orderOverview']['Coupon']['Coupon']['discount'] / 100;
        }
    }

    if ($total_sum < $discount_amount) {
        $discount_amount = $total_sum;
    }
    $itemDisplayArray['coupon_discount_amount'] = $this->Common->amount_format($discount_amount);
    $total_sum = $total_sum - $discount_amount;
    $_SESSION['Discount'] = $discount_amount;
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
    $tipamount = $this->Session->read('orderOverview.tip');
    $tipOption = $this->Session->read('Cart.tip_option');
    $tipSelect = $this->Session->read('Cart.tip_select');
    $tipLabel = '';
    $tipAmount = '';
    if ($tipOption == 0) {
        $tipLabel = 'No Tip';
        $tipAmount = '';
    } else if ($tipOption == 1) {
        $tipLabel = 'Tip With Cash';
        $tipAmount = '';
    } else if ($tipOption == 2) {
        $tipLabel = 'Tip With Card: ';
        $tipAmount = $this->Common->amount_format($tipamount);
    } else {
        $tipLabel = 'Tip % (' . $tipSelect . '%): ';
        $tipAmount = $this->Common->amount_format($tipamount);
    }
    $itemDisplayArray['tip_label'] = $tipLabel;
    $itemDisplayArray['tip_amount'] = $tipAmount;
    $total_sum = $total_sum + $tipamount;
}
$itemDisplayArray['total'] = $this->Common->amount_format($total_sum);
$_SESSION['Cart']['grand_total_final'] = $this->Common->amount_format($total_sum, true);
$itemDisplayArray['item'] = $this->Common->reOrgranizeCart($itemDisplayArray);
?>
<div class="orderId" style="float: left; font-size: 15px;font-style: italic;font-weight: bold;" >
    <span>Order ID :
        <?php echo $this->Session->read('orderOverview.orderID'); ?>
    </span><br>
    <span>
        <?php echo ($_SESSION['Order']['order_type'] == 2) ? 'Pick Up' : 'Delivery'; ?> Date/Time : <?php echo $this->Common->storeTimeFormateUser($_SESSION['Order']['store_pickup_date'] . ' ' . $_SESSION['Order']['store_pickup_time'], true); ?>
    </span>
</div>
<div class="share-button clearfix">
    <span>
        <a  class='twitter-share' target="blank" href= "http://twitter.com/share?text=Items : <?php echo $desc; ?> Coupon Discount : -<?php echo @$itemDisplayArray['coupon_discount_amount']; ?>, Total Payable Amount : <?php echo @$itemDisplayArray['total']; ?>&url=<?php echo $url; ?>&via=<?php echo $_SESSION['storeName']; ?>"><?php echo $this->Html->image('tw-share-button.png', array('alt' => 'twshare')); ?> </a>
    </span>
    <span>
        <a class='share_button'
           desc="Items : <?php echo $desc; ?> Coupon Discount : -<?php echo @$itemDisplayArray['coupon_discount_amount']; ?>, Total Payable Amount : <?php echo @$itemDisplayArray['total']; ?>" >
               <?php echo $this->Html->image('fb-share-button.png', array('alt' => 'fbshare')); ?>
        </a>
    </span>
</div>
<div class="clearfix"></div>
<div>
    <style>
        body { font-family:Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;font-size:12px;font-weight:300;}
        .iodr-table { width:100%;border:3px solid #000 !important;background:#ffffff;}
        .iodr-table input[type="text"],
        .iodr-table select { width:120px !important;margin-right:5px;border:1px solid rgba(155, 155, 155, 0.4);float:left;font-size:14px;padding:8px;}
        .iodr-table td { padding:1px 4px;font-size:14px;}
        .iodr-table .small-txt td { font-size:13px;}
        .iodr-table .seperator-box td { border-top:1px dashed #000000;padding:4px;}
        .editable-form { margin-bottom:15px;}
        .iodr-table .common-bold { font-size:14px;font-weight:600 !important;}
        .iodr-table .singleItemRemove { color:#381f02 !important;}
        .iodr-table .common-bold-cat { font-weight:bold !important;}
        .iodr-table .singleItemRemove b { display: none;}
    </style>
    <table class="iodr-table">
        <tr>
            <td>
                <table style="width:100%;" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="width:15%"><strong class="common-bold">Qty</strong></td>
                        <td style="width:70%"><strong class="common-bold">Item</strong></td>
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
                            echo '<tr><td>' . $orderDetail['item_quantity'] . '</td><td>' . $orderDetail['item_size'] . ' ' . $orderDetail['item_name'] . ' x ' . $orderDetail['item_actual_price'] . '</td><td>' . ($orderDetail['item_total_price_with_quantity']) . '<strong>' . $orderDetail['tax_label'] . '</strong></td></tr>';
                            if (!empty($orderDetail['subpreference_array'])) {//subpreference
                                foreach ($orderDetail['subpreference_array'] as $subPreference) {
                                    $unitPrice = $subPreference['price'] / $subPreference['size'];
                                    $price = $unitPrice * $subPreference['size'];
                                    $price = ($price > 0) ? $this->Common->amount_format($price * $orderDetail['item_quantity']) : '';
                                    $showIndividualPrice = ($unitPrice > 0) ? ' x ' . $this->Common->amount_format($unitPrice) : '';
                                    echo '<tr class="small-txt"><td>&nbsp;</td><td>+' . $subPreference['size'] . ' ' . $subPreference['name'] . $showIndividualPrice . '</td><td>' . $price . '</td></tr>';
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
                                    echo '<tr class="small-txt"><td>&nbsp;</td><td>+' . $defaultTopping['size'] . ' ' . $defaultTopping['name'] . $showIndividualPrice . '</td><td>' . $defaultToppingPrice . '</td></tr>';
                                }
                            }
                            if (!empty($orderDetail['paid_topping_array'])) {//paid toppings
                                foreach ($orderDetail['paid_topping_array'] as $paidTopping) {
                                    $paidToppingPrice = $paidTopping['price'] * $paidTopping['size'];
                                    $paidToppingPrice = ($paidToppingPrice > 0) ? $this->Common->amount_format($paidToppingPrice * $orderDetail['item_quantity']) : '';
                                    $showIndividualPrice = ($paidTopping['price'] > 0) ? ' x ' . $this->Common->amount_format($paidTopping['price']) : '';
                                    echo '<tr class="small-txt"><td>&nbsp;</td><td>+' . $paidTopping['size'] . ' ' . $paidTopping['name'] . $showIndividualPrice . '</td><td>' . $paidToppingPrice . '</td></tr>';
                                }
                            }
                            if (!empty($orderDetail['offer_item_name'])) {
                                echo '<tr class="small-txt"><td>&nbsp;</td><td colspan="2">Promotional Offer:<br>' . $orderDetail['offer_item_name'] . '</td></tr>';
                            }
                            echo '<tr class="small-txt"><td colspan="2"></td><td style="font-weight:initial"><strong style="font-size:14px;">' . $orderDetail['item_price'] . '<strong></td></td></tr>';
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
                        <td class="sub-total" core-sub-total="<?php echo $itemDisplayArray['sub_total']; ?>"><strong><?php echo $itemDisplayArray['sub_total']; ?></strong></td>
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
                        echo '<tr class="seperator-box"><td colspan="2">Coupon Code (' . $itemDisplayArray['coupon_code'] . ')</td><td>' . $itemDisplayArray['coupon_discount_amount'] . '</td></tr>';
                    }
                    if (!empty($itemDisplayArray['tip_label'])) {
                        ?>
                        <tr class="seperator-box">
                            <td colspan="2"><?php echo $itemDisplayArray['tip_label']; ?></td>
                            <td>
                                <span class="tip-amnt tipFinal"><?php echo $itemDisplayArray['tip_amount']; ?></span>
                            </td>
                        </tr>
                    <?php } if (!empty($itemDisplayArray['tax']) && $itemDisplayArray['tax'] > 0) { ?>
                        <tr class="seperator-box">
                            <td colspan="2">Tax</td>
                            <td><?php echo $this->Common->amount_format($itemDisplayArray['tax']); ?></td>
                        </tr>
                    <?php } ?>
                    <tr class="seperator-box">
                        <td colspan="2"><strong style="font-size:18px;">Total</strong></td>
                        <td><strong style="font-size:18px;"><?php echo $itemDisplayArray['total']; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</div>
<div id="fb-root"></div>
<?php $this->Common->deleteSession(); ?>
<script>
    window.fbAsyncInit = function () {
        FB.init({appId: '595206160619283', status: true, cookie: true,
            xfbml: true});
    };
    (function () {
        var e = document.createElement('script');
        e.async = true;
        e.src = document.location.protocol +
                '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
    }());
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.share_button').click(function (e) {
            description = $(this).attr('desc');
            e.preventDefault();
            FB.ui(
                    {
                        method: 'feed',
                        name: 'Order Detail',
                        link: '<?php echo $url; ?>',
                        picture: '<?php echo $imageurl; ?>',
                        caption: 'Full Order Summary - <?php echo $_SESSION['storeName']; ?>',
                        description: description,
                        message: ''
                    });
        });
    });
</script>
