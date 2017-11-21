<?php
if (!empty($graphData['Store']) && !empty($stores)) 
{
    $alltamntt = $alltorders = array();
    
    /**************************** For All Data in one Graph ****************************/
    $fromWeekNumber     = date('W', strtotime($startFrom)); 
    $endFromWeekNumber  = date('W', strtotime($endFrom));
    $totalCoupon = 0;
    
    $aData = $allTotalCoupon = array();
    if($graphPageNumber == 0)
    {
        foreach ($graphDataAll['Store'] as  $dataStore)
        {
            if (!empty($dataStore)) 
            {
                foreach ($dataStore as $akey => $itemArray) 
                {
                    if (isset($itemArray) && !empty($itemArray)) 
                    {
                        if(isset($itemArray['daywise']))
                        {
                            $aData[$akey]['daywise']    = $itemArray['daywise'];
                        }
                        if(isset($itemArray['datestring']))
                        {
                            $aData[$akey]['datestring'] = $itemArray['datestring'];
                        }
                        if(array_key_exists('totalcoupon', $itemArray) && isset($itemArray['totalcoupon']))
                        {
                            if(isset($aData[$akey]['totalcoupon']))
                            {
                                $aData[$akey]['week']           =  (isset($aData[$akey]['week']) ? $aData[$akey]['week'] : $akey);
                                $aData[$akey]['totalcoupon']    += $itemArray['totalcoupon'];
                                $totalCoupon                    += $itemArray['totalcoupon'];
                            }
                            else
                            {
                                $aData[$akey]['week']           =  (isset($aData[$akey]['week']) ? $aData[$akey]['week'] : $akey);
                                $aData[$akey]['totalcoupon']    =  $itemArray['totalcoupon'];
                                $totalCoupon                    += $itemArray['totalcoupon'];
                            }
                        }
                        else 
                        {
                            $aData[$akey]['week']               =  (isset($aData[$akey]['week']) ? $aData[$akey]['week'] : $akey);
                        }
                    }
                }
            }
            $allTotalCoupon[] = $aData;
        }
        
        
        /*$aData = array();
        if (!empty($allTotalCoupon)) {
            foreach ($allTotalCoupon as $akey => $amt) {
                foreach ($amt as $key => $amtvalue) {
                    if (!empty($aData[$key])) {
                        $aData[$key] = $aData[$key] + $amtvalue;
                    } else {
                        $aData[$key] = $amtvalue;
                    }
                }
            }
        }*/
        
        ksort($aData);
        if (!empty($aData) && !empty($weeknumbers)) 
        {
            //Total result
            $text = '<style="font-size:12px;">Weekly Report for</style> <br/><style="font-size:14px;">All Store</style><br/><style="font-size:12px;">Week ' . $fromWeekNumber . ' - Week ' . $endFromWeekNumber . '</style>';
            $subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalCoupon . ' Coupons </style>';
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
                            name        = ['Coupon'],      
                            data        = [
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
                                ?>
                                {
                                    y: <?php echo (isset($graphdata['totalcoupon'])) ? $graphdata['totalcoupon']:0;?>,
                                    color: '#f79d54',
                                    drilldown: {                        
                                        categories: [<?php echo @$graphdata['datestring'];?>],
                                        <?php
                                        $splinestring="";

                                        foreach($graphdata['daywise'] as $key => $innerGraphdata){
                                            if($splinestring==''){
                                                $splinestring .= (isset($innerGraphdata['totalcoupon'])) ? $innerGraphdata['totalcoupon'] : 0;    
                                            }else{
                                                $splinestring .= ","; 
                                                $splinestring .= (isset($innerGraphdata['totalcoupon'])) ? $innerGraphdata['totalcoupon'] : 0;

                                            }
                                        }
                                        
                                        if(isset($graphdata['totalcoupon'])){
                                            if($ordermax < $graphdata['totalcoupon']){
                                                $ordermax = $graphdata['totalcoupon'];
                                            }
                                        }
                                        ?>

                                        series: [{
                                            type: 'line',
                                            name: 'coupon',
                                            data: [<?php echo $splinestring;?>],
                                            color: '#f79d54'
                                        }]
                                    }
                                }

                                <?php  }?> 

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
                                max: <?php echo $ordermax+10; ?>,
                                    title: {
                                        text: 'Coupon Count',
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
                                                    setChart(name, categories,data, 'column'); 
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

                });
            </script>
            <?php
        }
    }
    
    
    /**************************** For Pagination Graph ****************************/
    
    foreach($pageMerchant[$graphPageNumber] as $keyStore => $valueStore)
    {
        $totalCoupons = 0;
        if(isset($graphData['Store'][$keyStore]))
        {
            foreach($graphData['Store'][$keyStore] as $keyTot => $graphdatatTot){
                if (isset($graphdatatTot) && !empty($graphdatatTot)) {
                    if(array_key_exists('totalcoupon', $graphdatatTot) && isset($graphdatatTot['totalcoupon']))
                    {
                        $aData[$keyTot]['daywise'] = $graphdatatTot['daywise'];
                        if(isset($aData[$keyTot]['totalcoupon']))
                        {
                            $totalCoupons                += $graphdatatTot['totalcoupon'];
                        } else {
                            $totalCoupons                 += $graphdatatTot['totalcoupon'];
                        }
                    } else {
                        $totalCoupons                += 0;
                    }
                }

            }
        }
        $text = '<style="font-size:12px;">Weekly Report for</style> <br/><style="font-size:14px;">' . addslashes($valueStore) . '</style><br/><style="font-size:12px;">Week ' . $fromWeekNumber . ' - Week ' . $endFromWeekNumber . '</style>';
        $subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalCoupons . ' Coupons </style>';
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
                    name        = ['Coupon'],
                    data        = [                
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
                        ?>
                        {
                            y: <?php echo (isset($graphdata['totalcoupon'])) ? $graphdata['totalcoupon']:0;?>,
                            color: '#f79d54',
                            drilldown: {                        
                                categories: [<?php echo @$graphdata['datestring'];?>],
                                <?php
                                $splinestring="";
                                foreach($graphdata['daywise'] as $key => $innerGraphdata){

                                    if($splinestring==''){
                                        $splinestring.=(isset($innerGraphdata['totalcoupon']))?$innerGraphdata['totalcoupon']:0;    
                                    }else{
                                        $splinestring.=","; 
                                        $splinestring.=(isset($innerGraphdata['totalcoupon']))?$innerGraphdata['totalcoupon']:0;

                                    }
                                }
                                if(isset($graphdata['totalcoupon'])){
                                    if($ordermax < $graphdata['totalcoupon']){
                                        $ordermax = $graphdata['totalcoupon'];
                                    }
                                }
                                ?>

                                series: [{
                                    type: 'line',
                                    yaxis:1,
                                    name: 'coupon',
                                    pointWidth  : 20,
                                    data: [<?php echo $splinestring;?>],
                                    color: '#f79d54'
                                }]
                            }
                        }

                        <?php }?>    

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
                        text: '<?php echo $subTitle; ?>'
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
                                text: 'Coupon Count',
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
                        color: '#f79d54',
                        data: data
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
}?>


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
    <?php echo $this->element('superNewReports/coupon/paginationall'); ?>
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