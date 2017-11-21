    jQuery(document).ready(function()
    {
        jQuery("#templateId").validate(
        {	
            errorElement: "div",
            rules: {	                 
                "data[NewsletterTemplate][title]": {
                    required: true
                },
                "data[NewsletterTemplate][template]": {
                    required: true
                }
            },
             messages: {
                "data[NewsletterTemplate][title]": {
                    required: "Please enter the template name."                    
                },
                "data[NewsletterTemplate][template]": {
                    required: "Please enter the template content."                    
                }
            }         
        });
        
        jQuery("#newsletterId").validate(
        {	
            errorElement: "div",
            rules: {	                 
                "data[Newsletter][title]": {
                    required: true
                },
                "data[Newsletter][description]": {
                    required: true
                },
                "data[Newsletter][send_type]": {
                    required: true
                }
            },
             messages: {
                "data[Newsletter][title]": {
                    required: "Please enter newsletter subject."                    
                },
                "data[Newsletter][description]": {
                    required: "Please enter newsletter content."                    
                },
                "data[Newsletter][send_type]": {
                    required: "Please select send type."      
                }
            }         
        });
    }); 
    