<?php
$amntdate = ''; $amnt = '' ; $tamnt = 0 ;
    if($type == 1){       
       echo $this->element('/superreport/customer/daily');
    }elseif($type == 2){
        //echo "In progress";
       echo $this->element('/superreport/customer/weekly'); 
    }elseif($type == 3){
       echo $this->element('/superreport/customer/monthly'); 
    }elseif($type == 4){
       echo $this->element('/superreport/customer/yearly'); 
    }elseif($type == 5){
       echo $this->element('/superreport/customer/life_time'); 
    } 
   
if(isset($storeTime) && !empty($storeTime)){
$date = strtotime($storeTime); // Change to whatever date you need    
}else{
$date = time(); // Change to whatever date you need    
}
$dotw = $dotw = date('w', $date);
$start = ($dotw == 6 /* Saturday */) ? $date : strtotime('last Saturday', $date);
$start= date('Y-m-d',$start);
$start= strtotime($start);
$step = '+1 day';
$start =  strtotime($step, $start);
$startDate = date('Y-m-d',$start);
$end = ($dotw == 5 /* Friday */) ? $date : strtotime('next Friday', $date);
$end= date('Y-m-d',$end);
$end= strtotime($end);
$step = '+1 day';
$end =  strtotime($step, $end);
$endDate = date('Y-m-d',$end); 
    
?>

<div class="row">
    <div class="col-lg-12">
        <h3>Customer Report</h3>
        <?php echo $this->Session->flash(); ?> 
        <?php echo $this->Form->create('Report', array('url' => array('controller' => 'superreports', 'action' => 'customerReport','id'=>'one')));  ?>
	    <div class="row padding_btm_20">
	         <div class="col-lg-2">
		  <label>Select Store</label>
		     <?php                    
                $merchantList=$this->Common->getStoreList();
                echo $this->Form->input('Store.id',array('options'=>$merchantList,'label' => false,'class'=>'form-control','div'=>false,'empty'=>'Please Select Store'));
	    ?>
	       </div>
                <div class="col-lg-2">	
                    <label>Type of Report</label>
		    <?php		    
		    $options=array('1'=>'Daily','2'=>'Weekly','3'=>'Monthly','4'=>'Yearly','5'=>'Life Time');
		    echo $this->Form->input('type',array('id'=>'DataType','type'=>'select','class'=>'form-control valid','label'=>false,'div'=>false,'options'=>$options)); ?>		
                </div>
		
	
                <div class="col-lg-2" id="start-daily-data">
                    <label>Start Date</label>
                    <?php echo $this->Form->input('startdate',array('label' => false,'div' => false,'class' => 'form-control date-select','value'=>$startdate,'readonly'=>true));?>
                </div>
                 <div class="col-lg-2" id="end-daily-data">
                <label>End Date</label>
                    <?php echo $this->Form->input('enddate',array('label' => false,'div' => false,'class' => 'form-control date-select','value'=>$enddate,'readonly'=>true));?>
                </div>
                <div id="start-weekly-data">
                    <div class="col-lg-2">
                        <label>Select Start Week</label>
                        <?php echo $this->Form->input('date_start_from',array('label' => false,'div' => false,'class' => 'form-control week-picker','value'=>@$startFrom,'readonly'=>true,'default'=>$startDate));?>
                      <span class="blue">  <label>Week Range:</label> (<span id="startDate"><?php echo @$startFrom;?></span>  -  <span id="endDate"><?php echo @$endFrom;?></span>)</span>
                    </div>
                </div>
                
                <div id="end-weekly-data">
                    <div class="col-lg-2">
                        <label>Select End Week</label>
                        <?php echo $this->Form->input('date_end_from',array('label' => false,'div' => false,'class' => 'form-control week-picker','value'=>@$endFrom,'readonly'=>true,'default'=>$endDate));?>
                      </div>
                </div>
                <div id="monthly-data">
                    <?php
                    $month=array('1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');
                    for($y = 2010; $y <= date('Y'); $y++){
                        $yr[$y] = $y;
                    }
                    for($m = 1; $m <= 12; $m++){
                        $mth[$m] = $month[$m];
                    } ?>
                    <div class="col-lg-2">
                        <label>Month</label>
                        <?php  echo $this->Form->input('month',array('type'=>'select','class'=>'form-control','label'=>false,'div'=>false,'options'=>$mth,'value'=>$Month)); ?>	
                    </div>
                    <div class="col-lg-2">
                        <label>Year</label>
                        <?php echo $this->Form->input('year',array('type'=>'select','class'=>'form-control','label'=>false,'div'=>false,'options'=>$yr,'value'=>$Year)); ?>		
                    </div>
                </div>
                <div id="yearly-data">
                    <?php	
                    for($y = 2010; $y <= date('Y'); $y++){
                        $yr[$y] = $y;
                    } ?>
                    <div class="col-lg-2">
                        <label>From Year</label>
                        <?php echo $this->Form->input('from_year',array('type'=>'select','class'=>'form-control','label'=>false,'div'=>false,'options'=>$yr,'value'=>$yearFrom)); ?>	
                    </div>
                    <div class="col-lg-2">
                        <label>To Year</label>
                        <?php echo $this->Form->input('to_year',array('type'=>'select','class'=>'form-control','label'=>false,'div'=>false,'options'=>$yr,'value'=>$yearTo)); ?>		
                    </div>
                </div>
                <div class="col-lg-1">	
                    <label>&nbsp;&nbsp;&nbsp;</label>
                    <?php echo $this->Form->button('Generate Report', array('type' => 'submit','class' => 'btn btn-default','id'=>'btnSubmit'));?>
                </div>
                <div style="float:right;">
                <div class="col-lg-1">	
                    <label>&nbsp;&nbsp;&nbsp;&nbsp;</label>
                    <?php
		     if(!empty($storeId)){
		    echo $this->Html->link('Download Excel', array('controller'=>'superreports','action'=>'customerReportDownload',$storeId,$type,$startdate,$enddate,$Month,$Year,$yearFrom,$yearTo),array('class' => 'btn btn-default'));
		     }else{
		    echo $this->Html->link('Download Excel', array('controller'=>'superreports','action'=>'supecustomerReportDownload',$type,$startdate,$enddate,$Month,$Year,$yearFrom,$yearTo),array('class' => 'btn btn-default'));

		     }
		    ?>
                </div>
           </div></div>
        <?php echo $this->Form->end();?>
        
        
          <div>
            <!--Content-->             
            <div id="container" style="min-width: 310px; height: 600px; margin: 0 auto"></div>
                
          </div>
          <br><br>
          <div class="row">
                    <div class="col-sm-6">
                        <?php echo $this->Paginator->counter('Page {:page} of {:pages}');?> 
                    </div>
                    <div class="col-sm-6 text-right">
                        <?php echo $this->Paginator->counter('showing {:current} records out of {:count} total');?> 
                    </div>
                </div>
          <?php
	
	  echo  $this->element('superreport/customer/index') ;
	  
   
	  ?>
    </div>
   
