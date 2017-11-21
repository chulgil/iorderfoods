<div class="row">
    <div class="col-lg-12">
        <h3>Store Hour Listing</h3>
        <hr>
    </div> 
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">   
            <?php echo $this->Form->create('StoreHour', array('url' => array('controller' => 'hqstores', 'action' => 'index'), 'id' => 'AdminId', 'type' => 'post')); ?>
           <div class="row padding_btm_20">
                <div class="col-lg-4">
                    <?php echo $this->Form->input('StoreHour.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Store Name','value'=>$keyword)); ?>
                    <span class="blue">(<b>Search by:</b>Store Name)</span>
                </div>
               <div class="col-lg-1">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Html->link('Clear', "/hqstores/index/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <?php   echo $this->element('show_pagination_count'); ?>
            <table class="table table-bordered table-hover table-striped tablesorter" id="preferenceListing">
                <thead>
                    <tr>
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
                            $EncryptTypeID = $this->Encryption->encode($data['Store']['id']);
                            ?>
                            <tr <?php echo $class; ?> notif-id="<?php echo $EncryptTypeID; ?>" >
                                <td><?php echo @$data['Store']['store_name']; ?></td>
                                <td>
                                    <?php
                                    if ($data['Store']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hqstores', 'action' => 'activateStore', $EncryptTypeID, 0), array('confirm' => 'Are you sure to Deactivate Preference?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hqstores', 'action' => 'activateStore', $EncryptTypeID, 1), array('confirm' => 'Are you sure to Activate Preference?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td style="width:150px;" class='sort_order'>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hqstores', 'action' => 'manageTimings', $EncryptTypeID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'hqstores', 'action' => 'deleteStore', $EncryptTypeID), array('confirm' => 'Are you sure to delete Store?', 'escape' => false)); ?>
                                    <?php ?>
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
            </table>
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
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#StoreHourSearch").autocomplete({
           source: "/hqstores/getMerchantStoreNames",
            minLength: 3,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        });
    });
</script>