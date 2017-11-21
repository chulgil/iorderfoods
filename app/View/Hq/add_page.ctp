<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<div class="row">
    <div class="col-lg-6">
        <h3>Add Page</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
        </div>
    </div>
</div>   
<div class="row">        
    <?php echo $this->Form->create('StoreContent', array('url' => array('controller' => 'hq', 'action' => 'addPage'))); ?>
    <div class="col-lg-6">            
        <div class="form-group form_margin">		 
            <label>Store<span class="required"> * </span></label>               

            <?php
            $merchantList = $this->Hq->getHQStores($merchantId);
            echo $this->Form->input('StoreContent.store_id', array('options' => $merchantList, 'class' => 'form-control', 'label' => '', 'div' => false, 'empty' => 'Please Select Store'));
            ?>
        </div>    

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
            <label class="radioLabel">Page Position<span class="required"> * </span></label>   
            <?php
            echo $this->Form->input('StoreContent.page_position', array(
                'type' => 'radio',
                'options' => array('1' => 'Main Menu', '2' => 'Footer Menu'),
                'default' => 1,
                'label' => false,
                'legend' => false,
                'div' => false,
            ));
            ?>
            <?php echo $this->Form->error('StoreContent.page_position');
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
            <label class="radioLabel">Status<span class="required"> * </span></label>                
            <?php
            echo $this->Form->input('StoreContent.is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Active', '0' => 'In-Active'),
                'default' => 1,
                'label' => false,
                'legend' => false,
                'div' => false
            ));
            echo $this->Form->error('StoreContent.is_active');
            ?>
        </div>


        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Html->link('Cancel', "/hq/pageList/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {
        $("#StoreContentAddPageForm").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[StoreContent][store_id]": {
                    required: true,
                },
                "data[StoreContent][name]": {
                    required: true,
                },
                "data[StoreContent][content_key]": {
                    required: true,
                },
            },
            messages: {
                "data[StoreContent][store_id]": {
                    required: "Please select store.",
                },
                "data[StoreContent][name]": {
                    required: "Please enter page name",
                },
                "data[StoreContent][content_key]": {
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