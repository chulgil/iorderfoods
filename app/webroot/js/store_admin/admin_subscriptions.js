    jQuery(document).ready(function()
    {
        $.validator.addMethod('positiveNumber',
            function (value) { 
                return Number(value) >= 0;
            }, '');
        
        jQuery("#subscription_add").validate(
        {	
            errorElement: "span",
            rules: {	                 
                "data[Subscription][name]": {
                    required: true
                },
                "data[Subscription][description]": {
                    required: true
                },
                 "data[Subscription][frequency]": {
                    required: true
                },
                 "data[Subscription][amount]": {
                    required: true,
                    positiveNumber : true
                }
            },
             messages: {
                "data[Subscription][name]": {
                    required: "Please enter plan name."                    
                },
                "data[Subscription][description]": {
                    required: "Please enter description."                    
                },
                 "data[Subscription][frequency]": {
                    required: "Please select plan duration."
                },
                 "data[Subscription][amount]": {
                    required: "Please enter amount.",
                    positiveNumber : "Please enter valid price."
                }
            }         
        });

           
    }); 

    function setStatus(val1){
        var status = $("#statusHidden_"+val1).val();
        if(status ==1){
              var newStatus = 0;
              var msz = "Are you sure, you want to deactivate this record? ";
        }else{
           var newStatus = 1;
           var msz = "Are you sure, you want to activate this record?";
        }
        if (!confirm(msz)) {
                           return false;
        }
         $.ajax({
           url: '/admin/admins/setnewStatus/'+val1+'/'+newStatus+'/Subscription',
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