    jQuery(document).ready(function()
    {
        jQuery("#userId").validate(
        {	
            errorElement: "span",
            rules: {	                 
                "data[User][first_name]": {
                    required: true
                },
                "data[User][last_name]": {
                    required: true
                },
                 "data[User][email]": {
                    required: true,
                    email: true
                },
                 "data[User][gender]": {
                    required: true
                },                
                 "data[User][address_1]": {
                    required: true
                },            
                 "data[User][country_id]": {
                    required: true
                },              
                 "data[User][state_id]": {
                    required: true
                },              
                 "data[User][zip]": {
                    required: true,               
                     number :true
                },              
                 "data[User][phone_no]": {
                    required: true,
                    number :true
                },
                "data[User][password]": {
                    required: true,
                    minlength : 8
                }
            },
             messages: {
                "data[User][first_name]": {
                    required: "Please enter first name."                    
                },
                "data[User][last_name]": {
                    required: "Please enter last name."                    
                },
                 "data[User][email]": {
                    required: "Please enter e-mail address.",
                    email: "Please enter a valid email address."
                },
                 "data[User][gender]": {
                    required: "Please select gender."
                },                
                 "data[User][address_1]": {
                    required: "Please enter address"
                },
                "data[User][city]": {
                    required: "Please enter city."
                },
                 "data[User][country_id]": {
                    required: "Please select country."
                },              
                 "data[User][state_id]": {
                    required: "Please select state."
                },              
                 "data[User][zip]": {
                    required: "Please enter zipcode.",
                    number :"Please enter valid zipcode"
                },              
                 "data[User][phone_no]": {
                    required: "Please enter phone number.",
                    number : "Please enter valid phone number."
                },
                "data[User][password]": {
                    required: "Please enter password.."
                    
                }
            }         
        });
    // Country state code
        if($('#UserCountryId :selected').val() != ''){
			fetchStates($('#UserCountryId :selected').val());
		}
		$('#UserCountryId').change(function(){
			var country_id = $('#UserCountryId :selected').val();
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
				
			$('#UserStateId').empty().append('<option value="">Select State / Province</option>');
			$('#UserStateId').append(newOptions);
            var oldStateVal = $('#hiddenUserState').val();
          
			$('#UserStateId').val(oldStateVal).attr('selected','selected');
		});
	}
   
 