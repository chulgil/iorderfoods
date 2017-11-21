    <?php // echo $this->Html->script('admin/admin_forgotpassword');?>
    
    <div class="panel-body">       
            <?php echo $this->Form->create('Store', array('autocomplete' => 'off')); ?>
            <fieldset>
                
                <?php echo $this->Session->flash();?>                              
                <div class="form-group form_margin">                                        
                    <?php echo $this->Form->input('User.email', array('label' => false, "placeholder" => "Please enter email address", 'autofocus' => true, 'type' => 'text',"class"=>"form-control user-name")); ?>
        <?php echo $this->Form->input('User.role_id', array('type' => 'hidden',"value"=>3)); ?>               
                </div>                
                <div class="row">                                                            
                    <div class="col-lg-2">
                        <?php echo $this->Form->submit('Submit',array('class' => 'btn btn-default'));?>
                    </div>
                    <div class="col-lg-2">                        
                        <?php echo $this->Html->link('Cancel','/Stores/login',array('class' => 'btn btn-default'));?>
                    </div>                    
                </div>                
            </fieldset>
            <?php echo $this->Form->end(); ?>
    </div>
    <script type="text/javascript">
    $("#StoreForgetPasswordForm").validate({
        rules: {
            "data[User][email]": {
                required: true,
                email: true
            }
        },
        messages: {
            "data[User][email]": {
                required: "Please enter your email id."
            }
        }
    });
</script>