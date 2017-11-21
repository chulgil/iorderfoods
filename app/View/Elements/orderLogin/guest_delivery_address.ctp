<style>
    #DeliveryAddressCountryCodeId{
        background-color:#e6e5e4;
        border: 1px solid rgba(155, 155, 155, 0.4);
        border-radius: 1px;
        font-size: 14px;
        font-weight: 300;
        padding: 8px;
    }

    #DeliveryAddressPhone{
        width:40%;
        margin-left:8px;
    }
    .egspan{margin:9px 0px 0px 10px;font-size:11px;display:inline-block;width:auto;font-weight:300;}
</style>

<section class="col-12 guest-delivery">
    <div id="flashMsg"></div>
    <?php echo $this->Form->create('guestdelivery', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'name' => 'guestForm', 'id' => 'guestForm', 'url' => array('controller' => 'ajaxMenus', 'action' => 'guestdelivery'))); ?>                  
    <ul class="clearfix"> 
        <?php if (!$this->Session->check('Order.delivery_address_id')) { ?>
            <li class="col-xs-6">
                <span class="title"><label>Name <em>*</em></label></span>
                <div class="title-box"> 
                    <?php
                    echo $this->Form->input('DeliveryAddress.name', array('type' => 'name', 'class' => 'inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name'));
                    echo $this->Form->error('DeliveryAddress.name');
                    ?> 
                </div>
            </li>
            <li class="col-xs-6">
                <span class="title"><label>Email <em>*</em></label></span>
                <div class="title-box"> 
                    <?php
                    echo $this->Form->input('DeliveryAddress.email', array('type' => 'email', 'class' => 'inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Email'));
                    echo $this->Form->error('DeliveryAddress.email');
                    ?> 
                </div>
            </li>

            <li class="col-xs-12">
                <span class="title"><label>Phone Number <em>*</em></label></span>
                <div class="title-box"> 
                    <?php
                    $countryCode = $this->Common->getAllCountryCode();
                    ;
                    echo $this->Form->input('DeliveryAddress.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'inbox country-code', 'label' => false, 'div' => false));
                    echo $this->Form->input('DeliveryAddress.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'inbox phone-number', 'placeholder' => 'Enter Your Phone Number', 'label' => false, 'div' => false));
                    echo $this->Form->error('DeliveryAddress.phone');
                    ?>
                    <span class='egspan'>(eg. 111-111-1111)</span>
                </div>
            </li>

        <?php } ?>


        <li class="col-xs-12">
            <span class="title"><label>Address <em>*</em></label></span>
            <div class="title-box"> <?php
                echo $this->Form->input('DeliveryAddress.address', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false));
                echo $this->Form->error('DeliveryAddress.address');
                ?> </div>
        </li>

        <li class="col-xs-4">
            <span class="title"><label>City <em>*</em></label></span>
            <div class="title-box"><?php
                echo $this->Form->input('DeliveryAddress.city', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your City', 'maxlength' => '50', 'label' => false, 'div' => false));
                echo $this->Form->error('DeliveryAddress.city');
                ?></div>
        </li>

        <li  class="col-xs-4">
            <span class="title"><label>State <em>*</em></label></span>
            <div class="title-box"><?php
                echo $this->Form->input('DeliveryAddress.state', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your State', 'maxlength' => '50', 'label' => false, 'div' => false, 'autocomplete' => 'off'));
                echo $this->Form->error('DeliveryAddress.state');
                ?></div>
        </li>

        <li class="col-xs-4">
            <span class="title"><label>Zip-Code <em>*</em></label></span>
            <div class="title-box"><?php
                echo $this->Form->input('DeliveryAddress.zipcode', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false));
                echo $this->Form->error('DeliveryAddress.zipcode');
                ?></div>
        </li>




    </ul>


    <div class="button-frame">
        <span style="float:left;">
            <b><?php echo $this->Html->link('Change Order Type', 'javascript:void(0)', array('class' => 'button-link', 'escape' => false, 'id' => 'changeorderType')); ?></b>
        </span>
        <button type="button" id="guestDeliveryButton" class="btn btn-primary theme-bg-1">Continue</button>


    </div>

    <?php echo $this->Form->end(); ?>    
</section>





<script>

    $(document).ready(function () {


        $('#changeorderType').click(function () {
            changeTabPan('chkOrderType', 'chkDeliveryAddress');
            setDefaultStoreTime(3);
        });
        $(".phone-number").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });
        $("[data-mask='mobileNo']").mask("(999) 999-9999");
<?php if ($this->Session->check('Order.delivery_address_id')) { ?>

            $("#guestForm").validate({
                rules: {
                    "data[DeliveryAddress][address]": {
                        required: true,
                    },
                    "data[DeliveryAddress][city]": {
                        required: true,
                        lettersonly: true,
                    },
                    "data[DeliveryAddress][state]": {
                        required: true,
                        lettersonly: true,
                    },
                    "data[DeliveryAddress][zipcode]": {
                        required: true,
                        number: true,
                        minlength: 5,
                        maxlength: 5,
                    }
                },
                messages: {
                    "data[DeliveryAddress][address]": {
                        required: "Please enter your address",
                    },
                    "data[DeliveryAddress][city]": {
                        required: "Please enter city",
                        lettersonly: "Only alphabates allowed",
                    },
                    "data[DeliveryAddress][state]": {
                        required: "Please enter state ",
                        lettersonly: "Only alphabates allowed",
                    },
                    "data[DeliveryAddress][zipcode]": {
                        required: "Please enter zip-code.",
                        number: "Only numbers are allowed"
                    }
                }
            });
<?php } else { ?>

            $("#guestForm").validate({
                rules: {
                    "data[DeliveryAddress][name]": {
                        required: true,
                        lettersonly: true,
                    },
                    "data[DeliveryAddress][email]": {
                        required: true,
                        email: true,
                        minlength: 10,
                        maxlength: 50,
                    },
                    "data[DeliveryAddress][phone]": {
                        required: true,
                    },
                    "data[DeliveryAddress][address]": {
                        required: true,
                    },
                    "data[DeliveryAddress][city]": {
                        required: true,
                        lettersonly: true,
                    },
                    "data[DeliveryAddress][state]": {
                        required: true,
                        lettersonly: true,
                    },
                    "data[DeliveryAddress][zipcode]": {
                        required: true,
                        number: true,
                        minlength: 5,
                        maxlength: 5,
                    }
                },
                messages: {
                    "data[DeliveryAddress][name]": {
                        required: "Please enter name",
                        lettersonly: "Only alphabates allowed",
                    },
                    "data[DeliveryAddress][email]": {
                        required: "Please enter email",
                        email: "Please enter valid email"
                    },
                    "data[DeliveryAddress][phone]": {
                        required: "Please enter phone number",
                    },
                    "data[DeliveryAddress][address]": {
                        required: "Please enter your address",
                    },
                    "data[DeliveryAddress][city]": {
                        required: "Please enter city",
                        lettersonly: "Only alphabates allowed",
                    },
                    "data[DeliveryAddress][state]": {
                        required: "Please enter state ",
                        lettersonly: "Only alphabates allowed",
                    },
                    "data[DeliveryAddress][zipcode]": {
                        required: "Please enter zip-code.",
                        number: "Only numbers are allowed"
                    }
                }
            });
<?php } ?>

        $('#guestDeliveryButton').click(function () {
            if ($("#guestForm").valid()) {
                $('#loading').show();
                $.ajax({
                    type: 'POST',
                    url: '/ajaxMenus/guestdelivery',
                    data: $('#guestForm').serialize(),
                    success: function (res) {
                        $('#loading').hide();
                        var obj = jQuery.parseJSON(res);
                        if (obj.status == 'Error') {
                            $('#flashMsg').html('<div class="alert alert-danger" id="flashMessage"><a href="#" data-dismiss="alert" aria-label="close" title="close" class="pull-right">×</a> ' + obj.msg + '</div>');
                            return false;
                        } else {
                            window.location = window.location;
                        }
                    }
                });
            }
        });
    });
</script>