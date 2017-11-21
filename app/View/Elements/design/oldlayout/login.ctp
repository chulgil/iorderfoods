
<!-- CONTENT START -->
<style>
    .description {
        font-weight: 400;
    }

    .description .details {
        margin-left: 124px;
    }

    .description p {
        line-height: 24px;
    }

    .closeReason {
        font-size: 14px;
        margin-left: 1%;
        /*position: absolute;
        top:39%;*/
    }

    .closeStore {
        font-size: 24px;
    }

    hr {
        margin: 5px 0;
    }
</style>
<?php
$guestUser = false;
if (!AuthComponent::User() && $this->Session->check('Order.delivery_address_id')) {
    $guestUser = true;
}
$PreorderAllowed = $this->Common->checkPreorder();
if (AuthComponent::User()) {
    ?>
    <div class="clearfix"></div>
    <?php
} else {
    if ($setPre == 1) {
        ?>
        <div class="">
            <section class="form-layout delivery-form1" style="width:100%;padding:10px;margin-bottom:10px;">
                <span class="closeStore">Store is closed </span>
                <span class="closeReason">
                    <?php echo ($store_data['Store']['close_details']) ? $store_data['Store']['close_details'] : ""; ?>
                </span>
            </section>
        </div>
    <?php } ?>
    <?php if (!$guestUser) { ?>
        <?php echo $this->Form->create('Users', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'name' => 'Form1', 'id' => 'GuestDeliverOrdering', 'url' => array('controller' => 'users', 'action' => 'guestOrdering'))); ?>
        <input type="radio" name="data[Order][type]" checked="checked"/>
        <ul class="nav nav-tabs delpkup">
            <?php
            $pickupActive = '';
            if ($store_data['Store']['is_delivery'] == 1 && $store_data['Store']['guest_user'] == 1 && $store_data['Store']['order_type_forms'] == 1) {
                ?>
                <li class="active"><a id="1">Delivery</a></li>
                <?php
            } else {
                $pickupActive = 'active';
            }
            if ($store_data['Store']['is_take_away'] == 1 && $store_data['Store']['guest_user'] == 1 && $store_data['Store']['order_type_forms'] == 1) {
                ?>
                <li class="<?php echo $pickupActive; ?>"><a id="2">Pickup</a></li>
            <?php } ?>
        </ul>
        <?php if ($store_data['Store']['is_delivery'] == 1 && $store_data['Store']['guest_user'] == 1 && $store_data['Store']['order_type_forms'] == 1) { ?>
            <div class="tabContent" id="ordertype1">
                <section class="form-layout delivery-form login-page-min-height dp-wrap">
                    <div class="radio-btn">
                    </div>
                    <div class="height20"></div>
                    <div class="row">
                        <div class="col-sm-6">
                            <ul class="clearfix">
                                <li>
                                    <span class="title"><label>Name <em>*</em></label></span>
                                    <div class="title-box">
                                        <?php
                                        echo $this->Form->input('DeliveryAddress.name_on_bell', array('type' => 'text', 'class' => 'inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name'));
                                        echo $this->Form->error('DeliveryAddress.name_on_bell');
                                        ?>
                                    </div>
                                </li>
                                <li>
                                    <span class="title"><label>Address <em>*</em></label></span>
                                    <div
                                        class="title-box"> <?php
                                            echo $this->Form->input('DeliveryAddress.address', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your Address', 'label' => false, 'div' => false));
                                            echo $this->Form->error('DeliveryAddress.address');
                                            ?> </div>
                                </li>

                                <li>
                                    <span class="title"><label>City <em>*</em></label></span>

                                    <div
                                        class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress.city', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your City', 'maxlength' => '50', 'label' => false, 'div' => false));
                                            echo $this->Form->error('DeliveryAddress.city');
                                            ?></div>
                                </li>

                                <li>
                                    <span class="title"><label>State <em>*</em></label></span>

                                    <div
                                        class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress.state', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your State', 'maxlength' => '50', 'label' => false, 'div' => false, 'autocomplete' => 'off'));
                                            echo $this->Form->error('DeliveryAddress.state');
                                            ?></div>
                                </li>
                                <li>
                                    <span class="title"><label>Zip-Code <em>*</em></label></span>

                                    <div
                                        class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress.zipcode', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your Zip-Code', 'maxlength' => '5', 'label' => false, 'div' => false));
                                            echo $this->Form->error('DeliveryAddress.zipcode');
                                            ?></div>
                                </li>
                            </ul>
                        </div>
                        <div class="col-sm-6">
                            <ul class="clearfix">


                                <li>
                                    <span class="title"><label>Phone Number <em>*</em></label></span>

                                    <div class="title-box">
                                        <?php echo $this->Form->input('DeliveryAddress.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'inbox country-code', 'label' => false, 'div' => false)); ?>
                                        <?php
                                        echo $this->Form->input('DeliveryAddress.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'inbox phone-number', 'placeholder' => 'Enter Your Phone Number', 'label' => false, 'div' => false));
                                        echo $this->Form->error('DeliveryAddress.phone');
                                        ?>
                                    </div>
                                </li>
                                <li>
                                    <span class="title"><label>Email <em>*</em></label></span>
                                    <div
                                        class="title-box"><?php
                                            echo $this->Form->input('DeliveryAddress.email', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your Email', 'maxlength' => '50', 'label' => false, 'div' => false));
                                            echo $this->Form->error('DeliveryAddress.email');
                                            ?></div>
                                    <?php echo $this->Form->input('Delivery.type', array('value' => 0, 'type' => 'hidden')); ?>
                                </li>

                                <?php if ($PreorderAllowed) { ?>
                                    <li>
                                        <span class="title"><label>Delivery Date <em>*</em></label></span>
                                        <div class="title-box">
                                            <?php
                                            echo $this->Form->input('Delivery.pickup_date', array('type' => 'text', 'class' => 'inbox date-select', 'placeholder' => 'Date', 'label' => false, 'div' => false, 'required' => true, 'readOnly' => true));
                                            echo $this->Form->error('Delivery.pickup_date');
                                            ?>
                                        </div>
                                    </li>
                                    <li>
                                        <span class="title"><label>Delivery Time</label></span>
                                        <div class="title-box"><span id="deliveryTime">
                                                <select id="PickUpPickupTimeNow" class="inbox"
                                                        name="data[Store][pickup_time_now]">
                                                            <?php
                                                            if (!empty($time_range)) {
                                                                foreach ($time_range as $key => $value) {
                                                                    $flag = true;
                                                                    foreach ($storeBreak as $breakKey => $breakVlue) {
                                                                        if (strtotime($storeBreak[$breakKey]['start']) <= strtotime($key) && strtotime($storeBreak[$breakKey]['end']) >= strtotime($key)) {
                                                                            echo "<option value='$key' disabled='disabled'>$value - Break Time </option>";
                                                                            $flag = false;
                                                                        }
                                                                    }
                                                                    if ($flag) {
                                                                        echo "<option value='$key'>$value</option>";
                                                                    }
                                                                }
                                                            } else {
                                                                echo "<option value=''>Store is closed for today</option>";
                                                            }
                                                            ?>
                                                </select>
                                            <?php echo $this->Form->error('Store.pickup_time_now'); ?></div>
                                        </span>
                                    </li>

                                <?php } ?>
                                <li>
                                    
                                    <?php
                                    if((isset($store_data['Store']['delivery_description']) && !empty($store_data['Store']['delivery_description'])))
                                    {
                                    ?>
                                    <div><i class="fa fa-caret-down"></i><label>Details</label>
                                        <div>
                                            <div>
                                                <?php if ($store_data['Store']['minimum_order_price'] > 0) { ?>

                                                    Minimum Amount for Delivery : $<?php echo $store_data['Store']['minimum_order_price']; ?>

                                                <?php } ?>

                                            </div>
                                            <div><p> <?php echo (isset($store_data['Store']['delivery_description']) && !empty($store_data['Store']['delivery_description']) ? $store_data['Store']['delivery_description'] : ''); ?></p></div>
                                        </div>
                                    </div>
                                    <?php
                                    }
                                    else
                                    {
                                        if (number_format($store_data['Store']['minimum_order_price'], 0) > 0)
                                        { ?>
                                        <div><i class="fa fa-caret-down"></i><label>Details</label>
                                            <div>
                                                <div>


                                                        Minimum Amount for Delivery : $<?php echo $store_data['Store']['minimum_order_price']; ?>



                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        }
                                    }
                                    ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>
            </div>  
            <?php
        }
        if ($store_data['Store']['is_take_away'] == 1 && $store_data['Store']['guest_user'] == 1 && $store_data['Store']['order_type_forms'] == 1) {
            ?>
            <div class="tabContent" id="ordertype2" <?php if (empty($pickupActive)) { ?>style="display:none;"<?php } ?>>
                <section class="form-layout pickup-form login-page-min-height dp-wrap">
                    <div class="radio-btn">
                    </div>
                    <div class="height20 pick"></div>
                    <div class="row">
                        <div class="col-sm-6">
                            <ul class="clearfix">
                                <li>
                                    <span class="title"><label>Name <em>*</em></label></span>
                                    <div
                                        class="title-box"><?php
                                            echo $this->Form->input('PickUpAddress.name_on_bell', array('type' => 'text', 'class' => 'inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter Your Name'));
                                            echo $this->Form->error('PickUpAddress.name_on_bell');
                                            ?>
                                    </div>
                                </li>
                                <li>
                                    <span class="title"><label>Email <em>*</em></label></span>
                                    <div
                                        class="title-box"><?php
                                            echo $this->Form->input('PickUpAddress.email', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'Enter Your Email', 'maxlength' => '50', 'label' => false, 'div' => false));
                                            echo $this->Form->error('PickUpAddress.email');
                                            ?>
                                    </div>
                                </li>
                                <?php if ($PreorderAllowed) { ?>
                                    <li>
                                        <span class="title"><label>Pick Up Date <em>*</em></label></span>
                                        <div class="title-box">
                                            <?php
                                            echo $this->Form->input('PickUp.pickup_date', array('type' => 'text', 'class' => 'inbox date-select', 'placeholder' => 'Date', 'label' => false, 'div' => false, 'required' => true, 'readOnly' => true));
                                            echo $this->Form->error('PickUp.pickup_date');
                                            ?>
                                        </div>
                                    </li>
                                    <li>
                                        <span class="title"><label>Pick Up Time</label></span>
                                        <div class="title-box"><span id="resvTime">
                                                <select id="PickUpPickupTimeNow" class="inbox"
                                                        name="data[Store][pickup_time_now]">
                                                            <?php
                                                            if (!empty($time_range)) {
                                                                foreach ($time_range as $key => $value) {
                                                                    $flag = true;
                                                                    foreach ($storeBreak as $breakKey => $breakVlue) {
                                                                        if (strtotime($storeBreak[$breakKey]['start']) <= strtotime($key) && strtotime($storeBreak[$breakKey]['end']) >= strtotime($key)) {
                                                                            echo "<option value='$key' disabled='disabled'>$value - Break Time </option>";
                                                                            $flag = false;
                                                                        }
                                                                    }
                                                                    if ($flag) {
                                                                        echo "<option value='$key'>$value</option>";
                                                                    }
                                                                }
                                                            } else {
                                                                echo "<option value=''>Store is closed for today</option>";
                                                            }
                                                            ?>
                                                </select>
                                                <?php echo $this->Form->error('Store.pickup_time_now'); ?>
                                        </div>
                                    </li>
                                <?php } ?>
                                <li>
                                    <span class="title"><label>Phone Number <em>*</em></label></span>

                                    <div class="title-box">
                                        <?php echo $this->Form->input('PickUpAddress.country_code_id', array('type' => 'select', 'options' => $countryCode, 'class' => 'inbox country-code', 'label' => false, 'div' => false)); ?>
                                        <?php
                                        echo $this->Form->input('PickUpAddress.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'inbox phone-number', 'placeholder' => 'Enter Your Phone Number', 'label' => false, 'div' => false));
                                        echo $this->Form->error('PickUpAddress.phone');
                                        ?>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="col-sm-6">
                            <div class="address">
                                <address class="inbox">
                                    <h3><?php echo $store_data['Store']['store_name']; ?></h3>
                                    <p> <?php echo $store_data['Store']['address']; ?>
                                        <br> <?php echo $store_data['Store']['city'] . ' ' . $store_data['Store']['state'] . ' ' . $store_data['Store']['zipcode']; ?>
                                        <br> <?php echo $store_data['Store']['phone']; ?></p>
                                </address>
                            </div>
                            <div class="height20 pick"></div>
                            <ul class="clearfix">
                                <li>
                                    <?php echo $this->Form->input('PickUp.type', array('value' => 0, 'type' => 'hidden')); ?>
                                    <?php
                                    if(isset($store_data['Store']['take_away_description']) && !empty($store_data['Store']['take_away_description'])){
                                        ?>
                                        <div><i class="fa fa-caret-down"></i><label>Details</label>
                                        <div>
                                            <div><p> <?php echo $store_data['Store']['take_away_description']; ?></p></div></div></div>
                                        <?php
                                    }
                                    ?>
                                    
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="height20 pick"></div>
                </section>
            </div>  
        <?php } ?>
        <div class="clearfix"></div>
        <?php if (($store_data['Store']['is_delivery'] == 1 || $store_data['Store']['is_take_away'] == 1) && $store_data['Store']['guest_user'] == 1 && $store_data['Store']['order_type_forms'] == 1) { ?>
            <div class="button-frame">
                <button type="submit" id='proceed' class="btn green-btn pink-btn brown-btn"><span>Proceed as Guest</span></button>
            </div>

        <?php } ?>
        <?php echo $this->Form->end(); ?> 
        <?php
    }
}
?>
<script>
    $('.delpkup a').click(function (e) {
        $(this).tab('show');
        var tabContent = '#ordertype' + this.id;
        $('#ordertype1').hide();
        $('#ordertype2').hide();
        $(tabContent).show();
        defaultForm();
    })
