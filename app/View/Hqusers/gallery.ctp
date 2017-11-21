<!-- static-banner -->
<?php echo $this->element('hquser/static_banner'); ?>
<!-- /banner -->

<!--- Gallery-->
<div class="Gallery common-padding">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="common-title clearfix">
                    <span class="yello-dash"></span>
                    <h2>Gallery</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="gallery-outbox">
                <?php
                if (!empty($allReviewImages)) {
                    foreach ($allReviewImages as $image) {
                        if (!empty($image['MerchantImage']['image']) && file_exists(WWW_ROOT . '/storeReviewImage/thumb/' . $image['MerchantImage']['image'])) {
                            ?>
                            <div class="col-xs-6 col-sm-4 col-md-3 padding0">
                                <div class="gallery-box">
                                    <div class="img-wrap outer-wrap">
                                        <div class="inner-wrap">
                                            <a href="javascript:void(0)" class="pop">
                                                
                                                <img src="/storeReviewImage/<?php echo $image['MerchantImage']['image']; ?>" alt="banner"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } else {
                            ?>
                            <div class="col-xs-6 col-sm-4 col-md-3 padding0">
                                <div class="gallery-box">
                                    <div class="img-wrap outer-wrap">
                                        <div class="inner-wrap">
                                            <img src="/img/hq/gallery-01.jpg" alt="gal">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                }
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="pagination-section clearfix">
                    <?php echo $this->element('pagination'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
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