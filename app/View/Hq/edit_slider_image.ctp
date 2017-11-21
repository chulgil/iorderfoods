<?php echo $this->Html->script('ckeditor/ckeditor');?>
<div class="row">
    <div class="col-lg-6">
        <h3>Edit Slider Images</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
</div>  
<hr>
<div class="row">             
    <?php
    echo $this->Form->create('MerchantGallery', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'ImageUpload', 'enctype' => 'multipart/form-data'));
    ?>
    <div class="col-lg-6">
        <div class="form-group form_spacing">
            <label>Upload Image<span class="required"> * </span></label>    
            <?php
            echo $this->Form->input('MerchantGallery.image', array('type' => 'file', 'label' => '', 'div' => false, 'class' => 'form-control', 'style' => "box-sizing:initial;"));
            echo $this->Form->error('MerchantGallery.image');
            ?>
            <span class="blue">Max Upload Size 2MB (For best viewing upload image resolution 1920x600)</span> 
        </div>
        <div class="deleteImage">
            <?php
            if (!empty($this->request->data['MerchantGallery']['image'])) {
                $EncryptimageID = $this->Encryption->encode($this->request->data['MerchantGallery']['id']);
                echo $this->Html->image('/merchantSliderImages/thumb/' . $this->request->data['MerchantGallery']['image'], array('alt' => 'Slider Image', 'height' => 200, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;', 'title' => $this->request->data['MerchantGallery']['description']));
                echo $this->Html->link("×", array('controller' => 'hq', 'action' => 'deleteSliderPhotoName', $EncryptimageID), array('confirm' => 'Are you sure to delete slider Photo?', 'title' => 'Delete Photo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;color:red;'));
            }
            ?>
        </div>
        <div class="form-group">
            <label>Description</label> 
            <?php
            echo $this->Form->input('MerchantGallery.description', array('type' => 'textarea', 'rows' => '6', 'cols' => '8', 'label' => '', 'class' => 'form-control ckeditor', 'Placeholder' => 'Enter Description'));
            ?>
        </div>
        <div class="form-group form-spacing">
            <?php
            echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default'));
            echo "&nbsp;";
            echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/hq/merchantManageSliderPhotos'", 'class' => 'btn btn-default'));
            echo $this->Form->end();
            ?>

        </div>	  
    </div>
    <div style="clear:both;"></div>
</div><!-- /.row -->


<script>

</script>
