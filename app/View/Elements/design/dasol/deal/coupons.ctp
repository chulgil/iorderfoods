<?php if (!empty($couponsData)) { ?>
    <?php
    $i = 0;
    foreach ($couponsData as $data) {
        ?>
        <div class="deal-wrap">
            <?php if (in_array(KEYWORD, array('IOF-D2-H', 'IOF-D2-V', 'IOF-D4-H', 'IOF-D4-V','IOF-D3-V','IOF-D3-H'))) { ?>
                <a href="javascript:void(0);" class="deal-domo">
                    <div class="deal-img-wrap">
                        <?php
                        if (!empty($data['Coupon']['image']) && file_exists(WWW_ROOT . '/Coupon-Image/' . $data['Coupon']['image'])) {
                            $imageUrl = "/Coupon-Image/thumb/" . $data['Coupon']['image'];
                        } else {
                            $imageUrl = "img/deals-item.jpg";
                        }
                        ?>
                        <img src="<?php echo $imageUrl; ?>" alt="deals-img">
                        <div class="deal-overlay">
                            <div class="deal-content">

                                <div class="deal-table">
                                    <div class="deal-cell">
                                        <span><?php echo 'Coupon Code -' . $data['Coupon']['coupon_code']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                        </p>
                    </div>
                    <?php
                    if (!empty($userId)) {
                        if (in_array($data['Coupon']['id'], $couponIdList)) {
                            ?>
                            <a href="javascript:void(0);">Added</a>
                        <?php } else {
                            ?>
                            <a id="add-coupons" data-id="<?php echo $this->Encryption->encode($data['Coupon']['id']); ?>" data-coupon="<?php echo $data['Coupon']['coupon_code']; ?>">ADD</a>
                        <?php }
                        ?>
                    <?php } ?>
                </a>

            <?php } else {
                ?>
                <div class="deal-img-wrap">
                    <?php
                    if (!empty($data['Coupon']['image']) && file_exists(WWW_ROOT . '/Coupon-Image/' . $data['Coupon']['image'])) {
                        $imageUrl = "/Coupon-Image/thumb/" . $data['Coupon']['image'];
                    } else {
                        $imageUrl = "img/deals-item.jpg";
                    }
                    ?>
                    <div class="deal-overlay" style="background-image:url(<?php echo $imageUrl; ?>)">
                        <div class="deal-content">
                            <p><?php echo 'Coupon Code -' . $data['Coupon']['coupon_code']; ?></p>
                            <div class="deal-table">
                                <div class="deal-cell">
                                    <h4>
                                        <?php
                                        if ($data['Coupon']['discount_type'] == 2) {//for percentage
                                            echo $data['Coupon']['discount'] . '% off on total order amount.';
                                        } else {
                                            echo '$' . $data['Coupon']['discount'] . ' off on total order amount.';
                                        }
                                        ?>
                                    </h4>
                                </div>
                            </div>
                            <?php
                            if (!empty($userId)) {
                                if (in_array($data['Coupon']['id'], $couponIdList)) {
                                    ?>
                                    <a href="javascript:void(0);">Added</a>
                                <?php } else {
                                    ?>
                                    <a id="add-coupons" data-id="<?php echo $this->Encryption->encode($data['Coupon']['id']); ?>" data-coupon="<?php echo $data['Coupon']['coupon_code']; ?>">ADD</a>
                                <?php }
                                ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }
}
?>