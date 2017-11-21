    jQuery(document).ready(function()
    {
        jQuery("#changePasswordId").validate(
        {	
            errorElement: "div",
            rules: {	                 
                "data[Admin][old_password]": {
                    required: true,
                    minlength : 6
                },
                "data[Admin][password]": {
                    required: true,
                    minlength : 6
                },
                "data[Admin][confirm_password]": {
                    required: true,
                    minlength : 6,
                    equalTo: "#AdminPassword"
                }
            },
             messages: {
                "data[Admin][old_password]": {
                    required: "Please enter old password.",
                    minlength: "Please enter at least 6 characters password."
                },
                "data[Admin][password]": {
                    required: "please enter new password.",
                    minlength: "Please enter at least 6 characters password."
                },
                "data[Admin][confirm_password]": {
                    required: "please enter confirm password.",
                    minlength: "Please enter at least 6 characters password.",
                    equalTo: "New password and confirm password do not match"
                }
            }
         
        });
    }); 
    