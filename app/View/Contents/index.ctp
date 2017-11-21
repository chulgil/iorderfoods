<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<?php echo $this->Html->script('ckfinder/ckfinder'); ?>
<div class="row">
    <div class="col-lg-6">
        <h3>Add Page</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
        </div>
    </div>
</div>   
<div class="row">        
    <?php echo $this->Form->create('Content', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'PageAdd')); ?>
    <div class="col-lg-6">            


        <div class="form-group form_margin">		 
            <label>Name<span class="required"> * </span></label>               

            <?php
            echo $this->Form->input('StoreContent.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Name', 'label' => '', 'div' => false));
            echo $this->Form->error('StoreContent.name');
            ?>
        </div>
        <div class="form-group form_margin">		 
            <label>Content Key<span class="required"> * </span></label>               

            <?php
            echo $this->Form->input('StoreContent.content_key', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Content Key', 'label' => '', 'div' => false));
            echo $this->Form->error('StoreContent.content_key');
            ?>
        </div>

        <div class="form-group">
            <label>Page Position<span class="required"> * </span></label>                
            &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('StoreContent.page_position', array(
                'type' => 'radio',
                'options' => array('1' => 'Main menu&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '2' => 'Footer menu&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '3' => 'More Info'),
                'default' => 1
            ));
            echo $this->Form->error('StoreContent.page_position');
            ?>
        </div>

        <div class="form-group form_spacing">
            <label>Page Content</label> 
            <?php
            echo $this->Form->textarea('StoreContent.content', array('class' => 'ckeditor'));
            echo $this->Form->error('StoreContent.content');
            ?>
        </div>




        <div class="form-group form_margin">
            <label>Status<span class="required"> * </span></label>                
            &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('StoreContent.is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive'),
                'default' => 1
            ));
            echo $this->Form->error('StoreContent.is_active');
            ?>
        </div>



        <?php //if($seasonalpost){ $display="style='display:block;'";}else{$display="style='display:none;'";}  ?>




        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default', "id" => "saveContent")); ?>             
        <?php echo $this->Html->link('Cancel', "/contents/pageList/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {

        $("#PageAdd").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[StoreContent][name]": {
                    required: true,
                },
                "data[StoreContent][content_key]": {
                    required: true,
                },
            },
            messages: {
                "data[StoreContent][name]": {
                    required: "Please enter Page title",
                },
                "data[StoreContent][content_key]": {
                    required: "Please enter Page code",
                },
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
        $('#StoreContentName').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });

        $('#StoreContentContentKey').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });
    });
</script>
<script type="text/javascript">
    var url = '<?php echo HTTP_ROOT . 'js/'; ?>';
    //var url = 'http://192.168.0.5:8154/app/webroot/js/';
    var editor = CKEDITOR.replace('StoreContentContent', {
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
                $("#saveContent").trigger("click");
            }
        });
        // Replace the old save's exec function with the new one
        ev.editor.commands.save.exec = overridecmd.exec;
    });
</script>
