<div class="row">
    
    <div class="col-lg-12">
        <h3>Inquiry Message Listing</h3>
        <hr>
        <?php echo $this->Session->flash(); ?> 
        <div class="table-responsive">
            <?php echo $this->Form->create('ContactUs', array('url' => array('controller' => 'hqtemplates', 'action' => 'enquiryMessages'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">
                    <?php echo $this->Form->input('ContactUs.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Name-email-message','value'=>$keyword)); ?>
                    <span class="blue">(<b>Search by:</b>Name-email-message)</span>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-2">
                    <?php echo $this->Html->link('Clear', "/hqtemplates/enquiryMessages/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <?php   echo $this->element('show_pagination_count'); ?>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>	    
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('ContactUs.name', 'Name'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('ContactUs.email', 'Email'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('ContactUs.phone', 'phone'); ?></th>
                        <th  class="th_checkbox">Message</th>
                        <th  class="th_checkbox">Created</th>
                        <th  class="th_checkbox">Action</th>
                </thead>
                <tbody class="dyntable">
                    <?php
                    if ($enquiryMessages) {
                        $i = 0;
                        foreach ($enquiryMessages as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptContactUsID = $this->Encryption->encode($data['ContactUs']['id']);
                            ?>
                            <tr <?php echo $class; ?>>	    
                                <td>
                                    <?php echo $data['ContactUs']['name']; ?>
                                </td>
                                <td>
                                    <?php echo $data['ContactUs']['email']; ?>
                                </td>
                                <td>
                                    <?php echo $data['ContactUs']['phone']; ?>
                                </td>
                                <td>
                                    <?php echo $data['ContactUs']['message']; ?>
                                </td>
                                <td>
                                    <?php echo $this->Dateform->us_format($data['ContactUs']['created']); ?>
                                </td>
<!--                                <td>
                                    <?php
                                    if ($data['ContactUs']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hqtemplates', 'action' => 'activateTemplate', $EncryptContactUsID, 0), array('confirm' => 'Are you sure to Inactive Template?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hqtemplates', 'action' => 'activateTemplate', $EncryptContactUsID, 1), array('confirm' => 'Are you sure to Activate Template?', 'escape' => false));
                                    }
                                    ?>
                                </td>-->
                                <td>
                                    <?php //echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Reply")), array('controller' => 'hqtemplates', 'action' => 'replyMessage', $EncryptContactUsID), array('escape' => false)); ?>
                                     <?php 
                                     if ($data['ContactUs']['flag'] == 0) {
                                                echo $this->Html->link('<i class="fa fa-mail-reply" style="font-size:15px" title="Reply"></i>', array('controller' => 'hqtemplates', 'action' => 'replyMessage', $EncryptContactUsID), array('escape' => false));
                                            } else { ?>
                                     <i class="fa fa-inbox" style="font-size:15px" title="Replied"></i>
                                       <?php     }
                                            ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'hqtemplates', 'action' => 'deleteMessage', $EncryptContactUsID), array('confirm' => 'Are you sure to delete message?', 'escape' => false)); ?>         
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
                <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/edit.png") . " Reply"; ?> </div>
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
         $("#ContactUsSearch").autocomplete({
                    source: "/hqtemplates/getEnquiryMessages",
                    minLength: 3,
                    select: function (event, ui) {
                        console.log(ui.item.value);
                    }
                }).autocomplete("instance")._renderItem = function (ul, item) {
                    return $("<li>")
                            .append("<div>" + item.desc + "</div>")
                            .appendTo(ul);
                };
        $("#EmailTemplateIsActive").change(function () {
            $("#TemplateId").submit();
        });
    });
</script>