<?php
$order=0;
$totalOrder=0;
$totalAmt=0;
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
                $totalOrder = $totalOrder + 1;
                $totalAmt = $totalAmt + $amount['Order']['amount']-$amount['Order']['coupon_discount'];
            } else {
                $list[date('Y',strtotime($amount['Order']['created']))]['Amount'] += $amount['Order']['amount']-$amount['Order']['coupon_discount']; 
                $totalOrder = $totalOrder + 1;
                $totalAmt = $totalAmt + $amount['Order']['amount']-$amount['Order']['coupon_discount'];
          
            }
            $torders[date('Y',strtotime($amount['Order']['created']))]=$torders[date('Y',strtotime($amount['Order']['created']))]+1;
        } 
        foreach($list as $lst){
            $datee[] = $lst['Year']; 
            $tamntt[] = $lst['Amount']; 
        }
        $subTitle = '<style="font-size:14px;font-weight:bold;">Total '.$totalOrder.' Order And  $ '.$totalAmt.' Amount</style>';
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
            zoomType: 'xy'
        },
        title: {
            text: '<?php echo $text;?>'
        },
        subtitle: {
                text: '<?php echo $subTitle;?>'
            },
        xAxis: [{
            categories: [<?php echo $amntdate;?>],
            crosshair: true
        }],
        yAxis: [
                { // Primary yAxis
                min: 0,
                        title: {
                            text: 'Amount ($)',
                            align: 'middle'                            
                        },
                        labels: {
                            overflow: 'justify'                            
                        }
                },
                { // Secondary yAxis
                min: 0,
                        title: {
                            text: 'Orders',
                            align: 'middle'
                        },
                        labels: {
                            overflow: 'justify',
                            
                        },
                opposite: true
                }        
        ],
        tooltip: {
            shared: true
        },
        legend: {
            layout: 'vertical',
            align: 'left',
            x: 120,
            verticalAlign: 'top',
            y: 100,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },
        series: [
                
                 {
            name: 'Order',
            type: 'spline',
            yAxis: 1,
            color:'#434348',
            data: [<?php echo $torders; ?>]
            
        },               
        {
            name: 'Amount',
            type: 'column',
            maxPointWidth:50,
            color:'#7CB5EC',
            data: [<?php echo $tamnt; ?>]
            

        } ],
        exporting: { enabled: false }
    });
});
    
    
    
    
    
        
</script>
    