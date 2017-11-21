<?php
if (!empty($graphData['Store']) && !empty($stores)) {
    $alltamntt = $alltorders = array();
    /**************************** For All Data in one Graph ****************************/
    $fromWeekNumber     = date('W', strtotime($startFrom)); 
    $endFromWeekNumber  = date('W', strtotime($endFrom));
    $totalOrders = $totalAmount = 0;
    
    $aData = $oData = array();
    if($graphPageNumber == 0)
    {
        foreach ($graphDataAll['Store'] as  $dataStore)
        {
            if (!empty($dataStore)) 
            {
                foreach ($dataStore as $akey => $amt) 
                {
                    if (isset($amt) && !empty($amt)) 
                    {
                        if(isset($amt['daywise']))
                        {
                            $aData[$akey]['daywise']    = $amt['daywise'];
                        }
                        if(isset($amt['datestring']))
                        {
                            $aData[$akey]['datestring'] = $amt['datestring'];
                        }
                        
                        if(array_key_exists('totalorders', $amt) && isset($amt['totalorders']))
                        {
                            if(isset($aData[$akey]['totalorders']))
                            {
                                $aData[$akey]['week'] = (isset($aData[$akey]['week']) ? $aData[$akey]['week'] : $akey);
                                $aData[$akey]['totalorders'] = $aData[$akey]['totalorders'] + $amt['totalorders'];
                                $totalOrders                += $amt['totalorders'];
                            } else {
                                $aData[$akey]['week'] = (isset($aData[$akey]['week']) ? $aData[$akey]['week'] : $akey);
                                $aData[$akey]['totalorders'] = $amt['totalorders'];
                                $totalOrders                 += $amt['totalorders'];
                            }
                        }
                        else 
                        {
                            $aData[$akey]['week'] = (isset($aData[$akey]['week']) ? $aData[$akey]['week'] : $akey);
                        }

                        if(array_key_exists('total', $amt) && isset($amt['total']))
                        {
                            if(isset($aData[$akey]['total']))
                            {
                                $aData[$akey]['total']  = $aData[$akey]['total'] + $amt['total'];
                            } else {
                                $aData[$akey]['total'] = $amt['total'];
                            }
                        }

                        if(array_key_exists('totalamount', $amt) && isset($amt['totalamount']))
                        {
                            if(isset($aData[$akey]['totalamount']))
                            {
                                $aData[$akey]['totalamount'] = $aData[$akey]['totalamount'] + $amt['totalamount'];
                                $totalAmount                 += $amt['totalamount'];
                            } else {
                                $aData[$akey]['totalamount'] = $amt['totalamount'];
                                $totalAmount                 += $amt['totalamount'];
                            }
                        }
                    }
                }
            }
        }
        ksort($aData);
        if (!empty($aData) && !empty($weeknumbers)) {//Total result
            $text = '<style="font-size:12px;">Weekly Report For</style> <br/><style="font-size:14px;">All Store</style><br/><style="font-size:12px;"> Week ' . $fromWeekNumber . ' - Week ' . $endFromWeekNumber . '</style>';
            $subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalOrders . ' Order And  $ ' . $totalAmount . ' Amount</style>';
            ?>
            <div class="col-lg-4">
                <div id="cTotal"></div>
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
                                foreach($aData as $key => $graphdata){
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
                                    color: '#9ac456',
                                    drilldown: {                        
                                        categories: [<?php echo @$graphdata['datestring'];?>],
                                        <?php
                                        $coloumstring="";
                                        $splinestring="";
                                        foreach($graphdata['daywise'] as $key => $innerGraphdata)
                                        {
                                            if($coloumstring==''){
                                                $coloumstring.=(isset($innerGraphdata['total']))?$innerGraphdata['total']:0;
                                            }else{
                                                $coloumstring.=",";    
                                                $coloumstring.=(isset($innerGraphdata['total']))?$innerGraphdata['total']:0;
                                            }

                                            if($splinestring == '')
                                            {
                                                $splinestring .=(isset($innerGraphdata['totalorders']))?$innerGraphdata['totalorders']:0;    
                                            }
                                            else
                                            {
                                                $splinestring .=","; 
                                                $splinestring .=(isset($innerGraphdata['totalorders']))?$innerGraphdata['totalorders']:0;
                                            }
                                            if(isset($innerGraphdata['totalamount'])){
                                                if($amountmax < $innerGraphdata['totalamount']){
                                                    $amountmax = $innerGraphdata['totalamount'];
                                                }
                                            }
                                        }
                                        if(isset($graphdata['totalamount'])){
                                            if($amountmax < $graphdata['totalamount']){
                                                $amountmax = $graphdata['totalamount'];
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
                                            pointWidth  : 20,
                                            yaxis:1,
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
                                foreach($aData as $key => $graphdata){
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
                                    
                                    if(isset($graphdata['totalorders'])){
                                        if($ordermax < $graphdata['totalorders']){
                                               $ordermax = $graphdata['totalorders'];
                                        }
                                    }
                                }
                                ?>    

                                ];



                        chart = new Highcharts.Chart({
                            chart: {
                                renderTo: 'cTotal',
                                zoomType: 'xy'
                            },
                            title: {
                                text: '<?php echo $text; ?>'
                            },
                            subtitle: {
                                text: '<?php echo $subTitle; ?>'
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
                                },
                                series: {
                                    pointWidth: 50
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
            <?php
        }
    }
    
    
    /**************************** For Pagination Graph ****************************/
    
    foreach($pageMerchant[$graphPageNumber] as $keyStore => $valueStore)
    {
        $totalOrders = $totalAmount = 0;
        if(isset($graphData['Store'][$keyStore]))
        {
            foreach($graphData['Store'][$keyStore] as $keyTot => $graphdatatTot){
                if (isset($graphdatatTot) && !empty($graphdatatTot)) {
                    if(array_key_exists('totalorders', $graphdatatTot) && isset($graphdatatTot['totalorders']))
                    {
                        $aData[$keyTot]['daywise'] = $graphdatatTot['daywise'];
                        if(isset($aData[$keyTot]['totalorders']))
                        {
                            $totalOrders                += $graphdatatTot['totalorders'];
                        } else {
                            $totalOrders                 += $graphdatatTot['totalorders'];
                        }
                    } else {
                        $totalOrders                += 0;
                    }

                    if(array_key_exists('totalamount', $graphdatatTot) && isset($graphdatatTot['totalamount']))
                    {
                        if(isset($aData[$keyTot]['totalamount']))
                        {
                            $totalAmount                 += $graphdatatTot['totalamount'];
                        } else {
                            $totalAmount                 += $graphdatatTot['totalamount'];
                        }
                    } else {
                        $totalAmount                 += 0;
                    }
                }

            }
        }
        
        $text = '<style="font-size:12px;">Weekly Report For</style> <br/><style="font-size:14px;">' . addslashes($valueStore) . '</style><br/><style="font-size:12px;">Week ' . $fromWeekNumber . ' - Week ' . $endFromWeekNumber . '</style>';
        $subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalOrders . ' Order And  $ ' . $totalAmount . ' Amount</style>';
        ?>
        <div class="col-lg-4">
            <div id="<?php echo "container" . $keyStore; ?>"></div>
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
                        foreach($graphData['Store'][$keyStore] as $key => $graphdata){
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
                                    name: 'order',
                                    pointWidth  : 20,
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
                        foreach($graphData['Store'][$keyStore] as $key => $graphdata){
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
                        }
                        ?>    

                        ];



                chart = new Highcharts.Chart({
                    chart: {
                        renderTo: 'container<?php echo $keyStore; ?>',
                        zoomType: 'xy'
                    },
                    title: {
                        text: '<?php echo $text; ?>'
                    },
                    subtitle: {
                        text: '<?php echo $subTitle; ?>'
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
                        },
                        series: {
                            pointWidth: 50
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
        <?php
    }
    //pr($graphData);
}?>

        
<script>
function setChartColumn(name, categories, data, type) { 
    chart.xAxis[0].setCategories(categories); 
    var dataLen = data.length; 
    while(chart.series.length>0) 
        chart.series[0].remove(); 
    for(var i = 0; i< dataLen; i++){ 
        chart.addSeries({ 
            type: (i == 0 ? 'column' : 'line'),
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
                color: '#9ac456'
            });
        }
    } else {
            chart.addSeries({
                name: name,
                data: data,
                type: 'column',
                pointWidth  : 20,
                color: '#9ac456'
            });
    }
}
</script>



<?php
if($allPagesCount > 1){
    ?>
    <?php echo $this->Html->css('pagination'); ?>
        <div class="clear clear-clearfix"></div>
    <div class="paginator paging_full_numbers graph-paginator" id="example_paginate" style="padding-top:10px">
    <?php
    for ($i=0, $j=1; $i < $allPagesCount, $j <= $allPagesCount; $i++, $j++)
    {
        if($i == $graphPageNumber)
        {
            ?>
            <span class="current"><?php echo $j;?></span>
            <?php
        } else {
            ?>
            <span><a href="/superNewReports/index/page:<?php echo $i;?>"><?php echo $j;?></a></span>
            <?php
        }
    }
    ?>
    </div>
    <style>
    #example_paginate span:nth-last-child(2) {padding: 2px 8px;}
    .clear{clear: both;}
    </style>
    <script>
        $(document).ready(function(){
            $(".graph-paginator a").click(function(e){
                e.preventDefault();
                var page = $.urlParam(this.href,'/');
                var page = $.urlParam(page,':');

                fetchGraphPaginationData(page);
                return false;
            });
        });


        $.urlParam = function(url,delimeter, c = 1){
            var param = '';
            if(url.length > 0)
            {
                param = url.split(delimeter);
                if(param.length > 0){
                    return param[param.length-c];
                }
            }
        }
    </script>
    <?php
}
?>
<div id="pagination_data_request">
    <?php echo $this->element('superNewReports/dollar/paginationall'); ?>
</div>