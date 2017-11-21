<div class="row">
    <div class="col-lg-8">
        <h3>Page Listing</h3>
        <br><br>
        <?php echo $this->Session->flash(); ?>
        <div class="table-responsive">
            <div class="row padding_btm_20">
                <?php //echo $this->Form->create('Content', array('url' => array('controller' => 'contents', 'action' => 'pageLocation'), 'id' => 'AdminId', 'type' => 'post')); ?>
                <div class="col-lg-6">
                    <?php
//                    echo $this->Form->input('Store.navigation', array(
//                        'type' => 'radio',
//                        'div' => false,
//                        'legend' => false,
//                        'options' => array('1' => '&nbsp;&nbsp;Left Navigation&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '2' => '&nbsp;&nbsp;Top Navigation'),
//                        'default' => 1
//                    ));
//                    echo $this->Form->error('Store.is_active');
//                    
                    ?>
<!--                    <br/><span class="blue">(Please select navigation for front end)</span>-->
                </div>
                <div class="col-lg-3">
                    <?php //echo $this->Form->button('Update', array('type' => 'submit', 'class' => 'btn btn-default'));  ?>
                </div>
                <?php //echo $this->Form->end();  ?>
                <div class="col-lg-3">
                    <div class="addbutton">
                        <?php echo $this->Form->button('Add Page', array('type' => 'button', 'onclick' => "window.location.href='/contents/index'", 'class' => 'btn btn-default')); ?>
                    </div>
                </div>
            </div>
            <?php //echo $this->element('show_pagination_count');  ?>
            <table class="table table-bordered table-hover table-striped tablesorter" id="contentListing">
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
                    if ($list) {
                        $i = 0;
                        foreach ($list as $key => $data) {
                            $class = ($i % 2 == 0) ? ' class="active"' : '';
                            $EncryptPageID = $this->Encryption->encode($data['StoreContent']['id']);
                            ?>
                            <tr <?php echo $class; ?> notif-id="<?php echo $EncryptPageID; ?>">
                                <td><?php
                                    echo $data['StoreContent']['name'];
                                    if (($data['StoreContent']['name'] == 'PLACE ORDER') || ($data['StoreContent']['name'] == 'PHOTO')) {
                                        echo '&nbsp;&nbsp;&nbsp;<small>(Old layout only)</small>';
                                    }
                                    if (($data['StoreContent']['name'] == 'DEALS') || ($data['StoreContent']['name'] == 'GALLERY')) {
                                        echo '&nbsp;&nbsp;&nbsp;<small>(New layout only)</small>';
                                    }
                                    ?></td>

                                <td>
                                    <?php
                                    echo $this->Dateform->us_format($this->Common->storeTimezone('', $data['StoreContent']['created']));
                                    //echo $this->Dateform->us_format($data['StoreContent']['created']);
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($data['StoreContent']['is_active']) {
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'contents', 'action' => 'activatePage', $EncryptPageID, 0), array('confirm' => 'Are you sure to Inactive Page?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'contents', 'action' => 'activatePage', $EncryptPageID, 1), array('confirm' => 'Are you sure to Activate Page?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td class='sort_order'>
                                    <?php
                                    echo $this->Html->image('uparrow.png', array('alt' => "Up", 'title' => "Up", 'class' => 'up_order', 'id' => 'upOrder'));
                                    echo $this->Html->image('downarrow.png', array('alt' => "Down", 'title' => "Down", 'class' => 'down_order', 'id' => 'downOrder'));
                                    ?>
                                    <?php if (($data['StoreContent']['name'] != 'HOME') && ($data['StoreContent']['name'] != 'PLACE ORDER') && ($data['StoreContent']['name'] != 'RESERVATIONS') && ($data['StoreContent']['name'] != 'STORE INFO') && ($data['StoreContent']['name'] != 'PHOTO') && ($data['StoreContent']['name'] != 'REVIEWS' && ($data['StoreContent']['name'] != 'MENU') && ($data['StoreContent']['name'] != 'DEALS' && ($data['StoreContent']['name'] != 'GALLERY')))) { ?>
                                        <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'contents', 'action' => 'editPage', $EncryptPageID), array('escape' => false)); ?>
                                        <?php echo " | "; ?>

                                        <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'contents', 'action' => 'deletePage', $EncryptPageID), array('confirm' => 'Are you sure to delete Page?', 'escape' => false)); ?>
                                    <?php } ?>

                                </td>

                            </tr>
                            <?php
                            $i++;
                        }
                        if (!empty($termsAndPolicy)) {
                            $EncryptTermsAndPolicyID = $this->Encryption->encode($termsAndPolicy['TermsAndPolicy']['id']);
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
                                        echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'contents', 'action' => 'activateTermsAndPolicy', $EncryptTermsAndPolicyID, 0), array('confirm' => 'Are you sure to Inactive Page?', 'escape' => false));
                                    } else {
                                        echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'contents', 'action' => 'activateTermsAndPolicy', $EncryptTermsAndPolicyID, 1), array('confirm' => 'Are you sure to Activate Page?', 'escape' => false));
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'stores', 'action' => 'saveTermsAndPolicies', $EncryptTermsAndPolicyID), array('escape' => false)); ?>

                                </td>

                            </tr>
                            <?php
                        }
                    } elseif (!empty($termsAndPolicy)) {
                        $EncryptTermsAndPolicyID = $this->Encryption->encode($termsAndPolicy['TermsAndPolicy']['id']);
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
                                    echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'contents', 'action' => 'activateTermsAndPolicy', $EncryptTermsAndPolicyID, 0), array('confirm' => 'Are you sure to Inactive Page?', 'escape' => false));
                                } else {
                                    echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'contents', 'action' => 'activateTermsAndPolicy', $EncryptTermsAndPolicyID, 1), array('confirm' => 'Are you sure to Activate Page?', 'escape' => false));
                                }
                                ?>
                            </td>
                            <td>
                                <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'stores', 'action' => 'saveTermsAndPolicies', $EncryptTermsAndPolicyID), array('escape' => false)); ?>

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
                <!--div class="col-lg-2"> <?php //echo $this->Html->image("admin/category.png"). " Add Sub Category";                                      ?> </div-->

            </div>

        </div>
        <?php //echo $this->Html->css('pagination');   ?>


        <script>
            var notifLen = $('table#contentListing').find('tr').length;
            $(document).ready(function () {
                $("#categoryIsActive").change(function () {
                    var catgoryId = $("#categoryIsActive").val
                    $("#AdminId").submit();
                });


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
                        url: '/contents/updatePageListingPosition?' + orderData,
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