    <div class="row">
            <div class="col-lg-6">
                <h3>Update Subscription Payment</h3> 
                <?php echo $this->Session->flash();?>   
            </div> 
            <div class="col-lg-6">                        
                <div class="addbutton">                
                        <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
                </div>
            </div>
    </div>   
    <div class="row">        
            <?php echo $this->Form->create('hqs', array('inputDefaults' => array('label' => false, 'div'=>false, 'required' => false, 'error' => false, 'legend' => false,'autocomplete' => 'off'),'id'=>'addOffer'));?>
        <div class="col-lg-6">
             <?php echo $this->Form->input('StorePayment.id', array('type' => 'hidden')); ?>  
            <?php echo $this->Form->input('StorePayment.invoice_number', array('type' => 'hidden')); ?>  
        
	    <div class="form-group form_margin">		 
                <label>Store<span class="required"> * </span></label>               
              
              <?php                    
                $merchantList=$this->Common->getHQStores($merchantId);
                echo $this->Form->input('StorePayment.store_id',array('options'=>$merchantList,'class'=>'form-control','div'=>false,'empty'=>'Please select Store'));
	         echo $this->Form->error('StorePayment.store_id'); 
	    ?>                
            </div>   
        

            <div class="form-group form_margin store-url hidden">		 
                <label>Store Url</label>
                <div id="store_url"></div>
            </div> 
	    
            <div class="form-group form_margin">		 
                <label>Type<span class="required"> * </span></label>               
              
	   <?php
                echo $this->Form->input('StorePayment.plan_id',array('options'=>$plan,'class'=>'form-control','div'=>false,'empty'=>'Please select Type'));
                  echo $this->Form->error('StorePayment.plan_id'); ?>
            </div>
        
        
            <div class="form-group form_margin store-payment-date <?php echo (isset($this->request->data['StorePayment']['plan_id']) && $this->request->data['StorePayment']['plan_id'] != 5 ? 'hidden' : ''); ?>">
                <label>Payment Date<span class="required"> * </span></label>  
                <?php
                echo $this->Form->input('StorePayment.payment_date', array('type' => 'text', 'class' => 'form-control', 'div' => false, 'readonly' => true, 'Placeholder' => 'Select Payment Date', 'value' => (isset($this->request->data['StorePayment']['payment_date']) && $this->request->data['StorePayment']['payment_date'] != '' ? $this->Dateform->us_format($this->request->data['StorePayment']['payment_date']) : '') ));
                echo $this->Form->error('StorePayment.payment_date');
                ?>
            </div>
        
            <div class="form-group form_margin">		 
                <label style="font-weight:bold;">Payment Type </label><br>
                <div class=" store-checkbox">
                    <?php
                    echo $this->Form->input('StorePayment.payment_type', array('type' => 'radio', 'options' => array(1 => 'One-Time', 2 => 'Recurring'), 'class' => '', 'div' => false, 'label' => true)); ?>
                </div>
            </div>
            
            
               <div class="form-group form_margin">		 
                <label>Status<span class="required"> * </span></label>               
              
	   <?php
                echo $this->Form->input('StorePayment.payment_status',array('options'=>array('Paid'=>'Paid','Invoice Created'=>'Invoice Created'),'class'=>'form-control','div'=>false,'empty'=>'Please select Payment Status'));
                  echo $this->Form->error('StorePayment.payment_status'); ?>
            </div>    
	 
	     
	    <div class="form-group form_margin">		 
                <label>Amount ($)<span class="required"> * </span></label>               
              
		<?php echo $this->Form->input('StorePayment.amount',array('type'=>'text','class'=>'form-control valid serialize','label'=>false,'div'=>false,'Placeholder'=>'Enter Amount','autocomplete' => 'off'));
		 echo $this->Form->error('StorePayment.amount');
		?>
           
	   
	    </div>  
        
            <div class="form-group form_spacing">		 
                <label>Comments</label>
                <?php
                echo $this->Form->input('StorePayment.comments', array('type' => 'textarea', 'class' => 'form-control valid serialize', 'label' => false, 'div' => false, 'Placeholder' => 'Enter Comments'));
                ?>
            </div>
	   
	    
	    <div id="dynamicItems" class="form-group form_spacing">               
                
            </div>
	    
	   
	        
            <?php echo $this->Form->button('Save', array('type' => 'submit','class' => 'btn btn-default'));?>             
            <?php echo $this->Html->link('Cancel', "/hq/storePaymentList/" . (isset($this->request->data['StorePayment']['store_id']) && $this->request->data['StorePayment']['store_id'] != 0 ? $this->Encryption->encode($this->request->data['StorePayment']['store_id']) : ''), array("class" => "btn btn-default",'escape' => false)); ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </div><!-- /.row -->
    
    
    <script>
    $(document).ready(function() {
        var storeDefaultId = '<?php echo (isset($this->request->data['StorePayment']['store_id']) ? $this->request->data['StorePayment']['store_id'] : ''); ?>';
        if(storeDefaultId != '')
        {
            getStoreUrl(storeDefaultId);
        }
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
              debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
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
                "data[StorePayment][comments]": {
                    maxlength: 500,
                }
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
                "data[MerchantPayment][comments]": {
                    maxlength: "Max Length 500.",
                }
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
	 
	$('#StorePaymentAmount').keyup(function () {
		this.value = this.value.replace(/[^0-9.,]/g,'');
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
        
	$('#StorePaymentPlanId').on('change', function(){
            var planId = $(this).val();
            if(planId == 5)
            {
                $(".store-payment-date").removeClass('hidden');
                $("#StorePaymentPaymentDate").addClass('required').attr('data-msg-required', 'Please select date.');
            } else {
                $(".store-payment-date").addClass('hidden');
                $("#StorePaymentPaymentDate").removeClass('required').removeAttr('data-msg-required');
            }
        });
        
        $('#StorePaymentStoreId').on('change', function(){
            var storeId = $(this).val();
            getStoreUrl(storeId);
        });
    });
    
    function getStoreUrl(storeId)
    {
        $.ajax({
            type        : 'post',
            dataType    : 'json',
            url         : '/hq/getStoreUrl',
            data        : {
                storeId  : storeId
            },
            success     : function (result) {
                if(result)
                {
                    if(result.Store.store_url != '')
                    {
                        $("#store_url").html('<a href="' + result.Store.store_url + '" target="_blank">' + result.Store.store_url + '</a>');
                        $(".store-url").removeClass('hidden');
                    } else {
                        $(".store-url").addClass('hidden');
                    }
                } else {
                    $(".store-url").addClass('hidden');
                }
            }
        });
    }
</script>
<style>
    
    .store-checkbox label{
        margin-right: 20px;
        padding-left: 5px;
        font-weight: normal;
    }
</style>