<?php       
        $summarytotalAmount=0;
        $summarytotalOrders=0;
        $totalorders=array();
        $step = '+1 day';
        $output_format = 'Y-m-d';
        $datee = array();
        $text = '';
        $current = strtotime($startdate);
        $last = strtotime($enddate);    
        while( $current <= $last ) {    
            $datee[] = "'".date($output_format, $current)."'";
            $totalProducts[date($output_format, $current)]=0;  
            $current = strtotime($step, $current);
                      
        }
        $amnt=0;
        $order=0;
        $totalItem=0;
         if(!empty($result)){
        foreach($result as $key => $data){
            $result1[$key]=$data[0];
            unset($data); 
        }        
        
          if(!empty($result1)){
                foreach($result1 as $product){             
                        $totalProducts[$product['order_date']]=$product['number'];
                        $totalItem = $totalItem + $product['number'];
        
                }
          }
          }
        $subTitle = '<style="font-size:14px;font-weight:bold;">Total '.$totalItem.' Item </style>';
        $itemdate = implode(',',$datee);
        $itemcount = implode(',',$totalProducts);      
        $text = 'Daily Report for '.$startdate.' to '. $enddate;    


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
                text: '<?php echo  $text;?>'
            },
            subtitle: {
                text: '<?php echo $subTitle;?>'
            },
            xAxis: {
                categories: [<?php echo $itemdate;?>],
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
                data: [<?php echo $itemcount; ?>]
    
            }]
        });
});
    
    
    
    
    
        
</script>

<script>
$('.date-select').datepicker({
    dateFormat: 'yy-mm-dd',
});
</script>