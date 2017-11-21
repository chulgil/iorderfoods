<?php
$guestUser=false;
if(!AuthComponent::User() && $this->Session->check('Order.delivery_address_id')){
    $guestUser=true;
}
?>
<div class="container  single-frame">
    <div class="wrap">
        <div class="clearfix">
            <section class="form-layout sign-up no-image editable-form">
                <h2 class="success-title"> <span>Your order has been placed successfully</span> </h2>  
                <div class="payment-options success-payment-options clearfix"> 
                    <?php
                    if($guestUser){
                        $link="/users/dologin";
                    }else{
                        $link = "/products/items/" . $encrypted_storeId . "/" . $encrypted_merchantId;
                    }
                        echo $this->Form->button('Thank You!', array('type' => 'button', 'onclick' => "window.location.href='$link'", 'class' => 'btn green-btn')); 
                        echo $this->Form->button('Continue Shopping', array('type' => 'button', 'onclick' => "window.location.href='$link'", 'class' => 'btn green-btn')); 
                    ?>
            	</div>
            </section>
	</div>
	
	<?php if($this->Session->check('orderOverview')){ ?>
	<div class="col-lg-offset-3 col-lg-6">
	    <div class="content  single-frame clearfix">
		<section class="form-layout delivery-form order-overview" style="width: 100% !important;">
		    <h2> <span>Order Overview</span>  </h2>      
		    <div class="editable-form">
			<?php echo $this->element('order_overview');?>
		    </div>
		</section>
	    </div>
	</div>
    <?php } ?>
	
	
    </div>
</div>

<script>
    
    window.onpopstate = function() {        
        window.location.assign("/users/login");
        // binding this event can be done anywhere, 
        // but shouldn't be inside document ready
    };
    
    $(document).ready(function() {
        history.pushState({}, '', '#');
    });
        
 
</script>
