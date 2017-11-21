<?php $id = $this->request->data['Merchant']['id'];?>
    <div class="row">
            <div class="col-lg-6">
	    <h3>Edit Merchant</h3> 
           <?php echo $this->Session->flash();?>   
            </div> 
            <div class="col-lg-6">                        
                <div class="addbutton">                
                        <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
                </div>
            </div>
    </div>   
    <div class="row">        
            <?php echo $this->Form->create('super', array('inputDefaults' => array('label' => false, 'div'=>false, 'required' => false, 'error' => false, 'legend' => false,'autocomplete' => 'off'),'id'=>'addmerchant'));?>
        <div class="col-lg-6">
            <div class="form-group form_margin">
                <label>Merchant Name<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Merchant.name',array('type'=>'text','class'=>'form-control','label'=>'','div'=>false));?>
            </div>
	    
	    <div class="form-group form_margin">
                <label>Merchant Domain<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Merchant.domain_name',array('type'=>'text','class'=>'form-control','label'=>'','placeholder'=>'Enter Domain','div'=>false));?>
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
		<?php  echo $this->Form->input('Merchant.email',array('type'=>'text','class'=>'form-control','label'=>'','div'=>false,'readOnly'=>true));?>
                 <span class="blue">(This email address is used for notification purpose)</span> 
             </div>

             
            <div class="form-group form_margin">
                <label>Contact no.<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Merchant.phone',array('data-mask'=>'mobileNo','type'=>'text','class'=>'form-control phone_number','label'=>'','div'=>false));?>
                 <span class="blue">(eg. 111-111-1111)</span> 
            </div> 
            
            <div class="form-group form_margin">
                <label>Company Name<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Merchant.company_name',array('type'=>'text','class'=>'form-control','label'=>'','div'=>false));?>
            </div>
	    
            
            <div class="form-group form_margin">
                <label>Address<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Merchant.address',array('type'=>'text','class'=>'form-control','placeholder'=>'Enter Address','label'=>'','div'=>false));?>
            </div>
            
            <div class="form-group form_margin">
                <label>City<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Merchant.city',array('type'=>'text','class'=>'form-control','placeholder'=>'Enter City','label'=>'','div'=>false));?>
            </div>
            
            <div class="form-group form_margin">
                <label>State<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Merchant.state',array('type'=>'text','class'=>'form-control','placeholder'=>'Enter State','label'=>'','div'=>false));?>
            </div>
            
            <div class="form-group form_margin">
                <label>Zipcode<span class="required"> * </span></label> 
		<?php  echo $this->Form->input('Merchant.zipcode',array('type'=>'text','class'=>'form-control','placeholder'=>'Enter Zipcode','label'=>'','div'=>false));?>
            </div>
            <div class="form-group form_margin">
                <label>Allow transaction to HQ<span class="required"> * </span></label>                
               &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                <?php    
                echo $this->Form->input('Merchant.is_allow_transaction', array(
  'type' => 'radio',
  'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive') ,
  'default'=>1
));
                echo $this->Form->error('Merchant.is_allow_transaction');
                   ?>
            </div>
                         
            <br/>
            
            <div class="form-group form_margin">
                <label>Salutation<span class="required"> * </span></label>
		    <?php echo $this->Form->input('User.salutation',array('type'=>'select','options'=>array('Mr.'=>'Mr.','Ms.'=>'Ms.','Mrs.'=>'Mrs.'),'class'=>'form-control valid','label'=>'','div'=>false)); ?>

            </div>
	    <div class="form-group form_margin">
                <label>First Name<span class="required"> * </span></label> 
		<?php echo $this->Form->input('User.fname',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Your First Name','label'=>'','div'=>false));
                echo $this->Form->input('User.id',array('type'=>'hidden'));
		 echo $this->Form->input('Merchant.id',array('type'=>'hidden'));
		 echo $this->Form->input('Merchant.user_id',array('type'=>'hidden'));
		 echo $this->Form->input('Merchant.owner_name',array('type'=>'hidden'));

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
                <label>Mobile Phone<span class="required"> * </span></label>
                <?php 
                echo $this->Form->input('User.phone',array('data-mask'=>'mobileNo','type'=>'text','class'=>'form-control valid phone_number','placeholder'=>'Enter Mobile Phone','label'=>'','div'=>false,'required'=>true));            
                echo $this->Form->error('User.phone');
                ?>
                 <span class="blue">(eg. 111-111-1111)</span> 
               </div>    
            
            <?php echo $this->Form->button('Save', array('type' => 'submit','class' => 'btn btn-default'));  echo "&nbsp;";           
             echo $this->Form->button('Cancel', array('type' => 'button','onclick'=>"window.location.href='/super/viewMerchantDetails'",'class' => 'btn btn-default'));
	     ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </div><!-- /.row -->
    
<script>
    $(document).ready(function() {
	 $(".phone_number").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {	       
                  return false;
        }
    });
    $("[data-mask='mobileNo']").mask("(999) 999-9999");
      	$("#addmerchant").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Merchant][merchant_name]": {
                    required: true,                   
                },
		"data[Merchant][domain_name]": {
                    required: true,     
                    remote:
                    {
                      url: '/super/checkAllDomainsMerchant',
                      data:
                      {
                            MerchantDomainId: function()
                          {
                              return <?php echo $id;?>
                          }
                      }
                    }
                },
                "data[Merchant][email]": {
                    required: true,
                     email: true,
                },
                "data[Merchant][phone]": {
                   required: true,                 
                },
                "data[Merchant][address]": {
                    required: true,                   
                },
                "data[Merchant][city]": {
                    required: true,                    
                },
                "data[Merchant][state]": {
                    required: true,                    
                },
                "data[Merchant][zipcode]": {
                    required: true,                    
                },
                "data[Merchant][company_name]": {
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
                    email: true
                },
                "data[User][phone]": { 
                required: true, 
                
                },  
            },
            messages: {
            
                "data[Merchant][merchant_name]": {
                    required: "Please enter Merchant name",                    
                },
		"data[Merchant][domain_name]": {
                    required: "Please enter Merchant Domain name",
                    remote:"Domain name already exists",
                },
                "data[Merchant][email]": {
                    required: "Please enter merchant email",
                    email:"Please enter valid email",
                },                
                "data[Merchant][phone]": {
                   required: "Contact number required",                    
                },             
                "data[Merchant][address]": {
                    required: "Please enter address",
                },
                "data[Merchant][city]": {
                    required: "Please enter city name",                    
                },                
                "data[Merchant][state]": {
                    required: "Please enter state",
                },                
                "data[Merchant][zipcode]": {
                    required: "Please enter zipcode",
                },                
                "data[Merchant][company_name]": {
                    required: "Please company name",
                },
		"data[User][fname]": {
                    required: "Please enter your first name",
                    lettersonly:"Only alphabates Allowed",
                },
                 "data[User][lname]": {
                    required: "Please enter your last name",
                    lettersonly:"Only alphabates Allowed",
                },
                "data[User][email]": {
                    required: "Please enter your email",
                    email:"Please enter valid email"
                },
                 "data[User][phone]": {
                     required: "Contact number required", 
                },
               
            },highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
    });
</script>    