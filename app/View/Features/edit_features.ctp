<?php //prx($this->params);                         ?>
<div class="row">
    <div class="col-lg-6">
        <h3>Edit Featured</h3> 
        <hr>
        <?php echo $this->Session->flash(); ?>   
    </div> 
</div>   
<div class="row">        
    <?php echo $this->Form->create('StoreFeaturedSection', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'FeatureEdit', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">            
        <div class="form-group form_margin">		 
            <label>Featured Name<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('StoreFeaturedSection.featured_name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter featured name', 'label' => '', 'div' => false));
            ?>           
        </div>
        <div class="form-group form_spacing clearfix">
            <div style="float:left;">
                <label>Upload Icon Image</label>
                <br>
                <span class="blue">Max image size 2MB (For best viewing upload images with resolution 60*60(W*H))</span>
                <?php
                echo $this->Form->input('StoreFeaturedSection.image', array('type' => 'file', 'div' => false));
                echo $this->Form->error('StoreFeaturedSection.image');
                ?>
            </div>
            <?php
            $EncryptFeaturedID = $this->Encryption->encode($this->request->data['StoreFeaturedSection']['id']);
            ?>
            <div style="float:right;">
                <?php
                if ($this->request->data['StoreFeaturedSection']['image']) {
                    echo $this->Html->image('/FeatureSection-IconImage/' . $this->request->data['StoreFeaturedSection']['image'], array('alt' => 'Store Featured Section Image', 'height' => 150, 'width' => 150, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;', 'title' => 'Item Image'));
                    echo $this->Html->link("X", array('controller' => 'features', 'action' => 'deleteFeaturedImage', $EncryptFeaturedID,'IconImage'), array('confirm' => 'Are you sure to delete Image?', 'title' => 'Delete Photo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
                }
                ?>
            </div>
        </div>
        <div class="form-group form_spacing clearfix">
            <div style="float:left;">
                <label>Upload Background Image</label>
                <br>
                <span class="blue">Max image size 2MB (For best viewing upload images with resolution 1360*580(W*H))</span>
                <?php
                echo $this->Form->input('StoreFeaturedSection.background_image', array('type' => 'file', 'div' => false));
                echo $this->Form->error('StoreFeaturedSection.background_image');
                ?>
            </div>
            <div style="float:right;">
                <?php
                if ($this->request->data['StoreFeaturedSection']['background_image']) {
                    echo $this->Html->image('/FeatureSection-BgImage/' . $this->request->data['StoreFeaturedSection']['background_image'], array('alt' => 'Store Featured Section Image', 'height' => 150, 'width' => 150, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;', 'title' => 'Item Image'));
                    echo $this->Html->link("X", array('controller' => 'features', 'action' => 'deleteFeaturedImage', $EncryptFeaturedID,'BgImage'), array('confirm' => 'Are you sure to delete Image?', 'title' => 'Delete Photo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
                }
                ?>
            </div>
        </div>
        <div class="form-group form_margin">
            <label>Status<span class="required"> * </span></label>                
            &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('StoreFeaturedSection.is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive'),
                'default' => 1
            ));
            ?>
        </div>
    <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
    <?php echo $this->Html->link('Cancel', "/features/index", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
<?php echo $this->Form->end(); ?>
</div><!-- /.row -->
<hr>
<div class="row">
    <div class="col-lg-12 clearfix">
        <div class="row">
            <div class="col-xs-6">
                <h3>Featured Item Listing</h3>
            </div>
            <div class="col-xs-6">
<?php echo $this->Form->button('Add New Item', array('type' => 'button', 'data-id' => $this->params->pass[0], 'class' => 'btn btn-default pull-right addNewItem')); ?>             
            </div>
        </div>
        <hr>
        <?php echo $this->Session->flash(); ?>
        <?php
        if (!empty($list)) {
            echo $this->element('show_pagination_count');
        }
        ?>
        <div class="table-responsive">   
            <table class="table table-bordered table-hover table-striped tablesorter" id="featuredListing">
                <thead>
                    <tr>	    
                        <th  class="th_checkbox">Item Name</th>
                        <th  class="th_checkbox">Category</th>
                        <th  class="th_checkbox">Position</th>
                        <th  class="th_checkbox">Action</th>

                </thead>
                <tbody class="dyntable" id="sortable">
                    <?php
                    if (!empty($list)) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptFeaturedItemID = $this->Encryption->encode($data['FeaturedItem']['id']);
                            ?>
                            <tr <?php echo $class; ?> notif-id="<?php echo $EncryptFeaturedItemID; ?>">	    
                                <td><?php echo $data['Item']['name']; ?></td>
                                <td><?php echo $data['Item']['Category']['name']; ?></td>
                                <td class='sort_order'>		
                                    <?php
                                    echo $this->Html->image('uparrow.png', array('alt' => "Up", 'title' => "Up", 'class' => 'up_order', 'id' => 'upOrder'));
                                    echo $this->Html->image('downarrow.png', array('alt' => "Down", 'title' => "Down", 'class' => 'down_order', 'id' => 'downOrder'));
                                    ?>
                                </td> 
                                <td>
                            <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'features', 'action' => 'deleteFeaturedItem', $EncryptFeaturedItemID), array('confirm' => 'Are you sure to delete Item?', 'escape' => false)); ?>         
                                </td>
                            </tr>
                            <?php
                            $i++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">
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
            <div class="row padding_btm_20" style="padding-top:10px">
                <div class="col-lg-1">   
                    LEGENDS:                        
                </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/delete.png") . " Delete"; ?> </div>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="FeaturedItemModal" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Featured Item</h4>
            </div>
            <span id="flashMessage"></span>
            <div class="modal-body">
<?php echo $this->Form->create('StoreFeaturedSection', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'))); ?>
                <div class="row">
                    <div class="col-xs-12" id="slide">

                    </div>
                    <br>
                    <br>
                    <div class="itemList col-xs-12">
                    </div>
                    <div class="col-xs-12">
                <?php echo $this->Form->button('Save', array('type' => 'button', 'class' => 'btn btn-success saveFetItem hidden')); ?>
                    </div>
                </div>
<?php echo $this->Form->end(); ?>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript">
    var notifLen = $('table#featuredListing').find('tr').length;
    $(document).ready(function () {
        $('#FeaturedItemModal').on('hidden.bs.modal', function () {
            location.reload();
        })
        $('.addNewItem').click(function () {
            var featuredSectionId = $(this).data('id');
            $.ajax({
                type: 'post',
                url: '/features/addFeaturedItem',
                data: {'featuredSectionId': featuredSectionId},
                async: false,
                success: function (result) {
                    $("#slide").html(result);
                    $('#FeaturedItemModal').modal('show');
                }
            });
        });
        $(document).on('change', '#CategoryCategoryId', function () {
            var categoryId = $(this).val();
            var featuredSectionId = $('.addNewItem').data('id');
            $.ajax({
                type: 'post',
                url: '/features/getItemByCategoryId',
                data: {'categoryId': categoryId, 'featuredSectionId': featuredSectionId},
                async: false,
                success: function (result) {
                    $(".itemList").html(result);
                    $(".saveFetItem").removeClass('hidden');
                }
            });
        });
        $(document).on('click', '.saveFetItem', function () {
            var formData = $("#StoreFeaturedSectionEditFeaturesForm").serialize();
            console.log(formData);
            if (formData) {
                $.ajax({
                    url: "<?php echo $this->Html->url(array('controller' => 'features', 'action' => 'ajaxfeatureUpdate')); ?>",
                    type: 'post',
                    data: {formData: formData},
                    success: function (response) {
                        if (response != '') {
                            var result = $.parseJSON(response);
                            if (result.status == 'Success') {
                                $("#flashMessage").html('<div class="alert-success">' + result.msg + '</div');
                                ;
                            } else if (result.status == 'Error') {
                                $("#flashMessage").html('<div class="alert-danger">' + result.msg + '</div');
                            }
                            setTimeout(function () {
                                $('#flashMessage').html('');
                            }, 6000);
                        }
                    }
                });
            }
        });

        // Hide up arrow from first row 
        $('table#featuredListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
        // Hide down arrow from last row 
        $('table#featuredListing').find('tr').eq(notifLen - 1).find('td.sort_order').find('img.down_order').hide();

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
    });

    function updateOrder() {
        $('img.up_order').show();
        $('img.down_order').show();

        $('table#featuredListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
        $('table#featuredListing').find('tr').eq(notifLen - 1).find('td.sort_order').find('img.down_order').hide();
        var orderData = getNotifOrderKeyVal();

        if (orderData) {
            $.ajax({
                url: '/features/updateFeatureItemPosition?' + orderData,
                type: 'get',
                success: function () {
                }
            });
        }
    }
    function getNotifOrderKeyVal() {
        if ($('table#featuredListing tbody').eq(0).find('tr').length > 0) {
            var orderData = '';
            $('table#featuredListing tbody').eq(0).find('tr').each(function (i) {
                var notifId = $(this).attr('notif-id');
                orderData += notifId + '=' + (i + 1) + '&';
            });
            return orderData;
        }
        return false;
    }
</script>
<script>
    $(document).ready(function () {
        $("#FeatureEdit").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[StoreFeaturedSection][featured_name]": {
                    required: true,
                }
            },
            messages: {
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
        //$("#StoreFeaturedSectionEditFeaturesForm")
    });
</script>