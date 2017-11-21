<div class="container">
    <?php echo $this->element('deals/deal_form'); ?>
    <div class="row">
        <div class="col-sm-12">
            <ul class="nav nav-tabs admin-nav-panel">
                <li><?php echo $this->Html->link('Coupons', array('controller' => 'coupons', 'action' => 'addCoupon')); ?></li>
                <li class="active"><?php echo $this->Html->link('Promotions', array('controller' => 'offers', 'action' => 'addOffer')); ?></li>
                <li><?php echo $this->Html->link('Extended Offers', array('controller' => 'itemOffers', 'action' => 'add')); ?></li>
            </ul>   
            <br>
            <?php
            echo $this->Html->script('bootstrap-multiselect');
            echo $this->Html->css('bootstrap-multiselect');
            ?>
            <div class="row">
                <div class="col-lg-6">
                    <h3>Add Offer</h3> 
                    <?php echo $this->Session->flash(); ?>   
                </div> 
                <div class="col-lg-6">                        
                    <div class="addbutton">                
                        <?php echo $this->Form->button('Upload Offer', array('type' => 'button', 'onclick' => "window.location.href='/offers/uploadfile'", 'class' => 'btn btn-default')); ?>  
                    </div>
                </div>
            </div>   
            <div class="row">        
                <?php echo $this->Form->create('Offers', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'addOffer', 'enctype' => 'multipart/form-data')); ?>
                <div class="col-lg-6">
                    <div class="form-group form_margin">		 
                        <label>Item Name<span class="required"> * </span></label>               

                        <?php echo $this->Form->input('Item.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $itemList, 'empty' => 'Select Item')); ?>
                        <span class="blue">(Main item on which offer is to be created.)</span>
                    </div>    
                    <div class="form-group form_margin">		 
                        <label>Number of units<span class="required"> * </span></label>               

                        <?php
                        echo $this->Form->input('Offer.unit', array('type' => 'number', 'min' => '1', 'class' => 'form-control valid', 'placeholder' => 'Number of units', 'label' => '', 'div' => false, 'value' => 1));
                        echo $this->Form->error('Offer.unit');
                        ?>
                    </div>
                    <?php
                    if ($sizepost) {
                        $display = "style='display:block;'";
                    } else {
                        $display = "style='display:none;'";
                    }
                    ?>
                    <div class="form-group form_spacing" id="SizesDiv" <?php echo $display; ?> >
                        <label>Size<span class=""><small>(Optional)</small></span></label>                
                        <span id="SizesBox" <?php echo $display; ?> >
                            <?php
                            echo $this->Form->input('Size.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'options' => $sizeList));
                            ?>
                        </span>
                    </div>        
                    <div class="form-group form_spacing">
                        <label>Description
            <!--                <span class="required"> * </span></label> -->
                            <?php
                            echo $this->Form->input('Offer.description', array('type' => 'textarea', 'class' => 'form-control valid', 'placeholder' => 'Description', 'label' => '', 'div' => false));
                            // echo $this->Form->error('Offer.description');
                            ?>
                            <span class="blue">(Offer Description)</span>
                    </div> 
                    <div class="form-group form_spacing">		 
                        <label>Offered Items<span class="required"> * </span></label>               
                        <?php echo $this->Form->input('Offered.id', array('type' => 'select', 'class' => 'form-control valid serialize multiOnly', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $itemList, 'multiple' => true)); ?>
                        <br><span id="OfferId-errors" class="error-message hidden">Please select offered item.</span>
                        <br><span class="blue">(Select Offered items which comes with offer)</span>
                    </div>  
                    <div id="dynamicItems" class="form-group form_spacing">               
                    </div>
                    <div class="form-group form_margin">
                        <label>Upload Image</label>
                        <?php
                        echo $this->Form->input('Offer.imgcat', array('type' => 'file', 'div' => false));

                        echo $this->Form->error('Item.offerImage');
                        ?>
                    </div>
                    <div class="form-group form_margin">		 
                        <label>Is Fixed Price</label><span>&nbsp;&nbsp;</span>                  
                        <?php
                        echo $this->Form->checkbox('Offer.is_fixed_price');
                        ?><br/>
                        <span class="blue">(Select "is Fixed" if you want to create aggregate price for above items)</span>
                    </div>
                    <?php
                    if ($isfixed) {
                        $display = "style='display:block;'";
                    } else {
                        $display = "style='display:none;'";
                    }
                    ?>
                    <div class="form-group form_margin" id="Offerprice" <?php echo $display; ?>>		 
                        <label>Fixed Price</label><span>&nbsp;&nbsp;</span>                  
                        <?php
                        echo $this->Form->input('Offer.offerprice', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Fixed Price', 'label' => '', 'div' => false));
                        echo $this->Form->error('Offer.offerprice');
                        ?>
                        <span class="blue">(aggregate Price for all selected items)</span>
                    </div>
                    <div class="form-group form_margin">
                        <label>Start Date</label><span class="required"> * </span>  
                        <?php
                        echo $this->Form->input('Offer.offer_start_date', array('type' => 'text', 'class' => 'form-control', 'div' => false, 'readonly' => true, 'Placeholder' => 'Select Start Date'));
                        ?>
                        <span class="blue">(Date from which offer will be applicable )</span>
                    </div>
                    <div class="form-group form_margin">
                        <label>End Date</label> <span class="required"> * </span> 
                        <?php
                        echo $this->Form->input('Offer.offer_end_date', array('type' => 'text', 'class' => 'form-control', 'div' => false, 'readonly' => true, 'disabled' => true, 'Placeholder' => 'Select End Date'));
                        ?>
                        <span class="blue">(Date till the offer will be applicable )</span>
                    </div><br>
                    <div class="form-group">
                        <?php
                        echo $this->Form->checkbox('Offer.is_time', array('value' => '1'));
                        echo $this->Form->error('Offer.is_time');
                        ?>
                        <label>Is Time</label><br>
                        <span class="blue">(Select "is Time" if you want to set Time)</span>
                    </div>
                    <span id="FromTodate" style="display:none"> 
                        <div class="form-group">
                            <td><label>Start Time</label></td> <td><?php echo $this->Form->input('Offer.offer_start_time', array('options' => $timeOptions, 'class' => 'passwrd-input ', 'div' => false)); ?></td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>&nbsp;&nbsp;&nbsp;&nbsp;  <td><label>End Time</label></td>       <td><?php echo $this->Form->input('Offer.offer_end_time', array('options' => $timeOptions, 'class' => 'passwrd-input ', 'div' => false)); ?></td>
                        </div>
                    </span>
                    <div class="form-group form_spacing">		 
                        <label>Status<span class="required"> * </span></label><span>&nbsp;&nbsp;</span>                  
                        <?php
                        $value = 1;
                        if (isset($this->request->data['Offer']['is_active'])) {
                            $value = $this->request->data['Offer']['is_active'];
                        }
                        echo $this->Form->input('Offer.is_active', array('type' => 'radio', 'separator' => '&nbsp;&nbsp;&nbsp;&nbsp;', 'value' => $value, 'options' => array('1' => 'Active', '0' => 'Inactive')));
                        ?>		 
                    </div>
                    <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default submit')); ?>             
                    <?php //echo $this->Html->link('Cancel', "/offers/index/", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
                <?php echo $this->Form->end(); ?>
            </div><!-- /.row -->
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <h3>Offers Listing</h3>
                    <hr>
                    <?php echo $this->Session->flash(); ?> 
                    <div class="table-responsive">   
                        <?php echo $this->Form->create('Offer', array('url' => array('controller' => 'offers', 'action' => 'addOffer'), 'id' => 'AdminId', 'type' => 'post')); ?>
                        <div class="row padding_btm_20">
                            <!--<div class="col-lg-2">		     
                            <?php echo $this->Form->input('Items.category_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $categoryList, 'empty' => 'Select Category')); ?>		
                           </div>-->
                            <div class="col-lg-4">
                                <?php echo $this->Form->input('Items.ids', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $nList, 'empty' => 'Select Item')); ?>
                            </div>
                            <div class="col-lg-2">		     
                                <?php
                                $options = array('1' => 'Active', '0' => 'Inactive');
                                echo $this->Form->input('Offer.isActive', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                                ?>		
                            </div>
                            <div class="col-lg-3">		     
                                <?php echo $this->Form->input('keyword', array('value' => @$keyword, 'label' => false, 'div' => false, 'placeholder' => 'Keyword Search', 'class' => 'form-control')); ?>
                                <span class="blue">(<b>Search by:</b>Item name-Offer Description)</span>
                            </div>
                            <div class="col-lg-2">		 
                                <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                                <?php echo $this->Html->link('Clear', array('controller' => 'offers', 'action' => 'addOffer', 'clear'), array('class' => 'btn btn-default')); ?>
                            </div>
                            <!--                <div class="col-lg-3">		  
                                                <div class="addbutton">                
                            <?php echo $this->Form->button('Add Offer', array('type' => 'button', 'onclick' => "window.location.href='/offers/addOffer'", 'class' => 'btn btn-default')); ?>  
                            <?php echo $this->Form->button('Upload Offer', array('type' => 'button', 'onclick' => "window.location.href='/offers/uploadfile'", 'class' => 'btn btn-default')); ?>  
                            
                                                </div>
                                            </div>-->
                        </div>
                        <?php echo $this->Form->end(); ?>
                        <?php
                        if (!empty($list)) {
                            echo $this->element('show_pagination_count');
                        }
                        ?>
                        <?php echo $this->Form->create('Offer', array('url' => array('controller' => 'offers', 'action' => 'deleteMultipleOffers'),'type' => 'post')); ?>
                        <table class="table table-bordered table-hover table-striped tablesorter">
                            <thead>
                                <tr>
                                    <th  class="th_checkbox" style="float:left;border:none;"><input type="checkbox" id="selectall"/></th>
                                    <th  class="th_checkbox">Offer Description</th>
                                    <th  class="th_checkbox"><?php echo @$this->Paginator->sort('Item.name', 'Item Name'); ?></th>
                                    <th  class="th_checkbox">Offer Used</th>
                                    <th  class="th_checkbox">Timing</th>
                                    <th  class="th_checkbox"><?php echo @$this->Paginator->sort('Offer.offer_start_date', 'Start date'); ?></th>
                                    <th  class="th_checkbox"><?php echo @$this->Paginator->sort('Offer.offer_end_date', 'End date'); ?></th>
                                    <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                                        <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>			
                                    <th  class="th_checkbox">Action</th>
                                </tr>
                            </thead>
                            <tbody class="dyntable">
                                <?php
                                if (!empty($list)) {
                                    $i = 0;
                                    foreach ($list as $key => $data) {
                                        if (!empty($data['Item']['Category'])) {
                                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                                            $EncryptOfferID = $this->Encryption->encode($data['Offer']['id']);
                                            ?>
                                            <tr <?php echo $class; ?>>
                                                <td class="firstCheckbox"><?php echo $this->Form->checkbox('Offer.id.' . $key, array('class' => 'case', 'value' => $data['Offer']['id'], 'style' => 'float:left;')); ?></td>
                                                <td><?php echo wordwrap($data['Offer']['description'], 50, "<br />"); ?></td>
                                                <td><?php echo $data['Item']['name']; ?></td>
                                                <td><?php echo $this->Html->link($data['Offer']['offer_used_count'], array('controller' => 'offers', 'action' => 'offerUsedList', $EncryptOfferID)); ?></td>
                                                <td>
                                                    <?php
                                                    if (!empty($data['Offer']['offer_start_time'])) {
                                                        echo substr($data['Offer']['offer_start_time'], 0, 5);
                                                        ?> to <?php
                                                        echo substr($data['Offer']['offer_end_time'], 0, 5);
                                                    } else {
                                                        echo "_";
                                                    }
                                                    ?>

                                                </td>			
                                                <td><?php
                                                    $Startdate = $data['Offer']['offer_start_date'];
                                                    echo ($Startdate != '0000-00-00') ? $this->Dateform->us_format($data['Offer']['offer_start_date']) : "-";
                                                    ?></td>            

                                                <td><?php
                                                    $enddate = $data['Offer']['offer_end_date'];
                                                    echo ($enddate != '0000-00-00') ? $this->Dateform->us_format($data['Offer']['offer_end_date']) : "-";
                                                    ?></td>
                                                <td>
                                                    <?php
                                                    if ($data['Offer']['is_active']) {
                                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'offers', 'action' => 'activateOffer', $EncryptOfferID, 0), array('confirm' => 'Are you sure to Deactivate Offer?', 'escape' => false));
                                                    } else {
                                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'offers', 'action' => 'activateOffer', $EncryptOfferID, 1), array('confirm' => 'Are you sure to Activate Offer?', 'escape' => false));
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($data['Offer']['is_active'] == 1) {
                                                        echo $this->Html->link($this->Html->image("store_admin/mail_sent.png", array("alt" => "Share", "title" => "Share")), array('controller' => 'offers', 'action' => 'shareOffer?offerId=' . $EncryptOfferID), array('escape' => false));
                                                        echo " | ";
                                                    } else {
                                                        
                                                    }
                                                    ?>
                                                    <?php //$EncryptStoreID=$this->Encryption->encode($data['User']['id']);  ?>
                                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'offers', 'action' => 'editOffer', $EncryptOfferID), array('escape' => false)); ?>
                                                    <?php echo " | "; ?>
                                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'offers', 'action' => 'deleteOffer', $EncryptOfferID), array('confirm' => 'Are you sure to delete Offer?', 'escape' => false)); ?>

                                                </td>
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center;">
                                            No record available
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <?php if ($list) { ?>
                                <tfoot>
                                    <tr>
                                        <td colspan="6">                       
                                            <?php
                                            echo $this->Form->button('Delete Offers', array('type' => 'submit', 'class' => 'btn btn-default', 'onclick' => 'return check();'));
                                            ?>                     
                                        </td>
                                    </tr>
                                </tfoot>
                            <?php } ?>
                        </table>
                        <?php echo $this->Form->end(); ?>
                        <?php
                        if (!empty($list)) {
                            echo $this->element('pagination');
                        }
                        ?>
                        <div class="row padding_btm_20" style="padding-top:10px">
                            <div class="col-lg-1">   
                                LEGENDS:                        
                            </div>
                            <div class="col-lg-1" style=" white-space: nowrap;"><?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                            <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                            <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                            <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    function check()
    {
        var fields = $(".case").serializeArray();
        if (fields.length == 0)
        {
            alert('Please select offers to proceed.');
            // cancel submit
            return false;
        }
        var r = confirm("Are you sure you want to delete");
        if (r == true) {
            txt = "You pressed OK!";
        } else {
            txt = "You pressed Cancel!";
            return false;
        }
    }
    $(document).ready(function () {
        $("#selectall").click(function () {
            var st = $("#selectall").prop('checked');
            $('.case').prop('checked', st);
        });
        $(".case").click(function () {
            if ($(".case").length == $(".case:checked").length) {
                $("#selectall").attr("checked", "checked");
            } else {
                $("#selectall").removeAttr("checked");
            }
        });
        $("#OfferKeyword").autocomplete({
            source: "<?php echo $this->Html->url(array('controller' => 'offers', 'action' => 'getSearchValues')); ?>",
            minLength: 3,
            search: function () {
                $("#loading").show();
            },
            response: function () {
                $("#loading").hide();
            },
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                    .append("<div>" + item.desc + "</div>")
                    .appendTo(ul);
        }
        $("#ItemsIds,#OfferIsActive").change(function () {
            $("#AdminId").submit();
        });
        $(document).on('click', '.submit', function (e) {
            e.preventDefault();
            if ($('.multiselect-container li').hasClass('active')) {
                $("#OfferId-errors").addClass('hidden');
            } else {
                $("#OfferId-errors").removeClass('hidden');
            }
            if ($('#addOffer').valid()) {
                $('#addOffer').submit();
            }
        });
        $(document).on('click', '.multiselect', function (e) {
            if ($('.multiselect-container li').hasClass('active')) {
                $("#OfferId-errors").addClass('hidden');
            } else {
                $("#OfferId-errors").removeClass('hidden');
            }
        });

        $('.multiOnly').multiselect();
        $('#OfferOfferStartDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: "<?php echo date("m-d-Y", strtotime($this->Common->storeTimezone('', date("Y-m-d H:i:s")))); ?>",
            onSelect: function (selected) {
                $("#OfferOfferStartDate").prev().find('div').remove();
                $("#OfferOfferEndDate").removeAttr('disabled');
                $("#OfferOfferEndDate").datepicker("option", "minDate", selected)
            }

        });
        $('#OfferOfferEndDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: "<?php echo date("m-d-Y", strtotime($this->Common->storeTimezone('', date("Y-m-d H:i:s")))); ?>",
        });



        $("#addOffer").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Item][id]": {
                    required: true,
                },
                "data[Offer][unit]": {
                    required: true,
                    digits: true,
                    min: 1,
                },
