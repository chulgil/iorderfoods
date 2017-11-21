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
if (!empty($finalItem)) {
    foreach ($finalItem as $session_key => $item) {
        if (isset($item['Item']['OfferItemName'])) {
            $data = strip_tags($item['Item']['OfferItemName']);
            $offerItemName = explode('x', $data);
            unset($offerItemName[0]);
            $offerName = implode("<br/>", $offerItemName);
        }
        //$ordertype = @$item['order_type'];
        $ordertype = ($this->Session->read('ordersummary.order_type')) ? $this->Session->read('ordersummary.order_type') : '';
        $total_sum = $total_sum + $item['Item']['final_price'];
        $total_of_items = $total_of_items + $item['Item']['final_price'];
        if (isset($offerName)) {
            $desc .= $item['Item']['quantity'] . ' ' . $item['Item']['name'] . ' ( Offer Items: ' . $offerName . ' ) @ $' . number_format($item['Item']['final_price'], 2) . ', ';
        } else {
            $desc .= $item['Item']['quantity'] . ' ' . $item['Item']['name'] . ' @ $' . number_format($item['Item']['final_price'], 2) . ', ';
        }
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
if ($this->Session->check('Zone.fee')) {
    $total_of_extra = $total_of_extra + $this->Session->read('Zone.fee');
    //$total_of_extra = $total_of_extra + $this->Session->read('delivery_fee');
}
if (isset($_SESSION['service_fee']) && ($_SESSION['service_fee'] > 0)) {
    $total_of_extra = $total_of_extra + $this->Session->read('service_fee');
}
$total_sum = $total_of_items + $total_of_extra;
$disable = ''; //($this->Session->read('ordersummary.order_type')) ? : 'disabled';
?>
<span id="checkDeliverType">
    <?php echo $this->element('orderoverview/coupon'); ?>
    <table>
        <thead>
            <tr>
                <th width="70%">Item Name</th>
                <th width="10%">Qty</th>
                <th width="15%">Price</th>
                <th width="5%"></th>
            </tr>
        </thead>
        <tbody>
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
                    $CatName = $CategoryName['Category']['name'] . " - ";
                }

                $taxlabel = '';

                if (!empty($item['Item']['taxamount'])) {
                    $taxlabel = "<font style='color:#000000;font-weight:bold;' title='Tax applicable'>T</font>";
                    //$totaltaxPrice = $totaltaxPrice + ($item['Item']['quantity'] * $item['Item']['taxamount']);
                    $totaltaxPrice = $totaltaxPrice + $item['taxCalculated'];
                }

                //$ordertype = @$item['order_type'];
                $ordertype = ($this->Session->read('ordersummary.order_type')) ? $this->Session->read('ordersummary.order_type') : '';
                $total_sum = $total_sum + $item['Item']['final_price'];
                $total_of_items = $total_of_items + $item['Item']['final_price'];
                ?>
                <tr>
                    <td>
                        <?php
                        $Interval = "";
                        if (isset($item['Item']['interval_id'])) {
                            $intervalId = $item['Item']['interval_id'];
                            $Interval = $this->Common->getIntervalName($intervalId);
                        }
                        //echo $item['Item']['quantity'];
                        ?>  <?php
                        echo $CatName . @$item['Item']['size'] . ' ' . @$item['Item']['type'] . ' ' . $item['Item']['name'];
                        echo ($Interval) ? $Interval : "";
                        ?>
                        <div class="ad-cart-dtl">
                            <?php
                            if (!empty($item['Item']['paid_topping']) || !empty($item['Item']['subPreferenceOld']) || !empty($item['Item']['default_topping']) || !empty($item['Item']['OfferItemName'])) {
                                ?>
                                <div>                            
                                    <span id="<?php echo $session_key; ?>" class="offerItems">
                                        <?php echo @$item['Item']['OfferItemName']; ?>
                                    </span>
                                    <?php
                                    if (!empty($item['Item']['default_topping'])) {
                                        $defaulttopping = "";
                                        echo '<strong>Default Toppings : </strong><p>';
                                        foreach ($item['Item']['default_topping'] as $dtop) {
                                            $defaulttopping .= $dtop['name'] . " ,";
                                        }
                                        echo rtrim($defaulttopping, ",");
                                        echo "</p>";
                                    }
                                    if (!empty($item['Item']['paid_topping'])) {
                                        $paidtopping = "";
                                        echo '<strong>Paid Toppings : </strong><p>';
                                        foreach ($item['Item']['paid_topping'] as $ptop) {
//                                            $addonsize = 1;
//                                            $addOnSizedetails = $this->Common->getaddonSize($ptop['size']);
//                                            if ($addOnSizedetails) {
//                                                $addonsize = $addOnSizedetails['AddonSize']['size'];
//                                            }
                                            $paidtopping .= $ptop['size'] . ' ' . $ptop['name'] . " ,";
                                        }
                                        echo rtrim($paidtopping, ",") . '</p>';
                                    }
                                    if (!empty($item['Item']['subPreferenceOld'])) {
                                        $paidpreference = "";
                                        echo '<strong>Preferences : </strong><p>';
                                        foreach ($item['Item']['subPreferenceOld'] as $prekey => $preval) {
                                            if ($preval) {
                                                //$paidpreference .=$prekey . "{";
//                                                foreach ($preval as $pData) {
//                                                    if ($pData) {
//                                                        $subdetails = $this->Common->getSubPreferenceDetail($pData);
//                                                        $paidpreference .= $subdetails['SubPreference']['name'] . ", ";
//                                                    }
//                                                }
//                                                $paidpreference = rtrim($paidpreference, ", ");
//                                                $paidpreference .=", ";
                                                $paidpreference .= ($preval['size'] > 1) ? $preval['size'] : '';
                                                $paidpreference.=' ' . $preval['name'] . ", ";
                                            }
                                        }
                                        echo rtrim($paidpreference, ", ") . '</p>';
                                    }
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                    </td>
                    <td>
                        <?php echo $item['Item']['quantity']; ?>
                    </td>
                    <td>
                        <span class="p-price">                
                            $<?php echo number_format($item['Item']['final_price'], 2) . $taxlabel; ?>
                        </span>
                    </td>
                    <td>
                        <span class="itemRemove" id="<?php echo $session_key; ?>">
                            <a href="javascript:void(0)" title="Remove Item">
                                <i class="fa fa-times" style="color: #ff3333;"></i>
                            </a>
                        </span> 
                    </td>
                </tr>

                <?php
                $ItemOfferArray[$session_key]['itemName'] = @$item['Item']['size'] . ' ' . @$item['Item']['type'] . ' ' . $item['Item']['name'];
                $ItemOfferArray[$session_key]['freeQuantity'] = @$item['Item']['freeQuantity'];
                $ItemOfferArray[$session_key]['price'] = $item['Item']['SizePrice'];
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td>
                    Subtotal
                </td>
                <td colspan="2" class="td-right">
                    <span>
                        <?php
                        if (isset($total_of_items)) {
                            echo "$" . number_format($total_of_items, 2);
                        }
                        ?>
                    </span>
                    <span style="display:none;" group-info="priceInfo" tag-info="subTotal" value-info="<?php echo isset($total_of_items) ? number_format($total_of_items, 2) : 0; ?>"></span>
                </td>
            </tr>

            <?php
            if ($ItemOfferArray) {
                $ItemDiscount = 0;
                foreach ($ItemOfferArray as $offkey => $freeunitdata) {
                    if ($freeunitdata['freeQuantity'] > 0) {
                        ?>
                        <tr>

                            <td>
                                Free Item <span><?php echo $freeunitdata['freeQuantity'] . ' ' . $freeunitdata['itemName']; ?></span>
                            </td>
                            <td colspan="2" class="td-right">
                                <?php
                                //$ItemOfferDiscount = $freeunitdata['price'] * $freeunitdata['freeQuantity'];
                                //echo '<span style="color:#FF3333";>-$' . number_format($freeunitdata['price'] * $freeunitdata['freeQuantity'], 2) . '</span>';
                                ?>
                            </td>
                        </tr>
                        <?php
                        //$ItemDiscount = $ItemDiscount + $ItemOfferDiscount;
                    }
                }
            }
            ?>
            <?php if (isset($_SESSION['Coupon'])) { ?>
                <tr>
                    <td>Discount <?php echo ($Couponcode) ? ": ($Couponcode)" : ''; ?></td>
                    <td colspan="2" class="td-right">
                        <?php
                        $discount_amount = 0;
                        if ($_SESSION['Coupon']['Coupon']['discount_type'] == 1) { // Price
                            $discount_amount = $_SESSION['Coupon']['Coupon']['discount'];
                        } else { // Percentage
                            $discount_amount = $total_of_items * $_SESSION['Coupon']['Coupon']['discount'] / 100;
                        }
                        if ($total_of_items < $discount_amount) {
                            $discount_amount = $total_of_items;
                        }
                        echo '<span style="color:#FF3333";> -$' . number_format($discount_amount, 2) . '</span>';
                        $_SESSION['Discount'] = $discount_amount;
                        $total_of_items = $total_of_items - $discount_amount;
                        ?>
                        <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('controller' => 'products', 'action' => 'removeCoupon'), array('escape' => false, 'confirm' => 'Are you sure to delete coupon?')); ?>
                        <span style="display:none;" group-info="priceInfo" tag-info="couponDiscount" value-info="<?php echo isset($discount_amount) ? number_format($discount_amount, 2) : 0; ?>"></span>
                    </td>
                </tr>
            <?php } ?>

            <?php
            if ($this->Session->check('Zone.fee')) {
                ?>
                <tr>
                    <?php
                    if ($this->Session->check('Zone.fee')) {
                        $total_of_extra = $total_of_extra + $this->Session->read('Zone.fee');
                    } else {
                        //$total_of_extra = $total_of_extra + $this->Session->read('delivery_fee');
                    }
                    ?>
                    <td>Delivery Fee <?php echo ($this->Session->read('Zone.name')) ? '(' . $this->Session->read('Zone.name') . ')' : ''; ?></td>
                    <td colspan="2" class="td-right">$<?php
                        //echo number_format($this->Session->read('delivery_fee'), 2);
                        if ($this->Session->check('Zone.fee')) {
                            echo $delivery_Fee = number_format($this->Session->read('Zone.fee'), 2);
                        } else {
                            //echo $delivery_Fee = number_format($this->Session->read('delivery_fee'), 2);
                        }
                        ?>
                        <?php $_SESSION['delivery_fee'] = $delivery_Fee; ?>
                        <span style="display:none;" group-info="priceInfo" tag-info="deliveryFee" value-info="<?php echo isset($delivery_Fee) ? number_format($delivery_Fee, 2) : 0; ?>">
                        </span>
                    </td>
                </tr>
            <?php } ?>
            <?php if (isset($_SESSION['service_fee']) && ($_SESSION['service_fee'] > 0)) { ?>
                <tr>
                    <?php
                    $total_of_extra = $total_of_extra + $this->Session->read('service_fee');
                    ?>
                    <td>Service Fee</td>
                    <td colspan="2" class="td-right">
                        $<?php echo number_format($this->Session->read('service_fee'), 2); ?>
                        <?php $serviceFee = $this->Session->read('service_fee'); ?>
                        <span style="display:none;" group-info="priceInfo" tag-info="serviceFee" value-info="<?php echo isset($serviceFee) ? number_format($serviceFee, 2) : 0; ?>"></span>
                    </td>
                </tr>
            <?php } ?>
            <?php
            if (!empty($storeSetting['StoreSetting']['discount_on_extra_fee'])) {
                $total_sum = number_format($total_of_items, 2) + number_format($total_of_extra, 2);
            } else {
                $total_sum = number_format($total_of_items, 2);
            }
            ?>  
            <?php if (isset($_SESSION['Coupon'])) { ?>
                <tr>
                    <td>Discount <?php echo ($Couponcode) ? ": ($Couponcode)" : ''; ?></td>
                    <td colspan="2" class="td-right">
                        <?php
                        $discount_amount = 0;
                        if ($_SESSION['Coupon']['Coupon']['discount_type'] == 1) { // Price
                            $discount_amount = $_SESSION['Coupon']['Coupon']['discount'];
                        } else { // Percentage
                            $discount_amount = $total_sum * $_SESSION['Coupon']['Coupon']['discount'] / 100;
                        }

                        if ($total_sum < $discount_amount) {
                            $discount_amount = $total_sum;
                        }
                        echo '<span style="color:#FF3333";> -$' . number_format($discount_amount, 2) . '</span>';
                        $total_sum = $total_sum - $discount_amount;
                        $_SESSION['Discount'] = $discount_amount;
                        ?>

                        <span style="display:none;" group-info="priceInfo" tag-info="couponDiscount" value-info="<?php echo isset($discount_amount) ? number_format($discount_amount, 2) : 0; ?>"></span>
                    </td>
                    <td>
                        <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('controller' => 'products', 'action' => 'removeCoupon'), array("style" => "color: #ff3333;", 'escape' => false, 'confirm' => 'Are you sure to remove coupon?')); ?>
                    </td>
                </tr>
            <?php } ?>    

            <?php if (isset($_SESSION['tip']) && ($_SESSION['tip'] > 0)) { ?>
                <tr>
                    <?php $tip = @$_SESSION['Cart']['tip']; ?>
                    <td>Add Tip </td>
                    <td colspan="2" class="td-right">
                        <?php echo $this->Form->input('Order.tip', array('type' => 'text', 'class' => 'tip', 'Placeholder' => '', 'label' => false, 'div' => false, 'maxlength' => '5', 'value' => ($tip) ? $tip : @$_POST['tip'])); ?>
                        <input type="button" class="btn btn-info theme-bg-1" id="tipButton" value="Save"/>
                    </td>
                </tr>
            <?php } ?>


            <?php if ($totaltaxPrice) { ?>
                <tr>
                    <td>Tax</td>
                    <td colspan="2" class="td-right">
                        <span>
                            <?php
                            if ($totaltaxPrice >= 0) {
                                echo "$" . number_format($totaltaxPrice, 2);
                            } else {
                                echo '$' . $totaltaxPrice = '0.00';
                            }
                            $_SESSION['taxPrice'] = $totaltaxPrice;
                            ?>
                        </span>
                        <span style="display:none;" group-info="priceInfo" tag-info="tax" value-info="<?php echo isset($totaltaxPrice) ? number_format($totaltaxPrice, 2) : 0; ?>"></span>
                    </td>
                </tr>
            <?php } ?>    



            <?php
            $total_sum = number_format($total_of_items, 2) + number_format($total_of_extra, 2);
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
            <tr>
                <td>TOTAL</td>
                <td colspan="2" class="td-right bld-ryt">
                    $<?php
                    if ($total_sum > 0) {
                        echo $total_sum = number_format($total_sum, 2);
                    } else {
                        echo $total_sum = "0.00";
                    }
                    ?>
                    <span style="display:none;" group-info="priceInfo" tag-info="total" value-info="<?php echo isset($total_sum) ? number_format($total_sum, 2) : 0; ?>"></span>
                    <?php $_SESSION['Cart']['grand_total_final'] = number_format($total_sum, 2);
                    $_SESSION['Cart']['grand_total_amount'] = number_format($total_sum, 2);?>
                </td>
            </tr>
        </tfoot>
    </table>
