<style>
    [type="checkbox"]:not(:checked), [type="checkbox"]:checked {
        left: 0;
        position: relative;
    }

</style>
<div class="content single-frame">
    <div class="wrap">
        <div id="flashMsg"></div>
        <?php echo $this->Session->flash(); ?>
        <?php
        echo $this->Form->create('DeliveryAddress', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'Deliveryaddress'));
        echo $this->Form->input('DeliveryAddress.id', array('type' => 'hidden', 'class' => 'usrname-input', 'div' => false, 'value' => $addressId));
        echo $this->Form->input('DeliveryAddress.store_id', array('type' => 'hidden', 'class' => 'usrname-input', 'div' => false, 'value' => $encrypted_storeId));
        echo $this->Form->input('DeliveryAddress.merchant_id', array('type' => 'hidden', 'class' => 'usrname-input', 'div' => false, 'value' => $encrypted_merchantId));
        ?>
        <div class="clearfix">
            <section class="col-12">
                <h2>Update Delivery Address</h2>    	
                <ul class="clearfix">
                    <li class="col-xs-12">
                        <span class="title"><label>Name <em>*</em></label></span>
                        <div class="title-box"><?php
                            echo $this->Form->input('DeliveryAddress.name_on_bell', array('type' => 'text', 'class' => 'inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name'));
                            echo $this->Form->error('DeliveryAddress.name_on_bell');
                            ?></div>
                    </li>

                    <li class="col-xs-12">
                        <span class="title"><label>Address <em>*</em></label></span>
                        <div class="title-box"><?php
                            echo $this->Form->input('DeliveryAddress.address', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false));
                            echo $this->Form->error('DeliveryAddress.address');
                            ?></div>
                    </li>

                    <li class="col-xs-6">
                        <span class="title"><label>City </label></span>
                        <div class="title-box"><?php
                            echo $this->Form->input('DeliveryAddress.city', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false));
                            echo $this->Form->error('DeliveryAddress.city');
                            ?></div>
                    </li>

                    <li class="col-xs-6">
                        <span class="title"><label>State <em>*</em></label></span>
                        <div class="title-box"><?php
                            echo $this->Form->input('DeliveryAddress.state', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off'));
                            echo $this->Form->error('DeliveryAddress.state');
                            ?></div>
                    </li>

                    <li class="col-xs-6">
                        <span class="title"><label>Zip-Code <em>*</em></label></span>
                        <div class="title-box"><?php
                            echo $this->Form->input('DeliveryAddress.zipcode', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true));
                            echo $this->Form->error('DeliveryAddress.zipcode');
                            ?></div>
                    </li>

                    <li class="col-xs-6 phn-id">
                        <span class="title"><label>Phone Number<em>*</em></label></span>
                        <div class="title-box">
                            <?php echo $this->Form->input('DeliveryAddress.country_code_id', array('type' => 'select', 'options' => $countryCode, 'value' => $this->request->data['CountryCode']['id'], 'class' => 'inbox country-code', 'label' => false, 'div' => false)); ?>
                            <?php
                            echo $this->Form->input('DeliveryAddress.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'inbox phone-number', 'placeholder' => 'Phone Number', 'label' => false, 'div' => false, 'required' => true));
                            echo $this->Form->error('DeliveryAddress.phone');
                            ?>
                            <span style='margin:7px 0px 0px 23px;font-size:12px;display:inline-block'>(eg. 111-111-1111)</span>
                        </div>
                    </li>


                    <li class="col-xs-12 default-add">
                        <span class="title"><label>Default address</label></span>
                        <span class="title-box">
                            <?php
                            $checked = "";
                            if ($this->request->data['DeliveryAddress']['default'] == 1) {
                                $checked = "checked";
                            }


                            echo $this->Form->checkbox('DeliveryAddress.default', array('checked' => $checked, 'class' => 'ordertype'));
                            ?>                             
                        </span>
                    </li>

                </ul>

                <div class="button-grp">
                    <?php
                    echo $this->Form->button('Update', array('type' => 'button', 'class' => 'btn btn-primary theme-bg-1', 'id' => 'updatebutton'));
                    echo $this->Form->button('Cancel', array('type' => 'button', 'class' => 'btn btn-primary theme-bg-1', 'id' => 'cancelbutton'));
                    ?>
                </div>
            </section>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>
<script>
    $(".phone-number").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    });
    $("[data-mask='mobileNo']").mask("(999) 999-9999");
    $("#Deliveryaddress").validate({
        rules: {
            "data[DeliveryAddress][name_on_bell]": {
                required: true,
                lettersonly: true,
            },
            "data[DeliveryAddress][address]": {
                required: true,
            },
            "data[DeliveryAddress][city]": {
                required: true,
                lettersonly: true,
            },
            "data[DeliveryAddress][state]": {
                required: true,
                lettersonly: true,
            },
            "data[DeliveryAddress][zipcode]": {
                required: true,
                number: true,
                minlength: 5,
                maxlength: 5,
            }, "data[DeliveryAddress][phone]": {
                required: true,
            },
        },
        messages: {
            "data[DeliveryAddress][name_on_bell]": {
                required: "Please enter your name",
                lettersonly: "Only alphabates allowed",
            },
            "data[DeliveryAddress][address]": {
                required: "Please enter your address",
            },
            "data[DeliveryAddress][city]": {
                required: "Please enter city",
                lettersonly: "Only alphabates are allowed",
            },
            "data[DeliveryAddress][state]": {
                required: "Please enter state ",
                lettersonly: "Only alphabates are allowed",
            },
            "data[DeliveryAddress][zipcode]": {
                required: "Please enter zip-code.",
                number: "Only numbers are allowed"
            },
            "data[DeliveryAddress][phone]": {
                required: "Contact number required",
            },
        }
    });


    $('#updatebutton').click(function () {
        if ($("#Deliveryaddress").valid()) {
            $('#loading').show();
            $.ajax({
                type: 'POST',
                url: '/ajaxMenus/updateAddress',
                async: false,
                data: $('#Deliveryaddress').serialize(),
                success: function (returnData) {
                    checkAddressJson = IsJsonString(returnData);
                    $('#loading').hide();
                    if (checkAddressJson) {
                        data = JSON.parse(returnData);
                        $("#errorPop").modal('show');
                        $("#errorPopMsg").html(data.msg);
                        /*$("#flashMsg").append('<div class="alert alert-danger" id="flashMessage"><a href="#" data-dismiss="alert" aria-label="close" title="close" class="pull-right">×</a> ' + data.msg + '</div>');
                         setTimeout(function () {
                         $("#flashMessage").remove()
                         }, 4000);*/
                        return false;
                    } else {
                        var deliveryId = $("#DeliveryAddressId").val();
                        var storeId = '<?php echo $encrypted_storeId; ?>';
                        var merchantId = '<?php echo $encrypted_merchantId; ?>';
                        if (deliveryId === undefined || deliveryId === null) {
                            deliveryId = $("#home").val();
                            $("#home").prop("checked", true);
                        }
                        $('#loading').hide();
                        $.ajax({
                            type: 'post',
                            url: '/ajaxMenus/getDeliveryAddress',
                            async: false,
                            data: {'deliveryId': deliveryId, 'storeId': storeId, 'merchantId': merchantId},
                            success: function (result) {
                                if (result) {
                                    $.ajax({
                                        type: 'POST',
                                        url: '/ajaxMenus/delivery',
                                        data: {},
                                        success: function (response) {
                                            //changeTabPan('chkDeliveryAddress', 'chkOrderType');
                                            $("#chkDeliveryAddress").html(response);
                                        }
                                    });
                                }
                            }
                        });
                    }
                }
            });
        }
    });

    $('#cancelbutton').click(function () {
        getdeliveryAddress();
    });

    function IsJsonString(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }
</script>