</div>




<script>	
$(document).ready(function(){
    $('#StoreId').on('change', function () {
            var storeId=$('#StoreId').val();
            $.ajax({
                type: 'post',
                url: '/hqreports/getcurrentStoreTime',
                data: {storeId:storeId},
                success: function (result) {
                    if (result) {
                        $("#ReportStartdate").val(result);
                        $("#ReportEnddate").val(result);
                    }
                },
                complete: function () {
                     $("#ReportCustomerReportForm").submit();
                }
            });
            
        });
    
    $('.highcharts-container').find("text[text-anchor='end']:last").hide();
    var type = '<?php echo $type;?>';
    if(type == 1){
       $('#start-daily-data').css('display', 'block');
        $('#end-daily-data').css('display', 'block');
        $('#start-weekly-data').css('display', 'none');
        $('#end-weekly-data').css('display', 'none');    
        $('#monthly-data').css('display', 'none');
        $('#yearly-data').css('display', 'none');
    } else if(type == 2){
        $('#start-daily-data').css('display', 'none');
        $('#end-daily-data').css('display', 'none');
        $('#start-weekly-data').css('display', 'block');
        $('#end-weekly-data').css('display', 'block');  
        $('#monthly-data').css('display', 'none');
        $('#yearly-data').css('display', 'none');
    } else if(type == 3){
        $('#start-daily-data').css('display', 'none');
        $('#end-daily-data').css('display', 'none');
        $('#start-weekly-data').css('display', 'none');
        $('#end-weekly-data').css('display', 'none');  
        $('#monthly-data').css('display', 'block');
        $('#yearly-data').css('display', 'none');
    } else if(type == 4){
        $('#start-daily-data').css('display', 'none');
        $('#end-daily-data').css('display', 'none');
        $('#start-weekly-data').css('display', 'none');
        $('#end-weekly-data').css('display', 'none'); 
        $('#monthly-data').css('display', 'none');
        $('#yearly-data').css('display', 'block');
    } else if(type == 5){
        $('#start-daily-data').css('display', 'none');
        $('#end-daily-data').css('display', 'none');
        $('#start-weekly-data').css('display', 'none');
        $('#end-weekly-data').css('display', 'none'); 
        $('#monthly-data').css('display', 'none');
        $('#yearly-data').css('display', 'none');
    }
});

