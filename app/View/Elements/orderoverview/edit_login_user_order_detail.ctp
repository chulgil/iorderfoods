<?php
$guestUserDetail = $this->Session->check('GuestUser');
$userId = AuthComponent::User('id');
$osDetail = $this->Session->read('ordersummary');
?>
<input type="hidden" id="osPickupDate" value="<?php echo @$osDetail['pickup_date']; ?>"/>
<input type="hidden" id="osPickupHour" value="<?php echo @$osDetail['pickup_hour']; ?>"/>
<input type="hidden" id="osPickupMinute" value="<?php echo @$osDetail['pickup_minute']; ?>"/>
<input type="hidden" id="osOrderType" value="<?php echo @$osDetail['order_type']; ?>"/>
<input type="hidden" id="osPreOrderType" value="<?php echo @$osDetail['preorder_type']; ?>"/>
<?php if (DESIGN == 2) { ?>
    <div class="panel-body-ch nested-ac-ch">
        <div class=" pay-wrap select-order edit-delivery-details">
            <?php echo $this->Form->create('Order', array('id' => 'eLoginUserDetail')); ?>
        <?php } else { ?>
            <div class="panel-body nested-ac">
                <div class=" pay-wrap select-order edit-delivery-details clearfix">
                    <?php echo $this->Form->create('Order', array('id' => 'eLoginUserDetail', 'class' => "clearfix")); ?>
                <?php } ?>
                <div class="pay-check">
                    <?php if (!empty($store_data_app['Store']['is_delivery']) && $store_data_app['Store']['is_delivery'] == 1) { ?>
                        <span>
                            <input type="checkbox" id="test1" name="data[Order][type]" value="3" class="order_type">
                            <label for="test1">Delivery</label>
                        </span>
                    <?php } if (!empty($store_data_app['Store']['is_delivery']) && $store_data_app['Store']['is_take_away'] == 1) { ?>
                        <span>
                            <input type="checkbox" id="test2" name="data[Order][type]" value="2" class="order_type">
                            <label for="test2">Pick-up</label>
                        </span>
                    <?php } ?>
                    <span id="getOrderType" class="hidden chk-span">
                    </span>
                    <?php
                    if (!empty($userId) || !empty($guestUserDetail)) {
                        echo $this->element('orderoverview/pickup_address');
                    }
                    ?>
                    <div class="hidden" id="deliveryAddress">
                        <?php
                        echo $this->element('orderoverview/delivery_address');
                        ?>
                    </div>
                </div>
                <button class="editOrderType cont-btn btn btn-info theme-bg-1" type="button">Save</button> 
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
        <script>
            function refreshCart() {
                $.ajax({
                    type: 'post',
                    url: '/Products/refreshCart',
                    async: false,
                    success: function (result) {
                        if (result) {
                            $('#ordercart').html(result);
                        }
                    }
                });
            }
            $(document).ready(function () {
                function checkDeliverType() {
                    $.ajax({
                        url: "<?php echo $this->Html->url(array('controller' => 'orderOverviews', 'action' => 'checkDeliverType')); ?>",
                        type: "Post",
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
                        success: function (result) {
                            if (result) {
                                $('#checkDeliverType').html(result);
                            }
                        },
                        complete: function () {
                            $.unblockUI();
                        }
                    });
                }
                $(document).on('click', '.order_type', function (e) {
                    e.stopImmediatePropagation();
                    var orderType = $(this).val();
                    if (orderType && $(".order_type").is(':checked')) {
                        if (orderType == 3) {
                            $("#deliveryAddress").removeClass('hidden');
                            $(".store-contact-info-ele").addClass('hidden');
                        } else {
                            $("#deliveryAddress").addClass('hidden');
                            $(".store-contact-info-ele").removeClass('hidden');
                        }
                        $.ajax({
                            url: "<?php echo $this->Html->url(array('controller' => 'orderOverviews', 'action' => 'getDateTime')); ?>",
                            type: "Post",
                            data: {orderType: orderType},
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
                            success: function (result) {
                                if (result) {
                                    $('#getOrderType').html(result);
                                }
                            },
                            complete: function () {
                                selectedValues();
                                checkDeliverType();
                            }
                        });
                    } else {
                        $('#getOrderType').html('');
                    }

                    if (this.checked) {
                        $('.order_type').not((this)).prop('checked', false);
                    } else {
                        $("#deliveryAddress").addClass('hidden');
                    }
                });



                $("#eLoginUserDetail").validate({
                    debug: false,
                    errorClass: "error",
                    errorElement: 'span',
                    onkeyup: false,
                    rules: {
                        'data[Order][type]': {
                            required: true,
                        },
                        'data[pickup][type]': {
                            required: true,
                        },
                        'data[Store][pickup_date]': {
                            required: true
                        },
                        "data[Store][pickup_minute]": {
                            required: true,
                        },
                        "data[Store][pickup_hour]": {
                            required: true,
                        },
                        "data[DeliveryAddress][id]": {
                            required: true,
                        },
                        "data[DeliveryAddress][address]": {
                            required: true,
                            minlength: 2,
                            maxlength: 50
                        },
                        "data[DeliveryAddress][city]": {
                            required: true,
                            lettersonly: true
                        },
                        "data[DeliveryAddress][state]": {
                            required: true,
                            lettersonly: true,
                        },
                        "data[DeliveryAddress][zipcode]": {
                            required: true,
                            number: true,
                            minlength: 5,
                            maxlength: 5
                        }
                    },
                    messages: {
                    }, highlight: function (element, errorClass) {
                        $(element).removeClass(errorClass);
                    }
                });
                $(document).on('click', '.editOrderType', function (e) {
                    e.stopImmediatePropagation();
                    if ($("#eLoginUserDetail").valid()) {
                        if ($("#now").is(":checked")) {
                            $("#StorePickuphour").val($("#StorePickuphour option:first").val());
                            $("#StorePickupmin").val($("#StorePickupmin option:first").val());
                        }
                        if ($("#test1").is(":checked")) {
<?php if (!empty($userId) && !empty($osDetail['order_type']) && $osDetail['order_type'] == 3) { ?>
                                var addressId = $('input[name="data[DeliveryAddress][id]"]').val();
                                if (addressId == '' || addressId == undefined) {
                                    $("#errorPop").modal('show');
                                    $("#errorPopMsg").html('Please add address.');
                                    return false;
                                }
<?php } ?>
                        }
                        if ($('.pay-check').find('#test1').prop("checked")) {
                            var checkAddress = $('input[name="data[DeliveryAddress][id]"]').is(":checked");
                            if (!checkAddress) {
                                $("#errorPop").modal('show');
                                $("#errorPopMsg").html('Please select delivery address.');
                                return false;
                            }
                        }
                        var formData = $("#eLoginUserDetail").serialize();
                        $.ajax({
                            type: 'post',
                            url: "<?php echo $this->Html->url(array('controller' => 'orderOverviews', 'action' => 'ajaxOrderDetailSave')); ?>",
                            async: false,
                            data: {'formData': formData},
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
                            }, success: function (successResult) {
                                try {
                                    data = JSON.parse(successResult);
                                    $("#errorPop").modal('show');
                                    $("#errorPopMsg").html(data.msg);
                                    /*$("#flashMessage").remove();
                                     $("#deliveryAddress").append('<div class="message message-success alert alert-danger" id="flashMessage"><a href="#" data-dismiss="alert" aria-label="close" title="close pull-right">×</a> ' + data.msg + '</div>');
                                     setTimeout(function () {
                                     $("#flashMessage").remove()
                                     }, 4000);*/
//                                    $(".editAddress").trigger("click");
//                                    setTimeout(function () {
//                                        $("#flashError").html('<div class="message message-success alert alert-danger" id="flashMessage"><a href="#" data-dismiss="alert" aria-label="close" title="close">×</a> ' + data.msg + '</div>');
//                                    }, 500);
                                } catch (e) {
                                    $('#collapseTwo').html(successResult);
                                    $('#collapseThree').collapse('show');
                                    $(".theme-color-1").prop('disabled', false);
                                    refreshCart();
                                    checkDeliverType();
                                }
                            }, complete: function () {
                                $.unblockUI();
                            },
                        });
                    }
                    e.preventDefault();
                });

                function selectedValues() {
                    var StorePickuphour = $("#osPickupHour").val();
                    var StorePickupmin = $("#osPickupMinute").val();
                    var pickup_date = $("#osPickupDate").val();
                    var preorder_type = $("#osPreOrderType").val();
                    setTimeout(function () {
                        $('#StorePickupDate').datepicker().datepicker("setDate", pickup_date);
                        $('#StorePickupDate').datepicker().change();
                    }, 500);
                    setTimeout(function () {
                        $("#StorePickuphour").val(StorePickuphour);
                        $('#StorePickuphour').trigger('change');
                        $("#StorePickupmin").val(StorePickupmin);
                        $("#getOrderType").removeClass('hidden');
                        if ($("#StorePickupmin").val() === "" || $("#StorePickupmin").val() == null) {
                            $("#StorePickupmin").val($("#StorePickupmin option:first").val());
                        }
                        $.unblockUI();
                    }, 1000);
                    if (preorder_type == 1) {
                        $('#pre-order').prop('checked', true);
                        $('.pay-date').removeClass("hidden");
                    } else {
                        $('#now').prop('checked', true);
                        $('.pay-date').addClass("hidden");
                    }
                }
                var test = $("#osOrderType").val();
                if (test == 3) {
                    $("#test1").trigger('click');
                    $(".store-contact-info-ele").addClass('hidden');
                } else {
                    $("#test2").trigger('click');
                    $(".store-contact-info-ele").removeClass('hidden');
                }
            });
            // To Show
            $(document).on('click', '#pre-order', function (e) {
                $('.pay-date').removeClass("hidden");
            });

            // To hide
            $(document).on('click', '#now', function (e) {
                $('.pay-date').addClass("hidden");
            });
            if ($('#now').is(':checked')) {
                $('.pay-date').addClass("hidden");
            }

            if (!$(".order_type").is(':checked')) {
                $(".saveOrderType").addClass("hidden");
            }
        </script>