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

            <input type="button" value="Apply" id='inprogress' class="btn green-btn pink-btn" />
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
    </div>

    <?php echo $this->Form->create('CartInfo', array('url' => array('controller' => 'Products', 'action' => 'orderDetails'))); ?>
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
                //$TaxInfo=$this->Common->getItemTax($row['Item']['id'],@$row['Item']['size_id']);
                //
    //if($TaxInfo['ItemPrice']['store_tax_id']){
                //    $storetaxInfo=$this->Common->getStoreTaxByID($TaxInfo['ItemPrice']['store_tax_id']);
                //    if(!empty($storetaxInfo['StoreTax']['tax_value'])){
                //        $taxlabel="<font style='color:#000000;font-weight:bold;' title='Tax applicable'>T</font>";
                //        $taxprice = ($storetaxInfo['StoreTax']['tax_value'] / 100) * $row['Item']['final_price'];
                //        $totaltaxPrice=$totaltaxPrice + $taxprice;
                //    }
                //}

                if ($row['Item']['taxamount'] > 0) {
                    $taxlabel = "<font style='color:#000000;font-weight:bold;' title='Tax applicable'>T</font>";
                    $totaltaxPrice = $totaltaxPrice + ($row['Item']['quantity'] * $row['Item']['taxamount']);
                }
                ?>
                <tr>
                    <td class="remove" id="<?php echo $session_key; ?>"><?php //echo $this->Html->image('remove.png',array('title'=>'Remove'));        ?> <a href="javascript:void(0)"><i class="fa fa-times"></i></a></td>
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
                            <?php echo $itemName = $CatName . @$row['Item']['size'] . ' ' . @$row['Item']['type'] . ' ' . @$row['Item']['name']; ?><br/><?php echo @$row['Item']['OfferItemName']; ?>
                        </a></td>

                    <td class="text-center" >$<?php echo @number_format($row['Item']['final_price'], 2) . $taxlabel; ?></td>
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
                        echo '$' . number_format($gross_amount, 2);
                    } else {
                        $gross_amount = 0;
                    }

                    //$_SESSION['cart']['grandTotal']=$gross_amount;
                    ?>
                </td>
            </tr>

            <?php if ($gross_amount && isset($_SESSION['Coupon'])) { ?>
                <tr>
                    <td><?php echo $this->Html->link('<i class="fa fa-times"></i>', array('controller' => 'products', 'action' => 'removeCoupon'), array('escape' => false, 'confirm' => 'Are you sure to delete coupon?')); ?></td>
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
                        echo '-$' . number_format($discount_amount, 2);
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
                            <td><small><?php echo $freeunitdata['freeQuantity'] . ' ' . $freeunitdata['itemName']; ?></small></td>
                            <td class="item-id hover">

                                <?php
                                //$ItemOfferDiscount = $freeunitdata['price'] * $freeunitdata['freeQuantity'];
                                //echo '-$' . number_format($freeunitdata['price'] * $freeunitdata['freeQuantity'], 2);
                                ?>

                            </td>
                        </tr>
                        <?php
                        //$ItemDiscount = $ItemDiscount + $ItemOfferDiscount;
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
                        if ($totaltaxPrice) {
                            echo '$' . number_format($totaltaxPrice, 2);
                        } else {
                            echo '$' . $totaltaxPrice = '0.00';
                        }
                        $_SESSION['taxPrice']=$totaltaxPrice;
                        ?>
                    </td>
                </tr>
            <?php } ?>


            <tr>
                <td></td>
                <td></td>
                <td class="common-bold">Total</td>
                <td class="text-center common-bold">
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
                    ?></td>
            </tr>


        </table>
    </div>
    <?php if ($this->Session->check('cart')) { ?>
        <span class="cls_no_deliverable"></span>
        <?php /* $storeInfo=$this->Common->getStoreDetail($this->Session->read('store_id'));
          $minimum_price=$storeInfo['Store']['minimum_order_price']; */
        ?>
        <div class="text-center" id="desktop_continue"> <input type="submit" value="Continue" class="btn green-btn pink-btn makeorder" /> </div>

        <div id="mobile_continuemenu"> <input type="button" value="Continue to menu" class="btn green-btn pink-btn makeorder" /> </div>

        <div id="mobile_continue"> <input type="submit" value="Continue to Payment" class="btn green-btn pink-btn makeorder" /> </div>

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


