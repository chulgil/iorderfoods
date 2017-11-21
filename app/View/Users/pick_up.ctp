<div class="content single-frame">
    <div class="wrap">
        <?php echo $this->Session->flash(); ?>
        <?php
        echo $this->Form->create('DeliveryAddress', array('inputDefaults' => array('autocomplete' => 'off'), 'id' => 'Deliveryaddress', 'url' => array('controller' => 'users', 'action' => 'ordercatCheck', $orderId)));
        echo $this->Form->type('Store.id', array('type' => 'hidden', 'value' => $store_data['Store']['id']));
        ?>
        <div class="clearfix">
            <section class="form-layout sign-up fullwidth clearfix
                     ">
                <h2> <span>Pickup</span> </h2>

                <div class="address">
                    <address class="inbox">
                        <h3><?php echo $store_data['Store']['store_name']; ?></h3>
                        <p> <?php echo $store_data['Store']['address']; ?> <br> <?php echo $store_data['Store']['city'] . ' ' . $store_data['Store']['state'] . ' ' . $store_data['Store']['zipcode']; ?> <br> <?php echo $store_data['Store']['phone']; ?></p>
                    </address>
                </div>

                <?php
                $PreorderAllowed = $this->Common->checkPreorder();
                if (isset($store_data['Store']) && !empty($store_data['Store']['take_away_description'])) {

                    if ($setPre != 1 || $PreorderAllowed == 1) {
                        ?>                    
                        <div class="radio-btn space20 delivery-address-option" style="float:right;width:35%;">
                            <label class="common-bold common-size" for="other"><span></span><i class="fa fa-caret-down"></i> Detail</label>
                            <div style="font-size:14px;">
                                <?php echo $store_data['Store']['take_away_description']; ?>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
                <?php
//date time div start
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
                                <span class="title"><label>Pick Up Date <em>*</em></label></span>
                                <?php
                                echo $this->Form->input('Store.pickup_date', array('type' => 'text', 'class' => 'inbox date-select', 'placeholder' => 'Date', 'label' => false, 'div' => false, 'required' => true, 'readOnly' => true));
                                echo $this->Form->error('Store.pickup_date');
                                ?>

                            </div>

                            <div id="resvTime">


                            </div>
                        </li>

                    </ul>

                    <div class="button fullwidth">
                        <?php
                        echo $this->Form->button('Order Now', array('type' => 'submit', 'class' => 'btn green-btn'));
                        echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/customerDashboard/$encrypted_storeId/$encrypted_merchantId'", 'class' => 'btn green-btn'));
                        ?>
                    </div>
                    <?php
                } elseif (!empty($PreorderAllowed) && empty($nowAvail)) {
                    //echo "Only Preorder allowed Show calendar";
                    ?>
                    <ul id="pickupdata" class="clearfix">                    
                        <li>
                            <div style="float:left">
                                <span class="title"><label>Pick Up Date <em>*</em></label></span>
                                <?php
                                echo $this->Form->input('Store.pickup_date', array('type' => 'text', 'class' => 'inbox date-select', 'placeholder' => 'Date', 'label' => false, 'div' => false, 'required' => true, 'readOnly' => true));
                                echo $this->Form->error('Store.pickup_date');
                                ?>

                            </div>

                            <div id="resvTime">


                            </div>
                        </li>

                    </ul>

                    <div class="button fullwidth">
                        <?php
                        echo $this->Form->button('Order Now', array('type' => 'submit', 'class' => 'btn green-btn'));
                        echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/customerDashboard/$encrypted_storeId/$encrypted_merchantId'", 'class' => 'btn green-btn'));
                        ?>
                    </div>
                    <?php
                } elseif (empty($PreorderAllowed) && !empty($nowAvail) && empty($setPre)) {
                    //echo "Only Now allowed not to Show calendar";
                    ?>
                    <div class="button fullwidth">
                        <?php
                        echo $this->Form->button('Order Now', array('type' => 'submit', 'class' => 'btn green-btn'));
                        echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/customerDashboard/$encrypted_storeId/$encrypted_merchantId'", 'class' => 'btn green-btn'));
                        ?>
                    </div>
                    <?php
                } else {
                    //echo "None is available";
                    ?>
                    <section class="form-layout" style="width:100%;padding:10px;">
                        <span class="closeStore">Store is closed</span>
                    </section>
                    <?php
                }
//date time div end
                ?>
            </section>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>
<?php
$pickupadvanceDay = $store_data['Store']['pickcalendar_limit'] - 1 + $store_data['Store']['pickblackout_limit'];
$datetoConvert = explode('-', $currentDateVar);

$datetoConvert = $datetoConvert[2] . '-' . $datetoConvert[0] . '-' . $datetoConvert[1];
$pickupmaxdate = date('m-d-Y', strtotime($datetoConvert . ' +' . $pickupadvanceDay . ' day'));
$currentDateVar = date('m-d-Y', strtotime($datetoConvert . ' +' . $store_data['Store']['pickblackout_limit'] . ' day'));
?>

<script>

    function getTime(date, orderType, preOrder, returnspan) {
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
                $('#' + returnspan).html(result);
            }
        });
    }



