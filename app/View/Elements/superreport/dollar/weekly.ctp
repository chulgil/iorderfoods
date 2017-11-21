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
            name = ['Amount'],    
            name2 = ['Order'],    
            data = [                
                <?php
                $i=0;
                $amountmax=0;
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
                    y: <?php echo (isset($graphdata['totalamount']))?$graphdata['totalamount']:0;?>,
                    color: colors[0],
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
                            type: 'column',
                            name: 'amount',
                            data: [<?php echo $coloumstring;?>],
                            color: colors[0]
                        },{
                            type: 'spline',
                            yaxis:1,
                            name: 'order',
                            data: [<?php echo $splinestring;?>],
                            color: colors[1]
                        }]
                    }
                }
                
                <?php  }?>    
                
                ],
                
                data2 = [
                
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
        
        function setChartColumn(name, categories, data, type) { 
            chart.xAxis[0].setCategories(categories); 
            var dataLen = data.length; 
            while(chart.series.length>0) 
                chart.series[0].remove(); 
            for(var i = 0; i< dataLen; i++){ 
                chart.addSeries({ 
                    type: (i == 0 ? 'column' : 'spline'), 
                    name: name[i], 
                    data: data[i], 
                    yAxis: (i == 0 ? 0 : 1), 
                    color: colors[i] 
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
                        type: data.series[i].type,
                        yAxis: (data.series[i].type == 'column' ? 0 : 1), 
                        color: data.series[i].color || 'white'
                    });
                }
            } else {
                    chart.addSeries({
                        name: name,
                        data: data,
                        type: type,
                        yAxis: (type == 'column' ? 0 : 1), 
                        color: color || 'white'
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
                    max: <?php echo $ordermax+10; ?>,
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
                                    setChartColumn([name,name2], categories, [data,data2], ['column','spline']); 
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
                },
                spline: {
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function() {
                                setChartColumn([name,name2], categories, [data,data2], ['column','spline']); 
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
                color:'#434348',
                data: data
            },{
                name: name2,
                type: 'spline',
                yAxis: 1,
                color:'#434348',
                data: data2 
            }],
            exporting: {
                enabled: false
            }
        });
    });
    
});
    
    
    
        
</script>


<script>
 /*    
$(function () {
    var chart;
    $(document).ready(function() {
    
        var colors = Highcharts.getOptions().colors,
            categories = [<?php echo $weeknumbers;?>],
            name = ['Weeks'],            
            data = [
                
                <?php
                $i=0;
                $amountmax=0;
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
                    y: <?php echo (isset($graphdata['totalamount']))?$graphdata['totalamount']:0;?>,
                    color: colors[0],
                    drilldown: {                        
                        categories: [<?php echo @$graphdata['datestring'];?>],
                        <?php
                        $coloumstring="";
                        $splinestring="";
                        
                        foreach($graphdata['daywise'] as $key => $innerGraphdata){
                                if($coloumstring==''){
                                    $coloumstring=(isset($innerGraphdata['total']))?$innerGraphdata['total']:0;
                                }else{
                                    $coloumstring.=",";    
                                    $coloumstring.=(isset($innerGraphdata['total']))?$innerGraphdata['total']:0;
                                }
                                
                                if($splinestring==''){
                                    $splinestring=(isset($innerGraphdata['totalorders']))?$innerGraphdata['totalorders']:0;    
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
                            type: 'spline',
                            yaxis:1,
                            name: 'order',
                            data: [<?php echo $splinestring;?>],
                            color: colors[1]
                        },{
                            type: 'column',
                            name: 'amount',
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
                console.log(chart.series.length);
                chart.series[0].remove();
            }
            console.log('a');
            if(data.series){
                for(var i = 0; i < data.series.length; i ++ ){
                    chart.addSeries({
                        name: data.series[i].name,
                        data: data.series[i].data,
                        type: data.series[i].type,
                        color: data.series[i].color || 'white'
                    });
                }
            } else {
                    chart.addSeries({
                        name: name,
                        data: data,
                        type: type,
                        color: color || 'white'
                    });
            }
        }
    
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'column',
                zoomType: 'xy'
            },
            title: {
                text: 'Weeks result'
            },
            xAxis: {
                categories: categories,
                crosshair: true
            },
            yAxis: [
                { // Primary yAxis
                min: 0,
                max: <?php echo $amountmax; ?>,
                        title: {
                            text: 'Amount ($)',
                            align: 'high'                            
                        },
                        labels: {
                            overflow: 'justify'                            
                        }
                },
                { // Secondary yAxis   
                    min: 0,
                    max: <?php echo $ordermax; ?>,
                        title: {
                            text: 'Orders',
                            align: 'high'
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
                                    setChart(name, categories, data, drilldown.type);
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
                },
                spline: {
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function() {
                                setChart(name, categories, data, type);
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
            series: [{
                name: name,
                data: data,
                color: '#7CB5EC'
            }],
            exporting: {
                enabled: false
            }
        });
    });
    
}); */
    
    
    
        
</script>

