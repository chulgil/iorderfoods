<?php $url = HTTP_ROOT; ?>
<?php $imageurl = HTTP_ROOT . 'MenuItem-Image/'; ?>
<?php
$popupstatus = $this->Common->popupallowed();
if (!empty($popupstatus)) {
    ?>
    <input type="hidden" id="popupAlert" value="<?php echo (!isset($popupstatus)) ? $popupstatus : 0; ?>" >
    <?php
}
if (isset($productInfo) && !empty($productInfo)) {
    $PreorderAllowed = $this->Common->checkPreorder();
    ?>

    <h2><span>Create Your Order</span></h2>
    <input type="hidden" id="chkloginstatus" name="chkloginstatus" value="" >
    <input type="hidden" id="chknowavial" name="chknowavial" value="<?php echo (!isset($avalibilty_status)) ? 1 : 0; ?>" >
    <input type="hidden" id="chkpreorder" name="chkpreorder" value="<?php echo ($PreorderAllowed) ? 1 : 0; ?>" >
    <?php echo $this->Form->create('Item', array('url' => array('controller' => 'Products', 'action' => 'addtoCart'), 'onsubmit' => 'submitItemForm(this);return false;'));
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
            echo $this->Form->input('hidden_itemID', array('type' => 'hidden', 'value' => $productInfo['Item']['id']));
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
        <?php
        if (!empty($display_offer)) {
            ?>
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
        <?php }
        ?>
        <?php if (!empty($itemOffer)) { ?>
            <div class="row-divide clearfix">
                <h3>Item Offers</h3>
                <ul class="checkbox-listing clearfix">
                    <?php foreach ($itemOffer as $iOff) { ?>
                        <li><i class="fa fa-pencil-square-o"></i>
                            <?php echo $iOff; ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <?php
        }
        $preferencearray = array();
        $minmax = array();
        $minmaxTopping = '';
        if ($productInfo['Item']['sizeOnly'] == 2) { // Type
            if ($productInfo['ItemType']) {
                $options = array();
                foreach ($productInfo['ItemType'] as $type) {
                    if ($type['Type']) {
                        if ($type['Type']['max_value'] > 0) {
                            if ($type['Type']['max_value'] > 0) {
                                if ($type['Type']['min_value'] == 0 && $type['Type']['max_value'] > 0) {
                                    if ($type['Type']['max_value'] == 1) {
                                        $minmax[$type['Type']['name']][] = 'Choose up to 1 item.';
                                    } else {
                                        $minmax[$type['Type']['name']][] = 'Choose up to ' . $type['Type']['max_value'] . ' items.';
                                    }
                                } elseif ($type['Type']['min_value'] == $type['Type']['max_value']) {
                                    if ($type['Type']['min_value'] == 1) {
                                        $minmax[$type['Type']['name']][] = 'Choose 1 item.';
                                    } else {
                                        $minmax[$type['Type']['name']][] = 'Choose ' . $type['Type']['max_value'] . ' items.';
                                    }
                                } else {
                                    if ($type['Type']['min_value'] == 1) {
                                        $minmax[$type['Type']['name']][] = 'Choose at least 1 item.';
                                    } else {
                                        $minmax[$type['Type']['name']][] = 'Choose at least ' . $type['Type']['min_value'] . ' items.';
                                    }
                                }
                            }
                            $minmax[$type['Type']['name']][] = $type['Type']['max_value'];
                        }
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
                                $preferencearray[$type['Type']['name']][$key]['type_id'] = $preferenceData['type_id'];
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
                                <h4><?php echo $preference; ?><?php if (!empty($minmax[$preference][0])) { ?>
                                        <em>*</em><i>(<?php echo $minmax[$preference][0]; ?>)</i>
                                    <?php } ?></h4>
                                <ul class="checkbox-listing clearfix">
                                    <li>
                                        <?php
                                        $k = 0;
                                        foreach ($subpreferencedata as $vkey => $predata) {
                                            if ($predata['price'] > 0) {
                                                $price = '$' . $predata['price'];
                                            } else {
                                                $price = '';
                                            }
                                            ?>

                                            <div class="inner-full-width-listing">
                                                <div class="left-col">
                                                    <?php
                                                    echo $this->Form->input('Item.subpreference.' . $predata['id'] . '.id', array('type' => 'checkbox', 'label' => $predata['subpreferncename'], 'class' => 'item_type subpreference' . $predata["id"] . ' typeId_' . $predata["type_id"], 'value' => $predata['id'], 'div' => false));
                                                    ?>
                                                    <?php
                                                    echo $this->Form->input('Item.subpreference.' . $predata['id'] . '.type_id', array('type' => 'hidden', 'value' => $predata['type_id'], 'class' => 'subpre' . $predata['id']));
                                                    echo $this->Form->input('Item.subpreference.' . $predata['id'] . '.name', array('type' => 'hidden', 'value' => $predata['subpreferncename'], 'div' => false));
                                                    echo $this->Form->input('Item.subpreference.' . $predata['id'] . '.price', array('type' => 'hidden', 'value' => $predata['price'], 'div' => false));
                                                    ?>
                                                    <span class='theme-txt-col-1 subPrice subPrice<?php echo $predata["id"]; ?>'  style='display:none;'><?php echo $price; ?></span>
                                                </div>
                                                <div class="input number">
                                                    <?php
                                                    $maxVal = 50;
                                                    if (!empty($minmax[$preference][1])) {
                                                        $maxVal = ($minmax[$preference][1] > 0) ? $minmax[$preference][1] : 50;
                                                    }
                                                    echo $this->Form->input('Item.subpreference.' . $predata['id'] . '.size', array('class' => 'subPreferenceSize inbox subPreferenceSize' . $predata["id"], 'label' => false, 'type' => 'number', 'min' => '1', 'max' => $maxVal, 'value' => 1, 'rel' => $predata["id"], 'style' => 'display:none;'));
                                                    ?>
                                                </div>
                                            </div>

                                            <?php
                                            $k++;
                                        }
                                        ?>
                                    </li>
                                </ul>
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
                foreach ($productInfo['ItemPrice'] as $key => $size) {
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
                    foreach ($productInfo['ItemPrice'] as $key => $size) {
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
                        $options = $minmax = array();

                        foreach ($productInfo['ItemType'] as $type) {
                            if (!empty($type['Type'])) {
                                if ($type['Type']['max_value'] > 0) {
                                    if ($type['Type']['max_value'] > 0) {
                                        if ($type['Type']['min_value'] == 0 && $type['Type']['max_value'] > 0) {
                                            if ($type['Type']['max_value'] == 1) {
                                                $minmax[$type['Type']['name']][] = 'Choose up to 1 item.';
                                            } else {
                                                $minmax[$type['Type']['name']][] = 'Choose up to ' . $type['Type']['max_value'] . ' items.';
                                            }
                                        } elseif ($type['Type']['min_value'] == $type['Type']['max_value']) {
                                            if ($type['Type']['min_value'] == 1) {
                                                $minmax[$type['Type']['name']][] = 'Choose 1 item.';
                                            } else {
                                                $minmax[$type['Type']['name']][] = 'Choose ' . $type['Type']['max_value'] . ' items.';
                                            }
                                        } else {
                                            if ($type['Type']['min_value'] == 1) {
                                                $minmax[$type['Type']['name']][] = 'Choose at least 1 item.';
                                            } else {
                                                $minmax[$type['Type']['name']][] = 'Choose at least ' . $type['Type']['min_value'] . ' items.';
                                            }
                                        }
                                    }
                                    $minmax[$type['Type']['name']][] = $type['Type']['max_value'];
                                }
                            }
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
                                        $preferencearray[$type['Type']['name']][$key]['type_id'] = $preferenceData['type_id'];
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
                                        <h4><?php echo $preference; ?><?php if (!empty($minmax[$preference][0])) { ?>
                                                <em>*</em><i>(<?php echo $minmax[$preference][0]; ?>)</i>
                                            <?php } ?></h4>
                                        <ul class="checkbox-listing clearfix">
                                            <li>
                                                <?php
                                                $k = 0;
                                                foreach ($subpreferencedata as $vkey => $predata) {
                                                    if ($predata['price'] > 0) {
                                                        $price = '$' . $predata['price'];
                                                    } else {
                                                        $price = '';
                                                    }
                                                    ?>

                                                    <div class="inner-full-width-listing">
                                                        <div class="left-col">
                                                            <?php
                                                            echo $this->Form->input('Item.subpreference.' . $predata['id'] . '.id', array('type' => 'checkbox', 'label' => $predata['subpreferncename'], 'class' => 'item_type subpreference' . $predata["id"] . ' typeId_' . $predata["type_id"], 'value' => $predata['id'], 'div' => false));
                                                            ?>

                                                            <?php
                                                            echo $this->Form->input('Item.subpreference.' . $predata['id'] . '.type_id', array('type' => 'hidden', 'value' => $predata['type_id'], 'class' => 'subpre' . $predata['id']));
                                                            echo $this->Form->input('Item.subpreference.' . $predata['id'] . '.name', array('type' => 'hidden', 'value' => $predata['subpreferncename'], 'div' => false));
                                                            echo $this->Form->input('Item.subpreference.' . $predata['id'] . '.price', array('type' => 'hidden', 'value' => $predata['price'], 'div' => false));
                                                            ?>
                                                            <span class='theme-txt-col-1 subPrice subPrice<?php echo $predata["id"]; ?>' style='display:none;'><?php echo $price; ?></span>
                                                        </div>
                                                        <?php
                                                        $maxVal = 50;
                                                        if (!empty($minmax[$preference][1])) {
                                                            $maxVal = ($minmax[$preference][1] > 0) ? $minmax[$preference][1] : 50;
                                                        }
                                                        echo $this->Form->input('Item.subpreference.' . $predata['id'] . '.size', array('class' => 'subPreferenceSize inbox subPreferenceSize' . $predata["id"], 'label' => false, 'type' => 'number', 'min' => '1', 'max' => $maxVal, 'value' => 1, 'rel' => $predata["id"], 'style' => 'display:none;'));
                                                        ?>
                                                    </div>
                                                    <?php
                                                    $k++;
                                                }
                                                ?>
                                            </li>
                                        </ul>
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
                                        $minmaxTopping = '';
                                        if ($top['max_value'] > 0) {
                                            if ($top['min_value'] == 0 && $top['max_value'] > 0) {
                                                if ($top['max_value'] == 1) {
                                                    $minmaxTopping = 'Choose up to 1 item.';
                                                } else {
                                                    $minmaxTopping = 'Choose up to ' . $top['max_value'] . ' items.';
                                                }
                                            } elseif ($top['min_value'] == $top['max_value']) {
                                                if ($top['min_value'] == 1) {
                                                    $minmaxTopping = 'Choose 1 item.';
                                                } else {
                                                    $minmaxTopping = 'Choose ' . $top['max_value'] . ' items.';
                                                }
                                            } else {
                                                if ($top['min_value'] == 1) {
                                                    $minmaxTopping = 'Choose at least 1 item.';
                                                } else {
                                                    $minmaxTopping = 'Choose at least ' . $top['min_value'] . ' items.';
                                                }
                                            }
                                        }
                                        ?>
                                        <h4><?php echo $top['name']; ?><?php if (!empty($minmaxTopping)) { ?>
                                                <em>*</em><i>(<?php echo $minmaxTopping; ?>)</i>
                                            <?php } ?></h4>
                                        <ul class="checkbox-listing clearfix countAddOns">
                                            <li>
                                                <?php
                                                foreach ($top['Topping'] as $topping) {
                                                    if (empty($productInfo['Item']['default_subs_price'])) {
                                                        if (isset($topping['ToppingPrice']) && !empty($topping['ToppingPrice']['price'])) {
                                                            $topping['price'] = $topping['ToppingPrice']['price'];
                                                        }
                                                    } else {
                                                        $topping['price'] = $topping['price'];
                                                    }
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
                                                    <div class="inner-full-width-listing">
                                                        <div class="left-col">
                                                            <?php
                                                            echo $this->Form->input('Item.toppings.' . $topping['id'] . '.id', array('type' => 'checkbox', 'label' => $topping['name'], 'class' => 'toppings toppings' . $topping["id"] . ' addonId_' . $topping["addon_id"], 'value' => $topping['id'], 'checked' => $checked));
                                                            echo $this->Form->input('Item.toppings.' . $topping['id'] . '.type', array('type' => 'hidden', 'value' => $top_value, 'class' => 'type' . $topping["id"]));
                                                            echo $this->Form->input('Item.toppings.' . $topping['id'] . '.addon_id', array('type' => 'hidden', 'value' => $topping['addon_id'], 'class' => 'addon' . $topping["id"]));
                                                            echo $this->Form->input('Item.toppings.' . $topping['id'] . '.name', array('type' => 'hidden', 'value' => $topping['name']));
                                                            echo $this->Form->input('Item.toppings.' . $topping['id'] . '.price', array('type' => 'hidden', 'value' => $topping['price']));
                                                            ?>
                                                            <span class='topPrice topPrice<?php echo $topping["id"]; ?>' <?php echo ($checked) ? '' : 'style=display:none;'; ?>><?php echo $top_price; ?></span> 
                                                        </div>
                                                        <?php
                                                        if ($topping['no_size'] == 0) {
                                                            $min = 1;
                                                            $max = ($top['max_value'] > 0) ? $top['max_value'] : 50;
                                                        } else {
                                                            $min = 1;
                                                            $max = 1;
                                                        }
                                                        echo $this->Form->input('Item.toppings.' . $topping['id'] . '.size', array('class' => 'toppingSize inbox toppingSize' . $topping['id'], 'label' => false, 'type' => 'number', 'min' => $min, 'max' => $max, 'value' => 1, 'rel' => $topping['id'], 'style' => ($checked) ? '' : 'display:none;'));
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
                    echo $this->Form->submit('Add to cart', array('class' => 'btn green-btn pink-btn', 'id' => 'addtocart'));
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
                    <?php
                    echo $this->Session->flash();
                    if (empty($nonDeliverable)) {
                        ?>
                        This Item is not available right now.
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

    </div> 
    <!-- </div>   -->
    <script>

        $(document).ready(function () {
            addtocart();
            cartcount();
            var subdefault = $("#subdefault").val();
            checkuserlogin();
            $("#countSizes").val($('.countSizes').length);
            $("#countPreferences").val($('.countPreferences').length);
            $("#countAddOns").val($('.countAddOns').length);

            if (($("#countSizes").val() > 0) && ($("#countPreferences").val() > 0 || $("#countAddOns").val() > 0)) {
                if (subdefault == 0) {
                    fetchProductSize();
                }
            }


            var middlebox = $("#selectOrderTypes").height();
            var firstbox = $("#anchorName").height();
            var lastbox = $("#isolated").height();
            var headerHeight = $('header').outerHeight();
            var largestheight = Math.max(firstbox, middlebox, lastbox);
            var productImage = $('#ItemProductpicid').val();
            if (window.screen.width < 700) {
                if (productImage == 1) {
                    var picHeight = 130;
                } else {
                    var picHeight = 35;
                }

            } else {
                if (productImage == 1) {
                    var picHeight = 250;
                } else {
                    var picHeight = 35;
                }
            }


            var totalHeight = (headerHeight + largestheight + picHeight);
            $('.content').css('height', 30 + (totalHeight) + 'px');

            if (<?php echo $default_price; ?>) {
                $("#addtocart").show();
                $(".addzero").hide();
            } else {
                $("#addtocart").hide();
                $(".addzero").show();
            }
        });



        function submitItemForm(el)
        {
            var checkSetting = true;
            $.ajax({
                type: 'post',
                url: '/products/checkMandatoryPrefAddons',
                data: $(el).serialize(),
                async: false,
                success: function (mData) {
                    checkJson = IsJsonString(mData);
                    if (checkJson) {
                        var obj = jQuery.parseJSON(mData);
                        if (obj.status == 'Error') {
                            $("#errorPop").modal('show');
                            $("#errorPopMsg").html(obj.msg);
                            checkSetting = false;
                            return false;
                        }
                    }
                }
            });
            if (checkSetting == false) {
                return false;
            }
            var chknowavial = $("#chknowavial").val();
            var chkpreorder = $("#chkpreorder").val();
            if (chknowavial == 0 && chkpreorder == 0) {
                $('#Closeprompt').modal('show');
                return false;
            }
            var popupAlertStatus = $("#popupAlert").val();
            if (popupAlertStatus) {
                var chkloginstatus = $("#chkloginstatus").val();
                if (chkloginstatus == 'invalid')
                {
                    addcart1($(el).serialize());
                    $("#chkloginstatus").val('invalid');
                    $('#orderLogin').modal('show');
                    return false;
                }

                var chkloginstatus2 = $("#chkloginstatus").val();
                if (chkloginstatus2 == '')
                {
                    //addcart($(this).serialize());
                    var resorder = checkOrderTime();
                    if (resorder == 0) {
                        addcart1($(el).serialize());
                        $('#orderLogin').modal('show');
                        changeTabPan('chkOrderType', 'chkLogin');
                        setDefaultStoreTime(2);
                        return false;
                    }

                }

                var resordertype = checkOrderType();
                if (resordertype == 0)
                {
                    $('#orderLogin').modal('show');
                    changeTabPan('chkOrderType', 'chkLogin');
                    setDefaultStoreTime(2);
                    return false;
                }


                var resdeladd = checkdeliveryadd();
                if (resdeladd == 0)
                {
                    $('#orderLogin').modal('show');
                    changeTabPan('chkDeliveryAddress', 'chkOrderType');
                    changeTabPan('chkDeliveryAddress', 'chkLogin');
                    getdeliveryAddress();
                    return false;
                }
            }

            $('#loading').show();
            $('input[type="submit"]').attr('disabled', 'disabled');
            var Data = $(el).serialize();
            $.ajax({
                type: 'post',
                url: '/Products/checkCombination',
                data: Data,
                async: false,
                success: function (result) {
                    $('input[type="submit"]').removeAttr('disabled');
                    if (result == 0) {
                        $.ajax({
                            type: 'post',
                            url: '/Products/cart',
                            data: Data,
                            success: function (data1) {
                                setTimeout(function () {
                                    $('#loading').hide();
                                }, 500);
                                checkJson = IsJsonString(data1);
                                if (checkJson) {
                                    var obj = jQuery.parseJSON(data1);
                                    if (obj.status == 'Error') {
                                        $('#' + obj.type).html('<div class="alert alert-danger" id="flashMessage"><a href="#" data-dismiss="alert" aria-label="close" title="close" class="pull-right">×</a> ' + obj.msg + '</div>');
                                        $("#selectOrderTypes").animate({
                                            scrollTop: $('#' + obj.type).offset().top
                                        }, 500);
                                        return false;
                                    }
                                } else {
                                    if (data1) {
                                        $('.online-order').html(data1);
                                        var offerHiddenid = $("#offerHiddenid").val();
                                        if (offerHiddenid == 1) {
                                            if (window.screen.width < 700) {
                                                $(window).scrollTop($('div#offerItemsanchor').offset().top - 30);
                                            } else {
                                                $(window).scrollTop($('#anchorName').offset().top);
                                            }
                                        } else {
                                            if (window.screen.width < 700) {
                                                $(window).scrollTop($('#cartstart').offset().top - 30);
                                            } else {
                                                $(window).scrollTop($('#anchorName').offset().top);
                                            }
                                        }
                                    }
                                }
                                cartcount();
                            }, complete: function (data1) {
                            }
                        });
                    } else {
                        var obj = jQuery.parseJSON(result);
                        var index_id = obj.index; //index of session array
                        var value = parseInt(obj.quantity) + 1;
                        $.ajax({
                            type: 'post',
                            url: '/Products/addQuantity',
                            data: {'index_id': index_id, 'value': value},
                            success: function (data1) {
                                if (data1) {
                                    $('.online-order').html(data1);
                                }
                                cartcount();
                            }, complete: function (data1) {
                                if (window.screen.width < 700) {
                                    $(window).scrollTop($('#cartstart').offset().top - 30);
                                } else {
                                    $(window).scrollTop($('#anchorName').offset().top);
                                }
                                setTimeout(function () {
                                    $('#loading').hide();
                                }, 500);
                            }
                        });
                    }
                }
            });
        }

        function IsJsonString(str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        }

    </script>
