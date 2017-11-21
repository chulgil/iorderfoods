<?php
$protocol = 'http';
if (isset($_SERVER['HTTPS'])) {
    if (strtoupper($_SERVER['HTTPS']) == 'ON') {
        $protocol = 'https';
    }
}
?>
<div class="row">
    <div class="col-lg-12">
        <h3>Store List</h3>
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">   
            <?php echo $this->Form->create('Coupon', array('url' => array('controller' => 'super', 'action' => 'viewStoreDetails'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">


                <div class="col-lg-3">		     

                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('Store.is_active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                    ?>
                </div>
                <div class="col-lg-3">
                    <?php echo $this->Form->input('Store.keyword', array('label' => false, 'div' => false, 'placeholder' => 'Keyword Search', 'class' => 'form-control')); ?>
                    <span class="blue">(<b>Search by:</b>Store Name, Store Domain, Merchant Name, Email, Contact no.)</span>
                </div> 
                <div class="col-lg-2">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                    <?php
                    echo $this->Html->link('Clear', "/super/viewStoreDetails", array("class" => "btn btn-default", 'escape' => false));
                    ?>
                </div>
                <div class="col-lg-4">		  
                    <div class="addbutton">                
                        <?php echo $this->Form->button('Add Store', array('type' => 'button', 'onclick' => "window.location.href='/super/addStore'", 'class' => 'btn btn-default')); ?>  
                    </div>
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
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.store_name', 'Store Name'); ?></th>
                        <th  class="th_checkbox">Store Domain</th>
                        <th  class="th_checkbox">Merchant name</th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.email', 'Email'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.phone', 'Contact no.'); ?></th>
                        <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?>
                        </th>
                        <th  class="th_checkbox">Action</th>

                </thead>

                <tbody class="dyntable">
                    <?php
                    if ($list) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptStoreID = $this->Encryption->encode($data['Store']['id']);
                            ?>
                            <tr>	    
                                <td><?php echo $data['Store']['store_name']; ?></td>
                                <td><?php echo $this->Html->link($data['Store']['store_url'], $protocol . "://" . $data['Store']['store_url'], array('target' => 'blank')); ?></td>
                                <td><?php echo $data['Merchant']['name']; ?></td>
                                <td><?php echo $data['Store']['email_id']; ?></td>
                                <td><?php echo $data['Store']['phone']; ?></td>
                                <td>
                                    <?php
                                    if ($data['Store']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'super', 'action' => 'activateStore', $EncryptStoreID, 0), array('confirm' => 'Are you sure to Deactivate Store?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'super', 'action' => 'activateStore', $EncryptStoreID, 1), array('confirm' => 'Are you sure to Activate Store?', 'escape' => false));
                                    }
                                    ?>

                                </td>
                                <td>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'super', 'action' => 'editStore', $EncryptStoreID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'super', 'action' => 'deleteStore', $EncryptStoreID), array('confirm' => 'Are you sure to delete Store?', 'escape' => false)); ?>  	
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link('<i class="fa fa-cog fa-spin"></i>', array('controller' => 'super', 'action' => 'storeConfiguration', $EncryptStoreID), array("title" => "Store Configuration", 'escape' => false)); ?>  	

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
                <!--div class="col-lg-2"> <?php //echo $this->Html->image("admin/category.png"). " Add Sub Category";                  ?> </div-->

            </div>


        </div>
        <?php echo $this->Html->css('pagination'); ?>

        <script>
            $(document).ready(function () {
//                $("#StoreIsActive").change(function () {
//                    $("#AdminId").submit();
//                });
                $("#StoreKeyword").autocomplete({
                    source: "<?php echo $this->Html->url(array('controller' => 'super', 'action' => 'getStoreSearchValues')); ?>",
                    minLength: 3,
                    select: function (event, ui) {
                        console.log(ui.item.value);
                    }
                }).autocomplete("instance")._renderItem = function (ul, item) {
                    return $("<li>")
                            .append("<div>" + item.desc + "</div>")
                            .appendTo(ul);
                };

            });
        </script>