<style>
    .table-responsive {
    min-height: 0.01%;
    overflow-x: hidden;
}
</style>
<div class="row">
    <div class="col-lg-12">
        <h3>Page Listing</h3>
        <hr>
        <?php 
        echo $this->Session->flash(); ?> 
        <div class="table-responsive"> 
            <div class="row">
                <div class="col-lg-4">
                    <?php echo $this->Form->create('', array('url' => array('controller' => 'hq', 'action' => 'pageList'), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'StoreList')); ?>
                    <?php
                    $merchantList = $this->Common->getHQStores($merchantId);
                    
                    echo $this->Form->input('Merchant.store_id', array('options' => $merchantList, 'class' => 'form-control', 'div' => false, 'empty' => 'Please Select Store', 'value' => $storeID));
                    ?>
                    <span class="blue">(For Store related features, select a store to proceed.)</span>
                    </div>
                    <div class="col-lg-2">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('PageList.isActive', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status','value'=>$active));
                    ?>		
                </div>
                    <div class="col-lg-3">
                    <?php echo $this->Form->input('PageList.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Page Name','value'=>$keyword)); ?>
                    <span class="blue">(<b>Search by:</b>Page Name)</span>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <div class="col-lg-2">
                    <?php echo $this->Html->link('Clear', "/hq/pageList/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
                    
                    <?php echo $this->Form->end(); ?>
                    </br>
                </div>
            </div>
        <div class="row padding_btm_20">
            <?php echo $this->Form->create('Content', array('url' => array('controller' => 'hq', 'action' => 'pageLocation'), 'id' => 'AdminId', 'type' => 'post')); ?>
                <div class="col-lg-6">		     
                    <?php
                    echo $this->Form->input('Store.navigation', array(
                        'type' => 'radio',
                        'div' => false,
                        'legend' => false,
                        'options' => array('1' => '&nbsp;&nbsp;Left Navigation&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '2' => '&nbsp;&nbsp;Top Navigation'),
                        'default' => 1
                    ));
                    echo $this->Form->error('Store.is_active');
                    ?>
                    <br/><span class="blue">(Please select navigation for front end)</span>
                </div>
                <div class="col-lg-3">		 
                    <?php echo $this->Form->button('Update', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                </div>
                <?php echo $this->Form->end(); ?>
                <div class="col-lg-3">		  
                    <div class="addbutton">                
                        <?php echo $this->Form->button('Add Page', array('type' => 'button', 'onclick' => "window.location.href='/hq/addPage'", 'class' => 'btn btn-default')); ?>  
                    </div>
                </div>
            </div>
            <?php   //echo $this->element('show_pagination_count'); ?>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>	    
                        <th  class="th_checkbox"><?php echo 'Page'; ?></th>
                        <th  class="th_checkbox"><?php echo 'Created'; ?></th>
                        <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>
                        <th  class="th_checkbox">Action</th>

                </thead>

                <tbody class="dyntable">
                    <?php
                    if (!empty($list)) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptPageID = $this->Encryption->encode($data['StoreContent']['id']);
                            ?>
                            <tr <?php echo $class; ?>>	    
                                <td><?php echo $data['StoreContent']['name']; ?></td>

                                <td>
                                    <?php echo $this->Dateform->us_format($data['StoreContent']['created']); ?>
                                </td>
                                <td>
                                    <?php
                                    if ($data['StoreContent']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hq', 'action' => 'activatePage', $EncryptPageID, 0), array('confirm' => 'Are you sure to Inactive Page?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hq', 'action' => 'activatePage', $EncryptPageID, 1), array('confirm' => 'Are you sure to Activate Page?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hq', 'action' => 'editPage', $EncryptPageID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'hq', 'action' => 'deletePage', $EncryptPageID), array('confirm' => 'Are you sure to delete Page?', 'escape' => false)); ?>         
                                </td> 

                            </tr>
                            <?php
                            $i++;
                        }
                        
                        if(!empty($termsAndPolicy)){ 
                            foreach ($termsAndPolicy as $k => $termsAndPolicyData) {
                            $EncryptTermsAndPolicyID=$this->Encryption->encode($termsAndPolicyData['TermsAndPolicy']['id']);
                            $EncryptStoreID=$this->Encryption->encode($termsAndPolicyData['TermsAndPolicy']['store_id']);
                            ?>
                            <tr>
                                <td><?php echo 'Terms And Policy'; ?></td>

                                <td>
                                    <?php
                                    echo $this->Dateform->us_format($this->Common->storeTimezone('', $termsAndPolicyData['TermsAndPolicy']['created']));
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($termsAndPolicyData['TermsAndPolicy']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hq', 'action' => 'activateTermsAndPolicy', $EncryptTermsAndPolicyID, 0), array('confirm' => 'Are you sure to Inactive Page?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hq', 'action' => 'activateTermsAndPolicy', $EncryptTermsAndPolicyID, 1), array('confirm' => 'Are you sure to Activate Page?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hq', 'action' => 'saveTermsAndPolicies', $EncryptTermsAndPolicyID,$EncryptStoreID), array('escape' => false)); ?>
                                   
                                </td>

                            </tr>
                            <?php } }
                        
                    }elseif(!empty($termsAndPolicy)){ 
                            foreach ($termsAndPolicy as $k => $termsAndPolicyData) {
                            $EncryptTermsAndPolicyID=$this->Encryption->encode($termsAndPolicyData['TermsAndPolicy']['id']);
                            $EncryptStoreID=$this->Encryption->encode($termsAndPolicyData['TermsAndPolicy']['store_id']);
                            ?>
                            <tr>
                                <td><?php echo 'Terms And Policy'; ?></td>

                                <td>
                                    <?php
                                    echo $this->Dateform->us_format($this->Common->storeTimezone('', $termsAndPolicyData['TermsAndPolicy']['created']));
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($termsAndPolicyData['TermsAndPolicy']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hq', 'action' => 'activateTermsAndPolicy', $EncryptTermsAndPolicyID, 0), array('confirm' => 'Are you sure to Inactive Page?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hq', 'action' => 'activateTermsAndPolicy', $EncryptTermsAndPolicyID, 1), array('confirm' => 'Are you sure to Activate Page?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hq', 'action' => 'saveTermsAndPolicies', $EncryptTermsAndPolicyID,$EncryptStoreID), array('escape' => false)); ?>
                                   
                                </td>

                            </tr>
                            <?php } }else {
                        ?>
                        <tr>
                            <td colspan="11" style="text-align: center;">
                                No record available
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>  


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
        <?php //echo $this->Html->css('pagination'); ?>
    </div>
</div>
<script>
    $(document).ready(function() {
        
        var storeId=$('#MerchantStoreId').val();
        $("#PageListSearch").autocomplete({
           source: "/hq/getStoreContents?storeId="+storeId,
            minLength: 3,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        });
        $("#MerchantStoreId").change(function(){
            var StoreId=$("#MerchantStoreId").val();
            //if(StoreId!="") {
                $("#StoreList").submit(); 
            //}
        });
        $("#PageListIsActive").change(function(){
                $("#StoreList").submit(); 
        });
			
    });
</script>
