<?php
echo $this->Html->script('bootstrap-multiselect');
echo $this->Html->css('bootstrap-multiselect');
?>
<div class="row">
    <div class="col-lg-6">
        <h3>Add Sub Add-ons</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php echo $this->Form->button('Upload Sub Add-on', array('type' => 'button', 'onclick' => "window.location.href='/toppings/uploadsubfile'", 'class' => 'btn btn-default')); ?>  
        </div>
    </div>
</div>   
<div class="row">        
    <?php echo $this->Form->create('Topping', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'SizeAdd')); ?>
    <div class="col-lg-6">

        <div class="form-group form_margin">		 
            <label>Sub Add-ons <span class="required"> * </span></label>               

            <?php
            echo $this->Form->input('Topping.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Sub Add-ons Name', 'label' => '', 'div' => false));
            echo $this->Form->error('Topping.name');
            ?>

        </div>


        <div class="form-group form_margin">
            <label>Category<span class="required"> * </span></label>  
            <?php
            echo $this->Form->input('Category.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'required' => true, 'autocomplete' => 'off', 'options' => $categoryList, 'empty' => 'Select'));
            ?>
        </div>
        <!--
        <div class="form-group form_margin">		 
            <label>Add-ons<span class="required"> * </span></label>               
          
        <?php
        echo $this->Form->input('Topping.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $addonList, 'empty' => 'Select Add-ons'));
        echo $this->Form->error('Topping.id');
        ?>
        </div>
        -->

        <?php
        if ($addonpost) {
            $display = "style='display:block;'";
        } else {
            $display = "style='display:none;'";
        }
        ?>
        <div class="form-group form_spacing" id="addonDiv" <?php echo $display; ?> >
            <label>Add-ons<span class="required"> * </span></label>                
            <span id="addonBox" <?php echo $display; ?> >
                <?php
                echo $this->Form->input('Topping.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'multiple' => false, 'options' => $addonList));
                ?>
            </span>
        </div>

        <?php //if($itempost){ $display="style='display:block;'";}else{$display="style='display:none;'";}  ?>
        <div class="form-group form_spacing" id="ItemsDiv" style="display:none;" >
            <label>Item<span class="required"> * </span></label>                
            <span id="ItemsBox" >               
            </span>
            <span id="OfferId-errors" class="error-message hidden">Please select item.</span>
        </div>


        <div class="form-group form_margin">		 
            <label>Price<span class="required"> * </span></label>               

            <?php
            echo $this->Form->input('Topping.price', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Price', 'label' => '', 'div' => false));
            echo $this->Form->error('Topping.price');
            ?>

        </div>

        <div class="form-group form_spacing">
            <label>No Size applicable</label>
            <?php
            echo $this->Form->checkbox('Topping.no_size');
            ?>
            <label>Default</label>
            <?php
            echo $this->Form->checkbox('Topping.defaultcheck');
            ?>
        </div>


        <div class="form-group form_margin">
            <label>Status<span class="required"> * </span></label>                
            &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('Topping.is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive'),
                'default' => 1
            ));
            echo $this->Form->error('Topping.is_active');
            ?>
        </div>



        <?php //if($seasonalpost){ $display="style='display:block;'";}else{$display="style='display:none;'";}?>




        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default submit')); ?>             
        <?php echo $this->Html->link('Cancel', "/toppings/listSubTopping/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {

        //$('.multiOnly').multiselect();
        $(document).on('click', '.submit', function (e) {
            e.preventDefault();
            if ($('.multiselect-container li').hasClass('active')) {
                $("#OfferId-errors").addClass('hidden');
            } else {
                $("#OfferId-errors").removeClass('hidden');
            }
            if ($('#SizeAdd').valid()) {
                $('#SizeAdd').submit();
            }
        });
        $(document).on('click', '.multiselect', function (e) {
            if ($('.multiselect-container li').hasClass('active')) {
                $("#OfferId-errors").addClass('hidden');
            } else {
                $("#OfferId-errors").removeClass('hidden');
            }
        });

        $("#SizeAdd").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Topping][name]": {
                    required: true,
                },
                "data[Topping][id]": {
                    required: true,
                },
                "data[Topping][price]": {
                    required: true,
                    number: true
                },
                "data[Topping][item_id][]": {
                    required: true,
                    minlength: 1,
                }

            },
            messages: {
                "data[Topping][name]": {
                    required: "Please enter sub add-ons name",
                },
                "data[Topping][id]": {
                    required: "Please select add-ons",
                },
                "data[Topping][price]": {
                    required: "Please enter price",
                },
                "data[Topping][item_id][]": {
                    required: "Please select Item",
                },
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
        $('#ToppingName').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });

        $('#ToppingPrice').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });



        $("#CategoryId").change(function () {
            var catgoryId = $("#CategoryId").val();
            if (catgoryId) {
                $.ajax({url: "/toppings/addonByCategory/" + catgoryId, success: function (result) {
                        $("#addonDiv").show();
                        $("#addonBox").show();
                        $("#addonBox").html(result);
                    }});
            }
        });

        $('#ToppingPrice').keyup(function () {
            this.value = this.value.replace(/[^0-9.,]/g, '');
        });




    });
</script>