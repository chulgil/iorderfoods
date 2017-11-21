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
$finalItem = $this->Common->taxCalculation($finalItem);
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
if (isset($_SESSION['Coupon'])) {
    if ($_SESSION['Coupon']['Coupon']['discount_type'] == 1) { // Price
        $discount_amount = $_SESSION['Coupon']['Coupon']['discount'];
        $total_of_items = $total_of_items - $discount_amount;
    } else {
        $discount_amount = $total_of_items * $_SESSION['Coupon']['Coupon']['discount'] / 100;
        $total_of_items = $total_of_items - $discount_amount;
    }
    $Couponcode = $_SESSION['Coupon']['Coupon']['coupon_code'];
} else {
    $discount_amount = 0;
    $total_of_items = $total_of_items - $discount_amount;
    $Couponcode = '';
}
$_SESSION['Discount'] = $discount_amount;
if ($this->Session->check('Zone.fee') && $ordertype == 3) {
    $total_of_extra = $total_of_extra + $this->Session->read('Zone.fee');
    //$total_of_extra = $total_of_extra + $this->Session->read('delivery_fee');
}
if (isset($_SESSION['service_fee']) && ($_SESSION['service_fee'] > 0)) {
    $total_of_extra = $total_of_extra + $this->Session->read('service_fee');
}
$total_sum = $total_of_items + $total_of_extra;
?>

<div class="share-button clearfix">
    <span>
        <a  class='twitter-share' target="blank" href= "http://twitter.com/share?text=Items : <?php echo $desc; ?> Coupon Discount : -$<?php echo number_format($discount_amount, 2); ?>, Extra Charges: $<?php echo number_format($total_of_extra, 2); ?>, Total Payable Amount : $<?php echo number_format($total_sum, 2); ?>&url=<?php echo $url; ?>&via=<?php echo $_SESSION['storeName']; ?>"><?php echo $this->Html->image('tw-share-button.png', array('alt' => 'twshare')); ?> </a>
    </span>
    <span>
        <!--        <a class='share_button' 
                   desc="Items : <?php echo $desc; ?> Coupon Discount : -$<?php echo number_format($discount_amount, 2); ?>, Extra Charges: $<?php echo number_format($total_of_extra, 2); ?>, Total Payable Amount : $<?php echo number_format($total_sum, 2); ?>" >
        <?php echo $this->Html->image('fb-share-button.png', array('alt' => 'fbshare')); ?>   
                </a> -->
        <?php
        $strDomainUrl = $_SERVER['HTTP_HOST'];
        $strShareLink = "https://www.facebook.com/sharer/sharer.php?u=" . $strDomainUrl;
        ?>
        <a href="#" onclick='window.open("<?php echo $strShareLink; ?>", "", "width=500, height=300");'>
            <?php echo $this->Html->image('fb-share-button.png', array('alt' => 'fbshare')); ?>
        </a>

    </span>
</div>


<style>
    .tip{
        width:50px;
        border:1px solid #003064;
    }
    .single-frame .form-layout .btn{
        min-width:auto;
    }
    .singleItemRemove{
        color:#4d4d4d !important;   
    }
    @media (max-width:60em) {
        #mobile_save{
            display:block;
        }

        #desktop_save{
            display:none;
        }

    }

    @media (min-width:60em) {
        #mobile_save{
            display:none;
        }

        #desktop_save{
            display:block;
        }
    }

