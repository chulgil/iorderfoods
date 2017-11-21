<?php
$totalDineIn=0;
$datee= array('1'=>"'Jan'",'2'=>"'Feb'",'3'=>"'Mar'",'4'=>"'Apr'",'5'=>"'May'",'6'=>"'Jun'",'7'=>"'Jul'",'8'=>"'Aug'",'9'=>"'Sep'",'10'=>"'Oct'",'11'=>"'Nov'",'12'=>"'Dec'");
$tdinein = array('1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0,'7'=>0,'8'=>0,'9'=>0,'10'=>0,'11'=>0,'12'=>0);
foreach($graphData as $key => $data){
   $result1[$key]=$data[0];
   unset($data); 
}
$totalCoupon = array();
if(!empty($result1)){
    foreach($result1 as $coupon){
        $datee[date('n',strtotime($coupon['order_date']))]="'".date('M',strtotime($coupon['order_date']))."'";
        if(array_key_exists($coupon['order_date'], $totalCoupon))
        {
            $totalCoupon[$coupon['order_date']] += 1;
            $tdinein[date('n',strtotime($coupon['order_date']))] += 1;
        } else {
            $totalCoupon[$coupon['order_date']] = 1;
            $tdinein[date('n',strtotime($coupon['order_date']))] += 1;
        }
        $totalDineIn += 1;
    }
}
$subTitle = '<style="font-size:14px;font-weight:bold;">Total '.$totalDineIn.' Dine In </style>';
$amntdate = implode(',',$datee);
$tdinein = implode(',',$tdinein);
$text = 'Monthly Report for ' . str_replace('\'', '', $datee[$month]) . ' - ' . $year;
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
            data: [<?php echo $tdinein; ?>],
            color: '#f79d54'
        }]
    });
});
</script>
<div id="pagination_data_request">
    <?php echo $this->element('superNewReports/dine_in/pagination'); ?>
</div>