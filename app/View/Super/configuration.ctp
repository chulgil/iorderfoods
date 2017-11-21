
<div class="row">
    <div class="col-lg-6">
        <h3>Configuration</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
</div>   
<div class="row">        

    <?php echo $this->Form->create('Super', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'ConfigurationAdd')); ?>
    <div class="col-lg-6">      
        <span class="blue">(SMTP Configuration Details)</span>
        <div class="form-group form_margin">		 
            <label>SMTP Host<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('MainSiteSetting.smtp_host', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'SMTP Host', 'label' => '', 'div' => false));
            echo $this->Form->error('MainSiteSetting.smtp_host');
            echo $this->Form->input('MainSiteSetting.id', array('type' => 'hidden'));
            ?>
        </div>
        <div class="form-group form_margin">		 
            <label>SMTP Port<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('MainSiteSetting.smtp_port', array('type' => 'text', 'class' => 'form-control valid integerValue', 'placeholder' => 'Smtp Port', 'label' => '', 'div' => false));
            echo $this->Form->error('MainSiteSetting.smtp_port');
            ?>
        </div>
        <div class="form-group form_margin">		 
            <label>SMTP Username<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('MainSiteSetting.smtp_username', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'SMTP Username', 'label' => '', 'div' => false));
            echo $this->Form->error('MainSiteSetting.smtp_username');
            ?>
        </div>
        <div class="form-group form_margin">		 
            <label>SMTP Password<span class="required"> * </span></label>               
            <?php
            echo $this->Form->input('MainSiteSetting.smtp_password', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'SMTP Password', 'label' => '', 'div' => false));
            echo $this->Form->error('MainSiteSetting.smtp_password');
            ?>
        </div>
        <div class="form-group form_margin">
            <label>Twilio Sms Gateway Number</label>
            <?php
            echo $this->Form->input('MainSiteSetting.twilio_number', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Twilio Sms Gateway Number'));
            ?>
        </div>
        <div class="form-group form_margin">
            <label>Twilio api Key</label>
            <?php
            echo $this->Form->input('MainSiteSetting.twilio_api_key', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Twilio Api Key'));
            ?>
        </div>
        <div class="form-group form_margin">
            <label>Twilio api token</label>
            <?php
            echo $this->Form->input('MainSiteSetting.twilio_api_token', array('type' => 'input', 'class' => 'form-control', 'Placeholder' => 'Enter Twilio Api Token'));
            ?>
        </div>

        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php echo $this->Html->link('Cancel', "/super/dashboard/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {
        $(".integerValue").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });
        $("#ConfigurationAdd").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[MainSiteSetting][api_key]": {
                    required: true,
                },
                "data[MainSiteSetting][api_username]": {
                    required: true,
                },
                "data[MainSiteSetting][api_password]": {
                    required: true,
                },
                "data[MainSiteSetting][smtp_port]": {
                    required: true,
                    number: true,
                    min: 1
                }

            },
            messages: {
                "data[MainSiteSetting][api_key]": {
                    required: "Please enter Api Key",
                },
                "data[MainSiteSetting][api_username]": {
                    required: "Please enter Api Username",
                },
                "data[MainSiteSetting][api_password]": {
                    required: "Please enter Api Password",
                },
            }
            , highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
        $(document).on('keyup', '#MainSiteSettingApiKey', function (e) {
            //$('#MainSiteSettingApiKey').onkeyup(function(){
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