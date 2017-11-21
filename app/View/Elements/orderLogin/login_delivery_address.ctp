<?php
$checkaddress = $this->Common->logindeliveryaddress();

$decrypt_storeId = $this->Session->read('store_id');
$decrypt_merchantId = $this->Session->read('merchant_id');
$encrypted_storeId = $this->Encryption->encode($decrypt_storeId);
$encrypted_merchantId = $this->Encryption->encode($decrypt_merchantId);
?>
<div class="content single-frame">
    <div class="wrap">
        <div id="flashMsg"></div>
        <?php echo $this->Session->flash(); ?>

        <?php if (!empty($checkaddress) && ($this->Session->check('Order.order_type'))) { ?> 
            <?php echo $this->Form->create('DeliveryAddress', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'Deliveryaddress', 'url' => array('controller' => 'ajaxMenus', 'action' => 'deliveryaddress')));
            ?>


            <div class="clearfix">
                <section class="form-layout sign-up">
                    <h2>My Delivery Address</h2>    	
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
                                    <?php
                                    echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-pencil', 'rel' => $this->Encryption->encode($checkaddress[0]['DeliveryAddress']['id']))) . 'Edit', '#', array('class' => 'button-link', 'escape' => false));
                                    ?> &nbsp;&nbsp;&nbsp;
                                    <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-trash-o')) . 'Delete', array('controller' => 'users', 'action' => 'deleteDeliveryAddress', $encrypted_storeId, $encrypted_merchantId, $this->Encryption->encode($checkaddress[0]['DeliveryAddress']['id'])), array('confirm' => __('Are you sure you want to delete this delivery address?'), 'class' => 'delete', 'escape' => false)); ?>
                                    <span style="float:right;">
                                        <b><?php echo $this->Html->link('Change Order Type', 'javascript:void(0)', array('class' => 'button-link', 'escape' => false, 'id' => 'changeorderType')); ?>
                                        </b>
                                    </span>        
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
                                    <?php
                                }
                                $i++;
                            }
                            ?>

                            <?php if ($i < 5) { ?>

                                <div class="edit-link add-more-line"> 
                                    <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-plus-circle')) . 'Add More Addresses', 'javascript:void(0)', array('class' => 'button-link addmoreaddress', 'escape' => false)); ?> 
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
                                <?php
                            }
                            $i++;
                        }
                        ?>

                        <?php if ($i < 5) { ?>

                            <div class="edit-link add-more-line"> 
                                <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-plus-circle')) . 'Add More Addresses', 'javascript:void(0)', array('class' => 'button-link addmoreaddress', 'escape' => false)); ?> 
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


                    <div class="button-frame"><button type="button" id="deliveryButton" class="btn btn-primary theme-bg-1">Deliver here</button></div>
                <?php } else { ?>
                    <div class="clearfix">
                        <section class="sign-up">
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

    <script>

        $('#changeorderType').click(function () {
            changeTabPan('chkOrderType', 'chkDeliveryAddress');
            setDefaultStoreTime(3);
        });
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
                url: '/ajaxMenus/getDeliveryAddress',
                data: {'deliveryId': deliveryId, 'storeId': storeId, 'merchantId': merchantId},
                success: function (result) {
                    if (result) {
                        $('#delivery_address').html(result);
                    }
                }
            });
        }
        getDefaultAddress();
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
        $('#deliveryButton').click(function () {
            if ($("#chkOrderType").hasClass("active")) {
                $.ajax({
                    type: 'POST',
                    url: '/ajaxMenus/delivery',
                    data: $('#OrderTypeForm').serialize(),
                    async: false,
                    success: function (response) {
                    }
                });
            }
            $.ajax({
                type: 'POST',
                url: '/ajaxMenus/deliveryaddress',
                data: $('#Deliveryaddress').serialize(),
                async: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (res) {
                    data = JSON.parse(res);
                    if (data.status == 'Error') {
                        $("#errorPop").modal('show');
                        $("#errorPopMsg").html(data.msg);
                        /*$("#flashMsg").append('<div class="alert alert-danger" id="flashMessage"><a href="#" data-dismiss="alert" aria-label="close" title="close" class="pull-right">×</a> ' + data.msg + '</div>');
                         setTimeout(function () {
                         $("#flashMessage").remove()
                         }, 4000);*/
                        return false;
                    } else {
                        window.location = window.location;
                    }
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
        $('#editLinkID').click(function () {
            var addressId = $('#editLinkID').attr('rel');
            $.ajax({
                type: 'POST',
                url: '/ajaxMenus/updateAddress',
                data: {address: addressId},
                success: function (response) {
                    $("#delivery_address").html(response);
                }
            });
        });
        $('.addmoreaddress').click(function () {
            $.ajax({
                type: 'POST',
                url: '/ajaxMenus/addAddress',
                data: {},
                success: function (response) {
                    $("#chkDeliveryAddress").html(response);
                }
            });
        });




    </script>