<!--<script src="https://www.google.com/recaptcha/api.js"></script>-->
<div class="title-bar">Sign-Up</div>
<div class="main-container ">
    <div class="ext-menu-title">
        <h4>Sign-Up</h4>
    </div>
    <div class="inner-wrap  profile">
        <?php //echo $this->Session->flash(); ?>
        <div class="form-section user-form-section">
            <?php
            echo $this->Form->create('Users', array('inputDefaults' => array('autocomplete' => 'off'), 'id' => 'UsersRegistration', 'class' => 'profile-detail'));
            echo $this->Form->input('User.role_id', array('type' => 'hidden', 'value' => 4));
            ?>
            <div class="form-space">
                <div class="profile-input form-group clearfix">
                    <label class="control-label col-lg-3">First Name<em>*</em></label>
                    <div class="col-lg-9">
                        <?php echo $this->Form->input('User.fname', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'Enter Your First Name', 'maxlength' => '20', 'label' => false, 'div' => false)); ?>
                    </div>
                </div>
                <div class="profile-input form-group clearfix">
                    <label class="control-label col-lg-3">Last Name<em>*</em></label>
                    <div class="col-lg-9">
                        <?php echo $this->Form->input('User.lname', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'Enter Your Last Name', 'maxlength' => '20', 'label' => false, 'div' => false)); ?>
                    </div>
                </div>
                <div class="profile-input form-group clearfix">
                    <label class="control-label col-lg-3">Email<em>*</em></label>
                    <div class="col-lg-9">
                        <?php echo $this->Form->input('User.email', array('id' => 'oldemail', 'type' => 'user-detail', 'class' => 'user-detail', 'placeholder' => 'Enter Your Email', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off')); ?>
                    </div>
                </div>
                <div class="profile-input form-group clearfix">
                    <label class="control-label col-lg-3">Password<em>*</em></label>
                    <div class="col-lg-9">
                        <?php echo $this->Form->input('User.password', array('type' => 'password', 'class' => 'user-detail', 'placeholder' => 'Enter Your password', 'maxlength' => '20', 'label' => false, 'div' => false, 'required' => true, 'id' => 'signup_password', 'autocomplete' => 'off')); ?>
                    </div>
                </div>

                <div class="profile-input form-group clearfix">
                    <label class="control-label col-lg-3">Password Confirmation<em>*</em></label>
                    <div class="col-lg-9">
                        <?php echo $this->Form->input('User.password_match', array('type' => 'password', 'class' => 'user-detail', 'placeholder' => 'Confirm Password', 'maxlength' => '20', 'label' => false, 'div' => false)); ?>
                    </div>
                </div>
                <div class="profile-input form-group clearfix">
                    <label class="control-label col-lg-3">Mobile Phone<em>*</em></label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-2 col-xs-3"><!-- SlectBox -->
                                <?php echo $this->Form->input('User.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'country-code user-detail', 'label' => false, 'div' => false)); ?>
                            </div>
                            <div class="col-lg-10 col-xs-9">
                                <?php echo $this->Form->input('User.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'user-detail phone', 'placeholder' => 'Mobile Phone', 'label' => false, 'div' => false, 'required' => true));
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="profile-input clearfix">
                    <label class="control-label col-lg-3">DOB<em>*</em></label>
                    <div class="col-lg-9">
                        <?php echo $this->Form->input('User.dateOfBirth', array('type' => 'text', 'class' => 'user-detail date_select', 'placeholder' => 'Date of Birth', 'maxlength' => '12', 'label' => false, 'div' => false, 'required' => true, 'readOnly' => true)); ?>
                    </div>
                </div>
                <div class="profile-input clearfix">
                    <label class="control-label col-lg-3">City<em>*</em></label>
                    <div class="col-lg-9 city-sel">
                        <?php echo $this->Form->input('User.city_id', array('type' => 'text', 'class' => 'user-detail', 'maxlength' => '20', 'label' => false, 'div' => false, 'placeholder' => 'Enter City')); ?>       
                    </div>
                </div>
                <div class="profile-input clearfix">
                    <label class="control-label col-lg-3">State<em>*</em></label>
                    <div class="col-lg-9">
                        <?php echo $this->Form->input('User.state_id', array('type' => 'text', 'class' => 'user-detail', 'label' => false, 'div' => false, 'placeholder' => 'Select State')); ?>       
                    </div>
                </div>

                <div class="profile-input clearfix">
                    <label class="control-label col-lg-3">Zip<em>*</em></label>
                    <div class="col-lg-9 zip-sel">
                        <?php echo $this->Form->input('User.zip_id', array('type' => 'text', 'class' => 'user-detail', 'label' => false, 'div' => false, 'placeholder' => 'Enter Zip', 'maxlength' => '5')); ?>       
                    </div>
                </div>
                <div class="profile-check-list clearfix">
                    <label class="control-label col-lg-3">&nbsp;</label>
                    <div class="col-lg-9">
                        <ul class="profile-notifications">
                            <li>
                                <span>
                                    <input type="checkbox" id="privacy_policy"  name="data[User][is_privacypolicy]" checked />
                                    <label for="privacy_policy" class="privacy-txt">Agree to our <a href="javascript:void(0)" class="termAndPolicy" data-name="Term">Terms of Use</a> &amp; <a href="javascript:void(0)" class="termAndPolicy" data-name="Policy">Privacy Policy</a> ?</label>
                                    <span id="data[User][is_privacypolicy]-error" class="error" for="data[User][is_privacypolicy]"></span>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
            <div class="profile-btn-section clearfix">
                <div class="row">
                    <?php if (DESIGN == 3) { ?>
                        <div class="col-sm-6 col-xs-6">
                            <?php echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/login'", 'class' => 'p-cancle')); ?>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                            <?php echo $this->Form->button('Sign Up', array('type' => 'submit', 'class' => 'p-save theme-bg-1')); ?>
                        </div>
                    <?php } else { ?>
                        <div class="col-sm-6 col-xs-6">
                            <?php echo $this->Form->button('Sign Up', array('type' => 'submit', 'class' => 'p-save theme-bg-1')); ?>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                            <?php echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/login'", 'class' => 'p-cancle theme-bg-2')); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>

<script>
    $(window).load(function () {
        // $('#oldemail').val('');
        // $('#signup_password').val('');
    });
    $(document).ready(function () {
        $(".phone").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });
        $("[data-mask='mobileNo']").mask("(999) 999-9999");


        jQuery.validator.addMethod("passw", function (pass, element) {
            pass = pass.replace(/\s+/g, "");
            return this.optional(element) || pass.length > 7 &&
                    pass.match(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[A-Za-z\d$@$!%*#?& ]{8,}$/);
        }, "Atleast one digit, one upper and one lower case letter");

        jQuery.validator.addMethod("lettersonly", function (value, element)
        {
            return this.optional(element) || /^[a-z," "]+$/i.test(value);
        }, "Letters and spaces only please");


        $('.date_select').datepicker({
            dateFormat: 'mm-dd-yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '1950:2017',
            maxDate: new Date()
        });
        $("#UsersRegistration").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[User][fname]": {
                    required: true,
                    lettersonly: true,
                },
                "data[User][lname]": {
                    required: true,
                    lettersonly: true,
                },
                "data[User][email]": {
                    required: true,
                    email: true,
                    remote: "/users/checkStoreEndUserEmail"
                },
                "data[User][password]": {
                    required: true,
                    minlength: 8,
                    maxlength: 20,
                    passw: true,
                },
                "data[User][password_match]": {
                    required: true,
                    equalTo: "#signup_password",
                },
                "data[User][phone]": {
                    required: true
                }, "data[User][dateOfBirth]": {
                    required: false,
                }, "data[User][is_privacypolicy]": {
                    required: true,
                },
                "data[User][state_id]": {
                    required: true
                },
                "data[User][city_id]": {
                    required: true
                },
                "data[User][zip_id]": {
                    required: true,
                    number: true,
                    minlength: 5,
                    maxlength: 5
                }
            },
            messages: {
                "data[User][fname]": {
                    required: "Please enter your first name",
                    lettersonly: "Only alphabates are allowed",
                },
                "data[User][lname]": {
                    required: "Please enter your last name",
                    lettersonly: "Only alphabates are allowed",
                },
                "data[User][email]": {
                    required: "Please enter your email",
                    email: "Please enter valid email",
                    remote: "Email already exists",
                },
                "data[User][password]": {
                    required: "Please enter your password",
                    minlength: "Password must be at least 8 characters",
                    maxlength: "Please enter no more than 20 characters",
                    passw: "Atleast one digit, one upper and one lower case letter"
                },
                "data[User][password_match]": {
                    required: "Please enter your password again",
                    equalTo: "Password not matched",
                },
                "data[User][phone]": {
                    required: "Contact number required",
                },
                "data[User][is_privacypolicy]": {
                    required: "Please agree to our Terms of use & Privacy policy.",
                }, "data[User][state_id]": {
                    required: "Please select State"
                }, "data[User][city_id]": {
                    required: "Please enter City"
                }, "data[User][zip_id]": {
                    required: "Please enter Zipcode",
                    number: "Only numbers are allowed"
                },
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
        //        jQuery(document).on('change', '#UserStateId', function () {
        //            var state_id = jQuery(this).val();
        //            jQuery.post("/users/city", {'state_id': state_id}, function (data) {
        //                $(".city-sel").html(data);
        //            });
        //        });
        //        jQuery(document).on('change', '#UserCityId', function () {
        //            var state_id = jQuery("#UserStateId").val();
        //            var city_id = jQuery(this).val();
        //            jQuery.post("/users/zip", {'state_id': state_id, 'city_id': city_id}, function (data) {
        //                $(".zip-sel").html(data);
        //            });
        //        });
    });
    $(function () {
        $("#UserFname").focus();
    });
    $(document).ready(function () {
        $("#UserStateId").autocomplete({
            source: "<?php echo $this->Html->url(array('controller' => 'hqusers', 'action' => 'getState')); ?>",
            minLength: 1,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        });
    });
</script>