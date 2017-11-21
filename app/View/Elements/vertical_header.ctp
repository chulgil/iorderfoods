<header>
    <?php
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    ?>
    <style>
        .ancsize{
            font-size:12px;
            float:left;
            margin-right:10px;
        }
    </style>
    <?php if (AuthComponent::User()) { ?>
        <section class="header-body vertical-login-header clearfix">

        <?php } else { ?>
            <section class="header-body clearfix">
            <?php } ?>
            <div class="wraper">
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



                <?php
                if (AuthComponent::User()) {
                    $cartlink = "/products/items/" . $encrypted_storeId . "/" . $encrypted_merchantId;
                    if (!$cartcount) {
                        $cartlink = "javascript:void(0)";
                    }
                    ?>

                    <div class="header-body-right">
                        <div class="welcome-text">Welcome : <span><?php echo ucfirst($_SESSION['Auth']['User']['fname']); ?> | </span></div>
                        <a href="<?php echo $cartlink; ?>" ><i class="fa fa-shopping-cart"></i> Cart&nbsp;<span class="numberCircle"><?php echo $cartcount; ?></span></a>

                        <a href="javascript:void(0)" class="welcome-user"><i class="fa fa-bars"></i> </a>
                        <ul class="welcome-user-menu">
                            <li><?php echo $this->Html->link('Delivery Addresses', array('controller' => 'users', 'action' => 'deliveryAddress', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                            <li><?php if (is_array($nzsafe_data_app)) echo $this->Html->link('My Billing Information', array('controller' => 'users', 'action' => 'myBillingInfo', $encrypted_storeId, $encrypted_merchantId)); ?></li>
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
                    </div>
                    <?php
                } else if (!AuthComponent::User() && $this->Session->check('Order.delivery_address_id')) {

                    $cartlink = "/products/items/" . $encrypted_storeId . "/" . $encrypted_merchantId;
                    if (!$cartcount) {
                        $cartlink = "javascript:void(0)";
                    }
                    ?>
                    <div class="header-body-right">
                        <div class="welcome-text">Welcome : <span>Guest | </span></div>
                        <a href="<?php echo $cartlink; ?>" ><i class="fa fa-shopping-cart"></i> Cart&nbsp;<span class="numberCircle"><?php echo $cartcount; ?></span></a>
                        <div class="left">

                            <?php echo $this->Html->link('Sign In', array('controller' => 'users', 'action' => 'dologin'), array("class" => "ancsize")); ?>
    <?php echo $this->Html->link('Sign Up', array('controller' => 'users', 'action' => 'registration'), array("class" => "ancsize")); ?>
    <?php echo $this->Html->link('Forgot Password?', array('controller' => 'users', 'action' => 'forgetPassword'), array("class" => "ancsize")); ?>
                        </div>

                    </div>


                    <?php
                } else {
                    ?>
                    <ul>
    <?php echo $this->Form->create('User', array('id' => 'UserLogin', 'url' => array('controller' => 'users', 'action' => 'login'), 'autocomplete' => 'off')); ?>
                        <div class="clearfix">
                            <div class="header-left">
                                <div class="filed-form">
                                    <div class="left"><?php echo $this->Form->input('User.email', array('type' => 'email', "placeholder" => "Enter Your Email", 'autofocus' => true, 'label' => false, 'maxlength' => '50', "class" => "inbox", 'div' => false)); ?></div>
                                    <div class="right"><?php echo $this->Form->input('User.password', array("placeholder" => "Enter Your Password", 'label' => false, 'type' => 'password', 'div' => false, 'maxlength' => '20', "class" => "inbox")); ?></div>
                                    <div class="clr"></div>
                                </div>
                                <div class="filed-form static-links">
                                    <div class="left">
                                        <?php if (!empty($rem)): ?>
                                            <input type="checkbox" id="Remember_me_1"  name="data[User][remember]" checked /> <label for="Remember_me_1">Remember me</label>
                                        <?php else: ?>
                                            <input type="checkbox" id="Remember_me_2"  name="data[User][remember]" /> <label for="Remember_me_2">Remember me</label>
    <?php endif; ?>
                                    </div>
                                    <div class="right"> <?php echo $this->Html->link('Forgot Password?', array('controller' => 'users', 'action' => 'forgetPassword')); ?>
                                        | <?php echo $this->Html->link('Sign Up', array('controller' => 'users', 'action' => 'registration')); ?>
                                    </div>
                                    <div class="clr"></div>
                                </div>
                            </div>
                            <div class="header-right"> <button type="submit" name="submit" class="btn green-btn pink-btn"> <span>Login</span> </button> </div>
                        </div>
                    <?php echo $this->Form->end(); ?>

                    </ul>
<?php } ?>

            </div>
        </section>
</header>

<script>
    $("#UserLogin").validate({
        rules: {
            "data[User][email]": {
                required: true,
            },
            "data[User][password]": {
                required: true,
            }
        },
        messages: {
            "data[User][email]": {
                required: "Please enter your email",
            },
            "data[User][password]": {
                required: "Please enter your password",
            }
        }
    });
</script>
