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

        if (DESIGN == 1) {

            echo $this->Html->css('star-rating');
            echo $this->Html->css('remodal');
            echo $this->Html->css('remodal-default-theme');
            //New Theme
            echo $this->Html->css('theme/bootstrap.min');
            echo $this->Html->css('theme/font-awesome.min');
            echo $this->Html->css('theme/master');
            echo $this->Html->css('theme/themes');
            echo $this->Html->css('theme/hamburger');
            echo $this->Html->css('theme/media');
            echo $this->Html->css('theme/bootstrap-datepicker');
            echo $this->Html->css('theme/sumoselect');
            echo $this->Html->css('jquery-ui');
            //New Theme
            //Custom CSS
            echo $this->Html->css('layout/custom');
            //Custom CSS


            echo $this->Html->script('jquery.min');
            echo $this->Html->script('responsiveslides.min');
            echo $this->Html->script('master');
            echo $this->Html->script('jquery-ui');
            echo $this->Html->script('bootstrap.min');
            echo $this->Html->script('validation/jquery.validate');
            echo $this->Html->script('validation/additional-methods');
            echo $this->Html->script('star-rating');
            echo $this->Html->script('jquery.simple-scroll-follow.2.0.1');
            echo $this->Html->script('jquery.maskedinput');
            echo $this->Html->script('remodal.min');
            echo $this->Html->script('theme/custom');
            echo $this->Html->script('theme/jquery.sumoselect');
            echo $this->Html->script('jquery.blockUI');
        } elseif (DESIGN == 2) {

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
        } elseif (DESIGN == 3) {
            echo $this->Html->css('jquery-ui');
            echo $this->Html->css('dasol/bootstrap.min');
            echo $this->Html->css('dasol/font-awesome.min');
            //echo $this->Html->css('dasol/lightbox');
            echo $this->Html->css('dasol/master');
            echo $this->Html->css('dasol/themes');
            echo $this->Html->css('dasol/hamburger');
            echo $this->Html->css('theme/bootstrap-datepicker');
            echo $this->Html->css('dasol/media');
            echo $this->Html->css('star-rating');

            echo $this->Html->script('jquery.min');
            echo $this->Html->script('responsiveslides.min');
            echo $this->Html->script('dasol/lightbox-plus-jquery.min');
            echo $this->Html->script('jquery-ui');
            echo $this->Html->script('bootstrap.min');
            echo $this->Html->script('validation/jquery.validate');
            echo $this->Html->script('validation/additional-methods');
            echo $this->Html->script('star-rating');
            echo $this->Html->script('jquery.maskedinput');
            echo $this->Html->script('jquery.blockUI');
        }
        ?>
        <!--[if lt IE 9]>
            <script src="/js/html5shiv.min.js"></script>
            <script src="/js/respond.min.js"></script>
        <![endif]-->

        <style>
<?php
if (!empty($store_data_app['Store']['background_image'])) {
    $image = "/storeBackground-Image/" . $store_data_app['Store']['background_image'];
} else {
    $image = "";
}
?>
            body {
                background: url("<?php echo $image; ?>") no-repeat center center fixed;
            }
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
    if (DESIGN == 1) {
        $addclass = "theme-one";
        $addclassmain = "Arron horizontal layout";
    } elseif (DESIGN == 2) {
        $addclass = "chole-main";
        $addclassmain = 'CHT2 CHT2-header-change Chole-theme-two';
        if ($this->params['action'] == 'login' && $this->params['controller'] == 'users') {
            $addclass = 'chole-main Chole-vertical-layout2';
            $addclassmain = 'CHT2 Chole-theme-two';
        }
    } elseif (DESIGN == 3) {
        $addclass = "";
        $addclassmain = "theme-green risen-hz";
    }
    ?>

    <body class="<?php echo $addclass; ?>">
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
        </script>
    </body>
</html>

