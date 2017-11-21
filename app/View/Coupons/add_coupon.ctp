<div class="container">
    <?php echo $this->element('deals/deal_form'); ?>
    <div class="row">
        <div class="col-sm-12">
            <ul class="nav nav-tabs">
                <li class="active"><?php echo $this->Html->link('Coupons', array('controller' => 'coupons', 'action' => 'addCoupon')); ?></li>
                <li><?php echo $this->Html->link('Promotions', array('controller' => 'offers', 'action' => 'addOffer')); ?></li>
                <li><?php echo $this->Html->link('Extended Offers', array('controller' => 'itemOffers', 'action' => 'add')); ?></li>
            </ul>   
            <br>
            <style>
                .form_margin { height: 59px;margin: 0px 1px 2px;}
                .coupon-days { position:relative;}
                .coupon-days .error { bottom:7px;left:0;position:absolute;}
            </style>
            <div class="row">
                <div class="col-lg-6">
                    <h3>Add Coupon</h3>
                    <?php echo $this->Session->flash(); ?>
                </div>
                <div class="col-lg-6">
                    <div class="addbutton">
                        <?php echo $this->Form->button('Upload Coupon', array('type' => 'button', 'onclick' => "window.location.href='/coupons/uploadfile'", 'class' => 'btn btn-default')); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php echo $this->Form->create('Coupons', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'CouponAdd', 'enctype' => 'multipart/form-data')); ?>
                <div class="col-lg-6">
                    <div class="form-group form_margin">
                        <label>Title<span class="required"> * </span></label>

                        <?php
                        echo $this->Form->input('Coupon.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Title', 'label' => '', 'div' => false));
                        echo $this->Form->error('Coupon.name');
                        ?>
                    </div>
                    <br>
                    <div class="form-group form_margin">
                        <label>Coupon Code<span class="required"> * </span></label>
                        <?php echo $this->Form->input('Coupon.coupon_code', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Code', 'label' => '', 'div' => false)); ?>

                        <?php echo $this->Form->error('Coupon.coupon_code'); ?>
                    </div>
                    <br>
                    <div class="form-group form_margin">
                        <label>Upload Image</label>
                        <?php
                        echo $this->Form->input('Coupon.image', array('type' => 'file', 'div' => false));

                        echo $this->Form->error('Coupon.image');
                        ?>
			<span class="blue">Max upload size 2MB</span> 
                    </div>
                    <br>
                    <div class="form-group form_margin">
                        <label>Type<span class="required"> * </span></label>
                        &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                        <?php
                        echo $this->Form->input('Coupon.discount_type', array(
                            'type' => 'radio',
                            'options' => array('1' => 'Price&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '2' => '%'),
                            'default' => 1
                        ));
                        echo $this->Form->error('Coupon.discount_type');
                        ?>
                    </div>

                    <div class="form-group form_margin">
                        <label>Discount<span class="blue">(Price / %)</span><span class="required"> * </span></label>

                        <?php
                        echo $this->Form->input('Coupon.discount', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter discount', 'label' => '', 'div' => false));
                        echo $this->Form->error('Coupon.discount');
                        ?>


                    </div>
                    <br>
                    <div class="form-group form_margin">
                        <label>No. of times can used<span class="required"> * </span></label>

                        <?php
                        echo $this->Form->input('Coupon.number_can_use', array('type' => 'number', 'min' => '1', 'class' => 'form-control valid', 'placeholder' => 'Enter no of times use', 'label' => '', 'div' => false, 'value' => 1));
                        echo $this->Form->error('Coupon.number_can_use');
                        ?>
                    </div>
                    <br>
                    <div class="form-group form_margin">
                        <label>Start Date<span class="required"> * </span></label>
                        <?php
                        echo $this->Form->input('Coupon.start_date', array('type' => 'text', 'class' => 'form-control', 'div' => false, 'readonly' => true));
                        ?>
                    </div>
                    <br>
                    <div class="form-group form_margin">
                        <label>End Date<span class="required"> * </span></label>
                        <?php
                        echo $this->Form->input('Coupon.end_date', array('type' => 'text', 'class' => 'form-control', 'div' => false, 'readonly' => true));
                        ?>
                    </div>
                    <br>
                    <div class="form-group form_margin">
                        <label>
                            <?php
                            echo $this->Form->checkbox('Coupon.allow_time', array('value' => '1'));
                            ?>
                            Time Restrictions
                        </label>
                    </div>
                    <span id="FromTodate" style="display:none" class="clearfix"> 
                        <div class="form-group form_margin">
                            <label>Start Time</label>
                            <td><?php
                                echo $this->Form->input('Coupon.start_time', array('options' => $timeOptions, 'class' => 'passwrd-input ', 'div' => false));
                                echo $this->Form->error('Coupon.start_time');
                                ?>
                            </td>
                            <label>End Time</label>
                            <td><?php
                                echo $this->Form->input('Coupon.end_time', array('options' => $timeOptions, 'class' => 'passwrd-input ', 'div' => false));
                                echo $this->Form->error('Coupon.end_time');
                                ?>
                            </td>
                        </div>
                        <div class="form-group form_margin coupon-days">
                            <?php
                            $days = array('1' => 'Mon', '2' => 'Tue', '3' => 'Wed', '4' => 'Thur', '5' => 'Fri', '6' => 'Sat', '7' => 'Sun');
                            $selectedDays = '';
                            foreach ($days as $key => $data) {
                                echo "<div class='pull-left' style='padding-right:15px;'>";
                                echo "<label>";
                                echo $this->Form->checkbox('Coupon.days.' . $key, array('hiddenField' => false, 'multiple' => 'checkbox', 'class' => 'dayCheckbox', 'value' => $data));
                                echo $data . "</div></label>";
                            }
                            ?>
                            <label id="checkBoxError" class="error hidden">You must select at least one!</label>
                        </div>
                    </span>
                    <div class="form-group form_spacing clearfix">
                        <label>Promotional Message</label>
                        <?php
                        echo $this->Form->input('Coupon.promotional_message', array('type' => 'textarea', 'class' => 'form-control valid', 'placeholder' => 'Enter Message', 'label' => '', 'div' => false));
                        echo $this->Form->error('Coupon.promotional_message');
                        ?>
                        <span class="blue">(Please enter promotional message for coupon sharing, variables available are ( <strong>{FULL_NAME}</strong> , <strong>{COUPON}</strong> ))</span>
                    </div>
                    <br>
                    <div class="form-group form_margin">
                        <label>Status<span class="required"> * </span></label>
                        &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                        <?php
                        echo $this->Form->input('Coupon.is_active', array(
                            'type' => 'radio',
                            'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive'),
                            'default' => 1
                        ));
                        echo $this->Form->error('Coupon.is_active');
                        ?>

                    </div>



                    <?php //if($seasonalpost){ $display="style='display:block;'";}else{$display="style='display:none;'";}    ?>




                    <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default save-coupon')); ?>
                    <?php //echo $this->Html->link('Cancel', "/coupons/index/", array("class" => "btn btn-default",'escape' => false));     ?>
                </div>
                <?php echo $this->Form->end(); ?>
            </div><!-- /.row -->
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <h3>Coupon Listing</h3>
                    <hr>
                    <?php echo $this->Session->flash(); ?>
                    <div class="table-responsive">
                        <?php echo $this->Form->create('Coupon', array('url' => array('controller' => 'coupons', 'action' => 'addCoupon'), 'id' => 'AdminId', 'type' => 'post')); ?>
                        <div class="row padding_btm_20">


                            <div class="col-lg-3">
                                <?php
                                $options = array('1' => 'Active', '0' => 'Inactive');
                                echo $this->Form->input('Coupon.isActive', array('type' => 'select', 'class' => 'form-control valid', 'label' => false, 'div' => false, 'autocomplete' => 'off', 'options' => $options, 'empty' => 'Select Status'));
                                ?>
                            </div>
                            <div class="col-lg-3">
                                <?php echo $this->Form->input('Coupon.search', array('type' => 'text', 'class' => 'form-control', 'label' => false, 'div' => false, 'placeholder' => 'Name')); ?>
                                <span class="blue">(<b>Search by:</b>Coupon Name-Coupon code)</span>
                            </div>
                            <div class="col-lg-1">
                                <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>

                            </div>
                            <div class="col-lg-1">
                                <?php echo $this->Html->link('Clear', "/coupons/addCoupon/clear", array("class" => "btn btn-default", 'escape' => false)); ?>
                            </div>

                            <!--                <div class="col-lg-9">
                                                <div class="addbutton">
                            <?php echo $this->Form->button('Add Coupon', array('type' => 'button', 'onclick' => "window.location.href='/coupons/addCoupon'", 'class' => 'btn btn-default')); ?>
                            <?php echo $this->Form->button('Upload Coupon', array('type' => 'button', 'onclick' => "window.location.href='/coupons/uploadfile'", 'class' => 'btn btn-default')); ?>
            
                                                </div>
                                            </div>-->
                        </div>
                        <?php echo $this->Form->end(); ?>
                        <div class="row">
                            <div class="col-sm-6">
                                <?php echo $this->Paginator->counter('Page {:page} of {:pages}'); ?>
                            </div>
                            <div class="col-sm-6 text-right">
                                <?php echo $this->Paginator->counter('showing {:current} records out of {:count} total'); ?>
                            </div>
                        </div>
                        <?php echo $this->Form->create('Category', array('url' => array('controller' => 'coupons', 'action' => 'deleteMultipleCoupons'), 'id' => 'OrderId', 'type' => 'post')); ?>
                        <table class="table table-bordered table-hover table-striped tablesorter">
                            <thead>
                                <tr>
                                    <th  class="th_checkbox" style="float:left;border:none;"><input type="checkbox" id="selectall"/></th>
                                    <th  class="th_checkbox"><?php echo $this->Paginator->sort('Coupon.name', 'Name'); ?></th>
                                    <th  class="th_checkbox">Code</th>
                                    <th  class="th_checkbox">Coupon can Use</th>
                                    <th  class="th_checkbox">Coupon Used</th>
                                    <th  class="th_checkbox">Start date</th>
                                    <th  class="th_checkbox">End date</th>
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
                                        $EncryptCouponID = $this->Encryption->encode($data['Coupon']['id']);
                                        ?>
                                        <tr <?php echo $class; ?>>
                                            <td class="firstCheckbox"><?php echo $this->Form->checkbox('Coupon.id.' . $key, array('class' => 'case', 'value' => $data['Coupon']['id'], 'style' => 'float:left;')); ?></td>
                                            <td><?php echo $data['Coupon']['name']; ?></td>
                                            <td><?php echo $data['Coupon']['coupon_code']; ?></td>
                                            <td><?php echo $data['Coupon']['number_can_use']; ?></td>
                                            <td><?php echo $this->Html->link($data['Coupon']['used_count'], array('controller' => 'coupons', 'action' => 'couponUsedList', $EncryptCouponID)); ?></td>
                                            <td><?php echo $data['Coupon']['start_date']; ?></td>
                                            <td><?php echo $data['Coupon']['end_date']; ?></td>
                                            <td>
                                                <?php
                                                if ($data['Coupon']['is_active']) {
                                                    echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'coupons', 'action' => 'activateCoupon', $EncryptCouponID, 0), array('confirm' => 'Are you sure to Deactivate Coupon?', 'escape' => false));
                                                } else {
                                                    echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'coupons', 'action' => 'activateCoupon', $EncryptCouponID, 1), array('confirm' => 'Are you sure to Activate Coupon?', 'escape' => false));
                                                }
                                                ?>
                                            </td>

                                            <td>


                                                <?php
                                                if ($data['Coupon']['is_active'] == 1) {
                                                    echo $this->Html->link($this->Html->image("store_admin/mail_sent.png", array("alt" => "Share", "title" => "Share")), array('controller' => 'coupons', 'action' => 'shareCoupon?couponId=' . $EncryptCouponID), array('escape' => false));
                                                    echo " | ";
                                                } else {
                                                    
                                                }
                                                ?>
                                                <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'coupons', 'action' => 'editCoupon', $EncryptCouponID), array('escape' => false)); ?>
                                                <?php echo " | "; ?>
                                                <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'coupons', 'action' => 'deleteCoupon', $EncryptCouponID), array('confirm' => 'Are you sure to delete Coupon?', 'escape' => false)); ?>
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
                            <?php if ($list) { ?>
                                <tfoot>
                                    <tr>
                                        <td colspan="6">                       
                                            <?php
                                            echo $this->Form->button('Delete Coupon', array('type' => 'submit', 'class' => 'btn btn-default', 'onclick' => 'return check();'));
                                            ?>                     
                                        </td>
                                    </tr>
                                </tfoot>
                            <?php } ?>
                        </table>
                        <?php echo $this->Form->end(); ?>
                        <?php echo $this->element('pagination') ?>
                        <div class="row padding_btm_20" style="padding-top:10px">
                            <div class="col-lg-1">
                                LEGENDS:
                            </div>
                            <div class="col-lg-1" style=" white-space: nowrap;"><?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                            <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                            <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                            <div class="col-lg-1" style=" white-space: nowrap;"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
                            <!--div class="col-lg-2"> <?php //echo $this->Html->image("admin/category.png"). " Add Sub Category";                                                                                               ?> </div-->

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
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
        $("#CouponIsActive").change(function () {
            var couponId = $("#CouponIsActive").val
            $("#AdminId").submit();
        });
        $("#CouponSearch").autocomplete({
            source: "<?php echo $this->Html->url(array('controller' => 'coupons', 'action' => 'getSearchValues')); ?>",
            minLength: 3,
            search: function () {
                $("#loading").show();
            },
            response: function () {
                $("#loading").hide();
            },
            select: function (event, ui) {
                console.log(ui.item.value);
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                    .append("<div>" + item.desc + "</div>")
                    .appendTo(ul);
        };
//        $.validator.addMethod("daycheck", function (value, elem, param) {
//            if ($(".dayCheckbox:checkbox:checked").length > 0) {
//                return true;
//            } else {
//                return false;
//            }
//        }, "You must select at least one!");
//        $.validator.addClassRules('dayCheckbox', {
//            daycheck: true,
//            minlength: 1
//        });
        $('#CouponAdd .save-coupon').click(function (e) {
            e.preventDefault();
            if ($("#CouponAdd").valid()) {
                if ($("#CouponAllowTime").is(":checked")) {
                    if ($(".dayCheckbox:checkbox:checked").length > 0) {
                        $("#checkBoxError").addClass('hidden');
                        $('#CouponAdd').submit();
                    } else {
                        $("#checkBoxError").removeClass('hidden');
                    }
                } else {
                    $('#CouponAdd').submit();
                }
            }
        });
        $(".dayCheckbox").change(function () {
            if ($(".dayCheckbox:checkbox:checked").length > 0) {
                $("#checkBoxError").addClass('hidden');
            }
        });
        $("#CouponAdd").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Coupon][name]": {
                    required: true,
                },
                "data[Coupon][coupon_code]": {
                    required: true,
                    alphanumeric: true,
                    minlength: 4
                },
                "data[Coupon][discount]": {
                    required: true,
                    number: true,
                    min: 1,
                    maxlength: 8
                },
                "data[Coupon][number_can_use]": {
                    required: true,
                    digits: true,
                    min: 1,
                },
                "data[Coupon][start_date]": {
                    required: true,
                },
                "data[Coupon][end_date]": {
                    required: true,
                }
            },
            messages: {
                "data[Coupon][name]": {
                    required: "Please enter coupon title",
                },
                "data[Coupon][coupon_code]": {
                    required: "Please enter coupon code",
                },
                "data[Coupon][discount]": {
                    required: "Please enter discount",
                },
                "data[Coupon][number_can_use]": {
                    required: "Please enter no of times",
                },
                "data[Coupon][start_date]": {
                    required: "Please select start date",
                },
                "data[Coupon][end_date]": {
                    required: "Please select end date",
                }
            },
            highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            }
        });
        $('#CouponName').keyup(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });
        $('#CouponNumberCanUse').keyup(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });
        $('#CouponStartDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: "<?php echo date("m-d-Y", strtotime($this->Common->storeTimezone('', date("Y-m-d H:i:s")))); ?>",
            onSelect: function (selected) {
                $("#CouponStartDate").prev().find('div').remove();
                $("#CouponEndDate").datepicker("option", "minDate", selected)
            }

        });
        $('#CouponEndDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: "<?php echo date("m-d-Y", strtotime($this->Common->storeTimezone('', date("Y-m-d H:i:s")))); ?>",
        });
        $("#CouponAllowTime").change(function () {
            var flag = $("#CouponAllowTime").val();
            if ($(this).is(":checked")) {
                $("#FromTodate").show();
            } else {
                $("#FromTodate").hide();
            }
        });
        $('#CouponEndTime option').last().prop('selected', true);
        if ($("#CouponAllowTime").is(":checked")) {
            $("#FromTodate").show();
        } else {
            $("#FromTodate").hide();
        }
        //        $('.save-coupon').click(function (e) {
        //            e.preventDefault();
        //            if ($('#CouponAdd').valid()) {
        //                $('#ListingInformationEditForm').submit();
        //            }
        //        });
        //$('#CouponDays1').addClass('dayCheckbox');
    });
    function check()
    {
        var fields = $(".case").serializeArray();
        if (fields.length == 0)
        {
            alert('Please select coupon to proceed.');
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
</script>
