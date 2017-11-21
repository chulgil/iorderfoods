    jQuery(document).ready(function()
    {
        jQuery("#brandId").validate(
        {	
            errorElement: "div",
            rules: {	                 
                "data[Brand][brand]": {
                    required: true
                }
            },
             messages: {
                "data[Brand][brand]": {
                    required: "Please enter the brand name."                    
                }
            }         
        });
    }); 
    