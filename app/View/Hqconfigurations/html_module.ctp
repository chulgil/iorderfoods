<style type="text/css">
    .text-center{
        border: 1px solid #000;
        padding: 12px;
    }
    .html-module .btn-default.pull-right { margin-left:10px;}
</style>
<div class="row html-module">
    <div class="col-lg-6">
        <h3 class="pull-left"><?php echo $pageDetail['MerchantContent']['name']; ?></h3>
        &nbsp;
        <?php //echo $this->Form->button('<i class="fa fa-edit"></i>', array("data-toggle" => "modal", "data-target" => "#editPageNameModal", 'escape' => false, 'title' => "Edit Page")); ?>
    </div> 
    <div class="col-lg-6">
        <?php
        $staticMenu = array('LOCATIONS', 'GALLERY', 'NEWSLETTER', 'PROMOTIONS');
        if (!in_array($pageDetail['MerchantContent']['name'], $staticMenu)) {
            ?>
            <?php //echo $this->Html->link('Add New Content Module', array('controller' => 'hqconfigurations', 'action' => 'contentModule', @$this->params['pass'][0]), array('class' => "btn btn-default pull-right", 'escape' => false)); ?>
            <?php echo $this->Form->button('Add New Content Module', array('class' => "btn btn-default pull-right addContent", 'escape' => false, 'data-merchant-content-id' => @$this->params['pass'][0], 'data-master-content-id' => '')); ?>
        <?php } ?>
        <?php echo $this->Html->link('Back', array('controller' => 'hq', 'action' => 'merchantPageList'), array('class' => "btn btn-default pull-right", 'escape' => false)); ?>
    </div>

</div>   
<div class="row">
    <div class="col-lg-6">
        <?php echo $this->Session->flash(); ?>   
    </div>
</div>
<hr>
<div class="row html-module">
    <div class="col-xs-5">
        <?php echo $this->Form->create('MerchantContent', array('url' => array('controller' => 'hqconfigurations', 'action' => 'merchantEditPage'))); ?>
        <div class="col-xs-12">
            <div class="form-group">
                <label>Name<span class="required"> * </span></label>               
                <?php
                echo $this->Form->input('MerchantContent.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false, 'value' => (@$pageDetail['MerchantContent']['content_key']) ? @$pageDetail['MerchantContent']['content_key'] : @$pageDetail['MerchantContent']['name']));
                echo $this->Form->error('MerchantContent.name');
                echo $this->Form->input('MerchantContent.id', array('type' => 'hidden', 'value' => @$pageDetail['MerchantContent']['id']));
                ?>
            </div>
            <?php array_push($staticMenu,"HOME");
            if (!in_array($pageDetail['MerchantContent']['name'], $staticMenu)) { ?>
                <div class="form-group">
                    <label class='radioLabel'>Page Navigation<span class="required"> * </span></label>
                    <?php
                    echo $this->Form->input('MerchantContent.page_position', array(
                        'type' => 'radio',
                        'options' => array('1' => 'Main Menu', '2' => 'Footer Menu', '3' => 'More Info'),
                        'default' => 1,
                        'label' => false,
                        'legend' => false,
                        'div' => false,
                        'disabled' => false
                    ));
                    echo $this->Form->error('MerchantContent.page_position');
                    ?>
                </div>
                <?php
            } else {
                echo $this->Form->input('MerchantContent.page_type', array('type' => 'hidden', 'value' => 'reserved'));
            }
            ?>

            <div class="form-group">
                <label class='radioLabel'>Status<span class="required"> * </span></label>                
                <?php
                echo $this->Form->input('MerchantContent.is_active', array(
                    'type' => 'radio',
                    'options' => array('1' => 'Active', '0' => 'In-Active'),
                    'default' => @$pageDetail['MerchantContent']['is_active'],
                    'label' => false,
                    'legend' => false,
                    'div' => false
                ));
                echo $this->Form->error('MerchantContent.is_active');
                ?>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
    <div class="col-xs-7">
        <?php
        if (!empty($mContent)) {
            foreach ($mContent as $data) {
                ?>
                <div class="col-xs-12">
                    <div class="col-xs-2"></div>
                    <a href="/hqconfigurations/htmlLayout?content=<?php echo $data['MasterContent']['id']; ?>" class="col-xs-6 text-center">
                        <?php echo $data['MasterContent']['name']; ?>
                    </a>
                    <div class="col-xs-4">
                        <?php //echo $this->Html->link('Edit', array('controller' => 'hqconfigurations', 'action' => 'contentModule?content=' . $data['MasterContent']['id']), array('class' => "btn btn-default pull-right", 'escape' => false)); ?>
                        <?php
                        echo $this->Html->link('<i class="fa fa-trash-o"></i>', array('controller' => 'hqconfigurations', 'action' => 'deleteMasterContent', @$this->params['pass'][0], $data['MasterContent']['id']), array('class' => "btn btn-default pull-right", 'confirm' => 'Are you sure to delete content?', 'escape' => false, 'title' => 'Delete Content'));
                        ?>     
                        <?php echo $this->Form->button('<i class="fa fa-edit"></i>', array('class' => "btn btn-default pull-right editContent", 'escape' => false, 'data-merchant-content-id' => @$this->params['pass'][0], 'data-master-content-id' => $data['MasterContent']['id'], 'title' => 'Edit Content')); ?>
                    </div>
                </div>
                <div class="col-xs-12"></div>
                <?php
            }
        }
        ?>
    </div>
</div><!-- /.row -->
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id='modal-title'></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div id="flashError"></div>
                    <div class="col-xs-6">
                        <?php echo $this->Form->create('MasterContent', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'enctype' => 'multipart/form-data')); ?>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>Name<span class="required"> * </span></label>
                                <?php
                                echo $this->Form->input('name', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'required' => true));
                                echo $this->Form->input('id', array('type' => 'hidden'));
                                echo $this->Form->input('merchant_content_id', array('type' => 'hidden'));
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default saveContent')); ?>             
                            </div>
                        </div>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<!--<div id="editPageNameModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
         Modal content
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id='modal-title'>Edit Page Name</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div id="flashErrorEditName"></div>
                    <div class="col-xs-10">
