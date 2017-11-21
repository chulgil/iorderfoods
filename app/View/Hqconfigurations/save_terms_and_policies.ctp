<?php echo $this->Html->script('ckeditor/ckeditor');?>
<div class="row">
    <div class="col-lg-6">
        <h3>Add Terms & Conditions and Privacy Policies</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
</div>   
<hr>
<div class="row">
    <div class="col-xs-6">
        <?php echo $this->Form->create('TermsAndPolicy', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'enctype' => 'multipart/form-data')); ?>
        <div class="col-xs-12">
            <div class="form-group">
                <label>Terms & Conditions<span class="required"> * </span></label>
                <?php
                echo $this->Form->input('terms_and_conditions', array('type' => 'textarea', 'class' => 'form-control ckeditor', 'label' => false, 'div' => false));
                echo $this->Form->input('id', array('type' => 'hidden'));
                ?>
            </div>
            <div class="form-group">
                <label>Privacy Policies<span class="required"> * </span></label>
                <?php
                echo $this->Form->input('privacy_policy', array('type' => 'textarea', 'class' => 'form-control ckeditor', 'label' => false, 'div' => false));
                ?>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <?php
                echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default'));?>&nbsp;
                <?php echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/hq/merchantPageList'", 'class' => 'btn btn-default'));
                ?>             
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div><!-- /.row -->
<script type="text/javascript">
    $("#MasterContentContentModuleForm").validate({
        rules: {
            "data[MasterContent][name]": {
                required: true,
            }
        },
        messages: {
            "data[MasterContent][name]": {
                required: "Please enter name.",
            }
        }
    });
</script>