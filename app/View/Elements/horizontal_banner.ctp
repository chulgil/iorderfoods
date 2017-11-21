<!-- BANNER START-->
<?php //echo $this->Session->flash(); ?>
<div class="banner wrap clearfix">
    <div class="callbacks_container">
        <ul class="rslides" id="slider4">
            <?php
            if (!empty($store_data_app['StoreGallery'])) {
                foreach ($store_data_app['StoreGallery'] as $gallery) {
                    if (!empty($gallery['image'])) {
                        ?>
                        <li>
                            <img src="/sliderImages/<?php echo $gallery['image']; ?>" alt="banner">
                            <?php if (!empty($gallery['description'])) { ?>
                                <p class="caption"><?php echo $gallery['description']; ?></p>
                        <?php } ?>
                        </li>
        <?php }
    }
} else { ?>
                <li>
                    <img src="/img/horizontal/banner-01.jpg" alt="banner">
                    <p class="caption">Lorem Ipsum has been the industry's standard dummy text ever since the 1500.</p>
                </li>
                <li>
                    <img src="/img/horizontal/banner-01.jpg" alt="banner">
                    <p class="caption">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                </li>
                <li>
                    <img src="/img/horizontal/banner-01.jpg" alt="banner">
                    <p class="caption">when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                </li>
                <li>
                    <img src="/img/horizontal/banner-01.jpg" alt="banner">
                    <p class="caption">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                </li>
<?php } ?>
        </ul>
    </div>
</div>
<!-- END BANNER-->
<style>
    .rslides li img{ min-height:80px !important;}
    .rslides li img{ max-height:567px !important;}
</style>
