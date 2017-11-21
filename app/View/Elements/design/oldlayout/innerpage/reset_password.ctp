<div class="single-frame">
        <section class="form-layout sign-in forgot-pass-wrap">
<!--            <h2><span>Reset Password1</span></h2>-->
            <?php if (!empty($record)) { ?>
                <?php echo $this->Form->create('Users', array('url' => array('controller' => 'users', 'action' => 'resetPassword'))); ?>
                <?php echo $this->Form->input("User.id", array('type' => 'hidden', 'value' => $record['User']['id'])); ?>
                <?php echo $this->Form->input("User.check", array('type' => 'hidden', 'value' => 4)); ?>
                <?php echo $this->Form->input("User.token", array('type' => 'hidden', 'value' => $record['User']['forgot_token'])); ?>
            <ul class="clearfix">
                <li>
                    <span class="title"><label>New Password <em>*</em></label></span>
                    <div class="title-box">
                        <?php
                            echo $this->Form->input('User.newpassword', array('type' => 'password', 'class' => 'inbox', 'placeholder' => 'New Password', 'maxlength' => '20', 'label' => false));
                            echo $this->Form->error('User.newpassword');
                        ?>
                    </div>
                </li>
                    
                <li>
                    <span class="title"><label>Confirm Password <em>*</em></label></span>
                    <div class="title-box">
                        <div class="password">
                            <?php
                                echo $this->Form->input('User.repassword', array('type' => 'password', 'class' => 'inbox', 'placeholder' => 'Confirm Password', 'maxlength' => '20', 'label' => false));
                                echo $this->Form->error('User.repassword');
                                ?>
                        </div>
                    </div>
                </li>
                    
                <li>
                    <span class="title blank">&nbsp;</span>
                    <div class="title-box">
                        <?php
                        echo $this->Form->button('Submit', array('type' => 'submit', 'class' => 'theme-bg-1 p-save btn green-btn','style'=>'margin-right:10px;'));
                    echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/login'", 'class' => 'theme-bg-2 p-cancle btn green-btn'));
                    ?>
                        
                    </div>
                </li>
            </ul>
            
                
<!--                <div class="profile-btn-section clearfix">
                    <?php
                    echo $this->Form->button('Submit', array('type' => 'submit', 'class' => 'theme-bg-1 p-save'));
                    echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/login'", 'class' => 'theme-bg-2 p-cancle'));
                    ?>
                </div>-->
                <?php echo $this->Form->end(); ?>
            <?php } ?>

            </section>
        </div>
            
            