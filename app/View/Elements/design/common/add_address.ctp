<?php if (DESIGN == 3) { ?>
    <div class="title-bar"><?php echo __('Add Delivery Addresses'); ?></div>
<?php } ?>
<div class="main-container">
    <div class="ext-menu-title">
        <h4><?php echo __('Add Delivery Addresses'); ?></h4>
    </div>
    <?php //echo $this->Session->flash(); ?>
    <div class="user-custom-wrap">
        <div class="inner-wrap no-border">
            <?php
            echo $this->Form->create('DeliveryAddress', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'Deliveryaddress', 'class' => 'form-horizontal'));
            ?>
            <?php if ($label1 != 1) { ?>
                <div class="delivery-add-wrap">
                    <input type="checkbox" id="home_address"  name="data[DeliveryAddress][label1]"  checked/>
                    <label for="home_address">Home Address</label>
                </div>
                <div id='home_block'>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Name<em>*</em>:</label>
                        <div class="col-sm-10">
                            <?php
                            echo $this->Form->input('DeliveryAddress.name_on_bell', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name'));
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Address<em>*</em>:</label>
                        <div class="col-sm-10">
    <?php
    echo $this->Form->input('DeliveryAddress.address', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false));
    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">City<em>*</em>:</label>
                        <div class="col-sm-10">
    <?php
    echo $this->Form->input('DeliveryAddress.city', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false));
    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">State<em>*</em>:</label>
                        <div class="col-sm-10">
    <?php
    echo $this->Form->input('DeliveryAddress.state', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off'));
    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Zip-Code<em>*</em></label>
                        <div class="col-sm-10">
    <?php
    echo $this->Form->input('DeliveryAddress.zipcode', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true));
    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Phone Number<em>*</em>:</label>
                        <div class="col-sm-10">
                            <div class="row">
                                <div class="col-sm-2 col-xs-3">
    <?php
    echo $this->Form->input('DeliveryAddress.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'form-control country-code', 'label' => false, 'div' => false));
    ?>
                                </div>
                                <div class="col-sm-10 col-xs-9">
                                    <?php
                                    echo $this->Form->input('DeliveryAddress.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control phone-number', 'placeholder' => 'Phone Number', 'label' => false, 'div' => false, 'required' => true));
                                    ?>
                                    <span>(eg. 111-111-1111)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                                <?php } ?>
                                <?php if ($label2 != 1) { ?>
                <div class="delivery-add-wrap">
                    <input type="checkbox" id="work_address"  name="data[DeliveryAddress1][label2]"/>
                    <label for="work_address">Work Address</label>
                </div>
                <div id='work_block'>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Name<em>*</em>:</label>
                        <div class="col-sm-10">
    <?php
    echo $this->Form->input('DeliveryAddress1.name_on_bell', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name'));
    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Address<em>*</em>:</label>
                        <div class="col-sm-10">
                            <?php
                            echo $this->Form->input('DeliveryAddress1.address', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false));
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">City<em>*</em>:</label>
                        <div class="col-sm-10">
                            <?php
                            echo $this->Form->input('DeliveryAddress1.city', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false));
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">State<em>*</em>:</label>
                        <div class="col-sm-10">
    <?php
    echo $this->Form->input('DeliveryAddress1.state', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off'));
    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Zip-Code<em>*</em></label>
                        <div class="col-sm-10">
    <?php
    echo $this->Form->input('DeliveryAddress1.zipcode', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true));
    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Phone Number<em>*</em>:</label>
                        <div class="col-sm-10">
                            <div class="row">
                                <div class="col-sm-2 col-xs-3">
    <?php
    echo $this->Form->input('DeliveryAddress1.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'form-control country-code', 'label' => false, 'div' => false));
    ?>
                                </div>
                                <div class="col-sm-10 col-xs-9">
                            <?php
                            echo $this->Form->input('DeliveryAddress1.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control phone-number', 'placeholder' => 'Phone Number', 'label' => false, 'div' => false, 'required' => true));
                            ?>
                                    <span>(eg. 111-111-1111)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                                <?php } ?>
                                <?php if ($label3 != 1) { ?>
                <div class="delivery-add-wrap">
                    <input type="checkbox" id="other_address"  name="data[DeliveryAddress2][label3]"  />
                    <label for="other_address">Other Address</label>
                </div>
                <div id='other_block'>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Name<em>*</em>:</label>
                        <div class="col-sm-10">
    <?php
    echo $this->Form->input('DeliveryAddress2.name_on_bell', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name'));
    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Address<em>*</em>:</label>
                        <div class="col-sm-10">
    <?php
    echo $this->Form->input('DeliveryAddress2.address', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false));
    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">City<em>*</em>:</label>
                        <div class="col-sm-10">
                            <?php
                            echo $this->Form->input('DeliveryAddress2.city', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false));
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">State<em>*</em>:</label>
                        <div class="col-sm-10">
                            <?php
                            echo $this->Form->input('DeliveryAddress2.state', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off'));
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Zip-Code<em>*</em></label>
                        <div class="col-sm-10">
                            <?php
                            echo $this->Form->input('DeliveryAddress2.zipcode', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true));
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Phone Number<em>*</em>:</label>
                        <div class="col-sm-10">
                            <div class="row">
                                <div class="col-sm-2 col-xs-3">
                            <?php
                            echo $this->Form->input('DeliveryAddress2.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'form-control country-code', 'label' => false, 'div' => false));
                            ?>
                                </div>
                                <div class="col-sm-10 col-xs-9">
    <?php
    echo $this->Form->input('DeliveryAddress2.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control phone-number', 'placeholder' => 'Phone Number', 'label' => false, 'div' => false, 'required' => true));
    ?>
                                    <span>(eg. 111-111-1111)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<?php } ?>
