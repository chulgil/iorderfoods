<div class="ext-menu-title">
    <h4>Reset Password</h4>
</div>
<div class="form-section user-form-section new-reset-password">
            <?php if (!empty($record)) { ?>
                <?php echo $this->Form->create('Users', array('url' => array('controller' => 'users', 'action' => 'resetPassword'))); ?>
                <?php echo $this->Form->input("User.id", array('type' => 'hidden', 'value' => $record['User']['id'])); ?>
                <?php echo $this->Form->input("User.check", array('type' => 'hidden', 'value' => 4)); ?>
                <?php echo $this->Form->input("User.token", array('type' => 'hidden', 'value' => $record['User']['forgot_token'])); ?>
                <div class="form-space">
                    <div class="profile-input clearfix">
                        <label>New Password <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php
                                echo $this->Form->input('User.newpassword', array('type' => 'password', 'class' => 'user-detail', 'placeholder' => 'New Password', 'maxlength' => '20', 'label' => false));
                                echo $this->Form->error('User.newpassword');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Confirm Password <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php
                                echo $this->Form->input('User.repassword', array('type' => 'password', 'class' => 'user-detail', 'placeholder' => 'Confirm Password', 'maxlength' => '20', 'label' => false));
                                echo $this->Form->error('User.repassword');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <div class="profile-btn-section clearfix">
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <?php
                            echo $this->Form->button('Submit', array('type' => 'submit', 'class' => 'theme-bg-1 p-save'));?>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <?php echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/login'", 'class' => 'theme-bg-2 p-cancle'));
                            ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo $this->Form->end(); ?>
            <?php } ?>
            <?php if (!empty($adminrecord)) { ?>
                <?php echo $this->Form->create('Users', array('url' => array('controller' => 'users', 'action' => 'resetPassword'))); ?>
                <?php echo $this->Form->input("User.id", array('type' => 'hidden', 'value' => $adminrecord['User']['id'])); ?>
                <?php echo $this->Form->input("User.check", array('type' => 'hidden', 'value' => 3)); ?>
                <?php echo $this->Form->input("User.token", array('type' => 'hidden', 'value' => $adminrecord['User']['forgot_token'])); ?>
                <div class="form-space" style="padding: 10px;">
                    <div class="profile-input clearfix">
                        <label>New Password <em style="color: red;">*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php
                                echo $this->Form->input('User.newpassword', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'New Password', 'maxlength' => '20', 'label' => false));
                                echo $this->Form->error('User.newpassword');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Confirm Password <em style="color: red;">*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php
                                echo $this->Form->input('User.repassword', array('type' => 'password', 'class' => 'form-control', 'placeholder' => 'Confirm Password', 'maxlength' => '20', 'label' => false));
                                echo $this->Form->error('User.repassword');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-btn-section clearfix">
                        <?php
                        echo $this->Form->button('Submit', array('type' => 'submit', 'class' => 'theme-bg-1 p-save'));
                        ?>
                    </div>
                    <?php echo $this->Form->end(); ?>
                </div>

            <?php } ?>
            <?php if (!empty($hqadminrecord)) {?>
            <style>
                .form-section.user-form-section{
                    padding: 10px;
                }
                em{
                    color: red;
                }
            </style>
                <?php echo $this->Form->create('Users', array('url' => array('controller' => 'users', 'action' => 'resetPassword'))); ?>
                <?php echo $this->Form->input("User.id", array('type' => 'hidden', 'value' => $hqadminrecord['User']['id'])); ?>
                <?php echo $this->Form->input("User.check", array('type' => 'hidden', 'value' => 2)); ?>
                <?php echo $this->Form->input("User.token", array('type' => 'hidden', 'value' => $hqadminrecord['User']['forgot_token'])); ?>
                <div class="form-space">
                    <div class="profile-input clearfix">
                        <label>New Password <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php
                                echo $this->Form->input('User.newpassword', array('type' => 'password', 'class' => 'form-control user-name', 'placeholder' => 'New Password', 'maxlength' => '20', 'label' => false));
                                echo $this->Form->error('User.newpassword');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Confirm Password <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php
                                echo $this->Form->input('User.repassword', array('type' => 'password', 'class' => 'form-control user-name', 'placeholder' => 'Confirm Password', 'maxlength' => '20', 'label' => false));
                                echo $this->Form->error('User.repassword');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="profile-btn-section clearfix">
                    <?php
                    echo $this->Form->button('Submit', array('type' => 'submit', 'class' => 'btn btn-default'));
                    ?>
                </div>
                <?php echo $this->Form->end(); ?>
            <?php } ?>
            <?php if (!empty($superadminrecord)) { ?>
                <?php echo $this->Form->create('Users', array('url' => array('controller' => 'users', 'action' => 'resetPassword'))); ?>
                <?php echo $this->Form->input("User.id", array('type' => 'hidden', 'value' => $superadminrecord['User']['id'])); ?>
                <?php echo $this->Form->input("User.check", array('type' => 'hidden', 'value' => 1)); ?>
                <?php echo $this->Form->input("User.token", array('type' => 'hidden', 'value' => $superadminrecord['User']['forgot_token'])); ?>
                <div class="form-space">
                    <div class="profile-input clearfix">
                        <label>New Password <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php
                                echo $this->Form->input('User.newpassword', array('type' => 'password', 'class' => 'user-detail', 'placeholder' => 'New Password', 'maxlength' => '20', 'label' => false));
                                echo $this->Form->error('User.newpassword');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="profile-input clearfix">
                        <label>Confirm Password <em>*</em></label>
                        <div class="col-right">
                            <div class="col-width">
                                <?php
                                echo $this->Form->input('User.repassword', array('type' => 'password', 'class' => 'user-detail', 'placeholder' => 'Confirm Password', 'maxlength' => '20', 'label' => false));
                                echo $this->Form->error('User.repassword');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="profile-btn-section clearfix">
                    <?php
                    echo $this->Form->button('Submit', array('type' => 'submit', 'class' => 'theme-bg-1 p-save'));
                    ?>
                </div>
                <?php echo $this->Form->end(); ?>
            <?php } ?>
        </div>