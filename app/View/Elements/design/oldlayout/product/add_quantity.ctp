<!--<div class="col-3 mid-col form-layout float-left">-->
<div class="col-3 mid-col ">
    <div id="selectOrderTypes" class="isolated form-layout form-layout-fixed scroll-div float-left addQualityCtp">
        <?php
        if (!empty($getOffer)) {
            echo $this->element('design/oldlayout/element/item-offer');
            //echo $this->element('item-offer');
        } else {
            echo $this->element('design/oldlayout/element/item-pannel');
            //echo $this->element('item-pannel');
        }
        ?>
    </div>
</div>

<div class="col-3 last-col" id="cartstart">
    <div id="isolated"  class="isolated form-layout form-layout-fixed scroll-div float-right">
        <?php
        echo $this->element('design/oldlayout/element/cart-element');
        //echo $this->element('cart-element');
        ?>
    </div>
</div>


