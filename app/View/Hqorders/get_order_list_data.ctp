<table class="table table-bordered table-hover table-striped tablesorter">
    <thead>
        <tr>	    
            <th  class="th_checkbox"><input type="checkbox" id="selectall"/></th>
            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.order_number', 'Order No.'); ?></th>
            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.store_name', 'Store Name'); ?></th>
            <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.fname', 'Customer Name'); ?></th> 
            <th  class="th_checkbox">Items</th>
            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.amount', 'Amount'); ?><span>&nbsp;&nbsp;($)</span></th>
            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.tax_price', 'Tax'); ?><span>&nbsp;($)</span></th>
            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.tip', 'Tip'); ?><span>&nbsp;($)</span></th>
            <th  class="th_checkbox"><?php echo $this->Paginator->sort('DeliveryAddress.phone', 'Phone'); ?></th>
            <th  class="th_checkbox"><?php echo $this->Paginator->sort('DeliveryAddress.address', 'Address'); ?></th>		
<!--			<th  class="th_checkbox"><?php //echo $this->Paginator->sort('User.email', 'Email');            ?></th>-->
           <!--<th  class="th_checkbox">Delivery/Pickup Time</th>-->
            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Segment.name', 'Order Type'); ?></th>
            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.pickup_time', 'Order Date'); ?></th>
            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.created', 'Created'); ?></th>
            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.order_status_id', 'Status'); ?></th>
            <th  class="th_checkbox">Action</th>	
        </tr>
    </thead>

    <tbody class="dyntable">
        <?php
        if ($list) {
            $i = 0;
            foreach ($list as $key => $data) {
                $class = ($i % 2 == 0) ? ' class="active"' : '';
                $EncryptOrderID = $this->Encryption->encode($data['Order']['id']);
                $EncryptStore_ID = $this->Encryption->encode($data['Order']['store_id']);
                ?>
                <tr>


                    <?php if ($data['OrderStatus']['name'] == 'Delivered' OR $data['OrderStatus']['name'] == 'Picked Up' OR $data['OrderStatus']['name'] == 'Confirmed' OR $data['OrderStatus']['name'] == 'Order not processed') { ?>
                        <td style="background-color: #B5B5B5;"><?php echo $this->Form->checkbox('Order.id.' . $key, array('class' => 'case', 'value' => $data['Order']['id'], 'style' => 'float:left;')); ?></td>
                    <?php } else { ?>
                        <td style="background-color: #F1592A;"><?php echo $this->Form->checkbox('Order.id.' . $key, array('class' => 'case', 'value' => $data['Order']['id'], 'style' => 'float:left;')); ?></td>


                    <?php } ?>

                    <?php if ($data['OrderStatus']['name'] == 'Delivered' OR $data['OrderStatus']['name'] == 'Picked Up' OR $data['OrderStatus']['name'] == 'Confirmed' OR $data['OrderStatus']['name'] == 'Order not processed') { ?>
                        <td style="background-color: #B5B5B5;"><?php echo $this->Html->link($data['Order']['order_number'], array('controller' => 'hqorders', 'action' => 'orderDetail', $EncryptOrderID, $EncryptStore_ID)); ?></td>
                    <?php } else { ?>
                        <td style="background-color: #F1592A;"><?php echo $this->Html->link($data['Order']['order_number'], array('controller' => 'hqorders', 'action' => 'orderDetail', $EncryptOrderID, $EncryptStore_ID)); ?></td>


                    <?php } ?>	
                    <?php if ($data['OrderStatus']['name'] == 'Delivered' OR $data['OrderStatus']['name'] == 'Picked Up' OR $data['OrderStatus']['name'] == 'Confirmed' OR $data['OrderStatus']['name'] == 'Order not processed') { ?>
                        <td style="background-color: #B5B5B5;"><?php
                            if (!empty($data['Store']['store_name'])) {
                                echo $data['Store']['store_name'];
                            }
                            ?> </td>
                    <?php } else { ?>
                        <td style="background-color: #F1592A;"><?php
                            if (!empty($data['Store']['store_name'])) {
                                echo $data['Store']['store_name'];
                            }
                            ?> </td>
                    <?php } ?>



                    <?php if ($data['OrderStatus']['name'] == 'Delivered' OR $data['OrderStatus']['name'] == 'Picked Up' OR $data['OrderStatus']['name'] == 'Confirmed' OR $data['OrderStatus']['name'] == 'Order not processed') { ?>
                        <td style="background-color: #B5B5B5;"><?php
                            if ($data['DeliveryAddress']['name_on_bell']) {
                                echo $data['DeliveryAddress']['name_on_bell'];
                            } else {
                                echo $data['User']['fname'] . " " . $data['User']['lname'];
                            }
                            ?> </td>
                    <?php } else { ?>
                        <td style="background-color: #F1592A;"><?php
                            if ($data['DeliveryAddress']['name_on_bell']) {
                                echo $data['DeliveryAddress']['name_on_bell'];
                            } else {
                                echo $data['User']['fname'] . " " . $data['User']['lname'];
                            }
                            ?> </td>


                    <?php } ?>




                    <?php if ($data['OrderStatus']['name'] == 'Delivered' OR $data['OrderStatus']['name'] == 'Picked Up' OR $data['OrderStatus']['name'] == 'Confirmed' OR $data['OrderStatus']['name'] == 'Order not processed') { ?>
                        <td style="background-color: #B5B5B5;"><?php
                            $i = 0;
                            $items = "";
                            foreach ($data['OrderItem'] as $key => $item) {
                                if ($i == 0) {
                                    $items = $item['Item']['name'];
                                } else {
                                    $items.=", " . $item['Item']['name'];
                                }
                                $i++;
                            }
                            echo "<span title='" . $items . "'>" . substr($items, 0, 50) . "</span>";
                            ?> </td>

                    <?php } else { ?>
                        <td  style="background-color: #F1592A;"><?php
                            $i = 0;
                            $items = "";
                            foreach ($data['OrderItem'] as $key => $item) {
                                if ($i == 0) {
                                    $items = $item['Item']['name'];
                                } else {
                                    $items.=", " . $item['Item']['name'];
                                }
                                $i++;
                            }
                            echo "<span title='" . $items . "'>" . substr($items, 0, 50) . "</span>";
                            ?> </td>
                    <?php } ?>



                    <?php if ($data['OrderStatus']['name'] == 'Delivered' OR $data['OrderStatus']['name'] == 'Picked Up' OR $data['OrderStatus']['name'] == 'Confirmed' OR $data['OrderStatus']['name'] == 'Order not processed') { ?>
                        <td style="background-color: #B5B5B5;"><?php
                            if ($data['Order']['coupon_discount'] > 0) {
                                $total_amount = $data['Order']['amount'] - $data['Order']['coupon_discount'];
                                echo $total_amount;
                            } else {

                                echo $data['Order']['amount'];
                            }
                            ?></td>

                    <?php } else { ?>
                        <td  style="background-color: #F1592A;"><?php
                            if ($data['Order']['coupon_discount'] > 0) {
                                $total_amount = $data['Order']['amount'] - $data['Order']['coupon_discount'];
                                echo $total_amount;
                            } else {

                                echo $data['Order']['amount'];
                            }
                            ?></td>
                    <?php } ?>



                    <?php if ($data['OrderStatus']['name'] == 'Delivered' OR $data['OrderStatus']['name'] == 'Picked Up' OR $data['OrderStatus']['name'] == 'Confirmed' OR $data['OrderStatus']['name'] == 'Order not processed') { ?>
                        <td style="background-color: #B5B5B5;"><?php
                            if ($data['Order']['tax_price']) {
                                //$total_amount =  $data['Order']['amount'] - $data['Order']['coupon_discount'];
                                $tax = $data['Order']['tax_price'];
                                echo $tax;
                            } else {
                                echo $data['Order']['tax_price'];
                            }
                            ?></td>

                    <?php } else { ?>
                        <td  style="background-color: #F1592A;"><?php
                            if ($data['Order']['tax_price']) {
                                //$total_amount =  $data['Order']['amount'] - $data['Order']['coupon_discount'];
                                $tax = $data['Order']['tax_price'];
                                echo $tax;
                            } else {
                                echo $data['Order']['tax_price'];
                            }
                            ?></td>
                    <?php } ?>

                    <?php if ($data['OrderStatus']['name'] == 'Delivered' OR $data['OrderStatus']['name'] == 'Picked Up' OR $data['OrderStatus']['name'] == 'Confirmed' OR $data['OrderStatus']['name'] == 'Order not processed') { ?>
                        <td style="background-color: #B5B5B5;"><?php
                            if ($data['Order']['tip']) {
                                //$total_amount =  $data['Order']['amount'] - $data['Order']['coupon_discount'];
                                $tip = $data['Order']['tip'];
                                echo $tip;
                            } else {
                                echo $data['Order']['tip'];
                            }
                            ?></td>

                    <?php } else { ?>
                        <td  style="background-color: #F1592A;"><?php
                            if ($data['Order']['tip']) {
                                //$total_amount =  $data['Order']['amount'] - $data['Order']['coupon_discount'];
                                $tip = $data['Order']['tip'];
                                echo $tip;
                            } else {
                                echo $data['Order']['tip'];
                            }
                            ?></td>
                    <?php } ?> 





                    <?php if ($data['OrderStatus']['name'] == 'Delivered' OR $data['OrderStatus']['name'] == 'Picked Up' OR $data['OrderStatus']['name'] == 'Confirmed' OR $data['OrderStatus']['name'] == 'Order not processed') { ?>

                        <td style="background-color: #B5B5B5;"><?php
                            if (!empty($data['DeliveryAddress']['phone'])) {
                                echo $data['DeliveryAddress']['phone'];
                            } else {
                                echo $data['User']['phone'];
                            }
                            ?></td>

                    <?php } else { ?>
                        <td style="background-color: #F1592A;"><?php
                            if (!empty($data['DeliveryAddress']['phone'])) {
                                echo $data['DeliveryAddress']['phone'];
                            } else {
                                echo $data['User']['phone'];
                            }
                            ?></td>

                    <?php } ?>

                    <?php if ($data['OrderStatus']['name'] == 'Delivered' OR $data['OrderStatus']['name'] == 'Picked Up' OR $data['OrderStatus']['name'] == 'Confirmed' OR $data['OrderStatus']['name'] == 'Order not processed') { ?>
                        <td style="background-color: #B5B5B5;"><?php
                            if (!empty($data['DeliveryAddress']['address'])) {
                                echo $data['DeliveryAddress']['address'];
                            } else {
                                echo $data['User']['address'];
                            }

                            if ($data['Segment']['id'] == 2) {
                                echo $data['Segment']['name'];
                            }
                            ?></td>

                    <?php } else { ?>
                        <td  style="background-color: #F1592A;"><?php
                            if (!empty($data['DeliveryAddress']['address'])) {
                                echo $data['DeliveryAddress']['address'];
                            } else {
                                echo $data['User']['address'];
                            }

                            if ($data['Segment']['id'] == 2) {
                                echo $data['Segment']['name'];
                            }
                            ?></td>
                    <?php } ?>



                    <?php //  if( $data['OrderStatus']['name'] == 'Delivered' OR $data['OrderStatus']['name'] == 'Picked Up' OR $data['OrderStatus']['name'] == 'Confirmed' OR $data['OrderStatus']['name'] == 'Order not processed'){   ?>
                                                                                                <!--			<td style="background-color: #B5B5B5;">-->
                    <?php
//			if(!empty($data['DeliveryAddress']['email'])){
//			echo $data['DeliveryAddress']['email'] ;
//			}else{
//			echo $data['User']['email'] ;	
//			}
                    ?>
                    <!--                        </td>-->
                    <?php //}else{  ?>
                    <!--			<td  style="background-color: #F1592A;">-->
                    <?php
//			if(!empty($data['DeliveryAddress']['email'])){
//			echo $data['DeliveryAddress']['email'] ;
//			}else{
//			echo $data['User']['email'] ;	
//			}
                    ?>
                    <!--                        </td>-->
                    <?php //}   ?>






                    <?php
                    if ($data['OrderPayment']['payment_gateway'] == 'COD') {
                        if ($data['Order']['seqment_id'] == 3) {
                            $paymentStatus = "UNPAID";
                        } else {
                            $paymentStatus = "UNPAID";
                        }
                    } else {
                        $paymentStatus = "PAID";
                    }

                    if ($data['OrderStatus']['name'] == 'Delivered' OR $data['OrderStatus']['name'] == 'Picked Up' OR $data['OrderStatus']['name'] == 'Confirmed' OR $data['OrderStatus']['name'] == 'Order not processed') {
                        ?>
                        <td style="background-color: #B5B5B5;"><?php
                            if ($data['Order']['seqment_id'] == 3) {
                                echo "Home Delivery -" . $paymentStatus . " - " . $data['OrderPayment']['payment_gateway'];
                            } else {
                                echo "Pickup -" . $paymentStatus . " - " . $data['OrderPayment']['payment_gateway'];
                            }
                            ?>

                        <?php } else { ?>
                        <td  style="background-color: #F1592A;"><?php
                            if ($data['Order']['seqment_id'] == 3) {
                                echo "Home Delivery -" . $paymentStatus . " - " . $data['OrderPayment']['payment_gateway'];
                            } else {
                                echo "Pickup -" . $paymentStatus . " - " . $data['OrderPayment']['payment_gateway'];
                            }
                            ?>
                        <?php } ?>

                        <?php if ($data['OrderStatus']['name'] == 'Delivered' OR $data['OrderStatus']['name'] == 'Picked Up' OR $data['OrderStatus']['name'] == 'Confirmed' OR $data['OrderStatus']['name'] == 'Order not processed') { ?>
                        <td style="background-color: #B5B5B5;"><?php echo $this->Dateform->us_format($data['Order']['pickup_time']); ?>
                        <?php } else { ?>
                        <td  style="background-color: #F1592A;"><?php echo $this->Dateform->us_format($data['Order']['pickup_time']); ?>
                        <?php } ?>



                        <?php if ($data['OrderStatus']['name'] == 'Delivered' OR $data['OrderStatus']['name'] == 'Picked Up' OR $data['OrderStatus']['name'] == 'Confirmed' OR $data['OrderStatus']['name'] == 'Order not processed') { ?>
                        <td style="background-color: #B5B5B5;"><?php echo $this->Dateform->us_format($this->Hq->storeTimezone(null, $data['Order']['created'], null, $data['Order']['store_id'])); ?>
                        <?php } else { ?>
                        <td  style="background-color: #F1592A;"><?php echo $this->Dateform->us_format($this->Hq->storeTimezone(null, $data['Order']['created'], null, $data['Order']['store_id'])); ?>
                        <?php } ?>

                        <?php if ($data['OrderStatus']['name'] == 'Delivered' OR $data['OrderStatus']['name'] == 'Picked Up' OR $data['OrderStatus']['name'] == 'Confirmed' OR $data['OrderStatus']['name'] == 'Order not processed') { ?>
                        <td style="background-color: #B5B5B5;"><?php echo $data['OrderStatus']['name']; ?>
                        <?php } else { ?>
                        <td style="background-color: #F1592A;"><?php echo $data['OrderStatus']['name']; ?>


                        <?php } ?>


                        <?php if ($data['OrderStatus']['name'] == 'Delivered' OR $data['OrderStatus']['name'] == 'Picked Up' OR $data['OrderStatus']['name'] == 'Confirmed' OR $data['OrderStatus']['name'] == 'Order not processed') { ?>
                        <td style="background-color: #B5B5B5;">
                            <?php echo $this->Html->link($this->Html->image("store_admin/view.png", array("alt" => "Detail", "title" => "Detail")), array('controller' => 'orders', 'action' => 'orderDetail', $EncryptOrderID), array('escape' => false)); ?>	

                        <?php } else { ?>
                        <td style="background-color: #F1592A;">
                            <?php echo $this->Html->link($this->Html->image("store_admin/view.png", array("alt" => "Detail", "title" => "Detail")), array('controller' => 'orders', 'action' => 'orderDetail', $EncryptOrderID), array('escape' => false)); ?>

                        <?php } ?>


                        <?php
                        $print_data = $data['StorePrintHistory'];
                        if (is_array($print_data)) {
                            foreach ($print_data as $_print) {
                                $_active = 'off';
                                if ($_print['is_active'])
                                    $_active = 'on';
                                if ($_print['type'] == 1) {
                                    $_icon_name = 'print';
                                    $_icon_type = 'kitchen';
                                    $_icon_alt = 'Kitchen Printer';
                                    if ($store['is_kitchen_printer'] != 1)
                                        continue;
                                }

                                if ($_print['type'] == 2) {
                                    $_icon_name = 'receipt';
                                    $_icon_type = 'receipt';
                                    $_icon_alt = 'Receipt Printer';
                                    if ($store['is_receipt_printer'] != 1)
                                        continue;
                                }
                                $_icon_name .= '_' . $_active . '.png';
                                echo '<img src="/img/' . $_icon_name . '" alt="' . $_icon_alt . '" style="height:15px; cursor:pointer;" OnClick="order_print(\'' . $_icon_type . '\',' . $_print['id'] . ');"/> ';
                            }
                        }
                        ?>


                        <?php //if($store['is_kitchen_printer']==1){ ?>
                                                                                                <!--						 <img src="/img/print_on.png" alt="Print" style="height:15px; cursor:pointer;" OnClick="order_print(<?= $data['Order']['order_number'] ?>);"/>-->
                        <?php //} ?>

                    </td>

                </tr>
                <?php
                $i++;
            }
        }else {
            ?>
            <tr>
                <td colspan="11" style="text-align: center;">
                    No record available
                </td>
            </tr>
        <?php } if ($list) { ?>
            <tr>

                <td colspan="6">                       

                    <?php
                    echo $this->Form->input('Order.order_status_id', array('type' => 'select', 'style' => 'background-color:white;text-align:left;', 'class' => 'btn btn-default', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $statusList, 'empty' => 'Select Status'));
                    foreach ($list as $key => $data) {
                        echo $this->Form->input('Order.store_id.', array('type' => 'hidden', 'value' => $data['Order']['store_id']));
                    }
                    ?>	&nbsp;&nbsp;&nbsp;&nbsp;

                    <?php echo $this->Form->button('Update Multiple Orders', array('type' => 'submit', 'class' => 'btn btn-default', 'onclick' => 'return check();'));
                    ?>                     


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

<script>
    $(document).ready(function () {


        $("#OrderStatusId").change(function () {
            // var catgoryId=$("#OrderOrderStatusId").val();
            $("#AdminId").submit();
        });

        $("#SegmentId").change(function () {
            //var catgoryId=$("#OrderSeqmentId").val();
            $("#AdminId").submit();
        });
        $("#OrderStoreId").change(function () {
            //var catgoryId=$("#OrderSeqmentId").val();
            $("#AdminId").submit();
        });

        $("#selectall").click(function () {
            var st = $("#selectall").prop('checked');
            $('.case').prop('checked', st);

        });
        // if all checkbox are selected, check the selectall checkbox
        // and viceversa
        $(".case").click(function () {
            if ($(".case").length == $(".case:checked").length) {
                $("#selectall").attr("checked", "checked");
            } else {
                $("#selectall").removeAttr("checked");
            }

        });

    });

    function check()
    {

        var statusId = $("#OrderOrderStatusId").val();

        var fields = $(".case").serializeArray();
        if (fields.length == 0)
        {
            alert('Please select one order to proceed.');
            // cancel submit
            return false;
        }
        if (statusId == '') {
            alert('Please select status.');
            return false;
        }


    }

    function order_print(print_type, order_number) {

        var print_ip = 'localhost';
<?php if ($store['printer_location']) echo "print_ip = '" . $store['printer_location'] . "';"; ?>
        var myWindow = window.open("http://" + print_ip + ":36523/NZPrint/iof_print/" + print_type + "/" + order_number, "NZPrint",
                "width=200, height=100,titlebar=no,status=no,scrollbars=no,resizable=no,localtion=no");
        setTimeout(function () {
            myWindow.close()
            $.ajax({
                type: 'post',
                url: '/hqorders/getOrderListData',
                data: {},
                success: function (result) {
                    if (result) {
                        $('.updateOrdersData').html(result);
                    }
                }
            });
        }, 500);
    }

    function progress() {
        alert("Work in progress.");
        return false;
    }

</script>