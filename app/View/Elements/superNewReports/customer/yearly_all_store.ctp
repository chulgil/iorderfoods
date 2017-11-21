<?php
if (!empty($graphData['Store']) && !empty($stores)) {
    $alltotalusers = $datee = array();
    /**************************** For All Data in one Graph ****************************/
    $totalCustomer  = 0;
    if($graphPageNumber == 0)
    {
        foreach ($stores as $store)
        {
            if (isset($graphDataAll))
            {
                $difference     = $yearTo-$yearFrom;
                for($i=$yearFrom;$i<=$yearTo;$i++){
                   $list[$i]['Year']    = "'".$i."'";
                   $list[$i]['per_day'] = 0;  
                }
                
                foreach($graphDataAll['Store'][$store['Store']['id']] as $amount)
                {
                    $list[date('Y',strtotime($amount['User']['created']))]['Year'] = "'".date('Y',strtotime($amount['User']['created']))."'";
                    if(empty($list[date('Y',strtotime($amount['User']['created']))]['per_day']))
                    {
                        $list[date('Y',strtotime($amount['User']['created']))]['per_day'] = $amount['User']['per_day'];
                        $totalCustomer += $amount['User']['per_day'];
                    }
                    else
                    {
                        $list[date('Y',strtotime($amount['User']['created']))]['per_day'] += $amount['User']['per_day'];
                        $totalCustomer += $amount['User']['per_day'];
                    }
                }
                foreach($list as $lstKey => $lst){
                    if(!in_array($lst['Year'],$datee))
                    {
                        $datee[$lstKey] = $lst['Year'];
                        $tamntt[$lstKey] = $lst['per_day'];
                    } else {
                        $tamntt[$lstKey] += $lst['per_day'];
                    }
                }
            }
            $alltotalusers[] = $tamntt;
        }
        
        if (!empty($tamntt) && !empty($datee)) 
        {
            $amntdate = implode(',',$datee);
            $totalUser = array_sum($tamntt);
            $text = '<style="font-size:12px;">Yearly Report for</style> <br/><style="font-size:14px;">All Store</style><br/><style="font-size:12px;">' . $yearFrom . ' - ' . $yearTo . '</style>';
            $subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalCustomer . ' Customer</style>';
            $taData = implode(',', $tamntt);
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
                                text: 'Customer Count',
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
                            name: 'Customer',
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
        if (isset($graphData['Store'][$keyStore]))
        {
            $totalCustomer  = 0;
            $difference     = $yearTo-$yearFrom;
            for($i=$yearFrom;$i<=$yearTo;$i++){
               $list[$i]['Year']    = "'".$i."'";
               $list[$i]['per_day'] = 0;  
            }
            foreach($graphData['Store'][$keyStore] as $amount){
                $list[date('Y',strtotime($amount['User']['created']))]['Year'] = "'".date('Y',strtotime($amount['User']['created']))."'";
                if(empty($list[date('Y',strtotime($amount['User']['created']))]['per_day']))
                {
                    $list[date('Y',strtotime($amount['User']['created']))]['per_day'] = $amount['User']['per_day'];
                    $totalCustomer += $amount['User']['per_day'];
                }
                else
                {
                    $list[date('Y',strtotime($amount['User']['created']))]['per_day'] += $amount['User']['per_day'];
                    $totalCustomer += $amount['User']['per_day'];
                }
            }
            $datee = $tamntt = array();
            foreach($list as $lstKey => $lst){
                if(!in_array($lst['Year'],$datee))
                {
                    $datee[$lstKey] = $lst['Year'];
                    $tamntt[$lstKey] = $lst['per_day'];
                } else {
                    $tamntt[$lstKey] += $lst['per_day'];
                }
            }
        }
        
        $text = '<style="font-size:12px;">Yearly Report for</style> <br/><style="font-size:14px;">' . addslashes($valueStore) . '</style><br/><style="font-size:12px;">' . $yearFrom . ' - ' . $yearTo . '</style>';
        $subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalCustomer . ' Customer</style>';
        
        $amntdate = implode(',',$datee);
        $tamnt = implode(',',$tamntt);
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
                        categories: [<?php echo $amntdate;?>],
                        title: {
                            text: null
                        },
                        crosshair: true
                    },
                   yAxis: {
                        min: 0,
                        title: {
                            text: 'Customer Count',
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
                        name: 'Customer',
                        data: [<?php echo $tamnt; ?>],
                        color: '#f79d54'

                    }]
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