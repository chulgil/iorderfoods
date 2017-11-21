<div class="content single-frame">
    <div class="wrap">
        <div class="clearfix">
            <section class="form-layout sign-up no-image editable-form reviewfix">
                <h2> <span>All Reviews</span></h2>
            <div class="col-md-12">
                <h3 class="addReviews pull-right green-btn srch-btn"><span><?php echo __('Add Reviews'); ?></span></h3>
            </div>
                <div>
                    <div class="repeat-deatil">
                        <?php
                        if (!empty($allReviews)) {
                            foreach ($allReviews as $reviews) {
                                $EncryptReviewID = $this->Encryption->encode($reviews['StoreReview']['id']);
                                ?>
                                <div class="order-history-detail order-history-detail-lreview clearfix">
                                    
                                <?php
                                if (!empty($reviews['StoreReviewImages'][0]['image']) && file_exists(WWW_ROOT . '/storeReviewImage/' . $reviews['StoreReviewImages'][0]['image'])) 
                                { ?>
                                <div class="r-img c-cur" data-value="<?php echo $EncryptReviewID; ?>">
                                    <?php echo $this->Html->image('/storeReviewImage/' . $reviews['StoreReviewImages'][0]['image'], array('alt' => 'Image')); ?>
                                    <div class="hover-box">
                                        <div class="table-box"><div class="table-box-cell"><img src="/img/plus-icon.png"></div></div>
                                    </div>
                                    <?php if (count($reviews['StoreReviewImages']) > 1) { ?>
                                        <div class="more-pic"><span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span></div>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                <div class="r-img c-cur">
                                    <img src="/img/r-img-2.png">
                                    <div class="hover-box">
                                        <div class="table-box"><div class="table-box-cell"><img src="/img/plus-icon.png"></div></div>
                                    </div>
                                </div>
                            <?php } ?>
                                    
                                    <div class="order-history-detail-lt order-history-detail-lreview-lt">
                                        <div class="review-margin pline-height clearfix">
                                            <div class="review-margin-lt">
                                                <i class="fa fa-user"></i>

                                                <?php
                                                //echo $reviews['User']['salutation'] . ' ' . ucfirst($reviews['User']['fname']) . ' ' . ucfirst($reviews['User']['lname']);
                                                $name = ($reviews['StoreReview']['user_id']) ? ucfirst($reviews['User']['fname']) . ' ' . ucfirst($reviews['User']['lname']) : 'Anonymous';
                                                echo $name;
                                                ?>
                                            </div>

                                            <div class="review-margin-rt">
                                                <span><?php echo __('Rated'); ?>:-</span> <input disabled="true" class="rating" data-glyphicon=0 value=<?php echo $reviews['StoreReview']['review_rating']; ?> >
                                            </div>
                                        </div>

                                        <div class="review-description">
                                            <p><?php echo ucfirst(@$reviews['OrderItem']['Item']['name']); ?> - <?php echo ucfirst($reviews['StoreReview']['review_comment']); ?> <span class="publish-date">- <?php echo $this->Common->storeTimeFormateUser($this->Common->storeTimeZoneUser('', $reviews['StoreReview']['created']), true); ?></span></p>
                                        </div>
                                    </div>

                                </div>
                            <?php }
                            $this->Paginator->options(array('url' =>  $this->request->query));
                            echo $this->element('pagination');
                            ?><div class="clr"></div>
                        <?php } else {
                            ?>
                            <div class="order-history-detail">
                                <div class="order-history-detail-lt">
                                    <p class="review-margin"> <span><?php echo __('No review found'); ?></span> </p>
                                </div>

                                <div class="clr"></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>


<style>
    .rating-xs {
        float: left;
        font-size: 1.5em;
        margin-left: 15px;
    }
</style>
<div class="modal fade add-info review-modal" id="add-review-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Add Review</h4>
        </div>
        <div class="modal-body">
            <div class="form-section">
                <?php
                echo $this->Form->create('StoreReview', array('url' => array('controller' => 'orders', 'action' => 'addReviewRating'), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'addReviewRating', 'enctype' => 'multipart/form-data'));
                ?>
                <div class="profile-input clearfix">
                    <label>Rating <em>*</em></label>
                    <div class="col-right">
                        <div class="col-width">
                            <?php echo $this->Form->input('StoreReview.review_rating', array('data-glyphicon' => 0, 'type' => 'number', 'class' => 'rating user-detail', 'max' => 5, 'min' => 0, 'label' => false, 'div' => false, 'value' => 1)); ?>
                        </div>
                    </div>
                </div>
                <div class="profile-input clearfix">
                    <label>Review <em>*</em></label>
                    <div class="col-right">
                        <div class="col-width">
                            <?php echo $this->Form->input('StoreReview.review_comment', array('type' => 'textarea', 'class' => 'user-detail', 'placeholder' => 'Enter Your Review', 'maxlength' => '200', 'label' => false, 'div' => false));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="profile-input clearfix">
                    <label>Images </label><span class="btn btn-danger btn-sm" id="addMoreImage"> ADD IMAGE</span><small>(Max Upload size 2MB)</small>
                    <div class="col-right" id="appendDiv">
                        <div class="col-width">
                            <?php //echo $this->Form->input('StoreReviewImage.image.0', array('type' => 'file', 'class' => 'user-detail', 'label' => false, 'div' => false, "accept" => "image/*"));
                            ?>
                            <div class="preview-image"></div>
                        </div>
                    </div>
                    <span id="StoreReviewImage-error" class="error hidden" for="StoreContentName">Upto 4 images are allowed</span>
                </div>

                <div class="profile-input btn-review-grp clearfix">
                    <?php
                    echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'editOrderType cont-btn green-btn btn-color-pale'));
                    ?>
                </div>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade add-info review-modal" id="gallery-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;"></div>
