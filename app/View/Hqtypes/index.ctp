<div class="row">
    <div class="col-lg-6">
        <h3>Add Preference</h3>
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">
        <?php echo $this->Form->button('Upload Types', array('type' => 'button', 'onclick' => "window.location.href='/hqtypes/uploadfile'", 'class' => 'btn btn-default')); ?> 
    </div>
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php //echo $this->Form->button('Upload Preference', array('type' => 'button', 'onclick' => "window.location.href='/types/uploadfile'", 'class' => 'btn btn-default')); ?>  
        </div>
    </div>
</div>
<hr>
<div class="row">        
    <?php echo $this->Form->create('Types', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'TypeAdd', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">
        <div class="form-group">
            <label>Store<span class="required"> * </span></label>
            <?php
            $merchantList = $mList = $this->Common->getHQStores($this->Session->read('merchantId'));
            if (!empty($merchantList)) {
                $allOption = array('All' => 'All Store');
                $merchantList = array_replace($allOption, $merchantList);
            }
            echo $this->Form->input('Type.store_id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Store'));
            ?>
        </div>
        <div class="form-group form_margin">
            <label>Min Sub-Preference<span class="required"> * </span></label>
            <?php
            $options = range(0, 10);
            echo $this->Form->input('Type.min_value', array('options' => $options, 'type' => 'select', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false));
            echo $this->Form->error('Type.name');
            ?>
        </div>
        <div class="form-group form_margin">
            <label>Max Sub-Preference<span class="required"> * </span></label>  
            <?php
            echo $this->Form->input('Type.max_value', array('options' => $options, 'type' => 'select', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false));
            echo $this->Form->error('Type.name');
            ?>
        </div>
        <div class="form-group form_margin">		 
            <label>Preference<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('Type.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Preference', 'label' => '', 'div' => false));
            echo $this->Form->error('Type.name');
            ?>
        </div>
        <div class="form-group">
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
        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Form->end(); ?>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-lg-12">
        <h3>Preference Listing</h3>
        <hr>
    </div> 
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">   
            <?php echo $this->Form->create('Type', array('url' => array('controller' => 'hqtypes', 'action' => 'index'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">
                    <?php echo $this->Form->input('Type.storeId', array('options' => @$mList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'All Store', 'id' => 'storeId')); ?>
                </div>
                <div class="col-lg-3">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('Type.isActive', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                    ?>		
                </div>

                <div class="col-lg-3">
                    <?php echo $this->Form->input('Type.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Preference Name')); ?>
                    <span class="blue">(<b>Search by:</b>Preference Name)</span>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Html->link('Clear', "/hqtypes/index/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <?php echo $this->element('show_pagination_count'); ?>
            <?php echo $this->Form->create('Type', array('url' => array('controller' => 'hqtypes', 'action' => 'deleteMultipleType'), 'id' => 'OrderId', 'type' => 'post')); ?>
            <table class="table table-bordered table-hover table-striped tablesorter" id="preferenceListing">
                <thead>
                    <tr>
                        <th  class="th_checkbox" style="float:left;border:none"><input type="checkbox" id="selectall"/></th>
                        <th  class="th_checkbox">Name</th>
                        <th  class="th_checkbox">Store Name</th>
                        <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>
                        <th  class="th_checkbox">Action</th>
                </thead>
                <tbody id="sortable" class="dyntable" >
                    <?php
                    if (!empty($list)) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="ui-state-default active "' : 'ui-state-default';
                            $EncryptTypeID = $this->Encryption->encode($data['Type']['id']);
                            ?>
                            <tr <?php echo $class; ?> notif-id="<?php echo $EncryptTypeID; ?>" >
                                <td class="firstCheckbox"><?php echo $this->Form->checkbox('Type.id.' . $key, array('class' => 'case', 'value' => $data['Type']['id'], 'style' => 'float:left;')); ?></td>
                                <td><?php echo $data['Type']['name']; ?></td>
                                <td><?php echo @$data['Store']['store_name']; ?></td>
                                <td>
                                    <?php
                                    if ($data['Type']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hqtypes', 'action' => 'activateType', $EncryptTypeID, 0), array('confirm' => 'Are you sure to Deactivate Preference?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hqtypes', 'action' => 'activateType', $EncryptTypeID, 1), array('confirm' => 'Are you sure to Activate Preference?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td style="width:150px;" class='sort_order'>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hqtypes', 'action' => 'editType', $EncryptTypeID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'hqtypes', 'action' => 'deleteType', $EncryptTypeID), array('confirm' => 'Are you sure to delete Preference?', 'escape' => false)); ?>
                                    <?php
                                    ?>
                                </td> 
                            </tr>
                            <?php
                            $i++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="11" style="text-align: center;">
                                No record available
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
                <?php if (!empty($list)) { ?>
                    <tfoot>
                        <tr>
                            <td colspan="6">                       
                                <?php
                                echo $this->Form->button('Delete Preference', array('type' => 'submit', 'class' => 'btn btn-default', 'onclick' => 'return check();'));
                                ?>                     
                            </td>
                        </tr>
                    </tfoot> 
                <?php } ?>
            </table>
            <?php echo $this->Form->end(); ?>	
            <?php if (!empty($pagingFlag)) { ?>
                <div class="paging_full_numbers" id="example_paginate" style="padding-top:10px">
                    <?php
                    echo $this->Paginator->first('First');
                    // Shows the next and previous links
                    echo $this->Paginator->prev('Previous', null, null, array('class' => 'disabled'));
                    // Shows the page numbers
                    echo $this->Paginator->numbers(array('separator' => ''));
                    echo $this->Paginator->next('Next', null, null, array('class' => 'disabled'));
                    // prints X of Y, where X is current page and Y is number of pages
                    //echo $this->Paginator->counter();
                    echo $this->Paginator->last('Last');
                    ?>
                </div>
            <?php } ?>
            <div class="row padding_btm_20" style="padding-top:10px">
                <div class="col-lg-2">   
                    LEGENDS:                        
                </div>
                <div class="col-lg-2"><?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
            </div>
        </div>
        <?php echo $this->Html->css('pagination'); ?>
        <style>
            .firstCheckbox{width:10px;}
            #btnUpdateOrder{margin-left: 1%;}
        </style>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#TypeMinValue").change(function () {
            var start = $(this).val();
            var end = 10;
            var that = $("#TypeMaxValue");
            //var array = new Array();
            that.html('');
            for (var i = start; i <= end; i++)
            {
                that.append("<option value=" + i + ">" + i + "</option>");

            }
        });
        var storeId = $('#storeId').val();
        $("#TypeSearch").autocomplete({
            source: "/hqtypes/getSearchValues?storeID=" + storeId,
            minLength: 3,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        });
        $("#TypeAdd").validate({
            rules: {
                "data[Type][store_id]": {
                    required: true,
                },
                "data[Type][name]": {
                    required: true,
                }
            },
            messages: {
                "data[Type][store_id]": {
                    required: "Please enter store.",
                },
                "data[Type][name]": {
                    required: "Please enter Preference name",
                }
            }
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
        /**********************FOR LISTING*************************************/
        $("#TypeIsActive").change(function () {
            var IsActive = $("#TypeIsActive").val();
            $("#AdminId").submit();
        });
        $("#storeId").change(function () {
            $("#AdminId").submit();
        });

        $("#TypeItemId").change(function () {
            var ItemId = $("#TypeItemId").val();
            $("#AdminId").submit();
        });

        $("#selectall").click(function () {
            var st = $("#selectall").prop('checked');
            $('.case').prop('checked', st);
        });
        // if all checkbox are selected, check the selectall checkbox
        // and viceversa
        $(".case").click(function () {
            if ($(".case").length == $(".case:checked").length) {
                $("#selectall").attr("checked", "checked");
            } else {
                $("#selectall").removeAttr("checked");
            }

        });
    });
    function check()
    {
        var fields = $(".case").serializeArray();
        if (fields.length == 0)
        {
            alert('Please select one Preference to proceed.');
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
</script>