<?php echo $this->Form->create('MerchantContent', array('url' => array('controller' => 'hq', 'action' => 'merchantEditPage'))); ?>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label>Name<span class="required"> * </span></label>               
<?php
echo $this->Form->input('MerchantContent.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false, 'value' => @$pageDetail['MerchantContent']['name']));
echo $this->Form->error('MerchantContent.name');
echo $this->Form->input('MerchantContent.id', array('type' => 'hidden', 'value' => @$pageDetail['MerchantContent']['id']));
?>
                            </div>
<?php if (($pageDetail['MerchantContent']['name'] != 'Home') && ($pageDetail['MerchantContent']['name'] != 'LOCATIONS') && ($pageDetail['MerchantContent']['name'] != 'GALLERY') && ($pageDetail['MerchantContent']['name'] != 'NEWSLETTER') && ($pageDetail['MerchantContent']['name'] != 'PROMOTIONS')) { ?>
                                            <div class="form-group">
                                                <label class='radioLabel'>Page Navigation<span class="required"> * </span></label>
    <?php
    echo $this->Form->input('MerchantContent.page_position', array(
        'type' => 'radio',
        'options' => array('1' => 'Main Menu', '2' => 'Footer Menu', '3' => 'More Info'),
        'default' => 1,
        'label' => false,
        'legend' => false,
        'div' => false
    ));
    echo $this->Form->error('MerchantContent.page_position');
    ?>
                                            </div>
<?php } ?>

                            <div class="form-group">
                                <label class='radioLabel'>Status<span class="required"> * </span></label>                
<?php
echo $this->Form->input('MerchantContent.is_active', array(
    'type' => 'radio',
    'options' => array('1' => 'Active', '0' => 'In-Active'),
    'default' => @$pageDetail['MerchantContent']['is_active'],
    'label' => false,
    'legend' => false,
    'div' => false
));
echo $this->Form->error('MerchantContent.is_active');
?>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
<?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default savePageName')); ?>             
                            </div>
                        </div>
<?php echo $this->Form->end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>-->
<script type="text/javascript">
    $("#MerchantContentHtmlModuleForm").validate({
        rules: {
            "data[MerchantContent][name]": {
                required: true,
            }
        },
        messages: {
            "data[MerchantContent][name]": {
                required: "Please enter page name",
            }
        }
    });
    $("#MasterContentHtmlModuleForm").validate({
        rules: {
            "data[MasterContent][name]": {
                required: true,
                remote: {
                    url: "/hqconfigurations/checkUniqueName",
                    type: "post",
                    data: {
                        MasterContentId: function () {
                            return $("#MasterContentId").val();
                        }
                    }
                }

            }
        },
        messages: {
            "data[MasterContent][name]": {
                required: "Please enter name.",
                remote: "Name already exists",
            }
        }
    });
    $(document).on('click', '.addContent,.editContent', function () {
        var merchantContentId = $(this).data('merchant-content-id');
        var masterContentId = $(this).data('master-content-id');
        if (masterContentId) {
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'hqconfigurations', 'action' => 'openContentModal')); ?>",
                type: "Post",
                dataType: 'html',
                data: {merchantContentId: merchantContentId, masterContentId: masterContentId},
                success: function (result) {
                    try {
                        data = JSON.parse(result);
                        $("#MasterContentName").val(data.name);
                        $("#MasterContentId").val(masterContentId);
                        $("#MasterContentMerchantContentId").val(merchantContentId);
                        $("#modal-title").html('Edit Content Module');
                        $("#flashError").html('');
                        $("#myModal").modal('show');
                    } catch (e) {
                        alert('Try aftersome time.');
                    }
                }
            });
        } else {
            $("#MasterContentMerchantContentId").val(merchantContentId);
            $("#MasterContentName").val('');
            $("#modal-title").html('Add Content Module');
            $("#myModal").modal('show');
        }
    }); //var formData = $("#eGuestUserDetail").serialize();
    $(document).on('click', '.saveContent', function (e) {
        var formData = $("#MasterContentHtmlModuleForm").serialize();
        if (formData && $("#MasterContentHtmlModuleForm").valid()) {
            $.ajax({
                url: "<?php echo $this->Html->url(array('controller' => 'hqconfigurations', 'action' => 'saveContentModal')); ?>",
                type: "Post",
                dataType: 'html',
                data: {formData: formData},
                success: function (result) {
                    data = JSON.parse(result);
                    if (data.status == 'Error') {
                        $("#flashError").html('<div class="message message-success alert alert-danger" id="flashMessage"><a href="#" data-dismiss="alert" aria-label="close" title="close">×</a> ' + data.msg + '</div>');
                    } else {
                        $("#flashError").html('<div class="message message-success alert alert-success" id="flashMessage"><a href="#" data-dismiss="alert" aria-label="close" title="close">×</a> ' + data.msg + '</div>');
                        setTimeout(function () {
                            $("#myModal").modal('hide');
                            location.reload();
                        }, 1000);
                    }
                }
            });
        }
        e.preventDefault();
    });
