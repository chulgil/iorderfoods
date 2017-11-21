<div class="row">
    <div class="col-lg-12">
        <h3>Item Preference Listing</h3>
        <hr/>
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">   
            <?php echo $this->Form->create('ItemType', array('url' => array('controller' => 'hqtypes', 'action' => 'typelisting'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">
                    <?php
                    $merchantList = $this->Common->getHQStores($this->Session->read('merchantId'));
                    echo $this->Form->input('ItemType.store_id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'All Store'));
                    ?>
                </div>
                <div class="col-lg-3 hidden">		     
                    <?php echo $this->Form->input('ItemType.item_id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $itemList, 'empty' => 'Select Item')); ?>		
                </div>
                <div class="col-lg-2">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('ItemType.is_active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                    ?>		
                </div>
                <div class="col-lg-2">
                    <?php echo $this->Form->input('Type.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Preference Name')); ?>
                    <span class="blue">(<b>Search by:</b>Preference Name)</span>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Html->link('Clear', "/hqtypes/typelisting/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
                <div class="col-lg-6">
                    <?php echo $this->Form->button('Upload Types', array('type' => 'button', 'onclick' => "window.location.href='/hqtypes/uploadfile'", 'class' => 'btn btn-default')); ?> 
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <?php   echo $this->element('show_pagination_count'); ?>
            <?php echo $this->Form->create('ItemType', array('url' => array('controller' => 'types', 'action' => 'deleteMultipleItemType'), 'id' => 'OrderId', 'type' => 'post')); ?>
            <table class="table table-bordered table-hover table-striped tablesorter" id="preferenceListing">
                <thead>
                    <tr>
                        <th  class="th_checkbox">Preference</th>
                        <th  class="th_checkbox">Item</th>
                        <th  class="th_checkbox">Store Name</th>
                        <th class="th_checkbox" style="width:150px;">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>
                        <?php if (!empty($this->request->data['ItemType']['item_id'])) { ?>
                            <th  class="th_checkbox">Action</th>
                        <?php } ?>
                </thead>
                <tbody id="sortable" class="dyntable" >
                    <?php
                    if ($list) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="ui-state-default active "' : 'ui-state-default';
                            $EncryptTypeID = $this->Encryption->encode($data['ItemType']['id']);
                            ?>
                            <tr <?php echo $class; ?> notif-id="<?php echo $EncryptTypeID; ?>" >
                                <td><?php echo $data['Type']['name']; ?></td>
                                <td><?php echo $data['Item']['name']; ?></td>
                                <td><?php echo $data['Store']['store_name']; ?></td>
                                <td>
                                    <?php
                                    if ($data['ItemType']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'types', 'action' => 'activatePreference', $EncryptTypeID, 0), array('confirm' => 'Are you sure to Deactivate Preference?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'types', 'action' => 'activatePreference', $EncryptTypeID, 1), array('confirm' => 'Are you sure to Activate Preference?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <?php if (!empty($this->request->data['ItemType']['item_id'])) { ?>
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
    </div>
</div>
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
        var storeId=$('#ItemTypeStoreId').val();
        $("#TypeSearch").autocomplete({
            source: "/hqtypes/getSearchValues?storeID="+storeId,
            minLength: 3,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        });
        if ($('#ItemTypeStoreId').val()) {
            $('.col-lg-3').removeClass('hidden');
        }
        $("#ItemTypeIsActive,#ItemTypeStoreId").change(function () {
            var catgoryId = $("#TypeIsActive").val
            $("#AdminId").submit();
        });

        $("#ItemTypeItemId").change(function () {
            var catgoryId = $("#TypeItemId").val
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

    }</script>

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
                url: '/types/updateitempreOrder?' + orderData,
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