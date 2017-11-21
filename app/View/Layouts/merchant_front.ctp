<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo $this->Html->charset('UTF-8'); ?>
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=0, target-densityDpi=device-dpi">
        <meta name="description" content="">
        <meta name="author" content="">  
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="shortcut icon" href="/img/favicon.png">
        <title><?php echo $name; ?></title>
        <link href="img/favicon.png" rel="icon"  type="image/png">
        <link rel="shortcut icon" href="img/favicon.ico" type="img/x-icon">
        <?php
        echo $this->Html->css('hq/bootstrap.min');
        echo $this->Html->css('nanumgothic');
//        echo $this->Html->css('hq/developer');
        echo $this->Html->css('hq/font-awesome.min');
        echo $this->Html->css('hq/flexslider');
        echo $this->Html->css('hq/owl.carousel');
        echo $this->Html->css('hq/merchant');
        echo $this->Html->css('jquery-ui');
        echo $this->Html->css('star-rating');
        echo $this->Html->script('hq/jquery.min');

        echo $this->Html->script('hq/jquery.flexslider-min');
        echo $this->Html->script('hq/owl.carousel.min');
//        echo $this->Html->script('hq/html5shiv.min');
//        echo $this->Html->script('hq/respond.min');
        echo $this->Html->script('hq/custom');

        echo $this->Html->script('jquery-ui');
        echo $this->Html->script('hq/bootstrap.min');
        echo $this->Html->script('validation/jquery.validate.min');
        echo $this->Html->script('validation/additional-methods.min');
        echo $this->Html->script('jquery.maskedinput');
        echo $this->Html->script('star-rating');
        echo $this->Html->script('jquery.blockUI.js');
        ?>
    </head>
    <?php if (!empty($layoutCss['MerchantDesign']['merchant_css'])) { ?>
        <style type="text/css">
    <?php echo $layoutCss['MerchantDesign']['merchant_css']; ?>
        </style>
    <?php } ?>
    <style type="text/css">
        <?php
        if (!empty($image)) {
            $image = "/merchantBackground-Image/" . $image;
            ?>
            body {
                background-image: url("<?php echo $image; ?>") !important;
                background-attachment:fixed;
                background-position:0 0;
                background-size:cover;
            }
            <?php
        }
        ?>
    </style>
    <div id="loading">
        <script>
            $.blockUI({css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff'
                }});
        </script>
    </div>
    <body>
        <!-- HEADER -->
        <header>
            <?php echo $this->element('hquser/home/top_header'); ?>
            <?php echo $this->element('hquser/home/menu'); ?>
        </header>
        <!-- HEADER END -->
        <!-- MAIN START -->
        <main class="main">
            <?php echo $this->fetch('content'); ?>
            <?php echo $this->element('modal/home_page_popup') ?>
        </main>
        <!-- MAIN END -->
        <!-- FOOTER -->
        <?php echo $this->element('hquser/home/footer'); ?>
        <!-- FOOTER END -->
        <script>
            $('#vt-hambug').on('click', function () {
                $(".main-menu").toggleClass('show-hamb');
            });
        </script>
    </body>
</html>
<script>
    $(window).load(function () {
        // run code
        $('#loading').addClass('hidden');
        if ($('#loading').hasClass('hidden')) {
            $.unblockUI();
        }
    });
</script>