</span>
<?php
$guestUserDetail = $this->Session->check('GuestUser');
$specialComment = $this->Session->check('Cart.comment');
$userId = AuthComponent::User('id');
if (!empty($userId) || !empty($guestUserDetail)) {
    ?>
    <div id="flashSpecialComment"></div>
    <div class="comment-box">
        <?php echo $this->Form->input('User.comment', array('type' => 'textarea', 'label' => false, 'placeholder' => "Special Comment", 'class' => 'form-control', 'value' => $this->Session->read('Cart.comment'))); ?>
    </div>
    <div class="sv-odr-ftr">
        <button class="btn save-order saveComment theme-bg-1" type="button"><?php echo ($specialComment) ? 'UPDATE COMMENT' : 'SAVE COMMENT'; ?></button>
        <?php
        if (!empty($storeSetting['StoreSetting']['save_to_order_btn'])) {
            echo $this->Form->button('SAVE TO ORDER', array('type' => 'button', 'class' => 'btn save-order', 'id' => 'desktop_save'));
        }
        ?>
        <?php echo $this->Html->link('CONTINUE SHOPPING', array('controller' => 'products', 'action' => 'items', $encrypted_storeId, $encrypted_merchantId), array('class' => 'go-bk-lnk save-order')); ?>
    </div>
<?php } ?>
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', '.itemRemove', function () {
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
                        //$('.right-menu').html(result);
                        $('#checkDeliverType').html(result);
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
                        //$('.right-menu').html(result);
                        $('#checkDeliverType').html(result);
                    }
                }
            });
        });

        $(document).on('click', '#tipButton', function () {
            var tipvalue = $("#OrderTip").val();

            $.ajax({
                type: 'post',
                url: '/Products/addTip',
                data: {'tip': tipvalue},
                success: function (result) {
                    if (result) {
                        //$('.right-menu').html(result);
                        $('#checkDeliverType').html(result);
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

        $(document).on('click', '#express-check-out', function () {
            $("#pay-credit-card").css('display', 'none')
            $("#paypal-express-btn").show();
        });
        $(document).on('click', '#payment2', function () {
            $("#pay-credit-card").show()
            $("#paypal-express-btn").css('display', 'none');
            $('button.changeName').text('PLACE ORDER');
        });
        $(document).on('click', '#payment', function () {
            $("#pay-credit-card").show()
            $("#paypal-express-btn").css('display', 'none');
            $('button.changeName').text('PAYMENT');
        });
        $(document).on('click', 'button.changeName', function () {
            var specialComment = $('#UserComment').val();
            if (specialComment != '') {
                $.ajax({
                    type: 'post',
                    url: "<?php echo $this->Html->url(array('controller' => 'payments', 'action' => 'saveSpecialComment')); ?>",
                    data: {'specialComment': specialComment},
                });
            }
        });
    });
    $(document).on('click', '.saveComment', function () {
        var specialComment = $('#UserComment').val();
        if (specialComment != '') {
            $.ajax({
                type: 'post',
                url: "<?php echo $this->Html->url(array('controller' => 'payments', 'action' => 'saveSpecialComment')); ?>",
                data: {'specialComment': specialComment},
                beforeSend: function () {
                    $.blockUI({css: {
                            border: 'none',
                            padding: '15px',
                            backgroundColor: '#000',
                            '-webkit-border-radius': '10px',
                            '-moz-border-radius': '10px',
                            opacity: .5,
                            color: '#fff'
                        }});
                },
                complete: function () {
                    $.unblockUI();
                },
                success: function (successResult) {
                    data = JSON.parse(successResult);
                    $("#errorPop").modal('show');
                    $("#errorPopMsg").html(data.msg);
                    //$("#flashSpecialComment").html('<div class="message message-success alert alert-success" id="flashMessage"><a href="#" data-dismiss="alert" aria-label="close" title="close" class="close">×</a> ' + data.msg + '</div>');
                }
            });
        }
    });
</script>

