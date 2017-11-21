<?php
$guestUser=false;
if(!AuthComponent::User() && $this->Session->check('Order.delivery_address_id')){
    $guestUser=true;
}
?>
<div class="content  single-frame">
    <div class="wrap">
        <div class="clearfix">
            <section class="form-layout sign-up no-image editable-form">
                <h2 class="success-title"> <span>Order could not be saved due to some issue.</span> </h2>  
                <div class="payment-options success-payment-options clearfix"> 
                    <?php
                    
                        if($guestUser){
                            $link="/users/dologin";
                        }else{
                            $link="/users/login";
                        }
                        echo $this->Form->button('Thank You!', array('type' => 'button', 'onclick' => "window.location.href='$link'", 'class' => 'btn green-btn')); 
                        echo $this->Form->button('Continue Shopping', array('type' => 'button', 'onclick' => "window.location.href='$link'", 'class' => 'btn green-btn')); 
                    ?>
            	</div>
            </section>
        </div>
    </div>
</div>