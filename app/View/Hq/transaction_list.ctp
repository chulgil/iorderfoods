<div class="col-lg-12">
    <h3>Transaction Listing</h3>
    <hr>
    <?php echo $this->Session->flash(); ?> 
    <div class="table-responsive">

        <?php
        //$encryptedStoreId = $this->Encryption->encode($storeId);
        echo $this->Form->create('Payment', array('url' => array('controller' => 'hq', 'action' => 'transactionList'), 'id' => 'AdminId', 'type' => 'post'));
        ?>
        <div class="row">
            <div class="col-lg-6">
                <?php
                $merchantList = $this->Common->getHQtransaction($merchantId);
                $storeId = '';
//                if ($this->Session->read('selectedStoreId')) {
//                    $storeId = $this->Session->read('selectedStoreId');
//                }
                echo $this->Form->input('Merchant.store_id', array('options' => $merchantList, 'class' => 'form-control', 'div' => false,'label'=>false ,'empty' => 'Please Select Store', 'default' => $storeId));
                ?>
                <span class="blue">(For Store related features, select a store to proceed.)</span>
                </br>
            </div>
            <div class="col-lg-2">
                <?php echo $this->Form->input('Segment.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => @$typeList, 'empty' => 'Select Type')); ?>
            </div>
        </div> 
        <div class="row padding_btm_20">


            <div class="col-lg-3">		     
                <?php
                $options = array('Paid' => 'Paid', 'Cash on Delivery' => 'Cash on Delivery');
                echo $this->Form->input('Payment.is_active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                ?>		
            </div>
            <div class="col-lg-2">

                <?php
                echo $this->Form->input('User.from', array('label' => false, 'type' => 'text', 'class' => 'form-control', 'maxlength' => '50', 'div' => false, 'readonly' => true, 'placeholder' => 'From'));
                //echo $this->Form->input('Merchant.store_id', array('type' => 'hidden', 'value' => $storeId));
                ?>
            </div>&nbsp;&nbsp;
            <div class="col-lg-2">

                <?php
                echo $this->Form->input('User.to', array('label' => false, 'type' => 'text', 'class' => 'form-control', 'maxlength' => '50', 'div' => false, 'readonly' => true, 'placeholder' => 'To'));
                ?>
            </div>
             <div class="col-lg-3">		     
                <?php echo $this->Form->input('User.search', array('value' => $keyword, 'label' => false, 'div' => false, 'placeholder' => 'Keyword Search', 'class' => 'form-control')); ?>
                <span class="blue">(<b>Search by:</b>Order Id, Transaction Id)</span>
            </div>
            <div class="col-lg-6">		 
                <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                <?php echo $this->Html->link('Clear', array('controller' => 'hq', 'action' => 'transactionList', 'clear'), array('class' => 'btn btn-default')); ?>
                
                <?php echo $this->Html->link('Download Excel', array('controller' => 'hq', 'action' => 'exportTransactionList'), array('class' => 'btn btn-default')); ?>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
        <?php   echo $this->element('show_pagination_count'); ?>
        <table class="table table-bordered table-hover table-striped tablesorter">
            <thead>
                <tr>	    
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Order.order_number', 'Order Id'); ?></th>
                     <th  class="th_checkbox">Store Name</th>
                    <th  class="th_checkbox">Order Type</th>
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
                if (isset($list) && !empty($list)) {
                    $i = 0;
                    foreach ($list as $key => $data) {
                        $class = ($i % 2 == 0) ? ' class="active"' : '';
                        $EncryptOrderID = $this->Encryption->encode($data['OrderPayment']['order_id']);
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
                                    echo $this->Html->link($data['Order']['order_number'], array('controller' => 'hqcustomers', 'action' => 'customerOrderDetail', $EncryptOrderID));
                                } else {
                                    echo "-";
                                }
                                ?></td>
                            <td><?php echo $data['Store']['store_name']; ?></td>
                            <td><?php 
                            if(!empty($data['Order']['Segment']['name']))
                            echo ($data['Order']['Segment']['name'])?$data['Order']['Segment']['name']:'-'; 
                            
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
                                    echo $this->Html->link($coupon_amount, array('controller' => 'hqcustomers', 'action' => 'customerOrderDetail', $EncryptOrderID)) . '<br/>';
                                    $showcount++;
                                }
                                
                                if($promotionCount > 0)
                                {
                                    echo $this->Html->link('Promotions', array('controller' => 'hq', 'action' => 'offerUsedDetail', $EncryptOrderID)) . '<br/>';
                                    $showcount++;
                                }
                                
                                if($extendedOffersCount > 0)
                                {
                                    echo $this->Html->link('Extended Offers', array('controller' => 'hq', 'action' => 'orderItemOfferUsedDetail', $EncryptOrderID)) . '<br/>';
                                    $showcount++;
                                }
                                if($showcount == 0)
                                {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td><?php echo $this->Common->amount_format($data['OrderPayment']['amount']-$data['Order']['coupon_discount']); ?></td>
                            <td><?php echo $this->Dateform->us_format($data['OrderPayment']['created']); ?></td>
                            <td><?php echo $data['OrderPayment']['payment_gateway']; ?></td>
                            <td><?php echo $data['OrderPayment']['payment_status']; ?></td>
                            <td>
                                <?php
                                $sReason = $data['OrderPayment']['response'];
                                if ($sReason) {
                                    if ($data['OrderPayment']['user_id'] == 0) {
                                        $sReason .= '</br>Non-members Payment';
                                    }
                                } else {
                                    $sReason = "NA";
                                }
                                echo $sReason;
                                ?>
                            </td>
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
<?php echo $this->Html->css('pagination'); ?>
<script>
    $(document).ready(function () {
         var storeId=$('#MerchantStoreId').val();
         $("#UserSearch").autocomplete({
            source: "/hq/getTransectionSearchValues?storeID="+storeId,
            minLength: 3
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                    .append("<div>" + item.desc + "</div>")
                    .appendTo(ul);
        };
        $("#MerchantStoreId,#SegmentId").change(function () {
            var StoreId = $("#MerchantStoreId").val();
            //if(StoreId!="") {
            //alert($(this).val());
            $("#AdminId").submit();
            //}
        });

    });
</script>
<script>
    $(document).ready(function () {
        $("#PaymentIsActive").change(function () {
            var transactionId = $("#PaymentIsActive").val();
            $("#AdminId").submit();
        });

    });
    $('#UserFrom').datepicker({
        dateFormat: 'mm-dd-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '1950:2015',
    });
    $('#UserTo').datepicker({
        dateFormat: 'mm-dd-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '1950:2015',
    });
</script>
