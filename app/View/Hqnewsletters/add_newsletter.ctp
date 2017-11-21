<style>
    @media (max-width: 960px) {
        .cke_reset { width:100%; }
    }
</style>
<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<?php echo $this->Html->script('ckfinder/ckfinder'); ?>
<div class="row">
    <div class="col-lg-6">
        <h3>Add Newsletter</h3>
        <hr>
        <?php echo $this->Session->flash(); ?>
    </div>
</div>
<div class="row">
    <?php echo $this->Form->create('Newsletter', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'NewsletterAdd', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6 ">
        <div class="form-group">
            <label>Store<span class="required"> * </span></label>
            <?php
            $merchantList = $mList = $this->Common->getHQStores($this->Session->read('merchantId'));
            if (!empty($merchantList)) {
                $allOption = array('All' => 'All Store','HQ' => 'HQ Only');
                $merchantList = array_replace($allOption, $merchantList);
            }
            echo $this->Form->input('Newsletter.store_id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Store'));
            ?>
        </div>
        <div class="form-group">
            <label>Subject<span class="required"> * </span></label>
            <?php
            echo $this->Form->input('name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Subject', 'label' => '', 'div' => false));
            ?>
        </div>
        <br>
        <div class="form-group">
            <label>Content Key<span class="required"> * </span></label>

            <?php
            echo $this->Form->input('content_key', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Content Key', 'label' => '', 'div' => false));
            ?>
        </div>

        <div class="form-group">
            <label>Body</label>
            <?php
            echo $this->Form->textarea('content', array('class' => 'ckeditor form-control', 'required' => 'false'));
            ?>
        </div>
        <div class="form-group">
            <label>Status<span class="required"> * </span></label>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('is_active', array(
                'type' => 'radio',
                'options' => array('0' => 'Inactive&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '1' => 'Active'),
                'default' => 1
            ));
            ?>
        </div>
        <div class="form-group">
            <label>Newsletter Type</label>
            &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            $nOptions = array('1' => 'Send Only', '2' => 'Display Only', '3' => 'Both (Send & Display)');
            echo $this->Form->input('type', array('options' => $nOptions, 'class' => 'form-control', 'label' => false, 'div' => false, 'autocomplete' => 'off'));
            ?>
        </div>
        <div id="hideOnDisplayOnly">
            <div class="form-group form_spacing">            
                <label>Frequency</label>
                &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;
                <?php
                echo $this->Form->input('NewsletterManagement.send_type', array(
                    'type' => 'radio',
                    'options' => array('1' => ' Monthly&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                        '2' => ' Weekly&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                        '3' => ' Daily&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                        '4' => ' Specific Date'),
                    'default' => 1
                ));
                ?>           
            </div>
            <div class="form-group">
                <span id="sendDate" class="sendMail">
                    <label>Date</label>
                    &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php
                    $dateOptions = array();
                    for ($i = 1; $i <= 31; $i++) {
                        $dateOptions[$i] = $i;
                    }
                    echo $this->Form->input('NewsletterManagement.send_date', array('options' => $dateOptions, 'class' => 'form-control', 'label' => false, 'div' => false, 'autocomplete' => 'off'));
                    ?>
                </span>
            </div>
            <div class="form-group">
                <span id="sendDay" class="sendMail">
                    <label>Day</label>
                    &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php
                    $dayOptions = array('1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday',
                        '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '7' => 'Sunday');
                    echo $this->Form->input('NewsletterManagement.send_day', array('options' => $dayOptions, 'class' => 'form-control', 'label' => false, 'div' => false, 'autocomplete' => 'off'));
                    ?>
                </span>
            </div>
            <div class="form-group">
                <span id="sendSepecificDate" class="sendMail">
                    <label>Specific Date<span class="required"> * </span></label>
                    <?php
                    echo $this->Form->input('NewsletterManagement.specific_date', array('type' => 'text', 'class' => 'form-control', 'div' => false, 'readonly' => true));
                    ?>
                </span>
            </div>
            <div class="form-group">            
                <span id="sendTime" class="sendMail">
                    <label>Time</label>
                    &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php
                    $timeOptions = array_merge(array("00:00:00" => "00:00"), @$timeOptions);
                    echo $this->Form->input('NewsletterManagement.timezone_send_time', array('options' => $timeOptions, 'class' => 'form-control', 'label' => false, 'div' => false, 'autocomplete' => 'off'));
                    ?>
                </span>
            </div>
        </div>
        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
        <?php //echo $this->Html->link('Cancel', "/hqnewsletters/index", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
<hr>
<div class="row">
    <div class="col-lg-12">
        <h3>Newsletter Listing</h3>
        <hr>
        <?php echo $this->Session->flash(); ?>
    </div>
    <div class="col-lg-12">
        <div class="table-responsive">
            <?php echo $this->Form->create('Newsletter', array('url' => array('controller' => 'hqnewsletters', 'action' => 'addNewsletter'), 'type' => 'post', 'id' => 'MerchantNewsletterIndexForm')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">		     
                    <?php
                    $merchantList = $mList = $this->Common->getHQStores($this->Session->read('merchantId'));
                    if (!empty($merchantList)) {
                        $allOption = array('All' => 'All Store','HQ' => 'HQ Only');
                        $merchantList = array_replace($allOption, $merchantList);
                    }
                    echo $this->Form->input('Newsletter.storeId', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'id' => 'storeId', 'type' => 'select'));
                    ?>
                </div>
                <div class="col-lg-3">
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('Newsletter.isActive', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                    ?>
                </div>
                <div class="col-lg-3">
                    <?php echo $this->Form->input('Newsletter.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Name')); ?>
                    <span class="blue">(<b>Search by:</b>Newsletter Subject name)</span>
                </div>
                <div class="col-lg-1">
                    <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>

                </div>
                <div class="col-lg-2">
                    <?php echo $this->Html->link('Clear', "/hqnewsletters/addNewsletter/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>

                <div class="col-lg-8">
                    <div class="addbutton">
                        <?php //echo $this->Form->button('Add Newsletter', array('type' => 'button', 'onclick' => "window.location.href='/hqnewsletters/addNewsletter'", 'class' => 'btn btn-default'));   ?>
                    </div>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <?php echo $this->element('show_pagination_count'); ?>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Newsletter.name', 'Subject'); ?></th>
                        <?php
                        $storeType = '';
                        if (!empty($this->request->data['Newsletter']['storeId'])) {
                            $storeType = $this->request->data['Newsletter']['storeId'];
                            if ($storeType != 'HQ') {
                                ?>
                                <th  class="th_checkbox">Store Name</th>
                                <?php
                            }
                        }
                        ?>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('Newsletter.created', 'Created'); ?></th>
                        <th  class="th_checkbox">Post to HQ Front</th>
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
                            $EncryptNewsletterID = $this->Encryption->encode($data['Newsletter']['id']);
                            ?>
                            <tr <?php echo $class; ?>>
                                <td><?php echo trim(strip_tags(wordwrap($data['Newsletter']['name'], 15, '<br/>'))); ?></td>
                                <?php
                                if (!empty($storeType) && $storeType != 'HQ') {
                                    echo "<td>" . $data['Store']['store_name'] . "</td>";
                                }
                                ?>
                                <td>
                                    <?php 
                                    echo $this->Dateform->us_format($data['Newsletter']['created']);
                                    //echo $this->Dateform->us_format($data['MerchantNewsletter']['created']);
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    if (!empty($storeType) && $storeType != 'HQ') {
                                        if (!empty($data['Newsletter']['show_to_hq_front'])) {
                                            echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Post")), array('controller' => 'hqnewsletters', 'action' => 'showStoreNewsletterToHqFront', $EncryptNewsletterID, 0), array('confirm' => 'Are you sure to remove newsletter from front site?', 'escape' => false));
                                        } else {
                                            echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Remove")), array('controller' => 'hqnewsletters', 'action' => 'showStoreNewsletterToHqFront', $EncryptNewsletterID, 1), array('confirm' => 'Are you sure to posted newsletter on front site?', 'escape' => false));
                                        }
                                    } else {
                                        if (!empty($data['Newsletter']['type']) && ($data['Newsletter']['type'] == '2' || $data['Newsletter']['type'] == '3')) {
                                            echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Post")), array('controller' => 'hqnewsletters', 'action' => 'showStoreNewsletterToHqFront', $EncryptNewsletterID, 0, 'HQ'), array('confirm' => 'Are you sure to remove newsletter from front site?', 'escape' => false));
                                        } else {
                                            echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Remove")), array('controller' => 'hqnewsletters', 'action' => 'showStoreNewsletterToHqFront', $EncryptNewsletterID, 1, 'HQ'), array('confirm' => 'Are you sure to posted newsletter on front site?', 'escape' => false));
                                        }
                                    }
                                    ?>
                                </td> 
                                <td class="text-center">
                                    <?php
                                    if ($data['Newsletter']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hqnewsletters', 'action' => 'activateNewsletter', $EncryptNewsletterID, 0), array('confirm' => 'Are you sure to Inactive Newsletter?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hqnewsletters', 'action' => 'activateNewsletter', $EncryptNewsletterID, 1), array('confirm' => 'Are you sure to Activate Newsletter?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    /*if ($data['Newsletter']['added_from'] == 2) {
                                        $action = 'editNewsletter';
                                    } else {
                                        $action = 'newsLetterEdit';
                                    }*/
                                    echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hqnewsletters', 'action' => 'editNewsletter', $EncryptNewsletterID), array('escape' => false));
                                    ?>
                                    <?php //echo " | ";  ?>
                                    <?php //echo $this->Html->link($this->Html->image("store_admin/time.png", array("alt" => "Cron", "title" => "Cron")), array('controller' => 'hqnewsletters', 'action' => 'newsletterManagement', $EncryptNewsletterID), array('escape' => false));  ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'hqnewsletters', 'action' => 'deleteNewsletter', $EncryptNewsletterID), array('confirm' => 'Are you sure to delete Newsletter?', 'escape' => false)); ?>
                                </td>
                            </tr>
                            <?php
                            $i++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="11" style="text-align: center;">
                                No record available
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php echo $this->element('pagination'); ?>
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
    </div>
</div>
<script>
    $(document).ready(function () {
//        $("#NewsletterSearch").autocomplete({
//            source: "/hqnewsletters/getMerchantNewsLetters",
//            minLength: 3
//        });
        var storeId = $('#storeId').val();
        $("#NewsletterSearch").autocomplete({
            source: "/hqnewsletters/getSearchValues?storeid=" + storeId,
            minLength: 3
        });
        $("#NewsletterIsActive,#storeId").change(function () {
            $("#MerchantNewsletterIndexForm").submit();
        });
        $("#NewsletterAdd").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Newsletter][name]": {
                    required: true,
                },
                "data[Newsletter][content_key]": {
                    required: true,
                },
                "data[Newsletter][content]": {
                    required: true,
                },
                "data[Newsletter][is_active]": {
                    required: true,
                },
                "data[NewsletterManagement][specific_date]": {
                    required: true
                },
            },
            messages: {
                "data[Newsletter][name]": {
                    required: "Please enter subject.",
                },
                "data[Newsletter][content_key]": {
                    required: "Please enter newsletter code.",
                },
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            }
        });
        $('#NewsletterName').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });
        $('#NewsletterContentKey').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });
        $('#NewsletterType').change(function () {
            if ($(this).val() == 2) {
                $("#hideOnDisplayOnly").hide();
            } else {
                $("#hideOnDisplayOnly").show();
            }
        });


    });
    $("#sendDate").show();
    $("#sendDay").hide();
    $("#sendTime").show();
    $("#sendSepecificDate").hide();
    $("input[name='data[NewsletterManagement][send_type]']").change(function () {
        if ($("input[name='data[NewsletterManagement][send_type]']:checked").val() == 1) {
            $("#sendDate").show();
            $("#sendDay").hide();
            $("#sendTime").show();
            $("#sendSepecificDate").hide();
        } else if ($("input[name='data[NewsletterManagement][send_type]']:checked").val() == 2) {
            $("#sendDate").hide();
            $("#sendDay").show();
            $("#sendTime").show();
            $("#sendSepecificDate").hide();
        } else if ($("input[name='data[NewsletterManagement][send_type]']:checked").val() == 3) {
            $("#sendDate").hide();
            $("#sendDay").hide();
            $("#sendTime").show();
            $("#sendSepecificDate").hide();
        } else if ($("input[name='data[NewsletterManagement][send_type]']:checked").val() == 4) {
            $("#sendDate").hide();
            $("#sendDay").hide();
            $("#sendTime").show();
            $("#sendSepecificDate").show();
        } else {
            $("#sendDate").show();
            $("#sendDay").hide();
            $("#sendTime").show();
            $("#sendSepecificDate").hide();
        }
    });


    $(document).ready(function () {
        $('#NewsletterManagementSpecificDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: 0
        });
        $("#NewsletterType").trigger("change");
        $("#NewsletterManagementSpecificDate").val($.datepicker.formatDate('mm-dd-yy', new Date()));
        if ($("input[name='data[NewsletterManagement][send_type]']:checked").val() == 1) {
            $("#sendDate").show();
            $("#sendDay").hide();
            $("#sendSepecificDate").hide();
            $("#sendTime").show();
        } else if ($("input[name='data[NewsletterManagement][send_type]']:checked").val() == 2) {
            $("#sendDate").hide();
            $("#sendDay").show();
            $("#sendSepecificDate").hide();
            $("#sendTime").show();
        } else if ($("input[name='data[NewsletterManagement][send_type]']:checked").val() == 3) {
            $("#sendDate").hide();
            $("#sendDay").hide();
            $("#sendTime").show();
            $("#sendSepecificDate").hide();
        } else {
            $("#sendDate").hide();
            $("#sendDay").hide();
            $("#sendTime").show();
            $("#sendSepecificDate").Show();
        }
    });
    
    
    $(document).ready(function () {
        $("#NewsletterStoreId").on('change', function(){
            var newsletterStoreId = $(this).val();
            if(newsletterStoreId == 'All')
            {
                var option = '<option value="1">Send Only</option>';
            } else {
                var option = '<option value="1">Send Only</option>';
                option += '<option value="2">Display Only</option>';
                option += '<option value="3">Both (Send &amp; Display)</option>';
            }
            $('#NewsletterType').html(option)
        });
    });
    
</script>
<script type="text/javascript">
    var url = '<?php echo HTTP_ROOT . 'js/'; ?>';
    //var url = 'http://192.168.0.5:8154/app/webroot/js/';
    CKEDITOR.env.isCompatible = true;
    var editor = CKEDITOR.replace('NewsletterContent', {
        filebrowserBrowseUrl: url + 'ckfinder/ckfinder.html',
        filebrowserImageBrowseUrl: url + 'ckfinder/ckfinder.html?type=Images',
        filebrowserFlashBrowseUrl: url + 'ckfinder/ckfinder.html?type=Flash',
        filebrowserUploadUrl: url + 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
        filebrowserImageUploadUrl: url + 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
        filebrowserFlashUploadUrl: url + 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
    });
    //CKFinder.setupCKEditor(editor, '../');
</script>