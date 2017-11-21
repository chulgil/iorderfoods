<div class="row">
    <div class="col-lg-6">
        <h3>Add Sub Preference</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">
        <?php echo $this->Form->button('Upload Sub Preference', array('type' => 'button', 'onclick' => "window.location.href='/hqsubpreferences/uploadfile'", 'class' => 'btn btn-default')); ?> 
    </div>
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php //echo $this->Form->button('Upload Preference', array('type' => 'button', 'onclick' => "window.location.href='/SubPreferences/uploadfile'", 'class' => 'btn btn-default')); ?>  
        </div>
    </div>
</div>   
<hr>
<div class="row">        
    <?php echo $this->Form->create('SubPreferences', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'TypeAdd', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">
        <div class="form-group">
            <label>Store<span class="required"> * </span></label>
            <?php
            $merchantList = $mList = $this->Common->getHQStores($this->Session->read('merchantId'));
            if (!empty($merchantList)) {
                $allOption = array('All' => 'All Store');
                $merchantList = array_replace($allOption, $merchantList);
            }
            echo $this->Form->input('SubPreference.store_id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Store'));
            ?>
        </div>
        <div class="form-group">		 
            <label>Preferences<span class="required"> * </span></label>
            <div class="SubPreferenceSelect">
                <?php echo $this->Form->input('SubPreference.type_id', array('type' => 'select', 'class' => 'form-control valid serialize', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => @$storePreferences, 'empty' => 'Select Preferences')); ?>               
            </div>
        </div>
        <div class="form-group">		 
            <label>Sub Preference<span class="required"> * </span></label>  
            <?php
            echo $this->Form->input('SubPreference.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Preference', 'label' => '', 'div' => false));
            echo $this->Form->error('SubPreference.name');
            ?>
        </div>
        <div class="form-group">		 
            <label>Price<span class="required"> * </span></label> 
            <?php
            echo $this->Form->input('SubPreference.price', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Price', 'label' => '', 'div' => false));
            echo $this->Form->error('SubPreference.price');
            ?>
        </div>


        <div class="form-group">
            <label class='radioLabel'>Status<span class="required"> * </span></label>                
            <?php
            echo $this->Form->input('SubPreference.is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Active', '0' => 'In-Active'),
                'default' => 1,
                'label' => false,
                'legend' => false,
                'div' => false
            ));
            echo $this->Form->error('SubPreference.is_active');
            ?>
        </div>
        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
    </div>
    <?php echo $this->Form->end(); ?>
