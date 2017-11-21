<?php
        $summarytotalAmount=0;
        $summarytotalOrders=0;
        $totalorders=array();
        $step = '+1 day';
        $output_format = 'Y-m-d';
        $datee = array();
        $current = strtotime($startdate);
        $last = strtotime($enddate);    
        while( $current <= $last ) {    
            $datee[] = "'".date($output_format, $current)."'";
            $current = strtotime($step, $current);
            $tamntt[]=0;
            $totalorders[]=0;
        }        
        $amnt=0;
        $order=0;
        $totalOrder=0;
        $totalAmt=0;
        if(!empty($result)){
        foreach($result as $amount){            
            $datearray = explode(" ",$amount['Order']['created']);            
            foreach($datee as $key => $date){                
                $date=str_replace("'",'',$date);
                if($date==$datearray[0]){
                    if($tamntt[$key]){
                        $amnt +=$amount['Order']['amount']-$amount['Order']['coupon_discount'];
                        $order +=1;
                        $totalOrder = $totalOrder + 1;
                        $totalAmt = $totalAmt + $amount['Order']['amount']-$amount['Order']['coupon_discount'];


                    }else{
                        $amnt =$amount['Order']['amount']-$amount['Order']['coupon_discount'];
                        $order=1;
                        $totalOrder = $totalOrder + 1;
                        $totalAmt = $totalAmt + $amount['Order']['amount']-$amount['Order']['coupon_discount'];

                    }
                    $tamntt[$key]=$amnt;
                    $totalorders[$key]=$order;                    
                }
            }             
        }
        }
        //$tamntt[] = $amnt;
        $subTitle = '<style="font-size:14px;font-weight:bold;">Total '.$totalOrder.' Order And  $ '.$totalAmt.' Amount</style>';
        $amntdate = implode(',',$datee);
        $tamnt = implode(',',$tamntt);
        $torders = implode(',',$totalorders);  
        $text = 'Daily Report for '.$startdate.' to '. $enddate;
        $summarytotalAmount=array_sum($tamntt);
        $summarytotalOrders=array_sum($totalorders);


?>
 <!--<div id="summary" style="min-width: 310px; height: 80px; margin: 0 auto"><?php echo "Total Amount: ".$summarytotalAmount." Total Orders:".$summarytotalOrders;?></div>-->
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


<script>
$('.date-select').datepicker({
    dateFormat: 'yy-mm-dd',
});
</script>