<?php if ($label4 != 1) { ?>
                <div class="delivery-add-wrap">
                    <input type="checkbox" id="address4"  name="data[DeliveryAddress3][label4]"  />
                    <label for="address4">Address 4</label>
                </div>
                <div id='address4_block'>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Name<em>*</em>:</label>
                        <div class="col-sm-10">
    <?php
    echo $this->Form->input('DeliveryAddress3.name_on_bell', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name'));
    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Address<em>*</em>:</label>
                        <div class="col-sm-10">
    <?php
    echo $this->Form->input('DeliveryAddress3.address', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false));
    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">City<em>*</em>:</label>
                        <div class="col-sm-10">
    <?php
    echo $this->Form->input('DeliveryAddress3.city', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false));
    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">State<em>*</em>:</label>
                        <div class="col-sm-10">
                            <?php
                            echo $this->Form->input('DeliveryAddress3.state', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off'));
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Zip-Code<em>*</em></label>
                        <div class="col-sm-10">
                            <?php
                            echo $this->Form->input('DeliveryAddress3.zipcode', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true));
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Phone Number<em>*</em>:</label>
                        <div class="col-sm-10">
                            <div class="row">
                                <div class="col-sm-2 col-xs-3">
                            <?php
                            echo $this->Form->input('DeliveryAddress3.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'form-control country-code', 'label' => false, 'div' => false));
                            ?>
                                </div>
                                <div class="col-sm-10 col-xs-9">
    <?php
    echo $this->Form->input('DeliveryAddress3.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control phone-number', 'placeholder' => 'Phone Number', 'label' => false, 'div' => false, 'required' => true));
    ?>
                                    <span>(eg. 111-111-1111)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<?php } ?>
                        <?php if ($label5 != 1) { ?>
                <div class="delivery-add-wrap">
                    <input type="checkbox" id="address5"  name="data[DeliveryAddress4][label5]"  />
                    <label for="address5">Address 5</label>
                </div>
                <div id='address5_block'>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Name<em>*</em>:</label>
                        <div class="col-sm-10">
    <?php
    echo $this->Form->input('DeliveryAddress4.name_on_bell', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name'));
    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Address<em>*</em>:</label>
                        <div class="col-sm-10">
                                    <?php
                                    echo $this->Form->input('DeliveryAddress4.address', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false));
                                    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">City<em>*</em>:</label>
                        <div class="col-sm-10">
    <?php
    echo $this->Form->input('DeliveryAddress4.city', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false));
    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">State<em>*</em>:</label>
                        <div class="col-sm-10">
    <?php
    echo $this->Form->input('DeliveryAddress4.state', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off'));
    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Zip-Code<em>*</em></label>
                        <div class="col-sm-10">
                            <?php
                            echo $this->Form->input('DeliveryAddress4.zipcode', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true));
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">Phone Number<em>*</em>:</label>
                        <div class="col-sm-10">
                            <div class="row">
                                <div class="col-sm-2 col-xs-3">
    <?php
    echo $this->Form->input('DeliveryAddress4.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'form-control country-code', 'label' => false, 'div' => false));
    ?>
                                </div>
                                <div class="col-sm-10 col-xs-9">
                            <?php
                            echo $this->Form->input('DeliveryAddress4.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control phone-number', 'placeholder' => 'Phone Number', 'label' => false, 'div' => false, 'required' => true));
                            ?>
                                    <span>(eg. 111-111-1111)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                        <?php } ?>
            <div class="profile-input profile-btn-section add-delivery-address clearfix">
                <div class="row">
                        <?php if (DESIGN == 3) { ?>
    <?php
    echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/deliveryAddress/$encrypted_storeId/$encrypted_merchantId'", 'class' => 'p-cancle'));
    ?>
    <?php
    echo $this->Form->button('Submit', array('type' => 'submit', 'class' => 'p-save theme-bg-1'));
    ?>
                        <?php } else { ?>
                        <div class="col-sm-6 col-xs-6">
                            <?php
                            echo $this->Form->button('Submit', array('type' => 'submit', 'class' => 'p-save theme-bg-1'));
                            ?>
                        </div>
                        <div class="col-sm-6 col-xs-6">
    <?php
    echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/deliveryAddress/$encrypted_storeId/$encrypted_merchantId'", 'class' => 'p-cancle theme-bg-2'));
    ?>
                        </div>
                                <?php } ?>
                </div>
            </div>
