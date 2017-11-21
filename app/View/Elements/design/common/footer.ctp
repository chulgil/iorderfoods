<!-- footer start here -->
<footer class="clearfix">
    <?php
    if (KEYWORD == 'IOF-D3-H') {
        echo $this->element('design/dasol/header_d3');
    }
    ?>
    <!-- footer top start here -->
    <div class="footer-top clearfix">
        <div class="container">
            <div class="common-config">
                <h3>Quick Links</h3>
                <ul class="site-map">
                    <li class="<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'Users') && ($this->params['action'] == 'login')) echo 'active'; ?>"><span><img src="/img/foot-arrow.png"></span><a href="/users/login">Home</a></li>
                    <?php if (AuthComponent::User()) { ?>
                        <li class="<?php if (($this->params['controller'] == 'pannels' || $this->params['controller'] == 'Pannels') && ($this->params['action'] == 'allReviews')) echo 'active'; ?>"><span><img src="/img/foot-arrow.png"></span><?php echo $this->Html->link('Reviews', array('controller' => 'pannels', 'action' => 'allReviews', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                        <?php if ($store_data_app['Store']['is_booking_open'] == 1) { ?>
                            <li class="<?php if (($this->params['controller'] == 'pannels' || $this->params['controller'] == 'Pannels') && ($this->params['action'] == 'myBookings')) echo 'active'; ?>"><span><img src="/img/foot-arrow.png"></span><?php echo $this->Html->link(__('My Reservations'), array('controller' => 'pannels', 'action' => 'myBookings', $encrypted_storeId, $encrypted_merchantId)); ?></li>
                        <?php } ?>
                    <?php } ?>
                    <li class='<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'Users') && ($this->params['action'] == 'storeLocation')) echo 'active'; ?>'><span><img src="/img/foot-arrow.png"></span><a href="/users/storeLocation/<?php echo $this->Encryption->encode($this->Session->read('store_id')); ?>/<?php echo $this->Encryption->encode($this->Session->read('merchant_id')); ?>">Store Info</a></li>
                    <?php if ($store_data_app['Store']['is_not_photo'] == 0) { ?>
                        <li class='<?php if (($this->params['controller'] == 'users' || $this->params['controller'] == 'Users') && ($this->params['action'] == 'storePhoto')) echo 'active'; ?>'><span><img src="/img/foot-arrow.png"></span><a href="/pannels/orderImages/<?php echo $this->Encryption->encode($this->Session->read('store_id')); ?>/<?php echo $this->Encryption->encode($this->Session->read('merchant_id')); ?>">Gallery</a></li>
                    <?php } ?>


                    <?php if ($store_data_app['Store']['is_not_photo'] == 0) { ?>
                        <li class='<?php if (($this->params['controller'] == 'products' || $this->params['controller'] == 'products') && ($this->params['action'] == 'items')) echo 'active'; ?>'><span><img src="/img/foot-arrow.png"></span><a href="/products/items/<?php echo $this->Encryption->encode($this->Session->read('store_id')); ?>/<?php echo $this->Encryption->encode($this->Session->read('merchant_id')); ?>">Menu</a></li>
                    <?php } ?>
                    <?php
                    if (!empty($store_data_app['StoreContent'])) {
                        foreach ($store_data_app['StoreContent'] as $content) {
                            if ($content['page_position'] == 2) {
                                ?>
                                <li><span><img src="/img/foot-arrow.png"></span><?php echo $this->Html->link($content['name'], array('controller' => 'pannels', 'action' => 'staticContent', $this->Encryption->encode($this->Session->read('store_id')), $this->Encryption->encode($this->Session->read('merchant_id')), $this->Encryption->encode($content['id']), $content['name'])); ?></li>
                                <?php
                            }
                        }
                    }
                    ?>
                </ul>
            </div>
            <div class="common-config">
                <h3>Address</h3>
                <ul class="contact-us">
                    <li>
                        <a href="javascript:void(0)">
                            <?php echo $store_data_app['Store']['address'] . '<br>' . $store_data_app['Store']['city'] . ', ' . $store_data_app['Store']['state'] . ' ' . $store_data_app['Store']['zipcode']; ?>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="common-config">
                <h3>Contact Us</h3>
                <ul class="contact-us">
                    <li>
                        <a href="javascript:void(0)"><?php echo $store_data_app['Store']['phone']; ?></a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">
                            <?php echo $store_data_app['Store']['display_email']; ?>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="common-config SOCIAL-MEDIA">
                <h3>Follow</h3>
                <?php
                if (!empty($store_data_app['SocialMedia'])) {
                    $social_media = $store_data_app['SocialMedia'];
                    $social_media = array_filter($social_media);
                    unset($social_media['id'], $social_media['store_id'], $social_media['merchant_id'], $social_media['is_active'], $social_media['created'], $social_media['modified']);
                    $socialCount = count($social_media);
                    ?>
                    <ul class="social-media">
                        <?php if (!empty($social_media['facebook'])) { ?>
                            <li><a href="<?php echo $social_media['facebook']; ?>" target="blank"><img alt="facebook" src="/img/fb.png" /></a></li>
                            <?php
                        }
                        if (!empty($social_media['twitter'])) {
                            ?>
                            <li><a href="<?php echo $social_media['twitter']; ?>" target="blank"><img alt="twitter" src="/img/tw.png" /></a></li>
                            <?php
                        }
                        if (!empty($social_media['pinterest'])) {
                            ?>
                            <li><a href="<?php echo $social_media['pinterest']; ?>" target="blank"><img alt="pinterest" src="/img/pinterest.png" /></a></li>
                            <?php
                        }
                        if (!empty($social_media['instagram'])) {
                            ?>
                            <li><a href="<?php echo $social_media['instagram']; ?>" target="blank"><img alt="instagram" src="/img/instagram.png" /></a></li>
                            <?php
                        }
                        if (!empty($social_media['yolo'])) {
                            ?>
                            <li><a href="<?php echo $social_media['yolo']; ?>" target="blank"><img alt="yelp" src="/img/yelp.png" /></a></li>
                            <?php
                        }
                        if (!empty($social_media['google'])) {
                            ?>
                            <li><a href="<?php echo $social_media['google']; ?>" target="blank"><img alt="google plue" src="/img/g+.png" /></a></li>
                            <?php
                        }
                        if (!empty($social_media['yahoo'])) {
                            ?>
                            <li><a href="<?php echo $social_media['yahoo']; ?>" target="blank"><img alt="ymail" src="/img/y-mail.png" /></a></li>
                            <?php
                        }
                        if (!empty($social_media['yellow_page'])) {
                            ?>
                            <li><a href="<?php echo $social_media['yellow_page']; ?>" target="blank"><img alt="yellow page" src="/img/yellow-page.png" /></a></li>
                            <?php
                        }
                        if (!empty($social_media['try_caviar'])) {
                            ?>
                            <li><a href="<?php echo $social_media['try_caviar']; ?>" target="blank"><img alt="try caviar" src="/img/try-cavier.png" /></a></li>
                            <?php
                        }
                        if (!empty($social_media['home'])) {
                            ?>
                            <li><a href="<?php echo $social_media['home']; ?>" target="blank"><img alt="home" src="/img/home_link.png" /></a></li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
        </div>
    </div><!-- /footer top end -->
    <!-- footer bottom start here -->
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; BankCard Services, Inc. All Rights Reserved</p>
        </div>
    </div><!-- /footer bottom end -->
    <?php
    echo $this->Html->css('popup');
    echo $this->element('session/timeout');
    ?>
    <style>.ui-dialog { z-index: 1000 !important ;}</style>
    <script>

        function clearsession(data) {
            $.ajax({
                type: 'POST',
                url: '/users/clearsession',
                data: data,
                async: false,
                success: function (response) {
                    if (response) {
                        window.location = "/users/login"
                    }
                }
            });
        }

        function sessionpopup() {
            $('#timeoutpop').modal('show');
        }

        // If theres no activity for 5 Minutes do something
        var activityTimeout = setTimeout(inActive, 300000);
        function resetActive() {
            clearTimeout(activityTimeout);
            activityTimeout = setTimeout(inActive, 300000);
        }
        // No activity do something.
        function inActive() {
            sessionpopup();
        }

        // Check for mousemove, could add other events here such as checking for key presses ect.
        $(document).bind('mousemove click mouseup mousedown keydown keypress keyup submit change mouseenter scroll resize dblclick', function () {
            resetActive()
        });
    </script>
</footer><!-- /footer end -->