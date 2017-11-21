<!--******************Review area start here***********************-->
<div role="tabpanel" class="tab-pane" id="review">
    <div class="row">
        <div class="col-sm-12">
            <?php echo $this->element('pagination'); ?>
        </div>
    </div>
    <?php echo $this->element('show_pagination_count'); ?>
    <table class="table table-bordered table-hover table-striped tablesorter">
        <thead>
            <tr>	    
                <th  class="th_checkbox">Review on Item</th>
                <th  class="th_checkbox"><?php echo $this->Paginator->sort('StoreReview.review_comment', 'Review'); ?></th>
                <th  class="th_checkbox"><?php echo $this->Paginator->sort('StoreReview.review_rating', 'Rating'); ?></th>
                <th  class="th_checkbox"><?php echo $this->Paginator->sort('StoreReview.created', 'Review Date'); ?></th>

            </tr>
        </thead>
        <tbody class="dyntable">
            <?php
            $i = 0;
            $class = ($i % 2 == 0) ? ' class="active"' : '';
            ?>
            <?php
            if (!empty($myReviews)) {
                foreach ($myReviews as $review) {
                    ?>
                    <tr >	    
                        <td>
                            <?php echo (!empty($review['OrderItem']['Item']['name']))?$review['OrderItem']['Item']['name']:''; ?>
                        </td>
                        <td>
                            <?php echo ucfirst($review['StoreReview']['review_comment']); ?>
                        </td>
                        <td>
                            <input disabled="disabled" type="number" class="rating" min=0 max=5 data-glyphicon=0 value=<?php echo $review['StoreReview']['review_rating']; ?> >
                        </td>
                        <td>
                            <?php echo $this->Dateform->us_format($this->Hq->storeTimezone(null, $review['StoreReview']['created'], null, $review['StoreReview']['store_id'])); ?>
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
    <?php echo $this->element('pagination'); ?>
</div>
<!--******************Review area end here***********************-->