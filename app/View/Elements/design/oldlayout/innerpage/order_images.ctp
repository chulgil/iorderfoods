<?php //echo $this->Session->flash(); ?>
<div class="banner clearfix clear-clearfix">
    <div class="callbacks_container">
        <ul class="rslides" id="slider4">
            <?php
            if (!empty($allReviewImages)) {
                foreach ($allReviewImages as $image) {
                    if (!empty($image['StoreReviewImage']['image'])) {
                        ?>
                        <li>
                            <img src="/storeReviewImage/<?php echo $image['StoreReviewImage']['image']; ?>" alt="banner">
                        </li>
                    <?php
                    }
                }
            } else {
                ?>
                <li>
                    <img src="/img/vertical/banner-01.jpg" alt="banner">
                    <p class="caption">Lorem Ipsum has been the industry's standard dummy text ever since the 1500.</p>
                </li>
                <li>
                    <img src="/img/vertical/banner-01.jpg" alt="banner">
                    <p class="caption">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                </li>
                <li>
                    <img src="/img/vertical/banner-01.jpg" alt="banner">
                    <p class="caption">when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                </li>
                <li>
                    <img src="/img/vertical/banner-01.jpg" alt="banner">
                    <p class="caption">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                </li>
<?php } ?>
        </ul>
    </div>
</div>
<style>
    .rslides li img{ min-height:80px !important;}
    .rslides li img{ max-height:450px !important;}
</style>


