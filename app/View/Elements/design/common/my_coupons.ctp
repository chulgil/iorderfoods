<style>
    .tab-form{
        border: 1px solid #e2e2e2;
        padding: 24px 0;
    }
</style>
<?php $storeId = $this->Session->read('store_id'); ?>
<div class="title-bar">My Coupons</div>
<div class="main-container">
    <div class="ext-menu-title">
        <h4><?php echo __('My Coupons'); ?></h4>
    </div>
    <div class="inner-wrap mycoupons no-border">
        <?php //echo $this->Session->flash(); ?>
        <div class="form-section">
            <?php echo $this->Form->create('Coupon', array('url' => array('controller' => 'coupons', 'action' => 'myCoupons'), 'id' => 'AdminId', 'type' => 'post', 'class' => 'clearfix tab-form')); ?>
            <?php echo $this->element('userprofile/filter_store'); ?>
            <div class="col-lg-4 col-sm-4 search-btm-btn">
                <div class="row">
                    <div class="col-lg-6 col-sm-6 col-xs-6">
                        <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'srch-btn theme-bg-1')); ?>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-xs-6">
                        <?php echo $this->Html->link('Clear', array('controller' => 'coupons', 'action' => 'myCoupons', 'clear'), array('class' => 'clr-link theme-bg-2')); ?>
                    </div>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <div class="inner-div my-coupon-tbl-wrap clearfix">
                <div class="pagination-section clearfix">
                    <?php echo $this->element('pagination'); ?>
                </div>
                <div class="responsive-table">
                    <table class="table table-striped">
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
                                        echo $coupon['Coupon']['name'] . ' - ';
                                        if ($coupon['Coupon']['discount_type'] == 2) {
                                            echo $description = 'Use coupon code ' . $coupon['Coupon']['coupon_code'] . ' get ' . $coupon['Coupon']['discount'] . "% Off. ";
                                            //echo $description = $coupon['Coupon']['discount'] . __('%') . __(' off on MRP');
                                        } else {
                                            echo $description = 'Use coupon code ' . $coupon['Coupon']['coupon_code'] . ' get $' . $coupon['Coupon']['discount'] . " Off. ";
                                            //echo $description = __('$') . $coupon['Coupon']['discount'] . __(' off on MRP');
                                        }
                                        ?></td>
                                    <td>
                                        <?php
                                        if (!empty($coupon['Coupon']['promotional_message'])) {
                                            echo $coupon['Coupon']['promotional_message'];
                                        } else {
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
                                        } else {
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
                                        } else {
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
                                            } else {
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
                <?php echo $this->element('pagination'); ?>
            </div>
        </div>
    </div>
</div>
<?php echo $this->Html->css('pagination'); ?>
<script>
    $(document).ready(function () {
        $("#MerchantStoreId").change(function () {
            var StoreId = $("#MerchantStoreId").val();
            $("#AdminId").submit();
        });
    });
</script>