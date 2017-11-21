<div class="row">
    <div class="col-lg-12">
        <h3>Customer Listing</h3>
        <hr>
        <?php
        
        echo $this->Session->flash(); ?> 
        <div class="table-responsive">   
            <?php echo $this->Form->create('Customer', array('url' => array('controller' => 'hqnewsletters', 'action' => 'customerList'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-4">		     
                    <?php
                    $mList = $this->Common->getHQStores($this->Session->read('merchantId'));
                    echo $this->Form->input('User.store_id', array('options' => @$mList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Store', 'id' => 'storeId'));
                    ?>
                </div>
            </div>
            <div class="row ">
<!--                <div class="col-lg-2">		     
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('User.is_active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                    ?>		
                </div>-->
                <div class="col-lg-2">
                    <?php
                    echo $this->Form->input('User.from', array('label' => false, 'type' => 'text', 'class' => 'form-control', 'maxlength' => '50', 'div' => false, 'readonly' => true, 'placeholder' => 'From'));
                    ?>
                </div>&nbsp;&nbsp;
                <div class="col-lg-2">

                    <?php
                    echo $this->Form->input('User.to', array('label' => false, 'type' => 'text', 'class' => 'form-control', 'maxlength' => '50', 'div' => false, 'readonly' => true, 'placeholder' => 'To'));
                    ?>
                </div>
                <div class="col-lg-3">		     
                    <?php echo $this->Form->input('User.keyword', array('value' => $keyword, 'label' => false, 'div' => false, 'placeholder' => 'Keyword Search', 'class' => 'form-control')); ?>
                    <span class="blue">(<b>Search by:</b>Customer name,email,phone)</span>
                </div>
                <div class="col-lg-2">		 
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                    <?php echo $this->Html->link('Clear', array('controller' => 'hqnewsletters', 'action' => 'customerList', 'clear'), array('class' => 'btn btn-default')); ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <?php
            echo $this->Form->create('Customer', array('url' => array('controller' => 'hqnewsletters', 'action' => 'share'), 'id' => 'CustomerCustomerListForm'));
            echo $this->Form->input('storeId', array('type' => 'hidden'));
            ?>
            <div class="row oprdiv hidden">
                <div class="col-lg-12 actiondivinr"> 
                    <div class="row">
                        <div class="col-lg-3">   
                            <?php echo $this->Form->input('selectPromotionType', array('label' => false, 'div' => false, 'options' => array('1' => 'Coupons', '2' => 'Promotions', '3' => 'Newsletters', '4' => 'Offers'), 'class' => 'form-control', 'id' => 'promotionTypeId', 'empty' => '-Promotion Type-')); ?>      
                        </div>
                        <div class="col-lg-3">   
                            <?php echo $this->Form->input('selectPromotionValue', array('label' => false, 'div' => false, 'options' => array(), 'class' => 'form-control', 'id' => 'promotionValueId', 'empty' => '-Promotion Record-')); ?>      
                        </div>
                        <div class="col-lg-1">   
                            <?php echo $this->Form->button('Send', array('type' => 'submit', 'name' => 'selectedUser', 'class' => 'btn btn-success disabled shareBtn', 'id' => 'selectedUserBtnId')); ?>
                        </div>
                        <div class="col-lg-5">   
                            <?php echo $this->Form->button('Send to All', array('type' => 'submit', 'name' => 'allUser', 'class' => 'btn btn-success disabled shareBtn', 'id' => 'allUserBtnId')); ?>
                        </div>
                    </div>
                </div>
            </div><?php
            if (!empty($list)) {
                echo $this->element('show_pagination_count');
            }
            ?>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="checkall"></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.fname', 'Customer Name'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.email', 'Email'); ?></th>
                        <th class="th_checkbox">Store Name</th>
                        <th  class="th_checkbox">Phone</th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.created', 'Created'); ?></th>
                        <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>
                        <th  class="th_checkbox">Action</th>
                    </tr>
                </thead>

                <tbody class="dyntable">
                    <?php
                    if ($list) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptCustomerID = $this->Encryption->encode($data['User']['id']);
                            ?>
                            <tr <?php echo $class; ?>>
                                <td><input type="checkbox" name="checkboxes[]" class ="checkboxlist" value="<?php echo base64_encode($data['User']['id']); ?>" id="<?php echo 'chkbox_' . $data['User']['id']; ?>"></td>     
                                <td><?php echo $data['User']['fname'] . " " . $data['User']['lname']; ?></td>
                                <td><?php echo $data['User']['email']; ?></td>
                                <td><?php echo $data['Store']['store_name']; ?></td>
                                <td><?php echo $data['User']['phone']; ?></td>	
                                <td><?php echo $this->Dateform->us_format($data['User']['created']); ?></td>			
                                <td>
                                    <?php
                                    if ($data['User']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hqnewsletters', 'action' => 'activateCustomerHq', $EncryptCustomerID, 0), array('confirm' => 'Are you sure to Deactivate Customer?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hqnewsletters', 'action' => 'activateCustomerHq', $EncryptCustomerID, 1), array('confirm' => 'Are you sure to Activate Customer?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo $this->Html->link($this->Html->image("store_admin/mail_sent.png", array("alt" => "Share", "title" => "Share")), 'javascript:void(0)', array('escape' => false, 'class' => 'shareIcon', 'shareId' => $data['User']['id']));
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
            <?php echo $this->Form->end(); ?>
            <?php echo $this->element('pagination'); ?>
            <div class="row padding_btm_20" style="padding-top:10px">
                <div class="col-lg-1">   
                    LEGENDS:                        
                </div>
                <div class="col-lg-1" style=" white-space: nowrap;"><?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
         var storeId=$('#storeId').val();
        $("#UserKeyword").autocomplete({
           source: "/hqnewsletters/getUserDetails?storeid="+storeId,
            minLength: 3,
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                    .append("<div>" + item.desc + "</div>")
                    .appendTo(ul);
        };
        
        if ($('#storeId').val()) {
            $('.oprdiv').removeClass('hidden');
            $('#CustomerStoreId').val($('#storeId').val());
        }
        $('#UserFrom').datepicker({
            dateFormat: 'mm-dd-yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '2010:' + new Date().getFullYear(),
            onSelect: function (selectedDate) {
                $("#UserTo").datepicker("option", "minDate", selectedDate);
            }
        });
        $('#UserTo').datepicker({
            dateFormat: 'mm-dd-yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '2010:' + new Date().getFullYear(),
            onSelect: function (selectedDate) {
                $("#UserFrom").datepicker("option", "maxDate", selectedDate);
            }
        });
        $("#UserIsActive,#storeId").change(function () {
            $("#AdminId").submit();
        });

        jQuery('.shareBtn').click(function () {
            var selectedCounter = 0;
            var errMsg = '';
            jQuery('.dyntable input[type=checkbox]').each(function () {
                if (jQuery(this).is(':checked')) {
                    selectedCounter++;
                }
            });
            if (selectedCounter < 1)
            {
                errMsg += 'Please select the at least one User.\n';
            }
            var promotion = jQuery('#promotionTypeId').val();
            var value = jQuery('#promotionValueId').val();
            var storeId = $('#storeId').val();
            if (storeId == '') {
                errMsg += 'Please select Store.\n';
            }
            if (promotion == '') {
                errMsg += 'Please select the Promotion Type.\n';
            }
            if (value == '') {
                errMsg += 'Please select the Promotion Record.\n';
            }
            if (errMsg != '') {
                alert(errMsg);
                return false;
            }
            return confirm("Are you sure you wish to continue?");
        });
        jQuery('#promotionTypeId').change(function () {
            var promotionTypeId = jQuery(this).val();
            var storeId = $('#storeId').val();
            if (storeId == '') {
                alert('Please select store first.');
                return false;
            }
            if (promotionTypeId != '') {
                jQuery.post("/hqnewsletters/getPromotionValue", {'promotionTypeId': promotionTypeId, 'storeId': storeId}, function (data) {
                    $("#promotionValueId").html(data);
                });
            }
        });
        jQuery('#promotionValueId').change(function () {
            $class = (jQuery(this).val() == '') ? 'btn btn-default disabled' : 'btn btn-default';
            jQuery('#selectedUserBtnId').attr('class', $class);
            jQuery('#allUserBtnId').attr('class', $class);
        });
        jQuery('.shareIcon').click(function () {
            var shareId = jQuery(this).attr('shareId');
            $('#chkbox_' + shareId).attr('checked', true);
            $('#selectedUserBtnId').click();
        });
    });
</script>