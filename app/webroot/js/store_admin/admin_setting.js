    jQuery(document).ready(function()
    {
        jQuery("#categoryId").validate(
        {	
            errorElement: "div",
            rules: {	                 
                "data[Setting][api_key]": {
                    required: true
                },
                "data[Setting][api_user]": {
                    required: true
                },
                "data[Setting][api_pass]": {
                    required: true
                },
                "data[Setting][enviroment]": {
                    required: true
                }
            },
             messages: {
                "data[Setting][api_key]": {
                    required: "Please enter api key."
                },
                "data[Setting][api_user]": {
                    required: "Please enter api user name."
                },
                "data[Setting][api_pass]": {
                    required: "Please enter api password."
                },
                "data[Setting][enviroment]": {
                    required: "Please select enviroment "
                }
            }         
        });
        
    }); 