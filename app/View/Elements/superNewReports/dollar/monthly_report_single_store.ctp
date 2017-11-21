<?php
$totalOrder = 0;
$totalAmt = 0;
$datee = array('1' => "'Jan'", '2' => "'Feb'", '3' => "'Mar'", '4' => "'Apr'", '5' => "'May'", '6' => "'Jun'", '7' => "'Jul'", '8' => "'Aug'", '9' => "'Sep'", '10' => "'Oct'", '11' => "'Nov'", '12' => "'Dec'");
$tamntt = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0, '12' => 0);
$torders = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0, '12' => 0);
$order = 0;
foreach ($graphData as $amount) {
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
$amntdate = implode(',', $datee);
$tamnt = implode(',', $tamntt);
$torders = implode(',', $torders);
$text = 'Monthly Report for ' . str_replace('\'', '', $datee[$month]) . ' - ' . $year;
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


                }
            ],
            exporting: {enabled: false}
        });
    });
</script>

<div id="pagination_data_request">
    <?php echo $this->element('superNewReports/dollar/pagination'); ?>
</div>