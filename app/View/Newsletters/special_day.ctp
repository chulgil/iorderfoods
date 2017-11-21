<style>
    @media (max-width: 960px) {
        .cke_reset { width:100%; }
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <h3>Special Day's Listing</h3>
        <hr>
    </div>
    <div class="col-lg-12">
        <div class="table-responsive">   
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>	    
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('SpecialDay.default_special_day_id', 'Subject'); ?></th>
<!--                        <th  class="th_checkbox"><?php// echo @$this->Paginator->sort('SpecialDay.created', 'Created'); ?></th>-->
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
                            $EncryptSpecialDayID = $this->Encryption->encode($data['SpecialDay']['id']);
                            ?>
                            <tr <?php echo $class; ?>>	    
                                <td><?php echo $data['DefaultSpecialDay']['name']; ?></td>

<!--                                <td>
                                    <?php //echo $this->Dateform->us_format($this->Common->storeTimezone('', $data['SpecialDay']['created'])); ?>
                                </td>-->
                                <td>
                                    <?php
                                    if ($data['SpecialDay']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'newsletters', 'action' => 'activateSpecialDay', $EncryptSpecialDayID, 0), array('confirm' => 'Are you sure to Inactive Newsletter?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'newsletters', 'action' => 'activateSpecialDay', $EncryptSpecialDayID, 1), array('confirm' => 'Are you sure to Activate Newsletter?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'newsletters', 'action' => 'editSpecialDay', $EncryptSpecialDayID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/time.png", array("alt" => "Cron", "title" => "Cron")), array('controller' => 'newsletters', 'action' => 'specialDayManagement', $EncryptSpecialDayID), array('escape' => false)); ?>         
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'newsletters', 'action' => 'deleteSpecialDay', $EncryptSpecialDayID), array('confirm' => 'Are you sure to delete Newsletter?', 'escape' => false)); ?>         
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
            <?php
            if (!empty($list)) {
                echo $this->element('pagination');
                ?>
                <div class="row padding_btm_20" style="padding-top:10px">
                    <div class="col-lg-2">   
                        LEGENDS:                        
                    </div>
                    <div class="col-lg-2"><?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                    <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                    <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                    <div class="col-lg-2"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
                </div>
                <?php echo $this->Html->css('pagination'); ?>
            <?php } ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#NewsletterIsActive").change(function () {
            $("#NewsletterId").submit();
        });
        $("#NewsletterAdd").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            ignore: [],
            onkeyup: false,
            rules: {
                "data[Newsletter][name]": {
                    required: true
                },
                "data[Newsletter][content_key]": {
                    required: true
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
            },
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
    });
</script>
