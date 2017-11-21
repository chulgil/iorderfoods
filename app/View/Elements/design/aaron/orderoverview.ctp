<div class="main-container ">
    <div class="inner-wrap menu-section clearfix">
        <?php //echo $this->Session->flash(); ?>
        <div class="left-menu">
            <?php
            $guestUserDetail = $this->Session->check('GuestUser');

            $guestUserOrderType = $this->Session->read('ordersummary.order_type');
            $userId = AuthComponent::User('id');
            if (empty($userId) && empty($guestUserDetail)) {
                echo $this->element('orderoverview/login');
            }
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
                <?php echo $this->element('orderoverview/special_comment'); ?>
                <?php echo $this->element('orderoverview/payment'); ?>
            </div>
        </div>

        <div class="right-menu">
            <div class="my-order order-overview">
                <div class="common-title">
                    <h3>ORDER OVERVIEW</h3>
                </div>
                <div class="order-list-section Od-list editable-form" id="checkDeliverType">
                    <?php echo $this->element('design/oldlayout/element/order-element-calculation'); ?>
                </div>
            </div>
            <div class="continue">
                <?php
                if (AuthComponent::User()) {
                    if (!empty($storeSetting['StoreSetting']['save_to_order_btn'])) {
                        ?>
                        <div id="desktop_save" class="oo-main-btn">
                            <?php echo $this->Form->button('SAVE TO ORDERS', array('type' => 'button', 'class' => 'cont-btn theme-bg-1', 'style' => 'float:right;')); ?>
                        </div>
                        <div id="mobile_save" class="oo-main-btn">
                            <?php echo $this->Form->button('SAVE FOR LATER', array('type' => 'button', 'class' => 'cont-btn theme-bg-1', 'style' => 'float:right;')); ?>
                        </div>
                        <?php
                    }
                }
                ?>
                <div id="desktop_continue" class="oo-main-btn">
                    <?php echo $this->Html->link('CONTINUE SHOPPING', array('controller' => 'products', 'action' => 'items', $encrypted_storeId, $encrypted_merchantId), array('class' => 'cont-btn theme-bg-1')); ?>
                </div>

                <?php
                $guestUserDetail = $this->Session->check('GuestUser');
                $userId = AuthComponent::User('id');
                if ((!empty($userId) || !empty($guestUserDetail))) {
                    ?>
                    <!-- ===== Payment selection Form  start ===== -->

                    <!-- ===== Express check-out form start ===== -->
                    <div id="paypal-express-btn" style="display: none" class="oo-main-btn">
                        <!-- ===== Express check-out Payment Button ===== -->
                        <?php
                        echo $this->Html->link('PAYMENT', array('controller' => 'payments', 'action' => 'express_checkout'), array('class' => 'theme-bg-1 cont-btn'));
                        ?>
                        <!-- ===== Express check-out Payment Button End===== -->
                    </div>
                    <div id="pay-credit-card" class="oo-main-btn">
                        <!-- ===== Express check-out Payment Button ===== -->
                        <?php
                        echo $this->Form->button('PAYMENT', array('class' => 'changeName theme-bg-1 cont-btn'));
                        ?>
                        <!-- ===== Express check-out Payment Button End===== -->
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>
</div>
<div class="modal fade add-info" id="address-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', '#express-check-out', function () {
            $("#pay-credit-card").css('display', 'none')
            $("#paypal-express-btn").show();
        });
        $(document).on('click', '#payment2', function () {
            $("#pay-credit-card").show()
            $("#paypal-express-btn").css('display', 'none');
            $('button.changeName').text('PLACE ORDER');
        });
        $(document).on('click', '#payment', function () {
            $("#pay-credit-card").show()
            $("#paypal-express-btn").css('display', 'none');
            $('button.changeName').text('PAYMENT');
        });
        $(document).on('click', 'button.changeName', function () {
            var specialComment = $('#UserComment').val();
            if (specialComment != '') {
                $.ajax({
                    type: 'post',
                    url: "<?php echo $this->Html->url(array('controller' => 'payments', 'action' => 'saveSpecialComment')); ?>",
                    data: {'specialComment': specialComment},
                    beforeSend: function () {
                        $.blockUI({css: {
                                border: 'none',
                                padding: '15px',
                                backgroundColor: '#000',
                                '-webkit-border-radius': '10px',
                                '-moz-border-radius': '10px',
                                opacity: .5,
                                color: '#fff'
                            }});
                    },
                    complete: function () {
                        $.unblockUI();
                    },
                    success: function (successResult) {
                        data = JSON.parse(successResult);
                        $("#errorPop").modal('show');
                        $("#errorPopMsg").html(data.msg);
                    }
                });
            }
        });
    });
</script>
