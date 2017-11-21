    jQuery(document).ready(function()
    {
        jQuery("#loginId").validate(
        {	           
            errorElement: "div",
            rules: {	                 
                "data[Admin][email]": {
                    required: true,
                    email: true
                },
                "data[Admin][password]": {
                    required: true,
                    minlength : 6
                }
            },
             messages: {
                "data[Admin][email]": {
                    required: "This field is required."
                },
                "data[Admin][password]": {
                    required: "This field is required.",
                    minlength: "Please enter at least 6 characters password."
                }
            }
         
        });
    }); 
    