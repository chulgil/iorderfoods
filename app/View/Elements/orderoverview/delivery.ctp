<?php
$osDetailPickupDate = $this->Session->read('ordersummary.pickup_date');
?>

<div class="pay-date">
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
        <div class="row">
            <div class="col-lg-6 col-sm-6">
                <div class="common-input"> 
                    <p>Date <em>*</em></p>                           	
                    <div class="input-group date">
                        <?php
                        echo $this->Form->input('Store.pickup_date', array('type' => 'text', 'class' => 'inbox date-select datepicker', 'placeholder' => 'Date', 'label' => false, 'div' => false, 'required' => true, 'readOnly' => true));
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-sm-6">
                <div class="common-input">  
                    <div id="resvTime"></div>
                </div>
            </div>
        </div> 
        <?php
    } elseif (!empty($PreorderAllowed) && empty($nowAvail)) {
        //echo "Only Preorder allowed Show calendar";
        ?>
        <div class="row">
            <div class="col-lg-6 col-sm-6">
                <div class="common-input"> 
                    <p>Date <em>*</em></p>                           	
                    <div class="input-group date">
                        <?php
                        echo $this->Form->input('Store.pickup_date', array('type' => 'text', 'class' => 'inbox date-select datepicker', 'placeholder' => 'Date', 'label' => false, 'div' => false, 'required' => true, 'readOnly' => true));
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-sm-6">
                <div class="common-input">  
                    <div id="resvTime"></div>
                </div>
            </div>
        </div>
        <?php
    } elseif (empty($PreorderAllowed) && !empty($nowAvail) && empty($setPre)) {
        //echo "Only Now allowed not to Show calendar";
        ?>

        <?php
    } else {
        //echo "None is available";
        ?>
        Store is closed
        <?php
    }
//date time div end
    ?>
</div>

<?php
$deliveryadvanceDay = $store_data['Store']['deliverycalendar_limit'] - 1 + $store_data['Store']['deliveryblackout_limit'];
$datetoConvert = explode('-', $currentDateVar);
$datetoConvert = $datetoConvert[2] . '-' . $datetoConvert[0] . '-' . $datetoConvert[1];
$deliverymaxdate = date('m-d-Y', strtotime($datetoConvert . ' +' . $deliveryadvanceDay . ' day'));
$currentDateVar = date('m-d-Y', strtotime($datetoConvert . ' +' . $store_data['Store']['deliveryblackout_limit'] . ' day'));
?>
<script>

    function getTime(date, orderType, preOrder, returnspan, ortype) {
//        if ($("#now").is(":checked")) {
//            $('.pay-date').addClass("hidden");
//        }
        var type1 = 'Store';
        var type2 = 'pickup_time';
        var type3 = ortype;
        var storeId = '<?php echo $encrypted_storeId; ?>';
        var merchantId = '<?php echo $encrypted_merchantId; ?>';
        $.ajax({
            url: "<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'getStoreTime')); ?>",
            type: "Post",
            dataType: 'html',
            beforeSend: function () {
                $.blockUI({css: {
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: .5,
                        color: '#fff'
                    }});
            },
            complete: function () {
                $.unblockUI();
            },
            data: {storeId: storeId, merchantId: merchantId, date: date, type1: type1, type2: type2, type3: type3, orderType: orderType, preOrder: preOrder},
            success: function (result) {
                $('#' + returnspan).html(result);
//                if ($("#pre-order").is(":checked")) {
//                    $('.pay-date').removeClass("hidden");
//                }
            }
        });
    }

    //$(".date-select").datepicker({dateFormat: 'mm-dd-yy'}).datepicker("setDate", new Date());
    $(document).ready(function () {
        $('.date-select').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: '<?php echo $currentDateVar; ?>',
            maxDate: '<?php echo $deliverymaxdate; ?>',
            beforeShowDay: function (date) {
                var day = date.getDay();
                var array = '<?php echo json_encode($closedDay); ?>';
                return [array.indexOf(day) == -1];
            }
        });
<?php if (empty($osDetailPickupDate)) { ?>
            $(".date-select").datepicker("setDate", '<?php echo $currentDateVar; ?>');
            var date = '<?php echo $currentDateVar; ?>';
            getTime(date, 3, 1, 'resvTime');
<?php } else { ?>
            $(".date-select").datepicker("setDate", '<?php echo $osDetailPickupDate; ?>');
            var date = '<?php echo $osDetailPickupDate; ?>';
            getTime(date, 3, 1, 'resvTime');
<?php } ?>

        $('#StorePickupDate').on('change', function (e) {
            e.stopImmediatePropagation();
            var date = $(this).val();
            var orderType = 3; // 3= Take-away/pick-up
            var preOrder = $("input[name='data[pickup][type]']:checked").val();
            if (preOrder == '' || typeof preOrder == 'undefined') {
                preOrder = 0;
            }
            var type1 = 'Store';
            var type2 = 'pickup_time';
            var type3 = 'StorePickupTime';
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'getStoreTime')); ?>",
                type: "Post",
                dataType: 'html',
                complete: function () {
                    $.unblockUI();
                },
                data: {date: date, type1: type1, type2: type2, type3: type3, orderType: orderType, preOrder: preOrder},
                success: function (result) {
                    $('#resvTime').html(result);
                }
            });
        });
    });

</script>