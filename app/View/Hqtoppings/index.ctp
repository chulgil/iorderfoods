<?php
echo $this->Html->script('bootstrap-multiselect');
echo $this->Html->css('bootstrap-multiselect');
?>
<div class="row">
    <div class="col-lg-6">
        <h3>Add Add-on</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">
        <?php echo $this->Form->button('Upload Toppings', array('type' => 'button', 'onclick' => "window.location.href='/hqtoppings/uploadfile'", 'class' => 'btn btn-default')); ?> 
    </div>
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php //echo $this->Form->button('Upload Add-on', array('type' => 'button', 'onclick' => "window.location.href='/toppings/uploadfile'", 'class' => 'btn btn-default')); ?>  
        </div>
    </div>
</div>   
<hr>
<div class="row">        
    <?php echo $this->Form->create('Toppings', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'addTopping')); ?>
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
            <label>Add-on Name<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('Topping.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Add-on', 'label' => '', 'div' => false));
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
        if (!empty($itempost)) {
            $display = "style='display:block;'";
        } else {
            $display = "style='display:none;'";
        }
        ?>
        <div class="form-group form_spacing" id="ItemsDiv" <?php echo $display; ?> >
            <label>Items<span class="required"> * </span></label>                
            <span id="ItemsBox" <?php echo $display; ?> >
                <?php
                echo $this->Form->input('Topping.item_id', array('type' => 'select', 'class' => 'form-control valid multiOnly', 'label' => '', 'div' => false, 'autocomplete' => 'off', 'multiple' => true, 'options' => @$itemList));
                ?>
            </span>
        </div>
        <div class="form-group form_margin">
            <label>Min Sub-Add-on<span class="required"> * </span></label>
            <?php
            $options = range(0, 10);
            echo $this->Form->input('Topping.min_value', array('options' => $options, 'type' => 'select', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false));
            echo $this->Form->error('Topping.min_value');
            ?>
        </div>
        <div class="form-group form_margin">
            <label>Max Sub-Add-on<span class="required"> * </span></label>  
            <?php
            echo $this->Form->input('Topping.max_value', array('options' => $options, 'type' => 'select', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false));
            echo $this->Form->error('Topping.max_value');
            ?>
        </div>
        <div class="form-group form_spacing">		 
            <label>Status<span class="required"> * </span></label><span>&nbsp;&nbsp;</span>                  
            <?php
            $value = 1;
            if (isset($this->request->data['Topping']['is_active'])) {
                $value = $this->request->data['Topping']['is_active'];
            }
            echo $this->Form->input('Topping.is_active', array('type' => 'radio', 'separator' => '&nbsp;&nbsp;&nbsp;&nbsp;', 'value' => @$value, 'options' => array('1' => 'Active', '0' => 'Inactive')));
            ?>		 
        </div>          
        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Html->link('Cancel', "/hqtoppings/index/", array("class" => "btn btn-default", 'escape' => false)); ?>
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
            <?php echo $this->Form->create('Topping', array('url' => array('controller' => 'hqtoppings', 'action' => 'index'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row">
                <div class="col-lg-2">		     
                    <?php echo $this->Form->input('Topping.storeId', array('options' => @$mList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'All Store', 'id' => 'storeId')); ?>
                </div>
                <?php if (!empty($itemList)) { ?>
                    <div class="col-lg-2 hidden">		     
                        <?php echo $this->Form->input('Topping.itemId', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => @$itemList, 'empty' => 'Select Item', 'id' => 'itemId')); ?>		
                    </div>
                <?php } ?>
                <div class="col-lg-2">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('Topping.isActive', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                    ?>		
                </div>

                <div class="col-lg-3">		     
                    <?php echo $this->Form->input('keyword', array('value' => @$keyword, 'label' => false, 'div' => false, 'placeholder' => 'Keyword Search', 'class' => 'form-control')); ?>
                    <span class="blue">(<b>Search by:</b>Add-on name,Item name)</span>
                </div>

                <div class="col-lg-1">		 
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Html->link('Clear', "/hqtoppings/index/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
             <?php echo $this->element('show_pagination_count'); ?>
            <table class="table table-bordered table-hover table-striped tablesorter" id="preferenceListing">
                <thead>
                    <tr>
                        <th  class="th_checkbox" style="float:left;border:none;"><input type="checkbox" id="selectall"/></th>			
                        <?php if (!empty($pagingFlag)) { ?>
                            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Topping.name', 'Add-on name'); ?></th>
                            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Item.name', 'Item name'); ?></th>
                            <th  class="th_checkbox"><?php echo $this->Paginator->sort('Category.name', 'Category name'); ?></th>
                            <th  class="th_checkbox">Store Name</th>
                        <?php } else { ?>
                            <th  class="th_checkbox">Add-on name</th>
                            <th  class="th_checkbox">Item name</th>
                            <th  class="th_checkbox">Category name</th>
                            <th  class="th_checkbox">Store Name</th>
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
                                <td><?php echo $data['Topping']['name']; ?></td>
                                <td><?php echo $data['Item']['name']; ?></td>
                                <td><?php echo $data['Category']['name']; ?></td>
                                <td><?php echo $data['Store']['store_name']; ?></td>
                                <td>
                                    <?php
                                    if ($data['Topping']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hqtoppings', 'action' => 'activateTopping', $EncryptToppingID, 0), array('confirm' => 'Are you sure to Deactivate Add-on?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hqtoppings', 'action' => 'activateTopping', $EncryptToppingID, 1), array('confirm' => 'Are you sure to Activate Add-on?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td style="width:150px;" class='sort_order'>
                                    <?php //$EncryptStoreID=$this->Encryption->encode($data['User']['id']); ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hqtoppings', 'action' => 'editTopping', $EncryptToppingID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'hqtoppings', 'action' => 'deleteTopping', $EncryptToppingID), array('confirm' => 'Are you sure to delete Addon?', 'escape' => false)); ?>
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
                            <td colspan="8" style="text-align: center;">
                                No record available
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
                <?php if (!empty($list)) { ?>
                    <tfoot>
                        <tr>
                            <td colspan="6">                       
                                <?php echo $this->Form->button('Delete Add-ons', array('type' => 'submit', 'class' => 'btn btn-default', 'onclick' => 'return check1();')); ?>                     
                            </td>
                        </tr>
                    </tfoot>
                <?php } ?>
                <?php echo $this->Form->end(); ?>
            </table>
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
<?php echo $this->Html->css('pagination'); ?>
<style>
    .firstCheckbox{width:10px;}
</style>

<script>
    $(document).ready(function () {
        $("#ToppingMinValue").change(function () {
            var start = $(this).val();
            var end = 10;
            var that = $("#ToppingMaxValue");
            //var array = new Array();
            that.html('');
            for (var i = start; i <= end; i++)
            {
                that.append("<option value=" + i + ">" + i + "</option>");

            }
        });
        var storeId = $('#storeId').val();

        $("#ToppingKeyword").autocomplete({
            source: "/hqtoppings/getSearchValues?storeID=" + storeId,
            minLength: 3,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                    .append("<div>" + item.desc + "</div>")
                    .appendTo(ul);
        };
        if ($('#storeId').val()) {
            $('.col-lg-2').removeClass('hidden');
        }
        $('.multiOnly').multiselect();
        $("#addTopping").validate({
            rules: {
                "data[Topping][store_id]": {
                    required: true,
                },
                "data[Topping][name]": {
                    required: true,
                },
                "data[Topping][category_id]": {
                    required: true,
                },
                "data[Topping][item_id][]": {
                    required: true,
                }
            },
            messages: {
                "data[Topping][store_id]": {
                    required: "Please select store.",
                },
                "data[Topping][name]": {
                    required: "Please enter Add-on name",
                },
                "data[Topping][category_id]": {
                    required: "Please select category",
                },
                "data[Topping][item_id][]": {
                    required: "Please select item",
                }
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
        $(document).on('click', '#CategoryId', function () {
            var catgoryId = $("#CategoryId").val();
            var storeId = $("#ToppingStoreId").val();
            if (catgoryId && storeId) {
                $.ajax({url: "/hqtoppings/itemsByCategory/" + catgoryId + "/" + storeId, success: function (result) {
                        $("#ItemsDiv").show();
                        $("#ItemsBox").show();
                        $("#ItemsBox").html(result);
                    }});
            }
        });

        $('#ToppingPrice').keyup(function () {
            this.value = this.value.replace(/[^0-9.,]/g, '');
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
        /*********************Addons List*******************************/
        $("#itemId,#ToppingIsActive,#storeId").change(function () {
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
    function check1()
    {
        var fields = $(".case").serializeArray();
        if (fields.length == 0)
        {
            alert('Please select one Add-ons to proceed.');
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

    if ($('#itemId').val() == '') {
        $('img.up_order').hide();
        $('img.down_order').hide();
    } else {
        $('img.up_order').show();
        $('img.down_order').show();
    }

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
                url: '/hqtoppings/updateAddOnsOrder?' + orderData,
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
