<div class="row">
    <div class="col-lg-6">
        <h3>Manage Configuration</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton"> 
        </div>
    </div>
</div>  
<hr>   
<div class="row">        
    <div class="col-lg-6">        
        <?php echo $this->Form->create('Merchant', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'url' => array('controller' => 'hq', 'action' => 'backgroundImage'), 'enctype' => 'multipart/form-data')); ?>
        <?php echo $this->Form->input('Merchant.id', array('type' => 'hidden', 'value' => $merchantId)); ?>
        <div class="form-group form_margin">
            <div style="float:left;">
                <label>Upload background Image</label>
                <?php
                echo $this->Form->input('Merchant.back_image', array('type' => 'file', 'label' => '', 'div' => false, 'class' => 'form-control', 'style' => "box-sizing:initial;"));
                ?>
                <span class="blue">Max size 2MB</span>
            </div>

            <?php
            $EncryptedMerchantID = $this->Encryption->encode($merchantId);
            ?>
            <div style="float:right;">
                <?php
                if (!empty($this->request->data['Merchant']['background_image'])) {
                    echo $this->Html->image('/merchantBackground-Image/' . $this->request->data['Merchant']['background_image'], array('alt' => 'Item Image', 'height' => 150, 'width' => 150, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;', 'title' => 'Item Image'));
                    echo $this->Html->link("X", array('controller' => 'hq', 'action' => 'deleteMerchantBackgroundPhoto', $EncryptedMerchantID, "BI"), array('confirm' => 'Are you sure to delete Background Photo?', 'title' => 'Delete Photo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
                }
                ?>  
            </div>
        </div>
        <div style="clear:both;"><br/></div>
        <div class="form-group form_margin">	
            <div style="float:left;">   
                <label>Upload Logo Image</label>
                <?php
                echo $this->Form->input('Merchant.logo', array('type' => 'file', 'label' => '', 'div' => false, 'class' => 'form-control', 'style' => "box-sizing:initial;"));
                ?>
                <span class="blue">Max size 2MB</span>
            </div>
            <div style="float:right;">
                <?php
                if (!empty($this->request->data['Merchant']['logo'])) {
                    echo $this->Html->image('/merchantLogo/' . $this->request->data['Merchant']['logo'], array('alt' => 'Logo Image', 'height' => 150, 'width' => 150, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;', 'title' => 'Logo Image'));
                    echo $this->Html->link("X", array('controller' => 'hq', 'action' => 'deleteMerchantBackgroundPhoto', $EncryptedMerchantID, "LI"), array('confirm' => 'Are you sure to delete Logo?', 'title' => 'Delete Logo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
                }
                ?>  
            </div>
        </div>
        <div style="clear:both;"><br/></div>
        <div class="form-group form_spacing">
            <label>Logo Type<span class="required"> * </span></label>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('Merchant.logotype', array(
                'type' => 'radio',
                'options' => array('1' => 'Square&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '2' => 'Rectangle'),
                'default' => 1
            ));
            echo $this->Form->error('Merchant.logotype');
            ?>
        </div>
        <div style="clear:both;"><br/></div>
        <div class="form-group form_margin">
            <div style="float:left;">
                <label>Upload banner image</label>
                <?php
                echo $this->Form->input('Merchant.banner_image', array('type' => 'file', 'label' => '', 'div' => false, 'class' => 'form-control', 'style' => "box-sizing:initial;"));
                ?>
                <span class="blue">(For best viewing upload image resolution 1350X350) Max size 2MB</span>
            </div>
            <div style="float:right;">
                <?php
                if (!empty($this->request->data['Merchant']['banner_image'])) {
                    echo $this->Html->image('/merchantBackground-Image/' . $this->request->data['Merchant']['banner_image'], array('alt' => 'Item Image', 'height' => 150, 'width' => 150, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;', 'title' => 'Item Image'));
                    echo $this->Html->link("X", array('controller' => 'hq', 'action' => 'deleteMerchantBackgroundPhoto', $EncryptedMerchantID, "BANNERI"), array('confirm' => 'Are you sure to delete Banner Photo?', 'title' => 'Delete Photo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
                }
                ?>  
            </div>
        </div>
        <div style="clear:both;"><br/></div>
        <div class="form-group form_margin">
            <div style="float:left;">
                <label>Contact us background image</label>
                <?php
                echo $this->Form->input('Merchant.contact_us_bgimage', array('type' => 'file', 'label' => '', 'div' => false, 'class' => 'form-control', 'style' => "box-sizing:initial;"));
                ?>
                <span class="blue">(For best viewing upload image resolution 1920x600) Max size 2MB</span>
<!--                <span class="blue">Max size 2MB</span>-->
            </div>
            <div style="float:right;">
                <?php
                if (!empty($this->request->data['Merchant']['contact_us_bg_image'])) {
                    echo $this->Html->image('/merchantBackground-Image/' . $this->request->data['Merchant']['contact_us_bg_image'], array('alt' => 'Item Image', 'height' => 150, 'width' => 150, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;', 'title' => 'Item Image'));
                    echo $this->Html->link("X", array('controller' => 'hq', 'action' => 'deleteMerchantBackgroundPhoto', $EncryptedMerchantID, "CONTACTUS"), array('confirm' => 'Are you sure to delete image?', 'title' => 'Delete Photo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
                }
                ?>  
            </div>
        </div>
        <div style="clear:both;"><br/></div>
        <div class="form-group form_spacing">
            <label>Select Logo Position</label>
            <?php
            $positionList = array('1' => 'TOP', '2' => 'LEFT', '3' => 'CENTER');
            echo $this->Form->input('MerchantConfiguration.logo_position', array('type' => 'select', 'options' => @$positionList, 'class' => 'form-control', 'label' => false, 'div' => false));
            echo $this->Form->input('MerchantConfiguration.id', array('type' => 'hidden'));
            ?>
        </div>
        <div class="form-group form_spacing">
            <label class="radioLabel">Contact Us</label>                
            <?php
            echo $this->Form->input('MerchantConfiguration.contact_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Enable', '0' => 'Disable'),
                'default' => 0,
                'label' => false,
                'legend' => false,
                'div' => false
            ));
            ?>
        </div>
        <div class="form-group form_spacing">
            <label>Map Zoom Level<span class="required"> * </span></label>               
            <?php echo $this->Form->input('MerchantConfiguration.map_zoom_level', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Zoom Level', 'label' => '', 'div' => false)); ?>
        </div>
        <div class="form-group form_spacing">
            <?php
            echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default'));
            echo $this->Form->button('Cancel', array('type' => 'button', 'onclick' => "window.location.href='/hq/dashboard'", 'class' => 'btn btn-default'));
            ?> 
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#MerchantBackgroundImageForm").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[MerchantConfiguration][map_zoom_level]": {
                    required: true,
                    number: true,
                    digits: true
                }
            },
            messages: {
                "data[MerchantConfiguration][map_zoom_level]": {
                    required: "Please enter map zoom level.",
                }
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            }
        });
    });
</script>