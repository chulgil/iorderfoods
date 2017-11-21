<!--div class="row">
    <div class="col-sm-12 col-xs-12">
<?php if (!empty($couponsData)) { ?>
    <?php
    foreach ($couponsData as $data) {
        $EncryptStoreID = $this->Encryption->encode($data['Coupon']['store_id']);
        $EncryptMerchantID = $this->Encryption->encode($data['Coupon']['merchant_id']);
        $url = $this->Html->url(array('controller' => 'products', 'action' => 'items', $EncryptStoreID, $EncryptMerchantID), true);
        ?>
                                                                                                                                                                                                                                                        <div class="deal-wrap">
                                                                                                                                                                                                                                                            <a href="<?php echo $url; ?>">
                                                                                                                                                                                                                                                                <div class="common-title-deal">
                                                                                                                                                                                                                                                                    <h3><?php echo 'Coupon Code - ' . substr($data['Coupon']['coupon_code'], 0, 23); ?></h3>
                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                <div class="deal-img-wrap">
        <?php if (!empty($data['Coupon']['image']) && file_exists(WWW_ROOT . '/Coupon-Image/' . $data['Coupon']['image'])) { ?>
                                                                                                                                                                                                                                                                                                                                                                                            <img src="/Coupon-Image/thumb/<?php echo $data['Coupon']['image']; ?>" alt="deals-img">
            <?php
        } else {
            ?>
                                                                                                                                                                                                                                                                                                                                                                                            <img src = "img/no_images.jpg" alt = "deals-img">
        <?php }
        ?>
                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                <div class="deals-detail clearfix">
                                                                                                                                                                                                                                                                    <p>
        <?php
        if ($data['Coupon']['discount_type'] == 2) {//for percentage
            echo $data['Coupon']['discount'] . '% off on total order amount.';
        } else {
            echo '$' . $data['Coupon']['discount'] . ' off on total order amount.';
        }
        ?>
                                                                                                                                                                                                                                                                        <span class="glyphicon glyphicon-info-sign" aria-hidden="true" data-toggle="tooltip" title="<?php echo 'Coupon Code - ' . $data['Coupon']['coupon_code']; ?>"></span>
                                                                                                                                                                                                                                                                    </p>
        <?php
        if (!empty($userId)) {
            if (in_array($data['Coupon']['id'], $couponIdList)) {
                ?> 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <i class="fa fa-check theme-txt-col-1"></i>
            <?php } else {
                ?>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <a id="add-coupons" class="add-deal theme-bg-1"  data-id="<?php echo $this->Encryption->encode($data['Coupon']['id']); ?>" data-coupon="<?php echo $data['Coupon']['coupon_code']; ?>">ADD</a>
            <?php }
            ?>
        <?php } ?>
                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                            </a>
                                                                                                                                                                                                                                                        </div>
    <?php }
    ?>
<?php }
?>
    </div>
</div-->
<?php
if (!empty($couponsData)) {
    $EncryptStoreID = $this->Encryption->encode($store_data_app['Store']['id']);
    $EncryptMerchantID = $this->Encryption->encode($store_data_app['Store']['merchant_id']);
    $dealsstr = '';
    foreach ($couponsData as $coupons) {
        $url = $this->Html->url(array('controller' => 'products', 'action' => 'items', $EncryptStoreID, $EncryptMerchantID), true);
        $dealsstr.= "<li>";
        if (!empty($coupons['Coupon']['image']) && file_exists(WWW_ROOT . '/Coupon-Image/' . $coupons['Coupon']['image'])) {
            $dealsstr.="<div class='dasol-img-frame'><a class='img-item'><img src='/Coupon-Image/" . $coupons['Coupon']['image'];
            $dealsstr.="'></a></div>";
        } else {
            $dealsstr.="<div class='dasol-img-frame'><a class='img-item'><img src='/img/no_images.jpg' alt='item'></a></div>";
        }
        $dealsstr.="<div class='dasol-txt-frame'><h3>" . ucfirst($coupons['Coupon']['name']) . ' (' . $coupons['Coupon']['coupon_code'] . ')' . "</h3><small>";
        if ($coupons['Coupon']['discount_type'] == 2) {//for percentage
            $dealsstr.=$coupons['Coupon']['discount'] . '% off on total order amount.';
        } else {
            $dealsstr.='$' . $coupons['Coupon']['discount'] . ' off on total order amount.';
        }
        $dealsstr.="</small>";
        $dealsstr.= $this->Html->link('Add to Cart', 'javascript:void(0)', array('escape' => false, 'class' => 'add-cart theme-bg-1 theme-border-1 addCouponToCart', 'data-id' => $this->Encryption->encode($coupons['Coupon']['id'])));
        $dealsstr.="</div></li>";
    }
    echo $dealsstr;
}
?>
<script type="text/javascript">
    $(document).on('click', '.addCouponToCart', function () {
        var couponId = $(this).data('id');
        if (couponId) {
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'products', 'action' => 'addCouponToCart')); ?>",
                type: "Post",
                dataType: 'html',
                async: false,
                data: {couponId: couponId},
                beforeSend: function () {
                    $.blockUI({css: {
                            border: 'none',
                            padding: '15px',
                            backgroundColor: '#000',
                            '-webkit-border-radius': '10px',
                            '-moz-border-radius': '10px',
                            opacity: .5,
                            color: '#fff'
                        }});
                },
                success: function (result) {
                    if (result) {
                        response = $.parseJSON(result);
                        if (response.status == "Error") {
                            $("#errorPop").modal('show');
                            $("#errorPopMsg").html(response.msg);
                            return false;
                        } else if (response.status == "Success" && response.url) {
                            window.location.href = response.url;
                        }
                    }
                },
                complete: function () {
                    $.unblockUI();
                }
            });
        }
    });
</script>
