<!-- static-banner -->
<?php echo $this->element('hquser/static_banner'); ?>
<!-- /banner -->
<!--<div class="location common-padding">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="common-title clearfix">
                    <span class="yello-dash"></span>
                    <h2><?php echo $content['MerchantContent']['name']; ?></h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
<?php echo $content['MerchantContent']['content']; ?>
            </div>
        </div>
    </div>
</div>-->
<?php if (!empty($homeContentData)) { ?>
    <div class="location common-padding">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="common-title clearfix">
                        <span class="yello-dash"></span>
                        <h2><?php echo $content['MerchantContent']['name']; ?></h2>
                    </div>
                </div>
            </div>
            <?php
            foreach ($homeContentData as $cLayoutData1) {
                ?>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="row" style="padding-top: 15px;">
                            <?php
                            foreach ($cLayoutData1 as $cLayoutData) { //pr($cLayoutData);
                                if (!empty($cLayoutData['LayoutBox'])) {
                                    if ($cLayoutData['LayoutBox']['ratio'] == 100.00) {
                                        $class = 'col-sm-12 col-xs-12';
                                    } elseif ($cLayoutData['LayoutBox']['ratio'] == 50.00) {
                                        $class = "col-sm-6 col-xs-12";
                                    } elseif ($cLayoutData['LayoutBox']['ratio'] == 33.33) {
                                        $class = "col-sm-4 col-xs-12";
                                    } elseif ($cLayoutData['LayoutBox']['ratio'] == 25.00) {
                                        $class = "col-sm-3 col-xs-12";
                                    } elseif ($cLayoutData['LayoutBox']['ratio'] == 66.66) {
                                        $class = "col-sm-8 col-xs-12";
                                    } elseif ($cLayoutData['LayoutBox']['ratio'] == 75.00) {
                                        $class = "col-sm-9 col-xs-12";
                                    }
                                    ?>
                                    <div class="<?php echo $class; ?>" ><?php echo $cLayoutData['HomeContent']['content'] ?></div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>

            <?php }
            ?>
        </div>
    </div>
<?php } ?>