</style>  
<ul class="order-overview-listing clearfix">
    <li class='dateTimeInfo'>
        <span class="title"><label><span class="common-bold datelabel"><?php echo ($ordertype == 3) ? 'Delivery' : 'Pickup'; ?> Date</span><br>
            </label></span>
        <div class="title-box dateInfo">
            <?php echo $this->Session->read('Order.store_pickup_date'); ?>           
        </div>

        <span class="title"><label><span class="common-bold timelabel"><?php echo ($ordertype == 3) ? 'Delivery' : 'Pickup'; ?> Time</span><br>
            </label></span>
        <div class="title-box timeInfo">
            <?php echo $this->Common->storeTimeFormate($this->Session->read('Order.store_pickup_time')); ?>   
        </div>


    </li>
    <?php
    $total_sum = 0;
    $total_of_items = 0;
    $ordertype = "";
    $total_of_extra = 0;
    $totaltaxPrice = 0;
    $ItemOfferArray = array();
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
            //$totaltaxPrice=$totaltaxPrice + ($item['Item']['taxvalue'] / 100) * $item['Item']['final_price'];
            $totaltaxPrice = $totaltaxPrice + $item['taxCalculated'];
        }

        $ordertype = $item['order_type'];
        $total_sum = $total_sum + $item['Item']['final_price'];
        $total_of_items = $total_of_items + $item['Item']['final_price'];
        ?>
        <li>
            <span class="title"><label>
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

                <item class="itemRemove" id="<?php echo $session_key; ?>" style="float:right;"><a href="javascript:void(0)" title="Remove Item"><i class="fa fa-times" style="color: #ff3333;"></i></a></item>
            </div>

            <div >
                <br/>
                <item id="<?php echo $session_key; ?>" style='font-size: 12px;' class="offerItems">
                    <?php echo @$item['Item']['OfferItemName']; ?></item>                   
                <?php
                if (!empty($item['Item']['default_topping'])) {
                    $defaulttopping = "";
                    echo '<br/><font size=2>(Default Toppings : ';
                    foreach ($item['Item']['default_topping'] as $dtop) {
                        $defaulttopping .= $dtop['name'] . " ,";
                    }
                    echo rtrim($defaulttopping, ",") . ')</font>';
                }
                if (!empty($item['Item']['paid_topping'])) {
                    $paidtopping = "";
                    echo '<br/><font size=2>(Paid Toppings : ';
                    foreach ($item['Item']['paid_topping'] as $ptop) {
//                        $addonsize = 1;
//                        $addOnSizedetails = $this->Common->getaddonSize($ptop['size']);
//                        if ($addOnSizedetails) {
//                            $addonsize = $addOnSizedetails['AddonSize']['size'];
//                        }
//                        $paidtopping .= $addonsize . ' ' . $ptop['name'] . " ,";
                        $paidtopping .= ($ptop['size'] > 1) ? $ptop['size'] : '';
                        $paidtopping .=' ' . $ptop['name'] . " ,";
                    }
                    echo rtrim($paidtopping, ",") . ')</font>';
                }
                if (!empty($item['Item']['subPreferenceOld'])) {
                    $paidpreference = "";
                    echo '<br/><font size=2>(Preferences : ';

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
                            $paidpreference.=' ' . $preval['name'] . ", ";
                        }
                    }

