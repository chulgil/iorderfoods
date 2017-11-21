<!-- static-banner -->
<?php echo $this->element('hquser/static_banner'); ?>
<!-- /banner -->
<div class="signup-form">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="common-title clearfix">
                    <span class="yello-dash"></span>
                    <h2><?php echo __('My Delivery Address'); ?></h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php echo $this->Session->flash(); ?>
                <div class="form-bg">
                    <!-- -->
                    <div class="sign-up">
                        <div class="delivery-address clearfix">
                            <?php if (!empty($checkaddress)) { ?>
                                <?php
                                $i = 0;
                                foreach ($checkaddress as $address) {
                                    if ($address['DeliveryAddress']['default'] == 1) {
                                        $checked = "checked = 'checked'";
                                    } else {
                                        $checked = "";
                                    }
                                    ?>
                                    <div class="custom-radio">
                                        <?php if ($address['DeliveryAddress']['label'] == 1) { ?>
                                            <input type="radio" id="home" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="home"><span></span>Home Address</label>
                                        <?php } else if ($address['DeliveryAddress']['label'] == 2) { ?>
                                            <input type="radio" id="work" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="work"><span></span>Work Address</label>
                                        <?php } else if ($address['DeliveryAddress']['label'] == 3) { ?>
                                            <input type="radio" id="other" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="other"><span></span>Other Address</label>
                                        <?php } else if ($address['DeliveryAddress']['label'] == 4) { ?>
                                            <input type="radio" id="other4" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="other4"><span></span>Address4</label>
                                        <?php } else if ($address['DeliveryAddress']['label'] == 5) { ?>
                                            <input type="radio" id="other5" name="data[DeliveryAddress][id]" <?php echo $checked; ?> value="<?php echo $address['DeliveryAddress']['id']; ?>" class="deladdress"/> <label for="other5"><span></span>Address5</label>
                                            <?php
                                        }
                                        $i++;
                                        ?>
                                    </div>
                                <?php }
                                ?>
                                <?php if ($i < 5) { ?>
                                    <div class="custom-radio"> 
                                        <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-plus-circle')) . 'Add More Addresses', array('controller' => 'hqusers', 'action' => 'addAddress'), array('class' => 'addMoreAdd', 'escape' => false)); ?> 
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <!-- CONTENT -->
                        <div class="form-content">
                            <?php if (!empty($checkaddress)) { ?>
                                <!-- CONTENT -->
                                <div id="delivery_address">
                                    <div class="static-content-bg">
                                        <ul class="list-style-none col-listing clearfix">
                                            <li>
                                                <div class="col-2">
                                                    <span class="col-left">
                                                        Name:
                                                    </span>
                                                    <span class="col-right">
                                                        <?php echo ucfirst($checkaddress[0]['DeliveryAddress']['name_on_bell']); ?>
                                                    </span>
                                                </div>
                                                <div class="col-2">
                                                    <span class="col-left">
                                                        Address:
                                                    </span>
                                                    <span class="col-right">
                                                        <?php echo ucfirst($checkaddress[0]['DeliveryAddress']['address']); ?>
                                                    </span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="col-2">
                                                    <span class="col-left">
                                                        City:</span>
                                                    <span class="col-right">
                                                        <?php echo ucfirst($checkaddress[0]['DeliveryAddress']['city']); ?>
                                                    </span>
                                                </div>
                                                <div class="col-2">
                                                    <span class="col-left">
                                                        State:
                                                    </span>
                                                    <span class="col-right">
                                                        <?php echo ucfirst($checkaddress[0]['DeliveryAddress']['state']); ?>
                                                    </span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="col-2">
                                                    <span class="col-left">
                                                        Zip Code:
                                                    </span>
                                                    <span class="col-right">
                                                        <?php echo ucfirst($checkaddress[0]['DeliveryAddress']['zipcode']); ?>
                                                    </span>
                                                </div>
                                                <div class="col-2">
                                                    <span class="col-left">
                                                        Phone Number:
                                                    </span>
                                                    <span class="col-right">
                                                        <?php echo $checkaddress[0]['CountryCode']['code'] . '' . $checkaddress[0]['DeliveryAddress']['phone']; ?>
                                                    </span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- /CONTENT END -->
                                    <!-- ACTION LINKS -->
                                    <div class="text-right action-links clearfix">
                                        <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-pencil-square-o', 'aria-hidden' => "true")) . 'EDIT', array('controller' => 'hqusers', 'action' => 'updateAddress', $this->Encryption->encode($checkaddress[0]['DeliveryAddress']['id'])), array('class' => '', 'escape' => false)); ?>  &nbsp;&nbsp;&nbsp;
                                        <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-trash-o', 'aria-hidden' => "true")) . 'DELETE', array('controller' => 'hqusers', 'action' => 'deleteDeliveryAddress', $this->Encryption->encode($checkaddress[0]['DeliveryAddress']['id'])), array('confirm' => __('Are you sure you want to delete this delivery address?'), 'class' => '', 'escape' => false)); ?>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="static-content-bg">
                                    <h3>Please add your delivery address.</h3>
                                    <?php echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-plus-circle')) . 'Add Address', array('controller' => 'hqusers', 'action' => 'addAddress'), array('class' => '', 'escape' => false)); ?> 
                                </div>
                            <?php } ?>
                        </div>
                        <!-- CONTENT END -->
                    </div>
                    <div class="ext-border">
                        <?php echo $this->Html->image('hq/thick-border.png', array('alt' => 'user')) ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function getDefaultAddress() {
        var deliveryId = $("input[type='radio'][class='deladdress']:checked").val();
        $.ajax({
            type: 'post',
            url: '/hqusers/getDeliveryAddress',
            data: {'deliveryId': deliveryId},
            success: function (result) {
                if (result) {
                    $('#delivery_address').html(result);
                }
            }
        });
    }

    getDefaultAddress();

    $(document).ready(function () {
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
        $.ajax({
            type: 'post',
            url: '/hqusers/getDeliveryAddress',
            data: {'deliveryId': deliveryId},
            success: function (result) {
                if (result) {
                    $('#delivery_address').html(result);
                }
            }
        });
    });


</script>