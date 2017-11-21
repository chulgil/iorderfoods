<?php
//$guestUser = false;
//if (!AuthComponent::User() && $this->Session->check('Order.delivery_address_id')) {
//    $guestUser = true;
//}
//
?>
<div class="title-bar">Order Overview</div>
<div class="main-container">
    <div class="inner-wrap order-type common-white-bg">
        <div class="order-detail">
            <div class="clearfix">
                <section class="form-layout sign-up no-image editable-form">
                    <h3 class="success-title"> <span>Your order has been placed successfully</span> </h3>
                    <hr>
                    <div class="payment-options success-payment-options clearfix">
                        <?php
//                        if ($guestUser) {
//                            $link = "/users/dologin";
//                        } else {
                        $link = "/products/items/" . $encrypted_storeId . '/' . $encrypted_merchantId;
//                        }
                        //echo $this->Form->button('Thank You!', array('type' => 'button', 'onclick' => "window.location.href='$link'", 'class' => 'theme-color-1 p-save'));
                        echo $this->Form->button('Continue Shopping', array('type' => 'button', 'onclick' => "window.location.href='$link'", 'class' => 'theme-bg-1 p-save'));
                        ?>
                    </div>
                    <?php if ($this->Session->check('orderOverview')) { ?>
                        <hr>
                        <div class="">
                            <h2><span>Order Overview</span>  </h2>
                            <hr>
                            <div class="editable-form">
                                <?php echo $this->element('order_overview'); ?>
                            </div>
                        </div>
                    <?php } ?>
                </section>
            </div>
        </div>
    </div>
</div>

<script>

    window.onpopstate = function () {
        window.location.assign("/users/login");
        // binding this event can be done anywhere,
        // but shouldn't be inside document ready
    };

    $(document).ready(function () {
        history.pushState({}, '', '#');
    });


</script>