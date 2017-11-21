<style>
.item-margin {
    margin: 0;
}

.form-layout.delivery-form, .form-layout.pickup-form {
    min-height: auto !important;
}

</style>
    <div class="row item-margin item-margin-top">
	<div class="form-layout delivery-form" >
	    <div class='online-order order-item-list'>        	    
		<div id="order-item" class="isolated scroll-div ">
		    <div class="col-3-structure col-3-str-menu-iteam">
			<?php //echo $this->element('menuItem/order_item'); ?>
		    </div>
		</div>
	    </div>
	</div>
	
	<div class="form-layout pickup-form">
	    <div class='online-order item-panel-list'>	    
		<div id="item-panel" class="isolated scroll-div ">
		    <?php //echo $this->element('menuItem/item_panel'); ?>
		</div>
	    </div>
	</div>
    </div>
    
</div>  