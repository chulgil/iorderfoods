<div class="row">
    <div class="col-lg-6">
        <h3>Home Images</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
</div>  
<hr>
<div class="row">             
    <?php
    echo $this->Form->create('Stores', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'ImageUpload', 'enctype' => 'multipart/form-data'));
    ?>
    <div class="col-lg-6">
        <div class="form-group form_spacing">
            <label>Contact Left<span class="required"> * </span></label>    
            <?php
            echo $this->Form->input('HomeImage.contact_left', array('type' => 'file', 'label' => '', 'div' => false, 'class' => 'form-control', 'style' => "box-sizing:initial;"));
            echo $this->Form->error('HomeImage.contact_left');
            ?>
            <span class="blue">(For best viewing upload images with minimum resolution 600X534), (Max upload size 2MB)</span> 
        </div>
        <div class="deleteImage">
            <?php
            if (!empty($themeImages['HomeImage']['contact_left'])) {
                $EncryptimageID = $this->Encryption->encode($themeImages['HomeImage']['id']);
                echo $this->Html->image('/sliderImages/thumb/' . $themeImages['HomeImage']['contact_left'], array('alt' => 'Slider Image', 'height' => 200, 'width' => 200, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;'));
                echo $this->Html->link("X", array('controller' => 'stores', 'action' => 'deleteThemeImage', $EncryptimageID, 'contact_left'), array('confirm' => 'Are you sure to delete slider Photo?', 'title' => 'Delete Photo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
            }
            ?>
        </div>
        <div class="form-group form_spacing">
            <label>Contact Right<span class="required"> * </span></label>    
            <?php
            echo $this->Form->input('HomeImage.contact_right', array('type' => 'file', 'label' => '', 'div' => false, 'class' => 'form-control', 'style' => "box-sizing:initial;"));
            echo $this->Form->error('HomeImage.contact_right');
            ?>
            <span class="blue">(For best viewing upload images with minimum resolution 400X284), (Max upload size 2MB)</span> 
        </div>
        <div class="deleteImage">
            <?php
            if (!empty($themeImages['HomeImage']['contact_right'])) {
                $EncryptimageID = $this->Encryption->encode($themeImages['HomeImage']['id']);
                echo $this->Html->image('/sliderImages/thumb/' . $themeImages['HomeImage']['contact_right'], array('alt' => 'Slider Image', 'height' => 200, 'width' => 200, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;'));
                echo $this->Html->link("X", array('controller' => 'stores', 'action' => 'deleteThemeImage', $EncryptimageID, 'contact_right'), array('confirm' => 'Are you sure to delete slider Photo?', 'title' => 'Delete Photo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
            }
            ?>
        </div>
        <div class="form-group form_spacing">
            <label>Opening Left<span class="required"> * </span></label>    
            <?php
            echo $this->Form->input('HomeImage.opening_left', array('type' => 'file', 'label' => '', 'div' => false, 'class' => 'form-control', 'style' => "box-sizing:initial;"));
            echo $this->Form->error('HomeImage.opening_left');
            ?>
            <span class="blue">(For best viewing upload images with minimum resolution 400X284), (Max upload size 2MB)</span> 
        </div>
        <div class="deleteImage">
            <?php
            if (!empty($themeImages['HomeImage']['opening_left'])) {
                $EncryptimageID = $this->Encryption->encode($themeImages['HomeImage']['id']);
                echo $this->Html->image('/sliderImages/thumb/' . $themeImages['HomeImage']['opening_left'], array('alt' => 'Slider Image', 'height' => 200, 'width' => 200, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;'));
                echo $this->Html->link("X", array('controller' => 'stores', 'action' => 'deleteThemeImage', $EncryptimageID, 'opening_left'), array('confirm' => 'Are you sure to delete slider Photo?', 'title' => 'Delete Photo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
            }
            ?>
        </div>

        <div class="form-group form_spacing">
            <label>Opening Right<span class="required"> * </span></label>    
            <?php
            echo $this->Form->input('HomeImage.opening_right', array('type' => 'file', 'label' => '', 'div' => false, 'class' => 'form-control', 'style' => "box-sizing:initial;"));
            echo $this->Form->error('HomeImage.opening_right');
            ?>
            <span class="blue">(For best viewing upload images with minimum resolution 600X534), (Max upload size 2MB)</span> 
        </div>
        <div class="deleteImage">
            <?php
            if (!empty($themeImages['HomeImage']['opening_right'])) {
                $EncryptimageID = $this->Encryption->encode($themeImages['HomeImage']['id']);
                echo $this->Html->image('/sliderImages/thumb/' . $themeImages['HomeImage']['opening_right'], array('alt' => 'Slider Image', 'height' => 200, 'width' => 200, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;'));
                echo $this->Html->link("X", array('controller' => 'stores', 'action' => 'deleteThemeImage', $EncryptimageID, 'opening_right'), array('confirm' => 'Are you sure to delete slider Photo?', 'title' => 'Delete Photo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
            }
            ?>
        </div>

        <div class="form-group form-spacing">
            <?php
            echo $this->Form->button('Upload File', array('type' => 'submit', 'class' => 'btn btn-default'));
            echo "&nbsp;";
            echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/stores/manageSliderPhotos'", 'class' => 'btn btn-default'));
            echo $this->Form->end();
            ?>

        </div>	  
    </div>
    <div style="clear:both;"></div>
</div><!-- /.row -->


<script>

</script>