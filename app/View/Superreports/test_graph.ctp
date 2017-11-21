<?php echo $this->element('chart/chart_script');?>



<div class="row">
    <div class="col-lg-12">
        <h3>Test Report</h3>
        <?php echo $this->Session->flash(); ?> 
        <?php echo $this->Form->create('Report', array('url' => array('controller' => 'reports', 'action' => 'testGraph')));  ?>
	    <div class="row padding_btm_20">
                <div class="col-lg-2">	
                    <label>Type of Report</label>
		    <?php		    
		    $options=array('1'=>'Daily','2'=>'Weekly','3'=>'Monthly','4'=>'Yearly','5'=>'Life Time');
		    echo $this->Form->input('type',array('id'=>'DataType','type'=>'select','class'=>'form-control valid','label'=>false,'div'=>false,'options'=>$options)); ?>		
                </div>
		
	
                <div class="col-lg-2" id="start_daily-data">
                    <label>Start Date</label>
                    <?php echo $this->Form->input('startdate',array('label' => false,'div' => false,'class' => 'form-control date-select','value'=>$startdate));?>
                </div>
                 <div class="col-lg-2" id="end_daily-data">
                <label>End Date</label>
                    <?php echo $this->Form->input('enddate',array('label' => false,'div' => false,'class' => 'form-control date-select','value'=>$enddate));?>
                </div>
                  <div id="start-weekly-data">
                    <div class="col-lg-3">
                        <label>Select Start Week</label>
                        <?php echo $this->Form->input('date_start_from',array('label' => false,'div' => false,'class' => 'form-control week-picker','value'=>$dateFrom));?>
                      <span class="blue">  <label>Week Range:</label> (<span id="startDate"><?php echo $dateFrom;?></span>  -  <span id="endDate"><?php echo $dateTo;?></span>)</span>
                    </div>
                </div>
                <div id="end-weekly-data">
                    <div class="col-lg-3">
                        <label>Select End Week</label>
                        <?php echo $this->Form->input('date_end_from',array('label' => false,'div' => false,'class' => 'form-control week-picker','value'=>$dateFrom));?>
                      <span class="blue">  <label>Week Range:</label> (<span id="startDate"><?php echo $dateFrom;?></span>  -  <span id="endDate"><?php echo $dateTo;?></span>)</span>
                    </div>
                </div>
                <div id="monthly-data">
                    <?php	
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
                    <?php echo $this->Form->button('Generate Report', array('type' => 'submit','class' => 'btn btn-default'));?>
                </div>
                <div style="float:right;">
                <div class="col-lg-1">	
                    <label>&nbsp;&nbsp;&nbsp;&nbsp;</label>
                    <?php echo $this->Html->link('Download Excel', array('controller'=>'reports','action'=>'moneyReportDownload',$type,$startdate,$enddate,$dateFrom,$Month,$Year,$yearFrom,$yearTo),array('class' => 'btn btn-default')); ?>
                </div>
           </div></div>
        <?php echo $this->Form->end();?>
        
        
          <div> 
            <!--Content--> 

            <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                
          </div>
    </div>
</div>

      


<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

<script>
    
    $(document).ready(function(){
    $('.highcharts-container').find("text[text-anchor='end']:last").hide();
    var type = '<?php echo $type;?>';
    if(type == 1){
        $('#start_daily-data').css('display', 'block');
        $('#end_daily-data').css('display', 'block');
        $('#start-weekly-data').css('display', 'none');
        $('#end-weekly-data').css('display', 'none');
        $('#monthly-data').css('display', 'none');
        $('#yearly-data').css('display', 'none');
    } else if(type == 2){
        $('#start_daily-data').css('display', 'none');
        $('#end_daily-data').css('display', 'none');
       $('#start-weekly-data').css('display', 'block');
       $('#end-weekly-data').css('display', 'block');
        $('#monthly-data').css('display', 'none');
        $('#yearly-data').css('display', 'none');
    } else if(type == 3){
         $('#start_daily-data').css('display', 'none');
        $('#end_daily-data').css('display', 'none');
        $('#start-weekly-data').css('display', 'none');
        $('#end-weekly-data').css('display', 'none');
        $('#monthly-data').css('display', 'block');
        $('#yearly-data').css('display', 'none');
    } else if(type == 4){
         $('#start_daily-data').css('display', 'none');
        $('#end_daily-data').css('display', 'none');
        $('#start-weekly-data').css('display', 'none');
        $('#end-weekly-data').css('display', 'none');
        $('#monthly-data').css('display', 'none');
        $('#yearly-data').css('display', 'block');
    } else if(type == 5){
          $('#start_daily-data').css('display', 'none');
        $('#end_daily-data').css('display', 'none');
       $('#start-weekly-data').css('display', 'none');
       $('#end-weekly-data').css('display', 'none');
        $('#monthly-data').css('display', 'none');
        $('#yearly-data').css('display', 'none');
    }
});

