<style>
    .form_margin {
        height: 59px;
        margin: 0px 1px 2px;
    }
    .coupon-days { position:relative;}
    .coupon-days .error { bottom:7px;left:0;position:absolute;}
</style>
<div class="row">
    <div class="col-lg-6">
        <h3>Edit Coupon</h3>
        <?php echo $this->Session->flash(); ?>
    </div>
    <div class="col-lg-6">
        <div class="addbutton">
            <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
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
            echo $this->Form->input('Coupon.used_count', array('type' => 'hidden'));
            ?>
        </div>
        <br>
        <div class="form-group form_margin">
            <label>Coupon Code<span class="required"> * </span></label>

            <?php
            echo $this->Form->input('Coupon.coupon_code', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Code', 'label' => '', 'div' => false, 'disabled' => true));
            echo $this->Form->error('Coupon.coupon_code');
            ?>
        </div><br>
        <div class="form-group form_spacing clearfix">
            <div style="float:left;">
                <label>Upload Coupon Photo</label>
                <?php
                echo $this->Form->input('Coupon.image', array('type' => 'file', 'div' => false));
                echo $this->Form->error('Coupon.image');
                ?>
		<span class="blue">Max upload size 2MB</span> 
            </div>
            <?php
            $EncryptCouponID = $this->Encryption->encode($this->request->data['Coupon']['id']);
            ?>
            <div style="float:right;">
                <?php
                if ($this->request->data['Coupon']['image']) {
                    echo $this->Html->image('/Coupon-Image/thumb/' . $this->request->data['Coupon']['image'], array('alt' => 'Coupon Image', 'height' => 150, 'width' => 150, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;', 'title' => 'Item Image'));
                    echo $this->Html->link("X", array('controller' => 'coupons', 'action' => 'deleteCouponPhoto', $EncryptCouponID), array('confirm' => 'Are you sure to delete Coupon Image?', 'title' => 'Delete Photo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
                }
                ?>
            </div>
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
            echo $this->Form->input('Coupon.discount', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Size', 'label' => '', 'div' => false));
            echo $this->Form->error('Coupon.discount');
            ?>


        </div>
        <br>
        <div class="form-group form_margin">
            <label>No. of times can used<span class="required"> * </span></label>

            <?php
            echo $this->Form->input('Coupon.number_can_use', array('type' => 'number', 'class' => 'form-control valid', 'min' => '1', 'placeholder' => 'Enter Size', 'label' => '', 'div' => false));
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
        <?php
        if (!empty($this->request->data['Coupon']['allow_time'])) {
            $display = "style='display:block;'";
        } else {
            $display = "style='display:none;'";
        }
        ?>
        <span id="FromTodate" <?php echo $display; ?> class="clearfix"> 
            <div class="form-group form_margin">
                <label>Start Time</label>
                <td><?php
                    echo $this->Form->input('Coupon.start_time', array('options' => $timeOptions, 'class' => 'passwrd-input', 'div' => false));
                    echo $this->Form->error('Coupon.start_time');
                    ?>
                </td>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
                if (!empty($this->request->data['Coupon']['days'])) {
                    $selectedDays = explode(',', $this->request->data['Coupon']['days']);
                }
                foreach ($days as $key => $data) {
                    if (!empty($selectedDays) && in_array($key, $selectedDays)) {
                        $checked = true;
                    } else {
                        $checked = false;
                    }
                    echo "<div class='pull-left' style='padding-right:15px;'>";
                    echo "<label>";
                    echo $this->Form->checkbox('Coupon.days.' . $key, array('hiddenField' => false, 'multiple' => 'checkbox', 'class' => 'dayCheckbox', 'value' => $data, 'checked' => $checked));
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



        <?php //if($seasonalpost){ $display="style='display:block;'";}else{$display="style='display:none;'";}?>




        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default update-coupon')); ?>
        <?php //echo $this->Html->link('Cancel', "/coupons/index/", array("class" => "btn btn-default",'escape' => false));   ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {
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
        $('#CouponAdd .update-coupon').click(function (e) {
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
        $("#CouponAllowTime").change(function () {
            var flag = $("#CouponAllowTime").val();
            if ($(this).is(":checked")) {
                $("#FromTodate").show();
            } else {
                $("#FromTodate").hide();
            }
        });
        if ($("#CouponAllowTime").is(":checked")) {
            $("#FromTodate").show();
        } else {
            $("#FromTodate").hide();
        }
        //$('#CouponDays1').addClass('dayCheckbox');
    });
</script>
