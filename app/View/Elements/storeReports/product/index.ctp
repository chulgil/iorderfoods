<?php
if (isset($graphData))
{
    $datee = $tamntt = $torders = array();
    $totalorders=array();
    $datee = array();
    $text = '';
    $amnt=0;
    $order=0;
    $totalItem=0;
    $itemNames = array();
    $itemTotalAmount = array();
    $totalProducts = array();

    foreach ($graphData as $product) {
        if(!empty($product)){
            $forCurrent = '';
            if(isset($product['Item']['name']) && isset($product[0]['number'])){
                $product['Item']['name'] = addslashes($product['Item']['name']);                          
                $itemNames[] = "'" . $product['Item']['name'] . "'";
                $itemTotalAmount[$product['Item']['name']] = $product[0]['total_amount'];
                $totalItem = $totalItem + $product[0]['number'];
            }
        }
    }
    $itemname              = implode(',',$itemNames);
    
    if (!empty($itemTotalAmount) && !empty($itemname)) 
    {
        $title = '';
        if($productCount != 'All')
        {
            $title = 'Top ' . $productCount . ' Product Sold';
        } else {
            $title = $productCount . ' Product Sold';
        }
        
        $dateString = '';
        if(!isset($startDate) && !isset($endDate)) {
            if(isset($fromYear) && isset($fromMonth) && isset($toMonth) && isset($toYear))
            {
                $dateObjFrom    = DateTime::createFromFormat('!m', $fromMonth);
                $monthNameFrom  = $dateObjFrom->format('F');
                
                $dateObjTo      = DateTime::createFromFormat('!m', $toMonth);
                $monthNameTo    = $dateObjTo->format('F');
                
                $dateString = $monthNameFrom . ' ' . $fromYear . ' to ' . $monthNameTo . ' ' . $toYear;
            }
            else if(isset($fromYear) && isset($fromMonth))
            {
                $dateObjFrom    = DateTime::createFromFormat('!m', $fromMonth);
                $monthNameFrom  = $dateObjFrom->format('F');
                $dateString = $monthNameFrom . ' ' . $fromYear;
            }
            else {
                $dateString = $fromYear . ' to ' . $toYear;
            }
        } else {
            $dateString = $startDate . ' to ' . $endDate;
        }
        $text = '<style="font-size:12px;"><strong>' . $title . '</strong></style> <br/><style="font-size:12px;">' . $dateString . '</style>';
        $subTitle = '<style="font-size:14px;font-weight:bold;"></style>';
        
        
        $colorArray = array('#FFE800', '#149FD1', '#AF5E9C', '#00B16A', '#E6567A', '#00529C', '#F7941E', '#14A2D4', '#AF5E9C');
        ?>
        <div class="col-lg-12">
            <div id="cTotal"></div>
        </div>
        <script>
            $(function () {
            $('#cTotal').highcharts({
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: '<?php echo  $text;?>'
                    },
                    subtitle: {
                        text: '<?php echo $subTitle;?>'
                    },
                    xAxis: {
                        categories: [<?php echo $itemname;?>],
                        crosshair: true,
                        labels: {
                            autoRotation: false,
                            style: {
                                font: '12px',
                                textOverflow: 'none'
                            },
                            formatter: function () {
                                return this.value;
                            }
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Revenue',
                            align: 'middle'
                        },
                        labels: {
                            overflow: ''
                        },
                        opposite: true
                    },
                    tooltip: {
                        valueSuffix: '',
                        useHTML: true,
                        formatter: function() {
                            var html = '';
                            var currentX = this.x;
                            <?php
                            $seriesData = '';
                            foreach($itemTotalAmount as $itemName => $itemTotal)
                            {
                                $color_key = array_rand($colorArray);
                                $seriesData .= "{y:" . $itemTotal . ",color: '" . $colorArray[$color_key] . "'},";
                                ?>
                                var itemPriceDollar = '<?php echo $this->Common->amount_format($itemTotal);?>';
                                var itemName = '<?php echo $itemName;?>';
                                var itemColor = '<?php echo $colorArray[$color_key];?>'
                                if(currentX == itemName)
                                {
                                    html = itemName + '<br/><ul style="margin: 0; padding: 0;"><li style="list-style-type: circle;">Revenue : <b>' + itemPriceDollar + '</b></li></ul>';
                                }
                                <?php
                            }
                            $seriesData = trim($seriesData, ',');
                            ?>
                            return html;
                        }
                    },
                    plotOptions: {
                        line: {
                            dataLabels: {
                                enabled: true
                            },
                            enableMouseTracking: true
                        }
                    },
                    exporting: { enabled: false },
                    series: [{
                        name        : 'Revenue',
                        data        : [<?php echo $seriesData;//$itemtotalamount; ?>],
                        color       : '#f79d50'

                    }]
                });
        });
        </script>
        <?php
    }
}

?>    
<div class="clear"></div>
<script>
$('.date-select').datepicker({
    dateFormat: 'yy-mm-dd',
});
</script>
<div id="pagination_data_request">
    <?php echo $this->element('storeReports/product/pagination');?>
</div>