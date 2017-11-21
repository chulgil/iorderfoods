<div class='loader'> 
    <div class="loader-inner"></div> 
</div>
<h2><span><?php echo __('Offers'); ?></span></h2>
<div class="offer-description"> <?php echo $getOffer['Offer']['description']; ?></div>
<?php foreach ($getOffer['OfferDetail'] as $off) { ?>
    <div>
        <div class="product-listing">
            <div class="item-pic-title">
                <h3><?php echo $off['Item']['name']; ?><small><?php echo nl2br($off['Item']['description']); ?></small></h3>
            </div>

            <div class="product-pic-frame">		 
                <div class="product-pic">   					    
                    <?php
                    if (isset($off['Item']['image']) && $off['Item']['image']) {
                        $image = "/MenuItem-Image" . "/" . $off['Item']['image'];
                        echo $this->Html->image($image);
                        $price_class = 'product-price';
                    } else {
                        $price_class = 'product-price product-price-single';
                    }
                    ?>
                </div>
                <?php if ($getOffer['Offer']['is_fixed_price'] == 1) { ?>


                    <span class="<?php echo $price_class; ?>">
                        <?php
                        echo $off['quantity'] . ' ';
                        if (!empty($off['Size'])) {
                            echo $off['Size']['size'] . ' ';
                        }
                        if (!empty($off['Type'])) {
                            echo $off['Type']['name'];
                        }
                        ?>
                    </span>

                <?php } else if ($getOffer['Offer']['is_fixed_price'] == 0) { ?> 

                    <span class="<?php echo $price_class; ?>">
                        <?php
                        echo $off['quantity'] . ' ';
                        if (!empty($off['Size'])) {
                            echo $off['Size']['size'] . ' ';
                        }
                        if (!empty($off['Type'])) {
                            echo $off['Type']['name'];
                        }
                        if ($off['discountAmt'] == 0) {
                            echo __(' Free');
                        } else {
                            echo ' @ $' . number_format($off['discountAmt'], 2);
                        }
                        ?>
                    </span>
                <?php } ?>
            </div>
        </div>
    </div>

<?php
}
if ($getOffer['Offer']['is_fixed_price'] == 1) {
    ?>
    <div class="message message-danger margin-bt20">
        <?php
        if ($getOffer['Offer']['offerprice'] == 0) {
            echo $getOffer['Item']['name'] . ' with all these items Free';
        } else {
            echo $getOffer['Item']['name'] . ' with all these items @ $' . number_format($getOffer['Offer']['offerprice'], 2);
        }
        ?>
    </div>
<?php } ?>

<div class="text-center button-frame">
    <?php
    echo $this->Form->button('Click To Redeem Offer', array('id' => 'continue', 'type' => 'button', 'class' => 'btn green-btn pink-btn margin-rt10 '));
    echo $this->Form->button('Cancel', array('id' => 'cancel', 'type' => 'button', 'class' => 'btn green-btn pink-btn'));
    ?>
</div>

<script>
    $('#continue').click(function (e) {
        $.ajax({
            type: 'post',
            url: '/Products/cart',
            //data: $(this).serialize(),
            data: {offerApply: 'YES'},
            success: function (result) {
                $('.online-order').html(result);
                if (window.screen.width < 700) {
                    $(window).scrollTop($('#cartstart').offset().top - 30);

                } else {
                    $(window).scrollTop($('#anchorName').offset().top);
                }
            }
        });
    });
    $('#cancel').on('click', function () {
        $.ajax({
            type: 'post',
            url: '/Products/cancelOffer',
            success: function (result) {
                $('.float-left').html(result);
                if (window.screen.width < 700) {
                    $(window).scrollTop($('#cartstart').offset().top - 30);
                } else {
                    $(window).scrollTop($('#anchorName').offset().top);
                }
            }
        });
    });


</script>
<style>
    .item-pic img {
        width: 35%;
    }
    .btn.green-btn.pink-btn.margin-rt10{
       margin-bottom: 5%; 
    }
</style>