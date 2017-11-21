
<div class="row">
    <div class="col-lg-6">
        <h3>Manage Social Media</h3> 
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
        </div>
    </div>
</div>   
<div class="row">        
    <?php echo $this->Form->create('Store', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'Social', 'enctype' => 'multipart/form-data')); ?>
    <div class="col-lg-6">            


        <div class="form-group form_margin">		 
            <label>Facebook</label>               

            <?php
            echo $this->Form->input('SocialMedia.facebook', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Facebook', 'label' => '', 'div' => false));
            echo $this->Form->error('SocialMedia.facebook');
            echo $this->Form->input('SocialMedia.id', array('type' => 'hidden'));
            ?>
        </div>
        <div class="form-group form_margin">		 
            <label>Twitter</label>               

            <?php
            echo $this->Form->input('SocialMedia.twitter', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Twitter', 'label' => '', 'div' => false));
            echo $this->Form->error('SocialMedia.twitter');
            ?>
        </div>
        <div class="form-group form_margin">		 
            <label>Instagram</label>               

            <?php
            echo $this->Form->input('SocialMedia.instagram', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Instagram', 'label' => '', 'div' => false));
            echo $this->Form->error('SocialMedia.instagram');
            ?>
        </div>
        <div class="form-group form_margin">		 
            <label>Pinterest</label>               

            <?php
            echo $this->Form->input('SocialMedia.pinterest', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Pinterest', 'label' => '', 'div' => false));
            echo $this->Form->error('SocialMedia.pinterest');
            ?>
        </div>
        <div class="form-group form_margin">		 
            <label>Yelp</label>               

<?php
echo $this->Form->input('SocialMedia.yolo', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Yelp', 'label' => '', 'div' => false));
echo $this->Form->error('SocialMedia.yolo');
?>
        </div>
        <div class="form-group form_margin">		 
            <label>Google</label>               

<?php
echo $this->Form->input('SocialMedia.google', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Google', 'label' => '', 'div' => false));
echo $this->Form->error('SocialMedia.google');
?>
        </div>
        <div class="form-group form_margin">		 
            <label>Yahoo</label>               

<?php
echo $this->Form->input('SocialMedia.yahoo', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Yahoo', 'label' => '', 'div' => false));
echo $this->Form->error('SocialMedia.yahoo');
?>
        </div>
        <div class="form-group form_margin">		 
            <label>Yellow Page</label>               

<?php
echo $this->Form->input('SocialMedia.yellow_page', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Yellow Page', 'label' => '', 'div' => false));
echo $this->Form->error('SocialMedia.yellow_page');
?>
        </div>

        <div class="form-group form_margin">		 
            <label>Try Caviar</label>               

        <?php
        echo $this->Form->input('SocialMedia.try_caviar', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Try Caviar', 'label' => '', 'div' => false));
        echo $this->Form->error('SocialMedia.try_caviar');
        ?>
        </div>



    <?php //if($seasonalpost){ $display="style='display:block;'";}else{$display="style='display:none;'";} ?>




<?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
<?php echo $this->Html->link('Cancel', "/hq/dashboard/", array("class" => "btn btn-default", 'escape' => false)); ?>
    </div>
<?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {

        $("#Social").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[SocialMedia][facebook]": {
                    url: true
                },
                "data[SocialMedia][twitter]": {
                    url: true
                },
                "data[SocialMedia][instagram]": {
                    url: true
                },
                "data[SocialMedia][pinterest]": {
                    url: true
                },
                "data[SocialMedia][yolo]": {
                    url: true
                },
                "data[SocialMedia][google]": {
                    url: true
                },
                "data[SocialMedia][yahoo]": {
                    url: true
                },
                "data[SocialMedia][yellow_page]": {
                    url: true
                },
                "data[SocialMedia][try_caviar]": {
                    url: true
                },
            },
            messages: {
                "data[SocialMedia][facebook]": {
                    required: "Please enter the facebook url.",
                },
                "data[SocialMedia][twitter]": {
                    required: "Please enter the twitter url.",
                },
                "data[SocialMedia][instagram]": {
                    required: "Please enter the instagram url.",
                },
                "data[SocialMedia][pinterest]": {
                    required: "Please enter the pinterest url.",
                },
                "data[SocialMedia][yolo]": {
                    required: "Please enter the yolo url.",
                },
                "data[SocialMedia][google]": {
                    required: "Please enter the google url.",
                },
                "data[SocialMedia][yahoo]": {
                    required: "Please enter the yahoo url.",
                },
                "data[SocialMedia][yellow_page]": {
                    required: "Please enter the yellow page url.",
                },
                "data[SocialMedia][try_caviar]": {
                    required: "Please enter the try caviar url.",
                },
            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
        $('#SocialMediaFacebook').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });
        $('#SocialMediaTwitter').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });
        $('#SocialMediaInstagram').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });
        $('#SocialMediaPinterest').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });
        $('#SocialMediaYolo').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });
        $('#SocialMediaGoogle').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });
        $('#SocialMediaYahoo').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });

        $('#SocialMediaYellowPage').change(function () {
            var str = $(this).val();
            if ($.trim(str) === '') {
                $(this).val('');
                $(this).css('border', '1px solid red');
                $(this).focus();
            } else {
                $(this).css('border', '');
            }
        });

        $('#SocialMediaTryCaviar').change(function () {
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


    $(document).ready(function () {
        $(".form-control").change(function () {
            var blankCheck = this.value;
            if (!blankCheck == '') {
                if (!/^http:\/\//.test(this.value) && !/^https:\/\//.test(this.value)) {
                    this.value = "http://" + this.value;
                }
            }
        });
    });

</script>