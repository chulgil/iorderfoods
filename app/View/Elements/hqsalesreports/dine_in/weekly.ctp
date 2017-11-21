<?php
$fromWeekNumber     = date('W', strtotime($startFrom)); 
$endFromWeekNumber  = date('W', strtotime($endFrom));

$startmonth = date('m', strtotime($startFrom));
$endmonth   = date('m', strtotime($endFrom));

$startyear  = date('Y', strtotime($startFrom));
$endyear    = date('Y', strtotime($endFrom));

$titleText = $this->Common->dineInTitleString($startmonth, $startyear, $endmonth, $endyear);
$text = '<style="font-size:14px;font-weight:bold;">' . $titleText . '</style>';
$subTitle = '<style="font-size:14px;font-weight:bold;">Total '.$totalDineIn.' Reservations </style>';
//$text="Weekly Report for Week ".$fromWeekNumber." to Week ". $endFromWeekNumber;


        
/* For Pie Chart*/
$pieData = $this->Common->dineInPieDataArrange($dineInPieData);

$totalPie = (isset($pieData['total']) ? $pieData['total'] : 0);
$finalPie = (isset($pieData['data']) ? $pieData['data'] : array());
/* End For Pie Chart*/
?>
<?php echo $this->element('chart/chart_script');?>

<div class="col-lg-<?php echo (($totalPie == 0) ? '12' : '6');?>">
    <div id="container"></div>
</div>
<div class="col-lg-6 <?php echo (($totalPie == 0) ? 'hidden' : '');?>">
    <div id="reservationPie"></div>
</div>
<script>  
$(function () {
    var chart;
    $(document).ready(function() {

        var colors = Highcharts.getOptions().colors,
            categories = [<?php echo $weeknumbers;?>],
            name = ['Reservations'],      
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
                    y: <?php echo (isset($graphdata['totalcount'])) ? $graphdata['totalcount']:0;?>,
                    color: '#f79d54',
                    drilldown: {                        
                        categories: [<?php echo @$graphdata['datestring'];?>],
                        <?php
                        $splinestring="";

                        foreach($graphdata['daywise'] as $key => $innerGraphdata){
                            if($splinestring==''){
                                $splinestring.=(isset($innerGraphdata['totalcount']))?$innerGraphdata['totalcount']:0;    
                            }else{
                                $splinestring.=","; 
                                $splinestring.=(isset($innerGraphdata['totalcount']))?$innerGraphdata['totalcount']:0;

                            }

                            if(isset($innerGraphdata['totalcount'])){
                                if($ordermax < $innerGraphdata['totalcount']){
                                       $ordermax = $innerGraphdata['totalcount'];
                                }
                            }
                        }

                        ?>

                        series: [{
                            type: 'line',
                            name: 'Reservations',
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
                        text: '# Of Reservations',
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
                },
                series: {
                    pointWidth: 50
                }
            },
            series: [ {
                name: name,
                type: 'line',
                color: '#f79d54',
                data: data
            }],
            exporting: {
                enabled: false
            }
        });
    });
    
    
    /* Pie Chart Start */
    Highcharts.chart('reservationPie', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Reservation Status'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}% ({point.pointcount})</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    distance : -50,
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} % ({point.pointcount})',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            name: 'Reservations',
            colorByPoint: true,
            data: [
            <?php
            foreach ($finalPie as $finalPieV)
            {
                $percentage = ($finalPieV['status_count'] / $totalPie) * 100;
                ?>
                {
                    name        : '<?php echo $finalPieV['status_name']?>',
                    y           : <?php echo $percentage?>,
                    color       : '<?php echo $finalPieV['color']?>',
                    pointcount  : '<?php echo $finalPieV['status_count'];?>'
                },
                <?php
            }
            ?>
             ]
        }],
        exporting: { enabled: false }
    });
    /* Pie Chart End */
});
        
</script>
<div id="pagination_data_request">
    <?php echo $this->element('hqsalesreports/dine_in/pagination'); ?>
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