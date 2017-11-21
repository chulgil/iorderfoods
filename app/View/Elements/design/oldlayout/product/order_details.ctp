<?php
//pr($this->Session->read('Order'));
$url = HTTP_ROOT;
$imageurl = HTTP_ROOT . 'storeLogo/' . $store_data_app['Store']['store_logo'];

$protocol = 'http';
if (isset($_SERVER['HTTPS'])) {
    if (strtoupper($_SERVER['HTTPS']) == 'ON') {
        $protocol = 'https';
    }
}

$customer_vault_id = '';
$credit_type = 'visa';
$credit_mask = '';

if (count($nzsafe_info) > 0) {
    $customer_vault_id = $nzsafe_info['customer_vault_id'];
    $credit_mask = $nzsafe_info['credit_mask'];
    $credit_type = strtolower($nzsafe_info['credit_type']);
}

if (AuthComponent::User()) {
    $userId = AuthComponent::User('id');
    $isNonUser = false;
} else {
    $userId = 0;
    $isNonUser = true;
}

$PreorderAllowed = $this->Common->checkPreorder();


$deliveryDate = $this->Session->read('Order.store_pickup_date');
$deliveryTime = $this->Session->read('Order.store_pickup_time');
//selected date time
if (!empty($deliveryTime)) {
    $explodedData = explode(":", $deliveryTime);
    if (strpos($deliveryTime, 'pm') !== false || strpos($deliveryTime, 'am') !== false) {
        $explodedData[0] = date("H", strtotime($deliveryTime));
        $explodedData[1] = date("i", strtotime($deliveryTime));
    }
    if (!empty($explodedData[0])) {
        echo $this->Form->input('osPickupHour', array('id' => 'osPickupHour', 'type' => 'hidden', 'value' => $explodedData[0]));
    }
    if (!empty($explodedData[1])) {
        echo $this->Form->input('osPickupMinute', array('id' => 'osPickupMinute', 'type' => 'hidden', 'value' => $explodedData[1]));
    }
}
?>
<?php
echo $this->Html->css('popup');
//$popupstatus = $this->Common->popupallowed();
if (1) {
    echo $this->element('confirmaddress/deliveryaddress');
    echo $this->element('confirmaddress/pickupaddress');

    if ($this->Session->read('Order.order_type') == 3 && !$this->Session->check('Order.delivery_address_id') && !$isNonUser) {
        ?>
        <script>

            function getdeliveryAddress() {
                $.ajax({
                    type: 'POST',
                    url: '/ajaxMenus/delivery',
                    async: false,
                    data: {},
                    success: function (response) {
                        $("div#chkDeliveryAddress").show();
                        $("div#chkOrderType").hide();
                        $("div#tab1login").hide();
                        $("#chkDeliveryAddress").html(response);
                    }
                });
            }
            $(document).ready(function () {
                $('#orddelivery').on('shown.bs.modal', function (e) {
                    getdeliveryAddress();
                }).modal('show');
            });
        </script>

        <?php
    }
}
?>
<input type="hidden" id="use_nzsafe" value="<?= $customer_vault_id ?>" />
<input type="hidden" id="isNonUser" value="<?= $isNonUser ?>" />

