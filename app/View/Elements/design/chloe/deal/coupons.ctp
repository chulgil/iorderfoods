<?php if (!empty($couponsData)) { ?>
    <?php
    $i = 0;
    foreach ($couponsData as $data) {
        ?>
<?php if ($i % 2 != 0) { ?>
        <div class="c-deal-wrap clearfix">
        
<?php } else{?>
<div class="c-deal-wrap clearfix <?php echo "c-odd-wrap"?> ">
    
<?php } 
        $i++;
        ?>
            <div class="deal-img">
                <?php if (!empty($data['Coupon']['image']) && file_exists(WWW_ROOT . '/Coupon-Image/' . $data['Coupon']['image'])) { ?>
                    <img src="/Coupon-Image/thumb/<?php echo $data['Coupon']['image']; ?>" alt="deals-img">
                    <?php
                } else {
                    ?>
                    <img src = "img/deals-item.jpg" alt = "deals-img">
                <?php }
                ?>
            </div>
            <div class="deal-info">
                <h3><?php echo 'Coupon Code -' . $data['Coupon']['coupon_code']; ?></h3>
                <p>
                    <?php
                    if ($data['Coupon']['discount_type'] == 2) {//for percentage
                        echo $data['Coupon']['discount'] . '% off on total order amount.';
                    } else {
                        echo '$' . $data['Coupon']['discount'] . ' off on total order amount.';
                    }
                    ?>
                </p>

                <?php
                if (!empty($userId)) {
                    if (in_array($data['Coupon']['id'], $couponIdList)) {
                        ?>
                        <a class=" add-deal theme-bg-1">Added</a>
                    <?php } else {
                        ?>
                        <a id="add-coupons" class="add-deal theme-bg-1"  data-id="<?php echo $this->Encryption->encode($data['Coupon']['id']); ?>" data-coupon="<?php echo $data['Coupon']['coupon_code']; ?>">ADD</a>
                    <?php }
                    ?>
                <?php } ?>
            </div>
        </div>

        <?php
    }
}
?>