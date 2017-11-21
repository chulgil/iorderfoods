    jQuery(document).ready(function()
    {
        $.validator.addMethod('positiveNumber',
		function (value) { 
			return Number(value) > 0;
		}, ''); 
        jQuery("#testPageID").validate({
            rules: {
                
                "data[Order][name_on_card]":{
                    required: true
                },
                "data[Order][cc_number]":{
                    required: true,
                    creditcard: true
                },
                "data[Order][exp_month]":{
                    required: true
                },
                "data[Order][exp_year]":{
                    required: true
                },
                "data[Order][card_type]":{
                    required: true
                },
				 "data[Order][amount]":{
                    required: true,
					positiveNumber   :true
                },
                "data[Order][cvv]":{
                    required: true,
                    number: true,
                    minlength: 3
                },
                "data[Order][billing_name]":{
                    required : true,
                    alphanumeric:true    
                },
                "data[Order][billing_street_1]":{
                    required:true    
                },
                "data[Order][billing_city]":{
                    required: true,   
                },
                "data[Order][billing_state]":{
                    required: true,   
                },
                "data[Order][billing_zip]":{
                    required: true,
                    number: true,
                },
                "data[Order][billing_phone]":{
                    required: true
                }
                
            },
            messages:{
               
                "data[Order][name_on_card]":{
                    required:"Please enter card holder name."
                },
                "data[Order][cc_number]":{
                    required: "Please enter valid credit card number.",
                    creditcard: "Please enter a valid credit card."
                },
                "data[Order][exp_month]":{
                    required: "Please select month in which credit card number will expire."
                },
                "data[Order][exp_year]":{
                    required: "Please select year in which credit card number will expire."
                },
                "data[Order][card_type]":{
                    required: "Please enter card type."
                },
                "data[Order][cvv]":{
                    required: "Please enter cvv number.",
                    number : "Please enter valid cvv number.",
                    
                },
                "data[Order][billing_name]":{
                    required: "Please enter your name.",
                    alphanumeric:"Only alphanumeric is allowed"
                },
                "data[Order][billing_street_1]":{
                    required:"Please enter billing address."
                },
                "data[Order][billing_city]":{
                    required:"Please enter city for billing address."
                },
                "data[Order][billing_state]":{
                    required:"Please select state for billing address."
                },
                "data[Order][billing_zip]":{
                    required:"Please enter zip code for billing address.",
                    number : "Please enter valid zip code."
                },
                "data[Order][billing_phone]":{
                    required:"Please enter phone number."
                },
				 "data[Order][amount]":{
                    required: "Please enter amount",
					positiveNumber   : "Please enter a valid amount"
                }
				
            },
            errorElement: "span",
		});
    // Country state code
        if($('#OrderCountryId :selected').val() != ''){
			fetchStates($('#OrderCountryId :selected').val());
		}
		$('#OrderCountryId').change(function(){
			var country_id = $('#OrderCountryId :selected').val();
			fetchStates(country_id);
		});
    
           
    }); 

    function fetchStates(country_id){
		var newOptions = '';
		$("#stateloader").show();
		$.ajax({
			type : "POST",
			url  : "/users/get_states",
			data : {'data[State][country_id]':country_id}
		})
		.success(function(sdata){
			$("#stateloader").hide();
			var obj = jQuery.parseJSON(sdata); 
			
			$.each(obj, function(key, value) { 
			  newOptions = newOptions+'<option value="'+value.State.id+'">'+value.State.name+'</option>';	
			});
				
			$('#OrderStateId').empty().append('<option value="">Select State / Province</option>');
			$('#OrderStateId').append(newOptions);
           
		});
	}
   
 