<?php if (!empty($contactUsBgImage)) { ?>
    <style>
        .contact-us {
            background-image: url("<?php echo '/merchantBackground-Image/' . $contactUsBgImage; ?>");
            background-size: 100% auto;
            background-repeat:no-repeat;background-size:cover !important;
        }
    </style>
<?php } ?>
<div class="contact-us">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-10 col-md-8 col-sm-pull-1 col-sm-push-1 col-md-pull-2 col-md-push-2 text-center">
                <h2>CONTACT US</h2>
                <div id="contactFlashMsg"></div>
<?php echo $this->Form->create('ContactUs', array('inputDefaults' => array('class' => 'text-left', 'label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'))); ?>
                <div class="form-field"><?php echo $this->Form->input('name', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'NAME', 'label' => false, 'div' => false, 'required' => "required")); ?></div>
                <div class="form-field"><?php echo $this->Form->input('email', array('type' => 'email', 'class' => 'inbox', 'placeholder' => 'E-MAIL ADDRESS', 'label' => false, 'div' => false, 'required' => "required")); ?></div>
                <div class="form-field"><?php echo $this->Form->input('phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'inbox phone-number', 'placeholder' => 'PHONE NUMBER', 'label' => false, 'div' => false, 'required' => true)); ?></div>
                <div class="form-field"><?php echo $this->Form->input('subject', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'SUBJECT', 'label' => false, 'div' => false, 'required' => "required")); ?></div>
                <div class="form-field"><?php echo $this->Form->textarea('message', array('type' => 'textarea', 'class' => 'inbox', 'placeholder' => 'MESSAGE', 'required' => "required")); ?></div>
                <div class="form-field"><?php echo $this->Form->input('SUBMIT', array('type' => 'submit', "id" => "contactUs")); ?></div>
<?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).on('click', '#contactUs', function (e) {
        e.stopImmediatePropagation();
        if ($("#ContactUsMerchantForm").valid()) {
            var formData = $("#ContactUsMerchantForm").serialize();
            $.ajax({
                type: 'post',
                url: "<?php echo $this->Html->url(array('controller' => 'hqusers', 'action' => 'contact_us')); ?>",
                data: {'formData': formData},
                beforeSend: function () {
                    $.blockUI({css: {
                            border: 'none',
                            padding: '15px',
                            backgroundColor: '#000',
                            '-webkit-border-radius': '10px',
                            '-moz-border-radius': '10px',
                            opacity: .5,
                            color: '#fff'
                        }});
                },
                complete: function () {
                    $.unblockUI();
                    $("html, body").delay(2000).animate({
                        scrollTop: $('#contactFlashMsg').offset().top
                    }, 1000);
                },
                success: function (successResult) {
                    if (successResult != '') {
                        result = jQuery.parseJSON(successResult);
                        if (result.status == 'Success') {
                            $("#contactFlashMsg").html('<div class="message message-danger alert alert-success" id="flashMessage"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>' + result.msg + '</div>');
                            $("#ContactUsMerchantForm").find("input[type=text], textarea,input[type=email]").val("");
                        }
                        if (result.status == 'Error') {
                            $("#contactFlashMsg").html('<div class="message message-danger alert alert-danger" id="flashMessage"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>' + result.msg + '</div>');
                        }
                    }
                }
            });
        }
        e.preventDefault();
    });
    $(document).ready(function () {
        $("#ContactUsMerchantForm").validate({
            rules: {
                "data[ContactUs][name]": {
                    required: true,
                },
                "data[ContactUs][email]": {
                    required: true,
                    email: true
                },
                "data[ContactUs][subject]": {
                    required: true,
                },
                "data[ContactUs][message]": {
                    required: true,
                },
                "data[ContactUs][phone]": {
                    required: true,
                }
            },
            messages: {
                "data[ContactUs][name]": {
                    required: 'Please enter name.',
                },
                "data[ContactUs][email]": {
                    required: 'Please enter email.',
                },
                "data[ContactUs][subject]": {
                    required: 'Please enter subject.',
                },
                "data[ContactUs][message]": {
                    required: 'Please enter message.',
                },
                "data[ContactUs][phone]": {
                    required: 'Please enter contact no.',
                }
            }
        });

        $(".phone-number").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });
        $("[data-mask='mobileNo']").mask("(999) 999-9999");
        $(document).on('click', '#contact_us', function (e) {
            $("html, body").delay(1000).animate({
                scrollTop: $('.contact-us').offset().top
            }, 1000);
        });
    });
</script>