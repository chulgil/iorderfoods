<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<div class="row">
    <div class="col-lg-6">
        <h3>Manage Slider Images</h3> 
        <hr>
        <?php echo $this->Session->flash(); ?>   
    </div> 
</div>
<div class="row">             
    <?php
    echo $this->Form->create('MerchantGallery', array('url' => array('controller' => 'hq', 'action' => 'merchantManageSliderPhotos'), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'ImageUpload', 'enctype' => 'multipart/form-data'));
    ?>
    <div class="col-lg-6">
        <div class="form-group form_spacing">
            <?php
            echo $this->Form->input('MerchantGallery.merchant_id', array('type' => 'hidden', 'value' => $merchantId));
            ?>
            <label>Upload Image<span class="required"> * </span></label>    
            <?php
            echo $this->Form->input('MerchantGallery.image', array('type' => 'file', 'label' => '', 'div' => false, 'class' => 'form-control', 'style' => "box-sizing:initial;"));
            echo $this->Form->error('StoreGallery.image');
            ?>
            <span class="blue">Max Upload Size 2MB (For best viewing upload image resolution 1920x600)</span> 
        </div>
        <div class="form-group">
            <label>Description</label> 
            <?php
            echo $this->Form->input('MerchantGallery.description', array('type' => 'textarea', 'rows' => '6', 'cols' => '6', 'label' => '', 'class' => 'form-control ckeditor'));
            ?>
        </div>
        <div class="form-group form-spacing">
            <?php
            echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default'));
            echo "&nbsp;";
            echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/hq/dashboard'", 'class' => 'btn btn-default'));
            echo $this->Form->end();
            ?>

        </div>	  
    </div>
    <div style="clear:both;"></div>
</div>
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
                <table class="table table-bordered table-hover table-striped tablesorter" id="imageListing">
                    <thead>
                        <tr>
                            <th  class="th_checkbox">Image</th>
                            <th  class="th_checkbox">Description</th>
                            <th  class="th_checkbox">Status</th>
                            <th  class="th_checkbox" width="100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody class="dyntable">
                        <?php
                        if (!empty($sliderImages)) {
                            foreach ($sliderImages as $key => $data) {
                                $EncryptGallaryImageID = $this->Encryption->encode($data['MerchantGallery']['id']);
                                ?>
                                <tr  class="text-center" notif-id="<?php echo $EncryptGallaryImageID; ?>">
                                    <td>
                                        <?php echo $this->Html->image('/merchantSliderImages/thumb/' . $data['MerchantGallery']['image'], array('width' => 100, "class" => "openImage")); ?>
                                    </td>
                                    <td><?php
                                        if (!empty($data['MerchantGallery']['description'])) {
                                            echo strip_tags($data['MerchantGallery']['description']);
                                        }
                                        ?></td>
                                    <td>
                                        <?php
                                        if ($data['MerchantGallery']['is_active']) {
                                            echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hq', 'action' => 'activateSliderImage', $EncryptGallaryImageID, 0), array('confirm' => 'Are you sure to Deactivate Image?', 'escape' => false));
                                        } else {
                                            echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hq', 'action' => 'activateSliderImage', $EncryptGallaryImageID, 1), array('confirm' => 'Are you sure to Activate Image?', 'escape' => false));
                                        }
                                        ?>
                                    </td>
                                    <td class='sort_order'>
                                        <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hq', 'action' => 'editSliderImage', $EncryptGallaryImageID), array('escape' => false)); ?>
                                        <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'hq', 'action' => 'merchantDeleteSliderPhoto', $EncryptGallaryImageID), array('confirm' => 'Are you sure to delete Image?', 'escape' => false)); ?>
                                        <?php
                                        echo $this->Html->image('uparrow.png', array('alt' => "Up", 'title' => "Up", 'class' => 'up_order', 'id' => 'upOrder'));
                                        echo $this->Html->image('downarrow.png', array('alt' => "Down", 'title' => "Down", 'class' => 'down_order', 'id' => 'downOrder'));
                                        ?>
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
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="storeReviewImageModal" role="dialog">
    <div class="modal-dialog modal-lg">

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
        $("#ImageUpload").validate({
            rules: {
                "data[MerchantGallery][image]": {
                    required: true,
                },
            },
            messages: {
                "data[MerchantGallery][image]": {
                    required: "Please Select image to upload",
                },
            }
        });
        $(".openImage").click(function () {
            var src = $(this).attr('src');
            $(".addSrc").attr("src", src);
            $('#storeReviewImageModal').modal('show');
        });
        $('#storeReviewImageModal').on('hidden.bs.modal', function () {
            $(".addSrc").attr("src", "");
        });
    });
    var notifLen = $('table#imageListing').find('tr').length;
    $(document).ready(function () {
        // Hide up arrow from first row 
        $('table#imageListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
        // Hide down arrow from last row 
        $('table#imageListing').find('tr').eq(notifLen - 1).find('td.sort_order').find('img.down_order').hide();

        var $up = $(".up_order")
        $up.click(function () {
            var $tr = $(this).parents("tr");
            if ($tr.index() != 0) {
                $tr.fadeOut().fadeIn();
                $tr.prev().before($tr);

            }
            updateOrder();
        });

        //down
        var $down = $(".down_order");
        var len = $down.length;
        $down.click(function () {
            var $tr = $(this).parents("tr");
            if ($tr.index() <= len) {

                $tr.fadeOut().fadeIn();
                $tr.next().after($tr);
            }
            updateOrder();
        });
        CKEDITOR.config.toolbar = 'Custom';
    });

    function updateOrder() {
        $('img.up_order').show();
        $('img.down_order').show();

        $('table#imageListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
        $('table#imageListing').find('tr').eq(notifLen - 1).find('td.sort_order').find('img.down_order').hide();
        var orderData = getNotifOrderKeyVal();
        if (orderData) {
            $.ajax({
                url: '/hq/updateHqImageOrder?' + orderData,
                type: 'get',
                success: function () {
                }
            });
        }
    }

    function getNotifOrderKeyVal() {
        if ($('table#imageListing tbody').eq(0).find('tr').length > 0) {
            var orderData = '';
            $('table#imageListing tbody').eq(0).find('tr').each(function (i) {
                var notifId = $(this).attr('notif-id');
                orderData += notifId + '=' + (i + 1) + '&';
            });
            return orderData;
        }
        return false;
    }
</script>
<!--Mid Section Ends