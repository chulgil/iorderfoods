
<style>
    .btn{
        font-size: 14px;
    }
    .blue{
        margin-left:-269px;
        font-size: 12px
    }
    .row {height: 10px;}
</style>
    <?php 

$storeId = $this->Session->read('store_id');
?>
<div class="pad-TP60 clearfix">
    <?php //echo $this->Session->flash(); ?>
    <div class="order-hostory form-layout clearfix">
<!--        <form name="select-order-type" method="post" action="javascript:void(0);">-->
            <h2><span><?php echo __('My Coupons'); ?></span></h2>
            <div>
    <div>
        <hr>
            <?php echo $this->Form->create('Coupon', array('url' => array('controller' => 'coupons', 'action' => 'myCoupons'), 'id' => 'AdminId', 'type' => 'post')); ?>
        <div class="row">
            <?php echo $this->element('userprofile/filter_store'); ?>
          
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn green-btn')); ?>
                    <?php echo $this->Html->link('Clear', array('controller' => 'coupons', 'action' => 'myCoupons', 'clear'), array('class' => 'btn green-btn')); ?>
          
            </div>
            <?php echo $this->Form->end(); ?>
        <span class="blue">(<b>Search by:</b> Coupon Code)</span>
        </div>
</div>
            
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
            <div id="horizontalTab">
                <!-- FORM VIEW -->
                <div class="resp-tabs-container">
                    <div class="repeat-deatil">                	
                        <div class="resp-tabs-frame">
                            <div class="responsive-table">
                                <table class="table table-striped order-history-table">
                                    <tr>
                                        <th><?php echo __('Coupon Code'); ?></th>
                                        <th><?php echo __('Description'); ?></th>
                                        <th><?php echo __('Promotional Message'); ?></th>
                                        <th><?php echo __('Start Date'); ?></th>
                                        <th><?php echo __('End Date'); ?></th>
                                        <th><?php echo __('Store'); ?></th>
                                        <th><?php echo __('Status'); ?></th>
                                        <th><?php echo __('Action'); ?></th>
                                    </tr>
                                    <?php
                                    if (!empty($myCoupons)) {
                                        foreach ($myCoupons as $coupon) {
                                            ?>

                                            <tr>
                                                <td><?php echo $coupon['Coupon']['coupon_code']; ?></td>
                                                <td><?php
                                                $promotionalMsg='';
                                                    if(!empty($coupon['Coupon']['promotional_message]'])){
                                                        $promotionalMsg=$coupon['Coupon']['promotional_message]'];
                                                    }
                                                    echo $coupon['Coupon']['name'] . ' - ';
                                                    if ($coupon['Coupon']['discount_type'] == 2) {
                                                        echo $description = 'Use coupon code ' . $coupon['Coupon']['coupon_code'] . ' get ' . $coupon['Coupon']['discount'] . "% Off. ".$promotionalMsg;
                                                        //echo $description = $coupon['Coupon']['discount'] . __('%') . __(' off on MRP');
                                                    } else {
                                                        echo $description = 'Use coupon code ' . $coupon['Coupon']['coupon_code'] . ' get $' . $coupon['Coupon']['discount'] . " Off. ".$promotionalMsg;
                                                        //echo $description = __('$') . $coupon['Coupon']['discount'] . __(' off on MRP');
                                                    }
                                                    ?></td>
                                                <td>
                                        <?php
                                        if (!empty($coupon['Coupon']['promotional_message'])) {
                                            echo $coupon['Coupon']['promotional_message'];
                                        }else{
                                            echo '-';
                                        }
                                        ?> </td>
                                      <td>
                                        <?php
                                        if (!empty($coupon['Coupon']['start_date'])) {
                                            //echo $coupon['Coupon']['start_date'];
                                            $startDate = $this->Common->storeTimeFormateUser($coupon['Coupon']['start_date'], true);
                                            $startDate = explode(' ', $startDate);
                                            echo $startDate[0];
                                        }else{
                                            echo '-';
                                        }
                                        ?> </td>
                                      <td>
                                        <?php
                                        if (!empty($coupon['Coupon']['end_date'])) {
                                            //echo $coupon['Coupon']['end_date'];
                                            $endDate = $this->Common->storeTimeFormateUser($coupon['Coupon']['end_date'], true);
                                            $endDate = explode(' ', $endDate);
                                            echo $endDate[0];
                                        }else{
                                            echo '-';
                                        }
                                        ?> </td>
                                                <td>
                                                    <?php
                                                    if (!empty($coupon['Store'])) {
                                                        echo $coupon['Store']['store_name'];
                                                    }
                                                    ?> </td>
                                                <td><?php
                                                    if ($coupon['Coupon']['used_count'] >= $coupon['Coupon']['number_can_use']) {
                                                        echo __('Expired');
                                                    } else {
                                                        echo __('Active');
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php
                                                    
                                                    if (!empty($storeId)) {
                                                        if ($coupon['UserCoupon']['store_id'] == $storeId) {

                                                            echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-trash-o')) . 'Delete', array('controller' => 'coupons', 'action' => 'deleteUserCoupon', $encrypted_storeId, $encrypted_merchantId, $this->Encryption->encode($coupon['UserCoupon']['id'])), array('confirm' => __('Are you sure you want to delete this coupon?'), 'class' => 'delete', 'escape' => false));
                                                        }else{
                                                            echo "-";
                                                        }
                                                    }
                                                    ?> 
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="4" class="text-center">' . __('No coupon found') . '</td></tr>';
                                    }
                                    ?>

                                </table>
                            </div>
                        </div>
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
    </div>       
</div>
<?php echo $this->Html->css('pagination'); ?>
            
<script>
            $(document).ready(function () {
                $("#MerchantStoreId").change(function () {
                    var StoreId = $("#MerchantStoreId").val();
                    //if(StoreId!="") {
                    $("#AdminId").submit();
                    //}
                });

            });
        </script>