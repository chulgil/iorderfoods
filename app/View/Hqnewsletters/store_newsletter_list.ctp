<style>
    @media (max-width: 960px) {
        .cke_reset { width:100%; }
    }
</style>
<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<?php echo $this->Html->script('ckfinder/ckfinder'); ?>
<div class="row">
    <div class="col-lg-6">
        <h3>Newsletter Listing</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
</div>   

<hr>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">   
            <?php echo $this->Form->create('Newsletter', array('url' => array('controller' => 'hqnewsletters', 'action' => 'storeNewsletterList'), 'id' => 'NewsletterId', 'type' => 'post')); ?>
            <div class="row padding_btm_20">
                <div class="col-lg-3">		     
                    <?php
                    $mList = $this->Common->getHQStores($this->Session->read('merchantId'));
                    echo $this->Form->input('Newsletter.storeId', array('options' => @$mList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'All Store', 'id' => 'storeId'));
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
                    <?php echo $this->Html->link('Clear', "/hqnewsletters/storeNewsletterList/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <?php echo $this->element('show_pagination_count'); ?>
            <?php echo $this->Form->create('Newsletter', array('url' => array('controller' => 'hqnewsletters', 'action' => 'deleteMultipleNewsletters'), 'type' => 'post')); ?>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>	
                        <!--th  class="th_checkbox" style="float:left;border:none;"><input type="checkbox" id="selectall"/></th-->
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('Newsletter.name', 'Subject'); ?></th>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('Store.store_name', 'Store Name'); ?></th>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('Newsletter.created', 'Created'); ?></th>
<!--                        <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                            <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>-->
                        <th  class="th_checkbox">Post to HQ Front</th>

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
                                <!--td class="firstCheckbox"><?php echo $this->Form->checkbox('Newsletter.id.' . $key, array('class' => 'case', 'value' => $data['Newsletter']['id'], 'style' => 'float:left;')); ?></td-->
                                <td><?php echo $data['Newsletter']['name']; ?></td>
                                <td><?php echo $data['Store']['store_name']; ?></td>
                                <td>
                                    <?php echo $this->Dateform->us_format($data['Newsletter']['created']); ?>
                                </td>
<!--                                <td class="text-center">
                                    <?php
//                                    if ($data['Newsletter']['is_active']) {
//                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hqnewsletters', 'action' => 'activateNewsletterHq', $EncryptNewsletterID, 0), array('confirm' => 'Are you sure to Inactive Newsletter?', 'escape' => false));
//                                    } else {
//                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hqnewsletters', 'action' => 'activateNewsletterHq', $EncryptNewsletterID, 1), array('confirm' => 'Are you sure to Activate Newsletter?', 'escape' => false));
//                                    }
                                    ?>
                                </td>-->
                                <td class="text-center">
                                    <?php
                                    if (!empty($data['Newsletter']['show_to_hq_front'])) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Post")), array('controller' => 'hqnewsletters', 'action' => 'showStoreNewsletterToHqFront', $EncryptNewsletterID, 0), array('confirm' => 'Are you sure to remove newsletter from front site?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Remove")), array('controller' => 'hqnewsletters', 'action' => 'showStoreNewsletterToHqFront', $EncryptNewsletterID, 1), array('confirm' => 'Are you sure to posted newsletter on front site?', 'escape' => false));
                                    }
                                    ?>
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
    $(document).ready(function () {
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
    $("input[name='data[NewsletterManagement][send_type]']").change(function () {
        if ($("input[name='data[NewsletterManagement][send_type]']:checked").val() == 1) {
            $("#sendDate").show();
            $("#sendDay").hide();
            $("#sendTime").show();
        } else if ($("input[name='data[NewsletterManagement][send_type]']:checked").val() == 2) {
            $("#sendDate").hide();
            $("#sendDay").show();
            $("#sendTime").show();
        } else {
            $("#sendDate").hide();
            $("#sendDay").hide();
            $("#sendTime").show();
        }
    });


    $(document).ready(function () {
        if ($("input[name='data[NewsletterManagement][send_type]']:checked").val() == 1) {
            $("#sendDate").show();
            $("#sendDay").hide();
            $("#sendTime").show();
        } else if ($("input[name='data[NewsletterManagement][send_type]']:checked").val() == 2) {
            $("#sendDate").hide();
            $("#sendDay").show();
            $("#sendTime").show();
        } else {
            $("#sendDate").hide();
            $("#sendDay").hide();
            $("#sendTime").show();
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