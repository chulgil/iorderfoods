<div class="modal-dialog clearfix ps-additional-info">
    <div class="common-title">
        <h3>ADDITIONAL INFORMATION</h3>
    
    </div>
    <button data-dismiss="modal" class="close" type="button">×</button>
    <?php echo $this->Form->create('Item', array('url' => array('controller' => 'Products', 'action' => 'addtoCart'))); ?>
    <div class="add-more">
        <h3>
            <span class="product-price product-price-single">
                <?php echo "$" . number_format($default_price, 2); ?>
            </span>
        </h3>
        <?php
        if (!empty($productInfo)) {
            ?>
            <!-- Size Only -->
            <?php
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
                        <div class="item-size">
                            <label>SIZE</label>
                            <?php
                            $i = 0;
                            foreach ($sizes as $size_value => $size_name) {
                                ?>

                                <?php
                                if ($i == 0) {
                                    $checked = "checked = 'checked'";
                                } else {
                                    $checked = "";
                                }
                                ?>
                                <span>
                                    <input type="radio" class='item_price countSizes' id='<?php echo $size_value . '_' . $size_name; ?>' <?php echo $checked; ?> name="data[Item][price]"  value='<?php echo $size_value; ?>'>
                                    <label for='<?php echo $size_value . '_' . $size_name; ?>'><span></span><?php echo $size_name; ?></label>
                                </span>

                                <?php
                                $i++;
                            }
                            ?>

                        </div>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
            <?php
            if ($productInfo['Item']['sizeOnly'] == 2) { // Type
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
                        <div class="check-more prefernceAndAddOns">
                            <h3>PREFERENCE</h3>
                            <div class="add-check-list">
                                <?php
                                $j = 0;
                                foreach ($preferencearray as $preference => $subpreferencedata) {
                                    ?>

                                    <div class="sub-menu-listing">
                                        <h4><?php echo $preference; ?></h4>
                                        <div class="check-more countPreferences">
                                            <ul class="add-check-list">
                                                <?php
                                                $k = 0;
                                                foreach ($subpreferencedata as $vkey => $predata) {
                                                    if ($predata['price'] > 0) {
                                                        $price = '($' . $predata['price'] . ')';
                                                    } else {
                                                        $price = '';
                                                    }
                                                    ?>
                                                    <li>
                                                        <input type="radio" class='item_type' id='<?php echo $predata['id'] . '_' . $predata['subpreferncename']; ?>' name="data[Item][subpreference][<?php echo $preference; ?>]" value='<?php echo $predata['id']; ?>' >
                                                        <label for='<?php echo $predata['id'] . '_' . $predata['subpreferncename']; ?>'><span><?php echo $predata['subpreferncename'] . " " . $price; ?></span></label>
                                                    </li>

                                                    <?php
                                                    $k++;
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                    <?php } ?>
                    <?php
                }
            }
            ?>
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
                        <div class="item-size">
                            <label>SIZE</label>
                            <?php
                            $l = 0;
                            foreach ($sizes as $size_value => $size_name) {
                                ?>

                                <?php
                                if ($l == 0) {
                                    $checked = "checked = 'checked'";
                                } else {
                                    $checked = '';
                                }
                                ?>
                                <span>
                                    <input type="radio" class='item_price countSizes' id='<?php echo $size_value . '_' . $size_name; ?>' name="data[Item][price]" <?php echo $checked; ?> value='<?php echo $size_value; ?>'>
                                    <label for='<?php echo $size_value . '_' . $size_name; ?>'><span></span><?php echo $size_name; ?></label>
                                </span>

                                <?php
                                $l++;
                            } 
                            ?>
                        </div>

                        <?php
                    }
                }
                ?>
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
                    <div class="prefernceAndAddOns">
                        <?php if ($preferencearray) { ?>
                            <div class="check-more prefernceAndAddOns">
                                <h3>PREFERENCE</h3>
                                <div class="add-check-list">
                                    <?php
                                    $j = 0;
                                    foreach ($preferencearray as $preference => $subpreferencedata) {
                                        ?>

                                        <div class="sub-menu-listing">
                                            <h4><?php echo $preference; ?></h4>
                                            <div class="check-more countPreferences">
                                                <ul class="add-check-list">
                                                    <?php
                                                    $k = 0;
                                                    foreach ($subpreferencedata as $vkey => $predata) {
                                                        if ($predata['price'] > 0) {
                                                            $price = '($' . $predata['price'] . ')';
                                                        } else {
                                                            $price = '';
                                                        }
                                                        ?>
                                                        <li>
                                                            <input type="radio" class='item_type' id='<?php echo $predata['id'] . '_' . $predata['subpreferncename']; ?>' name="data[Item][subpreference][<?php echo $preference; ?>]" value='<?php echo $predata['id']; ?>' >
                                                            <label for='<?php echo $predata['id'] . '_' . $predata['subpreferncename']; ?>'><span></span><font><?php echo $predata['subpreferncename'] . " " . $price; ?></font></label>
                                                        </li>

                                                        <?php
                                                        $k++;
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                        <?php } ?>

                        <?php
                    }
                }
                ?>
                <!--Topping-->
                <?php
                if ($productInfo['Topping']) {
                    ?>
                    <div class="check-more">
                        <h3>ADD-ON</h3>
                        <div class="add-check-list">
                            <?php
                            foreach ($productInfo['Topping'] as $top) {
                                if (!empty($top['Topping'])) {
                                    ?>
                                    <div class="sub-menu-listing">
                                        <h4><?php echo $top['name']; ?></h4>
                                        <div class="check-more countAddOns">
                                            <ul class="add-check-list clearfix">
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
                                                    <li>
                                                        <div class="select-left">
                                                            <?php
                                                            echo $this->Form->input('Item.toppings.' . $topping['id'] . '.id', array('type' => 'checkbox', 'label' => $topping['name'], 'class' => 'toppings toppings' . $topping["id"], 'value' => $topping['id'], 'checked' => $checked, 'div' => false));
                                                            echo $this->Form->input('Item.toppings.' . $topping['id'] . '.type', array('type' => 'hidden', 'value' => $top_value, 'class' => 'type' . $topping["id"], 'div' => false));
                                                            echo $this->Form->input('Item.toppings.' . $topping['id'] . '.name', array('type' => 'hidden', 'value' => $topping['name'], 'div' => false));
                                                            echo $this->Form->input('Item.toppings.' . $topping['id'] . '.price', array('type' => 'hidden', 'value' => $topping['price'], 'div' => false));
                                                            ?>
                                                            <span class='topPrice topPrice<?php echo $topping["id"]; ?>'><?php echo $top_price; ?></span>
                                                        </div>
                                                        <div class="select-right">
                                                            <?php
                                                            if ($topping['no_size'] == 0) {
                                                                $toppingSizes = $toppingSizes;
                                                            } else {
                                                                $toppingSizes = array('0' => 1);
                                                            }
                                                            echo $this->Form->input('Item.toppings.' . $topping['id'] . '.size', array('class' => 'toppingSize inbox toppingSize' . $topping['id'], 'label' => false, 'type' => 'select', 'options' => $toppingSizes, 'div' => false, 'rel' => $topping['id']));
                                                            ?>
                                                        </div>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>
        <?php echo $this->Form->input('CategoryID', array('type' => 'hidden', 'value' => $productInfo['Item']['category_id'])); ?>
        <input type="hidden" name="subdefault" id="subdefault" value="<?php echo $productInfo['Item']['default_subs_price']; ?>" />
        <input type="hidden" name="countSizes" id="countSizes" value="0" />
        <input type="hidden" name="countPreferences" id="countPreferences" value="0" />
        <input type="hidden" name="countAddOns" id="countAddOns" value="0" />
        <div class="confirm">
            <?php echo $this->Form->submit('CONFIRM', array('class' => 'confirm-btn theme-bg-1', 'id' => 'addtocart')); ?>
        </div>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
<script>
    $(document).ready(function () {
        var subdefault = $("#subdefault").val();
        $("#countSizes").val($('.countSizes').length);
        $("#countPreferences").val($('.countPreferences').length);
        $("#countAddOns").val($('.countAddOns').length);
        if (($("#countSizes").val() > 0) && ($("#countPreferences").val() > 0 || $("#countAddOns").val() > 0)) {
            if (subdefault == 0) {
                fetchProductSize();
            }
        }
        if (<?php echo $default_price; ?>) {
            $("#addtocart").show();
            $(".addzero").hide();
        } else {
            $("#addtocart").hide();
            $(".addzero").show();
        }
        var item_id = $('#itemIdpopup').val();
    });

    function addtocart() {
        var chkloginstatus = $("#chkloginstatus").val();
        if (chkloginstatus == 'invalid')
        {
            $("#chkloginstatus").val('invalid');
            //$('#orderLogin').modal('show');
            return false;
        }
        var resorder = checkOrderTime();
        if (resorder == 0) {
            return false;
        }

        var login = false;
        var Data = getItemData();//jQuery.parseJSON(getItemData());
        if (Data != 0) {
            login = true;
        }
        removeAddcart();
        if (login) {
            $.ajax({
                type: 'post',
                url: '/Products/cart',
                data: Data,
                success: function (data1) {
                    if (data1) {
                        $('.online-order').html(data1);
                    }
                    cartcount();
                }
            });
        }
    }
    function itemzero(price) {
        if (price <= 0) {
            $(".addzero").show();
            $("#addtocart").hide();

        } else {
            $("#addtocart").show();
            $(".addzero").hide();
        }
    }
    function fetchProductSize() {
        var sizeId = $('.item_price:checked').val();
        var itemId = $('#itemIdpopup').val();
        var categoryId = $("#ItemCategoryID").val();

        $.ajax({
            url: "/Products/fetchProductSize",
            type: "Post",
            data: {'sizeId': sizeId, 'itemId': itemId, 'categoryId': categoryId},
            success: function (result) {
                $('.prefernceAndAddOns').html(result);
            }
        });
    }

    function returnfalse() {
        return false;
    }
    function checkuserlogin() {
        var theResponse = null;
        $.ajax({
            type: 'POST',
            url: '/ajaxMenus/checklogin',
            data: {},
            async: false,
            success: function (response) {
                var data = jQuery.parseJSON(response);
                if (data == '0' || data == 0) {
                    $("#chkloginstatus").val('invalid');
                    return false;

                }
            }
        });

    }

    /* Check Mandatory preference*/
    var resonseVal = '';
    function checkPreference(data) {

        $.ajax({
            type: 'POST',
            url: '/Products/checkPreference',
            data: data,
            async: false,
            success: function (response) {
                resonseVal = response;
            }
        });
        return resonseVal;
    }

    /* Check Order type*/
    var orderVal = '';
    function checkOrderType() {

        $.ajax({
            type: 'POST',
            url: '/Products/checkOrderType',
            async: false,
            success: function (response) {
                orderVal = response;
            }
        });
        return orderVal;
    }

    /* Get delivery address */
    var deliVal = '';
    function checkdeliveryadd() {
        $.ajax({
            type: 'POST',
            url: '/Products/checkdeliveryadd',
            async: false,
            success: function (response) {
                deliVal = response;
            }
        });
        return deliVal;
    }

    /* Get Cart Count */
    function cartcount() {
        $.ajax({
            type: 'post',
            url: '/Products/getcartCount',
            data: {},
            async: false,
            success: function (data1) {
                if (data1) {
                    $('.numberCircle').html(data1);
                }
            }
        });
    }

    /*  */
    function addcart1(data) {
        $.ajax({
            type: 'post',
            url: '/Products/addtosession',
            data: data,
            async: false,
            success: function () {

            }
        });
    }

    /*  */
    function removeAddcart() {
        $.ajax({
            type: 'post',
            url: '/Products/removefrmSession',
            data: {},
            async: false,
            success: function () {

            }
        });
    }


    /* */
    var orderTime = '';
    function checkOrderTime() {
        $.ajax({
            type: 'post',
            url: '/Products/checkOrderTime',
            data: {},
            async: false,
            success: function (response) {
                orderTime = response;
            }
        });
        return orderTime;
    }

    /* Get item session data*/
    var itemVal = '';
    function getItemData() {
        $.ajax({
            type: 'POST',
            url: '/Products/getitemdata',
            async: false,
            success: function (response) {
                itemVal = response;
            }
        });
        return itemVal;
    }
</script>
