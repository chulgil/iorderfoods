<div class="row">
    <div class="col-lg-12">
        <h3>Staff Listing</h3>
        <hr>
        <div class="table-responsive">   
            <?php echo $this->Form->create('Store', array('url' => array('controller' => 'super', 'action' => 'staffList'), 'id' => 'AdminId', 'type' => 'get')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">   
                    <?php echo $this->Form->input('keyword', array('value' => @$keyword, 'label' => false, 'div' => false, 'placeholder' => 'Keyword Search', 'class' => 'form-control', 'maxlength' => 55)); ?>
                    <span class="blue">(<b>Search by:</b>First Name, Last Name, Email)</span>
                </div>
                <div class="col-lg-4">                        
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                    <?php echo $this->Html->link('Clear', array('controller' => 'super', 'action' => 'staffList'), array('class' => 'btn btn-default')); ?>
                </div>
<!--                <div class="col-lg-4">    
                    <div class="addbutton">                
                        <?php echo $this->Form->button('Add Staff', array('type' => 'button', 'onclick' => "window.location.href='/super/manageStaff'", 'class' => 'btn btn-default')); ?>  
                    </div>
                </div>-->
            </div>
            <?php echo $this->Form->end(); ?>
            <?php
            if (!empty($list)) {
                echo @$this->element('show_pagination_count');
            }
            ?>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>	    
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('User.fname', 'First name'); ?></th>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('User.lname', 'Last name'); ?></th> 
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('User.email', 'Email'); ?></th>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('User.created', 'Created'); ?></th>
                        <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>			
                        <th  class="th_checkbox">Action</th>
                    </tr>
                </thead>
                <tbody class="dyntable">
                    <?php
                    $i = 0;
                    if (!empty($list)) {
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptStoreID = $this->Encryption->encode($data['User']['id']);
                            ?>
                            <tr <?php echo $class; ?>>	    
                                <td><?php echo $data['User']['fname']; ?></td>
                                <td><?php echo $data['User']['lname']; ?></td> 
                                <td><?php echo $data['User']['email']; ?></td>
                                <td><?php echo $this->Dateform->us_format($data['User']['created']); ?></td>
                                <td>
                                    <?php
                                    if ($data['User']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'super', 'action' => 'activateStaff', $EncryptStoreID, 0), array('confirm' => 'Are you sure to Deactivate Record?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'super', 'action' => 'activateStaff', $EncryptStoreID, 1), array('confirm' => 'Are you sure to Activate Record?', 'escape' => false));
                                    }
                                    ?>
                                </td>


                                <td>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'super', 'action' => 'manageStaff', $EncryptStoreID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'super', 'action' => 'deleteStaff', $EncryptStoreID), array('confirm' => 'Are you sure to delete Record?', 'escape' => false)); ?>
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
            <?php
            if (!empty($list)) {
                echo $this->element('pagination');
            }
            ?>
            <div class="row padding_btm_20" style="padding-top:10px">
                <div class="col-lg-1">   
                    LEGENDS:                        
                </div>
                <div class="col-lg-1" style="white-space: nowrap;"><?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                <div class="col-lg-1" style="white-space: nowrap;"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                <div class="col-lg-1" style="white-space: nowrap;"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                <div class="col-lg-1" style="white-space: nowrap;"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
            </div>
        </div>
    </div>
</div>
