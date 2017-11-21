<footer class="front-footer">
    <div class="wrap">
        <ul class="footer-menu">
            <?php if (!empty($merchantList)) { ?>
                <style>
                    .front-footer ul li {
                        display: inline-block;
                    }
                    .front-footer ul.footer-menu li {
                        background: rgba(0, 0, 0, 0) url("/img/vertical/menu-bar.png") no-repeat scroll right 5px;
                        padding: 0 10px 0 5px;
                    }
                </style>
                <?php
                foreach ($merchantList as $content) {
                    if ($content['MerchantContent']['page_position'] == 2) {
                        ?>
                        <li><?php echo $this->Html->link($content['MerchantContent']['name'], array('controller' => 'hqusers', 'action' => 'staticContent', $this->Encryption->encode($this->Session->read('hq_id')), $this->Encryption->encode($content['MerchantContent']['id']), $content['MerchantContent']['name']), array('class' => 'menu')); ?></li>
                        <?php
                    }
                }
            }
            ?>
            <?php echo $this->Html->link('Contact Us', array('controller' => 'hqusers', 'action' => 'contact_us'), array('class' => 'menu')); ?>
        </ul>
        <?php
        if (!empty($socialLinks)) {
            $social_media = $socialLinks['SocialMedia'];
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
        <p>Copyright <?php echo date('Y') ?>. All Right Reserved.</p>
    </div>
</footer>