//                    foreach ($item['Item']['subpreference'] as $prekey => $pretop) {
//                        if ($pretop) {
//                            $subdetails = $this->Common->getSubPreferenceDetail($pretop);
//                            $paidpreference .= $subdetails['SubPreference']['name'] . " ,";
//                        }
//                    }
                    echo rtrim($paidpreference, ", ") . ')</font>';
                }
                ?></div>
        </li>







        <?php
        $ItemOfferArray[$session_key]['itemName'] = @$item['Item']['size'] . ' ' . @$item['Item']['type'] . ' ' . $item['Item']['name'];
        $ItemOfferArray[$session_key]['freeQuantity'] = @$item['Item']['freeQuantity'];
        $ItemOfferArray[$session_key]['price'] = $item['Item']['SizePrice'];
        //$ItemOfferArray[$session_key]['description']=$description;
    }
    ?>

    <li>
        <span class="title"><label>Sub-Total</label></span>
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
                    <span class="title"><label><?php echo $freeunitdata['freeQuantity'] . ' ' . $freeunitdata['itemName']; ?></label></span>
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


    <?php
    if ($this->Session->check('Zone.fee') && $ordertype == 3) {
        //$total_of_extra = $total_of_extra + $this->Session->read('delivery_fee');
        if ($this->Session->check('Zone.fee')) {
            $total_of_extra = $total_of_extra + $this->Session->read('Zone.fee');
        } else {
            //$total_of_extra = $total_of_extra + $this->Session->read('delivery_fee');
        }
        ?>
        <li>
            <span class="title"><label>Delivery Fee <?php echo ($this->Session->read('Zone.name')) ? '(' . $this->Session->read('Zone.name') . ')' : ''; ?></label></span>
            <div class="title-box">$<?php
                //echo number_format($this->Session->read('delivery_fee'), 2); 
                if ($this->Session->check('Zone.fee')) {
                    echo $zonePrice = number_format($this->Session->read('Zone.fee'), 2);
                    $_SESSION['delivery_fee'] = $zonePrice;
                } else {
                    //echo number_format($this->Session->read('delivery_fee'), 2);
                }
                ?></div>
            <?php $deliveryFee = $this->Session->read('delivery_fee');
            ?>
            <span style="display:none;" group-info="priceInfo" tag-info="deliveryFee" value-info="<?php echo isset($deliveryFee) ? number_format($deliveryFee, 2) : 0; ?>"></span>
        </li>            
        <?php
    }

    if (isset($_SESSION['service_fee']) && ($_SESSION['service_fee'] > 0)) {
        $total_of_extra = $total_of_extra + $this->Session->read('service_fee');
        ?>
        <li>
            <span class="title"><label>Service Fee</label></span>
            <div class="title-box">$<?php echo number_format($this->Session->read('service_fee'), 2); ?></div>
            <?php $serviceFee = $this->Session->read('delivery_fee'); ?>
            <span style="display:none;" group-info="priceInfo" tag-info="serviceFee" value-info="<?php echo isset($serviceFee) ? number_format($serviceFee, 2) : 0; ?>"></span>
        </li>          
    <?php } ?>

    <?php
    if (!empty($storeSetting['StoreSetting']['discount_on_extra_fee'])) {
        $total_sum = number_format($total_of_items, 2) + number_format($total_of_extra, 2);
    } else {
        $total_sum = number_format($total_of_items, 2);
    }
    ?>    

    <?php echo $this->element('orderoverview/coupon'); ?>    

    <?php if (isset($_SESSION['Coupon'])) { ?>

        <li>
            <span class="title"><label>Coupon Discount<font size="2"><?php echo ($Couponcode) ? ": ($Couponcode)" : ''; ?></font></label></span>
            <div class="title-box">-$
                <?php
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
                echo number_format($discount_amount, 2);
                $total_sum = $total_sum - $discount_amount;
                $_SESSION['Discount'] = number_format($discount_amount, 2);
                ?>
                <item class="pull-right">
                    <?php echo $this->Html->link('<i class="fa fa-times" style="color:#ff3333;"></i>', array('controller' => 'products', 'action' => 'removeCoupon'), array('escape' => false, 'confirm' => 'Are you sure to delete coupon?')); ?>
                </item>
            </div>
            <span style="display:none;" group-info="priceInfo" tag-info="couponDiscount" value-info="<?php echo isset($discount_amount) ? number_format($discount_amount, 2) : 0; ?>"></span>
        </li>
    <?php } ?>    



    <?php
    if (isset($_SESSION['tip']) && ($_SESSION['tip'] > 0)) {
        $tip = @$_SESSION['Cart']['tip'];
        ?>
        <li>
            <span class="title tipsection"><label>Add Tip</label></span>
            <span class="title-box tipnospacing"><?php echo $this->Form->input('Order.tip', array('type' => 'text', 'class' => 'tip', 'Placeholder' => '', 'label' => false, 'div' => false, 'maxlength' => '5', 'value' => ($tip) ? $tip : @$_POST['tip'])); ?>  <input type="button" class="btn btn-info" id="tipButton" value="Save" style="font-size: 14px;font-weight: bold;margin-left: 5px;padding: 3px 5px;vertical-align: top;width: auto;"/>
                <?php //echo $this->Form->input('Save',array('type'=>'button','class'=>'btn btn-info','Placeholder'=>'','label'=>false,'id'=>'tipButton'));      ?>

            </span>


        </li>          
    <?php } ?>


    <?php if ($totaltaxPrice) { ?>
        <li>
            <span class="title"><label>Tax</label></span>
            <div class="title-box">
                <?php
                if ($totaltaxPrice >= 0) {
                    echo "$" . number_format($totaltaxPrice, 2);
                } else {
                    echo '$' . $totaltaxPrice = '0.00';
                }
                $_SESSION['taxPrice'] = number_format($totaltaxPrice, 2);
                ?>
            </div>
            <span style="display:none;" group-info="priceInfo" tag-info="tax" value-info="<?php echo isset($totaltaxPrice) ? number_format($totaltaxPrice, 2) : 0; ?>"></span>
        </li> 
        <?php
    } else {
        $_SESSION['taxPrice'] = "0.00";
    }
    ?>    



    <li>
        <?php
        if (empty($storeSetting['StoreSetting']['discount_on_extra_fee'])) {
            $total_sum = $total_sum + number_format($total_of_extra, 2);
        }

        if ($totaltaxPrice) {
            $total_sum = $total_sum + number_format($totaltaxPrice, 2);
        }
        if ($ItemDiscount) {
            $total_sum = $total_sum - number_format($ItemDiscount, 2);
        }
        $tipamount = @$_SESSION['Cart']['tip'];
        if ($tipamount > 0) {
            $total_sum = $total_sum + number_format($tipamount, 2);
        }
        ?>
        <span class="title"><label class="common-bold common-size">Total</label></span>
        <div class="title-box common-bold common-size">$<?php echo number_format($total_sum, 2); ?></div>
        <span style="display:none;" group-info="priceInfo" tag-info="total" value-info="<?php echo isset($total_sum) ? number_format($total_sum, 2) : 0; ?>"></span>
    </li> 
    <?php $_SESSION['Cart']['grand_total_final'] = number_format($total_sum, 2); ?>
    <?php
    if (AuthComponent::User()) {
        if (!empty($storeSetting['StoreSetting']['save_to_order_btn'])) {
            ?>
            <li id="desktop_save"><?php echo $this->Form->button('SAVE TO ORDERS', array('type' => 'button', 'onclick' => "checkMandatoryItem()", 'class' => 'btn green-btn', 'style' => 'float:right;')); ?>
            </li>
            <li id="mobile_save">
                <?php echo $this->Form->button('SAVE FOR LATER', array('type' => 'button', 'onclick' => "checkMandatoryItem()", 'class' => 'btn green-btn', 'style' => 'float:right;')); ?>
            </li>
            <?php
        }
    }
    ?>


