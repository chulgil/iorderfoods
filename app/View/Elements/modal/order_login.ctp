<style>
    .modal-dialog {
        margin:25px auto !important;
        position:relative;
    }
    .modal-body{
        color:#000000;
    }
    .modal{
        /*        top: 32% !important; */
    }
    .order-form-layout{
        float: left;
        width: 100%;
        margin:0 auto !important;
    }

    .modal-backdrop {
        z-index: 0;
    }
    .modal-dialog label.error {
        color: #b50000;
        font-size: 12px;
        font-weight: 400;
        margin: 0;
        padding: 0;width:100%;text-align:left;
    }
    .modal-dialog .login-form ul.guest-list { float:none;margin:0 -15px;width:auto;}
    .modal-dialog .button-frame { margin-top:0;padding-bottom:0;}
    @media all and ( max-width: 680px) {
        .modal-dialog {
            width: auto;
        }
    }
    @media all and ( min-width: 700px) {
        .modal-dialog {
            width: 700px;
        }
    }
    .ordr-bx-pos{ padding-right: 0px;}
    .modal-dialog .ordr-bx-pos .orderTypePickUp, .modal-dialog .ordr-bx-pos .orderTypeDelivery{width:100%;}
    .modal-dialog label {
        color: #737373;
        font-size: 16px;
        font-weight: 400;
    }
    .nav > li.disabled > a{
        font-weight:500;
    }
    
    #chkOrderType .ordr-bx-pos #resvTime { margin-top:4px;}
    #chkOrderType .ordr-bx-pos #orderTypePickUpDate,
    #chkOrderType .ordr-bx-pos #orderTypeDeliveryDate { height:39px;}
    #chkOrderType .ordr-bx-pos div:first-of-type {float:left;margin-right:2.5%;width:49.5%;}
    #chkOrderType .ordr-bx-pos #resvTime .time-setting:first-of-type { margin-right:2%;}
    #chkOrderType .ordr-bx-pos div:first-of-type { width:45%;}
    #chkOrderType .ordr-bx-pos #resvTime .time-setting:first-of-type { margin-right:4%;}
    #chkOrderType .ordr-bx-pos div:first-child { float:left;width:50%;}
    #chkOrderType .ordr-bx-pos div:last-child { float:left;width:47%;}
    #chkOrderType #OrderTypeForm .login-form ul { margin-top:15px;}
    #chkOrderType #chkDeliveryAddress .address address.inbox { margin-bottom:0;padding-bottom:0;}
    #chkOrderType #chkDeliveryAddress .address address.inbox + span { display:none;}
    #chkOrderType #chkDeliveryAddress .address address.inbox h3 { font-size:16px;padding-bottom:5px;}
    #chkOrderType #chkDeliveryAddress .address address.inbox p { font-size:12px;line-height:18px;}
    #chkDeliveryAddress #Deliveryaddress #delivery_address li { margin-bottom:0;margin-top:5px;}
    #chkDeliveryAddress #Deliveryaddress #delivery_address li span { font-size:14px;}
    #chkDeliveryAddress #Deliveryaddress #delivery_address li span label { padding:2px 0;}
    #chkDeliveryAddress #Deliveryaddress #delivery_address li .title-box { color:#737373;font-size:13px;font-weight:500;position:relative;top:5px;}
    #chkDeliveryAddress #Deliveryaddress #delivery_address li #changeorderType { display:none;}
    #chkDeliveryAddress #Deliveryaddress .form-layout .title-box ul li,
    #chkDeliveryAddress #Deliveryaddress .form-layout .title-box ul li span.title { width:100%;}
    #chkOrderType #chkDeliveryAddress .content.single-frame { width:100% !important;}
    #chkOrderType #chkDeliveryAddress .content.single-frame li { margin-bottom:5px;}
    #chkOrderType #chkDeliveryAddress .content.single-frame input,
    #chkOrderType #chkDeliveryAddress .content.single-frame select { background-color:rgb(230,229,228);}
    #chkOrderType #chkDeliveryAddress .content .inbox.inbox.phone-number { width:63%;}
    .modal-dialog #chkOrderType #chkDeliveryAddress #guestForm .button-frame span { display:none;}
</style>
<?php $StoreDetails = $this->Common->getStoreDetails(); ?>

