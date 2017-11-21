<?php
if (DESIGN == 3) {
    if (!empty($allReviewImages)) {
        foreach ($allReviewImages as $image) {
            if (!empty($image['StoreReviewImage']['image']) && file_exists(WWW_ROOT . '/storeReviewImage/thumb/' . $image['StoreReviewImage']['image'])) {
                ?>
                <li>
                    <div class="expand-img">
                        <img class="example-image" src="/storeReviewImage/thumb/<?php echo $image['StoreReviewImage']['image']; ?>" alt="image-1" />
                        <span>
                            <a class="example-image-link" href="/storeReviewImage/thumb/<?php echo $image['StoreReviewImage']['image']; ?>" data-lightbox="example-1">
                            </a>
                        </span>                                
                    </div>
                </li>
                <?php
            }
        }
    }
} else {
    if (!empty($allReviewImages)) {
        foreach ($allReviewImages as $image) {
            if (!empty($image['StoreReviewImage']['image']) && file_exists(WWW_ROOT . '/storeReviewImage/thumb/' . $image['StoreReviewImage']['image'])) {
                ?>
                <li>
                    <a href="javascript:void(0)" class="pop">
                        <?php if (!empty($store_data_app['Store']['store_theme_id']) && $store_data_app['Store']['store_theme_id'] == 12) { ?>
                            <span></span>
                        <?php } ?>
                        <img src="/storeReviewImage/thumb/<?php echo $image['StoreReviewImage']['image']; ?>" alt="banner">
                    </a>
                </li>
                <?php
            }
        }
    }
}
?>
            