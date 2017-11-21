<?php
$fromWeekNumber     = date('W', strtotime($startFrom)); 
$endFromWeekNumber  = date('W', strtotime($endFrom));
$subTitle = '<style="font-size:14px;font-weight:bold;">Total '.$totalOffer.' Extended Offer </style>';
$text="Weekly Report for Week ".$fromWeekNumber." to Week ". $endFromWeekNumber;
?>
<?php echo $this->element('chart/chart_script');?>

<div class="col-lg-12">
    <div id="container"></div>
</div>
<script>  
$(function () {
    var chart;
    $(document).ready(function() {

        var colors = Highcharts.getOptions().colors,
            categories = [<?php echo $weeknumbers;?>],
            name = ['Extended Offer'],      
            data = [                
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
                ?>
                {
                    y: <?php echo (isset($graphdata['totaloffer'])) ? $graphdata['totaloffer']:0;?>,
                    color: '#f79d54',
                    drilldown: {                        
                        categories: [<?php echo @$graphdata['datestring'];?>],
                        <?php
                        $splinestring = "";
                        foreach($graphdata['daywise'] as $key => $innerGraphdata){
                            if($splinestring==''){
                                $splinestring.=(isset($innerGraphdata['totaloffer'])) ? $innerGraphdata['totaloffer'] : 0;    
                            }else{
                                $splinestring.=","; 
                                $splinestring.=(isset($innerGraphdata['totaloffer'])) ? $innerGraphdata['totaloffer'] : 0;

                            }
                            if(isset($innerGraphdata['totaloffer'])){
                                if($ordermax < $innerGraphdata['totaloffer']){
                                       $ordermax = $innerGraphdata['totaloffer'];
                                }
                            }
                        }

                        ?>

                        series: [{
                            type: 'line',
                            name: 'Extended Offer',
                            data: [<?php echo $splinestring;?>],
                            color: '#f79d54'
                        }]
                    }
                }

                <?php  }?>    

                ];



        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                zoomType: 'xy',
                type: 'line'
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
                max: <?php echo $ordermax+10; ?>,
                title: {
                    text: 'Extended Offer Count',
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
                line: {
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function() {
                                var drilldown = this.drilldown;
                                if (drilldown) { // drill down
                                    setChart(null, drilldown.categories, drilldown, drilldown.type);
                                } else { // restore
                                    setChart(name, categories,data, 'line'); 
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
                    },
                    enableMouseTracking: true
                }
            },
            series: [ {
                name: name,
                type: 'line',
                color:'#f79d54',
                data: data
            }],
            exporting: {
                enabled: false
            }
        });
    });

});
        
</script>
<div id="pagination_data_request">
    <?php echo $this->element('hqsalesreports/extended_promo/pagination'); ?>
</div>


<script>
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
                type: 'line',
                color: '#f79d54'
            });
        }
    } else {
            chart.addSeries({
                name: name,
                data: data,
                type: 'line',
                color: '#f79d54'
            });
    }
}
</script>