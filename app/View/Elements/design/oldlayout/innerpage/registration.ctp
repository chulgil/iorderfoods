<!--<script src="https://www.google.com/recaptcha/api.js"></script>-->
<div class="content single-frame">
    <div class="wrap">
        <?php //echo $this->Session->flash(); ?>
        <?php
        echo $this->Form->create('Users', array('inputDefaults' => array('autocomplete' => 'off'), 'id' => 'UsersRegistration'));
        echo $this->Form->input('User.role_id', array('type' => 'hidden', 'value' => 4));
        ?>
        <div class="clearfix">
            <section class="form-layout sign-up registration-from">
                <h2> <span>Sign-Up</span></h2>    	
                <ul class="clearfix">


                    <li>
                        <span class="title"><label>First Name <em>*</em></label></span>
                        <div class="title-box"><?php
                            echo $this->Form->input('User.fname', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your First Name', 'maxlength' => '20', 'label' => false, 'div' => false));
                            echo $this->Form->error('User.fname');
                            ?></div>
                    </li>

                    <li>
                        <span class="title"><label>Last Name <em>*</em> </label></span>
                        <div class="title-box"><?php
                            echo $this->Form->input('User.lname', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your Last Name', 'maxlength' => '20', 'label' => false, 'div' => false));
                            echo $this->Form->error('User.lname');
                            ?></div>
                    </li>

                    <li>
                        <span class="title"><label>Email <em>*</em></label></span>
                        <div class="title-box"><?php
                            echo $this->Form->input('User.email', array('id' => 'oldemail', 'type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your Email', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off'));
                            echo $this->Form->error('User.email');
                            ?></div>
                    </li>

                    <li>
                        <span class="title"><label>Password <em>*</em></label></span>
                        <div class="title-box"><?php
                            echo $this->Form->input('User.password', array('type' => 'password', 'class' => 'inbox', 'placeholder' => 'Enter Your password', 'maxlength' => '20', 'label' => false, 'div' => false, 'required' => true, 'id' => 'signup_password', 'autocomplete' => 'off'));
                            echo $this->Form->error('User.password');
                            ?></div>
                    </li>

                    <li>
                        <span class="title confirm-password"><label>Password Confirmation<em>*</em></label></span>
                        <div class="title-box"><?php
                            echo $this->Form->input('User.password_match', array('type' => 'password', 'class' => 'inbox', 'placeholder' => 'Confirm Password', 'maxlength' => '20', 'label' => false, 'div' => false));
                            echo $this->Form->error('User.password_match');
                            ?></div>
                    </li>

                    <li>
                        <span class="title"><label>Mobile Phone<em>*</em></label></span>
                        <div class="title-box">
<?php echo $this->Form->input('User.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'inbox country-code', 'label' => false, 'div' => false)); ?>       
<?php
echo $this->Form->input('User.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'inbox phone', 'placeholder' => 'Mobile Phone', 'label' => false, 'div' => false, 'required' => true));
echo $this->Form->error('User.phone');
?>
                            <span style='margin:2px 0px 0px 80px;font-size:12px;'>(eg. 111-111-1111)</span></div>
                    </li>
                    <li>
                        <span class="title"><label>DOB</label></span>
                        <div class="title-box">
<?php
echo $this->Form->input('User.dateOfBirth', array('type' => 'text', 'class' => 'inbox date_select', 'placeholder' => 'Date of Birth', 'maxlength' => '12', 'label' => false, 'div' => false, 'required' => true, 'readOnly' => true));
echo $this->Form->error('User.dateOfBirth');
?>
                        </div>
                    </li>
                    <li>
                        <span class="title"><label>City<em>*</em></label></span>
                        <div class="title-box">
                            <?php echo $this->Form->input('User.city_id', array('type' => 'text', 'class' => 'user-detail city-sel inbox', 'maxlength' => '20', 'label' => false, 'div' => false, 'placeholder' => 'Enter City')); ?>
                        </div>
                    </li>

                    <li>
                        <span class="title"><label>State<em>*</em></label></span>
                        <div class="title-box">
<?php echo $this->Form->input('User.state_id', array('type' => 'text', 'class' => 'user-detail inbox', 'label' => false, 'div' => false, 'placeholder' => 'Select State')); ?>       
                        </div>
                    </li>



                    <li>
                        <span class="title"><label>Zip<em>*</em></label></span>
                        <div class="title-box">
<?php echo $this->Form->input('User.zip_id', array('type' => 'text', 'class' => 'user-detail zip-sel inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter Zip', 'maxlength' => '5')); ?>   
                        </div>
                    </li>



                    <li>
                        <span class="title blank">&nbsp;</span>
                        <div class="title-box">
                            <div class="password-remember" style="padding-top:0;"><input type="checkbox" id="privacy_policy"  name="data[User][is_privacypolicy]" checked /> <label for="privacy_policy">Agree to our <a href="javascript:void(0)">Terms of Use</a> &amp; <a href="javascript:void(0)">Privacy Policy</a> ?</label><label id="data[User][is_privacypolicy]-error" class="error" for="data[User][is_privacypolicy]"></label></div>
                        </div>
                    </li>

                    <!--                    <li>
                                            <span class="title blank">&nbsp;</span>
                                            <div class="title-box"><div class="g-recaptcha" data-sitekey="<?php echo $googleSiteKey; ?>"></div></div>
                                        </li>-->

                    <li>
                        <span class="title blank">&nbsp;</span>
                        <div class="title-box"><?php
                            echo $this->Form->button('Sign Up', array('type' => 'submit', 'class' => 'btn green-btn'));
                            echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/login/'", 'class' => 'btn green-btn'));
?>
                        </div>
                    </li>
                </ul>
            </section>
        </div>
<?php echo $this->Form->end(); ?>
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

        $('.date_select').datepicker({
            dateFormat: 'mm-dd-yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '1950:2017',
            maxDate: new Date()
        });

        jQuery.validator.addMethod("lettersonly", function (value, element)
        {
            return this.optional(element) || /^[a-z," "]+$/i.test(value);
        }, "Letters and spaces only please");

        $("#UsersRegistration").validate({
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
                }, "data[User][state_id]": {
                    required: true,
                }, "data[User][city_id]": {
                    required: true,
                }, "data[User][zip_id]": {
                    required: true,
                    number: true,
                    minlength:5,
                    maxlength:5
                },
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
            }
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
            source: "<?php echo $this->Html->url(array('controller' => 'Hqusers', 'action' => 'getState')); ?>",
            minLength: 2,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        })
    });
</script>