<?php
$order=0;
$totalOrder=0;
 $difference = $yearTo-$yearFrom;
        for($i=$yearFrom;$i<=$yearTo;$i++){
           $list[$i]['Year'] = "'".$i."'";
           $list[$i]['Amount'] = 0;
           $torders[$i]=0;
        }
        //pr($torders);die;
        foreach($result as $amount){
            $list[date('Y',strtotime($amount['Order']['created']))]['Year'] = "'".date('Y',strtotime($amount['Order']['created']))."'";
            if(empty($list[date('Y',strtotime($amount['Order']['created']))]['Amount'])){
                $list[date('Y',strtotime($amount['Order']['created']))]['Amount'] = $amount['Order']['amount']-$amount['Order']['coupon_discount']; 
                $totalOrder=$totalOrder+1;

            } else {
                $list[date('Y',strtotime($amount['Order']['created']))]['Amount'] += $amount['Order']['amount']-$amount['Order']['coupon_discount']; 
                $totalOrder=$totalOrder+1;

            }
            $torders[date('Y',strtotime($amount['Order']['created']))]=$torders[date('Y',strtotime($amount['Order']['created']))]+1;
        } 
        foreach($list as $lst){
            $datee[] = $lst['Year']; 
            $tamntt[] = $lst['Amount']; 
        }
        $subTitle = '<style="font-size:14px;font-weight:bold;">Total '.$totalOrder.' Order </style>';
        $amntdate = implode(',',$datee);
        $tamnt = implode(',',$tamntt);
        $torders = implode(',',$torders);        
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
                    text: 'Order',
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
                name: 'Order',
                data: [<?php echo $torders; ?>]
    
            }]
        });
    });
        
</script>
    