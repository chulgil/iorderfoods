<style>
    .form-control.user-detail {
    width: 250px;
}
</style>
<div class="main-container">
    <?php echo $this->Session->flash(); ?>
    <div class="inner-wrap profile">
        <div class="form-section">
                <?php
                echo $this->Form->create('StoreReview', array('url' => array('controller' => 'reports', 'action' => 'imageGallary'), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'ReviewRating', 'enctype' => 'multipart/form-data'));
                ?>
                
                <div class="profile-input clearfix">
                    <label>Images </label>
                    <?php echo $this->Form->input('StoreReviewImage.image][', array('type' => 'file', 'class' => 'form-control user-detail', 'label' => false, 'div' => false, "accept" => "image/*", "multiple", 'id' => 'StoreReviewImage','style' => "box-sizing:initial;"));
                    ?>
                    <span id="StoreReviewImage-error" class="error hidden" for="StoreContentName">Upload Max 5 Files allowed </span>
                </div><br>
                <div class="profile-input clearfix">
                    <?php
                    echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default'));?> &nbsp;&nbsp;
                     <?php echo $this->Html->link('Cancel', array('controller' => 'reports', 'action' => 'imageGallary'), array('class' => 'btn btn-default')); 
                    ?>
                </div>
                <?php echo $this->Form->end(); ?>
            
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

<hr>

<style>
    .openImage{
        cursor: pointer;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <h3>Images</h3>
        
        <div class="table-responsive">
            <div class="updateOrdersData">
                <table class="table table-bordered table-hover table-striped tablesorter">
                    <thead>
                        <tr>
<!--                            <th  class="th_checkbox"><input type="checkbox" id="selectall"/></th>-->
                            <th  class="th_checkbox">Image</th>
                            <th  class="th_checkbox">Status</th>
                            <th  class="th_checkbox">Action</th>
                        </tr>
                    </thead>
                    <tbody class="dyntable">
                        <?php
                        if (!empty($storeReviewImages)) {
                            foreach ($storeReviewImages as $key => $data) {
                                ?>
                                <tr  class="text-center">
<!--                                    <td>
                                        <?php// echo $this->Form->checkbox('StoreReviewImage.id.' . $key, array('class' => 'case', 'value' => $data['StoreReviewImage']['id'])); ?>
                                    </td>-->
                                    <td>
                                        <?php echo $this->Html->image('/storeReviewImage/' . $data['StoreReviewImage']['image'], array('width' => 100, "class" => "openImage")); ?>
                                    </td>
                                    <td>
                                      <?php 
                                      $EncryptGallaryImageID=$this->Encryption->encode($data['StoreReviewImage']['id']);
                                      
                                      if($data['StoreReviewImage']['is_active']){
			   echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")),array('controller'=>'reports','action'=>'activateGallaryImage',$EncryptGallaryImageID,0),array('confirm' => 'Are you sure to Deactivate Image?','escape' => false));
			}else{
			   echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")),array('controller'=>'reports','action'=>'activateGallaryImage',$EncryptGallaryImageID,1),array('confirm' => 'Are you sure to Activate Image?','escape' => false));
			} ?>

                                    </td>
                                    <td>
                                    <?php echo  $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")),array('controller'=>'reports','action'=>'deleteGallaryImage',$EncryptGallaryImageID),array('confirm' => 'Are you sure to delete Image?','escape' => false)); ?>
                                     </td>    
                                </tr>    
                                <?php
                            }
                        } else { ?>
                            <tr>
                                <td colspan="11" style="text-align: center;">
                                    No record available
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-12">		 
        <?php //echo $this->Html->link('Cancel', array('controller' => 'reports', 'action' => 'imageGallary'), array('class' => 'btn btn-default')); ?>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="storeReviewImageModal" role="dialog">
    <div class="modal-dialog modal-sm">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Image</h4>
            </div>
            <div class="modal-body">
                <img class="addSrc" width="100%" alt="" src="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<script>
    $(document).ready(function () {
        $(".openImage").click(function () {
            var src = $(this).attr('src');
            $(".addSrc").attr("src", src);
            $('#storeReviewImageModal').modal('show');
        });
        $('#storeReviewImageModal').on('hidden.bs.modal', function () {
            $(".addSrc").attr("src", "");
        });
    });
    $("#selectall").click(function () {
        var st = $("#selectall").prop('checked');
        $('.case').prop('checked', st);
    });
    // if all checkbox are selected, check the selectall checkbox
    // and viceversa
    $(".case").click(function () {
        if ($(".case").length == $(".case:checked").length) {
            $("#selectall").attr("checked", "checked");
        } else {
            $("#selectall").removeAttr("checked");
        }

    });
    function check()
    {
        var statusId = $("#StoreReviewImageStatus").val();
        var fields = $(".case").serializeArray();
        if (fields.length == 0)
        {
            alert('Please select atleast one image to proceed.');
            // cancel submit
            return false;
        }
        if (statusId == '') {
            alert('Please select status.');
            return false;
        }
    }
    (function ($) {
        "use strict";
        function centerModal() {
            $(this).css('display', 'block');
            var $dialog = $(this).find(".modal-dialog"),
                    offset = ($(window).height() - $dialog.height()) / 2,
                    bottomMargin = parseInt($dialog.css('marginBottom'), 10);

            // Make sure you don't hide the top part of the modal w/ a negative margin if it's longer than the screen height, and keep the margin equal to the bottom margin of the modal
            if (offset < bottomMargin)
                offset = bottomMargin;
            $dialog.css("margin-top", offset);
        }

        $(document).on('show.bs.modal', '.modal', centerModal);
        $(window).on("resize", function () {
            $('.modal:visible').each(centerModal);
        });
    }(jQuery));
</script>