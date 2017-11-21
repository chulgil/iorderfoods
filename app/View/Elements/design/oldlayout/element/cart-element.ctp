<?php
$gross_amount = 0;
$discount_amount = 0;
$totaltaxPrice = 0;
?>
<?php
$order_type = 0;
if (isset($_SESSION['Order']['order_type'])) {
    $order_type = $_SESSION['Order']['order_type'];
}
?>
<div class='loader1'>
    <div class="loader-inner"></div>
</div>

<h2><span>My Order</span></h2>

<div class="info-box">
    <p>You can add or remove any of the available choices.</p>
    <p>When you finish with your choices, click on 'Continue' for a final overview and confirmation of your order.</p>
</div>
<?php if (isset($final_cart) && !empty($final_cart)) { ?>

    <div class="coupon-code-box"  id="anchorName2">
        <label for="coupon-code">Coupon Code</label>
        <div class="clearfix">
            <?php if (isset($_SESSION['Coupon'])) { ?>
                <input class="inbox coupon-code" type="text" name="coupon code" placeholder="" value='<?php echo $_SESSION['Coupon']['Coupon']['coupon_code']; ?>'/>
            <?php } else { ?>
                <input class="inbox coupon-code" type="text" name="coupon code" placeholder="" />
            <?php } ?>
            <?php
            if (!empty($coupon_data)) {
                if ($coupon_data == 1) {
                    echo '<span style="float:left;color:red;">Please enter valid coupon code</span>';
                } elseif ($coupon_data == 2) {
                    echo '<span style="float:left;color:red;">Coupon has been expired.</span>';
                }
            }
            ?>
            <?php
            if (isset($_SESSION['Coupon'])) {
                if ($_SESSION['Coupon']['Coupon']['discount_type'] == 1) {
                    echo '<span style="float:left;color:green;">You have got ' . $this->Common->amount_format($_SESSION['Coupon']['Coupon']['discount']) . ' off on total amount</span>';
                } else {
                    echo '<span style="float:left;color:green;">You have got ' . $_SESSION['Coupon']['Coupon']['discount'] . '% off on total amount</span>';
                }
            }
            ?>
        </div>
    </div>

    <?php echo $this->Form->create('CartInfo', array('url' => array('controller' => 'Products', 'action' => 'orderDetails'), 'onsubmit' => array('return checkMinAmount();'))); ?>
    <div class="responsive-table">
        <table class="table table-striped no-scroll">
            <tr>
                <th>&nbsp;</th>
                <th>Qty</th>
                <th>Item Name</th>
                <th class="text-center">Price</th>
            </tr>
            <?php
            $gross_amount = 0;

            //pr($this->Session->read('cart'));
            $final_cart = $this->Common->taxCalculation($final_cart);
            $ItemOfferArray = array();
            foreach ($final_cart as $session_key => $row) {
                $defaulttopping = "";
                $paidtopping = "";
                $paidpreference = "";
                $gross_amount = $gross_amount + @$row['Item']['final_price'];
                $quantity = $row['Item']['quantity'];
                $price = $row['Item']['final_price'];
                $type = "";
                if (isset($row['Item']['type'])) {
                    $type = $row['Item']['type'];
                }
                $size = "";
                if (isset($row['Item']['size'])) {
                    $size = $row['Item']['size'];
                }
                if (isset($row['Item']['subPreferenceOld']) && !empty($row['Item']['subPreferenceOld'])) {
                    foreach ($row['Item']['subPreferenceOld'] as $prekey => $preval) {
                        if ($preval) {
                            $paidpreference .= ($preval['size'] > 1) ? $preval['size'] : '';
                            $paidpreference .=' ' . $preval['name'] . ' ' . $this->Common->amount_format($preval['price']) . "\n";
                        }
                    }
                    $paidpreference = rtrim($paidpreference, ", ");
                }
                if (isset($row['Item']['default_topping'])) {
                    foreach ($row['Item']['default_topping'] as $val) {
                        if ($val['size'] == 1) {//unit 1 is default
                            $val['price'] = 0.00;
                        } else {
                            $val['price'] = $val['price'] * $val['size'];
                        }
                        $defaulttopping .= $val['name'] . ' ' . $this->Common->amount_format($val['price']) . "\n";
                    }

                    $defaulttopping = rtrim($defaulttopping, ",");
                }
                if (isset($row['Item']['paid_topping'])) {
                    foreach ($row['Item']['paid_topping'] as $val) {
                        $paidtopping .= ($val['size'] > 1) ? $val['size'] : '';
                        $paidtopping .=' ' . $val['name'] . ' ' . $this->Common->amount_format($val['price'] * $val['size']) . "\n";
                    }

                    $paidtopping = rtrim($paidtopping, ",");
                }
                $description = "Quantity : {$quantity}\n
                                Price($) : {$this->Common->amount_format($price)}\n";
                if (!empty($type)) {
                    $description .= "
                                Type : {$type}\n";
                }
                if (!empty($size)) {
                    $description .= "
                                Size : {$size}\n";
                }
                if (!empty($paidpreference)) {
                    $description .= "
                Preferences : 
                            {$paidpreference}";
                }

                if (!empty($defaulttopping)) {
                    $description .= "
                Default Add-on : 
                            {$defaulttopping}";
                }
                if (!empty($paidtopping)) {
                    $description .= "
                Paid Add-on : 
                            {$paidtopping}";
                }
                $CatName = '';
                $storetaxInfo = array();
                $CategoryName = $this->Common->getCategoryName($row['Item']['categoryid']);
                if ($CategoryName) {
                    $CatName = $CategoryName['Category']['name'] . " - ";
                }
                $taxlabel = '';

                if ($row['Item']['taxamount'] > 0) {
                    $taxlabel = "<font style='color:#000000;font-weight:bold;' title='Tax applicable'>T</font>";
                    $totaltaxPrice = $totaltaxPrice + $row['taxCalculated'];
                }
                ?>
                <tr>
                    <td class="remove" id="<?php echo $session_key; ?>"><?php //echo $this->Html->image('remove.png',array('title'=>'Remove'));                                                                                           ?> <a href="javascript:void(0)"><i class="fa fa-times"></i></a></td>
                    <td class="cart-row" id="<?php @$row['Item']['id']; ?>" key="<?php echo $session_key; ?>"><input class="inbox quantity" type="number" name="quantity"  value="<?php echo @$row['Item']['quantity'] ?>" min="1"  max="100" step="1" value="1" > </td>

                    <?php
                    $Interval = "";
                    if (isset($row['Item']['interval_id'])) {
                        $intervalId = $row['Item']['interval_id'];
                        $Interval = $this->Common->getIntervalName($intervalId);
                    }
                    ?>
                    <td class="item-id hover" index_id="<?php echo $session_key; ?>" id="<?php @$row['Item']['id']; ?>"  > <a data-tooltip="<?php echo $description; ?>">
                            <?php
                            if ($Interval) {
                                echo $Interval . "</br>";
                            }
                            ?>
                            <?php echo $itemName = $CatName . @$row['Item']['size'] . ' ' . @$row['Item']['type'] . ' ' . @$row['Item']['name']; ?><br/>
                            <?php
                            if (!empty($row['Item']['OfferItemName'])) {
                                echo "Offered Item-<br/>" . $row['Item']['OfferItemName'];
                            }
                            ?>
                        </a></td>

                    <td class="text-center" ><?php echo $this->Common->amount_format($row['Item']['final_price']) . $taxlabel; ?></td>
                </tr>


                <?php
                $ItemOfferArray[$session_key]['itemName'] = $itemName;
                $ItemOfferArray[$session_key]['freeQuantity'] = @$row['Item']['freeQuantity'];
                $ItemOfferArray[$session_key]['price'] = $row['Item']['SizePrice'];
                $ItemOfferArray[$session_key]['description'] = $description;
            }
            ?>

            <tr>
                <td></td>
                <td></td>
                <td>SubTotal</td>
                <td class="text-center">
                    <?php
                    if ($gross_amount) {
                        echo $this->Common->amount_format($gross_amount);
                    } else {
                        echo $this->Common->amount_format(0);
                        $gross_amount = 0;
                    }
                    ?>
                </td>
            </tr>
            <?php
            $deliveryFee = 0.00;
            if ($this->Session->check('Zone.fee')) {
                $deliveryFee = ($order_type == 3) ? $this->Session->read('Zone.fee') : 0;
            }
            if ($order_type == 3 && $deliveryFee > 0) {
                ?>
                <tr> 
                    <td></td>
                    <td></td>
                    <td>Delivery fee</td>
                    <td class="text-center">
                        <?php echo $this->Common->amount_format($deliveryFee); ?>
                    </td>
                </tr>
            <?php } ?> 
            <?php
            $serviceFee = 0.00;
            if ($this->Session->check('service_fee')) {
                $serviceFee = $_SESSION['final_service_fee'];
                ?>    
                <tr> 
                    <td></td>
                    <td></td>
                    <td>Service fee</td>
                    <td class="text-center">
                        <?php echo $this->Common->amount_format($serviceFee); ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            <?php
            if (!empty($storeSetting['StoreSetting']['discount_on_extra_fee'])) {
                $gross_amount = $gross_amount + $serviceFee + $deliveryFee;
            } else {
                $gross_amount = $gross_amount;
            }
            ?>
            <?php if ($gross_amount && isset($_SESSION['Coupon'])) { ?>
                <tr>
                    <td><?php echo $this->Html->link('<i class="fa fa-times"></i>', array('controller' => 'products', 'action' => 'removeCoupon'), array('escape' => false, 'confirm' => 'Are you sure to remove coupon?')); ?></td>
                    <td></td>
                    <td>Coupon Discount</td>
                    <td class="text-center">
                        <?php
                        if ($_SESSION['Coupon']['Coupon']['discount_type'] == 1) { // Price
                            $discount_amount = $_SESSION['Coupon']['Coupon']['discount'];
                        } else { // Percentage
                            $discount_amount = $gross_amount * ($_SESSION['Coupon']['Coupon']['discount'] / 100);
                        }
                        if ($gross_amount < $discount_amount) {
                            $discount_amount = $gross_amount;
                        }
                        $gross_amount = $gross_amount - $discount_amount;
                        echo '-' . $this->Common->amount_format($discount_amount);
                        ?>
                    </td>
                </tr>
            <?php } ?>


            <?php
            if ($ItemOfferArray) {
                $ItemDiscount = 0;
                foreach ($ItemOfferArray as $offkey => $freeunitdata) {
                    if ($freeunitdata['freeQuantity'] > 0) {
                        ?>
                        <tr>
                            <td></td>
                            <td><small>Free Item</small></td>
                            <td colspan="2"><small><?php echo $freeunitdata['freeQuantity'] . ' ' . $freeunitdata['itemName']; ?></small></td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>


            <?php if ($totaltaxPrice) { ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td>Tax</td>
                    <td class="text-center">
                        <?php
                        if ($totaltaxPrice >= 0) {
                            echo $this->Common->amount_format($totaltaxPrice);
                        } else {
                            $totaltaxPrice = 0;
                        }
                        ?>
                    </td>
                </tr>
            <?php } ?>

            <?php
            if (empty($storeSetting['StoreSetting']['discount_on_extra_fee'])) {
                $gross_amount = $gross_amount + $serviceFee + $deliveryFee;
            }
            if ($totaltaxPrice) {
                $gross_amount = $gross_amount + $totaltaxPrice;
            }
            if (!empty($ItemDiscount)) {
                $gross_amount = $gross_amount - $ItemDiscount;
            }
            ?>
            <tr>
                <td></td>
                <td></td>
                <td class="common-bold">Total</td>
                <td class="text-center common-bold">
                    <?php
                    if ($gross_amount) {
                        $gross_amount = $gross_amount;
                    } else {
                        $gross_amount = 0;
                    }
                    if ($gross_amount >= 0) {
                        echo $this->Common->amount_format($gross_amount);
                    }
                    ?>
                </td>
            </tr>
        </table>
    </div>
    <?php if ($this->Session->check('cart')) { ?>
        <span class="cls_no_deliverable"></span>
        <?php /* $storeInfo=$this->Common->getStoreDetail($this->Session->read('store_id'));
          $minimum_price=$storeInfo['Store']['minimum_order_price']; */
        ?>
        <div class="text-center" id="desktop_continue"> <input type="submit" value="Continue" class="btn green-btn pink-btn makeorder"/> </div>

        <div id="mobile_continuemenu"> <input type="button" value="Continue to menu" class="btn green-btn pink-btn makeorder"/> </div>

        <div id="mobile_continue"> <input type="submit" value="Continue to Payment" class="btn green-btn pink-btn makeorder"/> </div>

    <?php } ?>
    <?php echo $this->Form->end(); ?>
<?php } else { ?>

    <div class="info-box">
        <p class="no-order">Your cart is empty.</p>
        <p></p>
    </div>



<?php } ?>

<div class="toggle-scroll">
    <a href="javascript:void(0)" id="toggle_scroll">click to disable scroll</a>
</div>
<?php //echo $this->element('modal/error_popup_old')       ?>

<script>

    /* Get Cart Count */
    function cartcount() {
        $.ajax({
            type: 'post',
            url: '/Products/getcartCount',
            data: {},
            success: function (data1) {
                if (data1) {
                    $('.numberCircle').html(data1);
                }
            }
        });
    }

    function checkMinAmount() {
        $('.cls_no_deliverable').css('display', 'none');
        var total = "<?php echo $gross_amount; ?>";
<?php
$storeInfo = $this->Common->getStoreDetail($this->Session->read('store_id'));
if ($order_type == 2) {
    if ($storeInfo['Store']['is_pick_beftax']) {
        $totalwithoutTax = $gross_amount - $totaltaxPrice;
        ?>
                total = "<?php echo $totalwithoutTax; ?>";
        <?php
    }
    $minimum_price = $storeInfo['Store']['minimum_takeaway_price'];
} else {
    if ($storeInfo['Store']['is_delivery_beftax']) {
        $totalwithoutTax = $gross_amount - $totaltaxPrice;
        ?>
                total = "<?php echo $totalwithoutTax; ?>";
        <?php
    }
    $minimum_price = $storeInfo['Store']['minimum_order_price'];
}
?>
        var min_price =<?php echo $minimum_price; ?>;

        if ((total) >= (min_price)) {

        } else {
//            var message = "Order total should be equal or more than $<?php echo number_format($minimum_price, 2); ?> (minimum order price)";
//            $("#errorPop").modal('show');
//            $("#errorPopMsg").html(message);
//            $('.cls_no_deliverable').addClass('message message-danger');
//            var message = "Order total should be equal or more than $<?php echo number_format($minimum_price, 2); ?> (minimum order price)";
//            $('.cls_no_deliverable').html(message);
//            $('.cls_no_deliverable').css('display', 'block');
//            $(".cls_no_deliverable").fadeOut(6000);
            //return false;
        }
        res = true;
        $.ajax({
            type: 'POST',
            url: '/orderOverviews/checkMendatoryItem',
            async: false,
            success: function (response) {
                result = jQuery.parseJSON(response);
                if (result.status == 'Error') {
                    $("#errorPop").modal('show');
                    $("#errorPopMsg").html(result.msg);
                    res = false;
                } else {
                    res = true;
                }

            }
        });
        return res;
    }
</script>




