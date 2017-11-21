<?php
$step = '+1 day';
$output_format = 'Y-m-d';
$datee = $totalDineIn = array();
$text = '';
$current = strtotime($startDate);
$last = strtotime($endDate);    
while( $current <= $last ) {    
    $datee[]                                        = "'".date($output_format, $current)."'";
    $totalDineIn[date($output_format, $current)]    =   0;    
    $current                                        = strtotime($step, $current);
}
$amnt=0;
$order=0;
$totalItem=0;
if(!empty($graphData)){
    foreach($graphData as $key => $data){
        $result1[$key]=$data[0];
        unset($data); 
    }
    if(!empty($result1)){
        foreach($result1 as $coupon){
            if(array_key_exists($coupon['order_date'], $totalDineIn))
            {
                $totalDineIn[$coupon['order_date']] += 1;
            } else {
                $totalDineIn[$coupon['order_date']] = 1;
            }
            $totalItem += 1;
        }
    }
}
$subTitle = '<style="font-size:14px;font-weight:bold;">Total '.$totalItem.' Dine In </style>';
$itemdate = implode(',',$datee);
$itemcount = implode(',',$totalDineIn);      
$text = 'Daily Report for '.$startDate.' to '. $endDate;    
?>
<div class="col-lg-12">
    <div id="container"></div>
</div>

<script>
$(function () {
    $('#container').highcharts({
        chart: {
            type: 'line'
        },
        title: {
            text: '<?php echo  $text;?>'
        },
        subtitle: {
            text: '<?php echo $subTitle;?>'
        },
        xAxis: {
            categories: [<?php echo $itemdate;?>],
            title: {
                text: null
            },
            crosshair: true
        },
       yAxis: {
            min: 0,
            title: {
                text: 'Dine In Count',
                align: 'middle'
            },
            labels: {
                overflow: 'justify'
            }
        },
        tooltip: {
            valueSuffix: ''
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: true
            },
            series: {
                pointWidth: 50
            }
        },
        exporting: { enabled: false },
        series: [{
            name: 'Dine In',
            data: [<?php echo $itemcount; ?>],
            color: '#f79d54'

        }]
    });
});
</script>

<script>
$('.date-select').datepicker({
    dateFormat: 'yy-mm-dd',
});
</script>

<div id="pagination_data_request">
    <?php echo $this->element('superNewReports/dine_in/pagination'); ?>
</div>