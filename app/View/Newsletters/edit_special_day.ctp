<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<?php echo $this->Html->script('ckfinder/ckfinder'); ?>

<div class="row">
    <div class="col-lg-6">

        <h3><?php echo $this->request->data['DefaultSpecialDay']['name']; ?> Newsletter</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
        </div>
    </div>
</div>   
<hr>
<div class="row">        
    <?php echo $this->Form->create('SpecialDay', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'NewsletterAdd')); ?>
    <div class="col-lg-6">            
        <div class="form-group">		 
            <label>Subject<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('DefaultSpecialDay.name', array('type' => 'text', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'readonly' => 'readonly'));
            echo $this->Form->error('DefaultSpecialDay.name');
            echo $this->Form->input('SpecialDay.id', array('type' => 'hidden'));
            ?>
        </div>
        <br>
        <div class="form-group">
            <label>Body</label> 
            <?php
            echo $this->Form->textarea('SpecialDay.template_message', array('class' => 'ckeditor'));
            echo $this->Form->error('SpecialDay.template_message');
            ?>
        </div>  


        <div class="form-group">
            <label>Status<span class="required"> * </span></label>                
            &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('SpecialDay.is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive'),
                'default' => 1
            ));
            echo $this->Form->error('Newsletter.is_active');
            ?>
        </div>
        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Html->link('Cancel', "/newsletters/special_day", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->
<script>
    $(document).ready(function () {
        $("#NewsletterAdd").validate({
            rules: {
                "data[DefaultSpecialDay][name]": {
                    required: true,
                },
                "data[SpecialDay][template_message]": {
                    required: true,
                },
            },
            messages: {
                "data[DefaultSpecialDay][name]": {
                    required: "Please enter subject.",
                },
                "data[SpecialDay][template_message]": {
                    required: "Please enter newsletter code.",
                },
            }
        });
    });
    var url = '<?php echo HTTP_ROOT . 'js/'; ?>';
    CKEDITOR.env.isCompatible = true;
    var editor = CKEDITOR.replace('SpecialDayTemplateMessage', {
        filebrowserBrowseUrl: url + 'ckfinder/ckfinder.html',
        filebrowserImageBrowseUrl: url + 'ckfinder/ckfinder.html?type=Images',
        filebrowserFlashBrowseUrl: url + 'ckfinder/ckfinder.html?type=Flash',
        filebrowserUploadUrl: url + 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
        filebrowserImageUploadUrl: url + 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
        filebrowserFlashUploadUrl: url + 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
    });
</script>