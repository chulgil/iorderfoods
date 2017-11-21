<?php
$aData = array();
if (!empty($graphData['Store']) && !empty($stores)) 
{
    $alltotalusers = $allTotalCustomer = array();
    /**************************** For All Data in one Graph ****************************/
    $fromWeekNumber     = date('W', strtotime($startFrom)); 
    $endFromWeekNumber  = date('W', strtotime($endFrom));
    $totalUsers = 0;
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
                        if(array_key_exists('totaluser', $amt) && isset($amt['totaluser']))
                        {
                            if(isset($aData[$akey]['totaluser']))
                            {
                                $aData[$akey]['week'] = (isset($aData[$akey]['week']) ? $aData[$akey]['week'] : $akey);
                                $aData[$akey]['totaluser'] = $aData[$akey]['totaluser'] + $amt['totaluser'];
                                $totalUsers                += $amt['totaluser'];
                            } else {
                                $aData[$akey]['week'] = (isset($aData[$akey]['week']) ? $aData[$akey]['week'] : $akey);
                                $aData[$akey]['totaluser'] = $amt['totaluser'];
                                $totalUsers                 += $amt['totaluser'];
                            }
                            $aData[$akey]['datestring'] = $amt['datestring'];
                        }
                        else 
                        {
                            $aData[$akey]['week'] = (isset($aData[$akey]['week']) ? $aData[$akey]['week'] : $akey);
                            $totalUsers                += 0;
                        }
                    }
                }
            }
            
            $allTotalCustomer[] = $aData;
        }
        
        ksort($aData);
        
        /*$aData = array();
        if (!empty($allTotalCustomer)) {
            foreach ($allTotalCustomer as $akey => $amt) {
                foreach ($amt as $key => $amtvalue) {
                    if (!empty($aData[$key])) {
                        $aData[$key] = $aData[$key] + $amtvalue;
                    } else {
                        $aData[$key] = $amtvalue;
                    }
                }
            }
        }*/
        
        //pr($aData);;
        
        if (!empty($aData) && !empty($weeknumbers)) 
        {
            //$totalUser = array_sum($aData);
            $text = '<style="font-size:12px;">Weekly Report for</style> <br/><style="font-size:14px;">All Store</style><br/><style="font-size:12px;">Week ' . $fromWeekNumber . ' - Week ' . $endFromWeekNumber . '</style>';
            $subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalUsers . ' Customer</style>';
            //$taData = implode(',', $aData);
            ?>
            <div class="col-lg-4">
                <div id="cTotal"></div>
            </div>
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
                            foreach($aData as $key => $graphdata){
                                //pr($graphdata);
                                if($i==0){
                                  $commaseparate="";  
                                }else{
                                  $commaseparate=",";
                                }
                                echo $commaseparate;
                                $i++;
                            ?>
                            {
                                y: <?php echo (isset($graphdata['totaluser'])) ? $graphdata['totaluser']:0;?>,
                                color: '#f79d54',
                                drilldown: {                        
                                    categories: [<?php echo @$graphdata['datestring'];?>],
                                    <?php
                                    $coloumstring="";
                                    if(isset($graphdata['daywise']))
                                    {
                                        foreach($graphdata['daywise'] as $key => $innerGraphdata)
                                        {
                                            if($coloumstring==''){
                                                $coloumstring.=(isset($innerGraphdata['totaluser']))?$innerGraphdata['totaluser']:0;
                                            }else{
                                                $coloumstring.=",";    
                                                $coloumstring.=(isset($innerGraphdata['totaluser']))?$innerGraphdata['totaluser']:0;
                                            }


                                            if(isset($innerGraphdata['totaluser'])){
                                                if($amountmax < $innerGraphdata['totaluser']){
                                                    $amountmax = $innerGraphdata['totaluser'];
                                                }
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
                            renderTo: 'cTotal',
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
                            data: data,
                            color: '#f79d54'
                        }],
                        exporting: {
                            enabled: false
                        }
                    });
                    
                });
            </script>
            <?php
        }
    }
    
    
    
    /**************************** For Pagination Graph ****************************/
    
    foreach($pageMerchant[$graphPageNumber] as $keyStore => $valueStore)
    {
        $totalUsers = 0;
        if(isset($graphData['Store'][$keyStore]))
        {
            foreach($graphData['Store'][$keyStore] as $keyTot => $graphdatatTot){
                if (isset($graphdatatTot) && !empty($graphdatatTot)) {
                    
                    if(isset($graphdatatTot['daywise']))
                    {
                        if(array_key_exists('totaluser', $graphdatatTot) && isset($graphdatatTot['totaluser']))
                        {
                            $aData[$keyTot]['daywise'] = $graphdatatTot['daywise'];
                            if(isset($aData[$keyTot]['totaluser']))
                            {
                                $totalUsers                 += $graphdatatTot['totaluser'];
                            } else {
                                $totalUsers                 += $graphdatatTot['totaluser'];
                            }
                        } else {
                            $totalUsers                     += 0;
                        }
                    }
                }

            }
        }
        
        $text = '<style="font-size:12px;">Weekly Report for</style> <br/><style="font-size:14px;">' . addslashes($valueStore) . '</style><br/><style="font-size:12px;">Week ' . $fromWeekNumber . ' - Week ' . $endFromWeekNumber . '</style>';
        $subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalUsers . ' Customer</style>';

        ?>
        <div class="col-lg-4">
            <div id="<?php echo "container" . $keyStore; ?>"></div>
        </div>
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
                        foreach($graphData['Store'][$keyStore] as $key => $graphdata)
                        {
                            if($i==0)
                            {
                              $commaseparate="";  
                            }
                            else
                            {
                              $commaseparate=",";
                            }
                            echo $commaseparate;
                            $i++;
                        ?>
                        {
                            y: <?php echo (isset($graphdata['totaluser'])) ? $graphdata['totaluser']:0;?>,
                            color: '#f79d54',
                            drilldown: {                        
                                categories: [<?php echo @$graphdata['datestring'];?>],
                                <?php
                                $coloumstring="";
                                if(isset($graphdata['daywise']))
                                {
                                    foreach($graphdata['daywise'] as $key => $innerGraphdata)
                                    {
                                        if($coloumstring==''){
                                            $coloumstring.=(isset($innerGraphdata['totaluser']))?$innerGraphdata['totaluser']:0;
                                        }else{
                                            $coloumstring.=",";    
                                            $coloumstring.=(isset($innerGraphdata['totaluser']))?$innerGraphdata['totaluser']:0;
                                        }


                                        if(isset($innerGraphdata['totaluser'])){
                                            if($amountmax < $innerGraphdata['totaluser']){
                                                $amountmax = $innerGraphdata['totaluser'];
                                            }
                                        }
                                    }
                                }
                                
                                ?>

                                series: [{
                                    type: 'line',
                                    name: 'Customer',
                                    data: [<?php echo $coloumstring;?>],
                                    color: '#f79d54'
                                }]
                            }
                        }

                        <?php  }?>    

                        ];

                chart = new Highcharts.Chart({
                    chart: {
                        renderTo: 'container<?php echo $keyStore; ?>',
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
                        data: data,
                        color: '#f79d54'
                    }],
                    exporting: {
                        enabled: false
                    }
                });

            });
        </script>
        <?php
    }
}     
?>

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
    <?php echo $this->element('superNewReports/customer/paginationall'); ?>
</div>
    
<script>
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