    jQuery(document).ready(function()
    {
        jQuery("#forgotPasswordId").validate(
        {	           
            errorElement: "div",
            rules: {	                 
                "data[Admin][email]": {
                    required: true,
                    email: true
                }
            },
             messages: {
                "data[Admin][email]": {
                    required: "This field is required."
                }
            }         
        });
    });    