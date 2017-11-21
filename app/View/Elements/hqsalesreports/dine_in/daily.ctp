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

$itemdate = implode(',',$datee);
$itemcount = implode(',',$totalDineIn);      

$startmonth = date('m', strtotime($startDate));
$endmonth   = date('m', strtotime($endDate));

$startyear  = date('Y', strtotime($startDate));
$endyear    = date('Y', strtotime($endDate));

$titleText = $this->Common->dineInTitleString($startmonth, $startyear, $endmonth, $endyear);
$text = '<style="font-size:12px;"><strong>' . $titleText . '</strong></style>';
$subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalItem . ' Reservations</style>';

/* For Pie Chart*/
$pieData = $this->Common->dineInPieDataArrange($dineInPieData);

$totalPie = (isset($pieData['total']) ? $pieData['total'] : 0);
$finalPie = (isset($pieData['data']) ? $pieData['data'] : array());
/* End For Pie Chart*/
?>
<div class="col-lg-<?php echo (($totalPie == 0) ? '12' : '6');?>">
    <div id="container"></div>
</div>
<div class="col-lg-6 <?php echo (($totalPie == 0) ? 'hidden' : '');?>">
    <div id="reservationPie"></div>
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
                text: '# Of Reservations',
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
            name: 'Reservations',
            data: [<?php echo $itemcount; ?>],
            color: '#f79d54'

        }]
    });
    
    
    /* Pie Chart Start */
    Highcharts.chart('reservationPie', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Reservation Status'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}% ({point.pointcount})</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    distance : -50,
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} % ({point.pointcount})',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            name: 'Reservations',
            colorByPoint: true,
            data: [
            <?php
            foreach ($finalPie as $finalPieV)
            {
                $percentage = ($finalPieV['status_count'] / $totalPie) * 100;
                ?>
                {
                    name        : '<?php echo $finalPieV['status_name']?>',
                    y           : <?php echo $percentage?>,
                    color       : '<?php echo $finalPieV['color']?>',
                    pointcount  : '<?php echo $finalPieV['status_count'];?>'
                },
                <?php
            }
            ?>
             ]
        }],
        exporting: { enabled: false }
    });
    /* Pie Chart End */
});
</script>

<script>
$('.date-select').datepicker({
    dateFormat: 'yy-mm-dd',
});
</script>

<div id="pagination_data_request">
    <?php echo $this->element('hqsalesreports/dine_in/pagination'); ?>
</div>