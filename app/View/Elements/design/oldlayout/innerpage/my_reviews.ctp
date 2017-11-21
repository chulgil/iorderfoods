
<style>
    .btn{
        font-size: 14px;
    }
    .blue{
        margin-left:-255px;
        font-size: 12px
    }
    #horizontalTab { float:left;margin-top:15px;width:100%;}
</style>
<?php 
$storeId = $this->Session->read('store_id');
?>
	<div class="pad-TP60 clearfix">
		<?php //echo $this->Session->flash(); ?>
        <div class="order-hostory form-layout clearfix">
<!--            <form name="select-order-type" method="post" action="javascript:void(0);">-->
               <h2>
               	<span><?php echo __('My Reviews');?></span>
                <?php echo $this->Html->link('Add Review',array('controller' => 'orders',  'action' => 'myOrders', $encrypted_storeId, $encrypted_merchantId),array('class'=>'btn green-btn add-review-link', 'style'=>'font-size:22px; margin-top:-5px; float:right;'));?>
               </h2>
<div>
    <div>
        <hr>
        <?php echo $this->Form->create('Pannel', array('url' => array('controller' => 'pannels', 'action' => 'myReviews'), 'id' => 'AdminId', 'type' => 'post')); ?>
        <?php echo $this->element('userprofile/filter_store'); ?>
        
        <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn green-btn')); ?>
        <?php echo $this->Html->link('Clear', array('controller' => 'pannels', 'action' => 'myReviews', 'clear'), array('class' => 'btn green-btn')); ?>
        <?php echo $this->Form->end(); ?>
        <span class="blue">(<b>Search by:</b> Review, Rating)</span>
    </div>
</div>
                <div class="paging_full_numbers" id="example_paginate" style="padding-top:10px">
                <?php
                echo $this->Paginator->first('First');
                // Shows the next and previous links
                echo $this->Paginator->prev('Previous', null, null, array('class' => 'disabled'));
                // Shows the page numbers
                echo $this->Paginator->numbers(array('separator' => ''));
                echo $this->Paginator->next('Next', null, null, array('class' => 'disabled'));
                // prints X of Y, where X is current page and Y is number of pages
                //echo $this->Paginator->counter();
                echo $this->Paginator->last('Last');
                ?>
            </div>
                <div id="horizontalTab">
                <!-- FORM VIEW -->
                <div class="resp-tabs-container">
                        <div class="repeat-deatil">                	
		            <div class="resp-tabs-frame">
                                <div class="responsive-table">
                                    <table class="table table-striped order-history-table">
                                        <tr>
                                            <th><?php echo __('Review on Item');?></th>
                                            <th><?php echo __('Review');?></th>
                                            <th class="text-center"><?php echo __('Rating');?></th>
                                            <th class="text-center"><?php echo __('Review Date');?></th>
                                            <th class="text-center"><?php echo __('Status');?></th>
                                            <th class="text-center"><?php echo __('Store');?></th>
                                            <th class="text-center"><?php echo __('Action');?></th>
                                        </tr>
                                        <?php if(!empty($myReviews)){ 
                                            
                                            foreach($myReviews as $review) { ?>
                                           <tr>
                                            
                                            <td><?php echo $review['OrderItem']['Item']['name'];?></td>
                                            <td><?php echo ucfirst($review['StoreReview']['review_comment']);?></td>
                                            
                                            <td class="text-center"><input disabled="disabled" type="number" class="rating" min=0 max=5 data-glyphicon=0 value=<?php echo $review['StoreReview']['review_rating'];?> ></td>
                                            <td class="text-center"><?php echo $this->Common->storeTimeFormateUser($this->Common->storeTimeZoneUser('', $review['StoreReview']['created']), true); ?></td>
                                            <td class="text-center"><?php if($review['StoreReview']['is_approved'] ==  0){
                                                echo "Pending";
                                            }else if($review['StoreReview']['is_approved'] ==  1){
                                                echo "Approved";
                                            }else if($review['StoreReview']['is_approved'] ==  2){
                                                echo "Dis-Approved";
                                            }?> </td>
                                            <td>
                                                    <?php
                                                    if (!empty($review['Store'])) {
                                                        echo $review['Store']['store_name'];
                                                    }
                                                    ?> </td>
                                            <td class="text-center"> <?php 
                                            if (!empty($storeId)) {
                                                    if ($review['StoreReview']['store_id'] == $storeId) {
                                            
                                            echo $this->Html->link($this->Html->tag('i','',array('class'=>'fa fa-trash-o')).'Delete',array('controller'=>'pannels','action'=>'deleteReview',$encrypted_storeId,$encrypted_merchantId,$this->Encryption->encode($review['StoreReview']['id'])),array('confirm' => __('Are you sure you want to delete this review?'),'class'=>'delete','escape'=>false)); 
                                                    }else{
                                                        echo "-";
                                                    }
                                            }
                                            ?>
                                            </td>

                                          </tr>
                                                <?php } } else {
                                                    echo '<tr><td class="text-center" colspan="6">'.__('No review found').'</td></tr>';
                                                }?>
    
                                    </table>
                                </div>
                            </div>
                            <div class="paging_full_numbers" id="example_paginate" style="padding-top:10px">
                <?php
                echo $this->Paginator->first('First');
                // Shows the next and previous links
                echo $this->Paginator->prev('Previous', null, null, array('class' => 'disabled'));
                // Shows the page numbers
                echo $this->Paginator->numbers(array('separator' => ''));
                echo $this->Paginator->next('Next', null, null, array('class' => 'disabled'));
                // prints X of Y, where X is current page and Y is number of pages
                //echo $this->Paginator->counter();
                echo $this->Paginator->last('Last');
                ?>
             <?php echo $this->Html->css('pagination'); ?>
            </div>
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