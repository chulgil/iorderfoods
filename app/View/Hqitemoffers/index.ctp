<div class="container">
    <?php echo $this->element('deals/hq_deal_main_element'); ?>
    <div class="row">
        <div class="col-sm-12">
            <ul class="nav nav-tabs">
                <li><?php echo $this->Html->link('Coupons', array('controller' => 'hqcoupons', 'action' => 'index')); ?></li>
                <li><?php echo $this->Html->link('Promotions', array('controller' => 'hqoffers', 'action' => 'index')); ?></li>
                <li class="active"><?php echo $this->Html->link('Extended Offers', array('controller' => 'hqitemoffers', 'action' => 'index')); ?></li>
            </ul>
            <br>
            <div class="row">
                <div class="col-lg-6">
                    <h3>Add Extended Offers</h3>
                    <hr>
                    <?php echo $this->Session->flash(); ?>   
                </div>             
            </div>   
            <div class="row">        
                <?php echo $this->Form->create('ItemOffers', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'ItemOfferAdd', 'enctype' => 'multipart/form-data')); ?>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Store<span class="required"> * </span></label>
                        <?php
                        $merchantList = $mList = $this->Common->getHQStores($this->Session->read('merchantId'));
                        if (!empty($merchantList)) {
                            $allOption = array('All' => 'All Store');
                            $merchantList = array_replace($allOption, $merchantList);
                        }
                        echo $this->Form->input('ItemOffer.store_id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Store'));
                        ?>
                    </div>
                    <div class="form-group">		 
                        <label>Category<span class="required"> * </span></label>
                        <span class="StoreCategory">
                            <?php
                            echo $this->Form->input('ItemOffer.category_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'options' => @$categoryList, 'empty' => 'Select Category'));
                            echo $this->Form->error('ItemOffer.category_id');
                            ?>
                        </span>
                    </div>
                    <div class="form-group">		 
                        <label>Item<span class="required"> * </span></label>               
                        <span id="ItemsBox">
                            <?php
                            echo $this->Form->input('ItemOffer.item_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'empty' => 'Select Item'));
                            ?>
                        </span>
                    </div>
                    <div class="form-group">		 
                        <label>Offer Unit<span class="required"> * </span></label>               
                        <?php
                        echo $this->Form->input('ItemOffer.unit_counter', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter offer unit', 'label' => '', 'div' => false));
                        echo $this->Form->error('ItemOffer.unit_counter');
                        ?>
                        <span class="blue">(Please enter number of unit which is free between selected dates)</span>
                    </div>
                    <div class="form-group">
                        <label>Start Date<span class="required"> * </span></label>  
                        <?php
                        echo $this->Form->input('ItemOffer.start_date', array('type' => 'text', 'class' => 'form-control', 'div' => false, 'readonly' => true));
                        ?>
                    </div>
                    <div class="form-group">
                        <label>End Date<span class="required"> * </span></label>  
                        <?php
                        echo $this->Form->input('ItemOffer.end_date', array('type' => 'text', 'class' => 'form-control', 'div' => false, 'readonly' => true));
                        ?>
                    </div>
                    <div class="form-group">		 
                        <?php
                        $value = 1;
                        if (isset($this->request->data['ItemOffer']['is_active'])) {
                            $value = $this->request->data['ItemOffer']['is_active'];
                        }
                        echo $this->Form->input('ItemOffer.is_active', array('type' => 'radio', 'separator' => '&nbsp;&nbsp;&nbsp;&nbsp;', 'value' => $value, 'options' => array('1' => 'Active', '0' => 'Inactive')));
                        ?>		 
                    </div>
                    <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
                </div>
                <?php echo $this->Form->end(); ?>
            </div><!-- /.row -->
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <h3>Extended Offer Listing</h3>
                    <hr>
                    <div class="table-responsive">   
                        <?php echo $this->Form->create('ItemOffer', array('url' => array('controller' => 'hqitemoffers', 'action' => 'index'), 'id' => 'AdminId', 'type' => 'post')); ?>
                        <div class="row padding_btm_20">
                            <div class="col-lg-3">		     
                                <?php echo $this->Form->input('ItemOffer.storeId', array('options' => @$mList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'All Store', 'id' => 'storeId')); ?>
                            </div>

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
                            <div class="col-lg-2">
                                <?php echo $this->Html->link('Clear', "/hqitemoffers/index/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                            </div>
                        </div>
                        <?php echo $this->Form->end(); ?>
                        <?php echo $this->element('show_pagination_count'); ?>
                        <?php echo $this->Form->create('ItemOffer', array('url' => array('controller' => 'hqitemoffers', 'action' => 'deleteMultipleItemOffer'), 'type' => 'post')); ?>
                        <table class="table table-bordered table-hover table-striped tablesorter">
                            <thead>
                                <tr>	    
                                    <th  class="th_checkbox" style="float:left;border:none;"><input type="checkbox" id="selectall"/></th>
                                    <th  class="th_checkbox">Item name</th>
                                    <th  class="th_checkbox">Store name</th>
                                    <th  class="th_checkbox">Unit</th>
                                    <th  class="th_checkbox">Start date</th>
                                    <th  class="th_checkbox">End date</th>
                                    <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                                        <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>
                                    <th  class="th_checkbox">Action</th>

                            </thead>

                            <tbody class="dyntable">
                                <?php
                                if (!empty($list)) {
                                    $i = 0;
                                    foreach ($list as $key => $data) {
                                        $class = ($i % 2 == 0) ? ' class="active"' : '';
                                        $EncryptOfferID = $this->Encryption->encode($data['ItemOffer']['id']);
                                        ?>
                                        <tr <?php echo $class; ?>>	
                                            <td class="firstCheckbox"><?php echo $this->Form->checkbox('ItemOffer.id.' . $key, array('class' => 'case', 'value' => $data['ItemOffer']['id'], 'style' => 'float:left;')); ?></td>
                                            <td><?php echo $data['Item']['name']; ?></td>
                                            <td><?php echo $data['Store']['store_name']; ?></td>
                                            <td><?php echo $data['ItemOffer']['unit_counter']; ?></td>
                                            <td><?php echo $data['ItemOffer']['start_date']; ?></td>
                                            <td><?php echo $data['ItemOffer']['end_date']; ?></td>
                                            <td>
                                                <?php
                                                if ($data['ItemOffer']['is_active']) {
                                                    echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hqitemoffers', 'action' => 'activateOffer', $EncryptOfferID, 0), array('confirm' => 'Are you sure to Deactivate Offer?', 'escape' => false));
                                                } else {
                                                    echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hqitemoffers', 'action' => 'activateOffer', $EncryptOfferID, 1), array('confirm' => 'Are you sure to Activate Offer?', 'escape' => false));
                                                }
                                                ?>
                                            </td>

                                            <td>
                                                <?php
                                                if ($data['ItemOffer']['is_active'] == 1) {
                                                    echo $this->Html->link($this->Html->image("store_admin/mail_sent.png", array("alt" => "Share", "title" => "Share")), array('controller' => 'hqitemoffers', 'action' => 'shareExtendedOffer?extendedOfferId=' . $EncryptOfferID), array('escape' => false));
                                                    echo " | ";
                                                } else {
                                                    
                                                }
                                                ?>                                    
                                                <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hqitemoffers', 'action' => 'edit', $EncryptOfferID), array('escape' => false)); ?>
                                                <?php echo " | "; ?>
                                                <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'hqitemoffers', 'action' => 'deleteOffer', $EncryptOfferID), array('confirm' => 'Are you sure to delete Offer?', 'escape' => false)); ?>         
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
                        <div class="paging_full_numbers" id="example_paginate" style="padding-top:10px">
                            <?php
                            echo @$this->Paginator->first('First');
                            // Shows the next and previous links
                            echo @$this->Paginator->prev('Previous', null, null, array('class' => 'disabled'));
                            // Shows the page numbers
                            echo @$this->Paginator->numbers(array('separator' => ''));
                            echo @$this->Paginator->next('Next', null, null, array('class' => 'disabled'));
                            // prints X of Y, where X is current page and Y is number of pages
                            //echo $this->Paginator->counter();
                            echo @$this->Paginator->last('Last');
                            ?>
                        </div>
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
<?php echo $this->Html->css('pagination'); ?>

<script>
    function check()
    {
        var fields = $(".case").serializeArray();
        if (fields.length == 0)
        {
            alert('Please select offer to proceed.');
            // cancel submit
            return false;
        }
        var r = confirm("Are you sure you want to delete.");
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
        var storeId = $('#storeId').val();
        $("#ItemOfferSearch").autocomplete({
            source: "/hqitemoffers/getSearchValues?storeId=" + storeId,
            minLength: 3,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        });


        $("#ItemOfferIsActive,#storeId").change(function () {
            $("#AdminId").submit();
        });
        $('#ItemOfferStartDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: "<?php echo date("m-d-Y", strtotime(date("Y-m-d H:i:s"))); ?>",
            onSelect: function (selected) {
                $("#ItemOfferStartDate").prev().find('div').remove();
                $("#ItemOfferEndDate").datepicker("option", "minDate", selected)
            }

        });
        $('#ItemOfferEndDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: "<?php echo date("m-d-Y", strtotime(date("Y-m-d H:i:s"))); ?>",
        });
        $("#ItemOfferAdd").validate({
            rules: {
                "data[ItemOffer][store_id]": {
                    required: true,
                },
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

            }
        });
        $('#ItemOfferStoreId').change(function () {
            var storeId = $(this).val();
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Html->url(array('controller' => 'hqitemoffers', 'action' => 'getCategory')); ?>',
                data: {storeId: storeId},
                success: function (response) {
                    if (response != '') {
                        $('.StoreCategory').html(response);
                    }
                }
            });
        });
        $(document).on('change', '#ItemOfferCategoryId', function () {
            var catgoryId = $("#ItemOfferCategoryId").val();
            var storeId = $("#ItemOfferStoreId").val();
            if (catgoryId && storeId) {
                $.ajax({url: "/hqitemoffers/itemsByCategory/" + catgoryId + "/" + storeId, success: function (response) {
                        if (response != '') {
                            $('#ItemsBox').html(response);
                        }
                    }});
            }
        });

    });
</script>