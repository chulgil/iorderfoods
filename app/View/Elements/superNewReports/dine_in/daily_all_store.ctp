<?php
if (!empty($graphData['Store']) && !empty($stores)) {
    $totalDineIn = array();
    $totalItem = 0;
    /**************************** For All Data in one Graph ****************************/
    
    if($graphPageNumber == 0)
    {
        $i = 0;
        foreach ($stores as $store)
        {
            if (isset($graphDataAll))
            {
                $step = '+1 day';
                $output_format = 'Y-m-d';
                $datee = $tamntt = $totalorders = array();
                $current = strtotime($startDate);
                $last = strtotime($endDate);
                while ($current <= $last) 
                {
                    $datee[]                                        = "'".date($output_format, $current)."'";
                    $totalDineIn[date($output_format, $current)]    = 0;    
                    $current                                        = strtotime($step, $current);
                }
                
                
                
                foreach ($graphDataAll['Store'][$store['Store']['id']] as $key => $data) 
                {
                    if(!empty($data))
                    {
                        $result1[$i]=$data[0];
                        unset($data);
                    }
                    $i++;
                }
                
                if(!empty($result1)){
                    foreach($result1 as $dinein){
                        if(array_key_exists($dinein['order_date'], $totalDineIn))
                        {
                            $totalDineIn[$dinein['order_date']] += 1;
                        } else {
                            $totalDineIn[$dinein['order_date']] += 1;
                        }
                    }
                    
                }
            }
            $allTotalDineIn[] = $totalDineIn;
        }
        
        $totalItem = array_sum($totalDineIn);
        if (!empty($totalDineIn) && !empty($datee)) 
        {
            $text = '<style="font-size:12px;">Daily Report for</style> <br/><style="font-size:14px;">All Store</style><br/><style="font-size:12px;">' . $startDate . ' to ' . $endDate . '</style>';
            $subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalItem . ' Dine In</style>';
            $taData = implode(',', $totalDineIn);
            $itemdate = implode(',', $datee);
            ?>
            <div class="col-lg-4">
                <div id="cTotal"></div>
            </div>
            <script>
                $(function () {
                    $('#cTotal').highcharts({
                        chart: {
                            type: 'line'
                        },
                        title: {
                            text: '<?php echo  $text;?>'
                        },
                        subtitle: {
                            text: '<?php echo $subTitle;?>'
                        },
                        xAxis: {
                            categories: [<?php echo $itemdate;?>],
                            title: {
                                text: null
                            },
                            crosshair: true
                        },
                       yAxis: {
                            min: 0,
                            title: {
                                text: 'Dine In Count',
                                align: 'middle'
                            },
                            labels: {
                                overflow: 'justify'
                            }
                        },
                        tooltip: {
                            valueSuffix: ''
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
                        exporting: { enabled: false },
                        series: [{
                            name: 'Dine In',
                            data: [<?php echo $taData; ?>],
                            color: '#f79d54'

                        }]
                    });
                });
            </script>
            <?php
        }
    }
    
    /**************************** For Pagination Graph ****************************/
    
    foreach($pageMerchant[$graphPageNumber] as $keyStore => $valueStore)
    {
        $step = '+1 day';
        $output_format = 'Y-m-d';
        $datee = $result1 = array();
        $current = strtotime($startDate);
        $last = strtotime($endDate);
        while ($current <= $last) {
            $datee[]                                    = "'".date($output_format, $current)."'";
            $totalDineIn[date($output_format, $current)]= 0;    
            $current                                    = strtotime($step, $current);
        }
        $totalItem = 0;
        foreach ($graphData['Store'][$keyStore] as $key => $data) 
        {
            $result1[$key]=$data[0];
            unset($data); 
        }
        if(!empty($result1)){
            foreach($result1 as $coupon){
                if(array_key_exists($coupon['order_date'], $totalDineIn))
                {
                    $totalDineIn[$coupon['order_date']] += 1;
                } else {
                    $totalDineIn[$coupon['order_date']] = 1;
                }
                $totalItem += 1;
            }
        }
        $text = '<style="font-size:12px;">Daily Report for</style> <br/><style="font-size:14px;">' . addslashes($valueStore) . '</style><br/><style="font-size:12px;">' . $startDate . ' to ' . $endDate . '</style>';
        $subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalItem . ' Dine In</style>';
        $taData = implode(',', $totalDineIn);
        $itemdate = implode(',', $datee);
        ?>
        <div class="col-lg-4">
            <div id="<?php echo "container" . $keyStore; ?>"></div>
        </div>

        <script>
            $(function () {
                var cId = '<?php echo "container" . $keyStore; ?>';
                $('#' + cId).highcharts({
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: '<?php echo  $text;?>'
                    },
                    subtitle: {
                        text: '<?php echo $subTitle;?>'
                    },
                    xAxis: {
                        categories: [<?php echo $itemdate;?>],
                        title: {
                            text: null
                        },
                        crosshair: true
                    },
                   yAxis: {
                        min: 0,
                        title: {
                            text: 'Dine In Count',
                            align: 'middle'
                        },
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    tooltip: {
                        valueSuffix: ''
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
                    exporting: { enabled: false },
                    series: [{
                        name: 'Dine In',
                        data: [<?php echo $taData; ?>],
                        color: '#f79d54'

                    }]
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
    <?php echo $this->element('superNewReports/dine_in/paginationall'); ?>
</div>