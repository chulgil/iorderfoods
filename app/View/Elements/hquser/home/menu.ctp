<?php
if (!empty($logoType) && $logoType == 1) {//Square
    $logoStyle = "max-width:185px;max-height=>185px;";
} elseif (!empty($logoType) && $logoType == 2) {//Rectangle
    $logoStyle = "max-width:300px;max-height=>120px;";
}
if (!empty($logoPosition['MerchantConfiguration']['logo_position']) && $logoPosition['MerchantConfiguration']['logo_position'] == 1) {//TOP
    ?>
    <div class="header header-theme1">
        <!-- -->
        <div class="container">
            <div class="row text-center">
                <h1 class="col-xs-12 ">
                    <?php
                    if (!empty($logo)) {
                        echo $this->Html->link($this->Html->image('/merchantLogo/' . $logo, array('style' => $logoStyle)), array('controller' => 'hqusers', 'action' => 'merchant'), array('escape' => false, 'alt' => 'Logo'));
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
                                        <li class="locate <?php
                                        if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'merchant') {
                                            echo 'active';
                                        }
                                        ?>">
                                                <?php echo $this->Html->link(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name'], array('controller' => 'hqusers', 'action' => 'merchant'));
                                                ?>
                                        </li>
                                    <?php } elseif ($content['MerchantContent']['name'] == 'LOCATIONS') {
                                        ?>
                                        <li>
                                            <a href="javascript:void(0)"><?php echo ($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']; ?></a>                                             
                                            <ul class="list-style-none">
                                                <li><?php
                                                    echo $this->Html->link('All', array('controller' => 'hqusers', 'action' => 'location'), array('escape' => false));
                                                    ?></li>
                                                <?php
                                                if (!empty($storeCity)) {
                                                    foreach ($storeCity as $city) {
                                                        ?>
                                                        <li><?php
                                                            echo $this->Html->link(ucfirst($city['Store']['city']), array('controller' => 'hqusers', 'action' => 'location', $city['Store']['city']), array('escape' => false));
                                                            ?></li>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </li>
                                    <?php } elseif ($content['MerchantContent']['name'] == 'GALLERY') { ?>
                                        <li class="locate <?php
                                        if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'gallery') {
                                            echo 'active';
                                        }
                                        ?>">
                                                <?php echo $this->Html->link(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name'], array('controller' => 'hqusers', 'action' => 'gallery'));
                                                ?>
                                        </li>
                                    <?php } elseif ($content['MerchantContent']['name'] == 'NEWSLETTER') { ?>
                                        <li class="locate <?php
                                        if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'newsletter') {
                                            echo 'active';
                                        }
                                        ?>">
                                                <?php echo $this->Html->link(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name'], array('controller' => 'hqusers', 'action' => 'newsletter'));
                                                ?>
                                        </li>
                                    <?php } elseif ($content['MerchantContent']['name'] == 'PROMOTIONS') { ?>
                                        <li class="locate <?php
                                        if ($this->params['controller'] == 'hqdeals' && $this->params['action'] == 'index') {
                                            echo 'active';
                                        }
                                        ?>">
                                                <?php echo $this->Html->link(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name'], array('controller' => 'hqdeals', 'action' => 'index')); ?>
                                        </li>
                                    <?php } else {
                                        ?>
                                        <li class="locate <?php
                                        if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'staticContent') {
                                            echo 'active';
                                        }
                                        ?>">
                                                <?php echo $this->Html->link(strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']), array('controller' => 'hqusers', 'action' => 'staticContent', $this->Encryption->encode($content['MerchantContent']['id']), $content['MerchantContent']['name'])); ?>
                                        </li>

                                        <?php
                                    }
                                }
                            }
                        }
                        ?>
                        <!--<?php if ($this->params['action'] == 'merchant') { ?>
                                                                                                                                                                                    <li id='contact_us'><a href="javascript:void(0)">CONTACT</a></li>
                        <?php } ?>-->
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


                                            <li class='<?php if (($this->params['controller'] == 'hqusers') && ($this->params['action'] == 'staticContent')) echo 'active'; ?>'><?php echo $this->Html->link(strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']), array('controller' => 'hqusers', 'action' => 'staticContent', $this->Encryption->encode($content['MerchantContent']['id']), $content['MerchantContent']['name'])); ?></li>
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
<?php }elseif (!empty($logoPosition['MerchantConfiguration']['logo_position']) && $logoPosition['MerchantConfiguration']['logo_position'] == 2) {//LEFT ?>
    <!-- HEADER THEME 2 -->
    <div class="header header-theme2">
        <div class="container theme2">
            <div class="row text-center">
                <h1 class="col-xs-12 col-sm-3">
                    <?php
                    if (!empty($logo)) {
                        echo $this->Html->link($this->Html->image('/merchantLogo/' . $logo, array('style' => $logoStyle)), array('controller' => 'hqusers', 'action' => 'merchant'), array('escape' => false, 'alt' => 'Logo'));
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
                                        <li class="locate <?php
                                        if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'merchant') {
                                            echo 'active';
                                        }
                                        ?>">
                                                <?php echo $this->Html->link(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name'], array('controller' => 'hqusers', 'action' => 'merchant'));
                                                ?>
                                        </li>
                                    <?php } elseif ($content['MerchantContent']['name'] == 'LOCATIONS') {
                                        ?>
                                        <li>
                                            <a href="javascript:void(0)"><?php echo ($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']; ?></a>                                             
                                            <ul class="list-style-none">
                                                <li><?php
                                                    echo $this->Html->link('All', array('controller' => 'hqusers', 'action' => 'location'), array('escape' => false));
                                                    ?></li>
                                                <?php
                                                if (!empty($storeCity)) {
                                                    foreach ($storeCity as $city) {
                                                        ?>
                                                        <li><?php
                                                            echo $this->Html->link(ucfirst($city['Store']['city']), array('controller' => 'hqusers', 'action' => 'location', $city['Store']['city']), array('escape' => false));
                                                            ?>
                                                        </li>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </li>
                                    <?php } elseif ($content['MerchantContent']['name'] == 'GALLERY') { ?>
                                        <li class="locate <?php
                                        if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'gallery') {
                                            echo 'active';
                                        }
                                        ?>">
                                                <?php echo $this->Html->link(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name'], array('controller' => 'hqusers', 'action' => 'gallery'));
                                                ?>
                                        </li>
                                    <?php } elseif ($content['MerchantContent']['name'] == 'NEWSLETTER') { ?>
                                        <li class="locate <?php
                                        if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'newsletter') {
                                            echo 'active';
                                        }
                                        ?>">
                                                <?php echo $this->Html->link(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name'], array('controller' => 'hqusers', 'action' => 'newsletter'));
                                                ?>
                                        </li>
                                    <?php } elseif ($content['MerchantContent']['name'] == 'PROMOTIONS') { ?>
                                        <li class="locate <?php
                                        if ($this->params['controller'] == 'hqdeals' && $this->params['action'] == 'index') {
                                            echo 'active';
                                        }
                                        ?>">
                                                <?php echo $this->Html->link(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name'], array('controller' => 'hqdeals', 'action' => 'index')); ?>
                                        </li>
                                    <?php } else {
                                        ?>
                                        <li class="locate <?php
                                        if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'staticContent') {
                                            echo 'active';
                                        }
                                        ?>">
                                                <?php echo $this->Html->link(strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']), array('controller' => 'hqusers', 'action' => 'staticContent', $this->Encryption->encode($content['MerchantContent']['id']), $content['MerchantContent']['name'])); ?>
                                        </li>

                                        <?php
                                    }
                                }
                            }
                        }
                        ?> 
                        <!--<?php if ($this->params['action'] == 'merchant') { ?>
                                                                                                                                                                                    <li id='contact_us'><a href="javascript:void(0)">CONTACT</a></li>
                        <?php } ?>-->
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
                                            <li class='<?php if (($this->params['controller'] == 'hqusers') && ($this->params['action'] == 'staticContent')) echo 'active'; ?>'><?php echo $this->Html->link(strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']), array('controller' => 'hqusers', 'action' => 'staticContent', $this->Encryption->encode($content['MerchantContent']['id']), $content['MerchantContent']['name'])); ?></li>
                                            <?php
                                            $m++;
                                        }
                                        ?>    
                                    </ul>
                                </li>   
                                <?php
                            }
                            ?>
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
<?php } elseif (!empty($logoPosition['MerchantConfiguration']['logo_position']) && $logoPosition['MerchantConfiguration']['logo_position'] == 3) {//CENTER ?>
    <!-- HEADER THEME 3 -->
    <div class="header header-theme3">
        <div class="container theme3">
            <div class="row text-center">
                <h1 class="col-xs-12 logo">
                    <?php
                    if (!empty($logo)) {
                        echo $this->Html->link($this->Html->image('/merchantLogo/' . $logo, array('style' => $logoStyle)), array('controller' => 'hqusers', 'action' => 'merchant'), array('escape' => false, 'alt' => 'Logo'));
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
                                                echo $this->Html->link($this->Html->image('/merchantLogo/' . $logo, array('style' => $logoStyle)), array('controller' => 'hqusers', 'action' => 'merchant'), array('escape' => false, 'alt' => 'Logo'));
                                            } else {
                                                echo $this->Html->link($name, array('controller' => 'hqusers', 'action' => 'merchant'), array('escape' => false));
                                            }
                                            ?>
                                        </li>

                                        <?php
                                    }
                                    if ($content['MerchantContent']['name'] == 'HOME') {
                                        ?>
                                        <li class="locate <?php
                                        if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'merchant') {
                                            echo 'active';
                                        }
                                        ?>">
                                                <?php echo $this->Html->link(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name'], array('controller' => 'hqusers', 'action' => 'merchant'));
                                                ?>
                                        </li>
                                    <?php } elseif ($content['MerchantContent']['name'] == 'LOCATIONS') {
                                        ?>
                                        <li>
                                            <a href="javascript:void(0)"><?php echo ($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']; ?></a>                                             
                                            <ul class="list-style-none">
                                                <li><?php
                                                    echo $this->Html->link('All', array('controller' => 'hqusers', 'action' => 'location'), array('escape' => false));
                                                    ?></li>
                                                <?php
                                                if (!empty($storeCity)) {
                                                    foreach ($storeCity as $city) {
                                                        ?>
                                                        <li><?php
                                                            echo $this->Html->link(ucfirst($city['Store']['city']), array('controller' => 'hqusers', 'action' => 'location', $city['Store']['city']), array('escape' => false));
                                                            ?></li>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </li>
                                    <?php } elseif ($content['MerchantContent']['name'] == 'GALLERY') { ?>
                                        <li class="locate <?php
                                        if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'gallery') {
                                            echo 'active';
                                        }
                                        ?>">
                                                <?php echo $this->Html->link(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name'], array('controller' => 'hqusers', 'action' => 'gallery'));
                                                ?>
                                        </li>
                                    <?php } elseif ($content['MerchantContent']['name'] == 'NEWSLETTER') { ?>
                                        <li class="locate <?php
                                        if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'newsletter') {
                                            echo 'active';
                                        }
                                        ?>">
                                                <?php echo $this->Html->link(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name'], array('controller' => 'hqusers', 'action' => 'newsletter'));
                                                ?>
                                        </li>
                                    <?php } elseif ($content['MerchantContent']['name'] == 'PROMOTIONS') { ?>
                                        <li class="locate <?php
                                        if ($this->params['controller'] == 'hqdeals' && $this->params['action'] == 'index') {
                                            echo 'active';
                                        }
                                        ?>">
                                                <?php echo $this->Html->link(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name'], array('controller' => 'hqdeals', 'action' => 'index')); ?>
                                        </li>
                                    <?php } else {
                                        ?>
                                        <li class="locate <?php
                                        if ($this->params['controller'] == 'hqusers' && $this->params['action'] == 'staticContent') {
                                            echo 'active';
                                        }
                                        ?>">
                                                <?php echo $this->Html->link(strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']), array('controller' => 'hqusers', 'action' => 'staticContent', $this->Encryption->encode($content['MerchantContent']['id']), $content['MerchantContent']['name'])); ?>
                                        </li>

                                        <?php
                                    }
                                    $j++;
                                }
                            }
                        }
                        ?>
                        <!--<?php if ($this->params['action'] == 'merchant') { ?>
                                                                                                                                                                                    <li id='contact_us'><a href="javascript:void(0)">CONTACT</a></li>
                        <?php } ?>-->
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
                                            <li class='<?php if (($this->params['controller'] == 'hqusers') && ($this->params['action'] == 'staticContent')) echo 'active'; ?>'><?php echo $this->Html->link(strtoupper(($content['MerchantContent']['content_key']) ? $content['MerchantContent']['content_key'] : $content['MerchantContent']['name']), array('controller' => 'hqusers', 'action' => 'staticContent', $this->Encryption->encode($content['MerchantContent']['id']), $content['MerchantContent']['name'])); ?></li>
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


<script>
    $(document).ready(function () {
        var width = $(window).width();
        if ((width <= 767)) {
            $('.header nav ul li a').on('click', function () {
                $(this).next().slideToggle();
            });
        } else {

        }
    });
</script>