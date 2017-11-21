<style>
    .days{
        float: left;
        width: 20%;
        text-align:center;
        margin-right: 1%;
        margin-bottom: 4%;
    }

    .days .form-control{		
        width: 70%;
        text-align:center;
    }
</style>

<?php
echo $this->Html->script('bootstrap-multiselect');
echo $this->Html->css('bootstrap-multiselect');
?>
<div class="row">
    <div class="col-lg-6">
        <h3>Add Menu Item</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div>
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php //echo $this->Form->button('Upload Menu', array('type' => 'button', 'onclick' => "window.location.href='/items/uploadfile'", 'class' => 'btn btn-default')); ?>  
            <?php echo $this->Form->button('Upload Items', array('type' => 'button', 'onclick' => "window.location.href='/hqitems/uploadfile'", 'class' => 'btn btn-default')); ?> 
            <?php echo $this->Form->button('View Item', array('type' => 'button', 'onclick' => "window.location.href='/hqitems/index'", 'class' => 'btn btn-default', 'escape' => false)); ?>  
        </div>
    </div>
</div>   
<hr>
<div class="row">    
    <?php echo $this->Form->create('Stores', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'addMenuItem', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">
        <div class="form-group">
            <label>Store<span class="required"> * </span></label>
            <?php
            $merchantList = $mList = $this->Common->getHQStores($this->Session->read('merchantId'));
            if (!empty($merchantList)) {
                $allOption = array('All' => 'All');
                $merchantList = array_replace($allOption, $merchantList);
            }
            echo $this->Form->input('Store.store_id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Please Select Store', 'required' => true));
            ?>
        </div>
        <div class="form-group">		 
            <label>Item Name<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('Item.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Item Name', 'label' => '', 'div' => false));
            echo $this->Form->error('Item.name');
            ?>
        </div>
        <div class="form-group">
            <label>Upload Image</label>
            <?php
            echo $this->Form->input('Item.imgcat', array('type' => 'file', 'div' => false));
            echo $this->Form->error('Item.imgcat');
            ?>
            <span class="blue">Max Upload Size 2MB</span> 
        </div>
        <div class="form-group">
            <label>Description</label> 
            <?php
            echo $this->Form->input('Item.description', array('type' => 'textarea', 'class' => 'form-control valid', 'placeholder' => 'Description', 'label' => '', 'div' => false));
            echo $this->Form->error('Item.description');
            ?>
        </div>
        <div class="form-group">
            <label>Category<span class="required"> * </span></label>
            <span class="StoreCategory">
                <?php
                echo $this->Form->input('Item.category_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'required' => true, 'autocomplete' => 'off', 'options' => $categoryList, 'empty' => 'Select Category'));
                ?>
            </span>
        </div>
        <?php
        if ($sizepost) {
            $display = "style='display:block;'";
        } else {
            $display = "style='display:none;'";
        }
        ?>
        <div class="form-group" id="SizesDiv" <?php echo $display; ?> >
            <label>Sizes<span class=""><small>(Optional)</small></span></label>                
            <span id="SizesBox" <?php echo $display; ?> >
                <?php
                echo $this->Form->input('Size.id', array('type' => 'select', 'class' => 'form-control valid multiOnly', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'multiple' => true, 'options' => $sizeList));
                ?>
            </span>
        </div>
        <div class="form-group">		 
            <label>Prices<span class="required"> * </span></label>       
            <?php
            echo $this->Form->input('ItemPrice.price', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Price', 'label' => '', 'div' => false));
            echo $this->Form->error('ItemPrice.price');
            ?>
            <span class="blue">(Please enter multiple prices by comma separated,if comma separated price not entered for Multiple sizes first price will be applicable for others.)</span>
        </div>
        <div class="form-group row intervalList">

        </div>
        <div class="clearfix"></div> 
        <?php
        if ($typepost) {
            $display = "style='display:block;'";
        } else {
            $display = "style='display:none;'";
        }
        ?>
        <div class="form-group" id="Itemtype" <?php echo $display; ?>>
            <label>Preference<span class=""><small>(Optional)</small></span></label>
            <span class="StoreItems">
                <?php
                echo $this->Form->input('Type.id', array('type' => 'select', 'class' => 'form-control multiOnly valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => $typeList, 'multiple' => true));
                ?>
            </span>
        </div>
        <div class="form-group">
            <label>Taxes</label>
            <span class="storeTaxes">
                <?php
                echo $this->Form->input('StoreTax.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => $storeTaxlist, 'empty' => 'Please Select Tax'));
                ?>
            </span>
        </div>
        <div class="form-group form_spacing" id="isMandatory" style="display:none;">		 
            <label>Item unit mandatory<span class="required"> * </span></label>       
            <?php
            $minOptions = array_slice(range(0, 10), 1, NULL, TRUE);
            echo $this->Form->input('Item.mandatory_item_units', array('options' => $minOptions, 'type' => 'select', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false));
            echo $this->Form->error('Item.mandatory_item_units');
            ?>
            <?php
//            echo $this->Form->input('Item.mandatory_item_units', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter item unit', 'label' => '', 'div' => false, 'value' => 0));
//            echo $this->Form->error('Item.mandatory_item_units');
//            
            ?>
            <span class="blue">(Please enter item unit needs to be added in order to check out)</span>
        </div>
        <div class="form-group">
            <label>
                <?php
                echo $this->Form->checkbox('Item.is_seasonal_item');
                ?> Seasonal Item
            </label>
            <span>&nbsp;&nbsp;&nbsp;</span>
            <label>              
                <?php
                echo $this->Form->checkbox('Item.is_deliverable', array('checked' => 'checked'));
                ?> Is deliverable  
            </label>   
            <span>&nbsp;&nbsp;&nbsp;</span>
            <!--            <label>
            <?php
            //echo $this->Form->checkbox('Item.preference_mandatory');
            ?> Preference Mandatory
                        </label>
                        <span>&nbsp;&nbsp;&nbsp;</span>-->
            <label>
                <?php
                echo $this->Form->checkbox('Item.default_subs_price');
                ?> Subs Default Price Applicable
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
                <label>Select Date<span class="required"> * </span></label>  
                <?php
                echo $this->Form->input('Item.start_date', array('type' => 'text', 'class' => 'form-control', 'div' => false, 'readonly' => true));
                ?>
            </div>

            <div class="form-group">
                <label>End Date<span class="required"> * </span></label>  
                <?php
                echo $this->Form->input('Item.end_date', array('type' => 'text', 'class' => 'form-control', 'div' => false, 'readonly' => true));
                ?>
            </div>
        </span>
        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default', 'escape' => false)); ?>             
        <?php echo $this->Html->link('Cancel', "/hqitems/index/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
<script>
    $(document).ready(function () {
        $('.multiOnly').multiselect();
        $('#ItemStartDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: "<?php echo date("m-d-Y", strtotime(date("Y-m-d H:i:s"))); ?>",
            onSelect: function (selected) {
                $("#ItemStartDate").prev().find('div').remove();
                $("#ItemEndDate").datepicker("option", "minDate", selected)
            }
        });
        $('#ItemEndDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: "<?php echo date("m-d-Y", strtotime(date("Y-m-d H:i:s"))); ?>",
        });
        $("#addMenuItem").validate({
            rules: {
                "data[Item][start_date]": {
                    required: true,
                },
                "data[Item][end_date]": {
                    required: true,
                },
                "data[Item][name]": {
                    required: true,
                },
                "data[ItemPrice][price]": {
                    required: true,
                },
                "data[Item][mandatory_item_units]": {
                    required: true,
                    number: true,
                    digits: true
                }
            },
            messages: {
                "data[Item][start_date]": {
                    required: "Please select Start date",
                },
                "data[Item][end_date]": {
                    required: "Please select End date",
                },
                "data[Item][name]": {
                    required: "Please enter Item name",
                },
                "data[Item][price]": {
                    required: "Please enter price",
                },
            }
        });

        $(document).on('change', '#ItemCategoryId', function () {
            var catgoryId = $("#ItemCategoryId").val();
            var storeId = $("#StoreStoreId").val();
            if (catgoryId && storeId) {
                $.ajax({url: "/hqitems/getCategorySizes/" + catgoryId + "/" + storeId, success: function (result) {
                        $("#SizesDiv").show();
                        $("#SizesBox").show();
                        $("#SizesBox").html(result);
                        var sizeonly = $("#SizeIssizeonly").val();
                        if (sizeonly == 2 || sizeonly == 3) {
                            $("#Itemtype").show();
                        } else {
                            $("#Itemtype").hide();
                        }
                        var mandatoryCatagory = $("#CategoryIsMandatory").val();
                        if (mandatoryCatagory == 1) {
                            $("#isMandatory").show();
                        } else {
                            $("#isMandatory").hide();
                        }
                    }});
            } else {
                $("#SizesDiv").hide();
                $("#SizesBox").hide();
                $("#SizesBox").html("");
            }
        });

        $("#ItemIsSeasonalItem").change(function () {
            var flag = $("#ItemIsSeasonalItem").val();
            if ($(this).is(":checked")) {
                $("#FromTodate").show();
            } else {
                $("#FromTodate").hide();
            }
        });

        $('#ItemPricePrice').keyup(function () {
            this.value = this.value.replace(/[^0-9.,]/g, '');
        });
        $('.intervalPriceValue').keyup(function () {
            this.value = this.value.replace(/[^0-9.,]/g, '');
        });

        $('#ItemName').change(function () {
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
    $('#StoreStoreId').change(function () {
        var storeId = $(this).val();
        $.ajax({
            type: 'post',
            url: '<?php echo $this->Html->url(array('controller' => 'hqitems', 'action' => 'getCategory')); ?>',
            data: {storeId: storeId},
            success: function (response) {
                if (response != '') {
                    $('.StoreCategory').html(response);
                }
            }
        });
    });
    $('#StoreStoreId').change(function () {
        var storeId = $(this).val();
        $.ajax({
            type: 'post',
            url: '<?php echo $this->Html->url(array('controller' => 'hqitems', 'action' => 'getPreferencesType')); ?>',
            data: {storeId: storeId},
            success: function (response) {
                if (response != '') {
                    $('.StoreItems').html(response);
                }
            }
        });
    });
    $('#StoreStoreId').change(function () {
        var storeId = $(this).val();
        $.ajax({
            type: 'post',
            url: '<?php echo $this->Html->url(array('controller' => 'hqitems', 'action' => 'storeTaxes')); ?>',
            data: {storeId: storeId},
            success: function (response) {
                if (response != '') {
                    $('.storeTaxes').html(response);
                }
            }
        });
    });
    $('#StoreStoreId').change(function () {
        var storeId = $(this).val();
        $.ajax({
            type: 'post',
            url: '<?php echo $this->Html->url(array('controller' => 'hqitems', 'action' => 'getIntervalList')); ?>',
            data: {storeId: storeId},
            success: function (response) {
                if (response != '') {
                    $('.intervalList').html(response);
                } else {
                    $('.intervalList').html("");
                }
            }
        });
        $('#ItemPreferenceMandatory').click(function () {
            if ($("#Itemtype").find(".multiselect").attr('title') == "None selected") {
                alert("Please select atleast 1 Preference."); //checked
                return false;
            }
        });
    });
</script>