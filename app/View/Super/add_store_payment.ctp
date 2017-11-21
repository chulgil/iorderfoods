    <div class="row">
            <div class="col-lg-6">
                <h3>Add Subscription Payment</h3> 
                <?php echo $this->Session->flash();?>   
            </div> 
            <div class="col-lg-6">                        
                <div class="addbutton">                
                        <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
                </div>
            </div>
    </div>   
    <div class="row">        
            <?php echo $this->Form->create('super', array('inputDefaults' => array('label' => false, 'div'=>false, 'required' => false, 'error' => false, 'legend' => false,'autocomplete' => 'off'),'id'=>'addOffer'));?>
        <div class="col-lg-6">
            
	    <div class="form-group form_margin">		 
                <label>Store<span class="required"> * </span></label>               
              
              <?php                    
                $merchantList=$this->Common->getHQStores();
                echo $this->Form->input('StorePayment.store_id',array('options'=>$merchantList,'class'=>'form-control','div'=>false,'empty'=>'Please select Merchant'));
	         echo $this->Form->error('StorePayment.store_id'); 
	    ?>                
            </div>    
	    
            <div class="form-group form_margin">		 
                <label>Type<span class="required"> * </span></label>               
              
	   <?php
                echo $this->Form->input('StorePayment.plan_id',array('options'=>$plan,'class'=>'form-control','div'=>false,'empty'=>'Please select Type'));
                  echo $this->Form->error('StorePayment.plan_id'); ?>
            </div>
            
            
               <div class="form-group form_margin">		 
                <label>Status<span class="required"> * </span></label>               
              
	   <?php
                echo $this->Form->input('StorePayment.payment_status',array('options'=>array('Paid'=>'Paid','Not Paid'=>'Not Paid'),'class'=>'form-control','div'=>false,'empty'=>'Please select Payment Status'));
                  echo $this->Form->error('StorePayment.payment_status'); ?>
            </div>    
	 
	     
	    <div class="form-group form_spacing">		 
                <label>Amount ($)<span class="required"> * </span></label>               
              
		<?php echo $this->Form->input('StorePayment.amount',array('type'=>'text','class'=>'form-control valid serialize','label'=>false,'div'=>false,'autocomplete' => 'off'));
		 echo $this->Form->error('StorePayment.amount');
		?>
           
	   
	    </div>  
	   
	    
	    <div id="dynamicItems" class="form-group form_spacing">               
                
            </div>
        
	    
	    <div class="form-group form_margin">
               <label>Payment Date<span class="required"> * </span></label>  
                <?php
                    echo $this->Form->input('StorePayment.payment_date',array('type'=>'text','class'=>'form-control','div'=>false,'readonly'=>true));
                    echo $this->Form->error('StorePayment.payment_date');

		
		?>
            </div>
	    
	   
	        
            <?php echo $this->Form->button('Save', array('type' => 'submit','class' => 'btn btn-default'));?>             
            <?php echo $this->Html->link('Cancel', "/super/storePaymentList/", array("class" => "btn btn-default",'escape' => false)); ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </div><!-- /.row -->
    
    
    <script>
    $(document).ready(function() {
	
	$('#StorePaymentPaymentDate').datepicker({   
	   dateFormat: 'mm-dd-yy',
	    minDate: 0,
	     onSelect:function(selected){
                $("#StorePaymentOfferStartDate").prev().find('div').remove();
                $("#OfferOfferEndDate").datepicker("option","minDate", selected)
               }
	   
	});
	$('#OfferOfferEndDate').datepicker({
	   
	   dateFormat: 'mm-dd-yy',
	    minDate: 0,
	   
	});
         $("#addOffer").validate({
            rules: {
                "data[StorePayment][amount]": {
                    required: true,
		    number: true,
                },
		"data[StorePayment][store_id]": {
                    required: true,		   
                },
		"data[StorePayment][plan_id]": {
                    required: true,		   
                },
		"data[StorePayment][payment_status]": {
                    required: true,		   
                },
		"data[StorePayment][payment_date]": {
                    required: true,		   
                },
            },
            messages: {
                "data[StorePayment][amount]": {
                    required: "Please enter amount",
                },
		"data[StorePayment][store_id]": {
                    required: "Please select store.",
                },
		"data[StorePayment][plan_id]": {
                    required: "Please select plan.",
                },
		"data[StorePayment][payment_status]": {
                    required: "Please select status.",
                },
		"data[StorePayment][payment_date]": {
                    required: "Please select date.",
                },
            }
        });
	 
	
	
	$('#StorePaymentAmount').keyup(function(){
      var str = $(this).val();
      if ($.trim(str) === '') {
         $(this).val('');
         $(this).css('border', '1px solid red');
         $(this).focus();
      }else{
         $(this).css('border', '');
      }
      });
	
    });
</script>