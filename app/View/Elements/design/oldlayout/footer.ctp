<!-- FOOTER START -->
<?php if (($this->params['controller'] == 'products' || $this->params['controller'] == 'Products') && ($this->params['action'] == 'items')) { ?>
    <footer class="footer fluid-footer">
    <?php } else { ?>
        <footer class="footer">
        <?php } ?>
        <div class="wrap clearfix">
            <ul class="footer-menu">
                <?php
                if (!empty($store_data_app['StoreContent'])) {
                    foreach ($store_data_app['StoreContent'] as $content) {
                        if ($content['page_position'] == 2) {
                            ?>
                            <li><?php echo $this->Html->link($content['name'], array('controller' => 'pannels', 'action' => 'staticContent', $this->Encryption->encode($this->Session->read('store_id')), $this->Encryption->encode($this->Session->read('merchant_id')), $this->Encryption->encode($content['id']), $content['name'])); ?></li>
                            <?php
                        }
                    }
                }
                ?>
            </ul>
            <?php
            if (!empty($store_data_app['SocialMedia'])) {
                $social_media = $store_data_app['SocialMedia'];
                ?>
                <ul class="social-icon">
                    <?php if (!empty($social_media['facebook'])) { ?>
                        <li><a href="<?php echo $social_media['facebook']; ?>" target="blank"><img alt="facebook" src="/img/facebook.png" /></a></li>
                        <?php
                    }
                    if (!empty($social_media['twitter'])) {
                        ?>
                        <li><a href="<?php echo $social_media['twitter']; ?>" target="blank"><img alt="twitter" src="/img/twitter.png" /></a></li>
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
                        <li><a href="<?php echo $social_media['google']; ?>" target="blank"><img alt="google plue" src="/img/google-plus.png" /></a></li>
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
        <!-- footer bottom start here -->
        <div class="footer-bottom clearfix margin-TP20">
            <div class="container">
                <p>&copy; BankCard Services, Inc. All Rights Reserved</p>
            </div>
        </div><!-- /footer bottom end -->
        <?php
        echo $this->Html->css('popup');
        echo $this->element('session/timeout');
        ?>

        <style>.ui-dialog { z-index: 1000 !important ;}.footer-bottom .container p {color: #fff;}</style>

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
            var activityTimeout = setTimeout(inActive, 600000);
            function resetActive() {
                clearTimeout(activityTimeout);
                activityTimeout = setTimeout(inActive, 600000);
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
    </footer>