</script>
<?php
// Add 06/30/2016 Request from kctorrance
$popupMessage = '';
switch ($store_data['Store']['store_url']) {
    case "kctorrance.com" :
        $popupHeader = '<strong>Welcome to Kid Concepts!</strong>';
        $popupMessage = '
                Order your favorite meals and select what type of admission
                </br>you would like by selecting the "Pick-Up" option.
                </br></br>
                Request a reservation by selecting the "Dine-In" option.
                </br>We\'ll confirm it with you via phone or email.
                </br></br>
                Please call us if you have any questions at: 310-465-0075
                </br></br>
                For party reservations, please go to: <a href="http://www.kidconceptsusa.com/">kidconceptsusa.com</a>
            ';
        break;
}
if ($popupMessage) {
    ?>
    <div data-remodal-id="modal2" role="dialog" aria-labelledby="modal2Title" aria-describedby="modal2Desc">
        <div>
            <h2 id="modal2Title"><?= $popupHeader ?></h2></br><hr></br>
            <p id="modal2Desc">
                <?= $popupMessage ?>
            </p>
        </div>
        <br>
        <hr></br>
        <button data-remodal-action="confirm" class="remodal-confirm">&nbsp;&nbsp; [X] Close &nbsp;&nbsp; </button>
    </div>
    <script>
        $("[data-remodal-id=modal2]").remodal().open();
    </script>

    <?php
}
?>