//    $(document).ready(function () {
//        var setFlag = "<?php echo $setPre; ?>";
//        if (setFlag == 0) {
//            $('#pickupdata').css('display', 'none');
//            $('#StorePickupTime').css('display', 'none');
//            $('#StorePickupDate').css('display', 'none');
//            $('#StorePickupTimeNow').css('display', 'block');
//            $('#pickupnowdata').css('display', 'block');
//        } else {
//            $('#pickupdata').css('display', 'table');
//            $('#StorePickupTime').css('display', 'block');
//            $('#StorePickupDate').css('display', 'block');
//            $('#StorePickupTimeNow').css('display', 'none');
//            $('#pickupnowdata').css('display', 'none');
//        }
//    });

//    $("#pre-order").on('click', function () { // To Show
//        $('#pickupdata').css('display', 'table');
//        $('#StorePickupTime').css('display', 'block');
//        $('#StorePickupDate').css('display', 'block');
//        $('#StorePickupTimeNow').css('display', 'none');
//        $('#pickupnowdata').css('display', 'none');
//    });
//    $("#now").on('click', function () {// To hide
//        $('#pickupdata').css('display', 'none');
//        $('#StorePickupTime').css('display', 'none');
//        $('#StorePickupDate').css('display', 'none');
//        $('#StorePickupTimeNow').css('display', 'block');
//        $('#pickupnowdata').css('display', 'block');
//
//    });

    $("#Deliveryaddress").validate({
        rules: {
            "data[Store][pickup_hour]": {
                required: true,
            },
            "data[Store][pickup_date]": {
                required: true,
            },
        },
        messages: {
            "data[Store][pickup_hour]": {
                required: "Please select pickup time"
            },
            "data[Store][pickup_date]": {
                required: "Please enter your pickup date",
            }
        }
    });

    $('.date-select').datepicker({
        dateFormat: 'mm-dd-yy',
        minDate: '<?php echo $currentDateVar; ?>',
        maxDate: '<?php echo $pickupmaxdate; ?>',
        beforeShowDay: function (date) {
            var day = date.getDay();
            var array = '<?php echo json_encode($closedDay); ?>';
            return [array.indexOf(day) == -1];
        }
    });
    $(".date-select").datepicker("setDate", '<?php echo $currentDateVar; ?>');
    var date = '<?php echo $currentDateVar; ?>';
    getTime(date, 2, 1, 'resvTime');

    $('#StorePickupDate').on('change', function () {
        var date = $(this).val();
        var orderType = 2; // 3= Take-away/pick-up
        var preOrder = $("input[name='data[DeliveryAddress][type]']:checked").val();
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
    });


</script>