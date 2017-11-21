<style>
    .modal-dialog {    
        margin: 0 auto;        
        position:relative;          
    }
    .modal-body{
        color:#000000;
    } 
    .modal .form-layout ul li{
        width:45%;
    }
    .modal .form-layout ul li span.title label{
        padding:0px;
    }
    
    .modal .single-frame .form-layout.sign-up ul li span.title{
        width:37%;
    }
    .order-form-layout{
        float: left;
        width: 100%;
        margin: 15px auto;
    }

    .modal-backdrop {
        z-index: 0;
    }
    .error {
        color: #b50000;
        font-size: 12px;
        font-weight: 400;
        margin: 0;
        padding: 0;
    }
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

</style>
<?php $StoreDetails = $this->Common->getStoreDetails(); ?>

<!-- -------modal for ups calculator -->
<div class="modal fade" id="orddelivery" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog select-modal">
        <div class="modal-content">
            <div class="modal-header">                
                <h3 class="modal-title" id="lineModalLabel">Confirm address</h3>
            </div>
            <div class="modal-body">
                <span class="errorMsg"></span>
                <div class="container" style="width:auto;">
                    <div id="chkOrderType" class="tab-pane fade order-type chkOrdType"> 
                        <!-- order type section start  -->
                        <section>                                
                            <?php echo $this->Form->create('orderType', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'name' => 'OrderTypeForm', 'id' => 'OrderTypeForm', 'url' => array('controller' => 'ajaxMenus', 'action' => 'delivery'))); ?>
                            <div class="login-form clearfix">
                                <h2>Order Type</h2>
                                <ul class="clearfix">
                                    <li class="col-xs-4">
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
                                    <li id="orderTypeID" class="col-xs-8"> </li>
                                </ul>
                            </div>

                            <div class="button-frame clearfix">
                                <button type="button" id='btnOrderType' class="btn btn-primary"> <span>Submit</span> </button>
                            </div>
                            <?php echo $this->Form->end(); ?>                                
                        </section>
                        <!-- order type section end  -->
                    </div>
                    <div id="chkDeliveryAddress" class="tab-pane delivery-add chkDeliveryAdd"> 
                        <li id="orderDelivery">
                            <?php //echo $this->element('orderLogin/login_delivery_address');  ?>
                        </li>
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>

<script>

    $(document).ajaxStart(function() {
        $(".errorMsg").html('');
    });
    $(document).ajaxComplete(function(event, xhr, settings) {
        /*
         response = jQuery.parseJSON(xhr.responseText);
         if (response.status != 1) {
         $(".errorMsg").html(response.msg);
         }
         */

    });


    $(document).ready(function() {
        $("#chkOrderType").hide();
        $("#chkDeliveryAddress").hide();

        $(".nav-tabs > li").click(function() {
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
            data: {ordertype: ordertype},
            success: function(successResult) {
                $("#chkOrderType").show();
                $("#tab1login").hide();
                $("#chkDeliveryAddress").hide();
                $('#orderTypeType option[value="' + ordertype + '"]').prop('selected', true);
                console.log(successResult);
                $("#orderTypeID").html(successResult);
            }
        });
    }

    function getdeliveryAddress() {
        $.ajax({
            type: 'POST',
            url: '/ajaxMenus/delivery',
            async: false,
            data: {},
            success: function(response) {
                $("div#chkDeliveryAddress").show();
                $("div#chkOrderType").hide();
                $("div#tab1login").hide();
                $("#chkDeliveryAddress").html(response);
                $("a#changeorderType").hide();
            }
        });
    }

</script>





<!-- order type section end  -->
<script>
    $("#orderTypeType").change(function() {
        var ordertype = $("#orderTypeType").val();
        $.ajax({
            url: "<?php echo $this->Html->url(array('controller' => 'ajaxMenus', 'action' => 'setDefaultStoreTime')); ?>",
            type: "Post",
            dataType: 'html',
            async: false,
            data: {ordertype: ordertype},
            success: function(successResult) {
                $("#orderTypeID").html(successResult);
            }
        });
    });

    $('#btnOrderType').click(function() {
        $.ajax({
            type: 'POST',
            url: '/ajaxMenus/delivery',
            data: $('#OrderTypeForm').serialize(),
            async: false,
            success: function(response) {
                $("div#chkDeliveryAddress").show();
                $("div#chkOrderType").hide();
                $("div#tab1login").hide();
                changeTabPan('chkDeliveryAddress', 'chkOrderType');
                $("#chkDeliveryAddress").html(response);
            }
        });
    });



</script>
