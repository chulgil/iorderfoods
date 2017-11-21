<style>
    .risen-hz .my-order{
        display:block;
    }
    .risen-hz .right-menu .continue .cont-btn{
        display:block;
    }

</style>
<div class="title-bar">
    Menu
</div>
<div class="main-container ">
    <div class="inner-wrap menu-section clearfix">
        <div class="left-menu">
            <?php echo $this->element('design/dasol/storeMenu/list'); ?>
        </div>
        <div class="right-menu">
            <div class="odr-bx">
                <?php
                //echo $this->element('design/dasol/storeMenu/order_type');
                $guestUserDetail = $this->Session->check('GuestUser');
                $guestUserOrderType = $this->Session->read('ordersummary.order_type');
                $userId = AuthComponent::User('id');
                if (empty($userId) && empty($guestUserDetail)) {
                    echo $this->element('orderoverview/login');
                } else {
                    ?>
                    <?php if (!empty($userId) && !empty($guestUserOrderType)) { ?>
                        <div id="collapseTwo" class="panel-collapse collapse in">
                            <?php echo $this->element('orderoverview/login_user_order_detail'); ?>
                        </div>
                        <?php
                    } elseif (empty($userId) && !empty($guestUserDetail) && !empty($guestUserOrderType)) {
                        $checkAddressInZone = $this->Session->read('Zone.id');
                        if ($guestUserOrderType == '3' && empty($checkAddressInZone)) {
                            echo $this->element('orderoverview/order_type');
                        } else {
                            ?>
                            <div id="collapseTwo" class="panel-collapse collapse in">
                                <?php echo $this->element('orderoverview/guest_order_detail'); ?>
                            </div>
                            <?php
                        }
                    } else {
                        echo $this->element('orderoverview/order_type');
                    }
                    ?>
                    <?php
//                    if (!empty($userId)) {
//                        if (!empty($guestUserOrderType)) {
//                            if ($guestUserOrderType == 3) {
//                                if ($this->Session->check("ordersummary.delivery_address_id")) {
//                                    echo $this->element('orderoverview/login_user_order_detail');
//                                } else {
//                                    echo $this->element('orderoverview/edit_login_user_order_detail');
//                                }
//                            } else {
//                                echo $this->element('orderoverview/login_user_order_detail');
//                            }
//                        } else {
//                            echo $this->element('design/chloe/order_type');
//                        }
//                    } elseif (empty($userId) && !empty($guestUserDetail) && !empty($guestUserOrderType)) {
//                        $checkAddressInZone = $this->Session->read('Zone.id');
//                        if ($guestUserOrderType == '3' && empty($checkAddressInZone)) {
//                            echo $this->element('design/dasol/guest_order_type');
//                        } else {
//                            
                    ?>
                    <?php //echo $this->element('orderoverview/guest_order_detail'); ?>
                    <?php
//                        }
//                    } else {
//                        echo $this->element('design/dasol/order_type');
//                    }
                }
                ?>
            </div>
            <?php echo $this->Form->create('CartInfo', array('url' => array('controller' => 'Products', 'action' => 'orderDetails'), 'class' => 'odr-bx odr-bx-lst')); ?>
            <div id="ordercart">
                <?php echo $this->element('design/dasol/storeMenu/cart'); ?>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>
<div class="modal fade add-info" id="address-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>