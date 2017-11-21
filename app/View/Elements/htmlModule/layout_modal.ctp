<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Layout <?php echo $this->request->data['HomeContent']['content_layout_id']; ?></h4>
</div>
<div class="modal-body">
<!--    <span id="flashMessage">
        <div id="flashMessage" class="message message-danger alert alert-danger"> 
            <a title="close" aria-label="close" data-dismiss="alert" class="close" href="#">×</a>
    <?php echo 'this is test'; ?>
        </div>
    </span>-->
    <?php //if (empty($homeContentStatus)) { ?>
    <?php echo $this->Html->script('ckeditor/ckeditor'); ?>
    <?php echo $this->Html->script('ckfinder/ckfinder'); ?>
    <?php echo $this->Form->create('HomeContent', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'))); ?>
    <div class="form-group">
        <?php echo $this->Form->textarea('content', array('type' => 'textarea', 'class' => 'form-control', 'placeholder' => 'Content', 'required' => "required")); ?>
        <?php echo $this->Form->input('id', array('type' => 'hidden')); ?>
        <?php echo $this->Form->input('master_content_id', array('type' => 'hidden')); ?>
        <?php echo $this->Form->input('merchant_content_id', array('type' => 'hidden')); ?>
        <?php echo $this->Form->input('content_layout_id', array('type' => 'hidden')); ?>
        <?php echo $this->Form->input('layout_box_id', array('type' => 'hidden')); ?>
    </div>
    <?php echo $this->Form->input('SUBMIT', array('type' => 'button', 'class' => 'btn btn-default', "id" => "saveContent")); ?>
    <?php echo $this->Form->end(); ?>
    <?php
//    } else {
//        echo 'Please final submit Layout ' . $homeContentStatus['HomeContent']['content_layout_id'];
//    }
    ?>
</div>
<script type="text/javascript">
    var url = '<?php echo HTTP_ROOT . 'js/'; ?>';
    //var url = 'http://192.168.0.5:8154/app/webroot/js/';
    var editor = CKEDITOR.replace('HomeContentContent', {
        filebrowserBrowseUrl: url + 'ckfinder/ckfinder.html',
        filebrowserImageBrowseUrl: url + 'ckfinder/ckfinder.html?type=Images',
        filebrowserFlashBrowseUrl: url + 'ckfinder/ckfinder.html?type=Flash',
        filebrowserUploadUrl: url + 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
        filebrowserImageUploadUrl: url + 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
        filebrowserFlashUploadUrl: url + 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
    });
    //CKFinder.setupCKEditor(editor, '../');
    CKEDITOR.on('instanceReady', function (ev) {

        // Create a new command with the desired exec function
        var overridecmd = new CKEDITOR.command(editor, {
            exec: function (editor) {
                // Replace this with your desired save button code
                $( "#saveContent" ).trigger( "click" );
            }
        });

        // Replace the old save's exec function with the new one
        ev.editor.commands.save.exec = overridecmd.exec;
    });

</script>
