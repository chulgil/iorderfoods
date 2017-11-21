<!-- BANNER START-->
<div class="wraper no-background clearfix">
<div class="right-col">
    <div class="banner clearfix">
        <div class="callbacks_container">
            <ul class="rslides" id="slider4">
                <?php if(!empty($photo)) {
                    foreach($photo as $gallery){ 
                        if(!empty($gallery['MerchantGallery']['image'])) { ?>
                        <li>
                            <img src="/merchantSliderImages/<?php echo $gallery['MerchantGallery']['image'];?>" alt="banner">
                            <?php if(!empty($gallery['MerchantGallery']['description'])) { ?>
                            <p class="caption"><?php echo $gallery['MerchantGallery']['description'];?></p>
                            <?php } ?>
                        </li>
                <?php }}} else {
                    echo 'No photos added yet.';
                } ?>
            </ul>
        </div>
    </div>
     </div>
    </div>

    <!-- END BANNER-->
    <style>
    .rslides li img{ min-height:80px !important;}
    .rslides li img{ max-height:400px !important;}
</style>
<script>
    $(function () {
      $("#slider4").responsiveSlides({
        auto: false,
        pager: false,
        nav: true,
        speed: 500,
        namespace: "callbacks",
        before: function () {
          $('.events').append("<li>before event fired.</li>");
        },
        after: function () {
          $('.events').append("<li>after event fired.</li>");
        }
      });

    });
  </script>


   