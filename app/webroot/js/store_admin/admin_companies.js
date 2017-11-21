    jQuery(document).ready(function()
    {
        jQuery("#addCompany").validate(
        {	
            errorElement: "span",
            rules: {	                 
                "data[Company][name]": {
                    required: true
                },
                "data[Company][address]": {
                    required: true
                },
                 "data[Company][email]": {
                    required: true,
                    email: true
                },               
                "data[Company][phone_no]": {
                    required: true,
                    number :true
                },
                "data[Admin][password]": {
                    required: true,
                    minlength : 8
                },
				"data[Admin][confirmpassword]": {
                    required: true,
                    minlength : 8,
					equalTo : "#AdminPassword"
                },
                 "data[Admin][first_name]": {
                    required: true
                },
                 "data[Admin][last_name]": {
                    required: true                 
                },               
                "data[Admin][phone]": {
                    required: true,
                    number :true
                },
                "data[CompanySubscription][subscription_id]": {
                    required: true
                },            
                 "data[Company][country_id]": {
                    required: true
                },              
                 "data[Company][state_id]": {
                    required: true
                },              
                 "data[Company][zip]": {
                    required: true,               
                     number :true
                }
            },
             messages: {
                "data[Company][name]": {
                    required: "Please enter company name."                    
                },
                "data[Company][address]": {
                    required: "Please enter address."                    
                },
                 "data[Admin][email]": {
                    required: "Please enter e-mail address.",
                    email: "Please enter a valid email address."
                },              
                 "data[Company][phone_no]": {
                    required: "Please enter phone number.",
                    number : "Please enter valid phone number."
                },
                "data[Admin][password]": {
                    required: "Please enter password."                    
                },				
                "data[Admin][confirmpassword]": {
                    required: "Please enter confirmpassword.",
					equalTo: "Password and Confirm password do not match."
                },
				"data[Admin][first_name]": {
                    required: "Please enter contact person's first name."
                },
                 "data[Admin][last_name]": {
                    required: "Please enter contact person's last name."               
                },               
                "data[Admin][phone_no]": {
                     required: "Please enter phone number.",
                    number : "Please enter valid phone number."
                },                
                "data[Admin][city]": {
                    required: "Please enter city."
                },
                 "data[Company][country_id]": {
                    required: "Please select country."
                },              
                 "data[Company][state_id]": {
                    required: "Please select state."
                },              
                 "data[Company][zip]": {
                    required: "Please enter zipcode.",
                    number :"Please enter valid zipcode"
                },
                "data[CompanySubscription][subscription_id]": {
                    required: "Please select a subscription plan."
                }
            }         
        });

           
    // Country state code
        if($('#CompanyCountryId :selected').val() != ''){
			fetchStates($('#CompanyCountryId :selected').val());
		}
		$('#CompanyCountryId').change(function(){
			var country_id = $('#CompanyCountryId :selected').val();
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
				
			$('#CompanyStateId').empty().append('<option value="">Select State / Province</option>');
			$('#CompanyStateId').append(newOptions);
            var oldStateVal = $('#hiddenCompanyState').val();
          
			$('#CompanyStateId').val(oldStateVal).attr('selected','selected');
		});
	}
   
   