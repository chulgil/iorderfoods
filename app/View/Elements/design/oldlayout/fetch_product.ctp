
<?php $url = HTTP_ROOT; ?>
<?php $imageurl = HTTP_ROOT . 'MenuItem-Image/'; ?>
<?php
if (isset($productInfo) && !empty($productInfo)) {
    $PreorderAllowed = $this->Common->checkPreorder();
    ?>

    <h2><span>Create Your Order</span></h2>
    <input type="hidden" id="chkloginstatus" name="chkloginstatus" value="" >
    <input type="hidden" id="chknowavial" name="chknowavial" value="<?php echo (!isset($avalibilty_status)) ? 1 : 0; ?>" >
    <input type="hidden" id="chkpreorder" name="chkpreorder" value="<?php echo ($PreorderAllowed) ? 1 : 0; ?>" >
    <?php echo $this->Form->create('Item', array('url' => array('controller' => 'Products', 'action' => 'addtoCart')));
    ?>
    <div class="product-listing">
        <div class="share-button">
            <span class="twitter-share">
                <a target="blank" href= "http://twitter.com/share?text=<?php echo htmlspecialchars($productInfo['Item']['name']); ?>  - <?php echo nl2br(htmlspecialchars($productInfo['Item']['description'])); ?> at just  <?php echo "$" . number_format($default_price, 2); ?>&url=<?php echo $url; ?>&via=<?php echo $_SESSION['storeName']; ?>"><?php echo $this->Html->image('tw-share-button.png', array('alt' => 'twshare')); ?> </a>

            </span>
            <span>


                <?php
                $strDomainUrl = $_SERVER['HTTP_HOST'];
                $strShareLink = "https://www.facebook.com/sharer/sharer.php?u=" . $strDomainUrl;
                ?>
                <a href="#" onclick='window.open("<?php echo $strShareLink; ?>", "", "width=500, height=300");'>
                    <?php echo $this->Html->image('fb-share-button.png', array('alt' => 'fbshare')); ?>
                </a>
            </span>
            <?php
            if ($itemisFree) {
                echo "<span>if you order this Item it will cost free</span>";
            }
            ?>

            <div class="clr"></div>
        </div>

        <div class="item-pic-title">
            <?php
            echo $this->Form->input('categoryID', array('type' => 'hidden', 'value' => $productInfo['Item']['category_id']));
            $CatName = '';
            $CategoryName = $this->Common->getCategoryName($productInfo['Item']['category_id']);
            if ($CategoryName) {
                $CatName = $CategoryName['Category']['name'] . " - ";
            }
            $Interval = "";
            if ($this->Session->read('Order.Item.interval_id')) {
                $intervalId = $this->Session->read('Order.Item.interval_id');
                $Interval = $this->Common->getIntervalName($intervalId);
            }
            ?>
            <h3><?php echo ($Interval) ? $Interval . "</br>" : ''; ?><?php echo $CatName . $productInfo['Item']['name']; ?> <small><?php echo nl2br($productInfo['Item']['description']); ?></small></h3>
        </div>

        <div class="product-pic-frame">
            <div class="product-pic">
                <?php
                if (isset($productInfo['Item']['image']) && $productInfo['Item']['image']) {
                    $image = "/MenuItem-Image" . "/" . $productInfo['Item']['image'];
                    echo $this->Html->image($image, array('id' => 'productImage'));
                    $price_class = 'product-price';
                    echo $this->Form->input('productpicid', array('type' => 'hidden', 'value' => 1));
                } else {
                    echo $this->Form->input('productpicid', array('type' => 'hidden', 'value' => 0));
                    $price_class = 'product-price product-price-single';
                }
                ?>
            </div>
            <span class="<?php echo $price_class; ?>">
                <?php
                if ($default_price) {
                    echo "$" . number_format($default_price, 2);
                }
                ?>
            </span>
        </div>
    </div>
    <?php if (!empty($display_offer)) { ?>
        <div class="row-divide clearfix">
            <h3>Available Offers</h3>
            <ul class="checkbox-listing clearfix">
                <?php foreach ($display_offer as $doff) { ?>
                    <li><i class="fa fa-pencil-square-o"></i>
                        <?php echo $doff; ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <?php
    }
    $preferencearray = array();
    if ($productInfo['Item']['sizeOnly'] == 2) { // Type
        if ($productInfo['ItemType']) {
            $options = array();
            foreach ($productInfo['ItemType'] as $type) {
                if ($type['Type']) {
                    if ($type['Type']['price'] <= 0) {
                        $options[$type['Type']['id']] = $type['Type']['name'];
                    } else {
                        $options[$type['Type']['id']] = $type['Type']['name'] . ' ($' . number_format($type['Type']['price'], 2) . ')';
                    }

                    if ($type['Type']['SubPreference']) {
                        foreach ($type['Type']['SubPreference'] as $key => $preferenceData) {
                            $preferencearray[$type['Type']['name']][$key]['subpreferncename'] = $preferenceData['name'];
                            $preferencearray[$type['Type']['name']][$key]['id'] = $preferenceData['id'];
                            $preferencearray[$type['Type']['name']][$key]['price'] = $preferenceData['price'];
                        }
                    }
                }
            }
            ?>

            <?php if ($preferencearray) { ?>
                <div class="row-divide clearfix">

                    <h3>Choose Preferences</h3>
                    <?php
                    $j = 0;
                    foreach ($preferencearray as $preference => $subpreferencedata) {
                        ?>
                        <div class="option-value clearfix countPreferences">
                            <h4><?php echo $preference; ?></h4>
                            <?php
                            $k = 0;
                            foreach ($subpreferencedata as $vkey => $predata) {
                                if ($predata['price'] > 0) {
                                    $price = '($' . $predata['price'] . ')';
                                } else {
                                    $price = '';
                                }
                                ?>
                                <span>
                                    <input type="radio" class='item_type' id='<?php echo $predata['id'] . '_' . $predata['subpreferncename']; ?>' name="data[Item][subpreference][<?php echo $preference; ?>]" value='<?php echo $predata['id']; ?>' >
                                    <label for='<?php echo $predata['id'] . '_' . $predata['subpreferncename']; ?>'><span></span><font><?php echo $predata['subpreferncename'] . " " . $price; ?></font></label>
                                </span>
                                <?php
                                $k++;
                            }
                            ?>
                        </div>
                <?php } ?>
                </div>
            <?php } ?>


            <?php
            if ($options) {
                ?>
                <?php
            }
        }
    }
    if ($productInfo['Item']['sizeOnly'] == 1) { //Size and Price
        if ($productInfo['ItemPrice']) {
            $sizes = array();
            foreach ($productInfo['ItemPrice'] as $size) {
                if ($size['Size']) {
                    $sizes[$size['Size']['id']] = $size['Size']['size'];
                }
            }
            if ($sizes) {
                ?>
                <div class="row-divide clearfix">

                    <h3>Choose Size</h3>

                    <div class="option-value clearfix">

                        <?php
                        $i = 0;
                        foreach ($sizes as $size_value => $size_name) {
                            ?>
                            <span>
                                <?php
                                if ($i == 0) {
                                    $checked = "checked = 'checked'";
                                } else {
                                    $checked = "";
                                }
                                ?>
                                <input type="radio" class='item_price countSizes' id='<?php echo $size_value . '_' . $size_name; ?>' <?php echo $checked; ?> name="data[Item][price]"  value='<?php echo $size_value; ?>'>
                                <label for='<?php echo $size_value . '_' . $size_name; ?>'><span></span><?php echo $size_name; ?></label>
                            </span>
                            <?php
                            $i++;
                        }
                        ?>

                    </div>


                </div>
            <?php } ?>

            <?php } ?>
        <div class="prefernceAndAddOns">
        <?php } ?>

        <?php
        if ($productInfo['Item']['sizeOnly'] == 3) { //Both Size and Type
            $preferencearray = array();

            if ($productInfo['ItemPrice']) {
                $sizes = array();
                foreach ($productInfo['ItemPrice'] as $size) {
                    if ($size['Size']) {
                        $sizes[$size['Size']['id']] = $size['Size']['size'];
                    }
                }
                if ($sizes) {
                    ?>
                    <div class="row-divide clearfix">

                        <h3>Choose Size</h3>

                        <div class="option-value clearfix">
                            <?php
                            $l = 0;
                            foreach ($sizes as $size_value => $size_name) {
                                ?>
                                <span>
                                    <?php
                                    if ($l == 0) {
                                        $checked = "checked = 'checked'";
                                    } else {
                                        $checked = '';
                                    }
                                    ?>
                                    <input type="radio" class='item_price countSizes' id='<?php echo $size_value . '_' . $size_name; ?>' name="data[Item][price]" <?php echo $checked; ?> value='<?php echo $size_value; ?>'>
                                    <label for='<?php echo $size_value . '_' . $size_name; ?>'><span></span><?php echo $size_name; ?></label>
                                </span>
                    <?php
                    $l++;
                }
                ?>

                        </div>

                    </div>
                <?php
            }
        }
        ?>
            <div class="prefernceAndAddOns">



                <?php
                if ($productInfo['ItemType']) {
                    $options = array();

                    foreach ($productInfo['ItemType'] as $type) {
                        if (!empty($type['Type']['price'])) {
                            if ($type['Type']['price'] <= 0) {
                                $options[$type['Type']['id']] = $type['Type']['name'];
                            } else {
                                $options[$type['Type']['id']] = $type['Type']['name'] . ' ($' . number_format($type['Type']['price'], 2) . ')';
                            }
                        }
                        if (!empty($type['Type']['SubPreference'])) {
                            if ($type['Type']['SubPreference']) {
                                foreach ($type['Type']['SubPreference'] as $key => $preferenceData) {
                                    $preferencearray[$type['Type']['name']][$key]['subpreferncename'] = $preferenceData['name'];
                                    $preferencearray[$type['Type']['name']][$key]['id'] = $preferenceData['id'];
                                    $preferencearray[$type['Type']['name']][$key]['price'] = $preferenceData['price'];
                                }
                            }
                        }
                    }
                    ?>
                        <?php if ($preferencearray) { ?>
                        <div class="row-divide">

                            <h3>Choose Preferences</h3>
                                <?php
                                $j = 0;
                                foreach ($preferencearray as $preference => $subpreferencedata) {
                                    ?>
                                <div class="option-value clearfix countPreferences">
                                    <h4><?php echo $preference; ?></h4>
                                    <?php
                                    $k = 0;
                                    foreach ($subpreferencedata as $vkey => $predata) {
                                        if ($predata['price'] > 0) {
                                            $price = '($' . $predata['price'] . ')';
                                        } else {
                                            $price = '';
                                        }
                                        ?>
                                        <span>
                                            <input type="radio" class='item_type' id='<?php echo $predata['id'] . '_' . $predata['subpreferncename']; ?>' name="data[Item][subpreference][<?php echo $preference; ?>]" value='<?php echo $predata['id']; ?>' >
                                            <label for='<?php echo $predata['id'] . '_' . $predata['subpreferncename']; ?>'><span></span><font><?php echo $predata['subpreferncename'] . " " . $price; ?></font></label>
                                        </span>
                                    <?php
                                    $k++;
                                }
                                ?>
                                </div>
                        <?php } ?>
                        </div>
                    <?php } ?>


            <?php
        }
    }
    if ($productInfo['Topping']) {
        ?>
                <div class="row-divide last">
                    <h3>Choose Add-on</h3>
                    <ul class="checkbox-listing clearfix">
                        <li>
        <?php
        foreach ($productInfo['Topping'] as $top) {
            if (!empty($top['Topping'])) {
                ?>
                                    <h4><?php echo $top['name']; ?></h4>
                                    <ul class="checkbox-listing clearfix countAddOns">
                                        <li>
                                            <?php
                                            foreach ($top['Topping'] as $topping) {
                                                if ($topping['price'] <= 0) {
                                                    $top_price = '';
                                                } else {
                                                    $top_price = '$' . number_format($topping['price'], 2);
                                                }
                                                ?>
                                                <?php
                                                if ($topping['ItemDefaultTopping']) {
                                                    $checked = true;
                                                    $top_value = 1;
                                                    $top_price = '';
                                                } else {
                                                    $checked = false;
                                                    $top_value = 2;
                                                }
                                                ?>
                                                <div class="inner-full-width-listing"> <div class="left-col"><?php
                                                        echo $this->Form->input('Item.toppings.' . $topping['id'] . '.id', array('type' => 'checkbox', 'label' => $topping['name'], 'class' => 'toppings toppings' . $topping["id"], 'value' => $topping['id'], 'checked' => $checked));
                                                        echo $this->Form->input('Item.toppings.' . $topping['id'] . '.type', array('type' => 'hidden', 'value' => $top_value, 'class' => 'type' . $topping["id"]));
                                                        echo $this->Form->input('Item.toppings.' . $topping['id'] . '.name', array('type' => 'hidden', 'value' => $topping['name']));
                                                        echo $this->Form->input('Item.toppings.' . $topping['id'] . '.price', array('type' => 'hidden', 'value' => $topping['price']));
                                                        ?><span class='topPrice topPrice<?php echo $topping["id"]; ?>'><?php echo $top_price; ?></span> </div>
                                                        <?php
                                                        if ($topping['no_size'] == 0) {
                                                            $toppingSizes = $toppingSizes;
                                                        } else {
                                                            $toppingSizes = array('0' => 1);
                                                        }
                                                        echo $this->Form->input('Item.toppings.' . $topping['id'] . '.size', array('class' => 'toppingSize inbox toppingSize' . $topping['id'], 'label' => false, 'type' => 'select', 'options' => $toppingSizes));
                                                        ?>
                                                </div> <?php } ?>
                                        </li>
                                    </ul>
                    <?php
                    }
                }
                ?>
                        </li>
                    </ul>
                </div>
            <?php } ?>
        </div>
        <input type="hidden" name="subdefault" id="subdefault" value="<?php echo $productInfo['Item']['default_subs_price']; ?>" />
        <input type="hidden" name="countSizes" id="countSizes" value="0" />
        <input type="hidden" name="countPreferences" id="countPreferences" value="0" />
        <input type="hidden" name="countAddOns" id="countAddOns" value="0" />
        <div class='parant_cls_no_deliverable text-center'>
            <?php
            if ($this->Session->check('Order')) {
                $is_delivery = $this->Session->read('Order.Item.is_deliverable');
                if ($is_delivery == 1) {
                    echo $this->Form->submit('Add to cart', array('class' => 'btn green-btn pink-btn', 'id' => 'addtocart'));
                } else {
                    echo "<div class='message message-success'><span class='cls_no_deliverable'>This product is not deliverable for now.</span></div>";
                }
            }
            ?>
        </div>
        <style>
            .addzero{
                width: 75%;
            }
        </style>
        <?php
        if (isset($itemzero)) {

            echo "<div class='message message-success addzero' style='margin-top:5px;text-align: center;'><span>Please Select The Choices Above</span></div>";
        }
        echo "<div class='message message-success addzero' style='margin-top:5px;text-align: center;display:none;'><span>Please Select The Choices Above</span></div>";
        ?>
        <div id="PreMandatory" style="display:none;">
            <div class='message message-success' style='margin-top:5px;text-align: center;'>
                Please select preferences.
            </div>
        </div>
    <?php echo $this->Form->end(); ?>

    <?php } else { ?>
        <legend>Create Your Order</legend>
        <div>
            <div class="item-pic">
                This Item is not available right now.
            </div>
        </div>
<?php } ?>

