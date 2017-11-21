<div class="content single-frame">
    <div class="wrap">
        <?php echo $this->Session->flash(); ?>
        <?php echo $this->Form->create('Users', array('url' => array('controller' => 'users', 'action' => 'resetPassword'))); ?>
        <?php echo $this->Form->input("User.id", array('type' => 'hidden', 'value' => $record['User']['id'])); ?>
        <?php echo $this->Form->input("User.token", array('type' => 'hidden', 'value' => $record['User']['forgot_token'])); ?>
        <div class="clearfix">
            <section class="form-layout center-form-layout delivery-form full-width-form">
                <h2><span>Reset Password</span></h2>    	
                <ul class="clearfix">
                    <li>
                        <span class="title"><label>New Password <em>*</em></label></span>
                        <div class="title-box"> <?php
                            echo $this->Form->input('User.password', array('type' => 'password', 'class' => 'inbox', 'placeholder' => 'New Password', 'maxlength' => '20', 'label' => false, 'value' => ''));
                            echo $this->Form->error('User.password');?>
                        </div>
                    </li>
                    <li>
                        <span class="title confim-password"><label>Confirm Password<em>*</em></label></span>
                        <div class="title-box"><?php
                            echo $this->Form->input('User.repassword', array('type' => 'password', 'class' => 'inbox', 'placeholder' => 'Confirm Password', 'maxlength' => '20', 'label' => false));
                            echo $this->Form->error('User.repassword');
                            ?>
                        </div>
                    </li>
                </ul>

                <div class="button">
                    <?php
                    echo $this->Form->button('Update', array('type' => 'submit', 'class' => 'btn green-btn'));
                    ?>
                </div>
            </section>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>

<script>

    $("#UsersResetPasswordForm").validate({
        rules: {
            "data[User][password]": {
                required: true,
                minlength: 8,
                alphanumeric: true,
                maxlength: 20,
            },
            "data[User][repassword]": {
                required: true, equalTo: "#UserPassword"
            },
        },
        messages: {
            "data[User][password]": {
                required: "Please enter your password",
                alphanumeric: 'Only Letters,numbers,underscore are allowed',
                minlength: "Please enter at least 8 characters",
                maxlength: "Please enter no more than 20 characters",
            },
            "data[User][repassword]": {
                required: "Please enter your password again",
                equalTo: "Password not matched",
            }

        }
    });
</script>