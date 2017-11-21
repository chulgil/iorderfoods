<?php
$hClass = "";
if ($this->request->params['action'] == 'login') {// for chole header home page
    $hClass = "chole-header";
}
?>
<header class="custom-header <?php echo $hClass; ?> clearfix">
    <div class="black-bg-top">
        <div class="main-container">
            <div class="chole-black-top clearfix">
                <div class="left-item">
                    <span class="phone-icon"><img src="/img/c-phone.png" alt="#c-phone">
                    </span>
                    <span><?php echo $store_data_app['Store']['phone']; ?></span>
                </div>
                <div class="rgt-items">
                    <div class="wraper">
                        <?php
                        if (AuthComponent::User()) {
                            $cartlink = "/products/items/" . $encrypted_storeId . "/" . $encrypted_merchantId;
                            if (!$cartcount) {
                                $cartlink = "javascript:void(0)";
                            }
                            ?>
                            <div class="header-body-right">
                                <ul class="navbar-nav navbar-right new-header-drop">
                                    <li class="dropdown">
                                        <a href="<?php echo $cartlink; ?>"><span class="cart"><img src="/img/cart.png"></span>&nbsp;<span class="numberCircle"><?php echo $cartcount; ?></span></a>
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                    <!--                                                <span class="cart"><img src="/img/cart.png"></span>&nbsp;<span class="numberCircle"><?php echo $cartcount; ?></span>-->
                                            Welcome , <span><?php echo ucfirst($_SESSION['Auth']['User']['fname']); ?> </span> </a>

                                        <ul class="dropdown-menu dropdown-cart" role="menu">
                                            <li><?php echo $this->Html->link('Delivery Addresses', array('controller' => 'users', 'action' => 'deliveryAddress', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                                            <li><?php if (is_array($nzsafe_data_app)) echo $this->Html->link('My Billing Information', array('controller' => 'users', 'action' => 'myBillingInfo', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                    <!--                            <li><?php echo $this->Html->link('Place Order', array('controller' => 'users', 'action' => 'customerDashboard', $encrypted_storeId, $encrypted_merchantId)); ?></li>-->
                                            <li><?php echo $this->Html->link('Profile', array('controller' => 'users', 'action' => 'myProfile', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                                            <li> <?php echo $this->Html->link(__('My Favorites & Orders'), array('controller' => 'orders', 'action' => 'myOrders', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                                            <li><?php echo $this->Html->link(__('My Coupons'), array('controller' => 'coupons', 'action' => 'myCoupons', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                                            <?php if ($store_data_app['Store']['is_booking_open'] == 1) { ?>
                                                <li><?php echo $this->Html->link(__('My Reservations'), array('controller' => 'pannels', 'action' => 'myBookings', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                                            <?php } ?>
                                            <li><?php echo $this->Html->link(__('My Reviews'), array('controller' => 'pannels', 'action' => 'myReviews', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                                            <li><?php echo $this->Html->link('Logout', array('controller' => 'users', 'action' => 'logout')); ?></li>
                                            <span class="arw-up"><img src="/img/ARROW-UP.png"></span>

                                        </ul>
                                        </div>
                                        <?php
                                    } else if (!AuthComponent::User() && $this->Session->check('GuestUser.name')) {
                                        $gName = $this->Session->read('GuestUser.name');

                                        $cartlink = "/products/items/" . $encrypted_storeId . "/" . $encrypted_merchantId;
                                        if (!$cartcount) {
                                            $cartlink = "javascript:void(0)";
                                        }
                                        ?>
                                        <div class="header-body-right">

                                            <ul class="navbar-nav navbar-right new-header-drop">
                                                <li class="dropdown">
                                                    <a href="<?php echo $cartlink; ?>"><span class="cart"><img src="/img/cart.png"></span>&nbsp;<span class="numberCircle"><?php echo $cartcount; ?></span></a>
                                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                                        Welcome , <span><?php echo $gName; ?> </span> </a><span> | </span> <?php echo $this->Html->link('<i class="fa fa-power-off"></i> Logout', array('controller' => 'users', 'action' => 'logout'), array('escape' => false)); ?>
                                                </li>
                                            </ul>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <ul class="account-access">
                                            <li><a href="javascript:void();" data-toggle="modal" data-target="#login-modal">Login</a></li>
                                            <li><span>|</span></li>
                                            <li><?php echo $this->Html->link('Sign Up', array('controller' => 'users', 'action' => 'registration'), array('escape' => false, 'class' => "")); ?></li>
                                        </ul>
                                    <?php } ?>


                                    </div>
                                    </div>
                                    </div>
                                    </div>
                                    </div>

                                    <div class="white-bg sticky">
                                        <div class="main-container">
                                            <div class="log-in-header clearfix">
                                                <div class="log-head-left">
                                                   <?php if (isset($store_data_app['Store']['is_store_open'])) {

                                                                if (!empty($store_data_app['Store']['is_store_open'])) {
                                                                    ?>
                                                                    <span class="shop-status"></span><span class="status-lable">Store is opened</span>
                                                                <?php } else { ?>
<!--                                                                    <span class="shop-closed"></span><span class="status-lable">Store is closed</span>-->
                                                                <?php }
                                                            } else { ?>
<!--                                                                <span class="shop-status"></span><span class="status-lable">Store is opened</span>-->
                                                            <?php } ?>
                                                </div>

                                                <div class="log-head-right">



                                                </div>
                                            </div>
                                            <div class="o-grid__item">
                                                <button  id="vt-hambug" class="c-hamburger c-hamburger--htx " >
                                                    <span>toggle menu</span>
                                                </button>
                                            </div>
                                            <div class="arron-white-header clearfix">
                                                <?php if ($store_data_app['Store']['is_store_logo'] == 1) { ?>
                                                    <h1><a href="/users/login" class="store-name"><?php echo $store_data_app['Store']['store_name']; ?></a></h1>
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

                                        </div>
                                        <span class="black-hr">
                                        </span>
                                    </div>
                                    <div class="black-bg vt-header">
                                        <a href="javascript:void(0);" class="menu-control"><i class="glyphicon glyphicon-menu-hamburger"></i></a>
                                        <div class="main-container">
                                            <div class="main-header-menu clearfix">
                                                <div class="logo">

                                                </div>
                                                <div class="menu-right">
                                                    <nav class="CTA-menu">
                                                        <div class="navbar-header">
                                                            <button type = "button" class = "navbar-toggle"
                                                                    data-toggle = "collapse" data-target = "#example-navbar-collapse">
                                                                <span class = "sr-only">Toggle navigation</span>
                                                                <span class = "icon-bar"></span>
                                                                <span class = "icon-bar"></span>
                                                                <span class = "icon-bar"></span>
                                                            </button>
                                                        </div>
                                                        <div class="collapse navbar-collapse custom-colapse" id = "example-navbar-collapse">
                                                            <ul class="main-menu-list clearfix">
                                                                <?php
                                                                if (!empty($store_data_app['StoreContent'])) {
                                                                    foreach ($store_data_app['StoreContent'] as $content) {
                                                                        if ($content['page_position'] == 1) {
                                                                            if ($content['name'] == 'HOME') {
                                                                                ?>
                                                                                <li class = "<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'Users') && ($this->params['action'] == 'login')) echo 'active'; ?>"><a href = "/users/login">Home</a></li>
                                                                                <?php
                                                                            }elseif ($content['name'] == 'REVIEWS') {
                                                                                if ($store_data_app['Store']['review_page'] == 1) {
                                                                                    ?>
                                                                                    <li class="<?php if (($this->params['controller'] == 'pannels' || $this->params['controller'] == 'Pannels') && ($this->params['action'] == 'allReviews')) echo 'active'; ?>"> <?php echo $this->Html->link('Reviews', array('controller' => 'pannels', 'action' => 'allReviews')); ?></li>
                                                                                    <?php
                                                                                }
                                                                            }elseif ($content['name'] == 'DEALS') {
                                                                                if ($store_data_app['Store']['deal_page'] == 2) {
                                                                                    ?>
                                                                                    <li class="<?php if (($this->params['controller'] == 'deals' || $this->params['controller'] == 'Deals') && ($this->params['action'] == 'index')) echo 'active'; ?>"> <?php echo $this->Html->link('Deals', array('controller' => 'deals', 'action' => 'index')); ?></li>
                                                                                    <?php
                                                                                }
                                                                            }elseif ($content['name'] == 'RESERVATIONS') {
                                                                                if (AuthComponent::User() && !empty($store_data_app['Store']['is_booking_open'])) {
                                                                                    ?>
                                                                                    <li class="<?php if (($this->params['controller'] == 'pannels' || $this->params['controller'] == 'Pannels') && ($this->params['action'] == 'myBookings')) echo 'active'; ?>"><?php echo $this->Html->link(__('Reservations'), array('controller' => 'pannels', 'action' => 'myBookings', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                                                                                    <?php
                                                                                }
                                                                            }elseif ($content['name'] == 'STORE INFO') {
                                                                                ?>
                                                                                <li class='<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'Users') && ($this->params['action'] == 'storeLocation')) echo 'active'; ?>'><a href="/users/storeLocation/<?php echo $this->Encryption->encode($this->Session->read('store_id')); ?>/<?php echo $this->Encryption->encode($this->Session->read('merchant_id')); ?>">Store Info</a></li>
                                                                            <?php }elseif ($content['name'] == 'MENU') {
                                                                                ?>
                                                                                <li class='<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'products') && ($this->params['action'] == 'items')) echo 'active'; ?>'><a href="/products/items/<?php echo $this->Encryption->encode($this->Session->read('store_id')); ?>/<?php echo $this->Encryption->encode($this->Session->read('merchant_id')); ?>">Menu</a></li>
                                                                                <?php
                                                                            }elseif ($content['name'] == 'GALLERY') {
                                                                                if ($store_data_app['Store']['is_not_photo'] == 1) {
                                                                                    ?>
                                                                                    <li class="<?php if (($this->params['controller'] == 'pannels') && ($this->params['action'] == 'orderImages')) echo 'active'; ?>"><?php echo $this->Html->link('Gallery', array('controller' => 'pannels', 'action' => 'orderImages', $this->Encryption->encode($this->Session->read('store_id')), $this->Encryption->encode($this->Session->read('merchant_id')))); ?></li>
                                                                                    <?php
                                                                                }
                                                                            }else {
                                                                                if ($content['name'] != 'PLACE ORDER' && $content['name'] != 'PHOTO') {
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
                                                            </ul>
                                                        </div>
                                                    </nav>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </header>

<script>
	$(document).ready(function(){
            $('.menu-control').on('click',function(){
                $('.vt-header').toggleClass('open');
            });
	});
</script>
