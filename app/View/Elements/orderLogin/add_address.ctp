<div class="content single-frame" style="width:70%;">
    <div class="wrap">
        <div id="flashMsg"></div>
        <?php //echo $this->Session->flash(); ?>
        <?php
        echo $this->Form->create('DeliveryAddress', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'Deliveryaddress'));
        ?>
        <div class="clearfix">
            <section class="form-layout col-12"> <!-- left-blank -->
                <h2> Add Delivery Addresses</h2> 

               	<div class="clearfix clear-clearfix">
                    <div class="title-box">
                        <?php if ($label1 != 1) { ?>
                            <div id="label1">
                                <div class="full-width add-address-option">
                                    <div class="password-remember" style="padding-top:0;"><input type="checkbox" id="home_address"  name="data[DeliveryAddress][label1]"  checked/> <label for="home_address" class="common-bold common-size">Home Address</label></div>
                                </div>

                                <ul id='home_block' class="clear-cleartfix">
                                    <li>
                                        <span class="title"><label>Name <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress.name_on_bell', array('type' => 'text', 'class' => 'inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name'));
                                            echo $this->Form->error('DeliveryAddress.name_on_bell');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>Address <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress.address', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false));
                                            echo $this->Form->error('DeliveryAddress.address');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>City <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress.city', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false));
                                            echo $this->Form->error('DeliveryAddress.city');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>State <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress.state', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off'));
                                            echo $this->Form->error('DeliveryAddress.state');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>Zip-Code <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress.zipcode', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true));
                                            echo $this->Form->error('DeliveryAddress.zipcode');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>Phone Number<em>*</em></label></span>
                                        <div class="title-box">
                                            <?php echo $this->Form->input('DeliveryAddress.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'inbox country-code', 'label' => false, 'div' => false)); ?>
                                            <?php
                                            echo $this->Form->input('DeliveryAddress.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'inbox phone-number', 'placeholder' => 'Phone Number', 'label' => false, 'div' => false, 'required' => true));
                                            echo $this->Form->error('DeliveryAddress.phone');
                                            ?>
                                            <span style='margin:2px 0px 0px 80px;font-size:12px;'>(eg. 111-111-1111)</span>
                                        </div>
                                    </li>

                                </ul>       
                            </div>
                        <?php } ?>
                        <?php if ($label2 != 1) { ?>
                            <div id="label2">
                                <div class="full-width add-address-option">
                                    <div class="password-remember" style="padding-top:0;"><input type="checkbox" id="work_address"  name="data[DeliveryAddress1][label2]"  /> <label for="work_address" class="common-bold common-size">Work Address</label></div>
                                </div>

                                <ul id='work_block' class="clear-cleartfix">
                                    <li>
                                        <span class="title"><label>Name <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress1.name_on_bell', array('type' => 'text', 'class' => 'inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name'));
                                            echo $this->Form->error('DeliveryAddress1.name_on_bell');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>Address <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress1.address', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false));
                                            echo $this->Form->error('DeliveryAddress1.address');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>City <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress1.city', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false));
                                            echo $this->Form->error('DeliveryAddress1.city');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>State <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress1.state', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off'));
                                            echo $this->Form->error('DeliveryAddress1.state');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>Zip-Code <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress1.zipcode', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true));
                                            echo $this->Form->error('DeliveryAddress1.zipcode');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>Phone Number<em>*</em></label></span>
                                        <div class="title-box">
                                            <?php echo $this->Form->input('DeliveryAddress1.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'inbox country-code', 'label' => false, 'div' => false)); ?>
                                            <?php
                                            echo $this->Form->input('DeliveryAddress1.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'inbox phone-number', 'placeholder' => 'Phone Number', 'label' => false, 'div' => false, 'required' => true));
                                            echo $this->Form->error('DeliveryAddress1.phone');
                                            ?>
                                            <span style='margin:2px 0px 0px 80px;font-size:12px;'>(eg. 111-111-1111)</span>
                                        </div>
                                    </li>

                                </ul>
                            </div>
                        <?php } ?>
                        <?php if ($label3 != 1) { ?>
                            <div id="label3">
                                <div class="full-width add-address-option">
                                    <div class="password-remember" style="padding-top:0;"><input type="checkbox" id="other_address"  name="data[DeliveryAddress2][label3]"  /> <label for="other_address" class="common-bold common-size">Other Address</label></div>
                                </div>
                                <ul id='other_block' class="clear-cleartfix">
                                    <li>
                                        <span class="title"><label>Name <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress2.name_on_bell', array('type' => 'text', 'class' => 'inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name'));
                                            echo $this->Form->error('DeliveryAddress2.name_on_bell');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>Address <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress2.address', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false));
                                            echo $this->Form->error('DeliveryAddress2.address');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>City <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress2.city', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false));
                                            echo $this->Form->error('DeliveryAddress2.city');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>State <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress2.state', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off'));
                                            echo $this->Form->error('DeliveryAddress2.state');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>Zip-Code <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress2.zipcode', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true));
                                            echo $this->Form->error('DeliveryAddress2.zipcode');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>Phone Number<em>*</em></label></span>
                                        <div class="title-box">
                                            <?php echo $this->Form->input('DeliveryAddress2.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'inbox country-code', 'label' => false, 'div' => false)); ?>
                                            <?php
                                            echo $this->Form->input('DeliveryAddress2.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'inbox phone-number', 'placeholder' => 'Phone Number', 'label' => false, 'div' => false, 'required' => true));
                                            echo $this->Form->error('DeliveryAddress2.phone');
                                            ?>
                                            <span style='margin:2px 0px 0px 80px;font-size:12px;'>(eg. 111-111-1111)</span>
                                        </div>
                                    </li>

                                </ul>
                            </div>
                        <?php } ?>




                        <?php if ($label4 != 1) { ?>
                            <div id="label4">
                                <div class="full-width add-address-option">
                                    <div class="password-remember" style="padding-top:0;"><input type="checkbox" id="address4"  name="data[DeliveryAddress3][label4]"  /> <label for="address4" class="common-bold common-size">Address 4</label></div>
                                </div>
                                <ul id='address4_block' class="clear-cleartfix">
                                    <li>
                                        <span class="title"><label>Name <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress3.name_on_bell', array('type' => 'text', 'class' => 'inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name'));
                                            echo $this->Form->error('DeliveryAddress3.name_on_bell');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>Address <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress3.address', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false));
                                            echo $this->Form->error('DeliveryAddress3.address');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>City <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress3.city', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false));
                                            echo $this->Form->error('DeliveryAddress3.city');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>State <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress3.state', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off'));
                                            echo $this->Form->error('DeliveryAddress3.state');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>Zip-Code <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress3.zipcode', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true));
                                            echo $this->Form->error('DeliveryAddress3.zipcode');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>Phone Number<em>*</em></label></span>
                                        <div class="title-box">
                                            <?php echo $this->Form->input('DeliveryAddress3.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'inbox country-code', 'label' => false, 'div' => false)); ?>
                                            <?php
                                            echo $this->Form->input('DeliveryAddress3.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'inbox phone-number', 'placeholder' => 'Phone Number', 'label' => false, 'div' => false, 'required' => true));
                                            echo $this->Form->error('DeliveryAddress3.phone');
                                            ?>
                                            <span style='margin:2px 0px 0px 80px;font-size:12px;'>(eg. 111-111-1111)</span>
                                        </div>
                                    </li>

                                </ul>
                            </div>
                        <?php } ?>



                        <?php if ($label5 != 1) { ?>
                            <div id="label5">
                                <div class="full-width add-address-option">
                                    <div class="password-remember" style="padding-top:0;"><input type="checkbox" id="address5"  name="data[DeliveryAddress4][label5]"  /> <label for="address5" class="common-bold common-size">Address 5</label></div>
                                </div>
                                <ul id='address5_block' class="clear-cleartfix">
                                    <li>
                                        <span class="title"><label>Name <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress4.name_on_bell', array('type' => 'text', 'class' => 'inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name'));
                                            echo $this->Form->error('DeliveryAddress4.name_on_bell');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>Address <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress4.address', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false));
                                            echo $this->Form->error('DeliveryAddress4.address');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>City <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress4.city', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false));
                                            echo $this->Form->error('DeliveryAddress4.city');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>State <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress4.state', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off'));
                                            echo $this->Form->error('DeliveryAddress4.state');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>Zip-Code <em>*</em></label></span>
                                        <div class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress4.zipcode', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true));
                                            echo $this->Form->error('DeliveryAddress4.zipcode');
                                            ?></div>
                                    </li>

                                    <li>
                                        <span class="title"><label>Phone Number<em>*</em></label></span>
                                        <div class="title-box">
                                            <?php echo $this->Form->input('DeliveryAddress4.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'inbox country-code', 'label' => false, 'div' => false)); ?>
                                            <?php
                                            echo $this->Form->input('DeliveryAddress4.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'inbox phone-number', 'placeholder' => 'Phone Number', 'label' => false, 'div' => false, 'required' => true));
                                            echo $this->Form->error('DeliveryAddress4.phone');
                                            ?>
                                            <span style='margin:2px 0px 0px 80px;font-size:12px;'>(eg. 111-111-1111)</span>
                                        </div>
                                    </li>

                                </ul>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="button-frame margin-TP20">
                    <?php
                    echo $this->Form->button('Submit', array('type' => 'button', 'class' => 'btn btn-primary theme-bg-1', 'id' => 'multipleadd'));
                    echo $this->Form->button('Cancel', array('type' => 'button', 'class' => 'btn btn-primary theme-bg-1', 'id' => 'cancelbutton'));
                    ?>
                </div>
            </section>
        </div>
        <?php echo $this->Form->end(); ?>
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

    $("#Deliveryaddress").validate({
        rules: {
            "data[DeliveryAddress][name_on_bell]": {
                required: true,
                lettersonly: true,
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
            }, "data[DeliveryAddress][phone]": {
                required: true,
            },
            "data[DeliveryAddress1][name_on_bell]": {
                required: true,
                lettersonly: true,
            },
            "data[DeliveryAddress1][address]": {
                required: true,
            },
            "data[DeliveryAddress1][city]": {
                required: true,
                lettersonly: true,
            },
            "data[DeliveryAddress1][state]": {
                required: true,
                lettersonly: true,
            },
            "data[DeliveryAddress1][zipcode]": {
                required: true,
                number: true,
                minlength: 5,
                maxlength: 5,
            }, "data[DeliveryAddress1][phone]": {
                required: true,
            },
            "data[DeliveryAddress2][name_on_bell]": {
                required: true,
                lettersonly: true,
            },
            "data[DeliveryAddress2][address]": {
                required: true,
            },
            "data[DeliveryAddress2][city]": {
                required: true,
                lettersonly: true,
            },
            "data[DeliveryAddress2][state]": {
                required: true,
                lettersonly: true,
            },
            "data[DeliveryAddress2][zipcode]": {
                required: true,
                number: true,
                minlength: 5,
                maxlength: 5,
            }, "data[DeliveryAddress2][phone]": {
                required: true,
            },
            "data[DeliveryAddress3][name_on_bell]": {
                required: true,
                lettersonly: true,
            },
            "data[DeliveryAddress3][address]": {
                required: true,
            },
            "data[DeliveryAddress3][city]": {
                required: true,
                lettersonly: true,
            },
            "data[DeliveryAddress3][state]": {
                required: true,
                lettersonly: true,
            },
            "data[DeliveryAddress3][zipcode]": {
                required: true,
                number: true,
                minlength: 5,
                maxlength: 5,
            }, "data[DeliveryAddress3][phone]": {
                required: true,
            },
            "data[DeliveryAddress4][name_on_bell]": {
                required: true,
                lettersonly: true,
            },
            "data[DeliveryAddress4][address]": {
                required: true,
            },
            "data[DeliveryAddress4][city]": {
                required: true,
                lettersonly: true,
            },
            "data[DeliveryAddress4][state]": {
                required: true,
                lettersonly: true,
            },
            "data[DeliveryAddress4][zipcode]": {
                required: true,
                number: true,
                minlength: 5,
                maxlength: 5,
            }, "data[DeliveryAddress4][phone]": {
                required: true,
            }
        },
        messages: {
            "data[DeliveryAddress][name_on_bell]": {
                required: "Please enter your name",
                lettersonly: "Only alphabates are allowed",
            },
            "data[DeliveryAddress][address]": {
                required: "Please enter your are address",
            },
            "data[DeliveryAddress][city]": {
                required: "Please enter city",
                lettersonly: "Only alphabates are allowed",
            },
            "data[DeliveryAddress][state]": {
                required: "Please enter state ",
                lettersonly: "Only alphabates are allowed",
            },
            "data[DeliveryAddress][zipcode]": {
                required: "Please enter zip-code.",
                number: "Only numbers are allowed"
            },
            "data[DeliveryAddress][phone]": {
                required: "Contact number required",
            },
            "data[DeliveryAddress1][name_on_bell]": {
                required: "Please enter your name",
                lettersonly: "Only alphabates are allowed",
            },
            "data[DeliveryAddress1][address]": {
                required: "Please enter your are address",
            },
            "data[DeliveryAddress1][city]": {
                required: "Please enter city",
                lettersonly: "Only alphabates are allowed",
            },
            "data[DeliveryAddress1][state]": {
                required: "Please enter state ",
                lettersonly: "Only alphabates are allowed",
            },
            "data[DeliveryAddress1][zipcode]": {
                required: "Please enter zip-code.",
                number: "Only numbers are allowed"
            },
            "data[DeliveryAddress1][phone]": {
                required: "Contact number required",
            },
            "data[DeliveryAddress2][name_on_bell]": {
                required: "Please enter your name",
                lettersonly: "Only alphabates are allowed",
            },
            "data[DeliveryAddress2][address]": {
                required: "Please enter your are address",
            },
            "data[DeliveryAddress2][city]": {
                required: "Please enter city",
                lettersonly: "Only alphabates are allowed",
            },
            "data[DeliveryAddress2][state]": {
                required: "Please enter state ",
                lettersonly: "Only alphabates are allowed",
            },
            "data[DeliveryAddress2][zipcode]": {
                required: "Please enter zip-code.",
                number: "Only numbers are allowed"
            },
            "data[DeliveryAddress2][phone]": {
                required: "Contact number required",
            },
            "data[DeliveryAddress3][name_on_bell]": {
                required: "Please enter your name",
                lettersonly: "Only alphabates are allowed",
            },
            "data[DeliveryAddress3][address]": {
                required: "Please enter your are address",
            },
            "data[DeliveryAddress3][city]": {
                required: "Please enter city",
                lettersonly: "Only alphabates are allowed",
            },
            "data[DeliveryAddress3][state]": {
                required: "Please enter state ",
                lettersonly: "Only alphabates are allowed",
            },
            "data[DeliveryAddress3][zipcode]": {
                required: "Please enter zip-code.",
                number: "Only numbers are allowed"
            },
            "data[DeliveryAddress3][phone]": {
                required: "Contact number required",
            },
            "data[DeliveryAddress4][name_on_bell]": {
                required: "Please enter your name",
                lettersonly: "Only alphabates are allowed",
            },
            "data[DeliveryAddress4][address]": {
                required: "Please enter your are address",
            },
            "data[DeliveryAddress4][city]": {
                required: "Please enter city",
                lettersonly: "Only alphabates are allowed",
            },
            "data[DeliveryAddress4][state]": {
                required: "Please enter state ",
                lettersonly: "Only alphabates are allowed",
            },
            "data[DeliveryAddress4][zipcode]": {
                required: "Please enter zip-code.",
                number: "Only numbers are allowed"
            },
            "data[DeliveryAddress4][phone]": {
                required: "Contact number required",
            },
        }
    });

    $(document).ready(function () {
        $('#multipleadd').click(function () {
            if ($("#Deliveryaddress").valid()) {
                $('#loading').show();
                $.ajax({
                    type: 'POST',
                    url: '/ajaxMenus/addAddress',
                    data: $('#Deliveryaddress').serialize(),
                    success: function (successResult) {
                        $('#loading').hide();
                        isJson = IsJsonString(successResult);
                        if (isJson) {
                            var obj = jQuery.parseJSON(successResult);
                            if (obj.label1 == "Error") {
                                $("#label1").html("");
                            }
                            if (obj.label2 == "Error") {
                                $("#label2").html("");
                            }
                            if (obj.label3 == "Error") {
                                $("#label3").html("");
                            }
                            if (obj.label4 == "Error") {
                                $("#label4").html("");
                            }
                            if (obj.label5 == "Error") {
                                $("#label5").html("");
                            }
                            $("#errorPop").modal('show');
                            $("#errorPopMsg").html(obj.msg);
                            //$('#flashMsg').html('<div class="alert alert-danger" id="flashMessage"><a href="#" data-dismiss="alert" aria-label="close" title="close" class="pull-right">×</a> ' + obj.msg + '</div>');
                            return false;
                        } else {
                            getdeliveryAddress();
                        }
                    }
                });
            }
        });

        $('#cancelbutton').click(function () {
            getdeliveryAddress();
        });
    });
    function IsJsonString(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }


</script>