$('#ReportStartdate').datepicker({
    dateFormat: 'yy-mm-dd',      
    onSelect: function( selectedDate ) {
      $( "#ReportEnddate" ).datepicker( "option", "minDate", selectedDate );
    }
    
});
$('#ReportEnddate').datepicker({
    dateFormat: 'yy-mm-dd',
    onSelect: function( selectedDate ) {
        $( "#ReportStartdate" ).datepicker( "option", "maxDate", selectedDate );
    }
   
});

    $("#ReportDateStartFrom").datepicker({
        dateFormat: 'yy-mm-dd',
        showOtherMonths: true,
        selectOtherMonths: true,
        showWeek:true,
        beforeShowDay: enableSUNDAYS,
        onSelect: function( selectedDate ) {
            var dateText = $.datepicker.formatDate("yy-mm-dd", $(this).datepicker("getDate"));
            $('#startDate').text(dateText);
            $("#ReportDateEndFrom").datepicker( "option", "minDate", selectedDate );
        }
    });
    
    $("#ReportDateEndFrom").datepicker({
        dateFormat: 'yy-mm-dd',
        showOtherMonths: true,
        selectOtherMonths: true,
        showWeek:true,
        beforeShowDay: enableSUNDAYS,
        onSelect: function( selectedDate ) {
            var dateText = $.datepicker.formatDate("yy-mm-dd", $(this).datepicker("getDate"));
            $('#endDate').text(dateText);
            $("#ReportDateStartFrom").datepicker( "option", "maxDate", selectedDate );
        }
    });
    function enableSUNDAYS(date) {
        var day = date.getDay();
        return [(day == 0), ''];
    }

    
$('#DataType').on('change', function () {
    var type = $(this).val();
    if(type == 1){
        $('#start-daily-data').css('display', 'block');
        $('#end-daily-data').css('display', 'block');
        $('#start-weekly-data').css('display', 'none');
        $('#end-weekly-data').css('display', 'none');  
        $('#monthly-data').css('display', 'none');
        $('#yearly-data').css('display', 'none');
    } else if(type == 2){
        $('#start-daily-data').css('display', 'none');
        $('#end-daily-data').css('display', 'none');
        $('#start-weekly-data').css('display', 'block');
        $('#end-weekly-data').css('display', 'block');  
        $('#monthly-data').css('display', 'none');
        $('#yearly-data').css('display', 'none');
    } else if(type == 3){
        $('#start-daily-data').css('display', 'none');
        $('#end-daily-data').css('display', 'none');
        $('#start-weekly-data').css('display', 'none');
        $('#end-weekly-data').css('display', 'none');  
        $('#monthly-data').css('display', 'block');
        $('#yearly-data').css('display', 'none');
    } else if(type == 4){
        $('#start-daily-data').css('display', 'none');
        $('#end-daily-data').css('display', 'none');
        $('#start-weekly-data').css('display', 'none');
        $('#end-weekly-data').css('display', 'none');  
        $('#monthly-data').css('display', 'none');
        $('#yearly-data').css('display', 'block');
    } else if(type == 5){
        $('#start-daily-data').css('display', 'none');
        $('#end-daily-data').css('display', 'none');
        $('#start-weekly-data').css('display', 'none');
        $('#end-weekly-data').css('display', 'none');  
        $('#monthly-data').css('display', 'none');
        $('#yearly-data').css('display', 'none');
    }
});	

jQuery.validator.addMethod("greaterThan", 
function(value, element, params) {

    if (!/Invalid|NaN/.test(new Date(value))) {
        return new Date(value) >= new Date($(params).val());
    }

    return isNaN(value) && isNaN($(params).val()) 
        || (Number(value) >= Number($(params).val())); 
},'Must be greater than {0}.');

    
    $("#ReportMoneyReportForm").validate({
        rules: {
            'data[Report][to_year]': { 
                greaterThan: "#ReportFromYear" 
            }
        },
        messages:{
            'data[Report][to_year]': { 
                greaterThan: "From Year should be less than To Year" 
            }
        }
    });
</script>
<!--<script>
    $(document).ready(function() {	    
	$("#StoreId").change(function(){
	    $("#ReportCustomerReportForm").submit();
	});
	
	    
   });
</script>-->