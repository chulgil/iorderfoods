<style>
    /*  #sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
      #sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
      #sortable li span { position: absolute; margin-left: -1.3em; }*/
</style>
<script>
//$(function() {
//  $( "#sortable" ).sortable();
//  $( "#sortable" ).disableSelection();
//});
</script>
<div class="row">
    <div class="col-lg-10">
        <h3>Item Size Listing</h3>
        <hr />
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">   
            <?php echo $this->Form->create('ItemPrice', array('url' => array('controller' => 'sizes', 'action' => 'sizelisting'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">		     
                    <?php echo $this->Form->input('ItemPrice.item_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $itemList, 'empty' => 'Select Item')); ?>		
                </div>
                <div class="col-lg-2">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('ItemPrice.is_active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Status'));
                    ?>		
                </div>
                <div class="col-lg-4">
                    <?php echo $this->Form->input('Size.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Size')); ?>
                    <span class="blue">(<b>Search by:</b>Size Name-Item Name)</span>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Html->link('Clear', "/sizes/sizelisting/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
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
            <?php echo $this->Form->create('ItemType', array('url' => array('controller' => 'types', 'action' => 'deleteMultipleItemType'), 'id' => 'OrderId', 'type' => 'post')); ?>

            <table class="table table-bordered table-hover table-striped tablesorter" id="preferenceListing">
                <thead>
                    <tr>
                       <!--<th  class="th_checkbox" style="float:left;border:none"><input type="checkbox" id="selectall"/></th>-->
                        <th  class="th_checkbox">Size</th>
                        <th  class="th_checkbox">Item</th>
                        <th class="th_checkbox" style="width:150px;">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>
                        <?php if (!empty($this->request->data['ItemPrice']['item_id'])) { ?>
                            <th  class="th_checkbox">Action</th>
                        <?php } ?>
                </thead>

                <tbody id="sortable" class="dyntable" >
                    <?php
                    if ($list) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="ui-state-default active "' : 'ui-state-default';
                            $EncryptTypeID = $this->Encryption->encode($data['ItemPrice']['id']);
                            ?>
                            <tr <?php echo $class; ?> notif-id="<?php echo $EncryptTypeID; ?>" >
                                <td><?php echo $data['Size']['size']; ?></td>
                                <td><?php echo $data['Item']['name']; ?></td>

                                <td>
                                    <?php
                                    if ($data['ItemPrice']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'sizes', 'action' => 'activateItemSize', $EncryptTypeID, 0), array('confirm' => 'Are you sure to Deactivate Size?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'sizes', 'action' => 'activateItemSize', $EncryptTypeID, 1), array('confirm' => 'Are you sure to Activate Size?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <?php if (!empty($this->request->data['ItemPrice']['item_id'])) { ?>
                                    <td class='sort_order'>				
                                        <?php
                                        echo $this->Html->image('uparrow.png', array('alt' => "Up", 'title' => "Up", 'class' => 'up_order', 'id' => 'upOrder'));
                                        echo $this->Html->image('downarrow.png', array('alt' => "Down", 'title' => "Down", 'class' => 'down_order', 'id' => 'downOrder'));
                                        ?>
                                    </td> 
                                <?php } ?>			
                            </tr>
                            <?php
                            $i++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="9" style="text-align: center;">
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
//echo $this->Form->button('Delete Preference', array('type' => 'submit','class' => 'btn btn-default','onclick'=>'return check();'));           
                                ?>                     


                            </td>

                        </tr>
                    </tfoot> 
                <?php } ?>

            </table>
            <?php echo $this->Form->end(); ?>	
            <?php if ($pagingFlag) { ?>
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
                    <br></div>
            <?php } ?> 

        </div>
        <?php echo $this->Html->css('pagination'); ?>
        <style>
            .firstCheckbox{width:10px;}
            #btnUpdateOrder{margin-left: 1%;}
        </style>

        <script type="text/javascript">
            $('img.up_order').hide();
            $('img.down_order').hide();

            if ($('#ItemTypeItemId').val() == '') {
                $('img.up_order').hide();
                $('img.down_order').hide();
            } else {
                $('img.up_order').show();
                $('img.down_order').show();
            }

            $('select.ItemTypeItemId').change(function () {
                $('img.up_order').show();
                $('img.down_order').show();
            });
        </script>	    


        <script>
            $(document).ready(function () {
                $("#ItemPriceIsActive").change(function () {
                    var catgoryId = $("#SizeIsActive").val
                    $("#AdminId").submit();
                });
                $("#SizeSearch").autocomplete({
                    source: "<?php echo $this->Html->url(array('controller' => 'sizes', 'action' => 'getItemSizeList')); ?>",
                    minLength: 3,
                    select: function (event, ui) {
                        console.log(ui.item.value);
                    }
                }).autocomplete("instance")._renderItem = function (ul, item) {
                    return $("<li>")
                            .append("<div>" + item.desc + "</div>")
                            .appendTo(ul);
                };

                $("#ItemPriceItemId").change(function () {
                    var catgoryId = $("#SizeItemId").val
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

            function check() {
                var fields = $(".case").serializeArray();
                if (fields.length == 0) {
                    alert('Please select one Size to proceed.');
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
                        url: '/sizes/updateitempreOrder?' + orderData,
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