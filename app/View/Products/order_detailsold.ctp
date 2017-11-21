<?php $url = HTTP_ROOT;?>
<div class="content  single-frame clearfix">
    <section class="form-layout delivery-form order-overview">
        <h2> <span>Order Overview</span> </h2>   
        <div class="editable-form">
            
            <ul class="order-overview-listing clearfix">
                <?php
                $desc ='';
                $total_sum = 0;
                $total_of_items = 0;
                $ordertype = "";
                $total_of_extra = 0;
                foreach ($finalItem as $item) {
                    if(isset($item['Item']['OfferItemName'])){
                        $data = strip_tags($item['Item']['OfferItemName']);
                        $offerItemName = explode('x',$data);
                        unset($offerItemName[0]);
                        $offerName = implode("<br/>",$offerItemName);
                    }
                    $ordertype = $item['order_type'];
                    $total_sum = $total_sum + $item['Item']['final_price'];
                    $total_of_items = $total_of_items + $item['Item']['final_price'];
                    if(isset($offerName)){
                        $desc .= $item['Item']['quantity'].'X'. $item['Item']['name'].' ( Offer Items: '. $offerName.' ) @ $'.number_format($item['Item']['final_price'], 2).', ';
                    } else {
                        $desc .= $item['Item']['quantity'].'X'. $item['Item']['name'].' @ $'.number_format($item['Item']['final_price'], 2).', ';
                    } ?>
                
                    <li>
                        <span class="title"><label><?php echo $item['Item']['quantity']; ?> X <?php echo @$item['Item']['size'].' '.@$item['Item']['type'].' '.$item['Item']['name']; ?><br/><item style='font-size: 12px;'><?php echo @$offerName; ?></item></label></span>
                        <div class="title-box">$<?php echo number_format($item['Item']['final_price'], 2); ?></div>
                    </li>
<?php } ?>

                <li>
                    <span class="title"><label>Discount</label></span>
                    <div class="title-box">$
                        <?php
                        if (isset($_SESSION['Coupon'])) {
                            if ($_SESSION['Coupon']['Coupon']['discount_type'] == 1) { // Price
                                $discount_amount = $_SESSION['Coupon']['Coupon']['discount'];
                                echo number_format($discount_amount,2);
                                $total_of_items = $total_of_items - $discount_amount;
                            } else { // Percentage
                                $discount_amount = $total_of_items * $_SESSION['Coupon']['Coupon']['discount'] / 100;
                                echo number_format($discount_amount,2);
                                $total_of_items = $total_of_items - $discount_amount;
                            }
                        } else {
                            $discount_amount = 0;
                            echo number_format($discount_amount,2);
                            $total_of_items = $total_of_items - $discount_amount;
                        }
                        $_SESSION['Discount'] = $discount_amount;
                        ?>
                    </div>
                </li> 

<?php if (isset($_SESSION['delivery_fee']) && $ordertype == 3 && ($_SESSION['delivery_fee'] > 0)) {
    $total_of_extra = $total_of_extra + $this->Session->read('delivery_fee');
    ?>
                    <li>
                        <span class="title"><label>Delivery Fee</label></span>
                        <div class="title-box">$<?php echo number_format($this->Session->read('delivery_fee'), 2); ?></div>
                    </li>            
<?php
}

