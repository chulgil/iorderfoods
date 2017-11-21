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
    <div class="col-lg-6">
        <div class="form-group">
            <label>Store<span class="required"> * </span></label>
            <?php
            $merchantList = $mList = $this->Common->getHQStores($this->Session->read('merchantId'));
            if (!empty($merchantList)) {
                $allOption = array('All' => 'All Store');
                $merchantList = array_replace($allOption, $merchantList);
            }
            echo $this->Form->input('Newsletter.store_id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Store'));
            ?>
        </div>
        <div class="form-group">		 
            <label>Subject<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('Newsletter.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Subject', 'label' => '', 'div' => false));
            echo $this->Form->error('Newsletter.name');
            ?>
        </div>
        <div class="form-group">		 
            <label>Content Key<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('Newsletter.content_key', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Content Key', 'label' => '', 'div' => false));
            echo $this->Form->error('Newsletter.content_key');
            ?>
        </div>
        <div class="form-group">
            <label>Body</label>
            <?php
            echo $this->Form->textarea('Newsletter.content', array('class' => 'ckeditor form-control', 'required' => 'false', 'autocomplete' => 'off'));
            echo $this->Form->error('Newsletter.content');
            ?>
        </div>
        <div class="form-group">
            <label>Status<span class="required"> * </span></label>                
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            echo $this->Form->input('Newsletter.is_active', array(
                'type' => 'radio',
                'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive'),
                'default' => 1
            ));
            echo $this->Form->error('Newsletter.is_active');
            ?>
        </div>
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
                $timeOptions = array_merge(array("00:00:00" => "00:00"), $timeOptions);
                echo $this->Form->input('NewsletterManagement.timezone_send_time', array('options' => $timeOptions, 'class' => 'form-control', 'label' => false, 'div' => false, 'autocomplete' => 'off'));
                ?>
            </span>
        </div>
        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->
<hr>
<div class="row">
    <div class="col-lg-12">
        <h3>Newsletter Listing</h3>
        <hr>
        <div class="table-responsive">   
            <?php echo $this->Form->create('Newsletter', array('url' => array('controller' => 'hqnewsletters', 'action' => 'newsLetterAdd'), 'id' => 'NewsletterId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">		     
                    <?php echo $this->Form->input('Newsletter.storeId', array('options' => @$mList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'All Store', 'id' => 'storeId')); ?>
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
                    <?php echo $this->Html->link('Clear', "/hqnewsletters/newsLetterAdd/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <?php echo $this->element('show_pagination_count'); ?>
            <?php echo $this->Form->create('Newsletter', array('url' => array('controller' => 'hqnewsletters', 'action' => 'deleteMultipleNewsletters'), 'type' => 'post')); ?>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>	
                        <th  class="th_checkbox" style="float:left;border:none;"><input type="checkbox" id="selectall"/></th>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('Newsletter.name', 'Subject'); ?></th>
                        <th  class="th_checkbox">Store Name</th>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('Newsletter.created', 'Created'); ?></th>
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
                                <td class="firstCheckbox"><?php echo $this->Form->checkbox('Newsletter.id.' . $key, array('class' => 'case', 'value' => $data['Newsletter']['id'], 'style' => 'float:left;')); ?></td>
                                <td><?php echo $data['Newsletter']['name']; ?></td>
                                <td><?php echo $data['Store']['store_name']; ?></td>
                                <td>
                                    <?php echo $this->Dateform->us_format($data['Newsletter']['created']); ?>
                                </td>
                                <td>
                                    <?php
                                    if ($data['Newsletter']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hqnewsletters', 'action' => 'activateNewsletterHq', $EncryptNewsletterID, 0), array('confirm' => 'Are you sure to Inactive Newsletter?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hqnewsletters', 'action' => 'activateNewsletterHq', $EncryptNewsletterID, 1), array('confirm' => 'Are you sure to Activate Newsletter?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hqnewsletters', 'action' => 'newsLetterEdit', $EncryptNewsletterID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/time.png", array("alt" => "Cron", "title" => "Cron")), array('controller' => 'hqnewsletters', 'action' => 'newsletterManagement', $EncryptNewsletterID), array('escape' => false)); ?>         
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'hqnewsletters', 'action' => 'deleteNewsletterHq', $EncryptNewsletterID), array('confirm' => 'Are you sure to delete Newsletter?', 'escape' => false)); ?>         
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
                <?php if ($list) { ?>
                    <tfoot>
                        <tr>
                            <td colspan="6">                       
                                <?php
                                echo $this->Form->button('Delete Newsletters', array('type' => 'submit', 'class' => 'btn btn-default', 'onclick' => 'return check();'));
                                ?>                     
                            </td>
                        </tr>
                    </tfoot>
                <?php } ?>
            </table>  
            <?php echo $this->Form->end(); ?>
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
    function check()
    {
        var fields = $(".case").serializeArray();
        if (fields.length == 0)
        {
            alert('Please select newsletter to proceed.');
            // cancel submit
            return false;
        }
        var r = confirm("Are you sure you want to delete?");
        if (r == true) {
            txt = "You pressed OK!";
        } else {
            txt = "You pressed Cancel!";
            return false;
        }
    }
    $(document).ready(function () {
        $("#selectall").click(function () {
            var st = $("#selectall").prop('checked');
            $('.case').prop('checked', st);
        });
        $(".case").click(function () {
            if ($(".case").length == $(".case:checked").length) {
                $("#selectall").attr("checked", "checked");
            } else {
                $("#selectall").removeAttr("checked");
            }
        });
        var storeId = $('#storeId').val();
        $("#NewsletterSearch").autocomplete({
            source: "/hqnewsletters/getSearchValues?storeid=" + storeId,
            minLength: 3
        });

        $("#NewsletterAdd").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            ignore: [],
            onkeyup: false,
            rules: {
                "data[Newsletter][store_id]": {
                    required: true,
                },
                "data[Newsletter][name]": {
                    required: true,
                },
                "data[Newsletter][content_key]": {
                    required: true,
                },
                "data[Newsletter][content]": {
                    required: true
                }
            },
            messages: {
                "data[Newsletter][name]": {
                    required: "Please enter subject.",
                },
                "data[Newsletter][content_key]": {
                    required: "Please enter newsletter code.",
                },
            },
            highlight: function (element, errorClass) {
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
        $("#NewsletterIsActive,#storeId").change(function () {
            $("#NewsletterId").submit();
        });

    });

</script>
<script>
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
        } else {
            $("#sendSepecificDate").show();
            $("#sendTime").show();
            $("#sendDate").hide();
            $("#sendDay").hide();
        }
    });


    $(document).ready(function () {
        $('#NewsletterManagementSpecificDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: 0
        });
        $("#NewsletterManagementSpecificDate").val($.datepicker.formatDate('mm-dd-yy', new Date()));
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
        } else {
            $("#sendSepecificDate").show();
            $("#sendTime").show();
            $("#sendDate").hide();
            $("#sendDay").hide();
        }
    });
</script>

<script type="text/javascript">
    var url = '<?php echo HTTP_ROOT . 'js/'; ?>';
    //var url = 'http://192.168.0.5:8154/app/webroot/js/';
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