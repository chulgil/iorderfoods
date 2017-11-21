<?php
$customer_vault_id = '';
$credit_type = 'visa';
$credit_mask = '';

if (count($nzsafe_info) > 0) {
    $customer_vault_id = $nzsafe_info['customer_vault_id'];
    $credit_mask = $nzsafe_info['credit_mask'];
    $credit_type = strtolower($nzsafe_info['credit_type']);
}

if (AuthComponent::User()) {
    $userId = AuthComponent::User('id');
    $isNonUser = false;
} else {
    $userId = 0;
    $isNonUser = true;
}
$finalAmount = '';
$finalAmount = $this->Session->read('Cart.grand_total_final');
$guestUserDetail = $this->Session->check('GuestUser');
$userId = AuthComponent::User('id');
if (!empty($userId) || !empty($guestUserDetail)) {
    ?>
    <?php echo $this->Form->create('Payment', array('controller' => 'payments', 'action' => 'paymentSection', 'onsubmit' => 'return creditForm();')); ?>
    <input id="getFinalAmount" type="hidden" value="<?php echo $finalAmount; ?>"/>
    <input type="hidden" id="use_nzsafe" value="<?= $customer_vault_id ?>" />
    <input type="hidden" id="isNonUser" value="<?= $isNonUser ?>" />
    <div class="pay-select">                    
        <!-- ===== Payment selection(Radio) types  start ===== -->
        <span class="payoptioncls">
            <?php
            $fChecked = false;
            if ($store_result['Store']['is_pay_by_credit_card'] == 1 && $store_result['Store']['api_username'] != '' && $store_result['Store']['api_password'] != '' && !empty($storeSetting['StoreSetting']['is_creditcard_allow']) && $finalAmount > 0) {
                $fChecked = true;
                ?>
                <input type="radio" id='payment' name="payment" checked="checked" value=1 class="credit"/><label for="payment" class="common-bold"><span></span>Pay by Credit Card</label>
            <?php } ?>
        </span>
        <span class="payoptioncls">
            <?php if (AuthComponent::User() || ($store_result['Store']['guest_user'] == 1 && $store_result['Store']['guest_user_cod'] == 1)) { ?>
                <?php
                if ($store_result['Store']['cash_on_delivery'] == 1) {
                    if ($fChecked) {
                        $checked = "";
                    } else {
                        $checked = "checked=checked";
                    }
                    ?>
                    <input type="radio" id='payment2' name="payment" value=3 class="cod" <?php echo $checked; ?>/> <label for="payment2" class="common-bold"><span></span>Cash on Delivery</label>
                <?php } ?>
            <?php } ?>
        </span>
        <span class="payoptioncls">
            <?php
            if ($store_result['Store']['is_express_check_out'] == 1 && $store_result['Store']['paypal_email'] != '' && $store_result['Store']['paypal_password'] != '' && $store_result['Store']['paypal_signature'] != '' && !empty($storeSetting['StoreSetting']['paypal_allow']) && $finalAmount > 0) {
                if ($fChecked) {
                    $checked = "";
                } else {
                    $checked = "checked=checked";
                }
                ?>
                <input type="radio" id='express-check-out' name="payment" value=2 class="express-check-out" <?php echo $checked; ?>/> <label for="express-check-out" class="common-bold"><span></span>PayPal Express</label>
            <?php } ?>
        </span>              
    </div>
    <?php if ($finalAmount > 0) { ?>
        <!-- ===== Payment selection(Radio) types End ===== -->
        <div class="pay-option check-out-option">
            <div class="card-min-select">                    
                <?php if ($store_result['Store']['is_pay_by_credit_card'] == 1 && $store_result['Store']['api_username'] != '' && $store_result['Store']['api_password'] != '') {
                    ?>
                    </br></br>
                    <p id="field_use_nzsafe">
                        <input type="radio" id='payment_nzsafe' name="payment_vault" value="1" class="credit" />
                        <label for="payment_nzsafe" class="common-bold"><span></span>
                            <img src='../img/credit_<?= $credit_type ?>.png' class='cardimg'> Use credit card ending in <?= $credit_mask ?>
                        </label>
                        <input type="radio" id='payment_another' name="payment_vault" value="0" class="credit" />
                        <label for="payment_another" class="common-bold"><span></span> Use another credit card</label>
                    </p>

                <?php } ?>
            </div>
            <div class="card-detail pay-date-c" id='credit_payment1'>
                <div class="card-input">
                    <label>Card Type<em>*</em></label>
                    <?php
                    $cType = array('Visa' => 'Visa', 'Master' => 'MasterCard', 'Discover' => 'Discover', 'Amex' => 'American Express');
                    $cardType = array();
                    if (!empty($store_result['Store']['credit_card_type'])) {
                        $cardType = explode(',', $store_result['Store']['credit_card_type']);
                    }
                    $rType = array_flip(array_intersect(array_flip($cType), $cardType));
                    ?>
                    <?php echo $this->Form->input('Payment.creditype', array('options' => $rType, 'type' => 'select', 'id' => "CardType", 'class' => 'SlectBox user-detail', 'label' => false, 'div' => false)); ?>
                </div>
                <div class="card-input">
                    <label>Card Number <em>*</em></label>
                    <?php
                    echo $this->Form->input('cardnumber', array('id' => 'CardNumber', 'type' => 'text', 'class' => 'user-detail', 'label' => false, 'div' => false, 'placeholder' => 'Enter Card Number'));
                    echo $this->Form->error('cardnumber');
                    ?>
                </div>
                <div class="card-input">
                    <label>CVV<em>*</em></label>
                    <?php
                    echo $this->Form->input('cvv', array('id' => 'CVV', 'type' => 'text', 'class' => 'user-detail', 'label' => false, 'div' => false, 'placeholder' => 'Enter CVV'));
                    ?>
                </div>
                <div class="card-input exp-date">
                    <label>Expiration Date<em>*</em></label>
                    <?php
                    $yr = date('Y', strtotime('+20 years'));
                    for ($y = date('Y'); $y <= $yr; $y++) {
                        $year[$y] = $y;
                    }
                    $month = array('01' =>
                        '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', '10' => '10', '11' => '11', '12' => '12');
                    ?>
                    <?php
                    echo $this->Form->input('expiryMonth', array('type' => 'select', 'class' => 'SlectBox user-detail', 'label' => false, 'div' => false, 'options' => $month));
                    ?>
                    <?php echo $this->Form->input('expiryYear', array('type' => 'select', 'class' => 'SlectBox user-detail', 'label' => false, 'div' => false, 'options' => $year));
                    ?>                                
                </div>                        
            </div>    
            <div class="card-detail pay-date-c" id='credit_payment2'>                        
                <div class="card-input">
                    <label>First Name<em>*</em></label>
                    <?php
                    echo $this->Form->input('firstname', array('id' => 'first_name', 'type' => 'text', 'class' => 'user-detail', 'label' => false, 'div' => false, 'placeholder' => 'Enter First Name'));
                    echo $this->Form->error('firstname');
                    ?>
                </div>
                <div class="card-input">
                    <label>Last Name<em>*</em></label>
                    <?php
                    echo $this->Form->input('lastname', array('id' => 'last_name', 'type' => 'text', 'class' => 'user-detail', 'label' => false, 'div' => false, 'placeholder' => 'Enter Last Name'));
                    echo $this->Form->error('lastname');
                    ?>
                </div>
                <div class="card-input">
                    <label>Address<em>*</em></label>
                    <?php
                    echo $this->Form->input('address', array('id' => 'address', 'type' => 'text', 'class' => 'user-detail', 'label' => false, 'div' => false, 'placeholder' => 'Enter Address'));
                    echo $this->Form->error('address');
                    ?>
                </div>
                <div class="card-input">
                    <label>City<em>*</em></label>
                    <?php
                    echo $this->Form->input('city', array('id' => 'City', 'type' => 'text', 'class' => 'user-detail', 'label' => false, 'div' => false, 'placeholder' => 'City'));
                    echo $this->Form->error('city');
                    ?>
                </div>
                <div class="card-input">
                    <label>State<em>*</em></label>
                    <?php
                    echo $this->Form->input('state', array('id' => 'State', 'type' => 'text', 'class' => 'user-detail', 'label' => false, 'div' => false, 'placeholder' => 'State'));
                    echo $this->Form->error('state');
                    ?>
                </div>
                <div class="card-input">
                    <label>Zip code<em>*</em></label>
                    <?php
                    echo $this->Form->input('zipcode', array('id' => 'Zipcode', 'type' => 'text', 'class' => 'user-detail', 'label' => false, 'div' => false, 'placeholder' => 'Zip-Code', 'maxlength' => '5'));
                    echo $this->Form->error('zipcode');
                    ?>
                </div>
                <div class="pay-check">
                    <?php if (!$isNonUser) { ?>
                        <li>
                            <div class="input-field clearfix">
                                <?php
                                if ($customer_vault_id) {
                                    echo $this->Form->input('update_vault', array('type' => 'checkbox',
                                        'label' => array('text' => 'Update NZ Safe credit card with the information above.'), 'checked' => 'checked'));
                                } else {
                                    echo $this->Form->input('update_vault', array('type' => 'checkbox',
                                        'label' => array('text' => 'Store this credit card information to NZ Safe.',), 'checked' => 'checked'));
                                }
                                ?>
                                <div class="clr"></div>
                            </div>
                            <div class="chk-wrap">
                                <img src="../img/checkmark_25x25.png" class="checkmark"><span class="chk-span">Your credit card information will be securely encrypted and stored in NZ safe</span><br>
                                <div class="clr"></div>
                                <img src="../img/checkmark_25x25.png" class="checkmark"><span class="chk-span">NZ safe is a safe and secure feature of NZ Gateway which fully supports the latest PCI security standard</span><br>
                                <div class="clr"></div>
                            </div>
                        </li>
                    <?php } ?>
                </div>
            </div> 
        </div>
    <?php } ?>
    <?php echo $this->Form->end(); ?>
<?php } ?>
<div class="continue item-center">
    <div id="desktop_continue">
        <?php echo $this->Html->link('CONTINUE SHOPPING', array('controller' => 'products', 'action' => 'items', $encrypted_storeId, $encrypted_merchantId), array('class' => 'btn-primary save-order theme-bg-1')); ?>
    </div>
    <?php
    $guestUserDetail = $this->Session->check('GuestUser');
    $userId = AuthComponent::User('id');
    if ((!empty($userId) || !empty($guestUserDetail))) {
        ?>
        <!-- ===== Payment selection Form  start ===== -->

        <!-- ===== Express check-out form start ===== -->
        <div id="paypal-express-btn" style="display: none">
            <!-- ===== Express check-out Payment Button ===== -->
            <?php
            echo $this->Html->link('PAYMENT', array('controller' => 'payments', 'action' => 'express_checkout'), array('class' => 'btn-primary save-order theme-bg-1'));
            ?>
            <!-- ===== Express check-out Payment Button End===== -->
        </div>
        <div id="pay-credit-card">
            <!-- ===== Express check-out Payment Button ===== -->
            <?php
            echo $this->Form->button('PAYMENT', array('class' => 'changeName btn-primary save-order theme-bg-1'));
            ?>
            <!-- ===== Express check-out Payment Button End===== -->
        </div>
    <?php } ?>
