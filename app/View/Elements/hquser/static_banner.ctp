<div class="container-fluid padding0">
    <div class="row margin0">
        <div class="col-sm-12 padding0">
            <div class="static-banner">
                <?php
                if (!empty($bannerImage) && file_exists(WWW_ROOT . '/merchantBackground-Image/' . $bannerImage)) {
                    echo $this->Html->image('/merchantBackground-Image/' . $bannerImage, array('alt' => 'Banner Image'));
                } else {
                    ?>
                    <img src="/img/hq/cup.jpg" alt="cup">
                <?php } ?>
            </div>  
        </div>
    </div>
</div>