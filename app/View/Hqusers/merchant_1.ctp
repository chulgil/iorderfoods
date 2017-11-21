<!-- FLEX SLIDER -->
<?php echo $this->element('hquser/home/slider'); ?>
<!-- /FLEX SLIDER -->

<!-- HOW IT WORKS -->
<?php echo $this->element('hquser/home/content1'); ?>
<!-- /HOW IT WORKS -->

<!-- -->
<?php echo $this->element('hquser/home/content2'); ?>
<!-- -->

<!-- NEWSLETTER -->
<?php //echo $this->element('hquser/home/content3'); ?>
<!-- /NEWSLETTER -->

<!-- IMAGE CROUSAL -->
<?php echo $this->element('hquser/home/content4'); ?>
<!-- /IMAGE CROUSAL -->

<!-- LOCATIONS -->
<?php echo $this->element('hquser/home/content5'); ?>

<!-- /LOCATIONS -->

<!-- CONTACT US -->
<?php echo $this->element('hquser/home/contact_us'); ?>
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

    $('#vt-hambug').on('click', function () {
        $(".main-menu").toggleClass('show-hamb');
    });
</script>