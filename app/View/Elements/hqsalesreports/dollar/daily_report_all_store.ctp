<?php
if (!empty($graphData['Store']) && !empty($stores)) {
    $alltamntt = $alltorders = array();
    /**************************** For All Data in one Graph ****************************/
    
    if($graphPageNumber == 0)
    {
        foreach ($stores as $store)
        {
            if (isset($graphDataAll))
            {
                $step = '+1 day';
                $output_format = 'Y-m-d';
                $datee = $tamntt = $totalorders = array();
                $current = strtotime($startDate);
                $last = strtotime($endDate);
                while ($current <= $last) {
                    $datee[] = "'" . date($output_format, $current) . "'";
                    $current = strtotime($step, $current);
                    $tamntt[] = 0;
                    $totalorders[] = 0;
                }
                /*pr($datee);
                pr($graphData);*/
                $amnt = 0;
                $order = 0;
                $totalOrder = 0;
                $totalAmt = 0;
                $torders = $tamnt = '';
                
                foreach ($graphDataAll['Store'][$store['Store']['id']] as $data) 
                {
                    if($data['Order']['coupon_discount'] > 0)
                    {
                        $data['Order']['amount'] -= $data['Order']['coupon_discount'];
                    }
                    $datearray = explode(" ", $data['Order']['created']);
                    foreach ($datee as $key => $date) {
                        $date = str_replace("'", '', $date);
                        if ($date == $datearray[0]) {
                            if ($tamntt[$key]) {
                                $amnt +=$data['Order']['amount'];
                                $order +=1;
                                $totalOrder = $totalOrder + 1;
                                $totalAmt = $totalAmt + $data['Order']['amount'];
                            } else {
                                $amnt = $data['Order']['amount'];
                                $order = 1;
                                $totalOrder = $totalOrder + 1;
                                $totalAmt = $totalAmt + $data['Order']['amount'];
                            }
                            $tamntt[$key] = $amnt;
                            $totalorders[$key] = $order;
                        }
                    }
                }
            }
            
            //}
            $alltamntt[] = $tamntt;
            $alltorders[] = $totalorders;
            $amntdate = implode(',', $datee);
            $tamnt = implode(',', $tamntt);
            $torders = implode(',', $totalorders);
        }

        $aData = $oData = array();
        if (!empty($alltamntt)) {
            foreach ($alltamntt as $akey => $amt) {
                foreach ($amt as $key => $amtvalue) {
                    if (!empty($aData[$key])) {
                        $aData[$key] = $aData[$key] + $amtvalue;
                    } else {
                        $aData[$key] = $amtvalue;
                    }
                }
            }
        }
        if (!empty($alltorders)) {
            foreach ($alltorders as $ord) {
                foreach ($ord as $key => $ordvalue) {
                    if (!empty($oData[$key])) {
                        $oData[$key] = $oData[$key] + $ordvalue;
                    } else {
                        $oData[$key] = $ordvalue;
                    }
                }
            }
        }
        if (!empty($aData) && !empty($oData) && !empty($datee)) 
        {
            $totalAmount = array_sum($aData);
            $totalOrders = array_sum($oData);
            $text = '<style="font-size:12px;">Daily Report for</style> <br/><style="font-size:14px;">HQ</style><br/><style="font-size:12px;">' . $startDate . ' to ' . $endDate . '</style>';
            $subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalOrders . ' Order And  $ ' . $totalAmount . ' Amount</style>';
            $taData = implode(',', $aData);
            $toData = implode(',', $oData);
            ?>
            <div class="col-lg-12">
                <div id="cTotal"></div>
            </div>
            <script>
                $(function () {
                    $('#cTotal').highcharts({
                        chart: {
                            zoomType: 'xy'
                        },
                        title: {
                            text: '<?php echo $text; ?>'
                        },
                        subtitle: {
                            text: '<?php echo $subTitle; ?>'
                        },
                        xAxis: [{
                            categories: [<?php echo $amntdate; ?>],
                            crosshair: true
                        }],
                        yAxis: [
                            {// Primary yAxis
                                min: 0,
                                title: {
                                    text: 'Amount ($)',
                                    align: 'middle'
                                },
                                labels: {
                                    overflow: 'justify'
                                }
                            },
                            {// Secondary yAxis
                                min: 0,
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
                            line: {
                                dataLabels: {
                                    enabled: true
                                },
                                enableMouseTracking: true
                            },
                            series: {
                                pointWidth: 50
                            }
                        },
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            x: 0,
                            verticalAlign: 'bottom',
                            y: 0,
                            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
                        },
                        series: [
                            {
                                name: 'Order',
                                type: 'column',
                                yAxis: 1,
                                pointWidth:20,
                                color: '#f79d54',
                                data: [<?php echo $toData; ?>]

                            },
                            {
                                name: 'Amount',
                                type: 'line',
                                maxPointWidth: 50,
                                color: '#9ac456',
                                data: [<?php echo $taData; ?>]
                            }
                        ],
                        exporting: {enabled: false}
                    });
                });
            </script>
            <?php
        }
    }
    
    ?>    
    <div class="clear"></div>
    <?php
    
    /**************************** For Pagination Graph ****************************/
    
    //foreach ($stores as $store) {
    foreach($pageMerchant[$graphPageNumber] as $keyStore => $valueStore)
    {
        //if ($graphData['Store'][$store['Store']['id']]) {
        $step = '+1 day';
        $output_format = 'Y-m-d';
        $datee = $tamntt = $totalorders = array();
        $current = strtotime($startDate);
        $last = strtotime($endDate);
        while ($current <= $last) {
            $datee[] = "'" . date($output_format, $current) . "'";
            $current = strtotime($step, $current);
            $tamntt[] = 0;
            $totalorders[] = 0;
        }
        /*pr($datee);
        pr($graphData);*/
        $amnt = 0;
        $order = 0;
        $totalOrder = 0;
        $totalAmt = 0;
        $torders = $tamnt = '';
        
        foreach ($graphData['Store'][$keyStore] as $data) 
        {
            if($data['Order']['coupon_discount'] > 0)
            {
                $data['Order']['amount'] -= $data['Order']['coupon_discount'];
            }
            $datearray = explode(" ", $data['Order']['created']);
            foreach ($datee as $key => $date) {
                $date = str_replace("'", '', $date);
                if ($date == $datearray[0]) {
                    if ($tamntt[$key]) {
                        $amnt +=$data['Order']['amount'];
                        $order +=1;
                        $totalOrder = $totalOrder + 1;
                        $totalAmt = $totalAmt + $data['Order']['amount'];
                    } else {
                        $amnt = $data['Order']['amount'];
                        $order = 1;
                        $totalOrder = $totalOrder + 1;
                        $totalAmt = $totalAmt + $data['Order']['amount'];
                    }
                    $tamntt[$key] = $amnt;
                    $totalorders[$key] = $order;
                }
            }
        }
        //}
        $subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalOrder . ' Order And  $ ' . $totalAmt . ' Amount</style>';
        $alltamntt[] = $tamntt;
        $alltorders[] = $totalorders;
        $amntdate = implode(',', $datee);
        $tamnt = implode(',', $tamntt);
        $torders = implode(',', $totalorders);
        $text = '<style="font-size:12px;">Daily Report for</style> <br/><style="font-size:14px;">' . addslashes($valueStore) . '</style><br/><style="font-size:12px;">' . $startDate . ' to ' . $endDate . '</style>';
        ?>
        <div class="col-lg-4">
            <div id="<?php echo "container" . $keyStore; ?>"></div>
        </div>

        <script>
            $(function () {
                var cId = '<?php echo "container" . $keyStore; ?>';
                $('#' + cId).highcharts({
                    chart: {
                        zoomType    : 'xy'
                    },
                    title: {
                        text    : '<?php echo $text; ?>'
                    },
                    subtitle: {
                        text    : '<?php echo $subTitle; ?>'
                    },
                    xAxis: [{
                        categories  : [<?php echo $amntdate; ?>],
                        crosshair   : true
                    }],
                    yAxis: [
                        {// Primary yAxis
                            min : 0,
                            title: {
                                text    : 'Amount ($)',
                                align   : 'middle'
                            },
                            labels: {
                                overflow    : 'justify'
                            }
                        },
                        {// Secondary yAxis
                            min : 0,
                            title: {
                                text    : 'Orders',
                                align   : 'middle'
                            },
                            labels: {
                                overflow    : 'justify',
                            },
                            opposite    : true
                        }
                    ],
                    tooltip: {
                        shared  : true
                    },
                    plotOptions: {
                        line: {
                            dataLabels: {
                                enabled : true
                            },
                            enableMouseTracking : true
                        },
                        series: {
                            pointWidth  : 50
                        }
                    },
                    legend: {
                        layout          : 'horizontal',
                        align           : 'center',
                        x               : 0,
                        verticalAlign   : 'bottom',
                        y               : 0,
                        backgroundColor : (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
                    },
                    series: [
                        {
                            name        : 'Order',
                            type        : 'column',
                            yAxis       : 1,
                            pointWidth  : 20,
                            color       : '#f79d54',
                            data        : [<?php echo $torders; ?>]
                        },
                        {
                            name            : 'Amount',
                            type            : 'line',
                            maxPointWidth   : 50,
                            color           : '#9ac456',
                            data            : [<?php echo $tamnt; ?>]
                        }],
                    exporting: {enabled: false}
                });
            });
        </script>
        <?php
        //}
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
            <span><a href="/hqsalesreports/index/page:<?php echo $i;?>"><?php echo $j;?></a></span>
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
    <?php echo $this->element('hqsalesreports/dollar/paginationall'); ?>
</div>