<div class="content single-frame clearfix">
    <section class="form-layout delivery-form order-overview">
        <h2> <span>Order Overview</span>  </h2>  
        <div class="editable-form">     
            <?php echo $this->element('design/oldlayout/element/order-element-calculation'); ?>
            <?php //echo $this->element('order-element'); ?>

        </div>
        <div class="paymentForm">
            <?php echo $this->element('design/oldlayout/product/payment_form'); ?>
        </div>
    </section>

    <!-- ##### Dlivery Address section start ##### -->
    <section class="form-layout pickup-form">
        <?php
        if ($delivery_address) {
            if (AuthComponent::User()) {
                ?>
                <h2> <span>Delivery Address</span> </h2>     
                <div class="address">
                    <address class="inbox">
                        <p class="custdeladd"><?php
                            echo $delivery_address['DeliveryAddress']['name_on_bell'] . '<br>' . $delivery_address['DeliveryAddress']['address']
                            . '<br>' . $delivery_address['DeliveryAddress']['city'] . ', ' . $delivery_address['DeliveryAddress']['state']
                            . ' ' . $delivery_address['DeliveryAddress']['zipcode'] . '<br>' . $delivery_address['CountryCode']['code'] . '' . $delivery_address['DeliveryAddress']['phone'];
                            ?></p>
                    </address>
                    <button type="button" class="btn green-btn changeOrderType pull-right" id="changeOrderTypeV">Change Delivery Address</button>
                </div>
                <?php
            } else {
                if ($_SESSION['Order']['order_type'] == 3) {
                    ?>
                    <h2> <span>Delivery Address</span> </h2>     
                    <div class="address">
                        <address class="inbox">
                            <p class="custdeladd"><?php
                                echo $delivery_address['DeliveryAddress']['email'] . '<br>' . $delivery_address['DeliveryAddress']['name_on_bell'] . '<br>' . $delivery_address['DeliveryAddress']['address']
                                . '<br>' . $delivery_address['DeliveryAddress']['city'] . ', ' . $delivery_address['DeliveryAddress']['state']
                                . ' ' . $delivery_address['DeliveryAddress']['zipcode'] . '<br>' . $delivery_address['CountryCode']['code'] . '' . $delivery_address['DeliveryAddress']['phone'];
                                ?></p>
                        </address>
                    </div>
                <?php } else { ?>
                    <h2> <span>Your Information</span> </h2>    
                    <div class="address">
                        <address class="inbox">
                            <p class="custdeladd"><?php
                                echo $delivery_address['DeliveryAddress']['email'] . '<br>' . $delivery_address['DeliveryAddress']['name_on_bell'] . '<br>' . $delivery_address['CountryCode']['code'] . '' . $delivery_address['DeliveryAddress']['phone'];
                                ?></p>
                        </address>
                    </div>
                    <?php
                }
            }
        } else {
            ?>
            <h2> <span>Store Address</span> </h2>     
            <div class="address">
                <address class="inbox">
                    <p>
                        <?php
                        echo "<span class='common-bold'>" . $store_result['Store']['store_name'] . '</span><br>' . $store_result['Store']['address']
                        . '<br>' . $store_result['Store']['city'] . ', ' . $store_result['Store']['state']
                        . ' ' . $store_result['Store']['zipcode'] . '<br>' . $store_result['Store']['phone'];
                        ?>
                    </p>
                </address>
            </div>
        <?php } ?>   
    </section>
    <!-- ##### Dlivery Address section start ##### -->

    <!-- ##### Change order type section start ##### -->
    <section class="form-layout pickup-form pickup-order-form">
        <h2> <span>CHANGE ORDER METHOD</span> </h2>
        <div class="orderChangeMsg">
        </div>
        <?php $ordeType = $this->Session->read('Order.order_type'); ?>
        <div class="check-btn space20 delivery-address-option">
            <?php if ($store_result['Store']['is_delivery'] == 1) { ?>
                <input type="radio" class="ordertype changeOrderType" id="order_type_1"  value="3" name="orderType"  <?php echo ($ordeType == 3) ? "checked" : ''; ?> />
                <label for="order_type_1" class="common-bold common-size">
                    <span></span>Delivery
                </label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php } ?>
            <?php if ($store_result['Store']['is_take_away'] == 1) { ?>	
                <input type="radio" class="ordertype changeOrderType" id="order_type_2"  value="2" name="orderType" <?php echo ($ordeType == 2) ? "checked" : ''; ?> />
                <label for="order_type_2" class="common-bold common-size">
                    <span></span>Pick Up
                </label>
            <?php } ?>
        </div>

        <?php $preOrder = $this->Session->read('Order.is_preorder'); ?>
        <?php
        //date time div start
        $PreorderAllowed = $this->Common->checkPreorder();
        /*
          PreorderAllowed - 0 (Preorder not allow), 1 (Allowed) - Flag Based
          nowAvail - 0 (current date Black out), 1 (Current day available)- Date based
          setPre - 0 (Now is avalable), 1 (Preorder is available) - Time based
          close day  - array based on holidays dates
         */
        if (!empty($PreorderAllowed) && !empty($nowAvail)) {
            //echo "Both are avalibale Show calendar";
            ?>
            <ul id="pickupdata" class="clearfix">
                <li>
                    <div style="float:left">
                        <span class="title"><label><span  class='datelabel'>Delivery Date</span><em>*</em></label></span>
                        <?php
                        echo $this->Form->input('Store.pickup_date', array('type' => 'text', 'class' => 'inbox date-select changeOrderType', 'placeholder' => 'Date', 'label' => false, 'div' => false, 'required' => true, 'readOnly' => true, 'value' => $deliveryDate));
                        echo $this->Form->error('Store.pickup_date');
                        ?>
                    </div>
                    <div id="resvTime">
                    </div>
                </li>
            </ul>
            <div class="button-frame text-right">
                <button type="button" class="btn green-btn changeOrderType" id="changeOrderType">CHANGE</button>
            </div>
            <?php
        } elseif (!empty($PreorderAllowed) && empty($nowAvail)) {
            //echo "Only Preorder allowed Show calendar";
            ?>
            <ul id="pickupdata" class="clearfix">
                <li>
                    <div style="float:left">
                        <span class="title"><label><span  class='datelabel'>Delivery Date</span><em>*</em></label></span>
                        <?php
                        echo $this->Form->input('Store.pickup_date', array('type' => 'text', 'class' => 'inbox date-select changeOrderType', 'placeholder' => 'Date', 'label' => false, 'div' => false, 'required' => true, 'readOnly' => true, 'value' => $deliveryDate));
                        echo $this->Form->error('Store.pickup_date');
                        ?>
                    </div>
                    <div id="resvTime">
                    </div>
                </li>
            </ul>
            <div class="button-frame text-right">
                <button type="button" class="btn green-btn changeOrderType" id="changeOrderType">CHANGE</button>
            </div>
            <?php
        } elseif (empty($PreorderAllowed) && !empty($nowAvail) && empty($setPre)) {
            //echo "Only Now allowed not to Show calendar";
            ?>
            <div class="button-frame text-right">
                <button type="button" class="btn green-btn changeOrderType" id="changeOrderType">CHANGE</button>
            </div>
            <?php
        } else {
            //echo "None is available";
            ?>
            <ul id="pickupdata" class="clearfix">                    
                <li></li>
            </ul>
            <section class="form-layout" style="width:100%;padding:10px;">
                <span class="closeStore">Store is closed</span>
            </section>
            <?php
        }
        //date time div end
        ?>
    </section>
    <!-- ##### Change order type section End ##### -->

