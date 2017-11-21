    jQuery(document).ready(function()
    {
        jQuery("#emailTemplateId").validate(
        {	
            errorElement: "div",
            rules: {	                 
                "data[Emailtemplate][name]": {
                    required: true
                }
            },
             messages: {
                "data[Emailtemplate][name]": {
                    required: "Please enter the template name."                    
                }
            }         
        });
    }); 
    