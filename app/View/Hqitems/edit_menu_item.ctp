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
        <h3>Edit Menu Item</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));   ?>
            <?php echo $this->Form->button('View Item', array('type' => 'button', 'onclick' => "window.location.href='/hqitems/index'", 'class' => 'btn btn-default', 'escape' => false)); ?>  
            <?php echo $this->Form->button('Add Menu Item', array('type' => 'button', 'onclick' => "window.location.href='/hqitems/addMenuItem'", 'class' => 'btn btn-default', 'escape' => false)); ?>  
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
            $merchantList = $this->Common->getHQStores($this->Session->read('merchantId'));
            if (!empty($StoreValue)) {
                $store_id = $StoreValue;
            }
            echo $this->Form->input('Item.store_id', array('default' => @$StoreValue, 'options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Please Select Store', 'required' => true, 'disabled' => true));
            ?>
        </div>
        <div class="form-group">		 
            <label>Item Name<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('Item.id', array('type' => 'hidden', 'label' => false, 'div' => false));
            ?>
            <?php
            echo $this->Form->input('Item.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Item Name', 'label' => '', 'div' => false));
            echo $this->Form->error('Item.name');
            ?>
        </div>
        <div class="form-group">
            <div style="float:left;">
                <label>Upload Item Photo</label>
                <?php
                echo $this->Form->input('Item.imgcat', array('type' => 'file', 'div' => false));
                echo $this->Form->error('Item.imgcat');
                ?>
                <span class="blue">Max Upload Size 2MB</span> 
            </div>
            <?php
            $EncryptItemID = $this->Encryption->encode($this->request->data['Item']['id']);
            ?>
            <div style="float:right;">
                <?php
                if (!empty($this->request->data['Item']['image'])) {
                    echo $this->Html->image('/MenuItem-Image/' . $this->request->data['Item']['image'], array('alt' => 'Item Image', 'height' => 150, 'width' => 150, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;', 'title' => 'Item Image'));
                    echo $this->Html->link("X", array('controller' => 'hqitems', 'action' => 'deleteItemPhoto', $EncryptItemID), array('confirm' => 'Are you sure to delete Item Photo?', 'title' => 'Delete Photo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
                }
                ?>
            </div>		
        </div>
        <div style="clear:both;"></div>
        <div class="form-group">
            <label>Description</label> 
            <?php
            echo $this->Form->input('Item.description', array('type' => 'textarea', 'class' => 'form-control valid', 'placeholder' => 'Description', 'label' => '', 'div' => false));
            echo $this->Form->error('Item.description');
            ?>
        </div>
        <div class="form-group">
            <label>Category<span class="required"> * </span></label>                

            <?php
            echo $this->Form->input('Item.category_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'required' => true, 'autocomplete' => 'off', 'options' => $categoryList, 'empty' => 'Select'));
            ?>
        </div>

        <?php
        if (!empty($sizepost)) {
            $display = "style='display:block;'";
        } else {
            $display = "style='display:none;'";
        }
        ?>
        <div class="form-group" id="SizesDiv" <?php echo $display; ?> >
            <label>Sizes<span class=""><small>(Optional)</small></span></label>                
            <span id="SizesBox" <?php echo $display; ?> >
                <?php
                echo $this->Form->input('Size.id', array('type' => 'select', 'class' => 'form-control multiOnly valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'multiple' => true, 'options' => $sizeList));
                ?>
            </span>
        </div>


        <div class="form-group">		 
            <label>Prices<span class="required"> * </span></label>       

            <?php
            echo $this->Form->input('ItemPrice.price', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Price', 'label' => '', 'div' => false));
            echo $this->Form->error('Item.price');
            ?>
            <span class="blue">(Please enter multiple prices by comma separated,if comma separated price not entered for Multiple sizes first price will be applicable for others.)</span>
        </div>

        <div class="form-group row">	 		

            <?php
            if (!empty($intervalDetail) && isset($intervalDetail[0]['IntervalPrice']) && !empty($intervalDetail[0]['IntervalPrice'])) {
                foreach ($intervalDetail as $interval) {
                    ?>
                    <div class="col-lg-4" style="padding-bottom:15px;">
                        <div style="height:40px;font-size:12px;">
                            <?php
                            if (isset($interval['IntervalPrice']) && !empty($interval['IntervalPrice'])) {
                                echo $this->Form->checkbox('Interval.Edit.Status.' . $interval['Interval']['id'], array('checked' => $interval['IntervalPrice'][0]['is_active']));
                            } else {
                                echo $this->Form->checkbox('Interval.Edit.Status.' . $interval['Interval']['id']);
                            }

                            echo $interval['Interval']['name'];
                            ?>
                        </div>
                        <div>
                            <?php
                            if (isset($interval['IntervalPrice']) && !empty($interval['IntervalPrice'])) {
                                $intervalPriceArray = array();
                                foreach ($interval['IntervalPrice'] as $intervalPrice) {
                                    if (($this->request->data['Item']['id'] == $intervalPrice['item_id']) && ($intervalPrice['size_active'] == 1)) {
                                        $intervalPriceArray[] = number_format($intervalPrice['price'], 2);
                                    }
                                }
                                $intervalPriceArray = implode(',', $intervalPriceArray);
                                echo $this->Form->input('Interval.Edit.Price.' . $interval['Interval']['id'], array('type' => 'text', 'class' => 'form-control valid intervalPriceValue', 'placeholder' => '', 'label' => false, 'div' => false, 'value' => $intervalPriceArray));
                            } else {
                                echo $this->Form->input('Interval.Edit.Price.' . $interval['Interval']['id'], array('type' => 'text', 'class' => 'form-control valid intervalPriceValue', 'placeholder' => '', 'label' => false, 'div' => false));
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
            } elseif (!empty($intervalList)) {
                foreach ($intervalList as $key => $value) {
                    ?>
                    <div class="col-lg-4" style="padding-bottom:15px;">
                        <div style="height:40px;font-size:12px;">
                            <?php echo $this->Form->checkbox('Interval.Add.Status.' . $key); ?>
                            <?php echo $value; ?>
                        </div>
                        <div>
                            <?php echo $this->Form->input('Interval.Add.Price.' . $key, array('type' => 'text', 'class' => 'form-control valid intervalPriceValue', 'placeholder' => '', 'label' => false, 'div' => false)); ?>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>

        </div>
        <div class="clearfix"></div>


        <?php
        if (!empty($typepost)) {
            $display = "style='display:block;'";
        } else {
            $display = "style='display:none;'";
        }
        ?>
        <div class="form-group" id="Itemtype" <?php echo $display; ?>>
            <label>Preference<span class=""><small>(Optional)</small></span></label>   
            <?php
            echo $this->Form->input('Type.id', array('type' => 'select', 'class' => 'form-control multiOnly valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => $typeList, 'multiple' => true));
            ?>
        </div>

        <div class="form-group">
            <label>Taxes</label>   
            <?php
            echo $this->Form->input('ItemPrice.store_tax_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => $storeTaxlist, 'empty' => 'Please Select Tax'));
            ?>
        </div>
        <?php 
        if ($this->request->data["Category"]["is_mandatory"]) {
            $display = "style='display:block;'";
        } else {
            $display = "style='display:none;'";
        }
        ?>
        <div class="form-group form_spacing" id="isMandatory" <?php echo $display; ?>>		 
            <label>Item unit mandatory<span class="required"> * </span></label>       
            <?php
            $minOptions = array_slice(range(0, 10), 1, NULL, TRUE);
            echo $this->Form->input('Item.mandatory_item_units', array('options' => $minOptions, 'type' => 'select', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false));
            echo $this->Form->error('Item.mandatory_item_units');
            ?>
            <?php
//               echo $this->Form->input('Item.mandatory_item_units', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter item unit', 'label' => '', 'div' => false));
//               echo $this->Form->error('Item.mandatory_item_units');
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
        if (!empty($seasonalpost)) {
            $display = "style='display:block;'";
        } else {
            $display = "style='display:none;'";
        }
        ?>
        <span id="FromTodate" <?php echo $display; ?>>
            <div class="form-group">
                <label>Select Date<span class="required"> * </span></label>  
                <?php
                if ($this->request->data['Item']['start_date'] == "0000-00-00") {
                    $this->request->data['Item']['start_date'] = "";
                }
                echo $this->Form->input('Item.start_date', array('type' => 'text', 'class' => 'form-control', 'div' => false));
                ?>
            </div>

            <div class="form-group">
                <label>End Date<span class="required"> * </span></label>  
                <?php
                if ($this->request->data['Item']['end_date'] == "0000-00-00") {
                    $this->request->data['Item']['end_date'] = "";
                }
                echo $this->Form->input('Item.end_date', array('type' => 'text', 'class' => 'form-control', 'div' => false));
                ?>
            </div>
        </span>

        <?php echo $this->Form->button('Update', array('type' => 'submit', 'class' => 'btn btn-default', 'escape' => false)); ?>             
        <?php echo $this->Html->link('Cancel', "/hqitems/index/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
    <?php
    if (!empty($store_id)) {
        $minDate = date("m-d-Y", strtotime($this->Hq->storeTimezone(null, date("Y-m-d H:i:s"), null, $store_id)));
    } else {
        $minDate = date("m-d-Y", strtotime(date("Y-m-d H:i:s")));
    }
    ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {
        $('.multiOnly').multiselect();
        $('#ItemStartDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: "<?php echo $minDate; ?>",
            onSelect: function (selected) {
                $("#ItemStartDate").prev().find('div').remove();
                $("#ItemEndDate").datepicker("option", "minDate", selected)
            }

        });
        $('#ItemEndDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: "<?php echo $minDate; ?>",
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

        $("#ItemCategoryId").change(function () {
            var catgoryId = $("#ItemCategoryId").val();
            var storeId = $("#ItemStoreId").val();
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

        $('#SizeId :selected').attr('selected', '');

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

        $('#ItemPreferenceMandatory').click(function () {
            if ($("#Itemtype").find(".multiselect").attr('title') == "None selected") {
                alert("Please select atleast 1 Preference."); //checked
                return false;
            }
        });
    });
</script>