<!-- static-banner -->
<?php echo $this->element('hquser/static_banner'); ?>
<!-- /banner -->
<div class="signup-form">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="common-title clearfix">
                    <span class="yello-dash"></span>
                    <h2><?php echo __('My Profile'); ?></h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php echo $this->Session->flash(); ?>
                <div class="form-bg">
                    <!-- -->
                    <?php
                    echo $this->Form->create('UsersProfile', array('url' => array('controller' => 'hqusers', 'action' => 'myProfile'), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'UsersProfile', "class" => "sign-up"));
                    echo $this->Form->input('User.role_id', array('type' => 'hidden', 'value' => $roleId));
                    echo $this->Form->input('User.is_news_check', array('id' => 'newsNote', 'type' => 'hidden', 'value' => 0));
                    echo $this->Form->input('User.is_email_check', array('id' => 'emailNote', 'type' => 'hidden', 'value' => 0));
                    echo $this->Form->input('User.is_sms_check', array('id' => 'smsNote', 'type' => 'hidden', 'value' => 0));
                    ?>
                    <!-- CONTENT -->
                    <div class="main-form profile-main-form">
                        <div class="form-group">
                            <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/user.png', array('alt' => 'user')) ?></span><label>First Name <sup>*</sup></label></div>
                            <div class="rgt-box">
                                <?php
                                echo $this->Form->input('User.fname', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'Enter Your First Name', 'maxlength' => '20', 'label' => false, 'div' => false));
                                echo $this->Form->error('User.fname');
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/user-fill.png', array('alt' => 'user')) ?></span><label>Last Name <sup>*</sup></label></div>
                            <div class="rgt-box">
                                <?php
                                echo $this->Form->input('User.lname', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => 'Enter Your Last Name', 'maxlength' => '20', 'label' => false, 'div' => false));
                                echo $this->Form->error('User.lname');
                                ?>
                            </div>
                        </div>
                        <div class="form-group twin-block">
                            <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/mobile.png', array('alt' => 'user')) ?></span><label>Mobile Phone <sup>*</sup></label>
                            </div>
                            <div class="rgt-box">
                                <?php echo $this->Form->input('User.country_code_id', array('type' => 'select', 'options' => $countryCode, 'value' => $this->request->data['CountryCode']['id'], 'class' => 'form-control custom-text country-code', 'label' => false, 'div' => false)); ?>
                                <div class="phone-input">
                                    <?php
                                    echo $this->Form->input('User.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control custom-text phone-number', 'placeholder' => 'Mobile Phone ( 111-111-111)', 'label' => false, 'div' => false, 'required' => true));
                                    echo $this->Form->error('User.phone');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/birthday.png', array('alt' => 'user')) ?></span><label>Date of Birth <sup>*</sup></label></div>
                            <div class="rgt-box">
                                <?php
                                echo $this->Form->input('User.dateOfBirth', array('type' => 'text', 'class' => 'form-control custom-text date_select', 'placeholder' => 'Enter your Date of Birth', 'maxlength' => '12', 'label' => false, 'div' => false, 'required' => true, 'readOnly' => true));
                                echo $this->Form->error('User.dateOfBirth');
                                ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/city-state.png', array('alt' => 'user')) ?></span><label>City <sup>*</sup></label></div>
                            <div class="rgt-box city-sel">
                                 <?php echo $this->Form->input('User.city_id', array('type' => 'text', 'class' => 'form-control custom-text', 'label' => false, 'div' => false, 'empty' => 'Select City')); ?>  
                                <?php //echo $this->Form->input('User.city_id', array('type' => 'select', 'options' => @$cities, 'class' => 'form-control custom-text', 'label' => false, 'div' => false, 'empty' => 'Select City')); ?>       
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/city-state.png', array('alt' => 'user')) ?></span><label>State <sup>*</sup></label></div>
                            <div class="rgt-box">
                                <?php echo $this->Form->input('User.state_id', array('type' => 'text', 'class' => 'form-control custom-text', 'label' => false, 'div' => false, 'empty' => "Select State")); ?>       
                                <?php //echo $this->Form->input('User.state_id', array('type' => 'select', 'options' => @$states, 'class' => 'form-control custom-text', 'label' => false, 'div' => false, 'empty' => "Select State")); ?>       
                            </div>
                        </div>

                        

                        <div class="form-group">
                            <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/pincode.png', array('alt' => 'user')) ?></span><label>Zip Code <sup>*</sup></label></div>
                            <div class="rgt-box zip-sel">
                                <?php echo $this->Form->input('User.zip_id', array('type' => 'text', 'class' => 'form-control custom-text', 'label' => false, 'div' => false, 'empty' => 'Select Zip', 'maxlength' => '5')); ?>    
                                <?php //echo $this->Form->input('User.zip_id', array('type' => 'select', 'options' => @$zips, 'class' => 'form-control custom-text', 'label' => false, 'div' => false, 'empty' => 'Select Zip')); ?>       
                            </div>
                        </div>
                    </div>
                    <!-- CONTENT END -->

                    <div class="delivery-address clearfix">
                        <div class="custom-checkbox">
                            <input type="checkbox" id="changepassword"  name="data[User][changepassword]"/>
                            <label for="changepassword">Change Password</label> 
                        </div>
                    </div>

                    <!-- CONTENT -->
                    <div id="change_password_block" class="main-form margin-top35">
                        <div class="form-group">
                            <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/password.png', array('alt' => 'user')) ?></span><label>Old Password <sup>*</sup></label></div>
                            <div class="rgt-box">
                                <?php
                                echo $this->Form->input('User.oldpassword', array('type' => 'password', 'class' => 'form-control custom-text', 'placeholder' => 'Old Password', 'maxlength' => '20', 'label' => false));
                                echo $this->Form->error('User.oldpassword');
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/password.png', array('alt' => 'user')) ?></span><label>New Password <sup>*</sup></label></div>
                            <div class="rgt-box">
                                <?php
                                echo $this->Form->input('User.password', array('type' => 'password', 'class' => 'form-control custom-text', 'placeholder' => 'New Password', 'maxlength' => '20', 'label' => false,'value'=>''));
                                echo $this->Form->error('User.password');
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/password-fill.png', array('alt' => 'user')) ?></span><label>Confirm Password <sup>*</sup></label></div>
                            <div class="rgt-box">
                                <?php
                                echo $this->Form->input('User.password_match', array('type' => 'password', 'class' => 'form-control custom-text', 'placeholder' => 'Confirm Password', 'maxlength' => '20', 'label' => false));
                                echo $this->Form->error('User.password_match');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="submit-btn">
                            <?php
                            echo $this->Form->button('UPDATE', array('type' => 'submit', 'class' => 'btn common-config black-bg'));
                            echo $this->Form->button('CANCEL', array('type' => 'button', 'onclick' => "window.location.href='/hqusers/merchant'", 'class' => 'btn common-config black-bg'));
                            ?>
                        </div>
                    <!-- /CONTENT END -->
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
    $(document).ready(function () {
        $(".phone-number").keypress(function (e) {
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


        $('#UserPassword').css('display', 'none');
        $("#UserPassword").prop('disabled', true);

        $('#change_password_block').css('display', 'none');
        $('#changepassword').on('change', function () {


            if ($(this).prop('checked')) {
                $('#change_password_block').css('display', 'block');
                $('#UserPassword').css('display', 'block');
                $("#UserPassword").prop('disabled', false);
            } else {
                $('#change_password_block').css('display', 'none');
                $('#UserPassword').css('display', 'none');
                $("#UserPassword").prop('disabled', true);
            }

        });
        $('#news').on('click', function () {
            $('#newsNote').val(1);
        });
        $('#email_note').on('change', function () {
            $('#emailNote').val(1);
        });
        $('#sms_note').on('change', function () {
            $('#smsNote').val(1);
        });
        $('.date_select').datepicker({
            dateFormat: 'mm-dd-yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '1950:2015',
        });
        $("#UsersProfile").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'label',
            onkeyup: false,
            rules: {
                "data[User][fname]": {
                    required: true,
                    lettersonly: true,
                },
                "data[User][lname]": {
                    required: false,
                    lettersonly: true,
                },
                "data[User][phone]": {
                    required: true,
                },
                "data[User][password]": {
                    required: true,
                    minlength: 8,
                    maxlength: 20,
                    passw: true,
                },
                "data[User][oldpassword]": {
                    required: true,
                    minlength: 8,
                    maxlength: 20,
                },
                "data[User][password_match]": {
                    required: true,
                    equalTo: "#UserPassword"
                },
                "data[User][sate_id]": {
                    required: true,
                },
                "data[User][city_id]": {
                    required: true
                },
                "data[User][zip_id]": {
                    required: true
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
                "data[User][phone]": {
                    required: "Contact number required",
                },
                "data[User][password]": {
                    required: "Please enter your password",
                    minlength: "Password must be at least 8 characters",
                    maxlength: "Please enter no more than 20 characters",
                    passw: "Atleast one digit, one upper and one lower case letter"
                },
                "data[User][oldpassword]": {
                    required: "Please enter your old password",
                    minlength: "Please enter at least 8 characters",
                    maxlength: "Please enter no more than 20 characters",
                },
                "data[User][password_match]": {
                    required: "Please enter your password again",
                    equalTo: "Password not matched",
                }, "data[User][state_id]": {
                   required: "Please select State"
                }, "data[User][city_id]": {
                    required: "Please enter City"
                }, "data[User][zip_id]": {
                    required: "Please enter Zipcode"
                },
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
//        jQuery(document).on('change', '#UserStateId', function () {
//            var state_id = jQuery(this).val();
//            jQuery.post("/hqusers/city", {'state_id': state_id}, function (data) {
//                $(".city-sel").html(data);
//            });
//        });
//        jQuery(document).on('change', '#UserCityId', function () {
//            var state_id = jQuery("#UserStateId").val();
//            var city_id = jQuery(this).val();
//            jQuery.post("/hqusers/zip", {'state_id': state_id, 'city_id': city_id}, function (data) {
//                $(".zip-sel").html(data);
//            });
//        });
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