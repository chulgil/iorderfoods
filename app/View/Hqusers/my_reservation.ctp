<style>
    .btnsize{
        font-size: 14px;
    }
    .inbox.date-select.hasDatepicker{
        width: 122px;
    }
    #MerchantStoreId{
        margin-left: -30px;
    }
    .btn.green-btn.btnsize{
        margin-top: 15px;
    }
</style>
<div class="content">
    <div class="container">
        <div class="row">
            <?php echo $this->Session->flash(); ?>
            <div class="order-hostory form-layout margin-60" style="background-color: #fff;">
                <h2><span><?php echo __('Make Reservation'); ?></span></h2>
                <div id="horizontalTab">
                    <!-- FORM VIEW -->
                    <?php echo $this->Form->create('', array('id' => 'BookingForm', 'url' => array('controller' => 'pannels', 'action' => 'myReservation'))); ?>
                    <section style="display: table;">
                        <div class="col-lg-3 filter" style="margin-left: 15px;">
                            <?php
                            $merchantDet = array();
                            $merchantList = $this->Common->getStores($this->Session->read('hq_id'));
                            foreach ($merchantList as $key => $value) {
                                $J = $this->Encryption->encode($key);
                                $merchantDet[$J] = $value;
                            }
                            echo $this->Form->input('Merchant.store_id', array('options' => $merchantDet, 'class' => 'form-control', 'div' => false, 'empty' => 'Please Select Store', 'label' => FALSE));
                            ?>
                        </div>
                        <ul class="clearfix">
                            <li>
                                <span class="title"><label>Person <em>*</em></label></span>
                                <div class="title-box"><?php
                                    echo $this->Form->input('Booking.number_person', array('type' => 'select', 'class' => 'inbox', 'options' => $number_person, 'label' => false, 'div' => false));
                                    echo $this->Form->error('Booking.number_person');
                                    ?>
                                </div>
                            </li>
                            <li>
                                <span class="title"><label>Reservation Date <em>*</em></label></span>
                                <div class="title-box"><?php
                                    echo $this->Form->input('Booking.start_date', array('type' => 'text', 'class' => 'inbox date-select', 'placeholder' => 'Reservation Date', 'label' => false, 'div' => false, 'readOnly' => true));
                                    echo $this->Form->error('Booking.start_date');
                                    ?>
                                </div>
                            </li>
                            <li>
                                <span class="title"><label>Reservation Time<em>*</em></label></span>
                                <div id="resvTime">
                                </div>
                            </li>
                            <li>
                                <span class="title"><label>Special Request </label></span>
                                <div class="title-box">
                                    <?php
                                    echo $this->Form->input('Booking.special_request', array('type' => 'textarea', 'class' => 'inbox', 'placeholder' => 'Enter Special Request', 'maxlength' => '50', 'label' => false, 'div' => false));
                                    echo $this->Form->error('Booking.special_request');
                                    ?>
                                </div>
                            </li>
                        </ul>
                        <?php if (isset($store['Store']) && !empty($store['Store']['dine_in_description'])) { ?>                    
                            <div class="radio-btn space20 delivery-address-option">
                                <label class="common-bold common-size" for="other"><span></span><i class="fa fa-angle-double-down"></i> Detail</label>
                                <div class="edit-link"> 
                                    <?php echo $store['Store']['dine_in_description']; ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="button">
                            <?php
                            echo $this->Form->button('Request', array('type' => 'submit', 'class' => 'btn green-btn'));
                            echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/hqusers/myBookings'", 'class' => 'btn green-btn'));
                            ?>
                        </div>
                    </section>
                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#MerchantStoreId').on('change', function () {
            var storeId = $(this).val();
            if (storeId) {
                var date = '<?php echo $currentDateVar; ?>';
                getTime(date, 3, 1, 'resvTime', storeId);
            } else {
                $('#resvTime').html('');
            }
        });

        function getTime(date, orderType, preOrder, returnspan, storeId) {
            var type1 = 'Store';
            var type2 = 'pickup_time';
            var type3 = 'type3';
            var merchantId = '<?php echo $this->Encryption->encode($this->Session->read('hq_id')); ?>';
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'hqusers', 'action' => 'getStoreTime')); ?>",
                type: "Post",
                dataType: 'html',
                data: {storeId: storeId, merchantId: merchantId, date: date, type1: type1, type2: type2, type3: type3, orderType: orderType, preOrder: preOrder},
                success: function (result) {
                    $('#' + returnspan).html(result);
                }
            });
        }

        $(function () {
            $("#MyBookingFromDate, #MyBookingToDate").datepicker
                    ({dateFormat: 'yy-mm-dd'});
        });


        $('#BookingStartDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: '<?php echo $currentDateVar; ?>',
            maxDate: <?php echo !empty($store_data['Store']['calendar_limit']) ? "'+" . ($store_data['Store']['calendar_limit']) . "D'" : '+6D' ?>,
            beforeShowDay: function (date) {
                var day = date.getDay();
                var array ='<?php echo json_encode($closedDay); ?>';
                return [array.indexOf(day) == -1];
            }
        });
        $(".date-select").datepicker("setDate", '<?php echo $currentDateVar; ?>');
        $('#BookingStartDate').on('change', function () {
            var date = $(this).val();
            var orderType = 1; // 3= Dine-in/Booking
            var preOrder = 0;
            var type1 = 'Booking';
            var type2 = 'start_time';
            var type3 = 'BookingStartTime';
            var storeId = $('MerchantStoreId').val();
            var merchantId = '<?php echo $this->Encryption->encode($this->Session->read('hq_id')); ?>';
            if (storeId == '') {
                alert('Please Select Store.');
            }
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'hqusers', 'action' => 'getStoreTime')); ?>",
                type: "Post",
                dataType: 'html',
                data: {storeId: storeId, merchantId: merchantId, date: date, type1: type1, type2: type2, type3: type3, orderType: orderType, preOrder: preOrder},
                success: function (result) {
                    $('#resvTime').html(result);
                }
            });
        });

        $("#BookingForm").validate({
            rules: {
                "data[Booking][start_date]": {
                    required: true,
                }, "data[Booking][start_time]": {
                    required: true,
                }
            },
            messages: {
                "data[Booking][start_date]": {
                    required: "Please select booking date",
                }, "data[Booking][start_time]": {
                    required: "Please select booking time",
                }
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        $("#MerchantLock").change(function () {
            $("#AdminId").submit();
        });

    });
</script>