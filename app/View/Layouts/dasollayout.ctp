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
        echo $this->Html->css('jquery-ui');
        echo $this->Html->css('dasol/bootstrap.min');
        echo $this->Html->css('dasol/font-awesome.min');
        echo $this->Html->css('master_css/master');
        echo $this->Html->css('dasol/master');
        if (!empty($store_data_app['Store']['store_theme_id']) && $store_data_app['Store']['store_theme_id'] == 17) {
            echo $this->Html->css('theme_css/theme_17');
        } elseif (!empty($store_data_app['Store']['store_theme_id']) && $store_data_app['Store']['store_theme_id'] == 18) {
            echo $this->Html->css('theme_css/theme_18');
        } elseif (!empty($store_data_app['Store']['store_theme_id']) && $store_data_app['Store']['store_theme_id'] == 19) {
            echo $this->Html->css('theme_css/theme_19');
        } elseif (!empty($store_data_app['Store']['store_theme_id']) && $store_data_app['Store']['store_theme_id'] == 20) {
            echo $this->Html->css('theme_css/theme_20');
        }
        echo $this->Html->css('theme/owl.carousel');
        echo $this->Html->css('theme/owl.transitions');
        echo $this->Html->css('theme/owl.theme');
        //echo $this->Html->css('dasol/lightbox');
        echo $this->Html->css('lightbox/lightbox');
        if (KEYWORD == 'IOF-D2-H') {
            echo $this->Html->css('dasol/jquery.bxslider');
            echo $this->Html->css('dasol/master_d2');
        } elseif (KEYWORD == 'IOF-D2-V') {
            echo $this->Html->css('dasol/jquery.bxslider');
            echo $this->Html->css('dasol/v-master_d2');
        } elseif (KEYWORD == 'IOF-D3-H') {
            echo $this->Html->css('dasol/master_d3');
        } elseif (KEYWORD == 'IOF-D3-V') {
            echo $this->Html->css('dasol/v-master_d3');
        } elseif (KEYWORD == 'IOF-D4-H') {
            echo $this->Html->css('dasol/jquery.bxslider');
            echo $this->Html->css('dasol/master_d4');
        } elseif (KEYWORD == 'IOF-D4-V') {
            echo $this->Html->css('dasol/jquery.bxslider');
            echo $this->Html->css('dasol/v-master_d4');
        } elseif (KEYWORD == 'IOF-D1-H') {
            echo $this->Html->css('dasol/master_d1');
        } elseif (KEYWORD == 'IOF-D1-V') {
            echo $this->Html->css('dasol/v-master_d1');
        }
        echo $this->Html->css('dasol/themes');
        echo $this->Html->css('dasol/hamburger');
        echo $this->Html->css('theme/bootstrap-datepicker');
        echo $this->Html->css('dasol/media');
        echo $this->Html->css('star-rating');

        echo $this->Html->script('jquery.min');
        echo $this->Html->script('responsiveslides.min');
        //echo $this->Html->script('dasol/lightbox-plus-jquery.min');
        echo $this->Html->script('jquery-ui');
        echo $this->Html->script('bootstrap.min');
        echo $this->Html->script('owl.carousel.min');
        if (in_array(KEYWORD, array('IOF-D2-H', 'IOF-D2-V', 'IOF-D4-H', 'IOF-D4-V'))) {
            echo $this->Html->script('dasol/jquery.bxslider');
        }
        echo $this->Html->script('validation/jquery.validate');
        echo $this->Html->script('validation/additional-methods');
        echo $this->Html->script('star-rating');
        echo $this->Html->script('jquery.maskedinput');
        echo $this->Html->script('jquery.blockUI');
        echo $this->Html->script('lightbox/lightbox');
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
                    background: url("<?php echo $image; ?>") no-repeat center top fixed;
                }
                .title-bar{
                    background-image:url("<?php echo $image; ?>");
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
    //$addclass = "";
    //$addclassmain = "theme-green risen-hz";
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
    if ($store_data_app['Store']['is_store_logo'] == 1) {
        $logoType = '';
    }
    ?>
    <?php if (($this->params['controller'] != 'users') && ($this->params['action'] != 'login')) { ?>
        <body class="<?php echo KEYWORD . ' ' . SELECTEDTHEME . ' ' . $logoType; ?> INNER-PAGE">
        <?php } else { ?>
        <body class="<?php echo KEYWORD . ' ' . SELECTEDTHEME . ' ' . $logoType; ?>">
        <?php }
        ?>

        <div class="<?php echo $addclassmain; ?>">
            <?php
            if (KEYWORD == 'IOF-D4-H' || KEYWORD == 'IOF-D4-V') {
                echo $this->element('design/dasol/header_d4');
            } elseif (KEYWORD == 'IOF-D3-V') {
                echo $this->element('design/dasol/header_d3');
            } elseif (KEYWORD == 'IOF-D1-V') {
                echo $this->element('design/dasol/header/v_1');
            } else {
                echo $this->element('layout/header');
            }
            ?>
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
            /*function isScrolledIntoView(elem) {
             var docViewTop = $(window).scrollTop();
             var docViewBottom = docViewTop + $(window).height();
             
             var elemTop = $(elem).offset().top;
             var elemBottom = elemTop + $(elem).height();
             
             return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
             }
             $(window).scroll(function () {
             $('.footer-1').each(function () {
             if (isScrolledIntoView(this) === true) {
             $('.bottom-header').addClass('bottom')
             } else {
             $('.bottom-header').removeClass('bottom')
             }
             });
             });*/
<?php if (KEYWORD == 'IOF-D3-H') { ?>
                $(window).scroll(function () {
                    $('footer').removeClass('bottom')
                    if ($(window).scrollTop() + $(window).height() > ($(document).height() - 100)) {
                        //you are at bottom
                        $('footer').addClass('bottom')
                    }
                });
<?php } ?>
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
            $('#login-modal').on('shown.bs.modal', function () {
                $("#guest-sign-up-modal").modal("hide");
            });

            $(document).on('click', '.Slider-text .close', function () {
                $('.Slider-text-vertical').fadeOut(200);
            });
            $('.carousel').carousel({
                interval: 4000
            });
        </script>
    </body>
</html>