<script>
    $(document).ready(function () {
        $(document).on('click', '.r-img', function () {
            var storeReviewId = $(this).attr('data-value');
            if (storeReviewId) {
                $.ajax({
                    type: 'post',
                    url: '/pannels/reviewImageDet',
                    data: {'storeReviewId': storeReviewId},
                    async: false,
                    success: function (result) {
                        $("#gallery-modal").html('');
                        $("#lightbox, #lightboxOverlay").remove();
                        $("#gallery-modal").html(result).css('display', 'none');//.modal('show');
                        var $lt = lightbox; 
                        $lt.start($('#gallery_img_0'));
                        $lt.option({
                            resizeDuration: 200,
                            wrapAround: true,
                            disableScrolling: true
                        });
                        $('#lightboxOverlay, .lightboxOverlay, .lb-close, .lb-outerContainer', '.lightbox').on('click', function() {
                            $("#gallery-modal").html('');
                            $("#lightbox, #lightboxOverlay").remove();
                        });
                        
                    }
                });
            }
        });
        $(document).on('click', '.addReviews', function () {
            $("#add-review-modal").modal('show');
        });
        $('#StoreReviewImage').change(function (e) {
            //get the input and the file list
            var input = document.getElementById('StoreReviewImage');
            if (input.files.length <= 4) {
                $('#StoreReviewImage-error').addClass('hidden');
            } else {
                $('#StoreReviewImage-error').removeClass('hidden');
                e.preventDefault();
            }
        });
        $("#addReviewRating").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                'data[StoreReview][review_rating]': {
                    required: true,
                },
                'data[StoreReview][review_comment]': {
                    required: true
                }
            },
            messages: {
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            }
        });
    });
    $(document).on('change', '.user-detail1', function () {
        var countFiles = $(this)[0].files.length;
        console.log(countFiles);
        var imgPath = $(this)[0].value;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        var image_holder = $(this).next();
        image_holder.empty();

        if (extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof (FileReader) != "undefined") {

                for (var i = 0; i < countFiles; i++) {

                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $("<img />", {
                            "class": "thumbimage hidden",
                            "src": e.target.result
                        }).appendTo(image_holder);
                    }
                    image_holder.append('<span title="Remove" class="removeImage"><b>X&nbsp;&nbsp;</b></span> <span style="background-color:#d9534f;padding:2px;">' + $(this)[0].files[i].name + '</span>');
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[i]);
                }

            } else {
                alert("It doesn't supports");
            }
        } else {
            alert("Select Only images");
        }
    });
    $(document).on('click', '#addMoreImage', function () {
        $('.preview-image').each(function (index, element) {
            if ($(element).children().length == 0) {
                $(element).parents('.col-width').remove();
            }
        });
        var count = $('.removeImage').length;
        if (count >= 4) {
            alert("Can't add more than 4 images.");
            return false;
        } else {
            if ($("#StoreReviewImageImage" + count).length) {

                if (($("#StoreReviewImageImage0").length) == 0) {
                    count = 0;
                }
                if (($("#StoreReviewImageImage1").length) == 0) {
                    count = 1;
                }
                if (($("#StoreReviewImageImage2").length) == 0) {
                    count = 2;
                }
                if (($("#StoreReviewImageImage3").length) == 0) {
                    count = 3;
                }
            }
            $("#StoreReviewImageImage" + count).parents('.col-width').remove();
            var div = '<div class="col-width" style="margin-bottom:5px;"><input type="file" id="StoreReviewImageImage' + count + '" accept="image/*" class="user-detail1 hidden" autocomplete="off" name="data[StoreReviewImage][image][' + count + ']"><div class="preview-image"></div></div>';
            $('#appendDiv').append(div);
            $("#StoreReviewImageImage" + count).trigger('click');
        }

    });
    $(document).on('click', '.removeImage', function () {
        $(this).parents('.col-width').remove();
    });
    $(document).on('click', '.editOrderType', function (e) {
        e.stopImmediatePropagation();
        if ($("#addReviewRating").valid()) {
            $.blockUI({css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff'
                }});
        }
    });
</script>
