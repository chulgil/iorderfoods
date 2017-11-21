<div class="row">
    <div class="col-lg-6">
        <h3>Edit Category</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
</div>   
<div class="row">        
    <?php echo $this->Form->create('Categories', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'CategoryEdit', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">            
        <div class="form-group form_margin">		 
            <label>Category Name<span class="required"> * </span></label>               

            <?php
            echo $this->Form->input('Category.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Category', 'label' => '', 'div' => false));
            echo $this->Form->error('Category.name');
            ?>
        </div>
        <br>
        <div class="form-group form_spacing">

            <div style="float:left;">
                <label>Upload Image</label>
                <?php echo $this->Form->input('Category.imgcat', array('type' => 'file', 'div' => false)); ?>
                <span class="blue">Max upload size 2MB</span> 
            </div>
            <?php
            $EncryptCategoryID = $this->Encryption->encode($this->request->data['Category']['id']);
            ?>
            <div style="float:right;">

                <?php
                if (!empty($imgpath)) {
                    echo $this->Html->image('/Category-Image/' . $this->request->data['Category']['imgcat'], array('alt' => 'Category Image', 'height' => 150, 'width' => 150, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;', 'title' => 'Category Image'));
                    echo $this->Html->link("X", array('controller' => 'categories', 'action' => 'deleteCategoryPhoto', $EncryptCategoryID), array('confirm' => 'Are you sure to delete Category Photo?', 'title' => 'Delete Photo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
                } else {
                    echo $this->Html->image('/Category-Image/index.jpeg', array('alt' => 'Category Image', 'height' => 80, 'width' => 80));
                }
                ?>
            </div>
        </div>
        <div style="clear:both;"></div>
        <!--div class="form-group form_margin">
            <label>Item Options</label> 
            <?php //echo $this->Form->input('Category.is_sizeonly', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => array('1' => 'Size Only', '2' => 'Preference Only', '3' => 'Size and Preference'), 'empty' => 'Select'));
            ?>
        </div!-->
        <br>
        <div class="form-group form_margin">
            <label>Has Add-on<span class="required"> * </span></label>                
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('Category.has_topping', array(
                'type' => 'radio',
                'options' => array('1' => 'Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '2' => 'No')
            ));
            echo $this->Form->error('Category.has_topping');
            ?>

        </div>

        <div class="form-group form_margin">
            <label>Status<span class="required"> * </span></label>                
            &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('Category.is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive')
            ));
            echo $this->Form->error('Category.is_active');
            ?>
        </div>
        <div class="form-group ">
            <label>
                <?php
                echo $this->Form->checkbox('Category.is_meal', array('value' => '1'));
                echo $this->Form->error('Category.is_meal');
                ?>
                Time Restrictions
            </label>
        </div>
        <?php
        if ($seasonalpost) {
            $display = "style='display:block;'";
        } else {
            $display = "style='display:none;'";
        }
        ?>
        <span id="FromTodate" <?php echo $display; ?>> 
            <div class="form-group">
                <label>Start Time</label>
                <td><?php
                    echo $this->Form->input('Category.start_time', array('options' => $timeOptions, 'class' => 'passwrd-input', 'div' => false));
                    echo $this->Form->error('Category.start_time');
                    ?></td>

                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label>End Time</label>

                <td><?php
                    echo $this->Form->input('Category.end_time', array('options' => $timeOptions, 'class' => 'passwrd-input ', 'div' => false));
                    echo $this->Form->error('Category.end_time');
                    ?></td>

            </div>
            <div class="form-group form_margin">
                <?php
                $days = array('1' => 'Mon', '2' => 'Tue', '3' => 'Wed', '4' => 'Thur', '5' => 'Fri', '6' => 'Sat', '7' => 'Sun');
                $selectedDays = '';
                if (!empty($this->request->data['Category']['days'])) {
                    $selectedDays = explode(',', $this->request->data['Category']['days']);
                }
                foreach ($days as $key => $data) {
                    if (!empty($selectedDays) && in_array($key, $selectedDays)) {
                        $checked = true;
                    } else {
                        $checked = false;
                    }
                    echo "<div class='pull-left' style='padding-right:15px;'>";
                    echo "<label>";
                    echo $this->Form->checkbox('Category.days.' . $key, array('hiddenField' => false, 'multiple' => 'checkbox', 'class' => '', 'value' => $data, 'checked' => $checked));
                    echo $data . "</div></label>";
                }
                ?>
            </div>
        </span>
        <?php
        if ($this->request->data['Category']['is_mandatory']) {
            $display = "style='display:block;'";
        } else {
            $display = "style='display:none;'";
        }
        ?><div class="form-group">
            <label>
                <?php
                echo $this->Form->checkbox('Category.is_mandatory', array('value' => '1'));
                echo $this->Form->error('Category.is_mandetory');
                ?>
                Mandatory
            </label>
        </div>
        <span id="minMaxItem" <?php echo $display; ?>> 
            <div class="form-group form_margin">
                <label>Min Item<span class="required"> * </span></label>
                <?php
                $minOptions = array_slice(range(0, 10), 1, NULL, TRUE);
                echo $this->Form->input('Category.min_value', array('options' => $minOptions, 'type' => 'select', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false));
                echo $this->Form->error('Category.min_value');
                ?>
            </div>
            <div class="form-group form_margin">
                <label>Max Item<span class="required"> * </span></label>  
                <?php
                $maxOptions = array_slice(range(0, 10), 1, NULL, TRUE);
                echo $this->Form->input('Category.max_value', array('options' => $maxOptions, 'type' => 'select', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false));
                echo $this->Form->error('Category.max_value');
                ?>
            </div>
        </span>

        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Html->link('Cancel', "/categories/index/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {

        $("#CategoryEdit").validate({
            rules: {
                "data[Category][name]": {
                    required: true,
                },
                "data[Category][position]": {
                    required: true,
                },
                "data[Category][has_topping]": {
                    required: true,
                }

            },
            messages: {
                "data[Category][name]": {
                    required: "Please enter category name",
                    lettersonly: "Only alphabates allowed",
                },
                "data[Category][position]": {
                    required: "Please select category position",
                },
                "data[Category][has_topping]": {
                    required: "Please select topping",
                },
            }
        });

        $("#CategoryIsMeal").change(function () {
            var flag = $("#CategoryIsMeal").val();
            if ($(this).is(":checked")) {
                $("#FromTodate").show();
            } else {
                $("#FromTodate").hide();
            }
        });
        $("#CategoryIsMandatory").change(function () {
            var flag = $(this).val();
            if ($(this).is(":checked")) {
                $("#minMaxItem").show();
            } else {
                $("#minMaxItem").hide();
            }
        });

        $('#CategoryName').change(function () {
            var str = $(this).val();

            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });
        $("#CategoryMinValue").change(function () {
            var start = $(this).val();
            var end = 10;
            var that = $("#CategoryMaxValue");
            //var array = new Array();
            that.html('');
            for (var i = start; i <= end; i++)
            {
                that.append("<option value=" + i + ">" + i + "</option>");

            }
        });
    });
</script>