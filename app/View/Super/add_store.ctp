
    <div class="row">
            <div class="col-lg-6">
	    <h3>Add Store Details</h3> 
           <?php echo $this->Session->flash();?>   
            </div> 
            <div class="col-lg-6">                        
                <div class="addbutton">                
                        <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
                </div>
            </div>
    </div>   
    <div class="row">        
            <?php echo $this->Form->create('super', array('inputDefaults' => array('label' => false, 'div'=>false, 'required' => false, 'error' => false, 'legend' => false,'autocomplete' => 'off'),'id'=>'addstore'));?>
        <div class="col-lg-6">
            <div class="form-group form_margin">
                <label>Merchant<span class="required"> * </span></label> 
		<?php echo $this->Form->input('Merchant.id',array('options'=>$merchantList,'class'=>'form-control','div'=>false,'empty'=>'Please Select Merchant'));?>
            </div>  
            
            <div class="form-group form_margin">
                <label>Merchant #<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Store.merchant_number',array('type'=>'text','class'=>'form-control','placeholder'=>'Enter Unique Merchant Number','label'=>'','div'=>false));?>
            </div>
            
            <div class="form-group form_margin">
                <label>Store Name<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Store.store_name',array('type'=>'text','class'=>'form-control','placeholder'=>'Enter Name','label'=>'','div'=>false));?>
            </div>
            
            <div class="form-group form_margin">
                <label>Store Domain<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Store.store_url',array('type'=>'text','class'=>'form-control','placeholder'=>'Enter Store Domain','label'=>'','div'=>false));?>
            </div>
            
             <div class="form-group form_margin">
                <label>Email<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Store.email_id',array('type'=>'text','class'=>'form-control','placeholder'=>'Enter Email','label'=>'','div'=>false));
                  echo $this->Form->error('Store.email_id'); 
                ?>
                <label id="StoreEmailIderror1" class="error" style="display: none;"></label>
             <span class="blue">(This email address is used for notification purpose)</span> 
             </div>

             
            <div class="form-group form_margin">
                <label>Phone no<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Store.phone',array('data-mask'=>'mobileNo','type'=>'text','class'=>'form-control phone_number','placeholder'=>'Enter Contact Number','label'=>'','div'=>false));?>
                 <span class="blue">(eg. 111-111-1111)</span> 
            </div>
            
            <div class="form-group form_margin">
                <label>Address<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Store.address',array('type'=>'text','class'=>'form-control','placeholder'=>'Enter Address','label'=>'','div'=>false));?>
            </div>
            
            <div class="form-group form_margin">
                <label>City<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Store.city',array('type'=>'text','class'=>'form-control','placeholder'=>'Enter City','label'=>'','div'=>false));?>
            </div>
            
            <div class="form-group form_margin">
                <label>State<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Store.state',array('type'=>'text','class'=>'form-control','placeholder'=>'Enter State','label'=>'','div'=>false));?>
            </div>
            
            <div class="form-group form_margin">
                <label>Zipcode<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Store.zipcode',array('type'=>'text','class'=>'form-control','placeholder'=>'Enter Zipcode','label'=>'','div'=>false));?>
            </div> 
                         
            <hr/>
            
            <div class="form-group form_margin">
                <label>Salutation<span class="required"> * </span></label>
		    <?php echo $this->Form->input('User.salutation',array('type'=>'select','options'=>array('Mr.'=>'Mr.','Ms.'=>'Ms.','Mrs'=>'Mrs'),'class'=>'form-control valid','label'=>'','div'=>false)); ?>

            </div>
	    <div class="form-group form_margin">
                <label>First Name<span class="required"> * </span></label> 
		<?php echo $this->Form->input('User.fname',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Your First Name','label'=>'','div'=>false));
                  echo $this->Form->error('User.fname'); ?>
            </div>
	     <div class="form-group form_margin">
                <label>Last Name</label>
		<?php echo $this->Form->input('User.lname',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Your Last Name','label'=>'','div'=>false));  
                  echo $this->Form->error('User.lname');?>
            </div>
        
            <div class="form-group form_margin">
                <label>Email<span class="required"> * </span></label>                
              
                <?php          
                
                echo $this->Form->input('User.email',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Your Email','label'=>'','div'=>false,'required'=>true,'autocomplete' => 'off'));            
                  echo $this->Form->error('User.email'); ?>
                <span class="blue">(This email address is used for login)</span> 
                  
            </div>
            
            <div class="form-group form_margin">
            <label>Password<span class="required"> * </span></label>
            <?php
                echo $this->Form->input('User.password',array('type'=>'password','class'=>'form-control valid','placeholder'=>'Enter Your password','label'=>'','div'=>false,'required'=>true));            
                echo $this->Form->error('User.password');
            ?>
            </div>
	    
            
            <div class="form-group form_margin">
            <label>Confirm Password<span class="required"> * </span></label>
            <?php
                echo $this->Form->input('User.password_match',array('type'=>'password','class'=>'form-control valid','placeholder'=>'Enter Confirm Password','label'=>'','div'=>false,'required'=>true));            
                echo $this->Form->error('User.password_match');
            ?>
            </div>


            <div class="form-group form_margin">
                <label>NZ Gateway username <span class="required"> * </span></label>

                <?php
                echo $this->Form->input('Store.api_username',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Api Username','label'=>'','div'=>false,'required'=>true,'autocomplete' => 'off'));
                echo $this->Form->error('Store.api_username'); ?>
            </div>


            <div class="form-group form_margin">
                <label>NZ Gateway password <span class="required"> * </span></label>

                <?php
                echo $this->Form->input('Store.api_password',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Api Password','label'=>'','div'=>false,'required'=>true,'autocomplete' => 'off'));
                echo $this->Form->error('Store.api_password'); ?>
            </div>

            <div class="form-group form_margin">
                <label>NZ Gateway api key</span></label>
                <?php
                echo $this->Form->input('Store.nzgateway_apikey',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Api Key','label'=>'','div'=>false, 'autocomplete' => 'off'));
                echo $this->Form->error('Store.nzgateway_apikey'); ?>
            </div>

            <div class="form-group form_margin">
                <label>Mobile Phone<span class="required"> * </span></label>
                <?php
                echo $this->Form->input('User.phone',array('data-mask'=>'mobileNo','type'=>'text','class'=>'form-control valid phone_number','placeholder'=>'Enter Mobile Phone','label'=>'','div'=>false,'required'=>true));
                echo $this->Form->error('User.phone');
                ?>
                <span class="blue">(eg. 111-111-1111)</span>
            </div>

            <?php echo $this->Form->button('Save', array('type' => 'submit','class' => 'btn btn-default'));  echo "&nbsp;";
             echo $this->Form->button('Cancel', array('type' => 'button','onclick'=>"window.location.href='/super/viewStoreDetails'",'class' => 'btn btn-default'));
	     ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
    
    
    
<script>
	 $(".phone_number").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {	       
                  return false;
        }
    });
    $("[data-mask='mobileNo']").mask("(999) 999-9999");
	  jQuery.validator.addMethod("passw", function (pass, element) {
            pass = pass.replace(/\s+/g, "");
            return this.optional(element) || pass.length > 7 &&
                 pass.match(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[A-Za-z\d$@$!%*#?& ]{8,}$/);
                 //pass.match(/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?& ])[A-Za-z\d$@$!%*#?& ]{8,}$/);
        }, "Atleast one digit, one upper and one lower case letter");
        
      	$("#addstore").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Merchant][id]": {
                    required: true,                   
                },
                "data[Store][merchant_number]": {
                    required: true, 
                    minlength:4,
                    maxlength:8,
                    remote: "/super/checkMerchantNumber" 
                },
                "data[Store][store_name]": {
                    required: true,                   
                },
		"data[Store][store_url]": {
                    required: true,   
                    remote: "/super/checkAllDomainsStore"   
                },
                "data[Store][email_id]": {
                    required: true,
                    email: true,
                    
          
                },
                "data[Store][phone]": {
                    required: true,
                },
                "data[Store][address]": {
                    required: true,                   
                },
                "data[Store][city]": {
                    required: true,                    
                },
                "data[Store][state]": {
                    required: true,                    
                },
                "data[Store][zipcode]": {
                    required: true,                    
                },             
                "data[User][fname]": {
                    required: true,
                    lettersonly: true, 
                },
                 "data[User][email]": {
                    required: true,
                    email: true,                                
                },
                "data[User][password]": {
                    required: true,
                    minlength:8,
                    maxlength:20,
		    passw:true,
                },
                 "data[User][password_match]": { 
                required: true, 
                equalTo: "#UserPassword"
                },
                "data[User][phone]": { 
                required: true,
                
                },  
            },
            messages: {               
                "data[Merchant][id]": {
                    required: "Please select merchant name",                    
                },
                "data[Store][merchant_number]": {
                    required: "Please enter merchant unique number",
                    remote:"Merchant number already exists",
                },                
                "data[Store][store_name]": {
                    required: "Please enter store name",                    
                },
		"data[Store][store_url]": {
                    required: "Please enter domain name",  
                    remote:"Domain name already exists",
                }, 
                "data[Store][email_id]": {
                    required: "Please enter email",
                    email:"Please enter valid email",
                },
                "data[Store][phone]": {
                     required: "Contact number required",
		     
                },                
                "data[Store][address]": {
                    required: "Please enter address",
                },
                "data[Store][city]": {
                    required: "Please enter city name",                    
                },                
                "data[Store][state]": {
                    required: "Please enter state",
                },                
                "data[Store][zipcode]": {
                    required: "Please enter zipcode",
                },
		"data[User][fname]": {
                    required: "Please enter your first name",
                    lettersonly:"Only alphabates Allowed",
                },
                "data[User][email]": {
                    required: "Please enter your email",
                    email:"Please enter valid email",
                   
                },                
                "data[User][password]": {
                required: "Please enter your password",
		minlength: "Password must be at least 8 characters",
                maxlength: "Please enter no more than 20 characters",
		passw: "Atleast one digit, one upper and one lower case letter"
                },
                "data[User][password_match]": {
                    required: "Please enter your password again.",
                    equalTo:"Password not matched"
                },
                 "data[User][phone]": {
                    required: "Contact number required",
                },
               
            },highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
        
     $(document).ready(function() { 
        fetch_data();
        $(document).on('change', '#MerchantId', function (e) {
            fetch_data();
        });
        $(document).on('blur', '#StoreEmailId', function (e) {

                $('#StoreEmailId').filter(function(){
                var emil=$('#StoreEmailId').val();
                var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                    if( !emailReg.test( emil ) ) {
                        } else {
                       fetch_data();
                        }
                    })
        });
        
        function fetch_data() {
            var storeemail= $('#StoreEmailId').val();
            var merchantID= $('#MerchantId').val(); 
            if(merchantID=="Please Select Merchant" || merchantID=="" ){
             return false;
            }else{
                if(storeemail!=""){
                    ajaxCall(storeemail, merchantID);
                 }else{
                      return false;
                 }
            }
             
         }
         
          function ajaxCall(storeemail, merchantID) {
              
              $.ajax({
                               url: "<?php echo $this->Html->url(array('controller' => 'super', 'action' => 'checkStoreEmail')); ?>",
                               type: "Get",
                               dataType: 'html',
                               data: {
                                   storeemail: storeemail,
                                   merchantId: merchantID,
                               },
                               success: function (result) {
                                   if(result==1){
                                       
                               }else{
                                   $("#StoreEmailIderror1").show();
                                   $("#StoreEmailIderror1").html("Email Already Exist");
                                   $("#StoreEmailIderror1").fadeOut(3000);
                               }
                                   
                               }
                        });
              
          }
        });  
        
//        remote:
//                    {
//                      url: '/super/checkStoreNotificationEmail',
//                      data:
//                      {
//                          merchantId: function()
//                          {
//                              $('#addstore :input[name="data[Merchant][id]"]').val();
//                          }
//                      }
//                    }
        
</script>    