<?php echo $this->Html->css('jquery.minicolors'); ?>
<?php echo $this->Html->script('jscolor'); ?>
<?php echo $this->Html->script('jquery.minicolors.min'); ?>
<div class="row">
    <div class="col-lg-12">
        <h3>Merchant Css</h3>
        <hr>
        <?php echo $this->Session->flash(); ?> 
    </div>
    <div class="col-lg-8">
        <div class="form-group">		 
            <label>Color Picker</label>               
            <?php
            echo $this->Form->input('picker', array('type' => 'color', 'class' => 'form-control', 'placeholder' => 'Enter Css', 'label' => '', 'div' => false));
            ?>
        </div>
        <div class="form-group">		 
            <label>Color Picker New</label>               
            <?php
            echo $this->Form->input('picker1', array('type' => 'text', 'class' => "form-control jscolor {width:243, height:150, position:'right',borderColor:'#FFF', insetColor:'#FFF', backgroundColor:'#666'}", 'placeholder' => 'Enter Css', 'label' => '', 'div' => false, 'value' => 'ffcc00'));
            ?>
        </div>
        <div class="form-group">
            <label for="letter-case">Swatches</label>
            <br>
            <input type="text" id="swatches" class="form-control demo" data-swatches="#fff|#000|#f00|#0f0|#00f|#ff0|#0ff" value="#abcdef">
            <span class="help-block">
                Example with swatches.
            </span>
        </div>
        <?php echo $this->Form->create('MerchantDesign', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'TemplateAdd')); ?>
        <div class="form-group">		 
            <label>Layout Css<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('MerchantDesign.merchant_css', array('type' => 'textarea', 'class' => 'form-control', 'placeholder' => 'Enter Css', 'label' => '', 'div' => false, 'rows' => "20"));
            echo $this->Form->input('MerchantDesign.id', array('type' => 'hidden'));
            ?>
        </div>
        <div class="form-group">
            <label>Status<span class="required"> * </span></label>                
            &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('MerchantDesign.is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive'),
                'default' => 1
            ));
            echo $this->Form->error('MerchantDesign.is_active');
            ?>
        </div>
        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Form->end(); ?>
    </div>
</div>
<script>
    $(document).ready(function () {

        $('.demo').each(function () {
            //
            // Dear reader, it's actually very easy to initialize MiniColors. For example:
            //
            //  $(selector).minicolors();
            //
            // The way I've done it below is just for the demo, so don't get confused
            // by it. Also, data- attributes aren't supported at this time...they're
            // only used for this demo.
            //
            $(this).minicolors({
                control: $(this).attr('data-control') || 'hue',
                defaultValue: $(this).attr('data-defaultValue') || '',
                format: $(this).attr('data-format') || 'hex',
                keywords: $(this).attr('data-keywords') || '',
                inline: $(this).attr('data-inline') === 'true',
                letterCase: $(this).attr('data-letterCase') || 'lowercase',
                opacity: $(this).attr('data-opacity'),
                position: $(this).attr('data-position') || 'bottom left',
                swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
                change: function (value, opacity) {
                    if (!value)
                        return;
                    if (opacity)
                        value += ', ' + opacity;
                },
                theme: 'bootstrap'
            });

        });

    });
</script>