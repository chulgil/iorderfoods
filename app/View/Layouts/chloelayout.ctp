<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<!DOCTYPE html>
<html>
    <head>
        <?php //echo $this->Html->charset();  ?>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link rel="shortcut icon" href="/img/favicon.png">
        <title>
            <?php echo $this->Session->read('storeName'); ?>
        </title>

        <?php
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        echo $this->Html->css('nanumgothic');
        echo $this->Html->css('master_css/master');
        echo $this->Html->css('chloe/master');
        if (!empty($store_data_app['Store']['store_theme_id']) && $store_data_app['Store']['store_theme_id'] == 14) {
            echo $this->Html->css('theme_css/theme_14');
        } elseif (!empty($store_data_app['Store']['store_theme_id']) && $store_data_app['Store']['store_theme_id'] == 15) {
            echo $this->Html->css('theme_css/theme_15');
        } elseif (!empty($store_data_app['Store']['store_theme_id']) && $store_data_app['Store']['store_theme_id'] == 16) {
            echo $this->Html->css('theme_css/theme_16');
        }
        echo $this->Html->css('star-rating');
        echo $this->Html->css('jquery-ui');
        echo $this->Html->css('theme/bootstrap.min');
        echo $this->Html->css('theme/font-awesome.min');
        echo $this->Html->css('theme/owl.carousel');
        echo $this->Html->css('theme/owl.transitions');
        echo $this->Html->css('theme/owl.theme');
        echo $this->Html->css('theme/themes');
        echo $this->Html->css('theme/hamburger');
        echo $this->Html->css('theme/bootstrap-datepicker');
        echo $this->Html->css('theme/sumoselect');
        echo $this->Html->css('theme/master');
        if (KEYWORD == 'IOF-C1-V') {
            echo $this->Html->css('chloe/v-master_c1');
        } elseif (KEYWORD == 'IOF-C2-V') {
            echo $this->Html->css('chloe/v-master_c2');
        } elseif (KEYWORD == 'IOF-C3-V') {
            echo $this->Html->css('chloe/v-master_c3');
        } else {
            echo $this->Html->css('chloe/master');
        }
        echo $this->Html->css('theme/media');


        echo $this->Html->script('jquery.min');
        echo $this->Html->script('responsiveslides.min');
        echo $this->Html->script('master');
        echo $this->Html->script('jquery-ui');
        echo $this->Html->script('bootstrap.min');
        echo $this->Html->script('validation/jquery.validate');
        echo $this->Html->script('validation/additional-methods');
        echo $this->Html->script('star-rating');
        echo $this->Html->script('jquery.maskedinput');
        echo $this->Html->script('custom');
        echo $this->Html->script('owl.carousel.min');
        echo $this->Html->script('jquery.sumoselect');
        //echo $this->Html->script('bootstrap-datepicker.min');
        echo $this->Html->script('jquery.blockUI');
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
if (!empty($store_data_app['Store']['background_image'])) {
    $image = "/storeBackground-Image/" . $store_data_app['Store']['background_image'];
    ?>
                            
                body {
                    background: url("<?php echo $image; ?>") no-repeat center center fixed;
                }
                            
<?php }
?>
        </style>
    </head>


    <?php
//    if (isset($store_data_app['StoreTheme'])) {
//        $color_class = $store_data_app['StoreTheme']['name'];
//        $color_class1 = "wraper no-background";
//    } else {
//        $color_class = "theme-one";
//        $color_class1 = "wraper";
//    }
//
//
//
//    if (isset($store_data_app['StoreFont'])) {
//        $color_class .= " " . $store_data_app['StoreFont']['class'];
//    } else {
//        $color_class .= " " . "ff-open-sans";
//    }
    ?>

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
    <?php
    if (NAVIGATION == 1) {
        $addclassbody = $store_data_app['StoreTheme']['body_vertical'];
        $addclassmain = $store_data_app['StoreTheme']['main_vertical'];
    } else {
        $addclassbody = $store_data_app['StoreTheme']['body_horizontal'];
        $addclassmain = $store_data_app['StoreTheme']['main_horizontal'];
    }

    //for log type
    if ($store_data_app['Store']['logotype'] == 1) {
        $logoType = 'LOGO-SQUARE';
    } elseif ($store_data_app['Store']['logotype'] == 2) {
        $logoType = 'LOGO-RECTANGLE';
    } else {
        $logoType = '';
    }


//    $addclass = "chole-main";
//    $addclassmain = 'CHT2 CHT2-header-change Chole-theme-two';
//    if ($this->params['action'] == 'login' && $this->params['controller'] == 'users') {
//        $addclass = 'chole-main Chole-vertical-layout2';
//        $addclassmain = 'CHT2 Chole-theme-two';
//    }

    if (($this->params['action'] != 'login')) {
        ?>
        <body class="<?php echo $addclassbody . ' ' . KEYWORD . ' ' . SELECTEDTHEME . ' ' . $logoType; ?> INNER-PAGE">
        <?php } else { ?>
        <body class="<?php echo $addclassbody . ' ' . KEYWORD . ' ' . SELECTEDTHEME . ' ' . $logoType; ?>">
        <?php }
        ?>
<!--    <body class="<?php //echo $addclassbody . ' ' . KEYWORD . ' ' . SELECTEDTHEME;               ?>">-->
        <div class="<?php echo $addclassmain; ?>">
            <?php echo $this->element('layout/header'); ?>
            <main class="main-body">
                <?php echo $this->fetch('content'); ?>
            </main>
            <?php echo $this->element('footer'); ?>
            <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <?php echo $this->element('userlogin/login'); ?>
            </div>
        </div>
        <div class="modal fade add-info" id="tAndPModal" role="dialog">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content" id="tAndPContent">
                </div>
            </div>
        </div>
        <div class="modal fade add-info item-modal" id="item-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        </div>

        <div class="modal fade add-info review-modal" id="review-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        </div>
        <?php echo $this->element('userlogin/guest_sign_up'); ?>
        <?php echo $this->element('modal/error_popup_old') ?>
        <?php echo $this->element('modal/home_page_popup') ?>    
        <script>
            $(window).load(function () {
                // run code
                $('#loading').addClass('hidden');
                if ($('#loading').hasClass('hidden')) {
                    $.unblockUI();
                }
            });
            $(document).on('click', ".termAndPolicy", function () {
                var type = $(this).data('name');
                $.ajax({
                    type: 'post',
                    url: "<?php echo $this->Html->url(array('controller' => 'stores', 'action' => 'getTermsAndPolicyData')); ?>",
                    data: {'type': type},
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
                    },
                    success: function (response) {
                        $("#tAndPContent").html(response);
                        $("#tAndPModal").modal('show');
                    }
                });
            });
            $(document).on('click', ".guest-login-popup", function () {
                $("#login-modal").modal("hide");
                $("#guest-sign-up-modal").modal("show");
            });
        </script>
    </body>
</html>

