<style>
    .rating-xs {
        float: left;
        font-size: 1.5em;
        margin-left: 15px;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <h3>Review & Ratings</h3>
        <hr>
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">
            <?php echo $this->Form->create('StoreReview', array('url' => array('controller' => 'hq', 'action' => 'reviewRating'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <!--		<div class="col-lg-2">		     
                <?php // echo $this->Form->input('Customer.category_id',array('type'=>'select','class'=>'form-control valid','label'=>false,'div'=>false,'autocomplete' => 'off','options'=>$categoryList,'empty'=>'Select Category')); ?>		
                               </div>-->
                <div class="row">
                    <div class="col-lg-6" style="margin-left: 15px;">
                        <?php
                        $merchantList = $this->Common->getHQStores($merchantId);
                        echo $this->Form->input('Merchant.store_id', array('options' => $merchantList, 'class' => 'form-control', 'div' => false, 'empty' => 'Please Select Store'));
                        ?>
                        <span class="blue">(For Store related features, select a store to proceed.)</span>
                        <br/>
                    </div>
                </div>

                <div class="col-lg-2">		     
                    <?php
                    $options = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5');
                    echo $this->Form->input('StoreReview.review_rating', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Rating'));
                    ?>		
                </div>


                <div class="col-lg-3">		     
                    <?php echo $this->Form->input('User.keyword', array('value' => $keyword, 'label' => false, 'div' => false, 'placeholder' => 'Keyword Search', 'class' => 'form-control')); ?>
                    <span class="blue">(<b>Search by:</b>Order Id,Review)</span>
                </div>



                <div class="col-lg-2">		 
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                    <?php echo $this->Html->link('Clear', array('controller' => 'hq', 'action' => 'reviewRating', 'clear'), array('class' => 'btn btn-default')); ?>
                </div>
                <!-- <div class="col-lg-2">		  
                     <div class="addbutton">                
                <?php //echo $this->Form->button('Add Menu Item', array('type' => 'button','onclick'=>"window.location.href='/items/addMenuItem'",'class' => 'btn btn-default')); ?>  
                     </div>
                 </div>-->
            </div>
            <?php echo $this->Form->end(); ?>
            <?php
            if (!empty($list)) {
                echo $this->element('show_pagination_count');
            }
            ?>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>	    
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('Order.order_number', 'Order Id'); ?></th>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('Item.name', 'Item'); ?></th>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('StoreReview.review_comment', 'Review'); ?></th>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('StoreReview.review_rating', 'Ratings'); ?></th>
                        <th  class="th_checkbox">Status : <?php echo @$this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>
                        <th  class="th_checkbox">Action</th>
                    </tr>
                   <!-- <tr>	    
                       <th  class="th_checkbox"><?php //echo $this->Paginator->sort('StoreReview.order_id', 'Order Id');               ?></th>
                       <th  class="th_checkbox"><?php //echo $this->Paginator->sort('StoreReview.review_comment', 'Review');               ?></th>
                       <th  class="th_checkbox"><?php //echo $this->Paginator->sort('StoreReview.review_rating', 'Ratings');               ?></th>
                       <th  class="th_checkbox">Action</th>
                    </tr>-->
                </thead>

                <tbody class="dyntable">
                    <?php
                    if (!empty($list)) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptReviewID = $this->Encryption->encode($data['StoreReview']['id']);
                            ?>
                            <tr <?php echo $class; ?>>	    
                                <td><?php echo $data['Order']['order_number']; ?></td>
                                <td><?php
                                    echo (!empty($data['Item']['name'])) ? $data['Item']['name'] : '';
                                    ?></td>
                                <td><?php
                                    echo "<span title='" . $data['StoreReview']['review_comment'] . "'>" . substr($data['StoreReview']['review_comment'], 0, 50) . "</span>";
                                    ?>
                                </td>
                                <td>

                                    <?php echo $this->Form->input('StoreReview.review_rating', array('disabled' => true, 'data-glyphicon' => 0, 'type' => 'number', 'class' => 'rating', 'max' => 5, 'min' => 0, 'label' => false, 'div' => false, 'value' => $data['StoreReview']['review_rating'])); ?>

                                </td>
                                <td>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'hq', 'action' => 'deleteReviewRating', $EncryptReviewID), array('confirm' => 'Are you sure to delete review.?', 'escape' => false)); ?>
                                </td>

                                <td>

                                    <?php
                                    if ($data['StoreReview']['is_approved'] == 0) {
                                        echo $this->Html->link("Approved", array('controller' => 'hq', 'action' => 'approvedReview', $EncryptReviewID, 1), array('confirm' => 'Are you sure to Approve Review?', 'escape' => false));
                                        echo " ";
                                        echo "|";
                                        echo " ";
                                        echo $this->Html->link("Disapproved", array('controller' => 'hq', 'action' => 'approvedReview', $EncryptReviewID, 2), array('confirm' => 'Are you sure to Disapprove Review?', 'escape' => false));
                                    }
                                    if ($data['StoreReview']['is_approved'] == 1) {
                                        echo "Approved";
                                    }
                                    if ($data['StoreReview']['is_approved'] == 2) {
                                        echo "Disapproved";
                                    }
                                    ?>

                                </td>
                            </tr>
                            <?php
                            $i++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="11" style="text-align: center;">
                                No record available
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>  
            <?php
            if (!empty($list)) {
                echo $this->element('pagination');
            }
            ?>

        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        var storeId = $('#MerchantStoreId').val();
        $("#UserKeyword").autocomplete({
            source: "/hq/reviewRatingSearch?storeID=" + storeId,
            minLength: 3,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                    .append("<div>" + item.desc + "</div>")
                    .appendTo(ul);
        }
        $("#StoreReviewReviewRating").change(function () {
            var ratingId = $("#StoreReviewReviewRating").val();
            $("#AdminId").submit();
        });

    });
</script>
<script>
    $(document).ready(function () {
        $("#MerchantStoreId").change(function () {
            var StoreId = $("#MerchantStoreId").val();
            $("#AdminId").submit();
        });

    });
</script>