<?php If (DESIGN == 4) { ?>
    <style>
        .input.text {
            width: 405px;
        }
    </style>
    <div class="content  single-frame">
        <div class="wrap">
            <?php //echo $this->Session->flash(); ?>
            <?php
            echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'forgetPassword'), 'autocomplete' => 'off'));
            ?>
            <div class="clearfix">
                <section class="form-layout sign-up registration-from">
                    <h2> <span>Forgot Password</span> </h2>
                    <ul class='clearfix'>
                        <li class="clearfix">
                            <span class="title"><label>Email <em>*</em></label></span>
                            <div class="title-box"><?php
                                echo $this->Form->input('User.email', array('label' => false, "placeholder" => "Enter Your Email", 'autofocus' => true, 'type' => 'text', "class" => "inbox forgotpassword"));
                                ?> <?php
                                echo $this->Form->input('User.role_id', array('type' => 'hidden', "value" => 4));
                                ?></div>
                        </li>

                        <li>
                            <span class="title blank">&nbsp;</span>
                            <div class="title-box"><button type="submit" class="btn green-btn"> Submit </button></div>
                        </li>
                    </ul>
                </section>
            </div>
            <?php echo $this->Form->end(); ?>
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
        });
    </script>
<?php } else { ?>
    <?php if (DESIGN == 3) { ?>
        <div class="title-bar">Forgot Password</div>
    <?php } ?>
    <div class="main-container">
        <div class="ext-menu-title">
            <h4>Forgot Password</h4>
        </div> 
        <div class="inner-wrap profile">
            <div class="form-section user-form-section">
                <?php
                echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'forgetPassword'), 'autocomplete' => 'off'));
                ?>
                <div class="form-space">
                    <div class="profile-input clearfix">
                        <label>Email<em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php
                                echo $this->Form->input('User.email', array('label' => false, "placeholder" => "Enter Your Email", 'autofocus' => true, 'type' => 'text', "class" => "user-detail forgotpassword"));
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="forgot-pass-section clearfix">
                    <div class="row">
                        <?php if (DESIGN == 3) { ?>
                            <?php
                            echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/login'", 'class' => 'p-cancle'));
                            ?>
                            <?php
                            echo $this->Form->button('Submit', array('type' => 'submit', 'class' => 'p-save theme-bg-1'));
                            ?>
                        <?php } else { ?>
                            <div class="col-sm-6 col-xs-6">
                                <?php
                                echo $this->Form->button('Submit', array('type' => 'submit', 'class' => 'p-save theme-bg-1'));
                                ?>
                            </div>
                            <div class="col-sm-6 col-xs-6">
                                <?php
                                echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/login'", 'class' => 'p-cancle theme-bg-2'));
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $("#UserForgetPasswordForm").validate({
            rules: {
                debug: false,
                errorClass: "error",
                errorElement: 'span',
                onkeyup: false,
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
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            }
        });
        $(function () {
            $(".forgotpassword").focus();
        });
    </script>

<?php } ?>
