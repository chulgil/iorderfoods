<div class="container">
    <?php echo $this->element('deals/deal_form'); ?>
    <div class="row">
        <div class="col-sm-12">
            <ul class="nav nav-tabs">
                <li><?php echo $this->Html->link('Coupons', array('controller' => 'coupons', 'action' => 'addCoupon')); ?></li>
                <li><?php echo $this->Html->link('Promotions', array('controller' => 'offers', 'action' => 'addOffer')); ?></li>
                <li class="active"><?php echo $this->Html->link('Extended Offers', array('controller' => 'itemOffers', 'action' => 'add')); ?></li>
            </ul>   
            <br>
            <div class="row">
                <div class="col-lg-6">
                    <h3>Add Extended Offers</h3> 
                    <?php echo $this->Session->flash(); ?>   
                </div>             
            </div>  
            <div class="row">        
                <?php echo $this->Form->create('ItemOffers', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'ItemOfferAdd', 'enctype' => 'multipart/form-data')); ?>
                <div class="col-lg-6">            
                    <div class="form-group form_margin">		 
                        <label>Category<span class="required"> * </span></label>               

                        <?php
                        echo $this->Form->input('ItemOffer.category_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'options' => $categoryList, 'empty' => 'Select'));
                        echo $this->Form->error('ItemOffer.category_id');
                        ?>
                    </div>
                    <div class="form-group form_margin">		 
                        <label>Item<span class="required"> * </span></label>               
                        <span id="ItemsBox">
                            <?php
                            echo $this->Form->input('ItemOffer.item_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'empty' => 'Select'));
                            ?>
                        </span>
                    </div>
                    <div class="form-group form_margin">		 
                        <label>Offer Unit<span class="required"> * </span></label>               

                        <?php
                        echo $this->Form->input('ItemOffer.unit_counter', array('type' => 'text', 'class' => 'form-control valid integerValue', 'placeholder' => 'Enter offer unit', 'label' => '', 'div' => false));
                        echo $this->Form->error('ItemOffer.unit_counter');
                        ?>
                        <span class="blue">(Please enter number of unit which is free between selected dates)</span>

                    </div>
                    <div class="form-group form_margin">
                        <label>Start Date<span class="required"> * </span></label>  
                        <?php
                        echo $this->Form->input('ItemOffer.start_date', array('type' => 'text', 'class' => 'form-control', 'div' => false, 'readonly' => true));
                        ?>
                    </div>
                    <div class="form-group form_margin">
                        <label>End Date<span class="required"> * </span></label>  
                        <?php
                        echo $this->Form->input('ItemOffer.end_date', array('type' => 'text', 'class' => 'form-control', 'div' => false, 'readonly' => true));
                        ?>
                    </div>
                    <div class="form-group form_spacing">		 
                        <label>Status<span class="required"> * </span></label><span>&nbsp;&nbsp;</span>                  
                        <?php
                        $value = 1;
                        if (isset($this->request->data['ItemOffer']['is_active'])) {
                            $value = $this->request->data['ItemOffer']['is_active'];
                        }
                        echo $this->Form->input('ItemOffer.is_active', array('type' => 'radio', 'separator' => '&nbsp;&nbsp;&nbsp;&nbsp;', 'value' => $value, 'options' => array('1' => 'Active', '0' => 'Inactive')));
                        ?>		 
                    </div>
                    <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
                    <?php //echo $this->Html->link('Cancel', "/itemOffers", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
                <?php echo $this->Form->end(); ?>
            </div><!-- /.row -->
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <h3>Extended Offers Listing</h3>
                    <?php echo $this->Session->flash(); ?> 
                    <div class="table-responsive">   
                        <?php echo $this->Form->create('ItemOffer', array('url' => array('controller' => 'itemOffers', 'action' => 'add'), 'id' => 'AdminId', 'type' => 'post')); ?>
                        <div class="row padding_btm_20">
                            <div class="col-lg-3">		     
                                <?php
                                $options = array('1' => 'Active', '0' => 'Inactive');
                                echo $this->Form->input('ItemOffer.isActive', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                                ?>		
                            </div>
                            <div class="col-lg-3">
                                <?php echo $this->Form->input('ItemOffer.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Item Name')); ?>
                                <span class="blue">(<b>Search by:</b>Item Name)</span>
                            </div>
                            <div class="col-lg-1">
                                <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                            </div>
                            <div class="col-lg-1">
                                <?php echo $this->Html->link('Clear', "/itemOffers/add/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                            </div>
                            <!--                <div class="col-lg-9">		  
                                                <div class="addbutton">                
                            <?php echo $this->Form->button('Add item offer', array('type' => 'button', 'onclick' => "window.location.href='/itemOffers/add'", 'class' => 'btn btn-default')); ?> 
                                                </div>
                                            </div>-->
                        </div>
                        <?php echo $this->Form->end(); ?>
                        <div class="row">
                            <div class="col-sm-6">
                                <?php echo $this->Paginator->counter('Page {:page} of {:pages}'); ?> 
                            </div>
                            <div class="col-sm-6 text-right">
                                <?php echo $this->Paginator->counter('showing {:current} records out of {:count} total'); ?> 
                            </div>
                        </div>
                        <?php echo $this->Form->create('ItemOffer', array('url' => array('controller' => 'itemOffers', 'action' => 'deleteMultipleItemOffer'), 'type' => 'post')); ?>
                        <table class="table table-bordered table-hover table-striped tablesorter">
                            <thead>
                                <tr>
                                    <th  class="th_checkbox" style="float:left;border:none;"><input type="checkbox" id="selectall"/></th>
                                    <th  class="th_checkbox">Item name</th>
                                    <th  class="th_checkbox">Unit</th>
                                    <th  class="th_checkbox">Item Used</th>
                                    <th  class="th_checkbox">Start date</th>
                                    <th  class="th_checkbox">End date</th>
                                    <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                                        <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>
                                    <th  class="th_checkbox">Action</th>

                            </thead>
                            <tbody class="dyntable">
                                <?php
                                if ($list) {
                                    $i = 0;
                                    foreach ($list as $key => $data) {
                                        $class = ($i % 2 == 0) ? ' class="active"' : '';
                                        $EncryptOfferID = $this->Encryption->encode($data['ItemOffer']['id']);
                                        $EncryptItemID = $this->Encryption->encode($data['Item']['id']);
                                        ?>
                                        <tr <?php echo $class; ?>>
                                            <td class="firstCheckbox"><?php echo $this->Form->checkbox('ItemOffer.id.' . $key, array('class' => 'case', 'value' => $data['ItemOffer']['id'], 'style' => 'float:left;')); ?></td>
                                            <td><?php echo $data['Item']['name']; ?></td>
                                            <td><?php echo $data['ItemOffer']['unit_counter']; ?></td>
                                            <td><?php echo $this->Html->link($data['ItemOffer']['item_used_count'], array('controller' => 'itemOffers', 'action' => 'itemOfferUsedList', $EncryptItemID)); ?></td>
                                            <td><?php echo $data['ItemOffer']['start_date']; ?></td>
                                            <td><?php echo $data['ItemOffer']['end_date']; ?></td>
                                            <td>
                                                <?php
                                                if ($data['ItemOffer']['is_active']) {
                                                    echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'itemOffers', 'action' => 'activateOffer', $EncryptOfferID, 0), array('confirm' => 'Are you sure to Deactivate Offer?', 'escape' => false));
                                                } else {
                                                    echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'itemOffers', 'action' => 'activateOffer', $EncryptOfferID, 1), array('confirm' => 'Are you sure to Activate Offer?', 'escape' => false));
                                                }
                                                ?>
                                            </td>

                                            <td>      
                                                <?php
                                                if ($data['ItemOffer']['is_active'] == 1) {
                                                    echo $this->Html->link($this->Html->image("store_admin/mail_sent.png", array("alt" => "Share", "title" => "Share")), array('controller' => 'itemOffers', 'action' => 'shareExtendedOffer?extendedOfferId=' . $EncryptOfferID), array('escape' => false));
                                                    echo " | ";
                                                } else {
                                                    
                                                }
                                                ?>
                                                <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'itemOffers', 'action' => 'edit', $EncryptOfferID), array('escape' => false)); ?>
                                                <?php echo " | "; ?>
                                                <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'itemOffers', 'action' => 'deleteOffer', $EncryptOfferID), array('confirm' => 'Are you sure to delete Offer?', 'escape' => false)); ?>         
                                            </td> 

                                        </tr>
                                        <?php
                                        $i++;
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
                                            echo $this->Form->button('Delete Extended Offers', array('type' => 'submit', 'class' => 'btn btn-default', 'onclick' => 'return check();'));
                                            ?>                     
                                        </td>
                                    </tr>
                                </tfoot>
                            <?php } ?>
                        </table> 
                        <?php echo $this->Form->end(); ?>
                        <?php echo $this->element('pagination'); ?>
                        <div class="row padding_btm_20" style="padding-top:10px">
                            <div class="col-lg-1">   
                                LEGENDS:                        
                            </div>
                            <div class="col-lg-1" style=" white-space: nowrap;"><?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                            <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                            <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                            <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
                            <!--div class="col-lg-2"> <?php //echo $this->Html->image("admin/category.png"). " Add Sub Category";                      ?> </div-->

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
        $("#ItemOfferIsActive").change(function () {
            var couponId = $("#ItemOfferIsActive").val();
            $("#AdminId").submit();
        });
        $(".integerValue").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });
        $("#ItemOfferSearch").autocomplete({
            source: "<?php echo $this->Html->url(array('controller' => 'itemOffers', 'action' => 'getSearchValues')); ?>",
            minLength: 3,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        });


        $('#ItemOfferStartDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: "<?php echo date("m-d-Y", strtotime($this->Common->storeTimezone('', date("Y-m-d H:i:s")))); ?>",
            onSelect: function (selected) {
                $("#ItemOfferStartDate").prev().find('div').remove();
                $("#ItemOfferEndDate").datepicker("option", "minDate", selected)
            }

        });
        $('#ItemOfferEndDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: "<?php echo date("m-d-Y", strtotime($this->Common->storeTimezone('', date("Y-m-d H:i:s")))); ?>",
        });


        $("#ItemOfferCategoryId").change(function () {
            var catgoryId = $("#ItemOfferCategoryId").val();
            if (catgoryId) {
                $.ajax({url: "/itemOffers/itemsByCategory/" + catgoryId, success: function (result) {
                        $("#ItemsBox").html(result);
                    }});
            }
        });


        $("#ItemOfferAdd").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[ItemOffer][category_id]": {
                    required: true,
                },
                "data[ItemOffer][item_id]": {
                    required: true,
                },
                "data[ItemOffer][unit_counter]": {
                    required: true,
                    number: true,
                    min: 2,
                },
                "data[ItemOffer][start_date]": {
                    required: true,
                },
                "data[ItemOffer][end_date]": {
                    required: true,
                }

            },
            messages: {
                "data[ItemOffer][category_id]": {
                    required: "Please select category",
                },
                "data[ItemOffer][item_id]": {
                    required: "Please select Item",
                },
                "data[ItemOffer][unit_counter]": {
                    required: "Please enter offer unit",
                    number: "Please enter digit only",
                },
                "data[ItemOffer][start_date]": {
                    required: "Please select start date",
                },
                "data[ItemOffer][end_date]": {
                    required: "Please select end date",
                }

            },
            highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });


    });
</script>