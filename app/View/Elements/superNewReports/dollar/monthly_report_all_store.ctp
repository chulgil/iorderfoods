<?php
if (!empty($graphData['Store']) && !empty($stores)) {
    $alltamntt = $alltorders = array();
    //foreach ($stores as $store) {
    /**************************** For All Data in one Graph ****************************/
    
    if($graphPageNumber == 0)
    {
        foreach ($stores as $store) 
        {
            if(isset($graphDataAll))
            {
                $totalOrder = 0;
                $totalAmt = 0;
                $datee = array('1' => "'Jan'", '2' => "'Feb'", '3' => "'Mar'", '4' => "'Apr'", '5' => "'May'", '6' => "'Jun'", '7' => "'Jul'", '8' => "'Aug'", '9' => "'Sep'", '10' => "'Oct'", '11' => "'Nov'", '12' => "'Dec'");
                $tamntt = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0, '12' => 0);
                $torders = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0, '12' => 0);
                $order = 0;
                
                foreach ($graphDataAll['Store'][$store['Store']['id']] as $amount) 
                {
                    if($amount['Order']['coupon_discount'] > 0)
                    {
                        $amount['Order']['amount'] -= $amount['Order']['coupon_discount'];
                    }
                    $tamntt[date('n', strtotime($amount['Order']['created']))]  +=  $amount['Order']['amount'];
                    $datee[date('n', strtotime($amount['Order']['created']))]   =   "'" . date('M', strtotime($amount['Order']['created'])) . "'";
                    $torders[date('n', strtotime($amount['Order']['created']))] =   $torders[date('n', strtotime($amount['Order']['created']))] + 1;
                    $totalOrder                                                 =   $totalOrder + 1;
                    $totalAmt                                                   +=  $amount['Order']['amount'];
                }
                $alltamntt[] = $tamntt;
                $alltorders[] = $torders;
                $amntdate = implode(',', $datee);
                $tamnt = implode(',', $tamntt);
                $torders = implode(',', $torders);   
            }
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
        if (!empty($aData) && !empty($oData) && !empty($datee)) {//Total result
            $totalAmount = array_sum($aData);
            $totalOrders = array_sum($oData);
            $text = '<style="font-size:12px;">Monthly Report For</style> <br/><style="font-size:14px;">All Store</style><br/><style="font-size:12px;">' . str_replace('\'', '', $datee[$month]) . ' - ' . $year;
            $subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalOrders . ' Order And  $ ' . $totalAmount . ' Amount</style>';
            $taData = implode(',', $aData);
            $toData = implode(',', $oData);
            ?>
            <div class="col-lg-4">
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
                                name        : 'Order',
                                type        : 'column',
                                yAxis       : 1,
                                pointWidth  : 20,
                                color       : '#f79d54',
                                data        : [<?php echo $toData; ?>]

                            },
                            {
                                name            : 'Amount',
                                type            : 'line',
                                maxPointWidth   : 50,
                                color           : '#9ac456',
                                data            : [<?php echo $taData; ?>]
                            }
                        ],
                        exporting: {enabled: false}
                    });
                });
            </script>
            <?php
        }
    }
    
    /**************************** For Pagination Graph ****************************/
    foreach($pageMerchant[$graphPageNumber] as $keyStore => $valueStore)
    {
        $totalOrder = 0;
        $totalAmt = 0;
        $datee = array('1' => "'Jan'", '2' => "'Feb'", '3' => "'Mar'", '4' => "'Apr'", '5' => "'May'", '6' => "'Jun'", '7' => "'Jul'", '8' => "'Aug'", '9' => "'Sep'", '10' => "'Oct'", '11' => "'Nov'", '12' => "'Dec'");
        $tamntt = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0, '12' => 0);
        $torders = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0, '12' => 0);
        $order = 0;
        foreach ($graphData['Store'][$keyStore] as $amount) 
        {
            if($amount['Order']['coupon_discount'] > 0)
            {
                $amount['Order']['amount'] -= $amount['Order']['coupon_discount'];
            }
            $tamntt[date('n', strtotime($amount['Order']['created']))] += $amount['Order']['amount'];
            $datee[date('n', strtotime($amount['Order']['created']))] = "'" . date('M', strtotime($amount['Order']['created'])) . "'";
            $torders[date('n', strtotime($amount['Order']['created']))] = $torders[date('n', strtotime($amount['Order']['created']))] + 1;
            $totalOrder = $totalOrder + 1;
            $totalAmt = $totalAmt + $amount['Order']['amount'];
        }
        $subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalOrder . ' Order And  $ ' . $totalAmt . ' Amount</style>';
        $alltamntt[] = $tamntt;
        $alltorders[] = $torders;
        $amntdate = implode(',', $datee);
        $tamnt = implode(',', $tamntt);
        $torders = implode(',', $torders);
        $text = '<style="font-size:12px;">Monthly Report For</style> <br/><style="font-size:14px;">' . addslashes($valueStore) . '</style><br/><style="font-size:12px;">' . str_replace('\'', '', $datee[$month]) . ' - ' . $year;
        ?>
        <div class="col-lg-4">
            <div id="<?php echo "container" . $keyStore; ?>"></div>
        </div>
        <script>
            $(function () {
                var cId = '<?php echo "container" . $keyStore; ?>';
                $('#' + cId).highcharts({
                    chart: {
                        zoomType: 'xy',
                        type: 'line'
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
    <?php echo $this->element('superNewReports/dollar/paginationall'); ?>
</div>