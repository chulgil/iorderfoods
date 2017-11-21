<div class="title-bar">
    store info
</div>
<div class="main-container">
    <div class=" inner-wrap store-info clearfix">
        <div class="static-content">
            <div class="map-info row">
                <div class="col-sm-9">
                    <div class="map-info-inner">
                        <div class="map-wrap" id="map"  style="height: 600px;">
                            <img src="img/map.jpg" alt="map">
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="store-address">
                        <h3>Contact</h3>
                        <div class="adrs-bx">
                            <i><?php echo $store_data['Store']['address']; ?></i>
                            <i><?php echo $store_data['Store']['city'] . ' ,' . $store_data['Store']['state'] . ' ,' . $store_data['Store']['zipcode']; ?></i>
                            <i><?php echo $store_data['Store']['phone']; ?></i>
                            <i><?php if (!empty($store_data['Store']['display_fax'])) {
                                    echo "Fax: " . $store_data['Store']['display_fax'];
                                } ?></i>
                            <i><?php if (!empty($store_data['Store']['display_email'])) {
                                    echo $store_data['Store']['display_email'];
                                }?></i>
                        </div>
                    </div>

                    <div class="store-address">
                        <div class="adrs-bx">
                            <h3>Opening <strong>Hours</strong></h3>
                            <ul>
                                <?php
                                $days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
                                foreach ($days as $key => $value) {
                                    ?>
                                    <li>
                                        <span>
                                            <?php echo $value; ?>
                                        </span>
                                        <span>
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
                        <span><i aria-hidden="true" class="fa fa-phone"></i> call Us  @<?php echo $store_data['Store']['phone']; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>