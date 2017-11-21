<style>
    .pull-right.date-format { clear:both;display: block;font-size: 12px;margin-bottom: 5px;text-align: left;width: 100%;}
</style>
<!-- static-banner -->
<?php echo $this->element('hquser/static_banner'); ?>
<!-- /banner -->
<!-- NEWSLWTTER -->
<div class="newsletter-frame">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="common-title clearfix">
                    <span class="yello-dash"></span>
                    <h2>Newsletter</h2>
                </div>
            </div>
        </div>
        <div class="row" style="position:relative;">
            <div class="col-sm-12">
                <nav class="navbar navbar-default newsletter-page-wrap">
                    <div class="container-fluid">
                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <div id="bs-newsletter">
                            <?php
                            $url = array('controller' => 'hqusers', 'action' => 'newsletter');
                            echo $this->Form->create('Newsletter', array('url' => $url, 'inputDefaults' => array('label' => false, 'div' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), "class" => "navbar-form navbar-left", 'enctype' => 'multipart/form-data'));
                            ?>
                            <div class="form-group">
                                <?php echo $this->Form->input('content_key', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => "Name", 'label' => FALSE)); ?>
                            </div>
                            <div class="form-group">
                                <?php echo $this->Form->input('start_date', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => "Start Date", 'label' => FALSE, 'readonly')); ?>
                                <label id="MerchantNewsletterStartDate-error" class="error" for="MerchantNewsletterStartDate"></label>
                            </div>
                            <div class="form-group">
                                <?php echo $this->Form->input('end_date', array('type' => 'text', 'class' => 'form-control custom-text', 'placeholder' => "End Date", 'label' => FALSE, 'readonly')); ?>
                                <label id="MerchantNewsletterEndDate-error" class="error" for="MerchantNewsletterEndDate"></label>
                            </div>
                            <?php echo $this->Form->button('SEARCH', array('type' => 'submit', 'class' => 'contact-btn newsletterSearch submitSearch')); ?>
                            <?php echo $this->Html->link('CLEAR', "/hqusers/newsletter/clear", array("class" => "contact-btn newsletterSearch", 'escape' => false)); ?>
                            <?php echo $this->Form->end(); ?>
                        </div><!-- /.navbar-collapse -->
                    </div><!-- /.container-fluid -->
                </nav>
            </div>
            
            <!-- NEWSLETTER SIDEBAR -->
            <aside class="newsletter-sidebar col-xs-12 col-sm-5 col-md-4 pull-right">
                <div class="sidebar-box">
                    <h2>Recent Post</h2>
                    <?php
                    if (!empty($merchantNewsletterRecentPost)) {
                        $i = 0;
                        foreach ($merchantNewsletterRecentPost as $rList) {
                            $name = $rList['Newsletter']['name'];
                            $active = "";
                            if ($this->Encryption->encode($rList['Newsletter']['id']) == @$this->params['url']['val']) {
                                $active = 'active';
                            }
                            $oldVal = strlen($name) > 55 ? substr($name, 0, 55) . "..." : $name;

                            $val = '<span class="pull-right date-format">' . date("M-d-Y", strtotime($rList['Newsletter']['created'])) . '</span><i class="fa fa-angle-right" aria-hidden="true"></i>&nbsp;' . $oldVal;
                            echo $this->Html->link($val, array('controller' => 'hqusers', 'action' => 'newsletter?val=' . $this->Encryption->encode($rList['Newsletter']['id'])), array('escape' => false, 'class' => 'list-group-item ' . @$active));
                            $i++;
                            if ($i == 3)
                                break;
                        }
                    }
                    ?>
                </div>
                <div class="sidebar-box">
                    <h2>Archive</h2>
                    <?php
                    if (!empty($merchantNewsletterArchive)) {
                        foreach ($merchantNewsletterArchive as $aList) {
                            $monthName = $aList[0]['monthname'];
                            $month = $aList[0]['month'];
                            $year = date("Y", strtotime($aList['Newsletter']['created']));
                            $active = "";
                            if (($month == @$archiveSelect['Newsletter']['month']) && ($year == @$archiveSelect['Newsletter']['year'])) {
                                $active = 'active';
                            }
                            $val = '<i class="fa fa-folder-open-o"></i> ' . $monthName . ' ' . $year . '<span class="badge">' . @$aList[0]['count'] . '</span>';
                            echo $this->Html->link($val, array('controller' => 'hqusers', 'action' => 'newsletter', $month, $year), array('escape' => false, 'class' => 'list-group-item ' . @$active));
                        }
                    }
                    ?>
                </div>
            </aside>
            
            <div class="newsletter-content col-xs-12 col-sm-7 col-md-8 pull-left">
                <div id="showTemplate"></div>
                <div id="showList">
                    <div class="nl-wrap clearfix">
                        <?php
                        if (!empty($merchantNewsletterList)) {
                            foreach ($merchantNewsletterList as $list) {
                                ?>
                                <div class="newsletter-box">
                                    <h2><?php echo $list['Newsletter']['name']; ?></h2>
                                    <div class="clearfix">
                                        <?php echo substr($list['Newsletter']['content'], 0, 150); ?>
                                    </div>
                                    <span class="read-more viewTemplate" data-id="<?php echo $this->Encryption->encode($list['Newsletter']['id']); ?>">
                                        <a href="javascript:void(0);">
                                            read more...
                                        </a>
                                    </span>
                                </div>
                                <?php
                            }
                        } else {
                            echo "No record found.";
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="pgn-wrap">
                <div class="pgn-inner-wrap">
                    <?php echo $this->element('pagination');?>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
    $(document).ready(function () {
        $(document).on('click', '.submitSearch', function () {
            var sData = $('#NewsletterStartDate').val();
            var eData = $('#NewsletterEndDate').val();
            if (sData != "" && eData == "") {
                $('#NewsletterEndDate-error').html('Please fill end date.');
                return false;
            }
            if (eData != "" && sData == "") {
                $('#NewsletterStartDate-error').html('Please fill start date.');
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

        $('#NewsletterStartDate').datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function (selectedDate) {
                $("#NewsletterEndDate").datepicker("option", "minDate", selectedDate);
            }

        });
        $('#NewsletterEndDate').datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function (selectedDate) {
                $("#NewsletterStartDate").datepicker("option", "maxDate", selectedDate);
            }

        });
    });
</script>