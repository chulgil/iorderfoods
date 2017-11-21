<!-- static-banner -->
<?php echo $this->element('hquser/static_banner'); ?>
<!-- /banner -->
<div class="signup-form">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="common-title clearfix">
                    <span class="yello-dash"></span>
                    <h2><?php echo __('Update Delivery Address'); ?></h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php echo $this->Session->flash(); ?>
                <div class="form-bg">
                    <!-- -->
                    <?php
                    echo $this->Form->create('DeliveryAddress', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'Deliveryaddress', "class" => "sign-up"));
                    ?>
                    <div class="delivery-address clearfix">
                        <div class="custom-checkbox">

                            <?php
                            $checked = "";
                            if ($this->request->data['DeliveryAddress']['default'] == 1) {
                                $checked = "checked";
                            }
                            echo $this->Form->input('DeliveryAddress.default', array('type' => 'checkbox', 'checked' => $checked));
                            ?> 
                            <label for="DeliveryAddressDefault">Default address</label>
                        </div>
                    </div>
                    <!-- CONTENT -->
                    <div class="main-form margin-top35">
                        <div class="form-group">
                            <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/user.png', array('alt' => 'user')) ?></span><label>Name <sup>*</sup></label></div>
                            <div class="rgt-box">
                                <?php
                                echo $this->Form->input('DeliveryAddress.name_on_bell', array('type' => 'text', 'class' => 'form-control custom-text', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name'));
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/pincode.png', array('alt' => 'user')) ?></span><label>Address <sup>*</sup></label></div>
                            <div class="rgt-box">
                                <?php
                                echo $this->Form->input('DeliveryAddress.address', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false));
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/city-state.png', array('alt' => 'user')) ?></span><label>City <sup>*</sup></label></div>
                            <div class="rgt-box">
                                <?php
                                echo $this->Form->input('DeliveryAddress.city', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false));
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/city-state.png', array('alt' => 'user')) ?></span><label>State <sup>*</sup></label></div>
                            <div class="rgt-box">
                                <?php
                                echo $this->Form->input('DeliveryAddress.state', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off'));
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/pincode.png', array('alt' => 'user')) ?></span><label>Zip Code <sup>*</sup></label></div>
                            <div class="rgt-box">
                                <?php
                                echo $this->Form->input('DeliveryAddress.zipcode', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true));
                                ?>
                            </div>
                        </div>

                        <div class="form-group twin-block">
                            <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/mobile.png', array('alt' => 'user')) ?></span><label>Mobile Phone <sup>*</sup></label>
                            </div>
                            <div class="rgt-box">
                                <?php echo $this->Form->input('DeliveryAddress.country_code_id', array('type' => 'select', 'options' => $countryCode, 'value' => $this->request->data['CountryCode']['id'], 'class' => 'form-control custom-text country-code', 'label' => false, 'div' => false)); ?>
                                <div class="phone-input">
                                    <?php
                                    echo $this->Form->input('DeliveryAddress.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control custom-text phone-number', 'placeholder' => 'Mobile Phone ( 111-111-111)', 'label' => false, 'div' => false, 'required' => true));
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="submit-btn clearfix">
                            <?php
                            echo $this->Form->button('UPDATE', array('type' => 'submit', 'class' => 'btn common-config black-bg'));
                            echo $this->Form->button('CANCEL', array('type' => 'button', 'onclick' => "window.location.href='/hqusers/myDeliveryAddress'", 'class' => 'btn common-config black-bg'));
                            ?>
                        </div>
                    </div>
                    <!-- CONTENT END -->
                    <?php echo $this->Form->end(); ?>
                    <!-- -->
                    <div class="ext-border">
                        <?php echo $this->Html->image('hq/thick-border.png', array('alt' => 'user')) ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(".phone-number").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    });
    $("[data-mask='mobileNo']").mask("(999) 999-9999");
    
    jQuery.validator.addMethod("lettersonly", function(value, element) 
        {
        return this.optional(element) || /^[a-z," "]+$/i.test(value);
        }, "Letters and spaces only please"); 

    $("#Deliveryaddress").validate({
        debug: false,
        errorClass: "error",
        errorElement: 'span',
        onkeyup: false,
        rules: {
            "data[DeliveryAddress][name_on_bell]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress][address]": {
                required: true
            },
            "data[DeliveryAddress][city]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress][state]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress][zipcode]": {
                required: true,
                number: true,
                minlength: 5,
                maxlength: 5
            }, "data[DeliveryAddress][phone]": {
                required: true
            },
        },
        messages: {
            "data[DeliveryAddress][name_on_bell]": {
                required: "Please enter your name",
                lettersonly: "Only alphabates allowed"
            },
            "data[DeliveryAddress][address]": {
                required: "Please enter your address"
            },
            "data[DeliveryAddress][city]": {
                required: "Please enter city",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress][state]": {
                required: "Please enter state ",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress][zipcode]": {
                required: "Please enter zip-code.",
                number: "Only numbers are allowed"
            },
            "data[DeliveryAddress][phone]": {
                required: "Contact number required"
            }
        }, highlight: function (element, errorClass) {
            $(element).removeClass(errorClass);
        }
    });
</script>
