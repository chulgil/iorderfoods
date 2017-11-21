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
        if(!empty($result)){
        foreach($result as $amount){            
            $datearray = explode(" ",$amount['Order']['created']);            
            foreach($datee as $key => $date){                
                $date=str_replace("'",'',$date);
                if($date==$datearray[0]){
                    if($tamntt[$key]){
                        $amnt +=$amount['Order']['amount']-$amount['Order']['coupon_discount'];

                        $order +=1;
                         $totalOrder=$totalOrder+1;
                    }else{
                        $amnt =$amount['Order']['amount']-$amount['Order']['coupon_discount'];

                        $order=1;
                        $totalOrder=$totalOrder+1;

                    }
                    $tamntt[$key]=$amnt;
                    $totalorders[$key]=$order;                    
                }
            }             
        }
        }
        $subTitle = '<style="font-size:14px;font-weight:bold;">Total '.$totalOrder.' Order </style>';
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


<script>
$('.date-select').datepicker({
    dateFormat: 'yy-mm-dd',
});
</script>