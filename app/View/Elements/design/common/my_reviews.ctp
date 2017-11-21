<?php $storeId = $this->Session->read('store_id'); ?>
<div class="title-bar">My Reviews</div>
<div class="main-container">
    <div class="ext-menu-title">
        <h4><?php echo __('My Reviews'); ?></h4>
    </div>
    <div class="inner-wrap myreview">
        <?php //echo $this->Session->flash(); ?>
        <div class="common-title clearfix custom-title-wrap">
            <div class="col-md-3 col-sm-3 col-xs-4 pull-right">
                <?php echo $this->Html->link('Add Review', array('controller' => 'orders', 'action' => 'myOrders', $encrypted_storeId, $encrypted_merchantId), array('class' => 'theme-bg-1 srch-btn pull-right')); ?>
            </div>
        </div>
        <div class="form-section">
            <?php echo $this->Form->create('Pannel', array('url' => array('controller' => 'pannels', 'action' => 'myReviews'), 'id' => 'AdminId', 'type' => 'post', 'class' => 'clearfix tab-form')); ?>
            <?php echo $this->element('userprofile/filter_store'); ?>
            <div class="col-lg-4 col-sm-4 search-btm-btn">
                <div class="row">
                    <div class="col-lg-6 col-sm-6 col-xs-6">
                        <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'srch-btn theme-bg-1')); ?>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-xs-6">
                        <?php echo $this->Html->link('Clear', array('controller' => 'pannels', 'action' => 'myReviews', 'clear'), array('class' => 'clr-link theme-bg-2')); ?>
                    </div>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <div class="inner-div clearfix">
                <?php echo $this->element('pagination'); ?>
                <div class="responsive-table my-rev-tbl-wrap">
                    <table class="table table-striped">
                        <tr>
                            <th><?php echo __('Review on Item'); ?></th>
                            <th><?php echo __('Review'); ?></th>
                            <th class=""><?php echo __('Rating'); ?></th>
                            <th class=""><?php echo __('Review Date'); ?></th>
                            <th class=""><?php echo __('Status'); ?></th>
                            <th class=""><?php echo __('Store'); ?></th>
                            <th class=""><?php echo __('Action'); ?></th>
                        </tr>
                        <?php
                        if (!empty($myReviews)) {
                            foreach ($myReviews as $review) {
                                ?>
                                <tr>
                                    <td><?php
                                        if (!empty($review['OrderItem']['item_id'])) {
                                            echo $review['OrderItem']['Item']['name'];
                                        } else {
                                            echo $review['OrderItem']['Item']['name'] = "";
                                        }
                                        ?></td>
                                    <td><?php echo ucfirst($review['StoreReview']['review_comment']); ?></td>
                                    <td class=""><input disabled="disabled" type="number" class="rating" min=0 max=5 data-glyphicon=0 value=<?php echo $review['StoreReview']['review_rating']; ?> ></td>
                                    <td class=""><?php echo $this->Common->storeTimeFormateUser($this->Common->storeTimeZoneUser('', $review['StoreReview']['created']), true); ?></td>
                                    <td class=""><?php
                                if ($review['StoreReview']['is_approved'] == 0) {
                                    echo "Pending";
                                } else if ($review['StoreReview']['is_approved'] == 1) {
                                    echo "Approved";
                                } else if ($review['StoreReview']['is_approved'] == 2) {
                                    echo "Dis-Approved";
                                }
                                        ?> </td>
                                    <td>
                                        <?php
                                        if (!empty($review['Store'])) {
                                            echo $review['Store']['store_name'];
                                        }
                                        ?> </td>
                                    <td class=""> <?php
                                if (!empty($storeId)) {
                                    if ($review['StoreReview']['store_id'] == $storeId) {
                                        echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-trash-o')) . 'Delete', array('controller' => 'pannels', 'action' => 'deleteReview', $encrypted_storeId, $encrypted_merchantId, $this->Encryption->encode($review['StoreReview']['id'])), array('confirm' => __('Are you sure you want to delete this review?'), 'class' => '', 'escape' => false));
                                    } else {
                                        echo "-";
                                    }
                                }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td class="" colspan="6">' . __('No review found') . '</td></tr>';
                        }
                        ?>
                    </table>
                </div>
                <?php echo $this->element('pagination'); ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#MerchantStoreId").change(function () {
            $("#AdminId").submit();
        });
    });
</script>