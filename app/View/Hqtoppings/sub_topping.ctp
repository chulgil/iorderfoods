<?php
echo $this->Html->script('bootstrap-multiselect');
echo $this->Html->css('bootstrap-multiselect');
?>
<div class="row">
    <div class="col-lg-6">
        <h3>Add Sub Add-ons</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">
        <?php echo $this->Form->button('Upload Sub Toppings', array('type' => 'button', 'onclick' => "window.location.href='/hqtoppings/upload_sub_topping_file'", 'class' => 'btn btn-default')); ?> 
    </div>
</div>
<hr>
<div class="row">        
    <?php echo $this->Form->create('Topping', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'SizeAdd')); ?>
    <div class="col-lg-6">
        <div class="form-group">
            <label>Store<span class="required"> * </span></label>
            <?php
            $merchantList = $mList = $this->Common->getHQStores($this->Session->read('merchantId'));
            if (!empty($merchantList)) {
                $allOption = array('All' => 'All Store');
                $merchantList = array_replace($allOption, $merchantList);
            }
            echo $this->Form->input('Topping.store_id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Store'));
            ?>
        </div>
        <div class="form-group">		 
            <label>Sub Add-ons <span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('Topping.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Sub Add-ons Name', 'label' => '', 'div' => false));
            echo $this->Form->error('Topping.name');
            ?>
        </div>
        <div class="form-group">
            <label>Category<span class="required"> * </span></label>
            <span class="ToppingStoreCategory">
                <?php
                echo $this->Form->input('Category.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'required' => true, 'autocomplete' => 'off', 'options' => @$categoryList, 'empty' => 'Select Category'));
                ?>
            </span>
        </div>
        <?php
        if ($addonpost) {
            $display = "style='display:block;'";
        } else {
            $display = "style='display:none;'";
        }
        ?>
        <div class="form-group" id="addonDiv" <?php echo $display; ?> >
            <label>Add-ons<span class="required"> * </span></label>                
            <span id="addonBox" <?php echo $display; ?> >
                <?php
                echo $this->Form->input('Topping.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'multiple' => false, 'options' => @$addonList));
                ?>
            </span>
        </div>
        <div class="form-group" id="ItemsDiv" style="display:none;" >
            <label>Item<span class="required"> * </span></label>                
            <span id="ItemsBox" >               
            </span>
        </div>
        <div class="form-group">		 
            <label>Price<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('Topping.price', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Price', 'label' => '', 'div' => false));
            echo $this->Form->error('Topping.price');
            ?>
        </div>
        <div class="form-group">
            <label>No Size applicable</label>
            <?php
            echo $this->Form->checkbox('Topping.no_size');
            ?>
            <label>Default</label>
            <?php
            echo $this->Form->checkbox('Topping.defaultcheck');
            ?>
        </div>
        <div class="form-group">
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
<hr>
<div class="row">
    <div class="col-lg-12">
        <h3>Add-ons Listing</h3>
        <hr>
    </div>
    <div class="col-lg-12">
        <div class="table-responsive">   
            <?php echo $this->Form->create('Topping', array('url' => array('controller' => 'hqtoppings', 'action' => 'subTopping'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-2">		     
                    <?php echo $this->Form->input('Topping.storeId', array('options' => @$mList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'All Store', 'id' => 'storeId')); ?>
                </div>
                <?php if (!empty($itemList)) { ?>
                    <div class="col-lg-2">		     
                        <?php echo $this->Form->input('Topping.categoryId', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => @$categoryListHasSubAddons, 'empty' => 'Select Category')); ?>		
                    </div>
                <?php } ?>
                <?php if (!empty($itemList)) { ?>
                    <div class="col-lg-3">		     
                        <?php echo $this->Form->input('Topping.itemId', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => @$itemList, 'empty' => 'Select Item', "id" => "itemId")); ?>		
                    </div>
                <?php } ?>
                <?php if (!empty($addonList)) { ?>
                    <div class="col-lg-3">		     
                        <?php echo $this->Form->input('Topping.add_ons_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => @$addonList, 'empty' => 'Select Add-ons')); ?>		
                    </div>
                <?php } ?>
                <div class="col-lg-2">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('Topping.isActive', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => @$options, 'empty' => 'Select Status'));
                    ?>		
                </div>
                <div class="col-lg-3">
                    <?php echo $this->Form->input('Topping.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Sub Add-ons,Item,Category')); ?>
                    <span class="blue">(<b>Search by:</b>Sub Add-ons,Item,Category)</span>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-3">		  
                    <?php echo $this->Html->link('Clear', array('controller' => 'hqtoppings', 'action' => 'subTopping', 'clear'), array('class' => 'btn btn-default')); ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
             <?php echo $this->element('show_pagination_count'); ?>

            <table class="table table-bordered table-hover table-striped tablesorter" id="preferenceListing">
                <thead>
                    <tr>
                        <th  class="th_checkbox" style="text-align:left;border:none;"><input type="checkbox" id="selectall"/></th>
                        <?php
                        ?>
                        <th  class="th_checkbox" style="text-align:left;border:none;"><input type="checkbox" id="selectalldefault"/><span>&nbsp;Default</span></th>
                        <?php
                        ?>
                        <?php if (!empty($pagingFlag)) { ?>
                            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Topping.name', 'Sub Add-ons'); ?></th>
                            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Topping.name', 'Add-ons'); ?></th>
                            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Item.name', 'Item name'); ?></th>
                            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Category.name', 'Category name'); ?></th>
                            <th  class="th_checkbox">Store name</th>
                        <?php } else { ?>
                            <th  class="th_checkbox">Sub Add-ons</th>
                            <th  class="th_checkbox">Add-ons</th>
                            <th  class="th_checkbox">Item name</th>
                            <th  class="th_checkbox">Category name</th>
                            <th  class="th_checkbox">Store name</th>
                        <?php } ?>
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
                            $EncryptToppingID = $this->Encryption->encode($data['Topping']['id']);
                            ?>
                            <tr <?php echo $class; ?> notif-id="<?php echo $EncryptToppingID; ?>">
                                <td class="firstCheckbox"><?php echo $this->Form->checkbox('Topping.no.' . $key, array('class' => 'case', 'value' => $data['Topping']['id'], 'style' => 'float:left;')); ?></td>
                                <?php
                                $checked = false;
                                if (isset($data['ItemDefaultTopping'][0]['topping_id'])) {
                                    if ($data['Topping']['id'] == $data['ItemDefaultTopping'][0]['topping_id']) {
                                        $checked = true;
                                    }
                                }
                                ?>
                                <td><?php echo $this->Form->checkbox('Topping.id.' . $key, array('checked' => $checked, 'class' => 'checkid defaultsub', 'value' => $data['Topping']['id'], 'style' => 'float:left;')); ?></td>
                                <td><?php echo $data['Topping']['name']; ?></td>
                                <td><?php echo $data['ParentGroup']['name']; ?></td>
                                <td><?php echo $data['Item']['name']; ?></td>
                                <td><?php echo $data['Category']['name']; ?></td>
                                <td><?php echo $data['Store']['store_name']; ?></td>
                                <td>
                                    <?php
                                    if ($data['Topping']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hqtoppings', 'action' => 'activateSubTopping', $EncryptToppingID, 0), array('confirm' => 'Are you sure to Deactivate Sub Add-ons?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hqtoppings', 'action' => 'activateSubTopping', $EncryptToppingID, 1), array('confirm' => 'Are you sure to Activate Sub Add-ons?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td style="width:150px;" class='sort_order'>			
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hqtoppings', 'action' => 'editSubTopping', $EncryptToppingID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'hqtoppings', 'action' => 'deleteSubTopping', $EncryptToppingID), array('confirm' => 'Are you sure to delete Sub Add-ons?', 'escape' => false)); ?>
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
                            <td colspan="8">                       
                                <?php echo $this->Form->button('Delete Sub Add-ons', array('name' => 'subaddondelete', 'type' => 'submit', 'class' => 'btn btn-default', 'onclick' => 'return check1();')); ?> &nbsp;
                                <?php echo $this->Form->button('Set Default', array('type' => 'submit', 'class' => 'btn btn-default btn-margin', 'name' => 'set', 'onclick' => 'return check();')); ?>&nbsp;
                                <?php echo $this->Form->button('Unset Default', array('type' => 'submit', 'class' => 'btn btn-default btn-margin', 'name' => 'unset', 'onclick' => 'return check();'));
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
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
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
        //$('.multiOnly').multiselect();
        var storeId=$('#storeId').val();
        
        $("#ToppingSearch").autocomplete({
            source: "/hqtoppings/getSearchValues?storeID="+storeId+"&sub=sub",
            minLength: 3,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                    .append("<div>" + item.desc + "</div>")
                    .appendTo(ul);
        };
        
        $("#SizeAdd").validate({
            rules: {
                "data[Topping][store_id]": {
                    required: true,
                },
                "data[Topping][name]": {
                    required: true,
                },
                "data[Topping][id]": {
                    required: true,
                },
                "data[Topping][price]": {
                    required: true,
                },
                "data[Topping][item_id][]": {
                    required: true,
                    minlength: 1,
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
                "data[Topping][item_id][]": {
                    required: "Please select Item",
                },
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
        $('#ToppingStoreId').change(function () {
            var storeId = $(this).val();
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Html->url(array('controller' => 'hqtoppings', 'action' => 'getCategory')); ?>',
                data: {storeId: storeId},
                success: function (response) {
                    if (response != '') {
                        $('.ToppingStoreCategory').html(response);
                    }
                }
            });
        });
        $(document).on('change', '#CategoryId', function () {
            var catgoryId = $("#CategoryId").val();
            var storeId = $("#ToppingStoreId").val();
            if (catgoryId != "" && storeId != "") {
                $.ajax({url: "/hqtoppings/addonByCategory/" + catgoryId + "/" + storeId, success: function (result) {
                        $("#addonDiv").show();
                        $("#addonBox").show();
                        $("#addonBox").html(result);
                    }});
            }
        });
    });
</script>
<script>
    $(document).ready(function () {
        if ($(".defaultsub").length == $(".defaultsub:checked").length) {
            var o = $(".defaultsub").length;
            var p = $(".defaultsub:checked").length;
            if (o != 0 && p != 0) {
                $("#selectalldefault").prop("checked", true);
            }

        }
        $("#storeId,#itemId,#ToppingAddOnsId,#ToppingCategoryId,#ToppingIsActive").change(function () {
            //var catgoryId=$("#ToppingItemId").val();
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
                $("#selectall").prop("checked", true);
            } else {
                $("#selectall").removeAttr("checked");
            }

        });

        $("#selectalldefault").click(function () {
            var st = $("#selectalldefault").prop('checked');
            $('.defaultsub').prop('checked', st);
        });
        // if all checkbox are selected, check the selectall checkbox
        // and viceversa
        $(".defaultsub").click(function () {
            if ($(".defaultsub").length == $(".defaultsub:checked").length) {
                $("#selectalldefault").prop("checked", true);
            } else {
                $("#selectalldefault").removeAttr("checked");
            }
        });
    });
    function check() {
        var fields = $(".checkid").serializeArray();
        if (fields.length == 0) {
            alert('Please select Add-on.');
            // cancel submit
            return false;
        }
        if($('#storeId').val()==''){
            alert('Please select Store.');
            return false;
        }
        return true;
    }

    function check1()
    {
        var fields = $(".case").serializeArray();
        if (fields.length == 0)
        {
            alert('Please select one Sub Add-ons to proceed.');
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
<script type="text/javascript">
    $('img.up_order').hide();
    $('img.down_order').hide();

    if (($('#ToppingAddOnsId').val() != '') && ($('#itemId').val() != '')) {
        $('img.up_order').show();
        $('img.down_order').show();
    } else {
        $('img.up_order').hide();
        $('img.down_order').hide();
    }

    $('select.ToppingAddOnsId').change(function () {
        $('img.up_order').show();
        $('img.down_order').show();
    });

    $('select.itemId').change(function () {
        $('img.up_order').show();
        $('img.down_order').show();
    });
</script>
<script>
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
                url: '/hqtoppings/updateSubAddOnsOrder?' + orderData,
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