</div>

<?php
$pickupadvanceDay = $store_result['Store']['pickcalendar_limit'] - 1 + $store_result['Store']['pickblackout_limit'];
$deliveryadvanceDay = $store_result['Store']['deliverycalendar_limit'] - 1 + $store_result['Store']['pickblackout_limit'];

$datetoConvert = explode('-', $pickcurrentDateVar);
$datetoConvert = $datetoConvert[2] . '-' . $datetoConvert[0] . '-' . $datetoConvert[1];
$pickupmaxdate = date('m-d-Y', strtotime($datetoConvert . ' +' . $pickupadvanceDay . ' day'));
$pickcurrentDateVar = date('m-d-Y', strtotime($datetoConvert . ' +' . $store_result['Store']['pickblackout_limit'] . ' day'));

$datetoConvert = explode('-', $delcurrentDateVar);
$datetoConvert = $datetoConvert[2] . '-' . $datetoConvert[0] . '-' . $datetoConvert[1];
$deliverymaxdate = date('m-d-Y', strtotime($datetoConvert . ' +' . $deliveryadvanceDay . ' day'));
$delcurrentDateVar = date('m-d-Y', strtotime($datetoConvert . ' +' . $store_result['Store']['deliveryblackout_limit'] . ' day'));
?>

<style>
    .orderChangeMsg {
        font-size: 18px !important;
        font-weight: 500 !important;
        margin-bottom: 15px;
        padding: 5px !important;
    }

    .cardimg{display: inline-block !important;width:40px !important;max-width: none !important;}
    #field_use_nzsafe{margin-bottom: 10px;}
    /*.ui-datepicker-calendar { display: none !important; }*/
    .infomark{ display: inline-block; float: left; width: 17px;margin-right: 10px;margin-top: 3px;vertical-align: middle;}
    .checkmark{ display: inline-block !important;float: left;width: 15px !important;margin-right: 10px;margin-left: 15px;margin-top:3px;vertical-align: middle;}
    .chk-span{display: inline-block !important;float: left;width: 90% !important;}
    .chk{margin-bottom: 15px !important;font-weight: bold !important;padding-top: 3px;}
    .chk span{line-height: 19px;}
    .chk-wrap{margin-bottom: 15px !important;}

</style>

