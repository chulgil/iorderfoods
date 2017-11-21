<?php
$yearFrom = 2012;
$totalCustomer=0;
$yearTo = date('Y');
$difference =$yearTo-$yearFrom;
for($i=$yearFrom;$i<=$yearTo;$i++){
   $list[$i]['Year'] = "'".$i."'";
   $list[$i]['per_day'] = 0;  
}
foreach($result as $amount){
    $list[date('Y',strtotime($amount['User']['created']))]['Year'] = "'".date('Y',strtotime($amount['User']['created']))."'";
    if(empty($list[date('Y',strtotime($amount['User']['created']))]['per_day'])){
        $list[date('Y',strtotime($amount['User']['created']))]['per_day'] = $amount['User']['per_day'];
        $totalCustomer = $totalCustomer + $amount['User']['per_day'];

    } else {
        $list[date('Y',strtotime($amount['User']['created']))]['per_day'] += $amount['User']['per_day'];
        $totalCustomer = $totalCustomer + $amount['User']['per_day'];

    }
} 
foreach($list as $lst){
    $datee[] = $lst['Year']; 
    $tamntt[] = $lst['per_day']; 
}
//sort($datee);
$subTitle = '<style="font-size:14px;font-weight:bold;">Total '.$totalCustomer.' Customer </style>';
$amntdate = implode(',',$datee);
$tamnt = implode(',',$tamntt);
$text = 'Life Time Report';
?>


<?php echo $this->element('chart/chart_script');?>

<div id="container"></div>
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
                    text: 'Customer Count',
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
                name: 'Customer',
                data: [<?php echo $tamnt; ?>],
                color: '#f79d54'
    
            }]
        });
});  
</script>
<div id="pagination_data_request">
    <?php echo $this->element('superNewReports/customer/pagination');?>
</div>