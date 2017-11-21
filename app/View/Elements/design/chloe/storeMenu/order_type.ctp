<?php
$osDetail = $this->Session->read('ordersummary');
if (!empty($osDetail['pickup_date'])) {
    ?>
    <input type="hidden" id="osPickupDate" value="<?php echo $osDetail['pickup_date']; ?>"/>
    <input type="hidden" id="osPickupHour" value="<?php echo $osDetail['pickup_hour']; ?>"/>
    <input type="hidden" id="osPickupMinute" value="<?php echo $osDetail['pickup_minute']; ?>"/>
    <input type="hidden" id="osOrderType" value="<?php echo $osDetail['order_type']; ?>"/>
    <input type="hidden" id="osPreOrderType" value="<?php echo (!empty($osDetail['preorder_type'])) ? $osDetail['preorder_type'] : ''; ?>"/>
<?php } ?>
<div class="common-title grey-bg">
    <h3>SELECT ORDER TYPE</h3>
</div>

<div class=" pay-wrap select-order clearfix">
    <div class="pay-check">
        <?php if ($store_data_app['Store']['is_delivery'] == 1) { ?>
            <span>
                <input type="checkbox" id="test1" name="data[Order][type]" value="3" class="order_type">
                <label for="test1">Delivery</label>
            </span>
        <?php } if ($store_data_app['Store']['is_take_away'] == 1) { ?>
            <span>
                <input type="checkbox" id="test2" name="data[Order][type]" value="2" class="order_type">
                <label for="test2">Pick-up</label>
            </span>
            <?php
        }
        ?>
    </div>

    <div id="getOrderType">

    </div>
</div>



<script>
    $(document).ready(function () {

        $(document).on('click', '.saveOrderType', function (e) {
            e.stopImmediatePropagation();
            if ($("#OTDetail").valid()) {
                if ($("#now").is(":checked")) {
                    $("#StorePickuphour").val($("#StorePickuphour option:first").val());
                    $("#StorePickupmin").val($("#StorePickupmin option:first").val());
                }
                if ($('.pay-check').find('#test1').prop("checked")) {
<?php if (!empty($userId)) { ?>
                        var addressId = $('input[name="data[DeliveryAddress][id]"]').val();
                        if (addressId == '' || addressId == undefined) {
                            alert('Please add address.');
                            return false;
                        }
<?php } ?>
<?php if (!empty($userId)) { ?>
                        var checkAddress = $('input[name="data[DeliveryAddress][id]"]').is(":checked");
                        console.log(checkAddress);
                        if (!checkAddress) {
                            alert('Please select delivery address.');
                            return false;
                        }
<?php } ?>
                }
                var formData = $("#OTDetail").serialize();
                $.ajax({
                    type: 'post',
                    url: "<?php echo $this->Html->url(array('controller' => 'orderOverviews', 'action' => 'ajaxOrderDetailSave')); ?>",
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
                    },
                    complete: function () {
                        $.unblockUI();
                    },
                    success: function (successResult) {
                        try {
                            data = JSON.parse(successResult);
                            $("#errorPop").modal('show');
                            $("#errorPopMsg").html(data.msg);
                            /*$("#flashMessage").remove();
                             $("#deliveryAddress").append('<div class="message message-success alert alert-danger" id="flashMessage"><a href="#" data-dismiss="alert" aria-label="close" title="close pull-right">×</a> ' + data.msg + '</div>');
                             setTimeout(function () {
                             $("#flashMessage").remove()
                             }, 4000);*/
//                            $(".editAddress").trigger("click");
//                            setTimeout(function () {
//                                $("#flashError").html('<div class="message message-success alert alert-danger" id="flashMessage"><a href="#" data-dismiss="alert" aria-label="close" title="close">×</a> ' + data.msg + '</div>');
//                            }, 500);
                        } catch (e) {
                            $('#collapseTwo').html(successResult);
                            $('#collapseThree').collapse('show');
                            $(".theme-color-1").prop('disabled', false);
                            checkDeliverType();
                        }
                    }
                });

            }
            e.preventDefault();
        });


        $(document).on('change', '.order_type', function (e) {
            e.stopImmediatePropagation();
            var orderType = $(this).val();
            if (orderType && $(".order_type").is(':checked')) {
                if (orderType == 3) {
                    $("#deliveryAddress").removeClass('hidden');
                } else {
                    $("#deliveryAddress").addClass('hidden');
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
                        checkDeliverType();
                        selectedValues();
                        $.unblockUI();
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

        function selectedValues() {
            var StorePickuphour = $("#osPickupHour").val();
            var StorePickupmin = $("#osPickupMinute").val();
            var pickup_date = $("#osPickupDate").val();
            var preorder_type = $("#osPreOrderType").val();
            if (pickup_date != undefined) {
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
            }
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
        } else if (test == 2) {
            $("#test2").trigger('click');
        }


    });
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
</script>