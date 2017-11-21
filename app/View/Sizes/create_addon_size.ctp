
<div class="row">
    <div class="col-lg-6">
        <h3>Add Add-ons Size</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
        </div>
    </div>
</div>   
<div class="row">        
    <?php echo $this->Form->create('Sizes', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'SizeAdd', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">            
        <div class="form-group form_margin">		 
            <label>Size<span class="required"> * </span></label>               

            <?php
            echo $this->Form->input('AddonSize.size', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Size', 'label' => '', 'div' => false));
            echo $this->Form->error('AddonSize.size');
            ?>
            <span class="blue">(Please enter size.)</span>

        </div>

        <div class="form-group form_margin">		 
            <label>Price<span class="required"> * </span></label>               

            <?php
            echo $this->Form->input('AddonSize.price_percentage', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Price %', 'label' => '', 'div' => false));
            echo $this->Form->error('AddonSize.price_percentage');
            ?>
            <span class="blue">(Please enter %.)</span>

        </div>
        <br>
        <div class="form-group form_margin">
            <label>Status<span class="required"> * </span></label>                
            &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('AddonSize.is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive'),
                'default' => 1
            ));
            echo $this->Form->error('AddonSize.is_active');
            ?>
        </div>



<?php //if($seasonalpost){ $display="style='display:block;'";}else{$display="style='display:none;'";}  ?>




    <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default save')); ?>             
    <?php echo $this->Html->link('Cancel', "/sizes/addOnSizeList/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
<?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {
        $('#AddonSizePricePercentage').keyup(function () {
            this.value = this.value.replace(/[^0-9.,]/g, '');
        });
        $("#SizeAdd").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[AddonSize][size]": {
                    required: true,
                },
                "data[AddonSize][price_percentage]": {
                    required: true,
                    number: true,
                }

            },
            messages: {
                "data[AddonSize][size]": {
                    required: "Please enter Add-ons size",
                },
                "data[AddonSize][price_percentage]": {
                    required: "Please enter Add-ons price",
                },
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
        $('#AddonSizeSize').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });

        $('#AddonSizePricePercentage').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });
        
        $('#SizeAdd').submit(function() {
         var size=  $('#AddonSizeSize').val();
         if(size==1){
             alert("1 is already added as a default Addon Size");
             return false;
         }
    });
     });
</script>