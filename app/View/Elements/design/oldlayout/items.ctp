<?php
echo $this->Html->css('popup');
$popupstatus = $this->Common->popupallowed();
if ($popupstatus) {
    echo $this->element('modal/order_login');
} else {
    echo $this->element('modal/login_alert');
}
echo $this->element('modal/store_close');
?>

<div class='online-order'>
    <!--<div class="col-3 mid-col form-layout float-left" >-->
    <div class="col-3 mid-col " >
        <div id="selectOrderTypes" class="isolated form-layout form-layout-fixed scroll-div float-left itemCtp">
            <?php echo $this->element('item-pannel'); ?>
        </div>
    </div>
    <div class="col-3 last-col" id="cartstart">
        <div id="isolated"  class="isolated form-layout form-layout-fixed scroll-div float-right">
            <?php echo $this->element('cart-element'); ?>
        </div>
    </div>
</div>

<style>
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
        opacity: 1
    }
</style>



