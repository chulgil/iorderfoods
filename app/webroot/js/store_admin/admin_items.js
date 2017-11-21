    jQuery(document).ready(function()
    {
        
        //For image check
        var totalPicsallowed = jQuery('#totalpIcCount').val();
        
        var torlpcsOld = jQuery('#oldpiccount').attr('ttlCount');

        if(parseInt(torlpcsOld) >= parseInt(totalPicsallowed)){ 
            jQuery('#mainimgcontainer').hide();
            
        }
   $.validator.addMethod('positiveNumber',
    function (value) { 
        return Number(value) >= 0;
    }, '');
        jQuery("#itemsAdd").validate(
        {	
            errorElement: "span",
            rules: {	                 
                "data[Item][name]": {
                    required: true
                },
                "data[Item][product_code]": {
                    required: true
                },                
                "data[ItemCategory][category_id][]": {
                    required: true
                },
                "data[Item][description]": {
                    required: true
                },                
                "data[Item][price]": {
                    required: true,
                    positiveNumber :true
                },                
                "data[Item][product_weight]": {
                    required: true,
                    positiveNumber   :true
                }
            },
             messages: {
                "data[Item][name]": {
                    required: "Please enter item name."                    
                },
                "data[Item][product_code]": {
                    required: "Please enter product code."                    
                },
                "data[ItemCategory][category_id][]": {
                    required: "Please select category."                    
                },
                "data[Item][price]": {
                    required: "Please enter price.",
                    positiveNumber : "Please enter valid price."
                },
                "data[Item][product_weight]": {
                    required: "Please enter per unit weight.",
                    positiveNumber : "Please enter valid weight."
                }
                
            }         
        });
        
     // function to add new uploader using add more functionality     
        jQuery(document).on('click','.addnewuploaderlink',function(){
            var totalPicLimit = jQuery('#totalpIcCount').val();
           
            var picCount = totalPicLimit - 1;
            var oldPiccount = jQuery('#oldpiccount').attr('ttlCount');
            var finalCount = picCount - oldPiccount;
         
            if(finalCount > 0){
                
                    //if(n < finalCount){
                        var html = '<div class="row">';
                        html += '<div class="col-lg-12">';
                        html += '<div class="col-lg-12">';
                        html += '<div class="form-group form-spacing newDynInputs">';
                        html += '<div class="col-lg-2 form-label">&nbsp;</div>';
                        html += '<div class="col-lg-8 form-label">';
                      //  html += '<div class="fileUpload btn btn-primary"><span>Upload</span>';
                        html += '<input id="ItemImage1" class="valid upload" type="file" style ="display:inline-block !important;" >';
                      //  html += '</div>';
                        html += '<a class="removeFilelink" href="javascript:void(0)">&nbsp;Remove</a>';
                        html += '</div>';
                        html += '<div class="col-lg-2"></div>';
                        html += '</div></div></div></div>';
                      
                        jQuery('#adduploader').append(html);
                        var n = $( "#adduploader input" ).length;
                       
                         jQuery('#totalpIcCount').val(picCount);
                        changeName();
                    //}     
            }else{ 
                jQuery('.addnewuploaderlink').hide();
            }
        });
        jQuery(document).on('click','.removeFilelink',function(){
            $( ".newDynInputs" ).last().remove();
            var totalCount =  jQuery('#totalpIcCount').val();
            var newCount= parseInt(totalCount) + parseInt(1);
           
            jQuery('#totalpIcCount').val(newCount)
                 var n = $( ".newDynInputs input" ).length;
                 
                 if(n == 2){
                    $( ".addnewuploaderlink" ).show();
                 }
            });
       // function to delete image   
        jQuery(document).on('click','.deleteimgLnk',function(){
                if (!confirm("Are you sure you want to delete this image?")) {
                      return false;
                }
  
            var itemId = jQuery(this).attr('itmId');
                $.ajax({
                    type: "GET",
                    url: "/items/delete_pic/"+itemId,
                    success: function(result){
                                if(result){
                                    var divId = '#div_image_'+itemId; 
                                    jQuery(divId).remove();
                                    var oldPiccount = jQuery('#oldpiccount').attr('ttlCount');
                                    var newcountOldPic = oldPiccount-1;
                                    jQuery('#oldpiccount').attr('ttlCount',newcountOldPic);
                                    var totalPicLimit = jQuery('#totalpIcCount').val();
                                    if(newcountOldPic < totalPicLimit){
                                       jQuery('#mainimgcontainer').show();
                                       jQuery('.addnewuploaderlink').show();
                                        
                                    }
                                }
                            }
                });
                
        });
    }); 
     function changeName(){
            $('#adduploader input').each(function(i) {
                 $(this).attr('name','data[ItemImage][image_name]['+(i+1)+']');
                
            });
        }
        
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
           url: '/admin/admins/setnewStatus/'+val1+'/'+newStatus+'/Item',
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