</div>
<script>
    if ($('#getFinalAmount').val() == 0) {
        $("#payment2").click();
        $('.check-out-option').css('display', 'none');
    }
    $("#PaymentPaymentSectionForm").validate({
        debug: false,
        errorClass: "error",
        errorElement: 'span',
        onkeyup: false,
        rules: {
            'data[Payment][creditype]': {
                required: true
            },
            'data[Payment][firstname]': {
                required: true,
                minlength: 2,
                lettersonly: true
            },
            'data[Payment][lastname]': {
                required: true,
                minlength: 2,
                lettersonly: true
            },
            'data[Payment][address]': {
                required: true
            },
            "data[Payment][cardnumber]": {
                required: true,
                number: true,
                minlength: 13,
                maxlength: 16
            },
            "data[Payment][cvv]": {
                required: true,
                number: true,
                minlength: 3
            },
            "data[Payment][expiryDate]": {
                required: true,
                number: true,
                maxlength: 4,
                minlength: 4
            },
            "data[Payment][city]": {
                required: true,
            },
            "data[Payment][state]": {
                required: true,
            },
            "data[Payment][zipcode]": {
                required: true,
                minlength: 5,
                maxlength: 5
            }
        },
        messages: {
            'data[Payment][creditype]': {
                required: 'Please select card type.'
            },
            'data[Payment][firstname]': {
                required: 'Please enter First Name',
                minlength: 'Please Enter Atleast 2 Characters',
                lettersonly: "Only alphabates allowed"
            },
            'data[Payment][lastname]': {
                required: 'Please enter Last Name',
                minlength: 'Please enter atleast 2 characters',
                lettersonly: "Only alphabates allowed"
            },
            'data[Payment][address]': {
                required: 'Please enter address'
            },
            "data[Payment][cardnumber]": {
                required: "Please enter Card Number",
                number: "Please enter valid Card Number",
                minlength: 'Enter between 13-16 digits'
            },
            "data[Payment][cvv]": {
                required: "Plaese enter CVV",
                number: 'Please enter valid CVV',
                minlength: 'Enter atleast 3 digits'
            },
            "data[Payment][expiryDate]": {
                required: "Expiry date is required",
                number: "Only numbers are allowed"
            },
            "data[Payment][city]": {
                required: "Plaese enter city.",
            },
            "data[Payment][state]": {
                required: "Plaese enter state.",
            },
            "data[Payment][zipcode]": {
                required: "Plaese enter zip code.",
            }
        }
    });
    $(document).ready(function () {
        var use_nzsafe = $("#use_nzsafe").val();
        if (use_nzsafe) {
            $("#payment").click();
            $("#payment_nzsafe").click();
        } else {
            $("#payment").click();
            $("#field_use_nzsafe").hide();
        }
        $('.date_select').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'mmy',
            yearRange: '2015:2040',
            onClose: function (dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, month, 1));
            }
        });
        if ($("input[name='payment']").length == 1) {
            $("input[name='payment']").click();
        }
        var expresscheck = $('#express-check-out').prop('checked');
        if (expresscheck) {
            $('#credit_payment1').css('display', 'none');
            $('#credit_payment2').css('display', 'none');
            $('.other-option').css('display', 'none');
            $('.check-out-option').css('display', 'block');
            $('#field_use_nzsafe').css('display', 'none');
            $('#flashMessage').css('display', 'none');
        }
    });
</script>