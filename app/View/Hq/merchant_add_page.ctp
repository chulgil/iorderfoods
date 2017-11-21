<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<div class="row">
    <div class="col-lg-6">
        <h3>Merchant Add Page</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
        </div>
    </div>
</div>   
<div class="row">        
    <?php echo $this->Form->create('MerchantContent', array('url' => array('controller' => 'hq', 'action' => 'merchantAddPage'))); ?>
    <div class="col-lg-6">            
        <div class="form-group form_margin">		 
            <label>Name<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('MerchantContent.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Name', 'label' => '', 'div' => false));
            echo $this->Form->error('MerchantContent.name');
            ?>
        </div>
        <div class="form-group form_margin">		 
            <label>Content Key<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('MerchantContent.content_key', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Content Key', 'label' => '', 'div' => false));
            echo $this->Form->error('MerchantContent.content_key');
            ?>
        </div>
        <div class="form-group">
            <label class="radioLabel">Page Position<span class="required"> * </span></label>                
            <?php
            echo $this->Form->input('MerchantContent.page_position', array(
                'type' => 'radio',
                'options' => array('1' => 'Main Menu', '2' => 'Footer Menu'),
                'default' => 1,
                'label' => false,
                'legend' => false,
                'div' => false
            ));
            echo $this->Form->error('MerchantContent.page_position');
            ?>
        </div>
        <div class="form-group form_spacing">
            <label>Page Content</label> 
            <?php
            echo $this->Form->textarea('MerchantContent.content', array('class' => 'ckeditor'));
            echo $this->Form->error('MerchantContent.content');
            ?>
        </div>
        <div class="form-group form_margin">
            <label class='radioLabel'>Status<span class="required"> * </span></label>                
            <?php
            echo $this->Form->input('MerchantContent.is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Active', '0' => 'In-Active'),
                'default' => 1,
                'label' => false,
                'legend' => false,
                'div' => false
            ));
            echo $this->Form->error('MerchantContent.is_active');
            ?>
        </div>

        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Html->link('Cancel', "/hq/merchantpageList/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
<script>
    $(document).ready(function () {
        $("#MerchantContentMerchantAddPageForm").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[MerchantContent][name]": {
                    required: true,
                },
                "data[MerchantContent][content_key]": {
                    required: true,
                },
            },
            messages: {
                "data[MerchantContent][name]": {
                    required: "Please enter page name",
                },
                "data[MerchantContent][content_key]": {
                    required: "Please enter content key",
                },
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
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
