<?php

if (DESIGN == 4) {
    echo $this->element('design/oldlayout/innerpage/my_coupons');
} else {
     if((DESIGN == 1) && ($store_data_app['Store']['store_theme_id']==11)) { ?>
<!--        <div class="ext-menu">
            <div class="ext-menu-title">
               <h4>&nbsp;</h4>
        </div>
    </div>-->
    <?php }
    echo $this->element('design/common/my_coupons');
}
?>

