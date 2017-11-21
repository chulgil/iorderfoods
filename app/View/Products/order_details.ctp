
<?php
if (DESIGN == 1) {
    if ($store_data_app['Store']['store_theme_id'] == 11) {
        ?>
        <div class="ext-menu">
            <div class="ext-menu-title">
                <h4>ORDER OVERVIEW</h4>
            </div>
        </div>
    <?php } ?>

    <?php
    echo $this->element('design/aaron/orderoverview');
} elseif (DESIGN == 2) {
    echo $this->element('design/chloe/orderoverview');
} elseif (DESIGN == 3) {
    echo $this->element('design/dasol/orderoverview');
} elseif (DESIGN == 4) {
    echo $this->element('design/oldlayout/product/order_details');
}
?>



<style>
    .orderChangeMsg {
        font-size: 18px !important;
        font-weight: 500 !important;
        margin-bottom: 15px;
        padding: 5px !important;
    }
    .nDeliverable{
        font-size: 12px;
        color: #ff3333;
    }

    .cardimg{display: inline-block !important;width:40px !important;max-width: none !important;}
    #field_use_nzsafe{margin-bottom: 10px;}
    .single-frame .form-layout .btn{
        min-width:auto;
    }
    .singleItemRemove{
        color:#4d4d4d !important;
    }
    @media (max-width:60em) {
        #mobile_save{
            display:block;
        }

        /*        #desktop_save{
                    display:none;
                }*/

    }

    @media (min-width:60em) {
        #mobile_save{
            display:none;
        }

        /*        #desktop_save{
                    display:block;
                }*/
    }



</style>

<?php //echo $this->element('modal/error_popup_old')  ?>


