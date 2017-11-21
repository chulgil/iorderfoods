<div class="single-frame">
        <section class="form-layout sign-in forgot-pass-wrap">    


            <?php if (!empty($adminrecord)) { ?>
                <div class="panel-body">        
                <?php echo $this->Form->create('Users', array('url' => array('controller' => 'users', 'action' => 'resetPassword')));
                echo $this->Form->input("User.id", array('type' => 'hidden', 'value' => $adminrecord['User']['id']));
                echo $this->Form->input("User.check", array('type' => 'hidden', 'value' => 3));
                echo $this->Form->input("User.token", array('type' => 'hidden', 'value' => $adminrecord['User']['forgot_token']));
                ?>
                <fieldset>
                    <?php echo $this->Session->flash();?>              
                    <div class="form-group form_margin">                                        
                        <?php echo $this->Form->input('User.newpassword', array('type' => 'password', 'class' => 'form-control user-name', 'placeholder' => 'New Password', 'maxlength' => '20', 'label' => false));
                                echo $this->Form->error('User.newpassword');?>
                    </div>
                    <div class="form-group form_margin"> 

                        <?php echo $this->Form->input('User.repassword', array('type' => 'password', 'class' => 'form-control user-name', 'placeholder' => 'Confirm Password', 'maxlength' => '20', 'label' => false));
                                echo $this->Form->error('User.repassword');?>
                    </div>
                    
                    <?php 
                    
                    echo $this->Form->button('Submit', array('type' => 'submit', 'class' => 'btn btn-default'));
                    
                    //echo $this->Form->submit('Login',array('class' => 'btn btn-default'));?>                
                </fieldset>
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
                <div class="panel-body">        
                <?php echo $this->Form->create('Users', array('url' => array('controller' => 'users', 'action' => 'resetPassword')));
                echo $this->Form->input("User.id", array('type' => 'hidden', 'value' => $hqadminrecord['User']['id']));
                echo $this->Form->input("User.check", array('type' => 'hidden', 'value' => 2));
                echo $this->Form->input("User.token", array('type' => 'hidden', 'value' => $hqadminrecord['User']['forgot_token']));
                ?>
                <fieldset>
                    <?php echo $this->Session->flash();?>              
                    <div class="form-group form_margin">                                        
                        <?php echo $this->Form->input('User.newpassword', array('type' => 'password', 'class' => 'form-control user-name', 'placeholder' => 'New Password', 'maxlength' => '20', 'label' => false));
                                echo $this->Form->error('User.newpassword');?>
                    </div>
                    <div class="form-group form_margin"> 

                        <?php echo $this->Form->input('User.repassword', array('type' => 'password', 'class' => 'form-control user-name', 'placeholder' => 'Confirm Password', 'maxlength' => '20', 'label' => false));
                                echo $this->Form->error('User.repassword');?>
                    </div>
                    
                    <?php 
                    
                    echo $this->Form->button('Submit', array('type' => 'submit', 'class' => 'btn btn-default'));
                    
                    //echo $this->Form->submit('Login',array('class' => 'btn btn-default'));?>                
                </fieldset>
                <?php echo $this->Form->end(); ?>
            </div>
            <?php } ?>            
            
            
            <?php if (!empty($superadminrecord)) { ?>
                <div class="panel-body">        
                <?php echo $this->Form->create('Users', array('url' => array('controller' => 'users', 'action' => 'resetPassword')));
                echo $this->Form->input("User.id", array('type' => 'hidden', 'value' => $superadminrecord['User']['id']));
                echo $this->Form->input("User.check", array('type' => 'hidden', 'value' => 1));
                echo $this->Form->input("User.token", array('type' => 'hidden', 'value' => $superadminrecord['User']['forgot_token']));
                ?>
                <fieldset>
                    <?php echo $this->Session->flash();?>              
                    <div class="form-group form_margin">                                        
                        <?php echo $this->Form->input('User.newpassword', array('type' => 'password', 'class' => 'form-control user-name', 'placeholder' => 'New Password', 'maxlength' => '20', 'label' => false));
                                echo $this->Form->error('User.newpassword');?>
                    </div>
                    <div class="form-group form_margin"> 

                        <?php echo $this->Form->input('User.repassword', array('type' => 'password', 'class' => 'form-control user-name', 'placeholder' => 'Confirm Password', 'maxlength' => '20', 'label' => false));
                                echo $this->Form->error('User.repassword');?>
                    </div>
                    
                    <?php 
                    
                    echo $this->Form->button('Submit', array('type' => 'submit', 'class' => 'btn btn-default'));
                    
                    //echo $this->Form->submit('Login',array('class' => 'btn btn-default'));?>                
                </fieldset>
                <?php echo $this->Form->end(); ?>
            </div>
            <?php } ?>
        </section>
        </div>