<?php
$url = HTTP_ROOT;
$imageurl = HTTP_ROOT . 'storeLogo/' . $store_data_app['Store']['store_logo'];
$encrypted_storeId = $this->Encryption->encode($_SESSION['store_id']); // Encrypted Store Id
$encrypted_merchantId = $this->Encryption->encode($_SESSION['merchant_id']);
$desc = '';
$total_sum = 0;
$total_of_items = 0;
$ordertype = "";
$total_of_extra = 0;
//$finalItem = $this->Common->taxCalculation($finalItem);
foreach ($finalItem as $session_key => $item) {
    if (isset($item['Item']['OfferItemName'])) {
        $data = strip_tags($item['Item']['OfferItemName']);
        $offerItemName = explode('x', $data);
        unset($offerItemName[0]);
        $offerName = implode("<br/>", $offerItemName);
    }
    $ordertype = $item['order_type'];
    $total_sum = $total_sum + $item['Item']['final_price'];
    $total_of_items = $total_of_items + $item['Item']['final_price'];
    if (isset($offerName)) {
        $desc .= $item['Item']['quantity'] . 'X' . $item['Item']['name'] . ' ( Offer Items: ' . $offerName . ' ) @ $' . number_format($item['Item']['final_price'], 2) . ', ';
    } else {
        $desc .= $item['Item']['quantity'] . 'X' . $item['Item']['name'] . ' @ $' . number_format($item['Item']['final_price'], 2) . ', ';
    }
}
if (isset($_SESSION['orderOverview']['Coupon'])) {
    if ($_SESSION['orderOverview']['Coupon']['Coupon']['discount_type'] == 1) { // Price
        $discount_amount = $_SESSION['orderOverview']['Coupon']['Coupon']['discount'];
        $total_of_items = $total_of_items - $discount_amount;
    } else {
        $discount_amount = $total_of_items * $_SESSION['orderOverview']['Coupon']['Coupon']['discount'] / 100;
        $total_of_items = $total_of_items - $discount_amount;
    }
} else {
    $discount_amount = 0;
    $total_of_items = $total_of_items - $discount_amount;
}
$_SESSION['orderOverview']['Discount'] = $discount_amount;
if (isset($_SESSION['orderOverview']['delivery_fee']) && $ordertype == 3 && ($_SESSION['orderOverview']['delivery_fee'] > 0)) {
    $total_of_extra = $total_of_extra + $this->Session->read('orderOverview.delivery_fee');
}
if (isset($_SESSION['orderOverview']['service_fee']) && ($_SESSION['orderOverview']['service_fee'] > 0)) {
    $total_of_extra = $total_of_extra + $this->Session->read('orderOverview.service_fee');
}
$total_sum = $total_of_items + $total_of_extra;
?>
<div class="orderId" style="float: left; font-size: 15px;font-style: italic;font-weight: bold;" >
    <span>Order ID :
        <?php echo $this->Session->read('orderOverview.orderID'); ?>
    </span>
</div>
<div class="share-button clearfix">
    <span>
        <a  class='twitter-share' target="blank" href= "http://twitter.com/share?text=Items : <?php echo $desc; ?> Coupon Discount : -$<?php echo number_format($discount_amount, 2); ?>, Extra Charges: $<?php echo number_format($total_of_extra, 2); ?>, Total Payable Amount : $<?php echo number_format($total_sum, 2); ?>&url=<?php echo $url; ?>&via=<?php echo $_SESSION['storeName']; ?>"><?php echo $this->Html->image('tw-share-button.png', array('alt' => 'twshare')); ?> </a>
    </span>
    <span>
        <a class='share_button' 
           desc="Items : <?php echo $desc; ?> Coupon Discount : -$<?php echo number_format($discount_amount, 2); ?>, Extra Charges: $<?php echo number_format($total_of_extra, 2); ?>, Total Payable Amount : $<?php echo number_format($total_sum, 2); ?>" >
               <?php echo $this->Html->image('fb-share-button.png', array('alt' => 'fbshare')); ?>   
        </a> 
    </span>
