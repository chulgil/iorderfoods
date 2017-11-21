<?php echo $this->Form->create('User', array('id'=>'chkOrderUserLogin','url'=>array('controller'=>'users','action'=>'popuplogin'),'autocomplete' => 'off'));?>
                <div class="clearfix">
                    <div class="header-left">
                        <div class="filed-form">
                            <div class="left"><?php echo $this->Form->input('User.email1', array('type'=>'email',"placeholder" => "Enter Your Email", 'autofocus' => true, 'label' => false, 'maxlength' => '50', "class" => "inbox", 'div' => false)); ?></div>
                            <div class="right"><?php echo $this->Form->input('User.password1', array("placeholder" => "Enter Your Password", 'label' => false, 'type' => 'password', 'div' => false, 'maxlength' => '20', "class" => "inbox")); ?></div>
                            <div class="clr"></div>
                        </div>
                        <div class="filed-form static-links"> 
                            <div class="left error" style="display:none;" id="poperror">Invalid email or password, please try again</div>
                            <div class="right"> <?php echo $this->Html->link('Forgot Password?', array('controller' => 'users', 'action' => 'forgetPassword')); ?>
                                 | <?php echo $this->Html->link('Sign Up', array('controller' => 'users', 'action' => 'registration')); ?>
                         </div>
                            <div class="clr"></div>
                        </div>
                        <div class="button-frame clearfix">
            <button type="button" id='btnUserLogin' class="btn btn-primary theme-bg-1"> <span>Login</span> </button>
        </div>
                    </div>
                    
                </div>
            <?php echo $this->Form->end(); ?>





<script>
    $("#chkOrderUserLogin").validate({
        rules: {
            "data[User][email1]": {
                required: true,
                email: true,
                minlength: 10,
                maxlength: 50,
            },
            "data[User][password1]": {
                required: true,
            },
        },
        messages: {
            "data[User][email1]": {
                required: "Please enter email",
                email: "Please enter valid email"
            },
            "data[User][password1]": {
                required: "Please enter password",
            }
        }
    });

    $('#btnUserLogin').click(function () {        
        if ($("#chkOrderUserLogin").valid()) {
            var email = $('input#UserEmail1').val();
            var password = $('input#UserPassword1').val();           
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'popuplogin')); ?>",
                type: "Post",
                dataType: 'html',
                data: {email: email, password: password},
                success: function (successResult) {
                    response = jQuery.parseJSON(successResult);
                    if(response.status == 1) {
                       $('#poperror').hide();
                       changeTabPan('chkOrderType','chkLogin');
                       setDefaultStoreTime();
                    }else{
                        $('#poperror').show();
                    }
                }
            });
        }
    });

</script>