<div class="modal fade" id="orderLogin" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog select-modal">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
<!--                <h3 class="modal-title" id="lineModalLabel">Login</h3>-->
            </div>

            <div class="modal-body">
                <span class="errorMsg"></span>
                <div class="container" style="width:auto;">
                    <div class="row">
                        <div class="col-sm-12">
                            <ul class="nav nav-tabs">
                                <li class="chkLogin active"><a data-toggle="tab" href="#chkLogin">Login</a></li>
                                <li class="chkOrderType disabled"><a data-toggle="tab" href="#chkOrderType">Order Type</a></li>
                                <!--                                <li class="chkDeliveryAddress disabled"><a data-toggle="tab" href="#chkDeliveryAddress">Address</a></li>-->
                            </ul>

                        </div>
                    </div>



                    <div class="tab-content" id="tab1login">
                        <div id="chkLogin" class="tab-pane fade in active">
                            <!-- user login section start  -->
                            <?php echo $this->element('orderLogin/user_login') ?>
                            <!-- user login section end  -->
                            <?php if ($StoreDetails['Store']['guest_user'] == 1) { ?>
                                <div class="guest">
                                    <!-- guest section start  -->
                                    <?php echo $this->element('orderLogin/user_guest') ?>
                                    <!-- guest section end  -->
                                </div>
                            <?php } ?>
                        </div>

                    </div>
                    <div id="chkOrderType" class="tab-pane fade order-type chkOrdType">
                        <!-- order type section start  -->
                        <section>
                            <?php echo $this->Form->create('orderType', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'name' => 'OrderTypeForm', 'id' => 'OrderTypeForm', 'url' => array('controller' => 'ajaxMenus', 'action' => 'delivery'))); ?>
                            <div class="login-form clearfix">

                                <h2>Order Type</h2>

                                <ul class="clearfix">
                                    <li class="col-xs-3">
                                        <span class="title"><label>Order Type <em>*</em></label></span>
                                        <div class="title-box">
                                            <?php
                                            if ($StoreDetails['Store']['is_take_away'] == 1) {
                                                $orderTypeList[2] = 'PickUp';
                                            }

                                            if ($StoreDetails['Store']['is_delivery'] == 1) {
                                                $orderTypeList[3] = 'Delivery';
                                            }
                                            echo $this->Form->input('orderType.type', array('type' => 'select', 'options' => $orderTypeList, 'class' => 'inbox', 'label' => false, 'div' => false));
                                            echo $this->Form->error('orderType.type');
                                            ?>
                                        </div>
                                    </li>
                                    <li id="orderTypeID" class="col-xs-9 ordr-bx-pos">

                                    </li>

                                </ul>

                            </div>

                            <div class="button-frame clearfix hidden">
                                <button type="button" id='btnOrderType' class="btn btn-primary theme-bg-1"> <span>Submit</span> </button>
                            </div>
                            <?php echo $this->Form->end(); ?>
                            <div class="clearfix"></div>
                            <div id="chkDeliveryAddress" class="tab-pane fade delivery-add chkDeliveryAdd1 in">
                                <?php //echo $this->element('orderLogin/delivery'); ?>
                                <li id="orderDelivery">

                                </li>
                            </div>
                        </section>
                        <!-- order type section end  -->
                    </div>

                    <!--                    <div id="chkDeliveryAddress" class="tab-pane fade delivery-add chkDeliveryAdd">
                    <?php //echo $this->element('orderLogin/delivery'); ?>
                                            <li id="orderDelivery">
                    
                                            </li>
                                        </div>-->
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    $(document).ajaxStart(function () {
        $(".errorMsg").html('');
    });
    $(document).ajaxComplete(function (event, xhr, settings) {
        /*
         response = jQuery.parseJSON(xhr.responseText);
         if (response.status != 1) {
         $(".errorMsg").html(response.msg);
         }
         */

    });


    $(document).ready(function () {
        $("#chkOrderType").hide();
        $("#chkDeliveryAddress").hide();

        $(".nav-tabs > li").click(function () {
            if ($(this).hasClass("disabled"))
                return false;
        });
    });

    function changeTabPan(activeTab, deactiveTab) {
        $('.' + activeTab).removeClass('disabled').addClass('active');
        $('#' + deactiveTab).removeClass('in active');
        $('#' + activeTab).addClass('in active');
        $('.' + deactiveTab).removeClass('active').addClass('disabled');
    }

    function setDefaultStoreTime(ordertype) {
        $.ajax({
            url: "<?php echo $this->Html->url(array('controller' => 'ajaxMenus', 'action' => 'setDefaultStoreTime')); ?>",
            type: "Post",
            dataType: 'html',
            async: false,
            data: {ordertype: ordertype},
            beforeSend: function () {
                $('#loading').show();
            },
            success: function (successResult) {
                $("#chkOrderType").show();
                $("#tab1login").hide();
                $("#chkDeliveryAddress").hide();
                $('#orderTypeType option[value="' + ordertype + '"]').prop('selected', true);
                $("#orderTypeID").html(successResult);
                setTimeout(function () {
                    $("#btnOrderType").trigger("click");
                }, 500);

            }
        });
    }

    function getdeliveryAddress() {
        $.ajax({
            type: 'POST',
            url: '/ajaxMenus/delivery',
            data: {},
            success: function (response) {
                //$("div#chkDeliveryAddress").show();
                //$("div#chkOrderType").hide();
                $("div#tab1login").hide();

                //changeTabPan('chkDeliveryAddress', 'chkOrderType');
                $("#chkDeliveryAddress").html(response);
            }
        });
    }

</script>





<!-- order type section end  -->
<script>
    $(document).ready(function () {
        $("#orderTypeType").change(function () {
            var ordertype = $("#orderTypeType").val();
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'ajaxMenus', 'action' => 'setDefaultStoreTime')); ?>",
                type: "Post",
                dataType: 'html',
                data: {ordertype: ordertype},
                beforeSend: function () {
                    $('#chkDeliveryAddress').empty();
                    $('#loading').show();
                },
                success: function (successResult) {
                    $("#orderTypeID").html(successResult);
                    setTimeout(function () {
                        $("#btnOrderType").trigger("click");
                    }, 500);
                }
            });
        });

        $('#btnOrderType').click(function () {
            $.ajax({
                type: 'POST',
                url: '/ajaxMenus/delivery',
                data: $('#OrderTypeForm').serialize(),
                async: false,
                success: function (response) {
                    //$("div#chkDeliveryAddress").show();
                    //$("div#chkOrderType").hide();
                    $("div#tab1login").hide();
                    //changeTabPan('chkDeliveryAddress', 'chkOrderType');
                    $("#chkDeliveryAddress").html(response);

                    $("div#chkDeliveryAddress").show();
                    $('#loading').hide();
                }
            });
        });
    });


</script>
