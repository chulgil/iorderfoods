<div class="rgt-side">
        	<!-- header start here -->
        	<header class="clearfix">
            	
                <div class="banner">
                	&nbsp;
                </div>
                <div class="header-input-form clearfix">
		<?php echo $this->Session->flash(); ?>
		<?php echo $this->Form->create('User', array('controller'=>'users','action'=>'login','autocomplete' => 'off'));?>
                    	
			<div class="input-field clearfix">
                        	<label>Email</label>
			 <?php echo $this->Form->input('User.email', array("placeholder" => "Email",'autofocus' => true,'label'=>false,'maxlength'=>'30',"class"=>"txtbx",'div'=>false)); ?>

                           
                        </div>
                        <div class="input-field clearfix">
                        	<label>Password</label>
				<?php echo $this->Form->input('User.password', array("placeholder" => "Password",'label'=>false,'type'=>'password','div'=>false,'maxlength'=>'30',"class"=>"txtbx")); ?>

                            
                        </div>
                        <div class="input-field clearfix">
                        	<label class="chkbx-wrap">
					
							<?php if(!empty($rem)):?>
				      <?php echo $this->Form->input('User.remember',array('label'=>false,'div'=>false,'type'=>'checkbox','checked'=>true))."&nbsp";?><span>Remember me</span>
				      <?php else:?>
				      <?php echo $this->Form->input('User.remember',array('label'=>false,'div'=>false,'type'=>'checkbox','checked'=>false))."&nbsp";?><span>Remember me</span>
				      <?php endif;?>
				</label>
                            <input type="submit" value="Login">
                        </div>
                         <?php 
		        echo $this->Html->link('Forgot Password?', array('controller' => 'users', 'action' => 'forgetPassword')); ?>

                         <?php echo $this->Html->link('Signup', array('controller' => 'users', 'action' => 'registration'),array('class'=>'signup')); ?>

                    </form>
                </div>
		<script>
			    $("#UserLoginForm").validate({
            rules: {
             
                 "data[User][email]": {
                    required: true,
                    email: true
                  
                },
                "data[User][password]": {
                    required: true,
                    minlength:8,
                    maxlength:20,
		    alphanumeric:true
                }
            },
            messages: {
               
                "data[User][email]": {
                    required: "Please enter your email",
                    email:"Please enter valid email",
		  
                    
                },
                
                "data[User][password]": {
                    required: "Please enter your password",
		      alphanumeric:'Only Letters,numbers,underscore allowed',
		minlength:"Please enter at least 8 characters",
		    maxlength:"Please enter no more than 20 characters",
                }
	    }
    });
		</script>
</header>
		