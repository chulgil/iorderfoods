<style>
    .rating-xs {
        float: left;
        font-size: 1.5em;
        margin-left: 15px;
    }
    .imgPict {
        margin-left: 35px;
    }
    .rating-disabled{
        cursor: default;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <h3>Review & Ratings</h3>
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">   
            <?php echo $this->Form->create('Order', array('url' => array('controller' => 'orders', 'action' => 'reviewRating'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <!--		<div class="col-lg-2">		     
                <?php // echo $this->Form->input('Customer.category_id',array('type'=>'select','class'=>'form-control valid','label'=>false,'div'=>false,'autocomplete' => 'off','options'=>$categoryList,'empty'=>'Select Category')); ?>		
                               </div>-->

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
                    <?php echo $this->Html->link('Clear', array('controller' => 'orders', 'action' => 'reviewRating', 'clear'), array('class' => 'btn btn-default')); ?>
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
                        <th  class="th_checkbox">View Images</th>
                        <th  class="th_checkbox">Action</th>
                    </tr>
                   <!-- <tr>	    
                       <th  class="th_checkbox"><?php //echo $this->Paginator->sort('StoreReview.order_id', 'Order Id');         ?></th>
                       <th  class="th_checkbox"><?php //echo $this->Paginator->sort('StoreReview.review_comment', 'Review');         ?></th>
                       <th  class="th_checkbox"><?php //echo $this->Paginator->sort('StoreReview.review_rating', 'Ratings');         ?></th>
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
                                <td><?php echo @$data['Order']['order_number']; ?></td>
                                <td><?php
                                    echo @$data['Item']['name'];
                                    ?></td>
                                <td><?php
                                    echo "<span title='" . $data['StoreReview']['review_comment'] . "'>" . substr($data['StoreReview']['review_comment'], 0, 50) . "</span>";
                                    ?>
                                </td>
                                <td>

                                    <?php echo $this->Form->input('StoreReview.review_rating', array('disabled' => true, 'data-glyphicon' => 0, 'type' => 'number', 'class' => 'rating', 'max' => 5, 'min' => 0, 'label' => false, 'div' => false, 'value' => $data['StoreReview']['review_rating'])); ?>

                                </td>
                                <td>
                                    <?php if (count($data['StoreReviewImage']) >= 1) { ?>
                                        <button class="btn btn-default imgGet" data-toggle="modal" data-target=".bs-example-modal-lg" data-value="<?php echo $EncryptReviewID; ?>"><i class='fa fa-picture-o'></i></button>
                                        <?php
                                        //echo $this->Html->link("<i class='fa fa-picture-o'></i>",array('controller'=>'orders','action'=>'reviewImages',$EncryptReviewID),array('title'=>'View image status','class'=>'imgPict','escape' => false));   
                                    }
                                    ?>   
                                </td>

                                <td>

                                    <?php
                                    //echo  $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")),array('controller'=>'reports','action'=>'deleteGallaryImage',$EncryptGallaryImageID),array('confirm' => 'Are you sure to delete Image?','escape' => false)); 

                                    echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'orders', 'action' => 'deleteReview', $EncryptReviewID, 1), array('confirm' => 'Are you sure to Delete Review?', 'escape' => false));
//			  if($data['StoreReview']['is_approved']==0){
                                    //			echo $this->Html->link("Approved",array('controller'=>'orders','action'=>'approvedReview',$EncryptReviewID,1),array('confirm' => 'Are you sure to Approve Review?','escape' => false));
//			   echo " ";
//			   echo "|";
//			   echo " ";
//			   echo $this->Html->link("Disapproved",array('controller'=>'orders','action'=>'approvedReview',$EncryptReviewID,2),array('confirm' => 'Are you sure to Disapprove Review?','escape' => false));
//			  }
//			  if($data['StoreReview']['is_approved']==1){
//			    echo "Approved";
//			  }
//			  if($data['StoreReview']['is_approved']==2){
//			    echo "Disapproved";
//			  }
                                    ///if(count($data['StoreReviewImage']) >=1){
                                    // echo "|";
                                    //echo $this->Html->link("<i class='fa fa-edit'></i>",array('controller'=>'orders','action'=>'reviewImages',$EncryptReviewID),array('title'=>'Change image status','escape' => false));   
                                    // }
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
<script>
    $(document).ready(function() {
	 
	$("#StoreReviewReviewRating").change(function(){
	    var ratingId=$("#StoreReviewReviewRating").val();
	    $("#AdminId").submit();
	});
	
   });
</script>

        </div>
        <div class="modal fade" id="storeReviewImageModal" role="dialog">
            <div class="modal-dialog modal-sm">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Image</h4>
                    </div>
                    <div class="modal-body" id="slide">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
        <script>
            $(document).ready(function () {
                $("#UserKeyword").autocomplete({
                    source: "<?php echo $this->Html->url(array('controller' => 'orders', 'action' => 'reviewRatingSearch')); ?>",
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

    </div>
    <script>
        $(document).ready(function () {
            $('.imgGet').click(function () {
                var storeReviewId = $(this).attr('data-value');
                $.ajax({
                    type: 'post',
                    url: '/orders/reviewImageDet',
                    data: {'storeReviewId': storeReviewId},
                    async: false,
                    success: function (result) {
                        $("#slide").html(result);
                        $('#storeReviewImageModal').modal('show');
                    }
                });
            });
        });

    </script>
