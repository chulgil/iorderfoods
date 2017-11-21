<div class="row">
    <div class="col-lg-6">
        <h3>Edit Coupon</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
</div>
<hr>
<div class="row">        
    <?php echo $this->Form->create('Coupons', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'CouponAdd', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">
        <div class="form-group">
            <label>Store<span class="required"> * </span></label>
            <?php
            $merchantList = $this->Common->getHQStores($this->Session->read('merchantId'));
            if (!empty($this->request->data['Coupon']['store_id'])) {
                $store_id = $this->request->data['Coupon']['store_id'];
            }

            echo $this->Form->input('Coupon.store_id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Store', 'disabled' => true));
            ?>
        </div>
        <div class="form-group">		 
            <label>Title<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('Coupon.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Title', 'label' => '', 'div' => false));
            echo $this->Form->error('Coupon.name');
            echo $this->Form->input('Coupon.used_count', array('type' => 'hidden'));
            ?>
        </div>
        <div class="form-group">		 
            <label>Coupon Code<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('Coupon.coupon_code', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Code', 'label' => '', 'div' => false, 'disabled' => true));
            echo $this->Form->error('Coupon.coupon_code');
            ?>
        </div>
        <div class="form-group form_spacing clearfix">
            <div style="float:left;">
                <label>Upload Coupon Photo</label>
                <?php
                echo $this->Form->input('Coupon.image', array('type' => 'file', 'div' => false));
                echo $this->Form->error('Coupon.image');
                ?>
		<span class="blue">Max Upload Size 2MB</span> 
            </div>
            <?php
            $EncryptCouponID = $this->Encryption->encode($this->request->data['Coupon']['id']);
            ?>
            <div style="float:right;">
                <?php
                if ($this->request->data['Coupon']['image']) {
                    echo $this->Html->image('/Coupon-Image/thumb/' . $this->request->data['Coupon']['image'], array('alt' => 'Coupon Image', 'height' => 150, 'width' => 150, 'style' => 'border:1px solid #000000;margin:5px 0px 5px 5px;', 'title' => 'Item Image'));
                    echo $this->Html->link("X", array('controller' => 'hqcoupons', 'action' => 'deleteCouponPhoto', $EncryptCouponID), array('confirm' => 'Are you sure to delete Coupon Image?', 'title' => 'Delete Photo', 'style' => 'vertical-align:top;margin-right:10px;font-size:18px;font-weight:bold;'));
                }
                ?>
            </div>		
        </div>
        <div class="form-group">
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
        <div class="form-group">		 
            <label>Discount<span class="blue">(Price / %)</span><span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('Coupon.discount', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Size', 'label' => '', 'div' => false));
            echo $this->Form->error('Coupon.discount');
            ?>
        </div>
        <div class="form-group">		 
            <label>No. of times can used<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('Coupon.number_can_use', array('type' => 'number', 'class' => 'form-control valid', 'min' => '1', 'placeholder' => 'Enter Size', 'label' => '', 'div' => false));
            echo $this->Form->error('Coupon.number_can_use');
            ?>
        </div>
        <div class="form-group">
            <label>Start Date<span class="required"> * </span></label>  
            <?php
            echo $this->Form->input('Coupon.start_date', array('type' => 'text', 'class' => 'form-control', 'div' => false));
            ?>
        </div>
        <div class="form-group">
            <label>End Date<span class="required"> * </span></label>  
            <?php
            echo $this->Form->input('Coupon.end_date', array('type' => 'text', 'class' => 'form-control', 'div' => false));
            ?>
        </div>
        <div class="form-group">
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
        <span id="FromTodate" <?php echo $display; ?>> 
            <div class="form-group">
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
            <div class="form-group">
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

        <div class="form-group">		 
            <label>Promotional Message</label>               

            <?php
            echo $this->Form->input('Coupon.promotional_message', array('type' => 'textarea', 'class' => 'form-control valid', 'placeholder' => 'Enter Message', 'label' => '', 'div' => false));
            echo $this->Form->error('Coupon.promotional_message');
            ?>
            <span class="blue">(Please enter promotional message for coupon sharing, variables available are ( <strong>{FULL_NAME}</strong> , <strong>{COUPON}</strong> ))</span>
        </div>
        <div class="form-group">
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
        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default update-coupon')); ?>             
        <?php echo $this->Html->link('Cancel', "/hqcoupons/index/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>

    <?php
    if ($store_id) {
        $minDate = date("m-d-Y", strtotime($this->Hq->storeTimezone(null, date("Y-m-d H:i:s"), null, $store_id)));
    } else {
        $minDate = date("m-d-Y", strtotime(date("Y-m-d H:i:s")));
    }
    ?>


</div><!-- /.row -->
<script>
    $(document).ready(function () {
        $('#CouponStartDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: "<?php echo $minDate; ?>",
            onSelect: function (selected) {
                $("#CouponStartDate").prev().find('div').remove();
                $("#CouponEndDate").datepicker("option", "minDate", selected)
            }

        });
        $('#CouponEndDate').datepicker({
            dateFormat: 'mm-dd-yy',
            minDate: "<?php echo $minDate; ?>",
        });
        $('#CouponDiscount').keyup(function () {
            this.value = this.value.replace(/[^0-9.,]/g, '');
        });
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
                    minlength: 4,
                },
                "data[Coupon][discount]": {
                    required: true,
                    number: true,
                    min: 1,
                    maxlength: 8,
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
                }, "data[Coupon][start_date]": {
                    required: "Please select start date",
                },
                "data[Coupon][end_date]": {
                    required: "Please select end date",
                },
            }, highlight: function (element, errorClass) {
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
    });
</script>