</ul>

<script>
    $('.itemRemove').on('click', function () {
        var index_id = $(this).attr('id');
        $('#loading').show();
        $.ajax({
            type: 'post',
            url: '/Products/removeOrderItem',
            data: {'index_id': index_id},
            success: function (result) {
                if ($.trim(result).length == 0) {
                    window.location = "/Products/items/<?php echo $encrypted_storeId; ?>/<?php echo $encrypted_merchantId; ?>";
                } else {
                    $('.editable-form').html(result);
                }
            },
            complete: function (result) {
                $.ajax({
                    type: 'post',
                    url: '/Products/getlatesttotalamont',
                    data: {},
                    success: function (result) {
                        if (result != '') {
                            result = jQuery.parseJSON(result);
                            $("#expressAmount").val(result.expressAmount);              //console.log($("#expressAmount"));
                            $("#expressItemNumber").val(result.expressItemNumber);
                            $("#expressCustom").val(result.expressCustom);
                        }
                    }
                });

                $('#loading').hide();
            }
        });
    });

    $('.singleItemRemove').on('click', function () {
        var cart_index_id = $(this).parent().attr('id'); //index of session array 
        var offer_index_id = $(this).attr('value'); //index of session arrayd

        $.ajax({
            type: 'post',
            url: '/Products/removeOrderOfferItem',
            data: {'cart_index_id': cart_index_id, 'offer_index_id': offer_index_id},
            success: function (result) {
                if (result) {
                    $('.editable-form').html(result);
                }
            }
        });
    });

    $('#tipButton').on('click', function () {
        var tipvalue = $("#OrderTip").val();


        $.ajax({
            type: 'post',
            url: '/Products/addTip',
            data: {'tip': tipvalue},
            success: function (result) {
                if (result) {
                    $('.editable-form').html(result);
                }
            },
            complete: function (result) {
                $.ajax({
                    type: 'post',
                    url: '/Products/getlatesttotalamont',
                    data: {},
                    success: function (result) {
                        if (result != '') {
                            result = jQuery.parseJSON(result);
                            $("#expressAmount").val(result.expressAmount);              //console.log($("#expressAmount"));
                            $("#expressItemNumber").val(result.expressItemNumber);
                            $("#expressCustom").val(result.expressCustom);
                        }
                    }
                });


            }
        });
    });



</script>



