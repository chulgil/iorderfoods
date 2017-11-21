
<div class="row">
    <div class="col-lg-6">
        <h3>Add Sub Preference</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php echo $this->Form->button('Upload Preference', array('type' => 'button', 'onclick' => "window.location.href='/SubPreferences/uploadfile'", 'class' => 'btn btn-default')); ?>  
        </div>
    </div>
</div>   
<div class="row">        
    <?php echo $this->Form->create('SubPreferences', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'TypeAdd', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">            


        <div class="form-group form_margin">		 
            <label>Sub Preference<span class="required"> * </span></label>  
            <?php
            echo $this->Form->input('SubPreference.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Preference', 'label' => '', 'div' => false));
            echo $this->Form->error('SubPreference.name');
            ?>
        </div>

        <div class="form-group form_margin">		 
            <label>Price<span class="required"> * </span></label> 
            <?php
            echo $this->Form->input('SubPreference.price', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Price', 'label' => '', 'div' => false));
            echo $this->Form->error('SubPreference.price');
            ?>
        </div>

        <div class="form-group form_spacing">		 
            <label>Preferences<span class="required"> * </span></label> 
            <?php echo $this->Form->input('SubPreference.type_id', array('type' => 'select', 'class' => 'form-control valid serialize', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $storePreferences)); ?>               
        </div>

        <div class="form-group form_margin">
            <label class='radioLabel'>Status<span class="required"> * </span></label>                
            <?php
            echo $this->Form->input('SubPreference.is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Active', '0' => 'In-Active'),
                'default' => 1,
                'label' => false,
                'legend' => false,
                'div' => false
            ));
            echo $this->Form->error('SubPreference.is_active');
            ?>
        </div>


        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Html->link('Cancel', "/SubPreferences/index/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {

        $("#TypeAdd").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[SubPreference][name]": {
                    required: true,
                },
                "data[SubPreference][price]": {
                    required: true,
                    number: true
                }

            },
            messages: {
                "data[SubPreference][name]": {
                    required: "Please enter Sub Preference name",
                },
                "data[SubPreference][price]": {
                    required: "Please enter price",
                },
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
        $('#TypeName').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });

        $('#TypePrice').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });

    });
</script>