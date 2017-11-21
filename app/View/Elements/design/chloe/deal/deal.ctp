<main class="main-body">
     <?php if($store_data_app['Store']['store_theme_id']==14) { ?>
    <div class="ext-menu theme-bg-2">
     <?php } else { ?>
        <div class="ext-menu">
        <?php } ?>
        <div class="main-container">
            <div class="ext-menu-title">
                <h4>DEALS</h4>
            </div>
        </div>
    </div>
    <div class="main-container">
        <div class="inner-wrap deals clearfix">
            <?php
            echo $this->element('design/chloe/deal/coupons');
            echo $this->element('design/chloe/deal/item_offers');
            echo $this->element('design/chloe/deal/promotion_offers');
            ?>
        </div>
    </div>
</main>
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
