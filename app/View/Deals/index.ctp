<style type="text/css">
    .common-title-deal > h3 {
        min-height: 57px;
        word-wrap: break-word;
    }
</style>
<?php
if(DESIGN == 1){
        
        if($store_data_app['Store']['store_theme_id']==11) { ?>
    <div class="ext-menu">
            <div class="ext-menu-title">
                <h4>Deals</h4>
        </div>
    </div>
<?php } ?>
    <?php echo $this->element('design/aaron/deal');
} elseif (DESIGN == 2) {
    echo $this->element('design/chloe/deal/deal');
} elseif (DESIGN == 3) {
    echo $this->element('design/dasol/deal');
}
?>
<script type="text/javascript">
    $(document).on('click', '#add-coupons', function () {
        var coupon_id = $(this).data('id');
        var coupon_code = $(this).data('coupon');
        if (coupon_id && coupon_code) {
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => $this->params['controller'], 'action' => 'addCoupon')); ?>",
                type: "post",
                dataType: 'html',
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
                complete: function () {
                    $.unblockUI();
                },
                data: {coupon_id: coupon_id, coupon_code: coupon_code},
                success: function (result) {
                    if (result != '') {
                        var response = $.parseJSON(result);
                        alert(response.msg);
                    }
                }
            });
        }
    });
</script>

