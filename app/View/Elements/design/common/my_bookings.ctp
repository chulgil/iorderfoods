<!--<style>
    .order-type select,.user-detail{
        width: 74%;
    }
    .user-detail.date-select.hasDatepicker{
        width: 100%;
    }
    .tab-form{
        border: 1px solid #e2e2e2;
        padding: 24px 0;
    }
    .order-special-request a{
        text-decoration: underline;
    }
</style>-->
<?php
$storeId = $this->Session->read('store_id');
$url = HTTP_ROOT;
$imageurl = HTTP_ROOT . 'storeLogo/' . $store_data_app['Store']['store_logo'];
?>
<?php if (DESIGN == 3) { ?>
    <div class = "title-bar"> <?php echo __('Make Reservation'); ?> </div>
<?php }
?>
<div class="main-container reservation-page">
    <?php //echo $this->Session->flash(); ?>
    <div class="inner-wrap order-type common-white-bg profile">
        <div class="order-detail">
            <?php echo $this->Form->create('', array('id' => 'BookingForm', 'url' => array('controller' => 'pannels', 'action' => 'myBookings', $encrypted_storeId, $encrypted_merchantId))); ?>
            <?php if (!empty($authUrl)) { ?>
                <div class="order-special-request">
                    <h3>
                        <a href='javascript:poptastic("<?php echo $authUrl; ?>");'>Allow Calendar Sync <i class="fa fa-info-circle" data-toggle="tooltip" title="Allow calendar sync to google."></i></a>
                    </h3>
                </div>
            <?php } ?>
            <div class="order-drop-field clearfix">
                <div class="row">
                    <div class="col-sm-6 col-xs-6">
                        <div class="order-input form-group clearfix row">
                            <label for="name" class="control-label col-sm-12">Person <em>*</em></label>
                            <div class="col-sm-12">
                                <?php echo $this->Form->input('Booking.number_person', array('type' => 'select', 'class' => 'user-detail', 'options' => $number_person, 'label' => false, 'div' => false)); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                        <div class="order-input form-group clearfix row">
                            <label for="name" class="control-label col-sm-12">Reservation Date <em>*</em></label>
                            <div class="col-sm-12">
                                <?php echo $this->Form->input('Booking.start_date', array('type' => 'text', 'class' => 'user-detail date-select', 'placeholder' => 'Reservation Date', 'label' => false, 'div' => false, 'readOnly' => true)); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="order-input clearfix">
                    <label for="name">Reservation Time <em>*</em></label>
                    <div id="resvTime">
                        <div class="time-setting">
                            <label>Hour <em>*</em></label>
                            <?php echo $this->Form->input('Store.pickup_hour', array('type' => 'select', 'class' => 'user-detail', 'label' => false, 'div' => false, 'empty' => 'Select Hour')); ?>
                        </div>
                        <div class="time-setting">
                            <label>Minute <em>*</em></label>
                            <?php echo $this->Form->input('Store.pickup_minute', array('type' => 'select', 'class' => 'user-detail', 'label' => false, 'div' => false, 'empty' => 'Select Minute')); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="order-special-request">
                <h3>SPECIAL REQUEST</h3>
                <?php echo $this->Form->input('Booking.special_request', array('type' => 'textarea', 'class' => 'user-detail', 'placeholder' => 'Enter Special Request', 'maxlength' => '50', 'label' => false, 'div' => false)); ?>
            </div>
            <div class="order-bnt clearfix">
                <div class="row">
                    <?php if (DESIGN == 3) { ?>
                        <?php echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/login'", 'class' => 'p-cancle')); ?>
                        <?php echo $this->Form->button('Reserve', array('type' => 'submit', 'class' => 'theme-bg-1 p-save', 'id' => 'saveReservation')); ?>
                    <?php } else { ?>
                        <div class="col-sm-6 col-xs-6">
                            <?php echo $this->Form->button('Reserve', array('type' => 'submit', 'class' => 'theme-bg-1 p-save', 'id' => 'saveReservation')); ?>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                            <?php echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/users/login'", 'class' => 'theme-bg-2 p-cancle')); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
    <div class="inner-wrap mybooking common-white-bg no-border">
        <?php if (DESIGN == 3) { ?>
            <div class="reservations">
                <h3><?php echo __('My Reservations'); ?></h3>
            </div>
        <?php } ?>
        <div class="form-section">
            <?php echo $this->Form->create('MyBooking', array('url' => array('controller' => 'pannels', 'action' => 'myBookings'), 'id' => 'AdminId', 'class' => 'clearfix tab-form', 'type' => 'get')); ?>
            <div class="row">
                <?php echo $this->element('userprofile/filter_reservation'); ?>
                <div class="col-lg-4 col-sm-4 search-btm-btn">
                    <div class="row">
                        <div class="col-lg-6 col-sm-6 col-xs-6">
                            <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'srch-btn theme-bg-1')); ?>
                        </div>
                        <div class="col-lg-6 col-sm-6 col-xs-6">
                            <?php echo $this->Html->link('Clear', array('controller' => 'pannels', 'action' => 'myBookings', 'clear'), array('class' => 'clr-link theme-bg-2')); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <div class="inner-div clearfix">
                <div class="pagination-section clearfix">
                    <?php echo $this->element('pagination') ?>
                </div>
                <div class="responsive-table my-res-tbl-wrap">
                    <table class="table table-striped order-history-table">
                        <tr>
                            <th><?php echo __('No. of person'); ?></th>
                            <th><?php echo __('Reservation Date/Time'); ?></th>
                            <th><?php echo __('Store'); ?></th>
                            <th class="text-center"><?php echo __('Status'); ?></th>
                            <th class="text-center"><?php echo __('Action'); ?></th>
                            <th></th>
                        </tr>
                        <?php
                        if (!empty($myBookings)) {
                            foreach ($myBookings as $book) {
                                $today = date('Y-m-d');
                                $booking = date('Y-m-d', strtotime($book['Booking']['reservation_date']));
                                if ($today < $booking) { // future
                                    echo '<tr class = "danger">';
                                } else if ($today == $booking) { //present
                                    echo '<tr class = "success">';
                                } else {
                                    echo '<tr>';
                                }
                                ?>
                                <td><?php echo $book['Booking']['number_person']; ?></td>
                                <td>
                                    <?php
//                                    echo $book_date = $this->Common->storeTimeFormateUser($book['Booking']['reservation_date'], true);
//                                    $book_date2 = date('M d Y -  H:i a', strtotime($this->Common->storeTimeZoneUser('', $book['Booking']['reservation_date'])));
//                                    
                                    ?>
                                    <?php //echo $book_date2 = $this->Common->storeTimeFormateUser($this->Common->storeTimeZoneUser('', $book['Booking']['reservation_date']), true); ?>
                                    <?php
                                    $book_date2 = $this->Common->storeTimeFormateUser($book['Booking']['reservation_date'], true);
                                    echo $book_date2;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($book['Store'])) {
                                        echo $book['Store']['store_name'];
                                    }
                                    ?> </td>
                                <td class="text-center"><?php echo $book['BookingStatus']['name']; ?> </td>
                                <?php
                                if (!empty($storeId)) {
                                    if ($book['Booking']['store_id'] == $storeId) {
                                        if ($today < $booking) { // future
                                            echo '<td class="text-center">' . $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-times')) . '', array('controller' => 'pannels', 'action' => 'cancelBooking', $encrypted_storeId, $encrypted_merchantId, $this->Encryption->encode($book['Booking']['id'])), array('confirm' => __('Are you sure you want to cancel this booking?'), 'class' => 'delete', 'escape' => false)) . '<br/>' .
                                            $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-pencil')) . '', array('controller' => 'pannels', 'action' => 'updateBooking', $this->Encryption->encode($book['Booking']['id']), $encrypted_storeId, $encrypted_merchantId, $this->Encryption->encode($book['Booking']['id'])), array('escape' => false)) . '</td>';
                                        } else if ($today == $booking) { //present
                                            echo '<td class="text-center">' . $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-times')) . '', array('controller' => 'pannels', 'action' => 'cancelBooking', $encrypted_storeId, $encrypted_merchantId, $this->Encryption->encode($book['Booking']['id'])), array('confirm' => __('Are you sure you want to cancel this booking?'), 'class' => 'delete', 'escape' => false)) . '<br/> ' .
                                            $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-pencil')) . '', array('controller' => 'pannels', 'action' => 'updateBooking', $this->Encryption->encode($book['Booking']['id']), $encrypted_storeId, $encrypted_merchantId, $this->Encryption->encode($book['Booking']['id'])), array('escape' => false)) . '</td>';
                                        } else {
                                            echo '<td class="text-center">' . $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-trash-o')) . '', array('controller' => 'pannels', 'action' => 'deleteBooking', $encrypted_storeId, $encrypted_merchantId, $this->Encryption->encode($book['Booking']['id'])), array('confirm' => __('Are you sure you want to delete this booking?'), 'class' => 'delete', 'escape' => false)) . '</td>';
                                        }
                                    } else {
                                        ?>
                                        <td class="text-center"><?php echo "-" ?>-</td>
                                        <?php
                                    }
                                }
                                ?>
                                <td  class="text-center">
                                    <?php
                                    $strDomainUrl = $_SERVER['HTTP_HOST'];
                                    $strShareLink = "https://www.facebook.com/sharer/sharer.php?u=" . $strDomainUrl;
                                    ?>
                                    <a href="#" onclick='window.open("<?php echo $strShareLink; ?>", "", "width=500, height=300");'>
                                        <?php echo $this->Html->image('fb-share-button.png', array('alt' => 'fbshare')); ?>
                                    </a>
                                    <a target="blank" href= "http://twitter.com/share?text=I reserved table for <?php echo $book['Booking']['number_person']; ?> at <?php echo $_SESSION['storeName']; ?> on <?php echo $book_date2; ?>&url=<?php echo $url; ?>&via=<?php echo $_SESSION['storeName']; ?>">
                                        <?php echo $this->Html->image('tw-share-button.png', array('alt' => 'twshare')); ?>
                                    </a>
                                </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td class="text-center" colspan="5">' . __('No reservation request found') . '</td></tr>';
                        }
                        ?>
                    </table>
                </div>
                <?php echo $this->element('pagination') ?>
            </div>
        </div>
    </div>
