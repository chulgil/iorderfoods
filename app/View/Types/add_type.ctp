
<div class="row">
    <div class="col-lg-6">
        <h3>Add Preference</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php echo $this->Form->button('Upload Preference', array('type' => 'button', 'onclick' => "window.location.href='/types/uploadfile'", 'class' => 'btn btn-default')); ?>  
        </div>
    </div>
</div>   
<div class="row">        
    <?php echo $this->Form->create('Types', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'TypeAdd', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">            


        <div class="form-group form_margin">		 
            <label>Preference<span class="required"> * </span></label>               

            <?php
            echo $this->Form->input('Type.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Preference', 'label' => '', 'div' => false));
            echo $this->Form->error('Type.name');
            ?>
        </div>
        <!--<br>
             <div class="form-group form_margin">		 
           <label>Price<span class="required"> * </span></label>               
         
        <?php
        echo $this->Form->input('Type.price', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Price', 'label' => '', 'div' => false));
        echo $this->Form->error('Type.price');
        ?>
       </div><br>-->
        <div class="form-group form_margin">
            <label>Status<span class="required"> * </span></label>                
            &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('Type.is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive'),
                'default' => 1
            ));
            echo $this->Form->error('Type.is_active');
            ?>
        </div>



<?php //if($seasonalpost){ $display="style='display:block;'";}else{$display="style='display:none;'";}  ?>




    <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
    <?php echo $this->Html->link('Cancel', "/types/index/", array("class" => "btn btn-default", 'escape' => false)); ?>
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
                "data[Type][name]": {
                    required: true,
                }
            },
            messages: {
                "data[Type][name]": {
                    required: "Please enter Preference name",
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