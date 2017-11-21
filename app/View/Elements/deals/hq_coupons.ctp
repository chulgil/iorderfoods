<?php
if (!empty($couponsData)) {
    $bgClass = array('maroon-bg', 'maroon-bg', 'maroon-bg', 'maroon-bg', 'maroon-bg', 'maroon-bg');
    $i = 0;
    foreach ($couponsData as $data) {
        if ($i > 5) {
            $i = 0;
        }
        ?>
        <div class="col-xs-6 col-sm-4">
            <div class="promotion-box">
                <div class="p-top <?php echo $bgClass[$i]; ?>">
                    <h3>
                    <?php echo $data['Coupon']['name']; ?>
                    </h3>
                    <p class="promotion-box-map"><i class="fa fa-map-marker"></i> <?php echo @$data['Store']['city']; ?></p>
                </div>
                <div class="p-mid">
                    <?php if (!empty($data['Coupon']['image']) && file_exists(WWW_ROOT . '/Coupon-Image/' . $data['Coupon']['image'])) { ?>
                        <img src="/Coupon-Image/thumb/<?php echo $data['Coupon']['image']; ?>" alt="deals-img">
                        <?php
                    } else {
                        ?>
                        <img src = "img/no_images.jpg" alt = "deals-img">
                    <?php }
                    ?>
                </div>
                <div class="p-bottom <?php echo $bgClass[$i]; ?>">
                    <span class="promocode"><?php echo $data['Coupon']['coupon_code']; ?></span>
                    <?php if (!empty($data['Coupon']['start_date'])) { ?>
                        <p><?php echo $data['Coupon']['start_date'] . ' to ' . $data['Coupon']['end_date']; ?></p>
                    <?php } else { ?>
                        <p>&nbsp;</p>
                    <?php } ?>
                        <h3>    
                    <?php
                        if ($data['Coupon']['discount_type'] == 2) {//for percentage
                            echo $data['Coupon']['discount'] . '% off on total order amount.';
                        } else {
                            echo '$' . $data['Coupon']['discount'] . ' off on total order amount.';
                        }
                        ?>
                    </h3>
                    <?php
                    if (!empty($userId)) {
                        if (in_array($data['Coupon']['id'], $couponIdList)) {
                            ?> 
                            <button class="add-deal contact-btn">ADDED</button>
                        <?php } else {
                            ?>
                            <button class="add-coupons contact-btn" data-id="<?php echo $this->Encryption->encode($data['Coupon']['id']); ?>" data-coupon="<?php echo $data['Coupon']['coupon_code']; ?>">ADD</button>
                        <?php }
                        ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
        $i++;
    }
}
?>

