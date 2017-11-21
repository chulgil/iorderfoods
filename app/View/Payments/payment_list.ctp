<div class="col-lg-13">
    <h3>Transaction Listing</h3>
    <?php echo $this->Session->flash(); ?> 
    <div class="table-responsive">   
        <?php echo $this->Form->create('Payment', array('url' => array('controller' => 'payments', 'action' => 'paymentList'), 'id' => 'AdminId', 'type' => 'post')); ?>
        <div class="row padding_btm_20">


            <div class="col-lg-2">		     
                <?php
                $options = array('Paid' => 'Paid', 'Cash on Delivery' => 'Cash on Delivery');
                echo $this->Form->input('Payment.is_active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                ?>		
            </div>
            <div class="col-lg-2">

                <?php
                echo $this->Form->input('User.from', array('label' => false, 'type' => 'text', 'class' => 'form-control', 'maxlength' => '50', 'div' => false, 'readonly' => true, 'placeholder' => 'From'));
                ?>
            </div>&nbsp;&nbsp;
            <div class="col-lg-2">

                <?php
                echo $this->Form->input('User.to', array('label' => false, 'type' => 'text', 'class' => 'form-control', 'maxlength' => '50', 'div' => false, 'readonly' => true, 'placeholder' => 'To'));
                ?>
            </div>
            <div class="col-lg-2">		     
                <?php echo $this->Form->input('User.search', array('value' => $keyword, 'label' => false, 'div' => false, 'placeholder' => 'Keyword Search', 'class' => 'form-control')); ?>
                <span class="blue">(<b>Search by:</b>Order Id)</span>
            </div>
            <div class="col-lg-1">		 
                <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
            </div>
            <div class="col-lg-1">
                <?php echo $this->Html->link('Clear', array('controller' => 'payments', 'action' => 'paymentList', 'clear'), array('class' => 'btn btn-default')); ?>
            </div>
            <div class="col-lg-2" style="position: absolute; display: inline-block;">
                <?php echo $this->Html->link('Download Excel', array('controller' => 'payments', 'action' => 'exportPaymentList'), array('class' => 'btn btn-default')); ?>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
        <?php echo $this->element('show_pagination_count'); ?>
        <table class="table table-bordered table-hover table-striped tablesorter">
            <thead>
                <tr>	    
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.order_number', 'Order Id'); ?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('OrderPayment.transection_id', 'Transaction Id'); ?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('OrderPayment.amount', 'Sub Total'); ?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.tax_price', 'Tax($)'); ?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.tip', 'Tip'); ?></th>
                    <th  class="th_checkbox">Discount</th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('OrderPayment.amount', 'Total Sales Amount ($)'); ?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('OrderPayment.created', 'Date'); ?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('OrderPayment.payment_gateway', 'Payment Type'); ?></th>
                    <th  class="th_checkbox">Payment Status</th>
                    <th  class="th_checkbox">Reason</th>
                    <th  class="th_checkbox">Response Code</th>

            </thead>

            <tbody class="dyntable">
                <?php
                if ($list) {
                    $i = 0;
                    foreach ($list as $key => $data) {
                        $class = ($i % 2 == 0) ? ' class="active"' : '';
                        $EncryptOrderID = $this->Encryption->encode($data['OrderPayment']['order_id']);
                        $EncryptPaymentID = $this->Encryption->encode($data['OrderPayment']['id']);
                        $orderDetail = $this->Common->orderItemDetail($data['OrderPayment']['order_id']);
                        $totalItemPrice = 0;
                        if($orderDetail)
                        {
                            foreach ($orderDetail as $itemKey => $itemVal)
                            {
                                if ($itemVal['OrderItem']['total_item_price']) {
                                    $totalItemPrice += $itemVal['OrderItem']['total_item_price'];
                                }
                            }
                        }
                        $promotionCount = $this->Common->usedOfferDetailCount($data['OrderPayment']['order_id']);
                        
                        $extendedOffersCount = $this->Common->usedItemOfferDetailCount($data['OrderPayment']['order_id']);
                        ?>
                        <tr <?php echo $class; ?>>	    
                            <td><?php
                                if (!empty($data['Order']['order_number'])) {

                                    echo $this->Html->link($data['Order']['order_number'], array('controller' => 'payments', 'action' => 'orderDetail', $EncryptOrderID, $EncryptPaymentID));
                                } else {
                                    echo "-";
                                }
                                ?></td>
                            <td><?php echo $data['OrderPayment']['transection_id']; ?></td>
                            <td><?php echo $this->Common->amount_format($totalItemPrice); ?></td>
                            <td><?php echo $this->Common->amount_format($data['Order']['tax_price']); ?></td>
                            <td><?php echo ($data['Order']['tip'] && $data['Order']['tip'] > 0) ? $this->Common->amount_format($data['Order']['tip']) : "-"; ?></td>
                            <td>
                                <?php
                                $showcount = 0;
                                if($data['Order']['coupon_code'] != null)
                                {
                                    $coupon_amount = $this->Common->amount_format($data['Order']['coupon_discount']);
                                    echo $this->Html->link($coupon_amount, array('controller' => 'payments', 'action' => 'orderDetail', $EncryptOrderID, $EncryptPaymentID)) . '<br/>';
                                    $showcount++;
                                }
                                
                                if($promotionCount > 0)
                                {
                                    echo $this->Html->link('Promotions', array('controller' => 'offers', 'action' => 'offerUsedDetail', $EncryptOrderID)) . '<br/>';
                                    $showcount++;
                                }
                                
                                if($extendedOffersCount > 0)
                                {
                                    echo $this->Html->link('Extended Offers', array('controller' => 'itemOffers', 'action' => 'orderItemOfferUsedDetail', $EncryptOrderID)) . '<br/>';
                                    $showcount++;
                                }
                                if($showcount == 0)
                                {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td><?php echo $this->Common->amount_format(($data['OrderPayment']['amount'] - $data['Order']['coupon_discount'])); ?></td>
                            <td><?php echo $this->Dateform->us_format($this->Common->storeTimezone('', $data['OrderPayment']['created'])); ?></td>
                            <td><?php echo $data['OrderPayment']['payment_gateway']; ?></td>

                            <td><?php echo $data['OrderPayment']['payment_status']; ?></td>
                            <td><?php
                                $sReason = $data['OrderPayment']['response'];
                                if ($sReason) {
                                    if ($data['OrderPayment']['user_id'] == 0) {
                                        $sReason .= '</br>Non-members Payment';
                                    }
                                } else {
                                    $sReason = "-";
                                }
                                echo $sReason;
                                ?></td>
                            <td><?php echo ($data['OrderPayment']['response_code']) ? $data['OrderPayment']['response_code'] : "-"; ?></td>


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
<style>
    .ui-autocomplete {
        max-height: 200px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
    }
    /* IE 6 doesn't support max-height
     * we use height instead, but this forces the menu to always be this tall
     */
    * html .ui-autocomplete {
        height: 200px;
    }
    .paging_full_numbers {
        margin-top: 5px;
        float:right;
    }
    .paging_full_numbers .paginate_button {
        background: url("images/buttonbg5.png") repeat-x scroll left top #EEEEEE;
        border: 1px solid #CCCCCC;
        border-radius: 3px;
        cursor: pointer;
        display: inline-block;
        margin-left: 5px;
        padding: 2px 8px;
    }
    .paging_full_numbers .paginate_button:hover {
        background: none repeat scroll 0 0 #EEEEEE;
        box-shadow: 1px 1px 2px #CCCCCC inset;
    }
    .paging_full_numbers .paginate_active, .paging_full_numbers .paginate_button:active {
        background: url("images/buttonbg3.png") repeat-x scroll left top #405A87;
        border: 1px solid #405A87;
        border-radius: 3px;
        color: #FFFFFF;
        display: inline-block;
        margin-left: 5px;
        padding: 2px 8px;
    }
    .paging_full_numbers .paginate_button_disabled {
        color: #999999;
    }
    .paging_full_numbers span {
        background: url("images/buttonbg5.png") repeat-x scroll left top #EEEEEE;
        border: 1px solid #CCCCCC;
        border-radius: 3px;
        cursor: pointer;
        display: inline-block;
        margin-left: 5px;
        padding: 2px 8px;
    }
    .paging_full_numbers span:hover {
        background: none repeat scroll 0 0 #EEEEEE;
        box-shadow: 1px 1px 2px #CCCCCC inset;
    }
    .paging_full_numbers span:active {
        background: url("images/buttonbg3.png") repeat-x scroll left top #405A87;
        border: 1px solid #405A87;
        border-radius: 3px;
        color: #FFFFFF;
        display: inline-block;
        margin-left: 5px;
        padding: 2px 8px;
    }
    .paging_full_numbers .disabled {
        color: #999999;
    }
    .paging_full_numbers span a {
        color: #000000;
    }
    .pagination a {
        border-radius: 3px;
    }
    .pagination a {
        box-shadow: 1px 1px 0 #F7F7F7;
    }
    .pagination a:hover {
        background: none repeat scroll 0 0 #EEEEEE;
        box-shadow: 1px 1px 3px #EEEEEE inset;
        text-decoration: none;
    }
    .pagination a.disabled {
        border: 1px solid #CCCCCC;
        color: #999999;
    }
    .pagination a.disabled:hover {
        background: url("images/buttonbg5.png") repeat-x scroll left bottom rgba(0, 0, 0, 0);
        box-shadow: none;
    }
    .pagination a.current {
        background: url("images/buttonbg3.png") repeat-x scroll left top #333333;
        border: 1px solid #405A87;
        color: #FFFFFF;
    }
    .pagination a.current:hover {
        box-shadow: none;
    }
    .pgright {
        position: absolute;
        right: 10px;
        top: 12px;
    }
    .pgright a.disabled {
        border: 1px solid #CCCCCC;
    }
</style>

<script>
    $(document).ready(function () {
        $("#UserSearch").autocomplete({
            source: "<?php echo $this->Html->url(array('controller' => 'payments', 'action' => 'getSearchValues')); ?>",
            minLength: 3,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        });
//        $("#UserSearch").autocomplete({
//            source: "<?php echo $this->Html->url(array('controller' => 'payments', 'action' => 'getSearchValues')); ?>",
//            minLength: 3
//        }).autocomplete("instance")._renderItem = function (ul, item) {
//            return $("<li>")
//                    .append("<div>" + item.desc + "</div>")
//                    .appendTo(ul);
//        };
        $("#PaymentIsActive").change(function () {
            var transactionId = $("#PaymentIsActive").val
            $("#AdminId").submit();
        });

    });
    $('#UserFrom').datepicker({
        dateFormat: 'mm-dd-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '2010:' + new Date().getFullYear(),
        maxDate: 0,
        onSelect: function (selectedDate) {
            $("#UserTo").datepicker("option", "minDate", selectedDate);
        }
    });
    $('#UserTo').datepicker({
        dateFormat: 'mm-dd-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '2010:' + new Date().getFullYear(),
        maxDate: 0,
        onSelect: function (selectedDate) {
            $("#UserFrom").datepicker("option", "maxDate", selectedDate);
        }
    });
</script>
