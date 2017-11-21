<?php    
      $text="Weekly Report for ".$startFrom." to ". $endFrom;
     
?>
<?php echo $this->element('chart/chart_script');?>


<script>
$(function () {
    var chart;
    $(document).ready(function() {
    
        var colors = Highcharts.getOptions().colors,
            categories = [<?php echo $weeknumbers;?>],
            name = ['Order'],      
            data = [                
                <?php
                $i=0;
                $ordermax=0;
                foreach($data as $key => $graphdata){
                      if($i==0){
                        $commaseparate="";  
                      }else{
                        $commaseparate=",";
                      }
                      echo $commaseparate;
                      $i++;
                ?>
                {
                    y: <?php echo (isset($graphdata['totalorders']))?$graphdata['totalorders']:0;?>,
                    color: colors[0],
                    drilldown: {                        
                        categories: [<?php echo @$graphdata['datestring'];?>],
                        <?php
                        $splinestring="";
                        
                        foreach($graphdata['daywise'] as $key => $innerGraphdata){
                                
                                if($splinestring==''){
                                    $splinestring.=(isset($innerGraphdata['totalorders']))?$innerGraphdata['totalorders']:0;    
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
                        
                        ?>
                        
                        series: [{
                            type: 'column',
                            name: 'order',
                            data: [<?php echo $splinestring;?>],
                            color: colors[0]
                        }]
                    }
                }
                
                <?php  }?>    
                
                ];
      
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
                        color: colors[0]
                    });
                }
            } else {
                    chart.addSeries({
                        name: name,
                        data: data,
                        type: 'column',
                        color: colors[0]
                    });
            }
        }
    
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                zoomType: 'xy'
            },
            title: {
                text: '<?php echo $text; ?>'
            },         
            xAxis: {
                categories: categories,
                crosshair: true
            },
            yAxis: [
                { // Primary yAxis
                min: 0,
                max: <?php echo $ordermax+10; ?>,
                        title: {
                            text: 'Order',
                            align: 'middle'                            
                        },
                        labels: {
                            overflow: 'justify'                            
                        }
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
                                    setChart(name, categories,data, 'column'); 
                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        color: colors[0],
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
                type: 'column',
                color:colors[0],
                data: data
            }],
            exporting: {
                enabled: false
            }
        });
    });
    
});
    
    
    
        
</script>


