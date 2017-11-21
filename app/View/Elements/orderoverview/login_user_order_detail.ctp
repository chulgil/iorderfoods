<?php
$oDetail = $this->Session->read('ordersummary');
$guestUserDetail = $this->Session->check('GuestUser');
$userId = AuthComponent::User('id');
if (!empty($oDetail['order_type']) && $oDetail['order_type'] == 3 && empty($oDetail['delivery_address_id'])) {
    ?>
    <input type="hidden" id="preSelectTime" value="<?php echo $oDetail['order_type']; ?>"/>
<?php }
?>
<?php if (DESIGN == 3) { ?>
    <div class="row">
        <div class="col-sm-12 dlvr-dtl">
            <h3>Order Type</h3>
            <?php
            if (!empty($oDetail['order_type']) && $oDetail['order_type'] == 3) {
                $oType = "Delivery";
                ?>
                <a href="javascript:void(0);">Delivery</a>
                <?php
            } else {
                $oType = "Pick-up";
                ?>
                <a href="javascript:void(0);">Pick-up</a>
            <?php } ?>

        </div>
    </div>
    <div class="row">
        <div class="col-xs-6 dlvr-inf-bx">
            <strong><?php echo $oType; ?> date</strong>
            <p><?php
                if (!empty($oDetail['pickup_date'])) {
                    echo $oDetail['pickup_date'];
                }
                ?>
            </p>
        </div>
        <div class="col-xs-6 dlvr-inf-bx">
            <strong><?php echo $oType; ?> time</strong>
            <p><?php if (!empty($oDetail['pickup_date'])) { ?>
                    <?php
                    $h = @$oDetail['pickup_hour'];
                    $m = @$oDetail['pickup_minute'];
                    $date = "$h:$m:00";
                    echo date('h:i a', strtotime($date));
                }
                ?></p>
        </div>
    </div>
    <div class="row">
        <?php
        if (!empty($oDetail['order_type']) && $oDetail['order_type'] == 3) {
            $gDetail = $this->Common->getOrderDeliveryAddressUsingId();
            ?>
            <div class="col-xs-12 dlvr-inf-bx">
                <strong>DELIVERY ADDRESS</strong>
                <p>
                    <?php if (!empty($gDetail['DeliveryAddress']['name_on_bell'])) { ?>
                        Name : <?php echo ucfirst($gDetail['DeliveryAddress']['name_on_bell']); ?>
                    <?php } ?>
                </p>
                <p>
                    <?php if (!empty($gDetail['DeliveryAddress']['email'])) { ?>
                        Email : <?php echo $gDetail['DeliveryAddress']['email']; ?>
                    <?php } ?>
                </p>
                <p>
                    <?php if (!empty($gDetail['DeliveryAddress']['phone'])) { ?>
                        Phone : <?php echo $gDetail['CountryCode']['code'] . ' ' . $gDetail['DeliveryAddress']['phone']; ?>
                    <?php } ?>
                </p>
                <p>
                    <?php if (!empty($gDetail['DeliveryAddress']['address'])) { ?>
                        Address : <?php echo '<br/>' . ucfirst($gDetail['DeliveryAddress']['address']) . '</br>' . ucfirst($gDetail['DeliveryAddress']['city']) . ', ' . ucfirst($gDetail['DeliveryAddress']['state']) . ' ' . $gDetail['DeliveryAddress']['zipcode']; ?>
                    <?php } ?>
                </p>
            </div><?php
        } elseif (!empty($oDetail['order_type']) && $oDetail['order_type'] == 2) {
            if (!empty($userId) || !empty($guestUserDetail)) {
                echo $this->element('orderoverview/pickup_address');
            }
        }
        ?>
        <div class="col-xs-12 dlvr-inf-bx">
            <a href="javascript:void(0);" id="editOrderDetail"> Edit</a>
        </div>
    </div>
<?php } elseif (DESIGN == 2) { ?>
    <div class="panel-body-ch nested-ac-ch">
        <div class="pay-wrap select-order edit-delivery-details">
            <div class="pay-check">

                <div>
                    <?php if (!empty($oDetail['order_type']) && $oDetail['order_type'] == 3) { ?>
                        Order Type : Delivery
                    <?php } else { ?>
                        Order Type : Pick-up
                    <?php } ?>
                </div>                      
                <div>
                    <?php if (!empty($oDetail['pickup_date'])) { ?>
                        Order Time : <?php
                        $h = $oDetail['pickup_hour'];
                        $m = $oDetail['pickup_minute'];
                        $date = "$h:$m:00";
                        echo $oDetail['pickup_date'] . ' ' . date('h:i a', strtotime($date));
                    }
                    ?>
                </div> 
                <?php
                if (!empty($oDetail['order_type']) && $oDetail['order_type'] == 3) {
                    $gDetail = $this->Common->getOrderDeliveryAddressUsingId();
                    ?>
                    <div>
                        <?php if (!empty($gDetail['DeliveryAddress']['name_on_bell'])) { ?>
                            Name : <?php echo ucfirst($gDetail['DeliveryAddress']['name_on_bell']); ?>
                        <?php } ?>
                    </div>
                    <div>
                        <?php if (!empty($gDetail['DeliveryAddress']['email'])) { ?>
                            Email : <?php echo $gDetail['DeliveryAddress']['email']; ?>
                        <?php } ?>
                    </div>
                    <div>
                        <?php if (!empty($gDetail['DeliveryAddress']['phone'])) { ?>
                            Phone : <?php echo $gDetail['CountryCode']['code'] . ' ' . $gDetail['DeliveryAddress']['phone']; ?>
                        <?php } ?>
                    </div>
                    <div class="store-contact-info-ele">
                        <?php if (!empty($gDetail['DeliveryAddress']['address'])) { ?>
                            <strong class="mt-10 display-inline-block">Delivery Address :</strong><?php echo '<br/>' . ucfirst($gDetail['DeliveryAddress']['address']) . '<br>' . ucfirst($gDetail['DeliveryAddress']['city']) . ', ' . ucfirst($gDetail['DeliveryAddress']['state']) . ' ' . $gDetail['DeliveryAddress']['zipcode']; ?>
                    <?php } ?>
                    </div>
                    <?php
                } elseif (!empty($oDetail['order_type']) && $oDetail['order_type'] == 2) {
                    if (!empty($userId) || !empty($guestUserDetail)) {
                        echo $this->element('orderoverview/pickup_address');
                    }
                }
                ?>

            </div>
            <br/>
            <button class="cont-btn btn btn-info theme-bg-1" type="button" id="editOrderDetail">Edit</button>                    
        </div>
    </div>
<?php } else { ?>
    <div class="panel-body nested-ac">
        <div class="pay-wrap select-order edit-delivery-details clearfix">
            <div class="pay-check">

                <div>
                    <?php if (!empty($oDetail['order_type']) && $oDetail['order_type'] == 3) { ?>
                        Order Type : Delivery
                    <?php } else { ?>
                        Order Type : Pick-up
                    <?php } ?>
                </div>                      
                <div>
                    <?php if (!empty($oDetail['pickup_date'])) { ?>
                        Order Time : <?php
                        $h = $oDetail['pickup_hour'];
                        $m = $oDetail['pickup_minute'];
                        $date = "$h:$m:00";
                        echo $oDetail['pickup_date'] . ' ' . date('h:i a', strtotime($date));
                    }
                    ?>
                </div> 
                <?php
                if (!empty($oDetail['order_type']) && $oDetail['order_type'] == 3) {
                    $gDetail = $this->Common->getOrderDeliveryAddressUsingId();
                    ?>
                    <div>
                        <?php if (!empty($gDetail['DeliveryAddress']['name_on_bell'])) { ?>
                            Name : <?php echo ucfirst($gDetail['DeliveryAddress']['name_on_bell']); ?>
                        <?php } ?>
                    </div>
                    <div>
                        <?php if (!empty($gDetail['DeliveryAddress']['email'])) { ?>
                            Email : <?php echo $gDetail['DeliveryAddress']['email']; ?>
                        <?php } ?>
                    </div>
                    <div>
                        <?php if (!empty($gDetail['DeliveryAddress']['phone'])) { ?>
                            Phone : <?php echo $gDetail['CountryCode']['code'] . ' ' . $gDetail['DeliveryAddress']['phone']; ?>
                        <?php } ?>
                    </div>
                    <div class="store-contact-info-ele">
                        <?php if (!empty($gDetail['DeliveryAddress']['address'])) { ?>
                            <strong class="mt-10 display-inline-block">Delivery Address :</strong><?php echo '<br/>' . ucfirst($gDetail['DeliveryAddress']['address']) . '<br>' . ucfirst($gDetail['DeliveryAddress']['city']) . ', ' . ucfirst($gDetail['DeliveryAddress']['state']) . ' ' . $gDetail['DeliveryAddress']['zipcode']; ?>
                    <?php } ?>
                    </div>
                    <?php
                } elseif (!empty($oDetail['order_type']) && $oDetail['order_type'] == 2) {
                    if (!empty($userId) || !empty($guestUserDetail)) {
                        echo $this->element('orderoverview/pickup_address');
                    }
                }
                ?>

            </div>
            <button class="cont-btn btn btn-info theme-bg-1" type="button" id="editOrderDetail">Edit</button>                    
        </div>
    </div>
<?php } ?>
<script>
    $(document).on('click', '#editOrderDetail', function (e) {
        e.stopImmediatePropagation();
        $.ajax({
            type: 'post',
            url: "<?php echo $this->Html->url(array('controller' => 'orderOverviews', 'action' => 'editLoginUserOrderDetail')); ?>",
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
            success: function (result) {
                if (result) {
                    $('#collapseTwo').html(result);
                }
            }
        });
    });
    //$(document).one('ready', function () {
    var orderValue = $("#preSelectTime").val();
    if (orderValue == 3) {
        $("#editOrderDetail").trigger('click');
    } else {
        $(".store-contact-info-ele").removeClass('hidden');
    }
    //});
</script>