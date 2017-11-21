<div class="row">
    <div class="col-lg-6">
        <h3>Add Add-ons Size</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">
        <?php echo $this->Form->button('Upload Add-ons Size', array('type' => 'button', 'onclick' => "window.location.href='/hqsizes/uploadaddonsfile'", 'class' => 'btn btn-default')); ?> 
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
        </div>
    </div>
</div>   
<hr>
<div class="row">        
    <?php echo $this->Form->create('AddonSize', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'SizeAdd', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">        
        <div class="form-group">
            <label>Store<span class="required"> * </span></label>
            <?php
            $merchantList = $mList = $this->Common->getHQStores($this->Session->read('merchantId'));
            if (!empty($merchantList)) {
                $allOption = array('All' => 'All Store');
                $merchantList = array_replace($allOption, $merchantList);
            }
            echo $this->Form->input('AddonSize.store_id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Store'));
            ?>
        </div>
        <div class="form-group">		 
            <label>Size<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('AddonSize.size', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Size', 'label' => '', 'div' => false));
            echo $this->Form->error('AddonSize.size');
            ?>
            <span class="blue">(Please enter size.)</span>
        </div>
        <div class="form-group">		 
            <label>Price<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('AddonSize.price_percentage', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Price %', 'label' => '', 'div' => false));
            echo $this->Form->error('AddonSize.price_percentage');
            ?>
            <span class="blue">(Please enter %.)</span>
        </div>
        <div class="form-group">
            <label>Status<span class="required"> * </span></label>                
            &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('AddonSize.is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive'),
                'default' => 1
            ));
            echo $this->Form->error('AddonSize.is_active');
            ?>
        </div>
        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Html->link('Cancel', "/hqsizes/addOnSize/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
<hr>
<div class="row">
    <div class="col-lg-12">
        <h3>Add-ons size Listing</h3>
        <hr>
    </div>
    <div class="col-lg-12">
        <div class="table-responsive">   
            <?php echo $this->Form->create('AddonSize', array('url' => array('controller' => 'hqsizes', 'action' => 'addOnSize'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">		     
                    <?php
                    echo $this->Form->input('AddonSize.storeId', array('options' => @$mList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'All Store', 'id' => 'storeId'));
                    ?>		
                </div>
                <div class="col-lg-2">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('AddonSize.isActive', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                    ?>		
                </div>
                
                <div class="col-lg-3">
                    <?php echo $this->Form->input('AddonSize.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Size Name')); ?>
                    <span class="blue">(<b>Search by:</b>Size Name)</span>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Html->link('Clear', "/hqsizes/addOnSize/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
                <div class="col-lg-2">		 
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <?php   echo $this->element('show_pagination_count'); ?>
            <?php echo $this->Form->create('AddonSize', array('url' => array('controller' => 'hqsizes', 'action' => 'deleteMultipleAddonSize'), 'id' => 'OrderId', 'type' => 'post')); ?>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>
                        <th  class="th_checkbox" style="float:left;border:none;"><input type="checkbox" id="selectall"/></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('AddonSize.size', 'Size'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('AddonSize.price_percentage', 'Price'); ?> (%)</th>
                        <th  class="th_checkbox">Store Name</th>
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
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptSizeID = $this->Encryption->encode($data['AddonSize']['id']);
                            ?>
                            <tr <?php echo $class; ?>>
                                <td class="firstCheckbox"><?php echo $this->Form->checkbox('AddonSize.id.' . $key, array('class' => 'case', 'value' => $data['AddonSize']['id'], 'style' => 'float:left;')); ?></td>
                                <td><?php echo $data['AddonSize']['size']; ?></td>
                                <td style="width:120px;"><?php echo $data['AddonSize']['price_percentage']; ?></td>
                                <td><?php echo $data['Store']['store_name']; ?></td>
                                <td>
                                    <?php
                                    if ($data['AddonSize']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hqsizes', 'action' => 'activateAddonSize', $EncryptSizeID, 0), array('confirm' => 'Are you sure to Deactivate Add-ons size?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hqsizes', 'action' => 'activateAddonSize', $EncryptSizeID, 1), array('confirm' => 'Are you sure to Activate Add-ons size?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hqsizes', 'action' => 'editAddonSize', $EncryptSizeID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'hqsizes', 'action' => 'deleteAddonSize', $EncryptSizeID), array('confirm' => 'Are you sure to delete Add-ons size?', 'escape' => false)); ?>
                                </td>
                            </tr>
                            <?php
                            $i++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">
                                No record available
                            </td>
                        </tr>
                    <?php } if (!empty($list)) { ?>
                        <tr>
                            <td colspan="6">                       
                                <?php
                                echo $this->Form->button('Delete Size', array('type' => 'submit', 'class' => 'btn btn-default', 'onclick' => 'return check();'));
                                ?>                     
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php echo $this->Form->end(); ?>
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
    </div>
</div>   
<?php echo $this->Html->css('pagination'); ?>
<style>
    .firstCheckbox{width:10px;}
</style>
<script>
    $(document).ready(function () {
        
          var storeId=$('#storeId').val();
        $("#AddonSizeSearch").autocomplete({
            source: "/hqsizes/getAddonsSizeName?storeID="+storeId,
            minLength: 2,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        });
        
        $("#SizeAdd").validate({
            rules: {
                "data[AddonSize][store_id]": {
                    required: true,
                },
                "data[AddonSize][size]": {
                    required: true,
                },
                "data[AddonSize][price_percentage]": {
                    required: true,
                    number: true,
                }
            },
            messages: {
                "data[AddonSize][store_id]": {
                    required: "Please select store.",
                },
                "data[AddonSize][size]": {
                    required: "Please enter Add-ons size",
                },
                "data[AddonSize][price_percentage]": {
                    required: "Please enter Add-ons price",
                },
            }
        });
        $('#AddonSizeSize').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });

        $('#AddonSizePricePercentage').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });
        $('#SizeAdd').submit(function() {
         var size=  $('#AddonSizeSize').val();
         if(size==1){
             alert("1 is already added as a default Addon Size");
             return false;
         }
    });
    });
</script>
<script>
    /***********For List***********/
    $(document).ready(function () {
        $("#AddonSizeIsActive,#storeId").change(function () {
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
            alert('Please select one Add-ons size to proceed.');
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