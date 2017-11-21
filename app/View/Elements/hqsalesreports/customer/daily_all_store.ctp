<?php
if (!empty($graphData['Store']) && !empty($stores)) {
    $alltotalusers = array();
    /**************************** For All Data in one Graph ****************************/
    
    if($graphPageNumber == 0)
    {
        $classGraph = '';
        if(isset($customerType) && $customerType == 5)
        {
            $classGraph = 'col-lg-12';
            if (isset($graphDataAll))
            {
                $summarytotalUsers=0;
                $totalusers=array();
                $step = '+1 day';
                $output_format = 'Y-m-d';
                $datee = array();
                $current = strtotime($startdate);
                $last = strtotime($enddate);    
                while( $current <= $last ) {    
                    $datee[] = "'".date($output_format, $current)."'";
                    $current = strtotime($step, $current);
                    $totalusers[]=0;
                }        
                $user=0;
                $tamntt=0;
                $totalCustomer=0;
                foreach($graphDataAll['Store'][0] as $user){
                    $datearray = explode(" ",$user['User']['created']);
                   // echo '<pre>';print_r($datearray);die;
                    foreach($datee as $key => $date){                
                        $date=str_replace("'",'',$date);
                        if($date==$datearray[0]){
                            if($tamntt[$key]){
                                $user += $user['User']['per_day'];
                                 $totalCustomer = $totalCustomer + $user['User']['per_day'];
                               // $user +=1;
                            }else{
                                $user =$user['User']['per_day'];
                                $totalCustomer = $totalCustomer + $user;
                               // $user=1;
                            }
                            $totalusers[$key]=$user;                    
                        }
                    }             
                }
                $alltotalusers[] = $totalusers;
            }
        }
        else 
        {
            $classGraph = 'col-lg-12';
            foreach ($stores as $store)
            {
                if (isset($graphDataAll))
                {
                    $summarytotalUsers=0;
                    $totalusers=array();
                    $step = '+1 day';
                    $output_format = 'Y-m-d';
                    $datee = array();
                    $current = strtotime($startdate);
                    $last = strtotime($enddate);    
                    while( $current <= $last ) {    
                        $datee[] = "'".date($output_format, $current)."'";
                        $current = strtotime($step, $current);
                        $totalusers[]=0;
                    }        
                    $user=0;
                    $tamntt=0;
                    $totalCustomer=0;

                    foreach($graphDataAll['Store'][$store['Store']['id']] as $user){
                        $datearray = explode(" ",$user['User']['created']);
                        foreach($datee as $key => $date){                
                            $date=str_replace("'",'',$date);
                            if($date==$datearray[0]){
                                if($tamntt[$key]){
                                    $user += $user['User']['per_day'];
                                     $totalCustomer = $totalCustomer + $user['User']['per_day'];
                                   // $user +=1;
                                }else{
                                    $user =$user['User']['per_day'];
                                    $totalCustomer = $totalCustomer + $user;
                                   // $user=1;
                                }
                                $totalusers[$key]=$user;                    
                            }
                        }             
                    }
                }
                $alltotalusers[] = $totalusers;
            }
        }
        

        $aData = array();
        if (!empty($alltotalusers)) {
            foreach ($alltotalusers as $akey => $amt) {
                foreach ($amt as $key => $amtvalue) {
                    if (!empty($aData[$key])) {
                        $aData[$key] = $aData[$key] + $amtvalue;
                    } else {
                        $aData[$key] = $amtvalue;
                    }
                }
            }
        }
        
        if (!empty($aData) && !empty($datee)) 
        {
            $userdate = implode(',',$datee);
            $totalUser = array_sum($aData);
            if(isset($customerType) && $customerType == 5)
            {
                $text = '<style="font-size:12px;">Daily Report for</style> <br/><style="font-size:14px;">Merchant</style><br/><style="font-size:12px;">' . $startdate . ' to ' . $enddate . '</style>';
            }
            else 
            {
                $text = '<style="font-size:12px;">Daily Report for</style> <br/><style="font-size:14px;">HQ</style><br/><style="font-size:12px;">' . $startdate . ' to ' . $enddate . '</style>';
            }
            $subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalUser . ' Customer</style>';
            $taData = implode(',', $aData);
            ?>
            <div class="<?php echo $classGraph;?>">
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
                            categories: [<?php echo $userdate;?>],
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
    
    ?>    
    <div class="clear"></div>
    <?php
    
    /**************************** For Pagination Graph ****************************/
    
    if(!isset($customerType))
    {
        foreach($pageMerchant[$graphPageNumber] as $keyStore => $valueStore)
        {
            $summarytotalUsers=0;
            $totalusers=array();
            $step = '+1 day';
            $output_format = 'Y-m-d';
            $datee = array();
            $current = strtotime($startdate);
            $last = strtotime($enddate);    
            while( $current <= $last ) {    
                $datee[] = "'".date($output_format, $current)."'";
                $current = strtotime($step, $current);
                $totalusers[]=0;
            }        
            $user=0;
            $tamntt=0;
            $totalCustomer=0;
            foreach($graphData['Store'][$keyStore] as $user){
                $datearray = explode(" ",$user['User']['created']);
               // echo '<pre>';print_r($datearray);die;
                foreach($datee as $key => $date){                
                    $date=str_replace("'",'',$date);
                    if($date==$datearray[0]){
                        if($tamntt[$key]){
                            $user += $user['User']['per_day'];
                             $totalCustomer = $totalCustomer + $user['User']['per_day'];
                           // $user +=1;
                        }else{
                            $user =$user['User']['per_day'];
                            $totalCustomer = $totalCustomer + $user;
                           // $user=1;
                        }
                        $totalusers[$key]=$user;                    
                    }
                }             
            }
            $userdate = implode(',',$datee);
            $totalUser = array_sum($totalusers);
            $text = '<style="font-size:12px;">Daily Report for</style> <br/><style="font-size:14px;">' . addslashes($valueStore) . '</style><br/><style="font-size:12px;">' . $startdate . ' to ' . $enddate . '</style>';
            $subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalUser . ' Customer</style>';
            $taData = implode(',', $totalusers);
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
                            categories: [<?php echo $userdate;?>],
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
            //}
        }
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
    <?php echo $this->element('hqsalesreports/customer/paginationall'); ?>
</div>