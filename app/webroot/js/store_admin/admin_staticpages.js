  jQuery(document).ready(function()
    {
        jQuery("#staticPageFormId").validate(
        {	
            errorElement: "div",
            rules: {	                 
                "data[Staticpage][title]": {
                    required: true
                }
            },
             messages: {
                "data[Staticpage][title]": {
                    required: "Please enter the page title."                    
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
           url: '/admin/admins/setnewStatus/'+val1+'/'+newStatus+'/Staticpage',
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