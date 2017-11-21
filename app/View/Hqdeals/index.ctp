<!-- static-banner -->
<?php echo $this->element('hquser/static_banner'); ?>
<!-- /banner -->
<!--- Gallery-->
<div class="promotions common-padding">
    <div class="container">
        <span id="flashMessage"></span>
        <div class="row">
            <div class="col-sm-8">
                <div class="common-title clearfix">
                    <span class="yello-dash"></span>
                    <h2>Promotions</h2>
                </div>
            </div>
            <div class="col-md-4">
                <?php echo $this->Form->create('Store', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'enctype' => 'multipart/form-data')); ?>
                <?php
                if (!empty($storeList)) {
                $allOption = array('All' => 'All Stores');
                $storeList = array_replace($allOption, $storeList);
            }
                echo $this->Form->input('Store.store_id', array('options' => @$storeList, 'class' => 'form-control custom-text', 'label' => false, 'div' => false, 'empty' => 'Please Select Store'));
                ?>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
        <div class="row deal-listing">
            <?php
            echo $this->element('deals/hq_coupons');
            ?>
            <?php
            echo $this->element('deals/hq_item_offers');
            ?>
            <?php
            echo $this->element('deals/hq_promotion_offers');
            ?>
        </div>
        <div class="row">
            
        </div>
    </div>
</div>
<!-- -->
<script type="text/javascript">
    $(document).ready(function () {
        $("#StoreStoreId").change(function () {
            $("#StoreIndexForm").submit();
        });
    });
</script>
<script type="text/javascript">
    $(document).on('click', '.add-coupons', function () {
        var that = $(this);
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
                        if (response.status == 'Success') {
                            $("#flashMessage").html('<div class="message alert alert-success"><a title="close" aria-label="close" data-dismiss="alert" class="close" href="#">×</a>' + response.msg + '</div>');
                            that.text('ADDED');
                        } else if (response.status == 'Error') {
                            $("#flashMessage").html('<div class="message alert alert-danger"><a title="close" aria-label="close" data-dismiss="alert" class="close" href="#">×</a>' + response.msg + '</div>');
                        }
                        $("html, body").delay(1000).animate({
                            scrollTop: $('#flashMessage').offset().top
                        }, 1000);
                    }
                }
            });
        }
    });
</script>