    jQuery(document).ready(function()
    {
        jQuery("#itemsAdd").validate(
        {	
            errorElement: "div",
            rules: {	                 
                "data[Company][site_name]": {
                    required: true
                }/*,
                "data[Company][logo]": {
                    required: true
                }*/
            },
             messages: {
                "data[Company][site_name]": {
                    required: "Please enter site name."                    
                },
                "data[Company][logo]": {
                    required: "Upload a logo for site."
                }
            }         
        });
    }); 
    