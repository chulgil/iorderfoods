<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
<?php $url = HTTP_ROOT;?>
<?php $imageurl = HTTP_ROOT.'MenuItem-Image/';?>
<?php if (isset($productInfo) && !empty($productInfo)) { ?>
    <h2><span>Create Your Order</span></h2>
    <?php echo $this->Form->create('Item', array('url' => array('controller' => 'Products', 'action' => 'addtoCart'))); ?>
    <div class='loader'> <div class="loader-inner"></div> </div>	                        	
    <div>
        <div class="product-listing">
        	<div class="share-button">
                <span class="twitter-share">
                	<a class="twitter-share-button"
                        href="https://twitter.com/share"
                        data-count="none" 
                        data-url="<?php echo $url;?>"
                        data-via="<?php echo $_SESSION['storeName'];?>"
                        data-text= "<?php echo $productInfo['Item']['name']; ?>  - <?php echo $productInfo['Item']['description']; ?> at just  <?php echo "$" . number_format($default_price,2);?>" >
                	</a>
                </span>
                <span>
                	<a class='share_button' 
                    	desc="<?php echo $productInfo['Item']['description']; ?>" name="<?php echo $productInfo['Item']['name']; ?>" image="<?php echo $productInfo['Item']['image'];?>" price="<?php echo "$" . number_format($default_price,2);?>" >
						<?php echo $this->Html->image('fb-share-button.png', array('alt' => 'fbshare')); ?>   
                	</a>
                </span>
                <div class="clr"></div>
            </div>
            <div class="item-pic-title">
                <h3><?php echo $productInfo['Item']['name']; ?> <small><?php echo $productInfo['Item']['description']; ?></small></h3>
            </div>
            
            <div class="product-pic-frame">
				<div class="product-pic">
					<?php if (isset($productInfo['Item']['image']) && $productInfo['Item']['image']) {
                        $image = "/MenuItem-Image" . "/" . $productInfo['Item']['image'];
                        echo $this->Html->image($image);
                        $price_class = 'product-price';
                    } else {
                        $price_class = 'product-price product-price-single';
                    } ?>
                </div>
                <span class="<?php echo $price_class;?>">
                    <?php
                    if ($default_price) {
                        echo "$" . number_format($default_price,2);
                    }
                    ?>
                </span>
            </div>
        </div>
        <?php if (!empty($display_offer)) { ?>
            <div class="row-divide">
                <h3>Available Offers</h3>
                <ul class="checkbox-listing clearfix">
                    <?php foreach($display_offer as $doff){ ?>
                        <li><i class="fa fa-pencil-square-o"></i>
                            <?php echo $doff;?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php } 
        if ($productInfo['Item']['sizeOnly'] == 2) { // Type
            if ($productInfo['ItemType']) {      
                $options = array();
                foreach ($productInfo['ItemType'] as $type) {
                    if ($type['Type']) {

                        $options[$type['Type']['id']] = $type['Type']['name'].' ($'.number_format($type['Type']['price'],2).')';
                    
                    } else {

                    }
                }
                if ($options) { ?>	    
                    <div class="row-divide">
                        <h3>Choose Preference</h3>	
                      
                        <div class="option-value">
                           
                             <?php $k=0; foreach ($options as $type_value => $type_name) { ?>
                              <span>
                                  <?php if ($k==0){
                                      $checked = "checked = 'checked'";
                                  } else {
                                      $checked = "";
                                  } ?>
                                <input type="radio" class='item_type' id='<?php echo $type_value.'_'.$type_name;?>' name="data[Item][type]" <?php echo $checked;?> value='<?php echo $type_value;?>'>
                                <label for='<?php echo $type_value.'_'.$type_name;?>'><span></span><?php echo $type_name;?></label>
                              </span>
                              <?php $k++; }	 ?>    
                           
                        </div>
                 
                      
                    </div>
                <?php } 
            }
        } 
        if ($productInfo['Item']['sizeOnly'] == 1 ) { //Size and Price
            if ($productInfo['ItemPrice']) {
                $sizes = array();
                foreach ($productInfo['ItemPrice'] as $size) {
                    if ($size['Size']) {
                        $sizes[$size['Size']['id']] = $size['Size']['size'];
                    }
                }
                if ($sizes) { ?>
                    <div class="row-divide">
                        <h3>Choose Size</h3>
                        
                        <div class="option-value">

                            <?php $i= 0; foreach ($sizes as $size_value => $size_name) { 
                                ?>
                              <span>
                                  <?php if ($i==0){
                                      $checked = "checked = 'checked'";
                                  } else {
                                      $checked = "";
                                  }?>
                                <input type="radio" class='item_price' id='<?php echo $size_value.'_'.$size_name;?>' <?php echo $checked;?> name="data[Item][price]"  value='<?php echo $size_value;?>'>
                                <label for='<?php echo $size_value.'_'.$size_name;?>'><span></span><?php echo $size_name;?></label>
                            </span>
                              <?php $i++; }	 ?>  
                           
                        </div>
                  
                     
                    </div>	
                <?php } 
            }
        } 
        if ($productInfo['Item']['sizeOnly'] == 3) { //Both Size and Type
            if ($productInfo['ItemType']) {   
                $options = array();
                foreach ($productInfo['ItemType'] as $type) {
                    if ($type['Type']) {
                        $options[$type['Type']['id']] = $type['Type']['name'].' ($'.number_format($type['Type']['price'],2).')';
                    } 
                }
                if ($options) {  ?>
       
                    <div class="row-divide">
                        <h3>Choose Preference</h3>		    
                        
                        <div class="option-value">
                           
                            <?php $j=0; foreach ($options as $type_value => $type_name) { ?>
                             <span>
                                 <?php if ($j==0){
                                     $checked = "checked = 'checked'";
                                  } else {
                                      $checked = "";
                                  } ?>
                                <input type="radio" class='item_type' id='<?php echo $type_value.'_'.$type_name;?>' name="data[Item][type]"  <?php echo $checked;?> value='<?php echo $type_value;?>'>
                                <label for='<?php echo $type_value.'_'.$type_name;?>'><span></span><?php echo $type_name;?></label>
                                 </span>
                                 <?php $j++; }	 ?> 
                           
                        </div>
                   
                    </div>
                <?php } 
            } 
            if ($productInfo['ItemPrice']) {
                $sizes = array();
                foreach ($productInfo['ItemPrice'] as $size) {
                    if ($size['Size']) {
                        $sizes[$size['Size']['id']] = $size['Size']['size'];
                    } 
                }
                if ($sizes) { ?>
                <div class="row-divide">
                        <h3>Choose Size</h3>	
                   
                        <div class="option-value">
                             <?php $l=0; foreach ($sizes as $size_value => $size_name) { ?>
                              <span>
                                  <?php if ($l==0){
                                      $checked = "checked = 'checked'";
                                  } else {
                                      $checked = '';
                                  } ?>
                                <input type="radio" class='item_price' id='<?php echo $size_value.'_'.$size_name;?>' name="data[Item][price]" <?php echo $checked;?> value='<?php echo $size_value;?>'>
                                <label for='<?php echo $size_value.'_'.$size_name;?>'><span></span><?php echo $size_name;?></label>
                            </span>
                            <?php $l++; }	 ?>
                          
                        </div>
                   
                    </div> 
                <?php }
            }
        } 
        if ($productInfo['Topping']) { ?>
            <div class="row-divide last">
                <h3>Choose Add-on</h3>
                <ul class="checkbox-listing clearfix">
                	<li>
                            <?php foreach($productInfo['Topping'] as $topping) { ?>
                            <?php if ($topping['ItemDefaultTopping']) {
                                echo $this->Form->input('Item.defaulttoppings.' . $topping['ItemDefaultTopping'][0]['topping_id'], array('type' => 'checkbox', 'label' => $topping['name'], 'class' => 'toppings default-topping', 'value' => $topping['name'], 'checked' => true));
                            } else {
                                echo $this->Form->input('Item.toppings.' . $topping['id'], array('type' => 'checkbox', 'label' => $topping['name'].' ($'.number_format($topping['price'],2).')', 'class' => 'toppings', 'value' => $topping['id'], 'type-name' => $topping['name']));
                            } ?>                       
                        <?php } ?>
                     </li>
                </ul>
            </div>
        <?php } 
    echo $this->Form->end(); ?>
        <div class='parant_cls_no_deliverable text-center'>
        <?php if ($this->Session->check('Order')) {
                $is_delivery = $this->Session->read('Order.Item.is_deliverable');
            if ($is_delivery == 1) {
                echo $this->Form->submit('Add to cart',array('class'=>'btn green-btn'));
            } else {
                echo "<div class='message message-success'><span class='cls_no_deliverable'>This product is not deliverable for now.</span></div>";
            }
        } ?>
        </div>
    </div>

    <?php } else { ?>
        <div class='loader'> <div class="loader-inner"></div> </div>
        <legend>Create Your Order</legend>
        <div>
            <div class="item-pic"> 
                This Item is not available right now.
            </div>
        </div>
    <?php } ?>
    <script>
        $('.item_price').on('click', function () {
            var size_id = $(this).val();
            var item_id =<?php echo $this->Session->read('Order.Item.id'); ?>;
            $.ajax({
                url: "/Products/sizePrice",
                type: "Post",
                data: {sizeId: size_id, itemId: item_id},
                success: function (result) {
                    result = '$' + parseFloat(result).toFixed(2);
                    $('.product-price').html(result);
                }
            });
        });
        
        $('.item_type').on('click', function () {
            var type_id = $(this).val();
            var item_id =<?php echo $this->Session->read('Order.Item.id'); ?>;
            $.ajax({
                url: "/Products/typePrice",
                type: "Post",
                data: {typeId: type_id, itemId: item_id},
                success: function (result) {
                    result = '$' + parseFloat(result).toFixed(2);
                    $('.product-price').html(result);
                }
            });
        });
        
        $('.toppings').on('click', function () {
            var topping_id = $(this).val();
            var item_id =<?php echo $this->Session->read('Order.Item.id'); ?>;
            if (topping_id == 0) {
            } else {
                if ($(this).prop("checked") == true) {
                    var checked = 1;
                } else {
                    var checked = 0;
                }
                $.ajax({
                    url: "/Products/fetchToppingPrice",
                    type: "Post",
                    data: {'toppingId': topping_id, 'itemId': item_id, 'checked': checked},
                    success: function (result) {
                        if (result) {
                            result = '$' + parseFloat(result).toFixed(2);
                            $('.product-price').html(result);
                        }
                    }
                });
            }
        });

        $('#ItemFetchProductForm').on('submit', function (e) {
            e.preventDefault();
            $('input[type="submit"]').attr('disabled', 'disabled');
            $(".loader").css('display', 'block');

            $.ajax({
                type: 'post',
                url: '/Products/cart',
                data: $(this).serialize(),
                success: function (result) {
                    $(".loader").css('display', 'none');
                    $('input[type="submit"]').removeAttr('disabled');
                    if (result) {
                        $('.online-order').html(result);
                    }
                }
            });
        });
    </script>



<div id="fb-root"></div>
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
    name = $(this).attr('name');
    price = $(this).attr('price');
    image = $(this).attr('image');
e.preventDefault();
FB.ui(
{
method: 'feed',
name: name + '- <?php echo $_SESSION['storeName'];?>',
link: '<?php echo $url;?>',
picture: '<?php echo $imageurl;?>'+image,
caption: 'Price - '+price,
description: description,
message: ''
});
});
});
</script>