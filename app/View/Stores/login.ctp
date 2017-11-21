    <?php //echo $this->Html->script('admin/admin_login'); ?>
    <div class="panel-body">        
            <?php  echo $this->Form->create('Store');?>
            <fieldset>
                <?php echo $this->Session->flash();?>              
                <div class="form-group form_margin">                                        
                    <?php echo $this->Form->input('User.email',array('label' => false,'div' => false, 'placeholder' => 'E-mail','class' => 'form-control user-name','maxlength' => 55));?>
                </div>
                <div class="form-group form_margin"> 
                     
                    <?php echo $this->Form->input('User.password',array('label' => false,'div' => false, 'placeholder' => 'Password','class' => 'form-control user-password','maxlength' => 30,'type'=> 'password'));?>
                </div>
                <div class="checkbox">
                    <label>
                        
                         <?php if(!empty($rem)):?>
                        <?php echo $this->Form->input('User.remember',array('label'=>false,'div'=>false,'type'=>'checkbox','checked'=>true))."&nbsp";?><span>Remember me</span>
                        <?php else:?>
                        <?php echo $this->Form->input('User.remember',array('label'=>false,'div'=>false,'type'=>'checkbox','checked'=>false))."&nbsp";?><span>Remember me</span>
                        <?php endif;?>
                    </label>
                    
                    
                    <label style="float:right">
                        <?php echo $this->Html->link('Forgot password?','/Stores/forgetPassword')?>
                    </label>
                    
                </div>
                <?php echo $this->Form->submit('Login',array('class' => 'btn btn-default'));?>                
            </fieldset>
            <?php echo $this->Form->end(); ?>
    </div>
<?php echo $this->element('confirm_order');  ?>
    <script>
    $("#StoreLoginForm").validate({
            rules: {
                "data[User][email]": {
                    required: true,
                    email:true,
                },
                "data[User][password]": {
                      required: true,
                },
            },
            messages: {
                "data[User][email]": {
                    required: "Please enter your email",
                    email:"Please enter valid email"
                },
                "data[User][password]": {
                    required: "Please enter Password",                    
                },
               
            }
    });
</script>