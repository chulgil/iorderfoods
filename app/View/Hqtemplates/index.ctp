<div class="row">
    <div class="col-lg-7">
        <h3>Template Listing</h3>
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">   
            <?php echo $this->Form->create('Template', array('id' => 'TemplateId')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-4">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('EmailTemplate.is_active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                    ?>		
                </div>
                
                 <div class="col-lg-4">
                    <?php echo $this->Form->input('EmailTemplate.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Subject Name')); ?>
                    <span class="blue">(<b>Search by:</b>Subject name)</span>
                </div>
                <div class="col-lg-2">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-1">		  
                    <?php echo $this->Html->link('Clear', "/hqtemplates/index/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <?php   echo $this->element('show_pagination_count'); ?>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>	    
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('EmailTemplate.template_subject', 'Name'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('EmailTemplate.created', 'Date'); ?></th>
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
                            $EncryptTemplateID = $this->Encryption->encode($data['EmailTemplate']['id']);
                            ?>
                            <tr <?php echo $class; ?>>	    

                                <td>
                                    <?php echo $data['EmailTemplate']['template_subject']; ?>
                                </td>
                                <td>
                                    <?php 
                                    if(!empty($data['EmailTemplate']['store_id'])){
                                       echo $this->Dateform->us_format($this->Hq->storeTimezone(null, $data['EmailTemplate']['created'], null,  $data['EmailTemplate']['store_id'])); 
                                    }else{
                                        echo $this->Dateform->us_format($data['EmailTemplate']['created']);
                                    }
                                      
                                    
                                    //echo $this->Dateform->us_format($this->Common->storeTimezone('', $data['EmailTemplate']['created'])); 
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($data['EmailTemplate']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hqtemplates', 'action' => 'activateTemplate', $EncryptTemplateID, 0), array('confirm' => 'Are you sure to Inactive Template?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hqtemplates', 'action' => 'activateTemplate', $EncryptTemplateID, 1), array('confirm' => 'Are you sure to Activate Template?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hqtemplates', 'action' => 'editTemplate', $EncryptTemplateID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'hqtemplates', 'action' => 'deleteTemplate', $EncryptTemplateID), array('confirm' => 'Are you sure to delete Template?', 'escape' => false)); ?>         
                                </td> 

                            </tr>
                            <?php
                            $i++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td style="text-align: center;" colspan="11">
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
                <!--div class="col-lg-2"> <?php //echo $this->Html->image("admin/category.png"). " Add Sub Category";      ?> </div-->
            </div>
        </div>
        <?php echo $this->Html->css('pagination'); ?>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#EmailTemplateSearch").autocomplete({
                    source: "<?php echo $this->Html->url(array('controller' => 'hqtemplates', 'action' => 'getSearchValues')); ?>",
                    minLength: 3,
                    select: function (event, ui) {
                        console.log(ui.item.value);
                    }
                });
        $("#EmailTemplateIsActive").change(function () {
            $("#TemplateId").submit();
        });
    });
</script>