
    <div class="row">
            <div class="col-lg-6">
                <h3>Request New Store</h3> 
                <?php echo $this->Session->flash();?>   
            </div> 
            <div class="col-lg-6">                        
                <div class="addbutton">                
                        <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
                </div>
            </div>
    </div>   
    <div class="row">        
            <?php echo $this->Form->create('hq', array('inputDefaults' => array('label' => false, 'div'=>false, 'required' => false, 'error' => false, 'legend' => false,'autocomplete' => 'off'),'id'=>'storeAdd'));?>
        <div class="col-lg-6">            
	    <div class="form-group form_margin">		 
                <label>Store Name<span class="required"> * </span></label>               
              
	   <?php echo $this->Form->input('MerchantStoreRequest.store_name',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Store Name','label'=>'','div'=>false));
                  echo $this->Form->error('MerchantStoreRequest.store_name'); ?>
            </div>
	
	   <div class="form-group form_margin">		 
                <label>Email<span class="required"> * </span></label>               
		<?php echo $this->Form->input('MerchantStoreRequest.email',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Email','label'=>'','div'=>false));?>
		
                <?php  echo $this->Form->error('MerchantStoreRequest.email'); ?>
            </div>
	   
            <div class="form-group form_margin">		 
                <label>Phone No.<span class="required"> * </span></label>               
		<?php echo $this->Form->input('MerchantStoreRequest.phone',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Phone Number','label'=>'','div'=>false));?>
		
                <?php  echo $this->Form->error('MerchantStoreRequest.phone'); ?>
            </div>
            
            <div class="form-group form_spacing">		 
                <label>Comment</label>               
		<?php echo $this->Form->input('MerchantStoreRequest.request_text',array('type'=>'textarea','class'=>'form-control valid','placeholder'=>'Enter Comment','label'=>'','div'=>false));
                  echo $this->Form->error('MerchantStoreRequest.request_text'); ?>             
            </div>   

	       	       
 
	  
            <?php echo $this->Form->button('Save', array('type' => 'submit','class' => 'btn btn-default'));?>             
            <?php echo $this->Html->link('Cancel', "/hq/storeRequestList/", array("class" => "btn btn-default",'escape' => false)); ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </div><!-- /.row -->
    
    
    <script>
    $(document).ready(function() {
	
	    $("#storeAdd").validate({
            rules: {
                "data[MerchantStoreRequest][store_name]": {
                    required: true, 
                },
                "data[MerchantStoreRequest][email]": {
                    required: true,
		     email: true,
                },
		"data[MerchantStoreRequest][phone]": {
                    required: true,
		    number:true,
                    minlength:10,
                    maxlength:12,
                }
                
            },
            messages: {
                "data[MerchantStoreRequest][store_name]": {
                    required: "Please enter store name",
                },
                "data[MerchantStoreRequest][email]": {
                    required: "Please enter email",
                },
		"data[MerchantStoreRequest][phone]": {
                    required: "Please enter phone no.",
                }                
            }
        });         
            
    });
</script>