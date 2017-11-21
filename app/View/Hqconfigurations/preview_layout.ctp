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
                background: url("<?php echo $image; ?>") !important;
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
            <style>
                .icon-box {    
                    padding: 3px 7px;
                }
            </style>
            <div class="site-info">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="pull-left">
                                <div>
                                    <i class="fa fa-phone"></i>
                                    <span>
                                        <a href="tel:<?php echo $phone; ?>"><?php echo $phone; ?></a>
                                    </span>
                                </div>
                                <div>
                                    <i class="fa fa-envelope"></i>
                                    <span>
                                        <a href="mailto:<?php echo $m_email; ?>"><?php echo $m_email; ?></a>
                                    </span>
                                </div>
                            </div>
                            <div class="pull-right">
                                <a href="javascript:void(0)" id="log-in-pop">LOG IN</a>
                                <span>
                                    <?php echo $this->Html->image('hq/seprator-dots.png', array('alt' => 'dots')) ?>
                                </span>
                                <a href="javascript:void(0)">SIGN UP</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            if (!empty($logoType) && $logoType == 1) {//Square
                $logoStyle = "max-width:185px;max-height=>185px;";
            } elseif (!empty($logoType) && $logoType == 2) {//Rectangle
                $logoStyle = "max-width:300px;max-height=>120px;";
            }
            if (!empty($logoPosition['MerchantConfiguration']['logo_position']) && $logoPosition['MerchantConfiguration']['logo_position'] == 1) {
                ?>
                <div class="header header-theme1">
                    <!-- -->
                    <div class="container">
                        <div class="row text-center">
                            <h1 class="col-xs-12 ">
                                <?php
                                if (!empty($logo)) {
                                    echo $this->Html->image('/merchantLogo/' . $logo, array('style' => $logoStyle));
                                } else {
                                    echo $this->Html->link($name, array('controller' => 'hqusers', 'action' => 'merchant'), array('escape' => false));
                                }
                                ?>
                <!--                <a href="javascript:void(0)"><img src="img/hq/header-logo.png" alt="Logo" /></a>-->
                            </h1>
                            <nav class="col-xs-12">
                                <ul class="list-style-none main-menu">
                                    <?php
                                    if (!empty($merchantList)) {
                                        foreach ($merchantList as $content) {

                                            if ($content['MerchantContent']['page_position'] == 1) {
                                                if ($content['MerchantContent']['name'] == 'HOME') {
                                                    ?>
                                                    <li>
                                                        <a href = "javascript:void(0)"><?php echo strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']); ?></a>
                                                    </li>
                                                <?php } elseif ($content['MerchantContent']['name'] == 'LOCATIONS') {
                                                    ?>
                                                    <li>
                                                        <a href = "javascript:void(0)"><?php echo strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']); ?></a>
                                                    </li>
                                                <?php } elseif ($content['MerchantContent']['name'] == 'GALLERY') { ?>
                                                    <li class="locate <?php
                                                    if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'gallery') {
                                                        echo 'active';
                                                    }
                                                    ?>">
                                                        <a href = "javascript:void(0)"><?php echo strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']); ?></a>
                                                    </li>
                                                <?php } elseif ($content['MerchantContent']['name'] == 'NEWSLETTER') { ?>
                                                    <li class="locate <?php
                                                    if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'newsletter') {
                                                        echo 'active';
                                                    }
                                                    ?>">
                                                        <a href = "javascript:void(0)"><?php echo strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']); ?></a></li>
                                                <?php } elseif ($content['MerchantContent']['name'] == 'PROMOTIONS') { ?>
                                                    <li class="locate <?php
                                                    if ($this->params['controller'] == 'hqdeals' && $this->params['action'] == 'index') {
                                                        echo 'active';
                                                    }
                                                    ?>">
                                                        <a href = "javascript:void(0)"><?php echo strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']); ?></a></li>
                                                <?php } else {
                                                    ?>
                                                    <li class="locate <?php
                                                    if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'staticContent') {
                                                        echo 'active';
                                                    }
                                                    ?>">
                                                        <a href = "javascript:void(0)"><?php echo strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']); ?></a>
                                                    </li>

                                                    <?php
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                    <?php if (!empty($merchantList)) { ?>
                                        <?php
                                        $m = 0;
                                        foreach ($merchantList as $content) {
                                            if ($content['MerchantContent']['page_position'] == 3) {
                                                if ($m == 0) {
                                                    ?>
                                                    <li>
                                                        <a href="javascript:void(0)">MORE INFO</a>
                                                        <ul class="list-style-none">

                                                        <?php }
                                                        ?>


                                                        <li class='<?php if (($this->params['controller'] == 'hqusers') && ($this->params['action'] == 'staticContent')) echo 'active'; ?>'><a href="javascript:void(0)"><?php echo strtoupper($content['MerchantContent']['name']); ?></a></li>
                                                        <?php
                                                        $m++;
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </nav>
                            <div class="o-grid__item">
                                <button  id="vt-hambug" class="c-hamburger c-hamburger--htx " >
                                    <span>toggle menu</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- -->
                </div>
            <?php }elseif (!empty($logoPosition['MerchantConfiguration']['logo_position']) && $logoPosition['MerchantConfiguration']['logo_position'] == 2) { ?>
                <!-- HEADER THEME 2 -->
                <div class="header header-theme2">
                    <div class="container theme2">
                        <div class="row text-center">
                            <h1 class="col-xs-12 col-sm-3">
                                <?php
                                if (!empty($logo)) {
                                    echo $this->Html->image('/merchantLogo/' . $logo, array('style' => $logoStyle));
                                } else {
                                    echo $this->Html->link($name, array('controller' => 'hqusers', 'action' => 'merchant'), array('escape' => false));
                                }
                                ?>
                            </h1>
                            <nav class="col-xs-12 col-sm-9">
                                <ul class="list-style-none main-menu">

                                    <?php
                                    if (!empty($merchantList)) {
                                        foreach ($merchantList as $content) {

                                            if ($content['MerchantContent']['page_position'] == 1) {
                                                if ($content['MerchantContent']['name'] == 'HOME') {
                                                    ?>
                                                    <li>
                                                        <a href="javascript:void(0)"><?php echo strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']); ?></a>                                             
                                                    </li>
                                                <?php } elseif ($content['MerchantContent']['name'] == 'LOCATIONS') {
                                                    ?>
                                                    <li>
                                                        <a href="javascript:void(0)"><?php echo strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']); ?></a>                                             
                                                    </li>
                                                <?php } elseif ($content['MerchantContent']['name'] == 'GALLERY') { ?>
                                                    <li class="locate <?php
                                                    if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'gallery') {
                                                        echo 'active';
                                                    }
                                                    ?>">
                                                        <a href="javascript:void(0)"><?php echo strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']); ?></a>
                                                    </li>
                                                <?php } elseif ($content['MerchantContent']['name'] == 'NEWSLETTER') { ?>
                                                    <li class="locate <?php
                                                    if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'newsletter') {
                                                        echo 'active';
                                                    }
                                                    ?>">
                                                        <a href="javascript:void(0)"><?php echo strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']); ?></a>

                                                    </li>
                                                <?php } elseif ($content['MerchantContent']['name'] == 'PROMOTIONS') { ?>
                                                    <li class="locate <?php
                                                    if ($this->params['controller'] == 'hqdeals' && $this->params['action'] == 'index') {
                                                        echo 'active';
                                                    }
                                                    ?>">
                                                        <a href="javascript:void(0)"><?php echo strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']); ?></a>
                                                    </li>
                                                <?php } else {
                                                    ?>
                                                    <li class="locate <?php
                                                    if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'staticContent') {
                                                        echo 'active';
                                                    }
                                                    ?>">
                                                        <a href="javascript:void(0)"><?php echo strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']); ?></a>

                                                    </li>

                                                    <?php
                                                }
                                            }
                                        }
                                    }
                                    ?> 
                                    <?php if (!empty($merchantList)) { ?>
                                        <?php
                                        $m = 0;
                                        foreach ($merchantList as $content) {
                                            if ($content['MerchantContent']['page_position'] == 3) {
                                                if ($m == 0) {
                                                    ?>
                                                    <li>
                                                        <a href="javascript:void(0)">MORE INFO</a>
                                                        <ul class="list-style-none">

                                                        <?php }
                                                        ?>


                                                        <li class='<?php if (($this->params['controller'] == 'hqusers') && ($this->params['action'] == 'staticContent')) echo 'active'; ?>'><a href="javascript:void(0)"><?php echo strtoupper($content['MerchantContent']['name']); ?></a></li>
                                                        <?php
                                                        $m++;
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </nav>
                            <div class="o-grid__item">
                                <button  id="vt-hambug" class="c-hamburger c-hamburger--htx " >
                                    <span>toggle menu</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } elseif (!empty($logoPosition['MerchantConfiguration']['logo_position']) && $logoPosition['MerchantConfiguration']['logo_position'] == 3) { ?>
                <!-- HEADER THEME 3 -->
                <div class="header header-theme3">
                    <div class="container theme3">
                        <div class="row text-center">
                            <h1 class="col-xs-12 logo">
                                <?php
                                if (!empty($logo)) {
                                    echo $this->Html->image('/merchantLogo/' . $logo, array('style' => $logoStyle));
                                } else {
                                    echo $this->Html->link($name, array('controller' => 'hqusers', 'action' => 'merchant'), array('escape' => false));
                                }
                                ?>
                            </h1>
                            <nav class="col-xs-12">
                                <ul class="list-style-none main-menu">                        
                                    <?php
                                    if (!empty($merchantList)) {
                                        $i = 0;
                                        $j = 0;
                                        foreach ($merchantList as $content) {
                                            if ($content['MerchantContent']['page_position'] == 1) {
                                                $i++;
                                            }
                                        }

                                        foreach ($merchantList as $content) {

                                            if ($content['MerchantContent']['page_position'] == 1) {
                                                $v = ceil($i / 2);
                                                if ($v == $j) {
                                                    ?>
                                                    <li class="mobile-menu-logo">
                                                        <?php
                                                        if (!empty($logo)) {
                                                            echo $this->Html->image('/merchantLogo/' . $logo, array('style' => $logoStyle));
                                                        } else {
                                                            echo $this->Html->link($name, array('controller' => 'hqusers', 'action' => 'merchant'), array('escape' => false));
                                                        }
                                                        ?>
                                                    </li>

                                                    <?php
                                                }
                                                if ($content['MerchantContent']['name'] == 'HOME') {
                                                    ?>
                                                    <li>
                                                        <a href="javascript:void(0)"><?php echo strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']); ?></a>                                           
                                                    </li>        
                                                <?php } elseif ($content['MerchantContent']['name'] == 'LOCATIONS') {
                                                    ?>
                                                    <li>
                                                        <a href="javascript:void(0)"><?php echo strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']); ?></a>                                             
                                                    </li>
                                                <?php } elseif ($content['MerchantContent']['name'] == 'GALLERY') { ?>
                                                    <li class="locate <?php
                                                    if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'gallery') {
                                                        echo 'active';
                                                    }
                                                    ?>">
                                                        <a href="javascript:void(0)"><?php echo strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']); ?></a>
                                                    <?php } elseif ($content['MerchantContent']['name'] == 'NEWSLETTER') { ?>
                                                    <li class="locate <?php
                                                    if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'newsletter') {
                                                        echo 'active';
                                                    }
                                                    ?>">
                                                        <a href="javascript:void(0)"><?php echo strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']); ?></a></li>
                                                <?php } elseif ($content['MerchantContent']['name'] == 'PROMOTIONS') { ?>
                                                    <li class="locate <?php
                                                    if ($this->params['controller'] == 'hqdeals' && $this->params['action'] == 'index') {
                                                        echo 'active';
                                                    }
                                                    ?>">
                                                        <a href="javascript:void(0)"><?php echo strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']); ?></a></li>
                                                <?php } else {
                                                    ?>
                                                    <li class="locate <?php
                                                    if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'staticContent') {
                                                        echo 'active';
                                                    }
                                                    ?>">
                                                        <a href="javascript:void(0)"><?php echo strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']); ?></a>
                                                    </li>

                                                    <?php
                                                }
                                                $j++;
                                            }
                                        }
                                    }
                                    ?>
                                    <?php if (!empty($merchantList)) { ?>
                                        <?php
                                        $m = 0;
                                        foreach ($merchantList as $content) {
                                            if ($content['MerchantContent']['page_position'] == 3) {
                                                if ($m == 0) {
                                                    ?>
                                                    <li>
                                                        <a href="javascript:void(0)">MORE INFO</a>
                                                        <ul class="list-style-none">

                                                        <?php }
                                                        ?>
                                                        <li class='<?php if (($this->params['controller'] == 'hqusers') && ($this->params['action'] == 'staticContent')) echo 'active'; ?>'><a href="javascript:void(0)"><?php echo strtoupper($content['MerchantContent']['name']); ?></a></li>
                                                        <?php
                                                        $m++;
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </nav>
                            <div class="o-grid__item">
                                <button  id="vt-hambug" class="c-hamburger c-hamburger--htx " >
                                    <span>toggle menu</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </header>
        <!-- HEADER END -->
        <main class="main">
            <div class="flexslider">
                <ul class="slides">
                    <?php
                    if (!empty($photo)) {
                        foreach ($photo as $photos) {
                            if (!empty($photos['MerchantGallery']['image']) && file_exists(WWW_ROOT . '/merchantSliderImages/thumb/' . $photos['MerchantGallery']['image'])) {
                                ?>
                                <li> <?php echo $this->Html->image('/merchantSliderImages/' . $photos['MerchantGallery']['image'], array('alt' => 'Image')) ?>
                                    <?php if (!empty($photos['MerchantGallery']['description'])) { ?>
                                        <p class="flex-caption"><?php echo $photos['MerchantGallery']['description']; ?></p>
                                    <?php } ?>
                                </li>
                            <?php } else {
                                ?>
                                <li><?php echo $this->Html->image('img/hq/showcase-01.jpg', array('alt' => 'Image')) ?></li>
                                <?php
                            }
                        }
                    }
                    ?>
                </ul>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <?php if (!empty($homeContentData)) { ?>
                            <?php
                            foreach ($homeContentData as $cLayoutData) {
                                ?>
                                <?php
                                if (!empty($cLayoutData['LayoutBox'])) {
                                    if ($cLayoutData['LayoutBox']['ratio'] == 100.00) {
                                        $class = 'col-xs-12 borderLine';
                                        $dVal = '1';
                                    } elseif ($cLayoutData['LayoutBox']['ratio'] == 50.00) {
                                        $class = "col-xs-6 borderLine";
                                        $dVal = '1/2';
                                    } elseif ($cLayoutData['LayoutBox']['ratio'] == 33.33) {
                                        $class = "col-xs-4 borderLine";
                                        $dVal = '1/3';
                                    } elseif ($cLayoutData['LayoutBox']['ratio'] == 25.00) {
                                        $class = "col-xs-3 borderLine";
                                        $dVal = '1/4';
                                    } elseif ($cLayoutData['LayoutBox']['ratio'] == 66.66) {
                                        $class = "col-xs-8 borderLine";
                                        $dVal = '2/3';
                                    } elseif ($cLayoutData['LayoutBox']['ratio'] == 75.00) {
                                        $class = "col-xs-9 borderLine";
                                        $dVal = '3/4';
                                    }
                                    ?>
                                    <div class="<?php echo $class; ?>" ><?php echo $cLayoutData['HomeContent']['content'] ?></div>
                                    <?php
                                    ?>
                                    <?php
                                }
                            }
                            ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <!-- LOCATIONS -->
            <?php echo $this->element('hquser/home/content5'); ?>
            <!-- /LOCATIONS -->
            <!-- CONTACT US -->
            <?php if (!empty($logoPosition) && !empty($logoPosition['MerchantConfiguration']['contact_active'])) { ?>
                <?php if (!empty($contactUsBgImage)) { ?>
                    <style>
                        .contact-us {
                            background-image: url("<?php echo '/merchantBackground-Image/' . $contactUsBgImage; ?>");
                            background-size: 100% auto;
                            background-repeat:no-repeat;background-size:cover !important;
                        }
                    </style>
                <?php } ?>
                <div class="contact-us">
                    <div class="container">
                        <div class="row">
                            <div class="col-xs-12 col-sm-10 col-md-8 col-sm-pull-1 col-sm-push-1 col-md-pull-2 col-md-push-2 text-center">
                                <h2>CONTACT US</h2>
                                <div id="contactFlashMsg"></div>
                                <div class="form-field"><?php echo $this->Form->input('name', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'NAME', 'label' => false, 'div' => false, 'required' => "required")); ?></div>
                                <div class="form-field"><?php echo $this->Form->input('email', array('type' => 'email', 'class' => 'inbox', 'placeholder' => 'E-MAIL ADDRESS', 'label' => false, 'div' => false, 'required' => "required")); ?></div>
                                <div class="form-field"><?php echo $this->Form->input('phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'inbox phone-number', 'placeholder' => 'PHONE NUMBER', 'label' => false, 'div' => false, 'required' => true)); ?></div>
                                <div class="form-field"><?php echo $this->Form->input('subject', array('type' => 'text', 'class' => 'inbox', 'placeholder' => 'SUBJECT', 'label' => false, 'div' => false, 'required' => "required")); ?></div>
                                <div class="form-field"><?php echo $this->Form->textarea('message', array('type' => 'textarea', 'class' => 'inbox', 'placeholder' => 'MESSAGE', 'required' => "required")); ?></div>
                                <div class="form-field"><?php echo $this->Form->input('SUBMIT', array('type' => 'submit', "id" => "contactUs", 'label' => false, 'div' => false)); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
            ?>
            <!-- -->
        </main>
        <!-- FOOTER -->
        <footer class="footer">
            <div class="footer-top">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12 text-center">
                            <ul class="list-style-none">
                                <?php
                                if ($socialLinks) {
                                    if (!empty($socialLinks['SocialMedia']['facebook'])) {
                                        ?>
                                        <li><a href="javascript:void(0);"><i class="fa fa-facebook-f" aria-hidden="true"></i></a></li>
                                    <?php } if (!empty($socialLinks['SocialMedia']['twitter'])) { ?>
                                        <li><a href="javascript:void(0);"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                    <?php } if (!empty($socialLinks['SocialMedia']['instagram'])) { ?>
                                        <li><a href="javascript:void(0);"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                                    <?php } if (!empty($socialLinks['SocialMedia']['yolo'])) { ?>
                                        <li><a href="javascript:void(0);"><i class="fa fa-yelp" aria-hidden="true"></i></a></li>
                                    <?php } if (!empty($socialLinks['SocialMedia']['google'])) { ?>
                                        <li><a href="javascript:void(0);"><i class="fa fa-google-plus" aria-hidden="true"></i></a></li>
                                    <?php } if (!empty($socialLinks['SocialMedia']['pinterest'])) { ?>
                                        <li><a href="javascript:void(0);"><i class="fa fa-pinterest" aria-hidden="true"></i></a></li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                            <div class="quick-links">
                                <a href="javascript:void(0);">+Terms &amp; Conditions</a> <a href="javascript:void(0);">+Privacy Policy</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <p class="copright">All rights reserved. &copy;<?php echo date('Y') ?> <?php echo @$name; ?>.</p>
        </footer>
        <!-- FOOTER END -->
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

//    $('#vt-hambug').on('click', function () {
//        $(".main-menu").toggleClass('show-hamb');
//    });
</script>