//    $(document).on('click', '.savePageName', function (e) {
//        if ($("#MerchantContentHtmlModuleForm").valid()) {
//            var formData = $("#MerchantContentHtmlModuleForm").serialize();
//            $.ajax({
//                type: 'POST',
//                url: '/hqconfigurations/merchantEditPage',
//                data: {'formData': formData},
//                success: function (response) {
//                    console.log(response);
//                    if (response) {
//                        var obj = jQuery.parseJSON(response);
//                        if (obj.status == 'Error') {
//                            $('#flashErrorEditName').html('<div class="message message-success alert alert-danger" id="flashMessage"><a href="#" data-dismiss="alert" aria-label="close" title="close" class="pull-right">×</a> ' + obj.msg + '</div>');
//                            return false;
//                        }
//                        if (obj.status == 'Success') {
//                            $('#flashErrorEditName').html('<div class="message message-success alert alert-success" id="flashMessage"><a href="#" data-dismiss="alert" aria-label="close" title="close" class="pull-right">×</a> ' + obj.msg + '</div>');
//                            setTimeout(function () {
//                                $("#editPageNameModal").modal('hide');
//                                location.reload();
//                            }, 1000);
//                        }
//                    }
//                }
//            });
//        }
//        e.preventDefault();
//    });
</script>