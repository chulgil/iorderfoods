<?php
if ($store_data_app['Store']['store_theme_id'] == 14) {
    $colthemebg = 'theme-bg-2';
} else {
    $colthemebg = '';
}
?>
<main class="main-body">
    <div class="ext-menu">
        <div class="ext-menu-title <?php echo $colthemebg; ?>">
            <div class="main-container">
                <h4>STORE INFO</h4>
            </div>
        </div>
        <div class="ext-menu-img">
            <div class="emi-tbl">
                <div class="emi-tbl-cell">
                    <h3><?php echo $store_data['Store']['store_name']; ?></h3>
                </div>
                <div class="emi-tbl-cell">
                    <?php echo $store_data['Store']['store_info_description']; ?>
                </div>
            </div>
            <div class="emi-overlay"></div>
            <?php
            if (!empty($store_data['Store']['store_info_bg_image'])) {
                $image = "/storeBackground-Image/" . $store_data['Store']['store_info_bg_image'];
            } else {
                $image = "/img/store-mid-banner.png";
            }
            ?>
            <img src="<?php echo $image; ?>">
        </div>
    </div>
    <div class="store-mid-map" id="map" style="height: 600px;">
        <img src="/img/store-mid-banner.png">
    </div>
    <div class="store-mid-last clearfix">
        <div class="main-container clearfix">
            <div class="store-last-lft">
                <div class="store-contact-info">
                    <h3>CONTACT INFO</h3>
                    <p><?php echo $store_data['Store']['address']; ?></p>
                    <p><?php echo $store_data['Store']['city'] . ' ,' . $store_data['Store']['state'] . ' ,' . $store_data['Store']['zipcode']; ?></p>
                    <p><?php echo $store_data['Store']['phone']; ?></p>
                    <p><?php
                        if (!empty($store_data['Store']['display_fax'])) {
                            echo "Fax: " . $store_data['Store']['display_fax'];
                        }
                        ?></p>
                    <p><?php
                        if (!empty($store_data['Store']['display_email'])) {
                            echo $store_data['Store']['display_email'];
                        }
                        ?></p>
                </div>

                <div class="message-form clearfix">
                </div>
            </div>
            <div class="store-last-rgt">
                <div class="open-hours-card">
                    <div class="open-top">
                        <span class="close">
                            <i aria-hidden="true" class="fa fa-times-circle"></i></span>
                    </div>
                    <div class="open-mid clearfix">
                        <h3>Opening <strong>Hours</strong></h3>
                        <ul class="time-list">
                            <?php
                            $days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
                            foreach ($days as $key => $value) {
                                ?>

                                <li class="clearfix">

                                    <span class="lft-txt">
                                        <i class="fa fa-calendar-o" aria-hidden="true"></i>
                                        <?php echo $value; ?>
                                    </span>
                                    <span class="rgt-txt">
                                        <?php
                                        if ($availabilityInfo[$key]['StoreAvailability']['is_closed'] == 1) {
                                            echo "Closed";
                                        } else {
                                            echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreAvailability']['start_time']) . " - ";
                                            if ($store_data['Store']['is_break_time'] == 1) {
                                                if ($store_data['Store']['is_break1'] == 1) {
                                                    if ($availabilityInfo[$key]['StoreBreak']['break1_start_time'] != $availabilityInfo[$key]['StoreBreak']['break1_end_time']) {
                                                        echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break1_start_time']) . ",   ";
                                                        echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break1_end_time']) . " - ";
                                                    }
                                                }
                                                if ($store_data['Store']['is_break2'] == 1) {
                                                    if ($availabilityInfo[$key]['StoreBreak']['break2_start_time'] != $availabilityInfo[$key]['StoreBreak']['break2_end_time']) {
                                                        echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break2_start_time']) . ",   ";
                                                        echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreBreak']['break2_end_time']) . " - ";
                                                    }
                                                }
                                            }
                                            echo $this->Common->storeTimeFormateUser($availabilityInfo[$key]['StoreAvailability']['end_time']);
                                        }
                                        ?>

                                    </span>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="open-bottom">
                        <div class="contact-info">
                            <span><i aria-hidden="true" class="fa fa-phone"></i>call Us <strong> @(562)402-7443</strong></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>