<div class="main-container">
	<?php //echo $this->Session->flash(); ?>
    <div class="inner-wrap profile rating-review">
        <div class="common-title">
            <h3>Review &amp; Rating - <?php echo $orderName; ?></h3>
        </div>
        <div class="form-section">
            <?php if ($status == 'Pending') { ?>
                <?php
                echo $this->Form->create('StoreReview', array('url' => array('controller' => 'orders', 'action' => 'rating'), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'ReviewRating', 'enctype' => 'multipart/form-data'));
                echo $this->Form->input('StoreReview.user_id', array('type' => 'hidden', 'value' => $user_id));
                echo $this->Form->input('StoreReview.item_name', array('type' => 'hidden', 'value' => $orderName));
                echo $this->Form->input('StoreReview.order_id', array('type' => 'hidden', 'value' => $order_id));
                echo $this->Form->input('StoreReview.store_id', array('type' => 'hidden', 'value' => $decrypt_storeId));
                echo $this->Form->input('StoreReview.merchant_id', array('type' => 'hidden', 'value' => $decrypt_merchantId));
                echo $this->Form->input('StoreReview.order_item_id', array('type' => 'hidden', 'value' => $order_item_id));
                echo $this->Form->input('StoreReview.item_id', array('type' => 'hidden', 'value' => $item_id));
                ?>
                
                <div class="profile-input clearfix">
                    <label>Rating <em>*</em></label>
                    <?php echo $this->Form->input('StoreReview.review_rating', array('data-glyphicon' => 0, 'type' => 'number', 'class' => 'rating user-detail', 'max' => 5, 'min' => 0, 'label' => false, 'div' => false, 'value' => $orderRating)); ?>
                </div>
                <div class="profile-input clearfix">
                    <label>Review </label>
                    <?php echo $this->Form->input('StoreReview.review_comment', array('type' => 'textarea', 'class' => 'user-detail', 'placeholder' => 'Enter Your Review', 'maxlength' => '200', 'label' => false, 'div' => false));
                    ?>
                </div>
                <div class="profile-input clearfix">
                    <label>Images </label>
                    <?php echo $this->Form->input('StoreReviewImage.image][', array('type' => 'file', 'class' => 'user-detail', 'label' => false, 'div' => false, "accept" => "image/*", "multiple", 'id' => 'StoreReviewImage'));
                    ?>
                    <span id="StoreReviewImage-error" class="error hidden" for="StoreContentName">Upload Max 5 Files allowed </span>
                </div>
                <div class="profile-input clearfix mt-20">
                	<div class="row">
                    	<div class="col-sm-6 col-xs-12">
                        	<?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'theme-bg-1 srch-btn green-btn'));?>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                        	<?php echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/orders/myOrders/$encrypted_storeId/$encrypted_merchantId'", 'class' => 'theme-bg-2 clr-link green-btn'));?>
                        </div>
                    </div>
                    
                </div>
                <?php echo $this->Form->end(); ?>
            <?php } ?>
            <br><br>
            <h2><span>Reviews - <?php echo $orderName; ?></span></h2>  
            <?php
            if (!empty($allReviews)) {
                foreach ($allReviews as $reviews) {
                    ?>
                    <i class="fa fa-user"></i> <?php echo ucfirst($reviews['User']['fname']) . ' ' . ucfirst($reviews['User']['lname']); ?>
                    <span><?php echo __('Rated'); ?>:-</span> <input disabled="true" class="rating" data-glyphicon=0 value=<?php echo $reviews['StoreReview']['review_rating']; ?> >
                    <p><?php echo ucfirst($reviews['StoreReview']['review_comment']); ?> <span class="publish-date">- <?php echo date('d M', strtotime($reviews['StoreReview']['created'])) . ' at ' . date('H:i a', strtotime($reviews['StoreReview']['created'])); ?></span></p>
                <?php } ?>
            <?php } else {
                ?>
                <p><span><?php echo __('No review found'); ?></span></p>
            <?php } ?>
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