</div>


<ul class="order-overview-listing clearfix">
    <?php
    $total_sum = 0;
    $total_of_items = 0;
    $ordertype = "";
    $total_of_extra = 0;
    $totaltaxPrice = 0;

    foreach ($finalItem as $session_key => $item) {
        $storetaxInfo = array();
        $CatName = '';
        $CategoryName = $this->Common->getCategoryName($item['Item']['categoryid']);
        if ($CategoryName) {
            $CatName = "<span class='common-bold'>" . $CategoryName['Category']['name'] . "</span> - ";
        }

        $taxlabel = '';

        if ($item['Item']['taxamount'] > 0) {
            $taxlabel = "<font style='color:#000000;font-weight:bold;' title='Tax applicable'>T</font>";
            //$totaltaxPrice=$totaltaxPrice + ($item['Item']['quantity'] * $item['Item']['taxamount']);
            $totaltaxPrice = $totaltaxPrice + $item['taxCalculated'];
        }

        $ordertype = $item['order_type'];
        $total_sum = $total_sum + $item['Item']['final_price'];
        $total_of_items = $total_of_items + $item['Item']['final_price'];
        ?>
        <li>
            <span class="title" style="width:60%;"><label>
                    <?php
                    $Interval = "";
                    if (isset($item['Item']['interval_id'])) {
                        $intervalId = $item['Item']['interval_id'];
                        $Interval = $this->Common->getIntervalName($intervalId);
                    }

                    echo $item['Item']['quantity'];
                    ?> X <?php
                    echo $CatName . @$item['Item']['size'] . ' ' . @$item['Item']['type'] . ' ' . $item['Item']['name'];

                    echo ($Interval) ? "(" . $Interval . ")" : "";
                    ?>



                    <br/>
                </label></span>
            <div class="title-box">$<?php echo number_format($item['Item']['final_price'], 2) . $taxlabel; ?>
            </div>

            <div>

                <item id="<?php echo $session_key; ?>" style='font-size: 12px;'>
                    <?php echo @$item['Item']['OfferItemName']; ?></item>                   
                <?php
                if (!empty($item['Item']['default_topping'])) {
                    $defaulttopping = "";
                    echo '<font size=2>(Default Toppings : ';
                    foreach ($item['Item']['default_topping'] as $dtop) {
                        $defaulttopping .= $dtop['name'] . " ,";
                    }
                    echo rtrim($defaulttopping, ",") . ')</font><br/>';
                }
                if (!empty($item['Item']['paid_topping'])) {
                    $paidtopping = "";
                    echo '<br><font size=2>(Paid Toppings : ';
                    foreach ($item['Item']['paid_topping'] as $ptop) {
//                        $addonsize = 1;
//                        $addOnSizedetails = $this->Common->getaddonSize($ptop['size']);
//                        if ($addOnSizedetails) {
//                            $addonsize = $addOnSizedetails['AddonSize']['size'];
//                        }
//                        $paidtopping .= $addonsize . ' ' . $ptop['name'] . " ,";
                        $paidtopping .= ($ptop['size'] > 1) ? $ptop['size'] : '';
                        $paidtopping .=' ' . $ptop['name'] . " , ";
                    }
                    echo rtrim($paidtopping, ", ") . ')</font><br/>';
                }
                if (!empty($item['Item']['subPreferenceOld'])) {
                    $paidpreference = "";
                    echo '<font size=2>(Preferences : ';
//                    foreach ($item['Item']['subpreference'] as $prekey => $pretop) {
//                        if ($pretop) {
//                            $subdetails = $this->Common->getSubPreferenceDetail($pretop);
//                            $paidpreference .= $subdetails['SubPreference']['name'] . " ,";
//                        }
//                    }

                    foreach ($item['Item']['subPreferenceOld'] as $prekey => $preval) {
                        if ($preval) {
                            //$paidpreference .=$prekey . "{";
//                            foreach ($preval as $pData) {
//                                if ($pData) {
//                                    $subdetails = $this->Common->getSubPreferenceDetail($pData);
//                                    $paidpreference .= $subdetails['SubPreference']['name'] . ", ";
//                                }
//                            }
//                            $paidpreference = rtrim($paidpreference, ", ");
//                            $paidpreference .=", ";
                            $paidpreference .= ($preval['size'] > 1) ? $preval['size'] : '';
                            $paidpreference .=' ' . $preval['name'] . ", ";
                        }
                    }
                    echo rtrim($paidpreference, ", ") . ')</font><br/>';
                }
                ?></div>
        </li>
        <?php
        $ItemOfferArray[$session_key]['itemName'] = @$item['Item']['size'] . ' ' . @$item['Item']['type'] . ' ' . $item['Item']['name'];
        $ItemOfferArray[$session_key]['freeQuantity'] = $item['Item']['freeQuantity'];
        $ItemOfferArray[$session_key]['price'] = $item['Item']['SizePrice'];
    }
    ?>

    <li>
        <span class="title" style="width:60%;"><label>Sub-Total</label></span>
        <div class="title-box">
            <?php
            if (isset($total_of_items)) {
                echo "$" . number_format($total_of_items, 2);
            }
            ?>
        </div>
        <span style="display:none;" group-info="priceInfo" tag-info="subTotal" value-info="<?php echo isset($total_of_items) ? number_format($total_of_items, 2) : 0; ?>"></span>
    </li> 



    <?php if ($ItemOfferArray) {
        ?>
        <li> <span class="title"><label>Free Item</span> </li>
        <?php
        $ItemDiscount = 0;
        foreach ($ItemOfferArray as $offkey => $freeunitdata) {
            if ($freeunitdata['freeQuantity'] > 0) {
                ?>
                <li>
                    <span class="title" style="width:60%;"><label><?php echo $freeunitdata['freeQuantity'] . ' ' . $freeunitdata['itemName']; ?></label></span>
                    <div class="title-box">

                        <?php
                        //$ItemOfferDiscount=$freeunitdata['price']*$freeunitdata['freeQuantity'];
                        //echo '-$'.number_format($freeunitdata['price']*$freeunitdata['freeQuantity'],2);
                        ?>

                    </div>

                    <?php
                    //$ItemDiscount=$ItemDiscount+$ItemOfferDiscount;
                }
                ?>
            </li>    
            <?php
        }
    }
    ?>






    <li>
        <span class="title" style="width:60%;"><label>Coupon Discount
                <?php echo ($_SESSION['orderOverview']['Coupon']['Coupon']['coupon_code']) ? ': (' . $_SESSION['orderOverview']['Coupon']['Coupon']['coupon_code'] . ')' : ''; ?>
            </label></span>
        <div class="title-box">-$
            <?php
            $discount_amount = 0;
            if (isset($_SESSION['orderOverview']['Coupon'])) {
                if ($_SESSION['orderOverview']['Coupon']['Coupon']['discount_type'] == 1) { // Price
                    $discount_amount = $_SESSION['orderOverview']['Coupon']['Coupon']['discount'];
                } else { // Percentage
                    $discount_amount = $total_of_items * $_SESSION['orderOverview']['Coupon']['Coupon']['discount'] / 100;
                }
            }
            $total_of_items = $total_of_items - $discount_amount;
            if ($total_of_items < $discount_amount) {
                $discount_amount = $total_of_items;
            }
            echo number_format($discount_amount, 2);

            $_SESSION['orderOverview']['Discount'] = $discount_amount;
            ?>
            <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('controller' => 'products', 'action' => 'removeCoupon'), array('escape' => false, 'confirm' => 'Are you sure to delete coupon?')); ?>
        </div>
        <span style="display:none;" group-info="priceInfo" tag-info="couponDiscount" value-info="<?php echo isset($discount_amount) ? number_format($discount_amount, 2) : 0; ?>"></span>
    </li>
    <?php if ($totaltaxPrice) { ?>
        <li>
            <span class="title" style="width:60%;"><label>Tax</label></span>
            <div class="title-box">
                <?php
                if ($totaltaxPrice >= 0) {
                    echo "$" . number_format($totaltaxPrice, 2);
                } else {
                    echo '$' . $totaltaxPrice = '0.00';
                }
                $_SESSION['orderOverview']['taxPrice'] = $totaltaxPrice;
                ?>
            </div>
            <span style="display:none;" group-info="priceInfo" tag-info="tax" value-info="<?php echo isset($totaltaxPrice) ? number_format($totaltaxPrice, 2) : 0; ?>"></span>
        </li> 
    <?php } ?>
    <?php
    if (isset($_SESSION['orderOverview']['delivery_fee']) && $ordertype == 3 && ($_SESSION['orderOverview']['delivery_fee'] > 0)) {
        $total_of_extra = $total_of_extra + $this->Session->read('orderOverview.delivery_fee');
        ?>
        <li>
            <span class="title" style="width:60%;"><label>Delivery Fee</label></span>
            <div class="title-box">$<?php echo number_format($this->Session->read('orderOverview.delivery_fee'), 2); ?></div>
            <?php $deliveryFee = $this->Session->read('orderOverview.delivery_fee'); ?>
            <span style="display:none;" group-info="priceInfo" tag-info="deliveryFee" value-info="<?php echo isset($deliveryFee) ? number_format($deliveryFee, 2) : 0; ?>"></span>
        </li>            
        <?php
    }

    if (isset($_SESSION['orderOverview']['service_fee']) && ($_SESSION['orderOverview']['service_fee'] > 0)) {
        $total_of_extra = $total_of_extra + $this->Session->read('orderOverview.service_fee');
        ?>
        <li>
            <span class="title" style="width:60%;"><label>Service Fee</label></span>
            <div class="title-box">$<?php echo number_format($this->Session->read('orderOverview.service_fee'), 2); ?></div>
            <?php $serviceFee = $this->Session->read('orderOverview.delivery_fee'); ?>
            <span style="display:none;" group-info="priceInfo" tag-info="serviceFee" value-info="<?php echo isset($serviceFee) ? number_format($serviceFee, 2) : 0; ?>"></span>
        </li>          
    <?php } ?>

    <?php
    if (isset($_SESSION['orderOverview']['tip']) && ($_SESSION['orderOverview']['tip'] > 0)) {
        $tipamount = $this->Session->read('orderOverview.tip');
        ?>
        <li>
            <span class="title" style="width:60%;"><label>Tip</label></span>
            <div class="title-box">$<?php echo number_format($this->Session->read('orderOverview.tip'), 2); ?></div>
        </li>          
    <?php } ?>

    <li>
        <?php
        $total_sum = $total_of_items + $total_of_extra;

        if ($totaltaxPrice) {
            $total_sum = $total_sum + $totaltaxPrice;
        }

        if ($ItemDiscount) {
            $total_sum = $total_sum - $ItemDiscount;
        }
        if ($tipamount > 0) {
            $total_sum = $total_sum + number_format($tipamount, 2);
        }
        ?>
        <span class="title" style="width:60%;"><label class="common-bold common-size" >Total</label></span>
        <div class="title-box common-bold common-size">$<?php echo number_format($total_sum, 2); ?></div>
        <span style="display:none;" group-info="priceInfo" tag-info="total" value-info="<?php echo isset($total_sum) ? number_format($total_sum, 2) : 0; ?>"></span>
    </li> 
    <?php $_SESSION['orderOverview']['grand_total_final'] = $total_sum; ?>
</ul>

<div id="fb-root"></div>
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
