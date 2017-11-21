
    <div class="row">
            <div class="col-lg-6">
	    <h3>My Profile</h3> 
                <?php echo $this->Session->flash();?>   
            </div> 
            <div class="col-lg-6">                        
                <div class="addbutton">                
                        <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
                </div>
            </div>
    </div>   
    <div class="row">        
            <?php echo $this->Form->create('UsersProfile', array('inputDefaults' => array('label' => false, 'div'=>false, 'required' => false, 'error' => false, 'legend' => false,'autocomplete' => 'off'),'id'=>'UsersProfile'));?>
        <div class="col-lg-6">
            <div class="form-group form_margin">
	    		  <?php echo $this->Form->input('User.role_id',array('type'=>'hidden','value'=>$roleId));?>

                <label>Salutation<span class="required"> * </span></label>                
              
		                 <?php  echo $this->Form->input('User.salutation',array('type'=>'select','options'=>array('Mr.'=>'Mr.','Ms.'=>'Ms.','Mrs'=>'Mrs'),'class'=>'form-control','label'=>'','div'=>false));?>

            </div>
	    <div class="form-group form_margin">
                <label>First Name</label>                
              
		<?php echo $this->Form->input('User.fname',array('type'=>'text','class'=>'form-control','placeholder'=>'Enter Your First Name','maxlength'=>'50','div'=>false));
                  echo $this->Form->error('User.fname');?>
            </div>
	     <div class="form-group form_margin">
                <label>Last Name</label>                
              
		<?php echo $this->Form->input('User.lname',array('type'=>'text','class'=>'form-control','placeholder'=>'Enter Your Last Name','maxlength'=>'50','div'=>false));
                  echo $this->Form->error('User.lname');?>
            </div>
	     <div class="form-group form_margin">
                <label>Email</label>                
              
		<?php echo $this->Form->input('User.email',array('type'=>'text','class'=>'form-control email','placeholder'=>'Enter Your Email','maxlength'=>'50','div'=>false,'readonly'=>true));
                  echo $this->Form->error('User.email');?>
            </div>
	     <div class="form-group form_margin">
                <label>Phone</label>                
              
		<?php echo $this->Form->input('User.phone',array('type'=>'text','class'=>'form-control','placeholder'=>'Enter Your Phone number','div'=>false));
                  echo $this->Form->error('User.phone');?>
                <span class="blue">(eg. 111-111-1111)</span> 
            </div>
	       
	       <div class="form-group">
                      
              
		<?php echo $this->Form->input('User.changepassword',array('type'=>'checkbox','class'=>'passwrd-input','label'=>'Change Password'));?>

            </div>
	    <div class="change_password">
	     
	   
	   
	     <div class="form-group form_margin">
                <label>Old Password</label>                
              
		<?php
		  echo $this->Form->input('User.oldpassword',array('type'=>'password','class'=>'form-control','placeholder'=>'Old Password'));
                  echo $this->Form->error('User.oldpassword');?>
            </div>
	       <div class="form-group form_margin">
                <label>New Password</label>                
              
		<?php
		  echo $this->Form->input('User.password',array('type'=>'password','class'=>'form-control','placeholder'=>'New Password','value'=>''));
                  echo $this->Form->error('User.password');?>
            </div>
	         <div class="form-group form_margin">
                <label>Confirm Password</label>                
              
		<?php
		  echo $this->Form->input('User.password_match',array('type'=>'password','class'=>'form-control','placeholder'=>'Confirm Password'));
		  
                  echo $this->Form->error('User.password_match');?>
	    </div>
		 

	    
	  
	    </div>
	       
	       
        
        
            
                     
            <?php echo $this->Form->button('Update', array('type' => 'submit','class' => 'btn btn-default'));  echo "&nbsp;";           
             echo $this->Form->button('Cancel', array('type' => 'button','onclick'=>"window.location.href='/stores/index'",'class' => 'btn btn-default'));
	     ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </div><!-- /.row -->
    
<script>
    $(document).ready(function() {
	
      
      		 $('#UserPassword').css('display','none');
		 $("#UserPassword").prop('disabled', true);

      $('.change_password').css('display','none');
      
      
      $('#UserChangepassword').on('change',function() {
	  
	    
	    if($(this).prop('checked')){
	       $('.change_password').css('display','block');
	       $('#UserPassword').css('display','block');
	       $("#UserPassword").prop('disabled', false);
	     }else{
	        $('.change_password').css('display','none');
		 $('#UserPassword').css('display','none');
		 $("#UserPassword").prop('disabled', true);
	     }
	    
	});
	 $('.date_select').datepicker({
	       
	       dateFormat: 'mm-dd-yy',
	       
	   });
	    $("#UsersProfile").validate({
            rules: {
                "data[User][fname]": {
                    required: true,
                    lettersonly: true, 
                },
                 "data[User][lname]": {
                    required: false,
                    lettersonly: true, 
                },
                 "data[User][email]": {
                    required: true,
                    email: true
                
                },
                "data[User][phone]": { 
                required: true,
                number:true,
                minlength:10,
                maxlength:11,
                
                },
                "data[User][password]": {
                    required: true,
                    alphanumeric:true,
                    minlength:8,
                    maxlength:20,
                },
                 "data[User][password_match]": { 
                required: true, equalTo: "#UserPassword", minlength: 8,maxlength:20,alphanumeric:true,
                },
		"data[User][oldpassword]": {
                    required: true,
                    minlength:8,
		    alphanumeric:true,
                    maxlength:20,
                },
            },
            messages: {
                "data[User][fname]": {
                    required: "Please enter your first name",
                    lettersonly:"Only alphabates allowed",
                },
                 "data[User][lname]": {
                    required: "Please enter your last name",
                    lettersonly:"Only alphabates Allowed",
                },
                "data[User][email]": {
                    required: "Please enter your email",
                    email:"Please enter valid email",
                    remote:"Email Alreay exist",
                },
                 "data[User][phone]": {
                    required: "Please enter your phone number.",
                    number:"Only numbers are allowed"
                },
                
                "data[User][password]": {
                    required: "Please enter your password",
		     alphanumeric:"Only letters,numbers,underscore allowed"
                },
                "data[User][password_match]": {
                    required: "Please enter your password again.",
                    equalTo:"Password not matched"
                },
		"data[User][oldpassword]": {
                    required: "Please enter your old password",
                },
               
            }
    });
	});
</script>    