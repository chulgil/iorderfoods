<!-- HEADER START -->
<header>
    <?php
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    ?>
    <style>
        @media screen and (min-width: 990px) {
            .tabs-menue .callus {
                margin-top:-53px; margin-left:-21.8%; width:250px;
            }
        }
    </style>
    <!-- HEADER TOP -->
    <div class="header-top">
        <div class="wrap">
            <span style='float:left;' class="active"><a href="javascript:void(0);">Call us @ <?php echo $_SESSION['store_phone']; ?></a></span>
            <?php
            if (AuthComponent::User()) {

                $cartlink = "/products/items/" . $encrypted_storeId . "/" . $encrypted_merchantId;

                if (!$cartcount) {
                    $cartlink = "javascript:void(0)";
                }
                ?>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <!--                    <a href="javascript:void(0)" class="welcome-user">Welcome : <span><?php echo ucfirst($_SESSION['Auth']['User']['fname']); ?> | </span> <i class="fa fa-bars"></i> </a>-->

                <a href="javascript:void(0)">Welcome : <span><?php echo ucfirst($_SESSION['Auth']['User']['fname']); ?> | </span> </a>
                <a href="<?php echo $cartlink; ?>"><i class="fa fa-shopping-cart"></i> Cart&nbsp;<span class="numberCircle"><?php echo $cartcount; ?></span></a>
                <a href="javascript:void(0)" class="welcome-user"> <i class="fa fa-bars"></i> </a>

                <ul class="welcome-user-menu">
                    <li><?php echo $this->Html->link('Delivery Addresses', array('controller' => 'users', 'action' => 'deliveryAddress', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                    <?php if (!empty($store_data_app['StoreSetting']['order_allow'])) { ?>
                        <li><?php echo $this->Html->link('Place Order', array('controller' => 'users', 'action' => 'customerDashboard', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                    <?php } ?>
                    <li><?php echo $this->Html->link('Profile', array('controller' => 'users', 'action' => 'myProfile', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                    <li> <?php echo $this->Html->link(__('My Favorites & Orders'), array('controller' => 'orders', 'action' => 'myOrders', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                    <li><?php echo $this->Html->link(__('My Coupons'), array('controller' => 'coupons', 'action' => 'myCoupons', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                    <?php if ($store_data_app['Store']['is_booking_open'] == 1) { ?>
                        <li><?php echo $this->Html->link(__('My Reservations'), array('controller' => 'pannels', 'action' => 'myBookings', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                    <?php } ?>
                    <li><?php echo $this->Html->link(__('My Reviews'), array('controller' => 'pannels', 'action' => 'myReviews', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                    <li><?php echo $this->Html->link('Logout', array('controller' => 'users', 'action' => 'logout')); ?></li>
                </ul>
                <?php
            } else if (!AuthComponent::User() && $this->Session->check('Order.delivery_address_id')) {

                $cartlink = "/products/items/" . $encrypted_storeId . "/" . $encrypted_merchantId;
                if (!$cartcount) {
                    $cartlink = "javascript:void(0)";
                }
                ?>

                <a href="javascript:void(0)">Welcome : <span>Guest | </span> </a>
                <a href="<?php echo $cartlink; ?>"><i class="fa fa-shopping-cart"></i> Cart&nbsp;<span class="numberCircle"><?php echo $cartcount; ?></span></a>
                <ul>
                    <li><?php echo $this->Html->link('Sign In', array('controller' => 'users', 'action' => 'signIn')); ?></li>
                    <li><?php echo $this->Html->link('Sign Up', array('controller' => 'users', 'action' => 'registration')); ?></li>
                    <li><?php echo $this->Html->link('Forgot Password?', array('controller' => 'users', 'action' => 'forgetPassword')); ?></li>

                </ul>
                <?php
            } else {
                ?>
                <ul>
                    <li><?php echo $this->Html->link('Sign In', array('controller' => 'users', 'action' => 'signIn')); ?></li>
                    <li><?php echo $this->Html->link('Sign Up', array('controller' => 'users', 'action' => 'registration')); ?></li>
                    <li><?php echo $this->Html->link('Forgot Password?', array('controller' => 'users', 'action' => 'forgetPassword')); ?></li>

                </ul>
            <?php } ?>
        </div>
    </div>
    <!-- /HEADER TOP -->

    <!-- HEADER BODY -->
    <section class="header-body clearfix">
        <div class="wrap">
            <?php if ($store_data_app['Store']['is_store_logo'] == 1) { ?>
                <h1>
                   <a href="/users/login"><?php echo $store_data_app['Store']['store_name']; ?> <!-- <span></span> --> </a>
                </h1>
                <?php
            } else {
                if ($store_data_app['Store']['logotype'] == 1) {
                    ?>
                    <a href="/users/login" class="restaurant-logo-square"><?php echo $this->Html->image('/storeLogo/' . $store_data_app['Store']['store_logo']); ?></a>

                <?php } elseif ($store_data_app['Store']['logotype'] == 2) {
                    ?>
                    <a href="/users/login" class="restaurant-logo-rectangle"><?php echo $this->Html->image('/storeLogo/' . $store_data_app['Store']['store_logo']); ?></a>
                    <?php
                }
            }
            ?>

        </div>
    </section>
    <!-- /HEADER BODY -->
    <?php
//    if ($this->params['controller'] == 'products' || $this->params['controller'] == 'Products' || $this->params['controller'] == 'payments' || $this->params['controller'] == 'Payments') {
//        
//    } else {
//        ?>

        <!-- MENU -->
        <nav class="nav-menu">
            <div class="wrap">
                <a href="javascript:void(0)" class="small-screen-menu">MENU</a>
                <ul class="clearfix tabs-menue">
                    <?php
                    if (!empty($store_data_app['StoreContent'])) {
                        foreach ($store_data_app['StoreContent'] as $content) {
                            if ($content['page_position'] == 1) {
                                if ($content['name'] == 'HOME') {
                                    ?>
                                    <li class="<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'Users') && ($this->params['action'] == 'login')) echo 'active'; ?>"><a href="/users/login">Home</a></li>
                                    <?php
                                }elseif ($content['name'] == 'PLACE ORDER') {
                                    if (AuthComponent::User() && !empty($store_data_app['StoreSetting']['order_allow'])) {
                                        ?>
                                        <li class="<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'Users') && ($this->params['action'] == 'customerDashboard')) echo 'active'; ?>">
                                            <?php echo $this->Html->link('Place Order', array('controller' => 'users', 'action' => 'customerDashboard', $encrypted_storeId, $encrypted_merchantId)); ?>
                                        </li>
                                        <?php
                                    }
                                } elseif ($content['name'] == 'RESERVATIONS') {
                                    if (AuthComponent::User() && $store_data_app['Store']['is_booking_open'] == 1) {
                                        ?>
                                        <li class="<?php if (($this->params['controller'] == 'pannels' || $this->params['controller'] == 'Pannels') && ($this->params['action'] == 'myBookings')) echo 'active'; ?>"><?php echo $this->Html->link(__('Reservations'), array('controller' => 'pannels', 'action' => 'myBookings', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                                        <?php
                                    }
                                }elseif ($content['name'] == 'STORE INFO') {
                                    ?>
                                    <li class='<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'Users') && ($this->params['action'] == 'storeLocation')) echo 'active'; ?>'><a href="/users/storeLocation/<?php echo $this->Encryption->encode($this->Session->read('store_id')); ?>/<?php echo $this->Encryption->encode($this->Session->read('merchant_id')); ?>">Store Info</a></li>
                                    <?php
                                }elseif ($content['name'] == 'PHOTO') {
                                    if ($store_data_app['Store']['is_not_photo'] == 1) {
                                        ?>
                                        <li class='<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'Users') && ($this->params['action'] == 'storePhoto')) echo 'active'; ?>'><a href="/users/storePhoto/<?php echo $this->Encryption->encode($this->Session->read('store_id')); ?>/<?php echo $this->Encryption->encode($this->Session->read('merchant_id')); ?>">Photos</a></li>
                                        <?php
                                    }
                                }elseif ($content['name'] == 'REVIEWS') {
                                    if ($store_data_app['Store']['review_page'] == 1) {
                                        ?>
                                        <li class="<?php if (($this->params['controller'] == 'pannels' || $this->params['controller'] == 'Pannels') && ($this->params['action'] == 'allReviews')) echo 'active'; ?>"> <?php echo $this->Html->link('Reviews', array('controller' => 'pannels', 'action' => 'allReviews', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                                        <?php
                                    }
                                }elseif ($content['name'] == 'MENU') {
                                    ?>
                                    <li class='<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'Menus') && ($this->params['action'] == 'menuItems')) echo 'active'; ?>'><a href="/products/items/<?php echo $this->Encryption->encode($this->Session->read('store_id')); ?>/<?php echo $this->Encryption->encode($this->Session->read('merchant_id')); ?>">Menu</a></li>
                                    <?php
                                }else {
                                    if ($content['name'] != 'GALLERY' && $content['name'] != 'DEALS' && $content['name'] != 'PHOTO') {
                                        ?>
                                        <li class='<?php if (($this->params['controller'] == 'pannels') && ($this->params['action'] == 'staticContent') && ($this->params['pass'][3] == $content['name'])) echo 'active'; ?>'><?php echo $this->Html->link($content['name'], array('controller' => 'pannels', 'action' => 'staticContent', $this->Encryption->encode($this->Session->read('store_id')), $this->Encryption->encode($this->Session->read('merchant_id')), $this->Encryption->encode($content['id']), $content['name'])); ?></li>
                                        <?php
                                    }
                                }
                            }
                        }
                    }
                    ?>
                    <?php if (!empty($store_data_app['StoreContent'])) { ?>
                        <?php
                        $m = 0;
                        foreach ($store_data_app['StoreContent'] as $content) {
                            if ($content['page_position'] == 3) {
                                if ($m == 0) {
                                    ?>
                                    <li>
                                        <a href="javascript:void(0)">MORE INFO</a>
                                        <ul class="list-style-none">

                                        <?php }
                                        ?>


                                        <li class='<?php if (($this->params['controller'] == 'pannels') && ($this->params['action'] == 'staticContent') && ($this->params['pass'][3] == $content['name'])) echo 'active'; ?>'><?php echo $this->Html->link($content['name'], array('controller' => 'pannels', 'action' => 'staticContent', $this->Encryption->encode($this->Session->read('store_id')), $this->Encryption->encode($this->Session->read('merchant_id')), $this->Encryption->encode($content['id']), $content['name'])); ?>
                                        </li>
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
    <!--                    <li style='float:right' class="active"><a href="javascript:void(0);">Call us @ <?php echo $_SESSION['store_phone']; ?></a></li>-->
                </ul>
            </div>
        </nav>
        <!-- /MENU -->

    <?php //} ?>
</header>
<!-- END HEADER -->


<script>
    $('.tabs-menue li').click(function () {
        $('.tabs-menue li').removeClass('active');
        $(this).addClass('active');
    });
</script>
