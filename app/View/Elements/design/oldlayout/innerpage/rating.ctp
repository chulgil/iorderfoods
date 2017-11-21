
<?php if ($status == 'Pending') { ?>
    <div class="content single-frame">
        <div class="wrap">
            <?php
            //echo $this->Session->flash();
            echo $this->Form->create('StoreReview', array('url' => array('controller' => 'orders', 'action' => 'rating'), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'ReviewRating', 'enctype' => 'multipart/form-data'));
            echo $this->Form->input('StoreReview.user_id', array('type' => 'hidden', 'value' => $user_id));
            echo $this->Form->input('StoreReview.item_name', array('type' => 'hidden', 'value' => $orderName));
            echo $this->Form->input('StoreReview.order_id', array('type' => 'hidden', 'value' => $order_id));
            echo $this->Form->input('StoreReview.store_id', array('type' => 'hidden', 'value' => $decrypt_storeId));
            echo $this->Form->input('StoreReview.merchant_id', array('type' => 'hidden', 'value' => $decrypt_merchantId));
            echo $this->Form->input('StoreReview.order_item_id', array('type' => 'hidden', 'value' => $order_item_id));
            echo $this->Form->input('StoreReview.item_id', array('type' => 'hidden', 'value' => $item_id));
            ?>
            <div class="clearfix">
                <section class="form-layout delivery-form center-form-layout full-width-form">
                    <h2><span>Review &amp; Rating - <?php echo $orderName; ?></span></h2>
                    <ul>
                        <li>
                            <span class="title"><label>Rating <em>*</em></label></span>
                            <div class="title-box">
                                <?php echo $this->Form->input('StoreReview.review_rating', array('data-glyphicon' => 0, 'type' => 'number', 'class' => 'rating inbox', 'max' => 5, 'min' => 0, 'label' => false, 'div' => false, 'value' => $orderRating)); ?>
                            </div>
                        </li>

                        <li>
                            <span class="title"><label>Review </label></span>
                            <div class="title-box">
                                <?php echo $this->Form->input('StoreReview.review_comment', array('type' => 'textarea', 'class' => 'inbox', 'placeholder' => 'Enter Your Review', 'maxlength' => '200', 'label' => false, 'div' => false));
                                ?>
                            </div>
                        </li>
                        <li>
                            <span class="title"><label>Images </label></span>
                            <div class="title-box">
                                <?php echo $this->Form->input('StoreReviewImage.image][', array('type' => 'file', 'class' => 'inbox', 'label' => false, 'div' => false, "accept" => "image/*", "multiple",'id'=>'StoreReviewImage'));
                                ?>
                                <label id="StoreReviewImage-error" class="error hidden" for="StoreContentName">Upload Max 5 Files allowed </label>
                            </div>
                        </li>
                    </ul> <div class="clr"></div>
                    <div class="button">
                        <?php
                        echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn green-btn'));
                        echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/orders/myOrders/$encrypted_storeId/$encrypted_merchantId'", 'class' => 'btn green-btn'));
                        ?>
                    </div>
                </section>
            </div><?php echo $this->Form->end(); ?>
        </div>
    </div>
<?php } ?>

<div class="content single-frame">
    <div class="wrap">
        <div class="clearfix">
            <section class="form-layout sign-up no-image editable-form">
                <h2> <span>Reviews - <?php echo $orderName; ?></span></h2>    	

                <div>
                    <div class="repeat-deatil">       
                        <?php
                        if (!empty($allReviews)) {
                            foreach ($allReviews as $reviews) {
                                ?>
                                <div class="order-history-detail order-history-detail-lreview clearfix">
                                    <div class="order-history-detail-lt order-history-detail-lreview-lt">
                                        <div class="review-margin pline-height clearfix">
                                            <div class="review-margin-lt">
                                                <i class="fa fa-user"></i> <?php echo ucfirst($reviews['User']['fname']) . ' ' . ucfirst($reviews['User']['lname']); ?>
                                            </div>

                                            <div class="review-margin-rt">
                                                <span><?php echo __('Rated'); ?>:-</span> <input disabled="true" class="rating" data-glyphicon=0 value=<?php echo $reviews['StoreReview']['review_rating']; ?> >
                                            </div>
                                        </div>

                                        <div class="review-description">
                                            <p><?php echo ucfirst($reviews['StoreReview']['review_comment']); ?> <span class="publish-date">- <?php echo $this->Common->storeTimeFormateUser($this->Common->storeTimeZoneUser('', $reviews['StoreReview']['created']), true); ?></span></p>
                                        </div>
                                    </div>

                                </div>  <?php } ?><div class="clr"></div>
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