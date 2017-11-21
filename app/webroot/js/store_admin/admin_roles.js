    jQuery(document).ready(function()
    {
        jQuery("#adminRoleId").validate(
        {	
            errorElement: "div",
            rules: {	                 
                "data[AdminRole][role_name]": {
                    required: true
                },
                "data[AdminRole][role_description]": {
                    required: true
                }
            },
             messages: {
                "data[AdminRole][role_name]": {
                    required: "Please enter role name."                    
                },
                "data[AdminRole][role_description]": {
                    required: "Please enter description."                    
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
           url: '/admin/admins/setnewStatus/'+val1+'/'+newStatus+'/AdminRole',
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