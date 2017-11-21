<style>
    .chole-main .order-list-section{
        display:block;
    }

</style>
<div class="ext-menu <?php echo ($store_data_app['Store']['store_theme_id'] == 14) ? 'theme-bg-2' : ''; ?>">
    <div class="main-container">
        <div class="ext-menu-title">
            <h4>MENU</h4>
        </div>
    </div>
</div>
<div class="main-container ">
    <div class="inner-wrap menu-section clearfix">
        <div class="left-menu">
            <?php //echo $this->element('design/chloe/storeMenu/order_type'); ?>
            <?php
            $guestUserDetail = $this->Session->check('GuestUser');
            $guestUserOrderType = $this->Session->read('ordersummary.order_type');
            $userId = AuthComponent::User('id');
            if (empty($userId) && empty($guestUserDetail)) {
                echo $this->element('orderoverview/login');
            } else {
                ?>
                <div class="panel-group " id="accordion1">
                    <?php if (!empty($userId) && !empty($guestUserOrderType)) { ?>
                        <div class="panel panel-default">
                            <div class="panel-heading active">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion1" href="#collapseTwo">
                                        ORDER SUMMARY
                                        <span class="arrow-down">
                                            <i class="indicator fa fa-angle-down fa-2x" aria-hidden="true"></i>
                                        </span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseTwo" class="panel-collapse collapse in">
                                <?php echo $this->element('orderoverview/login_user_order_detail'); ?>
                            </div>
                        </div>
                        <?php
                    } elseif (empty($userId) && !empty($guestUserDetail) && !empty($guestUserOrderType)) {
                        $checkAddressInZone = $this->Session->read('Zone.id');
                        if ($guestUserOrderType == '3' && empty($checkAddressInZone)) {
                            echo $this->element('orderoverview/order_type');
                        } else {
                            ?>
                            <div class="panel panel-default">
                                <div class="panel-heading active">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion1" href="#collapseTwo">
                                            SELECT ORDER TYPE
                                            <span class="arrow-down">
                                                <i class="indicator fa fa-angle-down fa-2x" aria-hidden="true"></i>
                                            </span>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseTwo" class="panel-collapse collapse in">
                                    <?php echo $this->element('orderoverview/guest_order_detail'); ?>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo $this->element('orderoverview/order_type');
                    }
                    ?>
                </div>
            <?php } echo $this->element('design/chloe/storeMenu/list'); ?>
        </div>
        <div class="right-menu">
            <?php echo $this->Form->create('CartInfo', array('url' => array('controller' => 'Products', 'action' => 'orderDetails'))); ?>
            <div id="ordercart">
                <?php echo $this->element('design/chloe/storeMenu/cart'); ?>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>
<div class="modal fade add-info" id="address-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>