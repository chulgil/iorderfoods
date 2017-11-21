<div class="modal-dialog clearfix">
    <div class="common-title">
        <h3>OFFER DETAILS</h3>
    </div>
    <button aria-label="Close" data-dismiss="modal" class="close" type="button">×</button>
    <div class="product-detail clearfix">
        <?php foreach ($getOffer['OfferDetail'] as $off) { ?>
            <h3><?php echo $off['Item']['name']; ?></h3>
            <?php
            if (isset($off['Item']['image']) && $off['Item']['image']) {
                $image = "/MenuItem-Image" . "/" . $off['Item']['image'];
                echo "<div class='product-image-frame'>" . $this->Html->image($image) . "</div>";
                $price_class = 'product-price';
            } else {
                $price_class = 'product-price product-price-single';
            }
            ?>
            <p><?php echo nl2br($off['Item']['description']); ?></p>
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

            <?php
        }
        if ($getOffer['Offer']['is_fixed_price'] == 1) {
            ?>
            <?php
            if ($getOffer['Offer']['offerprice'] == 0) {
                echo $getOffer['Item']['name'] . ' with all these items Free';
            } else {
                echo $getOffer['Item']['name'] . ' with all these items @ $' . number_format($getOffer['Offer']['offerprice'], 2);
            }
            ?>
        <?php } ?>
        <div class="product-detail-btn">
            <div class="row">
                <div class="col-lg-6 col-sm-6 col-xs-6">
                    <?php echo $this->Form->button('Click to redeem offer', array('id' => 'continue', 'type' => 'button', 'class' => 'd-access-guest confirm-btn theme-bg-1')); ?>
                </div>
                <div class="col-lg-6 col-sm-6 col-xs-6">
                    <?php echo $this->Form->button('Cancel', array('id' => 'cancel', 'type' => 'button', 'class' => 'd-access-guest confirm-btn theme-bg-2')); ?>
                </div>
            </div>

        </div>
    </div>

</div>
<script>
    $(document).ready(function () {

        $('#continue').click(function (e) {
            $.ajax({
                type: 'post',
                url: '/Products/cart',
                //data: $(this).serialize(),
                data: {offerApply: 'YES'},
                success: function (result) {
                    var check = result.charAt(0);
                    result = result.substr(1);
                    $('#ordercart').html(result);
                    $('#item-modal').html('');
                    $('#item-modal').css('display', 'none');
                    $('#item-modal').modal('hide');
                }
            });
        });

        $('#cancel').on('click', function () {
            $.ajax({
                type: 'post',
                url: '/Products/cancelOffer',
                success: function (result) {
                    $('#item-modal').html('');
                    $('#item-modal').css('display', 'none');
                    $('#item-modal').modal('hide');
                    window.location = window.location;
                }
            });
        });
    });

</script>
<style>
    .item-pic img {
        width: 35%;
    }
</style>