<script>
    function defaultForm() {
        validator.resetForm();
        //var result = $("input[name='data[Order][type]']:checked").val();
        var result = $("ul.delpkup li.active a").attr("id");
        //if (result == 2) {
        if (result == 2) {// Pick Up
            $("input[name='data[Order][type]']").val("2");
            $(".pickup-form input").prop("disabled", false);
            $(".delivery-form input").prop("disabled", true);
            //$(".delivery-form input[name='data[Order][type]']").prop("disabled", true);
            $(".pickup-form select").prop("disabled", false);
            $(".delivery-form select").prop("disabled", true);
            $("#PickUpPickupDate").prop("name", 'data[PickUp][pickup_date]');
            $("#StorePickupTime").prop("name", 'data[Store][pickup_time]');
            $("#DeliveryPickupDate").prop("name", '');
            $("#DeliveryPickupTime").prop("name", '');
        } else {
            $("input[name='data[Order][type]']").val("3");
            $(".pickup-form input").prop("disabled", true);
            $(".delivery-form input").prop("disabled", false);
            //$(".pickup-form input[name='data[Order][type]']").prop("disabled", false);
            $(".pickup-form select").prop("disabled", true);
            $(".delivery-form select").prop("disabled", false);
            $("#DeliveryPickupDate").prop("name", 'data[Delivery][pickup_date]');
            $("#DeliveryPickupTime").prop("name", 'data[Store][pickup_time]');
            $("#StorePickupTime").prop("name", '');
            $("#PickUpPickupDate").prop("name", '');
        }
    }

    $(document).ready(function () {
        defaultForm();
        $("[data-mask='mobileNo']").mask("(999) 999-9999");

        $(".phone-number").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });

        if ($("section").hasClass("delivery-form") == false) {
            $(".pickup-form").css("float", 'left');
        }

        $("input[name='data[Order][type]']:radio").change(function () {
            defaultForm();
        });
        $('#GuestDeliverOrdering #proceed').click(function (e) {
            e.preventDefault();
            if ($("#GuestDeliverOrdering").valid() && $("input[name='data[Order][type]']").val() == 3) {
                var address = $("#DeliveryAddressAddress").val();
                var city = $("#DeliveryAddressCity").val();
                var state = $("#DeliveryAddressState").val();
                var zipcode = $("#DeliveryAddressZipcode").val();
                $.ajax({
                    url: "<?php echo $this->Html->url(array('controller' => 'ajaxMenus', 'action' => 'checkAddressInZone')); ?>",
                    type: "post",
                    data: {
                        address: address,
                        city: city,
                        state: state,
                        zipcode: zipcode,
                    },
                    success: function (result) {
                        if (result) {
                            response = $.parseJSON(result);
                            if (response.status == "Error") {
                                $("#errorPop").modal('show');
                                $("#errorPopMsg").html(response.msg);
                                return false;
                            } else if (response.status == "Success") {
                                $("#GuestDeliverOrdering").submit();
                            }
                        }
                    }
                });
            } else if ($("#GuestDeliverOrdering").valid() && $("input[name='data[Order][type]']").val() == 2) {
                $("#GuestDeliverOrdering").submit();
            }
        });

    });



    $("#proceed").click(function () {
        //var order_type = $("input[name='data[Order][type]']:checked").val();
        var order_type = $("ul.delpkup li.active a").attr("id");
        switch (order_type) {
            case 1 : // Delivery
                $("#StorePickupTime").prop("name", '');
                break;
            case 2 : // Pick Up
                $("#DeliveryPickupTime").prop("name", '');
                break;
        }
    });


    $(".notice-close").click(function () {
        $(".notice-func").fadeOut(600);
        $(".notice-bg").fadeOut(600);
    });
    $("#img-close").click(function () {
        $(".notice-func").fadeOut(600);
        $(".notice-bg").fadeOut(600);
    });



    var validator = $("#GuestDeliverOrdering").validate({
        rules: {
            "data[DeliveryAddress][name_on_bell]": {
                required: true,
                lettersonly: true,
                // noSpace:true
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
            "data[DeliveryAddress][email]": {
                required: true,
                email: true,
                minlength: 10,
                maxlength: 50,
            },
            "data[PickUpAddress][name_on_bell]": {
                required: true,
                lettersonly: true,
            },
            "data[PickUpAddress][email]": {
                required: true,
                email: true,
                minlength: 10,
                maxlength: 50,
            },
            "data[PickUpAddress][phone]": {
                required: true,
            }
        },
        messages: {
            "data[DeliveryAddress][name_on_bell]": {
                required: "Please enter your Name",
                lettersonly: "Only alphabates allowed",
            },
            "data[DeliveryAddress][address]": {
                required: "Please enter your address",
            },
            "data[DeliveryAddress][city]": {
                required: "Please enter city",
                lettersonly: "Only alphabates allowed",
            },
            "data[DeliveryAddress][state]": {
                required: "Please enter state ",
                lettersonly: "Only alphabates allowed",
            },
            "data[DeliveryAddress][zipcode]": {
                required: "Please enter zip-code.",
                number: "Only numbers are allowed"
            },
            "data[DeliveryAddress][phone]": {
                required: "Contact number required",
            },
            "data[DeliveryAddress][email]": {
                required: "Please enter email",
                email: "Please enter valid email"
            },
            "data[PickUpAddress][name_on_bell]": {
                required: "Please enter your Name",
                lettersonly: "Only alphabates allowed",
            },
            "data[PickUpAddress][phone]": {
                required: "Contact number required",
            },
            "data[PickUpAddress][email]": {
                required: "Please enter email",
                email: "Please enter valid email"
            }
        }
    });

<?php if (!empty($popupFlg)) { ?>
        $(window).on("load", function () {
            $(".notice-bg").css("min-height", window.innerHeight + "px");
            $(".notice-func").css("top", (window.innerHeight - 178) / 2 + "px");
            $(".notice-func").css("left", (window.innerWidth - 300) / 2 + "px");
            //        $(".notice-func").css("left", (window.innerWidth - 700) / 2 + "px");
        });

        $(window).on("resize", function () {
            $(".notice-bg").css("min-height", window.innerHeight + "px");
            $(".notice-func").css("top", (window.innerHeight - 178) / 2 + "px");
            $(".notice-func").css("left", (window.innerWidth - 300) / 2 + "px");
            //        $(".notice-func").css("left", (window.innerWidth - 700) / 2 + "px");
        });
    <?php
}

$pickupadvanceDay = $store_data['Store']['pickcalendar_limit'] - 1 + $store_data['Store']['pickblackout_limit'];
$deliveryadvanceDay = $store_data['Store']['deliverycalendar_limit'] - 1 + $store_data['Store']['deliveryblackout_limit'];

$datetoConvert = explode('-', $pickcurrentDateVar);
$datetoConvert = $datetoConvert[2] . '-' . $datetoConvert[0] . '-' . $datetoConvert[1];
$pickupmaxdate = date('m-d-Y', strtotime($datetoConvert . ' +' . $pickupadvanceDay . ' day'));
$pickcurrentDateVar = date('m-d-Y', strtotime($datetoConvert . ' +' . $store_data['Store']['pickblackout_limit'] . ' day'));

$datetoConvert = explode('-', $delcurrentDateVar);
$datetoConvert = $datetoConvert[2] . '-' . $datetoConvert[0] . '-' . $datetoConvert[1];
$deliverymaxdate = date('m-d-Y', strtotime($datetoConvert . ' +' . $deliveryadvanceDay . ' day'));
$delcurrentDateVar = date('m-d-Y', strtotime($datetoConvert . ' +' . $store_data['Store']['deliveryblackout_limit'] . ' day'));
?>
    //Pickup Date Scripts
    $('#PickUpPickupDate').datepicker({
        dateFormat: 'mm-dd-yy',
        minDate: '<?php echo $pickcurrentDateVar; ?>',
        maxDate: '<?php echo $pickupmaxdate; ?>',
        beforeShowDay: function (date) {
            var day = date.getDay();
            var array = '<?php echo json_encode($closedDay); ?>';
            return [array.indexOf(day) == -1];
        }
    });
    $("#PickUpPickupDate").datepicker("setDate", '<?php echo $pickcurrentDateVar; ?>');
    var date = '<?php echo $pickcurrentDateVar; ?>';
    getTime(date, 2, 1, 'resvTime', 'StorePickupTime', true);
    $('#PickUpPickupDate').on('change', function () {
        var date = $(this).val();
        var orderType = 2; // 3= Take-away/pick-up
        var preOrder = 1;
        getTime(date, orderType, preOrder, 'resvTime', 'StorePickupTime');
    });
    //Pickup Date Scripts
    //Delivery Date Scripts
    $('#DeliveryPickupDate').datepicker({
        dateFormat: 'mm-dd-yy',
        minDate: '<?php echo $delcurrentDateVar; ?>',
        maxDate: '<?php echo $deliverymaxdate; ?>',
        beforeShowDay: function (date) {
            var day = date.getDay();
            var array = '<?php echo json_encode($closedDay); ?>';
            return [array.indexOf(day) == -1];
        }
    });
    $("#DeliveryPickupDate").datepicker("setDate", '<?php echo $delcurrentDateVar; ?>');
    var date = '<?php echo $delcurrentDateVar; ?>';
    getTime(date, 3, 1, 'deliveryTime', 'DeliveryPickupTime');

    $('#DeliveryPickupDate').on('change', function () {
        var date = $(this).val();
        var orderType = 3; // 3= Take-away/pick-up
        var preOrder = 1;
        getTime(date, orderType, preOrder, 'deliveryTime', 'DeliveryPickupTime');
    });

    //Delivery Date Scripts
    function getTime(date, orderType, preOrder, returnspan, ortype) {
        var type1 = 'Store';
        var type2 = 'pickup_time';
        var type3 = ortype;
        var storeId = '<?php echo $encrypted_storeId; ?>';
        var merchantId = '<?php echo $encrypted_merchantId; ?>';
        $.ajax({
            url: "<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'getStoreTime')); ?>",
            type: "Post",
            dataType: 'html',
            data: {
                storeId: storeId,
                merchantId: merchantId,
                date: date,
                type1: type1,
                type2: type2,
                type3: type3,
                orderType: orderType,
                preOrder: preOrder
            },
            success: function (result) {
                $('#' + returnspan).html(result);
            }
        });
    }

</script>
