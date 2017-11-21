<!-- static-banner -->
<?php echo $this->element('hquser/static_banner'); ?>
<!-- /banner -->
<div class="signup-form">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="common-title clearfix">
                    <span class="yello-dash"></span>
                    <h2><?php echo __('My Reviews'); ?></h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php echo $this->Session->flash(); ?>
                <div class="form-bg">
                    <!-- ORDER TABS -->
                    <div class="sign-up order-content order-content-tabs">                        
                        <!-- SEARCH -->
                        <div class="tabs-search margin-top0 clearfix">
                            <?php echo $this->Form->create('Pannel', array('url' => array('controller' => 'hqusers', 'action' => 'myReviews'), 'id' => 'AdminId', 'type' => 'post')); ?>
                            <div class="col-2">
                                <?php
                                $merchantList = $this->Common->getStores($this->Session->read('hq_id'));
                                echo $this->Form->input('Merchant.store_id', array('options' => $merchantList, 'class' => 'inbox', 'div' => false, 'empty' => 'Please Select Store', 'label' => FALSE));
                                ?>
                            </div>
                            <div class="col-2 tab-search-right">
                                <div>
                                    <?php
                                    $val = '';
                                    if (isset($keyword) && !empty($keyword)) {
                                        $val = $keyword;
                                    }
                                    ?>
                                    <?php echo $this->Form->input('User.keyword', array('value' => $val, 'label' => false, 'div' => false, 'placeholder' => 'Search (Review, Rating)', 'class' => 'inbox')); ?>
                                </div>
                                <div class="searchh-btn">
                                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn common-config black-bg')); ?>
                                    <?php echo $this->Html->link('Clear', array('controller' => 'hqusers', 'action' => 'myReviews', 'clear'), array('class' => 'btn common-config black-bg')); ?>
                                </div>
                            </div>
                            <?php echo $this->Form->end(); ?>
                        </div>

                        <!-- PAGINATION -->
                        <?php echo $this->element('pagination');?>

                        <!-- TAB PANES -->
                        <?php echo $this->element('show_pagination_count');?>
                        <div class="tab-content">
                            <!-- MY FAVORITES -->
                            <div role="tabpanel" class="tab-pane active">
                                <div class="tab-panes">                                
                                    <div class="table-responsive">
                                        <table class="table table-striped tab-panes-table">
                                            <thead>
                                                <tr>
                                                    <th><?php echo __('Review on Item'); ?></th>
                                                    <th><?php echo __('Review'); ?></th>
                                                    <th class="text-center"><?php echo __('Rating'); ?></th>
                                                    <th class="text-center"><?php echo __('Review Date'); ?></th>
                                                    <th class="text-center"><?php echo __('Status'); ?></th>
                                                    <th class="text-center"><?php echo __('Store'); ?></th>
                                                    <th class="text-center"><?php echo __('Action'); ?></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php
                                                if (!empty($myReviews)) {

                                                    foreach ($myReviews as $review) {
                                                        ?>
                                                        <tr>

                                                            <td><?php echo @$review['OrderItem']['Item']['name']; ?></td>
                                                            <td><?php echo ucfirst($review['StoreReview']['review_comment']); ?></td>

                                                            <td class="text-center"><input disabled="disabled" type="number" class="rating" min=0 max=5 data-glyphicon=0 value=<?php echo $review['StoreReview']['review_rating']; ?> ></td>
                                                            <td class="text-center"><?php echo date('d M Y -  H:i a', strtotime($this->Hq->storeTimeZone('', $review['StoreReview']['created'], '', $review['StoreReview']['store_id']))); ?></td>
                                                            <td class="text-center"><?php
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
                                                            <td class="text-center"> <?php
//                                            if (!empty($storeId)) {
//                                                    if ($review['StoreReview']['store_id'] == $storeId) {

                                                                echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-trash-o')), array('controller' => 'hqusers', 'action' => 'deleteReview', $this->Encryption->encode($review['StoreReview']['id'])), array('confirm' => __('Are you sure you want to delete this review?'), 'class' => 'delete', 'escape' => false));
//                                                    }else{
//                                                        echo "-";
//                                                    }
//                                            }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                } else {
                                                    echo '<tr><td class="text-center" colspan="6">' . __('No review found') . '</td></tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /TAB PANES END -->

                        <!-- PAGINATION -->
                        <?php echo $this->element('pagination');?>
                    </div>
                    <!-- ORDER TABS END -->
                    <!-- -->
                    <div class="ext-border">
                        <?php echo $this->Html->image('hq/thick-border.png', array('alt' => 'user')) ?>
                    </div>
                    <!-- -->
                </div>
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