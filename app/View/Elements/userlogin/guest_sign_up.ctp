<div class="modal fade" id="guest-sign-up-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <?php if (DESIGN == 2) { ?>
        <div class="modal-dialog chole-login-popup theme-border-1 clearfix">
            <h2>Guest Login</h2>
            <button type="button" class="close" data-dismiss="modal">×</button>
            <?php echo $this->Form->create('User', array('id' => 'GuestUser', 'class' => "sign-up", 'url' => array('controller' => 'users', 'action' => 'guestUserSignUp'), 'autocomplete' => 'off')); ?>
            <div class="form-group clearfix">
                <label class="control-label col-sm-4" for="email">Name:</label>
                <div class="col-sm-8">
                    <?php echo $this->Form->input('name', array('type' => 'text', "placeholder" => "Enter Your Name", 'autofocus' => true, 'label' => false, 'maxlength' => '20', "class" => "form-control", 'div' => false)); ?>
                </div>
            </div>
            <div class="form-group clearfix">
                <label class="control-label col-sm-4" for="email">Email:</label>
                <div class="col-sm-8">
                    <?php echo $this->Form->input('User.email', array('type' => 'email', "placeholder" => "Enter Your Email", 'autofocus' => true, 'label' => false, 'maxlength' => '50', "class" => "form-control", 'div' => false, 'value' => @$userEmail)); ?>
                </div>
            </div>
            <div class="form-group clearfix gl-mobile-wrap">
                <label class="control-label col-sm-4" for="email">Phone:</label>
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-sm-4">
                            <?php echo $this->Form->input('country_code_id', array('type' => 'select', 'options' => @$countryCode, 'class' => 'form-control country-code', 'label' => false, 'div' => false)); ?>
                        </div>
                        <div class="col-sm-8">
                            <?php echo $this->Form->input('phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control phone', 'placeholder' => 'Mobile Phone', 'label' => false, 'div' => false, 'required' => true));
                            ?> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group chole-lf-btm theme-border-1 clearfix">
                <div class="col-sm-5 pull-right">
                    <?php echo $this->Form->input('Proceed as Guest', array('type' => 'button', 'id' => 'btnGuestUser', 'label' => false, 'div' => false, 'class' => 'theme-bg-1')); ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    <?php } else { ?>
        <div class="modal-dialog clearfix">
            <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                <span> <img src="/img/close.png" alt="#close"></span>
            </button>
            <?php if (DESIGN == 3) { ?>
                <a href="#" data-toggle="modal" data-target="#login-modal">SIGN IN</a>
                <h3>GUEST LOGIN</h3>
            <?php } ?>
            <?php echo $this->Form->create('User', array('id' => 'GuestUser', 'class' => "sign-up", 'url' => array('controller' => 'users', 'action' => 'guestUserSignUp'), 'autocomplete' => 'off')); ?>
            <div class="input-wrap clearfix">
                <div class="input-fields">
                    <?php echo $this->Form->input('name', array('type' => 'text', "placeholder" => "Enter Your Name", 'autofocus' => true, 'label' => false, 'maxlength' => '20', "class" => "sign-input", 'div' => false)); ?>
                    <?php
                    echo $this->Form->input('country_code_id', array('type' => 'select', 'options' => @$countryCode, 'class' => 'sign-input country-code', 'label' => false, 'div' => false));
                    echo $this->Form->input('phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'sign-input phone', 'placeholder' => 'Mobile Phone', 'label' => false, 'div' => false, 'required' => true));
                    ?> 
                    <?php echo $this->Form->input('email', array('type' => 'email', "placeholder" => "Enter Your Email", 'autofocus' => true, 'label' => false, 'maxlength' => '50', "class" => "sign-input", 'div' => false)); ?>
                </div>
                <div class="sign-up-btn">  
                    <?php echo $this->Form->input('SUBMIT', array('type' => 'button', "class" => "custom-btn theme-bg-1", 'id' => 'btnGuestUser', 'label' => false, 'div' => false)); ?>
                </div>
            </div>
            <div class="left error" style="display:none;" id="guestSignUpError">Something went wrong!</div>
            <div class="reg-btn">
                <?php echo $this->Html->link('Sign Up', array('controller' => 'users', 'action' => 'registration'), array('escape' => false, 'class' => "sign-up-custom theme-bg-2")); ?>            
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    <?php } ?>
</div>

<script>
    $(document).ready(function () {
        $(".phone").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });
        $("[data-mask='mobileNo']").mask("(999) 999-9999");
        $("#GuestUser").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[User][name]": {
                    required: true,
                    lettersonly: true
                },
                "data[User][email]": {
                    required: true,
                    email: true,
                    minlength: 10,
                    maxlength: 50,
                    remote: "/users/checkStoreEndUserEmail"
                },
                "data[User][country_code_id]": {
                    required: true
                },
                "data[User][phone]": {
                    required: true
                }
            },
            messages: {
                "data[User][name]": {
                    required: "Please enter name",
                },
                "data[User][email]": {
                    required: "Please enter email",
                    email: "Please enter valid email",
                    remote: "Email already exists"
                }
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            }
        });

        $('#btnGuestUser').click(function (e) {
            if ($("#GuestUser").valid()) {
                var name = $.trim($('input#UserName').val());
                var email = $.trim($('input#UserEmail').val());
                var countryCode = $('select#UserCountryCodeId').val();
                var userPhone = $('input#UserPhone').val();
                $.ajax({
                    url: "<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'guestUserSignUp')); ?>",
                    type: "Post",
                    dataType: 'html',
                    data: {name: name, countryCode: countryCode, userPhone: userPhone, email: email},
                    success: function (successResult) {
                        response = jQuery.parseJSON(successResult);
                        if (response.status == 1) {
                            $("#guest-sign-up-modal").modal('hide');
                            window.location = window.location;
                        } else {
                            $('#guestSignUpError').show();
                        }
                    }
                });
                e.preventDefault();
            }
        });
    });
</script>