<style>
    .col-3 form-layout span {
    background-color: #45342c;
    color: #fff;
}
.col-3 form-layout h2 span {
    left: 0;
    padding: 6px 20px 7px;
    position: absolute;
    top: -40px;
 
 
}
 .wraper.no-background.clearfix > a {
    margin-left: 85%;
    background-color: #45342c;
    color: #fff;
    font-size: 20px;
}
.form-layout.pickup-form.col-3.form-layout {
    background-color: white;
}
.form-layout.delivery-form.col-3.form-layout {
    background-color: white;
}
</style>


<div class="content" style="height: 1387px;">
    <div class="wraper no-background clearfix">
        <!--<?php
        if (isset($decrypt_storeId)) {
            $store_Det = $this->Common->getStoreDet($decrypt_storeId);
            if (!empty($store_Det)) {
                ?>
                <a target='blank' href='http://<?php echo $store_Det['Store']['store_url']; ?>'>Go To Store</a>
                <?php
            }
        }
        ?>-->

        <div class="form-layout delivery-form col-3 form-layout" >
            <div class='online-order order-item-list'>        	    
                <div id="order-item" class="isolated scroll-div ">
                    <?php echo $this->element('menuItem/hq_order_item'); ?>
                </div>
            </div>
        </div>

        <div class="form-layout pickup-form col-3 form-layout hidden" id="showMenuData">
            <div class='online-order item-panel-list'>	    
                <div id="item-panel" class="isolated scroll-div ">
                    <?php //echo $this->element('menuItem/item_panel'); ?>
                </div>

            </div>
        </div>

    </div>
</div>             
	    