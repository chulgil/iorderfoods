<?php
echo $this->Html->css('popup');
$popupstatus = $this->Common->popupallowed();
if ($popupstatus) {
    echo $this->element('modal/order_login');
}
echo $this->element('modal/store_close');
?>

<div class='online-order'>
    <div class="col-3 mid-col " >
        <div id="selectOrderTypes" class="isolated form-layout form-layout-fixed scroll-div float-left itemCtp">
            <?php echo $this->element('design/oldlayout/element/item-pannel'); ?>
        </div>
    </div>
    <div class="col-3 last-col" id="cartstart">
        <div id="isolated"  class="isolated form-layout form-layout-fixed scroll-div float-right">
            <?php echo $this->element('design/oldlayout/element/cart-element'); ?>
        </div>
    </div>
</div>
</div>
<?php
echo $this->Form->input('orderId', array('type' => 'hidden', 'value' => $orderId));
echo $this->Form->input('encryptedStoreId', array('type' => 'hidden', 'value' => $encrypted_storeId));
echo $this->Form->input('encryptedMerchantId', array('type' => 'hidden', 'value' => $encrypted_merchantId));
?>

<style>
    a:hover {
        text-decoration: none;
    }
    [data-tooltip] {
        position: relative;
        z-index: 2;
        cursor: pointer;
    }
    [data-tooltip]:before,
    [data-tooltip]:after {
        visibility: hidden;
        -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
        filter: "progid: DXImageTransform.Microsoft.Alpha(Opacity=0)";
        opacity: 0;
        pointer-events: none;
    }
    [data-tooltip]:before {
        position: absolute;
        bottom: 150%;
        left: 50%;
        margin-bottom: 5px;
        margin-left: -80px;
        padding: 7px;
        width: 160px;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        background-color: #000;
        background-color: hsla(0, 0%, 20%, 0.9);
        color: #fff;
        content: attr(data-tooltip);

        white-space: pre-line;
        text-align: left;
        font-size: 14px;
        line-height: 1.2;
    }
    [data-tooltip]:after {
        position: absolute;
        bottom: 150%;
        left: 50%;
        margin-left: -5px;
        width: 0;
        border-top: 5px solid #000;
        border-top: 5px solid hsla(0, 0%, 20%, 0.9);
        border-right: 5px solid transparent;
        border-left: 5px solid transparent;
        content: " ";
        font-size: 0;
        line-height: 0;

    }
    [data-tooltip]:hover:before,
    [data-tooltip]:hover:after {
        visibility: visible;
        -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";
        filter: "progid: DXImageTransform.Microsoft.Alpha(Opacity=100)";
        opacity: 1;
    }

    @media (max-width:55em) {
        #desktop_continue{
            display:none;
        }

        #mobile_continuemenu{
            display:block;
            text-align: center;
        }

        #mobile_continue{
            display:block;
        }
    }

    @media (min-width:55em) {
        #desktop_continue{
            display:block;
        }

        #mobile_continuemenu{
            display:none;
        }

        #mobile_continue{
            display:none;
        }
    }
</style>
<style>
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
        opacity: 1
    }
</style>


