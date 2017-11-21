<style>
    address{
        padding: 0;
    }
    .addAddress{
        font-size: 18px;
        cursor: pointer;
    }
</style>
<?php if (!empty($checkaddress)) { ?>
    <ul id="delivery_address">
        <li>
            <label>Name</label>
            <?php echo ucfirst($checkaddress[0]['DeliveryAddress']['name_on_bell']); ?>
        </li>
        <li>
            <label>Address</label>
            <?php echo ucfirst($checkaddress[0]['DeliveryAddress']['address']); ?>
        </li>
        <li>
            <label>City </label>
            <?php echo ucfirst($checkaddress[0]['DeliveryAddress']['city']); ?>pa
        </li>
        <li>
            <label>State</label>
            <?php echo ucfirst($checkaddress[0]['DeliveryAddress']['state']); ?>
        </li>
        <li>
            <label>Zip Code</label>
            <div class="title-box"><?php echo $checkaddress[0]['DeliveryAddress']['zipcode']; ?></div>
        </li>
        <li>
            <label>Ph no.</label>
            <?php echo $checkaddress[0]['CountryCode']['code'] . '' . $checkaddress[0]['DeliveryAddress']['phone']; ?>
        </li>
        <li>
            <span class="editAddress" data-id="<?php echo $this->Encryption->encode($checkaddress[0]['DeliveryAddress']['id']); ?>"><i class="fa fa-pencil"></i>Edit</span>
            <span class="deleteAddress" data-id="<?php echo $this->Encryption->encode($checkaddress[0]['DeliveryAddress']['id']); ?>"><i class="fa fa-trash-o"></i>Delete</span>
        </li>

    </ul>
<?php } else { ?>
    <address>
        <h4>Please add your delivery address.</h4>
        <a class="addAddress"><i class="fa fa-plus-circle"></i>Add More Addresses</a>
        <?php //echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-plus-circle')) . 'Add Address', array('controller' => 'users', 'action' => 'addAddress', $encrypted_storeId, $encrypted_merchantId), array('class' => '', 'escape' => false)); ?>
    </address>
<?php } ?>
<?php
if (!empty($checkaddress)) {
    if (DESIGN == 2) {
        $clr = "";
    } else {
        $clr = "clearfix";
    }
    ?>
    <div class="address-radio-wrap <?php echo $clr; ?>">
        <?php
        $i = 0;
        $delivery_address_id = $this->Session->read('ordersummary.delivery_address_id');
        foreach ($checkaddress as $address) {
            if ($address['DeliveryAddress']['default'] == 1) {
                $checked = "checked = 'checked'";
            } elseif ($i == 0) {
                $checked = "checked = 'checked'";
            }elseif ($address['DeliveryAddress']['id'] == $delivery_address_id) {
                $checked = "checked = 'checked'";
            } else {
                $checked = "";
            }
            ?>
            <?php if ($address['DeliveryAddress']['label'] == 1) { ?>
                <input type="radio" id="home" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="home"><span class="chk-span"></span>Home Address</label>
            <?php } else if ($address['DeliveryAddress']['label'] == 2) { ?>
                <input type="radio" id="work" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="work"><span class="chk-span"></span>Work Address</label>
            <?php } else if ($address['DeliveryAddress']['label'] == 3) { ?>
                <input type="radio" id="other" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="other"><span class="chk-span"></span>Other Address</label>
            <?php } else if ($address['DeliveryAddress']['label'] == 4) { ?>
                <input type="radio" id="other4" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="other4"><span class="chk-span"></span>Address4</label>
            <?php } else if ($address['DeliveryAddress']['label'] == 5) { ?>
                <input type="radio" id="other5" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="other5"><span class="chk-span"></span>Address5</label>
                <?php
            }
            $i++;
        }
        ?>
        <?php if ($i < 5) { ?>
            <div class="" style="width:100%;float:left;"> 
                <a class="addAddress"><i class="fa fa-plus-circle"></i>Add More Addresses</a>
                <?php //echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-plus-circle')) . 'Add More Addresses', array('controller' => 'users', 'action' => 'addAddress', $encrypted_storeId, $encrypted_merchantId), array('class' => '', 'escape' => false));   ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>
<script>
    $(document).on('click', '.closeModal', function () {
        $('#address-modal').modal('hide');
    });
    getDefaultAddress();
    function getDefaultAddress() {
        var deliveryId = $("input[type='radio'][class='deladdress']:checked").val();
        if (deliveryId === undefined || deliveryId === null) {
            deliveryId = $("#home").val();
            $("#home").prop("checked", true);
        }
        $.ajax({
            type: 'post',
            url: "<?php echo $this->Html->url(array('controller' => 'orderOverviews', 'action' => 'getDeliveryAddress')); ?>",
            async: false,
            data: {'deliveryId': deliveryId},
            success: function (result) {
                if (result) {
                    $('#delivery_address').html(result);
                }
            }
        });
    }

    $(document).ready(function () {
        $(document).on('change', "input[name='data[DeliveryAddress][id]']:radio", function (e) {
            e.stopImmediatePropagation();
            getDefaultAddress();
            var deliveryAddressId = $("input[type='radio'][class='deladdress']:checked").val();
            if (deliveryAddressId) {
                
                $.ajax({
                    type: 'post',
                    url: "<?php echo $this->Html->url(array('controller' => 'orderOverviews', 'action' => 'checkAddressInZone')); ?>",
                    async: false,
                    data: {'deliveryAddressId': deliveryAddressId},
                    success: function (response) {
                        if (response) {
                            result = $.parseJSON(response);
                            if (result.status == 'Error') {
                                $("#errorPop").modal('show');
                                $("#errorPopMsg").html(result.msg);
                                checkDeliverType();
                                return false;
                            }
                        }
                        checkDeliverType();
                    }
                });
                
                
                
                
                
//                $.post("/orderOverviews/checkAddressInZone", {'deliveryAddressId': deliveryAddressId}, function (response) {
//                    if (response) {
//                        result = $.parseJSON(response);
//                        if (result.status == 'Error') {
//                            $("#errorPop").modal('show');
//                            $("#errorPopMsg").html(result.msg);
//                            checkDeliverType();
//                            return false;
//                        }
//                    }
//                    checkDeliverType();
//                }, async: false);
            }
        });
        $(document).on('click', '.editAddress', function () {
            var addressId = $(this).data('id');
            $.ajax({
                type: 'post',
                url: "<?php echo $this->Html->url(array('controller' => 'orderOverviews', 'action' => 'getAddressDetail')); ?>",
                data: {'addressId': addressId},
                success: function (result) {
                    if (result) {
                        $('#address-modal').html(result).modal('show');
                    }
                }
            });
        });
        $(document).on('click', '.deleteAddress', function (e) {
            e.stopImmediatePropagation();
            if (confirm("Are you sure you want to delete this delivery address?")) {
                var addressId = $(this).data('id');
                $.ajax({
                    type: 'post',
                    url: "<?php echo $this->Html->url(array('controller' => 'orderOverviews', 'action' => 'deleteDeliveryAddress')); ?>",
                    data: {'addressId': addressId},
                    beforeSend: function () {
                        $.blockUI({css: {
                                border: 'none',
                                padding: '15px',
                                backgroundColor: '#000',
                                '-webkit-border-radius': '10px',
                                '-moz-border-radius': '10px',
                                opacity: .5,
                                color: '#fff'
                            }});
                    },
                    complete: function () {
                        $.unblockUI();
                    },
                    success: function (successResult) {
                        if (successResult) {
                            $('#deliveryAddress').html(successResult);
                            $('#address-modal').modal('hide');
                        } else {
                            $('#address-modal').modal('hide');
                        }
                    }
                });
            } else {
                return false;
            }

        });
        $(document).on('click', '.addAddress', function (e) {
            e.stopImmediatePropagation();
            $.ajax({
                type: 'post',
                url: "<?php echo $this->Html->url(array('controller' => 'orderOverviews', 'action' => 'addAddress')); ?>",
                success: function (result) {
                    if (result) {
                        $('#address-modal').html(result).modal('show');
                    }
                }
            });
        });
    });
</script>