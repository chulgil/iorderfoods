<div class="title-bar">
    Check out
</div>
<div class="main-container rszn-chkt">
    <div class="inner-wrap menu-section clearfix">
        <?php //echo $this->Session->flash(); ?>
        <div class="rszn-aside">
            <div class="odr-bx odr-bx-lst">
                <h4>· ORDER OVERVIEW ·</h4>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-quantity editable-form" id="checkDeliverType">
                            <?php //echo $this->element('design/dasol/overview'); ?>
                            <?php echo $this->element('design/oldlayout/element/order-element-calculation'); ?>
                        </div>
                        <div>
                            <?php
                            $guestUserDetail = $this->Session->check('GuestUser');
                            $specialComment = $this->Session->check('Cart.comment');
                            $userId = AuthComponent::User('id');
                            if (!empty($userId) || !empty($guestUserDetail)) {
                                ?>
                                <div class="comment-box">
                                    <?php echo $this->Form->input('User.comment', array('type' => 'textarea', 'label' => false, 'placeholder' => "Special Comment", 'class' => 'form-control', 'value' => $this->Session->read('Cart.comment'))); ?>
                                </div>
                                <div class="sv-odr-ftr">
                                    <button class="btn save-order saveComment theme-bg-1" type="button"><?php echo ($specialComment) ? 'UPDATE COMMENT' : 'SAVE COMMENT'; ?></button>
                                    <?php
                                    if (!empty($storeSetting['StoreSetting']['save_to_order_btn'])) {
                                        echo $this->Form->button('SAVE TO ORDER', array('type' => 'button', 'class' => 'btn save-order', 'id' => 'desktop_save'));
                                    }
                                    ?>
                                    <?php echo $this->Html->link('CONTINUE SHOPPING', array('controller' => 'products', 'action' => 'items', $encrypted_storeId, $encrypted_merchantId), array('class' => 'go-bk-lnk save-order')); ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="rszn-content">
            <div class="odr-bx">
                <h4>· DELIVERY DETAILS ·</h4>
                <div class="address-check">
                    <div class="address" id="collapseTwo">
                        <?php
                        $guestUserDetail = $this->Session->check('GuestUser');
                        $guestUserOrderType = $this->Session->read('ordersummary.order_type');
                        $userId = AuthComponent::User('id');
                        if (empty($userId) && empty($guestUserDetail)) {
                            echo $this->element('orderoverview/login');
                        }
                        ?>
                        <?php if (!empty($userId) && !empty($guestUserOrderType)) { ?>
                            <div id="collapseTwo" class="panel-collapse collapse in">
                                <?php echo $this->element('orderoverview/login_user_order_detail'); ?>
                            </div>
                            <?php
                        } elseif (empty($userId) && !empty($guestUserDetail) && !empty($guestUserOrderType)) {
                            $checkAddressInZone = $this->Session->read('Zone.id');
                            if ($guestUserOrderType == '3' && empty($checkAddressInZone)) {
                                echo $this->element('design/dasol/order_type');
                            } else {
                                ?>
                                <div id="collapseTwo" class="panel-collapse collapse in">
                                    <?php echo $this->element('orderoverview/guest_order_detail'); ?>
                                </div>
                                <?php
                            }
                        } else {
                            echo $this->element('design/dasol/order_type');
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php if (!empty($userId) || !empty($guestUserDetail)) {
                ?>
                <div class="payment-section">
                    <div class="common-title pink-bg theme-bg-1 ">
                        <h3><span> . </span> CHECK OUT <span> . </span></h3>
                    </div>
                    <div class="pay-wrap payment-opt clearfix paymentForm">
                        <?php echo $this->element('design/dasol/payment'); ?>
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
