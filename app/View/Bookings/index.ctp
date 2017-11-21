<?php
echo $this->Html->css('fullcalendar.min');
echo $this->Html->css('jquery.qtip.css');
echo $this->Html->script('moment.min');
echo $this->Html->script('fullcalendar.min');
//echo $this->Html->script('gcal');
echo $this->Html->script('jquery.qtip.js');
?>
<div class="row">
    <div class="col-lg-12">
        <h3>Dine-In</h3>
        <hr>
    </div>
    <div class="col-lg-12">
        <div id='calendar'></div>
    </div>
    <div class="col-lg-12">&nbsp;</div>
    <hr>
    <div class="col-lg-12">
        <?php echo $this->Session->flash(); ?>
        <div class="table-responsive">
            <?php echo $this->Form->create('Booking', array('url' => array('controller' => 'bookings', 'action' => 'index'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="col-lg-2">

                <?php echo $this->Form->input('Booking.is_replied', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => array('1' => 'Replied', '2' => 'Not Replied'), 'empty' => 'All')); ?>
            </div>

            <div class="row padding_btm_20">
                <div class="col-lg-2">
                    <?php echo $this->Form->input('OrderStatus.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $statusList, 'empty' => 'Select Status')); ?>
                </div>



                <div class="col-lg-3">
                    <?php echo $this->Form->input('Order.keyword', array('value' => $keyword, 'label' => false, 'div' => false, 'placeholder' => 'Keyword Search', 'class' => 'form-control')); ?>
                    <span class="blue">(<b>Search by:</b>Customer name)</span>
                </div>



                <div class="col-lg-2">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                    <?php echo $this->Html->link('Clear', array('controller' => 'bookings', 'action' => 'index', 'clear'), array('class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <div class="row">
                <div class="col-sm-6">
                    <?php echo $this->Paginator->counter('Page {:page} of {:pages}'); ?>
                </div>
                <div class="col-sm-6 text-right">
                    <?php echo $this->Paginator->counter('showing {:current} records out of {:count} total'); ?>
                </div>
            </div>
            <?php echo $this->Form->create('Order', array('url' => array('controller' => 'kitchens', 'action' => 'UpdateOrderStatus'), 'id' => 'OrderId', 'type' => 'post')); ?>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>
<!--                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Booking.id', 'Request Id.'); ?></th>-->
                        <th  class="th_checkbox" style="width:15%;"><?php echo $this->Paginator->sort('User.fname', 'Customer Name'); ?></th>
                        <th  class="th_checkbox">Persons</th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Booking.reservation_date', 'Date.'); ?></th>
                        <th  class="th_checkbox">Time</th>
                        <th  class="th_checkbox">Status</th>
                        <th  class="th_checkbox">Replied</th>
                        <th  class="th_checkbox">Admin Comment</th>
                        <th  class="th_checkbox">Action</th>
                    </tr>
                </thead>

                <tbody class="dyntable">
                    <?php
                    if ($list) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptOrderID = $this->Encryption->encode($data['Booking']['id']);
                            ?>
                            <tr >
        <!--                                <td style="width:100px;">
                                <?php echo $data['Booking']['id']; ?>
                                </td>-->
                                <td><?php echo $data['User']['fname'] . " " . $data['User']['lname']; ?></td>
                                <td style="width:100px;">
                                    <?php echo $data['Booking']['number_person']; ?></td>
                                <td><?php
                                    $book_date = $this->Common->storeTimeFormateUser($data['Booking']['reservation_date'], true);
                                    $general_time = explode(' ', $book_date);
                                    echo $general_time[0];
                                    ?></td>
                                <td>
                                    <?php echo $general_time[1] . " " . @$general_time[2]; ?></td>

                                <td><?php echo $data['BookingStatuse']['name']; ?></td>
                                <td><?php echo ($data['Booking']['is_replied']) ? 'Yes' : 'No'; ?></td>
                                <td><?php echo ($data['Booking']['admin_comment']) ? $data['Booking']['admin_comment'] : '-'; ?></td>
                                <td>
                                    <?php if ($data['Booking']['booking_status_id'] != 4) { ?>
                                        <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'bookings', 'action' => 'manageBooking', $EncryptOrderID), array('escape' => false)); ?>

                                        <?php
                                        $print_data = $data['StorePrintHistory'];
                                        if (is_array($print_data)) {
                                            $_active = 'off';
                                            if ($print_data['is_active'])
                                                $_active = 'on';
                                            if ($print_data['type'] == 3) {
                                                $_icon_name = 'print';
                                                $_icon_type = 'dinein';
                                                $_icon_alt = 'DineIn Printer';
                                                $_icon_name .= '_' . $_active . '.png';
                                                echo '<img src="/img/' . $_icon_name . '" alt="' . $_icon_alt . '" style="height:15px; cursor:pointer;" OnClick="order_print(\'' . $_icon_type . '\',' . $print_data['id'] . ');"/> ';
                                            }
                                        }
                                    }
                                    ?>
                                </td>

                            </tr>
                            <?php
                            $i++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="11" style="text-align: center;">
                                No record available
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php echo $this->Form->end(); ?>
            <div class="paging_full_numbers" id="example_paginate" style="padding-top:10px">
                <?php
                echo $this->Paginator->first('First');
                // Shows the next and previous links
                echo $this->Paginator->prev('Previous', null, null, array('class' => 'disabled'));
                // Shows the page numbers
                echo $this->Paginator->numbers(array('separator' => ''));
                echo $this->Paginator->next('Next', null, null, array('class' => 'disabled'));
                // prints X of Y, where X is current page and Y is number of pages
                //echo $this->Paginator->counter();
                echo $this->Paginator->last('Last');
                ?>
            </div>



        </div>
    </div>
</div>
<?php echo $this->Html->css('pagination'); ?>


<script>
    $(document).ready(function () {
        $("#OrderKeyword").autocomplete({
            source: "<?php echo $this->Html->url(array('controller' => 'bookings', 'action' => 'getSearchValues')); ?>",
            minLength: 3,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                    .append("<div>" + item.desc + "</div>")
                    .appendTo(ul);
        };
        $("#BookingIsReplied").change(function () {
            // var catgoryId=$("#OrderOrderStatusId").val();
            $("#AdminId").submit();
        });

        $("#OrderStatusId").change(function () {
            //var catgoryId=$("#OrderSeqmentId").val();
            $("#AdminId").submit();
        });
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            eventRender: function (event, element) {
                console.log(element);
                element.qtip({
                    content: event.title,
                    position: {
                        my: 'top center', // Position my top left...
                        at: 'top bottom', // at the bottom right of...
                        target: element
                    }
                });
            },
            navLinks: true, // can click day/week names to navigate views
            editable: false,
            eventLimit: true, // allow "more" link when too many events
            events: <?php echo $cJson; ?>
        });
    });

    function order_print(print_type, order_number) {

        var print_ip = 'localhost';
<?php if ($store['printer_location']) echo "print_ip = '" . $store['printer_location'] . "';"; ?>
        var myWindow = window.open("http://" + print_ip + ":36523/NZPrint/iof_print/" + print_type + "/" + order_number, "NZPrint",
                "width=200, height=100,titlebar=no,status=no,scrollbars=no,resizable=no,localtion=no");
        setTimeout(function () {
            myWindow.close()
            $.ajax({
                type: 'post',
                url: '/orders/getOrderListData',
                data: {},
                success: function (result) {
                    if (result) {
                        $('.updateOrdersData').html(result);
                    }
                }
            });
        }, 500);
    }



</script>