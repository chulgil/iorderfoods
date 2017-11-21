<?php //echo $this->element('sql_dump');               ?>
<style>
    .shadowClass {
        background-color: white;
        box-shadow: 10px 10px 5px #888888;
        padding: 20px;
        min-height: 490px;
        margin-bottom: 120px;
    }
    .viewTemplate{
        cursor: pointer;
        margin-top: 10px; 
    }
    .btn {
        font-size: 14px !important;
    }
    blockquote {
        font-size: 15px;
    }
    .btn.btn-primary.backToList {
        margin-top: 20px;
    }
    #showList{
        min-height: 490px;
    }
</style>
<div class="content">
    <div class="wrap">
        <div class="col-lg-12">
            <div class="col-md-2 col-sm-2">
                <?php echo $this->element("Merchant/newsletter_sidebar"); ?>
            </div>
            <div class="col-md-10">
                <nav class="navbar navbar-default">
                    <div class="container-fluid">
                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                            <?php
                            $url = array('controller' => 'hqusers', 'action' => 'newsletter');
                            echo $this->Form->create('MerchantNewsletter', array('url' => $url, 'inputDefaults' => array('label' => false, 'div' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), "class" => "navbar-form navbar-left", 'enctype' => 'multipart/form-data'));
                            ?>
                            <div class="form-group">
                                <?php echo $this->Form->input('content_key', array('type' => 'text', 'class' => 'form-control', 'placeholder' => "Keyword", 'label' => FALSE)); ?>
                            </div>
                            <div class="form-group">
                                <?php echo $this->Form->input('start_date', array('type' => 'text', 'class' => 'form-control', 'placeholder' => "Start Date", 'label' => FALSE, 'readonly')); ?>
                                <label id="MerchantNewsletterStartDate-error" class="error" for="MerchantNewsletterStartDate"></label>
                            </div>
                            <div class="form-group">
                                <?php echo $this->Form->input('end_date', array('type' => 'text', 'class' => 'form-control', 'placeholder' => "End Date", 'label' => FALSE, 'readonly')); ?>
                                <label id="MerchantNewsletterEndDate-error" class="error" for="MerchantNewsletterEndDate"></label>
                            </div>
                            <?php echo $this->Form->button('search', array('type' => 'submit', 'class' => 'btn btn-default submitSearch bg-btn-color')); ?>
                            <?php echo $this->Html->link('Clear', "/hqusers/newsletter/clear", array("class" => "btn btn-default bg-btn-color", 'escape' => false)); ?>
                            <?php echo $this->Form->end(); ?>
                        </div><!-- /.navbar-collapse -->
                    </div><!-- /.container-fluid -->
                </nav>
                <div class="col-md-12 shadowClass">
                    <div id="showTemplate" style="display: none;"></div>
                    <div id="showList">
                        <?php
                        if (!empty($merchantNewsletterList)) {
                            foreach ($merchantNewsletterList as $list) {
                                ?>
                                <blockquote>
                                    <h2><?php echo $list['MerchantNewsletter']['name']; ?></h2>
                                    <span><?php echo substr($list['MerchantNewsletter']['content'], 0, 150); ?>...</span>
                                    <footer class="viewTemplate" data-id="<?php echo $this->Encryption->encode($list['MerchantNewsletter']['id']); ?>"><code title="<?php echo $list['MerchantNewsletter']['name']; ?>">Read more..</code></footer>
                                </blockquote>
                                <?php
                            }
                            echo $this->element('pagination');
                        } else {
                            echo "No record found.";
                        }
                        ?>
                         <?php //echo $this->element('pagination'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $(document).on('click', '.submitSearch', function () {
            var sData = $('#MerchantNewsletterStartDate').val();
            var eData = $('#MerchantNewsletterEndDate').val();
            if (sData != "" && eData == "") {
                $('#MerchantNewsletterEndDate-error').html('Please fill end date.');
                return false;
            }
            if (eData != "" && sData == "") {
                $('#MerchantNewsletterStartDate-error').html('Please fill start date.');
                return false;
            }
        });
        $(document).on('click', '.viewTemplate', function () {
            var newsLetterId = $(this).data('id');
            if (newsLetterId) {
                $.ajax(
                        {
                            type: 'post',
                            url: "<?php echo $this->Html->url(array('controller' => 'hqusers', 'action' => 'getMerchantNewsLetterContent')); ?>",
                            data: {merchantNewsLetterId: newsLetterId},
                            success: function (response)
                            {
                                if (response != "") {
                                    $('#showTemplate').html(response).show();
                                    $('#showList').hide();
                                }
                            }
                        });
            } else {
                alert('Something went wrong.');
            }
        });
        $(document).on('click', '.backToList', function () {
            $('#showList').show();
            $('#showTemplate').empty();
        });

        $('#MerchantNewsletterStartDate').datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function (selectedDate) {
                $("#MerchantNewsletterEndDate").datepicker("option", "minDate", selectedDate);
            }

        });
        $('#MerchantNewsletterEndDate').datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function (selectedDate) {
                $("#MerchantNewsletterStartDate").datepicker("option", "maxDate", selectedDate);
            }

        });
    });
</script>