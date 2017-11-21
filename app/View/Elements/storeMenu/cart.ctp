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

<div class="right-menu">
    <?php echo $this->Form->create('CartInfo', array('url' => array('controller' => 'Products', 'action' => 'orderDetails'))); ?>
    <div id="ordercart">
        <div class="common-title"> <h3>MY ORDER</h3> </div>
        <div class="my-order-body">
            <div class="p-align-fix">
                <p>You can add or remove any of the available choices.</p>
                <p>When you finish with your choices, click on 'Continue' for a final overview and confirmation of your order.</p>
            </div>
        </div>

        <?php if (isset($final_cart) && !empty($final_cart)) { ?>
            <div class="coupon-code">
                <h4>Coupon Code</h4>
                <?php if (isset($_SESSION['Coupon'])) { ?>

                    <input class="cp-code  error-coupon" type="text" name="coupon code" placeholder="" value='<?php echo $_SESSION['Coupon']['Coupon']['coupon_code']; ?>'/>
                <?php } else { ?>
                    <input class="cp-code  error-coupon" type="text" name="coupon code" placeholder="" />
                <?php } ?>
                <input type="button" value="Apply" id='inprogress' class="apply-order theme-bg-1" />
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
                        echo '<span style="float:left;color:green;">You have got $' . number_format($_SESSION['Coupon']['Coupon']['discount'], 2) . ' off on total amount</span>';
                    } else {
                        echo '<span style="float:left;color:green;">You have got ' . $_SESSION['Coupon']['Coupon']['discount'] . '% off on total amount</span>';
                    }
                }
                ?>
            </div>






            <div class="order-list-section">
                <ul class="list-1 order-list-wrap">
                    <?php
                    $gross_amount = 0;
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
                        if (isset($row['Item']['default_topping'])) {
                            foreach ($row['Item']['default_topping'] as $val) {
                                $defaulttopping .= $val['name'] . " ,";
                            }

                            $defaulttopping = rtrim($defaulttopping, ",");
                        }
                        if (isset($row['Item']['paid_topping'])) {
                            foreach ($row['Item']['paid_topping'] as $val) {
                                $addonsize = 1;
                                $addOnSizedetails = $this->Common->getaddonSize($val['size']);
                                if ($addOnSizedetails) {
                                    $addonsize = $addOnSizedetails['AddonSize']['size'];
                                }
                                $paidtopping .= $addonsize . ' ' . $val['name'] . " ,";
                            }

                            $paidtopping = rtrim($paidtopping, ",");
                        }


                        if (isset($row['Item']['subpreference']) && !empty($row['Item']['subpreference'])) {
                            foreach ($row['Item']['subpreference'] as $prekey => $preval) {
                                if ($preval) {
                                    $subdetails = $this->Common->getSubPreferenceDetail($preval);
                                    $paidpreference .= $subdetails['SubPreference']['name'] . " ,";
                                }
                            }
                            $paidpreference = rtrim($paidpreference, ",");
                        }



                        $description = "Quantity : {$quantity}
                        Price($) : {$price}";
                        if (!empty($size)) {
                            $description .= "
                                Size : {$size}";
                        }
                        if (!empty($type)) {
                            $description .= "
                                Type : {$type}";
                        }
                        if (!empty($defaulttopping)) {
                            $description .= "
                                Default Add-on : {$defaulttopping}";
                        }
                        if (!empty($paidtopping)) {
                            $description .= "
                                Paid Add-on : {$paidtopping}";
                        }
                        if (!empty($paidpreference)) {
                            $description .= "
                                Preferences : {$paidpreference}";
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
                            //$totaltaxPrice=$totaltaxPrice + ($row['Item']['quantity'] * $row['Item']['taxamount']);
                            //$totaltaxPrice = $totaltaxPrice + ($row['Item']['taxvalue'] / 100) * $row['Item']['final_price'];
                            $totaltaxPrice = $totaltaxPrice + $item['taxCalculated'];
                        }
                        ?>
                        <li>
                            <div class="item-1  clearfix">

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
                                <span class="item-cost">$<?php echo @number_format($row['Item']['final_price'], 2) . $taxlabel; ?></span>
                                <div class="input-group spinner">
                                    <div class="in-spinner">
                                        <button type="button" class="btn btn-default pull-right quantity" key="<?php echo $session_key; ?>"><i aria-hidden="true" class="fa fa-plus "></i></button>
                                        <input type="text" value="<?php echo @$row['Item']['quantity'] ?>" class="form-control quantity" id="<?php @$row['Item']['id']; ?>" key="<?php echo $session_key; ?>">
                                        <button type="button" class="btn btn-default pull-left quantity" key="<?php echo $session_key; ?>"><i aria-hidden="true" class="  fa fa-minus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php
                        $ItemOfferArray[$session_key]['itemName'] = $itemName;
                        $ItemOfferArray[$session_key]['freeQuantity'] = @$row['Item']['freeQuantity'];
                        $ItemOfferArray[$session_key]['price'] = $row['Item']['SizePrice'];
                        $ItemOfferArray[$session_key]['description'] = $description;
                    }
                    ?>




                </ul>
                <ul>
                    <li>  <div class="total-price clearfix">
                            <span>SubTotal</span>
                            <span>
                                <?php
                                if ($gross_amount) {
                                    echo '$' . number_format($gross_amount, 2);
                                } else {
                                    $gross_amount = 0;
                                }
                                ?>
                            </span></div>
                    </li>

                    <?php if ($gross_amount && isset($_SESSION['Coupon'])) { ?>
                        <li>    <div class="total-price clearfix">
                                <span>Coupon Discount</span>
                                <span>
                                    <?php
                                    if ($_SESSION['Coupon']['Coupon']['discount_type'] == 1) { // Price
                                        $discount_amount = $_SESSION['Coupon']['Coupon']['discount'];
                                    } else { // Percentage
                                        $discount_amount = $gross_amount * ($_SESSION['Coupon']['Coupon']['discount'] / 100);
                                    }
                                    if ($gross_amount < $discount_amount) {
                                        $discount_amount = $gross_amount;
                                    }
                                    echo '-$' . number_format($discount_amount, 2);
                                    ?>
                                </span>
                                <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('controller' => 'products', 'action' => 'removeCoupon'), array('escape' => false, 'confirm' => 'Are you sure to delete coupon?')); ?>
                            </div>
                        </li>
                    <?php } ?>

                    <?php
                    if ($ItemOfferArray) {
                        $ItemDiscount = 0;
                        foreach ($ItemOfferArray as $offkey => $freeunitdata) {
                            if ($freeunitdata['freeQuantity'] > 0) {
                                ?>
                                <li>    <div class="total-price clearfix">
                                        <span>Free Item</span>
                                        <span>
                                            <?php echo $freeunitdata['freeQuantity'] . ' ' . $freeunitdata['itemName']; ?>
                                            <?php
                                            //$ItemOfferDiscount = $freeunitdata['price'] * $freeunitdata['freeQuantity'];
                                            //echo '-$' . number_format($freeunitdata['price'] * $freeunitdata['freeQuantity'], 2);
                                            ?>
                                        </span></div>
                                </li>
                                <?php
                                //$ItemDiscount = $ItemDiscount + $ItemOfferDiscount;
                            }
                        }
                    }
                    ?>

                    <?php if ($totaltaxPrice) { ?>
                        <li> <div class="total-price clearfix">
                                <span>Tax</span>
                                <span>
                                    <?php
                                    if ($totaltaxPrice >= 0) {
                                        echo '$' . number_format($totaltaxPrice, 2);
                                    } else {
                                        echo '$' . $totaltaxPrice = '0.00';
                                    }
                                    $_SESSION['taxPrice']=$totaltaxPrice;
                                    ?>
                                </span></div>
                        </li>
                    <?php } ?>


                    <li>   <div class="total-price clearfix">
                            <span>Total</span>
                            <span>
                                <?php
                                if ($gross_amount) {
                                    $gross_amount = $gross_amount + $totaltaxPrice - $discount_amount - $ItemDiscount;
                                    $gross_amount = number_format($gross_amount, 2);
                                    if ($gross_amount > 0) {
                                        echo '$' . $gross_amount;
                                    } else {
                                        echo '$' . '0.00';
                                    }
                                } else {
                                    $gross_amount = 0;
                                }

                                //$_SESSION['cart']['grandTotal']=$gross_amount;
                                ?>
                            </span>
                        </div>
                    </li>
                </ul>
            </div>



            <?php if ($this->Session->check('cart')) { ?>
                <span class="cls_no_deliverable message message-danger"></span>
                <?php /* $storeInfo=$this->Common->getStoreDetail($this->Session->read('store_id'));
                  $minimum_price=$storeInfo['Store']['minimum_order_price']; */
                ?>
            <?php } ?>
            <?php ?>
        <?php } else { ?>

            <div class="info-box">
                <p class="no-order">Your cart is empty.</p>
            </div>
        <?php } ?>

    </div>




    <div class="continue mo-item-center">
        <div id="desktop_continue"> <input type="submit" value="CONTINUE" class="theme-bg-1 cont-btn makeorder" /> </div>

        <div id="mobile_continuemenu"> <input type="button" value="Continue to menu" class="theme-bg-1 cont-btn makeorder" /> </div>

        <div id="mobile_continue"> <input type="submit" value="Continue to Payment" class="theme-bg-1 cont-btn makeorder" /> </div>
    </div>

    <?php echo $this->Form->end(); ?>
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

        #mobile_continuemenu{
            display:block;
            text-align: center;
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









