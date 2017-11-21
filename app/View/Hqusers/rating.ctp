<!-- static-banner -->
<?php echo $this->element('hquser/static_banner'); ?>
<!-- /banner -->
<div class="signup-form">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="common-title clearfix">
                    <span class="yello-dash"></span>
                    <h2><?php echo __('Review &amp; Rating - ' . $orderName); ?></h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php echo $this->Session->flash(); ?>
                <div class="form-bg">
                    <?php if ($status == 'Pending') { ?>
                        <!-- -->
                        <?php
                        echo $this->Form->create('StoreReview', array('url' => array('controller' => 'hqusers', 'action' => 'rating'), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'ReviewRating', "class" => "sign-up", 'enctype' => 'multipart/form-data'));
                        echo $this->Form->input('StoreReview.user_id', array('type' => 'hidden', 'value' => $user_id));
                        echo $this->Form->input('StoreReview.item_name', array('type' => 'hidden', 'value' => $orderName));
                        echo $this->Form->input('StoreReview.order_id', array('type' => 'hidden', 'value' => $order_id));
                        echo $this->Form->input('StoreReview.store_id', array('type' => 'hidden', 'value' => $decrypt_storeId));
                        echo $this->Form->input('StoreReview.order_item_id', array('type' => 'hidden', 'value' => $order_item_id));
                        echo $this->Form->input('StoreReview.item_id', array('type' => 'hidden', 'value' => $item_id));
                        ?>
                        <!-- CONTENT -->
                        <div class="main-form margin-top35">
                            <div class="form-group clearfix">
                                <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/user.png', array('alt' => 'user')) ?></span><label>Rating <sup>*</sup></label></div>
                                <div class="rgt-box">
                                    <?php echo $this->Form->input('StoreReview.review_rating', array('data-glyphicon' => 0, 'type' => 'number', 'class' => 'rating form-control custom-text', 'max' => 5, 'min' => 0, 'label' => false, 'div' => false, 'value' => $orderRating)); ?>
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/user-fill.png', array('alt' => 'user')) ?></span><label>Review <sup>*</sup></label></div>
                                <div class="rgt-box">
                                    <?php echo $this->Form->input('StoreReview.review_comment', array('type' => 'textarea', 'class' => 'form-control custom-text', 'placeholder' => 'Enter Your Review', 'maxlength' => '200', 'label' => false, 'div' => false));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group twin-block">
                                <div class="left-tile"><span class="label-icon"><?php echo $this->Html->image('hq/mobile.png', array('alt' => 'user')) ?></span><label>Images <sup>*</sup></label>
                                </div>
                                <div class="rgt-box">
                                    <?php echo $this->Form->input('StoreReviewImage.image][', array('type' => 'file', 'class' => 'form-control custom-text', 'label' => false, 'div' => false, "accept" => "image/*", "multiple", 'id' => 'StoreReviewImage'));
                                    ?>
                                    <label id="StoreReviewImage-error" class="error hidden" for="StoreContentName">Upload Max 5 Files allowed </label>
                                </div>
                            </div>
                            <div class="submit-btn">
                                <?php
                                echo $this->Form->button('SAVE', array('type' => 'submit', 'class' => 'btn common-config black-bg'));
                                echo $this->Form->button('CANCEL', array('type' => 'button', 'onclick' => "window.location.href='/hqusers/myOrders'", 'class' => 'btn common-config black-bg'));
                                ?>
                            </div>
                        </div>
                        <?php echo $this->Form->end(); ?>
                        <!-- /CONTENT END -->
                        <!-- -->
                    <?php } ?>
                    <div class="sign-up order-content order-content-tabs">
                        <div class="tab-content">
                            <!-- MY FAVORITES -->
                            <div role="tabpanel" class="tab-pane active">
                                <div class="tab-panes">
                                    <h2 class="tab-panes-title"><?php echo __('Reviews - ' . $orderName); ?></h2>
                                    <?php
                                    if (!empty($allReviews)) {
                                        foreach ($allReviews as $reviews) {
                                            ?>
                                            <div class="review-box">
                                                <div class="review-box-header clearfix">
                                                    <h3><i class="fa fa-user"></i> <?php echo ucfirst($reviews['User']['fname']) . ' ' . ucfirst($reviews['User']['lname']); ?></h3>
                                                    <div class="review-ratting"><span>Rated : </span><input disabled="true" class="rating" data-glyphicon=0 value=<?php echo $reviews['StoreReview']['review_rating']; ?>/></div>
                                                </div>
                                                <div class="review-box-content">
                                                    <p><?php echo ucfirst($reviews['StoreReview']['review_comment']); ?></p>
                                                    <span class="date-time"><?php echo date('d M Y', strtotime($reviews['StoreReview']['created'])) . ' at ' . date('H:i a', strtotime($reviews['StoreReview']['created'])); ?></span>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }else{
                                        echo "No Review Found.";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ext-border">
                        <?php echo $this->Html->image('hq/thick-border.png', array('alt' => 'user')) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $(".green-btn").click(function (e) {
            var input = document.getElementById('StoreReviewImage');
            if (input.files.length <= 5) {
                $('#StoreReviewImage-error').addClass('hidden');
            } else {
                $('#StoreReviewImage-error').removeClass('hidden');
                e.preventDefault();
            }

        });
        $('#StoreReviewImage').change(function (e) {
            //get the input and the file list
            var input = document.getElementById('StoreReviewImage');
            if (input.files.length <= 5) {
                $('#StoreReviewImage-error').addClass('hidden');
            } else {
                $('#StoreReviewImage-error').removeClass('hidden');
                e.preventDefault();
            }
        });
    });
</script>