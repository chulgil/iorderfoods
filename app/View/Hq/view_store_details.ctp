<?php
$protocol='http';
    if (isset($_SERVER['HTTPS'])) {
        if (strtoupper($_SERVER['HTTPS'])=='ON') {
            $protocol='https';
        }
    }
?>

<div class="row">
    <div class="col-lg-12">
        <h3>Store List</h3>
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">   
            <?php echo $this->Form->create('Coupon', array('url' => array('controller' => 'hq', 'action' => 'viewStoreDetails'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">


                <div class="col-lg-3">		     

                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('Store.is_active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                    ?>
                </div>
                <div class="col-lg-3">
                    <?php echo $this->Form->input('Store.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Store Name')); ?>
                    <span class="blue">(<b>Search by:</b>Store Name)</span>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-2">
                    <?php echo $this->Html->link('Clear', "/hq/viewStoreDetails/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
                <div class="col-lg-8">		  
                    <div class="addbutton">                
                    </div>
                </div>

            </div>
<?php echo $this->Form->end(); ?>
            <?php   echo $this->element('show_pagination_count'); ?>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>	    
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.store_name', 'Store Name'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Store.store_url', 'Domian'); ?></th>
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
                                <td><?php echo $this->Html->link($data['Store']['store_url'],$protocol."://".$data['Store']['store_url'],array('target'=>'blank')); ?>
                               <?php //echo $this->Html->link($data['Store']['store_url'],$data['Store']['store_url'], array('target'=>'_blank', 'escape' => false)); ?>
                                </td>
                                <td><?php echo $data['Store']['email_id']; ?></td>
                                <td><?php echo $data['Store']['phone']; ?></td>
                                <td>
                                    <?php
                                    if ($data['Store']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Enable")), array('controller' => 'hq', 'action' => 'activateStore', $EncryptStoreID, 0), array('confirm' => 'Are you sure you want to disable this Store?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Disable")), array('controller' => 'hq', 'action' => 'activateStore', $EncryptStoreID, 1), array('confirm' => 'Are you sure you want to enable this Store?', 'escape' => false));
                                    }
                                    ?>

                                </td>
                                <td>
        <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hq', 'action' => 'editStore', $EncryptStoreID), array('escape' => false)); ?>

                                </td>



                            </tr>
        <?php $i++;
    }
} else { ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">
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
                <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
            </div>


        </div>
    </div>
</div>
<?php echo $this->Html->css('pagination'); ?>

<script>
    $(document).ready(function () {
        $("#StoreSearch").autocomplete({
           source: "/hq/getStoreNames",
            minLength: 3,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        });
        $("#StoreIsActive").change(function () {
            $("#AdminId").submit();
        });

    });
</script>