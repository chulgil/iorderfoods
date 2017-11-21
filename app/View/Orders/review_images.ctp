<style>
    .openImage{
        cursor: pointer;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <h3>Store Review Images</h3>
        <br>
        <?php echo $this->Session->flash(); ?>
        <div class="table-responsive">
            <?php echo $this->Form->create('StoreReviewImage', array('url' => array('controller' => 'orders', 'action' => 'reviewImages', $store_review_id), 'id' => 'reviewImageId', 'type' => 'post')); ?>
            <div class="updateOrdersData">
                <table class="table table-bordered table-hover table-striped tablesorter">
                    <thead>
                        <tr>
                            <th  class="th_checkbox"><input type="checkbox" id="selectall"/></th>
                            <th  class="th_checkbox">Image</th>
                            <th  class="th_checkbox">Status</th>
                        </tr>
                    </thead>
                    <tbody class="dyntable">
                        <?php
                        if (!empty($storeReviewImages)) {
                            foreach ($storeReviewImages as $key => $data) {
                                ?>
                                <tr  class="text-center">
                                    <td>
                                        <?php echo $this->Form->checkbox('StoreReviewImage.id.' . $key, array('class' => 'case', 'value' => $data['StoreReviewImage']['id'])); ?>
                                    </td>
                                    <td>
                                        <?php echo $this->Html->image('/storeReviewImage/' . $data['StoreReviewImage']['image'], array('width' => 100, "class" => "openImage")); ?>
                                    </td>
                                    <td>
                                        <?php if ($data['StoreReviewImage']['is_active'] == 1) { ?>
                                            <span class="label label-success">Approved</span>
                                        <?php } elseif ($data['StoreReviewImage']['is_active'] == 2) { ?>
                                            <span class="label label-danger">Disapproved</span>
                                        <?php } else { ?>
                                            <span class="label label-default">Pending</span>
                                        <?php } ?>

                                    </td>
                                </tr>    
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="11" style="text-align: center;">
                                    No record available
                                </td>
                            </tr>
                        <?php } if ($storeReviewImages) { ?>
                            <tr>
                                <td colspan="6">
                                    <?php
                                    $statusList = array(1 => 'Approved', 2 => 'Disapproved', 3 => 'Delete');
                                    echo $this->Form->input('StoreReviewImage.status', array('type' => 'select', 'style' => 'background-color:white;text-align:left;', 'class' => 'btn btn-default', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => @$statusList, 'empty' => 'Select Status'));
                                    ?>	&nbsp;&nbsp;&nbsp;&nbsp;
                                    <?php echo $this->Form->button('Update Multiple Status', array('type' => 'submit', 'class' => 'btn btn-default', 'onclick' => 'return check();'));
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
    <div class="col-lg-12">		 
        <?php echo $this->Html->link('Cancel', array('controller' => 'orders', 'action' => 'reviewRating'), array('class' => 'btn btn-default')); ?>
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