<!DOCTYPE html>
<html>

    <head>
        <?php //echo $this->Html->charset(); ?>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link rel="shortcut icon" href="/img/favicon.png">
        <title><?php echo $this->Session->read('storeName'); //$this->fetch('title');   ?> </title>
        <?php
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');

        echo $this->Html->css('vertical/bootstrap.min');
        echo $this->Html->css('vertical/master');
        echo $this->Html->css('vertical/custom');
        echo $this->Html->css('vertical/color-theme');
        echo $this->Html->css('vertical/responsiveslides');
        echo $this->Html->css('vertical/font-awesome.min');
        echo $this->Html->css('jquery-ui');
        echo $this->Html->css('remodal');
        echo $this->Html->css('remodal-default-theme');

        echo $this->Html->script('jquery.min');
        echo $this->Html->script('bootstrap.min.js');
        echo $this->Html->script('responsiveslides.min');
        echo $this->Html->script('master');
        echo $this->Html->script('jquery-ui');
        echo $this->Html->script('validation/jquery.validate.js');
        echo $this->Html->script('validation/additional-methods');
        echo $this->Html->script('jquery.maskedinput');
        echo $this->Html->script('remodal.min');
        ?>
        <!--[if lt IE 9]>
        <script src="/js/html5shiv.min.js"></script>
        <script src="/js/respond.min.js"></script>
        <![endif]-->
        <style>
<?php
if (!empty($storeStyle['StoreStyle']['css'])) {
    echo $storeStyle['StoreStyle']['css'];
}
?>
        </style>
    </head>
    <?php
    if (isset($store_data_app['StoreTheme'])) {
        $color_class = $store_data_app['StoreTheme']['name'];
        $color_class1 = "wraper no-background";
    } else {
        $color_class = "theme-one";
        $color_class1 = "wraper";
    }

    if (isset($store_data_app['StoreFont'])) {
        $color_class .= " " . $store_data_app['StoreFont']['class'];
    } else {
        $color_class .= " " . "ff-open-sans";
    }
    ?>
    <body class='<?php echo $color_class; ?>'>
        <?php echo $this->element('vertical_header');
        ?>
        <div class="content">
            <div class="<?php echo $color_class1; ?> clearfix">
                <?php echo $this->element('vertical_menu');
                ?>

                <div class="right-col">

                    <?php
                    if (($this->params['controller'] == 'Users' || $this->params['controller'] == 'users') && ($this->params['action'] == 'login' || $this->params['action'] == 'storePhoto')) {
                        echo $this->element('vertical_banner');
                    }
                    echo $this->fetch('content');
                    ?>
                </div>
            </div>
        </div>
        <?php echo $this->element('modal/error_popup_old') ?>
        <?php echo $this->element('modal/home_page_popup') ?>
        <?php echo $this->element('footer'); ?>
    </body>
</html>
