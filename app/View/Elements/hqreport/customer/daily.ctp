<?php
        $summarytotalUsers=0;
        $totalusers=array();
        $step = '+1 day';
        $output_format = 'Y-m-d';
        $datee = array();
        $current = strtotime($startdate);
        $last = strtotime($enddate);    
        while( $current <= $last ) {    
            $datee[] = "'".date($output_format, $current)."'";
            $current = strtotime($step, $current);
            $totalusers[]=0;
        }        
        $user=0;
        $tamntt=0;
        $totalCustomer=0;
        if(!empty($result)){
        foreach($result as $user){            
            $datearray = explode(" ",$user['User']['created']);
           // echo '<pre>';print_r($datearray);die;
            foreach($datee as $key => $date){                
                $date=str_replace("'",'',$date);
                if($date==$datearray[0]){
                    if($tamntt[$key]){
                        $user +=$user['User']['per_day'];
                         $totalCustomer = $totalCustomer + $user['User']['per_day'];
                       // $user +=1;
                    }else{
                        $user =$user['User']['per_day'];
                        $totalCustomer = $totalCustomer + $user;
                       // $user=1;
                    }
                    $totalusers[$key]=$user;                    
                }
            }             
        }
        }
        //$tamntt[] = $amnt;
        $subTitle = '<style="font-size:14px;font-weight:bold;">Total '.$totalCustomer.' Customer </style>';
        $userdate = implode(',',$datee);
        $user = implode(',',$totalusers);
      // echo $user;die;
        $text = 'Daily Report for '.$startdate.' to '. $enddate;
        $summarytotalUsers=array_sum($totalusers);
                //echo $userdate;echo '<br>';echo $user;echo '<br>';echo $summarytotalUsers;die;



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
                categories: [<?php echo $userdate;?>],
                title: {
                    text: null
                },
                crosshair: true
            },
           yAxis: {
                min: 0,
                title: {
                    text: 'Customer',
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
                name: 'Customer',
                data: [<?php echo $user; ?>]
    
            }]
        });
});
    
    
    
    
    
        
</script>


<script>
$('.date-select').datepicker({
    dateFormat: 'yy-mm-dd',
});
</script>