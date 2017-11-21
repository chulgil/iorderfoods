<?php
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
$amnt = 0;
$order = 0;
$totalOrder = 0;
$totalAmt = 0;
$torders = $tamnt = '';
if (!empty($graphData)) {
    //pr($graphData);
    foreach ($graphData as $data) {
        $datearray = explode(" ", $data['Order']['created']);
        foreach ($datee as $key => $date) {
            $date = str_replace("'", '', $date);
            if ($date == $datearray[0]) {
                if($data['Order']['coupon_discount'] > 0)
                {
                    $data['Order']['amount'] -= $data['Order']['coupon_discount'];
                }
                
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
$subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalOrder . ' Order And  $ ' . $totalAmt . ' Amount</style>';
$amntdate = implode(',', $datee);
$tamnt = implode(',', $tamntt);
$torders = implode(',', $totalorders);
$text = 'Daily Report for ' . $startDate . ' to ' . $endDate;
?>
<div id="sContainer"></div>
<script>
    $(function () {
        $('#sContainer').highcharts({
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


        
<div id="pagination_data_request">
    <?php echo $this->element('storeReports/dollar/pagination'); ?>
</div>