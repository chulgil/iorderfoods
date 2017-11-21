<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<?php echo $this->Html->script('ckfinder/ckfinder'); ?>
<div class="row">
    <div class="col-lg-6">
        <h3>Edit Newsletter</h3> 
        <hr>
        <?php echo $this->Session->flash(); ?>   
    </div> 
</div>   
<div class="row">        
    <?php echo $this->Form->create('Newsletter', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'NewsletterAdd')); ?>
    <div class="col-lg-6">   
        <div class="form-group">
            <label>Store<span class="required"> * </span></label>
            <?php
            $merchantList = $this->Common->getHQStores($this->Session->read('merchantId'));
            echo $this->Form->input('Newsletter.store_id', array('options' => @$merchantList, 'class' => 'form-control', 'label' => false, 'div' => false, 'empty' => 'Select Store', 'disabled' => true));
            ?>
        </div>
        <div class="form-group">		 
            <label>Subject<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('Newsletter.name', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false));
            echo $this->Form->error('Newsletter.name');
            echo $this->Form->input('Newsletter.id', array('type' => 'hidden'));
            ?>
        </div>
        <div class="form-group">		 
            <label>Content Key<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('Newsletter.content_key', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Type', 'label' => '', 'div' => false));
            echo $this->Form->error('Newsletter.content_key');
            ?>
        </div>
        <div class="form-group">
            <label>Body</label> 
            <?php
            echo $this->Form->textarea('Newsletter.content', array('class' => 'ckeditor'));
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
        <?php echo $this->Html->link('Cancel', "/hqnewsletters/newsLetterAdd", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {
        $("#NewsletterAdd").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            ignore: [],
            onkeyup: false,
            rules: {
                "data[Newsletter][name]": {
                    required: true,
                },
                "data[Newsletter][content_key]": {
                    required: true,
                },
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