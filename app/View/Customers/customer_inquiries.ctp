
<div class="row">
    <div class="col-lg-12">
        <h3>Enquiry Listing</h3>
        <?php echo $this->Session->flash(); ?>
        <div class="table-responsive">
            <?php echo @$this->element('show_pagination_count'); ?>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('StoreInquiries.name', 'Customer Name'); ?></th>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('StoreInquiries.email', 'Email'); ?></th>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('StoreInquiries.phone', 'Phone'); ?></th>
                        <th  class="th_checkbox">Message</th>
                        <th  class="th_checkbox"><?php echo @$this->Paginator->sort('StoreInquiries.created', 'Created'); ?></th>
                        <th  class="th_checkbox">Action</th>
                    </tr>
                </thead>

                <tbody class="dyntable">
                    <?php
                    if (!empty($list)) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptCustomerID = $this->Encryption->encode($data['StoreInquiries']['id']);
                            ?>
                            <tr <?php echo $class; ?>>
                                <td><?php echo $data['StoreInquiries']['name']; ?></td>
                                <td><?php echo $data['StoreInquiries']['email']; ?></td>
                                <td><?php echo $data['StoreInquiries']['phone']; ?></td>
                                <td><?php echo $data['StoreInquiries']['message']; ?></td>
                                <td><?php
                                    echo $this->Dateform->us_format($this->Common->storeTimezone('', $data['StoreInquiries']['created']));
                                    ?>
                                </td>
                                <td>
                                    <?php if(empty($data['StoreInquiries']['reply_flag'])) {
                                        echo $this->Html->link('<i class="fa fa-reply" aria-hidden="true"></i>', array('controller' => 'customers', 'action' => 'replyCustomerInquiry', $EncryptCustomerID), array('escape' => false, 'title' => 'Reply'));
                                        ?>
                                        <?php echo " | ";
                                    } ?>
        <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'customers', 'action' => 'deleteCustomerInquiry', $EncryptCustomerID), array('confirm' => 'Are you sure to delete this inquiry?', 'escape' => false)); ?>

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
            <div class="paging_full_numbers" id="example_paginate" style="padding-top:10px">
                <?php
                echo @$this->Paginator->first('First');
                // Shows the next and previous links
                echo @$this->Paginator->prev('Previous', null, null, array('class' => 'disabled'));
                // Shows the page numbers
                echo @$this->Paginator->numbers(array('separator' => ''));
                echo @$this->Paginator->next('Next', null, null, array('class' => 'disabled'));
                // prints X of Y, where X is current page and Y is number of pages
                //echo $this->Paginator->counter();
                echo @$this->Paginator->last('Last');
                ?>
            </div>

            <div class="row padding_btm_20" style="padding-top:10px">
                <div class="col-lg-1">
                    LEGENDS:
                </div>
                <div class="col-lg-1" style=" white-space: nowrap;"><?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
                <!--div class="col-lg-2"> <?php //echo $this->Html->image("admin/category.png"). " Add Sub Category";                   ?> </div-->
            </div>

        </div>
<?php echo $this->Html->css('pagination'); ?> 
        <script>
            $(document).ready(function () {
                $("#UserKeyword").autocomplete({
                    source: "<?php echo $this->Html->url(array('controller' => 'customers', 'action' => 'getSearchValues')); ?>",
                    minLength: 3,
                    select: function (event, ui) {
                        console.log(ui.item.value);
                    }
                }).autocomplete("instance")._renderItem = function (ul, item) {
                    return $("<li>")
                            .append("<div>" + item.desc + "</div>")
                            .appendTo(ul);
                }
                $('#UserFrom').datepicker({
                    dateFormat: 'mm-dd-yy',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '2010:' + new Date().getFullYear(),
                    maxDate: 0,
                    onSelect: function (selectedDate) {
                        $("#UserTo").datepicker("option", "minDate", selectedDate);
                    }
                });
                $('#UserTo').datepicker({
                    dateFormat: 'mm-dd-yy',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '2010:' + new Date().getFullYear(),
                    maxDate: 0,
                    onSelect: function (selectedDate) {
                        $("#UserFrom").datepicker("option", "maxDate", selectedDate);
                    }
                });

                $("#UserIsActive").change(function () {
                    var catgoryId = $("#UserIsActive").val();
                    $("#AdminId").submit();
                });

                $("#UserIsActive").change(function () {
                    //var catgoryId=$("#ItemCategoryId").val();
                    $("#AdminId").submit();
                });

            });
        </script>