</div>
<hr>
<div class="row">
    <div class="col-lg-12">
        <h3>Sub-Preference Listing</h3> 
        <hr>
    </div> 
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">   
            <?php echo $this->Form->create('SubPreference', array('url' => array('controller' => 'hqsubpreferences', 'action' => 'index'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">
                    <?php echo $this->Form->input('SubPreference.storeId', array('options' => @$mList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'All Store', 'id' => 'storeId')); ?>
                </div>
                <div class="col-lg-3">		     
                    <?php echo $this->Form->input('SubPreference.typeId', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => @$types, 'empty' => 'Select Preference', 'id' => 'TypeId')); ?>		
                </div>
                <div class="col-lg-2">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('SubPreference.isActive', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => @$options, 'empty' => 'Select Status'));
                    ?>		
                </div>
                <div class="col-lg-2">
                    <?php echo $this->Form->input('SubPreference.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'SubPreference Name')); ?>
                    <span class="blue">(<b>Search by:</b>SubPreference Name)</span>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Html->link('Clear', "/hqsubpreferences/index/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <?php echo $this->element('show_pagination_count'); ?>
            <?php echo $this->Form->create('SubPreference', array('url' => array('controller' => 'hqsubpreferences', 'action' => 'deleteMultipleSubPreference'), 'id' => 'OrderId', 'type' => 'post')); ?>
            <table class="table table-bordered table-hover table-striped tablesorter" id="preferenceListing">
                <thead>
                    <tr>
                        <th  class="th_checkbox" style="float:left;border:none"><input type="checkbox" id="selectall"/></th>
                        <?php if (!empty($pagingFlag)) { ?>
                            <th  class="th_checkbox"><?php echo $this->Paginator->sort('SubPreference.name', 'Sub-Preference'); ?></th>
                            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Type.name', 'Preference'); ?></th>
                            <th  class="th_checkbox">Store Name</th>
                        <?php } else { ?>
                            <th  class="th_checkbox">Sub-Preference</th>
                            <th  class="th_checkbox">Preference</th>
                            <th  class="th_checkbox">Store Name</th>
                        <?php } ?>
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
                            $EncryptTypeID = $this->Encryption->encode($data['SubPreference']['id']);
                            ?>
                            <tr <?php echo $class; ?> notif-id="<?php echo $EncryptTypeID; ?>">
                                <td class="firstCheckbox"><?php echo $this->Form->checkbox('SubPreference.id.' . $key, array('class' => 'case', 'value' => $data['SubPreference']['id'], 'style' => 'float:left;')); ?></td>
                                <td><?php echo $data['SubPreference']['name']; ?></td>
                                <td><?php echo $data['Type']['name']; ?></td>
                                <td><?php echo $data['Store']['store_name']; ?></td>
                                <td>
                                    <?php
                                    if ($data['SubPreference']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hqsubpreferences', 'action' => 'activateSubPreference', $EncryptTypeID, 0), array('confirm' => 'Are you sure to Deactivate Sub-Preference?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hqsubpreferences', 'action' => 'activateSubPreference', $EncryptTypeID, 1), array('confirm' => 'Are you sure to Activate Sub-Preference?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td style="width:150px;" class='sort_order'>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hqsubpreferences', 'action' => 'editSubPreference', $EncryptTypeID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'hqsubpreferences', 'action' => 'deleteSubPreference', $EncryptTypeID), array('confirm' => 'Are you sure to delete Sub-Preference?', 'escape' => false)); ?>
                                    <?php
                                    echo $this->Html->image('uparrow.png', array('alt' => "Up", 'title' => "Up", 'class' => 'up_order', 'id' => 'upOrder'));
                                    echo $this->Html->image('downarrow.png', array('alt' => "Down", 'title' => "Down", 'class' => 'down_order', 'id' => 'downOrder'));
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
        </style>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#TypeAdd").validate({
            rules: {
                "data[SubPreference][store_id]": {
                    required: true},
                "data[SubPreference][type_id]": {
                    required: true
                },
                "data[SubPreference][name]": {
                    required: true
                },
                "data[SubPreference][price]": {
                    required: true,
                    number: true
                }
            },
            messages: {
                "data[SubPreference][store_id]": {
                    required: "Please select store."
                },
                "data[SubPreference][type_id]": {
                    required: "Please select preferences."
                },
                "data[SubPreference][name]": {
                    required: "Please enter Sub Preference name"
                },
                "data[SubPreference][price]": {
                    required: "Please enter price"
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
        $('#SubPreferenceStoreId').change(function () {
            var storeId = $(this).val();
            if (storeId) {
                $.ajax({
                    type: 'post',
                    url: '<?php echo $this->Html->url(array('controller' => 'hqsubpreferences', 'action' => 'getPreferences')); ?>',
                    data: {store_id: storeId},
                    success: function (response) {
                        if (response != '') {
                            $('.SubPreferenceSelect').html(response);
                        }
                    }
                });
            }
        });
    });
    /***************************FOR LIST************************************************/
    $(document).ready(function () {
        var storeId = $('#storeId').val();
//     $("#SubPreferenceSearch").autocomplete({
//         source: "/hqsubpreferences/getSearchValues?storeID="+storeId,
//        minLength: 3,
//        select: function (event, ui) {
//            console.log(ui.item.value);
//        }
//    });
        $("#SubPreferenceSearch").autocomplete({
            source: "/hqsubpreferences/getSearchValues?storeID=" + storeId,
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
        };
        $("#SubPreferenceIsActive,#storeId,#TypeId").change(function () {
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
            alert('Please select one sub-Preference to proceed.');
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
    $('img.up_order').hide();
    $('img.down_order').hide();

    if ($('#TypeId').val() == '') {
        $('img.up_order').hide();
        $('img.down_order').hide();
    } else {
        $('img.up_order').show();
        $('img.down_order').show();
    }

    $('select#TypeId').change(function () {
        $('img.up_order').show();
        $('img.down_order').show();
    });
    var notifLen = $('table#preferenceListing').find('tr').length;
    $(document).ready(function () {
        // Hide up arrow from first row 
        $('table#preferenceListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
        // Hide down arrow from last row 
        $('table#preferenceListing').find('tr').eq(notifLen - 2).find('td.sort_order').find('img.down_order').hide();

        var $up = $(".up_order")
        $up.click(function () {
            var $tr = $(this).parents("tr");
            if ($tr.index() != 0) {
                $tr.fadeOut().fadeIn();
                $tr.prev().before($tr);
            }
            updateOrder();
        });

        //down
        var $down = $(".down_order");
        var len = $down.length;
        $down.click(function () {
            var $tr = $(this).parents("tr");

            if ($tr.index() <= len) {

                $tr.fadeOut().fadeIn();
                $tr.next().after($tr);
            }
            updateOrder();
        });
    });

    function updateOrder() {
        $('img.up_order').show();
        $('img.down_order').show();

        $('table#preferenceListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
        $('table#preferenceListing').find('tr').eq(notifLen - 2).find('td.sort_order').find('img.down_order').hide();

        var orderData = getNotifOrderKeyVal();

        if (orderData) {
            $.ajax({
                url: '/SubPreferences/updateOrder?' + orderData,
                type: 'get',
                success: function () {


                }
            });
        }
    }

    function getNotifOrderKeyVal() {
        if ($('table#preferenceListing tbody').eq(0).find('tr').length > 0) {
            var orderData = '';
            $('table#preferenceListing tbody').eq(0).find('tr').each(function (i) {
                var notifId = $(this).attr('notif-id');
                orderData += notifId + '=' + (i + 1) + '&';
            });
            return orderData;
        }
        return false;
    }
</script>