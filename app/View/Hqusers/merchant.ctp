<!-- FLEX SLIDER -->
<?php echo $this->element('hquser/home/slider'); ?>
<!-- /FLEX SLIDER -->
<?php if (!empty($homeContentData)) { ?>
    <div class="container">
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
<?php } ?>
<!-- LOCATIONS -->
<?php
if (!empty($store)) {
    echo $this->element('hquser/home/content5');
}
?>
<!-- /LOCATIONS -->
<!-- CONTACT US -->
<?php
if (!empty($logoPosition['MerchantConfiguration']['contact_active'])) {
    echo $this->element('hquser/home/contact_us');
}
?>
<!-- -->
<script type="text/javascript">
    $(window).load(function () {
        $('.flexslider').flexslider({
            animation: "slide"
        });
    });

    $('.owl-carousel').owlCarousel({
        loop: true,
        nav: true,
        autoplay: true,
        autoplayTimeout: 2000,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    })
</script>