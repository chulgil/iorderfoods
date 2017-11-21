<div id="fb-root"></div>
<div class="main-container">
    <div class="ext-menu-title">
        <h4>MY BILLING INFORMATION</h4>
    </div>
    <div class="pad-TP60 clearfix">
    <?php echo $this->Session->flash(); ?>
        <div class="order-hostory form-layout clearfix margin-60">
            <div id="horizontalTab">
                <div class="inner-wrap no-border"><!-- profile -->
                    <div class="form-section profile-fsection">
            <!-- FORM VIEW -->
            <?php echo $this->Form->create('', array('id'=>'BookingForm','url'=>array('controller'=>'pannels','action'=>'myBillingInfo',$encrypted_storeId,$encrypted_merchantId))); ?>
            <br>

                    <ul class="clearfix">
                <li class="row">
                    <span class="title col-md-3"><label>Card Number</label></span>
                    <div class="title-var col-md-9"><?=$nzsafe_info['cc_number']?></div>
                </li>

                <li>
                    <span class="title" style="width:150px"><label>Expiration Date </label></span>
                    <div class="title-var"><?=$nzsafe_info['cc_exp']?></div>
                </li>
                <li>
                    <span class="title" style="width:150px"><label>Address</label></span>
                    <div class="title-var"><?=$nzsafe_info['address_1']?></div>
                </li>

                <li>
                    <span class="title" style="width:150px;"><label>City</label></span>
                    <div class="title-var"><?=$nzsafe_info['city']?></div>
                </li>
                <li>
                    <span class="title" style="width:150px"><label>First Name</label></span>
                    <div class="title-var"><?=$nzsafe_info['first_name']?></div>
                </li>

                <li>
                    <span class="title" style="width:150px;"><label>Last Name</label></span>
                    <div class="title-var"><?=$nzsafe_info['last_name']?></div>
                </li>
                <li>
                    <span class="title" style="width:150px"><label>State</label></span>
                    <div class="title-var"><?=$nzsafe_info['state']?></div>
                </li>

                <li>
                    <span class="title" style="width:150px;"><label>Zip-Code</label></span>
                    <div class="title-var"><?=$nzsafe_info['postal_code']?></div>
                </li>
            </ul>

            <div class="chk-wrap">
                <img src="../../../img/checkmark_25x25.png" class="checkmark"><span class="chk-span">You stored this billing information during the last check out process.</span><br>
                <div class="clr"></div>
                <img src="../../../img/checkmark_25x25.png" class="checkmark"><span class="chk-span">Your credit card information is securely encrypted and stored in NZ Safe.</span><br>
                <div class="clr"></div>
                <img src="../../../img/checkmark_25x25.png" class="checkmark"><span class="chk-span">NZ Safe is a safe and secure feature of NZ Gateway which fully supports the latest PCI security standard–PCI DSS.</span><br>
                <div class="clr"></div>
            </div>
            </br>
            <?php
                    if($nzsafe_info['customer_vault_id']) {
                        $link_tag = $this->Html->link($this->Html->tag('i','',
            array('class'=>'fa fa-trash-o')).' Remove this billing information from NZ Safe',
            array('controller'=>'users','action'=>'deleteBillingInfo',$encrypted_storeId,$encrypted_merchantId,$nzsafe_info['customer_vault_id']),
            array('confirm' => __('Are you sure you want to delete this Billing Information?'),'class'=>'delete','escape'=>false));
            echo '<div class="button" style="text-align: right">'.$link_tag.'</div>';
            }
            ?>

            </section>
            <?php echo $this->Form->end(); ?>
        </div>
           </div>
        </div>
    </div>
    <div class='clr'></div>

</div>
</div>
<style>

    .checkmark{ display: inline-block !important;float: left;width: 12px !important;margin-right: 10px;margin-left: 10px;margin-top:3px;vertical-align: middle;margin-bottom: 5px;}
    .chk-span{display: inline-block !important;float: left;width: 90% !important;}
    .chk-wrap{margin-bottom: 15px !important;}

    .title{font-size:13px;}
    .title-var { font-size:14px;font-weight:lighter !important;}
    .subtitle {
        padding:2px;
        font-size:15px;
    }

    .form-layout ul li{
        min-height: 40px;
        margin-bottom: 0;
    }
</style>
<!--<div id="fb-root"></div>
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
name: 'Booking',
link: '<?php echo $url;?>',
picture: '<?php echo $imageurl;?>',
caption: 'My Booking Request - <?php echo $_SESSION['storeName'];?>',
description: description,
});
});
});
</script>-->
