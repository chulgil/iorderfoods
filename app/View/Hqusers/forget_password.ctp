<!-- static-banner -->
<?php echo $this->element('hquser/static_banner'); ?>
<!-- /banner -->
<div class="signup-form">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="common-title clearfix">
                    <span class="yello-dash"></span>
                    <h2>Forgot Password</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php echo $this->Session->flash(); ?>
                <div class="form-bg">
                    <?php echo $this->Form->create('User', array('url' => array('controller' => 'hqusers', 'action' => 'forgetPassword'), 'autocomplete' => 'off', 'class' => 'sign-up')); ?>
                    <div class="account-info">
                        <p>Already have an account?<span class="ask-login">Login</span></p>
                    </div>
                    <div class="main-form">
                        <div class="form-group">
                            <div class="left-tile">
                                <label>Email <em>*</em></label>
                            </div>
                            <div class="rgt-box">
                                <?php echo $this->Form->input('User.email', array('label' => false, "placeholder" => "Enter Your Email", 'autofocus' => true, 'type' => 'text', "class" => "form-control custom-text forgotpassword")); ?>
                            </div>
                        </div>
                        <div class="submit-btn">
                            <?php echo $this->Form->button('SUBMIT', array('type' => 'submit', 'class' => 'btn common-config black-bg')); ?>
                        </div>
                    </div>
                    <?php echo $this->Form->end(); ?>
                    <div class="ext-border">
                        <?php echo $this->Html->image('hq/thick-border.png', array('alt' => 'user')) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $("#UserForgetPasswordForm").validate({
        rules: {
            "data[User][email]": {
                required: true,
                email: true
            }
        },
        messages: {
            "data[User][email]": {
                required: "Please enter your email",
                email: "Please enter valid email",
            }
        }
    });
    $(function () {
        $(".forgotpassword").focus();
        $(".account-info").click(function () {
            $(".login-pop").slideToggle("shop-popup");
            $("html, body").delay(2000).animate({
                scrollTop: $('#log-in-pop').offset().top
            }, 1000);

        });
    });
    $(function () {
        $("#flashMessage").fadeOut(8000);
    });


</script>