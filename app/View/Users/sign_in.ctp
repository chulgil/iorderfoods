<div class="content single-frame">
    <div class="wrap">
        <?php //echo $this->Session->flash(); ?>
        <?php echo $this->Form->create('User', array('autocomplete' => 'off'));?>
        <div class="clearfix">
            <section class="form-layout sign-in">
                <h2><span>Sign In</span></h2>    	
                
                <ul class="clearfix">
                	<li>
                    	<span class="title"><label>Email <em>*</em></label></span>
                        <div class="title-box"><?php echo $this->Form->input('User.email', array('type'=>'email',"placeholder" => "Enter Your Email", 'autofocus' => true, 'label' => false, 'maxlength' => '50', "class" => "inbox", 'div' => false)); ?></div>
                    </li>
                    
                    <li>
                    	<span class="title"><label>Password <em>*</em></label></span>
                        <div class="title-box">
							<div class="password"><?php echo $this->Form->input('User.password', array("placeholder" => "Enter Your Password", 'label' => false, 'type' => 'password', 'div' => false, 'maxlength' => '20', "class" => "inbox")); ?>
                            </div>
                            <div class="password-remember">
                                <?php if (!empty($rem)): ?>
                                	<input type="checkbox" id="Remember_me_1"  name="data[User][remember]" checked /> <label for="Remember_me_1">Remember me</label>
                                <?php else: ?>
                                    <input type="checkbox" id="Remember_me_2"  name="data[User][remember]" /> <label for="Remember_me_2">Remember me</label>
                                <?php endif; ?>
                                <div style='float:right;'>
                                    <?php echo $this->Html->link('Forgot Password?', array('controller' => 'users', 'action' => 'forgetPassword')); ?>
                                </div>
                            </div>
                            
                            
                        </div>
                    </li>
                    
                    <li>
                    	<span class="title blank">&nbsp;</span>
                        <div class="title-box"><button type="submit" class="btn green-btn">Sign In</button></div>
                    </li>
                </ul>
            </section>
        </div>
        <?php  echo $this->Form->end();?>
    </div>
</div>
       
        <script>
            $("#UserSignInForm").validate({
                rules: {
                    "data[User][email]": {
                        required: true,
                    },
                    "data[User][password]": {
                        required: true,
                    }
                },
                messages: {
                    "data[User][email]": {
                        required: "Please enter your email",
                    },
                    "data[User][password]": {
                        required: "Please enter your password",
                    }
                }
            });
        </script>