<script>

    function changeOrderType() {
        var returnValue = true;
        var orderType = $("input[name='orderType']:checked").val();
        var deliveryType = $("input[name='deliveryType']:checked").val();
        var storePickupDate = $('#StorePickupDate').val();
        var storePickuphour = $('#StorePickuphour').val();
        var StorePickupmin = $('#StorePickupmin').val();
        var storePickupTime = storePickuphour + ':' + StorePickupmin;
        $.ajax({
            url: "<?php echo $this->Html->url(array('controller' => 'Products', 'action' => 'ajaxChangeOrderType')); ?>",
            type: "Post",
            dataType: 'html',
            async: false,
            data: {orderType: orderType, deliveryType: deliveryType, storePickupDate: storePickupDate, storePickupTime: storePickupTime},
            success: function (result) {
                response = jQuery.parseJSON(result);
                if (response.status == 'Error') {
                    returnValue = false;
                    $("#errorPop").modal('show');
                    $("#errorPopMsg").html(response.msg);
                    return false;
                } else {
                    $('#orddelivery').modal('show');
                    if (response.status == 1) {
                        $(".dateInfo").html(response.date);
                        if (deliveryType == 1) {
                            $(".timeInfo").html($('#StorePickupTime :selected').html());
                        } else {
                            $(".timeInfo").html(response.time);
                        }
                        $(".orderChangeMsg").addClass("alert-success").html("<span>Order type is changed..</span>").fadeIn('slow');

                    } else {
                        $(".orderChangeMsg").addClass("alert-danger").html("<span>Order type is not changed..</span>").fadeIn('slow');
                    }
                }
            }
        });
        if (returnValue) {
            return 1;
        }
    }


    function changeDateLabel() {
        if ($(".changeOrderType:checked").val() == 3) {
            $("#pickupdata .datelabel").html("Delivery Date");
            $("#pickupdata .timelabel").html("Delivery Time");
        }
        if ($(".changeOrderType:checked").val() == 2) {
            $("#pickupdata .datelabel").html("Pickup Date");
            $("#pickupdata .timelabel").html("Pickup Time");
        }
        // for orderType radio button
//        var setFlag = $("input[name='deliveryType']:checked").val();
//        if (setFlag == 0) {
//            $('#pickupdata').css('display', 'none');
//            $('#StorePickupTime').css('display', 'none');
//            $('#StorePickupDate').css('display', 'none');
//        } else {
//            $('#pickupdata').css('display', 'block');
//            $('#StorePickupTime').css('display', 'block');
//            $('#StorePickupDate').css('display', 'block');
//        }
    }
    function changeOrderTypeLabel() {
        if ($(".changeOrderType:checked").val() == 3) {
            $("#pickupdata .datelabel").html("Delivery Date");
            $("#pickupdata .timelabel").html("Delivery Time");
        }
        if ($(".changeOrderType:checked").val() == 2) {
            $("#pickupdata .datelabel").html("Pickup Date");
            $("#pickupdata .timelabel").html("Pickup Time");
        }
        if ($(".changeOrderType:checked").val() == 3) {
            $(".common-bold.datelabel").html("Delivery Date");
            $(".common-bold.timelabel").html("Delivery Time");
        }
        if ($(".changeOrderType:checked").val() == 2) {
            $(".common-bold.datelabel").html("Pickup Date");
            $(".common-bold.timelabel").html("Pickup Time");
        }

        // for orderType radio button
//        var setFlag = $("input[name='deliveryType']:checked").val();
//        var preOrderFlag    = "<?php echo $PreorderAllowed; ?>";
//        var setNow          = "<?php echo $nowAvail; ?>";
//        if (setFlag == 0) {
//            if(!setNow && preOrderFlag)
//            {
//                $('#pickupdata').css('display', 'block');
//                $('#StorePickupTime').css('display', 'block');
//                $('#StorePickupDate').css('display', 'block');
//            } else {
//                $('#pickupdata').css('display', 'none');
//                $('#StorePickupTime').css('display', 'none');
//                $('#StorePickupDate').css('display', 'none');
//            }
//        } else {
//            $('#pickupdata').css('display', 'block');
//            $('#StorePickupTime').css('display', 'block');
//            $('#StorePickupDate').css('display', 'block');
//        }
    }


    function getlivetime() {
        var date = $('#StorePickupDate').val();
        var orderType = $("input[name='orderType']:checked").val();
        var preOrder = $("input[name='deliveryType']:checked").val();
        var type1 = 'Store';
        var type2 = 'pickup_time';
        var type3 = 'StorePickupTime';
        var storeId = '<?php echo $encrypted_storeId; ?>';
        var merchantId = '<?php echo $encrypted_merchantId; ?>';

        $.ajax({
            url: "<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'getStoreTime')); ?>",
            type: "Post",
            dataType: 'html',
            data: {storeId: storeId, merchantId: merchantId, date: date, type1: type1, type2: type2, type3: type3, orderType: orderType, preOrder: preOrder},
            success: function (result) {
                $('#resvTime').html(result);
            }
        });
    }

    $(document).ready(function () {


        $('.date-select').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: '<?php echo (($ordeType == 3) ? $delcurrentDateVar : $pickcurrentDateVar); ?>',
            maxDate: '<?php echo (($ordeType == 3)) ? $deliverymaxdate : $pickupmaxdate; ?>',
            beforeShowDay: function (date) {
                var day = date.getDay();
                var array = '<?php echo json_encode($closedDay); ?>';
                return [array.indexOf(day) == -1];
            }
        });

        $(".date-select").datepicker("setDate", '<?php echo (($deliveryDate != '') ? $deliveryDate : (($ordeType == 3) ? $delcurrentDateVar : $pickcurrentDateVar)); ?>');


