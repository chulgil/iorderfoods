
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
            <?php echo $this->Form->create('hqs', array('inputDefaults' => array('label' => false, 'div'=>false, 'required' => false, 'error' => false, 'legend' => false,'autocomplete' => 'off'),'id'=>'storeAdd'));?>
        <div class="col-lg-6">            
	    <div class="form-group form_margin">		 
                <label>Store Name<span class="required"> * </span></label>               
              
	   <?php echo $this->Form->input('MerchantStoreRequest.store_name',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Store Name','label'=>'','div'=>false));
                  echo $this->Form->input('MerchantStoreRequest.request_status',array('type'=>'hidden','value'=>3));
		  echo $this->Form->error('MerchantStoreRequest.store_name'); ?>
            </div>
	
	   <div class="form-group form_margin">		 
                <label>Email<span class="required"> * </span></label>               
		<?php echo $this->Form->input('MerchantStoreRequest.email',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Email','label'=>'','div'=>false));?>
		
                <?php  echo $this->Form->error('MerchantStoreRequest.email'); ?>
            </div>
	   
            <div class="form-group form_margin">		 
                <label>Phone No.<span class="required"> * </span></label>               
		<?php echo $this->Form->input('MerchantStoreRequest.phone',array('data-mask'=>'mobileNo','type'=>'text','class'=>'form-control valid phone_number','placeholder'=>'Enter Phone Number','label'=>'','div'=>false));?>
		
                <?php  echo $this->Form->error('MerchantStoreRequest.phone'); ?>
                 <span class="blue">(eg. 111-111-1111)</span> 
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
	$(".phone_number").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {	       
                  return false;
        }
    });
    $("[data-mask='mobileNo']").mask("(999) 999-9999");
	
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
                    required: "Contact number required",
                }                
            }
        });         
            
    });
</script>