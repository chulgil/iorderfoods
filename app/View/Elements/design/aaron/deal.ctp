<div class="main-container deal-container">
    <div class="inner-wrap deals clearfix">
        <div class="col-md-2">
            <div class="dasol-liw hm-deals-wrap">
                <ul class="clearfix remove-space-btwn-items hp-deals-wrap">
                    <?php
                    echo $this->element('deals/coupons');
                    echo $this->element('deals/item_offers');
                    echo $this->element('deals/promotion_offers');
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>