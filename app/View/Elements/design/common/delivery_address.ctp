
<div class="content single-frame">
    <div class="wrap">
        <?php //echo $this->Session->flash(); ?>

        <?php if (!empty($checkaddress) && ($this->Session->check('Order.order_type'))) { ?> 
            <?php echo $this->Form->create('DeliveryAddress', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'Deliveryaddress', 'url' => array('controller' => 'users', 'action' => 'ordercatCheck', $orderId)));
            ?>
            <div class="clearfix">
                <section class="form-layout sign-up">
                    <h2> <span>My Delivery Address</span></h2>    	
                    <?php if ($checkaddress) { ?>
                        <ul class="clearfix margin-bt-0 clear-clearfix" id="delivery_address" >
                            <li>
                                <span class="title"><label class="common-bold">Name</label></span>
                                <div class="title-box"><?php echo ucfirst($checkaddress[0]['DeliveryAddress']['name_on_bell']); ?></div>
                            </li>

                            <li>
                                <span class="title"><label class="common-bold">Address</label></span>
                                <div class="title-box"><?php echo ucfirst($checkaddress[0]['DeliveryAddress']['address']); ?></div>
                            </li>

                            <li>
                                <span class="title"><label class="common-bold">City </label></span>
                                <div class="title-box"><?php echo ucfirst($checkaddress[0]['DeliveryAddress']['city']); ?></div>
                            </li>

                            <li>
                                <span class="title"><label class="common-bold">State</label></span>
                                <div class="title-box"><?php echo ucfirst($checkaddress[0]['DeliveryAddress']['state']); ?></div>
                            </li>

                            <li>
                                <span class="title"><label class="common-bold">Zip Code</label></span>
                                <div class="title-box"><?php echo $checkaddress[0]['DeliveryAddress']['zipcode']; ?></div>
                            </li>

                            <li>
                                <span class="title"><label class="common-bold">Ph no.</label></span>
                                <div class="title-box"><?php echo $checkaddress[0]['CountryCode']['code'] . '' . $checkaddress[0]['DeliveryAddress']['phone']; ?></div>
                            </li>

                            <li>
                                <span class="title blank">&nbsp;</span>
                                <div class="title-box edit-link"> 
                                    <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-pencil')) . 'Edit', array('controller' => 'users', 'action' => 'updateAddress', $encrypted_storeId, $encrypted_merchantId, $this->Encryption->encode($checkaddress[0]['DeliveryAddress']['id'])), array('class' => 'button-link', 'escape' => false)); ?> &nbsp;&nbsp;&nbsp;
                                    <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-trash-o')) . 'Delete', array('controller' => 'users', 'action' => 'deleteDeliveryAddress', $encrypted_storeId, $encrypted_merchantId, $this->Encryption->encode($checkaddress[0]['DeliveryAddress']['id'])), array('confirm' => __('Are you sure you want to delete this delivery address?'), 'class' => 'delete', 'escape' => false)); ?>
                                </div>
                            </li>
                        </ul>
                    <?php } else { ?>
                        <div class="address">
                            <address class="inbox">
                                <h3>Please add your delivery address.</h3>
                                <div class="title-box edit-link"> 
                                    <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-plus-circle')) . 'Add Address', array('controller' => 'users', 'action' => 'addAddress', $encrypted_storeId, $encrypted_merchantId), array('class' => 'button-link', 'escape' => false)); ?> 
                                </div>
                            </address>
                        </div>
                    <?php } ?>

                    <?php if (($checkaddress) && (!$this->Session->check('Order.order_type'))) { ?> 

                        <div class="radio-btn space20 delivery-address-option">
                            <?php
                            $i = 0;
                            foreach ($checkaddress as $address) {
                                if ($address['DeliveryAddress']['default'] == 1) {
                                    $checked = "checked = 'checked'";
                                } else {
                                    $checked = "";
                                }
                                ?>

                                <?php if ($address['DeliveryAddress']['label'] == 1) { ?>
                                    <input type="radio" id="home" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="home" class="common-bold common-size"><span></span>Home Address</label>
                                <?php } else if ($address['DeliveryAddress']['label'] == 2) { ?>
                                    <input type="radio" id="work" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="work" class="common-bold common-size"><span></span>Work Address</label>
                                <?php } else if ($address['DeliveryAddress']['label'] == 3) { ?>
                                    <input type="radio" id="other" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="other" class="common-bold common-size"><span></span>Other Address</label>
                                <?php } else if ($address['DeliveryAddress']['label'] == 4) { ?>
                                    <input type="radio" id="other4" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="other4" class="common-bold common-size"><span></span>Address 4</label>
                                <?php } else if ($address['DeliveryAddress']['label'] == 5) { ?>
                                    <input type="radio" id="other5" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="other5" class="common-bold common-size"><span></span>Address 5</label>
                                <?php }
                                $i++;
                            }
                            ?>

                            <?php if ($i < 5) { ?>

                                <div class="edit-link add-more-line"> 
            			    <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-plus-circle')) . 'Add More Addresses', array('controller' => 'users', 'action' => 'addAddress', $encrypted_storeId, $encrypted_merchantId), array('class' => 'button-link', 'escape' => false)); ?> 
                                    <br/>
                                    <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-arrow-circle-right')) . 'Continue To Order', array('controller' => 'products', 'action' => 'items', $encrypted_storeId, $encrypted_merchantId), array('class' => 'button-link pull-left', 'escape' => false)); ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?php } ?> 


                    <div class="radio-btn space20 delivery-address-option">
                        <?php
                        $i = 0;
                        foreach ($checkaddress as $address) {
                            if ($address['DeliveryAddress']['default'] == 1) {
                                $checked = "checked = 'checked'";
                            } else {
                                $checked = "";
                            }
                            ?>

                            <?php if ($address['DeliveryAddress']['label'] == 1) { ?>
                                <input type="radio" id="home" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="home" class="common-bold common-size"><span></span>Home Address</label>
                            <?php } else if ($address['DeliveryAddress']['label'] == 2) { ?>
                                <input type="radio" id="work" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="work" class="common-bold common-size"><span></span>Work Address</label>
                            <?php } else if ($address['DeliveryAddress']['label'] == 3) { ?>
                                <input type="radio" id="other" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="other" class="common-bold common-size"><span></span>Other Address</label>
        <?php } else if ($address['DeliveryAddress']['label'] == 4) { ?>
                                <input type="radio" id="other4" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="other4" class="common-bold common-size"><span></span>Address 4</label>
                                <?php } else if ($address['DeliveryAddress']['label'] == 5) { ?>
                                <input type="radio" id="other5" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="other5" class="common-bold common-size"><span></span>Address 5</label>
                            <?php }
                            $i++;
                        }
                        ?>

    <?php if ($i < 5) { ?>

                            <div class="edit-link add-more-line"> 
        			<?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-plus-circle')) . 'Add More Addresses', array('controller' => 'users', 'action' => 'addAddress', $encrypted_storeId, $encrypted_merchantId), array('class' => 'button-link', 'escape' => false)); ?>
                            	<br/>
                            	<?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-arrow-circle-right')) . 'Continue To Order', array('controller' => 'products', 'action' => 'items', $encrypted_storeId, $encrypted_merchantId), array('class' => 'button-link pull-left', 'escape' => false)); ?>
                            </div>
                    <?php } ?>
                    </div>

                    <?php if (isset($deliveryDescription) && !empty($deliveryDescription)) { ?>
                        <div class="radio-btn space20 delivery-address-option" style="float:right;width:35%;">
                            <label class="common-bold common-size" for="other"><span></span><i class="fa fa-caret-down"></i> Detail</label>
                            <div class="edit-link" style="font-size:14px;"> 
                        <?php echo $deliveryDescription; ?>
                            </div>
                        </div>
                    <?php } ?>

    <?php
    $PreorderAllowed = $this->Common->checkPreorder();

    if ($setPre == 1) {

        if ($PreorderAllowed) {
            ?>
                            <div class="radio-btn space20 delivery-address-option">
                                <input type="radio" id="pre-order" name="data[DeliveryAddress][type]" checked value="1" /> <label for="pre-order" class="common-bold common-size"><span></span>Pre Order</label>
                            </div>
                                <?php
                            }
                        } else {
                            $pre_checked = '';
                            $now_checked = 'checked';
                            if(!$nowAvail && $PreorderAllowed == 1)
                            {
                                $pre_checked = 'checked';
                                $now_checked = '';
                            }
                            ?>
                        <div class="radio-btn space20 delivery-address-option">
                            <?php if ($nowAvail) { ?>
                                <input type="radio" id="now" name="data[DeliveryAddress][type]" <?php echo $now_checked;?> value="0" /> <label for="now" class="common-bold common-size"><span></span>Now</label>
        <?php } ?>
                        <?php
                        if ($PreorderAllowed) {
                            ?>

                                <input type="radio" id="pre-order" name="data[DeliveryAddress][type]" <?php echo $pre_checked;?> value="1" /> <label for="pre-order" class="common-bold common-size"><span></span>Pre Order</label>
                            </div> 
            <?php
        }
    }

    if ($setPre != 1 || $PreorderAllowed == 1) {
        ?>

                        <ul id="pickupdata" class="clearfix">                    
                            <li>
                                <div style="float:left">
                                    <span class="title"><label>Delivery Date <em>*</em></label></span>
        <?php echo $this->Form->input('Store.pickup_date', array('type' => 'text', 'class' => 'inbox date-select', 'placeholder' => 'Date', 'label' => false, 'div' => false, 'required' => true, 'readOnly' => true));
        echo $this->Form->error('Store.pickup_date'); ?>
                                </div>
                                <div id="resvTime">


                                </div>
                            </li>
                        </ul>
                        <div class="button-frame"><button type="submit" class="btn green-btn">Deliver here</button></div>

                    <?php } else { ?>
                        <ul id="pickupdata" class="clearfix">                    
                            <li></li>
                        </ul>
                        <section class="form-layout" style="width:100%;padding:10px;">
                            <span class="closeStore">Store is closed</span>
                        </section>
                                        <?php } ?>
                                    <?php } else { ?>
                    <div class="clearfix">
                        <section class="form-layout sign-up">
                            <div class="address">
                                <address class="inbox">
                                    <h3>Please add your delivery address.</h3>
                                    <div class="title-box edit-link"> 
                    <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-plus-circle')) . 'Add Address', array('controller' => 'users', 'action' => 'addAddress', $encrypted_storeId, $encrypted_merchantId), array('class' => 'button-link', 'escape' => false)); ?> 
                                    </div>
                                </address>
                            </div>
    <?php } ?>  
                    </section>
                </div>
    <?php echo $this->Form->end(); ?>
        </div>
    </div>
    <?php
    $deliveryadvanceDay = $store_data['Store']['deliverycalendar_limit'] - 1 + $store_data['Store']['deliveryblackout_limit'];
    $datetoConvert = explode('-', $currentDateVar);

    $datetoConvert = $datetoConvert[2] . '-' . $datetoConvert[0] . '-' . $datetoConvert[1];
    $deliverymaxdate = date('m-d-Y', strtotime($datetoConvert . ' +' . $deliveryadvanceDay . ' day'));
    $currentDateVar = date('m-d-Y', strtotime($datetoConvert . ' +' . $store_data['Store']['deliveryblackout_limit'] . ' day'));
    ?>
    <script>

        function getTime(date, orderType, preOrder, returnspan, ortype) {
            var type1 = 'Store';
            var type2 = 'pickup_time';
            var type3 = ortype;
            var storeId = '<?php echo $encrypted_storeId; ?>';
            var merchantId = '<?php echo $encrypted_merchantId; ?>';
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'getStoreTime')); ?>",
                type: "Post",
                dataType: 'html',
                data: {storeId: storeId, merchantId: merchantId, date: date, type1: type1, type2: type2, type3: type3, orderType: orderType, preOrder: preOrder},
                success: function (result) {
                    $('#' + returnspan).html(result);
                }
            });
        }


        getDefaultAddress();
        function getDefaultAddress() {
            var deliveryId = $("input[type='radio'][class='deladdress']:checked").val();
            var storeId = '<?php echo $encrypted_storeId; ?>';
            var merchantId = '<?php echo $encrypted_merchantId; ?>';
            if (deliveryId === undefined || deliveryId === null) {
                deliveryId = $("#home").val();
                $("#home").prop("checked", true);
            }
            $.ajax({
                type: 'post',
                url: '/Users/getDeliveryAddress',
                data: {'deliveryId': deliveryId, 'storeId': storeId, 'merchantId': merchantId},
                success: function (result) {
                    if (result) {
                        $('#delivery_address').html(result);
                    }
                }
            });
        }

        $(document).ready(function () {
            var setFlag = "<?php echo $setPre; ?>";
            var preOrderFlag    = "<?php echo $PreorderAllowed; ?>";
            var setNow          = "<?php echo $nowAvail;?>";
            if (setFlag == 0) {
                if(!setNow && preOrderFlag)
                {
                    $('#pickupdata').css('display', 'table');
                    $('#StorePickupTime').css('display', 'block');
                    $('#StorePickupDate').css('display', 'block');
                } else {
                    $('#pickupdata').css('display', 'none');
                    $('#StorePickupTime').css('display', 'none');
                    $('#StorePickupDate').css('display', 'none');
                }
                $('#pickupdata').css('display', 'none');
                $('#StorePickupTime').css('display', 'none');
                $('#StorePickupDate').css('display', 'none');
            } else {
                $('#pickupdata').css('display', 'table');
                $('#StorePickupTime').css('display', 'block');
                $('#StorePickupDate').css('display', 'block');
            }

        });

        $('.date-select').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: '<?php echo $currentDateVar; ?>',
            maxDate: '<?php echo $deliverymaxdate; ?>',
            beforeShowDay: function (date) {
                var day = date.getDay();
                var array ='<?php echo json_encode($closedDay); ?>';
                return [array.indexOf(day)==-1];
            }
        });



        $(".date-select").datepicker("setDate", '<?php echo $currentDateVar; ?>');
        //$('.date-select').datepicker('update'); 
        var date = '<?php echo $currentDateVar; ?>';
        getTime(date, 3, 1, 'resvTime');


        $("#pre-order").on('click', function () { // To Show
            $('#pickupdata').css('display', 'table');
            $('#StorePickupTime').css('display', 'block');
            $('#StorePickupDate').css('display', 'block');
        });
        $("#now").on('click', function () {// To hide
            $('#pickupdata').css('display', 'none');
            $('#StorePickupTime').css('display', 'none');
            $('#StorePickupDate').css('display', 'none');
        });

        $("input[name='data[DeliveryAddress][id]']:radio").change(function () {
            getDefaultAddress();
        });

        $("#Deliveryaddress").validate({
            rules: {
                "data[Store][pickup_hour]": {
                    required: true,
                },
                "data[Store][pickup_date]": {
                    required: true,
                },
            },
            messages: {
                "data[Store][pickup_hour]": {
                    required: "Please select pickup time"
                },
                "data[Store][pickup_date]": {
                    required: "Please enter your pickup date",
                }
            }
        });

        $('#StorePickupDate').on('change', function () {
            var date = $(this).val();
            var orderType = 3; // 3= delivery
            var preOrder = $("input[name='data[DeliveryAddress][type]']:checked").val();
            var type1 = 'Store';
            var type2 = 'pickup_time';
            var type3 = 'StorePickupTime';
            var storeId = '<?php echo $encrypted_storeId; ?>';
            var merchantId = '<?php echo $encrypted_merchantId; ?>';
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'getStoreTime')); ?>",
                type: "Post",
                dataType: 'html',
                data: {storeId: storeId, merchantId: merchantId, date: date, type1: type1, type2: type2, type3: type3, orderType: orderType, preOrder: preOrder},
                success: function (result) {
                    $('#resvTime').html(result); 
                }
            });
        });

    </script>