$('.date-select').datepicker({
    dateFormat: 'yy-mm-dd',
});
            
$(function() {
    var startDate;
    var endDate;
    
    var selectCurrentWeek = function() {
        window.setTimeout(function () {
            $('.week-picker').find('.ui-datepicker-current-day a').addClass('ui-state-active')
        }, 1);
    }
    
    $('.week-picker').datepicker( {
        dateFormat: 'yy-mm-dd',
        showOtherMonths: true,
        selectOtherMonths: true,
        showWeek:true,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay());
            endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 6);
            var dateFormat = inst.settings.dateFormat || $.datepicker._defaults.dateFormat;
            $('#startDate').text($.datepicker.formatDate( dateFormat, startDate, inst.settings ));
            $('#endDate').text($.datepicker.formatDate( dateFormat, endDate, inst.settings ));
            
            selectCurrentWeek();
        },
        beforeShowDay: function(date) {
            var day = date.getDay();
            return [(day == 0), ''];
        },
        onChangeMonthYear: function(year, month, inst) {
            selectCurrentWeek();
        }
    });
    
    $('.week-picker .ui-datepicker-calendar tr').bind('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
    $('.week-picker .ui-datepicker-calendar tr').bind('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });
});
    
$('#DataType').on('change', function () {
    var type = $(this).val();
    if(type == 1){
          $('#start_daily-data').css('display', 'block');
        $('#end_daily-data').css('display', 'block');
      $('#end-weekly-data').css('display', 'none');
      $('#start-weekly-data').css('display', 'none');
        $('#monthly-data').css('display', 'none');
        $('#yearly-data').css('display', 'none');
    } else if(type == 2){
         $('#start_daily-data').css('display', 'none');
        $('#end_daily-data').css('display', 'none');
       $('#start-weekly-data').css('display', 'block');
       $('#end-weekly-data').css('display', 'block');
        $('#monthly-data').css('display', 'none');
        $('#yearly-data').css('display', 'none');
    } else if(type == 3){
         $('#start_daily-data').css('display', 'none');
        $('#end_daily-data').css('display', 'none');
       $('#start-weekly-data').css('display', 'none');
       $('#end-weekly-data').css('display', 'none');
        $('#monthly-data').css('display', 'block');
        $('#yearly-data').css('display', 'none');
    } else if(type == 4){
         $('#start_daily-data').css('display', 'none');
        $('#end_daily-data').css('display', 'none');
       $('#start-weekly-data').css('display', 'none');
       $('#end-weekly-data').css('display', 'none');
        $('#monthly-data').css('display', 'none');
        $('#yearly-data').css('display', 'block');
    } else if(type == 5){
         $('#start_daily-data').css('display', 'none');
        $('#end_daily-data').css('display', 'none');
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
<?php

    if($type == 1){  
echo $this->element('report/dollar/daily');


    } else if($type == 2){
        
echo $this->element('report/dollar/weekly');
     
    } else if($type == 3){

 echo $this->element('report/dollar/monthly');
   
    } else if($type == 4){
 echo $this->element('report/dollar/yearly');

       
    } else if($type == 5){
       
  echo $this->element('report/dollar/life_time');

    }
?>