// Get Selected default order type and date & time      
        $('#loading').hide();
        var date = '<?php echo (($deliveryDate != '') ? $deliveryDate : (($ordeType == 3) ? $delcurrentDateVar : $pickcurrentDateVar)); ?>';
        var orderType = $("input[name='orderType']:checked").val();
        var preOrder = $("input[name='deliveryType']:checked").val();
        var type1 = 'Store';
        var type2 = 'pickup_time';
        var type3 = 'StorePickupTime';
        var storeId = '<?php echo $encrypted_storeId; ?>';
        var merchantId = '<?php echo $encrypted_merchantId; ?>';

        $.ajax({
            url: "<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'getStoreTime')); ?>",
            type: "Post",
            dataType: 'html',
            data: {storeId: storeId, merchantId: merchantId, date: date, type1: type1, type2: type2, type3: type3, orderType: orderType, preOrder: preOrder},
            success: function (result) {
                $('#resvTime').html(result);
                if ($("#osPickupHour").val()) {
                    $("#StorePickuphour").val($("#osPickupHour").val());
                }
                if ($("#osPickupMinute").val()) {
                    $("#StorePickupmin").val($("#osPickupMinute").val());
                }
                if (!$("#StorePickuphour").val()) {
                    $("#StorePickuphour").val($("#StorePickuphour option:first").val());
                }
                if (!$("#StorePickupmin").val()) {
                    $("#StorePickupmin").val($("#StorePickupmin option:first").val());
                }
            }
        });
        changeOrderTypeLabel();
        /////Default
        hideShowNow();
        function hideShowNow() {
            var orderType = $("input[name='orderType']:checked").val();
            if (orderType) {
                $.ajax({
                    url: "<?php echo $this->Html->url(array('controller' => 'products', 'action' => 'blackOutDaysHideNow')); ?>",
                    type: "Post",
                    //dataType: 'html',
                    data: {orderType: orderType},
                    success: function (result) {
                        if (result == 1) {
                            $('.hideNow').removeClass('hidden');
                        } else {
                            $('.hideNow').addClass('hidden');
                        }
                    }
                });
            }
        }



        $("#pre-order").on('click', function () { // To Show
            changeDateLabel();
        });

        $("#now").on('click', function () {// To hide
            changeDateLabel();
        });

        // get store time for a particullar orderType
        $('#StorePickupDate').on('change', function () {
            getlivetime();
        });
        //For delivery Popup
        $(document).on('click', '#deliveryButton', function (event) {
            $('#loading').show();
        });
        //For pickup Popup
        $(document).on('click', '#pickbtn', function (event) {
            $('#loading').show();
        });

        // Get Selected order type and date & time
        $(".ordertype").on('click', function () {
            var $typevalue = $(this).val();
            if ($typevalue == 3) {
<?php
$currentDateVar = $delcurrentDateVar;
$maxDateVar = $deliverymaxdate;
?>
            } else if ($typevalue == 2) {
<?php
$currentDateVar = $pickcurrentDateVar;
$maxDateVar = $pickupmaxdate;
?>
            }
            if ($typevalue) {
                hideShowNow();
                $('.date-select').datepicker('destroy');
                $('.date-select').datepicker({
                    dateFormat: 'mm-dd-yy',
                    minDate: '<?php echo $currentDateVar; ?>',
                    maxDate: '<?php echo $maxDateVar; ?>',
                });
                $(".date-select").datepicker("setDate", '<?php echo $currentDateVar; ?>');
                getlivetime();
                changeDateLabel();
            }
        });



        // Change order type ajax calling    
        $("#changeOrderType,#changeOrderTypeV").on('click', function () {// To hide    
            var orderType = $("input[name='orderType']:checked").val();
            if (orderType == 3) {
                //$('#orddelivery').modal('show');
                if (changeOrderType()) {
                    getdeliveryAddress();
                }
            } else if (orderType == 2) {
                $('#orderpickup').modal('show');
                changeOrderType();
            }

        });

        $(".orderChangeMsg").css('display', 'none');
        var displayElem = jQuery('.payoptioncls').find('input');
        displayElem.prop('checked', true);
    });
