<?php if (DESIGN == 3) { ?>
    <div class="title-bar">My Delivery Address</div>
<?php } ?>
<div class="main-container delivery-page">
    <div class="ext-menu-title">
        <h4>Delivery Address</h4>
    </div>
    <?php //echo $this->Session->flash(); ?>
    <div class="inner-wrap no-border">
        <div class="user-custom-wrap">
            <form class="form-horizontal">
                <?php if (!empty($checkaddress)) { ?>
                    <div id="delivery_address">
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-xs-3">Name:</label>
                            <div class="col-sm-10 col-xs-9">
                                <p><?php echo ucfirst($checkaddress[0]['DeliveryAddress']['name_on_bell']); ?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-xs-3">Address:</label>
                            <div class="col-sm-10 col-xs-9"> 
                                <p><?php echo ucfirst($checkaddress[0]['DeliveryAddress']['address']); ?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-xs-3">City:</label>
                            <div class="col-sm-10 col-xs-9"> 
                                <p><?php echo ucfirst($checkaddress[0]['DeliveryAddress']['city']); ?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-xs-3">State:</label>
                            <div class="col-sm-10 col-xs-9"> 
                                <p><?php echo ucfirst($checkaddress[0]['DeliveryAddress']['state']); ?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-xs-3">Zip Code:</label>
                            <div class="col-sm-10 col-xs-9"> 
                                <p><?php echo $checkaddress[0]['DeliveryAddress']['zipcode']; ?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2 col-xs-3">Ph no:</label>
                            <div class="col-sm-10 col-xs-9"> 
                                <p><?php echo $checkaddress[0]['CountryCode']['code'] . '' . $checkaddress[0]['DeliveryAddress']['phone']; ?></p>
                            </div>
                        </div>
                        <div class="form-group form-inner-action-btn">
                            <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-pencil')) . 'Edit', array('controller' => 'users', 'action' => 'updateAddress', $encrypted_storeId, $encrypted_merchantId, $this->Encryption->encode($checkaddress[0]['DeliveryAddress']['id'])), array('class' => 'apply-order theme-bg-1', 'escape' => false)); ?>
                            <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-trash-o')) . 'Delete', array('controller' => 'users', 'action' => 'deleteDeliveryAddress', $encrypted_storeId, $encrypted_merchantId, $this->Encryption->encode($checkaddress[0]['DeliveryAddress']['id'])), array('confirm' => __('Are you sure you want to delete this delivery address?'), 'class' => 'apply-order theme-bg-2', 'escape' => false)); ?>
                        </div>
                    </div>
                    <div class="form-group radio-wrap">
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
                                <div class="radio-bx">
                                    <input type="radio" id="home" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="home"><span></span>Home Address</label>
                                </div>
                            <?php } else if ($address['DeliveryAddress']['label'] == 2) { ?>
                                <div class="radio-bx">
                                    <input type="radio" id="work" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="work"><span></span>Work Address</label>
                                </div>
                            <?php } else if ($address['DeliveryAddress']['label'] == 3) { ?>
                                <div class="radio-bx">
                                    <input type="radio" id="other" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="other"><span></span>Other Address</label>
                                </div>
                            <?php } else if ($address['DeliveryAddress']['label'] == 4) { ?>
                                <div class="radio-bx">
                                    <input type="radio" id="other4" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="other4"><span></span>Address4</label>
                                </div>
                            <?php } else if ($address['DeliveryAddress']['label'] == 5) { ?>
                                <div class="radio-bx">
                                    <input type="radio" id="other5" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="other5"><span></span>Address5</label>
                                </div>
                                <?php
                            }
                            $i++;
                        }
                        ?>
                        <?php if ($i < 5) { ?>
                            <div class="more-address"> 
                                <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-plus-circle')) . 'Add More Addresses', array('controller' => 'users', 'action' => 'addAddress', $encrypted_storeId, $encrypted_merchantId), array('class' => 'btn-add-more-address', 'escape' => false)); ?> 
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <address>
                        <h3>Please add your delivery address.</h3>
                        <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-plus-circle')) . 'Add Address', array('controller' => 'users', 'action' => 'addAddress', $encrypted_storeId, $encrypted_merchantId), array('class' => '', 'escape' => false)); ?> 
                    </address>
                <?php } ?>
            </form>
        </div>
    </div>
</div>
<script>
    function getDefaultAddress() {
        var deliveryId = $("input[type='radio'][class='deladdress']:checked").val();
        var storeId = '<?php echo $encrypted_storeId; ?>';
        var merchantId = '<?php echo $encrypted_merchantId; ?>';
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

    getDefaultAddress();

    $(document).ready(function () {
        $('.date-select').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: 1,
        });

        $("#Deliveryaddress").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Store][pickup_time]": {
                    required: true,
                },
                "data[Store][pickup_date]": {
                    required: true,
                },
            },
            messages: {
                "data[Store][pickup_time]": {
                    required: "Please select pickup time"
                },
                "data[Store][pickup_date]": {
                    required: "Please enter your pickup date",
                }
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            }
        });
        $('#pickupdata').css('display', 'none');
        $('#StorePickupTime').css('display', 'none');
        $('#StorePickupDate').css('display', 'none');
        $("#pre-order").on('click', function () { // To Show
            $('#pickupdata').css('display', 'block');
            $('#StorePickupTime').css('display', 'block');
            $('#StorePickupDate').css('display', 'block');
        });
        $("#now").on('click', function () {// To hide
            $('#pickupdata').css('display', 'none');
            $('#StorePickupTime').css('display', 'none');
            $('#StorePickupDate').css('display', 'none');
        });
    });

    $("input[name='data[DeliveryAddress][id]']:radio").change(function () {
        var deliveryId = $(this).val();
        var storeId = '<?php echo $encrypted_storeId; ?>';
        var merchantId = '<?php echo $encrypted_merchantId; ?>';
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
    });

    $('#StorePickupDate').on('change', function () {
        var date = $(this).val();
        var type1 = 'Store';
        var type2 = 'pickup_time';
        var type3 = 'StorePickupTime';
        var storeId = '<?php echo $encrypted_storeId; ?>';
        var merchantId = '<?php echo $encrypted_merchantId; ?>';
        $.ajax({
            url: "<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'getStoreTime')); ?>",
            type: "Post",
            dataType: 'html',
            data: {storeId: storeId, merchantId: merchantId, date: date, type1: type1, type2: type2, type3: type3},
            success: function (result) {
                $('#resvTime').html(result);
            }
        });
    });

</script>
