<?php
if (!empty($graphData['Store']) && !empty($stores)) {
    $alltamntt = $datee = array();
    /**************************** For All Data in one Graph ****************************/
    $totalDineIn=0;
    if($graphPageNumber == 0)
    {
        foreach ($stores as $store)
        {
            if (isset($graphDataAll))
            {
                $result1 = array();
                $difference = $yearTo-$yearFrom;
                for($i=$yearFrom;$i<=$yearTo;$i++){
                   $list[$i]['Year'] = "'".$i."'";
                   $list[$i]['number'] = 0;  
                }
                
                foreach ($graphDataAll['Store'][$store['Store']['id']] as $key => $data) 
                {
                    $result1[$key]=$data[0];
                    unset($data); 
                }
                $totalCoupon = array();
                if(!empty($result1)){
                    foreach($result1 as $amount){
                        $list[date('Y',strtotime($amount['order_date']))]['Year'] = "'".date('Y',strtotime($amount['order_date']))."'";
                       if(empty($list[date('Y',strtotime($amount['order_date']))]['number'])){
                           $list[date('Y',strtotime($amount['order_date']))]['number'] += 1;
                           $totalDineIn = $totalDineIn + 1;

                       } else {
                           $list[date('Y',strtotime($amount['order_date']))]['number'] += 1;
                           $totalDineIn = $totalDineIn + 1;
                       }
                    }
                }
                foreach($list as $lstKey => $lst){
                    if(!in_array($lst['Year'],$datee))
                    {
                        $datee[$lstKey] = $lst['Year'];
                        $tamntt[$lstKey] = $lst['number'];
                    } else {
                        $tamntt[$lstKey] += $lst['number'];
                    }
                }
            }
            $allTotalCoupon[] = $tamntt;
        }
        
        /* For Pie Chart*/
        $pieData = $this->Common->dineInPieDataArrange($dineInPieData);
        
        $totalPie = (isset($pieData['total']) ? $pieData['total'] : 0);
        $finalPie = (isset($pieData['data']) ? $pieData['data'] : array());
        /* End For Pie Chart*/
        
        if (!empty($tamntt) && !empty($datee)) 
        {
            $startmonth = date('m', strtotime($dateFrom));
            $endmonth   = date('m', strtotime($dateTo));

            $startyear  = date('Y', strtotime($dateFrom));
            $endyear    = date('Y', strtotime($dateTo));

            $titleText = $this->Common->dineInTitleString($startmonth, $startyear, $endmonth, $endyear, 'life_time');
            $text = '<style="font-size:14px;font-weight:bold;">' . $titleText . '</style>';
            //$text = '<style="font-size:12px;">Lifet Time Report for</style> <br/><style="font-size:14px;">HQ</style><br/><style="font-size:12px;"></style>';
            $subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalDineIn . ' Reservations</style>';
            $amntdate = implode(',',$datee);
            $taData = implode(',',$tamntt);
            ?>
            <div class="col-lg-<?php echo (($totalPie == 0) ? '12' : '6');?>">
                <div id="cTotal"></div>
            </div>
            <div class="col-lg-6  <?php echo (($totalPie == 0) ? 'hidden' : '');?>">
                <div id="reservationPie"></div>
            </div>
            <script>
                $(function () {
                    $('#cTotal').highcharts({
                        chart: {
                            type: 'line'
                        },
                        title: {
                            text: '<?php echo $text;?>'
                        },
                        subtitle: {
                            text: '<?php echo $subTitle;?>'
                        },
                        xAxis: {
                            categories: [<?php echo $amntdate;?>],
                            title: {
                                text: null
                            },
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: '# Of Reservations',
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
                            name: 'Reservations',
                            data: [<?php echo $taData; ?>],
                            color: '#f79d54'
                        }]
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
                            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b> ({point.pointcount})'
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
            <?php
        }
    }
    
    ?>    
    <div class="clear"></div>
    <?php
    
    /**************************** For Pagination Graph ****************************/
    
    foreach($pageMerchant[$graphPageNumber] as $keyStore => $valueStore)
    {
        $totalDineIn=0;
        $difference = $yearTo-$yearFrom;
        for($i=$yearFrom;$i<=$yearTo;$i++){
           $list[$i]['Year'] = "'".$i."'";
           $list[$i]['number'] = 0;  
        }
        $result1 = $tamntt = $datee = array();
        foreach ($graphData['Store'][$keyStore] as $key => $data) 
        {
            $result1[$key]=$data[0];
            unset($data); 
        }
        if(!empty($result1)){
            foreach($result1 as $amount){
                $list[date('Y',strtotime($amount['order_date']))]['Year'] = "'".date('Y',strtotime($amount['order_date']))."'";
               if(empty($list[date('Y',strtotime($amount['order_date']))]['number'])){
                   $list[date('Y',strtotime($amount['order_date']))]['number'] += 1;
                   $totalDineIn = $totalDineIn + 1;

               } else {
                   $list[date('Y',strtotime($amount['order_date']))]['number'] += 1;
                   $totalDineIn = $totalDineIn + 1;
               }
            }
        }
        foreach($list as $lst){
            $datee[] = $lst['Year']; 
            $tamntt[] = $lst['number']; 
        }
        
        
        $startmonth = date('m', strtotime($dateFrom));
        $endmonth   = date('m', strtotime($dateTo));

        $startyear  = date('Y', strtotime($dateFrom));
        $endyear    = date('Y', strtotime($dateTo));

        $titleText = $this->Common->dineInTitleString($startmonth, $startyear, $endmonth, $endyear, 'life_time');
        $text = '<style="font-size:14px;font-weight:bold;">' . $titleText . '</style>';
        //$text = '<style="font-size:12px;">Lifet Time Report for</style> <br/><style="font-size:14px;">' . addslashes($valueStore) . '</style><br/><style="font-size:12px;"></style>';
        $subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalDineIn . ' Reservations</style>';
        $tcoupon = implode(',', $tamntt);
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
                        text: '<?php echo $text;?>'
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
                            text: '# Of Reservations',
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
                        name: 'Reservations',
                        data: [<?php echo $tcoupon; ?>],
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
    <?php echo $this->element('hqsalesreports/dine_in/paginationall'); ?>
</div>