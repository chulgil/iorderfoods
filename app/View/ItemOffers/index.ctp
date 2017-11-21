
<div class="row">
    <div class="col-lg-12">
        <h3>Item Offer Listing</h3>
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">   
            <?php echo $this->Form->create('ItemOffer', array('url' => array('controller' => 'itemOffers', 'action' => 'index'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">


                <div class="col-lg-3">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('ItemOffer.is_active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                    ?>		
                </div>

                <div class="col-lg-9">		  
                    <div class="addbutton">                
<?php echo $this->Form->button('Add item offer', array('type' => 'button', 'onclick' => "window.location.href='/itemOffers/add'", 'class' => 'btn btn-default')); ?> 
                    </div>
                </div>
            </div>
<?php echo $this->Form->end(); ?>
            <div class="row">
                    <div class="col-sm-6">
                        <?php echo $this->Paginator->counter('Page {:page} of {:pages}');?> 
                    </div>
                    <div class="col-sm-6 text-right">
                        <?php echo $this->Paginator->counter('showing {:current} records out of {:count} total');?> 
                    </div>
                </div>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>	    
                        <th  class="th_checkbox">Item name</th>
                        <th  class="th_checkbox">Unit</th>
                        <th  class="th_checkbox">Start date</th>
                        <th  class="th_checkbox">End date</th>
                        <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
			 <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>
                        <th  class="th_checkbox">Action</th>

                </thead>

                <tbody class="dyntable">
                    <?php
                    if ($list) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptOfferID = $this->Encryption->encode($data['ItemOffer']['id']);
                            ?>
                            <tr <?php echo $class; ?>>	    
                                <td><?php echo $data['Item']['name']; ?></td>
                                <td><?php echo $data['ItemOffer']['unit_counter']; ?></td>
                                <td><?php echo $data['ItemOffer']['start_date']; ?></td>
                                <td><?php echo $data['ItemOffer']['end_date']; ?></td>
                                <td>
                                    <?php
                                    if ($data['ItemOffer']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'itemOffers', 'action' => 'activateOffer', $EncryptOfferID, 0), array('confirm' => 'Are you sure to Deactivate Offer?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'itemOffers', 'action' => 'activateOffer', $EncryptOfferID, 1), array('confirm' => 'Are you sure to Activate Offer?', 'escape' => false));
                                    }
                                    ?>
                                </td>

                                <td>                                    
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'itemOffers', 'action' => 'edit', $EncryptOfferID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'itemOffers', 'action' => 'deleteOffer', $EncryptOfferID), array('confirm' => 'Are you sure to delete Offer?', 'escape' => false)); ?>         
                                </td> 

                            </tr>
                            <?php $i++;
                        }
                    } else { ?>
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
                <div class="col-lg-1">   
                    LEGENDS:                        
                </div>
                <div class="col-lg-1" style=" white-space: nowrap;"><?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
                <!--div class="col-lg-2"> <?php //echo $this->Html->image("admin/category.png"). " Add Sub Category";     ?> </div-->

            </div>

        </div>
         </div>
     </div>


<?php echo $this->Html->css('pagination'); ?>


        <script>
            $(document).ready(function () {
                $("#ItemOfferIsActive").change(function () {
                    var couponId = $("#ItemOfferIsActive").val();
                    $("#AdminId").submit();
                });

            });
        </script>