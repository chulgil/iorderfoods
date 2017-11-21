<?php
$totalDineIn=0;
$difference = $yearTo-$yearFrom;
for($i=$yearFrom;$i<=$yearTo;$i++){
   $list[$i]['Year'] = "'".$i."'";
   $list[$i]['number'] = 0;  
}
foreach($graphData as $key => $data){
    $result1[$key]=$data[0];
    unset($data); 
}
if(!empty($result1)){
    foreach($result1 as $amount){
        $list[date('Y',strtotime($amount['order_date']))]['Year'] = "'".date('Y',strtotime($amount['order_date']))."'";
       if(empty($list[date('Y',strtotime($amount['order_date']))]['number'])){
           $list[date('Y',strtotime($amount['order_date']))]['number'] += 1;
           $totalDineIn = $totalDineIn + 1;
       } else {
           $list[date('Y',strtotime($amount['order_date']))]['number'] += 1;
           $totalDineIn = $totalDineIn + 1;
       }
    }
}
foreach($list as $lst){
    $datee[] = $lst['Year']; 
    $tamntt[] = $lst['number']; 
}
$subTitle = '<style="font-size:14px;font-weight:bold;">Total '.$totalDineIn.' Dine In </style>';
$amntdate = implode(',',$datee);
$tamnt = implode(',',$tamntt);
$text = 'Yearly Report for '.$yearFrom.' - '. $yearTo;

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
                data: [<?php echo $tamnt; ?>],
                color: '#f79d54'
    
            }]
        });
    });
        
</script>
<div id="pagination_data_request">
    <?php echo $this->element('superNewReports/dine_in/pagination'); ?>
</div>