<a href="javascript:void(0)" id="log-in-pop">LOG IN</a>
<span>
    <?php echo $this->Html->image('hq/seprator-dots.png', array('alt' => 'dots')) ?>
</span>
<?php echo $this->Html->link('SIGN UP ', array('controller' => 'hqusers', 'action' => 'registration')); ?>
<div class="login-pop">
    <?php echo $this->Form->create('User', array('id' => 'UserLogin', 'url' => array('controller' => 'hqusers', 'action' => 'login'), 'autocomplete' => 'off', 'class' => 'min-form')); ?>
    <span class="arrow-up"><?php echo $this->Html->image('hq/ARROW-UP.png', array('alt' => 'arrow')) ?></span>
    <div class="single-row-field clearfix">
        <div class="custom-text-box"><?php echo $this->Form->input('User.email-m', array('type' => 'email', "placeholder" => "Enter your email", 'autofocus' => true, 'label' => false, 'maxlength' => '50', "class" => "form-control custom-text", 'div' => false)); ?></div>
        <div class="custom-text-box"><?php echo $this->Form->input('User.password-m', array("placeholder" => "Enter your password", 'label' => false, 'type' => 'password', 'div' => false, 'maxlength' => '20', "class" => "form-control custom-text")); ?></div>
        
        <span class="log-btn"><input type="button" class="btn-primary login-btn theme-bg-1" value="LOGIN"></span>
        <div class="clearfix"></div>
        <div class="form-bottom clearfix">
            <div class="left-col">
                <span class="custom-checkbox">
                    <?php if (!empty($rem)): ?>
                        <input type="checkbox" id="RememberMe"  name="data[User][remember]" checked />
                    <?php else: ?>
                        <input type="checkbox" id="RememberMe"  name="data[User][remember]" />
                    <?php endif; ?>
                    <label for="RememberMe">
                        <p><span class="bold-text">Remember me</span></p>
                    </label>
                </span>
            </div>
            <div class="rgt-col">
                <?php echo $this->Html->link('Forgot Password?', array('controller' => 'hqusers', 'action' => 'forgetPassword')); ?>
                <span>|</span>
                <?php echo $this->Html->link('SIGN UP ', array('controller' => 'hqusers', 'action' => 'registration')); ?>
            </div>
        </div>
    </div>
    <?php echo $this->Form->end(); ?>
    <div id="loginFlashMsg"></div>
</div>
<script type="text/javascript">
    $(function () {
        //$("#flashMessage").fadeOut(8000);
        $("#UserLogin").validate({
            rules: {
                "data[User][email-m]": {
                    required: true
                },
                "data[User][password-m]": {
                    required: true
                }
            },
            messages: {
                "data[User][email-m]": {
                    required: "Please enter your email"
                },
                "data[User][password-m]": {
                    required: "Please enter your password"
                }
            }
        });
    });
    $(document).on('click', '.login-btn', function (e) {
        e.stopImmediatePropagation();
        if ($("#UserLogin").valid()) {
            var formData = $("#UserLogin").serialize();
            $.ajax({
                type: 'post',
                url: "<?php echo $this->Html->url(array('controller' => 'hqusers', 'action' => 'login')); ?>",
                async: false,
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
                }, success: function (successResult) {
                    if (successResult != '') {
                        result = jQuery.parseJSON(successResult);
                        if (result.status == 'Error') {
                            $('#loginFlashMsg').html('<div id="flashMessage" class="message message-success alert alert-danger"><a title="close" aria-label="close" data-dismiss="alert" class="close" href="#">×</a>' + result.msg + '</div>');
                        }
                        if (result.status == 'Success') {
                            //location.reload();
                            location.href='/hqusers/merchant';
                        }
                    }
                }, complete: function () {
                    $.unblockUI();
                }
            });
        }
        e.preventDefault();
    });
</script>
