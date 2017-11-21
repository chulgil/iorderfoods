<?php
$url = HTTP_ROOT;?>
<?php $imageurl = HTTP_ROOT.'MenuItem-Image/';?>
<?php if (isset($productInfo) && !empty($productInfo)) { ?>
    <h2><span>Create Your Order</span></h2>
    <?php echo $this->Form->create('Item', array('url' => array('controller' => 'Products', 'action' => 'addtoCart'))); ?>	                        	
    <div>
        <div class="product-listing">
        	<div class="share-button">
                <span class="twitter-share">
                    <a target="blank" href= "http://twitter.com/share?text=<?php echo htmlspecialchars($productInfo['Item']['name']); ?>  - <?php echo nl2br(htmlspecialchars($productInfo['Item']['description'])); ?> at just  <?php echo "$" . number_format($default_price,2);?>&url=<?php echo $url; ?>&via=<?php echo $_SESSION['storeName']; ?>"><?php echo $this->Html->image('tw-share-button.png', array('alt' => 'twshare')); ?> </a>
                </span>
                <span>
                	<a class='share_button' 
                    	desc="<?php echo nl2br(htmlspecialchars($productInfo['Item']['description'])); ?>" name="<?php echo htmlspecialchars($productInfo['Item']['name']); ?>" image="<?php echo $productInfo['Item']['image'];?>" price="<?php echo "$" . number_format($default_price,2);?>" >
						<?php echo $this->Html->image('fb-share-button.png', array('alt' => 'fbshare')); ?>   
                	</a>
                </span>
                <div class="clr"></div>
            </div>
            <div class="item-pic-title">
		<?php
		$CatName='';
		$CategoryName=$this->Common->getCategoryName($productInfo['Item']['category_id']);
		if($CategoryName){
		    $CatName=$CategoryName['Category']['name']." - ";
		}
		
		?>
                <h3><?php echo $CatName.$productInfo['Item']['name']; ?> <small><?php echo nl2br($productInfo['Item']['description']); ?></small><br/></h3>
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
            <div class="row-divide clearfix">
                <h3>Available Offers</h3>
                <ul class="checkbox-listing clearfix">
                    <?php foreach($display_offer as $doff){ ?>
                        <li><i class="fa fa-pencil-square-o"></i>
                            <?php echo $doff;?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
	 
    <?php  echo $this->Form->end(); ?>
        
    </div>

    <?php } else { ?>
        <h2><span>Create Your Order</span></h2>
        <div>
            <div class="item-pic"> 
                This Item is not available right now.
            </div>
        </div>
    <?php } ?>
    

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
