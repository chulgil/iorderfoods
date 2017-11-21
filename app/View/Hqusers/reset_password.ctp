<!-- static-banner -->
<?php echo $this->element('hquser/static_banner'); ?>
<!-- /banner -->
<div class="signup-form">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="common-title clearfix">
                    <span class="yello-dash"></span>
                    <h2>Reset Password</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php echo $this->Session->flash(); ?>
                <div class="form-bg">
                    <?php echo $this->Form->create('User', array('url' => array('controller' => 'hqusers', 'action' => 'resetPassword'), 'class' => 'sign-up')); ?>
                    <?php echo $this->Form->input("User.id", array('type' => 'hidden', 'value' => @$userData['User']['id'])); ?>
                    <?php echo $this->Form->input("User.role_id", array('type' => 'hidden', 'value' => @$userData['User']['role_id'])); ?>
                    <?php echo $this->Form->input("User.forgot_token", array('type' => 'hidden', 'value' => @$userData['User']['forgot_token'])); ?>
                    <div class="account-info">
                        <p>Already have an account?<span class="ask-login">Login</span></p>
                    </div>
                    <div class="main-form">
                        <div class="form-group">
                            <div class="left-tile">
                                <span class="label-icon"><?php echo $this->Html->image('hq/password.png', array('alt' => 'user')) ?></span><label>New Password</label>
                            </div>
                            <div class="rgt-box">
                                <?php echo $this->Form->input('User.newpassword', array('type' => 'password', 'class' => 'form-control custom-text', 'placeholder' => 'New Password', 'maxlength' => '20', 'label' => false)); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="left-tile">
                                <span class="label-icon"><?php echo $this->Html->image('hq/password-fill.png', array('alt' => 'user')) ?></span><label>Confirm Password</label>
                            </div>
                            <div class="rgt-box">
                                <?php echo $this->Form->input('User.repassword', array('type' => 'password', 'class' => 'form-control custom-text', 'placeholder' => 'Confirm Password', 'maxlength' => '20', 'label' => false)); ?>
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
<script>
    $(document).ready(function () {
        jQuery.validator.addMethod("passw", function (pass, element) {
            pass = pass.replace(/\s+/g, "");
            return this.optional(element) || pass.length > 7 &&
                    pass.match(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[A-Za-z\d$@$!%*#?& ]{8,}$/);
            //pass.match(/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?& ])[A-Za-z\d$@$!%*#?& ]{8,}$/);
        }, "Atleast one digit, one upper and one lower case letter");

        $("#UserResetPasswordForm").validate({
            rules: {
                "data[User][newpassword]": {
                    required: true,
                    minlength: 8,
                    maxlength: 20,
                    passw: true,
                },
                "data[User][repassword]": {
                    required: true,
                    equalTo: "#UserNewpassword"
                },
            },
            messages: {
                "data[User][newpassword]": {
                    required: "Please enter your password",
                    minlength: "Password must be at least 8 characters",
                    maxlength: "Please enter no more than 20 characters",
                    passw: "Atleast one digit, one upper and lower case letter"
                },
                "data[User][repassword]": {
                    required: "Please enter your password again",
                    equalTo: "Password not matched",
                }

            }
        });
    });
</script>