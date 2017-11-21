<div class="col-lg-12">
    <h3>Customer Listing</h3>
    <?php echo $this->Session->flash(); ?> 
    <div class="table-responsive">   
        <?php
        //$encryptedStoreId = $this->Encryption->encode($storeId);
        echo $this->Form->create('Super', array('url' => array('controller' => 'super', 'action' => 'customerList'), 'id' => 'AdminId', 'type' => 'post'));
        ?>
        <div class="row padding_btm_20">
            <div class="col-sm-2">		     
                <?php
                $merchantList = $this->Common->getStoreList();
                $storeId = '';
                if ($this->Session->read('selectedStoreId')) {
                    $storeId = $this->Session->read('selectedStoreId');
                }
                echo $this->Form->input('User.store_id', array('options' => $merchantList, 'label' => false, 'class' => 'form-control', 'div' => false, 'empty' => 'Select Store'));
                ?>
            </div>
            <div class="col-sm-2">
                <?php
                echo $this->Form->input('User.from', array('label' => false, 'type' => 'text', 'class' => 'form-control', 'maxlength' => '50', 'div' => false, 'readonly' => true, 'placeholder' => 'From'));
                echo $this->Form->input('Merchant.store_id', array('type' => 'hidden', 'value' => $storeId));
                ?>
            </div>&nbsp;&nbsp;
            <div class="col-sm-2">

                <?php
                echo $this->Form->input('User.to', array('label' => false, 'type' => 'text', 'class' => 'form-control', 'maxlength' => '50', 'div' => false, 'readonly' => true, 'placeholder' => 'To'));
                ?>
            </div>
            <div class="col-sm-3">
                <?php echo $this->Form->input('User.name', array('value' => @$keyword, 'label' => false, 'div' => false, 'placeholder' => 'Customer Name', 'class' => 'form-control')); ?>
                <span class="blue">(Search by:Customer name,email)</span>
            </div>
            <div class="col-sm-2">		 
                <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                <?php echo $this->Html->link('Clear', array('controller' => 'super', 'action' => 'customerList', 'clear'), array('class' => 'btn btn-default')); ?>
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
        <table class="table table-bordered table-hover table-striped tablesorter">
            <thead>
                <tr>	    
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.fname', 'Customer Name'); ?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.email', 'Email'); ?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.phone', 'Phone'); ?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.store_id', 'Store'); ?></th>
                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.created', 'Created'); ?></th>
                    <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                        <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>
                    <th  class="th_checkbox">Order History</th>
                    <th  class="th_checkbox">Action</th>

            </thead>

            <tbody class="dyntable">
                <?php
                if ($list) {
                    $i = 0;
                    foreach ($list as $key => $data) {
                        $class = ($i % 2 == 0) ? ' class="active"' : '';
                        $UserID = $this->Encryption->encode($data['User']['id']);
                        $StoreID = $this->Encryption->encode(@$data['Store']['id']);
                        $MerchantID = $this->Encryption->encode($data['User']['merchant_id']);
                        ?>
                        <tr <?php echo $class; ?>>	    

                            <td><?php echo $data['User']['fname'] . " " . $data['User']['lname']; ?></td>
                            <td><?php echo @$data['User']['email']; ?></td>
                            <td><?php echo @$data['User']['phone']; ?></td>
                            <td><?php echo @$data['Store']['store_name']; ?></td>
                            <td><?php echo $this->Dateform->us_format($data['User']['created']); ?></td>
                            <td> <?php
                                if ($data['User']['is_active']) {
                                    echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'super', 'action' => 'activateCustomer', $UserID, 0), array('confirm' => 'Are you sure to Deactivate Customer?', 'escape' => false));
                                } else {
                                    echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'super', 'action' => 'activateCustomer', $UserID, 1), array('confirm' => 'Are you sure to Activate Customer?', 'escape' => false));
                                }
                                ?></td>
                            <td style="width:120px;"> <?php echo $this->Html->link("History", array('controller' => 'super', 'action' => 'orderHistory', $UserID, $StoreID, $MerchantID), array('escape' => false)); ?>
                            </td>
                            <td> <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'super', 'action' => 'editCustomer', $UserID), array('escape' => false)); ?>
                                <?php echo " | "; ?>
                                <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'super', 'action' => 'deleteCustomer', $UserID), array('confirm' => 'Are you sure to delete Customer?', 'escape' => false)); ?>         
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
    </div>

</div>
<?php echo $this->Html->css('pagination'); ?>
<script>
    $(document).ready(function () {
        $("#UserStoreId").change(function () {
            var transactionId = $("#UserStoreId").val
            $("#AdminId").submit();
        });


    });
    $('#UserFrom').datepicker({
        dateFormat: 'mm-dd-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '1950:2015',
    });
    $('#UserTo').datepicker({
        dateFormat: 'mm-dd-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '1950:2015',
    });


</script>