if (isset($_SESSION['service_fee']) && ($_SESSION['service_fee'] > 0)) {
    $total_of_extra = $total_of_extra + $this->Session->read('service_fee');
    ?>
                    <li>
                        <span class="title"><label>Service Fee</label></span>
                        <div class="title-box">$<?php echo number_format($this->Session->read('service_fee'), 2); ?></div>
                    </li>          
<?php } ?>



                <li>
        <?php $total_sum = $total_of_items + $total_of_extra; ?>
                    <span class="title"><label>Total</label></span>
                    <div class="title-box">$<?php echo number_format($total_sum, 2); ?></div>
                </li>            
            </ul>
            <div class="share-button order-share-button">
                <span>
                    <a class='share_button' 
                        desc="Items : <?php echo $desc; ?> Discount : $<?php echo number_format($discount_amount,2);?>, Extra Charges: $<?php echo number_format($total_of_extra, 2);?>, Total Payable Amount : $<?php echo number_format($total_sum, 2); ?>" >
                     <?php echo $this->Html->image('fb-share-button.png', array('alt' => 'fbshare')); ?>   
                    </a> 
                </span>
                
                <span class="twitter-share">
                	<a class="twitter-share-button"
                      href="https://twitter.com/share"
                      data-url="<?php echo $url;?>"
                      data-count="none" 
                      data-via="<?php echo $_SESSION['storeName'];?>"
                      data-text= "Items : <?php echo $desc; ?> Discount : <?php echo $discount_amount;?>, Extra Charges: $<?php echo number_format($total_of_extra, 2);?>, Total Payable Amount : $<?php echo number_format($total_sum, 2); ?>" ></a>
            	</span>
            </div>
        </div>

<?php echo $this->Form->create('Payment', array('controller' => 'payments', 'action' => 'paymentSection', 'onsubmit' => 'return testCreditCard();')); ?>

        <ul class="special-comment">
            <li>
                <span class="title"><label>Special Comment </label></span>
                <div class="title-box"><?php echo $this->Form->input('User.comment', array('type' => 'textarea', 'label' => false, 'class' => 'inbox')); ?></div>
            </li>
        </ul>
        <div class="clr"></div>
        
        <div class="radio-btn space20 delivery-address-option">
            <input type="radio" id='payment' name="payment" checked="checked" value=1 class="credit" /><label for="payment"><span></span>Pay by Credit Card</label>
            <input type="radio" id='payment1' name="payment" value=2 class="credit" /><label for="payment1"><span></span>Pay by Paypal</label>

<?php if (AuthComponent::User()) { ?>
                <input type="radio" id='payment2' name="payment" value=3 class="cod" /> <label for="payment2"><span></span>Cash on Delivery</label>
<?php } ?>
        </div>
<?php echo $this->Session->flash(); ?>
        <ul id='credit_payment1' class="clearfix">
            <li>
                <span class="title"><label>First Name <em>*</em></label></span>
                <div class="title-box"><?php echo $this->Form->input('firstname', array('type' => 'text', 'class' => 'inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter First Name'));
echo $this->Form->error('firstname'); ?></div>
            </li>
            <li>
                <span class="title"><label>Last Name <em>*</em></label></span>
                <div class="title-box"><?php echo $this->Form->input('lastname', array('type' => 'text', 'class' => 'inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter Last Name'));
echo $this->Form->error('lastname'); ?></div>
            </li>
            <li>
                <span class="title"><label>Address <em>*</em></label></span>
                <div class="title-box"><?php echo $this->Form->input('address', array('type' => 'text', 'class' => 'inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter Address'));
echo $this->Form->error('address'); ?></div>
            </li>
        </ul>
        <ul id='credit_payment2' class="clearfix">
            <li>
                <span class="title"><label>Card Type <em>*</em></label></span>
                <div class="title-box"><select name="data[Payment][creditype]" id="CardType" class='inbox'>
                    <option value="Visa" selected="selected"><?php echo __('Visa'); ?></option>
                    <option value="MasterCard"><?php echo __('MasterCard'); ?></option>
                    <option value="Discover"><?php echo __('Discover'); ?></option>
                    <option value="Amex"><?php echo __('American Express'); ?></option>
                </select></div>
            </li>
            <li>
                <span class="title"><label>Card Number <em>*</em></label></span>
                <div class="title-box"><?php echo $this->Form->input('cardnumber', array('id' => 'CardNumber', 'type' => 'text', 'class' => 'inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter Card Number'));
