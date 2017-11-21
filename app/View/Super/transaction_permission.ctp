<div class="row">
    <div class="col-lg-12">
        <h3>Merchant List</h3>
        <hr>
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">   
            <?php echo $this->Form->create('Merchant', array('url' => array('controller' => 'super', 'action' => 'transaction_permission'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">		     
                    <?php
                    echo $this->Form->input('Merchant.id', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $merchantList, 'empty' => 'Select Merchant'));
                    ?>
                </div>
                <div class="col-lg-3">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('Store.is_allow_transaction', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Permission Status'));
                    ?>
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
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('Merchant.name', 'Merchant Name'); ?></th>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('Store.store_name', 'Store Name'); ?></th>
                        <th  class="th_checkbox">Permission Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>
                </thead>
                <tbody class="dyntable">
                    <?php
                    if (!empty($list)) {
                        $i = 0;
                        $storecount = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptStoreID = $this->Encryption->encode($data['Store']['id']);
                            $storecount = count($data['Store']);
                            ?>
                            <tr>	    
                                <td><?php echo $data['Merchant']['name']; ?></td>
                                <td><?php echo $data['Store']['store_name']; ?></td>
                                <td class="text-center">
                                    <?php
                                    if ($data['Store']['is_allow_transaction']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'super', 'action' => 'activateStoreTransactionPermisssion', $EncryptStoreID, 0), array('confirm' => 'Are you sure to Deactivate Transaction?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'super', 'action' => 'activateStoreTransactionPermisssion', $EncryptStoreID, 1), array('confirm' => 'Are you sure to Activate Transaction?', 'escape' => false));
                                    }
                                    ?>	
                                </td>
                            </tr>
                            <?php
                            $i++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">
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
        </div>
    </div>
</div>
<?php echo $this->Html->css('pagination'); ?>
<script>
    $(document).ready(function () {
        $("#MerchantId,#StoreIsAllowTransaction").change(function () {
            $("#AdminId").submit();
        });
    });
</script>