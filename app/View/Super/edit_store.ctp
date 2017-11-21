<?php $id = $this->request->data['Store']['id'];?>
    <div class="row">
            <div class="col-lg-6">
	    <h3>Edit Store Details</h3> 
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
		<?php
		echo $this->Form->input('Store.merchant_id',array('options'=>$merchantList,'class'=>'form-control','div'=>false,'empty'=>'Please Select Merchant','disabled'=>true));
		echo $this->Form->input('Store.merchant_id',array('type'=>'hidden'));
		
                echo $this->Form->input('User.id',array('type'=>'hidden'));
		echo $this->Form->input('Store.id',array('type'=>'hidden'));
		echo $this->Form->input('Store.user_id',array('type'=>'hidden'));
		
		?>
            </div>           
            <div class="form-group form_margin">
                <label>Store Name<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Store.store_name',array('type'=>'text','class'=>'form-control','label'=>'','div'=>false));?>
            </div>
            
            <div class="form-group form_margin">
                <label>Merchant #<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Store.merchant_number',array('type'=>'text','class'=>'form-control','placeholder'=>'Enter Unique Merchant Number','label'=>'','div'=>false));?>
            </div>
            
            
            <div class="form-group form_margin">
                <label>Store Domain<span class="required"> * </span></label>
		<?php  echo $this->Form->input('Store.store_url',array('type'=>'text','class'=>'form-control','label'=>'','div'=>false));?>
            </div>


	    <div class="form-group form_spacing">
		<label>Use SSL(HTTPS) </label>
		<?php
		    $checked="";
		    if($this->request->data['Module']['is_ssl']==1){
		      $checked="checked";
		    }
		    echo $this->Form->checkbox('Module.is_ssl',array('checked'=>$checked));
		?>
        </div>

	    <div class="form-group form_spacing">
		<label>Use WWW host name as a default store URL </label>
		<?php
		    $checked="";
		    if($this->request->data['Module']['is_www']==1){
		      $checked="checked";
		    }
		    echo $this->Form->checkbox('Module.is_www',array('checked'=>$checked));
		?>
	    </div>

             <div class="form-group form_margin">
                <label>Email<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Store.email_id',array('type'=>'text','class'=>'form-control','label'=>'','div'=>false,'readOnly'=>true));?>
                <span class="blue">(This email address is used for notification purpose)</span> 
             </div>

             
            <div class="form-group form_margin">
                <label>Phone no<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Store.phone',array('data-mask'=>'mobileNo','type'=>'text','class'=>'form-control phone_number','label'=>'','div'=>false));?>
                 <span class="blue">(eg. 111-111-1111)</span> 
            </div>
            
            <div class="form-group form_margin">
                <label>Address<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Store.address',array('type'=>'text','class'=>'form-control','label'=>'','div'=>false));?>
            </div>
            
            <div class="form-group form_margin">
                <label>City<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Store.city',array('type'=>'text','class'=>'form-control','label'=>'','div'=>false));?>
            </div>
            
            <div class="form-group form_margin">
                <label>State<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Store.state',array('type'=>'text','class'=>'form-control','label'=>'','div'=>false));?>
            </div>
            
            <div class="form-group form_margin">
                <label>Zipcode<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Store.zipcode',array('type'=>'text','class'=>'form-control','label'=>'','div'=>false));?>
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
                
                echo $this->Form->input('User.email',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Your Email','label'=>'','div'=>false,'required'=>true,'autocomplete' => 'off','readOnly'=>true));            
                  echo $this->Form->error('User.email'); ?>
                <span class="blue">(This email address is used for login)</span>   
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
    $(document).ready(function() {
        $(".phone_number").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {	       
                  return false;
        }
    });
    $("[data-mask='mobileNo']").mask("(999) 999-9999");
      	$("#addstore").validate({
            rules: {
                "data[Merchant][id]": {
                    required: true,                   
                },                
                "data[Store][merchant_number]": {
                    required: true, 
                    minlength:4,
                    maxlength:8,
                    remote: 
                    {
                      url: '/super/checkMerchantNumber',
                      data:
                      {
                            StoreDbId: function()
                          {
                              return <?php echo $id;?>
                          }
                      }
                    }                
                },
                "data[Store][store_name]": {
                    required: true,                   
                },
		"data[Store][store_url]": {
                    required: true,    
                    remote:
                    {
                      url: '/super/checkAllDomainsStore',
                      data:
                      {
                            StoreDomainId: function()
                          {
                              return <?php echo $id;?>
                          }
                      }
                    }
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
                 "data[User][lname]": {
                    required: false,
                    lettersonly: true, 
                },
                 "data[User][email]": {
                    required: true,
                    email: true,  
                },
                "data[User][phone]": { 
                required: true,
                
                },  
            },
            messages: {               
                "data[Merchant][id]": {
                    required: "Please select merchant name",                    
                },
                "data[Store][store_name]": {
                    required: "Please enter store name",                    
                },
                "data[Store][merchant_number]": {
                    required: "Please enter merchant unique number",
                    remote:"Merchant number already exists",
                },
		"data[Store][store_url]": {
                    required: "Please enter domain name",    
                    remote:"Domain name already exists",
                }, 
                "data[Store][email_id]": {
                    required: "Please enter Email",
                    email:"Please enter valid email",
                },
                "data[Store][phone]": {
                    required: "Contact number required",                  
                },                
                "data[Store][address]": {
                    required: "Please enter Email",
                    email:"Please enter valid email",
                },
                "data[Store][city]": {
                    required: "Please enter store name",                    
                },                
                "data[Store][state]": {
                    required: "Please enter Email",
                    email:"Please enter valid email id ",
                },                
                "data[Store][zipcode]": {
                    required: "Please enter Email",
                    email:"Please enter valid email id ",
                },
                "data[User][lname]": {
                    required: "Please enter your last name",
                    lettersonly:"Only alphabates Allowed",
                },
                "data[User][email]": {
                    required: "Please enter your email",
                    email:"Please enter valid email",
                    remote:"Email Already exist",
                },
                 "data[User][phone]": {
                    required: "Contact number required",  
                },
               
            }
        });
    });
</script>    