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
                    $h = $oDetail['pickup_hour'];
                    $m = $oDetail['pickup_minute'];
                    $date = "$h:$m:00";
                    echo date('h:i a', strtotime($date));
                }
                ?></p>
        </div>
    </div>
    <div class="row">
        <?php
        $gDetail = $this->Session->read('GuestUser');
        ?>
        <div class="col-xs-12 dlvr-inf-bx">
            <strong>Other Details</strong>
            <?php if (!empty($gDetail['name'])) { ?>
                <p>Name : <?php echo ucfirst($gDetail['name']); ?></p>
            <?php } ?>
            <?php if (!empty($gDetail['email'])) { ?>
                <p>Email : <?php echo $gDetail['email']; ?></p>
            <?php } ?>
            <?php if (!empty($gDetail['userPhone'])) { ?>
                <p>Phone : <?php echo $gDetail['countryCode'] . ' ' . $gDetail['userPhone']; ?></p>
            <?php } ?>
            <?php if (!empty($oDetail['address']) && !empty($oDetail['order_type']) && $oDetail['order_type'] == 3) { ?>
                <p>Delivery Address : <?php echo ucfirst($oDetail['address']) . ',' . ucfirst($oDetail['city']) . ',' . ucfirst($oDetail['state']) . ',' . $oDetail['zipcode']; ?></p>
            <?php } ?>
        </div>
        <div class="col-xs-12 dlvr-inf-bx">
            <a href="javascript:void(0);" id="editOrderDetail"> Edit</a>
        </div>
    </div>
<?php } else { ?>
    <div class="panel-body nested-ac">
        <div class=" pay-wrap select-order clearfix">
            <div class="pay-check">
                <div>
                    <?php if (!empty($oDetail['order_type']) && $oDetail['order_type'] == 3) { ?>
                        Order Type : Delivery
                    <?php } elseif (!empty($oDetail['order_type']) && $oDetail['order_type'] == 2) { ?>
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
                $gDetail = $this->Session->read('GuestUser');
                ?>
                <div>
                    <?php if (!empty($gDetail['name'])) { ?>
                        Name : <?php echo ucfirst($gDetail['name']); ?>
                    <?php } ?>
                </div>
                <div>
                    <?php if (!empty($gDetail['email'])) { ?>
                        Email : <?php echo $gDetail['email']; ?>
                    <?php } ?>
                </div>
                <div>
                    <?php if (!empty($gDetail['userPhone'])) { ?>
                        Phone : <?php echo $gDetail['countryCode'] . ' ' . $gDetail['userPhone']; ?>
                    <?php } ?>
                </div>

                <?php if (!empty($oDetail['address']) && !empty($oDetail['order_type']) && $oDetail['order_type'] == 3) { ?>
                    <div class="store-contact-info-ele">
                        <strong class="mt-10 display-inline-block">Delivery Address :</strong> <?php echo '<br/>' . ucfirst($oDetail['address']) . '<br/>' . ucfirst($oDetail['city']) . ', ' . ucfirst($oDetail['state']) . ' ' . $oDetail['zipcode']; ?>
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
            url: "<?php echo $this->Html->url(array('controller' => 'orderOverviews', 'action' => 'editGuestOrderDetail')); ?>",
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
    var orderValue = $("#preSelectTime").val();
    if (orderValue == 3) {
        $("#editOrderDetail").trigger('click');
    } else {
        $(".store-contact-info-ele").removeClass('hidden');
    }
</script>