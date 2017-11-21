<?php
$order = 0;
$totalOrder = 0;
$totalAmt = 0;
$difference = $yearTo - $yearFrom;
for ($i = $yearFrom; $i <= $yearTo; $i++) {
    $list[$i]['Year'] = "'" . $i . "'";
    $list[$i]['Amount'] = 0;
    $torders[$i] = 0;
}
if (!empty($graphData)) {
    foreach ($graphData as $amount) {
        
        if($amount['Order']['coupon_discount'] > 0)
        {
            $amount['Order']['amount'] -= $amount['Order']['coupon_discount'];
        }
        
        $list[date('Y', strtotime($amount['Order']['created']))]['Year'] = "'" . date('Y', strtotime($amount['Order']['created'])) . "'";
        if (empty($list[date('Y', strtotime($amount['Order']['created']))]['Amount'])) {
            $list[date('Y', strtotime($amount['Order']['created']))]['Amount'] = $amount['Order']['amount'];
            $totalOrder = $totalOrder + 1;
            $totalAmt = $totalAmt + $amount['Order']['amount'];
        } else {
            $list[date('Y', strtotime($amount['Order']['created']))]['Amount'] += $amount['Order']['amount'];
            $totalOrder = $totalOrder + 1;
            $totalAmt = $totalAmt + $amount['Order']['amount'];
        }
        $torders[date('Y', strtotime($amount['Order']['created']))] = $torders[date('Y', strtotime($amount['Order']['created']))] + 1;
    }
}
foreach ($list as $lst) {
    $datee[] = $lst['Year'];
    $tamntt[] = $lst['Amount'];
}
$subTitle = '<style="font-size:14px;font-weight:bold;">Total ' . $totalOrder . ' Order And  $ ' . $totalAmt . ' Amount</style>';
$amntdate = implode(',', $datee);
$tamnt = implode(',', $tamntt);
$torders = implode(',', $torders);
$text = 'Yearly Report for ' . $yearFrom . '-' . $yearTo;
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
                    type: 'column',
                    name: 'Order',
                    yAxis: 1,
                    pointWidth  : 20,
                    color: '#f79d54',
                    data: [<?php echo $torders; ?>]

                },
                {
                    type: 'line',
                    name: 'Amount',
                    maxPointWidth: 50,
                    color: '#9ac456',
                    data: [<?php echo $tamnt; ?>]
                }],
            exporting: {enabled: false}
        });
    });
</script>

        
<div id="pagination_data_request">
    <?php echo $this->element('superNewReports/dollar/pagination'); ?>
</div>