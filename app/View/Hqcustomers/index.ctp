
<div class="row">
    <div class="col-lg-12">
        <h3>Customer Listing</h3>
        <hr>
        <?php echo $this->Session->flash(); ?>
        <div class="table-responsive">
            <?php echo $this->Form->create('Customer', array('url' => array('controller' => 'hqcustomers', 'action' => 'index'), 'id' => 'AdminId', 'type' => 'post')); ?>
            <div class="row">
                <div class="col-lg-3">
                    <?php
                    $merchantList = $mList = $this->Common->getHQStores($this->Session->read('merchantId'));
                    if (!empty($merchantList)) {
                        $allOption = array('All' => 'All Store');
                        $merchantList = array_replace($allOption, $merchantList);
                    }
                    echo $this->Form->input('User.store_id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Store'));
                    ?>
                </div>
            </div>
            <hr>
            <div class="row padding_btm_20">
                <div class="col-lg-2">
                    <?php
                    $options = array('1' => 'Active', '0' => 'Inactive');
                    echo $this->Form->input('User.is_active', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                    ?>
                </div>
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
                    <?php echo $this->Html->link('Clear', array('controller' => 'hqcustomers', 'action' => 'index', 'clear'), array('class' => 'btn btn-default')); ?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <?php echo $this->element('show_pagination_count'); ?>
            <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                    <tr>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.fname', 'Customer Name'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.email', 'Email'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.phone', 'Phone'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.created', 'Created'); ?></th>
                        <th  class="th_checkbox"><?php echo $this->Paginator->sort('User.is_active', "Status") ?></th>
                        <th  class="th_checkbox">Order History</th>
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
                                <td><?php echo $data['User']['fname'] . " " . $data['User']['lname']; ?></td>
                                <td><?php echo $data['User']['email']; ?></td>
                                <td><?php echo $data['User']['phone']; ?></td>
                                <td><?php
                                    echo $this->Dateform->us_format($this->Common->storeTimezone(null, $data['User']['created'], null, $data['User']['store_id']));
                                    //echo $this->Dateform->us_format($data['User']['created']);
                                    ?></td>
                                <td>
                                    <?php
                                    if ($data['User']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'hqcustomers', 'action' => 'activateCustomer', $EncryptCustomerID, 0), array('confirm' => 'Are you sure to Deactivate Customer?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'hqcustomers', 'action' => 'activateCustomer', $EncryptCustomerID, 1), array('confirm' => 'Are you sure to Activate Customer?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td style="width:120px;"> <?php echo $this->Html->link("History", array('controller' => 'hqcustomers', 'action' => 'orderHistory', $EncryptCustomerID)); ?>
                                </td>

                                <td>
                                    <?php //$EncryptStoreID=$this->Encryption->encode($data['User']['id']);  ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'hqcustomers', 'action' => 'editCustomer', $EncryptCustomerID), array('escape' => false)); ?>
                                    <?php echo " | "; ?>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'hqcustomers', 'action' => 'deleteCustomer', $EncryptCustomerID), array('confirm' => 'Are you sure to delete Customer?', 'escape' => false)); ?>

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
                <div class="col-lg-1">
                    LEGENDS:
                </div>
                <div class="col-lg-1" style=" white-space: nowrap;"><?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
                <!--div class="col-lg-2"> <?php //echo $this->Html->image("admin/category.png"). " Add Sub Category";                ?> </div-->
            </div>

        </div>
        <?php echo $this->Html->css('pagination'); ?>

        <script>
            $(document).ready(function () {
                var storeId = $('#storeId').val();
                $("#UserKeyword").autocomplete({
                    source: "/hqcustomers/getSearchValues?storeID=" + storeId,
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

                $("#UserIsActive,#UserStoreId").change(function () {
                    //var catgoryId=$("#ItemCategoryId").val();
                    $("#AdminId").submit();
                });

            });
        </script>