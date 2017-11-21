
    <div class='online-order'>
        <!--<div class="col-3 mid-col form-layout float-left" >-->
	<div class="col-3 mid-col " >
	    <div id="selectOrderTypes" class="isolated form-layout form-layout-fixed scroll-div float-left itemCtp">
		<?php echo $this->element('item-pannel'); ?>
	    </div>
	</div>
        <div class="col-3 last-col">
	    <div id="isolated"  class="isolated form-layout form-layout-fixed scroll-div float-right">
		<?php echo $this->element('cart-element');?>
	    </div>
	</div>
    </div>    
    
</div>
                                                     
<script>
$(document).ready(function() {
    var orderId = '<?php echo $orderId;?>'; 
    if(orderId){
            $.ajax({
                type: 'post',
                url: '/Products/reorder',
                data: {'orderId': orderId},					    
                success:function(result){
                    var parsedJson = $.parseJSON(result);
                    if(parsedJson.count == 0){
                        $("#errorPop").modal('show');
                        $("#errorPopMsg").html('Items are no longer available.');
                        return false; 
                    } else {
                        if(parsedJson.item >= 1){
                            $("#errorPop").modal('show');
                            $("#errorPopMsg").html('Items are no longer available.');
                            return false; 
                        } else {
                            $.ajax({
                                type: 'post',
                                url: '/Products/fetchReorderProduct',
                                data: {},					    
                                success:function(result2){
                                    if(result2 == 1){
                                       window.location = "/Products/items/<?php echo $encrypted_storeId;?>/<?php echo $encrypted_merchantId;?>";
                                    }
                                }
                            });
                        } 
                    } 
                }
            });
    }	
    });
</script>



	    