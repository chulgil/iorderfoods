<div class="row">
    <div class="col-lg-12">
        <h3>Merchant List</h3>
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">   
            <?php echo $this->Form->create('Coupon', array('url' => array('controller' => 'super', 'action' => 'viewMerchantDetails'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('Merchant.is_active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                    ?>
                </div>
                <div class="col-lg-3">
                    <?php echo $this->Form->input('Merchant.keyword', array('label' => false, 'div' => false, 'placeholder' => 'Keyword Search', 'class' => 'form-control')); ?>
                    <span class="blue">(<b>Search by:</b>Merchant Name,Email,Contact no.)</span>
                </div> 
                <div class="col-lg-2">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                    <?php echo $this->Html->link('Clear', "/super/viewMerchantDetails", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
                <div class="col-lg-4">		  
                    <div class="addbutton">                
                        <?php echo $this->Form->button('Add Merchant', array('type' => 'button', 'onclick' => "window.location.href='/super/addMerchant'", 'class' => 'btn btn-default')); ?>  
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
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Merchant.name', 'Merchant Name'); ?></th>
                        <th  class="th_checkbox">Total Store's</th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Merchant.email', 'Email'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Merchant.phone', 'Contact no'); ?></th>			
                        <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>
                        <th  class="th_checkbox">Action</th>

                </thead>

                <tbody class="dyntable">
                    <?php
                    if ($list) {
                        $i = 0;
                        $storecount = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptMerchantID = $this->Encryption->encode($data['Merchant']['id']);
                            $storecount = count($data['Store']);
                            ?>
                            <tr>	    
                                <td><?php echo ($storecount) ? $this->Html->link($data['Merchant']['name'], array('controller' => 'super', 'action' => 'storeList', $EncryptMerchantID)) : $data['Merchant']['name']; ?></td>
                                <td><?php echo ($storecount) ? $this->Html->link($storecount, array('controller' => 'super', 'action' => 'storeList', $EncryptMerchantID)) : $storecount; ?></td>
                                <td><?php echo $data['Merchant']['email']; ?></td>
                                <td><?php echo $data['Merchant']['phone']; ?></td>
                                <td>
                                    <?php
                                    if ($data['Merchant']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'super', 'action' => 'activateMerchant', $EncryptMerchantID, 0), array('confirm' => 'Are you sure to Deactivate Merchant?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'super', 'action' => 'activateMerchant', $EncryptMerchantID, 1), array('confirm' => 'Are you sure to Activate Merchant?', 'escape' => false));
                                    }
                                    ?>	

                                </td>
                                <td>

                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'super', 'action' => 'editMerchant', $EncryptMerchantID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'super', 'action' => 'deleteMerchant', $EncryptMerchantID), array('confirm' => 'Are you sure to delete Merchant?', 'escape' => false)); ?>  	
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


        </div>
        <?php echo $this->Html->css('pagination'); ?>

        <script>
            $(document).ready(function () {
//                $("#MerchantIsActive").change(function () {
//                    $("#AdminId").submit();
//                });
                $("#MerchantKeyword").autocomplete({
                    source: "<?php echo $this->Html->url(array('controller' => 'super', 'action' => 'getMerchantSearchValues')); ?>",
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