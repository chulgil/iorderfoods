<style>
    .modal-dialog {    
        margin: 0 auto;        
        position:relative;          
    }
    .modal-body{
        color:#000000;
    } 
    .modal{
         top: 32% !important;  
    }
    .order-form-layout{
        float: left;
        width: 100%;
        margin: 15px auto;
        }

    .modal-backdrop {
        z-index: 0;
    }
    @media all and ( max-width: 680px) {
        .modal-dialog {
            width: auto;
        }
    }
    @media all and ( min-width: 700px) {
        .modal-dialog {
            width: 400px;
        }
    }  
    
    .modal-header {
        border-bottom: none;
    }

</style>
<div class="modal fade" id="orderLogin" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog select-modal">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <span class="modal-title" id="lineModalLabel">Please <b><a href="/users/login">login</a></b> to continue, or </br>enter your information as a guest.</span>
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
            data: {ordertype:ordertype},
            success: function (successResult) {
                $("#chkOrderType").show();
                $("#tab1login").hide();
                $("#chkDeliveryAddress").hide();                
                $("#orderTypeID").html(successResult);
            }
        });
    }

    function getdeliveryAddress(){
        $.ajax({
            type: 'POST',                
            url: '/ajaxMenus/delivery',
            data: {},                
            success: function (response) { 
                $("div#chkDeliveryAddress").show();  
                $("div#chkOrderType").hide();
                $("div#tab1login").hide();
                
                changeTabPan('chkDeliveryAddress','chkOrderType');
                $("#chkDeliveryAddress").html(response);
            }
        });
    }

</script>





<!-- order type section end  -->
<script>    
    $("#orderTypeType").change(function () {        
        var ordertype=$("#orderTypeType").val();
        $.ajax({
            url: "<?php echo $this->Html->url(array('controller' => 'ajaxMenus', 'action' => 'setDefaultStoreTime')); ?>",
            type: "Post",
            dataType: 'html',
            data: {ordertype:ordertype},
            success: function (successResult) {                
                $("#orderTypeID").html(successResult);
            }
        });
    });
    
    $('#btnOrderType').click(function () {       
        $.ajax({
            type: 'POST',                
            url: '/ajaxMenus/delivery',
            data: $('#OrderTypeForm').serialize(),                
            success: function (response) {     
                $("div#chkDeliveryAddress").show();  
                $("div#chkOrderType").hide();
                $("div#tab1login").hide();
                changeTabPan('chkDeliveryAddress','chkOrderType');
                $("#chkDeliveryAddress").html(response);
            }
        });
    });
    
    
    
</script>
