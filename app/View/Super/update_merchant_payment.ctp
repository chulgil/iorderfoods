<div class="row">
    <div class="col-lg-6">
        <h3>Update Subscription Payment</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
        </div>
    </div>
</div>   
<div class="row">
    <?php echo $this->Form->create('super', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'addOffer')); ?>
    <div class="col-lg-6">
        <?php echo $this->Form->input('MerchantPayment.id', array('type' => 'hidden')); ?>  
        <?php echo $this->Form->input('MerchantPayment.invoice_number', array('type' => 'hidden')); ?>  
        <div class="form-group form_margin">		 
            <label>Merchant<span class="required"> * </span></label>               

            <?php
            $merchantList = $this->Common->getListMerchant();
            echo $this->Form->input('MerchantPayment.merchant_id', array('options' => $merchantList, 'class' => 'form-control', 'div' => false, 'empty' => 'Please select Merchant'));
            echo $this->Form->error('MerchantPayment.merchant_id');
            ?>                
        </div>    
        <div class="form-group form_margin merchant-url hidden">		 
            <label>Merchant Url</label>
            <div id="merchant_url"></div>
        </div>
        
        <div class="form-group form_margin">		 
            <label>Type<span class="required"> * </span></label>               

            <?php
            echo $this->Form->input('MerchantPayment.plan_id', array('options' => $plan, 'class' => 'form-control', 'div' => false, 'empty' => 'Please select Type'));
            echo $this->Form->error('MerchantPayment.plan_id');
            ?>
        </div>
        
        <div class="form-group form_margin merchant-payment-date <?php echo (isset($this->request->data['MerchantPayment']['plan_id']) && $this->request->data['MerchantPayment']['plan_id'] != 5 ? 'hidden' : ''); ?>">
            <label>Payment Date<span class="required"> * </span></label>  
            <?php
            echo $this->Form->input('MerchantPayment.payment_date', array('type' => 'text', 'class' => 'form-control', 'div' => false, 'readonly' => true, 'Placeholder' => 'Select Payment Date', 'value' => (isset($this->request->data['MerchantPayment']['payment_date']) && $this->request->data['MerchantPayment']['payment_date'] != '' ? $this->Dateform->us_format($this->request->data['MerchantPayment']['payment_date']) : '') ));
            echo $this->Form->error('MerchantPayment.payment_date');
            ?>
        </div>
        
        <div class="form-group form_margin">		 
            <label style="font-weight:bold;">Payment Type </label><br>
            <div class=" merchant-checkbox">
                <?php
                echo $this->Form->input('MerchantPayment.payment_type', array('type' => 'radio', 'options' => array(1 => 'One-Time', 2 => 'Recurring'), 'class' => '', 'div' => false, 'label' => true)); ?>
            </div>
        </div>

        <div class="form-group form_margin">		 
            <label>Status<span class="required"> * </span></label>               

            <?php
            echo $this->Form->input('MerchantPayment.payment_status', array('options' => array('Paid' => 'Paid', 'Invoice Created' => 'Invoice Created'), 'class' => 'form-control', 'div' => false, 'empty' => 'Please select Payment Status'));
            echo $this->Form->error('MerchantPayment.payment_status');
            ?>
        </div>    


        <div class="form-group form_margin">		 
            <label>Amount ($)<span class="required"> * </span></label>               

            <?php
            echo $this->Form->input('MerchantPayment.amount', array('type' => 'text', 'class' => 'form-control valid serialize', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'Placeholder' => 'Enter Amount'));
            echo $this->Form->error('MerchantPayment.amount');
            ?>


        </div>  
        
        <div class="form-group form_spacing">		 
            <label>Comments</label>               

            <?php
            echo $this->Form->input('MerchantPayment.comments', array('type' => 'textarea', 'class' => 'form-control valid serialize', 'label' => false, 'div' => false, 'Placeholder' => 'Enter Comments'));
            ?>
        </div>


        <div id="dynamicItems" class="form-group form_spacing">               

        </div>


        



        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
    <?php echo $this->Html->link('Cancel', "/super/merchantPaymentList/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
<?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {
        var merchantDefaultId = '<?php echo (isset($this->request->data['MerchantPayment']['merchant_id']) ? $this->request->data['MerchantPayment']['merchant_id'] : ''); ?>';
        if(merchantDefaultId != '')
        {
             getMerchantUrl(merchantDefaultId);
        }
        $('#MerchantPaymentPaymentDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: 0,
            onSelect: function (selected) {
                $("#MerchantPaymentOfferStartDate").prev().find('div').remove();
                $("#OfferOfferEndDate").datepicker("option", "minDate", selected)
            }

        });
        $('#OfferOfferEndDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: 0,
        });
        $("#addOffer").validate({
            rules: {
                "data[MerchantPayment][amount]": {
                    required: true,
                    number: true,
                },
                "data[MerchantPayment][merchant_id]": {
                    required: true,
                },
                "data[MerchantPayment][plan_id]": {
                    required: true,
                },
                "data[MerchantPayment][payment_status]": {
                    required: true,
                },
                "data[MerchantPayment][comments]": {
                    maxlength: 500,
                }
            },
            messages: {
                "data[MerchantPayment][amount]": {
                    required: "Please enter amount",
                },
                "data[MerchantPayment][merchant_id]": {
                    required: "Please select merchant.",
                },
                "data[MerchantPayment][plan_id]": {
                    required: "Please select plan.",
                },
                "data[MerchantPayment][payment_status]": {
                    required: "Please select status.",
                },
                "data[MerchantPayment][comments]": {
                    maxlength: "Max Length 500.",
                }
            }
        });



        $('#MerchantPaymentAmount').keyup(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });
        
        
        
        
        $('#MerchantPaymentPlanId').on('change', function(){
            var planId = $(this).val();
            if(planId == 5)
            {
                $(".merchant-payment-date").removeClass('hidden');
                $("#MerchantPaymentPaymentDate").addClass('required').attr('data-msg-required', 'Please select date.');
            } else {
                $(".merchant-payment-date").addClass('hidden');
                $("#MerchantPaymentPaymentDate").removeClass('required').removeAttr('data-msg-required');
            }
        });
        
        $('#MerchantPaymentMerchantId').on('change', function(){
            var merchantId = $(this).val();
            getMerchantUrl(merchantId);
        });
    });
    
    function getMerchantUrl(merchantId){
        $.ajax({
            type        : 'post',
            dataType    : 'json',
            url         : '/super/getMerchantUrl',
            data        : {
                merchantId  : merchantId
            },
            success     : function (result) {
                if(result)
                {
                    if(result.Merchant.domain_name != '')
                    {
                        $("#merchant_url").html('<a href="' + result.Merchant.domain_name + '" target="_blank">' + result.Merchant.domain_name + '</a>');
                        $(".merchant-url").removeClass('hidden');
                    } else {
                        $(".merchant-url").addClass('hidden');
                    }
                } else {
                    $(".merchant-url").addClass('hidden');
                }
            }
        });
    }
</script>
<style>
    
    .merchant-checkbox label{
        margin-right: 20px;
        padding-left: 5px;
        font-weight: normal;
    }
</style>