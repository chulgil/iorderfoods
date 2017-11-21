
    <div class="row">
            <div class="col-lg-6">
                <h3>Add Category1</h3> 
                <?php echo $this->Session->flash();?>   
            </div> 
            <div class="col-lg-6">                        
                <div class="addbutton">                
                        <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
                </div>
            </div>
    </div>   
    <div class="row">        
            <?php echo $this->Form->create('Stores', array('inputDefaults' => array('label' => false, 'div'=>false, 'required' => false, 'error' => false, 'legend' => false,'autocomplete' => 'off'),'id'=>'addCategory'));?>
        <div class="col-lg-6">            
	    <div class="form-group form_margin">
		 <?php
                                echo $this->Form->input('User.role_id',array('type'=>'hidden','value'=>$roleId));
                                echo $this->Form->input('User.store_id',array('type'=>'hidden'));
                                echo $this->Form->input('User.id',array('type'=>'hidden'));                      
                          ?>
                <label>Category Name<span class="required"> * </span></label>                
              
		<?php echo $this->Form->input('Category.name',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Category','label'=>'','div'=>false));
                  echo $this->Form->error('Category.name'); ?>
            </div>
	     <div class="form-group form_margin">
                <label>Image</label>                
              
		<?php echo $this->Form->input('Category.imgcat',array('type'=>'file','class'=>'form-control valid','placeholder'=>'Upload Image','label'=>'','div'=>false));  
                  echo $this->Form->error('Category.imgcat');?>
            </div>
        
            <div class="form-group form_margin">
                <label>Is Size<span class="required"> * </span></label>                
              
                <?php
                $options= array('1'=>'Size Only','2'=>'Size Only','3'=>'Size Only');
                
                echo $this->Form->input('Category.is_size',array('type'=>'select','class'=>'form-control valid','label'=>'','div'=>false,'required'=>true,'autocomplete' => 'off','options'=>$options));            
                ?>
            </div>
            
	    <div class="form-group form_margin">
                <label>Has Topping<span class="required"> * </span></label>      
              
                <?php               
                
                echo $this->Form->input('Category.has_topping', array(			
			'options' => array('1', '2')
		    ));      
                ?>
            </div>
	    
	    
	    <div class="form-group form_margin">
                <label>Status<span class="required"> * </span></label>      
              
                <?php               
                
                echo $this->Form->input('Category.is_active', array(			
			'options' => array('1', '2')
		    ));      
                ?>
            </div>
	    
	    <div class="form-group form_margin">
                <label>Meal<span class="required"> * </span></label>
                <?php 
                echo $this->Form->input('Category.is_meal',array('label'=>false,'div'=>false,'type'=>'checkbox','id'=>'test','class'=>'passwrd-input ','maxlength'=>'50','div'=>false));?>'))."&nbsp";?><span>Is Meal</span>  
                ?>
            </div>
	    
	    <div class="form-group form_margin">
                <label>Availability<span class="required"> * </span></label>      
                 <span>Start Time</span>  
                <?php               
                 echo $this->Form->input('Category.start_time',array('options'=>$timeOptions,'class'=>'form-control','maxlength'=>'50','div'=>false,'readonly'=>true));
                ?>
		
		<span>End Time</span>  
                <?php               
                 echo $this->Form->input('StoreHoliday.end_time',array('options'=>$timeOptions,'class'=>'form-control','maxlength'=>'50','div'=>false,'readonly'=>true));
                ?>
            </div>
	    
	        
            <?php echo $this->Form->button('Save', array('type' => 'submit','class' => 'btn btn-default'));?>             
            <?php echo $this->Html->link('Cancel', "/stores/index/", array("class" => "btn btn-default",'escape' => false)); ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </div><!-- /.row -->
    
    
    <script>
    $(document).ready(function() {
	 
	    $("#addCategory").validate({
            rules: {
                "data[Category][name]": {
                    required: true,
                    lettersonly: true, 
                },
                 "data[Category][imgcat]": {
                    required: false,
                    lettersonly: true, 
                },
                 "data[Category][is_size]": {
                    required: true,
                    email: true,
		 
                },
                "data[Category][has_topping]": {
                    required: true,
                    alphanumeric:true,
                    minlength:8,
                    maxlength:20,
                },
                 "data[Category][password_match]": { 
                required: true, equalTo: "#UserPassword", minlength: 8,maxlength:20, alphanumeric:true,
                },
                "data[Category][phone]": { 
                required: true,
                number:true,
                minlength:10,
                maxlength:12,
                
                },  
            },
            messages: {
                "data[Category][fname]": {
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
                
                "data[User][password]": {
                    required: "Please enter your password",
                },
                "data[User][password_match]": {
                    required: "Please enter your password again.",
                    equalTo:"Password not matched"
                },
                 "data[User][phone]": {
                    required: "Please enter your phone number.",
                    number:"Only numbers are allowed"
                },
               
            }
        });
    });
</script>