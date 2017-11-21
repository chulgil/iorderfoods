<?php
$url = HTTP_ROOT;
?>
<!-- static-banner -->
<?php echo $this->element('hquser/static_banner'); ?>
<!-- /banner -->
<div class="signup-form">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="common-title clearfix">
                    <span class="yello-dash"></span>
                    <h2><?php echo __('My Reservations'); ?></h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php echo $this->Session->flash(); ?>
                <div class="form-bg">
                    <!-- ORDER TABS -->
                    <div class="sign-up order-content order-content-tabs clearfix">                        
                        <!-- SEARCH -->
                        <div class="tabs-search booking-search margin-top0 clearfix">
                            <?php echo $this->Form->create('MyBooking', array('url' => array('controller' => 'hqusers', 'action' => 'myBookings'), 'id' => 'AdminId', 'type' => 'post')); ?>
                            <div class="col-2">
                                <?php
                                $merchantDet = array();
                                $merchantList = $this->Common->getStores($this->Session->read('hq_id'));
                                foreach ($merchantList as $key => $value) {
                                    $J = $this->Encryption->encode($key);
                                    $merchantDet[$J] = $value;
                                }
                                echo $this->Form->input('Merchant.store_id', array('options' => $merchantDet, 'class' => 'inbox', 'div' => false, 'empty' => 'Please Select Store', 'label' => false));
                                ?>
                            </div>
                            <div class="col-2 tab-search-right">
                                <div class="clearfix">
                                    <div class="col-22">
                                        <?php echo $this->Form->input('MyBooking.from_date', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'From Date', 'label' => false, 'div' => false, 'readOnly' => true)); ?>
                                        <label id="MyBookingFromDate-error" class="error" for="MyBookingFromDate"></label>
                                    </div>
                                    <div class="col-22">
                                        <?php echo $this->Form->input('MyBooking.to_date', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'To Date', 'label' => false, 'div' => false, 'readOnly' => true)); ?>
                                        <label id="MyBookingToDate-error" class="error" for="MyBookingToDate"></label>
                                    </div>
                                </div>
                                <div class="searchh-btn">
                                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn common-config black-bg submitSearch')); ?>
                                    <?php echo $this->Html->link('Clear', array('controller' => 'hqusers', 'action' => 'myBookings', 'clear'), array('class' => 'btn common-config black-bg')); ?>
                                </div>
                            </div>
                            <?php echo $this->Form->end(); ?>
                        </div>

                        <!-- PAGINATION -->
			<div class="clearfix"></div>
                        <?php echo $this->element('pagination');?>
			<div class="clearfix"></div>
                        <!-- TAB PANES -->
                        <?php echo $this->element('show_pagination_count');?>
                        <div class="tab-content">
                            <!-- MY FAVORITES -->
                            <div role="tabpanel" class="tab-pane active">
                                <div class="tab-panes">                                
                                    <div class="table-responsive">
                                        <table class="table table-striped tab-panes-table">
                                            <thead>
                                                <tr>
                                                    <th><?php echo __('No. of person'); ?></th>
                                                    <th><?php echo __('Reservation Date/Time'); ?></th>
                                                    <th><?php echo __('Store'); ?></th>
                                                    <th class="text-center"><?php echo __('Status'); ?></th>
                                                    <th class="text-center"><?php echo __('Action'); ?></th>
                                                    <th></th>
                                                </tr>
                                            </thead>

                                            <tbody>
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
                                                    <td><?php echo $book_date = date('d M Y -  H:i a', strtotime($this->Hq->storeTimeZone('', $book['Booking']['reservation_date'], '', $book['Booking']['store_id']))); 
                                                     $book_date2 = date('M d Y -  H:i a', strtotime($this->Hq->storeTimeZone('', $book['Booking']['reservation_date'], '', $book['Booking']['store_id'])));?></td>
                                                    <td>
                                                        <?php
                                                        if (!empty($book['Store'])) {
                                                            echo $book['Store']['store_name'];
                                                        }
                                                        ?>
                                                    </td>
                                                    <td class="text-center"><?php echo $book['BookingStatus']['name']; ?> </td>
                                                    <?php
//                                                if (!empty($storeId)) {
                                                    if (!empty($book['Booking']['store_id'])) {
                                                        if ($today < $booking) { // future
                                                            echo '<td class="text-center">' . $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-times')) . 'Cancel', array('controller' => 'hqusers', 'action' => 'cancelBooking', $this->Encryption->encode($book['Booking']['store_id']), $this->Encryption->encode($book['Booking']['id'])), array('confirm' => __('Are you sure you want to cancel this booking?'), 'class' => 'delete', 'escape' => false));
                                                        } else if ($today == $booking) { //present
                                                            echo '<td class="text-center">' . $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-times')) . 'Cancel', array('controller' => 'hqusers', 'action' => 'cancelBooking', $this->Encryption->encode($book['Booking']['store_id']), $this->Encryption->encode($book['Booking']['id'])), array('confirm' => __('Are you sure you want to cancel this booking?'), 'class' => 'delete', 'escape' => false));
                                                        } else {
                                                            echo '<td class="text-center">' . $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-trash-o','title'=>'')), array('controller' => 'hqusers', 'action' => 'deleteBooking', $this->Encryption->encode($book['Booking']['id'])), array('confirm' => __('Are you sure you want to delete this booking?'), 'class' => 'delete', 'escape' => false)) . '</td>';
                                                        }
                                                    } else {
                                                        ?>
                                                        <td class="text-center"><?php echo "-" ?></td> 
                                                        <?php
                                                    }
//                                                }
                                                    ?>
                                                    <td  class="text-center">  
                                                        <?php
                                                        $strDomainUrl = $_SERVER['HTTP_HOST'];
                                                        $strShareLink = "https://www.facebook.com/sharer/sharer.php?u=" . $strDomainUrl;
                                                        ?>
                                                        <a href="#" onclick='window.open("<?php echo $strShareLink; ?>", "", "width=500, height=300");'>
                                                            <?php echo $this->Html->image('fb-share-button.png', array('alt' => 'fbshare')); ?>
                                                        </a>
                                                        <span>
                                                            <a target="blank" href= "http://twitter.com/share?text=I reserved table for <?php echo $book['Booking']['number_person']; ?> at <?php echo $book['Store']['store_name']; ?> on <?php echo $book_date2; ?>&url=<?php echo $url; ?>&via=<?php echo $book['Store']['store_name']; ?>"><?php echo $this->Html->image('tw-share-button.png', array('alt' => 'twshare')); ?> </a>
                                                        </span>
                                                    </td>
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                echo '<tr><td class="text-center" colspan="5">' . __('No reservation request found') . '</td></tr>';
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /TAB PANES END -->

                        <!-- PAGINATION -->
                        <?php echo $this->element('pagination');?>
                    </div>
                    <!-- ORDER TABS END -->

                    <!-- -->
                    <div class="ext-border">
                        <?php echo $this->Html->image('hq/thick-border.png', array('alt' => 'user')) ?>
                    </div>
                    <!-- -->
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
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
        $(document).on('click', '.submitSearch', function () {
            var sData = $('#MyBookingFromDate').val();
            var eData = $('#MyBookingToDate').val();
            if (sData != "" && eData == "") {
                $('#MyBookingToDate-error').html('Please fill to date.');
                return false;
            }
            if (eData != "" && sData == "") {
                $('#MyBookingFromDate-error').html('Please fill from date.');
                return false;
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        $("#MerchantStoreId").change(function () {
            $("#AdminId").submit();
        });
    });
</script>