echo $this->Form->error('cardnumber'); ?></div>
            </li>
            <li>
                <span class="title"><label>CVV <em>*</em></label></span>
                <div class="title-box"><?php echo $this->Form->input('cvv', array('id' => 'CVV', 'type' => 'text', 'class' => 'inbox', 'label' => false, 'div' => false, 'placeholder' => 'Enter CVV')); ?></div>
            </li>
            <li>
                <span class="title"><label>Expiry-Date <em>*</em></label></span>
                <div class="title-box"><?php echo $this->Form->input('expiryDate', array('type' => 'text', 'class' => 'inbox date_select', 'label' => false, 'div' => false, 'placeholder' => 'Expiry Date(mmyy)', 'readOnly' => true)); ?></div>
            </li>
        </ul>
		<div class="clr"></div>
        <div class="payment-options text-center clearfix">
<?php
echo $this->Form->button('Proceed to Payment', array('type' => 'submit', 'class' => 'btn green-btn'));
echo $this->Form->button('Continue Shopping', array('type' => 'button', 'onclick' => "window.location.href='/products/items/$encrypted_storeId/$encrypted_merchantId'", 'class' => 'btn green-btn'));
?>
        </div>
                    <?php echo $this->Form->end(); ?>
    </section>
    <section class="form-layout pickup-form">
<?php if ($delivery_address) {
    if (AuthComponent::User()) {
        ?>
                <h2> <span>Delivery Address</span> </h2>     
                <div class="address">
                    <address class="inbox">
                        <p><?php
        echo $delivery_address['DeliveryAddress']['name_on_bell'] . '<br>' . $delivery_address['DeliveryAddress']['address']
        . '<br>' . $delivery_address['DeliveryAddress']['city'] . ', ' . $delivery_address['DeliveryAddress']['state']
        . ', ' . $delivery_address['DeliveryAddress']['zipcode'] . '<br>' . $delivery_address['CountryCode']['code'] . '' . $delivery_address['DeliveryAddress']['phone'];
        ?></p>
                    </address>
                </div>
            <?php } else {
                if ($_SESSION['Order']['order_type'] == 3) {
                    ?>
                    <h2> <span>Delivery Address</span> </h2>     
                    <div class="address">
                        <address class="inbox">
                            <p><?php
                                echo $delivery_address['DeliveryAddress']['email'] . '<br>' . $delivery_address['DeliveryAddress']['name_on_bell'] . '<br>' . $delivery_address['DeliveryAddress']['address']
                                . '<br>' . $delivery_address['DeliveryAddress']['city'] . ', ' . $delivery_address['DeliveryAddress']['state']
                                . ', ' . $delivery_address['DeliveryAddress']['zipcode'] . '<br>' . $delivery_address['CountryCode']['code'] . '' . $delivery_address['DeliveryAddress']['phone'];
                                ?></p>
                        </address>
                    </div>
        <?php } else { ?>
                    <h2> <span>Your Information</span> </h2>    
                    <div class="address">
                        <address class="inbox">
                            <p><?php
            echo $delivery_address['DeliveryAddress']['email'] . '<br>' . $delivery_address['DeliveryAddress']['name_on_bell'] . '<br>' . $delivery_address['CountryCode']['code'] . '' . $delivery_address['DeliveryAddress']['phone'];
            ?></p>
                        </address>
                    </div>
        <?php }
    }
} else { ?>
     <h2> <span>Store Address</span> </h2>     
                <div class="address">
                    <address class="inbox">
                        <p><?php
        echo $store_result['Store']['store_name'] . '<br>' . $store_result['Store']['address']
        . '<br>' . $store_result['Store']['city'] . ', ' . $store_result['Store']['state']
        . ', ' . $store_result['Store']['zipcode'] . '<br>' .$store_result['Store']['phone'];
        ?></p>
                    </address>
                </div>
<?php }
?>
    </section>
</div>
<style>
    .ui-datepicker-calendar { display: none !important; }