<script>

    function paymentForm() {
        $.ajax({
            type: 'POST',
            url: '/products/paymentForm',
            async: false,
            success: function (response) {
                if (response) {
                    $('.paymentForm').addClass("hidden");
                    $('.paymentForm').html(response);
                    if (!($("#getFinalAmount").val() > 0)) {
                        if ($("#designType").val() == 1) {
                            $("#accordion1 div.panel.panel-default:last").addClass('hidden');
                        } else if ($("#designType").val() == 4) {
                            $(".makePayment").html("Check Out");
                            $(".payoptioncls").addClass("hidden");
                        }
                    }
                    $('.paymentForm').removeClass("hidden");
                }
            }
        });
    }
    function tipCalc() {
        var tip = $("#OrderTip").val();
        var tipvalue = $("#OrderTipValue").val();
        var tipselect = $("#OrderTipSelect").val();
        var subTotal = $('td.sub-total').attr('core-sub-total');
        $.ajax({
            type: 'post',
            url: '/Products/addTip',
            data: {tip: tip, tipvalue: tipvalue, tipselect: tipselect, subTotal: subTotal},
            success: function (result) {
                if (result) {
                    $('.editable-form').html(result);
                    paymentForm();
                }
            },
            complete: function (result) {
                $.ajax({
                    type: 'post',
                    url: '/Products/getlatesttotalamont',
                    data: {},
                    success: function (result) {
                        if (result != '') {
                            result = jQuery.parseJSON(result);
                            $("#expressAmount").val(result.expressAmount);              //console.log($("#expressAmount"));
                            $("#expressItemNumber").val(result.expressItemNumber);
                            $("#expressCustom").val(result.expressCustom);
                        }
                    }
                });
                if (tip == 1) {
                    $("#payment2").click();
                    $("#pccLabel").hide();//credit card
                    $("#pexpcLabel").hide();//paypal
                } else if (tip == 2) {
                    $("#payment1").click();
                    $("#pcodLabel").hide();//cod
                    $("#pexpcLabel").hide();//paypal
                }
            }
        });
    }

    $('#OrderTipValue').keypress(function (e) {
        var regex = new RegExp("^[$0-9.]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }

        e.preventDefault();
        return false;
    });

    $(document).ready(function () {
        $(document).on('click', '.itemRemove', function () {
            var index_id = $(this).attr('id');
            $('#loading').show();
            $.ajax({
                type: 'post',
                url: '/Products/removeOrderItem',
                data: {'index_id': index_id},
                success: function (result) {
                    if ($.trim(result).length == 0) {
                        window.location = "/Products/items/<?php echo $encrypted_storeId; ?>/<?php echo $encrypted_merchantId; ?>";
                    } else {
                        $('.editable-form').html(result);
                        paymentForm();
                    }
                },
                complete: function (result) {
                    $.ajax({
                        type: 'post',
                        url: '/Products/getlatesttotalamont',
                        data: {},
                        success: function (result) {
                            if (result != '') {
                                result = jQuery.parseJSON(result);
                                $("#expressAmount").val(result.expressAmount);
                                $("#expressItemNumber").val(result.expressItemNumber);
                                $("#expressCustom").val(result.expressCustom);
                            }
                        }
                    });

                    $('#loading').hide();
                }
            });
        });
        $(document).on('click', '.singleItemRemove', function () {
            var cart_index_id = $(this).parent().attr('id'); //index of session array 
            var offer_index_id = $(this).attr('value'); //index of session arrayd
            $.ajax({
                type: 'post',
                url: '/Products/removeOrderOfferItem',
                data: {'cart_index_id': cart_index_id, 'offer_index_id': offer_index_id},
                success: function (result) {
                    if (result) {
                        $('.editable-form').html(result);
                    }
                }
            });
        });
        $(document).on('change', '#OrderTip', function () {
            tipCalc();
        });
        $(document).on('blur', '#OrderTipValue', function () {
            tipCalc();
        });
        $(document).on('change', '#OrderTipSelect', function () {
            tipCalc();
        });
        $(document).on('change', '.tip-select', function () {
            var tipSelect = $(this).val();
            if (tipSelect == 2)
            {
                $("#OrderTipValue").removeClass('hidden');
                $("#OrderTipSelect").addClass('hidden');
            } else if (tipSelect == 3)
            {
                $("#OrderTipValue").addClass('hidden');
                $("#OrderTipSelect").removeClass('hidden');
            } else {
                $("#OrderTipValue").addClass('hidden');
                $("#OrderTipSelect").addClass('hidden');
            }
        });

        // To Show
        $(document).on('change', '#pre-order', function (e) {
            $('.pay-date').removeClass("hidden");
        });

        // To hide
        $(document).on('change', '#now', function (e) {
            $('.pay-date').addClass("hidden");
        });


        if ($('#now').is(':checked')) {
            $('.pay-date').addClass("hidden");
        }


        $(document).on('click', '#pay-credit-card', function (event) {
            event.preventDefault();
            var payment = $('input:radio[name=payment]:checked').val();
            var check = makeOrder();
            if (check.status == 'Error') {
                $("#errorPop").modal('show');
                $("#errorPopMsg").html(check.msg);
                return false;
            }
            if (payment == 1) {
                var cardCheck = creditForm();
                if (!cardCheck) {
                    return false;
                }
            }
            if ($('#PaymentPaymentSectionForm').valid()) {
                $.blockUI({css: {
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: .5,
                        color: '#fff'
                    }});
                $("#PaymentPaymentSectionForm").submit();
            } else {
                return false;
            }
        });
        $(document).on('click', '#paypal-express-btn', function (event) {
            event.preventDefault();
            var check = makeOrder();
            if (check.status == 'Error') {
                //alert(check.msg);
                $("#errorPop").modal('show');
                $("#errorPopMsg").html(check.msg);
                return false;
            } else if (check.status == 'Success') {
                window.location = "/payments/express_checkout";
            }
        });

        $(document).on('click', '#desktop_save,#mobile_save', function (event) {
            event.preventDefault();
            var check = makeOrder();
            if (check.status == 'Error') {
                //alert(check.msg);
                $("#errorPop").modal('show');
                $("#errorPopMsg").html(check.msg);
                return false;
            } else if (check.status == 'Success') {
                window.location = "/payments/paymentSection";
            }
        });
        function toggleChevron(e) {
            $(e.target)
                    .prev('.panel-heading')
                    .find("i.indicator")
                    .toggleClass('fa-angle-down fa-angle-up');
        }
        $('#accordion1').on('hidden.bs.collapse', toggleChevron);
        $('#accordion1').on('shown.bs.collapse', toggleChevron);

        $('#credit_payment1').css('display', 'none');
        $('#credit_payment2').css('display', 'none');
        $('.check-out-option').css('display', 'none');

        $(document).on('change', '#payment', function () {
            validator.resetForm();
            if (use_nzsafe) {
                $('#field_use_nzsafe').css('display', 'block');
            } else {
                $('#credit_payment1').css('display', 'block');
                $('#credit_payment2').css('display', 'block');
                $('.check-out-option').css('display', 'none');
                $('.other-option').css('display', 'block');
            }
        });

        $(document).on('change', '#payment1', function () {
            validator.resetForm();
            $('#credit_payment1').css('display', 'block');
            $('#credit_payment2').css('display', 'block');
            $('.check-out-option').css('display', 'none');
            $('.other-option').css('display', 'block');
            $('#field_use_nzsafe').css('display', 'none');
            $('#flashMessage').css('display', 'none');

        });
        $(document).on('change', '#payment2', function () {
            validator.resetForm();
            $('#credit_payment1').css('display', 'none');
            $('#credit_payment2').css('display', 'none');
            $('.check-out-option').css('display', 'none');
            $('.other-option').css('display', 'block');
            $('#field_use_nzsafe').css('display', 'none');
            $('#flashMessage').css('display', 'none');
        });

        $(document).on('change', '#payment_nzsafe', function () {
            validator.resetForm();
            $('#credit_payment1').css('display', 'none');
            $('#credit_payment2').css('display', 'none');
            $('.check-out-option').css('display', 'none');
            $('.other-option').css('display', 'block');
        });

        $(document).on('change', '#payment_another', function () {
            validator.resetForm();
            $('#credit_payment1').css('display', 'block');
            $('#credit_payment2').css('display', 'block');
            $('.check-out-option').css('display', 'none');
            $('.other-option').css('display', 'block');
            $('#flashMessage').css('display', 'none');
        });

        var use_nzsafe = $("#use_nzsafe").val();
        if (use_nzsafe) {
            $("#payment").click();
            $("#payment_nzsafe").click();
        } else {
            $("#payment").click();
            $("#field_use_nzsafe").hide();
        }

        $(document).on('change', '#express-check-out', function () {
            validator.resetForm();
            $('#credit_payment1').css('display', 'none');
            $('#credit_payment2').css('display', 'none');
            $('.other-option').css('display', 'none');
            $('.check-out-option').css('display', 'block');
            $('#field_use_nzsafe').css('display', 'none');
            $('#flashMessage').css('display', 'none');
        });

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

        $("form").submit(function () {
            var paytype = $("input[name='payment']").val();
            if (paytype == 2) {
                event.preventDefault();
                var comment = $("#UserComment").val();
                $.ajax({
                    type: 'POST',
                    url: '/Payments/paymentSection',
                    data: {payment: paytype, comment: comment},
                    success: function (response) {
                        if (response == 1) {
                            $("input[name='payment']").val("0");
                            $("form").submit();
                        } else {

                        }
                    }
                });
            }
        });


        var expresscheck = $('#express-check-out').prop('checked');
        if (expresscheck) {
            $('#credit_payment1').css('display', 'none');
            $('#credit_payment2').css('display', 'none');
            $('.other-option').css('display', 'none');
            $('.check-out-option').css('display', 'block');
            $('#field_use_nzsafe').css('display', 'none');
            $('#flashMessage').css('display', 'none');
        }
        //paymentForm();
        tipCalc();
    });

    //if(($("input[name='payment']:checked").val()==1) || ($("input[name='payment']:checked").val()==2)){
    var validator = $("#PaymentPaymentSectionForm").validate({
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
            'data[Payment][city]': {
                required: true
            },
            'data[Payment][state]': {
                required: true
            },
            'data[Payment][zipcode]': {
                required: true,
                number: true,
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
        },
        messages: {
        }, highlight: function (element, errorClass) {
            $(element).removeClass(errorClass);
        }
    });
    //}

    function creditForm() {

        var payment = $('input:radio[name=payment]:checked').val();
        var payment_vault = $('input:radio[name=payment_vault]:checked').val();
        if (payment != 1)
            return true;
        if (payment_vault == 1)
            return true;

        myCardNo = document.getElementById('CardNumber').value;
        myCardType = document.getElementById('CardType').value;
        if (myCardNo) {
            if (checkCreditCard(myCardNo, myCardType)) {
                return true;
            } else {
                $("#errorPop").modal('show');
                $("#errorPopMsg").html(ccErrors[ccErrorNo]);
                return false;
                //alert(ccErrors[ccErrorNo]);
            }
            return false;
        } else {
            return true;
        }

    }



    var ccErrorNo = 0;
    var ccErrors = new Array();

    ccErrors [0] = "<?php echo __('Unknown card type'); ?>";
    ccErrors [1] = "<?php echo __('No card number provided'); ?>";
    ccErrors [2] = "<?php echo __('Credit card number is in invalid format'); ?>";
    ccErrors [3] = "<?php echo __('Credit card number is invalid'); ?>";
    ccErrors [4] = "<?php echo __('Credit card number has an inappropriate number of digits'); ?>";
    ccErrors [5] = "<?php echo __('Warning! This credit card number is associated with a scam attempt'); ?>";

    function checkCreditCard(cardnumber, cardname) {

        var cards = new Array();

        cards [0] = {name: "Visa",
            length: "13,16",
            prefixes: "4",
            checkdigit: true};
        cards [1] = {name: "Master",
            length: "16",
            prefixes: "51,52,53,54,55",
            checkdigit: true};
        cards [2] = {name: "Discover",
            length: "16",
            prefixes: "6011,622,64,65",
            checkdigit: true};
        cards [3] = {name: "Amex",
            length: "15",
            prefixes: "34,37",
            checkdigit: true};

        var cardType = -1;
        for (var i = 0; i < cards.length; i++) {
            if (cardname.toLowerCase() == cards[i].name.toLowerCase()) {
                cardType = i;
                break;
            }
        }

        if (cardType == -1) {
            ccErrorNo = 0;
            return false;
        }
        if (cardnumber.length == 0) {
            ccErrorNo = 1;
            return false;
        }
        cardnumber = cardnumber.replace(/\s/g, "");

        var cardNo = cardnumber
        var cardexp = /^[0-9]{13,19}$/;
        if (!cardexp.exec(cardNo)) {
            ccErrorNo = 2;
            return false;
        }

        if (cards[cardType].checkdigit) {
            var checksum = 0;
            var mychar = "";
            var j = 1;
            var calc;
            for (i = cardNo.length - 1; i >= 0; i--) {
                calc = Number(cardNo.charAt(i)) * j;

                if (calc > 9) {
                    checksum = checksum + 1;
                    calc = calc - 10;
                }
                checksum = checksum + calc;
                if (j == 1) {
                    j = 2;
                } else {
                    j = 1;
                }

            }
            if (checksum % 10 != 0) {
                ccErrorNo = 3;
                return false;
            }
        }

        if (cardNo == '5490997771092064') {
            ccErrorNo = 5;
            return false;
        }
        var LengthValid = false;
        var PrefixValid = false;
        var undefined;
        var prefix = new Array();
        var lengths = new Array();

        prefix = cards[cardType].prefixes.split(",");

        for (i = 0; i < prefix.length; i++) {
            var exp = new RegExp("^" + prefix[i]);
            if (exp.test(cardNo))
                PrefixValid = true;
        }
        if (!PrefixValid) {
            ccErrorNo = 3;
            return false;
        }
        lengths = cards[cardType].length.split(",");
        for (j = 0; j < lengths.length; j++) {
            if (cardNo.length == lengths[j])
                LengthValid = true;
        }

        if (!LengthValid) {
            ccErrorNo = 4;
            return false;
        }
        ;
        return true;
    }
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
    var results = '';
    function makeOrder() {
        $.ajax({
            type: 'post',
            url: '/orderOverviews/chkMinPmtAmt',
            async: false,
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
                $.unblockUI();
                results = jQuery.parseJSON(result);

            }
        });
        return results;
    }
</script>


