<style>
    .btn.active { background-color: gray;color: #ffff;}
</style>
<?php echo $this->element('chart/chart_script'); ?>
<?php
$reportName = array(1 => 'Order Type', 2 => 'Product', 3 => 'Customer', 4 => 'Coupon', 5 => 'Promo', 6 => 'Extended Offers', 7 => 'Dine In');

$dataRequest = $this->Session->read('storeReportRequest');
$type               = (isset($dataRequest['type']) ? $dataRequest['type'] : 1);
$orderType          = (isset($dataRequest['orderType']) ? $dataRequest['orderType'] : 1);
$reportType         = (isset($dataRequest['reportType']) ? $dataRequest['reportType'] : 1);
$merchantOption     = (isset($dataRequest['merchantOption']) ? $dataRequest['merchantOption'] : 0);
$startDate          = (isset($dataRequest['startDate']) ? $dataRequest['startDate'] : null);
$endDate            = (isset($dataRequest['endDate']) ? $dataRequest['endDate'] : null);
$date_start_from    = (isset($dataRequest['date_start_from']) ? $dataRequest['date_start_from'] : null);
$date_end_from      = (isset($dataRequest['date_end_from']) ? $dataRequest['date_end_from'] : null);
$defaultFromMonth   = (isset($dataRequest['fromMonth']) ? $dataRequest['fromMonth'] : date('m'));
$defaultFromYear    = (isset($dataRequest['fromYear']) ? $dataRequest['fromYear'] : date('Y'));
$defaultToMonth     = (isset($dataRequest['toMonth']) ? $dataRequest['toMonth'] : date('m'));
$defaultToYear      = (isset($dataRequest['toYear']) ? $dataRequest['toYear'] : date('Y'));
$fromYear           = (isset($dataRequest['fromYear']) ? $dataRequest['fromYear'] : date('Y'));
$toYear             = (isset($dataRequest['toYear']) ? $dataRequest['toYear'] : date('Y'));
$coupon_code        = (isset($dataRequest['coupon_code']) ? $dataRequest['coupon_code'] : null);
$promo_id           = (isset($dataRequest['promo_id']) ? $dataRequest['promo_id'] : null);
$extended_offer_id  = (isset($dataRequest['extended_offer_id']) ? $dataRequest['extended_offer_id'] : null);
$product_count      = (isset($dataRequest['product_count']) ? $dataRequest['product_count'] : 5);
if(!empty($dataRequest)){
    $startDate          = date('Y-m-d', strtotime($startDate));
    $endDate            = date("Y-m-d", strtotime($endDate));
    $start_date_from    = date('Y-m-d', strtotime($date_start_from));
    $end_date_from      = date('Y-m-d', strtotime($date_end_from));
} else {
    $startDate          = date('Y-m-d', strtotime('-6 day'));
    $endDate            = date("Y-m-d");
    $start_date_from    = date('Y-m-d', strtotime("first day of this month"));
    $end_date_from      = date('Y-m-d', strtotime("today"));
}

if($reportType == 2)
{
    $coupon_div         = 'hidden';
    $promo_div          = 'hidden';
    $extended_offer_div = 'hidden';
    $product_div        = '';
}
else if($reportType == 4)
{
    $coupon_div         = '';
    $promo_div          = 'hidden';
    $extended_offer_div = 'hidden';
    $product_div        = 'hidden';
} else if($reportType == 5) {
    $coupon_div         = 'hidden';
    $promo_div          = '';
    $extended_offer_div = 'hidden';
    $product_div        = 'hidden';
} else if($reportType == 6) {
    $coupon_div         = 'hidden';
    $promo_div          = 'hidden';
    $extended_offer_div = '';
    $product_div        = 'hidden';
} else {
    $coupon_div         = 'hidden';
    $promo_div          = 'hidden';
    $extended_offer_div = 'hidden';
    $product_div        = 'hidden';
}

if(isset($storeId))
{
    $getCouponList     = $this->Common->couponList($storeId);
    $getPromoList      = $this->Common->promoList($storeId);
    $getExtendedList   = $this->Common->extendedOfferList($storeId);
    
    if(!empty($getCouponList))
    {
        $couponList = array('' => 'Select Coupon');
        foreach ($getCouponList as $kCouponList => $vCouponList){
            $couponList[$vCouponList['Order']['coupon']] = $vCouponList['Order']['coupon'];
        }
    }
    
    if(!empty($getPromoList))
    {
        $promoList = array('' => 'Select Offer');
        foreach ($getPromoList as $kPromoList => $vPromoList){
            $promoList[$vPromoList['Offer']['id']] = $vPromoList['Offer']['description'];
        }
    }
    
    if(!empty($getExtendedList))
    {
        $extendedList = array('' => 'Select Extended Offer');
        foreach ($getExtendedList as $kExtendedList => $vExtendedList){
            $extendedList[$vExtendedList['Item']['id']] = $vExtendedList['Item']['name'];
        }
    }
} else {
    $couponList     = array();
    $promoList      = array();
    $extendedList   = array();
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="col-lg-9">
            <h3><?php echo (($reportType != 0) ? $reportName[$reportType] . ' Reports' : 'Order Type Reports');?></h3> 
        </div>
    </div>
</div>
<div class="col-lg-12">&nbsp;</div>
<div class="col-lg-12" id="error-message"><?php echo $this->Session->flash(); ?></div>
<script type="text/javascript">
    $(function(){
       setTimeout(function(){
           $("#error-message").hide();
       }, 5000);
    });
</script>
<div class="col-lg-12"><hr></div>
<div class="row">
    <div class="col-lg-12">
        <div class="col-lg-7 for-single-report">
            <ul class="nav nav-pills report-type">
                <li class="<?php echo ($reportType == 1 ? 'active' : '');?>" data-report-id="1" data-report-name="Order Type Reports"><?php echo $this->Html->link('Order Type', 'javascript:void(0)', array()); ?></li>
                <li class="<?php echo ($reportType == 2 ? 'active' : '');?>" data-report-id="2" data-report-name="Product Reports"><?php echo $this->Html->link('Product', 'javascript:void(0)', array()); ?></li>
                <li class="<?php echo ($reportType == 3 ? 'active' : '');?>" data-report-id="3" data-report-name="Customer Reports"><?php echo $this->Html->link('Customer', 'javascript:void(0)', array()); ?></li>
                <li class="<?php echo ($reportType == 4 ? 'active' : '');?>" data-report-id="4" data-report-name="Coupon Reports"><?php echo $this->Html->link('Coupon', 'javascript:void(0)', array()); ?></li>
                <li class="<?php echo ($reportType == 5 ? 'active' : '');?>" data-report-id="5" data-report-name="Promo Reports"><?php echo $this->Html->link('Promo', 'javascript:void(0)', array()); ?></li>
                <li class="<?php echo ($reportType == 6 ? 'active' : '');?>" data-report-id="6" data-report-name="Extended Offers Reports"><?php echo $this->Html->link('Extended Offers', 'javascript:void(0)', array()); ?></li>
                <li class="<?php echo ($reportType == 7 ? 'active' : '');?>" data-report-id="7" data-report-name="Dine In Reports"><?php echo $this->Html->link('Dine In', 'javascript:void(0)', array()); ?></li>
            </ul>
        </div>
        <div class="col-lg-5" style="padding-left:0;">
            <div class="col-lg-8">
                <div class="btn-group type">
                    <button class="btn <?php echo ((($type == 1) && ($merchantOption < 1)) ? 'active' : '');?>" data-type-id="1">Day</button>
                    <button class="btn <?php echo ((($type == 2) && ($merchantOption < 1)) ? 'active' : '');?>" data-type-id="2">Week</button>
                    <button class="btn <?php echo ((($type == 3) && ($merchantOption < 1)) ? 'active' : '');?>" data-type-id="3">Month</button>
                    <button class="btn <?php echo ((($type == 4) && ($merchantOption < 1)) ? 'active' : '');?>" data-type-id="4">Year</button>
                </div>
            </div>
            <div class="col-lg-4  for-single-report" style="padding: 0;">
                <?php
                $options = array('0' => 'Custom', '1' => 'Today', '2' => 'Yesterday', '3' => 'This week(Sun-Today)', '4' => 'This week(Mon-Today)', '5' => 'Last 7 days', '6' => 'Last week(Sun-Sat)', '7' => 'Last week(Mon-Sun)', '8' => 'Last business week(Mon-Fri)', '9' => 'Last 14 days', '10' => 'This month', '11' => 'Last 30 days', '12' => 'Last month', '13' => 'All time');
                echo $this->Form->input('Merchant.option', array('options' => $options, 'class' => 'form-control', 'label' => false, 'div' => false, 'value' => $merchantOption));
                ?>
            </div>
        </div>
    </div>
    <div class="col-lg-12">&nbsp</div>
    <div class="col-lg-12">
<!--        <div class="col-lg-3  hidden for-single-report">  
            <?php echo $this->Form->input('Item.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $categoryList, 'empty' => 'Item name')); ?>
        </div>-->
        <div class="col-lg-3">
            <div class="btn-group order-type <?php echo (($reportType == 3 || $reportType == 7) ? 'hidden' : '');?>">
                <button class="btn <?php echo ((($orderType == 1)) ? 'active' : '');?>" data-type-id="1">Both</button>
                <button class="btn <?php echo ((($orderType == 2)) ? 'active' : '');?>" data-type-id="2">Pick Up</button>
                <button class="btn <?php echo ((($orderType == 3)) ? 'active' : '');?>" data-type-id="3">Delivery</button>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="col-lg-6 daily-div" style="<?php echo ((($type == 1) && ($merchantOption < 1)) ? 'display:block' : 'display:none');?>">
                <div class="col-lg-6">
                    <?php echo $this->Form->input('startdate', array('label' => false, 'div' => false, 'class' => 'form-control date-select', 'placeholder' => 'Start Date', 'readonly' => true, 'value' => $startDate)); ?>
                </div>
                <div class="col-lg-6">
                    <?php echo $this->Form->input('enddate', array('label' => false, 'div' => false, 'class' => 'form-control date-select', 'placeholder' => 'End Date', 'readonly' => true, 'value' => $endDate)); ?>
                </div>
            </div>
            <div class="col-lg-8 weekmonthyear-div" style="<?php echo ((($type == 2 || $type == 3 || $type == 4) && ($merchantOption < 1)) ? 'display:block' : 'display:none');?>">
                <?php
                $month = array('1' => 'Jan', '2' => 'Feb', '3' => 'Mar', '4' => 'Apr', '5' => 'May', '6' => 'Jun', '7' => 'Jul', '8' => 'Aug', '9' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec');
                for ($y = 2010; $y <= date('Y'); $y++) {
                    $yr[$y] = $y;
                }
                for ($m = 1; $m <= 12; $m++) {
                    $mth[$m] = $month[$m];
                }
                ?>
                <div class="col-lg-2 no-padding">
                    <label>From</label>
                </div>
                <div class="col-lg-4 no-padding">
                    <?php echo $this->Form->input('from_month', array('type' => 'select', 'class' => 'form-control form-control-small', 'label' => false, 'div' => false, 'options' => $mth, 'default' => $defaultFromMonth)); ?> 
                    <?php echo $this->Form->input('from_year', array('type' => 'select', 'class' => 'form-control form-control-small', 'label' => false, 'div' => false, 'options' => $yr, 'placeholder' => 'End Select Year', 'default' => $defaultFromYear)); ?>	
                </div>
                <div class="col-lg-1 no-padding">
                    <label>to</label>
                </div>
                <div class="col-lg-4 no-padding">
                    <?php echo $this->Form->input('to_month', array('type' => 'select', 'class' => 'form-control form-control-small', 'label' => false, 'div' => false, 'options' => $mth, 'default' => $defaultToMonth)); ?>
                    <?php echo $this->Form->input('to_year', array('type' => 'select', 'class' => 'form-control form-control-small', 'label' => false, 'div' => false, 'options' => $yr, 'placeholder' => 'End Select Year', 'default' => $defaultToYear)); ?>		
                </div>
            </div>
<!--            <div class="col-lg-6 weekly-div" style="<?php echo ((($type == 2) && ($merchantOption < 1)) ? 'display:block' : 'display:none');?>">
                <div class="col-lg-6">
                    <?php echo $this->Form->input('date_start_from', array('label' => false, 'div' => false, 'class' => 'form-control week-picker', 'placeholder' => 'Start Week', 'readonly' => true, 'value' => $start_date_from)); ?>
                </div>
                <div class="col-lg-6">
                    <?php echo $this->Form->input('date_end_from', array('label' => false, 'div' => false, 'class' => 'form-control week-picker', 'placeholder' => 'End Week', 'readonly' => true, 'value' => $end_date_from)); ?>
                </div>
            </div>
            <div class="col-lg-6 monthly-div" style="<?php echo ((($type == 3) && ($merchantOption < 1)) ? 'display:block' : 'display:none');?>">
                <?php
                $month = array('1' => 'Jan', '2' => 'Feb', '3' => 'Mar', '4' => 'Apr', '5' => 'May', '6' => 'Jun', '7' => 'Jul', '8' => 'Aug', '9' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec');
                for ($y = 2010; $y <= date('Y'); $y++) {
                    $yr[$y] = $y;
                }
                for ($m = 1; $m <= 12; $m++) {
                    $mth[$m] = $month[$m];
                }
                ?>
                <div class="col-lg-6">
                    <?php echo $this->Form->input('month', array('type' => 'select', 'class' => 'form-control', 'label' => false, 'div' => false, 'options' => $mth, 'default' => $defaultMonth)); ?>	
                </div>
                <div class="col-lg-6">
                    <?php echo $this->Form->input('year', array('type' => 'select', 'class' => 'form-control', 'label' => false, 'div' => false, 'options' => $yr, 'placeholder' => 'End Select Year', 'default' => $defaultYear)); ?>		
                </div>
            </div>
            <div class="col-lg-6 yearly-div" style="<?php echo ((($type == 4) && ($merchantOption < 1)) ? 'display:block' : 'display:none');?>">
                <?php
                for ($y = 2010; $y <= date('Y'); $y++) {
                    $yr[$y] = $y;
                }
                ?>
                <div class="col-lg-6">
                    <?php echo $this->Form->input('from_year', array('type' => 'select', 'class' => 'form-control', 'label' => false, 'div' => false, 'options' => $yr, 'default' => $fromYear)); ?>	
                </div>
                <div class="col-lg-6">
                    <?php echo $this->Form->input('to_year', array('type' => 'select', 'class' => 'form-control', 'label' => false, 'div' => false, 'options' => $yr, 'default' => $toYear)); ?>		
                </div>
            </div>-->
            
            <div class="col-lg-4 coupon-div <?php echo $coupon_div;?>">
                <?php echo $this->Form->input('coupon_code', array('type' => 'select', 'class' => 'form-control', 'label' => false, 'div' => false, 'options' => (isset($couponList) ? $couponList : null), 'default' => $coupon_code)); ?>            
            </div>
            <div class="col-lg-4 promo-div <?php echo $promo_div;?>">
                <?php echo $this->Form->input('promo_id', array('type' => 'select', 'class' => 'form-control', 'label' => false, 'div' => false, 'options' => (isset($promoList) ? $promoList : null), 'default' => $promo_id)); ?>            
            </div>
            <div class="col-lg-4 extended-offer-div <?php echo $extended_offer_div;?>">
                <?php echo $this->Form->input('extended_offer_id', array('type' => 'select', 'class' => 'form-control', 'label' => false, 'div' => false, 'options' => (isset($extendedList) ? $extendedList : null), 'default' => $extended_offer_id)); ?>            
            </div>
            <div class="col-lg-4 product-div <?php echo $product_div;?>">
                <?php echo $this->Form->input('product_count', array('type' => 'select', 'class' => 'form-control', 'label' => false, 'div' => false, 'options' => array(5 => 'Top 5 Products Sold', 10 => 'Top 10 Products Sold', 25 => 'Top 25 Products Sold', 'All' => 'All Products Sold'), 'default' => $product_count)); ?>            
            </div>
        </div>
        <div class="col-lg-2">
            <?php echo $this->Html->link('Download Excel', array('controller' => 'storeReports', 'action' => 'reportDownload'), array('class' => 'btn btn-default')); ?>
        </div>
    </div>
    <div class="col-lg-12">&nbsp</div>
    <div class="col-lg-12">
        
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div id="showchart"></div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var now = new Date();
        $('#startdate').datepicker({
            dateFormat: 'yy-mm-dd',
            maxDate: 0,
            onSelect: function (selectedDate) {
                var dt = new Date(selectedDate);
                dt.setDate(dt.getDate() + 1);
                $("#enddate").datepicker("option", "minDate", dt);
                fetchGraphData();
            }

        });
        $('#enddate').datepicker({
            dateFormat: 'yy-mm-dd',
            maxDate: 0,
            onSelect: function (selectedDate) {
                var dt = new Date(selectedDate);
                dt.setDate(dt.getDate() - 1);
                $("#startdate").datepicker("option", "maxDate", dt);
                fetchGraphData();
            }

        });
        $(".type > .btn").click(function () {
            $(this).addClass("active").siblings().removeClass("active");
            changeDateDiv($(this).data('type-id'));
            $("#MerchantOption").val(0);
            $("#MerchantOption option").removeAttr('selected');
            fetchGraphData();
        });
        //changeDateDiv($(".type > .btn.active").data('type-id'));
        
        $("#SegmentId,#from_month,#from_year,#to_month,#to_year").change(function () {
            fetchGraphData();
        });
        $(".order-type > .btn").click(function () {
            $(this).addClass("active").siblings().removeClass("active");
            fetchGraphData();
        });
        
        $("#MerchantOption").change(function () {
            $(".order-type > .btn , .type > .btn").removeClass("active");
            changeDateDiv();
            fetchGraphData();
        });
    });
    function changeDateDiv(type) {
        
        if (type == 1) {
            $('.daily-div').css('display', 'block');
            $('.weekmonthyear-div').css('display', 'none');
        } else if (type == 2 || type == 3 || type == 4) {
            $('.weekmonthyear-div').css('display', 'block');
            $('.daily-div').css('display', 'none');
        } else{
            $('.daily-div').css('display', 'none');
            $('.weekmonthyear-div').css('display', 'none');
        }
    }
    function fetchGraphData(condition = null) {
        $('#loading').removeClass('hidden').css('display','block');
        //console.log();
        var reportType          = $(".nav-pills > li.active").data('report-id');
        var pageNo              = $("#pageNo").val();
        var type                = $(".type > .btn.active").data('type-id');
        var orderType           = $(".order-type > .btn.active").data('type-id');
        var startDate           = $('#startdate').val();
        var endDate             = $('#enddate').val();
        var merchantOption      = $('#MerchantOption').val();
        var itemId              = $('#ItemId').val();
        var fromMonth           = $('#from_month').val();
        var fromYear            = $('#from_year').val();
        var toMonth             = $('#to_month').val();
        var toYear              = $('#to_year').val();
        var coupon_code         = $('#coupon_code').val();
        var promo_id            = $('#promo_id').val();
        var extended_offer_id   = $('#extended_offer_id').val();
        var product_count       = $('#product_count').val();
        if(condition == 'first' && typeof type ===  "undefined" && merchantOption == 0)
        {
            $(".type > .btn:first").addClass('active');
            $(".daily-div").css('display','block');
            
        }
        
        $.ajax({
            type: 'post',
            url: '/storeReports/fetchReport',
            data: {reportType: reportType, type: type, orderType: orderType, startDate: startDate, endDate: endDate, fromMonth: fromMonth, fromYear: fromYear, toMonth: toMonth, toYear: toYear, pageNo: pageNo, merchantOption: merchantOption, itemId: itemId, coupon_code: coupon_code, promo_id: promo_id, extended_offer_id: extended_offer_id, product_count: product_count},
            success: function (result) {
                $("#showchart").html(result);
                $('#loading').addClass('hidden').css('display','none');
            }
        })
        .fail(function(xhr, err) {
            var responseTitle= $(xhr.responseText).filter('title').get(0);
            console.log($(responseTitle).text() + "\n" + xhr + "\n" + err);
            $('#loading').addClass('hidden').css('display','none');
        });
    }
    
    function fetchPaginationData(page = null, sort = null, sort_direction = null) {
        $('#loading').removeClass('hidden').css('display','block');
        var reportType          = $(".nav-pills > li.active").data('report-id');
        var orderType           = $(".order-type > .btn.active").data('type-id');
        var type                = $(".type > .btn.active").data('type-id');
        var merchantOption      = $('#MerchantOption').val();
        var itemId              = $('#ItemId').val();
        var startDate           = $('#startdate').val();
        var endDate             = $('#enddate').val();
        var fromMonth           = $('#from_month').val();
        var fromYear            = $('#from_year').val();
        var toMonth             = $('#to_month').val();
        var toYear              = $('#to_year').val();
        var coupon_code         = $('#coupon_code').val();
        var promo_id            = $('#promo_id').val();
        var extended_offer_id   = $('#extended_offer_id').val();
        var product_count       = $('#product_count').val();
        
        $.ajax({
            type: 'post',
            url: '/storeReports/getPaginationData',
            data: {reportType: reportType, orderType: orderType, type: type, merchantOption: merchantOption, itemId: itemId, startDate: startDate, endDate: endDate, fromMonth: fromMonth, fromYear: fromYear, toMonth: toMonth, toYear: toYear, coupon_code: coupon_code, promo_id: promo_id, extended_offer_id: extended_offer_id, product_count: product_count, page: page, sort: sort, sort_direction: sort_direction},
            success: function (result) {
                $("#pagination_data_request").html(result);
                $('#loading').addClass('hidden').css('display','none');

            }
        })
        .fail(function(xhr, err) {
            var responseTitle= $(xhr.responseText).filter('title').get(0);
            console.log($(responseTitle).text() + "\n" + xhr + "\n" + err);
            $('#loading').addClass('hidden').css('display','none');
        });
    }
    
    function fetchGraphPaginationData(graph_page_number = null) {
        $('#loading').removeClass('hidden').css('display','block');
        var reportType          = $(".nav-pills > li.active").data('report-id');
        var orderType           = $(".order-type > .btn.active").data('type-id');
        var type                = $(".type > .btn.active").data('type-id');
        var merchantOption      = $('#MerchantOption').val();
        var itemId              = $('#ItemId').val();
        var startDate           = $('#startdate').val();
        var endDate             = $('#enddate').val();
        var month               = $('#month').val();
        var year                = $('#year').val();
        var fromYear            = $('#from_year').val();
        var toYear              = $('#to_year').val();
        var date_start_from     = $('#date_start_from').val();
        var date_end_from       = $('#date_end_from').val();
        
        $.ajax({
            type: 'post',
            url: '/storeReports/getGraphPaginationData',
            data: {reportType: reportType, orderType: orderType, type: type, merchantOption: merchantOption, itemId: itemId, startDate: startDate, endDate: endDate, month: month, year: year, fromYear: fromYear, toYear: toYear, date_start_from: date_start_from, date_end_from: date_end_from, graph_page_number: graph_page_number},
            success: function (result) {
                $("#showchart").html(result);
                $('#loading').addClass('hidden').css('display','none');
            }
        })
        .fail(function(xhr, err) {
            var responseTitle= $(xhr.responseText).filter('title').get(0);
            console.log($(responseTitle).text() + "\n" + xhr + "\n" + err);
            $('#loading').addClass('hidden').css('display','none');
        });
    }
    
    $("#date_start_from").datepicker({
        dateFormat: 'yy-mm-dd',
        showOtherMonths: true,
        selectOtherMonths: true,
        showWeek: true,
        beforeShowDay: enableSUNDAYS,
        onSelect: function (selectedDate) {
            var dateText = $.datepicker.formatDate("yy-mm-dd", $(this).datepicker("getDate"));
            $('#date_start_from').text(dateText);
            
            var dt = new Date(selectedDate);
            dt.setDate(dt.getDate() + 6);
            $("#date_end_from").datepicker("option", "minDate", dt);
            fetchGraphData();
        }
    });

    $("#date_end_from").datepicker({
        dateFormat: 'yy-mm-dd',
        showOtherMonths: true,
        selectOtherMonths: true,
        showWeek: true,
        beforeShowDay: enableSUNDAYS,
        onSelect: function (selectedDate) {
            var dateText = $.datepicker.formatDate("yy-mm-dd", $(this).datepicker("getDate"));
            $('#date_start_from').text(dateText);
            
            var dt = new Date(selectedDate);
            dt.setDate(dt.getDate() - 6);
            $("#date_start_from").datepicker("option", "maxDate", dt);
            fetchGraphData();
        }
    });
    function enableSUNDAYS(date) {
        var day = date.getDay();
        return [(day == 0), ''];
    }
    
    $(".nav-pills > li").click(function(){
        $(this).addClass("active").siblings().removeClass("active");
        
        var reportName = $(this).data("report-name");
        $("h3").html(reportName);
        
        if($(this).data("report-id") == 7)
        {
            $(".customer-type").addClass('hidden');
            $(".order-type").addClass('hidden');
        }
        else if($(this).data("report-id") == 3)
        {
            $(".order-type").addClass('hidden');
        } else {
            $(".order-type").removeClass('hidden');
        }
        
        if($(this).data("report-id") == 2)
        {
            $(".product-div").removeClass('hidden');
            $(".coupon-div").addClass('hidden');
            $(".extended-offer-div").addClass('hidden');
            $(".promo-div").addClass('hidden');
        }
        else if($(this).data("report-id") == 4)
        {
            $.ajax({
                type        : 'post',
                dataType    : 'json',
                url         : '/storeReports/getOrderCouponList',
                data        : {
                },
                success     : function (result) {
                    if(result)
                    {
                        var option = '<option value="">Select Coupon</option>';
                        $.each(result, function(key,value){
                            option += '<option value="' + value.Order.coupon + '">' + value.Order.coupon + '</option>';
                        });
                        $(".coupon-div #coupon_code").html(option);
                    }
                }
            });
            $(".coupon-div").removeClass('hidden');
            $(".extended-offer-div").addClass('hidden');
            $(".promo-div").addClass('hidden');
            $(".product-div").addClass('hidden');
        }
        else if($(this).data("report-id") == 5)
        {
            $.ajax({
                type        : 'post',
                dataType    : 'json',
                url         : '/storeReports/getOrderPromoList',
                data        : {
                },
                success     : function (result) {
                    if(result)
                    {
                        var option = '<option value="">Select Offer</option>';
                        $.each(result, function(key,value){
                            option += '<option value="' + value.Offer.id + '">' + value.Offer.description + '</option>';
                        });
                        $(".promo-div #promo_id").html(option);
                    }
                }
            });
            $(".promo-div").removeClass('hidden');
            $(".extended-offer-div").addClass('hidden');
            $(".coupon-div").addClass('hidden');
            $(".product-div").addClass('hidden');
        }
        else if($(this).data("report-id") == 6)
        {
            $.ajax({
                type        : 'post',
                dataType    : 'json',
                url         : '/storeReports/getOrderExtendedOfferList',
                data        : {
                },
                success     : function (result) {
                    if(result)
                    {
                        var option = '<option value="">Select Extended Offer</option>';
                        $.each(result, function(key,value){
                            option += '<option value="' + value.Item.id + '">' + value.Item.name + '</option>';
                        });
                        $(".extended-offer-div #extended_offer_id").html(option);
                    }
                }
            });
            $(".extended-offer-div").removeClass('hidden');
            $(".coupon-div").addClass('hidden');
            $(".promo-div").addClass('hidden');
            $(".product-div").addClass('hidden');
        } else {
            $(".coupon-div").addClass('hidden');
            $(".promo-div").addClass('hidden');
            $(".extended-offer-div").addClass('hidden');
            $(".product-div").addClass('hidden');
        }
        fetchGraphData();
    });
    
    $(".coupon-div #coupon_code").on('change', function(){
        fetchGraphData();
    });
    
    $(".promo-div #promo_id").on('change', function(){
        fetchGraphData();
    });
    
    $(".extended-offer-div #extended_offer_id").on('change', function(){
        fetchGraphData();
    });
    
    $(".product-div #product_count").on('change', function(){
        fetchGraphData();
    });
    
    setTimeout(function(){
        fetchGraphData('first');
    },200)
</script>

<style>
    .btn-group.type button{
        background: gray;
        color: #ffffff;
        font-weight: 600;
    }
    .btn-group.order-type button{
        background: #ffc800;
        color: #ffffff;
        font-weight: 600;
    }
    
    .nav-pills.report-type > li
    {
        background: #93badc;
    }
    .nav-pills.report-type > li a
    {
        color: #ffffff;
        font-weight: 600;
        padding: 6px 7px;
    }
    .nav-pills.report-type > li.active > a, .nav-pills.report-type > li.active > a:hover, .nav-pills.report-type > li.active > a:focus{
        background: none;
    }
    .nav > li > a:hover, .nav > li > a:focus{
        background: none;
    }
    .nav-pills.report-type > li.active{
        background: #337ab7;
    }
    .btn:focus, .btn:active:focus, .btn.active:focus, .btn.focus, .btn:active.focus, .btn.active.focus{
        outline: none;
        outline: none;
        outline-offset: 0px;
    }
    .no-padding{padding: 0;}
    .form-control-small{width: auto; padding: 0px; display: inline-block; height: 25px;}
</style>