<div class="modal-dialog inner-wrap clearfix">
    <div class="common-title">
        <h3>Edit Address</h3>
    </div>

    <button data-dismiss="modal" class="close" type="button">×</button>
    <?php
    echo $this->Form->create('DeliveryAddress', array('url' => false, 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'Deliveryaddress'));
    echo $this->Form->input('DeliveryAddress.id', array('type' => 'hidden', 'class' => 'usrname-input', 'div' => false, 'value' => $this->Encryption->encode($addressId)));
    ?>
    <div class="form-section editadd">
        <span id="flashError"></span>
        <div class="profile-input clearfix">
            <label>Name <em>*</em></label>
            <div class="col-right">
                <div class="col-width">
<?php
echo $this->Form->input('DeliveryAddress.name_on_bell', array('type' => 'text', 'class' => 'user-detail', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name'));
?>
                </div>
            </div>
        </div>
        <div class="profile-input clearfix">
            <label>Address <em>*</em></label>
            <div class="col-right">
                <div class="col-width">
<?php
echo $this->Form->input('DeliveryAddress.address', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false));
?>
                </div>
            </div>
        </div>
        <div class="profile-input clearfix">
            <label>City <em>*</em></label>
            <div class="col-right">
                <div class="col-width">
<?php
echo $this->Form->input('DeliveryAddress.city', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'City', 'maxlength' => '50', 'label' => false, 'div' => false));
?>
                </div>
            </div>
        </div>
        <div class="profile-input clearfix">
            <label>State <em>*</em></label>
            <div class="col-right">
                <div class="col-width">
<?php
echo $this->Form->input('DeliveryAddress.state', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'State', 'maxlength' => '50', 'label' => false, 'div' => false, 'required' => true, 'autocomplete' => 'off'));
?>
                </div>
            </div>
        </div>
        <div class="profile-input clearfix">
            <label>Zip-Code <em>*</em></label>
            <div class="col-right">
                <div class="col-width">
<?php
echo $this->Form->input('DeliveryAddress.zipcode', array('type' => 'text', 'class' => 'user-detail', 'placeholder' => 'Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false, 'required' => true));
?>
                </div>
            </div>
        </div>
        <div class="profile-input clearfix">
            <label>Phone Number<em>*</em></label>
            <div class="col-right">
                <div class="col-width">
                    <div class="col-1">
<?php
echo $this->Form->input('DeliveryAddress.country_code_id', array('type' => 'select', 'options' => $countryCode, 'value' => $this->request->data['CountryCode']['id'], 'class' => 'user-detail country-code', 'label' => false, 'div' => false));
?>
                    </div>
                    <div class="col-2">
<?php
echo $this->Form->input('DeliveryAddress.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'user-detail phone-number', 'placeholder' => 'Phone Number', 'label' => false, 'div' => false, 'required' => true));
?>
                        <span>(eg. 111-111-1111)</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="profile-input popup-checkbox clearfix">
                        <?php
                        $checked = "";
                        if ($this->request->data['DeliveryAddress']['default'] == 1) {
                            $checked = "checked";
                        }
                        echo $this->Form->input('DeliveryAddress.default', array("type" => "checkbox", 'checked' => $checked, 'class' => 'ordertype', 'div' => false, 'label' => 'Default address'));
                        ?>
        </div>



    </div>

    <div class="confirm">
        <div class="submit">
            <?php
            if (DESIGN == 3) {
                echo $this->Form->button('Cancel', array('type' => 'button', 'class' => 'closeModal cont-btn p-cancle'));
                echo $this->Form->button('Update', array('type' => 'submit', 'class' => 'submitAddress cont-btn p-save theme-bg-1'));
            } else {
                echo $this->Form->button('Update', array('type' => 'submit', 'class' => 'submitAddress cont-btn p-save theme-bg-1'));
                echo $this->Form->button('Cancel', array('type' => 'button', 'class' => 'closeModal cont-btn p-cancle theme-bg-2'));
            }
            ?>
        </div>
    </div>
            <?php echo $this->Form->end(); ?>
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
        debug: false,
        errorClass: "error",
        errorElement: 'span',
        onkeyup: false,
        rules: {
            "data[DeliveryAddress][name_on_bell]": {
                required: true,
                lettersonly: true,
                minlength: 2,
                maxlength: 30
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
                lettersonly: true
            },
            "data[DeliveryAddress][zipcode]": {
                required: true,
                number: true,
                minlength: 5,
                maxlength: 6
            }, "data[DeliveryAddress][phone]": {
                required: true
            },
        },
        messages: {
            "data[DeliveryAddress][name_on_bell]": {
                required: "Please enter your name",
                lettersonly: "Only alphabates allowed"
            },
            "data[DeliveryAddress][address]": {
                required: "Please enter your address"
            },
            "data[DeliveryAddress][city]": {
                required: "Please enter city",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress][state]": {
                required: "Please enter state ",
                lettersonly: "Only alphabates are allowed"
            },
            "data[DeliveryAddress][zipcode]": {
                required: "Please enter zip-code.",
                number: "Only numbers are allowed"
            },
            "data[DeliveryAddress][phone]": {
                required: "Contact number required"
            }
        }, highlight: function (element, errorClass) {
            $(element).removeClass(errorClass);
        }
    });
    $(".submitAddress").on('click', function (e) {
        if ($("#Deliveryaddress").valid()) {
            var formData = $("#Deliveryaddress").serialize();
            $.ajax({
                type: 'post',
                url: "<?php echo $this->Html->url(array('controller' => 'orderOverviews', 'action' => 'updateDeliveryAddress')); ?>",
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
                    isJson = IsJsonString(successResult);
                    if (isJson) {
                        var obj = jQuery.parseJSON(successResult);
                        if (obj.status == 'Error') {
                            $("#errorPop").modal('show');
                            $("#errorPopMsg").html(obj.msg);
                            //$('#flashError').html('<div class="message message-success alert alert-danger" id="flashMessage"><a href="#" data-dismiss="alert" aria-label="close" title="close" class="pull-right">×</a> ' + obj.msg + '</div>');
                            return false;
                        }
                    } else {
                        if (successResult) {
                            $('#delivery_address').html(successResult);
                            $('#address-modal').modal('hide');
                        } else {
                            $('#address-modal').modal('hide');
                        }
                    }
                }
            });

        }
        e.preventDefault();
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