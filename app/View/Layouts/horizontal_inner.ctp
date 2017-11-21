<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link rel="shortcut icon" href="/img/favicon.png">
        <title>Restaurant Online Ordering System</title>
        <!--        <meta property="og:url"           content="http://biorf.com/pannels/myBookings/Ng/Ng" />
                <meta property="og:type"          content="website" />
                <meta property="og:title"         content="Booking Request" />
                <meta property="og:description"   content="This is testing of facebook sharer" />
                <meta property="og:image"         content="http://biorf.com/img/no_image.jpg" />-->
        <?php
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');

        echo $this->Html->css('horizontal/bootstrap.min');
        echo $this->Html->css('horizontal/font-awesome.min');
        echo $this->Html->css('horizontal/master');
        echo $this->Html->css('horizontal/custom');
        echo $this->Html->css('horizontal/color-theme');
        echo $this->Html->css('horizontal/responsiveslides');
        echo $this->Html->css('jquery-ui');
        echo $this->Html->css('star-rating');

        echo $this->Html->script('jquery.min');
        echo $this->Html->script('bootstrap.min.js');
        echo $this->Html->script('responsiveslides.min');
        echo $this->Html->script('master');
        echo $this->Html->script('jquery-ui');
        echo $this->Html->script('validation/jquery.validate.js');
        echo $this->Html->script('validation/additional-methods');
        echo $this->Html->script('star-rating');
        echo $this->Html->script('jquery.simple-scroll-follow.2.0.1');
        echo $this->Html->script('jquery.maskedinput');
        ?>
        <!--[if lt IE 9]>
      <script src="/js/html5shiv.min.js"></script>
      <script src="/js/respond.min.js"></script>
    <![endif]-->
        <style>
            #loading {
                width:100%;
                height:100%;
                background-color: black;
                opacity: 0.54;
                position:fixed;
                z-index: 999;
                display:none;
            }
            .ajax-loader {
                position: absolute;
                left: 50%;
                top: 50%;
                margin-left: -32px; /* -1 * image width / 2 */
                margin-top: -32px; /* -1 * image height / 2 */
            }

            <?php
            if (!empty($store_data_app['Store']['background_image'])) {
                $image = "/storeBackground-Image/" . $store_data_app['Store']['background_image'];
            } else {
                $image = "";
            }
            if (!empty($image)) {
                ?>
                body {
                    background: url("<?php echo $image; ?>") no-repeat center center / 100% fixed;
                }
                <?php
            }
            if (!empty($storeStyle['StoreStyle']['css'])) {
                echo $storeStyle['StoreStyle']['css'];
            }
            ?>
        </style>
    </head>


    <?php
    if (isset($store_data_app['StoreTheme'])) {
        $color_class = $store_data_app['StoreTheme']['name'];
    } else {
        $color_class = "theme-one";
    }
    if (isset($store_data_app['StoreFont'])) {
        $color_class.= " " . $store_data_app['StoreFont']['class'];
    } else {
        $color_class.= " " . "ff-open-sans";
    }
    ?>
    <?php if (($this->params['controller'] == 'products' || $this->params['controller'] == 'Products') && ($this->params['action'] == 'items')) { ?>
        <body class='<?php echo $color_class; ?>  fluid-footer-body'>
        <?php } else { ?>
        <body class='<?php echo $color_class; ?>'>
        <?php } ?>

        <div id="loading">
            <img src="/img/ajax-loader.gif" class="ajax-loader">
        </div>
        <?php echo $this->element('horizontal_header'); ?>
        <div class="content">
            <div class="wrap">
                <?php
                if (($this->params['controller'] == 'products' || $this->params['controller'] == 'Products') && ($this->params['action'] == 'items')) {

                    echo $this->element('order_item');
                }
                echo $this->fetch('content');
                ?>
            </div>
            <?php echo $this->element('modal/error_popup_old') ?>
            <?php echo $this->element('modal/home_page_popup') ?>
        </div>
        <?php echo $this->element('footer'); ?>

    </body>
</html>
