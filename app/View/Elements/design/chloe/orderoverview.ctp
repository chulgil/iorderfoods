<div class="ext-menu <?php echo ($store_data_app['Store']['store_theme_id'] == 14) ? 'theme-bg-2' : ''; ?>">
    <div class="main-container">
        <div class="ext-menu-title">
            <h4>ORDER OVERVIEW</h4>
        </div>
    </div>
</div>
<div class="main-container">
    <div class="inner-wrap menu-section check-out clearfix">
        <div class="chlole-lft-menu">
            <div class="my-order order-overview">
                <div class="common-title grey-bg">
                    <h3>ORDER OVERVIEW</h3>
                </div>
                <div class="order-list-section Od-list editable-form" id="checkDeliverType">
                    <?php
                    //echo $this->element('design/chloe/overview'); 
                    echo $this->element('design/oldlayout/element/order-element-calculation');
                    ?>
                </div>
                <div>
                    <?php
                    if (AuthComponent::User()) {
                        if (!empty($storeSetting['StoreSetting']['save_to_order_btn'])) {
                            ?>
                            <div class="continue clearfix">
                                <div id="desktop_save">
                                    <?php echo $this->Form->button('SAVE TO ORDER', array('type' => 'button', 'class' => 'btn-primary save-order theme-bg-1')); ?>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                    <?php
                    $guestUserDetail = $this->Session->check('GuestUser');
                    $specialComment = $this->Session->check('Cart.comment');
                    $userId = AuthComponent::User('id');
                    if (!empty($userId) || !empty($guestUserDetail)) {
                        ?>
                        <div class="special-comment">
                            <p>SPECIAL COMMENT</p>
                            <div id="flashSpecialComment"></div>
                            <div class="comment-box">
                                <?php echo $this->Form->input('User.comment', array('type' => 'textarea', 'label' => false, 'class' => 'mf-text', 'value' => $this->Session->read('Cart.comment'))); ?>
                            </div>
                            <button class="btn-primary save-order saveComment theme-bg-1" type="button"><?php echo ($specialComment) ? 'UPDATE COMMENT' : 'SAVE COMMENT'; ?></button>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="chole-rgt-menu clearfix">
            <div class="common-title pink-bg theme-bg-1 ">
                <h3>DELIVERY INFORMATIONS</h3>
            </div>
            <div class="address-check">
                <div class="address" id="collapseTwo">
                    <?php
                    $guestUserDetail = $this->Session->check('GuestUser');
                    $guestUserOrderType = $this->Session->read('ordersummary.order_type');
                    //$deliveryaddress=$this->Session->read('ordersummary.order_type');
                    $userId = AuthComponent::User('id');
                    if (empty($userId) && empty($guestUserDetail)) {
                        echo $this->element('orderoverview/login');
                    }
                    ?>
                    <?php
                    //pr($this->Session->read('ordersummary'));
                    if (!empty($userId) && !empty($guestUserOrderType)) {
                        ?>
                        <div id="collapseTwo" class="panel-collapse collapse in">
                            <?php echo $this->element('orderoverview/login_user_order_detail'); ?>
                        </div>
                        <?php
                    } elseif (empty($userId) && !empty($guestUserDetail) && !empty($guestUserOrderType)) {
                        $checkAddressInZone = $this->Session->read('Zone.id');
                        if ($guestUserOrderType == '3' && empty($checkAddressInZone)) {
                            echo $this->element('design/chloe/order_type');
                        } else {
                            ?>
                            <div id="collapseTwo" class="panel-collapse collapse in">
                                <?php echo $this->element('orderoverview/guest_order_detail'); ?>
                            </div>
                            <?php
                        }
                    } else {
                        echo $this->element('design/chloe/order_type');
                    }
                    ?>
                </div>
            </div>
            <?php if (!empty($userId) || !empty($guestUserDetail)) {
                ?>
                <div class="payment-section">
                    <div class="common-title pink-bg theme-bg-1 ">
                        <h3>CHECK OUT</h3>
                    </div>
                    <div class="pay-wrap payment-opt clearfix paymentForm">
                        <?php echo $this->element('design/chloe/payment'); ?>
                    </div>
                </div>
            <?php } ?>
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
                });
            }
        });
    });
    $(document).on('click', '.saveComment', function () {
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
</script>