</div>
<?php
echo $this->Html->css('pagination');
?>
<script>
    function poptastic(url) {
        var newWindow = window.open(url, 'name', 'height=600,width=450');
        if (window.focus) {
            newWindow.focus();
        }
    }
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $("#MerchantLock").change(function () {
            $("#AdminId").submit();
        });

        function getTime(date, orderType, preOrder, returnspan, ortype) {
            var orderType = 1; // 3= Dine-in/Booking
            var preOrder = 0;
            var type1 = 'Booking';
            var type2 = 'start_time';
            var type3 = 'BookingStartTime';

            var storeId = '<?php echo $encrypted_storeId; ?>';
            var merchantId = '<?php echo $encrypted_merchantId; ?>';
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'getStoreTime')); ?>",
                type: "post",
                data: {storeId: storeId, merchantId: merchantId, date: date, type1: type1, type2: type2, type3: type3, orderType: orderType, preOrder: preOrder},
                success: function (result) {
                    $('#' + returnspan).html(result);
                }
            });
        }

        $(function () {
            $("#MyBookingFromDate, #MyBookingToDate").datepicker({dateFormat: 'yy-mm-dd'});
        });

        $('#BookingStartDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: '<?php echo $currentDateVar; ?>',
            maxDate: <?php echo!empty($store_data['Store']['calendar_limit']) ? "'+" . ($store_data['Store']['calendar_limit']) . "D'" : '+6D' ?>,
            beforeShowDay: function (date) {
                var day = date.getDay();
                var array = '<?php echo json_encode($closedDay); ?>';
                return [array.indexOf(day) == -1];
            }
        });

        //$(".date-select").datepicker("setDate", '<?php echo $currentDateVar; ?>');

        //var date = '<?php echo $currentDateVar; ?>';
        //getTime(date, 1, 0, 'resvTime');

        $('#BookingStartDate').on('change', function () {
            var date = $(this).val();
            var orderType = 1; // 3= Dine-in/Booking
            var preOrder = 0;
            var type1 = 'Booking';
            var type2 = 'start_time';
            var type3 = 'BookingStartTime';
            var storeId = '<?php echo $encrypted_storeId; ?>';
            var merchantId = '<?php echo $encrypted_merchantId; ?>';
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'getStoreTime')); ?>",
                type: "Post",
                dataType: 'html',
                async: false,
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
                data: {storeId: storeId, merchantId: merchantId, date: date, type1: type1, type2: type2, type3: type3, orderType: orderType, preOrder: preOrder},
                complete: function () {
                    $.unblockUI();
                },
                success: function (result) {
                    $('#resvTime').html(result);
                }
            });
        });

        $("#BookingForm").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Booking][start_date]": {
                    required: true,
                }, "data[Booking][start_time]": {
                    required: true,
                }, "data[Store][pickup_hour]": {
                    required: true,
                }, "data[Store][pickup_minute]": {
                    required: true,
                }
            },
            messages: {
                "data[Booking][start_date]": {
                    required: "Please select booking date",
                }, "data[Booking][start_time]": {
                    required: "Please select booking time",
                }, "data[Store][pickup_hour]": {
                    required: "Please select reservation hour.",
                }, "data[Store][pickup_minute]": {
                    required: "Please select reservation minute.",
                }
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            }
        });
        $('#MyBookingFromDate').datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function (selectedDate) {
                $("#MyBookingToDate").datepicker("option", "minDate", selectedDate);
            }

        });
        $('#MyBookingToDate').datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function (selectedDate) {
                $("#MyBookingFromDate").datepicker("option", "maxDate", selectedDate);
            }

        });
        $(document).on('click', '#saveReservation', function (e) {
            e.stopImmediatePropagation();
            if ($("#BookingForm").valid()) {
                $.blockUI({css: {
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: .5,
                        color: '#fff'
                    }});
            }
        });
    });
    if (window.opener && !window.opener.closed) {
        window.opener.popUpClosed();
    }
</script>