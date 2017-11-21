<?php
$fromWeekNumber     = date('W', strtotime($startFrom)); 
$endFromWeekNumber  = date('W', strtotime($endFrom));
$text="Weekly Report for Week ".$fromWeekNumber." to Week ". $endFromWeekNumber;
$subTitle = '<style="font-size:14px;font-weight:bold;">Total '.$totalCustomer.' Customer </style>';
?>
<?php echo $this->element('chart/chart_script');?>

<div id="container"></div>
<script>  
$(function () {
    var chart;
    var colors = Highcharts.getOptions().colors,
        categories = [<?php echo $weeknumbers;?>],
        name = ['Customer'],    
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
                color: '#f79d54',
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
                        type: 'line',
                        name: 'Customer',
                        data: [<?php echo $coloumstring;?>],
                        color: '#f79d54'
                        //color: colors[0]
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
            line: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function() {
                            var drilldown = this.drilldown;
                            if (drilldown) { // drill down
                                setChart(null, drilldown.categories, drilldown, drilldown.type);
                            } else { // restore
                                setChart(name, categories,data,'line'); 
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
            series: {
                pointWidth: 50
            }
        },
        series: [ {
            name: name,
            type: 'line',
            //color: colors[0],
            data: data,
            color: '#f79d54'
        }],
        exporting: {
            enabled: false
        }
    });
});


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

<div id="pagination_data_request">
    <?php echo $this->element('superNewReports/customer/pagination');?>
</div>