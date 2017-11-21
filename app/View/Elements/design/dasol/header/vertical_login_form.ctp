<?php echo $this->Form->create('User', array('id' => 'chkOrderUserLogin', 'class' => "sign-up", 'url' => array('controller' => 'users', 'action' => 'popuplogin'), 'autocomplete' => 'off')); ?>
<div class="form-group clearfix">
    <div class="col-sm-12 pull-right text-right">
        <?php echo $this->Html->link('Forgot Password?', array('controller' => 'users', 'action' => 'forgetPassword'), array('class' => 'link-forgot-password')); ?>
    </div>
</div>
<div class="form-group clearfix">
    <div class="col-sm-12">
        <?php echo $this->Form->input('User.email', array('type' => 'email', "placeholder" => "Enter Your Email", 'autofocus' => true, 'label' => false, 'maxlength' => '50', "class" => "form-control", 'div' => false, 'id' => 'UserLoginEmail', 'value' => @$userEmail)); ?>
    </div>
</div>
<div class="form-group clearfix">
    <div class="col-sm-12">
        <?php echo $this->Form->input('User.password', array("placeholder" => "Enter Your Password", 'label' => false, 'type' => 'password', 'div' => false, 'maxlength' => '20', "class" => "form-control", 'value' => @$userPassword)); ?>
    </div>
</div>
<div class="col-sm-12">
    <div class="left error" style="display:none;" id="poperror">Invalid email or password, please try again</div>
</div>
<div class="form-group chole-lf-btm theme-border-1 clearfix">
    <div class="col-sm-12">
        <?php echo $this->Html->link('Sign Up', array('controller' => 'users', 'action' => 'registration'), array('escape' => false, 'class' => "link-sign-up")); ?>
        <?php echo $this->Form->input('LOGIN', array('type' => 'button', 'id' => 'btnUserLogin', 'label' => false, 'div' => false, 'class' => 'theme-bg-1 link-login')); ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>
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

        $(document).on('click', '.DV-login-control > a', function () {
            $('.DV-login-wrap').toggleClass('open');
        });
        $(document).mouseup(function (e)
        {
            var container = $(".DV-login-wrap");
            if (!container.is(e.target) && container.has(e.target).length === 0)
            {
                $('.DV-login-wrap').removeClass('open');
            }
        });
    });

</script>