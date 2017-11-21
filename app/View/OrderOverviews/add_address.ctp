<div class="modal-dialog inner-wrap clearfix">
    <div class="common-title">
        <h3>Add Address</h3>
    </div>
    <button data-dismiss="modal" class="close" type="button">×</button>
    <?php echo $this->Form->create('DeliveryAddress', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'addDeliveryaddress')); ?>
    <div class="form-section editadd">
        <span id="flashError"></span>
        <?php if ($label1 != 1) { ?>
            <div id="label1">
                <div class="profile-input popup-checkbox clearfix">
                    <input type="checkbox" id="home_address"  name="data[DeliveryAddress][label1]"  checked/> <label for="home_address">Home Address</label>
                </div>
                <div id='home_block'>

                    <div class="profile-input clearfix">
                        <label>Name<em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress.name_on_bell', array('type' => 'text', 'class' => 'user-detail', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Address<em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress.address', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false)); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>City<em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress.city', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false)); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>State<em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress.state', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Zip-Code<em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress.zipcode', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true)); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Phone Number<em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <div class="col-1"><?php echo $this->Form->input('DeliveryAddress.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'user-detail country-code', 'label' => false, 'div' => false)); ?></div>
                                <div class="col-2"><?php echo $this->Form->input('DeliveryAddress.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'user-detail phone-number', 'placeholder' => 'Phone Number', 'label' => false, 'div' => false, 'required' => true)); ?></div>
                                <span>(eg. 111-111-1111)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if ($label2 != 1) { ?>
            <div id="label2">
                <div class="profile-input popup-checkbox clearfix">
                    <input type="checkbox" id="work_address"  name="data[DeliveryAddress1][label2]"/><label for="work_address">Work Address</label>
                </div>
                <div id='work_block'>
                    <div class="profile-input clearfix">
                        <label>Name <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress1.name_on_bell', array('type' => 'text', 'class' => 'user-detail', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Address <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress1.address', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false)); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>City <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress1.city', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false)); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>State <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress1.state', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Zip-Code <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress1.zipcode', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true)); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Phone Number<em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <div class="col-1"><?php echo $this->Form->input('DeliveryAddress1.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'user-detail country-code', 'label' => false, 'div' => false)); ?></div>
                                <div class="col-2"><?php echo $this->Form->input('DeliveryAddress1.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'user-detail phone-number', 'placeholder' => 'Phone Number', 'label' => false, 'div' => false, 'required' => true)); ?></div>
                                <span>(eg. 111-111-1111)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if ($label3 != 1) { ?>
            <div id="label3">
                <div class="profile-input popup-checkbox clearfix">
                    <input type="checkbox" id="other_address"  name="data[DeliveryAddress2][label3]"  /> <label for="other_address">Other Address</label>
                </div>
                <div id='other_block'>
                    <div class="profile-input clearfix">
                        <label>Name <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress2.name_on_bell', array('type' => 'text', 'class' => 'user-detail', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Address <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress2.address', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false)); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>City <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress2.city', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false)); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>State <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress2.state', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Zip-Code <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress2.zipcode', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true)); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Phone Number<em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <div class="col-1"><?php echo $this->Form->input('DeliveryAddress2.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'user-detail country-code', 'label' => false, 'div' => false)); ?></div>
                                <div class="col-2"><?php echo $this->Form->input('DeliveryAddress2.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'user-detail phone-number', 'placeholder' => 'Phone Number', 'label' => false, 'div' => false, 'required' => true)); ?></div>
                                <span>(eg. 111-111-1111)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if ($label4 != 1) { ?>
            <div id="label4">
                <div class="profile-input popup-checkbox clearfix">
                    <input type="checkbox" id="address4"  name="data[DeliveryAddress3][label4]"  /> <label for="address4">Address 4</label>
                </div>
                <div id='address4_block'>
                    <div class="profile-input clearfix">
                        <label>Name <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress3.name_on_bell', array('type' => 'text', 'class' => 'user-detail', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Address <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress3.address', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false)); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>City <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress3.city', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false)); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>State <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress3.state', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Zip-Code <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress3.zipcode', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true)); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Phone Number<em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <div class="col-1"><?php echo $this->Form->input('DeliveryAddress3.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'user-detail country-code', 'label' => false, 'div' => false)); ?></div>
                                <div class="col-2"><?php echo $this->Form->input('DeliveryAddress3.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'user-detail phone-number', 'placeholder' => 'Phone Number', 'label' => false, 'div' => false, 'required' => true)); ?>
                                    <span>(eg. 111-111-1111)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if ($label5 != 1) { ?>
            <div id="label5">
                <div class="profile-input popup-checkbox clearfix">
                    <input type="checkbox" id="address5"  name="data[DeliveryAddress4][label5]"  /> <label for="address5">Address 5</label>
                </div>
                <div id='address5_block'>
                    <div class="profile-input clearfix">
                        <label>Name <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress4.name_on_bell', array('type' => 'text', 'class' => 'user-detail', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Address <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress4.address', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false)); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>City <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->inp1ut('DeliveryAddress4.city', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false)); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>State <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress4.state', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Zip-Code <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php echo $this->Form->input('DeliveryAddress4.zipcode', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true)); ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Phone Number<em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <div class="col-1"><?php echo $this->Form->input('DeliveryAddress4.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'user-detail country-code', 'label' => false, 'div' => false)); ?></div>
                                <div class="col-2"><?php echo $this->Form->input('DeliveryAddress4.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'user-detail phone-number', 'placeholder' => 'Phone Number', 'label' => false, 'div' => false, 'required' => true)); ?></div>
                                <span>(eg. 111-111-1111)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

    </div>
    <div class="confirm">
        <div class="submit">
            <?php if (DESIGN == 3) { ?>
            <?php
            echo $this->Form->button('Cancel', array('type' => 'button', 'class' => 'closeModal cont-btn p-cancle'));
            echo $this->Form->button('Submit', array('type' => 'submit', 'class' => 'submitAddAddress cont-btn p-save theme-bg-1'));
            ?>
            <?php }else{?>
            <?php
            echo $this->Form->button('Submit', array('type' => 'submit', 'class' => 'submitAddAddress cont-btn p-save theme-bg-1'));
            echo $this->Form->button('Cancel', array('type' => 'button', 'class' => 'closeModal cont-btn p-cancle theme-bg-2'));
            ?>
            <?php }?>
            
        </div>
    </div>
    <?php echo $this->Form->end(); ?>
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
        $("#addDeliveryaddress").validate({
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
                    maxlength: 6
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
                    maxlength: 6
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
                    maxlength: 6
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
                    maxlength: 6
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
                    maxlength: 6
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
                    required: "Please enter your are address"
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
                    required: "Please enter your are address"
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
                    required: "Please enter your are address"
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
                    required: "Please enter your are address"
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
                    required: "Please enter your are address"
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
        $(".submitAddAddress").on('click', function (e) {
            if ($("#addDeliveryaddress").valid()) {
                var formData = $("#addDeliveryaddress").serialize();
                $.ajax({
                    type: 'post',
                    url: "<?php echo $this->Html->url(array('controller' => 'orderOverviews', 'action' => 'ajaxAddressAdd')); ?>",
                    data: {'formData': formData},
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
                            //$('#flashError').html('<div class="message message-success alert alert-danger" id="flashMessage"><a href="#" data-dismiss="alert" aria-label="close" title="close" class="pull-right">×</a> ' + obj.msg + '</div>');
                            return false;
                        } else {
                            if (successResult) {
                                $('#deliveryAddress').html(successResult);
                                $('#address-modal').modal('hide');
                            } else {
                                $('#address-modal').modal('hide');
                            }
                        }
                    }
                });
            }
            e.preventDefault();
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

