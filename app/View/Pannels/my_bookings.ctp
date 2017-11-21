<?php

if (DESIGN == 4) {
    echo $this->element('design/oldlayout/innerpage/my_bookings');
} else {
    if((DESIGN == 1) && ($store_data_app['Store']['store_theme_id']==11)) { ?>
        <div class="ext-menu">
            <div class="ext-menu-title">
                <h4>Reservation</h4>
        </div>
    </div>
    <?php }
    if(DESIGN == 2) { ?>
    <?php if($store_data_app['Store']['store_theme_id']==14) { ?>
    <div class="ext-menu theme-bg-2">
     <?php } else { ?>
        <div class="ext-menu">
        <?php } ?>
        <div class="main-container">
            <div class="ext-menu-title">
                <h4>RESERVATIONS</h4>
            </div>
        </div>
    </div>
    <?php }
    echo $this->element('design/common/my_bookings');
}
?>

