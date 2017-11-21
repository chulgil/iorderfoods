<div class="content  single-frame">
    <div class="wrap">
        <div class="clearfix">
            <section class="form-layout sign-up no-image editable-form">
                <h2 class="success-title"> <span>Order could not be placed due to some issue.
                                            </span> </h2>  
                <div class="payment-options success-payment-options clearfix"> 
                    <?php
                        echo $this->Form->button('Thank You!', array('type' => 'button', 'onclick' => "window.location.href='/users/login'", 'class' => 'btn green-btn')); 
                        echo $this->Form->button('Continue Shopping', array('type' => 'button', 'onclick' => "window.location.href='/products/items/$encrypted_storeId/$encrypted_merchantId'", 'class' => 'btn green-btn')); 
                    ?>
            	</div>
            </section>
        </div>
    </div>
</div>