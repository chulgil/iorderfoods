
<div class="row">
    <div class="col-lg-12">
        <h3>Item Listing</h3>
        <hr />
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">   
            <?php echo $this->Form->create('Item', array('url' => array('controller' => 'items', 'action' => 'index'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">		     
                    <?php echo $this->Form->input('Item.category_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $categoryList, 'empty' => 'Select Category')); ?>		
                </div>

                <div class="col-lg-2">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('Item.is_active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                    ?>		
                </div>

                <div class="col-lg-3">		     
                    <?php echo $this->Form->input('keyword', array('value' => $keyword, 'label' => false, 'div' => false, 'placeholder' => 'Keyword Search', 'class' => 'form-control')); ?>
                    <span class="blue">(<b>Search by:</b>Name, Category, Description)</span>
                </div>

                <div class="col-lg-1">		 
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
            </div>
            <div class="row padding_btm_20">
                <div class="col-lg-8">		 
                </div>
                <div class="col-lg-4">		  
                    <div class="addbutton">                
                        <?php echo $this->Form->button('Add Menu Item', array('type' => 'button', 'onclick' => "window.location.href='/items/addMenuItem'", 'class' => 'btn btn-default')); ?>
                        <?php echo $this->Form->button('Upload Menu', array('type' => 'button', 'onclick' => "window.location.href='/items/uploadfile'", 'class' => 'btn btn-default', 'style' => 'margin-top:0px;')); ?>
                        <?php echo $this->Html->link('Clear', array('controller' => 'Items', 'action' => 'index', 'clear'), array('class' => 'btn btn-default')); ?>
                    </div>
                </div>
            </div>



            <?php echo $this->Form->end(); ?>
            <?php echo $this->element('show_pagination_count'); ?>
            <?php echo $this->Form->create('Item', array('url' => array('controller' => 'items', 'action' => 'deleteMultipleItem'), 'id' => 'OrderId', 'type' => 'post')); ?>

            <table class="table table-bordered table-hover table-striped tablesorter" id="itemListing">
                <thead>
                    <tr>
                        <th  class="th_checkbox" style="float:left;border:none;"><input type="checkbox" id="selectall"/></th>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('Item.name', 'Name'); ?></th>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('Category.name', 'Category'); ?></th>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('Item.description', 'Description'); ?></th> 
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('Item.start_date', 'Start date'); ?></th>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('Item.end_date', 'End date'); ?></th>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('Item.created', 'Created'); ?></th>

                        <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>			
                        <th  class="th_checkbox">Action</th>
                    </tr>
                </thead>

                <tbody class="dyntable">
                    <?php
                    if ($list) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptItemID = $this->Encryption->encode($data['Item']['id']);
                            $iStar = '';
                            if (!empty($data['Category']['is_mandatory'])) {
                                $iStar = "<sup style='color:red;font-size:18px;top:0;'>*</sup>";
                            }
                            ?>
                            <tr <?php echo $class; ?> notif-id="<?php echo $EncryptItemID; ?>">
                                <td><?php echo $this->Form->checkbox('Item.id.' . $key, array('class' => 'case', 'value' => $data['Item']['id'], 'style' => 'float:left;')); ?></td>

                                <td><?php echo $data['Item']['name'] . $iStar; ?></td>
                                <td><?php echo $data['Category']['name']; ?></td>
                                <td><?php
                                    $len = strlen($data['Item']['description']);
                                    if ($len > 50) {
                                        $pos = strpos($data['Item']['description'], ' ', 49);
                                        echo substr($data['Item']['description'], 0, $pos);
                                    } else {
                                        echo $data['Item']['description'];
                                    }
                                    ?>
                                </td> 
                                <td><?php echo ($data['Item']['start_date'] != '0000-00-00') ? $data['Item']['start_date'] : "-"; ?></td>

                                <td><?php echo ($data['Item']['end_date'] != '0000-00-00') ? $data['Item']['end_date'] : "-"; ?></td>
                                <td><?php echo $this->Dateform->us_format($this->Common->storeTimezone('', $data['Item']['created'])); ?></td>
                                <td>
                                    <?php
                                    if ($data['Item']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'Items', 'action' => 'activateItem', $EncryptItemID, 0), array('confirm' => 'Are you sure to Deactivate Item?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'Items', 'action' => 'activateItem', $EncryptItemID, 1), array('confirm' => 'Are you sure to Activate Item?', 'escape' => false));
                                    }
                                    ?>
                                </td>


                                <td style="width:150px;" class='sort_order'>
                                    <?php //$EncryptStoreID=$this->Encryption->encode($data['User']['id']); ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'items', 'action' => 'editMenuItem', $EncryptItemID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'items', 'action' => 'deleteItem', $EncryptItemID), array('confirm' => 'Are you sure to delete Item?', 'escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php
                                    if (isset($data['ItemPrice']) && !empty($data['ItemPrice'][0]['size_id']))
                                        echo $this->Html->link('Price', array('controller' => 'Items', 'action' => 'setPrice', $EncryptItemID),array("data-toggle"=>"tooltip" ,"title"=>"Please add prices of addons/preferences!"));
                                    ?>
                                    <?php
                                    echo $this->Html->image('uparrow.png', array('alt' => "Up", 'title' => "Up", 'class' => 'up_order', 'id' => 'upOrder'));
                                    echo $this->Html->image('downarrow.png', array('alt' => "Down", 'title' => "Down", 'class' => 'down_order', 'id' => 'downOrder'));
                                    ?>

                                </td>
                            </tr>
                            <?php
                            $i++;
                        }
                    }else {
                        ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">
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
                                echo $this->Form->button('Delete Item', array('type' => 'submit', 'class' => 'btn btn-default', 'onclick' => 'return check();'));
                                ?>                     


                            </td>

                        </tr>
                    </tfoot>
                <?php } ?>

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
                <div class="col-lg-1">   
                    LEGENDS:                        
                </div>
                <div class="col-lg-1" style=" white-space: nowrap;"><?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
                <!--div class="col-lg-2"> <?php //echo $this->Html->image("admin/category.png"). " Add Sub Category";               ?> </div-->

            </div>

        </div>
        <?php echo $this->Html->css('pagination'); ?>

        <script>
            $(document).ready(function () {
                $("#ItemKeyword").autocomplete({
                    source: "<?php echo $this->Html->url(array('controller' => 'items', 'action' => 'getSearchValues')); ?>",
                    minLength: 3,
                    select: function (event, ui) {
                        console.log(ui.item.value);
                    }
                }).autocomplete("instance")._renderItem = function (ul, item) {
                    return $("<li>")
                            .append("<div>" + item.desc + "</div>")
                            .appendTo(ul);
                };
                $("#ItemCategoryId").change(function () {
                    var catgoryId = $("#ItemCategoryId").val();
                    $("#AdminId").submit();
                });

                $("#ItemIsActive").change(function () {
                    //var catgoryId=$("#ItemCategoryId").val();
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
                    alert('Please select one item to proceed.');
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

            if ($('#ItemCategoryId').val() == '') {
                $('img.up_order').hide();
                $('img.down_order').hide();
            } else {
                $('img.up_order').show();
                $('img.down_order').show();
            }

            $('select.ItemCategoryId').change(function () {
                $('img.up_order').show();
                $('img.down_order').show();
            });
        </script>
        <script>
            var notifLen = $('table#itemListing').find('tr').length;
            $(document).ready(function () {

                // Hide up arrow from first row 
                $('table#itemListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
                // Hide down arrow from last row 
                $('table#itemListing').find('tr').eq(notifLen - 2).find('td.sort_order').find('img.down_order').hide();

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

                $('table#itemListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
                $('table#itemListing').find('tr').eq(notifLen - 2).find('td.sort_order').find('img.down_order').hide();

                var orderData = getNotifOrderKeyVal();

                if (orderData) {
                    $.ajax({
                        url: '/items/updateOrder?' + orderData,
                        type: 'get',
                        success: function () {


                        }
                    });
                }
            }

            function getNotifOrderKeyVal() {
                if ($('table#itemListing tbody').eq(0).find('tr').length > 0) {
                    var orderData = '';
                    $('table#itemListing tbody').eq(0).find('tr').each(function (i) {
                        var notifId = $(this).attr('notif-id');
                        orderData += notifId + '=' + (i + 1) + '&';
                    });
                    return orderData;
                }
                return false;
            }
        </script>