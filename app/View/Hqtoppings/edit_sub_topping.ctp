<div class="row">
    <div class="col-lg-6">
        <h3>Edit Sub Add-ons</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
        </div>
    </div>
</div> 
<hr>
<div class="row">        
    <?php echo $this->Form->create('Topping', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'SizeAdd', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">
        <div class="form-group">
            <label>Store<span class="required"> * </span></label>
            <?php
            $merchantList = $this->Common->getHQStores($this->Session->read('merchantId'));
            echo $this->Form->input('Topping.store_id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Store', 'disabled' => true));
            ?>
        </div>
        <div class="form-group form_margin">		 
            <label>Sub Add-ons <span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('Topping.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Sub Add-ons name', 'label' => '', 'div' => false));
            echo $this->Form->input('Topping.id', array('type' => 'hidden', 'class' => 'toppingHiddeniD'));
            echo $this->Form->error('Topping.name');
            echo $this->Form->input('Topping.addon_id', array('type' => 'hidden', 'id' => 'hiddenAddonid'));
            echo $this->Form->input('', array('type' => 'hidden', 'id' => 'hiddenitemid', 'value' => $this->request->data['Topping']['item_id']));
            ?>		    
        </div>
        <div class="form-group form_margin">
            <label>Category<span class="required"> * </span></label>  
            <?php
            echo $this->Form->input('Category.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'required' => true, 'autocomplete' => 'off', 'options' => $categoryList, 'empty' => 'Select'));
            ?>
        </div>		  
        <?php
        if ($addonpost) {
            $display = "style='display:block;'";
        } else {
            $display = "style='display:none;'";
        }
        ?>
        <div class="form-group form_spacing" id="addonDiv" <?php echo $display; ?> >
            <label>Add-ons<span class="required"> * </span></label>                
            <span id="addonBox" >
                <?php
                echo $this->Form->input('Topping.addon_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'multiple' => false, 'options' => $addonList));
                ?>
            </span>
        </div>
        <?php
        if ($itempost) {
            $display = "style='display:block;'";
        } else {
            $display = "style='display:none;'";
        }
        ?>
        <div class="form-group form_spacing" id="ItemsDiv" <?php echo $display; ?> >
            <label>Item<span class="required"> * </span></label>                
            <span id="ItemsBox" >
                <?php
                echo $this->Form->input('Topping.item_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => $Itemslist));
                ?>
            </span>
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
            $checked = "";
            if ($this->request->data['Topping']['no_size'] == 1) {
                $checked = "checked";
            }
            echo $this->Form->checkbox('Topping.no_size', array('checked' => $checked));
            ?>

            <label>Default</label>
            <?php
            if ($this->request->data['ItemDefaultTopping']['id']) {
                $checked = "checked";
                echo $this->Form->input('ItemDefaultTopping.id', array('type' => 'hidden'));
            }
            echo $this->Form->checkbox('Topping.defaultcheck', array('checked' => $checked));
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
        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Html->link('Cancel', "/hqtoppings/subTopping/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {

        $("#SizeAdd").validate({
            rules: {
                "data[Topping][name]": {
                    required: true,
                },
                "data[Topping][id]": {
                    required: true,
                },
                "data[Topping][price]": {
                    required: true,
                },
                "data[Category][id]": {
                    required: true
                },
                "data[Topping][addon_id]": {
                    required: true
                },
                "data[Topping][item_id]": {
                    required: true
                },
                "data[Topping][is_active]": {
                    required: true
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
                "data[Category][id]": {
                    required: "Please select category.",
                },
                "data[Topping][addon_id]": {
                    required: "Please select addons.",
                },
                "data[Topping][item_id]": {
                    required: "Please select item.",
                },
                "data[Topping][is_active]": {
                    required: "Please select status",
                }
            }
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


        $("#ToppingAddonId").change(function () {
            var catgoryId = $("#ToppingAddonId").val();
            var subtoppingID = $("#ToppingId").val();
            var itemID = $("#hiddenitemid").val();
            if (catgoryId) {
                $.ajax({url: "/hqtoppings/getItemsByAddonCategoryId/" + catgoryId + '/' + subtoppingID + '/' + itemID, success: function (result) {
                        $("#ItemsDiv").show();
                        $("#ItemsBox").show();
                        $("#ItemsBox").html(result);
                    }});
            }
        });

        $("#CategoryId").change(function () {
            var catgoryId = $("#CategoryId").val();
            var hiddenAddonid = $("#hiddenAddonid").val();
            var storeId = $("#ToppingStoreId").val();
            if (catgoryId) {
                $.ajax({url: "/hqtoppings/addonByCategoryEdit/" + catgoryId + "/" + hiddenAddonid + "/" + storeId, success: function (result) {
                        $("#addonDiv").show();
                        $("#addonBox").show();
                        $("#addonBox").html(result);
                        $("#ItemsBox").html("Please select Add-on");
                    }});
            }
        });


    });
</script>