</style>
<script>
    $('#credit_payment1').css('display', 'none');
    $('#payment').click(function () {
        validator.resetForm();
        $('#credit_payment1').css('display', 'none');
        $('#credit_payment2').css('display', 'block');
    });
    $('#payment1').click(function () {
        validator.resetForm();
        $('#credit_payment1').css('display', 'block');
        $('#credit_payment2').css('display', 'block');
    });
    $('#payment2').click(function () {
        validator.resetForm();
        $('#credit_payment1').css('display', 'none');
        $('#credit_payment2').css('display', 'none');
    });
    $('.date_select').datepicker( {
        changeMonth: true,
        changeYear: true,
        dateFormat: 'mmy',
        yearRange: '2015:2040',
        onClose: function(dateText, inst) { 
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(year, month, 1));
        }
    });
    var validator = $("#PaymentPaymentSectionForm").validate({
        rules: {
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
                maxlength:16
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
            }
        },
        messages: {
            'data[Payment][firstname]': {
                required: 'Please enter First Name',
                minlength: 'Please Enter Atleast 2 Characters',
                lettersonly:"Only alphabates allowed"
            }, 
            'data[Payment][lastname]': {
                required: 'Please enter Last Name',
                minlength: 'Please enter atleast 2 characters',
                lettersonly:"Only alphabates allowed"
            },
            'data[Payment][address]': {
                required: 'Please enter address'
            },
            "data[Payment][cardnumber]": {
                required: "Please enter Card Number",
                number: "Please enter valid Card Number",
                minlength: 'Enter between 13-16 digits',
            },
            "data[Payment][cvv]": {
                required: "Plaese enter CVV",
                number: 'Please enter valid CVV',
                minlength: 'Enter atleast 3 digits',
            },
            "data[Payment][expiryDate]": {
                required: "Expiry date is required",
                number: "Only numbers are allowed",
            }
        }
    });

    function testCreditCard() {
        myCardNo = document.getElementById('CardNumber').value;
        myCardType = document.getElementById('CardType').value;
        if(myCardNo){
            if (checkCreditCard(myCardNo, myCardType)) {
                return true;
            } else {
                alert(ccErrors[ccErrorNo]);
            }
            return false;
        } else {
            return true;
        }
    }

    var ccErrorNo = 0;
    var ccErrors = new Array()

    ccErrors [0] = "<?php echo __('Unknown card type'); ?>"
    ccErrors [1] = "<?php echo __('No card number provided'); ?>"
    ccErrors [2] = "<?php echo __('Credit card number is in invalid format'); ?>"
    ccErrors [3] = "<?php echo __('Credit card number is invalid'); ?>"
    ccErrors [4] = "<?php echo __('Credit card number has an inappropriate number of digits'); ?>"
    ccErrors [5] = "<?php echo __('Warning! This credit card number is associated with a scam attempt'); ?>"

    function checkCreditCard(cardnumber, cardname) {

        var cards = new Array();

        cards [0] = {name: "Visa",
            length: "13,16",
            prefixes: "4",
            checkdigit: true};
        cards [1] = {name: "MasterCard",
            length: "16",
            prefixes: "51,52,53,54,55",
            checkdigit: true};
        cards [2] = {name: "Discover",
            length: "16",
            prefixes: "6011,622,64,65",
            checkdigit: true};
        cards [3] = {name: "AmEx",
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
                    j = 2
                } else {
                    j = 1
                }
                ;
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



</script>

<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>

<div id="fb-root"></div>
<script>
window.fbAsyncInit = function() {
FB.init({appId: '595206160619283', status: true, cookie: true,
xfbml: true});
};
(function() {
var e = document.createElement('script'); e.async = true;
e.src = document.location.protocol +
'//connect.facebook.net/en_US/all.js';
document.getElementById('fb-root').appendChild(e);
}());
</script>
<script type="text/javascript">
$(document).ready(function(){
$('.share_button').click(function(e){
    description = $(this).attr('desc');
e.preventDefault();
FB.ui(
{
method: 'feed',
name: 'Order Detail',
link: '<?php echo $url;?>',
caption: 'Full Order Summary - <?php echo $_SESSION['storeName'];?>',
description: description,
message: ''
});
});
});
</script>