</script> 


<script>
    $(document).on('click', '.makeExpCheckout', function () {
        var specialComment = $('#UserComment').val();
        if (specialComment != '') {
            $.ajax({
                type: 'post',
                url: "<?php echo $this->Html->url(array('controller' => 'payments', 'action' => 'saveSpecialComment')); ?>",
                data: {'specialComment': specialComment},
                success: function (response) {
                    result = jQuery.parseJSON(response);
                    if (result.status == 'Success') {
                        window.location = "/payments/express_checkout";
                    } else {
                        window.location = "/payments/express_checkout";
                    }
                }
            });
        } else {
            window.location = "/payments/express_checkout";
        }
    });
    $(document).ready(function () {

        $('#credit_payment1').css('display', 'none');
        $('#credit_payment2').css('display', 'none');
        $('.check-out-option').css('display', 'none');

        $(document).on('click', '#payment', function () {
            validator.resetForm();
            if (use_nzsafe) {
                $('#field_use_nzsafe').css('display', 'block');
            } else {
                $('#credit_payment1').css('display', 'block');
                $('#credit_payment2').css('display', 'block');
                $('.check-out-option').css('display', 'none');
                $('.other-option').css('display', 'block');
            }
        });
        $(document).on('click', '#payment1', function () {
            validator.resetForm();
            $('#credit_payment1').css('display', 'block');
            $('#credit_payment2').css('display', 'block');
            $('.check-out-option').css('display', 'none');
            $('.other-option').css('display', 'block');
            $('#field_use_nzsafe').css('display', 'none');
            $('#flashMessage').css('display', 'none');

        });
        $(document).on('click', '#payment2', function () {
            validator.resetForm();
            $('#credit_payment1').css('display', 'none');
            $('#credit_payment2').css('display', 'none');
            $('.check-out-option').css('display', 'none');
            $('.other-option').css('display', 'block');
            $('#field_use_nzsafe').css('display', 'none');
            $('#flashMessage').css('display', 'none');
        });
        $(document).on('click', '#payment_nzsafe', function () {
            validator.resetForm();
            $('#credit_payment1').css('display', 'none');
            $('#credit_payment2').css('display', 'none');
            $('.check-out-option').css('display', 'none');
            $('.other-option').css('display', 'block');
        });
        $(document).on('click', '#payment_another', function () {
            validator.resetForm();
            $('#credit_payment1').css('display', 'block');
            $('#credit_payment2').css('display', 'block');
            $('.check-out-option').css('display', 'none');
            $('.other-option').css('display', 'block');
            $('#flashMessage').css('display', 'none');
        });

        var use_nzsafe = $("#use_nzsafe").val();
        if (use_nzsafe) {
            $("#payment").click();
            $("#payment_nzsafe").click();
        } else {
            $("#payment").click();
            $("#field_use_nzsafe").hide();
        }
        $(document).on('click', '#express-check-out', function () {
            validator.resetForm();
            $('#credit_payment1').css('display', 'none');
            $('#credit_payment2').css('display', 'none');
            $('.other-option').css('display', 'none');
            $('.check-out-option').css('display', 'block');
            $('#field_use_nzsafe').css('display', 'none');
            $('#flashMessage').css('display', 'none');
        });

        $('.date_select').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'mmy',
            yearRange: '2015:2040',
            onClose: function (dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, month, 1));
            }
        });

        if ($("input[name='payment']").length == 1) {
            $("input[name='payment']").click();
        }

        $("form").submit(function () {
            var paytype = $("input[name='payment']").val();
            if (paytype == 2) {
                event.preventDefault();
                var comment = $("#UserComment").val();
                $.ajax({
                    type: 'POST',
                    url: '/Payments/paymentSection',
                    data: {payment: paytype, comment: comment},
                    success: function (response) {
                        if (response == 1) {
                            $("input[name='payment']").val("0");
                            $("form").submit();
                        } else {

                        }
                    }
                });
            }
        });


        var expresscheck = $('#express-check-out').prop('checked');
        if (expresscheck) {
            $('#credit_payment1').css('display', 'none');
            $('#credit_payment2').css('display', 'none');
            $('.other-option').css('display', 'none');
            $('.check-out-option').css('display', 'block');
            $('#field_use_nzsafe').css('display', 'none');
            $('#flashMessage').css('display', 'none');
        }
        $(document).on('click', '.makePayment', function () {
            ret = true;
            $.ajax({
                type: 'post',
                url: '/orderOverviews/isItemDeliverable',
                async: false,
                success: function (result) {
                    if (result != '') {
                        results = jQuery.parseJSON(result);
                        if (results.status == 'Error') {
                            //alert(results.msg);
                            $("#errorPop").modal('show');
                            $("#errorPopMsg").html(results.msg);
                            ret = false;
                        }
                    }
                }
            });
            if (ret) {
                $.ajax({
                    type: 'POST',
                    url: '/orderOverviews/checkMendatoryItem',
                    async: false,
                    success: function (response) {
                        result = jQuery.parseJSON(response);
                        if (result.status == 'Error') {
                            //alert(result.msg);
                            $("#errorPop").modal('show');
                            $("#errorPopMsg").html(result.msg);
                            ret = false;
                        }
                    }
                });
            }
            return ret;
        });

    });

    //if(($("input[name='payment']:checked").val()==1) || ($("input[name='payment']:checked").val()==2)){    
    var validator = $("#PaymentPaymentSectionForm").validate({
        rules: {
            'data[Payment][creditype]': {
                required: true
            },
            'data[Payment][firstname]': {
                required: true,
                minlength: 2,
                lettersonly: true
            },
            'data[Payment][lastname]': {
                required: true,
                minlength: 2,
                lettersonly: true
            },
            'data[Payment][address]': {
                required: true
            },
            "data[Payment][cardnumber]": {
                required: true,
                number: true,
                minlength: 13,
                maxlength: 16
            },
            "data[Payment][cvv]": {
                required: true,
                number: true,
                minlength: 3
            },
            "data[Payment][expiryDate]": {
                required: true,
                number: true,
                maxlength: 4,
                minlength: 4
            }
        },
        messages: {
            'data[Payment][creditype]': {
                required: 'Please select card type.'
            },
            'data[Payment][firstname]': {
                required: 'Please enter First Name',
                minlength: 'Please Enter Atleast 2 Characters',
                lettersonly: "Only alphabates allowed"
            },
            'data[Payment][lastname]': {
                required: 'Please enter Last Name',
                minlength: 'Please enter atleast 2 characters',
                lettersonly: "Only alphabates allowed"
            },
            'data[Payment][address]': {
                required: 'Please enter address'
            },
            "data[Payment][cardnumber]": {
                required: "Please enter Card Number",
                number: "Please enter valid Card Number",
                minlength: 'Enter between 13-16 digits'
            },
            "data[Payment][cvv]": {
                required: "Plaese enter CVV",
                number: 'Please enter valid CVV',
                minlength: 'Enter atleast 3 digits'
            },
            "data[Payment][expiryDate]": {
                required: "Expiry date is required",
                number: "Only numbers are allowed"
            }
        }
    });
    //}    

    function creditForm() {

        var payment = $('input:radio[name=payment]:checked').val();
        var payment_vault = $('input:radio[name=payment_vault]:checked').val();
        if (payment != 1)
            return true;
        if (payment_vault == 1)
            return true;

        myCardNo = document.getElementById('CardNumber').value;
        myCardType = document.getElementById('CardType').value;
        if (myCardNo) {
            if (checkCreditCard(myCardNo, myCardType)) {
                return true;
            } else {
                $("#errorPop").modal('show');
                $("#errorPopMsg").html(ccErrors[ccErrorNo]);
                return false;
                //alert(ccErrors[ccErrorNo]);
            }
            return false;
        } else {
            return true;
        }

    }



    var ccErrorNo = 0;
    var ccErrors = new Array();

    ccErrors [0] = "<?php echo __('Unknown card type'); ?>";
    ccErrors [1] = "<?php echo __('No card number provided'); ?>";
    ccErrors [2] = "<?php echo __('Credit card number is in invalid format'); ?>";
    ccErrors [3] = "<?php echo __('Credit card number is invalid'); ?>";
    ccErrors [4] = "<?php echo __('Credit card number has an inappropriate number of digits'); ?>";
    ccErrors [5] = "<?php echo __('Warning! This credit card number is associated with a scam attempt'); ?>";

    function checkCreditCard(cardnumber, cardname) {

        var cards = new Array();

        cards [0] = {name: "Visa",
            length: "13,16",
            prefixes: "4",
            checkdigit: true};
        cards [1] = {name: "Master",
            length: "16",
            prefixes: "51,52,53,54,55",
            checkdigit: true};
        cards [2] = {name: "Discover",
            length: "16",
            prefixes: "6011,622,64,65",
            checkdigit: true};
        cards [3] = {name: "Amex",
            length: "15",
            prefixes: "34,37",
            checkdigit: true};

        var cardType = -1;
        for (var i = 0; i < cards.length; i++) {
            if (cardname.toLowerCase() == cards[i].name.toLowerCase()) {
                cardType = i;
                break;
            }
        }

        if (cardType == -1) {
            ccErrorNo = 0;
            return false;
        }
        if (cardnumber.length == 0) {
            ccErrorNo = 1;
            return false;
        }
        cardnumber = cardnumber.replace(/\s/g, "");

        var cardNo = cardnumber
        var cardexp = /^[0-9]{13,19}$/;
        if (!cardexp.exec(cardNo)) {
            ccErrorNo = 2;
            return false;
        }

        if (cards[cardType].checkdigit) {
            var checksum = 0;
            var mychar = "";
            var j = 1;
            var calc;
            for (i = cardNo.length - 1; i >= 0; i--) {
                calc = Number(cardNo.charAt(i)) * j;

                if (calc > 9) {
                    checksum = checksum + 1;
                    calc = calc - 10;
                }
                checksum = checksum + calc;
                if (j == 1) {
                    j = 2;
                } else {
                    j = 1;
                }

            }
            if (checksum % 10 != 0) {
                ccErrorNo = 3;
                return false;
            }
        }

        if (cardNo == '5490997771092064') {
            ccErrorNo = 5;
            return false;
        }
        var LengthValid = false;
        var PrefixValid = false;
        var undefined;
        var prefix = new Array();
        var lengths = new Array();

        prefix = cards[cardType].prefixes.split(",");

        for (i = 0; i < prefix.length; i++) {
            var exp = new RegExp("^" + prefix[i]);
            if (exp.test(cardNo))
                PrefixValid = true;
        }
        if (!PrefixValid) {
            ccErrorNo = 3;
            return false;
        }
        lengths = cards[cardType].length.split(",");
        for (j = 0; j < lengths.length; j++) {
            if (cardNo.length == lengths[j])
                LengthValid = true;
        }

        if (!LengthValid) {
            ccErrorNo = 4;
            return false;
        }
        ;
        return true;
    }
    function checkMandatoryItem() {
        $.ajax({
            type: 'POST',
            url: '/orderOverviews/checkMendatoryItem',
            async: false,
            success: function (response) {
                result = jQuery.parseJSON(response);
                if (result.status == 'Error') {
                    $("#errorPop").modal('show');
                    $("#errorPopMsg").html(result.msg);
                    return false;
                } else {
                    window.location = "/payments/paymentSection";
                }
            }
        });
    }
    if ($("#osPickupHour").val()) {
        $("#StorePickuphour").val($("#osPickupHour").val());
    }
    if ($("#osPickupMinute").val()) {
        $("#StorePickupmin").val($("#osPickupMinute").val());
    }
    if (!$("#StorePickuphour").val()) {
        $("#StorePickuphour").val($("#StorePickuphour option:first").val());
    }
    if (!$("#StorePickupmin").val()) {
        $("#StorePickupmin").val($("#StorePickupmin option:first").val());
    }
</script>

