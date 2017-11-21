<?php
 $totalItem=0;
$difference = $yearTo-$yearFrom;
        for($i=$yearFrom;$i<=$yearTo;$i++){
           $list[$i]['Year'] = "'".$i."'";
           $list[$i]['number'] = 0;  
        }
        foreach($result as $key => $data){
            $result1[$key]=$data[0];
            unset($data); 
        }
        if(!empty($result1)){
         foreach($result1 as $amount){
            $list[date('Y',strtotime($amount['order_date']))]['Year'] = "'".date('Y',strtotime($amount['order_date']))."'";
            if(empty($list[date('Y',strtotime($amount['order_date']))]['number'])){
                $list[date('Y',strtotime($amount['order_date']))]['number'] = $amount['number'];
                $totalItem = $totalItem + $amount['number'];

            } else {
                $list[date('Y',strtotime($amount['order_date']))]['number'] += $amount['number'];
                $totalItem = $totalItem + $amount['number'];

            }
        }
        }
        foreach($list as $lst){
            $datee[] = $lst['Year']; 
            $tamntt[] = $lst['number']; 
        }
        $subTitle = '<style="font-size:14px;font-weight:bold;">Total '.$totalItem.' Item </style>';
        $amntdate = implode(',',$datee);
        $tamnt = implode(',',$tamntt);
        $text = 'Yearly Report for '.$yearFrom.'-'. $yearTo;

?>

<?php echo $this->element('chart/chart_script');?>


<script>
     
   $(function () {
        $('#container').highcharts({
            chart: {
                type: 'column'
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
                    text: 'Item count',
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                }
            },
            tooltip: {
                valueSuffix: ''
            },
            plotOptions: {
                series: {
                    pointWidth: 50
                }
            },
            exporting: { enabled: false },
            series: [{
                name: 'Item',
                data: [<?php echo $tamnt; ?>]
    
            }]
        });
    });
        
</script>
    