//                "data[Offer][description]": {
//                    required: true,
//                },
                "data[Offer][offerprice]": {
                    required: true,
                    number: true,
                    maxlength: 8
                },
                "data[Offer][offer_start_date]": {
                    required: true,
                },
                "data[Offer][offer_end_date]": {
                    required: true,
                }
            },
            messages: {
                "data[Item][id]": {
                    required: "Please select Item",
                },
                "data[Offer][unit]": {
                    required: "Please enter no. of units",
                },
//                "data[Offer][description]": {
//                    required: "Please enter offer description",
//                },
                "data[Offer][offer_start_date]": {
                    required: "Please enter start date",
                },
                "data[Offer][offer_end_date]": {
                    required: "Please enter end date",
                }
            }
            , highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });

        $("#ItemId").change(function () {
            var catgoryId = $("#ItemId").val();
            if (catgoryId) {
                $.ajax({url: "/sizes/getItemSize/" + catgoryId, success: function (result) {
                        $("#SizesDiv").show();
                        $("#SizesBox").show();
                        $("#SizesBox").html(result);
                        //$("#SizeId").removeAttr("multiple");
                    }});
            }
        });

//        $("#OfferedId").change(function(){
//		var catgoryId=$("#OfferedId").val();
//		if (catgoryId) {	
//			$.ajax({url: "/sizes/getMultipleItemSizes/"+catgoryId, success: function(result){		
//			    $("#dynamicItemsDiv").show();
//			    $("#dynamicItemsBox").show();
//			    $("#dynamicItems").html(result);			    
//			}});
//		}else{
//                    $("#dynamicItems").html('');
//                }
//	});


        $("#OfferedId").change(function () {
            var catgoryId = $("#OfferedId").val();
            var texts = $(".serialize").serialize();
            //$("#showvalue").html(texts);
            if (catgoryId) {
                $.ajax({
                    url: "/sizes/getMultipleItemSizes/",
                    type: "POST",
                    data: texts,
                    success: function (result) {
                        $("#dynamicItemsDiv").show();
                        $("#dynamicItemsBox").show();
                        $("#dynamicItems").html(result);
                    }});
            } else {
                $("#dynamicItems").html('');
            }
        });

        $("#OfferIsTime").change(function () {
            var flag = $("#OfferIsTime").val();
            if ($(this).is(":checked")) {
                $("#FromTodate").show();
            } else {
                $("#FromTodate").hide();
            }
        });
        $("#OfferIsFixedPrice").change(function () {
            var flag = $("#OfferIsFixedPrice").val();
            if ($(this).is(":checked")) {
                $("#Offerprice").show();
            } else {
                $("#Offerprice").hide();
            }
        });

        $("#ItemIsSeasonalItem").change(function () {
            var flag = $("#ItemIsSeasonalItem").val();
            if ($(this).is(":checked")) {
                $("#Offerprice").show();
            } else {
                $("#Offerprice").hide();
            }
        });

        $('#ItemPricePrice').keyup(function () {
            this.value = this.value.replace(/[^0-9.,]/g, '');
        });

        $('#OfferUnit').keyup(function () {
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