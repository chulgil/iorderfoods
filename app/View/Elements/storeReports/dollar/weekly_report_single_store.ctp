<?php
$fromWeekNumber     = date('W', strtotime($startFrom)); 
$endFromWeekNumber  = date('W', strtotime($endFrom));
$text="Weekly Report for Week " . $fromWeekNumber . " to Week " . $endFromWeekNumber;
$subTitle = '<style="font-size:14px;font-weight:bold;">Total '.$totalOrders.' Orders And $' . $totalAmount . ' Amount</style>';

?>
<?php echo $this->element('chart/chart_script');?>

<div class="col-lg-12">
    <div id="container"></div>
</div>
<script>  
$(function () {
    var chart;
    $(document).ready(function() {

        var colors      = Highcharts.getOptions().colors,
            categories  = [<?php echo $weeknumbers;?>],
            name        = ['Amount'],    
            name2       = ['Order'],         
            data        = [                
                <?php
                $i=0;
                $amountmax=0;
                $ordermax=0;
                foreach($graphData as $key => $graphdata){
                      if($i==0){
                        $commaseparate="";  
                      }else{
                        $commaseparate=",";
                      }
                      echo $commaseparate;
                      $i++;
                ?>
                {
                    //y: <?php echo (isset($graphdata['totalorders'])) ? $graphdata['totalorders']:0;?>,
                    y: <?php echo (isset($graphdata['totalamount']))?$graphdata['totalamount']:0;?>,
                    
                    color: '#9ac456',
                    drilldown: {                        
                        categories: [<?php echo @$graphdata['datestring'];?>],
                        <?php
                        $coloumstring="";
                        $splinestring="";
                        foreach($graphdata['daywise'] as $key => $innerGraphdata){
                                if($coloumstring==''){
                                    $coloumstring.=(isset($innerGraphdata['total']))?$innerGraphdata['total']:0;
                                }else{
                                    $coloumstring.=",";    
                                    $coloumstring.=(isset($innerGraphdata['total']))?$innerGraphdata['total']:0;
                                }
                                
                                if($splinestring==''){
                                    $splinestring.=(isset($innerGraphdata['totalorders']))?$innerGraphdata['totalorders']:0;    
                                }else{
                                    $splinestring.=","; 
                                    $splinestring.=(isset($innerGraphdata['totalorders']))?$innerGraphdata['totalorders']:0;
                                       
                                }
                                if(isset($innerGraphdata['total'])){
                                        if($amountmax < $innerGraphdata['total']){
                                               $amountmax = $innerGraphdata['total'];
                                        }
                                }
                                
                                if(isset($innerGraphdata['totalorders'])){
                                        if($ordermax < $innerGraphdata['totalorders']){
                                               $ordermax = $innerGraphdata['totalorders'];
                                        }
                                }
                        }

                        ?>

                        series: [{
                            type: 'line',
                            name: 'amount',
                            data: [<?php echo $coloumstring;?>],
                            color: '#9ac456'
                        },{
                            type: 'column',
                            yaxis:1,
                            pointWidth  : 20,
                            name: 'order',
                            data: [<?php echo $splinestring;?>],
                            color: '#f79d54'
                        }]
                    }
                }

                <?php  }?>    

                ],
                
                data2 = [
                
                <?php
                $i=0;
                $ordermax=0;
                foreach($graphData as $key => $graphdata){
                      if($i==0){
                        $commaseparate="";  
                      }else{
                        $commaseparate=",";
                      }
                      echo $commaseparate;
                      $i++;

                        $splinestring="";
                        foreach($graphdata['daywise'] as $key => $innerGraphdata){
                            if($splinestring==''){
                                $splinestring=(isset($innerGraphdata['totalorders']))?$innerGraphdata['totalorders']:0;    
                            }else{
                                $splinestring.=","; 
                                $splinestring.=(isset($innerGraphdata['totalorders']))?$innerGraphdata['totalorders']:0;

                            }

                            if(isset($innerGraphdata['totalorders'])){
                                    if($ordermax < $innerGraphdata['totalorders']){
                                           $ordermax = $innerGraphdata['totalorders'];
                                    }
                            }
                        }
                        
                        echo $splinestring=(isset($graphdata['totalorders']))?$graphdata['totalorders']:0;
                    } ?>    
                
                ];


        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                zoomType: 'xy'
            },
            title: {
                text: '<?php echo $text; ?>'
            },
            subtitle: {
                text: '<?php echo $subTitle;?>'
            },
            xAxis: {
                categories: categories,
                crosshair: true
            },
            yAxis: [
                { // Primary yAxis
                    min: 0,
                    max: <?php echo $amountmax+500; ?>,
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
                    max: <?php echo $ordermax+20; ?>,
                    title: {
                        text: 'Order',
                        align: 'middle'                            
                    },
                    labels: {
                        overflow: 'justify'                            
                    },
                    opposite: true
                }       
            ],
            tooltip: {
                shared: true
            },
            plotOptions: {
                column: {
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function() {
                                var drilldown = this.drilldown;
                                if (drilldown) { // drill down
                                    setChart(null, drilldown.categories, drilldown, drilldown.type);
                                } else { // restore
                                    setChartColumn([name,name2], categories, [data,data2], ['line','column']); 
                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        color: '#000',
                        style: {
                            fontWeight: 'bold'
                        },
                        formatter: function() {
                            return this.y;
                        }
                    }
                },
                spline: {
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function() {
                                setChartColumn([name,name2], categories, [data,data2], ['line','column']); 
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        color: '#000',
                        style: {
                            fontWeight: 'bold'
                        },
                        formatter: function() {
                            return this.y;
                        }
                    }
                },
                line: {
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function() {
                                setChartColumn([name,name2], categories, [data,data2], ['line','column']); 
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        color: '#000',
                        style: {
                            fontWeight: 'bold'
                        },
                        formatter: function() {
                            return this.y;
                        }
                    }
                }
            },
            series: [ {
                name: name,
                type: 'line',
                color: '#9ac456',
                data: data
            },{
                name: name2,
                type: 'column',
                yAxis: 1,
                pointWidth  : 20,
                color: '#f79d54',
                data: data2 
            }],
            exporting: {
                enabled: false
            }
        });
    });

});
        
</script>
<div id="pagination_data_request">
    <?php echo $this->element('storeReports/dollar/pagination'); ?>
</div>


<script>
function setChartColumn(name, categories, data, type) { 
    chart.xAxis[0].setCategories(categories); 
    var dataLen = data.length; 
    while(chart.series.length>0) 
        chart.series[0].remove(); 
    for(var i = 0; i< dataLen; i++){ 
        chart.addSeries({ 
            type: (i == 0 ? 'column' : 'spline'), 
            //type: 'line',
            name: name[i], 
            data: data[i], 
            yAxis: (i == 0 ? 0 : 1),
            color: '#9ac456'
        }); 
    } 
}
    
    
function setChart(name, categories, data, color, type) {
    var len = chart.series.length;
    chart.xAxis[0].setCategories(categories);
    for(var i = 0; i < len; i++){
        console.log(chart.series.length);
        chart.series[0].remove();
    }
    if(data.series){
        for(var i = 0; i < data.series.length; i ++ ){
            if(data.series[i].type )
            chart.addSeries({
                name: data.series[i].name,
                data: data.series[i].data,
                type: 'column',
                pointWidth  : 20,
                //type: 'line',
                color: '#9ac456'
            });
        }
    } else {
            chart.addSeries({
                name: name,
                data: data,
                type: 'column',
                pointWidth  : 20,
                //type: 'line',
                color: '#9ac456'
            });
    }
}
</script>