<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<div class="row">
    <div class="col-lg-6">
        <h3>Add Modal Popup Detail</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
        </div>
    </div>
</div>   
<hr>
<div class="row">        
    <?php
    echo $this->Form->create('HomeModal', array('url' => array('controller' => 'hq', 'action' => 'homePageModal')));
    echo $this->Form->input('id', array('type' => 'hidden'));
    ?>
    <div class="col-lg-6">            
        <div class="form-group">
            <label class="radioLabel">Modal Type<span class="required"> * </span></label>   
            <?php
            echo $this->Form->input('modal_box_size_type', array(
                'type' => 'radio',
                'options' => array('1' => '550x480', '2' => '720x300'),
                'default' => 1,
                'label' => false,
                'legend' => false,
                'div' => false,
            ));
            ?>
            <?php echo $this->Form->error('modal_box_size_type');
            ?>
        </div>
        <div class="form-group form_spacing">
            <label>Page Content<span class="required"> * </span></label> 
            <?php
            echo $this->Form->textarea('modal_text', array('class' => 'ckeditor', /* 'rows' => 20, 'cols' => 55 */));
            echo $this->Form->error('modal_text');
            ?>
        </div>
        <div class="form-group form_margin">
            <label class="radioLabel">Status<span class="required"> * </span></label>                
            <?php
            echo $this->Form->input('is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Active', '0' => 'In-Active'),
                'default' => 1,
                'label' => false,
                'legend' => false,
                'div' => false
            ));
            echo $this->Form->error('is_active');
            ?>
        </div>


        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Html->link('Cancel', "/hq/dashboard", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {
        $("#HomeModalHomePageModalForm").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[HomeModal][modal_box_size_type]": {
                    required: true,
                },
                "data[HomeModal][modal_text]": {
                    required: true,
                },
                "data[HomeModal][is_active]": {
                    required: true,
                },
            },
            messages: {
                "data[StoreContent][modal_box_size_type]": {
                    required: "Please select modal type.",
                },
                "data[StoreContent][modal_text]": {
                    required: "Please enter modal text.",
                },
                "data[StoreContent][is_active]": {
                    required: "Please select status.",
                },
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
        CKEDITOR.config.toolbar = [
            ['Styles', 'Format', 'Font', 'FontSize', 'Bold', 'Italic', 'Underline', 'StrikeThrough', '-', 'Undo', 'Redo', '-', 'Cut', 'Copy', 'Paste', '-', 'Outdent', 'Indent', '-', 'NumberedList', 'BulletedList'],
            '/',
            ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
            ['Link', 'Smiley', 'TextColor', 'BGColor']
        ];
    });
</script>
<style>
    input[type="radio"] {
        line-height: normal;
        margin: 4px 10px;
    }
    .radioLabel{
        margin-right: 45px;
    }
</style>