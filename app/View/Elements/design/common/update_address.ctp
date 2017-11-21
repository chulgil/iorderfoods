<div class="title-bar">
    Delivery Address
</div>
<div class="main-container delivery-page">
    <div class="ext-menu-title">
        <h4><?php echo __('Update Delivery Address'); ?></h4>
    </div>
    <div class="inner-wrap profile">
        <?php //echo $this->Session->flash(); ?>
        <div class="form-section update-delivery-address-wrap">
            <?php
            echo $this->Form->create('DeliveryAddress', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'Deliveryaddress'));
            echo $this->Form->input('DeliveryAddress.id', array('type' => 'hidden', 'class' => 'usrname-input', 'div' => false, 'value' => $addressId));
            echo $this->Form->input('DeliveryAddress.store_id', array('type' => 'hidden', 'class' => 'usrname-input', 'div' => false, 'value' => $encrypted_storeId));
            echo $this->Form->input('DeliveryAddress.merchant_id', array('type' => 'hidden', 'class' => 'usrname-input', 'div' => false, 'value' => $encrypted_merchantId));
            ?>
            <div class="profile-input clearfix">
                <label>Name <em>*</em></label>
                <div class="col-right"><?php
                    echo $this->Form->input('DeliveryAddress.name_on_bell', array('type' => 'text', 'class' => 'user-detail', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name'));
                    ?>
                </div>
            </div>
            <div class="profile-input clearfix">
                <label>Address <em>*</em></label>
                <div class="col-right"><?php
                    echo $this->Form->input('DeliveryAddress.address', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false));
                    ?></div>
            </div>
            <div class="profile-input clearfix">
                <label>City </label>
                <div class="col-right"><?php
                    echo $this->Form->input('DeliveryAddress.city', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false));
                    ?></div>
            </div>
            <div class="profile-input clearfix">
                <label>State <em>*</em></label>
                <div class="col-right"><?php
                    echo $this->Form->input('DeliveryAddress.state', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off'));
                    ?></div>
            </div>
            <div class="profile-input clearfix">
                <label>Zip-Code <em>*</em></label>
                <div class="col-right"><?php
                    echo $this->Form->input('DeliveryAddress.zipcode', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true));
                    ?></div>
            </div>
            <div class="profile-input clearfix">
                <label>Phone Number<em>*</em></label>
                <div class="col-right">
                    <div class="col-1"><?php echo $this->Form->input('DeliveryAddress.country_code_id', array('type' => 'select', 'options' => $countryCode, 'value' => $this->request->data['CountryCode']['id'], 'class' => 'user-detail country-code', 'label' => false, 'div' => false)); ?></div>
                    <div class="col-2"><?php echo $this->Form->input('DeliveryAddress.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'user-detail phone-number', 'placeholder' => 'Phone Number', 'label' => false, 'div' => false, 'required' => true)); ?>
                        <span>(eg. 111-111-1111)</span>
                    </div>
                </div>
            </div>
            <div class="profile-input clearfix">
                <label>&nbsp;</label>
                <div class="col-right">
                    <?php
                    $checked = "";
                    if ($this->request->data['DeliveryAddress']['default'] == 1) {
                        $checked = "checked";
                    }
                    echo $this->Form->checkbox('DeliveryAddress.default', array('checked' => $checked, 'class' => 'ordertype', 'style' => 'position:relative;top:4px;'));
                    ?> &nbsp;Default address
                </div>
            </div>
            <div class="profile-btn-section clearfix">
                <div class="row">
                    <?php if (DESIGN == 3) { ?>
                        <?php echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/deliveryAddress/$encrypted_storeId/$encrypted_merchantId'", 'class' => 'p-cancle')); ?>
                        <?php echo $this->Form->button('Update', array('type' => 'submit', 'class' => 'p-save theme-bg-1')); ?>
                    <?php } else { ?>
                        <div class="col-sm-6 col-xs-6">
                            <?php echo $this->Form->button('Update', array('type' => 'submit', 'class' => 'p-save theme-bg-1')); ?>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                            <?php echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/deliveryAddress/$encrypted_storeId/$encrypted_merchantId'", 'class' => 'p-cancle theme-bg-2')); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
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

    jQuery.validator.addMethod("lettersonly", function (value, element)
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
                lettersonly: true,
                minlength: 2,
                maxlength: 30
            },
            "data[DeliveryAddress][address]": {
                required: true,
                minlength: 2,
                maxlength: 50
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
