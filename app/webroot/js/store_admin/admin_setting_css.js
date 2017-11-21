    jQuery(document).ready(function()
    {
        
      
  
        jQuery("#cssform").validate(
        {	
            errorElement: "span",
            rules: {	                 
                "data[UiSetting][file_name]": {
                    required: true
                },
                "data[UiSetting][content]": {
                    required: true
                }
            },
             messages: {
                "data[UiSetting][file_name]": {
                    required: "Please select file name."                    
                },
                "data[UiSetting][content]": {
                    required: "Please enter content"                    
                }                
            }         
        });
        
     // function to load file content     
        jQuery(document).on('change','#uiFileName',function(){
            var selectedFile = jQuery(this).val();
			jQuery(this).parent().next('div').append('<img src= "/img/ajax-loader.gif" /> ');
          //  alert(selectedFile);
                $.ajax({
                    type: "GET",
                    url: "/settings/getContent/"+selectedFile,
                    success: function(result){
						jQuery('#uiFileName').parent().next('div').html('');
                                if(result){ 
									
                                    if(result != 0){
                                      //  $('#fileContent').attr('rows',100);
                                        $('#fileContent').val(result);
                                    }
                                }
                            }
                });
         
          
        });
    
    }); 

        
	