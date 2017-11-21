<?php
$totalItem=0;
$datee= array('1'=>"'Jan'",'2'=>"'Feb'",'3'=>"'Mar'",'4'=>"'Apr'",'5'=>"'May'",'6'=>"'Jun'",'7'=>"'Jul'",'8'=>"'Aug'",'9'=>"'Sep'",'10'=>"'Oct'",'11'=>"'Nov'",'12'=>"'Dec'");
$toffer = array('1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0,'7'=>0,'8'=>0,'9'=>0,'10'=>0,'11'=>0,'12'=>0);
foreach($graphData as $key => $data){
    $result1[$key]              = $data[0];
    $result1[$key]['quantity']  = $data['OrderItemFree']['free_quantity'];
   unset($data); 
}
$totalItem=0;
$totalOffer = array();
if(!empty($result1)){
    foreach($result1 as $offer){
        //pr($offer);
        $datee[date('n',strtotime($offer['order_date']))]="'".date('M',strtotime($offer['order_date']))."'";
        if(array_key_exists($offer['order_date'], $totalOffer))
        {
            $totalOffer[$offer['order_date']]                   += $offer['quantity'];
            $toffer[date('n',strtotime($offer['order_date']))]  += $offer['quantity'];
        } else {
            $totalOffer[$offer['order_date']] = 1;
            $toffer[date('n',strtotime($offer['order_date']))]  += $offer['quantity'];
        }
        $totalItem += $offer['quantity'];
    }
}
$subTitle = '<style="font-size:14px;font-weight:bold;">Total '.$totalItem.' Extended Offer</style>';
$amntdate = implode(',',$datee);
$toffer = implode(',',$toffer);
$itemcount = implode(',',$totalOffer);
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
            data: [<?php echo $toffer; ?>],
            color: '#f79d54'
        }]
    });
});
</script>
<div id="pagination_data_request">
    <?php echo $this->element('storeReports/extended_promo/pagination'); ?>
</div>