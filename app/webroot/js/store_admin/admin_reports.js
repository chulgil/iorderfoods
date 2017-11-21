    jQuery(document).ready(function()
    {
        jQuery("#reportId").validate(
        {	
            errorElement: "div",
            rules: {	                 
                "data[Report][report_id]": {
                    required: true
                },
                "data[Report][start_date]": {
                    required: true
                },
                "data[Report][end_date]": {
                    required: true
                }
            },
            messages: {
                "data[Report][report_id]": {
                    required: "Please select the report type."                    
                },
                "data[Report][start_date]": {
                    required: "Please select the From date."                    
                },
                "data[Report][end_date]": {
                    required: "Please select the To date."                    
                }
                
            }         
        });
        
        jQuery( "#ReportReportId" ).change(function() {
            
            if(jQuery('#ReportReportId').val() == '1')
            {
                jQuery('#usertypeId').show();
                jQuery('#usertypeEmptyId').show();
                jQuery('#emptyContainerId').hide();
                
            }else{
                jQuery('#usertypeId').hide();
                jQuery('#usertypeEmptyId').hide();
                jQuery('#emptyContainerId').show();
            }
            
            
        });
        
        /* Calendar */
        var date = new Date();
        
        jQuery("#startDate").datepicker({ 
        dateFormat: 'dd/mm/yy',
        onSelect: function(dateText, inst) {            
            var dateStr = jQuery("#startDate").val();            
            var d = jQuery.datepicker.parseDate('dd/mm/yy', dateStr);
            d.setDate(d.getDate()); // Add one days
            jQuery('#endDate').datepicker('setDate', d);
            setTimeout("jQuery('#endDate').focus()", 10);
            
            getFromCurrentDate = jQuery(this).datepicker('getDate');
            getFromCurrentDateTime = new Date(getFromCurrentDate.getTime());
            getFromCurrentDateTime.setDate(getFromCurrentDateTime.getDate());
            jQuery("#endDate").datepicker("option", "minDate", getFromCurrentDateTime);
        }
                
        });
        
        jQuery("#endDate").datepicker({ 
            dateFormat: 'dd/mm/yy'            
        });
        
        /* Calendar */
        
    }); 
    