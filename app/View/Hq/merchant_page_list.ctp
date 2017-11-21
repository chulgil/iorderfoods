<style>
    .table-responsive {
        min-height: 0.01%;
        overflow-x: hidden;
    }
</style>
<div class="row">
    <div class="col-lg-12 clearfix">
        <h3>Merchant Add Page</h3> 
        <?php echo $this->Session->flash(); ?> 
        <hr>
    </div>
    <div class="col-lg-6 clearfix">
        <?php echo $this->Form->create('MerchantContent', array('url' => array('controller' => 'hq', 'action' => 'merchantAddPage'))); ?>
        <div class="form-group form_margin">		 
            <label>Name<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('MerchantContent.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Name', 'label' => '', 'div' => false));
            echo $this->Form->error('MerchantContent.name');
            ?>
        </div>
        <div class="form-group">
            <label class='radioLabel'>Page Navigation<span class="required"> * </span></label>
            <?php
            echo $this->Form->input('MerchantContent.page_position', array(
                'type' => 'radio',
                'options' => array('1' => 'Main Menu', '2' => 'Footer Menu', '3' => 'More Info'),
                'default' => 1,
                'label' => false,
                'legend' => false,
                'div' => false
            ));
            echo $this->Form->error('MerchantContent.page_position');
            ?>
        </div>
        <div class="form-group form_margin">
            <label class='radioLabel'>Status<span class="required"> * </span></label>                
            <?php
            echo $this->Form->input('MerchantContent.is_active1', array(
                'type' => 'radio',
                'options' => array('1' => 'Active', '0' => 'In-Active'),
                'default' => 1,
                'label' => false,
                'legend' => false,
                'div' => false
            ));
            echo $this->Form->error('MerchantContent.is_active');
            ?>

        </div
        <div class="form-group form_margin">
            <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>  
        </div>
        <?php echo $this->Form->end(); ?>

        <div class="col-mg-12">
            <hr>
        </div>
        <div class="col-lg-8 clearfix">
            <h3>Merchant Page Listing</h3>
            <hr>
            <?php echo $this->Session->flash(); ?> 
            <div class="table-responsive"> 
                <div class="row">
                    <?php echo $this->Form->create('MerchantContent', array('url' => array('controller' => 'hq', 'action' => 'merchantPageList'), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'StoreList')); ?>

                    <div class="col-lg-4">		     
                        <?php
                        $options = array('1' => 'Active', '0' => 'Inactive');
                        echo $this->Form->input('MerchantContent.is_active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status', 'value' => $active));
                        ?>		
                    </div>
                    <!--                    <div class="col-lg-4">
                    <?php //echo $this->Form->input('MerchantContent.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Page Name', 'value' => $keyword)); ?>
                                            <span class="blue">(<b>Search by:</b>Page Name)</span>
                                        </div>-->
                    <div class="col-lg-2">
                        <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                    </div>
                    <div class="col-lg-2">
                        <?php echo $this->Html->link('Clear', "/hq/merchantPageList/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                    </div>

                    <?php echo $this->Form->end(); ?>
                    </br>
                </div>
            </div>

            <!--            <div class="addbutton">                
            <?php //echo $this->Form->button('Add Page', array('type' => 'button', 'onclick' => "window.location.href='/hq/merchantAddPage'", 'class' => 'btn btn-default')); ?>  
                        </div>-->
        </div>
        <div class="col-lg-8">

            <?php echo $this->element('show_pagination_count'); ?>
            <table class="table table-bordered table-hover table-striped tablesorter" id="contentListing">
                <thead>
                    <tr>	    
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('MerchantContent.name', 'Page'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('MerchantContent.created', 'Created'); ?></th>
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
                            $EncryptPageID = $this->Encryption->encode($data['MerchantContent']['id']);
                            ?>
                            <tr <?php echo $class; ?> notif-id="<?php echo $EncryptPageID; ?>">
        <!--                            <tr <?php //echo $class;       ?>>	    -->
                                <td><?php echo ($data['MerchantContent']['content_key']) ? $data['MerchantContent']['content_key'] : $data['MerchantContent']['name']; ?></td>

                                <td>
                                    <?php echo $this->Dateform->us_format($data['MerchantContent']['created']); ?>
                                </td>
                                <td>
                                    <?php
                                    if ($data['MerchantContent']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hq', 'action' => 'merchantActivatePage', $EncryptPageID, 0), array('confirm' => 'Are you sure to Inactive Page?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hq', 'action' => 'merchantActivatePage', $EncryptPageID, 1), array('confirm' => 'Are you sure to Activate Page?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td style="width:150px;" class='sort_order'>
                                    <?php //if (($data['MerchantContent']['name'] != 'LOCATIONS') && ($data['MerchantContent']['name'] != 'GALLERY') && ($data['MerchantContent']['name'] != 'NEWSLETTER') && ($data['MerchantContent']['name'] != 'PROMOTIONS')) { ?>
                                        <?php
                                        echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hqconfigurations', 'action' => 'htmlModule', $EncryptPageID), array('escape' => false));
                                    //} else {
                                        //echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                    //}
                                    ?>
                                    <?php //if ($data['MerchantContent']['name'] != 'Home') { ?>
                                    <?php $staticMenu = array('LOCATIONS', 'GALLERY', 'NEWSLETTER', 'PROMOTIONS','HOME');
                                    if (!in_array($data['MerchantContent']['name'], $staticMenu)) { ?>
                                        <?php echo " | "; ?>
                                        <?php
                                        echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'hq', 'action' => 'merchantDeletePage', $EncryptPageID), array('confirm' => 'Are you sure to delete Page?', 'escape' => false));
                                        echo " | ";
                                    } else {
                                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                    }
                                    ?> 
                                    <?php
                                    echo $this->Html->image('uparrow.png', array('alt' => "Up", 'title' => "Up", 'class' => 'up_order', 'id' => 'upOrder'));
                                    echo $this->Html->image('downarrow.png', array('alt' => "Down", 'title' => "Down", 'class' => 'down_order', 'id' => 'downOrder'));
                                    ?>
                                    <?php //echo $this->Html->link('<i class="fa fa-level-up"></i>', array('controller' => 'hqconfigurations', 'action' => 'htmlModule', $EncryptPageID), array('escape' => false,'title'=>'Add Content'));  ?>        
                                </td> 

                            </tr>
                            <?php
                            $i++;
                        }
                        if (!empty($termsAndPolicy)) {
                            $EncryptTermsAndPolicyID = $this->Encryption->encode($termsAndPolicy['TermsAndPolicy']['id']);
                            $EncryptMerchantID = $this->Encryption->encode($termsAndPolicy['TermsAndPolicy']['merchant_id']);
                            ?>
                            <tr>
                                <td><?php echo 'Terms And Policy'; ?></td>

                                <td>
                                    <?php
                                    echo $this->Dateform->us_format($this->Common->storeTimezone('', $termsAndPolicy['TermsAndPolicy']['created']));
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($termsAndPolicy['TermsAndPolicy']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hq', 'action' => 'activateTermsAndPolicy', $EncryptTermsAndPolicyID, 0, $EncryptMerchantID), array('confirm' => 'Are you sure to Inactive Page?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hq', 'action' => 'activateTermsAndPolicy', $EncryptTermsAndPolicyID, 1, $EncryptMerchantID), array('confirm' => 'Are you sure to Activate Page?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hqconfigurations', 'action' => 'saveTermsAndPolicies', $EncryptTermsAndPolicyID), array('escape' => false)); ?>

                                </td>

                            </tr>
                            <?php
                        }
                    } elseif (!empty($termsAndPolicy)) {
                        $EncryptTermsAndPolicyID = $this->Encryption->encode($termsAndPolicy['TermsAndPolicy']['id']);
                        $EncryptMerchantID = $this->Encryption->encode($termsAndPolicy['TermsAndPolicy']['merchant_id']);
                        ?>
                        <tr>
                            <td><?php echo 'Terms And Policy'; ?></td>

                            <td>
                                <?php
                                echo $this->Dateform->us_format($this->Common->storeTimezone('', $termsAndPolicy['TermsAndPolicy']['created']));
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($termsAndPolicy['TermsAndPolicy']['is_active']) {
                                    echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hq', 'action' => 'activateTermsAndPolicy', $EncryptTermsAndPolicyID, 0, $EncryptMerchantID), array('confirm' => 'Are you sure to Inactive Page?', 'escape' => false));
                                } else {
                                    echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hq', 'action' => 'activateTermsAndPolicy', $EncryptTermsAndPolicyID, 1, $EncryptMerchantID), array('confirm' => 'Are you sure to Activate Page?', 'escape' => false));
                                }
                                ?>
                            </td>
                            <td>
                                <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hqconfigurations', 'action' => 'saveTermsAndPolicies', $EncryptTermsAndPolicyID), array('escape' => false)); ?>

                            </td>

                        </tr>
                    <?php } else {
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
                <!--div class="col-lg-2"> <?php //echo $this->Html->image("admin/category.png"). " Add Sub Category";                                                           ?> </div-->

            </div>
        </div>
    </div>
    <?php echo $this->Html->css('pagination'); ?>

    <script>
        $(document).ready(function () {
            $("#MerchantContentMerchantPageListForm").validate({
                debug: false,
                errorClass: "error",
                errorElement: 'span',
                onkeyup: false,
                rules: {
                    "data[MerchantContent][name]": {
                        required: true,
                    },
                    "data[MerchantContent][content_key]": {
                        required: true,
                    },
                },
                messages: {
                    "data[MerchantContent][name]": {
                        required: "Please enter page name",
                    },
                    "data[MerchantContent][content_key]": {
                        required: "Please enter content key",
                    },
                }, highlight: function (element, errorClass) {
                    $(element).removeClass(errorClass);
                },
            });
            var storeId = $('#MerchantStoreId').val();
            $("#MerchantContentSearch").autocomplete({
                source: "/hq/getMerchantContents",
                minLength: 3,
                select: function (event, ui) {
                    console.log(ui.item.value);
                }
            });
            $("#MerchantContentIsActive").change(function () {
                $("#StoreList").submit();
            });

        });
    </script>

    <script>
        var notifLen = $('table#contentListing').find('tr').length;
        $(document).ready(function () {
            // Hide up arrow from first row 
            $('table#contentListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
            // Hide down arrow from last row 
            $('table#contentListing').find('tr').eq(notifLen - 2).find('td.sort_order').find('img.down_order').hide();
            var $up = $(".up_order")
            $up.click(function () {
                var $tr = $(this).parents("tr");
                if ($tr.index() != 0) {
                    $tr.fadeOut().fadeIn();
                    $tr.prev().before($tr);

                }
                updateOrder();
            });
            //down
            var $down = $(".down_order");
            var len = $down.length;
            $down.click(function () {
                var $tr = $(this).parents("tr");

                if ($tr.index() <= len) {

                    $tr.fadeOut().fadeIn();
                    $tr.next().after($tr);
                }
                updateOrder();
            });
        });

        function updateOrder() {
            $('img.up_order').show();
            $('img.down_order').show();

            $('table#contentListing').find('tr').eq(1).find('td.sort_order').find('img.up_order').hide();
            $('table#contentListing').find('tr').eq(notifLen - 2).find('td.sort_order').find('img.down_order').hide();

            var orderData = getNotifOrderKeyVal();
            if (orderData) {
                $.ajax({
                    url: '/hq/updateContentListing?' + orderData,
                    type: 'get',
                    success: function () {
                    }
                });
            }
        }

        function getNotifOrderKeyVal() {
            if ($('table#contentListing tbody').eq(0).find('tr').length > 0) {
                var orderData = '';
                $('table#contentListing tbody').eq(0).find('tr').each(function (i) {
                    var notifId = $(this).attr('notif-id');
                    orderData += notifId + '=' + (i + 1) + '&';
                });
                return orderData;
            }
            return false;
        }
    </script>
