<?php       
$summarytotalAmount=0;
$summarytotalOrders=0;
$totalorders=array();
$step = '+1 day';
$output_format = 'Y-m-d';
$datee = $totalCoupon = array();
$text = '';
$current = strtotime($startDate);
$last = strtotime($endDate);    
while( $current <= $last ) {    
    $datee[]                                        = "'".date($output_format, $current)."'";
    $totalCoupon[date($output_format, $current)]    =   0;    
    $current                                        = strtotime($step, $current);
}
$amnt=0;
$order=0;
$totalItem=0;
if(!empty($graphData)){
    foreach($graphData as $key => $data){
        $result1[$key]              = $data[0];
        $result1[$key]['quantity']  = $data['OrderItemFree']['free_quantity'];
        unset($data); 
    }
    if(!empty($result1)){
        foreach($result1 as $coupon){
            if(array_key_exists($coupon['order_date'], $totalCoupon))
            {
                $totalCoupon[$coupon['order_date']] += $coupon['quantity'];
            } else {
                $totalCoupon[$coupon['order_date']] = $coupon['quantity'];
            }
            $totalItem += $coupon['quantity'];
        }
    }
}
$subTitle = '<style="font-size:14px;font-weight:bold;">Total '.$totalItem.' Extended Offer </style>';
$itemdate = implode(',',$datee);
$itemcount = implode(',',$totalCoupon);      
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
                text: 'Extended Offer Count',
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
            name: 'Extended Offer',
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
    <?php echo $this->element('superNewReports/extended_promo/pagination'); ?>
</div>