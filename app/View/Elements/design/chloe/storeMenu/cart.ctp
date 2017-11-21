<?php
$gross_amount = 0;
$discount_amount = 0;
$totaltaxPrice = 0;
?>
<?php
$order_type = 0;
if (isset($_SESSION['ordersummary']['order_type'])) {
    $order_type = $_SESSION['ordersummary']['order_type'];
}
?>
<div class="my-order">
    <div class="common-title pink-bg theme-bg-1 "> <h3>MY ORDER</h3> </div>
    <div class="my-order-body">
        <div class="p-align-fix">
            <p>You can add or remove any of the available choices.</p>
            <p>When you finish with your choices, click on 'Continue' for a final overview and confirmation of your order.</p>
        </div>
    </div>
    <?php if (isset($final_cart) && !empty($final_cart)) { ?>
        <div class="table-quantity">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr class="theme-border-1">
                            <th width="60%">Item Name</th>
                            <th width="10%">Qty</th>
                            <th width="40%">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $gross_amount = 0;
                        //prx($final_cart);
                        //pr($this->Session->read('cart'));
                        $final_cart = $this->Common->taxCalculation($final_cart);
                        $ItemOfferArray = array();
                        foreach ($final_cart as $session_key => $row) {
                            $defaulttopping = "";
                            $paidtopping = "";
                            $paidpreference = "";
                            $gross_amount = $gross_amount + @$row['Item']['final_price'];
                            $quantity = $row['Item']['quantity'];
                            $price = number_format($row['Item']['final_price'], 2);
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
                                    $paidpreference .= ($preval['size'] > 1) ? $preval['size'] : '';
                                    $paidpreference.=' ' . $preval['name'] . ' ' . $this->Common->amount_format($preval['price']) . "\n";
                                }
                                $paidpreference = rtrim($paidpreference, ", ");
                            }
                            
                            if (isset($row['Item']['default_topping'])) {
                                foreach ($row['Item']['default_topping'] as $val) {
                                    if ($val['size'] == 1) {//unit 1 is default
                                        $val['price'] = 0.00;
                                    } else{
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

                                $paidtopping = rtrim($paidtopping, ", ");
                            }

                            
                            $description = "Quantity : {$quantity}\n
                                Price($) : {$price}\n";
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
                            if (!empty($row['Item']['taxamount'])) {
                                $taxlabel = "<font style='color:#000000;font-weight:bold;' title='Tax applicable'>T</font>";
                                $totaltaxPrice = $totaltaxPrice + $row['taxCalculated'];
                            }
                            ?>

                            <tr>
                                <td>
                                    <span class="remove" id="<?php echo $session_key; ?>"><a href="javascript:void(0)"><i class="fa fa-times"></i></a></span>

                                    <h5 class="item-id hover" index_id="<?php echo $session_key; ?>" id="<?php @$row['Item']['id']; ?>">
                                        <a data-tooltip="<?php echo $description; ?>">
                                            <?php
                                            $Interval = "";
                                            if (isset($row['Item']['interval_id'])) {
                                                $intervalId = $row['Item']['interval_id'];
                                                $Interval = $this->Common->getIntervalName($intervalId);
                                            }
                                            ?>
                                            <?php
                                            if ($Interval) {
                                                echo $Interval . "</br>";
                                            }
                                            ?>
                                            <?php echo $itemName = $CatName . @$row['Item']['size'] . ' ' . @$row['Item']['type'] . ' ' . @$row['Item']['name']; ?><br/><?php echo @$row['Item']['OfferItemName']; ?>
                                        </a>
                                    </h5>
                                </td>
                                <td key="<?php echo $session_key; ?>">
                                    <input type="number" value="<?php echo @$row['Item']['quantity'] ?>" class="c-quantity quantity" id="<?php @$row['Item']['id']; ?>" key="<?php echo $session_key; ?>" min="1"  max="100" step="1" value="1" style="min-width:40px;">
                                </td>

                                <td><span class="p-price theme-txt-col-1"><?php echo $this->Common->amount_format($row['Item']['final_price']) . $taxlabel; ?></span></td>

                                </div>
                            </tr>
                            <?php
                            $ItemOfferArray[$session_key]['itemName'] = $itemName;
                            $ItemOfferArray[$session_key]['freeQuantity'] = @$row['Item']['freeQuantity'];
                            $ItemOfferArray[$session_key]['price'] = $row['Item']['SizePrice'];
                            $ItemOfferArray[$session_key]['description'] = $description;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="coupon-code">
                <div class="cp-code-inner theme-border-1">
                    <h4>Coupon Code</h4>
                    <?php if (isset($_SESSION['Coupon'])) { ?>

                        <input class="cp-code error-coupon" type="text" name="coupon code" placeholder="" value='<?php echo $_SESSION['Coupon']['Coupon']['coupon_code']; ?>'/>
                    <?php } else { ?>
                        <input class="cp-code error-coupon" type="text" name="coupon code" placeholder="" />
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
<!--                    <input type="button" value="Apply" id='inprogress' class="apply-order theme-bg-1" />-->
                    <div class="c-sub-total">
                        <ul class="s-total">
                            <li>
                                <span class="lft-cgt"><strong>SubTotal</strong></span>
                                <span class="rgt-total">
                                    <?php
                                    if ($gross_amount) {
                                        echo $this->Common->amount_format($gross_amount);
                                    } else {
                                        $gross_amount = 0;
                                        echo $this->Common->amount_format(0);
                                    }
                                    ?>
                                </span>
                            </li>
                            <?php
                            $deliveryFee = 0.00;
                            if ($this->Session->check('Zone.fee')) {
                                $deliveryFee = ($order_type==3)?$this->Session->read('Zone.fee'):0;
                            }
                            if ($order_type == 3 && $deliveryFee > 0) {
                                ?>
                                <li> 
                                    <div class="total-price clearfix">
                                        <span>Delivery fee</span>
                                        <span>
                                            <?php
                                            echo $this->Common->amount_format($deliveryFee);
                                            ?>
                                        </span>
                                    </div>
                                </li>
                            <?php } ?>   
                            <?php
                            $serviceFee = 0.00;
                            if ($this->Session->check('service_fee')) {
                                $serviceFeeType = ($this->Session->check('service_fee_type') ? $this->Session->read('service_fee_type') : 1);
                                $serviceFee = $this->Session->read('service_fee');
                                if ($serviceFeeType != 1) {
                                    $serviceFee = ($serviceFee / 100) * $gross_amount;
                                }
                                if ($serviceFee > 0) {
                                    $_SESSION['final_service_fee'] = $serviceFee;
                                    ?>    
                                    <li> 
                                        <div class="total-price clearfix">
                                            <span>Service fee</span>
                                            <span>
                                                <?php
                                                echo $this->Common->amount_format($serviceFee);
                                                ?>
                                            </span>
                                        </div>
                                    </li>
                                <?php
                                }
                            }
                            ?>
                            <?php
                            if (!empty($storeSetting['StoreSetting']['discount_on_extra_fee'])) {
                                $gross_amount = number_format($gross_amount, 2) + number_format($serviceFee, 2) + number_format($deliveryFee, 2);
                            } else {
                                $gross_amount = number_format($gross_amount, 2);
                            }
                            ?>
                            <?php if ($gross_amount && isset($_SESSION['Coupon'])) { ?>
                                <li>
                                    <span class="lft-cgt"><strong>Coupon Discount</strong></span>
                                    <span class="rgt-total">
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
                                        <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('controller' => 'products', 'action' => 'removeCoupon'), array('escape' => false, 'confirm' => 'Are you sure to remove coupon?')); ?>
                                    </span>
                                </li>
                            <?php } ?>


                            <?php
                            if ($ItemOfferArray) {
                                $ItemDiscount = 0;
                                foreach ($ItemOfferArray as $offkey => $freeunitdata) {
                                    if ($freeunitdata['freeQuantity'] > 0) {
                                        ?>
                                        <li>
                                            <span class="lft-cgt"><strong>Free Item</strong></span>
                                            <span class="rgt-total">
                                                <?php echo $freeunitdata['freeQuantity'] . ' ' . $freeunitdata['itemName']; ?>
                                                <?php
                                                //$ItemOfferDiscount = $freeunitdata['price'] * $freeunitdata['freeQuantity'];
                                                //echo '-$' . number_format($freeunitdata['price'] * $freeunitdata['freeQuantity'], 2);
                                                ?>
                                            </span>
                                        </li>
                                        <?php
                                        //$ItemDiscount = $ItemDiscount + $ItemOfferDiscount;
                                    }
                                }
                            }
                            ?>


                            <?php if ($totaltaxPrice) { ?>
                                <li class="theme-border-1">
                                    <span class="lft-cgt"><strong>Tax</strong></span>
                                    <span class="rgt-total">
                                        <?php
                                        if ($totaltaxPrice >= 0) {
                                            echo $this->Common->amount_format($totaltaxPrice);
                                        } else {
                                            $totaltaxPrice = 0;
                                        }
                                        $_SESSION['taxPrice'] = $totaltaxPrice;
                                        ?>
                                    </span>
                                </li>
                            <?php } ?>
                            <?php
                            if (empty($storeSetting['StoreSetting']['discount_on_extra_fee'])) {
                                $gross_amount = $gross_amount + number_format($serviceFee, 2) + number_format($deliveryFee, 2);
                            }
                            if ($totaltaxPrice) {
                                $gross_amount = $gross_amount + number_format($totaltaxPrice, 2);
                            }
                            if (!empty($ItemDiscount)) {
                                $gross_amount = $gross_amount - number_format($ItemDiscount, 2);
                            }
                            ?>

                            <li class="theme-border-1">
                                <span class="lft-cgt"><strong>Total</strong></span>
                                <span class="rgt-total">
                                    <?php
                                    if ($gross_amount) {
                                        $gross_amount = number_format($gross_amount, 2);
                                    } else {
                                        $gross_amount = 0;
                                    }
                                    if ($gross_amount >= 0) {
                                        echo $this->Common->amount_format($gross_amount);
                                    }
                                    ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    <?php } else { ?>

        <div class="info-box">
            <p class="no-order">Your cart is empty.</p>
        </div>
    <?php } ?>

    <div class="continue mo-item-center">
        <div id="desktop_continue"> <input type="submit" value="CONTINUE" class="theme-bg-1 cont-btn makeorder" /> </div>

        <div id="mobile_continuemenu"> <input type="button" value="Continue to menu" class="theme-bg-1 cont-btn makeorder" /> </div>

        <div id="mobile_continue"> <input type="submit" value="Continue to Payment" class="theme-bg-1 cont-btn makeorder" /> </div>
    </div>



</div>








<style>
    a:hover {
        text-decoration: none;
    }
    [data-tooltip] {
        position: relative;
        z-index: 2;
        cursor: pointer;
    }
    [data-tooltip]:before,
    [data-tooltip]:after {
        visibility: hidden;
        -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
        filter: "progid: DXImageTransform.Microsoft.Alpha(Opacity=0)";
        opacity: 0;
        pointer-events: none;
    }
    [data-tooltip]:before {
        position: absolute;
        bottom: 150%;
        left: 50%;
        margin-bottom: 5px;
        margin-left: -80px;
        padding: 7px;
        width: 160px;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        background-color: #000;
        background-color: hsla(0, 0%, 20%, 0.9);
        color: #fff;
        content: attr(data-tooltip);

        white-space: pre-line;
        text-align: left;
        font-size: 14px;
        line-height: 1.2;
    }
    [data-tooltip]:after {
        position: absolute;
        bottom: 150%;
        left: 50%;
        margin-left: -5px;
        width: 0;
        border-top: 5px solid #000;
        border-top: 5px solid hsla(0, 0%, 20%, 0.9);
        border-right: 5px solid transparent;
        border-left: 5px solid transparent;
        content: " ";
        font-size: 0;
        line-height: 0;

    }
    [data-tooltip]:hover:before,
    [data-tooltip]:hover:after {
        visibility: visible;
        -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";
        filter: "progid: DXImageTransform.Microsoft.Alpha(Opacity=100)";
        opacity: 1;
    }

    @media (max-width:55em) {
        #desktop_continue{
            display:none;
        }

        #mobile_continue{
            display:block;
        }
    }

    @media (min-width:55em) {
        #desktop_continue{
            display:block;
        }

        #mobile_continuemenu{
            display:none;
        }

        #mobile_continue{
            display:none;
        }
    }
</style>

<style>
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
        opacity: 1
    }
</style>









