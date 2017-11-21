<?php if (DESIGN == 2) { ?>
    <div class="modal-dialog chole-login-popup theme-border-1 clearfix">
        <h2>Login</h2>
        <button type="button" class="close" data-dismiss="modal">×</button>
        <?php echo $this->Form->create('User', array('id' => 'chkOrderUserLogin', 'class' => "sign-up", 'url' => array('controller' => 'users', 'action' => 'popuplogin'), 'autocomplete' => 'off')); ?>
        <div class="form-group clearfix">
            <label class="control-label col-sm-4" for="email">Email:</label>
            <div class="col-sm-8">
                <?php echo $this->Form->input('User.email', array('type' => 'email', "placeholder" => "Enter Your Email", 'autofocus' => true, 'label' => false, 'maxlength' => '50', "class" => "form-control", 'div' => false, 'id' => 'UserLoginEmail', 'value' => @$userEmail)); ?>
            </div>
        </div>
        <div class="form-group clearfix">
            <label class="control-label col-sm-4" for="pwd">Password:</label>
            <div class="col-sm-8">
                <?php echo $this->Form->input('User.password', array("placeholder" => "Enter Your Password", 'label' => false, 'type' => 'password', 'div' => false, 'maxlength' => '20', "class" => "form-control", 'value' => @$userPassword)); ?>
            </div>
        </div>
        <div class="form-group clearfix margin-top-20">
            <div class="col-sm-5">
                <?php
                $chek = '';
                if (!empty($rem)) {
                    $chek = 'checked';
                }
                ?>
                <div class="checkbox">
                    <label for="RememberMe">Remember me</label>
                    <input type="checkbox" id="RememberMe"  name="data[User][remember]" <?php echo $chek; ?>/>
                </div>
            </div>
            <div class="col-sm-5 pull-right text-right">
                <?php echo $this->Html->link('Forgot Password?', array('controller' => 'users', 'action' => 'forgetPassword'), array('class' => 'link-forgot-password')); ?>
            </div>
            <div class="col-sm-12">
                <div class="left error" style="display:none;" id="poperror">Invalid email or password, please try again</div>
            </div>
        </div>
        <div class="form-group chole-lf-btm theme-border-1 clearfix">
            <div class="col-sm-5">
                <?php echo $this->Form->input('SUBMIT', array('type' => 'button', 'id' => 'btnUserLogin', 'label' => false, 'div' => false, 'class' => 'theme-bg-1')); ?>
            </div>
            <div class="col-sm-5 pull-right">
                <button type="button" class="btn btn-default guest-login-popup" data-toggle="modal">Proceed as Guest</button>
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
            <a href="javascript:void(0);" class="guest-login-popup">PROCEED AS GUEST</a>
            <h3>SIGN IN</h3>
        <?php } ?>
        <?php echo $this->Form->create('User', array('id' => 'chkOrderUserLogin', 'class' => "sign-up", 'url' => array('controller' => 'users', 'action' => 'popuplogin'), 'autocomplete' => 'off')); ?>
        <div class="input-wrap clearfix">
            <div class="input-fields">
                <?php echo $this->Form->input('User.email', array('type' => 'email', "placeholder" => "Enter Your Email", 'autofocus' => true, 'label' => false, 'maxlength' => '50', "class" => "sign-input", 'div' => false, 'id' => 'UserLoginEmail', 'value' => @$userEmail)); ?>
                <?php echo $this->Form->input('User.password', array("placeholder" => "Enter Your Password", 'label' => false, 'type' => 'password', 'div' => false, 'maxlength' => '20', "class" => "sign-input", 'value' => @$userPassword)); ?>
            </div>
            <div class="sign-up-btn">
                <?php echo $this->Form->input('SUBMIT', array('type' => 'button', "class" => "custom-btn theme-bg-1", 'id' => 'btnUserLogin', 'label' => false, 'div' => false)); ?>
            </div>
        </div>
        <p><?php
            $chek = '';
            if (!empty($rem)) {
                $chek = 'checked';
            }
            ?>
            <input type="checkbox" id="RememberMe"  name="data[User][remember]" <?php echo $chek; ?>/> <label for="RememberMe">Remember me</label>
            | <?php echo $this->Html->link('Forgot Password?', array('controller' => 'users', 'action' => 'forgetPassword'), array('class'=>'txt-forgot-pass')); ?></p>
        <div class="left error" style="display:none;" id="poperror">Invalid email or password, please try again</div>
        <div class="reg-btn">
            <?php echo $this->Html->link('Sign Up', array('controller' => 'users', 'action' => 'registration'), array('escape' => false, 'class' => "sign-up-custom theme-bg-2")); ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
<?php } ?>
<script>
    $("#chkOrderUserLogin").validate({
        debug: false,
        errorClass: "error",
        errorElement: 'span',
        onkeyup: false,
        rules: {
            "data[User][email]": {
                required: true,
                email: true,
                minlength: 10,
                maxlength: 50,
            },
            "data[User][password]": {
                required: true,
            },
        },
        messages: {
            "data[User][email]": {
                required: "Please enter email",
                email: "Please enter valid email"
            },
            "data[User][password]": {
                required: "Please enter password",
            }
        }, highlight: function (element, errorClass) {
            $(element).removeClass(errorClass);
        }
    });

    $(document).ready(function () {
        $('#btnUserLogin').click(function (e) {
            if ($("#chkOrderUserLogin").valid()) {
                var remember = 0;
                var email = $.trim($("#UserLoginEmail").val());//$('#UserEmail').val();
                var password = $('input#UserPassword').val();
                if ($("#RememberMe").prop("checked")) {
                    remember = 1;
                }
                $.ajax({
                    url: "<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'popuplogin')); ?>",
                    type: "Post",
                    dataType: 'html',
                    data: {email: email, password: password, remember: remember},
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
                    success: function (successResult) {
                        response = jQuery.parseJSON(successResult);
                        if (response.status == 1) {
                            $("#login-modal").modal('hide');
                            window.location = window.location;
                        } else {
                            $('#poperror').show();
                        }
                    },
                    complete: function () {
                        $.unblockUI();
                    }
                });
                e.preventDefault();
            }
        });
    });

</script>