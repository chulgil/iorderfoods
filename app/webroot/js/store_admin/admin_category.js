    jQuery(document).ready(function()
    {
        jQuery("#categoryId").validate(
        {	
            errorElement: "div",
            rules: {	                 
                "data[Skill][name]": {
                    required: true
                }/*,
                "data[Category][parent_id]": {
                    required: true
                }*/
            },
             messages: {
                "data[Skill][name]": {
                    required: "Please enter the skill name."                    
                }
            }         
        });
        
    }); 