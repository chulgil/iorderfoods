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
            name = ['Customer Count'],    
            data = [                
                <?php
                $i=0;
                $amountmax=0;
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
                    y: <?php echo (isset($graphdata['totaluser']))?$graphdata['totaluser']:0;?>,
                    color: colors[0],
                    drilldown: {                        
                        categories: [<?php echo @$graphdata['datestring'];?>],
                        <?php
                        $coloumstring="";
                        
                       
                        foreach($graphdata['daywise'] as $key => $innerGraphdata){
                                if($coloumstring==''){
                                    $coloumstring.=(isset($innerGraphdata['total']))?$innerGraphdata['total']:0;
                                }else{
                                    $coloumstring.=",";    
                                    $coloumstring.=(isset($innerGraphdata['total']))?$innerGraphdata['total']:0;
                                }
                                
                                
                                if(isset($innerGraphdata['total'])){
                                        if($amountmax < $innerGraphdata['total']){
                                               $amountmax = $innerGraphdata['total'];
                                        }
                                }
                                
                               
                        }                        
                        ?>
                        
                        series: [{
                            type: 'column',
                            name: 'Customer',
                            data: [<?php echo $coloumstring;?>],
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
                max: <?php echo $amountmax+5;?>,
                        title: {
                            text: 'Customer Count',
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
                                    setChart(name, categories,data,'column'); 
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
                color: colors[0],
                data: data
            }],
            exporting: {
                enabled: false
            }
        });
    });
    
});
    
    
    
        
</script>
