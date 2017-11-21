    jQuery(document).ready(function()
    {
        jQuery("#editProfileId").validate(
        {	
            errorElement: "div",
            rules: {	                 
                "data[Admin][first_name]": {
                    required: true
                },
                 "data[Admin][email]": {
                    required: true,
                    email: true
                },
                "data[Admin][phone]": {
                    number: true                    
                },
                "data[Admin][password]": {
                    required: true,
                    minlength : 8
                },
                "data[Admin][admin_role_id]": {
                    required: true
                 
                }
                
            },
             messages: {
                "data[Admin][first_name]": {
                    required: "Please enter the first name."                    
                },
                 "data[Admin][email]": {
                    required: "Please enter a valid email address."
                },
                "data[Admin][phone]": {
                    number: "Please enter the valid phone number."                    
                },
                "data[Admin][password]": {
                    required: "Please enter password."
                    
                },
                "data[Admin][admin_role_id]": {
                    required: "Please select a role."
                    
                }
            }
         
        });
    }); 
     function setStatus(val1){
        var status = $("#statusHidden_"+val1).val();
        if(status ==1){
              var newStatus = 0;
              var msz = "Are you sure you want to deactivate this record? ";
        }else{
           var newStatus = 1;
           var msz = "Are you sure you want to activate this record?";
        }
        if (!confirm(msz)) {
                           return false;
        }
         $.ajax({
           url: '/admin/admins/setnewStatus/'+val1+'/'+newStatus+'/Admin',
           success: function(data) { 
            // $('.result').html(data);
                 if(data == 0){
                    imgdata = "<img src = '/img/admin/inactive.png' />";
                 }else{
                    imgdata = "<img src = '/img/admin/active.png' />";
                 }
                 $('#link_status_'+val1).html(imgdata);
                 $('#statusHidden_'+val1).val(data);
         
           }
         });
    }