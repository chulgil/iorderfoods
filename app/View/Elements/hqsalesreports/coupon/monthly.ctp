<?php
$totalItem=0;
$datee= array('1'=>"'Jan'",'2'=>"'Feb'",'3'=>"'Mar'",'4'=>"'Apr'",'5'=>"'May'",'6'=>"'Jun'",'7'=>"'Jul'",'8'=>"'Aug'",'9'=>"'Sep'",'10'=>"'Oct'",'11'=>"'Nov'",'12'=>"'Dec'");
$tcoupon = array('1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0,'7'=>0,'8'=>0,'9'=>0,'10'=>0,'11'=>0,'12'=>0);
foreach($graphData as $key => $data){
   $result1[$key]=$data[0];
   unset($data); 
}
$totalItem=0;
$totalCoupon = array();
if(!empty($result1)){
    foreach($result1 as $coupon){
        $datee[date('n',strtotime($coupon['order_date']))]="'".date('M',strtotime($coupon['order_date']))."'";
        if(array_key_exists($coupon['order_date'], $totalCoupon))
        {
            $totalCoupon[$coupon['order_date']] += 1;
            $tcoupon[date('n',strtotime($coupon['order_date']))] += 1;
        } else {
            $totalCoupon[$coupon['order_date']] = 1;
            $tcoupon[date('n',strtotime($coupon['order_date']))] += 1;
        }
        $totalItem += 1;
    }
}
$subTitle = '<style="font-size:14px;font-weight:bold;">Total '.$totalItem.' Coupon </style>';
$amntdate = implode(',',$datee);
$tcoupon = implode(',',$tcoupon);
$itemcount = implode(',',$totalCoupon);
$text = 'Monthly Report for ' . str_replace('\'', '', $datee[$month]) . ' - ' . $year . ' to ' . str_replace('\'', '', $datee[$toMonth]) . ' - ' . $toYear;
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
            text: '<?php echo $text;?>'
        },
        subtitle: {
            text: '<?php echo $subTitle;?>'
        },
        xAxis: {
            categories: [<?php echo $amntdate;?>],
            title: {
                text: null
            },
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Coupon Count',
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
            name: 'Coupon',
            data: [<?php echo $tcoupon; ?>],
            color: '#f79d54'
        }]
    });
});
</script>
<div id="pagination_data_request">
    <?php echo $this->element('hqsalesreports/coupon/pagination'); ?>
</div>