<?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $(".phone-number").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });

        $("[data-mask='mobileNo']").mask("(999) 999-9999");

        $('#work_block').css('display', 'none');
        $("#work_block input").prop("disabled", true);
        $("#work_block select").prop("disabled", true);
        $('#other_block').css('display', 'none');
        $("#other_block input").prop("disabled", true);
        $("#other_block select").prop("disabled", true);

        $('#address4_block').css('display', 'none');
        $("#address4_block input").prop("disabled", true);
        $("#address4_block select").prop("disabled", true);

        $('#address5_block').css('display', 'none');
        $("#address5_block input").prop("disabled", true);
        $("#address5_block select").prop("disabled", true);
    });

    $('#home_address').on('change', function () {
        if ($(this).prop('checked')) {
            $('#home_block').css('display', 'block');
            $("#home_block input").prop("disabled", false);
            $("#home_block select").prop("disabled", false);
        } else {
            $('#home_block').css('display', 'none');
            $("#home_block input").prop("disabled", true);
            $("#home_block select").prop("disabled", true);
        }
    });
    $('#work_address').on('change', function () {
        if ($(this).prop('checked')) {
            $('#work_block').css('display', 'block');
            $("#work_block input").prop("disabled", false);
            $("#work_block select").prop("disabled", false);
        } else {
            $('#work_block').css('display', 'none');
            $("#work_block input").prop("disabled", true);
            $("#work_block select").prop("disabled", true);
        }
    });
    $('#other_address').on('change', function () {
        if ($(this).prop('checked')) {
            $('#other_block').css('display', 'block');
            $("#other_block input").prop("disabled", false);
            $("#other_block select").prop("disabled", false);
        } else {
            $('#other_block').css('display', 'none');
            $("#other_block input").prop("disabled", true);
            $("#other_block select").prop("disabled", true);
        }
    });

    $('#address4').on('change', function () {
        if ($(this).prop('checked')) {
            $('#address4_block').css('display', 'block');
            $("#address4_block input").prop("disabled", false);
            $("#address4_block select").prop("disabled", false);
        } else {
            $('#address4_block').css('display', 'none');
            $("#address4_block input").prop("disabled", true);
            $("#address4_block select").prop("disabled", true);
        }
    });

    $('#address5').on('change', function () {
        if ($(this).prop('checked')) {
            $('#address5_block').css('display', 'block');
            $("#address5_block input").prop("disabled", false);
            $("#address5_block select").prop("disabled", false);
        } else {
            $('#address5_block').css('display', 'none');
            $("#address5_block input").prop("disabled", true);
            $("#address5_block select").prop("disabled", true);
        }
    });

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
            "data[DeliveryAddress1][name_on_bell]": {
                required: true,
                lettersonly: true,
                minlength: 2,
                maxlength: 30
            },
            "data[DeliveryAddress1][address]": {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            "data[DeliveryAddress1][city]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress1][state]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress1][zipcode]": {
                required: true,
                number: true,
                minlength: 5,
                maxlength: 5
            }, "data[DeliveryAddress1][phone]": {
                required: true
            },
            "data[DeliveryAddress2][name_on_bell]": {
                required: true,
                lettersonly: true,
                minlength: 2,
                maxlength: 30
            },
            "data[DeliveryAddress2][address]": {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            "data[DeliveryAddress2][city]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress2][state]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress2][zipcode]": {
                required: true,
                number: true,
                minlength: 5,
                maxlength: 5
            }, "data[DeliveryAddress2][phone]": {
                required: true
            },
            "data[DeliveryAddress3][name_on_bell]": {
                required: true,
                lettersonly: true,
                minlength: 2,
                maxlength: 30
            },
            "data[DeliveryAddress3][address]": {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            "data[DeliveryAddress3][city]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress3][state]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress3][zipcode]": {
                required: true,
                number: true,
                minlength: 5,
                maxlength: 5
            }, "data[DeliveryAddress3][phone]": {
                required: true
            },
            "data[DeliveryAddress4][name_on_bell]": {
                required: true,
                lettersonly: true,
                minlength: 2,
                maxlength: 30
            },
            "data[DeliveryAddress4][address]": {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            "data[DeliveryAddress4][city]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress4][state]": {
                required: true,
                lettersonly: true
            },
            "data[DeliveryAddress4][zipcode]": {
                required: true,
                number: true,
                minlength: 5,
                maxlength: 5
            }, "data[DeliveryAddress4][phone]": {
                required: true
            }
        },
        messages: {
            "data[DeliveryAddress][name_on_bell]": {
                required: "Please enter your name",
                lettersonly: "Only alphabates are allowed"
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
            },
            "data[DeliveryAddress1][name_on_bell]": {
                required: "Please enter your name",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress1][address]": {
                required: "Please enter your address"
            },
            "data[DeliveryAddress1][city]": {
                required: "Please enter city",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress1][state]": {
                required: "Please enter state ",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress1][zipcode]": {
                required: "Please enter zip-code.",
                number: "Only numbers are allowed"
            },
            "data[DeliveryAddress1][phone]": {
                required: "Contact number required"
            },
            "data[DeliveryAddress2][name_on_bell]": {
                required: "Please enter your name",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress2][address]": {
                required: "Please enter your address"
            },
            "data[DeliveryAddress2][city]": {
                required: "Please enter city",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress2][state]": {
                required: "Please enter state ",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress2][zipcode]": {
                required: "Please enter zip-code.",
                number: "Only numbers are allowed"
            },
            "data[DeliveryAddress2][phone]": {
                required: "Contact number required"
            },
            "data[DeliveryAddress3][name_on_bell]": {
                required: "Please enter your name",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress3][address]": {
                required: "Please enter your address"
            },
            "data[DeliveryAddress3][city]": {
                required: "Please enter city",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress3][state]": {
                required: "Please enter state ",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress3][zipcode]": {
                required: "Please enter zip-code.",
                number: "Only numbers are allowed"
            },
            "data[DeliveryAddress3][phone]": {
                required: "Contact number required"
            },
            "data[DeliveryAddress4][name_on_bell]": {
                required: "Please enter your name",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress4][address]": {
                required: "Please enter your address"
            },
            "data[DeliveryAddress4][city]": {
                required: "Please enter city",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress4][state]": {
                required: "Please enter state ",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress4][zipcode]": {
                required: "Please enter zip-code.",
                number: "Only numbers are allowed"
            },
            "data[DeliveryAddress4][phone]": {
                required: "Contact number required"
            }
        }, highlight: function (element, errorClass) {
            $(element).removeClass(errorClass);
        }
    });
</script>

