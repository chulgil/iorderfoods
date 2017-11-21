<!-- static-banner -->
<?php echo $this->element('hquser/static_banner'); ?>
<!-- /banner -->
<div class="signup-form">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="common-title clearfix">
                    <span class="yello-dash"></span>
                    <h2><?php echo __('My Coupons'); ?></h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php echo $this->Session->flash(); ?>
                <div class="form-bg">
                    <!-- ORDER TABS -->
                    <div class="sign-up order-content order-content-tabs">                        
                        <!-- SEARCH -->
                        <div class="tabs-search margin-top0 clearfix">
                            <?php echo $this->Form->create('Coupon', array('url' => array('controller' => 'hqusers', 'action' => 'myCoupons'), 'id' => 'AdminId', 'type' => 'post')); ?>
                            <div class="col-2">
                                <?php
                                $merchantList = $this->Common->getStores($this->Session->read('hq_id'));
                                echo $this->Form->input('Merchant.store_id', array('options' => $merchantList, 'class' => 'inbox', 'div' => false, 'empty' => 'Please Select Store', 'label' => FALSE));
                                ?>
                            </div>
                            <div class="col-2 tab-search-right">
                                <div>
                                    <?php
                                    $val = '';
                                    if (isset($keyword) && !empty($keyword)) {
                                        $val = $keyword;
                                    }
                                    ?>
                                    <?php echo $this->Form->input('User.keyword', array('value' => $val, 'label' => false, 'div' => false, 'placeholder' => 'Search (Coupon code)', 'class' => 'inbox')); ?>
                                </div>
                                <div class="searchh-btn">
                                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn common-config black-bg')); ?>
                                    <?php echo $this->Html->link('Clear', array('controller' => 'hqusers', 'action' => 'myCoupons', 'clear'), array('class' => 'btn common-config black-bg')); ?>
                                </div>
                            </div>
                            <?php echo $this->Form->end(); ?>
                        </div>

                        <!-- PAGINATION -->
                         <?php echo $this->element('pagination');?>
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
                                                    <th><?php echo __('Coupon Code'); ?></th>
                                                    <th><?php echo __('Description'); ?></th>
                                                    <th><?php echo __('Store'); ?></th>
                                                    <th><?php echo __('Status'); ?></th>
                                                    <th><?php echo __('Action'); ?></th>
<!--                                                    <th class="width20p">Items</th>
                                                    <th class="width15p">Size</th>
                                                    <th class="width20p">Preferences</th>
                                                    <th class="width15p">Add-ons</th>
                                                    <th class="width20p">Store</th>-->
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php
                                                if (!empty($myCoupons)) {
                                                    foreach ($myCoupons as $coupon) {
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $coupon['Coupon']['coupon_code']; ?></td>
                                                            <td><?php
                                                                echo $coupon['Coupon']['name'] . ' - ';
                                                                if ($coupon['Coupon']['discount_type'] == 2) {
                                                                    echo $description = $coupon['Coupon']['discount'] . __('%') . __(' off on MRP');
                                                                } else {
                                                                    echo $description = __('$') . $coupon['Coupon']['discount'] . __(' off on MRP');
                                                                }
                                                                ?></td>
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
//                                                    if (!empty($storeId)) {
//                                                        if ($coupon['UserCoupon']['store_id'] == $storeId) {
                                                                echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-trash-o', 'title' => 'Delete')), array('controller' => 'hqusers', 'action' => 'deleteUserCoupon', $this->Encryption->encode($coupon['UserCoupon']['id'])), array('confirm' => __('Are you sure you want to delete this coupon?'), 'class' => 'delete', 'escape' => false));
//                                                        }else{
//                                                            echo "-";
//                                                        }
//                                                    }
                                                                ?> 
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                } else {
                                                    echo '<tr><td colspan="5" class="text-center">' . __('No coupon found') . '</td></tr>';
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
        $("#MerchantStoreId").change(function () {
            var StoreId = $("#MerchantStoreId").val();
            //if(StoreId!="") {
            $("#AdminId").submit();
            //}
        });

    });
</script>