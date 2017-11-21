<style>
    .p-list > li {
        text-align: center;
        vertical-align: middle;
    }
    .chole-main .inner-wrap.photos{
        display:block;
    }
</style>

<main class="main-body">
    <?php if($store_data_app['Store']['store_theme_id']==14) { ?>
    <div class="ext-menu theme-bg-2">
     <?php } else { ?>
        <div class="ext-menu">
        <?php } ?>
        <div class="main-container">
            <div class="ext-menu-title">
                <h4>GALLERY</h4>
            </div>
        </div>
    </div>

    <div class="main-container ">
        <div class="inner-wrap photos">

            <div class="photo-gallery">
                <ul class="p-list">
                    <?php
                    if (!empty($allReviewImages)) {
                        foreach ($allReviewImages as $image) {
                            if (!empty($image['StoreReviewImage']['image']) && file_exists(WWW_ROOT . '/storeReviewImage/thumb/' . $image['StoreReviewImage']['image'])) {
                                ?>
                                <li>
                                     <a href="javascript:void(0)" class="pop">
                                    <img src="/storeReviewImage/<?php echo $image['StoreReviewImage']['image']; ?>" alt="banner"></a>
                                </li>
                                <?php
                            }
                        }
                    }
                    ?>
                </ul>
            </div>
            <div class="show-more-pic">
                <input type='hidden' id="pageId" value="2"/>
                <a class="no-color show-more-btn" id="test"> Show more result</a>
            </div>
        </div>
    </div>
</main>
<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog  gallery-dialog">
    <div class="modal-content">              
      <div class="modal-body">
      	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <img src="" class="imagepreview" style="width: 100%;" >
      </div>
    </div>
  </div>
</div>
<script>
    $(function() {
        $('.pop').on('click', function() {
                $('.imagepreview').attr('src', $(this).find('img').attr('src'));
                $('#imagemodal').modal('show');   
        });		
    });
    </script>